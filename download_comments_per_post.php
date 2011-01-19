<?php

include_once( 'config.php' );
echo "\nStarting the comment downloading process\n";

$query = "SELECT post_id, permalink, comment_count FROM original_posts";
$results = mysql_query( $query );

while ( $row = mysql_fetch_assoc( $results ) ) {

	extract( $row );
	$comments_feed_url = $permalink . 'feed/';
	
	$comments_feed = fetch_rss( $comments_feed_url );
	echo count( $comments_feed->items ) . " comments for $permalink\n";
	$query = sprintf( "UPDATE original_posts SET comment_count='%s' WHERE post_id='$post_id';", count( $comments_feed->items ) );
	mysql_query( $query );
	die();

	// download the comments RSS feed, parse the feed, count the comments, and insert the comments in db if they don't exist
	
}

?>