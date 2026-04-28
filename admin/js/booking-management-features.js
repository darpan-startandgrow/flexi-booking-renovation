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
html += '<td><button class="button button-small bm-options-show-values" data-set-id="' + set.id + '">Values</button></td>';
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
// Sync the filter dropdown to match the new set's service before loading.
$( '#bm-options-service-id' ).val( svcId );
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
// P13: up/down reorder controls stored in sort_order field (uses price_modifier position if no dedicated sort field — toggling visually here, no DB sort column; we use existing JS ordering).
var html = '<table class="bm-features-table"><thead><tr><th>Order</th><th>ID</th><th>Name</th><th>Modifier</th><th>Override</th><th>Default</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, v ) {
var isFirst = ( i === 0 );
var isLast  = ( i === rows.length - 1 );
html += '<tr data-value-id="' + v.id + '" data-set-id="' + setId + '">';
html += '<td>';
html += '<button class="button button-small bm-val-move-up" data-value-id="' + v.id + '" data-set-id="' + setId + '" title="Move up"' + ( isFirst ? ' disabled' : '' ) + '>&uarr;</button> ';
html += '<button class="button button-small bm-val-move-down" data-value-id="' + v.id + '" data-set-id="' + setId + '" title="Move down"' + ( isLast ? ' disabled' : '' ) + '>&darr;</button>';
html += '</td>';
html += '<td>' + v.id + '</td><td>' + $( '<span>' ).text( v.name ).html() + '</td>';
html += '<td>' + v.price_modifier + '</td><td>' + ( null !== v.price_override && undefined !== v.price_override ? v.price_override : '&mdash;' ) + '</td>';
html += '<td>' + ( v.is_default ? '&#10003;' : '' ) + '</td>';
html += '<td><button class="button button-small button-link-delete bm-delete-option-value" data-value-id="' + v.id + '" data-set-id="' + setId + '">Delete</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
// Store ordered list for up/down operations.
$wrap.data( 'option-value-rows', rows );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load values.</p>' ); });
}

// P13: move value up — swap sort_order by sending updated position to server.
$( document ).on( 'click', '.bm-val-move-up, .bm-val-move-down', function () {
var $btn    = $( this );
var isUp    = $btn.hasClass( 'bm-val-move-up' );
var valueId = $btn.data( 'value-id' );
var setId   = $btn.data( 'set-id' );
var $tbody  = $btn.closest( 'tbody' );
var $tr     = $btn.closest( 'tr' );
if ( isUp ) {
var $prev = $tr.prev( 'tr' );
if ( $prev.length ) { $tr.insertBefore( $prev ); }
} else {
var $next = $tr.next( 'tr' );
if ( $next.length ) { $tr.insertAfter( $next ); }
}
// Re-number and persist positions.
$tbody.find( 'tr' ).each( function ( idx ) {
var vid = $( this ).data( 'value-id' );
if ( vid ) {
bmRestPost( 'option-values/' + vid, { sort_order: idx } );
}
});
// Refresh up/down disabled states.
$tbody.find( 'tr' ).each( function ( idx, row ) {
var tot = $tbody.find( 'tr' ).length;
$( row ).find( '.bm-val-move-up' ).prop( 'disabled', idx === 0 );
$( row ).find( '.bm-val-move-down' ).prop( 'disabled', idx === tot - 1 );
});
});

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
// P11: resolve addon service names.
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Addon Service</th><th>Price Override</th><th>Frontend</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, row ) {
html += '<tr data-sae-id="' + row.id + '">';
html += '<td>' + row.id + '</td>';
html += '<td>' + $( '<span>' ).text( bmServiceName( row.addon_service_id ) ).html() + '</td>';
html += '<td>' + ( null !== row.price_override && undefined !== row.price_override ? row.price_override : '&mdash;' ) + '</td>';
html += '<td>' + ( row.is_visible_frontend ? 'Yes' : 'No' ) + '</td>';
html += '<td>';
html += '<button class="button button-small bm-sae-edit" data-id="' + row.id + '">Edit</button> ';
html += '<button class="button button-small button-link-delete bm-sae-delete" data-id="' + row.id + '">Delete</button>';
html += '</td></tr>';
// P19: inline edit row.
html += '<tr class="bm-child-rows" id="bm-sae-edit-' + row.id + '" style="display:none"><td colspan="5">';
html += '<div class="bm-features-form"><strong>Edit Add-on</strong>';
html += '<div class="bm-form-row"><label>Price Override</label><input type="number" class="bm-edit-sae-price" step="0.01" value="' + ( row.price_override !== null && row.price_override !== undefined ? row.price_override : '' ) + '" placeholder="Leave blank to use service price" /></div>';
html += '<div class="bm-form-row"><label>Show on Frontend</label><input type="checkbox" class="bm-edit-sae-frontend"' + ( row.is_visible_frontend ? ' checked' : '' ) + ' /></div>';
html += '<button class="button button-primary bm-save-sae-edit" data-sae-id="' + row.id + '">Save Changes</button>';
html += '</div></td></tr>';
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

// P19: edit service extra.
$( document ).on( 'click', '.bm-sae-edit', function () {
var id    = $( this ).data( 'id' );
var $edit = $( '#bm-sae-edit-' + id );
if ( $edit.is( ':visible' ) ) { $edit.hide(); return; }
$edit.show();
});

$( document ).on( 'click', '.bm-save-sae-edit', function () {
var saeId = $( this ).data( 'sae-id' );
var $form = $( this ).closest( '.bm-features-form' );
var price = $form.find( '.bm-edit-sae-price' ).val().trim();
var payload = {
price_override:      price !== '' ? parseFloat( price ) : null,
is_visible_frontend: $form.find( '.bm-edit-sae-frontend' ).is( ':checked' ) ? 1 : 0
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'service-extras/' + saeId, payload ).done( function () {
$( '#bm-sae-edit-' + saeId ).hide();
bmLoadServiceExtras();
}).fail( function () { alert( 'Failed to save.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Save Changes' ); });
});

$( document ).on( 'click', '.bm-sae-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Remove this addon relationship?' ) ) { return; }
bmRestDelete( 'service-extras/' + id ).done( function () { bmLoadServiceExtras(); })
.fail( function () { alert( 'Failed to remove addon.' ); });
});

// ═══════════════════════ TAB 3: BUNDLES ══════════════════════════════

// Helper: build service <select> HTML from bmFeaturesData.services.
function bmServiceSelect( cls, placeholder, selectedId ) {
var opts = '<option value="">' + ( placeholder || '— Select Service —' ) + '</option>';
var svcs = ( bmFeaturesData.services || [] );
$.each( svcs, function ( i, s ) {
var sel = ( selectedId && parseInt( selectedId, 10 ) === s.id ) ? ' selected' : '';
opts += '<option value="' + s.id + '"' + sel + '>' + $( '<span>' ).text( s.name ).html() + ' (ID: ' + s.id + ')</option>';
});
return '<select class="' + cls + '">' + opts + '</select>';
}

// Helper: resolve a service name from its ID.
function bmServiceName( id ) {
var svcs = ( bmFeaturesData.services || [] );
for ( var i = 0; i < svcs.length; i++ ) {
if ( svcs[ i ].id === parseInt( id, 10 ) ) {
return svcs[ i ].name + ' (ID: ' + svcs[ i ].id + ')';
}
}
return 'Service #' + id;
}

function bmLoadBundles() {
var $wrap = $( '#bm-bundles-list' );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'bundles' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No bundles yet.</p>' ); return; }
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Price</th><th>Discount</th><th>Status</th><th>Items</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, b ) {
var discountStr = b.discount_type ? b.discount_type + ': ' + b.discount_value : 'None';
var statusBadge = parseInt( b.status, 10 ) === 1
? '<span style="color:green;font-weight:600;">Active</span>'
: '<span style="color:#999;">Inactive</span>';
html += '<tr data-bundle-id="' + b.id + '">';
html += '<td>' + b.id + '</td><td>' + $( '<span>' ).text( b.name ).html() + '</td>';
html += '<td>' + ( b.price !== undefined ? parseFloat( b.price ).toFixed( 2 ) : '0.00' ) + '</td>';
html += '<td>' + discountStr + '</td><td>' + statusBadge + '</td>';
html += '<td><button class="button button-small bm-bundle-show-items" data-id="' + b.id + '">Items</button></td>';
html += '<td>';
html += '<button class="button button-small bm-bundle-edit" data-id="' + b.id + '">Edit</button> ';
html += '<button class="button button-small button-link-delete bm-bundle-delete" data-id="' + b.id + '">Delete</button>';
html += '</td></tr>';
html += '<tr class="bm-child-rows" id="bm-bundle-edit-' + b.id + '" style="display:none"><td colspan="7">';
html += '<div class="bm-features-form"><strong>Edit Bundle</strong>';
html += '<div class="bm-form-row"><label>Name <span class="bm-required-star">*</span></label><input type="text" class="bm-edit-bundle-name" value="' + $( '<span>' ).text( b.name ).html() + '" /></div>';
html += '<div class="bm-form-row"><label>Description</label><textarea class="bm-edit-bundle-desc">' + $( '<span>' ).text( b.description || '' ).html() + '</textarea></div>';
html += '<div class="bm-form-row"><label>Bundle Price <span class="bm-required-star">*</span></label><input type="number" class="bm-edit-bundle-price" step="0.01" min="0" value="' + ( b.price || 0 ) + '" /></div>';
html += '<div class="bm-form-row"><label>Discount Type</label><select class="bm-edit-bundle-discount-type"><option value="">None</option><option value="percent"' + ( b.discount_type === 'percent' ? ' selected' : '' ) + '>Percent (%)</option><option value="fixed"' + ( b.discount_type === 'fixed' ? ' selected' : '' ) + '>Fixed Amount</option></select></div>';
html += '<div class="bm-form-row"><label>Discount Value</label><input type="number" class="bm-edit-bundle-discount-value" step="0.01" value="' + ( b.discount_value || 0 ) + '" /></div>';
html += '<div class="bm-form-row"><label>Status</label><select class="bm-edit-bundle-status"><option value="1"' + ( parseInt( b.status, 10 ) === 1 ? ' selected' : '' ) + '>Active</option><option value="0"' + ( parseInt( b.status, 10 ) === 0 ? ' selected' : '' ) + '>Inactive</option></select></div>';
html += '<button class="button button-primary bm-save-bundle-edit" data-bundle-id="' + b.id + '">Save Changes</button>';
html += '</div></td></tr>';
html += '<tr class="bm-child-rows" id="bm-bundle-items-' + b.id + '" style="display:none"'
+ ' data-discount-type="' + ( b.discount_type || '' ) + '"'
+ ' data-discount-value="' + parseFloat( b.discount_value || 0 ) + '"'
+ ' data-bundle-price="' + parseFloat( b.price || 0 ) + '"'
+ '><td colspan="7">';
html += '<h4>Items in &ldquo;' + $( '<span>' ).text( b.name ).html() + '&rdquo;</h4>';
html += '<div id="bm-bundle-total-wrap-' + b.id + '" style="background:#f0f4ff;border:1px solid #d0d9f0;border-radius:4px;padding:10px 14px;margin-bottom:8px;font-size:13px;">';
html += '<strong>' + b.name + ' — Price preview</strong><br/>';
html += '<span id="bm-bundle-subtotal-' + b.id + '" style="display:block;margin-top:4px;">Component subtotal: <strong>—</strong></span>';
html += '<span id="bm-bundle-discount-line-' + b.id + '" style="display:block;">Discount: <strong>—</strong></span>';
html += '<span id="bm-bundle-total-' + b.id + '" style="display:block;font-size:14px;">Bundle price: <strong>—</strong></span>';
html += '</div>';
html += '<div id="bm-bundle-items-list-' + b.id + '"></div>';
html += '<div class="bm-features-form"><strong>Add Service to Bundle</strong>';
html += '<div class="bm-form-row"><label>Service <span class="bm-required-star">*</span></label>' + bmServiceSelect( 'bm-bundle-item-svc-id', '— Select Service —' ) + '</div>';
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
var name  = $( '#bm-bundle-name' ).val().trim();
var price = $( '#bm-bundle-price' ).val().trim();
if ( ! name ) { alert( 'Bundle name is required.' ); return; }
if ( price === '' || isNaN( parseFloat( price ) ) || parseFloat( price ) < 0 ) {
alert( 'A valid bundle price is required.' ); return;
}
var discType = $( '#bm-bundle-discount-type' ).val();
var payload = {
name:           name,
description:    $( '#bm-bundle-desc' ).val(),
price:          parseFloat( price ),
discount_type:  discType || null,
discount_value: parseFloat( $( '#bm-bundle-discount-value' ).val() ) || 0,
status:         parseInt( $( '#bm-bundle-status' ).val(), 10 )
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'bundles', payload ).done( function () {
$( '#bm-bundle-name, #bm-bundle-desc, #bm-bundle-discount-value, #bm-bundle-price' ).val( '' );
bmLoadBundles();
}).fail( function () { alert( 'Failed to create bundle.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Create Bundle' ); });
});

$( document ).on( 'click', '.bm-bundle-edit', function () {
var id    = $( this ).data( 'id' );
var $edit = $( '#bm-bundle-edit-' + id );
var $items = $( '#bm-bundle-items-' + id );
if ( $edit.is( ':visible' ) ) { $edit.hide(); return; }
$items.hide();
$edit.show();
});

$( document ).on( 'click', '.bm-save-bundle-edit', function () {
var bundleId = $( this ).data( 'bundle-id' );
var $form    = $( this ).closest( '.bm-features-form' );
var name     = $form.find( '.bm-edit-bundle-name' ).val().trim();
var price    = $form.find( '.bm-edit-bundle-price' ).val().trim();
if ( ! name ) { alert( 'Bundle name is required.' ); return; }
if ( price === '' || isNaN( parseFloat( price ) ) || parseFloat( price ) < 0 ) {
alert( 'A valid bundle price is required.' ); return;
}
var payload = {
name:           name,
description:    $form.find( '.bm-edit-bundle-desc' ).val(),
price:          parseFloat( price ),
discount_type:  $form.find( '.bm-edit-bundle-discount-type' ).val() || null,
discount_value: parseFloat( $form.find( '.bm-edit-bundle-discount-value' ).val() ) || 0,
status:         parseInt( $form.find( '.bm-edit-bundle-status' ).val(), 10 )
};
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'bundles/' + bundleId, payload ).done( function () {
$( '#bm-bundle-edit-' + bundleId ).hide();
bmLoadBundles();
}).fail( function () { alert( 'Failed to save bundle.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Save Changes' ); });
});

$( document ).on( 'click', '.bm-bundle-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this bundle and all its items?' ) ) { return; }
bmRestDelete( 'bundles/' + id ).done( function () { bmLoadBundles(); })
.fail( function () { alert( 'Failed to delete bundle.' ); });
});

$( document ).on( 'click', '.bm-bundle-show-items', function () {
var id    = $( this ).data( 'id' );
var $row  = $( '#bm-bundle-items-' + id );
var $edit = $( '#bm-bundle-edit-' + id );
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$edit.hide();
$row.show();
bmLoadBundleItems( id );
});

function bmLoadBundleItems( bundleId ) {
var $wrap = $( '#bm-bundle-items-list-' + bundleId );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'bundles/' + bundleId + '/items' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) {
$wrap.html( '<p>No items.</p>' );
$( '#bm-bundle-subtotal-' + bundleId ).html( 'Component subtotal: <strong>—</strong>' );
$( '#bm-bundle-discount-line-' + bundleId ).html( 'Discount: <strong>—</strong>' );
$( '#bm-bundle-total-' + bundleId ).html( 'Bundle price: <strong>—</strong>' );
return;
}
var html = '<table class="bm-features-table"><thead><tr><th>Item ID</th><th>Service</th><th>Unit Price</th><th>Qty</th><th>Optional</th><th>Actions</th></tr></thead><tbody>';
var subtotal = 0;
$.each( rows, function ( i, item ) {
var svcPrice = 0;
var svcs = ( bmFeaturesData.services || [] );
for ( var k = 0; k < svcs.length; k++ ) {
if ( svcs[ k ].id === parseInt( item.service_id, 10 ) ) {
svcPrice = svcs[ k ].price || 0;
break;
}
}
var lineTotal = svcPrice * ( parseInt( item.quantity, 10 ) || 1 );
subtotal += lineTotal;
html += '<tr><td>' + item.id + '</td>';
html += '<td>' + $( '<span>' ).text( bmServiceName( item.service_id ) ).html() + '</td>';
html += '<td>' + svcPrice.toFixed( 2 ) + '</td>';
html += '<td>' + item.quantity + '</td>';
html += '<td>' + ( item.is_optional ? 'Yes' : 'No' ) + '</td>';
html += '<td><button class="button button-small button-link-delete bm-remove-bundle-item" data-item-id="' + item.id + '" data-bundle-id="' + bundleId + '">Remove</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
// P10: real price arithmetic — compute discount and final price.
var $row         = $( '#bm-bundle-items-' + bundleId );
var discType     = $row.data( 'discount-type' ) || '';
var discValue    = parseFloat( $row.data( 'discount-value' ) ) || 0;
var bundlePrice  = parseFloat( $row.data( 'bundle-price' ) ) || 0;
var discAmount   = 0;
var discLabel    = 'None';
if ( discType === 'percent' && discValue > 0 ) {
discAmount = subtotal * ( discValue / 100 );
discLabel  = discValue + '% off = −' + discAmount.toFixed( 2 );
} else if ( discType === 'fixed' && discValue > 0 ) {
discAmount = Math.min( discValue, subtotal );
discLabel  = '−' + discAmount.toFixed( 2 ) + ' fixed';
}
var finalPrice = bundlePrice > 0 ? bundlePrice : Math.max( 0, subtotal - discAmount );
$( '#bm-bundle-subtotal-' + bundleId ).html( 'Component subtotal: <strong>' + subtotal.toFixed( 2 ) + '</strong>' );
$( '#bm-bundle-discount-line-' + bundleId ).html( 'Discount: <strong>' + discLabel + '</strong>' );
$( '#bm-bundle-total-' + bundleId ).html( 'Bundle price: <strong>' + finalPrice.toFixed( 2 ) + '</strong>' + ( bundlePrice > 0 ? ' <span style="color:#888;font-size:11px;">(fixed bundle price)</span>' : ' <span style="color:#888;font-size:11px;">(subtotal minus discount)</span>' ) );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load items.</p>' ); });
}

$( document ).on( 'click', '.bm-add-bundle-item', function () {
var bundleId = $( this ).data( 'bundle-id' );
var $form    = $( this ).closest( '.bm-features-form' );
var svcId    = $form.find( '.bm-bundle-item-svc-id' ).val();
if ( ! svcId ) { alert( 'Please select a service.' ); return; }
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
// P3: show VS Record ID (vs.id); omit service_id column.
var html = '<table class="bm-features-table"><thead><tr><th>VS Record ID</th><th>Name</th><th>Description</th><th>Components</th><th>Availability</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, vs ) {
html += '<tr>';
html += '<td>' + vs.id + '</td>';
html += '<td>' + $( '<span>' ).text( vs.name ).html() + '</td>';
html += '<td>' + $( '<span>' ).text( vs.description || '' ).html() + '</td>';
html += '<td><button class="button button-small bm-vs-show-components" data-id="' + vs.id + '">Components</button></td>';
// P18: availability check button.
html += '<td><button class="button button-small bm-vs-check-avail" data-id="' + vs.id + '">Check Availability</button></td>';
html += '<td>';
html += '<button class="button button-small bm-vs-edit" data-id="' + vs.id + '">Edit</button> ';
html += '<button class="button button-small button-link-delete bm-vs-delete" data-id="' + vs.id + '">Delete</button>';
html += '</td></tr>';
// Edit row (P19).
html += '<tr class="bm-child-rows" id="bm-vs-edit-' + vs.id + '" style="display:none"><td colspan="6">';
html += '<div class="bm-features-form"><strong>Edit Virtual Service</strong>';
html += '<div class="bm-form-row"><label>Name</label><input type="text" class="bm-edit-vs-name" value="' + $( '<span>' ).text( vs.name ).html() + '" /></div>';
html += '<div class="bm-form-row"><label>Description</label><textarea class="bm-edit-vs-desc">' + $( '<span>' ).text( vs.description || '' ).html() + '</textarea></div>';
html += '<button class="button button-primary bm-save-vs-edit" data-vs-id="' + vs.id + '">Save Changes</button>';
html += '</div></td></tr>';
// P18: availability panel row.
html += '<tr class="bm-child-rows" id="bm-vs-avail-' + vs.id + '" style="display:none"><td colspan="6">';
html += '<div class="bm-features-form"><strong>Availability Check</strong>';
html += '<div class="bm-form-row"><label>Date (YYYY-MM-DD)</label><input type="date" class="bm-vs-avail-date" /></div>';
html += '<button class="button bm-run-vs-avail" data-vs-id="' + vs.id + '">Check</button>';
html += '<div class="bm-vs-avail-result" style="margin-top:8px;"></div>';
html += '</div></td></tr>';
// Components row.
html += '<tr class="bm-child-rows" id="bm-vs-comps-' + vs.id + '" style="display:none"><td colspan="6">';
html += '<p class="description" style="margin:8px 0;"><strong>Components</strong> — real services whose simultaneous availability this virtual service requires. Attach 2 or more.</p>';
html += '<div id="bm-vs-comps-list-' + vs.id + '"></div>';
html += '<div class="bm-features-form"><strong>Add Component</strong>';
// P8/P14: use service dropdown instead of raw number input.
html += '<div class="bm-form-row"><label>Component Service <span class="bm-required-star">*</span></label>' + bmServiceSelect( 'bm-vs-comp-svc-id', '— Select Component Service —' ) + '</div>';
html += '<button class="button button-primary bm-add-vs-component" data-vs-id="' + vs.id + '">Add Component</button>';
html += '</div></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load virtual services.</p>' ); });
}

$( document ).on( 'click', '#bm-vs-load', function () { bmLoadVirtualServices(); });

// P3: create without service_id.
$( document ).on( 'click', '#bm-vs-add', function () {
var name  = $( '#bm-vs-name' ).val().trim();
if ( ! name ) { alert( 'Name is required.' ); return; }
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'virtual-services', { name: name, description: $( '#bm-vs-desc' ).val() } )
.done( function () {
$( '#bm-vs-name, #bm-vs-desc' ).val( '' );
bmLoadVirtualServices();
}).fail( function () { alert( 'Failed to create virtual service.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Create Virtual Service' ); });
});

// P19: edit VS.
$( document ).on( 'click', '.bm-vs-edit', function () {
var id    = $( this ).data( 'id' );
var $edit = $( '#bm-vs-edit-' + id );
$( '#bm-vs-comps-' + id + ', #bm-vs-avail-' + id ).hide();
if ( $edit.is( ':visible' ) ) { $edit.hide(); return; }
$edit.show();
});

$( document ).on( 'click', '.bm-save-vs-edit', function () {
var vsId  = $( this ).data( 'vs-id' );
var $form = $( this ).closest( '.bm-features-form' );
var name  = $form.find( '.bm-edit-vs-name' ).val().trim();
if ( ! name ) { alert( 'Name is required.' ); return; }
var $btn  = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'virtual-services/' + vsId, { name: name, description: $form.find( '.bm-edit-vs-desc' ).val() } )
.done( function () {
$( '#bm-vs-edit-' + vsId ).hide();
bmLoadVirtualServices();
}).fail( function () { alert( 'Failed to save virtual service.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Save Changes' ); });
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
$( '#bm-vs-edit-' + id + ', #bm-vs-avail-' + id ).hide();
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$row.show();
bmLoadVSComponents( id );
});

// P18: availability panel toggle.
$( document ).on( 'click', '.bm-vs-check-avail', function () {
var id    = $( this ).data( 'id' );
var $row  = $( '#bm-vs-avail-' + id );
$( '#bm-vs-comps-' + id + ', #bm-vs-edit-' + id ).hide();
if ( $row.is( ':visible' ) ) { $row.hide(); return; }
$row.show();
});

$( document ).on( 'click', '.bm-run-vs-avail', function () {
var vsId  = $( this ).data( 'vs-id' );
var $row  = $( '#bm-vs-avail-' + vsId );
var date  = $row.find( '.bm-vs-avail-date' ).val();
if ( ! date ) { alert( 'Please select a date.' ); return; }
var $res  = $row.find( '.bm-vs-avail-result' ).html( bmSpinner() + ' Checking&hellip;' );
bmRestGet( 'virtual-services/' + vsId + '/availability?date=' + encodeURIComponent( date ) )
.done( function ( data ) {
var avail = data && data.data ? data.data.available : ( data ? data.available : null );
if ( avail === true ) {
$res.html( '<span style="color:green;font-weight:600;">&#10003; Available on ' + date + '</span>' );
} else if ( avail === false ) {
$res.html( '<span style="color:#c0392b;font-weight:600;">&#10007; Not available on ' + date + ' (one or more components are booked)</span>' );
} else {
$res.html( '<span style="color:#999;">Could not determine availability.</span>' );
}
}).fail( function () { $res.html( '<span style="color:#c0392b;">Failed to check availability.</span>' ); });
});

function bmLoadVSComponents( vsId ) {
var $wrap = $( '#bm-vs-comps-list-' + vsId );
$wrap.html( bmSpinner() + ' Loading&hellip;' );
bmRestGet( 'virtual-services/' + vsId + '/components' ).done( function ( data ) {
var rows = Array.isArray( data ) ? data : ( data.data || [] );
if ( ! rows.length ) { $wrap.html( '<p>No components yet. Add 2 or more component services below.</p>' ); return; }
// P11: resolve service names.
var html = '<table class="bm-features-table"><thead><tr><th>Comp ID</th><th>Component Service</th><th>Position</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, c ) {
html += '<tr><td>' + c.id + '</td><td>' + $( '<span>' ).text( bmServiceName( c.component_service_id ) ).html() + '</td><td>' + c.component_position + '</td>';
html += '<td><button class="button button-small button-link-delete bm-remove-vs-component" data-comp-id="' + c.id + '" data-vs-id="' + vsId + '">Remove</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load components.</p>' ); });
}

$( document ).on( 'click', '.bm-add-vs-component', function () {
var vsId  = $( this ).data( 'vs-id' );
var $form = $( this ).closest( '.bm-features-form' );
// P8/P14: use select value.
var svcId = $form.find( '.bm-vs-comp-svc-id' ).val();
if ( ! svcId ) { alert( 'Please select a component service.' ); return; }
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
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Name</th><th>Total Cap.</th><th>Linked Services</th><th>Remaining</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, p ) {
html += '<tr><td>' + p.id + '</td><td>' + $( '<span>' ).text( p.name ).html() + '</td><td>' + p.total_capacity + '</td>';
html += '<td><button class="button button-small bm-pool-show-services" data-id="' + p.id + '">Services</button></td>';
// P9: date-filtered remaining capacity.
html += '<td><input type="date" class="bm-pool-avail-date" data-pool-id="' + p.id + '" style="width:120px;" /> <button class="button button-small bm-pool-check-avail" data-pool-id="' + p.id + '">Check</button> <span class="bm-pool-avail-result" data-pool-id="' + p.id + '"></span></td>';
html += '<td>';
html += '<button class="button button-small bm-pool-edit" data-id="' + p.id + '">Edit</button> ';
html += '<button class="button button-small button-link-delete bm-pool-delete" data-id="' + p.id + '">Delete</button>';
html += '</td></tr>';
// P19: edit row.
html += '<tr class="bm-child-rows" id="bm-pool-edit-' + p.id + '" style="display:none"><td colspan="6">';
html += '<div class="bm-features-form"><strong>Edit Resource Pool</strong>';
html += '<div class="bm-form-row"><label>Name</label><input type="text" class="bm-edit-pool-name" value="' + $( '<span>' ).text( p.name ).html() + '" /></div>';
html += '<div class="bm-form-row"><label>Description</label><textarea class="bm-edit-pool-desc">' + $( '<span>' ).text( p.description || '' ).html() + '</textarea></div>';
html += '<div class="bm-form-row"><label>Total Capacity</label><input type="number" class="bm-edit-pool-capacity" min="1" value="' + p.total_capacity + '" /></div>';
html += '<button class="button button-primary bm-save-pool-edit" data-pool-id="' + p.id + '">Save Changes</button>';
html += '</div></td></tr>';
// Services row.
html += '<tr class="bm-child-rows" id="bm-pool-svcs-' + p.id + '" style="display:none"><td colspan="6">';
html += '<div id="bm-pool-svcs-list-' + p.id + '"></div>';
html += '<div class="bm-features-form"><strong>Link Service to Pool</strong>';
// P8: use service dropdown.
html += '<div class="bm-form-row"><label>Service <span class="bm-required-star">*</span></label>' + bmServiceSelect( 'bm-pool-svc-id', '— Select Service —' ) + '</div>';
// P6: rename label.
html += '<div class="bm-form-row"><label>Seats consumed per booking</label><input type="number" class="bm-pool-svc-cap" min="1" value="1" /></div>';
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

// P9: check remaining capacity for a date.
$( document ).on( 'click', '.bm-pool-check-avail', function () {
var poolId = $( this ).data( 'pool-id' );
var date   = $( '.bm-pool-avail-date[data-pool-id="' + poolId + '"]' ).val();
var $res   = $( '.bm-pool-avail-result[data-pool-id="' + poolId + '"]' ).text( '…' );
if ( ! date ) { $res.text( 'Select a date first.' ); return; }
bmRestGet( 'resource-pools/' + poolId + '/availability?date=' + encodeURIComponent( date ) )
.done( function ( data ) {
var rem = ( data && data.data ) ? data.data.remaining : ( data ? data.remaining : '?' );
$res.text( 'Remaining: ' + rem );
}).fail( function () { $res.text( 'Error.' ); });
});

$( document ).on( 'click', '.bm-pool-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this resource pool?' ) ) { return; }
bmRestDelete( 'resource-pools/' + id ).done( function () { bmLoadResourcePools(); })
.fail( function () { alert( 'Failed to delete pool.' ); });
});

// P19: edit pool.
$( document ).on( 'click', '.bm-pool-edit', function () {
var id    = $( this ).data( 'id' );
var $edit = $( '#bm-pool-edit-' + id );
$( '#bm-pool-svcs-' + id ).hide();
if ( $edit.is( ':visible' ) ) { $edit.hide(); return; }
$edit.show();
});

$( document ).on( 'click', '.bm-save-pool-edit', function () {
var poolId = $( this ).data( 'pool-id' );
var $form  = $( this ).closest( '.bm-features-form' );
var name   = $form.find( '.bm-edit-pool-name' ).val().trim();
var cap    = parseInt( $form.find( '.bm-edit-pool-capacity' ).val(), 10 );
if ( ! name || ! cap || cap < 1 ) { alert( 'Name and capacity are required.' ); return; }
var $btn   = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'resource-pools/' + poolId, { name: name, description: $form.find( '.bm-edit-pool-desc' ).val(), total_capacity: cap } )
.done( function () {
$( '#bm-pool-edit-' + poolId ).hide();
bmLoadResourcePools();
}).fail( function () { alert( 'Failed to save pool.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Save Changes' ); });
});

$( document ).on( 'click', '.bm-pool-show-services', function () {
var id   = $( this ).data( 'id' );
var $row = $( '#bm-pool-svcs-' + id );
$( '#bm-pool-edit-' + id ).hide();
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
// P11: show service name; P6: rename column header.
var html = '<table class="bm-features-table"><thead><tr><th>Link ID</th><th>Service</th><th>Seats consumed per booking</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, s ) {
html += '<tr><td>' + s.id + '</td><td>' + $( '<span>' ).text( bmServiceName( s.service_id ) ).html() + '</td><td>' + s.capacity_used + '</td>';
html += '<td><button class="button button-small button-link-delete bm-unlink-pool-service" data-pool-id="' + poolId + '" data-svc-id="' + s.service_id + '">Unlink</button></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load services.</p>' ); });
}

$( document ).on( 'click', '.bm-add-pool-service', function () {
var poolId = $( this ).data( 'pool-id' );
var $form  = $( this ).closest( '.bm-features-form' );
// P8: use select value.
var svcId  = $form.find( '.bm-pool-svc-id' ).val();
var cap    = parseInt( $form.find( '.bm-pool-svc-cap' ).val(), 10 ) || 1;
if ( ! svcId ) { alert( 'Please select a service.' ); return; }
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
// P2/P11: show type label and service names.
var html = '<table class="bm-features-table"><thead><tr><th>ID</th><th>Service A</th><th>Service B</th><th>Type</th><th>Actions</th></tr></thead><tbody>';
$.each( rows, function ( i, c ) {
var typeLabel = ( c.chain_type === 'exclusive' || c.chain_type === 'mutual_exclusion' ) ? 'Mutual Exclusion' : 'Unknown Type';
html += '<tr>';
html += '<td>' + c.id + '</td>';
// P11: resolve names.
html += '<td>' + $( '<span>' ).text( bmServiceName( c.service_a_id ) ).html() + '</td>';
html += '<td>' + $( '<span>' ).text( bmServiceName( c.service_b_id ) ).html() + '</td>';
html += '<td>' + typeLabel + '</td>';
html += '<td>';
html += '<button class="button button-small bm-chain-edit" data-id="' + c.id + '">Edit</button> ';
html += '<button class="button button-small button-link-delete bm-chain-delete" data-id="' + c.id + '">Delete</button>';
html += '</td></tr>';
// P19: edit row allows correcting Service A and Service B references only. No status field (§1.5 has no status).
html += '<tr class="bm-child-rows" id="bm-chain-edit-' + c.id + '" style="display:none"><td colspan="5">';
html += '<div class="bm-features-form"><strong>Correct Service References</strong>';
html += '<p class="description" style="margin-bottom:8px;">Use this to correct a wrongly-assigned service reference. Chain type cannot be changed.</p>';
html += '<div class="bm-form-row"><label>Service A</label>' + bmServiceSelect( 'bm-edit-chain-svc-a', '— Select Service A —', c.service_a_id ) + '</div>';
html += '<div class="bm-form-row"><label>Service B</label>' + bmServiceSelect( 'bm-edit-chain-svc-b', '— Select Service B —', c.service_b_id ) + '</div>';
html += '<p class="bm-chain-edit-self-error" style="display:none;color:#c0392b;font-size:12px;">Service A and Service B must be different.</p>';
html += '<button class="button button-primary bm-save-chain-edit" data-chain-id="' + c.id + '">Save Changes</button>';
html += '</div></td></tr>';
});
html += '</tbody></table>';
$wrap.html( html );
}).fail( function () { $wrap.html( '<p class="bm-features-notice error">Failed to load chains.</p>' ); });
}

$( document ).on( 'click', '#bm-chains-load', function () { bmLoadServiceChains(); });

$( document ).on( 'click', '#bm-add-chain', function () {
var svcA = $( '#bm-chain-svc-a' ).val();
var svcB = $( '#bm-chain-svc-b' ).val();
// P4: client-side self-chain validation.
if ( ! svcA || ! svcB ) { alert( 'Please select both services.' ); return; }
if ( svcA === svcB ) {
$( '#bm-chain-self-error' ).show();
return;
}
$( '#bm-chain-self-error' ).hide();
// P2: chain_type is always mutual_exclusion (only one option in select).
var $btn = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'chains', { service_a_id: parseInt( svcA, 10 ), service_b_id: parseInt( svcB, 10 ) } )
.done( function () {
$( '#bm-chain-svc-a, #bm-chain-svc-b' ).val( '' );
bmLoadServiceChains();
}).fail( function ( xhr ) {
var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Failed to create service chain.';
alert( msg );
}).always( function () { $btn.prop( 'disabled', false ).text( 'Add Chain Rule' ); });
});

// P4: clear inline error when service selection changes.
$( document ).on( 'change', '#bm-chain-svc-a, #bm-chain-svc-b', function () {
$( '#bm-chain-self-error' ).hide();
});

// P19: edit chain.
$( document ).on( 'click', '.bm-chain-edit', function () {
var id    = $( this ).data( 'id' );
var $edit = $( '#bm-chain-edit-' + id );
if ( $edit.is( ':visible' ) ) { $edit.hide(); return; }
$edit.show();
});

$( document ).on( 'click', '.bm-save-chain-edit', function () {
var chainId = $( this ).data( 'chain-id' );
var $form   = $( this ).closest( '.bm-features-form' );
var svcA    = parseInt( $form.find( '.bm-edit-chain-svc-a' ).val(), 10 );
var svcB    = parseInt( $form.find( '.bm-edit-chain-svc-b' ).val(), 10 );
var $err    = $form.find( '.bm-chain-edit-self-error' );
if ( svcA && svcB && svcA === svcB ) { $err.show(); return; }
$err.hide();
var payload = {};
if ( svcA ) { payload.service_a_id = svcA; }
if ( svcB ) { payload.service_b_id = svcB; }
if ( ! Object.keys( payload ).length ) { alert( 'No changes to save.' ); return; }
var $btn    = $( this ).prop( 'disabled', true ).text( 'Saving\u2026' );
bmRestPost( 'chains/' + chainId, payload )
.done( function () {
$( '#bm-chain-edit-' + chainId ).hide();
bmLoadServiceChains();
}).fail( function () { alert( 'Failed to save chain.' ); })
.always( function () { $btn.prop( 'disabled', false ).text( 'Save Changes' ); });
});

$( document ).on( 'click', '.bm-chain-delete', function () {
var id = $( this ).data( 'id' );
if ( ! confirm( 'Delete this chain?' ) ) { return; }
bmRestDelete( 'chains/' + id ).done( function () { bmLoadServiceChains(); })
.fail( function () { alert( 'Failed to delete chain.' ); });
});

}( jQuery ));
