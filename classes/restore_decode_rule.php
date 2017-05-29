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
 * Restore decode rule base.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/backup/util/helper/restore_decode_rule.class.php');

/**
 * Default implementation of stash decode rules.
 *
 * Mainly this allows us to trick the backup API into thinking that we're converting
 * links where in fact we're converting placeholders which we've manually placed
 * in the content.
 *
 * This also comes up with a method defining how to encode the content.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class restore_decode_rule extends \restore_decode_rule {

    /**
     * Bypass the validation.
     *
     * Because we are not convering links, we're converting how placeholders.
     *
     * @param string $linkname The link name.
     * @param string $urltemplate The URL template.
     * @param string $mappings The mapping.
     * @return array
     */
    protected function validate_params($linkname, $urltemplate, $mappings) {
        // Bypass validation.
        return ['1' => $mappings];
    }

    /**
     * Encode the content in a format that this rule can decode.
     *
     * Note that this is not part of the standard backup API.
     *
     * @param string $content The content.
     * @return string
     */
    public static function encode_content($content) {
        // Nothing.
    }

    /**
     * Classes which we must use to encode/decode content.
     *
     * Each new plugin that Stash will work with should be manually added here.
     *
     * Note that this is not part of the standard backup API.
     *
     * @return array
     */
    public static function get_decode_rules_classes() {
        $rules = [
            'block_stash\\drop_snippet_restore_decode_rule'
        ];

        if (class_exists('filter_stash\\trade_snippet_restore_decode_rule')) {
            $rules[] = 'filter_stash\trade_snippet_restore_decode_rule';
        }
        if (class_exists('filter_stash\\drop_snippet_restore_decode_rule')) {
            $rules[] = 'filter_stash\drop_snippet_restore_decode_rule';
        }

        return $rules;
    }
}
