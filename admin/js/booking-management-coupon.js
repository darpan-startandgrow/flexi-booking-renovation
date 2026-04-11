
jQuery(document).ready(function ($) {
	var current_screen = bm_normal_object.current_screen;
	if (current_screen == 'admin_page_bm_add_coupon') {

		if (getUrlParameter('id') == '') {
			bm_return_value_for_coupon_type(0);
			bm_return_value_for_coupon_type(1);
			bm_return_value_for_coupon_type(2);
			bm_return_value_for_coupon_type(3);

		} else if (getUrlParameter('id') != '') {
			if (!$('#is_exclude_service').is(':checked')) {
				bm_return_value_for_coupon_type(0);
			}
			else {
				intitializeMultiselect('excluded_services');
			}
			if (!$('#is_exclude_category').is(':checked')) {
				bm_return_value_for_coupon_type(1);
			} else {
				intitializeMultiselect('excluded_category');
			}
			if (!$('#is_email_exclude').is(':checked')) {
				bm_return_value_for_coupon_type(2);
			} else {
				intitializeMultiselect('excluded_emails');
			}
			if (!$('#is_service_included').is(':checked')) {
				bm_return_value_for_coupon_type(3);
			} else {
				intitializeMultiselect('included_services');
			}

		}
	}
});

//Return coupon type values
function bm_return_value_for_coupon_type(a) {
	var post = {
		'type': a,
	}
	var data = { 'action': 'bm_fetch_value_for_coupon_type', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);

		var status = jsondata.status ? jsondata.status : '';
		var value = jsondata.value ? jsondata.value : '';
		if (status == true) {
			if (a == 0) {
				jQuery('#excluded_services').html(value);
				intitializeMultiselect('excluded_services');
			}else if (a == 1) {
				jQuery('#excluded_category').html(value);
				intitializeMultiselect('excluded_category');
			}else if (a == 2) {
				jQuery('#excluded_emails').html(value);
				intitializeMultiselect('excluded_emails');
			}else if( a == 3 ){
				jQuery('#included_services').html(value);
				intitializeMultiselect('included_services');
			}

		} else {
			// alert(bm_error_object.event_type_value_error);
		}
	});
}

//adding state and country dropdown on coupon form
jQuery(document).ready(function ($) {

	var current_screen = bm_normal_object.current_screen;
	if (current_screen == 'admin_page_bm_add_coupon') {

		if (getUrlParameter('id') == '') {
			bm_return_value_for_country(0, '');
			countrySelect = document.getElementById('country_list_0');
			stateSelect = document.getElementById('state_list_0');
			updateStates(countrySelect, stateSelect, '');
		} else if (getUrlParameter('id') != '' && !$('#is_geographic_restrictions').is(':checked')) {
			bm_return_value_for_country(0, '');
			countrySelect = document.getElementById('country_list_0');
			stateSelect = document.getElementById('state_list_0');
			updateStates(countrySelect, stateSelect, '');
		} else if (getUrlParameter('id') != '' && $('#is_geographic_restrictions').is(':checked')) {
			var passedArray = JSON.parse(document.getElementById('list_selcted_country').value);
			var next = 0;
			passedArray.forEach(function (item) {

				var option_box = "<td id='restriction_field_" + next + "' class='restriction_field'>" +
					"<div id='restriction_condition_div' class='bminput bm_required'>" +

					"<button type='button' id='remove_restriction_[" + next + "]' class='bm_remove_restrictions' onclick='bm_remove_restriction_box(this)'><i class='fa fa-remove'></i></button>" +
					"<div style='width:40%; float:left; margin-right:15px;'>" +
					"<select name='geographic_restriction[" + next + "][country_coupon]' id='country_list_" + next + "' onchange='bm_fetch_country_value(this, )' class='regular-text emailselect' style='width:40%;max-width:100% !important'>" +
					"</select></div>&nbsp;" +
					'<div style="width:40%; float:left;">' +
					"<select name='geographic_restriction[" + next + "][state_coupon][]' id='state_list_" + next + "' class='notification-multiselect' multiple='multiple'></select></div>" +
					"<div class='errortext'></div></div></td>";
				jQuery('#geographic_content_main td.restriction_field:last').after(option_box);
				bm_return_value_for_country(next, item.country_coupon);
				countyElement = document.getElementById('country_list_' + next);
				bm_fetch_country_value(countyElement, item.state_coupon);
				jQuery('#geographic_content_main td.restriction_field:last select').focus();
				next++;
			});
		}
	}

	jQuery('#coupon_creation_form').on('submit', function (e) {
		let isValid = true;

		jQuery('input[id^="unavailable_slot_start_"]').each(function () {
			const parentSpan = jQuery(this).closest('span.time_input_span');
			const fromTime = jQuery(this).val();
			const toTime = parentSpan.find('input[id^="unavailable_slot_end_"]').val();

			if (fromTime && !toTime) {
				alert(bm_error_object.coupon_from_slot_value_error);
				isValid = false;
				return false;
			}
		});

		if (!isValid) {
			e.preventDefault();
		}

		let isValid2 = true;

		jQuery('input[id^="unavailable_slot_start_"]').each(function () {
			const parentSpan = jQuery(this).closest('span.time_input_span');
			const fromTime = jQuery(this).val();
			const toTime = parentSpan.find('input[id^="unavailable_slot_end_"]').val();
			const errorSpan = parentSpan.find('span[id^="unavailable_slot_error_"]');

			if (fromTime && !toTime) {
				errorSpan.text(bm_error_object.coupon_from_slot_value_error);
				alert(bm_error_object.coupon_from_slot_value_error);
				isValid2 = false;
				return false;
			}
			if (!fromTime && toTime) {
				errorSpan.text(bm_error_object.coupon_to_slot_value_error);
				alert(bm_error_object.coupon_to_slot_value_error);
				isValid2 = false;
				return false;
			}
		});

		if (!isValid2) {
			e.preventDefault();
		}
	});
});

//form validation for coupon module
function add_coupon_form_validation() {
	jQuery('.errortext').html('');
	jQuery('.errortext').hide();
	var b = 0;
	try {
		jQuery('td.bm_required').each(
			function (index, element) {
				var type = jQuery(this).children().prop('type');
				var value = (type == 'select-one' || type == 'select-multiple') ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());
				if (value == "") {
					jQuery(this).children('.errortext').html(bm_error_object.required_field);
					jQuery(this).children('.errortext').show();
					b++;
				}
			}
		);

		val = document.getElementById("discount_amount").value;
		var regex = /^[1-9]\d*(\.\d+)?$/;
		if (!val.match(regex)) {
			alert(bm_error_object.numeric_field);
			jQuery("#dis_validate_amount").html(bm_error_object.numeric_field);
			jQuery("#dis_validate_amount").show();
			b++;
		}

		value = document.getElementById("coupon_code").value;
		if (value.trim().length < 4) {
			alert(bm_error_object.code_error);
			jQuery("#error_message").html(bm_error_object.code_error);
			jQuery("#error_message").show();
			b++;
		}
		if (b === 0) {
			return true;
		} else {
			return false;
		}
	} catch (err) {
		showMessage(bm_error_object.server_error, 'error');
		return false;
	}
}

// return the country values for dropdown 
function bm_return_value_for_country(x, selectedCountry) {
	const country_list = country_and_states.country;
	const selectElement = document.getElementById("country_list_" + x);
	const usedcountry = document.getElementById("list_selcted_country");
	const usedcountryVal = [];
	if (usedcountry !== null) {
		var passedArray = JSON.parse(usedcountry.value);
		var next = 0;
		if (Array.isArray(passedArray) && passedArray.length > 0) {
			passedArray.forEach(function (item) {
				usedcountryVal.push(item.country_coupon);
			});
		}
	}
	if (x >= 0) {
		for (i = 0; i <= x; i++) {
			const selectedElement = document.getElementById("country_list_" + i);
			if( selectedElement ){
				const selectElementLatest = selectedElement.value;
				usedcountryVal.push(selectElementLatest);
			}
			
		}
	}
	selectElement.innerHTML = '<option value="">Select a Country</option>';
	Object.keys(country_list).forEach(key => {

		const option = document.createElement("option");
		option.value = key;
		option.textContent = country_list[key];
		option.setAttribute("data-name", country_list[key]);
		if (key === selectedCountry) {
			option.selected = true;
		}
		if (Array.isArray(usedcountryVal) && usedcountryVal.length > 0 && usedcountryVal.includes(key) && key !== selectedCountry) {
		} else {
			selectElement.appendChild(option);
		}
	});
}

// fetch country value
function bm_fetch_country_value(countrySelect, selectedArray) {

	var id = jQuery(countrySelect).attr('id');
	var x = jQuery('#' + id).val();
	var y = Number(id.split("_")[2]);
	stateSelect = document.getElementById('state_list_' + y);
	stateSelect.innerHTML = "";

	updateStates(countrySelect, stateSelect, selectedArray);
}

//Add button for restriction list 
function addRestrictionList() {
	var last = jQuery('#geographic_content_main td.restriction_field:last').attr("id");
	var next = Number(last.split("_")[2]) + 1;

	var option_box = "<td id='restriction_field_" + next + "' class='restriction_field'>" +
		"<div id='restriction_condition_div' class='bminput bm_required' style='width:100%;margin-top:10px;'>" +

		
		'<div style="width:47%; float:left; margin-right:6px;">' +
		"<select name='geographic_restriction[" + next + "][country_coupon]' id='country_list_" + next + "' onchange='bm_fetch_country_value(this, )' class='regular-text emailselect' style='width:100%;max-width:100% !important'>" +
		"</select></div>&nbsp;" +
		'<div style="width:47%; float:left;">' +
		"<select name='geographic_restriction[" + next + "][state_coupon][]' id='state_list_" + next + "' class='notification-multiselect' multiple='multiple' style='width:100%'></select></div>" +
		"<div style='width:4%;float:left;'><button type='button' id='remove_restriction_[" + next + "]' class='bm_remove_restrictions' onclick='bm_remove_restriction_box(this)'><i class='fa fa-remove'></i></button></div>" +
		"<div class='errortext'></div></div></td>";
		 
	jQuery('#geographic_content_main td.restriction_field:last').after(option_box);
	bm_return_value_for_country(next, '');
	jQuery('#geographic_content_main td.restriction_field:last select').focus();
}

// Remove country box in coupon notification page
function bm_remove_restriction_box(a) {
	var total = jQuery('#geographic_content_main td.restriction_field').length;
	if (total == 1) {
		alert(bm_normal_object.at_least_one_condition);
	} else if (confirm(bm_normal_object.sure_remove_restriction)) {
		jQuery(a).parents('td.restriction_field').remove();
	}
}

// Function to update the countries dropdown
function updateCountry(selectElement) {
	selectElement.innerHTML = '<option value="">Select a Country</option>';
	const country_list = country_and_states.country;
	Object.keys(country_list).forEach(key => {
		const option = document.createElement("option");
		option.value = key;
		option.textContent = country_list[key];
		option.setAttribute("data-name", country_list[key]);
		selectElement.appendChild(option);
	});
}

// Function to update the states dropdown based on the selected country
function updateStates(countrySelect, stateSelect, selected_states) {

	const selectedCountry = countrySelect.value;
	const state_list = country_and_states.states;

	if (state_list[selectedCountry]) {
		state_list[selectedCountry].forEach(state => {
			const option = document.createElement("option");
			option.value = state.name;
			option.textContent = state.name;
			if (Array.isArray(selected_states) && selected_states.length > 0 && selected_states.includes(state.name)) {
				option.selected = true;
			}
			stateSelect.appendChild(option);
		});
		stateSelect.style.width = '300px';
		intitializeMultiselect(stateSelect.id);
	}
}


// Validate dynamic time slots
jQuery(document).on('change', 'input[type="time"]', function () {
	const parentSpan = jQuery(this).closest('span.time_input_span');

	const fromTime = parentSpan.find('input[id^="unavailable_slot_start_"]').val();
	const toTime = parentSpan.find('input[id^="unavailable_slot_end_"]').val();

	if (fromTime && toTime) {
		if (fromTime >= toTime) {
			alert(bm_error_object.coupon_time_less_error);
			parentSpan.find('input[type="time"]').val('');
		}
	}

});

//slot unavailaibility check
jQuery(document).on('change', 'input[id^="unavailable_slot_start_"]', function () {
	const parentSpan = jQuery(this).closest('span.time_input_span');
	const fromTime = parentSpan.find('input[id^="unavailable_slot_start_"]').val();
	const toInput = parentSpan.find('input[id^="unavailable_slot_end_"]');

	if (fromTime) {
		toInput.attr('required', true);
		toInput.addClass('required');
	} else {
		toInput.attr('required', false);
		toInput.removeClass('required');
	}
});

//slot unavailaibility check
jQuery(document).on('change', 'input[id^="unavailable_slot_start_"]', function () {
	const parentSpan = jQuery(this).closest('span.time_input_span');
	const fromTime = parentSpan.find('input[id^="unavailable_slot_start_"]').val();
	const toInput = parentSpan.find('input[id^="unavailable_slot_end_"]');
	const errorSpan = parentSpan.find('span[id^="unavailable_slot_error_"]');

	if (fromTime) {
		if (!toInput.val()) {
			errorSpan.text(bm_error_object.coupon_to_slot_value_error);
			errorSpan.show();
		} else {
			errorSpan.text('');
			errorSpan.hide();
		}
		toInput.attr('required', true);
	} else {
		errorSpan.text('');
		errorSpan.hide();
		toInput.attr('required', false);
	}
});

// Event listener for "To" time
jQuery(document).on('change', 'input[id^="unavailable_slot_end_"]', function () {
	const parentSpan = jQuery(this).closest('span.time_input_span');
	const fromTime = parentSpan.find('input[id^="unavailable_slot_start_"]').val();
	const toTime = jQuery(this).val();
	const errorSpan = parentSpan.find('span[id^="unavailable_slot_error_"]');

	if (fromTime && toTime) {
		errorSpan.text('');
		errorSpan.hide();
	}
	if (!fromTime && toTime) {
		errorSpan.text(bm_error_object.coupon_from_slot_value_error);
		errorSpan.show();
	}
});

// Event listener for "To" time
jQuery(document).on('change', 'input[id^=""]', function () {
	const parentSpan = jQuery(this).closest('span.time_input_span');
	const fromTime = parentSpan.find('input[id^="unavailable_slot_start_"]').val();
	const toTime = jQuery(this).val();
	const errorSpan = parentSpan.find('span[id^="unavailable_slot_error_"]');

	if (fromTime && toTime) {
		errorSpan.text('');
		errorSpan.hide();
	}
	if (!fromTime && toTime) {
		errorSpan.text(bm_error_object.coupon_from_slot_value_error);
		errorSpan.show();
	}
});


//coupon image check
jQuery(document).ready(function ($) {
	var custom_uploader;
	$('.cpn-image').click(function (e) {
		e.preventDefault();
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: bm_normal_object.choose_image,
			button: {
				text: bm_normal_object.choose_image
			},
			library: {
				type: 'image'
			},
			multiple: false
		});

		custom_uploader.on('select', function () {
			attachment = custom_uploader.state().get('selection').first().toJSON();
			var file_size = parseInt(attachment.filesizeInBytes);
			var file_width = parseInt(attachment.sizes.full.width);
			var file_height = parseInt(attachment.sizes.full.height);

			var min_file_size = parseInt(bm_normal_object.image_min_size);
			var max_file_size = parseInt(bm_normal_object.image_max_size);
			var minimum_width = parseInt(bm_normal_object.image_min_width);
			var maximum_width = parseInt(bm_normal_object.image_max_width);
			var minimum_height = parseInt(bm_normal_object.image_min_height);
			var maximum_height = parseInt(bm_normal_object.image_max_height);
			if (attachment['type'] == 'image') {
				if (min_file_size != 0 && file_size < min_file_size) {
					alert(bm_error_object.file_size_less_message + min_file_size + ' bytes');
				} else if (max_file_size != 0 && file_size > max_file_size) {
					alert(bm_error_object.file_size_more_message + max_file_size + ' bytes');
				} else if (minimum_width != 0 && file_width < minimum_width) {
					alert(bm_error_object.file_width_less_message + minimum_width + ' pixels');
				} else if (maximum_width != 0 && file_width > maximum_width) {
					alert(bm_error_object.file_width_more_message + maximum_width + ' pixels');
				} else if (minimum_height != 0 && file_height < minimum_height) {
					alert(bm_error_object.file_width_less_message + minimum_height + ' pixels');
				} else if (maximum_height != 0 && file_height > maximum_height) {
					alert(bm_error_object.file_width_more_message + maximum_height + ' pixels');
				} else {
					$('#cpn_image_id').val(attachment.id);
					$('#cpn_image_preview').attr('src', attachment.url);
					$('.cpn_image_container').show();
				}
			} else {
				alert(bm_error_object.file_type_not_supported);
			}

		});
		custom_uploader.open();
	});
});

// Coupon Image Remove
function cpn_remove_image() {
	jQuery('#cpn_image_id').val('');
	jQuery('#cpn_image_preview').attr('src', '');
	jQuery('.cpn_image_container').hide();
}

// Remove a process
jQuery(document).on('click', '#delcpn', function () {
	if (confirm(bm_normal_object.sure_remove_coupon)) {
		var id = jQuery(this).val();
		var data = { 'action': 'bm_remove_coupon', 'id': id, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				location.reload();
			}
		});
	}
});

// Add unavailable dates in coupon page
function bm_add_unavailable_date_cpn() {
	var total_elements = jQuery('td.date_option_field span.date_input_span').length;
	var date = new Date(jQuery.now());
	var crossSign = "✕";
	date = date.getFullYear() + "-" + padWithZeros((date.getMonth() + 1)) + "-" + padWithZeros(date.getDate());

	if (total_elements !== 0) {
		var id = jQuery('td.date_option_field span.date_input_span:last input').attr('id');
		var index = Number(id.split("_")[3]) + 1;
		var option_box = '<span class="date_input_span"><input type="date" id="cpn_unavailable_date_' + index + '" name="coupon_unavailability[dates][' + index + ']" min="' + date + '">';
		option_box += '<button type="button" id="svc_date_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_svc_unavailable_date(this)">' + crossSign + '</button></span>'
		jQuery('td.date_option_field span.date_input_span:last').after(option_box);
	} else {
		var option_box = '<span class="date_input_span"><input type="date" id="cpn_unavailable_date_1" name="coupon_unavailability[dates][1]" min="' + date + '">';
		option_box += '<button type="button" id="svc_date_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_svc_unavailable_date(this)">' + crossSign + '</button></span>'
		jQuery('td.date_option_field span.add_dates_button').before(option_box);
	}
}
// Add unavailable time in coupon page
function bm_add_unavailable_time_cpn() {
	var total_elements = jQuery('td.time_option_field span.time_input_span').length;
	var crossSign = "✕";
	if (total_elements !== 0) {
		var id = jQuery('td.time_option_field span.time_input_span:last input').attr('id');
		var index = Number(id.split("_")[3]) + 1;
		var option_box = '<div style="margin-top:8px; margin-bottom:8px;"><span class="time_input_span">From : <input type="time" id="unavailable_slot_start_' + index + '" name="unavailable_slot[' + index + '][start]">To : <input type="time" id="unavailable_slot_end_' + index + '" name="unavailable_slot[' + index + '][end]">';
		option_box += '<button type="button" id="svc_time_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_cpn_unavailable_time(this)">' + crossSign + '</button><span id="unavailable_slot_error_' + index + '" style="color: red; display: none;"></span></span></div>';
		jQuery('td.time_option_field span.time_input_span:last').after(option_box);
	} else {
		var option_box = '<div><span class="time_input_span">From : <input type="time" id="unavailable_slot_start_1" name="unavailable_slot[1][start]"> To : <input type="time" id="unavailable_slot_end_1" name="unavailable_slot[1][end]">';
		option_box += '<button type="button" id="cpn_time_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_cpn_unavailable_time(this)">' + crossSign + '</button><span id="unavailable_slot_error_1" style="color: red; display: none;"></span></span></div>';
		jQuery('td.time_option_field span.add_time_button').before(option_box);
	}
}

// Add event specific dates in coupon page
function bm_add_event_date_cpn() {
	var total_elements = jQuery('td.event_date_option_field span.event_date_input_span').length;
	// console.log(total_elements);
	var date = new Date(jQuery.now());
	var crossSign = "✕";
	date = date.getFullYear() + "-" + padWithZeros((date.getMonth() + 1)) + "-" + padWithZeros(date.getDate());

	if (total_elements !== 0) {
		var id = jQuery('td.event_date_option_field span.event_date_input_span:last input').attr('id');
		var index = Number(id.split("_")[3]) + 1;
		var option_box = '<span class="event_date_input_span"><input type="date" id="start_date_val_' + index + '" name="start_date_val[' + index + '][date]" min="' + date + '">';
		option_box += '<input type="text" id="start_date_desc_' + index + '" name="start_date_val[' + index + '][desc]" >';
		option_box += '<button type="button" id="svc_event_date_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_svc_event_date(this)">' + crossSign + '</button></span>'
		jQuery('td.event_date_option_field span.event_date_input_span:last').after(option_box);
	} else {
		var option_box = '<span class="event_date_input_span"><input type="date" id="start_date_val_1" name="start_date_val[1][date]" min="' + date + '">';
		option_box += '<input type="text" id="start_date_desc_1" name="start_date_val[1][desc]">';
		option_box += '<button type="button" id="svc_event_date_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_svc_event_date(this)">' + crossSign + '</button></span>'
		jQuery('td.event_date_option_field span.event_add_dates_button').before(option_box);
	}
}

// Remove unavailable dates in coupon page
function bm_remove_cpn_unavailable_date($this) {
	if (confirm(bm_normal_object.remove_cpn_unavl_date)) {
		jQuery($this).parent('span').remove();
		jQuery('[id^=cpn_unavailable_date_]').each(function (id, item) {

			var counter = id + 1;
			jQuery(item).attr('id', 'cpn_unavailable_date_' + counter);
			jQuery(item).attr('name', 'coupon_unavailability[dates][' + counter + ']');
		});
	}
}
// Remove unavailable time slot in coupon page 
function bm_remove_cpn_unavailable_time($this) {

	if (confirm(bm_normal_object.remove_svc_unavl_date)) {
		// jQuery($this).closest('span.time_input_span').remove();
		jQuery($this).parent('span').remove();
		jQuery('[id^=unavailable_slot_start_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'unavailable_slot_start_' + counter);
			jQuery(item).attr('name', 'unavailable_slot[' + counter + '][start]');
		});
		jQuery('[id^=unavailable_slot_end_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'unavailable_slot_end_' + counter);
			jQuery(item).attr('name', 'unavailable_slot[' + counter + '][end]');
		});

	}
}
// Remove unavailable dates in coupon page
function bm_remove_svc_event_date($this) {
	// console.log(this);
	if (confirm(bm_normal_object.remove_cpn_event_date)) {
		jQuery($this).parent('span').remove();
		// event_date_input_span
		jQuery('[id^=start_date_val_]').each(function (id, item) {
			var counter = id + 1;
			console.log(counter);
			jQuery(item).attr('id', 'start_date_val_' + counter);
			jQuery(item).attr('name', 'start_date_val[' + counter + '][date]');
		});
		jQuery('[id^=start_date_desc_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'start_date_desc_' + counter);
			jQuery(item).attr('name', 'start_date_val[' + counter + '][desc]');
		});
	}
}

// Redirect to edit coupon page
jQuery(document).on('click', '#editcoupon', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_coupon&id=' + id;
});

function maxSpend() {
	min = +document.getElementById("min_spend").value;
	max = +document.getElementById("max_spend").value;
	if (min !== 0 && max !== 0 && min > max) {
		document.getElementById('validate_amount').innerHTML = bm_error_object.max_cpn_amt;
	}
	else {
		document.getElementById('validate_amount').innerHTML = "";
	}
}

//check discount amount
function couponAmountCheck() {
	val = document.getElementById("discount_amount").value;
	var regex = /^[1-9]\d*(\.\d+)?$/;
	if (!val.match(regex)) {
		document.getElementById('dis_validate_amount').innerHTML = bm_error_object.numeric_field;
	} else {
		document.getElementById('dis_validate_amount').innerHTML = "";
	}
}

//check coupon code
function checkCouponCode(value) {
	if (value.trim().length < 4) {
		document.getElementById("error_message").innerHTML = bm_error_object.code_error;
	} else {
		document.getElementById("error_message").innerHTML = "";
	}
}
