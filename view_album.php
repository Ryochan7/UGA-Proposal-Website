<?php
/**
 * File defines the ViewAlbumController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Album.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Photo.php"));

/**
 * View album page
 * 
 * Read in album information and photos associated with an album from the database.
 * Display results in the page.
 * @package PageController
 */
class ViewAlbumController implements Controller {
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
     * Read in album information and photos associated with an album from the database.
     * Populate template and display results in the page. Pagination possible
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();
        $albumDAO = AlbumDAO::getInstance ();
        $photoDAO = PhotoDAO::getInstance ();
        
        $album = $photo_array = $photo_count = $paginator_page = $queryVars = null;
        $title = "";
        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }

        $id = (isset ($_GET["id"]) && is_numeric ($_GET["id"])) ? intval ($_GET["id"]) : 0;
        if ($id <= 0) {
            header ("Location: " . BASE_URL);
            return;
        }

        $album = $albumDAO->load ($id, array ("joins" => true));

        if ($album) {
            $title = $album->getTitle ();
            $count = $photoDAO->countByAlbum ($album);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $photo_array = $photoDAO->allByAlbum ($album, array ("limit" => $paginator_page));
            $queryVars = array ("id" => $id);
        }

        $this->template->render (array (
                                    "title" => "View Album - {$title}",
                                    "session" => $session,
                                    "album" => $album,
                                    "photo_array" => $photo_array,
                                    "paginator_page" => $paginator_page,
                                    "queryVars" => $queryVars,
                                    "main_page" => "view_album_tpl.php",
                                ));
    }
}

$controller = new ViewAlbumController ();
$controller->run ();
?>
