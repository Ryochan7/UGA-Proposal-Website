<?php
/**
 * File defines the abstract class DAOBase which will be used by DAO objects
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Database helper for managing the database connection
 *
 * Class contains the database connection, helper methods
 * to build parts of query strings, and methods for grabbing
 * data from models and populating models
 * @abstract
 * @package UGA
 */
abstract class DAOBase {
    /**
     * Database object
     * @access protected
     * @var PDO
     */
    protected static $dbh; // Reference to PDO object
    /**
     * Table name
     * @access protected
     * @var string
     */
    protected $tableName = "";
    /**
     * Array of strings representing table columns
     *
     * Example: array ("id", "username", "password")
     * @access protected
     * @var array
     */
    protected $columns = array ();
    /**
     * Declare if a limit clause exists
     * @access protected
     * @var bool
     */
    protected $limit = false;
    /**
     * Declare if joins are needed
     * @access protected
     * @var bool
     */
    protected $joins = false;
    /**
     * Array of strings representing table columns to grab from database
     *
     * Used for specifing columns from multiple tables.
     * Example: array ("users.id", "users.username", "users.password", "event.id")
     * @access protected
     * @var array
     */
    protected $select_columns = array ();
    /**
     * String specifying columns to obtain from database
     * @access protected
     * @var string
     */
    protected $query_select = "";
    /**
     * String specifying a join clause for a query
     * @access protected
     * @var string
     */
    protected $query_joins = "";
    /**
     * String specifying a limit clause for a query
     * @access protected
     * @var string
     */
    protected $query_limit = "";
    /**
     * String specifying a order clause for a query
     * @access protected
     * @var string
     */
    protected $query_order = "";
    /**
     * String specifying a where clause for a query
     * @access protected
     * @var string
     */
    protected $query_where = "";
    /**
     * Array specifying params used in a prepared statement
     * @access protected
     * @var array
     */
    protected $query_params = array ();
    /**
     * Declare if query strings should not be reset
     * @access protected
     * @var bool
     */
    protected $query_reset_lock = false;


    /**
     * Constructor. Create object and establish database connection.
     * Calls connect method
     *
     * @access protected
     * @return DAOBase
     */
    protected function __construct () {
        $this->connect ();
    }

    /**
     * Establish database connection using PDO. Uses DB_HOST, DB_NAME, DB_USER,
     * DB_PASS constants defined in config file
     *
     * @access protected
     */
    protected function connect () {
        if (!isset (self::$dbh)) {
            self::$dbh = new PDO ("mysql:host=" . DB_HOST. ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        }
    }

    /**
     * Close database connection.
     *
     * @access public
     */
    public function close () {
        self::$dbh = null; // Will close database connection
    }

    /**
     * Generate the limit clause for a query based off
     * the properties of a Paginator page
     *
     * @access protected
     * @param Page $page
     * @return string
     */
    protected function getLimitClause (Page $page) {
        $subquery = "";
        $limit = $page->getPaginator ()->getItemsPerPage ();
        $offset = $limit * ($page->getPageNumber () - 1);
        $subquery .= " LIMIT " . $limit . " OFFSET " . $offset;
        return $subquery;
    }

    /**
     * Build array of strings with each value consisting of {$tableName}.{$value}
     *
     * For columns defined in a DAO class, generate array with strings of table names
     * added to each defined column. Each value with be in the form of {$this->tableName}.{$value}
     * @access public
     * @return array
     */
    public function buildColumnArray () {
        foreach ($this->columns as $value) {
            $select_columns[] = $this->tableName . "." . $value;
        }
        return $select_columns;
    }

    /**
     * Take an array of strings with prefixes (table names) and
     * return a new array with prefixes removed.
     * 
     * Example: prefix of users. array ("users.id") => array ("id")
     * @access public
     * @param array $row
     * @return array
     */
    public function stripPrefixArray (&$row) {
        $temp_array = array ();
        $length = strlen ($this->tableName . ".");
        foreach ($row as $key => $value) {
            if (strpos ($key, $this->tableName . ".") === 0) {
                $temp_array[substr ($key, $length)] = $value;
            }
        }
        //print_r ($temp_array);
        return $temp_array;
    }

    /**
     * Generate column string to use in a select query
     * @access public
     * @param string $prefix Optional - Prefix to add to columns
     * @return string
     */
    public function buildColumnString ($prefix=null) {
        $add_prefix = "";
        $columnString = "";
        if (isset ($prefix) && is_string ($prefix)) {
            $add_prefix = $prefix . ".";
        }
        else {
            $add_prefix = $this->tableName . ".";
        }

        $count = count ($this->columns);
        foreach ($this->columns as $key => $value) {
            $columnString .= $add_prefix . $value;
            if ($key < $count-1) {
                $columnString .= ", ";
            }
        }
        //echo $columnString . "\n";
        return $columnString;
    }

    /**
     * Generate column string to use in an insert prepared statement query
     * @access public
     * @param array &$columns Optional - Array of column names to use. Use $columns if not defined
     * @param bool $keepId Optional - Specify if id column should be kept. Default: false
     * @return string
     */
    public function buildInsertString (&$columns=null, $keepId=false) {
        $queryVars = array ();
        if (!empty ($columns)) {
            $queryVars = &$columns;
        }
        else {
            $queryVars = $this->columns;
            if (!$keepId) {
                $varCount = count ($queryVars);
                $idfound = false;
                for ($i = 0; ($i < $varCount) && !$idfound; $i++) {
                    if ($queryVars[$i] == "id") {
                        unset ($queryVars[$i]);
                        $idfound = true;
                    }
                }
            }
        }
        $addon = "(";

        $queryVars = array_values ($queryVars);
        $varCount = count ($queryVars);
        foreach ($queryVars as $key => $value) {
            $addon .= $value;
            if ($key < $varCount-1) {
                $addon .= ", ";
            }
        }
        $addon .= ") VALUES (";

        for ($i = 0; $i < $varCount; $i++) {
            $addon .= "?";
            if ($i < ($varCount - 1)) {
                $addon .= ", ";
            }
        }
        $addon .= ")";
        return $addon;
    }

    /**
     * Generate column string to use in an update prepared statement query
     * @access public
     * @param array &$columns Optional - Array of column names to use. Use $columns if not defined
     * @param bool $keepId Optional - Specify if id column should be kept. Default: false
     * @return string
     */
    public function buildUpdateString (&$columns=null, $keepId=false) {
        $addon = " SET ";
        $queryVars = array ();
        if (!empty ($columns)) {
            $queryVars = &$columns;
        }
        else {
            $queryVars = $this->columns;

            if (!$keepId) {
                $varCount = count ($queryVars);
                $idfound = false;

                for ($i = 0; ($i < $varCount) && !$idfound; $i++) {
                    if ($queryVars[$i] == "id") {
                        unset ($queryVars[$i]);
                        $idfound = true;
                    }
                }
            }
        }

        $queryVars = array_values ($queryVars);
        $varCount = count ($queryVars);

        for ($i = 0; $i < $varCount; $i++) {
            $addon .= $queryVars[$i] . "=?" ;
            if ($i < ($varCount - 1)) {
                $addon .= ", ";
            }
        }
        return $addon;    
    }

    /**
     * Populate model object with data from a database row
     *
     * Row parameter should be an associated array with key being a column name and
     * value being the value obtained from the database. Method will find setter methods
     * for the model object to update the value in the model
     * @access protected
     * @param ModelBase $object
     * @param array &$row
     */
    protected function populateObject ($object, &$row) {
        foreach ($row as $key => $value) {
            if (method_exists ($object, "set" . ucfirst($key))) {
                $object->$key = $value;
            }
        }
    }

    /**
     * Reset query strings used when generating a query
     * @access protected
     */
    protected function resetQueryStrings () {
        if (!$this->query_reset_lock) {
            $this->limit = $this->joins = false;
            $this->select_columns = array ();
            $this->query_select = $this->buildColumnString ();
            $this->query_joins = "";
            $this->query_limit = "";
            $this->query_order = "";
            $this->query_where = "";
            $this->query_params = array ();
        }
    }

    /**
     * Return table name
     * @access public
     * @return string
     */
    public function getTableName () {
        return $this->tableName;
    }

    /**
     * Return copy of columns array
     * @access protected
     * @return array
     */
    protected function getColumns () {
        return $this->columns;
    }

    /**
     * Return database connection object
     *
     * Return database connection object for manual entry of queries
     * and performing other operations on the database. Use is discourage
     * @access public
     * @return PDO
     */
    public function getConnection () {
        return self::$dbh;
    }

    /**
     * Abstract placeholder for Singleton pattern
     * @abstract
     * @static
     * @access public
     */
    abstract static public function getInstance ();

}

?>
