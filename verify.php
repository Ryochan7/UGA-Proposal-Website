<?php
/**
 * File defines the VerifyController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "AuthToken.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));
require_once "Mail.php";

/**
 * Verification page. Display verification form and validate form input
 * 
 * Display a form for a user to confirm his/her user identity that was previously stored in the
 * database. For POST requests, check that an AuthToken exists and that the user credentials entered in
 * the form match the credentials of the user stored in the database. If true,
 * alter the user's status to NEEDADMIN and make a session message indicating the next step in the process.
 * @package PageController
 */
class VerifyController implements Controller {
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
     * Display a form for a user to confirm his/her user identity that was previously stored in the
     * database. For POST requests, check that an AuthToken exists and that the user credentials entered in
     * the form match the credentials of the user stored in the database. If true,
     * alter the user's status to NEEDADMIN and make a session message indicating the next step in the process.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();

        // Session should not have a defined user
        if ($session->getUser () != null) {
            $session->setMessage ("You are already a user", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }
        $form_errors = array ();
        $form_values = array ("username" => "", "password" => "", "token" => "");
        $tokenDAO = AuthTokenDAO::getInstance ();
        // Do garbage collection on token table
        //$tokenDAO->garbageCollect ();
        //return;

        // Register form
        if (!empty ($_POST)) {
            $form_values["username"] = isset ($_POST["username"]) ? trim ($_POST["username"]) : "";
            $form_values["password"] = isset ($_POST["password"]) ? trim ($_POST["password"]) : "";
            $form_values["token"] = isset ($_POST["token"]) ? trim ($_POST["token"]) : "";

            if (empty ($form_values["username"])) {
                $form_errors["username"] = "No username provided";
            }
            if (empty ($form_values["password"])) {
                $form_errors["password"] = "No password provided";
            }

            if (empty ($form_values["token"])) {
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;
            }

            $token = $tokenDAO->loadByToken ($form_values["token"], array ("joins" => true));
            // No corresponding token exists
            if ($token == null) {
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;
            }
            // Token has expired
            else if ($token->getExpireTime () < (time () - AuthToken::MAX_EXPIRE)) {
                $userDAO->delete ($token->getUser ());
                $tokenDAO->delete ($token);
                $session->setMessage ("Token has expired. Profile has been deleted");
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;
            }

            // Check password and status of pending user
            $user = $token->getUser ();
            $pass_hash = sha1 ($form_values["password"]);
            if (strcmp ($user->getUsername (), $form_values["username"]) != 0) {
                $form_errors["username"] = "User does not exist";
            }
            else if (strcmp ($user->getPasshash (), $pass_hash) != 0) {
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;
            }
            // User is already authenticated. Return
            else if ($user->getStatus () == User::STATUS_OK) {
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;
            }

            // Form and token are valid. Change user status
            if (empty ($form_errors)) {
                $user->setStatus (User::STATUS_NEEDADMIN);
                $user->setUserType (User::REGUSER_TYPE);
                $userDAO = UserDAO::getInstance ();
                if (!$userDAO->save ($user)) {
                    $session->setMessage ("Could not alter profile");
                }
                else {
                    //$session->setUser ($user);
                    $session->setMessage ("Now awaiting admin approval");
                    $tokenDAO->delete ($token);
                }
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;
            }
        }
        // Initial form request for token
        else if (!empty ($_GET)) {
            $token_string = isset ($_GET["token"]) ? trim ($_GET["token"]) : "";
            $form_values["token"] = $token_string;

            if (empty ($token_string)) {
                $tokenDAO->garbageCollect ();
                header ("Location: " . BASE_URL);
                return;                
            }
            else {
                $token = $tokenDAO->loadByToken ($token_string, array ("joins" => true));
                // Token does not exist. Redirect
                if ($token == null) {
                    $tokenDAO->garbageCollect ();
                    header ("Location: " . BASE_URL);
                    return;
                }
                // Associated user has no need to use this page
                else if ($token->getUser ()->getStatus () != User::STATUS_PENDING) {
                    $tokenDAO->garbageCollect ();
                    header ("Location: " . BASE_URL);
                    return;
                }
                // Token has expired
                else if ($token->getExpireTime () < (time () - AuthToken::MAX_EXPIRE)) {
                    $userDAO->delete ($token->getUser ());
                    $tokenDAO->delete ($token);
                    $session->setMessage ("Token has expired. Profile has been deleted", Session::MESSAGE_ERROR);
                    $tokenDAO->garbageCollect ();
                    header ("Location: " . BASE_URL);
                    return;
                }
            }
        }
        // Request for file directly. Redirect
        else {
            header ("Location: " . BASE_URL);
            return;
        }

        // Do garbage collection on token table
        $tokenDAO->garbageCollect ();
        $this->template->render (array (
                                    "title" => "Verify Account",
                                    "main_page" => "verify_tpl.php",
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                ));
    }
}

$controller = new VerifyController ();
$controller->run ();
?>
