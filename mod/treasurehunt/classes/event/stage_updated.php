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
 * The mod_treasurehunt stage updated event
 *
 * @package   mod_treasurehunt
 * @copyright 2016 onwards Adrian Rodriguez Fernandez <huorwhisp@gmail.com>, Juan Pablo de Castro <jpdecastro@tel.uva.es>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_treasurehunt\event;

defined('MOODLE_INTERNAL') || die();

defined('MOODLE_INTERNAL') || die();

/**
 * The mod_treasurehunt stage updated event class.
 *
 * @property-read array $other {
 *      Extra information about event.
 *
 *      - string concept: (optional) the concept of created stage.
 * }
 *
 */
class stage_updated extends \core\event\base {

    /**
     * Init method
     */
    protected function init() {
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'treasurehunt_stages';
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('eventstageupdated', 'mod_treasurehunt');
    }

    public function get_description() {
        return "The user with id '$this->userid' has updated the stage with id '$this->objectid' for " .
                "the treasure hunt activity with course module id '$this->contextinstanceid'.";
    }

    /**
     * Get URL related to the action.
     *
     * @return \moodle_url
     */
    public function get_url() {
        return new \moodle_url("/mod/treasurehunt/editstage.php",
                array('cmid' => $this->contextinstanceid,
            'id' => $this->objectid));
    }

    public static function get_objectid_mapping() {
        return array('db' => 'treasurehunt_stages', 'restore' => 'treasurehunt_stage');
    }

}
