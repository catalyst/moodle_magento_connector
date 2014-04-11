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
 * Magento enrolment plugin
 *
 * @package    enrol
 * @subpackage magento
 * @author     Edwin Phillips <edwin.phillips@catalyst-eu.net>
 * @copyright  Catalyst IT Ltd 2014 <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('ENROL_MAGENTO_STUDENT_SHORTNAME', 'student');

class enrol_magento_plugin extends enrol_plugin {

    public function roles_protected() {
        return false;
    }

    public function allow_manage(stdClass $instance) {
        return true;
    }

    public function allow_unenrol(stdClass $instance) {
        return true;
    }

    /**
     * Sets up navigation entries.
     *
     * @param stdClass $instancesnode
     * @param stdClass $instance
     * @return void
     */
    public function add_course_navigation($instancesnode, stdClass $instance) {

        if ($instance->enrol !== 'magento') {
             throw new coding_exception('Invalid enrol instance type!');
        }

        $context = context_course::instance($instance->courseid);
        if (has_capability('enrol/magento:config', $context)) {
            $managelink = new moodle_url('/enrol/magento/edit.php', array('courseid' => $instance->courseid, 'id' => $instance->id));
            $instancesnode->add($this->get_instance_name($instance), $managelink, navigation_node::TYPE_SETTING);
        }
    }

    /**
     * Returns edit icons for the page with list of instances
     *
     * @param stdClass $instance
     * @return array
     */
    public function get_action_icons(stdClass $instance) {
        global $OUTPUT;

        if ($instance->enrol !== 'magento') {
            throw new coding_exception('Invalid enrol instance!');
        }

        $context = context_course::instance($instance->courseid);
        $icons = array();
        if (has_capability('enrol/magento:config', $context)) {
            $editlink = new moodle_url("/enrol/magento/edit.php", array('courseid' => $instance->courseid, 'id' => $instance->id));
            $icons[] = $OUTPUT->action_icon($editlink, new pix_icon('t/edit', get_string('edit'), 'core',
                array('class' => 'smallicon')));
        }

        return $icons;
    }

    /**
     * Returns link to page which may be used to add new instance of enrolment plugin in course
     *
     * @param int $courseid
     * @return moodle_url page url
     */
    public function get_newinstance_link($courseid) {

        $context = context_course::instance($courseid, MUST_EXIST);
        if (!has_capability('moodle/course:enrolconfig', $context) || !has_capability('enrol/magento:config', $context)) {
            return null;
        }

        return new moodle_url('/enrol/magento/edit.php', array('courseid' => $courseid));
    }

    /**
     * Add new instance of enrol plugin with default settings.
     *
     * @param stdClass $course
     * @return int id of new instance
     */
    public function add_default_instance($course) {

        $fields = $this->get_instance_defaults();

        return $this->add_instance($course, $fields);
    }

    /**
     * Returns defaults for new instances.
     *
     * @return array
     */
    public function get_instance_defaults() {
        global $DB;

        $fields = array();
        $fields['status'] = $this->get_config('status');
        $fields['roleid'] = $DB->get_field('role', 'id', array('shortname' => ENROL_MAGENTO_STUDENT_SHORTNAME));

        return $fields;
    }

    /**
     * Gets an array of the user enrolment actions.
     *
     * @param course_enrolment_manager $manager
     * @param stdClass $ue A user enrolment object
     * @return array An array of user_enrolment_actions
     */
    public function get_user_enrolment_actions(course_enrolment_manager $manager, $ue) {

        $actions  = array();
        $context  = $manager->get_context();
        $instance = $ue->enrolmentinstance;

        $params = $manager->get_moodlepage()->url->params();
        $params['ue'] = $ue->id;

        if ($this->allow_unenrol_user($instance, $ue) && has_capability('enrol/magento:unenrol', $context)) {
            $url = new moodle_url('/enrol/unenroluser.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/delete', ''), get_string('unenrol', 'enrol'), $url,
                    array('class' => 'unenrollink', 'rel' => $ue->id));
        }
        if ($this->allow_manage($instance) && has_capability('enrol/magento:manage', $context)) {
            $url = new moodle_url('/enrol/editenrolment.php', $params);
            $actions[] = new user_enrolment_action(new pix_icon('t/edit', ''), get_string('edit'), $url,
                    array('class' => 'editenrollink', 'rel' => $ue->id));
        }

        return $actions;
    }

    /**
     * Send welcome email to specified user.
     *
     * @param stdClass $instance
     * @param stdClass $user user record
     * @return void
     */
    public function email_welcome_message($instance, $user) {
        global $CFG, $DB;

        $username = (isset($instance->newusername)) ? $instance->newusername : null;
        $password = (isset($instance->newaccountpassword)) ? $instance->newaccountpassword : null;

        $course = $DB->get_record('course', array('id' => $instance->courseid), '*', MUST_EXIST);
        $context = context_course::instance($course->id);

        $a = new stdClass();
        $a->coursename = format_string($course->fullname, true, array('context' => $context));
        $a->courseurl  = "$CFG->wwwroot/course/view.php?id={$course->id}";
        $a->profileurl = "$CFG->wwwroot/user/view.php?id={$user->id}&course={$course->id}";
        $a->forgottenpasswordurl = "$CFG->wwwroot/login/forgot_password.php";

        $a->username   = ($username) ? $username : '';
        $a->password   = ($password) ? $password : '';

        if (trim($instance->customtext1) !== '') { // If there is a custom welcome message use it
            $message = $instance->customtext1;
            $message = str_replace('{$a->coursename}', $a->coursename, $message);
            $message = str_replace('{$a->courseurl}',  $a->courseurl, $message);
            $message = str_replace('{$a->profileurl}', $a->profileurl, $message);
            $message = str_replace('{$a->forgottenpasswordurl}', $a->forgottenpasswordurl, $message);
            if (strpos($message, '<') === false) { // Plain text only.
                $messagetext = $message;
                $messagehtml = text_to_html($messagetext, null, false, true);
            } else { // This is most probably the tag/newline soup known as FORMAT_MOODLE
                $messagetext = html_to_text($messagehtml);
                $messagehtml = format_text($message, FORMAT_MOODLE,
                    array('context' => $context, 'para' => false, 'newlines' => true, 'filter' => true));
            }
        } else { // Otherwise use the default defined in the language file
            $messagetext = get_string('welcometocoursetext', 'enrol_magento', $a);
            $messagehtml = get_string('welcometocoursetexthtml', 'enrol_magento', $a);
        }

        if ($username && $password) {
            $messagetext .= get_string('newcredentials', 'enrol_magento', $a);
            $messagehtml .= get_string('newcredentialshtml', 'enrol_magento', $a);
        } else {
            $messagetext .= get_string('existinguser', 'enrol_magento', $a);
            $messagehtml .= get_string('existinguserhtml', 'enrol_magento', $a);
        }

        $subject = get_string('welcometocourse', 'enrol_magento', format_string($course->fullname, true, array('context' => $context)));

        $rusers = array();
        if (!empty($CFG->coursecontact)) {
            $croles = explode(',', $CFG->coursecontact);
            list($sort, $sortparams) = users_order_by_sql('u');
            $rusers = get_role_users($croles, $context, true, '', 'r.sortorder ASC, ' . $sort, null, '', '', '', '', $sortparams);
        }
        if ($rusers) {
            $contact = reset($rusers);
        } else {
            $contact = core_user::get_support_user();
        }

        email_to_user($user, $contact, $subject, $messagetext, $messagehtml);
    }

}
