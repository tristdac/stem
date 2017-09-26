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
 * School page.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

$id = optional_param('id', null, PARAM_INT);
$delete = optional_param('delete', false, PARAM_BOOL);
$confirm = optional_param('confirm', false, PARAM_BOOL);

admin_externalpage_setup('local_mootivated_school');

$url = new moodle_url('/local/mootivated/school.php', ['id' => $id]);
$school = new \local_mootivated\school($id);
$message = null;


if ($delete) {
    if ($confirm) {
        require_sesskey();
        $school->delete();
        redirect($PAGE->url, get_string('schooldeleted', 'local_mootivated'));
    }

} else {

    // Initialise the form.
    $form = new \local_mootivated\form\school($url->out(false), ['school' => $school]);
    if ($school) {
        $form->set_data($school->get_record());
    }

    // When the form is validated.
    if ($data = $form->get_data()) {
        $school->set_from_record($data);
        $school->save();
        $message = get_string('schoolsaved', 'local_mootivated');
        if (!$id) {
            $message = get_string('schoolcreated', 'local_mootivated');
        }

        $type = defined('core\output\notification::NOTIFY_SUCCESS') ? \core\output\notification::NOTIFY_SUCCESS : null;
        redirect(new moodle_url($url, ['id' => $school->get_id()]), $message, null, $type);

    } else if ($form->is_cancelled()) {
        // Redirect to the main admin page.
        redirect($PAGE->url);
    }
}

$output = $PAGE->get_renderer('local_mootivated');

// Display the page.
echo $output->header();
if ($message) {
    echo $output->notification($message, 'notifysuccess');
}
echo $output->heading(get_string('mootivatedsettings', 'local_mootivated'));
echo $output->admin_navigation('school_' . $school->get_id());

if ($delete) {
    $confirmbutton = new single_button(new moodle_url($url, ['delete' => 1, 'id' => $id, 'sesskey' => sesskey(), 'confirm' => 1]),
        get_string('yes'), 'get');
    echo $output->confirm(get_string('confirmdeleteschool', 'local_mootivated'), $confirmbutton, new moodle_url($url));
} else {
    $form->display();
}

echo $output->footer();
