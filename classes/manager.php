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
 * Stash manager.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash;
defined('MOODLE_INTERNAL') || die();

use context_course;
use context_user;

/**
 * Stash manager.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** @var array Array of singletons. */
    protected static $instances;

    /** @var context The context related to this manager. */
    protected $context;

    /** @var int Course ID. */
    protected $courseid = null;

    /** @var stash The stash object. */
    protected $stash;

    /**
     * Constructor.
     *
     * @param int $courseid The course ID.
     * @return void
     */
    protected function __construct($courseid) {
        $courseid = intval($courseid);
        $this->context = context_course::instance($courseid);
        $this->courseid = $courseid;

        $stash = stash::get_record(['courseid' => $courseid]);
        if (!$stash) {
            $stash = new stash(null, ['courseid' => $courseid]);
            $stash->create();
        }
        $this->stash = $stash;
    }

    /**
     * Create or update an item based on the data passed.
     *
     * @param stdClass $data Data to use to create or update.
     * @param int $draftitemid Draft item ID of the current user to get the image from.
     * @return item
     */
    public function create_or_update_item($data, $draftitemid) {
        globaL $USER;

        $item = new item(null, $data);
        if (!$item->get_id()) {
            $item->create();
        } else {
            $item->update();
        }

        // Rename the image to 'image.ext', in case we want to add a second one later.
        $fs = get_file_storage();
        $files = $fs->get_area_files(context_user::instance($USER->id)->id, 'user', 'draft', $draftitemid, '', false);
        $image = array_pop($files);
        if ($image) {
            $ext = strtolower(pathinfo($image->get_filename(), PATHINFO_EXTENSION));
            $image->rename('/', 'image' . ($ext ? '.' . $ext : ''));
        }

        $fileareaoptions = [];
        file_save_draft_area_files($draftitemid, $this->context->id, 'block_stash', 'item', $item->get_id(), $fileareaoptions);

        return $item;
    }

    /**
     * Get an instance of the manager.
     *
     * @param int $courseid The course ID.
     * @param bool $forcereload Force the reload of the singleton, to invalidate local cache.
     * @return manager The instance of the manager.
     */
    public static function get($courseid, $forcereload = false) {
        global $CFG;

        $courseid = intval($courseid);
        if ($forcereload || !isset(self::$instances[$courseid])) {
            self::$instances[$courseid] = new static($courseid);
        }
        return self::$instances[$courseid];
    }

    /**
     * Get the context.
     *
     * @return context
     */
    public function get_context() {
        return $this->context;
    }

    /**
     * Get the stash.
     *
     * @return stash
     */
    public function get_stash() {
        return $this->stash;
    }

    /**
     * Get an item.
     *
     * @param int $itemid The item ID.
     * @return item
     */
    public function get_item($itemid) {
        return new item($itemid);
    }

    /**
     * Get the items defined in this course.
     *
     * @return item[]
     */
    public function get_items() {
        return item::get_records(['stashid' => $this->stash->get_id()]);
    }

    /**
     * Get the item of a user.
     *
     * @param int $userid The user ID.
     * @param int $itemid The item ID.
     * @return user_item
     */
    public function get_user_item($userid, $itemid) {
        $params = ['userid' => $userid, 'itemid' => $itemid];

        $ui = user_item::get_record($params);
        if (!$ui) {
            $ui = new user_item(null, (object) $params);
            $ui->create();
        }

        return $ui;
    }

    /**
     * Is the stash enabled in the course?
     *
     * @return boolean True if enabled.
     */
    public function is_enabled() {
        // TODO Add logic.
        return true;
    }

}
