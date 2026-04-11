<?php
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

class Booking_Management_Feature_Control {

    public static function is_pro() {
        // This checks if the Pro version is active.
        // You would define this constant in your Pro Add-on plugin.
        return defined( 'BM_PRO_VERSION_ACTIVE' ) && BM_PRO_VERSION_ACTIVE;
    }

    public function init() {
        if ( !self::is_pro() ) {
            $this->apply_free_restrictions();
        }
    }

    private function apply_free_restrictions() {
        // 1. Limit Services to 20
        add_filter( 'bm_can_add_service', array( $this, 'bm_check_service_limit' ) );

        // 2. Restrict to WooCommerce Payments Only
        add_filter( 'bm_allow_custom_gateway', '__return_false' );

        // 3. Disable Bulk Updates
        add_filter( 'bm_allow_bulk_calendar_update', '__return_false' );

        // 4. Disable "Book on Request"
        add_filter( 'bm_allow_book_on_request', '__return_false' );

        // 5. Disable Email Automation
        add_filter( 'bm_allow_email_automation', '__return_false' );

        // 6. Restrict Order Editing
        add_filter( 'bm_allow_backend_order_edit', '__return_false' );

        // 7. Limit Categories (Single vs Multiple)
        add_filter( 'bm_allow_multiple_categories', '__return_false' );

        // 8. Filter Settings Tabs
        add_filter( 'bm_settings_tabs', array( $this, 'bm_filter_free_tabs' ) );
    }

    public function bm_check_service_limit( $can_add ) {
        $count = ( new BM_DBhandler() )->bm_count( 'SERVICE' );
        return ( $count < 20 );
    }

    public function bm_filter_free_tabs( $tabs ) {
        // Define which tabs are allowed in Free
        $allowed = array( 'general', 'format', 'timezone', 'fields' );
        return array_intersect_key( $tabs, array_flip( $allowed ) );
    }
}

// Initialize the controller
$bm_feature_control = new Booking_Management_Feature_Control();
$bm_feature_control->init();
