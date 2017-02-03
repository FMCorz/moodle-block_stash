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
 * Show item detail.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/templates',
    'block_stash/dialogue-base',
], function(Templates, DialogueBase) {

    /**
     * Detail dialogue class.
     *
     * @param {Number} itemId The item ID.
     */
    function TradeInfo() {
        DialogueBase.prototype.constructor.apply(this, []);
    }
    TradeInfo.prototype = Object.create(DialogueBase.prototype);
    TradeInfo.prototype.constructor = TradeInfo;


    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    TradeInfo.prototype._render = function() {
        return Templates.render('block_stash/trade_success');
    };

    return TradeInfo;

});
