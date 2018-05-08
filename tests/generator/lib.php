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
 * Data generator.
 *
 * @package    block_stash
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use block_stash\drop;
use block_stash\drop_pickup;
use block_stash\item;
use block_stash\stash;
use block_stash\user_item;

/**
 * Data generator class.
 *
 * @package    block_stash
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_stash_generator extends testing_block_generator {

    /** @var int The drop count. */
    protected $dropcount = 0;
    /** @var int The item count. */
    protected $itemcount = 0;

    /**
     * Create a stash.
     *
     * @param array $record The arguments.
     * @return stash
     */
    public function create_stash(array $record = []) {
        $record = (object) $record;
        if (empty($record->courseid)) {
            throw new coding_exception('The course ID must be set.');
        }

        $stash = new stash(null, $record);
        $stash->create();
        return $stash;
    }

    /**
     * Create a drop.
     *
     * @param array $record The arguments.
     * @return drop
     */
    public function create_drop(array $record = []) {
        $this->dropcount++;
        $record = (object) $record;

        if (isset($record->item)) {
            $record->itemid = $record->item->get_id();
            unset($record->item);
        }

        if (empty($record->itemid)) {
            throw new coding_exception('The item ID must be set.');
        }

        if (!isset($record->name)) {
            $record->name = 'Drop ' . $this->dropcount;
        }

        $drop = new drop(null, $record);
        $drop->create();
        return $drop;
    }

    /**
     * Create a drop pickup.
     *
     * @param array $record The arguments.
     * @return drop_pickup
     */
    public function create_drop_pickup(array $record = []) {
        $record = (object) $record;

        if (isset($record->drop)) {
            $record->dropid = $record->drop->get_id();
            unset($record->drop);
        }

        if (empty($record->dropid)) {
            throw new coding_exception('The drop ID must be set.');
        } else if (empty($record->userid)) {
            throw new coding_exception('The user ID must be set.');
        }

        $object = new drop_pickup(null, $record);
        $object->create();
        return $object;
    }

    /**
     * Create an item.
     *
     * @param array $record The arguments.
     * @return item
     */
    public function create_item(array $record = []) {
        $this->itemcount++;
        $record = (object) $record;

        if (isset($record->stash)) {
            $record->stashid = $record->stash->get_id();
            unset($record->stash);
        }

        if (empty($record->stashid)) {
            throw new coding_exception('The stash ID must be set.');
        }

        if (!isset($record->name)) {
            $record->name = 'Item ' . $this->itemcount;
        }

        $item = new item(null, $record);
        $item->create();
        return $item;
    }

    /**
     * Create a user item.
     *
     * @param array $record The arguments.
     * @return user_item
     */
    public function create_user_item(array $record = []) {
        $record = (object) $record;

        if (isset($record->item)) {
            $record->itemid = $record->item->get_id();
            unset($record->item);
        }

        if (empty($record->itemid)) {
            throw new coding_exception('The stash ID must be set.');
        } else if (empty($record->userid)) {
            throw new coding_exception('The user ID must be set.');
        }

        $item = new user_item(null, $record);
        $item->create();
        return $item;
    }
}
