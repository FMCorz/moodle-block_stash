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
 * Drop image renderable.
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

use block_stash\output\drop;

/**
 * Drop image renderable class.
 *
 * This can be used to render a drop with an image and an optional button.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop_image implements renderable, templatable {

    /** @var drop The drop renderable. */
    protected $drop;
    /** @var string Action text. */
    protected $actiontext;
    /** @var bool Whether to check visibility before showing the drop. */
    protected $checkvisibility;

    /**
     * Constructor.
     *
     * @param drop $drop The drop renderable.
     * @param string $actiontext Action text for the button.
     */
    public function __construct(drop $drop, $actiontext = '', $checkvisibility = false) {
        $this->drop = $drop;
        $this->actiontext = $actiontext;
        $this->checkvisibility = $checkvisibility;
    }

    /**
     * Export for template.
     *
     * @param renderer_base $output Renderer.
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        return (object) [
            'actiontext' => $this->actiontext,
            'checkvisibility' => $this->checkvisibility,
            'drop' => $this->drop->export_for_template($output),
            'uuid' => uniqid(),
        ];
    }

}
