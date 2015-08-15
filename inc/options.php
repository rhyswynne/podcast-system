<?php

class SoundPress {
	private $soundpress_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'soundpress_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'soundpress_page_init' ) );
	}

	public function soundpress_add_plugin_page() {
		add_options_page(
			'SoundPress', // page_title
			'SoundPress', // menu_title
			'manage_options', // capability
			'soundpress', // menu_slug
			array( $this, 'soundpress_create_admin_page' ) // function
			);
	}

	public function soundpress_create_admin_page() {
		$this->soundpress_options = get_option( 'soundpress_option_name' ); ?>

		<div class="wrap">
			<h2>SoundPress</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
				settings_fields( 'soundpress_option_group' );
				do_settings_sections( 'soundpress-admin' );
				submit_button();
				?>
			</form>
		</div>
		<?php }

		public function soundpress_page_init() {
			register_setting(
			'soundpress_option_group', // option_group
			'soundpress_option_name', // option_name
			array( $this, 'soundpress_sanitize' ) // sanitize_callback
			);

			add_settings_section(
			'soundpress_setting_section', // id
			'Settings', // title
			array( $this, 'soundpress_section_info' ), // callback
			'soundpress-admin' // page
			);

			add_settings_field(
			'soundcloud_oauth_client_id_0', // id
			'Soundcloud OAuth Client ID', // title
			array( $this, 'soundcloud_oauth_client_id_0_callback' ), // callback
			'soundpress-admin', // page
			'soundpress_setting_section' // section
			);

			add_settings_field(
			'soundcloud_oauth_secret_1', // id
			'Soundcloud OAuth Secret', // title
			array( $this, 'soundcloud_oauth_secret_1_callback' ), // callback
			'soundpress-admin', // page
			'soundpress_setting_section' // section
			);

			add_settings_field(
			'append_oembed_2', // id
			'Append oembed', // title
			array( $this, 'append_oembed_2_callback' ), // callback
			'soundpress-admin', // page
			'soundpress_setting_section' // section
			);

			add_settings_field(
			'append_include_in_loop_3', // id
			'Include Podcasts In Loop', // title
			array( $this, 'append_include_in_loop_3_callback' ), // callback
			'soundpress-admin', // page
			'soundpress_setting_section' // section
			);
		}

		public function soundpress_sanitize($input) {
			$sanitary_values = array();
			if ( isset( $input['soundcloud_oauth_client_id_0'] ) ) {
				$sanitary_values['soundcloud_oauth_client_id_0'] = sanitize_text_field( $input['soundcloud_oauth_client_id_0'] );
			}

			if ( isset( $input['soundcloud_oauth_secret_1'] ) ) {
				$sanitary_values['soundcloud_oauth_secret_1'] = sanitize_text_field( $input['soundcloud_oauth_secret_1'] );
			}

			if ( isset( $input['append_oembed_2'] ) ) {
				$sanitary_values['append_oembed_2'] = $input['append_oembed_2'];
			}

			if ( isset( $input['append_include_in_loop_3'] ) ) {
				$sanitary_values['append_include_in_loop_3'] = $input['append_include_in_loop_3'];
			}

			return $sanitary_values;
		}

		public function soundpress_section_info() {

		}

		public function soundcloud_oauth_client_id_0_callback() {
			printf(
				'<input class="regular-text" type="text" name="soundpress_option_name[soundcloud_oauth_client_id_0]" id="soundcloud_oauth_client_id_0" value="%s">',
				isset( $this->soundpress_options['soundcloud_oauth_client_id_0'] ) ? esc_attr( $this->soundpress_options['soundcloud_oauth_client_id_0']) : ''
				);
		}

		public function soundcloud_oauth_secret_1_callback() {
			printf(
				'<input class="regular-text" type="text" name="soundpress_option_name[soundcloud_oauth_secret_1]" id="soundcloud_oauth_secret_1" value="%s">',
				isset( $this->soundpress_options['soundcloud_oauth_secret_1'] ) ? esc_attr( $this->soundpress_options['soundcloud_oauth_secret_1']) : ''
				);
		}

		public function append_oembed_2_callback() {
			printf(
				'<input type="checkbox" name="soundpress_option_name[append_oembed_2]" id="append_oembed_2" value="append_oembed_2" %s> <label for="append_oembed_2">Check this if you would like append the embedded Soundcloud to the bottom of your post.</label>',
				( isset( $this->soundpress_options['append_oembed_2'] ) && $this->soundpress_options['append_oembed_2'] === 'append_oembed_2' ) ? 'checked' : ''
				);
		}

		public function append_include_in_loop_3_callback() {
			printf(
				'<input type="checkbox" name="soundpress_option_name[append_include_in_loop_3]" id="append_include_in_loop_3" value="append_include_in_loop_3" %s> <label for="append_include_in_loop_3">Check this if you would like to include the Podcast posts in the loop.</label>',
				( isset( $this->soundpress_options['append_include_in_loop_3'] ) && $this->soundpress_options['append_include_in_loop_3'] === 'append_include_in_loop_3' ) ? 'checked' : ''
				);
		}

	}
	if ( is_admin() )
		$soundpress = new SoundPress();

/*
 * Retrieve this value with:
 * $soundpress_options = get_option( 'soundpress_option_name' ); // Array of All Options
 * $soundcloud_oauth_client_id_0 = $soundpress_options['soundcloud_oauth_client_id_0']; // Soundcloud OAuth Client ID
 * $soundcloud_oauth_secret_1 = $soundpress_options['soundcloud_oauth_secret_1']; // Soundcloud OAuth Secret
 * $append_oembed_2 = $soundpress_options['append_oembed_2']; // Append oembed
 */
?>
