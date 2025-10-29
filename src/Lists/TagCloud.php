<?php

declare(strict_types=1);
//the namespace — so other files can 'use loadBlogHelpers\Lists\TagCloud;'  It’s part of the modular "Lists" helper package.

namespace loadBlogHelpers\Lists;


//Generates tag weights for display.
/*
conceptual usage: 

AI (appears in 15 posts)
startup (appears in 8 posts)
design (appears in 3 posts)
*/





final class TagCloud
{



  private static function standardizeTagList(array $postItems): array
  {

    if (count($postItems) > 1) { // checking if expected "array of strings" is here ['PHP', 'Backend', 'Programming'],
      $postItems = ["tags" => $postItems]; // 
    } else {  // the alternative "arrayed  string" ['PHP Backend Programming'];
      $explodedPostItems = ["tags" => explode(" ", $postItems[0])];
      $postItems = $explodedPostItems;
    }

    return $postItems;
  }

  private static function generate(array $post, array &$counts): array
  {


    foreach ($post['tags'] as $tag) {
      $tag = strtolower(trim($tag)); // normalize case + remove spaces
      if ($tag === '') {
        continue; // ignore empty strings
      }
      // Count how often each tag appears
      $counts[$tag] = ($counts[$tag] ?? 0) + 1;
    }

    if (!$counts) {
      return [];
    };

    // 'min' and 'max' find the smallest and largest frequencies.
    $min = min($counts);
    $max = max($counts);
    // (if all tags appear equally, 'spread' would be 0 → so guard with 'max(1, …)' to avoid division by zero).
    $spread = max(1, $max - $min);

    // Normalize counts to 1–5 scale
    $weights = [];
    foreach ($counts as $tag => $count) {
      $weights[$tag] = 1 + (int)round(4 * ($count - $min) / $spread);
    }

    ksort($weights); // optional alphabetical order
    return $weights;
  }



  public static function getTagCloud(array $posts): array
  {
    $iteration = 0;
    $counting = [];

    foreach ($posts as $post) {
      if (! is_array($post) || ! array_key_exists("tags", $post)) {
        // Safety guard against stringed post | i.e if post is like this: ['PHP Backend Programming']  
        $post = (is_string($post) ? (self::standardizeTagList([$post])) : (self::standardizeTagList($post)));

        // turn a situation of posts at an index with 'PHP Backend Programming' to ['PHP Backend Programming']  
        if (! is_array($posts[$iteration])) {
          $swap = [$posts[$iteration]];
          $posts[$iteration] = $swap;
        }
      }

      $posts[$iteration]["tagCloud"] = self::generate($post, $counting);
      $iteration++;
    }
    return $posts;
  }



  public static function runTestsForPossibleSituations(array $arraySituation): void
  {
    print_r(self::getTagCloud($arraySituation));
  }
}


/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ((PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))) {

  // Sample post data

  $posts1 = [
    ['title' => 'Intro to PHP', 'tags' => ['PHP', 'Backend', 'Programming']],
    ['title' => 'CSS Flexbox Guide', 'tags' => ['CSS', 'Frontend', 'Design']],
    ['title' => 'Advanced PHP OOP', 'tags' => ['PHP', 'OOP', 'Backend']],
    ['title' => 'JavaScript Basics', 'tags' => ['JavaScript', 'Frontend']],
    ['title' => 'Fullstack Development', 'tags' => ['PHP', 'JavaScript', 'Frontend', 'Backend']],
  ];


  $posts2 = [
    ['PHP', 'Backend', 'Programming'],
    ['CSS', 'Frontend', 'Design'],
    ['PHP', 'OOP', 'Backend'],
    ['JavaScript', 'Frontend'],
    ['PHP', 'JavaScript', 'Frontend', 'Backend'],
  ];

  $posts3 = [
    ['PHP Backend Programming'],
    ['CSS Frontend Design'],
    ['PHP OOP Backend'],
    ['JavaScript Frontend'],
    ['PHP JavaScript Frontend Backend'],
  ];

  $posts4 = ['PHP Backend Programming'];

  $posts5 = ['PHP Backend Programming', 'CSS Frontend Design', 'PHP OOP Backend', 'JavaScript Frontend', 'PHP JavaScript Frontend Backend'];



  echo TagCloud::runTestsForPossibleSituations($posts4);
}
