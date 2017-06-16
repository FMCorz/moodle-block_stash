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
 * Trade edit page.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$courseid = required_param('courseid', PARAM_INT);
$id = optional_param('id', '0', PARAM_INT);

$additemdata = optional_param_array('add_item', [], PARAM_INT);
$lossitemdata = optional_param_array('lose_item', [], PARAM_INT);
$hashcode = optional_param('hashcode', null, PARAM_ALPHANUM);
$name = optional_param('title', null, PARAM_TEXT);
$gain = optional_param('gain', null, PARAM_TEXT);
$loss = optional_param('loss', null, PARAM_TEXT);
$stashid = optional_param('stashid', null, PARAM_INT);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$context = context_course::instance($courseid);
$url = new moodle_url('/blocks/stash/trade_edit_new.php', array('courseid' => $courseid, 'id' => $id));

$trade = $id ? $manager->get_trade($id) : null;

$tradename = $trade ? format_string($trade->get_name(), true, ['context' => $context]) : '';

if (!empty($additemdata) || !empty($lossitemdata)) {
    // Form submission?
    require_sesskey();

    $tradename = (!empty($name)) ? $name : get_string('tradename', 'block_stash');

    $data = new stdClass();
    $data->id = $id;
    $data->name = $tradename;
    $data->gaintitle = (!empty($gain)) ? $gain : get_string('gain', 'block_stash');
    $data->losstitle = (!empty($loss)) ? $loss : get_string('loss', 'block_stash');
    $data->hashcode = $hashcode;
    $data->stashid = $stashid;

    $trade = $manager->create_or_update_trade($data);

    $tradeitems = $manager->get_trade_items($trade->get_id());
    if (!empty($tradeitems)) {
        foreach ($tradeitems as $tradeitem) {
            $manager->delete_trade_item($tradeitem);
        }
    }

    foreach ($additemdata as $itemid => $quantity) {
        $data = new stdClass();
        $data->tradeid = $trade->get_id();
        $data->itemid = $itemid;
        $data->quantity = ($quantity <= 1) ? 1 : $quantity;
        $data->gainloss = true;
        $manager->create_or_update_tradeitem($data);
    }

    foreach ($lossitemdata as $itemid => $quantity) {
        $data = new stdClass();
        $data->tradeid = $trade->get_id();
        $data->itemid = $itemid;
        $data->quantity = ($quantity <= 1) ? 1 : $quantity;
        $data->gainloss = false;
        $manager->create_or_update_tradeitem($data);
    }
    $PAGE->set_url($url);
    $url = new moodle_url('/blocks/stash/trade.php', ['courseid' => $courseid]);
    redirect($url, get_string('tradecreated', 'block_stash', $tradename));
}

$pagetitle = $trade ? get_string('edittrade', 'block_stash', $tradename) : get_string('addtrade', 'block_stash');
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_trade($url, $manager, $trade, $pagetitle);

$customdata = [
    'persistent' => $trade,
    'stash' => $manager->get_stash(),
    'manager' => $manager,
];

$renderer = $PAGE->get_renderer('block_stash');


echo $OUTPUT->header();
echo $OUTPUT->heading($title, 2);
echo $renderer->navigation($manager, 'trade');
if (!empty($subtitle)) {
    $subtitle = $subtitle . $OUTPUT->help_icon('tradewidget', 'block_stash');
    echo $OUTPUT->heading($subtitle, 3);
}

$tradeitemsdata = null;
if ($trade) {
    $tradeitemsdata = $manager->get_full_trade_items_data($trade->get_id());
}
$fulltrade = new \block_stash\output\fulltrade($manager->get_stash()->get_id(), $trade, $tradeitemsdata, $courseid);
echo $renderer->render_trade_form($fulltrade);

echo $OUTPUT->footer();
