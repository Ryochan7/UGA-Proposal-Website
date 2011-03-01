<?php
/**
 * File defines the ArticleTag model class and ArticleTagDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");
require_once ("Article.php");
require_once ("TaggedArticle.php");

/**
 * ArticleTag model class for representing an article entity
 *
 * Class contains the members that represent the values of an ArticleTag
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class ArticleTag extends ModelBase {
    /**
     * Name of the tag
     * @access protected
     * @var string
     */
    protected $name;

    /**
     * Returns the url of the page that can be used
     * to display the object
     *
     * @access public
     * @return string
     */
    public function getAbsoluteURL () {
        $url = "tagged_articles.php?id={$this->id}";
        return $url;
    }

    /**
     * Set the name of the tag
     *
     * @access public
     * @param string $name
     */
    public function setName ($name) {
        $this->name = $name;
    }

    /**
     * Returns the name of the entity
     *
     * @access public
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * Takes a space separated tag string and returns a sorted array of strings for the tags
     *
     * @access public
     * @static
     * @param string $tag_string
     * @return array
     */
    public static function tagsFromString ($tag_string) {
        if (!is_string ($tag_string)) {
            throw new InvalidArgumentException ("Tags must be specified in a space-separated string");
        }

        $tag_array = array ();
        if (!empty ($tag_string)) {
            $tmp_array = explode (" ", $tag_string);
            foreach ($tmp_array as $item) {
                $tag_array[] = strtolower ($item);
            }
            sort ($tag_array);
        }
        return $tag_array;
    }
}

/**
 * ArticleTag data access singleton class
 *
 * Data access class that will be used to read and write ArticleTag entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class ArticleTagDAO extends DAOBase {
    /**
     * Instance of ArticleTagDAO class
     * @access protected
     * @static
     * @var ArticleTagDAO
     */
    protected static $instance;
    /**
     * Name of database table holding ArticleTag data
     * @access protected
     * @var string
     */
    protected $tableName = "articleTag";
    /**
     * Array of strings containing column names for an ArticleTag row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "name");

    /**
     * Retrieve instance of an ArticleTagDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return ArticleTagDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an ArticleTag entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return ArticleTag
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
     * Load an instance of an ArticleTag entity from the database that has the specified name
     *
     * @access public
     * @param string $name
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return ArticleTag
     */
    public function loadByName ($name, $options=null) {
        if (!is_string ($name)) {
            throw new InvalidArgumentException ("Must pass the name as first parameter");
        }

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".name = ?";
        $this->query_params = array ($name);
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Save an instance of an ArticleTag entity to the database
     *
     * @access public
     * @param ArticleTag $articleTag
     * @return bool Return status of PDOStatement execute method
     */
    public function save (ArticleTag $articleTag) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $articleTag->$value;}
        $params[] = $articleTag->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an ArticleTag entity from the database. Will find and delete any associated TaggedArticle entities
     *
     * @access public
     * @param ArticleTag $articleTag
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (ArticleTag $articleTag) {
        // Sync tags
        $articleDAO = ArticleDAO::getInstance ();
        $article_array = $articleDAO->allWithTag ($articleTag);
        foreach ($article_array as $article) {
            $current_tag_array = $this->allArticleTags ($article);
            $found = -1;
            for ($i = 0; $i < count ($current_tag_array) && $found < 0; $i++) {
                if ($current_tag_array[$i]->getName () == $articleTag->getName ()) {
                    $found = $i;
                }
            }
            if ($found >= 0) {
                unset ($current_tag_array[$found]);
                $tmp_tag_array = array ();
                foreach ($current_tag_array as $tag) {
                    $tmp_tag_array[] = strtolower ($tag->getName ());
                }
                $current_tag_string = implode (" ", $tmp_tag_array);
                if ($articleDAO->save ($article)) {
                    $this->updateTags ($article);
                }
            }
        }

        $taggedDAO = TaggedArticleDAO::getInstance ();
        // Use LEFT JOIN to remove any associated tagged entries for old tag
        $query = "DELETE FROM {$this->tableName}, {$taggedDAO->getTableName ()} USING {$this->tableName} LEFT JOIN {$taggedDAO->getTableName ()} ON {$this->tableName}.id = {$taggedDAO->getTableName ()}.tagId WHERE {$this->tableName}.id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($articleTag->getId ());
        $status = $stmt->execute ($params);

        return $status;
    }

    /**
     * Insert an instance of an ArticleTag entity into the database 
     *
     * @access public
     * @param ArticleTag $articleTag
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (ArticleTag $articleTag) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $articleTag->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $articleTag->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of ArticleTag entities. Use options array to limit results read.
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
        while ($result = $stmt->fetchObject ("ArticleTag")) {
            $result_array[] = $result;
        }

        return $result_array;

    }

    /**
     * Return count number of ArticleTag entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an ArticleTag entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return ArticleTag
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
            //print_r ($this->query_params);
            $stmt->execute ($this->query_params);
        }
        else {
            $stmt->execute ();
        }

        $result = $stmt->fetchObject ("ArticleTag");
        if (!$result) {
            return null;
        }

        return $result;
    }

    /**
     * Determine new tags for an article and now unused tags for an Article. Insert new ArticleTag and TaggedArticle entities and delete any unused tags by deleting the associated TaggedArticle entities
     *
     * @access public
     * @param Article $article
     */
    public function updateTags (Article $article) {
        $articleDAO = ArticleDAO::getInstance ();
        $taggedDAO = TaggedArticleDAO::getInstance ();

        // Obtain current tags for an article
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_joins = " INNER JOIN {$taggedDAO->getTableName ()} ON {$this->tableName}.id = {$taggedDAO->getTableName ()}.tagId INNER JOIN {$articleDAO->getTableName ()} ON {$taggedDAO->getTableName ()}.articleId = {$articleDAO->getTableName ()}.id ";
        $this->query_where = "WHERE {$articleDAO->getTableName ()}.id = ?";
        $this->query_params = array ($article->getId ());
        $current_tags_array = $this->all ();
        $this->query_reset_lock = false;

        // Remove unused tags
        $updated_tags = ArticleTag::tagsFromString ($article->getTags ());
        $tags_to_remove = array ();
        foreach ($current_tags_array as $tag) {
            if (!in_array ($tag->getName (), $updated_tags)) {
                $tags_to_remove[] = $tag->getId ();
            }
        }

        //print_r ($tags_to_remove);
        if (!empty ($tags_to_remove)) {
            $status = $taggedDAO->deleteByTagged ($tags_to_remove, $article->id);
        }

        //print_r ($updated_tags);
        // Create new tags
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $current_tags_string_array = array ();
        foreach ($current_tags_array as $tag) {
            $current_tags_string_array[] = $tag->name;
        }

        //print_r ($current_tags_string_array);
        foreach ($updated_tags as $tag) {
            if (!in_array ($tag, $current_tags_string_array)) {
                //$newtag = new ArticleTag ();
                //$newtag->setName ($tag);
                $newtag = $this->loadByName ($tag);
                if (!$newtag) {
                    $newtag = new ArticleTag ();
                    $newtag->setName ($tag);
                    $this->insert ($newtag);
                }
                // Insert new tag. Will fail if tag name already exists.
                //if ($this->insert ($newtag)) {
                    //print_r ($newtag);
                //}
                // Tag already exists. Load it
                //else {
                //    $newtag = $this->loadByName ($newtag->name);
                    //print_r ($newtag);
               // }
                $tagged = new TaggedArticle ();
                $tagged->setArticleId ($article->getId ());
                $tagged->setTagId ($newtag->getId ());
                $status = $taggedDAO->insert ($tagged);
            }
        }

        $this->query_reset_lock = false;
        return null;
    }

    /**
     * Load all instances of ArticleTag entities associated with an Article. Use options array to limit results read.
     *
     * @access public
     * @param Article $article
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allArticleTags (Article $article, $options=null) {
        $articleDAO = ArticleDAO::getInstance ();
        $taggedDAO = TaggedArticleDAO::getInstance ();

        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_joins = " INNER JOIN {$taggedDAO->getTableName ()} ON {$taggedDAO->getTableName ()}.tagId = {$this->getTableName ()}.id INNER JOIN {$articleDAO->getTableName ()} ON {$articleDAO->getTableName ()}.id = {$taggedDAO->getTableName ()}.articleId ";

        $this->query_where = "WHERE {$articleDAO->getTableName ()}.id = ?";
        $this->query_params = array ($article->getId ());
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
     * Will add table name to field.
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
