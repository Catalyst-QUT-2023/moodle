<?php

namespace core\url;
global $CFG;
use core\url\unfurl;
require_once($CFG->libdir . '/classes/url/unfurler.php');
/**
 * Description of the UnfurlerTest class.
 */
class htmlextract_test extends \advanced_testcase
{
    private $unfurler;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unfurler = new \unfurl('http://example.com'); // Pass a URL to the constructor
    }

    /**
     * @dataProvider provideTestFiles
     */
    public function testExtractHtmlMetadata($file, $expectedTitle, $expectedSiteName, $expectedImage, $expectedDescription, $expectedCanonicalUrl, $expectedType)
    {
        $responseurl = file_get_contents(__DIR__ . "/fixtures/$file");

        $this->unfurler->extract_html_metadata('http://example.com', $responseurl); // Pass the URL to the method

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
            [
                '404.html',
                '',
                'Error 404 (Not Found)!!1',
                '', // No site name in this example
                '', // No image in this example
                '', // No description in this example
                '', // No canonical URL in this example
            ],
            [
                'ABC_News.html',
                'Brisbane Lions through to AFL grand final after beating Carlton by 16 points',
                '', // should return site name
                'https://live-production.wcms.abc-cdn.net.au/22a05500d5223e217660d3917dff8539?impolicy=wcms_watermark_news&cropH=2813&cropW=5000&xPos=0&yPos=260&width=862&height=485&imformat=generic', // Image URL
                'The Brisbane Lions are into the AFL grand final for the first time in 19 years after a 16-point win over Carlton in their preliminary final.', // Description
                'https://www.abc.net.au/news/2023-09-23/afl-preliminary-final-brisbane-vs-carlton/102875098', // Canonical URL
                'article' // Type
            ],
            [
                'Australian_Gov.html',
                '', // Title is not available or relevant in this case
                '', // Site name is not available or relevant in this case
                '', // Image URL is not available or relevant in this case
                '', // Description is not available or relevant in this case
                '', // Canonical URL is not available or relevant in this case
                '', // Type is not available or relevant in this case
            ],
            [
                'BBC_News.html',
                'Ukraine war: US to give Kyiv long-range ATACMS missiles - media reports',
                'BBC News',
                'https://ichef.bbci.co.uk/news/1024/branded_news/17023/production/_131234249_7028f59ebcb28c2b4b9f7531633b715167e77c3f-1.jpg',
                'Kyiv has long been pushing for ATACMS missiles capable of hitting far behind the front line.',
                'https://www.bbc.com/news/world-us-canada-66898029',
                'article',
            ],
            [
                'Drive.html',
                'Tesla Model 3 2024 Reviews, News, Specs & Prices - Drive',
                'Drive',
                'https://images.drive.com.au/driveau/image/upload/c_fill,f_auto,g_auto,h_675,q_auto:good,w_1200/cms/uploads/f1kan8igunrsx7rcr6fn',
                'Research 2024 Tesla Model 3 models with independent reviews, comparisons, news and deals. Find new, demo and used Tesla Model 3 cars for sale in your region.',
                'https://www.drive.com.au/showrooms/tesla/model-3/',
                'WebPage',
            ],
            [
                'Facebook.html',
                'Moodle - Home | Facebook',
                'Facebook',
                'https://scontent.fbne3-1.fna.fbcdn.net/v/t39.30808-1/311984243_476640631157682_7783140295804704427_n.png?_nc_cat=104&ccb=1-7&_nc_sid=5f2048&_nc_ohc=onUWmOXDdFcAb4mpkJX&_nc_oc=AdgdplNTIs3KR2JKKVAJNl14Um0WEdrmMPoavT-5NeNkSiY-3Y2CU1ax_PKYvrBqmV4&_nc_ht=scontent.fbne3-1.fna&oh=00_AfCeV5QOVnAvJYYR0PezW1D7XIOZsy1JQmX597fZ2eXuXw&oe=66239BB4',
                'Moodle. 100,561 likes · 75 talking about this. Moodle is the world’s most customisable and trusted online learning solution including Moodle LMS.',
                'https://www.facebook.com/moodle',
                'WebPage'
            ],
            [
                "flickr.html",
                "Flickr - Home",
                "Flickr",
                "https://www.flickr.com/",
                "Flickr is a popular image hosting and video hosting website.",
                "https://www.flickr.com/",
                "WebPage"
            ],
            [
                "GitHub.html",
                "GitHub - Catalyst-QUT-2023/moodle",
                "GitHub",
                "https://github.com/Catalyst-QUT-2023/moodle",
                "Contribute to Catalyst-QUT-2023/moodle development by creating an account on GitHub.",
                "https://github.com/Catalyst-QUT-2023/moodle",
                "object"
            ],
            [
                "Guardian.html",
                "Voice referendum 2023 poll tracker: latest results of opinion polling on support for yes and no campaign | Australia news | The Guardian",
                "The Guardian",
                "https://www.theguardian.com/news/datablog/ng-interactive/2023/oct/13/indigenous-voice-to-parliament-referendum-2023-poll-results-polling-latest-opinion-polls-yes-no-campaign-newspoll-essential-yougov-news-by-state-australia",
                "What a poll of the national opinion polls on the Indigenous voice referendum tells us – and how support or opposition in Australia is changing over time",
                "https://www.theguardian.com/news/datablog/ng-interactive/2023/oct/13/indigenous-voice-to-parliament-referendum-2023-poll-results-polling-latest-opinion-polls-yes-no-campaign-newspoll-essential-yougov-news-by-state-australia",
                "object"
            ],
            [
                "HTTP_Site.html",
                "Australia's official weather forecasts & weather radar - Bureau of Meteorology",
                "Bureau of Meteorology",
                "http://www.bom.gov.au/",
                "Bureau of Meteorology web homepage provides the Australian community with access to weather forecasts, severe weather warnings, observations, flood information, marine and high seas forecasts and climate information. Products include weather charts, satellite photos, radar pictures and climate maps. The Bureau also has responsibility for compiling and providing comprehensive water information across Australia.",
                "http://www.bom.gov.au/",
                "WebPage"
            ],
            [
                "Imgur.html",
                "Arp 142: The Hummingbird Galaxy - Album on Imgur",
                "Imgur",
                "https://imgur.com/",
                "Discover topics like science, astrophotography, nasa, space, and the magic of the internet at Imgur, a community powered entertainment destination. Lift your spirits with funny jokes, trending memes, entertaining gifs, inspiring stories, viral videos, and so much more from users like 16bitStarbuck.",
                "https://imgur.com/",
                "WebPage"
            ],
            [
                "linkedin.html",
                "https://www.linkedin.com/feed/update/urn:li:activity:6925169086444318720/",
                "LinkedIn",
                "https://www.linkedin.com/",
                "No description available",
                "https://www.linkedin.com/",
                "WebPage"
            ],
            [
                "Medium.html",
                "https://medium.com/@moodle",
                "Moodle – Medium",
                "https://medium.com/",
                "Read writing from Moodle on Medium. The world’s most customisable and trusted online learning solution. Moodle LMS is used by hundreds of millions of users.",
                "https://medium.com/",
                "ProfilePage"
            ],
            [
                "Moodle_Docs.html",
                "https://docs.moodle.org/en/Course_homepage",
                "Course homepage - MoodleDocs",
                "https://docs.moodle.org/",
                "",
                "https://docs.moodle.org/en/Course_homepage",
                "ArticlePage"
            ],
            [
                "New_York_Times.html",
                "https://www.nytimes.com/international/",
                "The New York Times International - Breaking News, US News, World News, Videos",
                "https://www.nytimes.com/international/",
                "https://www.nytimes.com/international/",
                "ArticlePage"
            ],
            [
                "Reddit_Community.html",
                "https://www.reddit.com/",
                "Reddit - Dive into anything",
                "https://www.reddit.com/",
                "https://www.reddit.com/",
                "HomePage"
            ],
            [
                "Reddit_Post.html",
                "https://www.reddit.com/",
                "Reddit - Dive into anything",
                "https://www.reddit.com/",
                "https://www.reddit.com/",
                "PostPage"
            ],
            [
                "Rolling_Stone_Article.html",
                "https://www.rollingstone.com/",
                "RFK Jr. Questions 9/11 Narrative on Peter Bergen Podcast",
                "https://www.rollingstone.com/",
                "https://www.rollingstone.com/",
                "ArticlePage"
            ],
            [
                "Stack_Overflow.html",
                "https://stackoverflow.com/",
                "Upgrade Moodle 2.5 to Moodle 3.3 - Stack Overflow",
                "https://stackoverflow.com/questions/45081210/upgrade-moodle-2-5-to-moodle-3-3",
                "https://stackoverflow.com/",
                "QuestionPage"
            ],
            [
                "twitter.html",
                "https://twitter.com/",
                "https://twitter.com/moodle",
                "https://twitter.com/moodle",
                "https://twitter.com/",
                "ProfilePage"
            ],
            [
                "Vimeo.html",
                "https://vimeo.com/",
                "https://vimeo.com/783455878",
                "https://vimeo.com/",
                "VideoPage"
            ],
            [
                "Wikipedia.html",
                "Moodle - Wikipedia",
                "",
                "",
                "https://en.wikipedia.org/",
                "https://en.wikipedia.org/wiki/Moodle",
                "https://en.wikipedia.org/",
                "website"
            ],
            [
                "youtube.html",
                "2022 Moodle LMS video",
                "YouTube",
                "https://i.ytimg.com/vi/3ORsUGVNxGs/maxresdefault.jpg",
                "View our latest Moodle LMS release video: https://www.youtube.com/watch?v=DubiRbeDpnM",
                "https://www.youtube.com/watch?v=3ORsUGVNxGs",
                "https://www.youtube.com/",
                "video"
            ]
        ];
    }
}
