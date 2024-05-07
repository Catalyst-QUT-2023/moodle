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



require_once($CFG->libdir.'/filelib.php');

class unfurl {
    public $title = '';
    public $sitename = '';
    public $image = '';
    public $description = '';
    public $canonicalurl = '';
    public $type = '';
    public $noogmetadata = true;
    private $response;

    public function __construct($url) {

        // Initialize cURL session
        $curl = new curl();
        $options = array(
            'CURLOPT_RETURNTRANSFER' => true,
            'CURLOPT_TIMEOUT' => 5
        );
        $this->response = $curl->get($url, null, $options);

        $curlresponse = $this->response;

        $error_no = $curl->get_errno();
        if ($error_no === CURLE_OPERATION_TIMEOUTED) {
            echo "Timeout occurred while fetching URL: $url"; 
            return;
        }
        
        $this->extract_html_metadata($url,$curlresponse);

    
        
    }

    public function extract_html_metadata($url, $responseurl){
        $doc = new DOMDocument();
        @$doc->loadHTML('<?xml encoding="UTF-8">' . $responseurl);
        $metataglist = $doc->getElementsByTagName('meta');

        //set default values
        //default html title
        $titleElement = $doc->getElementsByTagName('title')->item(0);
        $h1Element = $doc->getElementsByTagName('h1')->item(0);
        $h2Element = $doc->getElementsByTagName('h2')->item(0);

        if ($titleElement) {
            $this->title = $titleElement->textContent;
        } elseif ($h1Element) {
            $this->title = $h1Element->textContent;
        } elseif ($h2Element) {
            $this->title = $h2Element->textContent;
        }

        //iterate through meta tags
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
            $contentattribute = $metatag->getAttribute('content');
            if (
                !empty($propertyattribute) &&
                !empty($contentattribute) &&
                preg_match ('/^og:\w/i', $propertyattribute) === 1
            ) {
                $sanitizedcontent = clean_param($contentattribute, PARAM_TEXT);
                
                switch ($propertyattribute) {
                    case 'og:title':
                        $this->title = $sanitizedcontent;
                        break;
                    case 'og:site_name':
                        $this->sitename = $sanitizedcontent;
                        break;
                    case 'og:image':
                        $imageurlparts = parse_url($contentattribute);
                        // Some websites only give the path.
                        if (empty($imageurlparts['host']) && !empty($imageurlparts['path'])) {
                            $urlparts = parse_url($url);
                            $this->image = $urlparts['scheme'].'://'.$urlparts['host'].$imageurlparts['path'];
                        } else {
                            $sanitizedcontent = clean_param($contentattribute, PARAM_URL);
                            $this->image = $sanitizedcontent;
                        }
                        break;
                    case 'og:description':
                        $this->description = $sanitizedcontent;
                        break;
                    case 'og:url':
                        $sanitizedcontent = clean_param($contentattribute, PARAM_URL);
                        $this->canonicalurl = $sanitizedcontent;
                        break;
                    case 'og:type':
                        $sanitizedcontent = clean_param($contentattribute, PARAM_ALPHANUMEXT);
                        $this->type = $sanitizedcontent;
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

        // Use the render_from_template method to render Mustache template.
        return $OUTPUT->render_from_template('tool_urlpreview/metadata', $unfurldata);
    }
    public static function formatPreviewData($data)
    {
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
