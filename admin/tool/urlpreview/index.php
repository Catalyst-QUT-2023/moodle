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
 * Plugin version and other meta-data are defined here.
 *
 * @package     tool_urlpreview
 * @copyright   2023 Hanbin Lee <n10324402@qut.edu.au>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once('../../../lib/classes/url/unfurler.php');
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/urlpreview/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('pluginname', 'tool_urlpreview'));

require_login();
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

// if ($action == 'del') {
//     $id = required_param('id', PARAM_TEXT);

//     $DB->delete_records('tool_urlpreview_messages', array('id' => $id));
// }


//$allowpost = has_capability('tool/urlpreview:postmessages', $context);
//$deleteanypost = has_capability('tool/urlpreview:deleteanymessage', $context);
//$action = optional_param('action', '', PARAM_TEXT);

// if ($action == 'del') {
//     require_capability('tool/urlpreview:deleteanymessage', $context);
//     $id = required_param('id', PARAM_TEXT);
//     $DB->delete_records('tool_urlpreview_messages', array('id' => $id));
// }



$messageform = new \tool_urlpreview\form\message_form();


echo $OUTPUT->header();
if (isloggedin()) {
    echo '<h3>Welcome, ' . fullname($USER) . '</h3>';
}

$messageform->display();

if ($data = $messageform->get_data()) {
    //require_capability('tool/urlpreview:postmessages', $context);


    // Fetch the URL and get its metadata
    $url = $data->url;
    // Assuming the unfurl_store class is available
    $unfurler = new unfurl($url);  
    
    var_dump($unfurler);

}

// $userfields = \core_user\fields::for_name()->with_identity($context);
// $userfieldssql = $userfields->get_sql('u');
// $sql = "SELECT m.id, m.message, m.timecreated, m.userid {$userfieldssql->selects}
//        FROM {tool_urlpreview_messages} m
//   LEFT JOIN {user} u ON u.id = m.userid
//    ORDER BY timecreated DESC";

// $messages = $DB->get_records_sql($sql);

echo $OUTPUT->box_start('card-columns');

// foreach ($messages as $m) {
//     echo html_writer::start_tag('div', array('class' => 'card'));
//     echo html_writer::start_tag('div', array('class' => 'card-body'));
//     echo html_writer::tag('p', format_text($m->message, FORMAT_PLAIN), array('class' => 'card-text'));
//     echo html_writer::tag('p', get_string('postedby', 'tool_urlpreview', $m->firstname), array('class' => 'card-text'));
//     echo html_writer::start_tag('p', array('class' => 'card-text'));
//     echo html_writer::tag('small', userdate($m->timecreated), array('class' => 'text-muted'));
//     echo html_writer::end_tag('p');
//     echo html_writer::end_tag('div');
//     echo html_writer::end_tag('div');
//     if ($deleteanypost) {
//         echo html_writer::start_tag('p', array('class' => 'card-footer text-center'));
//         echo html_writer::link(
//          new moodle_url(
//              'index.php',
//              array('action' => 'del', 'id' => $m->id)
//          ),
//         $OUTPUT->pix_icon('t/delete', '') . get_string('delete')
//         );
//         echo html_writer::end_tag('p');
//     }
// }
echo $OUTPUT->box_end();
echo $OUTPUT->footer();

