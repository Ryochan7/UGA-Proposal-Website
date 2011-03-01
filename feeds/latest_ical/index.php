<?php
/**
 * File defines the ICalFeedController PageController class
 * @package PageController
 */
/**
 */
$base_dir = dirname (dirname (dirname (__FILE__)));
require_once ($base_dir . DIRECTORY_SEPARATOR . "shared" . DIRECTORY_SEPARATOR . "bootstrap.php");
require_once (joinPath (INCLUDES_DIR, "models", "Event.php"));

/**
 * Page to generate .ics text file
 *
 * Read latest approved event data from database. Alter output header so
 * client interprets sent text as calendar text. Send calendar text
 * to client
 * @package PageController
 */
class ICalFeedController implements Controller {
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
        $this->template = new PageTemplate (joinPath ("feeds", "events_ical_tpl.php"));
    }

    /**
     * Run method with main page logic
     * 
     * Read latest approved event data from database. Alter output header so
     * client interprets sent text as calendar text. Populate calendar template
     * and send calendar text to client
     * @access public
     */
    public function run () {
        $PAGINATION_LIMIT = 20;
        $eventDAO = EventDAO::getInstance ();

        $platform = (isset ($_GET["platform"]) && is_numeric ($_GET["platform"])) ? intval ($_GET["platform"]) : 0;

        $count = $paginator = $paginator_page = null;

        // Platform choice was made. Retrieve only events with platform id
        if ($platform <= 0) {
            $count = $eventDAO->countStatus (Event::APPROVED_STATUS);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage (1);
            $event_array = $eventDAO->allByStatus (Event::APPROVED_STATUS, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
        }
        // Get all events
        else {
            $count = $eventDAO->countPlatformStatus ($platform, Event::APPROVED_STATUS);
            $paginator = new Paginator ($count, $PAGINATION_LIMIT);
            $paginator_page = $paginator->getPage (1);
            $event_array = $eventDAO->allByPlatformStatus ($platform, Event::APPROVED_STATUS, array ("order" => "{$eventDAO->getTableName ()}.date DESC, {$eventDAO->getTableName ()}.id DESC", "joins" => true, "limit" => $paginator_page));
        }
        //print_r ($event_array);

        // Alter header so client does not interpret output as HTML
        header("Content-type: text/calendar");
        header("Content-Disposition: attachment; filename=\"latest_events.ics\"");
        $this->template->render (array (
                                    "title" => "Latest Events Feed",
                                    "event_array" => $event_array,
                                    "paginator_page" => $paginator_page,
                                ));
    }
}

$controller = new ICalFeedController ();
$controller->run ();

?>
