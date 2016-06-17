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
                'default' => 1,
                'null' => NULL_ALLOWED
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
     * Whether the drop can be picked up.
     *
     * This does not account for any capability checks, it only checks if
     * the user does not exceed the rules established by the drop itself.
     *
     * @param drop_pickup $dp Get from {@link drop_pickup::get_relation()}.
     * @return bool
     */
    public function can_pickup(drop_pickup $dp) {
        $maxpickup = $this->get_maxpickup();
        $interval = $this->get_pickupinterval();

        if ($maxpickup > 0 && $dp->get_pickupcount() >= $maxpickup) {
            return false;

        } else if ($interval > 0 && $dp->get_lastpickup() + $interval > time()) {
            return false;
        }

        return true;
    }

    /**
     * Is there a limit to how many times a user can pickup the item on this drop?
     *
     * @return bool
     */
    public function is_unlimited() {
        return $this->get('maxpickup') === null;
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
     * Null means unlimited. Zero does not have a meaning at the moment.
     *
     * @param string $value The max pickup.
     * @return true|lang_string
     */
    protected function validate_maxpickup($value) {
        if ($value !== null && $value < 1) {
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
