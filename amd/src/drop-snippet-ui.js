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
    'core/notification',
    'core/ajax',
], function($, Notification, Ajax) {

    function UI(snippet, container) {
        this._container = $(container);
        this._snippet = snippet;
        this._init();
    }

    UI.prototype._container;
    UI.prototype._appearanceNode;
    UI.prototype._labelNode;
    UI.prototype._actionTextNode;
    UI.prototype._previewNode;
    UI.prototype._actionTextNode;

    UI.prototype._actionTextZoneNode;
    UI.prototype._labelZoneNode;

    UI.prototype._init = function() {
        this._appearanceNode = this.find('[name=displaytype]');
        this._labelNode = this.find('[name=label]');
        this._actionTextNode = this.find('[name=actiontext]');
        this._snippetNode = this.find('[name="snippet"]');
        this._previewNode = this.find('.preview');

        this._labelZoneNode = this.find('.snippet-label');
        this._actionTextZoneNode = this.find('.snippet-actiontext');

        // Set placeholders.
        this._labelNode.attr('placeholder', this._snippet.getDefaultLabel());
        this._actionTextNode.attr('placeholder', this._snippet.getDefaultActionText());

        // Set form values.
        this._snippet.setLabel(this._labelNode.val());
        this._snippet.setDisplayType(this._appearanceNode.val());
        this._snippet.setActionText(this._actionTextNode.val());

        this._updateForm();
        this._setListeners();
        this.update();
    }

    UI.prototype.find = function(selector) {
        return this._container.find(selector);
    };

    UI.prototype._setListeners = function() {
        this._appearanceNode.change(function(e) {
            this._snippet.setDisplayType($(e.currentTarget).val());
            this._updateForm();
            this.update();
        }.bind(this));

        this._labelNode.on('keyup', function(e) {
            this._snippet.setLabel($(e.currentTarget).val());
            this._updateForm();
            this.update();
        }.bind(this));

        this._actionTextNode.on('keyup', function(e) {
            this._snippet.setActionText($(e.currentTarget).val());
            this._updateForm();
            this.update();
        }.bind(this));
    };

    UI.prototype.update = function() {
        var display = this._snippet.getDisplay();

        display.find('a, input').click(function(e) { e.preventDefault(); return; });

        this._snippetNode.val(this._snippet.getSnippet());
        this._previewNode.html(display);
    }

    UI.prototype._updateForm = function() {
        var appearance = this._appearanceNode.val();

        if (appearance == this._snippet.IMAGEANDBUTTON) {
            this._labelZoneNode.hide();
            this._actionTextZoneNode.show();

        } else if (appearance == this._snippet.TEXT) {
            this._labelZoneNode.show();
            this._actionTextZoneNode.hide();

        } else {
            this._labelZoneNode.hide();
            this._actionTextZoneNode.hide();
        }
    }

    return /** @alias module:block_stash/drop-snippet-ui */ UI;

});
