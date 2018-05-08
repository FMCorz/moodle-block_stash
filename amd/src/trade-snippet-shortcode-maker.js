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
 * Trade snippet shortcode maker module.
 *
 * @package    block_stash
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([
    'jquery',
    'block_stash/trade-snippet-maker',
], function($, MakerBase) {

    /**
     * Trade snippet maker class.
     *
     * @class
     * @extends {module:block_stash/trade-snippet-maker}
     */
    function Maker() {
        MakerBase.prototype.constructor.apply(this, arguments);
    }
    Maker.prototype = Object.create(MakerBase.prototype);

    /**
     * Get the snippet.
     *
     * @return {String}
     */
    Maker.prototype.getSnippet = function() {
        var trade = this.getTrade(),
            hashLength = 6,
            secret = trade.get('hashcode').substring(0, hashLength);

        return '[stashtrade secret="' + secret + '"]';
    };

    return /** @alias module:block_stash/trade-snippet-shortcode-maker */ Maker;

});
