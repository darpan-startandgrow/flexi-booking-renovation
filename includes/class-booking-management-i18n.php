<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @author     Start and Grow <laravel6@startandgrow.in>
 */
class Booking_Management_i18n {

	/**
	 * Supported translation plugins
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $supported_plugins = array(
		'translatepress-multilingual/index.php'    => 'translatepress',
		'sitepress-multilingual-cms/sitepress.php' => 'wpml',
	);

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'service-booking',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}//end load_plugin_textdomain()


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function bm_search_language() {
		$bmrequests = new BM_Request();
		$plugin     = $this->bm_detect_translation_plugin();
		$lang       = false;
		switch ( $plugin ) {
			case 'translatepress':
				$lang = $bmrequests->bm_get_current_trp_language();
				break;

			case 'wpml':
				$lang = apply_filters( 'wpml_current_language', null );
				break;

			default:
				$lang = get_locale();
		}
		return $lang;
	}//end bm_search_language()


	/**
	 * Detect which translation plugin is active.
	 *
	 * @since 1.0.0
	 * @return string Plugin slug: 'translatepress', 'wpml'
	 */
	public function bm_detect_translation_plugin() {
		// TranslatePress
		if ( class_exists( 'TRP_Translate_Press' ) ) {
			return 'translatepress';
		}

		// WPML
		if ( defined( 'ICL_SITEPRESS_VERSION' ) || function_exists( 'icl_get_current_language' ) ) {
			return 'wpml';
		}

		return 'none';
	}//end bm_detect_translation_plugin()

	/**
	 * Detect which translation plugin is active
	 */
	public function detect_active_plugin() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		$active_plugin = 'none';

		foreach ( $this->supported_plugins as $plugin_file => $slug ) {
			if ( is_plugin_active( $plugin_file ) ) {
				$active_plugin = $slug;
				break;
			}
		}

		if ( $active_plugin !== 'none' ) {
			$this->bm_add_option_language_plugin( $active_plugin );
		}
	}//end detect_active_plugin()


	/**
	 * On plugin activation
	 */
	public function on_plugin_activation( $plugin ) {
		$active_plugin = 'none';
		foreach ( $this->supported_plugins as $plugin_file => $slug ) {
			if ( strpos( $plugin, $plugin_file ) !== false ) {
				$active_plugin = $slug;
				$this->bm_add_option_language_plugin( $active_plugin );
			}
		}
	}//end on_plugin_activation()


	/**
	 * On plugin deactivation
	 *
	 * @since 1.0.0
	 */
	public function on_plugin_deactivation( $plugin ) {
		$active_plugin = 'none';

		foreach ( $this->supported_plugins as $plugin_file => $slug ) {
			if ( strpos( $plugin, $plugin_file ) !== false ) {
				if ( $active_plugin === $slug ) {
					$active_plugin = 'none';
				}
				$this->bm_remove_option_language_plugin( $plugin );
			}
		}
	}//end on_plugin_deactivation()


	/**
	 * Add option to store active language plugin
	 *
	 * @since 1.0.0
	 */
	public function bm_add_option_language_plugin( $plugin ) {
		$dbhandler = new BM_DBhandler();
		if ( $plugin != 'none' ) {
			$dbhandler->update_global_option_value( 'lang_plugin', $plugin );
		}
	}//end bm_add_option_language_plugin()


	/**
	 * Remove option to store active language plugin
	 *
	 * @since 1.0.0
	 */
	public function bm_remove_option_language_plugin( $plugin ) {
		if ( $plugin != 'none' ) {
			delete_option( 'lang_plugin', $plugin );
		}
	}//end bm_remove_option_language_plugin()


	/**
	 * Save frontend language selection
	 *
	 * @since 1.0.0
	 */
	public function bm_save_frontend_language() {
		$dbhandler = new BM_DBhandler();
		if ( is_admin() ) {
			return;
		}
		$lang = $this->bm_search_language();
		if ( in_array( $lang, array( 'en', 'it' ) ) ) {
			$current_locale = $lang == 'it' ? 'it_IT' : 'en_US';
			$dbhandler->update_global_option_value( 'bm_flexi_current_locale_frontend', $current_locale );
			$dbhandler->update_global_option_value( 'bm_flexi_current_language_frontend', $lang );
		}
	}//end bm_save_frontend_language()


	/**
	 * Save backend language selection
	 *
	 * @since 1.0.0
	 */
	public function bm_save_backend_language() {
		$dbhandler = new BM_DBhandler();
		global $sitepress;
		if ( ! is_admin() ) {
			return;
		}
		if ( is_admin() && ! wp_doing_ajax() && ! wp_doing_cron() ) {
			$active_plugin = get_option( 'lang_plugin', 'none' );
			$bm_lang       = get_option( 'bm_flexi_current_language', 'en' );
			$bm_locale     = get_option( 'bm_flexi_current_locale', 'en_US' );
			$dbhandler->update_global_option_value( 'bm_flexi_current_locale_backend', $bm_locale );
			$dbhandler->update_global_option_value( 'bm_flexi_current_language_backend', $bm_lang );
			switch_to_locale( $bm_locale );
			$bm_locale == 'en_US' ? update_option( 'WPLANG', '' ) : update_option( 'WPLANG', $bm_locale );

			$wpml_locale = apply_filters( 'wpml_current_language', null );
			$mapping     = array(
				'en' => 'en_US',
				'it' => 'it_IT',
			);
			if ( isset( $mapping[ $wpml_locale ] ) && $sitepress ) {
				switch_to_locale( $mapping[ $wpml_locale ] );
				$dbhandler->update_global_option_value( 'bm_flexi_current_locale_backend', $mapping[ $wpml_locale ] );
				$dbhandler->update_global_option_value( 'bm_flexi_current_language_backend', $wpml_locale );
			}
		}
	}//end bm_save_backend_language()


	/**
	 * Force admin locale from WPML
	 *
	 * @since 1.0.0
	 */
	public function bm_force_admin_locale_from_wpml( $locale ) {
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$wpml_locale = apply_filters( 'wpml_current_language', null );
			$mapping     = array(
				'en' => 'en_US',
				'it' => 'it_IT',
			);
			if ( isset( $mapping[ $wpml_locale ] ) ) {
				update_option( 'WPLANG', $mapping[ $wpml_locale ] == 'en_US' ? '' : $mapping[ $wpml_locale ] );
				$locale = $mapping[ $wpml_locale ];
			}
		}
		return $locale;
	}//end bm_force_admin_locale_from_wpml()


	/**
	 * Redirect textdomain to plugin language files for WPML
	 *
	 * @since 1.0.0
	 */
	public function bm_redirect_textdomain_to_plugin( $mofile, $domain ) {
		global $sitepress;
		if ( is_admin() && $sitepress ) {
			$locale      = determine_locale();
			$wpml_locale = apply_filters( 'wpml_current_language', null );
			$mapping     = array(
				'en' => 'en_US',
				'it' => 'it_IT',
			);
			if ( isset( $mapping[ $wpml_locale ] ) && $domain == 'service-booking' ) {
				$locale         = $mapping[ $wpml_locale ];
				$service_domain = 'service-booking';
				$custom_mofile  = WP_PLUGIN_DIR . '/sg-woocommerce-booking/languages/' . $service_domain . '-' . $locale . '.mo';
				if ( file_exists( $custom_mofile ) ) {
					return $custom_mofile;
				}
			}
		}
		return $mofile;
	}//end bm_redirect_textdomain_to_plugin()
}//end class
