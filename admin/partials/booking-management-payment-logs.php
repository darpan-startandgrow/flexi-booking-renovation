<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$bm_activator = new Booking_Management_Activator();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit_param  = filter_input( INPUT_GET, 'limit', FILTER_VALIDATE_INT );
$limit_param  = $limit_param ? max( 1, min( $limit_param, 200 ) ) : 0;
$limit        = $limit_param ? $limit_param : ( ! empty( $dbhandler->get_global_option_value( 'bm_payment_logs_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_payment_logs_per_page' ) : 20 );
$offset       = ( ( $pagenum - 1 ) * $limit );

$bookings_table     = esc_sql( $bm_activator->get_db_table_name( 'BOOKING' ) );
$transactions_table = esc_sql( $bm_activator->get_db_table_name( 'TRANSACTIONS' ) );
$failed_table       = esc_sql( $bm_activator->get_db_table_name( 'FAILED_TRANSACTIONS' ) );
$customers_table    = esc_sql( $bm_activator->get_db_table_name( 'CUSTOMERS' ) );

// Build WHERE conditions
// Common conditions that apply identically to both subqueries.
$where_common = array( '1=1' );
// Per-table extra conditions (search, status, month differ between tables).
$where_t_extra = '';
$where_f_extra = '';

$search_val        = isset( $_REQUEST['s'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['s'] ) ) : '';
$booking_id_filter = isset( $_REQUEST['booking_id'] ) ? absint( $_REQUEST['booking_id'] ) : 0;
$payment_filter    = isset( $_REQUEST['payment_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['payment_status'] ) ) : 'all';

if ( ! empty( $search_val ) ) {
	$search          = '%' . $dbhandler->esc_like( $search_val ) . '%';
	$where_t_extra  .= $dbhandler->prepare_sql( ' AND (b.service_name LIKE %s OR cust.customer_email LIKE %s)', $search, $search );
	// For failed transactions also search inside serialized booking_data / customer_data
	// because the BOOKING / CUSTOMERS JOINs may return NULL.
	$where_f_extra  .= $dbhandler->prepare_sql(
		' AND (b.service_name LIKE %s OR cust.customer_email LIKE %s OR f.booking_data LIKE %s OR f.customer_data LIKE %s)',
		$search, $search, $search, $search
	);
}
if ( ! empty( $booking_id_filter ) ) {
	$where_common[] = $dbhandler->prepare_sql( 'b.id = %d', $booking_id_filter );
}
if ( ! empty( $payment_filter ) && $payment_filter !== 'all' ) {
	if ( $payment_filter === 'failed' ) {
		// 'failed' in TRANSACTIONS: payment_status = 'failed' (webhook-reported).
		// All FAILED_TRANSACTIONS records represent failures — no extra filter needed.
		$where_t_extra .= $dbhandler->prepare_sql( ' AND t.payment_status = %s', 'failed' );
	} else {
		$where_t_extra .= $dbhandler->prepare_sql( ' AND t.payment_status = %s', $payment_filter );
		$where_f_extra .= $dbhandler->prepare_sql( ' AND f.payment_status = %s', $payment_filter );
	}
}
$month_filter = isset( $_REQUEST['m'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['m'] ) ) : '';
if ( ! empty( $month_filter ) ) {
	$year           = absint( substr( $month_filter, 0, 4 ) );
	$month          = absint( substr( $month_filter, 4, 2 ) );
	// Transactions table uses transaction_created_at; failed_transactions uses created_at.
	$where_t_extra .= $dbhandler->prepare_sql( ' AND (YEAR(t.transaction_created_at) = %d AND MONTH(t.transaction_created_at) = %d)', $year, $month );
	$where_f_extra .= $dbhandler->prepare_sql( ' AND (YEAR(f.created_at) = %d AND MONTH(f.created_at) = %d)', $year, $month );
}

$where_sql          = implode( ' AND ', $where_common );
$where_t_sql        = $where_sql . $where_t_extra;
$where_f_sql        = $where_sql . $where_f_extra;

// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- all dynamic parts are esc_sql table names or prepare_sql built strings.
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
    NULL as raw_booking_data,
    NULL as raw_customer_data,
    'transaction' as source,
    t.transaction_created_at as created_at
FROM $transactions_table t
LEFT JOIN $bookings_table b ON t.booking_id = b.id
LEFT JOIN $customers_table cust ON t.customer_id = cust.id
WHERE $where_t_sql";

// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- all dynamic parts are esc_sql table names or prepare_sql built strings.
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
    f.booking_data as raw_booking_data,
    f.customer_data as raw_customer_data,
    'failed' as source,
    f.created_at
FROM $failed_table f
LEFT JOIN $bookings_table b ON f.booking_key = b.booking_key
LEFT JOIN $customers_table cust ON f.customer_id = cust.id
WHERE $where_f_sql";

// Combine with UNION
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- subqueries built with prepare_sql above.
$union_sql    = $dbhandler->prepare_sql( "( $success_sql ) UNION ( $failed_sql ) ORDER BY created_at DESC LIMIT %d OFFSET %d", $limit, $offset );
$payment_logs = $dbhandler->get_results_raw( $union_sql ) ?? array();

// Total count — include cust join so search by customer_email works.
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- where_sql is built via prepare_sql() above.
$total_success = $dbhandler->get_var_raw( "SELECT COUNT(*) FROM $transactions_table t LEFT JOIN $bookings_table b ON t.booking_id = b.id LEFT JOIN $customers_table cust ON t.customer_id = cust.id WHERE $where_t_sql" );
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- where_sql is built via prepare_sql() above.
$total_failed  = $dbhandler->get_var_raw( "SELECT COUNT(*) FROM $failed_table f LEFT JOIN $bookings_table b ON f.booking_key = b.booking_key LEFT JOIN $customers_table cust ON f.customer_id = cust.id WHERE $where_f_sql" );
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
                <option value="pending" <?php selected( $payment_filter, 'pending' ); ?>><?php esc_html_e( 'Pending', 'service-booking' ); ?></option>
                <option value="requires_capture" <?php selected( $payment_filter, 'requires_capture' ); ?>><?php esc_html_e( 'Requires Capture', 'service-booking' ); ?></option>
                <option value="requires_payment_method" <?php selected( $payment_filter, 'requires_payment_method' ); ?>><?php esc_html_e( 'Requires Payment Method', 'service-booking' ); ?></option>
                <option value="failed" <?php selected( $payment_filter, 'failed' ); ?>><?php esc_html_e( 'Failed', 'service-booking' ); ?></option>
                <option value="canceled" <?php selected( $payment_filter, 'canceled' ); ?>><?php esc_html_e( 'Canceled (Stripe)', 'service-booking' ); ?></option>
                <option value="cancelled" <?php selected( $payment_filter, 'cancelled' ); ?>><?php esc_html_e( 'Cancelled (Admin)', 'service-booking' ); ?></option>
                <option value="refunded" <?php selected( $payment_filter, 'refunded' ); ?>><?php esc_html_e( 'Refunded', 'service-booking' ); ?></option>
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
                <input type="number" id="payment_log_items_per_page" name="payment_log_items_per_page" min="1" max="200" value="<?php echo esc_attr( $limit ); ?>" style="width:70px;" />
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
                    // For failed transactions, parse serialized data to fill missing fields.
                    $parsed_booking  = null;
                    $parsed_customer = null;
                    if ( $log->source === 'failed' ) {
                        if ( ! empty( $log->raw_booking_data ) ) {
                            $parsed_booking = maybe_unserialize( $log->raw_booking_data );
                        }
                        if ( ! empty( $log->raw_customer_data ) ) {
                            $parsed_customer = maybe_unserialize( $log->raw_customer_data );
                        }
                    }

                    // Service name — fallback to serialized booking_data.
                    $service_display = $log->service_name ?? '';
                    if ( empty( $service_display ) && is_array( $parsed_booking ) ) {
                        $service_display = $parsed_booking['service_name'] ?? '';
                        if ( empty( $service_display ) && ! empty( $parsed_booking['service_id'] ) ) {
                            $service_display = '#' . $parsed_booking['service_id'];
                        }
                    }

                    // Booking link
                    $booking_display = '—';
                    if ( ! empty( $log->booking_id ) ) {
                        $booking_url     = admin_url( 'admin.php?page=bm_single_order&booking_id=' . $log->booking_id );
                        $booking_display = '<a href="' . esc_url( $booking_url ) . '">' . esc_html( $log->booking_id ) . '</a>';
                    }

                    // Customer — fallback to serialized customer_data.
                    $customer_display = '—';
                    if ( ! empty( $log->customer_email ) ) {
                        $customer_display = esc_html( $log->customer_name . ' <' . $log->customer_email . '>' );
                    } elseif ( is_array( $parsed_customer ) ) {
                        $billing = isset( $parsed_customer['billing_details'] ) ? $parsed_customer['billing_details'] : $parsed_customer;
                        $cust_first = $billing['billing_first_name'] ?? '';
                        $cust_last  = $billing['billing_last_name'] ?? '';
                        $cust_email = $billing['billing_email'] ?? '';
                        $cust_name  = trim( $cust_first . ' ' . $cust_last );
                        if ( ! empty( $cust_email ) ) {
                            $customer_display = esc_html( ( ! empty( $cust_name ) ? $cust_name . ' <' . $cust_email . '>' : $cust_email ) );
                        } elseif ( ! empty( $cust_name ) ) {
                            $customer_display = esc_html( $cust_name );
                        }
                    }

                    // Amount — fallback to booking_data total_cost for failed transactions.
                    $display_amount = (float) ( $log->amount ?? 0 );
                    if ( $display_amount <= 0 && is_array( $parsed_booking ) ) {
                        $display_amount = (float) ( $parsed_booking['total_cost'] ?? ( $parsed_booking['subtotal'] ?? 0 ) );
                    }

                    // Currency — fallback for failed transactions.
                    $display_currency = $log->currency ?? '';

                    // Payment method — for failed transactions default to Stripe.
                    $display_method = $log->payment_method ?? '';
                    if ( empty( $display_method ) && $log->source === 'failed' ) {
                        $display_method = 'Stripe';
                    }

                    // Payment status with full color coverage
                    $status = $log->payment_status;
                    if ( $log->source === 'failed' ) {
                        // All records in FAILED_TRANSACTIONS represent failures.
                        $status_display = '<span style="color:red;font-weight:600;">' . esc_html__( 'Failed', 'service-booking' ) . '</span>';
                        if ( ! empty( $status ) && $status !== 'failed' ) {
                            $status_display .= ' <small style="color:#666;">(' . esc_html( $status ) . ')</small>';
                        }
                    } elseif ( $status === 'succeeded' || $status === 'free' ) {
                        $status_display = '<span style="color:green;font-weight:600;">' . esc_html( $status ) . '</span>';
                    } elseif ( $status === 'requires_capture' || $status === 'pending' || $status === 'requires_payment_method' ) {
                        $status_display = '<span style="color:orange;">' . esc_html( $status ) . '</span>';
                    } elseif ( $status === 'canceled' || $status === 'cancelled' ) {
                        $status_display = '<span style="color:#c00;font-weight:600;">' . esc_html( $status ) . '</span>';
                    } elseif ( $status === 'failed' ) {
                        $status_display = '<span style="color:red;font-weight:600;">' . esc_html__( 'Failed', 'service-booking' ) . '</span>';
                    } elseif ( $status === 'refunded' ) {
                        $status_display = '<span style="color:#0073aa;font-weight:600;">' . esc_html__( 'Refunded', 'service-booking' ) . '</span>';
                    } else {
                        $status_display = esc_html( $status );
                    }

                    // Refund
                    $refund_display = '—';
                    if ( ! empty( $log->refund_status ) && $log->refund_status != 'not_required' ) {
                        $refund_display = esc_html( $log->refund_status );
                    }

                    // Error — unserialize and display meaningfully
                    $error_display = '—';
                    if ( ! empty( $log->error_message ) ) {
                        $maybe_error = maybe_unserialize( $log->error_message );
                        if ( is_array( $maybe_error ) ) {
                            // Error from save_payment_error(): array with 'error', 'context', 'time'.
                            $error_text = '';
                            if ( isset( $maybe_error['error'] ) ) {
                                $error_text = $maybe_error['error'];
                                if ( ! empty( $maybe_error['context'] ) && is_array( $maybe_error['context'] ) ) {
                                    $ctx_parts = array();
                                    foreach ( $maybe_error['context'] as $ck => $cv ) {
                                        $ctx_parts[] = $ck . ': ' . $cv;
                                    }
                                    $error_text .= ' [' . implode( ', ', $ctx_parts ) . ']';
                                }
                            } else {
                                // Generic serialized array — show key:value pairs.
                                $parts = array();
                                foreach ( $maybe_error as $ek => $ev ) {
                                    if ( is_array( $ev ) || is_object( $ev ) ) {
                                        $ev = wp_json_encode( $ev );
                                    }
                                    $parts[] = ucfirst( $ek ) . ': ' . $ev;
                                }
                                $error_text = implode( ' | ', $parts );
                            }
                            $error_display = '<span style="color:red;" title="' . esc_attr( $error_text ) . '">' . esc_html( mb_strimwidth( $error_text, 0, 80, '…' ) ) . '</span>';
                        } elseif ( is_string( $maybe_error ) && ! empty( $maybe_error ) ) {
                            $error_display = '<span style="color:red;" title="' . esc_attr( $maybe_error ) . '">' . esc_html( mb_strimwidth( $maybe_error, 0, 80, '…' ) ) . '</span>';
                        }
                    }

                    // Source label
                    if ( $log->source === 'transaction' ) {
                        $source_display = '<span style="color:green;">' . esc_html__( 'Payment record', 'service-booking' ) . '</span>';
                    } else {
                        $source_display = '<span style="color:#c00;">' . esc_html__( 'Failed/Pending record', 'service-booking' ) . '</span>';
                    }
                    ?>
                    <tr>
                        <td style="text-align:center;"><input type="checkbox" class="bm-bulk-row-check payment_log-row-check" data-table="payment_log" value="<?php echo esc_attr( $log->id ); ?>"></td>
                        <td style="text-align:center;"><?php echo esc_html( $log->id ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $booking_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $service_display ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $customer_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( number_format( $display_amount, 2 ) ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $display_currency ); ?></td>
                        <td style="text-align:center;"><?php echo wp_kses_post( $status_display ); ?></td>
                        <td style="text-align:center;"><?php echo esc_html( $display_method ); ?></td>
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
