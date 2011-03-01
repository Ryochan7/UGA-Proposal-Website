<?php
/**
 * File defines the UserListController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * MEMBERS ONLY. Display member list with identity data
 *
 * Read in list of users in the database. Allow filtering by online identity
 * and by the first letter of a user name. Display list in the page.
 * Available to members only
 * @package PageController
 */
class UserListController implements Controller {
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
     * Populate template and read in list of users in the database. Allow filtering by online identity
     * and by the first letter of a user name. Display list in the page.
     * Available to members only
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();
        if (!$user || !$user->validUser ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $userDAO = UserDAO::getInstance ();
        $user_array = $paginator_page = null;

        $form_values = array ("identity" => "", "startswith" => "");
        $form_values["identity"] = $identity = isset ($_GET["identity"]) ? trim ($_GET["identity"]) : "";
        $form_values["startswith"] = isset ($_GET["startswith"]) ? trim ($_GET["startswith"]) : "";

        $identity_array = array ("steam", "xbox", "psn", "wii");
        $queryVars = array ();

        if ($identity) {
            $found = false;
            for ($i = 0; $i < count ($identity_array) && !$found; $i++) {
                if (strcmp ($identity, $identity_array[$i]) == 0) {
                    $paginator = new Paginator ($userDAO->countIdentity ($identity), $PAGINATION_LIMIT);
                    $paginator_page = $paginator->getPage ($page);
                    $user_array = $userDAO->allByIdentity ($identity, array ("limit" => $paginator_page, "order" => "userName ASC"));
                    $found = true;
                }
            }
            $queryVars["identity"] = $form_values["identity"];
        }
        else if (!empty ($form_values["startswith"]) && preg_match ("/^[a-z]/", $form_values["startswith"])) {
            $paginator = new Paginator ($userDAO->countLetter ($form_values["startswith"]), $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $user_array = $userDAO->allByLetter ($form_values["startswith"], array ("limit" => $paginator_page, "order" => "userName ASC"));
            $queryVars["startswith"] = $form_values["startswith"];
        }
        else {
            $paginator = new Paginator ($userDAO->count (), $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $user_array = $userDAO->all (array ("limit" => $paginator_page, "order" => "userName ASC"));
        }

        $this->template->render (array (
                                    "title" => "View Userlist",
                                    "main_page" => "user_list_tpl.php",
                                    "user_array" => $user_array,
                                    "session" => $session,
                                    "paginator_page" => $paginator_page,
                                    "form_values" => $form_values,
                                    "queryVars" => $queryVars,
                                ));
    }
}

$controller = new UserListController ();
$controller->run ();
?>
