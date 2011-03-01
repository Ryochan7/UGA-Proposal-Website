<?php
/**
 * File defines the EditAlbumController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Album.php"));

/**
 * ADMIN PAGE. Interface for editing an album entry
 *
 * Display form for editing an album entry. For POST requests,
 * check user credentials, check if album exists and then update entry in database.
 * Available to admins only
 * @package PageController
 */
class EditAlbumController implements Controller {
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
     * Populate template and display form for editing an album entry. For POST requests,
     * check user credentials, check if album exists and then update entry in database.
     * Available to admins only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $albumDAO = AlbumDAO::getInstance ();
        $album = null;
        $form_errors = array ();
        $form_values = array ("id" => "", "title" => "");

        if (!empty ($_POST)) {
            $form_values["id"] = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "No id specified";
            }
            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }

            if (empty ($form_errors)) {
                $album = $albumDAO->load ($form_values["id"]);
                if ($album) {
                    $album->setTitle ($form_values["title"]);

                    if ($albumDAO->save ($album)) {
                        $session->setMessage ("Album saved");
                        header ("Location: edit_album.php?id={$album->id}");
                        return;
                    }
                    else {
                        $session->setMessage ("Album not saved");
                    }
                }
            }
            else if (empty ($form_errors["id"])) {
                $album = $albumDAO->load ($form_values["id"]);
            }
        }
        else if (!empty ($_GET)) {
            $form_values["id"] = isset ($_GET["id"]) ? $_GET["id"] : "";

            if (empty ($form_values["id"])) {
                header ("Location: " . BASE_URL);
                return;
            }
            else {
                $album = $albumDAO->load ($form_values["id"]);
                // Album does not exist. Pass null to template
                if (!$album) {
                }
                // Only admin can access. Fill form data
                else {
                    $form_values["id"] = $album->getId ();
                    $form_values["title"] = $album->getTitle ();
                }
            }
        }

        
        $this->template->render (array (
                                    "title" => "Edit Album",
                                    "session" => $session,
                                    "main_page" => "edit_album_tpl.php",
                                    "album" => $album,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                ));
    }
}

$controller = new EditAlbumController ();
$controller->run ();
?>
