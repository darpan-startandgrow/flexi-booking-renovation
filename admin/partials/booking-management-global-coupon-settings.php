<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();
$limit      = $dbhandler->get_global_option_value( 'bm_auto_apply_limit' );

if ( filter_input( INPUT_POST, 'save_coupon_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_global_coupon_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }
    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_coupon_global',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_coupon_global' ) ) {
        $coupon_settings_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        $coupon_settings_post['bm_auto_apply_coupon'] = isset( $coupon_settings_post['bm_auto_apply_coupon'] ) ? 1 : 0;
        $coupon_settings_post['bm_auto_apply_limit']  = isset( $coupon_settings_post['bm_auto_apply_limit'] ) ? filter_input( INPUT_POST, 'bm_auto_apply_limit' ) : null;
        $coupon_settings_post['bm_inactive_coupons']  = isset( $coupon_settings_post['bm_inactive_coupons'] ) ? 1 : 0;

        foreach ( $coupon_settings_post as $key => $value ) {
            $dbhandler->update_global_option_value( $key, $value );
        }
        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_global_coupon_settings' ) );
    }

    if ( filter_input( INPUT_POST, 'resetdata' ) ) {
        foreach ( $_POST as $key => $value ) {
            delete_option( $key );
        }
        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_global_coupon_settings' ) );
    }
}
?>
<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Coupon Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_coupon_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_auto_apply_coupon"><?php esc_html_e( 'Allow auto apply', 'service-booking' ); ?></label></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_auto_apply_coupon" type="checkbox" id="bm_auto_apply_coupon" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_auto_apply_coupon' ), '1' ); ?>>
                        <label for="bm_auto_apply_coupon"></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_auto_apply_limit"><?php esc_html_e( 'Auto apply coupon maximum limit', 'service-booking' ); ?></label></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_auto_apply_limit" type="number" id="bm_auto_apply_limit" class="regular-text" min="0" step="1" value="<?php echo !empty( $limit ) ? esc_attr( $limit ) : ''; ?>">
                        <label for="bm_auto_apply_limit"></label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><label for="status"><?php esc_html_e( 'Inactive of all coupon', 'service-booking' ); ?></label></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_inactive_coupons" type="checkbox" id="bm_inactive_coupons" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_inactive_coupons' ), '1' ); ?>>
                        <label for="bm_inactive_coupons"></label>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_global_coupon_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_coupon_global" id="save_coupon_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


