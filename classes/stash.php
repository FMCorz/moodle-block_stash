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
     * Return whether there are items in this stash.
     *
     * @return bool
     */
    public function has_items() {
        return item::record_exists_select('stashid = ?', [$this->get_id()]);
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

    /**
     * Check if a course has an item.
     *
     * @param int $courseid Course ID.
     * @param int $itemid Item ID.
     * @return bool
     */
    public static function course_has_item($courseid, $itemid) {
        global $DB;
        $sql = "SELECT 'x'
                  FROM {" . item::TABLE . "} i
                  JOIN {" . self::TABLE . "} s
                    ON s.id = i.stashid
                 WHERE s.courseid = ?
                   AND i.id = ?";
        return $DB->record_exists_sql($sql, [$courseid, $itemid]);
    }

    /**
     * Return a stash by drop ID.
     *
     * @param  int $dropid The drop ID.
     * @return stash
     */
    public static function get_by_dropid($dropid) {
        global $DB;

        $sql = "SELECT s.*
                  FROM {" . drop::TABLE . "} d
                  JOIN {" . item::TABLE . "} i
                    ON i.id = d.itemid
                  JOIN {" . self::TABLE . "} s
                    ON s.id = i.stashid
                 WHERE d.id = ?";
        $record = $DB->get_record_sql($sql, [$dropid], MUST_EXIST);
        $stash = new static(null, $record);

        return $stash;
    }

    /**
     * Return a stash by item ID.
     *
     * @param  int $itemid The item ID.
     * @return stash
     */
    public static function get_by_itemid($itemid) {
        global $DB;

        $sql = "SELECT s.*
                  FROM {" . item::TABLE . "} i
                  JOIN {" . self::TABLE . "} s
                    ON s.id = i.stashid
                 WHERE i.id = ?";
        $record = $DB->get_record_sql($sql, [$itemid], MUST_EXIST);
        $stash = new static(null, $record);

        return $stash;
    }

    /**
     * Return a stash by item ID.
     *
     * @param  int $itemid The item ID.
     * @return stash
     */
    public static function get_by_tradeid($tradeid) {
        global $DB;

        $sql = "SELECT s.*
                  FROM {" . trade::TABLE . "} t
                  JOIN {" . self::TABLE . "} s
                    ON s.id = t.stashid
                 WHERE t.id = ?";
        $record = $DB->get_record_sql($sql, [$tradeid], MUST_EXIST);
        $stash = new static(null, $record);

        return $stash;
    }

}
