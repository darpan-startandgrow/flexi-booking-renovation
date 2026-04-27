<?php
/**
 * Resource Pool — Spec §1.5 (chaining) + §1.6 (shared availability) + Part 2 §2.6
 *
 * Manages shared capacity pools that multiple services can draw from.
 * Allocation rules:
 *   'shared'  — all linked services compete for the same seats (§1.6).
 *   'private' — booking one service makes the pool unavailable to others (§1.5).
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class BM_ResourcePool {

/** @var BM_DBhandler */
private $db;

public function __construct() {
$this->db = new BM_DBhandler();
}

// ─────────────────────────── RESOURCE POOL CRUD ──────────────────────────

/**
 * Create a new resource pool.
 *
 * @param string $name
 * @param int    $total_capacity
 * @param string $allocation_rule  'shared'|'private'
 * @param string $description
 * @return int|false  Inserted pool ID, or false on failure.
 */
public function create_pool( string $name, int $total_capacity, string $allocation_rule = 'shared', string $description = '' ) {
return $this->db->insert_row(
'RESOURCE_POOL',
[
'name'            => sanitize_text_field( $name ),
'description'     => sanitize_textarea_field( $description ),
'total_capacity'  => absint( $total_capacity ),
'allocation_rule' => in_array( $allocation_rule, [ 'shared', 'private' ], true ) ? $allocation_rule : 'shared',
'status'          => 1,
],
[ '%s', '%s', '%d', '%s', '%d' ]
);
}

/**
 * Update a resource pool.
 *
 * @param int   $pool_id
 * @param array $data  Keys: name, description, total_capacity, allocation_rule, status
 * @return bool
 */
public function update_pool( int $pool_id, array $data ): bool {
$allowed = [ 'name', 'description', 'total_capacity', 'allocation_rule', 'status' ];
$set     = [];
$formats = [];
foreach ( $allowed as $key ) {
if ( array_key_exists( $key, $data ) ) {
$set[ $key ] = $data[ $key ];
$formats[]   = in_array( $key, [ 'total_capacity', 'status' ], true ) ? '%d' : '%s';
}
}
if ( empty( $set ) ) {
return false;
}
$set['updated_at'] = current_time( 'mysql' );
$formats[]         = '%s';
return (bool) $this->db->update_row( 'RESOURCE_POOL', 'id', $pool_id, $set, $formats, [ '%d' ] );
}

/**
 * Delete a resource pool and all its service links.
 *
 * @param int $pool_id
 * @return bool
 */
public function delete_pool( int $pool_id ): bool {
$this->unlink_all_services_from_pool( $pool_id );
return $this->db->remove_row( 'RESOURCE_POOL', 'id', $pool_id, [ '%d' ] );
}

/**
 * Retrieve a single pool by ID.
 *
 * @param int $pool_id
 * @return object|null
 */
public function get_pool( int $pool_id ) {
return $this->db->get_row( 'RESOURCE_POOL', $pool_id );
}

/**
 * Retrieve all pools (optionally filtered by status).
 *
 * @param int|null $status  1=active, 0=inactive, null=all
 * @return array
 */
public function get_all_pools( $status = 1 ): array {
$table = $this->db->get_table_name( 'RESOURCE_POOL' );
if ( null === $status ) {
return $this->db->get_results_raw( "SELECT * FROM {$table} ORDER BY id ASC" ) ?: [];
}
return $this->db->get_results_raw(
$this->db->prepare_sql( "SELECT * FROM {$table} WHERE status = %d ORDER BY id ASC", $status )
) ?: [];
}

// ──────────────────────── SERVICE ↔ POOL LINKS ───────────────────────────

/**
 * Link a service to a resource pool.
 *
 * @param int $service_id
 * @param int $pool_id
 * @param int $consumption_per_booking  How many pool seats each booking consumes.
 * @return int|false
 */
public function link_service_to_pool( int $service_id, int $pool_id, int $consumption_per_booking = 1 ) {
$link_table = $this->db->get_table_name( 'SERVICE_RESOURCE_POOL' );

// Check for existing link so we don't reset created_at.
$existing = $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT id FROM {$link_table} WHERE service_id = %d AND resource_pool_id = %d",
$service_id,
$pool_id
)
);

if ( $existing ) {
$this->db->update_where(
'SERVICE_RESOURCE_POOL',
[ 'consumption_per_booking' => max( 1, $consumption_per_booking ) ],
[ 'id' => (int) $existing ],
[ '%d' ],
[ '%d' ]
);
return (int) $existing;
}

return $this->db->insert_row(
'SERVICE_RESOURCE_POOL',
[
'service_id'              => $service_id,
'resource_pool_id'        => $pool_id,
'consumption_per_booking' => max( 1, $consumption_per_booking ),
],
[ '%d', '%d', '%d' ]
);
}

/**
 * Unlink a service from a specific pool.
 *
 * @param int $service_id
 * @param int $pool_id
 * @return bool
 */
public function unlink_service_from_pool( int $service_id, int $pool_id ): bool {
return $this->db->delete_where(
'SERVICE_RESOURCE_POOL',
[ 'service_id' => $service_id, 'resource_pool_id' => $pool_id ],
[ '%d', '%d' ]
);
}

/**
 * Remove all service links from a pool.
 *
 * @param int $pool_id
 */
public function unlink_all_services_from_pool( int $pool_id ): void {
$this->db->delete_where( 'SERVICE_RESOURCE_POOL', [ 'resource_pool_id' => $pool_id ], [ '%d' ] );
}

/**
 * Get all pools linked to a service.
 *
 * @param int $service_id
 * @return array
 */
public function get_pools_for_service( int $service_id ): array {
$link_table = $this->db->get_table_name( 'SERVICE_RESOURCE_POOL' );
$pool_table = $this->db->get_table_name( 'RESOURCE_POOL' );
return $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT rp.*, srp.consumption_per_booking
 FROM {$pool_table} rp
 INNER JOIN {$link_table} srp ON srp.resource_pool_id = rp.id
 WHERE srp.service_id = %d AND rp.status = 1",
$service_id
)
) ?: [];
}

/**
 * Get all services linked to a pool.
 *
 * @param int $pool_id
 * @return array
 */
public function get_services_in_pool( int $pool_id ): array {
$link_table = $this->db->get_table_name( 'SERVICE_RESOURCE_POOL' );
return $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT id, service_id, consumption_per_booking FROM {$link_table} WHERE resource_pool_id = %d",
$pool_id
)
) ?: [];
}

// ───────────────────────── AVAILABILITY LOGIC ────────────────────────────

/**
 * Get the remaining availability of a resource pool on a given date.
 *
 * Sums consumption from SLOTCOUNT rows for all services in the pool on
 * that date and subtracts from total_capacity.
 *
 * @param int    $pool_id
 * @param string $date  YYYY-MM-DD
 * @return int  Remaining seats (never negative).
 */
public function get_pool_availability( int $pool_id, string $date ): int {
$pool = $this->get_pool( $pool_id );
if ( ! $pool ) {
return 0;
}

$link_table = $this->db->get_table_name( 'SERVICE_RESOURCE_POOL' );
$slot_table = $this->db->get_table_name( 'SLOTCOUNT' );

// Total consumed seats across all linked services on this date.
$consumed = (int) $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT COALESCE(SUM(sc.current_slots_booked * srp.consumption_per_booking), 0)
 FROM {$slot_table} sc
 INNER JOIN {$link_table} srp ON srp.service_id = sc.service_id
 WHERE srp.resource_pool_id = %d
   AND sc.booking_date = %s
   AND sc.is_active = 1",
$pool_id,
$date
)
);

return max( 0, (int) $pool->total_capacity - $consumed );
}

/**
 * Determine whether a service can be booked on a given date, considering
 * all resource pools it belongs to.
 *
 * @param int    $service_id
 * @param string $date        YYYY-MM-DD
 * @param int    $seats_requested
 * @return bool
 */
public function is_service_bookable_via_pools( int $service_id, string $date, int $seats_requested = 1 ): bool {
$pools = $this->get_pools_for_service( $service_id );
foreach ( $pools as $pool_row ) {
$pool = $this->get_pool( (int) $pool_row->id );
if ( ! $pool ) {
continue;
}
$needed    = $seats_requested * (int) $pool_row->consumption_per_booking;
$remaining = $this->get_pool_availability( (int) $pool->id, $date );

if ( 'private' === $pool->allocation_rule ) {
// For private/exclusive pools, any consumption makes the service unbookable for others.
if ( $remaining < $pool->total_capacity ) {
return false;
}
} else {
// Shared pool: check enough seats remain.
if ( $remaining < $needed ) {
return false;
}
}
}
return true;
}

/**
 * Atomically verify pool capacity and return true if all pools for a
 * service can accommodate the booking.
 *
 * Uses SELECT … FOR UPDATE inside a transaction so concurrent requests
 * serialise on the SLOTCOUNT rows and cannot double-book the same seats.
 *
 * Called from the booking-create path (after SLOTCOUNT lock is acquired).
 *
 * @param int    $service_id
 * @param string $date
 * @param int    $seats_requested
 * @return bool  True if seats are available.
 */
public function verify_pool_capacity_for_booking( int $service_id, string $date, int $seats_requested = 1 ): bool {
$pools = $this->get_pools_for_service( $service_id );
if ( empty( $pools ) ) {
return true; // Service not in any pool — no constraint.
}

$link_table = $this->db->get_table_name( 'SERVICE_RESOURCE_POOL' );
$slot_table = $this->db->get_table_name( 'SLOTCOUNT' );

foreach ( $pools as $pool_row ) {
$pool = $this->get_pool( (int) $pool_row->id );
if ( ! $pool ) {
continue;
}
$needed = $seats_requested * (int) $pool_row->consumption_per_booking;

// Lock all SLOTCOUNT rows for services in this pool to prevent race conditions.
$this->db->execute_ddl(
$this->db->prepare_sql(
"SELECT sc.id FROM {$slot_table} sc
 INNER JOIN {$link_table} srp ON srp.service_id = sc.service_id
 WHERE srp.resource_pool_id = %d AND sc.booking_date = %s AND sc.is_active = 1
 FOR UPDATE",
(int) $pool->id,
$date
)
);

// Re-read capacity after acquiring lock.
$consumed = (int) $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT COALESCE(SUM(sc.current_slots_booked * srp.consumption_per_booking), 0)
 FROM {$slot_table} sc
 INNER JOIN {$link_table} srp ON srp.service_id = sc.service_id
 WHERE srp.resource_pool_id = %d
   AND sc.booking_date = %s
   AND sc.is_active = 1",
(int) $pool->id,
$date
)
);

$remaining = max( 0, (int) $pool->total_capacity - $consumed );

if ( 'private' === $pool->allocation_rule ) {
if ( $remaining < $pool->total_capacity ) {
return false;
}
} else {
if ( $remaining < $needed ) {
return false;
}
}
}
return true;
}
}
