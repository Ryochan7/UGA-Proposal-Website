<?php
/**
 * File defines the ViewPhotoController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Photo.php"));

/**
 * View photo page
 *
 * Read in the specified photo from the database. Read in album data as well.
 * Display photo in the page.
 * @package PageController
 */
class ViewPhotoController implements Controller {
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
     * Read in the specified photo from the database. Read in album data as well.
     * Populate template and display photo in the page.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $photoDAO = PhotoDAO::getInstance ();
        $photo = $next_photo = $prev_photo = $photo_index = $photo_count = null;
        $title = "";

        if (!empty ($_GET["id"]) && is_numeric ($_GET["id"])) {
            $photo_id = intval ($_GET["id"]);
            $photo = $photoDAO->load ($photo_id, array ("joins" => true));
            if ($photo) {
                $title .= " - {$photo->getTitle ()}";
                // Load next and previous photos as well as position of current photo in album
                $next_photo = $photoDAO->loadNext ($photo);
                $prev_photo = $photoDAO->loadPrevious ($photo);
                $photo_index = $photoDAO->countPosition ($photo, $photo->getAlbum ());
                $photo_count = $photoDAO->countByAlbum ($photo->getAlbum ());
            }
        }

        $this->template->render (array (
                                    "title" => "View Photo" . $title,
                                    "main_page" => "view_photo_tpl.php",
                                    "session" => $session,
                                    "photo" => $photo,
                                    "next_photo" => $next_photo,
                                    "prev_photo" => $prev_photo,
                                    "photo_index" => $photo_index,
                                    "photo_count" => $photo_count,
                                ));
    }
}

$controller = new ViewPhotoController ();
$controller->run ();
?>
