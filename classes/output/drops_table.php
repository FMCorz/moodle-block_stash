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
 * Drops table.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

use action_link;
use action_menu;
use confirm_action;
use html_writer;
use moodle_url;
use pix_icon;
use stdClass;
use table_sql;
use block_stash\drop;
use block_stash\item as itemmodel;

/**
 * Drops table class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drops_table extends table_sql {

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
    public function __construct($uniqueid, $manager, $renderer) {
        parent::__construct($uniqueid);
        $this->set_attribute('class', $uniqueid . ' tablewithitems generaltable generalbox');
        $this->manager = $manager;
        $this->renderer = $renderer;

        // Define columns.
        $this->define_columns(array(
            'name',
            'itemname',
            'maxpickup',
            'pickupinterval',
            'actions'
        ));
        $this->define_headers(array(
            get_string('dropname', 'block_stash'),
            get_string('itemname', 'block_stash'),
            get_string('maxpickup', 'block_stash'),
            get_string('pickupinterval', 'block_stash'),
            get_string('actions')
        ));

        // Define SQL.
        $sqlfields = drop::get_sql_fields('d', '') . ',' . itemmodel::get_sql_fields('i', 'item');;
        $sqlfrom = "{" . drop::TABLE ."} d
               JOIN {" . itemmodel::TABLE . "} i
                 ON i.id = d.itemid";

        $this->sql = new stdClass();
        $this->sql->fields = $sqlfields;
        $this->sql->from = $sqlfrom;
        $this->sql->where = 'i.stashid = :stashid';
        $this->sql->params = ['stashid' => $this->manager->get_stash()->get_id()];

        // Define various table settings.
        $this->sortable(true, 'name', SORT_ASC);
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

        $actions = [];

        $url = new moodle_url('/blocks/stash/drop.php');
        $url->params(['dropid' => $row->id, 'courseid' => $this->manager->get_courseid(), 'returntype' => 'drops']);
        $actionlink = $OUTPUT->action_link($url, '', null, null, new pix_icon('t/edit',
            get_string('editdrop', 'block_stash', $row->name)));
        $actions[] = $actionlink;

        $action = new confirm_action(get_string('reallydeletedrop', 'block_stash'));
        $url = new moodle_url($this->baseurl);
        $url->params(['action' => 'delete', 'dropid' => $row->id, 'sesskey' => sesskey()]);
        $actionlink = $OUTPUT->action_link($url, '', $action, null, new pix_icon('t/delete',
            get_string('deletedrop', 'block_stash', $row->name)));
        $actions[] = $actionlink;

        return implode(' ', $actions);
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_name($row) {
        $url = new moodle_url($this->baseurl);
        $url->param('action', 'snippet');
        $url->param('dropid', $row->id);

        return html_writer::link($url, $row->name);
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_itemname($row) {
        $record = itemmodel::extract_record($row, 'item');
        $renderable = new item(new itemmodel(null, $record), $this->manager);

        $str = '';
        $str .= $this->renderer->render_item_xsmall($renderable);
        $str .= format_string($row->itemname, null, ['context' => $this->manager->get_context()]);

        return $str;
    }

    /**
     * Formats the name.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_maxpickup($row) {
        if ($row->maxpickup === null) {
            return get_string('unlimited', 'block_stash');
        }
        return $row->maxpickup;
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_pickupinterval($row) {
        if (!$row->pickupinterval) {
            return get_string('none', 'block_stash');
        }
        return format_time($row->pickupinterval);
    }

    /**
     * Override the default implementation to set a decent heading level.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;

        if (method_exists($this, 'can_be_reset') && $this->can_be_reset()) {
            // Compability with 2.9.
            echo $this->render_reset_button();
            $this->print_initials_bar();
            echo $OUTPUT->heading(get_string('nothingtodisplay'), 4);

        } else {
            echo $this->renderer->drops_fullpage_help();
        }

    }

}
