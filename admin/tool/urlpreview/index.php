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
$PAGE->set_heading(get_string('menuname', 'tool_urlpreview'));
$PAGE->requires->css('/admin/tool/urlpreview/style.css');


require_login();
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

$messageform = new \tool_urlpreview\form\message_form();


echo $OUTPUT->header();
if (isloggedin()) {
    echo '<h3>Welcome, ' . fullname($USER) . '</h3>';
}

$messageform->display();

if ($data = $messageform->get_data()) {

    // Fetch the URL and get its metadata.
    $url = $data->url;
    // Assuming the unfurl_store class is available.
    $unfurler = new unfurl($url);

    $renderedoutput = $unfurler->render_unfurl_metadata($url);

    echo $renderedoutput;

}

echo $OUTPUT->box_start('card-columns');

echo $OUTPUT->box_end();
echo $OUTPUT->footer();

