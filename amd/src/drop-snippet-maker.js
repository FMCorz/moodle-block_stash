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
 * Drop snippet module.
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

    function Maker(drop) {
        this._drop = drop;
        this._init();
    }

    Maker.prototype.IMAGE = 'image';
    Maker.prototype.IMAGEANDBUTTON = 'imageandbutton';
    Maker.prototype.TEXT = 'text';

    Maker.prototype._actionText = null;
    Maker.prototype._checkIfVisible = true;
    Maker.prototype._displayType = Maker.prototype.IMAGEANDBUTTON;
    Maker.prototype._label = null;

    Maker.prototype._init = function() {
    };

    Maker.prototype.setCheckIfVisible = function(v) {
        this._checkIfVisible = v;
    };

    Maker.prototype._getActionText = function() {
        if (this._actionText === null || this._actionText.trim() === '') {
            return this.getDefaultActionText();
        }
        return this._actionText;
    };

    Maker.prototype._getAnchor = function() {
        return $('<a href="#"></a>');
    };

    Maker.prototype.getDefaultActionText = function() {
        return 'Pick up!';
    };

    Maker.prototype.getDefaultLabel = function() {
        return "Pick up '" + this.getItem().get('name') + "'";
    };

    Maker.prototype._getDisplayButton = function() {
        var wrap = $('<div class="item-action"></div>'),
            btn = $('<button></button>');

        btn.text(this._getActionText());
        wrap.append(btn);

        return wrap;
    };

    Maker.prototype._getDisplayImage = function() {
        var img = $('<div class="item-image"></div>'),
            label = $('<div class="item-label"></div>');

        img.addClass('item-image');
        img.css('backgroundImage', 'url(' + this.getItem().get('imageurl') + ')');;
        label.text(this.getItem().get('name'));
        label.attr('title', this.getItem().get('name'));
        img.append(label);

        return img;
    }

    Maker.prototype.getDrop = function() {
        return this._drop;
    }

    Maker.prototype.getDisplay = function() {
        return this._getDisplayType(this._displayType);
    }

    Maker.prototype._getDisplayType = function(displaytype) {
        var display,
            anchor;

        if (displaytype == this.IMAGE) {
            display = $('<div class="block-stash-item"></div>');
            anchor = this._getAnchor();
            anchor.append(this._getDisplayImage());
            display.append(anchor);

        } else if (displaytype == this.IMAGEANDBUTTON) {
            display = $('<div class="block-stash-item"></div>');
            display.append(this._getDisplayImage());
            display.append(this._getDisplayButton());

        } else {
            display = $('<span></span>');
            anchor = this._getAnchor();
            anchor.text(this._getLabel());
            display.append(anchor);
        }

        return display;
    };

    Maker.prototype.getItem = function() {
        return this._drop.getItem();
    }

    Maker.prototype._getLabel = function() {
        if (this._label === null || this._label.trim() === '') {
            return this.getDefaultLabel();
        }
        return this._label;
    };

    Maker.prototype.getSnippet = function() {
        var snippet = '' +
            this.getDisplay().html() + '\n' +
            '<script>' + '\n' +
            '    Y.on("domready", () => {' + '\n' +
            '        require(["jquery", "block_stash/drop"], function($, Drop) {' + '\n' +
            '            var d = new Drop({' + '\n' +
            '                id: 1,' + '\n' +
            '                hashcode: ""' + '\n' +
            '            });' + '\n';

        if (this._checkIfVisible) {
            snippet += 'd.displayIfVisible("#find-the-hammer");' + '\n';
        }

        snippet += '' +
            '            d.run();' + '\n' +
            '            $("#find-the-hammer").click((e) => {' + '\n' +
            '                e.preventDefault();' + '\n' +
            '                d.find().then(() => {' + '\n' +
            '                    e.currentTarget.remove();' + '\n' +
            '                });' + '\n' +
            '            });' + '\n' +
            '        });' + '\n' +
            '    });' + '\n' +
            '</script>';

        return snippet;
    };

    Maker.prototype.setDisplayType = function(v) {
        this._displayType = v;
    }

    Maker.prototype.setActionText = function(v) {
        this._actionText = v;
    }

    Maker.prototype.setLabel = function(v) {
        this._label = v;
    }

    Maker.prototype._wrapForOnReady = function(code) {
        return 'document.addEventListener("DOMContentLoaded", function(event) {' + code + '});';
    }

    return /** @alias module:block_stash/drop-snippet-maker */ Maker;

});
