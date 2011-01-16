<?php
/**
 * move_authors_to_table.php
 * Moves author names and types to a dedicated table if they aren't already there.
 *
 * @author danielbachhuber
 */

include_once( 'config.php' );

// Get all of the posts from the database
$query = "SELECT post_id, author, author_type FROM original_posts;";
$all_posts = mysql_query( $query );
echo "\nGrabbing all of the posts from the database\n";

while ( $row = mysql_fetch_assoc( $all_posts ) ) {
	
	$post_id = $row['post_id'];
	$authors = $row['author'];
	$author_types = $row['author_type'];
	
	$authors = explode( ',', $authors );
	$author_types = explode( ',', $author_types );
	
	foreach ( $authors as $key => $author ) {
		$author = trim( $author );
		$query = "SELECT * FROM posts_to_authors WHERE post_id='$post_id' AND author='$author';";
	
		if ( !mysql_num_rows( mysql_query( $query ) ) ) {
		
			$insert_author = sprintf( "INSERT INTO posts_to_authors ( post_id, author, author_type ) VALUES ( '%s', '%s', '%s' );",
									mysql_real_escape_string( $post_id ),
									mysql_real_escape_string( $author ),
									mysql_real_escape_string( trim( $author_types[$key] ) )
			 						);
			mysql_query( $insert_author );
			echo "Entered record for $author on $post_id\n";
		} else {
			echo "Record exists for $author on $post_id\n";
		} // END - if ( !mysql_num_rows( mysql_query( $query ) ) )
		
	} // END - 	foreach ( $authors as $key => $author )
	
} // END - while ( $row = mysql_fetch_assoc( $all_posts ) )

echo "All done!\n"

?>