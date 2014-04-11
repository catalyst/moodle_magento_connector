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

define('LOCAL_MAGENTOCONNECTOR_STUDENT_SHORTNAME', 'student');

/**
 * Generates a unique username from the passed firstname and lastname
 *
 * @param string $firstname Passed user firstname
 * @param string $lastname Passed user lastname
 * @return $string
 */
function local_magentoconnector_generate_username($firstname, $lastname) {
    global $DB;

    $username = strtolower($firstname . substr($lastname, 0, 1));

    $i = 1;
    if ($DB->record_exists('user', array('username' => $username))) {
        $username .= $i;
        while ($DB->record_exists('user', array('username' => $username))) {
            $username = substr($username, 0, -1 * strlen("$i")) . $i;
            $i++;
        }
    }

    return $username;
}

/**
 * Returns details from the Magento transactions table
 *
 * @return array
 */
function local_magentoconnector_get_transactions() {
    global $DB;

    $sql = "SELECT t.id, u.id AS userid, u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename,
                   u.firstname, u.lastname, c.id AS courseid, c.fullname AS course, t.ordernum, t.timestamp
              FROM {local_magentoconnector_trans} t
              JOIN {user} u ON u.id = t.userid
              JOIN {course} c ON c.id = t.courseid
          ORDER BY id DESC";

    return $DB->get_records_sql($sql);
}
