<?php
/**
 * File defines the EditPhotoController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Photo.php"));
require_once (joinPath (INCLUDES_DIR, "phpThumb", "phpthumb.class.php"));

/**
 * ADMIN PAGE. Interface for editing a photo entry
 *
 * Display form for editing an photo entry. For POST requests,
 * check user credentials, check if photo exists and then update entry in database.
 * Available to admins only
 * @package PageController
 */
class EditPhotoController implements Controller {
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
     * Populate template and display form for editing an photo entry. For POST requests,
     * check user credentials, check if photo exists and then update entry in database.
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

        $photoDAO = PhotoDAO::getInstance ();
        $albumDAO = AlbumDAO::getInstance ();
        $photo = null;
        $form_errors = array ();
        $form_values = array ("id" => "", "albumid" => "", "title" => "", "description" => "");

        if (!empty ($_POST)) {
            $form_values["id"] = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";
            $form_values["albumid"] = (isset ($_POST["albumid"]) && is_numeric ($_POST["albumid"])) ? intval ($_POST["albumid"]) : "";

            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["description"] = isset ($_POST["description"]) ? trim ($_POST["description"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "No id specified";
            }
            $photo = $photoDAO->load ($form_values["id"]);
            if (!$photo) {
                $form_errors["id"] = "Photo does not exist";
            }
            if (empty ($form_values["albumid"])) {
                $form_errors["albumid"] = "No albumid specified";
            }
            else if (!$albumDAO->load ($form_values["albumid"])) {
                $form_errors["albumid"] = "Album does not exist";
            }

            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }
            if (empty ($form_values["description"])) {
                $form_errors["description"] = "No description specified";
            }

            // Check if image will be changed
            $upload_path = "";
            if (!empty ($_FILES["imagefile"]) && $_FILES["imagefile"]["error"] != UPLOAD_ERR_NO_FILE) {
                if ($_FILES["imagefile"]["error"] != UPLOAD_ERR_OK) {
                    $form_errors["imagefile"] = "File upload failed";
                }
                else {
                    $info = getimagesize ($_FILES["imagefile"]["tmp_name"]);
                    $path = pathinfo ($_FILES["imagefile"]["name"]);
                    $upload_path = joinPath (Photo::UPLOAD_DIR, strftime ("%Y_%m"), basename($_FILES['imagefile']['name']));
                    $thumbLoc = joinPath (Photo::THUMBNAIL_DIR, strftime ("%Y_%m"), $path["filename"] . "_thumb.jpg");
                    $smallThumbLoc = joinPath (Photo::THUMBNAIL_DIR, strftime ("%Y_%m"), $path["filename"] . "_thumb_small.jpg");
                    if (!$info || !(strtolower ($path["extension"]) != ".png" && strtolower ($path["extension"]) != ".jpg" && strtolower ($path["extension"]) != ".jpeg")) {
                        $form_errors["imagefile"] = "An invalid file was uploaded";
                    }
                    //else if ($info[0] > Photo::MAX_WIDTH || $info[1] > Photo::MAX_HEIGHT) {
                    //    $form_errors["imagefile"] = "The maximum size of an image is " . Photo::MAX_WIDTH . "x" . Photo::MAX_HEIGHT;
                    //}
                    // Allow current files to be overwritten
                    else if (file_exists ($upload_path)) {
                        unlink ($upload_path);
                        if (file_exists ($thumbLoc)) {
                            unlink ($thumbLoc);
                        }
                        if (file_exists ($smallThumbLoc)) {
                            unlink ($smallThumbLoc);
                        }
                        //$form_errors["imagefile"] = "Filename already exists.  Please choose different name or delete file first";
                    }
                }
            }

            if (empty ($form_errors)) {
                $photo->setAlbumId ($form_values["albumid"]);
                $photo->setTitle ($form_values["title"]);
                $photo->setDescription ($form_values["description"]);
                // New image has been uploaded
                if (!empty ($_FILES["imagefile"]) && $_FILES["imagefile"]["error"] != UPLOAD_ERR_NO_FILE) {
                    if (!file_exists (dirname ($upload_path))) {
                        mkdir (dirname ($upload_path));
                    }

                    if (move_uploaded_file ($_FILES["imagefile"]["tmp_name"], $upload_path)) {
                        $photo->setFileLoc ($upload_path);
                        // Reset thumbnail location in case new image does not need a thumbnail
                        $photo->setThumbLoc ("");
                        // Create thumbnail
                        if ($info[0] > Photo::MAX_WIDTH) {
                            $phpThumb = new phpThumb();
                            $phpThumb->setSourceFilename($photo->getFileLoc ());
                            $phpThumb->setParameter('w', Photo::MAX_WIDTH);
                            $phpThumb->setParameter('config_output_format', 'jpeg');
                            if (!file_exists (dirname ($thumbLoc))) {
                                mkdir (dirname ($thumbLoc));
                            }

                            if ($phpThumb->GenerateThumbnail() && $phpThumb->RenderToFile ($thumbLoc)) {
                                $photo->setThumbLoc ($thumbLoc);
                                $phpThumb = new phpThumb();
                                $phpThumb->setSourceFilename($photo->getFileLoc ());
                                $phpThumb->setParameter('h', Photo::SMALL_THUMB_HEIGHT);
                                $phpThumb->setParameter('config_output_format', 'jpeg');
                                $phpThumb->GenerateThumbnail();
                            }
                            else {
                                if (file_exists ($photo->getFileLoc ())) {
                                    unlink ($photo->getFileLoc ());
                                }
                                $form_errors["imagefile"] = "Image larger than " . Photo::MAX_WIDTH . "x" . Photo::MAX_HEIGHT . " and thumbnail generation failed";
                            }
                        }
                    }
                    else {
                        $form_errors["imagefile"] = "File could not be moved";
                    }

                }

                if (empty ($form_errors["imagefile"])) {
                    if ($photoDAO->save ($photo)) {
                        $session->setMessage ("Photo saved");
                        header ("Location: edit_photo.php?id={$photo->getId ()}");
                        return;
                    }
                    else {
                        $session->setMessage ("Photo not saved");
                    }
                }
            }
            else if (empty ($form_errors["id"])) {
                $photo = $photoDAO->load ($form_values["id"]);
            }
        }
        else if (!empty ($_GET)) {
            $form_values["id"] = isset ($_GET["id"]) ? $_GET["id"] : "";

            if (empty ($form_values["id"])) {
                header ("Location: " . BASE_URL);
                return;
            }
            else {
                $photo = $photoDAO->load ($form_values["id"]);
                if ($photo) {
                    $form_values["id"] = $photo->getId ();
                    $form_values["albumid"] = $photo->getAlbumId ();
                    $form_values["title"] = $photo->getTitle ();
                    $form_values["description"] = $photo->getDescription ();
                }
            }
        }

        $album_array = $albumDAO->all ();
        $this->template->render (array (
                                    "title" => "Edit Photo",
                                    "session" => $session,
                                    "main_page" => "edit_photo_tpl.php",
                                    "photo" => $photo,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                    "album_array" => $album_array,
                                ));
    }
}

$controller = new EditPhotoController ();
$controller->run ();
?>
