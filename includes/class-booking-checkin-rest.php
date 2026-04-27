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
 * GET    /checkins/listing      — admin listing with search/filter, column config, pagination HTML, saved search
 * GET    /checkins/stats        — aggregated status counts
 * GET    /checkins/saved-search — last saved checkin search criteria for current admin user
 * GET    /checkins/search       — attendee lookup returning JSON rows
 * GET    /checkins/export-options — export options HTML fragment
 * GET    /checkins/export       — CSV-ready rows for current filter set
 * GET    /checkins/{id}         — single check-in record
 * POST   /checkins/scan         — perform check-in by booking_key (QR payload)
 * POST   /checkins/bulk         — batch check-in by booking ID array
 * POST   /checkins/{booking_id}/checkin  — perform check-in
 * POST   /checkins/{booking_id}/undo     — undo check-in → pending
 * POST   /checkins/{booking_id}/no-show  — mark as no-show
 * POST   /checkins/{booking_id}/checkout — mark as checked-out
 * PATCH  /checkins/{id}/status  — set an arbitrary status
 * GET    /checkins/{booking_id}/details  — booking detail card for admin dashboard
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

		// POST /checkins/scan — public check-in by QR token / booking_key.
		// Accessible without authentication (customers scanning their own QR).
		// Rate-limited in scan_permission_check().
		register_rest_route(
			self::NAMESPACE,
			'/checkins/scan',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'handle_scan' ),
				'permission_callback' => array( $this, 'scan_permission_check' ),
				'args'                => array(
					'booking_key' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'gate'        => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// POST /checkins/bulk — admin batch check-in by booking ID array.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/bulk',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'bulk_checkin' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'booking_ids' => array(
						'required' => true,
						'type'     => 'array',
						'items'    => array( 'type' => 'integer' ),
					),
				),
			)
		);

		// GET /checkins/search — admin attendee lookup returning JSON rows.
		// Must be before /checkins/(?P<id>\d+) to avoid route conflict.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'search_checkins' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'search_type'  => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
						'validate_callback' => array( $this, 'validate_search_type' ),
					),
					'search_value' => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
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

		// GET /checkins/{booking_id}/details — JSON booking detail card for admin dashboard.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/(?P<booking_id>\d+)/details',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_checkin_details' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'booking_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /checkins/listing — admin listing with search, filters, pagination HTML, column config, saved search.
		// Must be registered before /checkins (GET) so it doesn't shadow the existing list endpoint.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/listing',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_checkins_listing' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'page'        => array( 'required' => false, 'default' => 1, 'sanitize_callback' => 'absint' ),
					'per_page'    => array( 'required' => false, 'default' => 10, 'sanitize_callback' => 'absint' ),
					'search'      => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'service_from'=> array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'service_to'  => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'checkin_from'=> array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'checkin_to'  => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'service_ids' => array( 'required' => false, 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
					'save_search' => array( 'required' => false, 'default' => false, 'sanitize_callback' => 'rest_sanitize_boolean' ),
					'base'        => array( 'required' => false, 'sanitize_callback' => 'esc_url_raw' ),
				),
			)
		);

		// GET /checkins/saved-search — return the last saved checkin search criteria for this admin user.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/saved-search',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_saved_search' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
			)
		);

		// GET /checkins/export-options — return the export options HTML fragment.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/export-options',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_export_options' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
			)
		);

		// GET /checkins/export — return CSV-ready rows for the current filter set.
		register_rest_route(
			self::NAMESPACE,
			'/checkins/export',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_export_data' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'type'         => array( 'required' => false, 'default' => 'all', 'sanitize_callback' => 'sanitize_text_field' ),
					'start_page'   => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'absint' ),
					'end_page'     => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'absint' ),
					'limit'        => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'absint' ),
					'total_pages'  => array( 'required' => false, 'default' => 0, 'sanitize_callback' => 'absint' ),
					'search'       => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'service_from' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'service_to'   => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'checkin_from' => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'checkin_to'   => array( 'required' => false, 'sanitize_callback' => 'sanitize_text_field' ),
					'services'     => array( 'required' => false, 'type' => 'array', 'items' => array( 'type' => 'integer' ) ),
					'order_column' => array( 'required' => false, 'default' => 'id', 'sanitize_callback' => 'sanitize_key' ),
					'order_dir'    => array( 'required' => false, 'default' => 'DESC', 'sanitize_callback' => 'sanitize_text_field' ),
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

	/**
	 * Permission callback for the public /checkins/scan endpoint.
	 *
	 * Allows any visitor (including unauthenticated customers) but enforces
	 * IP-based rate limiting to prevent brute-force enumeration of booking keys.
	 *
	 * Authenticated admins bypass the rate limit.
	 *
	 * @return bool|WP_Error
	 */
	public function scan_permission_check() {
		// Admins are always allowed and exempt from rate limiting.
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}

		$ip       = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ?? 'unknown' ) );
		$rate_key = 'bm_qr_scan_' . md5( $ip );
		$attempts = (int) get_transient( $rate_key );

		if ( $attempts >= 20 ) {
			return new WP_Error(
				'rate_limit',
				__( 'Too many attempts. Please try again in a minute.', 'service-booking' ),
				array( 'status' => 429 )
			);
		}

		set_transient( $rate_key, $attempts + 1, 60 );
		return true;
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

	/**
	 * Validate the search_type parameter for /checkins/search.
	 *
	 * @param string $type Search type string.
	 * @return bool|WP_Error
	 */
	public function validate_search_type( $type ) {
		$allowed = array( 'last_name', 'email', 'service', 'reference' );
		if ( in_array( $type, $allowed, true ) ) {
			return true;
		}
		return new WP_Error(
			'invalid_search_type',
			__( 'Invalid search type. Use: last_name, email, service, or reference.', 'service-booking' ),
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
	// POST /checkins/scan
	// -------------------------------------------------------------------------

	/**
	 * Public check-in endpoint — accepts a booking_key (QR payload) and performs check-in.
	 *
	 * Accessible to unauthenticated customers and authenticated admins.
	 * Rate limiting is enforced in scan_permission_check().
	 *
	 * Response codes:
	 *   200 — check-in successful
	 *   409 — already checked in
	 *   422 — booking not found / inactive / blocked
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response
	 */
	public function handle_scan( WP_REST_Request $request ) {
		$booking_key = sanitize_text_field( $request->get_param( 'booking_key' ) );
		$db          = new BM_DBhandler();

		// Lookup booking by its unique key (the QR payload).
		$booking = $db->get_row( 'BOOKING', $booking_key, 'booking_key' );

		if ( ! $booking ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'code'    => 'INVALID_TOKEN',
					'reason'  => 'not_found',
					'message' => __( 'Booking not found.', 'service-booking' ),
				),
				422
			);
		}

		if ( ! $booking->is_active ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'code'    => 'INVALID_TOKEN',
					'reason'  => 'cancelled',
					'message' => __( 'Cannot check in a cancelled or refunded booking.', 'service-booking' ),
				),
				422
			);
		}

		$booking_id = (int) $booking->id;

		// Check for idempotency — is this booking already checked in?
		$existing_checkin = $db->get_row( 'CHECKIN', $booking_id, 'booking_id' );
		if ( $existing_checkin && BM_CHECKIN_STATUS_CHECKED_IN === $existing_checkin->status ) {
			return new WP_REST_Response(
				array(
					'success'          => false,
					'code'             => 'ALREADY_CHECKED_IN',
					'message'          => __( 'This booking has already been checked in.', 'service-booking' ),
					'first_checkin_at' => sanitize_text_field( $existing_checkin->checkin_time ?? '' ),
				),
				409
			);
		}

		$result = BM_Checkin::do_checkin( $booking_id, $db, get_current_user_id() );

		if ( ! $result ) {
			return new WP_REST_Response(
				array(
					'success' => false,
					'code'    => 'CHECKIN_BLOCKED',
					'message' => __( 'Check-in was blocked by a restriction (e.g. time window).', 'service-booking' ),
				),
				422
			);
		}

		// Issue a one-time confirmation token so the frontend can redirect to a
		// confirmation page without exposing the booking_key in the URL.
		$token = wp_generate_password( 32, false );
		set_transient( 'bm_ci_confirm_' . $token, $booking_id, 10 * MINUTE_IN_SECONDS );

		return new WP_REST_Response(
			array(
				'success'       => true,
				'booking_id'    => $booking_id,
				'booking_key'   => sanitize_text_field( $booking->booking_key ?? '' ),
				'attendee'      => trim( sanitize_text_field( ( $booking->first_name ?? '' ) . ' ' . ( $booking->last_name ?? '' ) ) ),
				'first_name'    => sanitize_text_field( $booking->first_name ?? '' ),
				'last_name'     => sanitize_text_field( $booking->last_name ?? '' ),
				'email'         => sanitize_email( $booking->email_address ?? '' ),
				'service'       => sanitize_text_field( $booking->service_name ?? '' ),
				'booking_date'  => sanitize_text_field( $booking->booking_date ?? '' ),
				'message'       => __( 'Checked in successfully.', 'service-booking' ),
				'confirm_token' => $token,
			),
			200
		);
	}

	// -------------------------------------------------------------------------
	// POST /checkins/bulk
	// -------------------------------------------------------------------------

	/**
	 * Batch check-in for an array of booking IDs (admin only).
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response
	 */
	public function bulk_checkin( WP_REST_Request $request ) {
		$raw_ids    = $request->get_param( 'booking_ids' );
		$booking_ids = is_array( $raw_ids ) ? array_map( 'absint', $raw_ids ) : array();

		if ( empty( $booking_ids ) ) {
			return new WP_REST_Response(
				array( 'message' => __( 'No booking IDs provided.', 'service-booking' ) ),
				400
			);
		}

		$db      = new BM_DBhandler();
		$uid     = get_current_user_id();
		$success = array();
		$failed  = array();

		foreach ( $booking_ids as $bid ) {
			$is_active = $db->get_value( 'BOOKING', 'is_active', $bid, 'id' );
			if ( $is_active != 1 ) {
				$failed[] = $bid;
				continue;
			}
			if ( BM_Checkin::do_checkin( $bid, $db, $uid ) ) {
				$success[] = $bid;
			} else {
				$failed[] = $bid;
			}
		}

		$count = count( $success );

		return new WP_REST_Response(
			array(
				'success'       => $count > 0,
				'checked_in'    => $count,
				'failed'        => count( $failed ),
				'failed_ids'    => $failed,
				/* translators: %d number of bookings */
				'message'       => sprintf(
					_n( '%d booking checked in successfully.', '%d bookings checked in successfully.', $count, 'service-booking' ),
					$count
				),
			),
			200
		);
	}

	// -------------------------------------------------------------------------
	// GET /checkins/search
	// -------------------------------------------------------------------------

	/**
	 * Attendee search for the admin manual check-in modal.
	 *
	 * Returns a JSON array of matching booking rows with their check-in status.
	 * The JS is responsible for rendering the results table.
	 *
	 * Query parameters:
	 *   search_type  — last_name | email | service | reference
	 *   search_value — search term (comma-separated IDs for service type)
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function search_checkins( WP_REST_Request $request ) {
		$search_type  = sanitize_text_field( $request->get_param( 'search_type' ) );
		$search_value = sanitize_text_field( $request->get_param( 'search_value' ) );

		if ( '' === $search_value ) {
			return new WP_Error(
				'missing_search_value',
				__( 'Search value is required.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$db        = new BM_DBhandler();
		$activator = new Booking_Management_Activator();
		$bkt_table = $activator->get_db_table_name( 'BOOKING' );
		$cht_table = $activator->get_db_table_name( 'CHECKIN' );

		// Build WHERE clause per search type.
		$where_parts = array( 'b.is_active = 1' );
		$where_vals  = array();

		switch ( $search_type ) {
			case 'reference':
				$where_parts[] = 'b.booking_key = %s';
				$where_vals[]  = $search_value;
				break;

			case 'email':
				$where_parts[] = 'b.email_address = %s';
				$where_vals[]  = $search_value;
				break;

			case 'last_name':
				$like          = '%' . $db->esc_like( $search_value ) . '%';
				$where_parts[] = 'b.last_name LIKE %s';
				$where_vals[]  = $like;
				break;

			case 'service':
				// search_value may be comma-separated service IDs from a multi-select.
				$raw_ids     = array_map( 'absint', array_filter( explode( ',', $search_value ) ) );
				if ( empty( $raw_ids ) ) {
					return new WP_Error(
						'invalid_service_ids',
						__( 'No valid service IDs provided.', 'service-booking' ),
						array( 'status' => 400 )
					);
				}
				// Build safe IN clause: IDs are integers from absint, safe to interpolate.
				$placeholders  = implode( ', ', array_fill( 0, count( $raw_ids ), '%d' ) );
				$where_parts[] = "b.service_id IN ( {$placeholders} )"; // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$where_vals    = array_merge( $where_vals, $raw_ids );
				break;
		}

		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$where_sql = implode( ' AND ', $where_parts );
		$sql       = $db->prepare_sql(
			"SELECT b.id, b.booking_key, b.service_id, b.service_name,
			        b.first_name, b.last_name, b.email_address,
			        b.total_svc_slots AS svc_participants,
			        b.total_ext_svc_slots AS ex_svc_participants,
			        ch.status AS checkin_status, ch.checkin_time, ch.id AS checkin_id
			 FROM `{$bkt_table}` b
			 LEFT JOIN `{$cht_table}` ch ON ch.booking_id = b.id
			 WHERE {$where_sql}
			 ORDER BY b.id DESC
			 LIMIT 200",
			...$where_vals
		);
		// phpcs:enable

		$rows = $db->get_results_raw( $sql ) ?? array();

		$results = array_map( function ( $row ) {
			$status = sanitize_key( $row->checkin_status ?? '' );
			return array(
				'id'               => (int) $row->id,
				'booking_key'      => sanitize_text_field( $row->booking_key ?? '' ),
				'service_id'       => (int) $row->service_id,
				'service_name'     => sanitize_text_field( $row->service_name ?? '' ),
				'first_name'       => sanitize_text_field( $row->first_name ?? '' ),
				'last_name'        => sanitize_text_field( $row->last_name ?? '' ),
				'email_address'    => sanitize_email( $row->email_address ?? '' ),
				'svc_participants' => (int) ( $row->svc_participants ?? 0 ),
				'ex_participants'  => (int) ( $row->ex_svc_participants ?? 0 ),
				'checkin_status'   => $status,
				'checkin_label'    => $status ? BM_Checkin::get_status_label( $status ) : __( 'Pending', 'service-booking' ),
				'checkin_time'     => sanitize_text_field( $row->checkin_time ?? '' ),
				'checkin_id'       => (int) ( $row->checkin_id ?? 0 ),
			);
		}, $rows );

		return rest_ensure_response(
			array(
				'results' => $results,
				'total'   => count( $results ),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /checkins/{booking_id}/details
	// -------------------------------------------------------------------------

	/**
	 * Return a JSON booking-detail card for the admin check-in dashboard.
	 *
	 * Used by the "View" (eye icon) action in the manual check-in modal and
	 * the "Order Details" viewer in the QR scanner panel.
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_checkin_details( WP_REST_Request $request ) {
		$booking_id = absint( $request->get_param( 'booking_id' ) );
		$db         = new BM_DBhandler();

		$booking = $db->get_row( 'BOOKING', $booking_id, 'id' );
		if ( ! $booking ) {
			return new WP_Error(
				'booking_not_found',
				__( 'Booking not found.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		$checkin = $db->get_row( 'CHECKIN', $booking_id, 'booking_id' );

		// Retrieve billing address if available.
		$customer_name = trim( sanitize_text_field( ( $booking->first_name ?? '' ) . ' ' . ( $booking->last_name ?? '' ) ) );

		return rest_ensure_response(
			array(
				'booking_id'    => $booking_id,
				'booking_key'   => sanitize_text_field( $booking->booking_key ?? '' ),
				'attendee'      => $customer_name,
				'first_name'    => sanitize_text_field( $booking->first_name ?? '' ),
				'last_name'     => sanitize_text_field( $booking->last_name ?? '' ),
				'email'         => sanitize_email( $booking->email_address ?? '' ),
				'contact_no'    => sanitize_text_field( $booking->contact_no ?? '' ),
				'service_name'  => sanitize_text_field( $booking->service_name ?? '' ),
				'booking_date'  => sanitize_text_field( $booking->booking_date ?? '' ),
				'order_status'  => sanitize_text_field( $booking->order_status ?? '' ),
				'checkin_status'=> sanitize_key( $checkin->status ?? '' ),
				'checkin_time'  => sanitize_text_field( $checkin->checkin_time ?? '' ),
				'is_active'     => (bool) $booking->is_active,
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

	// -------------------------------------------------------------------------
	// GET /checkins/listing
	// -------------------------------------------------------------------------

	/**
	 * Admin listing endpoint — returns the same payload shape as the legacy
	 * bm_fetch_checkin_as_per_search AJAX action so that admin.js requires
	 * minimal changes.
	 *
	 * Response keys:
	 *   status            bool
	 *   checkins          array  — the page slice of checkin rows
	 *   active_columns    array  — key => column header label (active columns)
	 *   column_values     array  — [{column, name}] full column map
	 *   num_of_pages      int
	 *   saved_search      mixed  — last saved search data (null if none)
	 *   current_pagenumber int   — serial-number start for this page
	 *   pagination        string — server-rendered pagination HTML
	 *   status_counts     array  — aggregated checkin status counts
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_checkins_listing( WP_REST_Request $request ) {
		$bmrequests = new BM_Request();
		$dbhandler  = new BM_DBhandler();

		$page        = max( 1, (int) $request->get_param( 'page' ) );
		$per_page    = max( 1, min( 200, (int) $request->get_param( 'per_page' ) ) );
		$offset      = ( $page - 1 ) * $per_page;
		$search      = sanitize_text_field( $request->get_param( 'search' ) ?? '' );
		$svc_from    = sanitize_text_field( $request->get_param( 'service_from' ) ?? '' );
		$svc_to      = sanitize_text_field( $request->get_param( 'service_to' ) ?? '' );
		$ci_from     = sanitize_text_field( $request->get_param( 'checkin_from' ) ?? '' );
		$ci_to       = sanitize_text_field( $request->get_param( 'checkin_to' ) ?? '' );
		$service_ids = array_map( 'absint', (array) ( $request->get_param( 'service_ids' ) ?? array() ) );
		$service_ids = array_values( array_filter( $service_ids ) );
		$save_search = (bool) $request->get_param( 'save_search' );
		$base        = esc_url_raw( $request->get_param( 'base' ) ?? '' );
		$user_id     = get_current_user_id();
		$is_admin    = current_user_can( 'manage_options' ) ? 1 : 0;

		// Fetch all and apply PHP-side filters (preserves existing filter semantics).
		$all_checkins = $bmrequests->bm_fetch_all_order_checkins();
		$filtered     = $all_checkins;

		// Global text search.
		if ( '' !== $search ) {
			$search_date = DateTime::createFromFormat( 'd/m/y', $search );
			if ( $search_date !== false ) {
				$search_date_str = $search_date->format( 'Y-m-d' );
				$filtered        = array_filter(
					$filtered,
					function ( $c ) use ( $search_date_str ) {
						$booking_dt  = $c['booking_date'];
						$checkin_dt  = $c['checkin_time'] !== '-' ? gmdate( 'Y-m-d', strtotime( $c['checkin_time'] ) ) : null;
						return $booking_dt === $search_date_str || $checkin_dt === $search_date_str;
					}
				);
			} else {
				$lower    = strtolower( $search );
				$fields   = array( 'serial_no', 'service_name', 'booking_date', 'first_name', 'last_name', 'contact_no', 'email_address', 'total_cost', 'checkin_time', 'checkin_status' );
				$filtered = array_filter(
					$filtered,
					function ( $c ) use ( $lower, $fields ) {
						foreach ( $fields as $f ) {
							if ( $f === 'checkin_time' && $c[ $f ] === '-' ) {
								continue;
							}
							if ( stripos( (string) $c[ $f ], $lower ) !== false ) {
								return true;
							}
						}
						return $c['checkin_status'] === $lower;
					}
				);
			}
		}

		// Check-in date range filter.
		if ( '' !== $ci_from && '' !== $ci_to ) {
			$ci_from_str = $bmrequests->bm_convert_date_format( $ci_from, 'd/m/y', 'Y-m-d' ) . ' 00:00:00';
			$ci_to_str   = $bmrequests->bm_convert_date_format( $ci_to, 'd/m/y', 'Y-m-d' ) . ' 23:59:59';
			$filtered    = array_filter(
				$filtered,
				function ( $c ) use ( $ci_from_str, $ci_to_str, $bmrequests ) {
					if ( $c['checkin_time'] === '-' ) {
						return false;
					}
					$dt = $bmrequests->bm_convert_date_format( $c['checkin_time'], 'd/m/y H:i', 'Y-m-d H:i' );
					return $dt >= $ci_from_str && $dt <= $ci_to_str;
				}
			);
		}

		// Service (booking) date range filter.
		if ( '' !== $svc_from && '' !== $svc_to ) {
			$svc_from_str = $bmrequests->bm_convert_date_format( $svc_from, 'd/m/y', 'Y-m-d' ) . ' 00:00:00';
			$svc_to_str   = $bmrequests->bm_convert_date_format( $svc_to, 'd/m/y', 'Y-m-d' ) . ' 23:59:59';
			$filtered     = array_filter(
				$filtered,
				function ( $c ) use ( $svc_from_str, $svc_to_str, $bmrequests ) {
					$dt = $bmrequests->bm_convert_date_format( $c['booking_date'], 'd/m/y H:i', 'Y-m-d H:i' );
					return $dt >= $svc_from_str && $dt <= $svc_to_str;
				}
			);
		}

		// Service IDs filter.
		if ( ! empty( $service_ids ) ) {
			$filtered = array_filter(
				$filtered,
				function ( $c ) use ( $service_ids ) {
					return in_array( (int) $c['service_id'], $service_ids, true );
				}
			);
		}

		$total_records = count( $filtered );
		$page_slice    = array_slice( $filtered, $offset, $per_page );

		// Optionally persist the search criteria for later recall.
		if ( $save_search ) {
			$search_data = array(
				'service_from'  => $svc_from,
				'service_to'    => $svc_to,
				'checkin_from'  => $ci_from,
				'checkin_to'    => $ci_to,
				'global_search' => $search,
				'service_ids'   => $service_ids,
			);
			$sanitised   = $bmrequests->sanitize_request(
				array(
					'search_data' => $search_data,
					'user_id'     => $user_id,
					'is_admin'    => $is_admin,
					'module'      => 'checkin',
				),
				'SAVESEARCH'
			);
			if ( $sanitised ) {
				$last_id = $dbhandler->get_all_result(
					'SAVESEARCH',
					'id',
					array( 'user_id' => $user_id, 'module' => 'checkin', 'is_admin' => $is_admin ),
					'var', 0, 1, 'id', 'DESC'
				);
				if ( $last_id ) {
					$dbhandler->update_row( 'SAVESEARCH', 'id', $last_id, $sanitised, '', '%d' );
				} else {
					$sanitised['search_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
					$dbhandler->insert_row( 'SAVESEARCH', $sanitised );
				}
			}
		}

		$saved_search   = $bmrequests->bm_fetch_last_saved_search_data( 'checkin', $is_admin );
		$active_columns = $bmrequests->bm_fetch_active_columns( 'checkin' );
		$column_values  = $bmrequests->bm_fetch_column_order_and_names( 'checkin' );
		$num_of_pages   = (int) ceil( $total_records / $per_page );
		$pagination     = wp_kses_post( (string) $dbhandler->bm_get_pagination( $num_of_pages, $page, $base, 'list' ) );

		return rest_ensure_response(
			array(
				'status'             => true,
				'checkins'           => array_values( $page_slice ),
				'active_columns'     => $active_columns,
				'column_values'      => $column_values,
				'num_of_pages'       => $num_of_pages,
				'saved_search'       => $saved_search,
				'current_pagenumber' => 1 + $offset,
				'pagination'         => $pagination,
				'status_counts'      => BM_Checkin::get_status_counts(),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /checkins/saved-search
	// -------------------------------------------------------------------------

	/**
	 * Return the last saved checkin search criteria for the current admin user.
	 *
	 * @return WP_REST_Response
	 */
	public function get_saved_search() {
		$bmrequests  = new BM_Request();
		$is_admin    = current_user_can( 'manage_options' ) ? 1 : 0;
		$saved       = $bmrequests->bm_fetch_last_saved_search_data( 'checkin', $is_admin );
		return rest_ensure_response( $saved ?? (object) array() );
	}

	// -------------------------------------------------------------------------
	// GET /checkins/export-options
	// -------------------------------------------------------------------------

	/**
	 * Return the export options HTML fragment used by the export modal.
	 *
	 * @return WP_REST_Response
	 */
	public function get_export_options() {
		$bmrequests = new BM_Request();
		$html       = $bmrequests->bm_fetch_export_html_with_options();
		$ok         = ! empty( $html );
		if ( ! $ok ) {
			$html = '<div class="textcenter order_export_html_result">' . esc_html__( 'Something went wrong, try again', 'service-booking' ) . '</div>';
		}
		return rest_ensure_response( array( 'status' => $ok, 'html' => $html ) );
	}

	// -------------------------------------------------------------------------
	// GET /checkins/export
	// -------------------------------------------------------------------------

	/**
	 * Return CSV-ready rows for the current filter set.
	 *
	 * Response keys:
	 *   status  bool
	 *   headers array  — column header labels
	 *   keys    array  — column key names matching headers
	 *   orders  array  — the filtered/sliced rows
	 *
	 * @param WP_REST_Request $request Incoming request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_export_data( WP_REST_Request $request ) {
		$bmrequests  = new BM_Request();
		$dbhandler   = new BM_DBhandler();

		$type        = sanitize_text_field( $request->get_param( 'type' ) ?? 'all' );
		$start_page  = (int) $request->get_param( 'start_page' );
		$end_page    = (int) $request->get_param( 'end_page' );
		$limit       = (int) $request->get_param( 'limit' );
		$total_pages = (int) $request->get_param( 'total_pages' );
		$search      = sanitize_text_field( $request->get_param( 'search' ) ?? '' );
		$svc_from    = sanitize_text_field( $request->get_param( 'service_from' ) ?? '' );
		$svc_to      = sanitize_text_field( $request->get_param( 'service_to' ) ?? '' );
		$ci_from     = sanitize_text_field( $request->get_param( 'checkin_from' ) ?? '' );
		$ci_to       = sanitize_text_field( $request->get_param( 'checkin_to' ) ?? '' );
		$services    = array_map( 'absint', (array) ( $request->get_param( 'services' ) ?? array() ) );
		$services    = array_values( array_filter( $services ) );
		$order_col   = sanitize_key( $request->get_param( 'order_column' ) ?? 'id' );
		$order_dir   = strtoupper( sanitize_text_field( $request->get_param( 'order_dir' ) ?? 'DESC' ) );
		if ( ! in_array( $order_dir, array( 'ASC', 'DESC' ), true ) ) {
			$order_dir = 'DESC';
		}

		$filtered = $bmrequests->bm_fetch_all_order_checkins();

		// Text search.
		if ( '' !== $search ) {
			$lower    = strtolower( $search );
			$fields   = array( 'id', 'booking_id', 'checkin_id', 'serial_no', 'service_id', 'service_name', 'booking_date', 'first_name', 'last_name', 'contact_no', 'email_address', 'total_cost', 'checkin_time', 'checkin_status', 'email_id' );
			$filtered = array_filter(
				$filtered,
				function ( $c ) use ( $lower, $fields ) {
					foreach ( $fields as $f ) {
						if ( isset( $c[ $f ] ) && stripos( strtolower( (string) $c[ $f ] ), $lower ) !== false ) {
							return true;
						}
					}
					return false;
				}
			);
		}

		// Service date range.
		if ( '' !== $svc_from && '' !== $svc_to ) {
			$from_dt = DateTime::createFromFormat( 'd/m/y', $svc_from );
			$to_dt   = DateTime::createFromFormat( 'd/m/y', $svc_to );
			if ( $from_dt && $to_dt ) {
				$filtered = array_filter(
					$filtered,
					function ( $c ) use ( $from_dt, $to_dt ) {
						$dt = DateTime::createFromFormat( 'd/m/y H:i', $c['booking_date'] );
						return $dt && $dt >= $from_dt && $dt <= $to_dt;
					}
				);
			}
		}

		// Check-in date range.
		if ( '' !== $ci_from && '' !== $ci_to ) {
			$from_dt = DateTime::createFromFormat( 'd/m/y', $ci_from );
			$to_dt   = DateTime::createFromFormat( 'd/m/y', $ci_to );
			if ( $from_dt && $to_dt ) {
				$filtered = array_filter(
					$filtered,
					function ( $c ) use ( $from_dt, $to_dt ) {
						$dt = DateTime::createFromFormat( 'd/m/y H:i', $c['checkin_time'] );
						return $dt && $dt >= $from_dt && $dt <= $to_dt;
					}
				);
			}
		}

		// Services filter.
		if ( ! empty( $services ) ) {
			$filtered = array_filter(
				$filtered,
				function ( $c ) use ( $services ) {
					return in_array( (int) $c['service_id'], $services, true );
				}
			);
		}

		$filtered = array_values( $filtered );

		// Sort.
		if ( '' !== $order_col ) {
			$filtered = $bmrequests->bm_sort_array_by_key( $filtered, $order_col, strtolower( $order_dir ) === 'desc' );
		}

		// Slice.
		$offset = 0;
		switch ( $type ) {
			case 'all':
				$offset = 0;
				$limit  = 0;
				break;
			case 'current':
				// $offset and $limit already set from params.
				break;
			case 'range':
				if ( $start_page > 0 && $end_page > 0 && $start_page <= $end_page && ( 0 === $total_pages || $end_page <= $total_pages ) && $limit > 0 ) {
					$offset = ( $start_page - 1 ) * $limit;
					$limit  = ( $end_page - $start_page + 1 ) * $limit;
				} else {
					$filtered = array();
				}
				break;
			default:
				$filtered = array();
				break;
		}

		$exclude_cols   = array( 'ticket_pdf', 'actions' );
		$column_headers = array_values( array_diff( array_values( $bmrequests->bm_fetch_active_columns( 'checkin' ) ), array( 'Ticket PDF', 'Actions', 'PDF del biglietto', 'Azioni' ) ) );
		$active_keys    = array_keys( $bmrequests->bm_fetch_active_columns( 'checkin' ) );

		$data = array( 'status' => false );

		if ( ! empty( $filtered ) ) {
			$filtered = $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $filtered, $offset, $limit );
		}

		if ( ! empty( $filtered ) && ! empty( $active_keys ) ) {
			$filtered        = $dbhandler->filter_existing_data_by_columns( $filtered, $active_keys, $exclude_cols, true );
			$data['status']  = true;
		}

		$export_keys = array_values( array_diff( $active_keys, $exclude_cols ) );
		$data['headers'] = $data['status'] ? $column_headers : array();
		$data['keys']    = $data['status'] ? $export_keys : array();
		$data['orders']  = $data['status'] && ! empty( $filtered ) ? $filtered : array();

		return rest_ensure_response( $data );
	}
}
