<?php
declare(strict_types=1);
namespace loadBlogHelpers\Seo;


/*
Concept:

Render a '<script type="application/ld+json">' block containing JSON-LD that describes a blog article.  

Note: Search engines read this structured data directly — users don’t see it.
*/



/*
Expected input sample:
    [
    'title' => 'Learn PHP SEO',
    'description' => 'A guide to optimizing PHP websites for search engines.',
    'image' => 'https://example.com/cover.jpg',
    'published' => '2025-10-28',
    'updated' => '2025-10-29',
    'author' => 'Ridenzy Null',
    'url' => 'https://example.com/blog/php-seo'
    ]

Expected sample output:

    <script type="application/ld+json">
    {"@context":"https://schema.org","@type":"BlogPosting",
    "headline":"Learn PHP SEO","description":"A guide to optimizing PHP websites for search engines.",
    "image":"https://example.com/cover.jpg",
    "datePublished":"2025-10-28",
    "dateModified":"2025-10-29",
    "author":{"@type":"Person","name":"Ridenzy Null"},
    "mainEntityOfPage":"https://example.com/blog/php-seo"}
    </script>

*/

final class Schema 
{
  public static function article(array $o): string 
  {
    // Build the Schema data array
    $data = [
      '@context' => 'https://schema.org',  // Defines vocabulary source (always "https://schema.org")
      '@type' => 'BlogPosting', // Schema entity type (for blogs using "BlogPosting" in this case)
      'headline' => $o['title'] ?? '',  // The post title
      'description' => $o['description'] ?? '',  // Short summary for search results
      'image' => $o['image'] ?? null,  // Main article image (URL)
      'datePublished' => $o['published'] ?? null, // Original publication date (ISO 8601) i.e YYYY-MM-DD
      'dateModified'  => $o['updated'] ?? ($o['published'] ?? null),  // Last modified date
      'author' => ['@type'=>'Person','name'=>$o['author'] ?? ''], // Object with author info
      'mainEntityOfPage' => $o['url'] ?? null  // Canonical URL of the article
    ];

    //  Convert to JSON safely
    /*
    - 'array_filter()' removes null or empty values (so your JSON is clean).
    
    - 'JSON_UNESCAPED_SLASHES' keeps URLs readable (e.g. '/' instead of '\/').
        
    - 'json_encode()' converts the PHP array into proper JSON.

    Resulting isn --> {"@context":"https://schema.org","@type":"BlogPosting","headline":"Learn PHP SEO",...}    | them Wrapped inside a '<script>' tag
    */
    $jsonConversion = '<script type="application/ld+json">'.json_encode(array_filter($data), JSON_UNESCAPED_SLASHES).'</script>';

    return $jsonConversion;
  }
}





















/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

    $post = [
        'title' => 'Learn PHP SEO',
        'description' => 'A guide to optimizing PHP websites for search engines.',
        'image' => 'https://example.com/cover.jpg',
        'published' => '2025-10-28',
        'updated' => '2025-10-29',
        'author' => 'Ridenzy Null',
        'url' => 'https://example.com/blog/php-seo'
    ];

    echo Schema::article($post);

}
