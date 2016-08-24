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
 * User item summary exporter.
 *
 * This contains the information required to display an item and how many
 * a user has of it.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\external;
defined('MOODLE_INTERNAL') || die();

use lang_string;
use moodle_url;
use renderer_base;
use stdClass;

/**
 * User item summary exporter class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class user_item_summary_exporter extends exporter {

    protected static function define_related() {
        return [
            'context' => 'context',
            'item' => 'block_stash\\item',
            'useritem' => 'block_stash\\user_item',
        ];
    }

    protected static function define_properties() {
        return [
            'item' => [
                'type' => item_exporter::read_properties_definition()
            ],
            'useritem' => [
                'type' => user_item_exporter::read_properties_definition()
            ],
        ];
    }

    protected function get_other_values(renderer_base $output) {
        $data = new stdClass();
        $exporter = new item_exporter($this->related['item'], ['context' => $this->related['context']]);
        $data->item = $exporter->export($output);
        $exporter = new user_item_exporter($this->related['useritem'], ['context' => $this->related['context']]);
        $data->useritem = $exporter->export($output);
        return (array) $data;
    }

}
