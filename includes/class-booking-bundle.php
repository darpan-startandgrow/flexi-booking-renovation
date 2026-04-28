<?php
/**
 * Bundles and Combos — Spec §1.9
 *
 * A Bundle is a standalone commercial product composed of multiple services,
 * priced as the sum of individual items or with a discount.
 *
 * When a bundle is purchased the system records the bundle sale and correctly
 * allocates each included service (fulfilment lines per Part 2 §2.6).
 *
 * Discount types:
 *   'percent' — discount_value is a percentage (0–100).
 *   'fixed'   — discount_value is an absolute amount to subtract.
 *   null/''   — no discount; total = sum of component prices.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class BM_Bundle {

/** @var BM_DBhandler */
private $db;

public function __construct() {
$this->db = new BM_DBhandler();
}

// ───────────────────────────── BUNDLE CRUD ───────────────────────────────

/**
 * Create a bundle.
 *
 * @param string      $name
 * @param string      $description
 * @param string|null $discount_type   'percent'|'fixed'|null
 * @param float       $discount_value
 * @param float       $price           Base price for the bundle (required for commercial identity).
 * @param int         $status          1=active, 0=inactive
 * @return int|false
 */
public function create_bundle( string $name, string $description = '', $discount_type = null, float $discount_value = 0.0, float $price = 0.0, int $status = 1 ) {
return $this->db->insert_row(
'BUNDLE',
[
'name'           => sanitize_text_field( $name ),
'description'    => sanitize_textarea_field( $description ),
'discount_type'  => $discount_type ? sanitize_key( $discount_type ) : null,
'discount_value' => (float) $discount_value,
'price'          => (float) $price,
'status'         => max( 0, min( 1, $status ) ),
],
[ '%s', '%s', null !== $discount_type ? '%s' : '%s', '%f', '%f', '%d' ]
);
}

/**
 * Update a bundle.
 *
 * @param int   $bundle_id
 * @param array $data  Keys: name, description, discount_type, discount_value, price, status
 * @return bool
 */
public function update_bundle( int $bundle_id, array $data ): bool {
$allowed = [ 'name', 'description', 'discount_type', 'discount_value', 'price', 'status' ];
$set     = [];
$formats = [];
foreach ( $allowed as $key ) {
if ( array_key_exists( $key, $data ) ) {
$set[ $key ] = $data[ $key ];
if ( 'status' === $key ) {
$formats[] = '%d';
} elseif ( in_array( $key, [ 'discount_value', 'price' ], true ) ) {
$formats[] = '%f';
} else {
$formats[] = '%s';
}
}
}
if ( empty( $set ) ) {
return false;
}
$set['updated_at'] = current_time( 'mysql' );
$formats[]         = '%s';
return (bool) $this->db->update_row( 'BUNDLE', 'id', $bundle_id, $set, $formats, [ '%d' ] );
}

/**
 * Delete a bundle and all its items.
 *
 * @param int $bundle_id
 * @return bool
 */
public function delete_bundle( int $bundle_id ): bool {
$this->db->delete_where( 'BUNDLE_ITEM', [ 'bundle_id' => $bundle_id ], [ '%d' ] );
return $this->db->remove_row( 'BUNDLE', 'id', $bundle_id, [ '%d' ] );
}

/**
 * Get a single bundle by ID.
 *
 * @param int $bundle_id
 * @return object|null
 */
public function get_bundle( int $bundle_id ) {
return $this->db->get_row( 'BUNDLE', $bundle_id );
}

/**
 * Get all bundles (optionally filtered by status).
 *
 * @param int|null $status  1=active, 0=inactive, null=all
 * @return array
 */
public function get_all_bundles( $status = 1 ): array {
$table = $this->db->get_table_name( 'BUNDLE' );
if ( null === $status ) {
return $this->db->get_results_raw( "SELECT * FROM {$table} ORDER BY id ASC" ) ?: [];
}
return $this->db->get_results_raw(
$this->db->prepare_sql( "SELECT * FROM {$table} WHERE status = %d ORDER BY id ASC", $status )
) ?: [];
}

// ───────────────────────── BUNDLE ITEM CRUD ──────────────────────────────

/**
 * Add a service to a bundle.
 *
 * @param int  $bundle_id
 * @param int  $service_id
 * @param int  $quantity
 * @param bool $is_optional
 * @param int  $position
 * @return int|false
 */
public function add_bundle_item( int $bundle_id, int $service_id, int $quantity = 1, bool $is_optional = false, int $position = 0 ) {
return $this->db->insert_row(
'BUNDLE_ITEM',
[
'bundle_id'     => $bundle_id,
'service_id'    => $service_id,
'quantity'      => max( 1, $quantity ),
'is_optional'   => $is_optional ? 1 : 0,
'item_position' => $position,
],
[ '%d', '%d', '%d', '%d', '%d' ]
);
}

/**
 * Update a bundle item.
 *
 * @param int   $item_id
 * @param array $data  Keys: quantity, is_optional, item_position
 * @return bool
 */
public function update_bundle_item( int $item_id, array $data ): bool {
$allowed = [ 'quantity', 'is_optional', 'item_position' ];
$set     = [];
$formats = [];
foreach ( $allowed as $key ) {
if ( array_key_exists( $key, $data ) ) {
$set[ $key ] = $data[ $key ];
$formats[]   = '%d';
}
}
if ( empty( $set ) ) {
return false;
}
return (bool) $this->db->update_row( 'BUNDLE_ITEM', 'id', $item_id, $set, $formats, [ '%d' ] );
}

/**
 * Remove an item from a bundle.
 *
 * @param int $item_id
 * @return bool
 */
public function remove_bundle_item( int $item_id ): bool {
return $this->db->remove_row( 'BUNDLE_ITEM', 'id', $item_id, [ '%d' ] );
}

/**
 * Get all items in a bundle, ordered by position then ID.
 *
 * @param int $bundle_id
 * @return array
 */
public function get_bundle_items( int $bundle_id ): array {
$table = $this->db->get_table_name( 'BUNDLE_ITEM' );
return $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT * FROM {$table} WHERE bundle_id = %d ORDER BY item_position ASC, id ASC",
$bundle_id
)
) ?: [];
}

/**
 * Get all bundles that include a given service.
 *
 * @param int $service_id
 * @return array
 */
public function get_bundles_for_service( int $service_id ): array {
$item_table   = $this->db->get_table_name( 'BUNDLE_ITEM' );
$bundle_table = $this->db->get_table_name( 'BUNDLE' );
return $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT b.* FROM {$bundle_table} b
 INNER JOIN {$item_table} bi ON bi.bundle_id = b.id
 WHERE bi.service_id = %d AND b.status = 1",
$service_id
)
) ?: [];
}

// ───────────────────────────── PRICING ───────────────────────────────────

/**
 * Calculate the bundle total given an array of component base prices.
 *
 * @param int   $bundle_id
 * @param array $component_prices  Keyed by service_id => price
 * @return float
 */
public function calculate_bundle_total( int $bundle_id, array $component_prices ): float {
$bundle = $this->get_bundle( $bundle_id );
if ( ! $bundle ) {
return 0.0;
}

$items    = $this->get_bundle_items( $bundle_id );
$subtotal = 0.0;
foreach ( $items as $item ) {
$unit_price = isset( $component_prices[ $item->service_id ] ) ? (float) $component_prices[ $item->service_id ] : 0.0;
$subtotal  += $unit_price * (int) $item->quantity;
}

if ( 'percent' === $bundle->discount_type ) {
$discount = $subtotal * ( min( 100, max( 0, (float) $bundle->discount_value ) ) / 100 );
return max( 0.0, $subtotal - $discount );
}

if ( 'fixed' === $bundle->discount_type ) {
return max( 0.0, $subtotal - (float) $bundle->discount_value );
}

return $subtotal;
}

/**
 * Get the full bundle with its items as a structured object.
 *
 * @param int $bundle_id
 * @return object|null  Bundle with an 'items' array.
 */
public function get_bundle_with_items( int $bundle_id ) {
$bundle = $this->get_bundle( $bundle_id );
if ( ! $bundle ) {
return null;
}
$bundle->items = $this->get_bundle_items( $bundle_id );
return $bundle;
}

// ─────────────────────────── FULFILMENT ──────────────────────────────────

/**
 * Record a bundle sale and insert one fulfilment line per bundle item.
 *
 * Called from the booking creation path after a booking row has been
 * saved so that every bundle purchase has a traceable audit trail.
 *
 * @param int   $booking_id       ID of the freshly created BOOKING row.
 * @param int   $bundle_id        Bundle that was purchased.
 * @param float $discount_applied Discount amount that was applied.
 * @return int|false  BUNDLE_BOOKING row ID on success, false on failure.
 */
public function record_bundle_booking( int $booking_id, int $bundle_id, float $discount_applied = 0.0 ) {
$bundle = $this->get_bundle( $bundle_id );
if ( ! $bundle || ! $booking_id ) {
return false;
}

$bundle_booking_id = $this->db->insert_row(
'BUNDLE_BOOKING',
[
'booking_id'       => $booking_id,
'bundle_id'        => $bundle_id,
'bundle_name'      => (string) $bundle->name,
'discount_applied' => $discount_applied,
],
[ '%d', '%d', '%s', '%f' ]
);

if ( ! $bundle_booking_id ) {
return false;
}

$items = $this->get_bundle_items( $bundle_id );
foreach ( $items as $item ) {
$this->db->insert_row(
'BUNDLE_FULFILMENT_LINE',
[
'bundle_booking_id' => (int) $bundle_booking_id,
'service_id'        => (int) $item->service_id,
'quantity'          => (int) $item->quantity,
],
[ '%d', '%d', '%d' ]
);
}

return $bundle_booking_id;
}

/**
 * Delete all fulfilment records for a booking (called on booking cancellation).
 *
 * @param int $booking_id
 * @return bool
 */
public function delete_fulfilment_for_booking( int $booking_id ): bool {
$bb_table  = $this->db->get_table_name( 'BUNDLE_BOOKING' );
$bfl_table = $this->db->get_table_name( 'BUNDLE_FULFILMENT_LINE' );

// Find bundle booking IDs for this booking.
$bb_ids = $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT id FROM {$bb_table} WHERE booking_id = %d",
$booking_id
)
);

if ( ! empty( $bb_ids ) ) {
foreach ( $bb_ids as $bb ) {
$this->db->delete_where( 'BUNDLE_FULFILMENT_LINE', [ 'bundle_booking_id' => (int) $bb->id ], [ '%d' ] );
}
}

return $this->db->delete_where( 'BUNDLE_BOOKING', [ 'booking_id' => $booking_id ], [ '%d' ] );
}
}
