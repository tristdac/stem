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
 * Web service local plugin template external functions and service definitions.
 *
 * @package   local_mootivated
 * @copyright 2016 Mootivation Technologies Corp.
 * @author    Mootivation Technologies Corp.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// We defined the web service functions to install.
$functions = [
    'local_mootivated_login' => [
        'classname'     => 'local_mootivated\\external',
        'methodname'    => 'login',
        'description'   => 'Login to the remote server.',
        'type'          => 'write',
        'capabilities'  => '',
    ],
    'local_mootivated_upload_avatar' => [
        'classname'     => 'local_mootivated\\external',
        'methodname'    => 'upload_avatar',
        'description'   => 'Upload an avatar.',
        'type'          => 'write',
        'capabilities'  => 'moodle/user:viewdetails, moodle/user:editownprofile',
    ]
];

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = [
    get_string('mootivated_web_services', 'local_mootivated') => array(
        'functions' => [
            'local_mootivated_login',
            'local_mootivated_upload_avatar'
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'local_mootivated',
    ),
];
