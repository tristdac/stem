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
 * Mootivated uninstall.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Mootivated uninstall function.
 *
 * Note that we do not delete the mootivated user role as we cannot
 * risk to delete a role that is, or would, be used.
 */
function xmldb_local_mootivated_uninstall() {
    global $DB;

    // Drop tables.
    $dbman = $DB->get_manager();

    $table = new xmldb_table('local_mootivated_school');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_mootivated_log');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

    $table = new xmldb_table('local_mootivated_completion');
    if ($dbman->table_exists($table)) {
        $dbman->drop_table($table);
    }

}
