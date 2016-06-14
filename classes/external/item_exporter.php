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
 * Persistent exporter.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\external;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderer_base;

/**
 * Persistent exporter class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'block_stash\\item';
    }

    protected static function define_related() {
        return array('context' => 'context');
    }

    protected static function define_other_properties() {
        return [
            'imageurl' => [
                'type' => PARAM_URL
            ],
            'editurl' => [
                'type' => PARAM_URL
            ]
        ];
    }

    protected function get_other_values(renderer_base $output) {
        // print_object($this->persistent);
        $imageurl = moodle_url::make_pluginfile_url($this->related['context']->id, 'block_stash', 'item',
            $this->persistent->get_id(), '/', 'image');
        $editurl = new moodle_url('/blocks/stash/inventory_edit.php', array('id' => $this->persistent->get_id(), 'courseid' => '2'));

        return [
            'imageurl' => $imageurl->out(false),
            'editurl' => $editurl->out(false)
        ];
    }

}
