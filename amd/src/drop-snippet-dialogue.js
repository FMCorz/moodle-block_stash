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
    'block_stash/dialogue-base'
], function(Templates, DialogueBase) {

    /**
     * Snippet dialogue class.
     *
     * @param {Drop} drop The drop.
     * @param {Array} warnings List of warnings.
     * @param {String} altSnippetMaker The name of the alternat snippet maker module.
     */
    function Dialog(drop, warnings, altSnippetMaker) {
        this._drop = drop;
        this._warnings = warnings;
        this._altSnippetMaker = altSnippetMaker;
        this.setTitle(drop.get('name'));
        DialogueBase.prototype.constructor.apply(this, []);
    }
    Dialog.prototype = Object.create(DialogueBase.prototype);
    Dialog.prototype.constructor = Dialog;

    Dialog.prototype._drop = null;
    Dialog.prototype._altSnippetMaker = null;
    Dialog.prototype._warnings = null;

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
            altsnippetmaker: this._altSnippetMaker,
            warnings: this._warnings,
            haswarnings: this._warnings && this._warnings.length,
        };
        return Templates.render('block_stash/drop_snippet_dialogue', context)
        .then(function(html, js) {
            this._setDialogueContent(html);
            this.center();
            Templates.runTemplateJS(js);
        }.bind(this));
    };

    return /** @alias module:block_stash/drop-snippet-dialogue */ Dialog;

});
