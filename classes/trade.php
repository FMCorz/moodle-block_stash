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
 * Trade model.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Trade model class.
 *
 * The hashcode was initially 40 characters long, and we were not checking that the
 * code was unique per stash. We changed the length of the hash to be of 6
 * characters, but then it must be unique within its stash. This allows for the
 * snippets to contain the full hash, and no longer require the ID. If we
 * don't require the ID, we do not have to worry about backup and restore
 * and can pretty much always assume that the hash is unique.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trade extends persistent {

    const TABLE = 'block_stash_trade';

    protected static function define_properties() {
        return [
            'stashid' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'losstitle' => [
                'type' => PARAM_TEXT,
            ],
            'gaintitle' => [
                'type' => PARAM_TEXT,
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
     * Get a trade by hashcode portion.
     *
     * This will throw an exception when there are multiple matches.
     *
     * @param int $stashid The stash in which the item should be.
     * @param string $hash The hash portion.
     * @return self
     */
    public static function get_by_hashcode_portion($stashid, $hash) {
        global $DB;
        $hashlike = $DB->sql_like('hashcode', ':hashcode');
        $params = [
            'stashid' => $stashid,
            'hashcode' => $DB->sql_like_escape($hash) . '%'
        ];
        $record = $DB->get_record_select(self::TABLE, "stashid = :stashid AND $hashlike", $params, '*', MUST_EXIST);
        return new self(null, $record);
    }

    /**
     * Is the hashcode unique in the stash?
     *
     * @param string $hashcode The hash code.
     * @param int $stashid The stash ID.
     * @param int $ignoreid The ID to ignore when checking.
     * @return bool
     */
    public static function hashcode_exists($hashcode, $stashid, $ignoreid = 0) {
        $sql = 'stashid = :stashid AND hashcode = :hashcode AND id <> :id';
        $params = [
            'stashid' => $stashid,
            'hashcode' => $hashcode,
            'id' => $ignoreid,
        ];
        return static::record_exists_select($sql, $params);
    }

    /**
     * Is a trade widget in a specific stash?
     *
     * @param int $trademid The item ID.
     * @param int $stashid The stash ID.
     * @return boolean
     */
    public static function is_trade_in_stash($tradeid, $stashid) {
        global $DB;
        $sql = "SELECT i.id
                  FROM {" . self::TABLE . "} i
                 WHERE i.id = ?
                   AND i.stashid = ?";
        return $DB->record_exists_sql($sql, [$tradeid, $stashid]);
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
     * Validate the stash ID.
     *
     * @param string $value The stash ID.
     * @return true|lang_string
     */
    protected function validate_stashid($value) {
        if (!stash::record_exists($value)) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
    }

    /**
     * Validate the hash code.
     *
     * @param string $value The hash code.
     * @return true|lang_string
     */
    protected function validate_hashcode($value) {
        if (strlen($value) != 40 && strlen($value) != 6) {
            return new lang_string('invaliddata', 'error');
        } else if (static::hashcode_exists($value, $this->get_stashid(), $this->get_id())) {
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

    public function get_full_trade_items_list($tradeid) {
        global $DB;

        $tradeitemfields = tradeitems::get_sql_fields('ti', 'tradeitem');
        $itemfields = item::get_sql_fields('i', 'item');
        $sql = "SELECT $itemfields, $tradeitemfields
                  FROM {" . tradeitems::TABLE . "} ti
                  JOIN {" . item::TABLE . "} i ON ti.itemid = i.id
                 WHERE ti.tradeid = ?";
        return $DB->get_records_sql($sql, [$tradeid]);

    }

}
