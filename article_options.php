<?php
/**
 * File defines the ArticleOptionsController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));

/**
 * ADMIN PAGE. Interface to administer article data
 *
 * Read in articles from the database. Displays an interface to administer article data
 * for allowing bulk deletion of articles, deletion of a single
 * article, links to editing each article entry. Available to admins only.
 * @package PageController
 */
class ArticleOptionsController implements Controller {
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
     * Read in articles from the database. Displays an interface to administer article data
     * for allowing bulk deletion of articles, deletion of a single
     * article, links to editing each article entry. Pagination enabled.
     * Available to admins only.
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        if (!$user || !$user->isAdmin ()) {
            $session->setMessage ("Do not have permission to access", Session::MESSAGE_ERROR);
            header ("Location: " . BASE_URL);
            return;
        }

        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $action = isset ($_GET["action"]) ? trim ($_GET["action"]) : "";

        $articleDAO = ArticleDAO::getInstance ();
        $article_array = $paginator_page = null;
        $content_title = "";
        
        if (!empty ($_POST) && !empty ($_POST["ids"]) && !empty ($_POST["action"])) {
            $action = isset ($_POST["action"]) ? trim ($_POST["action"]) : "";
            if (!strcmp ($action, "delete") == 0) {
                header ("Location: " . BASE_URL);
                return;
            }

            $status = $articleDAO->deleteByIds ($_POST["ids"]);
            if ($status) {
                $session->setMessage ("Selected pages deleted");
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
            else {
                $session->setMessage ("Deletion failed", Session::MESSAGE_ERROR);
                header ("Location: {$_SERVER["PHP_SELF"]}");
                return;
            }
        }
        else if (strcmp ($action, "delete") == 0 && !empty ($_GET["ids"])) {
            $content_title = "Delete Articles";
            $article_array = $articleDAO->allByIds ($_GET["ids"]);
        }
        else if (strcmp ($action, "delete") == 0) {
        }
        else {
            $count = $articleDAO->count ();
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $article_array = $articleDAO->all (array ("limit" => $paginator_page));
        }

        $this->template->render (array (
                                    "title" => "Admin - Article Options",
                                    "main_page" => "article_options_tpl.php",
                                    "session" => $session,
                                    "article_array" => $article_array,
                                    "paginator_page" => $paginator_page,
                                    "action" => $action,
                                    "content_title" => $content_title,
                                ));
    }
}

$controller = new ArticleOptionsController ();
$controller->run ();
?>
