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
 * @copyright  2017 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/templates',
    'block_stash/counselor',
    'block_stash/drop',
    'core/notification',
    'block_stash/trade',
], function($, Templates, Counselor, Drop, notification, Trade) {

    /**
     * Trade class.
     *
     * @class
     * @param {Node} node The node.
     */
    function TradeArea(node) {
        this._node = $(node);
        this._setUp();
        this._update();
    }
    TradeArea.prototype._node = null;

    /**
     * Setup.
     */
    TradeArea.prototype._setUp = function() {
        Counselor.on(Drop.prototype.EVENT_PICKEDUP, this._dropPickedUpListener.bind(this));
        Counselor.on(Trade.prototype.EVENT_TRADE, this._dropPickedUpListener.bind(this));
    };

    /**
     * Whether the item is in this trade area.
     *
     * @param {Number} id The item ID.
     * @return {Boolean}
     */
    TradeArea.prototype.containsItem = function(id) {
        return this.getTradeItemNode(id).length > 0;
    };

    /**
     * Listens to drop picked up events.
     *
     * @param {Event} e The event.
     * @param {Object} data The event data.
     */
    TradeArea.prototype._dropPickedUpListener = function(e, data) {
        var userItem = data.useritem;
        if (this.containsItem(userItem.getItem().get('id'))) {
            this.updateTradeItemUserQuantity(userItem);
        }
    };

    /**
     * Can the user perform the trade?
     *
     * @return {Bool}
     */
    TradeArea.prototype.canTradeItems = function() {
        var removeditemnodes = this._node.find('.removed-items');

        var hasenough = true;
        removeditemnodes.each(function(i, node) {
            hasenough = hasenough && $(node).data('hasenough');
        });

        return hasenough;
    };

    /**
     * Get the trade item node.
     *
     * @param {Number} id The item ID.
     * @return {Node}
     */
    TradeArea.prototype.getTradeItemNode = function(id) {
        return this._node.find('.removed-items[data-itemid=' + id + ']');
    };

    /**
     * Update the quantity of a user item.
     *
     * @param {UserItem} userItem The user item.
     */
    TradeArea.prototype.updateTradeItemUserQuantity = function(userItem) {
        var itemid = userItem.getItem().get('id'),
            node = this.getTradeItemNode(itemid),
            newQuantity = parseInt(userItem.get('quantity'), 10),
            quantity = parseInt(node.attr('data-quantity'), 10),
            tradeid = node.parent().attr('data-tradeid'),
            name = userItem.getItem().get('name'),
            enoughitems = (newQuantity >= quantity);

        var context = {
            enoughitems: enoughitems,
            itemid: itemid,
            quantity: quantity,
            name: name,
            userquantity: newQuantity
        };

        // I want to switch the template to show the new values.
        Templates.render('block_stash/tradeitem_detail', context).done(function(html, js) {
            Templates.replaceNodeContents($('.block-stash-trade-item-' + itemid + '[data-tradeid="' + tradeid + '"]'), html, js);
            this._update();
        }.bind(this)).fail(notification.exception);
    };

    /**
     * Update the widget.
     *
     * @return {Void}
     */
    TradeArea.prototype._update = function() {
        var btn = this._node.find('.accept-trade button').first();
        if (!btn) {
            return;
        }

        if (!this.canTradeItems()) {
            btn.prop('disabled', true);
        } else {
            btn.prop('disabled', false);
        }
    };

    return /** @alias module:block_stash/trade-area */ TradeArea;

});
