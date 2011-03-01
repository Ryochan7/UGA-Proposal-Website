<?php
/**
 * File defines the abstract class ModelBase which will be used by Model objects
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Model helper class
 *
 * Class contains implementations of the magic __get and __set methods.
 * Magic methods are called when accessing protected properties of a class
 * to provide a JSF-like interface for using models
 * @abstract
 * @package UGA
 */
abstract class ModelBase {
    /**
     * Id of object. Will typically be -1 when unpopulated
     * @access protected
     * @var int
     */
    protected $id = -1;

    /**
     * Magic method for calling getter methods for protected properties
     * @access public
     */
    public function __get ($key) {
        $tmp = ucfirst ($key);
        // Check for getter function
        if (is_callable (array ($this, "get" . $tmp))) {
            //echo "This is defined: get" . $tmp;
            $method_str = "get" . $tmp;
            return $this->$method_str ();
        }
        // Getter function not defined. Throw exception
        else {
            throw new InvalidArgumentException ("Getter method get" . $tmp . " is not defined");
        }
    }

    /**
     * Magic method for calling setter methods for protected properties
     * @access public
     */
    public function __set ($key, $value) {
        $tmp = ucfirst ($key);
        if (is_callable (array ($this, "set" . $tmp))) {
            //echo "This is defined: set" . $tmp;
            $method_str = "set" . $tmp;
            return $this->$method_str ($value);
        }
        // Setter function not defined. Do nothing.
        // Behavior due to PDO fetchObject function regarding joins
        // and extra columns from other tables
        else {
            //echo "Called undefined method";
        }
    }

    /**
     * Set id of object
     * @access public
     */
    public function setId ($id) {
        $this->id = $id;
    }

    /**
     * Get id of object
     * @access public
     * @return int
     */
    public function getId () {
        return $this->id;
    }
}

?>
