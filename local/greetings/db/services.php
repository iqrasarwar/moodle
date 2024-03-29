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
 * External functions and service declaration for Greetings
 *
 * Documentation: {@link https://moodledev.io/docs/apis/subsystems/external/description}
 *
 * @package    local_greetings
 * @category   webservice
 * @copyright  2023 true
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    // The name of your web service function, as discussed above.
    'local_greetings_delete_message' => [
        // The name of the namespaced class that the function is located in.
        'classname'   => 'local_greetings\external\delete_message',

        // A brief, human-readable, description of the web service function.
        'description' => 'Delete a message',

        // Options include read, and write.
        'type'        => 'write',

        // Whether the service is available for use in AJAX calls from the web.
        'ajax'        => true,
    ]
];


$services = [];
