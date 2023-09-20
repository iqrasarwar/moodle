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

namespace local_greetings\external;

defined('MOODLE_INTERNAL') || die;

require_once("{$CFG->libdir}/externallib.php");
require_once('../../config.php');

/**
 * Class delete_message
 *
 * @package    local_greetings
 * @copyright  2023 true
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class delete_message extends \external_api
{
    public static function execute_parameters(): \external_function_parameters
    {
        return new \external_function_parameters(
            ['id' => new \external_value(PARAM_INT, 'id of message')]
        );
    }

    public static function execute($id)
    {
        global $CFG, $DB;
        require_once("$CFG->dirroot/config.php");
        $params = self::validate_parameters(self::execute_parameters(), ['id' => $id]);
        $DB->delete_records('local_greetings_messages', $params);
        return $id;
    }

    public static function execute_returns(): \external_value
    {
        return new \external_value(PARAM_INT, 'id of message');
    }
}
