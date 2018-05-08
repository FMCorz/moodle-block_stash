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
 * External API.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

require_once("$CFG->libdir/externallib.php");
require_once("$CFG->libdir/grade/grade_scale.php");

use context_course;
use context_user;
use coding_exception;
use external_api;
use external_function_parameters;
use external_value;
use external_format_value;
use external_single_structure;
use external_multiple_structure;
use stdClass;

use block_stash\external\item_exporter;
use block_stash\manager;
use block_stash\external\user_item_summary_exporter;
use block_stash\external\trade_items_exporter;
use block_stash\external\trade_summary_exporter;
use block_stash\external\items_exporter;

/**
 * External API class.
 *
 * @package    core_competency
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class external extends external_api {

    /**
     * External function parameter structure.
     * @return external_function_parameters
     */
    public static function is_drop_visible_parameters() {
        return new external_function_parameters([
            'dropid' => new external_value(PARAM_INT),
            'hashcode' => new external_value(PARAM_ALPHANUM),
        ]);
    }

    /**
     * Is allowed from ajax?
     * Only present for 2.9 compatibility.
     * @return true
     */
    public static function is_drop_visible_is_allowed_from_ajax() {
        return true;
    }

    /**
     * A drop has been found, hurray!
     *
     * @param int $dropid The drop ID.
     * @param int $hashcode The hash code of the drop.
     * @return bool
     */
    public static function is_drop_visible($dropid, $hashcode) {
        $params = self::validate_parameters(self::is_drop_visible_parameters(), compact('dropid', 'hashcode'));
        $dropid = $params['dropid'];
        $hashcode = $params['hashcode'];

        $manager = manager::get_by_dropid($dropid);
        self::validate_context($manager->get_context());

        $drop = $manager->get_drop($dropid);
        if ($drop->get_hashcode() != $hashcode) {
            throw new coding_exception('Unexpected hash code.');
        }

        return $manager->is_drop_visible($drop);
    }

    /**
     * External function return structure.
     * @return external_value
     */
    public static function is_drop_visible_returns() {
        return new external_value(PARAM_BOOL);
    }

    /**
     * External function parameter structure.
     * @return external_function_parameters
     */
    public static function pickup_drop_parameters() {
        return new external_function_parameters([
            'dropid' => new external_value(PARAM_INT),
            'hashcode' => new external_value(PARAM_ALPHANUM),
        ]);
    }

    /**
     * Is allowed from ajax?
     * Only present for 2.9 compatibility.
     * @return true
     */
    public static function pickup_drop_is_allowed_from_ajax() {
        return true;
    }

    /**
     * A drop has been found, hurray!
     *
     * @param int $dropid The drop ID.
     * @param int $hashcode The hash code of the drop.
     * @return bool
     */
    public static function pickup_drop($dropid, $hashcode) {
        global $PAGE, $USER;
        $params = self::validate_parameters(self::pickup_drop_parameters(), compact('dropid', 'hashcode'));
        $dropid = $params['dropid'];
        $hashcode = $params['hashcode'];

        $manager = manager::get_by_dropid($dropid);
        self::validate_context($manager->get_context());

        $drop = $manager->get_drop($dropid);
        if ($drop->get_hashcode() != $hashcode) {
            throw new coding_exception('Unexpected hash code.');
        }

        $manager->pickup_drop($drop);

        // TODO Do not disclose so much information to the student.
        $output = $PAGE->get_renderer('block_stash');
        $exporter = new user_item_summary_exporter([], [
            'context' => $manager->get_context(),
            'item' => $manager->get_item($drop->get_itemid()),
            'useritem' => $manager->get_user_item($USER->id, $drop->get_itemid())
        ]);
        return $exporter->export($output);
    }

    /**
     * External function return structure.
     * @return external_value
     */
    public static function pickup_drop_returns() {
        return user_item_summary_exporter::get_read_structure();
    }

    /**
     * External function parameter structure.
     * @return external_function_parameters
     */
    public static function get_item_parameters() {
        return new external_function_parameters([
            'itemid' => new external_value(PARAM_INT)
        ]);
    }

    /**
     * Is allowed from ajax?
     * Only present for 2.9 compatibility.
     * @return true
     */
    public static function get_item_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Get the item.
     *
     * @param  int $itemid The item ID.
     * @return stdClass The exported item.
     */
    public static function get_item($itemid) {
        global $USER, $PAGE;
        $params = self::validate_parameters(self::get_item_parameters(), compact('itemid'));
        $itemid = $params['itemid'];

        $manager = manager::get_by_itemid($itemid);
        self::validate_context($manager->get_context());

        if (!$manager->can_manage() && !$manager->has_ever_had($itemid, $USER->id)) {
            throw new coding_exception('Unauthorised call.');
        }

        $item = $manager->get_item($itemid);

        $output = $PAGE->get_renderer('block_stash');
        $exporter = new item_exporter($item, array('context' => $manager->get_context()));
        $record = $exporter->export($output);
        // TODO Formatting of the details should be done in the exporter.
        $record->detail = file_rewrite_pluginfile_urls($record->detail, 'pluginfile.php', $manager->get_context()->id,
                'block_stash', 'detail', $item->get_id());

        return $record;
    }

    /**
     * External function return structure.
     *
     * @return external_value
     */
    public static function get_item_returns() {
        return item_exporter::get_read_structure();
    }

    /**
     * External function parameter structure.
     *
     * @return external_function_parameters
     */
    public static function get_items_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT)
        ]);
    }

    /**
     * Is allowed from ajax?
     *
     * Only present for 2.9 compatibility.
     *
     * @return true
     */
    public static function get_items_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Get the items for this course.
     *
     * @param  int $courseid The course ID
     * @return stdClass The exported items.
     */
    public static function get_items($courseid) {
        global $USER, $PAGE;
        $params = self::validate_parameters(self::get_items_parameters(), compact('courseid'));
        $courseid = $params['courseid'];

        $manager = manager::get($courseid);
        self::validate_context($manager->get_context());

        if (!$manager->can_manage()) {
            throw new coding_exception('Unauthorised call.');
        }

        $items = $manager->get_items();

        $output = $PAGE->get_renderer('block_stash');
        $exporter = new items_exporter($items, ['context' => $manager->get_context()]);
        $record = $exporter->export($output);

        return $record;
    }

    /**
     * External function return structure.
     *
     * @return external_value
     */
    public static function get_items_returns() {
        return items_exporter::get_read_structure();
    }

    /**
     * External function parameter structure.
     *
     * @return external_function_parameters
     */
    public static function get_trade_items_parameters() {
        return new external_function_parameters([
            'tradeid' => new external_value(PARAM_INT)
        ]);
    }

    /**
     * Is allowed from ajax?
     *
     * Only present for 2.9 compatibility.
     *
     * @return true
     */
    public static function get_trade_items_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Get trade item.
     *
     * @param int $tradeid The trade ID.
     * @return array
     */
    public static function get_trade_items($tradeid) {
        global $PAGE, $USER;

        $params = self::validate_parameters(self::get_trade_items_parameters(), compact('tradeid'));
        $tradeid = $params['tradeid'];

        $manager = manager::get_by_tradeid($tradeid);
        self::validate_context($manager->get_context());

        $tradeitems = $manager->get_trade_items($tradeid);

        $records = [];
        $output = $PAGE->get_renderer('block_stash');
        foreach ($tradeitems as $tradeitem) {
            $item = $manager->get_item($tradeitem->get_itemid());
            $useritem = $manager->get_user_item($USER->id, $item->get_id());
            $exporter = new trade_items_exporter($tradeitem, array('context' => $manager->get_context(), 'item' => $item,
                    'useritem' => $useritem));
            $records[] = $exporter->export($output);
        }
        return $records;
    }

    /**
     * External function return strcture.
     *
     * @return external_value
     */
    public static function get_trade_items_returns() {
        return new external_multiple_structure(
            trade_items_exporter::get_read_structure()
        );
    }

    /**
     * External function parameter structure.
     * @return external_function_parameters
     */
    public static function complete_trade_parameters() {
        return new external_function_parameters([
            'tradeid' => new external_value(PARAM_INT),
            'hashcode' => new external_value(PARAM_ALPHANUM),
        ]);
    }

    /**
     * Is allowed from ajax?
     *
     * Only present for 2.9 compatibility.
     *
     * @return true
     */
    public static function complete_trade_is_allowed_from_ajax() {
        return true;
    }

    /**
     * Complete trade.
     *
     * @param int $tradeid The trade ID.
     * @param string $hashcode The hash code.
     * @return array
     */
    public static function complete_trade($tradeid, $hashcode) {
        global $USER, $PAGE;
        $params = self::validate_parameters(self::complete_trade_parameters(), compact('tradeid', 'hashcode'));
        $tradeid = $params['tradeid'];
        $hashcode = $params['hashcode'];

        $manager = manager::get_by_tradeid($tradeid);
        self::validate_context($manager->get_context());

        $trade = $manager->get_trade($tradeid);
        if ($trade->get_hashcode() != $hashcode) {
            throw new coding_exception('Unexpected hash code.');
        }

        $summarydata = $manager->do_trade($tradeid, $USER->id);

        // Need to take this data and turn it into items and user items.
        $removeditems = [];
        $gaineditems = [];
        if ($summarydata) {
            foreach ($summarydata['acquireditems'] as $gaineditem) {
                $gaineditems[$gaineditem->get_itemid()]->item = $manager->get_item($gaineditem->get_itemid());
                $gaineditems[$gaineditem->get_itemid()]->useritem = $manager->get_user_item($USER->id, $gaineditem->get_itemid());
            }
            foreach ($summarydata['removeditems'] as $removeditem) {
                $removeditems[$removeditem->get_itemid()]->item = $manager->get_item($removeditem->get_itemid());
                $removeditems[$removeditem->get_itemid()]->useritem = $manager->get_user_item($USER->id,
                    $removeditem->get_itemid());
            }
        }

        $exporter = new trade_summary_exporter([], ['context' => $manager->get_context(),
                                                    'gaineditems' => $gaineditems,
                                                    'removeditems' => $removeditems]);
        $output = $PAGE->get_renderer('block_stash');
        $records = $exporter->export($output);

        return $records;

    }

    /**
     * External function return value.
     *
     * @return external_value
     */
    public static function complete_trade_returns() {
        return trade_summary_exporter::get_read_structure();
    }

}
