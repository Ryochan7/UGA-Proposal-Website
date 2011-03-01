<?php
/**
 * File defines the Article model class and ArticleDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");
require_once ("ArticleTag.php");

/**
 * Article model class for representing an article entity
 *
 * Class contains the members that represent the values of an article
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class Article extends ModelBase {
    /**
     * User id of the author of an article
     * @access protected
     * @var int
     */
    protected $userId;
    /**
     * Title of an article
     * @access protected
     * @var string
     */
    protected $title;
    /**
     * XHTML content string of the article
     * @access protected
     * @var string
     */
    protected $content;
    /**
     * Posted date of an article represented as a UNIX timestamp
     * @access protected
     * @var int
     */
    protected $postDate = 0;
    /**
     * Update data of an article represented as a UNIX timestamp
     * @access protected
     * @var int
     */
    protected $updateDate = 0;
    /**
     * Bool indicating if an article is published
     * @access protected
     * @var bool
     */
    protected $published = false;
    /**
     * Space separated string representing the tags of an article
     * <code>
     * ssf4 xbox tekken
     * The previous string represents three tags that will be represented by ArticleTag entities
     * </code>
     * @access protected
     * @var string
     */
    protected $tags;
    /**
     * Instance of User representing the author of an article
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
        $url = "view_article.php?id={$this->id}";
        return $url;
    }

    /**
     * Set the id of the User that corresponds to the author of the article
     *
     * @access public
     * @param int $userId
     */
    public function setUserId ($userId) {
        $this->userId = $userId;
    }

    /**
     * Returns the User id of the author of the article
     *
     * @access public
     * @return int
     */
    public function getUserId () {
        return $this->userId;
    }

    /**
     * Set the title of the article
     *
     * @access public
     * @param string $title
     */
    public function setTitle ($title) {
        $this->title = $title;
    }

    /**
     * Returns the title of the article
     *
     * @access public
     * @return string
     */
    public function getTitle () {
        return $this->title;
    }

    /**
     * Set the XHTML content string of the article
     *
     * @access public
     * @param string $content
     */
    public function setContent ($content) {
        $this->content = $content;
    }

    /**
     * Returns the XHTML content string of the article
     *
     * @access public
     * @return string
     */
    public function getContent () {
        return $this->content;
    }

    /**
     * Set the UNIX timestamp of the post date of an article
     *
     * @access public
     * @param int $postDate
     */
    public function setPostDate ($postDate) {
        $this->postDate = $postDate;
    }

    /**
     * Return the UNIX timestamp of the post date of an article
     *
     * @access public
     * @return int
     */
    public function getPostDate () {
        return $this->postDate;
    }

    /**
     * Set the UNIX timestamp of the update date of an article
     *
     * @access public
     * @param int $updateDate
     */
    public function setUpdateDate ($updateDate) {
        $this->updateDate = $updateDate;
    }

    /**
     * Return the UNIX timestamp of the update date of an article
     *
     * @access public
     * @return int
     */
    public function getUpdateDate () {
        return $this->updateDate;
    }

    /**
     * Set the published status of an article
     *
     * @access public
     * @param bool $published
     */
    public function setPublished ($published) {
        $this->published = $published;
    }

    /**
     * Return the published status of an article
     *
     * @access public
     * @return string
     */
    public function getPublished () {
        return $this->published;
    }

    /**
     * Set the space separated tag string of the article
     *
     * @access public
     * @param string $tags
     */
    public function setTags ($tags) {
        $this->tags = $tags;
    }

    /**
     * Returns the tag string of the article
     *
     * @access public
     * @return string
     */
    public function getTags () {
        return $this->tags;
    }

    /**
     * Set the User object representing the author of the article
     *
     * @access public
     * @param User $user
     */
    public function setUser (User $user) {
        $this->user = $user;
    }

    /**
     * Return the User object representing the author of the article
     *
     * @access public
     * @return User
     */
    public function getUser () {
        return $this->user;
    }

}

/**
 * Article data access singleton class
 *
 * Data access class that will be used to read and write Article entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class ArticleDAO extends DAOBase {
    /**
     * Instance of ArticleDAO class
     * @access protected
     * @static
     * @var ArticleDAO
     */
    protected static $instance;
    /**
     * Name of database table holding Article data
     * @access protected
     * @var string
     */
    protected $tableName = "articles";
    /**
     * Array of strings containing column names for an Article row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "userId",  "title", "content", "postDate", "updateDate", "published", "tags");

    /**
     * Retrieve instance of an ArticleDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return ArticleDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an Article entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Article
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
     * Save an instance of an Article entity to the database
     *
     * @access public
     * @param Article $article
     * @return bool Return status of PDOStatement execute method
     */
    public function save (Article $article) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $article->$value;}
        $params[] = $article->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an Article entity from the database
     *
     * @access public
     * @param Article $article
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (Article $article) {
        // Delete associated tag entries first while the article still exists in the database.
        // MyISAM does not support transactions. Considering converting tables to InnoDB?
        $tagDAO = ArticleTagDAO::getInstance ();
        $oldtags = $article->getTags ();
        $article->setTags ("");
        $tagDAO->updateTags ($article);

        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($article->id);
        $status = $stmt->execute ($params);
        // If failed, revert tagged entries since MyISAM does not support transactions
        if (!$status) {
            $article->setTags ($oldtags);
            $tagDAO->updateTags ($article);
        }
        return $status;
    }

    /**
     * Insert an instance of an Article entity into the database 
     *
     * @access public
     * @param Article $article
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (Article $article) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $article->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $article->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of Article entities. Use options array to limit results read.
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
            $article = new Article ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($article, $temp_array);

            if ($this->joins) {
                $user = new User ();
                $temp_array = $userDAO->stripPrefixArray ($row);
                //print_r ($temp_array);
                $userDAO->populateObject ($user, $temp_array);
                $article->user = $user;
                //print_r ($article);
            }

            $result_array[] = $article;

        }

        return $result_array;

    }

    /**
     * Return count number of Article entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an Article entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Article
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

        $article = new Article ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($article, $temp_array);

        if ($this->joins) {
            $user = new User ();
            $temp_array = $userDAO->stripPrefixArray ($row);
            $userDAO->populateObject ($user, $temp_array);
            $article->user = $user;
            //print_r ($event);
        }
        return $article;

    }

    /**
     * Load instances of Article entities with the ids specified in the array param
     *
     * @access public
     * @param array $ids Array containing int ids of Article entities to load
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
     * Delete instances of an Article entities with the ids specified in the ids array
     *
     * @access public
     * @param array $ids Array containing int ids of Article entities to delete
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return bool Return status of PDOStatement execute method
     */
    public function deleteByIds ($ids, $options=null) {
        if (!is_array ($ids)) {
            throw new InvalidArgumentException ("Must pass array of ids as the first parameter");
        }

        // Delete associated tag entries first while the article still exists in the database.
        // MyISAM does not support transactions. Considering converting tables to InnoDB?
        $tagDAO = ArticleTagDAO::getInstance ();
        $oldtags = $article->getTags ();
        $article->setTags ("");
        $tagDAO->updateTags ($article);

        $str = "";
        for ($i = 0; $i < count ($ids) - 1; $i++) {
            $str .= "?,";
        }
        $str .= "?";

        $query = "DELETE FROM " . $this->tableName . " WHERE id IN (" . $str . ")";
        //echo $query;
        $stmt = self::$dbh->prepare ($query);
        $params = $ids;
        $status = $stmt->execute ($params);
        // If failed, revert tagged entries since MyISAM does not support transactions
        if (!$status) {
            $article->setTags ($oldtags);
            $tagDAO->updateTags ($article);
        }
        return $status;
    }

    /**
     * Return count number of Album entities in the database that have a published status indicated by the published param
     *
     * @access public
     * @param bool $published
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countPublished ($published, $options=null) {
        if (!is_bool ($published)) {
            throw new InvalidArgumentException ("Must pass the published status as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".published = ?";
        $this->query_params = array ($published);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load instances of Article entities that have a published status as indicated by the published param
     *
     * @access public
     * @param bool $published
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allPublished ($published, $options=null) {
        if (!is_bool ($published)) {
            throw new InvalidArgumentException ("Must pass the published status as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".published = ?";
        $this->query_params = array ($published);
        $result = $this->all ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Return count number of Album entities in the database that have a published status indicated by the published param and with the specified tag
     *
     * @access public
     * @param bool $published
     * @param ArticleTag $tag
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countPublishedWithTag ($published, ArticleTag $tag, $options=null) {
        if (!is_bool ($published)) {
            throw new InvalidArgumentException ("Must pass the published status as first parameter");
        }

        $tagDAO = ArticleTagDAO::getInstance ();
        $taggedDAO = TaggedArticleDAO::getInstance ();
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_joins = " INNER JOIN {$taggedDAO->getTableName ()} ON {$this->tableName}.id = {$taggedDAO->getTableName ()}.articleId INNER JOIN {$tagDAO->getTableName ()} ON {$taggedDAO->getTableName ()}.tagId = {$tagDAO->getTableName ()}.id ";
        $this->query_where = "WHERE " . $this->getTableName () . ".published = ? AND {$tagDAO->getTableName ()}.id = ?";
        $this->query_params = array ($published, $tag->id);
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load instances of Article entities that have a published status as indicated by the published param and the specified tag param
     *
     * @access public
     * @param bool $published
     * @param ArticleTag $tag
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allPublishedWithTag ($published, ArticleTag $tag, $options=null) {
        if (!is_bool ($published)) {
            throw new InvalidArgumentException ("Must pass the published status as first parameter");
        }

        $tagDAO = ArticleTagDAO::getInstance ();
        $taggedDAO = TaggedArticleDAO::getInstance ();

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_joins = " INNER JOIN {$taggedDAO->getTableName ()} ON {$this->tableName}.id = {$taggedDAO->getTableName ()}.articleId INNER JOIN {$tagDAO->getTableName ()} ON {$taggedDAO->getTableName ()}.tagId = {$tagDAO->getTableName ()}.id ";
        $this->query_where = "WHERE " . $this->getTableName () . ".published = ? AND {$tagDAO->getTableName ()}.id = ?";
        $this->query_params = array ($published, $tag->id);
        $result = $this->all ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load instances of Article entities that is tagged with the ArticleTag indicated by the tag param
     *
     * @access public
     * @param ArticleTag $tag
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allWithTag (ArticleTag $tag, $options=null) {
        $tagDAO = ArticleTagDAO::getInstance ();
        $taggedDAO = TaggedArticleDAO::getInstance ();

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_joins = " INNER JOIN {$taggedDAO->getTableName ()} ON {$this->tableName}.id = {$taggedDAO->getTableName ()}.articleId INNER JOIN {$tagDAO->getTableName ()} ON {$taggedDAO->getTableName ()}.tagId = {$tagDAO->getTableName ()}.id ";
        $this->query_where = "WHERE {$tagDAO->getTableName ()}.id = ?";
        $this->query_params = array ($tag->getId ());
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

        if (array_key_exists ("joins", $options) && $options["joins"] == true) {
            $userDAO = UserDAO::getInstance ();
            $this->query_select .= ", " . $userDAO->buildColumnString ();
            $this->query_joins .= "INNER JOIN (" . $userDAO->getTableName () . ") ON (" . $userDAO->getTableName () . ".id = " . $this->getTableName () . ".userId)";
            $this->select_columns = array_merge ($this->select_columns, $userDAO->buildColumnArray ());
            $this->joins = true;
        }

        if (array_key_exists ("order", $options) && is_string ($options["order"])) {
            // Reference to article member
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
//            else {
//                throw new InvalidArgumentException ("Invalid configuration for order option");
//            }
        }
    }
}

?>
