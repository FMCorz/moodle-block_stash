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
 * Trade item edit page.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$tradeid = required_param('tradeid', PARAM_INT);
$id = optional_param('id', 0, PARAM_INT);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$context = context_course::instance($courseid);
$url = new moodle_url('/blocks/stash/tradeitem.php', array('courseid' => $courseid, 'id' => $id));

$tradeitem = $id ? $manager->get_trade_item($id) : null;
$tradeitemname = '';
if ($tradeitem) {
    $item = $manager->get_item($tradeitem->get_itemid());
    $tradeitemname = format_string($item->get_name(), true, ['context' => $context]);
}

$pagetitle = $tradeitem ? get_string('edittradeitem', 'block_stash', $tradeitemname) : get_string('addtradeitem', 'block_stash');
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_trade_item($url, $manager, $tradeitemname, $pagetitle);

$customdata = [
    'persistent' => $tradeitem,
    'manager' => $manager,
    'tradeid' => $tradeid
];

$renderer = $PAGE->get_renderer('block_stash');
$form = new \block_stash\form\tradeitem($url->out(false), $customdata);


if ($data = $form->get_data()) {

    $manager->create_or_update_tradeitem($data);
    redirect($returnurl);

} else if ($form->is_cancelled()) {
    redirect($returnurl);
}

echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo $renderer->navigation($manager, 'trade');
if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle, 3);
}
$form->display();

echo $OUTPUT->footer();
