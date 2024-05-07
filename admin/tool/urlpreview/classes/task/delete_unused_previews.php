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
 * @package     tool_urlpreview
 * @copyright   2023 Thomas Daly <n11134551@qut.edu.au>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_urlpreview\task;

use core\task\scheduled_task;
use tool_urlpreview\urlpreview;


defined('MOODLE_INTERNAL') || die();

class delete_unused_previews extends scheduled_task {

    public function get_name() {
        return get_string('deleteunusedpreviews', 'tool_urlpreview');
    }

    public function execute() {
        global $DB;
        $threemonthsago = time() - (90 * DAYSECS);
        $DB->delete_records_select('urlpreview', 'lastpreviewed < ?', [$threemonthsago]);
    }
}
