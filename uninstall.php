<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package Booking_Management
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Clean up plugin data on uninstall.
 *
 * Removes all plugin options and transients. Database tables
 * are intentionally preserved to prevent accidental data loss.
 * Use the 'bm_uninstall_drop_tables' filter to opt-in to table removal.
 */

// Remove plugin options.
$bm_options = array(
	'bm_frontend_book_button_color',
	'bm_flexi_stripe_public_code',
	'bm_booking_time_zone',
	'bm_booking_currency',
	'bm_flexi_current_language',
	'bm_flexi_current_locale',
	'bm_show_frontend_service_image',
	'bm_show_frontend_service_desc_read_more_button',
	'bm_show_frontend_service_price',
	'bm_show_frontend_service_duration',
	'bm_show_frontend_service_description',
	'bm_frontend_service_title_color',
	'bm_frontend_service_price_text_color',
	'bm_service_title_font',
	'bm_service_shrt_desc_font',
	'bm_service_price_txt_font',
	'bm_frontend_book_button_txt_color',
	'bm_payment_session_time',
	'bm_auto_apply_limit',
	'bm_inactive_coupons',
	'bm_flexi_stripe_secret_code',
);

foreach ( $bm_options as $option ) {
	delete_option( $option );
}

// Clean up transients matching the plugin prefix via the DB handler so that
// $wpdb never appears outside the DB handler class.
require_once plugin_dir_path( __FILE__ ) . 'includes/class-booking-management-activator.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-booking-management-dbhandler.php';
( new BM_DBhandler() )->delete_transients_by_prefix( 'FLEXI' );

/**
 * Allow add-ons to perform their own cleanup.
 *
 * @since 1.0.0
 */
do_action( 'bm_plugin_uninstall' );
