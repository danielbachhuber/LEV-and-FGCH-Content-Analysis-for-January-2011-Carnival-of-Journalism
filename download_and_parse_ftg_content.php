<?php

include_once( 'config.php' );

// Connect to the database
$connection = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) or die( 'Error connecting to database' );
mysql_select_db( DB_NAME );
echo "\nStarting the scraping process\n";

$base_url = 'http://eastvillage.thelocal.nytimes.com/2010/11/';

for ( $i = 30; $i != 0; $i-- ) {
	$feed_url = "$base_url$i/feed/";
	$feed = fetch_rss( $feed_url );
	
	$publication = '';
	if ( strpos( $feed_url, '//fort-greene' ) ) {
		$publication = 'fort-greene';
	} else if ( strpos( $feed_url, '//eastvillage' ) ) {
		$publication = 'eastvillage';
	} else {
		$publication = NULL;
	}
	
	echo count( $feed->items ) . " items for $feed_url\n";
	if ( !count( $feed->items ) ) {
		continue;
	}
	foreach( $feed->items as $item ) {
		$permalink = $item['link'];
		$post_id = explode( '?p=', $item['guid'] );
		$post_id = $post_id[1];
		$body_excerpt = $item['description'];
		$body_html_raw = file_get_html( $permalink );
		$body_html = trim( $body_html_raw->outertext );
		
		// Extract the title from the page instead of the RSS feed
		$title = $body_html_raw->find( 'h2.entry-title' );
		if ( count( $title ) ==  1 ) {
			$title = trim( $title[0]->innertext );
		} else {
			$title = null;
		}

		// Extract the publication date
		$pub_date = $body_html_raw->find( 'span.date' );		
		if ( count( $pub_date ) ==  1 ) {
			$pub_date = trim( $pub_date[0]->plaintext );
		} else {
			$pub_date = null;
		}
		
		// Extract the author information
		$author = $body_html_raw->find( 'address.author a' );
		if ( count( $author ) ) {
			$new_author = '';
			foreach( $author as $value ) {
				$new_author .= ucwords( strtolower( $value->plaintext ) ) . ', ';
			}
			$author = trim( $new_author, ', ' );
		} else {
			$author = null;
		}
		
		// Extract the author type
		$author_type = $body_html_raw->find( 'address.author' );
		$working_author_type = explode( ',', $author_type[0]->plaintext );
		// If there isn't an author type after the comma, then it probably isn't specified
		if ( count( $working_author_type ) == 1 ) {
			$author_type = 'CUNY J-School';
		} else if ( count( $working_author_type ) == 2 ) {
			$author_type = trim( $working_author_type[1] );
		} else if ( count( $working_author_type ) == 3 ) {
			$author_type_1 = explode( 'and', $working_author_type[1] );
			$author_type_1 = trim( $author_type_1[0] );
			$author_type_2 = trim( $working_author_type[2] );
			$author_type = $author_type_1 . ', ' . $author_type_2;				
		} else {
			$author_type = null;
		}
		
		// Extract the body content in html
		$body_content_html = $body_html_raw->find( 'div.entry-content' );
		if ( count( $body_content_html ) == 1 ) {
			$body_content_html = trim( $body_content_html[0]->innertext );
		} else {
			$body_content_html = null;
		}
		
		// Extract the body content in text
		$body_content_text = $body_html_raw->find( 'div.entry-content' );
		if ( count( $body_content_text ) == 1 ) {
			$body_content_text = trim( $body_content_text[0]->plaintext );
		} else {
			$body_content_text = null;
		}
		
		// Extract the post category
		$post_category = $body_html_raw->find( 'span.entry-category' );
		if ( count( $post_category ) == 1 ) {
			$post_category = trim( $post_category[0]->plaintext, ' ,' );
		} else {
			$post_category = null;
		}
		
		// Extract the post category
		$post_tags = $body_html_raw->find( 'span.tags' );
		if ( count( $post_tags ) == 1 ) {
			$post_tags = trim( $post_tags[0]->plaintext, ' ,' );
		} else {
			$post_tags = null;
		}		
		
		// Check to see whether the row exists. If not, insert the record.
		$query = "SELECT * FROM original_posts WHERE post_id='$post_id';";
		$row_exists = mysql_query( $query );
		if ( !mysql_num_rows( $row_exists ) ) {
		$query = sprintf(
			"INSERT INTO original_posts ( post_id, permalink, publication, title, pub_date, author, author_type, body_html, body_excerpt, body_content_html, body_content_text, post_category, post_tags, word_count ) VALUES ( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' );",
			mysql_real_escape_string( $post_id ),
			mysql_real_escape_string( $permalink ),
			mysql_real_escape_string( $publication ),
			mysql_real_escape_string( $title ),
			mysql_real_escape_string( $pub_date ),
			mysql_real_escape_string( $author ),
			mysql_real_escape_string( $author_type ),
			mysql_real_escape_string( $body_html ),
			mysql_real_escape_string( $body_excerpt ),			
			mysql_real_escape_string( $body_content_html ),
			mysql_real_escape_string( $body_content_text ),
			mysql_real_escape_string( $post_category ),
			mysql_real_escape_string( $post_tags ),
			str_word_count( $body_content_text ) );
			
			$insert_results = mysql_query( $query );
			echo "Inserted entry for $permalink\n";
		} else {
			echo "Entry already exists for $permalink\n";
		}
			
	} // END - foreach( $feed->items as $item ) 
	
} // END - for ( $i = 30; $i != 0; $i-- )


?>