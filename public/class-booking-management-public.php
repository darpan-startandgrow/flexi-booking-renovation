<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/public
 * @author     Start and Grow <laravel6@startandgrow.in>
 */
class Booking_Management_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    string    $version    The current version of this plugin.
	 */
	private $version;

	protected static $counter;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $counter = 0 ) {
			$this->plugin_name = $plugin_name;
		$this->version         = $version;
		self::$counter         = $counter;
	}//end __construct()


	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
			global $post;
		$post_id = isset( $post->ID ) ? $post->ID : 0;
		$pid     = filter_input( INPUT_GET, 'pid' );

		if ( $pid == false || $pid == null ) {
			$pid = 0;
		}

		/*
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Booking_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Booking_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( 'jquery-ui-styles' );
		wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/booking-management-jquery-ui.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'jquery-ui-smoothness', plugin_dir_url( __FILE__ ) . 'css/smoothness-jquery-ui.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'googleFonts', plugin_dir_url( __FILE__ ) . 'css/googleFonts.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'material-icon', plugin_dir_url( __FILE__ ) . 'css/material-icons.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'googleFonts-1', plugin_dir_url( __FILE__ ) . 'css/googleFonts-1.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'googleSwap', plugin_dir_url( __FILE__ ) . 'css/googleSwap.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'slick-carousel', plugin_dir_url( __FILE__ ) . 'css/booking-management-slick-carousel.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'slick-theme', plugin_dir_url( __FILE__ ) . 'css/booking-management-slick-theme.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'multiselect', plugin_dir_url( __FILE__ ) . 'css/booking-management-multiselect.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ui-dialog-custom', plugin_dir_url( __FILE__ ) . 'css/booking-management-ui-dialog-custom.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'ui-tooltip', plugin_dir_url( __FILE__ ) . 'css/booking-management-tooltip.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'intl-tel-input', plugin_dir_url( __FILE__ ) . 'css/booking-management-intl-tel-input.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'gallery-images', plugin_dir_url( __FILE__ ) . 'css/booking-management-gallery-images.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'flexi-animate', plugin_dir_url( __FILE__ ) . 'css/booking-management-animate.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'flexi-daterangepicker', plugin_dir_url( __FILE__ ) . 'css/booking-management-daterangepicker.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'flexi-fullcalendar', plugin_dir_url( __FILE__ ) . 'css/booking-management-fullcalendar.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'flexi-timeslot-fullcalendar', plugin_dir_url( __FILE__ ) . 'css/booking-management-timeslot-fullcalendar.css', array(), $this->version, 'all' );
		wp_enqueue_style( 'flexi-cropper', plugin_dir_url( __FILE__ ) . 'css/booking-management-cropper.css', array(), $this->version, 'all' );

		if ( ! empty( $post_id ) ) {
			$original_title = get_the_title( $post_id );
			global $sitepress;
			if ( isset( $sitepress ) ) {
				$original_id = apply_filters( 'wpml_object_id', $post_id, 'page', true, $sitepress->get_default_language() );
				$wpml_title  = get_the_title( $original_id );
				if ( ! empty( $wpml_title ) ) {
					$original_title = $wpml_title;
				}
			}
			if ( $original_title == 'Flexibooking Checkout' ) {
				if ( empty( $pid ) ) {
					wp_enqueue_style( 'flexi-payment', plugin_dir_url( __FILE__ ) . 'css/booking-management-payment.css', array(), $this->version, 'all' );
					wp_enqueue_style( 'flexi-checkout-coupon', plugin_dir_url( __FILE__ ) . 'css/booking-management-coupon.css', array(), $this->version, 'all' );
				} else {
					wp_enqueue_style( 'flexi-payment-final-page', plugin_dir_url( __FILE__ ) . 'css/booking-management-booking-final-page.css', array(), $this->version, 'all' );
				}
			}

			if ( $original_title == 'Flexibooking Voucher Redeem' ) {
				wp_enqueue_style( 'flexi-redeem-voucher', plugin_dir_url( __FILE__ ) . 'css/booking-management-redeem-voucher.css', array(), $this->version, 'all' );

				wp_enqueue_style( 'flexi-payment-final-page', plugin_dir_url( __FILE__ ) . 'css/booking-management-booking-final-page.css', array(), $this->version, 'all' );
			}
		}

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/booking-management-public.css', array(), $this->version, 'all' );
	}//end enqueue_styles()


	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
			global $post;
		$post_id                    = isset( $post->ID ) ? $post->ID : 0;
		$dbhandler                  = new BM_DBhandler();
		$bmrequests                 = new BM_Request();
		$check_svc_search_shortcode = $bmrequests->bm_find_shortcode_occurences_per_page( 'sgbm_service_search', $post_id );
		$check_svc_by_cat_shortcode = $bmrequests->bm_find_shortcode_occurences_per_page( 'sgbm_service_by_category', $post_id );
		$check_single_svc_shortcode = $bmrequests->bm_find_shortcode_occurences_per_page( 'sgbm_single_service', $post_id );
		$check_svc_clndar_shortcode = $bmrequests->bm_find_shortcode_occurences_per_page( 'sgbm_single_service_calendar', $post_id );
		$payment_session_timer      = $dbhandler->get_global_option_value( 'bm_payment_session_time', '2' );
		$is_svc_search_shortcode    = isset( $check_svc_search_shortcode['post_id'] ) && $check_svc_search_shortcode['post_id'] == $post_id ? 1 : 0;
		$is_svc_by_cat_shortcode    = isset( $check_svc_by_cat_shortcode['post_id'] ) && $check_svc_by_cat_shortcode['post_id'] == $post_id ? 1 : 0;
		$is_single_svc_shortcode    = isset( $check_single_svc_shortcode['post_id'] ) && $check_single_svc_shortcode['post_id'] == $post_id ? 1 : 0;
		$is_svc_calendar_shortcode  = isset( $check_svc_clndar_shortcode['post_id'] ) && $check_svc_clndar_shortcode['post_id'] == $post_id ? 1 : 0;

		$card_options = array(
			'style'          => array(
				'base'    => array(
					'color'         => '#32325d',
					'fontFamily'    => '"Helvetica Neue", Helvetica, sans-serif',
					'fontSmoothing' => 'antialiased',
					'fontSize'      => '16px',
					'::placeholder' => array(
						'color' => '#aab7c4',
					),
				),
				'invalid' => array(
					'color'     => '#fa755a',
					'iconColor' => '#fa755a',
				),
			),
			'hidePostalCode' => true,
		);

		/*
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Booking_Management_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Booking_Management_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_Script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-form' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_media();

		wp_enqueue_script( 'slick', plugin_dir_url( __FILE__ ) . 'js/booking-management-slick.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'multiselect', plugin_dir_url( __FILE__ ) . 'js/booking-management-multiselect.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-ui.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'intl-tel-input', plugin_dir_url( __FILE__ ) . 'js/booking-management-intl-tel-input.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'slider', plugin_dir_url( __FILE__ ) . 'js/booking-management-slider.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( 'jquery-datepicker-i18n', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datepicker-i18n.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'jquery-moment', plugin_dir_url( __FILE__ ) . 'js/booking-management-momentjs.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'jquery-fullcalendar', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-fullcalendar.js', array( 'jquery', 'jquery-moment' ), $this->version, true );
		wp_enqueue_script( 'fullcalendar-moment', plugin_dir_url( __FILE__ ) . 'js/booking-management-fullcalendar-moment.js', array( 'jquery', 'jquery-fullcalendar', 'jquery-moment' ), $this->version, true );
		wp_enqueue_script( 'jquery-daterangepicker', plugin_dir_url( __FILE__ ) . 'js/booking-management-daterangepicker.js', array( 'jquery', 'jquery-fullcalendar', 'fullcalendar-moment', 'jquery-moment' ), $this->version, true );
		wp_enqueue_script( 'jquery-fullcalendar-custom', plugin_dir_url( __FILE__ ) . 'js/booking-management-fullcalendar-custom.js', array( 'jquery', 'jquery-moment', 'fullcalendar-moment', 'jquery-fullcalendar', 'jquery-daterangepicker' ), $this->version, true );
		wp_enqueue_script( 'jquery-timeslot-fullcalendar-custom', plugin_dir_url( __FILE__ ) . 'js/booking-management-timeslot-fullcalendar-custom.js', array( 'jquery', 'jquery-moment', 'fullcalendar-moment', 'jquery-fullcalendar', 'jquery-daterangepicker' ), $this->version, true );
		wp_enqueue_script( 'jquery-cropper', plugin_dir_url( __FILE__ ) . 'js/booking-management-cropper.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'jquery-pdf-cropper', plugin_dir_url( __FILE__ ) . 'js/booking-management-pdf-cropper.js', array( 'jquery' ), $this->version, true );

		if ( $dbhandler->get_global_option_value( 'bm_enable_stripe', 0 ) == 1 ) {
			wp_enqueue_script( 'stripes', 'https://js.stripe.com/v3/', array(), $this->version, false );
		}

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/booking-management-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( 'frontend-add-order-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-add-order.js', array( 'jquery' ), $this->version, true );
		$original_title = get_the_title( $post_id );
		global $sitepress;
		if ( isset( $sitepress ) ) {
			$original_id    = apply_filters( 'wpml_object_id', $post_id, 'page', true, $sitepress->get_default_language() );
			$original_title = get_the_title( $original_id );
		}

		if ( ! empty( $post_id ) && $original_title == 'Flexibooking Checkout' ) {
			wp_enqueue_script( 'stripes_checkout', plugin_dir_url( __FILE__ ) . 'js/booking-management-stripes-payment.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'coupon_checkout', plugin_dir_url( __FILE__ ) . 'js/booking-management-public-coupon.js', array( 'jquery' ), $this->version, true );
		}

		wp_deregister_script( 'wc-checkout-block' );
		wp_enqueue_script( 'custom-wc-checkout', plugin_dir_url( __FILE__ ) . 'js/booking-management-woo-coupon.js', array( 'wp-element', 'wp-i18n', 'wp-hooks', 'wp-data', 'wc-settings' ), time(), true );

		if ( ! empty( $post_id ) && $original_title == 'Flexibooking Voucher Redeem' ) {
			wp_enqueue_script( 'voucher-redeem', plugin_dir_url( __FILE__ ) . 'js/booking-management-redeem-voucher.js', array( 'jquery' ), $this->version, true );
		}

		$error   = array();
		$success = array();
		$normal  = array();

		$error['required']                = __( 'This field is required.', 'service-booking' );
		$error['is_required']             = __( ' is required.', 'service-booking' );
		$error['fill_up_age_fields']      = __( 'Please fill up all the required fields to calculate discount.', 'service-booking' );
		$error['invalid_total']           = __( 'Invalid value of total.', 'service-booking' );
		$error['term_co']                 = __( 'Terms and conditions must be accepted.', 'service-booking' );
		$error['invalid_email']           = __( 'Please enter a valid email.', 'service-booking' );
		$error['invalid_contact']         = __( 'Please enter a valid phone no.', 'service-booking' );
		$error['invalid_url']             = __( 'Please enter a valid URL.', 'service-booking' );
		$error['invalid_password']        = __( 'Please enter a valid password.', 'service-booking' );
		$error['server_error']            = __( 'Something is wrong, try again.', 'service-booking' );
		$error['service_unavailable']     = __( 'Service Unavailable on selected Date.', 'service-booking' );
		$error['invalid_url']             = __( 'Please enter a valid URL.', 'service-booking' );
		$error['invalid_password']        = __( 'Please enter a valid password.', 'service-booking' );
		$error['server_error']            = __( 'Something is wrong, try again.', 'service-booking' );
		$error['payment_error']           = __( 'Your payment was not successful, please try again.', 'service-booking' );
		$error['payment_imcomplete']      = __( 'Your payment was not complete, please try again.', 'service-booking' );
		$error['unexpected_error']        = __( 'An unexpected error occured.', 'service-booking' );
		$error['excess_order_total']      = __( 'The total number exceeds the number of people for the ordered service.', 'service-booking' );
		$error['voucher_expired']         = __( 'This voucher is expired.', 'service-booking' );
		$error['voucher_not_valid']       = __( 'This voucher is invalid.', 'service-booking' );
		$error['discount_not_applicable'] = __( 'Discount not applicable.', 'service-booking' );
		$error['no_services_text']        = __( 'No services found', 'service-booking' );
		$error['required_field']          = __( 'This is a required field.', 'service-booking' );
		$error['required']                = __( 'Required', 'service-booking' );
		$error['invalid_contact']         = __( 'Please enter a valid phone no.', 'service-booking' );
		$error['invalid_email']           = __( 'Please enter a valid email.', 'service-booking' );

		$success['checked_in_successfully'] = __( 'Checked in successfully.', 'service-booking' );
		$success['save_success']            = __( 'Saved successfully.', 'service-booking' );
		$success['coupon_applied']          = __( 'Coupon Applied Successfully, You got a discount of ', 'service-booking' );

		$normal['choose_category']         = __( 'Select a category', 'service-booking' );
		$normal['filter_service']          = __( 'Service', 'service-booking' );
		$normal['filter_category']         = __( 'Category', 'service-booking' );
		$normal['svc_full_desc']           = __( 'Full Description', 'service-booking' );
		$normal['category_selected']       = __( ' categories selected', 'service-booking' );
		$normal['session_timer']           = $payment_session_timer * 60;
		$normal['session_ends_in']         = __( 'Session ends in: ', 'service-booking' );
		$normal['session_expired']         = __( 'Session expired! ', 'service-booking' );
		$normal['payment_processing']      = __( 'Your payment is processing.', 'service-booking' );
		$normal['pay']                     = __( 'Pay  ', 'service-booking' );
		$normal['free_book']               = __( 'Free Booking', 'service-booking' );
		$normal['search_here']             = __( 'Search here', 'service-booking' );
		$normal['select_checkout_type']    = __( 'Select checkout type', 'service-booking' );
		$normal['woocommerce_checkout']    = __( 'WooCommerce Checkout', 'service-booking' );
		$normal['flexi_checkout']          = __( 'Flexi Checkout', 'service-booking' );
		$normal['no_states_available']     = __( 'No States Available', 'service-booking' );
		$normal['enter_state']             = __( 'enter state', 'service-booking' );
		$normal['enter_voucher_code']      = __( 'enter voucher code', 'service-booking' );
		$normal['date_required']           = __( 'please select date', 'service-booking' );
		$normal['slot_required']           = __( 'please select a slot', 'service-booking' );
		$normal['recipient_data_required'] = __( 'please insert recipient data', 'service-booking' );
		$normal['moving_to_checkout']      = __( 'Moving to Checkout....', 'service-booking' );
		$normal['services_text']           = __( 'Services', 'service-booking' );
		$normal['click_to_book']           = __( 'Click to book', 'service-booking' );
		$normal['show_slots']              = __( 'Show slots', 'service-booking' );
		$normal['book']                    = __( 'Book', 'service-booking' );
		$normal['reservation_list']        = __( 'Reservation List', 'service-booking' );
		$normal['more_info']               = __( 'More Info', 'service-booking' );
		$normal['minimum']                 = __( 'Minimum', 'service-booking' );
		$normal['capacity']                = __( 'Capacity', 'service-booking' );
		$normal['proceed']                 = __( 'Proceed', 'service-booking' );
		$normal['cancel']                  = __( 'Cancel', 'service-booking' );
		$normal['select_no_of_people']     = __( 'Select Number of People', 'service-booking' );
		$normal['select_to_proceed']       = __( 'Select to Proceed', 'service-booking' );
		$normal['confirm_selection']       = __( 'Confirm Selection', 'service-booking' );
		$normal['show_more']               = __( 'Show More', 'service-booking' );
		$normal['loading']                 = __( 'Loading...', 'service-booking' );
		$normal['service_discount_text']   = __( 'Service discount is ', 'service-booking' );
		$normal['select_service']          = __( 'Select Service', 'service-booking' );
		$normal['select_slot']             = __( 'select slot', 'service-booking' );
		$normal['price']                   = __( 'price', 'service-booking' );
		$normal['select_persons']          = __( 'select no of persons', 'service-booking' );
		$normal['total_price']             = __( 'Total Price', 'service-booking' );
		$normal['in']                      = __( 'in', 'service-booking' );
		$normal['add']                     = __( 'Add', 'service-booking' );
		$normal['quantity']                = __( 'Quantity', 'service-booking' );
		$normal['cap_left']                = __( 'Cap Left', 'service-booking' );
		$normal['add_option']              = __( 'Add Option', 'service-booking' );
		$normal['name']                    = __( 'name', 'service-booking' );
		$normal['from']                    = __( 'from', 'service-booking' );
		$normal['qr_code_detected']        = __( 'QR Code Detected', 'service-booking' );
		$normal['payment_method_refresh']  = __( 'Your payment method was declined. Please update your card details and try again.', 'service-booking' );
		$normal['payment_failed']          = __( 'Payment failed after multiple attempts. Please contact support.', 'service-booking' );
		$normal['select_all']              = __( 'Select All.', 'service-booking' );

		$normal['currency_position']         = $dbhandler->get_global_option_value( 'bm_currency_position', 'before' );
		$normal['currency_symbol']           = $bmrequests->bm_get_currency_char( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
		$normal['currency_type']             = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
		$normal['booking_country']           = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
		$normal['shortcode_occurances']      = self::$counter;
		$normal['is_svc_search_shortcode']   = $is_svc_search_shortcode;
		$normal['is_svc_by_cat_shortcode']   = $is_svc_by_cat_shortcode;
		$normal['is_single_svc_shortcode']   = $is_single_svc_shortcode;
		$normal['is_svc_calendar_shortcode'] = $is_svc_calendar_shortcode;
		$normal['flexi_public_key']          = defined( 'STRIPE_PUBLISHABLE_KEY' ) ? STRIPE_PUBLISHABLE_KEY : '';
		$normal['flexi_card_options']        = wp_json_encode( $card_options );
		$normal['plugin_directory']          = plugin_dir_path( __DIR__ );
		$normal['current_page_title']        = get_the_title( $post_id );
		$normal['current_language']          = isset( $_COOKIE['bm_flexibooking_language'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['bm_flexibooking_language'] ) ) : esc_html( 'en' );
		$normal['price_format']              = $dbhandler->get_global_option_value( 'bm_flexi_service_price_format', 'de_DE' );
		$normal['ajax_image_loader']         = esc_url( plugin_dir_url( __FILE__ ) . 'partials/images/ajax-loader.gif' );
		$primary_color                       = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
		$contrast                            = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
		$normal['svc_button_colour']         = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
		$normal['svc_btn_txt_colour']        = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );
		$normal['svc_info_svg_icon']         = esc_url( plugin_dir_url( __DIR__ ) . 'public/img/si_info-line.svg' );

		wp_localize_script( $this->plugin_name, 'bm_error_object', $error );
		wp_localize_script( $this->plugin_name, 'bm_success_object', $success );
		wp_localize_script( $this->plugin_name, 'bm_normal_object', $normal );
		wp_localize_script( 'stripes_checkout', 'bm_error_object', $error );
		wp_localize_script( 'stripes_checkout', 'bm_normal_object', $normal );
		wp_localize_script( 'frontend-add-order-js', 'bm_error_object', $error );
		wp_localize_script( 'frontend-add-order-js', 'bm_normal_object', $normal );
		wp_localize_script( 'jquery-fullcalendar-custom', 'bm_error_object', $error );
		wp_localize_script( 'jquery-fullcalendar-custom', 'bm_normal_object', $normal );

		wp_localize_script(
			$this->plugin_name,
			'bm_intl_script',
			array(
				'script_url' => plugin_dir_url( __FILE__ ) . 'js/booking-management-intl-tel-input.js',
			)
		);

		wp_localize_script(
			$this->plugin_name,
			'bm_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			)
		);

		wp_localize_script(
			'stripes_checkout',
			'bm_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			)
		);

		wp_localize_script(
			'jquery-fullcalendar-custom',
			'bm_ajax_object',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => wp_create_nonce( 'ajax-nonce' ),
			)
		);
	}//end enqueue_scripts()


	/**
	 * Register Shortcodes
	 *
	 * @author Darpan
	 */
	public function bm_register_shortcodes() {
		$bmrequests = new BM_Request();
		$trp_lang   = $bmrequests->bm_get_current_trp_language();
		$old_locale = $bmrequests->bm_switch_locale_by_booking_reference( '', $trp_lang );
		add_shortcode( 'sgbm_service_search', array( $this, 'bm_search_services' ) );
		add_shortcode( 'sgbm_service_fullcalendar', array( $this, 'bm_service_fullcalendar' ) );
		add_shortcode( 'sgbm_service_timeslot_fullcalendar', array( $this, 'bm_service_timeslot_fullcalendar' ) );
		add_shortcode( 'sgbm_service_by_category', array( $this, 'bm_service_by_category' ) );
		add_shortcode( 'sgbm_single_service', array( $this, 'bm_service_by_id' ) );
		add_shortcode( 'sgbm_single_service_calendar', array( $this, 'bm_service_calendar_by_id' ) );
		add_shortcode( 'sgbm_flexibooking_checkout_page', array( $this, 'bm_flexibooking_checkout_page' ) );
		add_shortcode( 'sgbm_flexibooking_language_switcher', 'bm_flexibooking_language_switcher' );
		add_shortcode( 'sgbm_flexibooking_coupon_form', array( $this, 'bm_flexibooking_coupon_page' ) );
		add_shortcode( 'sgbm_flexibooking_voucher_redeem_page', array( $this, 'bm_flexibooking_voucher_redeem_page' ) );
		add_shortcode( 'sgbm_add_order', array( $this, 'bm_booking_order_form' ) );
		add_shortcode( 'sgbm_qr_scanner', array( $this, 'bm_qr_scanner_shortcode' ) );
		if ( $old_locale ) {
			$bmrequests->bm_restore_locale( $old_locale );
		}
	}//end bm_register_shortcodes()


	/**
	 * Services listing shortcode
	 *
	 * @author Darpan
	 */
	public function bm_search_services( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'show_date'             => 'default',
				'show_category_filter'  => 'default',
				'show_service_filter'   => 'default',
				'show_service_sorting'  => 'default',
				'show_grid_list_button' => 'default',
				'show_service_limit'    => 'default',
				'service_view_type'     => 'default',
			),
			$atts,
			'sgbm_service_search'
		);

		$dbhandler = new BM_DBhandler();

		$visibility = array(
			'date'             => $this->bm_calculate_shortcode_filter_visibility(
				$atts['show_date'],
				$dbhandler->get_global_option_value( 'bm_show_frontend_service_booking_date_field', 0 )
			),

			'category_filter'  => $this->bm_calculate_shortcode_filter_visibility(
				$atts['show_category_filter'],
				$dbhandler->get_global_option_value( 'bm_show_frontend_category_search', 0 )
			),

			'service_filter'   => $this->bm_calculate_shortcode_filter_visibility(
				$atts['show_service_filter'],
				$dbhandler->get_global_option_value( 'bm_show_frontend_service_search', 0 )
			),

			'service_sorting'  => $this->bm_calculate_shortcode_filter_visibility(
				$atts['show_service_sorting'],
				$dbhandler->get_global_option_value( 'bm_show_frontend_service_sorting', 0 )
			),

			'grid_list_button' => $this->bm_calculate_shortcode_filter_visibility(
				$atts['show_grid_list_button'],
				$dbhandler->get_global_option_value( 'bm_show_frontend_grid_list_button', 0 )
			),

			'service_limit'    => $this->bm_calculate_shortcode_filter_visibility(
				$atts['show_service_limit'],
				$dbhandler->get_global_option_value( 'bm_show_service_limit_box', 0 )
			),

			'view_type'        => ( $atts['service_view_type'] === 'default' )
			? $dbhandler->get_global_option_value( 'bm_frontend_view_type', 'grid' )
			: $atts['service_view_type'],
		);

		ob_start();
		include_once 'partials/booking-management-booking-search.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}//end bm_search_services()


	/**
	 * QR scanner shortcode page
	 *
	 * @author Darpan
	 */
	public function bm_qr_scanner_shortcode() {
		wp_enqueue_script(
			'bm-qr-scanner',
			plugin_dir_url( __FILE__ ) . '../admin/js/booking-management-check-ins.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		wp_localize_script(
			'bm-qr-scanner',
			'bm_ajax_object',
			array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'nonce'      => wp_create_nonce( 'ajax-nonce' ),
				'plugin_url' => plugin_dir_url( __FILE__ ),
			)
		);

		wp_enqueue_script(
			'pdfjs-lib',
			plugin_dir_url( __FILE__ ) . '../public/js/booking-management-pdf-cropper.worker.js',
			array(),
			'2.14.305',
			true
		);

		wp_enqueue_script( 'public-jsqr', plugin_dir_url( __FILE__ ) . 'js/booking-management-jsqr.js', array( 'jquery' ), $this->version, true );

		wp_localize_script(
			'bm-qr-scanner',
			'qrScannerData',
			array(
				'scannerPageUrl' => get_permalink( get_option( 'bm_qr_scanner_page_id' ) ),
			)
		);

		ob_start();
		?>
		<div id="qr-scanner-container">
		<div id="qr-scanner-page">
			<h2><?php echo __( 'QR Code Scanner', 'service-booking' ); ?></h2>
			<div id="scanner-result"></div>
			<?php
			if ( isset( $_GET['qr_scan_done'] ) ) {
				$booking_key = filter_input( INPUT_GET, 'qr_scan_done' );
				if ( ! empty( $booking_key ) ) {
					$bmrequests = new BM_Request();
					$dbhandler  = new BM_DBhandler();

					$payment_txn_id = $booking_key;
					$booking_id     = $dbhandler->get_value( 'BOOKING', 'id', $booking_key, 'booking_key' );
					$transaction    = $dbhandler->get_row( 'TRANSACTIONS', $booking_id, 'booking_id' );

					if ( ! empty( $transaction ) ) {
						$payment_ref_id            = isset( $transaction->id ) ? $transaction->id : 0;
						$booking_type              = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );
						$serviceDate               = $dbhandler->get_value( 'BOOKING', 'booking_date', $booking_id, 'id' );
						$coupons                   = $dbhandler->get_value( 'BOOKING', 'coupons', $booking_id, 'id' );
						$productDetails            = $bmrequests->bm_fetch_product_info_order_details_page( $booking_id );
						$customer_id               = isset( $transaction->customer_id ) ? $transaction->customer_id : 0;
						$customer                  = $dbhandler->get_row( 'CUSTOMERS', $customer_id );
						$customer_billing          = ! empty( $customer ) && isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : '';
						$customer_shipping         = ! empty( $customer ) && isset( $customer->shipping_details ) ? maybe_unserialize( $customer->shipping_details ) : '';
						$price_module_data         = $dbhandler->get_value( 'BOOKING', 'price_module_data', $booking_id, 'id' );
						$price_module_data         = ! empty( $price_module_data ) ? maybe_unserialize( $price_module_data ) : array();
						$order_confirmation_header = esc_html__( 'QR code scan is successfully done.', 'service-booking' );

						$total_discounted_infants  = isset( $price_module_data['infant']['total'] ) ? intval( $price_module_data['infant']['total'] ) : 0;
						$total_discounted_children = isset( $price_module_data['children']['total'] ) ? intval( $price_module_data['children']['total'] ) : 0;
						$total_discounted_adults   = isset( $price_module_data['adult']['total'] ) ? intval( $price_module_data['adult']['total'] ) : 0;
						$total_discounted_seniors  = isset( $price_module_data['senior']['total'] ) ? intval( $price_module_data['senior']['total'] ) : 0;

						$infants_age_from  = isset( $price_module_data['infant']['age']['from'] ) ? intval( $price_module_data['infant']['age']['from'] ) : 0;
						$children_age_from = isset( $price_module_data['children']['age']['from'] ) ? intval( $price_module_data['children']['age']['from'] ) : 0;
						$adults_age_from   = isset( $price_module_data['adult']['age']['from'] ) ? intval( $price_module_data['adult']['age']['from'] ) : 0;
						$seniors_age_from  = isset( $price_module_data['senior']['age']['from'] ) ? intval( $price_module_data['senior']['age']['from'] ) : 0;

						$infants_age_to  = isset( $price_module_data['infant']['age']['to'] ) ? intval( $price_module_data['infant']['age']['to'] ) : 0;
						$children_age_to = isset( $price_module_data['children']['age']['to'] ) ? intval( $price_module_data['children']['age']['to'] ) : 0;
						$adults_age_to   = isset( $price_module_data['adult']['age']['to'] ) ? intval( $price_module_data['adult']['age']['to'] ) : 0;
						$seniors_age_to  = isset( $price_module_data['senior']['age']['to'] ) ? intval( $price_module_data['senior']['age']['to'] ) : 0;

						$infants_total_discount  = isset( $price_module_data['infant']['total_discount'] ) ? floatval( $price_module_data['infant']['total_discount'] ) : 0;
						$children_total_discount = isset( $price_module_data['children']['total_discount'] ) ? floatval( $price_module_data['children']['total_discount'] ) : 0;
						$adults_total_discount   = isset( $price_module_data['adult']['total_discount'] ) ? floatval( $price_module_data['adult']['total_discount'] ) : 0;
						$seniors_total_discount  = isset( $price_module_data['senior']['total_discount'] ) ? floatval( $price_module_data['senior']['total_discount'] ) : 0;

						$infants_total  = isset( $price_module_data['infant']['total_cost'] ) ? floatval( $price_module_data['infant']['total_cost'] ) : 0;
						$children_total = isset( $price_module_data['children']['total_cost'] ) ? floatval( $price_module_data['children']['total_cost'] ) : 0;
						$adults_total   = isset( $price_module_data['adult']['total_cost'] ) ? floatval( $price_module_data['adult']['total_cost'] ) : 0;
						$seniors_total  = isset( $price_module_data['senior']['total_cost'] ) ? floatval( $price_module_data['senior']['total_cost'] ) : 0;

						$infants_discount_type  = isset( $price_module_data['infant']['discount_type'] ) ? $price_module_data['infant']['discount_type'] : 'positive';
						$children_discount_type = isset( $price_module_data['children']['discount_type'] ) ? $price_module_data['children']['discount_type'] : 'positive';
						$adults_discount_type   = isset( $price_module_data['adult']['discount_type'] ) ? $price_module_data['adult']['discount_type'] : 'positive';
						$seniors_discount_type  = isset( $price_module_data['senior']['discount_type'] ) ? $price_module_data['senior']['discount_type'] : 'positive';

						$group_discount          = isset( $price_module_data['group_discount'] ) ? floatval( $price_module_data['group_discount'] ) : 0;
						$discount_type           = isset( $price_module_data['discount_type'] ) ? $price_module_data['discount_type'] : 'positive';
						$negative_group_discount = $dbhandler->get_global_option_value( 'negative_group_discount_' . $booking_key, 0 );

						if ( ! empty( $coupons ) ) {
							if ( $group_discount > 0 ) {
								$coupon_discount = isset( $productDetails['discount'] ) ? ( $productDetails['discount'] - ( $infants_total_discount + $children_total_discount + $group_discount ) ) : 0;
							} else {
								$coupon_discount = isset( $productDetails['discount'] ) ? ( $productDetails['discount'] - ( $infants_total_discount + $children_total_discount + $adults_total_discount + $seniors_total_discount ) ) : 0;
							}
						} else {
							$coupon_discount = 0;
						}

						wp_enqueue_style( 'add-order-detials-final-page', plugin_dir_url( __FILE__ ) . 'css/booking-management-booking-final-page.css', array(), $this->version, 'all' );

						ob_start();
						?>
		<div class="booking_details qr_scan_details">
		<table class="booking-container">
				<tr>
					<td>
						<table class="header">
							<tr>
								<td>
									<h1><?php esc_html_e( 'Hey ', 'service-booking' ); ?> <?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?>!</h1>
									<p><?php echo esc_html( $order_confirmation_header ); ?></p>
								</td>
							</tr>
						</table>
						
						<table class="order-details">
							<tr>
								<td class="subheading"><strong><?php esc_html_e( 'Service Date: ', 'service-booking' ); ?></strong><br/>
									<span><?php echo isset( $serviceDate ) ? esc_html( $bmrequests->bm_month_year_date_format( $serviceDate ) ) : ''; ?></span>
								</td>
								<td colspan="1" class="subheading td-center-align"><strong><?php esc_html_e( 'Order Ref: ', 'service-booking' ); ?></strong><br/>
									<span><?php echo esc_html( $payment_txn_id ); ?></span></td>
								<td colspan="3" class="subheading td-right-align"><strong><?php esc_html_e( 'Payment via ', 'service-booking' ); ?></strong><br/>
									<span><?php esc_html_e( 'Card ', 'service-booking' ); ?></span></td>
							</tr>
						<?php
						$count = 1;
						foreach ( $productDetails['products'] as $product ) {
							?>
							
							<tr>
								<td colspan="2">
							<?php if ( $count == 1 ) { ?>
										<span class="theading"><?php esc_html_e( 'Main Product ', 'service-booking' ); ?></span><br>
							<?php } elseif ( $count == 2 ) { ?>
										<span class="theading"><?php esc_html_e( 'Extra Products ', 'service-booking' ); ?></span><br>
							<?php } ?>
										<span class="subtext"><?php echo isset( $product['name'] ) ? esc_html( $product['name'] ) : 'N/A'; ?></span>
								</td>
								<td><span class="theading"><?php esc_html_e( 'Price ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['base_price'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['base_price'], true ) ) : 'N/A'; ?></span></td>
								<td><span class="theading"><?php esc_html_e( 'Qty ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['quantity'] ) ? esc_html( $product['quantity'] ) : 'N/A'; ?></span></td>
								<td class="td-right-align"><span class="theading td-right-align"><?php esc_html_e( 'Total ', 'total' ); ?></span><br/><span class="subtext"><?php echo isset( $product['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['total'], true ) ) : 'N/A'; ?></span></td>
							</tr>
							<?php
							++$count;
						}
						?>
							<tr >
								<td class="subtotal " colspan="4"><?php esc_html_e( 'Subtotal ', 'service-booking' ); ?></td>
								<td class="subtotal td-right-align"><?php echo isset( $productDetails['subtotal'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['subtotal'], true ) ) : 'N/A'; ?></td>
							</tr>
							<tr class="noborder">
								<td colspan="4"><?php esc_html_e( 'Discount ', 'service-booking' ); ?></td>
								<td class="discountvalue td-right-align">-<?php echo isset( $productDetails['discount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['discount'], true ) ) : 'N/A'; ?></td>
							</tr>
							<tr class="totalbar">
								<td colspan="4"><?php esc_html_e( 'Total ', 'service-booking' ); ?></td>
								<td class="td-right-align"><?php echo isset( $productDetails['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['total'], true ) ) : 'N/A'; ?></td>
							</tr>
						</table>
						<table class="billing-shipping noborder">
							<tr>
								<th><?php esc_html_e( 'Billing Address', 'service-booking' ); ?></th>
								<th class="td-right-align"><?php esc_html_e( 'Shipping Address', 'service-booking' ); ?></th>
							</tr>
							<tr>
								<td class="addresstext"><?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_address'] ) ? esc_html( $customer_billing['billing_address'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_state'] ) ? esc_html( $customer_billing['billing_state'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_email'] ) ? esc_html( $customer_billing['billing_email'] ) : ''; ?></td>
								<td class="addresstext td-right-align" style="padding-right:0px;"><?php echo isset( $customer_shipping['shipping_first_name'] ) ? esc_html( $customer_shipping['shipping_first_name'] ) . ' ' . esc_html( $customer_shipping['shipping_last_name'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_address'] ) ? esc_html( $customer_shipping['shipping_address'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_state'] ) ? esc_html( $customer_shipping['shipping_state'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_email'] ) ? esc_html( $customer_shipping['shipping_email'] ) : ''; ?></td>
							</tr>
							
						</table>
						<?php
						if ( ! empty( $price_module_data ) || ! empty( $coupons ) ) {
							?>
						<table class="billing-shipping-notification noborder hidden">
							<tr>
								<th>
								<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>    
								<?php esc_html_e( 'This order has', 'service-booking' ); ?></th>
							</tr>
							<?php
							if ( ! empty( $price_module_data ) && is_array( $price_module_data ) ) {
								if ( ! empty( $total_discounted_infants ) ) {
									$infants_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $infants_total_discount, true );
									$class                  = $infants_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
									?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
									<?php echo esc_html( $total_discounted_infants ) . ' <strong>' . esc_html__( 'infant/s of the age group from ', 'service-booking' ) . esc_html( $infants_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $infants_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $infants_total_discount ) . '</span>'; ?>
									</td>
								</tr>
								<?php } ?>

								<?php
								if ( ! empty( $total_discounted_children ) ) {
									$children_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $children_total_discount, true );
									$class                   = $children_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
									?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
									<?php
									echo esc_html( $total_discounted_children ) . ' <strong>' . esc_html__( 'child/children of the age group from ', 'service-booking' ) . esc_html( $children_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $children_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $children_total_discount ) . '</span>';
									?>
									</td>
								</tr>
								<?php } ?>

								<?php
								if ( empty( $group_discount ) ) {
									if ( ! empty( $total_discounted_adults ) ) {
										$adults_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $adults_total_discount, true );
										$class                 = $adults_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
										?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
										<?php echo esc_html( $total_discounted_adults ) . ' <strong>' . esc_html__( 'adult/s of the age group from ', 'service-booking' ) . esc_html( $adults_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $adults_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $adults_total_discount ) . '</span>'; ?>
									</td>
								</tr>
									<?php } ?>

									<?php
									if ( ! empty( $total_discounted_seniors ) ) {
										$seniors_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $seniors_total_discount, true );
										$class                  = $seniors_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
										?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
										<?php echo esc_html( $total_discounted_seniors ) . ' <strong>' . esc_html__( 'senior/s of the age group from ', 'service-booking' ) . esc_html( $seniors_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $seniors_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $seniors_total_discount ) . '</span>'; ?>
									</td>
								</tr>
										<?php
									}
								} else {
										$class = $negative_group_discount == 1 ? 'negative_discount' : 'postive_price_module_discount';
									?>
										<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
									<?php echo esc_html( $total_discounted_adults + $total_discounted_seniors ) . ' <strong>' . esc_html__( 'adult/s and senior/s of the age group from ', 'service-booking' ) . esc_html( $adults_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $seniors_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $group_discount, true ) ) . '</span>'; ?>
									</td>
								</tr>
									<?php
								}
								?>
								<?php
							}
							if ( ! empty( $coupons ) && $coupon_discount > 0 ) {
								$coupon_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $coupon_discount, true );
								?>
						<tr>
						<td class="addresstext addresstext-notic">
						<i class="fa fa-hand-o-right" aria-hidden="true"></i>
								<?php echo esc_html__( 'coupon/s ', 'service-booking' ) . '<strong>' . esc_html( $coupons ) . '</strong>' . ' ' . esc_html__( 'with total discount of ', 'service-booking' ) . '<span class="postive_price_module_discount">' . esc_html( $coupon_discount ) . '</span>'; ?>
						</td>
						</tr>
								<?php
							}
							?>
				</table>
						<?php } ?>
						<table class="footer">
							<tr>
								<td colspan="2" class="copyright"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'partials/images/logo.png' ); ?>" style="width:200px;"/><br/><?php esc_html_e( 'Copyrights Reserved ', 'service-booking' ); ?> &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
						<?php
						$html = ob_get_clean();
						return $html;
					} else {
						return '<div class="booking-success">' . __( 'Booking info could not be fetched!', 'service-booking' ) . '</div>';
					}
				} else {
					return '<div class="booking-success">' . __( 'Booking info could not be fetched!', 'service-booking' ) . '</div>';
				}
			}

			?>
		   
			<div id="scanner-container"> 
				<img id="arrowLeft" src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'partials/image/camera.svg' ); ?>"/>
				<video id="scanner-video" width="100%" playsinline></video>
				<canvas id="scanner-canvas" style="display: none;"></canvas>
			</div>
		   
			<div id="scanner-actions">
				<button id="start-scan" class="button button-primary"><?php echo esc_html__( 'Start Scanner', 'service-booking' ); ?></button>
				<button id="stop-scan" class="button button-primary"><?php echo esc_html__( 'Stop Scanner', 'service-booking' ); ?></button>
				<input type="file" id="qr-file-input" accept="image/*,application/pdf" style="display: none;">
				<button id="upload-qr" class="button button-primary"><?php echo esc_html__( 'Upload QR Code image', 'service-booking' ); ?></button>
			</div>

			<div id="qr-cropper-modal" style="display:none;">
				<div id="qr-modal-box">
					<div id="qr-modal-header">
					<span class="qr-modal-close">×</span>
					</div>
					<div id="qr-loading-spinner" style="display:none;"><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></div>
					<img id="cropper-image" src="" style="max-width:100%; display:none;" />
					<button id="crop-confirm"><?php echo esc_html__( 'Confirm Crop', 'service-booking' ); ?></button>
				</div>
			</div>

		</div>
	</div>
		<?php
		return ob_get_clean();
	}


	/**
	 * add order form
	 *
	 * @author Darpan
	 */
	public function bm_booking_order_form() {
		if ( isset( $_GET['booking_success'] ) ) {
			$booking_key = filter_input( INPUT_GET, 'booking_success' );
			if ( ! empty( $booking_key ) ) {
				$bmrequests = new BM_Request();
				$dbhandler  = new BM_DBhandler();

				$payment_txn_id = $booking_key;
				$booking_id     = $dbhandler->get_value( 'BOOKING', 'id', $booking_key, 'booking_key' );
				$transaction    = $dbhandler->get_row( 'TRANSACTIONS', $booking_id, 'booking_id' );

				if ( ! empty( $transaction ) ) {
					$payment_ref_id               = isset( $transaction->id ) ? $transaction->id : 0;
					$booking_type                 = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );
					$serviceDate                  = $dbhandler->get_value( 'BOOKING', 'booking_date', $booking_id, 'id' );
					$coupons                      = $dbhandler->get_value( 'BOOKING', 'coupons', $booking_id, 'id' );
					$productDetails               = $bmrequests->bm_fetch_product_info_order_details_page( $booking_id );
					$customer_id                  = isset( $transaction->customer_id ) ? $transaction->customer_id : 0;
					$customer                     = $dbhandler->get_row( 'CUSTOMERS', $customer_id );
					$customer_billing             = ! empty( $customer ) && isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : '';
					$customer_shipping            = ! empty( $customer ) && isset( $customer->shipping_details ) ? maybe_unserialize( $customer->shipping_details ) : '';
					$price_module_data            = $dbhandler->get_value( 'BOOKING', 'price_module_data', $booking_id, 'id' );
					$price_module_data            = ! empty( $price_module_data ) ? maybe_unserialize( $price_module_data ) : array();
					$order_confirmation_header    = esc_html__( 'Thanks for Your Order.', 'service-booking' );
					$order_confirmation_subheader = esc_html__( 'Your order is Confirmed. You will receive a confirmation mail in your billing email.', 'service-booking' );

					$total_discounted_infants  = isset( $price_module_data['infant']['total'] ) ? intval( $price_module_data['infant']['total'] ) : 0;
					$total_discounted_children = isset( $price_module_data['children']['total'] ) ? intval( $price_module_data['children']['total'] ) : 0;
					$total_discounted_adults   = isset( $price_module_data['adult']['total'] ) ? intval( $price_module_data['adult']['total'] ) : 0;
					$total_discounted_seniors  = isset( $price_module_data['senior']['total'] ) ? intval( $price_module_data['senior']['total'] ) : 0;

					$infants_age_from  = isset( $price_module_data['infant']['age']['from'] ) ? intval( $price_module_data['infant']['age']['from'] ) : 0;
					$children_age_from = isset( $price_module_data['children']['age']['from'] ) ? intval( $price_module_data['children']['age']['from'] ) : 0;
					$adults_age_from   = isset( $price_module_data['adult']['age']['from'] ) ? intval( $price_module_data['adult']['age']['from'] ) : 0;
					$seniors_age_from  = isset( $price_module_data['senior']['age']['from'] ) ? intval( $price_module_data['senior']['age']['from'] ) : 0;

					$infants_age_to  = isset( $price_module_data['infant']['age']['to'] ) ? intval( $price_module_data['infant']['age']['to'] ) : 0;
					$children_age_to = isset( $price_module_data['children']['age']['to'] ) ? intval( $price_module_data['children']['age']['to'] ) : 0;
					$adults_age_to   = isset( $price_module_data['adult']['age']['to'] ) ? intval( $price_module_data['adult']['age']['to'] ) : 0;
					$seniors_age_to  = isset( $price_module_data['senior']['age']['to'] ) ? intval( $price_module_data['senior']['age']['to'] ) : 0;

					$infants_total_discount  = isset( $price_module_data['infant']['total_discount'] ) ? floatval( $price_module_data['infant']['total_discount'] ) : 0;
					$children_total_discount = isset( $price_module_data['children']['total_discount'] ) ? floatval( $price_module_data['children']['total_discount'] ) : 0;
					$adults_total_discount   = isset( $price_module_data['adult']['total_discount'] ) ? floatval( $price_module_data['adult']['total_discount'] ) : 0;
					$seniors_total_discount  = isset( $price_module_data['senior']['total_discount'] ) ? floatval( $price_module_data['senior']['total_discount'] ) : 0;

					$infants_total  = isset( $price_module_data['infant']['total_cost'] ) ? floatval( $price_module_data['infant']['total_cost'] ) : 0;
					$children_total = isset( $price_module_data['children']['total_cost'] ) ? floatval( $price_module_data['children']['total_cost'] ) : 0;
					$adults_total   = isset( $price_module_data['adult']['total_cost'] ) ? floatval( $price_module_data['adult']['total_cost'] ) : 0;
					$seniors_total  = isset( $price_module_data['senior']['total_cost'] ) ? floatval( $price_module_data['senior']['total_cost'] ) : 0;

					$infants_discount_type  = isset( $price_module_data['infant']['discount_type'] ) ? $price_module_data['infant']['discount_type'] : 'positive';
					$children_discount_type = isset( $price_module_data['children']['discount_type'] ) ? $price_module_data['children']['discount_type'] : 'positive';
					$adults_discount_type   = isset( $price_module_data['adult']['discount_type'] ) ? $price_module_data['adult']['discount_type'] : 'positive';
					$seniors_discount_type  = isset( $price_module_data['senior']['discount_type'] ) ? $price_module_data['senior']['discount_type'] : 'positive';

					$group_discount          = isset( $price_module_data['group_discount'] ) ? floatval( $price_module_data['group_discount'] ) : 0;
					$discount_type           = isset( $price_module_data['discount_type'] ) ? $price_module_data['discount_type'] : 'positive';
					$negative_group_discount = $dbhandler->get_global_option_value( 'negative_group_discount_' . $booking_key, 0 );

					if ( $booking_type == 'on_request' ) {
						$order_confirmation_header    = esc_html__( 'Thanks for Your Request.', 'service-booking' );
						$order_confirmation_subheader = esc_html__( 'Your booking request is received. You will receive a mail in your billing email when your order is confirmed or cancelled.', 'service-booking' );
					}

					if ( ! empty( $coupons ) ) {
						if ( $group_discount > 0 ) {
							$coupon_discount = isset( $productDetails['discount'] ) ? ( $productDetails['discount'] - ( $infants_total_discount + $children_total_discount + $group_discount ) ) : 0;
						} else {
							$coupon_discount = isset( $productDetails['discount'] ) ? ( $productDetails['discount'] - ( $infants_total_discount + $children_total_discount + $adults_total_discount + $seniors_total_discount ) ) : 0;
						}
					} else {
						$coupon_discount = 0;
					}

					wp_enqueue_style( 'add-order-detials-final-page', plugin_dir_url( __FILE__ ) . 'css/booking-management-booking-final-page.css', array(), $this->version, 'all' );

					ob_start();
					?>
		<div class="booking_details">
		<table class="booking-container">
				<tr>
					<td>
						<table class="header">
							<tr>
								<td>
									<h1><?php esc_html_e( 'Hey ', 'service-booking' ); ?> <?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?>!</h1>
									<p><?php echo esc_html( $order_confirmation_header ); ?></p>
								</td>
							</tr>
						</table>
						
						<table class="order-details">
							<tr>
								<td colspan="5">
									<p><?php echo esc_html( $order_confirmation_subheader ); ?></p>
								</td>
							</tr>
							<tr>
								<td class="subheading"><strong><?php esc_html_e( 'Service Date: ', 'service-booking' ); ?></strong><br/>
									<span><?php echo isset( $serviceDate ) ? esc_html( $bmrequests->bm_month_year_date_format( $serviceDate ) ) : ''; ?></span>
								</td>
								<td colspan="1" class="subheading td-center-align"><strong><?php esc_html_e( 'Order Ref: ', 'service-booking' ); ?></strong><br/>
									<span><?php echo esc_html( $payment_txn_id ); ?></span></td>
								<td colspan="3" class="subheading td-right-align"><strong><?php esc_html_e( 'Payment via ', 'service-booking' ); ?></strong><br/>
									<span><?php esc_html_e( 'Card ', 'service-booking' ); ?></span></td>
							</tr>
					<?php
					$count = 1;
					foreach ( $productDetails['products'] as $product ) {
						?>
							
							<tr>
								<td colspan="2">
						<?php if ( $count == 1 ) { ?>
										<span class="theading"><?php esc_html_e( 'Main Product ', 'service-booking' ); ?></span><br>
						<?php } elseif ( $count == 2 ) { ?>
										<span class="theading"><?php esc_html_e( 'Extra Products ', 'service-booking' ); ?></span><br>
						<?php } ?>
										<span class="subtext"><?php echo isset( $product['name'] ) ? esc_html( $product['name'] ) : 'N/A'; ?></span>
								</td>
								<td><span class="theading"><?php esc_html_e( 'Price ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['base_price'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['base_price'], true ) ) : 'N/A'; ?></span></td>
								<td><span class="theading"><?php esc_html_e( 'Qty ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['quantity'] ) ? esc_html( $product['quantity'] ) : 'N/A'; ?></span></td>
								<td class="td-right-align"><span class="theading td-right-align"><?php esc_html_e( 'Total ', 'total' ); ?></span><br/><span class="subtext"><?php echo isset( $product['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['total'], true ) ) : 'N/A'; ?></span></td>
							</tr>
						<?php
						++$count;
					}
					?>
							<tr >
								<td class="subtotal " colspan="4"><?php esc_html_e( 'Subtotal ', 'service-booking' ); ?></td>
								<td class="subtotal td-right-align"><?php echo isset( $productDetails['subtotal'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['subtotal'], true ) ) : 'N/A'; ?></td>
							</tr>
							<tr class="noborder">
								<td colspan="4"><?php esc_html_e( 'Discount ', 'service-booking' ); ?></td>
								<td class="discountvalue td-right-align">-<?php echo isset( $productDetails['discount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['discount'], true ) ) : 'N/A'; ?></td>
							</tr>
							<tr class="totalbar">
								<td colspan="4"><?php esc_html_e( 'Total ', 'service-booking' ); ?></td>
								<td class="td-right-align"><?php echo isset( $productDetails['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['total'], true ) ) : 'N/A'; ?></td>
							</tr>
						</table>
						<table class="billing-shipping noborder">
							<tr>
								<th><?php esc_html_e( 'Billing Address', 'service-booking' ); ?></th>
								<th class="td-right-align"><?php esc_html_e( 'Shipping Address', 'service-booking' ); ?></th>
							</tr>
							<tr>
								<td class="addresstext"><?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_address'] ) ? esc_html( $customer_billing['billing_address'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_state'] ) ? esc_html( $customer_billing['billing_state'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_email'] ) ? esc_html( $customer_billing['billing_email'] ) : ''; ?></td>
								<td class="addresstext td-right-align" style="padding-right:0px;"><?php echo isset( $customer_shipping['shipping_first_name'] ) ? esc_html( $customer_shipping['shipping_first_name'] ) . ' ' . esc_html( $customer_shipping['shipping_last_name'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_address'] ) ? esc_html( $customer_shipping['shipping_address'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_state'] ) ? esc_html( $customer_shipping['shipping_state'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_email'] ) ? esc_html( $customer_shipping['shipping_email'] ) : ''; ?></td>
							</tr>
							
						</table>
					<?php
					if ( ! empty( $price_module_data ) || ! empty( $coupons ) ) {
						?>
						<table class="billing-shipping-notification noborder">
							<tr>
								<th>
								<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>    
								<?php esc_html_e( 'This order has', 'service-booking' ); ?></th>
							</tr>
						<?php
						if ( ! empty( $price_module_data ) && is_array( $price_module_data ) ) {
							if ( ! empty( $total_discounted_infants ) ) {
								$infants_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $infants_total_discount, true );
								$class                  = $infants_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
								?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
								<?php echo esc_html( $total_discounted_infants ) . ' <strong>' . esc_html__( 'infant/s of the age group from ', 'service-booking' ) . esc_html( $infants_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $infants_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $infants_total_discount ) . '</span>'; ?>
									</td>
								</tr>
							<?php } ?>

							<?php
							if ( ! empty( $total_discounted_children ) ) {
								$children_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $children_total_discount, true );
								$class                   = $children_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
								?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
								<?php
								echo esc_html( $total_discounted_children ) . ' <strong>' . esc_html__( 'child/children of the age group from ', 'service-booking' ) . esc_html( $children_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $children_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $children_total_discount ) . '</span>';
								?>
									</td>
								</tr>
							<?php } ?>

							<?php
							if ( empty( $group_discount ) ) {
								if ( ! empty( $total_discounted_adults ) ) {
									$adults_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $adults_total_discount, true );
									$class                 = $adults_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
									?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
									<?php echo esc_html( $total_discounted_adults ) . ' <strong>' . esc_html__( 'adult/s of the age group from ', 'service-booking' ) . esc_html( $adults_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $adults_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $adults_total_discount ) . '</span>'; ?>
									</td>
								</tr>
								<?php } ?>

								<?php
								if ( ! empty( $total_discounted_seniors ) ) {
									$seniors_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $seniors_total_discount, true );
									$class                  = $seniors_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
									?>
								<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
									<?php echo esc_html( $total_discounted_seniors ) . ' <strong>' . esc_html__( 'senior/s of the age group from ', 'service-booking' ) . esc_html( $seniors_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $seniors_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $seniors_total_discount ) . '</span>'; ?>
									</td>
								</tr>
									<?php
								}
							} else {
										$class = $negative_group_discount == 1 ? 'negative_discount' : 'postive_price_module_discount';
								?>
										<tr>
									<td class="addresstext addresstext-notic">
									<i class="fa fa-hand-o-right" aria-hidden="true"></i>
								<?php echo esc_html( $total_discounted_adults + $total_discounted_seniors ) . ' <strong>' . esc_html__( 'adult/s and senior/s of the age group from ', 'service-booking' ) . esc_html( $adults_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $seniors_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $group_discount, true ) ) . '</span>'; ?>
									</td>
								</tr>
								<?php
							}
							?>
							<?php
						}
						if ( ! empty( $coupons ) && $coupon_discount > 0 ) {
							$coupon_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $coupon_discount, true );
							?>
						<tr>
						<td class="addresstext addresstext-notic">
						<i class="fa fa-hand-o-right" aria-hidden="true"></i>
							<?php echo esc_html__( 'coupon/s ', 'service-booking' ) . '<strong>' . esc_html( $coupons ) . '</strong>' . ' ' . esc_html__( 'with total discount of ', 'service-booking' ) . '<span class="postive_price_module_discount">' . esc_html( $coupon_discount ) . '</span>'; ?>
						</td>
						</tr>
							<?php
						}
						?>
				</table>
					<?php } ?>
						<table class="footer">
							<tr>
								<td colspan="2" class="copyright"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'partials/images/logo.png' ); ?>" style="width:200px;"/><br/><?php esc_html_e( 'Copyrights Reserved ', 'service-booking' ); ?> &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</div>
					<?php
					$html = ob_get_clean();
					return $html;
				} else {
					return '<div class="booking-success">' . __( 'Booking info could not be fetched!', 'service-booking' ) . '</div>';
				}
			} else {
				return '<div class="booking-success">' . __( 'Booking info could not be fetched!', 'service-booking' ) . '</div>';
			}
		}

		if ( isset( $_GET['booking_error'] ) ) {
			return '<div class="booking-error">' . __( 'Your booking could not be saved!', 'service-booking' ) . '</div>';
		}

		ob_start();
		include_once 'partials/booking-management-add-order.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}


	/**
	 * Calculate visibility of fillters in default service shortcode
	 *
	 * @author Darpan
	 */
	private function bm_calculate_shortcode_filter_visibility( $shortcode_value, $global_value ) {
		if ( $shortcode_value === 'default' ) {
			return (bool) $global_value;
		}
		return filter_var( $shortcode_value, FILTER_VALIDATE_BOOLEAN );
	}//end bm_calculate_shortcode_filter_visibility()


	/**
	 * Service fullcalendar shortcode
	 *
	 * @author Darpan
	 */
	public function bm_service_fullcalendar( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'show_filters'         => 'true',
				'show_service_filter'  => 'true',
				'show_category_filter' => 'true',
				'cat_ids'              => '',
			),
			$atts,
			'sgbm_service_fullcalendar'
		);

		$show_filters         = filter_var( $atts['show_filters'], FILTER_VALIDATE_BOOLEAN );
		$show_service_filter  = filter_var( $atts['show_service_filter'], FILTER_VALIDATE_BOOLEAN );
		$show_category_filter = filter_var( $atts['show_category_filter'], FILTER_VALIDATE_BOOLEAN );
		$cat_ids              = $atts['cat_ids'];

		ob_start();
		include_once 'partials/booking-management-service-fullcalendar-shortcode.php';
		return ob_get_clean();
	}//end bm_service_fullcalendar()


	/**
	 * Service fullcalendar shortcode
	 *
	 * @author Darpan
	 */
	public function bm_service_timeslot_fullcalendar( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'show_filters'         => 'true',
				'show_service_filter'  => 'true',
				'show_category_filter' => 'true',
				'cat_ids'              => '',
			),
			$atts,
			'sgbm_service_timeslot_fullcalendar'
		);

		$show_filters         = filter_var( $atts['show_filters'], FILTER_VALIDATE_BOOLEAN );
		$show_service_filter  = filter_var( $atts['show_service_filter'], FILTER_VALIDATE_BOOLEAN );
		$show_category_filter = filter_var( $atts['show_category_filter'], FILTER_VALIDATE_BOOLEAN );
		$cat_ids              = $atts['cat_ids'];

		ob_start();
		include_once 'partials/booking-management-service-timeslot-fullcalendar-shortcode.php';
		return ob_get_clean();
	}//end bm_service_timeslot_fullcalendar()


	/**
	 * Services by category listing shortcode
	 *
	 * @author Darpan
	 */
	public function bm_service_by_category( $atts ) {
			$dbhandler      = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$default_attributes = array( 'ids' => '' );
		$attributes         = shortcode_atts( $default_attributes, $atts, 'sgbm_service_by_category' );
		$category_ids       = $attributes['ids'];
		$html               = '';

		if ( isset( $category_ids ) ) {
			$category_ids = explode( ',', $category_ids );

			if ( is_array( $category_ids ) ) {
				foreach ( $category_ids as $key => $category_id ) {
					$is_visible = $bmrequests->bm_check_if_category_is_visible( $category_id );

					if ( ( $is_visible == 0 ) || ( $category_id == '' ) ) {
						unset( $category_ids[ $key ] );
					}
				}
			}

			$category_ids = ! empty( $category_ids ) && is_array( $category_ids ) ? array_values( $category_ids ) : '';

			if ( ! empty( $category_ids ) ) {
				$category_ids = implode( ',', $category_ids );
				$additional   = "AND service_category in($category_ids)";
				$services     = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );

				if ( ! empty( $services ) ) {
					foreach ( $services as $service ) {
						$html .= $bmrequests->bm_fetch_service_by_category_response( $service );
					}
				}
			}

			if ( ! empty( $html ) ) {
				++self::$counter;
				ob_start();
				$content = $bmrequests->bm_fetch_service_by_category_shortcode_html_content( $html, $category_ids, self::$counter );
				ob_end_clean();
				return $content;
			}
		}
	}//end bm_service_by_category()


	/**
	 * Services by id shortcode
	 *
	 * @author Darpan
	 */
	public function bm_service_by_id( $att ) {
			$default_attribute = array( 'id' => '' );
		$attribute             = shortcode_atts( $default_attribute, $att, 'sgbm_single_service' );
		$service_id            = intval( $attribute['id'] );
		$html                  = '';

		if ( isset( $service_id ) ) {
			$dbhandler  = new BM_DBhandler();
			$bmrequests = new BM_Request();
			$service    = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$category_id = isset( $service->service_category ) ? $service->service_category : '';

				$is_service_visible  = isset( $service->is_service_front ) ? $service->is_service_front : 0;
				$is_category_visible = $bmrequests->bm_check_if_category_is_visible( $category_id );

				if ( ( $is_service_visible == 1 ) && ( $is_category_visible == 1 ) ) {
					$html .= $bmrequests->bm_fetch_service_by_category_response( $service, '', 'service_by_id' );
				}
			}

			if ( ! empty( $html ) ) {
				ob_start();
				$content = $bmrequests->bm_fetch_single_service_shortcode_html_content( $html );
				ob_end_clean();
				return $content;
			}
		}
	}//end bm_service_by_id()


	/**
	 * Service calendar by id shortcode
	 *
	 * @author Darpan
	 */
	public function bm_service_calendar_by_id( $att ) {
		$default_attribute = array( 'id' => '' );
		$attribute         = shortcode_atts( $default_attribute, $att, 'sgbm_single_service_calendar' );
		$service_id        = intval( $attribute['id'] );
		$html              = '';

		if ( isset( $service_id ) ) {
			$service = ( new BM_DBhandler() )->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$bmrequests  = new BM_Request();
				$category_id = isset( $service->service_category ) ? $service->service_category : '';

				$is_service_visible  = isset( $service->is_service_front ) ? $service->is_service_front : 0;
				$is_category_visible = $bmrequests->bm_check_if_category_is_visible( $category_id );

				if ( ( $is_service_visible == 1 ) && ( $is_category_visible == 1 ) ) {
					$html .= $bmrequests->bm_fetch_service_by_calendar_response( $service );
				}
			}

			if ( ! empty( $html ) ) {
				ob_start();
				$content = $bmrequests->bm_fetch_single_service_calendar_shortcode_html_content( $html );
				ob_end_clean();
				return $content;
			}
		}
	}//end bm_service_calendar_by_id()


	/**
	 * Checkout page shortcode
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_checkout_page() {
			ob_start();
		include_once 'partials/booking-management-checkout.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}//end bm_flexibooking_checkout_page()


	/**
	 * Voucher redeem page shortcode
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_voucher_redeem_page() {
			ob_start();
		include_once 'partials/booking-management-voucher-redeem.php';
		$content = ob_get_contents();
		ob_end_clean();
		return $content;
	}//end bm_flexibooking_voucher_redeem_page()


	/**
	 * Language Switcher
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_language_switcher() {
			ob_start();
		$content = $this->flexibooking_language_switcher();
		ob_end_clean();
		return $content;
	}//end bm_flexibooking_language_switcher()


	/**
	 * Language Switcher Content
	 *
	 * @author Darpan
	 */
	public function flexibooking_language_switcher() {
			/**$translations = wp_get_available_translations();
			$args = array(
				'id'                          => 'bm_flexibooking_language',
				'name'                        => 'bm_flexibooking_language',
				'languages'                   => get_available_languages(),
				'selected'                    => get_locale(),
				'show_available_translations' => false,
				'echo'                        => false,
			);

		$dropdown = wp_dropdown_languages( $args );
		return wp_kses( $dropdown, $bmrequests->bm_fetch_expanded_allowed_tags() );*/

		$dbhandler        = new BM_DBhandler();
		$bmrequests       = new BM_Request();
		$html             = '';
		$languages        = $dbhandler->get_global_option_value( 'bm_flexibooking_languages', array() );
		$current_language = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

		$html .= '<select name="bm_flexibooking_language" id="bm_flexibooking_language" onchange="change_flexi_language(this)">';
		foreach ( $languages as $lang_code => $lang_name ) {
			$selected = ( $current_language === $lang_code ) ? 'selected' : '';
			$html    .= '<option value="' . esc_html( $lang_code ) . '" ' . esc_html( $selected ) . '>' . esc_html( $lang_name ) . '</option>';
		}
		$html .= '</select>';

		$html = apply_filters( 'bm_flexibooking_frontend_language_switcher_html', $html, $languages, $current_language );

		return wp_kses( $html, $bmrequests->bm_fetch_expanded_allowed_tags() );
	}//end flexibooking_language_switcher()


	/**
	 * Set Language
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_language() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post       = apply_filters( 'bm_flexibooking_set_frontend_language_post_data', $post );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$set_language = isset( $post['flexi_lang_code'] ) ? esc_html( sanitize_text_field( wp_unslash( $post['flexi_lang_code'] ) ) ) : esc_html( 'en' );

			if ( in_array( $set_language, array( 'en', 'it' ) ) ) {
				$current_locale = $set_language == 'it' ? 'it_IT' : 'en_US';
				$dbhandler->update_global_option_value( 'bm_flexi_current_language', $set_language );
				$dbhandler->update_global_option_value( 'bm_flexi_current_locale', $current_locale );
				$this->bm_flexibooking_load_locale();

				do_action( 'bm_flexibooking_frontend_language_set', $set_language, $current_locale );

				$data['status'] = true;
			}
		}

		$data = apply_filters( 'bm_flexibooking_set_frontend_language_response', $data, $set_language );

		echo wp_json_encode( $data );
		die;
	} ///end bm_flexibooking_set_language()


	/**
	 * Load locale
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_load_locale() {
			$dbhandler  = new BM_DBhandler();
		$current_locale = $dbhandler->get_global_option_value( 'bm_flexi_current_locale', 'en_US' );
		$current_locale = apply_filters( 'bm_flexibooking_modify_frontend_locale', $current_locale );

		switch_to_locale( $current_locale );

		$current_locale == 'en_US' ? update_option( 'WPLANG', '' ) : update_option( 'WPLANG', $current_locale );

		do_action( 'bm_flexibooking_frontend_locale_switched', $current_locale );
	}//end bm_flexibooking_load_locale()


	/**
	 * Fetch all Services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_services() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();
		$resp       = '';

		if ( $post != false && $post != null ) {
			$date    = isset( $post['date'] ) ? $post['date'] : '';
			$type    = isset( $post['type'] ) ? $post['type'] : '';
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) && $post['limit'] !== '-1' ? $post['limit'] : false;
			$order   = isset( $post['order'] ) ? $post['order'] : '';
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) && $post['limit'] !== '-1' ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$sorting = $this->bm_fetch_service_sort_order( $order );
			$dbhandler->update_global_option_value( 'bm_svc_search_shortcode_data_sorting', $order );

			if ( ! empty( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) ) ) {
				$sorting = $this->bm_fetch_service_sort_order( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) );
			}

			$tables = array( 'SERVICE', 's' );
			$joins  = array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			);

			$where = array(
				's.is_service_front' => array( '=' => 1 ),
			);

			$additional = 'AND (c.cat_in_front = 1 OR s.service_category = 0)';

			$columns = 's.*';

			$total_records = $dbhandler->get_results_with_join(
				$tables,
				'COUNT(*) as total',
				$joins,
				$where,
				'var',
				0,
				false,
				null,
				false,
				$additional
			);

			$services = $dbhandler->get_results_with_join(
				$tables,
				$columns,
				$joins,
				$where,
				'results',
				$offset,
				$limit,
				$sorting['sort_by'],
				$sorting['descending'],
				$additional
			);

			$available_services = array();

			if ( ! empty( $services ) ) {
				$response_parts = array();

				foreach ( $services as $key => $service ) {
					$response = $bmrequests->bm_fetch_service_response( $service, $date, $type );

					if ( empty( $response ) ) {
						unset( $services[ $key ] );
						continue;
					}

					$response_parts[] = $response;
				}

				if ( ! empty( $response_parts ) ) {
					$available_services[] = $response_parts;
				}
			}

			if ( ! empty( $services ) ) {
				$services = array_values( $services );
			}

			if ( ! empty( $available_services ) ) {
				$flat_services = array_merge( ...$available_services );
				$resp          = implode( '', $flat_services );
			} else {
				$resp = '<div class="ajax-no-records">'
					. esc_html__( 'No Bookable Services Found', 'service-booking' )
					. '</div>';
			}

			$num_of_pages = $limit !== false ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base );

			$data['data']        = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
			$data['service_ids'] = ! empty( $services ) ? wp_list_pluck( $services, 'id' ) : array();
			$data['pagination']  = $pagination;
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_all_services()


	/**
	 * Fetch Service Gallery Images
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_gallry_images() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );
		$resp       = '';

		if ( $post != false && $post != null ) {
			$id = isset( $post['id'] ) ? esc_attr( $post['id'] ) : 0;

			if ( ! empty( $id ) ) {
				$gallery_images = $dbhandler->get_all_result(
					'GALLERY',
					'*',
					array(
						'module_type' => 'SERVICE',
						'module_id'   => $id,
					),
					'results'
				);

				if ( ! empty( $gallery_images ) && isset( $gallery_images[0] ) ) {
					$resp .= $bmrequests->bm_fetch_gallery_images_response( $gallery_images[0] );
				}
			}

			if ( empty( $resp ) ) {
				$resp .= '<div class="textcenter">' . esc_html__( 'No Images Found', 'service-booking' ) . '</div>';
			}

			$data['status'] = true;
			$data['data']   = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_service_gallry_images()


	/**
	 * Fetch all Services by category ids
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_services_by_categories() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$data       = array();

		$services = $dbhandler->get_results_with_join(
			array( 'SERVICE', 's' ),
			's.*',
			array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			),
			array(
				's.is_service_front' => array( '=' => 1 ),
			),
			'results',
			0,
			false,
			null,
			false,
			'AND (c.cat_in_front = 1 OR s.service_category = 0)'
		);

		$available_services = array();

		if ( ! empty( $services ) ) {
			$response_parts = array_map(
				function ( $service ) use ( $bmrequests, $date ) {
					return $bmrequests->bm_fetch_service_response( $service, $date );
				},
				$services
			);

			$filtered_response = array_filter( $response_parts );

			if ( ! empty( $filtered_response ) ) {
				$available_services[] = $filtered_response;
			}
		}

		if ( ! empty( $available_services ) ) {
			$flat_services = array_merge( ...$available_services );
			$resp          = implode( '', $flat_services );
		} else {
			$resp = '<div class="ajax-no-records">'
				. esc_html__( 'No Bookable Services Found', 'service-booking' )
				. '</div>';
		}

		$data['data'] = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_all_services_by_categories()


	/**
	 * Filter Services
	 *
	 * @author Darpan
	 */
	public function bm_filter_services() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();
		$services   = array();
		$resp       = '';

		if ( $post != false && $post != null ) {
			$ids     = isset( $post['ids'] ) ? $post['ids'] : array();
			$date    = isset( $post['date'] ) ? $post['date'] : '';
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) && $post['limit'] !== '-1' ? $post['limit'] : false;
			$order   = isset( $post['order'] ) ? $post['order'] : '';
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) && $post['limit'] !== '-1' ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$sorting = $this->bm_fetch_service_sort_order( $order );

			if ( ! empty( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) ) ) {
				$sorting = $this->bm_fetch_service_sort_order( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) );
			}

			$tables = array( 'SERVICE', 's' );
			$joins  = array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			);

			$where = array(
				's.is_service_front' => array( '=' => 1 ),
			);

			$additional = 'AND (c.cat_in_front = 1 OR s.service_category = 0)';

			$columns = 's.*';

			if ( ! empty( $ids ) ) {
				$where = array_merge( $where, array( 's.id' => array( 'IN' => $ids ) ) );
			}

			$total_records = $dbhandler->get_results_with_join(
				$tables,
				'COUNT(*) as total',
				$joins,
				$where,
				'var',
				0,
				false,
				null,
				false,
				$additional
			);

			$services = $dbhandler->get_results_with_join(
				$tables,
				$columns,
				$joins,
				$where,
				'results',
				$offset,
				$limit,
				$sorting['sort_by'],
				$sorting['descending'],
				$additional
			);

			$available_services = array();

			if ( ! empty( $services ) ) {
				$response_parts = array_map(
					function ( $service ) use ( $bmrequests, $date ) {
						return $bmrequests->bm_fetch_service_response( $service, $date );
					},
					$services
				);

				$filtered_response = array_filter( $response_parts );

				if ( ! empty( $filtered_response ) ) {
					$available_services[] = $filtered_response;
				}
			}

			if ( ! empty( $available_services ) ) {
				$flat_services = array_merge( ...$available_services );
				$resp          = implode( '', $flat_services );
			} else {
				$resp = '<div class="ajax-no-records">'
					. esc_html__( 'No Bookable Services Found', 'service-booking' )
					. '</div>';
			}

			$num_of_pages = $limit !== false ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base );

			$data['data']       = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
			$data['pagination'] = $pagination;
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_filter_services()


	/**
	 * Filter Categories
	 *
	 * @author Darpan
	 */
	public function bm_filter_categories() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();
		$services   = array();
		$resp       = '';

		if ( $post != false && $post != null ) {
			$ids     = isset( $post['ids'] ) ? $post['ids'] : array();
			$svc_ids = isset( $post['svc_ids'] ) ? $post['svc_ids'] : array();
			$date    = isset( $post['date'] ) ? $post['date'] : '';
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) && $post['limit'] !== '-1' ? $post['limit'] : false;
			$order   = isset( $post['order'] ) ? $post['order'] : '';
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) && $post['limit'] !== '-1' ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$sorting = $this->bm_fetch_service_sort_order( $order );

			if ( ! empty( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) ) ) {
				$sorting = $this->bm_fetch_service_sort_order( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) );
			}

			$tables = array( 'SERVICE', 's' );
			$joins  = array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			);

			$where = array(
				's.is_service_front' => array( '=' => 1 ),
			);

			$additional = 'AND (c.cat_in_front = 1 OR s.service_category = 0)';

			$columns = 's.*';

			if ( ! empty( $ids ) ) {
				$where = array_merge( $where, array( 's.service_category' => array( 'IN' => $ids ) ) );
			}

			if ( ! empty( $svc_ids ) ) {
				$where = array_merge( $where, array( 's.id' => array( 'IN' => $svc_ids ) ) );
			}

			$total_records = $dbhandler->get_results_with_join(
				$tables,
				'COUNT(*) as total',
				$joins,
				$where,
				'var',
				0,
				false,
				null,
				false,
				$additional
			);

			$services = $dbhandler->get_results_with_join(
				$tables,
				$columns,
				$joins,
				$where,
				'results',
				$offset,
				$limit,
				$sorting['sort_by'],
				$sorting['descending'],
				$additional
			);

			$available_services = array();

			if ( ! empty( $services ) ) {
				$response_parts = array_map(
					function ( $service ) use ( $bmrequests, $date ) {
						return $bmrequests->bm_fetch_service_response( $service, $date );
					},
					$services
				);

				$filtered_response = array_filter( $response_parts );

				if ( ! empty( $filtered_response ) ) {
					$available_services[] = $filtered_response;
				}
			}

			if ( ! empty( $available_services ) ) {
				$flat_services = array_merge( ...$available_services );
				$resp          = implode( '', $flat_services );
			} else {
				$resp = '<div class="ajax-no-records">'
					. esc_html__( 'No Bookable Services Found', 'service-booking' )
					. '</div>';
			}

			$num_of_pages = $limit !== false ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base );

			$data['data']       = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
			$data['pagination'] = $pagination;
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_filter_categories()


	/**
	 * Filter Services by Category
	 *
	 * @author Darpan
	 */
	public function bm_filter_service_by_category() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();
		$resp       = '';

		if ( $post != false && $post != null ) {
			$category_ids = isset( $post['ids'] ) ? $post['ids'] : array();
			$date         = isset( $post['date'] ) ? $post['date'] : '';
			$order        = isset( $post['order'] ) ? $post['order'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['base'] ) && $post['limit'] !== '-1' ? $post['limit'] : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) && $post['limit'] !== '-1' ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$sorting      = $this->bm_fetch_service_sort_order( $order );

			if ( ! empty( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) ) ) {
				$sorting = $this->bm_fetch_service_sort_order( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) );
			}

			$tables = array( 'SERVICE', 's' );
			$joins  = array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			);

			$where = array(
				's.is_service_front' => array( '=' => 1 ),
			);

			$additional = 'AND (c.cat_in_front = 1 OR s.service_category = 0)';

			$columns = 's.*';

			if ( ! empty( $category_ids ) ) {
				$where = array_merge( $where, array( 's.service_category' => array( 'IN' => $category_ids ) ) );
			}

			$total_records = $dbhandler->get_results_with_join(
				$tables,
				'COUNT(*) as total',
				$joins,
				$where,
				'var',
				0,
				false,
				null,
				false,
				$additional
			);

			$services = $dbhandler->get_results_with_join(
				$tables,
				$columns,
				$joins,
				$where,
				'results',
				$offset,
				$limit,
				$sorting['sort_by'],
				$sorting['descending'],
				$additional
			);

			$available_services = array();

			if ( ! empty( $services ) ) {
				$response_parts = array_map(
					function ( $service ) use ( $bmrequests, $date ) {
						return $bmrequests->bm_fetch_service_response( $service, $date );
					},
					$services
				);

				$filtered_response = array_filter( $response_parts );

				if ( ! empty( $filtered_response ) ) {
					$available_services[] = $filtered_response;
				}
			}

			if ( ! empty( $available_services ) ) {
				$flat_services = array_merge( ...$available_services );
				$resp          = implode( '', $flat_services );
			} else {
				$resp = '<div class="ajax-no-records">'
					. esc_html__( 'No Bookable Services Found', 'service-booking' )
					. '</div>';
			}

			$num_of_pages = $limit !== false ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base );

			$data['data']       = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
			$data['pagination'] = $pagination;

			echo wp_json_encode( $data );
			die;
		} //end if
	}//end bm_filter_service_by_category()


	/**
	 * Filter Services by Category
	 *
	 * @author Darpan
	 */
	public function bm_filter_services_by_service_id() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();
		$resp       = '';

		if ( $post != false && $post != null ) {
			$service_ids = isset( $post['ids'] ) ? $post['ids'] : array();
			$cat_ids     = isset( $post['cat_ids'] ) ? $post['cat_ids'] : array();
			$date        = isset( $post['date'] ) ? $post['date'] : '';
			$order       = isset( $post['order'] ) ? $post['order'] : '';
			$base        = isset( $post['base'] ) ? $post['base'] : '';
			$limit       = isset( $post['base'] ) && $post['limit'] !== '-1' ? $post['limit'] : false;
			$pagenum     = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset      = isset( $post['limit'] ) && $post['limit'] !== '-1' ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$sorting     = $this->bm_fetch_service_sort_order( $order );

			if ( ! empty( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) ) ) {
				$sorting = $this->bm_fetch_service_sort_order( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ) );
			}

			$tables = array( 'SERVICE', 's' );
			$joins  = array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			);

			$where = array(
				's.is_service_front' => array( '=' => 1 ),
			);

			$additional = 'AND (c.cat_in_front = 1 OR s.service_category = 0)';

			$columns = 's.*';

			if ( ! empty( $service_ids ) ) {
				$where = array_merge( $where, array( 's.id' => array( 'IN' => $service_ids ) ) );
			}

			if ( ! empty( $cat_ids ) ) {
				$where = array_merge( $where, array( 's.service_category' => array( 'IN' => $cat_ids ) ) );
			}

			$total_records = $dbhandler->get_results_with_join(
				$tables,
				'COUNT(*) as total',
				$joins,
				$where,
				'var',
				0,
				false,
				null,
				false,
				$additional
			);

			$services = $dbhandler->get_results_with_join(
				$tables,
				$columns,
				$joins,
				$where,
				'results',
				$offset,
				$limit,
				$sorting['sort_by'],
				$sorting['descending'],
				$additional
			);

			$available_services = array();

			if ( ! empty( $services ) ) {
				$response_parts = array_map(
					function ( $service ) use ( $bmrequests, $date ) {
						return $bmrequests->bm_fetch_service_response( $service, $date );
					},
					$services
				);

				$filtered_response = array_filter( $response_parts );

				if ( ! empty( $filtered_response ) ) {
					$available_services[] = $filtered_response;
				}
			}

			if ( ! empty( $available_services ) ) {
				$flat_services = array_merge( ...$available_services );
				$resp          = implode( '', $flat_services );
			} else {
				$resp = '<div class="ajax-no-records">'
					. esc_html__( 'No Bookable Services Found', 'service-booking' )
					. '</div>';
			}

			$num_of_pages = $limit !== false ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base );

			$data['data']       = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
			$data['pagination'] = $pagination;

			echo wp_json_encode( $data );
			die;
		} //end if
	}//end bm_filter_services_by_service_id()


	/**
	 * Fetch Service time slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_time_slots() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests         = new BM_Request();
		$post               = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp               = '';
		$max_cap_text       = __( 'Cap Left: ', 'service-booking' );
		$cap_exhausted_text = __( 'Capacity exhausted', 'service-booking' );
		$slot_min_cap       = 0;
		$data               = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id   = isset( $post['id'] ) ? $post['id'] : 0;
			$date = isset( $post['date'] ) ? $post['date'] : '';
			$type = isset( $post['type'] ) ? $post['type'] : '';

			$hidden_cap_left_text = false;

			if ( $id > 0 && ! empty( $date ) ) {
				$service_settings = ( new BM_DBhandler() )->get_value( 'SERVICE', 'service_settings', $id, 'id' );
				$service_settings = ! empty( $service_settings ) ? maybe_unserialize( $service_settings ) : array();
				if ( isset( $service_settings['show_cap_left_text'] ) && $service_settings['show_cap_left_text'] == 0 ) {
					$hidden_cap_left_text = true;
				}

				$total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id( $id );
				$is_bookable      = $bmrequests->bm_service_is_bookable( $id, $date );
				$is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $id, $date );

				if ( $is_variable_slot == 1 ) {
					$first_from_slot = $bmrequests->bm_fetch_variable_first_from_slot( $id, $date );
					$slot_min_cap    = $bmrequests->bm_fetch_variable_slot_min_cap_by_service_id_and_slot_id( $id, 1, $first_from_slot, $date );
				} elseif ( $is_variable_slot == 0 ) {
					$slot_min_cap = $bmrequests->bm_fetch_non_variable_slot_min_cap_by_service_id_and_slot_id( $id, 1 );
				}

				if ( $total_time_slots == 1 ) {
					$time_slot = $bmrequests->bm_fetch_single_time_slot_by_service_id( $id, $date );

					if ( $time_slot !== '-1' && $time_slot !== '0' && $is_bookable ) {
						if ( $type == 'service_by_category' ) {
							$resp .= $bmrequests->bm_fetch_service_calendar_html( $date );
						}

						$slot_text        = preg_match( '#\((.*?)\)#', $time_slot, $singleslot_text );
						$single_slot_text = $slot_text > 0 ? $singleslot_text[1] : '';

						$match     = preg_match_all( '/\<span class\="single_slot_timings"\>(.*?)\<\/span\>/', $time_slot, $slot_details );
						$time_slot = $match > 0 ? $slot_details[1][0] : $time_slot;

						if ( strpos( $time_slot, ' - ' ) !== false ) {
							$booking_slots = explode( ' - ', $time_slot );
							$from          = $bmrequests->bm_twenty_fourhrs_format( $booking_slots[0] );
						} else {
							$from = $bmrequests->bm_twenty_fourhrs_format( $time_slot );
						}

						if ( ! empty( $from ) ) {
							$is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $id, $date );
							$slot_info        = $bmrequests->bm_fetch_slot_details( $id, $from, $date, $total_time_slots, 0, $is_variable_slot );
							$resp            .= '<div class="single_slot_text">' . $single_slot_text;
							$resp            .= ' (' . $time_slot . ')';
							$resp            .= '<span class="service_capacity_left_text">';

							if ( $slot_info['capacity_left'] > 0 ) {
								if ( ! $hidden_cap_left_text ) {
									$resp .= '(' . ( $max_cap_text . $slot_info['capacity_left'] ) . ')';
								} else {
									$resp .= ' ' . $slot_info['capacity_left'];
								}
							} elseif ( $slot_info['capacity_left'] <= 0 ) {
								if ( ! $hidden_cap_left_text ) {
									$resp .= '(' . ( $cap_exhausted_text ) . ')</span>';
								} else {
									$resp .= ' 0';
								}
							}

							$resp .= '</span>';
							$resp .= '<span class="line_below"></span>';
							$resp .= '</div>';

							$dbhandler = new BM_DBhandler();

							$primary_color      = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
							$contrast           = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
							$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
							$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );

							$total_extra_rows = array();
							$global_extras    = $dbhandler->get_all_result(
								'EXTRA',
								'*',
								array(
									'is_global' => 1,
									'is_extra_service_front' => 1,
								),
								'results'
							);
							$extra_rows       = $dbhandler->get_all_result(
								'EXTRA',
								'*',
								array(
									'is_global'  => 0,
									'service_id' => $id,
									'is_extra_service_front' => 1,
								),
								'results'
							);

							if ( ! empty( $extra_rows ) && ! empty( $global_extras ) ) {
								$total_extra_rows = array_merge( $global_extras, $extra_rows );
							} elseif ( empty( $extra_rows ) && ! empty( $global_extras ) ) {
								$total_extra_rows = $global_extras;
							} elseif ( ! empty( $extra_rows ) && empty( $global_extras ) ) {
								$total_extra_rows = $extra_rows;
							}

							if ( ! empty( $total_extra_rows ) ) {
								if ( $type == 'service_by_category' || $type == 'service_by_category2' ) {
									$link_class = 'get_svc_by_cat_extra_service';
								} elseif ( $type == 'home_page' ) {
									$link_class = 'get_extra_service';
								}
							} else {
								$wcmmrce_integration = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );
								$only_wcmmrce        = $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout', 0 );

								if ( $wcmmrce_integration == 1 && ( new WooCommerceService() )->is_enabled() ) {
									if ( $only_wcmmrce == 1 ) {
										if ( $type == 'service_by_category' ) {
											$link_class = 'get_svc_by_cat_checkout_form';
										} else {
											$link_class = 'get_checkout_form';
										}
									} elseif ( $type == 'service_by_category' ) {
											$link_class = 'get_svc_by_cat_checkout_options';
									} else {
										$link_class = 'get_checkout_options';
									}
								} elseif ( $type == 'service_by_category' ) {
										$link_class = 'get_svc_by_cat_checkout_form';
								} else {
									$link_class = 'get_checkout_form';
								}
							}

							if ( ! empty( $slot_info ) && isset( $slot_info['capacity_left'] ) && isset( $slot_info['slot_min_cap'] ) ) {
								$resp .= $bmrequests->bm_fetch_service_selection_response( $id, $slot_info['capacity_left'], $slot_info['slot_min_cap'] );
								if ( ! empty( $resp ) ) {
									if ( $slot_info['capacity_left'] <= 0 ) {
										$resp .= '<div class="bookbtnbar">';
										$resp .= '<div class="bookbtn readonly_div" id="select_slot_button" style="background:' . $svc_button_colour . '!important;">';
										$resp .= '<a href="#" id="' . $id . '" class="inactiveLink ' . $link_class . '" style="color:' . $svc_btn_txt_colour . '!important">';
										$resp .= $hidden_cap_left_text ? __( 'Proceed', 'service-booking' ) : __( 'Book', 'service-booking' ) . '</a>';
										$resp .= '</div></div>';
									} else {
										$resp .= '<div class="bookbtnbar">';
										$resp .= '<div class="bookbtn bgcolor textwhite text-center" id="select_slot_button" style="background:' . $svc_button_colour . '!important;">';
										$resp .= '<a href="#" id="' . $id . '" class="' . $link_class . '" style="color:' . $svc_btn_txt_colour . '!important">';
										$resp .= $hidden_cap_left_text ? __( 'Proceed', 'service-booking' ) : __( 'Book', 'service-booking' ) . '</a>';
										$resp .= '</div></div>';
									}
								}
							}
						} //end if
					} else {
						if ( $type == 'service_by_category' ) {
							$resp .= $bmrequests->bm_fetch_service_calendar_html( $date );
						}

						if ( $is_bookable == false ) {
							$resp .= '<div class="textcenter no_slots_class">' . esc_html__( 'Service Unavailable on selected Date.', 'service-booking' ) . '</div>';
						} elseif ( $time_slot == '-1' ) {
								$resp .= '<div class="textcenter" style="color:red;font-size:14px;">' . esc_html__( 'No slots available.', 'service-booking' ) . '</div>';
						} elseif ( $time_slot == '0' ) {
							$resp .= '<div class="textcenter" style="color:red;font-size:14px;">' . esc_html__( 'All slots booked.', 'service-booking' ) . '</div>';
						}
					} //end if
				} elseif ( $total_time_slots > 1 ) {
					if ( $type == 'service_by_category' ) {
						$resp .= $bmrequests->bm_fetch_service_calendar_html( $date );
					}

					if ( $is_bookable == false ) {
						$resp .= '<div class="textcenter" style="color:red;font-size:14px;">' . esc_html__( 'Service Unavailable on selected Date.', 'service-booking' ) . '</div>';
					} else {
						$resp .= $bmrequests->bm_fetch_service_time_slot_by_service_id( $post, '', $type );
					}
				} //end if

				if ( empty( $resp ) ) {
					$resp .= '<div class="textcenter">' . esc_html__( 'No results found.', 'service-booking' ) . '</div>';
				}

				$data['status'] = true;
			} //end if
		} //end if

		$data['min_cap'] = $slot_min_cap;
		$data['data']    = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_service_time_slots()


	/**
	 * Fetch Service calendar time slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_by_id_calendar_time_slots() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Failed security check', 'service-booking' ) ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $post ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid Booking Data', 'service-booking' ) ) );
		}

		$id   = $post['id'] ?? 0;
		$date = $post['date'] ?? '';

		if ( $id <= 0 || empty( $date ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Invalid Booking Data', 'service-booking' ) ) );
		}

		$total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id( $id );
		$is_bookable      = $bmrequests->bm_service_is_bookable( $id, $date );
		$is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $id, $date );

		if ( ! $is_bookable ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Service not bookable on selected date', 'service-booking' ) ) );
		}

		$slot_min_cap = $is_variable_slot
			? $bmrequests->bm_fetch_variable_slot_min_cap_by_service_id_and_slot_id( $id, 1, $bmrequests->bm_fetch_variable_first_from_slot( $id, $date ), $date )
			: $bmrequests->bm_fetch_non_variable_slot_min_cap_by_service_id_and_slot_id( $id, 1 );

		if ( $total_time_slots == 1 ) {
			$time_slot = $bmrequests->bm_fetch_single_time_slot_by_service_id( $id, $date );

			if ( in_array( $time_slot, array( '-1', '0' ), true ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'No slots available', 'service-booking' ) ) );
			}

			preg_match_all( '/\<span class\="single_slot_timings"\>(.*?)\<\/span\>/', $time_slot, $slot_details );
			$time_slot = ! empty( $slot_details[1][0] ) ? $slot_details[1][0] : $time_slot;

			$from = strpos( $time_slot, ' - ' ) !== false
				? $bmrequests->bm_twenty_fourhrs_format( explode( ' - ', $time_slot )[0] )
				: $bmrequests->bm_twenty_fourhrs_format( $time_slot );

			if ( empty( $from ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Invalid Booking Data', 'service-booking' ) ) );
			}

			$slot_info = $bmrequests->bm_fetch_slot_details( $id, $from, $date, $total_time_slots, 0, $is_variable_slot );

			if ( empty( $slot_info['capacity_left'] ) || empty( $slot_info['slot_min_cap'] ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'Invalid Booking Data', 'service-booking' ) ) );
			}

			if ( $slot_info['capacity_left'] <= 0 ) {
				wp_send_json_error( array( 'message' => esc_html__( 'No slots available', 'service-booking' ) ) );
			}
		} elseif ( $total_time_slots > 1 ) {
			$time_slots = $bmrequests->bm_fetch_service_time_slot_array_by_service_id( $post );

			if ( empty( $time_slots ) ) {
				wp_send_json_error( array( 'message' => esc_html__( 'No slots available', 'service-booking' ) ) );
			}
		}

		wp_send_json_success( array( 'is_bookable' => true ) );
	}//end bm_fetch_service_by_id_calendar_time_slots()


	/**
	 * Fetch extra services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_extra_service() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp       = '';

		if ( $post != false && $post != null ) {
			$resp = $bmrequests->bm_fetch_extra_service_response( $post, $resp );

			if ( empty( $resp ) ) {
				$resp .= '<div class="textcenter">' . esc_html__( 'No Records Found', 'service-booking' ) . '</div>';
			}
		}

		echo wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_fetch_extra_service()


	/**
	 * Fetch checkout options
	 *
	 * @author Darpan
	 */
	public function bm_fetch_available_checkout_options() {
			$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['type'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$resp = ( new BM_Request() )->bm_fetch_checkout_options_response( $post );

		wp_send_json_success( $resp );
	}//end bm_fetch_available_checkout_options()


	/**
	 * Fetch service sleection html
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_selection() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp       = '';

		if ( $post != false && $post != null ) {
			$id            = isset( $post['id'] ) ? $post['id'] : 0;
			$capacity_left = isset( $post['capacity_left'] ) ? $post['capacity_left'] : 0;
			$mincap        = isset( $post['mincap'] ) ? $post['mincap'] : 0;

			if ( $id > 0 && $capacity_left > 0 && $mincap > 0 ) {
				$resp = $bmrequests->bm_fetch_service_selection_response( $id, $capacity_left, $mincap );
			}

			if ( empty( $resp ) ) {
				$resp .= '<div class="textcenter">' . esc_html__( 'No Records Found', 'service-booking' ) . '</div>';
			}
		}

		echo wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_fetch_service_selection()


	/**
	 * Fetch user form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_user_form() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp       = '';

		if ( $post != false && $post != null ) {
			$id   = isset( $post['id'] ) ? $post['id'] : 0;
			$date = isset( $post['date'] ) ? $post['date'] : '';

			if ( ! empty( $id ) && ! empty( $date ) ) {
				$total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id( $id );

				if ( $total_time_slots == 1 ) {
					$time_slot = $bmrequests->bm_fetch_single_time_slot_by_service_id( $id, $date );

					if ( $time_slot !== '-1' && $time_slot !== '0' ) {
						$post['time_slot'] = $time_slot;
					} elseif ( $time_slot == '-1' ) {
							$resp .= '<div class="textcenter">' . esc_html__( 'No slots available', 'service-booking' ) . '</div>';
					} elseif ( $time_slot == '0' ) {
						$resp .= '<div class="textcenter">' . esc_html__( 'All slots booked', 'service-booking' ) . '</div>';
					}
				}

				$resp = $bmrequests->bm_fetch_user_form( $post, $resp );
			}

			if ( empty( $resp ) ) {
				$resp .= '<div class="textcenter">' . esc_html__( 'No Records Found', 'service-booking' ) . '</div>';
			}
		} //end if

		echo wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_fetch_user_form()



	/**
	 * Fetch order info and redirect to checkout form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_info_and_redirect_to_checkout() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests     = new BM_Request();
		$post           = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$booking_fields = array();
		$resp           = '';
		$data['status'] = 'error';

		if ( $post != false && $post != null ) {
			$id   = isset( $post['id'] ) ? $post['id'] : 0;
			$date = isset( $post['date'] ) ? $post['date'] : '';

			$checkout_option = isset( $post['checkout_option'] ) ? $post['checkout_option'] : '';

			if ( $id > 0 && ! empty( $date ) ) {
				if ( $bmrequests->bm_service_is_bookable( $id, $date ) ) {
					$dbhandler = new BM_DBhandler();
					$wc_id     = $dbhandler->get_value( 'SERVICE', 'wc_product', $id, 'id' );

					$total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id( $id );
					$stopsales        = $bmrequests->bm_fetch_service_stopsales_by_service_id( $id, $date );
					$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
					$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );

					if ( ! empty( $stopsales ) ) {
						$stopSalesHours   = floor( $stopsales );
						$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

						if ( $bmrequests->bm_has_dynamic_stopsales_for_date( $id, $date ) ) {
							$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
						} else {
							$endDateTime = clone $now;
						}

						$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
						$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
					}

					if ( $total_time_slots == 1 ) {
						$time_slot = $bmrequests->bm_fetch_single_time_slot_by_service_id( $id, $date );

						if ( $time_slot !== '-1' && $time_slot !== '0' ) {
							$post['time_slot'] = $time_slot;
						}
					}

					$is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $id, $date );
					$booking_fields   = $bmrequests->bm_fetch_order_info( $post );
					$booked_slots     = $bmrequests->bm_fetch_booked_slot_info_from_booking_data( $booking_fields );
					$from_slot        = ! empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $booked_slots['from'] : '';
					$startSlot        = new DateTime( $date . ' ' . $from_slot, new DateTimeZone( $timezone ) );
					$startSlot        = $startSlot->format( 'Y-m-d H:i' );

					if ( ! empty( $booking_fields ) && ! empty( $from_slot ) ) {
						$booking_string = $bmrequests->bm_generate_unique_code( '', 'FLEXIB', 15 );
						$dbhandler->bm_save_data_to_transient( $booking_string, $booking_fields, 72 );
						$total_service_booked = isset( $booking_fields['total_service_booking'] ) ? $booking_fields['total_service_booking'] : 0;
						$bookable_extra       = $bmrequests->bm_is_selected_extra_service_bookable( $booking_string );

						$slot_info = $bmrequests->bm_fetch_slot_details( $id, $from_slot, $date, $total_time_slots, $total_service_booked, $is_variable_slot, array( 'slot_min_cap', 'slot_capacity_left_after_booking' ) );

						if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
							$data['data'] = '<div class="textcenter">' . esc_html__( 'Can not book on the selected slot', 'service-booking' ) . '</div>';
						} elseif ( isset( $slot_info['slot_capacity_left_after_booking'] ) && ( $slot_info['slot_capacity_left_after_booking'] < 0 ) ) {
							$data['data'] = '<div class="textcenter">' . esc_html__( 'Not enough capacity left, try booking another slot or service !!', 'service-booking' ) . '</div>';
						} elseif ( ( $bookable_extra == false ) ) {
							$data['data'] = '<div class="textcenter">' . esc_html__( 'One or more extra services does not have enough capacity, choose another !!', 'service-booking' ) . '</div>';
						} elseif ( $checkout_option == 'woocommerce_checkout' && $wc_id <= 0 ) {
							$data['data'] = '<div class="textcenter">' . esc_html__( 'The ordered product is not linked with any WooCommerce product !!', 'service-booking' ) . '</div>';
						} elseif ( isset( $slot_info['slot_capacity_left_after_booking'] ) && isset( $slot_info['slot_min_cap'] ) && ( $slot_info['slot_capacity_left_after_booking'] >= 0 ) && ( $total_service_booked % $slot_info['slot_min_cap'] == 0 ) ) {
							$wcmmrce_intn = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );
							$only_wcmmrce = $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout', 0 );

							if ( ( $checkout_option == 'woocommerce_checkout' && $wc_id > 0 ) || ( $wcmmrce_intn == 1 && $only_wcmmrce == 1 ) ) {
								$woocommerceservice = new WooCommerceService();
								if ( $woocommerceservice->is_enabled() ) {
									$is_booked_extra_wc_linked = $bmrequests->bm_is_selected_extra_service_wc_linked( $booking_string );
									$checkout_page_url         = $woocommerceservice->get_woo_commerce_checkout_url();

									if ( $is_booked_extra_wc_linked ) {
										if ( ! empty( $checkout_page_url ) ) {
											$add_to_cart = $woocommerceservice->add_to_cart( $booking_fields, $booking_string );

											if ( $add_to_cart ) {
												$data['data']   = $checkout_page_url;
												$data['status'] = 'success';
											} else {
												$data['data'] = '<div class="textcenter">' . esc_html__( 'Products could not be added to woocommerce cart !!', 'service-booking' ) . '</div>';
											}
										} else {
											$data['data'] = '<div class="textcenter">' . esc_html__( 'Checkout url could not be generated !!', 'service-booking' ) . '</div>';
										}
									} else {
										$data['data'] = '<div class="textcenter">' . esc_html__( 'One or more extra products are not linked with woocommerce !!', 'service-booking' ) . '</div>';
									}
								} else {
									$data['data'] = '<div class="textcenter">' . esc_html__( 'WooCommerce plugin not activated !!', 'service-booking' ) . '</div>';
								}
							} elseif ( defined( 'STRIPE_SECRET_KEY' ) ) {
									$stripe_payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

								if ( $stripe_payment_processor->isConnected() ) {
									$checkout_page_url = $dbhandler->bm_fetch_page_by_title( 'Flexibooking Checkout', OBJECT, 'page', 'url' );

									if ( ! empty( $checkout_page_url ) ) {
										/**$terms_and_conditions_message = $bmrequests->bm_fetch_payment_message_for_checkout_page( $booking_string );
										$dbhandler->update_global_option_value( 'bm_flexibooking_checkout_payment_conditions_message', $terms_and_conditions_message );*/

										$separator = ( strpos( $checkout_page_url, '?' ) !== false ) ? '&' : '?';

										$data['data']   = $checkout_page_url . $separator . 'flexi_booking=' . $booking_string;
										$data['status'] = 'success';
									} else {
										if ( isset( $time_slot ) && $time_slot == '-1' ) {
											$resp = '<div class="textcenter">' . esc_html__( 'No slots available, try booking another slot or service !!', 'service-booking' ) . '</div>';
										} elseif ( isset( $time_slot ) && $time_slot == '0' ) {
											$resp = '<div class="textcenter">' . esc_html__( 'All slots booked, try booking another slot or service !!', 'service-booking' ) . '</div>';
										} else {
											$resp = '<div class="textcenter">' . esc_html__( 'Error fetching booking info !!', 'service-booking' ) . '</div>';
										}
										$data['data'] = $resp;
									}
								} else {
									$data['data'] = '<div class="textcenter">' . esc_html__( 'Payment Gateway Server Error !!', 'service-booking' ) . '</div>';
								}
							} else {
								$data['data'] = '<div class="textcenter">' . esc_html__( 'Payment Gateway Not Enabled !!', 'service-booking' ) . '</div>';
							}
						} else {
							$data['data'] = '<div class="textcenter">' . esc_html__( 'Service is not bookable !!', 'service-booking' ) . '</div>';
						}
					} else {
						$data['data'] = '<div class="textcenter">' . esc_html__( 'Error fetching booking info !!', 'service-booking' ) . '</div>';
					}
				} else {
					$data['data'] = '<div class="textcenter">' . esc_html__( 'Service is not bookable !!', 'service-booking' ) . '</div>';
				}
			} else {
				$data['data'] = '<div class="textcenter">' . esc_html__( 'Error fetching booking info !!', 'service-booking' ) . '</div>';
			}
		} //end if

		echo wp_json_encode( $data );
		die;
	} //end bm_fetch_checkout_form()


	/**
	 * Fetch data from booking form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_data() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$dbhandler  = new BM_DBhandler();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp       = '';
		$data       = array();

        if ( $post != false && $post != null ) {
            if ( !empty( $post ) && isset( $post['other_data'] ) && isset( $post['field_data'] ) ) {
                $service_id = isset( $post['other_data']['service_id'] ) ? $post['other_data']['service_id'] : '';
                if ( isset( $post['other_data']['terms_conditions'] ) ) {
                    unset( $post['other_data']['terms_conditions'] );
                }

                if ( isset( $post['other_data']['terms_conditions1'] ) ) {
                    unset( $post['other_data']['terms_conditions1'] );
                }

				if ( ! empty( $service_id ) ) {
					$dbhandler->update_global_option_value( 'bm_flexibooking_latest_order_data', $post['other_data'] );
					$dbhandler->update_global_option_value( 'bm_flexibooking_latest_booking_form_data', $post['field_data'] );
					$checkout_page_url = $dbhandler->bm_fetch_page_by_title( 'Flexibooking Checkout', OBJECT, 'page', 'url' );

					if ( ! empty( $checkout_page_url ) ) {
						$data['data']   = $checkout_page_url;
						$data['status'] = 'success';
					} else {
						$resp .= '<div class="textcenter">' . esc_html__( 'Something Went wrong, try again !!', 'service-booking' ) . '</div>';

						$data['status'] = 'error';
						$data['data']   = $resp;
					}
				}
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_booking_data()



	/**
	 * Fetch data from checkout form and redirect to payment
	 *
	 * @author Darpan
	 */
	public function bm_fetch_checkout_data_redirect_to_payment() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests      = new BM_Request();
		$dbhandler       = new BM_DBhandler();
		$post            = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp            = '';
		$data            = array();
		$billing_details = array();
		$transient_data  = array();

		if ( $post != false && $post != null ) {
			if ( ! empty( $post ) && isset( $post['checkout_data'] ) && ! empty( $post['checkout_data'] ) && isset( $post['booking_data'] ) && ! empty( $post['booking_data'] ) ) {
				$booking_fields = $dbhandler->bm_fetch_data_from_transient( $post['booking_data'] );

				if ( ! empty( $booking_fields ) ) {
					$id   = isset( $booking_fields['service_id'] ) ? $booking_fields['service_id'] : 0;
					$date = isset( $booking_fields['booking_date'] ) ? $booking_fields['booking_date'] : '';

					if ( ! empty( $id ) && ! empty( $date ) ) {
						if ( $bmrequests->bm_service_is_bookable( $id, $date ) ) {
							if ( isset( $post['checkout_data']['other_data']['terms_conditions'] ) ) {
								unset( $post['checkout_data']['other_data']['terms_conditions'] );
							}

							if ( isset( $post['checkout_data']['billing_details'] ) ) {
								$checkout_string = $bmrequests->bm_generate_unique_code( '', 'FLEXIC', 15 );

								$transient_data['billing'] = $post['checkout_data']['billing_details'];

								if ( is_array( $post['checkout_data']['billing_details'] ) ) {
									foreach ( $post['checkout_data']['billing_details'] as $key => $value ) {
										$field_name = $dbhandler->get_value( 'FIELDS', 'field_name', $key, 'field_key' );

										if ( ! empty( $field_name ) ) {
											$billing_details[ $field_name ] = $value;
										}
									}

									if ( ! empty( $billing_details ) ) {
										$post['checkout_data']['billing_details'] = $billing_details;
									}
								}
							}

							$transient_data['checkout'] = $post['checkout_data'];
							$dbhandler->bm_save_data_to_transient( $checkout_string, $transient_data, 72 );

							$gift_data = isset( $post['checkout_data']['gift_details'] ) ? $post['checkout_data']['gift_details'] : array();

							if ( isset( $post['checkout_data']['other_data']['is_gift'] ) ) {
								$gift_data['is_gift'] = $post['checkout_data']['other_data']['is_gift'];
								unset( $post['checkout_data']['other_data']['is_gift'] );
							}

							$gift_key = base64_encode( $post['booking_data'] );
							$dbhandler->bm_save_data_to_transient( $gift_key, $gift_data, 72 );

							if ( defined( 'STRIPE_SECRET_KEY' ) ) {
								$stripe_payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

								if ( $stripe_payment_processor->isConnected() ) {
									$data = $bmrequests->bm_check_payment_type_and_return_data( $post['booking_data'], $checkout_string );

									if ( empty( $data ) ) {
										$resp = '<div class="textcenter">' . esc_html__( 'Error Fetching Payment Info !!', 'service-booking' ) . '</div>';

										$data['status'] = 'error';
										$data['data']   = $resp;
									}
								} else {
									$resp = '<div class="textcenter">' . esc_html__( 'Payment Gateway Server Error !!', 'service-booking' ) . '</div>';

									$data['status'] = 'error';
									$data['data']   = $resp;
								}
							} else {
								$resp = '<div class="textcenter">' . esc_html__( 'Payment Gateway Not Enabled !!', 'service-booking' ) . '</div>';

								$data['status'] = 'error';
								$data['data']   = $resp;
							}
						} else {
							$resp = '<div class="textcenter">' . esc_html__( 'Service is Not Bookable !!', 'service-booking' ) . '</div>';

							$data['status'] = 'error';
							$data['data']   = $resp;
						}
					} else {
						$resp = '<div class="textcenter">' . esc_html__( 'Error Fetching Booking Info !!', 'service-booking' ) . '</div>';

						$data['status'] = 'error';
						$data['data']   = $resp;
					}
				} else {
					$resp = '<div class="textcenter">' . esc_html__( 'Error Fetching Booking Info !!', 'service-booking' ) . '</div>';

					$data['status'] = 'error';
					$data['data']   = $resp;
				}
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_checkout_data_redirect_to_payment()


	/**
	 * Fetch Order of services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_sort_order( $sort_order ) {
			$data = array();

		if ( ! empty( $sort_order ) ) {
			switch ( $sort_order ) {
				case 'name_asc':
					$sort_by    = 'service_name';
					$descending = false;
					break;
				case 'name_desc':
					$sort_by    = 'service_name';
					$descending = 'DESC';
					break;
				case 'position_asc':
					$sort_by    = 'service_position';
					$descending = false;
					break;
				case 'position_desc':
					$sort_by    = 'service_position';
					$descending = 'DESC';
					break;
				default:
					$sort_by    = 'service_name';
					$descending = false;
					break;
			} //end switch

			if ( isset( $sort_by ) && isset( $descending ) ) {
				$data['sort_by']    = $sort_by;
				$data['descending'] = $descending;
			}
		} //end if

		return $data;
	}//end bm_fetch_service_sort_order()


	/**
	 * Fill field values in woocommerce checkout form
	 *
	 * @author Darpan
	 */
	public function bm_set_checkout_form_value( $value, $input ) {
		if ( is_user_logged_in() ) {
			return $value;
		}

		$fields = ( new BM_DBhandler() )->get_all_result( 'FIELDS', '*', 1, 'results' );

		if ( empty( $fields ) ) {
			return $value;
		}

		foreach ( $fields as $field ) {
			if ( ! isset( $field->field_options, $field->woocommerce_field ) ) {
				continue;
			}

			if ( $input === $field->woocommerce_field ) {
				$field_options = maybe_unserialize( $field->field_options );
				$default_value = $field_options['default_value'] ?? '';

				if ( ! empty( $default_value ) ) {
					if ( $input == 'billing_email' ) {
						$value = sanitize_email( $default_value );
					} else {
						$value = sanitize_text_field( $default_value );
					}
				}
			}
		}

		return $value;
	}//end bm_set_checkout_form_value()


	/**
	 * Set international codes for phone fields in booking form
	 *
	 * @author Darpan
	 */
	public function bm_set_intl_input() {
			$bmrequests = new BM_Request();
		$key            = $bmrequests->bm_fetch_all_tel_type_field_keys_with_active_intl_code();
		echo wp_json_encode( $key );
		die;
	}//end bm_set_intl_input()


	/**
	 * Prices in Service Calender on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_service_prices() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id        = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false || $id != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );

			if ( ! empty( $service ) ) {
				$data['status']          = true;
				$data['default_price']   = isset( $service->default_price ) ? $service->default_price : 0;
				$data['variable_module'] = isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();
				$data['variable_price']  = isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();
				$data['unavailability']  = isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();
				$data['gbl_unavlabilty'] = $dbhandler->get_global_option_value( 'bm_global_unavailability' );
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_get_service_prices()


	/**
	 * Redirect after WooCommerce order is placed
	 *
	 * @author Darpan
	 */
	public function bm_redirect_after_order( $order_id ) {
		$booking_details = array();
		$order           = wc_get_order( $order_id );

		if ( $order ) {
			$booking_details['booking_details']['order_number']   = $order->get_order_number();
			$booking_details['booking_details']['order_date']     = $order->get_date_created()->format( 'Y-m-d' );
			$booking_details['booking_details']['email']          = $order->get_billing_email();
			$booking_details['booking_details']['total']          = $order->get_formatted_order_total();
			$booking_details['booking_details']['payment_method'] = $order->get_payment_method_title();

			$product_details = '';
			foreach ( $order->get_items() as $item ) {
				$product_id       = $item['product_id'];
				$product_name     = $item['name'];
				$product_price    = get_post_meta( $product_id, '_price', true );
				$product_quantity = $item['quantity'];
				$product_total    = $item['total'];

				$product_details .= '<tr>';
				$product_details .= '<td>' . $product_name . '</td>';
				$product_details .= '<td>' . $product_price . '</td>';
				$product_details .= '<td>' . $product_quantity . '</td>';
				$product_details .= '<td>' . $product_total . '</td>';
				$product_details .= '</tr>';
			}

			$booking_details['booking_details']['product_details'] = $product_details;
			$booking_details['booking_details']['billing_address'] = $order->get_formatted_billing_address();

			ob_start();
			include_once 'partials/booking-management-order-thankyou.php';
			$content = ob_get_contents();
			ob_end_clean();
			echo wp_kses( $content, $bmrequests->bm_fetch_expanded_allowed_tags() );
			exit;
		} //end if
	}//end bm_redirect_after_order()


	/**
	 * Fetch services by name
	 *
	 * @author Darpan
	 */
	public function bm_fetch_services_by_name() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$html       = '';

		if ( $post != false && $post != null ) {
			$search_term = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$categories  = isset( $post['categories'] ) ? $post['categories'] : '';

			if ( isset( $categories ) ) {
				$categories = explode( ',', $categories );

				if ( ! empty( $categories ) && is_array( $categories ) ) {
					foreach ( $categories as $key => $category ) {
						if ( $category == '' ) {
							unset( $categories[ $key ] );
						}
					}
				}
			}

			if ( ! empty( $search_term ) ) {
				if ( ! empty( $categories ) ) {
					$categories = implode( ',', array_values( $categories ) );
					$additional = "AND service_category in($categories) AND service_name LIKE '" . $search_term . "%'";
					$services   = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );
				}
			} elseif ( ! empty( $categories ) ) {
					$categories = implode( ',', array_values( $categories ) );
					$additional = "AND service_category in($categories)";
					$services   = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );
			}

			if ( isset( $services ) && ! empty( $services ) ) {
				foreach ( $services as $service ) {
					$html .= $bmrequests->bm_fetch_service_by_category_response( $service );
				}
			} else {
				$html .= '<div class="textcenter svc_by_cat_search">' . esc_html__( 'No Services Found', 'service-booking' ) . '</div>';
			}
		} //end if

		echo wp_json_encode( $html );
		die;
	}//end bm_fetch_services_by_name()


	/**
	 * Process payment
	 *
	 * @author Darpan
	 */
	public function bm_process_final_payment() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => 'error' );

		if ( $post != false && $post != null ) {
			$booking_key  = isset( $post['booking'] ) ? $post['booking'] : '';
			$checkout_key = isset( $post['checkout'] ) ? $post['checkout'] : '';
			$method_id    = isset( $post['paymentMethod'] ) ? $post['paymentMethod'] : '';

			if ( ! empty( $booking_key ) && ! empty( $checkout_key ) && ! empty( $method_id ) ) {
				if ( defined( 'STRIPE_SECRET_KEY' ) ) {
					$stripe_payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

					if ( $stripe_payment_processor->isConnected() ) {
						$data['status'] = $bmrequests->bm_process_payment_data( $booking_key, $checkout_key, $method_id );

						if ( in_array( $data['status'], array( 'success', 'requires_capture', 'succeeded' ), true ) ) {
							$data['data'] = wp_kses_post( base64_decode( $dbhandler->get_global_option_value( 'bm_client_secret' . $booking_key ) ) );
						}
					} else {
						$resp = __( 'Payment Gateway Server Error !!', 'service-booking' );

						$data['data'] = wp_kses_post( $resp );
					}
				} else {
					$resp = __( 'Payment Gateway Not Enabled !!', 'service-booking' );

					$data['data'] = wp_kses_post( $resp );
				}
			} else {
				$resp = __( 'Could Not Capture Booking Info !!', 'service-booking' );

				$data['data'] = wp_kses_post( $resp );
			}
		} else {
			$resp = __( 'Could Not Capture Booking Info !!', 'service-booking' );

			$data['data'] = wp_kses_post( $resp );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_process_final_payment()


	/**
	 * Save payment data
	 *
	 * @author Darpan
	 */
	public function bm_save_final_payment() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler      = new BM_DBhandler();
		$bmrequests     = new BM_Request();
		$post           = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$process_status = 'error';

		if ( $post != false && $post != null ) {
			$booking_key  = isset( $post['booking'] ) ? $post['booking'] : '';
			$checkout_key = isset( $post['checkout'] ) ? $post['checkout'] : '';

			if ( ! empty( $booking_key ) && ! empty( $checkout_key ) ) {
				if ( defined( 'STRIPE_SECRET_KEY' ) ) {
					$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

					if ( $payment_processor->isConnected() ) {
						$process_status = $bmrequests->bm_save_payment_data( $booking_key, $checkout_key );
					}
				}
			}
		}

		echo wp_kses_post( $process_status );
		die;
	}//end bm_save_final_payment()


	/**
	 * Check for payment refund
	 *
	 * @author Darpan
	 */
	public function bm_check_for_refund_for_failed_payment() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp       = '';
		$status     = 'error';

		if ( $post != false && $post != null ) {
			$booking_key  = isset( $post['booking'] ) ? $post['booking'] : '';
			$checkout_key = isset( $post['checkout'] ) ? $post['checkout'] : '';
			$is_cancelled = $bmrequests->bm_cancel_payment_intent_for_failed_payment( $booking_key, $checkout_key );

			if ( $is_cancelled ) {
				$status = 'cancelled';
			}
		}

		if ( $status == 'cancelled' ) {
			$resp = __( 'Could not save transaction data, any amount charged is initiated for refund and may take 5–10 business days for funds to settle !!', 'service-booking' );
		} else {
			$resp = __( 'Transaction failed !!', 'service-booking' );
		}

		$dbhandler->bm_save_data_to_transient( 'bm_latest_payment_status_message' . $booking_key, $resp, 0.5 );

		echo wp_kses_post( $status );
		die;
	}//end bm_check_for_refund_for_failed_payment()


	/**
	 * Check if payment session has expired
	 *
	 * @author Darpan
	 */
	public function bm_check_if_payment_session_has_expired() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$booking_key = filter_input( INPUT_POST, 'booking_key' );

		$bmrequests = new BM_Request();
		$status     = $bmrequests->bm_is_session_expired( "flexi_current_payment_session_$booking_key" ) ? 1 : 0;

		echo wp_kses_post( $status );
		die;
	}//end bm_check_if_payment_session_has_expired()


	/**
	 * Check discount in checkout form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_age_data_and_check_discount() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => 'error' );

		if ( $post != false && $post != null ) {
			$booking_key = isset( $post['booking_key'] ) ? $post['booking_key'] : '';

			if ( ! empty( $booking_key ) ) {
				$data['status']            = $bmrequests->bm_fetch_age_type_booking_discounted_price( $post );
				$data['data']              = $bmrequests->bm_fetch_booked_service_info_for_checkout( $booking_key );
				$data['negative_discount'] = $dbhandler->get_global_option_value( 'negative_discount_' . $booking_key, 0 );
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_age_data_and_check_discount()


	/**
	 * Reset discount in checkout form
	 *
	 * @author Darpan
	 */
	public function bm_reset_discounted_value() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$booking_key = filter_input( INPUT_POST, 'booking_key' );
		$data        = array( 'status' => 'error' );

		if ( $booking_key != false && $booking_key != null ) {
			$dbhandler->update_global_option_value( 'discount_' . $booking_key, 0 );
			$dbhandler->update_global_option_value( 'negative_discount_' . $booking_key, 0 );
			$dbhandler->bm_delete_transient( 'discounted_' . $booking_key );
			$dbhandler->bm_delete_transient( 'price_module_discount_' . $booking_key );
			$dbhandler->bm_delete_transient( 'flexi_age_wise_discount_' . $booking_key );
			$dbhandler->bm_delete_transient( 'flexi_age_wise_total_price_' . $booking_key );
			$dbhandler->bm_delete_transient( 'flexi_total_person_discounted_' . $booking_key );

			$coupon_applied  = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
			$coupon_discount = ! empty( $coupon_applied ) ? floatval( array_sum( array_column( $coupon_applied, 'coupon_discount' ) ) ) : 0;

			if ( $coupon_discount > 0 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );

				$order_data['discount']    = $coupon_discount;
				$order_data['subtotal']    = $order_data['total_cost'];
				$order_data['total_cost'] -= $order_data['discount'];

				$dbhandler->update_global_option_value( 'discount_' . $booking_key, 1 );
				$dbhandler->bm_save_data_to_transient( 'discounted_' . $booking_key, $order_data, 72 );
			}

			$data['data']   = $bmrequests->bm_fetch_booked_service_info_for_checkout( $booking_key );
			$data['status'] = 'success';
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_reset_discounted_value()


	/**
	 * Free booking save
	 *
	 * @author Darpan
	 */
	public function bm_discounted_and_free_checkout_save() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();

		if ( $post != false && $post != null ) {
			$data = $bmrequests->bm_process_free_payment_data( $post );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_discounted_and_free_checkout_save()


	/**
	 * Fullcalendar events callbak
	 *
	 * @author Darpan
	 */
	public function bm_filter_fullcalendar_events_callback() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $post ) || ! isset( $post['start'], $post['end'] ) ) {
			wp_send_json_error( __( 'Invalid data', 'service-booking' ) );
			return;
		}

		$start_date = sanitize_text_field( $post['start'] );
		$end_date   = sanitize_text_field( $post['end'] );

		if ( empty( $start_date ) || empty( $end_date ) ) {
			wp_send_json_error( __( 'Invalid data', 'service-booking' ) );
			return;
		}

		$service_ids  = isset( $post['services'] ) ? array_map( 'intval', $post['services'] ) : array();
		$category_ids = isset( $post['categories'] ) ? array_map( 'intval', $post['categories'] ) : array();
		$cat_ids      = isset( $post['cat_ids'] ) ? array_map( 'intval', $post['cat_ids'] ) : array();

		$where = array(
			's.is_service_front' => array( '=' => 1 ),
			's.service_status'   => array( '=' => 1 ),
		);

		$additional = '';
		if ( ! empty( $cat_ids ) ) {
			if ( in_array( 0, $cat_ids ) ) {
				$where['s.service_category'] = array(
					'IN' => $cat_ids,
					'OR' => array( '=' => 0 ),
				);
				$additional                  = 'OR s.service_category = 0';
			} else {
				$where['s.service_category'] = array( 'IN' => $cat_ids );
				$where['c.cat_status']       = array( '=' => 1 );
			}
		} elseif ( ! empty( $category_ids ) ) {
			if ( in_array( 0, $category_ids ) ) {
				$where['s.service_category'] = array(
					'IN' => $category_ids,
					'OR' => array( '=' => 0 ),
				);
				$additional                  = 'OR s.service_category = 0';
			} else {
				$where['s.service_category'] = array( 'IN' => $category_ids );
				$where['c.cat_status']       = array( '=' => 1 );
			}
		} else {
			$where['c.cat_status'] = array( '=' => 1 );
			$additional            = 'OR s.service_category = 0';
		}

		if ( ! empty( $service_ids ) ) {
			$where['s.id'] = array( 'IN' => $service_ids );
		}

		$services = $dbhandler->get_results_with_join(
			array( 'SERVICE', 's' ),
			's.id, s.service_name, s.service_calendar_title, s.service_category, s.service_duration, s.default_price, s.service_desc, s.service_position',
			array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			),
			$where,
			'results',
			0,
			false,
			's.service_position',
			false,
			$additional
		);

		$timezone   = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$date_range = new DatePeriod(
			new DateTime( $start_date, new DateTimeZone( $timezone ) ),
			new DateInterval( 'P1D' ),
			( new DateTime( $end_date, new DateTimeZone( $timezone ) ) )->modify( '+1 day' )
		);
		$today      = ( new DateTime( 'now', new DateTimeZone( $timezone ) ) )->format( 'Y-m-d' );

		$filtered_events = array();

		if ( ! empty( $services ) && is_array( $services ) ) {
			foreach ( $services as $service ) {
				foreach ( $date_range as $date ) {
					$current_date = $date->format( 'Y-m-d' );

					$has_slots = ! empty(
						$bmrequests->bm_fetch_service_time_slot_array_by_service_id(
							array(
								'id'   => $service->id,
								'date' => $current_date,
							)
						)
					);

					$is_past_date = $current_date < $today;
					$event_class  = $is_past_date ? 'past-date-event' : '';

					if ( $bmrequests->bm_service_is_bookable( $service->id, $current_date ) && $has_slots ) {
						$category_name = $service->service_category ?
							$bmrequests->bm_fetch_category_name_by_category_id( $service->service_category ) :
							__( 'Uncategorized', 'service-booking' );

						$filtered_events[] = array(
							'id'             => $service->id ?? 0,
							'title'          => $service->service_name ?? '',
							'calendar_title' => $service->service_calendar_title ?? '',
							'start'          => $current_date ?? '',
							'allDay'         => true,
							'className'      => $event_class,
							'extendedProps'  => array(
								'duration'         => $bmrequests->bm_fetch_float_to_time_string( $service->service_duration ),
								'price'            => esc_html( $bmrequests->bm_fetch_service_price_by_service_id_and_date( $service->id, $current_date, 'global_format' ) ),
								'category'         => $service->service_category ?? 0,
								'service_position' => $service->service_position,
								'categoryName'     => $category_name,
								'full_desc'        => isset( $service->service_desc ) && ! empty( $service->service_desc ) ? wp_kses_post( stripslashes( $service->service_desc ) ) : '',
								'image'            => esc_url( $bmrequests->bm_fetch_image_url_or_guid( $service->id, 'SERVICE', 'url' ) ),
								'date'             => $current_date,
								'serviceId'        => $service->id ?? 0,
								'isPastDate'       => $is_past_date,
							),
						);
					}
				}
			}
		}

		wp_send_json_success(
			array(
				'events'  => $filtered_events,
				'message' => sprintf( __( 'Showing %d available services', 'service-booking' ), count( $filtered_events ) ),
			)
		);
	}//end bm_filter_fullcalendar_events_callback()


	public function bm_qr_checkin_process() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$reference = filter_input( INPUT_POST, 'booking_reference', FILTER_SANITIZE_STRING );
		if ( ! $reference ) {
			wp_send_json_error( __( 'Invalid QR code.', 'service-booking' ) );
			return;
		}

		$db         = new BM_DBhandler();
		$booking_id = $db->get_value( 'BOOKING', 'id', $reference, 'booking_key' );

		if ( ! $booking_id ) {
			wp_send_json_error( __( 'Booking not found.', 'service-booking' ) );
			return;
		}

		$success = $this->bm_mark_booking_checked_in( (int) $booking_id, $db );

		if ( $success ) {
			wp_send_json_success( array( 'message' => __( 'Booking checked in successfully.', 'service-booking' ) ) );
		} else {
			wp_send_json_error( __( 'Unable to check in booking. It may already be checked in or inactive.', 'service-booking' ) );
		}
	}


	private function bm_mark_booking_checked_in( int $booking_id, BM_DBhandler $db ): bool {
		$data = array(
			'qr_scanned'   => 1,
			'status'       => 'checked_in',
			'qr_token'     => $db->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' ),
			'booking_id'   => $booking_id,
			'checkin_time' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
			'updated_at'   => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
		);

		return $db->update_row( 'CHECKIN', 'booking_id', $booking_id, $data );
	}


	public function bm_handle_qr_verification() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$qr_data = isset( $_POST['qr_data'] ) ? filter_input( INPUT_POST, 'qr_data' ) : '';
		if ( ! $qr_data ) {
			wp_send_json_error( __( 'Invalid QR code', 'service-booking' ) );
			return;
		}

		$dbhandler = new BM_DBhandler();
		$booking   = $dbhandler->get_row( 'BOOKING', $qr_data, 'booking_key' );

		if ( ! $booking ) {
			wp_send_json_error( __( 'Booking not found', 'service-booking' ) );
			return;
		}

		$checkin = $dbhandler->get_row( 'CHECKIN', $booking->id, 'booking_id' );

		if ( ! $checkin ) {
			$checkin_data = array(
				'booking_id' => $booking->id,
				'qr_token'   => $booking->booking_key,
				'status'     => 'pending',
			);
			$checkin_id   = $dbhandler->insert_row( 'CHECKIN', $checkin_data );
			$checkin      = $dbhandler->get_row( 'CHECKIN', $checkin_id );
		}

		// if ( strtotime( $booking->booking_date ) < time() ) {
		// $dbhandler->update_row(
		// 'CHECKIN',
		// 'id',
		// $checkin->id,
		// array(
		// 'status'          => 'expired',
		// 'service_expired' => 1,
		// )
		// );
		// wp_send_json_error( __( 'Service date has expired', 'service-booking' ) );
		// return;
		// }

		// if ( $checkin->status === 'checked_in' ) {
		// wp_send_json_error( __( 'Ticket already used', 'service-booking' ) );
		// return;
		// }

		$updated = $dbhandler->update_row(
			'CHECKIN',
			'id',
			$checkin->id,
			array(
				'booking_id'   => $booking->id ?? 0,
				'status'       => 'checked_in',
				'qr_scanned'   => 1,
				'qr_token'     => $qr_data,
				'checkin_time' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
				'updated_at'   => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
			)
		);

		wp_send_json_success();
	}


	/**
	 * Timeslot fullcalendar events callbak
	 *
	 * @author Darpan
	 */
	public function bm_filter_timeslot_fullcalendar_events_callback() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $post ) || ! isset( $post['start'], $post['end'] ) ) {
			wp_send_json_error( __( 'Invalid data', 'service-booking' ) );
			return;
		}

		$start_date = sanitize_text_field( $post['start'] );
		$end_date   = sanitize_text_field( $post['end'] );

		$timezone      = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$show_duration = $dbhandler->get_global_option_value( 'bm_show_frontend_service_duration', 0 );

		$start_date_obj = new DateTime( $start_date, new DateTimeZone( $timezone ) );
		$end_date_obj   = new DateTime( $end_date, new DateTimeZone( $timezone ) );

		$date_range = new DatePeriod(
			$start_date_obj,
			new DateInterval( 'P1D' ),
			$end_date_obj->modify( '+1 day' )
		);

		if ( empty( $start_date ) || empty( $end_date ) ) {
			wp_send_json_error( __( 'Invalid data', 'service-booking' ) );
			return;
		}

		$service_ids  = isset( $post['services'] ) ? array_map( 'intval', $post['services'] ) : array();
		$category_ids = isset( $post['categories'] ) ? array_map( 'intval', $post['categories'] ) : array();
		$cat_ids      = isset( $post['cat_ids'] ) ? array_map( 'intval', $post['cat_ids'] ) : array();

		$where = array(
			's.is_service_front' => array( '=' => 1 ),
			's.service_status'   => array( '=' => 1 ),
		);

		$additional = '';
		if ( ! empty( $cat_ids ) ) {
			if ( in_array( 0, $cat_ids ) ) {
				$where['s.service_category'] = array(
					'IN' => $cat_ids,
					'OR' => array( '=' => 0 ),
				);
				$additional                  = 'OR s.service_category = 0';
			} else {
				$where['s.service_category'] = array( 'IN' => $cat_ids );
				$where['c.cat_status']       = array( '=' => 1 );
			}
		} elseif ( ! empty( $category_ids ) ) {
			if ( in_array( 0, $category_ids ) ) {
				$where['s.service_category'] = array(
					'IN' => $category_ids,
					'OR' => array( '=' => 0 ),
				);
				$additional                  = 'OR s.service_category = 0';
			} else {
				$where['s.service_category'] = array( 'IN' => $category_ids );
				$where['c.cat_status']       = array( '=' => 1 );
			}
		} else {
			$where['c.cat_status'] = array( '=' => 1 );
			$additional            = 'OR s.service_category = 0';
		}

		if ( ! empty( $service_ids ) ) {
			$where['s.id'] = array( 'IN' => $service_ids );
		}

		$services = $dbhandler->get_results_with_join(
			array( 'SERVICE', 's' ),
			's.id, s.service_name, s.service_calendar_title, s.service_category, s.service_duration, s.default_price, s.service_desc, s.service_position, s.service_settings',
			array(
				array(
					'table' => 'CATEGORY',
					'alias' => 'c',
					'on'    => 's.service_category = c.id',
					'type'  => 'LEFT',
				),
			),
			$where,
			'results',
			0,
			false,
			's.service_position',
			false,
			$additional
		);

		$filtered_events = array();
		$unique_services = array();
		$svc_ids         = array();

		if ( ! empty( $services ) && is_array( $services ) ) {
			foreach ( $services as $service ) {
				foreach ( $date_range as $date ) {
					$current_date = $date->format( 'Y-m-d' );
					$today        = ( new DateTime( 'now', new DateTimeZone( $timezone ) ) )->format( 'Y-m-d' );

					$is_past_date = $current_date < $today;
					$event_class  = $is_past_date ? 'past-date-event' : '';

					$category_name = $service->service_category ?
						$bmrequests->bm_fetch_category_name_by_category_id( $service->service_category ) :
						__( 'Uncategorized', 'service-booking' );

					if ( $bmrequests->bm_service_is_bookable( $service->id, $current_date ) ) {
						$timeslots = $bmrequests->bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date( $service->id, $current_date );
					} else {
						$timeslots = array();
					}

					$service_settings = isset( $service->service_settings ) && ! empty( $service->service_settings ) ? maybe_unserialize( $service->service_settings ) : array();

					$show_svc_duration = 0;
					if ( $show_duration > 0 ) {
						$show_svc_duration = isset( $service_settings['show_service_duration'] ) ? $service_settings['show_service_duration'] : 0;
					}

					$filtered_events[] = array(
						'id'             => $service->id ?? 0,
						'title'          => $service->service_name ?? '',
						'calendar_title' => $service->service_calendar_title ?? '',
						'start'          => $current_date ?? '',
						'allDay'         => true,
						'className'      => $event_class,
						'extendedProps'  => array(
							'duration'         => $bmrequests->bm_fetch_float_to_time_string( $service->service_duration ),
							'timeslots'        => $timeslots,
							'service_position' => $service->service_position,
							'price'            => esc_html( $bmrequests->bm_fetch_service_price_by_service_id_and_date( $service->id, $current_date, 'global_format' ) ),
							'category'         => $service->service_category ?? 0,
							'categoryName'     => $category_name,
							'full_desc'        => isset( $service->service_desc ) && ! empty( $service->service_desc ) ? wp_kses_post( stripslashes( $service->service_desc ) ) : '',
							'image'            => esc_url( $bmrequests->bm_fetch_image_url_or_guid( $service->id, 'SERVICE', 'url' ) ),
							'date'             => $current_date,
							'serviceId'        => $service->id ?? 0,
							'show_duration'    => $show_svc_duration,
							'isPastDate'       => $is_past_date,
						),
					);
				}
			}

			foreach ( $filtered_events as $event ) {
				$service_id = isset( $event['id'] ) ? $event['id'] : 0;

				if ( ! in_array( $service_id, $svc_ids ) ) {
					$svc_ids[]         = $service_id;
					$unique_services[] = array(
						'id'             => $service_id,
						'title'          => $event['title'],
						'calendar_title' => $event['calendar_title'],
						'extendedProps'  => $event['extendedProps'],
					);
				}
			}
		}

		wp_send_json_success(
			array(
				'events'   => $filtered_events,
				'services' => $unique_services,
				'message'  => sprintf( __( 'Showing %d available services', 'service-booking' ), count( $filtered_events ) ),
			)
		);
	}//end bm_filter_timeslot_fullcalendar_events_callback()


	/**
	 * Timeslot fullcalendar dialog content callbak
	 *
	 * @author Darpan
	 */
	public function bm_fetch_timeslot_dialog_content() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || ! isset( $post['service_id'], $post['capacity_left'], $post['mincap'] ) ) {
			wp_send_json_error( __( 'Invalid data', 'service-booking' ) );
			return;
		}

		$service_id    = isset( $post['service_id'] ) ? intval( $post['service_id'] ) : 0;
		$capacity_left = isset( $post['capacity_left'] ) ? intval( $post['capacity_left'] ) : 0;
		$mincap        = isset( $post['mincap'] ) ? intval( $post['mincap'] ) : 0;
		$date          = isset( $post['date'] ) ? $post['date'] : '';
		$time_slot     = isset( $post['time_slot_value'] ) ? $post['time_slot_value'] : '';

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$settings   = $dbhandler->get_value( 'SERVICE', 'service_settings', $service_id, 'id' );
		$settings   = ! empty( $settings ) ? maybe_unserialize( $settings ) : array();

		$contrast           = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
		$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );

		$hidden_no_persons = false;
		if ( isset( $settings['show_no_of_persons_box'] ) && $settings['show_no_of_persons_box'] == 0 ) {
			$hidden_no_persons = true;
		}

		$show_person_text = $bmrequests->bm_is_service_per_person_text_shown( $service_id );

		$counter_html = '';

		$counter_html .= '<div class="timeslot-info">';
		$counter_html .= '<span class="timeslot-counter-info"><span class="info-text">' . __( 'Selected Date', 'service-booking' ) . '</span>: ' . $bmrequests->bm_convert_date_format( $date, 'Y-m-d', 'd/m/y' ) . '</span>';
		$counter_html .= '<span class="timeslot-selected-slot-info"><span class="info-text">' . __( 'Selected Slot', 'service-booking' ) . '</span>: ' . $time_slot . '</span>';
		$counter_html .= '</div>';

		if ( $show_person_text ) {
			$counter_html .= '<span class="label_span"><span class="listed_service">' . __( 'Select number of persons', 'service-booking' ) . '</span>';
			$counter_html .= '<span class="svc_min_cap_text"> (' . __( 'minimum: ', 'service-booking' ) . '<span class="red_text">' . $mincap . '</span>)</span></span>';
		}

		$counter_html .= '<div class="timeslot-counter-container">';
		$counter_html .= '<input type="hidden" id="timeslot_booking_date" value="' . $date . '">';

		if ( $hidden_no_persons ) {
			$counter_html .= '<input type="hidden" id="timeslot-counter" min="' . $mincap . '" max="' . $capacity_left . '" value="' . $mincap . '" step="' . $mincap . '" class="timeslot-counter-input">';
		} else {
			$counter_html .= '<button class="timeslot-counter-btn minus" type="button">-</button>';
			$counter_html .= '<input type="number" id="timeslot-counter" min="' . $mincap . '" max="' . $capacity_left . '" value="' . $mincap . '" step="' . $mincap . '" class="timeslot-counter-input">';
			$counter_html .= '<button class="timeslot-counter-btn plus" type="button">+</button>';
		}

		$total_extra_rows = $dbhandler->get_all_result(
			'EXTRA',
			'*',
			1,
			'results',
			0,
			false,
			null,
			false,
			"is_global = 1 OR service_id = $service_id"
		);

		$link_class = '';
		if ( ! empty( $total_extra_rows ) ) {
			$link_class = 'get_extra_service';
		} else {
			$wcmmrce_integration = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );
			$only_wcmmrce        = $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout', 0 );
			$woo_enabled         = ( new WooCommerceService() )->is_enabled();

			if ( $wcmmrce_integration == 1 && $woo_enabled ) {
				if ( $only_wcmmrce == 1 ) {
					$link_class = 'get_checkout_form';
				} else {
					$link_class = 'get_checkout_options';
				}
			} else {
				$link_class = 'get_checkout_form';
			}
		}

		$counter_html .= '</div>';

		$counter_html .= '<div class="timeslot-dialog-footer">';
		$counter_html .= '<button id="' . $service_id . '" data-timeslot-booking="timeslot-booking" class="timeslot-proceed-btn ' . $link_class . '" style="color:' . $svc_btn_txt_colour . '!important">' . __( 'Book', 'service-booking' ) . '</button>';
		$counter_html .= '<button id="timeslot-cancel-btn" class="timeslot-cancel-btn">' . __( 'Cancel', 'service-booking' ) . '</button>';
		$counter_html .= '</div>';

		wp_send_json_success(
			array(
				'html'      => $counter_html,
				'min'       => $mincap,
				'max'       => $capacity_left,
				'step'      => $mincap,
				'has_error' => ( $capacity_left < $mincap ),
			)
		);
	}//end bm_fetch_timeslot_dialog_content()


	/**
	 * Fetch bookable Services by category id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_bookable_services_by_category_id_and_date() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$services = $bmrequests->bm_fetch_bookable_services_by_date_and_category_id( $post );

			$data['status']   = true;
			$data['services'] = $services;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_bookable_services_by_category_id_and_date()


	/**
	 * Fetch backend order service time slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_new_order_service_time_slots() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$slot_data  = array();

		if ( $post != false && $post != null ) {
			$id   = isset( $post['id'] ) ? $post['id'] : '';
			$date = isset( $post['date'] ) ? $post['date'] : '';

			if ( ! empty( $id ) && ! empty( $date ) ) {
				$total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id( $id );

				if ( $total_time_slots == 1 ) {
					$slot_data = $bmrequests->bm_fetch_backend_new_order_single_time_slot_by_service_id( $id, $date );
				} elseif ( $total_time_slots > 1 ) {
					$slot_data = $bmrequests->bm_fetch_backend_new_order_time_slot_by_service_id( $post );
				}
			}
		}

		echo wp_json_encode( $slot_data );
		die;
	}//end bm_fetch_new_order_service_time_slots()


	/**
	 * Fetch service min cap and cap left
	 *
	 * @author Darpan
	 */
	public function bm_fetch_mincap_and_cap_left() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$service_id = isset( $post['id'] ) && ! empty( $post['id'] ) ? $post['id'] : 0;
			$date       = isset( $post['date'] ) && ! empty( $post['date'] ) ? $post['date'] : '';

			if ( $service_id !== 0 && ! empty( $date ) ) {
				$svc_total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id( $service_id );
				$is_variable_slot     = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );

				if ( isset( $post['slots'] ) ) {
					if ( strpos( $post['slots'], ' - ' ) !== false ) {
						$booking_slots = explode( ' - ', $post['slots'] );
						$from          = $bmrequests->bm_twenty_fourhrs_format( $booking_slots[0] );
					} else {
						$from = $bmrequests->bm_twenty_fourhrs_format( $post['slots'] );
					}
				}

				$slot_info = $bmrequests->bm_fetch_slot_details( $service_id, $from, $date, $svc_total_time_slots, 0, $is_variable_slot, array( 'slot_min_cap', 'capacity_left' ) );

				if ( ! empty( $slot_info ) && isset( $slot_info['capacity_left'] ) && ( $slot_info['capacity_left'] > 0 ) ) {
					$data['status']    = true;
					$data['slot_info'] = $slot_info;
				}
			}
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_mincap_and_cap_left()

	/**
	 * Fetch service price for backend orders
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_price_for_add_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$service_id = isset( $post['id'] ) && ! empty( $post['id'] ) ? $post['id'] : 0;
			$date       = isset( $post['date'] ) && ! empty( $post['date'] ) ? $post['date'] : '';

			$data['status'] = true;
			$data['price']  = $bmrequests->bm_fetch_new_order_service_price_by_service_id_and_date( $service_id, $date );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_service_price_for_add_order()


	/**
	 * Fetch service price for backend orders
	 *
	 * @author Darpan
	 */
	public function bm_fetch_price_discount_module_for_backend_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$service_id = isset( $post['id'] ) && ! empty( $post['id'] ) ? $post['id'] : 0;
			$date       = isset( $post['date'] ) && ! empty( $post['date'] ) ? $post['date'] : '';

			$data['status'] = true;
			$data['html']   = $bmrequests->bm_fetch_price_discount_module_box_for_backend_order( $service_id, $date );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_price_discount_module_for_backend_order()


	/**
	 * Check discount in checkout form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_age_data_and_check_backend_discount() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => 'error' );

		if ( $post != false && $post != null ) {
			$booking_key = isset( $post['booking_key'] ) ? $post['booking_key'] : '';

			if ( ! empty( $booking_key ) ) {
				$data['status']            = $bmrequests->bm_fetch_backend_age_type_booking_discounted_price( $post );
				$data['data']              = $bmrequests->bm_fetch_order_price_info_after_discount( $booking_key );
				$data['negative_discount'] = $dbhandler->get_global_option_value( 'negative_discount_' . $booking_key, 0 );
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_age_data_and_check_backend_discount()


	/**
	 * Reset discount in checkout form
	 *
	 * @author Darpan
	 */
	public function bm_reset_backend_discounted_value() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$booking_key = filter_input( INPUT_POST, 'booking_key' );
		$data        = array( 'status' => 'error' );

		if ( $booking_key != false && $booking_key != null ) {
			$dbhandler->update_global_option_value( 'discount_' . $booking_key, 0 );
			$dbhandler->update_global_option_value( 'negative_discount_' . $booking_key, 0 );
			$dbhandler->bm_delete_transient( 'discounted_' . $booking_key );
			$dbhandler->bm_delete_transient( 'flexi_age_wise_discount_' . $booking_key );
			$dbhandler->bm_delete_transient( 'flexi_age_wise_total_price_' . $booking_key );
			$dbhandler->bm_delete_transient( 'flexi_total_person_discounted_' . $booking_key );
			$data['data']   = $bmrequests->bm_fetch_order_price_info_after_discount( $booking_key );
			$data['status'] = 'success';
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_reset_backend_discounted_value()


	/**
	 * Add custom attachmets to woocommerce mail
	 *
	 * @author Darpan
	 */
	public function bm_add_custom_attachments_to_woocommerce_email( $attachments, $email_id, $order ) {
		if ( is_a( $order, 'WC_Order' ) ) {
			$order_id         = $order->get_id();
			$flexi_booking_id = get_post_meta( $order_id, '_flexi_booking_id', true );

			if ( ! $flexi_booking_id || $flexi_booking_id <= 0 ) {
				return $attachments;
			}

			$bmrequests          = new BM_Request();
			$customer_attachment = $bmrequests->bm_get_customer_details_attachment( (int) $flexi_booking_id );
			$order_attachment    = $bmrequests->bm_get_order_details_attachment( (int) $flexi_booking_id );

			if ( $customer_attachment && file_exists( $customer_attachment ) ) {
				$attachments[] = $customer_attachment;
			}

			if ( $order_attachment && file_exists( $order_attachment ) ) {
				$attachments[] = $order_attachment;
			}
		}

		return $attachments;
	}//end bm_add_custom_attachments_to_woocommerce_email()


	/**
	 * Add custom attachmets to woocommerce mail
	 *
	 * @author Darpan
	 */
	public function flexibooking_add_checkout_body_class_to_woocommerce_checkout( $classes ) {
		if ( class_exists( 'WooCommerce' ) && function_exists( 'is_checkout' ) && is_checkout() && ! is_wc_endpoint_url() ) {
			$classes[] = 'flexi-woo-custom-checkout';
		}
		return $classes;
	}



	/**
	 * Disable cart quantity change
	 *
	 * @author Darpan
	 */
	public function bm_disable_quantity_change_for_plugin_products( $product_quantity, $cart_item_key, $cart_item ) {
		if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
			return $cart_item['quantity'];
		}

		return $product_quantity;
	}//end bm_disable_quantity_change_for_plugin_products()


	/**
	 * Disable cart product remove link
	 *
	 * @author Darpan
	 */
	public function bm_disable_remove_link_for_plugin_products( $remove_link, $cart_item_key ) {
			$cart = WC()->cart->get_cart();

		if ( isset( $cart[ $cart_item_key ]['added_by_flexibooking'] ) && $cart[ $cart_item_key ]['added_by_flexibooking'] ) {
			return '';
		}

		return $remove_link;
	}//end bm_disable_remove_link_for_plugin_products()


	/**
	 * Update booking data on woocommerce order cancellation
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_booking_data_on_order_cancellation( $order_id ) {
		if ( ! $order_id || $order_id < 1 ) {
			return;
		}

		$post_type = get_post_type( $order_id );
		if ( ! in_array( $post_type, array( 'shop_order', 'shop_order_placehold' ), true ) ) {
			return;
		}

		$flexi_booking_id   = get_post_meta( $order_id, '_flexi_booking_id', true );
		$flexi_service_date = get_post_meta( $order_id, '_flexi_service_date', true );

		if ( $flexi_booking_id > 0 && ! empty( $flexi_service_date ) ) {
			$dbhandler    = new BM_DBhandler();
			$booked_slots = $dbhandler->get_value( 'BOOKING', 'booking_slots', $flexi_booking_id, 'id' );
			$booked_slots = ! empty( $booked_slots ) ? maybe_unserialize( $booked_slots ) : array();

			if ( isset( $booked_slots['to'] ) ) {
				$flexi_service_date = $flexi_service_date . ' ' . $booked_slots['to'];

				$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
				$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
				$current_date = $today->format( 'Y-m-d' );
				$current_time = $today->format( 'H:i' );

				$currentDateTime = $current_date . ' ' . $current_time;

				if ( strtotime( $flexi_service_date ) > strtotime( $currentDateTime ) ) {
					( new BM_Request() )->bm_cancel_flexi_order( $flexi_booking_id );
				}
			}
		}
	}//end bm_update_flexi_booking_data_on_order_cancellation()


	/**
	 * Update booking data on woocommerce order refund
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_booking_data_on_order_refund( $order_id ) {
		if ( ! $order_id || $order_id < 1 ) {
			return;
		}

		$post_type = get_post_type( $order_id );
		if ( ! in_array( $post_type, array( 'shop_order', 'shop_order_placehold' ), true ) ) {
			return;
		}

		$order = wc_get_order( $order_id );

		if ( empty( $order ) ) {
			return;
		}

		$refund_id = null;
		if ( $order->get_payment_method() === 'stripe' ) {
			$refunds = $order->get_refunds();
			if ( ! empty( $refunds ) ) {
				$refund_id = $refunds[0]->get_id();
			}
		}

		$flexi_booking_id   = get_post_meta( $order_id, '_flexi_booking_id', true );
		$flexi_service_date = get_post_meta( $order_id, '_flexi_service_date', true );

		if ( $flexi_booking_id > 0 && ! empty( $flexi_service_date ) ) {
			$dbhandler    = new BM_DBhandler();
			$booked_slots = $dbhandler->get_value( 'BOOKING', 'booking_slots', $flexi_booking_id, 'id' );
			$booked_slots = ! empty( $booked_slots ) ? maybe_unserialize( $booked_slots ) : array();

			if ( isset( $booked_slots['to'] ) ) {
				$flexi_service_date = $flexi_service_date . ' ' . $booked_slots['to'];

				$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
				$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
				$current_date = $today->format( 'Y-m-d' );
				$current_time = $today->format( 'H:i' );

				$currentDateTime = $current_date . ' ' . $current_time;

				if ( strtotime( $flexi_service_date ) > strtotime( $currentDateTime ) ) {
					( new BM_Request() )->bm_update_flexi_order_status_as_refunded( $flexi_booking_id, $refund_id );
				}
			}
		}
	}//end bm_update_flexi_booking_data_on_order_refund()


	/**
	 * Update booking data on woocommerce order on hold
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_booking_data_on_order_on_hold( $order_id ) {
		if ( ! $order_id || $order_id < 1 ) {
			return;
		}

		$post_type = get_post_type( $order_id );
		if ( ! in_array( $post_type, array( 'shop_order', 'shop_order_placehold' ), true ) ) {
			return;
		}

		$flexi_booking_id   = get_post_meta( $order_id, '_flexi_booking_id', true );
		$flexi_service_date = get_post_meta( $order_id, '_flexi_service_date', true );

		if ( $flexi_booking_id > 0 && ! empty( $flexi_service_date ) ) {
			$dbhandler    = new BM_DBhandler();
			$booked_slots = $dbhandler->get_value( 'BOOKING', 'booking_slots', $flexi_booking_id, 'id' );
			$booked_slots = ! empty( $booked_slots ) ? maybe_unserialize( $booked_slots ) : array();

			if ( isset( $booked_slots['to'] ) ) {
				$flexi_service_date = $flexi_service_date . ' ' . $booked_slots['to'];

				$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
				$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
				$current_date = $today->format( 'Y-m-d' );
				$current_time = $today->format( 'H:i' );

				$currentDateTime = $current_date . ' ' . $current_time;

				if ( strtotime( $flexi_service_date ) > strtotime( $currentDateTime ) ) {
					( new BM_Request() )->bm_update_flexi_order_status_as_on_hold( $flexi_booking_id );
				}
			}
		}
	}//end bm_update_flexi_booking_data_on_order_on_hold()


	/**
	 * Disable quantity
	 *
	 * @author Darpan
	 */
	public function bm_disable_quantity_for_plugin_added_products( $sold_individually, $product ) {
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				return true;
			}
		}

		return $sold_individually;
	}//end bm_disable_quantity_for_plugin_added_products()


	/**
	 * Disable adding products
	 *
	 * @author Darpan
	 */
	public function bm_restrict_adding_products_if_added_through_flexi_plugin( $passed, $product_id, $quantity ) {
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				if ( $cart_item['product_id'] == $product_id ) {
					wc_add_notice( __( 'This product has already been added via the flexi plugin and cannot be added again.', 'service-booking' ), 'error' );
				} else {
					wc_add_notice( __( 'You cannot add more products as some items were added via the flexi booking.', 'service-booking' ), 'error' );
				}
				return false;
			}
		}

		return $passed;
	}//end bm_restrict_adding_products_if_added_through_flexi_plugin()


	/**
	 * Add service date info to woocommerce mail
	 *
	 * @author Darpan
	 */
	public function bm_add_service_date_to_email( $order, $sent_to_admin, $plain_text, $email ) {
		$order_id = 0;

		if ( isset( $order ) && is_a( $order, 'WC_Order' ) ) {
			$order_id = $order->get_id();
		}

		if ( ! $order_id ) {
			global $wp;
			if ( isset( $wp->query_vars['order-received'] ) ) {
				$order_id = absint( $wp->query_vars['order-received'] );
			}
		}

		if ( ! $order_id && isset( $_GET['key'] ) ) {
			$order_id = wc_get_order_id_by_order_key( sanitize_text_field( $_GET['key'] ) );
		}

		if ( ! $order_id || 'shop_order' !== get_post_type( $order_id ) ) {
			return;
		}

		$service_date = get_post_meta( $order_id, '_flexi_service_date', true );
		$booked_slots = get_post_meta( $order_id, '_flexi_booked_slots', true );
		$voucher_id   = get_post_meta( $order_id, '_flexi_voucher_id', true );
		$voucher_code = ( new BM_DBhandler() )->get_value( 'VOUCHERS', 'code', $voucher_id, 'id' );

		if ( $voucher_id && $voucher_code ) {
			echo '<div style="margin: 10px 0; padding: 10px; background: #f5f5f5; border: 1px solid #e0e0e0;">';
			echo '<p style="font-size: 1.1em;"><strong>' . sprintf( esc_html__( 'This is a gift and contains a gift voucher - %s', 'service-booking' ), esc_html( $voucher_code ) ) . '</strong></p>';
			echo '</div>';
		}

		if ( $service_date ) {
			echo '<div style="margin: 10px 0; padding: 10px; background: #f5f5f5; border: 1px solid #e0e0e0;">';
			echo '<p style="font-size: 1.1em;"><strong>' . esc_html__( 'Service Date:', 'service-booking' ) . '</strong> ' . esc_html( $service_date ) . '</p>';
			echo '</div>';
		}

		if ( $booked_slots ) {
			echo '<div style="margin: 10px 0; padding: 10px; background: #f5f5f5; border: 1px solid #e0e0e0;">';
			echo '<p style="font-size: 1.1em;"><strong>' . esc_html__( 'Slot Timing:', 'service-booking' ) . '</strong> ' . esc_html( $booked_slots ) . '</p>';
			echo '</div>';
		}
	}//end bm_add_service_date_to_email()


	/**
	 * Add service date info to woocommerce thank you page
	 *
	 * @author Darpan
	 */
	public function bm_display_service_date_in_thank_you_page( $thank_you_text, $order ) {
		$order_id = 0;

		if ( isset( $order ) && is_a( $order, 'WC_Order' ) ) {
			$order_id = $order->get_id();
		}

		if ( ! $order_id ) {
			global $wp;
			if ( isset( $wp->query_vars['order-received'] ) ) {
				$order_id = absint( $wp->query_vars['order-received'] );
			}
		}

		if ( ! $order_id && isset( $_GET['key'] ) ) {
			$order_id = wc_get_order_id_by_order_key( sanitize_text_field( $_GET['key'] ) );
		}

		if ( ! $order_id || 'shop_order' !== get_post_type( $order_id ) ) {
			return;
		}

		$service_date = get_post_meta( $order_id, '_flexi_service_date', true );
		$booked_slots = get_post_meta( $order_id, '_flexi_booked_slots', true );

		$service_info = '';
		if ( $service_date || $booked_slots ) {
			$service_info .= '<div class="woocommerce-flexi-service-info" style="margin-top:10px; padding:10px 0; font-size:18px;">';
			$service_info .= '<p><strong>' . esc_html__( 'Service Date:', 'service-booking' ) . '</strong> ' . esc_html( $service_date ) . '</p>';

			if ( $booked_slots ) {
				$service_info .= '<p><strong>' . esc_html__( 'Slot Timing:', 'service-booking' ) . '</strong> ' . esc_html( $booked_slots ) . '</p>';
			}

			$service_info .= '</div>';
		}

		return $thank_you_text . $service_info;
	}//end bm_display_service_date_in_thank_you_page()


	/**
	 * Add service date info to woocommerce view order page
	 *
	 * @author Darpan
	 */
	public function bm_display_service_date_in_view_order( $order ) {
		$order_id = 0;

		if ( isset( $order ) && is_a( $order, 'WC_Order' ) ) {
			$order_id = $order->get_id();
		}

		if ( ! $order_id ) {
			global $wp;
			if ( isset( $wp->query_vars['order-received'] ) ) {
				$order_id = absint( $wp->query_vars['order-received'] );
			}
		}

		if ( ! $order_id && isset( $_GET['key'] ) ) {
			$order_id = wc_get_order_id_by_order_key( sanitize_text_field( $_GET['key'] ) );
		}

		if ( ! $order_id || 'shop_order' !== get_post_type( $order_id ) ) {
			return;
		}

		if ( is_account_page() && get_query_var( 'view-order' ) ) {
			$service_date = get_post_meta( $order->get_id(), '_flexi_service_date', true );
			$booked_slots = get_post_meta( $order->get_id(), '_flexi_booked_slots', true );

			if ( $service_date || $booked_slots ) {
				echo '<div class="woocommerce-flexi-order-info" style="margin-top: 10px;">';
				echo '<p style="font-size: 1em; margin: 0;"><strong>Service Date:</strong> ' . esc_html( $service_date ) . '</p>';

				if ( $booked_slots ) {
					echo '<p style="font-size: 1em; margin: 0;"><strong>Time Slot:</strong> ' . esc_html( $booked_slots ) . '</p>';
				}

				echo '</div>';
			}
		}
	}//end bm_display_service_date_in_view_order()


	/**
	 * Add custom price to woocommerce products
	 *
	 * @author Darpan
	 */
	public function bm_adjust_cart_item_prices( $cart ) {
		if ( is_admin() || ! did_action( 'woocommerce_before_calculate_totals' ) ) {
			return;
		}

		foreach ( $cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				if ( isset( $cart_item['flexi_svc_price'] ) ) {
					$cart_item['data']->set_price( $cart_item['flexi_svc_price'] );
				}

				if ( isset( $cart_item['flexi_extra_svc_price'] ) ) {
					$extra_service_prices = $cart_item['flexi_extra_svc_price'];
					foreach ( $extra_service_prices as $key => $price ) {
						$cart_item['data']->set_price( $price );
					}
				}
			}
		}
	}//end bm_adjust_cart_item_prices()


	/**
	 * Save flexibooking keys to woocommerce order meta
	 *
	 * @author Darpan
	 */
	public function bm_save_flexibooking_order_keys_to_order_items( $item, $cart_item_key, $values, $order ) {
		if ( isset( $values['added_by_flexibooking'] ) ) {
			$item->add_meta_data( '_added_by_flexibooking', $values['added_by_flexibooking'], true );
		}

		if ( isset( $values['flexi_booking_key'] ) ) {
			$item->add_meta_data( '_flexi_booking_key', $values['flexi_booking_key'], true );
		}

		if ( isset( $values['flexi_checkout_key'] ) ) {
			$item->add_meta_data( '_flexi_checkout_key', $values['flexi_checkout_key'], true );
		}
		// trp language
		$current_lang = ( new Booking_Management_i18n() )->bm_search_language();

		$bookingKey = isset( $values['flexi_booking_key'] ) ? $values['flexi_booking_key'] : 0;

		$trp_lang = get_option( 'trp_lang_' . $bookingKey, false );

		if ( $current_lang && in_array( $current_lang, array( 'en', 'it' ) ) && $bookingKey != 0 && ! $trp_lang ) {
			update_option( 'trp_lang_' . $bookingKey, $current_lang );
		}
	}


	/**
	 * Remove custom keys when cart is emptied
	 *
	 * @author Darpan
	 */
	public function bm_clear_flexi_custom_order_keys() {
			$session = WC()->session;

		if ( $session ) {
			$session->set( 'added_by_flexibooking', null );
			$session->set( 'flexi_booking_key', null );
			$session->set( 'flexi_checkout_key', null );
		}
	}//end bm_clear_flexi_custom_order_keys()


	/**
	 * Add gift fields to woocommerce checkout
	 *
	 * @author Darpan
	 */
	public function bm_add_gift_fields_to_woocommerce_checkout( $checkout ) {
		if ( ! is_object( $checkout ) ) {
			return;
		}

		$show_gift_fields  = false;
		$flexi_booking_key = '';

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] === true ) {
				$flexi_booking_key = isset( $cart_item['flexi_booking_key'] ) ? $cart_item['flexi_booking_key'] : '';
				$show_gift_fields  = true;
				break;
			}
		}

		if ( ! $show_gift_fields || empty( $flexi_booking_key ) ) {
			return;
		}

		$is_allowed_as_gift = ( new BM_Request() )->bm_check_if_booked_service_is_allowed_as_gift( $flexi_booking_key );

		if ( ! $is_allowed_as_gift ) {
			return;
		}

		?>
		<div class="woocommerce_gift_fields">
			<div class="inputcheckgroup checkbox_and_radio_div">
				<input type="checkbox" name="is_gift" class="checkbox-size" id="is_gift" style="cursor: pointer;"/>
				<label for="is_gift" style="cursor:pointer;"><?php esc_html_e( 'Is it a gift?', 'service-booking' ); ?></label>
			</div>
			<div class="formtable hidden" id="gift_fields" style="margin-bottom:10px;">
				<div style="display:inline-block;">
					<h3 class="sub-heading"><?php esc_html_e( 'Gift Recipient details', 'service-booking' ); ?></h3>
					<?php
					woocommerce_form_field(
						'gift_details[first_name]',
						array(
							'type'     => 'text',
							'label'    => __( 'First name', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[first_name]' )
					);

					woocommerce_form_field(
						'gift_details[last_name]',
						array(
							'type'     => 'text',
							'label'    => __( 'Last name', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[last_name]' )
					);

					woocommerce_form_field(
						'gift_details[email]',
						array(
							'type'     => 'email',
							'label'    => __( 'Email', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[email]' )
					);

					woocommerce_form_field(
						'gift_details[contact]',
						array(
							'type'     => 'tel',
							'label'    => __( 'Mobile No', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[contact]' )
					);

					woocommerce_form_field(
						'gift_details[address]',
						array(
							'type'     => 'text',
							'label'    => __( 'Address', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[address]' )
					);

					woocommerce_form_field(
						'gift_details[city]',
						array(
							'type'     => 'text',
							'label'    => __( 'City', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[city]' )
					);

					woocommerce_form_field(
						'gift_details[state]',
						array(
							'type'     => 'select',
							'label'    => __( 'State', 'service-booking' ),
							'required' => false,
							'options'  => WC()->countries->get_states( $checkout->get_value( 'gift_details[country]' ) ?: 'IT' ),
						),
						$checkout->get_value( 'gift_details[state]' )
					);

					woocommerce_form_field(
						'gift_details[country]',
						array(
							'type'     => 'select',
							'label'    => __( 'Country', 'service-booking' ),
							'required' => true,
							'options'  => WC()->countries->get_countries(),
						),
						$checkout->get_value( 'gift_details[country]' ) ?? 'IT'
					);

					woocommerce_form_field(
						'gift_details[postcode]',
						array(
							'type'     => 'number',
							'label'    => __( 'Postcode', 'service-booking' ),
							'required' => true,
						),
						$checkout->get_value( 'gift_details[postcode]' )
					);
					?>
				</div>
			</div>
		</div>
		<?php
	}//end bm_add_gift_fields_to_woocommerce_checkout()


	/**
	 * Validate gift fields in woocommerce checkout
	 *
	 * @author Darpan
	 */
	public function bm_validate_woocommerce_gift_fields() {
			/**$post = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );*/
			$post = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );

		if ( isset( $post['is_gift'] ) && $post['is_gift'] === 'on' ) {
			// Validate First Name
			if ( empty( $post['gift_details']['first_name'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s first name.', 'service-booking' ), 'error' );
			}

			// Validate Last Name
			if ( empty( $post['gift_details']['last_name'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s last name.', 'service-booking' ), 'error' );
			}

			// Validate Email
			if ( empty( $post['gift_details']['email'] ) || ! filter_var( $post['gift_details']['email'], FILTER_VALIDATE_EMAIL ) ) {
				wc_add_notice( __( 'Please enter a valid recipient email.', 'service-booking' ), 'error' );
			}

			// Validate Contact
			if ( empty( $post['gift_details']['contact'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s contact number.', 'service-booking' ), 'error' );
			}

			// Validate Address
			if ( empty( $post['gift_details']['address'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s address.', 'service-booking' ), 'error' );
			}

			// Validate City
			if ( empty( $post['gift_details']['city'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s city.', 'service-booking' ), 'error' );
			}

			// Validate State
			/**if ( empty( $post['gift_details']['state'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s state.', 'service-booking' ), 'error' );
			}*/

			// Validate Country
			if ( empty( $post['gift_details']['country'] ) ) {
				wc_add_notice( __( 'Please select the recipient\'s country.', 'service-booking' ), 'error' );
			}

			// Validate Postcode
			if ( empty( $post['gift_details']['postcode'] ) ) {
				wc_add_notice( __( 'Please enter the recipient\'s postcode.', 'service-booking' ), 'error' );
			}
		}
	}//end bm_validate_woocommerce_gift_fields()


	/**
	 * Unset cod for woocommerce gift orders
	 *
	 * @author Darpan
	 */
	public function bm_restrict_cod_for_woocommerce_gift_orders( $available_gateways ) {
		if ( ! is_checkout() || ! isset( $available_gateways['cod'] ) ) {
			return $available_gateways;
		}

		$is_gift = isset( $_POST['is_gift'] ) && $_POST['is_gift'] === 'on';

		if ( $is_gift ) {
			$selected_payment_method = WC()->session->get( 'chosen_payment_method' );

			if ( empty( $selected_payment_method ) && isset( $_POST['payment_method'] ) ) {
				$selected_payment_method = $_POST['payment_method'];
			}

			unset( $available_gateways['cod'] );

			if ( $selected_payment_method === 'cod' ) {
				wc_add_notice(
					__( 'Cash on Delivery is not available for gift orders. Please choose another payment method.', 'your-text-domain' ),
					'error'
				);
			}
		}

		return $available_gateways;
	}//end bm_restrict_cod_for_woocommerce_gift_orders()


	/**
	 * Add gift fields to woocommerce order meta
	 *
	 * @author Darpan
	 */
	public function bm_save_gift_fields_to_woocommerce_order_meta( $order_id ) {
			/**$post   = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );*/
			$post  = array_map( 'sanitize_text_field', wp_unslash( $_POST ) );
		$gift_data = array();

		if ( isset( $post['is_gift'] ) ) {
			$gift_data['is_gift'] = 1;
		}

		if ( isset( $post['gift_details'] ) ) {
			foreach ( $post['gift_details'] as $key => $value ) {
				$gift_data[ 'recipient_' . $key ] = sanitize_text_field( $value );
			}
		}

		if ( ! empty( $gift_data ) ) {
			update_post_meta( $order_id, 'gift_data', $gift_data );
		}
	}//end bm_save_gift_fields_to_woocommerce_order_meta()


	/**
	 * Fetch woocommerce states by country
	 *
	 * @author Darpan
	 */
	public function bm_get_woocommerce_states_by_country() {
			$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['country'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$post    = apply_filters( 'bm_flexibooking_set_wc_states_post_data', $post );
		$country = sanitize_text_field( $post['country'] );
		$states  = WC()->countries->get_states( $country );

		if ( ! empty( $states ) ) {
			wp_send_json_success( $states );
		}

		wp_send_json_error();
	} ///end bm_get_woocommerce_states_by_country()


	/**
	 * Check voucher validity
	 *
	 * @author Darpan
	 */
	public function bm_check_if_valid_voucher() {
			$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['voucher'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$post = apply_filters( 'bm_flexibooking_set_voucher_validity_data', $post );
		$code = trim( stripslashes( sanitize_text_field( $post['voucher'] ) ) );

		if ( ! $code ) {
			wp_send_json_error( __( 'Voucher is invalid.', 'service-booking' ) );
			return;
		}

		try {
			$validate = ( new FlexiVoucherRedeem( $code ) )->validateVoucher();
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $validate['error'] ) ) {
			wp_send_json_error( $validate['error'] );
			return;
		}

		wp_send_json_success();
	} ///end bm_check_if_valid_voucher()


	/**
	 * Fetch voucher timeslots
	 *
	 * @author Darpan
	 */
	public function bm_get_valid_available_voucher_timeslots() {
			$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['voucher'] ) || empty( $post['date'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$post = apply_filters( 'bm_flexibooking_set_voucher_timeslot_data', $post );
		$code = trim( stripslashes( sanitize_text_field( $post['voucher'] ) ) );
		$date = trim( sanitize_text_field( $post['date'] ) );

		if ( ! $code ) {
			wp_send_json_error( __( 'Voucher is invalid.', 'service-booking' ) );
			return;
		}

		if ( ! $date ) {
			wp_send_json_error( __( 'Date is invalid.', 'service-booking' ) );
			return;
		}

		$redeemVoucher = new FlexiVoucherRedeem( $code );

		try {
			$validate = $redeemVoucher->validateVoucher();
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $validate['error'] ) ) {
			wp_send_json_error( $validate['error'] );
			return;
		}

		try {
			$slots = $redeemVoucher->fetchAvailableSlots( $date );
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $slots['error'] ) ) {
			wp_send_json_error( $slots['error'] );
			return;
		}

		wp_send_json_success( $slots );
	} ///end bm_get_valid_available_voucher_timeslots()


	/**
	 * Fetch voucher timeslots
	 *
	 * @author Darpan
	 */
	public function bm_get_confirm_and_redeem_voucher() {
			$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['voucher'] ) || empty( $post['date'] ) || empty( $post['slot'] ) || empty( $post['recipient'] ) || ! is_array( $post['recipient'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$post      = apply_filters( 'bm_flexibooking_set_voucher_timeslot_data', $post );
		$code      = trim( stripslashes( sanitize_text_field( $post['voucher'] ) ) );
		$date      = trim( sanitize_text_field( $post['date'] ) );
		$slot      = trim( sanitize_text_field( $post['slot'] ) );
		$recipient = $post['recipient'];

		if ( ! $code ) {
			wp_send_json_error( __( 'Voucher is invalid.', 'service-booking' ) );
			return;
		}

		if ( ! $date ) {
			wp_send_json_error( __( 'Date is invalid.', 'service-booking' ) );
			return;
		}

		if ( ! $slot ) {
			wp_send_json_error( __( 'Slot is invalid.', 'service-booking' ) );
			return;
		}

		$redeemVoucher = new FlexiVoucherRedeem( $code );

		try {
			$validate = $redeemVoucher->validateVoucher();
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $validate['error'] ) ) {
			wp_send_json_error( $validate['error'] );
			return;
		}

		try {
			$confirmRedemption = $redeemVoucher->confirmRedemption( $date, $slot );
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $confirmRedemption['error'] ) ) {
			wp_send_json_error( $confirmRedemption['error'] );
			return;
		}

		$redeemVoucher->updateVoucherInfo(
			array(
				'recipient_data' => maybe_serialize( $recipient ),
				'updated_at'     => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
			)
		);

		do_action( 'flexibooking_set_process_voucher_redeem', $redeemVoucher->getBookingInfo()[0]['id'] );
		wp_send_json_success();
	} ///end bm_get_confirm_and_redeem_voucher()


	/**
	 * Get states by country
	 *
	 * @author Darpan
	 */
	public function bm_fetch_states_by_country() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$country = trim( stripslashes( sanitize_text_field( filter_input( INPUT_POST, 'country' ) ) ) );
		if ( empty( $country ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$states = ( new BM_Request() )->bm_get_states( $country );
		wp_send_json_success( $states );
	}//end bm_fetch_states_by_country()


	/**
	 *  Prepare google analytics data
	 *
	 * @author Darpan
	 */
	public function bm_prepare_ga_purchase_data( $booking_key ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		$discounted_key = $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ? 'discounted_' : '';
		$order_data     = $dbhandler->bm_fetch_data_from_transient( $discounted_key . $booking_key );

		if ( empty( $order_data ) ) {
			return false;
		}

		$booking_id    = $dbhandler->get_value( 'BOOKING', 'id', $booking_key, 'booking_key' );
		$customer_data = $bmrequests->get_customer_info_for_order( $booking_id );

		$customer = array(
			'email'     => $customer_data['billing_email'] ?? '',
			'firstName' => $customer_data['billing_first_name'] ?? '',
			'lastName'  => $customer_data['billing_last_name'] ?? '',
		);

		$currency       = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
		$main_product   = $bmrequests->bm_prepare_service_data( $booking_key, $currency );
		$extra_products = $bmrequests->bm_prepare_extra_services_data( $booking_key, $currency );

		$items = array();
		$total = 0;

        if ( !empty( $main_product ) ) {
            $qty   = intval( $main_product['quantity'] ?? 1 );
            $price = floatval( $main_product['amount'] )/$qty;

			$items[] = array(
				'itemId'   => strval( $main_product['id'] ),
				'itemName' => sanitize_text_field( $main_product['name'] ),
				'price'    => $price,
				'quantity' => $qty,
			);

			$total += $price * $qty;
		}

        if ( !empty( $extra_products ) ) {
            foreach ( $extra_products as $p ) {
                $qty   = intval( $p['quantity'] ?? 1 );
                $price = floatval( $p['amount'] )/$qty;

				$items[] = array(
					'itemId'   => strval( $p['id'] ),
					'itemName' => sanitize_text_field( $p['name'] ),
					'price'    => $price,
					'quantity' => $qty,
				);

				$total += $price * $qty;
			}
		}

		return array(
			'transactionId'    => sanitize_text_field( $booking_key ),
			'transactionTotal' => floatval( $total ),
			'tax'              => 0,
			'shipping'         => 0,
			'currency'         => sanitize_text_field( $currency ),
			'orderDate'        => sanitize_text_field( $dbhandler->get_value( 'BOOKING', 'booking_date', $booking_key, 'booking_key' ) ),
			'items'            => $items,
			'customerData'     => $customer,
		);
	}


	/**
	 * Save booking data through woocommerce
	 *
	 * @author Darpan
	 */
	public function bm_save_woocommerce_booking_data( $order_id ) {
		if ( ! $order_id || $order_id < 1 ) {
			return;
		}

		$post_type = get_post_type( $order_id );
		if ( ! in_array( $post_type, array( 'shop_order', 'shop_order_placehold' ), true ) ) {
			return;
		}

		if ( is_admin() ) {
			$flexi_booking_id = get_post_meta( $order_id, '_flexi_booking_id', true );

			if ( $flexi_booking_id > 0 ) {
				( new BM_Request() )->bm_update_flexi_order_status_as_processing( $flexi_booking_id );
			}
		} else {
			$woocommerceservice = new WooCommerceService();

			if ( ! $woocommerceservice->bm_get_woo_commerce_cart() ) {
				wc_add_notice( __( 'An error occurred while processing the booking data. Please try again.', 'service-booking' ), 'notice' );
				wp_safe_redirect( $woocommerceservice->get_woo_commerce_checkout_url() );
				exit;
			}
			try {
				$wc_order = wc_get_order( $order_id );

				if ( empty( $wc_order ) ) {
					wc_add_notice( __( 'An error occurred while processing the booking data. Please try again.', 'service-booking' ), 'notice' );
					wp_safe_redirect( $woocommerceservice->get_woo_commerce_checkout_url() );
					exit;
				}

				$is_flexi_booking = false;
				$booking_key      = '';
				$checkout_key     = '';

				foreach ( $wc_order->get_items() as $item_id => $item ) {
					$added_by_flexibooking = $item->get_meta( '_added_by_flexibooking', true );

					if ( $added_by_flexibooking ) {
						$is_flexi_booking = true;
						$booking_key      = $item->get_meta( '_flexi_booking_key', true );
						$checkout_key     = $item->get_meta( '_flexi_checkout_key', true );
						break;
					}
				}

				if ( ! $is_flexi_booking ) {
					return;
				}

				if ( empty( $booking_key ) || empty( $checkout_key ) ) {
					wc_add_notice( __( 'An error occurred while processing the booking data. Please try again.', 'service-booking' ), 'notice' );
					// wp_safe_redirect( $woocommerceservice->get_woo_commerce_checkout_url() );
					// exit;
					return;
				}

				update_post_meta( $order_id, '_added_by_flexibooking', $added_by_flexibooking );
				update_post_meta( $order_id, '_flexi_booking_key', $booking_key );
				update_post_meta( $order_id, '_flexi_checkout_key', $checkout_key );

				$flexi_booking_id = ( new BM_Request() )->bm_save_woocommerce_booking_data_in_flexibooking( $order_id );

				if ( $flexi_booking_id <= 0 ) {
					wc_add_notice( __( 'An error occurred while saving the booking data. Please try again.', 'service-booking' ), 'notice' );
					wp_safe_redirect( $woocommerceservice->get_woo_commerce_checkout_url() );
					exit;
				}
			} catch ( Exception $e ) {
				wc_add_notice( __( 'An unexpected error occurred. Please try again.', 'service-booking' ), 'notice' );
				wp_safe_redirect( $woocommerceservice->get_woo_commerce_checkout_url() );
				exit;
			}
		}
	}


	/**
	 * Set flexi booking order as complete
	 *
	 * @author Darpan
	 */
	public function bm_set_flexibooking_order_as_completed( $order_id ) {
		if ( ! $order_id || $order_id < 1 ) {
			return;
		}

		$post_type = get_post_type( $order_id );
		if ( ! in_array( $post_type, array( 'shop_order', 'shop_order_placehold' ), true ) ) {
			return;
		}

		$flexi_booking_id   = get_post_meta( $order_id, '_flexi_booking_id', true );
		$flexi_customer_id  = get_post_meta( $order_id, '_flexi_customer_id', true );
		$flexi_service_date = get_post_meta( $order_id, '_flexi_service_date', true );

		if ( $flexi_booking_id > 0 && $flexi_customer_id > 0 && ! empty( $flexi_service_date ) ) {
			$dbhandler    = new BM_DBhandler();
			$booked_slots = $dbhandler->get_value( 'BOOKING', 'booking_slots', $flexi_booking_id, 'id' );
			$booked_slots = ! empty( $booked_slots ) ? maybe_unserialize( $booked_slots ) : array();

			if ( isset( $booked_slots['to'] ) ) {
				$flexi_service_date = $flexi_service_date . ' ' . $booked_slots['to'];

				$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
				$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
				$current_date = $today->format( 'Y-m-d' );
				$current_time = $today->format( 'H:i' );

				$currentDateTime = $current_date . ' ' . $current_time;

				if ( strtotime( $flexi_service_date ) > strtotime( $currentDateTime ) ) {
					( new BM_Request() )->bm_update_flexi_order_status_as_completed( $flexi_booking_id );
				}
			}
		}
	}//end bm_set_flexibooking_order_as_completed()


	/**
	 * Custom image shortcode inside wp editor
	 *
	 * @author Darpan
	 */
	public function bm_custom_img_caption_shortcode( $empty, $attr, $content ) {
		$caption = isset( $attr['caption'] ) ? $attr['caption'] : '';
		return '<div class="wp-caption">' . do_shortcode( $content ) . '<p class="wp-caption-text">' . $caption . '</p></div>';
	}//end bm_custom_img_caption_shortcode()


	/**
	 * Mark flexi order paid after woocommerce order is paid
	 *
	 * @author Darpan
	 */
	public function bm_mark_flexi_orders_paid( $order_id ) {
			$this->bm_set_flexibooking_order_as_completed( $order_id );
	}//end bm_mark_flexi_orders_paid()


	/**
	 * Hide title of a specific page
	 *
	 * @author Darpan
	 */
	public function bm_hide_specific_page_title( $title, $id ) {
		if ( is_page( 'flexi-services' ) || is_page( 'flexi-service-fullcalendar-1' ) || is_page( 'flexi-service-fullcalendar-2' ) || is_page( 'flexi-single-service-calendar' ) ) {
			return '';
		}
		return $title;
	}


	/**
	 * New order creation hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_new_order_callback( $order_id = 0 ) {
			$dbhandler = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$process_id    = 0;
		$template_id   = 0;

		if ( ! empty( $order_id ) ) {
			$is_frontend_booking = $dbhandler->get_value( 'BOOKING', 'is_frontend_booking', $order_id, 'id' );

			$processes = array();

			if ( $is_frontend_booking == 1 ) {
				$processes = $dbhandler->get_all_result(
					'EVENTNOTIFICATION',
					'*',
					array(
						'status' => 1,
						'type'   => 0,
					),
					'results'
				);
			} elseif ( $is_frontend_booking == 0 ) {
				$processes = $dbhandler->get_all_result(
					'EVENTNOTIFICATION',
					'*',
					array(
						'status' => 1,
						'type'   => 1,
					),
					'results'
				);
			}

			if ( ! empty( $processes ) && is_array( $processes ) ) {
				$scheduleable   = false;
				$non_existing   = true;
				$service_id     = $dbhandler->get_value( 'SLOTCOUNT', 'service_id', $order_id, 'booking_id' );
				$category_id    = $bmrequests->bm_fetch_category_id_by_service_id( $service_id );
				$order_status   = $dbhandler->get_value( 'BOOKING', 'order_status', $order_id, 'id' );
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $order_id, 'booking_id' );
				$delay          = 0;
				$seconds        = 1;
				$module         = '';

				foreach ( $processes as $process ) {
					$process_id  = isset( $process->id ) ? $process->id : 0;
					$template_id = isset( $process->template_id ) ? maybe_unserialize( $process->template_id ) : 0;
					$condition   = isset( $process->trigger_conditions ) ? maybe_unserialize( $process->trigger_conditions ) : array();
					$time_offset = isset( $process->time_offset ) ? maybe_unserialize( $process->time_offset ) : array();

					if ( ! empty( $condition ) && is_array( $condition ) ) {
						$types     = isset( $condition['type'] ) ? $condition['type'] : array();
						$operators = isset( $condition['operator'] ) ? $condition['operator'] : array();
						$values    = isset( $condition['values'] ) ? $condition['values'] : array();

						if ( ! empty( $types ) && is_array( $types ) ) {
							foreach ( $types as $key => $type ) {
								$operator = isset( $operators[ $key ] ) ? $operators[ $key ] : -1;
								$value    = isset( $values[ $key ] ) ? $values[ $key ] : array();

								if ( $type == 0 ) {
									$module = $service_id;
								} elseif ( $type == 1 ) {
									$module = $category_id;
								} elseif ( $type == 2 ) {
									$module = $order_status;
								} elseif ( $type == 3 ) {
									$module = $payment_status;
								}

								if ( $operator == 1 ) {
									if ( in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								} elseif ( $operator == 0 ) {
									if ( ! in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								}

								if ( ! $scheduleable ) {
									if ( $non_existing && in_array( $module, $value ) ) {
										$non_existing = false;
									}
								}
							}
						}
					} else {
						$scheduleable = true;
					}

					if ( ! empty( $time_offset ) && is_array( $time_offset ) ) {
						$offset   = isset( $time_offset['value'] ) ? $time_offset['value'] : 0;
						$unit     = isset( $time_offset['unit'] ) ? $time_offset['unit'] : -1;
						$position = isset( $time_offset['position'] ) ? $time_offset['position'] : -1;

						if ( $unit == 0 ) {
							$seconds = 60;
						} elseif ( $unit == 1 ) {
							$seconds = 60 * 60;
						} elseif ( $unit == 2 ) {
							$seconds = 24 * 60 * 60;
						}

						$delay = $offset * $seconds;
					}

					if ( $scheduleable ) {
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_new_order', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_new_order', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_new_order', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_new_order_callback()


	/**
	 * New order creation hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_new_order_callback( $order_id, $template_id, $process_id ) {
			$this->bm_flexibooking_mail_on_new_order( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_new_order_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_new_order( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler        = new BM_DBhandler();
		$bm_mail          = new BM_Email();
		$bmrequests       = new BM_Request();
		$order            = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$mail_to_admin    = false;
		$mail_to_customer = false;
		$mail_sent        = false;
		$admin_mail_error = '';
		$cust_mail_error  = '';
		$source           = -1;
		$mail_type        = 'new_order';
		$language         = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$back_lang        = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
		$language         = ! empty( $back_lang ) ? $back_lang : $language;
		$mail_data        = array();

		if ( ! empty( $order ) ) {
			$current_mail_sent = isset( $order->mail_sent ) ? (int) $order->mail_sent : 0;
			$need_admin        = false;
			$need_customer     = false;

			if ( $current_mail_sent == 0 ) {
				$need_admin    = true;
				$need_customer = true;
			} elseif ( $current_mail_sent == 1 ) {
				$need_admin    = false; // admin already sent
				$need_customer = true;
			} elseif ( $current_mail_sent == 2 ) {
				$need_admin    = true;
				$need_customer = false; // customer already sent
			} elseif ( $current_mail_sent == 3 ) {
				$need_admin    = false;
				$need_customer = false; // both sent
			}

			$source = isset( $order->is_frontend_booking ) ? $order->is_frontend_booking : -1;

			if ( empty( $process_id ) ) {
				if ( $source == 1 ) {
					$template_id = $dbhandler->get_all_result(
						'EMAIL_TMPL',
						'id',
						array(
							'status' => 1,
							'type'   => 0,
                        ),
						'var'
					);
				} elseif ( $source == 0 ) {
					$template_id = $dbhandler->get_all_result(
						'EMAIL_TMPL',
						'id',
						array(
							'status' => 1,
							'type'   => 1,
                        ),
						'var'
					);
				}
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'New order' );
			$message               = '<p>' . esc_html( 'New order received' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Nuovo ordine' );
				$message = '<p>' . esc_html( 'Nuovo ordine ricevuto' ) . '</p>';
			}

			// Admin email (only if needed)
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale            = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_new_order_template_id = $dbhandler->get_global_option_value( 'bm_new_order_admin_template', 0 );

				if ( ! empty( $admin_new_order_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_new_order_template_id, (int) $order_id );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_new_order_template_id, (int) $order_id );
				} else {
					$admin_email_subject = $subject;
					$admin_email_message = $message;
				}

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-admin-email-layout.php';
				$admin_email_message = ob_get_contents();
				ob_end_clean();
				if ( $admin_old_locale ) {
					$bmrequests->bm_restore_locale( $admin_old_locale );
				}
				$mail_to_admin = $bm_mail->bm_send_notification_to_shop_admin( $admin_email_subject, $admin_email_message, (int) $order_id );

				$mail_to_admin    = $mail_to_admin['success'];
				$admin_mail_error = $mail_to_admin['error'];
			}

			// Customer email (only if needed)
			if ( $need_customer ) {
				$trp_lang = get_option( 'trp_lang_' . $order->booking_key, false );
				$language = ! empty( $trp_lang ) ? $trp_lang : $language;

				if ( ! empty( $template_id ) ) {
					$template_subject = $bm_mail->bm_get_template_email_subject( $template_id, (int) $order_id, true );
					$template_body    = $bm_mail->bm_get_template_email_content( $template_id, (int) $order_id, true );
				} else {
					$template_subject = $subject;
					$template_body    = $message;
				}

				$customer_email = $bmrequests->bm_fetch_customer_email_from_booking_form_data( (int) $order_id );
				$customer_email = apply_filters( 'bm_flexibooking_new_order_user_mail', $customer_email, $order_id );

				$customer_old_locale = $bmrequests->bm_switch_locale_by_booking_reference( $order->booking_key );

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-email-layout.php';
				$template_body = ob_get_contents();
				ob_end_clean();

				$mail_to_customer = $bm_mail->bm_send_email_to_customer( $template_subject, $template_body, (int) $order_id );
				$mail_to_customer = $mail_to_customer['success'];
				$cust_mail_error  = $mail_to_customer['error'];

				$error_msg = array();
				if ( !empty( $admin_mail_error ) ) {
					$error_msg['admin'] = $admin_mail_error;
				}

				if ( !empty( $cust_mail_error ) ) {
					$error_msg['customer'] = $cust_mail_error;
				}

				$mail_data = array(
					'module_type'   => 'BOOKING',
					'module_id'     => $order_id,
					'mail_type'     => $mail_type,
					'template_id'   => $template_id,
					'process_id'    => $process_id,
					'mail_to'       => $customer_email,
					'mail_sub'      => wp_kses_post( $template_subject ),
					'mail_body'     => wp_kses_post( stripslashes( $template_body ) ),
					'mail_lang'     => $language,
					'status'        => $mail_to_customer ? 1 : 0,
					'error_message' => !empty( $error_msg ) ? maybe_serialize( $error_msg ) : '',
				);

				if ( $customer_old_locale ) {
					$bmrequests->bm_restore_locale( $customer_old_locale );
				}
			}

			// Update mail_sent using bitwise OR
			$new_mail_sent = $current_mail_sent;
			if ( $mail_to_admin && ! $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 1;
			} elseif ( ! $mail_to_admin && $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 2;
			} elseif ( $mail_to_admin && $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 3;
			}

			if ( $new_mail_sent != $current_mail_sent ) {
				$dbhandler->update_row( 'BOOKING', 'id', $order_id, array( 'mail_sent' => $new_mail_sent ), '', '%d' );
				$mail_sent = true;
			}

			// Log customer email if it was actually sent
			/**if ( $mail_sent && $need_customer && $mail_to_customer && ! empty( $mail_data ) ) {
			}*/

			if ( ! empty( $mail_data ) ) {
				$mail_data = $bmrequests->sanitize_request( $mail_data, 'EMAILS' );
				if ( $mail_data != false && $mail_data != null ) {
					$mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );
				}
			}
		}
	} //end bm_flexibooking_mail_on_new_order_created()


	/**
	 * Voucher redeem hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_voucher_redeem_callback( $order_id = 0 ) {
		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$process_id  = 0;
		$template_id = 0;

		if ( ! empty( $order_id ) ) {
			$processes = $dbhandler->get_all_result(
				'EVENTNOTIFICATION',
				'*',
				array(
					'status' => 1,
					'type'   => 9,
				),
				'results'
			);

			if ( ! empty( $processes ) && is_array( $processes ) ) {
				$scheduleable   = false;
				$non_existing   = true;
				$service_id     = $dbhandler->get_value( 'SLOTCOUNT', 'service_id', $order_id, 'booking_id' );
				$category_id    = $bmrequests->bm_fetch_category_id_by_service_id( $service_id );
				$order_status   = $dbhandler->get_value( 'BOOKING', 'order_status', $order_id, 'id' );
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $order_id, 'booking_id' );
				$delay          = 0;
				$seconds        = 1;
				$module         = '';

				foreach ( $processes as $process ) {
					$process_id  = isset( $process->id ) ? $process->id : 0;
					$template_id = isset( $process->template_id ) ? maybe_unserialize( $process->template_id ) : 0;
					$condition   = isset( $process->trigger_conditions ) ? maybe_unserialize( $process->trigger_conditions ) : array();
					$time_offset = isset( $process->time_offset ) ? maybe_unserialize( $process->time_offset ) : array();

					if ( ! empty( $condition ) && is_array( $condition ) ) {
						$types     = isset( $condition['type'] ) ? $condition['type'] : array();
						$operators = isset( $condition['operator'] ) ? $condition['operator'] : array();
						$values    = isset( $condition['values'] ) ? $condition['values'] : array();

						if ( ! empty( $types ) && is_array( $types ) ) {
							foreach ( $types as $key => $type ) {
								$operator = isset( $operators[ $key ] ) ? $operators[ $key ] : -1;
								$value    = isset( $values[ $key ] ) ? $values[ $key ] : array();

								if ( $type == 0 ) {
									$module = $service_id;
								} elseif ( $type == 1 ) {
									$module = $category_id;
								} elseif ( $type == 2 ) {
									$module = $order_status;
								} elseif ( $type == 3 ) {
									$module = $payment_status;
								}

								if ( $operator == 1 ) {
									if ( in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								} elseif ( $operator == 0 ) {
									if ( ! in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								}

								if ( ! $scheduleable ) {
									if ( $non_existing && in_array( $module, $value ) ) {
										$non_existing = false;
									}
								}
							}
						}
					} else {
						$scheduleable = true;
					}

					if ( ! empty( $time_offset ) && is_array( $time_offset ) ) {
						$offset   = isset( $time_offset['value'] ) ? $time_offset['value'] : 0;
						$unit     = isset( $time_offset['unit'] ) ? $time_offset['unit'] : -1;
						$position = isset( $time_offset['position'] ) ? $time_offset['position'] : -1;

						if ( $unit == 0 ) {
							$seconds = 60;
						} elseif ( $unit == 1 ) {
							$seconds = 60 * 60;
						} elseif ( $unit == 2 ) {
							$seconds = 24 * 60 * 60;
						}

						$delay = $offset * $seconds;
					}

					if ( $scheduleable ) {
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_voucher_redeem', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_voucher_redeem', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_voucher_redeem', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_voucher_redeem_callback()


	/**
	 * Voucher redeem hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_voucher_redeem_callback( $order_id, $template_id, $process_id ) {
			$this->bm_flexibooking_mail_on_voucher_redeem( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_voucher_redeem_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_voucher_redeem( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler            = new BM_DBhandler();
		$bm_mail              = new BM_Email();
		$bmrequests           = new BM_Request();
		$order                = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$mail_to_recipient    = false;
		$admin_mail_error     = '';
		$recipient_mail_error = '';
		$source               = -1;
		$mail_type            = 'voucher_redeem';
		$language             = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$back_lang            = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
		$language             = ! empty( $back_lang ) ? $back_lang : $language;
		$mail_data            = array();

		if ( ! empty( $order ) ) {
			if ( empty( $process_id ) ) {
				$template_id = $dbhandler->get_all_result(
					'EMAIL_TMPL',
					'id',
					array(
						'status' => 1,
						'type'   => 16,
					),
					'var'
				);
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'Voucher redeemed' );
			$message               = '<p>' . esc_html( 'Voucher successfully redeemed' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Buono riscattato' );
				$message = '<p>' . esc_html( 'Voucher riscattato con successo' ) . '</p>';
			}

			// Admin email (if needed)
			if ( $bm_admin_notification == 1 ) {
				$admin_old_locale                 = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_voucher_redeem_template_id = $dbhandler->get_global_option_value( 'bm_voucher_redeem_admin_template', 0 );

				if ( ! empty( $admin_voucher_redeem_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_voucher_redeem_template_id, (int) $order_id );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_voucher_redeem_template_id, (int) $order_id );
				} else {
					$admin_email_subject = $subject;
					$admin_email_message = $message;
				}

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-admin-email-layout.php';
				$admin_email_message = ob_get_contents();
				ob_end_clean();

				if ( $admin_old_locale ) {
					$bmrequests->bm_restore_locale( $admin_old_locale );
				}
				$admin_result     = $bm_mail->bm_send_notification_to_shop_admin( $admin_email_subject, $admin_email_message, (int) $order_id );
				$admin_mail_error = $admin_result['error'];
			}

			$trp_lang = get_option( 'trp_lang_' . $order->booking_key, false );
			$language = ! empty( $trp_lang ) ? $trp_lang : $language;

			if ( ! empty( $template_id ) ) {
				$template_subject = $bm_mail->bm_get_template_email_subject( $template_id, (int) $order_id, true );
				$template_body    = $bm_mail->bm_get_template_email_content( $template_id, (int) $order_id, true );
			} else {
				$template_subject = $subject;
				$template_body    = $message;
			}

			$recipient_email = $bmrequests->bm_fetch_gift_recipient_email_from_booking_form_data( (int) $order_id );

			$recipient_old_locale = $bmrequests->bm_switch_locale_by_booking_reference( $order->booking_key );

			ob_start();
			include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-email-layout.php';
			$template_body = ob_get_contents();
			ob_end_clean();

			$recipient_result     = $bm_mail->bm_send_voucher_email_to_recipient( $template_subject, $template_body, (int) $order_id );
			$recipient_mail_error = $recipient_result['error'];
			$mail_to_recipient    = $recipient_result['success'];

			if ( $recipient_old_locale ) {
				$bmrequests->bm_restore_locale( $recipient_old_locale );
			}

			// Build error message array
			$error_msg = array();
			if ( ! empty( $admin_mail_error ) ) {
				$error_msg['admin'] = $admin_mail_error;
			}
			if ( ! empty( $recipient_mail_error ) ) {
				$error_msg['customer'] = $recipient_mail_error;
			}

			$mail_data = array(
				'module_type'   => 'BOOKING',
				'module_id'     => $order_id,
				'mail_type'     => $mail_type,
				'template_id'   => $template_id,
				'process_id'    => $process_id,
				'mail_to'       => $recipient_email,
				'mail_sub'      => wp_kses_post( $template_subject ),
				'mail_body'     => wp_kses_post( stripslashes( $template_body ) ),
				'mail_lang'     => $language,
				'status'        => $mail_to_recipient ? 1 : 0,
				'error_message' => ! empty( $error_msg ) ? maybe_serialize( $error_msg ) : '',
			);

			// No mail_sent field for voucher redeem emails; we just log the attempt.
			/**if ( $need_recipient && ! empty( $mail_data ) ) {
			}*/

			if ( ! empty( $mail_data ) ) {
				$mail_data = $bmrequests->sanitize_request( $mail_data, 'EMAILS' );
				if ( $mail_data != false && $mail_data != null ) {
					$mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );
				}
			}
		}
	}//end bm_flexibooking_mail_on_voucher_redeem()


	/**
	 * New request creation hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_new_request_callback( $order_id = 0 ) {
			$dbhandler = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$process_id    = 0;
		$template_id   = 0;

		if ( ! empty( $order_id ) ) {
			$is_frontend_booking = $dbhandler->get_value( 'BOOKING', 'is_frontend_booking', $order_id, 'id' );

			$processes = array();

			if ( $is_frontend_booking == 1 ) {
				$processes = $dbhandler->get_all_result(
					'EVENTNOTIFICATION',
					'*',
					array(
						'status' => 1,
						'type'   => 7,
					),
					'results'
				);
			} elseif ( $is_frontend_booking == 0 ) {
				$processes = $dbhandler->get_all_result(
					'EVENTNOTIFICATION',
					'*',
					array(
						'status' => 1,
						'type'   => 8,
					),
					'results'
				);
			}

			if ( ! empty( $processes ) && is_array( $processes ) ) {
				$scheduleable   = false;
				$non_existing   = true;
				$service_id     = $dbhandler->get_value( 'SLOTCOUNT', 'service_id', $order_id, 'booking_id' );
				$category_id    = $bmrequests->bm_fetch_category_id_by_service_id( $service_id );
				$order_status   = $dbhandler->get_value( 'BOOKING', 'order_status', $order_id, 'id' );
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $order_id, 'booking_id' );
				$delay          = 0;
				$seconds        = 1;
				$module         = '';

				foreach ( $processes as $process ) {
					$process_id  = isset( $process->id ) ? $process->id : 0;
					$template_id = isset( $process->template_id ) ? maybe_unserialize( $process->template_id ) : 0;
					$condition   = isset( $process->trigger_conditions ) ? maybe_unserialize( $process->trigger_conditions ) : array();
					$time_offset = isset( $process->time_offset ) ? maybe_unserialize( $process->time_offset ) : array();

					if ( ! empty( $condition ) && is_array( $condition ) ) {
						$types     = isset( $condition['type'] ) ? $condition['type'] : array();
						$operators = isset( $condition['operator'] ) ? $condition['operator'] : array();
						$values    = isset( $condition['values'] ) ? $condition['values'] : array();

						if ( ! empty( $types ) && is_array( $types ) ) {
							foreach ( $types as $key => $type ) {
										$operator = isset( $operators[ $key ] ) ? $operators[ $key ] : -1;
										$value    = isset( $values[ $key ] ) ? $values[ $key ] : array();

								if ( $type == 0 ) {
									$module = $service_id;
								} elseif ( $type == 1 ) {
									$module = $category_id;
								} elseif ( $type == 2 ) {
									$module = $order_status;
								} elseif ( $type == 3 ) {
									$module = $payment_status;
								}

								if ( $operator == 1 ) {
									if ( in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								} elseif ( $operator == 0 ) {
									if ( ! in_array( $module, $value ) ) {
											$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								}

								if ( ! $scheduleable ) {
									if ( $non_existing && in_array( $module, $value ) ) {
										$non_existing = false;
									}
								}
							}
						}
					} else {
						$scheduleable = true;
					}

					if ( ! empty( $time_offset ) && is_array( $time_offset ) ) {
						$offset   = isset( $time_offset['value'] ) ? $time_offset['value'] : 0;
						$unit     = isset( $time_offset['unit'] ) ? $time_offset['unit'] : -1;
						$position = isset( $time_offset['position'] ) ? $time_offset['position'] : -1;

						if ( $unit == 0 ) {
							$seconds = 60;
						} elseif ( $unit == 1 ) {
							$seconds = 60 * 60;
						} elseif ( $unit == 2 ) {
							$seconds = 24 * 60 * 60;
						}

						$delay = $offset * $seconds;
					}

					if ( $scheduleable ) {
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_new_request', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_new_request', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_new_request', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_new_request_callback()


	/**
	 * New request creation hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_new_request_callback( $order_id, $template_id, $process_id ) {
			$this->bm_flexibooking_mail_on_new_request( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_mail_new_request_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_new_request( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler        = new BM_DBhandler();
		$bm_mail          = new BM_Email();
		$bmrequests       = new BM_Request();
		$order            = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$mail_to_admin    = false;
		$mail_to_customer = false;
		$admin_mail_error = '';
		$cust_mail_error  = '';
		$source           = -1;
		$mail_type        = 'new_request';
		$language         = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$back_lang        = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
		$language         = ! empty( $back_lang ) ? $back_lang : $language;
		$mail_data        = array();

		if ( ! empty( $order ) ) {
			$current_mail_sent = isset( $order->mail_sent ) ? (int) $order->mail_sent : 0;
			$need_admin        = false;
			$need_customer     = false;

			if ( $current_mail_sent == 0 ) {
				$need_admin    = true;
				$need_customer = true;
			} elseif ( $current_mail_sent == 1 ) {
				$need_admin    = false;
				$need_customer = true;
			} elseif ( $current_mail_sent == 2 ) {
				$need_admin    = true;
				$need_customer = false;
			} elseif ( $current_mail_sent == 3 ) {
				$need_admin    = false;
				$need_customer = false;
			}

			$source = isset( $order->is_frontend_booking ) ? $order->is_frontend_booking : -1;

			if ( empty( $process_id ) ) {
				if ( $source == 1 ) {
					$template_id = $dbhandler->get_all_result(
						'EMAIL_TMPL',
						'id',
						array(
							'status' => 1,
							'type'   => 12,
						),
						'var'
					);
				} elseif ( $source == 0 ) {
					$template_id = $dbhandler->get_all_result(
						'EMAIL_TMPL',
						'id',
						array(
							'status' => 1,
							'type'   => 13,
						),
						'var'
					);
				}
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'New request' );
			$message               = '<p>' . esc_html( 'New request received' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Nuova richiesta' );
				$message = '<p>' . esc_html( 'Nuova richiesta ricevuta' ) . '</p>';
			}

			// Admin email
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale              = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_new_request_template_id = $dbhandler->get_global_option_value( 'bm_new_request_admin_template', 0 );

				if ( ! empty( $admin_new_request_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_new_request_template_id, (int) $order_id );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_new_request_template_id, (int) $order_id );
				} else {
					$admin_email_subject = $subject;
					$admin_email_message = $message;
				}

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-admin-email-layout.php';
				$admin_email_message = ob_get_contents();
				ob_end_clean();

				if ( $admin_old_locale ) {
					$bmrequests->bm_restore_locale( $admin_old_locale );
				}
				$admin_result     = $bm_mail->bm_send_notification_to_shop_admin( $admin_email_subject, $admin_email_message, (int) $order_id );
				$mail_to_admin    = $admin_result['success'];
				$admin_mail_error = $admin_result['error'];
			}

			// Customer email
			if ( $need_customer ) {
				$trp_lang = get_option( 'trp_lang_' . $order->booking_key, false );
				$language = ! empty( $trp_lang ) ? $trp_lang : $language;

				if ( ! empty( $template_id ) ) {
					$template_subject = $bm_mail->bm_get_template_email_subject( $template_id, (int) $order_id, true );
					$template_body    = $bm_mail->bm_get_template_email_content( $template_id, (int) $order_id, true );
				} else {
					$template_subject = $subject;
					$template_body    = $message;
				}

				$customer_email = $bmrequests->bm_fetch_customer_email_from_booking_form_data( (int) $order_id );
				$customer_email = apply_filters( 'bm_flexibooking_new_request_user_mail', $customer_email, $order_id );

				$customer_old_locale = $bmrequests->bm_switch_locale_by_booking_reference( $order->booking_key );

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-email-layout.php';
				$template_body = ob_get_contents();
				ob_end_clean();

				$customer_result  = $bm_mail->bm_send_email_to_customer( $template_subject, $template_body, (int) $order_id );
				$mail_to_customer = $customer_result['success'];
				$cust_mail_error  = $customer_result['error'];

				if ( $customer_old_locale ) {
					$bmrequests->bm_restore_locale( $customer_old_locale );
				}

				$error_msg = array();
				if ( ! empty( $admin_mail_error ) ) {
					$error_msg['admin'] = $admin_mail_error;
				}
				if ( ! empty( $cust_mail_error ) ) {
					$error_msg['customer'] = $cust_mail_error;
				}

				$mail_data = array(
					'module_type'   => 'BOOKING',
					'module_id'     => $order_id,
					'mail_type'     => $mail_type,
					'template_id'   => $template_id,
					'process_id'    => $process_id,
					'mail_to'       => $customer_email,
					'mail_sub'      => wp_kses_post( $template_subject ),
					'mail_body'     => wp_kses_post( stripslashes( $template_body ) ),
					'mail_lang'     => $language,
					'status'        => $mail_to_customer ? 1 : 0,
					'error_message' => ! empty( $error_msg ) ? maybe_serialize( $error_msg ) : '',
				);
			}

			// Update mail_sent using bitwise OR
			$new_mail_sent = $current_mail_sent;
			if ( $mail_to_admin && ! $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 1;
			} elseif ( ! $mail_to_admin && $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 2;
			} elseif ( $mail_to_admin && $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 3;
			}

			if ( $new_mail_sent != $current_mail_sent ) {
				$dbhandler->update_row( 'BOOKING', 'id', $order_id, array( 'mail_sent' => $new_mail_sent ), '', '%d' );
			}

			// Log customer email if attempted
			/**if ( $need_customer && ! empty( $mail_data ) ) {
			}*/

			if ( ! empty( $mail_data ) ) {
				$mail_data = $bmrequests->sanitize_request( $mail_data, 'EMAILS' );
				if ( $mail_data != false && $mail_data != null ) {
					$mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );
				}
			}
		}
	}//end bm_flexibooking_mail_on_new_request()


	/**
	 * New order voucher mail hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_new_order_voucher_callback( $order_id = 0 ) {
			$dbhandler = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$process_id    = 0;
		$template_id   = 0;

		if ( $order_id > 0 ) {
			$processes = $dbhandler->get_all_result(
				'EVENTNOTIFICATION',
				'*',
				array(
					'status' => 1,
					'type'   => 6,
				),
				'results'
			);

			if ( ! empty( $processes ) && is_array( $processes ) ) {
				$scheduleable   = false;
				$non_existing   = true;
				$service_id     = $dbhandler->get_value( 'SLOTCOUNT', 'service_id', $order_id, 'booking_id' );
				$category_id    = $bmrequests->bm_fetch_category_id_by_service_id( $service_id );
				$order_status   = $dbhandler->get_value( 'BOOKING', 'order_status', $order_id, 'id' );
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $order_id, 'booking_id' );
				$delay          = 0;
				$seconds        = 1;
				$module         = '';

				foreach ( $processes as $process ) {
					$process_id  = isset( $process->id ) ? $process->id : 0;
					$template_id = isset( $process->template_id ) ? maybe_unserialize( $process->template_id ) : 0;
					$condition   = isset( $process->trigger_conditions ) ? maybe_unserialize( $process->trigger_conditions ) : array();
					$time_offset = isset( $process->time_offset ) ? maybe_unserialize( $process->time_offset ) : array();

					if ( ! empty( $condition ) && is_array( $condition ) ) {
						$types     = isset( $condition['type'] ) ? $condition['type'] : array();
						$operators = isset( $condition['operator'] ) ? $condition['operator'] : array();
						$values    = isset( $condition['values'] ) ? $condition['values'] : array();

						if ( ! empty( $types ) && is_array( $types ) ) {
							foreach ( $types as $key => $type ) {
								$operator = isset( $operators[ $key ] ) ? $operators[ $key ] : -1;
								$value    = isset( $values[ $key ] ) ? $values[ $key ] : array();

								if ( $type == 0 ) {
									$module = $service_id;
								} elseif ( $type == 1 ) {
									$module = $category_id;
								} elseif ( $type == 2 ) {
									$module = $order_status;
								} elseif ( $type == 3 ) {
									$module = $payment_status;
								}

								if ( $operator == 1 ) {
									if ( in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								} elseif ( $operator == 0 ) {
									if ( ! in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								}

								if ( ! $scheduleable ) {
									if ( $non_existing && in_array( $module, $value ) ) {
										$non_existing = false;
									}
								}
							}
						}
					} else {
						$scheduleable = true;
					}

					if ( ! empty( $time_offset ) && is_array( $time_offset ) ) {
						$offset   = isset( $time_offset['value'] ) ? $time_offset['value'] : 0;
						$unit     = isset( $time_offset['unit'] ) ? $time_offset['unit'] : -1;
						$position = isset( $time_offset['position'] ) ? $time_offset['position'] : -1;

						if ( $unit == 0 ) {
							$seconds = 60;
						} elseif ( $unit == 1 ) {
							$seconds = 60 * 60;
						} elseif ( $unit == 2 ) {
							$seconds = 24 * 60 * 60;
						}

						$delay = $offset * $seconds;
					}

					if ( $scheduleable ) {
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_voucher_mail_new_order', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_voucher_mail_new_order', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_voucher_mail_new_order', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_new_order_voucher_callback()


	/**
	 * New order voucher mail hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_voucher_mail_new_order_callback( $order_id, $template_id, $process_id ) {
			$this->bm_flexibooking_voucher_mail_on_new_order( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_voucher_mail_new_order_callback()


	/**
	 * Send voucher mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_voucher_mail_on_new_order( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler    = new BM_DBhandler();
		$bm_mail      = new BM_Email();
		$bmrequests   = new BM_Request();
		$order        = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$booking_type = $dbhandler->get_value( 'BOOKING', 'booking_type', $order_id, 'id' );
		$mail_type    = 'gift_voucher';
		$language     = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$mail_data    = array();

		if ( ! empty( $order ) ) {
			if ( empty( $process_id ) ) {
				$template_id = $dbhandler->get_all_result(
					'EMAIL_TMPL',
					'id',
					array(
						'status' => 1,
						'type'   => 11,
					),
					'var'
				);
			}

			$subject = esc_html( 'Gift voucher' );
			$message = '<p>' . esc_html( 'You have received a gift voucher' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Buono regalo' );
				$message = '<p>' . esc_html( 'Hai ricevuto un buono regalo' ) . '</p>';
			}

			$trp_lang = get_option( 'trp_lang_' . $order->booking_key, false );
			$language = ! empty( $trp_lang ) ? $trp_lang : $language;

			if ( ! empty( $template_id ) ) {
				$template_subject = $bm_mail->bm_get_template_email_subject( $template_id, (int) $order_id, true );
				$template_body    = $bm_mail->bm_get_template_email_content( $template_id, (int) $order_id, true );
			} else {
				$template_subject = $subject;
				$template_body    = $message;
			}

			$recipient_email = $bmrequests->bm_fetch_gift_recipient_email_from_booking_form_data( (int) $order_id );

			$old_locale = $bmrequests->bm_switch_locale_by_booking_reference( $order->booking_key );

			ob_start();
			include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-email-layout.php';
			$template_body = ob_get_contents();
			ob_end_clean();

			// Send email and capture result
			$recipient_result  = $bm_mail->bm_send_voucher_email_to_recipient( $template_subject, $template_body, (int) $order_id, false );
			$mail_to_recipient = $recipient_result['success'];
			$recipient_error   = $recipient_result['error'];

			if ( $old_locale ) {
				$bmrequests->bm_restore_locale( $old_locale );
			}

			// Prepare mail data for logging
			$mail_data = array(
				'module_type'   => 'BOOKING',
				'module_id'     => $order_id,
				'mail_type'     => $mail_type,
				'template_id'   => $template_id,
				'process_id'    => $process_id,
				'mail_to'       => $recipient_email,
				'mail_sub'      => wp_kses_post( $template_subject ),
				'mail_body'     => wp_kses_post( stripslashes( $template_body ) ),
				'mail_lang'     => $language,
				'status'        => $mail_to_recipient ? 1 : 0,
				'error_message' => ! empty( $recipient_error ) ? $recipient_error : '',
			);

			// Always log the attempt
			$mail_data = $bmrequests->sanitize_request( $mail_data, 'EMAILS' );
			if ( $mail_data != false && $mail_data != null ) {
				$mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
				$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );
			}
		}
	}//end bm_flexibooking_voucher_mail_on_new_order()


	/**
	 * Failed order refund hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_failed_order_refund_callback( $order_key = '' ) {
			$dbhandler = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$process_id    = 0;
		$template_id   = 0;

		if ( ! empty( $order_key ) ) {
			$processes = $dbhandler->get_all_result(
				'EVENTNOTIFICATION',
				'*',
				array(
					'status' => 1,
					'type'   => 2,
				),
				'results'
			);

			if ( ! empty( $processes ) && is_array( $processes ) ) {
				$scheduleable   = false;
				$non_existing   = false;
				$transaction    = $dbhandler->get_row( 'FAILED_TRANSACTIONS', $order_key, 'booking_key' );
				$booking_data   = isset( $transaction->booking_data ) ? maybe_serialize( $transaction->booking_data ) : array();
				$service_id     = isset( $booking_data['service_id'] ) ? $booking_data['service_id'] : 0;
				$category_id    = $bmrequests->bm_fetch_category_id_by_service_id( $service_id );
				$order_status   = isset( $transaction->is_refunded ) && $transaction->is_refunded == 1 ? 'refunded' : '';
				$payment_status = 'failed';
				$delay          = 0;
				$seconds        = 1;
				$module         = '';

				foreach ( $processes as $process ) {
					$process_id  = isset( $process->id ) ? $process->id : 0;
					$template_id = isset( $process->template_id ) ? maybe_unserialize( $process->template_id ) : 0;
					$condition   = isset( $process->trigger_conditions ) ? maybe_unserialize( $process->trigger_conditions ) : array();
					$time_offset = isset( $process->time_offset ) ? maybe_unserialize( $process->time_offset ) : array();

					if ( ! empty( $condition ) && is_array( $condition ) ) {
						$types     = isset( $condition['type'] ) ? $condition['type'] : array();
						$operators = isset( $condition['operator'] ) ? $condition['operator'] : array();
						$values    = isset( $condition['values'] ) ? $condition['values'] : array();

						if ( ! empty( $types ) && is_array( $types ) ) {
							foreach ( $types as $key => $type ) {
								$operator = isset( $operators[ $key ] ) ? $operators[ $key ] : -1;
								$value    = isset( $values[ $key ] ) ? $values[ $key ] : array();

								if ( $type == 0 ) {
									$module = $service_id;
								} elseif ( $type == 1 ) {
									$module = $category_id;
								} elseif ( $type == 2 ) {
									$module = $order_status;
								} elseif ( $type == 3 ) {
									$module = $payment_status;
								}

								if ( $operator == 1 ) {
									if ( in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								} elseif ( $operator == 0 ) {
									if ( ! in_array( $module, $value ) ) {
										$scheduleable = true;
									} else {
										$scheduleable = false;
									}
								}

								if ( ! $scheduleable ) {
									if ( $non_existing && in_array( $module, $value ) ) {
										$non_existing = false;
									}
								}
							}
						}
					} else {
						$scheduleable = true;
					}

					if ( ! empty( $time_offset ) && is_array( $time_offset ) ) {
						$offset   = isset( $time_offset['value'] ) ? $time_offset['value'] : 0;
						$unit     = isset( $time_offset['unit'] ) ? $time_offset['unit'] : -1;
						$position = isset( $time_offset['position'] ) ? $time_offset['position'] : -1;

						if ( $unit == 0 ) {
							$seconds = 60;
						} elseif ( $unit == 1 ) {
							$seconds = 60 * 60;
						} elseif ( $unit == 2 ) {
							$seconds = 24 * 60 * 60;
						}

						$delay = $offset * $seconds;
					}

					if ( $scheduleable ) {
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_failed_order_refund', array( $order_key, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_failed_order_refund', array( $order_key, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_failed_order_refund', array( $order_key, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_failed_order_refund_callback()


	/**
	 * Failed order refund hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_failed_order_refund_callback( $order_key, $template_id, $process_id ) {
			$this->bm_flexibooking_mail_on_failed_order_refund( $order_key, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_failed_order_refund_callback()



	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_failed_order_refund( $order_key = '', $template_id = 0, $process_id = 0 ) {
		$dbhandler        = new BM_DBhandler();
		$bm_mail          = new BM_Email();
		$bmrequests       = new BM_Request();
		$order            = $dbhandler->get_row( 'FAILED_TRANSACTIONS', $order_key, 'booking_key' );
		$order_id         = isset( $order->id ) ? $order->id : 0;
		$mail_to_admin    = false;
		$mail_to_customer = false;
		$admin_mail_error = '';
		$cust_mail_error  = '';
		$source           = -1;
		$mail_type        = 'failed_order_refund';
		$language         = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$back_lang        = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
		$language         = ! empty( $back_lang ) ? $back_lang : $language;
		$mail_data        = array();

		if ( ! empty( $order ) ) {
			$current_mail_sent = isset( $order->mail_sent ) ? (int) $order->mail_sent : 0;
			$need_admin        = false;
			$need_customer     = false;

			if ( $current_mail_sent == 0 ) {
				$need_admin    = true;
				$need_customer = true;
			} elseif ( $current_mail_sent == 1 ) {
				$need_admin    = false;
				$need_customer = true;
			} elseif ( $current_mail_sent == 2 ) {
				$need_admin    = true;
				$need_customer = false;
			} elseif ( $current_mail_sent == 3 ) {
				$need_admin    = false;
				$need_customer = false;
			}

			if ( empty( $process_id ) ) {
				$template_id = $dbhandler->get_all_result(
					'EMAIL_TMPL',
					'id',
					array(
						'status' => 1,
						'type'   => 2,
					),
					'var'
				);
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'Failed order refunded' );
			$message               = '<p>' . esc_html( 'Failed order has been refunded' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Ordine fallito rimborsato' );
				$message = '<p>' . esc_html( 'Ordine fallito è stato rimborsato' ) . '</p>';
			}

			// Admin email
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale               = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_refund_order_template_id = $dbhandler->get_global_option_value( 'bm_refund_order_admin_template', 0 );

				if ( ! empty( $admin_refund_order_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_refund_order_template_id, (string) $order_key );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_refund_order_template_id, (string) $order_key );
				} else {
					$admin_email_subject = $subject;
					$admin_email_message = $message;
				}

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-admin-email-layout.php';
				$admin_email_message = ob_get_contents();
				ob_end_clean();

				if ( $admin_old_locale ) {
					$bmrequests->bm_restore_locale( $admin_old_locale );
				}
				$admin_result     = $bm_mail->bm_send_notification_to_shop_admin( $admin_email_subject, $admin_email_message, (string) $order_key );
				$mail_to_admin    = $admin_result['success'];
				$admin_mail_error = $admin_result['error'];
			}

			// Customer email
			if ( $need_customer ) {
				$trp_lang = get_option( 'trp_lang_' . $order->booking_key, false );
				$language = ! empty( $trp_lang ) ? $trp_lang : $language;

				if ( ! empty( $template_id ) ) {
					$template_subject = $bm_mail->bm_get_template_email_subject( $template_id, (string) $order_key, true );
					$template_body    = $bm_mail->bm_get_template_email_content( $template_id, (string) $order_key, true );
				} else {
					$template_subject = $subject;
					$template_body    = $message;
				}

				$customer_email = $bmrequests->bm_fetch_customer_email_from_booking_form_data( (string) $order_key );

				$customer_old_locale = $bmrequests->bm_switch_locale_by_booking_reference( $order->booking_key );

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-email-layout.php';
				$template_body = ob_get_contents();
				ob_end_clean();

				$customer_result  = $bm_mail->bm_send_email_to_customer( $template_subject, $template_body, (string) $order_key );
				$mail_to_customer = $customer_result['success'];
				$cust_mail_error  = $customer_result['error'];

				if ( $customer_old_locale ) {
					$bmrequests->bm_restore_locale( $customer_old_locale );
				}

				$error_msg = array();
				if ( ! empty( $admin_mail_error ) ) {
					$error_msg['admin'] = $admin_mail_error;
				}
				if ( ! empty( $cust_mail_error ) ) {
					$error_msg['customer'] = $cust_mail_error;
				}

				$mail_data = array(
					'module_type'   => 'FAILED_TRANSACTIONS',
					'module_id'     => $order_id,
					'mail_type'     => $mail_type,
					'template_id'   => $template_id,
					'process_id'    => $process_id,
					'mail_to'       => $customer_email,
					'mail_sub'      => wp_kses_post( $template_subject ),
					'mail_body'     => wp_kses_post( stripslashes( $template_body ) ),
					'mail_lang'     => $language,
					'status'        => $mail_to_customer ? 1 : 0,
					'error_message' => ! empty( $error_msg ) ? maybe_serialize( $error_msg ) : '',
				);
			}

			// Update mail_sent in FAILED_TRANSACTIONS table
			$new_mail_sent = $current_mail_sent;
			if ( $mail_to_admin && ! $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 1;
			} elseif ( ! $mail_to_admin && $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 2;
			} elseif ( $mail_to_admin && $mail_to_customer ) {
				$new_mail_sent = $new_mail_sent | 3;
			}

			if ( $new_mail_sent != $current_mail_sent ) {
				$dbhandler->update_row( 'FAILED_TRANSACTIONS', 'id', $order_id, array( 'mail_sent' => $new_mail_sent ), '', '%d' );
			}

			// Log customer email if attempted
			/**if ( $need_customer && ! empty( $mail_data ) ) {
			}*/

			if ( ! empty( $mail_data ) ) {
				$mail_data = $bmrequests->sanitize_request( $mail_data, 'EMAILS' );
				if ( $mail_data != false && $mail_data != null ) {
					$mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );
				}
			}
		}
	}//end bm_flexibooking_mail_on_failed_order_refund()


	/**
	 * Add coupon box in the order page
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_coupon_page() {
			$dbhandler   = new BM_DBhandler();
		$global_inactive = $dbhandler->get_global_option_value( 'bm_inactive_coupons', '0' );

		$bmrequests         = new BM_Request();
		$primary_color      = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
		$contrast           = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
		$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
		$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );

		if ( $global_inactive == 0 ) {
			ob_start();
			?>
		<div id="cpn-data-form">

			<form id="coupon-form" method="post" action="">
				<input type="text" id="input_coupon_code" name="input_coupon_code" placeholder="<?php esc_html_e( 'Enter Coupon Code', 'service-booking' ); ?>">
				<button type="button" name="cpn_data_remove" id="cpn_data_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="resetCpnData()" style="display:none;">✕</button>
				<button type="submit" name="apply_flexi_coupon" id="apply_flexi_coupon" class="apply-coupon-btn" style="border:none;background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>"><?php esc_html_e( 'Apply Coupon', 'service-booking' ); ?></button>
			</form>
			<!-- New coupon implementation starts  -->
			<div id="coupon-message"></div>
			<div id="coupon-container" class="coupon-container"></div>
			<div class="more-coupon" id="myBtn">
				<?php esc_html_e( 'More Coupons', 'service-booking' ); ?>
			</div>
			<!-- The Modal -->
			<div id="coupon-myModal" class="modal">
				<!-- Modal content -->
				<div class="modal-content animate__animated animate__bounceIn">
					<span class="close">&times;</span>
					<h2>Coupons</h2>
					<div id="modal-cpn-container" class="coupon-container"></div>
				</div>
			</div>
			<ul id="discount-coupon-list" class="ordertextbox"></ul>
		</div>
			<?php
			return ob_get_clean();
		}
	} // end bm_flexibooking_coupon_page()


	/**
	 * Coupon code and discount removal
	 *
	 * @author Darpan
	 */
	public function bm_coupon_removal() {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$nonce      = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}
		$post               = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$booking_key        = isset( $post['booking_key'] ) ? esc_attr( $post['booking_key'] ) : '';
		$coupon_code        = isset( $post['coupon_code'] ) ? esc_attr( $post['coupon_code'] ) : '';
		$response['status'] = false;
		$array_event_code   = array();
		$render_suggestion  = '';
		$removed_cpn        = array();
		$timezone           = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

		if ( isset( $booking_key ) && ! empty( $booking_key ) ) {
			if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
			}
			$removed_cpn = $dbhandler->bm_fetch_data_from_transient( 'coupon_removed_' . $booking_key );
			if ( ! is_array( $removed_cpn ) ) {
				$removed_cpn = array();
			}

			if ( isset( $order_data ) && ! empty( $order_data ) ) {
				$applied_coupons      = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
				$applied_coupons      = ! empty( $applied_coupons ) ? $applied_coupons : array();
				$response['amount']   = $order_data['total_cost'];
				$response['discount'] = $order_data['discount'];
				if ( is_array( $applied_coupons ) && count( $applied_coupons ) > 0 ) {
					foreach ( $applied_coupons as $key => $value ) {
						if ( isset( $value['code'] ) && $value['code'] && $value['code'] == $coupon_code ) {
							$render_suggestion = $value['code'];
							$removed_cpn[]     = $value['code'];
							$removed_cpn       = array_unique( $removed_cpn );
							$dbhandler->bm_save_data_to_transient( 'coupon_removed_' . $booking_key, $removed_cpn, 72 );

							$response['amount'] += $value['coupon_discount'];
							if ( is_array( $response ) && isset( $response['discount'] ) ) {
								$response['discount'] -= $value['coupon_discount'];
								$response['discount']  = round( $response['discount'], 2 );
							}
							if ( $response['discount'] < 0 ) {
								$response['discount'] = 0;
							}
							if ( $response['amount'] > $order_data['subtotal'] ) {
								$response['amount'] = round( $order_data['subtotal'], 2 );
							}
							unset( $applied_coupons[ $key ] );
						}
					}
					if ( isset( $render_suggestion ) && ! empty( $render_suggestion ) && $render_suggestion != '' ) {
						$coupon_data       = $dbhandler->get_row( 'COUPON', $render_suggestion, 'coupon_code' );
						$event_description = ! empty( $coupon_data->coupon_description ) ? $coupon_data->coupon_description : esc_html__( 'Description not available', 'service-booking' );
						$date_display      = '∞';
						if ( isset( $coupon_data->expiry_date ) && $coupon_data->expiry_date != null ) {
							$date_display = new DateTime( $coupon_data->expiry_date, new DateTimeZone( $timezone ) );
							$date_display = $date_display->format( 'd F Y' );
						}
						if ( isset( $coupon_data->is_event_coupon ) && $coupon_data->is_event_coupon == 1 && isset( $coupon_data->start_date_val ) ) {
							$event_dates  = isset( $coupon_data->start_date_val ) ? maybe_unserialize( $coupon_data->start_date_val ) : array();
							$now          = new DateTime( 'now', new DateTimeZone( $timezone ) );
							$current_date = $now->format( 'Y-m-d' );

							if ( is_array( $event_dates ) && count( $event_dates ) > 0 ) {
								foreach ( $event_dates as $key => $event_data ) {
									if ( $current_date == $event_data['date'] ) {
										$event_description = ! empty( $event_data['desc'] ) ? $event_data['desc'] : $event_description;
										$date_display      = new DateTime( $event_data['date'], new DateTimeZone( $timezone ) );
										$date_display      = $date_display->format( 'd F Y' );
									}
								}
							}
						}
						$image_display    = $bmrequests->bm_fetch_cpn_image_url_or_guid( $coupon_data->id, 'COUPON', 'url' ) ? $bmrequests->bm_fetch_cpn_image_url_or_guid( $coupon_data->id, 'COUPON', 'url' ) : '';
						$array_event_code = array(
							'code'        => $coupon_data->coupon_code,
							'description' => $event_description,
							'date'        => $date_display,
							'image'       => $image_display,
							'type'        => $coupon_data->discount_type,
							'amount'      => $coupon_data->discount_amount,
						);

						$response['code'] = $array_event_code;
					}

					$applied_coupons = array_values( $applied_coupons );

					$dbhandler->bm_save_data_to_transient( 'coupon_applied_' . $booking_key, $applied_coupons, 72 );
					$dbhandler->bm_save_data_to_transient( 'coupon_final_amount_' . $booking_key, $response['amount'], 72 );

					$response['status']       = true;
					$response['value']        = $applied_coupons;
					$order_data['total_cost'] = round( $response['amount'], 2 );
					if ( $response['discount'] < 0 ) {
						$response['discount'] = 0;
					}
					$order_data['discount'] = round( $response['discount'], 2 );
					if ( isset( $booking_key ) && ! empty( $booking_key ) ) {
						if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
							$dbhandler->bm_save_data_to_transient( 'discounted_' . $booking_key, $order_data, 72 );
						}
					}
				}
			}
		}
		echo wp_json_encode( $response );
		wp_die();
	}//end bm_coupon_removal()


	/**
	 * Add validation for coupon data on checkout page
	 *
	 * @author Darpan
	 */
	public function bm_validate_coupon_code() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}
		$post         = filter_var_array( $_POST['post'], FILTER_DEFAULT );
		$booking_key  = isset( $post['booking_key'] ) ? esc_attr( $post['booking_key'] ) : '';
		$coupon_code  = isset( $post['coupon_code'] ) ? trim( esc_attr( $post['coupon_code'] ) ) : '';
		$email        = isset( $post['email'] ) ? esc_attr( $post['email'] ) : '';
		$country_data = isset( $post['country'] ) ? $post['country'] : array();
		$response     = $this->bm_check_coupon_validity( $booking_key, $coupon_code, $email, $country_data );
		echo wp_json_encode( $response );
		wp_die();
	}//end bm_validate_coupon_code()


	/**
	 * validation checks for coupon data
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_validity( $booking_key, $coupon_code, $email, $country_data = array() ) {
		$dbhandler       = new BM_DBhandler();
		$bmrequests      = new BM_Request();
		$data            = array( 'status' => false );
		$error_array     = array();
		$order_data      = array();
		$applied_coupons = array();
		$global_inactive = $dbhandler->get_global_option_value( 'bm_inactive_coupons', '0' );
		$exists_id       = '';
		$row_data        = '';

		if ( $global_inactive == 1 ) {
			$data['error'][] = esc_html__( 'All coupons are inactive', 'service-booking' );
		} elseif ( empty( $coupon_code ) ) {
			$data['error'][] = esc_html__( 'Please enter the code first', 'service-booking' );
		} else {
			$data['code'] = $coupon_code;
			if ( isset( $booking_key ) && ! empty( $booking_key ) && $booking_key != false ) {
				$exists_id       = $dbhandler->get_value( 'COUPON', 'id', $coupon_code, 'BINARY coupon_code' );
				$applied_coupons = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
				$applied_coupons = ! empty( $applied_coupons ) ? $applied_coupons : array();

				if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
					$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
					if ( $order_data['total_cost'] == 0 ) {
						$data['error'][] = esc_html__( "Can't apply coupon on 'Free Booking'", 'service-booking' );
					}
				} else {
					$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
				}
				if ( isset( $order_data ) && ! empty( $order_data ) ) {
					if ( empty( $exists_id ) ) {
						$data['error'][] = esc_html__( "Coupon doesn't exists", 'service-booking' );
						$data['pass']    = 1;
					} else {
						$row_data     = $dbhandler->get_row( 'COUPON', $exists_id );
						$data['code'] = $row_data->coupon_code;
						$user_data    = array(
							'email'        => $email,
							'country_data' => $country_data,
						);
						$data         = $this->bm_validation_checks_coupon( $exists_id, $applied_coupons, $data, $user_data, $booking_key );
					}
				}
			} else {
				$data['error'][] = esc_html__( 'Booking data not found', 'service-booking' );
			}
		}
		if ( isset( $data['error'] ) && ! empty( $data['error'] ) && count( $data['error'] ) > 0 ) {
			if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
			} else {
				$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
			}
			if ( ! empty( $order_data ) ) {
				$data['original_dis'] = 0;
				$total                = $order_data['total_cost'];
				$applied_coupons      = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
				$applied_coupons      = ! empty( $applied_coupons ) ? $applied_coupons : array();
				$error_array          = $data['error'];
				$data['error']        = $data['error'][0];
				if ( isset( $order_data['discount'] ) && $order_data['discount'] != '' ) {
					$data['original_dis'] = $order_data['discount'];
				}
				$data['amount'] = $total;
			}
		} elseif ( $booking_key != false ) {
				$discount_amt  = isset( $row_data->discount_amount ) ? $row_data->discount_amount : '';
				$discount_type = isset( $row_data->discount_type ) ? $row_data->discount_type : '';
				$exists_id     = isset( $row_data->id ) ? $row_data->id : false;
			if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
			} else {
				$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
			}
			if ( is_array( $order_data ) && isset( $order_data['total_cost'] ) ) {
				$total                 = $order_data['total_cost'];
				$data['original_data'] = $order_data;
				$calculated_discount   = 0;
				$update                = array();
				if ( $discount_type == 'percent' ) {
					$data['status']          = true;
					$calculated_discount     = round( $total * ( $discount_amt / 100 ), 2 );
					$data['coupon_discount'] = $calculated_discount;
					$update['discount']      = $calculated_discount;
					$data['discount']        = $data['coupon_discount'];
					$total                   = $total - $calculated_discount;
					$update['total_cost']    = $total;
					$data['amount']          = $total;
				} elseif ( $discount_type == 'fixed' ) {
					$total               = $total - $discount_amt;
					$calculated_discount = $discount_amt;
					if ( $total < 0 ) {
						$data['status']       = false;
						$data['error']        = esc_html__( "Coupon can't be applied.", 'service-booking' );
						$total                = $order_data['total_cost'];
						$update['total_cost'] = $order_data['total_cost'];
						$update['discount']   = 0;
						$data['original_dis'] = 0;
						if ( isset( $order_data['discount'] ) && ! empty( $order_data['discount'] ) ) {
							$data['original_dis'] = $order_data['discount'];
						}
						$data['amount'] = $total;
					} elseif ( $total == 0 ) {
						$data['status']          = true;
						$total                   = $order_data['total_cost'];
						$update['total_cost']    = 0;
						$update['discount']      = $discount_amt;
						$data['coupon_discount'] = $discount_amt;
						$data['amount']          = 0;
					} else {
						$data['status']          = true;
						$data['coupon_discount'] = $calculated_discount;
						$update['discount']      = $calculated_discount;
						$update['total_cost']    = $total;
						$data['discount']        = $data['coupon_discount'];
						$data['amount']          = $total;
					}
				}
			}

			if ( $data['status'] == true && isset( $data['coupon_discount'] ) ) {
				$removed_cpn       = $dbhandler->bm_fetch_data_from_transient( 'coupon_removed_' . $booking_key );
				$applied_coupons[] = array(
					'code'            => $data['code'],
					'coupon_discount' => $data['coupon_discount'],
				);
				$data['applied']   = $applied_coupons;
				if ( is_array( $removed_cpn ) && count( $removed_cpn ) > 0 && in_array( $data['code'], $removed_cpn ) ) {
					$key = array_search( $data['code'], $removed_cpn );
					if ( ( $key ) !== false ) {
						unset( $removed_cpn[ $key ] );
					}
					$removed_cpn          = array_values( $removed_cpn );
					$data['removed_list'] = $removed_cpn;
					$dbhandler->bm_save_data_to_transient( 'coupon_removed_' . $booking_key, $removed_cpn );
				}
				if ( isset( $order_data['discount'] ) && ( $update['discount'] > 0 ) ) {
						$update['discount'] += $order_data['discount'];
				}
				$order_data['discount']    = round( $update['discount'], 2 );
				$order_data['total_cost']  = round( $update['total_cost'], 2 );
				$data['checkout_discount'] = round( $update['discount'], 2 );
				$dbhandler->bm_save_data_to_transient( 'discounted_' . $booking_key, $order_data, 72 );
				$dbhandler->update_global_option_value( 'discount_' . $booking_key, 1 );
			}
			if ( is_array( $data ) && isset( $data['amount'] ) && isset( $booking_key ) ) {
				$dbhandler->bm_save_data_to_transient( 'coupon_final_amount_' . $booking_key, round( $data['amount'], 2 ), 72 );
				$dbhandler->bm_save_data_to_transient( 'coupon_applied_' . $booking_key, $applied_coupons, 72 );
			}
		}
		$dbhandler->bm_save_data_to_transient( 'coupon_used_' . $booking_key, 1, 72 );
		return $data;
	}//end bm_check_coupon_validity()


	/**
	 * Validation checks combined for both types of coupon
	 *
	 * @author Darpan
	 */
	public function bm_validation_checks_coupon( $exists_id, $applied_coupons, $data, $user_data, $booking_key = '', $woo_cart_total = 0 ) {
		$dbhandler    = new BM_DBhandler();
		$row_data     = $dbhandler->get_row( 'COUPON', $exists_id );
		$email        = isset( $user_data['email'] ) ? $user_data['email'] : '';
		$country_data = isset( $user_data['country_data'] ) ? $user_data['country_data'] : array();

		if ( isset( $booking_key ) && ! empty( $booking_key ) ) {
			if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
				$total      = $order_data['total_cost'];
			} else {
				$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
				$total      = $order_data['total_cost'];
			}
		} else {
			$total      = $woo_cart_total;
			$order_data = $data;
		}
		if ( is_array( $applied_coupons ) && count( $applied_coupons ) > 0 && $applied_coupons != null ) {
			$coupon_array_used = wp_list_pluck( $applied_coupons, 'code' );

			if ( in_array( $data['code'], $coupon_array_used ) ) {
				$data['error'][] = esc_html__( 'Coupon already used', 'service-booking' );
			}
			$is_individual = $this->bm_check_coupon_is_individual( $exists_id );

			if ( $is_individual == 1 ) {
				$data['error'][] = esc_html__( "This coupon can't be merged", 'service-booking' );
			}
			$last_coupon = end( $coupon_array_used );
			if ( $last_coupon && ! empty( $last_coupon ) && $last_coupon != '' ) {
				$last_id            = $dbhandler->get_value( 'COUPON', 'id', $last_coupon, 'coupon_code' );
				$is_individual_last = $this->bm_check_coupon_is_individual( $last_id );

				if ( $is_individual_last == 1 ) {
					$data['error'][] = esc_html__( "You can't use other coupons", 'service-booking' );
				}
			}
		}
		if ( ! empty( $email ) ) {
			$email_excluded = $this->bm_check_coupon_excluded_user_email( $exists_id, $email );
			if ( $email_excluded == 1 ) {
				$data['error'][] = esc_html__( "You can't use this coupon", 'service-booking' );
			}
			$email_used = $this->bm_check_coupon_day_per_user( $exists_id, $email );
			if ( $email_used == 1 ) {
				$data['error'][] = esc_html__( 'The Coupon is already used by the user today', 'service-booking' );
			}
		}
		$active = $row_data->is_active;
		if ( $active == 0 ) {
			$data['error'][] = esc_html__( "The Coupon is inactive and can't be used", 'service-booking' );
		}
		$coupon_expiry = $this->bm_check_coupon_expiry( $exists_id );
		if ( $coupon_expiry == 0 ) {
			$data['error'][] = esc_html__( 'The Coupon is already expired', 'service-booking' );
		}
		if ( $row_data->is_event_coupon == 1 ) {
			$coupon_evnt_used_date = $this->bm_check_coupon_event_usage( $exists_id );
			if ( $coupon_evnt_used_date == 0 ) {
				$data['error'][] = esc_html__( 'The Coupon event date not today', 'service-booking' );
			}
		}
		if ( $row_data->cannot_merged == 1 ) {
			$cannot_merge = $this->bm_check_coupon_is_mergable( $exists_id, $booking_key );
			if ( $cannot_merge == 1 ) {
				$data['error'][] = esc_html__( "The Coupon can't be merged", 'service-booking' );
			}
		}
		if ( $row_data->overall_used_once == 1 ) {
			$once_day = $this->bm_check_usage_once_day( $exists_id );
			if ( $once_day == 1 ) {
				$data['error'][] = esc_html__( 'The Coupon used today already', 'service-booking' );
			}
		}

		$check_avail = $this->bm_check_coupon_days_avail( $exists_id );
		if ( $check_avail == 1 ) {
			$data['error'][] = esc_html__( 'The Coupon is unavaiable on this week day', 'service-booking' );
		}
		$check_avail_date = $this->bm_check_coupon_date_avail( $exists_id );
		if ( $check_avail_date == 0 ) {
			$data['error'][] = esc_html__( 'The Coupon is unavailable today', 'service-booking' );
		}
		$check_usage = $this->bm_check_coupon_usage( $exists_id, $row_data );
		if ( $check_usage == 0 ) {
			$data['error'][] = esc_html__( "The Coupon limit ends and can't be used", 'service-booking' );
		}
		$service_excluded = $this->bm_check_coupon_excluded_service( $exists_id, $order_data );
		if ( $service_excluded == 1 ) {
			$data['error'][] = esc_html__( 'For this service coupon is not applicable', 'service-booking' );
		}
		$service_included = $this->bm_check_coupon_included_service( $exists_id, $order_data );
		if ( $service_included == 1 ) {
			$data['error'][] = esc_html__( 'For this service coupon is not applicable', 'service-booking' );
		}
		$service_used = $this->bm_check_coupon_day_per_service( $exists_id, $order_data );
		if ( $service_used == 1 ) {
			$data['error'][] = esc_html__( 'The Coupon is already used for this service today', 'service-booking' );
		}
		$category_excluded = $this->bm_check_coupon_excluded_category( $exists_id, $order_data );
		if ( $category_excluded == 1 ) {
			$data['error'][] = esc_html__( 'For this category of service coupon is not applicable', 'service-booking' );
		}
		$geographic_usage = $this->bm_check_coupon_geographic_restriction( $exists_id, $country_data );
		if ( $geographic_usage == 1 ) {
			$data['error'][] = esc_html__( 'The coupon cannot be used for this location', 'service-booking' );
		}
		$slot_applicable = $this->bm_check_coupon_slot_applicable( $exists_id, $order_data );
		if ( $slot_applicable == 1 ) {
			$data['error'][] = esc_html__( "The slot can't be used", 'service-booking' );
		}
		$min = isset( $row_data->min_spend ) ? $row_data->min_spend : '';
		$max = isset( $row_data->max_spend ) ? $row_data->max_spend : '';
		if ( ! empty( $min ) && $min > $total ) {
			$data['error'][] = esc_html__( "The Coupon can't be applied for total amount.", 'service-booking' );
		}
		if ( ! empty( $max ) && $max < $total ) {
			$data['error'][] = esc_html__( "The Coupon can't be applied for total amount.", 'service-booking' );
		}
		return $data;
	}//end bm_validation_checks_coupon()


	/**
	 * Add check for coupon expiry
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_expiry( $coupon_id ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$is_event      = $cpn_row->is_event_coupon;
		if ( $is_event == 0 ) {
			$expiry       = $cpn_row->expiry_date;
			$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now          = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date = $now->format( 'Y-m-d' );
			if ( ! empty( $expiry ) && $current_date > $expiry ) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return 1;
		}
	}//end bm_check_coupon_expiry()


	/**
	 * Add check for coupon for event usage
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_event_usage( $coupon_id ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$is_event      = $cpn_row->is_event_coupon;
		$used          = 0;
		if ( $is_event == 1 ) {
			$used_event_dates = isset( $cpn_row->start_date_val ) ? maybe_unserialize( $cpn_row->start_date_val ) : array();
			$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date     = $now->format( 'Y-m-d' );
			if ( is_array( $used_event_dates ) && count( $used_event_dates ) > 0 ) {
				foreach ( $used_event_dates as $key => $events ) {
					if ( $current_date == $events['date'] ) {
						$used = 1;
					}
				}
			}
		}
		return $used;
	}//end bm_check_coupon_event_usage()


	/**
	 * Add check for coupon for mergable with other type
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_is_mergable( $coupon_id, $booking_key ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$cannot_merged = $cpn_row->cannot_merged;
		$used          = 0;
		if ( $cannot_merged == 1 ) {
			$is_event        = $cpn_row->is_event_coupon;
			$applied_coupons = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
			if ( ! empty( $applied_coupons ) && is_array( $applied_coupons ) && count( $applied_coupons ) > 0 && $applied_coupons != null ) {
				$last_used = $applied_coupons[ sizeof( $applied_coupons ) - 1 ];
				if ( isset( $last_used['code'] ) && ! empty( $last_used['code'] ) ) {
					$exists_id     = $dbhandler->get_value( 'COUPON', 'id', $last_used['code'], 'coupon_code' );
					$last_row_data = $dbhandler->get_row( 'COUPON', $exists_id );
					$last_is_event = $last_row_data->is_event_coupon;
					if ( $last_is_event != $is_event ) {
						$used = 1;
					}
				}
			}
		}
		return $used;
	}//end bm_check_coupon_is_mergable()


	/**
	 * Add check for coupon days availability
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_days_avail( $coupon_id ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$unavailbility = isset( $cpn_row->coupon_unavailability ) ? maybe_unserialize( $cpn_row->coupon_unavailability ) : '';
		$avail         = 0;
		if ( isset( $unavailbility['weekdays'] ) && count( $unavailbility['weekdays'] ) > 0 ) {
			$timezone        = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now             = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_weekday = $now->format( 'w' );
			if ( in_array( $current_weekday, $unavailbility['weekdays'] ) ) {
				$avail = 1;
			} else {
				$avail = 0;
			}
		}
		return $avail;
	}//end bm_check_coupon_days_avail()


	/**
	 * Add check for coupon date availability
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_date_avail( $coupon_id ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$unavailbility = isset( $cpn_row->coupon_unavailability ) ? maybe_unserialize( $cpn_row->coupon_unavailability ) : '';
		if ( isset( $unavailbility['dates'] ) && count( $unavailbility['dates'] ) > 0 ) {
			$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now          = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date = $now->format( 'Y-m-d' );
			if ( in_array( $current_date, $unavailbility['dates'] ) ) {
				return 0;
			} else {
				return 1;
			}
		} else {
			return 1;
		}
	}//end bm_check_coupon_date_avail()


	/**
	 * Add check for coupon usage limit
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_usage( $coupon_id, $cpn_row = '' ) {
		if ( empty( $cpn_row ) ) {
			$dbhandler = new BM_DBhandler();
			$cpn_row   = $dbhandler->get_row( 'COUPON', $coupon_id );
		}
		$usage      = isset( $cpn_row->usage_limit ) ? $cpn_row->usage_limit : '';
		$times_used = isset( $cpn_row->coupon_used_count ) ? $cpn_row->coupon_used_count : '';
		if ( $usage == 0 || $usage > $times_used ) {
			return 1;
		} else {
			return 0;
		}
	}//end bm_check_coupon_usage()


	/**
	 * Add check for coupon used once in a day per user
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_day_per_user( $coupon_id, $email_used ) {
			$dbhandler        = new BM_DBhandler();
		$cpn_row              = $dbhandler->get_row( 'COUPON', $coupon_id );
		$used                 = 0;
		$per_person_used_once = isset( $cpn_row->per_person_used_once ) ? $cpn_row->per_person_used_once : '';
		if ( $per_person_used_once == 1 ) {
			$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date     = $now->format( 'Y-m-d' );
			$coupon_used_data = isset( $cpn_row->coupon_used_data ) ? maybe_unserialize( $cpn_row->coupon_used_data ) : '';
			if ( ! empty( $coupon_used_data ) && is_array( $coupon_used_data ) && count( $coupon_used_data ) > 0 ) {
				if ( $coupon_used_data['date'] == $current_date && in_array( $email_used, $coupon_used_data['user_email'] ) ) {
					$used = 1;
				}
			}
		}
		return $used;
	}//end bm_check_coupon_day_per_user()


	/**
	 * Add check for coupon usage once in a day
	 *
	 * @author Darpan
	 */
	public function bm_check_usage_once_day( $coupon_id ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$used          = 0;
		$usage_per_day = isset( $cpn_row->overall_used_once ) ? $cpn_row->overall_used_once : '';
		if ( $usage_per_day == 1 ) {
			$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date     = $now->format( 'Y-m-d' );
			$coupon_used_data = isset( $cpn_row->coupon_used_data ) ? maybe_unserialize( $cpn_row->coupon_used_data ) : '';
			if ( ! empty( $coupon_used_data ) && is_array( $coupon_used_data ) && count( $coupon_used_data ) > 0 ) {
				if ( $coupon_used_data['date'] == $current_date ) {
					$used = 1;
				}
			}
		}
		return $used;
	}//end bm_check_usage_once_day()


	/**
	 * Add check for coupon usage once in a day per service
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_day_per_service( $coupon_id, $service_data = '' ) {
			$dbhandler    = new BM_DBhandler();
		$cpn_row          = $dbhandler->get_row( 'COUPON', $coupon_id );
		$cpn_per_day_srvc = isset( $cpn_row->used_per_coupon_per_service ) ? $cpn_row->used_per_coupon_per_service : '';
		$used             = 0;
		$servc_id         = isset( $service_data['service_id'] ) ? $service_data['service_id'] : '';
		if ( $cpn_per_day_srvc == 1 ) {
			$coupon_used_data = isset( $cpn_row->coupon_used_data ) ? maybe_unserialize( $cpn_row->coupon_used_data ) : '';
			$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date     = $now->format( 'Y-m-d' );
			if ( ! empty( $coupon_used_data ) && is_array( $coupon_used_data ) && count( $coupon_used_data ) > 0 ) {
				if ( $coupon_used_data['date'] == $current_date && is_array( $coupon_used_data['service_id'] ) && in_array( $servc_id, $coupon_used_data['service_id'] ) ) {
					$used = 1;
				}
			}
		}
		return $used;
	}//end bm_check_coupon_day_per_service()


	/**
	 * Add check for geographic restriction country and state wise
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_geographic_restriction( $coupon_id, $service_data = array() ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$is_restricted = isset( $cpn_row->is_geographic_restrictions ) ? $cpn_row->is_geographic_restrictions : '';
		$used          = 0;
		if ( $is_restricted == 1 ) {
			$restriction_data = isset( $cpn_row->geographic_restriction ) ? maybe_unserialize( $cpn_row->geographic_restriction ) : '';
			if ( isset( $restriction_data ) && is_array( $restriction_data ) && count( $restriction_data ) > 0 ) {
				foreach ( $restriction_data as $key => $restriction ) {
					if ( isset( $restriction['state_coupon'] ) ) {
						if ( is_array( $restriction['state_coupon'] ) && count( $restriction['state_coupon'] ) > 0 && isset( $service_data['state'] ) && in_array( $service_data['state'], $restriction['state_coupon'] ) ) {
							$used = 1;
						}
					} elseif ( isset( $service_data['country'] ) && isset( $restriction['country_coupon'] ) && $service_data['country'] == $restriction['country_coupon'] ) {
							$used = 1;
					}
				}
			}
		}
		return $used;
	}//end bm_check_coupon_geographic_restriction()


	/**
	 * Add check for excluded category of coupon
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_excluded_category( $coupon_id, $service_data = '' ) {
			$dbhandler       = new BM_DBhandler();
		$bmrequests          = new BM_Request();
		$cpn_row             = $dbhandler->get_row( 'COUPON', $coupon_id );
		$excluded_conditions = isset( $cpn_row->excluded_conditions ) ? maybe_unserialize( $cpn_row->excluded_conditions ) : '';
		$used                = 0;
		if ( isset( $service_data['service_id'] ) ) {
			$category_id = $bmrequests->bm_fetch_category_id_by_service_id( $service_data['service_id'] );
			if ( isset( $excluded_conditions ) && isset( $excluded_conditions['excluded_category'] ) && count( $excluded_conditions['excluded_category'] ) > 0 ) {
				foreach ( $excluded_conditions['excluded_category'] as $key => $category ) {
					if ( $category_id == $category ) {
						$used = 1;
						break;
					}
				}
			}
		}
		return $used;
	}//end bm_check_coupon_excluded_category()


	/**
	 * Add check for excluded services of coupon
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_excluded_service( $coupon_id, $service_data = array() ) {
			$dbhandler       = new BM_DBhandler();
		$cpn_row             = $dbhandler->get_row( 'COUPON', $coupon_id );
		$excluded_conditions = isset( $cpn_row->excluded_conditions ) ? maybe_unserialize( $cpn_row->excluded_conditions ) : '';
		$used                = 0;
		if ( isset( $excluded_conditions ) && isset( $excluded_conditions['excluded_services'] ) && count( $excluded_conditions['excluded_services'] ) > 0 && isset( $service_data ) && isset( $service_data['service_id'] ) ) {
			foreach ( $excluded_conditions['excluded_services'] as $key => $service ) {
				if ( $service == $service_data['service_id'] ) {
					$used = 1;
					break;
				}
			}
		}
		return $used;
	}//end bm_check_coupon_excluded_service()


	/**
	 * Add check for excluded services of coupon
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_included_service( $coupon_id, $service_data = '' ) {
			$dbhandler     = new BM_DBhandler();
		$cpn_row           = $dbhandler->get_row( 'COUPON', $coupon_id );
		$included_services = isset( $cpn_row->included_services ) ? maybe_unserialize( $cpn_row->included_services ) : array();
		$used              = 0;
		if ( isset( $included_services ) && count( $included_services ) > 0 && isset( $service_data ) && isset( $service_data['service_id'] ) ) {
			if ( ! in_array( $service_data['service_id'], $included_services ) ) {
				$used = 1;
			}
		}
		return $used;
	}//end bm_check_coupon_included_service()


	/**
	 * Add check for excluded user of coupon
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_excluded_user_email( $coupon_id, $user_email ) {
			$dbhandler       = new BM_DBhandler();
		$cpn_row             = $dbhandler->get_row( 'COUPON', $coupon_id );
		$excluded_conditions = isset( $cpn_row->excluded_conditions ) ? maybe_unserialize( $cpn_row->excluded_conditions ) : '';
		$used                = 0;
		$service_category    = array();
		if ( isset( $excluded_conditions ) && isset( $excluded_conditions['excluded_emails'] ) && count( $excluded_conditions['excluded_emails'] ) > 0 ) {
			foreach ( $excluded_conditions['excluded_emails'] as $key => $email ) {
				if ( $email == $user_email ) {
					$used = 1;
					break;
				}
			}
		}
		return $used;
	}//end bm_check_coupon_excluded_user_email()


	/**
	 * Add check for coupon individual use only
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_is_individual( $coupon_id, $service_data = '' ) {
			$dbhandler     = new BM_DBhandler();
		$cpn_row           = $dbhandler->get_row( 'COUPON', $coupon_id );
		$is_individual_use = isset( $cpn_row->is_individual_use ) ? $cpn_row->is_individual_use : '';
		$used              = 0;
		if ( $is_individual_use == 1 ) {
			$used = 1;
		}
		return $used;
	}//end bm_check_coupon_is_individual()


	/**
	 * Parsing the function for coupon validations
	 *
	 * @author Darpan
	 */
	public function bm_parseTimeToMinutes( $time ) {
		if ( strpos( $time, 'AM' ) !== false || strpos( $time, 'PM' ) !== false ) {
			$dateTime = DateTime::createFromFormat( 'h:i A', $time );
		} else {
			$dateTime = DateTime::createFromFormat( 'H:i', $time );
		}
		if ( ! $dateTime ) {
			throw new Exception( "Invalid time format: $time" );
		}
		return $dateTime->format( 'H' ) * 60 + $dateTime->format( 'i' );
	}//end bm_parseTimeToMinutes()


	/**
	 * Add check for coupon individual use only
	 *
	 * @author Darpan
	 */
	public function bm_check_birthday_cpn( $coupon_id, $birthdate = '' ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$is_birthday   = isset( $cpn_row->is_birthday_coupon ) ? $cpn_row->is_birthday_coupon : '';
		$used          = 0;
		if ( $is_birthday == 1 ) {
			$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now          = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date = $now->format( 'Y-m-d' );
			if ( $birthdate == $current_date ) {
				$used = 1;
			}
		}
		return $used;
	}//end bm_check_birthday_cpn()



	/**
	 * Add coupon check for excluded slots
	 *
	 * @author Darpan
	 */
	public function bm_check_coupon_slot_applicable( $coupon_id, $service_data = '' ) {
			$dbhandler = new BM_DBhandler();
		$cpn_row       = $dbhandler->get_row( 'COUPON', $coupon_id );
		$slot_excluded = $cpn_row->unavailable_slot ? maybe_unserialize( $cpn_row->unavailable_slot ) : '';
		$slot_booked   = isset( $service_data['booking_slots'] ) ? $service_data['booking_slots'] : '';
		$used          = 0;
		$data          = array();
		if ( ! empty( $slot_excluded ) && ! empty( $slot_booked ) ) {
			foreach ( $slot_excluded as $key => $slots ) {
				list($booked_start, $booked_end) = explode( ' - ', $slot_booked );
				$excluded_start                  = $slots['start'];
				$excluded_end                    = $slots['end'];
				$booked_start                    = $this->bm_parseTimeToMinutes( $booked_start );
				$booked_end                      = $this->bm_parseTimeToMinutes( $booked_end );
				$excluded_start                  = $this->bm_parseTimeToMinutes( $excluded_start );
				$excluded_end                    = $this->bm_parseTimeToMinutes( $excluded_end );
				if (
					( $booked_start >= $excluded_start && $booked_start < $excluded_end )
					|| ( $booked_end > $excluded_start && $booked_end <= $excluded_end )
				) {
					$used = 1;
				}
			}
			return $used;
		}
	}//end bm_check_coupon_slot_applicable()


	/**
	 * Reset the coupon data from checkout page
	 *
	 * @author Darpan
	 */
	public function bm_reset_coupon_data() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		$data        = array( 'status' => false );
		$post        = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$booking_key = isset( $post['booking_id'] ) ? esc_attr( $post['booking_id'] ) : '';
		if ( isset( $booking_key ) && ! empty( $booking_key ) ) {
			if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
			} else {
				$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
			}
			if ( ! empty( $order_data ) ) {
				$data['status']   = true;
				$data['discount'] = $bmrequests->bm_fetch_price_in_global_settings_format( $order_data['discount'], true );
				$data['total']    = $bmrequests->bm_fetch_price_in_global_settings_format( $order_data['total_cost'], true );
			}
		}
		echo wp_json_encode( $data );
		wp_die();
	}//end bm_reset_coupon_data()


	/**
	 * Fetch event list for current date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_current_day_coupon_list() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}
		$dbhandler        = new BM_DBhandler();
		$bmrequests       = new BM_Request();
		$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date     = $now->format( 'Y-m-d' );
		$data             = array( 'status' => false );
		$booking_key      = filter_input( INPUT_POST, 'booking_key' );
		$array_event_code = array();
		$event_array      = array();
		$global_inactive  = $dbhandler->get_global_option_value( 'bm_inactive_coupons', '0' );
		if ( isset( $booking_key ) && ! empty( $booking_key ) && $global_inactive == 0 ) {
			if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
				$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
			} else {
				$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
			}

			$removed_cpn   = $dbhandler->bm_fetch_data_from_transient( 'coupon_removed_' . $booking_key );
			$coupons       = $dbhandler->get_all_result( 'COUPON', '*', 1, 'results' );
			$discount_used = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
			$discount_used = wp_list_pluck( $discount_used, 'code' );
			if ( isset( $coupons ) && is_array( $coupons ) && count( $coupons ) > 0 ) {
				foreach ( $coupons as $key => $coupon_data ) {
					if ( isset( $coupon_data->is_event_coupon ) && $coupon_data->is_event_coupon == 1 && isset( $coupon_data->start_date_val ) ) {
						$event_dates       = isset( $coupon_data->start_date_val ) ? maybe_unserialize( $coupon_data->start_date_val ) : array();
						$event_description = ! empty( $coupon_data->coupon_description ) ? $coupon_data->coupon_description : esc_html__( 'Description not available', 'service-booking' );
						$event_dates_saved = wp_list_pluck( $event_dates, 'date', 0 );
						if ( is_array( $event_dates ) && count( $event_dates ) > 0 ) {
							foreach ( $event_dates as $key => $event_data ) {
								if ( $current_date == $event_data['date'] && ! in_array( $coupon_data->coupon_code, $discount_used ) ) {
									$event_description  = ! empty( $event_data['desc'] ) ? $event_data['desc'] : $event_description;
									$date_display       = '∞';
									$timezone           = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
									$date_display       = new DateTime( $event_data['date'], new DateTimeZone( $timezone ) );
									$date_display       = $date_display->format( 'd F Y' );
									$image_display      = $bmrequests->bm_fetch_cpn_image_url_or_guid( $coupon_data->id, 'COUPON', 'url' ) ? $bmrequests->bm_fetch_cpn_image_url_or_guid( $coupon_data->id, 'COUPON', 'url' ) : '';
									$array_event_code[] = array(
										'code'        => $coupon_data->coupon_code,
										'description' => $event_description,
										'date'        => $date_display,
										'image'       => $image_display,
										'type'        => $coupon_data->discount_type,
										'amount'      => $coupon_data->discount_amount,
									);
									$event_array[]      = $coupon_data->coupon_code;
								}
							}
						}
					}
				}
				if ( is_array( $removed_cpn ) && count( $removed_cpn ) > 0 ) {
					foreach ( $removed_cpn as $key => $cpn ) {
						if ( ! in_array( $cpn, $event_array ) && ! in_array( $cpn, $discount_used ) ) {
							$coupon_data = $dbhandler->get_row( 'COUPON', $cpn, 'coupon_code' );

							if ( isset( $coupon_data->coupon_code ) && $coupon_data->coupon_code != null ) {
								$date_display      = '∞';
								$event_description = ! empty( $coupon_data->coupon_description ) ? $coupon_data->coupon_description : esc_html__( 'Description not available', 'service-booking' );
								if ( isset( $coupon_data->expiry_date ) && $coupon_data->expiry_date != null ) {
									$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
									$date_display = new DateTime( $coupon_data->expiry_date, new DateTimeZone( $timezone ) );
									$date_display = $date_display->format( 'd F Y' );
								}
								if ( isset( $coupon_data->is_event_coupon ) && $coupon_data->is_event_coupon == 1 && isset( $coupon_data->start_date_val ) ) {
									$event_dates = isset( $coupon_data->start_date_val ) ? maybe_unserialize( $coupon_data->start_date_val ) : array();
									if ( is_array( $event_dates ) && count( $event_dates ) > 0 ) {
										foreach ( $event_dates as $key => $event_data ) {
											if ( $current_date == $event_data['date'] && ! in_array( $coupon_data->coupon_code, $discount_used ) ) {
												$event_description = ! empty( $event_data['desc'] ) ? $event_data['desc'] : $event_description;
												$timezone          = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
												$date_display      = new DateTime( $event_data['date'], new DateTimeZone( $timezone ) );
												$date_display      = $date_display->format( 'd F Y' );
											}
										}
									}
								}
								$image_display      = $bmrequests->bm_fetch_cpn_image_url_or_guid( $coupon_data->id, 'COUPON', 'url' ) ? $bmrequests->bm_fetch_cpn_image_url_or_guid( $coupon_data->id, 'COUPON', 'url' ) : '';
								$array_event_code[] = array(
									'code'        => $coupon_data->coupon_code,
									'description' => $event_description,
									'date'        => $date_display,
									'image'       => $image_display,
									'type'        => $coupon_data->discount_type,
									'amount'      => $coupon_data->discount_amount,
								);
							}
						}
					}
				}
			}
			if ( ! empty( $array_event_code ) ) {
				$data['status'] = true;
				$data['code']   = $array_event_code;
			}
		}
		echo wp_json_encode( $data );
		wp_die();
	}//end bm_fetch_current_day_coupon_list()


	/**
	 * Check the auto apply enable
	 *
	 * @author Darpan
	 */
	public function bm_check_auto_apply_enable( $coupon_code ) {
			$dbhandler     = new BM_DBhandler();
		$bmrequests        = new BM_Request();
		$auto_apply_global = $dbhandler->get_global_option_value( 'bm_auto_apply_coupon', '0' );
		$applicable        = 0;
		if ( $auto_apply_global == 1 ) {
			$cpn_row = $dbhandler->get_row( 'COUPON', $coupon_code, 'coupon_code' );
			if ( isset( $cpn_row->auto_apply ) && $cpn_row->auto_apply == 1 ) {
				$applicable = 1;
			}
		}
		return $applicable;
	}//end bm_check_auto_apply_enable()


	/**
	 * Fetch auto apply coupon data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_auto_apply_coupon() {
			$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}
		$dbhandler       = new BM_DBhandler();
		$bmrequests      = new BM_Request();
		$timezone        = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now             = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date    = $now->format( 'Y-m-d' );
		$data            = array( 'status' => false );
		$post            = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$booking_key     = isset( $post['booking_id'] ) ? esc_attr( $post['booking_id'] ) : '';
		$email           = isset( $post['email'] ) ? esc_attr( $post['email'] ) : '';
		$auto_apply_list = array();

		if ( isset( $booking_key ) && ! empty( $booking_key ) && $booking_key != false ) {
			$pre_applied_list = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
			$coupon_used      = $dbhandler->bm_fetch_data_from_transient( 'coupon_used_' . $booking_key );

			if ( ( ! empty( $pre_applied_list ) && is_array( $pre_applied_list ) && count( $pre_applied_list ) > 0 ) || $coupon_used == 1 ) {
				$data['status']    = true;
				$data['code_data'] = $pre_applied_list;
				if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
					$order_data    = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
					$data['order'] = $order_data;
				} else {
					$data['order'] = 'else';
					$order_data    = $dbhandler->bm_fetch_data_from_transient( $booking_key );
				}
				$data['discount'] = isset( $order_data['discount'] ) ? round( $order_data['discount'], 2 ) : '';
				$data['amount']   = isset( $order_data['total_cost'] ) ? round( $order_data['total_cost'], 2 ) : '';
			} else {
				$coupons    = $dbhandler->get_all_result( 'COUPON', 'coupon_code', 1, 'results' );
				$auto_limit = $dbhandler->get_global_option_value( 'bm_auto_apply_limit' );
				$auto_limit = $auto_limit > 0 ? floor( $auto_limit ) : 2;
				$count      = 0;
				if ( isset( $coupons ) && is_array( $coupons ) && count( $coupons ) > 0 ) {
					foreach ( $coupons as $key => $coupon_code ) {
						$cpn         = $coupon_code->coupon_code;
						$auto_enable = $this->bm_check_auto_apply_enable( $cpn );
						if ( $auto_enable == 1 ) {
							$response = $this->bm_check_coupon_validity( $booking_key, $cpn, $email );
							if ( $response['status'] == true && $auto_limit > $count ) {
								$auto_apply_list[ $count ] = $response;
								++$count;
							}
						}
						if ( $count == $auto_limit ) {
							break;
						}
					}
					if ( ! empty( $auto_apply_list ) ) {
						$data['status']    = true;
						$data['code_data'] = $auto_apply_list;
						if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
							$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
						} else {
							$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
						}
						$data['discount'] = round( $order_data['discount'], 2 );
						$data['amount']   = round( $order_data['total_cost'], 2 );
					}
				}
			}
		}
		echo wp_json_encode( $data );
		wp_die();
	}//end bm_fetch_auto_apply_coupon()


	/**
	 * Saving the coupon rows after confirmation
	 *
	 * @author Darpan
	 */
	public function bm_after_booking_saved_callback( $booking_id, $order_data ) {

		$dbhandler      = new BM_DBhandler();
		$timezone       = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now            = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date   = $now->format( 'Y-m-d' );
		$user_array     = array();
		$service_id     = array();
		$coupon_applied = isset( $order_data['coupons'] ) ? $order_data['coupons'] : false;
		if ( isset( $coupon_applied ) && ! empty( $coupon_applied ) && $coupon_applied != false ) {
			$cpn_array = explode( ',', $coupon_applied );
			if ( is_array( $cpn_array ) && count( $cpn_array ) > 0 ) {
				foreach ( $cpn_array as $key => $coupon_code ) {
					$exist_data = $dbhandler->get_all_result( 'COUPON', '*', array( 'coupon_code' => $coupon_code ), 'results', 0, false, null, false, '', 'ARRAY_A' );
					$exist_data = $exist_data[0];
					$id         = isset( $exist_data['id'] ) ? $exist_data['id'] : false;
					if ( isset( $id ) && ! empty( $id ) ) {
						$booking_data = $dbhandler->get_row( 'BOOKING', $booking_id, 'id' );
						$customer_id  = $dbhandler->get_value( 'BOOKING', 'customer_id', $booking_id, 'id' );
						$user_email   = $dbhandler->get_value( 'CUSTOMERS', 'customer_email', $customer_id, 'id' );
						$data         = isset( $exist_data['coupon_used_data'] ) ? maybe_unserialize( $exist_data['coupon_used_data'] ) : false;
						$count        = isset( $exist_data['coupon_used_count'] ) ? $exist_data['coupon_used_count'] : 0;
						++$count;
						if ( $data != false && is_array( $data ) && isset( $data['date'] ) && $data['date'] == $current_date ) {
							if ( ! empty( $user_email ) && ! in_array( $user_email, $data['user_email'] ) ) {
								$data['user_email'][] = $user_email;
							}
							if ( is_array( $order_data ) && isset( $order_data['service_id'] ) && ! in_array( $order_data['service_id'], $data['service_id'] ) ) {
								$data['service_id'][] = $order_data['service_id'];
							}
							$coupon_data = maybe_serialize( $data );
							$update_data = array(
								'coupon_used_data'  => $coupon_data,
								'coupon_used_count' => $count,
							);
						} else {
							if ( is_array( $order_data ) && isset( $order_data['service_id'] ) ) {
								$service_id = $order_data['service_id'];
							}
							$coupo_res   = array(
								'date'       => $current_date,
								'user_email' => array( $user_email ),
								'service_id' => array( $service_id ),
							);
							$coupon_data = maybe_serialize(
								array(
									'date'       => $current_date,
									'user_email' => array( $user_email ),
									'service_id' => array( $service_id ),
								)
							);
							$update_data = array(
								'coupon_used_data'  => $coupon_data,
								'coupon_used_count' => $count,
							);
						}
						if ( isset( $update_data ) && is_array( $update_data ) && count( $update_data ) > 0 ) {
							$dbhandler->update_row( 'COUPON', 'id', $id, $update_data, '', '%d' );
						}
					}
				}
			}
		}
	}//end bm_after_booking_saved_callback()


	/**
	 * Clear session data for woo checkout coupons
	 *
	 * @author Darpan
	 */
	public function bm_clear_woo_coupons_after_checkout( $order_id ) {
		WC()->session->__unset( 'bm_applied_woo_coupons' );
	}


	/**
	 * Clear session data for woo checkout coupons
	 *
	 * @author Darpan
	 */
	public function bm_refresh_cart_after_checkout() {
		WC()->cart->calculate_totals();
		WC()->cart->set_session();
	}


	/**
	 * Update data for order booking from woo
	 *
	 * @author Darpan
	 */
	public function bm_update_coupon_woo_checkout( $order_id ) {
		if ( ! $order_id ) {
			return;
		}
		$dbhandler        = new BM_DBhandler();
		$flexi_booking_id = get_post_meta( $order_id, '_flexi_booking_id', true );
		$booking_key      = get_post_meta( $order_id, '_flexi_booking_key', true );
		$booking_data     = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		$service_id       = $booking_data['service_id'] ?? 0;
		if ( ! empty( $flexi_booking_id ) ) {
			$order          = wc_get_order( $order_id );
			$user_email     = $order->get_billing_email();
			$coupon_applied = WC()->session->get( 'bm_applied_woo_coupons', array() );
			$timezone       = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
			$now            = new DateTime( 'now', new DateTimeZone( $timezone ) );
			$current_date   = $now->format( 'Y-m-d' );
			$user_array     = array();

			if ( isset( $coupon_applied ) && ! empty( $coupon_applied ) && $coupon_applied != false ) {
				$cpn_array = wp_list_pluck( $coupon_applied, 'code' );
				if ( is_array( $cpn_array ) && count( $cpn_array ) > 0 && isset( $service_id ) && $service_id > 0 ) {
					foreach ( $cpn_array as $key => $coupon_code ) {
						$exist_data = $dbhandler->get_all_result( 'COUPON', '*', array( 'coupon_code' => $coupon_code ), 'results', 0, false, null, false, '', 'ARRAY_A' );
						$exist_data = $exist_data[0];

						$id = isset( $exist_data['id'] ) ? $exist_data['id'] : false;
						if ( isset( $id ) && ! empty( $id ) ) {
							$data  = isset( $exist_data['coupon_used_data'] ) ? maybe_unserialize( $exist_data['coupon_used_data'] ) : false;
							$count = isset( $exist_data['coupon_used_count'] ) ? $exist_data['coupon_used_count'] : 0;
							++$count;

							if ( $data != false && is_array( $data ) && isset( $data['date'] ) && $data['date'] == $current_date ) {
								if ( ! empty( $user_email ) && ! in_array( $user_email, $data['user_email'] ) ) {
									$data['user_email'][] = $user_email;
								}
								if ( isset( $service_id ) && is_array( $data['service_id'] ) && ! in_array( $service_id, $data['service_id'] ) ) {
									$data['service_id'][] = $service_id;
								}
								$coupon_data = maybe_serialize( $data );
								$update_data = array(
									'coupon_used_data'  => $coupon_data,
									'coupon_used_count' => $count,
								);
							} else {
								$coupon_data = maybe_serialize(
									array(
										'date'       => $current_date,
										'user_email' => array( $user_email ),
										'service_id' => array( $service_id ),
									)
								);
								$update_data = array(
									'coupon_used_data'  => $coupon_data,
									'coupon_used_count' => $count,
								);
							}
							if ( isset( $update_data ) && is_array( $update_data ) && count( $update_data ) > 0 ) {
								$dbhandler->update_row( 'COUPON', 'id', $id, $update_data, '', '%d' );
							}
						}
					}
					$global_applied_coupons = $order->get_coupon_codes();

					$applied_coupons = WC()->session->get( 'bm_applied_woo_coupons', array() );
					if ( ! empty( $global_applied_coupons ) ) {
						if ( ! empty( $applied_coupons ) ) {
							$coupon_array_used = wp_list_pluck( $applied_coupons, 'code' );
							$wc_coupons        = array_values( array_diff( $global_applied_coupons, $coupon_array_used ) );
						}
					}
					$wc_coupon_applied = ! empty( $wc_coupons ) ? implode( ',', $wc_coupons ) : null;
					$coupon_applied    = ! empty( $coupon_applied ) ? implode( ',', array_column( $coupon_applied, 'code' ) ) : null;
					$update_book_data  = array(
						'coupons'    => $coupon_applied,
						'wc_coupons' => $wc_coupon_applied,
					);
					$dbhandler->update_row( 'BOOKING', 'id', $flexi_booking_id, $update_book_data, '', '%s' );
				}
			}
		}
	}


	/**
	 * Adding the Flexi coupon on fly for validating with woo checkout
	 *
	 * @author Darpan
	 */
	public function bm_apply_flexi_cpn_woo( $coupon_data, $coupon_code ) {
		$dbhandler = new BM_DBhandler();
		if ( WC()->cart === null ) {
			return;
		}
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				$exists_id = $dbhandler->get_value( 'COUPON', 'id', $coupon_code, 'coupon_code' );
				if ( $exists_id && ! empty( $exists_id ) ) {
					$row_data = $dbhandler->get_row( 'COUPON', $exists_id );
					if ( $row_data ) {
						$type = $row_data->discount_type;
						if ( $type == 'fixed' ) {
							$type = 'fixed_cart';
						} else {
							$type = 'percent';
						}
						return array(
							'id'                 => $row_data->id,
							'code'               => $row_data->coupon_code,
							'amount'             => $row_data->discount_amount,
							'discount_type'      => $type,
							'individual_use'     => false,
							'product_ids'        => array(),
							'usage_limit'        => $row_data->usage_limit,
							'usage_count'        => '',
							'date_expires'       => '',
							'free_shipping'      => false,
							'exclude_sale_items' => false,
							'minimum_amount'     => floatval( $row_data->min_spend ),
							'maximum_amount'     => floatval( $row_data->max_spend ),
						);
					}
				}
			}
		}

		return $coupon_data;
	}



	/**
	 * Additional validations check for flexi coupon on woo checkout
	 *
	 * @author Darpan
	 */
	public function bm_validate_woo_checkout_cpn( $true, $coupon, $that ) {
		// if( did_action( 'woocommerce_coupon_is_valid' )){
		// return;
		// }

		$coupon_code = $coupon->get_code();
		if ( WC()->cart === null ) {
			return;
		}
		$dbhandler       = new BM_DBhandler();
		$customer        = WC()->customer;
		$email           = WC()->checkout()->get_value( 'billing_email' );
		$billing_country = WC()->checkout()->get_value( 'billing_country' );
		$billing_state   = WC()->checkout()->get_value( 'billing_state' );
		$states          = WC()->countries->get_states( $billing_country );
		$billing_state   = isset( $states[ $billing_state ] ) ? $states[ $billing_state ] : $billing_state;

		$user_data = array(
			'email'        => $email,
			'country_data' => array(
				'country' => $billing_country,
				'state'   => $billing_state,
			),
		);
		if ( $coupon_code ) {
			$total                  = WC()->cart->subtotal;
			$global_applied_coupons = WC()->cart->get_applied_coupons();
			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
					if ( ! empty( $global_applied_coupons ) ) {
						$total_discount = 0;
						foreach ( $global_applied_coupons as $coupon_code_save ) {
							$coupon          = new WC_Coupon( $coupon_code_save );
							$discount        = WC()->cart->get_coupon_discount_amount( $coupon_code_save );
							$total_discount += $discount;
						}
						if ( $total_discount > 0 ) {
							$total = $total - $total_discount;
						}
					}
					$flexi_booking_key = isset( $cart_item['flexi_booking_key'] ) ? $cart_item['flexi_booking_key'] : '';
					$booking_data      = $dbhandler->bm_fetch_data_from_transient( $flexi_booking_key );
					if ( ! empty( $booking_data ) ) {
						$data['booking_slots'] = isset( $booking_data['booking_slots'] ) ? $booking_data['booking_slots'] : '';
					}
					if ( isset( $cart_item['flexi_extra_svc_price'] ) && is_array( $cart_item['flexi_extra_svc_price'] ) ) {
						continue;
					}
					$service_id         = $dbhandler->get_value( 'SERVICE', 'id', $cart_item['product_id'], 'wc_product' );
					$data['service_id'] = $service_id;
					$global_inactive    = $dbhandler->get_global_option_value( 'bm_inactive_coupons', '0' );

					if ( $global_inactive == 1 ) {
						$data['error'][] = esc_html__( 'All Flexi coupons are inactive', 'service-booking' );
					} elseif ( empty( $coupon_code ) ) {
						$data['error'][] = esc_html__( 'Please enter the code first', 'service-booking' );
					} else {
						$data['code']    = $coupon_code;
						$exists_id       = $dbhandler->get_value( 'COUPON', 'id', $coupon_code, 'coupon_code' );
						$applied_coupons = WC()->session->get( 'bm_applied_woo_coupons', array() );
						if ( $total == 0 ) {
							$data['error'][] = esc_html__( "Can't apply coupon on 'Free Booking'", 'service-booking' );
						}
						if ( isset( $total ) && ! empty( $total ) && $total > 0 ) {
							if ( empty( $exists_id ) ) {
								$data['error'][] = esc_html__( "Coupon doesn't exists", 'service-booking' );
								$data['pass']    = 1;
							} else {
								$row_data          = $dbhandler->get_row( 'COUPON', $exists_id );
								$data['code']      = $row_data->coupon_code;
								$coupon_array_used = wp_list_pluck( $applied_coupons, 'code' );
								if ( is_array( $coupon_array_used ) && in_array( strtolower( $data['code'] ), $coupon_array_used ) ) {
									return $true;
								}
								$data          = $this->bm_validation_checks_coupon( $exists_id, $applied_coupons, $data, $user_data, '', $total );
								$discount_amt  = isset( $row_data->discount_amount ) ? $row_data->discount_amount : '';
								$discount_type = isset( $row_data->discount_type ) ? $row_data->discount_type : '';
								if ( $discount_type == 'fixed' && $total < $discount_amt ) {
									$data['error'][] = esc_html__( 'Coupon amount exceeds than total', 'service-booking' );
								}
							}
						}
					}
					if ( isset( $data['error'] ) && is_array( $data['error'] ) && count( $data['error'] ) > 0 ) {
						if ( isset( $data['pass'] ) && $data['pass'] == 1 ) {
							return $true;
						}
						throw new Exception( __( $data['error'][0], 'woocommerce' ) );
						return false;
					}
				}
			}
		}
		return $true;
	}



	/**
	 * Updating applied coupon list on woo checkout
	 *
	 * @author Darpan
	 */
	public function bm_update_list_cpn( $coupon_code ) {
		if ( WC()->cart === null ) {
			return;
		}
		$dbhandler = new BM_DBhandler();

		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				if ( isset( $cart_item['flexi_booking_key'] ) ) {
					$booking_key = $cart_item['flexi_booking_key'];
					if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
						$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
					} else {
						$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
					}
					$cart     = WC()->cart;
					$discount = $cart->get_discount_total();
					$total    = $cart->get_total();
				}
				$exists_id = $dbhandler->get_value( 'COUPON', 'id', $coupon_code, 'coupon_code' );
				if ( $exists_id && ! empty( $exists_id ) ) {
					$applied_coupons   = WC()->session->get( 'bm_applied_woo_coupons', array() );
					$coupon_array_used = wp_list_pluck( $applied_coupons, 'code' );
					if ( is_array( $coupon_array_used ) && ! in_array( $coupon_code, $coupon_array_used ) ) {
							$applied_coupons[] = array(
								'code' => $coupon_code,
							);
							WC()->session->set( 'bm_applied_woo_coupons', $applied_coupons );
							break;
					}
				}
			}
		}
	}


	/**
	 * Updating applied coupon list on woo checkout after removal
	 *
	 * @author Darpan
	 */
	public function bm_remove_list_cpn( $coupon_code ) {
		if ( WC()->cart === null ) {
			return;
		}
		foreach ( WC()->cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				$dbhandler = new BM_DBhandler();
				$exists_id = $dbhandler->get_value( 'COUPON', 'id', $coupon_code, 'coupon_code' );
				if ( $exists_id && ! empty( $exists_id ) ) {
					$applied_coupons = WC()->session->get( 'bm_applied_woo_coupons', array() );
					if ( is_array( $applied_coupons ) && count( $applied_coupons ) > 0 ) {
						foreach ( $applied_coupons as $key => $value ) {
							if ( $value['code'] == $coupon_code ) {
								unset( $applied_coupons[ $key ] );
								WC()->session->set( 'bm_applied_woo_coupons', array_values( $applied_coupons ) );
								break;
							}
						}
					}
				}
			}
		}
	}//end bm_remove_list_cpn()


	/**
	 * Removing the coupon from cart
	 *
	 * @author Darpan
	 */
	public function bm_refresh_cart_on_woo() {
		if ( did_action( 'wp_loaded' ) && function_exists( 'WC' ) ) {
			if ( WC()->cart === null ) {
				return;
			}
			WC()->cart->remove_coupons();
		}
	}//end bm_refresh_cart_on_woo()


	public function bm_update_discount_transient( $cart ) {
		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}
		$dbhandler = new BM_DBhandler();
		foreach ( $cart->get_cart() as $cart_item ) {
			if ( isset( $cart_item['added_by_flexibooking'] ) && $cart_item['added_by_flexibooking'] ) {
				if ( isset( $cart_item['flexi_booking_key'] ) ) {
					$booking_key = $cart_item['flexi_booking_key'];
					if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
						$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
					} else {
						$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
					}
					$discount = $cart->get_discount_total();
					$total    = $cart->get_total( 'edit' );
					if ( isset( $discount ) && $discount > 0 ) {
						$order_data['discount']   = floatval( $discount );
						$order_data['total_cost'] = floatval( $total );
						$dbhandler->bm_save_data_to_transient( 'discounted_' . $booking_key, $order_data, 72 );
						$dbhandler->update_global_option_value( 'discount_' . $booking_key, 1 );
					} else {
						$dbhandler->bm_delete_transient( 'discounted_' . $booking_key );
						$dbhandler->update_global_option_value( 'discount_' . $booking_key, 0 );
					}
				}
			}
		}
	}
}//end class
