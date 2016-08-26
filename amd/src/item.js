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
 * Item module.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/ajax',
    'block_stash/base',
], function(Ajax, Base) {

    /**
     * Item class.
     *
     * @param {Object} itemdata Data of the item.
     */
    function Item(itemdata) {
        Base.prototype.constructor.apply(this, [itemdata]);
    }
    Item.prototype = Object.create(Base.prototype);

    /**
     * Get an item.
     *
     * @param {Number} itemId The item ID.
     * @return {Promise} Resolved with the item.
     * @static
     */
    Item.getItem = function(itemId) {
        return Ajax.call([{
            methodname: 'block_stash_get_item',
            args: {
                itemid: itemId
            }
        }])[0].then(function(data) {
            return new Item(data);
        });
    };

    return /** @alias module:block_stash/item */ Item;

});
