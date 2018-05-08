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
 * Drop snippet shortcode maker module.
 *
 * @package    block_stash
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'jquery',
    'block_stash/drop-snippet-maker',
], function($, MakerBase) {

    /**
     * Drop snippet maker class.
     *
     * @class
     * @extends {module:block_stash/drop-snippet-maker}
     */
    function Maker() {
        MakerBase.prototype.constructor.apply(this, arguments);
    }
    Maker.prototype = Object.create(MakerBase.prototype);

    Maker.prototype._getActionText = function() {
        var txt = MakerBase.prototype._getActionText.apply(this, arguments);
        return txt.replace('"', '\\"');
    };

    Maker.prototype._getLabel = function() {
        var txt = MakerBase.prototype._getLabel.apply(this, arguments);
        return txt.replace('"', '\\"');
    };

    Maker.prototype.getSnippet = function() {
        var drop = this.getDrop(),
            hashLength = 6,
            secret = drop.get('hashcode').substring(0, hashLength),
            shortcode = '[stashdrop secret="' + secret + '"',
            text = '',
            image = true;

        if (this._displayType == this.IMAGEANDBUTTON) {
            text = this._getActionText();
        } else if (this._displayType == this.TEXT) {
            image = false;
            text = this._getLabel();
        }

        if (text.length) {
            shortcode += ' text="' + text + '"';
        }
        if (image) {
            shortcode += ' image';
        }

        return shortcode + ']';
    };

    return /** @alias module:block_stash/drop-snippet-shortcode-maker */ Maker;

});
