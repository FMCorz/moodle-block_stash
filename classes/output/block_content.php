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
 * Block stash renderable.
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

class block_content implements renderable, templatable {

    protected $courseid;

    public function __construct($courseid) {
        $this->courseid = $courseid;
    }

    public function export_for_template(renderer_base $output) {
        $data = array();
        $data['settingsurl'] = new moodle_url('/blocks/stash/settings.php', array('courseid' => $this->courseid));
        $data['inventoryurl'] = new moodle_url('/blocks/stash/inventory.php', array('courseid' => $this->courseid));
        return $data;
    }

}