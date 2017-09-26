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
class mod_points_calculator_stack implements imod_points_calculator {

    /** @var imod_points_calculator[] The calculators. */
    protected $calculators;
    /** @var int The default value when not found. */
    protected $default = 0;

    /**
     * Constructor.
     *
     * @param imod_points_calculator[] $calculators The calculators.
     */
    public function __construct(array $calculators) {
        $this->calculators = $calculators;
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

        foreach ($this->calculators as $calculator) {
            $points = $calculator->get_for_module($modname);
            if ($points !== null) {
                return $points;
            }
        }

        return $this->default;
    }

}
