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
 * @package   local_mootivated
 * @copyright 2016 Mootivation Technologies Corp.
 * @author    Mootivation Technologies Corp.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mootivated;
defined('MOODLE_INTERNAL') || die();

// Conditionally include completion lib.
if (!empty($CFG->enablecompletion)) {
    require_once($CFG->libdir . '/completionlib.php');
}

use completion_info;
use context_system;
use course_modinfo;

/**
 * Mootivated helper class.
 *
 * @package    local_mootivated
 * @copyright  2016 Mootivation Technologies
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    /** Role name. */
    const ROLE_SHORTNAME = 'mootivateduser';

    /** @var ischool_resolver Used to resolve a school. */
    protected static $schoolrevolver = null;

    /**
     * Create the mootivated role.
     *
     * @return void
     */
    public static function create_mootivated_role() {
        global $DB;

        $contextid = context_system::instance()->id;
        $roleid = create_role(get_string('mootivatedrole', 'local_mootivated'), static::ROLE_SHORTNAME,
            get_string('mootivatedroledesc', 'local_mootivated'));

        set_role_contextlevels($roleid, [CONTEXT_SYSTEM]);
        assign_capability('moodle/user:viewdetails', CAP_ALLOW, $roleid, $contextid, true);
        assign_capability('webservice/rest:use', CAP_ALLOW, $roleid, $contextid, true);
        assign_capability('moodle/webservice:createtoken', CAP_ALLOW, $roleid, $contextid, true);

        $managerroleid = $DB->get_field('role', 'id', ['shortname' => 'manager'], IGNORE_MISSING);
        if ($managerroleid) {
            allow_override($managerroleid, $roleid);
            allow_assign($managerroleid, $roleid);
            allow_switch($managerroleid, $roleid);
        }
    }

    /**
     * Return whether the mootivated role exists.
     *
     * @return bool
     */
    public static function mootivated_role_exists() {
        global $DB;
        return $DB->record_exists('role', array('shortname' => static::ROLE_SHORTNAME));
    }

    /**
     * Get the mootivated role.
     *
     * @return stdClass
     */
    public static function get_mootivated_role() {
        global $DB;
        return $DB->get_record('role', ['shortname' => static::ROLE_SHORTNAME], '*', MUST_EXIST);
    }

    /**
     * Return whether webservices are enabled.
     *
     * @return bool
     */
    public static function webservices_enabled() {
        global $CFG;
        return !empty($CFG->enablewebservices);
    }

    /**
     * Enable webservices.
     *
     * @return void
     */
    public static function enable_webservices() {
        set_config('enablewebservices', 1);
    }

    /**
     * Return whether REST is enabled.
     *
     * @return bool
     */
    public static function rest_enabled() {
        global $CFG;
        $protocols = !empty($CFG->webserviceprotocols) ? explode(',', $CFG->webserviceprotocols) : [];
        return in_array('rest', $protocols);
    }

    /**
     * Enable the REST protocol.
     *
     * @return void
     */
    public static function enable_rest() {
        global $CFG;
        $protocols = !empty($CFG->webserviceprotocols) ? explode(',', $CFG->webserviceprotocols) : [];
        $protocols[] = 'rest';
        $protocols = array_unique($protocols);
        set_config('webserviceprotocols', implode(',', $protocols));
    }

    /**
     * Quick set-up.
     *
     * Enables webservices, rest and creates the mootivated role.
     *
     * @return void
     */
    public static function quick_setup() {
        if (!static::webservices_enabled()) {
            static::enable_webservices();
        }
        if (!static::rest_enabled()) {
            static::enable_rest();
        }
        if (!static::mootivated_role_exists()) {
            static::create_mootivated_role();
        }
    }

    /**
     * Delete old log entries.
     *
     * @param int $epoch Delete everything before that timestamp.
     * @return void
     */
    public static function delete_logs_older_than($epoch) {
        global $DB;
        $DB->delete_records_select('local_mootivated_log', 'timecreated < :timecreated', ['timecreated' => $epoch]);
    }

    /**
     * Observe the events, and dispatch them if necessary.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    public static function observer(\core\event\base $event) {
        global $CFG;

        static $allowedcontexts = array(CONTEXT_COURSE, CONTEXT_MODULE);

        $linter = null;
        if ($event->component === 'local_mootivated') {
            // Skip own events.
            $linter = 'happy';
        } else if (!$event->userid || isguestuser($event->userid) || is_siteadmin($event->userid)) {
            // Skip non-logged in users and guests.
            $linter = 'happy';
        } else if ($event->anonymous) {
            // Skip all the events marked as anonymous.
            $linter = 'happy';
        } else if (!in_array($event->contextlevel, $allowedcontexts)) {
            // Ignore events that are not in the right context.
            $linter = 'happy';
        } else if ($event->edulevel !== \core\event\base::LEVEL_PARTICIPATING) {
            // Ignore events that are not participating.
            $linter = 'happy';
        } else {
            // Keep the event, and proceed.
            static::handle_event($event);
        }
    }

    /**
     * Handle an event.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    protected static function handle_event(\core\event\base $event) {
        global $CFG;

        // Don't use completion_info::is_enabled_for_site() because we only include the library when completion is enabled.
        // We also skip all non-module events as we current are only being conditional on activities.
        if ($CFG->enablecompletion && $event->contextlevel == CONTEXT_MODULE) {

            // Try to guess the user we need capture for.
            $userid = $event->userid;
            if ($event instanceof \core\event\course_module_completion_updated) {
                $userid = $event->relateduserid;
            }

            // Check their school.
            $school = self::get_school_resolver()->get_by_member($userid);
            if (!$school || !$school->is_setup()) {
                // No school, no chocolate.
                return;
            }

            // When the reward method is completion, then event, check if completion is enabled in module.
            if ($school->is_reward_method_completion_else_event()) {

                $courseinfo = course_modinfo::instance($event->courseid);
                $cminfo = $courseinfo->get_cm($event->get_context()->instanceid);
                $completioninfo = new completion_info($courseinfo->get_course());

                if ($completioninfo->is_enabled($cminfo)) {
                    static::reward_for_completion($event);
                    return;
                }
            }
        }

        static::reward_for_event($event);
    }

    /**
     * Reward a user for completion.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    protected static function reward_for_completion(\core\event\base $event) {
        // We only care about one event at this point.
        if ($event instanceof \core\event\course_module_completion_updated) {
            $data = $event->get_record_snapshot('course_modules_completion', $event->objectid);
            if ($data->completionstate == COMPLETION_COMPLETE
                    || $data->completionstate == COMPLETION_COMPLETE_PASS) {

                $userid = $event->relateduserid;
                $courseid = $event->courseid;
                $cmid = $event->get_context()->instanceid;

                $school = self::get_school_resolver()->get_by_member($userid);
                if ($school->was_user_rewarded_for_completion($userid, $courseid, $cmid)) {
                    return;
                }

                $modinfo = course_modinfo::instance($courseid);
                $cminfo = $modinfo->get_cm($cmid);
                $calculator = $school->get_completion_points_calculator_by_mod();
                $coins = (int) $calculator->get_for_module($cminfo->modname);

                $school->capture_event($userid, $event, $coins);
                $school->log_user_was_rewarded_for_completion($userid, $courseid, $cmid, $data->completionstate);
            }
        }
    }

    /**
     * Reward a user by event.
     *
     * @param \core\event\base $event The event.
     * @return void
     */
    protected static function reward_for_event(\core\event\base $event) {
        $coins = 0;
        $userid = $event->userid;

        static $ignored = [
            '\\core\\event\\competency_user_competency_review_request_cancelled' => true,
            '\\core\\event\\courses_searched' => true,
            '\\core\\event\\course_viewed' => true,
            '\\mod_glossary\\event\\entry_disapproved' => true,
            '\\mod_lesson\\event\\lesson_restarted' => true,
            '\\mod_lesson\\event\\lesson_resumed' => true,
            '\\mod_quiz\\event\\attempt_abandoned' => true,
            '\\mod_quiz\\event\\attempt_becameoverdue' => true,

            // Redudant events.
            '\\mod_book\\event\\course_module_viewed' => true,
            '\\mod_forum\\event\\discussion_subscription_created' => true,
            '\\mod_forum\\event\\subscription_created' => true,
        ];

        if ($event->crud === 'd') {
            $coins = 0;

        } else if (array_key_exists($event->eventname, $ignored)) {
            $coins = 0;

        } else if (strpos($event->eventname, 'assessable_submitted') !== false
                || strpos($event->eventname, 'assessable_uploaded') !== false) {
            // Loose redundancy check.
            $coins = 0;

        } else if ($event->crud === 'c') {
            $coins = 3;

        } else if ($event->crud === 'r') {
            $coins = 1;

        } else if ($event->crud === 'u') {
            $coins = 1;
        }

        if ($coins > 0) {
            static::add_coins_for_event($event->userid, $coins, $event);
        }
    }

    /**
     * Add coins for an event.
     *
     * @param int $userid The user ID.
     * @param int $coins The number of coins.
     * @param \core\event\base $event The event.
     */
    private static function add_coins_for_event($userid, $coins, \core\event\base $event) {
        $school = self::get_school_resolver()->get_by_member($userid);
        if (!$school) {
            // The user is not part of any school.
            return;
        }

        if (!$school->is_setup()) {
            // The school is not yet set-up.
            return;
        }

        if ($school->has_exceeded_threshold($userid, $event)) {
            // The user has exceeded the threshold, no coins for them!
            return;
        }

        $school->capture_event($userid, $event, $coins);
    }

    /**
     * Get the school resolver.
     *
     * @return ischool_resolver
     */
    public static function get_school_resolver() {
        if (!self::$schoolrevolver) {
            self::$schoolrevolver = new school_resolver();
        }
        return self::$schoolrevolver;
    }

    /**
     * Set the school resolver.
     *
     * @param ischool_resolver $resolver The resolver.
     */
    public static function set_school_resolver(ischool_resolver $resolver) {
        self::$schoolrevolver = $resolver;
    }

}
