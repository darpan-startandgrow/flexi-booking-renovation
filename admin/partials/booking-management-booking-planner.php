<?php
/**
 * Modern Booking Planner – Admin Page Template
 *
 * Renders a minimal root container. All UI is dynamically generated via JavaScript.
 * This is the entry point for the SPA-like planner experience.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin/partials
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div id="bm-planner-root"
	class="bm-planner-app"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>"
	data-rest-url="<?php echo esc_url( rest_url( 'booking-planner/v1' ) ); ?>">
	<div class="bm-planner-loading">
		<div class="bm-planner-spinner"></div>
		<span><?php esc_html_e( 'Loading Planner…', 'service-booking' ); ?></span>
	</div>
</div>
