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
 * Block Stash upgrade.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Block Stash upgrade function.
 *
 * @param int $oldversion Old version.
 * @return true
 */
function xmldb_block_stash_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2016052301) {

        // Define table block_stash to be created.
        $table = new xmldb_table('block_stash');

        // Adding fields to table block_stash.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '12', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table block_stash.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_stash.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Stash savepoint reached.
        upgrade_block_savepoint(true, 2016052301, 'stash');
    }

    if ($oldversion < 2016052302) {

        // Define table block_stash_items to be created.
        $table = new xmldb_table('block_stash_items');

        // Adding fields to table block_stash_items.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('stashid', XMLDB_TYPE_INTEGER, '12', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_TEXT, null, null, null, null, null);

        // Adding keys to table block_stash_items.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_stash_items.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Stash savepoint reached.
        upgrade_block_savepoint(true, 2016052302, 'stash');
    }

    if ($oldversion < 2016052303) {

        // Define table block_stash_user_items to be created.
        $table = new xmldb_table('block_stash_user_items');

        // Adding fields to table block_stash_user_items.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('itemid', XMLDB_TYPE_INTEGER, '12', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '12', null, XMLDB_NOTNULL, null, null);
        $table->add_field('quantity', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        // Adding keys to table block_stash_user_items.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for block_stash_user_items.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Stash savepoint reached.
        upgrade_block_savepoint(true, 2016052303, 'stash');
    }

    if ($oldversion < 2016052304) {

        // Define field maxnumber to be added to block_stash_items.
        $table = new xmldb_table('block_stash_items');
        $field = new xmldb_field('maxnumber', XMLDB_TYPE_INTEGER, '10', null, null, null, '1', 'name');

        // Conditionally launch add field maxnumber.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Stash savepoint reached.
        upgrade_block_savepoint(true, 2016052304, 'stash');
    }

    return true;

}
