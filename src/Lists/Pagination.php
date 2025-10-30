<?php
declare(strict_types=1);
namespace loadBlogHelpers\Lists;


//Splits a list of posts into pages (with total/page count).

/*
Pagination, also known as paging, is the process of dividing a document into discrete pages, either electronic pages or printed pages. 

*/

// also  : // Given a list of posts, finds the previous and next article.



final class Pagination 
{
    public static function paginationDetails(array $items, int $perPage, int $pageNumber): array
    {
        // get total length of items
        $total = count($items);

        /*
        - 'max(1, $perPage)' ensure to never divide by 0 (safety guard).
    
        - '$total / $perPage → how many pages needed.
            
        - 'ceil()' → rounds up (so 7 items at 2 per page = 4 pages).
            
        - 'max(1, …)' again ensures at least 1 page even if list is empty.
        */
        $pages = max(1,(int)ceil($total / max(1,$perPage)));

        // Ensure '$page' stays within valid range:  'max(1, $page)' → not less than 1.  and  'min(..., $pages)' → not greater than total pages.
        $pageNumber = min(max(1,$pageNumber),$pages);

        // Calculate where this page should start in the list.
        $offset = ($pageNumber -1) * $perPage;

        $sendBack = [
            'items' => array_slice($items, $offset, $perPage),
            'total' => $total,
            'pages' => $pages,
            'pageNumber' => $pageNumber,
        ];

        return $sendBack;
    }
}


/* -------------------------------
   Demo block (runs only when this file is executed directly via CLI)
-------------------------------- */

if ( (PHP_SAPI === 'cli') && (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) ) {

    $posts = ['A','B','C','D','E'];

    for( $i = 0; $i < count($posts); $i++ ){
        $result = Pagination::paginationDetails($posts, 2, $i+1);

        echo "Page " .( $i + 1) . ": \n";
        print_r($result);
        echo "\n ------------- \n";

    }

}