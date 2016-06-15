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
 * Drop snippet UI module.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
], function($) {

    /**
     * Snippet UI class.
     *
     * @param {DropSnippetMaker} maker The snippet maker.
     * @param {Node} container The container of the UI.
     */
    function UI(maker, container) {
        this._container = $(container);
        this._maker = maker;
        this._init();
    }

    UI.prototype._container = null;
    UI.prototype._appearanceNode = null;
    UI.prototype._labelNode = null;
    UI.prototype._actionTextNode = null;
    UI.prototype._previewNode = null;
    UI.prototype._actionTextNode = null;
    UI.prototype._snippetNode = null;

    UI.prototype._actionTextZoneNode = null;
    UI.prototype._labelZoneNode = null;

    /**
     * Initialise the things.
     *
     * @return {Void}
     */
    UI.prototype._init = function() {
        this._appearanceNode = this.find('[name=displaytype]');
        this._labelNode = this.find('[name=label]');
        this._actionTextNode = this.find('[name=actiontext]');
        this._snippetNode = this.find('[name="snippet"]');
        this._previewNode = this.find('.preview');

        this._labelZoneNode = this.find('.snippet-label');
        this._actionTextZoneNode = this.find('.snippet-actiontext');

        // Set placeholders.
        this._labelNode.attr('placeholder', this._maker.getDefaultLabel());
        this._actionTextNode.attr('placeholder', this._maker.getDefaultActionText());

        // Set form values.
        this._maker.setLabel(this._labelNode.val());
        this._maker.setDisplayType(this._appearanceNode.val());
        this._maker.setActionText(this._actionTextNode.val());

        this._updateForm();
        this._setListeners();
        this.update();
    };

    /**
     * Find a node in this UI.
     *
     * @param {String} selector The selector.
     * @return {Node}
     */
    UI.prototype.find = function(selector) {
        return this._container.find(selector);
    };

    /**
     * Set the event listeners.
     *
     * @return {Void}
     */
    UI.prototype._setListeners = function() {
        this._appearanceNode.change(function(e) {
            this._maker.setDisplayType($(e.currentTarget).val());
            this._updateForm();
            this.update();
        }.bind(this));

        this._labelNode.on('keyup', function(e) {
            this._maker.setLabel($(e.currentTarget).val());
            this._updateForm();
            this.update();
        }.bind(this));

        this._actionTextNode.on('keyup', function(e) {
            this._maker.setActionText($(e.currentTarget).val());
            this._updateForm();
            this.update();
        }.bind(this));
    };

    /**
     * Update the snippet and preview.
     *
     * @return {Void}
     */
    UI.prototype.update = function() {
        var display = this._maker.getDisplay();

        display.find('a, input').click(function(e) { e.preventDefault(); return; });

        this._snippetNode.val(this._maker.getSnippet());
        this._previewNode.html(display);
    };

    /**
     * Update the form.
     *
     * @return {Void}
     */
    UI.prototype._updateForm = function() {
        var appearance = this._appearanceNode.val();

        if (appearance == this._maker.IMAGEANDBUTTON) {
            this._labelZoneNode.hide();
            this._actionTextZoneNode.show();

        } else if (appearance == this._maker.TEXT) {
            this._labelZoneNode.show();
            this._actionTextZoneNode.hide();

        } else {
            this._labelZoneNode.hide();
            this._actionTextZoneNode.hide();
        }
    };

    return /** @alias module:block_stash/drop-snippet-ui */ UI;

});
