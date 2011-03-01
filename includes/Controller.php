<?php
/**
 * File defines the controller interface
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Controller interface. Defines a run method
 * @package UGA
 */
interface Controller {
    /**
     * Run method should contain main page logic. Each page will run the Controller run method
     * @access public
     */
    public function run ();
}

?>
