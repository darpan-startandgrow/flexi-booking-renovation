<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();

if ( filter_input( INPUT_POST, 'save_integration_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_global_payment_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_integration_global',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_integration_global' ) ) {
        $_POST['bm_enable_woocommerce_checkout'] = isset( $_POST['bm_enable_woocommerce_checkout'] ) ? 1 : 0;

        if ( !isset( $_POST['bm_woocommerce_only_checkout'] ) || $_POST['bm_enable_woocommerce_checkout'] == 0 ) {
            $_POST['bm_woocommerce_only_checkout'] = 0;
        } else {
            $_POST['bm_woocommerce_only_checkout'] = 1;
        }

        $integration_data = ( new BM_Request() )->sanitize_request( $_POST, $identifier, $exclude );

        if ( $integration_data != false ) {
            foreach ( $integration_data as $key => $value ) {
                $dbhandler->update_global_option_value( $key, $value );
            }

            echo ( '<div id="successMessage" class="bm-notice">' );
            echo esc_html__( 'Data Saved Sucessfully.', 'service-booking' );
            echo ( '</div>' );
        }
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

if ( $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 ) == 1 && !( new WooCommerceService() )->is_enabled() ) {
    echo ( '<div class="bm-notice bm-error">' );
    echo esc_html__( 'You do not have WooCommerce plugin activated and WooCommerce integration is ON !!', 'service-booking' );
    echo ( '</div>' );
}

?>
<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Integration Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_integration_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'Integrate WooCommerce Checkout ?', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 31.5%;">
                        <input name="bm_enable_woocommerce_checkout" type="checkbox" id="bm_enable_woocommerce_checkout" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout' ), 1 ); ?>  onclick="bm_open_close_tab('woocommerce_only')">
                        <label for="bm_enable_woocommerce_checkout"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'Check if you want to integrate woocommerce checkout.', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr class="hidden" id="woocommerce_only" <?php echo $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 ) == 1 ? "style='display: contents;'" : ''; ?>>
                    <th scope="row">
                        <?php esc_html_e( 'Only WooCommerce Checkout ?', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 31.5%;">
                        <input name="bm_woocommerce_only_checkout" type="checkbox" id="bm_woocommerce_only_checkout" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout' ), 1 ); ?>>
                        <label for="bm_woocommerce_only_checkout"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'Check when checkouts are allowed only through woocommerce provided that woocommerce integration is ON.', 'service-booking' ); ?>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_global_payment_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_integration_global" id="save_integration_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


