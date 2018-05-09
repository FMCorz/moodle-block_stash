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
 * Drop snippet maker module.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
], function($) {

    /**
     * Drop snippet maker class.
     *
     * @param {Drop} drop The drop.
     */
    function Maker(drop) {
        this._drop = drop;
    }

    Maker.prototype.IMAGE = 'image';
    Maker.prototype.IMAGEANDBUTTON = 'imageandbutton';
    Maker.prototype.TEXT = 'text';

    Maker.prototype._actionText = null;
    Maker.prototype._displayType = Maker.prototype.IMAGEANDBUTTON;
    Maker.prototype._label = null;

    /**
     * Get the action text.
     *
     * @return {String}
     */
    Maker.prototype._getActionText = function() {
        if (this._actionText === null || this._actionText.trim() === '') {
            return this.getDefaultActionText();
        }
        return this._actionText;
    };

    /**
     * Get the anchor.
     *
     * @return {Node}
     */
    Maker.prototype._getAnchor = function() {
        return $('<a href="#"></a>');
    };

    /**
     * Get the default action text.
     *
     * @return {String}
     */
    Maker.prototype.getDefaultActionText = function() {
        return 'Pick up!';
    };

    /**
     * Get the default label.
     *
     * @return {String}
     */
    Maker.prototype.getDefaultLabel = function() {
        return "Pick up '" + this.getItem().get('name') + "'";
    };

    /**
     * Get the button.
     *
     * @return {Node}
     */
    Maker.prototype._getDisplayButton = function() {
        var wrap = $('<div class="item-action"></div>'),
            btn = $('<button class="btn btn-secondary"></button>');

        btn.text(this._getActionText());
        wrap.append(btn);

        return wrap;
    };

    /**
     * Get the image.
     *
     * @return {Node}
     */

    Maker.prototype._getDisplayImage = function() {
        var img = $('<div class="item-image"></div>'),
            label = $('<div class="item-label"></div>');

        img.addClass('item-image');
        img.css('backgroundImage', 'url(' + this.getItem().get('imageurl') + ')');
        label.text(this.getItem().get('name'));
        label.attr('title', this.getItem().get('name'));
        img.append(label);

        return img;
    };

    /**
     * Get the drop.
     *
     * @return {Drop}
     */
    Maker.prototype.getDrop = function() {
        return this._drop;
    };

    /**
     * Get the whole display.
     *
     * @return {Node}
     */
    Maker.prototype.getDisplay = function() {
        return this._getDisplayType(this._displayType);
    };

    /**
     * Get the display by type.
     *
     * @param {String} displaytype The display type.
     * @return {Drop}
     */
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

    /**
     * Get the drop item.
     *
     * @return {Item}
     */
    Maker.prototype.getItem = function() {
        return this._drop.getItem();
    };

    /**
     * Get the label.
     *
     * @return {String}
     */
    Maker.prototype._getLabel = function() {
        if (this._label === null || this._label.trim() === '') {
            return this.getDefaultLabel();
        }
        return this._label;
    };

    /**
     * Get the snippet.
     *
     * @return {String}
     */
    Maker.prototype.getSnippet = function() {
        var node = $('<div>'),
            uuid = this.uuid(),
            wrapper = $('<span id="' + uuid + '">'),
            script = $('<script type="text/javascript">'),
            id = this.getDrop().get('id'),
            hashcode = this.getDrop().get('hashcode'),
            display = this.getDisplay(),
            snippet = '';

        // No need to scope this in an anonymous function, it already is because of require.
        // WARNING! Changing this structure could break restores!
        snippet = '' +
            'require(["jquery", "block_stash/drop"], function($, D) {' +
            ' var d = new D({id: ' + id + ', hashcode: "' + hashcode + '"}), n = $("#' + uuid + '");' +
            ' if (!n.length) return; n.removeClass(); d.isVisible().then(function() { n.show(); });' +
            ' n.find("a, button").click(function(e) { e.preventDefault(); d.pickup(); n.remove(); });' +
            '})';
        snippet = this._wrapForOnReady(snippet);

        node.append(wrapper);
        wrapper.css('display', 'none');
        wrapper.append(display);
        wrapper.append(script);
        script.html(snippet);

        return node.html();
    };

    /**
     * Set the display type.
     *
     * @param {String} v The display type.
     */
    Maker.prototype.setDisplayType = function(v) {
        this._displayType = v;
    };

    /**
     * Set the action text.
     *
     * @param {String} v The action text.
     */
    Maker.prototype.setActionText = function(v) {
        this._actionText = v;
    };

    /**
     * Set the label.
     *
     * @param {String} v The label.
     */
    Maker.prototype.setLabel = function(v) {
        this._label = v;
    };

    /**
     * Get a unique ID.
     *
     * @return {String}
     */
    Maker.prototype.uuid = function() {
        return (Math.random().toString(36).substring(2, 7)) + (((new Date()).getTime()).toString(36));
    };

    /**
     * Wrap some script in a function waiting for the document to be ready.
     *
     * @param {String} code The script.
     * @return {String} The new script.
     */
    Maker.prototype._wrapForOnReady = function(code) {
        return 'document.addEventListener("DOMContentLoaded", function(e) {' + code + '});';
    };

    return /** @alias module:block_stash/drop-snippet-maker */ Maker;

});
