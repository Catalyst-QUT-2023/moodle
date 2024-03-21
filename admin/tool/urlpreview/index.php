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
use tool_urlpreview\form\urlpreview;

$url = optional_param('url', '', PARAM_URL);

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/admin/tool/urlpreview/index.php'));
$PAGE->set_pagelayout('standard');
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading(get_string('menuname', 'tool_urlpreview'));
$allowuse = has_capability('tool/urlpreview:usetool', $context);

require_login();
require_capability('tool/urlpreview:usetool', $context);
if (isguestuser()) {
    throw new moodle_exception('noguest');
}

echo $OUTPUT->header();

$templatedata = [
    'action' => 'index.php',
    'submittedUrl' => $url,
];

echo $OUTPUT->render_from_template('tool_urlpreview/form', $templatedata);

//Display output from AJAX Call
if ($url !== '') {
    $PAGE->requires->js_call_amd('tool_urlpreview/get_url_data', 'getPreviewTemplate', [
        $url
    ]);
}

// // Code adapted from Matthew Hilton:
// // Render a generic loading icon while waiting for ajax.
// $loadingstr = get_string('loading', '', $this->check->get_name());
// $loadingicon = $OUTPUT->pix_icon('i/loading', $loadingstr);

// // Wrap it in a notification so we reduce style changes when loading is finished.
// $output = $OUTPUT->notification($loadingicon . $loadingstr, \core\output\notification::NOTIFY_INFO, false);

// // Wrap in a div with a reference. The JS getAndRender will replace this with the response from the webservice.
// $statusdiv = \html_writer::div($output, '', ['data-check-reference' => $domref]);

echo $OUTPUT->footer();
