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
 * Stash module.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/templates',
    'block_stash/counselor',
    'block_stash/drop'
], function($, Templates, Counselor, Drop) {

    /**
     * Stash class.
     *
     * @class
     * @param {Node} node The node.
     */
    function StashArea(node) {
        this._node = $(node);

        Counselor.on(Drop.prototype.EVENT_PICKEDUP, this._dropPickedUpListener.bind(this));
    }
    StashArea.prototype._node = null;
    StashArea.prototype._userItemTemplate = 'block_stash/user_item';

    /**
     * Add a user item to the stash.
     *
     * @param {UserItem} userItem The user item.
     * @return {Promise}
     */
    StashArea.prototype.addUserItem = function(userItem) {
        return this._renderUserItem(userItem).then(function(html, js) {
            $(html).data('useritem', userItem);
            this._node.find('.item-list').append(html);
            Templates.runTemplateJS(js);
        }.bind(this));
    };

    /**
     * Whether the item is in the stash.
     *
     * @param {Number} id The item ID.
     * @return {Boolean}
     */
    StashArea.prototype.containsItem = function(id) {
        return this.getUserItemNode(id).length > 0;
    };

    /**
     * Listens to drop picked up events.
     *
     * @param {Event} e The event.
     * @param {Object} data The event data.
     */
    StashArea.prototype._dropPickedUpListener = function(e, data) {
        var userItem = data.useritem;
        if (this.containsItem(userItem.getItem().get('id'))) {
            this.updateUserItemQuantity(userItem);
        } else {
            this.addUserItem(userItem).then(function() {
                this._node.find('.alert').hide();
            }.bind(this));
        }
    };

    /**
     * Get the user item node.
     *
     * @param {Number} id The item ID.
     * @return {Node}
     */
    StashArea.prototype.getUserItemNode = function(id) {
        return this._node.find('.block-stash-item[data-id=' + id + ']');
    };

    /**
     * Render a user item.
     *
     * @param {UserItem} userItem The user item.
     * @return {Promise}
     */
    StashArea.prototype._renderUserItem = function(userItem) {
        return Templates.render(this._userItemTemplate, {
            item: userItem.getItem().getData(),
            useritem: userItem.getData(),
        });
    };

    /**
     * Update the quantity of a user item.
     *
     * @param {UserItem} userItem The user item.
     */
    StashArea.prototype.updateUserItemQuantity = function(userItem) {
        var node = this.getUserItemNode(userItem.getItem().get('id')),
            quantityNode = node.find('.item-quantity'),
            newQuantity = userItem.get('quantity'),
            quantity = parseInt(quantityNode.text(), 10);

        quantityNode.text(newQuantity);
        node.removeClass('item-quantity-' + quantity);
        node.addClass('item-quantity-' + newQuantity);
    };

    return /** @alias module:block_stash/stash */ StashArea;

});
