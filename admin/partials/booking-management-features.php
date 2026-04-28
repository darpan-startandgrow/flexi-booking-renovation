<?php
/**
 * Admin partial: Booking Features management page.
 *
 * Provides a tabbed UI for all six FlexiBooking feature types:
 *  Tab 1 — Service Options  (§1.7)
 *  Tab 2 — Service Extras   (§1.4)
 *  Tab 3 — Bundles          (§1.9)
 *  Tab 4 — Virtual Services (§1.8)
 *  Tab 5 — Resource Pools   (§1.6)
 *  Tab 6 — Service Chains   (§1.5)
 *
 * CRUD operations are performed via the bm-features/v1 REST API.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$dbhandler = new BM_DBhandler();
$services  = $dbhandler->get_all_result( 'SERVICE', array( 'id', 'service_name' ), array( 'service_status' => 1 ), 'results' );
if ( empty( $services ) ) {
	$services = array();
}
?>
<div class="wrap bm-features-wrap">
	<h1 class="wp-heading-inline"><?php esc_html_e( 'Booking Features', 'service-booking' ); ?></h1>
	<p><?php esc_html_e( 'Manage advanced booking features: options, add-on services, bundles, virtual services, resource pools, and service chains.', 'service-booking' ); ?></p>
	<p class="description">
		<?php
		printf(
			/* translators: %s: URL to Shared Extras page */
			wp_kses( __( 'Shared Extras (§1.3) are managed on a separate page: <a href="%s">Shared Extras &rarr;</a>', 'service-booking' ), array( 'a' => array( 'href' => array() ) ) ),
			esc_url( admin_url( 'admin.php?page=bm_shared_extras' ) )
		);
		?>
	</p>

	<ul class="bm-features-tabs">
		<li class="bm-tab-active"><a href="#" data-tab="options"><?php esc_html_e( 'Service Options', 'service-booking' ); ?></a></li>
		<li><a href="#" data-tab="extras"><?php esc_html_e( 'Service Extras', 'service-booking' ); ?></a></li>
		<li><a href="#" data-tab="bundles"><?php esc_html_e( 'Bundles', 'service-booking' ); ?></a></li>
		<li><a href="#" data-tab="virtual"><?php esc_html_e( 'Virtual Services', 'service-booking' ); ?></a></li>
		<li><a href="#" data-tab="pools"><?php esc_html_e( 'Resource Pools', 'service-booking' ); ?></a></li>
		<li><a href="#" data-tab="chains"><?php esc_html_e( 'Service Chains', 'service-booking' ); ?></a></li>
	</ul>

	<!-- ─── TAB 1: SERVICE OPTIONS ────────────────────────────────────── -->
	<div id="bm-tab-options" class="bm-tab-panel bm-tab-panel-active">
		<p class="bm-features-notice">
			<?php esc_html_e( 'Service Options let customers choose a variant (e.g. "light" or "full" package) during booking. Each option set belongs to one service; selecting a value adjusts the booking price.', 'service-booking' ); ?>
		</p>
		<p class="bm-features-notice" style="background:#e8f4fd;border-left:4px solid #2271b1;padding:8px 12px;">
			<strong><?php esc_html_e( 'Note:', 'service-booking' ); ?></strong>
			<?php esc_html_e( 'Service Options share the base service\'s availability — they do not have independent time slots or capacity. An option can adjust the booking price but the service\'s own date, slot, and bookability rules always apply.', 'service-booking' ); ?>
		</p>
		<div class="bm-features-form">
			<strong><?php esc_html_e( 'Filter by Service', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Service', 'service-booking' ); ?></label>
				<select id="bm-options-service-id">
					<option value=""><?php esc_html_e( '— Select Service —', 'service-booking' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?> (ID: <?php echo (int) $svc->id; ?>)</option>
					<?php endforeach; ?>
				</select>
				<button id="bm-options-load" class="button"><?php esc_html_e( 'Load Option Sets', 'service-booking' ); ?></button>
			</div>
		</div>

		<div id="bm-options-list" style="margin-top:16px;"></div>

		<div class="bm-features-form" style="margin-top:20px;">
			<strong><?php esc_html_e( 'Add New Option Set', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Service', 'service-booking' ); ?></label>
				<select id="bm-option-set-service-id-ref">
					<option value=""><?php esc_html_e( '— Select Service —', 'service-booking' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?> (ID: <?php echo (int) $svc->id; ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Name', 'service-booking' ); ?> <span class="bm-required-star">*</span></label>
				<input type="text" id="bm-option-set-name" placeholder="<?php esc_attr_e( 'e.g. Package Type', 'service-booking' ); ?>" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Description', 'service-booking' ); ?></label>
				<textarea id="bm-option-set-desc" placeholder="<?php esc_attr_e( 'Optional description shown to customers', 'service-booking' ); ?>"></textarea>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Required', 'service-booking' ); ?></label>
				<input type="checkbox" id="bm-option-set-required" checked />
			</div>
			<button id="bm-add-option-set" class="button button-primary">
				<?php esc_html_e( 'Add Option Set', 'service-booking' ); ?>
			</button>
		</div>
	</div>

	<!-- ─── TAB 2: SERVICE EXTRAS ─────────────────────────────────────── -->
	<div id="bm-tab-extras" class="bm-tab-panel">
		<p class="bm-features-notice">
			<?php esc_html_e( 'Service Extras allow a standalone service to be sold as an add-on during the booking of another service. The add-on retains its own capacity and identity.', 'service-booking' ); ?>
		</p>
		<div class="bm-features-form">
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Parent Service', 'service-booking' ); ?></label>
				<select id="bm-sae-parent-id">
					<option value=""><?php esc_html_e( '— Select Parent Service —', 'service-booking' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?> (ID: <?php echo (int) $svc->id; ?>)</option>
					<?php endforeach; ?>
				</select>
				<button id="bm-sae-load" class="button"><?php esc_html_e( 'Load Add-ons', 'service-booking' ); ?></button>
			</div>
		</div>

		<div id="bm-sae-list" style="margin-top:16px;"></div>

		<div class="bm-features-form" style="margin-top:20px;">
			<strong><?php esc_html_e( 'Add Addon Relationship', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Addon Service', 'service-booking' ); ?></label>
				<select id="bm-sae-addon-id">
					<option value=""><?php esc_html_e( '— Select Addon Service —', 'service-booking' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?> (ID: <?php echo (int) $svc->id; ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Price Override', 'service-booking' ); ?></label>
				<input type="number" id="bm-sae-price-override" step="0.01" placeholder="<?php esc_attr_e( 'Leave blank to use addon service price', 'service-booking' ); ?>" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Show on Frontend', 'service-booking' ); ?></label>
				<input type="checkbox" id="bm-sae-frontend" checked />
			</div>
			<button id="bm-sae-add" class="button button-primary"><?php esc_html_e( 'Add Addon', 'service-booking' ); ?></button>
		</div>
	</div>

	<!-- ─── TAB 3: BUNDLES ────────────────────────────────────────────── -->
	<div id="bm-tab-bundles" class="bm-tab-panel">
		<p class="bm-features-notice">
			<?php esc_html_e( 'Bundles are commercial products composed of multiple services priced as a group, with an optional discount.', 'service-booking' ); ?>
		</p>
		<button id="bm-load-bundles" class="button"><?php esc_html_e( 'Load Bundles', 'service-booking' ); ?></button>
		<div id="bm-bundles-list" style="margin-top:16px;"></div>

		<div class="bm-features-form" style="margin-top:20px;">
			<strong><?php esc_html_e( 'Create New Bundle', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Bundle Name', 'service-booking' ); ?> <span class="bm-required-star">*</span></label>
				<input type="text" id="bm-bundle-name" placeholder="<?php esc_attr_e( 'e.g. Weekend Getaway', 'service-booking' ); ?>" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Description', 'service-booking' ); ?></label>
				<textarea id="bm-bundle-desc"></textarea>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Bundle Price', 'service-booking' ); ?> <span class="bm-required-star">*</span></label>
				<input type="number" id="bm-bundle-price" step="0.01" min="0" placeholder="<?php esc_attr_e( 'e.g. 149.00', 'service-booking' ); ?>" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Discount Type', 'service-booking' ); ?></label>
				<select id="bm-bundle-discount-type">
					<option value=""><?php esc_html_e( 'None', 'service-booking' ); ?></option>
					<option value="percent"><?php esc_html_e( 'Percent (%)', 'service-booking' ); ?></option>
					<option value="fixed"><?php esc_html_e( 'Fixed Amount', 'service-booking' ); ?></option>
				</select>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Discount Value', 'service-booking' ); ?></label>
				<input type="number" id="bm-bundle-discount-value" step="0.01" value="0" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Status', 'service-booking' ); ?></label>
				<select id="bm-bundle-status">
					<option value="1"><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
					<option value="0"><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
				</select>
			</div>
			<button id="bm-add-bundle" class="button button-primary"><?php esc_html_e( 'Create Bundle', 'service-booking' ); ?></button>
		</div>
	</div>

	<!-- ─── TAB 4: VIRTUAL SERVICES ──────────────────────────────────── -->
	<div id="bm-tab-virtual" class="bm-tab-panel">
		<p class="bm-features-notice">
			<?php esc_html_e( 'Virtual Services represent a service that is bookable only when ALL its component real services are available. Booking a virtual service blocks all its components.', 'service-booking' ); ?>
		</p>
		<p class="bm-features-notice" style="background:#fff8e1;border-left:4px solid #f59e0b;padding:8px 12px;">
			<strong><?php esc_html_e( 'Two-layer model:', 'service-booking' ); ?></strong>
			<?php esc_html_e( 'Step 1 — Create the Virtual Service entity (name + description). Step 2 — Use the Components sub-table to attach two or more real component services. The VS Record ID is the internal ID of the virtual service entity; component services are the real services whose availability it depends on.', 'service-booking' ); ?>
		</p>
		<button id="bm-vs-load" class="button"><?php esc_html_e( 'Load Virtual Services', 'service-booking' ); ?></button>
		<div id="bm-vs-list" style="margin-top:16px;"></div>

		<div class="bm-features-form" style="margin-top:20px;">
			<strong><?php esc_html_e( 'Create Virtual Service', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Name', 'service-booking' ); ?> <span class="bm-required-star">*</span></label>
				<input type="text" id="bm-vs-name" placeholder="<?php esc_attr_e( 'Virtual service display name', 'service-booking' ); ?>" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Description', 'service-booking' ); ?></label>
				<textarea id="bm-vs-desc"></textarea>
			</div>
			<p class="description" style="margin-top:8px;">
				<?php esc_html_e( 'After creation, open the Components sub-table to attach 2 or more real component services.', 'service-booking' ); ?>
			</p>
			<button id="bm-vs-add" class="button button-primary"><?php esc_html_e( 'Create Virtual Service', 'service-booking' ); ?></button>
		</div>
	</div>

	<!-- ─── TAB 5: RESOURCE POOLS ────────────────────────────────────── -->
	<div id="bm-tab-pools" class="bm-tab-panel">
		<p class="bm-features-notice">
			<?php esc_html_e( 'Resource Pools define a shared capacity bucket consumed by multiple services. Once the pool is exhausted, all linked services become unavailable.', 'service-booking' ); ?>
		</p>
		<button id="bm-pools-load" class="button"><?php esc_html_e( 'Load Pools', 'service-booking' ); ?></button>
		<div id="bm-pools-list" style="margin-top:16px;"></div>

		<div class="bm-features-form" style="margin-top:20px;">
			<strong><?php esc_html_e( 'Create Resource Pool', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Pool Name', 'service-booking' ); ?> <span class="bm-required-star">*</span></label>
				<input type="text" id="bm-pool-name" placeholder="<?php esc_attr_e( 'e.g. Guides Pool', 'service-booking' ); ?>" />
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Description', 'service-booking' ); ?></label>
				<textarea id="bm-pool-desc"></textarea>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Total Capacity', 'service-booking' ); ?> <span class="bm-required-star">*</span></label>
				<input type="number" id="bm-pool-capacity" min="1" placeholder="<?php esc_attr_e( 'e.g. 5', 'service-booking' ); ?>" />
			</div>
			<button id="bm-add-pool" class="button button-primary"><?php esc_html_e( 'Create Pool', 'service-booking' ); ?></button>
		</div>
	</div>

	<!-- ─── TAB 6: SERVICE CHAINS ─────────────────────────────────────── -->
	<div id="bm-tab-chains" class="bm-tab-panel">
		<p class="bm-features-notice">
			<?php esc_html_e( 'Service Chains define mutual exclusion rules: when Service A is booked on a date, Service B is blocked, and vice versa.', 'service-booking' ); ?>
		</p>
		<button id="bm-chains-load" class="button"><?php esc_html_e( 'Load Chains', 'service-booking' ); ?></button>
		<div id="bm-chains-list" style="margin-top:16px;"></div>

		<div class="bm-features-form" style="margin-top:20px;">
			<strong><?php esc_html_e( 'Add Service Chain Rule', 'service-booking' ); ?></strong>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Service A', 'service-booking' ); ?></label>
				<select id="bm-chain-svc-a">
					<option value=""><?php esc_html_e( '— Select Service —', 'service-booking' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?> (ID: <?php echo (int) $svc->id; ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Service B', 'service-booking' ); ?></label>
				<select id="bm-chain-svc-b">
					<option value=""><?php esc_html_e( '— Select Service —', 'service-booking' ); ?></option>
					<?php foreach ( $services as $svc ) : ?>
						<option value="<?php echo esc_attr( $svc->id ); ?>"><?php echo esc_html( $svc->service_name ); ?> (ID: <?php echo (int) $svc->id; ?>)</option>
					<?php endforeach; ?>
				</select>
			</div>
			<div class="bm-form-row">
				<label><?php esc_html_e( 'Chain Type', 'service-booking' ); ?></label>
				<select id="bm-chain-type">
					<option value="mutual_exclusion"><?php esc_html_e( 'Mutual Exclusion', 'service-booking' ); ?></option>
				</select>
			</div>
			<button id="bm-add-chain" class="button button-primary"><?php esc_html_e( 'Add Chain Rule', 'service-booking' ); ?></button>
			<p id="bm-chain-self-error" class="bm-features-notice error" style="display:none;color:#c0392b;margin-top:8px;">
				<?php esc_html_e( 'A service cannot be chained to itself. Please select two different services.', 'service-booking' ); ?>
			</p>
		</div>
	</div>

</div><!-- .bm-features-wrap -->
