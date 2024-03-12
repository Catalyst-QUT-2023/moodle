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

//replace the following with web service that is called via ajax call
echo $OUTPUT->render_from_template('tool_urlpreview/form', $templatedata);

if ($url !== '') {
    // Check if the linted data for this URL is already in the database.
    $linteddata = urlpreview::get_record(['url' => $url]);

    if (!$linteddata) {
        // If not in the database, lint the URL.
        $unfurler = new unfurl($url);
        $renderedoutput = $unfurler->render_unfurl_metadata();

        // Save the linted data to the database using the persistent class.
        $record = new urlpreview();
        $record->set('url', $url);
        $record->set('title', $unfurler->title);
        $record->set('type', $unfurler->type);
        $record->set('imageurl', $unfurler->image);
        $record->set('sitename', $unfurler->sitename);
        $record->set('description', $unfurler->description);
        $record->set('timecreated', time());
        $record->set('timemodified', time());
        $record->set('lastpreviewed', time());
        $record->create();
    } else {
        // Update the 'lastpreviewed' timestamp only if it's been more than an hour.
        $currenttime = time();
        if (($currenttime - $linteddata->get('lastpreviewed')) > 3600) { // 3600 seconds = 1 hour
            $linteddata->set('lastpreviewed', $currenttime);
            $linteddata->update();
        }
        $renderedoutput = rend($linteddata->to_record());
    }

    echo $renderedoutput;
}

echo $OUTPUT->footer();

/**
 * Renders linted data from the database for display.
 *
 * @param stdClass $data The linted data retrieved from the database.
 * @return string The formatted output for display.
 */
function rend($data) {
    global $OUTPUT;

    $templatedata = [
        'noogmetadata' => empty($data->title) && empty($data->imageurl) && empty($data->sitename)
        && empty($data->description) && empty($data->type),
        'canonicalurl' => $data->url,
        'title'        => $data->title,
        'image'        => $data->imageurl,
        'sitename'     => $data->sitename,
        'description'  => $data->description,
        'type'         => $data->type,
    ];
    return $OUTPUT->render_from_template('tool_urlpreview/metadata', $templatedata);
}
