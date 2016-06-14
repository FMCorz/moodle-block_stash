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
    'core/notification',
    'core/ajax',
], function($, Notification, Ajax) {

    function Drop(dropdata) {
        this._id = dropdata.id;
        this._hashcode = dropdata.hashcode;
    }
    Drop.prototype._id = 1;
    Drop.prototype._hascode = 1;
    Drop.prototype._displayIfVisible = null;
    Drop.prototype._isVisible = null;

    /**
     * Set something to be displayed when the drop is visible.
     *
     * Must be called prior to {@link self.run}.
     *
     * @param {String|Node} selector The node to display.
     * @return {Void}
     */
    Drop.prototype.displayIfVisible = function(selector) {
        this._displayIfVisible = $(selector);
    };

    /**
     * Report the drop has having been found.
     *
     * @return {Promise} Resolved when found without errors.
     */
    Drop.prototype.find = function() {
        return Ajax.call([{
            methodname: 'block_stash_pickup_drop',
            args: {
                dropid: this._id,
                hashcode: this._hashcode
            }
        }])[0];
    };

    /**
     * Run, initialise the drop in the page.
     *
     * @return {Void}
     */
    Drop.prototype.run = function() {

        if (this._displayIfVisible && this._displayIfVisible.length > 0) {
            this.isVisible().then(function(visible) {
                this._displayIfVisible.show();
            }.bind(this));
        }

    };

    /**
     * Is the drop visible to the current user?
     *
     * @return {Promise} Rejected when not visible.
     */
    Drop.prototype.isVisible = function() {
        var cb = function(visible) {
            this._isVisible = visible;
            if (!visible) {
                return $.Deferred().reject()
            }
            return visible;
        }.bind(this);

        if (this._isVisible !== null) {
            return $.when($this._isVisible).then(cb);
        }

        return Ajax.call([{
            methodname: 'block_stash_is_drop_visible',
            args: {
                dropid: this._id,
                hashcode: this._hashcode
            }
        }])[0].then(cb);
    }

    return /** @alias module:block_stash/drop */ Drop;

});
