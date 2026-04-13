<?php
$dbhandler   = new BM_DBhandler();
$bmrequests  = new BM_Request();

// Fetch all global extras.
$global_extras = $dbhandler->get_all_result( 'GLOBALEXTRA', '*', 1, 'results' );

// Fetch all services for linking.
$all_services = $dbhandler->get_all_result( 'SERVICE', '*', 1, 'results' );

// Batch-fetch linked services for each global extra.
$ge_ids = array();
if ( ! empty( $global_extras ) ) {
    foreach ( $global_extras as $ge ) {
        $ge_ids[] = (int) $ge->id;
    }
}
$services_for_globals = ! empty( $ge_ids )
    ? $dbhandler->batch_get_services_for_global_extras( $ge_ids )
    : array();

$currency_symbol = $bmrequests->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
?>

<div class="sg-admin-main-box">
<div class="wrap listing_table" id="bm_shared_extras_listing">
    <div class="row">
        <span style="display: inline-block;width:50%;">
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Shared Extras', 'service-booking' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Global extras share capacity across all linked services. Manage them centrally here.', 'service-booking' ); ?></p>
        </span>
        <span style="display: inline-block;width:49%;text-align:right;">
            <button type="button" class="button button-primary" id="bm_add_shared_extra_btn" style="margin-bottom:10px;">
                <?php esc_html_e( 'Add Shared Extra', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </span>
    </div>

    <!-- Create / Edit Form (hidden by default) -->
    <div id="bm_shared_extra_form_wrap" style="display:none; background:#f9f9f9; border:1px solid #ddd; padding:20px; margin-bottom:20px; border-radius:4px;">
        <h3 id="bm_shared_extra_form_title"><?php esc_html_e( 'Add Shared Extra', 'service-booking' ); ?></h3>
        <input type="hidden" id="bm_se_id" value="">
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="bm_se_name"><?php esc_html_e( 'Name', 'service-booking' ); ?> <span style="color:red;">*</span></label></th>
                <td><input type="text" id="bm_se_name" class="regular-text" required></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_description"><?php esc_html_e( 'Description', 'service-booking' ); ?></label></th>
                <td><textarea id="bm_se_description" class="regular-text" rows="3"></textarea></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_price"><?php esc_html_e( 'Price', 'service-booking' ); ?> (<?php echo esc_html( $currency_symbol ); ?>)</label></th>
                <td><input type="number" id="bm_se_price" class="regular-text" step="0.01" min="0" value="0"></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_duration"><?php esc_html_e( 'Duration (hours)', 'service-booking' ); ?></label></th>
                <td><input type="number" id="bm_se_duration" class="regular-text" step="0.01" min="0" value="0"></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_operation"><?php esc_html_e( 'Total Operation Hours', 'service-booking' ); ?></label></th>
                <td><input type="number" id="bm_se_operation" class="regular-text" step="0.01" min="0" value="0"></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_max_cap"><?php esc_html_e( 'Max Capacity (Shared Pool)', 'service-booking' ); ?></label></th>
                <td><input type="number" id="bm_se_max_cap" class="regular-text" step="1" min="1" value="1"></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_visible"><?php esc_html_e( 'Visible on Frontend', 'service-booking' ); ?></label></th>
                <td><input type="checkbox" id="bm_se_visible" checked></td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_se_wc"><?php esc_html_e( 'Link WooCommerce', 'service-booking' ); ?></label></th>
                <td>
                    <input type="checkbox" id="bm_se_wc">
                    <input type="number" id="bm_se_wc_product" class="regular-text" placeholder="<?php esc_attr_e( 'WC Product ID', 'service-booking' ); ?>" style="display:none;margin-left:10px;" min="0">
                </td>
            </tr>
        </table>
        <p>
            <button type="button" class="button button-primary" id="bm_se_save_btn"><?php esc_html_e( 'Save', 'service-booking' ); ?></button>
            <button type="button" class="button" id="bm_se_cancel_btn"><?php esc_html_e( 'Cancel', 'service-booking' ); ?></button>
        </p>
    </div>

    <!-- Global Extras Listing Table -->
    <?php if ( ! empty( $global_extras ) ) : ?>
    <table class="wp-list-table widefat striped" id="bm_shared_extras_table">
        <thead>
            <tr>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( '#', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Description', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Price', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Max Capacity', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Linked Services', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Frontend', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Created', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;" width="20%"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            $idx = 1;
            foreach ( $global_extras as $ge ) :
                $ge_id        = (int) $ge->id;
                $linked_svcs  = isset( $services_for_globals[ $ge_id ] ) ? $services_for_globals[ $ge_id ] : array();
                $svc_count    = count( $linked_svcs );
                $svc_names    = array();
                foreach ( $linked_svcs as $ls ) {
                    $svc_names[] = esc_html( $ls->service_name );
                }
                $svc_tooltip = ! empty( $svc_names ) ? implode( ', ', $svc_names ) : esc_attr__( 'No services linked', 'service-booking' );
            ?>
            <tr id="bm-se-row-<?php echo esc_attr( $ge_id ); ?>"
                data-id="<?php echo esc_attr( $ge_id ); ?>"
                data-name="<?php echo esc_attr( $ge->name ); ?>"
                data-description="<?php echo esc_attr( $ge->description ); ?>"
                data-price="<?php echo esc_attr( $ge->price ); ?>"
                data-duration="<?php echo esc_attr( $ge->duration_hours ); ?>"
                data-operation="<?php echo esc_attr( $ge->total_operation_hours ); ?>"
                data-max-cap="<?php echo esc_attr( $ge->max_capacity ); ?>"
                data-visible="<?php echo esc_attr( $ge->is_visible_frontend ); ?>"
                data-wc="<?php echo esc_attr( $ge->link_woocommerce ); ?>"
                data-wc-product="<?php echo esc_attr( $ge->wc_product_id ); ?>">
                <td style="text-align:center;"><?php echo esc_html( $idx ); ?></td>
                <td style="text-align:center;"><?php echo esc_html( $ge->name ); ?></td>
                <td style="text-align:center;" title="<?php echo esc_attr( $ge->description ); ?>"><?php echo esc_html( mb_strimwidth( wp_strip_all_tags( $ge->description ), 0, 50, '...' ) ); ?></td>
                <td style="text-align:center;"><?php echo esc_html( $currency_symbol . number_format( (float) $ge->price, 2 ) ); ?></td>
                <td style="text-align:center;"><?php echo esc_html( $ge->max_capacity ); ?></td>
                <td style="text-align:center;">
                    <span class="bm-shared-badge" title="<?php echo esc_attr( $svc_tooltip ); ?>" style="display:inline-block;background:#f59e0b;color:#fff;padding:2px 10px;border-radius:12px;font-size:12px;cursor:help;">
                        <span class="dashicons dashicons-share" style="font-size:13px;width:13px;height:13px;vertical-align:middle;margin-right:2px;"></span><?php echo esc_html( $svc_count ); ?>
                    </span>
                </td>
                <td style="text-align:center;"><?php echo $ge->is_visible_frontend ? '<span style="color:green;">&#10003;</span>' : '<span style="color:#999;">&#10007;</span>'; ?></td>
                <td style="text-align:center;"><?php echo esc_html( ! empty( $ge->created_at ) ? wp_date( get_option( 'date_format' ), strtotime( $ge->created_at ) ) : '—' ); ?></td>
                <td style="text-align:center;">
                    <button type="button" class="button bm-se-edit-btn" title="<?php esc_attr_e( 'Edit', 'service-booking' ); ?>" data-id="<?php echo esc_attr( $ge_id ); ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                    <button type="button" class="button bm-se-delete-btn" title="<?php esc_attr_e( 'Delete', 'service-booking' ); ?>" data-id="<?php echo esc_attr( $ge_id ); ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red;"></i></button>
                </td>
            </tr>
            <?php
                $idx++;
            endforeach;
            ?>
        </tbody>
    </table>
    <?php else : ?>
        <p style="padding:20px;text-align:center;color:#666;"><?php esc_html_e( 'No shared extras found. Click "Add Shared Extra" to create one.', 'service-booking' ); ?></p>
    <?php endif; ?>
</div>
</div>
