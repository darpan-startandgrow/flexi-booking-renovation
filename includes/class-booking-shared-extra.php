<?php
/**
 * Shared Inventory Extras — Spec §1.3
 *
 * A SharedExtra is a globally-managed add-on with a centralised stock counter
 * that can be linked to multiple services.  This class wraps the existing
 * GLOBALEXTRA / SERVICEGLOBALEXTRA tables as a proper business object,
 * providing CRUD, service-link management, and per-service consumption
 * analytics in one place.
 *
 * The underlying storage (GLOBALEXTRA / SERVICEGLOBALEXTRA / EXTRASLOTCOUNT)
 * is unchanged; this class adds the aligned API surface the spec requires.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @since      3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class BM_SharedExtra {

	/** @var BM_DBhandler */
	private $db;

	public function __construct() {
		$this->db = new BM_DBhandler();
	}

	// ─────────────────────────── EXTRA CRUD ──────────────────────────────────

	/**
	 * Create a shared extra.
	 *
	 * @param string $name
	 * @param float  $price
	 * @param int    $max_capacity
	 * @param string $description
	 * @param bool   $is_visible_frontend
	 * @return int|false  New row ID or false on failure.
	 */
	public function create_shared_extra(
		string $name,
		float $price = 0.0,
		int $max_capacity = 1,
		string $description = '',
		bool $is_visible_frontend = true
	) {
		return $this->db->insert_row(
			'GLOBALEXTRA',
			[
				'name'                 => sanitize_text_field( $name ),
				'description'          => sanitize_textarea_field( $description ),
				'price'                => $price,
				'max_capacity'         => max( 1, $max_capacity ),
				'is_visible_frontend'  => $is_visible_frontend ? 1 : 0,
				'duration_hours'       => 0,
				'total_operation_hours' => 0,
				'link_woocommerce'     => 0,
			],
			[ '%s', '%s', '%f', '%d', '%d', '%f', '%f', '%d' ]
		);
	}

	/**
	 * Update a shared extra.
	 *
	 * @param int   $extra_id
	 * @param array $data  Accepted keys: name, description, price, max_capacity,
	 *                     is_visible_frontend, duration_hours, total_operation_hours,
	 *                     link_woocommerce, wc_product_id.
	 * @return bool
	 */
	public function update_shared_extra( int $extra_id, array $data ): bool {
		$allowed = [
			'name'                  => '%s',
			'description'           => '%s',
			'price'                 => '%f',
			'max_capacity'          => '%d',
			'is_visible_frontend'   => '%d',
			'duration_hours'        => '%f',
			'total_operation_hours' => '%f',
			'link_woocommerce'      => '%d',
			'wc_product_id'         => '%d',
		];
		$set     = [];
		$formats = [];
		foreach ( $allowed as $key => $fmt ) {
			if ( array_key_exists( $key, $data ) ) {
				$set[ $key ] = $data[ $key ];
				$formats[]   = $fmt;
			}
		}
		if ( empty( $set ) ) {
			return false;
		}
		$set['updated_at'] = current_time( 'mysql' );
		$formats[]         = '%s';
		return (bool) $this->db->update_row( 'GLOBALEXTRA', 'id', $extra_id, $set, $formats, [ '%d' ] );
	}

	/**
	 * Delete a shared extra and its service links.
	 *
	 * @param int $extra_id
	 * @return bool
	 */
	public function delete_shared_extra( int $extra_id ): bool {
		$this->db->delete_where( 'SERVICEGLOBALEXTRA', [ 'global_extra_id' => $extra_id ], [ '%d' ] );
		return $this->db->remove_row( 'GLOBALEXTRA', 'id', $extra_id, [ '%d' ] );
	}

	/**
	 * Get a single shared extra by ID.
	 *
	 * @param int $extra_id
	 * @return object|null
	 */
	public function get_shared_extra( int $extra_id ) {
		return $this->db->get_row( 'GLOBALEXTRA', $extra_id );
	}

	/**
	 * Get all shared extras.
	 *
	 * @return array
	 */
	public function get_all_shared_extras(): array {
		$table = $this->db->get_table_name( 'GLOBALEXTRA' );
		return $this->db->get_results_raw( "SELECT * FROM {$table} ORDER BY id ASC" ) ?: [];
	}

	// ──────────────────────── SERVICE LINK MANAGEMENT ────────────────────────

	/**
	 * Link a service to a shared extra.
	 *
	 * @param int $service_id
	 * @param int $extra_id
	 * @return int|false
	 */
	public function link_service( int $service_id, int $extra_id ) {
		return $this->db->insert_row(
			'SERVICEGLOBALEXTRA',
			[ 'service_id' => $service_id, 'global_extra_id' => $extra_id ],
			[ '%d', '%d' ]
		);
	}

	/**
	 * Unlink a service from a shared extra.
	 *
	 * @param int $service_id
	 * @param int $extra_id
	 * @return bool
	 */
	public function unlink_service( int $service_id, int $extra_id ): bool {
		return $this->db->delete_where(
			'SERVICEGLOBALEXTRA',
			[ 'service_id' => $service_id, 'global_extra_id' => $extra_id ],
			[ '%d', '%d' ]
		);
	}

	/**
	 * Get all services linked to a shared extra.
	 *
	 * @param int $extra_id
	 * @return array  Each element has service_id and service_name.
	 */
	public function get_services_for_extra( int $extra_id ): array {
		$map   = $this->db->get_table_name( 'SERVICEGLOBALEXTRA' );
		$svcs  = $this->db->get_table_name( 'SERVICE' );
		return $this->db->get_results_raw(
			$this->db->prepare_sql(
				"SELECT sge.service_id, s.service_name
				 FROM {$map} sge
				 INNER JOIN {$svcs} s ON s.id = sge.service_id
				 WHERE sge.global_extra_id = %d
				 ORDER BY s.service_name ASC",
				$extra_id
			)
		) ?: [];
	}

	/**
	 * Get all shared extras linked to a service.
	 *
	 * @param int $service_id
	 * @return array
	 */
	public function get_extras_for_service( int $service_id ): array {
		$map    = $this->db->get_table_name( 'SERVICEGLOBALEXTRA' );
		$extras = $this->db->get_table_name( 'GLOBALEXTRA' );
		return $this->db->get_results_raw(
			$this->db->prepare_sql(
				"SELECT ge.*
				 FROM {$extras} ge
				 INNER JOIN {$map} sge ON sge.global_extra_id = ge.id
				 WHERE sge.service_id = %d
				 ORDER BY ge.id ASC",
				$service_id
			)
		) ?: [];
	}

	// ────────────────────────── CONSUMPTION ANALYTICS ────────────────────────

	/**
	 * Get stock consumption per linked service for a shared extra.
	 *
	 * Implements the per-service traceability requirement from §1.3:
	 * "stock consumption must be traceable per linked service."
	 *
	 * @param int         $extra_id
	 * @param string|null $from_date  Inclusive start date (Y-m-d), or null for all.
	 * @param string|null $to_date    Inclusive end date (Y-m-d), or null for all.
	 * @return array  Each row has service_id, service_name, booking_date, slots_consumed.
	 */
	public function get_consumption_by_service( int $extra_id, $from_date = null, $to_date = null ): array {
		$esc_table = $this->db->get_table_name( 'EXTRASLOTCOUNT' );
		$svc_table = $this->db->get_table_name( 'SERVICE' );

		$sql = $this->db->prepare_sql(
			"SELECT esc.service_id,
			        s.service_name,
			        esc.booking_date,
			        SUM(esc.slots_booked) AS slots_consumed
			 FROM {$esc_table} esc
			 LEFT JOIN {$svc_table} s ON s.id = esc.service_id
			 WHERE esc.extra_svc_id = %d
			   AND esc.extra_type   = 'global'
			   AND esc.is_active    = 1",
			$extra_id
		);

		if ( $from_date ) {
			$sql .= $this->db->prepare_sql( " AND esc.booking_date >= %s", $from_date );
		}
		if ( $to_date ) {
			$sql .= $this->db->prepare_sql( " AND esc.booking_date <= %s", $to_date );
		}

		$sql .= ' GROUP BY esc.service_id, esc.booking_date ORDER BY esc.booking_date ASC, esc.service_id ASC';

		return $this->db->get_results_raw( $sql ) ?: [];
	}

	/**
	 * Get the current total usage of a shared extra on a given date
	 * (delegates to the existing pooled-usage helper on BM_DBhandler).
	 *
	 * @param int    $extra_id
	 * @param string $date
	 * @return int
	 */
	public function get_usage_on_date( int $extra_id, string $date ): int {
		return $this->db->get_global_extra_pooled_usage( $extra_id, $date );
	}
}
