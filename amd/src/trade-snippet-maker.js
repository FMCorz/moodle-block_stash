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

define([
    'jquery',
], function($) {

    var ILLEGALCHARS = /]/g;
    var START_TAG = '[trade:';
    var END_TAG = ']';

/**
     * Trade snippet maker class.
     *
     * @param {Drop} drop The drop.
     */
    function TradeMaker(trade) {
        this._trade = trade;
    }

    /**
     * Get the drop.
     *
     * @return {Drop}
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
        var snippet = START_TAG,
            trade = this.getTrade();

        snippet += trade.get('id') + ':' + trade.get('hashcode').substring(0, 3);
        snippet += END_TAG;
        return snippet;
    };



    /**
     * Get a unique ID.
     *
     * @return {String}
     */
    // TradeMaker.prototype.uuid = function() {
    //     return (Math.random().toString(36).substring(2, 7)) + (((new Date()).getTime()).toString(36));
    // };

    /**
     * Wrap some script in a function waiting for the document to be ready.
     *
     * @param {String} code The script.
     * @return {String} The new script.
     */
    // TradeMaker.prototype._wrapForOnReady = function(code) {
    //     return 'document.addEventListener("DOMContentLoaded", function(e) {' + code + '});';
    // };

    return /** @alias module:block_stash/drop-snippet-maker */ TradeMaker;

});