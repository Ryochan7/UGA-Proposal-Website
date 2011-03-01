<?php
/**
 * File defines the EventOptionsController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface to administer album data
 * 
 * Read in events from the database. Displays an interface to administer event data
 * for allowing bulk deletion of events, deletion of a single
 * event, links to editing and viewing each event entry.
 * Available to admins only
 * @package PageController
 */
class EventOptionsController implements Controller {
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
     * Read in events from the database. Populate template and display an interface to administer event data
     * for allowing bulk deletion of events, deletion of a single
     * event, links to editing and viewing each event entry.
     * Available to admins only
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        // Check for admin user
        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $action = isset ($_GET["action"]) ? trim ($_GET["action"]) : "";

        $eventDAO = EventDAO::getInstance ();
        $event_array = $paginator_page = null;
        $content_title = "";

        // Check for POST request and necessary variable for deletion
        if (!empty ($_POST) && !empty ($_POST["ids"]) && !empty ($_POST["action"]) && empty ($_POST["domodstatus"])) {
            $action = isset ($_POST["action"]) ? trim ($_POST["action"]) : "";
            if (!strcmp ($action, "delete") == 0) {
                header ("Location: " . BASE_URL);
                return;
            }

            $status = $eventDAO->deleteByIds ($_POST["ids"]);
            if ($status) {
                $session->setMessage ("Selected events deleted");
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
            else {
                $session->setMessage ("Deletion failed", Session::MESSAGE_ERROR);
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
        }
        // Check for POST request and necessary variables to alter status
        else if (!empty ($_GET) && !empty ($_GET["ids"]) && !empty ($_GET["domodstatus"])) {
            $status = isset ($_GET["status"]) ? trim ($_GET["status"]) : "";
            if (!empty ($status)) {
                $status = intval ($status);
                $tmp = new Event ();

                try {
                    $tmp->setStatus ($status);
                } catch (Exception $e) {
                    $session->setMessage ("Invalid status choice");
                    header ("Location: {$_SERVER["PHP_SELF"]}");
                    return;
                }
            }

            $status = $eventDAO->saveStatusByIds ($status, $_GET["ids"]);
            if ($status) {
                $session->setMessage ("Selected events updated");
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
            else {
                $session->setMessage ("Update failed", Session::MESSAGE_ERROR);
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }

        }
        // Check for GET request and ids to mark for deletion
        else if (strcmp ($action, "delete") == 0 && !empty ($_GET["ids"])) {
            $content_title = "Delete Events";
            $event_array = $eventDAO->allByIds ($_GET["ids"]);
        }
        else if (strcmp ($action, "delete") == 0) {
        }
        // Regular GET request. Grab event array
        else {
            $count = $eventDAO->count ();
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->all (array ("limit" => $paginator_page, "joins" => true));
        }

        $this->template->render (array (
                                    "title" => "Admin - Event Options",
                                    "main_page" => "event_options_tpl.php",
                                    "session" => $session,
                                    "event_array" => $event_array,
                                    "paginator_page" => $paginator_page,
                                    "action" => $action,
                                    "content_title" => $content_title,
                                ));
    }
}

$controller = new EventOptionsController ();
$controller->run ();
?>
