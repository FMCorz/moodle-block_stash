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

use plugin_renderer_base;
use renderable;
/**
 * Block Stash renderer class.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends plugin_renderer_base {

    public function drop($drop, $item, $context) {
        // TODO Remove.
        if (!$drop) {
            return '';
        }

        $data = [];
        $exporter = new \block_stash\external\drop_exporter($drop, ['context' => $context]);
        $data['drop'] = json_encode($exporter->export($this));
        $exporter = new \block_stash\external\item_exporter($item, ['context' => $context]);
        $data['item'] = json_encode($exporter->export($this));

        return parent::render_from_template('block_stash/drop_snippet', (object) $data);
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
