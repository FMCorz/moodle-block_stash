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
$tradeid = optional_param('tradeid', 0, PARAM_INT);

require_login($courseid);

$manager = \block_stash\manager::get($courseid);
$manager->require_enabled();
$manager->require_manage();

$url = new moodle_url('/blocks/stash/trade.php', array('courseid' => $courseid));
list($title, $subtitle, $returnurl) = \block_stash\page_helper::setup_for_trade($url, $manager);

// Check for filter version 1.0.2 is enabled, otherwise show a message asking for it to be upgraded or installed.
list($altsnippetmaker, $warning, $release) = \block_stash\helper::get_alternate_amd_snippet_maker($manager->get_context());
$cantrade = false;
if (isset($release)) {
    $releaseinfo = explode('.', $release);
    if ((int)$releaseinfo[0] > 1) {
        $cantrade = true;
    } else if ((int)$releaseinfo[1] > 0) {
        $cantrade = true;
    } else if ((int)$releaseinfo[2] > 1) {
        $cantrade = true;
    } else {
        $a = new moodle_url('https://moodle.org/plugins/filter_stash');
        $warning = get_string('filterstashwrongversion', 'block_stash', $a->out());
    }
}

switch ($action) {
    case 'delete':
        require_sesskey();
        $trade = $manager->get_trade($tradeid);
        $manager->delete_trade($trade);
        redirect($url, get_string('thetradehasbeendeleted', 'block_stash', $trade->get_name()));
        break;
}

$renderer = $PAGE->get_renderer('block_stash');
if (!$cantrade) {
    echo $OUTPUT->header();

    echo $OUTPUT->heading($title);
    echo $renderer->navigation($manager, 'trade');
    echo $OUTPUT->notification($warning, 'warning');
    echo $OUTPUT->footer();
    exit();
}

echo $OUTPUT->header();

echo $OUTPUT->heading($title);
echo $renderer->navigation($manager, 'trade');

$addurl = new moodle_url('/blocks/stash/trade_edit.php', ['courseid' => $courseid]);
$addbtn = $OUTPUT->single_button($addurl, get_string('addtrade', 'block_stash'), 'get', ['class' => 'singlebutton heading-button']);
$heading = get_string('tradelist', 'block_stash') . $addbtn;
echo $OUTPUT->heading($heading, 3);

$table = new \block_stash\output\trades_table('tradestable', $manager, $renderer);
$table->define_baseurl($url);
echo $table->out(50, false);


$altsnippetmaker = json_encode($altsnippetmaker);
$warnings = json_encode($warning ? [$warning] : null);

$PAGE->requires->js_init_code("require([
    'jquery',
    'block_stash/trade',
    'block_stash/trade-snippet-dialogue',
], function($, Trade, Dialogue) {
    var warnings = $warnings;
    $('table.tradestable [rel=block-stash-trade]').click(function(e) {
        var node = $(e.currentTarget),
            trade = new Trade(node.data('trade')),
            dialogue = new Dialogue(trade, warnings);

        e.preventDefault();
        dialogue.show(e);
    });
});", true);

echo $OUTPUT->footer();
