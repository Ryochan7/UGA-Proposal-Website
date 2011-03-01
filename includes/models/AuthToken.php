<?php
/**
 * File defines the AuthToken model class and AuthTokenDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");

/**
 * AuthToken model class for representing an attendance entity
 *
 * Class contains the members that represent the values of an AuthToken
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class AuthToken extends ModelBase {
    /**
     * Token string length as returned by uniqid (rand (), true)
     * @access public
     * @var int
     */
    const TOKEN_LENGTH = 23;
    /**
     * Life of a valid token in seconds
     * @access public
     * @var int
     */
    const MAX_EXPIRE = 259200; // 60 * 60 * 24 * 3. // 3 days
    /**
     * User id of token
     * @access protected
     * @var int
     */
    protected $userId = -1;
    /**
     * Token string made with uniqid (rand (), true)
     * @access protected
     * @var string
     */
    protected $token;
    /**
     * UNIX timestamp of the expire time of a token
     * @access protected
     * @var int
     */
    protected $expireTime;
    /**
     * User object of token
     * @access protected
     * @var User
     */
    protected $user;

    /**
     * Constructor. Make new AuthToken object, create a new token string or use passed string and set default
     * expire time
     *
     * @access public
     * @param string $token
     */
    public function __construct ($token=null) {
        if ($token && !is_string ($token)) {
            throw new InvalidArgumentException ("Constructor requires a string");
        }
        else if ($token == null) {
            $this->token = uniqid (rand (), true);
        }
        else if (strlen ($token) != self::TOKEN_LENGTH) {
            throw new InvalidArgumentException ("Token string must be " . self::TOKEN_LENGTH . " characters long");
        }
        else {
            $this->token = $token;
        }
        $this->expireTime = time () + self::MAX_EXPIRE;
    }

    /**
     * Set the user id of the token
     *
     * @access public
     * @param int $userId
     */
    public function setUserId ($userId) {
        $this->userId = $userId;
        $this->user = null;
    }

    /**
     * Return the user id of the token
     *
     * @access public
     * @return int
     */
    public function getUserId () {
        return $this->userId;
    }

    /**
     * Set the token string of the token
     *
     * @access public
     * @param string $token
     */
    public function setToken ($token) {
        $this->token = $token;
    }

    /**
     * Return the token string of the token
     *
     * @access public
     * @return string
     */
    public function getToken () {
        return $this->token;
    }

    /**
     * Set the expire time of a token
     *
     * @access public
     * @param int $expireTime
     */
    public function setExpireTime ($expireTime) {
        $this->expireTime = $expireTime;
    }

    /**
     * Return the expire time of a token
     *
     * @access public
     * @return int
     */
    public function getExpireTime () {
        return $this->expireTime;
    }

    /**
     * Set the User object associated with the token
     *
     * @access public
     * @param User $user
     */
    public function setUser (User $user) {
        $this->user = $user;
        $this->userId = $user->getId ();
    }

    /**
     * Return the User object associated with the token
     *
     * @access public
     * @return User
     */
    public function getUser () {
        return $this->user;
    }
}

/**
 * AuthToken data access singleton class
 *
 * Data access class that will be used to read and write AuthToken entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class AuthTokenDAO extends DAOBase {
    /**
     * Instance of AuthTokenDAO class
     * @access protected
     * @static
     * @var AuthTokenDAO
     */
    protected static $instance;
    /**
     * Name of database table holding AuthToken data
     * @access protected
     * @var string
     */
    protected $tableName = "authToken";
    /**
     * Array of strings containing column names for an AuthToken row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "userId", "token", "expireTime");

    /**
     * Retrieve instance of an AuthTokenDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return AuthTokenDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an AuthToken entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return AuthToken
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
     * Load an instance of an AuthToken entity from the database that has the token string specified
     *
     * @access public
     * @param string $token
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return AuthToken
     */
    public function loadByToken ($token, $options=null) {
        if (!is_string ($token)) {
            throw new InvalidArgumentException ("Must pass the attendance id as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".token = ?";
        $this->query_params = array ($token);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Save an instance of an AuthToken entity to the database
     *
     * @access public
     * @param AuthToken $token
     * @return bool Return status of PDOStatement execute method
     */
    public function save (AuthToken $token) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $token->$value;}
        $params[] = $token->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an AuthToken entity from the database
     *
     * @access public
     * @param AuthToken $token
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (AuthToken $token) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($token->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an Attendance entity into the database 
     *
     * @access public
     * @param AuthToken $token
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (AuthToken $token) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $token->$value;}

        //print_r ($params);
        $status = $stmt->execute ($params);
        if ($status) {
            $token->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of AuthToken entities. Use options array to limit results read.
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function all ($options=null) {
        $userDAO = UserDAO::getInstance ();

        $this->resetQueryStrings ();
        $this->select_columns = array_merge ($this->select_columns, $this->buildColumnArray ());
        if (is_array ($options)) {
            $this->parseOptions ($options);
        }

        $query = "SELECT " . $this->query_select . " FROM " . $this->tableName . " " . $this->query_joins  . " " . $this->query_where . " " . $this->query_order . " " . $this->query_limit;
        echo $query;
        $stmt = self::$dbh->prepare ($query);
        if (!empty ($this->query_params)) {
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result_array = array ();
        while ($result = $stmt->fetch (PDO::FETCH_NUM)) {
            $token = new AuthToken ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($token, $temp_array);

            if ($this->joins) {
                $user = new User ();
                $temp_array = $userDAO->stripPrefixArray ($row);
                $userDAO->populateObject ($user, $temp_array);
                $token->user = $user;
            }

            $result_array[] = $token;
        }

        return $result_array;

    }

    /**
     * Return count number of AuthToken entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an AuthToken entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return AuthToken
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

        $token = new AuthToken ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($token, $temp_array);

        if ($this->joins) {
            $user = new User ();
            $temp_array = $userDAO->stripPrefixArray ($row);
            $userDAO->populateObject ($user, $temp_array);
            $token->user = $user;
            //print_r ($token);
        }

        return $token;
    }


    /**
     * Method for finding and deleting all AuthToken entities that have an expireTime that is less than the current time (token has expired). Associated users that are still pending (have not gone through the verification process) will be deleted in the process.
     *
     * @access private
     * @return int
     */
    public function garbageCollect () {
        $userDAO = UserDAO::getInstance ();
        $delete_status = array (User::STATUS_PENDING, User::STATUS_BANNED);

        // Use LEFT JOIN to avoid deleting users that have completed the registration process
        $query = "DELETE FROM " . $this->tableName . ", {$userDAO->getTableName ()} USING {$this->tableName} LEFT JOIN ({$userDAO->getTableName ()}) ON ({$this->tableName}.userId = {$userDAO->getTableName ()}.id AND {$userDAO->getTableName ()}.status IN (" . implode (",", $delete_status) . ")) WHERE expireTime < " . time ();
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        return $stmt->execute ();
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
            // Reference to token member
            if (strpos ($options["order"], ".") === false) {
                $this->query_order = "ORDER BY " . $this->tableName . "." . $options["order"];
            }
            // Reference to users member
            else if (strpos ($options["order"], "users.") === 0) {
                $this->query_order = "ORDER BY " . $options["order"];
            }
            else {
                throw new InvalidArgumentException ("Invalid configuration for order option");
            }
        }
    }
}

?>
