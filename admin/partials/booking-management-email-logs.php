<?php
$dbhandler   = new BM_DBhandler();
$bmrequests  = new BM_Request();
$pagenum     = filter_input( INPUT_GET, 'pagenum' );
$pagenum     = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit_param = filter_input( INPUT_GET, 'limit', FILTER_VALIDATE_INT );
$limit_param = $limit_param ? max( 1, min( $limit_param, 200 ) ) : 0;
$limit       = $limit_param ? $limit_param : ( ! empty( $dbhandler->get_global_option_value( 'bm_email_logs_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_email_logs_per_page' ) : 20 );
$offset      = ( ( $pagenum - 1 ) * $limit );

$where      = array();
$additional = '';

// Search
$search_val = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
if ( ! empty( $search_val ) ) {
	$search      = '%' . $dbhandler->esc_like( $search_val ) . '%';
	$additional .= $dbhandler->prepare_sql(
		' AND (e.mail_to LIKE %s OR e.mail_sub LIKE %s)',
		$search,
		$search
	);
}

// Status filter
$status_filter = isset( $_REQUEST['status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['status'] ) ) : 'all';
if ( ! empty( $status_filter ) && $status_filter !== 'all' ) {
	$where['e.status'] = array( '=' => intval( $status_filter ) );
}

// Booking ID filter
$booking_id_filter = isset( $_REQUEST['booking_id'] ) ? absint( $_REQUEST['booking_id'] ) : 0;
if ( ! empty( $booking_id_filter ) ) {
	$where['e.module_id'] = array( '=' => $booking_id_filter );
}

// Email type filter
$mail_type_filter = isset( $_REQUEST['mail_type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['mail_type'] ) ) : 'all';
if ( ! empty( $mail_type_filter ) && $mail_type_filter !== 'all' ) {
	$where['e.mail_type'] = array( '=' => $mail_type_filter );
}

// Month filter
$month_filter = isset( $_REQUEST['m'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['m'] ) ) : '';
if ( ! empty( $month_filter ) ) {
	// Accept both YYYYMM and YYYY-MM formats.
	$clean_month = str_replace( '-', '', $month_filter );
	$year        = absint( substr( $clean_month, 0, 4 ) );
	$month       = absint( substr( $clean_month, 4, 2 ) );
	if ( $year > 0 && $month > 0 && $month <= 12 ) {
		$additional .= $dbhandler->prepare_sql(
			' AND YEAR(e.created_at) = %d AND MONTH(e.created_at) = %d',
			$year,
			$month
		);
	}
}

// If we have additional raw SQL but no WHERE conditions, add a dummy always-true condition
if ( ! empty( $additional ) && empty( $where ) ) {
	$where['e.id'] = array( '>' => 0 );
}

// Joins
$joins = array(
	array(
		'table' => 'BOOKING',
		'alias' => 'b',
		'on'    => 'e.module_id = b.id AND e.module_type = "BOOKING"',
		'type'  => 'LEFT',
	),
	array(
		'table' => 'FAILED_TRANSACTIONS',
		'alias' => 'ft',
		'on'    => 'e.module_id = ft.id AND e.module_type = "FAILED_TRANSACTIONS"',
		'type'  => 'LEFT',
	),
	array(
		'table' => 'BOOKING',
		'alias' => 'b2',
		'on'    => 'ft.booking_key = b2.booking_key',
		'type'  => 'LEFT',
	),
);

$columns_sql = 'e.*, COALESCE(b.service_name, b2.service_name) as service_name';

// Get paginated results
$email_logs = $dbhandler->get_results_with_join(
	array( 'EMAILS', 'e' ),
	$columns_sql,
	$joins,
	$where,
	'results',
	$offset,
	$limit,
	'e.created_at',
	true,
	$additional,
	false,
	10000,
	OBJECT
);

// Get total count
$total = $dbhandler->get_results_with_join(
	array( 'EMAILS', 'e' ),
	'COUNT(*) as total',
	$joins,
	$where,
	'var',
	0,
	false,
	null,
	false,
	$additional,
	false,
	10000,
	OBJECT
);

$total        = intval( $total );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
?>

<div class="sg-admin-main-box">
<div class="wrap listing_table">
    <div class="row">
        <div>
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Email Logs', 'service-booking' ); ?></h2>
        </div>
    </div>

    <!-- Filters -->
    <div style="margin-bottom:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <form method="get" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="hidden" name="page" value="bm_email_logs" />
            <input type="text" name="s" placeholder="<?php esc_attr_e( 'Search Recipient/Subject', 'service-booking' ); ?>" value="<?php echo esc_attr( $search_val ); ?>" />
            <select name="status">
                <option value="all"><?php esc_html_e( 'All statuses', 'service-booking' ); ?></option>
                <option value="1" <?php selected( $status_filter, '1' ); ?>><?php esc_html_e( 'Success', 'service-booking' ); ?></option>
                <option value="0" <?php selected( $status_filter, '0' ); ?>><?php esc_html_e( 'Failed', 'service-booking' ); ?></option>
            </select>
            <select name="mail_type">
                <option value="all"><?php esc_html_e( 'All email types', 'service-booking' ); ?></option>
                <option value="new_order" <?php selected( $mail_type_filter, 'new_order' ); ?>><?php esc_html_e( 'New order', 'service-booking' ); ?></option>
                <option value="cancel_order" <?php selected( $mail_type_filter, 'cancel_order' ); ?>><?php esc_html_e( 'Cancel order', 'service-booking' ); ?></option>
                <option value="refund_order" <?php selected( $mail_type_filter, 'refund_order' ); ?>><?php esc_html_e( 'Refund order', 'service-booking' ); ?></option>
                <option value="approved_order" <?php selected( $mail_type_filter, 'approved_order' ); ?>><?php esc_html_e( 'Approved order', 'service-booking' ); ?></option>
                <option value="failed_order" <?php selected( $mail_type_filter, 'failed_order' ); ?>><?php esc_html_e( 'Failed order', 'service-booking' ); ?></option>
                <option value="gift_voucher" <?php selected( $mail_type_filter, 'gift_voucher' ); ?>><?php esc_html_e( 'Gift voucher', 'service-booking' ); ?></option>
                <option value="new_request" <?php selected( $mail_type_filter, 'new_request' ); ?>><?php esc_html_e( 'New request', 'service-booking' ); ?></option>
                <option value="voucher_redeem" <?php selected( $mail_type_filter, 'voucher_redeem' ); ?>><?php esc_html_e( 'Voucher redeem', 'service-booking' ); ?></option>
                <option value="failed_order_refund" <?php selected( $mail_type_filter, 'failed_order_refund' ); ?>><?php esc_html_e( 'Failed order refund', 'service-booking' ); ?></option>
            </select>
            <input type="month" name="m" value="<?php echo esc_attr( $month_filter ); ?>" placeholder="<?php esc_attr_e( 'Month', 'service-booking' ); ?>" style="min-width:140px;" />
            <input type="text" name="booking_id" placeholder="<?php esc_attr_e( 'Booking ID', 'service-booking' ); ?>" value="<?php echo esc_attr( $booking_id_filter ? $booking_id_filter : '' ); ?>" size="5" />
            <button type="submit" class="button"><?php esc_html_e( 'Filter', 'service-booking' ); ?></button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bm_email_logs' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'service-booking' ); ?></a>
        </form>
    </div>

    <?php if ( ! empty( $email_logs ) ) { ?>
        <!-- Bulk Actions Bar -->
        <div class="bm-bulk-bar" data-table="email_log" style="margin-bottom:10px;padding:8px 12px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <select class="bm-bulk-action-select" data-table="email_log" style="min-width:180px;">
                <option value=""><?php esc_html_e( '— Bulk Actions —', 'service-booking' ); ?></option>
                <option value="bulk_delete"><?php esc_html_e( 'Delete Selected', 'service-booking' ); ?></option>
            </select>
            <button type="button" class="button button-primary bm-bulk-apply" data-table="email_log" disabled><?php esc_html_e( 'Apply', 'service-booking' ); ?></button>
            <span class="bm-bulk-count" style="color:#666;font-size:12px;margin-left:8px;"></span>
            
            <!-- Dynamic Pagination -->
            <div class="bm-dynamic-pagination" style="margin-left:auto;display:flex;align-items:center;gap:6px;">
                <label for="email_log_items_per_page" style="font-size:13px;color:#3c434a;"><?php esc_html_e( 'Items per page:', 'service-booking' ); ?></label>
                <input type="number" id="email_log_items_per_page" name="email_log_items_per_page" min="1" max="200" value="<?php echo esc_attr( $limit ); ?>" style="width:70px;" />
            </div>
        </div>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th style="text-align:center;width:30px;"><input type="checkbox" class="bm-bulk-check-all" data-table="email_log" title="<?php esc_attr_e( 'Select All', 'service-booking' ); ?>"></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'ID', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Booking ID', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Service', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Email Type', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Recipient', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Subject', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Language', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Error', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Date', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $email_logs as $log ) {
                    // Service name
                    $service_display = '—';
                    if ( ! empty( $log->service_name ) ) {
                        $service_display = esc_html( $log->service_name );
                    } elseif ( ! empty( $log->module_type ) ) {
                        $service_display = sprintf( esc_html__( 'Record #%d (missing)', 'service-booking' ), $log->module_id );
                    }

                    // Booking link
                    $booking_display = '—';
                    if ( ! empty( $log->module_id ) ) {
                        $booking_url     = admin_url( 'admin.php?page=bm_single_order&booking_id=' . $log->module_id );
                        $booking_display = '<a href="' . esc_url( $booking_url ) . '">' . esc_html( $log->module_id ) . '</a>';
                    }

                    // Email type
                    $email_type = $bmrequests->bm_fetch_email_type( $log->mail_type );

                    // Status
                    if ( $log->status == 1 ) {
                        $status_display = '<span style="color:green;">' . esc_html__( 'Success', 'service-booking' ) . '</span>';
                    } else {
                        $status_display = '<span style="color:red;">' . esc_html__( 'Failed', 'service-booking' ) . '</span>';
                    }

                    // Error message
                    $error_display = '—';
                    if ( ! empty( $log->error_message ) ) {
                        $maybe_unserialized = maybe_unserialize( $log->error_message );
                        if ( is_array( $maybe_unserialized ) ) {
                            $lines = array();
                            foreach ( $maybe_unserialized as $err_key => $error ) {
                                $lines[] = '<strong>' . esc_html( ucfirst( $err_key ) ) . ':</strong> ' . esc_html( $error );
                            }
                            $error_display = implode( '<br>', $lines );
                        } else {
                            $error_display = esc_html( $maybe_unserialized );
                        }
                    }
                    ?>
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="bm-bulk-row-check email_log-row-check" data-table="email_log" value="<?php echo esc_attr( $log->id ); ?>"></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->id ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $booking_display ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $service_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $email_type ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->mail_to ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->mail_sub ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->mail_lang ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $status_display ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $error_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->created_at ?? '' ); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div class="email_logs_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Email Logs Found', 'service-booking' ); ?></p>
            </div>
        </div>
    <?php } ?>
</div>

<div class="loader_modal"></div>
</div>
