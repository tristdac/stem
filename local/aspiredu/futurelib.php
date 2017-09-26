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
 * Some functions needed for future compatibility.
 *
 * @package    local_aspiredu
 * @copyright  AspirEDU
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

if (!file_exists($CFG->dirroot . '/lib/classes/user.php') and !class_exists("core_user")) {

    /**
     * User class to access user details.
     *
     * @todo       move api's from user/lib.php and depreciate old ones.
     * @package    core
     * @copyright  2013 Rajesh Taneja <rajesh@moodle.com>
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     * @since 2.6
     */
    class core_user {
        /**
         * No reply user id.
         */
        const NOREPLY_USER = -10;

        /**
         * Support user id.
         */
        const SUPPORT_USER = -20;

        /** @var stdClass keep record of noreply user */
        public static $noreplyuser = false;

        /** @var stdClass keep record of support user */
        public static $supportuser = false;

        /**
         * Return user object from db or create noreply or support user,
         * if userid matches corse_user::NOREPLY_USER or corse_user::SUPPORT_USER
         * respectively. If userid is not found, then return false.
         *
         * @param int $userid user id
         * @param string $fields A comma separated list of user fields to be returned, support and noreply user
         *                       will not be filtered by this.
         * @param int $strictness IGNORE_MISSING means compatible mode, false returned if user not found, debug message if more found;
         *                        IGNORE_MULTIPLE means return first user, ignore multiple user records found(not recommended);
         *                        MUST_EXIST means throw an exception if no user record or multiple records found.
         * @return stdClass|bool user record if found, else false.
         * @throws dml_exception if user record not found and respective $strictness is set.
         */
        public static function get_user($userid, $fields = '*', $strictness = IGNORE_MISSING) {
            global $DB;

            // If noreply user then create fake record and return.
            switch ($userid) {
                case self::NOREPLY_USER:
                    return self::get_noreply_user($strictness);
                    break;
                case self::SUPPORT_USER:
                    return self::get_support_user($strictness);
                    break;
                default:
                    return $DB->get_record('user', array('id' => $userid), $fields, $strictness);
            }
        }


        /**
         * Return user object from db based on their username.
         *
         * @param string $username The username of the user searched.
         * @param string $fields A comma separated list of user fields to be returned, support and noreply user.
         * @param int $mnethostid The id of the remote host.
         * @param int $strictness IGNORE_MISSING means compatible mode, false returned if user not found, debug message if more found;
         *                        IGNORE_MULTIPLE means return first user, ignore multiple user records found(not recommended);
         *                        MUST_EXIST means throw an exception if no user record or multiple records found.
         * @return stdClass|bool user record if found, else false.
         * @throws dml_exception if user record not found and respective $strictness is set.
         */
        public static function get_user_by_username($username, $fields = '*', $mnethostid = null, $strictness = IGNORE_MISSING) {
            global $DB, $CFG;

            // Because we use the username as the search criteria, we must also restrict our search based on mnet host.
            if (empty($mnethostid)) {
                // If empty, we restrict to local users.
                $mnethostid = $CFG->mnet_localhost_id;
            }

            return $DB->get_record('user', array('username' => $username, 'mnethostid' => $mnethostid), $fields, $strictness);
        }

        /**
         * Helper function to return dummy noreply user record.
         *
         * @return stdClass
         */
        protected static function get_dummy_user_record() {
            global $CFG;

            $dummyuser = new stdClass();
            $dummyuser->id = self::NOREPLY_USER;
            $dummyuser->email = $CFG->noreplyaddress;
            $dummyuser->firstname = get_string('noreplyname');
            $dummyuser->username = 'noreply';
            $dummyuser->lastname = '';
            $dummyuser->confirmed = 1;
            $dummyuser->suspended = 0;
            $dummyuser->deleted = 0;
            $dummyuser->picture = 0;
            $dummyuser->auth = 'manual';
            $dummyuser->firstnamephonetic = '';
            $dummyuser->lastnamephonetic = '';
            $dummyuser->middlename = '';
            $dummyuser->alternatename = '';
            $dummyuser->imagealt = '';
            return $dummyuser;
        }

        /**
         * Return noreply user record, this is currently used in messaging
         * system only for sending messages from noreply email.
         * It will return record of $CFG->noreplyuserid if set else return dummy
         * user object with hard-coded $user->emailstop = 1 so noreply can be sent to user.
         *
         * @return stdClass user record.
         */
        public static function get_noreply_user() {
            global $CFG;

            if (!empty(self::$noreplyuser)) {
                return self::$noreplyuser;
            }

            // If noreply user is set then use it, else create one.
            if (!empty($CFG->noreplyuserid)) {
                self::$noreplyuser = self::get_user($CFG->noreplyuserid);
            }

            if (empty(self::$noreplyuser)) {
                self::$noreplyuser = self::get_dummy_user_record();
                self::$noreplyuser->maildisplay = '1'; // Show to all.
            }
            self::$noreplyuser->emailstop = 1; // Force msg stop for this user.
            return self::$noreplyuser;
        }

        /**
         * Return support user record, this is currently used in messaging
         * system only for sending messages to support email.
         * $CFG->supportuserid is set then returns user record
         * $CFG->supportemail is set then return dummy record with $CFG->supportemail
         * else return admin user record with hard-coded $user->emailstop = 0, so user
         * gets support message.
         *
         * @return stdClass user record.
         */
        public static function get_support_user() {
            global $CFG;

            if (!empty(self::$supportuser)) {
                return self::$supportuser;
            }

            // If custom support user is set then use it, else if supportemail is set then use it, else use noreply.
            if (!empty($CFG->supportuserid)) {
                self::$supportuser = self::get_user($CFG->supportuserid, '*', MUST_EXIST);
            }

            // Try sending it to support email if support user is not set.
            if (empty(self::$supportuser) && !empty($CFG->supportemail)) {
                self::$supportuser = self::get_dummy_user_record();
                self::$supportuser->id = self::SUPPORT_USER;
                self::$supportuser->email = $CFG->supportemail;
                if ($CFG->supportname) {
                    self::$supportuser->firstname = $CFG->supportname;
                }
                self::$supportuser->username = 'support';
                self::$supportuser->maildisplay = '1'; // Show to all.
            }

            // Send support msg to admin user if nothing is set above.
            if (empty(self::$supportuser)) {
                self::$supportuser = get_admin();
            }

            // Unset emailstop to make sure support message is sent.
            self::$supportuser->emailstop = 0;
            return self::$supportuser;
        }

        /**
         * Reset self::$noreplyuser and self::$supportuser.
         * This is only used by phpunit, and there is no other use case for this function.
         * Please don't use it outside phpunit.
         */
        public static function reset_internal_users() {
            if (PHPUNIT_TEST) {
                self::$noreplyuser = false;
                self::$supportuser = false;
            } else {
                debugging('reset_internal_users() should not be used outside phpunit.', DEBUG_DEVELOPER);
            }
        }

        /**
         * Return true is user id is greater than self::NOREPLY_USER and
         * alternatively check db.
         *
         * @param int $userid user id.
         * @param bool $checkdb if true userid will be checked in db. By default it's false, and
         *                      userid is compared with NOREPLY_USER for performance.
         * @return bool true is real user else false.
         */
        public static function is_real_user($userid, $checkdb = false) {
            global $DB;

            if ($userid < 0) {
                return false;
            }
            if ($checkdb) {
                return $DB->record_exists('user', array('id' => $userid));
            } else {
                return true;
            }
        }
    }
}

if (!function_exists("get_all_user_name_fields")) {
    /**
     * A centralised location for the all name fields. Returns an array / sql string snippet.
     *
     * @param bool $returnsql True for an sql select field snippet.
     * @param string $tableprefix table query prefix to use in front of each field.
     * @param string $prefix prefix added to the name fields e.g. authorfirstname.
     * @param string $fieldprefix sql field prefix e.g. id AS userid.
     * @return array|string All name fields.
     */
    function get_all_user_name_fields($returnsql = false, $tableprefix = null, $prefix = null, $fieldprefix = null) {
        $alternatenames = array('firstname' => 'firstname',
                                'lastname' => 'lastname');

        // Let's add a prefix to the array of user name fields if provided.
        if ($prefix) {
            foreach ($alternatenames as $key => $altname) {
                $alternatenames[$key] = $prefix . $altname;
            }
        }

        // Create an sql field snippet if requested.
        if ($returnsql) {
            if ($tableprefix) {
                if ($fieldprefix) {
                    foreach ($alternatenames as $key => $altname) {
                        $alternatenames[$key] = $tableprefix . '.' . $altname . ' AS ' . $fieldprefix . $altname;
                    }
                } else {
                    foreach ($alternatenames as $key => $altname) {
                        $alternatenames[$key] = $tableprefix . '.' . $altname;
                    }
                }
            }
            $alternatenames = implode(',', $alternatenames);
        }
        return $alternatenames;
    }
}

if (!function_exists("username_load_fields_from_object")) {
    /**
     * Reduces lines of duplicated code for getting user name fields.
     *
     * See also {@link user_picture::unalias()}
     *
     * @param object $addtoobject Object to add user name fields to.
     * @param object $secondobject Object that contains user name field information.
     * @param string $prefix prefix to be added to all fields (including $additionalfields) e.g. authorfirstname.
     * @param array $additionalfields Additional fields to be matched with data in the second object.
     * The key can be set to the user table field name.
     * @return object User name fields.
     */
    function username_load_fields_from_object($addtoobject, $secondobject, $prefix = null, $additionalfields = null) {
        $fields = get_all_user_name_fields(false, null, $prefix);
        if ($additionalfields) {
            // Additional fields can specify their own 'alias' such as 'id' => 'userid'. This checks to see if
            // the key is a number and then sets the key to the array value.
            foreach ($additionalfields as $key => $value) {
                if (is_numeric($key)) {
                    $additionalfields[$value] = $prefix . $value;
                    unset($additionalfields[$key]);
                } else {
                    $additionalfields[$key] = $prefix . $value;
                }
            }
            $fields = array_merge($fields, $additionalfields);
        }
        foreach ($fields as $key => $field) {
            // Important that we have all of the user name fields present in the object that we are sending back.
            $addtoobject->$key = '';
            if (isset($secondobject->$field)) {
                $addtoobject->$key = $secondobject->$field;
            }
        }
        return $addtoobject;
    }
}

require_once($CFG->dirroot . "/message/lib.php");

if (!function_exists("message_format_message_text")) {

    /**
     * Try to guess how to convert the message to html.
     *
     * @access private
     *
     * @param stdClass $message
     * @param bool $forcetexttohtml
     * @return string html fragment
     */
    function message_format_message_text($message, $forcetexttohtml = false) {
        // Note: this is a very nasty hack that tries to work around the weird messaging rules and design.

        $options = new stdClass();
        $options->para = false;

        $format = $message->fullmessageformat;

        if ($message->smallmessage !== '') {
            if ($message->notification == 1) {
                if ($message->fullmessagehtml !== '' or $message->fullmessage !== '') {
                    $format = FORMAT_PLAIN;
                }
            }
            $messagetext = $message->smallmessage;

        } else if ($message->fullmessageformat == FORMAT_HTML) {
            if ($message->fullmessagehtml !== '') {
                $messagetext = $message->fullmessagehtml;
            } else {
                $messagetext = $message->fullmessage;
                $format = FORMAT_MOODLE;
            }

        } else {
            if ($message->fullmessage !== '') {
                $messagetext = $message->fullmessage;
            } else {
                $messagetext = $message->fullmessagehtml;
                $format = FORMAT_HTML;
            }
        }

        if ($forcetexttohtml) {
            // This is a crazy hack, why not set proper format when creating the notifications?
            if ($format === FORMAT_PLAIN) {
                $format = FORMAT_MOODLE;
            }
        }
        return format_text($messagetext, $format, $options);
    }

}

require_once($CFG->dirroot . "/calendar/lib.php");

if (!function_exists("calendar_get_events_by_id")) {
    /** Get calendar events by id
     *
     * @since Moodle 2.5
     * @param array $eventids list of event ids
     * @return array Array of event entries, empty array if nothing found
     */

    function calendar_get_events_by_id($eventids) {
        global $DB;

        if (!is_array($eventids) || empty($eventids)) {
            return array();
        }
        list($wheresql, $params) = $DB->get_in_or_equal($eventids);
        $wheresql = "id $wheresql";

        return $DB->get_records_select('event', $wheresql, $params);
    }
}

require_once($CFG->libdir . "/grouplib.php");

if (!function_exists("groups_get_my_groups")) {
    /**
     * Gets array of all groups in current user.
     *
     * @since Moodle 2.5
     * @category group
     * @return array Returns an array of the group objects.
     */
    function groups_get_my_groups() {
        global $DB, $USER;
        return $DB->get_records_sql("SELECT *
                                       FROM {groups_members} gm
                                       JOIN {groups} g
                                        ON g.id = gm.groupid
                                      WHERE gm.userid = ?
                                       ORDER BY name ASC", array($USER->id));
    }
}

if (!function_exists("get_course")) {
    /**
     * Gets a course object from database. If the course id corresponds to an
     * already-loaded $COURSE or $SITE object, then the loaded object will be used,
     * saving a database query.
     *
     * If it reuses an existing object, by default the object will be cloned. This
     * means you can modify the object safely without affecting other code.
     *
     * @param int $courseid Course id
     * @param bool $clone If true (default), makes a clone of the record
     * @return stdClass A course object
     * @throws dml_exception If not found in database
     */
    function get_course($courseid, $clone = true) {
        global $DB, $COURSE, $SITE;
        if (!empty($COURSE->id) && $COURSE->id == $courseid) {
            return $clone ? clone($COURSE) : $COURSE;
        } else if (!empty($SITE->id) && $SITE->id == $courseid) {
            return $clone ? clone($SITE) : $SITE;
        } else {
            return $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        }
    }
}

if (!function_exists('get_course')) {

    function get_course($courseid, $clone = true) {
        global $DB, $COURSE, $SITE;
        if (!empty($COURSE->id) && $COURSE->id == $courseid) {
            return $clone ? clone($COURSE) : $COURSE;
        } else if (!empty($SITE->id) && $SITE->id == $courseid) {
            return $clone ? clone($SITE) : $SITE;
        } else {
            return $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
        }
    }

}