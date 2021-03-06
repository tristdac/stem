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
 * Persistent exporter.
 *
 * @package    block_stash
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_stash\external;
defined('MOODLE_INTERNAL') || die();

// Requirements for core_competency\external\persistent_exporter copy/paste.
use coding_exception;

if (!class_exists('core\\external\\persistent_exporter')) {

    // At the moment we rely on the core_competency.
    // Add compability with < 3.1 where the class does not exist.
    if (!class_exists('core_competency\\external\\persistent_exporter')) {

        require_once($CFG->libdir . '/externallib.php');

        /**
         * Copy/paste from core_competency\external\persistent_exporter.
         */
        abstract class persistent_exporter extends exporter {

            /** @var \core_competency\persistent The persistent object we will export. */
            protected $persistent = null;

            /**
             * Constructor - saves the persistent object, and the related objects.
             *
             * @param \core_competency\persistent $persistent The persistent object to export.
             * @param array $related - An optional list of pre-loaded objects related to this persistent.
             */
            public final function __construct(\block_stash\persistent $persistent, $related = array()) {
                $classname = static::define_class();
                if (!$persistent instanceof $classname) {
                    throw new coding_exception('Invalid type for persistent. ' .
                                               'Expected: ' . $classname . ' got: ' . get_class($persistent));
                }
                $this->persistent = $persistent;

                if (method_exists($this->persistent, 'get_context') && !isset($this->related['context'])) {
                    $this->related['context'] = $this->persistent->get_context();
                }

                $data = $persistent->to_record();
                parent::__construct($data, $related);
            }

            /**
             * Persistent exporters get their standard properties from the persistent class.
             *
             * @return array Keys are the property names, and value their definition.
             */
            protected final static function define_properties() {
                $classname = static::define_class();
                return $classname::properties_definition();
            }

            /**
             * Returns the specific class the persistent should be an instance of.
             *
             * @return string
             */
            protected static function define_class() {
                throw new coding_exception('define_class() must be overidden.');
            }

        }
    } else {
        // When this class will be in core we'll depend on it.

        /**
         * Persistent exporter class.
         *
         * @package    block_stash
         * @copyright  2016 Frédéric Massart - FMCorz.net
         * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
         */
        class persistent_exporter extends \core_competency\external\persistent_exporter {
        }

    }

} else {
    /**
     * Persistent exporter class.
     *
     * @package    block_stash
     * @copyright  2016 Frédéric Massart - FMCorz.net
     * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
     */
    class persistent_exporter extends \core\external\persistent_exporter {
    }    
}
