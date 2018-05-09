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
 * Persistent exporter.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\external;
defined('MOODLE_INTERNAL') || die();

use moodle_url;
use renderer_base;
use stdClass;
use block_stash\manager;

/**
 * Persistent exporter class.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trade_items_exporter extends persistent_exporter {

    protected static function define_class() {
        return 'block_stash\\tradeitems';
    }

    protected static function define_related() {
        return [
            'context' => 'context',
            'item' => 'block_stash\\item',
            'useritem' => 'block_stash\\user_item'
        ];
    }

    protected static function define_other_properties() {
        return [
            'name' => [
                'type' => PARAM_TEXT
            ],
            'imageurl' => [
                'type' => PARAM_URL
            ],
            'editurl' => [
                'type' => PARAM_URL
            ],
            'deleteurl' => [
                'type' => PARAM_URL
            ],
            'userquantity' => [
                'type' => PARAM_INT
            ],
            'enoughitems' => [
                'type' => PARAM_BOOL
            ]
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $item = $this->related['item'];
        $manager = manager::get_by_itemid($item->get_id());
        $imageurl = moodle_url::make_pluginfile_url($this->related['context']->id, 'block_stash', 'item',
            $item->get_id(), '/', 'image');
        $editurl = new moodle_url('/blocks/stash/trade_edit_new.php', ['id' => $this->persistent->get_tradeid(),
                'courseid' => $manager->get_courseid()]);
        $deleteurl = new moodle_url('/blocks/stash/trade.php', ['tradeitemid' => $this->persistent->get_id(),
            'courseid' => $manager->get_courseid(), 'action' => 'deletetradeitem', 'sesskey' => sesskey()]);
        $quantity = 0;
        if ($this->related['useritem']->get_quantity() !==  null) {
            $quantity = $this->related['useritem']->get_quantity();
        }
        $enoughitems = ($quantity >= $this->persistent->get_quantity()) ? true : false;

        return [
            'name' => $item->get_name(),
            'imageurl' => $imageurl->out(false),
            'editurl' => $editurl->out(false),
            'deleteurl' => $deleteurl->out(false),
            'userquantity' => $quantity,
            'enoughitems' => $enoughitems
        ];
    }

}
