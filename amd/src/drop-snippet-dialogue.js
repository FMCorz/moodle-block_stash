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
 * Drop snippet dialogue module.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'core/templates',
    'core/yui',
    'block_stash/drop-snippet-maker',
    'block_stash/drop-snippet-ui',
], function($, Templates, Y) {

    /**
     * Snippet dialogue class.
     *
     * @param {Drop} drop The drop.
     * @param {Node} container The container of the Dialog.
     */
    function Dialog(drop) {
        var deferred = $.Deferred();
        this._ready = deferred.promise();
        this._drop = drop;
        Y.use('moodle-core-notification', function() {
            this._init().then(function() {
                deferred.resolve();
            });
        }.bind(this));
    }
    Dialog.prototype._dialogue = null;
    Dialog.prototype._drop = null;
    Dialog.prototype._maker = null;
    Dialog.prototype._ready = null;
    Dialog.prototype._ui = null;

    /**
     * Initialise the things.
     *
     * @return {Void}
     */
    Dialog.prototype._init = function() {
        var deferred = $.Deferred(),
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

        // Set content.
        d.getStdModNode(Y.WidgetStdMod.HEADER).prepend(Y.Node.create('<h1>' + this._drop.get('name') + '</h1>'));
        this._render().then(function(html, js) {
            Templates.runTemplateJS(js);
            d.setStdModContent(Y.WidgetStdMod.BODY, html, Y.WidgetStdMod.REPLACE);
        }).then(function() {
            // Resolve the promise.
            deferred.resolve();
        });


        // Return the promise.
        return deferred;
    };

    /**
     * Find a node in this Dialog.
     *
     * @param {String} selector The selector.
     * @return {Node}
     */
    Dialog.prototype.find = function(selector) {
        return this._container.find(selector);
    };

    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    Dialog.prototype._render = function() {
        var context = {
            drop: this._drop.getData(),
            dropjson: JSON.stringify(this._drop.getData()),
            item: this._drop.getItem().getData(),
            itemjson: JSON.stringify(this._drop.getItem().getData()),
        };
        return Templates.render('block_stash/drop_snippet_dialogue', context);
    };

    /**
     * Initialise the things.
     *
     * @param {Event} e The event.
     * @return {Void}
     */
    Dialog.prototype.show = function(e) {
        this._ready.then(function() {
            this._dialogue.show(e);
        }.bind(this));
    };

    return /** @alias module:block_stash/drop-snippet-dialogue */ Dialog;

});
