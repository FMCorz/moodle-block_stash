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
$dropid = optional_param('dropid', null, PARAM_INT);
$action = optional_param('action', null, PARAM_ALPHA);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_manage();

$context = $manager->get_context();
$url = new moodle_url('/blocks/stash/drops.php', ['courseid' => $courseid]);
$addurl = new moodle_url('/blocks/stash/drop.php', ['courseid' => $manager->get_courseid()]);

$strdrops = get_string('drops', 'block_stash');
$drop = $dropid ? $manager->get_drop($dropid) : null;
$item = $drop ? $manager->get_item($drop->get_itemid()) : null;

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title($strdrops);
$PAGE->set_heading($strdrops);
$PAGE->set_url($url);
$PAGE->add_body_class('block-stash-drops-page');

switch ($action) {
    case 'delete':
        require_sesskey();
        if (!$drop) {
            throw new coding_exception('Unknown drop.');
        }
        $manager->delete_drop($drop);
        redirect($url, get_string('thedrophasbeendeleted', 'block_stash', $drop->get_name()));
        break;
}

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();

if ($drop && (empty($action) || $action === 'snippet')) {
    echo $OUTPUT->heading(get_string('dropa', 'block_stash', $drop->get_name()));
    echo $renderer->drop_snippet_ui($drop, $item, $context);
}

$strlist = get_string('dropslist', 'block_stash');
$addbtn = $OUTPUT->single_button($addurl, get_string('addnewdrop', 'block_stash'), 'get');
$heading = $strlist . $addbtn;
echo $OUTPUT->heading($heading);

$table = new \block_stash\output\drops_table('dropstable', $manager, $renderer);
$table->define_baseurl($url);
echo $table->out(50, false);

echo $OUTPUT->footer();
