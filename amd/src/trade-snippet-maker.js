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
 * Trade snippet maker module.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {

    var START_TAG = '[trade:';
    var END_TAG = ']';

    /**
     * Trade snippet maker class.
     *
     * @param {Trade} trade The trade.
     */
    function TradeMaker(trade) {
        this._trade = trade;
    }

    /**
     * Get the trade.
     *
     * @return {Trade}
     */
    TradeMaker.prototype.getTrade = function() {
        return this._trade;
    };

    /**
     * Get the snippet.
     *
     * @return {String}
     */
    TradeMaker.prototype.getSnippet = function() {
        var preHash = START_TAG,
            postHash = '',
            trade = this.getTrade(),
            hashLength = 3;

        preHash += trade.get('id') + ':';
        postHash += END_TAG;
        hashLength = Math.max(3, 32 - (preHash.length + postHash.length));

        // Backup will only encode 32 characters long texts, so we ensure
        // that the recommended snippet has the required length, in case it's
        // the only thing in the textarea.
        return preHash + trade.get('hashcode').substring(0, hashLength) + postHash;
    };

    return /** @alias module:block_stash/trade-snippet-maker */ TradeMaker;

});
