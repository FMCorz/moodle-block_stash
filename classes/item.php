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
 * Item model.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Item model class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item extends persistent {

    const TABLE = 'block_stash_items';

    protected static function define_properties() {
        return [
            'stashid' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
            ],
            'maxnumber' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'detail' => [
                'type' => PARAM_RAW,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'detailformat' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ]
        ];
    }

    /**
     * Is an item in a specific stash?
     *
     * @param int $itemid The item ID.
     * @param int $stashid The stash ID.
     * @return boolean
     */
    public static function is_item_in_stash($itemid, $stashid) {
        global $DB;
        $sql = "SELECT i.id
                  FROM {" . self::TABLE . "} i
                 WHERE i.id = ?
                   AND i.stashid = ?";
        return $DB->record_exists_sql($sql, [$itemid, $stashid]);
    }

    /**
     * Is there a limit to how many of this item a user can have at once.
     *
     * @return bool
     */
    public function is_unlimited() {
        return $this->get('maxnumber') === null;
    }

    /**
     * Validate the max number.
     *
     * Null means unlimited. Zero does not have a meaning at the moment.
     *
     * @param string $value The value.
     * @return true|lang_string
     */
    protected function validate_maxnumber($value) {
        if ($value !== null && $value <= 0) {
            return new lang_string('invaliddata', 'error');
        }
        return true;
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

}
