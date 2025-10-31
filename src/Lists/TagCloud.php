<?php

declare(strict_types=1);

//the namespace — so other files can 'use loadBlogHelpers\Lists\TagCloud;'  It’s part of the modular "Lists" helper package.
namespace loadBlogHelpers\Lists;
use loadBlogHelpers\Seo\Keywords;


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

    // checking if expected "array of strings" is here ['PHP', 'Backend', 'Programming'],
    if (count($postItems) > 1) { 
      $postItems = ["tags" => $postItems];
    } else {  
      // the alternative "arrayed  string" ['PHP Backend Programming'];
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

    arsort($weights); // arrange in descending order
    return $weights;
  }


  # get tags of multiple different  blog contents .. 
  public static function getKeywords(string $blogContent, int $cutOff = 0): array
  {
    
    return array_keys(Keywords::analyzePostForKeywords($blogContent, $cutOff));
  }



  public static function getTagCloud(array $posts): array // makes tag cloud of whole content .ideally: feed tags of multiple different  blog contents .. 
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




}



/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ((PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME']))) {
  require_once __DIR__ . '/../../vendor/autoload.php';


  // Sample post data

  $posts1 = [
    ['title' => 'Intro to PHP', 'tags' => ['PHP', 'Backend', 'Programming']], // sample tags in blog post 1
    ['title' => 'CSS Flexbox Guide', 'tags' => ['CSS', 'Frontend', 'Design']], // sample tags in blog post 2
    ['title' => 'Advanced PHP OOP', 'tags' => ['PHP', 'OOP', 'Backend']],  // sample tags in blog post 3
    ['title' => 'JavaScript Basics', 'tags' => ['JavaScript', 'Frontend']],  // sample tags in blog post 4
    ['title' => 'Fullstack Development', 'tags' => ['PHP', 'JavaScript', 'Frontend', 'Backend']],  // sample tags in blog post 5
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

  $article = " 
    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
    At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet. 
    Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor invidunt ut labore et dolore magna aliquyam erat, sed diam voluptua. 
    At vero eos et accusam et justo duo dolores et ea rebum. Stet clita kasd gubergren, no sea takimata sanctus est Lorem ipsum dolor sit amet.
    ";



  
}
