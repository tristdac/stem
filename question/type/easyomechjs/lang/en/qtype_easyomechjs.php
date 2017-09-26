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
 * Strings for component 'qtype_easyomechjs', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package    qtype
 * @subpackage easyomechjs
 * @copyright  2014 onwards Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


$string['addmoreanswerblanks'] = 'Blanks for {no} More Answers';
$string['answermustbegiven'] = 'You must enter an answer if there is a grade or feedback.';
$string['answerno'] = 'Answer {$a}';
$string['pluginname'] = 'Chemistry - Electron Pushing / Curved Arrow (MarvinJS)';
$string['pluginname_help'] = 'Test and strengthen students knowledge of reaction mechanism, resonance and curved arrow notation.  Draw a reaction mechanism or resonance problem in MarvinJS below.  Be sure to add curved arrows where appropriate...the student will be required to provide them.  Choose from two difficulty levels; Show Products (easier) or Do not Show Products (more difficult).  You can ask questions such as;<br><ul><li>Please add curved arrows showing the flow of electrons for the following reaction?</li><li>Please add curved arrows showing how the following resonance structure could be obtained?</li></ul>';
$string['pluginname_link'] = 'question/type/easyomechjs';
$string['pluginnameadding'] = 'Adding a Electron Pushing / Curved Arrow question (MarvinJS)';
$string['pluginnameediting'] = 'Editing a Electron Pushing / Curved Arrow question (MarvinJS)';
$string['pluginnamesummary'] = 'Students must provide curved arrows on a structure or reaction template that you predefine.  You can ask questions such as;<ul><li>Please add curved arrows showing the flow of electrons for the following reaction?</li><li>Please add curved arrows showing how the following resonance structure could be obtained?</li></ul>';
$string['easyomechjs_options'] = 'Path to MarvinJS installation';
$string['enablejava'] = 'Tried but failed to load MarvinJS editor. You have not got a JAVA runtime environment working in your browser. You will need one to attempt this question.';
$string['enablejavaandjavascript'] = 'Loading MarvinJS editor.... If this message does not get replaced by the MarvinJS editor then you have not got javascript and a JAVA runtime environment working in your browser.';
$string['configeasyomechjsoptions'] = 'The path of your marvin installation relative to your web root.  (e.g. If your moodle is installed at /var/www/html/moodle and you install your Marvinjs at /var/www/html/marvinjs then you should use the default /marvinjs)';
$string['filloutoneanswer'] = '<b><ul>
<li>Draw a complete reaction or resonance conversion with curved arrows in the MarvinJS editor below.</li>
<li>Use multiple arrows for muti-step reactions.</li>
<li>Click the "Insert from editor" button when finished.</li>
<li>Optionally:  Add your incorrect reactions/resonance transforms and incorrect arrows to the other answer fields.</li>
</ul></b>';
$string['filloutanswers'] = 'Use the MarvinJS Molecular editor to create the answers, then press the "Insert from editor" buttons to insert the SMILES code into the answer boxes';
$string['insertfromeditor'] = 'Insert from editor';
$string['javaneeded'] = 'To use this page you need a Java-enabled browser. Download the latest Java plug-in from {$a}.';
$string['instructions'] = 'The ChemAxon ("mrv") representation of your model must be stored in the following field in order to be graded:';
$string['answer'] = 'Answer: {$a}';
$string['youranswer'] = 'Your answer: {$a}';
$string['correctansweris'] = 'The correct answer is: {$a}.';
$string['correctanswers'] = '<b>Instructions</b>';
$string['notenoughanswers'] = 'This type of question requires at least {$a} answers';
$string['pleaseenterananswer'] = 'Please enter an answer.';
$string['easyomechjseditor'] = 'MarvinJS Editor';
$string['author'] = 'Question type courtesy of Carl LeBlond, Indiana University of Pennsylvania';
$string['insert'] = 'Insert from editor';
$string['view'] = 'View in editor';
$string['my_response'] = 'My Response';
$string['correct_answer'] = 'Correct Answer';
$string['viewing_answer1'] = 'Currently viewing answer 1';
$string['viewing_answer'] = 'Currently viewing answer';
$string['feedback_no_arrows'] = 'You did not add any arrows.  Use the arrow icon on the left to add arrows next time!';
$string['feedback_radical'] = 'This is a radical reaction but you used full arrow heads.<br> You should use half arrow heads for radical reactions.  In radical reactions single electrons move.';
$string['feedback_polar'] = 'This is a polar reaction but you used half arrow heads.<br> You should use full arrow heads for polar reactions.  In polar reactions the electrons move in pairs.';
