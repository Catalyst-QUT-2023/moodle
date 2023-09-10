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
 * @package     tool_urlpreview
 * @copyright   2023 Emily Lim <n10882243@qut.edu.au>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_urlpreview\url;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/url/unfurler.php');
// use unfurl;

class preview_url extends \unfurl
{
    /**
     * Define the form.
     */
    public function definition()
    {
        $url = 'https://moodle.academy/mod/book/view.php?id=895&chapterid=699';

        $customObject = new preview_url($url);

        echo $customObject;
        // $mform = $this->_form; // Don't forget the underscore!

        // $mform->addElement('textarea', 'message', get_string('yourmessage', 'tool_urlpreview')); // Add elements to your form.
        // $mform->setType('message', PARAM_TEXT); // Set type of element.

        // $submitlabel = get_string('submit');
        // $mform->addElement('submit', 'submitmessage', $submitlabel);
    }
}
