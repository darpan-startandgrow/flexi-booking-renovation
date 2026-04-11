<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin
 * @author     Start and Grow <laravel6@startandgrow.in>
 */
class Booking_Management_Admin {

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


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since 1.0.0
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_styles() {
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

		if ( is_user_logged_in() ) {
			$screen = get_current_screen();

			wp_enqueue_style( 'jquery-ui-styles' );
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_style( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'css/booking-management-jquery-ui.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'jquery-ui-smoothness', plugin_dir_url( __FILE__ ) . 'css/smoothness-jquery-ui.min.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'font-awesome', plugin_dir_url( __FILE__ ) . 'css/font-awesome.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'googleFonts', plugin_dir_url( __FILE__ ) . 'css/googleFonts.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'material-icon', plugin_dir_url( __FILE__ ) . 'css/material-icons.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'ui-tooltip', plugin_dir_url( __FILE__ ) . 'css/booking-management-tooltip.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'intl-tel-input', plugin_dir_url( __FILE__ ) . 'css/booking-management-intl-tel-input.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'ui-dialog-custom', plugin_dir_url( __FILE__ ) . 'css/booking-management-ui-dialog-custom.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'multiselect', plugin_dir_url( __FILE__ ) . 'css/booking-management-multiselect.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'flexi-animate', plugin_dir_url( __FILE__ ) . 'css/booking-management-animate.css', array(), $this->version, 'all' );
			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/booking-management-admin.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'flexi-daterangepicker', plugin_dir_url( __FILE__ ) . 'css/booking-management-daterangepicker.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'flexi-service-booking-planner', plugin_dir_url( __FILE__ ) . 'css/booking-management-service-booking-planner.css', array(), $this->version, 'all' );
			wp_enqueue_style( 'flexi-single-service-booking-planner', plugin_dir_url( __FILE__ ) . 'css/booking-management-single-service-booking-planner.css', array(), $this->version, 'all' );
            if ( $screen->base == 'toplevel_page_bm_home' ) {
                wp_enqueue_style( 'dashboard-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-dashboard.css', array(), $this->version, 'all' );
            }
            if ( $screen->base == 'flexibooking_page_bm_email_records' ) {
                wp_enqueue_style( 'resend-email-custom', plugin_dir_url( __FILE__ ) . 'css/booking-management-resend-email-custom.css', array(), $this->version, 'all' );
            }
            if ( $screen->base == 'admin_page_bm_customer_profile' ) {
                wp_enqueue_style( 'customer-profile-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-customer-profile.css', array(), $this->version, 'all' );
            }
            if ( $screen->base == 'admin_page_bm_customer_profile' || $screen->base == 'flexibooking_page_bm_check_ins' || $screen->base == 'flexibooking_page_bm_booking_analytics' ) {
                wp_enqueue_style( 'jquery-datatable-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-jquery-datatable.css', array(), $this->version, 'all' );
                wp_enqueue_style( 'jquery-datatable-buttons-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-jquery-datatable-buttons.css', array(), $this->version, 'all' );
                wp_enqueue_style( 'jquery-datatable-select-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-jquery-datatable-select.css', array(), $this->version, 'all' );
            }
            if ( $screen->base == 'flexibooking_page_bm_check_ins' ) {
                wp_enqueue_style( 'check-in-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-check-ins.css', array(), $this->version, 'all' );
                wp_enqueue_style( 'resend-email-custom', plugin_dir_url( __FILE__ ) . 'css/booking-management-resend-email-custom.css', array(), $this->version, 'all' );
            }
            if ( $screen->base == 'flexibooking_page_bm_booking_analytics' ) {
                wp_enqueue_style( 'analytics', plugin_dir_url( __FILE__ ) . 'css/booking-management-analytics.css', array(), $this->version, 'all' );
            }
            if ( $screen->base == 'admin_page_bm_add_coupon' || $screen->base == 'flexibooking_page_bm_all_coupons' ) {
                wp_enqueue_style( 'coupon-module-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-coupon.css', array(), $this->version, 'all' );
            }

			if ( $screen->base == 'admin_page_bm_single_order' ) {
				wp_enqueue_style( 'single-order-css', plugin_dir_url( __FILE__ ) . 'css/booking-management-single-order.css', array(), $this->version, 'all' );
			}
		} //end if
	}//end enqueue_styles()


	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
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

		if ( is_user_logged_in() ) {
			$dbhandler   = new BM_DBhandler();
			$bmrequests  = new BM_Request();
			$post_id     = get_the_ID();
			$post        = get_post( $post_id );
			$screen      = get_current_screen();
			$plugin_path = plugin_dir_url( __FILE__ );

			$age_groups = array(
				'0' => array(
					'name' => esc_html__( 'Infant', 'service-booking' ),
					'type' => 'infant',
					'from' => '0',
					'to'   => '2',
				),
				'1' => array(
					'name' => esc_html__( 'Children', 'service-booking' ),
					'type' => 'children',
					'from' => '3',
					'to'   => '17',
				),
				'2' => array(
					'name' => esc_html__( 'Adult', 'service-booking' ),
					'type' => 'adult',
					'from' => '18',
					'to'   => '40',
				),
				'3' => array(
					'name' => esc_html__( 'Senior', 'service-booking' ),
					'type' => 'senior',
					'from' => '41',
					'to'   => '100',
				),
			);

			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_Script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'jquery-form' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_media();

			wp_enqueue_script( 'jquery-ui', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-ui.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'intl-tel-input', plugin_dir_url( __FILE__ ) . 'js/booking-management-intl-tel-input.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'multiselect', plugin_dir_url( __FILE__ ) . 'js/booking-management-multiselect.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'chart-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-chart.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/booking-management-admin.js', array( 'jquery' ), $this->version, false );
			wp_enqueue_script( 'jquery-datepicker-i18n', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datepicker-i18n.min.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'jquery-moment', plugin_dir_url( __FILE__ ) . 'js/booking-management-momentjs.js', array( 'jquery' ), $this->version, true );
			wp_enqueue_script( 'jquery-fullcalendar', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-fullcalendar.js', array( 'jquery', 'jquery-moment' ), $this->version, true );
			wp_enqueue_script( 'fullcalendar-moment', plugin_dir_url( __FILE__ ) . 'js/booking-management-fullcalendar-moment.js', array( 'jquery', 'jquery-fullcalendar', 'jquery-moment' ), $this->version, true );
			wp_enqueue_script( 'jquery-daterangepicker', plugin_dir_url( __FILE__ ) . 'js/booking-management-daterangepicker.js', array( 'jquery', 'jquery-fullcalendar', 'fullcalendar-moment', 'jquery-moment' ), $this->version, true );
			wp_enqueue_script( 'single-service-planner', plugin_dir_url( __FILE__ ) . 'js/booking-management-single-service-booking-planner.js', array( 'jquery', 'jquery-moment', 'fullcalendar-moment', 'jquery-fullcalendar', 'jquery-daterangepicker' ), $this->version, true );
			wp_enqueue_script( 'service-planner', plugin_dir_url( __FILE__ ) . 'js/booking-management-service-booking-planner.js', array( 'jquery', 'jquery-moment', 'fullcalendar-moment', 'jquery-fullcalendar', 'jquery-daterangepicker' ), $this->version, true );

			if ( $screen->base == 'toplevel_page_bm_home' ) {
				wp_enqueue_script( 'dashboard-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-dashboard.js', array( 'jquery' ), $this->version, false );
			}

			if ( $screen->base == 'admin_page_bm_customer_profile' ) {
				wp_enqueue_script( 'customer-profile-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-customer-profile.js', array( 'jquery' ), $this->version, false );
			}

            if ( $screen->base == 'admin_page_bm_customer_profile' || $screen->base == 'flexibooking_page_bm_check_ins' || $screen->base == 'flexibooking_page_bm_booking_analytics' ) {
                wp_enqueue_script( 'jquery-datatable-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'datatables-buttons-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-buttons.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'datatables-colvis-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-colvis.js', array( 'datatables-buttons-js' ), $this->version, true );
                wp_enqueue_script( 'datatables-jszip', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-jszip.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'datatables-pdfmake', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-pdfmake.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'datatables-vfs-fonts', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-vfs-fonts.js', array( 'jquery' ), $this->version, true );
                wp_enqueue_script( 'datatables-html5', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-html5.js', array( 'datatables-buttons-js' ), $this->version, true );
                wp_enqueue_script( 'datatables-print', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-print.js', array( 'datatables-buttons-js' ), $this->version, true );
                wp_enqueue_script( 'datatables-select-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-jquery-datatable-select', array( 'jquery-datatable-js' ), $this->version, true );
            }

            if ( $screen->base == 'flexibooking_page_bm_booking_analytics' ) {
                wp_enqueue_script( 'bm-analytics-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-analytics.js', array( 'jquery', 'chart-js' ), '1.0', true );

                // Localize script with AJAX URL
				wp_localize_script(
                    'bm-analytics-js',
                    'bm_analytics_object',
                    array(
						'ajax_url' => admin_url( 'admin-ajax.php' ),
						'nonce'    => wp_create_nonce( 'bm_analytics_nonce' ),
                    )
				);
            }

			$bm_svc_shrt_desc_char_limit = $dbhandler->get_global_option_value( 'bm_svc_shrt_desc_char_limit', 0 );

			$error   = array();
			$success = array();
			$normal  = array();

			$error['required_field']              = __( 'This is a required field.', 'service-booking' );
			$error['required']                    = __( 'Required', 'service-booking' );
			$error['price_required']              = __( 'Price is required.', 'service-booking' );
			$error['price_module_required']       = __( 'Price module is required.', 'service-booking' );
			$error['stopsales_required']          = __( 'Stopsales is required.', 'service-booking' );
			$error['capacity_required']           = __( 'Capacity is required.', 'service-booking' );
			$error['date_required']               = __( 'Date is required.', 'service-booking' );
			$error['from_date_required']          = __( 'From date is required.', 'service-booking' );
			$error['to_date_required']            = __( 'To date is required.', 'service-booking' );
			$error['price_numeric']               = __( 'Must be numeric and > 0.', 'service-booking' );
			$error['set_price']                   = __( 'Please set a deafult price under service details section to set prices in calendar.', 'service-booking' );
			$error['set_stopsales']               = __( 'Please set a deafult stopsales under service details section to set stopsales in calendar.', 'service-booking' );
			$error['set_max_cap']                 = __( 'Please set a default max capacity under service details section to set max capacity in calendar.', 'service-booking' );
			$error['set_time_slot']               = __( 'Please set deafult time slots under service details section to set time slots in calendar.', 'service-booking' );
			$error['server_error']                = __( 'Something is Wrong.', 'service-booking' );
			$error['price_field']                 = __( 'Must be a numeric value with 2 decimal points >= zero.', 'service-booking' );
			$error['numeric_field']               = __( 'This field must have a numeric value greater than zero.', 'service-booking' );
			$error['min_cap_field']               = __( 'Minimum capacity must be lower than or equal to maximum capacity.', 'service-booking' );
			$error['min_length_field']            = __( 'Minimum length must be lower than or equal to maximum length.', 'service-booking' );
			$error['svc_duration_field']          = __( 'Service duration must be lower than or equal to total operating time.', 'service-booking' );
			$error['svc_to_date']                 = __( 'Service to date must be lower than or equal to from date.', 'service-booking' );
			$error['comma_separated_field']       = __( 'Please separate multiple values with a comma(",").', 'service-booking' );
			$error['comma_separated_emails']      = __( 'Please separate valid emails separated by a comma(",").', 'service-booking' );
			$error['bar_separated_field']         = __( 'Please separate multiple values with a bar("|").', 'service-booking' );
			$error['max_time']                    = __( 'Exceeds 24hrs.', 'service-booking' );
			$error['min_cap']                     = __( 'Exceeds default max cap.', 'service-booking' );
			$error['max_cap']                     = __( 'Choose greater than min cap.', 'service-booking' );
			$error['atleast_one_field']           = __( 'Select at least one field.', 'service-booking' );
			$error['field_label_validation']      = __( 'Words separated by spaces only (no spcl chars).', 'service-booking' );
			$error['timezone_error']              = __( 'Could not fetch timezone for selected country.', 'service-booking' );
			$error['products_error']              = __( 'Could not fetch products data.', 'service-booking' );
			$error['customer_error']              = __( 'Could not fetch customer data.', 'service-booking' );
			$error['service_error']               = __( 'Could not fetch service data.', 'service-booking' );
			$error['no_services']                 = __( 'No services found for this category.', 'service-booking' );
			$error['no_bookable_services']        = __( 'No bookable services found.', 'service-booking' );
			$error['no_time_slots']               = __( 'No bookable Time slots found.', 'service-booking' );
			$error['no_slot_capacity']            = __( 'No Capacity available for this slot.', 'service-booking' );
			$error['no_extras']                   = __( 'No bookable extras found.', 'service-booking' );
			$error['only_primary_email_field']    = __( 'Can not uncheck, this is the only primary email field.', 'service-booking' );
			$error['invalid_email']               = __( 'Please enter a valid email.', 'service-booking' );
			$error['invalid_contact']             = __( 'Please enter a valid phone no.', 'service-booking' );
			$error['invalid_url']                 = __( 'Please enter a valid URL.', 'service-booking' );
			$error['invalid_password']            = __( 'Please enter a valid password.', 'service-booking' );
			$error['existing_field_key']          = __( 'This value is taken, choose another one.', 'service-booking' );
			$error['linked_module']               = __( 'Can not delete this module as it is linked with one or more services.', 'service-booking' );
			$error['event_type_value_error']      = __( 'Could not fetch values.', 'service-booking' );
			$error['active_template_type']        = __( 'There is already an active template for this type, please deactivate the existing template.', 'service-booking' );
			$error['active_process_type']         = __( 'There is already an active process for this type, please deactivate the existing process.', 'service-booking' );
			$error['invalid_conditions']          = __( 'Invalid conditions given, please check again.', 'service-booking' );
			$error['wrong_transaction_id']        = __( 'Please double check the transaction id entered.', 'service-booking' );
			$error['transaction_id_not_required'] = __( 'For free orders, transaction id is not applicable.', 'service-booking' );
			$error['wrong_refund_id']             = __( 'Please double check the refund id entered.', 'service-booking' );
			$error['transaction_changes_revert']  = __( 'Transaction changes reverted due to some error.', 'service-booking' );
			$error['transaction_id_exists']       = __( 'Transaction id already exists in a different transaction.', 'service-booking' );
			$error['choose_correct_file_type']    = __( 'One or more files is/are invalid. Please select a valid file (PDF/DOC/DOCX/JPEG/JPG/PNG/GIF/SVG/XLSX/ZIP).', 'service-booking' );
			$error['max_files_to_be_attached']    = __( 'Maximum files can be attached at a time is ', 'service-booking' );
			$error['file_already_exists']         = __( 'The selected file already exists. ', 'service-booking' );
			$error['file_upload_failed']          = __( 'The selected files could not be uploaded. ', 'service-booking' );
			$error['attachments_clearing_failed'] = __( 'The attachments could not be cleared. ', 'service-booking' );
			$error['duplicate_attachment']        = __( 'One or more selected attachment/s is/are duplicate/s. ', 'service-booking' );
			$error['verification_failed']         = __( 'User verification failed. ', 'service-booking' );
			$error['file_size_less_message']      = __( 'File size should not be less than ', 'service-booking' );
			$error['file_size_more_message']      = __( 'File size should not be more than ', 'service-booking' );
			$error['file_width_less_message']     = __( 'File width should not be less than ', 'service-booking' );
			$error['file_width_more_message']     = __( 'File width should not be more than ', 'service-booking' );
			$error['file_height_less_message']    = __( 'File width should not be less than ', 'service-booking' );
			$error['file_height_more_message']    = __( 'File width should not be more than ', 'service-booking' );
			$error['file_type_not_supported']     = __( 'File type is not supported. ', 'service-booking' );
			$error['file_invalid']                = __( 'One of the uploaded files is invalid, try again. ', 'service-booking' );
			$error['invalid_page_numbers']        = __( 'Invalid page numbers entered.', 'service-booking' );
			$error['must_be_greater_than']        = __( 'must be greater than ', 'service-booking' );
			$error['must_be_less_than']           = __( 'must be less than ', 'service-booking' );
			$error['must_be_less_than_field']     = __( 'must be less than next from field', 'service-booking' );
			$error['must_be_greater_than_field']  = __( 'must be greater than last \'to\' field', 'service-booking' );
			$error['transaction_not_editable']    = __( 'This transaction is not editable', 'service-booking' );
			$error['fill_up_age_fields']          = __( 'Please fill up all the required fields to calculate discount.', 'service-booking' );
			$error['invalid_total']               = __( 'Invalid value of total.', 'service-booking' );
			$error['excess_order_total']          = __( 'The total number exceeds the number of people for the ordered service.', 'service-booking' );
			$error['existing_mail']               = __( 'This email is taken, choose a different one.', 'service-booking' );
			$error['coupon_frm_slot_value_error'] = __( '"To" time is required when "From" time is filled.', 'service-booking' );
			$error['coupon_to_slot_value_error']  = __( '"From" time is required when "To" time is filled.', 'service-booking' );
			$error['max_cpn_amt']                 = __( '*Selected amount should be greater than min', 'service-booking' );
			$error['coupon_time_less_error']      = __( 'To time is invalid. Please select greater value', 'service-booking' );
			$error['code_error']                  = __( 'Coupon code must be at least 4 characters.', 'service-booking' );
			$error['restore_failed']              = __( 'Failed to restore order. Please try again.', 'service-booking' );
			$error['failed_export']               = __( 'Failed to export data.', 'service-booking' );
			$error['no_services_text']            = __( 'No services found', 'service-booking' );
			$error['no_qr_code_found']            = __( 'No QR code found in cropped area.', 'service-booking' );
			$error['svc_short_desc_limit']        = sprintf( __( 'Short description cannot exceed %d characters.', 'service-booking' ), $bm_svc_shrt_desc_char_limit );

			$success['price_set']                    = __( 'Price set successfully.', 'service-booking' );
			$success['module_set']                   = __( 'Price module set successfully.', 'service-booking' );
			$success['stopsales_set']                = __( 'Stopsales set successfully.', 'service-booking' );
			$success['saleswitch_set']               = __( 'Saleswitch set successfully.', 'service-booking' );
			$success['capacity_set']                 = __( 'Capacity set successfully.', 'service-booking' );
			$success['time_slot_set']                = __( 'Time slot set successfully.', 'service-booking' );
			$success['save_success']                 = __( 'Saved successfully.', 'service-booking' );
			$success['slot_remove_success']          = __( 'Slot Removed successfully.', 'service-booking' );
			$success['field_remove_success']         = __( 'Field Removed successfully.', 'service-booking' );
			$success['remove_success']               = __( 'Removed successfully.', 'service-booking' );
			$success['order_cancel_success']         = __( 'Order Cancelled successfully.', 'service-booking' );
			$success['order_approve_success']        = __( 'Order Approved successfully.', 'service-booking' );
			$success['mail_send_success']            = __( 'Mail sent successfully.', 'service-booking' );
			$success['transaction_updated']          = __( 'Transaction data updated successfully.', 'service-booking' );
			$success['attachments_clearing_success'] = __( 'The attachments are cleared successfully.', 'service-booking' );
			$success['status_successfully_changed']  = __( 'The status has been changed successfully.', 'service-booking' );
			$success['text_copied']                  = __( 'Text copied successfully.', 'service-booking' );
			$success['checked_in_successfully']      = __( 'Checked in successfully.', 'service-booking' );

            $normal['choose_field']            = __( 'Select a field first', 'service-booking' );
            $normal['are_you_sure']            = __( 'Are You Sure ?', 'service-booking' );
            $normal['sure_remove_condition']   = __( 'Are you sure you want to remove this condition ?', 'service-booking' );
            $normal['sure_remove_attchmnt']    = __( 'Are you sure you want to remove this attachment ?', 'service-booking' );
            $normal['sure_save_transaction']   = __( 'Are you sure you want to save this order transaction ?', 'service-booking' );
            $normal['change_pro_visibility']   = __( "Are you sure you want to change this process's visibilty ?", 'service-booking' );
            $normal['change_svc_visibility']   = __( "Are you sure you want to change this service's visibilty ?", 'service-booking' );
            $normal['change_cat_visibility']   = __( "Are you sure you want to change this category's visibilty ?", 'service-booking' );
            $normal['change_cust_visibility']  = __( "Are you sure you want to change this customer's visibilty ?", 'service-booking' );
            $normal['change_tmpl_visibility']  = __( "Are you sure you want to change this template's visibilty ?", 'service-booking' );
            $normal['change_voucher_vsiblity'] = __( "Are you sure you want to change this voucher's visibilty ?", 'service-booking' );
            $normal['cancel_bor_order']        = __( 'Are you sure you want to cancel this order ? The process can not be reverted once done.', 'service-booking' );
            $normal['approve_bor_order']       = __( 'Are you sure you want to approve this order ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_service']     = __( 'Are you sure you want to remove this service ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_category']    = __( 'Are you sure you want to remove this category ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_template']    = __( 'Are you sure you want to remove this template ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_process']     = __( 'Are you sure you want to remove this notification ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_prce_module'] = __( 'Are you sure you want to remove this price module ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_order']       = __( 'Are you sure you want to remove this order ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_timeslot']    = __( 'Are you sure you want to remove this timeslot ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_field']       = __( 'Are you sure you want to remove this field ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_option']      = __( 'Are you sure you want to remove this option ? The process can not be reverted once done.', 'service-booking' );
            $normal['remove_svc_unavl_date']   = __( 'Are you sure you want to remove this service unavailable date ? The process can not be reverted once done.', 'service-booking' );
            $normal['remove_extra_product']    = __( 'Are you sure you want to remove this extra service ? The process can not be reverted once done.', 'service-booking' );
            $normal['price_change']            = __( 'This will also reset the calendar prices, Are You Sure ?', 'service-booking' );
            $normal['stopsales_change']        = __( 'This will also reset the calendar stopsales, Are You Sure ?', 'service-booking' );
            $normal['saleswitch_change']       = __( 'This will also reset the calendar saleswitch, Are You Sure ?', 'service-booking' );
            $normal['max_cap_change']          = __( 'This will also reset the calendar capacities, Are You Sure ?', 'service-booking' );
            $normal['timeslot_change']         = __( 'This will also reset the calendar timeslots, Are You Sure ?', 'service-booking' );
            $normal['atleast_one_checked']     = __( 'At least 1 column should be checked.', 'service-booking' );
            $normal['choose_one']              = __( 'Please choose one option.', 'service-booking' );
            $normal['save_it']                 = __( 'Save the changes ?', 'service-booking' );
            $normal['sure_complete_order']     = __( 'Are You Sure you want to change status ? This can not be reverted.', 'service-booking' );
            $normal['sure_change_status']      = __( 'Are You Sure you want to change status ?', 'service-booking' );
            $normal['no_services']             = __( 'No Services Found', 'service-booking' );
            $normal['no_categories']           = __( 'No Categories Found', 'service-booking' );
            $normal['no_price_modules']        = __( 'No Modules Found', 'service-booking' );
            $normal['no_records']              = __( 'No Records Found', 'service-booking' );
            $normal['module_per_age_info']     = __( 'Define prices for different age groups. If this module is linked with that service, these prices will be considered for that service on top of its default price/day specific price. you can diable a group if you don\'t want the price for a specific age group to be considered', 'service-booking' );
            $normal['module_per_group_info']   = __( 'Define prices for different groups. These prices are only for adult and senior age groups and will be considered only if the booked service has persons belonging to these age groups', 'service-booking' );
            $normal['delete']                  = __( 'Delete', 'service-booking' );
            $normal['success']                 = __( 'Success', 'service-booking' );
            $normal['failure']                 = __( 'Failed', 'service-booking' );
            $normal['edit']                    = __( 'Edit', 'service-booking' );
            $normal['type']                    = __( 'Type: ', 'service-booking' );
            $normal['remove']                  = __( 'Remove', 'service-booking' );
            $normal['archive']                 = __( 'Archive', 'service-booking' );
            $normal['restore']                 = __( 'Restore', 'service-booking' );
            $normal['disable']                 = __( 'Disable ?', 'service-booking' );
            $normal['options_selected']        = __( ' options selected', 'service-booking' );
            $normal['selected']                = __( ' Selected', 'service-booking' );
            $normal['choose_option']           = __( 'Select an option', 'service-booking' );
            $normal['filter_service']          = __( 'Service', 'service-booking' );
            $normal['filter_category']         = __( 'Category', 'service-booking' );
            $normal['filter_customer']         = __( 'Customer', 'service-booking' );
            $normal['filter_email']            = __( 'Email', 'service-booking' );
            $normal['choose_order_status']     = __( 'Order statuses', 'service-booking' );
            $normal['choose_payment_status']   = __( 'Payment statuses', 'service-booking' );
            $normal['search_here']             = __( 'Search here', 'service-booking' );
            $normal['approve']                 = __( 'Approve', 'service-booking' );
            $normal['save']                    = __( 'Save', 'service-booking' );
            $normal['cancel']                  = __( 'Cancel', 'service-booking' );
            $normal['backend']                 = __( 'Backend', 'service-booking' );
            $normal['frontend']                = __( 'Frontend', 'service-booking' );
            $normal['previous']                = __( 'Previous', 'service-booking' );
            $normal['next']                    = __( 'Next', 'service-booking' );
            $normal['services_text']           = __( 'Services', 'service-booking' );
            $normal['currency_position']       = $dbhandler->get_global_option_value( 'bm_currency_position', 'before' );
            $normal['currency_symbol']         = $bmrequests->bm_get_currency_char( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
            $normal['currency_type']           = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
            $normal['booking_country']         = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
            $normal['page_slug']               = isset( $post_id ) && isset( $post ) ? $post->post_name : basename( get_permalink() );
            $normal['dashboard_global_search'] = $dbhandler->get_global_option_value( 'bm_backend_dashboard_global_search_field' );
            $normal['current_screen']          = isset( $screen->base ) ? $screen->base : '';
            $normal['insert_value']            = __( 'insert value', 'service-booking' );
            $normal['insert_key']              = __( 'insert key', 'service-booking' );
            $normal['age_price_settings']      = __( 'Age Wise Price Settings', 'service-booking' );
            $normal['cross_sign']              = '✕';
            $normal['age_groups']              = $age_groups;
            $normal['service']                 = __( 'Service', 'service-booking' );
            $normal['category']                = __( 'Category', 'service-booking' );
            $normal['order_status']            = __( 'Order status', 'service-booking' );
            $normal['payment_status']          = __( 'Payment status', 'service-booking' );
            $normal['equal_to']                = __( 'Equal to ', 'service-booking' );
            $normal['not_equal_to']            = __( 'Not equal to', 'service-booking' );
            $normal['at_least_one_condition']  = __( 'You must have at least one condition if you have checked conditions checkbox.', 'service-booking' );
            $normal['edit_transaction']        = __( 'Edit transaction', 'service-booking' );
            $normal['loading_image']           = esc_url( $plugin_path . 'partials/images/ajax-loader.gif' );
            $normal['attachment_image']        = esc_url( $plugin_path . 'partials/images/attach.png' );
            $normal['customer_data']           = __( 'Customer data', 'service-booking' );
            $normal['copy_to_clipboard']       = __( 'Copy to clipboard', 'service-booking' );
            $normal['copied_to_clipboard']     = __( 'Copied to clipboard', 'service-booking' );
            $normal['enter_admin_password']    = __( 'Enter admin password', 'service-booking' );
            $normal['username_email']          = __( 'Enter username/email', 'service-booking' );
            $normal['password']                = __( 'Password', 'service-booking' );
            $normal['enter_admin_credentials'] = __( 'Enter admin credentials', 'service-booking' );
            $normal['first_name']              = __( 'First Name', 'service-booking' );
            $normal['last_name']               = __( 'Last Name', 'service-booking' );
            $normal['email']                   = __( 'Email', 'service-booking' );
            $normal['phone']                   = __( 'Phone', 'service-booking' );
            $normal['city']                    = __( 'City', 'service-booking' );
            $normal['state']                   = __( 'State', 'service-booking' );
            $normal['country']                 = __( 'Country', 'service-booking' );
            $normal['automcomplete']           = __( 'Automcomplete', 'service-booking' );
            $normal['field_label']             = __( 'Field Label', 'service-booking' );
            $normal['field_name_attribute']    = __( 'Field Name Attribute', 'service-booking' );
            $normal['field_description']       = __( 'Field Description', 'service-booking' );
            $normal['placeholder']             = __( 'Placeholder', 'service-booking' );
            $normal['custom_class']            = __( 'Custom Class', 'service-booking' );
            $normal['field_width']             = __( 'Field Width', 'service-booking' );
            $normal['set_as_primary_email']    = __( 'Set as primary email', 'service-booking' );
            $normal['multiple']                = __( 'Multiple', 'service-booking' );
            $normal['editable']                = __( 'Editable', 'service-booking' );
            $normal['visible']                 = __( 'Visible', 'service-booking' );
            $normal['default_options']         = __( 'Default Options', 'service-booking' );
            $normal['add_option']              = __( 'Add Option', 'service-booking' );
            $normal['name']                    = __( 'name', 'service-booking' );
            $normal['from']                    = __( 'from', 'service-booking' );
            $normal['to']                      = __( 'to', 'service-booking' );
            $normal['price']                   = __( 'price', 'service-booking' );
            $normal['select_persons']          = __( 'select no of persons', 'service-booking' );
            $normal['total_price']             = __( 'Total Price', 'service-booking' );
            $normal['in']                      = __( 'in', 'service-booking' );
            $normal['select_service']          = __( 'Select Service', 'service-booking' );
            $normal['select_slot']             = __( 'select slot', 'service-booking' );
            $normal['admin_username']          = __( 'admin username', 'service-booking' );
            $normal['admin_password']          = __( 'admin password', 'service-booking' );
            $normal['minimum_capacity']        = __( 'minimum capacity', 'service-booking' );
            $normal['maximum_capacity']        = __( 'maximum capacity', 'service-booking' );
            $normal['minimum_length']          = __( 'Minimum Length', 'service-booking' );
            $normal['maximum_length']          = __( 'Maximum Length', 'service-booking' );
            $normal['rows']                    = __( 'Rows', 'service-booking' );
            $normal['columns']                 = __( 'Columns', 'service-booking' );
            $normal['field_key']               = __( 'Field Key', 'service-booking' );
            $normal['show_intl_codes']         = __( 'Show International codes', 'service-booking' );
            $normal['link_woo_field']          = __( 'Link with WooCommerce Field', 'service-booking' );
            $normal['default_value']           = __( 'Default Value', 'service-booking' );
            $normal['field']                   = __( 'Field', 'service-booking' );
            $normal['settings']                = __( 'Settings', 'service-booking' );
            $normal['add']                     = __( 'Add', 'service-booking' );
            $normal['quantity']                = __( 'Quantity', 'service-booking' );
            $normal['cap_left']                = __( 'Cap Left', 'service-booking' );
            $normal['username']                = __( 'Username', 'service-booking' );
            $normal['selected']                = __( 'Selected', 'service-booking' );
            $normal['billing_first_name']      = __( 'Billing First Name', 'service-booking' );
            $normal['billing_last_name']       = __( 'Billing Last Name', 'service-booking' );
            $normal['billing_company']         = __( 'Billing Company', 'service-booking' );
            $normal['billing_country']         = __( 'Billing Country', 'service-booking' );
            $normal['billing_address']         = __( 'Billing Address', 'service-booking' );
            $normal['billing_address_1']       = __( 'Billing Address 1', 'service-booking' );
            $normal['billing_address_2']       = __( 'Billing Address 2', 'service-booking' );
            $normal['billing_city']            = __( 'Billing City', 'service-booking' );
            $normal['billing_state']           = __( 'Billing State', 'service-booking' );
            $normal['billing_postcode']        = __( 'Billing Postcode', 'service-booking' );
            $normal['billing_phone']           = __( 'Billing Phone', 'service-booking' );
            $normal['billing_email']           = __( 'Billing Email', 'service-booking' );
            $normal['shipping_first_name']     = __( 'Shipping First Name', 'service-booking' );
            $normal['shipping_last_name']      = __( 'Shipping Last Name', 'service-booking' );
            $normal['shipping_company']        = __( 'Shipping Company', 'service-booking' );
            $normal['shipping_address']        = __( 'Shipping Address', 'service-booking' );
            $normal['shipping_address_1']      = __( 'Shipping Address 1', 'service-booking' );
            $normal['shipping_address_2']      = __( 'Shipping Address 2', 'service-booking' );
            $normal['shipping_city']           = __( 'Shipping City', 'service-booking' );
            $normal['shipping_state']          = __( 'Shipping State', 'service-booking' );
            $normal['shipping_postcode']       = __( 'Shipping Postcode', 'service-booking' );
            $normal['order_comments']          = __( 'Order Comments', 'service-booking' );
            $normal['non_woocomerce']          = __( 'Non WooCommerce Field', 'service-booking' );
            $normal['order_details']           = __( 'Order Details', 'service-booking' );
            $normal['customer_details']        = __( 'Customer Details', 'service-booking' );
            $normal['order_details_pdf']       = __( 'Order details pdf', 'service-booking' );
            $normal['order_ticket_pdf']        = __( 'Order ticket pdf', 'service-booking' );
            $normal['customer_details_pdf']    = __( 'Customer details pdf', 'service-booking' );
            $normal['no_attachments']          = __( 'No attachments found', 'service-booking' );
            $normal['no_price_module_date']    = __( 'No discountable price modules found', 'service-booking' );
            $normal['pay']                     = __( 'Pay  ', 'service-booking' );
            $normal['free_book']               = __( 'Free Booking', 'service-booking' );
            $normal['service_discount_text']   = __( 'Service discount is ', 'service-booking' );
            $normal['bookings']                = __( 'Bookings', 'service-booking' );
            $normal['no_data_to_show']         = __( 'No data to show', 'service-booking' );
            $normal['sure_remove_coupon']      = __( 'Are you sure you want to remove this Coupon ? The process can not be reverted once done.', 'service-booking' );
            $normal['sure_remove_restriction'] = __( 'Are you sure you want to remove this restriction ?', 'service-booking' );
            $normal['remove_cpn_unavl_date']   = __( 'Are you sure you want to remove this Date ?', 'service-booking' );
            $normal['remove_cpn_event_date']   = __( 'Are you sure you want to remove this event date?', 'service-booking' );
            $normal['enter_reference_key']     = __( 'Please enter the booking reference key', 'service-booking' );
            $normal['resend_ticket_mail']      = __( 'Resend ticket mail', 'service-booking' );
            $normal['sure_archive_order']      = __( 'Are you sure you want to archive this order? It can be restored later.', 'service-booking' );
            $normal['sure_restore_order']      = __( 'Are you sure you want to restore this archived order?', 'service-booking' );
            $normal['order_restored']          = __( 'Order has been successfully restored.', 'service-booking' );
            $normal['order_archived']          = __( 'Order has been successfully archived.', 'service-booking' );
            $normal['reservation_list']        = __( 'Reservation List', 'service-booking' );
            $normal['booking']                 = __( 'Booking', 'service-booking' );
            $normal['show_more']               = __( 'Show More', 'service-booking' );
            $normal['more_info']               = __( 'More Info', 'service-booking' );
            $normal['total']                   = __( 'Total', 'service-booking' );
            $normal['payment_method']          = __( 'Payment Method', 'service-booking' );
            $normal['transaction_id']          = __( 'Transaction ID', 'service-booking' );
            $normal['amount']                  = __( 'Amount', 'service-booking' );
            $normal['payment_status']          = __( 'Payment Status', 'service-booking' );
            $normal['payment_date']            = __( 'Payment Date', 'service-booking' );
            $normal['additional_information']  = __( 'Additional Information', 'service-booking' );
            $normal['billing_information']     = __( 'Billing Information', 'service-booking' );
            $normal['subject']                 = __( 'Subject', 'service-booking' );
            $normal['date_sent']               = __( 'Date Sent', 'service-booking' );
            $normal['recipient']               = __( 'Recipient', 'service-booking' );
            $normal['view_mail']               = __( 'View Mail', 'service-booking' );
            $normal['product']                 = __( 'Product', 'service-booking' );
            $normal['total_quantity']          = __( 'Total Quantity', 'service-booking' );
            $normal['revenue']                 = __( 'Revenue', 'service-booking' );
            $normal['loading']                 = __( 'Loading', 'service-booking' );
            $normal['NotAllowedError']         = __( 'Camera access denied. Please allow camera access in your browser settings.', 'service-booking' );
            $normal['NotFoundError']           = __( 'No camera found. Please check if your device has a camera.', 'service-booking' );
            $normal['NotReadableError']        = __( 'Camera is already in use by another application.', 'service-booking' );
            $normal['OverconstrainedError']    = __( 'Camera does not support the required constraints.', 'service-booking' );
            $normal['SecurityError']           = __( 'Camera access is blocked by browser security settings.', 'service-booking' );
            $normal['enter_email']             = __( 'Please enter email', 'service-booking' );
            $normal['enter_last_name']         = __( 'Please enter last name', 'service-booking' );
            $normal['enter_reference_no']      = __( 'Please enter booking reference number', 'service-booking' );
            $normal['select_a_service']        = __( 'Select a service', 'service-booking' );
            $normal['qr_code_detected']        = __( 'QR Code Detected', 'service-booking' );
            $normal['select_all']              = __( 'Select All.', 'service-booking' );
            $normal['no_mails_sent']           = __( 'No emails have been sent for this order.', 'service-booking' );
            $normal['resend_regenerate_mail']  = __( 'Resend Email & Regenerate Ticket.', 'service-booking' );
            $normal['failed_mail_load']        = __( 'Failed to load email information.', 'service-booking' );
            $normal['error_mail_load']         = __( 'Error loading email information.', 'service-booking' );
            $normal['email_sent_success']      = __( 'Email sent successfully!', 'service-booking' );

            $normal['image_min_size']     = $dbhandler->get_global_option_value( 'bm_minimum_image_size', 0 );
            $normal['image_max_size']     = $dbhandler->get_global_option_value( 'bm_maximum_image_size', 0 );
            $normal['image_min_width']    = $dbhandler->get_global_option_value( 'bm_minimum_image_width', 0 );
            $normal['image_max_width']    = $dbhandler->get_global_option_value( 'bm_maximum_image_width', 0 );
            $normal['image_min_height']   = $dbhandler->get_global_option_value( 'bm_minimum_image_height', 0 );
            $normal['image_max_height']   = $dbhandler->get_global_option_value( 'bm_maximum_image_height', 0 );
            $normal['image_quality']      = $dbhandler->get_global_option_value( 'bm_image_quality', 0 );
            $normal['price_format']       = $dbhandler->get_global_option_value( 'bm_flexi_service_price_format', 'de_DE' );
            $normal['choose_image']       = __( 'Choose Image', 'service-booking' );
            $normal['current_language']   = isset( $_COOKIE['bm_flexibooking_language'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['bm_flexibooking_language'] ) ) : esc_html( 'en' );
            $normal['svc_shrt_dsc_lmt']   = $bm_svc_shrt_desc_char_limit;
            $normal['svc_info_svg_icon']  = esc_url( plugin_dir_url( dirname( __FILE__ ) ) . 'admin/img/si_info-line.svg' );
            $primary_color                = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
            $contrast                     = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
            $normal['svc_button_colour']  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
            $normal['svc_btn_txt_colour'] = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );
            $normal['svc_info_svg_icon']  = esc_url( plugin_dir_url( dirname( __FILE__ ) ) . 'public/img/si_info-line.svg' );
            $normal['admin_side_link']    = admin_url( 'admin.php?' );

			wp_localize_script( $this->plugin_name, 'bm_error_object', $error );
			wp_localize_script( $this->plugin_name, 'bm_success_object', $success );
			wp_localize_script( $this->plugin_name, 'bm_normal_object', $normal );

			if ( $screen->base == 'admin_page_bm_add_external_service_price' ) {
				wp_enqueue_script( 'service-price-module-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-price-module.js', array( 'jquery' ), $this->version, false );
				wp_localize_script( 'service-price-module-js', 'bm_error_object', $error );
				wp_localize_script( 'service-price-module-js', 'bm_normal_object', $normal );
			}

			if ( $screen->base == 'admin_page_bm_add_order' ) {
				wp_enqueue_script( 'backennd-order-script', plugin_dir_url( __FILE__ ) . 'js/booking-management-add-order.js', array( 'jquery' ), $this->version, true );
				wp_localize_script( 'backennd-order-script', 'bm_error_object', $error );
				wp_localize_script( 'backennd-order-script', 'bm_normal_object', $normal );
			}

			if ( $screen->base == 'flexibooking_page_bm_check_ins' ) {
				wp_enqueue_script( 'admin-jsqr', plugin_dir_url( __FILE__ ) . 'js/booking-management-jsqr.js', array( 'jquery' ), $this->version, true );
				wp_enqueue_script( 'check-in-script', plugin_dir_url( __FILE__ ) . 'js/booking-management-check-ins.js', array( 'jquery' ), $this->version, true );
				// WPML compatibility for QR scanner page URL
				$scanner_page_url = get_permalink( get_option( 'bm_qr_scanner_page_id' ) );

				global $sitepress;
				if ( $sitepress ) {
					$default_lang = $sitepress->get_default_language();
					$current_lang = $sitepress->get_current_language();
					// $sitepress->switch_lang( $default_lang, true );
					$sitepress->switch_lang( $current_lang, true );
					$original_page = get_option( 'bm_qr_scanner_page_id' );
					$translated_id = apply_filters( 'wpml_object_id', $original_page, 'page', true, $current_lang );
					if ( $translated_id ) {
						$scanner_page_url = get_permalink( $translated_id );
					}
				}
				wp_localize_script(
					'check-in-script',
					'qrScannerData',
					array(
						'scannerPageUrl' => get_permalink( get_option( 'bm_qr_scanner_page_id' ) ),
						'plugin_url'     => plugin_dir_url( __FILE__ ),
					)
				);
			}

			if ( $screen->base == 'admin_page_bm_add_coupon' || $screen->base == 'flexibooking_page_bm_all_coupons' ) {
				wp_enqueue_script( 'coupon-module-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-coupon.js', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( 'country-state', plugin_dir_url( __FILE__ ) . 'js/country-states.js', array( 'jquery' ), $this->version, false );
				wp_localize_script( 'coupon-module-js', 'bm_error_object', $error );
			}

			if ( $screen->base == 'admin_page_bm_single_order' ) {
				wp_enqueue_script( 'single-order-js', plugin_dir_url( __FILE__ ) . 'js/booking-management-single-order.js', array( 'jquery' ), $this->version, false );
			}

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
		} //end if
	}//end enqueue_scripts()


    public function booking_admin_menu() {
         add_menu_page( __( 'Dashboard', 'service-booking' ), __( 'FlexiBooking', 'service-booking' ), 'manage_options', 'bm_home', array( $this, 'bm_home' ), 'dashicons-groups', 26 );
        add_submenu_page( 'bm_home', __( 'Booking Dashboard', 'service-booking' ), __( 'Dashboard', 'service-booking' ), 'manage_options', 'bm_home', array( $this, 'bm_home' ) );
        add_submenu_page( 'bm_home', __( 'Analytics', 'service-booking' ), __( 'Analytics', 'service-booking' ), 'manage_options', 'bm_booking_analytics', array( $this, 'bm_booking_analytics' ) );
        add_submenu_page( 'bm_home', __( 'Orders', 'service-booking' ), __( 'Orders', 'service-booking' ), 'manage_options', 'bm_all_orders', array( $this, 'bm_all_orders' ) );
        add_submenu_page( '', __( 'Add Order', 'service-booking' ), __( 'Add Order', 'service-booking' ), 'manage_options', 'bm_add_order', array( $this, 'bm_add_order' ) );
        add_submenu_page( 'bm_home', __( 'Service Booking Planner', 'service-booking' ), __( 'Service Booking Planner', 'service-booking' ), 'manage_options', 'bm_service_booking_planner', array( $this, 'bm_service_booking_planner' ) );
        add_submenu_page( '', __( 'Single Order Page', 'service-booking' ), __( 'Single Order Page', 'service-booking' ), 'manage_options', 'bm_single_order', array( $this, 'bm_single_order' ) );
        add_submenu_page( 'bm_home', __( 'Single Service Booking Planner', 'service-booking' ), __( 'Single Service Booking Planner', 'service-booking' ), 'manage_options', 'bm_single_service_booking_planner', array( $this, 'bm_single_service_booking_planner' ) );
        add_submenu_page( 'bm_home', __( 'Customers', 'service-booking' ), __( 'Customers', 'service-booking' ), 'manage_options', 'bm_all_customers', array( $this, 'bm_all_customers' ) );
        add_submenu_page( '', __( 'Add Customer', 'service-booking' ), __( 'Add Customer', 'service-booking' ), 'manage_options', 'bm_add_customer', array( $this, 'bm_add_customer' ) );
        add_submenu_page( '', __( 'Customer Profile', 'service-booking' ), __( 'Customer Profile', 'service-booking' ), 'manage_options', 'bm_customer_profile', array( $this, 'bm_customer_profile' ) );
        add_submenu_page( 'bm_home', __( 'Services', 'service-booking' ), __( 'Services', 'service-booking' ), 'manage_options', 'bm_all_services', array( $this, 'bm_all_services' ) );
        add_submenu_page( '', __( 'Add Service', 'service-booking' ), __( 'Add Service', 'service-booking' ), 'manage_options', 'bm_add_service', array( $this, 'bm_add_service' ) );
        add_submenu_page( 'bm_home', __( 'Categories', 'service-booking' ), __( 'Categories', 'service-booking' ), 'manage_options', 'bm_all_categories', array( $this, 'bm_all_categories' ) );
        add_submenu_page( '', __( 'Add Category', 'service-booking' ), __( 'Add Category', 'service-booking' ), 'manage_options', 'bm_add_category', array( $this, 'bm_add_category' ) );
        add_submenu_page( 'bm_home', __( 'Mail Templates', 'service-booking' ), __( 'Mail Templates', 'service-booking' ), 'manage_options', 'bm_email_templates', array( $this, 'bm_email_templates' ) );
        add_submenu_page( '', __( 'Add Template', 'service-booking' ), __( 'Add Template', 'service-booking' ), 'manage_options', 'bm_add_template', array( $this, 'bm_add_template' ) );
        add_submenu_page( 'bm_home', __( 'Fields', 'service-booking' ), __( 'Fields', 'service-booking' ), 'manage_options', 'bm_fields', array( $this, 'bm_fields' ) );
        add_submenu_page( 'bm_home', __( 'Price Modules', 'service-booking' ), __( 'Price Modules', 'service-booking' ), 'manage_options', 'bm_all_external_service_prices', array( $this, 'bm_all_external_service_prices' ) );
        add_submenu_page( '', __( 'Add Price Module', 'service-booking' ), __( 'Add Price Module', 'service-booking' ), 'manage_options', 'bm_add_external_service_price', array( $this, 'bm_add_external_service_price' ) );
        add_submenu_page( 'bm_home', __( 'Notification Processes', 'service-booking' ), __( 'Notification Processes', 'service-booking' ), 'manage_options', 'bm_all_notification_processes', array( $this, 'bm_all_notification_processes' ) );
        add_submenu_page( '', __( 'Add Process', 'service-booking' ), __( 'Add Process', 'service-booking' ), 'manage_options', 'bm_add_notification_process', array( $this, 'bm_add_notification_process' ) );
        add_submenu_page( 'bm_home', __( 'Email Records', 'service-booking' ), __( 'Email Records', 'service-booking' ), 'manage_options', 'bm_email_records', array( $this, 'bm_email_records' ) );
        add_submenu_page( 'bm_home', __( 'Vouchers', 'service-booking' ), __( 'Vouchers', 'service-booking' ), 'manage_options', 'bm_voucher_records', array( $this, 'bm_voucher_records' ) );
        add_submenu_page( 'bm_home', __( 'Check ins', 'service-booking' ), __( 'Check ins', 'service-booking' ), 'manage_options', 'bm_check_ins', array( $this, 'bm_check_ins' ) );
        add_submenu_page( 'bm_home', __( 'PDF Customization', 'service-booking' ), __( 'PDF Customization', 'service-booking' ), 'manage_options', 'bm_pdf_customization', array( $this, 'bm_pdf_customization' ) );
		add_submenu_page( 'bm_home', __( 'Email Logs', 'service-booking' ), __( 'Email Logs', 'service-booking' ), 'manage_options', 'bm_email_logs', array( $this, 'bm_email_logs' ) );
		add_submenu_page( 'bm_home', __( 'Payment Logs', 'service-booking' ), __( 'Payment Logs', 'service-booking' ), 'manage_options', 'bm_payment_logs', array( $this, 'bm_payment_logs' ) );
        add_submenu_page( 'bm_home', __( 'Coupons', 'service-booking' ), __( 'Coupons', 'service-booking' ), 'manage_options', 'bm_all_coupons', array( $this, 'bm_all_coupons' ) );
        add_submenu_page( '', __( 'Add Coupon', 'service-booking' ), __( 'Add Coupon', 'service-booking' ), 'manage_options', 'bm_add_coupon', array( $this, 'bm_add_coupon' ) );
        add_submenu_page( 'bm_home', __( 'Global Settings', 'service-booking' ), __( 'Global Settings', 'service-booking' ), 'manage_options', 'bm_global', array( $this, 'bm_global' ) );
        add_submenu_page( '', __( 'Global General Settings', 'service-booking' ), __( 'Global General Settings', 'service-booking' ), 'manage_options', 'bm_global_general_settings', array( $this, 'bm_global_general_settings' ) );
        add_submenu_page( '', __( 'Global Email Settings', 'service-booking' ), __( 'Global Email Settings', 'service-booking' ), 'manage_options', 'bm_global_email_settings', array( $this, 'bm_global_email_settings' ) );
        add_submenu_page( '', __( 'Global Payment Settings', 'service-booking' ), __( 'Global Payment Settings', 'service-booking' ), 'manage_options', 'bm_global_payment_settings', array( $this, 'bm_global_payment_settings' ) );
        add_submenu_page( '', __( 'Service and Booking Settings', 'service-booking' ), __( 'Service and Booking Settings', 'service-booking' ), 'manage_options', 'bm_svc_booking_settings', array( $this, 'bm_svc_booking_settings' ) );
        add_submenu_page( '', __( 'CSS Settings', 'service-booking' ), __( 'CSS Settings', 'service-booking' ), 'manage_options', 'bm_global_css_settings', array( $this, 'bm_global_css_settings' ) );
        add_submenu_page( '', __( 'Timezone And Country Settings', 'service-booking' ), __( 'Timezone And Country Settings', 'service-booking' ), 'manage_options', 'bm_global_timezone_country_settings', array( $this, 'bm_global_timezone_country_settings' ) );
        add_submenu_page( '', __( 'Pagination Settings', 'service-booking' ), __( 'Pagination Settings', 'service-booking' ), 'manage_options', 'bm_pagination_settings', array( $this, 'bm_pagination_settings' ) );
        add_submenu_page( '', __( 'Upload Settings', 'service-booking' ), __( 'Upload Settings', 'service-booking' ), 'manage_options', 'bm_upload_settings', array( $this, 'bm_upload_settings' ) );
        add_submenu_page( '', __( 'Language Settings', 'service-booking' ), __( 'Language Settings', 'service-booking' ), 'manage_options', 'bm_global_language_settings', array( $this, 'bm_global_language_settings' ) );
        add_submenu_page( '', __( 'Format Settings', 'service-booking' ), __( 'Format Settings', 'service-booking' ), 'manage_options', 'bm_global_format_settings', array( $this, 'bm_global_format_settings' ) );
        add_submenu_page( '', __( 'Integration Settings', 'service-booking' ), __( 'Integration Settings', 'service-booking' ), 'manage_options', 'bm_global_integration_settings', array( $this, 'bm_global_integration_settings' ) );
        add_submenu_page( '', __( 'Coupon Settings', 'service-booking' ), __( 'Coupon Settings', 'service-booking' ), 'manage_options', 'bm_global_coupon_settings', array( $this, 'bm_global_coupon_settings' ) );
    } //end booking_admin_menu()


	public function bm_home() {
		include 'partials/booking-management-dashboard.php';
	}//end bm_home()

    // Display analytics page
    public function bm_booking_analytics() {
        include 'partials/booking-management-analytics.php';
    }


	public function bm_all_orders() {
		include 'partials/booking-management-order-listing.php';
	}//end bm_all_orders()



	public function bm_all_customers() {
		include 'partials/booking-management-customer-listing.php';
	}//end bm_all_customers()


	public function bm_all_services() {
		include 'partials/booking-management-service-listing.php';
	}


	public function bm_add_order() {
		include 'partials/booking-management-add-order.php';
	}//end bm_add_order()


	public function bm_single_order() {
		include 'partials/booking-management-single-order.php';
	}//end bm_single_order()


	public function bm_service_booking_planner() {
		include 'partials/booking-management-service-booking-planner-shortcode.php';
	}//end bm_service_booking_planner()


	public function bm_single_service_booking_planner() {
		include 'partials/booking-management-single-service-booking-planner-shortcode.php';
	}//end bm_single_service_booking_planner()


	public function bm_add_customer() {
		include 'partials/booking-management-add-customer.php';
	}//end bm_add_customer()


	public function bm_customer_profile() {
		include 'partials/booking-management-customer-profile.php';
	}//end bm_customer_profile()


	public function bm_add_service() {
		include 'partials/booking-management-add-service.php';
	}


	public function bm_all_external_service_prices() {
		include 'partials/booking-management-price-module-listing.php';
	}//end bm_all_external_service_prices()


	public function bm_add_external_service_price() {
		include 'partials/booking-management-add-service-price-module.php';
	}//end bm_add_external_service_price()


	public function bm_all_categories() {
		include 'partials/booking-management-category-listing.php';
	}//end bm_all_categories()


	public function bm_add_category() {
		include 'partials/booking-management-add-category.php';
	}//end bm_add_category()


	public function bm_email_records() {
		include 'partials/booking-management-email-records.php';
	}//end bm_email_records()


	public function bm_voucher_records() {
		include 'partials/booking-management-voucher-records.php';
	}//end bm_voucher_records()


	public function bm_check_ins() {
		include 'partials/booking-management-check_ins.php';
	} //end bm_check_ins()


	public function bm_email_logs() {
		include 'partials/booking-management-email-logs.php';
	} //end bm_email_logs()


	public function bm_payment_logs() {
		include 'partials/booking-management-payment-logs.php';
	} //end bm_payment_logs()


	public function bm_global() {
		include 'partials/booking-management-global-settings.php';
	}//end bm_global()


	public function bm_global_general_settings() {
		include 'partials/booking-management-global-general-settings.php';
	}//end bm_global_general_settings()


	public function bm_global_email_settings() {
		include 'partials/booking-management-global-mail-settings.php';
	}//end bm_global_email_settings()


	public function bm_global_payment_settings() {
		include 'partials/booking-management-global-payment-settings.php';
	}//end bm_global_payment_settings()


	public function bm_svc_booking_settings() {
		include 'partials/booking-management-global-svc-booking-settings.php';
	}//end bm_svc_booking_settings()


	public function bm_global_css_settings() {
		include 'partials/booking-management-global-css-settings.php';
	}//end bm_global_css_settings()


	public function bm_global_timezone_country_settings() {
		include 'partials/booking-management-global-timezone-country-settings.php';
	}//end bm_global_timezone_country_settings()


	public function bm_pagination_settings() {
		include 'partials/booking-management-global-pagination-settings.php';
	}//end bm_pagination_settings()


	public function bm_upload_settings() {
		include 'partials/booking-management-global-upload-settings.php';
	} //end bm_uploa    } //end bm_global_coupon_settings()


	public function bm_global_language_settings() {
		include 'partials/booking-management-global-language-settings.php';
	}//end bm_global_language_settings()


	public function bm_global_format_settings() {
		include 'partials/booking-management-global-format-settings.php';
	}//end bm_global_format_settings()


	public function bm_global_integration_settings() {
		include 'partials/booking-management-global-integration-settings.php';
	}//end bm_global_integration_settings()

	public function bm_global_coupon_settings() {
		include 'partials/booking-management-global-coupon-settings.php';
	}//end bm_global_coupon_settings()


	public function bm_fields() {
		include 'partials/booking-management-field-listing.php';
	}//end bm_fields()

	public function bm_email_templates() {
		include 'partials/booking-management-email-template-listing.php';
	}//end bm_email_templates()


	public function bm_add_template() {
		include 'partials/booking-management-add-email-template.php';
	}//end bm_add_template()

	public function bm_all_notification_processes() {
		include 'partials/booking-management-notification-processes.php';
	}//end bm_all_notification_processes()


	public function bm_add_notification_process() {
		include 'partials/booking-management-add-notification-process.php';
	}//end bm_add_notification_process()

	public function bm_all_coupons() {
		include 'partials/booking-management-coupon-listing.php';
	} //end bm_all_coupons

	public function bm_add_coupon() {
		include 'partials/booking-management-add-coupon.php';
	} //end bm_add_coupon

    public function bm_pdf_customization() {
        include 'partials/booking-management-pdf-customization.php';
	} //end bm_pdf_customization();


	/**
	 * Multilingual email content
	 *
	 * @author Darpan
	 */
	public function bm_multilingual_email() {
		do_action( 'wpml_multilingual_options', 'bm_new_order_admin_email_subject' );
		do_action( 'wpml_multilingual_options', 'bm_new_order_admin_email_body' );
	}//end bm_multilingual_email()


	/**
	 * Set Timezone
	 *
	 * @author Darpan
	 */
	public function bm_set_timezone() {
		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$wp_timezone = get_option( 'timezone_string' );

		if ( empty( $wp_timezone ) ) {
			$gmt_offset  = get_option( 'gmt_offset' );
			$wp_timezone = timezone_name_from_abbr( '', $gmt_offset * 3600, 0 );
		}

		$country_code = $bmrequests->bm_fetch_country_code_by_timezone( $wp_timezone );

		if ( ! empty( $country_code ) && ! empty( $bmrequests->bm_get_countries( $country_code ) ) ) {
			$dbhandler->update_global_option_value( 'bm_booking_country', $country_code );
		}

		$dbhandler->update_global_option_value( 'bm_booking_time_zone', $wp_timezone );
	}//end bm_set_timezone()


	/**
	 * Set Timezone
	 *
	 * @author Darpan
	 */
	public function bm_update_plugin_timezone_on_wp_change( $old_value, $new_value ) {
		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$country_code = $bmrequests->bm_fetch_country_code_by_timezone( $new_value );

		if ( ! empty( $country_code ) && ! empty( $bmrequests->bm_get_countries( $country_code ) ) ) {
			$dbhandler->update_global_option_value( 'bm_booking_country', $country_code );
		}

		$dbhandler->update_global_option_value( 'bm_booking_time_zone', $new_value );
	}//end bm_update_plugin_timezone_on_wp_change()


	/**
	 * Set Timezone
	 *
	 * @author Darpan
	 */
	public function bm_update_plugin_timezone_on_gmt_offset_change( $old_value, $new_value ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		if ( empty( get_option( 'timezone_string' ) ) ) {
			$gmt_timezone = timezone_name_from_abbr( '', $new_value * 3600, 0 );

			if ( $gmt_timezone ) {
				$country_code = $bmrequests->bm_fetch_country_code_by_timezone( $gmt_timezone );

				if ( ! empty( $country_code ) && ! empty( $bmrequests->bm_get_countries( $country_code ) ) ) {
					$dbhandler->update_global_option_value( 'bm_booking_country', $country_code );
				}

				$dbhandler->update_global_option_value( 'bm_booking_time_zone', $gmt_timezone );
			}
		}
	}//end bm_update_plugin_timezone_on_gmt_offset_change()


	/**
	 * Register Shortcodes
	 *
	 * @author Darpan
	 */
	public function bm_register_shortcodes() {
		add_shortcode( 'sgbm_flexibooking_language_switcher', 'bm_flexibooking_language_switcher' );
		add_shortcode( 'sgbm_customer_profile', array( $this, 'bm_sgbm_customer_profile' ) );
		add_shortcode( 'sgbm_service_booking_planner', array( $this, 'bm_service_booking_planner_shortcode' ) );
		add_shortcode( 'sgbm_single_service_booking_planner', array( $this, 'bm_single_service_booking_planner_shortcode' ) );
	}//end bm_register_shortcodes()


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

		/**ob_start();
		$this->flexibooking_language_switcher();
		return ob_get_clean();*/
	}//end bm_flexibooking_language_switcher()


	/**
	 * Customer profile by id shortcode
	 *
	 * @author Darpan
	 */
	public function bm_sgbm_customer_profile( $att ) {
		$default_attribute = array( 'id' => '' );
		$attribute         = shortcode_atts( $default_attribute, $att, 'sgbm_customer_profile' );
		$customer_id       = intval( $attribute['id'] );
		$html              = '';

		if ( $customer_id > 0 ) {
			$customer = ( new BM_Request() )->bm_fetch_all_customer_related_information( $customer_id );

			if ( ! empty( $customer ) ) {
				ob_start();
				require_once plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-profile-shortcode.php';
				$content = ob_get_contents();
				ob_end_clean();
				return $content;
			}
		}
	}//end bm_sgbm_customer_profile()


	/**
	 * Service fullcalendar shortcode
	 *
	 * @author Darpan
	 */
	public function bm_service_booking_planner_shortcode( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'show_filters'         => 'true',
				'show_service_filter'  => 'true',
				'show_category_filter' => 'true',
				'cat_ids'              => '',
			),
			$atts,
			'sgbm_service_booking_planner'
		);

		$show_filters         = filter_var( $atts['show_filters'], FILTER_VALIDATE_BOOLEAN );
		$show_service_filter  = filter_var( $atts['show_service_filter'], FILTER_VALIDATE_BOOLEAN );
		$show_category_filter = filter_var( $atts['show_category_filter'], FILTER_VALIDATE_BOOLEAN );
		$cat_ids              = $atts['cat_ids'];

		ob_start();
		include_once 'partials/booking-management-service-booking-planner.php';
		return ob_get_clean();
	}//end bm_service_booking_planner_shortcode()


	/**
	 * Single service booking planner
	 *
	 * @author Darpan
	 */
	public function bm_single_service_booking_planner_shortcode( $atts = array() ) {
		$atts = shortcode_atts(
			array(
				'show_filters'         => 'true',
				'show_service_filter'  => 'true',
				'show_category_filter' => 'true',
				'cat_ids'              => '',
			),
			$atts,
			'sgbm_single_service_booking_planner'
		);

		$show_filters         = filter_var( $atts['show_filters'], FILTER_VALIDATE_BOOLEAN );
		$show_service_filter  = filter_var( $atts['show_service_filter'], FILTER_VALIDATE_BOOLEAN );
		$show_category_filter = filter_var( $atts['show_category_filter'], FILTER_VALIDATE_BOOLEAN );
		$cat_ids              = $atts['cat_ids'];

		ob_start();
		include_once 'partials/booking-management-single-service-booking-planner.php';
		return ob_get_clean();
	}//end bm_single_service_booking_planner_shortcode()


	/**
	 * Language Switcher Content
	 *
	 * @author Darpan
	 */
	public function flexibooking_language_switcher() {
		// switcher check if any other plugin active then don't show this
		$lang_plugin_active = get_option( 'lang_plugin', false );
		if ( $lang_plugin_active && ( $lang_plugin_active != 'none' || $lang_plugin_active != '' ) && ! is_admin() ) {
			return;
		}
		global $sitepress;
		if ( $sitepress && is_admin() ) {
			return;
		}
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

		$html = apply_filters( 'bm_flexibooking_language_switcher_html', $html, $languages, $current_language );

		return wp_kses( $html, $bmrequests->bm_fetch_expanded_allowed_tags() );
	}//end flexibooking_language_switcher()


	/**
	 * Set installed languages
	 *
	 * @author Darpan
	 */
	public function bm_set_installed_languages_old() {
		$languages = get_option( 'available_languages', array() );
		$languages = apply_filters( 'bm_flexibooking_modify_installed_languages', $languages );

		if ( ! in_array( 'it_IT', $languages ) ) {
			$languages[] = 'it_IT';
			update_option( 'available_languages', $languages );
		}

		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		$it_translation = wp_download_language_pack( 'it_IT' );

		do_action( 'bm_flexibooking_languages_installed', $languages );
	} //end bm_set_installed_languages()


	/**
	 * Set installed languages
	 *
	 * @author Darpan
	 */
	public function bm_set_installed_languages() {
		$languages = get_option( 'available_languages', array() );
		$languages = apply_filters( 'bm_flexibooking_modify_installed_languages', $languages );

		if ( ! in_array( 'it_IT', $languages ) ) {
			$languages[] = 'it_IT';
			update_option( 'available_languages', $languages );
		}

		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		$url   = wp_nonce_url( admin_url() );
		$creds = request_filesystem_credentials( $url );

		if ( ! WP_Filesystem( $creds ) ) {
			return false;
		}

		require_once ABSPATH . 'wp-admin/includes/translation-install.php';
		$it_translation = wp_download_language_pack( 'it_IT' );

		do_action( 'bm_flexibooking_languages_installed', $languages );
	}
	// end bm_set_installed_languages()


	/**
	 * Force locale
	 *
	 * @author Darpan
	 */
	public function bm_load_service_booking_locale() {
		$dbhandler          = new BM_DBhandler();
		$current_locale     = $dbhandler->get_global_option_value( 'bm_flexi_current_locale', 'en_US' );
		$lang_plugin_active = get_option( 'lang_plugin', false );

		if ( $lang_plugin_active && ( $lang_plugin_active != 'none' || $lang_plugin_active != '' ) && ! is_admin() ) {
			switch_to_locale( $current_locale );
			return;
		}
		if ( function_exists( 'switch_to_locale' ) ) {
			update_option( 'WPLANG', $current_locale == 'en_US' ? '' : $current_locale );
		}

		do_action( 'bm_flexibooking_locale_loaded', $current_locale );
	}//end bm_load_service_booking_locale()


	/**
	 * Add Language Switcher In Admin Bar
	 *
	 * @author Darpan
	 */
	public function bm_add_flexibooking_language_switcher_in_admin_bar( $wp_admin_bar ) {
		$dbhandler                      = new BM_DBhandler();
		$language_switcher_in_admin_bar = $dbhandler->get_global_option_value( 'bm_show_lng_swtchr_in_admin_bar', '0' );
		$show_admin_bar                 = apply_filters( 'flexibooking_show_lang_switchr_in_admin_bar', $language_switcher_in_admin_bar );

		if ( $show_admin_bar == 1 ) {
			$wp_admin_bar->add_menu(
				array(
					'parent' => false,
					'id'     => 'bm_flexibooking_current_language',
					'title'  => $this->flexibooking_language_switcher(),
					'href'   => false,
				)
			);

			do_action( 'bm_flexibooking_admin_bar_language_switcher_added', $wp_admin_bar );
		}
	}//end bm_add_flexibooking_language_switcher_in_admin_bar()


	/**
	 * Add Language Switcher In footer
	 *
	 * @author Darpan
	 */
	public function bm_add_flexibooking_language_switcher_in_footer() {
		$dbhandler                   = new BM_DBhandler();
		$bmrequests                  = new BM_Request();
		$language_switcher_in_footer = $dbhandler->get_global_option_value( 'bm_show_lng_swtchr_in_footer', '0' );
		$show_admin_bar              = apply_filters( 'flexibooking_show_lang_switchr_in_footer', $language_switcher_in_footer );

		if ( $show_admin_bar == 1 ) {
			$html  = '<div class="flexi-lang-select-box" id="bm_flexibooking_current_language">';
			$html .= $this->flexibooking_language_switcher();
			$html .= '</div>';

			$html = apply_filters( 'bm_flexibooking_language_switcher_footer_html', $html );

			echo wp_kses( $html, $bmrequests->bm_fetch_expanded_allowed_tags() );

			do_action( 'bm_flexibooking_footer_language_switcher_added', $html );
		}
	}//end bm_add_flexibooking_language_switcher_in_footer()


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
		$post       = apply_filters( 'bm_flexibooking_set_language_post_data', $post );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$set_language = isset( $post['flexi_lang_code'] ) ? esc_html( sanitize_text_field( wp_unslash( $post['flexi_lang_code'] ) ) ) : esc_html( 'en' );

			if ( in_array( $set_language, array( 'en', 'it' ) ) ) {
				$current_locale = $set_language == 'it' ? 'it_IT' : 'en_US';
				$dbhandler->update_global_option_value( 'bm_flexi_current_language', $set_language );
				$dbhandler->update_global_option_value( 'bm_flexi_current_locale', $current_locale );
				$this->bm_flexibooking_load_locale();

				do_action( 'bm_flexibooking_language_set', $set_language, $current_locale );

				$data['status'] = true;
			}
		}

		$data = apply_filters( 'bm_flexibooking_set_language_response', $data, $set_language );

		echo wp_json_encode( $data );
		die;
	}//end bm_flexibooking_set_language()


	/**
	 * Load locale
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_load_locale() {
		$dbhandler      = new BM_DBhandler();
		$current_locale = $dbhandler->get_global_option_value( 'bm_flexi_current_locale', 'en_US' );
		$current_locale = apply_filters( 'bm_flexibooking_modify_locale', $current_locale );

		switch_to_locale( $current_locale );

		$current_locale == 'en_US' ? update_option( 'WPLANG', '' ) : update_option( 'WPLANG', $current_locale );

		do_action( 'bm_flexibooking_locale_switched', $current_locale );
	}//end bm_flexibooking_load_locale()


	/**
	 * Sort service Listing
	 *
	 * @author Darpan
	 */
	public function bm_sort_service_listing() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post                  = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post                  = apply_filters( 'bm_flexibooking_modify_sort_post_data', $post );
		$dbhandler             = new BM_DBhandler();
		$bmrequests            = new BM_Request();
		$total_service_records = $dbhandler->bm_count( 'SERVICE' );
		$category_name         = array();
		$data                  = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$ids     = isset( $post['ids'] ) ? $post['ids'] : array();

			if ( ! empty( $ids ) && $total_service_records > 0 ) {
				$order = ( 1 + $offset );
				for ( $i = 0; $i < $total_service_records; $i++ ) {
					if ( isset( $ids[ $i ] ) ) {
						$update_data = array(
							'service_position'   => $order,
							'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);
						$dbhandler->update_row( 'SERVICE', 'id', $ids[ $i ], $update_data, '', '%d' );
						++$order;
					}
				}
			}

			$services = $dbhandler->get_all_result( 'SERVICE', array( 'id', 'service_name', 'is_service_front', 'service_position' ), 1, 'results', $offset, $limit, 'service_position', false );
			$services = apply_filters( 'bm_flexibooking_modify_sorted_services', $services );

			if ( ! empty( $services ) && is_array( $services ) ) {
				foreach ( $services as $service ) {
					$category_name[] = $bmrequests->bm_fetch_category_name_by_service_id( $service->id ? $service->id : 0 );
				}
			}

			$category_name = apply_filters( 'bm_flexibooking_modify_category_names', $category_name, $services );

			$num_of_pages               = isset( $limit ) ? ceil( $total_service_records / $limit ) : 1;
			$data['status']             = true;
			$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['services']           = $services;
			$data['category_name']      = $category_name;
		}

		$data = apply_filters( 'bm_flexibooking_modify_sort_data', $data, $post );

		echo wp_json_encode( $data );
		die;
	}//end bm_sort_service_listing()


	/**
	 * Change service visibility
	 *
	 * @author Darpan
	 */
	public function bm_change_service_visibility() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_service_visibility_id', $id );

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$data       = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$service          = $dbhandler->get_row( 'SERVICE', $id );
			$is_service_front = isset( $service->is_service_front ) ? $service->is_service_front : 0;

			$update_data = array(
				'is_service_front'   => $is_service_front == 0 ? 1 : 0,
				'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			do_action( 'bm_flexibooking_before_service_visibility_update', $id, $update_data );

			$update = $dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );

			do_action( 'bm_flexibooking_after_service_visibility_update', $id, $service, $update );

			if ( $update ) {
				$data['status'] = true;
			}
		}

		$data = apply_filters( 'bm_flexibooking_modify_service_visibility_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_change_service_visibility()


	/**
	 * Change service visibility
	 *
	 * @author Darpan
	 */
	public function bm_change_extra_service_visibility() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$data       = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$extra_service          = $dbhandler->get_row( 'EXTRA', $id );
			$is_extra_service_front = isset( $extra_service->is_extra_service_front ) ? $extra_service->is_extra_service_front : 0;

			$update_data = array(
				'is_extra_service_front' => $is_extra_service_front == 0 ? 1 : 0,
				'extras_created_at'      => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$update = $dbhandler->update_row( 'EXTRA', 'id', $id, $update_data, '', '%d' );

			if ( $update ) {
				$data['status'] = true;
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_change_extra_service_visibility()


	/**
	 * Remove a service
	 *
	 * @author Darpan
	 */
	public function bm_remove_service() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post          = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post          = apply_filters( 'bm_flexibooking_modify_remove_service_post', $post );
		$dbhandler     = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$category_name = array();
		$data          = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$id      = isset( $post['id'] ) ? $post['id'] : 0;

			do_action( 'bm_flexibooking_service_id_before_service_removal', $id );

			if ( ! empty( $id ) ) {
				$svc_gallery_row = $dbhandler->get_all_result(
					'GALLERY',
					'*',
					array(
						'module_type' => 'SERVICE',
						'module_id'   => $id,
					),
					'results'
				);
				$svc_gallery_row = isset( $svc_gallery_row[0] ) && ! empty( $svc_gallery_row[0] ) ? $svc_gallery_row[0] : '';
				$svc_extra_rows  = $dbhandler->get_all_result( 'EXTRA', '*', array( 'service_id' => $id ), 'results' );
				$time_row        = $dbhandler->get_row( 'TIME', $id, 'service_id' );
				$bookings        = $dbhandler->get_all_result( 'BOOKING', '*', array( 'service_id' => $id ), 'results' );

				if ( ! empty( $svc_gallery_row ) ) {
					$dbhandler->remove_row( 'GALLERY', 'id', $svc_gallery_row->id, '%d' );
				}

				if ( ! empty( $svc_extra_rows ) ) {
					$svc_gallery_deleted = array();
					foreach ( $svc_extra_rows as $extra_row ) {
						$svc_gallery_deleted[] = $dbhandler->remove_row( 'EXTRA', 'id', $extra_row->id, '%d' );
					}
				}

				if ( ! empty( $time_row ) ) {
					$dbhandler->remove_row( 'TIME', 'id', $time_row->id, '%d' );
				}

				if ( ! empty( $time_row ) ) {
					$dbhandler->remove_row( 'TIME', 'id', $time_row->id, '%d' );
				}

				$service_removed = $dbhandler->remove_row( 'SERVICE', 'id', $id, '%d' );

				do_action( 'bm_flexibooking_after_service_removal', $id, $service_removed );

				if ( $service_removed ) {
					$frontend_selected_services = $dbhandler->get_global_option_value( 'bm_service_search_selected_services' );
					$backend_selected_services  = $dbhandler->get_global_option_value( 'bm_backend_dashboard_revenue_wise_order_svc_search_ids' );

					if ( ! empty( $frontend_selected_services ) && in_array( $id, $frontend_selected_services ) ) {
						$frontend_selected_services = array_diff( $frontend_selected_services, array( $id ) );
						$dbhandler->update_global_option_value( 'bm_service_search_selected_services', $frontend_selected_services );
					}

					do_action( 'bm_flexibooking_after_frontend_selected_service_removal', $id, $frontend_selected_services );

					if ( ! empty( $backend_selected_services ) && in_array( $id, $backend_selected_services ) ) {
						$backend_selected_services = array_diff( $backend_selected_services, array( $id ) );
						$dbhandler->update_global_option_value( 'bm_service_search_selected_services', $backend_selected_services );
					}

					do_action( 'bm_flexibooking_after_backend_selected_service_removal', $id, $backend_selected_services );

					if ( ! empty( $bookings ) ) {
						foreach ( $bookings as $booking ) {
							$is_active   = isset( $booking->is_active ) ? $booking->is_active : 0;
							$transaction = $dbhandler->get_row( 'TRANSACTIONS', $booking->id, 'booking_id' );

							if ( ! empty( $transaction ) ) {
								$dbhandler->remove_row( 'TRANSACTIONS', 'id', $transaction->id, '%d' );
							}

							if ( $is_active == 1 ) {
								$bmrequests->bm_cancel_and_refund_order( $booking->id );
							}
						}
					}

					$total_service_records      = $dbhandler->bm_count( 'SERVICE' );
					$num_of_pages               = isset( $limit ) ? ceil( $total_service_records / $limit ) : 1;
					$data['status']             = true;
					$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
					$data['current_pagenumber'] = ( 1 + $offset );
					$data['services']           = $dbhandler->get_all_result( 'SERVICE', array( 'id', 'service_name', 'is_service_front', 'service_position' ), 1, 'results', $offset, $limit, 'service_position', false );

					if ( ! empty( $data['services'] ) ) {
						foreach ( $data['services'] as $key => $service ) {
							$category_name[ $key ] = $bmrequests->bm_fetch_category_name_by_service_id( $service->id );
						}
					}

					$data['category_name'] = $category_name;
				}
			}
		} //end if

		$data = apply_filters( 'bm_flexibooking_modify_remove_service_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_service()


	/**
	 * Fetch templates
	 *
	 * @author Darpan
	 */
	public function bm_fetch_template_listing() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post = apply_filters( 'bm_flexibooking_modify_template_listing_post', $post );

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$type_names = array();
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;

			$language   = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
			$name_field = $language == 'it' ? 'tmpl_name_it' : 'tmpl_name_en';

			$total_template_records     = $dbhandler->bm_count( 'EMAIL_TMPL' );
			$num_of_pages               = isset( $limit ) ? ceil( $total_template_records / $limit ) : 1;
			$data['status']             = true;
			$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['templates']          = $dbhandler->get_all_result( 'EMAIL_TMPL', array( 'id', $name_field, 'type', 'status' ), 1, 'results', $offset, $limit );

			if ( ! empty( $data['templates'] ) ) {
				foreach ( $data['templates'] as $key => $template ) {
					$type_names[ $key ] = $bmrequests->bm_fetch_template_type_name_by_type_id( $template->type );
				}
			}

			$data['type_name'] = $type_names;
		}

		$data = apply_filters( 'bm_flexibooking_modify_template_listing_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_template_listing()


	/**
	 * Fetch price modules
	 *
	 * @author Darpan
	 */
	public function bm_fetch_price_module_listing() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post = apply_filters( 'bm_flexibooking_modify_price_module_listing_post', $post );

		$dbhandler  = new BM_DBhandler();
		$type_names = array();
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;

			$total_price_module_records = $dbhandler->bm_count( 'EXTERNAL_SERVICE_PRICE_MODULE' );
			$num_of_pages               = isset( $limit ) ? ceil( $total_price_module_records / $limit ) : 1;
			$data['status']             = true;
			$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['price_modules']      = $dbhandler->get_all_result( 'EXTERNAL_SERVICE_PRICE_MODULE', array( 'id', 'module_name', 'status' ), 1, 'results', $offset, $limit );
		}

		$data = apply_filters( 'bm_flexibooking_modify_price_module_listing_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_price_module_listing()


	/**
	 * Fetch notification processes
	 *
	 * @author Darpan
	 */
	public function bm_fetch_notification_processes_listing() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post = apply_filters( 'bm_flexibooking_modify_notification_process_listing_post', $post );

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$type_names = array();
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;

			$total_processes_records        = $dbhandler->bm_count( 'EVENTNOTIFICATION' );
			$num_of_pages                   = isset( $limit ) ? ceil( $total_processes_records / $limit ) : 1;
			$data['status']                 = true;
			$data['pagination']             = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
			$data['current_pagenumber']     = ( 1 + $offset );
			$data['notification_processes'] = $dbhandler->get_all_result( 'EVENTNOTIFICATION', array( 'id', 'name', 'type', 'status' ), 1, 'results', $offset, $limit );

			if ( ! empty( $data['notification_processes'] ) ) {
				foreach ( $data['notification_processes'] as $key => $process ) {
					$type_names[ $key ] = $bmrequests->bm_fetch_process_type_name_by_type_id( $process->type );
				}
			}

			$data['process_type'] = $type_names;
		}

		$data = apply_filters( 'bm_flexibooking_modify_notification_process_listing_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_notification_processes_listing()


	/**
	 * Fetch notification processes
	 *
	 * @author Darpan
	 */
	public function bm_remove_template() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post = apply_filters( 'bm_flexibooking_modify_remove_template_post', $post );

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$type_names = array();
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$id      = isset( $post['id'] ) ? $post['id'] : 0;

			$language = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
			$template = $dbhandler->get_row( 'EMAIL_TMPL', $id );

			if ( ! empty( $template ) ) {
				do_action( 'bm_flexibooking_before_template_removal', $id, $template );

				$removed         = $dbhandler->remove_row( 'EMAIL_TMPL', 'id', $id, '%d' );
				$email_templates = $dbhandler->get_all_result( 'EMAIL_TMPL', '*', 1, 'results' );

				if ( empty( $email_templates ) ) {
					$dbhandler->update_global_option_value( 'bm_email_templates_created', '0' );
				}

				do_action( 'bm_flexibooking_after_template_removal', $id, $template, $removed );

				if ( $removed ) {
					$name_field = $language == 'it' ? 'tmpl_name_it' : 'tmpl_name_en';

					$total_template_records     = $dbhandler->bm_count( 'EMAIL_TMPL' );
					$num_of_pages               = isset( $limit ) ? ceil( $total_template_records / $limit ) : 1;
					$data['status']             = true;
					$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
					$data['current_pagenumber'] = ( 1 + $offset );
					$data['templates']          = $dbhandler->get_all_result( 'EMAIL_TMPL', array( 'id', $name_field, 'type', 'status' ), 1, 'results', $offset, $limit );

					if ( ! empty( $data['templates'] ) ) {
						foreach ( $data['templates'] as $key => $template ) {
							$type_names[ $key ] = $bmrequests->bm_fetch_template_type_name_by_type_id( $template->type );
						}
					}

					$data['type_name'] = $type_names;
				}
			}
		}

		$data = apply_filters( 'bm_flexibooking_modify_remove_template_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_template()


	/**
	 * Change template visibility
	 *
	 * @author Darpan
	 */
	public function bm_change_email_template_visibility() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post       = apply_filters( 'bm_flexibooking_modify_template_visiblity_post', $post );
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$template_id  = isset( $post['id'] ) ? $post['id'] : 0;
			$input_status = isset( $post['status'] ) ? $post['status'] : -1;
			$input_type   = isset( $post['type'] ) ? $post['type'] : -1;

			if ( $template_id > 0 && $input_status != -1 && $input_type != -1 ) {
				do_action( 'bm_flexibooking_before_template_visibility_change', $template_id, $input_status, $input_type );

				$active_type = $bmrequests->bm_check_active_email_template_of_a_specific_type( $input_type );

				if ( $input_status == 1 && $active_type ) {
					$data['status'] = 'error';
				} else {
					$update_data = array(
						'status'              => $input_status,
						'template_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
					);

					$update = $dbhandler->update_row( 'EMAIL_TMPL', 'id', $template_id, $update_data, '', '%d' );

					do_action( 'bm_flexibooking_after_template_visibility_change', $template_id, $input_status, $update );

					if ( $update ) {
						$data['status'] = true;
					}
				}
			}
		}

		$data = apply_filters( 'bm_flexibooking_modify_template_visibility_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_change_email_template_visibility()


	/**
	 * Change process visibility
	 *
	 * @author Darpan
	 */
	public function bm_change_notification_process_visibility() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post       = apply_filters( 'bm_flexibooking_modify_process_visiblity_post', $post );
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$process_id   = isset( $post['id'] ) ? $post['id'] : 0;
			$input_status = isset( $post['status'] ) ? $post['status'] : -1;
			$input_type   = isset( $post['type'] ) ? $post['type'] : -1;

			if ( $process_id > 0 && $input_status != -1 && $input_type != -1 ) {
				do_action( 'bm_flexibooking_before_process_visibility_change', $process_id, $input_status, $input_type );

				$active_type = $bmrequests->bm_check_active_process_of_a_specific_type( $input_type );

				if ( $input_status == 1 && $active_type ) {
					$data['status'] = 'error';
				} else {
					$update_data = array(
						'status'     => $input_status,
						'created_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
					);

					$update = $dbhandler->update_row( 'EVENTNOTIFICATION', 'id', $process_id, $update_data, '', '%d' );

					do_action( 'bm_flexibooking_after_process_visibility_change', $process_id, $input_status, $update );

					if ( $update ) {
						$data['status'] = true;
					}
				}
			}
		}

		$data = apply_filters( 'bm_flexibooking_process_visibility_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_change_notification_process_visibility()


	/**
	 * Remove a process
	 *
	 * @author Darpan
	 */
	public function bm_remove_notification_process() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post = apply_filters( 'bm_flexibooking_modify_remove_notification_process_post', $post );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$id      = isset( $post['id'] ) ? $post['id'] : 0;

			$process = $dbhandler->get_row( 'EVENTNOTIFICATION', $id );

			// do_action('bm_before_removing_notification_process', $process, $id);

			if ( ! empty( $process ) ) {
				$removed = $dbhandler->remove_row( 'EVENTNOTIFICATION', 'id', $id, '%d' );

				// do_action('bm_after_removing_notification_process', $id, $process, $removed);

				if ( $removed ) {
					$total_processes_records        = $dbhandler->bm_count( 'EVENTNOTIFICATION' );
					$num_of_pages                   = isset( $limit ) ? ceil( $total_processes_records / $limit ) : 1;
					$data['status']                 = true;
					$data['pagination']             = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
					$data['current_pagenumber']     = ( 1 + $offset );
					$data['notification_processes'] = $dbhandler->get_all_result( 'EVENTNOTIFICATION', array( 'id', 'name', 'type', 'status' ), 1, 'results', $offset, $limit );

					if ( ! empty( $data['notification_processes'] ) ) {
						foreach ( $data['notification_processes'] as $key => $process ) {
							$type_names[ $key ] = ( new BM_Request() )->bm_fetch_process_type_name_by_type_id( $process->type );
						}
					}

					$data['process_type'] = $type_names;
				}
			}
		}

		$data = apply_filters( 'bm_flexibooking_modify_remove_notification_process_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_notification_process()

	/**
	 * Sort category Listing
	 *
	 * @author Darpan
	 */
	public function bm_sort_category_listing() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post                   = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post                   = apply_filters( 'bm_flexibooking_modify_sort_category_post', $post );
		$dbhandler              = new BM_DBhandler();
		$total_category_records = $dbhandler->bm_count( 'CATEGORY' );
		$data                   = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$ids     = isset( $post['ids'] ) ? $post['ids'] : array();

			do_action( 'bm_flexibooking_before_category_sort', $ids, $total_category_records );

			if ( ! empty( $ids ) && $total_category_records > 0 ) {
				$order = ( 1 + $offset );
				for ( $i = 0; $i < $total_category_records; $i++ ) {
					if ( isset( $ids[ $i ] ) ) {
						$update_data = array(
							'cat_position'   => $order,
							'cat_updated_at' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$dbhandler->update_row( 'CATEGORY', 'id', $ids[ $i ], $update_data, '', '%d' );
						++$order;
					}
				}
			}

			do_action( 'bm_flexibooking_after_category_sort', $ids, $total_category_records );

			$categories                 = $dbhandler->get_all_result( 'CATEGORY', array( 'id', 'cat_name', 'cat_in_front', 'cat_position' ), 1, 'results', $offset, $limit, 'cat_position', false );
			$cat_ids                    = wp_list_pluck( $categories, 'id', 0 );
			$cat_ids                    = ! empty( $cat_ids ) && is_array( $cat_ids ) ? implode( ',', $cat_ids ) : '';
			$num_of_pages               = isset( $limit ) ? ceil( $total_category_records / $limit ) : 1;
			$data['status']             = true;
			$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['cat_ids']            = $cat_ids;
			$data['categories']         = $categories;
		}

		$data = apply_filters( 'bm_flexibooking_sort_category_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_sort_category_listing()


	/**
	 * Change category visibility
	 *
	 * @author Darpan
	 */
	public function bm_change_category_visibility() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_category_visibility_id', $id );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$category     = $dbhandler->get_row( 'CATEGORY', $id );
			$cat_in_front = isset( $category->cat_in_front ) ? $category->cat_in_front : 0;

			$update_data = array(
				'cat_in_front'   => $cat_in_front == 0 ? 1 : 0,
				'cat_updated_at' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
			);

			do_action( 'bm_flexibooking_before_category_visibility_change', $id, $category, $update_data );

			$update = $dbhandler->update_row( 'CATEGORY', 'id', $id, $update_data, '', '%d' );

			do_action( 'bm_flexibooking_after_category_visibility_change', $id, $category, $update );

			if ( $update ) {
				$data['status'] = true;
			}
		}

		$data = apply_filters( 'bm_flexibooking_category_visibility_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_change_category_visibility()


	/**
	 * Change customer visibility
	 *
	 * @author Darpan
	 */
	public function bm_change_customer_visibility() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_customer_visibility_id', $id );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$customer  = $dbhandler->get_row( 'CUSTOMERS', $id );
			$is_active = isset( $customer->is_active ) ? $customer->is_active : 0;

			$update_data = array(
				'is_active'           => $is_active == 0 ? 1 : 0,
				'customer_updated_at' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
			);

			do_action( 'bm_flexibooking_before_customer_visibility_change', $id, $customer, $update_data );

			$update = $dbhandler->update_row( 'CUSTOMERS', 'id', $id, $update_data, '', '%d' );

			do_action( 'bm_flexibooking_after_customer_visibility_change', $id, $customer, $update );

			if ( $update ) {
				$data['status'] = true;
			}
		}

		$data = apply_filters( 'bm_flexibooking_customer_visibility_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_change_customer_visibility()


	/**
	 * Remove a category
	 *
	 * @author Darpan
	 */
	public function bm_remove_category() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post      = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post      = apply_filters( 'bm_flexibooking_modify_remove_category_post', $post );
		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$id           = isset( $post['id'] ) ? $post['id'] : 0;
			$service_rows = $dbhandler->get_all_result( 'SERVICE', '*', array( 'service_category' => $id ), 'results' );

			do_action( 'bm_flexibooking_before_unlinking_services_from_category', $id, $service_rows );

			if ( isset( $service_rows ) && ! empty( $service_rows ) ) {
				foreach ( $service_rows as $service_row ) {
					if ( ! empty( $service_row ) ) {
						$update_data = array(
							'service_category'   => '',
							'service_updated_at' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$dbhandler->update_row( 'SERVICE', 'id', $service_row->id, $update_data, '', '%d' );

						do_action( 'bm_flexibooking_after_unlinking_services_from_category', $service_row->id, $id );
					}
				}
			}

			do_action( 'bm_flexibooking_before_category_removal', $id );

			$cat_removed = $dbhandler->remove_row( 'CATEGORY', 'id', $id, '%d' );

			if ( $cat_removed ) {
				$frontend_selected_categories = $dbhandler->get_global_option_value( 'bm_front_svc_search_shortcode_cat_ids' );
				$backend_selected_categories  = $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' );

				if ( ! empty( $frontend_selected_categories ) && in_array( $id, $frontend_selected_categories ) ) {
					$frontend_selected_categories = array_diff( $frontend_selected_categories, array( $id ) );
					$dbhandler->update_global_option_value( 'bm_front_svc_search_shortcode_cat_ids', $frontend_selected_categories );
				}

				do_action( 'bm_flexibooking_after_frontend_selected_categories_removal', $id, $frontend_selected_categories );

				if ( ! empty( $backend_selected_categories ) && in_array( $id, $backend_selected_categories ) ) {
					$backend_selected_categories = array_diff( $backend_selected_categories, array( $id ) );
					$dbhandler->update_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids', $backend_selected_categories );
				}

				do_action( 'bm_flexibooking_after_backend_selected_categories_removal', $id, $backend_selected_categories );

				$total_category_records     = $dbhandler->bm_count( 'CATEGORY' );
				$categories                 = $dbhandler->get_all_result( 'CATEGORY', array( 'id', 'cat_name', 'cat_in_front', 'cat_position' ), 1, 'results', $offset, $limit, 'cat_position', false );
				$cat_ids                    = wp_list_pluck( $categories, 'id', 0 );
				$cat_ids                    = ! empty( $cat_ids ) && is_array( $cat_ids ) ? implode( ',', ( array_merge( array( 0 ), $cat_ids ) ) ) : '';
				$num_of_pages               = isset( $limit ) ? ceil( $total_category_records / $limit ) : 1;
				$data['status']             = true;
				$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
				$data['current_pagenumber'] = ( 1 + $offset );
				$data['cat_ids']            = $cat_ids;
				$data['categories']         = $categories;

				do_action( 'bm_flexibooking_after_category_removal', $id, $cat_removed );
			}
		}

		$data = apply_filters( 'bm_flexibooking_remove_category_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_category()


	/**
	 * Remove a price module
	 *
	 * @author Darpan
	 */
	public function bm_remove_price_module() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$post = apply_filters( 'bm_flexibooking_modify_remove_price_module_post', $post );

		$dbhandler = new BM_DBhandler();
		$data      = array(
			'status'        => false,
			'is_removeable' => false,
		);

		if ( $post != false && $post != null ) {
			$base    = isset( $post['base'] ) ? $post['base'] : '';
			$limit   = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset  = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$id      = isset( $post['id'] ) ? $post['id'] : 0;

			$service_linked = $dbhandler->get_all_result( 'SERVICE', '*', array( 'external_price_module' => $id ), 'results' );
			$service_linked = apply_filters( 'bm_flexibooking_modify_remove_price_module_linked_services', $service_linked );

			$services = $dbhandler->get_all_result( 'SERVICE', '*', 1, 'results' );
			$result   = ( new BM_Request() )->bm_search_data_from_serialized_column( $services, 'variable_svc_price_modules', $id );
			$result   = apply_filters( 'bm_flexibooking_modify_remove_price_module_filtered_services', $result, $id, $services );

			if ( empty( $service_linked ) && empty( $result ) ) {
				$data['is_removeable'] = true;
			}

			$data = apply_filters( 'bm_flexibooking_after_checking_price_module_removal', $data, $id );

			if ( $data['is_removeable'] == true ) {
				$removed = $dbhandler->remove_row( 'EXTERNAL_SERVICE_PRICE_MODULE', 'id', $id, '%d' );

				if ( $removed ) {
					$total_price_module_records = $dbhandler->bm_count( 'EXTERNAL_SERVICE_PRICE_MODULE' );
					$num_of_pages               = isset( $limit ) ? ceil( $total_price_module_records / $limit ) : 1;
					$data['pagination']         = wp_kses_post( $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' ) );
					$data['current_pagenumber'] = ( 1 + $offset );
					$data['price_modules']      = $dbhandler->get_all_result( 'EXTERNAL_SERVICE_PRICE_MODULE', array( 'id', 'module_name', 'status' ), 1, 'results', $offset, $limit );
				}

				do_action( 'bm_flexibooking_after_price_module_removal', $id, $data['price_modules'], $total_price_module_records );
			}

			$data['status'] = true;
		}

		$data = apply_filters( 'bm_flexibooking_price_module_removal_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_price_module()


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

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_service_prices_service_id', $id );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );
			$service = apply_filters( 'bm_flexibooking_prices_service_object', $service, $id );

			$data['status']          = true;
			$data['default_price']   = ! empty( $service ) && isset( $service->default_price ) ? $service->default_price : 0;
			$data['variable_price']  = ! empty( $service ) && isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();
			$data['variable_module'] = ! empty( $service ) && isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();
			$data['unavailability']  = ! empty( $service ) && isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();
			$data['gbl_unavlabilty'] = $dbhandler->get_global_option_value( 'bm_global_unavailability' );

			$data = apply_filters( 'bm_flexibooking_after_getting_service_prices', $data, $service, $id );
		}

		$data = apply_filters( 'bm_flexibooking_service_prices_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_get_service_prices()


	/**
	 * Stopsales in Service Calender on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_serice_stopsales() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_stopsales_service_id', $id );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );
			$service = apply_filters( 'bm_flexibooking_stopsales_service_object', $service, $id );

			$data['status']             = true;
			$data['default_stopsales']  = ! empty( $service ) && isset( $service->default_stopsales ) ? $service->default_stopsales : '';
			$data['variable_stopsales'] = ! empty( $service ) && isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();
			$data['unavailability']     = ! empty( $service ) && isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();
			$data['gbl_unavlabilty']    = $dbhandler->get_global_option_value( 'bm_global_unavailability' );
		}

		$data = apply_filters( 'bm_flexibooking_service_stopsales_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_get_serice_stopsales()


	/**
	 * Saleswitch in Service Calender on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_service_saleswitch() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_saleswitch_service_id', $id );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );
			$service = apply_filters( 'bm_flexibooking_saleswitch_service_object', $service, $id );

			$data['status']              = true;
			$data['default_saleswitch']  = ! empty( $service ) && isset( $service->default_saleswitch ) ? $service->default_saleswitch : '';
			$data['variable_saleswitch'] = ! empty( $service ) && isset( $service->variable_saleswitch ) ? maybe_unserialize( $service->variable_saleswitch ) : array();
			$data['unavailability']      = ! empty( $service ) && isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();
			$data['gbl_unavlabilty']     = $dbhandler->get_global_option_value( 'bm_global_unavailability' );
		}

		$data = apply_filters( 'bm_flexibooking_service_saleswitch_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_get_service_saleswitch()


	/**
	 * Service Maximum Capacity in Service Calender on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_service_max_cap() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_max_cap_service_id', $id );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );
			$service = apply_filters( 'bm_flexibooking_max_cap_service_object', $service, $id );

			$data['status']           = true;
			$data['default_max_cap']  = ! empty( $service ) && isset( $service->default_max_cap ) ? $service->default_max_cap : 0;
			$data['variable_max_cap'] = ! empty( $service ) && isset( $service->variable_max_cap ) ? maybe_unserialize( $service->variable_max_cap ) : array();
			$data['unavailability']   = ! empty( $service ) && isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();
			$data['gbl_unavlabilty']  = $dbhandler->get_global_option_value( 'bm_global_unavailability' );
		}

		$data = apply_filters( 'bm_flexibooking_service_max_cap_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_get_service_max_cap()


	/**
	 * Service Time slots in Service Calender on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_service_time_slots() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );

		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );

		if ( $id != false && $id != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );
			$service = apply_filters( 'bm_flexibooking_time_slots_service_object', $service, $id );

			$data['status']          = true;
			$variable_time_slots     = ! empty( $service ) && isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			$data['slot_ids']        = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'slot_id' ) : array();
			$data['dates']           = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();
			$data['unavailability']  = ! empty( $service ) && isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();
			$data['gbl_unavlabilty'] = $dbhandler->get_global_option_value( 'bm_global_unavailability' );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_get_service_time_slots()


	/**
	 * Fetch specific service time slot
	 *
	 * @author Darpan
	 */
	public function bm_get_specific_time_slot() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_specific_time_slot_service_id', $id );

		$date      = filter_input( INPUT_POST, 'date' );
		$dbhandler = new BM_DBhandler();
		$data      = array(
			'status'    => false,
			'slot_data' => array(),
		);

		if ( $id != false && $id != null && $date != false && $date != null ) {
			$service = $dbhandler->get_row( 'SERVICE', $id );
			$service = apply_filters( 'bm_flexibooking_specific_time_slot_service_object', $service, $id );

			if ( ! empty( $service ) ) {
				$time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				$dates      = ! empty( $time_slots ) ? wp_list_pluck( $time_slots, 'date' ) : array();
				$index      = (int) array_search( $date, $dates );

				if ( isset( $index ) && ! empty( $index ) ) {
					$data['status']    = true;
					$data['slot_data'] = $time_slots[ $index ];
				}
			}
		}

		$data = apply_filters( 'bm_flexibooking_specific_service_time_slot_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_get_specific_time_slot()


	/**
	 * Field data on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_all_field_labels() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$ordering            = filter_input( INPUT_POST, 'ordering', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$dbhandler           = new BM_DBhandler();
		$total_field_records = $dbhandler->bm_count( 'FIELDS' );

		$ordering = apply_filters( 'bm_flexibooking_before_updating_field_ordering', $ordering, $total_field_records );

		if ( $ordering != false && $ordering != null ) {
			if ( ! empty( $ordering ) && $total_field_records > 0 ) {
				for ( $i = 0; $i < $total_field_records; $i++ ) {
					$dbhandler->update_row( 'FIELDS', 'ordering', $ordering[ $i ], array( 'field_position' => ( $i + 1 ) ), '', '%d' );
				}
			}

			do_action( 'bm_flexibooking_after_updating_field_ordering', $ordering );
		}

		$fields = $dbhandler->get_all_result( 'FIELDS', array( 'id', 'field_type', 'field_label', 'field_desc', 'ordering', 'field_position' ), 1, 'results', 0, false, 'field_position', false );
		$fields = apply_filters( 'bm_flexibooking_before_fetching_fields', $fields );

		if ( ! empty( $fields ) ) {
			foreach ( $fields as $field ) {
				$is_default         = ( new BM_Request() )->bm_check_is_default_field( $field->id );
				$field->is_default  = $is_default;
				$field->field_label = isset( $field->field_label ) ? $field->field_label : '';
				$field->field_type  = isset( $field->field_type ) ? $field->field_type : '';
			}

			$fields = apply_filters( 'bm_flexibooking_after_fetching_fields', $fields );
		}

		$fields = apply_filters( 'bm_flexibooking_field_labels_response', $fields );

		echo wp_json_encode( $fields );
		die;
	}//end bm_get_all_field_labels()


	/**
	 * Field key and order on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_fieldkey_and_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$type       = filter_input( INPUT_POST, 'type' );
		$data       = array();

		$lastrow_id = $dbhandler->get_all_result( 'FIELDS', 'id', 1, 'var', 0, 1, 'id', 'DESC' );
		$lastrow_id = apply_filters( 'bm_flexibooking_before_fetching_last_row_id', $lastrow_id );

		$ordering = ( $lastrow_id + 1 );

		$field_key = $bmrequests->bm_fetch_field_key( $ordering );
		$field_key = apply_filters( 'bm_flexibooking_before_fetching_field_key', $field_key );

		$primary_mail_key = $bmrequests->bm_check_and_return_field_key_of_primary_email_in_field_data();
		$primary_mail_key = apply_filters( 'bm_flexibooking_primary_mail_filed_key', $primary_mail_key );

		do_action( 'bm_flexibooking_after_fetching_field_key_and_ordering', $field_key, $ordering );

		if ( $type != false && $type != null ) {
			if ( ! empty( $ordering ) && ! empty( $field_key ) ) {
				$data = array(
					'type'             => $type,
					'ordering'         => $ordering,
					'field_key'        => $field_key,
					'primary_mail_key' => $primary_mail_key,
				);
			}
		}

		$data = apply_filters( 'bm_flexibooking_fieldkey_and_order_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_get_fieldkey_and_order()


	/**
	 * Remove Time slot in calendar
	 *
	 * @author Darpan
	 */
	public function bm_remove_variable_time_slot() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();

		$id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$id = apply_filters( 'bm_flexibooking_modify_remove_variable_slot_service_id', $id );

		$date = filter_input( INPUT_POST, 'date' );
		$date = apply_filters( 'bm_flexibooking_modify_remove_variable_slot_date', $date );

		$data = array( 'status' => '' );

		if ( $id != false && $id != null && $date != false && $date != null ) {
			$service            = $dbhandler->get_row( 'SERVICE', $id );
			$variable_slot_data = ! empty( $service ) && isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			$dates              = ! empty( $variable_slot_data ) ? wp_list_pluck( $variable_slot_data, 'date' ) : array();

			if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
				$index = (int) array_search( $date, $dates );

				do_action( 'bm_before_unsetting_variable_time_slot_index', $variable_slot_data, $date, $index );

				unset( $variable_slot_data[ $index ] );
				$update_data = array();
				$i           = 1;

				foreach ( $variable_slot_data as $key => $value ) {
					if ( isset( $variable_slot_data[ $key ] ) ) {
						$update_data[ $i ] = $value;
					}

					++$i;
				}

				$update_count = ! empty( $update_data ) ? count( $update_data ) : 0;

				if ( ! empty( $update_count ) ) {
					for ( $i = 1; $i <= $update_count; $i++ ) {
						$update_data[ $i ]['slot_id'] = $i;
					}
				}

				do_action( 'bm_before_updating_variable_time_slots', $id, $update_data );

				$update_slots = $dbhandler->update_row( 'SERVICE', 'id', $id, array( 'variable_time_slots' => maybe_serialize( $update_data ) ), '', '%d' );

				do_action( 'bm_after_updating_variable_time_slots', $id, $update_slots );

				if ( $update_slots ) {
					$service            = $dbhandler->get_row( 'SERVICE', $id );
					$variable_slot_data = ! empty( $service ) && isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
					$data['status']     = true;
					$data['slot_ids']   = ! empty( $variable_slot_data ) ? wp_list_pluck( $variable_slot_data, 'slot_id' ) : array();
					$data['dates']      = ! empty( $variable_slot_data ) ? wp_list_pluck( $variable_slot_data, 'date' ) : array();

					do_action( 'bm_after_removing_variable_time_slot', $id, $data );
				}
			} //end if
		} //end if

		$data = apply_filters( 'bm_remove_variable_time_slot_response', $data );

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_variable_time_slot()


	/**
	 * Remove Field data
	 *
	 * @author Darpan
	 */
	public function bm_remove_field() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();
		$id        = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$data      = array( 'status' => '' );

		if ( $id != false && $id != null ) {
			$field_deleted = $dbhandler->remove_row( 'FIELDS', 'id', $id, '%d' );
			$fields        = $dbhandler->get_all_result( 'FIELDS', '*', 1, 'results' );

			if ( empty( $fields ) ) {
				$dbhandler->update_global_option_value( 'bm_booking_form_fields_created', '0' );
			}

			if ( $field_deleted ) {
				$data['status'] = 'deleted';
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_remove_field()


	/**
	 * Archive an order
	 *
	 * @author Darpan
	 */
	public function bm_archive_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Failed security check', 'service-booking' ) );
			return;
		}

		$dbhandler = new BM_DBhandler();
		$id        = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$data      = array( 'status' => false );

		if ( $id === false || $id === null ) {
			wp_send_json_error( esc_html__( 'Invalid order ID', 'service-booking' ) );
			return;
		}

		$can_delete = apply_filters( 'bm_before_order_archive', true, $id );
		if ( $can_delete === false ) {
			wp_send_json_error( esc_html__( 'Deletion prevented by another plugin', 'service-booking' ) );
			return;
		}

		$order_data       = $dbhandler->get_row( 'BOOKING', $id, 'id' );
		$slot_data        = $dbhandler->get_row( 'SLOTCOUNT', $id, 'booking_id' );
		$extraslot_data   = $dbhandler->get_all_result( 'EXTRASLOTCOUNT', '*', array( 'booking_id' => $id ), 'results' );
		$transaction_data = $dbhandler->get_row( 'TRANSACTIONS', $id, 'booking_id' );

		$folder    = 'new-mail';
		$directory = wp_normalize_path( plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details' );
		$pdf_path  = wp_normalize_path( $directory . '/order-details-booking-' . $id . '.pdf' );

		do_action( 'bm_before_order_archive', $id, $order_data, $slot_data, $extraslot_data, $transaction_data, $pdf_path );

		$archive_data = array(
			'original_id'      => $id,
			'booking_data'     => maybe_serialize( $order_data ),
			'slot_data'        => maybe_serialize( $slot_data ),
			'extraslot_data'   => maybe_serialize( $extraslot_data ),
			'transaction_data' => maybe_serialize( $transaction_data ),
			'pdf_path'         => $pdf_path,
			'deleted_at'       => current_time( 'mysql' ),
			'deleted_by'       => get_current_user_id(),
		);

		$archive_result = array();
		$archive_data   = ( new BM_Request() )->sanitize_request( $archive_data, 'BOOKING_ARCHIVE' );

		if ( $archive_data != false ) {
			$archive_result = $dbhandler->insert_row( 'BOOKING_ARCHIVE', $archive_data );
		}

		if ( empty( $archive_result ) ) {
			wp_send_json_error( esc_html__( 'Failed to archive order', 'service-booking' ) );
			return;
		}

		if ( ! empty( $slot_data ) ) {
			$dbhandler->remove_row( 'SLOTCOUNT', 'id', $slot_data->id, '%d' );
		}

		if ( ! empty( $extraslot_data ) ) {
			foreach ( $extraslot_data as $extraslot ) {
				$dbhandler->remove_row( 'EXTRASLOTCOUNT', 'id', $extraslot->id, '%d' );
			}
		}

		if ( ! empty( $transaction_data ) ) {
			$dbhandler->remove_row( 'TRANSACTIONS', 'id', $transaction_data->id, '%d' );
		}

		$order_deleted = $dbhandler->remove_row( 'BOOKING', 'id', $id, '%d' );

		if ( $order_deleted ) {
			do_action( 'bm_after_order_archive', $id, $archive_data );

			$archive_dir = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/archive/';
			if ( ! file_exists( $archive_dir ) ) {
				wp_mkdir_p( $archive_dir );
			}

			if ( file_exists( $pdf_path ) ) {
				$new_pdf_path = $archive_dir . 'order-details-booking-' . $id . '.pdf';
				rename( $pdf_path, $new_pdf_path );
			}

			$data['status'] = true;
		}

		do_action( 'bm_order_archive_complete', $id, $data['status'] );

		wp_send_json_success( $data );
	}//end bm_archive_order()


	/**
	 * Remove an order permanaently
	 *
	 * @author Darpan
	 */
	public function bm_remove_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Failed security check', 'service-booking' ) );
			return;
		}

		$dbhandler = new BM_DBhandler();
		$id        = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$data      = array( 'status' => false );

		if ( $id === false || $id === null ) {
			wp_send_json_error( esc_html__( 'Invalid order ID', 'service-booking' ) );
			return;
		}

		$can_delete = apply_filters( 'bm_before_order_delete', true, $id );
		if ( $can_delete === false ) {
			wp_send_json_error( esc_html__( 'Deletion prevented by another plugin', 'service-booking' ) );
			return;
		}

		$archive_data  = $dbhandler->get_row( 'BOOKING_ARCHIVE', $id );
		$order_deleted = $dbhandler->remove_row( 'BOOKING_ARCHIVE', 'id', $id, '%d' );

		if ( $order_deleted ) {
			do_action( 'bm_after_order_delete', $id, $archive_data );

			$archive_dir = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/archive/';
			$file        = $archive_dir . 'order-details-booking-' . $id . '.pdf';

			if ( file_exists( $file ) ) {
				unlink( $file );
			}

			$data['status'] = true;
		}

		do_action( 'bm_order_delete_complete', $id, $data['status'] );

		wp_send_json_success( $data );
	}//end bm_remove_order()


	/**
	 * Remove a failed order
	 *
	 * @author Darpan
	 */
	public function bm_remove_failed_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Failed security check', 'service-booking' ) );
			return;
		}

		$dbhandler = new BM_DBhandler();
		$id        = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$data      = array( 'status' => false );

		if ( $id === false || $id === null ) {
			wp_send_json_error( esc_html__( 'Invalid order ID', 'service-booking' ) );
			return;
		}

		$order_data    = $dbhandler->get_row( 'FAILED_TRANSACTIONS', $id );
		$order_deleted = $dbhandler->remove_row( 'FAILED_TRANSACTIONS', 'id', $id, '%d' );

		if ( $order_deleted ) {
			$data['status'] = true;
		}

		wp_send_json_success( $data );
	}//end bm_remove_failed_order()


	/**
	 * Restore an order
	 *
	 * @author Darpan
	 */
	public function bm_restore_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Failed security check', 'service-booking' ) );
			return;
		}

		$archive_id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$data       = array( 'status' => false );

		if ( $archive_id === false || $archive_id === null ) {
			wp_send_json_error( esc_html__( 'Invalid archive ID', 'service-booking' ) );
			return;
		}

		$dbhandler = new BM_DBhandler();
		$archive   = $dbhandler->get_row( 'BOOKING_ARCHIVE', $archive_id, 'id' );

		if ( ! $archive ) {
			wp_send_json_error( esc_html__( 'Archived order not found', 'service-booking' ) );
			return;
		}

		$can_restore = apply_filters( 'bm_before_order_restore', true, $archive_id, $archive );
		if ( $can_restore === false ) {
			wp_send_json_error( esc_html__( 'Restoration prevented by another plugin', 'service-booking' ) );
			return;
		}

		do_action( 'bm_before_order_restore', $archive_id, $archive );

		$booking_data     = maybe_unserialize( $archive->booking_data );
		$slot_data        = maybe_unserialize( $archive->slot_data );
		$extraslot_data   = maybe_unserialize( $archive->extraslot_data );
		$transaction_data = maybe_unserialize( $archive->transaction_data );

		$booking_id = $dbhandler->insert_row( 'BOOKING', (array) $booking_data );

		if ( $booking_id > 0 ) {
			if ( ! empty( $slot_data ) ) {
				unset( $slot_data->id );
				$slot_data->booking_id = $booking_id;
				$dbhandler->insert_row( 'SLOTCOUNT', (array) $slot_data );
			}

			if ( ! empty( $extraslot_data ) && is_array( $extraslot_data ) ) {
				foreach ( $extraslot_data as $extraslot ) {
					unset( $extraslot->id );
					$extraslot->booking_id = $booking_id;
					$dbhandler->insert_row( 'EXTRASLOTCOUNT', (array) $extraslot );
				}
			}

			if ( ! empty( $transaction_data ) ) {
				unset( $transaction_data->id );
				$transaction_data->booking_id = $booking_id;
				$dbhandler->insert_row( 'TRANSACTION', (array) $transaction_data );
			}

			if ( ! empty( $archive->pdf_path ) ) {
				$archive_dir  = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/archive/';
				$original_dir = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/new-mail/order-details/';

				if ( file_exists( $archive->pdf_path ) ) {
					$new_pdf_path = $original_dir . 'order-details-booking-' . $booking_id . '.pdf';
					rename( $archive->pdf_path, $new_pdf_path );
				}
			}

			$dbhandler->remove_row( 'BOOKING_ARCHIVE', 'id', $archive_id, '%d' );

			do_action( 'bm_after_order_restore', $booking_id, $archive );

			$data['status'] = true;
			$data['new_id'] = $booking_id;
		}

		wp_send_json_success( $data );
	}//end bm_restore_order()


	/**
	 * Delete an order permanently
	 *
	 * @author Darpan
	 */
	public function bm_delete_archive_permanently() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( esc_html__( 'Failed security check', 'service-booking' ) );
			return;
		}

		$archive_id = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$data       = array( 'status' => false );

		if ( $archive_id === false || $archive_id === null ) {
			wp_send_json_error( esc_html__( 'Invalid archive ID', 'service-booking' ) );
			return;
		}

		$dbhandler = new BM_DBhandler();
		$archive   = $dbhandler->get_row( 'BOOKING_ARCHIVE', $archive_id, 'id' );

		if ( $archive ) {
			if ( ! empty( $archive->pdf_path ) && file_exists( $archive->pdf_path ) ) {
				unlink( $archive->pdf_path );
			}

			$deleted = $dbhandler->remove_row( 'BOOKING_ARCHIVE', 'id', $archive_id, '%d' );

			if ( $deleted ) {
				$data['status'] = true;
			}
		}

		wp_send_json_success( $data );
	}//end bm_delete_archive_permanently()


	/**
	 * Fetch field settings on page load
	 *
	 * @author Darpan
	 */
	public function bm_get_field_settings() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$id         = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$data       = array();

		if ( $id != false && $id != null ) {
			$settings = $dbhandler->get_row( 'FIELDS', $id );

			if ( ! empty( $settings ) ) {
				$data['common'] = array(
					'id'                => isset( $settings->id ) ? $settings->id : 0,
					'field_type'        => isset( $settings->field_type ) ? $settings->field_type : '',
					'field_label'       => isset( $settings->field_label ) ? $settings->field_label : '',
					'field_name'        => isset( $settings->field_name ) ? $settings->field_name : '',
					'field_desc'        => isset( $settings->field_desc ) ? $settings->field_desc : '',
					'is_required'       => isset( $settings->is_required ) ? $settings->is_required : 0,
					'is_editable'       => isset( $settings->is_editable ) ? $settings->is_editable : 0,
					'ordering'          => isset( $settings->ordering ) ? $settings->ordering : 0,
					'woocommerce_field' => isset( $settings->woocommerce_field ) ? $settings->woocommerce_field : '',
					'field_key'         => isset( $settings->field_key ) ? $settings->field_key : '',
					'primary_mail_key'  => $bmrequests->bm_check_and_return_field_key_of_primary_email_in_field_data(),
					'field_position'    => isset( $settings->field_position ) ? $settings->field_position : '',
				);

				$data['field_options'] = isset( $settings->field_options ) ? maybe_unserialize( $settings->field_options ) : array();
			}
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_get_field_settings()


	/**
	 * Check if existing field key
	 *
	 * @author Darpan
	 */
	public function bm_check_if_existing_field_key() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$post         = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$is_existing  = 1;
		$original_key = '';
		$data         = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$field_key    = isset( $post['field_key'] ) ? $post['field_key'] : '';
			$field_id     = isset( $post['field_id'] ) ? $post['field_id'] : '';
			$original_key = $dbhandler->get_value( 'FIELDS', 'field_key', $field_id, 'id' );
			$id           = $dbhandler->get_value( 'FIELDS', 'id', $field_key, 'field_key' );

			if ( empty( $id ) ) {
				$is_existing = 0;
			} elseif ( ! empty( $id ) && ( $field_key == $original_key ) ) {
				$is_existing = 0;
			}

			$data['status'] = true;
		} //end if

		$data['is_existing']  = $is_existing;
		$data['original_key'] = $original_key;

		echo wp_json_encode( $data );
		die;
	}//end bm_check_if_existing_field_key()


	/**
	 * Set Price in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_serice_price() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_price = isset( $post['old_default_price'] ) ? $post['old_default_price'] : '';
			$default_price     = isset( $post['default_price'] ) ? $post['default_price'] : '';
			$price             = isset( $post['price'] ) ? $post['price'] : '';
			$date              = isset( $post['date'] ) ? $post['date'] : '';

			if ( ! empty( $id ) ) {
				if ( ! empty( $price ) && ! empty( $date ) ) {
					if ( ! empty( $old_default_price ) && ! empty( $default_price ) ) {
						if ( $old_default_price != $default_price ) {
							$update_data = array(
								'default_price'       => $default_price,
								'variable_svc_prices' => null,
								'variable_svc_price_modules' => null,
								'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);
							$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
						}
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
							$index = (int) array_search( $date, $dates );
							if ( isset( $service->default_price ) && $price != $service->default_price ) {
								$variable_data['price'][ $index ] = $price;
							} elseif ( count( $variable_data['date'] ) == 1 && count( $variable_data['price'] ) == 1 ) {
									unset( $variable_data['date'] );
									unset( $variable_data['price'] );
							} else {
								unset( $variable_data['date'][ $index ] );
								unset( $variable_data['price'][ $index ] );
							}
						} elseif ( ! empty( $dates ) && isset( $service->default_price ) && $price != $service->default_price ) {
								$date_keys  = array_keys( $dates );
								$last_index = (int) end( $date_keys );

								$variable_data['date'][ ( $last_index + 1 ) ]  = $date;
								$variable_data['price'][ ( $last_index + 1 ) ] = $price;
						}
					} elseif ( isset( $service->default_price ) && $price != $service->default_price ) {
							$variable_data = array(
								'price' => array( '1' => $price ),
								'date'  => array( '1' => $date ),
							);
					} else {
						$variable_data = null;
					} //end if

					$variable_data = array( 'variable_svc_prices' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$data['status'] = true;
					}

					if ( $data['status'] == true ) {
						$variable_module = isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();

						if ( ! empty( $variable_module ) ) {
							$dates = isset( $variable_module['date'] ) ? $variable_module['date'] : array();

							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $dates, true );
								if ( count( $variable_module['date'] ) == 1 && count( $variable_module['module'] ) == 1 ) {
									$variable_module = null;
								} else {
									unset( $variable_module['date'][ $index ] );
									unset( $variable_module['module'][ $index ] );
									$variable_module = maybe_serialize( $variable_module );
								}
								$dbhandler->update_row( 'SERVICE', 'id', $id, array( 'variable_svc_price_modules' => $variable_module ), '', '%d' );
							} //end if
						} //end if
					} //end if
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_serice_price()


	/**
	 * Set Bulk Price in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_bulk_serice_price() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_price = isset( $post['old_default_price'] ) ? $post['old_default_price'] : '';
			$default_price     = isset( $post['default_price'] ) ? $post['default_price'] : '';
			$price             = isset( $post['price'] ) ? $post['price'] : '';
			$from_date         = isset( $post['from_date'] ) ? $post['from_date'] : '';
			$to_date           = isset( $post['to_date'] ) ? $post['to_date'] : '';

			$period = new DatePeriod(
				new DateTime( $from_date ),
				new DateInterval( 'P1D' ),
				new DateTime( $to_date . '+1 day' )
			);

			if ( ! empty( $id ) ) {
				if ( ! empty( $price ) && ! empty( $from_date ) && ! empty( $to_date ) && ! empty( $period ) ) {
					if ( ! empty( $old_default_price ) && ! empty( $default_price ) ) {
						if ( $old_default_price != $default_price ) {
							$update_data = array(
								'default_price'       => $default_price,
								'variable_svc_prices' => null,
								'variable_svc_price_modules' => null,
								'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);
							$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
						}
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$i     = ! empty( $dates ) ? ( (int) end( array_keys( $dates ) ) + 1 ) : 0;
						foreach ( $period as $value ) {
							$date = $value->format( 'Y-m-d' );
							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $variable_data['date'] );
								if ( isset( $service->default_price ) && $price != $service->default_price ) {
									$variable_data['price'][ $index ] = $price;
								} elseif ( count( $variable_data['date'] ) == 1 && count( $variable_data['price'] ) == 1 ) {
										unset( $variable_data['date'] );
										unset( $variable_data['price'] );
								} else {
									unset( $variable_data['date'][ $index ] );
									unset( $variable_data['price'][ $index ] );
								}
							} elseif ( isset( $service->default_price ) && $price != $service->default_price ) {
									$variable_data['date'][ $i ]  = $date;
									$variable_data['price'][ $i ] = $price;
									++$i;
							}
						} //end foreach
					} else {
						$i           = 1;
						$price_value = array();
						$date_value  = array();
						if ( $price != $service->default_price ) {
							foreach ( $period as $value ) {
								$price_value[ $i ] = $price;
								$date_value[ $i ]  = $value->format( 'Y-m-d' );
								++$i;
							}

							$variable_data = array(
								'price' => $price_value,
								'date'  => $date_value,
							);
						} else {
							$variable_data = null;
						}
					} //end if

					$variable_data = array( 'variable_svc_prices' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']         = true;
						$data['default_price']  = ! empty( $service ) && isset( $service->default_price ) ? $service->default_price : 0;
						$data['variable_price'] = ! empty( $service ) && isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();
					}

					if ( $data['status'] == true ) {
						$variable_module = isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();

						if ( ! empty( $variable_module ) ) {
							$dates = isset( $variable_module['date'] ) ? $variable_module['date'] : array();

							foreach ( $period as $value ) {
								$date = $value->format( 'Y-m-d' );
								if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
									$index = (int) array_search( $date, $dates, true );
									unset( $variable_module['date'][ $index ] );
									unset( $variable_module['module'][ $index ] );
								} //end if
							} //end foreach

							if ( ! array_filter( $variable_module ) ) {
								$variable_module = null;
							} else {
								$variable_module = maybe_serialize( $variable_module );
							} //end if

							$dbhandler->update_row( 'SERVICE', 'id', $id, array( 'variable_svc_price_modules' => $variable_module ), '', '%d' );
						} //end if
					} //end if
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_bulk_serice_price()


	/**
	 * Set Price in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_serice_price_module() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                = isset( $post['id'] ) ? $post['id'] : 0;
			$module_id         = isset( $post['module'] ) ? $post['module'] : 0;
			$date              = isset( $post['date'] ) ? $post['date'] : '';
			$old_default_price = isset( $post['old_default_price'] ) ? $post['old_default_price'] : '';
			$default_price     = isset( $post['default_price'] ) ? $post['default_price'] : '';

			if ( ! empty( $id ) ) {
				if ( ! empty( $module_id ) && ! empty( $date ) ) {
					if ( ! empty( $old_default_price ) && ! empty( $default_price ) ) {
						if ( $old_default_price != $default_price ) {
							$update_data = array(
								'default_price'       => $default_price,
								'variable_svc_prices' => null,
								'variable_svc_price_modules' => null,
								'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);
							$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
						}
					}

					$service = $dbhandler->get_row( 'SERVICE', $id );
					if ( ! empty( $service ) ) {
						$variable_data = isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();

						if ( ! empty( $variable_data ) ) {
							$dates = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $dates, true );

								$variable_data['module'][ $index ] = $module_id;
							} elseif ( ! empty( $dates ) ) {
									$last_index                                     = (int) end( array_keys( $dates ) );
									$variable_data['date'][ ( $last_index + 1 ) ]   = $date;
									$variable_data['module'][ ( $last_index + 1 ) ] = $module_id;
							}
						} else {
							$variable_data = array(
								'module' => array( '1' => $module_id ),
								'date'   => array( '1' => $date ),
							);
						} //end if

						$variable_data = array( 'variable_svc_price_modules' => $variable_data );
						$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

						if ( $service_post != false ) {
							$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
							$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
							$data['status'] = true;
						} //end if

						if ( $data['status'] == true ) {
							$variable_price = isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();

							if ( ! empty( $variable_price ) ) {
								$dates = isset( $variable_price['date'] ) ? $variable_price['date'] : array();

								if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
									$index = (int) array_search( $date, $dates, true );
									if ( count( $variable_price['date'] ) == 1 && count( $variable_price['price'] ) == 1 ) {
										$variable_price = null;
									} else {
										unset( $variable_price['date'][ $index ] );
										unset( $variable_price['price'][ $index ] );
										$variable_price = maybe_serialize( $variable_price );
									}
									$dbhandler->update_row( 'SERVICE', 'id', $id, array( 'variable_svc_prices' => $variable_price ), '', '%d' );
								} //end if
							} //end if
						} //end if
					} //end if
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_serice_price_module()


	/**
	 * Set bulk price module in service calender
	 *
	 * @author Darpan
	 */
	public function bm_set_bulk_serice_price_module() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                = isset( $post['id'] ) ? $post['id'] : 0;
			$module_id         = isset( $post['module'] ) ? $post['module'] : 0;
			$from_date         = isset( $post['from_date'] ) ? $post['from_date'] : '';
			$to_date           = isset( $post['to_date'] ) ? $post['to_date'] : '';
			$old_default_price = isset( $post['old_default_price'] ) ? $post['old_default_price'] : '';
			$default_price     = isset( $post['default_price'] ) ? $post['default_price'] : '';

			$period = new DatePeriod(
				new DateTime( $from_date ),
				new DateInterval( 'P1D' ),
				new DateTime( $to_date . '+1 day' )
			);

			if ( ! empty( $id ) ) {
				if ( ! empty( $module_id ) && ! empty( $from_date ) && ! empty( $to_date ) && ! empty( $period ) ) {
					if ( ! empty( $old_default_price ) && ! empty( $default_price ) ) {
						if ( $old_default_price != $default_price ) {
							$update_data = array(
								'default_price'       => $default_price,
								'variable_svc_prices' => null,
								'variable_svc_price_modules' => null,
								'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);
							$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
						}
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$i     = ! empty( $dates ) ? ( (int) end( array_keys( $dates ) ) + 1 ) : 0;
						foreach ( $period as $value ) {
							$date = $value->format( 'Y-m-d' );
							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $variable_data['date'], true );

								$variable_data['module'][ $index ] = $module_id;
							} elseif ( ! empty( $dates ) ) {
									$variable_data['date'][ $i ]   = $date;
									$variable_data['module'][ $i ] = $module_id;
									++$i;
							}
						} //end foreach
					} else {
						$i            = 1;
						$module_value = array();
						$date_value   = array();
						foreach ( $period as $value ) {
							$module_value[ $i ] = $module_id;
							$date_value[ $i ]   = $value->format( 'Y-m-d' );
							++$i;
						}

						$variable_data = array(
							'module' => $module_value,
							'date'   => $date_value,
						);
					} //end if

					$variable_data = array( 'variable_svc_price_modules' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$data['status'] = true;
					} //end if

					if ( $data['status'] == true ) {
						$variable_price = isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();

						if ( ! empty( $variable_price ) ) {
							$dates = isset( $variable_price['date'] ) ? $variable_price['date'] : array();

							foreach ( $period as $value ) {
								$date = $value->format( 'Y-m-d' );
								if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
									$index = (int) array_search( $date, $dates, true );
									unset( $variable_price['date'][ $index ] );
									unset( $variable_price['price'][ $index ] );
								} //end if
							} //end foreach

							if ( ! array_filter( $variable_price ) ) {
								$variable_price = null;
							} else {
								$variable_price = maybe_serialize( $variable_price );
							} //end if

							$dbhandler->update_row( 'SERVICE', 'id', $id, array( 'variable_svc_prices' => $variable_price ), '', '%d' );
						} //end if
					} //end if
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_bulk_serice_price_module()


	/**
	 * Set Stopsales in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_serice_stopsales() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                    = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_stopsales = isset( $post['old_default_stopsales'] ) ? $post['old_default_stopsales'] : '';
			$default_stopsales     = isset( $post['default_stopsales'] ) ? $post['default_stopsales'] : '';
			$stopsales             = isset( $post['stopsales'] ) ? $post['stopsales'] : '';
			$date                  = isset( $post['date'] ) ? $post['date'] : '';

			if ( ! empty( $id ) ) {
				if ( ! empty( $date ) ) {
					if ( $old_default_stopsales != $default_stopsales ) {
						$update_data = array(
							'default_stopsales'  => $default_stopsales,
							'variable_stopsales' => null,
							'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);
						$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates          = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$excluded_dates = isset( $variable_data['exclude_dates'] ) ? $variable_data['exclude_dates'] : array();
						if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
							$index = (int) array_search( $date, $variable_data['date'] );
							if ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales && ! empty( $stopsales ) ) {
								$variable_data['stopsales'][ $index ] = $stopsales;
							} else {
								if ( count( $variable_data['date'] ) == 1 && count( $variable_data['stopsales'] ) == 1 ) {
									unset( $variable_data['date'] );
									unset( $variable_data['stopsales'] );
								} else {
									unset( $variable_data['date'][ $index ] );
									unset( $variable_data['stopsales'][ $index ] );
								}

								if ( empty( $stopsales ) && ! empty( $default_stopsales ) ) {
									if ( ! empty( $excluded_dates ) ) {
										$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
										$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
									} else {
										$variable_data['exclude_dates'][1] = $date;
									}
								}
							}
						} elseif ( ! empty( $excluded_dates ) && in_array( $date, $excluded_dates, true ) ) {
							if ( ! empty( $stopsales ) ) {
								$index = (int) array_search( $date, $variable_data['exclude_dates'] );
								if ( count( $variable_data['exclude_dates'] ) == 1 ) {
									unset( $variable_data['exclude_dates'] );
								} else {
									unset( $variable_data['exclude_dates'][ $index ] );
								}

								if ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales ) {
									if ( ! empty( $dates ) ) {
										$lastindex                                        = (int) end( array_keys( $variable_data['date'] ) );
										$variable_data['date'][ ( $lastindex + 1 ) ]      = $date;
										$variable_data['stopsales'][ ( $lastindex + 1 ) ] = $stopsales;
									} else {
										$variable_data['stopsales'] = array( '1' => $stopsales );
										$variable_data['date']      = array( '1' => $date );
									}
								}
							}
						} elseif ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales && ! empty( $stopsales ) ) {
							if ( isset( $dates ) && ! empty( $dates ) ) {
								$last_index                                        = (int) end( array_keys( $variable_data['date'] ) );
								$variable_data['date'][ ( $last_index + 1 ) ]      = $date;
								$variable_data['stopsales'][ ( $last_index + 1 ) ] = $stopsales;
							} else {
								$variable_data['stopsales'] = array( '1' => $stopsales );
								$variable_data['date']      = array( '1' => $date );
							}
						} elseif ( empty( $stopsales ) && ! empty( $default_stopsales ) ) {
							if ( isset( $excluded_dates ) && ! empty( $excluded_dates ) ) {
								$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
								$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
							} else {
								$variable_data['exclude_dates'][1] = $date;
							}
						} //end if
					} elseif ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales && ! empty( $stopsales ) ) {
							$variable_data['stopsales'] = array( '1' => $stopsales );
							$variable_data['date']      = array( '1' => $date );
					} elseif ( empty( $stopsales ) && ! empty( $default_stopsales ) ) {
							$variable_data['exclude_dates'][1] = $date;
					} else {
						$variable_data = null;
					} //end if

					$variable_data = array( 'variable_stopsales' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                    = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']             = true;
						$data['default_stopsales']  = ! empty( $service ) && isset( $service->default_stopsales ) ? $service->default_stopsales : 0;
						$data['variable_stopsales'] = ! empty( $service ) && isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_serice_stopsales()


	/**
	 * Set Saleswitch in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_service_saleswitch() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                     = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_saleswitch = isset( $post['old_default_saleswitch'] ) ? $post['old_default_saleswitch'] : '';
			$default_saleswitch     = isset( $post['default_saleswitch'] ) ? $post['default_saleswitch'] : '';
			$saleswitch             = isset( $post['saleswitch'] ) ? $post['saleswitch'] : '';
			$date                   = isset( $post['date'] ) ? $post['date'] : '';

			if ( ! empty( $id ) ) {
				if ( ! empty( $date ) ) {
					if ( $old_default_saleswitch != $default_saleswitch ) {
						$update_data = array(
							'default_saleswitch'  => $default_saleswitch,
							'variable_saleswitch' => null,
							'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);
						$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_saleswitch ) ? maybe_unserialize( $service->variable_saleswitch ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates          = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$excluded_dates = isset( $variable_data['exclude_dates'] ) ? $variable_data['exclude_dates'] : array();
						if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
							$index = (int) array_search( $date, $variable_data['date'] );
							if ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch && ! empty( $saleswitch ) ) {
								$variable_data['saleswitch'][ $index ] = $saleswitch;
							} else {
								if ( count( $variable_data['date'] ) == 1 && count( $variable_data['saleswitch'] ) == 1 ) {
									unset( $variable_data['date'] );
									unset( $variable_data['saleswitch'] );
								} else {
									unset( $variable_data['date'][ $index ] );
									unset( $variable_data['saleswitch'][ $index ] );
								}

								if ( empty( $saleswitch ) && ! empty( $default_saleswitch ) ) {
									if ( ! empty( $excluded_dates ) ) {
										$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
										$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
									} else {
										$variable_data['exclude_dates'][1] = $date;
									}
								}
							}
						} elseif ( ! empty( $excluded_dates ) && in_array( $date, $excluded_dates, true ) ) {
							if ( ! empty( $saleswitch ) ) {
								$index = (int) array_search( $date, $variable_data['exclude_dates'] );
								if ( count( $variable_data['exclude_dates'] ) == 1 ) {
									unset( $variable_data['exclude_dates'] );
								} else {
									unset( $variable_data['exclude_dates'][ $index ] );
								}

								if ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch ) {
									if ( ! empty( $dates ) ) {
										$lastindex                                   = (int) end( array_keys( $variable_data['date'] ) );
										$variable_data['date'][ ( $lastindex + 1 ) ] = $date;
										$variable_data['saleswitch'][ ( $lastindex + 1 ) ] = $saleswitch;
									} else {
										$variable_data['saleswitch'] = array( '1' => $saleswitch );
										$variable_data['date']       = array( '1' => $date );
									}
								}
							}
						} elseif ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch && ! empty( $saleswitch ) ) {
							if ( isset( $dates ) && ! empty( $dates ) ) {
								$last_index                                   = (int) end( array_keys( $variable_data['date'] ) );
								$variable_data['date'][ ( $last_index + 1 ) ] = $date;
								$variable_data['saleswitch'][ ( $last_index + 1 ) ] = $saleswitch;
							} else {
								$variable_data['saleswitch'] = array( '1' => $saleswitch );
								$variable_data['date']       = array( '1' => $date );
							}
						} elseif ( empty( $saleswitch ) && ! empty( $default_saleswitch ) ) {
							if ( isset( $excluded_dates ) && ! empty( $excluded_dates ) ) {
								$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
								$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
							} else {
								$variable_data['exclude_dates'][1] = $date;
							}
						} //end if
					} elseif ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch && ! empty( $saleswitch ) ) {
							$variable_data['saleswitch'] = array( '1' => $saleswitch );
							$variable_data['date']       = array( '1' => $date );
					} elseif ( empty( $saleswitch ) && ! empty( $default_saleswitch ) ) {
							$variable_data['exclude_dates'][1] = $date;
					} else {
						$variable_data = null;
					} //end if

					$variable_data = array( 'variable_saleswitch' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                     = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']              = true;
						$data['default_saleswitch']  = ! empty( $service ) && isset( $service->default_saleswitch ) ? $service->default_saleswitch : 0;
						$data['variable_saleswitch'] = ! empty( $service ) && isset( $service->variable_saleswitch ) ? maybe_unserialize( $service->variable_saleswitch ) : array();
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_service_saleswitch()


	/**
	 * Set Bulk Stopsales in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_bulk_serice_stopsales() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                    = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_stopsales = isset( $post['old_default_stopsales'] ) ? $post['old_default_stopsales'] : '';
			$default_stopsales     = isset( $post['default_stopsales'] ) ? $post['default_stopsales'] : '';
			$stopsales             = isset( $post['stopsales'] ) ? $post['stopsales'] : '';
			$from_date             = isset( $post['from_date'] ) ? $post['from_date'] : '';
			$to_date               = isset( $post['to_date'] ) ? $post['to_date'] : '';

			$period = new DatePeriod(
				new DateTime( $from_date ),
				new DateInterval( 'P1D' ),
				new DateTime( $to_date . '+1 day' )
			);

			if ( ! empty( $id ) ) {
				if ( ! empty( $from_date ) && ! empty( $to_date ) && ! empty( $period ) ) {
					if ( $old_default_stopsales != $default_stopsales ) {
						$update_data = array(
							'default_stopsales'  => $default_stopsales,
							'variable_stopsales' => null,
							'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);
						$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates          = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$excluded_dates = isset( $variable_data['exclude_dates'] ) ? $variable_data['exclude_dates'] : array();
						foreach ( $period as $value ) {
							$date = $value->format( 'Y-m-d' );
							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $variable_data['date'] );
								if ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales && ! empty( $stopsales ) ) {
									$variable_data['stopsales'][ $index ] = $stopsales;
								} else {
									if ( count( $variable_data['date'] ) == 1 && count( $variable_data['stopsales'] ) == 1 ) {
										unset( $variable_data['date'] );
										unset( $variable_data['stopsales'] );
									} else {
										unset( $variable_data['date'][ $index ] );
										unset( $variable_data['stopsales'][ $index ] );
									}

									if ( empty( $stopsales ) && ! empty( $default_stopsales ) ) {
										if ( isset( $variable_data['exclude_dates'] ) && ! empty( $variable_data['exclude_dates'] ) ) {
											$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
											$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
										} else {
											$variable_data['exclude_dates'][1] = $date;
										}
									}
								}
							} elseif ( ! empty( $excluded_dates ) && in_array( $date, $excluded_dates, true ) ) {
								if ( ! empty( $stopsales ) ) {
									$index = (int) array_search( $date, $variable_data['exclude_dates'] );
									if ( count( $variable_data['exclude_dates'] ) == 1 ) {
										unset( $variable_data['exclude_dates'] );
									} else {
										unset( $variable_data['exclude_dates'][ $index ] );
									}

									if ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales ) {
										if ( isset( $variable_data['date'] ) && ! empty( $variable_data['date'] ) ) {
											$lastindex                                        = (int) end( array_keys( $variable_data['date'] ) );
											$variable_data['date'][ ( $lastindex + 1 ) ]      = $date;
											$variable_data['stopsales'][ ( $lastindex + 1 ) ] = $stopsales;
										} else {
											$variable_data['stopsales'] = array( '1' => $stopsales );
											$variable_data['date']      = array( '1' => $date );
										}
									}
								}
							} elseif ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales && ! empty( $stopsales ) ) {
								if ( isset( $variable_data['date'] ) && ! empty( $variable_data['date'] ) ) {
									$lastindex                                        = (int) end( array_keys( $variable_data['date'] ) );
									$variable_data['date'][ ( $lastindex + 1 ) ]      = $date;
									$variable_data['stopsales'][ ( $lastindex + 1 ) ] = $stopsales;
								} else {
									$variable_data['stopsales'] = array( '1' => $stopsales );
									$variable_data['date']      = array( '1' => $date );
								}
							} elseif ( empty( $stopsales ) && ! empty( $default_stopsales ) ) {
								if ( isset( $variable_data['exclude_dates'] ) && ! empty( $variable_data['exclude_dates'] ) ) {
									$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
									$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
								} else {
									$variable_data['exclude_dates'][1] = $date;
								}
							} //end if
						} //end foreach
					} else {
						$i               = 1;
						$stopsales_value = array();
						$date_value      = array();
						if ( isset( $service->default_stopsales ) && $stopsales != $service->default_stopsales && ! empty( $stopsales ) ) {
							foreach ( $period as $value ) {
								$stopsales_value[ $i ] = $stopsales;
								$date_value[ $i ]      = $value->format( 'Y-m-d' );
								++$i;
							}

							$variable_data['stopsales'] = $stopsales_value;
							$variable_data['date']      = $date_value;
						} elseif ( empty( $stopsales ) && ! empty( $default_stopsales ) ) {
							foreach ( $period as $value ) {
								$date_value[ $i ] = $value->format( 'Y-m-d' );
								++$i;
							}

								$variable_data['exclude_dates'] = $date_value;
						} else {
							$variable_data = null;
						} //end if
					} //end if

					$variable_data = array( 'variable_stopsales' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                    = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']             = true;
						$data['default_stopsales']  = ! empty( $service ) && isset( $service->default_stopsales ) ? $service->default_stopsales : 0;
						$data['variable_stopsales'] = ! empty( $service ) && isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_bulk_serice_stopsales()


	/**
	 * Set Bulk Saleswitch in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_bulk_service_saleswitch() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                     = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_saleswitch = isset( $post['old_default_saleswitch'] ) ? $post['old_default_saleswitch'] : '';
			$default_saleswitch     = isset( $post['default_saleswitch'] ) ? $post['default_saleswitch'] : '';
			$saleswitch             = isset( $post['saleswitch'] ) ? $post['saleswitch'] : '';
			$from_date              = isset( $post['from_date'] ) ? $post['from_date'] : '';
			$to_date                = isset( $post['to_date'] ) ? $post['to_date'] : '';

			$period = new DatePeriod(
				new DateTime( $from_date ),
				new DateInterval( 'P1D' ),
				new DateTime( $to_date . '+1 day' )
			);

			if ( ! empty( $id ) ) {
				if ( ! empty( $from_date ) && ! empty( $to_date ) && ! empty( $period ) ) {
					if ( $old_default_saleswitch != $default_saleswitch ) {
						$update_data = array(
							'default_saleswitch'  => $default_saleswitch,
							'variable_saleswitch' => null,
							'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);
						$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
					}

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_saleswitch ) ? maybe_unserialize( $service->variable_saleswitch ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates          = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$excluded_dates = isset( $variable_data['exclude_dates'] ) ? $variable_data['exclude_dates'] : array();
						foreach ( $period as $value ) {
							$date = $value->format( 'Y-m-d' );
							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $variable_data['date'] );
								if ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch && ! empty( $saleswitch ) ) {
									$variable_data['saleswitch'][ $index ] = $saleswitch;
								} else {
									if ( count( $variable_data['date'] ) == 1 && count( $variable_data['saleswitch'] ) == 1 ) {
										unset( $variable_data['date'] );
										unset( $variable_data['saleswitch'] );
									} else {
										unset( $variable_data['date'][ $index ] );
										unset( $variable_data['saleswitch'][ $index ] );
									}

									if ( empty( $saleswitch ) && ! empty( $default_saleswitch ) ) {
										if ( isset( $variable_data['exclude_dates'] ) && ! empty( $variable_data['exclude_dates'] ) ) {
											$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
											$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
										} else {
											$variable_data['exclude_dates'][1] = $date;
										}
									}
								}
							} elseif ( ! empty( $excluded_dates ) && in_array( $date, $excluded_dates, true ) ) {
								if ( ! empty( $saleswitch ) ) {
									$index = (int) array_search( $date, $variable_data['exclude_dates'] );
									if ( count( $variable_data['exclude_dates'] ) == 1 ) {
										unset( $variable_data['exclude_dates'] );
									} else {
										unset( $variable_data['exclude_dates'][ $index ] );
									}

									if ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch ) {
										if ( isset( $variable_data['date'] ) && ! empty( $variable_data['date'] ) ) {
											$lastindex                                   = (int) end( array_keys( $variable_data['date'] ) );
											$variable_data['date'][ ( $lastindex + 1 ) ] = $date;
											$variable_data['saleswitch'][ ( $lastindex + 1 ) ] = $saleswitch;
										} else {
											$variable_data['saleswitch'] = array( '1' => $saleswitch );
											$variable_data['date']       = array( '1' => $date );
										}
									}
								}
							} elseif ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch && ! empty( $saleswitch ) ) {
								if ( isset( $variable_data['date'] ) && ! empty( $variable_data['date'] ) ) {
									$lastindex                                   = (int) end( array_keys( $variable_data['date'] ) );
									$variable_data['date'][ ( $lastindex + 1 ) ] = $date;
									$variable_data['saleswitch'][ ( $lastindex + 1 ) ] = $saleswitch;
								} else {
									$variable_data['saleswitch'] = array( '1' => $saleswitch );
									$variable_data['date']       = array( '1' => $date );
								}
							} elseif ( empty( $saleswitch ) && ! empty( $default_saleswitch ) ) {
								if ( isset( $variable_data['exclude_dates'] ) && ! empty( $variable_data['exclude_dates'] ) ) {
									$lastindex = (int) end( array_keys( $variable_data['exclude_dates'] ) );
									$variable_data['exclude_dates'][ ( $lastindex + 1 ) ] = $date;
								} else {
									$variable_data['exclude_dates'][1] = $date;
								}
							} //end if
						} //end foreach
					} else {
						$i                = 1;
						$saleswitch_value = array();
						$date_value       = array();
						if ( isset( $service->default_saleswitch ) && $saleswitch != $service->default_saleswitch && ! empty( $saleswitch ) ) {
							foreach ( $period as $value ) {
								$saleswitch_value[ $i ] = $saleswitch;
								$date_value[ $i ]       = $value->format( 'Y-m-d' );
								++$i;
							}

							$variable_data['saleswitch'] = $saleswitch_value;
							$variable_data['date']       = $date_value;
						} elseif ( empty( $saleswitch ) && ! empty( $default_saleswitch ) ) {
							foreach ( $period as $value ) {
								$date_value[ $i ] = $value->format( 'Y-m-d' );
								++$i;
							}

								$variable_data['exclude_dates'] = $date_value;
						} else {
							$variable_data = null;
						} //end if
					} //end if

					$variable_data = array( 'variable_saleswitch' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                     = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']              = true;
						$data['default_saleswitch']  = ! empty( $service ) && isset( $service->default_saleswitch ) ? $service->default_saleswitch : 0;
						$data['variable_saleswitch'] = ! empty( $service ) && isset( $service->variable_saleswitch ) ? maybe_unserialize( $service->variable_saleswitch ) : array();
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_bulk_service_saleswitch()


	/**
	 * Set Maximum Capacity in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_serice_max_cap() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                  = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_max_cap = isset( $post['old_default_max_cap'] ) ? $post['old_default_max_cap'] : '';
			$default_max_cap     = isset( $post['default_max_cap'] ) ? $post['default_max_cap'] : '';
			$capacity            = isset( $post['capacity'] ) ? $post['capacity'] : '';
			$date                = isset( $post['date'] ) ? $post['date'] : '';
			$time_row            = $dbhandler->get_row( 'TIME', $id );

			if ( ! empty( $id ) ) {
				if ( ! empty( $capacity ) && ! empty( $date ) ) {
					if ( ! empty( $old_default_max_cap ) && ! empty( $default_max_cap ) ) {
						if ( $old_default_max_cap != $default_max_cap ) {
							$update_data = array(
								'default_max_cap'    => $default_max_cap,
								'variable_max_cap'   => null,
								'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);
							$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );

							if ( ! empty( $time_row ) ) {
								$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
								if ( ! empty( $time_slots ) ) {
									$max_slot_count = isset( $time_slots['max_cap'] ) ? count( $time_slots['max_cap'] ) : 0;

									if ( ! empty( $max_slot_count ) ) {
										for ( $i = 1; $i <= $max_slot_count; $i++ ) {
											$time_slots['max_cap'][ $i ] = $default_max_cap;
										}
									}

									$min_slot_count = isset( $time_slots['min_cap'] ) ? count( $time_slots['min_cap'] ) : 0;

									if ( ! empty( $min_slot_count ) ) {
										for ( $i = 1; $i <= $min_slot_count; $i++ ) {
											if ( $time_slots['min_cap'][ $i ] > $default_max_cap ) {
												$time_slots['min_cap'][ $i ] = 1;
											}
										}
									}

									$update_data = array(
										'time_slots'      => maybe_serialize( $time_slots ),
										'time_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
									);
									$dbhandler->update_row( 'TIME', 'id', $id, $update_data, '', '%d' );
								} //end if
							} //end if
						} //end if
					} //end if

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_max_cap ) ? maybe_unserialize( $service->variable_max_cap ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
							$index = (int) array_search( $date, $dates );
							if ( isset( $service->default_max_cap ) && $capacity != $service->default_max_cap ) {
								$variable_data['capacity'][ $index ] = $capacity;
							} elseif ( count( $variable_data['date'] ) == 1 && count( $variable_data['capacity'] ) == 1 ) {
									unset( $variable_data['date'] );
									unset( $variable_data['capacity'] );
							} else {
								unset( $variable_data['date'][ $index ] );
								unset( $variable_data['capacity'][ $index ] );
							}
						} elseif ( ! empty( $dates ) && isset( $service->default_max_cap ) && $capacity != $service->default_max_cap ) {
								$last_index                                       = (int) end( array_keys( $dates ) );
								$variable_data['date'][ ( $last_index + 1 ) ]     = $date;
								$variable_data['capacity'][ ( $last_index + 1 ) ] = $capacity;
						}
					} elseif ( $capacity != $service->default_max_cap ) {
							$variable_data = array(
								'capacity' => array( '1' => $capacity ),
								'date'     => array( '1' => $date ),
							);
					} else {
						$variable_data = null;
					} //end if

					$variable_data = array( 'variable_max_cap' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                  = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']           = true;
						$data['default_max_cap']  = ! empty( $service ) && isset( $service->default_max_cap ) ? $service->default_max_cap : 0;
						$data['variable_max_cap'] = ! empty( $service ) && isset( $service->variable_max_cap ) ? maybe_unserialize( $service->variable_max_cap ) : array();
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_serice_max_cap()


	/**
	 * Set Bulk Maximum Capacity in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_bulk_serice_max_cap() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                  = isset( $post['id'] ) ? $post['id'] : '';
			$old_default_max_cap = isset( $post['old_default_max_cap'] ) ? $post['old_default_max_cap'] : '';
			$default_max_cap     = isset( $post['default_max_cap'] ) ? $post['default_max_cap'] : '';
			$capacity            = isset( $post['capacity'] ) ? $post['capacity'] : '';
			$from_date           = isset( $post['from_date'] ) ? $post['from_date'] : '';
			$to_date             = isset( $post['to_date'] ) ? $post['to_date'] : '';
			$time_row            = $dbhandler->get_row( 'TIME', $id );

			$period = new DatePeriod(
				new DateTime( $from_date ),
				new DateInterval( 'P1D' ),
				new DateTime( $to_date . '+1 day' )
			);

			if ( ! empty( $id ) ) {
				if ( ! empty( $capacity ) && ! empty( $from_date ) && ! empty( $to_date ) && ! empty( $period ) ) {
					if ( ! empty( $old_default_max_cap ) && ! empty( $default_max_cap ) ) {
						if ( $old_default_max_cap != $default_max_cap ) {
							$update_data = array(
								'default_max_cap'    => $default_max_cap,
								'variable_max_cap'   => null,
								'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);
							$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
						}

						if ( ! empty( $time_row ) ) {
							$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
							if ( ! empty( $time_slots ) ) {
								$max_slot_count = isset( $time_slots['max_cap'] ) ? count( $time_slots['max_cap'] ) : 0;

								if ( ! empty( $max_slot_count ) ) {
									for ( $i = 1; $i <= $max_slot_count; $i++ ) {
										$time_slots['max_cap'][ $i ] = $default_max_cap;
									}
								}

								$min_slot_count = isset( $time_slots['min_cap'] ) ? count( $time_slots['min_cap'] ) : 0;

								if ( ! empty( $min_slot_count ) ) {
									for ( $i = 1; $i <= $min_slot_count; $i++ ) {
										if ( $time_slots['min_cap'][ $i ] > $default_max_cap ) {
											$time_slots['min_cap'][ $i ] = 1;
										}
									}
								}

								$update_data = array(
									'time_slots'      => maybe_serialize( $time_slots ),
									'time_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
								);
								$dbhandler->update_row( 'TIME', 'id', $id, $update_data, '', '%d' );
							} //end if
						} //end if
					} //end if

					$service       = $dbhandler->get_row( 'SERVICE', $id );
					$variable_data = ! empty( $service ) && isset( $service->variable_max_cap ) ? maybe_unserialize( $service->variable_max_cap ) : array();

					if ( ! empty( $variable_data ) ) {
						$dates = isset( $variable_data['date'] ) ? $variable_data['date'] : array();
						$i     = ( (int) end( array_keys( $dates ) ) + 1 );
						foreach ( $period as $value ) {
							$date = $value->format( 'Y-m-d' );
							if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
								$index = (int) array_search( $date, $variable_data['date'] );
								if ( isset( $service->default_max_cap ) && $capacity != $service->default_max_cap ) {
									$variable_data['capacity'][ $index ] = $capacity;
								} elseif ( count( $variable_data['date'] ) == 1 && count( $variable_data['capacity'] ) == 1 ) {
										unset( $variable_data['date'] );
										unset( $variable_data['capacity'] );
								} else {
									unset( $variable_data['date'][ $index ] );
									unset( $variable_data['capacity'][ $index ] );
								}
							} elseif ( isset( $service->default_max_cap ) && $capacity != $service->default_max_cap ) {
									$variable_data['date'][ $i ]     = $date;
									$variable_data['capacity'][ $i ] = $capacity;
									++$i;
							}
						} //end foreach
					} else {
						$i              = 1;
						$capacity_value = array();
						$date_value     = array();
						if ( isset( $service->default_max_cap ) && $capacity != $service->default_max_cap ) {
							foreach ( $period as $value ) {
								$capacity_value[ $i ] = $capacity;
								$date_value[ $i ]     = $value->format( 'Y-m-d' );
								++$i;
							}

							$variable_data = array(
								'capacity' => $capacity_value,
								'date'     => $date_value,
							);
						} else {
							$variable_data = null;
						}
					} //end if

					$variable_data = array( 'variable_max_cap' => $variable_data );
					$service_post  = $bmrequests->sanitize_request( $variable_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$service_post['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                  = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']           = true;
						$data['default_max_cap']  = ! empty( $service ) && isset( $service->default_max_cap ) ? $service->default_max_cap : 0;
						$data['variable_max_cap'] = ! empty( $service ) && isset( $service->variable_max_cap ) ? maybe_unserialize( $service->variable_max_cap ) : array();
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_bulk_serice_max_cap()


	/**
	 * Set time slot in Service Calender
	 *
	 * @author Darpan
	 */
	public function bm_set_variable_time_slot() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id                   = isset( $post['id'] ) ? $post['id'] : '';
			$old_total_time_slots = isset( $post['old_total_time_slots'] ) ? $post['old_total_time_slots'] : '';
			$service_duration     = isset( $post['service_duration'] ) ? $post['service_duration'] : '';
			$service_operation    = isset( $post['service_operation'] ) ? $post['service_operation'] : '';
			$old_default_max_cap  = isset( $post['old_default_max_cap'] ) ? $post['old_default_max_cap'] : '';
			$default_max_cap      = isset( $post['default_max_cap'] ) ? $post['default_max_cap'] : '';
			$total_time_slots     = isset( $post['total_time_slots'] ) ? $post['total_time_slots'] : '';
			$default_slot_data    = isset( $post['default_time_slots'] ) ? $post['default_time_slots'] : '';
			$slot_data            = isset( $post['time_slots_data'] ) ? $post['time_slots_data'] : '';
			$date                 = isset( $post['date'] ) ? $post['date'] : '';

			if ( ! empty( $id ) ) {
				if ( ! empty( $old_default_max_cap ) && ! empty( $default_max_cap ) && $old_default_max_cap != $default_max_cap ) {
					$cap_data = array(
						'default_max_cap'    => $default_max_cap,
						'service_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
					);

					$time_row = $dbhandler->get_row( 'TIME', $id );

					if ( ! empty( $time_row ) ) {
						$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

						$max_cap_count = isset( $time_slots['max_cap'] ) ? count( $time_slots['max_cap'] ) : 0;

						if ( ! empty( $max_cap_count ) ) {
							for ( $i = 1; $i <= $max_cap_count; $i++ ) {
								$time_slots['max_cap'][ $i ] = $default_max_cap;
							}
						}

						$min_cap_count = isset( $time_slots['min_cap'] ) ? count( $time_slots['min_cap'] ) : 0;

						if ( ! empty( $min_cap_count ) ) {
							for ( $i = 1; $i <= $min_cap_count; $i++ ) {
								if ( $time_slots['min_cap'][ $i > $default_max_cap ] ) {
									$time_slots['min_cap'][ $i ] = 1;
								}
							}
						}

						$time_data = array(
							'time_slots'      => maybe_serialize( $time_slots ),
							'time_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$dbhandler->update_row( 'TIME', 'service_id', $id, $time_data, '', '%d' );
					} //end if

					$dbhandler->update_row( 'SERVICE', 'id', $id, $cap_data, '', '%d' );
				} //end if

				if ( ! empty( $slot_data ) && ! empty( $date ) ) {
					if ( ! empty( $old_total_time_slots ) && ! empty( $total_time_slots ) && $old_total_time_slots != $total_time_slots ) {
						$update_data = array(
							'service_duration'    => $service_duration,
							'service_operation'   => $service_operation,
							'default_max_cap'     => $default_max_cap,
							'variable_time_slots' => null,
							'service_updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$time_row = $dbhandler->get_row( 'TIME', $id );

						if ( ! empty( $time_row ) ) {
							$dbhandler->remove_row( 'TIME', 'service_id', $id, '%d' );

							if ( ! empty( $default_slot_data ) ) {
								$auto_time = array( 'auto_time' => $default_slot_data['autoselect_time'] );
								unset( $default_slot_data['autoselect_time'] );

								$add_time_data = array(
									'service_id'   => $id,
									'total_slots'  => $total_time_slots,
									'time_slots'   => $default_slot_data,
									'time_options' => $auto_time,
								);

								$time_post = $bmrequests->sanitize_request( $add_time_data, 'TIME' );

								if ( $time_post != false ) {
									$time_post['time_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
									$time_id                      = $dbhandler->insert_row( 'TIME', $time_post );
								}
							}
						} //end if

						$dbhandler->update_row( 'SERVICE', 'id', $id, $update_data, '', '%d' );
					} //end if

					$service            = $dbhandler->get_row( 'SERVICE', $id );
					$variable_slot_data = ! empty( $service ) && isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();

					if ( ! empty( $variable_slot_data ) ) {
						$dates = wp_list_pluck( $variable_slot_data, 'date' );
						if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
							$index                        = (int) array_search( $date, $dates );
							$slot_data[ $index ]['date']  = $date;
							$variable_slot_data[ $index ] = $slot_data[ $index ];
						} elseif ( ! empty( $dates ) ) {
								$max_index                                = (int) max( array_keys( $dates ) );
								$slot_data[ ( $max_index + 1 ) ]['date']  = $date;
								$variable_slot_data[ ( $max_index + 1 ) ] = $slot_data[ ( $max_index + 1 ) ];
						}
					} else {
						$slot_data[1]['date']  = $date;
						$variable_slot_data[1] = $slot_data[1];
					}

					$variable_slot_data = array( 'variable_time_slots' => $variable_slot_data );
					$service_post       = $bmrequests->sanitize_request( $variable_slot_data, 'SERVICE', $exclude );

					if ( $service_post != false ) {
						$dbhandler->update_row( 'SERVICE', 'id', $id, $service_post, '', '%d' );
						$service                    = $dbhandler->get_row( 'SERVICE', $id );
						$data['status']             = true;
						$data['default_max_cap']    = ! empty( $service ) && isset( $service->default_max_cap ) ? $service->default_max_cap : 0;
						$data['variable_slot_data'] = ! empty( $service ) && isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
						$data['total_time_slots']   = $total_time_slots;
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_set_variable_time_slot()


	/**
	 * Save field type and setting
	 *
	 * @author Darpan
	 */
	public function bm_save_field_and_setting() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$identifier = 'FIELDS';
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		$exclude  = array(
			'_wpnonce',
			'_wp_http_referer',
			'ajax-nonce',
		);
		$post     = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$response = array();

		if ( $post !== false && $post !== null ) {
			$id   = isset( $post['id'] ) ? $post['id'] : '';
			$data = isset( $post['formdata'] ) ? $post['formdata'] : array();

			$common_data = isset( $data['common_data'] ) ? $data['common_data'] : array();
			$conditional = isset( $data['conditional'] ) ? $data['conditional'] : array();

			if ( ! empty( $common_data ) && ! empty( $conditional ) ) {
				$type = isset( $common_data['field_type'] ) ? $common_data['field_type'] : '';

				! isset( $common_data['is_required'] ) ? $common_data['is_required'] = 0 : $common_data['is_required'] = 1;
				! isset( $common_data['is_editable'] ) ? $common_data['is_editable'] = 0 : $common_data['is_editable'] = 1;

				if ( isset( $common_data['field_name'] ) ) {
					$common_data['field_name'] = $bmrequests->bm_create_slug( $common_data['field_name'], '_' );
				}

				if ( $common_data['field_key'] == '' ) {
					if ( $bmrequests->bm_fetch_default_key_type( $type ) ) {
						$common_data['field_key'] = $bmrequests->bm_fetch_field_key( $common_data['ordering'] );
					}
				}

				if ( $type == 'email' ) {
					$conditional['field_options']['is_main_email'] = ! isset( $conditional['field_options']['is_main_email'] ) ? 0 : 1;
				}

				if ( $type == 'select' || $type == 'checkbox' ) {
					$conditional['field_options']['is_multiple'] = ! isset( $conditional['field_options']['is_multiple'] ) ? 0 : 1;
				}

				if ( $type == 'tel' ) {
					$conditional['field_options']['show_intl_code'] = ! isset( $conditional['field_options']['show_intl_code'] ) ? 0 : 1;
				}

				if ( $type != 'file' && $type != 'checkbox' && $type != 'radio' && $type != 'reset' && $type != 'button' && $type != 'submit' && $type != 'hidden' && $type != 'color' && $type != 'range' ) {
					$conditional['field_options']['autocomplete'] = ! isset( $conditional['field_options']['autocomplete'] ) ? 0 : 1;
				}

				if ( $type != 'button' && $type != 'submit' && $type != 'hidden' ) {
					$conditional['field_options']['is_visible'] = ! isset( $conditional['field_options']['is_visible'] ) ? 0 : 1;
				}

				$data      = array_merge( $common_data, $conditional );
				$finaldata = $bmrequests->sanitize_request( $data, $identifier, $exclude );

				if ( ( $finaldata != false && $finaldata != null ) ) {
					if ( $id == 0 ) {
						$field_id = $dbhandler->insert_row( $identifier, $finaldata );

						if ( isset( $field_id ) ) {
							$response = array(
								'status'     => 'saved',
								'data'       => $dbhandler->get_row( $identifier, $field_id ),
								'is_default' => $bmrequests->bm_check_is_default_field( $field_id ),
							);

							if ( $dbhandler->get_global_option_value( 'bm_booking_form_fields_created', '0' ) == '0' ) {
								$dbhandler->update_global_option_value( 'bm_booking_form_fields_created', '1' );
							}
						}
					} else {
						$dbhandler->update_row( $identifier, 'id', $id, $finaldata, '', '%d' );

						$response = array(
							'status' => 'updated',
							'data'   => $dbhandler->get_row( $identifier, $id ),
						);
					}
				}
			} //end if
		} //end if

		echo wp_json_encode( $response );
		die;
	}//end bm_save_field_and_setting()


	/**
	 * Fetch preview form for fields
	 *
	 * @author Darpan
	 */
	public function bm_fetch_preview_form() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests      = new BM_Request();
		$resp            = '';
		$no_results_text = __( 'No Results Found', 'service-booking' );

		$resp = $bmrequests->bm_fetch_fields();

		if ( empty( $resp ) ) {
			$resp .= '<p style="text-align: center;">';
			$resp .= $no_results_text;
			$resp .= '</p>';
		}

		echo wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_fetch_preview_form()


	/**
	 * Test SMTP connection
	 *
	 * @author Darpan
	 */
	public function bm_check_smtp_connection() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$bm_mail    = new BM_Email();
		$identifier = 'GLOBAL';
		$exclude    = array(
			'_wpnonce',
			'_wp_http_referer',
			'save_email_global',
		);
		$post       = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

		if ( $post != false && $post != null ) {
			if ( isset( $post['bm_smtp_password'] ) && $post['bm_smtp_password'] != '' ) {
				$post['bm_smtp_password'] = $post['bm_smtp_password'];
			} else {
				unset( $post['bm_smtp_password'] );
			}

			foreach ( $post as $key => $value ) {
				$dbhandler->update_global_option_value( $key, $value );
			}
		}

		$dbhandler->update_global_option_value( 'bm_enable_smtp', 1 );
		$to                 = $dbhandler->get_global_option_value( 'bm_smtp_test_email_address' );
		$from_email_address = $bm_mail->bm_get_from_email();
		$headers            = "MIME-Version: 1.0\r\n";
		$headers           .= "Content-type:text/html;charset=UTF-8\r\n";
		$headers           .= "From:$from_email_address\r\n";
		echo esc_html( wp_mail( $to, 'Test SMTP Connection', 'Test', $headers ) );
		die;
	}//end bm_check_smtp_connection()


    /**
     * Email field list
     *
     * @author Darpan
     */
    public function bm_fields_list_for_email( $editor_id ) {
         $dbhandler = new BM_DBhandler();
        $bmrequests = new BM_Request();
        $exclude    = "field_type not in('file','password', 'button', 'hidden', 'submit', 'reset', 'search', 'textarea')";
        $fields     = $dbhandler->get_all_result( 'FIELDS', '*', 1, 'results', 0, false, 'field_position', false, $exclude );

		echo '<select name="bm_field_list" class="bm_field_list" onchange="bm_insert_field_in_email(this.value)">';
		echo '<option value="">' . esc_html__( 'Choose Fields', 'service-booking' ) . '</option>';

		echo '<optgroup label="' . esc_html__( 'Admin Fields', 'service-booking' ) . '" >';
		echo '<option value="{{admin_email}}">' . esc_html__( 'Admin Email', 'service-booking' ) . '</option>';
		echo '<option value="{{admin_name}}">' . esc_html__( 'Admin Name', 'service-booking' ) . '</option>';
		echo '</optgroup>';

		if ( isset( $fields ) && ! empty( $fields ) ) {
			echo '<optgroup label="' . esc_html__( 'Booking Form Fields', 'service-booking' ) . '">';
			foreach ( $fields as $field ) {
				if ( $bmrequests->bm_check_if_field_is_visible( $field->id ) == 1 ) {
					echo '<option value="{{' . esc_attr( $field->field_name ) . '}}">' . esc_html( $field->field_label ) . '</option>';
				}
			}

			echo '</optgroup>';
		}

		echo '<optgroup label="' . esc_html__( 'Order Related Fields', 'service-booking' ) . '" >';
		echo '<option value="{{booking_key}}">' . esc_html__( 'Order Reference', 'service-booking' ) . '</option>';
		echo '<option value="{{booking_date}}">' . esc_html__( 'Service Date', 'service-booking' ) . '</option>';
		echo '<option value="{{booking_created_at}}">' . esc_html__( 'Booked On', 'service-booking' ) . '</option>';
		echo '<option value="{{service_name}}">' . esc_html__( 'Service Name', 'service-booking' ) . '</option>';
		echo '<option value="{{booking_slots}}">' . esc_html__( 'Booked Slots', 'service-booking' ) . '</option>';
		echo '<option value="{{service_duration}}">' . esc_html__( 'Service Duration', 'service-booking' ) . '</option>';
		echo '<option value="{{total_svc_slots}}">' . esc_html__( 'Total Service slots', 'service-booking' ) . '</option>';
		echo '<option value="{{total_ext_svc_slots}}">' . esc_html__( 'Total Extra Service slots', 'service-booking' ) . '</option>';
		echo '<option value="{{total_cost}}">' . esc_html__( 'Order Total Cost', 'service-booking' ) . '</option>';
		echo '<option value="{{base_svc_price}}">' . esc_html__( 'Service Base Price', 'service-booking' ) . '</option>';
		echo '<option value="{{service_cost}}">' . esc_html__( 'Service Total Price', 'service-booking' ) . '</option>';
		echo '<option value="{{disount_amount}}">' . esc_html__( 'Discount', 'service-booking' ) . '</option>';
		echo '<option value="{{subtotal}}">' . esc_html__( 'Subtotal', 'service-booking' ) . '</option>';
		echo '<option value="{{extra_services}}">' . esc_html__( 'Extra Services', 'service-booking' ) . '</option>';
		/**echo '<option value="{{coupon_id}}">' . esc_html__( 'Coupon Id', 'service-booking' ) . '</option>';
		echo '<option value="{{wc_coupon_id}}">' . esc_html__( 'Coupon Id', 'service-booking' ) . '</option>';*/
        echo '</optgroup>';

        $language = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

        if ( in_array( $editor_id, array( "booking_pdf_$language", "voucher_pdf_$language", "customer_info_pdf_$language" ) ) ) {
            echo '<optgroup label="' . esc_html__( 'PDF Related Fields', 'service-booking' ) . '" >';
            echo '<option value="{{service_qty}}">' . esc_html__( 'Service Quantity', 'service-booking' ) . '</option>';
            echo '<option value="{{infant_count}}">' . esc_html__( 'Infant Count', 'service-booking' ) . '</option>';
            echo '<option value="{{infant_discount}}">' . esc_html__( 'Infant Discount', 'service-booking' ) . '</option>';
            echo '<option value="{{child_count}}">' . esc_html__( 'Child Count', 'service-booking' ) . '</option>';
            echo '<option value="{{child_discount}}">' . esc_html__( 'Child Discount', 'service-booking' ) . '</option>';
            echo '<option value="{{adult_count}}">' . esc_html__( 'Adult Count', 'service-booking' ) . '</option>';
            echo '<option value="{{adult_discount}}">' . esc_html__( 'Adult Discount', 'service-booking' ) . '</option>';
            echo '<option value="{{senior_count}}">' . esc_html__( 'Senior Count', 'service-booking' ) . '</option>';
            echo '<option value="{{senior_discount}}">' . esc_html__( 'Senior Discount', 'service-booking' ) . '</option>';
            echo '<option value="{{date_time}}">' . esc_html__( 'Date Time', 'service-booking' ) . '</option>';
            echo '<option value="{{date}}">' . esc_html__( 'Date', 'service-booking' ) . '</option>';
            echo '<option value="{{time}}">' . esc_html__( 'Time', 'service-booking' ) . '</option>';
            echo '<option value="{{current_year}}">' . esc_html__( 'Current Year', 'service-booking' ) . '</option>';
            echo '<option value="{{admin_phone}}">' . esc_html__( 'Admin Phone', 'service-booking' ) . '</option>';
            echo '<option value="{{redeemed_date}}">' . esc_html__( 'Redeemed Date', 'service-booking' ) . '</option>';
            echo '<option value="{{qr_code}}">' . esc_html__( 'QR Code', 'service-booking' ) . '</option>';
            echo '<option value="{{logo}}">' . esc_html__( 'Logo', 'service-booking' ) . '</option>';
            echo '<option value="{{logo_url}}">' . esc_html__( 'Logo url', 'service-booking' ) . '</option>';
            echo '<option value="{{customer_since}}">' . esc_html__( 'Customer Since', 'service-booking' ) . '</option>';
            echo '<option value="{{total_bookings}}">' . esc_html__( 'Total Bookings', 'service-booking' ) . '</option>';
        }

		echo '<optgroup label="' . esc_html__( 'Voucher Fields', 'service-booking' ) . '" >';
		echo '<option value="{{recipient_first_name}}">' . esc_html__( 'Recipient First Name', 'service-booking' ) . '</option>';
		echo '<option value="{{recipient_last_name}}">' . esc_html__( 'Recipient Last Name', 'service-booking' ) . '</option>';
		echo '<option value="{{voucher_code}}">' . esc_html__( 'Voucher Code', 'service-booking' ) . '</option>';
		echo '<option value="{{voucher_expiry_date}}">' . esc_html__( 'Voucher Expiry Date', 'service-booking' ) . '</option>';
		echo '<option value="{{voucher_redeem_page_url}}">' . esc_html__( 'Voucher Redeem Page URL', 'service-booking' ) . '</option>';
		echo '</optgroup>';

		echo '<optgroup label="' . esc_html__( 'Other Fields', 'service-booking' ) . '" >';
		echo '<option value="{{from_name}}">' . esc_html__( 'From Name', 'service-booking' ) . '</option>';
		echo '<option value="{{from_mail}}">' . esc_html__( 'From Mail', 'service-booking' ) . '</option>';
		echo '</optgroup>';

		echo '</select>';
	}//end bm_fields_list_for_email()


	/**
	 * Add email attachement
	 *
	 * @author Darpan
	 */
	public function bm_add_mail_attachment() {
		echo '<form id="email_attachment_form" enctype="multipart/form-data">';
		echo '<label for="email_attachment" class="custom-email-attachement">';
		echo esc_html__( 'Add attachment', 'service-booking' );
		echo '</label>';
		echo '<input id="email_attachment" name="email_attachment[]" type="file" multiple class="hidden" onclick="this.value = null"/>';
		echo '</div>';
		echo '<div id="fileList" style="display: none;"></div>';
		echo '<div class="progress" style="display: none;">';
		echo '<div class="progress-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">' . esc_html( '0%' ) . '</div>';
		echo '<input type="hidden" id="resend_email_attachment" value="">';
		echo '<input type="hidden" id="final_files" value="">';
		echo '</form>';
	}//end bm_add_mail_attachment()


	/**
	 * Fetch timezone
	 *
	 * @author Darpan
	 */
	public function bm_fetch_timezone() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests   = new BM_Request();
		$country_code = filter_input( INPUT_POST, 'country_code' );
		$data         = array( 'status' => false );

		if ( $country_code != false && $country_code != null ) {
			$timezones = $bmrequests->bm_fetch_timezones( $country_code );

			if ( ! empty( $timezones ) ) {
				$data['status']    = true;
				$data['timezones'] = $timezones;
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_timezone()


	/**
	 * Customer details by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_data_for_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler     = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$order_id      = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data          = array( 'status' => false );
		$customer_data = array();

		if ( $order_id != false && $order_id != null ) {
			$order = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );

			if ( ! empty( $order ) && isset( $order->id ) ) {
				$customer_data = $bmrequests->get_customer_info_for_order( $order->id );

				$data['status']        = true;
				$data['customer_info'] = $customer_data;
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_customer_data_for_order()


	/**
	 * Customer details by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_data_for_failed_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler         = new BM_DBhandler();
		$bmrequests        = new BM_Request();
		$failed_booking_id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data              = array( 'status' => false );

		if ( $failed_booking_id != false && $failed_booking_id != null ) {
			$customer_data = $bmrequests->get_customer_info_for_failed_order( $failed_booking_id );

			$data['status']        = true;
			$data['customer_info'] = $customer_data;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_customer_data_for_failed_order()


	/**
	 * Customer details by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_data_for_archived_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler           = new BM_DBhandler();
		$bmrequests          = new BM_Request();
		$archived_booking_id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data                = array( 'status' => false );

		if ( $archived_booking_id != false && $archived_booking_id != null ) {
			$customer_data = $bmrequests->get_customer_info_for_archived_order( $archived_booking_id );

			$data['status']        = true;
			$data['customer_info'] = $customer_data;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_customer_data_for_archived_order()


	/**
	 * Attachments by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_attachments_for_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$order_id    = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data        = array( 'status' => false );
		$attachments = array();

		if ( $order_id != false && $order_id != null ) {
			$attachments    = $bmrequests->bm_fetch_order_attachments( $order_id );
			$data['status'] = true;
		}

		$data['attachments'] = $attachments;

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_attachments_for_order()


	/**
	 * Attachments by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_attachments_for_archived_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$order_id    = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data        = array( 'status' => false );
		$attachments = array();

		if ( $order_id != false && $order_id != null ) {
			$attachments    = $bmrequests->bm_fetch_archived_order_attachments( $order_id );
			$data['status'] = true;
		}

		$data['attachments'] = $attachments;

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_attachments_for_archived_order()


	/**
	 * Attachments by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_attachments_for_failed_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler       = new BM_DBhandler();
		$bmrequests      = new BM_Request();
		$failed_order_id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data            = array( 'status' => false );
		$attachments     = array();

		if ( $failed_order_id != false && $failed_order_id != null ) {
			$attachments    = $bmrequests->bm_fetch_failed_order_attachments( $failed_order_id );
			$data['status'] = true;
		}

		$data['attachments'] = $attachments;

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_attachments_for_failed_order()


	/**
	 * Services by category id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_services_by_category_id() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests  = new BM_Request();
		$category_id = filter_input( INPUT_POST, 'category_id', FILTER_VALIDATE_INT );
		$data        = array( 'status' => false );

		if ( $category_id != false && $category_id != null ) {
			$services = $bmrequests->bm_fetch_services_by_category_id( $category_id, 'booking' );

			$data['status']   = true;
			$data['services'] = $services;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_services_by_category_id()


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
	 * Product details by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_ordered_product_details() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$order_id     = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$data         = array( 'status' => false );
		$product_data = array();

		if ( $order_id != false && $order_id != null ) {
			$order = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );

			if ( ! empty( $order ) ) {
				$category_id                = $bmrequests->bm_fetch_category_id_by_service_id( $order->service_id );
				$product_data['service']    = $bmrequests->bm_fetch_non_woocmmerce_booked_service_info( $order->id );
				$product_data['services']   = $bmrequests->bm_fetch_services_by_category_id( $category_id );
				$product_data['categories'] = $dbhandler->get_all_result( 'CATEGORY', '*', 1, 'results', 0, false, 'cat_position', false );
				$product_data['extras']     = $bmrequests->get_booked_extra_products_info( $order->id );
			}

			$data['status']   = true;
			$data['products'] = $product_data;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_ordered_product_details()


	/**
	 * Service details by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_ordered_service_details() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$order_id   = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		$resp       = '';

		if ( $order_id != false && $order_id != null ) {
			$resp = $bmrequests->bm_fetch_ordered_service_details( $order_id );
		}

		echo wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_fetch_ordered_service_details()


    /**
     * View pdf content
     *
     * @author Darpan
     */
    public function bm_view_pdf_content() {
        $nonce = filter_input( INPUT_POST, 'nonce' );
        if ( !$nonce || !wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
            wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
            return;
        }

        $type          = sanitize_text_field( filter_input( INPUT_POST, 'type' ) ?? 'booking' );
        $pdf_processor = new BM_PDF_Processor();
        $html          = $pdf_processor->bm_get_template_pdf_content( $type, 'dummy' );

        if ( empty( $html ) ) {
            wp_send_json_error( __( 'No content found for this template.', 'service-booking' ) );
            return;
        }

        wp_send_json_success( $html );
    } //end bm_view_pdf_content()


    public function bm_handle_pdf_test_downloads() {
        if ( isset( $_GET['test_pdf_action'], $_GET['type'], $_GET['booking_id'], $_GET['page'] ) && $_GET['page'] === 'bm_pdf_customization' ) {
            $action            = sanitize_text_field( $_GET['test_pdf_action'] );
            $type              = sanitize_text_field( $_GET['type'] );
            $booking_id_or_key = sanitize_text_field( $_GET['booking_id'] );
            $nonce             = isset( $_GET['_wpnonce'] ) ? $_GET['_wpnonce'] : '';

            error_log( $nonce );

            if ( ! wp_verify_nonce( $nonce, 'test_pdf_action_' . $type . '_' . $booking_id_or_key ) ) {
                wp_die( esc_html__( 'Security check failed', 'service-booking' ) );
            }

            $pdf_processor = new BM_PDF_Processor();
            $pdf_path      = '';

            switch ( $type ) {
                case 'voucher':
                    $pdf_path = $pdf_processor->generate_voucher_pdf( $booking_id_or_key );
                    break;
                case 'customer_info':
                    $pdf_path = $pdf_processor->generate_customer_info_pdf( $booking_id_or_key );
                    break;
                case 'booking':
                default:
                    $pdf_path = $pdf_processor->generate_booking_pdf( $booking_id_or_key );
                    break;
            }

            error_log( $pdf_path );

            if ( ! file_exists( $pdf_path ) ) {
                wp_die( esc_html__( 'PDF could not be generated', 'service-booking' ) );
            }

            if ( ob_get_level() ) {
                while ( ob_get_level() ) {
                    ob_end_clean();
                }
            }

            header( 'Content-Type: application/pdf' );
            header( 'Content-Length: ' . filesize( $pdf_path ) );
            header( 'Cache-Control: private, max-age=0, must-revalidate' );
            header( 'Pragma: public' );

            if ( $action === 'download' ) {
                header( 'Content-Disposition: attachment; filename="' . basename( $pdf_path ) . '"' );
            } else {
                header( 'Content-Disposition: inline; filename="' . basename( $pdf_path ) . '"' );
            }

            readfile( $pdf_path );
            exit;
        }
    }


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
	 * Fetch backend order extra services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_extras_for_backend_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$extras     = array();

		if ( $post != false && $post != null ) {
			$extras = $bmrequests->bm_fetch_backend_new_order_extra_services( $post );
		}

		echo wp_json_encode( $extras );
		die;
	}//end bm_fetch_service_extras_for_backend_order()


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
	public function bm_fetch_service_price_for_backend_order() {
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
	}//end bm_fetch_service_price_for_backend_order()


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
	 * Fetch change backend order status
	 *
	 * @author Darpan
	 */
	public function bm_change_order_status_to_complete_or_cancelled() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$order_id = isset( $post['id'] ) && ! empty( $post['id'] ) ? $post['id'] : 0;
			$status   = isset( $post['status'] ) && ! empty( $post['status'] ) ? $post['status'] : '';

			if ( $status == 'cancelled' ) {
				$update_data = array(
					'order_status' => $status,
					'is_active'    => 0,
				);
			} else {
				$update_data = array( 'order_status' => $status );
			}

			if ( $order_id !== 0 && ! empty( $status ) ) {
				$update_data['booking_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
				$updated_status                    = $dbhandler->update_row( 'BOOKING', 'id', $order_id, $update_data, '', '%s' );

				if ( $updated_status ) {
					$data['status'] = true;
				}
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_change_order_status_to_complete_or_cancelled()


	/**
	 * Fetch change backend order status
	 *
	 * @author Darpan
	 */
	public function bm_change_order_status() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$order_id = isset( $post['id'] ) && ! empty( $post['id'] ) ? $post['id'] : 0;
			$status   = isset( $post['status'] ) && ! empty( $post['status'] ) ? $post['status'] : '';

			if ( $status == 'cancelled' ) {
				$update_data = array(
					'order_status' => $status,
					'is_active'    => 0,
				);
			} else {
				$update_data = array( 'order_status' => $status );
			}

			if ( $order_id !== 0 && ! empty( $status ) ) {
				$update_data['booking_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
				$updated_status                    = $dbhandler->update_row( 'BOOKING', 'id', $order_id, $update_data, '', '%s' );

				if ( $updated_status ) {
					$data['status'] = true;
				}
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_change_order_status()


	/**
	 * Fetch columns
	 *
	 * @author Darpan
	 */
	public function bm_fetch_columns_screen_options() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$view_type  = filter_input( INPUT_POST, 'type' );
		$resp       = '';

		if ( $view_type != false && $view_type != null ) {
			$resp = $bmrequests->bm_fetch_columns_screen_options( $view_type, $resp );
		}

		if ( empty( $resp ) ) {
			$resp = '<div class="textcenter">' . esc_html__( 'No results found.', 'service-booking' ) . '</div>';
		}

		echo wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_fetch_columns_screen_options()


	/**
	 * Save columns order
	 *
	 * @author Darpan
	 */
	public function bm_save_columns_screen_options() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$default    = isset( $post['default'] ) ? $post['default'] : null;
			$orders     = isset( $post['orders'] ) ? $post['orders'] : array();
			$names      = isset( $post['names'] ) ? $post['names'] : array();
			$texts      = isset( $post['texts'] ) ? $post['texts'] : array();
			$is_admin   = isset( $post['is_admin'] ) ? $post['is_admin'] : 0;
			$view_type  = isset( $post['view_type'] ) ? $post['view_type'] : '';
			$text_count = count( $texts );
			$language   = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
			$user_id    = get_current_user_id();

			if ( ! empty( $orders ) && ! empty( $names ) && ! empty( $texts ) ) {
				for ( $i = 0; $i < $text_count; $i++ ) {
					$custom_array[ $texts[ $i ] ] = array(
						'order'  => $orders[ $i ],
						'column' => $names[ $i ],
					);
				}

				if ( isset( $custom_array ) ) {
					$column_data = array(
						'language'        => $language,
						'user_id'         => $user_id,
						'default_columns' => $default,
						'screen_options'  => $custom_array,
						'is_admin'        => $is_admin,
						'view_type'       => $view_type,
					);

					$final_data = $bmrequests->sanitize_request( $column_data, 'MANAGECOLUMNS' );

					if ( $final_data != false && $final_data != null ) {
						$last_id = $dbhandler->get_all_result(
							'MANAGECOLUMNS',
							'id',
							array(
								'language'  => $language,
								'user_id'   => $user_id,
								'view_type' => $view_type,
								'is_admin'  => $is_admin,
							),
							'var',
							0,
							1,
							'id',
							'DESC'
						);

						if ( $last_id ) {
							$dbhandler->update_row( 'MANAGECOLUMNS', 'id', $last_id, $final_data, '', '%d' );
							$data['status'] = true;
						} else {
							$final_data['mc_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
							$new_column_id               = $dbhandler->insert_row( 'MANAGECOLUMNS', $final_data );

							if ( $new_column_id ) {
								$data['status'] = true;
							}
						}
					}
				} //end if
			} //end if
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_save_columns_screen_options()


	/**
	 * Search order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_as_per_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$search_term  = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$order_from   = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to     = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? absint( $post['limit'] ) : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$user_id      = get_current_user_id();

			$order_source = isset( $post['order_source'] ) ? sanitize_text_field( $post['order_source'] ) : '';
			$order_status = isset( $post['order_status'] ) ?
				( is_array( $post['order_status'] ) ? $post['order_status'] : explode( ',', $post['order_status'] ) ) :
				array();

			$payment_status = isset( $post['payment_status'] ) ?
				( is_array( $post['payment_status'] ) ? $post['payment_status'] : explode( ',', $post['payment_status'] ) ) :
				array();

			$services = isset( $post['services'] ) ?
				( is_array( $post['services'] ) ? $post['services'] : explode( ',', $post['services'] ) ) :
				array();

			$categories = isset( $post['categories'] ) ?
				( is_array( $post['categories'] ) ? $post['categories'] : explode( ',', $post['categories'] ) ) :
				array();

			$orderby = isset( $post['orderby'] ) ? sanitize_text_field( $post['orderby'] ) : 'id';
			$order   = isset( $post['order'] ) && in_array( strtolower( $post['order'] ), array( 'asc', 'desc' ) ) ? strtolower( $post['order'] ) : 'desc';

			$all_orders = $bmrequests->bm_fetch_all_orders_with_customer_data();
			$dbhandler->update_global_option_value( "show_backend_order_page_failed_orders_$user_id", 0 );
			$dbhandler->update_global_option_value( "show_backend_order_page_archived_orders_$user_id", 0 );

			$filtered_orders = $all_orders;

			if ( $type == 'save_search' ) {
				$search_data = array(
					'service_from'   => ! empty( $service_from ) ? $service_from : '',
					'service_to'     => ! empty( $service_to ) ? $service_to : '',
					'order_from'     => ! empty( $order_from ) ? $order_from : '',
					'order_to'       => ! empty( $order_to ) ? $order_to : '',
					'global_search'  => $search_term,
					'order_source'   => $order_source,
					'order_status'   => is_array( $order_status ) ? implode( ',', $order_status ) : '',
					'payment_status' => is_array( $payment_status ) ? implode( ',', $payment_status ) : '',
					'services'       => is_array( $services ) ? implode( ',', $services ) : '',
					'categories'     => is_array( $categories ) ? implode( ',', $categories ) : '',
				);

				$search_table_data = array(
					'search_data' => $search_data,
					'user_id'     => $user_id,
					'is_admin'    => current_user_can( 'manage_options' ) ? 1 : 0,
					'module'      => 'orders',
				);

				$search_final_data = $bmrequests->sanitize_request( $search_table_data, 'SAVESEARCH' );

				if ( $search_final_data != false && $search_final_data != null ) {
					$last_id = $dbhandler->get_all_result(
						'SAVESEARCH',
						'id',
						array(
							'user_id'  => $user_id,
							'module'   => 'orders',
							'is_admin' => current_user_can( 'manage_options' ) ? 1 : 0,
						),
						'var',
						0,
						1,
						'id',
						'DESC'
					);

					if ( $last_id ) {
						$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $search_final_data, '', '%d' );
					} else {
						$search_final_data['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->insert_row( 'SAVESEARCH', $search_final_data );
					}
				}
			}

			if ( ! empty( $order_source ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_source ) {
						if ( $order_source === 'frontend' ) {
							return $order['is_frontend_booking'] == 1;
						} elseif ( $order_source === 'backend' ) {
							return $order['is_frontend_booking'] != 1;
						}
						return true;
					}
				);
			}

			if ( ! empty( $order_status ) && is_array( $order_status ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_status ) {
						return in_array( $order['order_status'], $order_status );
					}
				);
			}

			if ( ! empty( $payment_status ) && is_array( $payment_status ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $payment_status ) {
						return isset( $order['transaction_status'] ) && in_array( $order['transaction_status'], $payment_status );
					}
				);
			}

			if ( ! empty( $services ) && is_array( $services ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $services ) {
						return in_array( $order['service_id'], $services );
					}
				);
			}

			if ( ! empty( $categories ) && is_array( $categories ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $categories ) {
						return in_array( $order['category'], $categories );
					}
				);
			}

			if ( ! empty( $search_term ) ) {
				$search_date = DateTime::createFromFormat( 'd/m/y', $search_term );
				if ( $search_date !== false ) {
					$search_date_str = $search_date->format( 'Y-m-d' );
					$filtered_orders = array_filter(
						$filtered_orders,
						function ( $order ) use ( $search_date_str, $bmrequests ) {
							$booking_date = $bmrequests->bm_convert_date_format( $order['booking_date'], 'd/m/y H:i', 'Y-m-d' );
							$order_date   = $bmrequests->bm_convert_date_format( $order['booking_created_at'], 'd/m/y H:i', 'Y-m-d' );
							return $booking_date === $search_date_str || $order_date === $search_date_str;
						}
					);
				} else {
					$search_term_lower = strtolower( $search_term );
					$filtered_orders   = array_filter(
						$filtered_orders,
						function ( $order ) use ( $search_term_lower ) {
							$searchable_fields = array(
								'serial_no',
								'service_name',
								'booking_created_at',
								'booking_date',
								'first_name',
								'last_name',
								'contact_no',
								'email_address',
								'total_cost',
								'ordered_from',
								'order_status',
								'service_participants',
								'extra_service_participants',
								'service_cost',
								'extra_service_cost',
								'discount',
								'payment_status',
							);

							foreach ( $searchable_fields as $field ) {
								$value = $order[ $field ];
								if ( is_numeric( $value ) ) {
									$value = (string) $value;
								}
								if ( stripos( $value, $search_term_lower ) !== false ) {
									return true;
								}
							}

							if ( $order['order_status'] === $search_term_lower ) {
								return true;
							}
							return false;
						}
					);
				}
			}

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$service_from = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );

				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $service_from, $service_to, $bmrequests ) {
						$booking_date = $bmrequests->bm_convert_date_format( $order['booking_date'], 'd/m/y H:i', 'Y-m-d' );
						return $booking_date >= $service_from && $booking_date <= $service_to;
					}
				);
			}

			if ( ! empty( $order_from ) && ! empty( $order_to ) ) {
				$order_from = $bmrequests->bm_convert_date_format( $order_from, 'd/m/y', 'Y-m-d' );
				$order_to   = $bmrequests->bm_convert_date_format( $order_to, 'd/m/y', 'Y-m-d' );
				$order_from = $order_from . ' 00:00:00';
				$order_to   = $order_to . ' 23:59:59';

				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_from, $order_to, $bmrequests ) {
						$order_date = $bmrequests->bm_convert_date_format( $order['booking_created_at'], 'd/m/y H:i', 'Y-m-d H:i' );
						return $order_date >= $order_from && $order_date <= $order_to;
					}
				);
			}

			if ( ! empty( $orderby ) ) {
				$filtered_orders = $bmrequests->bm_sort_array_by_key( $filtered_orders, $orderby, $order === 'desc' );
			}

			$total_records = count( $filtered_orders );
			$final_orders  = array_slice( $filtered_orders, $offset, $limit );

			$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;
			$saved_search   = $bmrequests->bm_fetch_last_saved_search_data( 'orders', $is_admin );
			$active_columns = $bmrequests->bm_fetch_active_columns( 'orders' );
			$column_values  = $bmrequests->bm_fetch_column_order_and_names( 'orders' );
			$statuses       = $bmrequests->bm_fetch_order_status_key_value();

			$num_of_pages = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = $final_orders;
			$data['svc_prtcpants']      = array_sum( array_column( $final_orders, 'service_participants' ) );
			$data['ex_svc_prtcpants']   = array_sum( array_column( $final_orders, 'extra_service_participants' ) );
			$data['svc_cost_sum']       = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'service_cost' ) ), true );
			$data['ex_svc_cost_sum']    = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'extra_service_cost' ) ), true );
			$data['discount_sum']       = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'discount' ) ), true );
			$data['total_cost_sum']     = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'total_cost' ) ), true );
			$data['active_columns']     = $active_columns;
			$data['num_of_pages']       = $num_of_pages;
			$data['column_values']      = $column_values;
			$data['order_statuses']     = $statuses;
			$data['saved_search']       = $saved_search;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = ! empty( $pagination ) ? wp_kses_post( $pagination ) : '';
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_order_as_per_search()


	/**
	 * Search archived order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_archived_order_as_per_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$search_term  = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$order_from   = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to     = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? absint( $post['limit'] ) : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$user_id      = get_current_user_id();

			$order_source = isset( $post['order_source'] ) ? sanitize_text_field( $post['order_source'] ) : '';
			$order_status = isset( $post['order_status'] ) ?
				( is_array( $post['order_status'] ) ? $post['order_status'] : explode( ',', $post['order_status'] ) ) :
				array();

			$payment_status = isset( $post['payment_status'] ) ?
				( is_array( $post['payment_status'] ) ? $post['payment_status'] : explode( ',', $post['payment_status'] ) ) :
				array();

			$services = isset( $post['services'] ) ?
				( is_array( $post['services'] ) ? $post['services'] : explode( ',', $post['services'] ) ) :
				array();

			$categories = isset( $post['categories'] ) ?
				( is_array( $post['categories'] ) ? $post['categories'] : explode( ',', $post['categories'] ) ) :
				array();

			$orderby = isset( $post['orderby'] ) ? sanitize_text_field( $post['orderby'] ) : 'id';
			$order   = isset( $post['order'] ) && in_array( strtolower( $post['order'] ), array( 'asc', 'desc' ) ) ? strtolower( $post['order'] ) : 'desc';

			$all_orders = $bmrequests->bm_fetch_all_archived_orders_with_customer_data();
			$dbhandler->update_global_option_value( "show_backend_order_page_failed_orders_$user_id", 0 );
			$dbhandler->update_global_option_value( "show_backend_order_page_archived_orders_$user_id", 1 );

			$filtered_orders = $all_orders;

			if ( $type == 'save_search' ) {
				$search_data = array(
					'service_from'   => ! empty( $service_from ) ? $service_from : '',
					'service_to'     => ! empty( $service_to ) ? $service_to : '',
					'order_from'     => ! empty( $order_from ) ? $order_from : '',
					'order_to'       => ! empty( $order_to ) ? $order_to : '',
					'global_search'  => $search_term,
					'order_source'   => $order_source,
					'order_status'   => is_array( $order_status ) ? implode( ',', $order_status ) : '',
					'payment_status' => is_array( $payment_status ) ? implode( ',', $payment_status ) : '',
					'services'       => is_array( $services ) ? implode( ',', $services ) : '',
					'categories'     => is_array( $categories ) ? implode( ',', $categories ) : '',
				);

				$search_table_data = array(
					'search_data' => $search_data,
					'user_id'     => $user_id,
					'is_admin'    => current_user_can( 'manage_options' ) ? 1 : 0,
					'module'      => 'archived_orders',
				);

				$search_final_data = $bmrequests->sanitize_request( $search_table_data, 'SAVESEARCH' );

				if ( $search_final_data != false && $search_final_data != null ) {
					$last_id = $dbhandler->get_all_result(
						'SAVESEARCH',
						'id',
						array(
							'user_id'  => $user_id,
							'module'   => 'archived_orders',
							'is_admin' => current_user_can( 'manage_options' ) ? 1 : 0,
						),
						'var',
						0,
						1,
						'id',
						'DESC'
					);

					if ( $last_id ) {
						$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $search_final_data, '', '%d' );
					} else {
						$search_final_data['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->insert_row( 'SAVESEARCH', $search_final_data );
					}
				}
			}

			if ( ! empty( $order_source ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_source ) {
						if ( $order_source === 'frontend' ) {
							return $order['is_frontend_booking'] == 1;
						} elseif ( $order_source === 'backend' ) {
							return $order['is_frontend_booking'] != 1;
						}
						return true;
					}
				);
			}

			if ( ! empty( $order_status ) && is_array( $order_status ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_status ) {
						return in_array( $order['order_status'], $order_status );
					}
				);
			}

			if ( ! empty( $payment_status ) && is_array( $payment_status ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $payment_status ) {
						return isset( $order['transaction_status'] ) && in_array( $order['transaction_status'], $payment_status );
					}
				);
			}

			if ( ! empty( $services ) && is_array( $services ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $services ) {
						return in_array( $order['service_id'], $services );
					}
				);
			}

			if ( ! empty( $categories ) && is_array( $categories ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $categories ) {
						return in_array( $order['category'], $categories );
					}
				);
			}

			if ( ! empty( $search_term ) ) {
				$search_date = DateTime::createFromFormat( 'd/m/y', $search_term );
				if ( $search_date !== false ) {
					$search_date_str = $search_date->format( 'Y-m-d' );
					$filtered_orders = array_filter(
						$filtered_orders,
						function ( $order ) use ( $search_date_str, $bmrequests ) {
							$booking_date = $bmrequests->bm_convert_date_format( $order['booking_date'], 'd/m/y H:i', 'Y-m-d' );
							$order_date   = $bmrequests->bm_convert_date_format( $order['booking_created_at'], 'd/m/y H:i', 'Y-m-d' );
							return $booking_date === $search_date_str || $order_date === $search_date_str;
						}
					);
				} else {
					$search_term_lower = strtolower( $search_term );
					$filtered_orders   = array_filter(
						$filtered_orders,
						function ( $order ) use ( $search_term_lower ) {
							$searchable_fields = array(
								'serial_no',
								'service_name',
								'booking_created_at',
								'booking_date',
								'first_name',
								'last_name',
								'contact_no',
								'email_address',
								'total_cost',
								'ordered_from',
								'order_status',
								'service_participants',
								'extra_service_participants',
								'service_cost',
								'extra_service_cost',
								'discount',
								'payment_status',
							);

							foreach ( $searchable_fields as $field ) {
								$value = $order[ $field ];
								if ( is_numeric( $value ) ) {
									$value = (string) $value;
								}
								if ( stripos( $value, $search_term_lower ) !== false ) {
									return true;
								}
							}

							if ( $order['order_status'] === $search_term_lower ) {
								return true;
							}
							return false;
						}
					);
				}
			}

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$service_from = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );

				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $service_from, $service_to, $bmrequests ) {
						$booking_date = $bmrequests->bm_convert_date_format( $order['booking_date'], 'd/m/y H:i', 'Y-m-d' );
						return $booking_date >= $service_from && $booking_date <= $service_to;
					}
				);
			}

			if ( ! empty( $order_from ) && ! empty( $order_to ) ) {
				$order_from = $bmrequests->bm_convert_date_format( $order_from, 'd/m/y', 'Y-m-d' );
				$order_to   = $bmrequests->bm_convert_date_format( $order_to, 'd/m/y', 'Y-m-d' );
				$order_from = $order_from . ' 00:00:00';
				$order_to   = $order_to . ' 23:59:59';

				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_from, $order_to, $bmrequests ) {
						$order_date = $bmrequests->bm_convert_date_format( $order['booking_created_at'], 'd/m/y H:i', 'Y-m-d H:i' );
						return $order_date >= $order_from && $order_date <= $order_to;
					}
				);
			}

			if ( ! empty( $orderby ) ) {
				$filtered_orders = $bmrequests->bm_sort_array_by_key( $filtered_orders, $orderby, $order === 'desc' );
			}

			$total_records = count( $filtered_orders );
			$final_orders  = array_slice( $filtered_orders, $offset, $limit );

			$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;
			$saved_search   = $bmrequests->bm_fetch_last_saved_search_data( 'archived_orders', $is_admin );
			$active_columns = $bmrequests->bm_fetch_active_columns( 'orders' );
			$column_values  = $bmrequests->bm_fetch_column_order_and_names( 'orders' );
			$statuses       = $bmrequests->bm_fetch_order_status_key_value();

			$num_of_pages = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = $final_orders;
			$data['svc_prtcpants']      = array_sum( array_column( $final_orders, 'service_participants' ) );
			$data['ex_svc_prtcpants']   = array_sum( array_column( $final_orders, 'extra_service_participants' ) );
			$data['svc_cost_sum']       = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'service_cost' ) ), true );
			$data['ex_svc_cost_sum']    = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'extra_service_cost' ) ), true );
			$data['discount_sum']       = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'discount' ) ), true );
			$data['total_cost_sum']     = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'total_cost' ) ), true );
			$data['active_columns']     = $active_columns;
			$data['num_of_pages']       = $num_of_pages;
			$data['column_values']      = $column_values;
			$data['order_statuses']     = $statuses;
			$data['saved_search']       = $saved_search;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = ! empty( $pagination ) ? wp_kses_post( $pagination ) : '';
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_archived_order_as_per_search()


	/**
	 * Search checkin data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_checkin_as_per_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$search_term  = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$checkin_from = isset( $post['checkin_from'] ) ? $post['checkin_from'] : '';
			$checkin_to   = isset( $post['checkin_to'] ) ? $post['checkin_to'] : '';
			$service_ids  = isset( $post['service_ids'] ) ? array_map( 'intval', (array) $post['service_ids'] ) : array();
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? absint( $post['limit'] ) : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$user_id      = get_current_user_id();
			$all_checkins = $bmrequests->bm_fetch_all_order_checkins();

			// foreach ( $all_checkins as &$checkin ) {
			// if ( $checkin['checkin_status'] === 'pending' &&
			// strtotime( $checkin['booking_date'] ) < time() ) {
			// $bmrequests->bm_update_checkin_status_as_expired( $checkin['booking_id'] );
			// $checkin['checkin_status'] = 'expired';
			// }
			// }

			$filtered_checkins = $all_checkins;

			if ( ! empty( $search_term ) ) {
				$search_date = DateTime::createFromFormat( 'd/m/y', $search_term );
				if ( $search_date !== false ) {
					$search_date_str   = $search_date->format( 'Y-m-d' );
					$filtered_checkins = array_filter(
						$filtered_checkins,
						function ( $checkin ) use ( $search_date_str ) {
							$booking_date      = $checkin['booking_date'];
							$checkin_time_date = $checkin['checkin_time'] !== '-' ? gmdate( 'Y-m-d', strtotime( $checkin['checkin_time'] ) ) : null;
							return $booking_date === $search_date_str || ( $checkin_time_date === $search_date_str );
						}
					);
				} else {
					$search_term_lower = strtolower( $search_term );
					$filtered_checkins = array_filter(
						$filtered_checkins,
						function ( $checkin ) use ( $search_term_lower ) {
							$searchable_fields = array(
								'serial_no',
								'service_name',
								'booking_date',
								'first_name',
								'last_name',
								'contact_no',
								'email_address',
								'total_cost',
								'checkin_time',
								'checkin_status',
							);
							foreach ( $searchable_fields as $field ) {
								$value = $checkin[ $field ];
								if ( $field === 'checkin_time' && $value === '-' ) {
									continue;
								}
								if ( $field === 'total_cost' || $field === 'serial_no' ) {
									$value = (string) $value;
								}
								if ( stripos( $value, $search_term_lower ) !== false ) {
									return true;
								}
							}

							if ( $checkin['checkin_status'] === $search_term_lower ) {
								return true;
							}
							return false;
						}
					);
				}
			}

			if ( ! empty( $checkin_from ) && ! empty( $checkin_to ) ) {
				$checkin_from_str = $bmrequests->bm_convert_date_format( $checkin_from, 'd/m/y', 'Y-m-d' );
				$checkin_to_str   = $bmrequests->bm_convert_date_format( $checkin_to, 'd/m/y', 'Y-m-d' );
				$checkin_from_str = $checkin_from_str . ' 00:00:00';
				$checkin_to_str   = $checkin_to_str . ' 23:59:59';

				$filtered_checkins = array_filter(
					$filtered_checkins,
					function ( $checkin ) use ( $checkin_from_str, $checkin_to_str, $bmrequests ) {
						if ( $checkin['checkin_time'] === '-' ) {
							return false;
						}
						$checkin_date = $bmrequests->bm_convert_date_format( $checkin['checkin_time'], 'd/m/y H:i', 'Y-m-d H:i' );
						return $checkin_date >= $checkin_from_str && $checkin_date <= $checkin_to_str;
					}
				);
			}

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$service_from_str = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to_str   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );
				$service_from_str = $service_from_str . ' 00:00:00';
				$service_to_str   = $service_to_str . ' 23:59:59';

				$filtered_checkins = array_filter(
					$filtered_checkins,
					function ( $checkin ) use ( $service_from_str, $service_to_str, $bmrequests ) {
						$booking_date = $bmrequests->bm_convert_date_format( $checkin['booking_date'], 'd/m/y H:i', 'Y-m-d H:i' );
						return $booking_date >= $service_from_str && $booking_date <= $service_to_str;
					}
				);
			}

			if ( ! empty( $service_ids ) ) {
				$filtered_checkins = array_filter(
					$filtered_checkins,
					function ( $checkin ) use ( $service_ids ) {
						return in_array( $checkin['service_id'], $service_ids );
					}
				);
			}

			$total_records  = count( $filtered_checkins );
			$final_checkins = array_slice( $filtered_checkins, $offset, $limit );
			$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;

			if ( $type == 'save_search' ) {
				$search_data = array(
					'service_from'  => $service_from ?? '',
					'service_to'    => $service_to ?? '',
					'checkin_from'  => $checkin_from ?? '',
					'checkin_to'    => $checkin_to ?? '',
					'global_search' => $search_term,
					'service_ids'   => $service_ids,
				);

				$search_table_data = array(
					'search_data' => $search_data,
					'user_id'     => $user_id,
					'is_admin'    => $is_admin,
					'module'      => 'checkin',
				);

				$search_final_data = $bmrequests->sanitize_request( $search_table_data, 'SAVESEARCH' );

				if ( $search_final_data != false && $search_final_data != null ) {
					$last_id = $dbhandler->get_all_result(
						'SAVESEARCH',
						'id',
						array(
							'user_id'  => $user_id,
							'module'   => 'checkin',
							'is_admin' => $is_admin,
						),
						'var',
						0,
						1,
						'id',
						'DESC'
					);

					if ( $last_id ) {
						$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $search_final_data, '', '%d' );
					} else {
						$search_final_data['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->insert_row( 'SAVESEARCH', $search_final_data );
					}
				}
			}

			$saved_search   = $bmrequests->bm_fetch_last_saved_search_data( 'checkin', $is_admin );
			$active_columns = $bmrequests->bm_fetch_active_columns( 'checkin' );
			$column_values  = $bmrequests->bm_fetch_column_order_and_names( 'checkin' );
			$statuses       = $bmrequests->bm_fetch_order_status_key_value();

			$num_of_pages = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['checkins']           = $final_checkins;
			$data['active_columns']     = $active_columns;
			$data['num_of_pages']       = $num_of_pages;
			$data['column_values']      = $column_values;
			$data['saved_search']       = $saved_search;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = wp_kses_post( is_string( $pagination ) ? $pagination : '' );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_checkin_as_per_search()


	/**
	 * Search failed order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_failed_order_as_per_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post ) {
			$search_term  = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$order_from   = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to     = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? absint( $post['limit'] ) : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$user_id      = get_current_user_id();

			$order_source = isset( $post['order_source'] ) ? sanitize_text_field( $post['order_source'] ) : '';

			$orderby = isset( $post['orderby'] ) ? sanitize_text_field( $post['orderby'] ) : 'id';
			$order   = isset( $post['order'] ) && in_array( strtolower( $post['order'] ), array( 'asc', 'desc' ) ) ? strtolower( $post['order'] ) : 'desc';

			$dbhandler->update_global_option_value( "show_backend_order_page_failed_orders_$user_id", 1 );
			$dbhandler->update_global_option_value( "show_backend_order_page_archived_orders_$user_id", 0 );

			$filtered_orders = $bmrequests->bm_fetch_all_failed_transactions_with_customer_data();

			if ( $type == 'save_search' ) {
				$search_data = array(
					'service_from'  => ! empty( $service_from ) ? $service_from : '',
					'service_to'    => ! empty( $service_to ) ? $service_to : '',
					'order_from'    => ! empty( $order_from ) ? $order_from : '',
					'order_to'      => ! empty( $order_to ) ? $order_to : '',
					'global_search' => $search_term,
					'order_source'  => $order_source,
				);

				$search_table_data = array(
					'search_data' => $search_data,
					'user_id'     => $user_id,
					'is_admin'    => current_user_can( 'manage_options' ) ? 1 : 0,
					'module'      => 'failed_orders',
				);

				$search_final_data = $bmrequests->sanitize_request( $search_table_data, 'SAVESEARCH' );

				if ( $search_final_data != false && $search_final_data != null ) {
					$last_id = $dbhandler->get_all_result(
						'SAVESEARCH',
						'id',
						array(
							'user_id'  => $user_id,
							'module'   => 'failed_orders',
							'is_admin' => current_user_can( 'manage_options' ) ? 1 : 0,
						),
						'var',
						0,
						1,
						'id',
						'DESC'
					);

					if ( $last_id ) {
						$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $search_final_data, '', '%d' );
					} else {
						$search_final_data['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->insert_row( 'SAVESEARCH', $search_final_data );
					}
				}
			}

			if ( ! empty( $order_source ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_source ) {
						if ( $order_source === 'frontend' ) {
							return $order['is_frontend_booking'] == 1;
						} elseif ( $order_source === 'backend' ) {
							return $order['is_frontend_booking'] != 1;
						}
						return true;
					}
				);
			}

			if ( ! empty( $search_term ) ) {
				$search_date = DateTime::createFromFormat( 'd/m/y', $search_term );
				if ( $search_date !== false ) {
					$search_date_str = $search_date->format( 'Y-m-d' );
					$filtered_orders = array_filter(
						$filtered_orders,
						function ( $order ) use ( $search_date_str, $bmrequests ) {
							$booking_date = $bmrequests->bm_convert_date_format( $order['booking_date'], 'd/m/y H:i', 'Y-m-d' );
							$order_date   = $bmrequests->bm_convert_date_format( $order['booking_created_at'], 'd/m/y H:i', 'Y-m-d' );
							return $booking_date === $search_date_str || $order_date === $search_date_str;
						}
					);
				} else {
					$search_term_lower = strtolower( $search_term );
					$filtered_orders   = array_filter(
						$filtered_orders,
						function ( $order ) use ( $search_term_lower ) {
							$searchable_fields = array(
								'serial_no',
								'service_name',
								'booking_created_at',
								'booking_date',
								'first_name',
								'last_name',
								'contact_no',
								'email_address',
								'total_cost',
								'ordered_from',
								'order_status',
								'service_participants',
								'extra_service_participants',
								'service_cost',
								'extra_service_cost',
								'discount',
								'payment_status',
							);

							foreach ( $searchable_fields as $field ) {
								$value = $order[ $field ];
								if ( is_numeric( $value ) ) {
									$value = (string) $value;
								}
								if ( stripos( $value, $search_term_lower ) !== false ) {
									return true;
								}
							}

							if ( $order['order_status'] === $search_term_lower ) {
								return true;
							}
							return false;
						}
					);
				}
			}

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$service_from = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );

				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $service_from, $service_to, $bmrequests ) {
						$booking_date = $bmrequests->bm_convert_date_format( $order['booking_date'], 'd/m/y H:i', 'Y-m-d' );
						return $booking_date >= $service_from && $booking_date <= $service_to;
					}
				);
			}

			if ( ! empty( $order_from ) && ! empty( $order_to ) ) {
				$order_from = $bmrequests->bm_convert_date_format( $order_from, 'd/m/y', 'Y-m-d' );
				$order_to   = $bmrequests->bm_convert_date_format( $order_to, 'd/m/y', 'Y-m-d' );
				$order_from = $order_from . ' 00:00:00';
				$order_to   = $order_to . ' 23:59:59';

				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $order_from, $order_to, $bmrequests ) {
						$order_date = $bmrequests->bm_convert_date_format( $order['booking_created_at'], 'd/m/y H:i', 'Y-m-d H:i' );
						return $order_date >= $order_from && $order_date <= $order_to;
					}
				);
			}

			if ( ! empty( $orderby ) ) {
				$filtered_orders = $bmrequests->bm_sort_array_by_key( $filtered_orders, $orderby, $order === 'desc' );
			}

			$total_records = count( $filtered_orders );
			$final_orders  = array_slice( $filtered_orders, $offset, $limit );

			$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;
			$saved_search   = $bmrequests->bm_fetch_last_saved_search_data( 'failed_orders', $is_admin );
			$active_columns = $bmrequests->bm_fetch_active_columns( 'orders' );
			$column_values  = $bmrequests->bm_fetch_column_order_and_names( 'orders' );
			$statuses       = $bmrequests->bm_fetch_order_status_key_value();

			$num_of_pages = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = $final_orders;
			$data['svc_prtcpants']      = array_sum( array_column( $final_orders, 'service_participants' ) );
			$data['ex_svc_prtcpants']   = array_sum( array_column( $final_orders, 'extra_service_participants' ) );
			$data['svc_cost_sum']       = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'service_cost' ) ), true );
			$data['ex_svc_cost_sum']    = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'extra_service_cost' ) ), true );
			$data['discount_sum']       = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'discount' ) ), true );
			$data['total_cost_sum']     = $bmrequests->bm_fetch_price_in_global_settings_format( array_sum( array_column( $final_orders, 'total_cost' ) ), true );
			$data['active_columns']     = $active_columns;
			$data['num_of_pages']       = $num_of_pages;
			$data['column_values']      = $column_values;
			$data['order_statuses']     = $statuses;
			$data['saved_search']       = $saved_search;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = ! empty( $pagination ) ? wp_kses_post( $pagination ) : '';
		}

		echo wp_json_encode( $data );
		die;
	}


	/**
	 * Service Planner events callbak
	 *
	 * @author Darpan
	 */
	public function bm_filter_service_planner_events_callback() {
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
						$timeslots = $bmrequests->bm_fetch_service_planner_time_slot_cap_left_min_cap_array_by_service_id_date( $service->id, $current_date );
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
	}//end bm_filter_service_planner_events_callback()


	/**
	 * Timeslot fullcalendar dialog content callbak
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_planner_dialog_content() {
		if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ?: array();

		if ( empty( $post ) || ! isset( $post['service_id'], $post['date'], $post['time_slot_value'] ) ) {
			wp_send_json_error( __( 'Invalid data', 'service-booking' ) );
			return;
		}

		$service_id      = absint( $post['service_id'] );
		$date            = sanitize_text_field( $post['date'] );
		$time_slot_value = sanitize_text_field( $post['time_slot_value'] );

		$bmrequests = new BM_Request();

		$start_time = '00:00';
		if ( isset( $time_slot_value ) && strpos( $time_slot_value, ' - ' ) !== false ) {
			$booking_slots = explode( ' - ', $time_slot_value );
			$start_time    = isset( $booking_slots[0] ) ? $booking_slots[0] : '00:00';
		} else {
			$start_time = isset( $time_slot_value ) ? $time_slot_value : '00:00';
		}

		$start_time = $bmrequests->bm_twenty_fourhrs_format( $start_time );

		$reservations = $bmrequests->bm_fetch_service_planner_reservation_list( $service_id, $date, $start_time );

		$html  = '<div class="Reservation-details">';
		$html .= '    <div class="Reservation-date-time">';
		$html .= '        <div class="datetime sg-reservation-slot-details">';
		$html .= '            <div class="Reservation-date-time">';
		$html .= '                <div class="datetime">';
		$html .= __( 'Date ', 'service-booking' ) . ' : <span>' . esc_html( $bmrequests->bm_month_year_date_format( $date ) ) . '</span>';
		$html .= '                </div>';
		$html .= '                <div class="datetime">';
		$html .= __( 'Service Name', 'service-booking' );
		$html .= '                  <span>';
		$html .= $bmrequests->bm_fetch_service_name_by_service_id( $service_id );
		$html .= '                  </span>';
		$html .= '                </div>';
		$html .= '                <div class="datetime">';
		$html .= __( 'Time ', 'service-booking' ) . ' : <span>' . esc_html( $start_time ) . '</span>';
		$html .= '                </div>';
		$html .= '            </div>';

		if ( ! empty( $reservations ) ) {
			$html .= '    <table class="booking-table">';
			$html .= '        <thead>';
			$html .= '            <tr>';
			$html .= '                <th>' . __( 'Reference', 'service-booking' ) . '</th>';
			$html .= '                <th>' . __( 'Last Name', 'service-booking' ) . '</th>';
			$html .= '                <th>' . __( 'Service Participants', 'service-booking' ) . '</th>';
			$html .= '                <th>' . __( 'Extra Service Participants', 'service-booking' ) . '</th>';
			$html .= '                <th>' . __( 'Booking Status', 'service-booking' ) . '</th>';
			$html .= '                <th>' . __( 'Payment Status', 'service-booking' ) . '</th>';
			$html .= '                <th>' . __( 'Total', 'service-booking' ) . '</th>';
			$html .= '            </tr>';
			$html .= '        </thead>';
			$html .= '        <tbody>';

			foreach ( $reservations as $reservation ) {
				$html .= '        <tr class="reservation-row" data-booking-id="' . esc_attr( $reservation['booking_id'] ) . '">';
				$html .= '            <td>';
				$html .= esc_html( $reservation['reference_number'] ) .
										' <a href="admin.php?page=bm_single_order&booking_id=' . $reservation['booking_id'] . '" target="_blank" class="view-order" title="' . __( 'View Order', 'service-booking' ) . '">';
				$html .= '                    <i class="dashicons dashicons-external"></i>';
				$html .= '                </a>';
				$html .= '            </td>';
				$html .= '            <td>' . esc_html( $reservation['last_name'] ) . '</td>';
				$html .= '            <td>' . esc_html( $reservation['svc_participants'] ) . '</td>';
				$html .= '            <td>' . esc_html( $reservation['ex_svc_participants'] ) . '</td>';
				$html .= '            <td>' . esc_html( $reservation['booking_status'] ) . '</td>';
				$html .= '            <td>' . esc_html( $reservation['payment_status'] ) . '</td>';
				$html .= '            <td>' . esc_html( $reservation['total'] ) . '</td>';
				$html .= '        </tr>';
			}

			$html .= '        </tbody>';
			$html .= '    </table>';
		} else {
			$html .= '    <div class="no-reservations">';
			$html .= '        <i class="dashicons dashicons-info"></i>';
			$html .= __( 'No reservations found for this time slot.', 'service-booking' );
			$html .= '    </div>';
		}

		$html .= '        </div>';
		$html .= '    </div>';
		$html .= '</div>';

		wp_send_json_success(
			array(
				'html' => wp_kses( $html, $bmrequests->bm_fetch_expanded_allowed_tags() ),
			)
		);
	}//end bm_fetch_service_planner_dialog_content()


	/**
	 * Get customer info
	 *
	 * @author Darpan
	 */
	public function bm_get_order_personal_info() {
		if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
			return;
		}

		$booking_id = filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT );

		if ( ! $booking_id ) {
			wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$order_data = $bmrequests->bm_fetch_order_details_for_single_page( $booking_id );

		if ( ! $order_data ) {
			wp_send_json_error( __( 'Order not found', 'service-booking' ) );
		}

		$response = array(
			'billing_name'    => $order_data['customer_info']['first_name'] . ' ' . $order_data['customer_info']['last_name'],
			'billing_address' => $order_data['billing_details']['address'],
			'billing_city'    => $order_data['billing_details']['city'],
			'billing_country' => $order_data['billing_details']['country'],
			'billing_phone'   => $order_data['customer_info']['phone'],
			'customer_since'  => date( 'M j, Y', strtotime( $order_data['customer_info']['created_at'] ?? 'now' ) ),
			'total_orders'    => $this->bm_count_customer_orders( $order_data['customer_info']['id'] ),
			'notes'           => $order_data['billing_details']['notes'],
		);

		wp_send_json_success( $response );
	}//end bm_get_order_personal_info()


	/**
	 * Get payment details
	 *
	 * @author Darpan
	 */
	public function bm_get_order_payment_details() {
		if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
			return;
		}

		$booking_id = filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT );

		if ( ! $booking_id ) {
			wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
		}

		$transaction = ( new BM_DBhandler() )->get_row( 'TRANSACTIONS', $booking_id, 'booking_id' );

		if ( ! $transaction ) {
			wp_send_json_error( __( 'No payment details found', 'service-booking' ) );
		}

		$response = array(
			'payment_method' => ucfirst( $transaction->payment_method ),
			'transaction_id' => $transaction->transaction_id,
			'amount'         => ( new BM_Request() )->bm_fetch_price_in_global_settings_format( $transaction->paid_amount, true ),
			'status'         => ucfirst( $transaction->payment_status ),
			'date'           => date( 'M j, Y H:i', strtotime( $transaction->transaction_created_at ) ),
		);

		wp_send_json_success( $response );
	}


	public function bm_get_order_email_info() {
		if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
			return;
		}

		$booking_id = filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT );

		$emails = ( new BM_DBhandler() )->get_all_result(
			'EMAILS',
			'*',
			array(
				'module_type' => 'BOOKING',
				'module_id'   => $booking_id,
			),
			'results'
		);

		$response = array(
            'emails' => array_map(
                function( $email ) {
                    return array(
                        'id'        => $email->id ?? null,
                        'subject'   => $email->mail_sub ?? '',
                        'recipient' => $email->mail_to ?? '',
                        'status'    => $email->status ?? '',
                        'date'      => isset( $email->created_at )
                            ? ( new BM_Request() )->bm_month_year_date_format( $email->created_at )
                            : '',
                    );
                },
                $emails ?: array()
            ),
        );

        wp_send_json_success( $response );

	}

    public function bm_resend_order_email() {
        if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
            return;
        }

        $booking_id = filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT );
        if ( ! $booking_id ) {
            wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
            return;
        }

        $db_handler = new BM_DBhandler();
        $booking    = $db_handler->get_row( 'BOOKING', '*', array( 'id' => $booking_id ) );

        if ( ! $booking ) {
            wp_send_json_error( __( 'Booking not found', 'service-booking' ) );
            return;
        }

        $emails = $db_handler->get_all_result(
            'EMAILS',
            '*',
            array(
                'module_type' => 'BOOKING',
                'module_id'   => $booking_id,
            ),
            'results'
        );
        if ( ! empty( $emails ) ) {
            wp_send_json_error( __( 'Emails have already been sent for this order.', 'service-booking' ) );
            return;
        }

        $timezone        = $db_handler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
        $today           = new DateTime( 'now', new DateTimeZone( $timezone ) );
        $current_date    = $today->format( 'Y-m-d' );
        $current_time    = $today->format( 'H:i' );
        $currentDateTime = $current_date . ' ' . $current_time;

        $service_date  = isset( $booking->booking_date ) ? $booking->booking_date : '';
        $booking_slots = isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();
        $from_slot     = isset( $booking_slots['from'] ) ? $booking_slots['from'] : '';

        if ( ! empty( $service_date ) && ! empty( $from_slot ) ) {
            $service_date_time = $service_date . ' ' . $from_slot;
        } else {
            $service_date_time = $service_date . ' 00:00:00';
        }

        $service_timestamp = strtotime( $service_date_time );
        $current_timestamp = strtotime( $currentDateTime );

        if ( $service_timestamp <= $current_timestamp ) {
            wp_send_json_error( __( 'Cannot resend email for a past service date.', 'service-booking' ) );
            return;
        }

        $booking_type = isset( $booking->booking_type ) ? $booking->booking_type : '';
        if ( $booking_type === 'on_request' ) {
            do_action( 'flexibooking_set_process_new_request', $booking_id );
        } elseif ( $booking_type === 'direct' ) {
            do_action( 'flexibooking_set_process_new_order', $booking_id );
        } else {
            wp_send_json_error( __( 'Unknown booking type.', 'service-booking' ) );
            return;
        }

        wp_send_json_success( __( 'Email resend process has been triggered.', 'service-booking' ) );
    }

	public function bm_get_order_products() {
		if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
			return;
		}

		$booking_id = filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT );

		if ( ! $booking_id ) {
			wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
		}

		$bm_requests = new BM_Request();
		$order_data  = $bm_requests->bm_fetch_order_details_for_single_page( $booking_id );

		if ( ! $order_data ) {
			wp_send_json_error( __( 'Order not found', 'service-booking' ) );
		}

		$response = array(
			'products' => $order_data['ordered_products'],
		);

		wp_send_json_success( $response );
	}


	public function bm_get_email_content() {
		if ( ! check_ajax_referer( 'ajax-nonce', 'nonce', false ) ) {
			wp_send_json_error( __( 'Security check failed', 'service-booking' ) );
			return;
		}

		$email_id = filter_input( INPUT_POST, 'email_id', FILTER_VALIDATE_INT );

		if ( ! $email_id ) {
			wp_send_json_error( __( 'Invalid email ID', 'service-booking' ) );
		}

		$email = ( new BM_DBhandler() )->get_row( 'EMAILS', $email_id, 'id' );

		if ( ! $email ) {
			wp_send_json_error( __( 'Email not found', 'service-booking' ) );
		}

		$response = array(
			'subject'   => $email->mail_sub,
			'recipient' => $email->mail_to,
			'content'   => wp_kses_post( $email->mail_body ),
			'date'      => ( new BM_Request() )->bm_month_year_date_format( $email->created_at ),
		);

		wp_send_json_success( $response );
	}


	private function bm_count_customer_orders( $customer_id ) {
		return ( new BM_DBhandler() )->bm_count( 'BOOKING', array( 'customer_id' => $customer_id ) );
	}


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


	/**
	 * Fullcalendar events callbak
	 *
	 * @author Darpan
	 */
	public function bm_single_service_planner_events_callback() {
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
						$bmrequests->bm_fetch_service_planner_time_slot_array_by_service_id(
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
	}//end bm_single_service_planner_events_callback()


	/**
	 * Service planner callbak
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_planner_time_slots() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$resp       = '';
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id   = isset( $post['id'] ) ? $post['id'] : 0;
			$date = isset( $post['date'] ) ? $post['date'] : '';

			if ( $id > 0 && ! empty( $date ) ) {
				$resp .= $bmrequests->bm_fetch_service_time_slot_by_service_id_for_service_planner( $post );

				if ( empty( $resp ) ) {
					$resp .= '<div class="textcenter">' . esc_html__( 'No results found.', 'service-booking' ) . '</div>';
				}

				$data['status'] = true;
			} //end if
		} //end if

		$data['data'] = wp_kses( $resp, $bmrequests->bm_fetch_expanded_allowed_tags() );

		echo wp_json_encode( $data );
		die;
	} // end of bm_fetch_service_planner_time_slots()


	/**
	 * Search dashboard order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_dashoboard_order_global_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler          = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$woocommerceservice = new WooCommerceService();
		$post               = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data               = array( 'status' => false );
		$statuses           = array();

		if ( $post != false && $post != null ) {
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$search_term  = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$order_from   = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to     = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$user_id      = get_current_user_id();
			$bookings     = $dbhandler->get_all_result( 'BOOKING', '*', 1, 'results', 0, false, 'booking_date', 'DESC' );
			$dbhandler->update_global_option_value( 'bm_backend_dashboard_global_search_field', $search_term );

			if ( ! empty( $type ) && $type == 'save_search' ) {
				$search_data = array(
					'service_from'  => ! empty( $service_from ) ? $service_from : '',
					'service_to'    => ! empty( $service_to ) ? $service_to : '',
					'order_from'    => ! empty( $order_from ) ? $order_from : '',
					'order_to'      => ! empty( $order_to ) ? $order_to : '',
					'global_search' => $search_term,
				);

				$search_table_data = array(
					'user_id'     => $user_id,
					'search_data' => $search_data,
					'is_admin'    => current_user_can( 'manage_options' ) ? 1 : 0,
					'module'      => 'dashboard_all_orders',
				);

				$search_final_data = $bmrequests->sanitize_request( $search_table_data, 'SAVESEARCH' );

				if ( $search_final_data != false && $search_final_data != null ) {
					$last_id = $dbhandler->get_all_result(
						'SAVESEARCH',
						'id',
						array(
							'user_id'  => $user_id,
							'module'   => 'dashboard_all_orders',
							'is_admin' => current_user_can( 'manage_options' ) ? 1 : 0,
						),
						'var',
						0,
						1,
						'id',
						'DESC'
					);

					if ( $last_id ) {
						$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $search_final_data, '', '%d' );
					} else {
						$search_final_data['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->insert_row( 'SAVESEARCH', $search_final_data );
					}
				}
			} //end if

			if ( ! empty( $search_term ) ) {
				$exclude_columns = array(
					'id',
					'wc_order_id',
					'service_id',
					'booking_slots',
					'field_values',
					'field_values',
					'has_extra',
					'extra_svc_booked',
					'coupon_id',
					'wc_coupon_id',
					'disount_amount',
					'newsletter',
					'mail_sent',
				);
				$search_term     = strtolower( sanitize_text_field( $search_term ) );
				$column_names    = $dbhandler->get_table_columns( 'BOOKING', $exclude_columns );
				$text_columns    = array();

				if ( ! empty( $column_names ) ) {
					foreach ( $column_names as $column ) {
						$text_columns[] = "LOWER($column) LIKE '" . $search_term . "%'";
					}
				}

				if ( stripos( $search_term, 'frontend' ) !== false || stripos( $search_term, 'backend' ) !== false ) {
					$search_value = ( strpos( $search_term, 'frontend' ) !== false ) ? 1 : 0;
					$search_value = ( strpos( $search_term, 'backend' ) !== false ) ? 0 : 1;
					$additional   = "AND is_frontend_booking LIKE '%" . $search_value . "%'";
				} elseif ( DateTime::createFromFormat( 'd/m/y', $search_term ) !== false ) {
					$service_date      = $bmrequests->bm_convert_date_format( $search_term, 'd/m/y', 'Y-m-d' );
					$order_search_from = $service_date . ' 00:00:00';
					$order_search_to   = $service_date . ' 23:59:59';
					$additional        = "AND booking_date = '$service_date' OR booking_created_at BETWEEN '$order_search_from' AND '$order_search_to'";
				} else {
					$additional = 'AND ' . implode( ' OR ', $text_columns ) . '';
				}

				$bookings = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'booking_date', 'DESC', $additional );
			} //end if

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$service_from = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );
				$bookings     = $bmrequests->bm_filter_results_by_date( $bookings, $service_from, $service_to, 'booking_date' );
			}

			if ( ! empty( $order_from ) && ! empty( $order_to ) ) {
				$order_from = $bmrequests->bm_convert_date_format( $order_from, 'd/m/y', 'Y-m-d' );
				$order_to   = $bmrequests->bm_convert_date_format( $order_to, 'd/m/y', 'Y-m-d' );
				$order_from = $order_from . ' 00:00:00';
				$order_to   = $order_to . ' 23:59:59';
				$bookings   = $bmrequests->bm_filter_results_by_date( $bookings, $order_from, $order_to, 'booking_created_at' );
			}

			$saved_search = $bmrequests->bm_fetch_last_saved_search_data( 'dashboard_all_orders', current_user_can( 'manage_options' ) ? 1 : 0 );

			/**if ( !empty( $bookings ) && is_array( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking->id, 'booking_id' );
					if ( $payment_status == 'requires_capture' || $payment_status == 'pending' ) {
						if ( isset( $bookings[ $key ] ) ) {
							unset( $bookings[ $key ] );
						}
					}
				}
				if ( !empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}*/

			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $bookings, $offset, $limit );

			if ( is_array( $final_bookings ) && ! empty( $final_bookings ) ) {
				$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

				foreach ( $final_bookings as $final_booking ) {
					$booking_slots                     = isset( $final_booking->booking_slots ) ? maybe_unserialize( $final_booking->booking_slots ) : array();
					$booking_created_at                = new DateTime( $final_booking->booking_created_at, new DateTimeZone( $timezone ) );
					$service_date                      = new DateTime( $final_booking->booking_date . ' ' . $booking_slots['from'], new DateTimeZone( $timezone ) );
					$final_booking->booking_created_at = esc_html( $booking_created_at->format( 'd/m/y H:i' ) );
					$final_booking->booking_date       = esc_html( $service_date->format( 'd/m/y H:i' ) );
				}
			}

			/**if ( $woocommerceservice->is_enabled() ) {
				$order_statuses = wc_get_order_statuses();
			} else {
				$order_statuses = $bmrequests->bm_fetch_order_status_key_value();
			}
			foreach ( $order_statuses as $key => $status ) {
				$value              = $bmrequests->bm_fetch_order_status_string( $key );
				$text               = $bmrequests->bm_fetch_order_status_key_value( $value );
				$statuses[ $value ] = $text;
			}*/

			$statuses = $bmrequests->bm_fetch_order_status_key_value();

			if ( ! empty( $search_term ) || ! empty( $service_from ) || ! empty( $service_to ) || ! empty( $order_from ) || ! empty( $order_to ) ) {
				$total_records = $bookings != null && ! empty( $bookings ) ? count( $bookings ) : 0;
			} else {
				$total_records = $dbhandler->bm_count( 'BOOKING' );
			}

			$num_of_pages = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = ! empty( $final_bookings ) ? array_values( $final_bookings ) : array();
			$data['saved_search']       = $saved_search;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['order_statuses']     = $statuses;
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_dashoboard_order_global_search()


	/**
	 * Search upcoming order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_upcoming_orders() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler          = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$woocommerceservice = new WooCommerceService();
		$post               = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data               = array( 'status' => false );
		$statuses           = array();
		$timezone           = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now                = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date       = $now->format( 'Y-m-d' );
		$current_time       = $now->format( 'H:i' );

		if ( $post != false && $post != null ) {
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$order_from   = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to     = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$user_id      = get_current_user_id();

			$additional = "AND booking_date >= '$current_date'";
			$bookings   = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );

			if ( ! empty( $type ) && $type == 'save_search' ) {
				$search_data = array(
					'service_from' => ! empty( $service_from ) ? $service_from : '',
					'service_to'   => ! empty( $service_to ) ? $service_to : '',
					'order_from'   => ! empty( $order_from ) ? $order_from : '',
					'order_to'     => ! empty( $order_to ) ? $order_to : '',
				);

				$search_table_data = array(
					'user_id'     => $user_id,
					'search_data' => $search_data,
					'is_admin'    => current_user_can( 'manage_options' ) ? 1 : 0,
					'module'      => 'dashboard_upcoming_orders',
				);

				$search_final_data = $bmrequests->sanitize_request( $search_table_data, 'SAVESEARCH' );

				if ( $search_final_data != false && $search_final_data != null ) {
					$last_id = $dbhandler->get_all_result(
						'SAVESEARCH',
						'id',
						array(
							'user_id'  => $user_id,
							'module'   => 'dashboard_upcoming_orders',
							'is_admin' => current_user_can( 'manage_options' ) ? 1 : 0,
						),
						'var',
						0,
						1,
						'id',
						'DESC'
					);

					if ( $last_id ) {
						$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $search_final_data, '', '%d' );
					} else {
						$search_final_data['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->insert_row( 'SAVESEARCH', $search_final_data );
					}
				}
			} //end if

			if ( ! empty( $bookings ) && is_array( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$service_date     = isset( $booking->booking_date ) ? $booking->booking_date : '';
					$booking_slots    = maybe_unserialize( $booking->booking_slots );
					$from_slot        = isset( $booking_slots['from'] ) ? $booking_slots['from'] : '00:00';
					$service_datetime = ! empty( $service_date ) ? $service_date . ' ' . $from_slot . ':00' : '';
					$current_datetime = $current_date . ' ' . $current_time . ':00';

					if ( ! empty( $service_datetime ) ) {
						if ( strtotime( $current_datetime ) > strtotime( $service_datetime ) ) {
							if ( isset( $bookings[ $key ] ) ) {
								unset( $bookings[ $key ] );
							}
						}
					}
				}

				if ( ! empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$service_from = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );
				$bookings     = $bmrequests->bm_filter_results_by_date( $bookings, $service_from, $service_to, 'booking_date' );
			}

			if ( ! empty( $order_from ) && ! empty( $order_to ) ) {
				$order_from = $bmrequests->bm_convert_date_format( $order_from, 'd/m/y', 'Y-m-d' );
				$order_to   = $bmrequests->bm_convert_date_format( $order_to, 'd/m/y', 'Y-m-d' );
				$order_from = $order_from . ' 00:00:00';
				$order_to   = $order_to . ' 23:59:59';
				$bookings   = $bmrequests->bm_filter_results_by_date( $bookings, $order_from, $order_to, 'booking_created_at' );
			}

			$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;
			$saved_search   = $bmrequests->bm_fetch_last_saved_search_data( 'dashboard_upcoming_orders', $is_admin );
			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $bookings, $offset, $limit );

			if ( is_array( $final_bookings ) && ! empty( $final_bookings ) ) {
				$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

				foreach ( $final_bookings as $final_booking ) {
					$booking_slots                     = isset( $final_booking->booking_slots ) ? maybe_unserialize( $final_booking->booking_slots ) : array();
					$booking_created_at                = new DateTime( $final_booking->booking_created_at, new DateTimeZone( $timezone ) );
					$service_date                      = new DateTime( $final_booking->booking_date . ' ' . $booking_slots['from'], new DateTimeZone( $timezone ) );
					$final_booking->booking_created_at = esc_html( $booking_created_at->format( 'd/m/y H:i' ) );
					$final_booking->booking_date       = esc_html( $service_date->format( 'd/m/y H:i' ) );
				}
			}

			$statuses      = $bmrequests->bm_fetch_order_status_key_value();
			$total_records = $bookings != null && ! empty( $bookings ) ? count( $bookings ) : 0;
			$num_of_pages  = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = ! empty( $final_bookings ) ? array_values( $final_bookings ) : array();
			$data['saved_search']       = $saved_search;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['order_statuses']     = $statuses;
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_upcoming_orders()


	/**
	 * Search weekly order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_dashboard_weekly_orders() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler          = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$woocommerceservice = new WooCommerceService();
		$post               = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data               = array( 'status' => false );
		$statuses           = array();

		if ( $post != false && $post != null ) {
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$bookings     = array();

			if ( ! empty( $service_from ) && ! empty( $service_to ) ) {
				$dbhandler->update_global_option_value( 'bm_backend_dashboard_service_from_field', $service_from );
				$dbhandler->update_global_option_value( 'bm_backend_dashboard_service_to_field', $service_to );

				$service_from = $bmrequests->bm_convert_date_format( $service_from, 'd/m/y', 'Y-m-d' );
				$service_to   = $bmrequests->bm_convert_date_format( $service_to, 'd/m/y', 'Y-m-d' );
				$additional   = "booking_date BETWEEN '$service_from' AND '$service_to'";
				$bookings     = $dbhandler->get_all_result( 'BOOKING', '*', 1, 'results', 0, false, 'id', 'DESC', $additional );
			}

			/**if ( !empty( $bookings ) && is_array( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking->id, 'booking_id' );
					if ( $payment_status == 'requires_capture' || $payment_status == 'pending' ) {
						if ( isset( $bookings[ $key ] ) ) {
							unset( $bookings[ $key ] );
						}
					}
				}
				if ( !empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}*/

			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $bookings, $offset, $limit );
			$statuses       = $bmrequests->bm_fetch_order_status_key_value();

			if ( ! empty( $service_from ) || ! empty( $service_to ) ) {
				$total_records = $bookings != null && ! empty( $bookings ) ? count( $bookings ) : 0;
			} else {
				$total_records = $dbhandler->bm_count( 'BOOKING' );
			}

			$num_of_pages = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = ! empty( $final_bookings ) ? array_values( $final_bookings ) : array();
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['order_statuses']     = $statuses;
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_dashboard_weekly_orders()


	/**
	 * Search category wise order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_cat_wise_orders() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler          = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$woocommerceservice = new WooCommerceService();
		$post               = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data               = array( 'status' => false );
		$statuses           = array();

		if ( $post != false && $post != null ) {
			$type         = isset( $post['type'] ) ? $post['type'] : '';
			$category_ids = isset( $post['categories'] ) ? $post['categories'] : array();
			$base         = isset( $post['base'] ) ? $post['base'] : '';
			$limit        = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum      = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset       = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$bookings     = array();
			$additional   = '';

			if ( ! empty( $category_ids ) ) {
				$dbhandler->update_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids', $category_ids );
				$service_ids = $bmrequests->bm_fetch_service_id_by_category_id( $category_ids, 'string' );
				if ( ! empty( $service_ids ) ) {
					$additional = "AND service_id in ($service_ids)";
				}
				$bookings = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );
			}

			if ( isset( $bookings ) && ! empty( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$booking->category = $bmrequests->bm_fetch_category_name_by_service_id( $booking->service_id );
				}
			}

			/**if ( isset( $bookings ) && !empty( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking->id, 'booking_id' );
					if ( $payment_status == 'requires_capture' || $payment_status == 'pending' ) {
						if ( isset( $bookings[ $key ] ) ) {
							unset( $bookings[ $key ] );
						}
					} else {
						$booking->category = $bmrequests->bm_fetch_category_name_by_service_id( $booking->service_id );
					}
				}
				if ( !empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}*/

			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $bookings, $offset, $limit, true, 'total_cost', 'DESC' );

			if ( is_array( $final_bookings ) && ! empty( $final_bookings ) ) {
				$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

				foreach ( $final_bookings as $final_booking ) {
					$booking_slots                     = isset( $final_booking->booking_slots ) ? maybe_unserialize( $final_booking->booking_slots ) : array();
					$booking_created_at                = new DateTime( $final_booking->booking_created_at, new DateTimeZone( $timezone ) );
					$service_date                      = new DateTime( $final_booking->booking_date . ' ' . $booking_slots['from'], new DateTimeZone( $timezone ) );
					$final_booking->booking_created_at = esc_html( $booking_created_at->format( 'd/m/y H:i' ) );
					$final_booking->booking_date       = esc_html( $service_date->format( 'd/m/y H:i' ) );
				}
			}

			$statuses = $bmrequests->bm_fetch_order_status_key_value();

			$total_records = $bookings != null && ! empty( $bookings ) ? count( $bookings ) : 0;
			$num_of_pages  = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = ! empty( $final_bookings ) ? array_values( $final_bookings ) : array();
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['order_statuses']     = $statuses;
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_cat_wise_orders()


	/**
	 * Search revenue wise order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_wise_revenue() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type        = isset( $post['type'] ) ? $post['type'] : '';
			$services    = isset( $post['services'] ) ? $post['services'] : array();
			$base        = isset( $post['base'] ) ? $post['base'] : '';
			$limit       = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum     = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset      = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$additional  = "AND order_status not in('processing','pending', 'on_hold')";
			$bookings    = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );
			$result      = array();
			$service_ids = array();

			$total_cost          = array();
			$total_orders        = array();
			$total_slots_booked  = array();
			$total_ext_svc_slots = array();

			if ( ! empty( $bookings ) && is_array( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$service_id                  = isset( $booking->service_id ) ? $booking->service_id : 0;
					$total_booked_services       = isset( $booking->total_svc_slots ) ? $booking->total_svc_slots : 0;
					$total_booked_extra_services = isset( $booking->total_ext_svc_slots ) ? $booking->total_ext_svc_slots : 0;
					$service_total_cost          = isset( $booking->total_cost ) ? $booking->total_cost : 0;
					$service_ids[]               = $service_id;

					$total_orders[ $service_id ]             = isset( $total_orders[ $service_id ] ) ? $total_orders[ $service_id ] += 1 : 1;
					$total_slots_booked[ $service_id ]       = isset( $total_slots_booked[ $service_id ] ) ? $total_slots_booked[ $service_id ] += $total_booked_services : $total_booked_services;
					$total_extra_slots_booked[ $service_id ] = isset( $total_extra_slots_booked[ $service_id ] ) ? $total_extra_slots_booked[ $service_id ] += $total_booked_extra_services : $total_booked_extra_services;
					$total_cost[ $service_id ]               = isset( $total_cost[ $service_id ] ) ? $total_cost[ $service_id ] += $service_total_cost : $service_total_cost;
				}

				if ( ! empty( $service_ids ) ) {
					$service_ids = array_values( array_unique( $service_ids ) );
				}

				if ( ! empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}

			if ( ! empty( $bookings ) && ! empty( $service_ids ) ) {
				foreach ( $service_ids as $key => $service_id ) {
					$result[ $key ]['ordered_service']          = $bmrequests->bm_fetch_service_name_by_service_id( $service_id );
					$result[ $key ]['total_orders']             = isset( $total_orders[ $service_id ] ) ? $total_orders[ $service_id ] : 0;
					$result[ $key ]['total_slots_booked']       = isset( $total_slots_booked[ $service_id ] ) ? $total_slots_booked[ $service_id ] : 0;
					$result[ $key ]['total_extra_slots_booked'] = isset( $total_extra_slots_booked[ $service_id ] ) ? $total_extra_slots_booked[ $service_id ] : 0;
					$result[ $key ]['total_cost']               = isset( $total_cost[ $service_id ] ) ? $total_cost[ $service_id ] : 0;
				}
			}

			if ( ! empty( $services ) ) {
				$dbhandler->update_global_option_value( 'bm_backend_dashboard_revenue_wise_order_svc_search_ids', $services );
				$service_names = $bmrequests->bm_fetch_service_name_by_service_id( $services, 'string' );
				$conditions    = array( 'ordered_service in' => $service_names );
				$result        = $dbhandler->bm_apply_sql_conditions( $result, $conditions );
			}

			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $result, $offset, $limit, true, 'total_cost', 'DESC' );

			$total_records = ! empty( $result ) ? count( $result ) : 0;
			$num_of_pages  = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = $final_bookings;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_service_wise_revenue()


	/**
	 * Search datewise revenue order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_datewise_revenue_orders() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type          = isset( $post['type'] ) ? $post['type'] : '';
			$order_from    = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to      = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$base          = isset( $post['base'] ) ? $post['base'] : '';
			$limit         = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum       = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset        = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$additional    = "AND order_status not in('processing','pending', 'on_hold')";
			$bookings      = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );
			$ordered_dates = array();
			$result        = array();

			$total_cost               = array();
			$total_orders             = array();
			$total_extra_slots_booked = array();
			$total_ext_svc_slots      = array();
			$orderDatetime            = array();

			if ( ! empty( $bookings ) && is_array( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$ordered_date                = $bmrequests->bm_convert_date_format( $booking->booking_created_at, 'Y-m-d H:i:s', 'Y-m-d' );
					$total_booked_services       = isset( $booking->total_svc_slots ) ? $booking->total_svc_slots : 0;
					$total_booked_extra_services = isset( $booking->total_ext_svc_slots ) ? $booking->total_ext_svc_slots : 0;
					$service_total_cost          = isset( $booking->total_cost ) ? $booking->total_cost : 0;
					$ordered_dates[]             = $ordered_date;

					$orderDatetime[ $ordered_date ]            = isset( $booking->booking_created_at ) ? $booking->booking_created_at : '';
					$total_orders[ $ordered_date ]             = isset( $total_orders[ $ordered_date ] ) ? $total_orders[ $ordered_date ] += 1 : 1;
					$total_slots_booked[ $ordered_date ]       = isset( $total_slots_booked[ $ordered_date ] ) ? $total_slots_booked[ $ordered_date ] += $total_booked_services : $total_booked_services;
					$total_extra_slots_booked[ $ordered_date ] = isset( $total_extra_slots_booked[ $ordered_date ] ) ? $total_extra_slots_booked[ $ordered_date ] += $total_booked_extra_services : $total_booked_extra_services;
					$total_cost[ $ordered_date ]               = isset( $total_cost[ $ordered_date ] ) ? $total_cost[ $ordered_date ] += $service_total_cost : $service_total_cost;
				}

				if ( ! empty( $ordered_dates ) ) {
					$ordered_dates = array_values( array_unique( $ordered_dates ) );
				}

				if ( ! empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}

			if ( ! empty( $bookings ) && ! empty( $ordered_dates ) ) {
				foreach ( $ordered_dates as $key => $ordered_date ) {
					$result[ $key ]['order_date_time']          = isset( $orderDatetime[ $ordered_date ] ) ? $orderDatetime[ $ordered_date ] : 0;
					$result[ $key ]['ordered_date']             = $ordered_date;
					$result[ $key ]['total_orders']             = isset( $total_orders[ $ordered_date ] ) ? $total_orders[ $ordered_date ] : 0;
					$result[ $key ]['total_slots_booked']       = isset( $total_slots_booked[ $ordered_date ] ) ? $total_slots_booked[ $ordered_date ] : 0;
					$result[ $key ]['total_extra_slots_booked'] = isset( $total_extra_slots_booked[ $ordered_date ] ) ? $total_extra_slots_booked[ $ordered_date ] : 0;
					$result[ $key ]['total_cost']               = isset( $total_cost[ $ordered_date ] ) ? $total_cost[ $ordered_date ] : 0;
				}
			}

			if ( ! empty( $order_from ) && ! empty( $order_to ) && ! empty( $result ) ) {
				$dbhandler->update_global_option_value( 'bm_backend_dashboard_datewise_revenue_order_from_field', $order_from );
				$dbhandler->update_global_option_value( 'bm_backend_dashboard_datewise_revenue_order_to_field', $order_to );

				$order_from = $bmrequests->bm_convert_date_format( $order_from, 'd/m/y', 'Y-m-d' );
				$order_to   = $bmrequests->bm_convert_date_format( $order_to, 'd/m/y', 'Y-m-d' );
				$order_from = $order_from . ' 00:00:00';
				$order_to   = $order_to . ' 23:59:59';
				$result     = $bmrequests->bm_filter_results_by_date( $result, $order_from, $order_to, 'ordered_date' );
			}

			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $result, $offset, $limit, true, 'total_cost', 'DESC' );

			if ( is_array( $final_bookings ) && ! empty( $final_bookings ) ) {
				$timezone    = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
				$total_count = count( $final_bookings );

				for ( $i = 0; $i < $total_count; $i++ ) {
					$ordered_date                            = new DateTime( $final_bookings[ $i ]['order_date_time'], new DateTimeZone( $timezone ) );
					$final_bookings[ $i ]['order_date_time'] = esc_html( $ordered_date->format( 'd/m/y H:i' ) );
				}
			}

			$total_records = ! empty( $result ) ? count( $result ) : 0;
			$num_of_pages  = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = $final_bookings;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_datewise_revenue_orders()


	/**
	 * Search customer wise revenue order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_wise_revenue_orders() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type          = isset( $post['type'] ) ? $post['type'] : '';
			$search_string = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$base          = isset( $post['base'] ) ? $post['base'] : '';
			$limit         = isset( $post['limit'] ) ? $post['limit'] : false;
			$pagenum       = isset( $post['pagenum'] ) ? absint( $post['pagenum'] ) : 1;
			$offset        = isset( $post['limit'] ) ? ( ( $pagenum - 1 ) * $limit ) : 0;
			$additional    = "AND order_status not in('processing','pending', 'on_hold')";
			$bookings      = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'id', 'DESC', $additional );
			$customer_ids  = array();
			$result        = array();

			$total_cost               = array();
			$total_orders             = array();
			$total_slots_booked       = array();
			$total_extra_slots_booked = array();
			$dbhandler->update_global_option_value( 'bm_backend_dashboard_customer_wise_global_search_field', $search_string );

			/**$customer_data = $dbhandler->get_all_result( 'CUSTOMERS', '*', array( 'is_active' => 1 ), 'results' );*/

			if ( ! empty( $bookings ) && is_array( $bookings ) ) {
				foreach ( $bookings as $key => $booking ) {
					$customer_id                 = isset( $booking->customer_id ) ? $booking->customer_id : 0;
					$total_booked_services       = isset( $booking->total_svc_slots ) ? $booking->total_svc_slots : 0;
					$total_booked_extra_services = isset( $booking->total_ext_svc_slots ) ? $booking->total_ext_svc_slots : 0;
					$service_total_cost          = isset( $booking->total_cost ) ? $booking->total_cost : 0;
					$customer_ids[]              = $customer_id;

					$total_orders[ $customer_id ]             = isset( $total_orders[ $customer_id ] ) ? $total_orders[ $customer_id ] += 1 : 1;
					$total_slots_booked[ $customer_id ]       = isset( $total_slots_booked[ $customer_id ] ) ? $total_slots_booked[ $customer_id ] += $total_booked_services : $total_booked_services;
					$total_extra_slots_booked[ $customer_id ] = isset( $total_extra_slots_booked[ $customer_id ] ) ? $total_extra_slots_booked[ $customer_id ] += $total_booked_extra_services : $total_booked_extra_services;
					$total_cost[ $customer_id ]               = isset( $total_cost[ $customer_id ] ) ? $total_cost[ $customer_id ] += $service_total_cost : $service_total_cost;
				}

				if ( ! empty( $customer_ids ) ) {
					$customer_ids = array_values( array_unique( $customer_ids ) );
				}

				if ( ! empty( $bookings ) ) {
					$bookings = array_values( $bookings );
				}
			}

			if ( ! empty( $bookings ) && ! empty( $customer_ids ) ) {
				foreach ( $customer_ids as $key => $customer_id ) {
					$result[ $key ]['customer_name']            = $dbhandler->get_value( 'CUSTOMERS', 'customer_name', $customer_id, 'id' );
					$result[ $key ]['customer_email']           = $dbhandler->get_value( 'CUSTOMERS', 'customer_email', $customer_id, 'id' );
					$result[ $key ]['total_orders']             = isset( $total_orders[ $customer_id ] ) ? $total_orders[ $customer_id ] : 0;
					$result[ $key ]['total_slots_booked']       = isset( $total_slots_booked[ $customer_id ] ) ? $total_slots_booked[ $customer_id ] : 0;
					$result[ $key ]['total_extra_slots_booked'] = isset( $total_extra_slots_booked[ $customer_id ] ) ? $total_extra_slots_booked[ $customer_id ] : 0;
					$result[ $key ]['total_cost']               = isset( $total_cost[ $customer_id ] ) ? $total_cost[ $customer_id ] : 0;
				}
			}

			if ( ! empty( $search_string ) && ! empty( $result ) ) {
				$result = $bmrequests->bm_search_string_from_existing_dataset( $result, $search_string );
			}

			$final_bookings = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $result, $offset, $limit, true, 'total_cost', 'DESC' );

			$total_records = ! empty( $result ) ? count( $result ) : 0;
			$num_of_pages  = isset( $limit ) ? ceil( $total_records / $limit ) : 1;
			$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $base, 'list' );

			$data['status']             = true;
			$data['bookings']           = $final_bookings;
			$data['current_pagenumber'] = ( 1 + $offset );
			$data['pagination']         = wp_kses_post( $pagination ?? '' );
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_customer_wise_revenue_orders()


	/**
	 * Fetch booking counts
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_counts() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type   = isset( $post['type'] ) ? $post['type'] : '';
			$year   = isset( $post['year'] ) ? $post['year'] : '';
			$month  = isset( $post['month'] ) ? $post['month'] : '';
			$status = isset( $post['status'] ) ? $post['status'] : '';

			$data['status'] = true;

			if ( $type == '' ) {
				$data['total_bookings_count']    = $bmrequests->bm_fetch_total_bookings_count( $year, $month, $status );
				$data['upcoming_bookings_count'] = $bmrequests->bm_fetch_upcoming_bookings_count( $year, $month, $status );
				$data['weekly_bookings_count']   = $bmrequests->bm_fetch_weekly_bookings_count( $status );
				$data['total_bookings_revenue']  = $bmrequests->bm_fetch_total_bookings_revenue( $year, $month, $status );
			} elseif ( $type == 'total' ) {
				$data['total_bookings_count'] = $bmrequests->bm_fetch_total_bookings_count( $year, $month, $status );
			} elseif ( $type == 'upcoming' ) {
				$data['upcoming_bookings_count'] = $bmrequests->bm_fetch_upcoming_bookings_count( $year, $month, $status );
			} elseif ( $type == 'revenue' ) {
				$data['total_bookings_revenue'] = $bmrequests->bm_fetch_total_bookings_revenue( $year, $month, $status );
			} elseif ( $type == 'weekly' ) {
				$data['weekly_bookings_count'] = $bmrequests->bm_fetch_weekly_bookings_count( $status );
			}

			$data['booking_type'] = $type;
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_booking_counts()


	/**
	 * Fetch customer and total booked slot counts
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_and_total_booked_slot_counts() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type = isset( $post['type'] ) ? $post['type'] : '';

			$data['status']                = true;
			$data['total_customers_count'] = $bmrequests->bm_fetch_total_customers_count( $type );
			$data['slots_booked_count']    = $bmrequests->bm_fetch_total_slot_bookings_count( $type );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_customer_and_total_booked_slot_counts()


	/**
	 * Fetch booking status counts
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_status_counts() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$data['status'] = true;
			$status_data    = $bmrequests->bm_fetch_booking_status_count( $post );
			$data['labels'] = isset( $status_data['labels'] ) ? $status_data['labels'] : array();
			$data['data']   = isset( $status_data['data'] ) ? $status_data['data'] : array();
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_booking_status_counts()


	/**
	 * Fetch booking overview data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_overview() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$data       = array();

		$comparison_data = $bmrequests->bm_fetch_booking_comparison_as_per_prev_month();
		if ( empty( $comparison_data ) ) {
			$comparison_data = '<div class="textcenter">' . esc_html__( 'Comparison Could not be done.', 'service-booking' ) . '</div>';
		}

		$overview_data = $bmrequests->bm_fetch_booking_overview_data();
		if ( empty( $overview_data ) ) {
			$overview_data = '<div class="textcenter">' . esc_html__( 'No Services Booked in last 7 days.', 'service-booking' ) . '</div>';
		}

		$data['comparison'] = wp_kses_post( $comparison_data );
		$data['data']       = wp_kses_post( $overview_data );

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_booking_overview()

    /**
     * Fetch overview, revenue, or products data.
     */
    public function bm_fetch_analytics_data_callback() {
        $nonce = filter_input( INPUT_POST, 'nonce' );
        if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
            wp_die( esc_html__( 'Failed security check', 'service-booking' ) );
        }

        $bmrequests = new BM_Request();
        $post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

        $date_from    = ! empty( $post['date_from'] ) ? $bmrequests->bm_convert_date_format( $post['date_from'], 'd/m/Y', 'Y-m-d' ) : gmdate( 'Y-m-01' );
        $date_to      = ! empty( $post['date_to'] ) ? $bmrequests->bm_convert_date_format( $post['date_to'], 'd/m/Y', 'Y-m-d' ) : gmdate( 'Y-m-t' );
        $compare_from = ! empty( $post['compare_from'] ) ? $bmrequests->bm_convert_date_format( $post['compare_from'], 'd/m/Y', 'Y-m-d' ) : '';
        $compare_to   = ! empty( $post['compare_to'] ) ? $bmrequests->bm_convert_date_format( $post['compare_to'], 'd/m/Y', 'Y-m-d' ) : '';
        $compare_type = ! empty( $post['compare_type'] ) ? sanitize_text_field( $post['compare_type'] ) : 'period';
        $action_type  = ! empty( $post['action_type'] ) ? sanitize_text_field( $post['action_type'] ) : 'overview';
        $category_id  = isset( $post['category_id'] ) && $post['category_id'] !== '' ? $post['category_id'] : '';
        $service_id   = isset( $post['service_id'] ) && $post['service_id'] !== '' ? $post['service_id'] : '';
        $metric       = isset( $post['metric'] ) ? sanitize_text_field( $post['metric'] ) : '';

        $response = array( 'status' => false );

        try {
            switch ( $action_type ) {
                case 'overview':
                    $response = $this->bm_get_overview_analytics( $date_from, $date_to, $compare_from, $compare_to, $compare_type );
                    break;
                case 'revenue':
                    $response = $this->bm_get_revenue_analytics( $date_from, $date_to, $compare_from, $compare_to, $compare_type );
                    break;
                case 'products':
                    $response = $this->bm_get_products_analytics( $date_from, $date_to, $compare_from, $compare_to, $category_id, $service_id, $compare_type );
                    break;
                case 'orders':
                    $filters  = isset( $post['filters'] ) ? (array) $post['filters'] : array();
                    $response = $this->bm_get_orders_analytics( $date_from, $date_to, $compare_from, $compare_to, $compare_type, $filters );
                    break;
                case 'metric_chart':
                    $response = $this->bm_get_metric_chart_data( $date_from, $date_to, $compare_from, $compare_to, $compare_type, $metric );
                    break;
                default:
                    $response = $this->bm_get_overview_analytics( $date_from, $date_to, $compare_from, $compare_to, $compare_type );
            }
            $response['status'] = true;
        } catch ( Exception $e ) {
            $response['error'] = $e->getMessage();
        }

        wp_send_json( $response );
    }

    /**
     * Get overview analytics (metrics, charts, leaderboards).
     */
    public function bm_get_overview_analytics( $date_from, $date_to, $compare_from = '', $compare_to = '', $compare_type = 'period' ) {
        $current_data  = $this->bm_get_analytics_data_join( $date_from, $date_to );
        $previous_data = ! empty( $compare_from ) && ! empty( $compare_to )
            ? $this->bm_get_analytics_data_join( $compare_from, $compare_to )
            : array(
                'total_sales'         => 0,
                'net_sales'           => 0,
                'total_orders'        => 0,
                'services_sold'       => 0,
                'extra_services_sold' => 0,
            );

        $response = array(
            'total_sales'                => $current_data['total_sales'],
            'total_sales_change'         => $this->bm_calculate_change( $previous_data['total_sales'], $current_data['total_sales'] ),
            'net_sales'                  => $current_data['net_sales'],
            'net_sales_change'           => $this->bm_calculate_change( $previous_data['net_sales'], $current_data['net_sales'] ),
            'total_orders'               => $current_data['total_orders'],
            'total_orders_change'        => $this->bm_calculate_change( $previous_data['total_orders'], $current_data['total_orders'] ),
            'services_sold'              => $current_data['services_sold'],
            'services_sold_change'       => $this->bm_calculate_change( $previous_data['services_sold'], $current_data['services_sold'] ),
            'extra_services_sold'        => $current_data['extra_services_sold'],
            'extra_services_sold_change' => $this->bm_calculate_change( $previous_data['extra_services_sold'], $current_data['extra_services_sold'] ),
        );

        // Charts
        $chart_data = $this->bm_get_chart_data_join( $date_from, $date_to, $compare_from, $compare_to, $compare_type );
        $response   = array_merge( $response, $chart_data );

        // Leaderboards
        $response['top_categories'] = $this->bm_get_top_categories_join( $date_from, $date_to, 5 );
        $response['top_services']   = $this->bm_get_top_services_join( $date_from, $date_to, 5 );

        return $response;
    }

    /**
     * Get revenue analytics.
     */
    public function bm_get_revenue_analytics( $date_from, $date_to, $compare_from = '', $compare_to = '', $compare_type = '' ) {
        $current_revenue  = $this->bm_get_revenue_data_join( $date_from, $date_to );
        $previous_revenue = ! empty( $compare_from ) && ! empty( $compare_to )
            ? $this->bm_get_revenue_data_join( $compare_from, $compare_to )
            : array(
                'gross_sales' => 0,
                'returns'     => 0,
                'coupons'     => 0,
                'net_sales'   => 0,
            );

        $response = array(
            'gross_sales'        => $current_revenue['gross_sales'],
            'gross_sales_change' => $this->bm_calculate_change( $previous_revenue['gross_sales'], $current_revenue['gross_sales'] ),
            'returns'            => $current_revenue['returns'],
            'returns_change'     => $this->bm_calculate_change( $previous_revenue['returns'], $current_revenue['returns'] ),
            'coupons'            => $current_revenue['coupons'],
            'coupons_change'     => $this->bm_calculate_change( $previous_revenue['coupons'], $current_revenue['coupons'] ),
            'net_sales'          => $current_revenue['net_sales'],
            'net_sales_change'   => $this->bm_calculate_change( $previous_revenue['net_sales'], $current_revenue['net_sales'] ),
        );

        // Daily breakdown
        $response['daily_revenue'] = $this->bm_get_daily_revenue_join( $date_from, $date_to );

        // Revenue chart
        $revenue_chart_data = $this->bm_get_revenue_chart_data_join( $date_from, $date_to );
        $response           = array_merge( $response, $revenue_chart_data );

        return $response;
    }

    /**
     * Get products analytics.
     */
    public function bm_get_products_analytics( $date_from, $date_to, $compare_from = '', $compare_to = '', $category_id = '', $service_id = '', $compare_type = '' ) {
        $current_products  = $this->bm_get_products_data_join( $date_from, $date_to, $category_id, $service_id );
        $previous_products = ! empty( $compare_from ) && ! empty( $compare_to )
            ? $this->bm_get_products_data_join( $compare_from, $compare_to, $category_id, $service_id )
            : array(
                'items_sold'   => 0,
                'net_sales'    => 0,
                'total_orders' => 0,
            );

        $response = array(
            'items_sold'          => $current_products['items_sold'],
            'items_sold_change'   => $this->bm_calculate_change( $previous_products['items_sold'], $current_products['items_sold'] ),
            'net_sales'           => $current_products['net_sales'],
            'net_sales_change'    => $this->bm_calculate_change( $previous_products['net_sales'], $current_products['net_sales'] ),
            'total_orders'        => $current_products['total_orders'],
            'total_orders_change' => $this->bm_calculate_change( $previous_products['total_orders'], $current_products['total_orders'] ),
        );

        // Product list
        $response['products'] = $this->bm_get_products_performance_join( $date_from, $date_to, $category_id, $service_id );

        // Items sold chart
        $items_chart_data = $this->bm_get_items_sold_chart_data_join( $date_from, $date_to, $compare_from, $compare_to, $category_id, $service_id, $compare_type );
        $response         = array_merge( $response, $items_chart_data );

        return $response;
    }

    // ------------------------------------------------------------
    // 2. DATA JOIN HELPERS (Existing, correctly calculating net sales)
    // ------------------------------------------------------------

    /**
     * Get aggregated analytics data for a date range.
     */
    public function bm_get_analytics_data_join( $date_from, $date_to ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'SUM(b.total_cost) as total_sales,
             SUM(COALESCE(b.disount_amount, 0)) as total_discounts,
             SUM(b.total_cost - COALESCE(b.disount_amount, 0)) as net_before_returns,
             COUNT(DISTINCT b.id) as total_orders,
             SUM(b.total_svc_slots) as services_sold,
             SUM(b.total_ext_svc_slots) as extra_services_sold',
            array(),
            $where,
            'row'
        );

        // Returns
        $returns_where = array(
            't.payment_status' => array( '=' => 'refunded' ),
        );
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $returns_where['t.transaction_created_at'] = array(
                '>=' => $date_from . ' 00:00:00',
                '<=' => $date_to . ' 23:59:59',
            );
        }

        $returns = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'SUM(t.paid_amount) as total_returns',
            array(),
            $returns_where,
            'row'
        );

        $total_sales     = $results ? floatval( $results->total_sales ) : 0;
        $total_discounts = $results ? floatval( $results->total_discounts ) : 0;
        $total_returns   = $returns ? floatval( $returns->total_returns ) : 0;
        $net_sales       = $total_sales - $total_discounts - $total_returns;

        return array(
            'total_sales'         => $total_sales,
            'total_discounts'     => $total_discounts,
            'total_returns'       => $total_returns,
            'net_sales'           => $net_sales,
            'total_orders'        => $results ? intval( $results->total_orders ) : 0,
            'services_sold'       => $results ? intval( $results->services_sold ) : 0,
            'extra_services_sold' => $results ? intval( $results->extra_services_sold ) : 0,
        );
    }

    /**
     * Get revenue data for a date range.
     */
    public function bm_get_revenue_data_join( $date_from, $date_to ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as coupons,
             SUM(b.total_cost - COALESCE(b.disount_amount, 0)) as net_before_returns,
             COUNT(DISTINCT b.id) as total_orders',
            array(),
            $where,
            'row'
        );

        // Returns
        $returns_where = array(
            't.payment_status' => array( '=' => 'refunded' ),
        );
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $returns_where['t.transaction_created_at'] = array(
                '>=' => $date_from . ' 00:00:00',
                '<=' => $date_to . ' 23:59:59',
            );
        }

        $returns = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'SUM(t.paid_amount) as total_returns',
            array(),
            $returns_where,
            'row'
        );

        $gross_sales   = $results ? floatval( $results->gross_sales ) : 0;
        $coupons       = $results ? floatval( $results->coupons ) : 0;
        $total_returns = $returns ? floatval( $returns->total_returns ) : 0;
        $net_sales     = $gross_sales - $coupons - $total_returns;

        return array(
            'gross_sales'  => $gross_sales,
            'returns'      => $total_returns,
            'coupons'      => $coupons,
            'net_sales'    => $net_sales,
            'total_orders' => $results ? intval( $results->total_orders ) : 0,
        );
    }

    /**
     * Daily revenue breakdown for a date range.
     */
    public function bm_get_daily_revenue_join( $date_from, $date_to ) {
        $dbhandler     = new BM_DBhandler();
        $date_range    = $this->bm_generate_date_range( $date_from, $date_to );
        $daily_revenue = array();

        foreach ( $date_range as $date ) {
            $where = array(
                'b.booking_date' => array( '=' => $date ),
                'b.order_status' => array( '=' => 'succeeded' ),
            );

            $results = $dbhandler->get_results_with_join(
                array( 'BOOKING', 'b' ),
                'COUNT(DISTINCT b.id) as orders,
                 SUM(b.total_cost) as gross_sales,
                 SUM(COALESCE(b.disount_amount, 0)) as coupons',
                array(),
                $where,
                'row'
            );

            // Returns for the day
            $returns_where = array(
                't.payment_status'         => array( '=' => 'refunded' ),
                't.transaction_created_at' => array(
                    '>=' => $date . ' 00:00:00',
                    '<=' => $date . ' 23:59:59',
                ),
            );

            $returns = $dbhandler->get_results_with_join(
                array( 'TRANSACTIONS', 't' ),
                'SUM(t.paid_amount) as total_returns',
                array(),
                $returns_where,
                'row'
            );

            $gross_sales   = $results ? floatval( $results->gross_sales ) : 0;
            $coupons       = $results ? floatval( $results->coupons ) : 0;
            $total_returns = $returns ? floatval( $returns->total_returns ) : 0;
            $net_sales     = $gross_sales - $coupons - $total_returns;

            $daily_revenue[] = array(
                'date'        => gmdate( 'd/m/Y', strtotime( $date ) ),
                'orders'      => $results ? intval( $results->orders ) : 0,
                'gross_sales' => $gross_sales,
                'returns'     => $total_returns,
                'coupons'     => $coupons,
                'net_sales'   => $net_sales,
                'taxes'       => 0,
                'shipping'    => 0,
                'total_sales' => $net_sales,
            );
        }

        return $daily_revenue;
    }

    /**
     * Get aggregated products data (items sold, net sales, orders) with filters.
     */
    public function bm_get_products_data_join( $date_from, $date_to, $category_id = '', $service_id = '' ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        if ( $service_id !== '' ) {
            $where['b.service_id'] = array( '=' => $service_id );
        }

        $joins = array();
        if ( $category_id !== '' ) {
            $joins[]                     = array(
                'table' => 'SERVICE',
                'alias' => 's',
                'on'    => 'b.service_id = s.id',
                'type'  => 'LEFT',
            );
            $where['s.service_category'] = array( '=' => $category_id );
        }

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'SUM(b.total_svc_slots + b.total_ext_svc_slots) as items_sold,
             SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as total_discounts,
             COUNT(DISTINCT b.id) as total_orders',
            $joins,
            $where,
            'row'
        );

        // Returns
        $returns_where = array(
            't.payment_status' => array( '=' => 'refunded' ),
        );
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $returns_where['t.transaction_created_at'] = array(
                '>=' => $date_from . ' 00:00:00',
                '<=' => $date_to . ' 23:59:59',
            );
        }

        $returns_joins = array();
        if ( $service_id !== '' ) {
            $returns_joins[]                = array(
                'table' => 'BOOKING',
                'alias' => 'b2',
                'on'    => 't.booking_id = b2.id',
                'type'  => 'INNER',
            );
            $returns_where['b2.service_id'] = array( '=' => $service_id );
        }
        if ( $category_id !== '' ) {
            $returns_joins[]                      = array(
                'table' => 'BOOKING',
                'alias' => 'b3',
                'on'    => 't.booking_id = b3.id',
                'type'  => 'INNER',
            );
            $returns_joins[]                      = array(
                'table' => 'SERVICE',
                'alias' => 's2',
                'on'    => 'b3.service_id = s2.id',
                'type'  => 'LEFT',
            );
            $returns_where['s2.service_category'] = array( '=' => $category_id );
        }

        $returns = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'SUM(t.paid_amount) as total_returns',
            $returns_joins,
            $returns_where,
            'row'
        );

        $items_sold      = $results ? intval( $results->items_sold ) : 0;
        $gross_sales     = $results ? floatval( $results->gross_sales ) : 0;
        $total_discounts = $results ? floatval( $results->total_discounts ) : 0;
        $total_returns   = $returns ? floatval( $returns->total_returns ) : 0;
        $net_sales       = $gross_sales - $total_discounts - $total_returns;

        return array(
            'items_sold'      => $items_sold,
            'gross_sales'     => $gross_sales,
            'total_discounts' => $total_discounts,
            'total_returns'   => $total_returns,
            'net_sales'       => $net_sales,
            'total_orders'    => $results ? intval( $results->total_orders ) : 0,
        );
    }

    /**
     * Get detailed performance for each service (products table).
     */
    public function bm_get_products_performance_join( $date_from, $date_to, $category_id = '', $service_id = '' ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        if ( $service_id !== '' ) {
            $where['b.service_id'] = array( '=' => $service_id );
        }

        $joins = array(
            array(
                'table' => 'SERVICE',
                'alias' => 's',
                'on'    => 'b.service_id = s.id',
                'type'  => 'LEFT',
            ),
            array(
                'table' => 'CATEGORY',
                'alias' => 'c',
                'on'    => 's.service_category = c.id',
                'type'  => 'LEFT',
            ),
        );

        if ( $category_id !== '' ) {
            $where['s.service_category'] = array( '=' => $category_id );
        }

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'b.service_id,
             b.service_name,
             COALESCE(c.cat_name, "Uncategorized") as category,
             SUM(b.total_svc_slots + b.total_ext_svc_slots) as items_sold,
             SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as total_discounts,
             COUNT(DISTINCT b.id) as orders',
            $joins,
            $where,
            'results',
            0,
            false,
            'items_sold DESC',
            false,
            'GROUP BY b.service_id'
        );

        $products = array();
        if ( ! empty( $results ) && is_array( $results ) ) {
            foreach ( $results as $result ) {
                // Returns per service
                $returns_where = array(
                    't.payment_status' => array( '=' => 'refunded' ),
                );
                if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
                    $returns_where['t.transaction_created_at'] = array(
                        '>=' => $date_from . ' 00:00:00',
                        '<=' => $date_to . ' 23:59:59',
                    );
                }
                $returns_joins                  = array(
                    array(
                        'table' => 'BOOKING',
                        'alias' => 'b2',
                        'on'    => 't.booking_id = b2.id',
                        'type'  => 'INNER',
                    ),
                );
                $returns_where['b2.service_id'] = array( '=' => $result->service_id );

                $returns = $dbhandler->get_results_with_join(
                    array( 'TRANSACTIONS', 't' ),
                    'SUM(t.paid_amount) as service_returns',
                    $returns_joins,
                    $returns_where,
                    'row'
                );

                $gross_sales     = floatval( $result->gross_sales );
                $total_discounts = floatval( $result->total_discounts );
                $service_returns = $returns ? floatval( $returns->service_returns ) : 0;
                $net_sales       = $gross_sales - $total_discounts - $service_returns;

                $products[] = array(
                    'id'         => $result->service_id,
                    'name'       => $result->service_name,
                    'category'   => $result->category,
                    'items_sold' => intval( $result->items_sold ),
                    'net_sales'  => $net_sales,
                    'orders'     => intval( $result->orders ),
                    'visits'     => 0,
                );
            }
        }

        return $products;
    }

    /**
     * Get top categories by services sold.
     */
    public function bm_get_top_categories_join( $date_from, $date_to, $limit = 5 ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        $joins = array(
            array(
                'table' => 'SERVICE',
                'alias' => 's',
                'on'    => 'b.service_id = s.id',
                'type'  => 'LEFT',
            ),
            array(
                'table' => 'CATEGORY',
                'alias' => 'c',
                'on'    => 's.service_category = c.id',
                'type'  => 'LEFT',
            ),
        );

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COALESCE(c.cat_name, "Uncategorized") as category_name,
             COALESCE(c.id, 0) as category_id,
             SUM(b.total_svc_slots) as services_sold,
             SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as total_discounts',
            $joins,
            $where,
            'results',
            0,
            $limit,
            'services_sold',
            true,
            'GROUP BY COALESCE(c.id, 0)'
        );

        $categories = array();
        if ( $results ) {
            foreach ( $results as $result ) {
                // Returns per category
                $returns_where = array(
                    't.payment_status' => array( '=' => 'refunded' ),
                );
                if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
                    $returns_where['t.transaction_created_at'] = array(
                        '>=' => $date_from . ' 00:00:00',
                        '<=' => $date_to . ' 23:59:59',
                    );
                }
                $returns_joins = array(
                    array(
                        'table' => 'BOOKING',
                        'alias' => 'b2',
                        'on'    => 't.booking_id = b2.id',
                        'type'  => 'INNER',
                    ),
                    array(
                        'table' => 'SERVICE',
                        'alias' => 's2',
                        'on'    => 'b2.service_id = s2.id',
                        'type'  => 'LEFT',
                    ),
                );
                if ( $result->category_id == 0 ) {
                    $returns_where[] = '(s2.service_category IS NULL OR s2.service_category = 0)';
                } else {
                    $returns_where['s2.service_category'] = array( '=' => $result->category_id );
                }

                $returns = $dbhandler->get_results_with_join(
                    array( 'TRANSACTIONS', 't' ),
                    'SUM(t.paid_amount) as total_returns',
                    $returns_joins,
                    $returns_where,
                    'row'
                );

                $gross_sales    = floatval( $result->gross_sales );
                $discounts      = floatval( $result->total_discounts );
                $returns_amount = $returns ? floatval( $returns->total_returns ) : 0;
                $net_sales      = $gross_sales - $discounts - $returns_amount;

                $categories[] = array(
                    'id'            => $result->category_id,
                    'name'          => $result->category_name,
                    'services_sold' => intval( $result->services_sold ),
                    'net_sales'     => $net_sales,
                );
            }
        }

        return $categories;
    }

    /**
     * Get top services by services sold.
     */
    public function bm_get_top_services_join( $date_from, $date_to, $limit = 5 ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'b.service_id, b.service_name,
             SUM(b.total_svc_slots) as services_sold,
             SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as total_discounts',
            array(),
            $where,
            'results',
            0,
            $limit,
            'services_sold',
            true,
            'GROUP BY b.service_id'
        );

        $services = array();
        if ( $results ) {
            foreach ( $results as $result ) {
                // Returns per service
                $returns_where = array(
                    't.payment_status' => array( '=' => 'refunded' ),
                );
                if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
                    $returns_where['t.transaction_created_at'] = array(
                        '>=' => $date_from . ' 00:00:00',
                        '<=' => $date_to . ' 23:59:59',
                    );
                }
                $returns_joins                  = array(
                    array(
                        'table' => 'BOOKING',
                        'alias' => 'b2',
                        'on'    => 't.booking_id = b2.id',
                        'type'  => 'INNER',
                    ),
                );
                $returns_where['b2.service_id'] = array( '=' => $result->service_id );

                $returns = $dbhandler->get_results_with_join(
                    array( 'TRANSACTIONS', 't' ),
                    'SUM(t.paid_amount) as total_returns',
                    $returns_joins,
                    $returns_where,
                    'row'
                );

                $gross_sales    = floatval( $result->gross_sales );
                $discounts      = floatval( $result->total_discounts );
                $returns_amount = $returns ? floatval( $returns->total_returns ) : 0;
                $net_sales      = $gross_sales - $discounts - $returns_amount;

                $services[] = array(
                    'id'            => $result->service_id,
                    'name'          => $result->service_name,
                    'services_sold' => intval( $result->services_sold ),
                    'net_sales'     => $net_sales,
                );
            }
        }

        return $services;
    }

    // ------------------------------------------------------------
    // 3. CHART HELPERS (with comparison type)
    // ------------------------------------------------------------

    /**
     * Get chart data for net sales and orders (overview).
     */
    public function bm_get_chart_data_join( $date_from, $date_to, $compare_from = '', $compare_to = '', $compare_type = 'period' ) {
        $date_range = $this->bm_generate_date_range( $date_from, $date_to );
        $response   = array(
            'chart_labels'            => array(),
            'current_net_sales_data'  => array(),
            'previous_net_sales_data' => array(),
            'current_orders_data'     => array(),
            'previous_orders_data'    => array(),
        );

        foreach ( $date_range as $date ) {
            $response['chart_labels'][] = gmdate( 'd M', strtotime( $date ) );

            $current                              = $this->bm_get_daily_data_join( $date );
            $response['current_net_sales_data'][] = $current['net_sales'];
            $response['current_orders_data'][]    = $current['orders'];

            if ( ! empty( $compare_from ) && ! empty( $compare_to ) ) {
                $prev_date                             = $this->bm_get_comparison_date( $date, $date_from, $date_to, $compare_from, $compare_to, $compare_type );
                $previous                              = $prev_date ? $this->bm_get_daily_data_join( $prev_date ) : array(
					'net_sales' => 0,
					'orders'    => 0,
				);
                $response['previous_net_sales_data'][] = $previous['net_sales'];
                $response['previous_orders_data'][]    = $previous['orders'];
            } else {
                $response['previous_net_sales_data'][] = 0;
                $response['previous_orders_data'][]    = 0;
            }
        }

        return $response;
    }

    /**
     * Get chart data for revenue trends (gross, returns, coupons, net).
     */
    public function bm_get_revenue_chart_data_join( $date_from, $date_to ) {
        $date_range = $this->bm_generate_date_range( $date_from, $date_to );
        $response   = array(
            'chart_labels'     => array(),
            'gross_sales_data' => array(),
            'returns_data'     => array(),
            'coupons_data'     => array(),
            'net_sales_data'   => array(),
        );

        foreach ( $date_range as $date ) {
            $response['chart_labels'][]     = gmdate( 'd M', strtotime( $date ) );
            $daily                          = $this->bm_get_daily_revenue_single_join( $date );
            $response['gross_sales_data'][] = $daily['gross_sales'];
            $response['returns_data'][]     = $daily['returns'];
            $response['coupons_data'][]     = $daily['coupons'];
            $response['net_sales_data'][]   = $daily['net_sales'];
        }

        return $response;
    }

    /**
     * Get chart data for items sold trend.
     */
    public function bm_get_items_sold_chart_data_join( $date_from, $date_to, $compare_from = '', $compare_to = '', $category_id = '', $service_id = '', $compare_type = 'period' ) {
        $date_range = $this->bm_generate_date_range( $date_from, $date_to );
        $response   = array(
            'chart_labels'             => array(),
            'current_items_sold_data'  => array(),
            'previous_items_sold_data' => array(),
        );

        foreach ( $date_range as $date ) {
            $response['chart_labels'][]            = gmdate( 'd M', strtotime( $date ) );
            $current                               = $this->bm_get_daily_items_sold_join( $date, $category_id, $service_id );
            $response['current_items_sold_data'][] = $current;

            if ( ! empty( $compare_from ) && ! empty( $compare_to ) ) {
                $prev_date                              = $this->bm_get_comparison_date( $date, $date_from, $date_to, $compare_from, $compare_to, $compare_type );
                $previous                               = $prev_date ? $this->bm_get_daily_items_sold_join( $prev_date, $category_id, $service_id ) : 0;
                $response['previous_items_sold_data'][] = $previous;
            } else {
                $response['previous_items_sold_data'][] = 0;
            }
        }

        return $response;
    }

    /**
     * Get daily data (orders, net sales) for a single date.
     */
    public function bm_get_daily_data_join( $date ) {
        $dbhandler = new BM_DBhandler();

        $where = array(
            'b.booking_date' => array( '=' => $date ),
            'b.order_status' => array( '=' => 'succeeded' ),
        );

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COUNT(DISTINCT b.id) as orders,
             SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as discounts',
            array(),
            $where,
            'row'
        );

        $returns_where = array(
            't.payment_status'         => array( '=' => 'refunded' ),
            't.transaction_created_at' => array(
                '>=' => $date . ' 00:00:00',
                '<=' => $date . ' 23:59:59',
            ),
        );

        $returns = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'SUM(t.paid_amount) as total_returns',
            array(),
            $returns_where,
            'row'
        );

        $gross_sales   = $results ? floatval( $results->gross_sales ) : 0;
        $discounts     = $results ? floatval( $results->discounts ) : 0;
        $total_returns = $returns ? floatval( $returns->total_returns ) : 0;
        $net_sales     = $gross_sales - $discounts - $total_returns;

        return array(
            'orders'    => $results ? intval( $results->orders ) : 0,
            'net_sales' => $net_sales,
        );
    }

    /**
     * Get daily revenue breakdown for a single date.
     */
    public function bm_get_daily_revenue_single_join( $date ) {
        $dbhandler = new BM_DBhandler();

        $where = array(
            'b.booking_date' => array( '=' => $date ),
            'b.order_status' => array( '=' => 'succeeded' ),
        );

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'SUM(b.total_cost) as gross_sales,
             SUM(COALESCE(b.disount_amount, 0)) as coupons',
            array(),
            $where,
            'row'
        );

        $returns_where = array(
            't.payment_status'         => array( '=' => 'refunded' ),
            't.transaction_created_at' => array(
                '>=' => $date . ' 00:00:00',
                '<=' => $date . ' 23:59:59',
            ),
        );

        $returns = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'SUM(t.paid_amount) as total_returns',
            array(),
            $returns_where,
            'row'
        );

        $gross_sales   = $results ? floatval( $results->gross_sales ) : 0;
        $coupons       = $results ? floatval( $results->coupons ) : 0;
        $total_returns = $returns ? floatval( $returns->total_returns ) : 0;
        $net_sales     = $gross_sales - $coupons - $total_returns;

        return array(
            'gross_sales' => $gross_sales,
            'returns'     => $total_returns,
            'coupons'     => $coupons,
            'net_sales'   => $net_sales,
        );
    }

    /**
     * Get daily items sold for a single date, with optional filters.
     */
    public function bm_get_daily_items_sold_join( $date, $category_id = '', $service_id = '' ) {
        $dbhandler = new BM_DBhandler();

        $where = array(
            'b.booking_date' => array( '=' => $date ),
            'b.order_status' => array( '=' => 'succeeded' ),
        );

        if ( $service_id !== '' ) {
            $where['b.service_id'] = array( '=' => $service_id );
        }

        $joins = array();
        if ( $category_id != '' ) {
            $joins[]                     = array(
                'table' => 'SERVICE',
                'alias' => 's',
                'on'    => 'b.service_id = s.id',
                'type'  => 'LEFT',
            );
            $where['s.service_category'] = array( '=' => $category_id );
        }

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'SUM(b.total_svc_slots + b.total_ext_svc_slots) as items_sold',
            $joins,
            $where,
            'row'
        );

        return $results ? intval( $results->items_sold ) : 0;
    }

    // ------------------------------------------------------------
    // 4. UTILITY HELPERS
    // ------------------------------------------------------------

    /**
     * Calculate percentage change.
     */
    public function bm_calculate_change( $previous, $current ) {
        if ( $previous == 0 ) {
            return $current > 0 ? 100 : 0;
        }
        return round( ( ( $current - $previous ) / $previous ) * 100, 2 );
    }

    /**
     * Generate array of dates between start and end.
     */
    public function bm_generate_date_range( $start_date, $end_date, $format = 'Y-m-d' ) {
        $dates   = array();
        $current = strtotime( $start_date );
        $end     = strtotime( $end_date );

        while ( $current <= $end ) {
            $dates[] = gmdate( $format, $current );
            $current = strtotime( '+1 day', $current );
        }
        return $dates;
    }

    /**
     * Get comparison date for period/year over year.
     */
    public function bm_get_comparison_date( $current_date, $current_start, $current_end, $compare_start, $compare_end, $compare_type = 'period' ) {
        if ( $compare_type === 'year' ) {
            $current_ts      = strtotime( $current_date );
            $compare_ts      = strtotime( $compare_start );
            $year_diff       = date( 'Y', $current_ts ) - date( 'Y', $compare_ts );
            $compare_date_ts = strtotime( $current_date . " -$year_diff years" );
            return date( 'Y-m-d', $compare_date_ts );
        } else {
            $current_start_ts = strtotime( $current_start );
            $current_end_ts   = strtotime( $current_end );
            $current_date_ts  = strtotime( $current_date );
            $compare_start_ts = strtotime( $compare_start );
            $compare_end_ts   = strtotime( $compare_end );

            $days_from_start = ( $current_date_ts - $current_start_ts ) / DAY_IN_SECONDS;
            $compare_date_ts = $compare_start_ts + ( $days_from_start * DAY_IN_SECONDS );

            if ( $compare_date_ts >= $compare_start_ts && $compare_date_ts <= $compare_end_ts ) {
                return date( 'Y-m-d', $compare_date_ts );
            }
            return null;
        }
    }

    // ------------------------------------------------------------
    // 5. NEW DETAIL ENDPOINTS (for clickable metric cards)
    // ------------------------------------------------------------

    /**
     * AJAX callback for detail views (orders, services, extra services, sales).
     * FIXED: Always returns valid DataTables JSON object.
     */
    public function bm_fetch_analytics_detail_callback() {
        $nonce = filter_input( INPUT_POST, 'nonce' );
        if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
            // Return valid JSON even on error
            wp_send_json(
                array(
					'draw'            => 0,
					'recordsTotal'    => 0,
					'recordsFiltered' => 0,
					'data'            => array(),
					'error'           => 'Security check failed',
                )
            );
        }

        $post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

        $draw     = intval( $post['draw'] ?? 1 );
        $start    = intval( $post['start'] ?? 0 );
        $length   = intval( $post['length'] ?? 20 );
        $per_page = $length;
        $offset   = $start;

        $bmrequests = new BM_Request();
        $metric     = sanitize_text_field( $post['metric'] ?? '' );
        $date_from  = ! empty( $post['date_from'] ) ? $bmrequests->bm_convert_date_format( $post['date_from'], 'd/m/Y', 'Y-m-d' ) : gmdate( 'Y-m-01' );
        $date_to    = ! empty( $post['date_to'] ) ? $bmrequests->bm_convert_date_format( $post['date_to'], 'd/m/Y', 'Y-m-d' ) : gmdate( 'Y-m-t' );
        $orderby    = sanitize_text_field( $post['order_col'] ?? '' );
        $order      = sanitize_text_field( $post['order_dir'] ?? 'DESC' );
        $filters    = isset( $post['filters'] ) ? (array) $post['filters'] : array();

        // Default Empty Response structure
        $response_data = array(
            'draw'            => $draw,
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'data'            => array(),
        );

        try {
            $data = array();

            switch ( $metric ) {
                case 'total_orders':
                case 'orders':
                    $data = $this->bm_get_orders_detail( $date_from, $date_to, $filters, $offset, $per_page, $orderby, $order );
                    break;
                case 'services_sold':
                case 'items_sold':
                    $data = $this->bm_get_services_detail( $date_from, $date_to, $filters, $offset, $per_page, $orderby, $order );
                    break;
                case 'extra_services_sold':
                    $data = $this->bm_get_extra_services_detail( $date_from, $date_to, $filters, $offset, $per_page, $orderby, $order );
                    break;
                default:
                    $data = $this->bm_get_sales_detail( $date_from, $date_to, $filters, $offset, $per_page, $orderby, $order );
                    break;
            }

            if ( ! empty( $data ) && is_array( $data ) ) {
                $response_data['recordsTotal']    = intval( $data['recordsTotal'] ?? 0 );
                $response_data['recordsFiltered'] = intval( $data['recordsFiltered'] ?? 0 );
                $response_data['data']            = is_array( $data['data'] ) ? $data['data'] : array();
            }
		} catch ( Exception $e ) {
            $response_data['error'] = $e->getMessage();
        }

        wp_send_json( $response_data );
    }

    /**
     * Detail: Orders (list of bookings).
     */
    private function bm_get_orders_detail( $date_from, $date_to, $filters, $offset, $limit, $orderby, $order ) {
        $dbhandler = new BM_DBhandler();

        // ------------------------------------------------------------------
        // 1. Base WHERE clause (date range + succeeded orders)
        // ------------------------------------------------------------------
        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        // ------------------------------------------------------------------
        // 2. Apply dynamic filters from frontend
        // ------------------------------------------------------------------
        if ( ! empty( $filters['order_status'] ) ) {
            $where['b.order_status'] = array( '=' => sanitize_text_field( $filters['order_status'] ) );
        }
        if ( ! empty( $filters['customer_name'] ) ) {
            // Use a raw string condition with LIKE
            $where[] = "c.customer_name LIKE '%" . esc_sql( $filters['customer_name'] ) . "%'";
        }
        if ( ! empty( $filters['service_name'] ) ) {
            $where['b.service_name'] = array( 'LIKE', '%' . $filters['service_name'] . '%' );
        }

        // ------------------------------------------------------------------
        // 3. Joins (customers table)
        // ------------------------------------------------------------------
        $joins = array(
            array(
                'table' => 'CUSTOMERS',
                'alias' => 'c',
                'on'    => 'b.customer_id = c.id',
                'type'  => 'LEFT',
            ),
        );

        // ------------------------------------------------------------------
        // 4. Get total count (without LIMIT) for DataTables recordsTotal
        // ------------------------------------------------------------------
        $count_result = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COUNT(DISTINCT b.id) as total',
            $joins,
            $where,
            'row'
        );
        $total        = $count_result ? intval( $count_result->total ) : 0;

        // ------------------------------------------------------------------
        // 5. Sorting – map frontend column keys to database fields
        // ------------------------------------------------------------------
        $sortable_columns = array(
            'booking_date'    => 'b.booking_date',
            'order_id'        => 'b.id',
            'customer_name'   => 'c.customer_name',
            'service_name'    => 'b.service_name',
            'total_svc_slots' => 'b.total_svc_slots',
            'total_cost'      => 'b.total_cost',
            'disount_amount'  => 'b.disount_amount',
            'order_status'    => 'b.order_status',
        );

        $sort_column = isset( $sortable_columns[ $orderby ] ) ? $sortable_columns[ $orderby ] : 'b.booking_date';
        $sort_order  = ( strtoupper( $order ) === 'DESC' ) ? true : false;

        // ------------------------------------------------------------------
        // 6. Fetch paginated, sorted rows
        // ------------------------------------------------------------------
        $columns = 'b.id, 
                    b.booking_date, 
                    b.service_name, 
                    b.total_svc_slots, 
                    b.total_ext_svc_slots,
                    b.total_cost, 
                    b.disount_amount, 
                    b.order_status, 
                    b.booking_created_at,
                    c.customer_name, 
                    c.customer_email';

        $rows = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            $columns,
            $joins,
            $where,
            'results',
            $offset,
            $limit,
            $sort_column,
            $sort_order
        );

        // ------------------------------------------------------------------
        // 7. Format rows for JSON (convert to array, handle data types)
        // ------------------------------------------------------------------
        $formatted_rows = array();
        if ( $rows ) {
            foreach ( $rows as $row ) {
                $formatted_rows[] = array(
                    'booking_date'        => $row->booking_date,
                    'order_id'            => $row->id,
                    'customer_name'       => $row->customer_name,
                    'service_name'        => $row->service_name,
                    'total_svc_slots'     => intval( $row->total_svc_slots ),
                    'total_ext_svc_slots' => intval( $row->total_ext_svc_slots ),
                    'total_cost'          => floatval( $row->total_cost ),
                    'disount_amount'      => floatval( $row->disount_amount ),
                    'order_status'        => $row->order_status,
                );
            }
        }

        // ------------------------------------------------------------------
        // 8. Column definitions for DataTable and frontend
        // ------------------------------------------------------------------
        $columns_config = array(
            array(
                'key'      => 'booking_date',
                'label'    => 'Date',
                'type'     => 'date',
                'sortable' => true,
            ),
            array(
                'key'      => 'order_id',
                'label'    => 'Order #',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'customer_name',
                'label'    => 'Customer',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'service_name',
                'label'    => 'Service',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'total_svc_slots',
                'label'    => 'Items',
                'type'     => 'number',
                'sortable' => true,
            ),
            array(
                'key'      => 'total_cost',
                'label'    => 'Total',
                'type'     => 'currency',
                'sortable' => true,
            ),
            array(
                'key'      => 'disount_amount',
                'label'    => 'Discount',
                'type'     => 'currency',
                'sortable' => true,
            ),
            array(
                'key'      => 'order_status',
                'label'    => 'Status',
                'type'     => 'text',
                'sortable' => true,
            ),
        );

        // ------------------------------------------------------------------
        // 9. Filter configuration (for frontend filter bar)
        // ------------------------------------------------------------------
        $filter_config = array(
            'order_status'  => array(
                'label'   => 'Order Status',
                'type'    => 'select',
                'options' => array(
                    array(
                        'value' => 'succeeded',
                        'label' => 'Succeeded',
                    ),
                    array(
                        'value' => 'pending',
                        'label' => 'Pending',
                    ),
                    array(
                        'value' => 'cancelled',
                        'label' => 'Cancelled',
                    ),
                ),
            ),
            'customer_name' => array(
                'label' => 'Customer Name',
                'type'  => 'text',
            ),
            'service_name'  => array(
                'label' => 'Service Name',
                'type'  => 'text',
            ),
        );

        // ------------------------------------------------------------------
        // 10. Default visible columns (can be overridden by user preferences)
        // ------------------------------------------------------------------
        $visible_columns = array( 'booking_date', 'order_id', 'customer_name', 'service_name', 'total_svc_slots', 'total_cost', 'order_status' );

        // ------------------------------------------------------------------
        // 11. Return DataTables‑compatible JSON
        // ------------------------------------------------------------------
        // 'draw' is sent by DataTables – we echo it back
        $draw = isset( $_POST['draw'] ) ? intval( $_POST['draw'] ) : 1;

        return array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,   // same as total because filters are applied in SQL
            'data'            => $formatted_rows,
            'columns'         => $columns_config,
            'visible_columns' => $visible_columns,
            'filter_config'   => $filter_config,
            'active_filters'  => $filters,
        );
    }

    public function bm_get_orders_analytics( $date_from, $date_to, $compare_from = '', $compare_to = '', $compare_type = 'period', $filters = array() ) {
        $dbhandler = new BM_DBhandler();

        // ---- METRICS (current period) ----
        $current_metrics = $this->bm_get_orders_metrics( $date_from, $date_to, $filters );
        $total_orders    = $current_metrics['total_orders'];
        $total_revenue   = $current_metrics['total_revenue'];
        $avg_order_value = $total_orders > 0 ? $total_revenue / $total_orders : 0;

        // ---- METRICS (previous period) ----
        $prev_metrics = ! empty( $compare_from ) && ! empty( $compare_to )
            ? $this->bm_get_orders_metrics( $compare_from, $compare_to, $filters )
            : array(
                'total_orders'  => 0,
                'total_revenue' => 0,
            );

        $response = array(
            'total_orders'           => $total_orders,
            'total_orders_change'    => $this->bm_calculate_change( $prev_metrics['total_orders'], $total_orders ),
            'total_revenue'          => $total_revenue,
            'total_revenue_change'   => $this->bm_calculate_change( $prev_metrics['total_revenue'], $total_revenue ),
            'avg_order_value'        => $avg_order_value,
            'avg_order_value_change' => $this->bm_calculate_change( $prev_metrics['total_revenue'] > 0 ? $prev_metrics['total_revenue'] / $prev_metrics['total_orders'] : 0, $avg_order_value ),
        );

        // ---- CHART DATA ----
        $chart_data = $this->bm_get_orders_chart_data( $date_from, $date_to, $compare_from, $compare_to, $compare_type, $filters );
        $response   = array_merge( $response, $chart_data );

        // ---- TABLE DATA (paginated) ----
        // For the first load we'll get the first 20 rows (pagination will be handled by DataTables via separate endpoint)
        // Here we return a limited set; full pagination will use bm_fetch_analytics_detail_callback.
        $table_data         = $this->bm_get_orders_table_data( $date_from, $date_to, $filters, 0, 20, 'booking_created_at', 'DESC' );
        $response['orders'] = $table_data['data'];

        // ---- FILTER OPTIONS ----
        $response['filters'] = $this->bm_get_orders_filter_options( $date_from, $date_to );

        $response['status'] = true;
        return $response;
    }

    private function bm_get_orders_metrics( $date_from, $date_to, $filters = array() ) {
        $dbhandler = new BM_DBhandler();
        $where     = $this->bm_build_orders_where( $date_from, $date_to, $filters );

        $joins = array(
            array(
                'table' => 'CUSTOMERS',
                'alias' => 'c',
                'on'    => 'b.customer_id = c.id',
                'type'  => 'LEFT',
            ),
            array(
                'table' => 'TRANSACTIONS',
                'alias' => 't',
                'on'    => 'b.id = t.booking_id',
                'type'  => 'LEFT',
            ),
        );

        $result = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COUNT(DISTINCT b.id) as total_orders, COALESCE(SUM(b.total_cost),0) as total_revenue',
            $joins,
            $where,
            'row'
        );

        return array(
            'total_orders'  => $result ? intval( $result->total_orders ) : 0,
            'total_revenue' => $result ? floatval( $result->total_revenue ) : 0,
        );
    }

    private function bm_build_orders_where( $date_from, $date_to, $filters = array() ) {
        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' ); // adjust as needed

        // Apply multi‑select filters
        if ( ! empty( $filters['customers'] ) && is_array( $filters['customers'] ) ) {
            $where['c.id'] = array( 'IN' => $filters['customers'] );
        }
        if ( ! empty( $filters['services'] ) && is_array( $filters['services'] ) ) {
            $where['b.service_id'] = array( 'IN' => $filters['services'] );
        }
        if ( ! empty( $filters['order_status'] ) && is_array( $filters['order_status'] ) ) {
            $where['b.order_status'] = array( 'IN' => $filters['order_status'] );
        }
        if ( ! empty( $filters['payment_status'] ) && is_array( $filters['payment_status'] ) ) {
            // Need to join transactions
            $where['t.payment_status'] = array( 'IN' => $filters['payment_status'] );
        }
        if ( ! empty( $filters['emails'] ) && is_array( $filters['emails'] ) ) {
            $where['c.customer_email'] = array( 'IN' => $filters['emails'] );
        }

        return $where;
    }

    private function bm_get_orders_chart_data( $date_from, $date_to, $compare_from = '', $compare_to = '', $compare_type = 'period', $filters = array() ) {
        $date_range = $this->bm_generate_date_range( $date_from, $date_to );
        $response   = array(
            'chart_labels'         => array(),
            'current_orders_data'  => array(),
            'previous_orders_data' => array(),
        );

        foreach ( $date_range as $date ) {
            $response['chart_labels'][]        = gmdate( 'd M', strtotime( $date ) );
            $current                           = $this->bm_get_daily_orders_count( $date, $filters );
            $response['current_orders_data'][] = $current;

            if ( ! empty( $compare_from ) && ! empty( $compare_to ) ) {
                $prev_date                          = $this->bm_get_comparison_date( $date, $date_from, $date_to, $compare_from, $compare_to, $compare_type );
                $previous                           = $prev_date ? $this->bm_get_daily_orders_count( $prev_date, $filters ) : 0;
                $response['previous_orders_data'][] = $previous;
            } else {
                $response['previous_orders_data'][] = 0;
            }
        }

        return $response;
    }

    private function bm_get_daily_orders_count( $date, $filters = array() ) {
        $dbhandler = new BM_DBhandler();
        $where     = $this->bm_build_orders_where( $date, $date, $filters );
        $joins     = array(
            array(
                'table' => 'CUSTOMERS',
                'alias' => 'c',
                'on'    => 'b.customer_id = c.id',
                'type'  => 'LEFT',
            ),
            array(
                'table' => 'TRANSACTIONS',
                'alias' => 't',
                'on'    => 'b.id = t.booking_id',
                'type'  => 'LEFT',
            ),
        );

        $result = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COUNT(DISTINCT b.id) as cnt',
            $joins,
            $where,
            'row'
        );

        return $result ? intval( $result->cnt ) : 0;
    }

    private function bm_get_orders_table_data( $date_from, $date_to, $filters, $offset, $limit, $orderby, $order ) {
        $dbhandler = new BM_DBhandler();
        $where     = $this->bm_build_orders_where( $date_from, $date_to, $filters );

        $joins = array(
            array(
                'table' => 'CUSTOMERS',
                'alias' => 'c',
                'on'    => 'b.customer_id = c.id',
                'type'  => 'LEFT',
            ),
            array(
                'table' => 'TRANSACTIONS',
                'alias' => 't',
                'on'    => 'b.id = t.booking_id',
                'type'  => 'LEFT',
            ),
        );

        // Columns needed for the table – note: we fetch billing_details, not individual fields
        $columns = 'b.id, b.service_name, b.booking_created_at, b.booking_date,
                    c.billing_details, c.customer_email,
                    b.total_svc_slots, b.total_ext_svc_slots, b.service_cost, b.extra_svc_cost,
                    b.disount_amount, b.total_cost, b.order_status, t.payment_status';

        $sortable = array(
            'booking_created_at' => 'b.booking_created_at',
            'booking_date'       => 'b.booking_date',
            'service_name'       => 'b.service_name',
            'total_cost'         => 'b.total_cost',
            'order_status'       => 'b.order_status',
            'payment_status'     => 't.payment_status',
        );
        $sort_col = isset( $sortable[ $orderby ] ) ? $sortable[ $orderby ] : 'b.booking_created_at';
        $sort_dir = ( strtoupper( $order ) === 'DESC' ) ? true : false;

        $rows = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            $columns,
            $joins,
            $where,
            'results',
            $offset,
            $limit,
            $sort_col,
            $sort_dir
        );

        $count_result = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COUNT(DISTINCT b.id) as total',
            $joins,
            $where,
            'row'
        );
        $total        = $count_result ? intval( $count_result->total ) : 0;

        $data = array();
        if ( $rows ) {
            foreach ( $rows as $row ) {
                // Unserialize billing details
                $billing = maybe_unserialize( $row->billing_details );
                if ( ! is_array( $billing ) ) {
                    $billing = array();
                }

                $first_name = isset( $billing['billing_first_name'] ) ? $billing['billing_first_name'] : '';
                $last_name  = isset( $billing['billing_last_name'] ) ? $billing['billing_last_name'] : '';
                $contact_no = isset( $billing['billing_contact'] ) ? $billing['billing_contact'] : '';
                $email      = ! empty( $row->customer_email ) ? $row->customer_email : ( isset( $billing['billing_email'] ) ? $billing['billing_email'] : '' );

                $data[] = array(
                    'orderId'             => $row->id,
                    'service_name'        => $row->service_name,
                    'booking_created_at'  => $row->booking_created_at,
                    'booking_date'        => $row->booking_date,
                    'first_name'          => $first_name,
                    'last_name'           => $last_name,
                    'contact_no'          => $contact_no,
                    'email_address'       => $email,
                    'total_svc_slots'     => intval( $row->total_svc_slots ),
                    'total_ext_svc_slots' => intval( $row->total_ext_svc_slots ),
                    'service_cost'        => floatval( $row->service_cost ),
                    'extra_svc_cost'      => floatval( $row->extra_svc_cost ),
                    'disount_amount'      => floatval( $row->disount_amount ),
                    'total_cost'          => floatval( $row->total_cost ),
                    'order_status'        => $row->order_status,
                    'payment_status'      => $row->payment_status,
                );
            }
        }

        return array(
            'total' => $total,
            'data'  => $data,
        );
    }

    private function bm_get_orders_filter_options( $date_from, $date_to ) {
        $dbhandler = new BM_DBhandler();
        $where     = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }

        // Customers
        $joins_cust = array(
			array(
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'b.customer_id = c.id',
				'type'  => 'INNER',
			),
		);
        $cust_rows  = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'DISTINCT c.id as value, c.customer_name as label',
            $joins_cust,
            $where,
            'results'
        );
        $customers  = array();
        if ( $cust_rows ) {
            foreach ( $cust_rows as $row ) {
                $customers[] = array(
					'value' => $row->value,
					'label' => $row->label,
				);
            }
        }

        // Services
        $service_rows = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'DISTINCT b.service_id as value, b.service_name as label',
            array(),
            $where,
            'results'
        );
        $services     = array();
        if ( $service_rows ) {
            foreach ( $service_rows as $row ) {
                $services[] = array(
					'value' => $row->value,
					'label' => $row->label,
				);
            }
        }

        // Order statuses (from bookings)
        $status_rows    = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'DISTINCT b.order_status as value',
            array(),
            $where,
            'results'
        );
        $order_statuses = array();
        if ( $status_rows ) {
            foreach ( $status_rows as $row ) {
                $order_statuses[] = array(
					'value' => $row->value,
					'label' => ucfirst( $row->value ),
				);
            }
        }

        // Payment statuses (from transactions)
        $joins_pay        = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);
        $pay_rows         = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'DISTINCT t.payment_status as value',
            $joins_pay,
            $where,
            'results'
        );
        $payment_statuses = array();
        if ( $pay_rows ) {
            foreach ( $pay_rows as $row ) {
                $payment_statuses[] = array(
					'value' => $row->value,
					'label' => ucfirst( $row->value ),
				);
            }
        }

        // Emails (from customers table)
        $email_rows = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'DISTINCT c.customer_email as value',
            $joins_cust,
            $where,
            'results'
        );
        $emails     = array();
        if ( $email_rows ) {
            foreach ( $email_rows as $row ) {
                $emails[] = array(
					'value' => $row->value,
					'label' => $row->value,
				);
            }
        }

        return array(
            'customers'        => $customers,
            'services'         => $services,
            'order_statuses'   => $order_statuses,
            'payment_statuses' => $payment_statuses,
            'emails'           => $emails,
        );
    }

    private function bm_get_services_detail( $date_from, $date_to, $filters, $offset, $limit, $orderby, $order ) {
        $dbhandler = new BM_DBhandler();

        // ------------------------------------------------------------------
        // 1. Base WHERE clause (date range + succeeded orders)
        // ------------------------------------------------------------------
        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['b.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }
        $where['b.order_status'] = array( '=' => 'succeeded' );

        // ------------------------------------------------------------------
        // 2. Apply dynamic filters
        // ------------------------------------------------------------------
        if ( ! empty( $filters['service_name'] ) ) {
            $where['b.service_name'] = array( 'LIKE', '%' . $filters['service_name'] . '%' );
        }
        if ( ! empty( $filters['category'] ) ) {
            if ( $filters['category'] === 'Uncategorized' ) {
                $where[] = '(s.service_category IS NULL OR s.service_category = 0)';
            } else {
                $where['s.service_category'] = array( '=', intval( $filters['category'] ) );
            }
        }

        // ------------------------------------------------------------------
        // 3. Joins (service and category)
        // ------------------------------------------------------------------
        $joins = array(
            array(
                'table' => 'SERVICE',
                'alias' => 's',
                'on'    => 'b.service_id = s.id',
                'type'  => 'LEFT',
            ),
            array(
                'table' => 'CATEGORY',
                'alias' => 'c',
                'on'    => 's.service_category = c.id',
                'type'  => 'LEFT',
            ),
        );

        // ------------------------------------------------------------------
        // 4. Get total distinct services (recordsTotal)
        // ------------------------------------------------------------------
        $count_result = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            'COUNT(DISTINCT b.service_id) as total',
            $joins,
            $where,
            'row'
        );
        $total        = $count_result ? intval( $count_result->total ) : 0;

        // ------------------------------------------------------------------
        // 5. Sorting – map frontend column keys to SQL expressions
        // ------------------------------------------------------------------
        $sortable_columns = array(
            'service_name' => 'b.service_name',
            'category'     => 'category',          // alias from SELECT
            'items_sold'   => 'items_sold',        // alias from SELECT
            'net_sales'    => 'net_sales',         // will be calculated
            'orders'       => 'orders',            // alias from SELECT
            'gross_sales'  => 'gross_sales',       // alias from SELECT
            'discounts'    => 'total_discounts',   // alias from SELECT
            'returns'      => 'returns',           // will be calculated
        );

        $sort_column = isset( $sortable_columns[ $orderby ] ) ? $sortable_columns[ $orderby ] : 'items_sold';
        $sort_order  = ( strtoupper( $order ) === 'DESC' ) ? true : false;

        // ------------------------------------------------------------------
        // 6. Fetch aggregated data per service (with pagination)
        // ------------------------------------------------------------------
        $columns    = 'b.service_id,
                    b.service_name,
                    COALESCE(c.cat_name, "Uncategorized") as category,
                    SUM(b.total_svc_slots + b.total_ext_svc_slots) as items_sold,
                    SUM(b.total_cost) as gross_sales,
                    SUM(COALESCE(b.disount_amount, 0)) as total_discounts,
                    COUNT(DISTINCT b.id) as orders';
        $additional = 'GROUP BY b.service_id';

        $results = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            $columns,
            $joins,
            $where,
            'results',
            $offset,
            $limit,
            $sort_column,
            $sort_order,
            $additional
        );

        // ------------------------------------------------------------------
        // 7. Format rows – calculate net sales (gross - discounts - returns)
        // ------------------------------------------------------------------
        $formatted_rows = array();
        if ( $results ) {
            foreach ( $results as $result ) {
                // Get returns for this service
                $returns_where = array(
                    't.payment_status' => array( '=', 'refunded' ),
                );
                if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
                    $returns_where['t.transaction_created_at'] = array(
                        '>=' => $date_from . ' 00:00:00',
                        '<=' => $date_to . ' 23:59:59',
                    );
                }
                $returns_joins                  = array(
                    array(
                        'table' => 'BOOKING',
                        'alias' => 'b2',
                        'on'    => 't.booking_id = b2.id',
                        'type'  => 'INNER',
                    ),
                );
                $returns_where['b2.service_id'] = array( '=', $result->service_id );

                $returns = $dbhandler->get_results_with_join(
                    array( 'TRANSACTIONS', 't' ),
                    'SUM(t.paid_amount) as service_returns',
                    $returns_joins,
                    $returns_where,
                    'row'
                );

                $gross_sales     = floatval( $result->gross_sales );
                $total_discounts = floatval( $result->total_discounts );
                $service_returns = $returns ? floatval( $returns->service_returns ) : 0;
                $net_sales       = $gross_sales - $total_discounts - $service_returns;

                $formatted_rows[] = array(
                    'service_id'   => $result->service_id,
                    'service_name' => $result->service_name,
                    'category'     => $result->category,
                    'items_sold'   => intval( $result->items_sold ),
                    'net_sales'    => $net_sales,
                    'orders'       => intval( $result->orders ),
                    'gross_sales'  => $gross_sales,
                    'discounts'    => $total_discounts,
                    'returns'      => $service_returns,
                );
            }
        }

        // ------------------------------------------------------------------
        // 8. Column definitions
        // ------------------------------------------------------------------
        $columns_config = array(
            array(
                'key'      => 'service_name',
                'label'    => 'Service',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'category',
                'label'    => 'Category',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'items_sold',
                'label'    => 'Items Sold',
                'type'     => 'number',
                'sortable' => true,
            ),
            array(
                'key'      => 'net_sales',
                'label'    => 'Net Sales',
                'type'     => 'currency',
                'sortable' => true,
            ),
            array(
                'key'      => 'orders',
                'label'    => 'Orders',
                'type'     => 'number',
                'sortable' => true,
            ),
            array(
                'key'      => 'gross_sales',
                'label'    => 'Gross Sales',
                'type'     => 'currency',
                'sortable' => true,
            ),
            array(
                'key'      => 'discounts',
                'label'    => 'Discounts',
                'type'     => 'currency',
                'sortable' => true,
            ),
            array(
                'key'      => 'returns',
                'label'    => 'Returns',
                'type'     => 'currency',
                'sortable' => true,
            ),
        );

        // ------------------------------------------------------------------
        // 9. Filter configuration
        // ------------------------------------------------------------------
        $filter_config = array(
            'service_name' => array(
                'label' => 'Service Name',
                'type'  => 'text',
            ),
            'category'     => array(
                'label'   => 'Category',
                'type'    => 'select',
                'options' => $this->bm_get_category_options(),
            ),
        );

        // ------------------------------------------------------------------
        // 10. Default visible columns
        // ------------------------------------------------------------------
        $visible_columns = array( 'service_name', 'category', 'items_sold', 'net_sales', 'orders' );

        // ------------------------------------------------------------------
        // 11. Return DataTables JSON
        // ------------------------------------------------------------------
        $draw = isset( $_POST['draw'] ) ? intval( $_POST['draw'] ) : 1;

        return array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $formatted_rows,
            'columns'         => $columns_config,
            'visible_columns' => $visible_columns,
            'filter_config'   => $filter_config,
            'active_filters'  => $filters,
        );
    }

    private function bm_get_extra_services_detail( $date_from, $date_to, $filters, $offset, $limit, $orderby, $order ) {
        $dbhandler = new BM_DBhandler();

        // ------------------------------------------------------------------
        // 1. Base WHERE clause (date range)
        // ------------------------------------------------------------------
        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['es.booking_date'] = array(
                '>=' => $date_from,
                '<=' => $date_to,
            );
        }

        // ------------------------------------------------------------------
        // 2. Joins (booking + extra)
        // ------------------------------------------------------------------
        $joins                   = array(
            array(
                'table' => 'BOOKING',
                'alias' => 'b',
                'on'    => 'es.booking_id = b.id',
                'type'  => 'INNER',
            ),
            array(
                'table' => 'EXTRA',
                'alias' => 'e',
                'on'    => 'es.extra_svc_id = e.id',
                'type'  => 'LEFT',
            ),
        );
        $where['b.order_status'] = array( '=', 'succeeded' );

        // ------------------------------------------------------------------
        // 3. Apply dynamic filters
        // ------------------------------------------------------------------
        if ( ! empty( $filters['extra_name'] ) ) {
            $where['e.extra_name'] = array( 'LIKE', '%' . $filters['extra_name'] . '%' );
        }
        if ( ! empty( $filters['service_name'] ) ) {
            $where['b.service_name'] = array( 'LIKE', '%' . $filters['service_name'] . '%' );
        }

        // ------------------------------------------------------------------
        // 4. Get total distinct extra services (recordsTotal)
        // ------------------------------------------------------------------
        $count_result = $dbhandler->get_results_with_join(
            array( 'EXTRASLOTCOUNT', 'es' ),
            'COUNT(DISTINCT es.extra_svc_id) as total',
            $joins,
            $where,
            'row'
        );
        $total        = $count_result ? intval( $count_result->total ) : 0;

        // ------------------------------------------------------------------
        // 5. Sorting – map frontend column keys to SQL expressions
        // ------------------------------------------------------------------
        $sortable_columns = array(
            'extra_name'    => 'e.extra_name',
            'service_name'  => 'b.service_name',
            'slots_booked'  => 'slots_booked',
            'total_revenue' => 'total_revenue',
        );

        $sort_column = isset( $sortable_columns[ $orderby ] ) ? $sortable_columns[ $orderby ] : 'slots_booked';
        $sort_order  = ( strtoupper( $order ) === 'DESC' ) ? true : false;

        // ------------------------------------------------------------------
        // 6. Fetch aggregated data per extra service
        // ------------------------------------------------------------------
        $columns    = 'e.id as extra_id,
                    e.extra_name,
                    b.service_name,
                    SUM(es.slots_booked) as slots_booked,
                    SUM(es.slots_booked * e.extra_price) as total_revenue';
        $additional = 'GROUP BY e.id';

        $results = $dbhandler->get_results_with_join(
            array( 'EXTRASLOTCOUNT', 'es' ),
            $columns,
            $joins,
            $where,
            'results',
            $offset,
            $limit,
            $sort_column,
            $sort_order,
            $additional
        );

        // ------------------------------------------------------------------
        // 7. Format rows
        // ------------------------------------------------------------------
        $formatted_rows = array();
        if ( $results ) {
            foreach ( $results as $result ) {
                $formatted_rows[] = array(
                    'extra_id'      => $result->extra_id,
                    'extra_name'    => $result->extra_name,
                    'service_name'  => $result->service_name,
                    'slots_booked'  => intval( $result->slots_booked ),
                    'total_revenue' => floatval( $result->total_revenue ),
                );
            }
        }

        // ------------------------------------------------------------------
        // 8. Column definitions
        // ------------------------------------------------------------------
        $columns_config = array(
            array(
                'key'      => 'extra_name',
                'label'    => 'Extra Service',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'service_name',
                'label'    => 'Booked with Service',
                'type'     => 'text',
                'sortable' => true,
            ),
            array(
                'key'      => 'slots_booked',
                'label'    => 'Slots Sold',
                'type'     => 'number',
                'sortable' => true,
            ),
            array(
                'key'      => 'total_revenue',
                'label'    => 'Revenue',
                'type'     => 'currency',
                'sortable' => true,
            ),
        );

        // ------------------------------------------------------------------
        // 9. Filter configuration
        // ------------------------------------------------------------------
        $filter_config = array(
            'extra_name'   => array(
                'label' => 'Extra Service Name',
                'type'  => 'text',
            ),
            'service_name' => array(
                'label' => 'Service Name',
                'type'  => 'text',
            ),
        );

        // ------------------------------------------------------------------
        // 10. Default visible columns
        // ------------------------------------------------------------------
        $visible_columns = array( 'extra_name', 'service_name', 'slots_booked', 'total_revenue' );

        // ------------------------------------------------------------------
        // 11. Return DataTables JSON
        // ------------------------------------------------------------------
        $draw = isset( $_POST['draw'] ) ? intval( $_POST['draw'] ) : 1;

        return array(
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $formatted_rows,
            'columns'         => $columns_config,
            'visible_columns' => $visible_columns,
            'filter_config'   => $filter_config,
            'active_filters'  => $filters,
        );
    }

    /**
     * Detail: Sales / Transactions
     * Optimized to return only the raw data required by the new JS implementation.
     */
    private function bm_get_sales_detail( $date_from, $date_to, $filters, $offset, $limit, $orderby, $order ) {
        $dbhandler = new BM_DBhandler();

        $where = array();
        if ( ! empty( $date_from ) && ! empty( $date_to ) ) {
            $where['t.transaction_created_at'] = array(
                '>=' => $date_from . ' 00:00:00',
                '<=' => $date_to . ' 23:59:59',
            );
        }

        $joins = array(
            array(
				'table' => 'BOOKING',
				'alias' => 'b',
				'on'    => 't.booking_id = b.id',
				'type'  => 'INNER',
			),
            array(
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'c.id = b.customer_id',
				'type'  => 'INNER',
			),
        );

        if ( ! empty( $filters['payment_method'] ) ) {
            $where['t.payment_method'] = array( '=', sanitize_text_field( $filters['payment_method'] ) );
        }
        if ( ! empty( $filters['payment_status'] ) ) {
            $where['t.payment_status'] = array( '=', sanitize_text_field( $filters['payment_status'] ) );
        }

        $count_result = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'COUNT(DISTINCT t.id) as total',
            $joins,
            $where,
            'row'
        );
        $total        = $count_result ? intval( $count_result->total ) : 0;

        // Sort mapping
        $sortable_columns = array(
            'date'           => 't.transaction_created_at',
            'transaction_id' => 't.id',
            'customer_name'  => 'c.customer_name',
            'service_name'   => 'b.service_name',
            'paid_amount'    => 't.paid_amount',
            'net_sales'      => 't.paid_amount',
            'payment_method' => 't.payment_method',
            'payment_status' => 't.payment_status',
        );

        $sort_column = isset( $sortable_columns[ $orderby ] ) ? $sortable_columns[ $orderby ] : 't.transaction_created_at';
        $is_desc     = ( strtoupper( $order ) === 'DESC' );

        $columns = 't.id as transaction_id, t.booking_id, t.paid_amount, t.payment_method, t.payment_status, t.transaction_created_at, b.service_name, c.customer_name';

        $rows = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            $columns,
            $joins,
            $where,
            'results',
            $offset,
            $limit,
            $sort_column,
            $is_desc
        );

        $formatted_rows = array();
        if ( $rows ) {
            foreach ( $rows as $row ) {
                $formatted_rows[] = array(
                    'date'           => $row->transaction_created_at,
                    'transaction_id' => $row->transaction_id,
                    'customer_name'  => $row->customer_name,
                    'service_name'   => $row->service_name,
                    'paid_amount'    => floatval( $row->paid_amount ),
                    'net_sales'      => floatval( $row->paid_amount ),
                    'payment_method' => ucfirst( $row->payment_method ),
                    'payment_status' => ucfirst( $row->payment_status ),
                    'booking_id'     => $row->booking_id,
                );
            }
        }

        return array(
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $formatted_rows,
        );
    }

    /**
     * Helper: Get category options for filter dropdown.
     */
    private function bm_get_category_options() {
        $dbhandler  = new BM_DBhandler();
        $categories = $dbhandler->get_all_result( 'CATEGORY', '*', 1, 'results', 0, false, 'cat_position', false );
        $options    = array(
			array(
				'value' => 'Uncategorized',
				'label' => 'Uncategorized',
			),
		);
        if ( $categories ) {
            foreach ( $categories as $cat ) {
                $options[] = array(
                    'value' => $cat->id,
                    'label' => $cat->cat_name,
                );
            }
        }
        return $options;
    }

    /**
     * Helper: Get distinct payment methods for filter dropdown.
     *
     * @return array Options array for select filter.
     */
    private function bm_get_payment_method_options() {
        $dbhandler = new BM_DBhandler();

        // Use get_results_with_join to fetch distinct payment_method
        $results = $dbhandler->get_results_with_join(
            array( 'TRANSACTIONS', 't' ),
            'DISTINCT t.payment_method',
            array(),
            array(),
            'results',
            0,
            false,
            null,
            false,
            '',
            true,
        );

        $options = array();
        if ( $results ) {
            foreach ( $results as $row ) {
                if ( ! empty( $row->payment_method ) ) {
                    $options[] = array(
                        'value' => $row->payment_method,
                        'label' => ucfirst( $row->payment_method ),
                    );
                }
            }
        }

        return $options;
    }

    // ------------------------------------------------------------
    // 6. CSV DOWNLOAD
    // ------------------------------------------------------------

    public function bm_download_analytics_csv_callback() {
        $nonce = filter_input( INPUT_POST, 'nonce' );
        if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
            die( esc_html__( 'Failed security check', 'service-booking' ) );
        }

        $bmrequests  = new BM_Request();
        $post        = isset( $_POST['post'] ) ? json_decode( stripslashes( $_POST['post'] ), true ) : array();
        $action_type = ! empty( $post['action_type'] ) ? sanitize_text_field( $post['action_type'] ) : '';

        $date_from = ! empty( $post['date_from'] ) ? $bmrequests->bm_convert_date_format( $post['date_from'], 'd/m/Y', 'Y-m-d' ) : gmdate( 'Y-m-01' );
        $date_to   = ! empty( $post['date_to'] ) ? $bmrequests->bm_convert_date_format( $post['date_to'], 'd/m/Y', 'Y-m-d' ) : gmdate( 'Y-m-t' );
        $filters   = isset( $post['filters'] ) ? (array) $post['filters'] : array();

        header( 'Content-Type: text/csv; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename=analytics_' . $action_type . '_' . gmdate( 'Y-m-d' ) . '.csv' );

        $output = fopen( 'php://output', 'w' );

        switch ( $action_type ) {
            case 'download_revenue_csv':
                $this->bm_generate_revenue_csv( $output, $date_from, $date_to );
                break;
            case 'download_products_csv':
                $category_id = ! empty( $post['category_id'] ) ? intval( $post['category_id'] ) : '';
                $service_id  = ! empty( $post['service_id'] ) ? intval( $post['service_id'] ) : '';
                $this->bm_generate_products_csv( $output, $date_from, $date_to, $category_id, $service_id );
                break;
            case 'download_detail_csv':
                $metric = ! empty( $post['metric'] ) ? sanitize_text_field( $post['metric'] ) : '';
                $this->bm_generate_detail_csv( $output, $date_from, $date_to, $metric, $filters );
                break;
            case 'download_orders_csv':
                $filters = isset( $post['filters'] ) ? (array) $post['filters'] : array();
                $this->bm_generate_orders_csv( $output, $date_from, $date_to, $filters );
                break;
        }

        fclose( $output );
        wp_die();
    }

    private function bm_generate_orders_csv( $output, $date_from, $date_to, $filters ) {
        $data    = $this->bm_get_orders_table_data( $date_from, $date_to, $filters, 0, 999999, '', 'ASC' );
        $headers = array( 'Ordered Service', 'Ordered Date', 'Service Date', 'First Name', 'Last Name', 'Contact', 'Email', 'Service Participants', 'Extra Participants', 'Service Cost', 'Extra Cost', 'Discount', 'Total', 'Order Status', 'Payment Status' );
        fputcsv( $output, $headers );
        foreach ( $data['data'] as $row ) {
            fputcsv(
                $output,
                array(
                    $row['service_name'],
                    $row['booking_created_at'],
                    $row['booking_date'],
                    $row['first_name'],
                    $row['last_name'],
                    $row['contact_no'],
                    $row['email_address'],
                    $row['total_svc_slots'],
                    $row['total_ext_svc_slots'],
                    $row['service_cost'],
                    $row['extra_svc_cost'],
                    $row['disount_amount'],
                    $row['total_cost'],
                    $row['order_status'],
                    $row['payment_status'],
                )
            );
        }
    }

    /**
     * Generate CSV for detail view (all records, no pagination).
     */
    private function bm_generate_detail_csv( $output, $date_from, $date_to, $metric, $filters ) {
        switch ( $metric ) {
            case 'total_orders':
            case 'orders':
                $data    = $this->bm_get_orders_detail( $date_from, $date_to, $filters, 0, 999999, '', 'ASC' );
                $rows    = $data['rows'];
                $headers = array( 'Date', 'Order ID', 'Customer', 'Service', 'Items', 'Total', 'Discount', 'Status' );
                fputcsv( $output, $headers );
                foreach ( $rows as $row ) {
                    fputcsv(
                        $output,
                        array(
							$row['booking_date'],
							$row['order_id'],
							$row['customer_name'],
							$row['service_name'],
							$row['total_svc_slots'],
							$row['total_cost'],
							$row['disount_amount'],
							$row['order_status'],
                        )
                    );
                }
                break;

            case 'services_sold':
                $data    = $this->bm_get_services_detail( $date_from, $date_to, $filters, 0, 999999, '', 'ASC' );
                $rows    = $data['rows'];
                $headers = array( 'Service', 'Category', 'Items Sold', 'Net Sales', 'Orders', 'Gross Sales', 'Discounts', 'Returns' );
                fputcsv( $output, $headers );
                foreach ( $rows as $row ) {
                    fputcsv(
                        $output,
                        array(
							$row['service_name'],
							$row['category'],
							$row['items_sold'],
							$row['net_sales'],
							$row['orders'],
							$row['gross_sales'],
							$row['discounts'],
							$row['returns'],
                        )
                    );
                }
                break;

            case 'extra_services_sold':
                $data    = $this->bm_get_extra_services_detail( $date_from, $date_to, $filters, 0, 999999, '', 'ASC' );
                $rows    = $data['rows'];
                $headers = array( 'Extra Service', 'Booked with Service', 'Slots Sold', 'Revenue' );
                fputcsv( $output, $headers );
                foreach ( $rows as $row ) {
                    fputcsv(
                        $output,
                        array(
							$row['extra_name'],
							$row['service_name'],
							$row['slots_booked'],
							$row['total_revenue'],
                        )
                    );
                }
                break;

            case 'total_sales':
            case 'net_sales':
                $data    = $this->bm_get_sales_detail( $date_from, $date_to, $filters, 0, 999999, '', 'ASC' );
                $rows    = $data['rows'];
                $headers = array( 'Date', 'Transaction ID', 'Customer', 'Service', 'Paid Amount', 'Net Sales', 'Payment Method', 'Status' );
                fputcsv( $output, $headers );
                foreach ( $rows as $row ) {
                    fputcsv(
                        $output,
                        array(
							$row['date'],
							$row['transaction_id'],
							$row['customer_name'],
							$row['service_name'],
							$row['paid_amount'],
							$row['net_sales'],
							$row['payment_method'],
							$row['payment_status'],
                        )
                    );
                }
                break;
        }
    }

    public function bm_generate_revenue_csv( $output, $date_from, $date_to ) {
        fputcsv( $output, array( 'Date', 'Orders', 'Gross Sales', 'Returns', 'Coupons', 'Net Sales', 'Taxes', 'Shipping', 'Total Sales' ) );
        $daily_revenue = $this->bm_get_daily_revenue_join( $date_from, $date_to );
        foreach ( $daily_revenue as $day ) {
            fputcsv(
                $output,
                array(
					$day['date'],
					$day['orders'],
					$day['gross_sales'],
					$day['returns'],
					$day['coupons'],
					$day['net_sales'],
					$day['taxes'],
					$day['shipping'],
					$day['total_sales'],
                )
            );
        }
    }

    public function bm_generate_products_csv( $output, $date_from, $date_to, $category_id = '', $service_id = '' ) {
        fputcsv( $output, array( 'Service', 'Category', 'Items Sold', 'Net Sales', 'Orders', 'Average Order Value', 'Conversion Rate' ) );
        $products = $this->bm_get_products_performance_join( $date_from, $date_to, $category_id, $service_id );
        foreach ( $products as $product ) {
            $avg_order_value = $product['orders'] > 0 ? $product['net_sales'] / $product['orders'] : 0;
            $conversion_rate = $product['visits'] > 0 ? ( $product['orders'] / $product['visits'] ) * 100 : 0;
            fputcsv(
                $output,
                array(
					$product['name'],
					$product['category'],
					$product['items_sold'],
					$product['net_sales'],
					$product['orders'],
					$avg_order_value,
					$conversion_rate . '%',
                )
            );
        }
    }

    public function bm_get_metric_chart_data( $date_from, $date_to, $compare_from, $compare_to, $compare_type, $metric ) {
        $date_range = $this->bm_generate_date_range( $date_from, $date_to );
        $response   = array(
            'chart_labels'  => array(),
            'current_data'  => array(),
            'previous_data' => array(),
        );

        foreach ( $date_range as $date ) {
            $response['chart_labels'][] = gmdate( 'd M', strtotime( $date ) );
            $current                    = $this->bm_get_daily_metric_value( $date, $metric );
            $response['current_data'][] = $current;

            if ( ! empty( $compare_from ) && ! empty( $compare_to ) ) {
                $prev_date                   = $this->bm_get_comparison_date( $date, $date_from, $date_to, $compare_from, $compare_to, $compare_type );
                $previous                    = $prev_date ? $this->bm_get_daily_metric_value( $prev_date, $metric ) : 0;
                $response['previous_data'][] = $previous;
            } else {
                $response['previous_data'][] = 0;
            }
        }

        $response['status'] = true;
        return $response;
    }

    /**
     * Get daily metric value for a specific date using the custom join handler.
     *
     * @param string $date   Date in Y-m-d format.
     * @param string $metric Metric identifier.
     * @return float
     */
    private function bm_get_daily_metric_value( $date, $metric ) {
        global $wpdb;
        $dbhandler          = new BM_DBhandler();
        $transactions_table = $wpdb->prefix . 'sgbm_transactions';

        $metric_map = array(
            'total_sales'         => 'SUM(b.total_cost)',
            'net_sales'           => "SUM(b.total_cost - COALESCE(b.disount_amount, 0)) - COALESCE((SELECT SUM(t.paid_amount) FROM $transactions_table t WHERE t.booking_id = b.id AND t.payment_status = 'refunded' AND DATE(t.transaction_created_at) = '__DATE__'), 0)",
            'total_orders'        => 'COUNT(DISTINCT b.id)',
            'services_sold'       => 'SUM(b.total_svc_slots)',
            'extra_services_sold' => 'SUM(b.total_ext_svc_slots)',
            'gross_sales'         => 'SUM(b.total_cost)',
            'returns'             => "COALESCE((SELECT SUM(t.paid_amount) FROM $transactions_table t WHERE t.booking_id = b.id AND t.payment_status = 'refunded' AND DATE(t.transaction_created_at) = '__DATE__'), 0)",
            'coupons'             => 'SUM(COALESCE(b.disount_amount, 0))',
            'items_sold'          => 'SUM(b.total_svc_slots + b.total_ext_svc_slots)',
            'revenue_net_sales'   => "SUM(b.total_cost - COALESCE(b.disount_amount, 0)) - COALESCE((SELECT SUM(t.paid_amount) FROM $transactions_table t WHERE t.booking_id = b.id AND t.payment_status = 'refunded' AND DATE(t.transaction_created_at) = '__DATE__'), 0)",
            'products_net_sales'  => "SUM(b.total_cost - COALESCE(b.disount_amount, 0)) - COALESCE((SELECT SUM(t.paid_amount) FROM $transactions_table t WHERE t.booking_id = b.id AND t.payment_status = 'refunded' AND DATE(t.transaction_created_at) = '__DATE__'), 0)",
            'products_orders'     => 'COUNT(DISTINCT b.id)',
            'orders_revenue'      => "SUM(b.total_cost - COALESCE(b.disount_amount, 0)) - COALESCE((SELECT SUM(t.paid_amount) FROM $transactions_table t WHERE t.booking_id = b.id AND t.payment_status = 'refunded' AND DATE(t.transaction_created_at) = '__DATE__'), 0)",
            'avg_order_value'     => "COALESCE( ( SUM(b.total_cost - COALESCE(b.disount_amount, 0)) - COALESCE((SELECT SUM(t.paid_amount) FROM $transactions_table t WHERE t.booking_id = b.id AND t.payment_status = 'refunded' AND DATE(t.transaction_created_at) = '__DATE__'), 0) ) / NULLIF(COUNT(DISTINCT b.id), 0), 0)",
        );

        if ( ! isset( $metric_map[ $metric ] ) ) {
            return 0.0;
        }

        // Replace __DATE__ placeholder
        $sql_expression = str_replace( '__DATE__', $date, $metric_map[ $metric ] );

        $where = array(
            'b.booking_date' => array( '=' => $date ),
            'b.order_status' => array( '=' => 'succeeded' ),
        );

        $result = $dbhandler->get_results_with_join(
            array( 'BOOKING', 'b' ),
            $sql_expression . ' AS metric_value',
            array(),
            $where,
            'row'
        );

        if ( $result && isset( $result->metric_value ) ) {
            return floatval( $result->metric_value );
        }

        return 0.0;
    }

	/**
	 * Fetch all orders
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_orders() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler          = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$woocommerceservice = new WooCommerceService();
		$data               = array();
		$statuses           = array();

		/**if ( $woocommerceservice->is_enabled() ) {
			$order_statuses = wc_get_order_statuses();
		} else {
			$order_statuses = $bmrequests->bm_fetch_order_status_key_value();
		}
		foreach ( $order_statuses as $key => $status ) {
			$value              = $bmrequests->bm_fetch_order_status_string( $key );
			$text               = $bmrequests->bm_fetch_order_status_key_value( $value );
			$statuses[ $value ] = $text;
		}*/

		$statuses = $bmrequests->bm_fetch_order_status_key_value();

		$data['status']         = true;
		$data['bookings']       = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'booking_date', 'DESC' );
		$data['active_columns'] = $bmrequests->bm_fetch_active_columns( 'orders' );
		$data['column_values']  = $bmrequests->bm_fetch_column_order_and_names( 'orders' );
		$data['order_statuses'] = $statuses;

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_all_orders()


	/**
	 * Fetch saved search for orders
	 *
	 * @author Darpan
	 */
	public function bm_fetch_saved_order_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$saved_search = array();
		$bmrequests   = new BM_Request();
		$module       = filter_input( INPUT_POST, 'module' );

		if ( $module != false && $module != null ) {
			$is_admin     = current_user_can( 'manage_options' ) ? 1 : 0;
			$saved_search = $bmrequests->bm_fetch_last_saved_search_data( $module, $is_admin );
		}

		echo wp_json_encode( $saved_search );
		die;
	}//end bm_fetch_saved_order_search()


	/**
	 * Fetch saved search for checkins
	 *
	 * @author Darpan
	 */
	public function bm_fetch_saved_checkin_search() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$saved_search = array();
		$bmrequests   = new BM_Request();
		$module       = filter_input( INPUT_POST, 'module' );

		if ( $module != false && $module != null ) {
			$is_admin     = current_user_can( 'manage_options' ) ? 1 : 0;
			$saved_search = $bmrequests->bm_fetch_last_saved_search_data( $module, $is_admin );
		}

		echo wp_json_encode( $saved_search );
		die;
	}//end bm_fetch_saved_checkin_search()


	/**
	 * Fetch primary email field key
	 *
	 * @author Darpan
	 */
	public function bm_get_primary_email_field_key() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$data       = array();

		$primary_email_key  = $bmrequests->bm_check_and_return_field_key_of_primary_email_in_field_data();
		$total_email_fields = $bmrequests->bm_fetch_total_number_of_email_fields_in_active_filelds();

		if ( $total_email_fields > 1 ) {
			$checkbox_html = $bmrequests->bm_fetch_available_email_fields_in_active_filelds_checkbox_html( $primary_email_key );
		}

		$data['primary_email_key']  = $primary_email_key;
		$data['total_email_fields'] = $total_email_fields;
		$data['checkbox_html']      = isset( $checkbox_html ) ? wp_kses( $checkbox_html, $bmrequests->bm_fetch_expanded_allowed_tags() ) : '';

		echo wp_json_encode( $data );
		die;
	}//end bm_get_primary_email_field_key()


	/**
	 * Save primary email field key
	 *
	 * @author Darpan
	 */
	public function bm_save_primary_email_field_key() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();
		$post      = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data      = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id           = isset( $post['id'] ) ? $post['id'] : 0;
			$email_fields = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_type' => 'email' ), 'results' );

			if ( ! empty( $id ) && ! empty( $email_fields ) && is_array( $email_fields ) ) {
				foreach ( $email_fields as $email ) {
					if ( ! empty( $email ) ) {
						$email_id      = isset( $email->id ) ? $email->id : 0;
						$email_options = isset( $email->field_options ) ? maybe_unserialize( $email->field_options ) : array();

						if ( ! empty( $email_options ) && ! empty( $email_id ) ) {
							if ( $email_id == $id ) {
								$email_options['is_main_email'] = 1;
							} elseif ( $email_id !== $id ) {
								$email_options['is_main_email'] = 0;
							}
						}

						$dbhandler->update_row( 'FIELDS', 'id', $email_id, array( 'field_options' => maybe_serialize( $email_options ) ), '', '%d' );
					}
				}

				$data['status'] = true;
			}
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_save_primary_email_field_key()


	/**
	 * Save non primary email as primary
	 *
	 * @author Darpan
	 */
	public function bm_save_non_primary_email_as_primary() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();
		$field_key = filter_input( INPUT_POST, 'field_key' );
		$data      = array( 'status' => false );

		if ( $field_key != false && $field_key != null ) {
			$email_fields = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_type' => 'email' ), 'results' );

			if ( ! empty( $email_fields ) && is_array( $email_fields ) ) {
				foreach ( $email_fields as $email ) {
					if ( ! empty( $email ) ) {
						$email_field_key = isset( $email->field_key ) ? $email->field_key : '';
						$email_options   = isset( $email->field_options ) ? maybe_unserialize( $email->field_options ) : array();

						if ( ! empty( $email_options ) && ! empty( $email_field_key ) ) {
							if ( $email_field_key == $field_key ) {
								$email_options['is_main_email'] = 1;
							} elseif ( $email_field_key !== $field_key ) {
								$email_options['is_main_email'] = 0;
							}
						}

						$dbhandler->update_row( 'FIELDS', 'field_key', $email_field_key, array( 'field_options' => maybe_serialize( $email_options ) ), '', '%s' );
					}
				}

				$data['status'] = true;
			}
		} //end if

		echo wp_json_encode( $data );
		die;
	}//end bm_save_non_primary_email_as_primary()


	/**
	 * Custom cron schedule
	 *
	 * @author Darpan
	 */
	public function bm_custom_cron_schedule( $schedules ) {
		if ( ! isset( $schedules['per_minute'] ) ) {
			$schedules['per_minute'] = array(
				'interval' => 60,
				'display'  => __( 'Once every minute', 'service-booking' ),
			);
		}

		if ( ! isset( $schedules['per_5_minute'] ) ) {
			$schedules['per_5_minute'] = array(
				'interval' => 300,
				'display'  => __( 'Every 5 Minutes', 'service-booking' ),
			);
		}

		return $schedules;
	}//end bm_custom_cron_schedule()


	/**
	 * Check booking requests
	 *
	 * @author Darpan
	 */
	public function bm_check_booking_requests() {
		if ( ! wp_next_scheduled( 'flexibooking_check_expired_book_on_request_bookings' ) ) {
			wp_schedule_event( time(), 'per_minute', 'flexibooking_check_expired_book_on_request_bookings' );
		}
	}//end bm_check_booking_requests()


	/**
	 * Check booking requests
	 *
	 * @author Darpan
	 */
	public function bm_check_falied_emails_and_resend_pdfs() {
		if ( ! wp_next_scheduled( 'bm_resend_missing_emails_hook' ) ) {
        	wp_schedule_event( time(), 'per_5_minute', 'bm_resend_missing_emails_hook' );
    	}
	}//end bm_check_falied_emails_and_resend_pdfs()


	/**
	 * Cron job to resend missing emails for paid bookings with future service dates.
	 * Uses the custom get_results_with_join method to fetch eligible bookings.
	 */
	public function bm_resend_missing_emails_cron() {
		$dbhandler = new BM_DBhandler();

		$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date     = $now->format( 'Y-m-d' );
		$current_time     = $now->format( 'H:i' );
		$current_datetime = $current_date . ' ' . $current_time;

		$results = $dbhandler->get_results_with_join(
			array( 'BOOKING', 'b' ),
			'b.id, b.booking_type, b.mail_sent, b.booking_date, b.booking_slots, b.vouchers',
			array(
				array(
					'table' => 'TRANSACTIONS',
					'alias' => 't',
					'on'    => 'b.id = t.booking_id',
					'type'  => 'INNER',
				),
			),
			array(
				't.payment_status' => array( 'IN' => array( 'succeeded', 'free' ) ),
				'b.mail_sent'      => array( '<' => 3 ),
			),
			'results',
			0,
			false,
			null,
			false,
			'AND b.booking_date IS NOT NULL'
		);

		if ( empty( $results ) ) {
			return;
		}

		foreach ( $results as $booking ) {
			// --- TRANSIENT LOCK: prevent duplicate processing ---
			$lock_key = 'bm_processing_booking_' . $booking->id;
			if ( get_transient( $lock_key ) ) {
				continue; // already being processed
			}
			set_transient( $lock_key, true, 10 * MINUTE_IN_SECONDS ); // lock for 10 minutes

			// Combine date and time from booking_slots
			$service_date = $booking->booking_date;
			$slots        = maybe_unserialize( $booking->booking_slots );
			$from_slot    = isset( $slots['from'] ) ? $slots['from'] : '';
			if ( ! empty( $from_slot ) ) {
				$service_datetime = $service_date . ' ' . $from_slot;
			} else {
				$service_datetime = $service_date . ' 00:00:00';
			}

			// Skip if service date/time has passed
			if ( strtotime( $service_datetime ) <= strtotime( $current_datetime ) ) {
				delete_transient( $lock_key );
				continue;
			}

			// 1. Trigger main email resend based on booking type
			if ( $booking->booking_type === 'direct' ) {
				do_action( 'flexibooking_set_process_new_order', $booking->id );
			} elseif ( $booking->booking_type === 'on_request' ) {
				do_action( 'flexibooking_set_process_new_request', $booking->id );
			}

			// 2. Handle gift/voucher emails if voucher exists and not yet sent
			if ( ! empty( $booking->vouchers ) ) {
				$voucher_emails = $dbhandler->get_all_result(
					'EMAILS',
					'*',
					array(
						'module_type' => 'BOOKING',
						'module_id'   => $booking->id,
						'mail_type'   => 'gift_voucher',
					),
					'results'
				);
				// get_all_result may return null on error; check empty array
				if ( empty( $voucher_emails ) ) {
					do_action( 'flexibooking_set_process_new_order_voucher', $booking->id );
				} else {
					// Optional: log that voucher email already exists
					error_log( "Voucher email already sent for booking {$booking->id}" );
				}
			}

			// Release the lock after processing (optional – keep for the full 10 minutes to be safe)
			// delete_transient( $lock_key );
		}
	}


	/**
	 * Callback of check book on request bookings
	 *
	 * @author Darpan
	 */
	public function flexibooking_check_expired_book_on_request_bookings_callback() {
		$bmrequests     = new BM_Request();
		$dbhandler      = new BM_DBhandler();
		$transactions   = $bmrequests->bm_fetch_book_on_request_transactions();
		$booking_expiry = $dbhandler->get_global_option_value( 'bm_book_on_request_expiry' );

		if ( $booking_expiry <= 0 ) {
			$booking_expiry = 7;
		}

		if ( ! empty( $transactions ) && is_array( $transactions ) ) {
			foreach ( $transactions as $transaction ) {
				$booking_id        = isset( $transaction->booking_id ) ? $transaction->booking_id : 0;
				$creation_datetime = isset( $transaction->transaction_created_at ) ? $transaction->transaction_created_at : '';

				if ( ! empty( $booking_id ) && ! empty( $creation_datetime ) ) {
					$timezone          = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
					$creation_datetime = new DateTime( $creation_datetime, new DateTimeZone( $timezone ) );
					$current_datetime  = new DateTime( 'now', new DateTimeZone( $timezone ) );

					$time_to_compare = clone $creation_datetime;
					$time_to_compare->modify( '+' . $booking_expiry . ' hours' );

					if ( $current_datetime > $time_to_compare ) {
						$bmrequests->bm_cancel_and_refund_order( $booking_id );
						$is_cancelled = $dbhandler->get_global_option_value( 'bm_is_booking_cancelled-' . $booking_id, 0 );

						if ( $is_cancelled == 1 ) {
							$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $booking_id, 'id' );
							if ( $wc_order_id > 0 && ( new WooCommerceService() )->is_enabled() ) {
								$wc_order = wc_get_order( $wc_order_id );

								if ( $wc_order ) {
									$wc_order->update_status( 'cancelled', 'marked from flexi booking plugin' );
									update_post_meta( $wc_order_id, '_is_flexi_order_expired', true );

									$voucher_code = get_post_meta( $wc_order_id, '_flexi_voucher_id', true );

									if ( $voucher_code ) {
										$redeemVoucher = new FlexiVoucherRedeem( $voucher_code );
										$redeemVoucher->markVoucherExpired();
									}
								}
							}
						}
					}
				}
			}
		}
	}//end flexibooking_check_expired_book_on_request_bookings_callback()


	/**
	 * Mark processing bookings as completed
	 *
	 * @author Darpan
	 */
	public function bm_mark_flexi_paid_processing_bookings_as_completed() {
		if ( ! wp_next_scheduled( 'flexibooking_check_paid_expired_processing_bookings' ) ) {
			wp_schedule_event( time(), 'per_minute', 'flexibooking_check_paid_expired_processing_bookings' );
		}
	}//end bm_mark_flexi_paid_processing_bookings_as_completed()


	/**
	 * Mark backend pending bookings as cancelled
	 *
	 * @author Darpan
	 */
	public function bm_mark_pending_bookings_as_cancelled() {
		if ( ! wp_next_scheduled( 'flexibooking_check_expired_pending_bookings' ) ) {
			wp_schedule_event( time(), 'per_minute', 'flexibooking_check_expired_pending_bookings' );
		}
	}//end bm_mark_pending_bookings_as_cancelled()


	/**
	 * Mark expired free bookings as completed
	 *
	 * @author Darpan
	 */
	public function bm_mark_expired_free_bookings_as_completed() {
		if ( ! wp_next_scheduled( 'flexibooking_check_expired_free_bookings' ) ) {
			wp_schedule_event( time(), 'per_minute', 'flexibooking_check_expired_free_bookings' );
		}
	}//end bm_mark_expired_free_bookings_as_completed()


	/**
	 * Check expired vouchers
	 *
	 * @author Darpan
	 */
	public function bm_check_expired_vouchers() {
		if ( ! wp_next_scheduled( 'flexibooking_check_expired_vouchers' ) ) {
			wp_schedule_event( time(), 'per_minute', 'flexibooking_check_expired_vouchers' );
		}
	}//end bm_check_expired_vouchers()


	/**
	 * Callback of check expired vouchers
	 *
	 * @author Darpan
	 */
	public function flexibooking_check_expired_vouchers_callback() {
		$dbhandler = new BM_DBhandler();
		$vouchers  = $dbhandler->get_all_result( 'VOUCHERS', array( 'settings', 'booking_id', 'id', 'created_at' ), array( 'is_expired' => 0 ), 'results' );

		if ( empty( $vouchers ) || ! is_array( $vouchers ) ) {
			return;
		}

		$timezone            = new DateTimeZone( $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' ) );
		$current_datetime    = new DateTime( 'now', $timezone );
		$default_expiry_days = (int) $dbhandler->get_global_option_value( 'bm_voucher_expiry', 30 );

		foreach ( $vouchers as $voucher ) {
			$expiry_date = null;

			if ( ! empty( $voucher->settings ) ) {
				$settings    = maybe_unserialize( $voucher->settings );
				$expiry_date = ! empty( $settings['expiry'] ) ? new DateTime( $settings['expiry'], $timezone ) : null;
			}

			if ( ! $expiry_date && ! empty( $voucher->created_at ) ) {
				$creation_date = new DateTime( $voucher->created_at, $timezone );
				$expiry_date   = $creation_date->add( new DateInterval( "P{$default_expiry_days}D" ) );
			}

			if ( $expiry_date && $current_datetime > $expiry_date ) {
				( new BM_Request() )->bm_mark_vouchers_expired( $voucher->id );
			}
		}
	}//end flexibooking_check_expired_vouchers_callback()



	/**
	 * Mark expired processing transactions as completed
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mark_processing_orders_as_complete( $booking_id = 0 ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$status     = false;

		if ( ! empty( $booking_id ) ) {
			$booking_data = array(
				'order_status'       => 'succeeded',
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_update = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );

			if ( $booking_update ) {
				$status = true;
			}
		}

		return $status;
	} // end bm_flexibooking_mark_processing_orders_as_complete()


	/**
	 * Mark expired free bookings as completed
	 *
	 * @author Darpan
	 */
	public function bm_mark_free_orders_as_complete( $booking_id = 0 ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$status     = false;

		if ( ! empty( $booking_id ) ) {
			$booking_data = array(
				'order_status'       => 'succeeded',
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_update = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
			$wc_order_id    = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $booking_id, 'id' );

			if ( $booking_update ) {
				$status = true;
			}
		}

		return $status;
	} // end bm_mark_free_orders_as_complete()


	/**
	 * Mark book on request transactions as cancelled
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_cancel_booking( $booking_id = 0 ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$cancelled  = false;

		if ( $booking_id > 0 ) {
			$customer_id    = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
			$customer_count = $dbhandler->bm_count( 'TRANSACTIONS', array( 'customer_id' => $customer_id ) );

			$transaction_data = array(
				'payment_status'         => 'cancelled',
				'is_active'              => 0,
				'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_data = array(
				'order_status'       => 'cancelled',
				'is_active'          => 0,
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$slotcount_data = array(
				'is_active'       => 0,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$extra_slotcount_data = array(
				'is_active'       => 0,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$one   = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );
			$two   = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
			$three = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );
			$four  = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

			if ( ( $customer_count == 1 ) ) {
				$customer_data = array(
					'is_active'           => 0,
					'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
				);
				$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );
			}

			if ( $one != false && $two != false && $three != false ) {
				$cancelled = true;
			}
		}

		return $cancelled;
	} // end bm_flexibooking_cancel_booking()


	/**
	 * Mark an order as refunded
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_status_as_refunded( $booking_id = 0, $refund_id = '' ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$updated    = false;

		if ( $booking_id > 0 ) {
			$customer_id    = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
			$customer_count = $dbhandler->bm_count( 'TRANSACTIONS', array( 'customer_id' => $customer_id ) );

			$transaction_data = array(
				'payment_status'         => 'refunded',
				'refund_id'              => $refund_id,
				'is_active'              => 0,
				'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_data = array(
				'order_status'       => 'refunded',
				'is_active'          => 0,
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$slotcount_data = array(
				'is_active'       => 0,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$extra_slotcount_data = array(
				'is_active'       => 0,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$one   = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );
			$two   = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
			$three = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );
			$four  = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

			if ( ( $customer_count == 1 ) ) {
				$customer_data = array(
					'is_active'           => 0,
					'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
				);
				$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );
			}

			if ( $one != false && $two != false && $three != false ) {
				$updated = true;
			}
		}

		return $updated;
	} // end bm_flexibooking_update_status_as_refunded()


	/**
	 * Mark an order as completed
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_status_as_completed( $booking_id = 0 ) {
		$updated = false;

		if ( $booking_id > 0 ) {
			$bmrequests       = new BM_Request();
			$transaction_data = array(
				'payment_status'         => 'succeeded',
				'is_active'              => 1,
				'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_data = array(
				'order_status'       => 'succeeded',
				'is_active'          => 1,
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$slotcount_data = array(
				'is_active'       => 1,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$extra_slotcount_data = array(
				'is_active'       => 1,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$dbhandler = new BM_DBhandler();

			$one   = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );
			$two   = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
			$three = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );
			$four  = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

			$customer_id   = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
			$customer_data = array(
				'is_active'           => 1,
				'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);
			$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );

			if ( $one != false && $two != false && $three != false ) {
				$updated = true;
			}
		}

		return $updated;
	} // end bm_flexibooking_update_status_as_completed()


	/**
	 * Mark an order as processing
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_status_as_processing( $booking_id = 0 ) {
		$updated = false;

		if ( $booking_id > 0 ) {
			$bmrequests       = new BM_Request();
			$transaction_data = array(
				'payment_status'         => 'pending',
				'is_active'              => 1,
				'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_data = array(
				'order_status'       => 'processing',
				'is_active'          => 1,
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$slotcount_data = array(
				'is_active'       => 1,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$extra_slotcount_data = array(
				'is_active'       => 1,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$dbhandler = new BM_DBhandler();

			$one   = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );
			$two   = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
			$three = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );
			$four  = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

			$customer_id   = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
			$customer_data = array(
				'is_active'           => 1,
				'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);
			$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );

			if ( $one != false && $two != false && $three != false ) {
				$updated = true;
			}
		}

		return $updated;
	} // end bm_flexibooking_update_status_as_processing()


	/**
	 * Mark an order as on hold
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_status_as_on_hold( $booking_id = 0 ) {
		$updated = false;

		if ( $booking_id > 0 ) {
			$bmrequests       = new BM_Request();
			$transaction_data = array(
				'payment_status'         => 'pending',
				'is_active'              => 1,
				'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$booking_data = array(
				'order_status'       => 'on_hold',
				'is_active'          => 1,
				'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$slotcount_data = array(
				'is_active'       => 1,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$extra_slotcount_data = array(
				'is_active'       => 1,
				'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$dbhandler = new BM_DBhandler();

			$one   = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );
			$two   = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
			$three = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );
			$four  = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

			$customer_id   = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
			$customer_data = array(
				'is_active'           => 1,
				'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);
			$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );

			if ( $one != false && $two != false && $three != false ) {
				$updated = true;
			}
		}

		return $updated;
	} // end bm_flexibooking_update_status_as_on_hold()


	/**
	 * Refund cancelled book on request transactions
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_refund_cancelled_order( $booking_id = 0 ) {
		$refund_id = '';

		if ( $booking_id > 0 ) {
			$dbhandler      = new BM_DBhandler();
			$transaction_id = $dbhandler->get_value( 'TRANSACTIONS', 'transaction_id', $booking_id, 'booking_id' );

			if ( $transaction_id > 0 ) {
				if ( defined( 'STRIPE_SECRET_KEY' ) ) {
					$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

					if ( $payment_processor->isConnected() ) {
						$cancelled_intent = $payment_processor->cancelPaymentIntent( $transaction_id );

						if ( ! empty( $cancelled_intent ) && isset( $cancelled_intent['status'] ) && $cancelled_intent['status'] == 'canceled' ) {
							$charge_data = isset( $cancelled_intent['charges']['data'][0] ) ? $cancelled_intent['charges']['data'][0] : array();

							if ( ! empty( $charge_data ) ) {
								$refund_data = isset( $charge_data['refunds']['data'][0] ) ? $charge_data['refunds']['data'][0] : array();

								if ( ! empty( $refund_data ) ) {
									$refund_id = isset( $refund_data['id'] ) ? $refund_data['id'] : '';
								}
							}

							$bmrequests       = new BM_Request();
							$transaction_data = array(
								'payment_status'         => 'refunded',
								'refund_id'              => $refund_id,
								'is_active'              => 0,
								'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);

							$booking_data = array(
								'order_status'       => 'refunded',
								'is_active'          => 0,
								'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
							);

							$dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );
							$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
						}
					}
				}
			}
		}

		return $refund_id;
	} // end bm_flexibooking_refund_cancelled_order()


	/**
	 * Callback to check expired processing bookings and mark them as completed
	 *
	 * @author Darpan
	 */
	public function flexibooking_check_paid_expired_processing_bookings_callback() {
		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$bookings     = $bmrequests->bm_fetch_paid_bookings_with_processing_status();
		$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date = $today->format( 'Y-m-d' );
		$current_time = $today->format( 'H:i' );
		$to_slot      = '';

		$currentDateTime = $current_date . ' ' . $current_time;

		if ( ! empty( $bookings ) && is_array( $bookings ) ) {
			foreach ( $bookings as $booking ) {
				$booking_id    = isset( $booking->id ) ? $booking->id : 0;
				$service_date  = isset( $booking->booking_date ) ? $booking->booking_date : '';
				$booking_slots = isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();

				if ( ! empty( $booking_id ) && ! empty( $service_date ) && ! empty( $booking_slots ) && is_array( $booking_slots ) ) {
					$to_slot = isset( $booking_slots['to'] ) ? $booking_slots['to'] : '';

					if ( ! empty( $to_slot ) ) {
						$service_date = $service_date . ' ' . $to_slot;

						if ( strtotime( $service_date ) < strtotime( $currentDateTime ) ) {
							$bmrequests->bm_mark_processing_orders_as_complete( $booking_id );
							$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $booking_id, 'id' );

							if ( $wc_order_id > 0 && ( new WooCommerceService() )->is_enabled() ) {
								$wc_order = wc_get_order( $wc_order_id );

								if ( $wc_order ) {
									$wc_order->update_status( 'completed', 'marked from flexi booking plugin' );
									update_post_meta( $wc_order_id, '_is_flexi_order_expired', true );

									$voucher_code = get_post_meta( $wc_order_id, '_flexi_voucher_id', true );

									if ( $voucher_code ) {
										$redeemVoucher = new FlexiVoucherRedeem( $voucher_code );
										$redeemVoucher->markVoucherExpired();
									}
								}
							}
						}
					}
				}
			}
		}
	}//end flexibooking_check_paid_expired_processing_bookings_callback()


	/**
	 * Callback to check expired pending bookings and mark them as cancelled
	 *
	 * @author Darpan
	 */
	public function flexibooking_check_expired_pending_bookings_callback() {
		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$bookings     = $bmrequests->bm_fetch_unpaid_bookings_with_processing_status();
		$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date = $today->format( 'Y-m-d' );
		$current_time = $today->format( 'H:i' );
		$to_slot      = '';

		$currentDateTime = $current_date . ' ' . $current_time;

		if ( ! empty( $bookings ) && is_array( $bookings ) ) {
			foreach ( $bookings as $booking ) {
				$booking_id    = isset( $booking->id ) ? $booking->id : 0;
				$service_date  = isset( $booking->booking_date ) ? $booking->booking_date : '';
				$booking_slots = isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();

				if ( ! empty( $booking_id ) && ! empty( $service_date ) && ! empty( $booking_slots ) && is_array( $booking_slots ) ) {
					$to_slot = isset( $booking_slots['to'] ) ? $booking_slots['to'] : '';

					if ( ! empty( $to_slot ) ) {
						$service_date = $service_date . ' ' . $to_slot;

						if ( strtotime( $service_date ) < strtotime( $currentDateTime ) ) {
							$bmrequests->bm_cancel_and_refund_order( $booking_id );
							$is_cancelled = $dbhandler->get_global_option_value( 'bm_is_booking_cancelled-' . $booking_id, 0 );

							if ( $is_cancelled == 1 ) {
								$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $booking_id, 'id' );

								if ( $wc_order_id > 0 && ( new WooCommerceService() )->is_enabled() ) {
									$wc_order = wc_get_order( $wc_order_id );

									if ( $wc_order ) {
										$wc_order->update_status( 'cancelled', 'marked from flexi booking plugin' );
										update_post_meta( $wc_order_id, '_is_flexi_order_expired', true );

										$voucher_code = get_post_meta( $wc_order_id, '_flexi_voucher_id', true );

										if ( $voucher_code ) {
											$redeemVoucher = new FlexiVoucherRedeem( $voucher_code );
											$redeemVoucher->markVoucherExpired();
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}//end flexibooking_check_expired_pending_bookings_callback()


	/**
	 * Callback to check expired free bookings and mark them as completed
	 *
	 * @author Darpan
	 */
	public function flexibooking_check_expired_free_bookings_callback() {
		$dbhandler    = new BM_DBhandler();
		$bmrequests   = new BM_Request();
		$bookings     = $bmrequests->bm_fetch_free_bookings();
		$timezone     = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$today        = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date = $today->format( 'Y-m-d' );
		$current_time = $today->format( 'H:i' );
		$to_slot      = '';

		$currentDateTime = $current_date . ' ' . $current_time;

		if ( ! empty( $bookings ) && is_array( $bookings ) ) {
			foreach ( $bookings as $booking ) {
				$booking_id = isset( $booking->id ) ? $booking->id : 0;

				if ( ! empty( $booking_id ) ) {
					$service_date  = isset( $booking->booking_date ) ? $booking->booking_date : '';
					$booking_slots = isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();

					if ( ! empty( $service_date ) && ! empty( $booking_slots ) && is_array( $booking_slots ) ) {
						$to_slot = isset( $booking_slots['to'] ) ? $booking_slots['to'] : '';

						if ( ! empty( $to_slot ) ) {
							$service_date = $service_date . ' ' . $to_slot;

							if ( strtotime( $service_date ) < strtotime( $currentDateTime ) ) {
								$bmrequests->bm_mark_free_orders_as_complete( $booking_id );

								$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $booking_id, 'id' );
								if ( $wc_order_id > 0 && ( new WooCommerceService() )->is_enabled() ) {
									$wc_order = wc_get_order( $wc_order_id );

									if ( $wc_order ) {
										$wc_order->update_status( 'completed', 'marked from flexi booking plugin' );
										update_post_meta( $wc_order_id, '_is_flexi_order_expired', true );

										$voucher_code = get_post_meta( $wc_order_id, '_flexi_voucher_id', true );

										if ( $voucher_code ) {
											$redeemVoucher = new FlexiVoucherRedeem( $voucher_code );
											$redeemVoucher->markVoucherExpired();
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}//end flexibooking_check_expired_free_bookings_callback()


	/**
	 * Fetch value for event notification type
	 *
	 * @author Darpan
	 */
	public function bm_fetch_value_for_notification_event_type() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type     = isset( $post['type'] ) ? $post['type'] : '';
			$response = $bmrequests->bm_fetch_event_type_value_html( $type );

			if ( ! empty( $response ) ) {
				$data['status'] = true;
			}

			$data['value'] = $response;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_value_for_notification_event_type()


	/**
	 * Cancel book on request order
	 *
	 * @author Darpan
	 */
	public function bm_cancel_book_on_request_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$booking_id    = isset( $post['id'] ) ? $post['id'] : 0;
			$paymentStatus = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking_id, 'booking_id' );
			$is_active     = $dbhandler->get_value( 'TRANSACTIONS', 'is_active', $booking_id, 'booking_id' );

			if ( $paymentStatus == 'requires_capture' && $is_active == 1 ) {
				$bmrequests->bm_cancel_and_refund_order( $booking_id );
				$is_cancelled = $dbhandler->get_global_option_value( 'bm_is_booking_cancelled-' . $booking_id, 0 );

				if ( $is_cancelled == 1 ) {
					$data['status'] = true;
				}
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_cancel_book_on_request_order()


	/**
	 * Approve book on request order
	 *
	 * @author Darpan
	 */
	public function bm_approve_book_on_request_order() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$id = isset( $post['id'] ) ? $post['id'] : 0;
			$bmrequests->bm_approve_pending_book_on_request_order( $id );
			$is_approved = $dbhandler->get_global_option_value( 'bm_is_book_on_request_approved-' . $id, 0 );

			if ( $is_approved == 1 ) {
				$data['status'] = true;
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_approve_book_on_request_order()


	/**
	 * Update order transaction data
	 *
	 * @author Darpan
	 */
	public function bm_update_order_transaction() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$booking_id        = isset( $post['id'] ) ? $post['id'] : 0;
			$is_active         = $dbhandler->get_value( 'TRANSACTIONS', 'is_active', $booking_id, 'booking_id' );
			$transaction_data  = apply_filters( 'flexibooking_fetch_order_transaction_data', $booking_id );
			$html              = apply_filters( 'flexibooking_fetch_html_with_transaction_data', $transaction_data );
			$data['html']      = wp_kses( $html, $bmrequests->bm_fetch_expanded_allowed_tags() );
			$data['is_active'] = $is_active;
			$data['status']    = true;
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_update_order_transaction()


	/**
	 * Save order transaction data
	 *
	 * @author Darpan
	 */
	public function bm_save_order_transaction() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( $post != false && $post != null ) {
			$booking_id     = isset( $post['id'] ) ? $post['id'] : 0;
			$transaction_id = isset( $post['transaction_id'] ) ? $post['transaction_id'] : '';
			$payment_status = isset( $post['payment_status'] ) ? $post['payment_status'] : '';
			$refund_id      = isset( $post['refund_id'] ) ? $post['refund_id'] : '';
			$is_active      = isset( $post['is_active'] ) ? $post['is_active'] : '';

			$status = apply_filters( 'flexibooking_save_order_transaction_data', $booking_id, $transaction_id, $refund_id, $payment_status, $is_active );
		}

		echo wp_kses_post( $status );
		die;
	}//end bm_save_order_transaction()


	/**
	 * Fetch order transaction data
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_fetch_order_transaction_data( $booking_id ) {
		$dbhandler        = new BM_DBhandler();
		$transaction_data = $dbhandler->get_all_result( 'TRANSACTIONS', '*', array( 'booking_id' => $booking_id ), 'results', 0, false, null, false, '', 'ARRAY_A' );
		return $transaction_data[0];
	}//end bm_flexibooking_fetch_order_transaction_data()


	/**
	 * Fetch order transaction data with html
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_fetch_html_with_transaction_data( $transaction_data ) {
		$bmrequests = new BM_Request();
		$html       = $bmrequests->bm_fetch_html_with_order_transaction( $transaction_data );
		return $html;
	}//end bm_flexibooking_fetch_html_with_transaction_data()


	/**
	 * Save order transaction data
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_save_order_transaction_data( $booking_id, $transaction_id, $refund_id, $payment_status, $is_active ) {
		$dbhandler     = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$status        = 0;
		$update_status = 1;

		if ( empty( $booking_id ) ) {
			return $status;
		}

		do_action( 'flexibooking_save_existing_transaction_data_before_update', $booking_id );

		$transaction_id_before_update = $dbhandler->bm_fetch_data_from_transient( 'transaction_id_before_update_' . $booking_id );

		if ( ! empty( $transaction_id_before_update ) && ( $transaction_id_before_update != $transaction_id ) ) {
			$status = apply_filters( 'flexibooking_verify_if_valid_transaction_id', $booking_id, $transaction_id, $payment_status );

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $payment_status == 'succeeded' ) {
			$status = apply_filters( 'flexibooking_verify_if_paid_transaction_id', $transaction_id );

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $payment_status == 'pending' ) {
			$status = apply_filters( 'flexibooking_verify_if_pending_transaction_id', $transaction_id );

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $payment_status == 'cancelled' ) {
			$status = apply_filters( 'flexibooking_verify_if_cancelled_transaction_id', $transaction_id );

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $payment_status == 'free' ) {
			$status = apply_filters( 'flexibooking_verify_transaction_for_free_payment_status', $transaction_id );

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $payment_status == 'refunded' ) {
			$is_frontend_booking = $dbhandler->bm_fetch_data_from_transient( 'is_frontend_booking_' . $booking_id );

			if ( $is_frontend_booking == 0 && empty( $transaction_id ) ) {
				$status = 1;
			} else {
				$status = apply_filters( 'flexibooking_verify_if_refunded_transaction_id', $refund_id );
			}

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $payment_status == 'failed' ) {
			$status = apply_filters( 'flexibooking_check_and_remove_duplicate_record_in_failed_transaction_table', $booking_id );

			if ( $status == 1 ) {
				$status = apply_filters( 'flexibooking_add_data_to_failed_transaction_table', $booking_id, $transaction_id );

				if ( $status == 1 ) {
					$status = apply_filters( 'flexibooking_update_booking_data_before_marking_transaction_failed', $booking_id );
				}
			}

			if ( $status != 1 ) {
				return $status;
			}
		}

		if ( $status == 1 ) {
			if ( $is_active == 0 || $payment_status == 'refunded' || $payment_status == 'succeeded' || $payment_status == 'free' || $payment_status == 'pending' ) {
				$status = apply_filters( 'flexibooking_update_booking_data_after_transaction_update', $booking_id, $payment_status );
			}

			if ( $payment_status != 'cancelled' ) {
				if ( $payment_status == 'refunded' ) {
					$is_active = 0;
				} elseif ( $payment_status == 'failed' ) {
					$is_active = 2;
				}

				$transaction_data = array(
					'transaction_id'         => $transaction_id,
					'payment_status'         => $payment_status,
					'refund_id'              => $payment_status == 'refunded' ? $refund_id : '',
					'is_active'              => $is_active,
					'transaction_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
				);

				$status = apply_filters( 'flexibooking_update_transaction_data', $booking_id, $transaction_data );
			}

			if ( $payment_status == 'cancelled' ) {
				$bmrequests->bm_cancel_and_refund_order( $booking_id );
			}

			if ( $payment_status == 'failed' ) {
				$booking_key = $dbhandler->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );
				do_action( 'flexibooking_set_process_failed_order', $booking_key );
			}
		}

		if ( $status == 0 || $status == 2 || $status == 3 || $status == 4 || $update_status == 0 ) {
			$status = apply_filters( 'flexibooking_revert_transaction_update', $booking_id );
		}

		return $status;
	}//end bm_flexibooking_save_order_transaction_data()


	/**
	 * Save old transaction data
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_save_existing_transaction_data_before_update( $booking_id ) {
		$dbhandler = new BM_DBhandler();

		$customer_id                             = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
		$transaction_data_before_update          = $dbhandler->get_all_result( 'TRANSACTIONS', '*', array( 'booking_id' => $booking_id ), 'results', 0, false, null, false, '', 'ARRAY_A' );
		$transaction_data_before_update          = $transaction_data_before_update[0];
		$transaction_id_before_update            = isset( $transaction_data_before_update['transaction_id'] ) ? $transaction_data_before_update['transaction_id'] : '';
		$paid_amount_before_update               = isset( $transaction_data_before_update['paid_amount'] ) ? $transaction_data_before_update['paid_amount'] : '';
		$paid_currency_before_update             = isset( $transaction_data_before_update['paid_amount_currency'] ) ? $transaction_data_before_update['paid_amount_currency'] : '';
		$refund_id_before_update                 = isset( $transaction_data_before_update['refund_id'] ) ? $transaction_data_before_update['refund_id'] : '';
		$booking_order_status_before_update      = $dbhandler->get_value( 'BOOKING', 'order_status', $booking_id, 'id' );
		$is_frontend_booking                     = $dbhandler->get_value( 'BOOKING', 'is_frontend_booking', $booking_id, 'id' );
		$booking_is_active_before_update         = $dbhandler->get_value( 'BOOKING', 'is_active', $booking_id, 'id' );
		$slotcount_is_active_before_update       = $dbhandler->get_value( 'SLOTCOUNT', 'is_active', $booking_id, 'booking_id' );
		$extra_slotcount_is_active_before_update = $dbhandler->get_value( 'EXTRASLOTCOUNT', 'is_active', $booking_id, 'booking_id' );
		$customer_is_active_before_update        = $dbhandler->get_value( 'CUSTOMERS', 'is_active', $customer_id, 'id' );

		$dbhandler->bm_save_data_to_transient( 'transaction_data_before_update_' . $booking_id, $transaction_data_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'transaction_id_before_update_' . $booking_id, $transaction_id_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'paid_amount_before_update_' . $booking_id, $paid_amount_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'refund_id_before_update_' . $booking_id, $refund_id_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'booking_order_status_before_update_' . $booking_id, $booking_order_status_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'paid_currency_before_update_' . $booking_id, $paid_currency_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'booking_is_active_before_update_' . $booking_id, $booking_is_active_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'slotcount_is_active_before_update_' . $booking_id, $slotcount_is_active_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'extra_slotcount_is_active_before_update_' . $booking_id, $extra_slotcount_is_active_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'customer_is_active_before_update_' . $booking_id, $customer_is_active_before_update, 1 );
		$dbhandler->bm_save_data_to_transient( 'is_frontend_booking_' . $booking_id, $is_frontend_booking, 1 );
	}//end bm_flexibooking_save_existing_transaction_data_before_update()


	/**
	 * Verify new transaction id
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_verify_if_valid_transaction_id( $booking_id, $transaction_id, $payment_status ) {
		$dbhandler                 = new BM_DBhandler();
		$existing_transaction      = $dbhandler->get_row( 'TRANSACTIONS', $transaction_id, 'id' );
		$existing_booking_id       = isset( $existing_transaction->booking_id ) ? $existing_transaction->booking_id : 0;
		$paid_amount_before_update = $dbhandler->bm_fetch_data_from_transient( 'paid_amount_before_update_' . $booking_id );
		$is_frontend_booking       = $dbhandler->bm_fetch_data_from_transient( 'is_frontend_booking_' . $booking_id );
		$status                    = 1;

		if ( $is_frontend_booking == 0 && empty( $transaction_id ) ) {
			return $status;
		}

		if ( ! empty( $existing_transaction ) && ( $booking_id != $existing_booking_id ) ) {
			return 6;
		}

		if ( empty( $paid_amount_before_update ) && empty( $transaction_id ) && $payment_status == 'free' ) {
			return $status;
		}

		$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
		$get_transaction   = $payment_processor->getPaymentIntent( $transaction_id );
		$get_paid_amount   = isset( $get_transaction['amount'] ) ? $get_transaction['amount'] : 0;
		$get_paid_amount   = ( $get_paid_amount / 100 );
		$get_paid_currency = isset( $get_transaction['currency'] ) ? $get_transaction['currency'] : '';
		$get_customer_id   = isset( $get_transaction['customer'] ) ? $get_transaction['customer'] : '';

		$customer_id                    = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
		$stripe_customer_id             = $dbhandler->get_value( 'CUSTOMERS', 'stripe_id', $customer_id, 'id' );
		$transaction_data_before_update = $dbhandler->bm_fetch_data_from_transient( 'transaction_data_before_update_' . $booking_id );
		$paid_currency_before_update    = $dbhandler->bm_fetch_data_from_transient( 'paid_currency_before_update_' . $booking_id );

		if ( ! $get_transaction || ( $paid_amount_before_update != $get_paid_amount ) || ( $paid_currency_before_update != $get_paid_currency ) || ( $stripe_customer_id != $get_customer_id ) ) {
			$status = 2;
		}

		return $status;
	}//end bm_flexibooking_verify_if_valid_transaction_id()


	/**
	 * Verify if paid transaction
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_verify_if_paid_transaction_id( $transaction_id ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$status     = 1;

		$booking_id           = $dbhandler->get_value( 'TRANSACTIONS', 'booking_id', $transaction_id, 'id' );
		$is_frontend_booking  = $dbhandler->bm_fetch_data_from_transient( 'is_frontend_booking_' . $booking_id );
		$payment_processor    = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
		$get_transaction      = $payment_processor->getPaymentIntent( $transaction_id );
		$get_payment_status   = isset( $get_transaction['status'] ) ? $get_transaction['status'] : '';
		$paid_intent_statuses = apply_filters( 'flexibooking_paid_transaction_statuses', $bmrequests->bm_fetch_paid_transaction_statuses() );

		if ( $is_frontend_booking == 0 && empty( $transaction_id ) ) {
			return $status;
		}

		if ( ! in_array( $get_payment_status, $paid_intent_statuses ) ) {
			$status = 2;
		}

		return $status;
	}//end bm_flexibooking_verify_if_paid_transaction_id()


	/**
	 * Paid transaction statuses
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_paid_transaction_statuses( $statuses ) {
		return $statuses;
	} //end bm_bm_flexibooking_paid_transaction_statuses()


	/**
	 * Pending transaction statuses
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_pending_transaction_statuses( $statuses ) {
		return $statuses;
	}//end bm_flexibooking_pending_transaction_statuses()


	/**
	 * Verify if pending transaction
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_verify_if_pending_transaction_id( $transaction_id ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$status     = 1;

		$booking_id               = $dbhandler->get_value( 'TRANSACTIONS', 'booking_id', $transaction_id, 'id' );
		$is_frontend_booking      = $dbhandler->bm_fetch_data_from_transient( 'is_frontend_booking_' . $booking_id );
		$payment_processor        = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
		$get_transaction_id       = $payment_processor->getPaymentIntent( $transaction_id );
		$get_payment_status       = isset( $get_transaction_id['status'] ) ? $get_transaction_id['status'] : '';
		$pending_payment_Statuses = apply_filters( 'flexibooking_pending_transaction_statuses', $bmrequests->bm_fetch_pending_transaction_statuses() );

		if ( $is_frontend_booking == 0 && empty( $transaction_id ) ) {
			return $status;
		}

		if ( ! in_array( $get_payment_status, $pending_payment_Statuses ) ) {
			$status = 2;
		}

		return $status;
	}//end bm_flexibooking_verify_if_pending_transaction_id()


	/**
	 * Verify if cancelled transaction
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_verify_if_cancelled_transaction_id( $transaction_id ) {
		$dbhandler = new BM_DBhandler();
		$status    = 1;

		$booking_id          = $dbhandler->get_value( 'TRANSACTIONS', 'booking_id', $transaction_id, 'id' );
		$is_frontend_booking = $dbhandler->bm_fetch_data_from_transient( 'is_frontend_booking_' . $booking_id );
		$payment_processor   = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
		$get_transaction     = $payment_processor->getPaymentIntent( $transaction_id );
		$get_cancel_status   = isset( $get_transaction['canceled_at'] ) ? $get_transaction['canceled_at'] : '';

		if ( $is_frontend_booking == 0 && empty( $transaction_id ) ) {
			return $status;
		}

		if ( $get_cancel_status == null ) {
			$status = 2;
		}

		return $status;
	}//end bm_flexibooking_verify_if_cancelled_transaction_id()


	/**
	 * Verify if free transaction
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_verify_transaction_for_free_payment_status( $transaction_id ) {
		$dbhandler = new BM_DBhandler();
		$status    = 1;

		$booking_id          = $dbhandler->get_value( 'TRANSACTIONS', 'booking_id', $transaction_id, 'id' );
		$is_frontend_booking = $dbhandler->bm_fetch_data_from_transient( 'is_frontend_booking_' . $booking_id );

		if ( $is_frontend_booking == 0 && empty( $transaction_id ) ) {
			return $status;
		}

		if ( ! empty( $transaction_id ) ) {
			$status = 3;
		}

		return $status;
	}//end bm_flexibooking_verify_transaction_for_free_payment_status()


	/**
	 * Verify if refunded transaction
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_verify_if_refunded_transaction_id( $refund_id ) {
		$status = 1;

		$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
		$refund            = $payment_processor->getRefund( $refund_id );

		if ( ! $refund ) {
			$status = 4;
		}

		return $status;
	}//end bm_flexibooking_verify_if_refunded_transaction_id()


	/**
	 * Update transaction data
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_transaction_data( $booking_id, $transaction_data ) {
		$dbhandler = new BM_DBhandler();
		$status    = 0;

		if ( empty( $booking_id ) ) {
			return $status;
		}

		$transaction_update = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data, '', '%d' );

		if ( ! is_wp_error( $transaction_update ) ) {
			$status = 1;
		}

		return $status;
	}//end bm_flexibooking_update_transaction_data()


	/**
	 * Update booking related data before updating transaction as failed
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_booking_data_before_marking_transaction_failed( $booking_id ) {
		$dbhandler     = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$status        = 1;
		$customer_data = array();

		if ( empty( $booking_id ) ) {
			return 0;
		}

		$customer_id    = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
		$customer_count = $dbhandler->bm_count( 'TRANSACTIONS', array( 'customer_id' => $customer_id ) );

		$booking_data = array(
			'is_active'          => 2,
			'order_status'       => 'failed',
			'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$slotcount_data = array(
			'is_active'       => 2,
			'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$extra_slotcount_data = array(
			'is_active'       => 2,
			'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		if ( ( $customer_count == 1 ) ) {
			$customer_data = array(
				'is_active'           => 2,
				'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);
		}

		$booking_update = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );

		if ( is_wp_error( $booking_update ) ) {
			$status = 0;
		}

		$slotcount_update = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );

		if ( is_wp_error( $slotcount_update ) ) {
			$status = 0;
		}

		$extra_slotcount_update = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

		if ( is_wp_error( $extra_slotcount_update ) ) {
			$status = 0;
		}

		if ( ! empty( $customer_data ) ) {
			$customer_update = $dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );

			if ( is_wp_error( $customer_update ) ) {
				$status = 0;
			}
		}

		return $status;
	}//end bm_flexibooking_update_booking_data_before_marking_transaction_failed()


	/**
	 * Update transaction as failed
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_add_data_to_failed_transaction_table( $booking_id, $transaction_id ) {
		$dbhandler             = new BM_DBhandler();
		$bmrequests            = new BM_Request();
		$status                = 0;
		$failed_transaction_id = 0;
		$failed_booking_data   = array();

		if ( empty( $booking_id ) ) {
			return;
		}

		$customer_id        = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
		$stripe_customer_id = $dbhandler->get_value( 'CUSTOMERS', 'stripe_id', $customer_id, 'id' );
		$customer           = $dbhandler->get_row( 'CUSTOMERS', $customer_id, 'id' );
		$booking_key        = $dbhandler->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );
		$checkout_key       = $dbhandler->get_value( 'BOOKING', 'checkout_key', $booking_id, 'id' );
		$refund_id          = $dbhandler->bm_fetch_data_from_transient( 'refund_id_before_update_' . $booking_id );
		$gift_key           = base64_encode( $booking_key );
		$gift_data          = $dbhandler->bm_fetch_data_from_transient( $gift_key );

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$failed_booking_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$failed_booking_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		if ( empty( $failed_booking_data ) ) {
			$booking = $dbhandler->get_row( 'BOOKING', $booking_id, 'id' );

			if ( ! empty( $booking ) ) {
				$extra_svc_booked         = isset( $booking->extra_svc_booked ) ? explode( ',', $booking->extra_svc_booked ) : '';
				$extra_svc_booked         = count( $extra_svc_booked );
				$total_extra_slots_booked = $dbhandler->get_all_result( 'EXTRASLOTCOUNT', 'slots_booked', array( 'booking_id' => 2 ), 'results' );
				$total_extra_slots_booked = ! empty( $total_extra_slots_booked ) ? array_sum( array_column( $total_extra_slots_booked, 'slots_booked' ) ) : 0;
				$service_id               = isset( $booking->service_id ) ? $booking->service_id : 0;
				$service_price_module_id  = $dbhandler->get_value( 'SERVICE', 'external_price_module', $service_id, 'id' );

				$failed_booking_data = array(
					'service_id'               => $service_id,
					'booking_slots'            => isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array(),
					'booking_date'             => isset( $booking->booking_date ) ? $booking->booking_date : '',
					'service_name'             => $bmrequests->bm_fetch_service_name_by_service_id( $service_id ),
					'total_service_booking'    => $dbhandler->get_value( 'SLOTCOUNT', 'current_total_booking', $booking_id, 'booking_id' ),
					'extra_svc_booked'         => $extra_svc_booked,
					'total_extra_slots_booked' => $total_extra_slots_booked,
					'base_svc_price'           => isset( $booking->base_svc_price ) ? $booking->base_svc_price : 0,
					'service_cost'             => isset( $booking->service_cost ) ? $booking->service_cost : 0,
					'svc_price_module_id'      => $service_price_module_id,
					'extra_svc_cost'           => isset( $booking->extra_svc_cost ) ? $booking->extra_svc_cost : 0,
					'total_cost'               => isset( $booking->total_cost ) ? $booking->total_cost : 0,
				);
			}
		}

		if ( ! empty( $customer ) ) {
			$failed_customer_data['billing_details']  = isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : array();
			$failed_customer_data['shipping_details'] = isset( $customer->shipping_details ) ? maybe_unserialize( $customer->shipping_details ) : array();

			$failed_customer_data['other_data']['shipping_same_as_billing'] = isset( $customer->shipping_same_as_billing ) ? $customer->shipping_same_as_billing : -1;
		}

		$failed_transaction_data = array(
			'customer_id'        => $customer_id,
			'transaction_id'     => $transaction_id,
			'stripe_customer_id' => $stripe_customer_id,
			'amount'             => $dbhandler->bm_fetch_data_from_transient( 'paid_amount_before_update_' . $booking_id ),
			'amount_currency'    => $dbhandler->bm_fetch_data_from_transient( 'paid_currency_before_update_' . $booking_id ),
			'booking_data'       => $failed_booking_data,
			'customer_data'      => $failed_customer_data,
			'gift_data'          => $gift_data,
			'is_refunded'        => ! empty( $refund_id ) ? 1 : 0,
			'refund_id'          => $refund_id,
			'payment_status'     => 'failed',
			'refund_status'      => ! empty( $refund_id ) ? 'succeeded' : '',
			'booking_key'        => $booking_key,
			'checkout_key'       => $checkout_key,
		);

		$failed_transaction_data = $bmrequests->sanitize_request( $failed_transaction_data, 'FAILED_TRANSACTIONS' );

		if ( $failed_transaction_data != false ) {
			$failed_transaction_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
			$failed_transaction_id                 = $dbhandler->insert_row( 'FAILED_TRANSACTIONS', $failed_transaction_data );
		}

		if ( ! empty( $failed_transaction_id ) ) {
			$status = 1;
		}

		return $status;
	}//end bm_flexibooking_add_data_to_failed_transaction_table()


	/**
	 * Update booking data after transaction update
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_update_booking_data_after_transaction_update( $booking_id, $payment_status ) {
		$dbhandler     = new BM_DBhandler();
		$bmrequests    = new BM_Request();
		$status        = 1;
		$customer_data = array();

		if ( empty( $booking_id ) ) {
			return 0;
		}

		$customer_id    = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
		$customer_count = $dbhandler->bm_count( 'TRANSACTIONS', array( 'customer_id' => $customer_id ) );

		$booking_data = array(
			'is_active'          => $payment_status == 'refunded' ? 0 : 1,
			'order_status'       => $payment_status,
			'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$slotcount_data = array(
			'is_active'       => $payment_status == 'refunded' ? 0 : 1,
			'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$extra_slotcount_data = array(
			'is_active'       => $payment_status == 'refunded' ? 0 : 1,
			'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		if ( ( $customer_count == 1 ) ) {
			$customer_data = array(
				'is_active'           => $payment_status == 'refunded' ? 0 : 1,
				'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
			);
		}

		$booking_update = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );

		if ( is_wp_error( $booking_update ) ) {
			$status = 0;
		}

		$slotcount_update = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );

		if ( is_wp_error( $slotcount_update ) ) {
			$status = 0;
		}

		$extra_slotcount_update = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

		if ( is_wp_error( $extra_slotcount_update ) ) {
			$status = 0;
		}

		if ( ! empty( $customer_data ) ) {
			$customer_update = $dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );

			if ( is_wp_error( $customer_update ) ) {
				$status = 0;
			}
		}

		return $status;
	}//end bm_flexibooking_update_booking_data_after_transaction_update()


	/**
	 * Remove duplicate record in failed transaction table
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_check_and_remove_duplicate_record_in_failed_transaction_table( $booking_id ) {
		$dbhandler   = new BM_DBhandler();
		$status      = 1;
		$booking_key = $dbhandler->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );

		if ( empty( $booking_key ) ) {
			return 0;
		}

		$failed_transaction         = $dbhandler->get_row( 'FAILED_TRANSACTIONS', $booking_key, 'booking_key' );
		$removed_failed_transaction = $dbhandler->remove_row( 'FAILED_TRANSACTIONS', 'booking_key', $booking_key, '%s' );

		if ( ! empty( $failed_transaction ) && is_wp_error( $removed_failed_transaction ) ) {
			$status = 0;
		}

		return $status;
	}//end bm_flexibooking_check_and_remove_duplicate_record_in_failed_transaction_table()


	/**
	 * Revert transaction update
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_revert_transaction_update( $booking_id ) {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$status     = 5;

		if ( empty( $booking_id ) ) {
			return false;
		}

		$customer_id                             = $dbhandler->get_value( 'TRANSACTIONS', 'customer_id', $booking_id, 'booking_id' );
		$transaction_data_before_update          = $dbhandler->bm_fetch_data_from_transient( 'transaction_data_before_update_' . $booking_id );
		$booking_order_status_before_update      = $dbhandler->bm_fetch_data_from_transient( 'booking_order_status_before_update_' . $booking_id );
		$booking_is_active_before_update         = $dbhandler->bm_fetch_data_from_transient( 'booking_is_active_before_update_' . $booking_id );
		$slotcount_is_active_before_update       = $dbhandler->bm_fetch_data_from_transient( 'slotcount_is_active_before_update_' . $booking_id );
		$extra_slotcount_is_active_before_update = $dbhandler->bm_fetch_data_from_transient( 'extra_slotcount_is_active_before_update_' . $booking_id );
		$customer_is_active_before_update        = $dbhandler->bm_fetch_data_from_transient( 'customer_is_active_before_update_' . $booking_id );

		$transaction_update = $dbhandler->update_row( 'TRANSACTIONS', 'booking_id', $booking_id, $transaction_data_before_update, '', '%d' );

		if ( is_wp_error( $transaction_update ) ) {
			$status = 0;
		}

		$booking_data = array(
			'order_status'       => $booking_order_status_before_update,
			'is_active'          => $booking_is_active_before_update,
			'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$slotcount_data = array(
			'is_active'       => $slotcount_is_active_before_update,
			'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$extra_slotcount_data = array(
			'is_active'       => $extra_slotcount_is_active_before_update,
			'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$customer_data = array(
			'is_active'           => $customer_is_active_before_update,
			'customer_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$booking_update = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );

		if ( is_wp_error( $booking_update ) ) {
			$status = 0;
		}

		$slotcount_update = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $booking_id, $slotcount_data, '', '%d' );

		if ( is_wp_error( $slotcount_update ) ) {
			$status = 0;
		}

		$extra_slotcount_update = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, $extra_slotcount_data, '', '%d' );

		if ( is_wp_error( $extra_slotcount_update ) ) {
			$status = 0;
		}

		$customer_update = $dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_data, '', '%d' );

		if ( is_wp_error( $customer_update ) ) {
			$status = 0;
		}

		return $status;
	}//end bm_flexibooking_revert_transaction_update()


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
	 * Fetch voucher booking info for voucher lisitng page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_vocuher_booking_info() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$order_id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		if ( empty( $order_id ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		try {
			$booking = ( new BM_Request() )->bm_fetch_product_info_order_details_page( $order_id, true );
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( empty( $booking ) ) {
			wp_send_json_error( __( 'No booking found.', 'service-booking' ) );
			return;
		}

		wp_send_json_success( $booking );
	} ///end bm_fetch_vocuher_booking_info()


	/**
	 * Fetch voucher gifter info for voucher lisitng page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_vocuher_gifter_info() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$order_id = filter_input( INPUT_POST, 'order_id', FILTER_VALIDATE_INT );
		if ( empty( $order_id ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		try {
			$customer_data = ( new BM_Request() )->get_customer_info_for_order( $order_id );
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( empty( $customer_data ) ) {
			wp_send_json_error( __( 'No customer found.', 'service-booking' ) );
			return;
		}

		wp_send_json_success( $customer_data );
	} ///end bm_fetch_vocuher_gifter_info()


	/**
	 * Fetch voucher recipient info for voucher lisitng page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_vocuher_recipient_info() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$code = trim( stripslashes( sanitize_text_field( filter_input( INPUT_POST, 'code' ) ) ) );
		if ( empty( $code ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$redeemVoucher = new FlexiVoucherRedeem( $code );

		try {
			$voucher = $redeemVoucher->getVoucherInfo();
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $voucher['error'] ) ) {
			wp_send_json_error( $voucher['error'] );
			return;
		}

		$voucher        = $voucher[0];
		$recipinet_data = isset( $voucher['recipient_data'] ) && ! empty( $voucher['recipient_data'] ) ? maybe_unserialize( $voucher['recipient_data'] ) : array();

		$recipinet_data['recipient_country'] = isset( $recipinet_data['recipient_country'] ) && ! empty( $recipinet_data['recipient_country'] ) ? ( new BM_Request() )->bm_get_countries( $recipinet_data['recipient_country'] ) : '';

		wp_send_json_success( $recipinet_data );
	} ///end bm_fetch_vocuher_recipient_info()


	/**
	 * Fetch voucher booking info for voucher lisitng page
	 *
	 * @author Darpan
	 */
	public function bm_change_voucher_status() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['code'] ) || ! isset( $post['status'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$code   = trim( stripslashes( sanitize_text_field( $post['code'] ) ) );
		$status = trim( stripslashes( sanitize_text_field( $post['status'] ) ) );

		if ( empty( $code ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$redeemVoucher = new FlexiVoucherRedeem( $code );

		try {
			$validate = $redeemVoucher->validateVoucherForStatusChange();
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Something went wrong.', 'service-booking' ) );
			return;
		}

		if ( isset( $validate['error'] ) ) {
			wp_send_json_error( $validate['error'] );
			return;
		}

		try {
			$redeemVoucher->updateVoucherInfo(
				array(
					'status'     => $status,
					'updated_at' => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
				)
			);
		} catch ( Exception $e ) {
			wp_send_json_error( __( 'Status could not be updated.', 'service-booking' ) );
			return;
		}

		wp_send_json_success();
	}//end bm_change_voucher_status()


	/**
	 *  Handle QR code verification
	 *
	 * @author Darpan
	 */
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

		$is_active = $db->get_value( 'BOOKING', 'is_active', $booking->id, 'id' );

		if ( $is_active != 1 ) {
			wp_send_json_error( __( 'Can not check in cancelled or refunded orders', 'service-booking' ) );
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
	} ///end bm_handle_qr_verification()


	public function bm_qr_checkin_process() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$reference = isset( $_POST['booking_reference'] ) ? sanitize_text_field( $_POST['booking_reference'] ) : '';
		if ( ! $reference ) {
			wp_send_json_error( __( 'Invalid QR code.', 'service-booking' ) );
			return;
		}

		$db         = new BM_DBhandler();
		$booking_id = $db->get_value( 'BOOKING', 'id', $reference, 'booking_key' );
		$is_active  = $db->get_value( 'BOOKING', 'is_active', $search_value, 'booking_key' );

		if ( $is_active != 1 ) {
			wp_send_json_error( __( 'Can not check in cancelled or refunded orders', 'service-booking' ) );
			return;
		}

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


	/**
	 *  Handle manual checkin
	 *
	 * @author Darpan
	 */
	public function bm_manual_checkin_process() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
		}

		$search_type = sanitize_text_field( filter_input( INPUT_POST, 'search_type' ) ?? '' );
		$raw_value   = $_POST['search_value'] ?? '';

		if ( is_array( $raw_value ) ) {
			$search_value = array_map( 'sanitize_text_field', $raw_value );
		} else {
			$search_value = sanitize_text_field( $raw_value );
		}

		$booking_ids = filter_input( INPUT_POST, 'booking_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		$db = new BM_DBhandler();

		if ( $search_type === 'reference' ) {
			$booking_id = $db->get_value( 'BOOKING', 'id', $search_value, 'booking_key' );
			$is_active  = $db->get_value( 'BOOKING', 'is_active', $search_value, 'booking_key' );

			if ( $is_active != 1 ) {
				wp_send_json_error( __( 'Can not check in cancelled or refunded orders', 'service-booking' ) );
				return;
			}

			if ( ! $booking_id ) {
				wp_send_json_error( __( 'Booking not found', 'service-booking' ) );
				return;
			}

			$success = $this->bm_mark_booking_checked_in( (int) $booking_id, $db );
			if ( ! $success ) {
				wp_send_json_error( __( 'Already checked in or expired.', 'service-booking' ) );
			}

			wp_send_json_success( array( 'message' => __( 'Booking successfully checked in.', 'service-booking' ) ) );
		}

		if ( empty( $booking_ids ) ) {
			wp_send_json_error( __( 'No bookings selected.', 'service-booking' ) );
		}

		$count = 0;
		foreach ( $booking_ids as $id ) {
			$is_active = $db->get_value( 'BOOKING', 'is_active', $id, 'id' );

			if ( $is_active != 1 ) {
				continue;
			}

			if ( $this->bm_mark_booking_checked_in( (int) $id, $db ) ) {
				++$count;
			}
		}

		if ( $count === 0 ) {
			wp_send_json_error( __( 'No valid bookings were checked in.', 'service-booking' ) );
		}

		wp_send_json_success( array( 'message' => sprintf( __( '%d bookings successfully checked in.', 'service-booking' ), $count ) ) );
	}//end bm_manual_checkin_process()


	private function bm_mark_booking_checked_in( int $booking_id, BM_DBhandler $db ): bool {
		$now = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();

		$data = array(
			'qr_scanned'   => 1,
			'status'       => 'checked_in',
			'qr_token'     => $db->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' ),
			'booking_id'   => $booking_id,
			'checkin_time' => $now,
			'updated_at'   => $now,
		);

		$existing = $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );

		if ( $existing ) {
			return $db->update_row( 'CHECKIN', 'booking_id', $booking_id, $data );
		} else {
			return $db->insert_row( 'CHECKIN', $data );
		}
	}


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

		if ( ! empty( $main_product ) ) {
			$price = floatval( $main_product['amount'] );
			$qty   = intval( $main_product['quantity'] ?? 1 );

			$items[] = array(
				'itemId'   => $main_product['id'],
				'itemName' => $main_product['name'],
				'price'    => $price,
				'quantity' => $qty,
			);

			$total += $price * $qty;
		}

		if ( ! empty( $extra_products ) ) {
			foreach ( $extra_products as $p ) {
				$price = floatval( $p['amount'] );
				$qty   = intval( $p['quantity'] ?? 1 );

				$items[] = array(
					'itemId'   => $p['id'],
					'itemName' => $p['name'],
					'price'    => $price,
					'quantity' => $qty,
				);

				$total += $price * $qty;
			}
		}

		return array(
			'transactionId'    => $booking_key,
			'transactionTotal' => $total,
			'tax'              => 0,
			'shipping'         => 0,
			'currency'         => $currency,
			'orderDate'        => $dbhandler->get_value( 'BOOKING', 'booking_date', $booking_key, 'booking_key' ),
			'items'            => $items,
			'customerData'     => $customer,
		);
	}


	/**
	 * Fetch booking info for check in
	 *
	 * @author Darpan
	 */
	public function bm_get_order_detail_for_check_in() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$booking_id = sanitize_text_field( filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT ) );
		$dbhandler  = new BM_DBhandler();
		$booking    = $dbhandler->get_row( 'BOOKING', $booking_id, 'id' );

		if ( ! $booking ) {
			wp_send_json_error( esc_html__( 'Booking not found', 'service-booking' ) );
			return;
		}

		$customer_data = ( new BM_Request() )->get_customer_info_for_order( $booking->id );

		if ( ! $customer_data ) {
			wp_send_json_error( esc_html__( 'Customer not found', 'service-booking' ) );
			return;
		}

		$html  = '<div class="order-details">';
		$html  = '<div class="fx-modal-header">';
		$html .= '<h2>' . esc_html__( 'Order Details ', 'service-booking' ) . '#' . $booking->id . '</h2>';
		$html .= '</div>';
		$html .= '<table class="widefat fixed">';
		$html .= '<tr><th>' . esc_html__( 'Attendee', 'service-booking' ) . ':</th><td>' . $customer_data['billing_first_name'] ?? '' . '</td></tr>';
		$html .= '<tr><th>' . esc_html__( 'Email', 'service-booking' ) . ':</th><td>' . $customer_data['billing_email'] ?? '' . '</td></tr>';
		$html .= '<tr><th>' . esc_html__( 'Service', 'service-booking' ) . ':</th><td>' . $booking->service_name . '</td></tr>';
		$html .= '<tr><th>' . esc_html__( 'Booking Date', 'service-booking' ) . ':</th><td>' . $booking->booking_date . '</td></tr>';
		$html .= '<tr><th>' . esc_html__( 'Order Status', 'service-booking' ) . ':</th><td>' . ucfirst( $booking->order_status ) . '</td></tr>';

		$html .= '</table></div>';

		wp_send_json_success( $html );
	}//end bm_get_order_detail_for_check_in()


	/**
	 * Update checkin status
	 *
	 * @author Darpan
	 */
	public function bm_update_checkin_status_old() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$checkin_id = sanitize_text_field( filter_input( INPUT_POST, 'checkin_id', FILTER_VALIDATE_INT ) );
		$status     = sanitize_text_field( filter_input( INPUT_POST, 'new_status' ) );

		$dbhandler = new BM_DBhandler();
		$checkin   = $dbhandler->get_row( 'CHECKIN', $checkin_id, 'id' );

		if ( ! $checkin ) {
			wp_send_json_error( esc_html__( 'Checkin data not found', 'service-booking' ) );
			return;
		}

		$data = array( 'status' => $status );

		if ( $status === 'checked_in' ) {
			$data['checkin_time'] = current_time( 'mysql' );
		} else {
			$data['checkin_time'] = null;
		}

		$updated = $dbhandler->update_row( 'CHECKIN', 'id', $checkin_id, $data );
		wp_send_json_success();
	} //end bm_update_checkin_status()


	/**
	 * Update checkin status
	 *
	 * @author Darpan
	 */
	public function bm_update_checkin_status() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$checkin_id = filter_input( INPUT_POST, 'checkin_id', FILTER_VALIDATE_INT );
		$status     = sanitize_text_field( filter_input( INPUT_POST, 'new_status' ) );
		$booking_id = filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT );

		$dbhandler = new BM_DBhandler();
		$checkin   = $checkin_id ? $dbhandler->get_row( 'CHECKIN', $checkin_id, 'id' ) : null;

		$data = array(
			'status'     => $status,
			'updated_at' => current_time( 'mysql' ),
		);

		if ( $status === 'checked_in' ) {
			$data['checkin_time'] = current_time( 'mysql' );
		} else {
			$data['checkin_time'] = null;
		}

		if ( $checkin ) {
			$updated = $dbhandler->update_row( 'CHECKIN', 'id', $checkin_id, $data );
		} else {
			if ( ! $booking_id ) {
				wp_send_json_error( esc_html__( 'Booking ID required to create checkin record.', 'service-booking' ) );
				return;
			}

			$data['booking_id'] = $booking_id;
			$data['qr_token']   = $dbhandler->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );
			$data['qr_scanned'] = ( $status === 'checked_in' ) ? 1 : 0;
			$data['created_at'] = current_time( 'mysql' );

			$updated = $dbhandler->insert_row( 'CHECKIN', $data );
		}

		if ( ! $updated ) {
			wp_send_json_error( __( 'Unable to update or create checkin.', 'service-booking' ) );
		}

		wp_send_json_success();
	}



	/**
	 * Manual checkin
	 *
	 * @author Darpan
	 */
	public function bm_manual_checkin_check() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
		}

		$search_type = sanitize_text_field( filter_input( INPUT_POST, 'search_type' ) ?? '' );
		$raw_value   = $_POST['search_value'] ?? '';

		if ( is_array( $raw_value ) ) {
			$search_value = array_map( 'sanitize_text_field', $raw_value );
		} else {
			$search_value = sanitize_text_field( $raw_value );
		}

		if ( empty( $search_type ) || empty( $search_value ) ) {
			wp_send_json_error( __( 'Invalid search parameters', 'service-booking' ) );
		}

		$db = new BM_DBhandler();

		$bmrequests = new BM_Request();

		if ( $search_type === 'reference' ) {
			$booking_id = $db->get_value( 'BOOKING', 'id', $search_value, 'booking_key' );
			if ( ! $booking_id ) {
				wp_send_json_error( __( 'Booking not found', 'service-booking' ) );
			}

			$html = $bmrequests->bm_get_order_details_attachment( (int) $booking_id, false, false );
			if ( empty( $html ) ) {
				wp_send_json_error( __( 'Booking data not found', 'service-booking' ) );
			}

			wp_send_json_success( $html );
		}

		$joins = array(
			array(
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'c.id = b.customer_id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'CHECKIN',
				'alias' => 'ch',
				'on'    => 'ch.booking_id = b.id',
				'type'  => 'LEFT',
			),
		);

		if ( $search_type === 'email' ) {
			$where = array( 'c.customer_email' => array( '=' => $search_value ) );
		} elseif ( $search_type === 'service' ) {
			$where = array( 'b.service_id' => array( 'IN' => $search_value ) );
		} else {
			$where = array(
				'c.customer_name' => array(
					'LIKE' => '%' . $search_value,
				),
			);
		}

		$results = $db->get_results_with_join(
			array( 'BOOKING', 'b' ),
			'b.id, b.service_id, b.service_name, b.total_svc_slots as svc_participants, b.total_ext_svc_slots as ex_svc_participants, b.booking_key, c.customer_email, c.billing_details, ch.qr_scanned, ch.checkin_time',
			$joins,
			$where,
			'results'
		);

		if ( ! $results || count( $results ) === 0 ) {
			wp_send_json_error( __( 'No bookings found', 'service-booking' ) );
		}

		ob_start(); ?>
		<div class="bm-bookings-list">
			<table class="manual_checkin_records_table widefat striped">
				<thead>
					<tr>
						<th><input type="checkbox" id="bm-checkall"></th>
						<th><?php esc_html_e( 'Booking Key', 'service-booking' ); ?></th>
						<th><?php esc_html_e( 'Service Name', 'service-booking' ); ?></th>
						<?php if ( $search_type === 'email' ) : ?>
							<th><?php esc_html_e( 'Email', 'service-booking' ); ?></th>
						<?php else : ?>
							<th><?php esc_html_e( 'First Name', 'service-booking' ); ?></th>
							<th><?php esc_html_e( 'Last Name', 'service-booking' ); ?></th>
						<?php endif; ?>
						<th><?php esc_html_e( 'Service Participants', 'service-booking' ); ?></th>
						<th><?php esc_html_e( 'Extra Service Participants', 'service-booking' ); ?></th>
						<th><?php esc_html_e( 'Check-in Status', 'service-booking' ); ?></th>
						<th><?php esc_html_e( 'Check-in Date', 'service-booking' ); ?></th>
						<th><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $results as $row ) :
					$first_name = $last_name = '';
					if ( ! empty( $row->billing_details ) ) {
						$details = maybe_unserialize( $row->billing_details );
						if ( is_array( $details ) ) {
							$first_name = esc_html( $details['billing_first_name'] ?? '' );
							$last_name  = esc_html( $details['billing_last_name'] ?? '' );
						}
					}
					$status = ( $row->qr_scanned == 1 ) ? __( 'Checked-in', 'service-booking' ) : __( 'Pending', 'service-booking' );
					$date   = ! empty( $row->checkin_time ) ? $bmrequests->bm_convert_date_format( $row->checkin_time, 'Y-m-d H:i:s', 'd/m/y H:i' ) : '-';
					?>
					<tr>
						<td><input type="checkbox" class="bm-booking-select" value="<?php echo esc_attr( $row->id ); ?>"></td>
						<td><?php echo esc_html( $row->booking_key ); ?></td>
						<td><?php echo esc_html( $row->service_name ); ?></td>
						<?php if ( $search_type === 'email' ) : ?>
							<td><?php echo esc_html( $row->customer_email ); ?></td>
						<?php else : ?>
							<td><?php echo esc_html( $first_name ); ?></td>
							<td><?php echo esc_html( $last_name ); ?></td>
						<?php endif; ?>
						<td><?php echo esc_html( $row->svc_participants ); ?></td>
						<td><?php echo esc_html( $row->ex_svc_participants ); ?></td>
						<td><?php echo esc_html( $status ); ?></td>
						<td><?php echo esc_html( $date ); ?></td>
						<td>
							<div class="bm-view-details" data-id="<?php echo esc_attr( $row->id ); ?>">
								<i class="fa fa-eye"></i> <?php esc_html_e( 'View', 'service-booking' ); ?>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		$html = ob_get_clean();

		wp_send_json_success( $html );
	}//end bm_manual_checkin_check()


	/**
	 * View manual checkin order details
	 *
	 * @author Darpan
	 */
	public function bm_manual_checkin_view_details() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$booking_id = intval( filter_input( INPUT_POST, 'booking_id' ) ?? 0 );
		if ( ! $booking_id ) {
			wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
			return;
		}

		$html = ( new BM_Request() )->bm_get_order_details_attachment( (int) $booking_id, false, false );
		if ( empty( $html ) ) {
			wp_send_json_error( __( 'Booking data not found', 'service-booking' ) );
			return;
		}

		wp_send_json_success( $html );
	}//end bm_manual_checkin_view_details()


	/**
	 * Order refund hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_order_refund_callback( $order_id = 0, $refund_id = '' ) {
		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$process_id  = 0;
		$template_id = 0;

		if ( ! empty( $order_id ) && ! empty( $refund_id ) ) {
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
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_order_refund', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_order_refund', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_order_refund', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_order_refund_callback()


	/**
	 * Order refund hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_order_refund_callback( $order_id, $template_id, $process_id ) {
		$this->bm_flexibooking_mail_on_order_refund( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_order_refund_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_order_refund( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler        = new BM_DBhandler();
		$bm_mail          = new BM_Email();
		$bmrequests       = new BM_Request();
		$order            = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$mail_to_admin    = false;
		$mail_to_customer = false;
		$admin_mail_error = '';
		$cust_mail_error  = '';
		$source           = -1;
		$mail_type        = 'refund_order';
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
			$subject               = esc_html( 'Order refunded' );
			$message               = '<p>' . esc_html( 'Order amount refunded' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Ordine rimborsato' );
				$message = '<p>' . esc_html( 'Importo dell\'ordine rimborsato' ) . '</p>';
			}

			// Admin email
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale               = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_refund_order_template_id = $dbhandler->get_global_option_value( 'bm_refund_order_admin_template', 0 );

				if ( ! empty( $admin_refund_order_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_refund_order_template_id, (int) $order_id );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_refund_order_template_id, (int) $order_id );
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

			// Update mail_sent
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
	}//end bm_flexibooking_mail_on_order_refund()


	/**
	 * Cancel order hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_cancel_order_callback( $order_id = 0 ) {
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
					'type'   => 3,
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
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_cancel_order', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_cancel_order', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_cancel_order', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_cancel_order_callback()


	/**
	 * Cancel order hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_cancel_order_callback( $order_id, $template_id, $process_id ) {
		$this->bm_flexibooking_mail_on_cancel_order( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_cancel_order_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_cancel_order( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler        = new BM_DBhandler();
		$bm_mail          = new BM_Email();
		$bmrequests       = new BM_Request();
		$order            = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$mail_to_admin    = false;
		$mail_to_customer = false;
		$admin_mail_error = '';
		$cust_mail_error  = '';
		$source           = -1;
		$mail_type        = 'cancel_order';
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
						'type'   => 3,
					),
					'var'
				);
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'Order cancelled' );
			$message               = '<p>' . esc_html( 'An order has been cancelled' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Ordine annullato' );
				$message = '<p>' . esc_html( 'Un ordine è stato annullato' ) . '</p>';
			}

			// Admin email
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale               = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_cancel_order_template_id = $dbhandler->get_global_option_value( 'bm_cancel_order_admin_template', 0 );

				if ( ! empty( $admin_cancel_order_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_cancel_order_template_id, (int) $order_id );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_cancel_order_template_id, (int) $order_id );
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

			// Update mail_sent
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
	}//end bm_flexibooking_mail_on_cancel_order()


	/**
	 * Approve order hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_approved_order_callback( $order_id = 0 ) {
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
					'type'   => 4,
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
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_approved_order', array( $order_id, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_approved_order', array( $order_id, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_approved_order', array( $order_id, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_approved_order_callback()


	/**
	 * Approve order hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_approved_order_callback( $order_id, $template_id, $process_id ) {
		$this->bm_flexibooking_mail_on_approved_order( $order_id, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_approved_order_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_approved_order( $order_id = 0, $template_id = 0, $process_id = 0 ) {
		$dbhandler        = new BM_DBhandler();
		$bm_mail          = new BM_Email();
		$bmrequests       = new BM_Request();
		$order            = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$mail_to_admin    = false;
		$mail_to_customer = false;
		$admin_mail_error = '';
		$cust_mail_error  = '';
		$source           = -1;
		$mail_type        = 'approved_order';
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
						'type'   => 4,
					),
					'var'
				);
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'Order approved' );
			$message               = '<p>' . esc_html( 'Order has been approved' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Ordine approvato' );
				$message = '<p>' . esc_html( "L'ordine è stato approvato" ) . '</p>';
			}

			// Admin email
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale                 = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_approved_order_template_id = $dbhandler->get_global_option_value( 'bm_approved_order_admin_template', 0 );

				if ( ! empty( $admin_approved_order_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_approved_order_template_id, (int) $order_id );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_approved_order_template_id, (int) $order_id );
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

			// Update mail_sent
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
	}//end bm_flexibooking_mail_on_approved_order()


	/**
	 * Failed order hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_set_process_failed_order_callback( $order_key = '' ) {
		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$process_id  = 0;
		$template_id = 0;

		if ( ! empty( $order_key ) ) {
			$processes = $dbhandler->get_all_result(
				'EVENTNOTIFICATION',
				'*',
				array(
					'status' => 1,
					'type'   => 5,
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
						$schedule_mail = wp_schedule_single_event( time() + $delay, 'flexibooking_mail_failed_order', array( $order_key, $template_id, $process_id ), true );
					}

					if ( ! $scheduleable && $non_existing ) {
						$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_failed_order', array( $order_key, 0, 0 ), true );
					}
				}
			} else {
				$schedule_mail = wp_schedule_single_event( time(), 'flexibooking_mail_failed_order', array( $order_key, 0, 0 ), true );
			}
		}
	}//end bm_flexibooking_set_process_failed_order_callback()


	/**
	 * Failed order hook callbak
	 *
	 * @author Darpan
	 */
	public function bm_flexibooking_mail_on_failed_order_callback( $order_key, $template_id, $process_id ) {
		$this->bm_flexibooking_mail_on_failed_order( $order_key, $template_id, $process_id );
	}//end bm_flexibooking_mail_on_failed_order_callback()


	/**
	 * Send mail to shop admin and customer
	 *
	 * @author Darpan
	 */
	private function bm_flexibooking_mail_on_failed_order( $order_key = '', $template_id = 0, $process_id = 0 ) {
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
		$mail_type        = 'failed_order';
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
						'type'   => 9,
					),
					'var'
				);
			}

			$bm_admin_notification = $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 );
			$subject               = esc_html( 'Order failed' );
			$message               = '<p>' . esc_html( 'Order failed' ) . '</p>';

			if ( $language == 'it' ) {
				$subject = esc_html( 'Ordine fallito' );
				$message = '<p>' . esc_html( 'Ordine fallito' ) . '</p>';
			}

			// Admin email
			if ( $need_admin && $bm_admin_notification == 1 ) {
				$admin_old_locale               = $bmrequests->bm_switch_locale_by_booking_reference( '', $language );
				$admin_failed_order_template_id = $dbhandler->get_global_option_value( 'bm_failed_order_admin_template', 0 );

				if ( ! empty( $admin_failed_order_template_id ) ) {
					$admin_email_subject = $bm_mail->bm_get_template_email_subject( $admin_failed_order_template_id, (string) $order_key );
					$admin_email_message = $bm_mail->bm_get_template_email_content( $admin_failed_order_template_id, (string) $order_key );
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
	}//end bm_flexibooking_mail_on_failed_order()

	/**
	 * Fetch email details
	 *
	 * @author Darpan
	 */
	public function bm_show_mail_details() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests    = new BM_Request();
		$post          = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$email_details = '';

		if ( $post != false && $post != null ) {
			$email_id      = isset( $post['id'] ) ? $post['id'] : 0;
			$email_details = $bmrequests->bm_fetch_mail_details( $email_id );

			if ( empty( $email_details ) ) {
				$email_details = '<div class="textcenter">' . esc_html__( 'Nothing to show', 'service-booking' ) . '</div>';
			}
		}

		echo wp_kses( $email_details, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_show_mail_details()


	/**
	 * Fetch email body
	 *
	 * @author Darpan
	 */
	public function bm_show_email_body_old() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$email_body = '';

		if ( $post != false && $post != null ) {
			$email_id   = isset( $post['id'] ) ? $post['id'] : 0;
			$email_body = $bmrequests->bm_fetch_mail_body( $email_id );

			if ( empty( $email_body ) ) {
				$email_body = '<div class="textcenter">' . esc_html__( 'Nothing to show', 'service-booking' ) . '</div>';
			}
		}

		echo wp_kses( $email_body, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	} //end bm_show_email_body()


	/**
	 * Fetch email body
	 *
	 * @author Darpan
	 */
	public function bm_show_email_body() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$email_body = '';

		if ( $post != false && $post != null ) {
			$email_id   = isset( $post['id'] ) ? $post['id'] : 0;
			$email_body = $dbhandler->get_value( 'EMAILS', 'mail_body', $email_id, 'id' );

			if ( empty( $email_body ) ) {
				$email_body = '<div class="textcenter">' . esc_html__( 'Nothing to show', 'service-booking' ) . '</div>';
			}
		}

		echo wp_kses( $email_body, $bmrequests->bm_fetch_expanded_allowed_tags() );
		die;
	}//end bm_show_email_body()


	/**
	 * Open email body
	 *
	 * @author Darpan
	 */
	public function bm_open_email_body_old() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$bm_mail    = new BM_Email();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();

		if ( $post != false && $post != null ) {
			$email_id        = isset( $post['id'] ) ? $post['id'] : 0;
			$email_body      = $bmrequests->bm_fetch_mail_body( $email_id );
			$template_id     = $dbhandler->get_value( 'EMAILS', 'template_id', $email_id, 'id' );
			$module_type     = $dbhandler->get_value( 'EMAILS', 'module_type', $email_id, 'id' );
			$email_type      = $dbhandler->get_value( 'EMAILS', 'mail_type', $email_id, 'id' );
			$module_id       = $dbhandler->get_value( 'EMAILS', 'module_id', $email_id, 'id' );
			$to_email        = $dbhandler->get_value( 'EMAILS', 'mail_to', $email_id, 'id' );
			$mail_cc         = $dbhandler->get_value( 'EMAILS', 'mail_cc', $email_id, 'id' );
			$mail_bcc        = $dbhandler->get_value( 'EMAILS', 'mail_bcc', $email_id, 'id' );
			$mail_attahments = $dbhandler->get_value( 'EMAILS', 'mail_attachments', $email_id, 'id' );
			$attachments     = ! empty( $mail_attahments ) ? maybe_unserialize( $mail_attahments ) : array();

			$mail_subject = $bm_mail->bm_get_template_email_subject( $template_id );

			if ( $email_type == 'failed_order' ) {
				$module_key = $dbhandler->get_value( $module_type, 'booking_key', $module_id, 'id' );
				if ( empty( $to_email ) ) {
					$to_email = $bmrequests->bm_fetch_customer_email_from_booking_form_data( (string) $module_key );
				}
			} elseif ( empty( $to_email ) ) {
					$to_email = $bmrequests->bm_fetch_customer_email_from_booking_form_data( (int) $module_id );
			}

			if ( empty( $email_body ) ) {
				$email_body = '<div class="textcenter">' . esc_html__( 'Type here', 'service-booking' ) . '</div>';
			}

			$email_body = wp_kses( $email_body, $bmrequests->bm_fetch_expanded_allowed_tags() );

			if ( empty( $mail_subject ) ) {
				$mail_subject = esc_html__( 'Resending mail', 'service-booking' );
			}

			$data['to']          = $to_email;
			$data['cc']          = $mail_cc;
			$data['bcc']         = $mail_bcc;
			$data['attachments'] = ! empty( $attachments ) && ! empty( array_filter( $attachments, fn( $v ) => ! empty( array_filter( $v ) ) ) ) ? $attachments : array();
			$data['subject']     = $mail_subject;
			$data['body']        = $email_body;
		}

		echo wp_json_encode( $data );
		die;
	} //end bm_open_email_body()


	/**
	 * Open email body
	 *
	 * @author Darpan
	 */
	public function bm_open_email_body() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array();

		if ( $post != false && $post != null ) {
			$email_id        = isset( $post['id'] ) ? $post['id'] : 0;
			$module_type     = isset( $post['module_type'] ) ? $post['module_type'] : '';
			$email_record    = $dbhandler->get_row( 'EMAILS', $email_id );
			$email_body      = isset( $email_record->mail_body ) ? $email_record->mail_body : '';
			$mail_subject    = isset( $email_record->mail_sub ) ? $email_record->mail_sub : '';
			$to_email        = isset( $email_record->mail_to ) ? $email_record->mail_to : '';
			$mail_cc         = isset( $email_record->mail_cc ) ? $email_record->mail_cc : '';
			$mail_bcc        = isset( $email_record->mail_bcc ) ? $email_record->mail_bcc : '';
			$mail_attahments = isset( $email_record->mail_attachments ) ? $email_record->mail_attachments : '';
			$attachments     = ! empty( $mail_attahments ) ? maybe_unserialize( $mail_attahments ) : array();

			if ( empty( $mail_subject ) ) {
				$mail_subject = esc_html__( 'Resending mail', 'service-booking' );
			}

			if ( empty( $email_body ) ) {
				$email_body = '<div class="textcenter">' . esc_html__( 'Type here', 'service-booking' ) . '</div>';
			}

			if ( $module_type === 'checkin' ) {
				$order_id = $dbhandler->get_value( 'EMAILS', 'module_id', $email_id, 'id' );

				$ticket_file_path = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/new-mail/order-details/order-details-booking-' . $order_id . '.pdf';
				$ticket_file_url  = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/new-mail/order-details/order-details-booking-' . $order_id . '.pdf';

				$filepaths = array();
				$basenames = array();

				if ( file_exists( $ticket_file_path ) ) {
					$filepaths[] = $ticket_file_url;
					$basenames[] = __( 'Booking Ticket.pdf', 'service-booking' );
				}

				$attachments = array(
					'filepath' => $filepaths,
					'basename' => $basenames,
				);
			}

			$data['to']          = $to_email;
			$data['cc']          = $mail_cc;
			$data['bcc']         = $mail_bcc;
			$data['attachments'] = ! empty( $attachments ) && ! empty( array_filter( $attachments, fn( $v ) => ! empty( array_filter( $v ) ) ) ) ? $attachments : array();
			$data['subject']     = $mail_subject;
			$data['body']        = wp_kses( $email_body, $bmrequests->bm_fetch_expanded_allowed_tags() );
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_open_email_body()


	/**
	 * Resend email
	 *
	 * @author Darpan
	 */
	public function bm_resend_email() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler   = new BM_DBhandler();
		$bmrequests  = new BM_Request();
		$bm_mail     = new BM_Email();
		$post        = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data        = array( 'status' => false );
		$file_paths  = array();
		$attachments = array();

		if ( $post != false && $post != null ) {
			$to            = isset( $post['to'] ) ? $post['to'] : '';
			$cc            = isset( $post['cc'] ) ? $post['cc'] : '';
			$bcc           = isset( $post['bcc'] ) ? $post['bcc'] : '';
			$subject       = isset( $post['subject'] ) ? $post['subject'] : '';
			$template_body = isset( $post['body'] ) ? $post['body'] : '';
			$email_id      = isset( $post['email_id'] ) ? $post['email_id'] : 0;
			$module_id     = isset( $post['module_id'] ) ? $post['module_id'] : 0;
			$module_type   = isset( $post['module_type'] ) ? $post['module_type'] : '';
			$type          = isset( $post['type'] ) ? $post['type'] : '';
			$mail_type     = isset( $post['mail_type'] ) ? $post['mail_type'] : '';
			$template_id   = isset( $post['template_id'] ) ? $post['template_id'] : 0;
			$process_id    = isset( $post['process_id'] ) ? $post['process_id'] : 0;
			$guids         = isset( $post['guids'] ) ? $post['guids'] : array();
			$custom_files  = isset( $post['custom_files'] ) ? $post['custom_files'] : array();
			$mail_id       = 0;
			$source        = -1;
			$sent          = false;
			$copied_files  = array();
			$language      = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

			if ( ! empty( $to ) && ! empty( $subject ) && ! empty( $template_body ) && ! empty( $module_id ) && ! empty( $email_id ) ) {
				if ( $mail_type == 'failed_order' ) {
					$module_key    = $dbhandler->get_value( $module_type, 'booking_key', $module_id, 'id' );
					$template_body = $bm_mail->bm_filter_email_content( $template_body, (string) $module_key );
				} else {
					$template_body = $bm_mail->bm_filter_email_content( $template_body, (int) $module_id );
				}

				$from     = $bm_mail->bm_get_from_email();
				$headers  = "MIME-Version: 1.0\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8\r\n";
				$headers .= "From:$from\r\n";

				$mail_data = array(
					'module_type' => $module_type,
					'module_id'   => $module_id,
					'mail_type'   => $mail_type,
					'template_id' => $template_id,
					'process_id'  => $process_id,
					'mail_to'     => $to,
					'mail_cc'     => $cc,
					'mail_bcc'    => $bcc,
					'mail_sub'    => wp_kses_post( $subject ),
					'mail_body'   => ! empty( $template_body ) ? wp_kses_post( stripslashes( $template_body ) ) : '',
					'is_resent'   => 1,
					'mail_lang'   => $language,
					'status'      => 1,
				);

				if ( ! empty( $guids ) && is_array( $guids ) && array_filter( $guids ) ) {
					$directory = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/resend-mail/' . strtolower( $module_type ) . '-' . $module_id;

					if ( ! file_exists( $directory ) ) {
						mkdir( $directory, 0777, true );
					}

					foreach ( $guids as $guid ) {
						if ( ! empty( $guid ) ) {
							$file_path                 = get_attached_file( $guid );
							$filename                  = basename( $file_path );
							$file_paths[]              = $file_path;
							$attachments['guid'][]     = $guid;
							$attachments['filepath'][] = $file_path;
							$attachments['basename'][] = $filename;
							$destination               = $directory . '/' . $filename;

							if ( copy( $file_path, $destination ) ) {
								$copied_files[] = $destination;
							}
						}
					}
				}

				if ( $type == 'checkin' && ! empty( $custom_files ) && is_array( $custom_files ) ) {
					foreach ( $custom_files as $file_url ) {
						if ( filter_var( $file_url, FILTER_VALIDATE_URL ) ) {
							$file_path = str_replace( site_url() . '/', ABSPATH, $file_url );
							$file_path = wp_normalize_path( $file_path );

							if ( file_exists( $file_path ) ) {
								$file_paths[]              = $file_path;
								$attachments['filepath'][] = $file_path;
								$attachments['basename'][] = basename( $file_path );
								$attachments['guid'][]     = '';
							}
						}
					}
				}

				if ( ! empty( $cc ) ) {
					$headers .= "Cc:$cc\r\n";
				}

				if ( ! empty( $bcc ) ) {
					$headers .= "Bcc:$bcc\r\n";
				}

				ob_start();
				include plugin_dir_path( __DIR__ ) . 'admin/partials/booking-management-customer-email-layout.php';
				$template_body = ob_get_contents();
				ob_end_clean();

				$mail_data['mail_attachments'] = $attachments;

				$mail_data = $bmrequests->sanitize_request( $mail_data, 'EMAILS' );

				if ( $mail_data != false && $mail_data != null ) {
					$mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );
				}

				if ( ! empty( $mail_id ) ) {
					if ( ! empty( $file_paths ) ) {
						$sent = wp_mail( $to, $subject, $template_body, $headers, $file_paths );
					} else {
						$sent = wp_mail( $to, $subject, $template_body, $headers );
					}
				}

				if ( $sent ) {
					$data['status'] = true;
				} elseif ( ! empty( $copied_files ) ) {
					foreach ( $copied_files as $file ) {
						if ( file_exists( $file ) ) {
							unlink( $file );
						}
					}
				}
			}
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_resend_email()


	/**
	 * Add email attachment
	 *
	 * @author Darpan
	 */
	public function bm_add_email_attachment() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler          = new BM_DBhandler();
		$bmrequests         = new BM_Request();
		$attachments        = isset( $_FILES ) ? $_FILES : array();
		$email_id           = filter_input( INPUT_POST, 'email_id', FILTER_VALIDATE_INT );
		$existing_guids     = filter_input( INPUT_POST, 'existing_guids' );
		$data               = array( 'status' => 0 );
		$guids              = array();
		$existing_filenames = array();
		$allowFiletypes     = array( 'jpg', 'jpeg', 'png', 'gif', 'pdf', 'svg', 'zip', 'docx', 'doc', 'xlsx', 'ppt', 'csv' );

		if ( ! empty( $attachments ) && is_array( $attachments ) && ( $email_id != false && $email_id != null ) ) {
			if ( ! empty( $existing_guids ) ) {
				if ( ! is_array( $existing_guids ) ) {
					$existing_guids = explode( ',', $existing_guids );
				}

				if ( ! empty( $existing_guids ) && is_array( $existing_guids ) ) {
					foreach ( $existing_guids as $existing_guid ) {
						$existing_filenames[] = basename( get_attached_file( $existing_guid ) );
					}
				}
			}

			foreach ( $attachments as $attachment ) {
				$filename = isset( $attachment['name'] ) ? $attachment['name'] : '';

				if ( ! empty( $existing_filenames ) && is_array( $existing_filenames ) ) {
					if ( ! in_array( $filename, $existing_filenames ) ) {
						$guids[] = $bmrequests->bm_make_upload_and_get_attached_id( $attachment, $allowFiletypes );
					}
				} else {
					$guids[] = $bmrequests->bm_make_upload_and_get_attached_id( $attachment, $allowFiletypes );
				}
			}

			if ( ! empty( $guids ) && array_filter( $guids ) ) {
				$data['status'] = 1;
			}

			if ( ! empty( $existing_guids ) && is_array( $existing_guids ) ) {
				$guids = array_values( array_merge( $existing_guids, $guids ) );
			}
		}

		$data['guids'] = $guids;
		echo wp_json_encode( $data );
		die;
	}//end bm_add_email_attachment()


	/**
	 * Remove mail attachment
	 *
	 * @author Darpan
	 */
	public function bm_remove_email_attachment() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();
		$post      = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data      = array( 'status' => false );
		$guids     = array();

		if ( $post != false && $post != null ) {
			$guid     = isset( $post['id'] ) ? $post['id'] : -1;
			$email_id = isset( $post['email_id'] ) ? $post['email_id'] : 0;
			$guids    = isset( $post['guids'] ) ? $post['guids'] : array();

			if ( ( $guid != -1 ) && ! empty( $guids ) && is_array( $guids ) ) {
				$file_index = (int) array_search( $guid, $guids );
				if ( isset( $guids[ $file_index ] ) ) {
					unset( $guids[ $file_index ] );

					if ( ! empty( $guids ) ) {
						$guids = array_values( $guids );
					}

					$data['status'] = true;
				}
			}
		}

		$data['guids'] = $guids;
		echo wp_json_encode( $data );
		die;
	}//end bm_remove_email_attachment()


	/**
	 * Remove saved mail attachment
	 *
	 * @author Darpan
	 */
	public function bm_remove_temporary_email_attachment() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();
		$post      = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$status    = 0;
		$guids     = array();

		if ( $post != false && $post != null ) {
			$email_id    = isset( $post['email_id'] ) ? $post['email_id'] : 0;
			$attachments = maybe_unserialize( $dbhandler->get_value( 'EMAILS', 'mail_attachments', $email_id, 'id' ) );

			if ( ! empty( $attachments ) ) {
				$guids = isset( $attachments['guid'] ) ? $attachments['guid'] : array();
			}

			if ( ! empty( $guids ) ) {
				$deleted = delete_option( 'bm_resend_email_attachments-' . $email_id );

				if ( $deleted ) {
					$status = 1;
				}
			} else {
				$status = 2;
			}
		}

		echo wp_kses_post( $status );
		die;
	}//end bm_remove_temporary_email_attachment()


	/**
	 * Check admin password
	 *
	 * @author Darpan
	 */
	public function bm_check_admin_password() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler = new BM_DBhandler();
		$post      = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$status    = false;

		if ( $post != false && $post != null ) {
			$username = isset( $post['username'] ) ? $post['username'] : '';
			$password = isset( $post['password'] ) ? $post['password'] : '';

			if ( ! empty( $username ) && ! empty( $password ) ) {
				/**$user = wp_authenticate( $username, $password );
				$user = wp_authenticate_username_password( null, $username, $password );

				if ( is_a( $user, 'WP_User' ) ) {
					if ( in_array( 'administrator', (array) $user->roles ) ) {
						$status = true;
					}
				}*/

				$user = get_user_by( 'login', $username );

				if ( ! $user ) {
					$user = get_user_by( 'email', $username );
				}

				if ( $user ) {
					$result = wp_check_password( $password, $user->user_pass, $user->ID );

					if ( ! $result ) {
						$result = password_verify( $password, $user->user_pass );
					}

					if ( $result && is_a( $user, 'WP_User' ) ) {
						if ( in_array( 'administrator', (array) $user->roles ) ) {
							$status = true;
						}
					}
				}
			}
		}

		echo wp_kses_post( $status );
		die;
	}//end bm_check_admin_password()


	/**
	 * Fetch order export modal html
	 *
	 * @author Darpan
	 */
	public function bm_fetch_export_order_modal_options_html() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$html       = $bmrequests->bm_fetch_export_html_with_options();
		$data       = array( 'status' => true );

		if ( empty( $html ) ) {
			$data['status'] = false;
			$html           = '<div class="textcenter order_export_html_result">' . esc_html__( 'Something went wrong, try again', 'service-booking' ) . '</div>';
		}

		$data['html'] = $html;

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_export_order_modal_options_html()


	/**
	 * Fetch checkin export modal html
	 *
	 * @author Darpan
	 */
	public function bm_export_checkin_options_html() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$bmrequests = new BM_Request();
		$html       = $bmrequests->bm_fetch_export_html_with_options();
		$data       = array( 'status' => true );

		if ( empty( $html ) ) {
			$data['status'] = false;
			$html           = '<div class="textcenter order_export_html_result">' . esc_html__( 'Something went wrong, try again', 'service-booking' ) . '</div>';
		}

		$data['html'] = $html;

		echo wp_json_encode( $data );
		die;
	}//end bm_export_checkin_options_html()


	/**
	 * Fetch export records
	 *
	 * @author Darpan
	 */
	public function bm_fetch_export_order_records_as_per_type() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post ) {
			$type       = isset( $post['type'] ) ? $post['type'] : '';
			$start_page = isset( $post['start_page'] ) ? $post['start_page'] : 0;
			$end_page   = isset( $post['end_page'] ) ? $post['end_page'] : 0;
			$limit      = isset( $post['limit'] ) ? $post['limit'] : 0;
			$user_id    = get_current_user_id();

			$order_column = isset( $post['order_column'] ) ? $post['order_column'] : 'id';
			$order_dir    = isset( $post['order_dir'] ) ? $post['order_dir'] : 'DESC';

			$search_term    = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from   = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to     = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$order_from     = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$order_to       = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$order_source   = isset( $post['order_source'] ) ? $post['order_source'] : '';
			$order_status   = isset( $post['order_status'] ) ? $post['order_status'] : '';
			$payment_status = isset( $post['payment_status'] ) ? $post['payment_status'] : '';

			$search_params = array(
				'search_term'    => sanitize_text_field( $post['search_string'] ?? '' ),
				'service_from'   => $post['service_from'] ?? '',
				'service_to'     => $post['service_to'] ?? '',
				'order_from'     => $post['order_from'] ?? '',
				'order_to'       => $post['order_to'] ?? '',
				'order_source'   => $post['order_source'] ?? '',
				'order_status'   => $post['order_status'] ?? '',
				'payment_status' => $post['payment_status'] ?? '',
			);

			$failed_order_filter   = $dbhandler->get_global_option_value( "show_backend_order_page_failed_orders_$user_id", 0 );
			$archived_order_filter = $dbhandler->get_global_option_value( "show_backend_order_page_archived_orders_$user_id", 0 );

			if ( $failed_order_filter == 1 ) {
				$filtered_orders = $bmrequests->bm_fetch_all_failed_transactions_with_customer_data();
			} elseif ( $archived_order_filter == 1 ) {
				$filtered_orders = $bmrequests->bm_fetch_all_archived_orders_with_customer_data();
			} else {
				$filtered_orders = $bmrequests->bm_fetch_all_orders_with_customer_data();
			}

			if ( ! empty( $search_params['search_term'] ) ) {
				$search_term     = strtolower( $search_params['search_term'] );
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $search_term ) {
						$search_fields = array(
							'id',
							'service_name',
							'first_name',
							'last_name',
							'contact_no',
							'email_address',
							'total_cost',
							'ordered_from',
							'order_status',
							'transaction_status',
						);

						foreach ( $search_fields as $field ) {
							if ( isset( $order[ $field ] ) && stripos( strtolower( $order[ $field ] ), $search_term ) !== false ) {
								return true;
							}
						}
						return false;
					}
				);
			}

			if ( ! empty( $search_params['order_source'] ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $search_params ) {
						return strtolower( $order['ordered_from'] ) === strtolower( $search_params['order_source'] );
					}
				);
			}

			if ( ! empty( $search_params['order_status'] ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $search_params ) {
						return strtolower( $order['order_status'] ) === strtolower( $search_params['order_status'] );
					}
				);
			}

			if ( ! empty( $search_params['payment_status'] ) ) {
				$filtered_orders = array_filter(
					$filtered_orders,
					function ( $order ) use ( $search_params ) {
						return isset( $order['transaction_status'] ) &&
						strtolower( $order['transaction_status'] ) === strtolower( $search_params['payment_status'] );
					}
				);
			}

			if ( ! empty( $search_params['service_from'] ) && ! empty( $search_params['service_to'] ) ) {
				$from_date = DateTime::createFromFormat( 'd/m/y', $search_params['service_from'] );
				$to_date   = DateTime::createFromFormat( 'd/m/y', $search_params['service_to'] );

				if ( $from_date && $to_date ) {
					$filtered_orders = array_filter(
						$filtered_orders,
						function ( $order ) use ( $from_date, $to_date ) {
							$booking_date = DateTime::createFromFormat( 'd/m/y H:i', $order['booking_date'] );
							return $booking_date >= $from_date && $booking_date <= $to_date;
						}
					);
				}
			}

			if ( ! empty( $search_params['order_from'] ) && ! empty( $search_params['order_to'] ) ) {
				$from_date = DateTime::createFromFormat( 'd/m/y', $search_params['order_from'] );
				$to_date   = DateTime::createFromFormat( 'd/m/y', $search_params['order_to'] );

				if ( $from_date && $to_date ) {
					$filtered_orders = array_filter(
						$filtered_orders,
						function ( $order ) use ( $from_date, $to_date ) {
							$order_date = DateTime::createFromFormat( 'd/m/y H:i', $order['booking_created_at'] );
							return $order_date >= $from_date && $order_date <= $to_date;
						}
					);
				}
			}

			$filtered_orders = array_values( $filtered_orders );

			if ( ! empty( $order_column ) ) {
				$filtered_orders = $bmrequests->bm_sort_array_by_key( $filtered_orders, $order_column, $order_dir === 'desc' );
			}

			switch ( $type ) {
				case 'all':
					break;
				case 'current':
					$offset          = ( $start_page - 1 ) * $limit;
					$filtered_orders = array_slice( $filtered_orders, $offset, $limit );
					break;

				case 'range':
					$offset          = ( $start_page - 1 ) * $limit;
					$length          = ( $end_page - $start_page + 1 ) * $limit;
					$filtered_orders = array_slice( $filtered_orders, $offset, $length );
					break;

				default:
					$filtered_orders = array();
					break;
			}

			$exclude_columns = array( 'customer_data', 'order_attachments', 'actions' );
			$active_columns  = array_keys( $bmrequests->bm_fetch_active_columns( 'orders' ) );
			$column_headers  = array_values(
				array_diff(
					array_values( $bmrequests->bm_fetch_active_columns( 'orders' ) ),
					array( 'Customer Data', 'Actions', 'Order Attachments', 'Dati dei clienti', 'Azioni', "Allegati dell'ordine" )
				)
			);

			foreach ( $filtered_orders as $i => &$order ) {
				$order['serial_no'] = $i + 1;
			}

			if ( ! empty( $active_columns ) ) {
				$filtered_orders = array_map(
					function ( $order ) use ( $active_columns, $exclude_columns ) {
						return array_intersect_key( $order, array_flip( array_diff( $active_columns, $exclude_columns ) ) );
					},
					$filtered_orders
				);
			}

			$data = array(
				'status'      => true,
				'headers'     => $column_headers,
				'keys'        => array_values( array_diff( $active_columns, $exclude_columns ) ),
				'orders'      => $filtered_orders,
				'total_count' => count( $filtered_orders ),
			);
		}

		wp_send_json( $data );
	}//end bm_fetch_export_order_records_as_per_type()


	/**
	 * Export checkin records
	 *
	 * @author Darpan
	 */
	public function bm_fetch_export_checkin_records_as_per_type() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$post       = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data       = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type       = isset( $post['type'] ) ? $post['type'] : '';
			$start_page = isset( $post['start_page'] ) ? $post['start_page'] : 0;
			$end_page   = isset( $post['end_page'] ) ? $post['end_page'] : 0;
			$limit      = isset( $post['limit'] ) ? $post['limit'] : 0;

			$order_column = isset( $post['order_column'] ) ? $post['order_column'] : 'id';
			$order_dir    = isset( $post['order_dir'] ) ? $post['order_dir'] : 'DESC';

			$search_term  = isset( $post['search_string'] ) ? $post['search_string'] : '';
			$service_from = isset( $post['service_from'] ) ? $post['service_from'] : '';
			$service_to   = isset( $post['service_to'] ) ? $post['service_to'] : '';
			$checkin_from = isset( $post['order_from'] ) ? $post['order_from'] : '';
			$checkin_to   = isset( $post['order_to'] ) ? $post['order_to'] : '';
			$services     = isset( $post['services'] ) ? $post['services'] : array();

			$search_params = array(
				'search_term'  => sanitize_text_field( $post['search_string'] ?? '' ),
				'service_from' => $post['service_from'] ?? '',
				'service_to'   => $post['service_to'] ?? '',
				'checkin_from' => $post['order_from'] ?? '',
				'checkin_to'   => $post['order_to'] ?? '',
				'services'     => $post['services'] ?? array(),
			);

			$filtered_checkins = $bmrequests->bm_fetch_all_order_checkins();

			if ( ! empty( $search_params['search_term'] ) ) {
				$search_term       = strtolower( $search_params['search_term'] );
				$filtered_checkins = array_filter(
					$filtered_checkins,
					function ( $checkin ) use ( $search_term ) {
						$search_fields = array(
							'id',
							'booking_id',
							'checkin_id',
							'serial_no',
							'service_id',
							'service_name',
							'booking_date',
							'first_name',
							'last_name',
							'contact_no',
							'email_address',
							'total_cost',
							'checkin_time',
							'checkin_status',
							'email_id',
						);

						foreach ( $search_fields as $field ) {
							if ( isset( $checkin[ $field ] ) && stripos( strtolower( $checkin[ $field ] ), $search_term ) !== false ) {
								return true;
							}
						}
						return false;
					}
				);
			}

			if ( ! empty( $search_params['service_from'] ) && ! empty( $search_params['service_to'] ) ) {
				$from_date = DateTime::createFromFormat( 'd/m/y', $search_params['service_from'] );
				$to_date   = DateTime::createFromFormat( 'd/m/y', $search_params['service_to'] );

				if ( $from_date && $to_date ) {
					$filtered_checkins = array_filter(
						$filtered_checkins,
						function ( $checkin ) use ( $from_date, $to_date ) {
							$booking_date = DateTime::createFromFormat( 'd/m/y H:i', $checkin['booking_date'] );
							return $booking_date >= $from_date && $booking_date <= $to_date;
						}
					);
				}
			}

			if ( ! empty( $search_params['checkin_from'] ) && ! empty( $search_params['checkin_to'] ) ) {
				$from_date = DateTime::createFromFormat( 'd/m/y', $search_params['checkin_from'] );
				$to_date   = DateTime::createFromFormat( 'd/m/y', $search_params['checkin_to'] );

				if ( $from_date && $to_date ) {
					$filtered_checkins = array_filter(
						$filtered_checkins,
						function ( $checkin ) use ( $from_date, $to_date ) {
							$order_date = DateTime::createFromFormat( 'd/m/y H:i', $checkin['checkin_time'] );
							return $order_date >= $from_date && $order_date <= $to_date;
						}
					);
				}
			}

			if ( ! empty( $search_params['services'] ) ) {
				$filtered_checkins = array_filter(
					$filtered_checkins,
					function ( $checkin ) use ( $search_params ) {
						return in_array( $checkin['service_id'], $search_params['services'], true );
					}
				);
			}

			$filtered_checkins = array_values( $filtered_checkins );

			if ( ! empty( $order_column ) ) {
				$filtered_checkins = $bmrequests->bm_sort_array_by_key( $filtered_checkins, $order_column, $order_dir === 'desc' );
			}

			switch ( $type ) {
				case 'all':
					$offset = 0;
					$limit  = 0;
					break;
				case 'current':
					break;
				case 'range':
					if ( $start_page <= 0 || $end_page <= 0 || $start_page % 1 !== 0 || $end_page % 1 !== 0 || $start_page > $end_page || empty( $total_pages ) || $end_page > $total_pages ) {
						$filtered_checkins = array();
					} else {
						$offset = ( $start_page - 1 ) * $limit;
						$limit  = ( $end_page - $start_page + 1 ) * $limit;
					}
					break;
				default:
					$filtered_checkins = array();
					break;
			}

			$exclude_columns = array(
				'ticket_pdf',
				'actions',
			);

			$column_headers = array_values( array_diff( array_values( $bmrequests->bm_fetch_active_columns( 'checkin' ) ), array( 'Ticket PDF', 'Actions', 'PDF del biglietto', 'Azioni' ) ) );
			$active_columns = array_keys( $bmrequests->bm_fetch_active_columns( 'checkin' ) );

			if ( ! empty( $filtered_checkins ) ) {
				$filtered_checkins = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $filtered_checkins, $offset, $limit );
			}

			if ( ! empty( $filtered_checkins ) && ! empty( $active_columns ) ) {
				$filtered_checkins = $dbhandler->filter_existing_data_by_columns( $filtered_checkins, $active_columns, $exclude_columns, true );
				$data['status']    = true;
			}

			$data['headers'] = ! empty( $column_headers ) && $data['status'] == true ? $column_headers : array();
			$data['keys']    = ! empty( $active_columns ) && $data['status'] == true ? array_values( array_diff( $active_columns, $exclude_columns ) ) : array();
			$data['orders']  = ! empty( $filtered_checkins ) && $data['status'] == true ? $filtered_checkins : array();
		}

		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_export_checkin_records_as_per_type()


	/**
	 * Display service date in admin order details
	 *
	 * @author Darpan
	 */
	public function bm_display_service_date_in_admin( $order ) {
		$service_date = get_post_meta( $order->get_id(), '_flexi_service_date', true );
		$booked_slots = get_post_meta( $order->get_id(), '_flexi_booked_slots', true );

		if ( $service_date ) {
			echo '<div class="order_data_column flexi-service-date">';
			echo '<h3>' . __( 'Service Date', 'servuice-booking' ) . '</h3>';
			echo '<p>' . esc_html( $service_date ) . '</p>';
			echo '</div>';
		}

		if ( $booked_slots ) {
			echo '<div class="order_data_column flexi-booked-slots">';
			echo '<h3>' . __( 'Slot Timing:', 'servuice-booking' ) . '</h3>';
			echo '<p>' . esc_html( $booked_slots ) . '</p>';
			echo '</div>';
		}
	}//end bm_display_service_date_in_admin()


	/**
	 * Delete flexi order data if woocommerce order is deleted permanently
	 *
	 * @author Darpan
	 */
	public function bm_remove_flexi_order_if_woocommerce_order_is_permanently_deleted( $post_id ) {
		if ( get_post_type( $post_id ) === 'shop_order' ) {
			$flexi_booking_id  = get_post_meta( $post_id, '_flexi_booking_id', true );
			$flexi_customer_id = get_post_meta( $post_id, '_flexi_customer_id', true );

			if ( $flexi_booking_id > 0 || $flexi_customer_id > 0 ) {
				( new BM_Request() )->bm_remove_order_data( $flexi_booking_id, $flexi_customer_id );
			}
		}
	}//end bm_remove_flexi_order_if_woocommerce_order_is_permanently_deleted()


	/**
	 * Modify flexi order data as per woocommerce order trash
	 *
	 * @author Darpan
	 */
	public function bm_modify_flexi_plugin_order_on_woocommerce_order_trash( $post_id ) {
		if ( get_post_type( $post_id ) === 'shop_order' ) {
			$flexi_booking_id   = get_post_meta( $post_id, '_flexi_booking_id', true );
			$flexi_service_date = get_post_meta( $post_id, '_flexi_service_date', true );

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
		}
	}//end bm_modify_flexi_plugin_order_on_woocommerce_order_trash()


	/**
	 * Schedule woocommerce order status check as order untrash
	 *
	 * @author Darpan
	 */
	public function bm_schedule_woocommerce_order_status_check_on_untrash( $post_id ) {
		if ( get_post_type( $post_id ) === 'shop_order' ) {
			$flexi_booking_id   = get_post_meta( $post_id, '_flexi_booking_id', true );
			$flexi_service_date = get_post_meta( $post_id, '_flexi_service_date', true );

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
						wp_schedule_single_event( time() + 2, 'bm_update_flexi_order_as_woocommerce_order_is_restored', array( $post_id ) );
					}
				}
			}
		}
	} //end bm_schedule_order_status_check_on_untrash()


	/**
	 * Modify flexi order data as per woocommerce order untrash
	 *
	 * @author Darpan
	 */
	public function bm_modify_flexi_plugin_order_on_woocommerce_order_untrash( $post_id ) {
		if ( get_post_type( $post_id ) === 'shop_order' ) {
			$bmrequests = new BM_Request();

			$flexi_booking_id = get_post_meta( $post_id, '_flexi_booking_id', true );
			$order            = wc_get_order( $post_id );
			$restored_status  = $order->get_status();

			if ( in_array( $restored_status, array( 'pending', 'processing' ) ) ) {
				$bmrequests->bm_update_flexi_order_status_as_processing( $flexi_booking_id );
			} elseif ( $restored_status === 'completed' ) {
				$bmrequests->bm_update_flexi_order_status_as_completed( $flexi_booking_id );
			} elseif ( $restored_status === 'canceled' ) {
				$bmrequests->bm_cancel_flexi_order( $flexi_booking_id );
			} elseif ( $restored_status === 'refunded' ) {
				$bmrequests->bm_update_flexi_order_status_as_refunded( $flexi_booking_id );
			} elseif ( $restored_status === 'on-hold' ) {
				$bmrequests->bm_update_flexi_order_status_as_on_hold( $flexi_booking_id );
			}
		}
	}//end bm_modify_flexi_plugin_order_on_woocommerce_order_untrash()


	/**
	 * Hide flexi order itemmeta for woocommerce orders
	 *
	 * @author Darpan
	 */
	public function bm_hide_flexi_order_itemmeta( $arr ) {
		$arr[] = '_flexi_booking_key';
		$arr[] = '_flexi_checkout_key';
		return $arr;
	}//end bm_hide_flexi_order_itemmeta()


	/**
	 * Block status update for expired woocommerce orders
	 *
	 * @author Darpan
	 */
	public function bm_prevent_expired_woocommerce_order_updates( $post_id, $data ) {
		if ( get_post_type( $post_id ) === 'shop_order' ) {
			$is_expired = get_post_meta( $post_id, '_is_flexi_order_expired', true );

			if ( $is_expired ) {
				$flexi_booking_notice = __( 'This order is expired and cannot be updated.', 'service-booking' );
				update_option( 'flexi_booking_notice', $flexi_booking_notice, 'no' );

				$current_screen = get_current_screen();
				$redirect_url   = admin_url( "post.php?post={$post_id}&action=edit" );

				if ( $current_screen && $current_screen->id === 'edit-shop_order' ) {
					$redirect_url = admin_url( 'edit.php?post_type=shop_order' );
				}

				wp_safe_redirect( $redirect_url );
				exit;
			}
		}
	}//end bm_prevent_expired_woocommerce_order_updates()


	/**
	 * Order expiry notice
	 *
	 * @author Darpan
	 */
	public function bm_flexi_admin_notice() {
		$flexi_booking_notice = get_option( 'flexi_booking_notice', false );

		if ( $flexi_booking_notice ) {
			delete_option( 'flexi_booking_notice' );
			?>
			<div class="notice notice-error is-dismissible">
				<p><?php echo esc_html( $flexi_booking_notice ); ?></p>
			</div>
			<?php
		}
	}//end bm_flexi_admin_notice()


	/**
	 * Order expiry notice
	 *
	 * @author Darpan
	 */
	public function bm_disable_admin_notices_on_specific_pages() {
		$screen = get_current_screen();

		$pages_to_disable = array( 'toplevel_page_bm_home', 'flexibooking_page_bm_all_orders', 'admin_page_bm_add_order', 'flexibooking_page_bm_all_customers', 'admin_page_bm_add_customer', 'admin_page_bm_customer_profile', 'flexibooking_page_bm_all_services', 'admin_page_bm_add_service', 'flexibooking_page_bm_all_categories', 'admin_page_bm_add_category', 'flexibooking_page_bm_email_templates', 'admin_page_bm_add_template', 'flexibooking_page_bm_fields', 'flexibooking_page_bm_all_external_service_prices', 'flexibooking_page_bm_voucher_records', 'admin_page_bm_add_external_service_price', 'flexibooking_page_bm_all_notification_processes', 'admin_page_bm_add_notification_process', 'flexibooking_page_bm_email_records', 'flexibooking_page_bm_all_coupons', 'admin_page_bm_add_coupon', 'flexibooking_page_bm_global', 'admin_page_bm_global_general_settings', 'admin_page_bm_global_css_settings', 'admin_page_bm_global_timezone_country_settings', 'admin_page_bm_global_email_settings', 'admin_page_bm_global_payment_settings', 'admin_page_bm_svc_booking_settings', 'admin_page_bm_pagination_settings', 'admin_page_bm_upload_settings', 'admin_page_bm_global_language_settings', 'admin_page_bm_global_format_settings', 'admin_page_bm_global_integration_settings', 'admin_page_bm_global_coupon_settings', 'flexibooking_page_bm_service_booking_planner', 'flexibooking_page_bm_single_service_booking_planner', 'flexibooking_page_bm_check_ins' );

		if ( in_array( $screen->id, $pages_to_disable ) ) {
			remove_all_actions( 'admin_notices' );
		}
	}//end bm_disable_admin_notices_on_specific_pages()


	/**
	 * Check if existing email
	 *
	 * @author Darpan
	 */
	public function bm_check_if_exisiting_customer() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}

		$post = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		if ( empty( $post ) || empty( $post['main_email'] ) || empty( $post['billing_email'] ) || empty( $post['shipping_email'] ) || ! isset( $post['customer_id'] ) ) {
			wp_send_json_error( __( 'Invalid request data', 'service-booking' ) );
			return;
		}

		$main_email     = strtolower( $post['main_email'] );
		$billing_email  = strtolower( $post['billing_email'] );
		$shipping_email = strtolower( $post['shipping_email'] );
		$customer_id    = strtolower( $post['customer_id'] );

		$bmrequests = new BM_Request();

		$data = array(
			'main_email'     => $bmrequests->bm_is_exisiting_customer_email( $main_email, $customer_id ),
			'billing_email'  => $bmrequests->bm_is_exisiting_customer_email( $billing_email, $customer_id ),
			'shipping_email' => $bmrequests->bm_is_exisiting_customer_email( $shipping_email, $customer_id ),
		);

		wp_send_json_success( $data );
	}//end bm_check_if_exisiting_customer()


	/**
	 * Remove a coupon
	 *
	 *  @author Darpan
	 */
	public function bm_remove_coupon_function() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}
		$id        = filter_input( INPUT_POST, 'id', FILTER_VALIDATE_INT );
		$dbhandler = new BM_DBhandler();
		$data      = array( 'status' => false );
		if ( $id != false && $id != null ) {
			$coupon = $dbhandler->get_row( 'COUPON', $id );
			if ( ! empty( $coupon ) ) {
				$code                 = $coupon->coupon_code;
				$additional_condition = "AND FIND_IN_SET('$code', coupons)";
				$Bookings             = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'id', 'DESC', $additional_condition );
				if ( empty( $Bookings ) ) {
					$removed = $dbhandler->remove_row( 'COUPON', 'id', $id, '%d' );
					if ( $removed ) {
						$data['status'] = true;
					}
				} else {
					$update_data = array(
						'is_active' => '0',
					);
					$dbhandler->update_row( 'COUPON', 'id', $id, $update_data, '', '%d' );
					$data['status'] = true;
				}
			}
		}
		echo wp_json_encode( $data );
		die;
	}//end bm_remove_coupon_function()


	/**
	 * Fetch value for coupon type
	 *
	 * @author Darpan
	 */
	public function bm_fetch_value_for_coupon_type() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! isset( $nonce ) || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			die( esc_html__( 'Failed security check', 'service-booking' ) );
		}

		$coupon_validation = new BM_Coupon_validation();
		$post              = filter_input( INPUT_POST, 'post', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$data              = array( 'status' => false );

		if ( $post != false && $post != null ) {
			$type     = isset( $post['type'] ) ? $post['type'] : '';
			$response = $coupon_validation->bm_fetch_coupon_value_html( $type );
			if ( ! empty( $response ) ) {
				$data['status'] = true;
			}
			$data['value'] = $response;
		}
		echo wp_json_encode( $data );
		die;
	}//end bm_fetch_value_for_coupon_type()
}//end class
