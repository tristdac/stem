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
 * Status admin setting.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mootivated;
defined('MOODLE_INTERNAL') || die();

use admin_setting;

/**
 * Status admin setting class.
 *
 * This is a fake admin setting to display whether the plugin is set-up to work
 * and to offer a one-click do it all solution.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class status_admin_setting extends admin_setting {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->nosave = true;
        parent::__construct('local_mootivated/status', get_string('setupstatus', 'local_mootivated'), '', '');
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true.
     */
    public function get_setting() {
        return true;
    }

    /**
     * Always returns true.
     *
     * @return bool Always returns true.
     */
    public function get_defaultsetting() {
        return true;
    }

    /**
     * Never write settings.
     *
     * @param mixed $data The data.
     * @return string Always empty.
     */
    public function write_setting($data) {
        return '';
    }

    /**
     * Display.
     *
     * @param mixed $data The data.
     * @param string $query The query.
     * @return string Returns an HTML string
     */
    public function output_html($data, $query='') {
        global $PAGE;
        $renderer = $PAGE->get_renderer('local_mootivated');
        return format_admin_setting($this, $this->visiblename, $renderer->status_report(), '', false);
    }
}
