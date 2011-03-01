<?php
/**
 * File defines the DeleteEventController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface for event deletion
 *
 * Display confirmation for event deletion. For POST request,
 * check user credentials, check if event exists and then delete entry from database.
 * Available to admins only
 * @package PageController
 */
class DeleteEventController implements Controller {
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
     * Populate template and display confirmation for event deletion. For POST request,
     * check user credentials, check if event exists and then delete entry from database.
     * Available to admins only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        // Check if user is an admin
        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $eventDAO = EventDAO::getInstance ();
        $delete_event = null;
        $form_errors = array ();
        $form_values = array ("id" => "");

        if (!empty ($_POST)) {
            // Check if a number was passed for the id
            $id = isset ($_POST["id"]) ? trim ($_POST["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            // Check if event exists
            else if (is_numeric ($id)) {
                $delete_event = $eventDAO->load ($id);
                // Event exists. Delete
                if ($delete_event) {
                    if ($eventDAO->delete ($delete_event)) {
                        $session->setMessage ("Event deleted");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete event", Session::MESSAGE_ERROR);
                    }
                }
            }
            
        }
        // Read event for confirmation
        else if (!empty ($_GET)) {
            $id = isset ($_GET["id"]) ? trim ($_GET["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_event = $eventDAO->load ($id);
                if ($delete_event) {
                    $form_values["id"] = $delete_event->getId ();
                }
            }
        }
        // Direct file access. Redirect
        else {
            header ("Location: " . BASE_URL);
            return;
        }
        $this->template->render (array (
                                    "title" => "Delete Event",
                                    "main_page" => "delete_event_tpl.php",
                                    "session" => $session,
                                    "delete_event" => $delete_event,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new DeleteEventController ();
$controller->run ();
?>
