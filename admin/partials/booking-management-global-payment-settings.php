<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();

if ( filter_input( INPUT_POST, 'save_payment_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_global_payment_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_payment_global',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_payment_global' ) ) {
        $global_payment_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $global_payment_post != false ) {
            $global_payment_post['bm_enable_stripe']           = isset( $global_payment_post['bm_enable_stripe'] ) ? 1 : 0;
            $global_payment_post['bm_show_stripe_credentials'] = isset( $global_payment_post['bm_show_stripe_credentials'] ) ? 1 : 0;

            if ( $global_payment_post['bm_enable_stripe'] == 1 ) {
                $global_payment_post['bm_flexi_stripe_public_code']  = $bmrequests->encrypt_key( $global_payment_post['bm_flexi_stripe_public_code'], 'flexibooking_public_stripe_code' );
                $global_payment_post['bm_flexi_stripe_private_code'] = $bmrequests->encrypt_key( $global_payment_post['bm_flexi_stripe_private_code'], 'flexibooking_private_stripe_code' );
            } else {
                $global_payment_post['bm_flexi_stripe_public_code']  = '';
                $global_payment_post['bm_flexi_stripe_private_code'] = '';
            }

            if ( $global_payment_post['bm_payment_session_time'] < 2 ) {
                $global_payment_post['bm_payment_session_time'] = 2;
            } elseif ( $global_payment_post['bm_payment_session_time'] > 20 ) {
                $global_payment_post['bm_payment_session_time'] = 20;
            }

            foreach ( $global_payment_post as $key => $value ) {
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

$public_key  = $bmrequests->decrypt_key( $dbhandler->get_global_option_value( 'bm_flexi_stripe_public_code' ), 'flexibooking_public_stripe_code' );
$private_key = $bmrequests->decrypt_key( $dbhandler->get_global_option_value( 'bm_flexi_stripe_private_code' ), 'flexibooking_private_stripe_code' );

?>
<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Payment Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_payment_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'Enable Stripe ?', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 31.5%;">
                        <input name="bm_enable_stripe" type="checkbox" id="bm_enable_stripe" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_enable_stripe' ), '1' ); ?> onclick="bm_open_close_tab( <?php echo !empty( $public_key ) && !empty( $private_key ) ? "'stripe_credentials_checkbox'" : "'stripe_credentials'"; ?> )">
                        <label for="bm_enable_stripe"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'Check if you want to enable stripes payment system.', 'service-booking' ); ?>
                    </td>
                </tr>

                <?php if ( !empty( $public_key ) && !empty( $private_key ) ) { ?>
                <tr id="stripe_credentials_checkbox" <?php echo $dbhandler->get_global_option_value( 'bm_enable_stripe', 0 ) == 1 ? '' : "style='display: none;'"; ?>>
                    <th scope="row">
                        <?php esc_html_e( 'Show stripe credentials ?', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 31.5%;">
                        <input name="bm_show_stripe_credentials" type="checkbox" id="bm_show_stripe_credentials" class="regular-text bm_toggle" onchange="show_stripe_credentials(this)">
                        <label for="bm_show_stripe_credentials"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'Check to see stripe credentials.', 'service-booking' ); ?>
                    </td>
                </tr>
                <?php } ?>
                <table id="stripe_credentials" class="form-table" role="presentation" style="display:none;">
                    <tr>
                        <th scope="row">
                            <label for="bm_flexi_stripe_public_code"><?php esc_html_e( 'Stripe public key', 'service-booking' ); ?><strong class="required_asterisk"> *</strong></label>
                        </th>
                        <td class="bminput bm_required" style="width: 40%;">
                            <input name="bm_flexi_stripe_public_code" type="text" id="bm_flexi_stripe_public_code" class="regular-text" value="<?php echo esc_html( $public_key ); ?>" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                        <td style="vertical-align:top;position:absolute;left:50.5%;">
                            <?php esc_html_e( 'Stripe public key.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_flexi_stripe_private_code"><?php esc_html_e( 'Stripe secret key', 'service-booking' ); ?><strong class="required_asterisk"> *</strong></label>
                        </th>
                        <td class="bminput bm_required" style="width: 40%;">
                            <input name="bm_flexi_stripe_private_code" type="password" id="bm_flexi_stripe_private_code" class="regular-text" value="<?php echo esc_html( $private_key ); ?>" autocomplete="new-password">
                            <div class="errortext"></div>
                        </td>
                        <td style="vertical-align:top;position:absolute;left:50.5%;">
                            <?php esc_html_e( 'Stripe private key.', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="bm_booking_currency"><?php esc_html_e( 'Currency', 'service-booking' ); ?></label></th>
                        <td style="width: 31.5%;">
                            <select name="bm_booking_currency" id="bm_booking_currency" class="regular-text">
                                <option value="USD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'USD' ); ?>><?php esc_html_e( 'US Dollars', 'service-booking' ); ?> ($)</option>
                                <option value="EUR" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'EUR' ); ?>><?php esc_html_e( 'Euros', 'service-booking' ); ?> (&euro;)</option>
                                <option value="GBP" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'GBP' ); ?>><?php esc_html_e( 'Pounds Sterling', 'service-booking' ); ?> (&pound;)</option>
                                <option value="AUD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'AUD' ); ?>><?php esc_html_e( 'Australian Dollars', 'service-booking' ); ?> ($)</option>
                                <option value="BRL" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'BRL' ); ?>><?php esc_html_e( 'Brazilian Real', 'service-booking' ); ?> (R$)</option>
                                <option value="CAD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'CAD' ); ?>><?php esc_html_e( 'Canadian Dollars', 'service-booking' ); ?> ($)</option>
                                <option value="CZK" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'CZK' ); ?>><?php esc_html_e( 'Czech Koruna', 'service-booking' ); ?></option>
                                <option value="DKK" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'DKK' ); ?>><?php esc_html_e( 'Danish Krone', 'service-booking' ); ?></option>
                                <option value="HKD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'HKD' ); ?>><?php esc_html_e( 'Hong Kong Dollar', 'service-booking' ); ?> ($)</option>
                                <option value="HUF" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'HUF' ); ?>><?php esc_html_e( 'Hungarian Forint', 'service-booking' ); ?></option>
                                <option value="ILS" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'ILS' ); ?>><?php esc_html_e( 'Israeli Shekel', 'service-booking' ); ?> (&#x20aa;)</option>
                                <option value="JPY" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'JPY' ); ?>><?php esc_html_e( 'Japanese Yen', 'service-booking' ); ?> (&yen;)</option>
                                <option value="MYR" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'MYR' ); ?>><?php esc_html_e( 'Malaysian Ringgits', 'service-booking' ); ?></option>
                                <option value="MXN" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'MXN' ); ?>><?php esc_html_e( 'Mexican Peso', 'service-booking' ); ?> ($)</option>
                                <option value="NZD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'NZD' ); ?>><?php esc_html_e( 'New Zealand Dollar', 'service-booking' ); ?> ($)</option>
                                <option value="NOK" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'NOK' ); ?>><?php esc_html_e( 'Norwegian Krone', 'service-booking' ); ?></option>
                                <option value="PHP" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'PHP' ); ?>><?php esc_html_e( 'Philippine Pesos', 'service-booking' ); ?></option>
                                <option value="PLN" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'PLN' ); ?>><?php esc_html_e( 'Polish Zloty', 'service-booking' ); ?></option>
                                <option value="SGD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'SGD' ); ?>><?php esc_html_e( 'Singapore Dollar', 'service-booking' ); ?> ($)</option>
                                <option value="SEK" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'SEK' ); ?>><?php esc_html_e( 'Swedish Krona', 'service-booking' ); ?></option>
                                <option value="CHF" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'CHF' ); ?>><?php esc_html_e( 'Swiss Franc', 'service-booking' ); ?></option>
                                <option value="TWD" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'TWD' ); ?>><?php esc_html_e( 'Taiwan New Dollars', 'service-booking' ); ?></option>
                                <option value="THB" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'THB' ); ?>><?php esc_html_e( 'Thai Baht', 'service-booking' ); ?> (&#3647;)</option>
                                <option value="INR" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'INR' ); ?>><?php esc_html_e( 'Indian Rupee', 'service-booking' ); ?> (&#x20B9;)</option>
                                <option value="TRY" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'TRY' ); ?>><?php esc_html_e( 'Turkish Lira', 'service-booking' ); ?> (&#8378;)</option>
                                <option value="RIAL" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'RIAL' ); ?>><?php esc_html_e( 'Iranian Rial', 'service-booking' ); ?></option>
                                <option value="RUB" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_currency' ), 'RUB' ); ?>><?php esc_html_e( 'Russian Rubles', 'service-booking' ); ?></option>
                            </select>
                        </td>
                        <td>
                            <?php esc_html_e( 'Currency to be used for booking purpose', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="bm_currency_position"><?php esc_html_e( 'Currency Symbol Position', 'service-booking' ); ?></label></th>
                        <td style="width: 31.5%">
                            <select id="bm_currency_position" name="bm_currency_position" class="regular-text">
                                <option value="before" <?php selected( $dbhandler->get_global_option_value( 'bm_currency_position' ), 'before' ); ?>><?php esc_html_e( 'Before - $10', 'service-booking' ); ?></option>
                                <option value="after" <?php selected( $dbhandler->get_global_option_value( 'bm_currency_position' ), 'after' ); ?>><?php esc_html_e( 'After - 10$', 'service-booking' ); ?></option>
                            </select>
                        </td>
                        <td>
                            <?php esc_html_e( 'Position of the currency symbol', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="bm_payment_session_time"><?php esc_html_e( 'Payment session timer (in minutes)', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="bm_payment_session_time" type="number" step="1" min="2" max="20" id="bm_payment_session_time" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_payment_session_time' ) ) ? $dbhandler->get_global_option_value( 'bm_payment_session_time' ) : 2 ); ?>">
                        </td>
                        <td style="vertical-align:top;position:absolute;left:50.5%;">
                            <?php esc_html_e( 'Specify time limit for payment session in payment page, Minimum-2 minutes, Maximum-20 minutes', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_global_payment_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_payment_global" id="save_payment_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onclick="return bm_payment_settings_validation()">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


