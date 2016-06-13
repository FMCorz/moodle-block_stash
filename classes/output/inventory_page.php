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
 * Block stash inventory page.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;

defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;
use moodle_url;
use block_stash\external\item_exporter;

class inventory_page implements renderable, templatable {

    protected $courseid;
    protected $manager;

    public function __construct($courseid) {
        $this->courseid = $courseid;
        $this->manager = \block_stash\manager::get($courseid);
    }

    public function export_for_template(renderer_base $output) {

        $data = array();
        // $data['settingsurl'] = new moodle_url('/blocks/stash/settings.php');
        $data['inventoryediturl'] = new moodle_url('/blocks/stash/inventory_edit.php', array('courseid' => $this->courseid));
        $data['items'] = array_map(function($item) use ($output) {
            $exporter = new item_exporter($item, ['context' => $this->manager->get_context()]);
            return $exporter->export($output);
        }, $this->manager->get_items());

        return $data;
    }

}