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
 * Data provider tests.
 *
 * @package    block_stash
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
global $CFG;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use block_stash\privacy\provider;
use block_stash\drop_pickup;
use block_stash\user_item;

/**
 * Data provider testcase class.
 *
 * @package    block_stash
 * @category   test
 * @copyright  2018 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_stash_privacy_testcase extends advanced_testcase {

    public function setUp() {
        if (!class_exists('core_privacy\manager')) {
            $this->markTestSkipped('Moodle versions does not support privacy subsystem.');
        }

        $this->resetAfterTest();
        writer::reset();
        parent::setUp();
    }

    public function test_get_contexts_for_userid() {
        $dg = $this->getDataGenerator();
        $sg = $dg->get_plugin_generator('block_stash');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $s1 = $sg->create_stash(['courseid' => $c1->id]);
        $s2 = $sg->create_stash(['courseid' => $c2->id]);
        $i1a = $sg->create_item(['stash' => $s1]);
        $i1b = $sg->create_item(['stash' => $s1]);
        $i2a = $sg->create_item(['stash' => $s2]);
        $d2a = $sg->create_drop(['item' => $i2a]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u1->id]);
        $sg->create_user_item(['item' => $i1b, 'userid' => $u2->id]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u1->id]);

        $contextids = provider::get_contexts_for_userid($u1->id)->get_contextids();
        $this->assertCount(2, $contextids);
        $this->assertTrue(in_array($c1ctx->id, $contextids));
        $this->assertTrue(in_array($c2ctx->id, $contextids));

        $contextids = provider::get_contexts_for_userid($u2->id)->get_contextids();
        $this->assertCount(1, $contextids);
        $this->assertTrue(in_array($c1ctx->id, $contextids));
    }

    public function test_delete_data_for_user() {
        $dg = $this->getDataGenerator();
        $sg = $dg->get_plugin_generator('block_stash');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $s1 = $sg->create_stash(['courseid' => $c1->id]);
        $s2 = $sg->create_stash(['courseid' => $c2->id]);
        $i1a = $sg->create_item(['stash' => $s1]);
        $i1b = $sg->create_item(['stash' => $s1]);
        $d1b = $sg->create_drop(['item' => $i1b]);
        $i2a = $sg->create_item(['stash' => $s2]);
        $d2a = $sg->create_drop(['item' => $i2a]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u1->id]);
        $sg->create_user_item(['item' => $i1b, 'userid' => $u1->id]);
        $sg->create_user_item(['item' => $i2a, 'userid' => $u1->id]);
        $sg->create_drop_pickup(['drop' => $d1b, 'userid' => $u1->id]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u1->id]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u2->id]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u2->id]);

        $this->assertEquals(3, user_item::count_records(['userid' => $u1->id]));
        $this->assertEquals(2, drop_pickup::count_records(['userid' => $u1->id]));
        $this->assertEquals(1, user_item::count_records(['userid' => $u2->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u2->id]));

        provider::delete_data_for_user(new approved_contextlist($u1, 'block_stash', [$c1ctx->id]));

        $this->assertEquals(1, user_item::count_records(['userid' => $u1->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u1->id]));
        $this->assertEquals(1, user_item::count_records(['userid' => $u2->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u2->id]));

        provider::delete_data_for_user(new approved_contextlist($u2, 'block_stash', [$c1ctx->id, $c2ctx->id]));

        $this->assertEquals(1, user_item::count_records(['userid' => $u1->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u1->id]));
        $this->assertEquals(0, user_item::count_records(['userid' => $u2->id]));
        $this->assertEquals(0, drop_pickup::count_records(['userid' => $u2->id]));
    }

    public function test_delete_data_for_all_users_in_context() {
        $dg = $this->getDataGenerator();
        $sg = $dg->get_plugin_generator('block_stash');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $s1 = $sg->create_stash(['courseid' => $c1->id]);
        $s2 = $sg->create_stash(['courseid' => $c2->id]);
        $i1a = $sg->create_item(['stash' => $s1]);
        $i1b = $sg->create_item(['stash' => $s1]);
        $d1b = $sg->create_drop(['item' => $i1b]);
        $i2a = $sg->create_item(['stash' => $s2]);
        $d2a = $sg->create_drop(['item' => $i2a]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u1->id]);
        $sg->create_user_item(['item' => $i1b, 'userid' => $u1->id]);
        $sg->create_user_item(['item' => $i2a, 'userid' => $u1->id]);
        $sg->create_drop_pickup(['drop' => $d1b, 'userid' => $u1->id]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u1->id]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u2->id]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u2->id]);

        $this->assertEquals(3, user_item::count_records(['userid' => $u1->id]));
        $this->assertEquals(2, drop_pickup::count_records(['userid' => $u1->id]));
        $this->assertEquals(1, user_item::count_records(['userid' => $u2->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u2->id]));

        provider::delete_data_for_all_users_in_context($c1ctx);

        $this->assertEquals(1, user_item::count_records(['userid' => $u1->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u1->id]));
        $this->assertEquals(0, user_item::count_records(['userid' => $u2->id]));
        $this->assertEquals(1, drop_pickup::count_records(['userid' => $u2->id]));

        provider::delete_data_for_all_users_in_context($c2ctx);

        $this->assertEquals(0, user_item::count_records(['userid' => $u1->id]));
        $this->assertEquals(0, drop_pickup::count_records(['userid' => $u1->id]));
        $this->assertEquals(0, user_item::count_records(['userid' => $u2->id]));
        $this->assertEquals(0, drop_pickup::count_records(['userid' => $u2->id]));
    }

    public function test_export_data_for_user() {
        $dg = $this->getDataGenerator();
        $sg = $dg->get_plugin_generator('block_stash');

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $now = time();
        $path = [get_string('pluginname', 'block_stash')];

        $s1 = $sg->create_stash(['courseid' => $c1->id]);
        $s2 = $sg->create_stash(['courseid' => $c2->id]);
        $i1a = $sg->create_item(['stash' => $s1]);
        $d1a = $sg->create_drop(['item' => $i1a]);
        $i1b = $sg->create_item(['stash' => $s1]);
        $d1b = $sg->create_drop(['item' => $i1b]);
        $d1b2 = $sg->create_drop(['item' => $i1b]);
        $i1c = $sg->create_item(['stash' => $s1]);
        $i2a = $sg->create_item(['stash' => $s2]);
        $d2a = $sg->create_drop(['item' => $i2a]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u1->id, 'quantity' => 2]);
        $sg->create_user_item(['item' => $i1b, 'userid' => $u1->id, 'quantity' => 0]);
        $sg->create_user_item(['item' => $i2a, 'userid' => $u1->id]);
        $sg->create_drop_pickup(['drop' => $d1b, 'userid' => $u1->id, 'lastpickup' => $now - HOURSECS, 'pickupcount' => 2]);
        $sg->create_drop_pickup(['drop' => $d1b2, 'userid' => $u1->id, 'lastpickup' => $now - HOURSECS - 2, 'pickupcount' => 3]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u1->id, 'lastpickup' => $now - DAYSECS, 'pickupcount' => 23]);

        $sg->create_user_item(['item' => $i1a, 'userid' => $u2->id, 'quantity' => 3]);
        $sg->create_drop_pickup(['drop' => $d1a, 'userid' => $u2->id, 'lastpickup' => $now, 'pickupcount' => 123]);
        $sg->create_drop_pickup(['drop' => $d2a, 'userid' => $u2->id, 'lastpickup' => $now - YEARSECS, 'pickupcount' => 1]);

        $assertu1inc1 = function($data) use ($now, $i1a, $i1b, $d1b, $d1b2) {
            $this->assertCount(2, $data->items);
            $item = $data->items[0];
            $this->assertEquals($i1a->get_name(), $item['name']);
            $this->assertEquals(2, $item['owned']);
            $this->assertCount(0, $item['pickups']);
            $item = $data->items[1];
            $this->assertEquals($i1b->get_name(), $item['name']);
            $this->assertEquals(0, $item['owned']);
            $this->assertCount(2, $item['pickups']);
            $drop = $item['pickups'][0];
            $this->assertEquals($d1b->get_name(), $drop['location']);
            $this->assertEquals(2, $drop['quantity']);
            $this->assertEquals(transform::datetime($now - HOURSECS), $drop['last_pickup']);
            $drop = $item['pickups'][1];
            $this->assertEquals($d1b2->get_name(), $drop['location']);
            $this->assertEquals(3, $drop['quantity']);
            $this->assertEquals(transform::datetime($now - HOURSECS - 2), $drop['last_pickup']);
        };

        // Export u1, in course c1.
        provider::export_user_data(new approved_contextlist($u1, 'block_stash', [$c1ctx->id]));
        $data = writer::with_context($c2ctx)->get_data($path);
        $this->assertEmpty($data);
        $data = writer::with_context($c1ctx)->get_data($path);
        $assertu1inc1($data);

        // Export u1, in course c1, c2.
        writer::reset();
        provider::export_user_data(new approved_contextlist($u1, 'block_stash', [$c1ctx->id, $c2ctx->id]));
        $data = writer::with_context($c1ctx)->get_data($path);
        $assertu1inc1($data);
        $data = writer::with_context($c2ctx)->get_data($path);
        $this->assertCount(1, $data->items);
        $item = $data->items[0];
        $this->assertEquals($i2a->get_name(), $item['name']);
        $this->assertEquals(0, $item['owned']);
        $this->assertCount(1, $item['pickups']);
        $drop = $item['pickups'][0];
        $this->assertEquals($d2a->get_name(), $drop['location']);
        $this->assertEquals(23, $drop['quantity']);
        $this->assertEquals(transform::datetime($now - DAYSECS), $drop['last_pickup']);
    }

}
