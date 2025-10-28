<?php

declare(strict_types=1);

namespace loadBlogHelpers\Lists;


// Finds similar posts based on title/body text similarity (mini search engine logic).


final class Related
{
    // Relations by terms | i.e: turn chunks of texts into a small “word frequency” dictionary.
    private static function terms(string $text): array
    {
        // Strip HTML tags to get inner text and set its string to lower case
        $text = mb_strtolower(strip_tags($text));

        /*
        - Finds all sequences of 3+ letters (\p{L} = any Unicode letter).
        - The 'u' flag means 'UTF-8 aware.'
        */
        preg_match_all('/\p{L}{3,}/u', $text, $m);

        //Counts how often each word appears.
        $counts = array_count_values($m[0] ?? []);

        //Sorts the array by frequency, descending.
        arsort($counts);

        // Keep and Return only the top 50 most frequent words (preserving keys).
        $sendBack = array_slice($counts, 0, 50, true);
        return $sendBack;

        /*
        e.g "PHP Basics: From Zero to Hero!" --> 
        Array
        (
            [php] => 1
            [basics] => 1
            [from] => 1
            [zero] => 1
            [hero] => 1
        )

        This becomes the "term vector" for one post.

        */
    }


    // Compute similarity | i.e: cosine similarity between two term vectors.
    /*
    Mindspace:

    Think of each post as a long vector of word frequencies:
        Post A:  [php: 3, code: 1, learn: 2]
        Post B:  [php: 2, learn: 1, tutorial: 4]
    */
    private static function sim(array $a, array $b): float
    {
        // Initialize accumulators.
        $dot = 0.0;
        $na = 0.0;
        $nb = 0.0;

        // For each word '$t'(key) and its weight '$w'(value) in post A
        foreach ($a as $t => $w) {
            $na += $w * $w;  //  builds magnitude of A.
            if (isset($b[$t])) {  // If word exists in B
                $dot += $w * $b[$t]; // Add '$w * $b[$t]' to dot product
            }
        }
        foreach ($b as $w) {
            $nb += $w * $w;  //  Builds magnitude of B.
        }

        // If both have length, return cosine; else '0.0' (no similarity).
        $sendBack = ($na && $nb) ? $dot / (sqrt($na) * sqrt($nb)) : 0.0;
        return $sendBack;


        /*
        - identical posts → '1.0'
    
        - completely different → '0.0'
            
        - moderately related → around '0.3–0.7'
        */
    }



    // Find related posts
    public static function relationShip(array $posts, string $currentId, int $limit = 5): array
    {
        $index = [];

        // Build a “term index” for every post
        foreach ($posts as $p) {
            $index[$p['id']] = self::terms($p['title'] . ' ' . $p['body']);
        }
        // Get current post’s term vector
        $cur = $index[$currentId] ?? [];  //If the ID doesn’t exist, `$cur` is empty (so all similarities = 0).
        $scores = [];

        //Compute similarity with every other post
        foreach ($posts as $p) {
            if ($p['id'] === $currentId) {
                continue;
            }
            $scores[$p['id']] = self::sim($cur, $index[$p['id']] ?? []);
        }




        //Sort and pick top N (limit)
        arsort($scores);
        // ------------------------------------
        // DEBUG 1: show computed similarity scores | if needed
        //var_export($scores);
        //echo PHP_EOL;
        // ------------------------------------

        $ids = array_map('strval', array_slice(array_keys($scores), 0, $limit));
        // ------------------------------------
        // DEBUG 2: show which IDs we plan to return | if needed
        //var_export($ids);
        //echo PHP_EOL;
        // ------------------------------------

        $sendBack = array_values(array_filter($posts, fn($p) => in_array($p['id'], $ids, true)));

        // ------------------------------------
        // DEBUG 3: show how many made it through filter  | if needed
        //echo "count(sendBack) = " . count($sendBack) . PHP_EOL;
        // ------------------------------------

        //Return full post data
        return $sendBack;
    }


    /*public static function runTest(): void{
        print_r(self::terms("PHP Basics: From Zero to Hero!"));
        $hold = [["php"] => 1, ["basics"] => 1, ["from"] => 1, ["zero"] => 1, ["hero"] => 1];
        print_r(self::sim($hold,$hold));
    }*/
}



/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */
if ((PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))) {

    //echo Related::runTest();

    // Sample mock dataset
    $posts = [
        [
            'id'    => '1',
            'title' => 'Learn PHP Basics',
            'body'  => 'This post introduces PHP syntax, variables, and basic functions for beginners.'
        ],
        [
            'id'    => '2',
            'title' => 'Mastering JavaScript Functions',
            'body'  => 'A guide to closures, callbacks, and async functions in modern JavaScript.'
        ],
        [
            'id'    => '3',
            'title' => 'PHP and MySQL Integration',
            'body'  => 'Learn how to connect PHP with MySQL to build dynamic websites and APIs.'
        ],
        [
            'id'    => '4',
            'title' => 'Building REST APIs with PHP',
            'body'  => 'This article covers how to use PHP to create and consume RESTful APIs securely.'
        ],
        [
            'id'    => '5',
            'title' => 'CSS Grid Layout Guide',
            'body'  => 'Master modern CSS grid techniques to build responsive layouts efficiently.'
        ],
        [
            'id'    => '6',
            'title' => 'Debugging PHP Code Like a Pro',
            'body'  => 'Tips for debugging, profiling, and error handling in PHP applications.'
        ],
        [
            'id'    => '7',
            'title' => 'More PHP  Basics',
            'body'  => 'This post expands PHP syntax, variables, and basic functions for beginners.'
        ],
    ];

    // Choose the current post (simulate viewing post #1)
    $currentId = '1';

    // Get top 3 related posts
    $related = Related::relationShip($posts, $currentId, 5);

    // Print results
    echo "--> Current Post: " . $posts[0]['title'] . " \n\n";
    echo "Top Related Posts:\n";
    foreach ($related as $i => $p) {
        $rank = $i + 1;
        echo "  " . $rank . "." . $p['title'] . "\n";
    }
}

