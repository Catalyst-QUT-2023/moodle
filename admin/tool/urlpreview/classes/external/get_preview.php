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

namespace tool_urlpreview\external;

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_api;
use core_external\external_value;

require_once('../../../../../config.php');
require_once('../../../../../lib/classes/url/unfurler.php');

/**
 * Implementation of web service tool_urlpreview_get_preview
 *
 * @package    tool_urlpreview
 * @copyright  2024 Team The Z <your@email.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class get_preview extends external_api {

    /**
     * Describes the parameters for tool_urlpreview_get_preview
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            array('url' => new external_value(PARAM_URL, 'The URL to get data'))
            );
    }

    private static function scrape_url($url) {
        require_once('../../../../../lib/classes/url/unfurler.php');

        $unfurler = new unfurl($url);
        $scrapeddata = [
            'url' => $url,
            'title' => $unfurler->title,
            'sitename' => $unfurler->sitename,
            'image' => $unfurler->image,
            'description' => $unfurler->type,
        ];
        return $scrapeddata;
    }


    /**
     * Implementation of web service tool_urlpreview_get_preview
     *
     * @param string $url
     */
    public static function execute($url) {
        // Parameter validation.
        ['url' => $url] = self::validate_parameters(
            self::execute_parameters(),
            ['url' => $url]
        );

        // From web services we don't call require_login(), but rather validate_context.
        $context = \context_system::instance(); // TODO change if required.
        self::validate_context($context);

        // TODO check permissions and implement WS.

        global $DB;
        // Validate the URL.
        self::validate_context($url);

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

        return $renderedoutput;
    }

    /**
     * Describe the return structure for tool_urlpreview_get_preview
     *
     * @return external_multiple_structure
     */
    public static function execute_returns(): external_multiple_structure {
        return new external_multiple_structure(
            new external_single_structure([
                'url' => new external_value(PARAM_URL, 'The URL'),
                'title' => new external_value(PARAM_TEXT, 'The title'),
                'sitename' => new external_value(PARAM_TEXT, 'Site Name'),
                'image' => new external_value(PARAM_TEXT, 'Image URL'),
                'description' => new external_value(PARAM_TEXT, 'Description'),
                'type' => new external_value(PARAM_TEXT, 'Type'),
            ])
         );
    }

    /**
     * Renders linted data from the database for display.
     *
     * @param stdClass $data The linted data retrieved from the database.
     * @return string The formatted output for display.
     */
    private static function rend($data) {
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
}


