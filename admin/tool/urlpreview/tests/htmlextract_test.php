<?php

namespace core\url;

global $CFG;
use core\url\unfurl;

require_once ($CFG->libdir . '/classes/url/unfurler.php');
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
                'Error 404 (Not Found)!!1',
                '',
                '', // No site name in this example
                '', // No image in this example
                '', // No description in this example
                '', // No canonical URL in this example
            ],
            [
                'ABC_News.html',
                'Brisbane Lions through to AFL grand final after beating Carlton by 16 points',
                'ABC News',
                'https://live-production.wcms.abc-cdn.net.au/22a05500d5223e217660d3917dff8539?impolicy=wcms_watermark_news&cropH=2813&cropW=5000&xPos=0&yPos=260&width=862&height=485&imformat=generic', // Image URL
                'The Brisbane Lions are into the AFL grand final for the first time in 19 years after a 16-point win over Carlton in their preliminary final.', // Description
                'https://www.abc.net.au/news/2023-09-23/afl-preliminary-final-brisbane-vs-carlton/102875098', // Canonical URL
                'article' // Type
            ],
            [
                'Australian_Gov.html',
                "Prime Minister of Australia", // Title is not available or relevant in this case
                "Prime Minister of Australia", // Site name is not available or relevant in this case
                '', // Image URL is not available or relevant in this case
                '', // Description is not available or relevant in this case
                '', // Canonical URL is not available or relevant in this case
                '', // Type is not available or relevant in this case
            ],
            [
                'BBC_News.html',
                'Ukraine war: US to give Kyiv long-range ATACMS missiles - media reports',
                'BBC News', //possible to go to publisher
                'https://ichef.bbci.co.uk/news/1024/branded_news/17023/production/_131234249_7028f59ebcb28c2b4b9f7531633b715167e77c3f-1.jpg',
                'Kyiv has long been pushing for ATACMS missiles capable of hitting far behind the front line.',
                'https://www.bbc.com/news/world-us-canada-66898029',
                'article',
            ],
            [
                'Drive.html',
                'Tesla Model 3 2024 Reviews, News, Specs & Prices - Drive',
                'Drive',
                'https://media.drive.com.au/driveau/image/upload/c_fill,f_auto,g_auto,h_675,q_auto:good,w_1200/cms/uploads/f1kan8igunrsx7rcr6fn',
                'Research 2024 Tesla Model 3 models with independent reviews, comparisons, news and deals. Find new, demo and used Tesla Model 3 cars for sale in your region.',
                'https://www.drive.com.au/showrooms/tesla/model-3/',
                'article',
            ],
            [
                'Facebook.html',
                'Moodle',
                'Facebook',
                'https://scontent.fbne3-1.fna.fbcdn.net/v/t39.30808-1/311984243_476640631157682_7783140295804704427_n.png?_nc_cat=104&ccb=1-7&_nc_sid=5f2048&_nc_ohc=onUWmOXDdFcAb4mpkJX&_nc_oc=AdgdplNTIs3KR2JKKVAJNl14Um0WEdrmMPoavT-5NeNkSiY-3Y2CU1ax_PKYvrBqmV4&_nc_ht=scontent.fbne3-1.fna&oh=00_AfCeV5QOVnAvJYYR0PezW1D7XIOZsy1JQmX597fZ2eXuXw&oe=66239BB4',
                'Moodle. 100,561 likes · 75 talking about this. Moodle is the world’s most customisable and trusted online learning solution including Moodle LMS.',
                'https://www.facebook.com/moodle',
                'video.other'
            ],
            [
                "flickr.html",
                "The famous Buttes of Monument Valley, Utah, USA",
                "iStock",
                "https://media.istockphoto.com/id/157009212/photo/monument-vally-buttes.jpg?s=170667a&w=0&k=20&c=jjJXHeh5HehfEfiemtdNLkxHDHXP0zFecjXdN-npryU=",
                "The famous Buttes of Monument Valley, Utah, USA",
                "https://www.istockphoto.com/photo/monument-vally-buttes-gm157009212-22183732",
                "article"
            ],
            [
                "GitHub.html",
                "Catalyst-QUT-2023/moodle",
                "GitHub",
                "https://opengraph.githubassets.com/0e80c410c46b1746e1d16dfe44d8bc33f31472f62564978a9d2ba02481dc43b5/Catalyst-QUT-2023/moodle",
                "Contribute to Catalyst-QUT-2023/moodle development by creating an account on GitHub.",
                "https://github.com/Catalyst-QUT-2023/moodle",
                "object"
            ],
            [
                "Guardian.html",
                "Move to protect Australian beef industry from EU land clearing laws criticised by scientists",
                "the Guardian",
                "https://i.guim.co.uk/img/media/bc145a61ce2aa3f9530a88d53d713a50e09077f8/0_204_5847_3508/master/5847.jpg?width=1200&height=630&quality=85&auto=format&fit=crop&overlay-align=bottom%2Cleft&overlay-width=100p&overlay-base64=L2ltZy9zdGF0aWMvb3ZlcmxheXMvdGctZGVmYXVsdC5wbmc&enable=upscale&s=4a8267611abab799f11efa2158839422",
                "Agriculture minister says there’s ‘no risk’ cattle industry is ‘connected to deforestation’, a claim environmental scientists say is ‘patently false’",
                "https://www.theguardian.com/australia-news/article/2024/may/10/move-to-protect-australian-beef-industry-from-eu-land-clearing-laws-criticised-by-scientists",
                "article"
            ],
            [
                "HTTP_Site.html",
                "Australia's official weather forecasts & weather radar - Bureau of Meteorology", //title of site
                "Bureau of Meteorology", // from v-card author tag??
                "",
                "Bureau of Meteorology web homepage provides the Australian community with access to weather forecasts, severe weather warnings, observations, flood information, marine and high seas forecasts and climate information. Products include weather charts, satellite photos, radar pictures and climate maps. The Bureau also has responsibility for compiling and providing comprehensive water information across Australia.",
                "http://www.bom.gov.au/",
                "WebPage"
            ],
            [
                "Imgur.html",
                "Arp 142: The Hummingbird Galaxy",
                "Imgur",
                "https://s.imgur.com/images/logo-1200-630.png",
                "Discover topics like science, astrophotography, nasa, space, and the magic of the internet at Imgur, a community powered entertainment destination. Lift your spirits with funny jokes, trending memes, entertaining gifs, inspiring stories, viral videos, and so much more from users like 16bitStarbuck.",
                "https://imgur.com/t/space/aAa44rI",
                "article"
            ],
            [
                "linkedin.html",
                "LinkedIn Ads on LinkedIn: Progress your Career",
                "LinkedIn",
                "https://static.licdn.com/aero-v1/sc/h/c45fy346jw096z9pbphyyhdz7",
                "Are you in the right place to progress your career? More
                and more marketers are willing to relocate for a bigger salary and a 
               better chance of promotion. Get…",
                "https://www.linkedin.com/posts/linkedin-ads_progress-your-career-activity-6925169086444318720-VFbb",
                "article"
            ],
            [
                "Medium.html",
                "Moodle – Medium",
                "Medium",
                "https://miro.medium.com/v2/resize:fit:1200/0*ykfJuzCfY5e3UeNF.png",
                "Read writing from Moodle on Medium. The world’s most customisable and trusted online learning solution. Moodle LMS is used by hundreds of millions of users.",
                "https://medium.com/@moodle",
                "profile"
            ],
            [
                "Moodle_Docs.html",
                "Course homepage - MoodleDocs",
                "",
                "",
                "",
                "",
                ""
            ],
            [
                "New_York_Times.html",
                "The New York Times International - Breaking News, US News, World News, Videos",
                "The New York Times",
                "https://static01.nyt.com/newsgraphics/images/icons/defaultPromoCrop.png",
                "The New York Times seeks the truth and helps people understand the world. With 1,700 journalists reporting from more than 150 countries, we provide live updates, investigations, photos and video of international and regional news, politics, business, technology, science, health, arts, sports and opinion.",
                "https://www.nytimes.com/international/",
                "website"
            ],
            [
                "Reddit_Post.html",
                "Reddit - Dive into anything",
                "",
                "",
                "",
                "",
                ""
            ],
            [
                "Reddit_Post.html",
                "Reddit - Dive into anything",
                "",
                "",
                "",
                "",
                ""
            ],
            [
                "Rolling_Stone_Article.html",
                "RFK Jr. Isn't So Sure About 9/11: 'Strange Things Happened'",
                "Rolling Stone",
                "https://www.rollingstone.com/wp-content/uploads/2023/09/rfk-jr-podcast.jpg?crop=0px%2C7px%2C1800px%2C1014px&resize=1600%2C900",
                "Robert F. Kennedy Jr. says he doesn't know if he believes the government explanation about 9/11, including that al-Qaeda was responsible.",
                "https://www.rollingstone.com/politics/politics-features/rfk-jr-questions-911-narrative-1234828619/",
                "article"
            ],
            [
                "Stack_Overflow.html",
                "Upgrade Moodle 2.5 to Moodle 3.3",
                "Stack Overflow",
                "https://cdn.sstatic.net/Sites/stackoverflow/Img/apple-touch-icon@2.png?v=73d79a89bded",
                "I want to upgrade a moodle website currently on version 2.5 to the latest version. &#xA;&#xA;I need to clarify that is it possible to upgrade Moodle 2.5 directly to Moodle 3.3.&#xA;&#xA;OR&#xA;&#xA;As mentioned in the moo...",
                "https://stackoverflow.com/questions/45081210/upgrade-moodle-2-5-to-moodle-3-3",
                "website"
            ],
            [
                "twitter.html",
                "Moodle | Online learning, delivered your way. (@moodle) / X",
                "X (formerly Twitter)",
                "https://pbs.twimg.com/profile_images/1125713968637579265/L4HJ0qyd_200x200.png",
                "Moodle is used by 400+ million learners.\nNeed support? Visit https://t.co/lqYoZXMFiA\nNeed support on a school site? Reach out to your school directly.",
                "https://twitter.com/moodle",
                "profile"
            ],
            [
                "Vimeo.html",
                "Indonesia FPV: Bali & Java (Interactive)",
                "Vimeo",
                "https://i.vimeocdn.com/video/1582822079-2aea6fdb70cf5e40a2ab93a7df446f7c4bdf1351f4a3cfa0e43a26fdbe074d41-d?f=webp",
                "Indonesia FPV: Bali & Java is a winner of the 2022 Best of the Year award. To explore the full list of winners, check out vimeo.com/bestoftheyear   From impenetrable",
                "https://vimeo.com/783455878",
                "video.other"
            ],
            [
                "Wikipedia.html",
                "Moodle - Wikipedia",
                "Wikipedia",
                "",
                "https://en.wikipedia.org/",
                "https://en.wikipedia.org/wiki/Moodle",
                "website"
            ],
            [
                "youtube.html",
                "2022 Moodle LMS video",
                "YouTube",
                "https://i.ytimg.com/vi/3ORsUGVNxGs/maxresdefault.jpg",
                "View our latest Moodle LMS release video: https://www.youtube.com/watch?v=DubiRbeDpnM&t=2sMoodle LMS is the world’s most customisable and trusted open source...",
                "https://www.youtube.com/watch?v=3ORsUGVNxGs",
                "videoother"
            ]
        ];
    }
}
