<?php


function soundpress_include_post_types( $query ) {

	$soundpress_options = get_option( 'soundpress_option_name' );
 	$append_include_in_loop_3 = array_key_exists('append_include_in_loop_3', $soundpress_options ) ? $soundpress_options['append_include_in_loop_3'] : FALSE;

 	if ( $append_include_in_loop_3 && $query->is_main_query() && !$query->is_admin() && ( $query->is_archive() || $query->is_home() ) ) {
 		$query->set('post_type', 'podcast' );
 	}

 	return;

}

add_action( 'pre_get_posts', 'soundpress_include_post_types' )
?>