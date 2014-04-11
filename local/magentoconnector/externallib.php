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
 * @package    local
 * @subpackage magentoconnector
 * @author     Edwin Phillips <edwin.phillips@catalyst-eu.net>
 * @copyright  Catalyst IT Ltd 2014 <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/local/magentoconnector/lib.php');

class local_magentoconnector_external extends external_api {

    /**
     * Returns description of method parameters
     *
     * @return external_function_parameters
     */
    public static function process_magento_request_parameters() {

        return new external_function_parameters(
            array(
                'order_number' => new external_value(PARAM_TEXT),
                'customer'     => new external_single_structure(
                    array(
                        'firstname' => new external_value(PARAM_TEXT),
                        'lastname'  => new external_value(PARAM_TEXT),
                        'email'     => new external_value(PARAM_TEXT),
                        'city'      => new external_value(PARAM_TEXT),
                        'country'   => new external_value(PARAM_TEXT)
                    )
                ),
                'moodle_courses' => new external_multiple_structure(
                    new external_single_structure(
                        array(
                            'course_id' => new external_value(PARAM_TEXT)
                        )
                    )
                )
            )
        );
    }

    /**
     * Returns success or failure
     *
     * @return bool success or failure
     */
    public static function process_magento_request($order_number, $customer, $moodle_courses) {
        global $USER, $DB;

        if (get_config('magentoconnector', 'magentoconnectorenabled') == 0) {
            return false;
        }

        $params = self::validate_parameters(self::process_magento_request_parameters(), array(
            'order_number' => $order_number, 'customer' => $customer, 'moodle_courses' => $moodle_courses));

        $context = context_user::instance($USER->id);
        self::validate_context($context);

        if (!$user = $DB->get_record('user', array('email' => $customer['email']))) {

            $user = new stdClass();
            $user->firstname    = $customer['firstname'];
            $user->lastname     = $customer['lastname'];
            $user->email        = $customer['email'];
            $user->city         = $customer['city'];
            $user->country      = $customer['country'];
            $user->confirmed    = 1;
            $user->policyagreed = 1;
            $user->mnethostid   = 1;
            $user->username     = local_magentoconnector_generate_username($customer['firstname'], $customer['lastname']);
            $user->timecreated  = time();
            $password           = generate_password();
            $user->password     = hash_internal_user_password($password);
            $userid = $DB->insert_record('user', $user);
        } else {

            $userid = $user->id;
        }

        $roleid = $DB->get_field('role', 'id', array('shortname' => LOCAL_MAGENTOCONNECTOR_STUDENT_SHORTNAME));

        $enrol = enrol_get_plugin('magento');

        foreach ($moodle_courses as $moodle_course) {
            if ($course = $DB->get_record('course', array('idnumber' => $moodle_course['course_id']))) {
                $enrolinstance = $DB->get_record('enrol', array('courseid' => $course->id,'enrol' => 'magento'), '*', MUST_EXIST);
                $enrol->enrol_user($enrolinstance, $userid, $roleid);
                $record = new stdClass();
                $record->userid = $userid;
                $record->ordernum = $order_number;
                $record->courseid = $course->id;
                $record->timestamp = time();
                $DB->insert_record('local_magentoconnector_trans', $record);
            } else {
                // no such course ... ?
            }
        }

        if (isset($password)) {
            $enrolinstance->newusername = $user->username;
            $enrolinstance->newaccountpassword = $password;
        }

        $customer = $DB->get_record('user', array('id' => $userid));
        $enrol->email_welcome_message($enrolinstance, $customer);

        return true;
    }

    /**
     * Returns description of method result value
     *
     * @return external_description
     */
    public static function process_magento_request_returns() {

        return new external_value(PARAM_BOOL);
    }
}
