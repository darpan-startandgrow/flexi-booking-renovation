<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://startandgrow.in
 * @since      1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @author     Start and Grow <laravel6@startandgrow.in>
 */
class Booking_Management {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Booking_Management_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'BOOKING_MANAGEMENT_VERSION' ) ) {
			$this->version = BOOKING_MANAGEMENT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'booking-management';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->define_smtp_connection();
		$this->enable_stripes_connection();

		// ✅ Initialize the API
        $this->init_api();

		// Initialize the Shortcodes
		$this->init_react_shortcodes();
		ob_start();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Booking_Management_Loader. Orchestrates the hooks of the plugin.
	 * - Booking_Management_i18n. Defines internationalization functionality.
	 * - Booking_Management_Admin. Defines all hooks for the admin area.
	 * - Booking_Management_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-activator.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-booking-management-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-booking-management-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-dbhandler.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-request.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-woocommerce.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-email.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-smtp.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-sanitized.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-payment-gateway.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-stripes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-process-payment.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-voucher-base.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-voucher-redeem.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-coupon-validation.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-feature-control.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-management-pdf-customizer.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-api.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-react-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-booking-validation.php';
		$this->loader = new Booking_Management_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Booking_Management_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
         $plugin_i18n = new Booking_Management_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'detect_active_plugin', 20 );
        $this->loader->add_action( 'activated_plugin', $plugin_i18n, 'on_plugin_activation', 10, 1 );
        $this->loader->add_action( 'deactivated_plugin', $plugin_i18n, 'on_plugin_deactivation', 10, 1 );
        $this->loader->add_action( 'wp', $plugin_i18n, 'bm_save_frontend_language' );
        $this->loader->add_action( 'admin_init', $plugin_i18n, 'bm_save_backend_language', 1 );
		$this->loader->add_filter( 'locale', $plugin_i18n, 'bm_force_admin_locale_from_wpml', 999, 1 );
		$this->loader->add_filter( 'load_textdomain_mofile', $plugin_i18n, 'bm_redirect_textdomain_to_plugin', 10, 2 );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
         $plugin_admin = new Booking_Management_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'booking_admin_menu' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'bm_disable_admin_notices_on_specific_pages', 1 );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_set_timezone' );
		$this->loader->add_action( 'update_option_timezone_string', $plugin_admin, 'bm_update_plugin_timezone_on_wp_change', 10, 2 );
		$this->loader->add_action( 'update_option_gmt_offset', $plugin_admin, 'bm_update_plugin_timezone_on_gmt_offset_change', 10, 2 );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_register_shortcodes' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_set_installed_languages' );
		// $this->loader->add_action( 'init', $plugin_admin, 'bm_load_service_booking_locale' ); //->translation issues
		$this->loader->add_action( 'init', $plugin_admin, 'bm_multilingual_email' );
		$this->loader->add_filter( 'cron_schedules', $plugin_admin, 'bm_custom_cron_schedule' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_check_booking_requests' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_check_falied_emails_and_resend_pdfs' );
		$this->loader->add_action( 'bm_resend_missing_emails_hook', $plugin_admin, 'bm_resend_missing_emails_cron' );
		$this->loader->add_action( 'flexibooking_check_expired_book_on_request_bookings', $plugin_admin, 'flexibooking_check_expired_book_on_request_bookings_callback' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_mark_flexi_paid_processing_bookings_as_completed' );
		$this->loader->add_action( 'flexibooking_check_paid_expired_processing_bookings', $plugin_admin, 'flexibooking_check_paid_expired_processing_bookings_callback' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_mark_pending_bookings_as_cancelled' );
		$this->loader->add_action( 'flexibooking_check_expired_pending_bookings', $plugin_admin, 'flexibooking_check_expired_pending_bookings_callback' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_mark_expired_free_bookings_as_completed' );
		$this->loader->add_action( 'flexibooking_check_expired_free_bookings', $plugin_admin, 'flexibooking_check_expired_free_bookings_callback' );
		$this->loader->add_action( 'init', $plugin_admin, 'bm_check_expired_vouchers' );
		$this->loader->add_action( 'flexibooking_check_expired_vouchers', $plugin_admin, 'flexibooking_check_expired_vouchers_callback' );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'bm_add_flexibooking_language_switcher_in_admin_bar', 999 );
		$this->loader->add_action( 'wp_footer', $plugin_admin, 'bm_add_flexibooking_language_switcher_in_footer' );
		$this->loader->add_action( 'wp_ajax_bm_flexi_set_lang', $plugin_admin, 'bm_flexibooking_set_language' );
		$this->loader->add_action( 'wp_ajax_bm_sort_service_listing', $plugin_admin, 'bm_sort_service_listing' );
		$this->loader->add_action( 'wp_ajax_bm_remove_service', $plugin_admin, 'bm_remove_service' );
		$this->loader->add_action( 'wp_ajax_bm_remove_category', $plugin_admin, 'bm_remove_category' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_price_module_listing', $plugin_admin, 'bm_fetch_price_module_listing' );
		$this->loader->add_action( 'wp_ajax_bm_remove_price_module', $plugin_admin, 'bm_remove_price_module' );
		$this->loader->add_action( 'wp_ajax_bm_sort_category_listing', $plugin_admin, 'bm_sort_category_listing' );
		$this->loader->add_action( 'wp_ajax_bm_get_service_prices', $plugin_admin, 'bm_get_service_prices' );
		$this->loader->add_action( 'wp_ajax_bm_set_serice_price', $plugin_admin, 'bm_set_serice_price' );
		$this->loader->add_action( 'wp_ajax_bm_set_serice_price_module', $plugin_admin, 'bm_set_serice_price_module' );
		$this->loader->add_action( 'wp_ajax_bm_set_bulk_serice_price', $plugin_admin, 'bm_set_bulk_serice_price' );
		$this->loader->add_action( 'wp_ajax_bm_set_bulk_serice_price_module', $plugin_admin, 'bm_set_bulk_serice_price_module' );
		$this->loader->add_action( 'wp_ajax_bm_get_serice_stopsales', $plugin_admin, 'bm_get_serice_stopsales' );
		$this->loader->add_action( 'wp_ajax_bm_get_service_saleswitch', $plugin_admin, 'bm_get_service_saleswitch' );
		$this->loader->add_action( 'wp_ajax_bm_set_serice_stopsales', $plugin_admin, 'bm_set_serice_stopsales' );
		$this->loader->add_action( 'wp_ajax_bm_set_service_saleswitch', $plugin_admin, 'bm_set_service_saleswitch' );
		$this->loader->add_action( 'wp_ajax_bm_set_bulk_serice_stopsales', $plugin_admin, 'bm_set_bulk_serice_stopsales' );
		$this->loader->add_action( 'wp_ajax_bm_set_bulk_service_saleswitch', $plugin_admin, 'bm_set_bulk_service_saleswitch' );
		$this->loader->add_action( 'wp_ajax_bm_get_service_max_cap', $plugin_admin, 'bm_get_service_max_cap' );
		$this->loader->add_action( 'wp_ajax_bm_set_serice_max_cap', $plugin_admin, 'bm_set_serice_max_cap' );
		$this->loader->add_action( 'wp_ajax_bm_set_bulk_serice_max_cap', $plugin_admin, 'bm_set_bulk_serice_max_cap' );
		$this->loader->add_action( 'wp_ajax_bm_get_service_time_slots', $plugin_admin, 'bm_get_service_time_slots' );
		$this->loader->add_action( 'wp_ajax_bm_get_specific_time_slot', $plugin_admin, 'bm_get_specific_time_slot' );
		$this->loader->add_action( 'wp_ajax_bm_set_variable_time_slot', $plugin_admin, 'bm_set_variable_time_slot' );
		$this->loader->add_action( 'wp_ajax_bm_remove_variable_time_slot', $plugin_admin, 'bm_remove_variable_time_slot' );
		$this->loader->add_action( 'wp_ajax_bm_save_field_and_setting', $plugin_admin, 'bm_save_field_and_setting' );
		$this->loader->add_action( 'wp_ajax_bm_get_all_field_labels', $plugin_admin, 'bm_get_all_field_labels' );
		$this->loader->add_action( 'wp_ajax_bm_get_field_settings', $plugin_admin, 'bm_get_field_settings' );
		$this->loader->add_action( 'wp_ajax_bm_get_fieldkey_and_order', $plugin_admin, 'bm_get_fieldkey_and_order' );
		$this->loader->add_action( 'wp_ajax_bm_remove_field', $plugin_admin, 'bm_remove_field' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_preview_form', $plugin_admin, 'bm_fetch_preview_form' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_template_listing', $plugin_admin, 'bm_fetch_template_listing' );
		$this->loader->add_action( 'wp_ajax_bm_remove_template', $plugin_admin, 'bm_remove_template' );
		$this->loader->add_action( 'wp_ajax_bm_test_smtp', $plugin_admin, 'bm_check_smtp_connection' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_timezone', $plugin_admin, 'bm_fetch_timezone' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_ordered_product_details', $plugin_admin, 'bm_fetch_ordered_product_details' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_ordered_service_details', $plugin_admin, 'bm_fetch_ordered_service_details' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_customer_data_for_order', $plugin_admin, 'bm_fetch_customer_data_for_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_customer_data_for_failed_order', $plugin_admin, 'bm_fetch_customer_data_for_failed_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_customer_data_for_archived_order', $plugin_admin, 'bm_fetch_customer_data_for_archived_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_attachments_for_order', $plugin_admin, 'bm_fetch_attachments_for_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_attachments_for_archived_order', $plugin_admin, 'bm_fetch_attachments_for_archived_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_attachments_for_failed_order', $plugin_admin, 'bm_fetch_attachments_for_failed_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_services_by_category_id', $plugin_admin, 'bm_fetch_services_by_category_id' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_new_order_service_time_slots', $plugin_admin, 'bm_fetch_new_order_service_time_slots' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_extras_for_backend_order', $plugin_admin, 'bm_fetch_service_extras_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_mincap_and_cap_left', $plugin_admin, 'bm_fetch_mincap_and_cap_left' );
		$this->loader->add_action( 'wp_ajax_bm_archive_order', $plugin_admin, 'bm_archive_order' );
		$this->loader->add_action( 'wp_ajax_bm_remove_order', $plugin_admin, 'bm_remove_order' );
		$this->loader->add_action( 'wp_ajax_bm_restore_order', $plugin_admin, 'bm_restore_order' );
		$this->loader->add_action( 'wp_ajax_bm_remove_failed_order', $plugin_admin, 'bm_remove_failed_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_bookable_services_by_category_id_and_date', $plugin_admin, 'bm_fetch_bookable_services_by_category_id_and_date' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_price_for_backend_order', $plugin_admin, 'bm_fetch_service_price_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_bm_change_order_status_to_complete_or_cancelled', $plugin_admin, 'bm_change_order_status_to_complete_or_cancelled' );
		$this->loader->add_action( 'wp_ajax_bm_change_order_status', $plugin_admin, 'bm_change_order_status' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_columns_screen_options', $plugin_admin, 'bm_fetch_columns_screen_options' );
		$this->loader->add_action( 'wp_ajax_bm_save_columns_screen_options', $plugin_admin, 'bm_save_columns_screen_options' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_order_as_per_search', $plugin_admin, 'bm_fetch_order_as_per_search' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_archived_order_as_per_search', $plugin_admin, 'bm_fetch_archived_order_as_per_search' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_failed_order_as_per_search', $plugin_admin, 'bm_fetch_failed_order_as_per_search' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_all_orders', $plugin_admin, 'bm_fetch_all_orders' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_saved_order_search', $plugin_admin, 'bm_fetch_saved_order_search' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_dashoboard_order_global_search', $plugin_admin, 'bm_fetch_dashoboard_order_global_search' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_dashboard_weekly_orders', $plugin_admin, 'bm_fetch_dashboard_weekly_orders' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_cat_wise_orders', $plugin_admin, 'bm_fetch_cat_wise_orders' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_wise_revenue', $plugin_admin, 'bm_fetch_service_wise_revenue' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_upcoming_orders', $plugin_admin, 'bm_fetch_upcoming_orders' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_datewise_revenue_orders', $plugin_admin, 'bm_fetch_datewise_revenue_orders' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_customer_wise_revenue_orders', $plugin_admin, 'bm_fetch_customer_wise_revenue_orders' );
		$this->loader->add_action( 'wp_ajax_bm_get_primary_email_field_key', $plugin_admin, 'bm_get_primary_email_field_key' );
		$this->loader->add_action( 'wp_ajax_bm_save_primary_email_field_key', $plugin_admin, 'bm_save_primary_email_field_key' );
		$this->loader->add_action( 'wp_ajax_bm_save_non_primary_email_as_primary', $plugin_admin, 'bm_save_non_primary_email_as_primary' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_booking_counts', $plugin_admin, 'bm_fetch_booking_counts' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_customer_and_total_booked_slot_counts', $plugin_admin, 'bm_fetch_customer_and_total_booked_slot_counts' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_booking_status_counts', $plugin_admin, 'bm_fetch_booking_status_counts' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_booking_overview', $plugin_admin, 'bm_fetch_booking_overview' );
		$this->loader->add_action( 'wp_ajax_bm_change_service_visibility', $plugin_admin, 'bm_change_service_visibility' );
		$this->loader->add_action( 'wp_ajax_bm_change_extra_service_visibility', $plugin_admin, 'bm_change_extra_service_visibility' );
		$this->loader->add_action( 'wp_ajax_bm_change_category_visibility', $plugin_admin, 'bm_change_category_visibility' );
		$this->loader->add_action( 'wp_ajax_bm_change_customer_visibility', $plugin_admin, 'bm_change_customer_visibility' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_vocuher_booking_info', $plugin_admin, 'bm_fetch_vocuher_booking_info' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_vocuher_gifter_info', $plugin_admin, 'bm_fetch_vocuher_gifter_info' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_vocuher_recipient_info', $plugin_admin, 'bm_fetch_vocuher_recipient_info' );
		$this->loader->add_action( 'wp_ajax_bm_change_voucher_status', $plugin_admin, 'bm_change_voucher_status' );
		$this->loader->add_filter( 'flexibooking_cancel_booking', $plugin_admin, 'bm_flexibooking_cancel_booking', 10, 1 );
		$this->loader->add_filter( 'flexibooking_update_status_as_refunded', $plugin_admin, 'bm_flexibooking_update_status_as_refunded', 10, 2 );
		$this->loader->add_filter( 'flexibooking_update_status_as_completed', $plugin_admin, 'bm_flexibooking_update_status_as_completed', 10, 1 );
		$this->loader->add_filter( 'flexibooking_update_status_as_processing', $plugin_admin, 'bm_flexibooking_update_status_as_processing', 10, 1 );
		$this->loader->add_filter( 'flexibooking_update_status_as_on_hold', $plugin_admin, 'bm_flexibooking_update_status_as_on_hold', 10, 1 );
		$this->loader->add_filter( 'flexibooking_mark_processing_orders_as_complete', $plugin_admin, 'bm_flexibooking_mark_processing_orders_as_complete', 10, 1 );
		$this->loader->add_filter( 'bm_mark_free_orders_as_complete', $plugin_admin, 'bm_mark_free_orders_as_complete', 10, 1 );
		$this->loader->add_action( 'wp_ajax_bm_check_if_existing_field_key', $plugin_admin, 'bm_check_if_existing_field_key' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_event_condition_value', $plugin_admin, 'bm_fetch_value_for_notification_event_type' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_notification_processes_listing', $plugin_admin, 'bm_fetch_notification_processes_listing' );
		$this->loader->add_action( 'wp_ajax_bm_remove_process', $plugin_admin, 'bm_remove_notification_process' );
		$this->loader->add_action( 'wp_ajax_bm_change_process_visibility', $plugin_admin, 'bm_change_notification_process_visibility' );
		$this->loader->add_action( 'wp_ajax_bm_change_template_visibility', $plugin_admin, 'bm_change_email_template_visibility' );
		$this->loader->add_action( 'wp_ajax_bm_cancel_bor_order', $plugin_admin, 'bm_cancel_book_on_request_order' );
		$this->loader->add_action( 'wp_ajax_bm_approve_bor_order', $plugin_admin, 'bm_approve_book_on_request_order' );
		$this->loader->add_action( 'wp_ajax_bm_update_transaction', $plugin_admin, 'bm_update_order_transaction' );
		$this->loader->add_action( 'wp_ajax_bm_save_order_transaction', $plugin_admin, 'bm_save_order_transaction' );
		$this->loader->add_action( 'flexibooking_set_process_approved_order', $plugin_admin, 'bm_flexibooking_set_process_approved_order_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_approved_order', $plugin_admin, 'bm_flexibooking_mail_on_approved_order_callback', 10, 3 );
		$this->loader->add_action( 'flexibooking_set_process_cancel_order', $plugin_admin, 'bm_flexibooking_set_process_cancel_order_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_cancel_order', $plugin_admin, 'bm_flexibooking_mail_on_cancel_order_callback', 10, 3 );
		$this->loader->add_action( 'flexibooking_set_process_failed_order', $plugin_admin, 'bm_flexibooking_set_process_failed_order_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_failed_order', $plugin_admin, 'bm_flexibooking_mail_on_failed_order_callback', 10, 3 );
		$this->loader->add_filter( 'flexibooking_refund_cancelled_order', $plugin_admin, 'bm_flexibooking_refund_cancelled_order', 10, 1 );
		$this->loader->add_action( 'flexibooking_set_process_order_refund', $plugin_admin, 'bm_flexibooking_set_process_order_refund_callback', 10, 2 );
		$this->loader->add_action( 'flexibooking_mail_order_refund', $plugin_admin, 'bm_flexibooking_mail_on_order_refund_callback', 10, 3 );
		// $this->loader->add_filter( 'flexibooking_google_analytics_data', $plugin_admin, 'bm_prepare_ga_purchase_data', 10, 1 );
		$this->loader->add_action( 'wp_ajax_bm_show_mail_details', $plugin_admin, 'bm_show_mail_details' );
		$this->loader->add_action( 'wp_ajax_bm_show_email_body', $plugin_admin, 'bm_show_email_body' );
		$this->loader->add_action( 'wp_ajax_bm_open_email_body', $plugin_admin, 'bm_open_email_body' );
		$this->loader->add_action( 'wp_ajax_bm_resend_email', $plugin_admin, 'bm_resend_email' );
		$this->loader->add_action( 'wp_ajax_bm_add_email_attachment', $plugin_admin, 'bm_add_email_attachment' );
		$this->loader->add_action( 'wp_ajax_bm_remove_email_attachment', $plugin_admin, 'bm_remove_email_attachment' );
		$this->loader->add_action( 'wp_ajax_bm_remove_temporary_email_attachment', $plugin_admin, 'bm_remove_temporary_email_attachment' );
		$this->loader->add_action( 'wp_ajax_bm_check_admin_password', $plugin_admin, 'bm_check_admin_password' );
		$this->loader->add_action( 'wp_ajax_bm_filter_service_planner_events', $plugin_admin, 'bm_filter_service_planner_events_callback' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_planner_dialog_content', $plugin_admin, 'bm_fetch_service_planner_dialog_content' );
		$this->loader->add_action( 'wp_ajax_bm_resend_order_email', $plugin_admin, 'bm_resend_order_email' );

		$this->loader->add_action( 'wp_ajax_bm_single_service_planner_events', $plugin_admin, 'bm_single_service_planner_events_callback' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_planner_time_slots', $plugin_admin, 'bm_fetch_service_planner_time_slots' );
		$this->loader->add_action( 'wp_ajax_bm_get_order_personal_info', $plugin_admin, 'bm_get_order_personal_info' );
		$this->loader->add_action( 'wp_ajax_bm_get_order_payment_details', $plugin_admin, 'bm_get_order_payment_details' );
		$this->loader->add_action( 'wp_ajax_bm_get_order_email_info', $plugin_admin, 'bm_get_order_email_info' );
		$this->loader->add_action( 'wp_ajax_bm_get_order_failed_transactions', $plugin_admin, 'bm_get_order_failed_transactions' );
		$this->loader->add_action( 'wp_ajax_bm_get_order_products', $plugin_admin, 'bm_get_order_products' );
		$this->loader->add_action( 'wp_ajax_bm_get_email_content', $plugin_admin, 'bm_get_email_content' );
		$this->loader->add_action( 'wp_ajax_bm_retry_failed_payment', $plugin_admin, 'bm_retry_failed_payment' );
		$this->loader->add_action( 'wp_ajax_qr_checkin_process', $plugin_admin, 'bm_qr_checkin_process' );

		$this->loader->add_action( 'wp_ajax_bm_export_checkin_options_html', $plugin_admin, 'bm_export_checkin_options_html' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_export_checkin_records', $plugin_admin, 'bm_fetch_export_checkin_records_as_per_type' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_saved_checkin_search', $plugin_admin, 'bm_fetch_saved_checkin_search' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_checkin_as_per_search', $plugin_admin, 'bm_fetch_checkin_as_per_search' );
		$this->loader->add_action( 'wp_ajax_verify_qr_code', $plugin_admin, 'bm_handle_qr_verification' );
		$this->loader->add_action( 'wp_ajax_get_order_details', $plugin_admin, 'bm_get_order_detail_for_check_in' );
		$this->loader->add_action( 'wp_ajax_update_checkin_status', $plugin_admin, 'bm_update_checkin_status' );
		$this->loader->add_action( 'wp_ajax_manual_checkin_check', $plugin_admin, 'bm_manual_checkin_check' );
		$this->loader->add_action( 'wp_ajax_manual_checkin_process', $plugin_admin, 'bm_manual_checkin_process' );
		$this->loader->add_action( 'wp_ajax_manual_checkin_view_details', $plugin_admin, 'bm_manual_checkin_view_details' );
		$this->loader->add_action( 'wp_ajax_view_pdf_content', $plugin_admin, 'bm_view_pdf_content' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_analytics_data', $plugin_admin, 'bm_fetch_analytics_data_callback' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_analytics_detail', $plugin_admin, 'bm_fetch_analytics_detail_callback' );
		$this->loader->add_action( 'wp_ajax_bm_download_analytics_csv', $plugin_admin, 'bm_download_analytics_csv_callback' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'bm_handle_pdf_test_downloads' );

		$this->loader->add_filter( 'flexibooking_fetch_order_transaction_data', $plugin_admin, 'bm_flexibooking_fetch_order_transaction_data', 10, 1 );
		$this->loader->add_filter( 'flexibooking_fetch_html_with_transaction_data', $plugin_admin, 'bm_flexibooking_fetch_html_with_transaction_data', 10, 1 );
		$this->loader->add_filter( 'flexibooking_save_order_transaction_data', $plugin_admin, 'bm_flexibooking_save_order_transaction_data', 10, 5 );
		$this->loader->add_action( 'flexibooking_save_existing_transaction_data_before_update', $plugin_admin, 'bm_flexibooking_save_existing_transaction_data_before_update', 10, 1 );
		$this->loader->add_filter( 'flexibooking_verify_if_valid_transaction_id', $plugin_admin, 'bm_flexibooking_verify_if_valid_transaction_id', 10, 3 );
		$this->loader->add_filter( 'flexibooking_verify_if_paid_transaction_id', $plugin_admin, 'bm_flexibooking_verify_if_paid_transaction_id', 10, 1 );
		$this->loader->add_filter( 'flexibooking_paid_transaction_statuses', $plugin_admin, 'bm_flexibooking_paid_transaction_statuses', 10, 1 );
		$this->loader->add_filter( 'flexibooking_verify_if_pending_transaction_id', $plugin_admin, 'bm_flexibooking_verify_if_pending_transaction_id', 10, 1 );
		$this->loader->add_filter( 'flexibooking_pending_transaction_statuses', $plugin_admin, 'bm_flexibooking_pending_transaction_statuses', 10, 1 );
		$this->loader->add_filter( 'flexibooking_verify_if_cancelled_transaction_id', $plugin_admin, 'bm_flexibooking_verify_if_cancelled_transaction_id', 10, 1 );
		$this->loader->add_filter( 'flexibooking_verify_transaction_for_free_payment_status', $plugin_admin, 'bm_flexibooking_verify_transaction_for_free_payment_status', 10, 1 );
		$this->loader->add_filter( 'flexibooking_verify_if_refunded_transaction_id', $plugin_admin, 'bm_flexibooking_verify_if_refunded_transaction_id', 10, 1 );
		$this->loader->add_filter( 'flexibooking_update_transaction_data', $plugin_admin, 'bm_flexibooking_update_transaction_data', 10, 2 );
		$this->loader->add_filter( 'flexibooking_update_booking_data_before_marking_transaction_failed', $plugin_admin, 'bm_flexibooking_update_booking_data_before_marking_transaction_failed', 10, 1 );
		$this->loader->add_filter( 'flexibooking_add_data_to_failed_transaction_table', $plugin_admin, 'bm_flexibooking_add_data_to_failed_transaction_table', 10, 2 );
		$this->loader->add_filter( 'flexibooking_update_booking_data_after_transaction_update', $plugin_admin, 'bm_flexibooking_update_booking_data_after_transaction_update', 10, 2 );
		$this->loader->add_filter( 'flexibooking_check_and_remove_duplicate_record_in_failed_transaction_table', $plugin_admin, 'bm_flexibooking_check_and_remove_duplicate_record_in_failed_transaction_table', 10, 1 );
		$this->loader->add_filter( 'flexibooking_revert_transaction_update', $plugin_admin, 'bm_flexibooking_revert_transaction_update', 10, 1 );
		$this->loader->add_action( 'wp_ajax_bm_fetch_export_order_modal_html', $plugin_admin, 'bm_fetch_export_order_modal_options_html' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_export_order_records', $plugin_admin, 'bm_fetch_export_order_records_as_per_type' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_discount_module_for_backend_order', $plugin_admin, 'bm_fetch_price_discount_module_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_bm_check_backend_discount', $plugin_admin, 'bm_fetch_age_data_and_check_backend_discount' );
		$this->loader->add_action( 'wp_ajax_bm_reset_backend_discount', $plugin_admin, 'bm_reset_backend_discounted_value' );
		$this->loader->add_action( 'wp_ajax_bm_check_if_exisiting_customer', $plugin_admin, 'bm_check_if_exisiting_customer' );
		$this->loader->add_action( 'woocommerce_admin_order_data_after_order_details', $plugin_admin, 'bm_display_service_date_in_admin', 10, 1 );
		$this->loader->add_action( 'before_delete_post', $plugin_admin, 'bm_remove_flexi_order_if_woocommerce_order_is_permanently_deleted' );
		$this->loader->add_action( 'wp_trash_post', $plugin_admin, 'bm_modify_flexi_plugin_order_on_woocommerce_order_trash', 10, 1 );
		$this->loader->add_action( 'untrash_post', $plugin_admin, 'bm_schedule_woocommerce_order_status_check_on_untrash', 10, 1 );
		$this->loader->add_action( 'bm_update_flexi_order_as_woocommerce_order_is_restored', $plugin_admin, 'bm_modify_flexi_plugin_order_on_woocommerce_order_untrash' );
		$this->loader->add_filter( 'woocommerce_hidden_order_itemmeta', $plugin_admin, 'bm_hide_flexi_order_itemmeta', 10, 1 );
		$this->loader->add_action( 'pre_post_update', $plugin_admin, 'bm_prevent_expired_woocommerce_order_updates', 10, 2 );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'bm_flexi_admin_notice' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_value_for_coupon_type', $plugin_admin, 'bm_fetch_value_for_coupon_type' );
		$this->loader->add_action( 'wp_ajax_bm_remove_coupon', $plugin_admin, 'bm_remove_coupon_function' );
		$this->loader->add_action( 'wp_ajax_get_states', $plugin_admin, 'bm_fetch_states_by_country' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
        $plugin_public = new Booking_Management_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'init', $plugin_public, 'bm_register_shortcodes' );
		$this->loader->add_action( 'wp_ajax_bm_flexi_set_frontend_lang', $plugin_public, 'bm_flexibooking_set_language' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_flexi_set_frontend_lang', $plugin_public, 'bm_flexibooking_set_language' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_all_services', $plugin_public, 'bm_fetch_all_services' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_all_services', $plugin_public, 'bm_fetch_all_services' );
		$this->loader->add_action( 'wp_ajax_bm_filter_services', $plugin_public, 'bm_filter_services' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_filter_services', $plugin_public, 'bm_filter_services' );
		$this->loader->add_action( 'wp_ajax_bm_filter_categories', $plugin_public, 'bm_filter_categories' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_filter_categories', $plugin_public, 'bm_filter_categories' );
		$this->loader->add_action( 'wp_ajax_bm_filter_service_by_category', $plugin_public, 'bm_filter_service_by_category' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_filter_service_by_category', $plugin_public, 'bm_filter_service_by_category' );
		$this->loader->add_action( 'wp_ajax_bm_filter_services_by_id', $plugin_public, 'bm_filter_services_by_service_id' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_filter_services_by_id', $plugin_public, 'bm_filter_services_by_service_id' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_frontend_service_time_slots', $plugin_public, 'bm_fetch_service_time_slots' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_frontend_service_time_slots', $plugin_public, 'bm_fetch_service_time_slots' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_calendar_time_slots', $plugin_public, 'bm_fetch_service_by_id_calendar_time_slots' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_service_calendar_time_slots', $plugin_public, 'bm_fetch_service_by_id_calendar_time_slots' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_extra_service', $plugin_public, 'bm_fetch_extra_service' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_extra_service', $plugin_public, 'bm_fetch_extra_service' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_user_form', $plugin_public, 'bm_fetch_user_form' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_user_form', $plugin_public, 'bm_fetch_user_form' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_order_info_and_redirect_to_checkout', $plugin_public, 'bm_fetch_order_info_and_redirect_to_checkout' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_order_info_and_redirect_to_checkout', $plugin_public, 'bm_fetch_order_info_and_redirect_to_checkout' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_booking_data', $plugin_public, 'bm_fetch_booking_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_booking_data', $plugin_public, 'bm_fetch_booking_data' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_selection', $plugin_public, 'bm_fetch_service_selection' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_service_selection', $plugin_public, 'bm_fetch_service_selection' );
		$this->loader->add_action( 'wp_ajax_bm_set_intl_input', $plugin_public, 'bm_set_intl_input' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_set_intl_input', $plugin_public, 'bm_set_intl_input' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_all_services_by_categories', $plugin_public, 'bm_fetch_all_services_by_categories' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_all_services_by_categories', $plugin_public, 'bm_fetch_all_services_by_categories' );
		$this->loader->add_action( 'wp_ajax_bm_get_frontend_service_prices', $plugin_public, 'bm_get_service_prices' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_get_frontend_service_prices', $plugin_public, 'bm_get_service_prices' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_services_by_name', $plugin_public, 'bm_fetch_services_by_name' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_services_by_name', $plugin_public, 'bm_fetch_services_by_name' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_gallry_images', $plugin_public, 'bm_fetch_service_gallry_images' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_service_gallry_images', $plugin_public, 'bm_fetch_service_gallry_images' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_checkout_data', $plugin_public, 'bm_fetch_checkout_data_redirect_to_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_checkout_data', $plugin_public, 'bm_fetch_checkout_data_redirect_to_payment' );
		$this->loader->add_action( 'wp_ajax_bm_free_checkout', $plugin_public, 'bm_discounted_and_free_checkout_save' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_free_checkout', $plugin_public, 'bm_discounted_and_free_checkout_save' );
		$this->loader->add_action( 'wp_ajax_bm_process_payment', $plugin_public, 'bm_process_final_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_process_payment', $plugin_public, 'bm_process_final_payment' );
		$this->loader->add_action( 'wp_ajax_bm_save_payment', $plugin_public, 'bm_save_final_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_save_payment', $plugin_public, 'bm_save_final_payment' );
		$this->loader->add_action( 'wp_ajax_bm_check_for_refund', $plugin_public, 'bm_check_for_refund_for_failed_payment' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_check_for_refund', $plugin_public, 'bm_check_for_refund_for_failed_payment' );
		$this->loader->add_action( 'wp_ajax_bm_check_session', $plugin_public, 'bm_check_if_payment_session_has_expired' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_check_session', $plugin_public, 'bm_check_if_payment_session_has_expired' );
		$this->loader->add_action( 'wp_ajax_bm_check_discount', $plugin_public, 'bm_fetch_age_data_and_check_discount' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_check_discount', $plugin_public, 'bm_fetch_age_data_and_check_discount' );
		$this->loader->add_action( 'wp_ajax_bm_reset_discount', $plugin_public, 'bm_reset_discounted_value' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_reset_discount', $plugin_public, 'bm_reset_discounted_value' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_checkout_options', $plugin_public, 'bm_fetch_available_checkout_options' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_checkout_options', $plugin_public, 'bm_fetch_available_checkout_options' );
		$this->loader->add_action( 'wp_ajax_fetch_woocommerce_states', $plugin_public, 'bm_get_woocommerce_states_by_country' );
		$this->loader->add_action( 'wp_ajax_nopriv_fetch_woocommerce_states', $plugin_public, 'bm_get_woocommerce_states_by_country' );
		$this->loader->add_action( 'wp_ajax_check_voucher_validity', $plugin_public, 'bm_check_if_valid_voucher' );
		$this->loader->add_action( 'wp_ajax_nopriv_check_voucher_validity', $plugin_public, 'bm_check_if_valid_voucher' );
		$this->loader->add_action( 'wp_ajax_fetch_available_timeslots', $plugin_public, 'bm_get_valid_available_voucher_timeslots' );
		$this->loader->add_action( 'wp_ajax_nopriv_fetch_available_timeslots', $plugin_public, 'bm_get_valid_available_voucher_timeslots' );
		$this->loader->add_action( 'wp_ajax_confirm_voucher_redemption', $plugin_public, 'bm_get_confirm_and_redeem_voucher' );
		$this->loader->add_action( 'wp_ajax_nopriv_confirm_voucher_redemption', $plugin_public, 'bm_get_confirm_and_redeem_voucher' );
		$this->loader->add_action( 'wp_ajax_get_states', $plugin_public, 'bm_fetch_states_by_country' );
		$this->loader->add_action( 'wp_ajax_nopriv_get_states', $plugin_public, 'bm_fetch_states_by_country' );
		$this->loader->add_action( 'wp_ajax_bm_filter_fullcalendar_events', $plugin_public, 'bm_filter_fullcalendar_events_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_filter_fullcalendar_events', $plugin_public, 'bm_filter_fullcalendar_events_callback' );
		$this->loader->add_action( 'wp_ajax_bm_filter_timeslot_fullcalendar_events', $plugin_public, 'bm_filter_timeslot_fullcalendar_events_callback' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_filter_timeslot_fullcalendar_events', $plugin_public, 'bm_filter_timeslot_fullcalendar_events_callback' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_timeslot_dialog_content', $plugin_public, 'bm_fetch_timeslot_dialog_content' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_timeslot_dialog_content', $plugin_public, 'bm_fetch_timeslot_dialog_content' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_bookable_services_by_category_id_and_date', $plugin_public, 'bm_fetch_bookable_services_by_category_id_and_date' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_bookable_services_by_category_id_and_date', $plugin_public, 'bm_fetch_bookable_services_by_category_id_and_date' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_new_order_service_time_slots', $plugin_public, 'bm_fetch_new_order_service_time_slots' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_new_order_service_time_slots', $plugin_public, 'bm_fetch_new_order_service_time_slots' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_mincap_and_cap_left', $plugin_public, 'bm_fetch_mincap_and_cap_left' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_mincap_and_cap_left', $plugin_public, 'bm_fetch_mincap_and_cap_left' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_price_for_backend_order', $plugin_public, 'bm_fetch_service_price_for_add_order' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_service_price_for_backend_order', $plugin_public, 'bm_fetch_service_price_for_add_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_service_extras_for_backend_order', $plugin_public, 'bm_fetch_service_extras_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_service_extras_for_backend_order', $plugin_public, 'bm_fetch_service_extras_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_discount_module_for_backend_order', $plugin_public, 'bm_fetch_price_discount_module_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_discount_module_for_backend_order', $plugin_public, 'bm_fetch_price_discount_module_for_backend_order' );
		$this->loader->add_action( 'wp_ajax_qr_checkin_process', $plugin_public, 'bm_qr_checkin_process' );
    	$this->loader->add_action( 'wp_ajax_nopriv_qr_checkin_process', $plugin_public, 'bm_qr_checkin_process' );
		$this->loader->add_action( 'wp_ajax_verify_qr_code', $plugin_public, 'bm_handle_qr_verification' );
		$this->loader->add_action( 'wp_ajax_nopriv_verify_qr_code', $plugin_public, 'bm_handle_qr_verification' );
		$this->loader->add_action( 'flexibooking_set_process_new_order', $plugin_public, 'bm_flexibooking_set_process_new_order_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_new_order', $plugin_public, 'bm_flexibooking_mail_on_new_order_callback', 10, 3 );
		$this->loader->add_action( 'flexibooking_set_process_voucher_redeem', $plugin_public, 'bm_flexibooking_set_process_voucher_redeem_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_voucher_redeem', $plugin_public, 'bm_flexibooking_mail_on_voucher_redeem_callback', 10, 3 );
		$this->loader->add_action( 'flexibooking_set_process_new_request', $plugin_public, 'bm_flexibooking_set_process_new_request_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_new_request', $plugin_public, 'bm_flexibooking_mail_new_request_callback', 10, 3 );
		$this->loader->add_action( 'flexibooking_set_process_new_order_voucher', $plugin_public, 'bm_flexibooking_set_process_new_order_voucher_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_voucher_mail_new_order', $plugin_public, 'bm_flexibooking_voucher_mail_new_order_callback', 10, 3 );
		$this->loader->add_action( 'flexibooking_set_process_failed_order_refund', $plugin_public, 'bm_flexibooking_set_process_failed_order_refund_callback', 10, 1 );
		$this->loader->add_action( 'flexibooking_mail_failed_order_refund', $plugin_public, 'bm_flexibooking_mail_on_failed_order_refund_callback', 10, 3 );
		$this->loader->add_filter( 'flexibooking_google_analytics_data', $plugin_public, 'bm_prepare_ga_purchase_data', 10, 1 );
		$this->loader->add_filter( 'woocommerce_checkout_get_value', $plugin_public, 'bm_set_checkout_form_value', 10, 2 );
		$this->loader->add_filter( 'woocommerce_cart_item_quantity', $plugin_public, 'bm_disable_quantity_change_for_plugin_products', 10, 3 );
		$this->loader->add_filter( 'woocommerce_cart_item_remove_link', $plugin_public, 'bm_disable_remove_link_for_plugin_products', 10, 2 );
		$this->loader->add_filter( 'woocommerce_email_attachments', $plugin_public, 'bm_add_custom_attachments_to_woocommerce_email', 99, 3 );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'bm_save_flexibooking_order_keys_to_order_items', 10, 4 );
		$this->loader->add_action( 'woocommerce_order_status_processing', $plugin_public, 'bm_save_woocommerce_booking_data', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_cancelled', $plugin_public, 'bm_update_flexi_booking_data_on_order_cancellation', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_refunded', $plugin_public, 'bm_update_flexi_booking_data_on_order_refund', 10, 1 );
		$this->loader->add_action( 'woocommerce_order_status_on-hold', $plugin_public, 'bm_update_flexi_booking_data_on_order_on_hold', 10, 1 );
		$this->loader->add_filter( 'woocommerce_add_to_cart_validation', $plugin_public, 'bm_restrict_adding_products_if_added_through_flexi_plugin', 10, 3 );
		$this->loader->add_action( 'woocommerce_order_status_completed', $plugin_public, 'bm_set_flexibooking_order_as_completed', 10, 1 );
		$this->loader->add_action( 'woocommerce_email_before_order_table', $plugin_public, 'bm_add_service_date_to_email', 20, 4 );
		$this->loader->add_filter( 'woocommerce_thankyou_order_received_text', $plugin_public, 'bm_display_service_date_in_thank_you_page', 20, 2 );
		$this->loader->add_action( 'woocommerce_order_details_before_order_table', $plugin_public, 'bm_display_service_date_in_view_order', 20 );
		$this->loader->add_action( 'woocommerce_before_calculate_totals', $plugin_public, 'bm_adjust_cart_item_prices', 10, 1 );
		$this->loader->add_action( 'woocommerce_cart_emptied', $plugin_public, 'bm_clear_flexi_custom_order_keys' );
		$this->loader->add_action( 'woocommerce_before_checkout_billing_form', $plugin_public, 'bm_add_gift_fields_to_woocommerce_checkout' );
		$this->loader->add_action( 'woocommerce_checkout_process', $plugin_public, 'bm_validate_woocommerce_gift_fields' );
		$this->loader->add_action( 'woocommerce_checkout_update_order_meta', $plugin_public, 'bm_save_gift_fields_to_woocommerce_order_meta' );
		$this->loader->add_filter( 'woocommerce_available_payment_gateways', $plugin_public, 'bm_restrict_cod_for_woocommerce_gift_orders' );
		$this->loader->add_filter( 'img_caption_shortcode', $plugin_public, 'bm_custom_img_caption_shortcode', 10, 3 );
		$this->loader->add_filter( 'the_title', $plugin_public, 'bm_hide_specific_page_title', 10, 2 );
		$this->loader->add_filter( 'body_class', $plugin_public, 'flexibooking_add_checkout_body_class_to_woocommerce_checkout' );
		/**$this->loader->add_action( 'woocommerce_payment_complete', $plugin_public, 'bm_mark_flexi_orders_paid', 10, 1 );
		$this->loader->add_filter( 'woocommerce_is_sold_individually', $plugin_public, 'bm_disable_quantity_for_plugin_added_products', 10, 2 )
		$this->loader->add_action('woocommerce_thankyou', $plugin_public, 'bm_redirect_after_order', 10, 1);*/
		$this->loader->add_action( 'wp_ajax_validate_coupon', $plugin_public, 'bm_validate_coupon_code' );
		$this->loader->add_action( 'wp_ajax_nopriv_validate_coupon', $plugin_public, 'bm_validate_coupon_code' );
		$this->loader->add_action( 'wp_ajax_reset_coupon_data', $plugin_public, 'bm_reset_coupon_data' );
		$this->loader->add_action( 'wp_ajax_nopriv_reset_coupon_data', $plugin_public, 'bm_reset_coupon_data' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_current_day_coupon_list', $plugin_public, 'bm_fetch_current_day_coupon_list' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_current_day_coupon_list', $plugin_public, 'bm_fetch_current_day_coupon_list' );
		$this->loader->add_action( 'wp_ajax_bm_fetch_auto_apply_coupon', $plugin_public, 'bm_fetch_auto_apply_coupon' );
		$this->loader->add_action( 'wp_ajax_nopriv_bm_fetch_auto_apply_coupon', $plugin_public, 'bm_fetch_auto_apply_coupon' );
		$this->loader->add_action( 'wp_ajax_coupon_removal', $plugin_public, 'bm_coupon_removal' );
		$this->loader->add_action( 'wp_ajax_nopriv_coupon_removal', $plugin_public, 'bm_coupon_removal' );
		$this->loader->add_action( 'bm_after_booking_saved', $plugin_public, 'bm_after_booking_saved_callback', 10, 2 );
		$this->loader->add_filter( 'woocommerce_get_shop_coupon_data', $plugin_public, 'bm_apply_flexi_cpn_woo', 10, 2 );
		$this->loader->add_filter( 'woocommerce_coupon_is_valid', $plugin_public, 'bm_validate_woo_checkout_cpn', 10, 3 );
		$this->loader->add_action( 'woocommerce_applied_coupon', $plugin_public, 'bm_update_list_cpn' );
        $this->loader->add_action( 'woocommerce_removed_coupon', $plugin_public, 'bm_remove_list_cpn' );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'bm_update_coupon_woo_checkout', 10, 1 );
		$this->loader->add_action( 'woocommerce_thankyou', $plugin_public, 'bm_clear_woo_coupons_after_checkout', 20, 1 );
		$this->loader->add_action( 'woocommerce_before_cart', $plugin_public, 'bm_refresh_cart_after_checkout' );
		$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'bm_refresh_cart_on_woo', 999 );
		$this->loader->add_action( 'woocommerce_cart_emptied', $plugin_public, 'bm_refresh_cart_on_woo', 999 );
		$this->loader->add_action( 'woocommerce_after_calculate_totals', $plugin_public, 'bm_update_discount_transient', 10, 1 );
	}

	/**
	 * Establish smtp connection
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_smtp_connection() {
		$dbhandler   = new BM_DBhandler();
		$plugin_smtp = new Booking_Management_SMTP( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'phpmailer_init', $plugin_smtp, 'bm_mail_connection' );
	}


	/**
	 * Establish stripes connection
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function enable_stripes_connection() {
		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$public_code  = $dbhandler->get_global_option_value( 'bm_flexi_stripe_public_code' );
		$private_code = $dbhandler->get_global_option_value( 'bm_flexi_stripe_private_code' );

		if ( !empty( $public_code ) & !empty( $private_code ) && ( $dbhandler->get_global_option_value( 'bm_enable_stripe', 0 ) == 1 ) ) {
			if ( ! defined( 'STRIPE_SECRET_KEY' ) ) {
				define( 'STRIPE_SECRET_KEY', $bmrequests->decrypt_key( $private_code, 'flexibooking_private_stripe_code' ) );
			}

			if ( ! defined( 'STRIPE_PUBLISHABLE_KEY' ) ) {
				define( 'STRIPE_PUBLISHABLE_KEY', $bmrequests->decrypt_key( $public_code, 'flexibooking_public_stripe_code' ) );
			}
		} else {
			if ( defined( 'STRIPE_SECRET_KEY' ) ) {
				runkit7_constant_remove( 'STRIPE_SECRET_KEY' );
			}

			if ( defined( 'STRIPE_PUBLISHABLE_KEY' ) ) {
				runkit7_constant_remove( 'STRIPE_PUBLISHABLE_KEY' );
			}

			delete_option( 'bm_flexi_stripe_public_code' );
			delete_option( 'bm_flexi_stripe_private_code' );
		}
	}

	/**
	 * Initializes the Booking API by creating a new instance of the Booking_API class.
	 * This method is intended to set up API-related functionality for booking management.
	 *
	 * @access private
	 */
	private function init_api() {
		new Booking_API( $this->get_plugin_name(), $this->get_version() );
	}


	private function init_react_shortcodes() {
		new React_Shortcodes_Plugin();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
         $this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
         return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Booking_Management_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
         return $this->version;
	}
}
