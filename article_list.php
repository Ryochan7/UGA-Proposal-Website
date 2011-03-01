<?php
/**
 * File defines the ArticleListController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));
require_once (joinPath (INCLUDES_DIR, "models", "ArticleTag.php"));

/**
 * Display published article list
 *
 * Read in list of the latest published articles.
 * Display results in the page.
 * @package PageController
 */
class ArticleListController implements Controller {
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
     * Read in list of the latest published articles. Pagination enabled.
     * Populate template and display results in the page.
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
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
        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        if ($page < 1) {
            $page = 1;
        }
        $count = $paginator = $paginator_page = null;
        $article = $articletags_array = null;
        $title = "";

        $count = $articleDAO->countPublished (true);
        $paginator = new Paginator ($count, $PAGINATION_LIMIT);
        $paginator_page = $paginator->getPage ($page);
        $article_array = $articleDAO->allPublished (true, array ("order" => "{$articleDAO->getTableName ()}.postDate DESC, {$articleDAO->getTableName ()}.id DESC", "limit" => $paginator_page, "joins" => true));

        foreach ($article_array as $article) {
            $articletags_array[] = $tagDAO->allArticleTags ($article, array ("order" => "name"));
        }

        $this->template->render (array (
                                    "title" => "Latests Articles",
                                    "main_page" => "article_list_tpl.php",
                                    "session" => $session,
                                    "article_array" => $article_array,
                                    "articletags_array" => $articletags_array,
                                    "paginator_page" => $paginator_page,
                                ));
    }
}

$controller = new ArticleListController ();
$controller->run ();
?>

