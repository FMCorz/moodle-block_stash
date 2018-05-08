<?php
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
 * Items page.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$itemid = optional_param('itemid', 0, PARAM_INT);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$url = new moodle_url('/blocks/stash/items.php', array('courseid' => $courseid));
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_item($url, $manager);

switch ($action) {
    case 'delete':
        require_sesskey();
        $item = $manager->get_item($itemid);
        $manager->delete_item($item);
        redirect($url, get_string('theitemhasbeendeleted', 'block_stash', $item->get_name()));
        break;
}

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();

echo $OUTPUT->heading($title);
echo $renderer->navigation($manager, 'items');

$addurl = new moodle_url('/blocks/stash/item_edit.php', ['courseid' => $courseid]);
$addbtn = $OUTPUT->single_button($addurl, get_string('additem', 'block_stash'), 'get', ['class' => 'singlebutton heading-button']);
$heading = get_string('itemslist', 'block_stash') . $addbtn;
echo $OUTPUT->heading($heading, 3);

$table = new \block_stash\output\items_table('itemstable', $manager, $renderer);
$table->define_baseurl($url);
echo $table->out(50, false);

list($altsnippetmaker, $warning) = \block_stash\helper::get_alternate_amd_snippet_maker($manager->get_context());
$altsnippetmaker = json_encode($altsnippetmaker->drop);
$warnings = json_encode($warning ? [$warning] : null);

$PAGE->requires->js_init_code("require([
    'jquery',
    'block_stash/drop',
    'block_stash/drop-snippet-dialogue',
    'block_stash/item'
], function($, Drop, Dialogue, Item) {
    var altsnippetmaker = $altsnippetmaker,
        warnings = $warnings;
    $('table.itemstable [rel=block-stash-drop]').click(function(e) {
        var node = $(e.currentTarget),
            item = new Item(node.data('item')),
            drop = new Drop(node.data('json'), item),
            dialogue = new Dialogue(drop, warnings, altsnippetmaker);

        e.preventDefault();
        dialogue.show(e);
    });
});", true);

echo $OUTPUT->footer();
