<?php
/**
 * File defines the DeleteProfileController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface for deleting profile entry
 *
 * Display confirmation for profile deletion. For POST requests,
 * check user credentials, check if profile exists and then delete entry from database.
 * Available to admins only
 * @package PageController
 */
class DeleteProfileController implements Controller {
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
     * Populate template and display confirmation for profile deletion. For POST requests,
     * check user credentials, check if profile exists and then delete entry from database.
     * Available to admins only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        
        if ($user == null || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $userDAO = UserDAO::getInstance ();
        $delete_user = null;
        $form_errors = array ();
        $form_values = array ("id" => "");

        if (!empty ($_POST)) {
            $id = isset ($_POST["id"]) ? trim ($_POST["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_user = $userDAO->load ($id);
                if ($delete_user) {
                    if ($userDAO->delete ($delete_user)) {
                        $session->setMessage ("User deleted");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete user", Session::MESSAGE_ERROR);
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
                $delete_user = $userDAO->load ($id);
                if ($delete_user) {
                    $form_values["id"] = $delete_user->getId ();
                }
            }
        }
        else {
            header ("Location: " . BASE_URL);
            return;
        }
        $this->template->render (array (
                                    "title" => "Delete Profile",
                                    "main_page" => "delete_profile_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                    "delete_user" => $delete_user,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new DeleteProfileController ();
$controller->run ();
?>
