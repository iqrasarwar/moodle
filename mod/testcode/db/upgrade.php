<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin upgrade steps are defined here.
 *
 * @package     mod_testcode
 * @category    upgrade
 * @copyright   2023 iqra <iqra.sarwar@arbisoft.com>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Execute mod_testcode upgrade from the given old version.
 *
 * @param int $oldversion
 * @return bool
 */
function xmldb_testcode_upgrade($oldversion)
{
    global $DB;

    $dbman = $DB->get_manager();

    if ($oldversion < 2023091404) {

        // Define field message to be added to testcode.
        $table = new xmldb_table('testcode');
        $field = new xmldb_field('message', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null, 'introformat');

        // Conditionally launch add field message.
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Testcode savepoint reached.
        upgrade_mod_savepoint(true, 2023091404, 'testcode');
    }


    return true;
}
