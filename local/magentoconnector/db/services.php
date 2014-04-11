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

$functions = array(
    'local_magentoconnector_process_request' => array(
        'classname'   => 'local_magentoconnector_external',
        'methodname'  => 'process_magento_request',
        'classpath'   => 'local/magentoconnector/externallib.php',
        'description' => 'Receives data from Magento MoodleConnector extension',
        'type'        => 'write',
        // 'capabilities'=> 'local/magentoconnector:processrequest'
    )
);

$services = array(
    'Magento connector' => array(
        'functions'       => array('local_magentoconnector_process_request'),
        'restrictedusers' => 0,
        'enabled'         => 1,
        'shortname'       => 'magentoconnector',
        'downloadfiles'   => 1
    )
);
