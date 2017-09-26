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
 * Duration element.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/form/duration.php');

/**
 * Duration element class.
 *
 * We cannot use namespaces because formslib sucks.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_form_duration extends \MoodleQuickForm_duration {

    /** @var array The units. */
    protected $_units;

    /**
     * Real constructor.
     *
     * @param string $elementName Name.
     * @param string $elementLabel Label.
     * @param array $options Options.
     * @param array $attributes Attributes.
     */
    public function __construct($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        \MoodleQuickForm_duration::__construct($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Ugly constructor override...
     *
     * @param string $elementName Name.
     * @param string $elementLabel Label.
     * @param array $options Options.
     * @param array $attributes Attributes.
     */
    function local_mootivated_form_duration($elementName = null, $elementLabel = null, $options = array(), $attributes = null) {
        $this->MoodleQuickForm_duration($elementName, $elementLabel, $options, $attributes);
    }

    /**
     * Get units.
     *
     * Override to remove days and weeks.
     *
     * @return array
     */
    public function get_units() {
        if (is_null($this->_units)) {
            $this->_units = array(
                3600 => get_string('hours'),
                60 => get_string('minutes'),
                1 => get_string('seconds'),
            );
        }
        return $this->_units;
    }

}
