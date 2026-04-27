<?php
/**
 * Virtual Services — Spec §1.8
 *
 * A virtual service is a derived offer that depends on the simultaneous
 * availability of multiple underlying real services.
 *
 * Example: a "10-seat boat" virtual service derived from a "4-seat boat"
 * (service A) and a "6-seat boat" (service B).
 *
 * Propagation rules (§1.8):
 * 1. If the virtual service is sold → close both component real services.
 * 2. If a real component service is sold → close the virtual service.
 * 3. Selling one real component does NOT close the other real component.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BM_VirtualService {

	/** @var Booking_Management_Activator */
	private $activator;

	public function __construct() {
		$this->activator = new Booking_Management_Activator();
	}

	// ─────────────────────── VIRTUAL SERVICE CRUD ────────────────────────────

	/**
	 * Register a service as a virtual/derived service.
	 *
	 * The $service_id must already exist in the SERVICE table; this record
	 * stores the virtual-service metadata that links it to its components.
	 *
	 * @param int    $service_id  ID of the service record in sgbm_services.
	 * @param string $name
	 * @param string $description
	 * @return int|false  Virtual service record ID.
	 */
	public function create_virtual_service( int $service_id, string $name, string $description = '' ) {
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE' );
		$result = $wpdb->replace(
			$table,
			[
				'service_id'  => $service_id,
				'name'        => sanitize_text_field( $name ),
				'description' => sanitize_textarea_field( $description ),
				'status'      => 1,
			],
			[ '%d', '%s', '%s', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Update virtual service metadata.
	 *
	 * @param int   $virtual_id  ID from sgbm_virtual_services.
	 * @param array $data  Keys: name, description, status
	 * @return bool
	 */
	public function update_virtual_service( int $virtual_id, array $data ): bool {
		global $wpdb;
		$table   = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE' );
		$allowed = [ 'name', 'description', 'status' ];
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
		$set['updated_at'] = current_time( 'mysql' );
		$formats[]         = '%s';
		return (bool) $wpdb->update( $table, $set, [ 'id' => $virtual_id ], $formats, [ '%d' ] );
	}

	/**
	 * Delete a virtual service record and all its component links.
	 *
	 * @param int $virtual_id
	 * @return bool
	 */
	public function delete_virtual_service( int $virtual_id ): bool {
		global $wpdb;
		$wpdb->delete(
			$this->activator->get_db_table_name( 'VIRTUAL_SERVICE_COMPONENT' ),
			[ 'virtual_service_id' => $virtual_id ],
			[ '%d' ]
		);
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'VIRTUAL_SERVICE' ),
			[ 'id' => $virtual_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get a virtual service record by its internal ID.
	 *
	 * @param int $virtual_id
	 * @return object|null
	 */
	public function get_virtual_service( int $virtual_id ) {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE id = %d", $virtual_id ) );
	}

	/**
	 * Find the virtual-service record for a given service ID.
	 *
	 * @param int $service_id
	 * @return object|null
	 */
	public function get_virtual_service_by_service_id( int $service_id ) {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE' );
		return $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $table WHERE service_id = %d", $service_id ) );
	}

	/**
	 * Get all active virtual services.
	 *
	 * @return array
	 */
	public function get_all_virtual_services(): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE' );
		return $wpdb->get_results( "SELECT * FROM $table WHERE status = 1 ORDER BY id ASC" ) ?: [];
	}

	// ────────────────────── COMPONENT CRUD ───────────────────────────────────

	/**
	 * Add a component (real service) to a virtual service.
	 *
	 * @param int $virtual_id
	 * @param int $component_service_id
	 * @param int $position
	 * @return int|false
	 */
	public function add_component( int $virtual_id, int $component_service_id, int $position = 0 ) {
		global $wpdb;
		$table  = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE_COMPONENT' );
		$result = $wpdb->insert(
			$table,
			[
				'virtual_service_id'   => $virtual_id,
				'component_service_id' => $component_service_id,
				'component_position'   => $position,
			],
			[ '%d', '%d', '%d' ]
		);
		return $result ? $wpdb->insert_id : false;
	}

	/**
	 * Remove a component from a virtual service.
	 *
	 * @param int $component_id  Row ID from sgbm_virtual_service_components.
	 * @return bool
	 */
	public function remove_component( int $component_id ): bool {
		global $wpdb;
		return (bool) $wpdb->delete(
			$this->activator->get_db_table_name( 'VIRTUAL_SERVICE_COMPONENT' ),
			[ 'id' => $component_id ],
			[ '%d' ]
		);
	}

	/**
	 * Get all component services of a virtual service.
	 *
	 * @param int $virtual_id
	 * @return array  Rows with component_service_id and component_position.
	 */
	public function get_components( int $virtual_id ): array {
		global $wpdb;
		$table = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE_COMPONENT' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT * FROM $table WHERE virtual_service_id = %d ORDER BY component_position ASC, id ASC",
			$virtual_id
		) ) ?: [];
	}

	/**
	 * Find all virtual services that include a given real service as a component.
	 *
	 * @param int $service_id
	 * @return array  Virtual service records.
	 */
	public function get_virtual_services_containing( int $service_id ): array {
		global $wpdb;
		$comp_table    = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE_COMPONENT' );
		$virtual_table = $this->activator->get_db_table_name( 'VIRTUAL_SERVICE' );
		return $wpdb->get_results( $wpdb->prepare(
			"SELECT vs.* FROM $virtual_table vs
			 INNER JOIN $comp_table vc ON vc.virtual_service_id = vs.id
			 WHERE vc.component_service_id = %d AND vs.status = 1",
			$service_id
		) ) ?: [];
	}

	// ─────────────────────── AVAILABILITY PROPAGATION ────────────────────────

	/**
	 * Determine whether a virtual service is available on a given date.
	 *
	 * A virtual service is only available if ALL its component real services
	 * are available (i.e. not already booked / sold out) on that date.
	 *
	 * @param int    $virtual_service_id  ID from sgbm_virtual_services.
	 * @param string $date                YYYY-MM-DD
	 * @return bool
	 */
	public function is_virtual_service_available( int $virtual_service_id, string $date ): bool {
		$components = $this->get_components( $virtual_service_id );
		if ( empty( $components ) ) {
			return false;
		}
		$chain_checker = new BM_ServiceChain();
		foreach ( $components as $comp ) {
			if ( $chain_checker->service_is_booked_on_date( (int) $comp->component_service_id, $date ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Check whether a real service is blocked because its virtual parent is sold.
	 *
	 * When the virtual service is sold, ALL component real services must be
	 * considered closed for that date.
	 *
	 * @param int    $real_service_id
	 * @param string $date  YYYY-MM-DD
	 * @return bool  true = blocked by virtual sale.
	 */
	public function is_real_service_blocked_by_virtual( int $real_service_id, string $date ): bool {
		$virtual_services = $this->get_virtual_services_containing( $real_service_id );
		$chain_checker    = new BM_ServiceChain();

		foreach ( $virtual_services as $vs ) {
			// Check if the virtual service itself has been booked
			if ( $chain_checker->service_is_booked_on_date( (int) $vs->service_id, $date ) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Get all real service IDs that should be blocked when a virtual service
	 * is booked on a given date.
	 *
	 * @param int    $virtual_service_id
	 * @param string $date
	 * @return int[]
	 */
	public function get_blocked_real_services_for_virtual( int $virtual_service_id, string $date ): array {
		$components = $this->get_components( $virtual_service_id );
		return array_map( fn( $c ) => (int) $c->component_service_id, $components );
	}

	/**
	 * Get all virtual service IDs that should be blocked when a real service
	 * component is booked.
	 *
	 * @param int    $real_service_id
	 * @param string $date
	 * @return int[]  Virtual service IDs (from sgbm_virtual_services.id).
	 */
	public function get_blocked_virtual_services_for_real( int $real_service_id, string $date ): array {
		$virtual_services = $this->get_virtual_services_containing( $real_service_id );
		return array_map( fn( $vs ) => (int) $vs->id, $virtual_services );
	}

	/**
	 * Get the full virtual service record with its component list.
	 *
	 * @param int $virtual_id
	 * @return object|null
	 */
	public function get_virtual_service_with_components( int $virtual_id ) {
		$vs = $this->get_virtual_service( $virtual_id );
		if ( ! $vs ) {
			return null;
		}
		$vs->components = $this->get_components( $virtual_id );
		return $vs;
	}
}
