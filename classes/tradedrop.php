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
 * Trade drop model.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Trade drop model class.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tradedrop extends persistent {

    const TABLE = 'block_stash_trade_drops';

    protected static function define_properties() {
        return [
            'tradeid' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
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
     * Get the course ID from a trade ID.
     *
     * @param int $id The trade drop ID.
     * @return int
     */
    public static function get_courseid_by_id($id) {
        global $DB;
        $sql = "SELECT s.courseid
                  FROM {" . stash::TABLE . "} s
                  JOIN {" . trade::TABLE . "} i
                    ON i.stashid = s.id
                  JOIN {" . tradedrop::TABLE . "} d
                    ON d.tradeid = i.id
                 WHERE d.id = ?";

        return $DB->get_field_sql($sql, [$id], MUST_EXIST);
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
     * Validate the trade ID.
     *
     * @param string $value The trade ID.
     * @return true|lang_string
     */
    protected function validate_tradeid($value) {
        if (!trade::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }
}
