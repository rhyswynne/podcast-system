<?php

define( 'TEST_TRACK', 'https://soundcloud.com/thenextweb/testing-rapmic-for-ios' );
define( 'POST_TYPE', 'post' );


/**
 * Add track details when a post is saved.
 *
 * @param int 	$post_id 		The post ID.
 * @param post 	$post 			The post object.
 * @param bool 	$update 		Whether this is an existing post being updated or not.
 */
function soundpress_add_track_details_to_post( $post_id, $post, $update ) {

	$trackurl = TEST_TRACK;

	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	if ( $trackurl ) {

		$track_details = soundcloud_remote_get( $trackurl );

		if ( is_object( $track_details ) ) {

			// Check for thumbnail. If not present, get the board Image
			if ( !has_post_thumbnail( $post_id ) ) {

				$thumbnailurl = $track_details->artwork_url;

				$tmp = download_url( $thumbnailurl );
				
				if( is_wp_error( $tmp ) ){

				}
				
				$desc = get_the_title( $post_id );
				$file_array = array();

				// Set variables for storage
				// fix file filename for query strings
				preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png)/i', $thumbnailurl, $matches);
				$file_array['name'] = basename($matches[0]);
				$file_array['tmp_name'] = $tmp;

				// If error storing temporarily, unlink
				if ( is_wp_error( $tmp ) ) {
					@unlink($file_array['tmp_name']);
					$file_array['tmp_name'] = '';
				}

				// do the validation and storage stuff
				$id = media_handle_sideload( $file_array, $post_id, $desc );

				// If error storing permanently, unlink
				if ( is_wp_error($id) ) {
					@unlink($file_array['tmp_name']);
					return $id;
				}

				set_post_thumbnail( $post_id, $id );

			}

			// Check for Description
			if ( "" == get_the_content() ) {
				$postcontentarray = array(
					'ID'           => $post_id,
					'post_content' => $track_details->description,
					);

				// Update the post into the database
				wp_update_post( $postcontentarray );
			}

			// Get The Duration
			$durationseconds = $track_details->duration / 1000;

			update_post_meta( $post_id, 'podcast_duration', esc_attr( $durationseconds ) );

			if ( TRUE == $track_details->downloadable ) {

				$download_url = esc_attr( $track_details->download_url );

				update_post_meta( $post_id, 'podcast_download_url', $download_url );

			}

		}

	}
	
}
add_action( 'save_post_' . POST_TYPE, 'soundpress_add_track_details_to_post', 10, 3 );

/**
 * Connect to a Soundcloud URL and then pull in details.
 *
 * @param  string $trackurl 		 	The URL string.
 * @return mixed 						Array if track found, or else false.
 */
function soundcloud_remote_get( $trackurl ) {

	$soundpress_options = get_option( 'soundpress_option_name' );
	$client_id 			= $soundpress_options['soundcloud_oauth_client_id_0'];
	$trackname 			= soundcloud_get_track_name( $trackurl );
	$apiurl				= 'http://api.soundcloud.com/tracks/' . $trackname . '?client_id=' . $client_id;

	$response = wp_remote_get( $apiurl );

	if( is_array($response) ) {

  		$trackdetailsjson 		= $response['body']; // use the content

  		$trackdetails = json_decode( $trackdetailsjson );

  		if ( is_object( $trackdetails ) ) {
  			return $trackdetails;
  		} else {
  			return FALSE;
  		}
  	} else {
  		return FALSE;
  	}
  } 


/**
 * Get the track name based on the URL.
 * @param  string $url 		 The URL string.
 * @return string $trackname The Track Name.
 */
function soundcloud_get_track_name( $url ) {

	$urlparts 	= explode( '/', $url );
	$trackname 	= end( $urlparts );

	return $trackname; 
}