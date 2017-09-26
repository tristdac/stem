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
 * School resolver.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mootivated;
defined('MOODLE_INTERNAL') || die();

/**
 * School resolver class.
 *
 * The main purpose of this implementation is to keep a record of the schools
 * in a static cache to try to avoid some database queries.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class school_resolver implements ischool_resolver {

    /** @var school[] Schools indexed by member ID. */
    protected $schoolbymembers = [];

    /**
     * Find a school by member.
     *
     * @param int $userid The user/member ID.
     * @return school|null
     */
    public function get_by_member($userid) {
        if (!array_key_exists($userid, $this->schoolbymembers)) {
            $this->schoolbymembers[$userid] = school::load_from_member($userid);
        }
        return $this->schoolbymembers[$userid];
    }

}
