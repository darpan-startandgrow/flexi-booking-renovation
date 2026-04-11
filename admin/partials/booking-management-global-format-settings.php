<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();
$languages  = $dbhandler->get_global_option_value( 'bm_flexibooking_languages', array() );

if ( filter_input( INPUT_POST, 'save_format_settings' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'format_settings_nonce' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_format_settings',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_format_settings' ) ) {
        $format_settings_data = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $format_settings_data != false ) {
            foreach ( $format_settings_data as $key => $value ) {
                $dbhandler->update_global_option_value( $key, $value );
            }
        }

        /**$args = array(
            'type'        => 'success',
            'dismissible' => true,
        );

        wp_admin_notice( esc_html__( 'Data Saved Sucessfully.' ), $args );*/

        echo ( '<div id="successMessage" class="bm-notice">' );
        echo esc_html_e( 'Data Saved Sucessfully.', 'service-booking' );
        echo ( '</div>' );
    }

    if ( filter_input( INPUT_POST, 'resetdata' ) ) {
        foreach ( $_POST as $key => $value ) {
            delete_option( $key );
        }

        echo ( '<div id="successMessage" class="bm-notice">' );
        echo esc_html_e( 'Data Cleared Sucessfully.', 'service-booking' );
        echo ( '</div>' );
    }
}

$current_language = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

?>
<div class="sg-admin-main-box" id="language_settings_form">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Format Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_format_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_flexi_service_time_slot_format"><?php esc_html_e( 'Service time slot format', 'service-booking' ); ?></label></th>
                    <td style="width: 31.5%">
                        <select id="bm_flexi_service_time_slot_format" name="bm_flexi_service_time_slot_format" class="regular-text">
                            <option value="12" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format' ), '12' ); ?>><?php esc_html_e( '12hrs (11PM)', 'service-booking' ); ?></option>
                            <option value="24" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format' ), '24' ); ?>><?php esc_html_e( '24hrs (23:00)', 'service-booking' ); ?></option>
                        </select>
                    </td>
                    <td>
                        <?php esc_html_e( 'Choose a format for the service time slot to be shown in frontend.', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_flexi_service_price_format"><?php esc_html_e( 'Service price format', 'service-booking' ); ?></label></th>
                    <td style="width: 31.5%">
                        <select id="bm_flexi_service_price_format" name="bm_flexi_service_price_format" class="regular-text">
                            <option value="en" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_US' ); ?>><?php esc_html_e( 'US', 'service-booking' ); ?></option>
                            <option value="de_DE" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'de_DE' ); ?>><?php esc_html_e( 'European', 'service-booking' ); ?></option>
                            <option value="en_GB" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_GB' ); ?>><?php esc_html_e( 'British', 'service-booking' ); ?></option>
                            <option value="en_AU" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_AU' ); ?>><?php esc_html_e( 'Australian', 'service-booking' ); ?></option>
                            <option value="pt_BR" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'pt_BR' ); ?>><?php esc_html_e( 'Brazilian', 'service-booking' ); ?></option>
                            <option value="en_CA" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_CA' ); ?>><?php esc_html_e( 'Canadian', 'service-booking' ); ?></option>
                            <option value="cs_CZ" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'cs_CZ' ); ?>><?php esc_html_e( 'Czech', 'service-booking' ); ?></option>
                            <option value="da_DK" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'da_DK' ); ?>><?php esc_html_e( 'Danish', 'service-booking' ); ?></option>
                            <option value="zh_HK" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'zh_HK' ); ?>><?php esc_html_e( 'Hong Kong', 'service-booking' ); ?></option>
                            <option value="hu_HU" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'hu_HU' ); ?>><?php esc_html_e( 'Hungarian', 'service-booking' ); ?></option>
                            <option value="ko_KR" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'ko_KR' ); ?>><?php esc_html_e( 'South Korean', 'service-booking' ); ?></option>
                            <option value="he_IL" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'he_IL' ); ?>><?php esc_html_e( 'Israeli', 'service-booking' ); ?></option>
                            <option value="ja_JP" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'ja_JP' ); ?>><?php esc_html_e( 'Japanese', 'service-booking' ); ?></option>
                            <option value="ms_MY" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'ms_MY' ); ?>><?php esc_html_e( 'Malaysian', 'service-booking' ); ?></option>
                            <option value="es_MX" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'es_MX' ); ?>><?php esc_html_e( 'Mexican', 'service-booking' ); ?></option>
                            <option value="en_NZ" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_NZ' ); ?>><?php esc_html_e( 'New Zealandian', 'service-booking' ); ?></option>
                            <option value="no_NO" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'no_NO' ); ?>><?php esc_html_e( 'Norwegian', 'service-booking' ); ?></option>
                            <option value="tl_PH" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'tl_PH' ); ?>><?php esc_html_e( 'Philippine', 'service-booking' ); ?></option>
                            <option value="pl_PL" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'pl_PL' ); ?>><?php esc_html_e( 'Polish', 'service-booking' ); ?></option>
                            <option value="en_SG" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_SG' ); ?>><?php esc_html_e( 'Singapore', 'service-booking' ); ?></option>
                            <option value="sv_SE" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'sv_SE' ); ?>><?php esc_html_e( 'Swedish', 'service-booking' ); ?></option>
                            <option value="de_CH" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'de_CH' ); ?>><?php esc_html_e( 'Swiss', 'service-booking' ); ?></option>
                            <option value="zh_TW" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'zh_TW' ); ?>><?php esc_html_e( 'Taiwan', 'service-booking' ); ?></option>
                            <option value="th_TH" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'th_TH' ); ?>><?php esc_html_e( 'Thai', 'service-booking' ); ?></option>
                            <option value="en_IN" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_IN' ); ?>><?php esc_html_e( 'Indian', 'service-booking' ); ?></option>
                            <option value="tr_TR" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'tr_TR' ); ?>><?php esc_html_e( 'Turkish', 'service-booking' ); ?></option>
                            <option value="fa_IR" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'fa_IR' ); ?>><?php esc_html_e( 'Iranian', 'service-booking' ); ?></option>
                            <option value="ru_RU" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'ru_RU' ); ?>><?php esc_html_e( 'Russian', 'service-booking' ); ?></option>
                            <option value="es_AR" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'es_AR' ); ?>><?php esc_html_e( 'Argentinian', 'service-booking' ); ?></option>
                            <option value="es_CL" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'es_CL' ); ?>><?php esc_html_e( 'Chilean', 'service-booking' ); ?></option>
                            <option value="es_CO" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'es_CO' ); ?>><?php esc_html_e( 'Colombian', 'service-booking' ); ?></option>
                            <option value="en_ZA" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'en_ZA' ); ?>><?php esc_html_e( 'South African', 'service-booking' ); ?></option>
                            <option value="ar_EG" <?php selected( $dbhandler->get_global_option_value( 'bm_flexi_service_price_format' ), 'ar_EG' ); ?>><?php esc_html_e( 'Egyptian', 'service-booking' ); ?></option>
                        </select>
                    </td>
                    <td>
                        <?php esc_html_e( 'Choose a format for the service price to be shown in frontend.', 'service-booking' ); ?>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'format_settings_nonce' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_format_settings" id="save_format_settings" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onclick="return bm_language_settings_validation()">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


