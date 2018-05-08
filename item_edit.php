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
 * Item edit page.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', '0', PARAM_INT);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$context = context_course::instance($courseid);
$url = new moodle_url('/blocks/stash/item_edit.php', array('courseid' => $courseid, 'id' => $id));

$item = $id ? $manager->get_item($id) : null;
$itemname = $item ? format_string($item->get_name(), true, ['context' => $context]) : '';
$pagetitle = $item ? get_string('edititem', 'block_stash', $itemname) : get_string('additem', 'block_stash');
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_item($url, $manager, $item, $pagetitle);

$fileareaoptions = ['maxfiles' => 1];
$editoroptions = ['noclean' => true, 'maxfiles' => -1, 'maxbytes' => $CFG->maxbytes, 'context' => $context];
$customdata = [
    'fileareaoptions' => $fileareaoptions,
    'editoroptions' => $editoroptions,
    'persistent' => $item,
    'stash' => $manager->get_stash(),
];

$renderer = $PAGE->get_renderer('block_stash');
$form = new \block_stash\form\item($url->out(false), $customdata);

$draftitemid = file_get_submitted_draft_itemid('item');
file_prepare_draft_area($draftitemid, $context->id, 'block_stash', 'item', $id, $fileareaoptions);
$data = new stdClass();
if (!is_null($item)) {
    $data->id = $item->get_id();
    $data->detail = $item->get_detail();
    $data->detailformat = $item->get_detailformat();
} else {
    $data->id = $id;
    $data->detail = '';
    $data->detailformat = 1;
}
$data = file_prepare_standard_editor($data, 'detail', $editoroptions, $context, 'block_stash', 'detail', $data->id);
$form->set_data((object) array('image' => $draftitemid, 'detail_editor' => $data->detail_editor));

if ($data = $form->get_data()) {

    $saveandnext = !empty($data->saveandnext);
    unset($data->saveandnext);
    $draftitemid = $data->image;
    unset($data->image);

    // Add editor options to the data stdClass.
    $data->editoroptions = $editoroptions;
    $data->fileareaoptions = $fileareaoptions;

    $saveditem = $manager->create_or_update_item($data, $draftitemid);
    if ($saveandnext) {
        redirect(new moodle_url('/blocks/stash/drop.php', ['itemid' => $saveditem->get_id(),
            'courseid' => $manager->get_courseid()]));
    }
    redirect($returnurl);

} else if ($form->is_cancelled()) {
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo $renderer->navigation($manager, 'items');
if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle, 3);
}
$form->display();
echo $OUTPUT->footer();
