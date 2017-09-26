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
 * School.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mootivated;
defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/cohort/lib.php');
require_once($CFG->libdir . '/filelib.php');

use curl;
use stdClass;
use local_mootivated\local\calculator\mod_points_calculator;
use local_mootivated\local\calculator\mod_points_calculator_stack;

/**
 * School class.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class school {

    /** Reward method using events. */
    const METHOD_EVENT = 1;
    /** Reward methos using completion, else events. */
    const METHOD_COMPLETION_ELSE_EVENT = 2;

    /** @var array The default settings. */
    protected static $defaultsettings = [
        'cohortid' => 0,
        'privatekey' => '',
        'sendusername' => true,
        'maxactions' => 10,
        'timeframeformaxactions' => 60,
        'timebetweensameactions' => 3600,
        'rewardmethod' => self::METHOD_EVENT,
        'modcompletionrules' => '',
    ];

    /** @var int The school internal ID. */
    protected $id;
    /** @var int The cohort ID. */
    protected $cohortid;
    /** @var string The private key. */
    protected $privatekey;
    /** @var bool Whether to send the username. */
    protected $sendusername;
    /** @var int Max actions. */
    protected $maxactions;
    /** @var int Time frame for max actions in seconds. */
    protected $timeframeformaxactions;
    /** @var int Time between same actions in seconds. */
    protected $timebetweensameactions;
    /** @var int Reward method. */
    protected $rewardmethod;
    /** @var string Mod completion rules. */
    protected $modcompletionrules;

    /** @var string The host to communicate with. */
    protected $host;

    /** @var string Mod completion rules. */
    protected $modcompletioncalculator;

    /**
     * Constructor.
     *
     * @param int|null $id The ID of the school.
     * @param array $settings Settings to set, or override with.
     */
    public function __construct($id, array $settings = array()) {
        $this->id = $id;
        $this->host = get_config('local_mootivated', 'server_ip');

        if ($this->id) {
            $this->load();
        } else {
            $settings = array_merge(static::get_default_settings(), $settings);
        }

        $settings = array_intersect_key($settings, static::get_default_settings());
        foreach ($settings as $setting => $value) {
            $this->{$setting} = $value;
        }
    }

    /**
     * Capture an event.
     *
     * This method has to be called once a user has been validated and should
     * be rewarded for the event triggered. No validation is done at this stage.
     *
     * @param int $userid The user ID.
     * @param \core\event\base $event The event.
     * @param int $coins The number of coins.
     * @return void
     */
    public function capture_event($userid, \core\event\base $event, $coins) {
        // Log the event.
        $this->log_event($userid, $event, $coins);

        $username = '';
        $firstname = '';
        $lastname = '';

        if ($this->get_send_username()) {
            $user = \core_user::get_user($userid, 'username, firstname, lastname');

            $username = !empty($user->username) ? $user->username : '';
            $firstname = !empty($user->firstname) ? $user->firstname : '';
            $lastname = !empty($user->lastname) ? $user->lastname : '';
        }

        // Send to server.
        $data = array(
            'plugin_id' => $this->get_remote_user_id($userid),
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'num_coins' => $coins,
            'private_key' => $this->get_private_key(),
            'reason' => $event->eventname
        );

        $result = $this->request('/coins/add', $data);

        if (strpos($result, '200') === false) {
            debugging('Server error calling /coins/add with data: ' . json_encode($data), DEBUG_DEVELOPER);
        }

        $event = \local_mootivated\event\coins_earned::create([
            'context' => $event->get_context(),
            'relateduserid' => $userid,
            'other' => [
                'amount' => $coins,
            ]
        ]);
        $event->trigger();
    }

    /**
     * Delete the school and associated data.
     *
     * @return void
     */
    public function delete() {
        global $DB;
        if (!$this->id) {
            return;
        }
        $DB->delete_records('local_mootivated_school', ['id' => $this->id]);
        $DB->delete_records('local_mootivated_log', ['schoolid' => $this->id]);
        $this->id = null;
    }

    /**
     * Count the number of actions since epoch.
     *
     * @param int $userid User ID.
     * @param int $since Epoch.
     * @return int
     */
    public function get_action_count_since($userid, $since) {
        global $DB;
        $sql = 'schoolid = :id AND userid = :userid AND timecreated >= :since';
        return $DB->count_records_select('local_mootivated_log', $sql, ['id' => $this->id, 'userid' => $userid,
            'since' => $since]);
    }

    /**
     * Get the cohort ID.
     *
     * @return int
     */
    public function get_cohort_id() {
        return (int) $this->cohortid;
    }

    /**
     * The object computing points for completing an activity.
     *
     * @return mod_points_calculator
     */
    public function get_completion_points_calculator_by_mod() {
        if (!$this->modcompletioncalculator) {
            $rules = null;
            $customcalculator = null;
            if (!empty($this->modcompletionrules)) {
                $rules = json_decode($this->modcompletionrules);
                if (is_array($rules)) {
                    $customcalculator = new mod_points_calculator($rules, null);
                }
            }

            $calculator = self::get_default_completion_points_calculator_by_mod();
            if (!empty($customcalculator)) {
                $calculator = new mod_points_calculator_stack([$customcalculator, $calculator]);
            }

            $this->modcompletioncalculator = $calculator;
        }

        return $this->modcompletioncalculator;
    }

    /**
     * Get the host.
     *
     * @return string
     */
    public function get_host() {
        return $this->host;
    }

    /**
     * Get the current ID, or 0 when unset.
     *
     * @return int
     */
    public function get_id() {
        return (int) $this->id;
    }

    /**
     * Get max actions.
     *
     * @return int
     */
    public function get_max_actions() {
        return (int) $this->maxactions;
    }

    /**
     * Get private key.
     *
     * @return string
     */
    public function get_private_key() {
        return $this->privatekey;
    }

    /**
     * Get the database-like record.
     *
     * @return stdClass
     */
    public function get_record() {
        $record = new stdClass();
        foreach (static::get_default_settings() as $setting => $value) {
            $record->{$setting} = $this->{$setting};
        }
        if ($this->id) {
            $record->id = $this->id;
        }
        return $record;
    }

    /**
     * Get the remote user ID hash.
     *
     * @param int $userid The user ID.
     * @return string
     */
    public function get_remote_user_id($userid) {
        return md5(get_site_identifier() . '_' . $userid);
    }

    /**
     * Get the reward method.
     *
     * @return int
     */
    public function get_reward_method() {
        return (int) $this->rewardmethod;
    }

    /**
     * Get whether to send the user name.
     *
     * @return bool
     */
    public function get_send_username() {
        return (bool) $this->sendusername;
    }

    /**
     * Get time between same actions.
     *
     * @return int
     */
    public function get_time_between_same_actions() {
        return (int) $this->timebetweensameactions;
    }

    /**
     * Get time frame for max actions.
     *
     * @return int
     */
    public function get_time_frame_for_max_actions() {
        return (int) $this->timeframeformaxactions;
    }

    /**
     * Retrieve the amount of coins a user has from the server.
     *
     * @param int $userid The user ID.
     * @return int
     */
    public function get_user_coins($userid) {
        $data = [
            'plugin_id' => $this->get_remote_user_id($userid),
            'private_key' => $this->get_private_key()
        ];

        $result = $this->request('/coins/get', $data);
        $json = json_decode($result);

        if ($json === false || !isset($json->coins)) {
            // Whoops, there was a problem...
            return 0;
        }

        return $json->coins;
    }

    /**
     * Check whether an action was done since epoch.
     *
     * @param int $userid The user ID.
     * @param \core\event\base $event The event.
     * @param int $since Epoch.
     * @return bool
     */
    public function has_done_action_since($userid, \core\event\base $event, $since) {
        global $DB;

        $sql = 'schoolid = :id
            AND userid = :userid
            AND contextid = :contextid
            AND eventname = :eventname
            AND timecreated >= :since';

        $params = [
            'id' => $this->id,
            'userid' => $userid,
            'contextid' => $event->contextid,
            'eventname' => $event->eventname,
            'since' => $since
        ];

        if ($event->objectid === null) {
            $sql .= ' AND objectid IS NULL';
        } else {
            $sql .= ' AND objectid = :objectid';
            $params['objectid'] = $event->objectid;
        }

        if ($event->relateduserid === null) {
            $sql .= ' AND relateduserid IS NULL';
        } else {
            $sql .= ' AND relateduserid = :relateduserid';
            $params['relateduserid'] = $event->relateduserid;
        }

        return $DB->count_records_select('local_mootivated_log', $sql, $params) > 0;
    }

    /**
     * Check whether a user has exceeded the threshold.
     *
     * @param int $userid The user ID.
     * @param \core\event\base $event The event.
     * @return bool
     */
    public function has_exceeded_threshold($userid, \core\event\base $event) {
        $now = time();
        $maxactions = $this->get_max_actions();

        $actionsdone = $this->get_action_count_since($userid, $now - $this->get_time_frame_for_max_actions());
        if ($actionsdone >= $maxactions) {
            return true;
        }

        return $this->has_done_action_since($userid, $event, $now - $this->get_time_between_same_actions());
    }

    /**
     * Check whether the school contains the user.
     *
     * @param int $userid The user ID.
     * @return bool
     */
    public function has_member($userid) {
        return $this->get_cohort_id() && cohort_is_member($this->get_cohort_id(), $userid);
    }

    /**
     * Whether the reward method is completion, else event.
     *
     * @return bool
     */
    public function is_reward_method_event() {
        return $this->get_reward_method() === self::METHOD_EVENT;
    }

    /**
     * Whether the reward method is completion, else event.
     *
     * @return bool
     */
    public function is_reward_method_completion_else_event() {
        return $this->get_reward_method() === self::METHOD_COMPLETION_ELSE_EVENT;
    }

    /**
     * Whether the school is set-up for capturing events.
     *
     * @return bool
     */
    public function is_setup() {
        return $this->get_host() && $this->get_private_key();
    }

    /**
     * Load school from database.
     *
     * @return void
     */
    public function load() {
        global $DB;
        if ($this->id) {
            $record = $DB->get_record('local_mootivated_school', ['id' => $this->id]);
            if (!$record) {
                return;
            }
            $this->set_from_record($record);
        }
    }

    /**
     * Log an event.
     *
     * @param int $userid The user.
     * @param \core\event\base $event The event.
     * @param int $coins The number of coins given.
     * @return void
     */
    public function log_event($userid, \core\event\base $event, $coins) {
        global $DB;
        $data = $event->get_data();
        $data['schoolid'] = $this->id;
        $data['userid'] = $userid;
        $data['coins'] = $coins;
        $DB->insert_record('local_mootivated_log', $data);
    }

    /**
     * Log that a user was rewarded for completion.
     *
     * @param int $userid The user ID.
     * @param int $courseid The course ID.
     * @param int $cmid The CM ID, or 0.
     * @param int $state The completion state.
     * @return void
     */
    public function log_user_was_rewarded_for_completion($userid, $courseid, $cmid, $state) {
        global $DB;
        $data = [
            'schoolid' => $this->get_id(),
            'userid' => $userid,
            'courseid' => $courseid,
            'cmid' => $cmid,
            'state' => $state,
            'timecreated' => time()
        ];
        $DB->insert_record('local_mootivated_completion', $data);
    }

    /**
     * Logs a remote user id.
     *
     * @param stdClass $user The user.
     * @param string $token The token.
     * @param string $langcode The language code.
     * @return string
     */
    public function login(stdClass $user, $token, $langcode = '') {
        $username = '';
        $firstname = '';
        $lastname = '';

        if ($this->get_send_username()) {
            $username = !empty($user->username) ? $user->username : '';
            $firstname = !empty($user->firstname) ? $user->firstname : '';
            $lastname = !empty($user->lastname) ? $user->lastname : '';
        }

        $data = array(
            'token' => $token,
            'plugin_id' => $this->get_remote_user_id($user->id),
            'username' => $username,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'private_key' => $this->get_private_key(),
            'language_code' => $langcode
        );

        return $this->request('/user/logged_in', $data);
    }

    /**
     * Sends a request to the server.
     *
     * @param string $uri The URI.
     * @param array $data The data.
     * @return result
     */
    protected function request($uri, $data) {
        $url = 'https://' . $this->get_host() . '/' . ltrim($uri, '/');
        $curl = new curl();
        $curl->setHeader('Content-Type: application/json');
        return $curl->post($url, json_encode($data));
    }

    /**
     * Create, or update, the school.
     *
     * @return void
     */
    public function save() {
        global $DB;
        $record = $this->get_record();
        if (!$this->id) {
            $this->id = $DB->insert_record('local_mootivated_school', $record);
        } else {
            $DB->update_record('local_mootivated_school', $record);
        }
    }

    /**
     * Populates the school from a database-like record.
     *
     * @param stdClass $data The data.
     */
    public function set_from_record(stdClass $data) {
        $settings = static::get_default_settings();
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $settings)) {
                continue;
            }
            $this->{$key} = $value;
        }
    }

    /**
     * Was the user already rewarded for a completion?
     *
     * We don't really need to add the school ID in here, but it doesn't hurt.
     *
     * @param int $userid The user ID.
     * @param int $courseid The course ID.
     * @param int $cmid The CM ID.
     * @return bool
     */
    public function was_user_rewarded_for_completion($userid, $courseid, $cmid = 0) {
        global $DB;
        return $DB->record_exists('local_mootivated_completion', [
            'schoolid' => $this->get_id(),
            'userid' => $userid,
            'courseid' => $courseid,
            'cmid' => $cmid
        ]);
    }

    /**
     * Get the default settings.
     *
     * @return array
     */
    public static function get_default_settings() {
        return static::$defaultsettings;
    }

    /**
     * Get the default completion points calculator.
     *
     * @return array
     */
    protected static function get_default_completion_points_calculator_by_mod() {
        return new mod_points_calculator([
            (object) ['mod' => 'quiz', 'points' => 15],
            (object) ['mod' => 'lesson', 'points' => 15],
            (object) ['mod' => 'scorm', 'points' => 15],
            (object) ['mod' => 'assign', 'points' => 15],
            (object) ['mod' => 'forum', 'points' => 15],

            (object) ['mod' => 'feedback', 'points' => 10],
            (object) ['mod' => 'questionnaire', 'points' => 10],
            (object) ['mod' => 'workshop', 'points' => 10],
            (object) ['mod' => 'glossary', 'points' => 10],
            (object) ['mod' => 'database', 'points' => 10],
            (object) ['mod' => 'journal', 'points' => 10],
            (object) ['mod' => 'hotpot', 'points' => 10],

            (object) ['mod' => 'book', 'points' => 2],
            (object) ['mod' => 'resource', 'points' => 2],
            (object) ['mod' => 'folder', 'points' => 2],
            (object) ['mod' => 'imscp', 'points' => 2],
            (object) ['mod' => 'label', 'points' => 2],
            (object) ['mod' => 'page', 'points' => 2],
            (object) ['mod' => 'url', 'points' => 2]
        ], 5);
    }

    /**
     * Get a menu of schools.
     *
     * @return array Keys are IDs, values are names.
     */
    public static function get_menu() {
        global $DB;
        $sql = 'SELECT s.id, c.name
                  FROM {local_mootivated_school} s
             LEFT JOIN {cohort} c
                    ON s.cohortid = c.id
              ORDER BY c.name';
        $records = $DB->get_records_sql($sql);
        return array_combine(array_keys($records), array_map(function($record) {
            return !empty($record->name) ? format_string($record->name) : get_string('schooln', 'local_mootivated', $record->id);
        }, $records));
    }

    /**
     * Load a school from a user ID.
     *
     * This returns the first school the user is part of.
     *
     * @param int $userid The user ID.
     * @return school
     */
    public static function load_from_member($userid) {
        global $DB;
        $sql = 'SELECT s.id
                  FROM {cohort_members} cm
                  JOIN {local_mootivated_school} s
                    ON s.cohortid = cm.cohortid
                 WHERE cm.userid = :userid
              ORDER BY cm.cohortid ASC';
        $id = $DB->get_field_sql($sql, ['userid' => $userid], IGNORE_MULTIPLE);
        return $id ? new static($id) : null;
    }

}
