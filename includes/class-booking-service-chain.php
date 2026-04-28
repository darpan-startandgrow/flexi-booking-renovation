<?php
/**
 * Service Chaining — Spec §1.5
 *
 * Manages mutual exclusion rules between services that share the same exclusive
 * resource (e.g. shared-boat vs private-boat on the same slot/date).
 *
 * Chain types:
 *   'exclusive'     — booking service A closes service B (bidirectional).
 *   'unidirectional'— booking service A closes service B only.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class BM_ServiceChain {

/** @var BM_DBhandler */
private $db;

public function __construct() {
$this->db = new BM_DBhandler();
}

// ───────────────────────────── CHAIN CRUD ────────────────────────────────

/**
 * Create a chain rule between two services.
 *
 * @param int    $service_a_id
 * @param int    $service_b_id
 * @param string $chain_type  Always 'mutual_exclusion'. Any other value defaults to 'mutual_exclusion'.
 * @return int|false
 */
public function create_chain( int $service_a_id, int $service_b_id ) {
// P4 — server-side self-chain validation.
if ( $service_a_id === $service_b_id ) {
return false;
}
// P2 — only 'mutual_exclusion' is supported per spec §1.5.
$chain_type = 'mutual_exclusion';
$table      = $this->db->get_table_name( 'SERVICE_CHAIN' );

// Check for existing chain to avoid resetting created_at.
$existing = $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT id FROM {$table} WHERE service_a_id = %d AND service_b_id = %d",
$service_a_id,
$service_b_id
)
);

if ( $existing ) {
$this->db->update_where(
'SERVICE_CHAIN',
[ 'chain_type' => $chain_type, 'status' => 1 ],
[ 'id' => (int) $existing ],
[ '%s', '%d' ],
[ '%d' ]
);
return (int) $existing;
}

return $this->db->insert_row(
'SERVICE_CHAIN',
[
'service_a_id' => $service_a_id,
'service_b_id' => $service_b_id,
'chain_type'   => $chain_type,
'status'       => 1,
],
[ '%d', '%d', '%s', '%d' ]
);
}

/**
 * Update chain status or type.
 *
 * @param int   $chain_id
 * @param array $data  Keys: service_a_id, service_b_id (correcting wrong service references only)
 * @return bool
 */
public function update_chain( int $chain_id, array $data ): bool {
// P19 — chain editing is limited to correcting the two service references.
$allowed = [ 'service_a_id', 'service_b_id' ];
$set     = [];
$formats = [];
foreach ( $allowed as $key ) {
if ( array_key_exists( $key, $data ) ) {
$set[ $key ] = (int) $data[ $key ];
$formats[]   = '%d';
}
}
// Prevent self-chain after update.
$a = isset( $set['service_a_id'] ) ? $set['service_a_id'] : null;
$b = isset( $set['service_b_id'] ) ? $set['service_b_id'] : null;
if ( null !== $a && null !== $b && $a === $b ) {
return false;
}
if ( empty( $set ) ) {
return false;
}
return (bool) $this->db->update_row( 'SERVICE_CHAIN', 'id', $chain_id, $set, $formats, [ '%d' ] );
}

/**
 * Delete a chain rule.
 *
 * @param int $chain_id
 * @return bool
 */
public function delete_chain( int $chain_id ): bool {
return $this->db->remove_row( 'SERVICE_CHAIN', 'id', $chain_id, [ '%d' ] );
}

/**
 * Get a single chain rule.
 *
 * @param int $chain_id
 * @return object|null
 */
public function get_chain( int $chain_id ) {
return $this->db->get_row( 'SERVICE_CHAIN', $chain_id );
}

/**
 * Get all active chains involving a specific service (either as A or B).
 *
 * @param int $service_id
 * @return array
 */
public function get_chains_for_service( int $service_id ): array {
$table = $this->db->get_table_name( 'SERVICE_CHAIN' );
return $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT * FROM {$table}
 WHERE (service_a_id = %d OR service_b_id = %d) AND status = 1",
$service_id,
$service_id
)
) ?: [];
}

/**
 * Get all chain rules.
 *
 * @param int|null $status  1=active, 0=inactive, null=all
 * @return array
 */
public function get_all_chains( $status = 1 ): array {
$table = $this->db->get_table_name( 'SERVICE_CHAIN' );
if ( null === $status ) {
return $this->db->get_results_raw( "SELECT * FROM {$table} ORDER BY id ASC" ) ?: [];
}
return $this->db->get_results_raw(
$this->db->prepare_sql( "SELECT * FROM {$table} WHERE status = %d ORDER BY id ASC", $status )
) ?: [];
}

// ─────────────────────── AVAILABILITY / EXCLUSION ────────────────────────

/**
 * Given a service and a date/slot, return IDs of services that are closed
 * because of an existing booking.
 *
 * @param int    $service_id  Service that has just been booked.
 * @param string $date        YYYY-MM-DD
 * @param int    $slot_id     0 if date-only logic.
 * @return int[]  Array of service IDs that should be closed.
 */
public function get_excluded_services( int $service_id, string $date, int $slot_id = 0 ): array {
$chains   = $this->get_chains_for_service( $service_id );
$excluded = [];

foreach ( $chains as $chain ) {
$other_id = (int) $chain->service_a_id === $service_id
? (int) $chain->service_b_id
: (int) $chain->service_a_id;

// For unidirectional chains only close when booking A (not B).
if ( 'unidirectional' === $chain->chain_type && (int) $chain->service_b_id === $service_id ) {
continue;
}

if ( $this->service_is_booked_on_date( $service_id, $date, $slot_id ) ) {
$excluded[] = $other_id;
}
}

return array_unique( $excluded );
}

/**
 * Check whether a service is blocked on a date/slot by any of its chains.
 *
 * @param int    $service_id
 * @param string $date
 * @param int    $slot_id
 * @return bool  true if the service is blocked.
 */
public function is_service_blocked_by_chain( int $service_id, string $date, int $slot_id = 0 ): bool {
$chains = $this->get_chains_for_service( $service_id );

foreach ( $chains as $chain ) {
$peer_id = (int) $chain->service_a_id === $service_id
? (int) $chain->service_b_id
: (int) $chain->service_a_id;

if ( $this->service_is_booked_on_date( $peer_id, $date, $slot_id ) ) {
return true;
}
}

return false;
}

/**
 * Determine whether a service has any active confirmed booking on a given date/slot.
 *
 * Uses SUM(current_slots_booked) > 0 instead of COUNT(*) so that
 * zero-capacity SLOTCOUNT rows (e.g. gift bookings with
 * current_slots_booked = 0) never falsely indicate the service is occupied.
 *
 * @param int    $service_id
 * @param string $date
 * @param int    $slot_id   0 = date-level check (any slot).
 * @return bool
 */
public function service_is_booked_on_date( int $service_id, string $date, int $slot_id = 0 ): bool {
$slot_table = $this->db->get_table_name( 'SLOTCOUNT' );

if ( $slot_id > 0 ) {
$total = (int) $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT COALESCE(SUM(current_slots_booked), 0) FROM {$slot_table}
 WHERE service_id = %d AND booking_date = %s AND slot_id = %d AND is_active = 1",
$service_id,
$date,
$slot_id
)
);
} else {
$total = (int) $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT COALESCE(SUM(current_slots_booked), 0) FROM {$slot_table}
 WHERE service_id = %d AND booking_date = %s AND is_active = 1",
$service_id,
$date
)
);
}

return $total > 0;
}
}
