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

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$url = new moodle_url('/blocks/stash/report.php', ['courseid' => $courseid]);
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_report($url, $manager);

switch ($action) {
    case 'reset':
        require_sesskey();
        $userid = required_param('userid', PARAM_INT);
        $manager->reset_stash_of($userid);
        $user = core_user::get_user($userid);
        redirect($url, get_string('thestashofhasbeenreset', 'block_stash', fullname($user)));
        break;
}

$renderer = $PAGE->get_renderer('block_stash');
$group = groups_get_course_group($COURSE, true);

echo $OUTPUT->header();

echo $OUTPUT->heading($title);
echo $renderer->navigation($manager, 'report');

groups_print_course_menu($COURSE, $url);

if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle, 3);
}

$table = new \block_stash\output\report_table('reporttable', $manager, $renderer, $group);
$table->define_baseurl($url);
echo $table->out(10, false);

echo $OUTPUT->footer();
