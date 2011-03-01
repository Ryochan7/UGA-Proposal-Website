<?php
/**
 * File defines many global constants for use within the application. Template for necessary config.php file
 *
 * Copy config.default.php to config.php and overwrite any values that need to be changed. Likely candidates
 * would be the DB database constants. Add any more values that you want defined throughout the application
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();
/**
 * Constant defining the file location of the includes directory
 */
define ("INCLUDES_DIR", ROOT_DIRECTORY . DS . "includes");
/**
 * Constant defining the file location of the templates directory
 */
define ("TEMPLATE_DIR", ROOT_DIRECTORY . DS . "templates");
/**
 * Constant defining the domain address of the server hosting the application
 */
define ("DOMAIN_ADDR", "http://localhost");
/**
 * Constant defining the base URL used to access the main directory of the application
 */
define ("BASE_URL", DOMAIN_ADDR);
/**
 * Constant defining the file location of the media directory
 */
define ("MEDIA_PATH", ROOT_DIRECTORY . DS . "media");
/**
 * Constant defining the URL used to access the media directory
 */
define ("MEDIA_URL", BASE_URL . DS . "media");
/**
 * Constant defining the name of the web site
 */
define ("SITE_NAME", "Test Site");

/**
 * Constant defining the address of the database host
 */
define ("DB_HOST", "localhost");
/**
 * Constant defining the user of the database
 */
define ("DB_USER", "dbuser");
/**
 * Constant defining the password used to access the database
 */
define ("DB_PASS", "dbpass");
/**
 * Constant defining the name of the database schema to access for the database connection
 */
define ("DB_NAME", "dbname");
/**
 * REQUIRED FOR EMAIL FUNCTIONALITY TO WORK. Constant defining the email address of the site admin
 */
define ("EMAIL_ADDRESS", "");
/**
 * REQUIRED FOR EMAIL FUNCTIONALITY TO WORK. Constant defining the address of the email server
 */
define ("SMTP_HOST", "");
/**
 * REQUIRED FOR EMAIL FUNCTIONALITY TO WORK. Constant defining the user name used to access smtp server
 */
define ("SMTP_USERNAME", "");
/**
 * REQUIRED FOR EMAIL FUNCTIONALITY TO WORK. Constant defining the password used to access smtp server
 */
define ("SMTP_PASSWORD", "");

?>
