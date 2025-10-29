<?php
namespace loadBlogHelpers\Feeds;

// build a valid RSS 2.0 feed (XML) from your post data.

/*
RSS = “Really Simple Syndication” — a standardized XML format that allows apps, bots, or other websites to automatically read your blog’s newest posts.
*/
final class Rss 
{
  //returns the entire RSS feed as an XML string.
  /*
  Expected sample input:
    '$posts' -->  articles (list of associative arrays)
    '$site' --> overall blog info (title, URL, description)
  */
  public static function generate(array $posts, array $site): string 
  {
    // Create the root '<rss>' and '<channel>' elements
    // PHP’s 'SimpleXMLElement' lets you dynamically build XML trees easily.
    

    $xml = new \SimpleXMLElement('<rss version="2.0"><channel/></rss>'); // This line initializes: <rss version="2.0"><channel></channel></rss>  | Everything inside the blog feed goes inside #<channel>'.

    // Add basic site metadata
    /*
    Adds the global blog information:
        <title>My Blog</title>
        <link>https://example.com</link>
        <description>My tech articles and tutorials</description>
    */
    $xml->channel->addChild('title', htmlspecialchars($site['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $xml->channel->addChild('link', htmlspecialchars($site['url'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
    $xml->channel->addChild('description', htmlspecialchars($site['description'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

    //Add '<item>' entries for each post
    /*
    Each '<item>' block represents one post.

    Example output:

        <item>
            <title>Learn PHP SEO</title>
            <link>https://example.com/blog/php-seo</link>
            <description>A guide to optimizing PHP websites for search engines.</description>
            <pubDate>Mon, 28 Oct 2025 15:00:00 +0000</pubDate>
        </item>

    Notes:
        - gmdate(DATE_RSS, ...) formats the date to RSS-compliant UTC format:  -->  Mon, 28 Oct 2025 15:00:00 +0000
            
        - strtotime() converts a string date (like '2025-10-28') to a timestamp.
    */
    foreach ($posts as $p) {
      $item = $xml->channel->addChild('item');
      $item->addChild('title', htmlspecialchars($p['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
      $item->addChild('link', htmlspecialchars($p['url'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
      $item->addChild('description', htmlspecialchars($p['excerpt'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
      $item->addChild('pubDate', gmdate(DATE_RSS, strtotime($p['published_at'])));
    }

    // Return the final XML, i.e convert the 'SimpleXMLElement' tree into an XML string.
    return $xml->asXML();
  }
}




if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $site = [
        'title' => 'Kingsmaking101 — Business Ideas & Strategy',
        'url' => 'https://kingsmaking101.com',
        'description' => 'In-depth reports and creative business ideas for solopreneurs.'
    ];

    $posts = [
        [
            'title' => 'How to Start a Paid Telegram Bot Business',
            'url' => 'https://kingsmaking101.com/blog/telegram-bot-business',
            'excerpt' => 'Learn how to build and sell Telegram bots for passive income.',
            'published_at' => '2025-10-20'
        ],
        [
            'title' => 'Modular Smartphones: The Future of Customization',
            'url' => 'https://kingsmaking101.com/blog/modular-smartphones',
            'excerpt' => 'Explore the next-gen of personalized tech — modular phone systems.',
            'published_at' => '2025-10-18'
        ],
    ];

    echo Rss::generate($posts, $site);

    /*
    Expected output:

        <?xml version="1.0"?>
        <rss version="2.0">
        <channel>
            <title>Kingsmaking101 — Business Ideas & Strategy</title>
            <link>https://kingsmaking101.com</link>
            <description>In-depth reports and creative business ideas for solopreneurs.</description>
            <item>
            <title>How to Start a Paid Telegram Bot Business</title>
            <link>https://kingsmaking101.com/blog/telegram-bot-business</link>
            <description>Learn how to build and sell Telegram bots for passive income.</description>
            <pubDate>Mon, 20 Oct 2025 00:00:00 +0000</pubDate>
            </item>
            <item>
            <title>Modular Smartphones: The Future of Customization</title>
            <link>https://kingsmaking101.com/blog/modular-smartphones</link>
            <description>Explore the next-gen of personalized tech — modular phone systems.</description>
            <pubDate>Sat, 18 Oct 2025 00:00:00 +0000</pubDate>
            </item>
        </channel>
        </rss>
    */
}