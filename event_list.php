<?php
/**
 * File defines the EventListController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * Display list of latest published events
 * 
 * Read in list of the latest published events.
 * Display results in the page. Pagination enabled
 * @package PageController
 */
class EventListController implements Controller {
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
     * Read in list of the latest published events and populate template with results.
     * Display results in the page. Pagination enabled
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $eventDAO = EventDAO::getInstance ();
        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        $platform_id = (isset ($_GET["platform"]) && is_numeric ($_GET["platform"])) ? intval ($_GET["platform"]) : 0;
        if ($page < 1) {
            $page = 1;
        }
        $count = $paginator = $paginator_page = $queryVars = $current_platform = null;

        if ($platform_id <= 0) {
            $count = $eventDAO->countStatus (Event::APPROVED_STATUS);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByStatus (Event::APPROVED_STATUS, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
        }
        else {
            $count = $eventDAO->countPlatformStatus ($platform_id, Event::APPROVED_STATUS);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByPlatformStatus ($platform_id, Event::APPROVED_STATUS, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
            $queryVars = array ("platform" => $platform_id);
        }

        $platformDAO = PlatformDAO::getInstance ();
        $platform_array = $platformDAO->all ();
        //print_r ($event_array);

        if ($platform_id > 0) {
            $current_platform = $platformDAO->load ($platform_id);
        }

        $this->template->render (array (
                                    "title" => "Event List",
                                    "main_page" => "event_list_tpl.php",
                                    "event_array" => $event_array,
                                    "session" => $session,
                                    "paginator_page" => $paginator_page,
                                    "sidebar_extra" => joinPath ("fragments", "event_sidebar_tpl.php"),
                                    "platform_array" => $platform_array,
                                    "queryVars" => $queryVars,
                                    "current_platform" => $current_platform,
                                ));
    }
}

$controller = new EventListController ();
$controller->run ();
?>
