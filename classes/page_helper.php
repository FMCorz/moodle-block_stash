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
 * Page helper.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Page helper class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_helper {

    public static function setup_for_drop(moodle_url $url, manager $manager, $drop = null, $subtitle = '', $returntype = null) {
        global $PAGE;

        $context = $manager->get_context();
        $heading = $context->get_context_name();

        $title = get_string('items', 'block_stash');
        if ($returntype == 'drops') {
            $title = get_string('drops', 'block_stash');
        }

        $PAGE->set_context($context);
        $PAGE->set_pagelayout('course');
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);
        $PAGE->set_url($url);

        $returnurl = new moodle_url('/blocks/stash/items.php', ['courseid' => $manager->get_courseid()]);
        if ($returntype == 'drops') {
            $returnurl = new moodle_url('/blocks/stash/drops.php', ['courseid' => $manager->get_courseid()]);
        }

        $PAGE->navbar->add(get_string('stash', 'block_stash'));
        $PAGE->navbar->add($title, $returnurl);

        if (!empty($drop)) {
            $PAGE->navbar->add($drop->get_name());  // Drops don't have URLs yet.
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            $PAGE->navbar->add($subtitle, $url);
        }

        return [$title, $subtitle, $returnurl];
    }

    public static function setup_for_item(moodle_url $url, manager $manager, $item = null, $subtitle = '') {
        global $PAGE;

        $context = $manager->get_context();
        $heading = $context->get_context_name();
        $title = get_string('items', 'block_stash');

        $PAGE->set_context($context);
        $PAGE->set_pagelayout('course');
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);
        $PAGE->set_url($url);

        $returnurl = new moodle_url('/blocks/stash/items.php', ['courseid' => $manager->get_courseid()]);

        $PAGE->navbar->add(get_string('stash', 'block_stash'));
        $PAGE->navbar->add($title, $returnurl);

        if (!empty($item)) {
            $PAGE->navbar->add($item->get_name());  // Items don't have URLs yet.
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            $PAGE->navbar->add($subtitle, $url);
        }

        return [$title, $subtitle, $returnurl];
    }

    public static function setup_for_trade(moodle_url $url, manager $manager, $trade = null, $subtitle = '') {
        global $PAGE;

        $context = $manager->get_context();
        $heading = $context->get_context_name();
        $title = get_string('trade', 'block_stash');

        $PAGE->set_context($context);
        $PAGE->set_pagelayout('course');
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);
        $PAGE->set_url($url);

        $returnurl = new moodle_url('/blocks/stash/trade.php', ['courseid' => $manager->get_courseid()]);

        $PAGE->navbar->add(get_string('stash', 'block_stash'));
        $PAGE->navbar->add($title, $returnurl);

        if (!empty($trade)) {
            $PAGE->navbar->add($trade->get_name());  // Items don't have URLs yet.
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            $PAGE->navbar->add($subtitle, $url);
        }

        return [$title, $subtitle, $returnurl];
    }

    public static function setup_for_trade_item(moodle_url $url, manager $manager, $tradename = null, $subtitle = '') {
        global $PAGE;

        $context = $manager->get_context();
        $heading = $context->get_context_name();
        $title = get_string('tradeitem', 'block_stash');

        $PAGE->set_context($context);
        $PAGE->set_pagelayout('course');
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);
        $PAGE->set_url($url);

        $returnurl = new moodle_url('/blocks/stash/trade.php', ['courseid' => $manager->get_courseid()]);

        $PAGE->navbar->add(get_string('stash', 'block_stash'));
        $PAGE->navbar->add($title, $returnurl);

        if (!empty($tradename)) {
            $PAGE->navbar->add($tradename);  // Items don't have URLs yet.
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            $PAGE->navbar->add($subtitle, $url);
        }

        return [$title, $subtitle, $returnurl];
    }

    public static function setup_for_report(moodle_url $url, manager $manager, $user = null, $subtitle = '') {
        global $PAGE;

        $context = $manager->get_context();
        $heading = $context->get_context_name();
        $title = get_string('report', 'block_stash');

        $PAGE->set_context($context);
        $PAGE->set_pagelayout('course');
        $PAGE->set_title($title);
        $PAGE->set_heading($heading);
        $PAGE->set_url($url);

        $returnurl = new moodle_url('/blocks/stash/report.php', ['courseid' => $manager->get_courseid()]);

        $PAGE->navbar->add(get_string('stash', 'block_stash'));
        $PAGE->navbar->add($title, $returnurl);

        if (!empty($user)) {
            $PAGE->navbar->add(fullname($user));
            if (!empty($subtitle)) {
                $PAGE->navbar->add($subtitle, $url);
            }
        } else if (!empty($subtitle)) {
            $PAGE->navbar->add($subtitle, $url);
        }

        return [$title, $subtitle, $returnurl];
    }

}
