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

$courseid = required_param('courseid', PARAM_INT);

$context = context_course::instance($courseid);

require_login($courseid);
$url = new moodle_url('/blocks/stash/inventory.php', array('courseid' => $courseid));

$PAGE->set_context($context);
$PAGE->set_pagelayout('course');
$PAGE->set_title('Inventory');
$PAGE->set_heading('Inventory');
$PAGE->set_url($url);

echo $OUTPUT->header();
echo $OUTPUT->heading('Inventory');

$renderer = $PAGE->get_renderer('block_stash');
// Might need a better check for this.
if (has_capability('block/stash:addinstance', $context)) {
    $page = new \block_stash\output\inventory_page($courseid);
    // Show inventory for teachers (Maybe students as well).
    echo $renderer->render_inventory_page($page);
} else {
    $page = new \block_stash\output\user_inventory_page($courseid, $USER->id);
    echo $renderer->render_user_inventory($page);
}

echo $OUTPUT->footer();
