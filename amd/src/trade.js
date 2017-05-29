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
    'block_stash/trade-dialogue',
    'block_stash/item',
    'block_stash/user-item',
], function($, Ajax, Log, Base, Counselor, Dialogue, Item, UserItem) {

    /**
     * Trade class.
     *
     * @param {Object} dropdata The data of this trade widget.
     */
    function Trade(tradedata) {
        Base.prototype.constructor.apply(this, [tradedata]);
    }
    Trade.prototype = Object.create(Base.prototype);

    Trade.prototype.EVENT_TRADE = 'trade:pickedup';


    Trade.prototype.do = function() {

        return Ajax.call([{
            methodname: 'block_stash_complete_trade',
            args: {
                tradeid: this.get('id'),
                hashcode: this.get('hashcode')
            }
        }])[0].fail(function() {
            Log.debug('The trade could not be completed.');

        }).then(function(data) {

            // Notify other areas about item removal and acquirement.
            if (data) {
                for (var index in data.gaineditems) {

                    var userItem = new UserItem(data.gaineditems[index].useritem, new Item(data.gaineditems[index].item));
                    Counselor.trigger(this.EVENT_TRADE, {
                        id: this.get('id'),
                        hashcode: this.get('hashcode'),
                        useritem: userItem
                    });
                }

                for (var index in data.removeditems) {
                    var userItem = new UserItem(data.removeditems[index].useritem, new Item(data.removeditems[index].item));
                    Counselor.trigger(this.EVENT_TRADE, {
                        id: this.get('id'),
                        hashcode: this.get('hashcode'),
                        useritem: userItem
                    });
                }
            }

        }.bind(this));
    };


    return /** @alias module:block_stash/trade */ Trade;

});
