<?php

$identifier         = 'COUPON';
$dbhandler          = new BM_DBhandler();
$bmrequests         = new BM_Request();
$coupon_validation  = new BM_Coupon_validation();
$coupon_id          = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$auto_apply_global  = $dbhandler->get_global_option_value( 'bm_auto_apply_coupon', '1' );
$id                 = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
if ( $coupon_id == false || $coupon_id == null ) {
    $coupon_id = 0;
}
if ( $id != 0 ) {
    $cpn_row = $dbhandler->get_row( $identifier, $id );
    if ( isset( $cpn_row ) ) {
        $unavailable_slot       = isset( $cpn_row->unavailable_slot ) ? maybe_unserialize( $cpn_row->unavailable_slot ) : array();
        $selected_emails        = isset( $cpn_row->customer_email_exclude ) ? maybe_unserialize( $cpn_row->customer_email_exclude ) : array();
        $cpn_unavailability     = isset( $cpn_row->coupon_unavailability ) ? maybe_unserialize( $cpn_row->coupon_unavailability ) : array();
        $start_date_val         = isset( $cpn_row->is_event_coupon ) && isset( $cpn_row->start_date_val ) ? maybe_unserialize( $cpn_row->start_date_val ) : array();
        $excluded_conditions    = isset( $cpn_row->excluded_conditions ) ? maybe_unserialize( $cpn_row->excluded_conditions ) : array();
        $saved_expiry_date      = isset( $cpn_row->expiry_date ) ? $cpn_row->expiry_date : '';
        $geographic_restriction = isset( $cpn_row->geographic_restriction ) ? maybe_unserialize( $cpn_row->geographic_restriction ) : array();
        $included_services      = isset( $cpn_row->included_services ) ? maybe_unserialize( $cpn_row->included_services ) : array();
        $cpn_img                = $bmrequests->bm_fetch_image_url_or_guid( $id, $identifier, 'url' ) ? $bmrequests->bm_fetch_image_url_or_guid( $id, $identifier, 'url' )  : '';
    }
}
if ( filter_input( INPUT_POST, 'save_coupon' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_coupon_data' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }
    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_coupon',
    );
    if ( filter_input( INPUT_POST, 'save_coupon' ) ) {

        $coupon_data = $_POST;
        if ( $coupon_data != false ) {
            $coupon_data = array(
                'is_active'                   => isset( $_POST['is_active'] ) ? filter_input( INPUT_POST, 'is_active' ) : null,
                'coupon_code'                 => isset( $_POST['coupon_code'] ) ? trim( filter_input( INPUT_POST, 'coupon_code' ) ) : null,
                'discount_amount'             => isset( $_POST['discount_amount'] ) ? floatval( round( filter_input( INPUT_POST, 'discount_amount' ), 2) ) : null,
                'coupon_description'          => isset( $_POST['coupon_description'] ) ? filter_input( INPUT_POST, 'coupon_description' ) : null,
                'discount_type'               => isset( $_POST['discount_type'] ) ? filter_input( INPUT_POST, 'discount_type' ) : null,
                'coupon_unavailability'       => isset( $_POST['coupon_unavailability'] ) ? filter_input( INPUT_POST, 'coupon_unavailability', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
                'unavailable_slot'            => isset( $_POST['unavailable_slot'] ) ? filter_input( INPUT_POST, 'unavailable_slot', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
                'min_spend'                   => !empty( $_POST['min_spend'] ) ? filter_input( INPUT_POST, 'min_spend' ) : null,
                'max_spend'                   => !empty( $_POST['max_spend'] ) ? filter_input( INPUT_POST, 'max_spend' ) : null,
                'expiry_date'                 => !empty( $_POST['expiry_date'] ) ?  filter_input( INPUT_POST, 'expiry_date' ) : null,
                'start_date_val'              => isset( $_POST['start_date_val'] ) ? filter_input( INPUT_POST, 'start_date_val', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
                'per_person_used_once'        => isset( $_POST['per_person_used_once'] ) ? 1 : 0,
                'overall_used_once'           => isset( $_POST['overall_used_once'] ) ? 1 : 0,
                'is_individual_use'           => isset( $_POST['is_individual_use'] ) ? 1 : 0,
                'used_per_coupon_per_service' => isset( $_POST['used_per_coupon_per_service'] ) ? 1 : 0,
                'usage_limit'                 => isset( $_POST['usage_limit'] ) ? filter_input( INPUT_POST, 'usage_limit' ) : null,
                'is_event_coupon'             => isset( $_POST['is_event_coupon'] ) ? 1 : 0,
                'is_exclude_category'         => isset( $_POST['is_exclude_category'] ) ? 1 : 0,
                'is_exclude_service'          => isset( $_POST['is_exclude_service'] ) ? 1 : 0,
                'excluded_conditions'         => isset( $_POST['excluded_conditions'] ) ? filter_input( INPUT_POST, 'excluded_conditions', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
                'is_service_included'         => isset( $_POST['is_service_included'] ) ? 1 : 0,
                'included_services'           => isset( $_POST['included_services'] ) ? filter_input( INPUT_POST, 'included_services', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
                'is_birthday_coupon'          => isset( $_POST['is_birthday_coupon'] ) ? 1 : 0,
                'is_email_exclude'            => isset( $_POST['is_email_exclude'] ) ? 1 : 0,
                'cannot_merged'               => isset( $_POST['cannot_merged'] ) ? 1 : 0,
                'auto_apply'                  => isset( $_POST['auto_apply'] ) ? 1 : 0,
                'geographic_restriction'      => isset( $_POST['geographic_restriction'] ) ? filter_input( INPUT_POST, 'geographic_restriction', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) : null,
                'coupon_image_guid'           => isset( $_POST['cpn_image_id'] ) ? filter_input( INPUT_POST, 'cpn_image_id' ) : 0,
            );
            if ( isset( $coupon_data['is_exclude_service'] ) && isset( $coupon_data['excluded_conditions']['excluded_services'] ) && !empty( $coupon_data['excluded_conditions']['excluded_services'] ) && is_array( $coupon_data['excluded_conditions']['excluded_services'] ) ) {
                $coupon_data['is_exclude_service'] = 1;
            } else {
                $coupon_data['is_exclude_service'] = 0;
            }
            if ( isset( $coupon_data['is_exclude_category'] ) && isset( $coupon_data['excluded_conditions']['excluded_category'] ) && !empty( $coupon_data['excluded_conditions']['excluded_category'] ) && is_array( $coupon_data['excluded_conditions']['excluded_category'] ) ) {
                $coupon_data['is_exclude_category'] = 1;
            } else {
                $coupon_data['is_exclude_category'] = 0;
            }
            if ( isset( $coupon_data['is_email_exclude'] ) && isset( $coupon_data['excluded_conditions']['excluded_emails'] ) && !empty( $coupon_data['excluded_conditions']['excluded_emails'] ) && is_array( $coupon_data['excluded_conditions']['excluded_emails'] ) ) {
                $coupon_data['is_email_exclude'] = 1;
            } else {
                $coupon_data['is_email_exclude'] = 0;
            }
            if ( isset( $coupon_data['is_service_included'] ) && isset( $coupon_data['included_services'] ) && !empty( $coupon_data['included_services'] ) && is_array( $coupon_data['included_services'] ) ) {
                $coupon_data['is_service_included'] = 1;
            } else {
                $coupon_data['is_service_included'] = 0;
            }
            if ( isset( $coupon_data['is_event_coupon'] ) && $coupon_data['is_event_coupon'] == 1 ) {
                $coupon_data['expiry_date'] = null;
                if ( isset( $coupon_data['start_date_val'] ) && count( $coupon_data['start_date_val'] ) > 0 ) {
                    foreach ( $coupon_data['start_date_val'] as $key => $data_value ) {
                        if ( empty( $data_value['date'] ) ) {
                            unset( $coupon_data['start_date_val'][ $key ] );
                        }
                    }
                    $coupon_data['start_date_val'] = array_values( $coupon_data['start_date_val'] );
                    $coupon_data['start_date_val'] = maybe_serialize( $coupon_data['start_date_val'] );
                }
            }
            if ( isset( $coupon_data['is_event_coupon'] ) && $coupon_data['is_event_coupon'] == 0 ) {
                $coupon_data['start_date_val'] = '';
            }
            if ( isset( $coupon_data['geographic_restriction'] ) && !empty( $coupon_data['geographic_restriction'] ) && is_array( $coupon_data['geographic_restriction'] ) && count( $coupon_data['geographic_restriction'] ) > 0 ) {
                if ( isset( $coupon_data['geographic_restriction'] ) && count( $coupon_data['geographic_restriction'] ) > 0 ) {
                    foreach ( $coupon_data['geographic_restriction'] as $key => $data_value ) {
                        if ( empty( $data_value['country_coupon'] ) ) {
                            unset( $coupon_data['geographic_restriction'][ $key ] );
                        }
                    }
                    if( empty( $coupon_data['geographic_restriction'])){
                        $coupon_data['is_geographic_restrictions'] = '0';
                        $coupon_data['geographic_restriction'] = null;
                    }else {
                        $coupon_data['is_geographic_restrictions'] = '1';
                        $coupon_data['geographic_restriction']     = maybe_serialize( array_values( $coupon_data['geographic_restriction'] ) );
                    }
                }
            } else {
                $coupon_data['is_geographic_restrictions'] = '0';
                $coupon_data['geographic_restriction'] = null;
            }
            if ( $coupon_data['unavailable_slot'] && count( $coupon_data['unavailable_slot'] ) > 0 ) {
                foreach ( $coupon_data['unavailable_slot'] as $key => $time_slot ) {
                    if ( empty( $time_slot['start'] ) ) {
                        unset( $coupon_data['unavailable_slot'][ $key ] );
                    }
                }
            }
            $coupon_data['coupon_unavailability'] = maybe_serialize( $coupon_data['coupon_unavailability'] );
            $coupon_data['unavailable_slot']      = maybe_serialize( $coupon_data['unavailable_slot'] );
            $coupon_data['excluded_conditions']   = maybe_serialize( $coupon_data['excluded_conditions'] );
            $coupon_data['included_services']     = maybe_serialize( $coupon_data['included_services'] );
            if ( empty( $coupon_id ) ) {

                $exists_id = $dbhandler->get_value( 'COUPON', 'id', $coupon_data['coupon_code'], 'coupon_code' );
                if ( is_null( $exists_id ) ) {
                    $coupon_id = $dbhandler->insert_row( $identifier, $coupon_data );
                    if ( !empty( $coupon_id ) ) {
                        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_all_coupons' ) );
                        exit;
                    } else {
                        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                        echo esc_html__( 'Coupon could not be saved !!', 'service-booking' );
                        echo ( '</div>' );
                    }
                } else {
                    echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                    echo esc_html__( 'Coupon Could not be saved as the code already exists !!', 'service-booking' );
                    echo ( '</div>' );
                }
            } elseif ( $coupon_id > 0 ) {

                $dbhandler->update_row( $identifier, 'id', $coupon_id, $coupon_data, '', '%d' );
                wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_coupon&id=' . esc_attr( $coupon_id ) ) );
                exit;
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Something is wrong, try again !!', 'service-booking' );
                echo ( '</div>' );
            }
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Coupon could not be saved!!', 'service-booking' );
            echo ( '</div>' );
        }
    }
}

?>

<div class="sg-admin-main-box">
    <div class="wrap">
        <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Coupon code creation', 'service-booking' ); ?></h2>
        <form role="form" method="post" id="coupon_creation_form">
            <tbody>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="status"><?php esc_html_e( 'Status', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput">
                            <select name="is_active" id="is_active" class="regular-text">
                                <option value="1" <?php isset( $cpn_row ) && isset( $cpn_row->is_active ) ? selected( $cpn_row->is_active, '1' ) : ''; ?>><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
                                <option value="0" <?php isset( $cpn_row ) && isset( $cpn_row->is_active ) ? selected( $cpn_row->is_active, '0' ) : ''; ?>><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
                            </select>
                            <span><?php esc_html_e( 'Status of this Coupon', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Auto Apply', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="auto_apply" type="checkbox" id="auto_apply" class="regular-text bm_toggle" <?php isset( $cpn_row ) && isset( $cpn_row->auto_apply ) ? checked( esc_attr( $cpn_row->auto_apply ), 1 ) : ''; ?>>
                            <label for="auto_apply"></label>
                            <span> <?php esc_html_e( 'Do you want this coupon to be applied automatically', 'service-booking' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="coupon_code"><?php esc_html_e( 'Coupon Code', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="coupon_code" type="text" id="coupon_code" placeholder="<?php esc_html_e( 'Coupon Code', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $cpn_row ) && !empty( $cpn_row->coupon_code ) ? esc_html( $cpn_row->coupon_code ) : ''; ?>" oninput="checkCouponCode(this.value)" autocomplete="off">
                            <span><?php esc_html_e( 'Code of the coupon', 'service-booking' ); ?></span>
                            <div id="error_message" class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="coupon_image"><?php esc_html_e( 'Image', 'service-booking' ); ?></label></th>
                        <td>
                            <input type="hidden" name="cpn_image_id" id="cpn_image_id" value="<?php echo isset( $cpn_row ) && esc_attr( $cpn_row->coupon_image_guid ) != 0 ? esc_attr( $cpn_row->coupon_image_guid ) : ''; ?>">
                            <span class="cpn_image_container" id="cpn_image_container" style="<?php echo isset( $cpn_img ) && !empty( $cpn_img ) ? 'display: inline-block' : 'display: none'; ?>">
                                <img src="<?php echo isset( $cpn_img ) ? esc_url( $cpn_img ) : ''; ?>" width="100" height="100" id="cpn_image_preview">
                                <button type="button" name="cpn_image_remove" id="cpn_image_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="cpn_remove_image()"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                            </span>
                            <div>
                                <a href="javascript:void(0)" class="button cpn-image"><?php esc_html_e( 'Upload image', 'service-booking' ); ?>&nbsp;<i class="fa fa-arrow-up" aria-hidden="true"></i></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="coupon_description"><?php esc_html_e( 'Description', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="coupon_description" type="text" id="coupon_description" placeholder="<?php esc_html_e( 'Enter the coupon main description', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $cpn_row ) && !empty( $cpn_row->coupon_description ) ? esc_html( $cpn_row->coupon_description ) : ''; ?>" autocomplete="off">
                            <span><?php esc_html_e( 'Description of the Coupon', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="type"><?php esc_html_e( 'Discount Type', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">

                            <select name="discount_type" id="discount_type" class="regular-text">
                                <option value="percent" <?php isset( $cpn_row ) && isset( $cpn_row->discount_type )  ? selected( $cpn_row->discount_type, 'percent' ) : ''; ?>><?php esc_html_e( 'Percentage Discount', 'service-booking' ); ?></option>
                                <option value="fixed" <?php isset( $cpn_row ) && isset( $cpn_row->discount_type ) ? selected( $cpn_row->discount_type, 'fixed' ) : ''; ?>><?php esc_html_e( 'Fixed Discount', 'service-booking' ); ?></option>
                            </select>
                            <span> <?php esc_html_e( 'Type of discount', 'service-booking' ); ?></span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><label for="discount_amount"><?php esc_html_e( 'Discount Amount', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="discount_amount" type="text" id="discount_amount" placeholder="<?php esc_html_e( 'Discount Amount', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $cpn_row ) && !empty( $cpn_row->discount_amount ) ? esc_html( $cpn_row->discount_amount ) : ''; ?>" autocomplete="off" onblur="couponAmountCheck()">
                            <span><?php esc_html_e( 'Amount of the Coupon', 'service-booking' ); ?></span>
                            <div class="errortext" id="dis_validate_amount"  style="color: red;"></div>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Is event Coupon?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_event_coupon" type="checkbox" id="is_event_coupon" class="regular-text bm_toggle" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_event_coupon ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'event_content' ); bm_open_close_tab( 'expiry_date_content' ); bm_open_close_tab( 'unavailable_date_tr' ); bm_open_close_tab( 'weekdays_tr' );">
                            <label for="is_event_coupon"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="event_content"
                    <?php
                    if ( isset( $cpn_row ) && !empty( $cpn_row->is_event_coupon ) ) {
                        echo "style='display:block;'";
                    }
                    ?>
                    >
                    <tr class="date_input_tr">
                        <th scope="row"><label for="start_date"><?php esc_html_e( 'Event Dates', 'service-booking' ); ?></label></th>
                        <td class="event_date_option_field">
                            <?php
                            if ( isset( $start_date_val ) && !empty( $start_date_val ) && is_array( $start_date_val ) ) {
                                $i = 1;
                                foreach ( $start_date_val as $date ) {
                                    if ( !empty( $date['date'] ) ) {
                                        $date_name = "start_date_val[$i][date]";
                                        $date_id   = "start_date_val_$i";
                                        $desc_name = "start_date_val[$i][desc]";
                                        $desc_id   = "start_date_desc_$i";
                                        ?>
                                        <span class="event_date_input_span">
                                            <input type="date" id="<?php echo esc_html( $date_id ); ?>" name="<?php echo esc_html( $date_name ); ?>" value="<?php echo esc_html( $date['date'] ); ?>" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>">
                                            <input type="textarea" id="<?php echo esc_html( $desc_id ); ?>" name="<?php echo esc_html( $desc_name ); ?>" value="<?php echo esc_html( $date['desc'] ); ?>">
                                            <button type="button" id="svc_event_date_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="bm_remove_svc_event_date(this)"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                                        </span>
                                        <?php
                                        $i++;
                                    }
                                }
                            }
                            ?>
                            <span class="event_add_dates_button"><button type="button" class="button button-primary" onClick="bm_add_event_date_cpn()"><?php esc_html_e( 'add date', 'service-booking' ); ?></button></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Is Birthday Coupon?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_birthday_coupon" type="checkbox" id="is_birthday_coupon" class="regular-text bm_toggle" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_birthday_coupon ) ? 'checked' : ''; ?>>
                            <label for="is_birthday_coupon"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Cannot be merged with other type of coupon ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="cannot_merged" type="checkbox" id="cannot_merged" class="regular-text bm_toggle" <?php isset( $cpn_row ) && isset( $cpn_row->cannot_merged ) ? checked( esc_attr( $cpn_row->cannot_merged ), 1 ) : ''; ?>>
                            <label for="cannot_merged"></label>
                            <span> <?php esc_html_e( 'Event and normal coupon can be used together', 'service-booking' ); ?></span>
                        </td>
                    </tr>
                    <tr id="weekdays_tr"
                        <?php
                        if ( isset( $cpn_row ) && !empty( $cpn_row->is_event_coupon ) ) {
                            echo "style='display:none;'";
                        }
                        ?>
                        >
                        <th scope="row"><label><?php esc_html_e( 'Days of the week when the Coupon is unavailable', 'service-booking' ); ?></label></th>
                        <td>
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][1]" value="<?php echo esc_attr( '1' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '1', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Monday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][2]" value="<?php echo esc_attr( '2' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '2', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Tuesday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][3]" value="<?php echo esc_attr( '3' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '3', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Wednesday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][4]" value="<?php echo esc_attr( '4' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '4', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Thurday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][5]" value="<?php echo esc_attr( '5' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '5', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Friday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][6]" value="<?php echo esc_attr( '6' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '6', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Saturday', 'service-booking' ); ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input type="checkbox" name="coupon_unavailability[weekdays][7]" value="<?php echo esc_attr( '0' ); ?>" <?php echo isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['weekdays'] ) && in_array( '0', $cpn_unavailability['weekdays'] ) ? 'checked' : ''; ?>><?php esc_html_e( 'Sunday', 'service-booking' ); ?></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="usage_limit"><?php esc_html_e( 'Usage Limit', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="usage_limit" type="number" id="usage_limit" placeholder="<?php esc_html_e( 'No limit', 'service-booking' ); ?>" min="0" step="1" class="regular-text" value="<?php echo isset( $cpn_row ) && ( $cpn_row->usage_limit ) ? esc_html( $cpn_row->usage_limit ) : ''; ?>" autocomplete="off">
                            <span><?php esc_html_e( 'Number of times a coupon can be used', 'service-booking' ); ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="min_spend"><?php esc_html_e( 'Minimum Spend', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="min_spend" type="number" id="min_spend" placeholder="<?php esc_html_e( 'No Minimum Spend', 'service-booking' ); ?>" min="0" step="1" class="regular-text" value="<?php echo isset( $cpn_row ) && ( $cpn_row->min_spend ) ? esc_html( $cpn_row->min_spend ) : ''; ?>" autocomplete="off" onblur="maxSpend()">
                            <span><?php esc_html_e( 'Minimum spend of the Coupon', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="max_spend"><?php esc_html_e( 'Maximum Spend', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="max_spend" type="number" id="max_spend" placeholder="<?php esc_html_e( 'No Maximum Spend', 'service-booking' ); ?>" min="0" step="1" class="regular-text" value="<?php echo isset( $cpn_row ) && ( $cpn_row->max_spend ) ? esc_html( $cpn_row->max_spend ) : ''; ?>" autocomplete="off" onblur="maxSpend()">
                            <span><?php esc_html_e( 'Maximum spend for the Coupon', 'service-booking' ); ?></span>
                            <div class="errortext" id="validate_amount" style="color:red;"></div>
                        </td>
                    </tr>
                    <tr id='expiry_date_content'
                        <?php
                        if ( isset( $cpn_row ) && !empty( $cpn_row->is_event_coupon ) ) {
                            echo "style='display:none;'";
                        }
                        ?>
                        >
                        <th scope="row"><label for="expiry_date"><?php esc_html_e( 'Expiry Date', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="expiry_date" type="date" id="expiry_date" placeholder="<?php esc_html_e( 'Expiry Date', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $cpn_row ) && isset( $saved_expiry_date ) ? esc_html( $saved_expiry_date ) : ''; ?>" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>" autocomplete="off">
                            <span><?php esc_html_e( 'Expiry of the Coupon, leave blank for no expiry', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr id='unavailable_date_tr'
                        <?php
                        if ( isset( $cpn_row ) && !empty( $cpn_row->is_event_coupon ) ) {
                            echo "style='display:none;'";
                        }
                        ?>
                        >
                        <th scope="row"><label for="unavailable_date"><?php esc_html_e( 'Specific dates when the Coupon is not applicable', 'service-booking' ); ?></label></th>
                        <td class="date_option_field">
                            <?php
                            if ( isset( $cpn_unavailability ) && !empty( $cpn_unavailability ) && isset( $cpn_unavailability['dates'] ) && !empty( $cpn_unavailability['dates'] ) ) {
                                $i = 1;
                                foreach ( $cpn_unavailability['dates'] as $unavailable_date ) {
                                    if ( !empty( $unavailable_date ) ) {
                                        $date_name = "coupon_unavailability[dates][$i]";
                                        $date_id   = "cpn_unavailable_date_$i";
                                        ?>
                                        <span class="date_input_span">
                                            <input type="date" id="<?php echo esc_html( $date_id ); ?>" name="<?php echo esc_html( $date_name ); ?>" value="<?php echo esc_html( $unavailable_date ); ?>" min="<?php echo esc_html( gmdate( 'Y-m-d' ) ); ?>">
                                            <button type="button" id="cpn_date_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="bm_remove_cpn_unavailable_date(this)"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                                        </span>
                                        <?php
                                        $i++;
                                    }
                                }
                            }
                            ?>
                            <span class="add_dates_button"><button type="button" class="button button-primary" onClick="bm_add_unavailable_date_cpn()"><?php esc_html_e( 'add date', 'service-booking' ); ?></button></span>
                        </td>
                    </tr>

                    <tr class="date_input_tr">
                        <th scope="row"><label for="unavailable_slot"><?php esc_html_e( 'Specific Time range when the Coupon is not applicable', 'service-booking' ); ?></label></th>
                        <td class="time_option_field">
                            <?php
                            if ( isset( $unavailable_slot ) && !empty( $unavailable_slot ) ) {
                                $i = 1;
                                foreach ( $unavailable_slot as $key => $single_slot ) {
                                    if ( !empty( $single_slot ) ) {
                                        $start_name = "unavailable_slot[$i][start]";
                                        $end_name   = "unavailable_slot[$i][end]";
                                        $start_id   = "unavailable_slot_start_$i";
                                        $end_id     = "unavailable_slot_end_$i";
                                        $start_time = isset( $single_slot ) && isset( $single_slot['start'] ) ? $single_slot['start'] : '';
                                        $end_time   = isset( $single_slot ) && isset( $single_slot['end'] ) ? $single_slot['end'] : '';
                                        $error_id   = "unavailable_slot_error_$i";
                                        ?>
                                        <div style="margin-top:8px; margin-bottom:8px;"><span class="time_input_span">
                                            <?php esc_html_e( 'From :', 'service-booking' ); ?><input type="time" id="<?php echo esc_html( $start_id ); ?>" name="<?php echo esc_html( $start_name ); ?>" value="<?php echo isset( $start_time ) ? $start_time : ''; ?>">
                                            <?php esc_html_e( 'To :', 'service-booking' ); ?><input type="time" id="<?php echo esc_html( $end_id ); ?>" name="<?php echo esc_html( $end_name ); ?>" value="<?php echo isset( $end_time ) ? $end_time : ''; ?>">
                                            <button type="button" id="cpn_time_remove" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="bm_remove_cpn_unavailable_time(this)"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                                            <span id="<?php echo esc_html( $error_id ); ?>" style="color: red; display: none;"></span>
                                            </span>
                                        </div>
                                        <?php
                                        $i++;
                                    }
                                }
                            }
                            ?>
                            <span class="add_time_button"><button type="button" class="button button-primary" onClick="bm_add_unavailable_time_cpn()"><?php esc_html_e( 'Add Time', 'service-booking' ); ?></button></span>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row"><?php esc_html_e( 'Can be used once in a day (Per user) ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="per_person_used_once" type="checkbox" id="per_person_used_once" class="regular-text bm_toggle" <?php isset( $cpn_row ) && isset( $cpn_row->per_person_used_once ) ? checked( esc_attr( $cpn_row->per_person_used_once ), 1 ) : ''; ?>>
                            <label for="per_person_used_once"></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Can be used once in a day ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="overall_used_once" type="checkbox" id="overall_used_once" class="regular-text bm_toggle" <?php isset( $cpn_row ) && isset( $cpn_row->overall_used_once ) ? checked( esc_attr( $cpn_row->overall_used_once ), 1 ) : ''; ?>>
                            <label for="overall_used_once"></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Can be used once in a day (Per Service)?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="used_per_coupon_per_service" type="checkbox" id="used_per_coupon_per_service" class="regular-text bm_toggle" <?php isset( $cpn_row ) && isset( $cpn_row->used_per_coupon_per_service ) ? checked( esc_attr( $cpn_row->used_per_coupon_per_service ), 1 ) : ''; ?>>
                            <label for="used_per_coupon_per_service"></label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Is individual use ?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_individual_use" type="checkbox" id="is_individual_use" class="regular-text bm_toggle" <?php isset( $cpn_row ) && isset( $cpn_row->is_individual_use ) ? checked( esc_attr( $cpn_row->is_individual_use ), 1 ) : ''; ?>>
                            <label for="is_individual_use"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Want to specify service', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_service_included" type="checkbox" id="is_service_included" class="regular-text bm_toggle" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_service_included ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'included_service_content' );">
                            <label for="is_service_included"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="included_service_content"
                    <?php
                    if ( isset( $cpn_row ) && !empty( $cpn_row->included_services ) ) {
                        echo "style='display:block;'";
                    }
                    ?>
                    >
                    <?php
                    if ( isset( $cpn_row->is_service_included ) && isset( $included_services ) && !empty( $included_services ) && is_array( $included_services ) ) {
                        $condition_values = $coupon_validation->bm_fetch_coupon_value_html( 3, $coupon_id, 0 );
                       ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Service Applicable', 'service-booking' ); ?></label>
                            </th>

                            <td id="service_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="included_services[]" id="included_services" class="notification-multiselect" style="width:300px;" multiple="multiple">
                                            <?php echo wp_kses( $condition_values, $bmrequests->bm_fetch_expanded_allowed_tags() ); ?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Service Applicable', 'service-booking' ); ?></label>
                            </th>
                            <td id="service_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="included_services[]" id="included_services" class="notification-multiselect" style="width:300px;" multiple="multiple"></select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                <table class="form-table" role="presentation" id="geographic_content_main">

                    <tr>
                        <th scope="row">
                            <label><?php esc_html_e( 'Geographic Restrictions', 'service-booking' ); ?></label>
                        </th>

                        <input type="checkbox" id="is_geographic_restrictions" name="is_geographic_restrictions" style="display: none;" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_geographic_restrictions ) ? 'checked' : ''; ?>>
                        <?php
                        if ( isset( $cpn_row ) && !empty( $cpn_row->is_geographic_restrictions ) && array_filter( $geographic_restriction ) && is_array( $geographic_restriction ) ) {
                            $total = count( $geographic_restriction );
                            ?>
                            <input type="hidden" id="list_selcted_country" value="<?php echo htmlspecialchars( json_encode( $geographic_restriction ) ); ?>">
                            <?php
                            for ( $i = 0; $i < $total; $i++ ) {
                                $country_name = isset( $geographic_restriction[ $i ]['country_coupon'] ) ? $geographic_restriction[ $i ]['country_coupon'] : '';
                                $state_lists  = isset( $geographic_restriction[ $i ]['state_coupon'] ) ? $geographic_restriction[ $i ]['state_coupon'] : '';

                                ?>
                                <td id="restriction_field_<?php echo $i; ?>" class="restriction_field">
                                    <div id="restriction_condition_div" class="bminput">
                                    </div>
                                </td><br>
                                <?php
                            }
                        } else {
                            ?>
                            <td id="restriction_field_0" class="restriction_field">
                                <div id="restriction_condition_div" class="bminput" style='width:100%;'>
                                    <div style="width:47%; float:left; margin-right:5px;">
                                        <label><?php esc_html_e( 'Country', 'service-booking' ); ?></label><br />
                                        <select name="geographic_restriction[0][country_coupon]" id="country_list_0" class="regular-text emailselect" onchange="bm_fetch_country_value(this)">
                                        </select>
                                    </div>
                                    <div style="width:47%; float:left;">
                                        <label><?php esc_html_e( 'States', 'service-booking' ); ?></label><br />
                                        <select name="geographic_restriction[0][state_coupon][]" id="state_list_0" class="notification-multiselect" style="width:300px;" multiple="multiple"></select>
                                    </div>
                                    <div style="width:4%; float:left; margin-left:1%; margin-top:25px;">
                                        <button type="button" class="bm_remove_event_condition" id="remove_restriction_0" onclick="bm_remove_restriction_box(this)"><i class="fa fa-remove"></i></button>
                                    </div>
                                </div>
                            </td><br>
                        <?php } ?>
                        <td class="add_trigger_conditions_class  restrictiontop">
                            <span class="add_restriction_button"><button type="button" class="button button-primary" onClick="addRestrictionList()"><?php esc_html_e( 'Add Restriction', 'service-booking' ); ?></button></span>
                        </td>
                    </tr>
                </table>

                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Want to Exclude service?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_exclude_service" type="checkbox" id="is_exclude_service" class="regular-text bm_toggle" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_exclude_service ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'service_content' );">
                            <label for="is_exclude_service"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="service_content"
                    <?php
                    if ( isset( $cpn_row ) && !empty( $cpn_row->is_exclude_service ) ) {
                        echo "style='display:block;'";
                    }
                    ?>
                    >
                    <?php
                    if ( isset( $cpn_row->is_exclude_service ) && isset( $excluded_conditions ) && !empty( $excluded_conditions ) && is_array( $excluded_conditions ) ) {
                        $condition_values = $coupon_validation->bm_fetch_coupon_value_html( 0, $coupon_id, 0 );
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Service Excluded', 'service-booking' ); ?></label>
                            </th>

                            <td id="service_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="excluded_conditions[excluded_services][]" id="excluded_services" class="notification-multiselect" style="width:300px;" multiple="multiple">
                                            <?php echo wp_kses( $condition_values, $bmrequests->bm_fetch_expanded_allowed_tags() ); ?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Service Excluded', 'service-booking' ); ?></label>
                            </th>
                            <td id="service_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="excluded_conditions[excluded_services][]" id="excluded_services" class="notification-multiselect" style="width:300px;" multiple="multiple"></select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Want to Exclude Category?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_exclude_category" type="checkbox" id="is_exclude_category" class="regular-text bm_toggle" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_exclude_category ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'cat_content' );">
                            <label for="is_exclude_category"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="cat_content"
                    <?php
                    if ( isset( $cpn_row ) && !empty( $cpn_row->is_exclude_category ) ) {
                        echo "style='display:block;'";
                    }
                    ?>
                    >
                    <?php
                    if ( isset( $cpn_row->is_exclude_category ) && isset( $excluded_conditions ) && !empty( $excluded_conditions ) && is_array( $excluded_conditions ) ) {
                        $condition_values = $coupon_validation->bm_fetch_coupon_value_html( 1, $coupon_id, 0 );
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Category Excluded', 'service-booking' ); ?></label>
                            </th>

                            <td id="category_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="excluded_conditions[excluded_category][]" id="excluded_category" class="notification-multiselect" style="width:300px;" multiple="multiple">
                                            <?php echo wp_kses( $condition_values, $bmrequests->bm_fetch_expanded_allowed_tags() ); ?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Category Excluded', 'service-booking' ); ?></label>
                            </th>
                            <td id="category_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <label><?php esc_html_e( 'Values', 'service-booking' ); ?></label><br />
                                        <select name="excluded_conditions[excluded_category][]" id="excluded_category" class="notification-multiselect" style="width:300px;" multiple="multiple"></select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Exclude user Email?', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_email_exclude" type="checkbox" id="is_email_exclude" class="regular-text bm_toggle" <?php echo isset( $cpn_row ) && !empty( $cpn_row->is_email_exclude ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'email_content' );">
                            <label for="is_email_exclude"></label>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="email_content"
                    <?php
                    if ( isset( $cpn_row ) && !empty( $cpn_row->is_email_exclude ) ) {
                        echo "style='display:block;'";
                    }
                    ?>
                    >
                    <?php
                    if ( isset( $cpn_row->is_exclude_service ) && isset( $excluded_conditions ) && !empty( $excluded_conditions ) && is_array( $excluded_conditions ) ) {
                        $condition_values = $coupon_validation->bm_fetch_coupon_value_html( 2, $coupon_id, 0 );
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Email Excluded', 'service-booking' ); ?></label>
                            </th>

                            <td id="service_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="excluded_conditions[excluded_emails][]" id="excluded_emails" class="notification-multiselect" style="width:300px;" multiple="multiple">
                                            <?php echo wp_kses( $condition_values, $bmrequests->bm_fetch_expanded_allowed_tags() ); ?>
                                        </select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    } else {
                        ?>
                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Excluded Email', 'service-booking' ); ?></label>
                            </th>
                            <td id="email_condition" class="condition_field">
                                <div id="trigger_condition_div" class="bminput bm_required">
                                    <div style="width:40%; float:left;">
                                        <select name="excluded_conditions[excluded_emails][]" id="excluded_emails" class="notification-multiselect" style="width:300px;" multiple="multiple"></select>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>

                <div class="row">
                    <p class="submit">
                        <?php wp_nonce_field( 'save_coupon_data' ); ?>
                        <a href="admin.php?page=bm_all_coupons" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Cancel', 'service-booking' ); ?></a>
                        <input type="submit" name="save_coupon" id="save_coupon" class="button button-primary" value="<?php empty( $coupon_id ) ? esc_attr_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="return add_coupon_form_validation()">
                    </p>
                </div>
            </tbody>
        </form>
    </div>

    <div class="loader_modal"></div>
</div>

