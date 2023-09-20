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
 * TODO describe file delete_multiple
 *
 * @package    local_greetings
 * @copyright  2023 YOUR NAME <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once('../../config.php');
require_once($CFG->dirroot . '/local/greetings/lib.php');

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/greetings/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('bulkdeleteheading', 'local_greetings'));

require_login();

if (isguestuser()) {
    throw new moodle_exception('noguest');
}


$action = optional_param('action', '', PARAM_TEXT);

if ($action == 'del') {
    $unserializedIds = [];
    require_sesskey();
    $ids = optional_param('ids', null, PARAM_TEXT);

    if (!empty($ids) && is_string($ids)) {
        $unserializedIds = unserialize($ids);
        if (is_array($unserializedIds)) {
            foreach ($unserializedIds as $id) {
                $params = array('id' => $id);
                $DB->delete_records('local_greetings_messages', $params);
            }
        }
    }
    redirect(new moodle_url('/local/greetings/index.php'), count($unserializedIds) . " Messages Deleted", null, \core\output\notification::NOTIFY_SUCCESS);
}
echo $OUTPUT->header();

$bulk_delete = optional_param('bulk_delete', null, PARAM_INT);

if ($bulk_delete) {
    $messagesToDelete = [];
    $msg = optional_param_array('message', null, PARAM_INT);
    foreach ($msg as $id) {
        if ($result = $DB->get_record('local_greetings_messages', ['id' => $id])) {
            $messagesToDelete[] = $result->message;
        }
    }

    $templateData = [
        'messages' => $messagesToDelete,
        'deleteIcon' => $OUTPUT->pix_icon('t/delete', get_string('delete')),
        'deleteUrl' => new moodle_url(
            '/local/greetings/delete_multiple.php',
            ['action' => 'del', 'ids' => (serialize($msg)), 'sesskey' => sesskey()]
        ),
        "deleteText" => get_string('bulkdeleteheading', 'local_greetings')
    ];

    $content = $OUTPUT->render_from_template('local_greetings/delete_multiple', $templateData);

    echo $content;
}

echo $OUTPUT->footer();
