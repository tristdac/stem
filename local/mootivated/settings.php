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
 * Settings.
 *
 * @package   local_mootivated
 * @copyright 2016 Mootivation Technologies Corp.
 * @author    Mootivation Technologies Corp.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

// Ensure the configurations for this site are set.
if ($hassiteconfig) {

    // Create the new settings page.
    $settings = new \local_mootivated\global_settings_page();
    $settings->add(new \local_mootivated\status_admin_setting());
    $settings->add(new admin_setting_configtext('local_mootivated/server_ip',
        get_string('serverip', 'local_mootivated'), get_string('serverip_desc', 'local_mootivated'),
        'school.mootivated.com', PARAM_RAW));
    $ADMIN->add('localplugins', $settings);

    // Create the hidden page holding the schools.
    $temp = new admin_externalpage('local_mootivated_school', get_string('mootivatedsettings', 'local_mootivated'),
        new moodle_url('/admin/settings.php', array('section' => 'local_mootivated')), 'moodle/site:config', true);
    $ADMIN->add('localplugins', $temp);
}
