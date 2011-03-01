<?php
/**
 * File defines the Event model class and EventDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");
require_once ("Platform.php");

/**
 * Event model class for representing an event entity
 *
 * Class contains the members that represent the values of an Event
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class Event extends ModelBase {
    /**
     * Pending status flag int
     * @access public
     * @var int
     */
    const PENDING_STATUS = 1;
    /**
     * Approved status flag int
     * @access public
     * @var int
     */
    const APPROVED_STATUS = 2;
    /**
     * Denied status flag int
     * @access public
     * @var int
     */
    const DENIED_STATUS = 3;
    /*
    const PLATFORM_MIX = 1;
    const PLATFORM_BOARD = 2;
    const PLATFORM_CARD = 3;
    const PLATFORM_TRPG = 4;
    const PLATFORM_HANDHELD = 5;
    const PLATFORM_CONSOLE = 6;
    */
    /**
     * User id of user posting event
     * @access protected
     * @var int
     */
    protected $userId;
    /**
     * Platform id that an event is categorized as
     * @access protected
     * @var int
     */
    protected $platformId;
    /**
     * Title of event
     * @access protected
     * @var string
     */
    protected $title;
    /**
     * Description of event
     * @access protected
     * @var string
     */
    protected $description;
    /**
     * Sanctioned bool of event. Indicates if the event is an officially sanctioned UGA event
     * @access protected
     * @var bool
     */
    protected $sanctioned = false;
    /**
     * Date of the event expressed as a UNIX timestamp
     * @access protected
     * @var int
     */
    protected $date;
    /**
     * Status flag of an event. Defaults to PENDING_STATUS
     * @access protected
     * @var status
     */
    protected $status = self::PENDING_STATUS;
    /**
     * User object of event. Indicates the user who posted the event
     * @access protected
     * @var User
     */
    protected $user;
    /**
     * Platform object of event.
     * @access protected
     * @var Platform
     */
    protected $platform;

    /**
     * Returns the url of the page that can be used
     * to display the object
     *
     * @access public
     * @return string
     */
    public function getAbsoluteURL () {
        $url = "view_event.php?id={$this->id}";
        return $url;
    }

    /**
     * Set the user id of the event
     *
     * @access public
     * @param int $userId
     */
    public function setUserId ($userId) {
        if (!is_numeric ($userId)) {
            throw new InvalidArgumentException ("User id must be an integer value");
        }
        $this->userId = intval ($userId);
    }

    /**
     * Return the user id of the event
     *
     * @access public
     * @return int
     */
    public function getUserId () {
        return $this->userId;
    }

    /**
     * Set the platform id of the event
     *
     * @access public
     * @return int
     */
    public function setPlatformId ($platformId) {
        if (!is_numeric ($platformId)) {
            throw new InvalidArgumentException ("Platform must be an integer value");
        }
        /*
        $platformId = intval ($platformId);
        if ($platformId < self::PLATFORM_MIX || $platformId > self::PLATFORM_CONSOLE) {
            throw new InvalidArgumentException ("Platform id not in a valid range");
        }
        */
        $this->platformId = intval ($platformId);
    }

    /**
     * Return the platform id of the event
     *
     * @access public
     * @return int
     */
    public function getPlatformId () {
        return $this->platformId;
    }

    /**
     * Set the title of the event
     *
     * @access public
     * @param string $title
     */
    public function setTitle ($title) {
        if (!is_string ($title)) {
            throw new InvalidArgumentException ("Title must be a string");
        }

        $this->title = $title;
    }

    /**
     * Return the title of the event
     *
     * @access public
     * @return string
     */
    public function getTitle () {
        return $this->title;
    }

    /**
     * Set the description of an event
     *
     * @access public
     * @param string $description
     */
    public function setDescription ($description) {
        if (!is_string ($description)) {
            throw new InvalidArgumentException ("Description must be a string");
        }

        $this->description = $description;
    }

    /**
     * Return the description of the event
     *
     * @access public
     * @return string
     */
    public function getDescription () {
        return $this->description;
    }

    /**
     * Set the sanctioned bool flag of the event
     *
     * @access public
     * @param bool $sanctioned
     */
    public function setSanctioned ($sanctioned) {
        if (!is_numeric ($sanctioned) && !is_bool ($sanctioned)) {
            throw new InvalidArgumentException ("Sanctioned must be of type bool");
        }

        $this->sanctioned = intval ($sanctioned);
    }

    /**
     * Return the sanctioned bool flag of the event
     *
     * @access public
     * @return bool
     */
    public function getSanctioned () {
        return $this->sanctioned;
    }

    /**
     * Set the date as a UNIX timestamp of the event
     *
     * @access public
     * @param int $date
     */
    public function setDate ($date) {
        if (!is_numeric ($date)) {
            throw new InvalidArgumentException ("Date must be a unix timestamp");
        }
        $this->date = $date;
    }

    /**
     * Return the UNIX timestamp of the date of the event
     *
     * @access public
     * @return int
     */
    public function getDate () {
        return $this->date;
    }

    /**
     * Set the status flag of the event
     *
     * @access public
     * @param int $status
     */
    public function setStatus ($status) {
        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Status must be an integer");
        }

        $status = intval ($status);
        if ($status < self::PENDING_STATUS || $status > self::DENIED_STATUS) {
            throw new InvalidArgumentException ("Invalid value for status");
        }

        $this->status = $status;
    }

    /**
     * Return the status flag of the event
     *
     * @access public
     * @return int
     */
    public function getStatus () {
        return $this->status;
    }

    /**
     * Set the User object associated with the poster of the event
     *
     * @access public
     * @param User $user
     */
    public function setUser (User $user) {
        $this->user = $user;
        if ($user->id != -1) {
            $this->userId = $user->id;
        }
    }

    /**
     * Return the User object associated with the poster of the event
     *
     * @access public
     * @return User
     */
    public function getUser () {
        return $this->user;
    }

    /**
     * Set the Platform object associated with the event
     *
     * @access public
     * @param Platform $platform
     */
    public function setPlatform (Platform $platform) {
        $this->platform = $platform;
        if ($platform->id != -1) {
            $this->platformId = $platform->id;
        }
    }

    /**
     * Return the Platform object associated with the event
     *
     * @access public
     * @return Platform
     */
    public function getPlatform () {
        return $this->platform;
    }

    /**
     * Return the date of the event expressed as a string. The following example shows the call to strftime that formats the string.
     * <code>
     * strftime ("%B %d, %Y", $this->date)
     * </code>
     *
     * @access public
     * @return string
     */
    public function getDateString () {
        return strftime ("%B %d, %Y", $this->date);
    }
}

/**
 * Event data access singleton class
 *
 * Data access class that will be used to read and write Event entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class EventDAO extends DAOBase {
    /**
     * Instance of EventDAO class
     * @access protected
     * @static
     * @var EventDAO
     */
    protected static $instance;
    /**
     * Name of database table holding Event data
     * @access protected
     * @var string
     */
    protected $tableName = "events";
    /**
     * Array of strings containing column names for an Event row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "userId", "platformId", "title", "description", "sanctioned", "date", "status");

    /**
     * Retrieve instance of an EventDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return EventDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Constructor. Calls DAOBase constructor and sets a default ORDER BY clause. Defaults to ORDER BY event.id
     *
     * @access protected
     */
    protected function __construct () {
        parent::__construct ();
        $this->order_by = "ORDER BY " . $this->tableName . "." . $this->columns[0];
    }

    /**
     * Load an instance of an Event entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Event
     */
    public function load ($id, $options=null) {
        if (!is_numeric ($id)) {
            throw new InvalidArgumentException ("Must pass the event id as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".id = ?";
        $this->query_params = array ($id);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an Event entity from the database for the first event happening after the date specified. Filter by platform and status flag as well
     *
     * @access public
     * @param int $platform
     * @param int $date
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Event
     */
    public function loadByNextDayPlatform ($platform, $date, $status, $options=null) {
        if (!is_numeric ($platform)) {
            throw new InvalidArgumentException ("Must pass the platform as first parameter");
        }

        if (!is_numeric ($date)) {
            throw new InvalidArgumentException ("Must pass the date as second parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as third parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        if (!is_array ($options) || !array_key_exists ("order", $options)) {
            $this->query_order = "ORDER BY {$this->tableName}.date ASC";
        }
        $this->query_where = "WHERE " . $this->getTableName () . ".date > ? AND {$this->tableName}.status = ? AND {$this->tableName}.platformId = ?";
        $this->query_params = array ($date, $status, $platform);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an Event entity from the database for the first event happening before the date specified. Filter by platform and status flag as well
     *
     * @access public
     * @param int $platform
     * @param int $date
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Event
     */
    public function loadByPreviousDayPlatform ($platform, $date, $status, $options=null) {
        if (!is_numeric ($platform)) {
            throw new InvalidArgumentException ("Must pass the platform as first parameter");
        }

        if (!is_numeric ($date)) {
            throw new InvalidArgumentException ("Must pass the date as second parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as third parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        if (!is_array ($options) || !array_key_exists ("order", $options)) {
            $this->query_order = "ORDER BY {$this->tableName}.date DESC";
        }
        $this->query_where = "WHERE " . $this->getTableName () . ".date < ? AND {$this->tableName}.status = ? AND {$this->tableName}.platformId = ?";
        $this->query_params = array ($date, $status, $platform);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an Event entity from the database for the first event happening after the date specified. Filter by status flag as well
     *
     * @access public
     * @param int $date
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Event
     */
    public function loadByNextDay ($date, $status, $options=null) {
        if (!is_numeric ($date)) {
            throw new InvalidArgumentException ("Must pass the date as first parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as second parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        if (!is_array ($options) || !array_key_exists ("order", $options)) {
            $this->query_order = "ORDER BY {$this->tableName}.date ASC";
        }
        $this->query_where = "WHERE " . $this->getTableName () . ".date > ? AND {$this->tableName}.status = ?";
        $this->query_params = array ($date, $status);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an Event entity from the database for the first event happening after the date specified. Filter by status flag as well
     *
     * @access public
     * @param int $date
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Event
     */
    public function loadByPreviousDay ($date, $status, $options=null) {
        if (!is_numeric ($date)) {
            throw new InvalidArgumentException ("Must pass the date as first parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as second parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        if (!is_array ($options) || !array_key_exists ("order", $options)) {
            $this->query_order = "ORDER BY {$this->tableName}.date DESC";
        }
        $this->query_where = "WHERE " . $this->getTableName () . ".date < ? AND {$this->tableName}.status = ?";
        $this->query_params = array ($date, $status);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Save an instance of an Event entity to the database
     *
     * @access public
     * @param Event $event
     * @return bool Return status of PDOStatement execute method
     */
    public function save (Event $event) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $event->$value;}
        $params[] = $event->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an Event entity from the database
     *
     * @access public
     * @param Event $event
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (Event $event) {
        require_once ("Attendance.php");

        $attendDAO = AttendanceDAO::getInstance ();
        $query = "DELETE FROM {$this->tableName}, {$attendDAO->getTableName ()} USING {$this->tableName} LEFT JOIN {$attendDAO->getTableName ()} ON {$this->tableName}.id = {$attendDAO->getTableName ()}.eventId WHERE {$this->tableName}.id = ?";
        //$query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($event->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an Event entity into the database 
     *
     * @access public
     * @param Event $event
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (Event $event) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $event->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $event->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of Event entities. Use options array to limit results read.
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function all ($options=null) {
        $this->resetQueryStrings ();
        $userDAO = UserDAO::getInstance ();
        $this->select_columns = array_merge ($this->select_columns, $this->buildColumnArray ());
        if (is_array ($options)) {
            $this->parseOptions ($options);
        }

        $query = "SELECT " . $this->query_select . " FROM " . $this->tableName . " " . $this->query_joins  . " " . $this->query_where . " " . $this->query_order . " " . $this->query_limit;
        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        if (!empty ($this->query_params)) {
            //print_r ($this->query_params);
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result_array = array ();
        while ($result = $stmt->fetch (PDO::FETCH_NUM)) {
            $event = new Event ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($event, $temp_array);

            if ($this->joins) {
                $platformDAO = PlatformDAO::getInstance ();

                $user = new User ();
                $temp_array = $userDAO->stripPrefixArray ($row);
                $userDAO->populateObject ($user, $temp_array);
                $event->user = $user;
                $platform = new Platform ();
                $temp_array = $platformDAO->stripPrefixArray ($row);
                $platformDAO->populateObject ($platform, $temp_array);
                $event->platform = $platform;
            }

            $result_array[] = $event;
        }

        return $result_array;

    }

    /**
     * Return count number of Event entities in the database
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function count ($options=null) {
        $this->resetQueryStrings ();
        $this->select_columns = array_merge ($this->select_columns, $this->buildColumnArray ());
        if (is_array ($options)) {
            $this->parseOptions ($options);
        }

        if (!$this->query_reset_lock) {
            $this->query_select = "COUNT({$this->columns[0]}) AS count";
        }
        $query = "SELECT " . $this->query_select . "  FROM " . $this->tableName . " " . $this->query_joins  . " " . $this->query_where . " " . $this->query_order;

        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        if (!empty ($this->query_params)) {
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result = $stmt->fetch ();
        if (!$result) {
            return 0;
        }

        return $result["count"];
    }

    /**
     * Return count number of Event entities in the database. Filter by status flag
     *
     * @access public
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countStatus ($status, $options=null) {
        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as first parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".status = ?";
        $this->query_params = array ($status);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Return count number of Event entities in the database. Filter by status flag and date range (start and end)
     *
     * @access public
     * @param int $status
     * @param int $start UNIX timestamp representing start date
     * @param int $end UNIX timestamp representing end date
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countStatusAndRange ($status, $start, $end, $options=null) {
        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as first parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }
        if (!is_numeric ($start)) {
            throw new InvalidArgumentException ("Must pass the start time as second parameter");
        }
        if (!is_numeric ($end)) {
            throw new InvalidArgumentException ("Must pass the end time as third parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        //$this->query_where = "WHERE " . $this->getTableName () . ".status = ?";
        $this->query_where = "WHERE " . $this->getTableName () . ".status = ? AND {$this->tableName}.date BETWEEN ? AND ?";
        $this->query_params = array ($status, $start, $end);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Return count number of Event entities in the database.  Filter by platform id, status flag and date range (start and end)
     *
     * @access public
     * @param int $platform
     * @param int $status
     * @param int $start UNIX timestamp representing start date
     * @param int $end UNIX timestamp representing end date
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countPlatformStatusAndRange ($platform, $status, $start, $end, $options=null) {
        if (!is_numeric ($platform)) {
            throw new InvalidArgumentException ("Must pass the platform id as first parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as second parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }
        if (!is_numeric ($start)) {
            throw new InvalidArgumentException ("Must pass the start time as third parameter");
        }
        if (!is_numeric ($end)) {
            throw new InvalidArgumentException ("Must pass the end time as fourth parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".platformId = ? AND {$this->tableName}.status = ? AND {$this->tableName}.date BETWEEN ? AND ?";
        $this->query_params = array ($platform, $status, $start, $end);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Return count number of Event entities in the database.  Filter by platform id and status flag
     *
     * @access public
     * @param int $platform
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countPlatformStatus ($platform, $status, $options=null) {
        if (!is_numeric ($platform)) {
            throw new InvalidArgumentException ("Must pass the platform id as first parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as second parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        } 

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".platformId = ? AND {$this->tableName}.status = ?";
        $this->query_params = array ($platform, $status);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Helper method used with various public load methods. Used to load an instance of an Event entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Event
     */
    private function loadGeneral ($options=null) {
        $userDAO = UserDAO::getInstance ();
        $this->resetQueryStrings ();
        $this->select_columns = array_merge ($this->select_columns, $this->buildColumnArray ());
        if (is_array ($options)) {
            $this->parseOptions ($options);
        }

        $query = "SELECT " . $this->query_select . " FROM " . $this->tableName . " " . $this->query_joins  . " " . $this->query_where . " " . $this->query_order . " LIMIT 1";

        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        if (!empty ($this->query_params)) {
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result = $stmt->fetch (PDO::FETCH_NUM);
        if (!$result) {
            return null;
        }

        $event = new Event ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($event, $temp_array);

        if ($this->joins) {
            $platformDAO = PlatformDAO::getInstance ();
            $user = new User ();
            $temp_array = $userDAO->stripPrefixArray ($row);
            $userDAO->populateObject ($user, $temp_array);
            $event->user = $user;
            $platform = new Platform ();
            $temp_array = $platformDAO->stripPrefixArray ($row);
            $platformDAO->populateObject ($platform, $temp_array);
            $event->platform = $platform;
        }
        return $event;
    }

    /**
     * Load instances of Event entities with the ids specified in the array param
     *
     * @access public
     * @param array $ids Array containing int ids of Event entities to load
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByIds ($ids, $options=null) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";
        $this->query_where = "WHERE " . $this->getTableName () . ".id IN (" . $str .")";
        $this->query_params = $ids;
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Delete instances of an Event entities with the ids specified in the ids array
     *
     * @access public
     * @param array $ids Array containing int ids of Event entities to delete
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return bool Return status of PDOStatement execute method
     */
    public function deleteByIds ($ids, $options=null) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        require_once ("Attendance.php");
        $attendDAO = AttendanceDAO::getInstance ();

        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";

        $query = "DELETE FROM {$this->tableName}, {$attendDAO->getTableName ()} USING {$this->tableName} LEFT JOIN {$attendDAO->getTableName ()} ON {$this->tableName}.id = {$attendDAO->getTableName ()}.eventId WHERE {$this->tableName}.id IN (" . $str . ")";
        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        $params = $ids;
        return $stmt->execute ($params);
    }

    /**
     * Load all instances of Event entities. Filter by status flag, start date and end date (UNIX timestamps). Use options array to limit results read.
     *
     * @access public
     * @param int $status
     * @param int $start UNIX timestamp of start date
     * @param int $end UNIX timestamp of end date
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByStatusAndRange ($status, $start, $end, $options=null) {
        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as first parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }
        if (!is_numeric ($start)) {
            throw new InvalidArgumentException ("Must pass the start as second parameter");
        }
        if (!is_numeric ($end)) {
            throw new InvalidArgumentException ("Must pass the end as third parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".status = ? AND {$this->tableName}.date BETWEEN ? AND ?";
        $this->query_params = array ($status, $start, $end);
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Load all instances of Event entities. Filter by status flag. Use options array to limit results read.
     *
     * @access public
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByStatus ($status, $options=null) {
        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as first parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".status = ?";
        $this->query_params = array ($status);
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Load all instances of Event entities. Filter by platform id and status flag. Use options array to limit results read.
     *
     * @access public
     * @param int $platform
     * @param int $status
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByPlatformStatus ($platform, $status, $options=null) {
        if (!is_numeric ($platform)) {
            throw new InvalidArgumentException ("Must pass the platform id as first parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as second parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".platformId = ? AND {$this->tableName}.status = ?";
        $this->query_params = array ($platform, $status);
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Load all instances of Event entities. Filter by platform id, status flag, start date and end date (UNIX timestamps). Use options array to limit results read.
     *
     * @access public
     * @param int $platform
     * @param int $status
     * @param int $start UNIX timestamp of start date
     * @param int $end UNIX timestamp of end date
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByPlatformStatusAndRange ($platform, $status, $start, $end, $options=null) {
        if (!is_numeric ($platform)) {
            throw new InvalidArgumentException ("Must pass the platform id as first parameter");
        }

        if (!is_numeric ($status)) {
            throw new InvalidArgumentException ("Must pass the event status as second parameter");
        }
        $status = intval ($status);
        if ($status < Event::PENDING_STATUS || $status > Event::DENIED_STATUS) {
            throw new InvalidArgumentException ("Event status is invalid");
        }
        if (!is_numeric ($start)) {
            throw new InvalidArgumentException ("Must pass the start as third parameter");
        }
        if (!is_numeric ($end)) {
            throw new InvalidArgumentException ("Must pass the end as fourth parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".platformId = ? AND {$this->tableName}.status = ? AND {$this->tableName}.date BETWEEN ? AND ?";
        $this->query_params = array ($platform, $status, $start, $end);
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Update status flags of Event entities with an id specified in the $ids array
     * 
     * @access public
     * @param int $status
     * @param array $ids Array containing int ids of Event entities to load
     * @return bool Return status of PDOStatement execute method
     */
    public function saveStatusByIds ($status, $ids) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";
        $query = "UPDATE {$this->tableName} SET status=? WHERE id IN (" . $str . ")";

        $stmt = self::$dbh->prepare ($query);
        $params = array_merge (array ($status), $ids);
        return $stmt->execute ($params);
    }

    /**
     * Parse the options array for limit clauses and order by clauses. The valid keys and value types are specified below.
     * limit - Page object. Will take values from a Paginator Page object and
     * set LIMIT and OFFSET portions of database query accordingly
     * 
     * joins - bool. If true, an INNER JOIN will be done to retrieve the
     * User and Platform associated with the event
     * 
     * order - string. Concatenate string with ORDER BY operator. Will add table name to 
     * field if only associated with current table.
     * @access private
     * @param array &$options
     */
    protected function parseOptions (&$options) {
        if (!is_array ($options)) {
            throw new InvalidArgumentException ("Options for a database access function must be in an array");
        }

        if (array_key_exists ("limit", $options) && $options["limit"] instanceof Page) {
            $this->query_limit .= $this->getLimitClause ($options["limit"]);
        }

        if (array_key_exists ("joins", $options) && $options["joins"] == true) {
            $userDAO = UserDAO::getInstance ();
            $platformDAO = PlatformDAO::getInstance ();
            $this->query_select .= ", " . $userDAO->buildColumnString () . ", {$platformDAO->buildColumnString ()}";
            $this->query_joins .= " INNER JOIN (" . $userDAO->getTableName () . ", {$platformDAO->getTableName ()}) ON (" . $userDAO->getTableName () . ".id = " . $this->getTableName () . ".userId AND {$platformDAO->getTableName ()}.id = {$this->tableName}.platformId) ";
            $this->select_columns = array_merge ($this->select_columns, $userDAO->buildColumnArray ());
            $this->select_columns = array_merge ($this->select_columns, $platformDAO->buildColumnArray ());
            $this->joins = true;
        }

        if (array_key_exists ("order", $options) && is_string ($options["order"])) {
            // Reference to attendance member
            if (strpos ($options["order"], ".") === false) {
                $this->query_order = "ORDER BY " . $this->tableName . "." . $options["order"];
            }
            // Reference to user member
            else if (strpos ($options["order"], "users.") === 0 && $this->joins) {
                $this->query_order = "ORDER BY " . $options["order"];
            }
            else {
                $this->query_order = "ORDER BY " . $options["order"];
            }

            //else {
            //    throw new InvalidArgumentException ("Invalid configuration for order option");
            //}
        }
    }
}

?>
