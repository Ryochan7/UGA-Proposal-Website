<?php
/**
 * File defines the ViewEventController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Attendance.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * View event details page
 *
 * Read in the specified event from the database.
 * Display event details in the page. Allow admin preview of un-approved event
 * @package PageController
 */
class ViewEventController implements Controller {
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
     * Read in the specified event from the database.
     * Populate template and display event details in the page. Allow admin preview of un-approved event
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $eventDAO = EventDAO::getInstance ();
        $attendDAO = AttendanceDAO::getInstance ();

        $title = "";
        $event = $attending = $attend_array = null;
        $attend_count = null;

        if (!empty ($_GET["id"]) && is_numeric ($_GET["id"])) {
            $id = intval ($_GET["id"]);
            $event = $eventDAO->load ($id, array ("joins" => true));
            // Check if event is approved
            if ($event && $event->status == Event::APPROVED_STATUS) {
                $title .= " - {$event->title}";
                if ($user) {
                    $attending = $attendDAO->loadExists ($event, $user);
                }
                $attend_count = $attendDAO->countByEvent ($event);
                $attend_array = $attendDAO->allByEvent ($event, array ("joins" => true, "order" => "id DESC"));
            }
            // Allow admin preview access to non-approved events
            else if ($event && $session->getUser () && $session->getUser ()->isAdmin ()) {
                $title .= " - {$event->title}";
                $attending = $attendDAO->loadExists ($event, $user);
                $attend_count = $attendDAO->countByEvent ($event);
                $attend_array = $attendDAO->allByEvent ($event, array ("joins" => true, "order" => "id DESC"));
            }
            // Event does not exist
            else {
                $event = null;
            }
        }

        $this->template->render (array (
                                    "title" => "Event Details" . $title,
                                    "main_page" => "view_event_tpl.php",
                                    "session" => $session,
                                    "event" => $event,
                                    "attending" => $attending,
                                    "attend_array" => $attend_array,
                                    "attend_count" => $attend_count,
                                ));
    }
}

$controller = new ViewEventController ();
$controller->run ();
?>
