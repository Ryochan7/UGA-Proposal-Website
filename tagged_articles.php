<?php
/**
 * File defines the TaggedArticlesController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));
require_once (joinPath (INCLUDES_DIR, "models", "ArticleTag.php"));

/**
 * Display list of articles tagged with a selected tag
 *
 * Read in articles from the database that are tagged with a particular tag.
 * The ArticleTag table will be scanned to check that a tag exists. Then, the ArticleDAO
 * goes through the TaggedArticle table to find all articles that are tagged with the
 * the specified tag. The articles are then displayed in the page.
 * @package PageController
 */
class TaggedArticlesController implements Controller {
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
     * Populate template and read in articles from the database that are tagged with a particular tag
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
        $tagId = (isset ($_GET["id"]) && is_numeric ($_GET["id"])) ? intval ($_GET["id"]) : 0;
        if ($page < 1) {
            $page = 1;
        }
        $count = $paginator = $paginator_page = null;
        $article = $articletags_array = $tag = $article_array = null;
        $title = "Tagged by ";

        if ($tagId >= 0) {
            $tag = $tagDAO->load ($tagId);
            if ($tag) {
                $title .= $tag->name;
                $count = $articleDAO->countPublishedWithTag (true, $tag);
                $paginator = new Paginator ($count, $PAGINATION_LIMIT);
                $paginator_page = $paginator->getPage ($page);
                $article_array = $articleDAO->allPublishedWithTag (true, $tag, array ("order" => "{$articleDAO->getTableName ()}.postDate DESC, {$articleDAO->getTableName ()}.id DESC", "limit" => $paginator_page, "joins" => true));
                foreach ($article_array as $article) {
                    $articletags_array[] = $tagDAO->allArticleTags ($article, array ("order" => "name"));
                }
            }
            else {
                $title = "Tag not found";
            }
        }
        else {
            $title = "Tag not found";
        }

        $this->template->render (array (
                                    "title" => $title,
                                    "main_page" => "tagged_articles_tpl.php",
                                    "session" => $session,
                                    "article_array" => $article_array,
                                    "articletags_array" => $articletags_array,
                                    "paginator_page" => $paginator_page,
                                    "tag" => $tag,
                                ));
    }
}

$controller = new TaggedArticlesController ();
$controller->run ();
?>

