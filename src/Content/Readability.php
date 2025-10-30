<?php
declare(strict_types=1);
namespace loadBlogHelpers\Content;

//Estimates reading time in minutes (based on word count).



final class Readability 
{

  // count all words.
  public static function wordCount(string $content_): int
  {
    // Strip HTML and decode entities
    $clean = strip_tags(html_entity_decode($content_));

    // Match all word-like sequences (unicode letters)
    preg_match_all('/\p{L}+/u', $clean, $matches);

    // Return the number of matches (words)
    return count($matches[0]);
  }


    //ps: The average adult reads around 200–250 words per minute, so 200 is a good default.
  public static function readTime(string $text, int $wpm=200): int 
  {
    

    //strip_tags($text): Removes HTML markup ('<p>', '<h1>', '<b>', etc.), so we only count the visible text words.
    $words = self::wordCount($text);


    /*
    
    1. 'max(100, $wpm)' ensures '$wpm' is at least 100 — prevents division by zero or unrealistic speeds.
        
    2. '$words / $wpm' gives you reading time in minutes.
        
    3. 'ceil()' rounds up — if 1.2 minutes, you show 2.
        
    4. '(int)' converts to integer.
        
    5. 'max(1, ...)' ensures minimum 1 minute even for tiny texts. 
    */
    $readTimeInMinute = max(1, (int)ceil($words / max(100, $wpm)));

    return $readTimeInMinute;
  }
}





/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */
if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {


  $sampleText = "Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
  At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. Lorem ipsum dolor sit amet, 
  consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. At vero eos et accusam et justo duo dolores et ea rebum. 
  Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.";

  echo Readability::wordCount($sampleText) . "\n"; // 100 words

  echo Readability::readTime($sampleText) . "\n"; // 1 minute

}
