<?php
/**
 * File defines the UserOptionsController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));

/**
 * ADMIN PAGE. Interface to administer users
 *
 * Read in users from the database. Displays an interface to administer user data
 * for allowing bulk deletion of users, deletion of a single
 * users, links to editing each album entry. Bulk altering of user status is also possible.
 * Available to admins only
 * @package PageController
 */
class UserOptionsController implements Controller {
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
     * Populate template and read in list of users in the database. Populate template and
     * display an interface to administer user data for allowing bulk deletion of users, deletion of a single
     * user, links to editing and viewing each user entry. Available to admins only
     * Available to members only
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();
        
        if ($user == null || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }
        
        $userDAO = UserDAO::getInstance ();
        $method = isset ($_GET["users"]) ? trim ($_GET["users"]) : "";
        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $action = isset ($_GET["action"]) ? trim ($_GET["action"]) : "";
        $page = is_numeric ($page) ? $page : 1;
        $paginator_page = $queryVars = null;

        // POST request for bulk deletion of users
        if (!empty ($_POST) && !empty ($_POST["userids"]) && !empty ($_POST["action"]) && empty ($_POST["domodstatus"])) {
            $action = isset ($_POST["action"]) ? trim ($_POST["action"]) : "";
            if (!strcmp ($action, "delete") == 0) {
                header ("Location: " . BASE_URL);
                return;
            }

            $status = $userDAO->deleteByIds ($_POST["userids"]);
            if ($status) {
                $session->setMessage ("Selected users deleted");
                header ("Location: {$_SERVER["PHP_SELF"]}?users=all");
                return;
            }
            else {
                $session->setMessage ("Deletion failed", Session::MESSAGE_ERROR);
                header ("Location: {$_SERVER["PHP_SELF"]}?users=all");
                return;
            }
        }
        // Alter status of selected users
        else if (!empty ($_GET) && !empty ($_GET["userids"]) && !empty ($_GET["domodstatus"])) {
            $status = isset ($_GET["status"]) ? trim ($_GET["status"]) : "";
            if (!empty ($status)) {
                $status = intval ($status);
                $tmp = new User ();

                try {
                    $tmp->setUserType ($status);
                } catch (InvalidUserTypeException $e) {
                    $session->setMessage ("Invalid status choice");
                    header ("Location: {$_SERVER["PHP_SELF"]}?users=all");
                    return;
                }
            }

            $status = $userDAO->saveStatusByIds ($status, $_GET["userids"]);
            if ($status) {
                $session->setMessage ("Selected users updated");
                header ("Location: {$_SERVER["PHP_SELF"]}?users=all");
                return;
            }
            else {
                $session->setMessage ("Update failed", Session::MESSAGE_ERROR);
                header ("Location: {$_SERVER["PHP_SELF"]}?users=all");
                return;
            }            
        }
        // Check for GET request and ids pending deletion
        else if (strcmp ($action, "delete") == 0 && !empty ($_GET["userids"])) {
            $content_title = "Delete Users";
            $user_array = $userDAO->allByIds ($_GET["userids"]);
        }
        // Read in all users
        else if (strcmp ($method, "all") == 0) {
            $count = $userDAO->count ();
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            if ($page < 0) {
                $page = 1;
            }
            $paginator_page = $paginator->getPage ($page);
            $user_array = $userDAO->all (array ("limit" => $paginator_page, "order" => "userName"));
            $content_title = "All Users Options";
            $queryVars = array ("users" => "all");
        }
        // Only read in list of pending users
        else {
            $user_array = $userDAO->allPendingUsers ();
            $content_title = "Pending Users Options";
        }

        $this->template->render (array (
                                    "title" => "Admin - User Options",
                                    "main_page" => "user_options_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                    "user_array" => $user_array,
                                    "content_title" => $content_title,
                                    "paginator_page" => $paginator_page,
                                    "queryVars" => $queryVars,
                                    "action" => $action,
                                ));
    }
}

$controller = new UserOptionsController ();
$controller->run ();
?>
