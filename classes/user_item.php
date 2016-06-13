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
