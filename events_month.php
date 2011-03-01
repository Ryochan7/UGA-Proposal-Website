<?php
/**
 * File defines the EventsMonthController PageController class
 * @package PageController
 */
/**
 */
$current_dir = dirname (__FILE__);
require_once ($current_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));
require_once (joinPath (INCLUDES_DIR, "models", "Platform.php"));

/**
 * Display event calendar for events occurring within a given month
 *
 * Reads in events for a given month or current month if no parameters are passed.
 * Allow filtering by platform id. Display event data in a calendar view on the page.
 * @package PageController
 */
class EventsMonthController implements Controller {
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
     * Reads in events for a given month or current month if no parameters are passed.
     * Allow filtering by platform id. Populate template and display event data in a calendar view on the page.
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 10;
        $session = Session::getInstance ();
        $user = $session->getUser ();

        $eventDAO = EventDAO::getInstance ();
        $platformDAO = PlatformDAO::getInstance ();
        //$page = (isset ($_GET["page"]) && is_numeric ($_GET["page"])) ? intval ($_GET["page"]) : 1;
        $platform_id = (isset ($_GET["platform"]) && is_numeric ($_GET["platform"])) ? intval ($_GET["platform"]) : 0;
        $month = (isset ($_GET["month"]) && is_numeric ($_GET["month"])) ? intval ($_GET["month"]) : 0;
        $year = (isset ($_GET["year"]) && is_numeric ($_GET["year"])) ? intval ($_GET["year"]) : 0;
        
        //if ($page < 1) {
        //    $page = 1;
        //}
        $count = $paginator = $paginator_page = $event_array = $next_eventday = $prev_eventday = $current_platform = null;


        if ($platform_id > 0 && checkdate ($month, 1, $year)) {
            $start = mktime (0, 0, 0, $month, 1, $year);
            $end = strtotime ("+1 month", $start) - 1;
            //$count = $eventDAO->countPlatformStatusAndRange ($platform, Event::APPROVED_STATUS, $start, $end);
            //$paginator = new Paginator ($count, 3);
            //$paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByPlatformStatusAndRange ($platform_id, Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true));

        }
        else if ($platform_id > 0) {
            $start = mktime (0, 0, 0, idate ("m"), 1, idate ("Y"));
            $end = strtotime ("+1 month", $start) - 1;
            //$count = $eventDAO->countPlatformStatusAndRange ($platform, Event::APPROVED_STATUS, $start, $end);
            //$paginator = new Paginator ($count, 3);
            //$paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByPlatformStatusAndRange ($platform_id, Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true));
        }
        else if (checkdate ($month, 1, $year)) {
            $start = mktime (0, 0, 0, $month, 1, $year);
            $end = strtotime ("+1 month", $start) - 1;
            //$count = $eventDAO->countStatus (Event::APPROVED_STATUS);
            //$paginator = new Paginator ($count, 3);
            //$paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByStatusAndRange (Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true));
        }
        else {
            $start = mktime (0, 0, 0, idate ("m"), 1, idate ("Y"));
            $end = strtotime ("+1 month", $start) - 1;
            //$count = $eventDAO->countStatus (Event::APPROVED_STATUS);
            //$paginator = new Paginator ($count, 3);
            //$paginator_page = $paginator->getPage ($page);
            $event_array = $eventDAO->allByStatusAndRange (Event::APPROVED_STATUS, $start, $end, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true));
        }

        $next_eventday = $eventDAO->loadByNextDay ($end, Event::APPROVED_STATUS);
        $prev_eventday = $eventDAO->loadByPreviousDay ($start, Event::APPROVED_STATUS);

        if ($platform_id > 0) {
            $current_platform = $platformDAO->load ($platform_id);
        }

        $platform_array = $platformDAO->all ();
            //print_r ($event_array);
        $this->template->render (array (
                                    "title" => "Event Month Calendar - " . date ("F", $start) . " " . date ("Y", $start),
                                    "main_page" => "events_month_tpl.php",
                                    "event_array" => $event_array,
                                    "session" => $session,
                                    "start" => $start,
                                    "end" => $end,
                                    "next_eventday" => $next_eventday,
                                    "prev_eventday" => $prev_eventday,
                                    "sidebar_extra" => joinPath ("fragments", "event_sidebar_tpl.php"),
                                    "platform_array" => $platform_array,
                                    "current_platform" => $current_platform,
                                ));
    }
}

$controller = new EventsMonthController ();
$controller->run ();
?>
