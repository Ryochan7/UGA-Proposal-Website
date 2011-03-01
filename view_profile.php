<?php
/**
 * File defines the ViewProfileController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * MEMBERS ONLY. View user profile page
 *
 * Read in the specified profile from the database. Check if the current visitor is a valid user
 * and redirect if the user is not. If the user is valid,
 * display profile details in the page. Available to members only
 * @package PageController
 */
class ViewProfileController implements Controller {
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
     * Read in the specified profile from the database. Check if the current visitor is a valid user
     * and redirect if the user is not. If the user is valid,
     * populate template and display profile details in the page. Available to members only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        // Check for a valid user
        if ($user == null || !$user->validUser ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $userDAO = UserDAO::getInstance ();
        $user = null;
        $title = "";

        if (!empty ($_GET["id"]) && is_numeric ($_GET["id"])) {
            $user_id = intval ($_GET["id"]);
            $user = $userDAO->load ($user_id);
            if ($user) {
                $title .= " - {$user->getUserName ()}";
            }
        }

        $this->template->render (array (
                                    "title" => "View Profile" . $title,
                                    "main_page" => "view_profile_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                ));
    }
}

$controller = new ViewProfileController ();
$controller->run ();
?>
