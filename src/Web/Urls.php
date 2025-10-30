<?php
declare(strict_types=1);
namespace loadBlogHelpers\Web;


// 1: Generates absolute URLs from base + path.

// 2: sanitize urls to a consistent url friendly format




final class Urls 
{
  public static function canonical(string $base, string $path): string 
  {
    return rtrim($base, '/') . '/' . ltrim($path, '/');
  }
}






/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

    

echo Urls::canonical('https://kingsmaking101.com', '/blog/my-article');
// → https://kingsmaking101.com/blog/my-article

}