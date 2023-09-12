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
 * Receives Open Graph protocol metadata (a.k.a social media metadata) from link
 *
 * @package    core
 * @copyright  2021 Jon Green <jgreen01@stanford.edu>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unfurl {
    public $title = '';
    public $sitename = '';
    public $image = '';
    public $description = '';
    public $canonicalurl = '';
    public $noogmetadata = true;

    public function __construct($url) {
        $html = file_get_contents($url);

        $doc = new DOMDocument();
        @$doc->loadHTML('<?xml encoding="UTF-8">' . $html);
        $metataglist = $doc->getElementsByTagName('meta');

        foreach ($metataglist as $metatag) {
            $propertyattribute = strtolower(s($metatag->getAttribute('property')));
            if (
                !empty($propertyattribute) &&
                preg_match ('/^og:\w/i', $propertyattribute) === 1
            ) {
                $this->noogmetadata = false;
                break;
            }
        }

        if ($this->noogmetadata) {
            return;
        }

        foreach ($metataglist as $metatag) {
            $propertyattribute = strtolower(s($metatag->getAttribute('property')));
            $contentattribute = s($metatag->getAttribute('content'));
            if (
                !empty($propertyattribute) &&
                !empty($contentattribute) &&
                preg_match ('/^og:\w/i', $propertyattribute) === 1
            ) {
                switch ($propertyattribute) {
                    case 'og:title':
                        $this->title = $contentattribute;
                        break;
                    case 'og:site_name':
                        $this->sitename = $contentattribute;
                        break;
                    case 'og:image':
                        $imageurlparts = parse_url($contentattribute);
                        // Some websites only give the path.
                        if (empty($imageurlparts['host']) && !empty($imageurlparts['path'])) {
                            $urlparts = parse_url($url);
                            $this->image = $urlparts['scheme'].'://'.$urlparts['host'].$imageurlparts['path'];
                        } else {
                            $this->image = $contentattribute;
                        }
                        break;
                    case 'og:description':
                        $this->description = $contentattribute;
                        break;
                    case 'og:url':
                        $this->canonicalurl = $contentattribute;
                        break;
                    default:
                        break;
                }
            }
        }
    }
    public function render_unfurl_metadata() {
        global $OUTPUT;  // Use the global $OUTPUT variable, Moodle's core renderer.

        // Get the properties of this object as an array.
        $unfurldata = get_object_vars($this);

        // Use the render_from_template method to render your Mustache template.
        return $OUTPUT->render_from_template('../../../admin/tool/urlpreview/template/metadata', $unfurldata);
    }

}
