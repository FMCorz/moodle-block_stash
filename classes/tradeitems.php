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
 * Trade items model.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Trade items model class.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tradeitems extends persistent {

    const TABLE = 'block_stash_trade_items';

    protected static function define_properties() {
        return [
            'tradeid' => [
                'type' => PARAM_INT,
            ],
            'itemid' => [
                'type' => PARAM_INT,
            ],
            'quantity' => [
                'type' => PARAM_INT,
                'default' => null,
                'null' => NULL_ALLOWED
            ],
            'gainloss' => [
                'type' => PARAM_BOOL,
                'default' => true,
            ],
        ];
    }
}
