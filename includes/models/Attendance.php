<?php
/**
 * File defines the Attendance model class and AttendanceDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");
require_once ("Event.php");
require_once "Mail.php";

/**
 * Attendance model class for representing an attendance entity
 *
 * Class contains the members that represent the values of an Attendance
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class Attendance extends ModelBase {
    /**
     * User id of associated attending user
     * @access protected
     * @var int
     */
    protected $userId;
    /**
     * Event id of associated event
     * @access protected
     * @var int
     */
    protected $eventId;
    /**
     * User object of associated user
     * @access protected
     * @var User
     */
    protected $user;

    /**
     * Set the user id of the attendance record
     *
     * @access public
     * @param int $userId
     */
    public function setUserId ($userId) {
        if (!is_numeric ($userId)) {
            throw new InvalidArgumentException ();
        }
        $this->userId = intval ($userId);
    }

    /**
     * Return the user id of the attendance record
     *
     * @access public
     * @return int
     */
    public function getUserId () {
        return $this->userId;
    }

    /**
     * Set the event id of the attendance record
     *
     * @access public
     * @param int $eventId
     */
    public function setEventId ($eventId) {
        if (!is_numeric ($eventId)) {
            throw new InvalidArgumentException ();
        }
        $this->eventId = intval ($eventId);
    }

    /**
     * Return the event id
     *
     * @access public
     * @return int
     */
    public function getEventId () {
        return $this->eventId;
    }

    /**
     * Set the User of the object
     *
     * @access public
     * @param User $user
     */
    public function setUser (User $user) {
        $this->user = $user;
    }

    /**
     * Return the associated User entity
     *
     * @access public
     * @return User
     */
    public function getUser () {
        return $this->user;
    }

    /**
     * Find and email all attendees of an Event
     *
     * @access public
     * @static
     * @param Event $event
     */
    public static function emailAttendees (Event $event, User $ignore_user=null) {
        $attendDAO = AttendanceDAO::getInstance ();
        $attend_array = $attendDAO->allByEvent ($event, array ("joins" => true));
        foreach ($attend_array as $attend) {
            if ((!$ignore_user || $attend->getUser ()->getId () != $ignore_user->getId ()) && defined ("SMTP_HOST") && strcmp (SMTP_HOST, "") != 0) {
                $from_addr = EMAIL_ADDRESS;
                $to = "{$attend->getUser ()->getUlid ()}@" . User::ISU_EMAIL_DOMAIN;
                $subject = "Event details updated for event " . htmlspecialchars (stripslashes ($event->getTitle ())) . " | ". SITE_NAME;
                $body = "Here is a copy of the newest description for the event:\n\n" . htmlspecialchars (stripslashes ($event->getDescription ())) . "\n\n";
                $body .= "Event Date: {$event->getDateString ()}\n\n";
                $body .= "Event Page: " . joinPath (BASE_URL, $event->getAbsoluteUrl ());

                $headers = array ("From" => $from_addr, "To" => $to, "Subject" => $subject);
                $stmp = Mail::factory ("smtp", array ("host" => SMTP_HOST, "auth" => true, "username" => SMTP_USERNAME, "password" => SMTP_PASSWORD));
                $mail = $stmp->send ($to, $headers, $body);
            }            
        }
    }

}

/**
 * Attendance data access singleton class
 *
 * Data access class that will be used to read and write Attendance entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class AttendanceDAO extends DAOBase {
    /**
     * Instance of AttendanceDAO class
     * @access protected
     * @static
     * @var AttendanceDAO
     */
    protected static $instance;
    /**
     * Name of database table holding Attendance data
     * @access protected
     * @var string
     */
    protected $tableName = "attendance";
    /**
     * Array of strings containing column names for an Attendance row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "userId", "eventId");

    /**
     * Retrieve instance of an AttendanceDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return AttendanceDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an Attendance entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Attendance
     */
    public function load ($id, $options=null) {
        if (!is_numeric ($id)) {
            throw new InvalidArgumentException ("Must pass the attendance id as first parameter");
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
     * Save an instance of an Attendance entity to the database
     *
     * @access public
     * @param Attendance $attend
     * @return bool Return status of PDOStatement execute method
     */
    public function save (Attendance $attend) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $attend->$value;}
        $params[] = $attend->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an Attendance entity from the database
     *
     * @access public
     * @param Attendance $attend
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (Attendance $attend) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($attend->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an Attendance entity into the database 
     *
     * @access public
     * @param Attendance $attend
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (Attendance $attend) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $attend->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $attend->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of Attendance entities. Use options array to limit results read.
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
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result_array = array ();
        while ($result = $stmt->fetch (PDO::FETCH_NUM)) {
            $attend = new Attendance ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($attend, $temp_array);

            if ($this->joins) {
                $user = new User ();
                $temp_array = $userDAO->stripPrefixArray ($row);
                $userDAO->populateObject ($user, $temp_array);
                $attend->user = $user;
                //print_r ($attend);
            }

            $result_array[] = $attend;
        }

        return $result_array;
    }

    /**
     * Return count number of Attendance entities in the database
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
     * Return count number of ArticleTag entities in the database that correspond to a specified Event
     *
     * @access public
     * @param Event $event
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countByEvent (Event $event, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $this->query_where = "WHERE " . $this->getTableName () . ".eventId = ?";
        $this->query_params = array ($event->id);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;
        return $result;
    }

    /**
     * Load all instances of Attendance entities with the specified event id integer. Use options array to limit results read.
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByEventId ($id, $options=null) {
        if (!is_numeric ($id)) {
            throw new InvalidArgumentException ("Must pass the attendance id as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".eventId = ?";
        $this->query_params = array ($id);
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Helper method used with various public load methods. Used to load an instance of an ArticleTag entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Attendance
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

        $attend = new Attendance ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($attend, $temp_array);

        if ($this->joins) {
            $user = new User ();
            $temp_array = $userDAO->stripPrefixArray ($row);
            $userDAO->populateObject ($user, $temp_array);
            $attend->user = $user;
            //print_r ($attend);
        }
        return $attend;

    }

    /**
     * Load an instance of an Attendance entity from the database with the specified Event and User
     *
     * @access public
     * @param Event $event
     * @param User $user
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Attendance
     */
    public function loadExists (Event $event, User $user, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE {$this->getTableName ()}.eventId = ? AND {$this->getTableName ()}.userId = ?";
        $this->query_params = array ($event->id, $user->id);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load all instances of Attendance entities corresponding to the specified Event. Use options array to limit results read.
     *
     * @access public
     * @param Event $event
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByEvent (Event $event, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE {$this->getTableName ()}.eventId = ?";
        $this->query_params = array ($event->id);
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Parse the options array for limit clauses and order by clauses. The valid keys and value types are specified below.
     * limit - Page object. Will take values from a Paginator Page object and
     * set LIMIT and OFFSET portions of database query accordingly
     * 
     * joins - bool. If true, an INNER JOIN will be done to retrieve the
     * User associated with the article
     * 
     * order - string. Concatenate string with ORDER BY operator.
     * Will add table name to field if only associated with current table.
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
            $this->query_select .= ", " . $userDAO->buildColumnString ();
            $this->query_joins .= " INNER JOIN (" . $userDAO->getTableName () . ") ON (" . $userDAO->getTableName () . ".id = " . $this->getTableName () . ".userId) ";
            $this->select_columns = array_merge ($this->select_columns, $userDAO->buildColumnArray ());
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
            // Reference to events member
            else if (strpos ($options["order"], "events.") === 0 && $this->joins) {
                $this->query_order = "ORDER BY " . $options["order"];
            }
            else {
                throw new InvalidArgumentException ("Invalid configuration for order option");
            }
        }
    }
}

?>
