<?php

if ( !defined( 'ABSPATH' ) ) {
    exit;
}


$identifier           = 'BOOKING';
$dbhandler            = new BM_DBhandler();
$bmrequests           = new BM_Request();
$id                   = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$currency_symbol      = $bmrequests->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
$categories           = $dbhandler->get_all_result( 'CATEGORY', '*', 1, 'results', 0, false, 'cat_position', false );
$countries            = $bmrequests->bm_get_countries();
$country_globally_set = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
$transient_data       = array();
$booking_key          = '';
$checkout_key         = '';
/**$order_statuses    = $bmrequests->bm_fetch_order_status_key_value();*/

if ( $id == false || $id == null ) {
    $id = 0;
} else {
    die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Forbidden !!', 'service-booking' ) . '</div>' );
}

if ( !empty( $id ) ) {
    $order = $dbhandler->get_row( $identifier, $id, 'id' );

    if ( !empty( $order ) ) {
        $category_id              = $bmrequests->bm_fetch_category_id_by_service_id( $order->service_id );
        $services                 = $bmrequests->bm_fetch_services_by_category_id( $category_id );
        $category_name            = $bmrequests->bm_fetch_category_name_by_service_id( $order->service_id );
        $total_time_slots         = $bmrequests->bm_fetch_total_time_slots_by_service_id( $order->service_id );
        $booked_slots             = isset( $order->booking_slots ) ? maybe_unserialize( $order->booking_slots ) : array();
        $from_slot                = !empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $bmrequests->bm_am_pm_format( $booked_slots['from'] ) : '';
        $to_slot                  = !empty( $booked_slots ) && isset( $booked_slots['to'] ) ? $bmrequests->bm_am_pm_format( $booked_slots['to'] ) : '';
        $is_hidden_to_slot        = $bmrequests->bm_check_if_hidden_to_slot( $order->service_id, $order->booking_date, $booked_slots['from'] );
        $is_slot_variable         = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $order->service_id, $order->booking_date );
        $service_extra_ids        = $bmrequests->bm_fetch_backend_new_order_extra_services(
            array(
				'id'   => $order->service_id,
				'date' => $order->booking_date,
            )
        );
        $service_extra_ids        = !empty( $service_extra_ids ) ? array_column( $service_extra_ids, 'id' ) : null;
        $customerID               = $dbhandler->get_value( $identifier, 'customer_id', $id, 'id' );
        $customer                 = $dbhandler->get_row( 'CUSTOMERS', $customerID );
        $billing_details          = !empty( $customer ) && isset( $customer->billing_details ) ? esc_html( maybe_unserialize( $customer->billing_details ) ) : array();
        $shipping_details         = !empty( $customer ) && isset( $customer->shipping_details ) ? esc_html( maybe_unserialize( $customer->shipping_details ) ) : array();
        $shipping_same_as_billing = !empty( $customer ) && isset( $customer->shipping_same_as_billing ) ? esc_attr( $customer->shipping_same_as_billing ) : 0;
        $booking_key              = isset( $order->booking_key ) ? $order->booking_key : '';
        $checkout_key             = isset( $order->checkout_key ) ? $order->checkout_key : '';
        $price_module_data        = isset( $order->price_module_data ) ? $order->price_module_data : '';
        $price_module_data        = !empty( $price_module_data ) ? maybe_unserialize( $price_module_data ) : array();

        if ( !empty( $price_module_data ) && is_array( $price_module_data ) ) {
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
        }

        if ( $global_show_to_slots == 0 ) {
            $checkslot = $from_slot;
        } elseif ( $global_show_to_slots == 1 && $is_hidden_to_slot == 1 ) {
            $checkslot = $from_slot;
        } elseif ( $global_show_to_slots == 1 && $is_hidden_to_slot == 0 ) {
            $checkslot = $from_slot . ' - ' . $to_slot;
        }

        $slot_total_booked = $dbhandler->get_all_result(
            'SLOTCOUNT',
            'slot_total_booked',
            array(
				'booking_id'   => $id,
				'service_id'   => $order->service_id,
				'booking_date' => $order->booking_date,
                'is_active'    => 1,
            ),
            'var'
        );

        if ( !empty( $slot_total_booked ) && isset( $booked_slots['from'] ) ) {
            $booked_slot_info = $bmrequests->bm_fetch_slot_details( $order->service_id, $booked_slots['from'], $order->booking_date, $total_time_slots, 0, $is_slot_variable, array( 'slot_min_cap', 'capacity_left' ) );
        }

        if ( $total_time_slots == 1 ) {
            $time_slots = $bmrequests->bm_fetch_backend_new_order_single_time_slot_by_service_id( $order->service_id, $order->booking_date );
        } elseif ( $total_time_slots > 1 ) {
            $time_slots = $bmrequests->bm_fetch_backend_new_order_time_slot_by_service_id(
                array(
					'id'   => $order->service_id,
					'date' => $order->booking_date,
                )
            );
        }

        /**if (isset($order->wc_order_id) && !empty($order->wc_order_id) && $order->wc_order_id !== 0) {
        $order_statuses   = $bmrequests->get_all_woocommerce_order_statuses();
        }*/
        if ( isset( $order->has_extra ) && $order->has_extra == 1 ) {
            $extra_svc_ids = isset( $order->extra_svc_booked ) && !empty( $order->extra_svc_booked ) ? $order->extra_svc_booked : 0;
            $extra_content = array();
            if ( $extra_svc_ids !== 0 ) {
                $extra_svc_ids    = explode( ',', $extra_svc_ids );
                $merged_extra_ids = array_unique( array_merge( $service_extra_ids, $extra_svc_ids ) );
                $merged_extra_ids = array_values(
                    call_user_func(
                        function ( array $a ) {
                            asort( $a );
                            return $a;
                        },
                        $merged_extra_ids
                    )
                );

                foreach ( $merged_extra_ids as $key => $extra_id ) {
                    $name     = $dbhandler->get_all_result( 'EXTRA', 'extra_name', array( 'id' => $extra_id ), 'var' );
                    $price    = $dbhandler->get_all_result( 'EXTRA', 'extra_price', array( 'id' => $extra_id ), 'var' );
                    $quantity = $dbhandler->get_all_result(
                        'EXTRASLOTCOUNT',
                        'slots_booked',
                        array(
							'booking_id'   => $id,
							'service_id'   => $order->service_id,
							'extra_svc_id' => $extra_id,
							'booking_date' => $order->booking_date,
                            'is_active'    => 1,
                        ),
                        'var'
                    );
                    $quantity = isset( $quantity ) ? $quantity : 1;
                    $cap_left = $dbhandler->get_all_result(
                        'EXTRASLOTCOUNT',
                        'cap_left',
                        array(
							'booking_id'   => $id,
							'service_id'   => $order->service_id,
							'extra_svc_id' => $extra_id,
							'booking_date' => $order->booking_date,
                            'is_active'    => 1,
                        ),
                        'var'
                    );
                    $max_cap  = $dbhandler->get_all_result( 'EXTRA', 'extra_max_cap', array( 'id' => $extra_id ), 'var' );
                    $cap_left = isset( $cap_left ) ? $cap_left : $max_cap;
                    $total    = number_format( $bmrequests->bm_fetch_total_price( $price, $quantity ), 2 );

                    $extra_content[ $key ]['id']       = $extra_id;
                    $extra_content[ $key ]['name']     = $name;
                    $extra_content[ $key ]['price']    = $price;
                    $extra_content[ $key ]['quantity'] = $quantity;
                    $extra_content[ $key ]['cap_left'] = $cap_left;
                    $extra_content[ $key ]['max_cap']  = $max_cap;
                    $extra_content[ $key ]['total']    = number_format( $bmrequests->bm_fetch_total_price( $extra_content[ $key ]['price'], $extra_content[ $key ]['quantity'] ), 2 );
                }
            }//end if
        }//end if
    }//end if
}//end if

if ( ( filter_input( INPUT_POST, 'saveorder' ) ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_order_section' ) ) {
        die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'saveorder',
    );

    $order_post   = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );
    $booking_key  = isset( $order_post['booking_key'] ) ? $order_post['booking_key'] : '';
    $checkout_key = isset( $order_post['checkout_key'] ) ? $order_post['checkout_key'] : '';

    if ( $order_post !== false && !empty( $booking_key ) && !empty( $checkout_key ) ) {
        $service_id   = isset( $order_post['service_id'] ) && !empty( $order_post['service_id'] ) ? $order_post['service_id'] : 0;
        $service_date = isset( $order_post['booking_date'] ) && !empty( $order_post['booking_date'] ) ? $order_post['booking_date'] : '';

        if ( !empty( $service_id ) && !empty( $service_date ) && ( $bmrequests->bm_service_is_bookable( $service_id, $service_date ) ) ) {
            $saved_backend_order_data = $dbhandler->get_global_option_value( 'backend_order_data_' . $booking_key );
            $booking_fields           = $dbhandler->bm_fetch_data_from_transient( $booking_key );

            if ( $saved_backend_order_data != 1 || empty( $booking_fields ) ) {
                $booking_fields = $bmrequests->bm_fetch_backend_order_info( $order_post );
                $dbhandler->bm_save_data_to_transient( $booking_key, $booking_fields, 72 );
            }

            if ( !empty( $booking_fields ) ) {
                if ( ( $bmrequests->bm_check_if_cart_order_is_still_bookable( $booking_key ) ) ) {
                    if ( !empty( $id ) ) {
                        $order_post['booking_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
                    } else {
                        $order_post['booking_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
                    }

                    $bookable_extra = $bmrequests->bm_is_selected_extra_service_bookable( $booking_key );

                    if ( isset( $order_post['has_extra'] ) ) {
                        $order_post['has_extra']                = 1;
                        $extra_svc_booked                       = isset( $order_post['extra_svc_booked'] ) && !empty( $order_post['extra_svc_booked'] ) ? maybe_unserialize( $order_post['extra_svc_booked'] ) : array();
                        $total_extra_slots_booked               = isset( $order_post['total_extra_slots_booked'] ) && !empty( $order_post['total_extra_slots_booked'] ) ? maybe_unserialize( $order_post['total_extra_slots_booked'] ) : array();
                        $order_post['extra_svc_booked']         = !empty( $extra_svc_booked ) ? implode( ',', $extra_svc_booked ) : '';
                        $order_post['total_extra_slots_booked'] = !empty( $total_extra_slots_booked ) ? implode( ',', $total_extra_slots_booked ) : '';
                    } else {
                        $order_post['has_extra']                = 0;
                        $order_post['extra_svc_booked']         = '';
                        $order_post['total_extra_slots_booked'] = '';
                    }

                    $billing_details               = isset( $order_post['billing_details'] ) && !empty( $order_post['billing_details'] ) ? maybe_unserialize( $order_post['billing_details'] ) : array();
                    $shipping_details              = isset( $order_post['shipping_details'] ) && !empty( $order_post['shipping_details'] ) ? maybe_unserialize( $order_post['shipping_details'] ) : array();
                    $shipping_same_as_billing      = isset( $order_post['shipping_same_as_billing'] ) ? 1 : 0;
                    $order_post['booking_country'] = !empty( $billing_details ) && isset( $billing_details['billing_country'] ) ? $billing_details['billing_country'] : $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );

                    $first_name     = isset( $billing_details['billing_first_name'] ) ? $billing_details['billing_first_name'] : '';
                    $last_name      = isset( $billing_details['billing_last_name'] ) ? $billing_details['billing_last_name'] : '';
                    $customer_name  = !empty( $last_name ) ? $first_name . ' ' . $last_name : $first_name;
                    $customer_email = isset( $billing_details['billing_email'] ) ? $billing_details['billing_email'] : '';

                    $checkout_data = array(
                        'billing_details'  => $billing_details,
                        'shipping_details' => $shipping_details,
                        'other_data'       => array(
                            'shipping_same_as_billing' => $shipping_same_as_billing,
                            'booking_country'          =>  $order_post['booking_country'],
                        ),
                    );

                    $transient_data['checkout'] = $checkout_data;
                    $dbhandler->bm_save_data_to_transient( $checkout_key, $transient_data, 24 );

                    $svc_total_time_slots = !empty( $service_id ) ? $bmrequests->bm_fetch_total_time_slots_by_service_id( $service_id ) : 0;
                    $is_variable_slot     = !empty( $service_id ) && isset( $order_post['booking_date'] ) ? $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $order_post['booking_date'] ) : 0;

                    if ( isset( $order_post['booking_slots'] ) && !empty( $service_id ) && isset( $order_post['booking_date'] ) ) {
                        if ( strpos( $order_post['booking_slots'], ' - ' ) !== false ) {
                            $booking_slots = explode( ' - ', $order_post['booking_slots'] );
                            $from          = $bmrequests->bm_twenty_fourhrs_format( $booking_slots[0] );
                            $to            = $bmrequests->bm_twenty_fourhrs_format( $booking_slots[1] );
                        } else {
                            $from = $bmrequests->bm_twenty_fourhrs_format( $order_post['booking_slots'] );
                            if ( isset( $is_variable_slot ) && $is_variable_slot == 1 ) {
                                $to = $bmrequests->bm_fetch_variable_to_time_slot_by_service_id( $service_id, $from, $order_post['booking_date'] );
                            } else {
                                $to = $bmrequests->bm_fetch_non_variable_to_time_slot_by_service_id( $service_id, $from );
                            }
                        }

                        $order_post['booking_slots'] = maybe_serialize(
                            array(
                                'from' => $from,
                                'to'   => $to,
                            )
                        );
                    }

                    $total_service_booked = isset( $order_post['total_service_booking'] ) && !empty( $order_post['total_service_booking'] ) ? $order_post['total_service_booking'] : 0;
                    if ( isset( $order_post['add_more'] ) && isset( $order_post['add_more_persons'] ) ) {
                        $total_service_booked = ( $total_service_booked + intval( $order_post['add_more_persons'] ) );
                    }

                    if ( !empty( $service_id ) && !empty( $total_service_booked ) && isset( $order_post['booking_date'] ) ) {
                        $extra_service_ids         = isset( $order_post['extra_svc_booked'] ) && !empty( $order_post['extra_svc_booked'] ) ? $order_post['extra_svc_booked'] : null;
                        $extra_slots_booked        = isset( $order_post['total_extra_slots_booked'] ) && !empty( $order_post['total_extra_slots_booked'] ) ? $order_post['total_extra_slots_booked'] : null;
                        $order_post['wc_order_id'] = 0;

                        $slot_info = $bmrequests->bm_fetch_slot_details( $service_id, $from, $order_post['booking_date'], $svc_total_time_slots, $total_service_booked, $is_variable_slot );

                        if ( !empty( $slot_info ) ) {
                            if ( ( $bookable_extra == false ) ) {
                                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                                echo esc_html_e( 'One or more extra services does not have enough capacity, choose another !!', 'service-booking' );
                                echo ( '</div>' );
                            } else {
                                if ( isset( $slot_info['slot_capacity_left_after_booking'] ) && isset( $slot_info['slot_min_cap'] ) && ( $slot_info['slot_capacity_left_after_booking'] >= 0 ) && ( $total_service_booked % $slot_info['slot_min_cap'] == 0 ) ) {
                                    $total_cost = $order_post['has_extra'] == 1 ? ( floatval( $order_post['service_cost_float'] ) + floatval( $order_post['extra_svc_cost'] ) ) : floatval( $order_post['service_cost_float'] );

                                    if ( isset( $order_post['has_discount'] ) && isset( $order_post['service_discount'] ) && !empty( $order_post['service_discount'] ) ) {
                                        $subtotal   = $total_cost;
                                        $total_cost = ( $total_cost > $order_post['service_discount'] ) ? $total_cost - floatval( $order_post['service_discount'] ) : floatval( $order_post['service_discount'] ) - $total_cost;
                                    }

                                    $svc_price_module_data = $bmrequests->bm_fetch_price_module_data_for_order( $booking_key );

                                    $order_post['is_frontend_booking'] = 0;
                                    $order_post['base_svc_price']      = isset( $order_post['base_svc_price_float'] ) ? floatval( $order_post['base_svc_price_float'] ) : 0;
                                    $order_post['service_cost']        = isset( $order_post['service_cost_float'] ) ? floatval( $order_post['service_cost_float'] ) : 0;
                                    $order_post['extra_svc_cost']      = isset( $order_post['extra_svc_cost'] ) ? floatval( $order_post['extra_svc_cost'] ) : 0;
                                    $order_post['subtotal']            = isset( $subtotal ) ? $subtotal : $total_cost;
                                    $order_post['total_cost']          = $total_cost;
                                    $order_post['newsletter']          = 0;
                                    $order_post['mail_sent']           = 0;
                                    $order_post['is_active']           = 1;
                                    $order_post['booking_key']         = $booking_key;
                                    $order_post['checkout_key']        = $checkout_key;
                                    $order_post['total_svc_slots']     = !empty( $total_service_booked ) ? $total_service_booked : 0;
                                    $order_post['total_ext_svc_slots'] = !empty( $extra_slots_booked ) ? array_sum( explode( ',', $extra_slots_booked ) ) : 0;
                                    $order_post['disount_amount']      = isset( $order_post['service_discount'] ) ? floatval( $order_post['service_discount'] ) : 0;
                                    $order_post['price_module_data']   = isset( $order_post['has_discount'] ) && !empty( $svc_price_module_data ) ? maybe_serialize( $svc_price_module_data ) : null;


                                    if ( isset( $order_post['billing_details'] ) ) {
                                        unset( $order_post['billing_details'] );
                                    }

                                    if ( isset( $order_post['shipping_details'] ) ) {
                                        unset( $order_post['shipping_details'] );
                                    }

                                    if ( isset( $order_post['shipping_same_as_billing'] ) ) {
                                        unset( $order_post['shipping_same_as_billing'] );
                                    }

                                    if ( isset( $order_post['base_svc_price_float'] ) ) {
                                        unset( $order_post['base_svc_price_float'] );
                                    }

                                    if ( isset( $order_post['service_cost_float'] ) ) {
                                        unset( $order_post['service_cost_float'] );
                                    }

                                    if ( isset( $order_post['service_discount'] ) ) {
                                        unset( $order_post['service_discount'] );
                                    }

                                    if ( isset( $order_post['has_discount'] ) ) {
                                        unset( $order_post['has_discount'] );
                                    }

                                    if ( isset( $order_post['service_category'] ) ) {
                                        unset( $order_post['service_category'] );
                                    }

                                    if ( isset( $order_post['total_service_booking'] ) ) {
                                        unset( $order_post['total_service_booking'] );
                                    }

                                    if ( isset( $order_post['total_extra_slots_booked'] ) ) {
                                        unset( $order_post['total_extra_slots_booked'] );
                                    }

                                    if ( isset( $order_post['add_more_persons'] ) ) {
                                        unset( $order_post['add_more_persons'] );
                                    }

                                    if ( isset( $order_post['add_more'] ) ) {
                                        unset( $order_post['add_more'] );
                                    }

                                    if ( isset( $order_post['add_more_persons'] ) ) {
                                        unset( $order_post['add_more_persons'] );
                                    }

                                    if ( !empty( $id ) ) {
                                        $booking_id    = $id;
                                        $order_updated = $dbhandler->update_row( $identifier, 'id', $id, $order_post, '', '%d' );
                                    } else {
                                        $booking_id = $dbhandler->insert_row( $identifier, $order_post );
                                    }

                                    if ( isset( $booking_id ) && !empty( $booking_id ) ) {
                                        $dbhandler->update_global_option_value( 'bm_flexibooking_booking_id' . $booking_key, $booking_id );

                                        $slot_count_data = array(
                                            'service_id'   => $service_id,
                                            'booking_id'   => $booking_id,
                                            'wc_order_id'  => 0,
                                            'booking_date' => $order_post['booking_date'],
                                            'slot_id'      => $slot_info['slot_id'],
                                            'is_variable'  => $is_variable_slot,
                                            'slot_min_cap' => $slot_info['slot_min_cap'],
                                            'slot_max_cap' => $slot_info['slot_max_cap'],
                                            'slot_cap_left' => $slot_info['slot_capacity_left_after_booking'],
                                            'current_slots_booked' => $total_service_booked,
                                            'slot_total_booked' => $slot_info['slot_total_booked'],
                                            'svc_total_booked_slots' => $slot_info['total_booked_after_current_booking'],
                                            'total_time_slots' => $svc_total_time_slots,
                                            'svc_total_cap' => $slot_info['total_capacity'],
                                            'svc_total_cap_left' => $slot_info['svc_total_cap_left_after_booking'],
                                            'is_active'    => 1,
                                        );

                                        $slot_count_final_data = $bmrequests->sanitize_request( $slot_count_data, 'SLOTCOUNT' );

                                        if ( $slot_count_final_data != false && $slot_count_final_data != null ) {
                                            if ( !empty( $id ) ) {
                                                $slot_count_final_data['slot_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                $slot_count_updated = $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $id, $slot_count_final_data, '', '%d' );
                                            } else {
                                                $slot_count_final_data['slot_booked_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                $slot_count_id = $dbhandler->insert_row( 'SLOTCOUNT', $slot_count_final_data );
                                            }
                                        }

                                        if ( $extra_service_ids !== null && $extra_slots_booked !== null ) {
                                            $extra_service_ids  = explode( ',', $extra_service_ids );
                                            $extra_slots_booked = explode( ',', $extra_slots_booked );

                                            foreach ( $extra_service_ids as $key => $extra_id ) {
                                                $slots_booked  = $extra_slots_booked[ $key ];
                                                $extra_max_cap = $bmrequests->bm_fetch_extra_service_max_cap_by_extra_service_id( $extra_id );
                                                $cap_left      = $bmrequests->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date( $extra_id, $extra_max_cap, $slots_booked, $order_post['booking_date'] );

                                                $extra_svc_count_data = array(
                                                    'extra_svc_id' => $extra_id,
                                                    'service_id' => $service_id,
                                                    'booking_id' => $booking_id,
                                                    'wc_order_id' => 0,
                                                    'booking_count_id' => $slot_count_id,
                                                    'booking_date' => $order_post['booking_date'],
                                                    'max_cap'  => $extra_max_cap,
                                                    'slots_booked' => $slots_booked,
                                                    'cap_left' => $cap_left,
                                                    'is_active' => 1,
                                                );

                                                $extra_svc_count_final_data = $bmrequests->sanitize_request( $extra_svc_count_data, 'EXTRASLOTCOUNT' );

                                                if ( $extra_svc_count_final_data != false && $extra_svc_count_final_data != null ) {
                                                    if ( !empty( $id ) ) {
                                                        $extra_svc_count_final_data['slot_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                        $extra_slot_count_id      = $dbhandler->get_all_result(
                                                            'EXTRASLOTCOUNT',
                                                            'id',
                                                            array(
                                                                'service_id'   => $service_id,
                                                                'extra_svc_id' => $extra_id,
                                                                'booking_id'   => $id,
                                                                'booking_date' => $order_post['booking_date'],
                                                                'is_active'    => 1,
                                                            ),
                                                            'var'
                                                        );
                                                        $extra_slot_count_updated = $dbhandler->update_row( 'EXTRASLOTCOUNT', 'id', $extra_slot_count_id, $extra_svc_count_final_data, '', '%d' );
                                                    } else {
                                                        $extra_svc_count_final_data['slot_booked_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                        $extra_slot_count_id = $dbhandler->insert_row( 'EXTRASLOTCOUNT', $extra_svc_count_final_data );
                                                    }
                                                }
                                            }//end foreach
                                        }//end if

                                        $customer_data = array(
                                            'stripe_id' => null,
                                            'customer_name' => $customer_name,
                                            'customer_email' => $customer_email,
                                            'billing_details' => $billing_details,
                                            'shipping_details' => $shipping_details,
                                            'shipping_same_as_billing' => $shipping_same_as_billing,
                                            'is_active' => 1,
                                        );

                                        $customer_id = $dbhandler->get_value( 'CUSTOMERS', 'id', $customer_email, 'customer_email' );

                                        $customer_final_data = $bmrequests->sanitize_request( $customer_data, 'CUSTOMERS' );

                                        if ( $customer_final_data != false && $customer_final_data != null ) {
                                            if ( !empty( $customer_id ) ) {
                                                $customer_final_data['customer_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                $customer_updated = $dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_final_data, '', '%d' );
                                            } else {
                                                if ( !empty( $id ) ) {
                                                    $customer_id = $dbhandler->get_value( 'BOOKING', 'customer_id', $id, 'id' );

                                                    $customer_final_data['customer_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                    $customer_updated = $dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_final_data, '', '%d' );
                                                } else {
                                                    $customer_final_data['customer_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                    $customer_id = $dbhandler->insert_row( 'CUSTOMERS', $customer_final_data );
                                                }//end if
                                            }
                                        }//end if

                                        if ( isset( $customer_id ) && !empty( $customer_id ) ) {
                                            $dbhandler->update_row( 'BOOKING', 'id', $booking_id, array( 'customer_id' => $customer_id ), '', '%d' );
                                        }

                                        // Transaction data
                                        $transaction_data = array(
                                            'booking_id'  => $booking_id,
                                            'wc_order_id' => 0,
                                            'customer_id' => $customer_id,
                                            'paid_amount' => $order_post['total_cost'],
                                            'paid_amount_currency' => $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ),
                                            'transaction_id' => null,
                                            'payment_method' => 'card',
                                            'payment_status' => isset( $total_cost ) && empty( $total_cost ) ? 'free' : 'pending',
                                            'is_active'   => 1,
                                        );

                                        $payment_final = $bmrequests->sanitize_request( $transaction_data, 'TRANSACTIONS' );

                                        if ( $payment_final != false && $payment_final != null ) {
                                            if ( !empty( $id ) ) {
                                                $transaction_id = $dbhandler->get_value( 'TRANSACTIONS', 'id', $id, 'booking_id' );

                                                $payment_final['transaction_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                $dbhandler->update_row( 'TRANSACTIONS', 'id', $transaction_id, $payment_final, '', '%d' );
                                            } else {
                                                $payment_final['transaction_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

                                                $payment_id = $dbhandler->insert_row( 'TRANSACTIONS', $payment_final );
                                            }

                                            do_action( 'flexibooking_set_process_new_order', $booking_id );
                                        }

                                        if ( $payment_id > 0 ) {
                                            if ( is_admin() ) {
                                                $redirect_url = admin_url( 'admin.php?page=bm_all_orders' );
                                            } else {
                                                $redirect_url = add_query_arg( 'booking_success', '1', wp_get_referer() );
                                            }

                                            wp_safe_redirect( esc_url_raw( $redirect_url ) );
                                            exit;
                                        } else {
                                            if ( !is_admin() ) {
                                                $redirect_url = add_query_arg( 'booking_error', '1', wp_get_referer() );

                                                wp_safe_redirect( esc_url_raw( $redirect_url ) );
                                                exit;
                                            }
                                        }
                                    } else {
                                        $checkout_data = $dbhandler->bm_fetch_data_from_transient( $checkout_key );

                                        $failed_transaction_data = array(
                                            'amount'       => $order_post['total_cost'],
                                            'amount_currency' => $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ),
                                            'booking_data' => $dbhandler->bm_fetch_data_from_transient( $booking_key ),
                                            'customer_data' => isset( $checkout_data['checkout'] ) ? $checkout_data['checkout'] : array(),
                                            'booking_key'  => $booking_key,
                                            'checkout_key' => $checkout_key,
                                            'is_refunded'  => 0,
                                            'payment_status' => isset( $total_cost ) && empty( $total_cost ) ? 'free' : 'pending',
                                        );

                                        $failed_transaction = $bmrequests->sanitize_request( $failed_transaction_data, 'FAILED_TRANSACTIONS' );

                                        if ( $failed_transaction != false && $failed_transaction != null ) {
                                            $failed_transaction_id = $dbhandler->insert_row( 'FAILED_TRANSACTIONS', $failed_transaction );
                                        }

                                        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                                        echo esc_html__( 'Booking data could not be saved !!', 'service-booking' );
                                        echo ( '</div>' );
                                    }//end if
                                } else {
                                    echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                                    echo esc_html__( 'Not enough capacity left, please select another slot or service !!', 'service-booking' );
                                    echo ( '</div>' );
                                }//end if
                            }//end if
                        }//end if
                    }//end if
                } else {
                    echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                    echo esc_html__( 'Booking expired, please reselect slot and date !!', 'service-booking' );
                    echo ( '</div>' );
                }//end if
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Order Data could not be Processed !!', 'service-booking' );
                echo ( '</div>' );
            }//end if
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Service is not bookable !!', 'service-booking' );
            echo ( '</div>' );
        }//end if
    } else {
        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
        echo esc_html__( 'Order Data could not be Processed !!', 'service-booking' );
        echo ( '</div>' );
    }//end if
}//end if

if ( empty( $id ) ) {
    $booking_key  = $bmrequests->bm_generate_unique_code( '', 'FLEXIB', 15 );
    $checkout_key = $bmrequests->bm_generate_unique_code( '', 'FLEXIC', 15 );
}

wp_localize_script(
    'backennd-order-script',
    'backend_order_keys',
    array(
        'booking_key'  => $booking_key,
        'checkout_key' => $checkout_key,
    )
);

?>

<div class="sg-admin-main-box">
<div class="wrap">
    <form role="form" method="post" id="order_form">
        <h1 style="text-decoration :underline;"><?php !empty( $id ) ? esc_html_e( 'Booking Details', 'service-booking' ) : esc_html_e( 'Add Order', 'service-booking' ); ?></h1>
        <table class="form-table" role="presentation" id="order_table">
            <input type="hidden" name="service_discount" id="service_discount" value="<?php echo !empty( $order ) && isset( $order->disount_amount ) ? esc_attr( $order->disount_amount ) : 0; ?>">
            <tr>
                <th scope="row"><label for="booking_date"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></label></th>
                <td class="bminput bm_order_field_required">
                    <input name="booking_date" type="date" id="booking_date" placeholder="<?php esc_html_e( 'date', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $order ) && !empty( $order->booking_date ) ? esc_html( $order->booking_date ) : esc_html( gmdate( 'Y-m-d' ) ); ?>" min="<?php echo $id == 0 ? esc_html( gmdate( 'Y-m-d' ) ) : ''; ?>" autocomplete="off">
                    <div class="order_field_errortext"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="order_status"><?php esc_html_e( 'Order Status', 'service-booking' ); ?></label></th>
                <td class="bminput bm_order_field_required">
                    <select name="order_status" id="order_status" class="regular-text">
                        <option value="<?php echo esc_html_e( 'on_hold', 'service-booking' ); ?>"><?php esc_html_e( 'On Hold', 'service-booking' ); ?></option>
                    </select>
                    <div class="order_field_errortext"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="service_category"><?php esc_html_e( 'Category', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                <td class="bminput bm_order_field_required">
                    <select name="service_category" id="service_category" class="regular-text" onchange="bm_fetch_bookable_services(this.value)">
                        <option value=""><?php esc_html_e( 'Choose Category', 'service-booking' ); ?></option>
                        <option value="<?php echo esc_attr( 0 ); ?>"><?php esc_html_e( 'Uncategorized', 'service-booking' ); ?></option>
                        <?php
                        if ( !empty( $categories ) ) {
							foreach ( $categories as $category ) {
								?>
                            <option value="<?php echo esc_attr( $category->id ); ?>" <?php isset( $order ) && isset( $category_id ) && selected( $category_id, $category->id ); ?>><?php echo esc_html( $category->cat_name ); ?></option>
								<?php
                            }
						}
						?>
                    </select>
                    <div class="order_field_errortext"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="service_id"><?php esc_html_e( 'Service', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                <input type="hidden" name="service_name" id="service_name" value="<?php echo isset( $order ) && isset( $order->service_name ) ? esc_html( $order->service_name ) : ''; ?>">
                <td class="bminput bm_order_field_required" id="bm_services_by_category">
                    <select name="service_id" id="service_id" class="regular-text" onchange="bm_fetch_service_time_slots_by_service_id(this.value)" <?php echo $id == 0 ? 'disabled' : ''; ?>>
                        <?php
                        if ( isset( $services ) ) {
                            foreach ( $services as $service ) {
								?>
                                <option value="<?php echo esc_attr( $service->id ); ?>" <?php isset( $order ) && isset( $order->service_id ) && selected( $order->service_id, $service->id ); ?>><?php echo esc_html( $order->service_name ); ?></option>
								<?php
                            }
                        }
                        ?>
                    </select>
                    <div class="fetch_services_by_category_order_field_errortext"></div>
                    <div class="order_field_errortext"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="booking_slots"><?php esc_html_e( 'Time Slot', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                <td class="bminput bm_order_field_required">
                    <select name="booking_slots" id="booking_slots" class="regular-text" onchange="bm_fetch_bookable_no_of_slots_by_slot(this)" <?php echo $id == 0 ? 'disabled' : ''; ?>>
                        <?php
                        if ( isset( $time_slots ) ) {
                            foreach ( $time_slots as $slot ) {
								?>
                                <option value="<?php echo esc_html( $slot ); ?>" <?php isset( $checkslot ) && selected( $checkslot, $slot ); ?>><?php echo esc_html( $slot ); ?></option>
								<?php
                            }
                        }
                        ?>
                    </select>
                    <div class="fetch_slots_by_service_id_order_field_errortext"></div>
                    <div class="order_field_errortext"></div>
                </td>
            </tr>
            <?php if ( $id == 0 ) { ?>
                <tr>
                    <th scope="row"><label for="total_service_booking"><?php esc_html_e( 'No of Persons', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                    <td class="bminput bm_order_field_required">
                        <div class="extra_service_costs"></div>
                        <select name="total_service_booking" id="total_service_booking" class="regular-text" onchange="bm_fetch_svc_total_price()" <?php echo $id == 0 ? 'disabled' : ''; ?>></select>
                        <div class="fetch_no_of_slots_by_slot_data_order_field_errortext"></div>
                        <div class="order_field_errortext"></div>
                    </td>
                </tr>
            <?php } elseif ( !empty( $id ) ) { ?>
                <tr>
                    <th scope="row"><label for="total_service_booking"><?php esc_html_e( 'Booked No of Persons', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="total_service_booking" id="total_service_booking" type="text" class="regular-text" value="<?php echo isset( $slot_total_booked ) && !empty( $slot_total_booked ) ? esc_attr( $slot_total_booked ) : ''; ?>" autocomplete="off" <?php echo $id == 0 ? 'disabled' : 'readonly'; ?>>
                    </td>
                </tr>
                <?php if ( isset( $booked_slot_info['capacity_left'] ) && $booked_slot_info['capacity_left'] > 0 ) { ?>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Add More Persons ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="add_more" type="checkbox" id="add_more" class="regular-text bm_toggle" onchange="check_for_more_persons(this)">
                            <label for="add_more"></label>
                        </td>
                    </tr>
                    <tr class="add_more_person_section" style="display :none;">
                        <th scope="row"><label for="add_more_persons"><?php esc_html_e( 'No of Persons', 'service-booking' ); ?></label></th>
                        <td>
                            <select name="add_more_persons" id="add_more_persons" class="regular-text" disabled>
                                <?php
                                if ( isset( $booked_slot_info ) && isset( $booked_slot_info['slot_min_cap'] ) && isset( $booked_slot_info['capacity_left'] ) ) {
                                    for ( $i = $booked_slot_info['slot_min_cap']; $i <= $booked_slot_info['capacity_left']; $i += $booked_slot_info['slot_min_cap'] ) {
										?>
                                        <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_attr( $i ); ?></option>
										<?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
					<?php
                }
            }//end if
            ?>
            <tr class="service_price_tr" <?php echo $id == 0 ? 'style="display :none;"' : ''; ?>>
                <th scope="row"><label for="base_svc_price"><?php echo sprintf( esc_html__( 'Base Price (in %s)', 'service-booking' ), esc_html( $currency_symbol ) ); ?></label></th>
                <td>
                    <input name="base_svc_price" type="text" id="base_svc_price" class="regular-text" value="<?php echo isset( $order ) && !empty( $order->base_svc_price ) ? esc_attr( $order->base_svc_price ) : ''; ?>" autocomplete="off" <?php echo $id == 0 ? 'disabled' : 'readonly'; ?>>
                    <input type="hidden" name="base_svc_price_float" id="base_svc_price_float" value="<?php echo isset( $order ) && !empty( $order->base_svc_price ) ? esc_attr( $order->base_svc_price ) : ''; ?>">
                </td>
            </tr>
            <tr class="service_total_price_tr" <?php echo $id <= 0 ? 'style="display :none;"' : ''; ?>>
                <th scope="row"><label for="service_cost"><?php echo sprintf( esc_html__( 'Service Total (in %s)', 'service-booking' ), esc_html( $currency_symbol ) ); ?></label></th>
                <td>
                    <input name="service_cost" type="text" id="service_cost" class="regular-text" value="<?php echo isset( $order ) && !empty( $order->service_cost ) ? esc_attr( $order->service_cost ) : ''; ?>" autocomplete="off" <?php echo $id == 0 ? 'disabled' : 'readonly'; ?>>
                    <input type="hidden" name="service_cost_float" id="service_cost_float" value="<?php echo isset( $order ) && !empty( $order->service_cost ) ? esc_attr( $order->service_cost ) : ''; ?>">
                    <span class="service_discount_text hidden"><?php esc_html_e( 'service discount is .', 'service-booking' ); ?></span>
                </td>
            </tr>
            <tr class="service_add_extra" <?php echo ( isset( $extra_content ) && empty( $extra_content ) ) || $id == 0 ? 'style="display :none;"' : ''; ?>>
                <th scope="row"><?php esc_html_e( 'Add Extra', 'service-booking' ); ?></th>
                <td class="bm-checkbox-td">
                    <input name="has_extra" type="checkbox" id="has_extra" class="regular-text bm_toggle" <?php isset( $order ) && isset( $order->has_extra ) ? checked( $order->has_extra, '1' ) : ''; ?> onchange="bm_fetch_service_extra()">
                    <label for="has_extra"></label>
                </td>
            </tr>
            <tr class="service_extras" <?php echo !empty( $id ) && ( $order->has_extra == 1 ) ? '' : 'style="display :none;"'; ?>>
                <th scope="row"><label><?php esc_html_e( 'Service Extras', 'service-booking' ); ?></label></th>
                <input type="hidden" name="extra_svc_cost" id="extra_svc_cost" value="<?php echo isset( $order ) && !empty( $order->extra_svc_cost ) ? esc_attr( $order->extra_svc_cost ) : ''; ?>">
                <td>
                    <ul class="extra_content">
                        <?php
                        if ( isset( $extra_content ) && !empty( $extra_content ) ) {
                            foreach ( $extra_content as $key => $extra ) {
                                $booked_input_id = "extra_svc_booked_$key";
                                $quantity_id     = "extra_quantity_$key";
                                ?>
                                <li class='extra_services'>
                                    <span class='add_extra'>
                                        <input type='checkbox' name='extra_svc_booked[]' id="<?php echo esc_html( $booked_input_id ); ?>" value="<?php echo isset( $extra ) && isset( $extra['id'] ) ? esc_attr( $extra['id'] ) : ''; ?>" <?php echo isset( $extra_svc_ids ) && !empty( $extra_svc_ids ) && in_array( $extra['id'], $extra_svc_ids ) ? 'checked' : ''; ?> onchange='getExtraServicePrice(this)'>&nbsp;&nbsp;<strong><?php esc_html_e( 'Add', 'service-booking' ); ?></strong>
                                    </span>
                                    <label><strong><?php esc_html_e( 'Name:', 'service-booking' ); ?></strong></label>
                                    <input type='text' class='regular-text extra_name' value="<?php echo isset( $extra ) && isset( $extra['name'] ) ? esc_attr( $extra['name'] ) : ''; ?>" readonly><br>
                                    <label><strong><?php echo sprintf( esc_html__( 'Price (in %s):', 'service-booking' ), esc_html( $currency_symbol ) ); ?></strong></label>
                                    <input type='text' class='regular-text extra_price' value="<?php echo isset( $extra ) && isset( $extra['price'] ) ? esc_attr( $extra['price'] ) : ''; ?>" readonly><br>
                                    <label><strong><?php esc_html_e( 'Quantity (Cap left: ', 'service-booking' ); ?><span class='max_cap_text'><?php echo isset( $extra ) && isset( $extra['cap_left'] ) ? esc_attr( $extra['cap_left'] ) : ''; ?></span><?php esc_html_e( '): ', 'service-booking' ); ?></strong></label>
                                    <input type='number' name='total_extra_slots_booked[]' class='regular-text extra_quantity' min='1' max="<?php echo isset( $extra ) && isset( $extra['max_cap'] ) ? esc_attr( $extra['max_cap'] ) : ''; ?>" id="<?php echo esc_html( $quantity_id ); ?>" value="<?php echo isset( $extra ) && isset( $extra['quantity'] ) ? esc_attr( $extra['quantity'] ) : ''; ?>" onchange='checkExtraQuantityInputMaxValue(this)' <?php echo isset( $extra_svc_ids ) && !empty( $extra_svc_ids ) && in_array( $extra['id'], $extra_svc_ids ) ? '' : 'disabled'; ?>><br>
                                    <div class='extra-price-calculation'><?php esc_html_e( 'Total Price: ', 'service-booking' ); ?><?php echo isset( $extra ) && isset( $extra['total'] ) ? esc_attr( $extra['total'] ) : ''; ?><?php echo sprintf( esc_html__( ' (in %s):', 'service-booking' ), esc_html( $currency_symbol ) ); ?></div>
                                    <hr>
                                </li>
								<?php
                            }
                        }
                        ?>
                    </ul>
                </td>
            </tr>
            <tr class="service_add_discount" <?php echo ( isset( $price_module_data ) && empty( $price_module_data ) ) || $id == 0 ? 'style="display :none;"' : ''; ?>>
                <th scope="row"><?php esc_html_e( 'Add Discount', 'service-booking' ); ?></th>
                <td class="bm-checkbox-td">
                    <input name="has_discount" type="checkbox" id="has_discount" class="regular-text bm_toggle" <?php echo isset( $order ) && isset( $price_module_data ) && !empty( $price_module_data ) ? 'checked' : ''; ?> onchange="bm_fetch_service_price_discount_module()">
                    <label for="has_discount"></label>
                </td>
            </tr>
            <tr class="price_module_data" <?php echo !empty( $id ) && isset( $price_module_data ) && !empty( $price_module_data ) ? '' : 'style="display :none;"'; ?>>
                <th scope="row"><label><?php esc_html_e( 'Service Discount', 'service-booking' ); ?></label></th>
                <td>
                    <ul class="price_module_discount_content">
                        <?php
                        if ( isset( $price_module_data ) && !empty( $price_module_data ) ) {
                            for ( $i=0; $i < 4; $i++ ) {
                                $age_type_id        = "age_type_$i";
                                $age_group_from_id  = "age_group_from_$i";
                                $age_group_to_id    = "age_group_to_$i";
                                $age_group_total_id = "age_group_total_$i";

                                if ( $i == 0 ) {
                                    $age_label     = esc_html__( 'Infants', 'service-booking' );
                                    $age_title     = esc_html__( 'Age', 'service-booking' ) . ': ' . $infants_age_from . '-' . $infants_age_to;
                                    $total_persons = $total_discounted_infants;
                                } elseif ( $i == 1 ) {
                                    $age_label     = esc_html__( 'Children', 'service-booking' );
                                    $age_title     = esc_html__( 'Age', 'service-booking' ) . ': ' . $children_age_from . '-' . $children_age_to;
                                    $total_persons = $total_discounted_children;
                                } elseif ( $i == 2 ) {
                                    $age_label     = esc_html__( 'Adults', 'service-booking' );
                                    $age_title     = esc_html__( 'Age', 'service-booking' ) . ': ' . $adults_age_from . '-' . $adults_age_to;
                                    $total_persons = $total_discounted_adults;
                                } elseif ( $i == 3 ) {
                                    $age_label     = esc_html__( 'Seniors', 'service-booking' );
                                    $age_title     = esc_html__( 'Age', 'service-booking' ) . ': ' . $seniors_age_from . '-' . $seniors_age_to;
                                    $total_persons = $total_discounted_seniors;
                                }
                                ?>
                                <div class="checkout_age_range_fields">
                                    <div id="<?php echo esc_html( $age_type_id ); ?>" class="hidden">
                                        <span>
                                            <input name="<?php echo esc_html( $age_group_from_id ); ?>" type="hidden" class="age_range_fields" id="<?php echo esc_html( $age_group_from_id ); ?>" value="0">
                                        </span>
                                        <span>
                                            <input name="<?php echo esc_html( $age_group_to_id ); ?>" type="hidden" class="age_range_fields" id="<?php echo esc_html( $age_group_to_id ); ?>" value="2">
                                        </span>
                                    </div>
                                    <div class="age_label_total_parent_div">
                                        <div class="age_label_input_div">
                                            <label class="checkout_age_from_label"><?php echo esc_html( $age_label ); ?></label>
                                            <i class="fa fa-info-circle" aria-hidden="true" title="<?php echo esc_html( $age_title ); ?>"></i>
                                        </div>
                                        <div class="age_total_input_div">
                                            <input name="<?php echo esc_html( $age_group_to_id ); ?>" type="number" class="age_range_fields" id="<?php echo esc_html( $age_group_to_id ); ?>" style="margin-left:3px" placeholder="<?php echo esc_html__( 'total', 'service-booking' ); ?>" value="<?php echo esc_html( $total_persons ); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                </div>
								<?php
                            }
                            ?>
                                <div class="checkout_discount_buttons">
                                    <span class="primarybutton button-primary">
                                        <a href="#" id="check_checkout_discount" class="check_checkout_discount" title="<?php esc_html_e( 'Calculate', 'service-booking' ); ?>">
                                            <!-- <?php esc_html_e( 'Calculate', 'service-booking' ); ?> -->
                                            <i class="fa fa-calculator"></i>
                                        </a>
                                    </span>
                                    <span class="secondarybutton button-secondary">
                                        <a href="#" id="reset_checkout_discount" class="reset_checkout_discount" title="<?php esc_html_e( 'Reset', 'service-booking' ); ?>">
                                            <!-- <?php esc_html_e( 'Reset', 'service-booking' ); ?> -->
                                            <i class="fa fa-refresh"></i>
                                        </a>
                                    </span>
                                </div>
                            <?php
                        }
                        ?>
                    </ul>
                    <div class="age_errortext"></div>
                    <div class="order_details hidden">
                        <div class="expandorderbox" id="expandorderbox">
                            <ul class="ordertextbox">
                                <li><?php esc_html_e( 'Subtotal', 'service-booking' ); ?><span id="checkout_subtotal"></span><br /></li>
                                <li><?php esc_html_e( 'Discount', 'service-booking' ); ?> <span id="checkout_discount"></span></li>
                            </ul>
                            <p class="total"><?php esc_html_e( 'Total Cost ', 'service-booking' ); ?> <span id="checkout_total"></span></p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <div class="customer_details" style="display: flex;">
                <div class="greybox1 billing_details" <?php echo $id == 0 ? 'style="display :none;"' : ''; ?> id="billing_details">
                
                <h1 style="text-decoration :underline;"><?php esc_html_e( 'Billing Details', 'service-booking' ); ?></h1>

                <table class="form-table" role="presentation" >
                    <tr>
                        <td class="first_td">
                           
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing First Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_order_field_required">
                            <input name="billing_details[billing_first_name]" type="text" id="billing_first_name" placeholder="<?php esc_html_e( 'billing first name', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_first_name'] ) ? esc_html( $billing_details['billing_first_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Last Name', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="billing_details[billing_last_name]" type="text" id="billing_last_name" placeholder="<?php esc_html_e( 'billing last name', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_last_name'] ) ? esc_html( $billing_details['billing_last_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Email', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_order_field_required">
                            <input name="billing_details[billing_email]" type="email" id="billing_email" placeholder="<?php esc_html_e( 'billing email', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_email'] ) ? esc_html( $billing_details['billing_email'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Phone', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="order_billing_tel_input">
                            <input name="billing_details[billing_contact]" type="tel" id="billing_contact" placeholder="<?php esc_html_e( 'billing contact', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_contact'] ) ? esc_html( $billing_details['billing_contact'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Company', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="billing_details[billing_company]" type="text" id="billing_company" placeholder="<?php esc_html_e( 'billing company', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_company'] ) ? esc_html( $billing_details['billing_company'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Address', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="billing_details[billing_address]" type="text" id="billing_address" placeholder="<?php esc_html_e( 'billing address', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_address'] ) ? esc_html( $billing_details['billing_address'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Country', 'service-booking' ); ?></label></th>
                        <td class="bminput bm_order_field_required">
                            <select id="billing_country" name="billing_details[billing_country]" class="regular-text">
                                <?php
                                if ( !empty( $countries ) ) {
                                    foreach ( $countries as $key => $country ) {
                                        ?>
                                        <option value="<?php echo esc_html( $key ); ?>" <?php isset( $billing_details ) && isset( $billing_details['billing_country'] ) ? selected( esc_html( $billing_details['billing_country'] ), esc_html( $key ) ) : ''; ?> <?php !isset( $billing_details ) && !empty( $country_globally_set )  ? selected( esc_html( $country_globally_set ), esc_html( $key ) ) : ''; ?>><?php echo esc_html( $country ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing State', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <select id="billing_state" name="billing_details[billing_state]" class="regular-text">
                            <?php
                            $country = $billing_details['billing_country'] ?? $country_globally_set;
                            $states  = $bmrequests->bm_get_states( $country );
							if ( !empty( $states ) ) {
								foreach ( $states as $key => $state ) {
									?>
                                        <option value="<?php echo esc_html( $state['name'] ); ?>" <?php !empty( $billing_details['billing_state'] ) ? selected( esc_html( $billing_details['billing_state'] ), esc_html( $state['name'] ) ) : ''; ?>><?php echo esc_html( $state['name'] ); ?></option>
                                        <?php
								}
							}
							?>
                            </select>
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing City', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="billing_details[billing_city]" type="text" id="billing_city" placeholder="<?php esc_html_e( 'billing city', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_city'] ) ? esc_html( $billing_details['billing_city'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Postcode', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="billing_details[billing_postcode]" type="text" id="billing_postcode" placeholder="<?php esc_html_e( 'billing postcode', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_postcode'] ) ? esc_html( $billing_details['billing_postcode'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                </table>
                </div>
                <div class="greybox2 shipping_details"  <?php echo $id == 0 ? 'style="display :none;"' : ''; ?> id="shipping_details">
                <h1 class="shipping_detail_heading" style="text-decoration :underline;"><?php esc_html_e( 'Shipping Details', 'service-booking' ); ?>
                <span class="shipping_after_heading bm-checkbox-td">
                                <input name="shipping_same_as_billing" type="checkbox" id="shipping_same_as_billing" class="regular-text auto-checkbox bm_toggle" <?php isset( $shipping_same_as_billing )  ? checked( $shipping_same_as_billing, '1' ) : ''; ?>><label for="shipping_same_as_billing"></label>&nbsp;&nbsp;<h4 class="shipping_detail_subheading"><?php esc_html_e( 'Same as billing ?', 'service-booking' ); ?></h4>
                </span>
                </h1>
                <table class="form-table " role="presentation" style="margin-top:0px !important;">
                    <!-- <tr>
                        <td class="first_td">
                            <span class="shipping_detail_heading"></span>
                            
                        </td>
                    </tr> -->
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping First Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_order_field_required">
                            <input name="shipping_details[shipping_first_name]" type="text" id="shipping_first_name" placeholder="<?php esc_html_e( 'shipping first name', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_first_name'] ) ? esc_html( $shipping_details['shipping_first_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Last Name', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="shipping_details[shipping_last_name]" type="text" id="shipping_last_name" placeholder="<?php esc_html_e( 'shipping last name', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_last_name'] ) ? esc_html( $shipping_details['shipping_last_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Email', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_order_field_required">
                            <input name="shipping_details[shipping_email]" type="email" id="shipping_email" placeholder="<?php esc_html_e( 'shipping email', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_email'] ) ? esc_html( $shipping_details['shipping_email'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Phone', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="order_shipping_tel_input">
                            <input name="shipping_details[shipping_contact]" type="tel" id="shipping_contact" placeholder="<?php esc_html_e( 'shipping contact', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_contact'] ) ? esc_html( $shipping_details['shipping_contact'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Company', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="shipping_details[shipping_company]" type="text" id="shipping_company" placeholder="<?php esc_html_e( 'shipping company', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_company'] ) ? esc_html( $shipping_details['shipping_company'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Address', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="shipping_details[shipping_address]" type="text" id="shipping_address" placeholder="<?php esc_html_e( 'shipping address', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_address'] ) ? esc_html( $shipping_details['shipping_address'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Country', 'service-booking' ); ?></label></th>
                        <td class="bminput bm_order_field_required">
                            <select id="shipping_country" name="shipping_details[shipping_country]" class="regular-text">
                                <?php
                                if ( !empty( $countries ) ) {
                                    foreach ( $countries as $key => $country ) {
                                        ?>
                                        <option value="<?php echo esc_html( $key ); ?>" <?php isset( $shipping_details ) && isset( $shipping_details['shipping_country'] ) ? selected( esc_html( $shipping_details['shipping_country'] ), $key ) : ''; ?> <?php !isset( $shipping_details ) && !empty( $dbhandler->get_global_option_value( 'bm_booking_country' ) )  ? selected( esc_html( $dbhandler->get_global_option_value( 'bm_booking_country' ) ), esc_html( $key ) ) : ''; ?>><?php echo esc_html( $country ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping State', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                        <select id="shipping_state" name="shipping_details[shipping_state]" class="regular-text">
                            <?php
                            $country = $shipping_details['shipping_country'] ?? $country_globally_set;
                            $states  = $bmrequests->bm_get_states( $country );
							if ( !empty( $states ) ) {
								foreach ( $states as $key => $state ) {
									?>
                                        <option value="<?php echo esc_html( $state['name'] ); ?>" <?php !empty( $shipping_details['shipping_state'] ) ? selected( esc_html( $shipping_details['shipping_state'] ), esc_html( $state['name'] ) ) : ''; ?>><?php echo esc_html( $state['name'] ); ?></option>
                                        <?php
								}
							}
							?>
                            </select>
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping City', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="shipping_details[shipping_city]" type="text" id="shipping_city" placeholder="<?php esc_html_e( 'shipping city', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_city'] ) ? esc_html( $shipping_details['shipping_city'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Postcode', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="shipping_details[shipping_postcode]" type="text" id="shipping_postcode" placeholder="<?php esc_html_e( 'shipping postcode', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_postcode'] ) ? esc_html( $shipping_details['shipping_postcode'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                </table>
                </div>
                
                
            </div>
            <table>
                <tr>
                    <td>
                        <div class="row">
                            <?php wp_nonce_field( 'save_order_section' ); ?>
                            <a href="admin.php?page=bm_all_orders" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                            <input type="submit" name="saveorder" id="saveorder" class="button button-primary" value="<?php $id == 0 ? esc_attr_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="return order_form_validation()">
                            <div class="all_order_error_text" style="display:none;"></div>
                            <input type="hidden" name="booking_key" value="<?php echo esc_html( $booking_key ); ?>">
                            <input type="hidden" name="checkout_key" value="<?php echo esc_html( $checkout_key ); ?>">
                        </div>
                    </td>
                </tr>
            </table>
        </table>
    </form>
</div>

<div class="loader_modal"></div>
</div>
