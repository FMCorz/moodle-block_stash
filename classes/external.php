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

use block_stash\manager;

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
     * @return external_function_paramters
     */
    public static function pickup_drop_parameters() {
        return new external_function_parameters([
            'dropid' => new external_value(PARAM_INT),
            'hashcode' => new external_value(PARAM_ALPHANUM),
        ]);
    }

    /**
     * A drop has been found, hurray!
     *
     * @param int $dropid The drop ID.
     * @param int $hashcode The hash code of the drop.
     * @return bool
     */
    public static function pickup_drop($dropid, $hashcode) {
        $params = self::validate_parameters(self::pickup_drop_parameters(), compact('dropid', 'hashcode'));
        extract($params);

        $manager = manager::get_by_dropid($dropid);
        self::validate_context($manager->get_context());
        $manager->require_pickup();

        $drop = $manager->get_drop($dropid);
        if ($drop->get_hashcode() != $hashcode) {
            throw new coding_exception('Unexpected hash code.');
        }

        // Check that the drop is available. (not already dropped, etc...).

        // TODO Implement quantity from the drop configuration.
        $manager->pickup_item($drop->get_itemid(), 1);

        return true;
    }

    /**
     * External function return structure.
     * @return external_value
     */
    public static function pickup_drop_returns() {
        return new external_value(PARAM_BOOL);
    }
}
