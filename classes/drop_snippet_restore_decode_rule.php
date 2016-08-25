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
 * Drop snippet restore decode rule.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

/**
 * Drop snippet restore decode rule class.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class drop_snippet_restore_decode_rule extends restore_decode_rule {

    /** @var array Cache. */
    protected $cache = array();

    /**
     * Constructor.
     */
    public function __construct($placeholder = 'BLOCKSTASHDROPSNIPPET') {
        parent::__construct($placeholder, '', 'block_stash_drop');
    }

    /**
     * Nasty override to get things done.
     *
     * @param string $content The content.
     * @return string
     */
    public function decode($content) {
        if (preg_match_all($this->cregexp, $content, $matches) === 0) {
            return $content;
        }

        foreach ($matches[0] as $key => $tosearch) {
            foreach ($this->mappings as $mappingkey => $mappingsource) {
                $oldid = $matches[$mappingkey][$key];
                $drop = $this->get_drop($oldid);
            }

            $content = str_replace($tosearch, $this->get_replacement($drop, $oldid), $content);
        }
        return $content;
    }

    /**
     * Encodes the content.
     *
     * @param string $content The content.
     * @return string The content.
     */
    public static function encode_content($content) {

        // Replace Javascript in Snippet.
        $search = preg_quote('new D({id: ') . '([0-9]+)' . preg_quote(', hashcode: "') . '[a-z0-9]+' . preg_quote('"})');
        $content = preg_replace('/' . $search . '/i', 'new D($@BLOCKSTASHDROPSNIPPET*$1@$)', $content);

        return $content;
    }

    /**
     * Get the replacement.
     *
     * This is not part of the standard backup API. It's purpose is to
     * generate the new content based on the drop identified, if any.
     *
     * @param drop|null $drop The drop if any.
     * @param int $oldid The old ID.
     * @return string The replacement string.
     */
    protected function get_replacement($drop, $oldid) {
        if ($drop) {
            return '{id: ' . $drop->get_id() . ', hashcode: "' . $drop->get_hashcode() . '"}';
        }
        return '{id: 0, hashcode: "WHOOPS' . $oldid . '"}';
    }

    /**
     * Get the drop by mapping ID.
     * @param int $oldid The old drop ID.
     * @return drop|false
     */
    protected function get_drop($oldid) {
        if (!isset($this->cache[$oldid])) {
            $newid = $this->get_mapping('block_stash_drop', $oldid);
            if ($newid) {
                $this->cache[$oldid] = new drop($newid);
            } else {
                $this->cache[$oldid] = false;
            }
        }
        return $this->cache[$oldid];
    }

}
