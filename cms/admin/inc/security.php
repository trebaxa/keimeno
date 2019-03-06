<?

if ( !defined('IN_SIDE') ) {

 $_SESSION = array();
 header( 'HTTP/1.1 404 Not Found' );
 header( 'Status: 404 Not Found' );
 header( 'Connection: close' );
 exit();

}

?>