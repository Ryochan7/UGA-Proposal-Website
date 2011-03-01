<?php
/**
 * File defines the ViewArticleController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));
require_once (joinPath (INCLUDES_DIR, "models", "ArticleTag.php"));

/**
 * View article page
 * 
 * Read in the specified article from the database.
 * Display article in the page
 * @package PageController
 */
class ViewArticleController implements Controller {
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
     * Read in the specified article from the database.
     * Populate template and display article in the page
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();
        /*
        if ($user == null || !$user->validUser ()) {
            header ("Location: " . BASE_URL);
            return;
        }
        */

        $articleDAO = ArticleDAO::getInstance ();
        $tagDAO = ArticleTagDAO::getInstance ();
        $article = $articletags = null;
        $title = "";

        if (!empty ($_GET["id"]) && is_numeric ($_GET["id"])) {
            $article_id = intval ($_GET["id"]);
            $article = $articleDAO->load ($article_id, array ("joins" => true));
            if ($article) {
                $title .= "{$article->getTitle ()}";
                $articletags = $tagDAO->allArticleTags ($article, array ("order" => "name"));
            }
        }
        //print_r ($articletags);

        $this->template->render (array (
                                    "title" => "Article - " . $title,
                                    "main_page" => "view_article_tpl.php",
                                    "session" => $session,
                                    "article" => $article,
                                    "articletags" => $articletags,
                                ));
    }
}

$controller = new ViewArticleController ();
$controller->run ();
?>
