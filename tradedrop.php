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
 * Trade drops.
 *
 * @package    block_stash
 * @copyright  2017 Adrian Greeve - adriangreeve.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

$id = optional_param('id', 0, PARAM_INT);
if (!$id) {
    $courseid = required_param('courseid', PARAM_INT);
} else {
    $courseid = \block_stash\manager::get_courseid_by_tradedropid($id);
}
$tradeid = optional_param('tradeid', 0, PARAM_INT);
$returntype = optional_param('returntype', null, PARAM_ALPHA);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$url = new moodle_url('/blocks/stash/tradedrop.php', ['courseid' => $manager->get_courseid(), 'id' => $id]);
if (!empty($returntype)) {
    $url->param('returntype', $returntype);
}
$tradedrop = $id ? $manager->get_tradedrop($id) : null;
$pagetitle = $tradedrop ? get_string('edittradedrop', 'block_stash', $tradedrop->get_name()) : get_string('addnewdrop', 'block_stash');
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_tradedrop($url, $manager, $tradedrop, $pagetitle, $returntype);

$trade = $tradeid ? $manager->get_trade($tradeid) : ($tradedrop ? $manager->get_trade($tradedrop->get_tradeid()) : null);
$form = new \block_stash\form\tradedrop($url->out(false), ['persistent' => $tradedrop, 'trade' => $trade, 'manager' => $manager]);
if ($data = $form->get_data()) {

    $saveandnext = !empty($data->saveandnext);
    unset($data->saveandnext);

    $tradedrop = $manager->create_or_update_tradedrop($data);

    if ($saveandnext) {
        $dropsnippeturl = new moodle_url('/blocks/stash/drop_snippet.php', ['dropid' => $tradedrop->get_id()]);
        if (!empty($returntype)) {
            $dropsnippeturl->param('returntype', $returntype);
        }
        redirect($dropsnippeturl);
    }

    $returnurl->param('dropid', $tradedrop->get_id());
    redirect($returnurl);

} else if ($form->is_cancelled()) {
    redirect($returnurl);
}

$renderer = $PAGE->get_renderer('block_stash');
echo $OUTPUT->header();

echo $OUTPUT->heading($title, 2);


echo $renderer->navigation($manager, $returntype == 'tradedrops' ? 'tradedrops' : 'trade');
if (!empty($subtitle)) {
    echo $OUTPUT->heading($subtitle . $OUTPUT->help_icon('tradedrops', 'block_stash'), 3);
}

if (empty($tradedrop)) {
    echo $renderer->tradedrop_whats_that();
}

$form->display();

echo $OUTPUT->footer();
