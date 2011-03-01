<?php
/**
 * File defines the IndexController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Article.php"));
require_once (joinPath (INCLUDES_DIR, "models", "User.php"));
require_once (joinPath (INCLUDES_DIR, "models", "AuthToken.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * Display index page
 *
 * Only read in session data. Display index page.
 * @package PageController
 */
class IndexController implements Controller {
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
     * Only read in session data. Populate template and display index page.
     * @access public
     */
    public function run () {
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $this->template->render (array (
                                    "title" => "Index",
                                    "user" => $user,
                                    "session" => $session,
                                ));
    }
}

$controller = new IndexController ();
$controller->run ();
?>
