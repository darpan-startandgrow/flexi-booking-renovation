<?php
/**
 * CHECKIN Module — helpers, hooks, and feature extensions.
 *
 * Change summary:
 *   Security  — Capability guard on all public entry points.
 *   Hooks     — Fires bm_checkin_before_checkin / bm_checkin_after_checkin /
 *               bm_checkin_before_undo / bm_checkin_after_undo /
 *               bm_checkin_no_show / bm_checkin_page_loaded.
 *               Applies bm_checkin_can_checkin / bm_checkin_status_label /
 *               bm_checkin_columns / bm_checkin_row_actions /
 *               bm_checkin_checkin_data / bm_checkin_allowed_statuses /
 *               bm_checkin_time_window.
 *   Features  — No-show status, undo check-in, optional time-window
 *               enforcement, real-time status counter data.
 *   DB        — Version-gated upgrade that adds `checked_in_by` and
 *               `notes` columns to the CHECKIN table.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CHECKIN module constants.
 */
define( 'BM_CHECKIN_VERSION', '2.0.0' );
define( 'BM_CHECKIN_STATUS_PENDING', 'pending' );
define( 'BM_CHECKIN_STATUS_CHECKED_IN', 'checked_in' );
define( 'BM_CHECKIN_STATUS_EXPIRED', 'expired' );
define( 'BM_CHECKIN_STATUS_NO_SHOW', 'no_show' );

/**
 * BM_Checkin
 *
 * Centralises all check-in business logic, extensibility hooks, and
 * optional feature additions introduced in version 2.0.0.
 */
class BM_Checkin {

	/**
	 * Register WordPress hooks.
	 *
	 * Called once during plugin bootstrap.
	 */
	public static function init() {
		add_action( 'wp_ajax_bm_checkin_no_show', array( __CLASS__, 'handle_no_show' ) );
		add_action( 'wp_ajax_bm_checkin_undo', array( __CLASS__, 'handle_undo_checkin' ) );
		add_action( 'wp_ajax_bm_checkin_counter', array( __CLASS__, 'handle_status_counter' ) );

		// Version-gated DB upgrade.
		add_action( 'admin_init', array( __CLASS__, 'maybe_run_upgrade' ) );
	}

	// -------------------------------------------------------------------------
	// Allowed statuses
	// -------------------------------------------------------------------------

	/**
	 * Return the full list of allowed check-in statuses, including any
	 * registered via the bm_checkin_allowed_statuses filter.
	 *
	 * @return string[]
	 */
	public static function get_allowed_statuses() {
		$statuses = array(
			BM_CHECKIN_STATUS_PENDING,
			BM_CHECKIN_STATUS_CHECKED_IN,
			BM_CHECKIN_STATUS_EXPIRED,
			BM_CHECKIN_STATUS_NO_SHOW,
		);

		/**
		 * Filters the list of valid check-in status strings.
		 *
		 * @since 2.0.0
		 * @param string[] $statuses Allowed status slugs.
		 */
		return (array) apply_filters( 'bm_checkin_allowed_statuses', $statuses );
	}

	/**
	 * Return a human-readable label for a check-in status.
	 *
	 * Applies the bm_checkin_status_label filter so third-party code
	 * can override or translate labels.
	 *
	 * @param  string $status Status slug.
	 * @return string
	 */
	public static function get_status_label( $status ) {
		$labels = array(
			BM_CHECKIN_STATUS_PENDING    => __( 'Pending', 'service-booking' ),
			BM_CHECKIN_STATUS_CHECKED_IN => __( 'Checked In', 'service-booking' ),
			BM_CHECKIN_STATUS_EXPIRED    => __( 'Expired', 'service-booking' ),
			BM_CHECKIN_STATUS_NO_SHOW    => __( 'No Show', 'service-booking' ),
		);

		$label = $labels[ $status ] ?? ucfirst( str_replace( '_', ' ', $status ) );

		/**
		 * Filters the human-readable label for a check-in status.
		 *
		 * @since 2.0.0
		 * @param string $label  Human-readable label.
		 * @param string $status Status slug.
		 */
		return apply_filters( 'bm_checkin_status_label', $label, $status );
	}

	// -------------------------------------------------------------------------
	// Core check-in action (shared helper used by all entry points)
	// -------------------------------------------------------------------------

	/**
	 * Perform a check-in for a single booking.
	 *
	 * Fires bm_checkin_can_checkin, bm_checkin_checkin_data,
	 * bm_checkin_before_checkin, and bm_checkin_after_checkin.
	 *
	 * An atomic WHERE-guarded UPDATE is used so that concurrent requests
	 * cannot double-check-in the same booking.
	 *
	 * @param  int           $booking_id Booking primary key.
	 * @param  BM_DBhandler  $db         DB handler instance.
	 * @param  int|null      $user_id    WordPress user performing the action.
	 * @return bool True on success.
	 */
	public static function do_checkin( int $booking_id, BM_DBhandler $db, ?int $user_id = null ) {
		global $wpdb;

		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		}

		/**
		 * Filters whether a check-in should proceed.
		 *
		 * Return false to prevent the check-in.
		 *
		 * @since 2.0.0
		 * @param bool $can_checkin Whether the check-in is allowed.
		 * @param int  $booking_id  Booking ID.
		 * @param int  $user_id     WordPress user ID performing the action.
		 */
		if ( ! apply_filters( 'bm_checkin_can_checkin', true, $booking_id, $user_id ) ) {
			return false;
		}

		$now        = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();
		$qr_token   = $db->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );
		// esc_sql() ensures the table name is safe even if the DB prefix contains unusual chars.
		$table      = esc_sql( $wpdb->prefix . 'checkin' );

		$data = array(
			'booking_id'      => $booking_id,
			'qr_scanned'      => 1,
			'status'          => BM_CHECKIN_STATUS_CHECKED_IN,
			'qr_token'        => $qr_token,
			'checkin_time'    => $now,
			'updated_at'      => $now,
			'checked_in_by'   => $user_id,
		);

		/**
		 * Filters the data array written to the CHECKIN table.
		 *
		 * @since 2.0.0
		 * @param array $data       Row data.
		 * @param int   $booking_id Booking ID.
		 */
		$data = (array) apply_filters( 'bm_checkin_checkin_data', $data, $booking_id );

		/**
		 * Fires immediately before a check-in is recorded.
		 *
		 * @since 2.0.0
		 * @param int $booking_id Booking ID.
		 * @param int $user_id    WordPress user performing the action.
		 */
		do_action( 'bm_checkin_before_checkin', $booking_id, $user_id );

		$existing_id = $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );

		if ( $existing_id ) {
			// Atomic guard: only update rows that are NOT already checked_in.
			$affected = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$table,
				array(
					'status'        => BM_CHECKIN_STATUS_CHECKED_IN,
					'qr_scanned'    => 1,
					'qr_token'      => $data['qr_token'],
					'checkin_time'  => $now,
					'updated_at'    => $now,
					'checked_in_by' => $user_id,
				),
				array(
					'id'     => (int) $existing_id,
					'status' => BM_CHECKIN_STATUS_PENDING,     // Guard: skip if already checked_in.
				),
				array( '%s', '%d', '%s', '%s', '%s', '%d' ),
				array( '%d', '%s' )
			);

			// If status guard blocked us, check the current status.
			if ( 0 === $affected ) {
				$current = $wpdb->get_var( $wpdb->prepare( "SELECT status FROM {$table} WHERE id = %d", (int) $existing_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				if ( BM_CHECKIN_STATUS_CHECKED_IN === $current ) {
					return false; // Already checked in.
				}
				// Otherwise force-update (e.g. expired → checked_in).
				$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$table,
					array(
						'status'        => BM_CHECKIN_STATUS_CHECKED_IN,
						'qr_scanned'    => 1,
						'qr_token'      => $data['qr_token'],
						'checkin_time'  => $now,
						'updated_at'    => $now,
						'checked_in_by' => $user_id,
					),
					array( 'id' => (int) $existing_id ),
					array( '%s', '%d', '%s', '%s', '%s', '%d' ),
					array( '%d' )
				);
			}
		} else {
			$insert_data = array_merge(
				$data,
				array( 'created_at' => $now )
			);
			$wpdb->insert( $table, $insert_data ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		$checkin_record = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table} WHERE booking_id = %d", $booking_id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

		/**
		 * Fires after a check-in has been recorded.
		 *
		 * @since 2.0.0
		 * @param int       $booking_id     Booking ID.
		 * @param object    $checkin_record DB row of the CHECKIN record.
		 */
		do_action( 'bm_checkin_after_checkin', $booking_id, $checkin_record );

		return true;
	}

	// -------------------------------------------------------------------------
	// No-show AJAX handler
	// -------------------------------------------------------------------------

	/**
	 * Mark one or more bookings as no-show.
	 *
	 * AJAX action: bm_checkin_no_show
	 *
	 * Expected POST params:
	 *   nonce       — ajax-nonce
	 *   booking_ids — array of integer booking IDs
	 */
	public static function handle_no_show() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'service-booking' ) );
			return;
		}

		if ( ! get_option( 'bm_checkin_no_show_enabled', true ) ) {
			wp_send_json_error( __( 'No-show feature is disabled', 'service-booking' ) );
			return;
		}

		$raw_ids    = filter_input( INPUT_POST, 'booking_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
		$booking_ids = is_array( $raw_ids ) ? array_map( 'absint', $raw_ids ) : array();

		if ( empty( $booking_ids ) ) {
			wp_send_json_error( __( 'No booking IDs provided', 'service-booking' ) );
			return;
		}

		global $wpdb;
		$table = esc_sql( $wpdb->prefix . 'checkin' );
		$now   = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();
		$count = 0;
		$uid   = get_current_user_id();

		foreach ( $booking_ids as $bid ) {
			/**
			 * Fires when a booking is marked as no-show.
			 *
			 * @since 2.0.0
			 * @param int $booking_id Booking ID.
			 * @param int $user_id    WordPress user performing the action.
			 */
			do_action( 'bm_checkin_no_show', $bid, $uid );

			$existing_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$table} WHERE booking_id = %d", $bid ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

			if ( $existing_id ) {
				$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$table,
					array( 'status' => BM_CHECKIN_STATUS_NO_SHOW, 'updated_at' => $now ),
					array( 'id' => $existing_id ),
					array( '%s', '%s' ),
					array( '%d' )
				);
			} else {
				$db    = new BM_DBhandler();
				$token = $db->get_value( 'BOOKING', 'booking_key', $bid, 'id' );
				$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					$table,
					array(
						'booking_id'    => $bid,
						'status'        => BM_CHECKIN_STATUS_NO_SHOW,
						'qr_token'      => $token,
						'qr_scanned'    => 0,
						'checked_in_by' => $uid,
						'created_at'    => $now,
						'updated_at'    => $now,
					)
				);
			}
			++$count;
		}

		wp_send_json_success(
			array( 'message' => sprintf(
				/* translators: %d number of bookings */
				_n( '%d booking marked as no-show.', '%d bookings marked as no-show.', $count, 'service-booking' ),
				$count
			) )
		);
	}

	// -------------------------------------------------------------------------
	// Undo check-in AJAX handler
	// -------------------------------------------------------------------------

	/**
	 * Undo a check-in, reverting the booking to "pending".
	 *
	 * AJAX action: bm_checkin_undo
	 *
	 * Expected POST params:
	 *   nonce      — ajax-nonce
	 *   booking_id — integer
	 *   checkin_id — integer (optional, for precision)
	 */
	public static function handle_undo_checkin() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'service-booking' ) );
			return;
		}

		if ( ! get_option( 'bm_checkin_undo_enabled', true ) ) {
			wp_send_json_error( __( 'Undo check-in feature is disabled', 'service-booking' ) );
			return;
		}

		$booking_id = absint( filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT ) );
		$checkin_id = absint( filter_input( INPUT_POST, 'checkin_id', FILTER_VALIDATE_INT ) );

		if ( ! $booking_id ) {
			wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
			return;
		}

		global $wpdb;
		$table = esc_sql( $wpdb->prefix . 'checkin' );
		$now   = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();

		/**
		 * Fires before a check-in is undone.
		 *
		 * @since 2.0.0
		 * @param int $booking_id Booking ID.
		 * @param int $checkin_id CHECKIN table row ID (0 if not provided).
		 */
		do_action( 'bm_checkin_before_undo', $booking_id, $checkin_id );

		$where = $checkin_id
			? array( 'id' => $checkin_id, 'booking_id' => $booking_id )
			: array( 'booking_id' => $booking_id );

		$where_format = $checkin_id ? array( '%d', '%d' ) : array( '%d' );

		$updated = $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table,
			array(
				'status'       => BM_CHECKIN_STATUS_PENDING,
				'qr_scanned'   => 0,
				'checkin_time' => null,
				'updated_at'   => $now,
			),
			$where,
			array( '%s', '%d', null, '%s' ),
			$where_format
		);

		if ( false === $updated ) {
			wp_send_json_error( __( 'Failed to undo check-in', 'service-booking' ) );
			return;
		}

		/**
		 * Fires after a check-in has been undone.
		 *
		 * @since 2.0.0
		 * @param int $booking_id Booking ID.
		 */
		do_action( 'bm_checkin_after_undo', $booking_id );

		wp_send_json_success( array( 'message' => __( 'Check-in successfully undone.', 'service-booking' ) ) );
	}

	// -------------------------------------------------------------------------
	// Real-time status counter
	// -------------------------------------------------------------------------

	/**
	 * Return aggregated counts per status for the status counter bar.
	 *
	 * AJAX action: bm_checkin_counter
	 *
	 * No POST params required.
	 */
	public static function handle_status_counter() {
		$nonce = filter_input( INPUT_POST, 'nonce' );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
			wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
			return;
		}
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'Insufficient permissions', 'service-booking' ) );
			return;
		}

		wp_send_json_success( self::get_status_counts() );
	}

	/**
	 * Query aggregated check-in counts per status.
	 *
	 * @return array<string,int> Keys: total, pending, checked_in, expired, no_show.
	 */
	public static function get_status_counts() {
		global $wpdb;

		$table   = $wpdb->prefix . 'checkin';
		$booking = esc_sql( $wpdb->prefix . 'booking' );

		$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			"SELECT ch.status, COUNT(*) AS cnt
			 FROM {$table} ch
			 INNER JOIN {$booking} b ON b.id = ch.booking_id
			 WHERE b.is_active = 1
			 GROUP BY ch.status"
		);

		$counts = array(
			'total'      => 0,
			'pending'    => 0,
			'checked_in' => 0,
			'expired'    => 0,
			'no_show'    => 0,
		);

		if ( is_array( $rows ) ) {
			foreach ( $rows as $row ) {
				$key = sanitize_key( $row->status );
				if ( isset( $counts[ $key ] ) ) {
					$counts[ $key ] = (int) $row->cnt;
				}
				$counts['total'] += (int) $row->cnt;
			}
		}

		return $counts;
	}

	// -------------------------------------------------------------------------
	// Time-window enforcement helper
	// -------------------------------------------------------------------------

	/**
	 * Check whether a booking is within the allowed check-in time window.
	 *
	 * The window size (in minutes) is controlled by the
	 * bm_checkin_time_window filter (default: 0 = disabled).
	 *
	 * @param  int $booking_id Booking primary key.
	 * @return bool True if within window (or window disabled), false otherwise.
	 */
	public static function is_within_time_window( int $booking_id ) {
		if ( ! get_option( 'bm_checkin_time_window_enabled', false ) ) {
			return true;
		}

		/**
		 * Filters the check-in time window in minutes.
		 *
		 * 0 means disabled (always allow).
		 *
		 * @since 2.0.0
		 * @param int $minutes    Window size in minutes before/after service start.
		 * @param int $booking_id Booking ID.
		 */
		$window = (int) apply_filters( 'bm_checkin_time_window', 60, $booking_id );
		if ( $window <= 0 ) {
			return true;
		}

		$db   = new BM_DBhandler();
		$row  = $db->get_row( 'BOOKING', $booking_id, 'id' );
		if ( ! $row ) {
			return false;
		}

		$booking_slots = maybe_unserialize( $row->booking_slots ?? '' );
		$from_time     = is_array( $booking_slots ) && isset( $booking_slots['from'] ) ? $booking_slots['from'] : '00:00';
		$service_start = strtotime( $row->booking_date . ' ' . $from_time );
		$now           = time();

		return abs( $now - $service_start ) <= ( $window * 60 );
	}

	// -------------------------------------------------------------------------
	// DB upgrade routine
	// -------------------------------------------------------------------------

	/**
	 * Version-gated, idempotent upgrade routine.
	 *
	 * Adds `checked_in_by` (INT) and `notes` (TEXT) columns to the CHECKIN
	 * table if they don't already exist. Runs once per site, guarded by the
	 * bm_checkin_db_version option.
	 */
	public static function maybe_run_upgrade() {
		if ( get_option( 'bm_checkin_db_version' ) === BM_CHECKIN_VERSION ) {
			return;
		}

		global $wpdb;
		$table = esc_sql( $wpdb->prefix . 'checkin' );

		// Guard: table name must match a safe pattern before use in DDL.
		if ( ! preg_match( '/^[a-z0-9_]+$/i', $table ) ) {
			return;
		}

		// Use $wpdb->dbname for the current DB name rather than the DB_NAME constant
		// so we work correctly in all WordPress configurations.
		$db_name = $wpdb->dbname;

		// Add checked_in_by column.
		$col_exists = $wpdb->get_results( $wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s',
			$db_name, $table, 'checked_in_by'
		) );

		if ( empty( $col_exists ) ) {
			$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN `checked_in_by` int(11) DEFAULT NULL AFTER `service_expired`" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		// Add notes column.
		$notes_exists = $wpdb->get_results( $wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = %s',
			$db_name, $table, 'notes'
		) );

		if ( empty( $notes_exists ) ) {
			$wpdb->query( "ALTER TABLE `{$table}` ADD COLUMN `notes` text DEFAULT NULL AFTER `checked_in_by`" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		}

		update_option( 'bm_checkin_db_version', BM_CHECKIN_VERSION );
	}
}
