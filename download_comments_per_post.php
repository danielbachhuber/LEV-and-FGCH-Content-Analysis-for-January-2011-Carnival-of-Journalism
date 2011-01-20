<?php
/**
 * download_comments_per_post.php
 * For any given WordPress-powered post permalink, download the comments RSS feed for count and to store each comment
 *
 * @author danielbachhuber
 */

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
	
	if ( !count( $comments_feed->items ) ) {
		continue;
	}
	
	foreach( $comments_feed->items as $item ) {
		$comment_id = explode( '#comment-', $item['guid'] );
		$comment_id = $comment_id[1];
		$comment_author = $item['dc']['creator'];
		$comment_link = $item['link'];
		$comment_content_html = $item['content']['encoded'];
		$comment_content_text = $item['description'];
		
		$query = "SELECT * FROM posts_to_comments WHERE comment_link='$comment_link';";
		$row_exists = mysql_query( $query );
		if ( !mysql_num_rows( $row_exists ) ) {
			$query = sprintf( "INSERT INTO posts_to_comments( post_id, comment_id, comment_author, comment_content_html, comment_content_text, comment_link, comment_word_count ) VALUES ( $post_id, '%s', '%s', '%s', '%s', '%s', '%s' );",
					mysql_real_escape_string( $comment_id ),
					mysql_real_escape_string( $comment_author ),
					mysql_real_escape_string( $comment_content_html ),
					mysql_real_escape_string( $comment_content_text ),
					mysql_real_escape_string( $comment_link ),
					str_word_count( $comment_content_text )
				);
			mysql_query( $query );
			echo "Inserted entry for $comment_link\n";
		} else {
			echo "Entry already exists for $comment_link\n";			
		}
		
	} // END - foreach( $comments_feed->items as $item )
	
} // END - while ( $row = mysql_fetch_assoc( $results ) )

?>