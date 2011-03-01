<?php
/**
 * File defines the EditEventController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Platform.php"));

/**
 * ADMIN PAGE. Interface for editing an event entry
 * 
 * Display form for editing an event entry. For POST requests,
 * check user credentials, check if event exists and then update entry in database.
 * Available to admins only
 * @package PageController
 */
class EditEventController implements Controller {
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
     * Populate template and Display form for editing an event entry. For POST requests,
     * check user credentials, check if event exists and then update entry in database.
     * Available to admins only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        //if (!$user || !$user->isAdmin ()) {
        if (!$user || !$user->validUser ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }
        
        $form_errors = array ();
        $form_values = array ("id" => "", "title" => "", "description" => "", "sanctioned" => "", "status" => "", "date" => "", "platform" => "");
        $eventDAO = EventDAO::getInstance ();
        $event = null;

        if (!empty ($_POST)) {
            $form_values["id"] = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";

            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["description"] = isset ($_POST["description"]) ? trim ($_POST["description"]) : "";
            $form_values["platform"] = isset ($_POST["platform"]) ? trim ($_POST["platform"]) : "";
            $form_values["sanctioned"] = isset ($_POST["sanctioned"]) ? trim ($_POST["sanctioned"]) : "";
            $form_values["status"] = isset ($_POST["status"]) ? trim ($_POST["status"]) : "";
            $form_values["date"] = isset ($_POST["date"]) ? trim ($_POST["date"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "No id specified";
            }
            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }
            if (empty ($form_values["description"])) {
                $form_errors["description"] = "No description specified";
            }
            if (empty ($form_values["platform"])) {
                $form_errors["platform"] = "No platform specified";
            }
            else if (!is_numeric ($form_values["platform"])) {
                $form_errors["platform"] = "Platform choice must be an integer value";
            }
            else {
                $platformDAO = PlatformDAO::getInstance ();
                $platform = $platformDAO->load ($form_values["platform"]);
                if (!$platform) {
                    $form_errors["platform"] = "Invalid platform specified";
                }
            }

            if ($user->isAdmin () && empty ($form_values["sanctioned"])) {
                $form_errors["sanctioned"] = "No sanctioned flag specified";
            }
            else if ($user->isAdmin () && strcmp ($form_values["sanctioned"], "true") != 0 && strcmp ($form_values["sanctioned"], "false") != 0) {
                $form_errors["sanctioned"] = "sanctioned flag must be a boolean value";
            }

            if ($user->isAdmin () && empty ($form_values["status"])) {
                $form_errors["status"] = "No status flag specified";
            }
            else if ($user->isAdmin () && !is_numeric ($form_values["status"])) {
                $form_errors["status"] = "Status flag must be an integer value";
            }
            else if ($user->isAdmin ()) {
                $status = intval ($form_values["status"]);
                $tmp = new Event ();

                try {
                    $tmp->setStatus ($status);
                } catch (Exception $e) {
                    $form_errors["status"] = "Invalid value for status";
                }
            }

            if (empty ($form_values["date"])) {
                $form_errors["date"] = "No date specified";
            }
            else if (strtotime ($_POST["date"]) == 0) {
                $form_errors["date"] = "An invalid date was specified";
                $form_values["date"] = "";
            }

            if (empty ($form_errors)) {
                $event = $eventDAO->load ($form_values["id"]);
                if ($event && ($user->isAdmin () || $event->getUserId () == $user->getId ())) {
                    $event->setTitle ($form_values["title"]);
                    $event->setDescription ($form_values["description"]);
                    $event->setPlatformId (intval ($form_values["platform"]));
                    if ($user->isAdmin () || ($user->validUser () && $user->getUserType () == User::TRUSTED_TYPE)) {
                        $sanctioned_value = (strcmp ($form_values["sanctioned"], "true") == 0) ? true : false;
                        $event->setSanctioned ($sanctioned_value);
                        $event->setStatus ($form_values["status"]);
                    }
                    $pubtimestamp = strtotime ($_POST["date"]);
                    $event->setDate ($pubtimestamp);
                    $event->setUserId ($user->id);
                    //print_r ($event);
                    
                    if ($eventDAO->save ($event)) {
                        // Attempt to ignore for regular admin edits
                        if ($event->getUserId () == $user->getId ()) {
                            require_once (joinPath (INCLUDES_DIR, "models", "Attendance.php"));
                            Attendance::emailAttendees ($event, $user);
                        }
                        $session->setMessage ("Event details saved");
                        header ("Location: edit_event.php?id={$event->getId ()}");
                        return;
                    }
                    else {
                        $session->setMessage ("Event details could not be saved", Session::MESSAGE_ERROR);
                    }
                    
                }
            }
            else if (empty ($form_errors["id"])) {
                $event = $eventDAO->load ($form_values["id"]);
            }
        }
        else if (!empty ($_GET)) {
            $form_values["id"] = isset ($_GET["id"]) ? $_GET["id"] : "";

            if (empty ($form_values["id"])) {
                header ("Location: " . BASE_URL);
                return;
            }
            else {
                $event = $eventDAO->load ($form_values["id"]);
                // Event does not exist. Pass null to template
                if (!$event) {
                }
                // Check for edit permissions
                else if (!$user->isAdmin () && $event->userId != $user->id) {
                    $session->setMessage ("Do not have permission to edit page", Session::MESSAGE_ERROR);
                    header ("Location: " . BASE_URL);
                    return;
                }
                else {
                    $form_values["id"] = $event->getId ();
                    $form_values["title"] = $event->getTitle ();
                    $form_values["description"] = $event->getDescription ();
                    $form_values["sanctioned"] = ($event->getSanctioned () == true) ? "true" : "false";
                    $form_values["status"] = $event->getStatus ();
                    $form_values["date"] = strftime ("%d %B %Y",$event->getDate ());
                    $form_values["platform"] = $event->getPlatformId ();
                }
            }
        }

        $platformDAO = PlatformDAO::getInstance ();
        $platform_array = $platformDAO->all ();

        $this->template->render (array (
                                    "title" => "Edit Event",
                                    "extra_header" => joinPath ("headers", "jscal_header_tpl.php"),
                                    "main_page" => "edit_event_tpl.php",
                                    "session" => $session,
                                    "event" => $event,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                    "platform_array" => $platform_array,
                                ));
    }
}

$controller = new EditEventController ();
$controller->run ();
?>
