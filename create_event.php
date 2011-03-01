<?php
/**
 * File defines the CreateEventController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Platform.php"));

/**
 * MEMBERS ONLY. Interface for creating a new article entry
 * 
 * Display form for creating a new event entry. Regular users are allowed to create events but an
 * admin must approve them before they are visible on the site. Trusted users are allowed to create
 * events that will immediately be visible on the event calendar. For POST request,
 * validate form data and save information to database. Available to members only
 * @package PageController
 */
class CreateEventController implements Controller {
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
     * Populate template and display form for creating a new event entry. Regular users are allowed to create events but an
     * admin must approve them before they are visible on the site. Trusted users are allowed to create
     * events that will immediately be visible on the event calendar. For POST request,
     * validate form data and save information to database. Available to members only
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
        $form_values = array ("title" => "", "description" => "", "sanctioned" => "", "status" => "", "date" => "", "platform" => "");
        $eventDAO = EventDAO::getInstance ();
        //$event_array = $eventDAO->all ();

        if (!empty ($_POST)) {
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["description"] = isset ($_POST["description"]) ? trim ($_POST["description"]) : "";
            $form_values["platform"] = isset ($_POST["platform"]) ? trim ($_POST["platform"]) : "";
            $form_values["sanctioned"] = isset ($_POST["sanctioned"]) ? trim ($_POST["sanctioned"]) : "";
            $form_values["status"] = isset ($_POST["status"]) ? trim ($_POST["status"]) : "";
            $form_values["date"] = isset ($_POST["date"]) ? trim ($_POST["date"]) : "";

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
                $platform = intval ($form_values["platform"]);
                $tmp = new Event ();

                try {
                    $tmp->setPlatformId ($platform);
                } catch (Exception $e) {
                    $form_errors["platform"] = "Invalid value for platform";
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
                $event = new Event ();
                $event->setTitle ($form_values["title"]);
                $event->setDescription ($form_values["description"]);
                $event->setPlatformId (intval ($form_values["platform"]));
                if ($user->isAdmin () || ($user->validUser () && $user->getUserType () == User::TRUSTED_TYPE)) {
                    $sanctioned_value = (strcmp ($form_values["sanctioned"], "true") == 0) ? true : false;
                    $event->setSanctioned ($sanctioned_value);
                    $event->setStatus ($form_values["status"]);
                }
                else if ($user->validUser ()) {
                    $event->setSanctioned (false);
                    $event->setStatus (Event::PENDING_STATUS);
                }
                $pubtimestamp = strtotime ($_POST["date"]);
                $event->setDate ($pubtimestamp);
                $event->setUserId ($user->id);
                //print_r ($event);
                
                if ($eventDAO->insert ($event)) {
                    $session->setMessage ("Event details saved");
                    header ("Location: edit_event.php?id={$event->id}");
                    return;
                }
                else {
                    $session->setMessage ("Event details could not be saved", Session::MESSAGE_ERROR);
                }
                
            }
        }

        $platformDAO = PlatformDAO::getInstance ();
        $platform_array = $platformDAO->all ();

        $this->template->render (array (
                                    "title" => "Create Event",
                                    "extra_header" => joinPath ("headers", "jscal_header_tpl.php"),
                                    "main_page" => "create_event_tpl.php",
                                    "session" => $session,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                    "platform_array" => $platform_array,
                                ));
    }
}

$controller = new CreateEventController ();
$controller->run ();
?>
