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
 * Counselor module.
 *
 * This module is responsible for helping different parts of the stash communicate.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {

    /**
     * Counselor class.
     *
     * @class
     */
    function Counselor() {
        this._node = $('<div>');
    }
    Counselor.prototype._node = null;
    Counselor.prototype.ITEM_SAVE = 'item-save';

    /**
     * Register an event listener.
     *
     * @param {String} eventType The event type.
     * @param {Function} callback The event listener.
     * @method on
     */
    Counselor.prototype.on = function(eventType, callback) {
        this._node.on(eventType, callback);
    };

    /**
     * Trigger an event.
     *
     * @param {String} eventType The type of event.
     * @param {Object} data The data to pass to the listeners.
     * @method trigger
     */
    Counselor.prototype.trigger = function(eventType, data) {
        this._node.trigger(eventType, [data]);
    };

    return /** @alias module:block_stash/counselor */ new Counselor();

});
