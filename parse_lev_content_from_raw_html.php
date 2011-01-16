<?php

define('DB_NAME', 'locals_2010_analysis');
define('DB_USER', 'root');
define('DB_PASSWORD', 'root');
define('DB_HOST', ':/Applications/MAMP/tmp/mysql/mysql.sock');

include_once( 'lib/simple_html_dom.php' );

// Connect to the database
$connection = mysql_connect( DB_HOST, DB_USER, DB_PASSWORD ) or die( 'Error connecting to database' );
mysql_select_db( DB_NAME );

$query = "SELECT * FROM original_posts;";
$results = mysql_query( $query );
echo "\nSelected all current posts.\n";

/**
 * UPDATE original_posts SET title=NULL, pub_date=NULL, author=NULL, author_type=NULL, body_content_html=NULL, body_content_text=NULL, post_category=NULL, post_tags=NULL, word_count=NULL;
 */

while ( $row = mysql_fetch_assoc( $results ) ) {
	
	extract( $row );
	if ( $body_html ) {
		$html_obj = str_get_html( $body_html );
		
		// Extract the title
		if ( !$title ) {
			$title = $html_obj->find( 'h2.entry-title' );
			if ( count( $title ) ==  1 ) {
				$title = trim( $title[0]->innertext );
			} else {
				$title = null;
			}
		}
		
		// Extract the publication date
		if ( !$pub_date ) {
			$pub_date = $html_obj->find( 'span.date' );		
			if ( count( $pub_date ) ==  1 ) {
				$pub_date = trim( $pub_date[0]->plaintext );
			} else {
				$pub_date = null;
			}
		}
		
		// Extract the author information
		if ( !$author ) {
			$author = $html_obj->find( 'address.author a' );
			if ( count( $author ) ) {
				$new_author = '';
				foreach( $author as $value ) {
					$new_author .= ucwords( strtolower( $value->plaintext ) ) . ', ';
				}
				$author = trim( $new_author, ', ' );
			} else {
				$author = null;
			}
		}
		
		// Extract the author type
		if ( !$author_type ) {
			$author_type = $html_obj->find( 'address.author' );
			$working_author_type = explode( ',', $author_type[0]->plaintext );
			// If there isn't an author type after the comma, then it probably isn't specified
			if ( count( $working_author_type ) == 1 ) {
				$author_type = '20 Cooper Square';
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
		}
		
		// Extract the body content in html
		if ( !$body_content_html ) {
			$body_content_html = $html_obj->find( 'div.entry-content' );
			if ( count( $body_content_html ) == 1 ) {
				$body_content_html = trim( $body_content_html[0]->innertext );
			} else {
				$body_content_html = null;
			}
		}
		
		// Extract the body content in text
		if ( !$body_content_text ) {
			$body_content_text = $html_obj->find( 'div.entry-content' );
			if ( count( $body_content_text ) == 1 ) {
				$body_content_text = trim( $body_content_text[0]->plaintext );
			} else {
				$body_content_text = null;
			}
		}
		
		// Extract the post category
		if ( !$post_category ) {
			$post_category = $html_obj->find( 'span.entry-category' );
			if ( count( $post_category ) == 1 ) {
				$post_category = trim( $post_category[0]->plaintext, ' ,' );
			} else {
				$post_category = null;
			}
		}
		
		// Extract the post category
		if ( !$post_tags ) {
			$post_tags = $html_obj->find( 'span.tags' );
			if ( count( $post_tags ) == 1 ) {
				$post_tags = trim( $post_tags[0]->plaintext, ' ,' );
			} else {
				$post_tags = null;
			}
		}
		
		$query = sprintf( "UPDATE original_posts SET publication='eastvillage', title='%s', pub_date='%s', author='%s', author_type='%s', body_content_html='%s', body_content_text='%s', post_category='%s', post_tags='%s', word_count='%s' WHERE id=$id;", mysql_real_escape_string( $title ), mysql_real_escape_string( $pub_date ), mysql_real_escape_string( $author ), mysql_real_escape_string( $author_type ), mysql_real_escape_string( $body_content_html ), mysql_real_escape_string( $body_content_text ), mysql_real_escape_string( $post_category ), mysql_real_escape_string( $post_tags ), str_word_count( $body_content_text ) );
		$insert_results = mysql_query( $query );
		echo "Extracted and inserted values for post $id.\n";
		
	}
	
}

?>