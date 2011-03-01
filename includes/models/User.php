<?php
/**
 * File defines the User model class and UserDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();
/**
 * InvalidStatusException exception indicates that an invalid value for status was passed
 * @package UGA
 */
class InvalidStatusException extends Exception {};
/**
 * InvalidUserTypeException exception indicates that an invalid value for a user type was passed
 * @package UGA
 */
class InvalidUserTypeException extends Exception {};

/**
 * User model class for representing an User entity
 *
 * Class contains the members that represent the values of an User
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class User extends ModelBase {
    /**
     * NULL user type int flag
     * @access public
     * @var int
     */
    const NULL_TYPE = -1;
    /**
     * ANONYMOUS user type int flag. Used for guests
     * @access public
     * @var int
     */
    const ANONYMOUS_TYPE = 1;
    /**
     * ADMIN user type int flag. Users with this user type are declared an admin of the site
     * @access public
     * @var int
     */
    const ADMIN_TYPE = 2;
    /**
     * TRUSTED_TYPE user type int flag. Users with this user type are allowed more access to the system than a regular user
     * @access public
     * @var int
     */
    const TRUSTED_TYPE = 3;
    /**
     * REGUSER user type int flag. Ordinary registered users have this user type
     * @access public
     * @var int
     */
    const REGUSER_TYPE = 4;
    /**
     * PENDING status int flag. Users with this status are pending in the system (not through verification process)
     * @access public
     * @var int
     */
    const STATUS_PENDING = 1;
    /**
     * NEEDADMIN status int flag. Users with this status have gone through the verification process but are not allowed to access the site yet.
     * @access public
     * @var int
     */
    const STATUS_NEEDADMIN = 2;
    /**
     * OK status int flag. Users with this status have gone through the verification process and have been approved by the admin. The user type for a user should be one confirming a registered user
     * @access public
     * @var int
     */
    const STATUS_OK = 3;
    /**
     * BANNED status int flag. Users with this status have been banned from using the system
     * @access public
     * @var int
     */
    const STATUS_BANNED = 4;
    /**
     * ISU_EMAIL_DOMAIN specifies the email domain of Illinois State University
     * @access public
     * @var int
     */
    const ISU_EMAIL_DOMAIN = "ilstu.edu";
    /**
     * User type. Defaults to ANONYMOUS_TYPE
     * @access protected
     * @var int
     */
    protected $userType = self::ANONYMOUS_TYPE;
    /**
     * User name of a user
     * @access protected
     * @var string
     */
    protected $userName;
    /**
     * SHA1 password hash
     * @access protected
     * @var string
     */
    protected $passHash;
    /**
     * Ulid of a user
     * @access protected
     * @var string
     */
    protected $ulid;
    /**
     * Status int flag of the user. Defaults to STATUS_PENDING
     * @access protected
     * @var int
     */
    protected $status = self::STATUS_PENDING;
    /**
     * Steam id of the user
     * @access protected
     * @var string
     */
    protected $steamId;
    /**
     * Xbox Live id of the user
     * @access protected
     * @var string
     */
    protected $xboxId;
    /**
     * PlayStation Network id of the user
     * @access protected
     * @var string
     */
    protected $psnId;
    /**
     * Wii Friend Code of the user
     * @access protected
     * @var string
     */
    protected $wiiId;

    /**
     * Returns the url of the page that can be used
     * to display the object
     *
     * @access public
     * @return string
     */
    public function getAbsoluteURL () {
        $url = "view_profile.php?id={$this->id}";
        return $url;
    }

    /**
     * Return the edit URL of the user
     *
     * @access public
     * @return string
     */
    public function getEditProfileUrl () {
        return joinPath (BASE_URL, "edit_profile.php?id={$this->id}");
    }

    /**
     * Set the status int flag of the user
     *
     * @access public
     * @param int $userType
     */
    public function setUserType ($userType) {
        switch ($userType) {
            case self::ANONYMOUS_TYPE:
            case self::ADMIN_TYPE:
            case self::TRUSTED_TYPE:
            case self::REGUSER_TYPE:
                $this->userType = $userType;
                break;
            default:
                throw new InvalidUserTypeException ("An invalid integer was passed to setUserType");
        }
    }

    /**
     * Return the status int flag of the user
     *
     * @access public
     * @return int
     */
    public function getUserType () {
        return $this->userType;
    }

    /**
     * Set the user name of the user
     *
     * @access public
     * @param string $userName
     */
    public function setUserName ($userName) {
        $this->userName = $userName;
    }

    /**
     * Return the user name of the user
     *
     * @access public
     * @return string
     */
    public function getUserName () {
        return $this->userName;
    }

    /**
     * Set the SHA1 password hash of the user
     *
     * @access public
     * @param string $passHash
     */
    public function setPassHash ($passHash) {
        $this->passHash = $passHash;
    }

    /**
     * Return the SHA1 password hash of the user
     *
     * @access public
     * @return string
     */
    public function getPassHash () {
        return $this->passHash;
    }

    /**
     * Set the ULID of the user
     *
     * @access public
     * @param string $ulid
     */
    public function setUlid ($ulid) {
        $this->ulid = $ulid;
    }

    /**
     * Return the ULID of the user
     *
     * @access public
     * @return ulid
     */
    public function getUlid () {
        return $this->ulid;
    }

    /**
     * Set the status flag of the user
     *
     * @access public
     * @param int $status
     */
    public function setStatus ($status) {
        switch ($status) {
            case self::STATUS_PENDING:
            case self::STATUS_NEEDADMIN:
            case self::STATUS_OK:
            case self::STATUS_BANNED:
                $this->status = $status;
                break;
            default:
                throw new InvalidStatusException ("An invalid integer was passed to setStatus");
        }
    }

    /**
     * Return the status flag of the user
     *
     * @access public
     * @return int
     */
    public function getStatus () {
        return $this->status;
    }

    /**
     * Set the Steam id of the user
     *
     * @access public
     * @param string $steamId
     */
    public function setSteamId ($steamId) {
        $this->steamId = $steamId;
    }

    /**
     * Return the Steam id of the user
     *
     * @access public
     * @return string
     */
    public function getSteamId () {
        return $this->steamId;
    }

    /**
     * Set the Xbox Live id of the user
     *
     * @access public
     * @param string $xboxId
     */
    public function setXboxId ($xboxId) {
        $this->xboxId = $xboxId;
    }

    /**
     * Return the Xbox Live id of the user
     *
     * @access public
     * @return string
     */
    public function getXboxId () {
        return $this->xboxId;
    }

    /**
     * Set the PlayStation Network id of the user
     *
     * @access public
     * @param string $psnId
     */
    public function setPsnId ($psnId) {
        $this->psnId = $psnId;
    }

    /**
     * Return the PlayStation Network id of the user
     *
     * @access public
     * @return string
     */
    public function getPsnId () {
        return $this->psnId;
    }

    /**
     * Set the Wii Friend Code of the user
     *
     * @access public
     * @param string $wiiId
     */
    public function setWiiId ($wiiId) {
        $this->wiiId = $wiiId;
    }

    /**
     * Return the Wii Friend Code of the user
     *
     * @access public
     * @return string
     */
    public function getWiiId () {
        return $this->wiiId;
    }

    /**
     * Returns a bool indicating if a user is an admin. Method checks the user status for STATUS_OK and the userType for ADMIN_TYPE
     *
     * @access public
     * @return bool
     */
    public function isAdmin () {
        return ($this->status == self::STATUS_OK) && ($this->userType == self::ADMIN_TYPE);
    }

    /**
     * Returns a bool indicating if the user is a valid user. Method checks the userType for valid user choices and status for STATUS_OK
     *
     * @access public
     * @return bool
     */
    public function validUser () {
        $valid_user = false;
        if ($this->userType == self::ANONYMOUS_TYPE) {
            $valid_user = false;
        }
        else if ($this->userType == self::NULL_TYPE) {
            $valid_user = false;
        }
        else if ($this->status != self::STATUS_OK) {
            $valid_user = false;
        }
        else {
            $valid_user = true;
        }
        return $valid_user;
    }

    /**
     * Return the Gravatar image URL associated with the email of the user
     *
     * @access public
     * @return string
     */
    public function getGravatarImage () {
        $grav_url = "http://www.gravatar.com/avatar/" . md5( strtolower( trim( "{$this->ulid}@" . self::ISU_EMAIL_DOMAIN ) ) );
        return $grav_url;
    }
}

/**
 * User data access singleton class
 *
 * Data access class that will be used to read and write User entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class UserDAO extends DAOBase {
    /**
     * Instance of UserDAO class
     * @access protected
     * @static
     * @var UserDAO
     */
    protected static $instance;
    /**
     * Name of database table holding User data
     * @access protected
     * @var string
     */
    protected $tableName = "users";
    /**
     * Array of strings containing column names for an User row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "userType",  "userName", "passHash", "ulid", "status", "steamId", "xboxId", "psnId", "wiiId");

    /**
     * Retrieve instance of an UserDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return UserDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }


    /**
     * Constructor. Calls DAOBase constructor and sets a default ORDER BY clause. Defaults to ORDER BY users.id
     *
     * @access protected
     */
    protected function __construct () {
        parent::__construct ();
        $this->order_by = "ORDER BY {$this->tableName}." . $this->columns[0];
    }

    /**
     * Load an instance of an User entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return User
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
     * Load an instance of an User entity from the database that has the username specified
     *
     * @access public
     * @param string $username
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return User
     */
    public function loadByUsername ($username, $options=null) {
        if (!is_string ($username)) {
            throw new InvalidArgumentException ("Must pass a string as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".userName = ?";
        $this->query_params = array ($username);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an User entity from the database that has the ulid specified
     *
     * @access public
     * @param string $ulid
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return TaggedArticle
     */
    public function loadByUlid ($ulid, $options=null) {
        if (!is_string ($ulid)) {
            throw new InvalidArgumentException ("Must pass a string as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".ulid = ?";
        $this->query_params = array ($ulid);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Save an instance of an User entity to the database
     *
     * @access public
     * @param User $user
     * @return bool Return status of PDOStatement execute method
     */
    public function save (User $user) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $user->$value;}
        $params[] = $user->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an User entity from the database. LEFT JOIN clauses will be added to delete any associated attendance records, pages, articles and events
     *
     * @access public
     * @param User $user
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (User $user) {
        // Import associated DAOs
        require_once ("Attendance.php");
        require_once ("Page.php");
        require_once ("Article.php");
        require_once ("Event.php");

        $attendDAO = AttendanceDAO::getInstance ();
        $pagesDAO = PageDAO::getInstance ();
        $articlesDAO = ArticleDAO::getInstance ();
        $eventsDAO = EventDAO::getInstance ();

        // Use LEFT JOIN in case user does not have some entries
        $query = "DELETE FROM {$this->tableName}, {$attendDAO->getTableName ()}, {$pagesDAO->getTableName ()}, {$articlesDAO->getTableName ()}, {$eventsDAO->getTableName ()} USING {$this->tableName} LEFT JOIN {$attendDAO->getTableName ()} ON {$this->tableName}.id = {$attendDAO->getTableName ()}.userId LEFT JOIN {$pagesDAO->getTableName ()} ON {$this->tableName}.id = {$pagesDAO->getTableName ()}.userId LEFT JOIN {$articlesDAO->getTableName ()} ON {$this->tableName}.id = {$articlesDAO->getTableName ()}.userId LEFT JOIN {$eventsDAO->getTableName ()} ON {$this->tableName}.id = {$eventsDAO->getTableName ()}.userId WHERE {$this->tableName}.id = ?";

        $stmt = self::$dbh->prepare ($query);
        $params = array ($user->id);
        $status = $stmt->execute ($params);

        return $status;
    }

    /**
     * Insert an instance of an User entity into the database 
     *
     * @access public
     * @param User $user
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (User $user) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $user->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $user->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of User entities. Use options array to limit results read.
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function all ($options=null) {
        $this->resetQueryStrings ();
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
        while ($result = $stmt->fetchObject ("User")) {
            $result_array[] = $result;
        }

        return $result_array;

    }

    /**
     * Return count number of User entities in the database
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
     * Return count number of User entities in the database filter by the identity code specified
     *
     * @access public
     * @param string $identity Specify steamId, xboxId, psnId, or wiiId
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countIdentity ($identity, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $column = "";
        switch ($identity) {
            case "steam":
                $column = "steamId";
                break;
            case "xbox":
                $column = "xboxId";
                break;
            case "psn":
                $column = "psnId";
                break;
            case "wii":
                $column = "wiiId";
                break;
            default:
                throw new InvalidArugmentException ("Identity {$identity} is not recognized");
        }

        $this->query_where = "WHERE " . $this->getTableName () . ".{$column} IS NOT NULL AND {$this->getTableName ()}.{$column} != ''";
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;
        return $result;
    }

    /**
     * Return count number of User entities in the database by which users have a user name that start with a specified letter
     *
     * @access public
     * @param string $letter
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countLetter ($letter, $options=null) {
        if (!is_string ($letter) || !preg_match ("/[a-z]{1}/", $letter)) {
            throw new InvalidArgumentException ("Must pass a letter as the first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $this->query_where = "WHERE " . $this->getTableName () . ".userName LIKE ?";
        $this->query_params = array ($letter . "%");
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;
        return $result;
    }

    /**
     * Helper method used with various public load methods. Used to load an instance of an User entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return User
     */
    private function loadGeneral ($options=null) {
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

        $result = $stmt->fetchObject ("User");
        if (!$result) {
            return null;
        }
        return $result;

    }

    /**
     * Load all instances of User entities that have a status of PENDING or NEEDADMIN. Use options array to limit results read.
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allPendingUsers ($options=null) {
        $pending_array = array (User::STATUS_PENDING, User::STATUS_NEEDADMIN);
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".status IN (" . implode (",", $pending_array) . ")";
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Load instances of User entities with the ids specified in the array param
     *
     * @access public
     * @param array $ids Array containing int ids of User entities to load
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
     * Delete instances of a User entities with the ids specified in the ids array. LEFT JOIN clauses will be added to delete any associated attendance records, pages, articles and events
     *
     * @access public
     * @param array $ids Array containing int ids of User entities to delete
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return bool Return status of PDOStatement execute method
     */
    public function deleteByIds ($ids, $options=null) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        // Import associated DAOs
        require_once ("Attendance.php");
        require_once ("Page.php");
        require_once ("Article.php");
        require_once ("Event.php");

        $attendDAO = AttendanceDAO::getInstance ();
        $pagesDAO = PageDAO::getInstance ();
        $articlesDAO = ArticleDAO::getInstance ();
        $eventsDAO = EventDAO::getInstance ();

        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";

        // Use LEFT JOIN in case user does not have some entries
        $query = "DELETE FROM {$this->tableName}, {$attendDAO->getTableName ()}, {$pagesDAO->getTableName ()}, {$articlesDAO->getTableName ()}, {$eventsDAO->getTableName ()} USING {$this->tableName} LEFT JOIN {$attendDAO->getTableName ()} ON {$this->tableName}.id = {$attendDAO->getTableName ()}.userId LEFT JOIN {$pagesDAO->getTableName ()} ON {$this->tableName}.id = {$pagesDAO->getTableName ()}.userId LEFT JOIN {$articlesDAO->getTableName ()} ON {$this->tableName}.id = {$articlesDAO->getTableName ()}.userId LEFT JOIN {$eventsDAO->getTableName ()} ON {$this->tableName}.id = {$eventsDAO->getTableName ()}.userId WHERE {$this->tableName}.id IN ({$str})";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = $ids;
        $status = $stmt->execute ($params);
        return $status;
    }

    /**
     * Update the instances of User with the specified status and with the ids specified
     *
     * @access public
     * @param int $status
     * @param array $ids Array containing int ids of User entities to load
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
        $query = "UPDATE " . $this->tableName . " SET status=? WHERE id IN (" . $str . ")";

        $stmt = self::$dbh->prepare ($query);
        $params = array_merge (array ($status), $ids);
        return $stmt->execute ($params);
    }

    /**
     * Load instances of User entities with the specified identity defined
     *
     * @access public
     * @param string $identity Specify steamId, xboxId, psnId, or wiiId
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByIdentity ($identity, $options=null) {
        if (!is_string ($identity)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $column = "";
        switch ($identity) {
            case "steam":
                $column = "steamId";
                break;
            case "xbox":
                $column = "xboxId";
                break;
            case "psn":
                $column = "psnId";
                break;
            case "wii":
                $column = "wiiId";
                break;
            default:
                throw new InvalidArugmentException ("Identity {$identity} is not recognized");
        }

        $this->query_where = "WHERE " . $this->getTableName () . ".{$column} IS NOT NULL AND {$this->getTableName ()}.{$column} != ''";
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Load instances of User entities with a user name that start with a specified letter
     *
     * @access public
     * @param string $letter
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByLetter ($letter, $options=null) {
        if (!is_string ($letter) || !preg_match ("/[a-z]{1}/", $letter)) {
            throw new InvalidArgumentException ("Must pass a letter as the first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $this->query_where = "WHERE " . $this->getTableName () . ".userName LIKE ?";
        $this->query_params = array ($letter . "%");
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Parse the options array for limit clauses and order by clauses. The valid keys and value types are specified below.
     * limit - Page object. Will take values from a Paginator Page object and
     * set LIMIT and OFFSET portions of database query accordingly
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

        if (array_key_exists ("order", $options) && is_string ($options["order"])) {
            // Reference to attendance member
            if (strpos ($options["order"], ".") === false) {
                $this->query_order = "ORDER BY " . $this->tableName . "." . $options["order"];
            }
            else {
                throw new InvalidArgumentException ("Invalid configuration for order option");
            }
        }
    }
}

?>
