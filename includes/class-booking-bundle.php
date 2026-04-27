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

	/** @var Booking_Management_Activator */
	private $activator;

	public function __construct() {
		$this->activator = new Booking_Management_Activator();
	}

	// ───────────────────────────── BUNDLE CRUD ───────────────────────────────

	/**
	 * Create a bundle.
	 *
	 * @param string      $name
	 * @param string      $description
	 * @param string|null $discount_type   'percent'|'fixed'|null
	 * @param float       $discount_value
	 * @return int|false
	 */
	public function create_bundle( string $name, string $description = '', $discount_type = null, float $discount_value = 0.0 ) {
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'BUNDLE' );
		$result = $wpdb->insert(
			$table,
			[
				'name'           => sanitize_text_field( $name ),
				'description'    => sanitize_textarea_field( $description ),
				'discount_type'  => $discount_type ? sanitize_key( $discount_type ) : null,
				'discount_value' => (float) $discount_value,
				'status'         => 1,
			],
			[ '%s', '%s', null !== $discount_type ? '%s' : 'NULL', '%f', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update a bundle.
	 *
	 * @param int   $bundle_id
	 * @param array $data  Keys: name, description, discount_type, discount_value, status
	 * @return bool
	 */
	public function update_bundle( int $bundle_id, array $data ): bool {
		global $wpdb;
		$table   = $this->activator->get_db_table_name( 'BUNDLE' );
		$allowed = [ 'name', 'description', 'discount_type', 'discount_value', 'status' ];
		$set     = [];
		$formats = [];
		foreach ( $allowed as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				$set[ $key ] = $data[ $key ];
				if ( 'status' === $key ) {
					$formats[] = '%d';
				} elseif ( 'discount_value' === $key ) {
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
		return (bool) $wpdb->update( $table, $set, [ 'id' => $bundle_id ], $formats, [ '%d' ] );
	}

	/**
	 * Delete a bundle and all its items.
	 *
	 * @param int $bundle_id
	 * @return bool
	 */
	public function delete_bundle( int $bundle_id ): bool {
		global $wpdb;
		$wpdb->delete(
			$this->activator->get_db_table_name( 'BUNDLE_ITEM' ),
			[ 'bundle_id' => $bundle_id ],
			[ '%d' ]
		);
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'BUNDLE' ),
			[ 'id' => $bundle_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get a single bundle by ID.
	 *
	 * @param int $bundle_id
	 * @return object|null
	 */
	public function get_bundle( int $bundle_id ) {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'BUNDLE' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $bundle_id ) );
	}

	/**
	 * Get all bundles (optionally filtered by status).
	 *
	 * @param int|null $status  1=active, 0=inactive, null=all
	 * @return array
	 */
	public function get_all_bundles( $status = 1 ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'BUNDLE' );
		if ( null === $status ) {
			return $wpdb->get_results( "SELECT * FROM $table ORDER BY id ASC" ) ?: [];
		}
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $table WHERE status = %d ORDER BY id ASC", $status )
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
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'BUNDLE_ITEM' );
		$result = $wpdb->insert(
			$table,
			[
				'bundle_id'     => $bundle_id,
				'service_id'    => $service_id,
				'quantity'      => max( 1, $quantity ),
				'is_optional'   => $is_optional ? 1 : 0,
				'item_position' => $position,
			],
			[ '%d', '%d', '%d', '%d', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update a bundle item.
	 *
	 * @param int   $item_id
	 * @param array $data  Keys: quantity, is_optional, item_position
	 * @return bool
	 */
	public function update_bundle_item( int $item_id, array $data ): bool {
		global $wpdb;
		$table   = $this->activator->get_db_table_name( 'BUNDLE_ITEM' );
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
		return (bool) $wpdb->update( $table, $set, [ 'id' => $item_id ], $formats, [ '%d' ] );
	}

	/**
	 * Remove an item from a bundle.
	 *
	 * @param int $item_id
	 * @return bool
	 */
	public function remove_bundle_item( int $item_id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'BUNDLE_ITEM' ),
			[ 'id' => $item_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get all items in a bundle, ordered by position then ID.
	 *
	 * @param int $bundle_id
	 * @return array
	 */
	public function get_bundle_items( int $bundle_id ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'BUNDLE_ITEM' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table WHERE bundle_id = %d ORDER BY item_position ASC, id ASC",
			$bundle_id
		) ) ?: [];
	}

	/**
	 * Get all bundles that include a given service.
	 *
	 * @param int $service_id
	 * @return array
	 */
	public function get_bundles_for_service( int $service_id ): array {
		global $wpdb;
		$item_table   = $this->activator->get_db_table_name( 'BUNDLE_ITEM' );
		$bundle_table = $this->activator->get_db_table_name( 'BUNDLE' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT b.* FROM $bundle_table b
			 INNER JOIN $item_table bi ON bi.bundle_id = b.id
			 WHERE bi.service_id = %d AND b.status = 1",
			$service_id
		) ) ?: [];
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

		$items   = $this->get_bundle_items( $bundle_id );
		$subtotal = 0.0;
		foreach ( $items as $item ) {
			$unit_price  = isset( $component_prices[ $item->service_id ] ) ? (float) $component_prices[ $item->service_id ] : 0.0;
			$subtotal   += $unit_price * (int) $item->quantity;
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
}
