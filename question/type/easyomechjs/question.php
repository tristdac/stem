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
 * easyomechjs Molecular Editor question definition class.
 *
 * @package    qtype
 * @subpackage easyomechjs
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
global $qa;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');
global $PAGE;
$PAGE->requires->strings_for_js(array('viewing_answer1', 'viewing_answer'), 'qtype_easyomechjs');

class qtype_easyomechjs_question extends qtype_shortanswer_question {
    public function compare_response_with_answer(array $response, question_answer $answer) {
        global $DB;

        if (!array_key_exists('answer', $response) || is_null($response['answer'])) {
            return false;
        }
        // Strip arrows from mrv strings!
        $cmlans        = new SimpleXMLElement($answer->answer);
        $cmlusr        = new SimpleXMLElement($response['answer']);
        $arrowscorrect = 0;
        $i             = 0;
        $arrowsusrall  = "";
        foreach ($cmlusr->MDocument[0]->MEFlow as $meflowusr) {
            $numbasepointsusr = $meflowusr->MEFlowBasePoint->count();
            $numsetpointsusr  = $meflowusr->MAtomSetPoint->count();
            if ($numbasepointsusr == 1) {
                $attrsusrstart  = $meflowusr->MEFlowBasePoint[0]->attributes();
                $attrsusrfinish = $meflowusr->MAtomSetPoint[0]->attributes();
            } else {
                $attrsusrstart  = $meflowusr->MAtomSetPoint[0]->attributes();
                $attrsusrfinish = $meflowusr->MAtomSetPoint[1]->attributes();
            }
            $arrowusr[$i] = $attrsusrstart . $attrsusrfinish;
            $arrowsusrall .= "*" . $arrowusr[$i];
            $i                = $i + 1;
            $numbasepointsans = $cmlans->MDocument[0]->MEFlow->MEFlowBasePoint->count();
        }
        $i            = 0;
        $arrowsansall = "";
        foreach ($cmlans->MDocument[0]->MEFlow as $meflowans) {
            $numbasepointsans = $meflowans->MEFlowBasePoint->count();
            $numsetpointsans  = $meflowans->MAtomSetPoint->count();
            if ($numbasepointsans == 1) {
                $attrsansstart  = $meflowans->MEFlowBasePoint[0]->attributes();
                $attrsansfinish = $meflowans->MAtomSetPoint[0]->attributes();
            } else {
                $attrsansstart  = $meflowans->MAtomSetPoint[0]->attributes();
                $attrsansfinish = $meflowans->MAtomSetPoint[1]->attributes();
            }
            $arrowans[$i] = $attrsansstart . $attrsansfinish;
            $arrowsansall .= "*" . $arrowans[$i];
            $i = $i + 1;
        }
        /*    general feedback
        if (!isset($arrowusr)) {
            $this->usecase = get_string('feedback_no_arrows', 'qtype_easyomechjs');
            return 0;
        }
        if (isset($cmlans->MDocument[0]->MEFlow['headFlags'])) { // Must be radical reaction!
            if (!isset($cmlusr->MDocument[0]->MEFlow['headFlags'])) {
                $this->usecase = get_string('feedback_radical', 'qtype_easyomechjs');
                return 0;
            }
        } else { // Must be polar reaction.
            if (isset($cmlusr->MDocument[0]->MEFlow['headFlags'])) {
                $this->usecase = get_string('feedback_polar', 'qtype_easyomechjs');
                return 0;
            }
        }
        */
        if (array_count_values($arrowusr) == array_count_values($arrowans)) {
            return 1;
        } else {
            return 0;
        }
        
    }
    public function get_expected_data() {
        return array(
            'answer' => PARAM_RAW,
            'easyomechjs' => PARAM_RAW,
            'mol' => PARAM_RAW
        );
    }
}
