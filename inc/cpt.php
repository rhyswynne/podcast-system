<?php 
/*
 *  SoundPress Custom Post Type
 */
//Register Custom Post Type
function soundpress_podcast() {

	$labels = array(
		'name'                => _x( 'Post Types', 'Post Type General Name', 'text_domain' ),
		'singular_name'       => _x( 'soundpress', 'Post Type Singular Name', 'text_domain' ),
		'menu_name'           => __( 'SoundPress', 'text_domain' ),
		'name_admin_bar'      => __( 'SoundPress', 'text_domain' ),
		'parent_item_colon'   => __( 'Parent Item:', 'text_domain' ),
		'all_items'           => __( 'All Items', 'text_domain' ),
		'add_new_item'        => __( 'Add New Item', 'text_domain' ),
		'add_new'             => __( 'Add New', 'text_domain' ),
		'new_item'            => __( 'New Item', 'text_domain' ),
		'edit_item'           => __( 'Edit Item', 'text_domain' ),
		'update_item'         => __( 'Update Item', 'text_domain' ),
		'view_item'           => __( 'View Item', 'text_domain' ),
		'search_items'        => __( 'Search Item', 'text_domain' ),
		'not_found'           => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'text_domain' ),
		);
	$args = array(
		'label'               => __( 'SoundPress', 'text_domain' ),
		'description'         => __( 'SoundPress CPT', 'text_domain' ),
		'labels'              => $labels,
		'supports'            => array('title', 'editor'),
		'taxonomies'          => array( 'category', 'post_tag' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'menu_position'       => 5,
		'show_in_admin_bar'   => true,
		'show_in_nav_menus'   => true,
		'can_export'          => true,
		'has_archive'         => true,		
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'page',
		);
	register_post_type( 'podcast', $args );

}
add_action( 'init', 'soundpress_podcast', 0 );


add_action( 'load-post.php', 'soundpress_meta_boxes_setup' );
add_action( 'load-post-new.php', 'soundpress_meta_boxes_setup' );

/* Meta box setup function. */
function soundpress_meta_boxes_setup() {

	/* Add meta boxes on the 'add_meta_boxes' hook. */
	add_action( 'add_meta_boxes', 'soundpress_add_soundcloud_meta_boxes' );
	/* Save post meta on the save_post hook */
	add_action( 'save_post', 'soundpress_save_class_meta', 10, 2 );
}

/* Create one or more meta boxes to be displayed on the post editor screen. */
function soundpress_add_soundcloud_meta_boxes() {

	add_meta_box(
    'soundpress_url',      // Unique ID
    esc_html__( 'Soundcloud Link', 'Soundcloud' ),    // Title
    'soundpress_soundcloud_meta_box',   // Callback function
    'podcast',         // Admin page (or post type)
    'normal',         // Context
    'default'         // Priority
    );
}

/* Display the post meta box. */
function soundpress_soundcloud_meta_box( $object, $box ) { ?>

<?php wp_nonce_field( basename( __FILE__ ), 'soundpress_url_nonce' ); ?>

<p>
	<label for="soundpress_url"><?php _e( "Add the link to the audio hosted on Soundcloud", 'example' ); ?></label>
	<br />
	<input class="widefat" type="text" name="soundpress_url" id="soundpress_url" value="<?php echo esc_attr( get_post_meta( $object->ID, 'soundpress_soundcloud_class', true ) ); ?>" size="30" />
</p>
<?php }

/* Save the meta box's post metadata. */
function soundpress_save_class_meta( $post_id, $post ) {

	/* Verify the nonce before proceeding. */
	if ( !isset( $_POST['soundpress_url_nonce'] ) || !wp_verify_nonce( $_POST['soundpress_url_nonce'], basename( __FILE__ ) ) )
		return $post_id;

	/* Get the post type object. */
	$post_type = get_post_type_object( $post->post_type );

	/* Check if the current user has permission to edit the post. */
	if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
		return $post_id;

	/* Get the posted data and sanitize it for use as an HTML class. */
	$new_meta_value = ( isset( $_POST['soundpress_url'] ) ? esc_url( $_POST['soundpress_url'] ) : '' );

	/* Get the meta key. */
	$meta_key = 'soundpress_soundcloud_class';

	/* Get the meta value of the custom field key. */
	$meta_value = get_post_meta( $post_id, $meta_key, true );

	/* If a new meta value was added and there was no previous value, add it. */
	if ( $new_meta_value && '' == $meta_value )
		add_post_meta( $post_id, $meta_key, $new_meta_value, true );

	/* If the new meta value does not match the old value, update it. */
	elseif ( $new_meta_value && $new_meta_value != $meta_value )
		update_post_meta( $post_id, $meta_key, $new_meta_value );

	/* If there is no new meta value but an old value exists, delete it. */
	elseif ( '' == $new_meta_value && $meta_value )
		delete_post_meta( $post_id, $meta_key, $meta_value );
}

/* Filter the post class hook with our custom post class function. */
add_filter( 'post_class', 'soundpress_soundcloud_class' );

function soundpress_soundcloud_class( $classes ) {

	/* Get the current post ID. */
	$post_id = get_the_ID();

	/* If we have a post ID, proceed. */
	if ( !empty( $post_id ) ) {

		/* Get the custom post class. */
		$post_class = get_post_meta( $post_id, 'soundpress_soundcloud_class', true );

		/* If a post class was input, sanitize it and add it to the post class array. */
		if ( !empty( $post_class ) )
			$classes[] = sanitize_html_class( $post_class );
	}

	return $classes;
}

function soundpress_add_oembed( $content ) {
	$soundpress_options = get_option( 'soundpress_option_name' ); // Array of All Options
	$append_oembed_2 = $soundpress_options['append_oembed_2']; // Append oembed

	if (is_singular('podcast') && $append_oembed_2 == true)
	{
		$url = get_post_meta(get_the_ID(), 'soundpress_soundcloud_class', true);
		$embed_code = '<div class="soundpress-embedded">'. wp_oembed_get($url) . '</div>';
		$content.= $embed_code;
	}
	return $content;
}

add_filter('the_content', 'soundpress_add_oembed');
