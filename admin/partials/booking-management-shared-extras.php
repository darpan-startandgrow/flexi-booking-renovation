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

// Fetch all local extras for the import dropdown (grouped by service).
$local_extras = $dbhandler->get_all_result( 'EXTRA', '*', array( 'is_global' => 0 ), 'results' );

// Build service name map for import dropdown labels.
$service_name_map = array();
if ( ! empty( $all_services ) ) {
    foreach ( $all_services as $svc ) {
        $service_name_map[ (int) $svc->id ] = $svc->service_name;
    }
}
?>

<div class="sg-admin-main-box">
<div class="wrap listing_table" id="bm_shared_extras_listing">
    <div class="row">
        <span style="display: inline-block;width:50%;">
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Shared Extras', 'service-booking' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Global extras share capacity across all linked services. Manage them centrally here.', 'service-booking' ); ?></p>
        </span>
        <span style="display: inline-block;width:49%;text-align:right;">
            <button type="button" class="button" id="bm_import_extra_btn" style="margin-bottom:10px;margin-right:5px;">
                <?php esc_html_e( 'Import from Service', 'service-booking' ); ?>&nbsp;<i class="fa fa-download" aria-hidden="true"></i>
            </button>
            <button type="button" class="button button-primary" id="bm_add_shared_extra_btn" style="margin-bottom:10px;">
                <?php esc_html_e( 'Add Shared Extra', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i>
            </button>
        </span>
    </div>

    <!-- Import from Service Form (hidden by default) -->
    <div id="bm_import_extra_form_wrap" style="display:none; background:#fff3cd; border:1px solid #ffc107; padding:15px; margin-bottom:20px; border-radius:4px;">
        <h3><?php esc_html_e( 'Import Local Extra as Shared Extra', 'service-booking' ); ?></h3>
        <p class="description"><?php esc_html_e( 'Select a service-specific extra to clone into a new shared (global) extra. The original local extra remains unchanged.', 'service-booking' ); ?></p>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><label for="bm_import_service_filter"><?php esc_html_e( 'Filter by Service', 'service-booking' ); ?></label></th>
                <td>
                    <select id="bm_import_service_filter" class="regular-text">
                        <option value=""><?php esc_html_e( '— All Services —', 'service-booking' ); ?></option>
                        <?php if ( ! empty( $all_services ) ) : ?>
                            <?php foreach ( $all_services as $svc ) : ?>
                                <option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_import_extra_select"><?php esc_html_e( 'Select Extra', 'service-booking' ); ?></label></th>
                <td>
                    <select id="bm_import_extra_select" class="regular-text">
                        <option value=""><?php esc_html_e( '— Select a local extra —', 'service-booking' ); ?></option>
                        <?php if ( ! empty( $local_extras ) ) : ?>
                            <?php foreach ( $local_extras as $le ) : ?>
                                <?php
                                $svc_label = '';
                                if ( ! empty( $le->service_id ) && isset( $service_name_map[ (int) $le->service_id ] ) ) {
                                    $svc_label = ' (' . $service_name_map[ (int) $le->service_id ] . ')';
                                } elseif ( ! empty( $le->service_id ) ) {
                                    $svc_label = ' (' . esc_html__( 'Service', 'service-booking' ) . ' #' . $le->service_id . ')';
                                }
                                ?>
                                <option value="<?php echo esc_attr( $le->id ); ?>"
                                    data-service="<?php echo esc_attr( $le->service_id ); ?>"
                                    data-name="<?php echo esc_attr( $le->extra_name ); ?>"
                                    data-desc="<?php echo esc_attr( $le->extra_desc ); ?>"
                                    data-price="<?php echo esc_attr( $le->extra_price ); ?>"
                                    data-duration="<?php echo esc_attr( $le->extra_duration ); ?>"
                                    data-operation="<?php echo esc_attr( $le->extra_operation ); ?>"
                                    data-max-cap="<?php echo esc_attr( $le->extra_max_cap ); ?>">
                                    <?php echo esc_html( $le->extra_name . $svc_label ); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
            <tr id="bm_import_preview_row" style="display:none;">
                <th scope="row"><?php esc_html_e( 'Preview', 'service-booking' ); ?></th>
                <td>
                    <div style="background:#fff;border:1px solid #ddd;padding:10px;border-radius:4px;">
                        <strong id="bm_import_preview_name"></strong><br>
                        <span id="bm_import_preview_details" style="color:#666;font-size:12px;"></span>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="bm_import_link_service"><?php esc_html_e( 'Link to Service (optional)', 'service-booking' ); ?></label></th>
                <td>
                    <select id="bm_import_link_service" class="regular-text">
                        <option value=""><?php esc_html_e( '— None —', 'service-booking' ); ?></option>
                        <?php if ( ! empty( $all_services ) ) : ?>
                            <?php foreach ( $all_services as $svc ) : ?>
                                <option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </td>
            </tr>
        </table>
        <p>
            <button type="button" class="button button-primary" id="bm_import_extra_submit"><?php esc_html_e( 'Import', 'service-booking' ); ?></button>
            <button type="button" class="button" id="bm_import_extra_cancel"><?php esc_html_e( 'Cancel', 'service-booking' ); ?></button>
        </p>
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

    <!-- Bulk Actions Bar -->
    <?php if ( ! empty( $global_extras ) ) : ?>
    <div id="bm_se_bulk_bar" style="margin-bottom:10px;padding:8px 12px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <select id="bm_se_bulk_action" style="min-width:180px;">
            <option value=""><?php esc_html_e( '— Bulk Actions —', 'service-booking' ); ?></option>
            <option value="bulk_delete"><?php esc_html_e( 'Delete Selected', 'service-booking' ); ?></option>
            <option value="bulk_link"><?php esc_html_e( 'Link to Services', 'service-booking' ); ?></option>
            <option value="bulk_unlink"><?php esc_html_e( 'Unlink from Services', 'service-booking' ); ?></option>
            <option value="bulk_toggle_visibility"><?php esc_html_e( 'Toggle Visibility', 'service-booking' ); ?></option>
        </select>
        <span id="bm_se_bulk_service_wrap" style="display:none;">
            <select id="bm_se_bulk_service_select" multiple style="min-width:250px;height:auto;max-height:100px;">
                <?php if ( ! empty( $all_services ) ) : ?>
                    <?php foreach ( $all_services as $svc ) : ?>
                        <option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <small style="color:#666;"><?php esc_html_e( 'Hold Ctrl/Cmd to select multiple', 'service-booking' ); ?></small>
        </span>
        <span id="bm_se_bulk_visibility_wrap" style="display:none;">
            <select id="bm_se_bulk_visibility_val">
                <option value="1"><?php esc_html_e( 'Visible', 'service-booking' ); ?></option>
                <option value="0"><?php esc_html_e( 'Hidden', 'service-booking' ); ?></option>
            </select>
        </span>
        <button type="button" class="button button-primary" id="bm_se_bulk_apply" disabled><?php esc_html_e( 'Apply', 'service-booking' ); ?></button>
        <span id="bm_se_bulk_count" style="color:#666;font-size:12px;margin-left:8px;"></span>
    </div>

    <!-- Global Extras Listing Table -->
    <table class="wp-list-table widefat striped" id="bm_shared_extras_table">
        <thead>
            <tr>
                <th style="text-align:center;width:30px;"><input type="checkbox" id="bm_se_check_all" title="<?php esc_attr_e( 'Select All', 'service-booking' ); ?>"></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( '#', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Description', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Price', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Max Capacity', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Usage', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Linked Services', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Frontend', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Created', 'service-booking' ); ?></th>
                <th style="text-align:center;font-weight:600;" width="15%"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
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
                data-description="<?php echo esc_attr( isset( $ge->description ) ? $ge->description : '' ); ?>"
                data-price="<?php echo esc_attr( $ge->price ); ?>"
                data-duration="<?php echo esc_attr( $ge->duration_hours ); ?>"
                data-operation="<?php echo esc_attr( $ge->total_operation_hours ); ?>"
                data-max-cap="<?php echo esc_attr( $ge->max_capacity ); ?>"
                data-visible="<?php echo esc_attr( $ge->is_visible_frontend ); ?>"
                data-wc="<?php echo esc_attr( $ge->link_woocommerce ); ?>"
                data-wc-product="<?php echo esc_attr( $ge->wc_product_id ); ?>">
                <td style="text-align:center;"><input type="checkbox" class="bm-se-row-check" value="<?php echo esc_attr( $ge_id ); ?>"></td>
                <td style="text-align:center;"><?php echo esc_html( $idx ); ?></td>
                <td style="text-align:center;"><?php echo esc_html( $ge->name ); ?></td>
                <td style="text-align:center;" title="<?php echo esc_attr( isset( $ge->description ) ? $ge->description : '' ); ?>"><?php echo esc_html( mb_strimwidth( wp_strip_all_tags( isset( $ge->description ) ? $ge->description : '' ), 0, 50, '...' ) ); ?></td>
                <td style="text-align:center;"><?php echo esc_html( $currency_symbol . number_format( (float) $ge->price, 2 ) ); ?></td>
                <td style="text-align:center;"><?php echo esc_html( $ge->max_capacity ); ?></td>
                <td style="text-align:center;">
                    <span class="bm-usage-badge" data-id="<?php echo esc_attr( $ge_id ); ?>" style="display:inline-block;background:#6366f1;color:#fff;padding:2px 10px;border-radius:12px;font-size:12px;cursor:pointer;" title="<?php esc_attr_e( 'Click to load usage stats', 'service-booking' ); ?>">
                        <span class="dashicons dashicons-chart-bar" style="font-size:13px;width:13px;height:13px;vertical-align:middle;margin-right:2px;"></span><span class="bm-usage-val">&mdash;</span>
                    </span>
                </td>
                <td style="text-align:center;">
                    <span class="bm-shared-badge bm-se-manage-links" data-id="<?php echo esc_attr( $ge_id ); ?>" title="<?php echo esc_attr( $svc_tooltip ); ?>" style="display:inline-block;background:#f59e0b;color:#fff;padding:2px 10px;border-radius:12px;font-size:12px;cursor:pointer;">
                        <span class="dashicons dashicons-share" style="font-size:13px;width:13px;height:13px;vertical-align:middle;margin-right:2px;"></span><?php echo esc_html( $svc_count ); ?>
                    </span>
                </td>
                <td style="text-align:center;"><?php echo $ge->is_visible_frontend ? '<span style="color:green;">&#10003;</span>' : '<span style="color:#999;">&#10007;</span>'; ?></td>
                <td style="text-align:center;"><?php echo esc_html( ! empty( $ge->created_at ) ? wp_date( get_option( 'date_format' ), strtotime( $ge->created_at ) ) : '&mdash;' ); ?></td>
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

<!-- Service Link Management Modal -->
<div id="bm_se_link_modal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:100010;overflow-y:auto;">
    <div style="max-width:500px;margin:80px auto;background:#fff;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.3);padding:0;">
        <div style="padding:16px 20px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
            <h3 style="margin:0;"><?php esc_html_e( 'Manage Service Links', 'service-booking' ); ?></h3>
            <button type="button" id="bm_se_link_modal_close" style="border:none;background:none;font-size:20px;cursor:pointer;color:#666;">&times;</button>
        </div>
        <div style="padding:20px;">
            <input type="hidden" id="bm_se_link_modal_ge_id" value="">
            <p style="margin-top:0;"><strong id="bm_se_link_modal_name"></strong></p>
            <div id="bm_se_link_modal_services" style="max-height:300px;overflow-y:auto;margin-bottom:15px;">
                <p style="color:#666;text-align:center;"><?php esc_html_e( 'Loading services...', 'service-booking' ); ?></p>
            </div>
            <div style="text-align:right;">
                <button type="button" class="button" id="bm_se_link_modal_close_btn"><?php esc_html_e( 'Close', 'service-booking' ); ?></button>
            </div>
        </div>
    </div>
</div>
