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

defined('MOODLE_INTERNAL') || die;

$systemcontext = context_system::instance();

if (has_any_capability(array('local/magentoconnector:manage', 'local/magentoconnector:viewtransactions'), $systemcontext)) {
    $ADMIN->add('localplugins', new admin_category('magentoconnector', new lang_string('pluginname','local_magentoconnector')));
}

if (has_capability('local/magentoconnector:manage', $systemcontext)) {
    $settings = new admin_settingpage('magentoconnectorsettings', new lang_string('settings'));
    $options = array(1 => get_string('yes'), 0 => get_string('no'));
    $settings->add(new admin_setting_configselect('magentoconnector/magentoconnectorenabled',
            get_string('enabled', 'local_magentoconnector'),
            get_string('enableordisable', 'local_magentoconnector'), 1, $options));
    $ADMIN->add('magentoconnector', $settings);

}

if (has_capability('local/magentoconnector:viewtransactions', $systemcontext)) {
    $ADMIN->add('magentoconnector', new admin_externalpage('magentoconnectortransactions', get_string('transactions', 'local_magentoconnector'),
        $CFG->wwwroot . '/local/magentoconnector/viewtransactions.php'));
}
