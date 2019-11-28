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
 * Stash plugin file.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * File serving.
 *
 * @param stdClass $course The course object.
 * @param stdClass $cm The cm object.
 * @param context $context The context object.
 * @param string $filearea The file area.
 * @param array $args List of arguments.
 * @param bool $forcedownload Whether or not to force the download of the file.
 * @param array $options Array of options.
 * @return void|false
 */
function block_stash_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    global $CFG;

    $manager = block_stash\manager::get($course->id);
    $manager->require_enabled();
    $manager->require_view();

    $fs = get_file_storage();
    $file = null;

    $itemid = array_shift($args);
    $filename = array_shift($args);
    $filepath = $args ? '/' .implode('/', $args) . '/' : '/';

    if ($filearea == 'item' && $context->contextlevel == CONTEXT_COURSE) {
        $files = $fs->get_area_files($context->id, 'block_stash', 'item', $itemid, '', false);
        $file = array_pop($files);
    } else if ($filearea == 'detail' && $context->contextlevel == CONTEXT_COURSE) {
        $file = $fs->get_file($context->id, 'block_stash', 'detail', $itemid, $filepath, $filename);
    }

    if (!$file) {
        return false;
    }

    send_stored_file($file, null, 0, $forcedownload);
}

/**
 * Show the user's stash on their profile.
 *
 * @param  \core_user\output\myprofile\tree $tree          User profile tree
 * @param  stdClass                         $user          The user object
 * @param  bool                             $iscurrentuser If this is the current user.
 * @param  stdClass                         $course        Course information
 */
function block_stash_myprofile_navigation(\core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
    global $PAGE;

    if (empty($course)) {
        return;
    }

    $manager = block_stash\manager::get($course->id);

    // Show nothing if stash is not on this course.
    if (!$manager->is_enabled()) {
        return;
    }

    $page = new \block_stash\output\block_content($manager, $user->id);
    $renderer = $PAGE->get_renderer('block_stash');
    $items = $renderer->render_profile_content($page);

    $title = $manager->get_stash_title();
    $category = new core_user\output\myprofile\category('stash', $title, 'contact');
    $tree->add_category($category);
    $localnode = new core_user\output\myprofile\node('stash', 'stash', '', null, null, $items);
    $tree->add_node($localnode);

}