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
 * easyoselectjs Molecular Editor question definition class.
 *
 * @package    qtype
 * @subpackage easyoselectjs
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $qa;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');
$generatedfeedback = "";
global $PAGE;
$PAGE->requires->strings_for_js(array('viewing_answer1', 'viewing_answer'), 'qtype_easyoselectjs');

class qtype_easyoselectjs_question extends qtype_shortanswer_question {
    public function compare_response_with_answer(array $response, question_answer $answer) {
        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }
        return self::compare_string_with_wildcard(
                $response['answer'], $answer->answer, false);
    }
    public function get_expected_data() {
        return array(
            'answer' => PARAM_RAW,
            'easyoselectjs' => PARAM_RAW,
            'mol' => PARAM_RAW
        );
    }
}
