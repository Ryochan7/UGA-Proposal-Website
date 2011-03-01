<?php
/**
 * File defines the Photo model class and PhotoDAO data access class
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once ("User.php");
require_once ("Album.php");

/**
 * Photo model class for representing an Photo entity
 *
 * Class contains the members that represent the values of an Photo
 * either read from the database or to be written to the database
 * @package UGA
 * @subpackage Model
 */
class Photo extends ModelBase {
    /**
     * Default upload directory
     * @access public
     * @var string
     */
    const UPLOAD_DIR = "media/uploads/images/";
    /**
     * Default thumbnail directory
     * @access public
     * @var string
     */
    const THUMBNAIL_DIR = "media/uploads/thumbnails/";
    /**
     * Maximum width of thumbnail image
     * @access public
     * @var int
     */
    const MAX_WIDTH = 640;
    /**
     * Currently unused
     * @access public
     * @var int
     */
    const MAX_HEIGHT = 640;
    /**
     * Currently unused
     * @access public
     * @var int
     */
    const SMALL_THUMB_HEIGHT = 100;
    /**
     * Album id associated with photo
     * @access protected
     * @var int
     */
    protected $albumId;
    /**
     * Title of photo
     * @access protected
     * @var string
     */
    protected $title;
    /**
     * Description of photo
     * @access protected
     * @var string
     */
    protected $description;
    /**
     * Relative file location (to UPLOAD_DIR) of full-size photo
     * @access protected
     * @var string
     */
    protected $fileLoc;
    /**
     * Relative file location (to THUMBNAIL_DIR) of photo thumbnail
     * @access protected
     * @var string
     */
    protected $thumbLoc = "";
    /**
     * Currently unused
     * @access protected
     * @var string
     */
    protected $smallThumbLoc = "";
    /**
     * Album object associated with photo
     * @access protected
     * @var Album
     */
    protected $album;

    /**
     * Returns the url of the page that can be used
     * to display the object
     *
     * @access public
     * @return string
     */
    public function getAbsoluteURL () {
        $url = "view_photo.php?id={$this->id}";
        return $url;
    }

    /**
     * Return the photo URL of an image. Used with img XHTML tag
     *
     * @access public
     * @return string
     */
    public function getMediaUrl () {
        $tmp_array = array (BASE_URL, $this->getFileLoc ());
        $url = implode ("/", $tmp_array);
        return $url;
    }

    /**
     * Return the thumbnail URL of an image. Used with img XHTML tag
     *
     * @access public
     * @return string
     */
    public function getMediaThumbUrl () {
        $tmp_array = array (BASE_URL, $this->getThumbLoc ());
        $url = implode ("/", $tmp_array);
        return $url;
    }

    /**
     * Set the album id of the photo
     *
     * @access public
     * @param int $albumId
     */
    public function setAlbumId ($albumId) {
        $this->albumId = $albumId;
    }

    /**
     * Return the album id of a photo
     *
     * @access public
     * @return int
     */
    public function getAlbumId () {
        return $this->albumId;
    }

    /**
     * Set the title of the photo
     *
     * @access public
     * @param string $title
     */
    public function setTitle ($title) {
        $this->title = $title;
    }

    /**
     * Return the title of the photo
     *
     * @access public
     * @return string
     */
    public function getTitle () {
        return $this->title;
    }

    /**
     * Set the description of the photo
     *
     * @access public
     * @param string $description
     */
    public function setDescription ($description) {
        $this->description = $description;
    }

    /**
     * Return the description of the photo
     *
     * @access public
     * @return string
     */
    public function getDescription () {
        return $this->description;
    }

    /**
     * Set the relative file location (to UPLOAD_DIR) of the photo
     *
     * @access public
     * @param string $fileLoc
     */
    public function setFileLoc ($fileLoc) {
        $this->fileLoc = $fileLoc;
    }

    /**
     * Return the relative file location (to UPLOAD_DIR) of the photo
     *
     * @access public
     * @return string
     */
    public function getFileLoc () {
        return $this->fileLoc;
    }

    /**
     * Set the relative file location (to THUMBNAIL_DIR) of the photo
     *
     * @access public
     * @param string $thumbLoc
     */
    public function setThumbLoc ($thumbLoc) {
        $this->thumbLoc = $thumbLoc;
    }

    /**
     * Return the relative file location (to THUMBNAIL_DIR) of the photo
     *
     * @access public
     * @return string
     */
    public function getThumbLoc () {
        return $this->thumbLoc;
    }

    /**
     * Don't use since $smallThumbLoc is not being used
     *
     * @access public
     * @param string $smallThumbLoc
     */
    public function setSmallThumbLoc ($smallThumbLoc) {
        $this->smallThumbLoc = $smallThumbLoc;
    }


    /**
     * Dont' use since $smallThumbLoc is not being used
     *
     * @access public
     * @return string
     */
    public function getSmallThumbLoc () {
        return $this->smallThumbLoc;
    }


    /**
     * Set the Album object associated with the photo
     *
     * @access public
     * @param Album $album
     */
    public function setAlbum (Album $album) {
        $this->album = $album;
    }


    /**
     * Return the Album object associated with the photo
     *
     * @access public
     * @return Photo
     */
    public function getAlbum () {
        return $this->album;
    }
}

/**
 * Photo data access singleton class
 *
 * Data access class that will be used to read and write Photo entities from or to the database
 * @static
 * @package UGA
 * @subpackage DAO
 */
class PhotoDAO extends DAOBase {
    /**
     * Instance of PhotoDAO class
     * @access protected
     * @static
     * @var PhotoDAO
     */
    protected static $instance;
    /**
     * Name of database table holding Photo data
     * @access protected
     * @var string
     */
    protected $tableName = "photos";
    /**
     * Array of strings containing column names for an Photo row
     * @access protected
     * @var array
     */
    protected $columns = array ("id", "albumId", "title", "description", "fileLoc", "thumbLoc");

    /**
     * Retrieve instance of an PhotoDAO or create one if it does
     * not exist.
     *
     * @access public
     * @static
     * @return PhotoDAO
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Load an instance of an Photo entity from the database that has the id specified
     *
     * @access public
     * @param int $id
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Photo
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
     * Save an instance of an Photo entity to the database
     *
     * @access public
     * @param Photo $photo
     * @return bool Return status of PDOStatement execute method
     */
    public function save (Photo $photo) {
        $query = "UPDATE " . $this->tableName . " " . $this->buildUpdateString () . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $photo->$value;}
        $params[] = $photo->id;
        return $stmt->execute ($params);
    }

    /**
     * Delete an instance of an Photo entity from the database
     *
     * @access public
     * @param Photo $photo
     * @return bool Return status of PDOStatement execute method
     */
    public function delete (Photo $photo) {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = ?";
        //echo $query;

        $stmt = self::$dbh->prepare ($query);
        $params = array ($photo->id);
        return $stmt->execute ($params);
    }

    /**
     * Insert an instance of an Photo entity into the database 
     *
     * @access public
     * @param Photo $photo
     * @return bool Return status of PDOStatement execute method
     */
    public function insert (Photo $photo) {
        $query = "INSERT INTO " . $this->tableName . " " . $this->buildInsertString ();

        $stmt = self::$dbh->prepare ($query);
        $temp = array_merge (array (), $this->columns);
        unset ($temp[0]);
        $params = array ();
        foreach ($temp as $value) {$params[] = $photo->$value;}

        $status = $stmt->execute ($params);
        if ($status) {
            $photo->id = intval (self::$dbh->lastInsertId ());
        }
        return $status;
    }

    /**
     * Load all instances of Photo entities. Use options array to limit results read.
     *
     * @access public
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function all ($options=null) {
        $albumDAO = AlbumDAO::getInstance ();
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
            $photo = new Photo ();
            $row = array_combine ($this->select_columns, $result);
            $temp_array = $this->stripPrefixArray ($row);
            $this->populateObject ($photo, $temp_array);

            if ($this->joins) {
                $album = new Album ();
                $temp_array = $albumDAO->stripPrefixArray ($row);
                //print_r ($temp_array);
                $albumDAO->populateObject ($album, $temp_array);
                $photo->album = $album;
                //print_r ($article);
            }

            $result_array[] = $photo;
        }

        return $result_array;

    }

    /**
     * Return count number of Photo entities in the database
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
     * Helper method used with various public load methods. Used to load an instance of an Photo entity using the built strings of a query as specified in the caller method
     *
     * @access private
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Photo
     */
    private function loadGeneral ($options=null) {
        $albumDAO = AlbumDAO::getInstance ();
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

        $photo = new Photo ();
        $row = array_combine ($this->select_columns, $result);
        $temp_array = $this->stripPrefixArray ($row);
        $this->populateObject ($photo, $temp_array);

        if ($this->joins) {
            $album = new Album ();
            $temp_array = $albumDAO->stripPrefixArray ($row);
            $userDAO->populateObject ($album, $temp_array);
            $photo->album = $album;
            //print_r ($event);
        }

        return $photo;
    }

    /**
     * Load instances of Photo entities with the ids specified in the $ids array
     *
     * @access public
     * @param array $ids Array containing int ids of Photo entities to load
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
     * Delete instances of an Photo entities with the ids specified in the ids array
     *
     * @access public
     * @param array $ids Array containing int ids of Photo entities to delete
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
     * Load instances of Photo entities that have an albumId associated with an Album
     *
     * @access public
     * @param Album $album
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return array
     */
    public function allByAlbum (Album $album, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;

        $this->query_where = "WHERE " . $this->getTableName () . ".albumId = ?";
        $this->query_params = array ($album->getId ());
        $result_array = $this->all ($options);
        $this->query_reset_lock = false;

        return $result_array;
    }

    /**
     * Return count number of Photo entities in the database that have an albumId associated with an Album
     *
     * @access public
     * @param Album $album
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countByAlbum (Album $album, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE " . $this->getTableName () . ".albumId = ?";
        $this->query_params = array ($album->getId ());
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Return the count position of a Photo relative to the position in an Album
     *
     * @access public
     * @param Photo $photo Current photo
     * @param Album $album
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return int
     */
    public function countPosition (Photo $photo, Album $album, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        $this->query_where = "WHERE {$this->tableName}.id <= ? AND {$this->tableName}.albumId = ?";
        $this->query_params = array ($photo->getId (), $album->getId ());
        $this->query_select = "COUNT({$this->columns[0]}) AS count";
        $result = $this->count ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an Photo entity from the database that corresponds to the next Photo in Album
     *
     * @access public
     * @param Photo $photo
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Photo
     */
    public function loadNext (Photo $photo, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        if (!is_array ($options) || !array_key_exists ("order", $options)) {
            $this->query_order = "ORDER BY {$this->tableName}.id ASC";
        }
        $this->query_where = "WHERE " . $this->getTableName () . ".id > ? AND {$this->tableName}.albumid = ?";
        $this->query_params = array ($photo->getId (), $photo->getAlbumId ());
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Load an instance of an Photo entity from the database that corresponds to the previous Photo in Album
     *
     * @access public
     * @param Photo $photo
     * @param array $options (Optional) Read documentation on parseOptions for details
     * @return Photo
     */
    public function loadPrevious (Photo $photo, $options=null) {
        $this->resetQueryStrings ();
        $this->query_reset_lock = true;
        if (!is_array ($options) || !array_key_exists ("order", $options)) {
            $this->query_order = "ORDER BY {$this->tableName}.id DESC";
        }
        $this->query_where = "WHERE " . $this->getTableName () . ".id < ? AND {$this->tableName}.albumid = ?";
        $this->query_params = array ($photo->getId (), $photo->getAlbumId ());
        $result = $this->loadGeneral ($options);
        $this->query_reset_lock = false;

        return $result;
    }

    /**
     * Parse the options array for limit clauses and order by clauses. The valid keys and value types are specified below.
     * limit - Page object. Will take values from a Paginator Page object and
     * set LIMIT and OFFSET portions of database query accordingly
     *
     * joins - bool. If true, an INNER JOIN will be done to retrieve the
     * Album associated with the page
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
            $albumDAO = AlbumDAO::getInstance ();
            $this->query_select .= ", " . $albumDAO->buildColumnString ();
            $this->query_joins .= " INNER JOIN (" . $albumDAO->getTableName () . ") ON (" . $albumDAO->getTableName () . ".id = " . $this->getTableName () . ".albumId) ";
            $this->select_columns = array_merge ($this->select_columns, $albumDAO->buildColumnArray ());
            $this->joins = true;
        }

        if (array_key_exists ("order", $options) && is_string ($options["order"])) {
            // Reference to album member
            if (strpos ($options["order"], ".") === false) {
                $this->query_order = "ORDER BY " . $this->tableName . "." . $options["order"];
            }
            else if (strpos ($options["order"], "albums.") === 0 && $this->joins) {
                $this->query_order = "ORDER BY " . $options["order"];
            }
            else {
                throw new InvalidArgumentException ("Invalid configuration for order option");
            }
        }
    }
}

?>
