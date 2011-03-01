<?php
/**
 * File defines the LogoutController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * Logout page
 *
 * MEMBERS ONLY. If a user has a valid session, kill old session data and start new anonymous session. Display
 * logout status in page.
 * @package PageController
 */
class LogoutController implements Controller {
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
     * If a user has a valid session, kill old session data and start new anonymous session.
     * Populate template and display logout status in page.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        if ($user == null) {
            $session->setMessage ("Not currently logged in", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $session->kill ();
        $user = $session->getUser ();

        $this->template->render (array (
                                    "main_page" => "logout_tpl.php",
                                    "title" => "Logged out",
                                    "user" => $user,
                                ));
    }
}

$controller = new LogoutController ();
$controller->run ();
?>
