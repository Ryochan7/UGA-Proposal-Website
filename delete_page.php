<?php
/**
 * File defines the DeletePageController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Page.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface for deleting a page entry
 * 
 * Display confirmation for page deletion. For POST request,
 * check user credentials, check if page exists and then delete entry from database.
 * Available to admins only.
 * @package PageController
 */
class DeletePageController implements Controller {
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
     * Populate template and display confirmation for page deletion. For POST requests,
     * check user credentials, check if page exists and then delete entry from database.
     * Available to admins only.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        
        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $pageDAO = PageDAO::getInstance ();
        $delete_page = null;
        $form_errors = array ();
        $form_values = array ("id" => "");

        if (!empty ($_POST)) {
            $id = isset ($_POST["id"]) ? trim ($_POST["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_page = $pageDAO->load ($id);
                if ($delete_page) {
                    if ($pageDAO->delete ($delete_page)) {
                        $session->setMessage ("Page deleted");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete page", Session::MESSAGE_ERROR);
                    }
                }
            }
            
        }
        else if (!empty ($_GET)) {
            $id = isset ($_GET["id"]) ? trim ($_GET["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_page = $pageDAO->load ($id);
                if ($delete_page) {
                    $form_values["id"] = $delete_page->getId ();
                }
            }
        }
        else {
            header ("Location: " . BASE_URL);
            return;
        }
        $this->template->render (array (
                                    "title" => "Admin - Delete Page",
                                    "main_page" => "delete_page_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                    "delete_page" => $delete_page,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new DeletePageController ();
$controller->run ();
?>
