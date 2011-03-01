<?php
/**
 * File defines the CreatePageController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Page.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * ADMIN PAGE. Interface for creating a new page entry
 *
 * Display form for creating a new page entry. For POST request,
 * validate form data and save information to database. Available to admins only
 * @package PageController
 */
class CreatePageController implements Controller {
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
     * Populate template and display form for creating a new page entry. For POST request,
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

        $pageDAO = PageDAO::getInstance ();
        $page = null;
        $form_errors = array ();
        $form_values = array ("id" => "", "title" => "", "content" => "", "published" => false, "template" => "");

        if (!empty ($_POST)) {
            $form_values["id"] = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["content"] = isset ($_POST["content"]) ? trim ($_POST["content"]) : "";
            $form_values["published"] = isset ($_POST["published"]) ? trim ($_POST["published"]) : "";
            $form_values["template"] = isset ($_POST["template"]) ? trim ($_POST["template"]) : "";

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
                $page = new PageModel ();
                $page->setTitle ($form_values["title"]);
                $page->setContent ($form_values["content"]);
                $page->setUserId ($user->id);
                $pub_value = (strcmp ($form_values["published"], "true") == 0) ? true : false;
                $page->setPublished ($pub_value);
                if (!empty ($form_values["template"])) {
                    $page->setTemplate ($form_values["template"]);                  
                }

                if ($pageDAO->insert ($page)) {
                    $session->setMessage ("Page saved");
                    header ("Location: edit_page.php?id={$page->id}");
                    return;
                }
                else {
                    $session->setMessage ("Page not saved");
                }
            }
        }
        
        $this->template->render (array (
                                    "title" => "Create Page",
                                    "session" => $session,
                                    "main_page" => "create_page_tpl.php",
                                    "page" => $page,
                                    "form_values" => $form_values,
                                    "form_errors" => $form_errors,
                                ));
    }
}

$controller = new CreatePageController ();
$controller->run ();
?>
