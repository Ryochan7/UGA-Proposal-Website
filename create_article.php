<?php
/**
 * File defines the CreateArticleController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));

/**
 * ADMIN PAGE. Interface for creating a new article entry
 *
 * Display form for creating a new article entry. For POST requests,
 * validate form data and save information to database. Available to admins only
 * @package PageController
 */
class CreateArticleController implements Controller {
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
     * Populate template and display form for creating a new article entry. For POST requests,
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
        
        $form_errors = array ();
        $form_values = array ("title" => "", "content" => "", "postDate" => "", "published" => "", "tags" => "");
        $articleDAO = ArticleDAO::getInstance ();
        $tagDAO = ArticleTagDAO::getInstance ();

        if (!empty ($_POST)) {
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["content"] = isset ($_POST["content"]) ? trim ($_POST["content"]) : "";
            $form_values["postDate"] = isset ($_POST["postDate"]) ? trim ($_POST["postDate"]) : "";
            $form_values["published"] = isset ($_POST["published"]) ? trim ($_POST["published"]) : "";
            $form_values["tags"] = isset ($_POST["tags"]) ? trim ($_POST["tags"]) : "";

            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }
            if (empty ($form_values["content"])) {
                $form_errors["content"] = "No content specified";
            }
            if (empty ($form_values["postDate"])) {
                $form_errors["postDate"] = "No post date specified";
            }
            else if (strtotime ($_POST["postDate"]) == 0) {
                $form_errors["postDate"] = "An invalid post date was specified";
                $form_values["postDate"] = "";
            }
            if ($form_values["published"] != "true" && $form_values["published"] != "false") {
                $form_errors["published"] = "Invalid published choice";
            }

            if (empty ($form_errors)) {
                $article = new Article ();
                $article->setTitle ($form_values["title"]);
                $article->setContent ($form_values["content"]);
                $article->setPostDate (strtotime ($form_values["postDate"]));
                $article->setUpdateDate (0);
                $published = ($form_values["published"] == "true") ? 1: 0;
                $article->setPublished ($published);
                $article->setUserId ($user->id);
                //$article->setTags ($form_values["tags"]);
                $sorted_tag_array = ArticleTag::tagsFromString ($form_values["tags"]);
                $sorted_tags = implode (" ", $sorted_tag_array);
                $article->setTags ($sorted_tags);

                if ($articleDAO->insert ($article)) {
                    $tagDAO->updateTags ($article);
                    $session->setMessage ("Article details saved");
                    header ("Location: edit_article.php?id={$article->id}");
                    return;
                }
                else {
                    $session->setMessage ("Article details could not be saved", Session::MESSAGE_ERROR);
                }
                
            }
        }

        $this->template->render (array (
                                    "title" => "Create Article",
                                    "extra_header" => joinPath ("headers", "jscal_header_tpl.php"),
                                    "main_page" => "create_article_tpl.php",
                                    "session" => $session,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new CreateArticleController ();
$controller->run ();
?>
