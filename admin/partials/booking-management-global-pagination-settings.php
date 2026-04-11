<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();

if ( filter_input( INPUT_POST, 'save_pagination' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_pagination_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_pagination',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_pagination' ) ) {
        $global_pagination_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $global_pagination_post != false ) {
            foreach ( $global_pagination_post as $key => $value ) {
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
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Number of records per page Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_pagination_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_orders_per_page"><?php esc_html_e( 'Orders per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_orders_per_page" type="number" step="1" min="1" max="100" id="bm_orders_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_orders_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_orders_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many orders to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_services_per_page"><?php esc_html_e( 'Services per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_services_per_page" type="number" step="1" min="1" max="100" id="bm_services_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_services_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_services_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many services to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_categories_per_page"><?php esc_html_e( 'Categories per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_categories_per_page" type="number" step="1" min="1" max="100" id="bm_categories_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_categories_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_categories_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many categories to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_templates_per_page"><?php esc_html_e( 'Templates per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_templates_per_page" type="number" step="1" min="1" max="100" id="bm_templates_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_templates_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_templates_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many templates to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_price_modules_per_page"><?php esc_html_e( 'Price Modules per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_price_modules_per_page" type="number" step="1" min="1" max="100" id="bm_price_modules_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_price_modules_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_price_modules_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many price modules to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_notification_processes_per_page"><?php esc_html_e( 'Notification processses per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_notification_processes_per_page" type="number" step="1" min="1" max="100" id="bm_notification_processes_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_notification_processes_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_notification_processes_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many notification processes to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_email_records_per_page"><?php esc_html_e( 'Email records per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_email_records_per_page" type="number" step="1" min="1" max="100" id="bm_email_records_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_email_records_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_email_records_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many email records to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_voucher_records_per_page"><?php esc_html_e( 'Voucher records per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_voucher_records_per_page" type="number" step="1" min="1" max="100" id="bm_voucher_records_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_voucher_records_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_voucher_records_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many voucher records to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_coupon_per_page"><?php esc_html_e( 'Coupon records per page', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_coupon_per_page" type="number" step="1" min="1" max="100" id="bm_coupon_per_page" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_coupon_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_coupon_per_page' ) : 10 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Specify how many coupon records to be shown in a single page, Maximum-100', 'service-booking' ); ?>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_pagination_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_pagination" id="save_pagination" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


