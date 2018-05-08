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
 * The hashcode was initially 40 characters long, and we were not checking that the
 * code was unique per stash. We changed the length of the hash to be of 6
 * characters, but then it must be unique within its stash. This allows for the
 * snippets to contain the full hash, and no longer require the ID. If we
 * don't require the ID, we do not have to worry about backup and restore
 * and can pretty much always assume that the hash is unique.
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
                    return random_string(6);
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
     * Get a drop by hashcode portion.
     *
     * This will throw an exception when there are multiple matches.
     *
     * @param int $stashid The stash in which the item should be.
     * @param string $hash The hash portion.
     * @return self
     */
    public static function get_by_hashcode_portion($stashid, $hash) {
        global $DB;
        $hashlike = $DB->sql_like('d.hashcode', ':hashcode');

        $sql = "
            SELECT d.*
              FROM {" . self::TABLE . "} d
              JOIN {" . item::TABLE . "} i
                ON i.id = d.itemid
              JOIN {" . stash::TABLE . "} s
                ON s.id = i.stashid
             WHERE s.id = :stashid
               AND $hashlike";

        $params = [
            'stashid' => $stashid,
            'hashcode' => $DB->sql_like_escape($hash) . '%'
        ];

        return new self(null, $DB->get_record_sql($sql, $params, MUST_EXIST));
    }

    /**
     * Get the course ID from a drop ID.
     *
     * @param int $dropid The drop ID.
     * @return int
     */
    public static function get_courseid_by_id($dropid) {
        global $DB;
        $sql = "SELECT s.courseid
                  FROM {" . stash::TABLE . "} s
                  JOIN {" . item::TABLE . "} i
                    ON i.stashid = s.id
                  JOIN {" . self::TABLE . "} d
                    ON d.itemid = i.id
                 WHERE d.id = ?";
        return $DB->get_field_sql($sql, [$dropid], MUST_EXIST);
    }

    /**
     * Is the hashcode unique in the stash?
     *
     * @param string $hashcode The hash code.
     * @param int $stashid The stash ID.
     * @param int $ignoredropid The drop ID to ignore when checking.
     * @return bool
     */
    public static function hashcode_exists($hashcode, $stashid, $ignoredropid = 0) {
        global $DB;
        $sql = "
            SELECT 'x'
              FROM {" . self::TABLE . "} d
              JOIN {" . item::TABLE . "} i
                ON i.id = d.itemid
              JOIN {" . stash::TABLE . "} s
                ON s.id = i.stashid
             WHERE d.hashcode = :hashcode
               AND s.id = :stashid
               AND d.id <> :dropid";
        $params = [
            'hashcode' => $hashcode,
            'stashid' => $stashid,
            'dropid' => $ignoredropid,
        ];
        return $DB->record_exists_sql($sql, $params);
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
     * Regenerate the hash code.
     *
     * @return void
     */
    public function regenerate_hashcode() {
        $this->set('hashcode', random_string(6));
    }

    /**
     * Validate the hash code.
     *
     * @param string $value The hash code.
     * @return true|lang_string
     */
    protected function validate_hashcode($value) {
        if (strlen($value) != 40 && strlen($value) != 6) {
            // There are two formats of hashes, the old one at 40 chars, and the new one at 6.
            return new lang_string('invaliddata', 'error');
        }

        $item = new item($this->get_itemid());
        if (static::hashcode_exists($value, $item->get_stashid(), $this->get_id())) {
            // The hashcode is not unique within the stash.
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
