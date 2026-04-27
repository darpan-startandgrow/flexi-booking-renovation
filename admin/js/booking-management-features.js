/* global bmFeaturesData, jQuery */
/**
 * FlexiBooking — Booking Features admin page JS
 *
 * Handles 6 feature tabs:
 *  1. Service Options  (§1.7)
 *  2. Service Extras   (§1.4)
 *  3. Bundles          (§1.9)
 *  4. Virtual Services (§1.8)
 *  5. Resource Pools   (§1.6)
 *  6. Service Chains   (§1.5)
 *
 * Uses bm-features/v1 REST namespace.
 */
(function ($) {
'use strict';

var restUrl   = bmFeaturesData.restUrl;
var restNonce = bmFeaturesData.nonce;

// ─────────────────────── TAB NAVIGATION ─────────────────────────────

$( document ).on( 'click', '.bm-features-tabs li a', function (e) {
e.preventDefault();
var target = $( this ).data( 'tab' );
$( '.bm-features-tabs li' ).removeClass( 'bm-tab-active' );
$( this ).closest( 'li' ).addClass( 'bm-tab-active' );
$( '.bm-tab-panel' ).removeClass( 'bm-tab-panel-active' );
$( '#bm-tab-' + target ).addClass( 'bm-tab-panel-active' );
});

// ─────────────────────── REST HELPERS ────────────────────────────────

function bmRestGet( path ) {
return $.ajax({
url:         restUrl + '/' + path.replace( /^\//, '' ),
method:      'GET',
beforeSend:  function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', restNonce ); }
});
}

function bmRestPost( path, data ) {
return $.ajax({
url:         restUrl + '/' + path.replace( /^\//, '' ),
method:      'POST',
contentType: 'application/json',
data:        JSON.stringify( data ),
beforeSend:  function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', restNonce ); }
});
}

function bmRestDelete( path, data ) {
var opts = {
url:        restUrl + '/' + path.replace( /^\//, '' ),
method:     'DELETE',
beforeSend: function ( xhr ) { xhr.setRequestHeader( 'X-WP-Nonce', restNonce ); }
};
if ( data ) {
opts.contentType = 'application/json';
opts.data        = JSON.stringify( data );
}
return $.ajax( opts );
}

function bmSpinner() {
return '<span class="bm-spinner"></span>';
}

// ═══════════════════════ TAB 1: SERVICE OPTIONS ══════════════════════

function bmLoadOptionSets() {
var $wrap = $( '#bm-options-list' );
var svcId = $( '#bm-options-service-id' ).val();
if ( ! svcId ) { $wrap.html( '<p>Select a service above to manage its option sets.</p>' ); return; }
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'option-sets/service/' + encodeURIComponent( svcId ) ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No option sets yet. Add one below.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Required</th><th>Values</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, set ) {
html += '<tr data-set-id="' + set.id + '">';
html += '<td>' + set.id + '</td>';
html += '<td>' + $( '<span>' ).text( set.name ).html() + '</td>';
html += '<td>' + ( set.is_required ? 'Yes' : 'No' ) + '</td>';
html += '<td><button class="button button-small bm-options-show-values" data-set-id="' + set.id + '" data-set-name="' + $('<span>').text(set.name).html() + '">Values</button></td>';
html += '<td><button class="button button-small button-link-delete bm-options-delete-set" data-set-id="' + set.id + '">Delete</button></td>';
html += '</tr>';
html += '<tr class="bm-child-rows" id="bm-option-values-' + set.id + '" style="display:none"><td colspan="5">';
html += '<h4>Option Values for &ldquo;' + $( '<span>' ).text( set.name ).html() + '&rdquo;</h4>';
html += '<div id="bm-values-list-' + set.id + '"></div>';
html += '<div class="bm-features-form">';
html += '<strong>Add Value</strong>';
html += '<div class="bm-form-row"><label>Name</label><input type="text" class="bm-val-name" placeholder="Value name" /></div>';
html += '<div class="bm-form-row"><label>Description</label><textarea class="bm-val-desc" placeholder="Optional"></textarea></div>';
html += '<div class="bm-form-row"><label>Price Modifier (+/-)</label><input type="number" class="bm-val-modifier" step="0.01" value="0" /></div>';
html += '<div class="bm-form-row"><label>Price Override</label><input type="number" class="bm-val-override" step="0.01" placeholder="Leave blank to use modifier" /></div>';
html += '<div class="bm-form-row"><label>Is Default</label><input type="checkbox" class="bm-val-default" /></div>';
html += '<button class="button button-primary bm-add-option-value" data-set-id="' + set.id + '">Add Value</button>';
html += '</div></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load option sets.</p>' ); });
}

$( document ).on( 'click', '#bm-options-load', function () { bmLoadOptionSets(); });

$( document ).on( 'click', '#bm-add-option-set', function () {
var svcId = $( '#bm-option-set-service-id-ref' ).val();
var name  = $( '#bm-option-set-name' ).val().trim();
if ( ! svcId || ! name ) { alert( 'Service and name are required.' ); return; }
var payload = {
service_id:  parseInt( svcId, 10 ),
name:        name,
description: $( '#bm-option-set-desc' ).val(),
is_required: $( '#bm-option-set-required' ).is( ':checked' ) ? 1 : 0
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'option-sets', payload ).done( function () {
$( '#bm-option-set-name' ).val( '' );
$( '#bm-option-set-desc' ).val( '' );
$( '#bm-options-service-id' ).val( svcId );
bmLoadOptionSets();
}).fail( function () { alert( 'Failed to create option set.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Add Option Set' ); });
});

$( document ).on( 'click', '.bm-options-delete-set', function () {
var setId = $( this ).data( 'set-id' );
if ( ! confirm( 'Delete option set and all its values?' ) ) { return; }
bmRestDelete( 'option-sets/' + setId ).done( function () { bmLoadOptionSets(); })
.fail( function () { alert( 'Failed to delete option set.' ); });
});

$( document ).on( 'click', '.bm-options-show-values', function () {
var setId = $( this ).data( 'set-id' );
var $row  = $( '#bm-option-values-' + setId );
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$row.show();
bmLoadOptionValues( setId );
});

function bmLoadOptionValues( setId ) {
var $wrap = $( '#bm-values-list-' + setId );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'option-sets/' + setId + '/values' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No values yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Modifier</th><th>Override</th><th>Default</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, v ) {
html += '<tr><td>' + v.id + '</td><td>' + $( '<span>' ).text( v.name ).html() + '</td>';
html += '<td>' + v.price_modifier + '</td><td>' + ( null !== v.price_override && undefined !== v.price_override ? v.price_override : '&mdash;' ) + '</td>';
html += '<td>' + ( v.is_default ? '&#10003;' : '' ) + '</td>';
html += '<td><button class="button button-small button-link-delete bm-delete-option-value" data-value-id="' + v.id + '" data-set-id="' + setId + '">Delete</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load values.</p>' ); });
}

$( document ).on( 'click', '.bm-add-option-value', function () {
var setId    = $( this ).data( 'set-id' );
var $form    = $( this ).closest( '.bm-features-form' );
var name     = $form.find( '.bm-val-name' ).val().trim();
if ( ! name ) { alert( 'Value name is required.' ); return; }
var override = $form.find( '.bm-val-override' ).val().trim();
var payload  = {
name:           name,
description:    $form.find( '.bm-val-desc' ).val(),
price_modifier: parseFloat( $form.find( '.bm-val-modifier' ).val() ) || 0,
price_override: override !== '' ? parseFloat( override ) : null,
is_default:     $form.find( '.bm-val-default' ).is( ':checked' ) ? 1 : 0
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'option-sets/' + setId + '/values', payload ).done( function () {
$form.find( '.bm-val-name' ).val( '' );
$form.find( '.bm-val-desc' ).val( '' );
$form.find( '.bm-val-modifier' ).val( '0' );
$form.find( '.bm-val-override' ).val( '' );
$form.find( '.bm-val-default' ).prop( 'checked', false );
bmLoadOptionValues( setId );
}).fail( function () { alert( 'Failed to add option value.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Add Value' ); });
});

$( document ).on( 'click', '.bm-delete-option-value', function () {
var valId = $( this ).data( 'value-id' );
var setId = $( this ).data( 'set-id' );
if ( ! confirm( 'Delete this option value?' ) ) { return; }
bmRestDelete( 'option-values/' + valId ).done( function () { bmLoadOptionValues( setId ); })
.fail( function () { alert( 'Failed to delete option value.' ); });
});

// ═══════════════════════ TAB 2: SERVICE EXTRAS ═══════════════════════

function bmLoadServiceExtras() {
var $wrap    = $( '#bm-sae-list' );
var parentId = $( '#bm-sae-parent-id' ).val();
if ( ! parentId ) { $wrap.html( '<p>Select a parent service above.</p>' ); return; }
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'service-extras/service/' + encodeURIComponent( parentId ) ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No add-on services configured yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Addon Svc ID</th><th>Price Override</th><th>Frontend</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, row ) {
html += '<tr><td>' + row.id + '</td><td>' + row.addon_service_id + '</td>';
html += '<td>' + ( null !== row.price_override && undefined !== row.price_override ? row.price_override : '&mdash;' ) + '</td>';
html += '<td>' + ( row.is_visible_frontend ? 'Yes' : 'No' ) + '</td>';
html += '<td><button class="button button-small button-link-delete bm-sae-delete" data-id="' + row.id + '">Delete</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load service extras.</p>' ); });
}

$( document ).on( 'click', '#bm-sae-load', function () { bmLoadServiceExtras(); });

$( document ).on( 'click', '#bm-sae-add', function () {
var parentId = $( '#bm-sae-parent-id' ).val();
var addonId  = $( '#bm-sae-addon-id' ).val();
if ( ! parentId || ! addonId ) { alert( 'Both parent and addon service are required.' ); return; }
var override = $( '#bm-sae-price-override' ).val().trim();
var payload = {
parent_service_id:   parseInt( parentId, 10 ),
addon_service_id:    parseInt( addonId, 10 ),
price_override:      override !== '' ? parseFloat( override ) : null,
is_visible_frontend: $( '#bm-sae-frontend' ).is( ':checked' ) ? 1 : 0
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'service-extras', payload ).done( function () {
$( '#bm-sae-addon-id' ).val( '' );
$( '#bm-sae-price-override' ).val( '' );
bmLoadServiceExtras();
}).fail( function () { alert( 'Failed to add service extra.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Add Addon' ); });
});

$( document ).on( 'click', '.bm-sae-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Remove this addon relationship?' ) ) { return; }
bmRestDelete( 'service-extras/' + id ).done( function () { bmLoadServiceExtras(); })
.fail( function () { alert( 'Failed to remove addon.' ); });
});

// ═══════════════════════ TAB 3: BUNDLES ══════════════════════════════

function bmLoadBundles() {
var $wrap = $( '#bm-bundles-list' );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'bundles' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No bundles yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Discount</th><th>Items</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, b ) {
var discountStr = b.discount_type ? b.discount_type + ': ' + b.discount_value : 'None';
html += '<tr><td>' + b.id + '</td><td>' + $( '<span>' ).text( b.name ).html() + '</td><td>' + discountStr + '</td>';
html += '<td><button class="button button-small bm-bundle-show-items" data-id="' + b.id + '">Items</button></td>';
html += '<td><button class="button button-small button-link-delete bm-bundle-delete" data-id="' + b.id + '">Delete</button></td></tr>';
html += '<tr class="bm-child-rows" id="bm-bundle-items-' + b.id + '" style="display:none"><td colspan="5">';
html += '<h4>Items in &ldquo;' + $('<span>').text(b.name).html() + '&rdquo;</h4>';
html += '<div id="bm-bundle-items-list-' + b.id + '"></div>';
html += '<div class="bm-features-form"><strong>Add Service to Bundle</strong>';
html += '<div class="bm-form-row"><label>Service ID</label><input type="number" class="bm-bundle-item-svc-id" min="1" /></div>';
html += '<div class="bm-form-row"><label>Quantity</label><input type="number" class="bm-bundle-item-qty" min="1" value="1" /></div>';
html += '<div class="bm-form-row"><label>Optional</label><input type="checkbox" class="bm-bundle-item-optional" /></div>';
html += '<button class="button button-primary bm-add-bundle-item" data-bundle-id="' + b.id + '">Add Item</button>';
html += '</div></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load bundles.</p>' ); });
}

$( document ).on( 'click', '#bm-load-bundles', function () { bmLoadBundles(); });

$( document ).on( 'click', '#bm-add-bundle', function () {
var name = $( '#bm-bundle-name' ).val().trim();
if ( ! name ) { alert( 'Bundle name is required.' ); return; }
var discType = $( '#bm-bundle-discount-type' ).val();
var payload = {
name:           name,
description:    $( '#bm-bundle-desc' ).val(),
discount_type:  discType || null,
discount_value: parseFloat( $( '#bm-bundle-discount-value' ).val() ) || 0
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'bundles', payload ).done( function () {
$( '#bm-bundle-name, #bm-bundle-desc, #bm-bundle-discount-value' ).val( '' );
bmLoadBundles();
}).fail( function () { alert( 'Failed to create bundle.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Create Bundle' ); });
});

$( document ).on( 'click', '.bm-bundle-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this bundle and all its items?' ) ) { return; }
bmRestDelete( 'bundles/' + id ).done( function () { bmLoadBundles(); })
.fail( function () { alert( 'Failed to delete bundle.' ); });
});

$( document ).on( 'click', '.bm-bundle-show-items', function () {
var id   = $( this ).data( 'id' );
var $row = $( '#bm-bundle-items-' + id );
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$row.show();
bmLoadBundleItems( id );
});

function bmLoadBundleItems( bundleId ) {
var $wrap = $( '#bm-bundle-items-list-' + bundleId );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'bundles/' + bundleId + '/items' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No items.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>Item ID</th><th>Service ID</th><th>Qty</th><th>Optional</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, item ) {
html += '<tr><td>' + item.id + '</td><td>' + item.service_id + '</td><td>' + item.quantity + '</td><td>' + ( item.is_optional ? 'Yes' : 'No' ) + '</td>';
html += '<td><button class="button button-small button-link-delete bm-remove-bundle-item" data-item-id="' + item.id + '" data-bundle-id="' + bundleId + '">Remove</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load items.</p>' ); });
}

$( document ).on( 'click', '.bm-add-bundle-item', function () {
var bundleId = $( this ).data( 'bundle-id' );
var $form    = $( this ).closest( '.bm-features-form' );
var svcId    = $form.find( '.bm-bundle-item-svc-id' ).val();
if ( ! svcId ) { alert( 'Service ID is required.' ); return; }
var payload = {
service_id:  parseInt( svcId, 10 ),
quantity:    parseInt( $form.find( '.bm-bundle-item-qty' ).val(), 10 ) || 1,
is_optional: $form.find( '.bm-bundle-item-optional' ).is( ':checked' ) ? 1 : 0
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'bundles/' + bundleId + '/items', payload ).done( function () {
$form.find( '.bm-bundle-item-svc-id' ).val( '' );
$form.find( '.bm-bundle-item-qty' ).val( '1' );
bmLoadBundleItems( bundleId );
}).fail( function () { alert( 'Failed to add item.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Add Item' ); });
});

$( document ).on( 'click', '.bm-remove-bundle-item', function () {
var itemId   = $( this ).data( 'item-id' );
var bundleId = $( this ).data( 'bundle-id' );
if ( ! confirm( 'Remove this item from the bundle?' ) ) { return; }
bmRestDelete( 'bundle-items/' + itemId ).done( function () { bmLoadBundleItems( bundleId ); })
.fail( function () { alert( 'Failed to remove item.' ); });
});

// ═══════════════════════ TAB 4: VIRTUAL SERVICES ═════════════════════

function bmLoadVirtualServices() {
var $wrap = $( '#bm-vs-list' );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'virtual-services' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No virtual services yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Service ID</th><th>Components</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, vs ) {
html += '<tr><td>' + vs.id + '</td><td>' + $( '<span>' ).text( vs.name ).html() + '</td><td>' + vs.service_id + '</td>';
html += '<td><button class="button button-small bm-vs-show-components" data-id="' + vs.id + '">Components</button></td>';
html += '<td><button class="button button-small button-link-delete bm-vs-delete" data-id="' + vs.id + '">Delete</button></td></tr>';
html += '<tr class="bm-child-rows" id="bm-vs-comps-' + vs.id + '" style="display:none"><td colspan="5">';
html += '<div id="bm-vs-comps-list-' + vs.id + '"></div>';
html += '<div class="bm-features-form"><strong>Add Component</strong>';
html += '<div class="bm-form-row"><label>Component Service ID</label><input type="number" class="bm-vs-comp-svc-id" min="1" /></div>';
html += '<button class="button button-primary bm-add-vs-component" data-vs-id="' + vs.id + '">Add Component</button>';
html += '</div></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load virtual services.</p>' ); });
}

$( document ).on( 'click', '#bm-vs-load', function () { bmLoadVirtualServices(); });

$( document ).on( 'click', '#bm-vs-add', function () {
var svcId = $( '#bm-vs-service-id' ).val();
var name  = $( '#bm-vs-name' ).val().trim();
if ( ! svcId || ! name ) { alert( 'Service ID and name are required.' ); return; }
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'virtual-services', { service_id: parseInt( svcId, 10 ), name: name, description: $( '#bm-vs-desc' ).val() } )
.done( function () {
$( '#bm-vs-service-id' ).val( '' );
$( '#bm-vs-name, #bm-vs-desc' ).val( '' );
bmLoadVirtualServices();
}).fail( function () { alert( 'Failed to create virtual service.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Create Virtual Service' ); });
});

$( document ).on( 'click', '.bm-vs-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this virtual service?' ) ) { return; }
bmRestDelete( 'virtual-services/' + id ).done( function () { bmLoadVirtualServices(); })
.fail( function () { alert( 'Failed to delete.' ); });
});

$( document ).on( 'click', '.bm-vs-show-components', function () {
var id   = $( this ).data( 'id' );
var $row = $( '#bm-vs-comps-' + id );
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$row.show();
bmLoadVSComponents( id );
});

function bmLoadVSComponents( vsId ) {
var $wrap = $( '#bm-vs-comps-list-' + vsId );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'virtual-services/' + vsId + '/components' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No components yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>Comp ID</th><th>Service ID</th><th>Position</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, c ) {
html += '<tr><td>' + c.id + '</td><td>' + c.component_service_id + '</td><td>' + c.component_position + '</td>';
html += '<td><button class="button button-small button-link-delete bm-remove-vs-component" data-comp-id="' + c.id + '" data-vs-id="' + vsId + '">Remove</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load components.</p>' ); });
}

$( document ).on( 'click', '.bm-add-vs-component', function () {
var vsId  = $( this ).data( 'vs-id' );
var $form = $( this ).closest( '.bm-features-form' );
var svcId = $form.find( '.bm-vs-comp-svc-id' ).val();
if ( ! svcId ) { alert( 'Component service ID is required.' ); return; }
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'virtual-services/' + vsId + '/components', { component_service_id: parseInt( svcId, 10 ) } )
.done( function () {
$form.find( '.bm-vs-comp-svc-id' ).val( '' );
bmLoadVSComponents( vsId );
}).fail( function () { alert( 'Failed to add component.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Add Component' ); });
});

$( document ).on( 'click', '.bm-remove-vs-component', function () {
var compId = $( this ).data( 'comp-id' );
var vsId   = $( this ).data( 'vs-id' );
if ( ! confirm( 'Remove this component?' ) ) { return; }
bmRestDelete( 'virtual-service-components/' + compId ).done( function () { bmLoadVSComponents( vsId ); })
.fail( function () { alert( 'Failed to remove component.' ); });
});

// ═══════════════════════ TAB 5: RESOURCE POOLS ═══════════════════════

function bmLoadResourcePools() {
var $wrap = $( '#bm-pools-list' );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'resource-pools' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No resource pools yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Total Cap.</th><th>Linked Services</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, p ) {
html += '<tr><td>' + p.id + '</td><td>' + $( '<span>' ).text( p.name ).html() + '</td><td>' + p.total_capacity + '</td>';
html += '<td><button class="button button-small bm-pool-show-services" data-id="' + p.id + '">Services</button></td>';
html += '<td><button class="button button-small button-link-delete bm-pool-delete" data-id="' + p.id + '">Delete</button></td></tr>';
html += '<tr class="bm-child-rows" id="bm-pool-svcs-' + p.id + '" style="display:none"><td colspan="5">';
html += '<div id="bm-pool-svcs-list-' + p.id + '"></div>';
html += '<div class="bm-features-form"><strong>Link Service to Pool</strong>';
html += '<div class="bm-form-row"><label>Service ID</label><input type="number" class="bm-pool-svc-id" min="1" /></div>';
html += '<div class="bm-form-row"><label>Capacity Used</label><input type="number" class="bm-pool-svc-cap" min="1" value="1" /></div>';
html += '<button class="button button-primary bm-add-pool-service" data-pool-id="' + p.id + '">Link Service</button>';
html += '</div></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load pools.</p>' ); });
}

$( document ).on( 'click', '#bm-pools-load', function () { bmLoadResourcePools(); });

$( document ).on( 'click', '#bm-add-pool', function () {
var name = $( '#bm-pool-name' ).val().trim();
var cap  = parseInt( $( '#bm-pool-capacity' ).val(), 10 );
if ( ! name || ! cap || cap < 1 ) { alert( 'Name and a positive total capacity are required.' ); return; }
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'resource-pools', { name: name, description: $( '#bm-pool-desc' ).val(), total_capacity: cap } )
.done( function () {
$( '#bm-pool-name, #bm-pool-desc, #bm-pool-capacity' ).val( '' );
bmLoadResourcePools();
}).fail( function () { alert( 'Failed to create resource pool.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Create Pool' ); });
});

$( document ).on( 'click', '.bm-pool-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this resource pool?' ) ) { return; }
bmRestDelete( 'resource-pools/' + id ).done( function () { bmLoadResourcePools(); })
.fail( function () { alert( 'Failed to delete pool.' ); });
});

$( document ).on( 'click', '.bm-pool-show-services', function () {
var id   = $( this ).data( 'id' );
var $row = $( '#bm-pool-svcs-' + id );
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$row.show();
bmLoadPoolServices( id );
});

function bmLoadPoolServices( poolId ) {
var $wrap = $( '#bm-pool-svcs-list-' + poolId );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'resource-pools/' + poolId + '/linked-services' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No services linked.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>Link ID</th><th>Service ID</th><th>Cap. Used</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, s ) {
html += '<tr><td>' + s.id + '</td><td>' + s.service_id + '</td><td>' + s.capacity_used + '</td>';
html += '<td><button class="button button-small button-link-delete bm-unlink-pool-service" data-pool-id="' + poolId + '" data-svc-id="' + s.service_id + '">Unlink</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load services.</p>' ); });
}

$( document ).on( 'click', '.bm-add-pool-service', function () {
var poolId = $( this ).data( 'pool-id' );
var $form  = $( this ).closest( '.bm-features-form' );
var svcId  = $form.find( '.bm-pool-svc-id' ).val();
var cap    = parseInt( $form.find( '.bm-pool-svc-cap' ).val(), 10 ) || 1;
if ( ! svcId ) { alert( 'Service ID is required.' ); return; }
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'resource-pools/' + poolId + '/services', { service_id: parseInt( svcId, 10 ), consumption_per_booking: cap } )
.done( function () {
$form.find( '.bm-pool-svc-id' ).val( '' );
$form.find( '.bm-pool-svc-cap' ).val( '1' );
bmLoadPoolServices( poolId );
}).fail( function () { alert( 'Failed to link service.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Link Service' ); });
});

$( document ).on( 'click', '.bm-unlink-pool-service', function () {
var poolId = $( this ).data( 'pool-id' );
var svcId  = $( this ).data( 'svc-id' );
if ( ! confirm( 'Unlink this service from the pool?' ) ) { return; }
bmRestDelete( 'resource-pools/' + poolId + '/services', { service_id: svcId } )
.done( function () { bmLoadPoolServices( poolId ); })
.fail( function () { alert( 'Failed to unlink service.' ); });
});

// ═══════════════════════ TAB 6: SERVICE CHAINS ═══════════════════════

function bmLoadServiceChains() {
var $wrap = $( '#bm-chains-list' );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'chains' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No service chains yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Service A</th><th>Service B</th><th>Type</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, c ) {
html += '<tr><td>' + c.id + '</td><td>Svc #' + c.service_a_id + '</td><td>Svc #' + c.service_b_id + '</td>';
html += '<td>' + $( '<span>' ).text( c.chain_type || 'mutual_exclusion' ).html() + '</td>';
html += '<td><button class="button button-small button-link-delete bm-chain-delete" data-id="' + c.id + '">Delete</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load chains.</p>' ); });
}

$( document ).on( 'click', '#bm-chains-load', function () { bmLoadServiceChains(); });

$( document ).on( 'click', '#bm-add-chain', function () {
var svcA = $( '#bm-chain-svc-a' ).val();
var svcB = $( '#bm-chain-svc-b' ).val();
if ( ! svcA || ! svcB ) { alert( 'Both service IDs are required.' ); return; }
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'chains', { service_a_id: parseInt( svcA, 10 ), service_b_id: parseInt( svcB, 10 ), chain_type: $( '#bm-chain-type' ).val() } )
.done( function () {
$( '#bm-chain-svc-a, #bm-chain-svc-b' ).val( '' );
bmLoadServiceChains();
}).fail( function () { alert( 'Failed to create service chain.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Add Chain Rule' ); });
});

$( document ).on( 'click', '.bm-chain-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this chain?' ) ) { return; }
bmRestDelete( 'chains/' + id ).done( function () { bmLoadServiceChains(); })
.fail( function () { alert( 'Failed to delete chain.' ); });
});

}( jQuery ));
