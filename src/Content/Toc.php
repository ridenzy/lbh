<?php
declare(strict_types=1);

namespace loadBlogHelpers\Content; // puts the class in loadBlogHelpers\Content so it won’t clash with other classes of the same name.

require_once __DIR__ . '/Slug.php'; // pulls in the Slug helper so Slug::make() is available.  | Note: Make sure Slug.php uses the same namespace (loadBlogHelpers\Content).

final class Toc{
    //Extracts h2/h3 as TOC and injects missing ids back into the HTML.
    public static function extract(string $html): array
    {
        // 'DOMDocument' builds a DOM tree from our HTML.
        $dom = new \DOMDocument('1.0', 'UTF-8');  

        /*
        - Help DOMDocument interpret UTF-8 and suppress minor HTML warnings
        - The fake XML header ('<?xml encoding="utf-8" ?>') helps DOM treat the string as UTF-8.
        - Note: The '@' here, is suppressing warnings from slightly invalid HTML and flags silence libxml notices. |  Good for noisy input, but can hide real problems during development, so use sparingly.
        - 'loadHTML()' wraps our fragment in '<html><body>…</body></html>'.  
        */
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $html, LIBXML_NOERROR | LIBXML_NOWARNING); 

        // 'DOMXPath' lets you query nodes.
        $xp = new \DOMXPath($dom); 

        // Here it returns all '<h2>' and '<h3>' anywhere in the document.
        $nodes = $xp->query('//h2|//h3');  

        $toc = [];
        $seen = []; // deduplicate ids: id => count

        foreach ($nodes as $h) {
            /** @var \DOMElement $h */
            // - 'textContent' → visible text in the heading.
            $text = trim($h->textContent);  

            // - Base id from existing attribute or slugified text
            // - if the heading already has an 'id', use it; otherwise generate one with your 'Slug::make($text)'
            $base = $h->getAttribute('id') ?: Slug::make($text);  
            $id = $base;

            // Deduplicate: intro, intro-2, intro-3, ...
            if ($id !== '') {
                if (!isset($seen[$id])) {
                    $seen[$id] = 1;
                } else {
                    $seen[$id]++;
                    $id = $base . '-' . $seen[$base];
                }
            } else {
                // extreme edge: empty slug → "n-a", dedup too
                $id = 'n-a';
                if (!isset($seen[$id])) $seen[$id] = 1; else { $seen[$id]++; $id .= '-' . $seen[$id]; }
            }

            // Inject id back into DOM element if missing or changed
            if ($h->getAttribute('id') !== $id) {
                //'setAttribute('id', $id)' makes sure the heading in the DOM actually has the anchor target your TOC will link to. This is the key to making '<a href="#id">' work.
                $h->setAttribute('id', $id);
            }

            $toc[] = [
                // "h2" -> 2  |i.e:  'substr('h2', 1)' → "2", cast to '(int) → 2'.
                'level' => (int) substr($h->nodeName, 1), 
                'id'    => $id,
                'text'  => $text,
            ];
        }

        // Extract inner HTML of <body> only (strip DOM wrappers)
        $updatedHtml = '';
        $body = $dom->getElementsByTagName('body')->item(0);
        if ($body) {
            foreach ($body->childNodes as $child) {
                // This block rebuilds only the inner body content so it can be rendered directly into a page.  | ps: saveHTML($child) Converts each node back to HTML
                // $updatedHtml holds the whole inner body content, perfect for embedding
                $updatedHtml .= $dom->saveHTML($child);
            }
        }

        return ['toc' => $toc, 'html' => $updatedHtml];
    }
}

/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */
if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {
    $html = <<<HTML
    <h2>Intro</h2>
    <p>Text…</p>
    <h3>Details</h3>
    <p>More…</p>
    <h2>Intro</h2>
    HTML;

    $result = Toc::extract($html);
    $toc = $result['toc'];
    $bodyHtml = $result['html'];

    var_dump($toc);

    echo "<aside><ul>";
    foreach ($toc as $item) {
        $level = (int) $item['level'];
        $id    = htmlspecialchars($item['id'], ENT_QUOTES, 'UTF-8');
        $text  = htmlspecialchars($item['text'], ENT_QUOTES, 'UTF-8');
        echo "<li class='level-{$level}'><a href='#{$id}'>{$text}</a></li>";
    }
    echo "</ul></aside>";

    echo "<article>{$bodyHtml}</article>";
}
