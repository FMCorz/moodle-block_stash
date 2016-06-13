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
 * Stash model.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use lang_string;

/**
 * Stash model class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stash extends persistent {

    const TABLE = 'block_stash';

    protected static function define_properties() {
        return [
            'courseid' => [
                'type' => PARAM_INT,
            ],
            'name' => [
                'type' => PARAM_TEXT,
                'default' => null,
                'null' => NULL_ALLOWED
            ]
        ];
    }

    /**
     * Validate the course ID.
     *
     * @param string $value The course ID.
     * @return true|lang_string
     */
    protected function validate_courseid($value) {
        global $DB;

        $sql = 'courseid = :courseid AND id <> :id';
        $params = [
            'id' => $this->get('id'),
            'courseid' => $value,
        ];
        if (self::record_exists_select($sql, $params)) {
            // There must be only one record for this course.
            return new lang_string('invaliddata', 'error');

        } else if (!$DB->record_exists('course', ['id' => $value])) {
            // The course must exist.
            return new lang_string('invaliddata', 'error');
        }

        return true;
    }

}