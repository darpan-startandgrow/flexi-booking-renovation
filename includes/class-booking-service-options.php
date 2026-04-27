<?php
/**
 * Service Options — Spec §1.7
 *
 * Manages mutually exclusive purchase variants (OptionSets) for a service.
 * Example: a thermal park entry with a "light" option (ticket only) vs
 * a "full" option (ticket + lunch + wellness kit).
 *
 * Rules:
 * - A service can have one or more OptionSets.
 * - Within an OptionSet the user selects exactly one OptionValue.
 * - Options share the base service availability; they do not create separate capacity.
 * - A price_modifier adjusts the service base price; price_override replaces it.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BM_ServiceOptions {

	/** @var Booking_Management_Activator */
	private $activator;

	public function __construct() {
		$this->activator = new Booking_Management_Activator();
	}

	// ─────────────────────────── OPTION SET CRUD ─────────────────────────────

	/**
	 * Create an option set for a service.
	 *
	 * @param int    $service_id
	 * @param string $name
	 * @param string $description
	 * @param bool   $is_required
	 * @return int|false
	 */
	public function create_option_set( int $service_id, string $name, string $description = '', bool $is_required = true ) {
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'OPTION_SET' );
		$result = $wpdb->insert(
			$table,
			[
				'service_id'  => $service_id,
				'name'        => sanitize_text_field( $name ),
				'description' => sanitize_textarea_field( $description ),
				'is_required' => $is_required ? 1 : 0,
				'status'      => 1,
			],
			[ '%d', '%s', '%s', '%d', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update an option set.
	 *
	 * @param int   $set_id
	 * @param array $data  Keys: name, description, is_required, status
	 * @return bool
	 */
	public function update_option_set( int $set_id, array $data ): bool {
		global $wpdb;
		$table   = $this->activator->get_db_table_name( 'OPTION_SET' );
		$allowed = [ 'name', 'description', 'is_required', 'status' ];
		$set     = [];
		$formats = [];
		foreach ( $allowed as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				$set[ $key ] = $data[ $key ];
				$formats[]   = in_array( $key, [ 'is_required', 'status' ], true ) ? '%d' : '%s';
			}
		}
		if ( empty( $set ) ) {
			return false;
		}
		$set['updated_at'] = current_time( 'mysql' );
		$formats[]         = '%s';
		return (bool) $wpdb->update( $table, $set, [ 'id' => $set_id ], $formats, [ '%d' ] );
	}

	/**
	 * Delete an option set and all its values.
	 *
	 * @param int $set_id
	 * @return bool
	 */
	public function delete_option_set( int $set_id ): bool {
		global $wpdb;
		// Remove child option values first
		$wpdb->delete(
			$this->activator->get_db_table_name( 'OPTION_VALUE' ),
			[ 'option_set_id' => $set_id ],
			[ '%d' ]
		);
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'OPTION_SET' ),
			[ 'id' => $set_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get a single option set by ID.
	 *
	 * @param int $set_id
	 * @return object|null
	 */
	public function get_option_set( int $set_id ) {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'OPTION_SET' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $set_id ) );
	}

	/**
	 * Get all active option sets for a service.
	 *
	 * @param int $service_id
	 * @return array
	 */
	public function get_option_sets_for_service( int $service_id ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'OPTION_SET' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table WHERE service_id = %d AND status = 1 ORDER BY id ASC",
			$service_id
		) ) ?: [];
	}

	// ────────────────────────── OPTION VALUE CRUD ────────────────────────────

	/**
	 * Create an option value within an option set.
	 *
	 * @param int        $set_id
	 * @param string     $name
	 * @param string     $description
	 * @param float      $price_modifier   Delta to apply on top of base price.
	 * @param float|null $price_override   If set, replaces base price entirely.
	 * @param bool       $is_default
	 * @return int|false
	 */
	public function create_option_value(
		int $set_id,
		string $name,
		string $description = '',
		float $price_modifier = 0.0,
		$price_override = null,
		bool $is_default = false
	) {
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'OPTION_VALUE' );
		$result = $wpdb->insert(
			$table,
			[
				'option_set_id'  => $set_id,
				'name'           => sanitize_text_field( $name ),
				'description'    => sanitize_textarea_field( $description ),
				'price_modifier' => (float) $price_modifier,
				'price_override' => null !== $price_override ? (float) $price_override : null,
				'is_default'     => $is_default ? 1 : 0,
				'status'         => 1,
			],
			// null values use '%s' so wpdb serialises them as NULL
			[ '%d', '%s', '%s', '%f', null !== $price_override ? '%f' : '%s', '%d', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update an option value.
	 *
	 * @param int   $value_id
	 * @param array $data  Keys: name, description, price_modifier, price_override, is_default, status
	 * @return bool
	 */
	public function update_option_value( int $value_id, array $data ): bool {
		global $wpdb;
		$table   = $this->activator->get_db_table_name( 'OPTION_VALUE' );
		$allowed = [ 'name', 'description', 'price_modifier', 'price_override', 'is_default', 'status' ];
		$set     = [];
		$formats = [];
		foreach ( $allowed as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				$set[ $key ] = $data[ $key ];
				if ( in_array( $key, [ 'is_default', 'status' ], true ) ) {
					$formats[] = '%d';
				} elseif ( in_array( $key, [ 'price_modifier', 'price_override' ], true ) ) {
					// null values use '%s' so wpdb serialises them as NULL
					$formats[] = null !== $data[ $key ] ? '%f' : '%s';
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
		return (bool) $wpdb->update( $table, $set, [ 'id' => $value_id ], $formats, [ '%d' ] );
	}

	/**
	 * Delete an option value.
	 *
	 * @param int $value_id
	 * @return bool
	 */
	public function delete_option_value( int $value_id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'OPTION_VALUE' ),
			[ 'id' => $value_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get a single option value by ID.
	 *
	 * @param int $value_id
	 * @return object|null
	 */
	public function get_option_value( int $value_id ) {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'OPTION_VALUE' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $value_id ) );
	}

	/**
	 * Get all active values for an option set.
	 *
	 * @param int $set_id
	 * @return array
	 */
	public function get_values_for_option_set( int $set_id ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'OPTION_VALUE' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table WHERE option_set_id = %d AND status = 1 ORDER BY is_default DESC, id ASC",
			$set_id
		) ) ?: [];
	}

	// ────────────────────────── PRICING HELPERS ──────────────────────────────

	/**
	 * Compute the final service price given a selected option value.
	 *
	 * @param float $base_price   The service base price.
	 * @param int   $value_id     Selected OptionValue ID.
	 * @return float
	 */
	public function apply_option_to_price( float $base_price, int $value_id ): float {
		$value = $this->get_option_value( $value_id );
		if ( ! $value ) {
			return $base_price;
		}
		if ( null !== $value->price_override && '' !== $value->price_override ) {
			return (float) $value->price_override;
		}
		return $base_price + (float) $value->price_modifier;
	}

	/**
	 * Get the full option tree for a service (sets + their values).
	 *
	 * @param int $service_id
	 * @return array  Array of option sets, each with a 'values' key.
	 */
	public function get_option_tree_for_service( int $service_id ): array {
		$sets = $this->get_option_sets_for_service( $service_id );
		foreach ( $sets as &$set ) {
			$set->values = $this->get_values_for_option_set( (int) $set->id );
		}
		return $sets;
	}
}
