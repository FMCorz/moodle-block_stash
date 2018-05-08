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
 * Block restore steplib.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use block_stash\stash;
use block_stash\item;
use block_stash\drop;
use block_stash\drop_pickup;
use block_stash\user_item;
use block_stash\trade;
use block_stash\tradeitems;

/**
 * Block restore structure step class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_stash_block_structure_step extends restore_structure_step {

    /**
     * Execution conditions.
     *
     * @return bool
     */
    protected function execute_condition() {
        global $DB;

        // No restore on the front page.
        if ($this->get_courseid() == SITEID) {
            return false;
        }

        return true;
    }

    /**
     * Define structure.
     */
    protected function define_structure() {
        global $DB;

        $paths = array();
        $userinfo = $this->get_setting_value('users');

        // Define each path.
        $paths[] = new restore_path_element('block', '/block');
        $paths[] = new restore_path_element('block_stash', '/block/stash');
        $paths[] = new restore_path_element('block_stash_item', '/block/stash/items/item');
        $paths[] = new restore_path_element('block_stash_drop', '/block/stash/items/item/drops/drop');
        $paths[] = new restore_path_element('block_stash_trade', '/block/stash/trades/trade');
        $paths[] = new restore_path_element('block_stash_tradeitems', '/block/stash/trades/trade/tradeitems/tradeitem');

        if ($userinfo) {
            $paths[] = new restore_path_element('pickup', '/block/stash/items/item/drops/drop/pickups/pickup');
            $paths[] = new restore_path_element('useritem', '/block/stash/items/item/useritems/useritem');
        }

        return $paths;
    }

    /**
     * Process block.
     */
    protected function process_block($data) {
        // Nothing to do here... \o/!
    }

    /**
     * Process stash.
     */
    protected function process_block_stash($data) {
        global $DB;
        $data = (object) $data;
        $stashid = $DB->get_field(stash::TABLE, 'id', ['courseid' => $this->get_courseid()]);
        $oldid = $data->id;
        unset($data->id);
        if (!$stashid) {
            $data->courseid = $this->get_courseid();
            $stash = new stash(null, $data);
            $stash->create();
            $stashid = $stash->get_id();
        }
        $this->set_mapping('block_stash', $oldid, $stashid);
    }

    /**
     * Process item.
     */
    protected function process_block_stash_item($data) {
        $data = (object) $data;
        $data->stashid = $this->get_new_parentid('block_stash');
        $oldid = $data->id;
        unset($data->id);
        $item = new item(null, $data);
        $item->create();
        $this->set_mapping('block_stash_item', $oldid, $item->get_id(), true, $this->task->get_old_course_contextid());
    }

    /**
     * Process drop.
     */
    protected function process_block_stash_drop($data) {
        $data = (object) $data;
        $data->itemid = $this->get_new_parentid('block_stash_item');
        $oldid = $data->id;
        unset($data->id);

        // When the hashcode conflicts, regenerate it.
        $drop = new drop(null, $data);
        while (drop::hashcode_exists($drop->get_hashcode(), $this->get_new_parentid('block_stash'))) {
            $drop->regenerate_hashcode();
        }
        $drop->create();

        $this->set_mapping('block_stash_drop', $oldid, $drop->get_id());
    }

    /**
     * Process drop pickup.
     */
    protected function process_pickup($data) {
        $data = (object) $data;
        $data->dropid = $this->get_new_parentid('block_stash_drop');
        $data->userid = $this->get_mappingid('user', $data->userid);
        unset($data->id);
        $dp = new drop_pickup(null, $data);
        $dp->create();
    }

    /**
     * Process user_item.
     */
    protected function process_useritem($data) {
        $data = (object) $data;
        $data->itemid = $this->get_new_parentid('block_stash_item');
        $data->userid = $this->get_mappingid('user', $data->userid);
        unset($data->id);
        $ui = new user_item(null, $data);
        $ui->create();
    }

    /**
     * Process trade.
     */
    protected function process_block_stash_trade($data) {
        $data = (object) $data;
        $data->stashid = $this->get_new_parentid('block_stash');
        $oldid = $data->id;
        unset($data->id);

        // When the hashcode conflicts, regenerate it.
        $trade = new trade(null, $data);
        while (trade::hashcode_exists($trade->get_hashcode(), $this->get_new_parentid('block_stash'))) {
            $trade->regenerate_hashcode();
        }
        $trade->create();

        $this->set_mapping('block_stash_trade', $oldid, $trade->get_id());
    }

    /**
     * Process_trade_items.
     */
    protected function process_block_stash_tradeitems($data) {
        $data = (object) $data;
        $data->tradeid = $this->get_new_parentid('block_stash_trade');
        $data->itemid = $this->get_mappingid('block_stash_item', $data->itemid);
        $oldid = $data->id;
        unset($data->id);
        $tradeitem = new tradeitems(null, $data);
        $tradeitem->create();
    }

    /**
     * After execute.
     */
    protected function after_execute() {
        $this->add_related_files('block_stash', 'item', 'block_stash_item', $this->task->get_old_course_contextid());
        $this->add_related_files('block_stash', 'detail', 'block_stash_item', $this->task->get_old_course_contextid());
    }

}
