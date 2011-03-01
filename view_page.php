<?php
/**
 * File defines the ViewPageController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Page.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * Display custom page
 *
 * Read in the specified page from the database.
 * Display the contents of the page. Overwrite template file choice if necessary.
 * @package PageController
 */
class ViewPageController implements Controller {
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
     * Read in the specified page from the database.
     * Populate template and display the contents of the page. Overwrite template file choice if necessary.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        $pageDAO = PageDAO::getInstance ();
        
        $id = (isset ($_GET["id"]) && is_numeric ($_GET["id"])) ? intval ($_GET["id"]) : 0;
        if ($id <= 0) {
            header ("Location: " . BASE_URL);
            return;
        }

        $template = "view_page_tpl.php";
        $title = "Not Found";
        $page = $pageDAO->load ($id, array ("joins" => true));

        if ($page) {
            // Only allow admin to view unpublished pages
            if (!$page->getPublished () && $user && !$user->isAdmin ()) {
                $page = null;
            }
            else {
                // Custom template file is defined. Create new PageTemplate instance using defined file
                if ($page->getTemplate ()) {
                    $this->template = new PageTemplate ($page->getTemplate ());
                    $title = $page->title;
                    $template = "";
                }
                // Use standard template
                else {
                    $title = $page->title;
                }
            }
        }

        $this->template->render (array (
                                    "title" => $title,
                                    "session" => $session,
                                    "page" => $page,
                                    "main_page" => $template,
                                ));
    }
}

$controller = new ViewPageController ();
$controller->run ();
?>
