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
 * Helper.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use context_system;
use core_plugin_manager;
use moodle_url;

/**
 * Helper.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /**
     * Get alternate snippet module, and the warning if not installed.
     *
     * @param context $context The context.
     * @return array with string module, and string warning.
     */
    public static function get_alternate_amd_snippet_maker($context) {
        global $DB;

        // Check availability of the filter.
        $pluginmanager = core_plugin_manager::instance();
        $filters = $pluginmanager->get_plugins_of_type('filter');
        $hasfilter = array_key_exists('stash', $filters);

        // TODO: When MDL-55663 lands everywhere we should use the core function.
        // $enabledfilters = $pluginmanager->get_enabled_plugins('filter');
        // $hasfilterenabled = array_key_exists('stash', $enabledfilters);
        $hasfilterenabled = $DB->record_exists_select('filter_active', 'filter = ? AND contextid = ? AND active != ?', [
            'stash', context_system::instance()->id, TEXTFILTER_DISABLED]);

        $activefilters = filter_get_active_in_context($context);
        $isfilteractive = array_key_exists('stash', $activefilters);

        $alternatemodule = $isfilteractive ? 'filter_stash/drop-snippet-maker' : null;

        $a = (object) [
            'installurl' => (new moodle_url('https://moodle.org/plugins/filter_stash'))->out(),
            'enableurl' => (new moodle_url('/admin/filters.php'))->out(),
            'activeurl' => (new moodle_url('/filter/manage.php', ['contextid' => $context->id]))->out(),
        ];

        // Note, the order of the checks is important!
        $warning = null;
        $release = null;
        if ($isfilteractive) {
            // All good.
            // Record the release information to see if trading is possible (1.0.2 onwards).
            $release = $filters['stash']->release;
        } else if (!$hasfilter) {
            // It is not installed.
            $warning = get_string('filterstashnotinstalled', 'block_stash', $a);
        } else if (!$hasfilterenabled) {
            // It is globally disabled, it cannot be overriden in other contexts.
            $warning = get_string('filterstashnotenabled', 'block_stash', $a);
        } else if (!$isfilteractive) {
            // It is not enabled in the course.
            $warning = get_string('filterstashnotactive', 'block_stash', $a);
        }

        return [$alternatemodule, $warning, $release];
    }

}
