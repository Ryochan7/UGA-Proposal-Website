<?php
/**
 * Miscellaneous functions for use in application
 * @package UGA
 */

if (!defined ("IN_APP")) exit ();

/**
 * Joins elements of a file path 
 *
 * Joins many elements of a file path.
 * Example: pathJoin ("includes", "models", "Class.php")
 * => includes/models/Class.php
 * 
 * @access public
 * @param string $params,... Strings representing portions of a file path
 * @return string
 */
function joinPath () 
{
    $args = func_get_args();
    $paths = array();
    foreach( $args as $arg ) 
    {
        $paths = array_merge( $paths, (array)$arg );
    }
    foreach( $paths as &$path ) 
    {
        $path = trim( $path, DS );
    }
    if( substr( $args[0], 0, 1 ) == DS )
    {
        $paths[0] = DS . $paths[0];
    }
    return join (DS, $paths);
}

?>
