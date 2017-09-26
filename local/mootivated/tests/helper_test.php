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
 * Helper tests.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/local/mootivated/tests/fixtures/events.php');

use local_mootivated\helper;
use local_mootivated\event\action2_done;

/**
 * Helper tests class.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_helper_testcase extends advanced_testcase {

    public function setUp() {
        $this->resetAfterTest();
    }

    /**
     * Test deleting older logs.
     */
    public function test_delete_logs_older_than() {
        global $DB;

        $pg = $this->getDataGenerator()->get_plugin_generator('local_mootivated');
        $now = time();
        $s1 = $pg->create_school();

        $event = action2_done::create(['context' => context_system::instance()]);

        $pg->create_log($s1, 1, ['timecreated' => $now - 3 * DAYSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now - 2 * DAYSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now - 25 * HOURSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now - 23 * HOURSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now - 3 * HOURSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now - 2 * HOURSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now - HOURSECS] + $event->get_data());
        $pg->create_log($s1, 1, ['timecreated' => $now] + $event->get_data());

        $this->assertEquals(8, $DB->count_records('local_mootivated_log', []));
        $this->assertEquals(3, $DB->count_records_select('local_mootivated_log', 'timecreated < :t', ['t' => $now - DAYSECS]));
        helper::delete_logs_older_than($now - DAYSECS);
        $this->assertEquals(5, $DB->count_records('local_mootivated_log', []));
        $this->assertEquals(0, $DB->count_records_select('local_mootivated_log', 'timecreated < :t', ['t' => $now - DAYSECS]));
    }
}
