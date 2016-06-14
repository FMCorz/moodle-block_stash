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
 * Item drop model.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Item drop model class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop extends persistent {

    const TABLE = 'block_stash_drops';

    protected static function define_properties() {
        return [
            'itemid' => [
                'type' => PARAM_INT
            ],
            'name' => [
                'type' => PARAM_NOTAGS
            ],
            'maxpickup' => [
                'type' => PARAM_INT,
                'default' => 1
            ],
            'pickupinterval' => [
                'type' => PARAM_INT,
                'default' => HOURSECS
            ],
            'hashcode' => [
                'type' => PARAM_ALPHANUM,
                'default' => function() {
                    return random_string(40);
                }
            ]
        ];
    }

    /**
     * Validate the hash code.
     *
     * @param string $value The hash code.
     * @return true|lang_string
     */
    protected function validate_hashcode($value) {
        if (strlen($value) != 40) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the item ID.
     *
     * @param string $value The item ID.
     * @return true|lang_string
     */
    protected function validate_itemid($value) {
        if (!item::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the max pickup.
     *
     * @param string $value The max pickup.
     * @return true|lang_string
     */
    protected function validate_maxpickup($value) {
        if ($value < 0) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the pickup interval.
     *
     * @param string $value The pickup interval.
     * @return true|lang_string
     */
    protected function validate_pickupinterval($value) {
        if ($value < 0) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }
}
