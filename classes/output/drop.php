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
 * Drop renderable.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\output;
defined('MOODLE_INTERNAL') || die();

use renderable;
use renderer_base;
use templatable;

use block_stash\drop as dropmodel;
use block_stash\item;
use block_stash\manager;
use block_stash\external\drop_exporter;
use block_stash\external\item_exporter;

/**
 * Drop renderable class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop implements renderable, templatable {

    /** @var drop The drop. */
    protected $drop;

    /** @var item The item. */
    protected $item;

    /** @var manager The manager. */
    protected $manager;

    /**
     * Constructor.
     *
     * @param item $item The item.
     * @param manager $manager The manager.
     */
    public function __construct(dropmodel $drop, item $item, manager $manager) {
        $this->drop = $drop;
        $this->item = $item;
        $this->manager = $manager;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $exporter = new drop_exporter($this->drop, ['context' => $this->manager->get_context()]);
        $data = $exporter->export($output);
        $exporter = new item_exporter($this->item, ['context' => $this->manager->get_context()]);
        $data->item = $exporter->export($output);
        return $data;
    }

}
