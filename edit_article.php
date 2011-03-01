<?php
/**
 * File defines the EditArticleController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));

/**
 * ADMIN PAGE. Interface for editing an article entry
 * 
 * Display form for editing an article entry. For POST requests,
 * check user credentials, check if article exists and then update entry in database.
 * Available to admins only
 * @package PageController
 */
class EditArticleController implements Controller {
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
     * Populate template and display form for editing an article entry. For POST requests,
     * check user credentials, check if article exists and then update entry in database.
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
        $form_values = array ("id" => "", "title" => "", "content" => "", "postDate" => "", "updateDate" => "", "published" => "", "tags" => "");
        $articleDAO = ArticleDAO::getInstance ();
        $tagDAO = ArticleTagDAO::getInstance ();
        $article = null;


        if (!empty ($_POST)) {
            $form_values["id"] = (isset ($_POST["id"]) && is_numeric ($_POST["id"])) ? intval ($_POST["id"]) : "";
            $form_values["title"] = isset ($_POST["title"]) ? trim ($_POST["title"]) : "";
            $form_values["content"] = isset ($_POST["content"]) ? trim ($_POST["content"]) : "";
            $form_values["postDate"] = isset ($_POST["postDate"]) ? trim ($_POST["postDate"]) : "";
            $form_values["updateDate"] = isset ($_POST["updateDate"]) ? trim ($_POST["updateDate"]) : "";
            $form_values["published"] = isset ($_POST["published"]) ? trim ($_POST["published"]) : "";
            $form_values["tags"] = isset ($_POST["tags"]) ? trim ($_POST["tags"]) : "";

            if (empty ($form_values["id"])) {
                $form_errors["id"] = "No id specified";
            }
            if (empty ($form_values["title"])) {
                $form_errors["title"] = "No title specified";
            }
            if (empty ($form_values["content"])) {
                $form_errors["content"] = "No content specified";
            }
            if (empty ($form_values["postDate"])) {
                $form_errors["postDate"] = "No post date specified";
            }
            else if (strtotime ($form_values["postDate"]) == 0) {
                $form_errors["postDate"] = "An invalid post date was specified";
                $form_values["postDate"] = "";
            }

            if (!empty ($form_values["updateDate"]) && strtotime ($form_values["updateDate"]) == 0) {
                $form_errors["updateDate"] = "An invalid update date was specified";
                $form_values["updateDate"] = "";
            }

            if ($form_values["published"] != "true" && $form_values["published"] != "false") {
                $form_errors["published"] = "Invalid published choice";
            }

            if (empty ($form_errors)) {
                $article = $articleDAO->load ($form_values["id"]);
                if ($article && ($user->isAdmin () || $article->userId == $user->id)) {
                    $article->setTitle ($form_values["title"]);
                    $article->setContent ($form_values["content"]);
                    $article->setPostDate (strtotime ($form_values["postDate"]));
                    if (!empty ($form_values["updateDate"])) {
                        $article->setUpdateDate (strtotime ($form_values["updateDate"]));
                    }
                    //$article->setUpdateDate (time ());
                    $published = ($form_values["published"] == "true") ? 1: 0;
                    $article->setPublished ($published);
                    $article->setUserId ($user->id);
                    $sorted_tag_array = ArticleTag::tagsFromString ($form_values["tags"]);
                    $sorted_tags = implode (" ", $sorted_tag_array);
                    $article->setTags ($sorted_tags);
                    //print_r ($article);

                    if ($articleDAO->save ($article)) {
                        $tagDAO->updateTags ($article);
                        $session->setMessage ("Article details saved");
                        header ("Location: edit_article.php?id={$article->id}");
                        return;
                    }
                    else {
                        $session->setMessage ("Article details could not be saved", Session::MESSAGE_ERROR);
                    }
                }
                else {
                    $session->setMessage ("Do not have permission to edit the article", Session::MESSAGE_ERROR);
                    header ("Location: " . BASE_URL);
                    return;
                }
            }
            else if (empty ($form_errors["id"])) {
                $article = $articleDAO->load ($form_values["id"]);
            }
        }

        else if (!empty ($_GET)) {
            $form_values["id"] = isset ($_GET["id"]) ? $_GET["id"] : "";

            if (empty ($form_values["id"])) {
                header ("Location: " . BASE_URL);
                return;
            }
            else {
                $article = $articleDAO->load ($form_values["id"]);
                // Article does not exist. Pass null to template
                if (!$article) {
                }
                // Check for edit permissions
                else if (!$user->isAdmin () && $article->userId != $user->id) {
                    $session->setMessage ("Do not have permission to edit article", Session::MESSAGE_ERROR);
                    header ("Location: " . BASE_URL);
                    return;
                }
                // Access granted. Fill form data
                else {
                    $form_values["id"] = $article->getId ();
                    $form_values["title"] = $article->getTitle ();
                    $form_values["content"] = $article->getContent ();
                    $form_values["published"] = ($article->getPublished () == true) ? "true" : "false";
                    $form_values["postDate"] = strftime ("%d %B %Y",$article->getPostDate ());
                    $form_values["updateDate"] = ($article->getUpdateDate () > 0) ? strftime ("%d %B %Y",$article->getUpdateDate ()) : "";
                    $form_values["tags"] = $article->getTags ();
                }
            }
        }

        $this->template->render (array (
                                    "title" => "Edit Article",
                                    "extra_header" => joinPath ("headers", "jscal_header_tpl.php"),
                                    "main_page" => "edit_article_tpl.php",
                                    "session" => $session,
                                    "article" => $article,
                                    "form_errors" => $form_errors,
                                    "form_values" => $form_values,
                                ));
    }
}

$controller = new EditArticleController ();
$controller->run ();
?>
