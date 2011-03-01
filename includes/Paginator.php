<?php
/**
 * File defines classes used for pagination and setting limit clauses in queries
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Class useful when performing pagination
 *
 * Class will take a total count of items and how many items should
 * be represented per page. The class can be used to find how many
 * pages will be necessary to paginate over all objects 
 * @package UGA
 */
class Paginator {
    /**
     * Total number of items in a set
     * @access protected
     * @var int
     */
    protected $totalItems;
    /**
     * Defines the number of items to contain per page
     * @access protected
     * @var int
     */
    protected $itemsPerPage;
    /**
     * Total number of pages used to split items
     * @access protected
     * @var int
     */
    protected $numPages;

    /**
     * Constructor
     * 
     * Constructor. Takes total number of items in a set
     * and how many items should be used per page. Determines
     * the total number of pages
     *
     * @access public
     * @param int $totalItems Number of items in a set
     * @param int $itemsPerPage Split item count by number
     */
    public function __construct ($totalItems, $itemsPerPage) {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->numPages = ceil (($totalItems/$itemsPerPage));
    }

    /**
     * Generate a Page object for a given page number
     * @access public
     * @param int $pageNumber
     * @return Page
     */
    public function getPage ($pageNumber) {
        if ($pageNumber < 1) {
            throw new Exception ();
        }
        return new Page ($pageNumber, $this);
    }

    /**
     * Get count of items in a set
     *
     * @access public
     * @return int
     */
    public function getCount () {
        return $this->totalItems;
    }

    /**
     * Get an array defining the split in page numbers
     * @access public
     * @return array
     */
    public function getPageRange () {
        $range_array = array ();
        for ($i = 1; $i <= $this->numPages; $i++) {
            $range_array[$i] = $i;
        }
        return $range_array;
    }

    /**
     * Get number of pages
     * @access public
     * @return int
     */
    public function getNumPages () {
        return $this->numPages;
    }

    /**
     * Get number of items per page
     * @access public
     * @return int
     */
    public function getItemsPerPage () {
        return $this->itemsPerPage;
    }
}

/**
 * Class can be used to determine if adjacent pages exist
 * @package UGA
 */
class Page {
    /**
     * Number of page relative to the set
     * @access protected
     * @var int
     */
    protected $pageNumber;
    /**
     * Number of pages the set is split by
     * @access protected
     * @var int
     */
    protected $numPages;
    /**
     * Associated Paginator object
     * @access protected
     * @var Paginator
     */
    protected $paginator;

    /**
     * Constructor
     *
     * Constructor. Takes page number and Paginator object.
     * Define associations of one page to others in the set
     * @access public
     * @param int $pageNumber
     * @param Paginator $paginator
     */
    public function __construct ($pageNumber, Paginator $paginator) {
        $this->pageNumber = $pageNumber;
        $this->numPages = $paginator->getNumPages ();
        $this->paginator = $paginator;
    }

    /**
     * Determine if there is another page
     * @return int
     */
    public function hasNext () {
        $hasnext = false;
        if ($this->pageNumber < $this->numPages) {
            $hasnext = true;
        }
        else {
            $hasnext = false;
        }
        return $hasnext;
    }

    /**
     * Determine if there is a previous page
     * @return int
     */
    public function hasPrevious () {
        $has_previous = false;
        if ($this->pageNumber > 1) {
            $has_previous = true;
        }
        else {
            $has_previous = false;
        }
        return $has_previous;
    }

    /**
     * Get page number for next page. Return same number if next page does not exist
     * @return int
     */
    public function nextPageNumber () {
        $pageNum = 0;
        if ($this->hasNext ()) {
            $pageNum = $this->pageNumber + 1;
        }
        else {
            $pageNum = $this->pageNumber;
        }
        return $pageNum;
    }

    /**
     * Get page number for previous page. Return same number if a previous page does not exist
     * @return int
     */
    public function previousPageNumber () {
        $pageNum = 0;
        if ($this->hasPrevious ()) {
            $pageNum = $this->pageNumber - 1;
        }
        else {
            $pageNum = $this->pageNumber;
        }
        return $pageNum;
    }

    /**
     * Get page number
     * @return int
     */
    public function getPageNumber () {
        return $this->pageNumber;
    }

    /**
     * Get associated paginator object
     * @return Paginator
     */
    public function getPaginator () {
        return $this->paginator;
    }
}

?>
