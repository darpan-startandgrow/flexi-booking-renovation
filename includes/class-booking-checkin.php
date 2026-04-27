<?php
/**
 * CHECKIN Module — helpers, hooks, and feature extensions.
 *
 * Change summary (v2.0.0 → v2.1.0):
 *   Security  — All DB access goes through BM_DBhandler; zero direct $wpdb calls.
 *   Hooks     — Fires bm_checkin_before_checkin / bm_checkin_after_checkin /
 *               bm_checkin_before_undo / bm_checkin_after_undo /
 *               bm_checkin_no_show / bm_checkin_page_loaded.
 *               Applies bm_checkin_can_checkin / bm_checkin_status_label /
 *               bm_checkin_columns / bm_checkin_row_actions /
 *               bm_checkin_checkin_data / bm_checkin_allowed_statuses /
 *               bm_checkin_time_window.
 *   Features  — No-show, undo, checkout, optional time-window enforcement,
 *               real-time status counter, admin notification email.
 *   DB        — Version-gated upgrade that adds `checked_in_by` and
 *               `notes` columns to the CHECKIN table (via BM_DBhandler).
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @since      2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ---------------------------------------------------------------------------
// Module constants
// ---------------------------------------------------------------------------

define( 'BM_CHECKIN_VERSION', '2.1.0' );

define( 'BM_CHECKIN_STATUS_PENDING',    'pending' );
define( 'BM_CHECKIN_STATUS_CHECKED_IN', 'checked_in' );
define( 'BM_CHECKIN_STATUS_EXPIRED',    'expired' );
define( 'BM_CHECKIN_STATUS_NO_SHOW',    'no_show' );
define( 'BM_CHECKIN_STATUS_LATE',       'late' );
define( 'BM_CHECKIN_STATUS_EARLY',      'early' );
define( 'BM_CHECKIN_STATUS_CHECKED_OUT','checked_out' );

/**
 * BM_Checkin
 *
 * Centralises all check-in business logic, extensibility hooks, and
 * optional feature additions introduced in version 2.0.0.
 *
 * All database access goes exclusively through BM_DBhandler.
 */
class BM_Checkin {

/**
 * Register WordPress hooks. Called once during plugin bootstrap.
 */
public static function init() {
// Core AJAX actions.
add_action( 'wp_ajax_bm_checkin_no_show',  array( __CLASS__, 'handle_no_show' ) );
add_action( 'wp_ajax_bm_checkin_undo',     array( __CLASS__, 'handle_undo_checkin' ) );
add_action( 'wp_ajax_bm_checkin_checkout', array( __CLASS__, 'handle_checkout' ) );
add_action( 'wp_ajax_bm_checkin_counter',  array( __CLASS__, 'handle_status_counter' ) );

// Version-gated DB upgrade.
add_action( 'admin_init', array( __CLASS__, 'maybe_run_upgrade' ) );

// Built-in: admin notification email on successful check-in.
add_action( 'bm_checkin_after_checkin', array( __CLASS__, 'send_checkin_notification' ), 10, 2 );

// Built-in: time-window enforcement (opt-in via bm_checkin_time_window_enabled option).
add_filter( 'bm_checkin_can_checkin', array( __CLASS__, 'enforce_time_window' ), 10, 3 );
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
BM_CHECKIN_STATUS_LATE,
BM_CHECKIN_STATUS_EARLY,
BM_CHECKIN_STATUS_CHECKED_OUT,
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
 * @param  string $status Status slug.
 * @return string
 */
public static function get_status_label( $status ) {
$labels = array(
BM_CHECKIN_STATUS_PENDING     => __( 'Pending',      'service-booking' ),
BM_CHECKIN_STATUS_CHECKED_IN  => __( 'Checked In',   'service-booking' ),
BM_CHECKIN_STATUS_EXPIRED     => __( 'Expired',      'service-booking' ),
BM_CHECKIN_STATUS_NO_SHOW     => __( 'No Show',      'service-booking' ),
BM_CHECKIN_STATUS_LATE        => __( 'Late',         'service-booking' ),
BM_CHECKIN_STATUS_EARLY       => __( 'Early',        'service-booking' ),
BM_CHECKIN_STATUS_CHECKED_OUT => __( 'Checked Out',  'service-booking' ),
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
 * Uses BM_DBhandler::update_if_state_matches() for an atomic, race-safe
 * update that prevents double-check-in under concurrent requests.
 *
 * Fires: bm_checkin_before_checkin, bm_checkin_after_checkin.
 * Applies: bm_checkin_can_checkin, bm_checkin_checkin_data.
 *
 * @param  int          $booking_id Booking primary key.
 * @param  BM_DBhandler $db         DB handler instance.
 * @param  int|null     $user_id    WordPress user performing the action.
 * @return bool True on success, false if check-in was blocked or duplicate.
 */
public static function do_checkin( int $booking_id, BM_DBhandler $db, ?int $user_id = null ) {
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

$now      = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();
$qr_token = $db->get_value( 'BOOKING', 'booking_key', $booking_id, 'id' );

$data = array(
'booking_id'    => $booking_id,
'qr_scanned'    => 1,
'status'        => BM_CHECKIN_STATUS_CHECKED_IN,
'qr_token'      => $qr_token,
'checkin_time'  => $now,
'updated_at'    => $now,
'checked_in_by' => $user_id,
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

$existing_id = (int) $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );

if ( $existing_id ) {
/*
 * Atomic guard: update_if_state_matches() issues
 *   UPDATE … WHERE id = $existing_id AND status = 'pending'
 * so concurrent requests that race past the get_value check
 * cannot both succeed — only the first UPDATE will match.
 */
$update_data = array(
'status'        => BM_CHECKIN_STATUS_CHECKED_IN,
'qr_scanned'    => 1,
'qr_token'      => $data['qr_token'],
'checkin_time'  => $now,
'updated_at'    => $now,
'checked_in_by' => $user_id,
);

$affected = $db->update_if_state_matches(
'CHECKIN',
'id',
$existing_id,
'status',
BM_CHECKIN_STATUS_PENDING,
$update_data
);

if ( 0 === $affected ) {
/*
 * The WHERE guard blocked the update. Determine why:
 * – already checked_in → bail (no duplicate check-in)
 * – other status (expired, etc.) → force the transition
 */
$current = $db->get_value( 'CHECKIN', 'status', $existing_id );
if ( BM_CHECKIN_STATUS_CHECKED_IN === $current ) {
return false;
}
// Force transition from any other status (e.g. expired → checked_in).
$db->update_row( 'CHECKIN', 'id', $existing_id, $update_data );
}
} else {
$db->insert_row( 'CHECKIN', array_merge( $data, array( 'created_at' => $now ) ) );
}

$checkin_record = $db->get_row( 'CHECKIN', $booking_id, 'booking_id' );

/**
 * Fires after a check-in has been recorded.
 *
 * @since 2.0.0
 * @param int    $booking_id     Booking ID.
 * @param object $checkin_record DB row of the CHECKIN record.
 */
do_action( 'bm_checkin_after_checkin', $booking_id, $checkin_record );

return true;
}

// -------------------------------------------------------------------------
// Admin notification email
// -------------------------------------------------------------------------

/**
 * Send a short admin notification email when a booking checks in.
 *
 * Hooked to bm_checkin_after_checkin. Can be disabled via the
 * bm_checkin_notify_enabled option (default: enabled).
 *
 * @since 2.1.0
 * @param int    $booking_id     Booking ID.
 * @param object $checkin_record CHECKIN table row (may be null on insert failure).
 */
public static function send_checkin_notification( $booking_id, $checkin_record ) {
if ( ! get_option( 'bm_checkin_notify_enabled', true ) ) {
return;
}

$db      = new BM_DBhandler();
$booking = $db->get_row( 'BOOKING', (int) $booking_id, 'id' );
if ( ! $booking ) {
return;
}

/* translators: %d = booking ID */
$subject = sprintf( __( '[Check-In] Booking #%d has checked in', 'service-booking' ), (int) $booking_id );

/* translators: 1 = booking reference, 2 = service name, 3 = booking date */
$message = sprintf(
__( 'Booking reference %1$s for service "%2$s" on %3$s has been checked in.', 'service-booking' ),
esc_html( $booking->booking_key ?? '' ),
esc_html( $booking->service_name ?? '' ),
esc_html( $booking->booking_date ?? '' )
);

$bm_mail = new BM_Email();
$bm_mail->bm_send_notification_to_shop_admin( $subject, $message, (int) $booking_id );
}

// -------------------------------------------------------------------------
// Time-window enforcement
// -------------------------------------------------------------------------

/**
 * Enforce the check-in time window.
 *
 * Hooked to bm_checkin_can_checkin with priority 10. Active only when the
 * bm_checkin_time_window_enabled option is set to true.
 *
 * @since 2.1.0
 * @param bool $can        Current gate value.
 * @param int  $booking_id Booking ID.
 * @param int  $user_id    WordPress user ID.
 * @return bool
 */
public static function enforce_time_window( bool $can, int $booking_id, int $user_id ): bool {
if ( ! $can ) {
return false; // Already blocked upstream.
}
return self::is_within_time_window( $booking_id );
}

/**
 * Check whether a booking is within the allowed check-in time window.
 *
 * The window size (in minutes) is controlled by the
 * bm_checkin_time_window filter (default: 60). The feature must be
 * enabled via the bm_checkin_time_window_enabled option.
 *
 * @param  int $booking_id Booking primary key.
 * @return bool True if within window (or feature disabled), false otherwise.
 */
public static function is_within_time_window( int $booking_id ) {
if ( ! get_option( 'bm_checkin_time_window_enabled', false ) ) {
return true; // Feature disabled — always allow.
}

/**
 * Filters the check-in time window in minutes.
 *
 * 0 or negative means disabled (always allow).
 *
 * @since 2.0.0
 * @param int $minutes    Window size in minutes before/after service start.
 * @param int $booking_id Booking ID.
 */
$window = (int) apply_filters( 'bm_checkin_time_window', 60, $booking_id );
if ( $window <= 0 ) {
return true;
}

$db  = new BM_DBhandler();
$row = $db->get_row( 'BOOKING', $booking_id, 'id' );
if ( ! $row ) {
return false;
}

$booking_slots = maybe_unserialize( $row->booking_slots ?? '' );
$from_time     = is_array( $booking_slots ) && isset( $booking_slots['from'] )
? $booking_slots['from']
: '00:00';

$service_start = strtotime( $row->booking_date . ' ' . $from_time );
$now           = time();

return abs( $now - $service_start ) <= ( $window * 60 );
}

// -------------------------------------------------------------------------
// No-show AJAX handler
// -------------------------------------------------------------------------

/**
 * Mark one or more bookings as no-show.
 *
 * AJAX action: bm_checkin_no_show
 *
 * POST params:
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

$raw_ids     = filter_input( INPUT_POST, 'booking_ids', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
$booking_ids = is_array( $raw_ids ) ? array_map( 'absint', $raw_ids ) : array();
if ( empty( $booking_ids ) ) {
wp_send_json_error( __( 'No booking IDs provided', 'service-booking' ) );
return;
}

$db    = new BM_DBhandler();
$now   = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();
$uid   = get_current_user_id();
$count = 0;

foreach ( $booking_ids as $bid ) {
/**
 * Fires when a booking is marked as no-show.
 *
 * @since 2.0.0
 * @param int $booking_id Booking ID.
 * @param int $user_id    WordPress user performing the action.
 */
do_action( 'bm_checkin_no_show', $bid, $uid );

$existing_id = (int) $db->get_value( 'CHECKIN', 'id', $bid, 'booking_id' );

if ( $existing_id ) {
$db->update_row(
'CHECKIN',
'id',
$existing_id,
array(
'status'     => BM_CHECKIN_STATUS_NO_SHOW,
'updated_at' => $now,
)
);
} else {
$token = $db->get_value( 'BOOKING', 'booking_key', $bid, 'id' );
$db->insert_row(
'CHECKIN',
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
array(
'message' => sprintf(
/* translators: %d number of bookings */
_n( '%d booking marked as no-show.', '%d bookings marked as no-show.', $count, 'service-booking' ),
$count
),
)
);
}

// -------------------------------------------------------------------------
// Undo check-in AJAX handler
// -------------------------------------------------------------------------

/**
 * Undo a check-in, reverting the booking back to "pending".
 *
 * AJAX action: bm_checkin_undo
 *
 * POST params:
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

$db  = new BM_DBhandler();
$now = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();

/**
 * Fires before a check-in is undone.
 *
 * @since 2.0.0
 * @param int $booking_id Booking ID.
 * @param int $checkin_id CHECKIN table row ID (0 if not provided).
 */
do_action( 'bm_checkin_before_undo', $booking_id, $checkin_id );

$update_data = array(
'status'       => BM_CHECKIN_STATUS_PENDING,
'qr_scanned'   => 0,
'checkin_time' => null,
'updated_at'   => $now,
);

if ( $checkin_id ) {
// Verify the checkin_id belongs to the given booking before updating.
$record = $db->get_row( 'CHECKIN', $checkin_id, 'id' );
if ( ! $record || (int) $record->booking_id !== $booking_id ) {
wp_send_json_error( __( 'Check-in record not found', 'service-booking' ) );
return;
}
$updated = $db->update_row( 'CHECKIN', 'id', $checkin_id, $update_data );
} else {
$updated = $db->update_row( 'CHECKIN', 'booking_id', $booking_id, $update_data );
}

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
// Checkout AJAX handler
// -------------------------------------------------------------------------

/**
 * Mark a checked-in booking as "checked out".
 *
 * AJAX action: bm_checkin_checkout
 *
 * POST params:
 *   nonce      — ajax-nonce
 *   booking_id — integer
 */
public static function handle_checkout() {
$nonce = filter_input( INPUT_POST, 'nonce' );
if ( ! $nonce || ! wp_verify_nonce( $nonce, 'ajax-nonce' ) ) {
wp_send_json_error( __( 'Failed security check', 'service-booking' ) );
return;
}
if ( ! current_user_can( 'manage_options' ) ) {
wp_send_json_error( __( 'Insufficient permissions', 'service-booking' ) );
return;
}

$booking_id = absint( filter_input( INPUT_POST, 'booking_id', FILTER_VALIDATE_INT ) );
if ( ! $booking_id ) {
wp_send_json_error( __( 'Invalid booking ID', 'service-booking' ) );
return;
}

$db  = new BM_DBhandler();
$now = ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp();

/*
 * Only transition from checked_in → checked_out.
 * Use update_if_state_matches for the atomic guard.
 */
$existing_id = (int) $db->get_value( 'CHECKIN', 'id', $booking_id, 'booking_id' );
if ( ! $existing_id ) {
wp_send_json_error( __( 'No check-in record found for this booking', 'service-booking' ) );
return;
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
wp_send_json_error( __( 'Booking is not in checked-in status', 'service-booking' ) );
return;
}

wp_send_json_success( array( 'message' => __( 'Booking successfully checked out.', 'service-booking' ) ) );
}

// -------------------------------------------------------------------------
// Real-time status counter
// -------------------------------------------------------------------------

/**
 * Return aggregated counts per status for the status counter bar.
 *
 * AJAX action: bm_checkin_counter
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
 * Query aggregated check-in counts per status via BM_DBhandler.
 *
 * Uses a single GROUP BY JOIN query, built through prepare_sql() and
 * get_results_raw() — the BM_DBhandler pattern for complex queries.
 * Table names are obtained from the activator (never from user input).
 *
 * @return array<string,int> Keys: total, pending, checked_in, expired, no_show, late, early, checked_out.
 */
public static function get_status_counts() {
$db        = new BM_DBhandler();
$activator = new Booking_Management_Activator();
$cht_table = $activator->get_db_table_name( 'CHECKIN' );
$bkt_table = $activator->get_db_table_name( 'BOOKING' );

// Table names come from our own activator — safe to interpolate.
// The literal 1 is the only user-supplied-style value and is
// prepared via %d to keep the helper happy.
$sql = $db->prepare_sql(
// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
"SELECT ch.status, COUNT(*) AS cnt
 FROM `{$cht_table}` ch
 INNER JOIN `{$bkt_table}` b ON b.id = ch.booking_id
 WHERE b.is_active = %d
 GROUP BY ch.status",
1
);

$rows = $db->get_results_raw( $sql );

$counts = array(
'total'       => 0,
'pending'     => 0,
'checked_in'  => 0,
'expired'     => 0,
'no_show'     => 0,
'late'        => 0,
'early'       => 0,
'checked_out' => 0,
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
// DB upgrade routine
// -------------------------------------------------------------------------

/**
 * Version-gated, idempotent upgrade routine.
 *
 * Adds `checked_in_by` (INT) and `notes` (TEXT) columns to the CHECKIN
 * table if they do not already exist. All schema checks go through
 * BM_DBhandler::column_exists(); DDL runs via BM_DBhandler::execute_ddl().
 */
public static function maybe_run_upgrade() {
if ( get_option( 'bm_checkin_db_version' ) === BM_CHECKIN_VERSION ) {
return;
}

$db        = new BM_DBhandler();
$activator = new Booking_Management_Activator();
$table     = $activator->get_db_table_name( 'CHECKIN' );

// Guard: table name must be a safe identifier before use in DDL.
if ( ! $table || ! preg_match( '/^[a-z0-9_]+$/i', $table ) ) {
return;
}

if ( ! $db->column_exists( 'CHECKIN', 'checked_in_by' ) ) {
$db->execute_ddl(
"ALTER TABLE `{$table}` ADD COLUMN `checked_in_by` int(11) DEFAULT NULL AFTER `service_expired`"
);
}

if ( ! $db->column_exists( 'CHECKIN', 'notes' ) ) {
$db->execute_ddl(
"ALTER TABLE `{$table}` ADD COLUMN `notes` text DEFAULT NULL AFTER `checked_in_by`"
);
}

update_option( 'bm_checkin_db_version', BM_CHECKIN_VERSION );
}
}
