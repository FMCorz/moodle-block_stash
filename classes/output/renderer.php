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
 * Block Stash renderer.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;
defined('MOODLE_INTERNAL') || die();

use context;
use plugin_renderer_base;
use renderable;
use blocK_stash\drop;
use blocK_stash\item;
use block_stash\external\drop_exporter;
use block_stash\external\item_exporter;

/**
 * Block Stash renderer class.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    /**
     * Renderer to output the snippet UI.
     *
     * @param drop $drop The drop.
     * @param item $item The item.
     * @param context $context The context of the drop.
     * @return string
     */
    public function drop_snippet_ui(drop $drop, item $item, context $context) {
        $data = (object) [];
        $exporter = new drop_exporter($drop, ['context' => $context]);
        $data->drop = $exporter->export($this);
        $data->dropjson = json_encode($data->drop);
        $exporter = new item_exporter($item, ['context' => $context]);
        $data->item = $exporter->export($this);
        $data->itemjson = json_encode($data->item);
        return parent::render_from_template('block_stash/drop_snippet_ui', $data);
    }

    public function render_block_content(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_stash/main_content', $data);
    }

    public function render_inventory_page(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_stash/inventory', $data);
    }

    public function render_item_xsmall(renderable $renderable) {
        $data = $renderable->export_for_template($this);
        return parent::render_from_template('block_stash/item_xsmall', $data);
    }

    public function render_settings_page(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_stash/settings', $data);
    }

    public function render_user_inventory(renderable $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('block_stash/user_inventory', $data);
    }

}
