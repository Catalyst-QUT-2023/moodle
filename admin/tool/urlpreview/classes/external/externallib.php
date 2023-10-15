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

use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_multiple_structure;
use core_external\external_value;

class tool_urlpreview_external extends core_external\external_api {

    /**
     * @return external_fucntion_parameters
     */
    public static function get_url_data_parameters(): external_function_parameters {
        return new external_function_parameters(
           array('url' => new external_value(PARAM_URL, 'The URL to get data'))
           );
    }


    public static function get_url_data($url) {
        global $DB;
        // Validate the URL.
        self::validate_context($url);
        // Scrape data.
        $scrapeddata = self::scrape_url($url);
        // Store the scraped data into the database.
        $DB->insert_record('urlpreview', $scrapeddata);

        return $scrapeddata;
    }

    public static function scrape_url($url) {
        require_once('../../../../../lib/classes/url/unfurler.php');

        $unfurler = new unfurl($url);
        $scrapeddata = [
            'url' => $url,
            'title' => $unfurler->title,
            'sitename' => $unfurler->sitename,
            'image' => $unfurler->image,
            'description' => $unfurler->type
        ];
        return $scrapeddata;
    }

    /**
     * @return external_multiple_structure
     */
    public static function get_url_data_returns() {
        return new external_multiple_structure(
            new external_single_structure([
                'url' => new external_value(PARAM_URL, 'The URL'),
                'title' => new external_value(PARAM_TEXT, 'The title'),
                'sitename' => new external_value(PARAM_TEXT, 'Site Name'),
                'image' => new external_value(PARAM_TEXT, 'Image URL'),
                'description' => new external_value(PARAM_TEXT, 'Description'),
                'type' => new external_value(PARAM_TEXT, 'Type')
            ])

         );
    }



}

