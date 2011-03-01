<?php
/**
 * File defines the DeletePhotoController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Photo.php"));

/**
 * ADMIN PAGE. Interface for deleting photo entry
 *
 * Display confirmation for photo deletion. For POST requests,
 * check user credentials, check if photo exists and then delete entry from database.
 * Available to admins only
 * @package PageController
 */
class DeletePhotoController implements Controller {
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
     * Populate template and display confirmation for photo deletion. For POST requests,
     * check user credentials, check if photo exists and then delete entry from database.
     * Available to admins only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        
        if ($user == null || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $photoDAO = PhotoDAO::getInstance ();
        $delete_photo = null;
        $form_errors = array ();
        $form_values = array ("id" => "");

        if (!empty ($_POST)) {
            $id = isset ($_POST["id"]) ? trim ($_POST["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_photo = $photoDAO->load ($id);
                if ($delete_photo) {
                    if ($photoDAO->delete ($delete_photo)) {
                        unlink ($delete_photo->getFileLoc ());
                        if ($delete_photo->getThumbLoc ()) {
                            unlink ($delete_photo->getThumbLoc ());
                        }
                        $session->setMessage ("Photo deleted");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete photo", Session::MESSAGE_ERROR);
                    }
                }
            }
            
        }
        else if (!empty ($_GET)) {
            $id = isset ($_GET["id"]) ? trim ($_GET["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_photo = $photoDAO->load ($id);
                if ($delete_photo) {
                    $form_values["id"] = $delete_photo->getId ();
                }
            }
        }
        else {
            header ("Location: " . BASE_URL);
            return;
        }
        $this->template->render (array (
                                    "title" => "Delete Profile",
                                    "main_page" => "delete_photo_tpl.php",
                                    "session" => $session,
                                    "delete_photo" => $delete_photo,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new DeletePhotoController ();
$controller->run ();
?>
