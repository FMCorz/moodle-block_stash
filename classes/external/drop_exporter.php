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

use lang_string;
use moodle_url;
use renderer_base;

/**
 * Persistent exporter class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'block_stash\\drop';
    }

    protected static function define_related() {
        return array('context' => 'context');
    }

    protected static function define_other_properties() {
        return [
            'editurl' => [
                'type' => PARAM_URL
            ],
            'maxpickupformatted' => [
                'type' => PARAM_RAW
            ],
            'pickupintervalformatted' => [
                'type' => PARAM_RAW
            ],
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $interval = $this->persistent->get_pickupinterval();
        $maxpickup = $this->persistent->get_maxpickup();
        $unlimited = $this->persistent->is_unlimited();

        return [
            'editurl' => (new moodle_url('/blocks/stash/drop.php', ['dropid' => $this->persistent->get_id()]))->out(false),
            'maxpickupformatted' => $unlimited ? get_string('unlimited', 'block_stash') : $maxpickup,
            'pickupintervalformatted' => $interval ? format_time($interval) : get_string('none', 'block_stash'),
        ];
    }

}
