<?php
/**
 * File defines class used to render a PHP/(X)HTML page with populated data
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Class for rendering a PHP/(X)HTML file with populated data
 * @package UGA
 */
class PageTemplate {
    /**
     * File name for PHP file with miscellaneous template functions
     * @static
     * @access protected
     * @var string
     */
    protected static $TPL_FUNCS_FILE = "template_functions.php";
    /**
     * File path to template directory
     * @access protected
     * @var string
     */
    protected $template_directory_name;
    /**
     * File name of template file
     * @access protected
     * @var string
     */
    protected $template_file;
    /**
     * Associated array with variables used to populate PHP/HTML page
     * @access protected
     * @var array
     */
    protected $data_array;

    /**
     * Constructor
     * 
     * Constructor. Takes a file name of a template file (PHP/HTML)
     * and a file path to a template directory. The file name of the file
     * will be joined with the file path of the template directory to
     * load the file. Uses SITE_NAME and MEDIA_URL constants if defined.
     *
     * @access public
     * @param string $tpl_file Optional - File name to template file
     * @param string $tpl_dir_name Optional - File path to template directory
     */
    public function __construct ($tpl_file = "index_tpl.php", $tpl_dir_name = "templates") {

        $this->template_directory_name = $tpl_dir_name;
        $this->template_file = $tpl_file;
        $this->data_array = array ();

        $SITE_NAME = defined ("SITE_NAME") ? SITE_NAME : "Test Site";
        $MEDIA_URL = defined ("MEDIA_URL") ? MEDIA_URL : "";
        $this->data_array["SITE_NAME"] = $SITE_NAME;
        $this->data_array["MEDIA_URL"] = $MEDIA_URL;
    }

    /**
     * Render a PHP/(X)HTML page with any values from the $data_array param
     *
     * $data_array must be in the form of an associative array with key => value pairs for variables.
     * The key will be the name of the variable used to access a value in the template file with the
     * value being the defined value set from value. The array will run through the extract
     * function to produce the requested variables.
     * @access public
     * @param array $data_array Associative array with key => value pairs to be extracted to the template
     */
    public function render ($data_array=null) {
        if (is_array ($data_array)) {
            $this->data_array = array_merge ($this->data_array, $data_array);
        }

        // Import template specific functions. Only in scope for duration of render
        require_once (joinPath (INCLUDES_DIR, self::$TPL_FUNCS_FILE));

        // Allow template object to be called through $template variable
        // along with the $this variable
        global $template;
        $template = $this;

        // Extra variables from data array for use in templates.
        // Avoids using array syntax to grab variables in template
	    // files
        extract ($this->data_array);

        // Import template file. Include is used in the case of fragments being
        // loaded multiple times
        include (joinPath (TEMPLATE_DIR, $this->template_file));
    }

    /**
     * Get a variable from the data array
     * @access public
     * @param string $key
     * @return mixed
     */
    public function get ($key) {
        return $this->data_array[$key];
    }

}

?>
