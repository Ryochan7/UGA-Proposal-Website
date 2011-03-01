<?php
/**
 * File defines the AlbumOptionsController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Album.php"));

/**
 * ADMIN PAGE. Interface to administer album data
 *
 * Read in albums from the database. Displays an interface to administer album data
 * for allowing bulk deletion of albums, deletion of a single
 * album, links to editing each album entry. Available to admins only.
 * @package PageController
 */
class AlbumOptionsController implements Controller {
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
     * Read in albums from the database. Displays an interface to administer album data
     * for allowing bulk deletion of albums, deletion of a single
     * album and links to edit and view each album entry. Pagination enabled.
     * Available to admins only
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? $_GET["page"] : 1;
        if ($page < 1) {
            $page = 1;
        }
        $action = isset ($_GET["action"]) ? trim ($_GET["action"]) : "";

        $albumDAO = AlbumDAO::getInstance ();
        $album_array = $paginator_page = null;
        $content_title = "";
        
        // Check for POST request and necessary data for deletion
        if (!empty ($_POST) && !empty ($_POST["ids"]) && !empty ($_POST["action"])) {
            $action = isset ($_POST["action"]) ? trim ($_POST["action"]) : "";
            if (!strcmp ($action, "delete") == 0) {
                header ("Location: " . BASE_URL);
                return;
            }

            $status = $albumDAO->deleteByIds ($_POST["ids"]);
            if ($status) {
                $session->setMessage ("Selected pages deleted");
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
            else {
                $session->setMessage ("Deletion failed", Session::MESSAGE_ERROR);
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
        }
        // Check for GET request and ids pending deletion
        else if (strcmp ($action, "delete") == 0 && !empty ($_GET["ids"])) {
            $content_title = "Delete Album";
            $album_array = $albumDAO->allByIds ($_GET["ids"]);
        }
        else if (strcmp ($action, "delete") == 0) {
        }
        // Regular GET request
        else {
            $count = $albumDAO->count ();
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $album_array = $albumDAO->all (array ("limit" => $paginator_page));
        }

        $this->template->render (array (
                                    "title" => "Admin - Album Options",
                                    "main_page" => "album_options_tpl.php",
                                    "session" => $session,
                                    "album_array" => $album_array,
                                    "paginator_page" => $paginator_page,
                                    "action" => $action,
                                    "content_title" => $content_title,
                                ));
    }
}

$controller = new AlbumOptionsController ();
$controller->run ();
?>
