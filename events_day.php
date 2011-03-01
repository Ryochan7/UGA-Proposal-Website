<?php
/**
 * File defines the EventsDayController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Platform.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/**
 * Display list of events for a given day
 * 
 * Reads in events for a given day or current day if no parameters are passed.
 * Allow filtering by platform id. Display event data on page.
 * @package PageController
 */
class EventsDayController implements Controller {
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
     * Reads in events for a given day or current day if no parameters are passed.
     * Allow filtering by platform id. Populate template and display event data on page.
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $eventDAO = EventDAO::getInstance ();
        $page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        $platform_id = (isset ($_GET["platform"]) && is_numeric ($_GET["platform"])) ? intval ($_GET["platform"]) : 0;
        $month = (isset ($_GET["month"]) && is_numeric ($_GET["month"])) ? intval ($_GET["month"]) : 0;
        $day = (isset ($_GET["day"]) && is_numeric ($_GET["day"])) ? intval ($_GET["day"]) : 0;
        $year = (isset ($_GET["year"]) && is_numeric ($_GET["year"])) ? intval ($_GET["year"]) : 0;

        if ($page < 1) {
            $page = 1;
        }
        $count = $paginator = $paginator_page = $event_array = $next_eventday = $prev_eventday = $queryVars = $current_platform = null;

        if ($platform_id > 0 && checkdate ($month, $day, $year)) {
            $start = mktime (0, 0, 0, $month, $day, $year);
            $end = strtotime ("+1 day", $start) - 1;
            $count = $eventDAO->countPlatformStatusAndRange ($platform_id, Event::APPROVED_STATUS, $start, $end);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByPlatformStatusAndRange ($platform_id, Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
            $queryVars = array ("platform" => $platform_id);
        }
        else if ($platform_id > 0) {
            $start = mktime (0, 0, 0);
            $end = strtotime ("+1 day", $start) - 1;
            $count = $eventDAO->countPlatformStatusAndRange ($platform_id, Event::APPROVED_STATUS, $start, $end);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByPlatformStatusAndRange ($platform_id, Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
            $queryVars = array ("platform" => $platform_id);
        }
        else if (checkdate ($month, $day, $year)) {
            $start = mktime (0, 0, 0, $month, $day, $year);
            $end = strtotime ("+1 day", $start) - 1;
            $count = $eventDAO->countStatusAndRange (Event::APPROVED_STATUS, $start, $end);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByStatusAndRange (Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
        }
        else {
            $start = mktime (0, 0, 0);
            $end = strtotime ("+1 day", $start) - 1;
            $count = $eventDAO->countStatusAndRange (Event::APPROVED_STATUS, $start, $end);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByStatusAndRange (Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
        }

        $platformDAO = PlatformDAO::getInstance ();
        $platform_array = $platformDAO->all ();

        if ($platform_id > 0) {
            $current_platform = $platformDAO->load ($platform_id);
            $next_eventday = $eventDAO->loadByNextDayPlatform ($platform_id, $end, Event::APPROVED_STATUS);
            $prev_eventday = $eventDAO->loadByPreviousDayPlatform ($platform_id, $start, Event::APPROVED_STATUS);
        }
        else {
            $next_eventday = $eventDAO->loadByNextDay ($end, Event::APPROVED_STATUS);
            $prev_eventday = $eventDAO->loadByPreviousDay ($start, Event::APPROVED_STATUS);
        }

        $this->template->render (array (
                                    "title" => "Event List for day " . strftime (strftime ("%B %d, %Y", $start)),
                                    "main_page" => "events_day_tpl.php",
                                    "event_array" => $event_array,
                                    "session" => $session,
                                    "paginator_page" => $paginator_page,
                                    "start" => $start,
                                    "end" => $end,
                                    "next_eventday" => $next_eventday,
                                    "prev_eventday" => $prev_eventday,
                                    "sidebar_extra" => joinPath ("fragments", "event_sidebar_tpl.php"),
                                    "platform_array" => $platform_array,
                                    "queryVars" => $queryVars,
                                    "current_platform" => $current_platform,
                                ));
    }
}

$controller = new EventsDayController ();
$controller->run ();
?>
