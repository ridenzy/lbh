<?php
declare(strict_types=1);
namespace loadBlogHelpers\Seo;



final class Meta {

/*
    
    Expects: 
        [
            'title' => 'My Page Title',
            'description' => 'Short SEO summary.',
            'canonical' => 'https://example.com/page',
            'robots' => 'index,follow'
        ]

   Expected output sample: 
    <title>My Page Title</title>
    <meta name="description" content="Short SEO summary.">
    <link rel="canonical" href="https://example.com/page">
    <meta name="robots" content="index,follow">

*/
  public static function metaTags(array $o): string {  // 1: Generates <meta> and <title> tags.
    // Escaping for safety --> escaped like this to avoid: Cross-Site Scripting (XSS) — the most common web injection vulnerability
    $title = htmlspecialchars($o['title'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $desc  = htmlspecialchars($o['description'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $canon = htmlspecialchars($o['canonical'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $robots = htmlspecialchars($o['robots'] ?? 'index,follow', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    
    //Build the `<head>` tags
    $head = implode("\n", array_filter([
      $title ? "<title>{$title}</title>" : null,
      $desc  ? "<meta name=\"description\" content=\"{$desc}\">" : null,
      $canon ? "<link rel=\"canonical\" href=\"{$canon}\">" : null,
      "<meta name=\"robots\" content=\"{$robots}\">",
    ]));
    return $head;
  }



  /*    
  Expected output sample: 
    <meta property="og:title" content="My Page Title">
    <meta property="og:description" content="Short SEO summary.">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://example.com/page">
    <meta property="og:image" content="https://example.com/preview.jpg">
    <meta name="twitter:card" content="summary_large_image">

*/

  public static function openGraph(array $o): string {  // 2. Generates OpenGraph and Twitter Card meta tags for rich previews (Facebook, LinkedIn, X/Twitter).
    //'$pairs' defines which OG/Twitter keys to output.
    $pairs = [
      'og:title' => $o['title'] ?? null,
      'og:description' => $o['description'] ?? null,
      'og:type' => $o['type'] ?? 'article',
      'og:url' => $o['url'] ?? null,
      'og:image' => $o['image'] ?? null,
      'twitter:card' => $o['twitter_card'] ?? 'summary_large_image'
    ];

    // Loop & render each meta tag
    $tags = [];
    foreach ($pairs as $p=>$v) if ($v) {
      $v = htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
      // Chooses attribute name: OpenGraph tags use 'property="og:..."'  |   Twitter/meta tags use 'name="..."'
      $attr = str_starts_with($p,'og:') ? 'property' : 'name';
      // Adds each generated line into '$tags[]'
      $tags[] = "<meta {$attr}=\"$p\" content=\"$v\">";
    }
    return implode("\n", $tags);
  }
}








/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

    $data = [
        'title' => 'Understanding PHP SEO Helpers',
        'description' => 'Learn how to generate meta and OpenGraph tags dynamically in PHP.',
        'canonical' => 'https://example.com/php-seo-helper',
        'url' => 'https://example.com/php-seo-helper',
        'image' => 'https://example.com/img/preview.png',
        'robots' => 'index,follow'
    ];

    echo "=== Meta Tags ===\n";
    echo Meta::metaTags($data);
    echo "\n\n=== OpenGraph Tags ===\n";
    echo Meta::openGraph($data);


}
