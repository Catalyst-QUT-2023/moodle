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

namespace core\url;

global $CFG;
use core\url\unfurl;

require_once ($CFG->libdir . '/classes/url/unfurler.php');
/**
 * URLPreview HTML Extract unit tests
 *
 * @package    tool_urlpreview
 * @copyright  2024 Thomas Daly <n11134551@qut.edu.au>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class htmlextract_test extends \advanced_testcase
{
    private $unfurler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unfurler = new \unfurl('http://example.xyz'); // Pass a URL to the constructor
    }

    /**
     * Unit test for \tool_urlpreview
     *
     * @dataProvider provideTestFiles
     * @param string $file name of the test fixture html file
     * @param string $expectedTitle expected title
     * @param string $expectedSiteName expected site name
     * @param string $expectedImage expected image
     * @param string $expectedDescription expected description
     * @param string $expectedCanonicalUrl expected canonical URL
     * @param string $expectedType expected type
     */
    public function testExtractHtmlMetadata($file, $expectedTitle, $expectedSiteName, $expectedImage, $expectedDescription, $expectedCanonicalUrl, $expectedType)
    {
        $responseurl = file_get_contents(__DIR__ . "/fixtures/$file");
        
        // Extract metadata from the HTML file
        $this->unfurler->extract_html_metadata('http://example.com', $responseurl); // Pass the URL to the method
        
        // Check the extracted metadata
        $this->assertEquals($expectedTitle, $this->unfurler->title);
        $this->assertEquals($expectedSiteName, $this->unfurler->sitename);
        $this->assertEquals($expectedImage, $this->unfurler->image);
        $this->assertEquals($expectedDescription, $this->unfurler->description);
        $this->assertEquals($expectedCanonicalUrl, $this->unfurler->canonicalurl);
        $this->assertEquals($expectedType, $this->unfurler->type);
    }
    public function provideTestFiles()
    {
        return [
            ['404.html', '404 Not Found', 'Example Site', 'https://example.com/image.jpg', 'Example description.', 'https://example.com', 'website'],
            ['missing_title.html', '', 'Example Site', 'https://example.com/image.jpg', 'Example description.', 'https://missingtitle.com', 'article'],
            ['missing_sitename.html', 'Missing Sitename', '', 'https://example.com/image.jpg', 'Example description.', 'https://example.com', 'website'],
            ['missing_image.html', 'Missing Image', 'Example Site', '', 'Example description.', 'https://example.com', 'website'],
            ['missing_description.html', 'Missing Description', 'Example Site', 'https://example.com/image.jpg', '', 'https://example.com', 'website'],
            ['missing_canonicalurl.html', 'Missing URL', 'Example Site', 'https://example.com/image.jpg', 'Example description.', '', 'website'],
            ['missing_type.html', 'Missing Type', 'Example Site', 'https://example.com/image.jpg', 'Example description.', 'https://example.com', ''],
        ];
    }
}
