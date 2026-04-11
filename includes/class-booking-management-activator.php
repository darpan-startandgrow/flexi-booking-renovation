<?php

/**
 * Fired during plugin activation
 *
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @author     Start and Grow <laravel6@startandgrow.in>
 */
class Booking_Management_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since 1.0.0
	 */
	public function activate() {
        $this->create_table();
	} //end activate()


	public function create_table() {
        global $wpdb;
		if ( version_compare( get_bloginfo( 'version' ), '6.1' ) < 0 ) {
			include_once ABSPATH . 'wp-includes/wp-db.php';
		} else {
			include_once ABSPATH . 'wp-includes/class-wpdb.php';
		}

		include_once ABSPATH . 'wp-admin/includes/upgrade.php';
		// Ensures proper charset support. Also limits support for WP v3.5+.
		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = $this->get_db_table_name( 'SERVICE' );
		$sql             = "CREATE TABLE IF NOT EXISTS $table_name (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`service_name` varchar(255) NOT NULL,
		`service_calendar_title` varchar(255) DEFAULT NULL,
		`service_category` int(11) DEFAULT NULL,
		`service_duration` float(24) DEFAULT NULL,
		`service_operation` float(24) DEFAULT NULL,
		`default_max_cap` int(100) NOT NULL DEFAULT 1,
		`default_saleswitch` float(24) DEFAULT NULL,
		`default_stopsales` float(24) DEFAULT NULL,
		`is_only_book_on_request` int(11) DEFAULT NULL,
		`is_service_front` int(11) NOT NULL DEFAULT 1,
		`show_stopsales_data` int(11) NOT NULL DEFAULT 1,
		`service_short_desc` text DEFAULT NULL,
		`service_desc` longtext DEFAULT NULL,
		`default_price` float(50) DEFAULT NULL,
		`external_price_module` int(11) DEFAULT NULL,
		`variable_svc_prices` longtext DEFAULT NULL,
		`variable_svc_price_modules` longtext DEFAULT NULL,
		`variable_saleswitch` longtext DEFAULT NULL,
		`variable_stopsales` longtext DEFAULT NULL,
		`variable_max_cap` longtext DEFAULT NULL,
		`variable_time_slots` longtext DEFAULT NULL,
		`service_unavailability` longtext DEFAULT NULL,
		`service_image_guid` int(11) DEFAULT 0,
		`is_linked_wc_product` int(11) DEFAULT 0,
		`wc_product` int(11) DEFAULT NULL,
		`service_status` int(11) NOT NULL DEFAULT 1,
		`service_options` longtext DEFAULT NULL,
		`service_settings` longtext DEFAULT NULL,
		`service_position` int(11) NOT NULL DEFAULT '0',
		`service_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`service_updated_at` datetime DEFAULT NULL,
		PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'TIME' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`service_id` int(11) DEFAULT NULL,
		`total_slots` int(11) DEFAULT NULL,
		`time_slots` longtext DEFAULT NULL,
		`time_options` longtext DEFAULT NULL,
		`time_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`time_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'GALLERY' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `module_type` varchar(255) DEFAULT NULL,
		`module_id` int(11) DEFAULT NULL,
		`image_guid` longtext DEFAULT NULL,
		`gallery_options` longtext DEFAULT NULL,
		`gallery_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`gallery_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'EXTRA' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`service_id` int(11) DEFAULT NULL,
		`is_global` int(11) DEFAULT 0,
		`exclude_from` longtext DEFAULT NULL,
        `extra_name` varchar(255) DEFAULT NULL,
		`extra_duration` float(24) DEFAULT NULL,
		`extra_operation` float(24) DEFAULT NULL,
		`extra_price` float(50) DEFAULT NULL,
		`extra_max_cap` int(100) NOT NULL DEFAULT 1,
		`is_extra_service_front` int(11) DEFAULT 1,
		`is_linked_wc_extrasvc` int(11) DEFAULT 0,
		`svcextra_wc_product` int(11) DEFAULT NULL,
		`extra_desc` longtext DEFAULT NULL,
		`extra_options` longtext DEFAULT NULL,
		`extras_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`extras_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'CATEGORY' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `cat_name` varchar(255) NOT NULL,
		`cat_in_front` int(11) DEFAULT NULL,
		`cat_status` int(11) NOT NULL DEFAULT 1,
		`cat_options` longtext DEFAULT NULL,
		`cat_position` int(11) NOT NULL DEFAULT '0',
		`cat_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`cat_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'FIELDS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`field_type` varchar(255) NOT NULL,
		`field_label` varchar(255) NOT NULL,
        `field_name` varchar(255) NOT NULL,
        `field_desc` longtext DEFAULT NULL,
        `field_options` longtext DEFAULT NULL,
        `is_required` int(11) DEFAULT NULL,
        `is_editable` int(11) DEFAULT NULL,
        `ordering` int(11) NOT NULL,
		`woocommerce_field` varchar(255) DEFAULT NULL,
        `field_key` varchar(255) NOT NULL,
		`field_position` int(11) NOT NULL DEFAULT '0',
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'BOOKING' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`wc_order_id` int(11) DEFAULT NULL,
		`service_id` int(11) DEFAULT NULL,
		`customer_id` int(11) DEFAULT NULL,
		`service_name` varchar(255) DEFAULT NULL,
		`booking_date` date DEFAULT NULL,
        `booking_slots` longtext DEFAULT NULL,
        `field_values` longtext DEFAULT NULL,
		`has_extra` int(11) NOT NULL DEFAULT '0',
		`is_frontend_booking` int(11) NOT NULL DEFAULT '0',
		`extra_svc_booked` longtext DEFAULT NULL,
		`total_svc_slots` int(11) DEFAULT NULL,
		`total_ext_svc_slots` int(11) DEFAULT NULL,
        `coupons` longtext DEFAULT NULL,
		`wc_coupons` longtext DEFAULT NULL,
		`vouchers` longtext DEFAULT NULL,
		`base_svc_price` float(50) DEFAULT NULL,
        `service_cost` float(50) DEFAULT NULL,
		`disount_amount` float(50) DEFAULT NULL,
		`extra_svc_cost` float(50) DEFAULT NULL,
		`subtotal` float(50) DEFAULT NULL,
		`total_cost` float(50) DEFAULT NULL,
		`order_status` varchar(100) DEFAULT NULL,
		`booking_country` varchar(100) DEFAULT NULL,
		`booking_key` varchar(255) DEFAULT NULL,
		`checkout_key` varchar(255) DEFAULT NULL,
        `newsletter` int(11) NOT NULL DEFAULT '0',
		`mail_sent` int(11) NOT NULL DEFAULT '0',
		`booking_type` varchar(100) DEFAULT NULL,
		`price_module_data` longtext DEFAULT NULL,
		`is_active` int(11) DEFAULT NULL,
		`booking_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`booking_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'SLOTCOUNT' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`service_id` int(11) DEFAULT NULL,
		`booking_id` int(11) DEFAULT NULL,
		`wc_order_id` int(11) DEFAULT NULL,
		`booking_date` date DEFAULT NULL,
		`slot_id` int(11) DEFAULT NULL,
		`is_variable` int(11) DEFAULT NULL,
		`slot_min_cap` int(50) DEFAULT NULL,
		`slot_max_cap` int(50) DEFAULT NULL,
		`slot_cap_left` int(50) DEFAULT NULL,
		`current_slots_booked` int(50) DEFAULT NULL,
		`slot_total_booked` int(50) DEFAULT NULL,
		`svc_total_booked_slots` int(50) DEFAULT NULL,
		`total_time_slots` int(50) DEFAULT NULL,
		`svc_total_cap` int(50) DEFAULT NULL,
		`svc_total_cap_left` int(50) DEFAULT NULL,
		`is_active` int(11) DEFAULT NULL,
		`slot_booked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`slot_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'EXTRASLOTCOUNT' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`extra_svc_id` int(11) DEFAULT NULL,
		`service_id` int(11) DEFAULT NULL,
		`booking_id` int(11) DEFAULT NULL,
		`wc_order_id` int(11) DEFAULT NULL,
		`booking_count_id` int(11) DEFAULT NULL,
		`booking_date` date DEFAULT NULL,
		`max_cap` int(50) DEFAULT NULL,
		`slots_booked` int(50) DEFAULT NULL,
		`cap_left` int(50) DEFAULT NULL,
		`is_active` int(11) DEFAULT NULL,
		`slot_booked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`slot_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'MANAGECOLUMNS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`language` varchar(100) DEFAULT NULL,
		`user_id` int(11) DEFAULT NULL,
		`default_columns` longtext DEFAULT NULL,
		`screen_options` longtext DEFAULT NULL,
		`is_admin` int(11) DEFAULT NULL,
		`view_type` varchar(255) DEFAULT NULL,
		`mc_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'SAVESEARCH' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`user_id` int(11) DEFAULT NULL,
		`search_data` longtext DEFAULT NULL,
		`is_admin` int(11) DEFAULT NULL,
		`module` varchar(255) DEFAULT NULL,
		`search_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'EMAIL_TMPL' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tmpl_name_en` varchar(600) DEFAULT NULL,
		`tmpl_name_it` varchar(600) DEFAULT NULL,
		`type` int(11) DEFAULT NULL,
        `email_subject_en` varchar(255) DEFAULT NULL,
		`email_subject_it` varchar(255) DEFAULT NULL,
        `email_body_en` longtext DEFAULT NULL,
		`email_body_it` longtext DEFAULT NULL,
		`status` int(11) NOT NULL DEFAULT 1,
		`template_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`template_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`))$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'CUSTOMERS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`stripe_id` varchar(600) DEFAULT NULL,
		`customer_name` varchar(255) DEFAULT NULL,
		`customer_email` varchar(255) DEFAULT NULL,
        `billing_details` longtext DEFAULT NULL,
		`shipping_details` longtext DEFAULT NULL,
		`shipping_same_as_billing` int(11) DEFAULT 0,
		`is_active` int(11) DEFAULT NULL,
		`customer_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`customer_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`))$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'TRANSACTIONS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `booking_id` int(11) DEFAULT NULL,
		`wc_order_id` int(11) DEFAULT NULL,
		`customer_id` int(11) DEFAULT NULL,
        `paid_amount` float(50) DEFAULT NULL,
        `paid_amount_currency` varchar(100) DEFAULT NULL,
		`transaction_id` varchar(600) DEFAULT NULL,
		`payment_method` varchar(100) DEFAULT NULL,
		`payment_status` varchar(255) DEFAULT NULL,
		`refund_id` varchar(600) DEFAULT NULL,
		`is_active` int(11) DEFAULT NULL,
		`transaction_created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`transaction_updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`))$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'BOOKING_ARCHIVE' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `original_id` int(11) DEFAULT 0,
        `booking_data` longtext DEFAULT NULL,
        `slot_data` longtext DEFAULT NULL,
        `extraslot_data` longtext DEFAULT NULL,
        `transaction_data` longtext DEFAULT NULL,
        `pdf_path` varchar(255) DEFAULT NULL,
        `deleted_at` datetime DEFAULT NULL DEFAULT CURRENT_TIMESTAMP,
        `deleted_by` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`))$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'FAILED_TRANSACTIONS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`customer_id` int(11) DEFAULT NULL,
        `transaction_id` varchar(600) DEFAULT NULL,
		`stripe_customer_id` varchar(600) DEFAULT NULL,
		`amount` float(50) DEFAULT NULL,
        `amount_currency` varchar(100) DEFAULT NULL,
		`booking_data` longtext DEFAULT NULL,
		`customer_data` longtext DEFAULT NULL,
		`gift_data` longtext DEFAULT NULL,
		`is_refunded` int(11) DEFAULT NULL,
		`refund_id` varchar(600) DEFAULT NULL,
		`payment_status` varchar(255) DEFAULT NULL,
		`refund_status` varchar(255) DEFAULT NULL,
		`booking_key` varchar(255) DEFAULT NULL,
		`checkout_key` varchar(255) DEFAULT NULL,
		`mail_sent` int(11) DEFAULT NULL,
		`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`))$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'EXTERNAL_SERVICE_PRICE_MODULE' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `module_name` varchar(255) DEFAULT NULL,
		`module_values` longtext DEFAULT NULL,
		`status` int(11) NOT NULL DEFAULT 1,
		`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'EMAILS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`module_type` varchar(255) DEFAULT NULL,
        `module_id` int(11) DEFAULT NULL,
		`mail_type` varchar(255) DEFAULT NULL,
		`template_id` int(11) DEFAULT NULL,
		`process_id` int(11) DEFAULT NULL,
		`mail_to` longtext DEFAULT NULL,
		`mail_cc` longtext DEFAULT NULL,
		`mail_bcc` longtext DEFAULT NULL,
		`mail_attachments` longtext DEFAULT NULL,
		`is_resent` int(11) DEFAULT NULL,
		`mail_sub` longtext DEFAULT NULL,
		`mail_body` longtext DEFAULT NULL,
		`mail_lang` varchar(100) DEFAULT NULL,
		`status` int(11) DEFAULT NULL,
		`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'EVENTNOTIFICATION' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`name` varchar(255) DEFAULT NULL,
        `type` int(11) DEFAULT NULL,
		`trigger_conditions` longtext DEFAULT NULL,
		`time_offset` longtext DEFAULT NULL,
		`template_id` int(11) DEFAULT NULL,
		`status` int(11) NOT NULL DEFAULT 1,
		`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'VOUCHERS' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`code` varchar(255) DEFAULT NULL,
		`booking_id` int(11) DEFAULT 0,
		`customer_id` int(11) DEFAULT 0,
		`transaction_id` int(11) DEFAULT 0,
		`recipient_data` longtext DEFAULT NULL,
		`is_gift` int(11) DEFAULT 0,
		`settings` longtext DEFAULT NULL,
		`is_redeemed` int(11) DEFAULT 0,
		`is_expired` int(11) DEFAULT 0,
		`status` int(11) DEFAULT 1,
		`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'CHECKIN' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`booking_id` int(11) NOT NULL,
			`qr_token` varchar(255) DEFAULT NULL,
			`qr_scanned` tinyint(1) DEFAULT 0,
			`status` varchar(255) DEFAULT NULL,
			`checkin_time` datetime DEFAULT NULL,
			`service_expired` tinyint(1) DEFAULT 0,
			`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` datetime DEFAULT NULL,
			PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'PDF_CUSTOMIZATION' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `booking_pdf_en` longtext DEFAULT NULL,
		`booking_pdf_it` longtext DEFAULT NULL,
		`voucher_pdf_en` longtext DEFAULT NULL,
		`voucher_pdf_it` longtext DEFAULT NULL,
		`customer_info_pdf_en` longtext DEFAULT NULL,
		`customer_info_pdf_it` longtext DEFAULT NULL,
		`pdf_logo_guid` int(11) DEFAULT 0,
		`created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$table_name = $this->get_db_table_name( 'COUPON' );
		$sql        = "CREATE TABLE IF NOT EXISTS $table_name (
        `id` int(11) NOT NULL AUTO_INCREMENT,
		`coupon_code` varchar(255) NOT NULL,
		`discount_type` varchar(255) NOT NULL, 
		`discount_amount` float(50) NOT NULL,
	    `coupon_description` longtext DEFAULT NULL,
		`wc_order_id` int(11) DEFAULT NULL,
		`booking_id` int(11) DEFAULT NULL,
		`usage_limit` float(50) DEFAULT NULL,
		`expiry_date` date DEFAULT NULL,
		`coupon_unavailability` longtext DEFAULT NULL, 
		`unavailable_slot` longtext DEFAULT NULL, 
		`min_spend` float(50) DEFAULT NULL,	
		`max_spend` float(50) DEFAULT NULL,	
		`is_individual_use` int(11) DEFAULT NULL,
		`per_person_used_once` int(11) DEFAULT NULL,
		`overall_used_once` int(11) DEFAULT NULL,
		`used_per_coupon_per_service` int(11) DEFAULT NULL,
		`is_exclude_service` int(11) DEFAULT NULL,
		`is_exclude_category` int(11) DEFAULT NULL,
		`is_email_exclude` int(11) DEFAULT NULL,
		`excluded_conditions` longtext DEFAULT NULL,
		`is_geographic_restrictions` int(11) DEFAULT NULL,
		`geographic_restriction` longtext DEFAULT NULL, 
		`is_active` int(11) DEFAULT NULL,
		`coupon_used_count` int(11) DEFAULT NULL,
		`coupon_used_data` longtext DEFAULT NULL, 
		`is_event_coupon` int(11) DEFAULT NULL,
		`start_date_val` longtext DEFAULT NULL, 
		`is_birthday_coupon` int(11) DEFAULT NULL,
		`is_service_included` int(11) DEFAULT NULL,
		`included_services` longtext DEFAULT NULL,
		`cannot_merged` int(11) DEFAULT NULL,
		`auto_apply` int(11) DEFAULT NULL,
		`coupon_image_guid` int(11) DEFAULT 0,
        PRIMARY KEY (`id`)
		)$charset_collate;";
		dbDelta( $sql );

		$this->add_error_column_to_emails();
		$this->add_error_column_to_failed_transactions();
		$this->create_default_form_fields();
		$this->create_default_email_templates();
		$this->create_default_pdf_contents();
		$this->add_default_options();
		$this->bm_create_custom_pages();
	} //end create_table()


	public function get_db_table_name( $identifier ) {
		global $wpdb;
		$plugin_prefix = $wpdb->prefix . 'sgbm_';

		switch ( $identifier ) {
			case 'CATEGORY':
				$table_name = $plugin_prefix . 'categories';
				break;
			case 'SERVICE':
				$table_name = $plugin_prefix . 'services';
				break;
			case 'GLOBAL':
				$table_name = $wpdb->prefix . 'options';
				break;
			case 'GALLERY':
				$table_name = $plugin_prefix . 'gallery';
				break;
			case 'EXTRA':
				$table_name = $plugin_prefix . 'service_extras';
				break;
			case 'FIELDS':
				$table_name = $plugin_prefix . 'fields';
				break;
			case 'TIME':
				$table_name = $plugin_prefix . 'time_slots';
				break;
			case 'BOOKING':
				$table_name = $plugin_prefix . 'booking';
				break;
			case 'SLOTCOUNT':
				$table_name = $plugin_prefix . 'booking_slot_count';
				break;
			case 'EXTRASLOTCOUNT':
				$table_name = $plugin_prefix . 'extra_svc_booking_count';
				break;
			case 'EMAIL_TMPL':
				$table_name = $plugin_prefix . 'email_template';
				break;
			case 'MANAGECOLUMNS':
				$table_name = $plugin_prefix . 'manage_columns';
				break;
			case 'SAVESEARCH':
				$table_name = $plugin_prefix . 'saved_search';
				break;
			case 'CUSTOMERS':
				$table_name = $plugin_prefix . 'customers';
				break;
			case 'TRANSACTIONS':
				$table_name = $plugin_prefix . 'transactions';
				break;
			case 'BOOKING_ARCHIVE':
				$table_name = $plugin_prefix . 'booking_archive';
				break;
			case 'FAILED_TRANSACTIONS':
				$table_name = $plugin_prefix . 'failed_transactions';
				break;
			case 'EXTERNAL_SERVICE_PRICE_MODULE':
				$table_name = $plugin_prefix . 'external_svc_price_module';
				break;
			case 'EMAILS':
				$table_name = $plugin_prefix . 'email_records';
				break;
			case 'EVENTNOTIFICATION':
				$table_name = $plugin_prefix . 'notification_events';
				break;
			case 'VOUCHERS':
				$table_name = $plugin_prefix . 'vouchers';
				break;
			case 'CHECKIN':
				$table_name = $plugin_prefix . 'checkin';
				break;
			case 'PDF_CUSTOMIZATION':
				$table_name = $plugin_prefix . 'pdf_content_customization';
				break;
			case 'COUPON':
				$table_name = $plugin_prefix . 'coupon';
				break;
			default:
				$classname = "BM_Helper_$identifier";
				if ( class_exists( $classname ) ) {
					$externalclass = new $classname();
					$table_name    = $externalclass->get_db_table_name( $identifier );
				} else {
					return false;
				}
		} //end switch

		return $table_name;
	} //end get_db_table_name()


	public function get_db_table_unique_field_name( $identifier ) {
		switch ( $identifier ) {
			case 'CATEGORY':
				$unique_field_name = 'id';
				break;
			case 'SERVICE':
				$unique_field_name = 'id';
				break;
			case 'GLOBAL':
				$unique_field_name = 'option_id';
				break;
			case 'GALLERY':
				$unique_field_name = 'module_id';
				break;
			case 'EXTRA':
				$unique_field_name = 'id';
				break;
			case 'FIELDS':
				$unique_field_name = 'id';
				break;
			case 'TIME':
				$unique_field_name = 'service_id';
				break;
			case 'BOOKING':
				$unique_field_name = 'service_id';
				break;
			case 'SLOTCOUNT':
				$unique_field_name = 'service_id';
				break;
			case 'EXTRASLOTCOUNT':
				$unique_field_name = 'extra_svc_id';
				break;
			case 'EMAIL_TMPL':
				$unique_field_name = 'id';
				break;
			case 'MANAGECOLUMNS':
				$unique_field_name = 'id';
				break;
			case 'SAVESEARCH':
				$unique_field_name = 'id';
				break;
			case 'CUSTOMERS':
				$unique_field_name = 'id';
				break;
			case 'TRANSACTIONS':
				$unique_field_name = 'id';
				break;
			case 'BOOKING_ARCHIVE':
				$unique_field_name = 'id';
				break;
			case 'FAILED_TRANSACTIONS':
				$unique_field_name = 'id';
				break;
			case 'EXTERNAL_SERVICE_PRICE_MODULE':
				$unique_field_name = 'id';
				break;
			case 'EMAILS':
				$unique_field_name = 'id';
				break;
			case 'EVENTNOTIFICATION':
				$unique_field_name = 'id';
				break;
			case 'VOUCHERS':
				$unique_field_name = 'id';
				break;
			case 'CHECKIN':
				$unique_field_name = 'id';
				break;
			case 'COUPON':
				$unique_field_name = 'id';
				break;
			case 'PDF_CUSTOMIZATION':
				$unique_field_name = 'id';
				break;
			default:
				$classname = "BM_Helper_$identifier";
				if ( class_exists( $classname ) ) {
					$externalclass     = new $classname();
					$unique_field_name = $externalclass->get_db_table_unique_field_name( $identifier );
				} else {
					return false;
				}
		} //end switch

		return $unique_field_name;
	} //end get_db_table_unique_field_name()


	public function get_db_table_field_type( $identifier, $field ) {
        $functionname = 'get_field_format_type_' . $identifier;
		if ( method_exists( 'Booking_Management_Activator', $functionname ) ) {
			$format = $this->$functionname( $field );
		} else {
			$classname = "BM_Helper_$identifier";
			if ( class_exists( $classname ) ) {
				$externalclass = new $classname();
				$format        = $externalclass->get_db_table_field_type( $identifier, $field );
			} else {
				return false;
			}
		}

		return $format;
	} //end get_db_table_field_type()


	public function get_field_format_type_SERVICE( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'service_name':
				$format = '%s';
				break;
			case 'service_calendar_title':
				$format = '%s';
				break;
			case 'service_category':
				$format = '%d';
				break;
			case 'service_duration':
				$format = '%f';
				break;
			case 'service_operation':
				$format = '%f';
				break;
			case 'default_max_cap':
				$format = '%d';
				break;
			case 'default_saleswitch':
				$format = '%f';
				break;
			case 'default_stopsales':
				$format = '%f';
				break;
			case 'external_price_module':
				$format = '%d';
				break;
			case 'is_only_book_on_request':
				$format = '%d';
				break;
			case 'is_linked_wc_product':
				$format = '%d';
				break;
			case 'wc_product':
				$format = '%d';
				break;
			case 'is_service_front':
				$format = '%d';
				break;
			case 'show_stopsales_data':
				$format = '%d';
				break;
			case 'service_desc':
				$format = '%s';
				break;
			case 'service_short_desc':
				$format = '%s';
				break;
			case 'default_price':
				$format = '%f';
				break;
			case 'variable_svc_prices':
				$format = '%s';
				break;
			case 'variable_svc_price_modules':
				$format = '%s';
				break;
			case 'variable_saleswitch':
				$format = '%s';
				break;
			case 'variable_stopsales':
				$format = '%s';
				break;
			case 'variable_max_cap':
				$format = '%s';
				break;
			case 'variable_time_slots':
				$format = '%s';
				break;
			case 'service_image_guid':
				$format = '%d';
				break;
			case 'service_status':
				$format = '%d';
				break;
			case 'service_options':
				$format = '%s';
				break;
			case 'service_settings':
				$format = '%s';
				break;
			case 'service_unavailability':
				$format = '%s';
				break;
			case 'service_position':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_SERVICE()


	public function get_field_format_type_CATEGORY( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'cat_name':
				$format = '%s';
				break;
			case 'cat_in_front':
				$format = '%d';
				break;
			case 'cat_status':
				$format = '%d';
				break;
			case 'cat_options':
				$format = '%s';
				break;
			case 'cat_position':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_CATEGORY()


	public function get_field_format_type_TIME( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'service_id':
				$format = '%d';
				break;
			case 'total_slots':
				$format = '%d';
				break;
			case 'time_slots':
				$format = '%s';
				break;
			case 'time_options':
				$format = '%s';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_TIME()


	public function get_field_format_type_GALLERY( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'module_type':
				$format = '%s';
				break;
			case 'module_id':
				$format = '%d';
				break;
			case 'image_guid':
				$format = '%d';
				break;
			case 'gallery_options':
				$format = '%s';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_GALLERY()


	public function get_field_format_type_EXTRA( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'extra_name':
				$format = '%s';
				break;
			case 'is_global':
				$format = '%d';
				break;
			case 'exclude_from':
				$format = '%s';
				break;
			case 'extra_duration':
				$format = '%f';
				break;
			case 'extra_operation':
				$format = '%f';
				break;
			case 'extra_price':
				$format = '%f';
				break;
			case 'extra_max_cap':
				$format = '%d';
				break;
			case 'is_extra_service_front':
				$format = '%d';
				break;
			case 'is_linked_wc_extrasvc':
				$format = '%d';
				break;
			case 'svcextra_wc_product':
				$format = '%d';
				break;
			case 'extra_options':
				$format = '%s';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_EXTRA()


	public function get_field_format_type_FIELDS( $field ) {
        switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'field_type':
				$format = '%s';
				break;
			case 'field_label':
				$format = '%s';
				break;
			case 'field_name':
				$format = '%s';
				break;
			case 'field_desc':
				$format = '%s';
				break;
			case 'field_options':
				$format = '%s';
				break;
			case 'is_required':
				$format = '%d';
				break;
			case 'is_editable':
				$format = '%d';
				break;
			case 'ordering':
				$format = '%d';
				break;
			case 'field_key':
				$format = '%s';
				break;
			case 'woocommerce_field':
				$format = '%s';
				break;
			case 'field_position':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_FIELDS()


	public function get_field_format_type_BOOKING( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'wc_order_id':
				$format = '%d';
				break;
			case 'service_id':
				$format = '%d';
				break;
			case 'service_name':
				$format = '%s';
				break;
			case 'booking_date':
				$format = '%s';
				break;
			case 'booking_slots':
				$format = '%s';
				break;
			case 'field_values':
				$format = '%s';
				break;
			case 'has_extra':
				$format = '%d';
				break;
			case 'is_frontend_booking':
				$format = '%d';
				break;
			case 'extra_svc_booked':
				$format = '%s';
				break;
			case 'total_svc_slots':
				$format = '%d';
				break;
			case 'total_ext_svc_slots':
				$format = '%d';
				break;
			case 'coupons':
				$format = '%s';
				break;
			case 'wc_coupons':
				$format = '%s';
				break;
			case 'base_svc_price':
				$format = '%f';
				break;
			case 'service_cost':
				$format = '%f';
				break;
			case 'disount_amount':
				$format = '%f';
				break;
			case 'subtotal':
				$format = '%f';
				break;
			case 'total_cost':
				$format = '%f';
				break;
			case 'extra_svc_cost':
				$format = '%f';
				break;
			case 'order_status':
				$format = '%s';
				break;
			case 'booking_country':
				$format = '%s';
				break;
			case 'booking_key':
				$format = '%s';
				break;
			case 'vouchers':
				$format = '%s';
				break;
			case 'checkout_key':
				$format = '%s';
				break;
			case 'newsletter':
				$format = '%d';
				break;
			case 'mail_sent':
				$format = '%d';
				break;
			case 'booking_type':
				$format = '%s';
				break;
			case 'price_module_data':
				$format = '%s';
				break;
			case 'is_active':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_BOOKING()


	public function get_field_format_type_SLOTCOUNT( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'service_id':
				$format = '%d';
				break;
			case 'booking_id':
				$format = '%d';
				break;
			case 'wc_order_id':
				$format = '%d';
				break;
			case 'slot_id':
				$format = '%d';
				break;
			case 'is_variable':
				$format = '%d';
				break;
			case 'slot_min_cap':
				$format = '%d';
				break;
			case 'slot_max_cap':
				$format = '%d';
				break;
			case 'slot_cap_left':
				$format = '%d';
				break;
			case 'current_slots_booked':
				$format = '%d';
				break;
			case 'slot_total_booked':
				$format = '%d';
				break;
			case 'svc_total_booked_slots':
				$format = '%d';
				break;
			case 'total_time_slots':
				$format = '%d';
				break;
			case 'svc_total_cap':
				$format = '%d';
				break;
			case 'svc_total_cap_left':
				$format = '%d';
				break;
			case 'booking_date':
				$format = '%s';
				break;
			case 'slot_booked_at':
				$format = '%s';
				break;
			case 'is_active':
				$format = '%d';
				break;
			default:
				$format = '%d';
		} //end switch

		return $format;
	} //end get_field_format_type_SLOTCOUNT()


	public function get_field_format_type_EXTRASLOTCOUNT( $field ) {
        switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'extra_svc_id':
				$format = '%d';
				break;
			case 'service_id':
				$format = '%d';
				break;
			case 'booking_id':
				$format = '%d';
				break;
			case 'wc_order_id':
				$format = '%d';
				break;
			case 'bokking_count_id':
				$format = '%d';
				break;
			case 'max_cap':
				$format = '%d';
				break;
			case 'slots_booked':
				$format = '%d';
				break;
			case 'cap_left':
				$format = '%d';
				break;
			case 'booking_date':
				$format = '%s';
				break;
			case 'slot_booked_at':
				$format = '%s';
				break;
			case 'is_active':
				$format = '%d';
				break;
			default:
				$format = '%d';
		} //end switch

		return $format;
	} //end get_field_format_type_EXTRASLOTCOUNT()


	public function get_field_format_type_EMAIL_TMPL( $field ) {
        switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'tmpl_name_en':
				$format = '%s';
				break;
			case 'tmpl_name_it':
				$format = '%s';
				break;
			case 'type':
				$format = '%d';
				break;
			case 'email_subject_en':
				$format = '%s';
				break;
			case 'email_subject_it':
				$format = '%s';
				break;
			case 'email_body_en':
				$format = '%s';
				break;
			case 'email_body_it':
				$format = '%s';
				break;
			case 'status':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_EMAIL_TMPL()


	public function get_field_format_type_MANAGECOLUMNS( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'language':
				$format = '%s';
				break;
			case 'user_id':
				$format = '%d';
				break;
			case 'default_columns':
				$format = '%s';
				break;
			case 'screen_options':
				$format = '%s';
				break;
			case 'is_admin':
				$format = '%d';
				break;
			case 'view_type':
				$format = '%s';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_MANAGECOLUMNS()


	public function get_field_format_type_SAVESEARCH( $field ) {
        switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'user_id':
				$format = '%d';
				break;
			case 'search_data':
				$format = '%s';
				break;
			case 'is_admin':
				$format = '%d';
				break;
			case 'module':
				$format = '%s';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_SAVESEARCH()


	public function get_field_format_type_CUSTOMERS( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'stripe_id':
				$format = '%s';
				break;
			case 'customer_name':
				$format = '%s';
				break;
			case 'customer_email':
				$format = '%s';
				break;
			case 'billing_details':
				$format = '%s';
				break;
			case 'shipping_details':
				$format = '%s';
				break;
			case 'shipping_same_as_billing':
				$format = '%d';
				break;
			case 'is_active':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_CUSTOMERS()


	public function get_field_format_type_TRANSACTIONS( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'booking_id':
				$format = '%d';
				break;
			case 'wc_order_id':
				$format = '%d';
				break;
			case 'customer_id':
				$format = '%d';
				break;
			case 'paid_amount':
				$format = '%f';
				break;
			case 'paid_amount_currency':
				$format = '%s';
				break;
			case 'transaction_id':
				$format = '%s';
				break;
			case 'payment_method':
				$format = '%s';
				break;
			case 'payment_status':
				$format = '%s';
				break;
			case 'refund_id':
				$format = '%s';
				break;
			case 'is_active':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_TRANSACTIONS()


	public function get_field_format_type_BOOKING_ARCHIVE( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'original_id':
				$format = '%d';
				break;
			case 'booking_data':
				$format = '%S';
				break;
			case 'slot_data':
				$format = '%s';
				break;
			case 'extraslot_data':
				$format = '%s';
				break;
			case 'transaction_data':
				$format = '%s';
				break;
			case 'pdf_path':
				$format = '%s';
				break;
			case 'deleted_by':
				$format = '%D';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_BOOKING_ARCHIVE()


	public function get_field_format_type_FAILED_TRANSACTIONS( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'transaction_id':
				$format = '%s';
				break;
			case 'stripe_customer_id':
				$format = '%s';
				break;
			case 'amount':
				$format = '%f';
				break;
			case 'amount_currency':
				$format = '%s';
				break;
			case 'booking_data':
				$format = '%s';
				break;
			case 'customer_data':
				$format = '%s';
				break;
			case 'gift_data':
				$format = '%s';
				break;
			case 'is_refunded':
				$format = '%d';
				break;
			case 'refund_id':
				$format = '%s';
				break;
			case 'payment_status':
				$format = '%s';
				break;
			case 'refund_status':
				$format = '%s';
				break;
			case 'error_message':
				$format = '%s';
				break;
			case 'booking_key':
				$format = '%s';
				break;
			case 'checkout_key':
				$format = '%s';
				break;
			case 'mail_sent':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_FAILED_TRANSACTIONS()

	public function get_field_format_type_EXTERNAL_SERVICE_PRICE_MODULE( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'module_name':
				$format = '%s';
				break;
			case 'module_values':
				$format = '%s';
				break;
			case 'status':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_EXTERNAL_SERVICE_PRICE_MODULE()

	public function get_field_format_type_EMAILS( $field ) {
        switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'module_type':
				$format = '%s';
				break;
			case 'module_id':
				$format = '%d';
				break;
			case 'mail_type':
				$format = '%s';
				break;
			case 'template_id':
				$format = '%d';
				break;
			case 'process_id':
				$format = '%d';
				break;
			case 'mail_to':
				$format = '%s';
				break;
			case 'mail_cc':
				$format = '%s';
				break;
			case 'mail_bcc':
				$format = '%s';
				break;
			case 'mail_attachments':
				$format = '%s';
				break;
			case 'is_resent':
				$format = '%d';
				break;
			case 'mail_sub':
				$format = '%s';
				break;
			case 'mail_body':
				$format = '%s';
				break;
			case 'mail_lang':
				$format = '%s';
				break;
			case 'status':
				$format = '%d';
				break;
			case 'error_message':
				$format = '%s';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_EMAILS()

	public function get_field_format_type_EVENTNOTIFICATION( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'name':
				$format = '%s';
				break;
			case 'type':
				$format = '%d';
				break;
			case 'trigger_conditions':
				$format = '%s';
				break;
			case 'time_offset':
				$format = '%s';
				break;
			case 'template_id':
				$format = '%d';
				break;
			case 'status':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_EVENTNOTIFICATION()

	public function get_field_format_type_VOUCHERS( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'code':
				$format = '%s';
				break;
			case 'booking_id':
				$format = '%d';
				break;
			case 'customer_id':
				$format = '%d';
				break;
			case 'transaction_id':
				$format = '%d';
				break;
			case 'recipient_data':
				$format = '%s';
				break;
			case 'is_gift':
				$format = '%d';
				break;
			case 'settings':
				$format = '%s';
				break;
			case 'is_expired':
				$format = '%d';
				break;
			case 'is_redeemed':
				$format = '%d';
				break;
			case 'status':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_VOUCHERS()

	public function get_field_format_type_CHECKIN( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'qr_token':
				$format = '%s';
				break;
			case 'qr_scanned':
				$format = '%d';
				break;
			case 'status':
				$format = '%s';
				break;
			case 'checkin_time':
				$format = '%s';
				break;
			case 'service_expired':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_CHECKIN()

	public function get_field_format_type_PDF_CUSTOMIZATION( $field ) {
		switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'pdf_logo_guid':
				$format = '%d';
				break;
			default:
				$format = '%s';
		} //end switch

		return $format;
	} //end get_field_format_type_EVENTNOTIFICATION()

	public function get_field_format_type_COUPON( $field ) {
        switch ( $field ) {
			case 'id':
				$format = '%d';
				break;
			case 'coupon_code':
				$format = '%s';
				break;
			case 'discount_type':
				$format = '%s';
				break;
			case 'discount_amount':
				$format = '%f';
				break;
			case 'coupon_description':
				$format = '%s';
				break;
			case 'wc_order_id':
				$format = '%d';
				break;
			case 'booking_id':
				$format = '%d';
				break;
			case 'usage_limit':
				$format = '%d';
				break;
			case 'expiry_date':
				$format = '%d';
				break;
			case 'coupon_unavailability':
				$format = '%s';
				break;
			case 'min_spend':
				$format = '%s';
				break;
			case 'max_spend':
				$format = '%d';
				break;
			case 'is_individual_use':
				$format = '%d';
				break;
			case 'per_person_used_once':
				$format = '%d';
				break;
			case 'overall_used_once':
				$format = '%d';
				break;
			case 'used_per_coupon_per_service':
				$format = '%d';
				break;
			case 'is_email_exclude':
				$format = '%d';
				break;
			case 'excluded_conditions':
				$format = '%s';
				break;
			case 'is_service_included':
				$format = '%d';
				break;
			case 'included_services':
				$format = '%s';
				break;
			case 'is_geographic_restrictions':
				$format = '%d';
				break;
			case 'geographic_restriction':
				$format = '%s';
				break;
			case 'is_active':
				$format = '%d';
				break;
			case 'coupon_used_count':
				$format = '%d';
				break;
			case 'coupon_used_data':
				$format = '%s';
				break;
			case 'is_event_coupon':
				$format = '%d';
				break;
			case 'start_date_val':
				$format = '%s';
				break;
			case 'is_birthday_coupon':
				$format = '%d';
				break;
			case 'cannot_merged':
				$format = '%d';
				break;
			case 'auto_apply':
				$format = '%d';
				break;
			case 'coupon_image_guid':
				$format = '%d';
				break;
			default:
				$format = '%s';
		}

		return $format;
	} //end get_field_format_type_COUPON()

	private function add_error_column_to_emails() {
		global $wpdb;
		$table_name = $this->get_db_table_name( 'EMAILS' );
		$row        = $wpdb->get_results( "SHOW COLUMNS FROM $table_name LIKE 'error_message'" );
		if ( empty( $row ) ) {
			$wpdb->query( "ALTER TABLE $table_name ADD `error_message` text NULL AFTER `mail_lang`" );
		}
	}

	private function add_error_column_to_failed_transactions() {
		global $wpdb;
		$table_name = $this->get_db_table_name( 'FAILED_TRANSACTIONS' );
		$row        = $wpdb->get_results( "SHOW COLUMNS FROM $table_name LIKE 'error_message'" );
		if ( empty( $row ) ) {
			$wpdb->query( "ALTER TABLE $table_name ADD `error_message` text NULL AFTER `refund_status`" );
		}
	}

	public function create_default_form_fields() {
		$dbhandler  = new BM_DBhandler();
		$bmrequest  = new BM_Request();
		$is_created = $dbhandler->get_global_option_value( 'bm_booking_form_fields_created', '0' );
		$resutls    = $dbhandler->get_all_result( 'FIELDS', '*', 1, 'results' );
		if ( $is_created == '0' || empty( $resutls ) ) {
			$bmrequest->bm_create_default_booking_form_fields();
		}
	} //end create_default_form_fields()


	public function create_default_email_templates() {
		$dbhandler  = new BM_DBhandler();
		$bmrequest  = new BM_Request();
		$is_created = $dbhandler->get_global_option_value( 'bm_email_templates_created', '0' );
		$resutls    = $dbhandler->get_all_result( 'EMAIL_TMPL', '*', 1, 'results' );
		if ( $is_created == '0' || empty( $resutls ) ) {
			$bmrequest->bm_create_default_email_templates();
		}
	} //end create_default_email_templates()


	public function create_default_pdf_contents() {
		$dbhandler  = new BM_DBhandler();
		$bmrequest  = new BM_Request();
		$is_created = $dbhandler->get_global_option_value( 'bm_pdf_contents_created', '0' );
		$resutls    = $dbhandler->get_all_result( 'PDF_CUSTOMIZATION', '*', 1, 'results' );
		if ( $is_created == '0' || empty( $resutls ) ) {
			$bmrequest->bm_create_default_pdf_contents();
		}
	} //end create_default_pdf_contents()


	public function add_default_options() {
		$bmrequests    = new BM_Request();
		$primary_color = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
		$contrast      = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';

         add_option( 'bm_show_frontend_progress_bar', '1' );
		add_option( 'bm_show_frontend_grid_list_button', '1' );
		add_option( 'bm_frontend_view_type', 'grid' );
		add_option( 'bm_booking_country', 'IT' );
		add_option( 'bm_booking_time_zone', 'Asia/Kolkata' );
		add_option( 'bm_show_frontend_service_booking_date_field', '1' );
		add_option( 'bm_show_frontend_service_search', '1' );
		add_option( 'bm_show_frontend_category_search', '1' );
		add_option( 'bm_show_frontend_service_sorting', '1' );
		add_option( 'bm_show_frontend_service_image', '1' );
		add_option( 'bm_show_frontend_service_desc_read_more_button', '1' );
		add_option( 'bm_show_frontend_service_price', '1' );
		add_option( 'bm_show_frontend_service_duration', '1' );
		add_option( 'bm_show_frontend_service_description', '1' );
		add_option( 'bm_show_frontend_edit_button_in_booking_form', '1' );
		add_option( 'bm_show_frontend_pagination', '1' );
		add_option( 'bm_show_service_to_time_slot', '1' );
		add_option( 'bm_show_service_limit_box', '1' );
		add_option( 'bm_frontend_service_title_color', '#000000' );
		add_option( 'bm_frontend_book_button_color', $primary_color );
		add_option( 'bm_shop_admin_notification', '1' );
		add_option( 'bm_new_order_admin_template', '5' );
		add_option( 'bm_new_request_admin_template', '15' );
		add_option( 'bm_cancel_order_admin_template', '6' );
		add_option( 'bm_refund_order_admin_template', '7' );
		add_option( 'bm_approved_order_admin_template', '8' );
		add_option( 'bm_failed_order_admin_template', '10' );
		add_option( 'bm_voucher_redeem_admin_template', '16' );
		add_option( 'bm_enable_smtp', '0' );
		add_option( 'bm_attach_customer_data_with_admin_email_body', '1' );
		add_option( 'bm_booking_currency', 'EUR' );
		add_option( 'bm_currency_position', 'before' );
		add_option( 'bm_payment_session_time', '3' );
		add_option( 'bm_allowed_stopsales', '24' );
		add_option( 'bm_allowed_saleswitch', '24' );
		add_option( 'bm_book_on_request_expiry', '7' );
		add_option( 'bm_voucher_expiry', '30' );
		add_option( 'bm_date_field_label_font', '20' );
		add_option( 'bm_category_search_label_font', '20' );
		add_option( 'bm_category_checkbox_label_font', '14' );
		add_option( 'bm_service_title_font', '20' );
		add_option( 'bm_service_shrt_desc_font', '14' );
		add_option( 'bm_service_price_txt_font', '16' );
		add_option( 'bm_frontend_service_price_text_color', '#000000' );
		add_option( 'bm_frontend_book_button_txt_color', $contrast );
		add_option( 'bm_orders_per_page', '10' );
		add_option( 'bm_services_per_page', '10' );
		add_option( 'bm_categories_per_page', '10' );
		add_option( 'bm_templates_per_page', '10' );
		add_option( 'bm_price_modules_per_page', '10' );
		add_option( 'bm_notification_processes_per_page', '10' );
		add_option( 'bm_email_records_per_page', '10' );
		add_option( 'bm_voucher_records_per_page', '10' );
		add_option( 'bm_coupon_per_page', '10' );
		add_option( 'bm_minimum_image_size', '100' );
		add_option( 'bm_maximum_image_size', '2097152000' );
		add_option( 'bm_minimum_image_width', '100' );
		add_option( 'bm_maximum_image_width', '2000000' );
		add_option( 'bm_minimum_image_height', '100' );
		add_option( 'bm_maximum_image_height', '1500000' );
		add_option( 'bm_image_quality', '90' );
		add_option(
			'bm_flexibooking_languages',
			array(
				'en' => 'English',
				'it' => 'Italian',
			)
		);
		add_option( 'bm_flexi_current_language', 'en' );
		add_option( 'bm_svc_shrt_desc_char_limit', '66' );
		add_option( 'bm_svc_overall_start_time', '09:30' );
		add_option( 'bm_flexi_current_locale', 'en_US' );
		add_option( 'bm_show_lng_swtchr_in_admin_bar', '1' );
		add_option( 'bm_show_lng_swtchr_in_footer', '0' );
		add_option( 'bm_flexi_service_time_slot_format', '24' );
		add_option( 'bm_flexi_service_price_format', 'de_DE' );
		add_option( 'bm_front_svc_search_shortcode_cat_ids', array( 0 ) );
		add_option( 'bm_enable_woocommerce_checkout', '0' );
		add_option( 'bm_woocommerce_only_checkout', '0' );
		add_option( 'bm_auto_apply_limit', '4' );
	} //end add_default_options()


	public function bm_create_custom_pages() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		//checkout page
		$checkout_page_slug = 'flexibooking-checkout';
		$checkout_page      = array(
			'post_type'    => 'page',
			'post_title'   => 'Flexibooking Checkout',
			'post_content' => '[sgbm_flexibooking_checkout_page]',
			'post_status'  => 'publish',
			'post_name'    => $checkout_page_slug,
		);

		if ( !get_page_by_path( $checkout_page_slug, OBJECT, 'page' ) ) { // Check If Does Page Not Exit
			$checkout_page_id = wp_insert_post( $checkout_page );
			update_option( 'bm_checkout_page_id', $checkout_page_id );
		}

		//voucher redeem page
		$voucher_redeem_page_slug = 'flexibooking-voucher-redeem';
		$voucher_redeem_page      = array(
			'post_type'    => 'page',
			'post_title'   => 'Flexibooking Voucher Redeem',
			'post_content' => '[sgbm_flexibooking_voucher_redeem_page]',
			'post_status'  => 'publish',
			'post_name'    => $voucher_redeem_page_slug,
		);

		if ( !get_page_by_path( $voucher_redeem_page_slug, OBJECT, 'page' ) ) { // Check If Does Page Not Exit
			$voucher_redeem_page_id = wp_insert_post( $voucher_redeem_page );
			update_option( 'bm_voucher_redeem_page_id', $voucher_redeem_page_id );
		}

		$qr_scanner_page_slug = 'flexibooking-qr-scanner';
		$qr_scanner_page      = array(
			'post_type'    => 'page',
			'post_title'   => 'Flexibooking qr Scanner',
			'post_content' => '[sgbm_qr_scanner]',
			'post_status'  => 'publish',
			'post_name'    => $qr_scanner_page_slug,
		);

		if ( !get_page_by_path( $qr_scanner_page_slug, OBJECT, 'page' ) ) { // Check If Does Page Not Exit
			$qr_scanner_page_id = wp_insert_post( $qr_scanner_page );
			update_option( 'bm_qr_scanner_page_id', $qr_scanner_page_id );
		}
	} //end bm_create_custom_pages()


}//end class
