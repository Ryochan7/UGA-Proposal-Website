<?php
/**
 * File defines the AlbumListController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Album.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Photo.php"));

/**
 * Display album listings
 *
 * Read in list of albums and the latest photos for each album.
 * Display results in the page.
 * @package PageController
 */
class AlbumListController implements Controller {
    /**
     * PageTemplate object used to render page
     * @access protected
     * @var PageTemplate
     */
    protected $template;

    /**
     * Constructor. Create instance of PageTemplate using default index_tpl.php file
     * @access public
     */
    public function __construct () {
        $this->template = new PageTemplate ();
    }

    /**
     * Run method with main page logic
     * 
     * Read in list of albums and the latest photos for each album. Pagination enabled.
     * Populate template with data and display results in the page.
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $albumDAO = AlbumDAO::getInstance ();
        $photoDAO = PhotoDAO::getInstance ();

        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $count = $paginator = $paginator_page = null;
        $album = $photo_info_array = null;
        $title = "";

        $count = $albumDAO->count ();
        $paginator = new Paginator ($count, $PAGINATION_LIMIT);
        $paginator_page = $paginator->getPage ($page);
        $album_array = $albumDAO->all (array ("limit" => $paginator_page));
        $photo_info_array = array ();
        foreach ($album_array as $album) {
            $count = $photoDAO->countByAlbum ($album);
            if ($count > 0) {
                $tmp_paginator = new Paginator ($count, 1);
                $tmp_paginator_page = $paginator->getPage ($page);
                // Only get latest item
                list ($latest_photo) = $photoDAO->allByAlbum ($album, array ("order" => "id DESC", "limit" => $tmp_paginator_page));
                $photo_info_array[]  = array ($count, $latest_photo);
            }
        }

        $this->template->render (array (
                                    "title" => "Album List",
                                    "main_page" => "album_list_tpl.php",
                                    "session" => $session,
                                    "album_array" => $album_array,
                                    "photo_info_array" => $photo_info_array,
                                    "paginator_page" => $paginator_page,
                                ));
    }
}

$controller = new AlbumListController ();
$controller->run ();
?>

