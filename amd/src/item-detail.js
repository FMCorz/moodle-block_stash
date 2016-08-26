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
 * Show item detail.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/ajax',
    'core/templates',
    'core/yui',
], function($, ajax, Templates, Y) {



    function showdetail(itemid) {
        var deferred = $.Deferred();
        this._ready = deferred.promise();
        this._itemid = itemid;
        Y.use('moodle-core-notification', function() {
            this._init().then(function() {
                deferred.resolve();
            });
        }.bind(this));
    }
    showdetail.prototype._dialogue = null;
    showdetail.prototype._itemid = null;
    showdetail.prototype._item = null;
    showdetail.prototype._maker = null;
    showdetail.prototype._ready = null;
    showdetail.prototype._ui = null;

    /**
     * Initialise the things.
     *
     * @return {Void}
     */
    showdetail.prototype._init = function() {
        var deferred = $.Deferred(),
            loading = Y.Node.create('<p style="text-align: center;"><img src="' + M.util.image_url('y/loading') + '" alt=""></p>'),
            d;

        // New dialogue.
        d = new M.core.dialogue({
            draggable: true,
            modal: true,
            width: '600px',
        });
        this._dialogue = d;

        // Destroy on hide.
        var origHide = d.hide;
        d.hide = function() {
            origHide.apply(d, arguments);
            this.destroy();
        }.bind(d);


        // Async fetch real content.
        this._getItem().then(function(data) {
            // Set content.
            d.getStdModNode(Y.WidgetStdMod.HEADER).prepend(Y.Node.create('<h1>' + data.name + '</h1>'));
            d.setStdModContent(Y.WidgetStdMod.BODY, loading, Y.WidgetStdMod.REPLACE);
            Templates.render('block_stash/item_detail', data).then(function(html, js) {
                Templates.runTemplateJS(js);
                d.setStdModContent(Y.WidgetStdMod.BODY, html, Y.WidgetStdMod.REPLACE);
                d.centerDialogue();
                deferred.resolve();
            }).fail(deferred.reject);
        }).fail(deferred.reject);

        // Return the promise.
        return deferred.promise();
    };

    /**
     * Find a node in this Dialog.
     *
     * @param {String} selector The selector.
     * @return {Node}
     */
    showdetail.prototype.find = function(selector) {
        return this._container.find(selector);
    };

    /**
     * Return all the information for an item.
     *
     * @return {promise} Eventually all details for an item.
     */
    showdetail.prototype._getItem = function() {
        var promises = ajax.call([{
            methodname: 'block_stash_get_item_detail',
            args: {
                itemid: this._itemid
            }
        }], false);
        var deferred = $.Deferred();

        promises[0].done(function(data) {
            deferred.resolve(data);
        }).fail(function(ex) {
            deferred.reject(ex);
        });
        return deferred.promise();
    };

    /**
     * Initialise the things.
     *
     * @param {Event} e The event.
     * @return {Void}
     */
    showdetail.prototype.show = function(e) {
        this._ready.then(function() {
            this._dialogue.show(e);
        }.bind(this));
    };

    return showdetail;

});