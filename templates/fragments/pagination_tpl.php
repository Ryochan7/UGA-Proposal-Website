<?php
    $pagstring = "";
    $PAGINATION_WINDOW = 9;

    if (empty ($paginator_page)) {
        return;
    }

    $queryVarString = "";
    if (!empty ($queryVars)) {
        foreach ($queryVars as $key => $value) {
            $queryVarString .= $key . "=" . $value;
            $queryVarString .= "&amp;";
        }
    }

    $tmp_paginator = $paginator_page->getPaginator ();
    if ($tmp_paginator->getNumPages () > 1) {
        // Start div tag
        $pagstring .= "<div class=\"pagination\">";

        // First page link
        $pagstring .= "<a href=\"?{$queryVarString}page=1\">first</a>&nbsp;";

        // Previous page link
        if ($paginator_page->hasPrevious ()) {
            $pagstring .= "<a href=\"?{$queryVarString}page={$paginator_page->previousPageNumber ()}\">prev</a>&nbsp;";
        }

        // Not enough pages to split
        if ($tmp_paginator->getNumPages () < $PAGINATION_WINDOW) {
            for ($i = 1; $i <= $tmp_paginator->getNumPages (); $i++) {
                if ($i == $paginator_page->getPageNumber ()) {
                    $pagstring .= "<span class=\"current_page\">{$i}</span>&nbsp;";
                }
                else {
                    $pagstring .= "<a href='?{$queryVarString}page={$i}'>{$i}</a>&nbsp;";
                }
            }
        }
        // There are enough pages to split. Try to place the current page
        // number in the middle
        else if ($tmp_paginator->getNumPages () >= $PAGINATION_WINDOW) {
            $page_range = $tmp_paginator->getPageRange ();
            $pagenum = $paginator_page->getPageNumber ();
            $seperator = intval ($PAGINATION_WINDOW / 2);
            $start = $pagenum - 1 - $seperator;
            $start = ($start < 0) ? 0 : $start;
            $end = $pagenum - 1 + $seperator;
            $end = ($end > $tmp_paginator->getNumPages ()) ? $tmp_paginator->getNumPages () : $end;
            $splice = array_splice ($page_range, $start, $PAGINATION_WINDOW);
            //print_r ($splice);
            //echo "START: " . $start . "\n";
            //echo "END: " . $end . "\n";
            //echo "COUNT: " . count ($splice) . "\n";
            
            for ($i = 0; $i < count ($splice); $i++) {
                if ($splice[$i] == $paginator_page->getPageNumber ()) {
                    $pagstring .= "<span class=\"current_page\">{$splice[$i]}</span>&nbsp;";
                }
                else {
                    $pagstring .= "<a href=\"?{$queryVarString}page={$splice[$i]}\">{$splice[$i]}</a>&nbsp;";
                }
           }
        }

        // Next page link
        if ($paginator_page->hasNext ()) {
            $pagstring .= "<a href=\"?{$queryVarString}page={$paginator_page->nextPageNumber ()}\">next</a>&nbsp;";
        }

        // Last page link
        $pagstring .= "<a href=\"?{$queryVarString}page={$tmp_paginator->getNumPages ()}\">last</a>&nbsp;";

        // Close div tag
        $pagstring .= "</div>\n";
        echo $pagstring;
    }

?>
