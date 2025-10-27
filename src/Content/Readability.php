<?php

namespace loadBlogHelpers\Content;

//Estimates reading time in minutes (based on word count).



final class Readability {
    //ps: The average adult reads around 200–250 words per minute, so 200 is a good default.
  public static function readTime(string $text, int $wpm=200): int {
    

    //strip_tags($text): Removes HTML markup ('<p>', '<h1>', '<b>', etc.), so we only count the visible text words.
    // This line uses regex with 'preg_match_all()' to count all words.
    $words = preg_match_all('/\p{L}+/u', strip_tags($text));


    /*
    
    1. 'max(100, $wpm)' ensures '$wpm' is at least 100 — prevents division by zero or unrealistic speeds.
        
    2. '$words / $wpm' gives you reading time in minutes.
        
    3. 'ceil()' rounds up — if 1.2 minutes, you show 2.
        
    4. '(int)' converts to integer.
        
    5. 'max(1, ...)' ensures minimum 1 minute even for tiny texts. 
    */
    return max(1, (int)ceil($words / max(100, $wpm)));
  }
}
