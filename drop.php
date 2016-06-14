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

$itemid = required_param('itemid', PARAM_INT);
$dropid = optional_param('dropid', 0, PARAM_INT);
$manager = \block_stash\manager::get_by_itemid($itemid);

$manager->require_manage();
require_login($manager->get_courseid());

$context = $manager->get_context();
$url = new moodle_url('/blocks/stash/drop.php', ['itemid' => $itemid, 'dropid' => $dropid]);

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title('Item drop');
$PAGE->set_heading('Item drop');
$PAGE->set_url($url);

$item = $manager->get_item($itemid);
$drop = $dropid ? $manager->get_drop($dropid) : null;

if ($drop && $drop->get_id() != $itemid) {
    throw new coding_exception('IDs mismatch!');
}

$form = new \block_stash\form\drop($url->out(false), ['persistent' => $drop, 'item' => $item]);
if ($data = $form->get_data()) {

    $manager->create_or_update_drop($data);
    redirect($url);

} else if ($form->is_cancelled()) {
    redirect('/');
}

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($item->get_name(), true, ['context' => $context]));

$form->display();

if ($item) {
    // TODO Replace.
    echo $renderer->drop($drop, $item, $context);
}

echo $OUTPUT->footer();
