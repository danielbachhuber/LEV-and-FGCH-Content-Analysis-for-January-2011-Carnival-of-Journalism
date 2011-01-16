<?php
/**
 *
 * @todo Total number of posts per publication
 * @todo Total number of contributors per publication
 * @todo Number of posts 
 */

include( 'config.php' );

$query = "SELECT * FROM stats;";
$stats_results = mysql_query( $query );

$stats = array();
// Load all of the stats into an array
while ( $row = mysql_fetch_assoc( $stats_results ) ) {
	$key = $row['stat'];
	$value = $row['value'];
	$stats[$key] = $value;
}

extract( $stats );
// Generate the stats if they don't yet exist

// Calculation: total number of posts from Fort Greene blog
if ( !isset( $total_post_count_ftg ) ) {
	$query = "SELECT post_id FROM original_posts WHERE publication='fort-greene';";
	$total_post_count_ftg = mysql_num_rows( mysql_query( $query ) );
	$stat = 'total_post_count_ftg';
	$value = $total_post_count_ftg;
	$query = sprintf( "INSERT INTO stats( stat, value ) VALUES ( '$stat', '%s' );", mysql_real_escape_string( $value ) );
	$result = mysql_query( $query );
}

// Calculation: total number of posts from LEV blog
if ( !isset( $total_post_count_lev ) ) {
	$query = "SELECT post_id FROM original_posts WHERE publication='eastvillage';";
	$total_post_count_lev = mysql_num_rows( mysql_query( $query ) );
	$stat = 'total_post_count_lev';
	$value = $total_post_count_lev;
	$query = sprintf( "INSERT INTO stats( stat, value ) VALUES ( '$stat', '%s' );", mysql_real_escape_string( $value ) );
	$result = mysql_query( $query );
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">

<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Stats from The Locals</title>
	<meta name="author" content="Daniel Bachhuber">
	
	<link rel="stylesheet" href="/css/style.css" type="text/css">
	<!-- Date: 2011-01-16 -->
</head>
<body>
	
	<div class="wrap">
		<div class="header">
			
		</div>
	</div>
	
	<div class="wrap">
		<div class="main">
			
			<h3>The Local Fort Greene-Clinton Hill and The Local East Village comparison</h3>
			
			<table>
				<tr>
					<th>Stat</th>
					<th>The Local Fort Greene-Clinton Hill</th>
					<th>The Local East Village</th>
				</tr>
				<tr>
					<td>Total number of posts in November 2010</td>
					<td><?php echo $total_post_count_ftg; ?></td>
					<td><?php echo $total_post_count_lev; ?></td>
			</table>
			
		</div>
	</div>
	
	<div class="wrap">
		<div class="header">
			
		</div>
	</div>

</body>
</html>
