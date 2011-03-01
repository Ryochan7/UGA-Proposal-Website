<?php
/**
 * File defines the Album model class and AlbumDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");

/**
 * Album model class for representing an album entity
 *
 * Class contains the members that represent the values of an album
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class Album extends ModelBase {
    /**
     * Title of an album
     * @access protected
     * @var string
     */
    protected $title;

    /**
     * Returns the url of the page that can be used
     * to display the object
     *
     * @access public
     * @return string
     */
    public function getAbsoluteURL () {
        $url = "view_album.php?id={$this->id}";
        //$url = "/video/{$this->id}/";
        return $url;
    }

    /**
     * Set the title of the album
     *
     * @access public
     * @param string $title
     */
    public function setTitle ($title) {
        if (!is_string ($title)) {
            throw new InvalidArgumentException ();
        }
        $this->title = $title;
    }

    /**
     * Returns the title associated with the Album object
     *
     * @access public
     * @return string
     */
    public function getTitle () {
        return $this->title;
    }
}

/**
 * Album data access singleton class
 *
 * Data access class that will be used to read and write Album entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class AlbumDAO extends DAOBase {
    /**
     * Instance of AlbumDAO class
     * @access protected
     * @static
     * @var AlbumDAO
     */
    protected static $instance;
    /**
     * Name of database table holding Album data
     * @access protected
     * @var string
     */
    protected $tableName = "albums";
    /**
     * Array of strings containing column names for an Album row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "title");

    /**
     * Retrieve instance of an AlbumDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return AlbumDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an Album entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Album
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
     * Save an instance of an Album entity to the database
     *
     * @access public
     * @param Album $album
     * @return bool Return status of PDOStatement execute method
     */
    public function save (Album $album) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $album->$value;}
        $params[] = $album->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an Album entity from the database
     *
     * @access public
     * @param Album $album
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (Album $album) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($album->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an Album entity into the database 
     *
     * @access public
     * @param Album $album
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (Album $album) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $album->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $album->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of Album entities. Use options array to limit results read.
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
        while ($result = $stmt->fetchObject ("Album")) {
            $result_array[] = $result;

        }

        return $result_array;

    }

    /**
     * Return count number of Album entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an Album entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Album
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

        $result = $stmt->fetchObject ("Album");
        if (!$result) {
            return null;
        }

        return $result;
    }

    /**
     * Load all instances of Album entities from the database except for the album passed.
     * Exclusion is based on album id
     *
     * @access public
     * @param Album $album
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allExclude (Album $album, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $this->query_where = "WHERE " . $this->getTableName () . ".id != ?";
        $this->query_params = array ($album->getId ());
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Load instances of Album entities with the ids specified in the array param
     *
     * @access public
     * @param array $ids Array containing int ids of Album entities to load
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
     * Delete instances of an Album entities with the ids specified in the ids array
     *
     * @access public
     * @param array $ids Array containing int ids of Album entities to delete
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
     * limit - Page object. Will take values from a Paginator Page object
     * and set LIMIT and OFFSET portions of database query accordingly
     * 
     * order - string. Concatenate string with ORDER BY operator
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
            // Reference to album member
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
