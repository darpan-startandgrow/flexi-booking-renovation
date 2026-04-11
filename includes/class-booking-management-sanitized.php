<?php
class BM_Sanitizer {

	public function get_sanitized_fields( $identifier, $field, $value ) {
		$sanitize_method = 'get_sanitized_' . strtolower( $identifier ) . '_field';

		if ( method_exists( $this, $sanitize_method ) ) {
			$sanitized_value = $this->$sanitize_method( $field, $value );
		} else {
			$classname = "bm_Helper_$identifier";
		}

		if ( isset( $classname ) && class_exists( $classname ) ) {
			$externalclass   = new $classname();
			$sanitized_value = $externalclass->get_sanitized_fields( $identifier, $field, $value );
		}

		return $sanitized_value;
	} //end get_sanitized_fields()


	public function get_sanitized_category_field( $field, $value ) {
        switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'cat_name':
				$value = sanitize_text_field( $value );
				break;
			case 'cat_in_front':
				$value = sanitize_text_field( $value );
				break;
			case 'cat_options':
				$value = $value;
				break;
			case 'cat_status':
				$value = sanitize_text_field( $value );
				break;
			case 'cat_position':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_category_field()


	public function get_sanitized_gallery_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'module_type':
				$value = sanitize_text_field( $value );
				break;
			case 'module_id':
				$value = sanitize_text_field( $value );
				break;
			case 'image_guid':
				$value = sanitize_text_field( $value );
				break;
			case 'gallery_options':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_gallery_field()


	public function get_sanitized_time_field( $field, $value ) {
        switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_id':
				$value = sanitize_text_field( $value );
				break;
			case 'total_slots':
				$value = sanitize_text_field( $value );
				break;
			case 'time_slots':
				$value = $value;
				break;
			case 'time_options':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_time_field()


	public function get_sanitized_extra_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_id':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_name':
				$value = sanitize_text_field( $value );
				break;
			case 'is_global':
				$value = sanitize_text_field( $value );
				break;
			case 'exclude_from':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_duration':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_operation':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_price':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_max_cap':
				$value = sanitize_text_field( $value );
				break;
			case 'is_linked_wc_extrasvc':
				$value = sanitize_text_field( $value );
				break;
			case 'svcextra_wc_product':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_desc':
				$value = wp_kses_post( $value );
				break;
			case 'extra_options':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_extra_field()


	public function get_sanitized_service_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_name':
				$value = sanitize_text_field( $value );
				break;
			case 'service_calendar_title':
				$value = sanitize_text_field( $value );
				break;
			case 'service_category':
				$value = sanitize_text_field( $value );
				break;
			case 'service_duration':
				$value = sanitize_text_field( $value );
				break;
			case 'service_operation':
				$value = sanitize_text_field( $value );
				break;
			case 'default_saleswitch':
				$value = sanitize_text_field( $value );
				break;
			case 'default_stopsales':
				$value = sanitize_text_field( $value );
				break;
			case 'external_price_module':
				$value = sanitize_text_field( $value );
				break;
			case 'is_only_book_on_request':
				$value = sanitize_text_field( $value );
				break;
			case 'is_linked_wc_product':
				$value = sanitize_text_field( $value );
				break;
			case 'wc_product':
				$value = sanitize_text_field( $value );
				break;
			case 'default_max_cap':
				$value = sanitize_text_field( $value );
				break;
			case 'is_service_front':
				$value = sanitize_text_field( $value );
				break;
			case 'show_stopsales_data':
				$value = sanitize_text_field( $value );
				break;
			case 'service_desc':
				$value = wp_kses_post( $value );
				break;
			case 'service_short_desc':
				$value = wp_kses_post( $value );
				break;
			case 'default_price':
				$value = sanitize_text_field( $value );
				break;
			case 'variable_svc_prices':
				$value = $value;
				break;
			case 'variable_svc_price_modules':
				$value = $value;
				break;
			case 'variable_saleswitch':
				$value = $value;
				break;
			case 'variable_stopsales':
				$value = $value;
				break;
			case 'variable_max_cap':
				$value = $value;
				break;
			case 'variable_time_slots':
				$value = $value;
				break;
			case 'service_image_guid':
				$value = sanitize_text_field( $value );
				break;
			case 'service_options':
				$value = $value;
				break;
			case 'service_settings':
				$value = $value;
				break;
			case 'service_unavailability':
				$value = $value;
				break;
			case 'service_position':
				$value = sanitize_text_field( $value );
				break;
			case 'service_status':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_service_field()


	public function get_sanitized_booking_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'wc_order_id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_name':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_date':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_slot':
				$value = $value;
				break;
			case 'field_values':
				$value = $value;
				break;
			case 'has_extra':
				$value = sanitize_text_field( $value );
				break;
			case 'is_frontend_booking':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_svc_booked':
				$value = $value;
				break;
			case 'total_svc_slots':
				$value = sanitize_text_field( $value );
				break;
			case 'total_ext_svc_slots':
				$value = sanitize_text_field( $value );
				break;
			case 'coupons':
				$value = $value;
				break;
			case 'wc_coupons':
				$value = $value;
				break;
			case 'vouchers':
				$value = $value;
				break;
			case 'base_svc_price':
				$value = sanitize_text_field( $value );
				break;
			case 'service_cost':
				$value = sanitize_text_field( $value );
				break;
			case 'disount_amount':
				$value = sanitize_text_field( $value );
				break;
			case 'subtotal':
				$value = sanitize_text_field( $value );
				break;
			case 'total_cost':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_svc_cost':
				$value = sanitize_text_field( $value );
				break;
			case 'order_status':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_country':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_key':
				$value = sanitize_text_field( $value );
				break;
			case 'checkout_key':
				$value = sanitize_text_field( $value );
				break;
			case 'newsletter':
				$value = sanitize_text_field( $value );
				break;
			case 'mail_sent':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_type':
				$value = sanitize_text_field( $value );
				break;
			case 'price_module_data':
				$value = $value;
				break;
			case 'is_active':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_booking_field()


	public function get_sanitized_slotcount_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_id':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_id':
				$value = sanitize_text_field( $value );
				break;
			case 'wc_order_id':
				$value = sanitize_text_field( $value );
				break;
			case 'slot_id':
				$value = sanitize_text_field( $value );
				break;
			case 'is_variable':
				$value = sanitize_text_field( $value );
				break;
			case 'slot_min_cap':
				$value = sanitize_text_field( $value );
				break;
			case 'slot_max_cap':
				$value = sanitize_text_field( $value );
				break;
			case 'slot_cap_left':
				$value = sanitize_text_field( $value );
				break;
			case 'current_slots_booked':
				$value = sanitize_text_field( $value );
				break;
			case 'slot_total_booked':
				$value = sanitize_text_field( $value );
				break;
			case 'svc_total_booked_slots':
				$value = sanitize_text_field( $value );
				break;
			case 'total_time_slots':
				$value = sanitize_text_field( $value );
				break;
			case 'svc_total_cap':
				$value = sanitize_text_field( $value );
				break;
			case 'svc_total_cap_left':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_date':
				$value = sanitize_text_field( $value );
				break;
			case 'is_active':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_slotcount_field()


	public function get_sanitized_managecolumns_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'language':
				$value = sanitize_text_field( $value );
				break;
			case 'user_id':
				$value = sanitize_text_field( $value );
				break;
			case 'default_columns':
				$value = $value;
				break;
			case 'screen_options':
				$value = $value;
				break;
			case 'is_admin':
				$value = sanitize_text_field( $value );
				break;
			case 'view_type':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_managecolumns_field()


	public function get_sanitized_savesearch_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'user_id':
				$value = sanitize_text_field( $value );
				break;
			case 'search_data':
				$value = $value;
				break;
			case 'is_admin':
				$value = sanitize_text_field( $value );
				break;
			case 'module':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_savesearch_field()


	public function get_sanitized_extraslotcount_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_id':
				$value = sanitize_text_field( $value );
				break;
			case 'extra_svc_id':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_id':
				$value = sanitize_text_field( $value );
				break;
			case 'wc_order_id':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_count_id':
				$value = sanitize_text_field( $value );
				break;
			case 'max_cap':
				$value = sanitize_text_field( $value );
				break;
			case 'slots_booked':
				$value = sanitize_text_field( $value );
				break;
			case 'cap_left':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_date':
				$value = sanitize_text_field( $value );
				break;
			case 'is_active':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_extraslotcount_field()


	public function get_sanitized_frontend_field( $type, $value ) {
		switch ( $type ) {
			case 'email':
				$value = sanitize_email( $value );
				break;
			case 'first_name':
				$value = sanitize_text_field( $value );
				break;
			case 'last_name':
				$value = sanitize_text_field( $value );
				break;
			case 'description':
				$value = wp_kses_post( wp_rel_nofollow( $value ) );
				break;
			case 'gender':
				$value = sanitize_text_field( $value );
				break;
			case 'phone':
				$value = filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
				break;
			case 'DatePicker':
				$value = filter_var( $value, FILTER_SANITIZE_NUMBER_INT );
				break;
			case 'gender':
				$value = sanitize_text_field( $value );
				break;
			case 'facebook':
				$value = esc_url( $value );
				break;

			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_frontend_field()


	public function get_sanitized_global_field( $field, $value ) {
		switch ( $field ) {
			case 'bm_show_frontend_progress_bar':
				$value = sanitize_textarea_field( $value );
				break;
			case 'bm_frontend_view_type':
				$value = sanitize_textarea_field( $value );
				break;
			case 'bm_show_frontend_service_search':
				$value = sanitize_textarea_field( $value );
				break;
			case 'bm_show_frontend_category_search':
				$value = sanitize_textarea_field( $value );
				break;
			case 'bm_show_frontend_service_image':
				$value = sanitize_textarea_field( $value );
				break;
			case 'bm_show_frontend_service_desc_read_more_button':
				$value = sanitize_textarea_field( $value );
				break;
			case 'bm_show_frontend_service_price':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_service_duration':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_service_description':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_edit_button_in_booking_form':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_pagination':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_service_to_time_slot':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_frontend_service_title_color':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_frontend_book_button_color':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_service_booking_date_field':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_service_limit_box':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_service_sorting':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_frontend_grid_list_button':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_booking_currency':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_currency_position':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_from_email_name':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_admin_email':
				$value = sanitize_email( $value );
				break;
			case 'bm_from_email_address':
				$value = sanitize_email( $value );
				break;
			case 'bm_new_order_admin_email_body':
				$value = wp_kses_post( $value );
				break;
			case 'bm_stripe_secret_key':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_payment_session_time':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_allowed_stopsales':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_allowed_saleswitch':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_orders_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_services_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_categories_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_templates_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_price_modules_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_notification_processes_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_email_records_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_voucher_records_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_coupon_per_page':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_minimum_image_size':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_maximum_image_size':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_minimum_image_width':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_maximum_image_width':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_minimum_image_height':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_maximum_image_height':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_image_quality':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_lng_swtchr_in_admin_bar':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_show_lng_swtchr_in_footer':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_flexi_current_language':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_flexi_service_time_slot_format':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_flexi_service_price_format':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_enable_woocommerce_checkout':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_svc_shrt_desc_char_limit':
				$value = sanitize_text_field( $value );
				break;
			case 'bm_svc_overall_start_time':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_global_field()


	public function get_sanitized_fields_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'field_name':
				$value = sanitize_text_field( $value );
				break;
			case 'field_desc':
				$value = sanitize_text_field( $value );
				break;
			case 'field_type':
				$value = sanitize_text_field( $value );
				break;
			case 'field_options':
				$value = $value;
				break;
			case 'field_label':
				$value = sanitize_text_field( $value );
				break;
			case 'field_icon':
				$value = sanitize_text_field( $value );
				break;
			case 'is_required':
				$value = sanitize_text_field( $value );
				break;
			case 'is_editable':
				$value = sanitize_text_field( $value );
				break;
			case 'ordering':
				$value = sanitize_text_field( $value );
				break;
			case 'field_key':
				$value = sanitize_text_field( $value );
				break;
			case 'woocommerce_field':
				$value = sanitize_text_field( $value );
				break;
			case 'field_position':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_fields_field()


	public function get_sanitized_email_tmpl_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'tmpl_name_en':
				$value = sanitize_text_field( $value );
				break;
			case 'tmpl_name_it':
				$value = sanitize_text_field( $value );
				break;
			case 'type':
				$value = sanitize_text_field( $value );
				break;
			case 'email_subject_en':
				$value = sanitize_text_field( $value );
				break;
			case 'email_subject_it':
				$value = sanitize_text_field( $value );
				break;
			case 'email_body_en':
				$value = wp_kses_post( $value );
				break;
			case 'email_body_it':
				$value = wp_kses_post( $value );
				break;
			case 'status':
				$value = sanitize_text_field( $value );
				break;
			case 'error_message':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_email_tmpl_field()


	public function get_sanitized_customers_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'stripe_id':
				$value = sanitize_text_field( $value );
				break;
			case 'customer_name':
				$value = sanitize_text_field( $value );
				break;
			case 'customer_email':
				$value = sanitize_email( $value );
				break;
			case 'billing_details':
				$value = $value;
				break;
			case 'shipping_details':
				$value = $value;
				break;
			case 'shipping_same_as_billing':
				$value = sanitize_text_field( $value );
				break;
			case 'is_active':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_customers_field()


	public function get_sanitized_transactions_field( $field, $value ) {
        switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_id':
				$value = sanitize_text_field( $value );
				break;
			case 'wc_order_id':
				$value = sanitize_text_field( $value );
				break;
			case 'customer_id':
				$value = sanitize_text_field( $value );
				break;
			case 'paid_amount':
				$value = sanitize_text_field( $value );
				break;
			case 'paid_amount_currency':
				$value = sanitize_text_field( $value );
				break;
			case 'transaction_id':
				$value = sanitize_text_field( $value );
				break;
			case 'payment_method':
				$value = sanitize_text_field( $value );
				break;
			case 'payment_status':
				$value = sanitize_text_field( $value );
				break;
			case 'refund_id':
				$value = sanitize_text_field( $value );
				break;
			case 'is_active':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_transactions_field()


	public function get_sanitized_booking_archive_field( $field, $value ) {
        switch ( $field ) {
			case 'booking_data':
			case 'slot_data':
			case 'extraslot_data':
			case 'transaction_data':
			case 'pdf_path':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_booking_archive_field()


	public function get_sanitized_failed_transactions_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'customer_id':
				$value = sanitize_text_field( $value );
				break;
			case 'transaction_id':
				$value = sanitize_text_field( $value );
				break;
			case 'stripe_customer_id':
				$value = sanitize_text_field( $value );
				break;
			case 'amount':
				$value = sanitize_text_field( $value );
				break;
			case 'amount_currency':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_data':
				$value = $value;
				break;
			case 'customer_data':
				$value = $value;
				break;
			case 'gift_data':
				$value = $value;
				break;
			case 'is_refunded':
				$value = sanitize_text_field( $value );
				break;
			case 'refund_id':
				$value = sanitize_text_field( $value );
				break;
			case 'payment_status':
				$value = sanitize_text_field( $value );
				break;
			case 'refund_status':
				$value = sanitize_text_field( $value );
				break;
			case 'error_message':
				$value = $value;
				break;
			case 'booking_key':
				$value = sanitize_text_field( $value );
				break;
			case 'checkout_key':
				$value = sanitize_text_field( $value );
				break;
			case 'mail_sent':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_transactions_field()


	public function get_sanitized_external_service_price_module_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'module_name':
				$value = sanitize_text_field( $value );
				break;
			case 'module_values':
				$value = $value;
				break;
			case 'status':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_external_service_price_module_field()


	public function get_sanitized_emails_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'module_type':
				$value = sanitize_text_field( $value );
				break;
			case 'module_id':
				$value = sanitize_text_field( $value );
				break;
			case 'mail_type':
				$value = sanitize_text_field( $value );
				break;
			case 'template_id':
				$value = sanitize_text_field( $value );
				break;
			case 'process_id':
				$value = sanitize_text_field( $value );
				break;
			case 'status':
				$value = sanitize_text_field( $value );
				break;
			case 'is_resent':
				$value = sanitize_text_field( $value );
				break;
			case 'mail_sub':
				$value = $value;
				break;
			case 'mail_body':
				$value = $value;
				break;
			case 'mail_to':
				$value = $value;
				break;
			case 'mail_cc':
				$value = $value;
				break;
			case 'mail_bcc':
				$value = $value;
				break;
			case 'mail_attachments':
				$value = $value;
				break;
			case 'mail_lang':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_emails_field()


	public function get_sanitized_eventnotification_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'name':
				$value = sanitize_text_field( $value );
				break;
			case 'type':
				$value = sanitize_text_field( $value );
				break;
			case 'trigger_conditions':
				$value = $value;
				break;
			case 'time_offset':
				$value = $value;
				break;
			case 'template_id':
				$value = sanitize_text_field( $value );
				break;
			case 'status':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_eventnotification_field()


	public function get_sanitized_vouchers_field( $field, $value ) {
        switch ( $field ) {
			case 'recipient_data':
				$value = $value;
				break;
			case 'settings':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_vouchers_field()


	public function get_sanitized_pdf_customization_field( $field, $value ) {
        switch ( $field ) {
			case 'booking_pdf_en':
				$value = $value;
				break;
			case 'booking_pdf_it':
				$value = $value;
				break;
			case 'voucher_pdf_en':
				$value = $value;
				break;
			case 'voucher_pdf_it':
				$value = $value;
				break;
			case 'customer_info_pdf_en':
				$value = $value;
				break;
			case 'customer_info_pdf_it':
				$value = $value;
				break;
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_pdf_customization()


	public function get_sanitized_checkin_field( $field, $value ) {
        switch ( $field ) {
			default:
				$value = sanitize_text_field( $value );
		}

		return $value;
	} //end get_sanitized_checkin_field()


	public function remove_magic_quotes( $input ) {
		foreach ( $input as $key => $value ) {
			if ( is_array( $value ) ) {
				$input[ $key ] = $this->remove_magic_quotes( $value );
			} elseif ( is_string( $value ) ) {
				$input[ $key ] = stripslashes( $value );
			}
		}

		return $input;
	} //end remove_magic_quotes()

	public function get_sanitized_coupon_field( $field, $value ) {
		switch ( $field ) {
			case 'id':
				$value = sanitize_text_field( $value );
				break;
			case 'coupon_code':
				$value = sanitize_text_field( $value );
				break;
			case 'wc_order_id':
				$value = sanitize_text_field( $value );
				break;
			case 'service_id':
				$value = sanitize_text_field( $value );
				break;
			case 'discount_type':
				$value = sanitize_text_field( $value );
				break;
			case 'discount_amount':
				$value = sanitize_text_field( $value );
				break;
			case 'coupon_description':
				$value = sanitize_text_field( $value );
				break;
			case 'booking_id':
				$value = sanitize_text_field( $value );
				break;
			case 'expiry_date':
				$value = sanitize_text_field( $value );
				break;
			case 'coupon_unavailability':
				$value = $value;
				break;
			case 'min_spend':
				$value = sanitize_text_field( $value );
				break;
			case 'is_individual_use':
				$value = sanitize_text_field( $value );
				break;
			case 'max_spend':
				$value = sanitize_text_field( $value );
				break;
			case 'per_person_used_once':
				$value = sanitize_text_field( $value );
				break;
			case 'overall_used_once':
				$value = sanitize_text_field( $value );
				break;
			case 'used_per_coupon_per_service':
				$value = sanitize_text_field( $value );
				break;
			case 'is_email_exclude':
				$value = sanitize_text_field( $value );
				break;
			case 'excluded_conditions':
				$value = $value;
				break;
			case 'is_service_included':
				$value = sanitize_text_field( $value );
				break;
			case 'included_services':
				$value = $value;
				break;
			case 'is_geographic_restrictions':
				$value = sanitize_text_field( $value );
				break;
			case 'geographic_restriction':
				$value = $value;
				break;
			case 'is_active':
				$value = sanitize_text_field( $value );
				break;
			case 'coupon_used_count':
				$value = sanitize_text_field( $value );
				break;
			case 'coupon_used_data':
				$value = $value;
			case 'is_event_coupon':
				$value = sanitize_text_field( $value );
				break;
			case 'start_date_val':
				$value = sanitize_text_field( $value );
				break;
			case 'is_birthday_coupon':
				$value = sanitize_text_field( $value );
				break;

			case 'cannot_merged':
				$value = sanitize_text_field( $value );
				break;
			case 'auto_apply':
				$value = sanitize_text_field( $value );
				break;
			case 'coupon_image_guid':
				$value = sanitize_text_field( $value );
				break;
			default:
				$value = sanitize_text_field( $value );
		} //end switch

		return $value;
	} //end get_sanitized_coupon_field()


}//end class
