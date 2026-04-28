<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit_param  = filter_input( INPUT_GET, 'limit', FILTER_VALIDATE_INT );
$limit_param  = $limit_param ? max( 1, min( $limit_param, 200 ) ) : 0;
$limit        = $limit_param ? $limit_param : ( !empty( $dbhandler->get_global_option_value( 'bm_services_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_services_per_page' ) : 10 );
$offset       = ( ( $pagenum - 1 ) * $limit );
$i            = ( 1 + $offset );
$total        = $dbhandler->bm_count( 'SERVICE' );
$services     = $dbhandler->get_all_result( 'SERVICE', '*', 1, 'results', $offset, $limit, 'service_position', false );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );

// Batch-fetch global extras for all services on this page (no N+1).
$service_ids_on_page = array();
if ( ! empty( $services ) ) {
    foreach ( $services as $svc ) {
        $service_ids_on_page[] = (int) $svc->id;
    }
}
$global_extras_by_service = ! empty( $service_ids_on_page )
    ? $dbhandler->batch_get_global_extras_for_services( $service_ids_on_page )
    : array();

// Collect all global extra IDs for batch fetching linked services.
$all_ge_ids = array();
foreach ( $global_extras_by_service as $ge_list ) {
    foreach ( $ge_list as $ge ) {
        $all_ge_ids[] = (int) $ge->id;
    }
}
$all_ge_ids = array_unique( $all_ge_ids );
$services_for_globals = ! empty( $all_ge_ids )
    ? $dbhandler->batch_get_services_for_global_extras( $all_ge_ids )
    : array();

// Allow extensions to filter shared column data.
$shared_column_data = apply_filters( 'bm_services_shared_column_data', array(
    'global_extras_by_service' => $global_extras_by_service,
    'services_for_globals'     => $services_for_globals,
), $service_ids_on_page );
$global_extras_by_service = $shared_column_data['global_extras_by_service'];
$services_for_globals     = $shared_column_data['services_for_globals'];

// P12 — Batch-fetch feature participation flags for all services on this page.
$bm_feature_participation = array();
if ( ! empty( $service_ids_on_page ) ) {
    global $wpdb;
    $plugin_prefix  = $wpdb->prefix . 'sgbm_';
    $ids_placeholder = implode( ',', array_map( 'intval', $service_ids_on_page ) );

    // Chains.
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery -- IDs are already cast to int.
    $chain_rows = $wpdb->get_results( "SELECT service_a_id AS svc_id FROM {$plugin_prefix}service_chains WHERE service_a_id IN ($ids_placeholder) UNION SELECT service_b_id AS svc_id FROM {$plugin_prefix}service_chains WHERE service_b_id IN ($ids_placeholder)" );
    foreach ( (array) $chain_rows as $r ) {
        $bm_feature_participation[ (int) $r->svc_id ]['chain'] = true;
    }

    // Resource Pools.
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
    $pool_rows = $wpdb->get_results( "SELECT service_id AS svc_id FROM {$plugin_prefix}service_resource_pools WHERE service_id IN ($ids_placeholder)" );
    foreach ( (array) $pool_rows as $r ) {
        $bm_feature_participation[ (int) $r->svc_id ]['pool'] = true;
    }

    // Options.
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
    $opt_rows = $wpdb->get_results( "SELECT service_id AS svc_id FROM {$plugin_prefix}option_sets WHERE service_id IN ($ids_placeholder)" );
    foreach ( (array) $opt_rows as $r ) {
        $bm_feature_participation[ (int) $r->svc_id ]['options'] = true;
    }

    // Bundle items.
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
    $bundle_rows = $wpdb->get_results( "SELECT service_id AS svc_id FROM {$plugin_prefix}bundle_items WHERE service_id IN ($ids_placeholder)" );
    foreach ( (array) $bundle_rows as $r ) {
        $bm_feature_participation[ (int) $r->svc_id ]['bundle'] = true;
    }

    // Virtual Service components.
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
    $vs_rows = $wpdb->get_results( "SELECT component_service_id AS svc_id FROM {$plugin_prefix}virtual_service_components WHERE component_service_id IN ($ids_placeholder)" );
    foreach ( (array) $vs_rows as $r ) {
        $bm_feature_participation[ (int) $r->svc_id ]['virtual'] = true;
    }

    // Service as Extra (parent or addon).
    // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery.DirectQuery
    $sae_rows = $wpdb->get_results( "SELECT parent_service_id AS svc_id FROM {$plugin_prefix}service_as_extra WHERE parent_service_id IN ($ids_placeholder) UNION SELECT addon_service_id AS svc_id FROM {$plugin_prefix}service_as_extra WHERE addon_service_id IN ($ids_placeholder)" );
    foreach ( (array) $sae_rows as $r ) {
        $bm_feature_participation[ (int) $r->svc_id ]['extra'] = true;
    }
}

?>

<!-- Services -->
<div class="sg-admin-main-box">
<div class="wrap listing_table" id="service_records_listing">
    <div class="row">
        <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'All Services', 'service-booking' ); ?></h2>
        <?php if ( apply_filters( 'bm_can_add_service', true ) ) : ?>
            <a href="admin.php?page=bm_add_service" class="button-primary"><?php esc_html_e( 'Add Service', 'service-booking' ); ?></a>
        <?php else : ?>
            <button class="button" disabled title="Upgrade to Pro for unlimited services"><?php esc_html_e( 'Add Service (Limit Reached)', 'service-booking' ); ?></button>
        <?php endif; ?>
        <!-- <a href="admin.php?page=bm_add_service" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Service', 'service-booking' ); ?>"><?php esc_html_e( 'Add Service', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a> -->
    </div>
    <?php if ( isset( $services ) && !empty( $services ) ) { ?>
        <!-- Bulk Actions Bar -->
        <div class="bm-bulk-bar" data-table="service" style="margin-bottom:10px;padding:8px 12px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <select class="bm-bulk-action-select" data-table="service" style="min-width:180px;">
                <option value=""><?php esc_html_e( '— Bulk Actions —', 'service-booking' ); ?></option>
                <option value="bulk_delete"><?php esc_html_e( 'Delete Selected', 'service-booking' ); ?></option>
                <option value="bulk_toggle_visibility"><?php esc_html_e( 'Toggle Visibility', 'service-booking' ); ?></option>
            </select>
            <span class="bm-bulk-visibility-wrap" style="display:none;">
                <select class="bm-bulk-visibility-val">
                    <option value="1"><?php esc_html_e( 'Visible', 'service-booking' ); ?></option>
                    <option value="0"><?php esc_html_e( 'Hidden', 'service-booking' ); ?></option>
                </select>
            </span>
            <button type="button" class="button button-primary bm-bulk-apply" data-table="service" disabled><?php esc_html_e( 'Apply', 'service-booking' ); ?></button>
            <span class="bm-bulk-count" style="color:#666;font-size:12px;margin-left:8px;"></span>
            
            <!-- Dynamic Pagination -->
            <div class="bm-dynamic-pagination" style="margin-left:auto;display:flex;align-items:center;gap:6px;">
                <label for="service_items_per_page" style="font-size:13px;color:#3c434a;"><?php esc_html_e( 'Items per page:', 'service-booking' ); ?></label>
                <input type="number" id="service_items_per_page" name="service_items_per_page" min="1" max="200" value="<?php echo esc_attr( $limit ); ?>" style="width:70px;" />
            </div>
        </div>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th style="text-align:center;width:30px;"><input type="checkbox" class="bm-bulk-check-all" data-table="service" title="<?php esc_attr_e( 'Select All', 'service-booking' ); ?>"></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Category', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Show in frontend', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Features', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Shared', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Shortcodes', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody class="service_records">
                <?php
                foreach ( $services as $service ) {
                    ?>
                    <tr class="single_service_record">
                        <form role="form" method="post">
                            <td style="text-align:center;"><input type="checkbox" class="bm-bulk-row-check service-row-check" data-table="service" value="<?php echo esc_attr( $service->id ); ?>"></td>
                            <td style="text-align: center;cursor:move;" data-id="<?php echo esc_attr( $service->id ); ?>" data-order="<?php echo esc_attr( $i ); ?>" class="service_listing_number"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;cursor:move;" title="<?php echo isset( $service->service_name ) ? esc_html( $service->service_name ) : ''; ?>"><?php echo isset( $service->service_name ) ? esc_html( mb_strimwidth( $service->service_name, 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;" title="<?php echo esc_html( $bmrequests->bm_fetch_category_name_by_service_id( $service->id ) ); ?>"><?php echo esc_html( mb_strimwidth( $bmrequests->bm_fetch_category_name_by_service_id( $service->id ), 0, 40, '...' ) ); ?></td>
                            <td style="text-align: center;" class="bm-checkbox-td">
                                <input name="bm_show_service_in_front" type="checkbox" id="bm_show_service_in_front_<?php echo esc_attr( $service->id ); ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $service->is_service_front ), '1' ); ?> onchange="bm_change_service_visibility(this)">
                                <label for="bm_show_service_in_front_<?php echo esc_attr( $service->id ); ?>"></label>
                            </td>
                            <!-- P12: feature-participation badges -->
                            <td style="text-align: center;">
                                <?php
                                $fp = isset( $bm_feature_participation[ (int) $service->id ] ) ? $bm_feature_participation[ (int) $service->id ] : array();
                                $badge_style = 'display:inline-block;border-radius:10px;font-size:10px;padding:2px 7px;margin:1px;color:#fff;cursor:default;';
                                if ( ! empty( $fp ) ) {
                                    if ( ! empty( $fp['chain'] ) ) {
                                        echo '<span style="' . esc_attr( $badge_style ) . 'background:#8b5cf6;" title="' . esc_attr__( 'Part of a Service Chain', 'service-booking' ) . '">Chain</span>';
                                    }
                                    if ( ! empty( $fp['pool'] ) ) {
                                        echo '<span style="' . esc_attr( $badge_style ) . 'background:#0ea5e9;" title="' . esc_attr__( 'Part of a Resource Pool', 'service-booking' ) . '">Pool</span>';
                                    }
                                    if ( ! empty( $fp['options'] ) ) {
                                        echo '<span style="' . esc_attr( $badge_style ) . 'background:#10b981;" title="' . esc_attr__( 'Has Service Options', 'service-booking' ) . '">Options</span>';
                                    }
                                    if ( ! empty( $fp['bundle'] ) ) {
                                        echo '<span style="' . esc_attr( $badge_style ) . 'background:#f59e0b;" title="' . esc_attr__( 'Part of a Bundle', 'service-booking' ) . '">Bundle</span>';
                                    }
                                    if ( ! empty( $fp['virtual'] ) ) {
                                        echo '<span style="' . esc_attr( $badge_style ) . 'background:#ec4899;" title="' . esc_attr__( 'Component of a Virtual Service', 'service-booking' ) . '">VS Comp</span>';
                                    }
                                    if ( ! empty( $fp['extra'] ) ) {
                                        echo '<span style="' . esc_attr( $badge_style ) . 'background:#64748b;" title="' . esc_attr__( 'Used as Service Extra', 'service-booking' ) . '">Extra</span>';
                                    }
                                } else {
                                    echo '<span style="color:#999;font-size:11px;">—</span>';
                                }
                                ?>
                            </td>
                            <td style="text-align: center;" class="bm-shared-extras-td">
                                <?php
                                $svc_id     = (int) $service->id;
                                $svc_ge_list = isset( $global_extras_by_service[ $svc_id ] ) ? $global_extras_by_service[ $svc_id ] : array();
                                $seen_ge_ids = array();
                                if ( ! empty( $svc_ge_list ) ) {
                                    foreach ( $svc_ge_list as $ge ) {
                                        $ge_id = (int) $ge->id;
                                        if ( in_array( $ge_id, $seen_ge_ids, true ) ) {
                                            continue; // Prevent duplicate badges.
                                        }
                                        $seen_ge_ids[] = $ge_id;

                                        // Build tooltip: other services sharing this extra.
                                        $other_services = array();
                                        if ( isset( $services_for_globals[ $ge_id ] ) ) {
                                            foreach ( $services_for_globals[ $ge_id ] as $linked_svc ) {
                                                if ( (int) $linked_svc->service_id !== $svc_id ) {
                                                    $other_services[] = esc_html( $linked_svc->service_name );
                                                }
                                            }
                                        }
                                        $tooltip_parts = array();
                                        if ( ! empty( $other_services ) ) {
                                            $tooltip_parts[] = esc_attr__( 'Also shared with: ', 'service-booking' ) . implode( ', ', $other_services );
                                        }
                                        $tooltip_parts[] = esc_attr__( 'Price: ', 'service-booking' ) . esc_attr( $ge->price );
                                        $tooltip_parts[] = esc_attr__( 'Capacity: ', 'service-booking' ) . esc_attr( $ge->max_capacity );
                                        $tooltip = implode( ' | ', $tooltip_parts );
                                        ?>
                                        <span class="bm-shared-badge" title="<?php echo esc_attr( $tooltip ); ?>" style="display:inline-block;background:#f59e0b;color:#fff;padding:2px 8px;border-radius:12px;font-size:11px;margin:2px;cursor:help;">
                                            <span class="dashicons dashicons-share" style="font-size:13px;width:13px;height:13px;vertical-align:middle;margin-right:2px;"></span><?php echo esc_html( $ge->name ); ?>
                                        </span>
                                        <?php
                                    }
                                } else {
                                    echo '<span style="color:#999;font-size:11px;">—</span>';
                                }
                                ?>
                            </td>
                            <td style="text-align: center;">
                                <div class="copyMessagetooltip" style="margin-bottom: 5px;">
                                    <input class="copytextTooltip" value="<?php echo esc_attr( '[sgbm_single_service id="' . $service->id . '"]' ); ?>" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                                    <span class="tooltiptext"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                                    <button type="button" class="bm-info-button" data-shortcode="sgbm_single_service" title="<?php esc_html_e( 'Shortcode Info', 'service-booking' ); ?>">i</button>
                                </div>
                                <div class="copyMessagetooltip">
                                    <input class="copytextTooltip" value="<?php echo esc_attr( '[sgbm_single_service_calendar id="' . $service->id . '"]' ); ?>" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                                    <span class="tooltiptext"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                                    <button type="button" class="bm-info-button" data-shortcode="sgbm_single_service_calendar" title="<?php esc_html_e( 'Shortcode Info', 'service-booking' ); ?>">i</button>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" name="editsvc" class="edit-button" id="editsvc" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $service->id ) ? esc_attr( $service->id ) : ''; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" name="delsvc" class="delete-button" id="delsvc" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $service->id ) ? esc_attr( $service->id ) : ''; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                            </td>
                        </form>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="service_pagination"><?php echo !empty( $pagination ) ? wp_kses_post( $pagination ) : ''; ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Services Found', 'service-booking' ); ?></p>
            </div>
        </div>
    <?php } ?>
    
    <!-- Global Shortcodes Section -->
    <h2 class="title" style="font-weight: bold; margin-top: 30px;"><?php esc_html_e( 'Global Shortcodes', 'service-booking' ); ?></h2>
    <table class="wp-list-table widefat striped">
        <thead>
            <tr>
                <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Shortcode', 'service-booking' ); ?></th>
                <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Info', 'service-booking' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="text-align: center;">
                    <div class="copyMessagetooltip">
                        <input class="copytextTooltip" value="[sgbm_service_search]" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                        <span class="tooltiptext"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="bm-info-button" data-shortcode="sgbm_service_search" title="<?php esc_html_e( 'Shortcode Info', 'service-booking' ); ?>">i</button>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <div class="copyMessagetooltip">
                        <input class="copytextTooltip" value="[sgbm_service_fullcalendar]" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                        <span class="tooltiptext"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="bm-info-button" data-shortcode="sgbm_service_fullcalendar" title="<?php esc_html_e( 'Shortcode Info', 'service-booking' ); ?>">i</button>
                </td>
            </tr>
            <tr>
                <td style="text-align: center;">
                    <div class="copyMessagetooltip">
                        <input class="copytextTooltip" value="[sgbm_service_timeslot_fullcalendar]" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                        <span class="tooltiptext"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                    </div>
                </td>
                <td style="text-align: center;">
                    <button type="button" class="bm-info-button" data-shortcode="sgbm_service_timeslot_fullcalendar" title="<?php esc_html_e( 'Shortcode Info', 'service-booking' ); ?>">i</button>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<!-- Shortcode Info Modal -->
<div id="bm-shortcode-info-modal" class="bm-shortcode-modal" style="display:none;">
    <div class="bm-shortcode-modal-content">
        <span class="bm-close-shortcode-modal">&times;</span>
        <h2 id="bm-shortcode-title"></h2>
        <div id="bm-shortcode-description"></div>
        <h3><?php esc_html_e( 'Attributes', 'service-booking' ); ?></h3>
        <table id="bm-shortcode-attributes" class="widefat">
            <thead>
                <tr>
                    <th><?php esc_html_e( 'Attribute', 'service-booking' ); ?></th>
                    <th><?php esc_html_e( 'Description', 'service-booking' ); ?></th>
                    <th><?php esc_html_e( 'Default', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <!-- Will be populated by JavaScript -->
            </tbody>
        </table>
        <h3><?php esc_html_e( 'Examples', 'service-booking' ); ?></h3>
        <pre id="bm-shortcode-examples"></pre>
    </div>
</div>

<input type="hidden" id="service_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__shakeY" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>

<script>
var bm_shortcode_info = {
    'sgbm_single_service': {
        title: '<?php esc_html_e( 'Single Service', 'service-booking' ); ?>',
        description: '<?php esc_html_e( 'Displays a single service with details.', 'service-booking' ); ?>',
        attributes: [
            {name: 'id', description: '<?php esc_html_e( 'Service ID', 'service-booking' ); ?>', default: '<?php esc_html_e( 'Required', 'service-booking' ); ?>'}
        ],
        examples: ['[sgbm_single_service id="1"]']
    },
    'sgbm_single_service_calendar': {
        title: '<?php esc_html_e( 'Single Service Calendar', 'service-booking' ); ?>',
        description: '<?php esc_html_e( 'Displays booking calendar for a single service.', 'service-booking' ); ?>',
        attributes: [
            {name: 'id', description: '<?php esc_html_e( 'Service ID', 'service-booking' ); ?>', default: '<?php esc_html_e( 'Required', 'service-booking' ); ?>'}
        ],
        examples: ['[sgbm_single_service_calendar id="1"]']
    },
    'sgbm_service_search': {
        title: '<?php esc_html_e( 'Service Search', 'service-booking' ); ?>',
        description: '<?php esc_html_e( 'Displays default service shortcode and filters.', 'service-booking' ); ?>',
        attributes: [
            {name: 'show_date', description: '<?php esc_html_e( 'Show date selector', 'service-booking' ); ?>', default: 'default'},
            {name: 'show_category_filter', description: '<?php esc_html_e( 'Show category filter', 'service-booking' ); ?>', default: 'default'},
            {name: 'show_service_filter', description: '<?php esc_html_e( 'Show service filter', 'service-booking' ); ?>', default: 'default'},
            {name: 'show_service_sorting', description: '<?php esc_html_e( 'Show sorting options', 'service-booking' ); ?>', default: 'default'},
            {name: 'show_grid_list_button', description: '<?php esc_html_e( 'Show grid/list toggle', 'service-booking' ); ?>', default: 'default'},
            {name: 'show_list_button', description: '<?php esc_html_e( 'Show list view button', 'service-booking' ); ?>', default: 'default'},
            {name: 'show_service_limit', description: '<?php esc_html_e( 'Show results per page selector', 'service-booking' ); ?>', default: 'default'},
            {name: 'service_view_type', description: '<?php esc_html_e( 'Default view type (grid/list)', 'service-booking' ); ?>', default: 'grid'}
        ],
        examples: [
            '[sgbm_service_search show_date="true" show_category_filter="true"]',
            '[sgbm_service_search show_service_filter="false" show_service_sorting="false"]',
            '[sgbm_service_search show_date="true" show_category_filter="false" show_grid_list_button="true" show_list_button="false"]',
            '[sgbm_service_search show_date="default" show_service_filter="true" show_category_filter="false" show_service_sorting="false" show_grid_list_button="false" show_service_limit="default" service_view_type="grid"]'
        ]
    },
    'sgbm_service_timeslot_fullcalendar': {
        title: '<?php esc_html_e( 'Service Timeslot Full Calendar', 'service-booking' ); ?>',
        description: '<?php esc_html_e( 'Displays timeslot-based full calendar for services.', 'service-booking' ); ?>',
        attributes: [
            {name: 'show_filters', description: '<?php esc_html_e( 'Show all filters', 'service-booking' ); ?>', default: 'true'},
            {name: 'show_category_filter', description: '<?php esc_html_e( 'Show category filter', 'service-booking' ); ?>', default: 'true'},
            {name: 'show_service_filter', description: '<?php esc_html_e( 'Show service filter', 'service-booking' ); ?>', default: 'true'},
            {name: 'cat_ids', description: '<?php esc_html_e( 'Comma-separated category IDs', 'service-booking' ); ?>', default: '[]'}
        ],
        examples: [
            '[sgbm_service_timeslot_fullcalendar]',
            '[sgbm_service_timeslot_fullcalendar show_filters="false"]',
            '[sgbm_service_timeslot_fullcalendar show_category_filter="false"]',
            '[sgbm_service_timeslot_fullcalendar show_service_filter="false"]',
            '[sgbm_service_timeslot_fullcalendar cat_ids="1,2"]'
        ]
    },
    'sgbm_service_fullcalendar': {
        title: '<?php esc_html_e( 'Service Full Calendar', 'service-booking' ); ?>',
        description: '<?php esc_html_e( 'Displays a full calendar view of services.', 'service-booking' ); ?>',
        attributes: [
            {name: 'show_filters', description: '<?php esc_html_e( 'Show all filters', 'service-booking' ); ?>', default: 'true'},
            {name: 'show_category_filter', description: '<?php esc_html_e( 'Show category filter', 'service-booking' ); ?>', default: 'true'},
            {name: 'show_service_filter', description: '<?php esc_html_e( 'Show service filter', 'service-booking' ); ?>', default: 'true'},
            {name: 'cat_ids', description: '<?php esc_html_e( 'Comma-separated category IDs', 'service-booking' ); ?>', default: '[]'}
        ],
        examples: [
            '[sgbm_service_fullcalendar]',
            '[sgbm_service_fullcalendar show_filters="false"]',
            '[sgbm_service_fullcalendar show_category_filter="false"]',
            '[sgbm_service_fullcalendar show_service_filter="false"]',
            '[sgbm_service_fullcalendar cat_ids="1,2"]'
        ]
    }
};

jQuery(document).ready(function($) {
    $('.bm-info-button').on('click', function(e) {
        e.preventDefault();
        var shortcode = $(this).data('shortcode');
        var info = bm_shortcode_info[shortcode];
        
        if (info) {
            $('#bm-shortcode-title').text(info.title);
            $('#bm-shortcode-description').text(info.description);
            
            var attributesBody = $('#bm-shortcode-attributes tbody');
            attributesBody.empty();
            
            if (info.attributes.length > 0) {
                $.each(info.attributes, function(i, attr) {
                    attributesBody.append(
                        '<tr>' +
                        '<td>' + attr.name + '</td>' +
                        '<td>' + attr.description + '</td>' +
                        '<td>' + attr.default + '</td>' +
                        '</tr>'
                    );
                });
            } else {
                attributesBody.append(
                    '<tr><td colspan="3"><?php esc_html_e( 'No attributes available', 'service-booking' ); ?></td></tr>'
                );
            }
            
            var examplesHtml = info.examples.join('\n');
            $('#bm-shortcode-examples').text(examplesHtml);
            
            $('#bm-shortcode-info-modal').show();
        }
    });
    
    $('.bm-close-shortcode-modal').on('click', function() {
        $('#bm-shortcode-info-modal').hide();
    });
    
    $(window).on('click', function(event) {
        if ($(event.target).is('#bm-shortcode-info-modal')) {
            $('#bm-shortcode-info-modal').hide();
        }
    });
});
</script>

