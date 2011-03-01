<?php
/**
 * File defines the CreateAlbumController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Album.php"));

/**
 * ADMIN PAGE. Interface for creating a new album entry
 *
 * Display form for creating a new album entry. For POST request,
 * validate form data and save information to database. Available to admins only
 * @package PageController
 */
class CreateAlbumController implements Controller {
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
     * Populate template and display form for creating a new album entry. For POST request,
     * validate form data and save information to database. Available to admins only
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
        $form_values = array ("title" => "");

        if (!empty ($_POST)) {
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";

            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }

            if (empty ($form_errors)) {
                $album = new Album ();
                $album->setTitle ($form_values["title"]);

                if ($albumDAO->insert ($album)) {
                    $session->setMessage ("Album saved");
                    header ("Location: edit_album.php?id={$album->id}");
                    return;
                }
                else {
                    $session->setMessage ("Album not saved");
                }
            }
        }
        
        $this->template->render (array (
                                    "title" => "Create Album",
                                    "session" => $session,
                                    "main_page" => "create_album_tpl.php",
                                    "album" => $album,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                ));
    }
}

$controller = new CreateAlbumController ();
$controller->run ();
?>
