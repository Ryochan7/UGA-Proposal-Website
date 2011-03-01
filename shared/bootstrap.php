<?php
/**
 * File used to load essential classes, functions and define constants for the application
 * @package UGA
 */

// Use display_errors option when debugging code
//ini_set ("display_errors", "On");
$shareddir = dirname (__FILE__);
/**
 * Constant to define if current scope is within application. Used to prevent direct file access.
 */
define ("IN_APP", true);
/**
 * Constant that is a shortcut to DIRECTORY_SEPARATOR php constant
 */
define ("DS", DIRECTORY_SEPARATOR);
/**
 * Constant that defines the root directory of the application
 */
define ("ROOT_DIRECTORY", dirname (dirname (__FILE__)));

require_once (dirname ($shareddir) . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "config.php");
require_once ($shareddir . DS . "extra.php");
require_once (joinPath (INCLUDES_DIR, "Controller.php"));
require_once (joinPath (INCLUDES_DIR, "DAOBase.php"));
require_once (joinPath (INCLUDES_DIR, "ModelBase.php"));
require_once (joinPath (INCLUDES_DIR, "Paginator.php"));
require_once (joinPath (INCLUDES_DIR, "Template.php"));
require_once (joinPath (INCLUDES_DIR, "Session.php"));

/** 
 * Constant to determine if a request is known to be from an AJAX request
 *
 * Using technique from http://www.electrictoolbox.com/how-to-tell-ajax-request-php/
 * to determine if a request is an ajax request. First inspired by Django's
 * HttpRequest.is_ajax() method
 */
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

?>
