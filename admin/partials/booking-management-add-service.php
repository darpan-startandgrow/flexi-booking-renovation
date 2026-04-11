<?php

// Check if user is allowed to add more services
$can_add = apply_filters( 'booking_management_can_add_service', true );

if ( !$can_add ) {
    echo '<div class="notice notice-warning"><p>Free version limit reached (20 services). Upgrade to Pro for unlimited services.</p></div>';
    return;
}

$svc_identifier     = 'SERVICE';
$cat_identifier     = 'CATEGORY';
$extra_identifier   = 'EXTRA';
$gallery_identifier = 'GALLERY';
$time_identifier    = 'TIME';
$dbhandler          = new BM_DBhandler();
$bmrequests         = new BM_Request();
$woocommerceservice = new WooCommerceService();
$categories         = $dbhandler->get_all_result( $cat_identifier, '*', 1, 'results' );
$global_extras      = $dbhandler->get_all_result( $extra_identifier, '*', array( 'is_global' => 1 ), 'results' );
$id                 = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$service_extra_id   = filter_input( INPUT_POST, 'svc_extra_id', FILTER_VALIDATE_INT );
$extra_id           = filter_input( INPUT_GET, 'extra_id', FILTER_VALIDATE_INT );
$currency_symbol    = $bmrequests->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
$stopsales_limit    = $dbhandler->get_global_option_value( 'bm_allowed_stopsales', 0 );
$stopsales_limit    = !empty( $stopsales_limit ) ? $stopsales_limit : 24;
$saleswitch_limit   = $dbhandler->get_global_option_value( 'bm_allowed_saleswitch', 0 );
$saleswitch_limit   = !empty( $saleswitch_limit ) ? $saleswitch_limit : 24;
$new_wc_price       = 0;
$price_modules      = $dbhandler->get_all_result( 'EXTERNAL_SERVICE_PRICE_MODULE', '*', 1, 'results' );
$image_quality      = intval( $dbhandler->get_global_option_value( 'bm_image_quality', '90' ) );

$woocommrce_integration = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );

$frontend_all_services_shortcode_selected_cat_ids = $dbhandler->get_global_option_value( 'bm_front_svc_search_shortcode_cat_ids', array() );

if ( $woocommerceservice->is_enabled() ) {
    $args        = array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
    );
    $wc_products = new WP_Query( $args );
}

$service_short_desc_settings = array(
    'wpautop'           => false,
    'media_buttons'     => true,
    'textarea_name'     => 'service_short_desc',
    'textarea_rows'     => 20,
    'tabindex'          => 4,
    'editor_height'     => 150,
    'tabfocus_elements' => ':prev,:next',
    'editor_css'        => '',
    'editor_class'      => '',
    'teeny'             => false,
    'dfw'               => false,
    'tinymce'           => true,
    'quicktags'         => true,
);

$service_desc_settings = array(
    'wpautop'           => false,
    'media_buttons'     => true,
    'textarea_name'     => 'service_desc',
    'textarea_rows'     => 20,
    'tabindex'          => 4,
    'editor_height'     => 150,
    'tabfocus_elements' => ':prev,:next',
    'editor_css'        => '',
    'editor_class'      => '',
    'teeny'             => false,
    'dfw'               => false,
    'tinymce'           => true,
    'quicktags'         => true,
);

$service_extra_desc_settings = array(
    'wpautop'           => false,
    'media_buttons'     => true,
    'textarea_name'     => 'svc_extra_desc',
    'textarea_rows'     => 20,
    'tabindex'          => 4,
    'editor_height'     => 150,
    'tabfocus_elements' => ':prev,:next',
    'editor_css'        => '',
    'editor_class'      => '',
    'teeny'             => false,
    'dfw'               => false,
    'tinymce'           => true,
    'quicktags'         => true,
);

if ( $id == false || $id == null ) {
    $id = 0;
}

if ( $service_extra_id == false || $service_extra_id == null ) {
    $service_extra_id = 0;
}

if ( $extra_id == false || $extra_id == null ) {
    $extra_id = 0;
}

if ( $id > 0 ) {
    $svc_row      = $dbhandler->get_row( $svc_identifier, $id );
    $svc_options  = isset( $svc_row->service_options ) ? maybe_unserialize( $svc_row->service_options ) : array();
    $svc_settings = isset( $svc_row->service_settings ) ? maybe_unserialize( $svc_row->service_settings ) : array();
    if ( isset( $svc_row ) ) {
        /**$image = wp_get_image_editor( get_attached_file( isset( $svc_row->service_image_guid ) ? $svc_row->service_image_guid : 0 ) );

        if ( is_numeric( $image_quality ) && isset( $image ) ) {
            $image->set_quality( intval( $image_quality ) );
        }*/

        $wc_product_id      = isset( $svc_row->wc_product ) ? esc_attr( $svc_row->wc_product ) : 0;
        $svc_img            = $bmrequests->bm_fetch_image_url_or_guid( $id, $svc_identifier, 'url' );
        $svc_unavailability = isset( $svc_row->service_unavailability ) ? maybe_unserialize( $svc_row->service_unavailability ) : array();
    }
    $extra_rows = $dbhandler->get_all_result( $extra_identifier, '*', array( 'service_id' => $id ), 'results' );
    $time_row   = $dbhandler->get_row( $time_identifier, $id );
    if ( isset( $time_row ) ) {
        $time_options = isset( $time_row->time_options ) ? maybe_unserialize( $time_row->time_options ) : array();
    }
    if ( !empty( $extra_rows ) && !empty( $global_extras ) ) {
        $total_extra_rows = array_merge( $global_extras, $extra_rows );
    } elseif ( empty( $extra_rows ) && !empty( $global_extras ) ) {
        $total_extra_rows = $global_extras;
    } elseif ( !empty( $extra_rows ) && empty( $global_extras ) ) {
        $total_extra_rows = $extra_rows;
    }
    $svc_gallery_images = $dbhandler->get_all_result(
        $gallery_identifier,
        '*',
        array(
            'module_type' => $svc_identifier,
            'module_id'   => $id,
        ),
        'results'
    );
    if ( isset( $svc_gallery_images ) ) {
        $svc_gallery_images = $svc_gallery_images[0];
    }
    if ( isset( $svc_gallery_images ) ) {
        $svc_gallery_guids = $bmrequests->bm_fetch_image_url_or_guid( $id, $gallery_identifier, 'guid', $svc_identifier );
    }
}

if ( $extra_id > 0 ) {
    $sv_extra_row = $dbhandler->get_row( $extra_identifier, $extra_id );
}

if ( ( filter_input( INPUT_POST, 'savesvc' ) ) || ( filter_input( INPUT_POST, 'upsvc' ) ) ) {

    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_svc_section' ) ) {
        die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
    }

    $exclude = array( '_wpnonce', '_wp_http_referer', 'savesvc', 'upsvc' );

    $data = array(
        'service_name'            => isset( $_POST['service_name'] ) ? ucfirst( filter_input( INPUT_POST, 'service_name' ) ) : '',
        'service_calendar_title'  => isset( $_POST['service_calendar_title'] ) ? ucfirst( filter_input( INPUT_POST, 'service_calendar_title' ) ) : '',
        'service_category'        => isset( $_POST['service_category'] ) ? filter_input( INPUT_POST, 'service_category', FILTER_VALIDATE_INT ) : null,
        'service_duration'        => isset( $_POST['service_duration'] ) ? filter_input( INPUT_POST, 'service_duration' ) : null,
        'service_operation'       => isset( $_POST['service_operation'] ) ? filter_input( INPUT_POST, 'service_operation' ) : null,
        'service_unavailability'  => isset( $_POST['service_unavailability'] ) ? filter_input( INPUT_POST, 'service_unavailability', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
        'service_options'         => isset( $_POST['service_options'] ) && array_filter( filter_input( INPUT_POST, 'service_options', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) ? filter_input( INPUT_POST, 'service_options', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
        'default_max_cap'         => !empty( $_POST['default_max_cap'] ) ? filter_input( INPUT_POST, 'default_max_cap' ) : 1,
        'default_stopsales'       => isset( $_POST['default_stopsales'] ) ? filter_input( INPUT_POST, 'default_stopsales' ) : null,
        'default_saleswitch'      => isset( $_POST['default_saleswitch'] ) ? filter_input( INPUT_POST, 'default_saleswitch' ) : null,
        'is_only_book_on_request' => isset( $_POST['is_only_book_on_request'] ) ? 1 : 0,
        'is_service_front'        => isset( $_POST['is_service_front'] ) ? 1 : 0,
        'show_stopsales_data'     => isset( $_POST['show_stopsales_data'] ) ? 1 : 0,
        'service_short_desc'      => isset( $_POST['service_short_desc'] ) ? filter_input( INPUT_POST, 'service_short_desc' ) : null,
        'service_desc'            => isset( $_POST['service_desc'] ) ? filter_input( INPUT_POST, 'service_desc' ) : null,
        'default_price'           => isset( $_POST['default_price'] ) ? filter_input( INPUT_POST, 'default_price' ) : null,
        'external_price_module'   => isset( $_POST['external_price_module'] ) ? filter_input( INPUT_POST, 'external_price_module' ) : null,
        'service_image_guid'      => isset( $_POST['svc_image_id'] ) ? filter_input( INPUT_POST, 'svc_image_id' ) : 0,
        'is_linked_wc_product'    => isset( $_POST['is_linked_wc_product'] ) ? 1 : 0,
        'wc_product'              => isset( $_POST['is_linked_wc_product'] ) ? filter_input( INPUT_POST, 'wc_product' ) : null,
        'service_settings'        => isset( $_POST['service_settings'] ) ? filter_input( INPUT_POST, 'service_settings', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
    );

    $svc_gallery = array(
        'module_type' => isset( $svc_identifier ) ? $svc_identifier : '',
        'image_guid'  => isset( $_POST['svc_gallery_image_id'] ) ? filter_input( INPUT_POST, 'svc_gallery_image_id' ) : null,
    );

    $time_data = array(
        'total_slots'  => isset( $_POST['total_time_slots'] ) ? filter_input( INPUT_POST, 'total_time_slots' ) : null,
        'time_slots'   => isset( $_POST['time_slots'] ) ? filter_input( INPUT_POST, 'time_slots', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
        'time_options' => isset( $_POST['time_options'] ) ? filter_input( INPUT_POST, 'time_options', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
    );

    $extrafields = array(
        'extra_name'             => isset( $_POST['svc_extra_name'] ) ? ucfirst( filter_input( INPUT_POST, 'svc_extra_name' ) ) : null,
        'extra_duration'         => isset( $_POST['svc_extra_duration'] ) ? filter_input( INPUT_POST, 'svc_extra_duration' ) : null,
        'extra_operation'        => isset( $_POST['svc_extra_operation'] ) ? filter_input( INPUT_POST, 'svc_extra_operation' ) : null,
        'extra_price'            => isset( $_POST['svc_extra_price'] ) ? filter_input( INPUT_POST, 'svc_extra_price' ) : null,
        'extra_max_cap'          => !empty( $_POST['svc_extra_max_cap'] ) ? filter_input( INPUT_POST, 'svc_extra_max_cap' ) : 1,
        'is_global'              => isset( $_POST['is_global'] ) ? 1 : 0,
        'is_extra_service_front' => filter_input( INPUT_POST, 'is_extra_service_front' ),
        'extra_desc'             => isset( $_POST['svc_extra_desc'] ) ? filter_input( INPUT_POST, 'svc_extra_desc' ) : null,
        'is_linked_wc_extrasvc'  => isset( $_POST['is_linked_wc_extrasvc'] ) ? 1 : 0,
        'svcextra_wc_product'    => isset( $_POST['is_linked_wc_extrasvc'] ) ? filter_input( INPUT_POST, 'svcextra_wc_product' ) : null,
    );

    if ( ( filter_input( INPUT_POST, 'savesvc' ) ) ) {
        if ( isset( $_POST['variable_svc_prices'] ) ) {
            $data['variable_svc_prices'] = filter_input( INPUT_POST, 'variable_svc_prices', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        }
        if ( isset( $_POST['variable_svc_price_modules'] ) ) {
            $data['variable_svc_price_modules'] = filter_input( INPUT_POST, 'variable_svc_price_modules', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        }
        if ( isset( $_POST['variable_stopsales'] ) ) {
            $data['variable_stopsales'] = filter_input( INPUT_POST, 'variable_stopsales', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        }
        if ( isset( $_POST['variable_saleswitch'] ) ) {
            $data['variable_saleswitch'] = filter_input( INPUT_POST, 'variable_saleswitch', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        }
        if ( isset( $_POST['variable_max_cap'] ) ) {
            $data['variable_max_cap'] = filter_input( INPUT_POST, 'variable_max_cap', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        }
        if ( isset( $_POST['variable_time_slots'] ) ) {
            $data['variable_time_slots'] = filter_input( INPUT_POST, 'variable_time_slots', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        }

        $service_post = $bmrequests->sanitize_request( $data, $svc_identifier, $exclude );

        if ( $service_post != false ) {

            $service_post['service_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $service_id                         = $dbhandler->insert_row( $svc_identifier, $service_post );
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html_e( 'Service Data could not be Processed !!', 'service-booking' );
            echo ( '</div>' );
        }

        if ( $service_id ) {
            if ( isset( $service_post['default_price'] ) && $service_post['wc_product'] > 0 && $woocommerceservice->is_enabled() ) {
                $woocommerceservice->set_wc_product_regular_price( $service_post['wc_product'], $service_post['default_price'] );
			}

			if ( ( filter_input( INPUT_POST, 'if_extra_svc' ) == '1' ) ) {
				$extrafields['service_id'] = isset( $_POST['is_global'] ) ? 0 : esc_attr( $service_id );
				$extra_service_post        = $bmrequests->sanitize_request( $extrafields, $extra_identifier, $exclude );

				if ( $extra_service_post != false ) {
					$extra_service_post['extras_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$extra_svc_id                            = $dbhandler->insert_row( $extra_identifier, $extra_service_post );

                    if ( !empty( $extra_svc_id ) ) {
                        if ( isset( $extra_service_post['extra_price'] ) && $extra_service_post['svcextra_wc_product'] > 0 && $woocommerceservice->is_enabled() ) {
                            $woocommerceservice->set_wc_product_regular_price( $extra_service_post['svcextra_wc_product'], $extra_service_post['extra_price'] );
                        }
                    }
				}
			}

			if ( ( filter_input( INPUT_POST, 'is_gallery_image' ) == '1' ) ) {
				$svc_gallery['module_id'] = esc_attr( $service_id );
				$gallery_post             = $bmrequests->sanitize_request( $svc_gallery, $gallery_identifier, $exclude );

				if ( $gallery_post != false ) {
					$gallery_post['gallery_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$svc_gallery_id                     = $dbhandler->insert_row( $gallery_identifier, $gallery_post );
				}
			}

			if ( filter_input( INPUT_POST, 'total_time_slots' ) != '0' ) {
				$time_data['service_id'] = esc_attr( $service_id );
				$time_post               = $bmrequests->sanitize_request( $time_data, $time_identifier, $exclude );

				if ( $time_post != false ) {
					$time_post['time_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$time_post_id                 = $dbhandler->insert_row( $time_identifier, $time_post );
				}
			}

            $category_id_added = isset( $service_post['service_category'] ) ? $service_post['service_category'] : 0;

			if ( !in_array( $category_id_added, $frontend_all_services_shortcode_selected_cat_ids ) ) {
				$frontend_all_services_shortcode_selected_cat_ids = array_merge( $frontend_all_services_shortcode_selected_cat_ids, array( $category_id_added ) );
				$dbhandler->update_global_option_value( 'bm_front_svc_search_shortcode_cat_ids', $frontend_all_services_shortcode_selected_cat_ids );
			}

            wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_all_services' ) );
            exit;
		} else {
			echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
			echo esc_html__( 'Service Could not be Added !!', 'service-booking' );
			echo ( '</div>' );
		}
	}

	if ( ( filter_input( INPUT_POST, 'upsvc' ) ) ) {
		if ( $id != 0 ) {
			if ( filter_input( INPUT_POST, 'default_price' ) != filter_input( INPUT_POST, 'old_default_price' ) ) {
				$new_wc_price                = 1;
				$data['variable_svc_prices'] = null;

				$data['variable_svc_price_modules'] = null;
			}
			if ( ( filter_input( INPUT_POST, 'default_stopsales' ) != filter_input( INPUT_POST, 'old_default_stopsales' ) ) ) {
				$data['variable_stopsales'] = null;
			}
			if ( ( filter_input( INPUT_POST, 'default_saleswitch' ) != filter_input( INPUT_POST, 'old_default_saleswitch' ) ) ) {
				$data['variable_saleswitch'] = null;
			}
			if ( ( filter_input( INPUT_POST, 'default_max_cap' ) != filter_input( INPUT_POST, 'old_default_max_cap' ) ) ) {
				$data['variable_max_cap'] = null;
			}
			if ( ( filter_input( INPUT_POST, 'total_time_slots' ) != filter_input( INPUT_POST, 'old_total_time_slots' ) ) ) {
				$data['variable_time_slots'] = null;
			}
			$data['service_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
			$service_post               = $bmrequests->sanitize_request( $data, $svc_identifier, $exclude );

			if ( $service_post != false ) {
				$svc_updated = $dbhandler->update_row( $svc_identifier, 'id', $id, $service_post, '', '%d' );
			} else {
				echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
				echo esc_html__( 'Service Data could not be Processed !!', 'service-booking' );
				echo ( '</div>' );
			}

			if ( $svc_updated ) {
                if ( isset( $service_post['default_price'] ) && $service_post['wc_product'] > 0 && $woocommerceservice->is_enabled() ) {
                    $woocommerceservice->set_wc_product_regular_price( $service_post['wc_product'], $service_post['default_price'] );
                }

				if ( ( filter_input( INPUT_POST, 'is_gallery_image' ) == '1' ) ) {
					$svc_gallery['module_id'] = esc_attr( $id );
					$gallery_post             = $bmrequests->sanitize_request( $svc_gallery, $gallery_identifier, $exclude );

					if ( $gallery_post != false ) {
						if ( isset( $svc_gallery_images ) && !empty( $svc_gallery_images ) ) {
							$gallery_post['gallery_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
							$svc_gallery_updated                = $dbhandler->update_row( $gallery_identifier, 'module_id', $id, $gallery_post, '', '%d' );
						} else {
							$svc_gallery_id = $dbhandler->insert_row( $gallery_identifier, $gallery_post );
						}
					}
				}

				if ( ( filter_input( INPUT_POST, 'if_extra_svc' ) == '1' ) ) {
					$extrafields['service_id'] = isset( $_POST['is_global'] ) ?  0 : esc_attr( $id );
					$extra_service_post        = $bmrequests->sanitize_request( $extrafields, $extra_identifier, $exclude );

					if ( $extra_service_post != false ) {
						$extra_service_post['extras_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
						$extra_svc_id                            = $dbhandler->insert_row( $extra_identifier, $extra_service_post );
					}
				}

				if ( filter_input( INPUT_POST, 'total_time_slots' ) != '0' ) {
					$time_data['service_id']      = esc_attr( $id );
					$time_data['time_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$time_post                    = $bmrequests->sanitize_request( $time_data, $time_identifier, $exclude );

					if ( $time_post != false ) {
						$time_post_updated = $dbhandler->update_row( $time_identifier, 'service_id', $id, $time_post, '', '%d' );
					}
				}


				$category_id_updated = isset( $service_post['service_category'] ) ? $service_post['service_category'] : 0;

				if ( !in_array( $category_id_updated, $frontend_all_services_shortcode_selected_cat_ids ) ) {
					$frontend_all_services_shortcode_selected_cat_ids = array_merge( $frontend_all_services_shortcode_selected_cat_ids, array( $category_id_updated ) );
					$dbhandler->update_global_option_value( 'bm_front_svc_search_shortcode_cat_ids', $frontend_all_services_shortcode_selected_cat_ids );
				}

				wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_service&id=' . esc_attr( $id ) ) );
				exit;
			} else {
				echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
				echo esc_html__( 'Service Could not be Updated !!', 'service-booking' );
				echo ( '</div>' );
			}
		} else {
			echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
			echo esc_html__( 'Service Id could not fetched !!', 'service-booking' );
			echo ( '</div>' );
		}
	}
}

if ( filter_input( INPUT_POST, 'editsvc_extra' ) ) {
    if ( $service_extra_id != 0 && $id != 0 ) {
        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_service&id=' . esc_attr( $id ) . '&extra_id=' . esc_attr( $service_extra_id ) ) );
        exit;
    } else {
        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
        echo esc_html__( 'Service Id or Extra Service Id could not fetched !!', 'service-booking' );
        echo ( '</div>' );
    }
}

if ( filter_input( INPUT_POST, 'cancel_upsvc_extra' ) ) {
    if ( $id != 0 ) {
        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_service&id=' . esc_attr( $id ) ) );
        exit;
    } else {
        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
        echo esc_html__( 'Service Id could not fetched !!', 'service-booking' );
        echo ( '</div>' );
    }
}

if ( filter_input( INPUT_POST, 'savesvc_extra' ) || filter_input( INPUT_POST, 'upsvc_extra' ) ) {

    $exclude = array( '_wpnonce', '_wp_http_referer', 'savesvc_extra', 'upsvc_extra' );

    $extrafields = array(
        'extra_name'             => isset( $_POST['svc_extra_name'] ) ? ucfirst( filter_input( INPUT_POST, 'svc_extra_name' ) ) : null,
        'extra_duration'         => isset( $_POST['svc_extra_duration'] ) ? filter_input( INPUT_POST, 'svc_extra_duration' ) : null,
        'extra_operation'        => isset( $_POST['svc_extra_operation'] ) ? filter_input( INPUT_POST, 'svc_extra_operation' ) : null,
        'extra_price'            => isset( $_POST['svc_extra_price'] ) ? filter_input( INPUT_POST, 'svc_extra_price' ) : null,
        'extra_max_cap'          => !empty( $_POST['svc_extra_max_cap'] ) ? filter_input( INPUT_POST, 'svc_extra_max_cap' ) : 1,
        'is_global'              => isset( $_POST['is_global'] ) ? 1 : 0,
        'is_extra_service_front' => filter_input( INPUT_POST, 'is_extra_service_front' ),
        'extra_desc'             => isset( $_POST['svc_extra_desc'] ) ? filter_input( INPUT_POST, 'svc_extra_desc' ) : null,
        'is_linked_wc_extrasvc'  => isset( $_POST['is_linked_wc_extrasvc'] ) ? 1 : 0,
        'svcextra_wc_product'    => isset( $_POST['is_linked_wc_extrasvc'] ) ? filter_input( INPUT_POST, 'svcextra_wc_product' ) : null,
    );

    if ( filter_input( INPUT_POST, 'savesvc_extra' ) ) {
        if ( $id != 0 ) {
            isset( $_POST['is_global'] ) ? $extrafields['service_id'] = 0 : $extrafields['service_id'] = esc_attr( $id );
            $extra_service_post                                       = $bmrequests->sanitize_request( $extrafields, $extra_identifier, $exclude );

            if ( $extra_service_post != false ) {
                $extra_field_id = $dbhandler->insert_row( $extra_identifier, $extra_service_post );
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Extra Service Data could not be Processed !!', 'service-booking' );
                echo ( '</div>' );
            }

            if ( isset( $extra_field_id ) && !empty( $extra_field_id ) ) {
                if ( isset( $extra_service_post['extra_price'] ) && $extra_service_post['svcextra_wc_product'] > 0 && $woocommerceservice->is_enabled() ) {
                    $woocommerceservice->set_wc_product_regular_price( $extra_service_post['svcextra_wc_product'], $extra_service_post['extra_price'] );
                }
                wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_service&id=' . esc_attr( $id ) ) );
                exit;
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Extra Service Could Not be Added !!', 'service-booking' );
                echo ( '</div>' );
            }
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Service Id could not fetched !!', 'service-booking' );
            echo ( '</div>' );
        }
    }

    if ( filter_input( INPUT_POST, 'upsvc_extra' ) ) {
        if ( $extra_id > 0 && $id > 0 ) {
            $extrafields['service_id']        = isset( $_POST['is_global'] ) ?  0 : esc_attr( $id );
            $extrafields['extras_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $extra_service_post               = $bmrequests->sanitize_request( $extrafields, $extra_identifier, $exclude );

            if ( $extra_service_post != false ) {
                $extra_field_updated = $dbhandler->update_row( $extra_identifier, 'id', $extra_id, $extra_service_post, '', '%d' );
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html_e( 'Extra Service Data could not be Processed !!', 'service-booking' );
                echo ( '</div>' );
            }

            if ( isset( $extra_field_updated ) && !empty( $extra_field_updated ) ) {
                if ( isset( $extra_service_post['extra_price'] ) && $extra_service_post['svcextra_wc_product'] > 0 && $woocommerceservice->is_enabled() ) {
                    $woocommerceservice->set_wc_product_regular_price( $extra_service_post['svcextra_wc_product'], $extra_service_post['extra_price'] );
                }
                wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_service&id=' . esc_attr( $id ) ) );
                exit;
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Extra Service Could Not be Updated !!', 'service-booking' );
                echo ( '</div>' );
            }
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Service Id or Extra Service Id could not fetched !!', 'service-booking' );
            echo ( '</div>' );
        }
    }
}

if ( filter_input( INPUT_POST, 'delsvc_extra' ) ) {
    if ( $service_extra_id > 0 && $id > 0 ) {
        $svc_extra_deleted = $dbhandler->remove_row( $extra_identifier, 'id', $service_extra_id, '%d' );
        if ( $svc_extra_deleted ) {
            wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_service&id=' . esc_attr( $id ) ) );
            exit;
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Extra Service Could Not be Deleted !!', 'service-booking' );
            echo ( '</div>' );
        }
    } else {
        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
        echo esc_html__( 'Service Id or Extra Service Id could not fetched !!', 'service-booking' );
        echo ( '</div>' );
    }
}

?>

<div class="sg-admin-main-box" id="service-records-main-box">
    <div class="wrap">
        <form role="form" method="post" enctype="multipart/form-data" class="service_page_form">
            <div class="tab" id="serviceTabs">
                <button type="button" class="tablinks <?php echo esc_attr( $extra_id ) == 0 ? 'active' : ''; ?>" onclick="openSection(event, 'service_details')"><?php esc_html_e( 'Service Details', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="gallery_button" onclick="openSection(event, 'service_gallery')"><?php esc_html_e( 'Gallery', 'service-booking' ); ?></button>
                <button type="button" class="tablinks <?php echo esc_attr( $extra_id ) != 0 ? 'active' : ''; ?>" id="extra_button" onclick="openSection(event, 'service_extra')"><?php esc_html_e( 'Extra', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="price_calendar_button" onclick="openSection(event, 'price_calendar')"><?php esc_html_e( 'Prices', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="stopsales_calendar_button" onclick="openSection(event, 'stopsales_calendar')"><?php esc_html_e( 'Stopsales', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="saleswitch_calendar_button" onclick="openSection(event, 'saleswitch_calendar')"><?php esc_html_e( 'Saleswitch', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="capacity_calendar_button" onclick="openSection(event, 'capacity_calendar')"><?php esc_html_e( 'Default Max Capacity', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="time_slot_button" onclick="openSection(event, 'time_slots_calendar')"><?php esc_html_e( 'Time Slots', 'service-booking' ); ?></button>
                <button type="button" class="tablinks" id="svc_settings_button" onclick="openSection(event, 'svc_settings_section')"><?php esc_html_e( 'Unavailability and other settings', 'service-booking' ); ?></button>
            </div>

        <tbody>
            <div id="service_details" class="tabcontent">
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="service_image"><?php esc_html_e( 'Image', 'service-booking' ); ?></label></th>
                        <td>
                            <input type="hidden" name="svc_image_id" id="svc_image_id" value="<?php echo isset( $svc_row ) && esc_attr( $svc_row->service_image_guid ) != 0 ? esc_attr( $svc_row->service_image_guid ) : ''; ?>">
                            <span class="svc_image_container" id="svc_image_container" style="<?php echo isset( $svc_img ) && !empty( $svc_img ) ? 'display: inline-block' : 'display: none'; ?>">
                                <img src="<?php echo isset( $svc_img ) ? esc_url( $svc_img ) : ''; ?>" width="100" height="100" id="svc_image_preview">
                                <button type="button" name="svc_image_remove" id="svc_image_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="remove_pdf_logo()"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                            </span>
                            <div>
                                <a href="javascript:void(0)" class="button svc-image"><?php esc_html_e( 'Upload image', 'service-booking' ); ?>&nbsp;<i class="fa fa-arrow-up" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="service_name"><?php esc_html_e( 'Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="service_name" type="text" id="service_name" placeholder="<?php esc_html_e( 'name', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $svc_row ) && !empty( $svc_row->service_name ) ? esc_html( $svc_row->service_name ) : ''; ?>" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="service_name"><?php esc_html_e( 'Calendar Title', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <input name="service_calendar_title" type="text" id="service_calendar_title" placeholder="<?php esc_html_e( 'service calendar title', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $svc_row->service_calendar_title ) ? esc_html( $svc_row->service_calendar_title ) : ''; ?>" autocomplete="off">
                            <div class="errortext"></div>
                            <span class="info_text">
                                <?php esc_html_e( 'Insert only if you want to show a specific title in service fullcalendar shortcode', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="service_category"><?php esc_html_e( 'Category', 'service-booking' ); ?></label></th>
                        <td>
                            <select name="service_category" id="service_category" class="regular-text">
                                <option value="0"><?php echo esc_html( 'uncategorized' ); ?></option>
                                <?php if ( isset( $categories ) && !empty( $categories ) ) { ?>
                                    <?php foreach ( $categories as $category ) { ?>
                                        <option value="<?php echo esc_attr( $category->id ) ?? ''; ?>" <?php isset( $svc_row ) && isset( $svc_row->service_category ) ? selected( esc_attr( $svc_row->service_category ), esc_attr( $category->id ) ) : ''; ?>><?php echo esc_html( $category->cat_name ) ?? ''; ?></option>
                                    <?php } ?>
                                <?php } ?>
                            </select>
                        </td>
                    </tr>
                    <input type="hidden" name="old_default_max_cap" id="old_default_max_cap">
                    <tr>
                        <th scope="row"><label for="default_max_cap"><?php esc_html_e( 'Default Maximum Capacity', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="default_max_cap" type="number" min="1" id="default_max_cap" placeholder="<?php esc_html_e( 'default maximum capacity', 'service-booking' ); ?>" value="<?php echo isset( $svc_row ) && !empty( $svc_row->default_max_cap ) ? esc_attr( $svc_row->default_max_cap ) : ''; ?>" class="regular-text" onchange="addCapacityInfo()" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <input type="hidden" name="old_total_time_slots" id="old_total_time_slots">
                    <input type="hidden" name="total_time_slots" id="total_time_slots" value="<?php echo isset( $time_row ) && !empty( $time_row ) ? esc_attr( $time_row->total_slots ) : '0'; ?>">
                    <input type="hidden" name="time_options[auto_time]" id="auto_time" value="<?php echo isset( $time_options ) && isset( $time_options['auto_time'] ) ? esc_attr( $time_options['auto_time'] ) : '0'; ?>">
                    <tr>
                        <th scope="row"><label for="service_duration"><?php esc_html_e( 'Duration (in hrs)', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <select name="service_duration" id="service_duration" class="regular-text" data-old="<?php echo isset( $svc_row ) && isset( $svc_row->service_duration ) ? esc_attr( $svc_row->service_duration ) : ''; ?>" onchange="showSlots(this)" 
                            <?php
                            if ( !isset( $svc_row ) ) {
								echo 'disabled';}
							?>
                            >
                                <option value=""><?php esc_html_e( 'Select Service Duration', 'service-booking' ); ?></option>
                                <option value="0.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '0.5' ) : ''; ?>><?php esc_html_e( '30min', 'service-booking' ); ?></option>
                                <option value="1" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '1' ) : ''; ?>><?php esc_html_e( '1h', 'service-booking' ); ?></option>
                                <option value="1.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '1.5' ) : ''; ?>><?php esc_html_e( '1h 30min', 'service-booking' ); ?></option>
                                <option value="2" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '2' ) : ''; ?>><?php esc_html_e( '2h', 'service-booking' ); ?></option>
                                <option value="2.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '2.5' ) : ''; ?>><?php esc_html_e( '2h 30min', 'service-booking' ); ?></option>
                                <option value="3" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '3' ) : ''; ?>><?php esc_html_e( '3h', 'service-booking' ); ?></option>
                                <option value="3.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '3.5' ) : ''; ?>><?php esc_html_e( '3h 30min', 'service-booking' ); ?></option>
                                <option value="4" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '4' ) : ''; ?>><?php esc_html_e( '4h', 'service-booking' ); ?></option>
                                <option value="4.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '4.5' ) : ''; ?>><?php esc_html_e( '4h 30min', 'service-booking' ); ?></option>
                                <option value="5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '5' ) : ''; ?>><?php esc_html_e( '5h', 'service-booking' ); ?></option>
                                <option value="5.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '5.5' ) : ''; ?>><?php esc_html_e( '5h 30min', 'service-booking' ); ?></option>
                                <option value="6" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '6' ) : ''; ?>><?php esc_html_e( '6h', 'service-booking' ); ?></option>
                                <option value="6.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '6.5' ) : ''; ?>><?php esc_html_e( '6h 30min', 'service-booking' ); ?></option>
                                <option value="7" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '7' ) : ''; ?>><?php esc_html_e( '7h', 'service-booking' ); ?></option>
                                <option value="7.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '7.5' ) : ''; ?>><?php esc_html_e( '7h 30min', 'service-booking' ); ?></option>
                                <option value="8" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '8' ) : ''; ?>><?php esc_html_e( '8h', 'service-booking' ); ?></option>
                                <option value="8.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '8.5' ) : ''; ?>><?php esc_html_e( '8h 30min', 'service-booking' ); ?></option>
                                <option value="9" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '9' ) : ''; ?>><?php esc_html_e( '9h', 'service-booking' ); ?></option>
                                <option value="9.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '9.5' ) : ''; ?>><?php esc_html_e( '9h 30min', 'service-booking' ); ?></option>
                                <option value="10" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '10' ) : ''; ?>><?php esc_html_e( '10h', 'service-booking' ); ?></option>
                                <option value="10.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '10.5' ) : ''; ?>><?php esc_html_e( '10h 30min', 'service-booking' ); ?></option>
                                <option value="11" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '11' ) : ''; ?>><?php esc_html_e( '11h', 'service-booking' ); ?></option>
                                <option value="11.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '11.5' ) : ''; ?>><?php esc_html_e( '11h 30min', 'service-booking' ); ?></option>
                                <option value="12" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '12' ) : ''; ?>><?php esc_html_e( '12h', 'service-booking' ); ?></option>
                                <option value="12.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '12.5' ) : ''; ?>><?php esc_html_e( '12h 30min', 'service-booking' ); ?></option>
                                <option value="13" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '13' ) : ''; ?>><?php esc_html_e( '13h', 'service-booking' ); ?></option>
                                <option value="13.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '13.5' ) : ''; ?>><?php esc_html_e( '13h 30min', 'service-booking' ); ?></option>
                                <option value="14" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '14' ) : ''; ?>><?php esc_html_e( '14h', 'service-booking' ); ?></option>
                                <option value="14.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '14.5' ) : ''; ?>><?php esc_html_e( '14h 30min', 'service-booking' ); ?></option>
                                <option value="15" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '15' ) : ''; ?>><?php esc_html_e( '15h', 'service-booking' ); ?></option>
                                <option value="15.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '15.5' ) : ''; ?>><?php esc_html_e( '15h 30min', 'service-booking' ); ?></option>
                                <option value="16" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '16' ) : ''; ?>><?php esc_html_e( '16h', 'service-booking' ); ?></option>
                                <option value="16.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '16.5' ) : ''; ?>><?php esc_html_e( '16h 30min', 'service-booking' ); ?></option>
                                <option value="17" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '17' ) : ''; ?>><?php esc_html_e( '17h', 'service-booking' ); ?></option>
                                <option value="17.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '17.5' ) : ''; ?>><?php esc_html_e( '17h 30min', 'service-booking' ); ?></option>
                                <option value="18" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '18' ) : ''; ?>><?php esc_html_e( '18h', 'service-booking' ); ?></option>
                                <option value="18.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '18.5' ) : ''; ?>><?php esc_html_e( '18h 30min', 'service-booking' ); ?></option>
                                <option value="19" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '19' ) : ''; ?>><?php esc_html_e( '19h', 'service-booking' ); ?></option>
                                <option value="19.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '19.5' ) : ''; ?>><?php esc_html_e( '19h 30min', 'service-booking' ); ?></option>
                                <option value="20" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '20' ) : ''; ?>><?php esc_html_e( '20h', 'service-booking' ); ?></option>
                                <option value="20.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '20.5' ) : ''; ?>><?php esc_html_e( '20h 30min', 'service-booking' ); ?></option>
                                <option value="21" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '21' ) : ''; ?>><?php esc_html_e( '21h', 'service-booking' ); ?></option>
                                <option value="21.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '21.5' ) : ''; ?>><?php esc_html_e( '21h 30min', 'service-booking' ); ?></option>
                                <option value="22" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '22' ) : ''; ?>><?php esc_html_e( '22h', 'service-booking' ); ?></option>
                                <option value="22.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '22.5' ) : ''; ?>><?php esc_html_e( '22h 30min', 'service-booking' ); ?></option>
                                <option value="23" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '23' ) : ''; ?>><?php esc_html_e( '23h', 'service-booking' ); ?></option>
                                <option value="23.5" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '23.5' ) : ''; ?>><?php esc_html_e( '23h 30min', 'service-booking' ); ?></option>
                                <option value="24" <?php isset( $svc_row ) && !empty( $svc_row->service_duration ) ? selected( esc_attr( $svc_row->service_duration ), '24' ) : ''; ?>><?php esc_html_e( '24h', 'service-booking' ); ?></option>
                            </select>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="service_operation"><?php esc_html_e( 'Total operating time (in hrs)', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <select name="service_operation" id="service_operation" class="regular-text" data-old="<?php echo isset( $svc_row ) && isset( $svc_row->service_operation ) ? esc_attr( $svc_row->service_operation ) : ''; ?>" onchange="showSlots(this)" 
                            <?php
                            if ( !isset( $svc_row ) ) {
								echo 'disabled';}
							?>
                            >
                                <option value=""><?php esc_html_e( 'Select Total Operation Time', 'service-booking' ); ?></option>
                                <option value="0.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '0.5' ) : ''; ?>><?php esc_html_e( '30min', 'service-booking' ); ?></option>
                                <option value="1" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '1' ) : ''; ?>><?php esc_html_e( '1h', 'service-booking' ); ?></option>
                                <option value="1.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '1.5' ) : ''; ?>><?php esc_html_e( '1h 30min', 'service-booking' ); ?></option>
                                <option value="2" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '2' ) : ''; ?>><?php esc_html_e( '2h', 'service-booking' ); ?></option>
                                <option value="2.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '2.5' ) : ''; ?>><?php esc_html_e( '2h 30min', 'service-booking' ); ?></option>
                                <option value="3" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '3' ) : ''; ?>><?php esc_html_e( '3h', 'service-booking' ); ?></option>
                                <option value="3.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '3.5' ) : ''; ?>><?php esc_html_e( '3h 30min', 'service-booking' ); ?></option>
                                <option value="4" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '4' ) : ''; ?>><?php esc_html_e( '4h', 'service-booking' ); ?></option>
                                <option value="4.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '4.5' ) : ''; ?>><?php esc_html_e( '4h 30min', 'service-booking' ); ?></option>
                                <option value="5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '5' ) : ''; ?>><?php esc_html_e( '5h', 'service-booking' ); ?></option>
                                <option value="5.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '5.5' ) : ''; ?>><?php esc_html_e( '5h 30min', 'service-booking' ); ?></option>
                                <option value="6" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '6' ) : ''; ?>><?php esc_html_e( '6h', 'service-booking' ); ?></option>
                                <option value="6.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '6.5' ) : ''; ?>><?php esc_html_e( '6h 30min', 'service-booking' ); ?></option>
                                <option value="7" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '7' ) : ''; ?>><?php esc_html_e( '7h', 'service-booking' ); ?></option>
                                <option value="7.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '7.5' ) : ''; ?>><?php esc_html_e( '7h 30min', 'service-booking' ); ?></option>
                                <option value="8" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '8' ) : ''; ?>><?php esc_html_e( '8h', 'service-booking' ); ?></option>
                                <option value="8.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '8.5' ) : ''; ?>><?php esc_html_e( '8h 30min', 'service-booking' ); ?></option>
                                <option value="9" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '9' ) : ''; ?>><?php esc_html_e( '9h', 'service-booking' ); ?></option>
                                <option value="9.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '9.5' ) : ''; ?>><?php esc_html_e( '9h 30min', 'service-booking' ); ?></option>
                                <option value="10" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '10' ) : ''; ?>><?php esc_html_e( '10h', 'service-booking' ); ?></option>
                                <option value="10.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '10.5' ) : ''; ?>><?php esc_html_e( '10h 30min', 'service-booking' ); ?></option>
                                <option value="11" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '11' ) : ''; ?>><?php esc_html_e( '11h', 'service-booking' ); ?></option>
                                <option value="11.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '11.5' ) : ''; ?>><?php esc_html_e( '11h 30min', 'service-booking' ); ?></option>
                                <option value="12" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '12' ) : ''; ?>><?php esc_html_e( '12h', 'service-booking' ); ?></option>
                                <option value="12.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '12.5' ) : ''; ?>><?php esc_html_e( '12h 30min', 'service-booking' ); ?></option>
                                <option value="13" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '13' ) : ''; ?>><?php esc_html_e( '13h', 'service-booking' ); ?></option>
                                <option value="13.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '13.5' ) : ''; ?>><?php esc_html_e( '13h 30min', 'service-booking' ); ?></option>
                                <option value="14" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '14' ) : ''; ?>><?php esc_html_e( '14h', 'service-booking' ); ?></option>
                                <option value="14.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '14.5' ) : ''; ?>><?php esc_html_e( '14h 30min', 'service-booking' ); ?></option>
                                <option value="15" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '15' ) : ''; ?>><?php esc_html_e( '15h', 'service-booking' ); ?></option>
                                <option value="15.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '15.5' ) : ''; ?>><?php esc_html_e( '15h 30min', 'service-booking' ); ?></option>
                                <option value="16" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '16' ) : ''; ?>><?php esc_html_e( '16h', 'service-booking' ); ?></option>
                                <option value="16.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '16.5' ) : ''; ?>><?php esc_html_e( '16h 30min', 'service-booking' ); ?></option>
                                <option value="17" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '17' ) : ''; ?>><?php esc_html_e( '17h', 'service-booking' ); ?></option>
                                <option value="17.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '17.5' ) : ''; ?>><?php esc_html_e( '17h 30min', 'service-booking' ); ?></option>
                                <option value="18" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '18' ) : ''; ?>><?php esc_html_e( '18h', 'service-booking' ); ?></option>
                                <option value="18.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '18.5' ) : ''; ?>><?php esc_html_e( '18h 30min', 'service-booking' ); ?></option>
                                <option value="19" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '19' ) : ''; ?>><?php esc_html_e( '19h', 'service-booking' ); ?></option>
                                <option value="19.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '19.5' ) : ''; ?>><?php esc_html_e( '19h 30min', 'service-booking' ); ?></option>
                                <option value="20" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '20' ) : ''; ?>><?php esc_html_e( '20h', 'service-booking' ); ?></option>
                                <option value="20.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '20.5' ) : ''; ?>><?php esc_html_e( '20h 30min', 'service-booking' ); ?></option>
                                <option value="21" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '21' ) : ''; ?>><?php esc_html_e( '21h', 'service-booking' ); ?></option>
                                <option value="21.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '21.5' ) : ''; ?>><?php esc_html_e( '21h 30min', 'service-booking' ); ?></option>
                                <option value="22" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '22' ) : ''; ?>><?php esc_html_e( '22h', 'service-booking' ); ?></option>
                                <option value="22.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '22.5' ) : ''; ?>><?php esc_html_e( '22h 30min', 'service-booking' ); ?></option>
                                <option value="23" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '23' ) : ''; ?>><?php esc_html_e( '23h', 'service-booking' ); ?></option>
                                <option value="23.5" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '23.5' ) : ''; ?>><?php esc_html_e( '23h 30min', 'service-booking' ); ?></option>
                                <option value="24" <?php isset( $svc_row ) && !empty( $svc_row->service_operation ) ? selected( esc_attr( $svc_row->service_operation ), '24' ) : ''; ?>><?php esc_html_e( '24h', 'service-booking' ); ?></option>
                            </select>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr class="slot_blocks" style="<?php echo isset( $time_row ) && !empty( $time_row ) ? ' ' : 'display: none'; ?>">
                        <th scope="row"><label for="time_slots"><?php esc_html_e( 'Time Slots', 'service-booking' ); ?></label></th>
                        <td id="time_slots">
                            <?php if ( isset( $time_row ) && !empty( $time_row ) ) { ?>
                                <div id="autoSelectTime" class="bm-checkbox-td"><b><?php esc_html_e( 'Autoselect Time ?', 'service-booking' ); ?></b>&nbsp;&nbsp;
                                    <input type='checkbox' name='autoselect_time' id='autoselect_time' class='auto-checkbox bm_toggle' onchange='autoTime()' <?php isset( $time_options ) && isset( $time_options['auto_time'] ) ? checked( esc_attr( $time_options['auto_time'] ), 1 ) : ''; ?>>
                                    <label for="autoselect_time"></label>
                                </div>
                                <br>
                            <?php } ?>
                            <?php
                            if ( isset( $time_row ) && !empty( $time_row ) ) {
                                $existing_slots = maybe_unserialize( $time_row->time_slots );
                                if ( !empty( $existing_slots['from'] ) && !empty( $existing_slots['to'] ) && !empty( $existing_slots['min_cap'] ) && !empty( $existing_slots['max_cap'] ) && !empty( $existing_slots['hide_to_slot'] ) && !empty( $existing_slots['disable'] ) ) {
                                    for ( $i = 1; $i <= $time_row->total_slots; $i++ ) {
                                        if ( isset( $existing_slots['from'][ $i ] ) && isset( $existing_slots['to'][ $i ] ) ) {
                                            $slotName     = "active_slot_$i";
                                            $from         = "time_slots[from][$i]";
                                            $to           = "time_slots[to][$i]";
                                            $from_id      = "from_$i";
                                            $to_id        = "to_$i";
                                            $disableName  = "time_slots[disable][$i]";
                                            $showToSlot   = "time_slots[hide_to_slot][$i]";
                                            $showToSlotId = "hide_to_slot_$i";
                                            $disableId    = "disable_$i";
                                            $maxCapName   = "time_slots[max_cap][$i]";
                                            $maxCap_id    = "max_cap_$i";
                                            $minCapName   = "time_slots[min_cap][$i]";
                                            $minCap_id    = "min_cap_$i";
                                            ?>
                                            <div id="<?php echo esc_html( $slotName ); ?>">
                                                <span class='bminput bm_required time_box'>
                                                    <?php esc_html_e( 'From:', 'service-booking' ); ?>&nbsp;<input type="time" name="<?php echo esc_html( $from ); ?>" id="<?php echo esc_html( $from_id ); ?>" value="<?php echo isset( $existing_slots ) && isset( $existing_slots['from'][ $i ] ) ? esc_html( $existing_slots['from'][ $i ] ) : ''; ?>" onchange="checkTime(this)" 
                                                                      <?php
																		if ( isset( $existing_slots['disable'][ $i ] ) && $existing_slots['disable'][ $i ] == '1' ) {
																			echo 'readonly';}
																		?>
                                                     autocomplete="off">
                                                    <span class='errortext calculated_time'></span>
                                                </span>&nbsp;&nbsp;
                                                <span class='bminput bm_required time_box'>
                                                    <?php esc_html_e( 'To:', 'service-booking' ); ?>&nbsp;<input type="time" name="<?php echo esc_html( $to ); ?>" id="<?php echo esc_html( $to_id ); ?>" value="<?php echo isset( $existing_slots ) && isset( $existing_slots['to'][ $i ] ) ? esc_html( $existing_slots['to'][ $i ] ) : ''; ?>" onchange="checkTime(this)" 
                                                                      <?php
																		if ( isset( $existing_slots['disable'][ $i ] ) && $existing_slots['disable'][ $i ] == '1' ) {
																			echo 'readonly';}
																		?>
                                                     autocomplete="off">
                                                    <span class='errortext calculated_time'></span>
                                                </span>&nbsp;&nbsp;
                                                <span class='bminput bm_required cap_box'>
                                                    <?php esc_html_e( 'Min Cap:', 'service-booking' ); ?>&nbsp;<input type="number" name="<?php echo esc_html( $minCapName ); ?>" min="1" id=<?php echo esc_html( $minCap_id ); ?> placeholder="<?php esc_html_e( 'minimum capacity', 'service-booking' ); ?>" value="<?php echo isset( $existing_slots ) && isset( $existing_slots['min_cap'][ $i ] ) ? esc_html( $existing_slots['min_cap'][ $i ] ) : ''; ?>" onchange="changeMaxCap(this)" 
                                                                      <?php
																		if ( isset( $existing_slots['disable'][ $i ] ) && $existing_slots['disable'][ $i ] == '1' ) {
																			echo 'readonly';}
																		?>
                                                     autocomplete="off">
                                                    <span class='errortext capacity_message'></span>
                                                </span>&nbsp;&nbsp;
                                                <span class='bminput bm_required cap_box'>
                                                    <?php esc_html_e( 'Max Cap:', 'service-booking' ); ?>&nbsp;<input type="number" name="<?php echo esc_html( $maxCapName ); ?>" min="<?php echo isset( $existing_slots ) && isset( $existing_slots['min_cap'][ $i ] ) ? esc_html( $existing_slots['min_cap'][ $i ] ) : '1'; ?>" id="<?php echo esc_html( $maxCap_id ); ?>" placeholder="<?php esc_html_e( 'maximum capacity', 'service-booking' ); ?>" value="<?php echo isset( $existing_slots ) && isset( $existing_slots['max_cap'][ $i ] ) ? esc_html( $existing_slots['max_cap'][ $i ] ) : ''; ?>" style="width: 80px" onchange='changeMaxCap(this)' 
                                                                      <?php
																		if ( isset( $existing_slots['disable'][ $i ] ) && $existing_slots['disable'][ $i ] == '1' ) {
																			echo 'readonly';}
																		?>
                                                     autocomplete="off">
                                                    <span class='errortext capacity_message'></span>
                                                </span>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <span>
                                                    <input type="hidden" name="<?php echo esc_html( $showToSlot ); ?>" id="<?php echo esc_html( $showToSlotId ); ?>" value="0">
                                                    <input type="checkbox" name="<?php echo esc_html( $showToSlot ); ?>" id="<?php echo esc_html( $showToSlotId ); ?>" value="1" <?php isset( $existing_slots ) && isset( $existing_slots['hide_to_slot'][ $i ] ) ? checked( esc_attr( $existing_slots['hide_to_slot'][ $i ] ), 1 ) : ''; ?> class="<?php echo isset( $existing_slots['disable'][ $i ] ) && $existing_slots['disable'][ $i ] == '1' ? 'readonly_checkbox' : ''; ?>">&nbsp;<?php esc_html_e( "Hide 'to' Slot ?", 'service-booking' ); ?>
                                                </span>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <span>
                                                    <input type="hidden" name="<?php echo esc_html( $disableName ); ?>" id="<?php echo esc_html( $disableId ); ?>" value="0">
                                                    <input type="checkbox" name="<?php echo esc_html( $disableName ); ?>" id="<?php echo esc_html( $disableId ); ?>" value="1" onchange="disableSlot(this)" <?php isset( $existing_slots ) && isset( $existing_slots['disable'][ $i ] ) ? checked( esc_attr( $existing_slots['disable'][ $i ] ), 1 ) : ''; ?>>&nbsp;<?php esc_html_e( 'Disable ?', 'service-booking' ); ?>
                                                </span>&nbsp;&nbsp;&nbsp;&nbsp;
                                                <div id="universal_slot_error_<?php echo esc_attr( $i ); ?>" style="display :none;font-family: monospace;color: #fb0000;font-size: 12px;margin-top :8px"></div>
                                            </div>
                                            <br>
											<?php
										}
                                    }
                                }
                            }
                            ?>
                        </td>
                    </tr>
                    <input type="hidden" name="old_default_stopsales" id="old_default_stopsales">
                    <input type="hidden" name="old_default_saleswitch" id="old_default_saleswitch">
                    <tr>
                        <th scope="row"><label for="default_stopsales"><?php esc_html_e( 'Default Stopsales (in hrs)', 'service-booking' ); ?></label></th>
                        <td>
                            <select name="default_stopsales" id="default_stopsales" class="regular-text" onchange="addStopsalesInfo()">
                                <option value="<?php echo esc_attr( 0 ); ?>"><?php esc_html_e( 'Select Stopsales Duration', 'service-booking' ); ?></option>
                                <?php for ( $i=0.5; $i<=$stopsales_limit; $i+=0.5 ) { ?>
                                    <option value="<?php echo esc_attr( $i ); ?>" <?php isset( $svc_row ) && isset( $svc_row->default_stopsales ) ? selected( esc_attr( $svc_row->default_stopsales ), $i ) : ''; ?>><?php echo esc_html( $bmrequests->bm_convert_float_to_time_string( $i ) ); ?></option>
                                <?php } ?>
                            </select>
                            <span class="info_text">
                                <?php esc_html_e( 'If stopsales of a service is 4 hours, then the service is not bookable until 4 hours from the current time ( if current time is 10AM, product will be bookable for slots available after 2PM )', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="default_saleswitch"><?php esc_html_e( 'Default Saleswitch (in hrs)', 'service-booking' ); ?></label></th>
                        <td>
                            <select name="default_saleswitch" id="default_saleswitch" class="regular-text" onchange="addSaleswitchInfo()">
                                <option value="<?php echo esc_attr( 0 ); ?>"><?php esc_html_e( 'Select Saleswitch Duration', 'service-booking' ); ?></option>
                                <?php for ( $i=0.5; $i<=$saleswitch_limit; $i+=0.5 ) { ?>
                                    <option value="<?php echo esc_attr( $i ); ?>" <?php isset( $svc_row ) && isset( $svc_row->default_saleswitch ) ? selected( esc_attr( $svc_row->default_saleswitch ), $i ) : ''; ?>><?php echo esc_html( $bmrequests->bm_convert_float_to_time_string( $i ) ); ?></option>
                                <?php } ?>
                            </select>
                            <span class="info_text">
                                <?php esc_html_e( 'If saleswicth of a service is 4 hours, then until 4 hours from the service bookable time, the bookings are eligible for book on request service, after that, only direct booking is allowed', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Book on request only ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_only_book_on_request" type="checkbox" id="is_only_book_on_request" class="regular-text bm_toggle" <?php isset( $svc_row ) && isset( $svc_row->is_only_book_on_request ) ? checked( esc_attr( $svc_row->is_only_book_on_request ), 1 ) : ''; ?>>
                            <label for="is_only_book_on_request"></label>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="service_short_desc"><?php esc_html_e( 'Short Description', 'service-booking' ); ?></label></th>
                        <td>
                            <div style="width: 75%;" class="sg-rg-buttom">
                                <?php isset( $svc_row ) && isset( $svc_row->service_short_desc ) ? wp_editor( $svc_row->service_short_desc, 'service_short_desc', $service_short_desc_settings ) : wp_editor( '', 'service_short_desc', $service_short_desc_settings ); ?>
                            </div>
                        </td>
                        <div class="svc_short_desc_error"></div>
                    </tr>

                    <tr>
                        <th scope="row"><label for="service_desc"><?php esc_html_e( 'Full Description', 'service-booking' ); ?></label></th>
                        <td>
                            <div style="width: 75%;" class="sg-rg-buttom">
                                <?php isset( $svc_row ) && isset( $svc_row->service_desc ) ? wp_editor( $svc_row->service_desc, 'service_desc', $service_desc_settings ) : wp_editor( '', 'service_desc', $service_desc_settings ); ?>
                            </div>
                        </td>
                    </tr>
                    <input type="hidden" name="old_default_price" id="old_default_price">
                    <tr>
                        <th scope="row"><label for="default_price"><?php echo sprintf( esc_html__( 'Default Price (in %s)', 'service-booking' ), esc_html( $currency_symbol ) ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="default_price" type="text" id="default_price" placeholder="<?php esc_html_e( 'price', 'service-booking' ); ?>" value="<?php echo isset( $svc_row ) && !empty( $svc_row->default_price ) ? esc_html( $svc_row->default_price ) : ''; ?>" onchange="addPriceInfo()" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="external_price_module"><?php echo sprintf( esc_html__( 'Link Price Module', 'service-booking' ), esc_html( $currency_symbol ) ); ?></label></th>
                        <td id="price_module_section">
                            <?php
                            if ( !empty( $price_modules ) ) {
                                ?>
                                <select name="external_price_module" id="external_price_module" class="regular-text">
                                    <option value=""><?php esc_html_e( 'Select Price Module', 'service-booking' ); ?></option>
                                    <?php foreach ( $price_modules as $price_module ) { ?>
                                        <option value="<?php echo esc_attr( $price_module->id ) ?? ''; ?>" <?php isset( $svc_row ) && isset( $svc_row->external_price_module ) ? selected( esc_attr( $svc_row->external_price_module ), esc_attr( $price_module->id ) ) : ''; ?>><?php echo esc_html( $price_module->module_name ) ?? ''; ?></option>
                                    <?php } ?>
                                </select>
                            <?php } else { ?>
                                <p><?php esc_html_e( 'No price modules found !!', 'service-booking' ); ?> &nbsp;&nbsp;<a href="<?php echo esc_url_raw( 'admin.php?page=bm_add_external_service_price' ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'Add price module', 'service-booking' ); ?></a><span class="info_text">
                                        <?php esc_html_e( 'Optional. If linked, prices defined as per this external module will be considered while checking out an order', 'service-booking' ); ?>
                                    </span></p>
                                <?php
                            }
                            ?>

                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Link WooCommerce Product ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_linked_wc_product" type="checkbox" id="is_linked_wc_product" class="regular-text bm_toggle" <?php isset( $svc_row ) && isset( $svc_row->is_linked_wc_product ) ? checked( esc_attr( $svc_row->is_linked_wc_product ), 1 ) : ''; ?> onclick="bm_open_close_tab('wc_products_section')">
                            <label for="is_linked_wc_product"></label>
                        </td>
                    </tr>
                    <tr id="wc_products_section" style="<?php echo !isset( $svc_row ) || $svc_row->is_linked_wc_product == 0 ? 'display: none' : ''; ?>">
                        <th scope="row"><label for="wc_product"><?php esc_html_e( 'WooCommerce Product', 'service-booking' ); ?></label></th>
                        <td id="products_section">
                            <?php
                            if ( isset( $wc_products ) ) {
                                if ( $wc_products->have_posts() ) {
                                    ?>
                                    <select name="wc_product" id="wc_product" class="regular-text">
                                        <option value=""><?php esc_html_e( 'Select WooCommerce Product', 'service-booking' ); ?></option>
                                        <?php
                                        while ( $wc_products->have_posts() ) {
                                            $wc_products->the_post();
                                            $product = wc_get_product( get_the_ID() );

                                            if ( $product ) {
                                                ?>
                                                <option value="<?php echo esc_attr( $product->get_id() ); ?>" 
                                                    <?php echo ( isset( $svc_row ) && isset( $svc_row->wc_product ) ) ? selected( esc_attr( $svc_row->wc_product ), esc_attr( $product->get_id() ), false ) : ''; ?>>
                                                    <?php echo esc_html( $product->get_title() ); ?>
                                                </option>
                                                <?php
                                            }
                                        }
                                        wp_reset_postdata();
                                        ?>
                                    </select>
                                    <?php
                                } else {
                                    ?>
                                    <p><?php esc_html_e( 'WooCommerce Products not available !!', 'service-booking' ); ?> &nbsp;&nbsp;
                                        <a href="<?php echo esc_url_raw( admin_url( 'post-new.php?post_type=product' ) ); ?>" target="_blank" class="button button-secondary">
                                            <?php esc_html_e( 'Add Product', 'service-booking' ); ?>
                                        </a>
                                    </p>
                                    <?php
                                }
                            } else {
								?>
                                <p><?php esc_html_e( 'WooCommerce Plugin is not installed/enabled !!', 'service-booking' ); ?> &nbsp;&nbsp;<a href="<?php echo esc_url_raw( get_site_url() . '/wp-admin/update.php?action=install-plugin&plugin=woocommerce&_wpnonce=f86b375973' ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'Add Plugin', 'service-booking' ); ?></a></p>
                            <?php } ?>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                </table>
            </div>

                <div id="service_gallery" class="tabcontent">
                    <input type="hidden" name="is_gallery_image" id="is_gallery_image" value="<?php echo isset( $svc_gallery_guids ) && !empty( $svc_gallery_guids ) ? '1' : '0'; ?>">
                    <input type="hidden" name="svc_gallery_image_id" id="svc_gallery_image_id" value="<?php echo isset( $svc_gallery_guids ) && !empty( $svc_gallery_guids ) ? esc_html( $svc_gallery_guids ) : ''; ?>">
                    <table class="form-table" role="presentation">
                        <tr>
                            <th scope="row"><label for="service_image"><?php esc_html_e( 'Images', 'service-booking' ); ?></label></th>
                            <td>
                                <div id="gallery_images" style="<?php echo isset( $svc_gallery_guids ) && !empty( $svc_gallery_guids ) ? 'display: block' : 'display: none'; ?>">
                                    <?php
                                    if ( isset( $svc_gallery_guids ) && !empty( $svc_gallery_guids ) ) {
                                        $image_guids = explode( ',', $svc_gallery_guids );
                                        foreach ( $image_guids as $guid ) {
                                            if ( !empty( $guid ) ) {
												?>
                                                <span class="svc_gallery_image_container" id="svc_gallery_image_container">
                                                    <img src="<?php echo esc_url( wp_get_attachment_image_src( esc_attr( $guid ), array( 100, 100 ) )[0] ); ?>" id="svc_gallery_image_preview">
                                                    <button type="button" class="svc_gallery_image_remove" id="<?php echo esc_attr( $guid ); ?>" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="svc_gallery_remove(this)"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                                                </span>
												<?php
                                            }
                                        }
                                    }
                                    ?>
                                </div>
                                <a href="javascript:void(0)" class="button svc-gallery-image"><?php esc_html_e( 'Add image', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                    </table>
                </div>

            <div id="service_extra" class="tabcontent">
                <input type="hidden" name="if_extra_svc" id="if_extra_svc">
                <table class="form-table" role="presentation">
                    <tr style="<?php echo esc_attr( $extra_id ) != 0 ? 'display: none' : ''; ?>" id="extraTitle">
                        <th scope="row"><label for="service_image"><?php esc_html_e( 'Add Extra', 'service-booking' ); ?></label></th>
                        <td>
                            <button type="button" id="add_extra" class="button button-secondary"><?php esc_attr_e( 'Add Extra', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></button>
                        </td>
                    </tr>
                    <table class="form-table" id="svc_extra_fields" role="presentation" style="<?php echo esc_attr( $extra_id ) != 0 ? 'display: block' : 'display: none'; ?>">
                        <tr>
                            <th scope="row"><label for="svc_extra_name"><?php esc_html_e( 'Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                            <td class="bminput bm_ex_required">
                                <input name="svc_extra_name" type="text" id="svc_extra_name" placeholder="<?php esc_html_e( 'name', 'service-booking' ); ?>" value="<?php echo isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_name ) ? esc_html( $sv_extra_row->extra_name ) : ''; ?>" class="regular-text" autocomplete="off">
                                <div class="errortext"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="svc_extra_duration"><?php esc_html_e( 'Duration (in hrs)', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                            <td class="bminput bm_ex_required">
                                <select name="svc_extra_duration" id="svc_extra_duration" class="regular-text">
                                    <option value=""><?php esc_html_e( 'Select Service Duration', 'service-booking' ); ?></option>
                                    <option value="0.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '0.5' ) : ''; ?>><?php esc_html_e( '30min', 'service-booking' ); ?></option>
                                    <option value="1" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '1' ) : ''; ?>><?php esc_html_e( '1h', 'service-booking' ); ?></option>
                                    <option value="1.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '1.5' ) : ''; ?>><?php esc_html_e( '1h 30min', 'service-booking' ); ?></option>
                                    <option value="2" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '2' ) : ''; ?>><?php esc_html_e( '2h', 'service-booking' ); ?></option>
                                    <option value="2.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '2.5' ) : ''; ?>><?php esc_html_e( '2h 30min', 'service-booking' ); ?></option>
                                    <option value="3" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '3' ) : ''; ?>><?php esc_html_e( '3h', 'service-booking' ); ?></option>
                                    <option value="3.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '3.5' ) : ''; ?>><?php esc_html_e( '3h 30min', 'service-booking' ); ?></option>
                                    <option value="4" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '4' ) : ''; ?>><?php esc_html_e( '4h', 'service-booking' ); ?></option>
                                    <option value="4.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '4.5' ) : ''; ?>><?php esc_html_e( '4h 30min', 'service-booking' ); ?></option>
                                    <option value="5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '5' ) : ''; ?>><?php esc_html_e( '5h', 'service-booking' ); ?></option>
                                    <option value="5.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '5.5' ) : ''; ?>><?php esc_html_e( '5h 30min', 'service-booking' ); ?></option>
                                    <option value="6" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '6' ) : ''; ?>><?php esc_html_e( '6h', 'service-booking' ); ?></option>
                                    <option value="6.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '6.5' ) : ''; ?>><?php esc_html_e( '6h 30min', 'service-booking' ); ?></option>
                                    <option value="7" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '7' ) : ''; ?>><?php esc_html_e( '7h', 'service-booking' ); ?></option>
                                    <option value="7.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '7.5' ) : ''; ?>><?php esc_html_e( '7h 30min', 'service-booking' ); ?></option>
                                    <option value="8" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '8' ) : ''; ?>><?php esc_html_e( '8h', 'service-booking' ); ?></option>
                                    <option value="8.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '8.5' ) : ''; ?>><?php esc_html_e( '8h 30min', 'service-booking' ); ?></option>
                                    <option value="9" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '9' ) : ''; ?>><?php esc_html_e( '9h', 'service-booking' ); ?></option>
                                    <option value="9.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '9.5' ) : ''; ?>><?php esc_html_e( '9h 30min', 'service-booking' ); ?></option>
                                    <option value="10" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '10' ) : ''; ?>><?php esc_html_e( '10h', 'service-booking' ); ?></option>
                                    <option value="10.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '10.5' ) : ''; ?>><?php esc_html_e( '10h 30min', 'service-booking' ); ?></option>
                                    <option value="11" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '11' ) : ''; ?>><?php esc_html_e( '11h', 'service-booking' ); ?></option>
                                    <option value="11.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '11.5' ) : ''; ?>><?php esc_html_e( '11h 30min', 'service-booking' ); ?></option>
                                    <option value="12" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '12' ) : ''; ?>><?php esc_html_e( '12h', 'service-booking' ); ?></option>
                                    <option value="12.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '12.5' ) : ''; ?>><?php esc_html_e( '12h 30min', 'service-booking' ); ?></option>
                                    <option value="13" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '13' ) : ''; ?>><?php esc_html_e( '13h', 'service-booking' ); ?></option>
                                    <option value="13.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '13.5' ) : ''; ?>><?php esc_html_e( '13h 30min', 'service-booking' ); ?></option>
                                    <option value="14" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '14' ) : ''; ?>><?php esc_html_e( '14h', 'service-booking' ); ?></option>
                                    <option value="14.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '14.5' ) : ''; ?>><?php esc_html_e( '14h 30min', 'service-booking' ); ?></option>
                                    <option value="15" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '15' ) : ''; ?>><?php esc_html_e( '15h', 'service-booking' ); ?></option>
                                    <option value="15.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '15.5' ) : ''; ?>><?php esc_html_e( '15h 30min', 'service-booking' ); ?></option>
                                    <option value="16" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '16' ) : ''; ?>><?php esc_html_e( '16h', 'service-booking' ); ?></option>
                                    <option value="16.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '16.5' ) : ''; ?>><?php esc_html_e( '16h 30min', 'service-booking' ); ?></option>
                                    <option value="17" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '17' ) : ''; ?>><?php esc_html_e( '17h', 'service-booking' ); ?></option>
                                    <option value="17.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '17.5' ) : ''; ?>><?php esc_html_e( '17h 30min', 'service-booking' ); ?></option>
                                    <option value="18" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '18' ) : ''; ?>><?php esc_html_e( '18h', 'service-booking' ); ?></option>
                                    <option value="18.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '18.5' ) : ''; ?>><?php esc_html_e( '18h 30min', 'service-booking' ); ?></option>
                                    <option value="19" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '19' ) : ''; ?>><?php esc_html_e( '19h', 'service-booking' ); ?></option>
                                    <option value="19.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '19.5' ) : ''; ?>><?php esc_html_e( '19h 30min', 'service-booking' ); ?></option>
                                    <option value="20" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '20' ) : ''; ?>><?php esc_html_e( '20h', 'service-booking' ); ?></option>
                                    <option value="20.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '20.5' ) : ''; ?>><?php esc_html_e( '20h 30min', 'service-booking' ); ?></option>
                                    <option value="21" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '21' ) : ''; ?>><?php esc_html_e( '21h', 'service-booking' ); ?></option>
                                    <option value="21.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '21.5' ) : ''; ?>><?php esc_html_e( '21h 30min', 'service-booking' ); ?></option>
                                    <option value="22" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '22' ) : ''; ?>><?php esc_html_e( '22h', 'service-booking' ); ?></option>
                                    <option value="22.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '22.5' ) : ''; ?>><?php esc_html_e( '22h 30min', 'service-booking' ); ?></option>
                                    <option value="23" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '23' ) : ''; ?>><?php esc_html_e( '23h', 'service-booking' ); ?></option>
                                    <option value="23.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '23.5' ) : ''; ?>><?php esc_html_e( '23h 30min', 'service-booking' ); ?></option>
                                    <option value="24" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_duration ) ? selected( esc_attr( $sv_extra_row->extra_duration ), '24' ) : ''; ?>><?php esc_html_e( '24h', 'service-booking' ); ?></option>
                                </select>
                                <div class="errortext"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="svc_extra_operation"><?php esc_html_e( 'Total Operation Time (in hrs)', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                            <td class="bminput bm_ex_required">
                                <select name="svc_extra_operation" id="svc_extra_operation" class="regular-text">
                                    <option value=""><?php esc_html_e( 'Select Total Operation Time', 'service-booking' ); ?></option>
                                    <option value="0.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '0.5' ) : ''; ?>><?php esc_html_e( '30min', 'service-booking' ); ?></option>
                                    <option value="1" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '1' ) : ''; ?>><?php esc_html_e( '1h', 'service-booking' ); ?></option>
                                    <option value="1.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '1.5' ) : ''; ?>><?php esc_html_e( '1h 30min', 'service-booking' ); ?></option>
                                    <option value="2" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '2' ) : ''; ?>><?php esc_html_e( '2h', 'service-booking' ); ?></option>
                                    <option value="2.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '2.5' ) : ''; ?>><?php esc_html_e( '2h 30min', 'service-booking' ); ?></option>
                                    <option value="3" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '3' ) : ''; ?>><?php esc_html_e( '3h', 'service-booking' ); ?></option>
                                    <option value="3.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '3.5' ) : ''; ?>><?php esc_html_e( '3h 30min', 'service-booking' ); ?></option>
                                    <option value="4" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '4' ) : ''; ?>><?php esc_html_e( '4h', 'service-booking' ); ?></option>
                                    <option value="4.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '4.5' ) : ''; ?>><?php esc_html_e( '4h 30min', 'service-booking' ); ?></option>
                                    <option value="5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '5' ) : ''; ?>><?php esc_html_e( '5h', 'service-booking' ); ?></option>
                                    <option value="5.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '5.5' ) : ''; ?>><?php esc_html_e( '5h 30min', 'service-booking' ); ?></option>
                                    <option value="6" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '6' ) : ''; ?>><?php esc_html_e( '6h', 'service-booking' ); ?></option>
                                    <option value="6.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '6.5' ) : ''; ?>><?php esc_html_e( '6h 30min', 'service-booking' ); ?></option>
                                    <option value="7" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '7' ) : ''; ?>><?php esc_html_e( '7h', 'service-booking' ); ?></option>
                                    <option value="7.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '7.5' ) : ''; ?>><?php esc_html_e( '7h 30min', 'service-booking' ); ?></option>
                                    <option value="8" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '8' ) : ''; ?>><?php esc_html_e( '8h', 'service-booking' ); ?></option>
                                    <option value="8.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '8.5' ) : ''; ?>><?php esc_html_e( '8h 30min', 'service-booking' ); ?></option>
                                    <option value="9" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '9' ) : ''; ?>><?php esc_html_e( '9h', 'service-booking' ); ?></option>
                                    <option value="9.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '9.5' ) : ''; ?>><?php esc_html_e( '9h 30min', 'service-booking' ); ?></option>
                                    <option value="10" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '10' ) : ''; ?>><?php esc_html_e( '10h', 'service-booking' ); ?></option>
                                    <option value="10.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '10.5' ) : ''; ?>><?php esc_html_e( '10h 30min', 'service-booking' ); ?></option>
                                    <option value="11" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '11' ) : ''; ?>><?php esc_html_e( '11h', 'service-booking' ); ?></option>
                                    <option value="11.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '11.5' ) : ''; ?>><?php esc_html_e( '11h 30min', 'service-booking' ); ?></option>
                                    <option value="12" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '12' ) : ''; ?>><?php esc_html_e( '12h', 'service-booking' ); ?></option>
                                    <option value="12.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '12.5' ) : ''; ?>><?php esc_html_e( '12h 30min', 'service-booking' ); ?></option>
                                    <option value="13" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '13' ) : ''; ?>><?php esc_html_e( '13h', 'service-booking' ); ?></option>
                                    <option value="13.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '13.5' ) : ''; ?>><?php esc_html_e( '13h 30min', 'service-booking' ); ?></option>
                                    <option value="14" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '14' ) : ''; ?>><?php esc_html_e( '14h', 'service-booking' ); ?></option>
                                    <option value="14.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '14.5' ) : ''; ?>><?php esc_html_e( '14h 30min', 'service-booking' ); ?></option>
                                    <option value="15" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '15' ) : ''; ?>><?php esc_html_e( '15h', 'service-booking' ); ?></option>
                                    <option value="15.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '15.5' ) : ''; ?>><?php esc_html_e( '15h 30min', 'service-booking' ); ?></option>
                                    <option value="16" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '16' ) : ''; ?>><?php esc_html_e( '16h', 'service-booking' ); ?></option>
                                    <option value="16.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '16.5' ) : ''; ?>><?php esc_html_e( '16h 30min', 'service-booking' ); ?></option>
                                    <option value="17" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '17' ) : ''; ?>><?php esc_html_e( '17h', 'service-booking' ); ?></option>
                                    <option value="17.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '17.5' ) : ''; ?>><?php esc_html_e( '17h 30min', 'service-booking' ); ?></option>
                                    <option value="18" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '18' ) : ''; ?>><?php esc_html_e( '18h', 'service-booking' ); ?></option>
                                    <option value="18.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '18.5' ) : ''; ?>><?php esc_html_e( '18h 30min', 'service-booking' ); ?></option>
                                    <option value="19" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '19' ) : ''; ?>><?php esc_html_e( '19h', 'service-booking' ); ?></option>
                                    <option value="19.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '19.5' ) : ''; ?>><?php esc_html_e( '19h 30min', 'service-booking' ); ?></option>
                                    <option value="20" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '20' ) : ''; ?>><?php esc_html_e( '20h', 'service-booking' ); ?></option>
                                    <option value="20.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '20.5' ) : ''; ?>><?php esc_html_e( '20h 30min', 'service-booking' ); ?></option>
                                    <option value="21" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '21' ) : ''; ?>><?php esc_html_e( '21h', 'service-booking' ); ?></option>
                                    <option value="21.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '21.5' ) : ''; ?>><?php esc_html_e( '21h 30min', 'service-booking' ); ?></option>
                                    <option value="22" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '22' ) : ''; ?>><?php esc_html_e( '22h', 'service-booking' ); ?></option>
                                    <option value="22.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '22.5' ) : ''; ?>><?php esc_html_e( '22h 30min', 'service-booking' ); ?></option>
                                    <option value="23" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '23' ) : ''; ?>><?php esc_html_e( '23h', 'service-booking' ); ?></option>
                                    <option value="23.5" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '23.5' ) : ''; ?>><?php esc_html_e( '23h 30min', 'service-booking' ); ?></option>
                                    <option value="24" <?php isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_operation ) ? selected( esc_attr( $sv_extra_row->extra_operation ), '24' ) : ''; ?>><?php esc_html_e( '24h', 'service-booking' ); ?></option>
                                </select>
                                <div class="errortext"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="svc_extra_price"><?php echo sprintf( esc_html__( 'Price (in %s)', 'service-booking' ), esc_html( $currency_symbol ) ); ?></label><strong class="required_asterisk"> *</strong></th>
                            <td class="bminput bm_ex_required">
                                <input name="svc_extra_price" type="text" id="svc_extra_price" value="<?php echo isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_price ) ? esc_html( $sv_extra_row->extra_price ) : ''; ?>" placeholder="<?php esc_html_e( 'price', 'service-booking' ); ?>" class="regular-text" autocomplete="off">
                                <div class="errortext"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="svc_extra_max_cap"><?php esc_html_e( 'Maximum Capacity', 'service-booking' ); ?></label></th>
                            <td><input name="svc_extra_max_cap" type="number" min="1" id="svc_extra_max_cap" value="<?php echo isset( $sv_extra_row ) && !empty( $sv_extra_row->extra_max_cap ) ? esc_html( $sv_extra_row->extra_max_cap ) : ''; ?>" placeholder="<?php esc_html_e( 'maximum capacity', 'service-booking' ); ?>" class="regular-text" autocomplete="off"></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Global ?', 'service-booking' ); ?></th>
                            <td class="bm-checkbox-td">
                                <input name="is_global" type="checkbox" id="is_global" class="regular-text bm_toggle" <?php isset( $sv_extra_row ) ?  checked( $sv_extra_row->is_global, 1 ) : ''; ?>>
                                <label for="is_global"></label>
                                <span class="info_text" style="margin-left:0px;margin-top:5px;">
                                    <?php esc_html_e( '( If Global, this extra will be available for all services )', 'service-booking' ); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php esc_html_e( 'Is visible in frontend ?', 'service-booking' ); ?></th>
                            <td class="bm-checkbox-td">
                                <input name="is_extra_service_front" type="hidden" value="0">
                                <input name="is_extra_service_front" type="checkbox" id="is_extra_service_front" class="regular-text bm_toggle" value="1" 
                                <?php
                                if ( ! isset( $sv_extra_row ) ) {
                                    echo 'checked';
                                }
                                ?>
                                <?php isset( $sv_extra_row->is_extra_service_front ) ?  checked( $sv_extra_row->is_extra_service_front, 1 ) : ''; ?>>
                                <label for="is_extra_service_front"></label>
                                <span class="info_text" style="margin-left:0px;margin-top:5px;">
                                    <?php esc_html_e( '( If ON, this extra will be visible in frontend )', 'service-booking' ); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="svc_extra_desc"><?php esc_html_e( 'Description', 'service-booking' ); ?></label></th>
                            <td>
                                <div style='width :100%' class="sg-rg-buttom">
                                    <?php isset( $sv_extra_row ) && isset( $sv_extra_row->extra_desc ) ? wp_editor( $sv_extra_row->extra_desc, 'svc_extra_desc', $service_extra_desc_settings ) : wp_editor( '', 'svc_extra_desc', $service_extra_desc_settings ); ?>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="is_linked_wc_extrasvc"><?php esc_html_e( 'Link WooCommerce Product ?', 'service-booking' ); ?></label></th>
                            <td class="bm-checkbox-td">
                                <input name="is_linked_wc_extrasvc" type="checkbox" id="is_linked_wc_extrasvc" class="regular-text bm_toggle" <?php isset( $sv_extra_row ) && isset( $sv_extra_row->is_linked_wc_extrasvc ) ? checked( esc_attr( $sv_extra_row->is_linked_wc_extrasvc ), 1 ) : ''; ?> onclick="bm_open_close_tab('wc_svcextra_products_section')">
                                <label for="is_linked_wc_extrasvc"></label>
                            </td>
                        </tr>
                        <tr id="wc_svcextra_products_section" style="<?php echo !isset( $sv_extra_row ) || $sv_extra_row->is_linked_wc_extrasvc == 0 ? 'display: none' : ''; ?>">
                            <th scope="row"><label for="wc_product"><?php esc_html_e( 'WooCommerce Product', 'service-booking' ); ?></label></th>
                            <td id="svcextra_products_section">
                                <?php
                                if ( isset( $wc_products ) ) {
                                    if ( $wc_products->have_posts() ) {
                                        ?>
                                        <select name="svcextra_wc_product" id="svcextra_wc_product" class="regular-text">
                                            <option value=""><?php esc_html_e( 'Select WooCommerce Product', 'service-booking' ); ?></option>
                                            <?php
                                            while ( $wc_products->have_posts() ) {
                                                $wc_products->the_post();
                                                $product = wc_get_product( get_the_ID() );

                                                if ( $product ) {
                                                    ?>
                                                    <option value="<?php echo esc_attr( $product->get_id() ); ?>" 
                                                        <?php echo ( isset( $sv_extra_row ) && isset( $sv_extra_row->svcextra_wc_product ) ) ? selected( esc_attr( $sv_extra_row->svcextra_wc_product ), esc_attr( $product->get_id() ), false ) : ''; ?>>
                                                        <?php echo esc_html( $product->get_title() ); ?>
                                                    </option>
                                                    <?php
                                                }
                                            }
                                            wp_reset_postdata();
                                            ?>
                                        </select>
                                        <?php
                                    } else {
                                        ?>
                                        <p><?php esc_html_e( 'WooCommerce Products not available !!', 'service-booking' ); ?> &nbsp;&nbsp;
                                            <a href="<?php echo esc_url_raw( admin_url( 'post-new.php?post_type=product' ) ); ?>" target="_blank" class="button button-secondary">
                                                <?php esc_html_e( 'Add Product', 'service-booking' ); ?>
                                            </a>
                                        </p>
                                        <?php
                                    }
                                } else {
									?>
                                    <p><?php esc_html_e( 'WooCommerce Plugin is not installed/enabled !!', 'service-booking' ); ?> &nbsp;&nbsp;<a href="<?php echo esc_url_raw( get_site_url() . '/wp-admin/update.php?action=install-plugin&plugin=woocommerce&_wpnonce=f86b375973' ); ?>" target="_blank" class="button button-secondary"><?php esc_html_e( 'Add Plugin', 'service-booking' ); ?></a></p>
                                <?php } ?>
                                <div class="errortext"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cancel_extra"></label></th>
                            <td>
                                <?php
                                if ( !isset( $sv_extra_row ) ) {
                                    if ( esc_attr( $id ) != 0 ) {
										?>
                                        <input type="submit" name="savesvc_extra" id="savesvc_extra" class="button button-primary" value="<?php esc_attr_e( 'Save Extra', 'service-booking' ); ?>" onClick="return add_form_validation('extra')">
                                    <?php } ?>
                                    <a href="javascript:void(0)" name="cancel_extra" id="cancel_extra" class="button button-secondary"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;<i class="fa fa-times" aria-hidden="true" style="color: brown;"></i></a>
                                <?php } elseif ( isset( $sv_extra_row ) ) { ?>
                                    <input type="submit" name="upsvc_extra" id="upsvc_extra" class="button button-primary" value="<?php esc_attr_e( 'Update Extra', 'service-booking' ); ?>" onClick="return add_form_validation('extra')">
                                    <input type="submit" name="cancel_upsvc_extra" id="cancel_upsvc_extra" onclick="extraUpdate()" class="button button-secondary" value="<?php esc_attr_e( 'Cancel', 'service-booking' ); ?>">
                                <?php } ?>
                            </td>
                        </tr>
                    </table>

                        <?php if ( isset( $total_extra_rows ) ) { ?>
                            <table class="wp-list-table widefat striped" id="existing_extra_content" style="<?php echo esc_attr( $extra_id ) != 0 ? 'display: none' : ''; ?>">
                                <thead>
                                    <tr>
                                        <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                                        <th style="text-align: center;font-weight: 600;"><?php echo sprintf( esc_html__( 'Price (in %s)', 'service-booking' ), esc_html( $currency_symbol ) ); ?></th>
                                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Is visible in frontend ?', 'service-booking' ); ?></th>
                                        <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    foreach ( $total_extra_rows as $extra_field ) {
										?>
                                        <form role="form" method="post">
                                            <tr>
                                                <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                                                <td style="text-align: center;"><?php echo !empty( $extra_field ) ? esc_html( $extra_field->extra_name ) : ''; ?></td>
                                                <td style="text-align: center;"><?php echo !empty( $extra_field ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $extra_field->extra_price, true ) ) : ''; ?></td>
                                                <td style="text-align: center;" class="bm-checkbox-td">
                                                    <input name="bm_show_extra_service_in_front" type="checkbox" id="bm_show_extra_service_in_front_<?php echo esc_attr( $extra_field->id ); ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $extra_field->is_extra_service_front ), '1' ); ?> onchange="bm_change_extra_service_visibility(this)">
                                                    <label for="bm_show_extra_service_in_front_<?php echo esc_attr( $extra_field->id ); ?>"></label>
                                                </td>
                                                <td style="text-align: center;">
                                                    <input type="hidden" name="svc_extra_id" value="<?php echo isset( $extra_field->id ) ? esc_attr( $extra_field->id ) : ''; ?>">
                                                    <button type="submit" name="editsvc_extra" class="edit-button" id="editsvc_extra" value="<?php esc_html_e( 'Edit', 'service-booking' ); ?>"><i class="fa fa-pencil" aria-hidden="true"></i></button>
                                                    <button type="submit" name="delsvc_extra" class="delete-button" id="delsvc_extra" onclick="extraUpdate()" value="<?php esc_html_e( 'Delete', 'service-booking' ); ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                                                </td>
                                            </tr>
                                        </form>
                                </tbody>
										<?php
                                        $i++;
                                    }
									?>
                            </table>
                        <?php } ?>

                    </table>
                </div>

                <input type="hidden" id="has_variable_price_module" value="0">
                <div id="price_calendar" class="tabcontent">
                    <table class="form-table" role="presentation">
                        <tr>
                            <td>
                                <div style="width:62%; float: left;">
                                    <label for="price_datepicker"><?php esc_html_e( 'Prices', 'service-booking' ); ?></label>
                                    <br />
                                    <br />

                                    <div class="booking-status">
                                        <div class="booking-statusinnerbox">
                                            <div class="status-box">
                                                <div class="available_for_booking"></div>
                                                <span><?php esc_html_e( 'Available', 'service-booking' ); ?></span>
                                            </div>
                                            <div class="status-box">
                                                <div class="not_available_for_booking"></div>
                                                <span><?php esc_html_e( 'Unavailable', 'service-booking' ); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <br />
                                    <br />

                                    <div id="price_datepicker">
                                    </div>
                                </div>
                                <div style="width: 33%; float: left; margin-left: 3%; margin-top: 2%;">
                                    <div id="svc_price_modal" class="rightbox" style="display: none;">
                                        <table class="form-table" role="presentation">
                                            <tr>
                                                <th scope="row"><?php esc_html_e( 'Bulk Price Change ?', 'service-booking' ); ?></th>
                                                <td class="bm-checkbox-td">
                                                    <input name="bulk_price_change" type="checkbox" id="bulk_price_change" class="regular-text bm_toggle" onclick="bm_open_close_tab('calendar_bulk_price_change')">
                                                    <label for="bulk_price_change"></label>
                                                </td>
                                            </tr>

                                            <tr id="single_price_change">

                                                <td colspan="2" style="padding:0px;">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;"><label for="variable_price"><?php esc_html_e( 'Set Price', 'service-booking' ); ?></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Date', 'service-booking' ); ?></td>
                                                                <td> <input type="date" name="variable_date" id="variable_date" style="width:180px; max-width:145px !important;" readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Price', 'service-booking' ); ?></td>
                                                                <td><input name="variable_price" type="text" id="variable_price" style="width:180px; max-width:145px !important;" placeholder="<?php esc_html_e( 'price', 'service-booking' ); ?>" autocomplete="off">
                                                                    <select name="variable_external_price_module" id="variable_external_price_module" style="width:180px;display:none; max-width:145px !important;">
                                                                        <?php
                                                                        if ( !empty( $price_modules ) ) {
                                                                            foreach ( $price_modules as $price_module ) {
																				?>
                                                                                <option value="<?php echo esc_attr( $price_module->id ) ?? ''; ?>"><?php echo esc_html( $price_module->module_name ) ?? ''; ?></option>
																				<?php
                                                                            }
                                                                        } else {
                                                                            ?>
                                                                            <option value=""><?php esc_html_e( 'No Price Modules Found', 'service-booking' ); ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td colspan="2">


                                                                    <i class="fa fa-refresh fa-spin bm-set_price-spiner" style="display: none;"></i>
                                                                    <input name="link_external_price_module" type="checkbox" id="link_external_price_module" class="regular-text bm_toggle" onclick="bm_open_close_tab('variable_external_price_module')">

                                                                    <?php esc_html_e( 'Link Price Module ?', 'service-booking' ); ?>
                                                                    <span class="variable_errortext"></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <input type="button" name="up_svc_price" id="up_svc_price" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="variable_price_validation_submit()">
                                                                    <input type="button" name="up_svc_price_module" id="up_svc_price_module" class="button button-primary" style="display:none;" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="variable_price_module_validation_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_svc_price" id="cancel_svc_price" class="button button-secondary" onclick="bm_open_close_tab('svc_price_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>


                                            <tr id="calendar_bulk_price_change" style="display: none;">
                                                <td colspan="2" style="padding:0px;">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;">
                                                                    <label for="bulk_price_change"><?php esc_html_e( 'Set Price', 'service-booking' ); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'From', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_bm_required">
                                                                        <input type="date" name="from_bulk_price_change" id="from_bulk_price_change" style="width:180px; max-width:145px !important;" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>" onchange="showToDate('price')" autocomplete="off">
                                                                        <span class="bulk_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'To', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_bm_required">
                                                                        <input type="date" name="to_bulk_price_change" id="to_bulk_price_change" style="width:180px; max-width:145px !important;" autocomplete="off" readonly>
                                                                        <span class="bulk_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Price', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_bm_required">
                                                                        <input type="text" name="bulk_variable_price" id="bulk_variable_price" style="width:180px; max-width:145px !important;" placeholder="<?php echo esc_html_e( 'please enter price', 'service-booking' ); ?>" autocomplete="off">
                                                                        <span class="bulk_errortext"></span>
                                                                    </span>
                                                                    <span class="bulk_bm_required">
                                                                        <select name="bulk_variable_external_price_module" id="bulk_variable_external_price_module" style="width:180px;max-width:145px !important;display:none;">
                                                                            <?php
                                                                            if ( !empty( $price_modules ) ) {
                                                                                foreach ( $price_modules as $price_module ) {
																					?>
                                                                                    <option value="<?php echo esc_attr( $price_module->id ) ?? ''; ?>"><?php echo esc_html( $price_module->module_name ) ?? ''; ?></option>
																					<?php
                                                                                }
                                                                            } else {
                                                                                ?>
                                                                                <option value=""><?php esc_html_e( 'No Price Modules Found', 'service-booking' ); ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <span class="bulk_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>

                                                                <td colspan="2">

                                                                    <input name="bulk_link_external_price_module" type="checkbox" id="bulk_link_external_price_module" class="regular-text bm_toggle" onclick="bm_open_close_tab('bulk_variable_external_price_module')">
                                                                    <?php esc_html_e( 'Link Price Module ?', 'service-booking' ); ?>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <input type="button" name="up_bulk_svc_price" id="up_bulk_svc_price" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="bulk_price_validation_submit()">
                                                                    <input type="button" name="up_bulk_vc_price_module" id="up_bulk_vc_price_module" class="button button-primary" style="display:none;" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="bulk_variable_price_module_validation_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_svc_price" id="cancel_svc_price" class="button button-secondary" onclick="bm_open_close_tab('svc_price_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>&nbsp;&nbsp;
                                </div>
                                <div class="calendar_errortext"></div>
                                <div class="price_update_errortext"></div>
                                <div class="price_update_successtext"></div>
                                <div id="svc_calendar_date_inputs" style="display: none;"></div>
                                <div id="svc_calendar_price_inputs" style="display: none;"></div>
                                <div id="svc_calendar_module_date_inputs" style="display: none;"></div>
                                <div id="svc_calendar_module_inputs" style="display: none;"></div>
                            </td>
                        </tr>
                    </table>
                </div>


                <div id="stopsales_calendar" class="tabcontent">
                    <table class="form-table" role="presentation">
                        <tr>

                            <td>
                                <div style="width:62%; float: left;">
                                    <label for="stopsales_datepicker"><?php esc_html_e( 'Stopsales', 'service-booking' ); ?></label>
                                    <br />
                                    <br />

                                    <div class="booking-status">
                                        <div class="booking-statusinnerbox">
                                            <div class="status-box">
                                                <div class="available_for_booking"></div>
                                                <span><?php esc_html_e( 'Available', 'service-booking' ); ?></span>
                                            </div>
                                            <div class="status-box">
                                                <div class="not_available_for_booking"></div>
                                                <span><?php esc_html_e( 'Unavailable', 'service-booking' ); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <br />
                                    <br />

                                    <div id="stopsales_datepicker">
                                    </div>
                                </div>

                                <div style="width: 33%; float: left; margin-left: 3%; margin-top: 2%;">
                                    <div id="stopsales_modal" class="rightbox" style="display: none;">

                                        <table class="form-table" role="presentation">
                                            <tr>
                                                <th scope="row"><?php esc_html_e( 'Bulk Stopsales Change ?', 'service-booking' ); ?></th>
                                                <td class="bm-checkbox-td">
                                                    <input name="bulk_hour_change" type="checkbox" id="bulk_hour_change" class="regular-text bm_toggle" onclick="bm_open_close_tab('calendar_bulk_hour_change')">
                                                    <label for="bulk_hour_change"></label>
                                                </td>
                                            </tr>
                                            <tr id="single_hour_change">

                                                <td colspan="2" style="padding:0px">
                                                    <div>


                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;"><label for="variable_hour"><?php esc_html_e( 'Set Stopsales hour', 'service-booking' ); ?></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Date', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <input type="date" name="variable_stopsales_date" id="variable_stopsales_date" readonly>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Stop Sales', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <select name="variable_hour" id="variable_hour">
                                                                        <option value="<?php echo esc_attr( 0 ); ?>"><?php esc_html_e( 'No Stopsales', 'service-booking' ); ?></option>
                                                                        <?php for ( $i = 0.5; $i <= $stopsales_limit; $i += 0.5 ) { ?>
                                                                            <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $bmrequests->bm_convert_float_to_time_string( $i ) ); ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="fa fa-refresh fa-spin bm-set_stopsales-spiner" style="display: none;"></i>&nbsp;&nbsp;
                                                                    <span class="variable_hour_errortext"></span>

                                                                    <input type="button" name="up_svc_hour" id="up_svc_hour" class="button button-primary" style="margin-left:-10px;" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="variable_stopsales_validation_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_svc_stopsales" id="cancel_svc_stopsales" class="button button-secondary" onclick="bm_open_close_tab('stopsales_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>



                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="calendar_bulk_hour_change" style="display: none;">

                                                <td colspan="2" style="padding:0px">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;">
                                                                    <label for="bulk_stopsales_change"><?php esc_html_e( 'Set Stopsales hour', 'service-booking' ); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'From', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_stopsales_bm_required">
                                                                        <input type="date" name="from_bulk_stopsales_change" id="from_bulk_stopsales_change" style="width:180px;max-width:145px !important;" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>" onchange="showToDate('stopsales')" autocomplete="off">
                                                                        <span class="bulk_stopsales_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'To', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_stopsales_bm_required">
                                                                        <input type="date" name="to_bulk_stopsales_change" id="to_bulk_stopsales_change" style="width:180px; max-width:145px !important;" autocomplete="off" readonly>
                                                                        <span class="bulk_stopsales_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Stop Sales', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_stopsales_bm_required">
                                                                        <select name="bulk_variable_hour" id="bulk_variable_hour" style="width:180px; max-width:145px !important;">
                                                                            <option value=""><?php esc_html_e( 'No Stopsales', 'service-booking' ); ?></option>
                                                                            <?php for ( $i = 0.5; $i <= $stopsales_limit; $i += 0.5 ) { ?>
                                                                                <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $bmrequests->bm_convert_float_to_time_string( $i ) ); ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <span class="bulk_stopsales_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="fa fa-refresh fa-spin bm-set_stopsales-spiner" style="display: none;"></i>
                                                                    <input type="button" name="up_bulk_svc_hour" id="up_bulk_svc_hour" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="bulk_stopsales_validation_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_svc_stopsales" id="cancel_svc_stopsales" class="button button-secondary" onclick="bm_open_close_tab('stopsales_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>



                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>&nbsp;&nbsp;
                                </div>

                                <div class="stopsales_errortext"></div>
                                <div class="stopsales_update_errortext"></div>
                                <div class="stopsales_update_successtext"></div>
                                <div id="stopsales_calendar_date_inputs" style="display: none;"></div>
                                <div id="stopsales_calendar_hour_inputs" style="display: none;"></div>
                                <div id="stopsales_calendar_exclude_date_inputs" style="display: none;"></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="saleswitch_calendar" class="tabcontent">
                    <table class="form-table" role="presentation">
                        <tr>

                            <td>
                                <div style="width:62%; float: left;">
                                    <label for="saleswitch_datepicker"><?php esc_html_e( 'Saleswitch', 'service-booking' ); ?></label>
                                    <br />
                                    <br />

                                    <div class="booking-status">
                                        <div class="booking-statusinnerbox">
                                            <div class="status-box">
                                                <div class="available_for_booking"></div>
                                                <span><?php esc_html_e( 'Available', 'service-booking' ); ?></span>
                                            </div>
                                            <div class="status-box">
                                                <div class="not_available_for_booking"></div>
                                                <span><?php esc_html_e( 'Unavailable', 'service-booking' ); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <br />
                                    <br />

                                    <div id="saleswitch_datepicker">
                                    </div>
                                </div>

                                <div style="width: 33%; float: left; margin-left: 3%; margin-top: 2%;">
                                    <div id="saleswitch_modal" class="rightbox" style="display: none;">

                                        <table class="form-table" role="presentation">
                                            <tr>
                                                <th scope="row"><?php esc_html_e( 'Bulk Saleswitch Change ?', 'service-booking' ); ?></th>
                                                <td class="bm-checkbox-td">
                                                    <input name="bulk_saleswitch_change" type="checkbox" id="bulk_saleswitch_change" class="regular-text bm_toggle" onclick="bm_open_close_tab('calendar_bulk_saleswitch_change')">
                                                    <label for="bulk_saleswitch_change"></label>
                                                </td>
                                            </tr>
                                            <tr id="single_saleswitch_change">

                                                <td colspan="2" style="padding:0px">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;">
                                                                    <label for="variable_saleswitch_hour"><?php esc_html_e( 'Set Saleswitch hour', 'service-booking' ); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Date', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <input type="date" name="variable_saleswitch_date" id="variable_saleswitch_date" readonly>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Sale Switch', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <select name="variable_saleswitch_hour" id="variable_saleswitch_hour">
                                                                        <option value="<?php echo esc_attr( 0 ); ?>"><?php esc_html_e( 'No Saleswicth', 'service-booking' ); ?></option>
                                                                        <?php for ( $i = 0.5; $i <= $saleswitch_limit; $i += 0.5 ) { ?>
                                                                            <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $bmrequests->bm_convert_float_to_time_string( $i ) ); ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="fa fa-refresh fa-spin bm-set_saleswitch-spiner" style="display: none;"></i>&nbsp;&nbsp;
                                                                    <span class="variable_saleswitch_errortext"></span>

                                                                    <input type="button" name="up_svc_saleswitch" id="up_svc_saleswitch" class="button button-primary" style="margin-left:-10px;" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="variable_saleswitch_validation_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_svc_saleswitch" id="cancel_svc_saleswitch" class="button button-secondary" onclick="bm_open_close_tab('saleswitch_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>


                                                    </div>
                                                </td>
                                            </tr>
                                            <tr id="calendar_bulk_saleswitch_change" style="display: none;">

                                                <td colspan="2" style="padding:0px">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;">
                                                                    <label for="bulk_saleswitch_change"><?php esc_html_e( 'Set Saleswitch hour', 'service-booking' ); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'From', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_saleswitch_bm_required">
                                                                        <input type="date" name="from_bulk_saleswitch_change" id="from_bulk_saleswitch_change" style="width:180px; max-width:145px !important;" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>" onchange="showToDate('saleswitch')" autocomplete="off">
                                                                        <span class="bulk_saleswitch_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'To', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_saleswitch_bm_required">
                                                                        <input type="date" name="to_bulk_saleswitch_change" id="to_bulk_saleswitch_change" style="width:180px; max-width:145px !important;" autocomplete="off" readonly>
                                                                        <span class="bulk_saleswitch_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Sale Switch', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_saleswitch_bm_required">
                                                                        <select name="bulk_saleswitch_hour" id="bulk_saleswitch_hour" style="width:180px; max-width:145px !important;">
                                                                            <option value=""><?php esc_html_e( 'No Saleswitch', 'service-booking' ); ?></option>
                                                                            <?php for ( $i = 0.5; $i <= $saleswitch_limit; $i += 0.5 ) { ?>
                                                                                <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( $bmrequests->bm_convert_float_to_time_string( $i ) ); ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                        <span class="bulk_saleswitch_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="fa fa-refresh fa-spin bm-set_saleswitch-spiner" style="display: none;"></i>
                                                                    <input type="button" name="up_bulk_saleswitch_hour" id="up_bulk_saleswitch_hour" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="bulk_saleswitch_validation_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_svc_saleswitch" id="cancel_svc_saleswitch" class="button button-secondary" onclick="bm_open_close_tab('saleswitch_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>&nbsp;&nbsp;
                                </div>

                                <div class="saleswitch_errortext"></div>
                                <div class="saleswitch_update_errortext"></div>
                                <div class="saleswitch_update_successtext"></div>
                                <div id="saleswitch_calendar_date_inputs" style="display: none;"></div>
                                <div id="saleswitch_calendar_hour_inputs" style="display: none;"></div>
                                <div id="saleswitch_calendar_exclude_date_inputs" style="display: none;"></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="capacity_calendar" class="tabcontent">
                    <table class="form-table" role="presentation">
                        <tr>
                            <td colspan="2">

                                <div style="width:62%; float: left;">
                                    <label for="cap_datepicker"><?php esc_html_e( 'Default Max Capacity', 'service-booking' ); ?></label>
                                    <br />
                                    <br />

                                    <div class="booking-status">
                                        <div class="booking-statusinnerbox">
                                            <div class="status-box">
                                                <div class="available_for_booking"></div>
                                                <span><?php esc_html_e( 'Available', 'service-booking' ); ?></span>
                                            </div>
                                            <div class="status-box">
                                                <div class="not_available_for_booking"></div>
                                                <span><?php esc_html_e( 'Unavailable', 'service-booking' ); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <br />
                                    <br />

                                    <div id="cap_datepicker">
                                    </div>
                                </div>

                                <div style="width: 33%; float: left; margin-left: 3%; margin-top: 2%;">
                                    <div id="max_cap_modal" class="rightbox" style="display: none;">
                                        <table class="form-table" role="presentation">
                                            <tr>
                                                <th scope="row"><?php esc_html_e( 'Bulk Capacity Change ?', 'service-booking' ); ?></th>
                                                <td class="bm-checkbox-td">
                                                    <input name="bulk_max_cap_change" type="checkbox" id="bulk_max_cap_change" class="regular-text bm_toggle" onclick="bm_open_close_tab('max_cap_bulk_change')">
                                                    <label for="bulk_max_cap_change"></label>
                                                </td>
                                            </tr>
                                            <tr id="single_max_cap_change">


                                                <td colspan="2" style="padding:0px;">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;"><label for="max_cap_date"><?php esc_html_e( 'Set Capacity', 'service-booking' ); ?></label></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Date', 'service-booking' ); ?></td>
                                                                <td><input type="date" name="max_cap_date" id="max_cap_date" style="width:180px; max-width:145px !important;" readonly></td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Capacity', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <input name="max_cap_value" type="number" min="1" id="max_cap_value" placeholder="<?php esc_html_e( 'capacity', 'service-booking' ); ?>" autocomplete="off" style="width:180px; max-width:145px !important;">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <i class="fa fa-refresh fa-spin bm-capacity-spiner" style="display: none;"></i>
                                                                    <span class="max_cap_errortext"></span>
                                                                    <input type="button" name="up_max_cap" id="up_max_cap" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="validate_capacity_and_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_max_cap" id="cancel_max_cap" class="button button-secondary" onclick="bm_open_close_tab('max_cap_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>

                                            </tr>
                                            <tr id="max_cap_bulk_change" style="display: none;">

                                                <td colspan="2" style="padding:0px;">
                                                    <div>
                                                        <table class="insidetablebox">
                                                            <tr>
                                                                <td style="padding-top:0px;">
                                                                    <label for="bulk_max_cap"><?php esc_html_e( 'Set Capacity', 'service-booking' ); ?></label>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'From', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_cap_bm_required">
                                                                        <input type="date" name="from_bulk_cap_change" id="from_bulk_cap_change" style="width:180px; max-width:145px !important;" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>" onchange="showToDate('capacity')" autocomplete="off">
                                                                        <span class="bulk_cap_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'To', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_cap_bm_required">
                                                                        <input type="date" name="to_bulk_cap_change" id="to_bulk_cap_change" style="width:180px; max-width:145px !important;" autocomplete="off" readonly>
                                                                        <span class="bulk_cap_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?php esc_html_e( 'Capacity', 'service-booking' ); ?></td>
                                                                <td>
                                                                    <span class="bulk_cap_bm_required">
                                                                        <input type="number" name="bulk_max_cap" id="bulk_max_cap" min="1" style="width:180px; max-width:145px !important;" placeholder="<?php esc_html_e( 'please enter capacity', 'service-booking' ); ?>" autocomplete="off">
                                                                        <span class="bulk_cap_errortext"></span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <input type="button" name="up_bulk_max_cap" id="up_bulk_max_cap" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="validate_bulk_capacity_and_submit()">
                                                                    <a href="javascript:void(0)" name="cancel_max_cap" id="cancel_max_cap" class="button button-secondary" onclick="bm_open_close_tab('max_cap_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>&nbsp;&nbsp;
                                </div>

                                <div class="capacity_calendar_errortext"></div>
                                <div class="capacity_update_errortext"></div>
                                <div class="capacity_update_successtext"></div>
                                <div id="capacity_calendar_date_inputs" style="display: none;"></div>
                                <div id="capacity_calendar_cap_inputs" style="display: none;"></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="time_slots_calendar" class="tabcontent">
                    <input type="hidden" id="total_variable_slots">
                    <input type="hidden" id="removed_slot_id" value="">
                    <table class="form-table" role="presentation">
                        <tr>
                            <td colspan="2">
                                <div style="width:62%; float: left;">
                                    <label for="time_slots_datepicker"><?php esc_html_e( 'Time Slots', 'service-booking' ); ?></label>
                                    <br />
                                    <br />

                                    <div class="booking-status">
                                        <div class="booking-statusinnerbox">
                                            <div class="status-box">
                                                <div class="available_for_booking"></div>
                                                <span><?php esc_html_e( 'Available', 'service-booking' ); ?></span>
                                            </div>
                                            <div class="status-box">
                                                <div class="not_available_for_booking"></div>
                                                <span><?php esc_html_e( 'Unavailable', 'service-booking' ); ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <br />
                                    <br />

                                    <div id="time_slots_datepicker"></div>
                                </div>

                                <div id="time_slot_modal" style="display: none;">
                                    <table class="form-table" role="presentation" id="time_slot_change">
                                        <tr>
                                            <th scope="row"><label for="time_slot_date"><?php esc_html_e( 'Date', 'service-booking' ); ?></label></th>
                                            <td><input type="date" name="time_slot_date" id="time_slot_date" readonly></td>
                                        </tr>
                                        <tr id="loader_div" style="display :none;">
                                            <th scope="row"><label for="time_slot_value"></label></th>
                                            <td>
                                                <span id="loader_span" class="let1"><?php esc_html_e( 'l', 'service-booking' ); ?></span>
                                                <span id="loader_span" class="let2"><?php esc_html_e( 'o', 'service-booking' ); ?></span>
                                                <span id="loader_span" class="let3"><?php esc_html_e( 'a', 'service-booking' ); ?></span>
                                                <span id="loader_span" class="let4"><?php esc_html_e( 'd', 'service-booking' ); ?></span>
                                                <span id="loader_span" class="let5"><?php esc_html_e( 'i', 'service-booking' ); ?></span>
                                                <span id="loader_span" class="let6"><?php esc_html_e( 'n', 'service-booking' ); ?></span>
                                                <span id="loader_span" class="let7"><?php esc_html_e( 'g', 'service-booking' ); ?></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="time_slot_value"><?php esc_html_e( 'Set Time Slots', 'service-booking' ); ?></label></th>
                                            <td><span id="time_slot_value"></span></td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label for="up_time_slot_data"></label></th>
                                            <td>
                                                <input type="button" name="up_time_slot_data" id="up_time_slot_data" class="button button-primary" value="<?php !isset( $svc_row ) ? esc_html_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="validate_slots_and_submit()">
                                                <a href="javascript:void(0)" name="cancel_time_slot_data" id="cancel_time_slot_data" class="button button-secondary" onclick="bm_open_close_tab('time_slot_modal')"><?php esc_attr_e( 'Cancel', 'service-booking' ); ?>&nbsp;<i class="fa fa-times" aria-hidden="true" style="color: brown;"></i></a>
                                                <i class="fa fa-refresh fa-spin bm-slot-spiner" style="display: none;"></i>
                                                <span class="time_slot_errortext"></span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>&nbsp;&nbsp;

                                <div class="time_slot_calendar_errortext"></div>
                                <div class="time_slot_update_errortext"></div>
                                <div class="time_slot_update_successtext"></div>
                                <div id="time_slot_calendar_date_inputs" style="display: none;"></div>
                                <div id="time_slot_calendar_slot_data" style="display: none;"></div>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="svc_settings_section" class="tabcontent">
                    <table class="form-table" role="presentation">
                        <h3><?php esc_html_e( 'Unavailability on Week Days', 'service-booking' ); ?></h3>
                        <tr>
                            <th scope="row"><label><?php esc_html_e( 'Days of the week when the service is unavailable', 'service-booking' ); ?></label></th>
                            <td>
                                <label><input type="checkbox" name="service_unavailability[weekdays][1]" value="<?php echo esc_attr( '1' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '1', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Monday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="checkbox" name="service_unavailability[weekdays][2]" value="<?php echo esc_attr( '2' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '2', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Tuesday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="checkbox" name="service_unavailability[weekdays][3]" value="<?php echo esc_attr( '3' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '3', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Wednesday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="checkbox" name="service_unavailability[weekdays][4]" value="<?php echo esc_attr( '4' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '4', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Thurday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="checkbox" name="service_unavailability[weekdays][5]" value="<?php echo esc_attr( '5' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '5', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Friday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="checkbox" name="service_unavailability[weekdays][6]" value="<?php echo esc_attr( '6' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '6', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Saturday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                                <label><input type="checkbox" name="service_unavailability[weekdays][7]" value="<?php echo esc_attr( '0' ); ?>" <?php echo isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['weekdays'] ) && in_array( '0', $svc_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Sunday', 'service-booking' ); ?></label>
                            </td>
                        </tr>
                    </table>

                    <!-- <table class="form-table" role="presentation">
                        <h3><?php esc_html_e( 'Unavailability on Specific Dates', 'service-booking' ); ?></h3>
                        <tr class="date_input_tr">
                            <th scope="row"><label for="unavailable_date"><?php esc_html_e( 'Specific dates when the service is unavailable', 'service-booking' ); ?></label></th>
                            <td class="date_option_field">
                                <?php
                                if ( isset( $svc_unavailability ) && !empty( $svc_unavailability ) && isset( $svc_unavailability['dates'] ) && !empty( $svc_unavailability['dates'] ) ) {
                                    $i = 1;
                                    foreach ( $svc_unavailability['dates'] as $unavailable_date ) {
                                        if ( !empty( $unavailable_date ) ) {
                                            $date_name = "service_unavailability[dates][$i]";
                                            $date_id   = "unavailable_date_$i";
											?>
                                            <span class="date_input_span">
                                                <input type="date" id="<?php echo esc_html( $date_id ); ?>" name="<?php echo esc_html( $date_name ); ?>" value="<?php echo esc_html( $unavailable_date ); ?>">
                                                <button type="button" id="svc_date_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="bm_remove_svc_unavailable_date(this)"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                                            </span>
											<?php
                                            $i++;
                                        }
                                    }
                                }
                                ?>
                                <span class="add_dates_button"><button type="button" class="button button-primary" onClick="bm_add_unavailable_date()"><?php esc_html_e( 'add date', 'service-booking' ); ?></button></span>
                            </td>
                        </tr>
                    </table> -->
                    
                    <table class="form-table" role="presentation">
                    <h3><?php esc_html_e( 'Unavailability on Specific Dates', 'service-booking' ); ?></h3>
                    <tr class="date_input_tr">
                        <th scope="row"><label for="unavailable_date_range"><?php esc_html_e( 'Select unavailable date ranges', 'service-booking' ); ?></label></th>
                        <td class="date_option_field">
                            <div id="unavailable_date_ranges">
                                <?php
                                if ( isset( $svc_unavailability['dates'] ) && !empty( $svc_unavailability['dates'] ) ) {
                                    $i = 1;
                                    foreach ( $svc_unavailability['dates'] as $range ) {
                                        if ( !empty( $range ) ) {
                                            $range_name = "service_unavailability[dates][$i]";
                                            $range_id   = "unavailable_date_range_$i";
                                            ?>
                                            <span class="date_range_span">
                                                <input type="text" readonly id="<?php echo esc_html( $range_id ); ?>" name="<?php echo esc_html( $range_name ); ?>" value="<?php echo esc_html( $range ); ?>" class="date_range_input">
                                                <button type="button" class="remove_range" onclick="bm_remove_unavailable_range(this)">✕</button>
                                            </span>
                                            <?php
                                            $i++;
                                        }
                                    }
                                }
                                ?>
                            </div>

                            <input type="text" id="service_date_range_picker" placeholder="<?php esc_attr_e( 'Select date range', 'service-booking' ); ?>" readonly>
                            <button type="button" class="button button-primary" id="add_date_range"><?php esc_html_e( 'Add Range', 'service-booking' ); ?></button>
                        </td>
                    </tr>
                </table>

                <table class="form-table" role="presentation">
                    <h3><?php esc_html_e( 'Frontend Settings', 'service-booking' ); ?></h3>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Show service in frontend ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_service_front" type="checkbox" id="is_service_front" <?php isset( $svc_row ) && isset( $svc_row->is_service_front ) ? checked( esc_attr( $svc_row->is_service_front ), 1 ) : ''; ?> <?php
                            if ( !isset( $svc_row ) ) {
                                echo 'checked';}
                            ?>
                         class="regular-text bm_toggle">
                         <label for="is_service_front"></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Show stopsales info in frontend ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="show_stopsales_data" type="checkbox" id="show_stopsales_data" class="regular-text bm_toggle" <?php isset( $svc_row ) && isset( $svc_row->show_stopsales_data ) ? checked( esc_attr( $svc_row->show_stopsales_data ), 1 ) : ''; ?>>
                            <label for="show_stopsales_data"></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Show duration info in frontend ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="service_settings[show_service_duration]" type="hidden" value="0">
                            <input name="service_settings[show_service_duration]" type="checkbox" id="show_service_duration" class="regular-text bm_toggle" value="1"
                            <?php
                            if ( ! isset( $svc_settings['show_service_duration'] ) ) {
								echo 'checked'; }
							?>
                            <?php isset( $svc_settings['show_service_duration'] ) ? checked( $svc_settings['show_service_duration'], 1 ) : ''; ?>>
                            <label for="show_service_duration"></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Allow to be added as Gift Voucher ?', 'service-booking' ); ?>
                        </th>
                        <td class="bm-checkbox-td" style="width: 31.5%;">
                            <input name="service_settings[allow_as_gift]" type="hidden" value="0">
                            <input name="service_settings[allow_as_gift]" type="checkbox" id="allow_as_gift" class="regular-text bm_toggle" value="1" 
                            <?php
                            if ( ! isset( $svc_settings['allow_as_gift'] ) ) {
								echo 'checked'; }
							?>
                             <?php isset( $svc_settings['allow_as_gift'] ) ? checked( $svc_settings['allow_as_gift'], 1 ) : ''; ?>>
                            <label for="allow_as_gift"></label>
                        </td>
                        <td>
                            <?php esc_html_e( 'Check if you want to allow customers to add this service as a gift in checkout.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Show per person text ?', 'service-booking' ); ?>
                        </th>
                        <td class="bm-checkbox-td" style="width: 31.5%;">
                            <input name="service_settings[show_per_person_text]" type="hidden" value="0">
                            <input name="service_settings[show_per_person_text]" type="checkbox" id="show_per_person_text" class="regular-text bm_toggle" value="1" 
                             <?php isset( $svc_settings['show_per_person_text'] ) ? checked( $svc_settings['show_per_person_text'], 1 ) : ''; ?>>
                            <label for="show_per_person_text"></label>
                        </td>
                        <td>
                            <?php esc_html_e( 'Check if you want to show per person text near service price in service card.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Show cap left text ?', 'service-booking' ); ?>
                        </th>
                        <td class="bm-checkbox-td" style="width: 31.5%;">
                            <input name="service_settings[show_cap_left_text]" type="hidden" value="0">
                            <input name="service_settings[show_cap_left_text]" type="checkbox" id="show_cap_left_text" class="regular-text bm_toggle" value="1" 
                            <?php
                            if ( ! isset( $svc_settings['show_cap_left_text'] ) ) {
								echo 'checked'; }
							?>
                             <?php isset( $svc_settings['show_cap_left_text'] ) ? checked( $svc_settings['show_cap_left_text'], 1 ) : ''; ?>>
                            <label for="show_cap_left_text"></label>
                        </td>
                        <td>
                            <?php esc_html_e( 'Check if you want to show cap left number in service time slot boxes.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Show number of persons select box ?', 'service-booking' ); ?>
                        </th>
                        <td class="bm-checkbox-td" style="width: 31.5%;">
                            <input name="service_settings[show_no_of_persons_box]" type="hidden" value="0">
                            <input name="service_settings[show_no_of_persons_box]" type="checkbox" id="show_no_of_persons_box" class="regular-text bm_toggle" value="1" 
                            <?php
                            if ( ! isset( $svc_settings['show_no_of_persons_box'] ) ) {
								echo 'checked'; }
							?>
                             <?php isset( $svc_settings['show_no_of_persons_box'] ) ? checked( $svc_settings['show_no_of_persons_box'], 1 ) : ''; ?>>
                            <label for="show_no_of_persons_box"></label>
                        </td>
                        <td>
                            <?php esc_html_e( 'Check if you want to show number of persons select box in service time slot modal.', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
                </br>
                <div class="greybox_external_price">
                    <table class="form-table" role="presentation">
                    <h2><?php esc_html_e( 'External Price Module Age Settings (optional)', 'service-booking' ); ?></h2>
                    <tr>
                        <th scope="row"><label for="age_from_1"><?php esc_html_e( 'Infant Group Age Limit', 'service-booking' ); ?></label></th>
                        <td>
                            <span>
                                <b><?php esc_html_e( 'From: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_infant_age_from]" type="number" step="1" min="0" id="age_from_1" style="width:165px;" placeholder="<?php esc_html_e( 'from', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_infant_age_from'] ) ? esc_attr( $svc_options['svc_infant_age_from'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_infant_disable'] ) && ( $svc_options['svc_infant_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <b><?php esc_html_e( 'To: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_infant_age_to]" type="number" step="1" min="" id="age_to_1" style="width:165px;" placeholder="<?php esc_html_e( 'to', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_infant_age_to'] ) ? esc_attr( $svc_options['svc_infant_age_to'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_infant_disable'] ) && ( $svc_options['svc_infant_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <input type="hidden" name="service_options[svc_infant_disable]" value="0">
                                <input type="checkbox" name="service_options[svc_infant_disable]" id="svc_age_disable_1" value="1" onchange="disableAgeGroup(this)" <?php isset( $svc_options ) && isset( $svc_options['svc_infant_disable'] ) ? checked( esc_attr( $svc_options['svc_infant_disable'] ), 1 ) : ''; ?>>&nbsp;<b><?php esc_html_e( 'Disable ?', 'service-booking' ); ?></b>
                            </span>
                            <span class="service-price-module-info_text">
                                <?php esc_html_e( 'If specified and if an external price module is connected to this service, these prices will be considered. If disabled, deafult prices as per the module connected will be considered', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="age_from_2"><?php esc_html_e( 'Children Group Age Limit', 'service-booking' ); ?></label></th>
                        <td>
                            <span>
                                <b><?php esc_html_e( 'From: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_children_age_from]" type="number" step="1" min="0" id="age_from_2" style="width:165px;" placeholder="<?php esc_html_e( 'from', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_children_age_from'] ) ? esc_attr( $svc_options['svc_children_age_from'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_children_disable'] ) && ( $svc_options['svc_children_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <b><?php esc_html_e( 'To: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_children_age_to]" type="number" step="1" min="" id="age_to_2" style="width:165px;" placeholder="<?php esc_html_e( 'to', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_children_age_to'] ) ? esc_attr( $svc_options['svc_children_age_to'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_children_disable'] ) && ( $svc_options['svc_children_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <input type="hidden" name="service_options[svc_children_disable]" value="0">
                                <input type="checkbox" name="service_options[svc_children_disable]" id="svc_age_disable_2" value="1" onchange="disableAgeGroup(this)" <?php isset( $svc_options ) && isset( $svc_options['svc_children_disable'] ) ? checked( esc_attr( $svc_options['svc_children_disable'] ), 1 ) : ''; ?>>&nbsp;<b><?php esc_html_e( 'Disable ?', 'service-booking' ); ?></b>
                            </span>
                            <span class="service-price-module-info_text">
                                <?php esc_html_e( 'If specified and if an external price module is connected to this service, these prices will be considered. If disabled, deafult prices as per the module connected will be considered', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="age_from_3"><?php esc_html_e( 'Adult Group Age Limit', 'service-booking' ); ?></label></th>
                        <td>
                            <span>
                                <b><?php esc_html_e( 'From: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_adult_age_from]" type="number" step="1" min="0" id="age_from_3" style="width:165px;" placeholder="<?php esc_html_e( 'from', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_adult_age_from'] ) ? esc_attr( $svc_options['svc_adult_age_from'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_adult_disable'] ) && ( $svc_options['svc_adult_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <b><?php esc_html_e( 'To: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_adult_age_to]" type="number" step="1" min="" id="age_to_3" style="width:165px;" placeholder="<?php esc_html_e( 'to', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_adult_age_to'] ) ? esc_attr( $svc_options['svc_adult_age_to'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_adult_disable'] ) && ( $svc_options['svc_adult_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <input type="hidden" name="service_options[svc_adult_disable]" value="0">
                                <input type="checkbox" name="service_options[svc_adult_disable]" id="svc_age_disable_3" value="1" onchange="disableAgeGroup(this)" <?php isset( $svc_options ) && isset( $svc_options['svc_adult_disable'] ) ? checked( esc_attr( $svc_options['svc_adult_disable'] ), 1 ) : ''; ?>>&nbsp;<b><?php esc_html_e( 'Disable ?', 'service-booking' ); ?></b>
                            </span>
                            <span class="service-price-module-info_text">
                                <?php esc_html_e( 'If specified and if an external price module is connected to this service, these prices will be considered. If disabled, deafult prices as per the module connected will be considered', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="age_from_4"><?php esc_html_e( 'Senior Group Age Limit', 'service-booking' ); ?></label></th>
                        <td>
                            <span>
                                <b><?php esc_html_e( 'From: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_senior_age_from]" type="number" step="1" min="0" id="age_from_4" style="width:165px;" placeholder="<?php esc_html_e( 'from', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_senior_age_from'] ) ? esc_attr( $svc_options['svc_senior_age_from'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_senior_disable'] ) && ( $svc_options['svc_senior_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <b><?php esc_html_e( 'To: ', 'service-booking' ); ?></b>&nbsp;&nbsp;<input name="service_options[svc_senior_age_to]" type="number" step="1" min="" id="age_to_4" style="width:165px;" placeholder="<?php esc_html_e( 'to', 'service-booking' ); ?>" value="<?php echo isset( $svc_options ) && isset( $svc_options['svc_senior_age_to'] ) ? esc_attr( $svc_options['svc_senior_age_to'] ) : ''; ?>" onchange="checkServiceAgeValue(this)" <?php echo isset( $svc_options ) && isset( $svc_options['svc_senior_disable'] ) && ( $svc_options['svc_senior_disable'] == '1' ) ? 'readonly' : ''; ?>>
                            </span>
                            &nbsp;&nbsp;<span>
                                <input type="hidden" name="service_options[svc_senior_disable]" value="0">
                                <input type="checkbox" name="service_options[svc_senior_disable]" id="svc_age_disable_4" value="1" onchange="disableAgeGroup(this)" <?php isset( $svc_options ) && isset( $svc_options['svc_senior_disable'] ) ? checked( esc_attr( $svc_options['svc_senior_disable'] ), 1 ) : ''; ?>>&nbsp;<b><?php esc_html_e( 'Disable ?', 'service-booking' ); ?></b>
                            </span>
                            <span class="service-price-module-info_text">
                                <?php esc_html_e( 'If specified and if an external price module is connected to this service, these prices will be considered. If disabled, deafult prices as per the module connected will be considered', 'service-booking' ); ?>
                            </span>
                        </td>
                    </tr>
                    </table>
                </div>
            </div>

            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_svc_section' ); ?>
                    <a href="admin.php?page=bm_all_services" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <?php if ( !isset( $svc_row ) ) { ?>
                        <input type="submit" name="savesvc" id="savesvc" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onClick="return add_form_validation()">
                    <?php } else { ?>
                        <input type="submit" name="upsvc" id="upsvc" class="button button-primary" value="<?php esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="return add_form_validation()">
                    <?php } ?>
                    <!-- <?php if ( !isset( $svc_row ) ) { ?>
                        <button type="reset" name="resetfrm" id="resetfrm" class="button" style="background-color: #5F5B50;color: white;"><?php esc_attr_e( 'Reset', 'service-booking' ); ?></button>
                    <?php } ?> -->
                <div class="all_error_text" style="display:none;"></div>
                </p>
            </div>
        </tbody>
    </form>
</div>

    <div class="popup-message-overlay" id="popup-message-overlay"></div>
    <div class="popup-message-container" id="popup-message-container">
        <span id="popup-message"></span>
        <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
    </div>

    <div class="loader_modal"></div>
</div>
