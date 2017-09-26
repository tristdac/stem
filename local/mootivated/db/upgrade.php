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
 * Mootivated upgrade.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Mootivated upgrade function.
 *
 * @param int $oldversion Old version.
 * @return true
 */
function xmldb_local_mootivated_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2017040600) {

        // Define table local_mootivated_log to be created.
        $table = new xmldb_table('local_mootivated_log');

        // Adding fields to table local_mootivated_log.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('schoolid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('contextid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('eventname', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('objectid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('relateduserid', XMLDB_TYPE_INTEGER, '11', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mootivated_log.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mootivated_log.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017040600, 'local', 'mootivated');
    }

    if ($oldversion < 2017040601) {

        // Define field coins to be added to local_mootivated_log.
        $table = new xmldb_table('local_mootivated_log');
        $field = new xmldb_field('coins', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '0', 'relateduserid');

        // Conditionally launch add field coins.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017040601, 'local', 'mootivated');
    }

    if ($oldversion < 2017040602) {

        // Define index schooluser (not unique) to be added to local_mootivated_log.
        $table = new xmldb_table('local_mootivated_log');
        $index = new xmldb_index('schooluser', XMLDB_INDEX_NOTUNIQUE, array('schoolid', 'userid'));

        // Conditionally launch add index schooluser.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017040602, 'local', 'mootivated');
    }

    if ($oldversion < 2017040603) {

        // Define index all (not unique) to be added to local_mootivated_log.
        $table = new xmldb_table('local_mootivated_log');
        $index = new xmldb_index('all', XMLDB_INDEX_NOTUNIQUE, array('schoolid', 'userid', 'contextid', 'eventname',
            'objectid', 'relateduserid', 'timecreated'));

        // Conditionally launch add index all.
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017040603, 'local', 'mootivated');
    }

    if ($oldversion < 2017040604) {

        // Define table local_mootivated_school to be created.
        $table = new xmldb_table('local_mootivated_school');

        // Adding fields to table local_mootivated_school.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('cohortid', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, null);
        $table->add_field('schoolid', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('privatekey', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('sendusername', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('maxactions', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '3');
        $table->add_field('timeframeformaxactions', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '600');
        $table->add_field('timebetweensameactions', XMLDB_TYPE_INTEGER, '11', null, XMLDB_NOTNULL, null, '3600');

        // Adding keys to table local_mootivated_school.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Conditionally launch create table for local_mootivated_school.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017040604, 'local', 'mootivated');
    }

    if ($oldversion < 2017040607) {

        $keys = [
            'cohortid' => 'school_settings_%d_cohort_id',
            'schoolid' => 'school_settings_%d_school_id',
            'privatekey' => 'school_settings_%d_private_key',
            'sendusername' => 'school_settings_%d_send_username'
        ];

        // Converting admin-setting schools to entries in schools.
        $schoolcount = (int) get_config('local_mootivated', 'school_count');
        for ($i = 1; $i <= $schoolcount; $i++) {
            $record = new stdClass();

            foreach ($keys as $newkey => $key) {
                $value = get_config('local_mootivated', sprintf($key, $i));
                if ($newkey == 'cohortid' || $newkey == 'sendusername') {
                    $value = (int) $value;
                }
                $record->{$newkey} = $value;
            }

            $DB->insert_record('local_mootivated_school', $record);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017040607, 'local', 'mootivated');
    }

    if ($oldversion < 2017051900) {

        // Define field schoolid to be dropped from local_mootivated_school.
        $table = new xmldb_table('local_mootivated_school');
        $field = new xmldb_field('schoolid');

        // Conditionally launch drop field schoolid.
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017051900, 'local', 'mootivated');
    }

    if ($oldversion < 2017051901) {

        // Define field rewardmethod to be added to local_mootivated_school.
        $table = new xmldb_table('local_mootivated_school');
        $field = new xmldb_field('rewardmethod', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL,
            null, '1', 'timebetweensameactions');

        // Conditionally launch add field rewardmethod.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017051901, 'local', 'mootivated');
    }

    if ($oldversion < 2017051902) {

        // Define table local_mootivated_completion to be created.
        $table = new xmldb_table('local_mootivated_completion');

        // Adding fields to table local_mootivated_completion.
        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('schoolid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('courseid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('cmid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('state', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        // Adding keys to table local_mootivated_completion.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, array('id'));

        // Adding indexes to table local_mootivated_completion.
        $table->add_index('usercoursecm', XMLDB_INDEX_UNIQUE, array('userid', 'schoolid', 'courseid', 'cmid'));

        // Conditionally launch create table for local_mootivated_completion.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017051902, 'local', 'mootivated');
    }

    if ($oldversion < 2017081100) {

        // Define field modcompletionrules to be added to local_mootivated_school.
        $table = new xmldb_table('local_mootivated_school');
        $field = new xmldb_field('modcompletionrules', XMLDB_TYPE_TEXT, null, null, null, null, null, 'rewardmethod');

        // Conditionally launch add field modcompletionrules.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Mootivated savepoint reached.
        upgrade_plugin_savepoint(true, 2017081100, 'local', 'mootivated');
    }

    return true;
}
