<?php
/**
 * File defines the MarkAttendanceController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Attendance.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * MEMBERS ONLY. Alter attendance record for event page
 *
 * POST requests only. Check that the user has a valid session and that a specified event exists. If true,
 * make sure that the user does not already have an Attendance record. If no record exists,
 * create new Attendance record and save it to database.
 * @package PageController
 */
class MarkAttendanceController implements Controller {
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
     * MEMBERS ONLY. POST requests only. Check that the user has a valid session and that a specified event exists. If true,
     * make sure that the user does not already have an Attendance record. If no record exists,
     * create new Attendance record and save it to database.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        if (!$user || !$user->validUser ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $eventDAO = EventDAO::getInstance ();
        $attendDAO = AttendanceDAO::getInstance ();

        if (!empty ($_POST)) {
            $event_id = (isset ($_POST["eventid"]) && is_numeric ($_POST["eventid"])) ? intval ($_POST["eventid"]) : 0;
            $action = isset ($_POST["action"]) ? $_POST["action"] : "";

            if (empty ($event_id) || $event_id < 0) {
                $session->setMessage ("Invalid event id", Session::MESSAGE_ERROR);
                header ("Location: " . BASE_URL);
                return;
            }
            $event = $eventDAO->load ($event_id);
            if (!$event) {
                $session->setMessage ("Event could not be found", Session::MESSAGE_ERROR);
                header ("Location: " . BASE_URL);
                return;
            }

            if ($action && strcmp ($action, "remove") == 0) {
                $attend = $attendDAO->loadExists ($event, $user);
                if (!$attend) {
                    $session->setMessage ("You are not marked as attending", Session::MESSAGE_ERROR);
                    header ("Location: {$event->getAbsoluteUrl ()}");
                    return;
                }

                if ($attendDAO->delete ($attend)) {
                    $session->setMessage ("You are no longer as attending");
                    header ("Location: {$event->getAbsoluteUrl ()}");
                    return;
                }
                else {
                    $session->setMessage ("Request for attendance removal failed", Session::MESSAGE_ERROR);
                    header ("Location: {$event->getAbsoluteUrl ()}");
                    return;
                }

            }
            else {
                $attend = $attendDAO->loadExists ($event, $user);
                if ($attend) {
                    $session->setMessage ("You are already marked as attending", Session::MESSAGE_ERROR);
                    header ("Location: {$event->getAbsoluteUrl ()}");
                    return;
                }

                $attend = new Attendance ();
                $attend->setEventId ($event->id);
                $attend->setUserId ($user->id);

                if ($attendDAO->insert ($attend)) {
                    $session->setMessage ("You are now marked as attending");
                    header ("Location: {$event->getAbsoluteUrl ()}");
                    return;
                }
                else {
                    $session->setMessage ("Request for attendance failed", Session::MESSAGE_ERROR);
                    header ("Location: {$event->getAbsoluteUrl ()}");
                    return;
                }
            }
        }

        header ("Location: " . BASE_URL);
        return;
    }
}

$controller = new MarkAttendanceController ();
$controller->run ();
?>
