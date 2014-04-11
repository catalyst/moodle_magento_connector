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
 * Strings for component 'enrol_magento', language 'en'.
 *
 * @package    enrol
 * @subpackage magento
 * @author     Edwin Phillips <edwin.phillips@catalyst-eu.net>
 * @copyright  Catalyst IT Ltd 2014 <http://catalyst-eu.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'Magento enrolments';
$string['pluginname_desc'] = 'The Magento enrolment plugin enables enrolments via the Magento connector web service.';
$string['status'] = 'Allow Magento enrolments';
$string['status_desc'] = 'Allow Magento to enrol users into courses by default.';
$string['status_help'] = 'This setting determines whether Magento can enrol users onto the course.';
$string['customwelcomemessage'] = 'Custom welcome email';
$string['customwelcomemessage_help'] = 'A custom welcome email may be added as plain text or Moodle-auto format, including HTML tags and multi-lang tags.

The following placeholders may be included in the message:

* Course name {$a->coursename}
* Link to course {$a->courseurl}
* Link to user\'s profile page {$a->profileurl}
* Link to forgotten password page {$a->forgottenpasswordurl}

If the user is newly created by Moodle as well as enrolled in this course, their new username and password details will be automatically appended to the email.

If no custom message is provided then a default one will be used which contains links to the course and to the users\'s profile page.';
$string['welcometocourse'] = 'Welcome to {$a}';
$string['welcometocoursetext'] = 'Welcome to {$a->coursename}!
Please use the following URL to access this course:

  {$a->courseurl}

If you have not done so already, you should edit your profile page so that we can learn more about you:

  {$a->profileurl}';
$string['welcometocoursetexthtml'] = 'Welcome to {$a->coursename}!<br/>
<br/>
Please use the following URL to access this course:<br/>
<br/>
  <a href="{$a->courseurl}">{$a->courseurl}</a><br/>
<br/>
If you have not done so already, you should edit your profile page so that we can learn more about you:<br/>
<br/>
  <a href="{$a->courseurl}">{$a->profileurl}</a><br/>';
$string['newcredentials'] = '
Your account has been created, please use the following details to log in:

Username: {$a->username}
Password: {$a->password}
';
$string['newcredentialshtml'] = '<br/>
Your account has been created, please use the following details to log in:<br/>
<br/>
Username: {$a->username}<br/>
Password: {$a->password}<br/>
<br/>';
$string['magento:config'] = 'Edit Magento enrolment instances';
$string['magento:manage'] = 'Edit user enrolments made by Magento';
$string['magento:unenrol'] = 'Unenrol users enrolled by Magento';

$string['existinguser'] = '
You can use your existing account to access this course, if you have forgotten your details please use the following URL to retrieve them:
    
{$a->forgottenpasswordurl}
';
$string['existinguserhtml'] = '<br/>
You can use your existing account to access this course, if you have forgotten your details please use the following URL to retrieve them:<br/>
<br/>
{$a->forgottenpasswordurl}<br/>
<br/>';
