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
 * Extra small trade renderable.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

use block_stash\trade as trademodel;
use block_stash\manager;
use block_stash\external\trade_exporter;
use block_stash\external\trade_items_exporter;

/**
 * Trade renderable class.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class trade implements renderable, templatable {

    /** @var trade The trade widget. */
    protected $trade;

    /** @var manager The manager. */
    protected $manager;

    /** @var tradeitems items related to this trade */
    protected $tradeitems;

    /**
     * Constructor.
     *
     * @param trade $trade The trade widget.
     * @param manager $manager The manager.
     * @param array $tradeitems A list of trade items.
     */
    public function __construct(trademodel $trade, manager $manager, $tradeitems) {
        $this->trade = $trade;
        $this->manager = $manager;
        $this->tradeitems = $tradeitems;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        global $USER;
        $data = [];
        $exporter = new trade_exporter($this->trade, ['context' => $this->manager->get_context()]);
        $data['trade'] = $exporter->export($output);
        $data['tradeitems'] = [];
        $data['uuid'] = uniqid();
        $data['cantrade'] = $this->manager->do_trade($this->trade->get_id(), $USER->id, true);
        foreach ($this->tradeitems as $tradeitem) {
            $item = $this->manager->get_item($tradeitem->get_itemid());
            $useritem = $this->manager->get_user_item($USER->id, $item->get_id());
            $exporter = new trade_items_exporter($tradeitem, ['context' => $this->manager->get_context(), 'item' => $item,
                    'useritem' => $useritem]);
            $data['tradeitems'][] = $exporter->export($output);
        }
        return $data;
    }

}