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
 * Block restore task.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/stash/backup/moodle2/restore_stash_stepslib.php');

use block_stash\drop;

/**
 * Block restore task class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class restore_stash_block_task extends restore_block_task {

    /**
     * Return the old course context ID.
     * @return int
     */
    public function get_old_course_contextid() {
        return $this->plan->get_info()->original_course_contextid;
    }

    /**
     * Define my settings.
     */
    protected function define_my_settings() {
    }

    /**
     * Define my steps.
     */
    protected function define_my_steps() {
        $this->add_step(new restore_stash_block_structure_step('stash', 'stash.xml'));
    }

    /**
     * File areas.
     * @return array
     */
    public function get_fileareas() {
        return array();
    }

    /**
     * Config data.
     */
    public function get_configdata_encoded_attributes() {
    }

    /**
     * Define decode contents.
     * @return array
     */
    public static function define_decode_contents() {
        return array();
    }

    /**
     * Define decode rules.
     * @return array
     */
    public static function define_decode_rules() {
        $rules = array_map(function($class) {
            return new $class();
        }, \block_stash\restore_decode_rule::get_decode_rules_classes());
        return $rules;
    }

    /**
     * Encore content links.
     * @param  string $content The content.
     * @return string
     */
    public static function encode_content_links($content) {
        foreach (\block_stash\restore_decode_rule::get_decode_rules_classes() as $class) {
            $content = $class::encode_content($content);
        }
        return $content;
    }

}
