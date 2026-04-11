// Functions By Darpan

jQuery(document).ready(function ($) {
	// $.datepicker.setDefaults($.datepicker.regional[bm_normal_object.current_language]);

	var is_svc_search_shortcode = bm_normal_object.is_svc_search_shortcode;

	if (is_svc_search_shortcode == 1) {
		// Get all Services on page load
		bm_fetch_all_services('');
	}

	$(document).on('click', 'div.svc_search_shortcode_content a.page-numbers', function (e) {
		e.preventDefault();
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];
		$('#svc_search_shortcode_pagenum').val(pagenum ? pagenum : '1');
		bm_fetch_all_services(pagenum ? pagenum : '1');
	});

	//EVENT DELEGATION	
	$('#tab_nav > ul').on('click', 'a', function () {

		var aElement = $('#tab_nav > ul > li > a');
		var divContent = $('#tab_nav > div');

		/*Handle Tab Nav*/
		aElement.removeClass("selected current-view-type textblue");
		$(this).addClass("selected current-view-type textblue");

		/*Handle Tab Content*/
		var clicked_index = aElement.index(this);
		divContent.css('display', 'none');
		divContent.eq(clicked_index).css('display', 'block');

		$(this).blur();
		return false;
	});
});



// Get Url Param
function getUrlParameter(sParam) {
	var sPageURL = window.location.search.substring(1),
		sURLVariables = sPageURL.split('&'),
		sParameterName,
		i;

	for (i = 0; i < sURLVariables.length; i++) {
		sParameterName = sURLVariables[i].split('=');

		if (sParameterName[0] === sParam) {
			return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
		}
	}
	return false;
}



//Mobile Filter
function mobileFilter() {
	var x = document.getElementById("leftbar-modal");
	if (x.style.display == "block") {
		x.style.display = "none";
	} else {
		x.style.display = "block";
	}
}



// Show/hide Grid/List View
function showGridOrList($instance) {
	if ($instance == 'gridview') {
		jQuery('.gridview').show();
		jQuery('.listview').hide();
	} else {
		jQuery('.gridview').hide();
		jQuery('.listview').show();
	}
}



// Get all Services on page load
function bm_fetch_all_services(pagenum = '', $type = '') {
	jQuery('.gridview').html('');
	jQuery('.listview').html('');
	jQuery('.loader_modal').show();

	var booking_date = jQuery('#booking_date').val();

	if ($type == 'mobile') {
		booking_date = jQuery('#booking_date_mobile').val();
	}

	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#svc_search_shortcode_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'order': jQuery.trim(jQuery('#service_category_result_order').val()),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'date': booking_date,
	}

	var data = { 'action': 'bm_fetch_all_services', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('.gridview').html('');
		jQuery('.listview').html('');

		if (response) {
			var jsondata = jQuery.parseJSON(response);
			var active_ids = jsondata.service_ids;

			jQuery('.gridview').html(jsondata.data);
			jQuery('.listview').html(jsondata.data);
			jQuery('.pagination').html(jsondata.pagination);

			if (active_ids.length != 0) {
				var encoded_string = strict_encode(active_ids.join(','));
				jQuery('#active_services').val(encoded_string);
			}
		} else {
			jQuery('.gridview').html(bm_error_object.server_error);
			jQuery('.listview').html(bm_error_object.server_error);
		}
	});
}



// Read a page's or a string's GET URL variables and return them as an associative array.
function getUrlVars(string = '') {
	var vars = [], hash;
	if (string != '') {
		var hashes = string.slice(string.indexOf('?') + 1).split('&');
	} else {
		var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	}

	for (var i = 0; i < hashes.length; i++) {
		hash = hashes[i].split('=');
		vars.push(hash[0]);
		vars[hash[0]] = hash[1];
	}
	return vars;
}



// Encode a string
function strict_encode(string = '') {
	return btoa(encodeURIComponent(string));
}



// Decode a string
function strict_decode(string = '') {
	return decodeURIComponent(atob(string));
}



// Fetch Service Results in Booking Search Page
function bm_filter_services($this) {
	jQuery('.gridview').html('');
	jQuery('.listview').html('');
	jQuery('.loader_modal').show();
	var ids = [];

	jQuery($this).parents(".all_available_services").find("input:checked").each(function () {
		var id = jQuery(this).attr('name').split('_')[1];
		ids.push(id);
	});

	var post = {
		'pagenum': 1,
		'base': jQuery(location).attr("href"),
		'order': jQuery.trim(jQuery('#service_category_result_order').val()),
		'ids': ids,
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'date': jQuery('#booking_date').val(),
	}

	var data = { 'action': 'bm_filter_services', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('.gridview').html('');
		jQuery('.listview').html('');
		if (response) {
			var jsondata = jQuery.parseJSON(response);
			jQuery('.gridview').html(jsondata.data);
			jQuery('.listview').html(jsondata.data);
			jQuery('.pagination').html(jsondata.pagination);
		} else {
			jQuery('.gridview').html(bm_error_object.server_error);
			jQuery('.listview').html(bm_error_object.server_error);
		}
	});
}


// Fetch category Results in Booking Search Page
function bm_filter_categories($this) {
	jQuery('.gridview').html('');
	jQuery('.listview').html('');
	jQuery('.loader_modal').show();
	var cat_ids = [];
	var svc_ids = [];

	jQuery($this).parents(".all_available_categories").find("input:checked").each(function () {
		var id = jQuery(this).attr('name').split('_')[1];
		cat_ids.push(id);
	});

	jQuery("#search_by_service option:selected").each(function () {
		svc_ids.push(jQuery(this).val());
	});

	var post = {
		'pagenum': 1,
		'base': jQuery(location).attr("href"),
		'order': jQuery.trim(jQuery('#service_category_result_order').val()),
		'ids': cat_ids,
		'svc_ids': svc_ids,
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'date': jQuery('#booking_date').val(),
	}

	var data = { 'action': 'bm_filter_categories', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('.gridview').html('');
		jQuery('.listview').html('');
		if (response) {
			var jsondata = jQuery.parseJSON(response);
			jQuery('.gridview').html(jsondata.data);
			jQuery('.listview').html(jsondata.data);
			jQuery('.pagination').html(jsondata.pagination);
		} else {
			jQuery('.gridview').html(bm_error_object.server_error);
			jQuery('.listview').html(bm_error_object.server_error);
		}
	});
}



// Search Services by category
function bm_filter_service_by_category() {
	jQuery('.gridview').html('');
	jQuery('.listview').html('');
	jQuery('.loader_modal').show();

	var categories = [];
	jQuery("#search_by_category option:selected").each(function () {
		var id = jQuery(this).val();
		categories.push(id);
	});

	var post = {
		'pagenum': 1,
		'base': jQuery(location).attr("href"),
		'ids': categories,
		'order': jQuery.trim(jQuery('#service_category_result_order').val()),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'date': jQuery('#booking_date').val(),
	}

	var data = { 'action': 'bm_filter_service_by_category', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('.gridview').html('');
		jQuery('.listview').html('');
		if (response) {
			var jsondata = jQuery.parseJSON(response);
			jQuery('.gridview').html(jsondata.data);
			jQuery('.listview').html(jsondata.data);
			jQuery('.pagination').html(jsondata.pagination);
		} else {
			jQuery('.gridview').html(bm_error_object.server_error);
			jQuery('.listview').html(bm_error_object.server_error);
		}
	});
}


// Search Services by id
function bm_filter_services_by_id() {
	jQuery('.gridview').html('');
	jQuery('.listview').html('');
	jQuery('.loader_modal').show();

	var svc_ids = [];
	var cat_ids = [];

	jQuery("#search_by_service option:selected").each(function () {
		var id = jQuery(this).val();
		svc_ids.push(id);
	});

	jQuery(".all_available_categories").find("input:checked").each(function () {
		var id = jQuery(this).attr('name').split('_')[1];
		cat_ids.push(id);
	});

	var post = {
		'pagenum': 1,
		'base': jQuery(location).attr("href"),
		'ids': svc_ids,
		'cat_ids': cat_ids,
		'order': jQuery.trim(jQuery('#service_category_result_order').val()),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'date': jQuery('#booking_date').val(),
	}

	var data = { 'action': 'bm_filter_services_by_id', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('.gridview').html('');
		jQuery('.listview').html('');
		if (response) {
			var jsondata = jQuery.parseJSON(response);
			jQuery('.gridview').html(jsondata.data);
			jQuery('.listview').html(jsondata.data);
			jQuery('.pagination').html(jsondata.pagination);
		} else {
			jQuery('.gridview').html(bm_error_object.server_error);
			jQuery('.listview').html(bm_error_object.server_error);
		}
	});
}



// Fetch service gallery images
jQuery(document).on('click', '.product-gallery-btn, .gallery-btn', function (e) {
	e.preventDefault();
	jQuery('#service_gallery_images_html').html('');
	jQuery('#service_gallery_modal').addClass('active-slot');
	jQuery('#service_gallery_modal').find('.loader_modal').show();
	var post = {
		'id': jQuery(this).attr('id'),
	}

	var data = { 'action': 'bm_fetch_service_gallry_images', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('#service_gallery_images_html').html('');
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery('#service_gallery_images_html').html(jsondata.data);
			galleryCurrentSlide(1);
		} else {
			jQuery('#service_gallery_images_html').html(bm_error_object.server_error);
		}
	});
});



// Fetch Service time slots details
jQuery(document).on('click', '.get_slot_details', function (e) {
	e.preventDefault();
	jQuery('#slot_details').html('');
	jQuery('#time_slot_modal').addClass('active-slot');
	jQuery('#time_slot_modal').find('.loader_modal').show();

	var $dialog = jQuery('#timeslot-capacity-dialog');
	var isDialogOpen = $dialog.length && $dialog.dialog('instance') && $dialog.dialog('isOpen');
	if (isDialogOpen) $dialog.dialog('close');
	
	var date = jQuery('#booking_date').val();
	var service_id = jQuery(this).attr('id');

	if (jQuery(this).attr('data-mobile-date')) {
		date = jQuery(this).attr('data-mobile-date');
		sessionStorage.setItem('mobile-service-date-' + service_id, date);
	}

	if (jQuery(this).attr('data-service-date')) {
		date = jQuery(this).attr('data-service-date');
		sessionStorage.setItem('service-calendar-service-date-' + service_id, date);
	}

	if (jQuery(this).attr('data-fullcalendar-id')) {
		date = jQuery(this).attr('data-fullcalendar-id');
		sessionStorage.setItem('service-fullcalendar-service-date-' + service_id, date);
	}

	if (jQuery(this).attr('data-timeslot-fullcalendar-id')) {
		date = jQuery(this).attr('data-timeslot-fullcalendar-id');
		sessionStorage.setItem('service-timeslot-fullcalendar-service-date-' + service_id, date);
	}

	var post = {
		'date': date,
		'id': service_id,
		'type': 'home_page',
	}

	jQuery('#current_service_id').val(strict_encode(jQuery(this).attr('id')));

	var data = { 'action': 'bm_fetch_frontend_service_time_slots', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('#slot_details').html('');
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;
		var min_cap = jsondata.min_cap;
		var data = jsondata.data;
		jQuery('#total_service_booking').val(min_cap);

		if (status == false) {
			jQuery('#slot_details').html(bm_error_object.server_error);
		} else if (data != null && data != '' && status == true) {
			jQuery('#slot_details').html(data);
		} else {
			jQuery('#slot_details').html(bm_error_object.server_error);
		}
	});
});



// Fetch Service calendar and time slots details
jQuery(document).on('click', '.get_calendar_and_slot_details', function (e) {
	e.preventDefault();
	jQuery('#calendar_and_slot_details').html('');
	jQuery('#slot_modal').addClass('active-slot');
	jQuery('#slot_modal').find('.loader_modal').show();

	var date = new Date(jQuery.now());
	var today = date.getFullYear() + "-" + padWithZeros((date.getMonth() + 1)) + "-" + padWithZeros(date.getDate());

	jQuery('#booking_date2').val(today);
	jQuery('#current_service_id').val(strict_encode(jQuery(this).attr('id')));

	var post = {
		'date': today,
		'id': jQuery(this).attr('id'),
		'type': 'service_by_category',
	}

	var data = { 'action': 'bm_fetch_frontend_service_time_slots', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#calendar_and_slot_details').html('');
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;
		var min_cap = jsondata.min_cap;
		var data = jsondata.data;
		jQuery('#total_service_booking').val(min_cap);

		if (status == false) {
			jQuery('#calendar_and_slot_details').html(bm_error_object.server_error);
		} else if (data != null && data != '' && status == true) {
			jQuery('#calendar_and_slot_details').html(data);
			initiateServiceCalendar('calendar_and_slot_details');
		} else {
			jQuery('#calendar_and_slot_details').html(bm_error_object.server_error);
		}
	});
});



// Fetch checkout options
jQuery(document).on('click', '.get_checkout_options', function (e) {
	e.preventDefault();

	var no_of_persons = [];
	var i = 0;

	jQuery('[id^=extra_service_total_booking_]').each(function () {
		if (jQuery(this).val() != '' && !jQuery(this).hasClass('readonly_checkbox')) {
			no_of_persons[i] = jQuery(this).val();
			i++;
		}
	});

	jQuery('#no_of_persons').val(no_of_persons.join(','));
	jQuery('#service_id_for_checkout').val(strict_encode(this.id));

	jQuery('#checkout_options_html').html('');
	jQuery('#time_slot_modal').removeClass('active-slot');
	jQuery('#slot_modal').removeClass('active-slot');
	jQuery('#extra_service_modal').removeClass('active-slot');
	jQuery('#checkout_options_modal').addClass('active-slot');
	jQuery('#checkout_options_modal').find('.loader_modal').show();

	var $dialog = jQuery('#timeslot-capacity-dialog');
	var isDialogOpen = $dialog.length && $dialog.dialog('instance') && $dialog.dialog('isOpen');
	if (isDialogOpen) $dialog.dialog('close');

	var post = {
		'type': 'home_page',
	}

	var data = { 'action': 'bm_fetch_checkout_options', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('#checkout_options_html').html('');
		if (response.success && response.data) {
			jQuery('#checkout_options_html').html(response.data);
		} else if (!response.success && response.data) {
			jQuery('#checkout_options_html').html(response.data);
		} else {
			jQuery('#checkout_options_html').html(bm_error_object.server_error);
		}
	});
});



// Fetch checkout options
jQuery(document).on('click', '.get_svc_by_cat_checkout_options', function (e) {
	e.preventDefault();

	var no_of_persons = [];
	var i = 0;

	jQuery('[id^=extra_service_total_booking_]').each(function () {
		if (jQuery(this).val() != '' && !jQuery(this).hasClass('readonly_checkbox')) {
			no_of_persons[i] = jQuery(this).val();
			i++;
		}
	});

	jQuery('#no_of_persons').val(no_of_persons.join(','));
	jQuery('#service_id_for_checkout').val(strict_encode(this.id));

	jQuery('#checkout_options_html').html('');
	jQuery('#time_slot_modal').removeClass('active-slot');
	jQuery('#slot_modal').removeClass('active-slot');
	jQuery('#extra_service_modal').removeClass('active-slot');
	jQuery('#checkout_options_modal').addClass('active-slot');
	jQuery('#checkout_options_modal').find('.loader_modal').show();

	var post = {
		'type': 'service_by_category',
	}

	var data = { 'action': 'bm_fetch_checkout_options', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('#checkout_options_html').html('');
		if (response.success && response.data) {
			jQuery('#checkout_options_html').html(response.data);
		} else if (!response.success && response.data) {
			jQuery('#checkout_options_html').html(response.data);
		} else {
			jQuery('#checkout_options_html').html(bm_error_object.server_error);
		}
	});
});



// Fetch extra services
jQuery(document).on('click', '.get_extra_service', function (e) {
	e.preventDefault();
	jQuery('#extra_service_details').html('');
	jQuery('#time_slot_modal').removeClass('active-slot');
	jQuery('#extra_service_modal').addClass('active-slot');
	jQuery('#extra_service_modal').find('.loader_modal').show();
	var date = jQuery('#booking_date').val();
	var service_id = jQuery(this).attr('id');

	var $dialog = jQuery('#timeslot-capacity-dialog');
	var isDialogOpen = $dialog.length && $dialog.dialog('instance') && $dialog.dialog('isOpen');
	if (isDialogOpen) $dialog.dialog('close');

	if (sessionStorage.getItem('mobile-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('mobile-service-date-' + service_id);
	}

	if (sessionStorage.getItem('service-calendar-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('service-calendar-service-date-' + service_id);
	}

	if (sessionStorage.getItem('service-fullcalendar-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('service-fullcalendar-service-date-' + service_id);
	}

	if (sessionStorage.getItem('service-timeslot-fullcalendar-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('service-timeslot-fullcalendar-service-date-' + service_id);
	}

	if (jQuery(document).find('#timeslot_booking_date').length > 0) {
		date = jQuery(document).find('#timeslot_booking_date').val();
	}

	var post = {
		'date': date,
		'id': service_id,
		'type': 'home_page',
	}

	var data = { 'action': 'bm_fetch_extra_service', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('#extra_service_details').html('');
		if (response != null && response != '') {
			jQuery('#extra_service_details').html(response);
		} else {
			jQuery('#extra_service_details').html(bm_error_object.server_error);
		}
	});
});



// Fetch extra services
jQuery(document).on('click', '.get_svc_by_cat_extra_service', function (e) {
	e.preventDefault();
	jQuery('#extra_service_details').html('');
	jQuery('#slot_modal').removeClass('active-slot');
	jQuery('#extra_service_modal').addClass('active-slot');
	jQuery('#extra_service_modal').find('.loader_modal').show();

	var post = {
		'date': jQuery('#booking_date2').val(),
		'id': jQuery(this).attr('id'),
		'type': 'service_by_category',
	}

	var data = { 'action': 'bm_fetch_extra_service', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery('#extra_service_details').html('');
		if (response != null && response != '') {
			jQuery('#extra_service_details').html(response);
		} else {
			jQuery('#extra_service_details').html(bm_error_object.server_error);
		}
	});
});



// Redirect to checkout form
jQuery(document).on('click', '.get_checkout_form', function (e) {
	e.preventDefault();

	let modal;
	if (jQuery('#time_slot_modal').hasClass('active-slot')) {
		modal = jQuery('#time_slot_modal');
	} else if (jQuery('#slot_modal').hasClass('active-slot')) {
		modal = jQuery('#slot_modal');
	} else {
		modal = jQuery('#flexi_checkout_options').length > 0 ? jQuery('#checkout_options_modal') : jQuery('#extra_service_modal');
	}

	localStorage.setItem('booking_url', location.href);

	const modal_body = modal.find('.modal-body');

	var $dialog = jQuery('#timeslot-capacity-dialog');
	var isDialogOpen = $dialog.length && $dialog.dialog('instance') && $dialog.dialog('isOpen');
	var $currentLoader = isDialogOpen 
		? $dialog.find('.loader_modal') 
		: modal.find('.loader_modal');
	var $targetContent = isDialogOpen 
		? $dialog.find('.timeslot-dialog-content') 
		: modal_body;

	if (isDialogOpen) $targetContent.find('.timeslot-dialog-footer').hide();

	$currentLoader.show();

	var no_of_persons = [];
	var i = 0;

	jQuery('[id^=extra_service_total_booking_]').each(function () {
		if (jQuery(this).val() != '' && !jQuery(this).hasClass('readonly_checkbox')) {
			no_of_persons[i] = jQuery(this).val();
			i++;
		}
	});

	var total_service_booking = 0;
	if (jQuery(document).find('#timeslot-counter').length > 0) {
		total_service_booking = jQuery('#timeslot-counter').val();
	} else {
		total_service_booking = jQuery('#total_service_booking').val();
	}

	var date = jQuery('#booking_date').val();
	var service_id = jQuery(this).attr('id') ? jQuery(this).attr('id') : 0;
	service_id = service_id ? service_id : strict_decode(jQuery('#service_id_for_checkout').val())

	if (sessionStorage.getItem('mobile-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('mobile-service-date-' + service_id);
		sessionStorage.removeItem('mobile-service-date-' + service_id);
	}

	if (sessionStorage.getItem('service-calendar-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('service-calendar-service-date-' + service_id);
		sessionStorage.removeItem('service-calendar-service-date-' + service_id);
	}

	if (sessionStorage.getItem('service-fullcalendar-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('service-fullcalendar-service-date-' + service_id);
		sessionStorage.removeItem('service-fullcalendar-service-date-' + service_id);
	}

	if (sessionStorage.getItem('service-timeslot-fullcalendar-service-date-' + service_id) != null) {
		date = sessionStorage.getItem('service-timeslot-fullcalendar-service-date-' + service_id);
		sessionStorage.removeItem('service-timeslot-fullcalendar-service-date-' + service_id);
	}

	if (jQuery(document).find('#timeslot_booking_date').length > 0) {
		date = jQuery(document).find('#timeslot_booking_date').val();
	}

	var post = {
		'time_slot': jQuery('#selected_slot').val(),
		'date': date,
		'id': service_id,
		'total_service_booking': total_service_booking,
		'extra_svc_ids': jQuery('#selected_extra_service_ids').val(),
		'no_of_persons': no_of_persons.length != 0 ? no_of_persons.join(',') : jQuery('#no_of_persons').val(),
		'checkout_option': jQuery('#flexi_checkout_options').length > 0 ? jQuery('#flexi_checkout_options').val() : '',
		'type': 'home_page',
	}

	jQuery(modal_body).html('');

	var data = { 'action': 'bm_fetch_order_info_and_redirect_to_checkout', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		$currentLoader.hide();
		
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;
		var data = jsondata.data;

		if (data != null && data != '' && status == 'error') {
			$targetContent.html(data);
		} else if (data != null && data != '' && status == 'success') {
			$targetContent.html('<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' + bm_normal_object.moving_to_checkout + '</p></div>');
			
			// if (isDialogOpen) $dialog.dialog('close');
			
			window.location.href = data;
		} else {
			$targetContent.html(bm_error_object.server_error);
		}
	});
});



// Redirect to checkout form
jQuery(document).on('click', '.get_svc_by_cat_checkout_form', function (e) {
	e.preventDefault();

	let modal;
	if (jQuery('#time_slot_modal').hasClass('active-slot')) {
		modal = jQuery('#time_slot_modal');
	} else if (jQuery('#slot_modal').hasClass('active-slot')) {
		modal = jQuery('#slot_modal');
	} else {
		modal = jQuery('#flexi_checkout_options').length > 0 ? jQuery('#checkout_options_modal') : jQuery('#extra_service_modal');
	}

	localStorage.setItem('booking_url', location.href);

	const modal_body = modal.find('.modal-body');
	modal.find('.loader_modal').show();

	var no_of_persons = [];
	var i = 0;

	jQuery('[id^=extra_service_total_booking_]').each(function () {
		if (jQuery(this).val() != '' && !jQuery(this).hasClass('readonly_checkbox')) {
			no_of_persons[i] = jQuery(this).val();
			i++;
		}
	});

	var post = {
		'time_slot': jQuery('#selected_slot').val(),
		'date': jQuery('#booking_date2').val(),
		'id': jQuery(this).attr('id') ? jQuery(this).attr('id') : strict_decode(jQuery('#service_id_for_checkout').val()),
		'total_service_booking': jQuery('#total_service_booking').val(),
		'extra_svc_ids': jQuery('#selected_extra_service_ids').val(),
		'no_of_persons': no_of_persons.length != 0 ? no_of_persons.join(',') : jQuery('#no_of_persons').val(),
		'checkout_option': jQuery('#flexi_checkout_options').length > 0 ? jQuery('#flexi_checkout_options').val() : '',
		'type': 'service_by_category',
	}

	jQuery(modal_body).html('');

	var data = { 'action': 'bm_fetch_order_info_and_redirect_to_checkout', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;
		var data = jsondata.data;

		if (data != null && data != '' && status == 'error') {
			jQuery(modal_body).html(data);
		} else if (data != null && data != '' && status == 'success') {
			// jQuery(modal).removeClass('active-slot');
			jQuery(modal_body).html('<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' + bm_normal_object.moving_to_checkout + '</p></div>');
			window.location.href = data;
		} else {
			jQuery(modal_body).html(bm_error_object.server_error);
		}
	});
});



// Highlight time slot on selection
jQuery(document).on('click', '#slot_value', function () {
	$this = jQuery(this);
	var frontend_button_background_colur = bm_normal_object.svc_button_colour;
	var frontend_button_text_colur = bm_normal_object.svc_btn_txt_colour;

	jQuery(this).parent().children()
		.removeClass('bgcolor bordercolor textwhite')
		.css('cssText', 'background-color: initial !important; color: initial !important;');
	jQuery(this)
		.addClass('bgcolor bordercolor textwhite')
		.css('cssText', `background-color: ${frontend_button_background_colur} !important; color: ${frontend_button_text_colur} !important;`);

	var time_slot_value = jQuery(this).find('.slot_value_text').text();
	jQuery('#selected_slot').val(time_slot_value);

	jQuery(document).find('.service_selection_div').html('');
	jQuery(document).find('.service_selection_div').show();
	jQuery($this).parents('.modal').find('.loader_modal').css('top', 'initial');
	jQuery($this).parents('.modal').find('.loader_modal').show();

	var post = {
		'capacity_left': jQuery(this).find('.slot_count_text').data('capacity'),
		'mincap': jQuery(this).find('.slot_count_text').data('mincap'),
		'id': strict_decode(jQuery('#current_service_id').val()),
	};

	var data = { 'action': 'bm_fetch_service_selection', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		jQuery(document).find('.service_selection_div').html('');

		if (response != null && response != '') {
			jQuery(document).find('.service_selection_div').append(response);

			if (jQuery(document).find('.service_selection_div .terms_required_errortext').length > 0) {
				jQuery('#total_service_booking').val('0');
				jQuery($this).parents('.slot_box_modal').find('#select_slot_button')
					.addClass('readonly_div')
					.removeClass('bgcolor textwhite text-center')
					.css('cssText', 'background-color: initial !important; color: initial !important;');
				jQuery($this).parents('.slot_box_modal').find('#select_slot_button').children().addClass('inactiveLink');
			} else {
				jQuery('#total_service_booking').val(jQuery(document).find('.service_total_booking').val());
				jQuery($this).parents('.slot_box_modal').find('#select_slot_button')
					.removeClass('readonly_div')
					.addClass('bgcolor textwhite text-center')
					.css('cssText', `background-color: ${frontend_button_background_colur} !important; color: ${frontend_button_text_colur} !important;`);
				jQuery($this).parents('.slot_box_modal').find('#select_slot_button').children().removeClass('inactiveLink');
			}
		} else {
			jQuery(document).find('.service_selection_div').append(bm_error_object.server_error);
		}

		jQuery($this).parent('.modalcontentbox').animate({
			scrollTop: jQuery(".service_selection_div").offset().top
		}, 2000);
	});
});



// Change value on service selection
jQuery(document).on('change', '.service_total_booking', function () {
	jQuery('#total_service_booking').val('');
	jQuery('#total_service_booking').val(jQuery(this).val());
});



// Highlight next button on extra service selection
jQuery(document).on('change', '.listed_extra_service', function () {
	var ids = [];
	var type = '';

	jQuery(".extra_services_available input:checked").each(function () {
		ids.push(jQuery(this).attr('id'));
	});

	var $cancelBtn = jQuery(this).parents('div.extra_service_results').find('.cancelbtn');

	if ($cancelBtn.data('type')) {
		type = $cancelBtn.data('type');
	} else {
		if ($cancelBtn.hasClass('get_checkout_form')) {
			type = 'service_shortcode';
			$cancelBtn.data('type', type);
		} else if ($cancelBtn.hasClass('get_svc_by_cat_checkout_form')) {
			type = 'service_by_cat_shortcode';
			$cancelBtn.data('type', type);
		} else if ($cancelBtn.hasClass('get_checkout_options')) {
			type = 'service_options';
			$cancelBtn.data('type', type);
		} else if ($cancelBtn.hasClass('get_svc_by_cat_checkout_options')) {
			type = 'service_by_cat_options';
			$cancelBtn.data('type', type);
		}
	}

	if (jQuery(this).is(':checked')) {
		jQuery(this).parents('div.extra_service_content')
			.find('div.extra_service_booking_no').removeClass('readonly_cursor')
			.find('.extra_service_total_booking').removeClass('readonly_checkbox');
	} else {
		jQuery(this).parents('div.extra_service_content')
			.find('div.extra_service_booking_no').addClass('readonly_cursor')
			.find('.extra_service_total_booking').addClass('readonly_checkbox');
	}

	var frontend_button_background_colur = bm_normal_object.svc_button_colour;
	var frontend_button_text_colur = bm_normal_object.svc_btn_txt_colour;

	if (ids.length !== 0) {
		jQuery(this).parents('div.extra_service_results')
			.find('.bookbtn')
			.removeClass('readonly_div')
			.addClass('bgcolor textwhite text-center')
			.css('cssText', `background-color: ${frontend_button_background_colur} !important; color: ${frontend_button_text_colur} !important;`)
			.children()
			.removeClass('inactiveLink');

		switch (type) {
			case 'service_shortcode':
				$cancelBtn.addClass('readonly_div').removeClass('get_checkout_form');
				break;
			case 'service_by_cat_shortcode':
				$cancelBtn.addClass('readonly_div').removeClass('get_svc_by_cat_checkout_form');
				break;
			case 'service_options':
				$cancelBtn.addClass('readonly_div').removeClass('get_checkout_options');
				break;
			case 'service_by_cat_options':
				$cancelBtn.addClass('readonly_div').removeClass('get_svc_by_cat_checkout_options');
				break;
		}

		$cancelBtn.css('cssText', `background-color: '' !important; color: '' !important;`);

		jQuery('#selected_extra_service_ids').val(ids.join(","));
	} else {
		jQuery(this)
			.parents('div.extra_service_results')
			.find('.bookbtn')
			.removeClass('bgcolor bordercolor textwhite')
			.addClass('readonly_div')
			.css('cssText', 'background-color: initial !important; color: initial !important;')
			.children()
			.addClass('inactiveLink');

		switch (type) {
			case 'service_shortcode':
				$cancelBtn.removeClass('readonly_div').addClass('get_checkout_form');
				break;
			case 'service_by_cat_shortcode':
				$cancelBtn.removeClass('readonly_div').addClass('get_svc_by_cat_checkout_form');
				break;
			case 'service_options':
				$cancelBtn.removeClass('readonly_div').addClass('get_checkout_options');
				break;
			case 'service_by_cat_options':
				$cancelBtn.removeClass('readonly_div').addClass('get_svc_by_cat_checkout_options');
				break;
		}

		$cancelBtn.css('cssText', `background-color: ${frontend_button_background_colur} !important; color: ${frontend_button_text_colur} !important;`);

		jQuery('#selected_extra_service_ids').val('');
	}
});



// Cancel button on booking form
jQuery(document).on('click', '#cancel_booking', function (e) {
	e.preventDefault();
	closeModal('user_form_modal');
});



// Cancel button on checkout form
jQuery(document).on('click', '#booking_home', function (e) {
	location.href = localStorage.getItem('booking_url');
});



// Confirm order and fetch details from booking form
jQuery(document).on('click', '#confirm_booking', function (e) {
	e.preventDefault();
	jQuery(this).parents('#user_form_modal').find('.loader_modal').css('top', 'initial');
	jQuery(this).parents('#user_form_modal').find('.loader_modal').show();

	var formData = {};
	var fieldData = {};
	var otherData = {};

	jQuery('#booking_form :input').map(function () {
		validateFields(jQuery(this));

		var type = jQuery(this).prop("type");
		var contentFolder = jQuery(this).attr("id").startsWith('sgbm_field_') ? fieldData : otherData;

		if ((type == "checkbox")) {
			if (this.checked) {
				contentFolder[jQuery(this).attr('id')] = 1;
			} else {
				contentFolder[jQuery(this).attr('id')] = 0;
			}
		} else if ((type == "radio")) {
			if (this.checked) contentFolder[jQuery(this).attr('id')] = jQuery(this).val();
		} else if ((type == "tel" && jQuery(this).hasClass('intl_phone_field_input'))) {
			var country_text = jQuery(document).find("div.iti__selected-flag div:first-child").attr('class');
			var country_code = country_text.split('_').pop();
			otherData['country_code'] = country_code;
			var intl_code = jQuery(document).find(".iti__selected-dial-code").text();
			contentFolder[jQuery(this).attr('id')] = intl_code + jQuery(this).val();
		} else {
			contentFolder[jQuery(this).attr('id')] = jQuery(this).val();
		}
	});

	if (jQuery('#booking_form .required_errortext').length > 0 || jQuery('#booking_form .terms_required_errortext').length > 0 || jQuery('#booking_form .checkbox_required_errortext').length > 0) {
		jQuery('.loader_modal').hide();
		return false;
	} else {
		formData['field_data'] = fieldData;
		formData['other_data'] = otherData;

		var data = { 'action': 'bm_fetch_booking_data', 'post': formData, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			jQuery('.loader_modal').hide();
			jQuery('#user_form_modal').removeClass('active-slot');
			jQuery('#user_form').html('');
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status;
			var data = jsondata.data;

			if (data != null && data != '' && status == 'error') {
				jQuery('#booking_detail').html('');
				jQuery('#booking_detail').html(data);
				jQuery('#booking_detail_modal').addClass('active-slot');
			} else if (data != null && data != '' && status == 'success') {
				window.location.href = data;
			} else {
				jQuery('#booking_detail').html('');
				jQuery('#booking_detail').html(bm_error_object.server_error);
				jQuery('#booking_detail_modal').addClass('active-slot');
			}
		});
	}
});



// Validation for dynamic fields
function validateFields($this = '') {

	if ($this != '') {
		var type = jQuery($this).prop('type');
		var required = jQuery($this).prop("required");
		var visiblity = jQuery($this).parent().parent().is(':visible');

		if (type !== 'hidden' && type !== 'button' && type !== 'submit' && type !== 'reset' && type !== 'search') {
			switch (type) {

				case 'checkbox':
					var checked = $this.is(':checked');

					if (jQuery($this).attr('name') == 'terms_conditions') {
						jQuery($this).parent().find('div.terms_required_errortext').remove();
						if (checked == false) jQuery($this).next('label').after('<div class="terms_required_errortext">' + bm_error_object.term_co + '</div>');
					} else if (jQuery($this).attr('name') == 'terms_conditions1') {
						jQuery($this).parent().find('div.terms_required1_errortext').remove();
						if (checked == false) jQuery($this).next('label').after('<div class="terms_required1_errortext">' + bm_error_object.term_co + '</div>');
					} else if (visiblity == true && required == true) {
						jQuery($this).parents('.checkbox_and_radio_div').find('div.checkbox_required_errortext').remove();
						if (checked == false) {
							if (jQuery($this).parents('.checkbox_and_radio_div').find('div.checkbox_required_errortext').length == 0) {
								jQuery($this).parents('.checkbox_and_radio_div').append('<div class="checkbox_required_errortext">' + bm_error_object.required + '</div>');
							}
						}
					}
					break;

				case 'radio':
					jQuery($this).parents('.checkbox_and_radio_div').find('div.checkbox_required_errortext').remove();

					var checked = $this.is(':checked');

					if (visiblity == true && required == true && checked == false) {
						if (jQuery($this).parents('.checkbox_and_radio_div').find('div.checkbox_required_errortext').length == 0) {
							jQuery($this).parents('.checkbox_and_radio_div').append('<div class="checkbox_required_errortext">' + bm_error_object.required + '</div>');
						}
					}
					break;

				case 'select':
					jQuery($this).parents('.checkbox_and_radio_div').find('div.checkbox_required_errortext').remove();

					var selected = $this.is(':selected');

					if (selected == false && visiblity == true && required == true) {
						if (jQuery($this).parents('.checkbox_and_radio_div').find('div.checkbox_required_errortext').length == 0) {
							jQuery($this).parents('.checkbox_and_radio_div').append('<div class="checkbox_required_errortext">' + bm_error_object.required + '</div>');
						}
					}
					break;

				case 'email':
					jQuery($this).parent().find('div.required_errortext').remove();

					var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;
					var value = jQuery($this).val();

					if (visiblity == true) {
						if (required == true) {
							if (value == '') {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.required + '</div>');
							} else if (!pattern.test(value)) {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_email + '</div>');
							}
						} else if (value != '' && !pattern.test(value)) {
							jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_email + '</div>');
						}
					}
					break;

				case 'tel':
					jQuery($this).parent().find('div.required_errortext').remove();

					var pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
					var value = jQuery($this).val();

					if (visiblity == true) {
						if (required == true) {
							if (value == '') {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.required + '</div>');
							} else if (!pattern.test(value)) {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_contact + '</div>');
							}
						} else if (value != '' && !pattern.test(value)) {
							jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_contact + '</div>');
						}
					}
					break;

				case 'url':
					jQuery($this).parent().find('div.required_errortext').remove();

					var pattern = /^((https?|ftp|smtp):\/\/)?(www.)?[a-z0-9]+\.[a-z]+(\/[a-zA-Z0-9#]+\/?)*$/;
					var value = jQuery($this).val();

					if (visiblity == true) {
						if (required == true) {
							if (value == '') {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.required + '</div>');
							} else if (!pattern.test(value)) {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_url + '</div>');
							}
						} else if (value != '' && !pattern.test(value)) {
							jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_url + '</div>');
						}
					}
					break;

				case 'password':
					jQuery($this).parent().find('div.required_errortext').remove();

					var pattern = /^(?=.*[A-Z].*[A-Z])(?=.*[!@#$&*])(?=.*[0-9].*[0-9])(?=.*[a-z].*[a-z].*[a-z]).{8}$/;
					var value = jQuery($this).val();

					if (visiblity == true) {
						if (required == true) {
							if (value == '') {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.required + '</div>');
							} else if (!pattern.test(value)) {
								jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_password + '</div>');
							}
						} else if (value != '' && !pattern.test(value)) {
							jQuery($this).after('<div class="required_errortext">' + bm_error_object.invalid_password + '</div>');
						}
					}
					break;

				case 'text':
				case 'date':
				case 'time':
				case 'datetime':
				case 'month':
				case 'week':
				case 'number':
				case 'textarea':
					jQuery($this).parent().find('div.required_errortext').remove();

					var value = jQuery($this).val();

					if (visiblity == true) {
						if (required == true && value == '') {
							jQuery($this).after('<div class="required_errortext">' + bm_error_object.required + '</div>');
						}
					}
					break;

				default:
					jQuery($this).parent().find('div.required_errortext').remove();

					var value = jQuery($this).val();

					if (visiblity == true) {
						if (required == true && value == '') {
							jQuery($this).after('<div class="required_errortext">' + bm_error_object.required + '</div>');
						}
					}
					break;
			}

		}
	}
}



// Close modal
function closeModal(id) {
	// jQuery('#' + id).removeClass('active-slot');

	var modal = jQuery('#' + id);

	modal.animate({ top: "-=100px" }, 300, function () {
		modal.css({ top: "" });
		modal.removeClass('active-slot');
	});
}



// Close modal
jQuery(document).on('click', '#close_modal', function (e) {
	e.preventDefault();
	var modal = jQuery(this).parents('.modaloverlay');

	modal.animate({ top: "-=100px" }, 300, function () {
		modal.css({ top: "" });
		modal.removeClass('active-slot');
	});
});



// Close booking_details
jQuery(document).on('click', '#close_booking_details', function (e) {
	e.preventDefault();
	closeModal('booking_detail_modal');
});



// Get form data
function getFormData(formId) {
	var formData = {};
	var inputs = jQuery('#' + formId).serializeArray();

	jQuery.each(inputs, function (i, input) {
		formData[input.name] = input.value;
	});

	return formData;
}



// Edit slot selection
jQuery(document).on('click', '.edit_slot_selection', function () {
	jQuery('#selected_extra_service_ids').val('');
	jQuery(this).parents('.modaloverlay ').removeClass('active-slot');
	jQuery('#time_slot_modal').addClass('active-slot');
});



// Edit slot selection
jQuery(document).on('click', '.edit_svc_by_cat_slot_selection', function () {
	jQuery('#selected_extra_service_ids').val('');
	jQuery(this).parents('.modaloverlay ').removeClass('active-slot');
	jQuery('#slot_modal').addClass('active-slot');
});



// Show full service description in frontend
jQuery(document).on('click', '.service-desc-fa', function (e) {
	e.preventDefault();
	var svc_button_colour = bm_normal_object.svc_button_colour;
	var svc_btn_txt_colour = bm_normal_object.svc_btn_txt_colour;
	var svc_title = jQuery(this).parents('.main-parent').find('.service_full_description').data('title');

	if (!svc_title || svc_title == 'undefined') {
		svc_title = jQuery(this).parents('.main-parent').find('.fc-title').attr('title');
	}

	// full description dialog
	var dialog = jQuery(this).parents('.main-parent').find('.service_full_description').dialog({
		autoOpen: false,
		resizable: false,
		draggable: false,
		title: svc_title + ' ' + bm_normal_object.svc_full_desc,
		height: 400,
		width: 800,
		modal: true,
		show: {
			effect: "bounce",
			duration: 1000
		},
		hide: {
			effect: "slide",
			direction: 'up',
			duration: 1000
		},
		close: function () {
			jQuery(this).dialog("destroy");
		},
		buttons: [{
            text: "Ok",
            click: function() {
                jQuery(this).dialog("destroy");
            }
        }],
        open: function() {
            var button = jQuery(this).parent().find('.ui-dialog-buttonset button');
            
            var styleId = 'dialog-button-style-' + Date.now();
            jQuery('head').append(`
                <style id="${styleId}">
                    .ui-dialog .ui-dialog-buttonpane button.ui-button {
                        color: ${svc_btn_txt_colour} !important;
                        background: ${svc_button_colour} !important;
                        border-color: ${svc_button_colour} !important;
                    }
                    .ui-dialog .ui-dialog-buttonpane button.ui-button:hover,
                    .ui-dialog .ui-dialog-buttonpane button.ui-button:focus {
                        background: ${svc_button_colour} !important;
                        opacity: 0.9 !important;
                    }
                    .ui-dialog .ui-dialog-buttonpane button.ui-button:active {
                        background: ${svc_button_colour} !important;
                        opacity: 0.8 !important;
                    }
                </style>
            `);
            
            // Remove the style when dialog is closed
            jQuery(this).on('dialogclose', function() {
                jQuery('#' + styleId).remove();
            });
        }
	});

	// Open dialog
	dialog.dialog("open");
});



// Tooltip
if (window.innerWidth >= 1025) {
	jQuery(document).ready(function ($) {
	$(document).tooltip({
		position: {
			my: "center bottom-10",
			at: "center top",
			using: function (position, feedback) {
				$(this).css(position);
				$("<div>")
					.addClass("arrow")
					.addClass(feedback.vertical)
					.addClass(feedback.horizontal)
					.appendTo(this);
			}
		}
	});
});

}
else{
jQuery(document).ready(function ($) {
	 // Only on mobile & tablet screens (max-width 1025px)
		$(document).tooltip({
			items: "[title]:not(.no-tooltip):not(input[type='checkbox'])",
			position: {
				my: "center bottom-10",
				at: "center top",
				using: function (position, feedback) {
					$(this).css(position);
					$("<div>")
						.addClass("arrow")
						.addClass(feedback.vertical)
						.addClass(feedback.horizontal)
						.appendTo(this);
				}
			}
		});
	
});
}



//International tel input for phone form fields
function setIntlInput(formID) {
	jQuery('#' + formID + ' :input').map(function () {
		var type = jQuery(this).prop("type");
		var id = jQuery(this).attr("id");

		if ((type == "tel") && jQuery(this).hasClass('intl_phone_field_input')) {
			jQuery("#" + id).intlTelInput({
				initialCountry: bm_normal_object.booking_country,
				separateDialCode: true,
				autoInsertDialCode: true,
				showFlags: true,
				utilsScript: bm_intl_script.script_url
			});
		}
	});
}



// Get all Services by categories on page load
function bm_fetch_all_services_by_categories() {
	var data = { 'action': 'bm_fetch_all_services_by_categories', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.service_by_category_gridview').html('');
		jQuery('.service_by_category_gridview slider1').html('');

		if (response) {
			var jsondata = jQuery.parseJSON(response);
			jQuery('.service_by_category_gridview').html(jsondata.data);
			jQuery('.service_by_category_gridview slider1').html(jsondata.data);

		} else {
			jQuery('.service_by_category_gridview').html(bm_error_object.server_error);
			jQuery('.service_by_category_gridview slider1').html(bm_error_object.server_error);
		}
	});
}



// Pad those with no leading zeroes
function padWithZeros(number) {
	var lengthOfNumber = (parseInt(number) + '').length;
	if (lengthOfNumber == 2) return number;
	else if (lengthOfNumber == 1) return '0' + number;
	else if (lengthOfNumber == 0) return '00';
	else return false;
}



if (bm_normal_object.current_page_title == "Flexibooking Checkout") {
	setIntlInput('checkout_form');
}



// Load Price Calendar
function initiateServiceCalendar($container, service_id = 0, $this = '') {
	let $datepicker = jQuery('#' + $container).find('.service_by_category_calendar');

	if ($container == 'service_calendar_details') {
		$datepicker = jQuery($this).find('.service-by-id-calendar');
	}

	$datepicker.datepicker({
		dateFormat: 'yy-mm-dd',
		minDate: 0,
		//---^----------- if closed by default (when you're using <input>)
		beforeShowDay: function (date) {
			var returnday = "weekday";
			return [true, returnday];
		},
		onChangeMonthYear: function () { bm_get_service_price($this, $container, service_id) },
		onSelect: function (date, inst) {
			if ($container == 'calendar_and_slot_details') {
				jQuery(this).parents('.modal').find('.loader_modal').css('top', 'initial');
				jQuery(this).parents('.modal').find('.loader_modal').show();
			}

			jQuery($datepicker).find('.ui-datepicker-calendar td').removeClass('custom-highlight');
			var selectedDateElement = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
				return jQuery(this).find('a').text() == inst.selectedDay;
			});

			if (selectedDateElement.length) {
				selectedDateElement.addClass('custom-highlight');
			}

			if (!service_id) {
				service_id = strict_decode(jQuery('#current_service_id').val());
			}

			if ($container == 'calendar_and_slot_details') {
				jQuery('#booking_date2').val(date);
			} else {
				jQuery('.loader_modal').show();
				jQuery('.calendar_shortcode_error_message').html('');
				jQuery(this).parents('.' + $container).find('.booknowbtn').children('a').attr('data-service-date', date);
			}

			if ($container == 'calendar_and_slot_details') {
				jQuery('#' + $container).find('div.calender-modal .booking-status .selected_date_div').text(jQuery.datepicker.formatDate('DD, MM dd, yy', new Date(date)));
			}

			var element = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
				return jQuery(this).find('a').text() == inst.selectedDay;
			}).find("a", "span");

			if (!element.hasClass('not_available_for_booking')) {
				var post = {
					'date': date,
					'id': service_id,
					'type': $container == 'calendar_and_slot_details' ? 'service_by_category2' : '',
				}

				if ($container == 'calendar_and_slot_details') {
					var data = { 'action': 'bm_fetch_frontend_service_time_slots', 'post': post, 'nonce': bm_ajax_object.nonce };
					jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
						jQuery('.loader_modal').hide();
						jQuery('#' + $container).find('.modalcontentbox').html('');
						var jsondata = jQuery.parseJSON(response);
						var status = jsondata.status ? jsondata.status : '';
						var data = jsondata.data ? jsondata.data : '';

						if (status == false) {
							jQuery('#' + $container).find('.modalcontentbox').html('<div class="no_slots_class">' + bm_error_object.server_error + '</div>');
						} else if (data != null && data != '' && status == true) {
							jQuery('#' + $container).find('.modalcontentbox').html(data);
						} else {
							jQuery('#' + $container).find('.modalcontentbox').html('<div class="no_slots_class">' + bm_error_object.server_error + '</div>');
						}
					});
				} else {
					const data = {
						action: 'bm_fetch_service_calendar_time_slots',
						post: post,
						nonce: bm_ajax_object.nonce
					};

					const $btn = jQuery(this).parents('.' + $container).find('.booknowbtn');
					const $link = $btn.children('a');
					const disabledColor = '#d3d3d3';

					const btnBgColor = bm_normal_object.svc_button_colour;

					jQuery.post(bm_ajax_object.ajax_url, data)
						.done(function (response) {
							jQuery('.loader_modal').hide();
							if (response.success && response.data.is_bookable) {
								$btn.removeClass('readonly_div');
								$link.removeClass('inactiveLink');
								$btn.addClass('textblue bordercolor');
								$link.addClass('get_slot_details');
								$btn.attr('style', `background-color: ${btnBgColor} !important;`);
							} else {
								$btn.removeClass('textblue bordercolor');
								$link.removeClass('get_slot_details');
								$btn.addClass('readonly_div');
								$link.addClass('inactiveLink');
								$btn.attr('style', `background-color: ${disabledColor} !important;`);

								const errorMessage = response.data.message ? response.data.message : bm_error_object.server_error;
								$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(errorMessage);
							}
						})
						.fail(function () {
							jQuery('.loader_modal').hide();
							$btn.removeClass('textblue bordercolor');
							$link.removeClass('get_slot_details');
							$btn.addClass('readonly_div');
							$link.addClass('inactiveLink');
							$btn.attr('style', `background-color: ${disabledColor} !important;`);
							$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(bm_error_object.server_error);
						});
				}
			} else {
				jQuery('.loader_modal').hide();
				if ($container == 'calendar_and_slot_details') {
					jQuery('#' + $container).find('.modalcontentbox').html('<div class="no_slots_class">' + bm_error_object.service_unavailable + '</div>');
				} else {
					const $btn = jQuery(this).parents('.' + $container).find('.booknowbtn');
					const $link = $btn.children('a');
					const disabledColor = '#d3d3d3';

					$btn.removeClass('textblue bordercolor');
					$link.removeClass('get_slot_details');
					$btn.addClass('readonly_div');
					$link.addClass('inactiveLink');
					$btn.attr('style', `background-color: ${disabledColor} !important;`);
					$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(bm_error_object.service_unavailable);
				}
			}

			//Prevent the redraw.
			inst.inline = false;
		},
	});

	var today = new Date();
	var todayElement = jQuery($datepicker).find('.ui-datepicker-calendar td[data-year="' + today.getFullYear() + '"][data-month="' + today.getMonth() + '"]').filter(function () {
		return jQuery(this).find('a').text() == today.getDate();
	});

	if (todayElement.length) {
		todayElement.addClass('custom-highlight');
	}

	bm_get_service_price($this, $container, service_id); // if open by default (when you're using <div>)
}



// Variable Service Price Ajax on service calendar Load
// function bm_get_service_price($this = '', $container, service_id = 0) {
// 	jQuery('#' + $container).find('.front_calendar_errortext').html('');
// 	var currency_symbol = bm_normal_object.currency_symbol;
// 	var currency_position = bm_normal_object.currency_position;

// 	if (!service_id) {
// 		service_id = strict_decode(jQuery('#current_service_id').val());
// 	}

// 	var data = { 'action': 'bm_get_frontend_service_prices', 'id': service_id, 'nonce': bm_ajax_object.nonce };
// 	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
// 		var jsondata = jQuery.parseJSON(response);
// 		var status = jsondata.status;
// 		if (status == true) {
// 			var default_price = jsondata.default_price ?? '';
// 			var variable_price_obj = jsondata.variable_price.price ? jsondata.variable_price.price : '';
// 			var variable_price_date_obj = jsondata.variable_price.date ? jsondata.variable_price.date : '';
// 			var variable_module_obj = jsondata.variable_module.module ? jsondata.variable_module.module : '';
// 			var variable_module_date_obj = jsondata.variable_module.date ? jsondata.variable_module.date : '';
// 			var unavailability = jsondata.unavailability ? jsondata.unavailability : '';
// 			var unavailability_dates = jsondata.unavailability.dates ? jsondata.unavailability.dates : '';
// 			var unavailability_weekdays = jsondata.unavailability.weekdays ? jsondata.unavailability.weekdays : '';
// 			var price_text = '';
// 			var price_array = '';
// 			var price_date_array = '';
// 			var module_date_array = '';
// 			var unavailable_days_array = '';
// 			var weekdays_array = '';

// 			if (variable_price_obj != '' && variable_price_date_obj != '') {
// 				var price_array = Object.keys(variable_price_obj).map(function (key) { return variable_price_obj[key]; });
// 				var price_date_array = Object.keys(variable_price_date_obj).map(function (key) { return variable_price_date_obj[key]; });
// 			}

// 			if (variable_module_obj != '' && variable_module_date_obj != '') {
// 				var module_date_array = Object.keys(variable_module_date_obj).map(function (key) { return variable_module_date_obj[key]; });
// 			}

// 			if (unavailability != '') {
// 				if (unavailability_dates !== '') {
// 					var unavailable_days_array = Object.keys(unavailability_dates).map(function (key) { return unavailability_dates[key]; });
// 				}

// 				if (unavailability_weekdays !== '') {
// 					var weekdays_array = Object.keys(unavailability_weekdays).map(function (key) { return unavailability_weekdays[key]; });
// 				}
// 			}

// 			var calendar = $container == 'calendar_and_slot_details' ? 'service_by_category_calendar' : 'service-by-id-calendar';

// 			setTimeout(function () {
// 				var calendarElement = $this ? jQuery($this).find('.' + calendar) : jQuery('.' + calendar);
// 				calendarElement.datepicker().find(".ui-datepicker-calendar td").filter(function () {
// 					var date = jQuery(this).text();
// 					return /\d/.test(date);
// 				}).find('a', 'span').html(function (i, html) {
// 					var day = jQuery(this).data('date');
// 					var month = jQuery(this).parent().data('month') + 1;
// 					var year = jQuery(this).parent().data('year');
// 					var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);
// 					var price = jsondata.default_price;
// 					var week_date = new Date(date);

// 					if (jQuery.inArray(date, unavailable_days_array) !== -1 || jQuery.inArray(String(week_date.getDay()), weekdays_array) !== -1) {
// 						jQuery(this).addClass('not_available_for_booking');
// 						jQuery(this).addClass('brightValue');
// 						jQuery(this).attr('data-custom', '-');
// 					} else {
// 						jQuery(this).addClass('available_for_booking');
// 					}

// 					if (!jQuery(this).hasClass('not_available_for_booking')) {
// 						if (jQuery.inArray(date, price_date_array) !== -1) {
// 							var price = price_array[jQuery.inArray(date, price_date_array)];

// 							if (parseFloat(price) > parseFloat(default_price)) {
// 								jQuery(this).addClass('highValue');
// 							} else if (parseFloat(price) < parseFloat(default_price)) {
// 								jQuery(this).addClass('lowValue');
// 							}

// 							price_text = currency_position == 'before' ? currency_symbol + Math.round(parseFloat(price)) : Math.round(parseFloat(price)) + currency_symbol;
// 							jQuery(this).attr('data-custom', price == '' ? '-' : price_text);
// 						} else if (jQuery.inArray(date, module_date_array) !== -1) {
// 							jQuery(this).attr('data-custom', '#emod');
// 							jQuery(this).addClass('bluetValue');
// 						} else {
// 							jQuery(this).addClass('brightValue');
// 							price_text = currency_position == 'before' ? currency_symbol + Math.round(parseFloat(default_price)) : Math.round(parseFloat(default_price)) + currency_symbol;
// 							jQuery(this).attr('data-custom', default_price == '' ? '-' : price_text);
// 						}
// 					} else {
// 						var today = new Date();
// 						today = today.getFullYear() + '-' + padWithZeros(today.getMonth() + 1) + '-' + padWithZeros(today.getDate());

// 						if ($container == 'service_calendar_details' && jQuery(this).parent().hasClass('custom-highlight')) {
// 							const $btn = jQuery($this).find('.booknowbtn');
// 							const $link = $btn.children('a');
// 							const disabledColor = '#d3d3d3';

// 							$btn.removeClass('textblue bordercolor');
// 							$link.removeClass('get_slot_details');
// 							$btn.addClass('readonly_div');
// 							$link.addClass('inactiveLink');
// 							$btn.attr('style', `background-color: ${disabledColor} !important;`);
// 							$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(bm_error_object.service_unavailable);
// 						}
// 					}
// 				});
// 			});
// 			jQuery('.loader_modal').hide();
// 		} else {
// 			jQuery('.loader_modal').hide();
// 			if ($container == 'service_calendar_details') {
// 				const $btn = jQuery($this).find('.booknowbtn');
// 				const $link = $btn.children('a');
// 				const disabledColor = '#d3d3d3';

// 				$btn.removeClass('textblue bordercolor');
// 				$link.removeClass('get_slot_details');
// 				$btn.addClass('readonly_div');
// 				$link.addClass('inactiveLink');
// 				$btn.attr('style', `background-color: ${disabledColor} !important;`);
// 				$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(bm_error_object.server_error);
// 			} else {
// 				jQuery('#slot_modal').find('.front_calendar_errortext').html(bm_error_object.server_error);
// 				jQuery('#slot_modal').find('.calendar_errortext').show();
// 			}
// 		}
// 	});
// }



// Variable Service Price Ajax on service calendar Load
function bm_get_service_price($this = '', $container, service_id = 0) {
	jQuery('#' + $container).find('.front_calendar_errortext').html('');
	var currency_symbol = bm_normal_object.currency_symbol;
	var currency_position = bm_normal_object.currency_position;

	if (!service_id) {
		service_id = strict_decode(jQuery('#current_service_id').val());
	}

	var data = {
		'action': 'bm_get_frontend_service_prices',
		'id': service_id,
		'nonce': bm_ajax_object.nonce
	};

	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;

		if (status == true) {
			var default_price = jsondata.default_price ?? '';
			var variable_price_obj = jsondata.variable_price.price || '';
			var variable_price_date_obj = jsondata.variable_price.date || '';
			var variable_module_obj = jsondata.variable_module.module || '';
			var variable_module_date_obj = jsondata.variable_module.date || '';

			var unavailability = jsondata.unavailability || '';
			var gbl_unavailability = jsondata.gbl_unavlabilty || '';
			var unavailable_days_array = [];
			var weekdays_array = [];

			if (unavailability && typeof unavailability === 'object') {
				if (unavailability.weekdays && unavailability.weekdays !== '') {
					weekdays_array = Object.values(unavailability.weekdays).map(String);
				}
			}

			if (
				gbl_unavailability &&
				typeof gbl_unavailability === 'object' &&
				gbl_unavailability.dates &&
				Object.keys(gbl_unavailability.dates).length > 0
			) {
				unavailable_days_array = Object.values(gbl_unavailability.dates);
			} else if (unavailability && typeof unavailability === 'object') {
				if (unavailability.dates && unavailability.dates !== '') {
					unavailable_days_array = Object.values(unavailability.dates);
				}
			}

			var price_array = [];
			var price_date_array = [];
			var module_date_array = [];

			if (variable_price_obj && variable_price_date_obj) {
				price_array = Object.values(variable_price_obj);
				price_date_array = Object.values(variable_price_date_obj);
			}

			if (variable_module_obj && variable_module_date_obj) {
				module_date_array = Object.values(variable_module_date_obj);
			}

			var calendar = $container == 'calendar_and_slot_details'
				? 'service_by_category_calendar'
				: 'service-by-id-calendar';

			setTimeout(function () {
				var calendarElement = $this
					? jQuery($this).find('.' + calendar)
					: jQuery('.' + calendar);

				calendarElement.datepicker().find(".ui-datepicker-calendar td").filter(function () {
					var date = jQuery(this).text();
					return /\d/.test(date);
				}).find('a, span').html(function (i, html) {
					var day = jQuery(this).data('date');
					var month = jQuery(this).parent().data('month') + 1;
					var year = jQuery(this).parent().data('year');
					var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);
					var week_date = new Date(date);
					var isUnavailable = false;

					for (var r = 0; r < unavailable_days_array.length; r++) {
						var rawRange = unavailable_days_array[r];
						if (!rawRange) continue;

						var rangeStr = String(rawRange).trim();
						if (!rangeStr.length) continue;

						if (rangeStr.indexOf('to') !== -1) {
							var parts = rangeStr.split('to');
							var start = parts[0].trim();
							var end = parts[1].trim();
							if (start && end && date >= start && date <= end) {
								isUnavailable = true;
								break;
							}
						} else if (date === rangeStr) {
							isUnavailable = true;
							break;
						}
					}

					if (!isUnavailable && weekdays_array.length > 0) {
						var weekdayStr = String(week_date.getDay());
						if (weekdays_array.indexOf(weekdayStr) !== -1) {
							isUnavailable = true;
						}
					}

					if (isUnavailable) {
						jQuery(this).addClass('not_available_for_booking brightValue');
						jQuery(this).attr('data-custom', '-');
					} else {
						jQuery(this).addClass('available_for_booking');

						if (jQuery.inArray(date, price_date_array) !== -1) {
							var price = price_array[jQuery.inArray(date, price_date_array)];
							if (parseFloat(price) > parseFloat(default_price)) {
								jQuery(this).addClass('highValue');
							} else if (parseFloat(price) < parseFloat(default_price)) {
								jQuery(this).addClass('lowValue');
							}
							var price_text = currency_position == 'before'
								? currency_symbol + Math.round(parseFloat(price))
								: Math.round(parseFloat(price)) + currency_symbol;
							jQuery(this).attr('data-custom', price === '' ? '-' : price_text);
						} else if (jQuery.inArray(date, module_date_array) !== -1) {
							jQuery(this).attr('data-custom', '#emod');
							jQuery(this).addClass('bluetValue');
						} else {
							jQuery(this).addClass('brightValue');
							var price_text = currency_position == 'before'
								? currency_symbol + Math.round(parseFloat(default_price))
								: Math.round(parseFloat(default_price)) + currency_symbol;
							jQuery(this).attr('data-custom', default_price === '' ? '-' : price_text);
						}
					}

					if (isUnavailable && $container == 'service_calendar_details' && jQuery(this).parent().hasClass('custom-highlight')) {
						const $btn = jQuery($this).find('.booknowbtn');
						const $link = $btn.children('a');
						const disabledColor = '#d3d3d3';

						$btn.removeClass('textblue bordercolor');
						$link.removeClass('get_slot_details');
						$btn.addClass('readonly_div');
						$link.addClass('inactiveLink');
						$btn.attr('style', `background-color: ${disabledColor} !important;`);
						$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(bm_error_object.service_unavailable);
					}
				});
			});
			jQuery('.loader_modal').hide();
		} else {
			jQuery('.loader_modal').hide();
			if ($container == 'service_calendar_details') {
				const $btn = jQuery($this).find('.booknowbtn');
				const $link = $btn.children('a');
				const disabledColor = '#d3d3d3';

				$btn.removeClass('textblue bordercolor');
				$link.removeClass('get_slot_details');
				$btn.addClass('readonly_div');
				$link.addClass('inactiveLink');
				$btn.attr('style', `background-color: ${disabledColor} !important;`);
				$btn.parent('.productbottombar').find('.calendar_shortcode_error_message').html(bm_error_object.server_error);
			} else {
				jQuery('#slot_modal').find('.front_calendar_errortext').html(bm_error_object.server_error);
				jQuery('#slot_modal').find('.calendar_errortext').show();
			}
		}
	});
}



// Search services by name
jQuery(document).on('keyup', '#search_service_by_name', function (e) {
	e.preventDefault();
	var $this = jQuery(this);
	var element = jQuery($this).parents('.service-by-catrgory');
	jQuery(element).find('.service_by_category_gridview').html('');
	jQuery(element).find('.service_by_category_gridview').html('<div class="loader_modal" style="top: initial;"></div>');
	jQuery(element).find('.service_by_category_gridview').find('.loader_modal').show();

	var categories = jQuery($this).parent('.inputgroup').find('#service_categories').val();
	var post = {
		'search_string': jQuery.trim($this.val()),
		'categories': categories,
	}

	var data = { 'action': 'bm_fetch_services_by_name', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		jQuery(element).find('.service_by_category_gridview').html('');

		if (jsondata != null && jsondata != "") {
			if (!jQuery(jsondata).hasClass('svc_by_cat_search')) {
				var sliders = document.querySelectorAll('.slick-initialized');

				sliders.forEach(item => {
					jQuery(item).slick('unslick');
				})
			}

			jQuery(element).find('.service_by_category_gridview').html(jsondata);
			activateSlick();
		} else {
			jQuery(element).find('.service_by_category_gridview').html(bm_error_object.server_error);
		}
	});
});



// Show/Hide Section
function bm_open_close_tab(a) {
	if (jQuery('#' + a).is(':visible')) {
		jQuery('#' + a).hide();
	} else {
		jQuery('#' + a).show();

		if (a == 'shipping_fields') {
			const shipping_state = jQuery.trim(jQuery('select[id="shipping_state"]').val());

			if (!shipping_state) {
				const country_code = jQuery.trim(jQuery('select[id="shipping_country"]').val());

				if (country_code) {
					bm_get_state_of_country(country_code, jQuery('select[id="shipping_state"]'));
				}
			}

			jQuery([document.documentElement, document.body]).animate({
				scrollTop: jQuery("#shipping_fields").offset().top
			}, 2000);
		}
	}
}



// Show/Hide Section
function bm_slide_up_down(a) {
	if (jQuery('#' + a).is(':visible')) {
		jQuery('#' + a).slideUp("slow");
	} else {
		jQuery('#' + a).slideDown("slow");

		if (a == 'gift_fields') {
			const recipient_state = jQuery.trim(jQuery('select[id="recipient_state"]').val());

			if (!recipient_state) {
				const country_code = jQuery.trim(jQuery('select[id="recipient_country"]').val());

				if (country_code) {
					bm_get_state_of_country(country_code, jQuery('select[id="recipient_state"]'));
				}
			}
		}
	}
}



// Add discount in checkout form
jQuery(document).on('click', '#check_checkout_discount', function (e) {
	e.preventDefault();
	jQuery(document).find('span.age_errortext').html('');
	var b = 0;
	var formData = {};
	var ageFromData = {};
	var ageToData = {};
	var ageTotalData = {};

	jQuery('.checkout_age_range_fields :input').map(function () {
		var type = jQuery(this).prop('type');
		var value = jQuery(this).val();
		var index = jQuery(this).attr('id').split('_')[3];
		jQuery(this).parent().find('div.required_errortext').remove();

		if (type !== 'button' && type !== 'submit' && type !== 'reset' && type !== 'search') {
			if (value == '') {
				jQuery(document).find('span.age_errortext').html(bm_error_object.fill_up_age_fields);
				b++;
			} else if ((value < 0) || (value % 1 != 0)) {
				jQuery(document).find('span.age_errortext').html(bm_error_object.invalid_total);
				b++;
			} else {
				if (jQuery(this).attr("id").match("age_group_from_")) {
					ageFromData[index] = jQuery(this).val();
				} else if (jQuery(this).attr("id").match("age_group_to_")) {
					ageToData[index] = jQuery(this).val();
				} else if (jQuery(this).attr("id").match("age_group_total_")) {
					ageTotalData[index] = jQuery(this).val();
				}
			}
		}
	});

	if (b == 0) {
		jQuery('.loader_modal').show();
		formData['from_data'] = ageFromData;
		formData['to_data'] = ageToData;
		formData['total_data'] = ageTotalData;
		formData['booking_key'] = getUrlParameter('flexi_booking');

		var data = { 'action': 'bm_check_discount', 'post': formData, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			jQuery('.loader_modal').hide();
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			var data = jsondata.data ? jsondata.data : '';
			var negative_discount = jsondata.negative_discount ? jsondata.negative_discount : 0;

			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;
			if (status == 'success' && typeof (data.subtotal) != "undefined" && typeof (data.discount) != "undefined" && typeof (data.total) != "undefined") {
				if (data.discount <= 0) {
					jQuery('.discount_li').addClass('hidden')
				} else {
					jQuery('.discount_li').removeClass('hidden')
				}

				jQuery(document).find('span#checkout_subtotal').html('');
				jQuery(document).find('span#checkout_discount').html('');
				jQuery(document).find('span#checkout_total').html('');
				var subtotal_text = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(data.subtotal).toFixed(2)) : changePriceFormat(parseFloat(data.subtotal).toFixed(2)) + currency_symbol;
				var discount_text = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(data.discount).toFixed(2)) : changePriceFormat(parseFloat(data.discount).toFixed(2)) + currency_symbol;
				var total_text = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(data.total).toFixed(2)) : changePriceFormat(parseFloat(data.total).toFixed(2)) + currency_symbol;
				negative_discount == 1 ? jQuery(document).find('span#checkout_discount').removeClass('positive_discount').addClass('negative_discount') : jQuery(document).find('span#checkout_discount').removeClass('negative_discount').addClass('positive_discount');

				jQuery(document).find('span#checkout_subtotal').html(subtotal_text);
				jQuery(document).find('span#checkout_discount').html(discount_text);
				jQuery(document).find('span#checkout_total').html(total_text);

				if (data.total == 0) {
					jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "free_booking_no_payment");
					jQuery(document).find('div#free_booking_no_payment').html(bm_normal_object.free_book);
				} else {
					jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "go_to_payment_page");
					jQuery(document).find('div#go_to_payment_page').html(bm_normal_object.pay + total_text);
				}

				jQuery([document.documentElement, document.body]).animate({
					scrollTop: jQuery(".order_price_heading").offset().top
				}, 2000);
			} else if (status == 'excess') {
				jQuery(document).find('span.age_errortext').html(bm_error_object.excess_order_total);
			} else if (status == 'negative') {
				jQuery(document).find('span.age_errortext').html(bm_error_object.discount_not_applicable);
			} else {
				jQuery(document).find('span.age_errortext').html(bm_error_object.server_error);
			}
		});
	} else {
		return false;
	}
});



// Reset discount in checkout form
jQuery(document).on('click', '#reset_checkout_discount', function (e) {
	e.preventDefault();
	jQuery('.loader_modal').show();
	jQuery(document).find('span.age_errortext').html('');

	var data = { 'action': 'bm_reset_discount', 'booking_key': getUrlParameter('flexi_booking'), 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		var data = jsondata.data ? jsondata.data : '';
		var currency_symbol = bm_normal_object.currency_symbol;
		var currency_position = bm_normal_object.currency_position;
		if (status == 'success' && typeof (data.subtotal) != "undefined" && typeof (data.discount) != "undefined" && typeof (data.total) != "undefined") {
			if (data.discount <= 0) {
				jQuery('.discount_li').addClass('hidden');
			} else {
				jQuery('.discount_li').removeClass('hidden');
			}

			jQuery(document).find('span#checkout_subtotal').html('');
			jQuery(document).find('span#checkout_discount').html('');
			jQuery(document).find('span#checkout_total').html('');

			jQuery(document).find('input#age_group_total_0').val(0);
			jQuery(document).find('input#age_group_total_1').val(0);
			jQuery(document).find('input#age_group_total_2').val(0);
			jQuery(document).find('input#age_group_total_3').val(0);

			var subtotal_text = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(data.subtotal).toFixed(2)) : changePriceFormat(parseFloat(data.subtotal).toFixed(2)) + currency_symbol;
			var discount_text = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(data.discount).toFixed(2)) : changePriceFormat(parseFloat(data.discount).toFixed(2)) + currency_symbol;
			var total_text = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(data.total).toFixed(2)) : changePriceFormat(parseFloat(data.total).toFixed(2)) + currency_symbol;
			jQuery(document).find('span#checkout_discount').removeClass('negative_discount').addClass('positive_discount');

			jQuery(document).find('span#checkout_subtotal').html(subtotal_text);
			jQuery(document).find('span#checkout_discount').html(discount_text);
			jQuery(document).find('span#checkout_total').html(total_text);

			if (data.total == 0) {
				jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "free_booking_no_payment");
				jQuery(document).find('div#free_booking_no_payment').html(bm_normal_object.free_book);
			} else {
				jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "go_to_payment_page");
				jQuery(document).find('div#go_to_payment_page').html(bm_normal_object.pay + total_text);
			}

			jQuery([document.documentElement, document.body]).animate({
				scrollTop: jQuery(".order_price_heading").offset().top
			}, 2000);

		} else {
			jQuery(document).find('span.age_errortext').html(bm_error_object.server_error);
		}
	});
});



// Change price format
function changePriceFormat(price) {
	price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0.00;
	var formatLocale = bm_normal_object.price_format ? bm_normal_object.price_format : 'it-IT';
	formatLocale = formatLocale.replace('_', '-');
	var currency = bm_normal_object.currency_type ? bm_normal_object.currency_type : 'EUR';

	const formattedPrice = new Intl.NumberFormat(formatLocale, {
		// style: 'currency',
		// currency: currency,
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(price);

	return formattedPrice;
}



// Gallery previous slide
jQuery(document).on('click', '.gallery_prev', function (e) {
	e.preventDefault();
	galleryPlusSlides(-1);
});



// Gallery next slide
jQuery(document).on('click', '.gallery_next', function (e) {
	e.preventDefault();
	galleryPlusSlides(1);
});



// Gallery sliders
function galleryPlusSlides(n) {
	showGallerySlides(slideIndex += n);
}



// Gallery current slides
function galleryCurrentSlide(n) {
	showGallerySlides(slideIndex = n);
}



// Show Gallery sliders
function showGallerySlides(n) {
	var i;
	var slides = document.getElementsByClassName("gallery_slides");
	var dots = document.getElementsByClassName("gallery_single_image");
	// var captionText = document.getElementById("caption");
	if (n > slides.length) { slideIndex = 1 }
	if (n < 1) { slideIndex = slides.length }
	for (i = 0; i < slides.length; i++) {
		slides[i].style.display = "none";
	}
	for (i = 0; i < dots.length; i++) {
		dots[i].className = dots[i].className.replace(" gallery_active", "");
	}
	slides[slideIndex - 1].style.display = "block";
	dots[slideIndex - 1].className += " gallery_active";
	// captionText.innerHTML = dots[slideIndex - 1].alt;
}



// Initialize multiselect
jQuery(document).ready(function ($) {
	intitializeMultiselect('search_by_category');
	intitializeMultiselect('search_by_service');
});



// Multiselect
function intitializeMultiselect(a) {
	jQuery('#' + a).multiselect('reload');
	jQuery('#' + a).multiselect({
		columns: 1,
		texts: {
			placeholder: a == 'search_by_service' || a == 'search_fullcalendar_by_service' || a == 'search_timeslot_fullcalendar_by_service' ? bm_normal_object.filter_service : bm_normal_object.filter_category,
			search: bm_normal_object.search_here,
			selectAll: bm_normal_object.select_all
		},
		search: true,
		selectAll: true,
		onOptionClick: function (element, option) {
			var maxSelect = 1000;

			// too many selected, deselect this option
			if (jQuery(element).val().length > maxSelect) {
				if (jQuery(option).is(':checked')) {
					var thisVals = jQuery(element).val();

					thisVals.splice(
						thisVals.indexOf(jQuery(option).val()), 1
					);

					jQuery(element).val(thisVals);

					jQuery(option).prop('checked', false).closest('li')
						.toggleClass('selected');
				}
			}
		}
	});
}




// Swicth flexibooking language
function change_flexi_language($this) {
	var lang_code = jQuery($this).val();
	sessionStorage.setItem("flexi_current_lang", lang_code);

	var post = {
		'flexi_lang_code': lang_code,
	}

	var data = { 'action': 'bm_flexi_set_frontend_lang', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';

		if (status == true) {
			location.reload();
		} else {
			// Display an error message
			return false;
		}
	});
}



// Fill woocommerce states wrt country
jQuery(document).ready(function ($) {
	$('.woocommerce_gift_fields #is_gift').on('click', function () {
		bm_slide_up_down('gift_fields');
	});

	$('.woocommerce_gift_fields #gift_details\\[country\\]').change(function () {
		var country = $(this).val();
		var stateField = $('.woocommerce_gift_fields #gift_details\\[state\\]');
		var post = {
			country,
		}

		var data = { 'action': 'fetch_woocommerce_states', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			stateField.empty();
			if (response.success && response.data) {
				$.each(response.data, function (stateCode, stateName) {
					stateField.append(new Option(stateName, stateCode));
				});
			} else {
				stateField.append(new Option(bm_normal_object.no_states_available, ''));
			}
		});
	});

	$('.woocommerce_gift_fields #gift_details\\[first_name\\]').closest('.form-row').addClass('form-row-first');
	$('.woocommerce_gift_fields #gift_details\\[last_name\\]').closest('.form-row').addClass('form-row-last');
	$('.woocommerce_gift_fields #gift_details\\[email\\]').closest('.form-row').addClass('form-row-first');
	$('.woocommerce_gift_fields #gift_details\\[contact\\]').closest('.form-row').addClass('form-row-last');
	$('.woocommerce_gift_fields #gift_details\\[address\\]').closest('.form-row').addClass('form-row-wide');
	$('.woocommerce_gift_fields #gift_details\\[city\\]').closest('.form-row').addClass('form-row-first');
	$('.woocommerce_gift_fields #gift_details\\[state\\]').closest('.form-row').addClass('form-row-last');
	$('.woocommerce_gift_fields #gift_details\\[postcode\\]').closest('.form-row').addClass('form-row-first');
	$('.woocommerce_gift_fields #gift_details\\[country\\]').closest('.form-row').addClass('form-row-last');
});



// Fill checkout page states wrt country
jQuery(document).ready(async function ($) {
	const country_code = $.trim($('select[name="billing_country"]').val());

	if (country_code) {
		await bm_get_state_of_country(country_code, $('select[name="billing_state"]'));
	}

	$('select[name="billing_country"], select[id="shipping_country"], select[id="recipient_country"]').on('change', async function () {
		const country = $(this).val();
		let stateField;

		$('.loader_modal').show();

		if ($(this).attr('name') === 'billing_country') {
			stateField = $('select[name="billing_state"]');
		} else if ($(this).attr('id') === 'shipping_country') {
			stateField = $('select[id="shipping_state"]');
		} else if ($(this).attr('id') === 'recipient_country') {
			stateField = $('select[id="recipient_state"]');
		}

		if (stateField) {
			await bm_get_state_of_country(country, stateField);
		}
	});
});



// Get states wrt country
async function bm_get_state_of_country(country, stateField) {
	if (country) {
		const data = {
			action: 'get_states',
			country,
			nonce: bm_ajax_object.nonce
		};

		try {
			const response = await jQuery.post(bm_ajax_object.ajax_url, data);
			stateField.empty();
			jQuery('.no_states_message').remove();

			if (response.success && response.data && Object.keys(response.data).length > 0) {
				jQuery.each(response.data, function (stateCode, state) {
					stateField.append(new Option(state.name, state.name));
				});
			} else {
				stateField.after(jQuery('<div class="no_states_message">' + bm_normal_object.no_states_available + '</div>'));
			}
		} catch (error) {
			alert(bm_error_object.server_error);
		} finally {
			jQuery('.loader_modal').hide();
		}
	}
}

jQuery(document).ready(function ($) {
	let is_svc_calendar_shortcode = bm_normal_object.is_svc_calendar_shortcode;

	if (is_svc_calendar_shortcode == 1) {
		$('.service_calendar_details').each(function (id, item) {
			const serviceId = $(this).data('service-id');
			initiateServiceCalendar('service_calendar_details', serviceId, $(this));
		});
	}
});


jQuery(document).ready(function ($) {
	if (window.location.href.includes("flexibooking-checkout")) {
		let titleElement = document.querySelector(".entry-title");
		if (titleElement) {
			titleElement.style.display = "none";
		}
	}
});

// document.querySelector('.calendar-box .ui-datepicker .ui-datepicker-title').innerHTML = document.querySelector('.calendar-box .ui-datepicker-title').innerHTML.replace('年', '');


