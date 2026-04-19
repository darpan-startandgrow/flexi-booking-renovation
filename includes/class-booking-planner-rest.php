<?php
/**
 * Booking Planner REST API
 *
 * Dedicated REST API for the modern booking planner page (page=bm_booking_planner).
 * Namespaced under booking-planner/v1/, fully decoupled from class-booking-api.php.
 *
 * All endpoints require manage_options (admin only).
 * Reads/writes reuse existing BM_DBhandler and BM_Request without modifying them.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Booking_Planner_REST
 *
 * @since 1.0.0
 */
class Booking_Planner_REST {

	const NAMESPACE = 'booking-planner/v1';

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
		// GET /services
		register_rest_route(
			self::NAMESPACE,
			'/services',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_services' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'category_id' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /categories
		register_rest_route(
			self::NAMESPACE,
			'/categories',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_categories' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
			)
		);

		// GET /bookings
		register_rest_route(
			self::NAMESPACE,
			'/bookings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_bookings' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'start_date'   => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'end_date'     => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'service_id'   => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
					'order_status' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// GET /timeslots
		register_rest_route(
			self::NAMESPACE,
			'/timeslots',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_timeslots' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'service_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'date'       => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// GET /reservations
		register_rest_route(
			self::NAMESPACE,
			'/reservations',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_reservations' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'service_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'date'       => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'time_slot'  => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// POST /bookings
		register_rest_route(
			self::NAMESPACE,
			'/bookings',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'create_booking' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'service_id'      => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'booking_date'    => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'time_slot_from'  => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'time_slot_to'    => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'total_svc_slots' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
					'customer_name'   => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'customer_email'  => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_email',
					),
				),
			)
		);

		// PUT /bookings/{id}
		register_rest_route(
			self::NAMESPACE,
			'/bookings/(?P<id>\d+)',
			array(
				'methods'             => 'PUT',
				'callback'            => array( $this, 'update_booking' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'id'             => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'booking_date'   => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'booking_slots'  => array(
						'required'          => false,
						'sanitize_callback' => array( __CLASS__, 'sanitize_array_callback' ),
					),
					'time_slot_from' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'time_slot_to'   => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'service_id'     => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
					'order_status'   => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// DELETE /bookings/{id}
		register_rest_route(
			self::NAMESPACE,
			'/bookings/(?P<id>\d+)',
			array(
				'methods'             => 'DELETE',
				'callback'            => array( $this, 'delete_booking' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /availability
		register_rest_route(
			self::NAMESPACE,
			'/availability',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_availability' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'service_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'date'       => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
				),
			)
		);

		// GET /planner-week
		register_rest_route(
			self::NAMESPACE,
			'/planner-week',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_planner_week' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'start_date'  => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'end_date'    => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'service_id'  => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
					'category_id' => array(
						'required'          => false,
						'sanitize_callback' => 'absint',
					),
				),
			)
		);

		// GET /slot-bookings
		register_rest_route(
			self::NAMESPACE,
			'/slot-bookings',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_slot_bookings' ),
				'permission_callback' => array( $this, 'admin_permission_check' ),
				'args'                => array(
					'service_id' => array(
						'required'          => true,
						'sanitize_callback' => 'absint',
					),
					'date'       => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					),
					'time_slot'  => array(
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
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
	// Sanitization helpers
	// -------------------------------------------------------------------------

	/**
	 * Recursively sanitize an array value.
	 *
	 * @param mixed $value Value to sanitize.
	 * @return mixed Sanitized value.
	 */
	public static function sanitize_array_callback( $value ) {
		if ( is_array( $value ) ) {
			return array_map( array( __CLASS__, 'sanitize_array_callback' ), $value );
		}
		if ( is_numeric( $value ) ) {
			return $value + 0;
		}
		return sanitize_text_field( $value );
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
	private function validate_date( $date ) {
		$dt = DateTime::createFromFormat( 'Y-m-d', $date );
		return $dt && $dt->format( 'Y-m-d' ) === $date;
	}

	/**
	 * Validate a time string in H:i format.
	 *
	 * @param string $time Time string.
	 * @return bool
	 */
	private function validate_time( $time ) {
		return (bool) preg_match( '/^\d{2}:\d{2}$/', $time );
	}

	/**
	 * Extract service type from serialized service_settings.
	 *
	 * @param object $service Service row object.
	 * @return string
	 */
	private function extract_service_type( $service ) {
		if ( ! empty( $service->service_settings ) ) {
			$settings = maybe_unserialize( $service->service_settings );
			if ( is_array( $settings ) && isset( $settings['service_type'] ) ) {
				return sanitize_text_field( $settings['service_type'] );
			}
		}
		return 'entries';
	}

	// -------------------------------------------------------------------------
	// GET /services
	// -------------------------------------------------------------------------

	/**
	 * Fetch all active services.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response
	 */
	public function get_services( WP_REST_Request $request ) {
		$dbhandler = new BM_DBhandler();
		$params    = $request->get_query_params();

		$where = array( 'service_status' => 1 );
		if ( ! empty( $params['category_id'] ) ) {
			$where['service_category'] = absint( $params['category_id'] );
		}

		$services = $dbhandler->get_all_result(
			'SERVICE',
			'*',
			$where,
			'results',
			0,
			false,
			'service_position',
			false
		);

		$response = array();
		if ( ! empty( $services ) ) {
			foreach ( $services as $service ) {
				$category_name = '';
				if ( ! empty( $service->service_category ) ) {
					$category_name = (string) $dbhandler->get_value(
						'CATEGORY',
						'cat_name',
						$service->service_category
					);
				}
				$response[] = array(
					'id'               => (int) $service->id,
					'service_name'     => $service->service_name,
					'service_duration' => $service->service_duration,
					'default_price'    => $service->default_price,
					'service_category' => (int) $service->service_category,
					'category_name'    => $category_name,
					'service_image'    => ! empty( $service->service_image_guid ) ? ( wp_get_attachment_url( (int) $service->service_image_guid ) ?: '' ) : '',
					'service_position' => (int) $service->service_position,
					'service_type'     => $this->extract_service_type( $service ),
					'total_svc_slots'  => isset( $service->default_max_cap ) ? (int) $service->default_max_cap : 1,
				);
			}
		}

		return rest_ensure_response(
			array(
				'services' => $response,
				'total'    => count( $response ),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /categories
	// -------------------------------------------------------------------------

	/**
	 * Fetch all active categories.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response
	 */
	public function get_categories( WP_REST_Request $request ) {
		$dbhandler  = new BM_DBhandler();
		$categories = $dbhandler->get_all_result(
			'CATEGORY',
			'*',
			array( 'cat_status' => 1 ),
			'results',
			0,
			false,
			'cat_position',
			false
		);

		$response = array();
		if ( ! empty( $categories ) ) {
			foreach ( $categories as $cat ) {
				$response[] = array(
					'id'           => (int) $cat->id,
					'cat_name'     => $cat->cat_name,
					'cat_position' => (int) $cat->cat_position,
					'cat_options'  => $cat->cat_options ? maybe_unserialize( $cat->cat_options ) : null,
				);
			}
		}

		return rest_ensure_response(
			array(
				'categories' => $response,
				'total'      => count( $response ),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /bookings
	// -------------------------------------------------------------------------

	/**
	 * Fetch bookings within a date range.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_bookings( WP_REST_Request $request ) {
		$params     = $request->get_query_params();
		$start_date = sanitize_text_field( $params['start_date'] );
		$end_date   = sanitize_text_field( $params['end_date'] );

		if ( ! $this->validate_date( $start_date ) || ! $this->validate_date( $end_date ) ) {
			return new WP_Error(
				'invalid_date',
				__( 'start_date and end_date must be in Y-m-d format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		$where_clauses = array(
			'b.booking_date' => array(
				'>=' => $start_date,
				'<=' => $end_date,
			),
		);

		if ( ! empty( $params['service_id'] ) ) {
			$where_clauses['b.service_id'] = array( '=' => absint( $params['service_id'] ) );
		}

		if ( ! empty( $params['order_status'] ) ) {
			$where_clauses['b.order_status'] = array( '=' => sanitize_text_field( $params['order_status'] ) );
		}

		$columns = 'b.id, b.service_id, b.service_name, b.customer_id, b.booking_date, b.booking_slots, b.total_svc_slots, b.base_svc_price, b.service_cost, b.extra_svc_cost, b.subtotal, b.total_cost, b.order_status, b.booking_key, b.is_frontend_booking, b.booking_created_at, b.is_active, c.customer_name, c.customer_email, t.payment_status, t.payment_method';

		$joins = array(
			array(
				'type'  => 'LEFT',
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'b.customer_id = c.id',
			),
			array(
				'type'  => 'LEFT',
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id AND t.is_active = 1',
			),
		);

		$bookings = $dbhandler->get_results_with_join(
			array( 'BOOKING', 'b' ),
			$columns,
			$joins,
			$where_clauses,
			'results',
			0,
			false,
			'b.booking_date',
			false
		);

		$response = array();
		if ( ! empty( $bookings ) ) {
			foreach ( $bookings as $booking ) {
				$slots        = maybe_unserialize( $booking->booking_slots );
				$status_label = $bmrequests->bm_fetch_order_status_key_value( $booking->order_status );

				$response[] = array(
					'id'                   => (int) $booking->id,
					'service_id'           => (int) $booking->service_id,
					'service_name'         => $booking->service_name,
					'service_display_name' => $booking->service_name,
					'customer_id'          => (int) $booking->customer_id,
					'customer_name'        => $booking->customer_name,
					'customer_email'       => $booking->customer_email,
					'booking_date'         => $booking->booking_date,
					'booking_slots'        => is_array( $slots ) ? $slots : array(),
					'total_svc_slots'      => (int) $booking->total_svc_slots,
					'base_svc_price'       => $booking->base_svc_price,
					'service_cost'         => $booking->service_cost,
					'extra_svc_cost'       => $booking->extra_svc_cost,
					'subtotal'             => $booking->subtotal,
					'total_cost'           => $booking->total_cost,
					'order_status'         => $booking->order_status,
					'order_status_label'   => $status_label,
					'payment_status'       => $booking->payment_status,
					'payment_method'       => $booking->payment_method,
					'booking_key'          => $booking->booking_key,
					'is_frontend_booking'  => (int) $booking->is_frontend_booking,
					'booking_created_at'   => $booking->booking_created_at,
					'is_active'            => (int) $booking->is_active,
				);
			}
		}

		return rest_ensure_response(
			array(
				'bookings' => $response,
				'total'    => count( $response ),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /timeslots
	// -------------------------------------------------------------------------

	/**
	 * Fetch available timeslots for a service on a given date.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_timeslots( WP_REST_Request $request ) {
		$params     = $request->get_query_params();
		$service_id = absint( $params['service_id'] );
		$date       = sanitize_text_field( $params['date'] );

		if ( ! $this->validate_date( $date ) ) {
			return new WP_Error(
				'invalid_date',
				__( 'The date must be in Y-m-d format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$bmrequests = new BM_Request();
		$slot_data  = $bmrequests->bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date(
			$service_id,
			$date
		);

		return rest_ensure_response(
			array(
				'service_id' => $service_id,
				'date'       => $date,
				'timeslots'  => ! empty( $slot_data ) ? $slot_data : array(),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /reservations
	// -------------------------------------------------------------------------

	/**
	 * Fetch reservations for a specific service, date, and timeslot.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_reservations( WP_REST_Request $request ) {
		$params     = $request->get_query_params();
		$service_id = absint( $params['service_id'] );
		$date       = sanitize_text_field( $params['date'] );
		$time_slot  = sanitize_text_field( $params['time_slot'] );

		if ( ! $this->validate_date( $date ) ) {
			return new WP_Error(
				'invalid_date',
				__( 'The date must be in Y-m-d format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		if ( ! $this->validate_time( $time_slot ) ) {
			return new WP_Error(
				'invalid_time',
				__( 'The time_slot must be in HH:MM format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$bmrequests   = new BM_Request();
		$reservations = $bmrequests->bm_fetch_service_planner_reservation_list(
			$service_id,
			$date,
			$time_slot
		);

		return rest_ensure_response(
			array(
				'service_id'   => $service_id,
				'date'         => $date,
				'time_slot'    => $time_slot,
				'reservations' => ! empty( $reservations ) ? $reservations : array(),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /availability
	// -------------------------------------------------------------------------

	/**
	 * Check availability for a service on a specific date.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_availability( WP_REST_Request $request ) {
		$params     = $request->get_query_params();
		$service_id = absint( $params['service_id'] );
		$date       = sanitize_text_field( $params['date'] );

		if ( ! $this->validate_date( $date ) ) {
			return new WP_Error(
				'invalid_date',
				__( 'The date must be in Y-m-d format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$bmrequests  = new BM_Request();
		$is_bookable = $bmrequests->bm_service_is_bookable( $service_id, $date );

		$timeslots = array();
		if ( $is_bookable ) {
			$slot_data = $bmrequests->bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date(
				$service_id,
				$date
			);
			$timeslots = ! empty( $slot_data ) ? $slot_data : array();
		}

		return rest_ensure_response(
			array(
				'service_id'  => $service_id,
				'date'        => $date,
				'is_bookable' => (bool) $is_bookable,
				'timeslots'   => $timeslots,
			)
		);
	}

	// -------------------------------------------------------------------------
	// POST /bookings
	// -------------------------------------------------------------------------

	/**
	 * Create a new admin booking.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function create_booking( WP_REST_Request $request ) {
		$params = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		$service_id    = absint( $params['service_id'] ?? 0 );
		$booking_date  = sanitize_text_field( $params['booking_date'] ?? '' );
		$time_slot_from = sanitize_text_field( $params['time_slot_from'] ?? '' );
		$time_slot_to   = sanitize_text_field( $params['time_slot_to']   ?? '' );
		$total_slots    = absint( $params['total_svc_slots'] ?? 1 );
		$customer_name  = sanitize_text_field( $params['customer_name']  ?? '' );
		$customer_email = sanitize_email( $params['customer_email'] ?? '' );

		// Validate required fields.
		if ( ! $service_id ) {
			return new WP_Error( 'missing_service', __( 'service_id is required.', 'service-booking' ), array( 'status' => 400 ) );
		}
		if ( ! $this->validate_date( $booking_date ) ) {
			return new WP_Error( 'invalid_date', __( 'booking_date must be in Y-m-d format.', 'service-booking' ), array( 'status' => 400 ) );
		}
		if ( ! $this->validate_time( $time_slot_from ) || ! $this->validate_time( $time_slot_to ) ) {
			return new WP_Error( 'invalid_time', __( 'time_slot_from and time_slot_to must be in HH:MM format.', 'service-booking' ), array( 'status' => 400 ) );
		}

		$dbhandler = new BM_DBhandler();

		// Verify service exists.
		$service = $dbhandler->get_row( 'SERVICE', $service_id );
		if ( empty( $service ) ) {
			return new WP_Error( 'service_not_found', __( 'Service not found.', 'service-booking' ), array( 'status' => 404 ) );
		}

		$dbhandler->begin_transaction();

		try {
			global $wpdb;

			$booking_slots = array( 'from' => $time_slot_from, 'to' => $time_slot_to );

			// Find or create customer.
			$customer_id = 0;
			if ( $customer_email ) {
				$bm_activator = new Booking_Management_Activator();
				$cust_table   = $bm_activator->get_db_table_name( 'CUSTOMERS' );
				$existing     = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM {$cust_table} WHERE customer_email = %s LIMIT 1", $customer_email ) );
				if ( $existing ) {
					$customer_id = (int) $existing->id;
				} else {
					$wpdb->insert(
						$cust_table,
						array(
							'customer_name'  => $customer_name,
							'customer_email' => $customer_email,
						),
						array( '%s', '%s' )
					);
					$customer_id = (int) $wpdb->insert_id;
				}
			}

			// Insert booking record.
			$bm_activator = new Booking_Management_Activator();
			$bkg_table    = $bm_activator->get_db_table_name( 'BOOKING' );

			$booking_data = array(
				'service_id'          => $service_id,
				'service_name'        => $service->service_name,
				'customer_id'         => $customer_id,
				'booking_date'        => $booking_date,
				'booking_slots'       => maybe_serialize( $booking_slots ),
				'total_svc_slots'     => max( 1, $total_slots ),
				'base_svc_price'      => $service->default_price,
				'service_cost'        => $service->default_price,
				'extra_svc_cost'      => 0,
				'subtotal'            => $service->default_price,
				'total_cost'          => $service->default_price,
				'order_status'        => 'pending',
				'booking_key'         => wp_generate_password( 20, false ),
				'is_frontend_booking' => 0,
				'booking_created_at'  => current_time( 'mysql' ),
				'is_active'           => 1,
			);

			$inserted = $wpdb->insert( $bkg_table, $booking_data );

			if ( ! $inserted ) {
				throw new Exception( __( 'Failed to insert booking record.', 'service-booking' ) );
			}

			$booking_id = (int) $wpdb->insert_id;

			// Update slot capacity (decrement).
			$slot_table = $bm_activator->get_db_table_name( 'SLOTCOUNT' );
			$wpdb->query(
				$wpdb->prepare(
					"UPDATE {$slot_table} SET slot_cap_left = GREATEST(0, slot_cap_left - 1) WHERE service_id = %d AND booking_date = %s AND slot_id = %s",
					$service_id,
					$booking_date,
					$time_slot_from
				)
			);

			$dbhandler->commit_transaction();

			// Fetch and return the created booking.
			$created_booking = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bkg_table} WHERE id = %d", $booking_id ) );

			return rest_ensure_response(
				array(
					'booking' => array(
						'id'                   => $booking_id,
						'service_id'           => $service_id,
						'service_name'         => $service->service_name,
						'service_display_name' => $service->service_name,
						'booking_date'         => $booking_date,
						'booking_slots'        => $booking_slots,
						'total_svc_slots'      => max( 1, $total_slots ),
						'order_status'         => 'pending',
						'customer_name'        => $customer_name,
						'customer_email'       => $customer_email,
						'total_cost'           => $service->default_price,
						'booking_key'          => $booking_data['booking_key'],
						'booking_created_at'   => $booking_data['booking_created_at'],
						'is_active'            => 1,
					),
					'message' => __( 'Booking created successfully.', 'service-booking' ),
				)
			);

		} catch ( Exception $e ) {
			$dbhandler->rollback_transaction();
			return new WP_Error( 'booking_error', $e->getMessage(), array( 'status' => 500 ) );
		}
	}

	// -------------------------------------------------------------------------
	// PUT /bookings/{id}
	// -------------------------------------------------------------------------

	/**
	 * Update a booking's date, slots, service, or status.
	 *
	 * Accepts either booking_slots object or time_slot_from/time_slot_to pair.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function update_booking( WP_REST_Request $request ) {
		$booking_id = absint( $request->get_param( 'id' ) );
		$params     = $request->get_json_params();
		if ( empty( $params ) ) {
			$params = $request->get_body_params();
		}

		$dbhandler = new BM_DBhandler();
		$booking   = $dbhandler->get_row( 'BOOKING', $booking_id );

		if ( empty( $booking ) ) {
			return new WP_Error(
				'booking_not_found',
				__( 'The specified booking does not exist.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		$update_data   = array();
		$update_format = array();

		$new_booking_date = isset( $params['booking_date'] ) ? sanitize_text_field( $params['booking_date'] ) : null;
		$new_slots        = isset( $params['booking_slots'] ) ? $params['booking_slots'] : null;
		$new_service_id   = isset( $params['service_id'] ) ? absint( $params['service_id'] ) : null;
		$new_status       = isset( $params['order_status'] ) ? sanitize_text_field( $params['order_status'] ) : null;

		// Normalise time_slot_from / time_slot_to into booking_slots.
		if ( null === $new_slots && ( isset( $params['time_slot_from'] ) || isset( $params['time_slot_to'] ) ) ) {
			$existing_slots = maybe_unserialize( $booking->booking_slots );
			$existing_slots = is_array( $existing_slots ) ? $existing_slots : array( 'from' => '', 'to' => '' );
			$new_slots      = array(
				'from' => isset( $params['time_slot_from'] ) ? sanitize_text_field( $params['time_slot_from'] ) : $existing_slots['from'],
				'to'   => isset( $params['time_slot_to'] )   ? sanitize_text_field( $params['time_slot_to'] )   : $existing_slots['to'],
			);
		}

		// Validate and apply booking_date.
		if ( null !== $new_booking_date ) {
			if ( ! $this->validate_date( $new_booking_date ) ) {
				return new WP_Error( 'invalid_date', __( 'booking_date must be in Y-m-d format.', 'service-booking' ), array( 'status' => 400 ) );
			}
			$update_data['booking_date'] = $new_booking_date;
			$update_format[]             = '%s';
		}

		// Validate and apply booking_slots.
		if ( null !== $new_slots ) {
			if ( ! is_array( $new_slots ) || ! isset( $new_slots['from'] ) || ! isset( $new_slots['to'] ) ) {
				return new WP_Error( 'invalid_slots', __( 'booking_slots must have "from" and "to" keys.', 'service-booking' ), array( 'status' => 400 ) );
			}
			if ( ! $this->validate_time( $new_slots['from'] ) || ! $this->validate_time( $new_slots['to'] ) ) {
				return new WP_Error( 'invalid_time', __( 'booking_slots from/to must be in HH:MM format.', 'service-booking' ), array( 'status' => 400 ) );
			}
			$update_data['booking_slots'] = maybe_serialize( $new_slots );
			$update_format[]              = '%s';
		}

		// Validate and apply service_id.
		if ( null !== $new_service_id && $new_service_id > 0 ) {
			$new_service = $dbhandler->get_row( 'SERVICE', $new_service_id );
			if ( empty( $new_service ) ) {
				return new WP_Error( 'service_not_found', __( 'The specified service does not exist.', 'service-booking' ), array( 'status' => 404 ) );
			}
			$update_data['service_id']   = $new_service_id;
			$update_data['service_name'] = $new_service->service_name;
			$update_format[]             = '%d';
			$update_format[]             = '%s';
		}

		// Apply order_status.
		if ( null !== $new_status ) {
			$update_data['order_status'] = $new_status;
			$update_format[]             = '%s';
		}

		if ( empty( $update_data ) ) {
			return new WP_Error( 'no_changes', __( 'No valid fields to update.', 'service-booking' ), array( 'status' => 400 ) );
		}

		$dbhandler->begin_transaction();
		try {
			global $wpdb;
			$bm_activator = new Booking_Management_Activator();
			$bkg_table    = $bm_activator->get_db_table_name( 'BOOKING' );

			$result = $wpdb->update(
				$bkg_table,
				$update_data,
				array( 'id' => $booking_id ),
				$update_format,
				array( '%d' )
			);

			if ( false === $result ) {
				throw new Exception( __( 'Failed to update booking record.', 'service-booking' ) );
			}

			$dbhandler->commit_transaction();

			$updated = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$bkg_table} WHERE id = %d", $booking_id ) );
			$slots   = maybe_unserialize( $updated->booking_slots );

			return rest_ensure_response(
				array(
					'booking' => array(
						'id'                   => (int) $updated->id,
						'service_id'           => (int) $updated->service_id,
						'service_name'         => $updated->service_name,
						'service_display_name' => $updated->service_name,
						'booking_date'         => $updated->booking_date,
						'booking_slots'        => is_array( $slots ) ? $slots : array(),
						'order_status'         => $updated->order_status,
						'is_active'            => (int) $updated->is_active,
					),
					'message' => __( 'Booking updated successfully.', 'service-booking' ),
				)
			);

		} catch ( Exception $e ) {
			$dbhandler->rollback_transaction();
			return new WP_Error( 'booking_error', $e->getMessage(), array( 'status' => 500 ) );
		}
	}

	// -------------------------------------------------------------------------
	// DELETE /bookings/{id}
	// -------------------------------------------------------------------------

	/**
	 * Soft-delete (cancel) a booking.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function delete_booking( WP_REST_Request $request ) {
		$booking_id = absint( $request->get_param( 'id' ) );
		$dbhandler  = new BM_DBhandler();
		$booking    = $dbhandler->get_row( 'BOOKING', $booking_id );

		if ( empty( $booking ) ) {
			return new WP_Error( 'booking_not_found', __( 'The specified booking does not exist.', 'service-booking' ), array( 'status' => 404 ) );
		}

		if ( 'cancelled' === $booking->order_status && 0 === (int) $booking->is_active ) {
			return rest_ensure_response(
				array(
					'success' => true,
					'message' => __( 'Booking is already cancelled.', 'service-booking' ),
				)
			);
		}

		$dbhandler->begin_transaction();
		try {
			global $wpdb;
			$bm_activator = new Booking_Management_Activator();
			$bkg_table    = $bm_activator->get_db_table_name( 'BOOKING' );

			$result = $wpdb->update(
				$bkg_table,
				array(
					'order_status' => 'cancelled',
					'is_active'    => 0,
				),
				array( 'id' => $booking_id ),
				array( '%s', '%d' ),
				array( '%d' )
			);

			if ( false === $result ) {
				throw new Exception( __( 'Failed to cancel booking.', 'service-booking' ) );
			}

			// Restore slot capacity.
			$slots     = maybe_unserialize( $booking->booking_slots );
			$slot_from = is_array( $slots ) && isset( $slots['from'] ) ? $slots['from'] : '';

			if ( $slot_from ) {
				$slot_table = $bm_activator->get_db_table_name( 'SLOTCOUNT' );
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$slot_table} SET slot_cap_left = slot_cap_left + 1 WHERE service_id = %d AND booking_date = %s AND slot_id = %s",
						(int) $booking->service_id,
						$booking->booking_date,
						$slot_from
					)
				);
			}

			$dbhandler->commit_transaction();

			return rest_ensure_response(
				array(
					'success' => true,
					'booking' => array(
						'id'           => $booking_id,
						'order_status' => 'cancelled',
						'is_active'    => 0,
					),
					'message' => __( 'Booking cancelled successfully.', 'service-booking' ),
				)
			);

		} catch ( Exception $e ) {
			$dbhandler->rollback_transaction();
			return new WP_Error( 'booking_error', $e->getMessage(), array( 'status' => 500 ) );
		}
	}

	// -------------------------------------------------------------------------
	// GET /planner-week
	// -------------------------------------------------------------------------

	/**
	 * Bulk-fetch services + slot availability for a date range.
	 *
	 * Returns:
	 *   services[]          – array of service objects
	 *   slots{}             – map service_id → date → [{slot_id,from,to,time_display,max_capacity,available_capacity,booking_count}]
	 *   summary{}           – { total_services, total_bookings }
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_planner_week( WP_REST_Request $request ) {
		$params     = $request->get_query_params();
		$start_date = sanitize_text_field( $params['start_date'] );
		$end_date   = sanitize_text_field( $params['end_date'] );

		if ( ! $this->validate_date( $start_date ) || ! $this->validate_date( $end_date ) ) {
			return new WP_Error(
				'invalid_date',
				__( 'start_date and end_date must be in Y-m-d format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		// Build service filter.
		$svc_where = array( 'service_status' => 1 );
		if ( ! empty( $params['category_id'] ) ) {
			$svc_where['service_category'] = absint( $params['category_id'] );
		}
		if ( ! empty( $params['service_id'] ) ) {
			$svc_where['id'] = absint( $params['service_id'] );
		}

		$raw_services = $dbhandler->get_all_result(
			'SERVICE',
			'*',
			$svc_where,
			'results',
			0,
			false,
			'service_position',
			false
		);

		if ( empty( $raw_services ) ) {
			return rest_ensure_response(
				array(
					'services' => array(),
					'slots'    => array(),
					'summary'  => array( 'total_services' => 0, 'total_bookings' => 0 ),
				)
			);
		}

		// Build service list + category name map.
		$services      = array();
		$service_ids   = array();
		$cat_name_cache = array();

		foreach ( $raw_services as $svc ) {
			$service_ids[] = (int) $svc->id;

			$cat_name = '';
			$cat_id   = (int) $svc->service_category;
			if ( $cat_id > 0 ) {
				if ( ! isset( $cat_name_cache[ $cat_id ] ) ) {
					$cat_name_cache[ $cat_id ] = (string) $dbhandler->get_value( 'CATEGORY', 'cat_name', $cat_id );
				}
				$cat_name = $cat_name_cache[ $cat_id ];
			}

			$services[] = array(
				'id'               => (int) $svc->id,
				'service_name'     => $svc->service_name,
				'service_duration' => $svc->service_duration,
				'default_price'    => $svc->default_price,
				'service_category' => $cat_id,
				'category_name'    => $cat_name,
				'service_image'    => ! empty( $svc->service_image_guid ) ? ( wp_get_attachment_url( (int) $svc->service_image_guid ) ?: '' ) : '',
				'service_position' => (int) $svc->service_position,
				'service_type'     => $this->extract_service_type( $svc ),
				'total_svc_slots'  => isset( $svc->default_max_cap ) ? (int) $svc->default_max_cap : 1,
			);
		}

		// Pre-fetch all bookings in the range for all services in one query.
		global $wpdb;
		$bm_activator = new Booking_Management_Activator();
		$bkg_table    = $bm_activator->get_db_table_name( 'BOOKING' );
		$ids_ph       = implode( ',', array_fill( 0, count( $service_ids ), '%d' ) );
		$query_args   = array_merge( $service_ids, array( $start_date, $end_date ) );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$raw_bookings = $wpdb->get_results(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT service_id, booking_date, booking_slots FROM {$bkg_table}
				 WHERE is_active = 1
				   AND service_id IN ({$ids_ph})
				   AND booking_date BETWEEN %s AND %s",
				$query_args
			)
		);

		// Build booking-count lookup: [ "svcId_date_fromTime" => count ].
		$bkg_count = array();
		$total_bookings = 0;
		if ( ! empty( $raw_bookings ) ) {
			foreach ( $raw_bookings as $bkg ) {
				$slots = maybe_unserialize( $bkg->booking_slots );
				$from  = is_array( $slots ) && isset( $slots['from'] ) ? $slots['from'] : '';
				if ( ! $from ) {
					continue;
				}
				$key = (int) $bkg->service_id . '_' . $bkg->booking_date . '_' . $from;
				$bkg_count[ $key ] = isset( $bkg_count[ $key ] ) ? $bkg_count[ $key ] + 1 : 1;
				$total_bookings++;
			}
		}

		// Build slot map per service per date.
		$slots_map = array();

		// Generate all dates in range.
		$dates    = array();
		$cur_date = new DateTime( $start_date );
		$end_dt   = new DateTime( $end_date );
		while ( $cur_date <= $end_dt ) {
			$dates[] = $cur_date->format( 'Y-m-d' );
			$cur_date->modify( '+1 day' );
		}

		foreach ( $services as $svc ) {
			$svc_id              = $svc['id'];
			$slots_map[ $svc_id ] = array();

			foreach ( $dates as $date ) {
				$slot_data = $bmrequests->bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date(
					$svc_id,
					$date
				);

				if ( empty( $slot_data ) ) {
					$slots_map[ $svc_id ][ $date ] = array();
					continue;
				}

				$day_slots = array();
				foreach ( $slot_data as $slot ) {
					$from        = isset( $slot['from'] ) ? $slot['from'] : ( isset( $slot['slot_id'] ) ? $slot['slot_id'] : '' );
					$to          = isset( $slot['to'] )   ? $slot['to']   : '';
					if ( empty( $to ) && ! empty( $from ) ) {
						// Derive end time from service duration.
						$duration = isset( $svc['service_duration'] ) ? (int) $svc['service_duration'] : 60;
						$from_obj = DateTime::createFromFormat( 'H:i', $from );
						if ( $from_obj ) {
							$from_obj->modify( '+' . $duration . ' minutes' );
							$to = $from_obj->format( 'H:i' );
						}
					}
					$max_cap     = isset( $slot['max_cap'] )  ? (int) $slot['max_cap']  : 0;
					$cap_left    = isset( $slot['cap_left'] ) ? (int) $slot['cap_left'] : 0;
					$bkg_key     = $svc_id . '_' . $date . '_' . $from;
					$bkg_cnt     = isset( $bkg_count[ $bkg_key ] ) ? $bkg_count[ $bkg_key ] : 0;

					$day_slots[] = array(
						'slot_id'            => $from,
						'from'               => $from,
						'to'                 => $to,
						'time_display'       => $from . ( $to ? ' - ' . $to : '' ),
						'max_capacity'       => $max_cap,
						'available_capacity' => $cap_left,
						'booking_count'      => $bkg_cnt,
					);
				}
				$slots_map[ $svc_id ][ $date ] = $day_slots;
			}
		}

		return rest_ensure_response(
			array(
				'services' => $services,
				'slots'    => $slots_map,
				'summary'  => array(
					'total_services' => count( $services ),
					'total_bookings' => $total_bookings,
				),
			)
		);
	}

	// -------------------------------------------------------------------------
	// GET /slot-bookings
	// -------------------------------------------------------------------------

	/**
	 * Return bookings for a specific service + date + time slot.
	 *
	 * @param WP_REST_Request $request REST request.
	 * @return WP_REST_Response|WP_Error
	 */
	public function get_slot_bookings( WP_REST_Request $request ) {
		$params     = $request->get_query_params();
		$service_id = absint( $params['service_id'] );
		$date       = sanitize_text_field( $params['date'] );
		$time_slot  = sanitize_text_field( $params['time_slot'] );

		if ( ! $this->validate_date( $date ) ) {
			return new WP_Error(
				'invalid_date',
				__( 'The date must be in Y-m-d format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		if ( ! $this->validate_time( $time_slot ) ) {
			return new WP_Error(
				'invalid_time',
				__( 'The time_slot must be in HH:MM format.', 'service-booking' ),
				array( 'status' => 400 )
			);
		}

		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		// Get service info.
		$service = $dbhandler->get_row( 'SERVICE', $service_id );
		if ( empty( $service ) ) {
			return new WP_Error(
				'service_not_found',
				__( 'Service not found.', 'service-booking' ),
				array( 'status' => 404 )
			);
		}

		// Fetch bookings for this slot via JOIN.
		$columns = 'b.id, b.service_id, b.booking_key, b.total_svc_slots, b.base_svc_price, b.extra_svc_cost, b.total_cost, b.order_status, b.booking_slots, c.customer_name, c.customer_email, t.payment_status, t.payment_method';

		$joins = array(
			array(
				'type'  => 'LEFT',
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'b.customer_id = c.id',
			),
			array(
				'type'  => 'LEFT',
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id AND t.is_active = 1',
			),
		);

		$where_clauses = array(
			'b.service_id'   => array( '=' => $service_id ),
			'b.booking_date' => array( '=' => $date ),
			'b.is_active'    => array( '=' => 1 ),
		);

		$all_bookings = $dbhandler->get_results_with_join(
			array( 'BOOKING', 'b' ),
			$columns,
			$joins,
			$where_clauses,
			'results',
			0,
			false,
			'b.id',
			false
		);

		// Filter to the requested time slot.
		$response_bookings = array();
		if ( ! empty( $all_bookings ) ) {
			foreach ( $all_bookings as $bkg ) {
				$slots = maybe_unserialize( $bkg->booking_slots );
				$from  = is_array( $slots ) && isset( $slots['from'] ) ? $slots['from'] : '';
				if ( $from !== $time_slot ) {
					continue;
				}

				// Derive last name from customer_name.
				$customer_name = $bkg->customer_name ?: '';
				$name_parts    = explode( ' ', trim( $customer_name ) );
				$last_name     = count( $name_parts ) > 1 ? end( $name_parts ) : $customer_name;

				// Extra participants: bookings over base price.
				$base_price  = (float) $bkg->base_svc_price;
				$extra_cost  = (float) $bkg->extra_svc_cost;
				$extra_pax   = ( $base_price > 0 ) ? (int) round( $extra_cost / $base_price ) : 0;

				$response_bookings[] = array(
					'id'                 => (int) $bkg->id,
					'order_ref'          => $bkg->booking_key,
					'customer_name'      => $customer_name,
					'customer_last_name' => $last_name,
					'customer_email'     => $bkg->customer_email,
					'total_svc_slots'    => (int) $bkg->total_svc_slots,
					'extra_participants' => $extra_pax,
					'order_status'       => $bkg->order_status,
					'payment_status'     => $bkg->payment_status,
					'payment_method'     => $bkg->payment_method,
					'total_cost'         => $bkg->total_cost,
				);
			}
		}

		// Get slot capacity info.
		$slot_data     = $bmrequests->bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date( $service_id, $date );
		$max_capacity  = 0;
		$avail_capacity = 0;
		if ( ! empty( $slot_data ) ) {
			foreach ( $slot_data as $slot ) {
				$from = isset( $slot['from'] ) ? $slot['from'] : ( isset( $slot['slot_id'] ) ? $slot['slot_id'] : '' );
				if ( $from === $time_slot ) {
					$max_capacity   = isset( $slot['max_cap'] )  ? (int) $slot['max_cap']  : 0;
					$avail_capacity = isset( $slot['cap_left'] ) ? (int) $slot['cap_left'] : 0;
					break;
				}
			}
		}

		return rest_ensure_response(
			array(
				'service_id'          => $service_id,
				'service_name'        => $service->service_name,
				'service_price'       => $service->default_price,
				'service_duration'    => $service->service_duration,
				'date'                => $date,
				'time_slot'           => $time_slot,
				'max_capacity'        => $max_capacity,
				'available_capacity'  => $avail_capacity,
				'bookings'            => $response_bookings,
				'booking_count'       => count( $response_bookings ),
			)
		);
	}
}
