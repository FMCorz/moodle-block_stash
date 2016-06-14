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
    Drop.prototype._quantity = 1;
    Drop.prototype.find = function() {
        return Ajax.call([{
            methodname: 'block_stash_find_drop',
            args: {
                dropid: this._id,
                hashcode: this._hashcode
            }
        }])[0];
    };

    return /** @alias module:block_stash/drop */ Drop;

});
