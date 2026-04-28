<?php
/**
 * Features REST API
 *
 * Unified REST API for admin management of all Part-1 spec features:
 *   §1.4 Product as Extra  → /features/service-extras
 *   §1.5 Service Chaining  → /features/chains
 *   §1.6 Resource Pools    → /features/resource-pools
 *   §1.7 Service Options   → /features/option-sets, /features/option-values
 *   §1.8 Virtual Services  → /features/virtual-services
 *   §1.9 Bundles           → /features/bundles
 *
 * Namespace: bm-features/v1
 * All endpoints require manage_options capability.
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @since      3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Booking_Features_REST {

	const NAMESPACE = 'bm-features/v1';

	/** @var BM_SharedExtra */
	private $shared_extra;

	/** @var BM_ResourcePool */
	private $resource_pool;

	/** @var BM_ServiceChain */
	private $service_chain;

	/** @var BM_ServiceOptions */
	private $service_options;

	/** @var BM_Bundle */
	private $bundle;

	/** @var BM_VirtualService */
	private $virtual_service;

	/** @var BM_ServiceAsExtra */
	private $service_as_extra;

	public function __construct() {
		$this->shared_extra     = new BM_SharedExtra();
		$this->resource_pool    = new BM_ResourcePool();
		$this->service_chain    = new BM_ServiceChain();
		$this->service_options  = new BM_ServiceOptions();
		$this->bundle           = new BM_Bundle();
		$this->virtual_service  = new BM_VirtualService();
		$this->service_as_extra = new BM_ServiceAsExtra();

		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	// ─────────────────────────── ROUTE REGISTRATION ──────────────────────────

	public function register_routes(): void {
		$ns = self::NAMESPACE;

		// ── §1.3 Shared Inventory Extras ──────────────────────────────────────
		register_rest_route( $ns, '/shared-extras', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_shared_extras' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_shared_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/shared-extras/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_shared_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_shared_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_shared_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/shared-extras/(?P<id>\d+)/services', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_shared_extra_services' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'link_service_to_shared_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'unlink_service_from_shared_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/shared-extras/(?P<id>\d+)/consumption', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_shared_extra_consumption' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/shared-extras/service/(?P<service_id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_extras_for_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );

		// ── Resource Pools ────────────────────────────────────────────────────
		register_rest_route( $ns, '/resource-pools', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_resource_pools' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_resource_pool' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/resource-pools/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_resource_pool' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_resource_pool' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_resource_pool' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/resource-pools/(?P<id>\d+)/availability', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_pool_availability' ],
				'permission_callback' => [ $this, 'admin_permission' ],
				'args'                => [
					'date' => [ 'required' => true, 'type' => 'string' ],
				],
			],
		] );
		register_rest_route( $ns, '/resource-pools/(?P<id>\d+)/services', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_pool_services' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'link_service_to_pool' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'unlink_service_from_pool' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		// Alias for JS compatibility: GET /resource-pools/{id}/linked-services
		register_rest_route( $ns, '/resource-pools/(?P<id>\d+)/linked-services', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_pool_services' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );

		// ── Service Chains ────────────────────────────────────────────────────
		register_rest_route( $ns, '/chains', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_chains' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_chain' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/chains/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_chain' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_chain' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_chain' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/chains/service/(?P<service_id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_chains_for_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );

		// ── Option Sets ───────────────────────────────────────────────────────
		register_rest_route( $ns, '/option-sets', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_option_set' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/option-sets/service/(?P<service_id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_option_sets_for_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/option-sets/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_option_set' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_option_set' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_option_set' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/option-sets/(?P<set_id>\d+)/values', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_option_values' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_option_value' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/option-values/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_option_value' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_option_value' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/option-sets/service/(?P<service_id>\d+)/tree', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_option_tree' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );

		// ── Bundles ───────────────────────────────────────────────────────────
		register_rest_route( $ns, '/bundles', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_bundles' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_bundle' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/bundles/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_bundle' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_bundle' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_bundle' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/bundles/(?P<bundle_id>\d+)/items', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_bundle_items' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'add_bundle_item' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/bundle-items/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_bundle_item' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'remove_bundle_item' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );

		// ── Virtual Services ──────────────────────────────────────────────────
		register_rest_route( $ns, '/virtual-services', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'list_virtual_services' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_virtual_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/virtual-services/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_virtual_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_virtual_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_virtual_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/virtual-services/(?P<id>\d+)/components', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_virtual_components' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'add_virtual_component' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/virtual-service-components/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'remove_virtual_component' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/virtual-services/(?P<id>\d+)/availability', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'check_virtual_service_availability' ],
				'permission_callback' => [ $this, 'admin_permission' ],
				'args'                => [
					'date' => [ 'required' => true, 'type' => 'string' ],
				],
			],
		] );

		// ── Service as Extra ──────────────────────────────────────────────────
		register_rest_route( $ns, '/service-extras', [
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => [ $this, 'create_service_as_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/service-extras/service/(?P<service_id>\d+)', [
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => [ $this, 'get_addons_for_service' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
		register_rest_route( $ns, '/service-extras/(?P<id>\d+)', [
			[
				'methods'             => WP_REST_Server::EDITABLE,
				'callback'            => [ $this, 'update_service_as_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => [ $this, 'delete_service_as_extra' ],
				'permission_callback' => [ $this, 'admin_permission' ],
			],
		] );
	}

	// ─────────────────────────── PERMISSION ──────────────────────────────────

	public function admin_permission(): bool {
		return current_user_can( 'manage_options' );
	}

	// ──────────────────── §1.3 SHARED EXTRA HANDLERS ─────────────────────────

	public function list_shared_extras( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->shared_extra->get_all_shared_extras();
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_shared_extra( WP_REST_Request $request ): WP_REST_Response {
		$extra = $this->shared_extra->get_shared_extra( (int) $request['id'] );
		if ( ! $extra ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Not found' ], 404 );
		}
		$extra->services = $this->shared_extra->get_services_for_extra( (int) $extra->id );
		return new WP_REST_Response( [ 'success' => true, 'data' => $extra ] );
	}

	public function create_shared_extra( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->shared_extra->create_shared_extra(
			(string) ( $params['name'] ?? '' ),
			(float)  ( $params['price'] ?? 0.0 ),
			(int)    ( $params['max_capacity'] ?? 1 ),
			(string) ( $params['description'] ?? '' ),
			(bool)   ( $params['is_visible_frontend'] ?? true )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create shared extra' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_shared_extra( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->shared_extra->update_shared_extra( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_shared_extra( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->shared_extra->delete_shared_extra( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_shared_extra_services( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->shared_extra->get_services_for_extra( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function link_service_to_shared_extra( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->shared_extra->link_service(
			(int) ( $params['service_id'] ?? 0 ),
			(int) $request['id']
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to link service' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function unlink_service_from_shared_extra( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$deleted = $this->shared_extra->unlink_service(
			(int) ( $params['service_id'] ?? 0 ),
			(int) $request['id']
		);
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_shared_extra_consumption( WP_REST_Request $request ): WP_REST_Response {
		$from = $request->get_param( 'from' );
		$to   = $request->get_param( 'to' );
		$data = $this->shared_extra->get_consumption_by_service(
			(int) $request['id'],
			$from ?: null,
			$to ?: null
		);
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_extras_for_service( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->shared_extra->get_extras_for_service( (int) $request['service_id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	// ──────────────────────── RESOURCE POOL HANDLERS ─────────────────────────

	public function list_resource_pools( WP_REST_Request $request ): WP_REST_Response {
		$status = $request->get_param( 'status' );
		$data   = $this->resource_pool->get_all_pools( null !== $status ? (int) $status : 1 );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_resource_pool( WP_REST_Request $request ): WP_REST_Response {
		$pool = $this->resource_pool->get_pool( (int) $request['id'] );
		if ( ! $pool ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Not found' ], 404 );
		}
		$pool->services = $this->resource_pool->get_services_in_pool( (int) $pool->id );
		return new WP_REST_Response( [ 'success' => true, 'data' => $pool ] );
	}

	public function create_resource_pool( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->resource_pool->create_pool(
			(string) ( $params['name'] ?? '' ),
			(int) ( $params['total_capacity'] ?? 1 ),
			(string) ( $params['allocation_rule'] ?? 'shared' ),
			(string) ( $params['description'] ?? '' )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create resource pool' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_resource_pool( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->resource_pool->update_pool( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_resource_pool( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->resource_pool->delete_pool( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_pool_availability( WP_REST_Request $request ): WP_REST_Response {
		$remaining = $this->resource_pool->get_pool_availability(
			(int) $request['id'],
			(string) $request->get_param( 'date' )
		);
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'remaining' => $remaining ] ] );
	}

	public function list_pool_services( WP_REST_Request $request ): WP_REST_Response {
		$services = $this->resource_pool->get_services_in_pool( (int) $request['id'] );
		// Normalize field names for JS compatibility: map consumption_per_booking → capacity_used.
		$result = array_map( function ( $row ) {
			return array(
				'id'           => isset( $row->id ) ? (int) $row->id : 0,
				'service_id'   => (int) $row->service_id,
				'capacity_used' => isset( $row->consumption_per_booking ) ? (int) $row->consumption_per_booking : 1,
			);
		}, $services );
		return new WP_REST_Response( [ 'success' => true, 'data' => $result ] );
	}

	public function link_service_to_pool( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->resource_pool->link_service_to_pool(
			(int) ( $params['service_id'] ?? 0 ),
			(int) $request['id'],
			(int) ( $params['consumption_per_booking'] ?? 1 )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to link service' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function unlink_service_from_pool( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$deleted = $this->resource_pool->unlink_service_from_pool(
			(int) ( $params['service_id'] ?? 0 ),
			(int) $request['id']
		);
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	// ──────────────────────── CHAIN HANDLERS ─────────────────────────────────

	public function list_chains( WP_REST_Request $request ): WP_REST_Response {
		$status = $request->get_param( 'status' );
		$data   = $this->service_chain->get_all_chains( null !== $status ? (int) $status : 1 );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_chain( WP_REST_Request $request ): WP_REST_Response {
		$chain = $this->service_chain->get_chain( (int) $request['id'] );
		if ( ! $chain ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Not found' ], 404 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $chain ] );
	}

	public function create_chain( WP_REST_Request $request ): WP_REST_Response {
		$params     = $request->get_json_params() ?: $request->get_body_params();
		$service_a  = (int) ( $params['service_a_id'] ?? 0 );
		$service_b  = (int) ( $params['service_b_id'] ?? 0 );
		// P4 — server-side self-chain guard.
		if ( $service_a > 0 && $service_a === $service_b ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'A service cannot be chained to itself.' ], 422 );
		}
		$id = $this->service_chain->create_chain(
			$service_a,
			$service_b,
			(string) ( $params['chain_type'] ?? 'mutual_exclusion' )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create chain' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_chain( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->service_chain->update_chain( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_chain( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->service_chain->delete_chain( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_chains_for_service( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->service_chain->get_chains_for_service( (int) $request['service_id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	// ─────────────────────── OPTION SET HANDLERS ─────────────────────────────

	public function get_option_sets_for_service( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->service_options->get_option_sets_for_service( (int) $request['service_id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_option_set( WP_REST_Request $request ): WP_REST_Response {
		$set = $this->service_options->get_option_set( (int) $request['id'] );
		if ( ! $set ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Not found' ], 404 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $set ] );
	}

	public function create_option_set( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->service_options->create_option_set(
			(int) ( $params['service_id'] ?? 0 ),
			(string) ( $params['name'] ?? '' ),
			(string) ( $params['description'] ?? '' ),
			(bool) ( $params['is_required'] ?? true )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create option set' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_option_set( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->service_options->update_option_set( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_option_set( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->service_options->delete_option_set( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_option_values( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->service_options->get_values_for_option_set( (int) $request['set_id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function create_option_value( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->service_options->create_option_value(
			(int) $request['set_id'],
			(string) ( $params['name'] ?? '' ),
			(string) ( $params['description'] ?? '' ),
			(float) ( $params['price_modifier'] ?? 0.0 ),
			isset( $params['price_override'] ) ? (float) $params['price_override'] : null,
			(bool) ( $params['is_default'] ?? false )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create option value' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_option_value( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->service_options->update_option_value( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_option_value( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->service_options->delete_option_value( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_option_tree( WP_REST_Request $request ): WP_REST_Response {
		$tree = $this->service_options->get_option_tree_for_service( (int) $request['service_id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $tree ] );
	}

	// ─────────────────────────── BUNDLE HANDLERS ─────────────────────────────

	public function list_bundles( WP_REST_Request $request ): WP_REST_Response {
		$status = $request->get_param( 'status' );
		$data   = $this->bundle->get_all_bundles( null !== $status ? (int) $status : 1 );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_bundle( WP_REST_Request $request ): WP_REST_Response {
		$bundle = $this->bundle->get_bundle_with_items( (int) $request['id'] );
		if ( ! $bundle ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Not found' ], 404 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $bundle ] );
	}

	public function create_bundle( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->bundle->create_bundle(
			(string) ( $params['name'] ?? '' ),
			(string) ( $params['description'] ?? '' ),
			isset( $params['discount_type'] ) ? (string) $params['discount_type'] : null,
			(float) ( $params['discount_value'] ?? 0.0 ),
			(float) ( $params['price'] ?? 0.0 ),
			(int) ( $params['status'] ?? 1 )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create bundle' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_bundle( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->bundle->update_bundle( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_bundle( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->bundle->delete_bundle( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_bundle_items( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->bundle->get_bundle_items( (int) $request['bundle_id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function add_bundle_item( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->bundle->add_bundle_item(
			(int) $request['bundle_id'],
			(int) ( $params['service_id'] ?? 0 ),
			(int) ( $params['quantity'] ?? 1 ),
			(bool) ( $params['is_optional'] ?? false ),
			(int) ( $params['item_position'] ?? 0 )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to add bundle item' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_bundle_item( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->bundle->update_bundle_item( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function remove_bundle_item( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->bundle->remove_bundle_item( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	// ──────────────────── VIRTUAL SERVICE HANDLERS ───────────────────────────

	public function list_virtual_services( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->virtual_service->get_all_virtual_services();
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function get_virtual_service( WP_REST_Request $request ): WP_REST_Response {
		$vs = $this->virtual_service->get_virtual_service_with_components( (int) $request['id'] );
		if ( ! $vs ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Not found' ], 404 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => $vs ] );
	}

	public function create_virtual_service( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		// P3 — service_id is now optional. Default to 0 when not provided.
		$id     = $this->virtual_service->create_virtual_service(
			(int) ( $params['service_id'] ?? 0 ),
			(string) ( $params['name'] ?? '' ),
			(string) ( $params['description'] ?? '' )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create virtual service' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_virtual_service( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->virtual_service->update_virtual_service( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_virtual_service( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->virtual_service->delete_virtual_service( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function get_virtual_components( WP_REST_Request $request ): WP_REST_Response {
		$data = $this->virtual_service->get_components( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function add_virtual_component( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->virtual_service->add_component(
			(int) $request['id'],
			(int) ( $params['component_service_id'] ?? 0 ),
			(int) ( $params['component_position'] ?? 0 )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to add component' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function remove_virtual_component( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->virtual_service->remove_component( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}

	public function check_virtual_service_availability( WP_REST_Request $request ): WP_REST_Response {
		$available = $this->virtual_service->is_virtual_service_available(
			(int) $request['id'],
			(string) $request->get_param( 'date' )
		);
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'available' => $available ] ] );
	}

	// ─────────────────── SERVICE-AS-EXTRA HANDLERS ───────────────────────────

	public function get_addons_for_service( WP_REST_Request $request ): WP_REST_Response {
		$frontend_only = (bool) $request->get_param( 'frontend_only' );
		$data          = $this->service_as_extra->get_addons_for_service( (int) $request['service_id'], $frontend_only );
		return new WP_REST_Response( [ 'success' => true, 'data' => $data ] );
	}

	public function create_service_as_extra( WP_REST_Request $request ): WP_REST_Response {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$id     = $this->service_as_extra->create_service_as_extra(
			(int) ( $params['parent_service_id'] ?? 0 ),
			(int) ( $params['addon_service_id'] ?? 0 ),
			isset( $params['price_override'] ) ? (float) $params['price_override'] : null,
			(bool) ( $params['is_visible_frontend'] ?? true )
		);
		if ( ! $id ) {
			return new WP_REST_Response( [ 'success' => false, 'message' => 'Failed to create service-as-extra' ], 500 );
		}
		return new WP_REST_Response( [ 'success' => true, 'data' => [ 'id' => $id ] ], 201 );
	}

	public function update_service_as_extra( WP_REST_Request $request ): WP_REST_Response {
		$params  = $request->get_json_params() ?: $request->get_body_params();
		$updated = $this->service_as_extra->update_service_as_extra( (int) $request['id'], $params );
		return new WP_REST_Response( [ 'success' => $updated ] );
	}

	public function delete_service_as_extra( WP_REST_Request $request ): WP_REST_Response {
		$deleted = $this->service_as_extra->delete_service_as_extra( (int) $request['id'] );
		return new WP_REST_Response( [ 'success' => $deleted ] );
	}
}
