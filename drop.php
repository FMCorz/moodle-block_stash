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
 * Item drops.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$dropid = optional_param('dropid', 0, PARAM_INT);
$itemid = optional_param('itemid', 0, PARAM_INT);
$manager = \block_stash\manager::get($courseid);

$manager->require_manage();
require_login($manager->get_courseid());

$context = $manager->get_context();
$url = new moodle_url('/blocks/stash/drop.php', ['courseid' => $manager->get_courseid(), 'dropid' => $dropid]);
$listurl = new moodle_url('/blocks/stash/drops.php', ['courseid' => $manager->get_courseid()]);

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title('Item drop');
$PAGE->set_heading('Item drop');
$PAGE->set_url($url);

$drop = $dropid ? $manager->get_drop($dropid) : null;
$item = $itemid ? $manager->get_item($itemid) : ($drop ? $manager->get_item($drop->get_itemid()) : null);

$form = new \block_stash\form\drop($url->out(false), ['persistent' => $drop, 'item' => $item, 'manager' => $manager]);
if ($data = $form->get_data()) {
    $drop = $manager->create_or_update_drop($data);
    $listurl->param('dropid', $drop->get_id());
    redirect($listurl);

} else if ($form->is_cancelled()) {
    redirect($listurl);
}

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();

if ($drop) {
    echo $OUTPUT->heading(get_string('editdrop', 'block_stash', $drop->get_name()));
} else {
    echo $OUTPUT->heading(get_string('addnewdrop', 'block_stash'));
}

$form->display();

echo $OUTPUT->footer();
