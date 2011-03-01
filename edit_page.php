<?php
/**
 * File defines the EditPageController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Page.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface for editing a page entry
 * 
 * Display form for editing an page entry. For POST requests,
 * check user credentials, check if page exists and then update entry in database.
 * Available to admins only
 * @package PageController
 */
class EditPageController implements Controller {
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
     * Populate template and display form for editing an page entry. For POST requests,
     * check user credentials, check if page exists and then update entry in database.
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
        
        $form_errors = array ();
        $form_values = array ("id" => "", "title" => "", "content" => "", "published" => false, "template" => "");
        $pageDAO = PageDAO::getInstance ();
        $page = null;

        if (!empty ($_POST)) {
            $form_values["id"] = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["content"] = isset ($_POST["content"]) ? trim ($_POST["content"]) : "";
            $form_values["published"] = isset ($_POST["published"]) ? trim ($_POST["published"]) : "";
            $form_values["template"] = isset ($_POST["template"]) ? trim ($_POST["template"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "No id specified";
            }
            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }
            if (empty ($form_values["content"])) {
                $form_errors["content"] = "No content specified";
            }
            if (empty ($form_values["published"])) {
                $form_errors["published"] = "Published status not specified";
            }
            else if (strcmp ($form_values["published"], "true") != 0 && strcmp ($form_values["published"], "false") != 0) {
                $form_errors["published"] = "Published must be a boolean value";
            }

            if (empty ($form_errors)) {
                $page = $pageDAO->load ($form_values["id"]);
                if ($page && ($user->isAdmin () || $page->userId == $user->id)) {
                    $page->setTitle ($form_values["title"]);
                    $page->setContent ($form_values["content"]);
                    $page->setUserId ($user->id);
                    $pub_value = (strcmp ($form_values["published"], "true") == 0) ? true : false;
                    $page->setPublished ($pub_value);
                    if (!empty ($form_values["template"])) {
                        $page->setTemplate ($form_values["template"]);                        
                    }

                    if ($pageDAO->save ($page)) {
                        $session->setMessage ("Page saved");
                        header ("Location: {$_SERVER["PHP_SELF"]}?id={$page->id}");
                        return;
                    }
                    else {
                        $session->setMessage ("Page not saved");
                    }
                }
                else {
                    $session->setMessage ("Do not have permission to edit page", Session::MESSAGE_ERROR);
                    header ("Location: " . BASE_URL);
                    return;
                }
            }
            else if (empty ($form_errors["id"])) {
                $page = $pageDAO->load ($form_values["id"]);
            }

        }
        else if (!empty ($_GET)) {
            $form_values["id"] = isset ($_GET["id"]) ? $_GET["id"] : "";

            if (empty ($form_values["id"])) {
                header ("Location: " . BASE_URL);
                return;
            }
            else {
                $page = $pageDAO->load ($form_values["id"]);
                // Page does not exist
                if (!$page) {
                }
                // Check for edit permissions
                else if (!$user->isAdmin () && $page->userId != $user->id) {
                    $session->setMessage ("Do not have permission to edit page", Session::MESSAGE_ERROR);
                    header ("Location: " . BASE_URL);
                    return;
                }
                // Fill form data
                else {
                    $form_values["id"] = $page->getId ();
                    $form_values["title"] = $page->getTitle ();
                    $form_values["content"] = $page->getContent ();
                    $form_values["published"] = ($page->getPublished () == true) ? "true" : "false";
                    $form_values["template"] = $page->getTemplate ();
                }
            }
        }

        $this->template->render (array (
                                    "title" => "Edit Page",
                                    "main_page" => "edit_page_tpl.php",
                                    "session" => $session,
                                    "page" => $page,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                ));
    }
}

$controller = new EditPageController ();
$controller->run ();
?>
