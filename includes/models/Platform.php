<?php
/**
 * File defines the Platform model class and PlatformDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("Event.php");

/**
 * Platform model class for representing an Platform entity
 *
 * Class contains the members that represent the values of an Platform
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class Platform extends ModelBase {
    /**
     * Name of the platform
     * @access protected
     * @var string
     */
    protected $name;
    /**
     * Event instance
     * @access protected
     * @var int
     */
    protected $event;

    /**
     * Set the name of the platform
     *
     * @access public
     * @param string $name
     */
    public function setName ($name) {
        if (!is_string ($name)) {
            throw new InvalidArgumentException ();
        }

        $this->name = $name;
    }

    /**
     * Return the name of the platform
     *
     * @access public
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * Set an Event instance for joins
     *
     * @access public
     * @param Event $event
     */
    public function setEvent (Event $event) {
        $this->event = $event;
    }

    /**
     * Return the Event instance associated with a platform
     *
     * @access public
     * @return Event
     */
    public function getEvent () {
        return $this->event;
    }
}

/**
 * Platform data access singleton class
 *
 * Data access class that will be used to read and write Platform entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class PlatformDAO extends DAOBase {
    /**
     * Instance of PlatformDAO class
     * @access protected
     * @static
     * @var PlatformDAO
     */
    protected static $instance;
    /**
     * Name of database table holding Platform data
     * @access protected
     * @var string
     */
    protected $tableName = "platform";
    /**
     * Array of strings containing column names for an Platform row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "name");

    /**
     * Retrieve instance of an PlatformDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return PlatformDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an Platform entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Platform
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
     * Save an instance of an Platform entity to the database
     *
     * @access public
     * @param Platform $platform
     * @return bool Return status of PDOStatement execute method
     */
    public function save (Platform $platform) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $platform->$value;}
        $params[] = $platform->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an Platform entity from the database
     *
     * @access public
     * @param Platform $platform
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (Platform $platform) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($platform->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an Platform entity into the database 
     *
     * @access public
     * @param Platform $platform
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (Platform $platform) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $platform->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $platform->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of Platform entities. Use options array to limit results read.
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function all ($options=null) {
        $this->resetQueryStrings ();
        $eventDAO = EventDAO::getInstance ();
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
            $platform = new Platform ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($platform, $temp_array);

            if ($this->joins) {
                $event = new Event ();
                $temp_array = $eventDAO->stripPrefixArray ($row);
                //print_r ($temp_array);
                $eventDAO->populateObject ($event, $temp_array);
                $platform->event = $event;
                //print_r ($platform);
            }

            $result_array[] = $platform;
        }

        return $result_array;

    }

    /**
     * Return count number of Platform entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an Platform entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Platform
     */
    private function loadGeneral ($options=null) {
        $eventDAO = EventDAO::getInstance ();
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

        $platform = new Platform ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($platform, $temp_array);

        if ($this->joins) {
            $event = new Event ();
            $temp_array = $eventDAO->stripPrefixArray ($row);
            $eventDAO->populateObject ($event, $temp_array);
            
            $platform->event = $event;
            //print_r ($event);
        }
        return $platform;
    }

    /**
     * Parse the options array for limit clauses and order by clauses. The valid keys and value types are specified below.
     * limit - Page object. Will take values from a Paginator Page object and
     * set LIMIT and OFFSET portions of database query accordingly
     *
     * joins - bool. If true, an INNER JOIN will be done to retrieve the
     * Event associated with the platform
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
            $eventDAO = EventDAO::getInstance ();
            $this->query_select .= ", " . $eventDAO->buildColumnString ();
            $this->query_joins .= " INNER JOIN (" . $eventDAO->getTableName () . ") ON (" . $eventDAO->getTableName () . ".platformId = " . $this->getTableName () . ".id) ";
            $this->select_columns = array_merge ($this->select_columns, $eventDAO->buildColumnArray ());
            $this->joins = true;
        }

        if (array_key_exists ("order", $options) && is_string ($options["order"])) {
            // Reference to attendance member
            if (strpos ($options["order"], ".") === false) {
                $this->query_order = "ORDER BY " . $this->tableName . "." . $options["order"];
            }
            // Reference to user member
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
