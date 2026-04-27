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
						'sanitize_callback' => 'sanitize_text_field',
					),
					'category_id' => array(
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
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

	/**
	 * Resolve a service image GUID to a full URL.
	 *
	 * @param int|string $image_guid Attachment ID stored in service_image_guid.
	 * @return string Full URL or empty string.
	 */
	private function resolve_service_image( $image_guid ) {
		if ( empty( $image_guid ) ) {
			return '';
		}
		$url = wp_get_attachment_url( (int) $image_guid );
		return $url ? $url : '';
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

		// isset() + strict '' check handles category_id=0 (Uncategorised) correctly.
		// PHP's empty('0') === true, so !empty() would silently skip the filter for id 0.
		$filter_cat = isset( $params['category_id'] ) && $params['category_id'] !== '';
		$cat_id     = $filter_cat ? absint( $params['category_id'] ) : null;

		if ( $filter_cat && 0 === $cat_id ) {
			// Uncategorised: service_category = 0 or NULL. Requires an OR clause.
			$service_table = $dbhandler->get_table_name( 'SERVICE' );
			$services      = $dbhandler->get_results_raw( "SELECT * FROM {$service_table} WHERE service_status = 1 AND (service_category = 0 OR service_category IS NULL) ORDER BY service_position ASC" );
		} else {
			$where = array( 'service_status' => 1 );
			if ( $filter_cat ) {
				$where['service_category'] = $cat_id;
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
		}

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
				} else {
					$category_name = 'Uncategorised';
				}
				$response[] = array(
					'id'               => (int) $service->id,
					'service_name'     => $service->service_name,
					'service_duration' => $service->service_duration,
					'default_price'    => $service->default_price,
					'service_category' => (int) $service->service_category,
					'category_name'    => $category_name,
					'service_image'    => $this->resolve_service_image( $service->service_image_guid ),
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

		// Add virtual "Uncategorised" entry for services with category 0.
		$response[] = array(
			'id'           => 0,
			'cat_name'     => 'Uncategorised',
			'cat_position' => 0,
			'cat_options'  => null,
		);

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
			$booking_slots = array( 'from' => $time_slot_from, 'to' => $time_slot_to );

			// Lock and check slot capacity inside the transaction to prevent overbooking.
			$slot_table = $dbhandler->get_table_name( 'SLOTCOUNT' );
			$cap_row    = $dbhandler->get_row_raw(
				$dbhandler->prepare_sql(
					"SELECT slot_cap_left FROM {$slot_table} WHERE service_id = %d AND booking_date = %s AND slot_id = %s LIMIT 1 FOR UPDATE",
					$service_id,
					$booking_date,
					$time_slot_from
				)
			);
			if ( ! $cap_row || (int) $cap_row->slot_cap_left < 1 ) {
				throw new Exception( __( 'No capacity available for this slot.', 'service-booking' ) );
			}

			// Find or create customer.
			$customer_id = 0;
			if ( $customer_email ) {
				$cust_table = $dbhandler->get_table_name( 'CUSTOMERS' );
				$existing   = $dbhandler->get_row_raw(
					$dbhandler->prepare_sql( "SELECT id FROM {$cust_table} WHERE customer_email = %s LIMIT 1", $customer_email )
				);
				if ( $existing ) {
					$customer_id = (int) $existing->id;
				} else {
					$customer_id = (int) $dbhandler->insert_row(
						'CUSTOMERS',
						array(
							'customer_name'  => $customer_name,
							'customer_email' => $customer_email,
						),
						array( '%s', '%s' )
					);
				}
			}

			// Insert booking record.
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

			$booking_id = (int) $dbhandler->insert_row( 'BOOKING', $booking_data );

			if ( ! $booking_id ) {
				throw new Exception( __( 'Failed to insert booking record.', 'service-booking' ) );
			}

			// Update slot capacity (decrement).
			$dbhandler->execute_query(
				$dbhandler->prepare_sql(
					"UPDATE {$slot_table} SET slot_cap_left = GREATEST(0, slot_cap_left - 1) WHERE service_id = %d AND booking_date = %s AND slot_id = %s",
					$service_id,
					$booking_date,
					$time_slot_from
				)
			);

			$dbhandler->commit_transaction();

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
			$result = $dbhandler->update_where(
				'BOOKING',
				$update_data,
				array( 'id' => $booking_id ),
				$update_format,
				array( '%d' )
			);

			if ( false === $result ) {
				throw new Exception( __( 'Failed to update booking record.', 'service-booking' ) );
			}

			$dbhandler->commit_transaction();

			$updated = $dbhandler->get_row( 'BOOKING', $booking_id );
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
			$result = $dbhandler->update_where(
				'BOOKING',
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
				$slot_table = $dbhandler->get_table_name( 'SLOTCOUNT' );
				$dbhandler->execute_query(
					$dbhandler->prepare_sql(
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
	// Planner slot helpers
	// -------------------------------------------------------------------------

	/**
	 * Build slot data for a service on a specific date for admin planner display.
	 *
	 * Returns ALL defined (non-disabled) time slots for the date regardless of
	 * remaining capacity or whether the slot time has already passed.
	 * This gives the admin a complete picture of every configured slot.
	 *
	 * @param int    $svc_id       Service ID.
	 * @param string $date         Date in Y-m-d format.
	 * @param int    $svc_duration Service duration in minutes (used to derive 'to' when missing).
	 * @return array Array of slot objects with keys: slot_id, from, to, time_display,
	 *               max_capacity, available_capacity, booking_count (always 0 here;
	 *               caller must inject booking_count from its own lookup).
	 */
	private function build_planner_slots_for_service_date( $svc_id, $date, $svc_duration ) {
		$dbhandler = new BM_DBhandler();
		$time_row  = $dbhandler->get_row( 'TIME', $svc_id );

		if ( empty( $time_row ) ) {
			return array();
		}

		// Check for variable time slots on this specific date.
		$service             = $dbhandler->get_row( 'SERVICE', $svc_id );
		$variable_time_slots = array();
		if ( ! empty( $service ) && ! empty( $service->variable_time_slots ) ) {
			$variable_time_slots = maybe_unserialize( $service->variable_time_slots );
		}

		$v_dates      = ( is_array( $variable_time_slots ) && ! empty( $variable_time_slots ) )
			? wp_list_pluck( $variable_time_slots, 'date' )
			: array();
		$use_variable = ( ! empty( $v_dates ) && in_array( $date, $v_dates, true ) );

		if ( $use_variable ) {
			$v_index     = array_search( $date, $v_dates );
			$slot_data   = $variable_time_slots[ $v_index ];
			$total_slots = isset( $slot_data['total_slots'] ) ? (int) $slot_data['total_slots'] : 0;
		} else {
			$time_slots  = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
			$total_slots = isset( $time_row->total_slots ) ? (int) $time_row->total_slots : 0;
		}

		$result = array();

		for ( $i = 1; $i <= $total_slots; $i++ ) {
			if ( $use_variable ) {
				if ( isset( $slot_data['disable'][ $i ] ) && 1 === (int) $slot_data['disable'][ $i ] ) {
					continue;
				}
				$from            = isset( $slot_data['from'][ $i ] )    ? $slot_data['from'][ $i ]    : '';
				$to              = isset( $slot_data['to'][ $i ] )      ? $slot_data['to'][ $i ]      : '';
				$max_cap         = isset( $slot_data['max_cap'][ $i ] ) ? (int) $slot_data['max_cap'][ $i ] : 0;
				$is_variable_int = 1;
			} else {
				if ( ! is_array( $time_slots ) || ( isset( $time_slots['disable'][ $i ] ) && 1 === (int) $time_slots['disable'][ $i ] ) ) {
					continue;
				}
				$from            = isset( $time_slots['from'][ $i ] )    ? $time_slots['from'][ $i ]    : '';
				$to              = isset( $time_slots['to'][ $i ] )      ? $time_slots['to'][ $i ]      : '';
				$max_cap         = isset( $time_slots['max_cap'][ $i ] ) ? (int) $time_slots['max_cap'][ $i ] : 0;
				$is_variable_int = 0;
			}

			if ( empty( $from ) ) {
				continue;
			}

			// Derive end time from service duration when not explicitly set.
			if ( empty( $to ) && $svc_duration > 0 ) {
				$from_dt = DateTime::createFromFormat( 'H:i', $from );
				if ( $from_dt ) {
					$from_dt->modify( '+' . (int) $svc_duration . ' minutes' );
					$to = $from_dt->format( 'H:i' );
				}
			}

			// Get live capacity from SLOTCOUNT table.
			$cap = $this->get_slot_capacity_from_db( $svc_id, $i, $date, $is_variable_int, $max_cap );

			$time_display = $from . ( ! empty( $to ) ? ' - ' . $to : '' );

			$result[] = array(
				'slot_id'            => $from,
				'from'               => $from,
				'to'                 => $to,
				'time_display'       => $time_display,
				'max_capacity'       => $cap['max_capacity'],
				'available_capacity' => $cap['available_capacity'],
				'booking_count'      => 0, // Caller injects the real count.
			);
		}

		return $result;
	}

	/**
	 * Query SLOTCOUNT for live max/available capacity for a single slot.
	 *
	 * When no SLOTCOUNT row exists (no booking has been made for this slot on this
	 * date yet), the slot is considered fully available and max capacity equals the
	 * value configured in the TIME / variable_time_slots data.
	 *
	 * @param int    $service_id  Service ID.
	 * @param int    $slot_index  Slot index (1-based integer).
	 * @param string $date        Date in Y-m-d format.
	 * @param int    $is_variable 1 for variable slots, 0 for non-variable.
	 * @param int    $default_max Default max capacity from TIME / variable_time_slots.
	 * @return array { max_capacity: int, available_capacity: int }
	 */
	private function get_slot_capacity_from_db( $service_id, $slot_index, $date, $is_variable, $default_max ) {
		$dbhandler  = new BM_DBhandler();
		$slot_table = $dbhandler->get_table_name( 'SLOTCOUNT' );

		$row = $dbhandler->get_row_raw(
			$dbhandler->prepare_sql(
				"SELECT slot_cap_left, slot_max_cap FROM {$slot_table}
				 WHERE service_id = %d AND booking_date = %s AND slot_id = %d AND is_variable = %d AND is_active = 1
				 ORDER BY id DESC LIMIT 1",
				$service_id,
				$date,
				$slot_index,
				(int) $is_variable
			)
		);

		if ( $row ) {
			return array(
				'max_capacity'       => (int) $row->slot_max_cap,
				'available_capacity' => (int) $row->slot_cap_left,
			);
		}

		// No SLOTCOUNT row → slot is fully available (no bookings made yet).
		return array(
			'max_capacity'       => $default_max,
			'available_capacity' => $default_max,
		);
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

		$dbhandler = new BM_DBhandler();

		// Build service filter – supports comma-separated multi-values.
		// Note: isset() + strict '' check is used instead of !empty() so that
		// category_id=0 (Uncategorised) is not silently skipped (PHP empty('0') === true).
		$svc_where         = array( 'service_status' => 1 );
		$custom_in_clauses = array();
		$cat_where_sql     = '';   // Raw OR clause used when Uncategorised (0) is selected.
		$cat_where_vals    = array();

		if ( isset( $params['category_id'] ) && $params['category_id'] !== '' ) {
			// Filter empty strings from split but KEEP numeric zeros (Uncategorised).
			$category_strings = array_filter( explode( ',', $params['category_id'] ), function( $v ) { return $v !== ''; } );
			$cat_ids          = array_values( array_unique( array_map( 'absint', $category_strings ) ) );

			$has_uncategorised = in_array( 0, $cat_ids, true );
			$positive_cat_ids  = array_values( array_filter( $cat_ids, function( $v ) { return $v > 0; } ) );

			if ( $has_uncategorised ) {
				// Build an OR clause that covers category 0 / NULL plus any named categories.
				$cat_clauses = array( '(service_category = 0 OR service_category IS NULL)' );
				if ( count( $positive_cat_ids ) === 1 ) {
					$cat_clauses[]  = 'service_category = %d';
					$cat_where_vals[] = $positive_cat_ids[0];
				} elseif ( count( $positive_cat_ids ) > 1 ) {
					$placeholders   = implode( ',', array_fill( 0, count( $positive_cat_ids ), '%d' ) );
					$cat_clauses[]  = "service_category IN ($placeholders)";
					$cat_where_vals = array_merge( $cat_where_vals, $positive_cat_ids );
				}
				$cat_where_sql = '(' . implode( ' OR ', $cat_clauses ) . ')';
			} elseif ( count( $cat_ids ) === 1 ) {
				$svc_where['service_category'] = $cat_ids[0];
			} elseif ( count( $cat_ids ) > 1 ) {
				$custom_in_clauses['service_category'] = $cat_ids;
			}
		}
		if ( ! empty( $params['service_id'] ) ) {
			$svc_ids_filter = array_values( array_filter( array_map( 'absint', explode( ',', $params['service_id'] ) ) ) );
			if ( count( $svc_ids_filter ) === 1 ) {
				$svc_where['id'] = $svc_ids_filter[0];
			} elseif ( count( $svc_ids_filter ) > 1 ) {
				$custom_in_clauses['id'] = $svc_ids_filter;
			}
		}

		$needs_custom_query = ! empty( $custom_in_clauses ) || $cat_where_sql !== '';
		if ( $needs_custom_query ) {
			// Use custom query for multi-value IN filters and/or Uncategorised OR clause.
			$service_table = $dbhandler->get_table_name( 'SERVICE' );
			$where_parts   = array( 'service_status = 1' );
			$values        = array();
			$allowed_cols  = array( 'service_category', 'id' );

			// Uncategorised OR clause must be added as a single AND group.
			if ( $cat_where_sql !== '' ) {
				$where_parts[] = $cat_where_sql;
				$values        = array_merge( $values, $cat_where_vals );
			}

			foreach ( $custom_in_clauses as $col => $ids ) {
				if ( ! in_array( $col, $allowed_cols, true ) ) {
					continue;
				}
				$placeholders  = implode( ',', array_fill( 0, count( $ids ), '%d' ) );
				$where_parts[] = "`$col` IN ($placeholders)";
				$values        = array_merge( $values, $ids );
			}
			// Also include single-value where clauses.
			if ( isset( $svc_where['service_category'] ) ) {
				$where_parts[] = 'service_category = %d';
				$values[]      = $svc_where['service_category'];
			}
			if ( isset( $svc_where['id'] ) ) {
				$where_parts[] = 'id = %d';
				$values[]      = $svc_where['id'];
			}
			$where_sql    = implode( ' AND ', $where_parts );
			$prepared_sql = "SELECT * FROM {$service_table} WHERE {$where_sql} ORDER BY service_position ASC";
			if ( ! empty( $values ) ) {
				$raw_services = $dbhandler->get_results_raw( $dbhandler->prepare_sql( $prepared_sql, ...$values ) );
			} else {
				$raw_services = $dbhandler->get_results_raw( $prepared_sql );
			}
		} else {
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
		}

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
			} else {
				$cat_name = 'Uncategorised';
			}

			$services[] = array(
				'id'               => (int) $svc->id,
				'service_name'     => $svc->service_name,
				'service_duration' => $svc->service_duration,
				'default_price'    => $svc->default_price,
				'service_category' => $cat_id,
				'category_name'    => $cat_name,
				'service_image'    => $this->resolve_service_image( $svc->service_image_guid ),
				'service_position' => (int) $svc->service_position,
				'service_type'     => $this->extract_service_type( $svc ),
				'total_svc_slots'  => isset( $svc->default_max_cap ) ? (int) $svc->default_max_cap : 1,
			);
		}

		// Pre-fetch all bookings in the range for all services in one query.
		$bkg_table  = $dbhandler->get_table_name( 'BOOKING' );
		$ids_ph     = implode( ',', array_fill( 0, count( $service_ids ), '%d' ) );
		$query_args = array_merge( $service_ids, array( $start_date, $end_date ) );

		$raw_bookings = $dbhandler->get_results_raw(
			$dbhandler->prepare_sql(
				"SELECT service_id, booking_date, booking_slots FROM {$bkg_table}
				 WHERE is_active = 1
				   AND service_id IN ({$ids_ph})
				   AND booking_date BETWEEN %s AND %s",
				...$query_args
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
			$svc_id               = $svc['id'];
			$slots_map[ $svc_id ] = array();
			$svc_duration         = isset( $svc['service_duration'] ) ? (int) ( floatval( $svc['service_duration'] ) * 60 ) : 60;

			foreach ( $dates as $date ) {
				$day_slots = $this->build_planner_slots_for_service_date( $svc_id, $date, $svc_duration );

				// Inject booking counts from the pre-fetched lookup.
				foreach ( $day_slots as &$slot ) {
					$bkg_key              = $svc_id . '_' . $date . '_' . $slot['from'];
					$slot['booking_count'] = isset( $bkg_count[ $bkg_key ] ) ? $bkg_count[ $bkg_key ] : 0;
				}
				unset( $slot );

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

		$dbhandler = new BM_DBhandler();

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

		// Get slot capacity info using the same reliable helper used by get_planner_week().
		$max_capacity   = 0;
		$avail_capacity = 0;
		$svc_duration   = isset( $service->service_duration ) ? (int) ( floatval( $service->service_duration ) * 60 ) : 60;
		$day_slots      = $this->build_planner_slots_for_service_date( $service_id, $date, $svc_duration );
		foreach ( $day_slots as $sl ) {
			if ( $sl['from'] === $time_slot ) {
				$max_capacity   = $sl['max_capacity'];
				$avail_capacity = $sl['available_capacity'];
				break;
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
