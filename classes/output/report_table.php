<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Report table.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

use confirm_action;
use help_icon;
use html_writer;
use moodle_url;
use pix_icon;
use stdClass;
use table_sql;
use user_picture;
use block_stash\manager;
use block_stash\drop as dropmodel;
use block_stash\item as itemmodel;
use block_stash\user_item;
use block_stash\external\user_item_summary_exporter;

/**
 * Report table class.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_table extends table_sql {

    /** @var block_stash\manager The manager. */
    protected $manager;

    /** @var block_stash\renderer The renderer. */
    protected $renderer;

    /**
     * Constructor.
     *
     * @param string $uniqueid Unique ID.
     * @param manager $manager The manager.
     */
    public function __construct($uniqueid, $manager, $renderer, $groupid) {
        global $DB;
        parent::__construct($uniqueid);

        $this->set_attribute('class', $uniqueid . ' tablewithitems generaltable generalbox');
        $this->manager = $manager;
        $this->renderer = $renderer;

        // Define columns.
        $this->define_columns(array(
            'userpic',
            'fullname',
            'stash',
            'actions'
        ));
        $this->define_headers(array(
            '',
            get_string('fullname'),
            get_string('stash', 'block_stash'),
            get_string('actions')
        ));
        $this->column_class('stash', 'stash-items');

        // Get all the users that are enrolled and can earn XP.
        $ids = array();
        $users = get_enrolled_users($manager->get_context(), manager::CAN_ACQUIRE_ITEMS, $groupid);
        foreach ($users as $user) {
            $ids[$user->id] = $user->id;
        }
        unset($users);

        // Get the users which might not be enrolled or are revoked the permission, but still should
        // be displayed in the report for the teachers' benefit. We need to filter out the users which
        // are not a member of the group though.
        if (empty($groupid)) {
            $sql = 'SELECT ui.userid
                     FROM {' . user_item::TABLE . '} ui
                     JOIN {' . itemmodel::TABLE . '} i
                       ON i.id = ui.itemid
                    WHERE i.stashid = ?';
            $params = [$manager->get_stash()->get_id()];
        } else {
            $sql = 'SELECT ui.userid
                     FROM {' . user_item::TABLE . '} ui
                     JOIN {' . itemmodel::TABLE . '} i
                       ON i.id = ui.itemid
                     JOIN {groups_members} gm
                       ON ui.userid = gm.userid
                      AND gm.groupid = ?
                    WHERE i.stashid = ?';
            $params = [$groupid, $manager->get_stash()->get_id()];
        }
        $entries = $DB->get_recordset_sql($sql, $params);
        foreach ($entries as $entry) {
            $ids[$entry->userid] = $entry->userid;
        }
        $entries->close();
        list($insql, $inparams) = $DB->get_in_or_equal($ids, SQL_PARAMS_NAMED, 'param', true, null);

        // Define SQL.
        $this->sql = new stdClass();
        $this->sql->fields = user_picture::fields('u') . '';
        $this->sql->from = "{user} u";
        $this->sql->where = "u.id $insql";
        $this->sql->params = array_merge($inparams, []);

        // Define various table settings.
        $this->sortable(true, 'firstname', SORT_ASC);
        $this->no_sorting('userpic');
        $this->no_sorting('stash');
        $this->no_sorting('actions');
        $this->collapsible(false);
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_actions($row) {
        global $OUTPUT;

        $fullname = fullname($row);

        $actions = [];
        $action = new confirm_action(get_string('reallyresetstashof', 'block_stash', $fullname));
        $url = new moodle_url($this->baseurl);
        $url->params(['action' => 'reset', 'userid' => $row->id, 'sesskey' => sesskey()]);
        $actionlink = $OUTPUT->action_link($url, '', $action, null, new pix_icon('t/reset',
            get_string('resetstashof', 'block_stash', $fullname)));
        $actions[] = $actionlink;

        return implode(' ', $actions);
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_stash($row) {
        // This is everything but efficient...
        $items = $this->manager->get_all_user_items_in_stash($row->id);
        if (empty($items)) {
            return '-';
        }

        $html = '';
        foreach ($items as $item) {
            $exporter = new user_item_summary_exporter([], [
                'context' => $this->manager->get_context(),
                'item' => $item->item,
                'useritem' => $item->useritem,
            ]);
            $data = $exporter->export($this->renderer);
            $html .= $this->renderer->render_from_template('block_stash/user_item_small', $data);
        }

        return $html;
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_userpic($row) {
        global $OUTPUT;
        return $OUTPUT->user_picture($row);
    }

    /**
     * Override the default implementation to set a decent heading level.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        if (method_exists($this, 'render_reset_button')) {
            // Compability with 2.9.
            echo $this->render_reset_button();
        }
        $this->print_initials_bar();
        echo $OUTPUT->heading(get_string('nothingtodisplay'), 4);
    }

    /**
     * Defines a help icon for the header
     *
     * Always use this function if you need to create header with sorting and help icon.
     *
     * @param renderable[] $helpicons An array of renderable objects to be used as help icons
     */
    public function define_help_for_headers($helpicons) {
        // Check if parent method exists.
        if (method_exists('table_sql', 'define_help_for_headers')) {
            parent::define_help_for_headers($helpicons);
        }
        // This method does not exist in the parent yet. Do nothing.
    }

}
