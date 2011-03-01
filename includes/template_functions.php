<?php
/**
 * File defines miscellaneous functions for use in a template
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Combined a page name with the site name
 * @param string $page_name
 * @return string
 */
function get_title ($page_name="") {
    global $template;

    $title = isset ($page_name) ? $page_name . " | " : "";
    $site_name = $template->get ("SITE_NAME");
    //echo $site_name;

    return $title . $site_name;
}

/**
 * Generate url useful for a hyperlink by joining value of BASE_URL constant and $add_path
 * @param string $add_path
 * @return string
 */
function generate_link_url ($add_path="") {
    $full_path = joinPath (BASE_URL, $add_path);
    return $full_path;
}

/**
 * Generate url useful for linking to media (img, embed, etc.) by joining value of MEDIA_URL constant and $add_path
 * @param string $add_path
 * @return string
 */
function generate_media_url ($add_path) {
    $full_path = joinPath (MEDIA_URL, $add_path);
    return $full_path;
}

/**
 * Escape a string to avoid XSS
 * @param string $string
 * @return string
 */
function full_escape ($string) {
    return htmlspecialchars (stripslashes ($string));
}

?>
