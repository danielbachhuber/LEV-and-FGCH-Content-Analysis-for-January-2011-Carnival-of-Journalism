<?php

define('DB_NAME', 'locals_2010_analysis');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', ':/Applications/MAMP/tmp/mysql/mysql.sock');

// Connect to the database
$connection = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) or die( 'Error connecting to database' );
mysql_select_db( DB_NAME );

$query = "SELECT id, permalink, body_html FROM original_posts";
$results = mysql_query( $query );
echo "\nDownloaded permalinks from database.\n";

$new_data = array();

while ( $row = mysql_fetch_assoc( $results ) ) {
	
	// Only download the page if the HTML isnt' already there
	if ( !$row['body_html'] ) {
		$id = $row['id'];
		$permalink = $row['permalink'];		

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $permalink );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		$body_html = curl_exec ($ch);
		curl_close ($ch);
		echo "Downloaded $permalink\n";
		
		$query = sprintf( "UPDATE original_posts SET body_html='%s' WHERE id=$id;", mysql_real_escape_string( $body_html ) );
		$insert_results = mysql_query( $query );
		echo "Inserted results into database for post $id\n";
		
	}

}

echo "All done!\n";

?>