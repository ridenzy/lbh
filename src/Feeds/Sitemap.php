<?php
declare(strict_types=1);
namespace loadBlogHelpers\Feeds;

//Builds a Google-friendly sitemap.xml.
/*

A sitemap.xml is a standardized XML file that tells search engines (Google, Bing, etc.): "Here are all my pages, and here’s when they were last updated."

That helps bots crawl sites faster, and ensures new articles get indexed sooner.
*/


final class Sitemap 
{
  public static function generate(array $urls): string 
  {
    // Create the XML skeleton
    $xml = new \SimpleXMLElement(
      '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"/>'
    );
    // Loop through URLs
    foreach ($urls as $u) {
      $url = $xml->addChild('url'); // Adds <url> block
      $url->addChild('loc', htmlspecialchars($u['loc'])); // Adds child <loc> (the absolute page link)
      if (!empty($u['lastmod'])) { // Optionally: add <lastmod> (last modification date)
        $url->addChild('lastmod', htmlspecialchars($u['lastmod'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
      }
    }
    return $xml->asXML();
  }
}






if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {

    // Expected input sample
    $sitemap = Sitemap::generate([
        ['loc' => 'https://kingsmaking101.com/'],
        ['loc' => 'https://kingsmaking101.com/blog/personalized-fragrance-generator', 'lastmod' => '2025-10-21'],
    ]);

    file_put_contents('public/sitemap.xml', $sitemap); // This writes the generated XML into the public folder, making it accessible at: https://kingsmaking101.com/sitemap.xml    
    // submit that URL to Google Search Console or Bing Webmaster Tools.

}