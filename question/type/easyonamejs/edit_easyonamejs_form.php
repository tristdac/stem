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
 * Defines the editing form for the easyonamejs question type.
 *
 * @package    qtype
 * @subpackage easyonamejs
 * @copyright  2014 and onward Carl LeBlond
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/question/type/shortanswer/edit_shortanswer_form.php');
class qtype_easyonamejs_edit_form extends qtype_shortanswer_edit_form {
    protected function definition_inner($mform) {
        global $PAGE, $CFG;
        $PAGE->requires->css('/question/type/easyonamejs/easyonamejs_styles.css');
        $marvinjsconfig = get_config('qtype_easyonamejs_options');
        $marvinjspath   = $marvinjsconfig->path;
        $protocol = (empty($_SERVER['HTTPS']) or $_SERVER['HTTPS'] == 'off') ? 'http://' : 'https://';
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/js/promise-0.1.1.min.js'));
        $PAGE->requires->js(new moodle_url($protocol . $_SERVER['HTTP_HOST'] . $marvinjspath . '/js/marvinjslauncher.js'));
        $mform->addElement('static', 'answersinstruct',
            get_string('correctanswers', 'qtype_easyonamejs'), get_string('filloutoneanswer', 'qtype_easyonamejs'));
        $mform->closeHeaderBefore('answersinstruct');
        $mform->addElement('html', html_writer::start_tag('div', array(
        //    'style' => 'width:650px;',
            'class' => 'easyonamejs resizable',
            'id' => 'appletdiv'
        )));

        $mform->addElement('html', html_writer::start_tag('div', array(
            'style' => 'float: left;font-style: italic ;'
        )));
        $mform->addElement('html', html_writer::start_tag('small'));
        $easyonamejshomeurl = 'http://www.chemaxon.com';
        $mform->addElement('html', html_writer::link($easyonamejshomeurl, get_string('easyonamejseditor', 'qtype_easyonamejs')));
        $mform->addElement('html', html_writer::empty_tag('br'));
        $mform->addElement('html', html_writer::tag('span', get_string('author', 'qtype_easyonamejs'), array(
            'class' => 'easyonamejsauthor'
        )));
        $mform->addElement('html', html_writer::end_tag('small'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $mform->addElement('html', html_writer::end_tag('div'));
        $marvinconfig = get_config('qtype_easyonamejs_options');
        $marvinpath   = $marvinconfig->path;
        $PAGE->requires->js_init_call('M.qtype_easyonamejs.insert_applet', array(
            $CFG->wwwroot,
            $marvinpath
        ));
        $this->add_per_answer_fields($mform,
            get_string('answerno', 'qtype_easyonamejs', '{no}'), question_bank::fraction_options());
        $this->add_interactive_settings();
        $PAGE->requires->js_init_call('M.qtype_easyonamejs.init_getanswerstring', array(
            $CFG->version
        ));
        $PAGE->requires->js_init_call('M.qtype_easyonamejs.init_viewanswerstring', array(
            $CFG->version
        ));
        $PAGE->requires->js_init_call('M.qtype_easyonamejs.init_viewanswerstring', array(
            $CFG->version
        ));
    }
    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $answeroptions = array();
        $answeroptions[] = $mform->createElement('textarea', 'answer',
                $label, array('row' => 1));
        $answeroptions[] = $mform->createElement('select', 'fraction',
                get_string('grade'), $gradeoptions);
        $repeated[] = $mform->createElement('group', 'answeroptions',
                 $label, $answeroptions, null, false);
        $repeated[] = $mform->createElement('editor', 'feedback',
                get_string('feedback', 'question'), array('rows' => 5), $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        $scriptattrs  = 'class = id_insert';
        $viewbutton = $mform->createElement('button', 'view',
            get_string('view', 'qtype_easyonamejs'), 'class = id_view');
        array_splice($repeated, 1, 0, array($viewbutton));
        $insertbutton = $mform->createElement('button', 'insert',
            get_string('insertfromeditor', 'qtype_easyonamejs'), $scriptattrs);
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
        return 'easyonamejs';
    }
}
