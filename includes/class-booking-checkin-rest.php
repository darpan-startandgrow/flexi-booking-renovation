<?php
/**
 * Check-In REST API
 *
 * Dedicated REST API for the CHECKIN module.
 * Namespace: bm-checkin/v1
 *
 * All endpoints are admin-only (manage_options).
 * All DB access goes through BM_DBhandler; no direct $wpdb calls.
 * class-booking-api.php is intentionally left untouched.
 *
 * Endpoints
 * ---------
 * GET    /checkins              — paginated list with optional filters
 * GET    /checkins/stats        — aggregated status counts
 * GET    /checkins/{id}         — single check-in record
 * POST   /checkins/{booking_id}/checkin  — perform check-in
 * POST   /checkins/{booking_id}/undo     — undo check-in → pending
 * POST   /checkins/{booking_id}/no-show  — mark as no-show
 * POST   /checkins/{booking_id}/checkout — mark as checked-out
 * PATCH  /checkins/{id}/status  — set an arbitrary status
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @since      2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Booking_Checkin_REST
 *
 * @since 2.1.0
 */
class Booking_Checkin_REST {

	const NAMESPACE = 'bm-checkin/v1';

	/**
	 * Register routes on instantiation.
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	// -------------------------------------------------------------------------
	// Route Registration
	// -------------------------------------------------------------------------

	/**
	 * Register all REST routes.
	 */
	public function register_routes() {

		// GET /checkins — list with optional filters
		register_rest_route(
			self::NAMESPACE,
			'/checkins',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_checkins' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'status'     => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'service_id' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
					'date_from'  => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( $this, 'validate_date' ),
					),
					'date_to'    => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( $this, 'validate_date' ),
					),
					'search'     => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'page'       => array(
						'required'          => false,
						'default'           => 1,
						'sanitize_callback' => 'absint',
					),
					'per_page'   => array(
						'required'          => false,
						'default'           => 25,
						'sanitize_callback' => 'absint',
					),
					'orderby'    => array(
						'required'          => false,
						'default'           => 'created_at',
						'sanitize_callback' => 'sanitize_key',
					),
					'order'      => array(
						'required'          => false,
						'default'           => 'DESC',
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// GET /checkins/stats — aggregated counts (must be before {id} to avoid routing conflict)
		register_rest_route(
			self::NAMESPACE,
			'/checkins/stats',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_stats' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
			)
		);

		// GET /checkins/{id} — single record
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<id>\d+)',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_checkin' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// POST /checkins/{booking_id}/checkin
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<booking_id>\d+)/checkin',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'do_checkin' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'booking_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// POST /checkins/{booking_id}/undo
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<booking_id>\d+)/undo',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'undo_checkin' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'booking_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// POST /checkins/{booking_id}/no-show
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<booking_id>\d+)/no-show',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'mark_no_show' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'booking_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// POST /checkins/{booking_id}/checkout
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<booking_id>\d+)/checkout',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'do_checkout' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'booking_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// PATCH /checkins/{id}/status
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<id>\d+)/status',
			array(
				'methods'             => 'PATCH',
				'callback'            => array( $this, 'update_status' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'id'     => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'status' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( $this, 'validate_status' ),
					),
				),
			)
		);
	}

	// -------------------------------------------------------------------------
	// Permission callback
	// -------------------------------------------------------------------------

	/**
	 * Allow only users with manage_options.
	 *
	 * @return bool|WP_Error
	 */
	public function admin_permission_check() {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		return new WP_Error(
			'rest_forbidden',
			__( 'You do not have permission to access this resource.', 'service-booking' ),
			array( 'status' => 403 )
		);
	}

	// -------------------------------------------------------------------------
	// Validation helpers
	// -------------------------------------------------------------------------

	/**
	 * Validate a date string in Y-m-d format.
	 *
	 * @param string $date Date string.
	 * @return bool
	 */
	public function validate_date( $date ) {
		if ( ! is_string( $date ) || empty( $date ) ) {
			return true; // Optional field; empty is fine.
		}
		$dt = DateTime::createFromFormat( 'Y-m-d', $date );
		return $dt && $dt->format( 'Y-m-d' ) === $date;
	}

	/**
	 * Validate that a status string is in the allowed list.
	 *
	 * @param string $status Status slug.
	 * @return bool|WP_Error
	 */
	public function validate_status( $status ) {
		if ( in_array( $status, BM_Checkin::get_allowed_statuses(), true ) ) {
			return true;
		}
		return new WP_Error(
			'invalid_status',
			sprintf(
				/* translators: %s = invalid status value */
				__( 'Invalid check-in status: %s', 'service-booking' ),
				esc_html( $status )
			),
			array( 'status' => 400 )
		);
	}

	// -------------------------------------------------------------------------
	// GET /checkins
	// -------------------------------------------------------------------------

	/**
	 * List check-in records with optional filters, pagination, and search.
	 *
	 * Response shape:
	 *   { data: [...], total: N, page: N, per_page: N, total_pages: N }
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_checkins( WP_REST_Request $request ) {
		$db        = new BM_DBhandler();
		$activator = new Booking_Management_Activator();
		$cht_table = $activator->get_db_table_name( 'CHECKIN' );
		$bkt_table = $activator->get_db_table_name( 'BOOKING' );
		$svc_table = $activator->get_db_table_name( 'SERVICE' );

		$page     = max( 1, (int) $request->get_param( 'page' ) );
		$per_page = min( 100, max( 1, (int) $request->get_param( 'per_page' ) ) );
		$offset   = ( $page - 1 ) * $per_page;

		$status     = sanitize_text_field( $request->get_param( 'status' ) ?? '' );
		$service_id = absint( $request->get_param( 'service_id' ) );
		$date_from  = sanitize_text_field( $request->get_param( 'date_from' ) ?? '' );
		$date_to    = sanitize_text_field( $request->get_param( 'date_to' ) ?? '' );
		$search     = sanitize_text_field( $request->get_param( 'search' ) ?? '' );
		$orderby    = sanitize_key( $request->get_param( 'orderby' ) ?? 'created_at' );
		$order      = strtoupper( sanitize_text_field( $request->get_param( 'order' ) ?? 'DESC' ) );

		// Whitelist sortable columns to prevent SQL injection.
		$sortable = array( 'created_at', 'checkin_time', 'status', 'booking_id' );
		if ( ! in_array( $orderby, $sortable, true ) ) {
			$orderby = 'created_at';
		}
		if ( ! in_array( $order, array( 'ASC', 'DESC' ), true ) ) {
			$order = 'DESC';
		}

		// Build WHERE clauses dynamically.
		$where_parts = array( 'b.is_active = 1' );
		$where_vals  = array();

		if ( $status && in_array( $status, BM_Checkin::get_allowed_statuses(), true ) ) {
			$where_parts[] = 'ch.status = %s';
			$where_vals[]  = $status;
		}

		if ( $service_id ) {
			$where_parts[] = 'b.service_id = %d';
			$where_vals[]  = $service_id;
		}

		if ( $date_from ) {
			$where_parts[] = 'b.booking_date >= %s';
			$where_vals[]  = $date_from;
		}

		if ( $date_to ) {
			$where_parts[] = 'b.booking_date <= %s';
			$where_vals[]  = $date_to;
		}

		if ( $search ) {
			$like = '%' . $db->esc_like( $search ) . '%';
			$where_parts[] = '(b.booking_key LIKE %s OR b.first_name LIKE %s OR b.last_name LIKE %s OR b.email_address LIKE %s)';
			$where_vals[]  = $like;
			$where_vals[]  = $like;
			$where_vals[]  = $like;
			$where_vals[]  = $like;
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$where_sql = implode( ' AND ', $where_parts );

		$base_sql = "SELECT ch.*, b.booking_key, b.booking_date, b.first_name, b.last_name,
		                    b.email_address, b.contact_no, b.service_id, b.service_name
		             FROM `{$cht_table}` ch
		             INNER JOIN `{$bkt_table}` b ON b.id = ch.booking_id
		             WHERE {$where_sql}";

		// Total count for pagination.
		$count_sql = $db->prepare_sql(
			"SELECT COUNT(*) FROM `{$cht_table}` ch INNER JOIN `{$bkt_table}` b ON b.id = ch.booking_id WHERE {$where_sql}",
			...$where_vals
		);
		$total = (int) $db->get_var_raw( $count_sql );
		// phpcs:enable

		// Main data query.
		$data_vals   = array_merge( $where_vals, array( $per_page, $offset ) );
		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$data_sql    = $db->prepare_sql(
			$base_sql . " ORDER BY ch.`{$orderby}` {$order} LIMIT %d OFFSET %d",
			...$data_vals
		);
		// phpcs:enable
		$rows = $db->get_results_raw( $data_sql ) ?? array();

		$data = array_map( array( $this, 'format_checkin_row' ), $rows );

		return rest_ensure_response(
			array(
				'data'        => $data,
				'total'       => $total,
				'page'        => $page,
				'per_page'    => $per_page,
				'total_pages' => (int) ceil( $total / $per_page ),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /checkins/stats
	// -------------------------------------------------------------------------

	/**
	 * Return aggregated status counts.
	 *
	 * @return WP_REST_Response
	 */
	public function get_stats() {
		return rest_ensure_response( BM_Checkin::get_status_counts() );
	}

	// -------------------------------------------------------------------------
	// GET /checkins/{id}
	// -------------------------------------------------------------------------

	/**
	 * Return a single check-in record by its primary key.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_checkin( WP_REST_Request $request ) {
		$id = absint( $request->get_param( 'id' ) );
		$db = new BM_DBhandler();

		$row = $db->get_row( 'CHECKIN', $id, 'id' );
		if ( ! $row ) {
			return new WP_Error(
				'checkin_not_found',
				__( 'Check-in record not found.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		return rest_ensure_response( $this->format_checkin_row( $row ) );
	}

	// -------------------------------------------------------------------------
	// POST /checkins/{booking_id}/checkin
	// -------------------------------------------------------------------------

	/**
	 * Perform a check-in for the given booking.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function do_checkin( WP_REST_Request $request ) {
		$booking_id = absint( $request->get_param( 'booking_id' ) );
		$db         = new BM_DBhandler();

		// Verify booking exists and is active.
		$booking = $db->get_row( 'BOOKING', $booking_id, 'id' );
		if ( ! $booking || ! $booking->is_active ) {
			return new WP_Error(
				'booking_not_found',
				__( 'Booking not found or is not active.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		$result = BM_Checkin::do_checkin( $booking_id, $db, get_current_user_id() );
		if ( ! $result ) {
			return new WP_Error(
				'checkin_failed',
				__( 'Check-in failed. The booking may already be checked in or was blocked by a restriction.', 'service-booking' ),
				array( 'status' => 422 )
			);
		}

		$record = $db->get_row( 'CHECKIN', $booking_id, 'booking_id' );
		return rest_ensure_response(
			array(
				'message' => __( 'Booking checked in successfully.', 'service-booking' ),
				'data'    => $record ? $this->format_checkin_row( $record ) : null,
			)
		);
	}

	// -------------------------------------------------------------------------
	// POST /checkins/{booking_id}/undo
	// -------------------------------------------------------------------------

	/**
	 * Undo a check-in, resetting the booking to "pending".
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function undo_checkin( WP_REST_Request $request ) {
		$booking_id = absint( $request->get_param( 'booking_id' ) );
		$db         = new BM_DBhandler();
		$now        = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();

		$existing_id = (int) $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );
		if ( ! $existing_id ) {
			return new WP_Error(
				'checkin_not_found',
				__( 'No check-in record found for this booking.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		do_action( 'bm_checkin_before_undo', $booking_id, $existing_id );

		$updated = $db->update_row(
			'CHECKIN',
			'id',
			$existing_id,
			array(
				'status'       => BM_CHECKIN_STATUS_PENDING,
				'qr_scanned'   => 0,
				'checkin_time' => null,
				'updated_at'   => $now,
			)
		);

		if ( false === $updated ) {
			return new WP_Error(
				'undo_failed',
				__( 'Failed to undo check-in.', 'service-booking' ),
				array( 'status' => 500 )
			);
		}

		do_action( 'bm_checkin_after_undo', $booking_id );

		return rest_ensure_response( array( 'message' => __( 'Check-in undone successfully.', 'service-booking' ) ) );
	}

	// -------------------------------------------------------------------------
	// POST /checkins/{booking_id}/no-show
	// -------------------------------------------------------------------------

	/**
	 * Mark a booking as no-show.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function mark_no_show( WP_REST_Request $request ) {
		$booking_id = absint( $request->get_param( 'booking_id' ) );
		$db         = new BM_DBhandler();
		$now        = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();
		$uid        = get_current_user_id();

		do_action( 'bm_checkin_no_show', $booking_id, $uid );

		$existing_id = (int) $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );

		if ( $existing_id ) {
			$db->update_row( 'CHECKIN', 'id', $existing_id, array(
				'status'     => BM_CHECKIN_STATUS_NO_SHOW,
				'updated_at' => $now,
			) );
		} else {
			// Booking has no checkin row yet — create one.
			$token = $db->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );
			if ( ! $token ) {
				return new WP_Error(
					'booking_not_found',
					__( 'Booking not found.', 'service-booking' ),
					array( 'status' => 404 )
				);
			}
			$db->insert_row( 'CHECKIN', array(
				'booking_id'    => $booking_id,
				'status'        => BM_CHECKIN_STATUS_NO_SHOW,
				'qr_token'      => $token,
				'qr_scanned'    => 0,
				'checked_in_by' => $uid,
				'created_at'    => $now,
				'updated_at'    => $now,
			) );
		}

		return rest_ensure_response( array( 'message' => __( 'Booking marked as no-show.', 'service-booking' ) ) );
	}

	// -------------------------------------------------------------------------
	// POST /checkins/{booking_id}/checkout
	// -------------------------------------------------------------------------

	/**
	 * Mark a checked-in booking as checked-out.
	 *
	 * Only transitions from checked_in → checked_out (atomic guard).
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function do_checkout( WP_REST_Request $request ) {
		$booking_id  = absint( $request->get_param( 'booking_id' ) );
		$db          = new BM_DBhandler();
		$now         = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();
		$existing_id = (int) $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );

		if ( ! $existing_id ) {
			return new WP_Error(
				'checkin_not_found',
				__( 'No check-in record found for this booking.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		$affected = $db->update_if_state_matches(
			'CHECKIN',
			'id',
			$existing_id,
			'status',
			BM_CHECKIN_STATUS_CHECKED_IN,
			array(
				'status'     => BM_CHECKIN_STATUS_CHECKED_OUT,
				'updated_at' => $now,
			)
		);

		if ( 0 === $affected ) {
			return new WP_Error(
				'invalid_state',
				__( 'Booking is not in checked-in status.', 'service-booking' ),
				array( 'status' => 422 )
			);
		}

		return rest_ensure_response( array( 'message' => __( 'Booking checked out successfully.', 'service-booking' ) ) );
	}

	// -------------------------------------------------------------------------
	// PATCH /checkins/{id}/status
	// -------------------------------------------------------------------------

	/**
	 * Update the status of a check-in record directly.
	 *
	 * Useful for bulk admin operations and external integrations.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_status( WP_REST_Request $request ) {
		$id     = absint( $request->get_param( 'id' ) );
		$status = sanitize_text_field( $request->get_param( 'status' ) );
		$db     = new BM_DBhandler();
		$now    = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();

		$row = $db->get_row( 'CHECKIN', $id, 'id' );
		if ( ! $row ) {
			return new WP_Error(
				'checkin_not_found',
				__( 'Check-in record not found.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		$db->update_row(
			'CHECKIN',
			'id',
			$id,
			array(
				'status'     => $status,
				'updated_at' => $now,
			)
		);

		$updated = $db->get_row( 'CHECKIN', $id, 'id' );

		return rest_ensure_response(
			array(
				'message' => __( 'Status updated.', 'service-booking' ),
				'data'    => $updated ? $this->format_checkin_row( $updated ) : null,
			)
		);
	}

	// -------------------------------------------------------------------------
	// Response formatter
	// -------------------------------------------------------------------------

	/**
	 * Normalise a CHECKIN DB row for REST output.
	 *
	 * Strips raw DB objects down to a clean, consistently typed array.
	 *
	 * @param object $row Raw row from DB.
	 * @return array
	 */
	private function format_checkin_row( $row ) {
		if ( ! is_object( $row ) ) {
			return array();
		}

		$status = sanitize_key( $row->status ?? '' );

		return array(
			'id'            => (int) ( $row->id ?? 0 ),
			'booking_id'    => (int) ( $row->booking_id ?? 0 ),
			'booking_key'   => sanitize_text_field( $row->booking_key ?? '' ),
			'booking_date'  => sanitize_text_field( $row->booking_date ?? '' ),
			'first_name'    => sanitize_text_field( $row->first_name ?? '' ),
			'last_name'     => sanitize_text_field( $row->last_name ?? '' ),
			'email_address' => sanitize_email( $row->email_address ?? '' ),
			'contact_no'    => sanitize_text_field( $row->contact_no ?? '' ),
			'service_id'    => (int) ( $row->service_id ?? 0 ),
			'service_name'  => sanitize_text_field( $row->service_name ?? '' ),
			'status'        => $status,
			'status_label'  => BM_Checkin::get_status_label( $status ),
			'qr_scanned'    => (bool) ( $row->qr_scanned ?? false ),
			'checkin_time'  => sanitize_text_field( $row->checkin_time ?? '' ),
			'checked_in_by' => (int) ( $row->checked_in_by ?? 0 ),
			'notes'         => sanitize_textarea_field( $row->notes ?? '' ),
			'created_at'    => sanitize_text_field( $row->created_at ?? '' ),
			'updated_at'    => sanitize_text_field( $row->updated_at ?? '' ),
		);
	}
}
