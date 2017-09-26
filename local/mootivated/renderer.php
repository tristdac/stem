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
 * Mootivated renderer.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
use \local_mootivated\school;
use \local_mootivated\helper;

/**
 * Mootivated renderer class.
 *
 * @package    local_mootivated
 * @copyright  2017 Mootivation Technologies Corp.
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_mootivated_renderer extends plugin_renderer_base {

    /**
     * Render admin navigation.
     *
     * @param string $page Current page.
     * @return string
     */
    public function admin_navigation($page) {
        $tabs = [
            new tabobject('global', new moodle_url('/admin/settings.php', ['section' => 'local_mootivated']),
                get_string('global', 'local_mootivated'))
        ];

        $baseurl = new moodle_url('/local/mootivated/school.php');
        $schools = school::get_menu();
        foreach ($schools as $id => $name) {
            $tabs[] = new tabobject('school_' . $id, new moodle_url($baseurl, array('id' => $id)), $name);
        }

        $tabs[] = new tabobject(
            'school_0', new moodle_url($baseurl, array('id' => 0)), get_string('addschool', 'local_mootivated')
        );

        return $this->tabtree($tabs, $page);
    }

    /**
     * Render delete school button.
     *
     * @param school $school The school.
     * @return string
     */
    public function delete_school_button(school $school) {
        $deleteurl = new moodle_url('/local/mootivated/school.php', ['id' => $school->get_id(), 'delete' => 1]);
        $icon = new pix_icon('t/delete', '', '', ['class' => 'icon iconsmall']);
        return $this->action_link($deleteurl, get_string('deleteschool', 'local_mootivated'), null, null, $icon);
    }

    /**
     * Render status report.
     *
     * @return string
     */
    public function status_report() {
        $ok = $this->pix_icon('i/valid', '');
        $nok = $this->pix_icon('i/invalid', '');

        $o = '';

        $missingbits = !helper::mootivated_role_exists() || !helper::webservices_enabled() || !helper::rest_enabled();
        if ($missingbits) {
            $o .= html_writer::tag('p', get_string('setupnotcomplete', 'local_mootivated'));
            $o .= html_writer::tag('p',
                $this->action_link(new moodle_url('/local/mootivated/quicksetup.php', ['sesskey' => sesskey()]),
                get_string('doitforme', 'local_mootivated'), null, ['class' => 'btn btn-default']));
        }

        $o .= html_writer::start_tag('table', ['class' => 'generaltable']);

        $o .= html_writer::start_tag('tr');
        $o .= html_writer::start_tag('td', ['width' => 20]);
        $o .= helper::webservices_enabled() ? $ok : $nok;
        $o .= html_writer::end_tag('td');
        $o .= html_writer::start_tag('td');
        $o .= get_string('webservicesenabled', 'local_mootivated');
        $o .= html_writer::end_tag('td');
        $o .= html_writer::start_tag('td');
        $o .= $this->action_link(new moodle_url('/admin/search.php', ['query' => 'enablewebservices']), '', null,
            null, new pix_icon('t/edit', get_string('edit')));
        $o .= html_writer::end_tag('td');
        $o .= html_writer::end_tag('tr');

        $o .= html_writer::start_tag('tr');
        $o .= html_writer::start_tag('td');
        $o .= helper::rest_enabled() ? $ok : $nok;
        $o .= html_writer::end_tag('td');
        $o .= html_writer::start_tag('td');
        $o .= get_string('restprotocolenabled', 'local_mootivated');
        $o .= html_writer::end_tag('td');
        $o .= html_writer::start_tag('td');
        $o .= $this->action_link(new moodle_url('/admin/settings.php', ['section' => 'webserviceprotocols']), '', null,
            null, new pix_icon('t/edit', get_string('edit')));
        $o .= html_writer::end_tag('td');
        $o .= html_writer::end_tag('tr');

        $o .= html_writer::start_tag('tr');
        $o .= html_writer::start_tag('td');
        $o .= helper::mootivated_role_exists() ? $ok : $nok;
        $o .= html_writer::end_tag('td');
        $o .= html_writer::start_tag('td');
        $o .= get_string('mootivatedrolecreated', 'local_mootivated');
        $o .= html_writer::end_tag('td');
        $o .= html_writer::start_tag('td');
        if (helper::mootivated_role_exists()) {
            $role = helper::get_mootivated_role();
            $contextid = context_system::instance()->id;
            $o .= $this->action_link(new moodle_url('/admin/roles/assign.php', ['contextid' => $contextid, 'roleid' => $role->id]),
                '', null, null, new pix_icon('t/assignroles', get_string('assignrole', 'role')));
            $o .= '&nbsp;';
            $o .= $this->action_link(new moodle_url('/admin/roles/define.php', ['action' => 'view', 'roleid' => $role->id]),
                '', null, null, new pix_icon('t/edit', get_string('edit')));
        } else {
            $o .= $this->action_link(new moodle_url('/admin/roles/manage.php'), '', null,
                null, new pix_icon('t/edit', get_string('edit')));
        }
        $o .= html_writer::end_tag('td');
        $o .= html_writer::end_tag('tr');

        $o .= html_writer::end_tag('table');

        return $o;
    }

}
