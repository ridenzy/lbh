<?php
declare(strict_types=1);
namespace loadBlogHelpers\Web;
use loadBlogHelpers\Content\Slug;


// 1: Generates absolute URLs from base + path.

// 2: sanitize urls to a consistent url friendly format




final class Urls 
{
  // Creates one official URL for the post. --> to Avoid duplicate URLs (SEO).
  public static function canonical(string $base, string $path): string 
  {
    return rtrim($base, '/') . '/' . ltrim($path, '/');
  }



  public static function sanitizeUrl(string $webBase, string $postTitle, string | int $postId=""): string 
  {
    $webBase = rtrim($webBase, '/');
    $postTitle = Slug::make($postTitle);
    $postId = $postId ? ("-". trim((string)$postId)) : "";
    return $webBase . "/" . $postTitle . $postId;
  }
}






/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

  require_once __DIR__ . '/../../vendor/autoload.php';

    

  echo Urls::canonical('https://kingsmaking101.com', '/blog/my-article') . "\n";
  // --> https://kingsmaking101.com/blog/my-article

  echo Urls::sanitizeUrl('https://kingsmaking101.com', 'who is the greatest realestate developer') . "\n";
  // https://kingsmaking101.com/who-is-the-greatest-realestate-developer

  echo Urls::sanitizeUrl('https://kingsmaking101.com', 'who is the greatest realestate developer',101) . "\n";
  // https://kingsmaking101.com/who-is-the-greatest-realestate-developer-101

}