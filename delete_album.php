<?php
/**
 * File defines the DeleteAlbumController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Album.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Photo.php"));

/**
 * ADMIN PAGE. Interface for deleting an album entry
 * 
 * Display confirmation for album deletion. For POST request,
 * check user credentials, check if album exists and then delete entry from database.
 * Available to admins only.
 * @package PageController
 */
class DeleteAlbumController implements Controller {
    protected $template;

    public function __construct () {
        $this->template = new PageTemplate ();
    }

    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        
        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $albumDAO = AlbumDAO::getInstance ();
        $delete_album = $album_array = $move_album = null;
        $form_errors = array ();
        $form_values = array ("id" => "", "album_move" => -1);

        if (!empty ($_POST)) {
            $id = isset ($_POST["id"]) ? trim ($_POST["id"]) : "";
            $album_move = (isset ($_POST["album_move"]) && is_numeric ($_POST["album_move"])) ? intval ($_POST["album_move"]) : -1;

            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            if ($album_move > 0) {
                $move_album = $albumDAO->load ($album_move);
            }

            // Move photos to different category
            if (is_numeric ($id) && $move_album) {
                $delete_album = $albumDAO->load ($id);
                if ($delete_album) {
                    $album_photos = $photoDAO->loadByAlbum ($delete_album);
                    if ($albumDAO->delete ($delete_album)) {
                        if ($album_photos) {
                            $photoDAO->moveByAlbumId ($delete_album, $move_album);
                        }
                        $session->setMessage ("Album deleted and photos moved");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete album", Session::MESSAGE_ERROR);
                    }
                }
            }
            // Delete photos in category
            else if (is_numeric ($id) && $album_move == 0) {
                $delete_album = $albumDAO->load ($id);
                if ($delete_album) {
                    $album_photos = $photoDAO->loadByAlbum ($delete_album);
                    if ($albumDAO->delete ($delete_album)) {
                        if ($album_photos) {
                            $photo_ids = array ();
                            foreach ($album_photos as $photo) {
                                $photo_ids[] = $photo->getId ();
                            }
                            $photoDAO->deleteByIds ($photo_ids);
                        }
                        $session->setMessage ("Album and photos deleted");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete album", Session::MESSAGE_ERROR);
                    }
                }
            }
            // Do nothing to photos
            else if (is_numeric ($id)) {
                $delete_album = $albumDAO->load ($id);
                if ($delete_album) {
                    if ($albumDAO->delete ($delete_album)) {
                        $session->setMessage ("Album deleted");
                        header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete album", Session::MESSAGE_ERROR);
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
                $delete_album = $albumDAO->load ($id);
                if ($delete_album) {
                    $form_values["id"] = $delete_album->getId ();
                    $album_array = $albumDAO->allExclude ($delete_album);
                    //print_r ($album_array);
                    //print_r ($delete_album);
                }
            }
        }
        else {
            header ("Location: " . BASE_URL);
            return;
        }

        $this->template->render (array (
                                    "title" => "Admin - Delete Album",
                                    "main_page" => "delete_album_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                    "delete_album" => $delete_album,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                    "album_array" => $album_array,
                                ));
    }
}

$controller = new DeleteAlbumController ();
$controller->run ();
?>
