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
 * Base module.
 *
 * This simply allows for data to be added and fetched from an object.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
], function() {

    /**
     * Base class.
     *
     * @param {Object} data Data of the item.
     */
    function Base(data) {
        this._data = data || {};
    }
    Base.prototype._data = null;

    /**
     * Return a property of the item.
     *
     * @param {String} property The name of the property.
     * @return {Mixed}
     */
    Base.prototype.get = function(property) {
        return this._data[property];
    };

    /**
     * Return the data of this item.
     *
     * @return {Object}
     */
    Base.prototype.getData = function() {
        return this._data;
    };

    return /** @alias module:block_stash/base */ Base;

});
