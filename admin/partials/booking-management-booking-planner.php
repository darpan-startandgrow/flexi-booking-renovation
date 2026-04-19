<?php
/**
 * Modern Booking Planner – Admin Page Template
 *
 * Renders a minimal root container. All UI is dynamically generated via JavaScript.
 * This is the entry point for the SPA-like planner experience.
 *
 * Supports sub-views via the `view` GET parameter:
 *   page=bm_booking_planner             → Home (choose planner type)
 *   page=bm_booking_planner&view=service-planner → Service Planner
 *   page=bm_booking_planner&view=time-planner    → Time Planner
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin/partials
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$initial_view = '';
if ( isset( $_GET['view'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$view_param = sanitize_text_field( wp_unslash( $_GET['view'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( in_array( $view_param, array( 'service-planner', 'time-planner' ), true ) ) {
		$initial_view = $view_param;
	}
}
?>
<div id="bm-planner-root"
	class="bm-planner-app"
	data-nonce="<?php echo esc_attr( wp_create_nonce( 'wp_rest' ) ); ?>"
	data-rest-url="<?php echo esc_url( rest_url( 'booking-planner/v1' ) ); ?>"
	data-initial-view="<?php echo esc_attr( $initial_view ); ?>">
	<div class="bm-planner-loading">
		<div class="bm-planner-spinner"></div>
		<span><?php esc_html_e( 'Loading Planner…', 'service-booking' ); ?></span>
	</div>
</div>
