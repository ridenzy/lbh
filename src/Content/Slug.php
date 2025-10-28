<?php
declare(strict_types=1);

namespace loadBlogHelpers\Content;


//Turns a blog title into a clean URL slug (e.g., My Post → my-post).

final class Slug 
{
  public static function make(string $title): string 
  {

    // Detect which encoding text is most likely in.
    $encoding = mb_detect_encoding($title, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
    if ($encoding !== 'UTF-8') {
        $title = mb_convert_encoding($title, 'UTF-8', $encoding ?: 'UTF-8'); // This automatically converts from detected source to UTF-8 before processing
    }   

    //Transliteration to Convert all Unicode characters (accents, symbols) into closest ASCII equivalents.
    $t = iconv('UTF-8','ASCII//TRANSLIT', $title);
    if ($t === false) {
      $t = $title; // fallback
    }

    // Replace non-letter/digit sequences with dashes
    $t = preg_replace('~[^\\pL\\d]+~u', '-', $t);

    //Remove leading and trailing `-` that might have been added by the regex
    $t = trim($t, '-');

    //Convert to lowercase for consistent, SEO-friendly URLs
    $t = strtolower($t);


    return $t ?: 'n-a';  // If '$t' is empty or falsey, return 'n-a' instead.
  }
}


