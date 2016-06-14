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

$manager = \block_stash\manager::get($courseid);

$context = $manager->get_context();

require_login($courseid);
$url = new moodle_url('/blocks/stash/inventory_edit.php', array('courseid' => $courseid, 'id' => $id));
$listurl = new moodle_url('/blocks/stash/inventory.php', ['courseid' => $courseid]);

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title('Item');
$PAGE->set_heading('Item');
$PAGE->set_url($url);

$item = $id ? $manager->get_item($id) : null;

$fileareaoptions = ['maxfiles' => 1];
$customdata = [
    'fileareaoptions' => $fileareaoptions,
    'persistent' => $item,
    'stash' => $manager->get_stash(),
];

$form = new \block_stash\form\item($url->out(false), $customdata);

$draftitemid = file_get_submitted_draft_itemid('item');
file_prepare_draft_area($draftitemid, $context->id, 'block_stash', 'item', $id, $fileareaoptions);
$form->set_data((object) array('image' => $draftitemid));

if ($data = $form->get_data()) {

    $draftitemid = $data->image;
    unset($data->image);

    $thing = $manager->create_or_update_item($data, $draftitemid);
    redirect($listurl);

} else if ($form->is_cancelled()) {
    redirect($listurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading('Item');

echo $form->render();




$renderer = $PAGE->get_renderer('block_stash');
// $page = new \block_stash\output\inventory_page($courseid);
// Show inventory for teachers (Maybe students as well).
// echo $renderer->render_inventory_page($page);



echo $OUTPUT->footer();