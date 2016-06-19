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
 * The block_stash item aquired event
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\event;

defined('MOODLE_INTERNAL') || die();

/**
 * The block_stash item acquired event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - int quantity: item quantity acquired.
 * }
 *
 * @package    block_stash
 * @since      Block stash 1.0
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class item_acquired extends \core\event\base {

    /**
     * Returns description of what happened.
     *
     * @return string
     */
    public function get_description() {
        return "The user with id '$this->userid' helped the user '$this->relateduserid' to acquire an item with
                the id '$this->objectid' with the quantity of '" . $this->other['quantity'] . "'.";
    }

    /**
     * Return localised event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventitemacquired', 'block_stash');
    }

    /**
     * Returns relevant URL, override in subclasses.
     * @return \moodle_url
     */
    public function get_url() {
        // Not exactly sure where we should direct the user here. It's not easy getting the location of
        // where this event happened.
        return null;
    }

    /**
     * Init method.
     *
     * @return void
     */
    protected function init() {
        $this->data['objecttable'] = \block_stash\item::TABLE;
        $this->data['crud'] = 'c';
        $this->data['edulevel'] = self::LEVEL_PARTICIPATING;
    }

    /**
     * Custom validation.
     *
     * @throws \coding_exception
     * @return void
     */
    protected function validate_data() {
        parent::validate_data();

        if (!isset($this->relateduserid)) {
            throw new \coding_exception('The \'relateduserid\' must be set.');
        }

        if (!isset($this->other['quantity'])) {
            throw new \coding_exception('The \'quantity\' must be set.');
        }
    }
}
