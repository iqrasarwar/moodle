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
 * Main file to view greetings
 *
 * @package     local_greetings
 * @copyright   2022 Your name <your@email>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot . '/local/greetings/lib.php');

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/greetings/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('pluginname', 'local_greetings'));

require_login();

if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$allowpost = has_capability('local/greetings:postmessages', $context);
$deletepost = has_capability('local/greetings:deleteownmessage', $context);
$deleteanypost = has_capability('local/greetings:deleteanymessage', $context);

$action = optional_param('action', '', PARAM_TEXT);

if ($action == 'del') {
    require_sesskey();

    $id = required_param('id', PARAM_TEXT);

    if ($deleteanypost || $deletepost) {
        $params = array('id' => $id);

        // Users without permission should only delete their own post.
        if (!$deleteanypost) {
            $params += ['userid' => $USER->id];
        }

        $PAGE->requires->js_call_amd(
            'local_greetings/delete_ajax',
            'init',
            [[
                'id' => $id,
            ]]
        );
    }
}

if ($action == 'read') {
    $id = required_param('id', PARAM_TEXT);

    if (!$result = $DB->get_record('local_greetings_messages', ['id' => $id])) {
        throw new moodle_exception('norecordfound', 'local_greetings');
    }

    $result->unread = 0;

    $DB->update_record('local_greetings_messages', $result);

    redirect($PAGE->url);
}

$messageform = new \local_greetings\form\message_form();

if ($data = $messageform->get_data()) {
    require_capability('local/greetings:postmessages', $context);

    $message = required_param('message', PARAM_TEXT);

    if (!empty($message)) {
        $record = new stdClass;
        $record->message = $message;
        $record->timecreated = time();
        $record->userid = $USER->id;

        $DB->insert_record('local_greetings_messages', $record);

        redirect($PAGE->url, "Message " . $message . " Inserted", null, \core\output\notification::NOTIFY_SUCCESS);
    }
}

echo $OUTPUT->header();

if (isloggedin()) {
    echo local_greetings_get_greeting($USER);
} else {
    echo get_string('greetinguser', 'local_greetings');
}

if ($allowpost) {
    $messageform->display();
}

if (has_capability('local/greetings:viewmessages', $context)) {
    $userfields = \core_user\fields::for_name()->with_identity($context);
    $userfieldssql = $userfields->get_sql('u');

    $sql = "SELECT m.id, m.message, m.timecreated, m.userid, m.unread {$userfieldssql->selects}
            FROM {local_greetings_messages} m
            LEFT JOIN {user} u ON u.id = m.userid
            ORDER BY timecreated DESC";

    $messages = $DB->get_records_sql($sql);

    $unreadcardbackgroundcolor = get_config('local_greetings', 'messagecardbgcolor');
    $readcardbackgroundcolor = get_config('local_greetings', 'readmessagecardbgcolor');

    $templateData = [];

    foreach ($messages as $m) {
        $color = $m->unread == 1 ? $unreadcardbackgroundcolor : $readcardbackgroundcolor;
        $showMarkAsRead = $m->unread == 1;
        $messageData = [
            'bgcolor' => $color,
            'message' => format_text($m->message, FORMAT_PLAIN),
            'postedby' => get_string('postedby', 'block_greetings', $m->firstname),
            'timecreated' => userdate($m->timecreated),
            'deletePermission' => ($deleteanypost || ($deletepost && $m->userid == $USER->id)),
            'id' => $m->id,
            'editIcon' => $OUTPUT->pix_icon('i/edit', get_string('edit')),
            'deleteIcon' => $OUTPUT->pix_icon('t/delete', get_string('delete')),
            'readIcon' => $OUTPUT->pix_icon('e/spellcheck', get_string('readmessage', 'local_greetings')),
            'sesskey' => sesskey(),
            'editUrl' => new moodle_url(
                '/local/greetings/edit.php',
                ['id' => $m->id]
            ),
            'deleteUrl' => new moodle_url(
                '/local/greetings/index.php',
                ['action' => 'del', 'id' => $m->id, 'sesskey' => sesskey()]
            ),
            'readUrl' => new moodle_url(
                '/local/greetings/index.php',
                ['action' => 'read', 'id' => $m->id]
            ),
            'showMarkAsRead' => $showMarkAsRead,
        ];

        $templateData[] = $messageData;
    }
    $content = $OUTPUT->render_from_template('local_greetings/container', [
        'messages' => $templateData,
    ]);

    echo $content;
}

echo $OUTPUT->footer();
