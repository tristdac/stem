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
 * Defines the editing form for the easyoselectjs question type.
 *
 * @package    qtype
 * @subpackage easyoselectjs
 * @copyright  2014 and onward Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');
class qtype_easyoselectjs_edit_form extends qtype_shortanswer_edit_form {
    protected function definition_inner($mform) {
        global $PAGE, $CFG;
        $PAGE->requires->css('/question/type/easyoselectjs/easyoselectjs_styles.css');
        $marvinjsconfig = get_config('qtype_easyoselectjs_options');
        $marvinjspath   = $marvinjsconfig->path;
        $protocol = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/js/promise-0.1.1.min.js'));
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/js/marvinjslauncher.js'));
        $mform->addElement('static', 'answersinstruct',
            get_string('correctanswers', 'qtype_easyoselectjs'), get_string('filloutoneanswer', 'qtype_easyoselectjs'));
        $mform->closeHeaderBefore('answersinstruct');
        $mform->setType('structure', PARAM_RAW);
        $mform->addElement('hidden', 'structure', "", array('id' => 'id_structure'));
        $mform->addElement('html', html_writer::start_tag('div', array(
         //   'style' => 'width:650px;',
            'id' => 'appletdiv',
            'class' => 'easyoselectjs resizable'
        )));
        $mform->addElement('html', html_writer::start_tag('div', array(
            'style' => 'float: left;font-style: italic ;'
        )));
        $mform->addElement('html', html_writer::start_tag('small'));
        $easyoselectjshomeurl = 'http://www.chemaxon.com';
        $mform->addElement('html', html_writer::link($easyoselectjshomeurl,
            get_string('easyoselectjseditor', 'qtype_easyoselectjs')));
        $mform->addElement('html', html_writer::empty_tag('br'));
        $mform->addElement('html', html_writer::tag('span', get_string('author', 'qtype_easyoselectjs'), array(
            'class' => 'easyoselectjsauthor'
        )));
        $mform->addElement('html', html_writer::end_tag('small'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $marvinconfig = get_config('qtype_easyoselectjs_options');
        $marvinpath   = $marvinconfig->path;
        $PAGE->requires->js_init_call('M.qtype_easyoselectjs.insert_applet', array(
            $CFG->wwwroot,
            $marvinpath
        ));
        $this->add_per_answer_fields($mform,
            get_string('answerno', 'qtype_easyoselectjs', '{no}'), question_bank::fraction_options());
        $this->add_interactive_settings();
        $PAGE->requires->js_init_call('M.qtype_easyoselectjs.init_getanswerstring', array(
            $CFG->version
        ));
        $PAGE->requires->js_init_call('M.qtype_easyoselectjs.init_viewanswerstring', array(
            $CFG->version
        ));
    }
    protected function get_per_answer_fields($mform, $label, $gradeoptions, &$repeatedoptions, &$answersoption) {
        $repeated     = parent::get_per_answer_fields($mform, $label, $gradeoptions, $repeatedoptions, $answersoption);
        $scriptattrs  = 'class = id_insert';
        $viewbutton = $mform->createElement('button', 'view',
            get_string('view', 'qtype_easyoselectjs'), 'class = id_view');
        array_splice($repeated, 1, 0, array(
            $viewbutton
        ));
        $insertbutton = $mform->createElement('button', 'insert',
            get_string('insertfromeditor', 'qtype_easyoselectjs'), $scriptattrs);
        array_splice($repeated, 1, 0, array(
            $insertbutton
        ));
        return $repeated;
    }
    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);
        return $question;
    }
    public function qtype() {
        return 'easyoselectjs';
    }
}
