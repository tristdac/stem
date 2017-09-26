<?php
// This file is part of Treasurehunt for Moodle
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
 * This file contains the definition for the renderable classes for the assignment
 *
 * @package   mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro <jpdecastro@tel.uva.es>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * Renderable user_historical_attempts
 * @package   mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro <jpdecastro@tel.uva.es>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class treasurehunt_user_historical_attempts implements renderable {

    public $attempts = [];
    public $coursemoduleid = 0;
    public $username = '';
    public $outoftime = 0;
    public $roadfinished = 0;
    public $teacherreview = 0;

    /**
     * constructor
     *
     */
    public function __construct($attempts, $coursemoduleid, $username, $outoftime, $roadfinished, $teacherreview) {
        $this->attempts = $attempts;
        $this->coursemoduleid = $coursemoduleid;
        $this->username = $username;
        $this->outoftime = $outoftime;
        $this->roadfinished = $roadfinished;
        $this->teacherreview = $teacherreview;
    }

}

/**
 * Renderable info
 * @package   mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro <jpdecastro@tel.uva.es>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class treasurehunt_info implements renderable {

    public $treasurehunt = null;
    public $timenow = 0;
    public $courseid = 0;
    public $numqrs = 0;

    /**
     * constructor
     */
    public function __construct($treasurehunt, $timenow, $courseid, $roads, $numqrs) {
        $this->treasurehunt = $treasurehunt;
        $this->timenow = $timenow;
        $this->courseid = $courseid;
        $this->roads = $roads;
        $this->numqrs = $numqrs;
    }
}

/**
 * Renderable users_progress
 * @package   mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro <jpdecastro@tel.uva.es>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class treasurehunt_users_progress implements renderable {

    /** @var array participantcount - The number of users who can submit to this assignment */
    public $roadsusersprogress = array();
    public $groupmode = 0;
    public $coursemoduleid = 0;
    public $duplicategroupsingroupings = array();
    public $duplicateusersingroups = array();
    public $unassignedusers = array();
    public $viewpermission = false;
    public $managepermission = false;

    /**
     * constructor
     *
     */
    public function __construct($roadsusersprogress, $groupmode, $coursemoduleid,
                                $duplicategroupsingroupings, $duplicateusersingroups,
                                $unassignedusers, $viewpermission, $managepermission) {
        $this->roadsusersprogress = $roadsusersprogress;
        $this->groupmode = $groupmode;
        $this->coursemoduleid = $coursemoduleid;
        $this->duplicategroupsingroupings = $duplicategroupsingroupings;
        $this->duplicateusersingroups = $duplicateusersingroups;
        $this->unassignedusers = $unassignedusers;
        $this->viewpermission = $viewpermission;
        $this->managepermission = $managepermission;
    }

}

/**
 * Renderable, Templatable play_page
 * @package   mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro <jpdecastro@tel.uva.es>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class treasurehunt_play_page implements renderable, templatable {

    private $treasurehunt = null;
    private $cmid = 0;

    public function __construct($treasurehunt, $cmid) {
        $this->treasurehunt = $treasurehunt;
        $this->cmid = $cmid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        GLOBAL $USER;
        $data = new stdClass();
        $user = new stdClass();
        $user->name = fullname($USER);
        $user->picture = $output->user_picture($USER, array('link' => false));
        $data->user = $user;
        $data->cmid = $this->cmid;
        $data->treasurehunt = $this->treasurehunt;
        if (empty($this->treasurehunt->description)) {
            $hasdescription = false;
        } else {
            $hasdescription = true;
        }
        $data->hasdescription = $hasdescription;
        return $data;
    }

}
