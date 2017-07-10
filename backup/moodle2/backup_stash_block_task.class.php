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
 * Block backup task.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/blocks/stash/backup/moodle2/backup_stash_stepslib.php');

/**
 * Block backup task class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class backup_stash_block_task extends backup_block_task {

    /**
     * Define settings.
     */
    protected function define_my_settings() {
    }

    /**
     * Define steps.
     */
    protected function define_my_steps() {
        $this->add_step(new backup_stash_block_structure_step('stash', 'stash.xml'));
    }

    /**
     * File areas.
     * @return array
     */
    public function get_fileareas() {
        return array();
    }

    /**
     * Config data encoded attributes.
     */
    public function get_configdata_encoded_attributes() {
    }

    /**
     * Encode content links.
     * @param $content string The content.
     * @return string
     */
    public static function encode_content_links($content) {
        // We abuse the encode_content_links because it is the only way we can transform
        // the text prior to backing it up, however there is a catch here! The class
        // responsible for applying these transformations is expecting that we will only
        // transform links, and thus skips any length that isn't 32 characters long. To
        // remedy this we recommend longer snippets, but just so you know why it would
        // not always work.
        foreach (\block_stash\restore_decode_rule::get_decode_rules_classes() as $class) {
            $content = $class::encode_content($content);
        }
        return $content;
    }
}
