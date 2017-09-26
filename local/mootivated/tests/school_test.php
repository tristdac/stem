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
 * School tests.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/mootivated/tests/fixtures/events.php');

use local_mootivated\school;
use local_mootivated\event\action1_done;
use local_mootivated\event\action2_done;

/**
 * School tests class.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_school_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test action count since.
     */
    public function test_action_count_since() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');

        $school1 = $pg->create_school();
        $school2 = $pg->create_school();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $action2a = action2_done::create([
            'context' => context_course::instance($c1->id)
        ]);
        $action2b = action2_done::create([
            'context' => context_course::instance($c2->id)
        ]);

        // School 1, user 1, mixed events.
        $pg->create_log($school1, $u1->id, ['timecreated' => 100000] + $action2a->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => 100015] + $action2b->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => 100030] + $action2b->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => 100060] + $action2a->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => time() - 10] + $action2a->get_data());

        // School 2.
        $pg->create_log($school2, $u1->id, ['timecreated' => 100000] + $action2a->get_data());
        $pg->create_log($school2, $u2->id, ['timecreated' => 100000] + $action2b->get_data());

        $this->assertEquals(5, $school1->get_action_count_since($u1->id, 0));
        $this->assertEquals(5, $school1->get_action_count_since($u1->id, 100000));
        $this->assertEquals(4, $school1->get_action_count_since($u1->id, 100010));
        $this->assertEquals(2, $school1->get_action_count_since($u1->id, 100060));
        $this->assertEquals(1, $school1->get_action_count_since($u1->id, time() - 3600));
        $this->assertEquals(0, $school1->get_action_count_since($u1->id, time() + 10));

        // Same user, other school.
        $this->assertEquals(1, $school2->get_action_count_since($u1->id, 0));

        // Other user.
        $this->assertEquals(0, $school1->get_action_count_since($u2->id, 0));

        // Other user, other school.
        $this->assertEquals(1, $school2->get_action_count_since($u2->id, 0));
    }

    /**
     * Test has done action since.
     */
    public function test_has_done_action_since() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');

        $school1 = $pg->create_school();
        $school2 = $pg->create_school();

        $c1 = $dg->create_course();
        $c2 = $dg->create_course();

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $action1a = action1_done::create([
            'context' => context_course::instance($c1->id),
            'objectid' => 2
        ]);
        $action1b = action1_done::create([
            'context' => context_course::instance($c1->id),
            'objectid' => 2,
            'relateduserid' => 3
        ]);
        $action1c = action1_done::create([
            'context' => context_course::instance($c1->id),
            'objectid' => 3,
            'relateduserid' => 3
        ]);

        $action2a = action2_done::create([
            'context' => context_course::instance($c1->id)
        ]);
        $action2b = action2_done::create([
            'context' => context_course::instance($c2->id)
        ]);

        $pg->create_log($school1, $u1->id, ['timecreated' => 10] + $action1a->get_data());
        $pg->create_log($school1, $u2->id, ['timecreated' => 10] + $action1b->get_data());
        $pg->create_log($school2, $u2->id, ['timecreated' => 10] + $action2a->get_data());

        // Simple check on school 1.
        $this->assertTrue($school1->has_done_action_since($u1->id, $action1a, 10));
        $this->assertFalse($school1->has_done_action_since($u1->id, $action1b, 10));
        $this->assertFalse($school1->has_done_action_since($u1->id, $action1c, 10));
        $this->assertFalse($school1->has_done_action_since($u2->id, $action1a, 10));
        $this->assertTrue($school1->has_done_action_since($u2->id, $action1b, 10));
        $this->assertFalse($school1->has_done_action_since($u2->id, $action1c, 10));

        // School 1 doens't contain school 2.
        $this->assertFalse($school1->has_done_action_since($u1->id, $action2a, 10));
        $this->assertFalse($school1->has_done_action_since($u1->id, $action2b, 10));
        $this->assertFalse($school1->has_done_action_since($u2->id, $action2a, 10));
        $this->assertFalse($school1->has_done_action_since($u2->id, $action2b, 10));

        // Checks done since an earlier date don't match.
        $this->assertFalse($school1->has_done_action_since($u1->id, $action1a, 100));
        $this->assertFalse($school1->has_done_action_since($u2->id, $action1b, 100));

        // Checks done on the other school don't match.
        $this->assertFalse($school2->has_done_action_since($u1->id, $action1a, 10));
        $this->assertFalse($school2->has_done_action_since($u2->id, $action1b, 10));

        // Second school has one match.
        $this->assertTrue($school2->has_done_action_since($u2->id, $action2a, 10));
        $this->assertFalse($school2->has_done_action_since($u2->id, $action2b, 10));
    }

    /**
     * Test cheat guard threshold.
     */
    public function test_threshold() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');
        $school1 = $pg->create_school([
            'maxactions' => 5,
            'timeframeformaxactions' => 7200,
            'timebetweensameactions' => 3600
        ]);
        $c1 = $dg->create_course();
        $u1 = $dg->create_user();
        $u2 = $dg->create_user();

        $action1a = action1_done::create([
            'context' => context_course::instance($c1->id),
            'objectid' => 2
        ]);
        $action2a = action2_done::create([
            'context' => context_course::instance($c1->id)
        ]);

        $twohoursago = time() - 7200;
        $onehourago = time() - 3600;

        $pg->create_log($school1, $u1->id, ['timecreated' => $twohoursago + 100] + $action1a->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => $twohoursago + 200] + $action1a->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => $twohoursago + 300] + $action1a->get_data());
        $pg->create_log($school1, $u1->id, ['timecreated' => $onehourago + 100] + $action1a->get_data());
        $this->assertFalse($school1->has_exceeded_threshold($u1->id, $action2a));
        $pg->create_log($school1, $u1->id, ['timecreated' => $onehourago + 200] + $action1a->get_data());
        $this->assertTrue($school1->has_exceeded_threshold($u1->id, $action2a));

        $this->assertFalse($school1->has_exceeded_threshold($u2->id, $action1a));
        $this->assertFalse($school1->has_exceeded_threshold($u2->id, $action2a));
        $pg->create_log($school1, $u2->id, ['timecreated' => $onehourago + 300] + $action2a->get_data());
        $this->assertFalse($school1->has_exceeded_threshold($u2->id, $action1a));
        $this->assertTrue($school1->has_exceeded_threshold($u2->id, $action2a));
    }

    /**
     * Test load from member.
     */
    public function test_load_from_member() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');

        $c1 = $dg->create_cohort();
        $c2 = $dg->create_cohort();

        $s1 = $pg->create_school(['cohortid' => $c1->id]);
        $s2 = $pg->create_school(['cohortid' => $c2->id]);

        $u1 = $dg->create_user();
        $u2 = $dg->create_user();
        $u3 = $dg->create_user();
        $u4 = $dg->create_user();

        cohort_add_member($c1->id, $u1->id);
        cohort_add_member($c2->id, $u2->id);
        cohort_add_member($c1->id, $u4->id);
        cohort_add_member($c2->id, $u4->id);

        $school = school::load_from_member($u1->id);
        $this->assertEquals($s1->get_id(), $school->get_id());
        $school = school::load_from_member($u2->id);
        $this->assertEquals($s2->get_id(), $school->get_id());
        $school = school::load_from_member($u4->id);    // Get the first.
        $this->assertEquals($s1->get_id(), $school->get_id());
        $school = school::load_from_member($u3->id);
        $this->assertEquals(null, $school);
    }

    /**
     * Test school members and cohort deletion.
     */
    public function test_school_members_and_cohort_deletion() {
        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');
        $c1 = $dg->create_cohort();
        $s1 = $pg->create_school(['cohortid' => $c1->id]);
        $u1 = $dg->create_user();
        cohort_add_member($c1->id, $u1->id);

        $this->assertEquals($c1->id, $s1->get_cohort_id());
        $this->assertTrue($s1->has_member($u1->id));

        cohort_delete_cohort($c1);
        $s1->load();

        $this->assertEquals(0, $s1->get_cohort_id());
        $this->assertFalse($s1->has_member($u1->id));
    }

    /**
     * Test deleting a school.
     */
    public function test_delete_school() {
        global $DB;

        $dg = $this->getDataGenerator();
        $pg = $dg->get_plugin_generator('local_mootivated');

        $s1 = $pg->create_school();
        $s2 = $pg->create_school();

        $action2 = action2_done::create(['context' => context_system::instance()]);

        $pg->create_log($s1, 1, $action2->get_data());
        $pg->create_log($s1, 1, $action2->get_data());
        $pg->create_log($s2, 1, $action2->get_data());

        $this->assertEquals(2, $DB->count_records('local_mootivated_log', ['schoolid' => $s1->get_id()]));
        $this->assertEquals(1, $DB->count_records('local_mootivated_log', ['schoolid' => $s2->get_id()]));
        $this->assertTrue($DB->record_exists('local_mootivated_school', ['id' => $s1->get_id()]));
        $this->assertTrue($DB->record_exists('local_mootivated_school', ['id' => $s2->get_id()]));

        $s1id = $s1->get_id();
        $s1->delete();

        $this->assertEquals(0, $s1->get_id());
        $this->assertEquals(0, $DB->count_records('local_mootivated_log', ['schoolid' => $s1id]));
        $this->assertEquals(1, $DB->count_records('local_mootivated_log', ['schoolid' => $s2->get_id()]));
        $this->assertFalse($DB->record_exists('local_mootivated_school', ['id' => $s1id]));
        $this->assertTrue($DB->record_exists('local_mootivated_school', ['id' => $s2->get_id()]));
    }
}
