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
 * easyonamejs Molecular Editor question definition class.
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/question.php');
$generatedfeedback = "";
global $PAGE;
$PAGE->requires->strings_for_js(array('viewing_answer1', 'viewing_answer'), 'qtype_easyonamejs');

class qtype_easyonamejs_question extends qtype_shortanswer_question {
    public function compare_response_with_answer(array $response, question_answer $answer) {
        // Check to see if correct or not.
        $usrsmiles = $this->openbabel_convert_molfile($response['answer'], 'can');
        $anssmiles = $this->openbabel_convert_molfile($answer->answer, 'can');

        if ($usrsmiles == $anssmiles) {
            return true;
        } else {
            return false;
        }
    }
    public function get_expected_data() {
        return array(
            'answer' => PARAM_RAW,
            'easyonamejs' => PARAM_RAW,
            'mol' => PARAM_RAW
        );
    }
    public function openbabel_convert_molfile($molfile, $format) {
        $marvinjsconfig = get_config('qtype_easyonamejs_options');
        $descriptorspec = array(
           0 => array("pipe", "r"),  // Stdin is a pipe that the child will read from.
           1 => array("pipe", "w"),  // Stdout is a pipe that the child will write to.
           2 => array("pipe", "r") // Stderr is a file to write to.
        );
        $output = '';
        //echo $marvinjsconfig->obabelpath;
        //$command = escapeshellarg($marvinjsconfig->obabelpath . ' -imol -o' . $format . ' --title');
        $command = $marvinjsconfig->obabelpath . ' -imol -o' . $format . ' --title';

        $process = proc_open($command, $descriptorspec, $pipes);
        //print_object($process);
        if (is_resource($process)) {
            /* 0 => writeable handle connected to child stdin
               1 => readable handle connected to child stdout
               2 +> errors */
            //print_object($pipes);
            fwrite($pipes[0], $molfile);
            fclose($pipes[0]);
            $output = stream_get_contents($pipes[1]);
            fclose($pipes[1]);

            $err = stream_get_contents($pipes[2]);
            fclose($pipes[2]);
            //echo $output;
            //echo $err;
            // It is important that you close any pipes before calling,
            // proc_close in order to avoid a deadlock.
            $returnvalue = proc_close($process);
        }
        return trim($output);
    }

}
