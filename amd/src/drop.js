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
], function($, Ajax, Log) {

    /**
     * Drop class.
     *
     * @param {Object} dropdata The data of this drop.
     * @param {Item} item The item related to this drop.
     */
    function Drop(dropdata, item) {
        this._data = dropdata || {};
        this._item = item;
    }
    Drop.prototype._data = null;

    /**
     * Return a property of the drop.
     *
     * @param {String} property The name of the property.
     * @return {Mixed}
     */
    Drop.prototype.get = function(property) {
        return this._data[property];
    };

    /**
     * Return the data of this drop.
     *
     * @return {Object}
     */
    Drop.prototype.getData = function() {
        return this._data;
    };

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
        });
    };

    return /** @alias module:block_stash/drop */ Drop;

});
