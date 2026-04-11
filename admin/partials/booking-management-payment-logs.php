<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class BM_Payment_Logs_Table extends WP_List_Table {

    private $dbhandler;

    public function __construct() {
        parent::__construct(
            array(
				'singular' => 'payment',
				'plural'   => 'payments',
				'ajax'     => false,
            )
        );
        $this->dbhandler = new BM_DBhandler();
    }

    public function get_columns() {
        return array(
			// 'cb' removed – no bulk actions
			'id'             => __( 'ID', 'service-booking' ),
			'booking_id'     => __( 'Booking ID', 'service-booking' ),
			'service_name'   => __( 'Service', 'service-booking' ),
			'customer'       => __( 'Customer', 'service-booking' ),
			'amount'         => __( 'Amount', 'service-booking' ),
			'currency'       => __( 'Currency', 'service-booking' ),
			'payment_status' => __( 'Status', 'service-booking' ),
			'payment_method' => __( 'Method', 'service-booking' ),
			'transaction_id' => __( 'Transaction ID', 'service-booking' ),
			'refund_status'  => __( 'Refund', 'service-booking' ),
			'error_message'  => __( 'Error', 'service-booking' ), // new column
			'source'         => __( 'Source', 'service-booking' ),
			'created_at'     => __( 'Date', 'service-booking' ),
		);
    }

    protected function column_error_message( $item ) {
		return ! empty( $item->error_message ) ? esc_html( $item->error_message ) : '—';
	}

    protected function column_default( $item, $column_name ) {
        return isset( $item->$column_name ) ? esc_html( $item->$column_name ) : '';
    }

    protected function column_cb( $item ) {
        return sprintf( '<input type="checkbox" name="payment[]" value="%d" />', $item->id );
    }

    protected function column_booking_id( $item ) {
        if ( empty( $item->booking_id ) ) {
			return '—';
        }
        $url = admin_url( 'admin.php?page=bm_single_order&booking_id=' . $item->booking_id );
        return '<a href="' . esc_url( $url ) . '">' . $item->booking_id . '</a>';
    }

    protected function column_customer( $item ) {
        if ( ! empty( $item->customer_email ) ) {
            return esc_html( $item->customer_name . ' <' . $item->customer_email . '>' );
        }
        return '—';
    }

    protected function column_amount( $item ) {
        return number_format( $item->amount, 2 );
    }

    protected function column_payment_status( $item ) {
        $status = $item->payment_status;
        if ( $status == 'succeeded' || $status == 'free' ) {
            return '<span style="color:green;">' . esc_html( $status ) . '</span>';
        } elseif ( $status == 'requires_capture' ) {
            return '<span style="color:orange;">' . esc_html( $status ) . '</span>';
        } elseif ( $status == 'canceled' ) {
            return '<span style="color:red;">' . esc_html( $status ) . '</span>';
        }
        return esc_html( $status );
    }

    protected function column_refund_status( $item ) {
        if ( ! empty( $item->refund_status ) && $item->refund_status != 'not_required' ) {
            return esc_html( $item->refund_status );
        }
        return '—';
    }

    protected function column_source( $item ) {
        return $item->source == 'transaction' ? __( 'Success', 'service-booking' ) : __( 'Failed', 'service-booking' );
    }

    public function prepare_items() {
        $per_page     = 20;
        $current_page = $this->get_pagenum();
        $offset       = ( $current_page - 1 ) * $per_page;

        global $wpdb;

        $bookings_table     = $wpdb->prefix . 'sgbm_booking';
        $transactions_table = $wpdb->prefix . 'sgbm_transactions';
        $failed_table       = $wpdb->prefix . 'sgbm_failed_transactions';
        $customers_table    = $wpdb->prefix . 'sgbm_customers';

        // Build WHERE conditions
        $where = array( '1=1' );
        if ( ! empty( $_REQUEST['s'] ) ) {
            $search  = '%' . $wpdb->esc_like( $_REQUEST['s'] ) . '%';
            $where[] = $wpdb->prepare( '(b.service_name LIKE %s OR c.customer_email LIKE %s)', $search, $search );
        }
        if ( ! empty( $_REQUEST['booking_id'] ) ) {
            $where[] = $wpdb->prepare( 'b.id = %d', $_REQUEST['booking_id'] );
        }
        if ( ! empty( $_REQUEST['payment_status'] ) && $_REQUEST['payment_status'] !== 'all' ) {
            $where[] = $wpdb->prepare( 'payment_status = %s', $_REQUEST['payment_status'] );
        }
        if ( ! empty( $_REQUEST['m'] ) ) {
            $yearmonth = $_REQUEST['m'];
            $year      = substr( $yearmonth, 0, 4 );
            $month     = substr( $yearmonth, 4, 2 );
            $where[]   = $wpdb->prepare( '(YEAR(created_at) = %d AND MONTH(created_at) = %d)', $year, $month );
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
            NULL as error_message,   -- success: no error
            'transaction' as source,
            t.transaction_created_at as created_at
        FROM $transactions_table t
        LEFT JOIN $bookings_table b ON t.booking_id = b.id
        LEFT JOIN $customers_table cust ON t.customer_id = cust.id
        WHERE $where_sql";

        // Failed transactions (from FAILED_TRANSACTIONS)
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
            f.error_message,        -- error message from failed table
            'failed' as source,
            f.created_at
        FROM $failed_table f
        LEFT JOIN $bookings_table b ON f.booking_key = b.booking_key
        LEFT JOIN $customers_table cust ON f.customer_id = cust.id
        WHERE $where_sql";

        // Combine with UNION
        $union_sql = "( $success_sql ) UNION ( $failed_sql ) ORDER BY created_at DESC LIMIT $offset, $per_page";

        $this->items = $wpdb->get_results( $union_sql );

        // Total count (run without LIMIT)
        $total_success = $wpdb->get_var( "SELECT COUNT(*) FROM $transactions_table t LEFT JOIN $bookings_table b ON t.booking_id = b.id WHERE $where_sql" );
        $total_failed  = $wpdb->get_var( "SELECT COUNT(*) FROM $failed_table f LEFT JOIN $bookings_table b ON f.booking_key = b.booking_key WHERE $where_sql" );
        $total         = $total_success + $total_failed;

        $this->set_pagination_args(
            array(
				'total_items' => $total,
				'per_page'    => $per_page,
            )
        );

        $columns               = $this->get_columns();
        $hidden                = array();
        $sortable              = array();
        $this->_column_headers = array( $columns, $hidden, $sortable );
    }

    protected function extra_tablenav( $which ) {
        if ( $which !== 'top' ) {
			return;
        }
        ?>
        <div class="alignleft actions">
            <input type="text" name="booking_id" placeholder="<?php esc_attr_e( 'Booking ID', 'service-booking' ); ?>" value="<?php echo esc_attr( $_REQUEST['booking_id'] ?? '' ); ?>" size="5" />
            <select name="payment_status">
                <option value="all"><?php _e( 'All statuses', 'service-booking' ); ?></option>
                <option value="succeeded" <?php selected( $_REQUEST['payment_status'] ?? '', 'succeeded' ); ?>><?php _e( 'Succeeded', 'service-booking' ); ?></option>
                <option value="free" <?php selected( $_REQUEST['payment_status'] ?? '', 'free' ); ?>><?php _e( 'Free', 'service-booking' ); ?></option>
                <option value="requires_capture" <?php selected( $_REQUEST['payment_status'] ?? '', 'requires_capture' ); ?>><?php _e( 'Requires Capture', 'service-booking' ); ?></option>
                <option value="cancelled" <?php selected( $_REQUEST['payment_status'] ?? '', 'cancelled' ); ?>><?php _e( 'Canceled', 'service-booking' ); ?></option>
                <option value="requires_payment_method" <?php selected( $_REQUEST['payment_status'] ?? '', 'requires_payment_method' ); ?>><?php _e( 'Requires Payment Method', 'service-booking' ); ?></option>
            </select>
            <?php submit_button( __( 'Filter', 'service-booking' ), '', 'filter_action', false ); ?>

            <!-- Reset button -->
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=bm_payment_logs' ) ); ?>" class="button"><?php _e( 'Reset', 'service-booking' ); ?></a>
        </div>
        <?php
    }
}

// Display the page
$table = new BM_Payment_Logs_Table();
$table->prepare_items();
?>
<div class="wrap">
    <h1><?php _e( 'Payment Logs', 'service-booking' ); ?></h1>
    <form method="get">
        <input type="hidden" name="page" value="bm_payment_logs" />
        <?php $table->search_box( __( 'Search Service/Customer', 'service-booking' ), 'search_id' ); ?>
        <?php $table->display(); ?>
    </form>
</div>
