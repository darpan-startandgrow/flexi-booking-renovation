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

	/** @var Booking_Management_Activator */
	private $activator;

	public function __construct() {
		$this->activator = new Booking_Management_Activator();
	}

	// ───────────────────────────── CHAIN CRUD ────────────────────────────────

	/**
	 * Create a chain rule between two services.
	 *
	 * @param int    $service_a_id
	 * @param int    $service_b_id
	 * @param string $chain_type  'exclusive'|'unidirectional'
	 * @return int|false
	 */
	public function create_chain( int $service_a_id, int $service_b_id, string $chain_type = 'exclusive' ) {
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'SERVICE_CHAIN' );
		$result = $wpdb->replace(
			$table,
			[
				'service_a_id' => $service_a_id,
				'service_b_id' => $service_b_id,
				'chain_type'   => in_array( $chain_type, [ 'exclusive', 'unidirectional' ], true ) ? $chain_type : 'exclusive',
				'status'       => 1,
			],
			[ '%d', '%d', '%s', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update chain status or type.
	 *
	 * @param int   $chain_id
	 * @param array $data  Keys: chain_type, status
	 * @return bool
	 */
	public function update_chain( int $chain_id, array $data ): bool {
		global $wpdb;
		$table   = $this->activator->get_db_table_name( 'SERVICE_CHAIN' );
		$allowed = [ 'chain_type', 'status' ];
		$set     = [];
		$formats = [];
		foreach ( $allowed as $key ) {
			if ( array_key_exists( $key, $data ) ) {
				$set[ $key ] = $data[ $key ];
				$formats[]   = 'status' === $key ? '%d' : '%s';
			}
		}
		if ( empty( $set ) ) {
			return false;
		}
		return (bool) $wpdb->update( $table, $set, [ 'id' => $chain_id ], $formats, [ '%d' ] );
	}

	/**
	 * Delete a chain rule.
	 *
	 * @param int $chain_id
	 * @return bool
	 */
	public function delete_chain( int $chain_id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'SERVICE_CHAIN' ),
			[ 'id' => $chain_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get a single chain rule.
	 *
	 * @param int $chain_id
	 * @return object|null
	 */
	public function get_chain( int $chain_id ) {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'SERVICE_CHAIN' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $chain_id ) );
	}

	/**
	 * Get all active chains involving a specific service (either as A or B).
	 *
	 * @param int $service_id
	 * @return array
	 */
	public function get_chains_for_service( int $service_id ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'SERVICE_CHAIN' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table
			 WHERE (service_a_id = %d OR service_b_id = %d) AND status = 1",
			$service_id,
			$service_id
		) ) ?: [];
	}

	/**
	 * Get all chain rules.
	 *
	 * @param int|null $status  1=active, 0=inactive, null=all
	 * @return array
	 */
	public function get_all_chains( $status = 1 ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'SERVICE_CHAIN' );
		if ( null === $status ) {
			return $wpdb->get_results( "SELECT * FROM $table ORDER BY id ASC" ) ?: [];
		}
		return $wpdb->get_results(
			$wpdb->prepare( "SELECT * FROM $table WHERE status = %d ORDER BY id ASC", $status )
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

			// For unidirectional chains only close when booking A (not B)
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

			// Unidirectional: only A blocks B, so if checking B, peer is A which blocks B
			// For exclusive: any booking of the peer blocks this service
			if ( $this->service_is_booked_on_date( $peer_id, $date, $slot_id ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Determine whether a service has any active confirmed booking on a given date/slot.
	 *
	 * @param int    $service_id
	 * @param string $date
	 * @param int    $slot_id   0 = date-level check (any slot).
	 * @return bool
	 */
	public function service_is_booked_on_date( int $service_id, string $date, int $slot_id = 0 ): bool {
		global $wpdb;
		$slot_table = ( new Booking_Management_Activator() )->get_db_table_name( 'SLOTCOUNT' );

		if ( $slot_id > 0 ) {
			$count = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM $slot_table
				 WHERE service_id = %d AND booking_date = %s AND slot_id = %d AND is_active = 1",
				$service_id,
				$date,
				$slot_id
			) );
		} else {
			$count = (int) $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM $slot_table
				 WHERE service_id = %d AND booking_date = %s AND is_active = 1",
				$service_id,
				$date
			) );
		}

		return $count > 0;
	}
}
