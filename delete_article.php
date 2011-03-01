<?php
/**
 * File defines the DeleteArticleController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));

/**
 * ADMIN PAGE. Interface for article deletion
 * 
 * Display confirmation for article deletion. For POST request,
 * check user credentials, check if article exists and then delete entry from database.
 * Available to admins only
 * @package PageController
 */
class DeleteArticleController implements Controller {
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
     * Populate template and display confirmation for article deletion. For POST request,
     * check user credentials, check if article exists and then delete entry from database.
     * Available to admins only
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        // Check for an admin user
        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $articleDAO = ArticleDAO::getInstance ();
        $delete_article = null;
        $form_errors = array ();
        $form_values = array ("id" => "");

        if (!empty ($_POST)) {
            // Check if a number was passed for the id
            $id = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            // Check if an article exists
            else {
                $delete_article = $articleDAO->load ($id);
                // Article exists. Delete
                if ($delete_article) {
                    if ($articleDAO->delete ($delete_article)) {
                        $session->setMessage ("Article deleted");
                        //header ("Location: " . BASE_URL);
                        return;
                    }
                    else {
                        $session->setMessage ("Could not delete article", Session::MESSAGE_ERROR);
                    }
                }
            }
        }
        // Read article from database for confirmation
        else if (!empty ($_GET)) {
            $id = isset ($_GET["id"]) ? trim ($_GET["id"]) : "";
            if (empty ($id)) {
                header ("Location: " . BASE_URL);
                return;
            }
            else if (is_numeric ($id)) {
                $delete_article = $articleDAO->load ($id);
                if ($delete_article) {
                    $form_values["id"] = $delete_article->getId ();
                }
            }
        }
        // Direct file access. Redirect
        else {
            header ("Location: " . BASE_URL);
            return;
        }
        $this->template->render (array (
                                    "title" => "Delete Article",
                                    "main_page" => "delete_article_tpl.php",
                                    "user" => $user,
                                    "session" => $session,
                                    "delete_article" => $delete_article,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new DeleteArticleController ();
$controller->run ();
?>
