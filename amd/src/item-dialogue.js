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
 * Show item detail.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([
    'core/templates',
    'block_stash/dialogue-base',
    'block_stash/item',
], function(Templates, DialogueBase, Item) {

    /**
     * Detail dialogue class.
     *
     * @param {Number} itemId The item ID.
     */
    function Detail(itemId) {
        this._itemId = itemId;
        DialogueBase.prototype.constructor.apply(this, []);
    }
    Detail.prototype = Object.create(DialogueBase.prototype);
    Detail.prototype.constructor = Detail;

    Detail.prototype._itemId = null;

    /**
     * Render the dialogue.
     *
     * @method _render
     * @return {Promise}
     */
    Detail.prototype._render = function() {
        return Item.getItem(this._itemId).then(function(item) {
            var data = item.getData();
            Templates.render('block_stash/item_detail', data).then(function(html, js) {
                Templates.runTemplateJS(js);
                this.setTitle(data.name);
                this._setDialogueContent(html);
                this.center();
            }.bind(this));
        }.bind(this));
    };

    return Detail;

});
