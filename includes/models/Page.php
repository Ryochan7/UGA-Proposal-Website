<?php
/**
 * File defines the Page model class and PageDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");

/**
 * PageModel model class for representing an PageModel entity
 *
 * Class contains the members that represent the values of an PageModel
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class PageModel extends ModelBase {
    /**
     * User id of page author
     * @access protected
     * @var int
     */
    protected $userId;
    /**
     * Title of a page
     * @access protected
     * @var string
     */
    protected $title;
    /**
     * XHTML content string of a page
     * @access protected
     * @var string
     */
    protected $content;
    /**
     * String representing the relative file path of a template file. Defaults to blank
     * @access protected
     * @var string
     */
    protected $template = "";
    /**
     * Published bool flag of page
     * @access protected
     * @var bool
     */
    protected $published;
    /**
     * User object corresponding to page author
     * @access protected
     * @var User
     */
    protected $user;

    /**
     * Returns the url of the page that can be used
     * to display the object
     *
     * @access public
     * @return string
     */
    public function getAbsoluteURL () {
        $url = "view_page.php?id={$this->id}";
        return $url;
    }

    /**
     * Set the user id of the page
     *
     * @access public
     * @param int $userId
     */
    public function setUserId ($userId) {
        $this->userId = $userId;
    }

    /**
     * Return the user id of the page
     *
     * @access public
     * @return int
     */
    public function getUserId () {
        return $this->userId;
    }

    /**
     * Set the title of the page
     *
     * @access public
     * @param string $title
     */
    public function setTitle ($title) {
        $this->title = $title;
    }

    /**
     * Return the title of the page
     *
     * @access public
     * @return string
     */
    public function getTitle () {
        return $this->title;
    }

    /**
     * Set the XHTML content string of the page
     *
     * @access public
     * @param string $content
     */
    public function setContent ($content) {
        $this->content = $content;
    }

    /**
     * Return the XHTML content string of the page
     *
     * @access public
     * @return string
     */
    public function getContent () {
        return $this->content;
    }

    /**
     * Set the relative template file path of the page
     *
     * @access public
     * @param string $templtae
     */
    public function setTemplate ($template) {
        $this->template = $template;
    }

    /**
     * Return the relative template file path of the page
     *
     * @access public
     * @return string
     */
    public function getTemplate () {
        return $this->template;
    }

    /**
     * Set the published bool flag of the page
     *
     * @access public
     * @param bool $published
     */
    public function setPublished ($published) {
        $this->published = $published;
    }

    /**
     * Return the published bool flag of the page
     *
     * @access public
     * @return bool
     */
    public function getPublished () {
        return $this->published;
    }

    /**
     * Set the User object of the page corresponding to the author of the page
     *
     * @access public
     * @param User $user
     */
    public function setUser (User $user) {
        $this->user = $user;
        $this->userId = $user->id;
    }

    /**
     * Return the User object of the page corresponding to the author of the page
     *
     * @access public
     * @return User
     */
    public function getUser () {
        return $this->user;
    }
}

/**
 * PageModel data access singleton class
 *
 * Data access class that will be used to read and write PageModel entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class PageDAO extends DAOBase {
    /**
     * Instance of PageDAO class
     * @access protected
     * @static
     * @var PageDAO
     */
    protected static $instance;
    /**
     * Name of database table holding PageModel data
     * @access protected
     * @var string
     */
    protected $tableName = "pages";
    /**
     * Array of strings containing column names for an Page row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "userId", "title", "content", "template", "published");

    /**
     * Retrieve instance of an PageDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return PageDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an PageModel entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return PageModel
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
     * Save an instance of an PageModel entity to the database
     *
     * @access public
     * @param PageModel $page
     * @return bool Return status of PDOStatement execute method
     */
    public function save (PageModel $page) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $page->$value;}
        $params[] = $page->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an PageModel entity from the database
     *
     * @access public
     * @param PageModel $page
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (PageModel $page) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($page->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an PageModel entity into the database 
     *
     * @access public
     * @param PageModel $page
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (PageModel $page) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $page->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $page->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of PageModel entities. Use options array to limit results read.
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
            $page = new PageModel ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($page, $temp_array);

            if ($this->joins) {
                $user = new User ();
                $temp_array = $userDAO->stripPrefixArray ($row);
                //print_r ($temp_array);
                $userDAO->populateObject ($user, $temp_array);
                $page->user = $user;
                //print_r ($article);
            }

            $result_array[] = $page;
        }

        return $result_array;

    }

    /**
     * Return count number of PageModel entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an PageModel entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return PageModel
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

        $page = new PageModel ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($page, $temp_array);

        if ($this->joins) {
            $user = new User ();
            $temp_array = $userDAO->stripPrefixArray ($row);
            $userDAO->populateObject ($user, $temp_array);
            $page->user = $user;
            //print_r ($event);
        }


        return $page;
    }

    /**
     * Load instances of PageModel entities with the ids specified in the $ids array
     *
     * @access public
     * @param array $ids Array containing int ids of PageModel entities to load
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
     * Delete instances of an PageModel entities with the ids specified in the ids array
     *
     * @access public
     * @param array $ids Array containing int ids of PageModel entities to delete
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return bool Return status of PDOStatement execute method
     */
    public function deleteByIds ($ids, $options=null) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";

        $query = "DELETE FROM " . $this->tableName . " WHERE id IN (" . $str . ")";
        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        $params = $ids;
        return $stmt->execute ($params);
    }

    /**
     * Parse the options array for limit clauses and order by clauses. The valid keys and value types are specified below.
     * limit - Page object. Will take values from a Paginator Page object and
     * set LIMIT and OFFSET portions of database query accordingly
     *
     * joins - bool. If true, an INNER JOIN will be done to retrieve the
     * User associated with the page
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
            // Reference to album member
            if (strpos ($options["order"], ".") === false) {
                $this->query_order = "ORDER BY " . $this->tableName . "." . $options["order"];
            }
            if (strpos ($options["order"], "users.") === 0 && $this->joins) {
                $this->query_order = "ORDER BY " . $options["order"];
            }
            else {
                throw new InvalidArgumentException ("Invalid configuration for order option");
            }
        }
    }
}

?>
