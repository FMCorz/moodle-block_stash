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
 * Drop module.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/log',
    'block_stash/base',
    'block_stash/counselor',
    'block_stash/item',
    'block_stash/user-item',
], function($, Ajax, Log, Base, Counselor, Item, UserItem) {

    /**
     * Drop class.
     *
     * @param {Object} dropdata The data of this drop.
     * @param {Item} item The item related to this drop.
     */
    function Drop(dropdata, item) {
        Base.prototype.constructor.apply(this, [dropdata]);
        this._item = item;
    }
    Drop.prototype = Object.create(Base.prototype);

    Drop.prototype.EVENT_PICKEDUP = 'drop:pickedup';

    /**
     * Return the item of this drop.
     *
     * @return {Item}
     */
    Drop.prototype.getItem = function() {
        return this._item;
    };

    /**
     * Is the drop visible to the current user?
     *
     * @return {Promise} Rejected when not visible.
     */
    Drop.prototype.isVisible = function() {
        return Ajax.call([{
            methodname: 'block_stash_is_drop_visible',
            args: {
                dropid: this.get('id'),
                hashcode: this.get('hashcode')
            }
        }])[0].then(function(visible) {
            if (!visible) {
                return $.Deferred().reject();
            }
            return true;
        });
    };

    /**
     * Report the drop has having been picked up.
     *
     * @return {Promise} Resolved when picked up without errors.
     */
    Drop.prototype.pickup = function() {
        return Ajax.call([{
            methodname: 'block_stash_pickup_drop',
            args: {
                dropid: this.get('id'),
                hashcode: this.get('hashcode')
            }
        }])[0].fail(function() {
            Log.debug('The item could not be picked up.');

        }).then(function(data) {
            // Do not change this._item as it's not a predictable behaviour.
            var userItem = new UserItem(data.useritem, new Item(data.item));
            Counselor.trigger(this.EVENT_PICKEDUP, {
                id: this.get('id'),
                hashcode: this.get('hashcode'),
                useritem: userItem
            });
        }.bind(this));
    };

    return /** @alias module:block_stash/drop */ Drop;

});
