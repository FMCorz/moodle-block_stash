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
 * User item model.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * User item model class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_item extends persistent {

    const TABLE = 'block_stash_user_items';

    protected static function define_properties() {
        return [
            'itemid' => [
                'type' => PARAM_INT,
            ],
            'userid' => [
                'type' => PARAM_INT,
            ],
            'quantity' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ]
        ];
    }

    /**
     * Delete all entries for a user in a stash.
     *
     * @param int $userid The user ID.
     * @param int $stashid The stash ID.
     */
    public static function delete_all_for_user_in_stash($userid, $stashid) {
        global $DB;
        $sql = 'DELETE FROM {' . self::TABLE . '}
                 WHERE userid = :userid
                   AND itemid IN (
                       SELECT id
                         FROM {' . item::TABLE . '}
                        WHERE stashid = :stashid
                   )';
        $DB->execute($sql, ['userid' => $userid, 'stashid' => $stashid]);
    }

    /**
     * Get all items in the stash of a user.
     *
     * @param int $userid The user ID.
     * @param int $stashid The stash ID.
     * @param bool $onlyvisible When true, only return visible items.
     * @return array An array of objects containing items and user items.
     */
    public static function get_all_in_stash($userid, $stashid, $onlyvisible = true) {
        global $DB;
        $result = [];

        $itemfields = item::get_sql_fields('i', 'item');
        $uifields = self::get_sql_fields('ui', 'useritem');
        $sql = "SELECT $itemfields, $uifields
                  FROM {" . self::TABLE . "} ui
                  JOIN {" . item::TABLE . "} i
                    ON i.id = ui.itemid
                 WHERE ui.userid = ?
                   AND i.stashid = ?";

        if ($onlyvisible) {
            $sql .= " AND ui.quantity IS NOT NULL";
        }

        $records = $DB->get_recordset_sql($sql, [$userid, $stashid]);
        foreach ($records as $record) {
            $result[] = (object) [
                'item' => new item(null, item::extract_record($record, 'item')),
                'useritem' => new self(null, self::extract_record($record, 'useritem'))
            ];
        }
        $records->close();

        return $result;
    }

    /**
     * Does the user have stock?
     *
     * @return bool
     */
    public function has_stock() {
        $quantity = $this->get('quantity');
        return $quantity !== null && $quantity > 0;
    }

    /**
     * An item is hidden until it's been acquired at least once.
     *
     * @return bool
     */
    public function is_visible() {
        return $this->get('quantity') !== null;
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
     * Validate the user ID.
     *
     * @param string $value The user ID.
     * @return true|lang_string
     */
    protected function validate_userid($value) {
        global $DB;

        $sql = 'itemid = :itemid AND userid = :userid AND id <> :id';
        $params = [
            'id' => $this->get('id'),
            'itemid' => $this->get('itemid'),
            'userid' => $value,
        ];
        if (self::record_exists_select($sql, $params)) {
            // There must be only one record per item.
            return new lang_string('invaliddata', 'error');

        } else if (!$DB->record_exists('user', ['id' => $value])) {
            // The user must exist.
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

}
