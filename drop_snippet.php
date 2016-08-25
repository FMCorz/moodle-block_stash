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

$dropid = required_param('dropid', PARAM_INT);
$returntype = optional_param('returntype', null, PARAM_ALPHA);
$courseid = \block_stash\manager::get_courseid_by_dropid($dropid);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$url = new moodle_url('/blocks/stash/drop_snippet.php', ['dropid' => $dropid]);
if (!empty($returntype)) {
    $url->param('returntype', $returntype);
}
$drop = $dropid ? $manager->get_drop($dropid) : null;
$pagetitle = get_string('dropsnippet', 'block_stash', $drop->get_name());

list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_drop($url, $manager, $drop, $pagetitle, $returntype);
$item = $manager->get_item($drop->get_itemid());

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();

echo $OUTPUT->heading($title, 2);

echo $renderer->navigation($manager, 'items');
if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle, 3);
}

echo $renderer->drop_snippet_whatsnext();

echo $renderer->drop_snippet_ui($drop, $item, $manager->get_context());

echo $OUTPUT->single_button($returnurl, get_string('backtostart', 'block_stash'), 'get');

echo $OUTPUT->footer();
