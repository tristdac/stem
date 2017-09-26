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
 * Module points calculator.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_mootivated\local\calculator;
defined('MOODLE_INTERNAL') || die();

/**
 * Module points calculator.
 *
 * Returns a certain amount of points for a module.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_points_calculator implements imod_points_calculator {

    /** @var object[] Each object contains they 'points', and 'mod' keys. */
    protected $rules;
    /** @var int Value when unknown. */
    protected $default;

    /**
     * Constructor.
     *
     * @param object[] $rules The rules.
     */
    public function __construct(array $rules, $default = null) {
        $this->rules = $rules;
        $this->default = $default;
    }

    /**
     * Find a school by member.
     *
     * @param string $modname The module name
     * @return points|null
     */
    public function get_for_module($modname) {
        if (strpos($modname, 'mod_') === 0) {
            $modname = substr($modname, 4);
        }

        $rules = array_filter($this->rules, function($rule) use ($modname) {
            return $rule->mod === $modname;
        });

        if (empty($rules)) {
            return $this->default;
        }

        // If we have more than one match, we take the first one.
        $rule = array_shift($rules);
        return $rule->points;
    }

}
