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
 * Item drop pickup model.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Item drop pickup model class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop_pickup extends persistent {

    const TABLE = 'block_stash_drop_pickups';

    protected static function define_properties() {
        return [
            'dropid' => [
                'type' => PARAM_INT
            ],
            'userid' => [
                'type' => PARAM_INT
            ],
            'pickupcount' => [
                'type' => PARAM_INT,
                'default' => 0
            ],
            'lastpickup' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ]
        ];
    }

    /**
     * Get a drop pickup for a drop and user.
     *
     * This creates the row if it does not exist.
     *
     * @param int $dropid The drop ID.
     * @param int $userid The user ID.
     * @return drop_pickup
     */
    public static function get_relation($dropid, $userid) {
        $params = ['dropid' => $dropid, 'userid' => $userid];
        $dp = self::get_record($params);
        if (!$dp) {
            $dp = new self(null, (object) $params);
        }
        return $dp;
    }

    /**
     * Validate the item ID.
     *
     * @param string $value The item ID.
     * @return true|lang_string
     */
    protected function validate_dropid($value) {
        if (!drop::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the pickup count.
     *
     * @param string $value The pickup count.
     * @return true|lang_string
     */
    protected function validate_pickupcount($value) {
        if ($value < 0) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the user ID.
     *
     * @param string $value The user ID.
     * @return true|lang_string
     */
    protected function validate_userid($value) {
        global $DB;
        if (!$DB->record_exists('user', ['id' => $value])) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }
}
