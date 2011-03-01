<?php
/**
 * File defines the LoginController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * Login form page and login validation page
 *
 * Display form for entering login data. For POST requests,
 * check if a user exists with the specified password, and enter user id into session if login is valid.
 * @package PageController
 */
class LoginController implements Controller {
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
     * Populate template and display login form. For POST requests,
     * check if a user exists with the specified password, and enter user id into session if login is valid.
     * @access public
     */
    public function run () {
        $form_errors = array ();
        $form_values = array ("username" => "", "password" => "");

        $session = Session::getInstance ();
        $user = $session->getUser ();

        if ($user != null) {
            $session->setMessage ("You are already logged in", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        // Check if form data is being passed
        if (!empty ($_POST)) {
            $form_values["username"] = isset ($_POST["username"]) ? trim ($_POST["username"]) : "";
            $form_values["password"] = isset ($_POST["password"]) ? trim ($_POST["password"]) : "";
            $password = sha1 ($form_values["password"]);
            if (empty ($form_values["username"])) {
                $form_errors["username"] = "A username was not specified";
            }
            if (empty ($form_values["password"])) {
                $form_errors["password"] = "A password was not specified";
            }

            if (empty ($form_errors["username"])) {
                $userDAO = UserDAO::getInstance ();
                $user = $userDAO->loadByUsername ($form_values["username"]);
                if ($user && $user->getStatus () == User::STATUS_OK) {
                    if (strcmp ($user->getPasshash (), $password) != 0) {
                        $form_errors["username"] = "Invalid username or password";
                    }
                }
                else if ($user && $user->getStatus () == User::STATUS_NEEDADMIN) {
                    $form_errors["username"] = "Your user is awaiting admin approval";
                }
                else {
                    $form_errors["username"] = "Invalid username or password";
                }
            }

            if (empty ($form_errors)) {
                $session->setUser ($user);
                $session->setMessage ("Welcome, {$user->getUsername ()}");
                header ("Location: " . BASE_URL);
                return;
            }

        }

        $user = $session->getUser ();
        $this->template->render (array (
                                    "main_page" => "login_tpl.php",
                                    "title" => "Login",
                                    "user" => $user,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                ));
    }
}

$controller = new LoginController ();
$controller->run ();
?>
