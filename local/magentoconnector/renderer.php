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

require_once($CFG->libdir.'/tablelib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

class local_magentoconnector_renderer extends core_renderer {

    public function list_transactions($transactions) {
        global $CFG;

        $table = new flexible_table('local-magentoconnector-transaction-list');
        $table->define_columns(array('user', 'course', 'transactionid', 'timestamp'));

        $table->define_headers(array(
            get_string('user'),
            get_string('course'),
            get_string('transactionid', 'local_magentoconnector'),
            get_string('timestamp', 'local_magentoconnector')
        ));
        $table->define_baseurl(new moodle_url('/local/magentoconnector/viewtransactions.php'));

        $table->sortable(false);
        $table->collapsible(false);

        $table->column_class('user', 'user');
        $table->column_class('course', 'course');
        $table->column_class('transactionid', 'transactionid');
        $table->column_class('timestamp', 'timestamp');

        $table->set_attribute('cellspacing', '0');
        $table->set_attribute('id', 'local-magentoconnector-transaction-list');
        $table->set_attribute('class', 'local-magentoconnector-transaction-list generaltable');
        $table->set_attribute('width', '100%');
        $table->setup();

        if ($transactions) {

            $user = new stdClass();
            foreach ($transactions as $transaction) {

                $user->id                = $transaction->userid;
                $user->firstname         = $transaction->firstname;
                $user->lastname          = $transaction->lastname;
                $user->firstnamephonetic = $transaction->firstnamephonetic;
                $user->lastnamephonetic  = $transaction->lastnamephonetic;
                $user->middlename        = $transaction->middlename;
                $user->alternatename     = $transaction->alternatename;

                $row = array();

                $userurl = new moodle_url($CFG->wwwroot . '/user/profile.php', array('id' => $user->id));
                $row[] = html_writer::link($userurl, fullname($user), array('title' => get_string('viewprofile')));

                $courseurl = new moodle_url($CFG->wwwroot . '/course/view.php', array('id' => $transaction->courseid));
                $row[] = html_writer::link($courseurl, $transaction->course, array('title' => $transaction->course));

                $row[] = $transaction->ordernum;
                $row[] = userdate($transaction->timestamp, get_string('strftimedatetime'));

                $table->add_data($row);
            }
        }

        $table->print_html();
    }
}
