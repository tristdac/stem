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
 * Generator tests.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \local_mootivated\school;

/**
 * Generator tests class.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_generator_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test creating a school.
     */
    public function test_create_school() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');

        $school = $pg->create_school();
        $this->assertEquals($school->get_cohort_id(), 0);
        $this->assertEquals($school->get_private_key(), '');
        $this->assertEquals($school->get_send_username(), true);
        $this->assertEquals($school->get_max_actions(), 3);
        $this->assertEquals($school->get_time_frame_for_max_actions(), 600);
        $this->assertEquals($school->get_time_between_same_actions(), 3600);

        $school2 = $pg->create_school();
        $this->assertNotEquals($school, $school2);
    }

    /**
     * Test creating a school with settings.
     */
    public function test_create_school_with_settings() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');
        $school = $pg->create_school([
            'cohortid' => 123,
            'schoolid' => 'GreatSchool',
            'privatekey' => '<secret>',
            'sendusername' => true,
            'maxactions' => 100,
            'timeframeformaxactions' => 200,
            'timebetweensameactions' => 300,
        ]);

        $this->assertEquals($school->get_cohort_id(), 123);
        $this->assertEquals($school->get_private_key(), '<secret>');
        $this->assertEquals($school->get_send_username(), true);
        $this->assertEquals($school->get_max_actions(), 100);
        $this->assertEquals($school->get_time_frame_for_max_actions(), 200);
        $this->assertEquals($school->get_time_between_same_actions(), 300);
    }

    /**
     * Test creating a log.
     */
    public function test_create_log() {
        global $DB;

        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');
        $school = $pg->create_school();

        $data = [
            'userid' => 789,     // Is ignored.
            'contextid' => 147,
            'eventname' => '\\core\\something',
            'objectid' => 555,
            'relateduserid' => 987,
            'timecreated' => 31337
        ];
        $pg->create_log($school, 456, $data);

        $data['schoolid'] = $school->get_id();
        $data['userid'] = 456;
        $this->assertTrue($DB->record_exists('local_mootivated_log', $data));
    }

}
