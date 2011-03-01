<?php
/**
 * File defines the PageOptionsController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Page.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface to administer page data
 *
 * Read in pages from the database. Displays an interface to administer page data
 * for allowing bulk deletion of pages, deletion of a single
 * page, links to editing each page entry. Available to admins only
 * @package PageController
 */
class PageOptionsController implements Controller {
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
     * Read in pages from the database. Populate template and display an interface to
     * administer page data for allowing bulk deletion of pages, deletion of a single
     * page, links to editing each page entry. Available to admins only
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

        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $action = isset ($_GET["action"]) ? trim ($_GET["action"]) : "";

        $pageDAO = PageDAO::getInstance ();
        $page_array = $paginator_page = null;
        $content_title = "Page Options";
        
        if (!empty ($_POST) && !empty ($_POST["ids"]) && !empty ($_POST["action"])) {
            $action = isset ($_POST["action"]) ? trim ($_POST["action"]) : "";
            if (!strcmp ($action, "delete") == 0) {
                header ("Location: " . BASE_URL);
                return;
            }

            $status = $pageDAO->deleteByIds ($_POST["ids"]);
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
        else if (strcmp ($action, "delete") == 0 && !empty ($_GET["ids"])) {
            $content_title = "Delete Pages";
            $page_array = $pageDAO->allByIds ($_GET["ids"]);
        }
        else if (strcmp ($action, "delete") == 0) {
        }
        else {
            $count = $pageDAO->count ();
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $page_array = $pageDAO->all (array ("limit" => $paginator_page));
        }

        $this->template->render (array (
                                    "title" => "Admin - {$content_title}",
                                    "main_page" => "page_options_tpl.php",
                                    "session" => $session,
                                    "page_array" => $page_array,
                                    "paginator_page" => $paginator_page,
                                    "action" => $action,
                                    "content_title" => $content_title,
                                ));
    }
}

$controller = new PageOptionsController ();
$controller->run ();
?>
