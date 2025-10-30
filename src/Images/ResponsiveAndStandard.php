<?php
declare(strict_types=1);
namespace loadBlogHelpers\Images;



final class ResponsiveAndStandard 
{
    # Build a responsive image tag set (src, srcset, sizes).
    public static function responsiveSrcSet(string $path, array $widths=[480,768,1200]): array 
    {
        // get file extension ('jpg', 'png', etc.)
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        // remove the extension from $path.
        $base = preg_replace('/\.' . preg_quote($ext, '/') . '$/', '', $path);
        $srcset = [];
        // Loop through all given widths and build filenames, e.g: /images/posts/ai-trends-480.jpg 480w
        foreach ($widths as $w) {
            $srcset[] = "{$base}-{$w}.{$ext} {$w}w";
        }
        $sendBack = [
            'src' => "{$base}-{$widths[0]}.{$ext}", // Default image (smallest width, for fallback)
            'srcset' => implode(', ', $srcset), // Full list of available sizes + their pixel width
            'sizes' => '(max-width: 768px) 100vw, 768px' // Hint for the browser: if viewport ≤ 768px → use full width (100vw), else use 768px image.
        ];
        return $sendBack;
    }


    # Auto-generate a readable alt tag from the post title.
    public static function fromTitle(string $title, string $brand): string 
    {
        // Replace hyphens/underscores
        $text = preg_replace('/[-_]+/', ' ', $title);

        //Trim & Capitalize first letter 
        $text = ucfirst(trim($text));

        // Escape special characters
        return htmlspecialchars($text . ' — ' . $brand . ' image', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}






/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {


    $img = ResponsiveAndStandard::responsiveSrcSet('/images/posts/ai-trends.jpg');
    echo "<img src='{$img['src']}' srcset='{$img['srcset']}' sizes='{$img['sizes']}' alt='AI Trends'>";
        
    /*
    HTML OUTPUT:

    <img 
    src="/images/posts/ai-trends-480.jpg" 
    srcset="/images/posts/ai-trends-480.jpg 480w, /images/posts/ai-trends-768.jpg 768w, /images/posts/ai-trends-1200.jpg 1200w" 
    sizes="(max-width: 768px) 100vw, 768px"
    alt="AI Trends">


    Browser now automatically picks the optimal size depending on device:

    - Phone --> 480px version
        
    - Tablet --> 768px
        
    - Desktop --> 1200px
        
    This massively improves performance + SEO. :)


    */

    echo "<img src='/images/fragrance.jpg' alt='" . ResponsiveAndStandard::fromTitle('Personalized Fragrance Generator', 'Kingsmaking101 article') . "'>";
    // → alt="Personalized fragrance generator — Kingsmaking101 image"

}