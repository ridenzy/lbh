<?php
declare(strict_types=1);
namespace loadBlogHelpers\Seo;


// Auto-generate keyword list by frequency analysis. , useful for SEO auto-tagging or content suggestions.



final class Keywords
{

    public static function analyzePostForKeywords(string $articleTexts, int $N = 0): array
    {
        $capture = []; // expecting to be an array in such format [[keyword] => Frequency];

        // Strip HTML and decode entities, if any
        $clean = strip_tags(html_entity_decode($articleTexts));

        //Transliteration to Convert all Unicode characters (accents, symbols) into closest ASCII equivalents.
        $t = iconv('UTF-8','ASCII//TRANSLIT', $clean);
        if ($t === false) {
            $t = $clean; // fallback
        }

        //Convert all to lowercase for consistency
        $t = strtolower($t);

        // convert string to list
        $t = explode(" ", $t);

        foreach($t as $v) {
            // if empty string skip this iteration
            if(empty($v)){
                continue;
            }
            $v = trim($v);
            if((array_key_exists($v,$capture)) && (strlen($v) > 1)){
                $capture[$v] = $capture[$v] + 1;
            }else{
                $capture[$v] = 1;
            }
           
        }

        arsort($capture); // sort associative arrays in descending order,  according to the value
        $capture = array_slice($capture,0, (($N === 0) ? (count($capture)) : $N)); // ps: grab top N(all elements if not set)
        return $capture;
        
    }

}





/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

    $article = " 
        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
        At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
        Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
        At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
        ";


    print_r(Keywords::analyzePostForKeywords($article));


}
