<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$bm_activator = new Booking_Management_Activator();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit_param  = filter_input( INPUT_GET, 'limit', FILTER_VALIDATE_INT );
$limit_param  = $limit_param ? min( $limit_param, 100 ) : 0;
$limit        = $limit_param ? $limit_param : ( ! empty( $dbhandler->get_global_option_value( 'bm_payment_logs_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_payment_logs_per_page' ) : 20 );
$offset       = ( ( $pagenum - 1 ) * $limit );

$bookings_table     = esc_sql( $bm_activator->get_db_table_name( 'BOOKING' ) );
$transactions_table = esc_sql( $bm_activator->get_db_table_name( 'TRANSACTIONS' ) );
$failed_table       = esc_sql( $bm_activator->get_db_table_name( 'FAILED_TRANSACTIONS' ) );
$customers_table    = esc_sql( $bm_activator->get_db_table_name( 'CUSTOMERS' ) );

// Build WHERE conditions
$where = array( '1=1' );

$search_val        = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
$booking_id_filter = isset( $_REQUEST['booking_id'] ) ? absint( $_REQUEST['booking_id'] ) : 0;
$payment_filter    = isset( $_REQUEST['payment_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['payment_status'] ) ) : 'all';

if ( ! empty( $search_val ) ) {
	$search  = '%' . $dbhandler->esc_like( $search_val ) . '%';
	$where[] = $dbhandler->prepare_sql( '(b.service_name LIKE %s OR c.customer_email LIKE %s)', $search, $search );
}
if ( ! empty( $booking_id_filter ) ) {
	$where[] = $dbhandler->prepare_sql( 'b.id = %d', $booking_id_filter );
}
if ( ! empty( $payment_filter ) && $payment_filter !== 'all' ) {
	$where[] = $dbhandler->prepare_sql( 'payment_status = %s', $payment_filter );
}
$month_filter = isset( $_REQUEST['m'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['m'] ) ) : '';
if ( ! empty( $month_filter ) ) {
	$year    = absint( substr( $month_filter, 0, 4 ) );
	$month   = absint( substr( $month_filter, 4, 2 ) );
	$where[] = $dbhandler->prepare_sql( '(YEAR(created_at) = %d AND MONTH(created_at) = %d)', $year, $month );
}

$where_sql = implode( ' AND ', $where );

$success_sql = "SELECT
    t.id,
    t.booking_id,
    b.service_name,
    cust.customer_name,
    cust.customer_email,
    t.paid_amount as amount,
    t.paid_amount_currency as currency,
    t.payment_status,
    t.payment_method,
    t.transaction_id,
    NULL as refund_status,
    NULL as error_message,
    'transaction' as source,
    t.transaction_created_at as created_at
FROM $transactions_table t
LEFT JOIN $bookings_table b ON t.booking_id = b.id
LEFT JOIN $customers_table cust ON t.customer_id = cust.id
WHERE $where_sql";

$failed_sql = "SELECT
    f.id,
    b.id as booking_id,
    b.service_name,
    cust.customer_name,
    cust.customer_email,
    f.amount/100 as amount,
    f.amount_currency as currency,
    f.payment_status,
    NULL as payment_method,
    f.transaction_id,
    f.refund_status,
    f.error_message,
    'failed' as source,
    f.created_at
FROM $failed_table f
LEFT JOIN $bookings_table b ON f.booking_key = b.booking_key
LEFT JOIN $customers_table cust ON f.customer_id = cust.id
WHERE $where_sql";

// Combine with UNION
$union_sql    = $dbhandler->prepare_sql( "( $success_sql ) UNION ( $failed_sql ) ORDER BY created_at DESC LIMIT %d OFFSET %d", $limit, $offset );
$payment_logs = $dbhandler->get_results_raw( $union_sql ) ?? array();

// Total count
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- where_sql is built via prepare_sql() above.
$total_success = $dbhandler->get_var_raw( "SELECT COUNT(*) FROM $transactions_table t LEFT JOIN $bookings_table b ON t.booking_id = b.id WHERE $where_sql" );
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- where_sql is built via prepare_sql() above.
$total_failed  = $dbhandler->get_var_raw( "SELECT COUNT(*) FROM $failed_table f LEFT JOIN $bookings_table b ON f.booking_key = b.booking_key WHERE $where_sql" );
$total         = intval( $total_success ) + intval( $total_failed );
$num_of_pages  = ceil( $total / $limit );
$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
?>

<div class="sg-admin-main-box">
<div class="wrap listing_table">
    <div class="row">
        <div>
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Payment Logs', 'service-booking' ); ?></h2>
        </div>
    </div>

    <!-- Filters -->
    <div style="margin-bottom:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
        <form method="get" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <input type="hidden" name="page" value="bm_payment_logs" />
            <input type="text" name="s" placeholder="<?php esc_attr_e( 'Search Service/Customer', 'service-booking' ); ?>" value="<?php echo esc_attr( $search_val ); ?>" />
            <input type="text" name="booking_id" placeholder="<?php esc_attr_e( 'Booking ID', 'service-booking' ); ?>" value="<?php echo esc_attr( $booking_id_filter ? $booking_id_filter : '' ); ?>" size="5" />
            <select name="payment_status">
                <option value="all"><?php esc_html_e( 'All statuses', 'service-booking' ); ?></option>
                <option value="succeeded" <?php selected( $payment_filter, 'succeeded' ); ?>><?php esc_html_e( 'Succeeded', 'service-booking' ); ?></option>
                <option value="free" <?php selected( $payment_filter, 'free' ); ?>><?php esc_html_e( 'Free', 'service-booking' ); ?></option>
                <option value="requires_capture" <?php selected( $payment_filter, 'requires_capture' ); ?>><?php esc_html_e( 'Requires Capture', 'service-booking' ); ?></option>
                <option value="cancelled" <?php selected( $payment_filter, 'cancelled' ); ?>><?php esc_html_e( 'Canceled', 'service-booking' ); ?></option>
                <option value="requires_payment_method" <?php selected( $payment_filter, 'requires_payment_method' ); ?>><?php esc_html_e( 'Requires Payment Method', 'service-booking' ); ?></option>
            </select>
            <button type="submit" class="button"><?php esc_html_e( 'Filter', 'service-booking' ); ?></button>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bm_payment_logs' ) ); ?>" class="button"><?php esc_html_e( 'Reset', 'service-booking' ); ?></a>
        </form>
    </div>

    <?php if ( ! empty( $payment_logs ) ) { ?>
        <!-- Bulk Actions Bar -->
        <div class="bm-bulk-bar" data-table="payment_log" style="margin-bottom:10px;padding:8px 12px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <select class="bm-bulk-action-select" data-table="payment_log" style="min-width:180px;">
                <option value=""><?php esc_html_e( '— Bulk Actions —', 'service-booking' ); ?></option>
                <option value="bulk_delete"><?php esc_html_e( 'Delete Selected', 'service-booking' ); ?></option>
            </select>
            <button type="button" class="button button-primary bm-bulk-apply" data-table="payment_log" disabled><?php esc_html_e( 'Apply', 'service-booking' ); ?></button>
            <span class="bm-bulk-count" style="color:#666;font-size:12px;margin-left:8px;"></span>
            
            <!-- Dynamic Pagination -->
            <div class="bm-dynamic-pagination" style="margin-left:auto;display:flex;align-items:center;gap:6px;">
                <label for="payment_log_items_per_page" style="font-size:13px;color:#3c434a;"><?php esc_html_e( 'Items per page:', 'service-booking' ); ?></label>
                <select id="payment_log_items_per_page" name="payment_log_items_per_page" style="min-width:80px;">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th style="text-align:center;width:30px;"><input type="checkbox" class="bm-bulk-check-all" data-table="payment_log" title="<?php esc_attr_e( 'Select All', 'service-booking' ); ?>"></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'ID', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Booking ID', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Service', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Customer', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Amount', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Currency', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Method', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Transaction ID', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Refund', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Error', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Source', 'service-booking' ); ?></th>
                    <th style="text-align:center;font-weight:600;"><?php esc_html_e( 'Date', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $payment_logs as $log ) {
                    // Booking link
                    $booking_display = '—';
                    if ( ! empty( $log->booking_id ) ) {
                        $booking_url     = admin_url( 'admin.php?page=bm_single_order&booking_id=' . $log->booking_id );
                        $booking_display = '<a href="' . esc_url( $booking_url ) . '">' . esc_html( $log->booking_id ) . '</a>';
                    }

                    // Customer
                    $customer_display = '—';
                    if ( ! empty( $log->customer_email ) ) {
                        $customer_display = esc_html( $log->customer_name . ' <' . $log->customer_email . '>' );
                    }

                    // Payment status
                    $status = $log->payment_status;
                    if ( $status == 'succeeded' || $status == 'free' ) {
                        $status_display = '<span style="color:green;">' . esc_html( $status ) . '</span>';
                    } elseif ( $status == 'requires_capture' ) {
                        $status_display = '<span style="color:orange;">' . esc_html( $status ) . '</span>';
                    } elseif ( $status == 'canceled' ) {
                        $status_display = '<span style="color:red;">' . esc_html( $status ) . '</span>';
                    } else {
                        $status_display = esc_html( $status );
                    }

                    // Refund
                    $refund_display = '—';
                    if ( ! empty( $log->refund_status ) && $log->refund_status != 'not_required' ) {
                        $refund_display = esc_html( $log->refund_status );
                    }

                    // Error
                    $error_display = ! empty( $log->error_message ) ? esc_html( $log->error_message ) : '—';

                    // Source
                    $source_display = $log->source == 'transaction' ? esc_html__( 'Success', 'service-booking' ) : esc_html__( 'Failed', 'service-booking' );
                    ?>
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="bm-bulk-row-check payment_log-row-check" data-table="payment_log" value="<?php echo esc_attr( $log->id ); ?>"></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->id ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $booking_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->service_name ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $customer_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( number_format( (float) ( $log->amount ?? 0 ), 2 ) ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->currency ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $status_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->payment_method ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->transaction_id ?? '' ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $refund_display ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $error_display ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $source_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->created_at ?? '' ); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <div class="payment_logs_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Payment Logs Found', 'service-booking' ); ?></p>
            </div>
        </div>
    <?php } ?>
</div>

<div class="loader_modal"></div>
</div>
