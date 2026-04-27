<?php
/**
 * Product as Extra — Spec §1.4
 *
 * Allows a standalone service to be sold as an add-on during the purchase of
 * another service. The add-on service retains its own identity, availability,
 * and booking logic; from the user's perspective it is an upsell; from the
 * system's perspective it is an autonomous product also usable as a child.
 *
 * Key rules (§1.4):
 * - An addon service keeps its own price (unless a price_override is set).
 * - An addon service keeps its own availability (its slots are decremented
 *   independently when it is selected).
 * - From the order it is recognisable as a separate service upsell.
 * - This is NOT a bundle (user chooses whether to add it) and NOT an option
 *   (it is additional, not a variant).
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
exit;
}

class BM_ServiceAsExtra {

/** @var BM_DBhandler */
private $db;

public function __construct() {
$this->db = new BM_DBhandler();
}

// ──────────────────────── SERVICE-AS-EXTRA CRUD ──────────────────────────

/**
 * Register a service as an add-on for another service.
 *
 * @param int        $parent_service_id   The host service.
 * @param int        $addon_service_id    The service to offer as add-on.
 * @param float|null $price_override      If set, overrides the addon's default_price.
 * @param bool       $is_visible_frontend Whether to show on public booking form.
 * @return int|false
 */
public function create_service_as_extra( int $parent_service_id, int $addon_service_id, $price_override = null, bool $is_visible_frontend = true ) {
$table = $this->db->get_table_name( 'SERVICE_AS_EXTRA' );

// Check for existing row to avoid resetting created_at.
$existing = $this->db->get_var_raw(
$this->db->prepare_sql(
"SELECT id FROM {$table} WHERE parent_service_id = %d AND addon_service_id = %d",
$parent_service_id,
$addon_service_id
)
);

$data    = [
'is_visible_frontend' => $is_visible_frontend ? 1 : 0,
'status'              => 1,
];
$formats = [ '%d', '%d' ];

if ( null !== $price_override ) {
$data['price_override'] = (float) $price_override;
$formats[]              = '%f';
} else {
$data['price_override'] = null;
$formats[]              = '%s';
}

if ( $existing ) {
$data['updated_at'] = current_time( 'mysql' );
$formats[]          = '%s';
$this->db->update_where( 'SERVICE_AS_EXTRA', $data, [ 'id' => (int) $existing ], $formats, [ '%d' ] );
return (int) $existing;
}

$insert_data    = [
'parent_service_id'   => $parent_service_id,
'addon_service_id'    => $addon_service_id,
'is_visible_frontend' => $is_visible_frontend ? 1 : 0,
'status'              => 1,
];
$insert_formats = [ '%d', '%d', '%d', '%d' ];
if ( null !== $price_override ) {
$insert_data['price_override'] = (float) $price_override;
$insert_formats[]              = '%f';
} else {
$insert_data['price_override'] = null;
$insert_formats[]              = '%s';
}

return $this->db->insert_row( 'SERVICE_AS_EXTRA', $insert_data, $insert_formats );
}

/**
 * Update a service-as-extra entry.
 *
 * @param int   $entry_id
 * @param array $data  Keys: price_override, is_visible_frontend, status
 * @return bool
 */
public function update_service_as_extra( int $entry_id, array $data ): bool {
$allowed = [ 'price_override', 'is_visible_frontend', 'status' ];
$set     = [];
$formats = [];
foreach ( $allowed as $key ) {
if ( array_key_exists( $key, $data ) ) {
$set[ $key ] = $data[ $key ];
if ( 'price_override' === $key ) {
// NULL is a valid value — pass null with '%s' so wpdb serialises as NULL.
$formats[] = null !== $data[ $key ] ? '%f' : '%s';
} else {
$formats[] = '%d';
}
}
}
if ( empty( $set ) ) {
return false;
}
$set['updated_at'] = current_time( 'mysql' );
$formats[]         = '%s';
return (bool) $this->db->update_row( 'SERVICE_AS_EXTRA', 'id', $entry_id, $set, $formats, [ '%d' ] );
}

/**
 * Remove a service-as-extra relationship.
 *
 * @param int $entry_id
 * @return bool
 */
public function delete_service_as_extra( int $entry_id ): bool {
return $this->db->remove_row( 'SERVICE_AS_EXTRA', 'id', $entry_id, [ '%d' ] );
}

/**
 * Get a single service-as-extra entry.
 *
 * @param int $entry_id
 * @return object|null
 */
public function get_service_as_extra( int $entry_id ) {
return $this->db->get_row( 'SERVICE_AS_EXTRA', $entry_id );
}

/**
 * Get all active add-on services for a parent service.
 *
 * @param int  $parent_service_id
 * @param bool $frontend_only  If true, only return is_visible_frontend=1 entries.
 * @return array
 */
public function get_addons_for_service( int $parent_service_id, bool $frontend_only = false ): array {
$table = $this->db->get_table_name( 'SERVICE_AS_EXTRA' );
$sql   = $this->db->prepare_sql(
"SELECT * FROM {$table} WHERE parent_service_id = %d AND status = 1",
$parent_service_id
);
if ( $frontend_only ) {
$sql .= ' AND is_visible_frontend = 1';
}
return $this->db->get_results_raw( $sql ) ?: [];
}

/**
 * Get all parent services for which a given service is registered as an add-on.
 *
 * @param int $addon_service_id
 * @return array
 */
public function get_parent_services_for_addon( int $addon_service_id ): array {
$table = $this->db->get_table_name( 'SERVICE_AS_EXTRA' );
return $this->db->get_results_raw(
$this->db->prepare_sql(
"SELECT * FROM {$table} WHERE addon_service_id = %d AND status = 1",
$addon_service_id
)
) ?: [];
}

// ────────────────────────── PRICE HELPER ─────────────────────────────────

/**
 * Get the effective price for an addon service in the context of a parent.
 *
 * If a price_override is configured it takes precedence; otherwise the
 * addon service's own default_price from the SERVICE table is used.
 *
 * @param int $parent_service_id
 * @param int $addon_service_id
 * @return float
 */
public function get_addon_price( int $parent_service_id, int $addon_service_id ): float {
$table = $this->db->get_table_name( 'SERVICE_AS_EXTRA' );
$row   = $this->db->get_row_raw(
$this->db->prepare_sql(
"SELECT price_override FROM {$table}
 WHERE parent_service_id = %d AND addon_service_id = %d AND status = 1",
$parent_service_id,
$addon_service_id
)
);

if ( $row && null !== $row->price_override ) {
return (float) $row->price_override;
}

// Fall back to the addon's own price.
$svc_table = $this->db->get_table_name( 'SERVICE' );
$price     = $this->db->get_var_raw(
$this->db->prepare_sql( "SELECT default_price FROM {$svc_table} WHERE id = %d", $addon_service_id )
);

return null !== $price ? (float) $price : 0.0;
}
}
