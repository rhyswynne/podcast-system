<?php


define('CLIENT_ID', '603647fd1bed7658d61538e8d66293e8' );
define('CLIENT_SECRET', '7fc36c961b2053215488a6ec2e2979b3' );
define( 'TEST_TRACK', 'https://soundcloud.com/thenextweb/testing-rapmic-for-ios' );


/**
 * Connect to a Soundcloud URL and then pull in details.
 *
 * @return void
 */
function soundcloud_remote_get() {

	$trackname 		= soundcloud_get_track_name( TEST_TRACK );
	$apiurl			= 'http://api.soundcloud.com/tracks/' . $trackname . '?client_id=' . CLIENT_ID;

	$response = wp_remote_get( $apiurl, $args );

	if( is_array($response) ) {
  		$header 	= $response['headers']; // array of http header lines
  		$body 		= $response['body']; // use the content
	}

	wp_die( print_r( $header ) . '<br/><br/>' . print_r( $body ) );

}


/**
 * Get the track name based on the URL.
 * @param  string $url 		 The URL string.
 * @return string $trackname The Track Name.
 */
function soundcloud_get_track_name( $url ) {

	$urlparts 	= explode( '/', $url );
	$trackname 	= end( $url );

	return $trackname; 
}