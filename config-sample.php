<?php

define( 'DB_NAME', 'your_database_name' );
define( 'DB_USER', 'your_database_user' );
define( 'DB_PASSWORD', 'your_database_password' );
define( 'DB_HOST', 'localhost' ); // MAMP might be something like ':/Applications/MAMP/tmp/mysql/mysql.sock'

include_once( 'lib/simple_html_dom.php' );
include_once( 'lib/rss_fetch.inc' );
include_once( 'lib/rss_parse.inc' );

// Connect to the database
$connection = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) or die( 'Error connecting to database' );
mysql_select_db( DB_NAME );

?>