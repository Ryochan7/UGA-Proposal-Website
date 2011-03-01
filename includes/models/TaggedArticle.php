<?php
/**
 * File defines the TaggedArticle model class and TaggedArticleDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("Article.php");
require_once ("ArticleTag.php");

/**
 * TaggedArticle model class for representing an TaggedArticle entity
 *
 * Class contains the members that represent the values of an TaggedArticle
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class TaggedArticle extends ModelBase {
    /**
     * Associated Article id
     * @access protected
     * @var int
     */
    protected $articleId;
    /**
     * Associated ArticleTag id
     * @access protected
     * @var int
     */
    protected $tagId;

    /**
     * Set the article id
     *
     * @access public
     * @param int $articleId
     */
    public function setArticleId ($articleId) {
        $this->articleId = $articleId;
    }

    /**
     * Return the article id
     *
     * @access public
     * @return int
     */
    public function getArticleId () {
        return $this->articleId;
    }

    /**
     * Set the ArticleTag id
     *
     * @access public
     * @param int $tagId
     */
    public function setTagId ($tagId) {
        $this->tagId = $tagId;
    }

    /**
     * Return the ArticleTag id
     *
     * @access public
     * @return int
     */
    public function getTagId () {
        return $this->tagId;
    }

}

/**
 * TaggedArticle data access singleton class
 *
 * Data access class that will be used to read and write TaggedArticle entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class TaggedArticleDAO extends DAOBase {
    /**
     * Instance of TaggedArticleDAO class
     * @access protected
     * @static
     * @var TaggedArticleDAO
     */
    protected static $instance;
    /**
     * Name of database table holding TaggedArticle data
     * @access protected
     * @var string
     */
    protected $tableName = "taggedArticle";
    /**
     * Array of strings containing column names for an TaggedArticle row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "articleId", "tagId");

    /**
     * Retrieve instance of an TaggedArticleDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return TaggedArticleDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an TaggedArticle entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return TaggedArticle
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
     * Save an instance of an TaggedArticle entity to the database
     *
     * @access public
     * @param TaggedArticle $tagged
     * @return bool Return status of PDOStatement execute method
     */
    public function save (TaggedArticle $tagged) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $tagged->$value;}
        $params[] = $tagged->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an TaggedArticle entity from the database
     *
     * @access public
     * @param TaggedArticle $tagged
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (TaggedArticle $tagged) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($tagged->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an TaggedArticle entity into the database 
     *
     * @access public
     * @param TaggedArticle $tagged
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (TaggedArticle $tagged) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $tagged->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $tagged->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of TaggedArticle entities. Use options array to limit results read.
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
            //print_r ($this->query_params);
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result_array = array ();
        while ($result = $stmt->fetchObject ("TaggedArticle")) {
            $result_array[] = $result;
        }

        return $result_array;

    }

    /**
     * Helper method used with various public load methods. Used to load an instance of an TaggedArticle entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return TaggedArticle
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

        $result = $stmt->fetchObject ("TaggedArticle");
        if (!$result) {
            return null;
        }

        return $result;

    }

    /**
     * Return count number of TaggedArticle entities in the database
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
     * Delete instances of TaggedArticle entities from the database associated with an article id and with tag ids as specified in the $ids array param
     *
     * @access public
     * @param array $ids Array of int ids associated with the ids of TaggedArticle entities
     * @param int $article_id Id of an Article entity
     * @return bool Return status of PDOStatement execute method
     */
    public function deleteByTagged ($ids, $article_id, $options=null) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }
        if (!is_numeric ($article_id)) {
            throw new InvalidArgumentException ("Must pass article id as the second parameter");
        }


        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";

        $query = "DELETE FROM " . $this->tableName . " WHERE articleId = ? AND tagId IN (" . $str . ")";
        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        $params = array ($article_id);
        $params = array_merge ($params, $ids);
        return $stmt->execute ($params);
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
            // Reference to tagged member
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
