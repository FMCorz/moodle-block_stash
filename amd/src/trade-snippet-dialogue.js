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
    'core/templates',
    'block_stash/dialogue-base',
    'core/ajax',
], function(Templates, DialogueBase, ajax) {

    /**
     * Snippet dialogue class.
     *
     * @param {Trade} trade The trade widget.
     * @param {Array} warnings List of warnings.
     * @param {String} altSnippetMaker The name of the alternate snippet maker module.
     */
    function Dialog(trade, warnings, altSnippetMaker) {
        this._trade = trade;
        this._warnings = warnings;
        this._altSnippetMaker = altSnippetMaker;
        this.setTitle(trade.get('name'));
        DialogueBase.prototype.constructor.apply(this, []);
    }
    Dialog.prototype = Object.create(DialogueBase.prototype);
    Dialog.prototype.constructor = Dialog;

    Dialog.prototype._trade = null;
    Dialog.prototype._warnings = null;

    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    Dialog.prototype._render = function() {
        var context = {
            trade: this._trade.getData(),
            tradejson: JSON.stringify(this._trade.getData()),
            warnings: this._warnings,
            haswarnings: this._warnings && this._warnings.length,
            altsnippetmaker: this._altSnippetMaker
        };

        return ajax.call([
            {methodname: 'block_stash_get_trade_items', args: {
                tradeid: this._trade.get('id')
            }}
        ])[0].then(function(stuff) {
            context.tradeitems = stuff;
            Templates.render('block_stash/trade_snippet_dialogue', context)
            .then(function(html, js) {
                this._setDialogueContent(html);
                this.center();
                Templates.runTemplateJS(js);
            }.bind(this));

        }.bind(this));

    };

    return /** @alias module:block_stash/drop-snippet-dialogue */ Dialog;

});
