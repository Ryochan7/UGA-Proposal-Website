<?php
/**
 * File defines the RegisterController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));
require_once (joinPath (INCLUDES_DIR, "models", "AuthToken.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));
require_once "Mail.php";

/**
 * Registration page for application
 *
 * Display a form for allowing a user to start the registration process. For POST requests,
 * check if the requests user name already exists in the database. If not, create a new User and AuthToken
 * and add them into the database. Afterwards, display status message to user and
 * email the user with a link to the page that the user can
 * use to continue the process.
 * @package PageController
 */
class RegisterController implements Controller {
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
     * Populate template and display form for registration. For POST requests, check if the user
     * already exists. If not, create new User and AuthToken entries and send an email notification to the user
     * @access public
     */
    public function run () {
        $form_errors = array ();
        $form_values = array ("username" => "", "password" => "", "password2" => "", "ulid" => "");
        $session = Session::getInstance ();
        $user = $session->getUser ();

        // Session should not have a defined user
        if ($user != null) {
            $session->setMessage ("You are already a user", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }
        if (!empty ($_POST)) {
            $form_values["username"] = isset ($_POST["username"]) ? trim ($_POST["username"]) : "";
            $form_values["password"] = isset ($_POST["password"]) ? trim ($_POST["password"]) : "";
            $form_values["password2"] = isset ($_POST["password2"]) ? trim ($_POST["password2"]) : "";
            $form_values["ulid"] = isset ($_POST["ulid"]) ? trim ($_POST["ulid"]) : "";

            if (empty ($form_values["username"])) {
                $form_errors["username"] = "No username specified";
            }
            if (empty ($form_values["password"])) {
                $form_errors["password"] = "No password specified";
            }
            if (empty ($form_values["password2"])) {
                $form_errors["password"] = "Password must be entered twice";
            }
            if (empty ($form_values["ulid"])) {
                $form_errors["ulid"] = "No ulid specified";
            }
            else if (!preg_match ("/[a-z]{5,7}/", $form_values["ulid"])) {
                $form_errors["ulid"] = "Ulid is not in the proper format.";
            }

            $userDAO = UserDAO::getInstance ();
            $user = $userDAO->loadByUsername ($form_values["username"]);
            // User already exists
            if ($user != null) {
                $form_errors["username"] = "User already exists";
            }
            if (strcmp ($form_values["password"], $form_values["password2"]) != 0) {
                $form_errors["password"] = "Passwords do not match";
            }
            $user = $userDAO->loadByUlid ($form_values["ulid"]);
            // User already exists
            if ($user != null) {
                $form_errors["ulid"] = "Ulid is already registered";
            }

            if (empty ($form_errors)) {
                $user = new User ();
                $user->setUsername ($form_values["username"]);
                $user->setPassHash (sha1 ($form_values["password"]));
                $user->setUlid ($form_values["ulid"]);
                $status = $userDAO->insert ($user);
                if ($status) {
                    $token = new AuthToken ();
                    $token->setUser ($user);
                    $tokenDAO = AuthTokenDAO::getInstance ();
                    $status = $tokenDAO->insert ($token);
                    if ($status) {
                        $session->setMessage ("Registration started. Check your email for a message to continue");
                        if (defined ("SMTP_HOST") && strcmp (SMTP_HOST, "") != 0) {
                            $from_addr = EMAIL_ADDRESS;
                            //$to = "tanickl@ilstu.edu";
                            $to = "{$form_values["ulid"]}@" . User::ISU_EMAIL_DOMAIN;
                            $subject = "Verify registration with " . SITE_NAME;
                            $body = "To start the next step of the registration process, click the verify link below and enter the requested information. If the URL does not appear as a link, copy the URL, paste it into your browser's address bar and proceed to the web page.\n\n" . joinPath (BASE_URL, "verify.php") . "?token={$token->getToken ()}\n";

                            $headers = array ("From" => $from_addr, "To" => $to, "Subject" => $subject);
                            $stmp = Mail::factory ("smtp", array ("host" => SMTP_HOST, "auth" => true, "username" => SMTP_USERNAME, "password" => SMTP_PASSWORD));
                            $mail = $stmp->send ($to, $headers, $body);
                        }
                        header ("Location: " . BASE_URL);
                        return;
                    }
                }
            }
        }

        $user = $session->getUser ();
        $this->template->render (array (
                                    "title" => "Register",
                                    "main_page" => "register_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new RegisterController ();
$controller->run ();
?>
