<?php
/**
 * File defines the EditProfileController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN AND USER PAGE (ADMIN WILL HAVE ACCESS TO EDIT MORE FIELDS). Interface for editing a user profile entry
 * 
 * Display form for editing an profile entry. For POST requests,
 * check user credentials, check if profile exists and then update entry in database.
 * Available to members only
 * @package PageController
 */
class EditProfileController implements Controller {
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
     * Populate template and display form for editing an profile entry. For POST requests,
     * check user credentials, check if profile exists and then update entry in database.
     * Available to members only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        
        if ($user == null || !$user->validUser ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $userDAO = UserDAO::getInstance ();
        $alter_user = null;
        $form_errors = array ();
        $form_values = array ("id" => "", "password" => "", "password2" => "", "status" => "", "usertype" => "", "steamId" => "", "xboxId" => "", "psnId" => "", "wiiId" => "");
        // Check form
        if (!empty ($_POST)) {
            $form_values["id"] = isset ($_POST["id"]) ? trim ($_POST["id"]) : "";
            $form_values["password"] = isset ($_POST["password"]) ? trim ($_POST["password"]) : "";
            $form_values["password2"] = isset ($_POST["password2"]) ? trim ($_POST["password2"]) : "";
            $form_values["status"] = isset ($_POST["status"]) ? trim ($_POST["status"]) : "";
            $form_values["usertype"] = isset ($_POST["usertype"]) ? trim ($_POST["usertype"]) : "";

            $form_values["steamId"] = isset ($_POST["steamId"]) ? trim ($_POST["steamId"]) : "";
            $form_values["xboxId"] = isset ($_POST["xboxId"]) ? trim ($_POST["xboxId"]) : "";
            $form_values["psnId"] = isset ($_POST["psnId"]) ? trim ($_POST["psnId"]) : "";
            $form_values["wiiId"] = isset ($_POST["wiiId"]) ? trim ($_POST["wiiId"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "User id not set";
            }
            if (empty ($form_values["password"]) && empty ($form_values["password2"])) {
            }

            else if (empty ($form_values["password"])) {
                $form_errors["password"] = "Passwords not set";
            }
            else if (empty ($form_values["password2"])) {
                $form_errors["password"] = "Passwords not set";
            }
            else if (strcmp ($form_values["password"], $form_values["password2"]) != 0) {
                $form_errors["password"] = "Passwords do not match";
                $form_values["password2"] = "";
            }

            if ($user->isAdmin () && !empty ($form_values["status"])) {
                if (!is_numeric ($form_values["status"])) {
                    $form_errors["status"] = "Status must be a number";
                }
                // Check for valid status flag
                else {
                    $status = intval ($form_values["status"]);
                    $tmp = new User ();

                    try {
                        $tmp->setUserType ($status);
                    } catch (InvalidUserTypeException $e) {
                        $form_errors["status"] = "Invalid value for status";
                    }
                }
            }
            else if ($user->isAdmin () && empty ($form_values["status"])) {
                $form_errors["status"] = "Status not defined";
            }

            if ($user->isAdmin () && !empty ($form_values["usertype"])) {
                if (!is_numeric ($form_values["usertype"])) {
                    $form_errors["usertype"] = "Status must be a number";
                }
                $tmp = new User ();
                try {
                    $tmp->setUserType ($status);
                } catch (InvalidStatusException $e) {
                    $form_errors["usertype"] = "Invalid value for status";
                }
            }
            else if ($user->isAdmin () && !empty ($form_values["usertype"])) {
                $form_errors["usertype"] = "Type not defined";
            }

            // Regular expression check for identities
            if (!empty ($form_values["steamId"])) {
                if (strlen ($form_values["steamId"]) > 20) {
                    $form_errors["steamId"] = "Steam ID too long";
                }
                else if (!preg_match ("/^([A-Za-z0-9_]{3,20})$/", $form_values["steamId"])) {
                    $form_errors["steamId"] = "Steam ID is not valid";
                }
            }

            if (!empty ($form_values["xboxId"])) {
                if (strlen ($form_values["xboxId"]) > 15) {
                    $form_errors["xboxId"] = "Xbox gamertag too long";
                }
                else if (!preg_match ("/^[A-Za-z0-9 ]{3,15}$/", $form_values["xboxId"])) {
                    $form_errors["xboxId"] = "Xbox gamertag is not valid";
                }
            }

            if (!empty ($form_values["psnId"])) {
                if (strlen ($form_values["psnId"]) > 16) {
                    $form_errors["psnId"] = "PSN ID too long";
                }
                else if (!preg_match ("/^([A-Za-z0-9-_]+){3,16}$/", $form_values["psnId"])) {
                    $form_errors["psnId"] = "PSN ID is not valid";
                }
            }

            if (!empty ($form_values["wiiId"])) {
                if (strlen ($form_values["wiiId"]) > 20) {
                    $form_errors["wiiId"] = "Steam Id too long";
                }
                else if (!preg_match ("/^([0-9]{4}[- ][0-9]{4}[- ][0-9]{4}[- ][0-9]{4})$/", $form_values["wiiId"])) {
                    $form_errors["wiiId"] = "Wii Friend Code is not valid";
                }
            }

            // No errors found
            if (empty ($form_errors)) {
                // Status call not done
                $alter_user = $userDAO->load ($form_values["id"]);

                if ($alter_user != null) {
                    if ($session->getUser ()->isAdmin () || ($alter_user->getId () == $session->getUser ()->id)) {
                        if (!empty ($form_values["password"])) {
                            $alter_user->setPassHash (sha1 ($form_values["password"]));
                        }
                        if (!empty ($form_values["status"])) {
                            $alter_user->setStatus (intval ($form_values["status"]));
                        }
                        if (!empty ($form_values["usertype"])) {
                            $alter_user->setUserType (intval ($form_values["usertype"]));
                        }

                        if (!empty ($form_values["steamId"])) {
                            $alter_user->setSteamId ($form_values["steamId"]);
                        }
                        if (!empty ($form_values["xboxId"])) {
                            $alter_user->setXboxId ($form_values["xboxId"]);
                        }
                        if (!empty ($form_values["psnId"])) {
                            $alter_user->setPsnId ($form_values["psnId"]);
                        }
                        if (!empty ($form_values["wiiId"])) {
                            $alter_user->setWiiId ($form_values["wiiId"]);
                        }
                        // Save profile
                        if ($userDAO->save ($alter_user)) {
                            $session->setMessage ("User profile altered");
                            header ("Location: {$_SERVER["PHP_SELF"]}?id={$alter_user->id}");
                            return;
                        }
                        // Save failed
                        else {
                            $session->setMessage ("User profile not altered", Session::MESSAGE_ERROR);
                        }
                    }
                    // User does not exist. Redirect
                    else {
                        header ("Location: " . BASE_URL);
                        return;
                    }
                }
            }
            // Reload object
            else if (empty ($form_errors["id"])) {
                $alter_user = $userDAO->load ($form_values["id"]);
            }
        }
        else if (!empty ($_GET)) {
            $form_values["id"] = isset ($_GET["id"]) ? trim ($_GET["id"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "User id not set";
            }

            if (empty ($form_errors)) {
                $alter_user = $userDAO->load ($form_values["id"]);

                // Value is null so user does not exist. Allow null to be passed to template
                if (!$alter_user) {
                }
                // Admin access approval
                else if ($session->getUser ()->isAdmin ()) {
                    $form_values["steamId"] = $alter_user->getSteamId ();
                    $form_values["xboxId"] = $alter_user->getXboxId ();
                    $form_values["psnId"] = $alter_user->getPsnId ();
                    $form_values["wiiId"] = $alter_user->getWiiId ();
                }
                // Disallow one regular member from altering another member's profile
                else if (!$session->getUser ()->isAdmin () && $alter_user->getId () != $session->getUser ()->getId ()) {
                    $session->setMessage ("Do not have permission", Session::MESSAGE_ERROR);
                    header ("Location: " . BASE_URL);
                    return;
                }
                // Regular user access to own profile
                else {
                    $form_values["steamId"] = $alter_user->getSteamId ();
                    $form_values["xboxId"] = $alter_user->getXboxId ();
                    $form_values["psnId"] = $alter_user->getPsnId ();
                    $form_values["wiiId"] = $alter_user->getWiiId ();
                }
            }
        }
        // Direct file access. Redirect
        else {
            header ("Location: " . BASE_URL);
            return;
        }
        $this->template->render (array (
                                    "title" => "Edit Profile",
                                    "main_page" => "edit_profile_tpl.php",
                                    "session" => $session,
                                    "alter_user" => $alter_user,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new EditProfileController ();
$controller->run ();
?>
