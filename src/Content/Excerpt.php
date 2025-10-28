<?php
declare(strict_types=1);

namespace loadBlogHelpers\Content;


/*

excerpt  --> Cuts long text to a clean preview length (no mid-word cuts).

To select or use (a passage or segment from a longer work).
To select or use material from (a longer work).
*/




final class Excerpt 
{
    public static function strictExcerpt(string $text = "", int $textLimit = 50, bool $showDetails = false): string 
    {

        if ($showDetails) {
            echo "Debug info:\n";
            echo "  text: '$text'\n";
            echo "  limit: " . $textLimit . "\n";
            echo "  showDetails: " . ($showDetails ? 'true' : 'false') . "\n\n";
        }

        if (strlen($text) <= $textLimit) {
            return $text;
        } else {
            return substr($text, 0, $textLimit) . "...";
        }
    }



    public static function flexibleExcerpt($a = "", $b = null, $c = null): string 
    {
        $text = "";
        $limit = 50;
        $showDetails = false;

        foreach ([$a, $b, $c] as $arg) {
            if (is_string($arg)){ 
                $text = $arg;
            }elseif (is_int($arg)){
                $limit = $arg;
            }elseif (is_bool($arg)){ 
                $showDetails = $arg;
            }
        }

        if ($showDetails) {
            echo "Debug info:\n";
            echo "  text: '$text'\n";
            echo "  limit: $limit\n";
            echo "  showDetails: " . ($showDetails ? 'true' : 'false') . "\n\n";
        }

        return strlen($text) <= $limit ? $text : substr($text, 0, $limit) . '...';
    }
    



}

?>





<?php

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

    echo "Strict type\n";
    echo Excerpt::strictExcerpt("Hello world this is a test of excerpts", 10, true); 

    echo "flexible type\n";
    echo Excerpt::flexibleExcerpt(true);; // accept any argument order or type and rectify using a robust internal detection

}



?>