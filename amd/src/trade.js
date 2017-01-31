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
 * Trade module.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/log',
    'block_stash/base',
    'block_stash/counselor',
], function($, Ajax, Log, Base, Counselor) {

    /**
     * Trade class.
     *
     * @param {Object} dropdata The data of this trade widget.
     */
    function Trade(tradedata) {
        Base.prototype.constructor.apply(this, [tradedata]);
    }
    Trade.prototype = Object.create(Base.prototype);



    return /** @alias module:block_stash/trade */ Trade;

});
