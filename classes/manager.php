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

use coding_exception;
use moodle_exception;
use context_course;
use context_user;
use stdClass;

/**
 * Stash manager.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {

    /** Capability name to acquire items. */
    const CAN_ACQUIRE_ITEMS = 'block/stash:acquireitems';
    /** Capability name to manage stash. */
    const CAN_MANAGE = 'block/stash:addinstance';
    /** Capability name to view the stash. */
    const CAN_VIEW = 'block/stash:view';

    /** @var array Array of singletons. */
    protected static $instances;

    /** @var context The context related to this manager. */
    protected $context;

    /** @var int Course ID. */
    protected $courseid = null;

    /** @var bool Whether the API should be open. */
    protected $isenabled = null;

    /** @var stash The stash object, do not refer to directly as it's lazy loaded. */
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
    }

    /**
     * Whether the user can acquire items.
     *
     * Note that admins do not automatically get this permission, because
     * managers of the stash should not automatically be granted the right
     * to acquire the items.
     *
     * @param int $userid The user ID.
     * @return void
     */
    public function can_acquire_items($userid = null) {
        return has_capability(self::CAN_ACQUIRE_ITEMS, $this->context, $userid, false);
    }

    /**
     * Whether the user can manage the plugin.
     *
     * @param int $userid The user ID.
     * @return void
     */
    public function can_manage($userid = null) {
        return has_capability(self::CAN_MANAGE, $this->context, $userid);
    }

    /**
     * Whether the user can manage the plugin.
     *
     * @param int $userid The user ID.
     * @return void
     */
    public function can_view($userid = null) {
        return has_capability(self::CAN_VIEW, $this->context, $userid) || $this->can_manage();
    }

    /**
     * Create or update an item based on the data passed.
     *
     * @param stdClass $data Data to use to create or update.
     * @param int $draftitemid Draft item ID of the current user to get the image from.
     * @return item
     */
    public function create_or_update_item($data, $draftitemid) {
        global $USER;
        $this->require_enabled();
        $this->require_manage();

        $item = new item(null, $data);
        if (!$item->get_id()) {
            $item->create();
        } else {
            $item->update();
        }

        // Rename the image to 'image.ext', in case we want to add a second one later.
        $fs = get_file_storage();
        $usercontextid = context_user::instance($USER->id)->id;
        $files = $fs->get_area_files($usercontextid, 'user', 'draft', $draftitemid, '', false);
        $image = array_pop($files);
        if ($image) {

            $ext = strtolower(pathinfo($image->get_filename(), PATHINFO_EXTENSION));
            $filename = 'image' . ($ext ? '.' . $ext : '');
            // Check that we don't already have this image saved before renaming it.
            if (!$fs->file_exists($usercontextid, 'user', 'draft', $draftitemid, '/', $filename)) {
                $image->rename('/', $filename);
            }
        }

        $fileareaoptions = ['maxfiles' => 1];
        file_save_draft_area_files($draftitemid, $this->context->id, 'block_stash', 'item', $item->get_id(), $fileareaoptions);

        return $item;
    }

    /**
     * Create or update an item drop based on the data passed.
     *
     * @param stdClass $data Data to use to create or update.
     * @return drop
     */
    public function create_or_update_drop($data) {
        $this->require_enabled();
        $this->require_manage();

        if (!$data->id) {
            $drop = new drop(null, $data);
            $drop->create();

        } else {
            $drop = new drop($data->id);
            if ($data->itemid != $drop->get_itemid()) {
                throw new coding_exception('The item ID of a drop cannot be changed.');
            }
            $drop->from_record($data);
            $drop->update();
        }
        return $drop;
    }

    /**
     * Delete a drop.
     *
     * @param drop|int $droporid The drop, or its ID.
     * @return void
     */
    public function delete_drop($droporid) {
        global $DB;
        $this->require_enabled();
        $this->require_manage();

        $drop = $droporid;
        if (!is_object($drop)) {
            $drop = $this->get_drop($droporid);
        }

        if (!$this->is_item_in_stash($drop->get_itemid())) {
            throw new coding_exception('Unexpected drop ID.');
        }

        $transaction = $DB->start_delegated_transaction();
        $DB->delete_records(drop_pickup::TABLE, ['dropid' => $drop->get_id()]);
        $DB->delete_records(drop::TABLE, ['id' => $drop->get_id()]);
        $transaction->allow_commit();
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
     * Get the manager by drop ID.
     *
     * @param int $dropid The drop ID.
     * @return manager
     */
    public static function get_by_dropid($dropid) {
        $stash = stash::get_by_dropid($dropid);
        $manager = self::get($stash->get_courseid());
        $manager->stash = $stash;
        return $manager;
    }

    /**
     * Get the manager by item ID.
     *
     * @param int $itemid The item ID.
     * @return manager
     */
    public static function get_by_itemid($itemid) {
        $stash = stash::get_by_itemid($itemid);
        $manager = self::get($stash->get_courseid());
        $manager->stash = $stash;
        return $manager;
    }

    /**
     * Get the course ID.
     *
     * @return int
     */
    public function get_courseid() {
        return $this->courseid;
    }

    /**
     * Get the course ID.
     *
     * @param int $dropid The drop ID.
     * @return int
     */
    public static function get_courseid_by_dropid($dropid) {
        return drop::get_courseid_by_id($dropid);
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
        $this->require_enabled();
        $this->require_view();

        if (!$this->stash) {
            $stash = stash::get_record(['courseid' => $this->courseid]);
            if (!$stash) {
                $stash = new stash(null, (object) ['courseid' => $this->courseid]);
                $stash->create();
            }
            $this->stash = $stash;
        }
        return $this->stash;
    }

    /**
     * Get an item.
     *
     * @param int $itemid The item ID.
     * @return item
     */
    public function get_item($itemid) {
        $this->require_enabled();
        $this->require_view();

        $item = new item($itemid);
        if ($item->get_stashid() != $this->get_stash()->get_id()) {
            throw new coding_exception('Unexpected item ID.');
        }
        return $item;
    }

    /**
     * Delete an item from everywhere.
     *
     * @param object $item
     */
    public function delete_item($item) {
        global $DB;
        $this->require_enabled();
        $this->require_manage();

        // Delete drops.
        $drops = $this->get_drops($item->get_id());
        $transaction = $DB->start_delegated_transaction();

        foreach ($drops as $drop) {
            $this->delete_drop($drop);
        }

        // Delete items from users stashes.
        $DB->delete_records(\block_stash\user_item::TABLE, ['itemid' => $item->get_id()]);
        // Delete the item.
        $DB->delete_records(\block_stash\item::TABLE, ['id' => $item->get_id()]);

        // Remove image from file storage.
        $fs = get_file_storage();
        $fs->delete_area_files($this->context->id, 'block_stash', 'item', $item->get_id());

        $transaction->allow_commit();
    }

    /**
     * Get an item drop.
     *
     * @param int $drop The drop ID.
     * @return item
     */
    public function get_drop($dropid) {
        $this->require_enabled();
        $this->require_view();

        $drop = new \block_stash\drop($dropid);
        if (!$this->is_item_in_stash($drop->get_itemid())) {
            throw new coding_exception('Unexpected drop ID.');
        }
        return $drop;
    }

    /**
     * Get the drops for an item.
     *
     * @todo Support optional itemid.
     * @param int $itemid The item ID.
     * @return drop[]
     */
    public function get_drops($itemid) {
        $this->require_enabled();
        $this->require_manage();


        if (!$this->is_item_in_stash($itemid)) {
            throw new coding_exception('Unexpected item ID.');
        }
        return drop::get_records(['itemid' => $itemid], 'name');
    }

    /**
     * Get the items defined in this course.
     *
     * @return item[]
     */
    public function get_items() {
        $this->require_enabled();
        $this->require_view();

        return item::get_records(['stashid' => $this->get_stash()->get_id()], 'name');
    }

    /**
     * Get the item of a user.
     *
     * @param int $userid The user ID.
     * @param int $itemid The item ID.
     * @return user_item
     */
    public function get_user_item($userid, $itemid) {
        global $USER;
        $this->require_enabled();

        if ($userid == $USER->id) {
            $this->require_view();
        } else {
            $this->require_manage();
        }

        if (!$this->is_item_in_stash($itemid)) {
            throw new coding_exception('Unexpected item ID.');
        }

        $params = ['userid' => $userid, 'itemid' => $itemid];
        $ui = user_item::get_record($params);
        if (!$ui) {
            $ui = new user_item(null, (object) $params);
            $ui->create();
        }

        return $ui;
    }

    /**
     * Get all the items in a user's stash.
     *
     * @param int $userid The user ID.
     * @return An array of objects containing the keys 'item', and 'user_item'.
     */
    public function get_all_user_items_in_stash($userid) {
        global $USER;
        $this->require_enabled();

        if ($userid == $USER->id) {
            $this->require_view();
        } else {
            $this->require_manage();
        }

        return user_item::get_all_in_stash($userid, $this->get_stash()->get_id());
    }

    /**
     * Return whether items are defined in this stash.
     *
     * @return bool
     */
    public function has_items() {
        return $this->get_stash()->has_items();
    }

    /**
     * Is the stash enabled in the course?
     *
     * Not yet used, but in place in case we need it later.
     *
     * @return boolean True if enabled.
     */
    public function is_enabled() {
        global $DB;
        if ($this->isenabled === null) {
            $this->isenabled = $DB->record_exists('block_instances', [
                'blockname' => 'stash',
                'parentcontextid' => $this->context->id
            ]);
        }
        return $this->isenabled;
    }

    /**
     * Is a drop visible?
     *
     * Often a drop stops being visible when it has been picked up recently,
     * or picked up up to its capacity.
     *
     * @param int $droporid The drop, or ids ID.
     * @param int $userid The user who we're checking the visibility of.
     * @return bool
     */
    public function is_drop_visible($droporid, $userid = null) {
        global $USER;

        $this->require_enabled();
        $this->require_view();

        $userid = !empty($userid) ? $userid : $USER->id;
        if ($userid == $USER->id) {
            $this->require_view();
        } else {
            $this->require_manage();
        }

        $drop = $droporid;
        if (!is_object($drop)) {
            $drop = $this->get_drop($droporid);
        }
        $dp = drop_pickup::get_relation($drop->get_id(), $userid);

        return $drop->can_pickup($dp);
    }

    /**
     * Whether the item is part of this stash.
     *
     * @param int $itemid The item ID.
     * @return bool
     */
    public function is_item_in_stash($itemid) {
        $this->require_enabled();
        $this->require_view();

        return item::is_item_in_stash($itemid, $this->get_stash()->get_id());
    }

    /**
     * Pickup a drop.
     *
     * @param drop|int $droporid The drop, or its ID.
     * @param int $userid The user pickuping the drop.
     * @return void
     */
    public function pickup_drop($droporid, $userid = null) {
        global $USER;
        $this->require_enabled();

        $userid = !empty($userid) ? $userid : $USER->id;
        if ($userid == $USER->id) {
            $this->require_acquire_items();
        } else {
            // The current user needs to be able to manage, and the target user
            // must have the permission to acquire items.
            $this->require_manage();
            $this->require_acquire_items($userid);
        }

        // Find the drop.
        $drop = $droporid;
        if (!is_object($drop)) {
            $drop = $this->get_drop($droporid);
        }

        // Check that the drop is allowed: not already dropped, etc...
        $dp = drop_pickup::get_relation($drop->get_id(), $userid);
        if (!$drop->can_pickup($dp)) {
            throw new coding_exception('The drop cannot be picked up.');
        }

        // TODO Implement quantity from the drop configuration.
        $quantity = 1;
        $this->pickup_item($drop->get_itemid(), $quantity, $userid);

        // Update the drop pickup values.
        $dp->set_pickupcount($dp->get_pickupcount() + 1);
        $dp->set_lastpickup(time());
        if (!$dp->get_id()) {
            $dp->create();
        } else {
            $dp->update();
        }
    }

    /**
     * pickup an item.
     *
     * @param int|item $itemorid The item, or its ID.
     * @param int $quantity The quantity of item being pickuped.
     * @param int $userid The user pickuping the item.
     * @return void
     */
    public function pickup_item($itemorid, $quantity = 1, $userid = null) {
        global $USER;
        $this->require_enabled();

        if ($userid == $USER->id) {
            $this->require_acquire_items();
        } else {
            // The current user needs to be able to manage, and the target user
            // must have the permission to acquire items.
            $this->require_manage();
            $this->require_acquire_items($userid);
        }

        if ($quantity < 1) {
            throw new coding_exception('Invalid quantity.');
        }

        $item = $itemorid;
        if (!is_object($item)) {
            $item = $this->get_item($itemorid);
        }

        $ui = $this->get_user_item($userid, $item->get_id());
        $currentquantity = intval($ui->get_quantity());

        // TODO Check if can have more than $quantity items.
        // TODO Create a method that automatically pushes to the database to prevent race conditions.
        $ui->set_quantity($currentquantity + $quantity);
        $ui->update();
        $event = \block_stash\event\item_acquired::create(array(
                'context' => $this->context,
                'userid' => $USER->id,
                'courseid' => $this->courseid,
                'objectid' => $item->get_id(),
                'relateduserid' => $userid,
                'other' => array('quantity' => $quantity)
            )
        );
        $event->trigger();
    }

    /**
     * Delete all information to do with this instance.
     *
     * @return void
     */
    public function delete_instance() {
        global $DB;
        $this->require_enabled();
        $this->require_manage();
        // Delete all items. This should also recursively delete all drops and user items as well.
        foreach ($this->get_items() as $item) {
            $this->delete_item($item);
        }
        // Delete the stash as well.
        $DB->delete_records(\block_stash\stash::TABLE, ['id' => $this->stash->get_id()]);
    }

    /**
     * Throws an exception when the user cannot acquire items.
     *
     * Note that admins do not automatically get this permission, because
     * managers of the stash should not automatically be granted the right
     * to acquire the items.
     *
     * @param int $userid The user ID.
     * @return void
     */
    public function require_acquire_items($userid = null) {
        require_capability(self::CAN_ACQUIRE_ITEMS, $this->context, $userid, false);
    }

    /**
     * Throws an exception when the stash is not enabled in the course.
     *
     * @return void
     */
    public function require_enabled() {
        if (!$this->is_enabled()) {
            throw new moodle_exception('stashdisabled', 'block_stash');
        }
    }

    /**
     * Throws an exception when the user cannot manage the stash.
     *
     * @param int $userid The user ID.
     * @return void
     */
    public function require_manage($userid = null) {
        require_capability(self::CAN_MANAGE, $this->context, $userid);
    }

    /**
     * Throws an exception when the user cannot manage the stash.
     *
     * @param int $userid The user ID.
     * @return void
     */
    public function require_view($userid = null) {
        if (!$this->can_view($userid)) {
            throw new required_capability_exception($this->get_context(), self::CAN_VIEW, 'nopermissions');
        }
    }

}
