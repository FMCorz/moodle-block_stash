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
 * Items table.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir . '/tablelib.php');

use confirm_action;
use html_writer;
use moodle_url;
use pix_icon;
use stdClass;
use table_sql;
use block_stash\item as itemmodel;

/**
 * Items table class.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class items_table extends table_sql {

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
            'maxnumber',
            'actions'
        ));
        $this->define_headers(array(
            get_string('itemname', 'block_stash'),
            get_string('maxnumber', 'block_stash'),
            get_string('actions')
        ));


        $sqlfields = itemmodel::get_sql_fields('i', '');
        $sqlfrom = "{" . itemmodel::TABLE . "} i";

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

        $url = new moodle_url('/blocks/stash/item_edit.php');
        $url->params(['id' => $row->id, 'courseid' => $this->manager->get_courseid()]);
        $actionlink = $OUTPUT->action_link($url, '', null, null, new pix_icon('t/edit',
            get_string('edititem', 'block_stash', $row->name)));
        $actions[] = $actionlink;

        $url = new moodle_url('/blocks/stash/drop.php');
        $url->params(['itemid' => $row->id, 'courseid' => $this->manager->get_courseid()]);
        $actionlink = $OUTPUT->action_link($url, '', null, null, new pix_icon('t/add',
            get_string('addnewdrop', 'block_stash', $row->name)));
        $actions[] = $actionlink;

        $action = new confirm_action(get_string('reallydeleteitem', 'block_stash'));
        $url = new moodle_url($this->baseurl);
        $url->params(['itemid' => $row->id, 'action' => 'delete', 'sesskey' => sesskey()]);
        $actionlink = $OUTPUT->action_link($url, '', $action, null, new pix_icon('t/delete',
            get_string('deleteitem', 'block_stash', $row->name)));
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
        $renderable = new item(new itemmodel(null, $row), $this->manager);
        $str = '';
        $str .= $this->renderer->render_item_xsmall($renderable);
        $str .= format_string($row->name, null, ['context' => $this->manager->get_context()]);

        return $str;
    }

    /**
     * Formats the column.
     *
     * @param stdClass $row Table row.
     * @return string Output produced.
     */
    protected function col_maxnumber($row) {
        $str = $row->maxnumber;
        if ($row->maxnumber == 0) {
            $str = get_string('unlimited', 'block_stash');
        }
        return $str;
    }

    /**
     * Override the default implementation to set a decent heading level.
     */
    public function print_nothing_to_display() {
        global $OUTPUT;
        echo $this->render_reset_button();
        $this->print_initials_bar();
        echo $OUTPUT->heading(get_string('nothingtodisplay'), 4);
    }

}
