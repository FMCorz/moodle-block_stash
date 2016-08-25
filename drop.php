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

$dropid = optional_param('dropid', 0, PARAM_INT);
if (!$dropid) {
    $courseid = required_param('courseid', PARAM_INT);
} else {
    $courseid = \block_stash\manager::get_courseid_by_dropid($dropid);
}
$itemid = optional_param('itemid', 0, PARAM_INT);
$returntype = optional_param('returntype', null, PARAM_ALPHA);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$url = new moodle_url('/blocks/stash/drop.php', ['courseid' => $manager->get_courseid(), 'dropid' => $dropid]);
if (!empty($returntype)) {
    $url->param('returntype', $returntype);
}
$drop = $dropid ? $manager->get_drop($dropid) : null;
$pagetitle = $drop ? get_string('editdrop', 'block_stash', $drop->get_name()) : get_string('addnewdrop', 'block_stash');
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_drop($url, $manager, $drop, $pagetitle, $returntype);

$item = $itemid ? $manager->get_item($itemid) : ($drop ? $manager->get_item($drop->get_itemid()) : null);
$form = new \block_stash\form\drop($url->out(false), ['persistent' => $drop, 'item' => $item, 'manager' => $manager]);
if ($data = $form->get_data()) {

    $saveandnext = !empty($data->saveandnext);
    unset($data->saveandnext);

    $drop = $manager->create_or_update_drop($data);

    if ($saveandnext) {
        $dropsnippeturl = new moodle_url('/blocks/stash/drop_snippet.php', ['dropid' => $drop->get_id()]);
        if (!empty($returntype)) {
            $dropsnippeturl->param('returntype', $returntype);
        }
        redirect($dropsnippeturl);
    }

    $returnurl->param('dropid', $drop->get_id());
    redirect($returnurl);

} else if ($form->is_cancelled()) {
    redirect($returnurl);
}

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();

echo $OUTPUT->heading($title, 2);

// Drops appear under 'items' by default.
echo $renderer->navigation($manager, $returntype == 'drops' ? 'drops' : 'items');
if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle . $OUTPUT->help_icon('drops', 'block_stash'), 3);
}

if (empty($drop)) {
    echo $renderer->drop_whats_that();
}

$form->display();

echo $OUTPUT->footer();
