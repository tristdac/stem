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
 * Generator.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \local_mootivated\school;

/**
 * Generator class.
 *
 * @package    local_mootivated
 * @copyright  2017 mootivated Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_generator extends component_generator_base {

    /**
     * Create a log entry.
     *
     * @param school $school The school.
     * @param int $userid The user.
     * @param array $event The event data.
     * @return void
     */
    public function create_log($school, $userid, array $event) {
        global $DB;
        $record = [
            'schoolid' => $school->get_id(),
            'userid' => $userid
        ] + $event;
        $DB->insert_record('local_mootivated_log', (object) $record);
    }

    /**
     * Create a school.
     *
     * @param array $settings The settings.
     * @return school
     */
    public function create_school(array $settings = []) {
        $school = new school(null, $settings);
        $school->save();
        return $school;
    }

}
