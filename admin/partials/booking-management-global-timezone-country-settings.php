<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier       = 'GLOBAL';
$dbhandler        = new BM_DBhandler();
$bmrequests       = new BM_Request();
$countries        = $bmrequests->bm_get_countries();
$default_country  = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
$default_timezone = $dbhandler->get_global_option_value( 'bm_booking_time_zone', get_option( 'timezone_string' ) );
$timezones        = $bmrequests->bm_fetch_timezones( $default_country );

if ( filter_input( INPUT_POST, 'save_tz_country_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_tz_country_global_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_tz_country_global',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_tz_country_global' ) ) {
        $general_tz_country_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $general_tz_country_post != false ) {
            $general_tz_country_post['bm_booking_country']   = isset( $general_tz_country_post['bm_booking_country'] ) ? $general_tz_country_post['bm_booking_country'] : 'IN';
            $general_tz_country_post['bm_booking_time_zone'] = isset( $general_tz_country_post['bm_booking_time_zone'] ) ? $general_tz_country_post['bm_booking_time_zone'] : 'Asia/Kolkata';

            foreach ( $general_tz_country_post as $key => $value ) {
                $dbhandler->update_global_option_value( $key, $value );
            }

            if ( isset( $general_tz_country_post['bm_booking_time_zone'] ) ) {
                update_option( 'timezone_string', $general_tz_country_post['bm_booking_time_zone'] );
            }

            wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_global_timezone_country_settings' ) );
        }
    }

    if ( filter_input( INPUT_POST, 'resetdata' ) ) {
        foreach ( $_POST as $key => $value ) {
            delete_option( $key );
        }

        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_global_timezone_country_settings' ) );
    }
}

?>

<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Time Zone and Country Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_timezone_country_settings">
        <tbody>
            <br>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_booking_country"><?php esc_html_e( 'Country', 'service-booking' ); ?></label></th>
                    <td>
                        <select id="bm_booking_country" name="bm_booking_country" class="regular-text" onchange="bm_fetch_timezone()">
                            <?php
                            if ( !empty( $countries ) ) {
                                foreach ( $countries as $key => $country ) {
									?>
                                    <option value="<?php echo esc_html( $key ); ?>" <?php selected( $default_country, esc_html( $key ) ); ?>><?php echo esc_html( $country ); ?></option>
									<?php
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td><?php esc_html_e( 'Set default country for the plugin.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_booking_time_zone"><?php esc_html_e( 'Time Zone', 'service-booking' ); ?></label></th>
                    <td>
                        <select id="bm_booking_time_zone" name="bm_booking_time_zone" class="regular-text">
                            <?php
                            if ( !empty( $timezones ) ) {
                                foreach ( $timezones as $timezone ) {
									?>
                                    <option value="<?php echo esc_html( $timezone ); ?>" <?php selected( $default_timezone, esc_html( $timezone ) ); ?>><?php echo esc_html( $timezone ); ?></option>
									<?php
                                }
                            }
                            ?>
                        </select>
                        <div class="global_timezone_errortext"></div>
                    </td>
                    <td><?php esc_html_e( 'Set default timezone for the plugin.', 'service-booking' ); ?></td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_tz_country_global_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_tz_country_global" id="save_tz_country_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                <div class="all_tmpl_error_text" style="display:none;"></div>
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>





