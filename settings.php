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
 * My inventory.
 *
 * @package    block_stash
 * @copyright  2016 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('id', PARAM_INT);

$context = context_course::instance($courseid);

require_login($courseid);
$url = new moodle_url('/blocks/stash/settings.php', array('id' => $courseid));

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title('Settings');
$PAGE->set_heading('Settings');
$PAGE->set_url($url);

echo $OUTPUT->header();
echo $OUTPUT->heading('Settings');


$renderer = $PAGE->get_renderer('block_stash');
$page = new \block_stash\output\settings_page();
echo $renderer->render_settings_page($page);

echo $OUTPUT->footer();