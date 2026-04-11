<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();

if ( filter_input( INPUT_POST, 'save_svc_and_booking_settings' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'svc_and_booking_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_svc_and_booking_settings',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_svc_and_booking_settings' ) ) {
        $global_stopsales_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( !$global_stopsales_post['bm_global_unavailability'] ) {
            delete_option( 'bm_global_unavailability' );
        }

        if ( $global_stopsales_post != false ) {
            foreach ( $global_stopsales_post as $key => $value ) {
                $dbhandler->update_global_option_value( $key, $value );
            }
        }

        echo ( '<div id="successMessage" class="bm-notice">' );
        echo esc_html__( 'Data Saved Sucessfully.', 'service-booking' );
        echo ( '</div>' );
    }

    if ( filter_input( INPUT_POST, 'resetdata' ) ) {
        foreach ( $_POST as $key => $value ) {
            delete_option( $key );
        }

        echo ( '<div id="successMessage" class="bm-notice">' );
        echo esc_html__( 'Data Cleared Sucessfully.', 'service-booking' );
        echo ( '</div>' );
    }
}

?>
<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Service / Booking Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_svc_booking_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_allowed_stopsales"><?php esc_html_e( 'Allow Stopsales unto (hrs)', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_allowed_stopsales" type="number" step="0.5" min="0.5" id="bm_allowed_stopsales" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_allowed_stopsales' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify select dropdown hours limit of stopsales in add service page', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_allowed_saleswitch"><?php esc_html_e( 'Allow Salewsitch unto (hrs)', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_allowed_saleswitch" type="number" step="0.5" min="0.5" id="bm_allowed_saleswitch" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_allowed_saleswitch' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify select dropdown hours limit of saleswicth in add service page', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_book_on_request_expiry"><?php esc_html_e( 'Book on request expiry (hrs)', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_book_on_request_expiry" type="number" step="1" min="1" max="24" id="bm_book_on_request_expiry" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_book_on_request_expiry' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify expiry time in hours of book on request orders (max:24)', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label><?php esc_html_e( 'Unavailable Date Ranges', 'service-booking' ); ?></label>
                    </th>
                    <td colspan="2">
                        <input type="text" id="global_date_range_picker" class="regular-text" placeholder="<?php esc_attr_e( 'Select date range', 'service-booking' ); ?>" />
                        <button type="button" class="button" id="add_global_date_range">
                            <?php esc_html_e( 'Add Range', 'service-booking' ); ?>
                        </button>

                        <div id="global_unavailable_date_ranges" style="margin-top: 10px;">
                            <?php
                            $global_unavailability = $dbhandler->get_global_option_value( 'bm_global_unavailability' );

                            if ( !empty( $global_unavailability['dates'] ) ) :
                                foreach ( $global_unavailability['dates'] as $i => $range ) :
									?>
                                <span class="date_range_span">
                                    <input type="text"
                                        readonly
                                        id="global_unavailable_date_range_<?php echo esc_attr( $i ); ?>"
                                        name="bm_global_unavailability[dates][<?php echo esc_attr( $i ); ?>]"
                                        value="<?php echo esc_attr( $range ); ?>"
                                        class="date_range_input">
                                    <button type="button" class="remove_range" onclick="bm_remove_global_unavailable_range(this)">✕</button>
                                </span>
									<?php
                                endforeach;
                            endif;
                            ?>
                        </div>

                        <p class="description">
                            <?php esc_html_e( 'These date ranges will make all services unavailable.', 'service-booking' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_voucher_expiry"><?php esc_html_e( 'Voucher expiry (days)', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_voucher_expiry" type="number" step="1" min="1" id="bm_voucher_expiry" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_voucher_expiry' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify expiry time in days of vouchers', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_svc_shrt_desc_char_limit"><?php esc_html_e( 'Service short desc char limit', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_svc_shrt_desc_char_limit" type="number" step="1" min="0" id="bm_svc_shrt_desc_char_limit" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_svc_shrt_desc_char_limit' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify service short description character limit, keep 0 if not required', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_svc_overall_start_time"><?php esc_html_e( 'Service overall start time', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_svc_overall_start_time" type="time" id="bm_svc_overall_start_time" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_svc_overall_start_time' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify overall start time for all services, keep blank if not required', 'service-booking' ); ?>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'svc_and_booking_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_svc_and_booking_settings" id="save_svc_and_booking_settings" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


