jQuery(document).on('click', '#check_checkout_discount', function (e) {
	e.preventDefault();
	jQuery(document).find('div.age_errortext').html('');
	jQuery(document).find('span.service_discount_text').addClass('hidden');
	jQuery(document).find('span.service_discount_text').html('');
	var booking_key = backend_order_keys.booking_key ? backend_order_keys.booking_key : '';
	var service_price = jQuery('#service_cost_float').val();
	var extra_svc_cost = jQuery('#extra_svc_cost').val() !== '' ? jQuery('#extra_svc_cost').val() : 0;
	sessionStorage.setItem("service_old_cost_" + booking_key, service_price);
	var total_cost = jQuery('#has_extra').is(':checked') && extra_svc_cost != 0 ? parseFloat(service_price) + parseFloat(extra_svc_cost) : parseFloat(service_price);
	var extra_svc_booked = [];
	var total_extra_slots_booked = [];
	var b = 0;
	var formData = {};
	var ageFromData = {};
	var ageToData = {};
	var ageTotalData = {};

	if (jQuery('#has_extra').is(':checked') && jQuery('[id^=extra_svc_booked_]').length > 0) {
		jQuery('[id^=extra_svc_booked_]').each(function (id, item) {
			if (this.checked) {
				extra_svc_booked.push(this.value);
			}
		});
	}

	if (jQuery('#has_extra').is(':checked') && jQuery('.extra_quantity').length > 0 && extra_svc_booked.length > 0) {
		jQuery('[id^=extra_quantity_]').each(function (id, item) {
			if (!jQuery(this).prop('disabled')) {
				total_extra_slots_booked.push(parseInt(this.value));
			}
		});
	}

	jQuery('.checkout_age_range_fields :input').map(function () {
		var type = jQuery(this).prop('type');
		var value = jQuery(this).val();
		var index = jQuery(this).attr('id').split('_')[3];

		if (type !== 'button' && type !== 'submit' && type !== 'reset' && type !== 'search') {
			if (value == '') {
				jQuery(document).find('div.age_errortext').html(bm_error_object.fill_up_age_fields);
				b++;
			} else if ((value < 0) || (value % 1 != 0)) {
				jQuery(document).find('div.age_errortext').html(bm_error_object.invalid_total);
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
		formData['booking_key'] = booking_key;
		formData['service_id'] = jQuery('#service_id').val();
		formData['booking_slots'] = jQuery('#booking_slots').val();
		formData['booking_date'] = jQuery('#booking_date').val();
		formData['service_name'] = jQuery('#service_name').val();
		formData['total_service_booking'] = jQuery('#total_service_booking').val();
		formData['extra_svc_booked'] = extra_svc_booked;
		formData['total_extra_slots_booked'] = total_extra_slots_booked;
		formData['base_svc_price'] = jQuery('#base_svc_price_float').val();
		formData['service_cost'] = service_price;
		formData['total_cost'] = total_cost;
		formData['subtotal'] = total_cost;
		formData['extra_svc_cost'] = extra_svc_cost;

		var data = { 'action': 'bm_check_backend_discount', 'post': formData, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			jQuery('.loader_modal').hide();
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			var data = jsondata.data ? jsondata.data : '';
			var negative_discount = jsondata.negative_discount ? jsondata.negative_discount : 0;
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (status == 'success' && typeof (data.subtotal) != "undefined" && typeof (data.discount) != "undefined" && typeof (data.total) != "undefined") {
				var discount = parseFloat(data.discount).toFixed(2);
				var new_service_cost = service_price > discount ? service_price - discount : discount - service_price;

				jQuery('#service_cost').val(changePriceFormat(new_service_cost));
				jQuery('#service_discount').val(discount);
				var discount_text = currency_position == 'before' ? currency_symbol + changePriceFormat(discount) : changePriceFormat(discount) + currency_symbol;
				var discount_class = negative_discount == 1 ? 'negative_discount' : 'positive_discount';
				jQuery(document).find('span.service_discount_text').html(bm_normal_object.service_discount_text + '<span class="' + discount_class + '">' + discount_text + '</span>');
				jQuery(document).find('span.service_discount_text').removeClass('hidden');

				jQuery([document.documentElement, document.body]).animate({
					scrollTop: jQuery("#service_cost").offset().top - 100
				}, 2000);
			} else if (status == 'excess') {
				jQuery(document).find('div.age_errortext').html(bm_error_object.excess_order_total);
			} else {
				jQuery(document).find('div.age_errortext').html(bm_error_object.server_error);
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
	jQuery(document).find('div.age_errortext').html('');
	var booking_key = backend_order_keys.booking_key ? backend_order_keys.booking_key : '';
	var service_old_cost = sessionStorage.getItem("service_old_cost_" + booking_key);

	var data = { 'action': 'bm_reset_backend_discount', 'booking_key': booking_key, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		var data = jsondata.data ? jsondata.data : '';

		if (status == 'success' && typeof (data.subtotal) != "undefined" && typeof (data.discount) != "undefined" && typeof (data.total) != "undefined") {
			jQuery('#service_cost').val(changePriceFormat(service_old_cost));
			jQuery('#service_discount').val(0);
			jQuery(document).find('span#checkout_subtotal').html('');
			jQuery(document).find('span#checkout_discount').html('');
			jQuery(document).find('span#checkout_total').html('');

			jQuery(document).find('input#age_group_total_0').val(0);
			jQuery(document).find('input#age_group_total_1').val(0);
			jQuery(document).find('input#age_group_total_2').val(0);
			jQuery(document).find('input#age_group_total_3').val(0);

			jQuery(document).find('span.service_discount_text').addClass('hidden');
			jQuery(document).find('span.service_discount_text').html('');

		} else {
			jQuery(document).find('div.age_errortext').html(bm_error_object.server_error);
		}
	});
});

jQuery(document).on('click', '#savefrontorder', function (e) {
	jQuery('.order_field_errortext').html('');
	jQuery('.order_field_errortext').hide();
	jQuery('.all_order_error_text').html('');

	var tel_pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;
	var b = 0;

	jQuery('.bm_order_field_required').each(
		function (index, element) {
			var value = jQuery(this).children('select').length != 0 ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());
			
			console.log(value);

			if (jQuery(this).closest('table').attr('id') == 'billing_details' || jQuery(this).closest('table').attr('id') == 'shipping_details') {
				if (jQuery(this).closest('table').is(':visible')) {
					var type = jQuery(this).children('input').attr('type');

					if (type == 'email') {
						var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;

						if (value == "") {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
							jQuery(this).children('.order_field_errortext').show();
							b++;
						} else if (!pattern.test(value)) {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.invalid_email);
							jQuery(this).children('.order_field_errortext').show();
							b++;
						}

					} else if (type == 'tel') {
						if (value == "") {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
							jQuery(this).children('.order_field_errortext').show();
							b++;
						} else if (!tel_pattern.test(value)) {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.invalid_contact);
							jQuery(this).children('.order_field_errortext').show();
							b++;
						}
					} else if (value == "") {
						jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
						jQuery(this).children('.order_field_errortext').show();
						b++;
					}
				}
			} else {
				if (value == "") {
					jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
					jQuery(this).children('.order_field_errortext').show();
					b++;
				}
			}
		}
	);

	if (jQuery(document).find('#billing_contact').val() == '') {
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').html(bm_error_object.required_field);
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').show();
		b++;
	} else if (!tel_pattern.test(jQuery(document).find('#billing_contact').val())) {
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').html(bm_error_object.invalid_contact);
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').show();
		b++;
	}

	if (jQuery(document).find('#shipping_contact').val() == '') {
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').html(bm_error_object.required_field);
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').show();
		b++;
	} else if (!tel_pattern.test(jQuery(document).find('#billing_contact').val())) {
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').html(bm_error_object.invalid_contact);
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').show();
		b++;
	}

	if (b > 0) {
		return false;
	} else {
		return true;
	}
});


function bm_fetch_bookable_services(category_id) {
	jQuery('.order_field_errortext').html('');
	jQuery('.order_field_errortext').hide();
	jQuery('.all_order_error_text').html('');
	jQuery('.fetch_services_by_category_order_field_errortext').html('');
	jQuery('.fetch_services_by_category_order_field_errortext').hide();

	var post = {
		'id': category_id,
		'date': jQuery('#booking_date').val(),
	}

	if (category_id !== '') {
		var data = { 'action': 'bm_fetch_bookable_services_by_category_id_and_date', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			jQuery('#service_id').html('');

			if (jsondata.status == true) {
				var services = jsondata.services;
				if (services.length !== 0) {
					jQuery('#service_id').prop('disabled', false);
					jQuery('#service_id').append(jQuery('<option></option>').val('').text(bm_normal_object.select_service));
					for (var i = 0; i < services.length; i++) {
						jQuery('#service_id').append(jQuery('<option></option>').val(services[i].id).text(services[i].service_name));
					};
				} else {
					jQuery('.fetch_services_by_category_order_field_errortext').html(bm_error_object.no_bookable_services);
					jQuery('.fetch_services_by_category_order_field_errortext').show();
				}
			} else {
				jQuery('.fetch_services_by_category_order_field_errortext').html(bm_error_object.service_error);
				jQuery('.fetch_services_by_category_order_field_errortext').show();
			}
		});
	} else {
		resetOrderPage();
	}
}


// Fetch service time slots by service id
function bm_fetch_service_time_slots_by_service_id(service_id) {
	jQuery('.order_field_errortext').html('');
	jQuery('.order_field_errortext').hide();
	jQuery('.all_order_error_text').html('');
	jQuery('.fetch_slots_by_service_id_order_field_errortext').html('');
	jQuery('.fetch_slots_by_service_id_order_field_errortext').hide();
	jQuery('#service_name').val(jQuery('#service_id :selected').text());

	resetTimeSlots();
	resetOrderPageServicePrice();
	resetNoOfServiceSelection();

	if (service_id !== '') {
		var post = {
			'id': service_id,
			'date': jQuery('#booking_date').val(),
		}

		var data = { 'action': 'bm_fetch_new_order_service_time_slots', 'post': post, 'nonce': bm_ajax_object.nonce };

		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			jQuery('#booking_slots').html('');
			var slots = jQuery.parseJSON(response);

			if (slots != null && slots.length != 0) {
				jQuery('#booking_slots').prop('disabled', false);
				jQuery('#booking_slots').append(jQuery('<option></option>').val('').text(bm_normal_object.select_slot));

				jQuery.each(slots, function (index, slot) {
					jQuery('#booking_slots').append(
						jQuery("<option></option>").val(slot).text(slot)
					);
				});
			} else {
				jQuery('.fetch_slots_by_service_id_order_field_errortext').html(bm_error_object.no_time_slots);
				jQuery('.fetch_slots_by_service_id_order_field_errortext').show();
			}
		});
	}
}

// Reset order page
function resetOrderPage() {
	jQuery('#service_id').prop('disabled', true);
	jQuery('#service_id').html('');
	resetNoOfServiceSelection();
	resetTimeSlots();
	resetOrderPageServicePrice();
}


// Reset order page service content
function resetTimeSlots() {
	jQuery('#booking_slots').prop('disabled', true);
	jQuery('#booking_slots').html('');
}


function resetNoOfServiceSelection() {
	jQuery('#total_service_booking').prop('disabled', true);
	jQuery('#total_service_booking').html('');
}

// Reset order page service price content
function resetOrderPageServicePrice() {
	jQuery('#base_svc_price').val('');
	jQuery('#service_cost').val('');
	jQuery('#service_discount').val(0);
	jQuery('#base_svc_price').prop('disabled', true);
	jQuery('#service_cost').prop('disabled', true);
	jQuery('#base_svc_price').prop('readonly', false);
	jQuery('#service_cost').prop('readonly', false);
	jQuery('.service_price_tr').hide();
	jQuery('.service_total_price_tr').hide();
	jQuery('.order_details').addClass('hidden');
	resetExtraContent();
	resetCustomerDetails();
}

// Reset order page extra service content
function resetExtraContent() {
	jQuery('.service_add_extra').hide();
	jQuery('.extra_content').html('');
	jQuery('#has_extra').prop('checked', false);
	jQuery('.service_extras').hide();
}


// Reset order page customer details content
function resetCustomerDetails() {
	jQuery('.billing_details').hide();
	jQuery('.shipping_details').hide();
}

// Fetch service no of slots
function bm_fetch_bookable_no_of_slots_by_slot($this) {
	jQuery('.order_field_errortext').html('');
	jQuery('.order_field_errortext').hide();
	jQuery('.all_order_error_text').html('');
	jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').html('');
	jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').hide();
	jQuery('#total_service_booking').html('');

	var slot = jQuery($this).val();

	if (slot !== '') {
		var post = {
			'id': jQuery('#service_id').val(),
			'date': jQuery('#booking_date').val(),
			'slots': slot,
		}

		var data = { 'action': 'bm_fetch_mincap_and_cap_left', 'post': post, 'nonce': bm_ajax_object.nonce };

		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);

			if (jsondata.status == true) {
				var slot_info = jsondata.slot_info ? jsondata.slot_info : {};
				var mincap = slot_info.slot_min_cap ? parseInt(slot_info.slot_min_cap) : '';
				var capacity_left = slot_info.capacity_left ? parseInt(slot_info.capacity_left) : '';

				if (capacity_left !== '' && mincap !== '') {
					jQuery('#total_service_booking').prop('disabled', false);
					jQuery('#total_service_booking').append(jQuery('<option></option>').val('').text(bm_normal_object.select_persons));

					for (var i = mincap; i <= capacity_left; i += mincap) {
						jQuery('#total_service_booking').append(
							jQuery("<option></option>").val(i).text(i)
						);
					}

				} else {
					jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').html(bm_error_object.no_slot_capacity);
					jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').show();
				}
			} else {
				jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').html(bm_error_object.service_error);
				jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').show();
			}
		});
	} else {
		resetOrderPageServicePrice();
	}
}

// Event handler for calculating price of a product
function bm_fetch_svc_total_price() {
	jQuery('.order_field_errortext').html('');
	jQuery('.order_field_errortext').hide();
	jQuery('.all_order_error_text').html('');

	var totalbooking = jQuery('#total_service_booking').val();

	if (totalbooking !== '') {
		var post = {
			'id': jQuery('#service_id').val(),
			'date': jQuery('#booking_date').val(),
		}

		var data = { 'action': 'bm_fetch_service_price_for_backend_order', 'post': post, 'nonce': bm_ajax_object.nonce };

		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				if (typeof (jsondata.price) != "undefined" && jsondata.price != null) {
					var price = jsondata.price;
					jQuery('#base_svc_price_float').val(price);
					jQuery('#base_svc_price').val(changePriceFormat(price));
					jQuery('#base_svc_price').prop('disabled', false);
					jQuery('#base_svc_price').prop('readonly', true);
					jQuery('.service_price_tr').show();

					var total = calculatePrice(price, jQuery('#total_service_booking').val());

					jQuery('#service_cost_float').val(total);
					jQuery('#service_cost').val(changePriceFormat(total));
					jQuery('#service_cost').prop('disabled', false);
					jQuery('#service_cost').prop('readonly', true);
					jQuery('.service_total_price_tr').show();
					jQuery('.service_add_extra').show();
					jQuery('.service_add_discount').show();
					setIntlInputForBackendOrder();
					jQuery('.billing_details').show();
					jQuery('.shipping_details').show();
				} else {
					jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').html(bm_error_object.service_error);
					jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').show();
				}
			} else {
				jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').html(bm_error_object.service_error);
				jQuery('.fetch_no_of_slots_by_slot_data_order_field_errortext').show();
			}

		});
	} else {
		resetOrderPageServicePrice();
	}
}

// Fetch service no of slots
function bm_fetch_service_extra() {
	jQuery('.extra_content').html('');
	jQuery('.service_extras').hide();

	if (jQuery('#has_extra').is(':checked')) {
		var post = {
			'date': jQuery('#booking_date').val(),
			'id': jQuery('#service_id').val(),
		}

		var data = { 'action': 'bm_fetch_service_extras_for_backend_order', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			if (response) {
				var extras = jQuery.parseJSON(response);
				console.log(extras);
				if (extras.length > 0) {
					jQuery.each(extras, function (index, extra) {
						addExtraService(index, extra);
					});
					jQuery('.service_extras').show();
				} else {
					jQuery('#has_extra').prop('checked', false);
					jQuery('.extra_content').html(bm_error_object.no_extras);
					jQuery('.service_extras').show();
				}
			} else {
				jQuery('#has_extra').prop('checked', false);
				jQuery('.extra_content').html(bm_error_object.service_error);
				jQuery('.service_extras').show();
			}
		});
	} else {
		jQuery('.extra_content').html('');
		jQuery('.service_extras').hide();
	}
}

// Fetch service no of slots
function bm_fetch_service_price_discount_module() {
	jQuery('.price_module_discount_content').html('');
	jQuery('.price_module_data').hide();

	if (jQuery('#has_discount').is(':checked')) {
		var post = {
			'date': jQuery('#booking_date').val(),
			'id': jQuery('#service_id').val(),
		}

		var data = { 'action': 'bm_fetch_discount_module_for_backend_order', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			var html = jsondata.html ? jsondata.html : '';

			if (status == true) {
				if (html != '') {
					jQuery('.price_module_discount_content').html(html);
					jQuery('.price_module_data').show();
				} else {
					jQuery('#has_discount').prop('checked', false);
					jQuery('.price_module_discount_content').html(bm_error_object.no_price_module_date);
					jQuery('.price_module_data').show();
				}
			} else {
				jQuery('#has_discount').prop('checked', false);
				jQuery('.price_module_discount_content').html(bm_error_object.server_error);
				jQuery('.price_module_data').show();
			}
		});
	} else {
		jQuery('.price_module_discount_content').html('');
		jQuery('.price_module_data').hide();
	}
}

// Function to add extra service to the list
function addExtraService(index, product) {
	var listItem = jQuery("<li class='extra_services'></li>");

	// Checkbox
	var checkInput = "<span class='add_extra bm-checkbox-td'><span class='extra_backend_label'>" + bm_normal_object.add + "</span><input type='checkbox' name='extra_svc_booked[]' id='extra_svc_booked_" + index + "' class='auto-checkbox bm_toggle' value=" + (product && product.id ? product.id : '') + " onchange='getExtraServicePrice(this)'><label for='extra_svc_booked_" + index + "'></label>&nbsp;&nbsp;</span>";
	listItem.append(checkInput);

	// Name
	var nameInput = jQuery("<input type='text' class='regular-text extra_name' readonly>").val(product && product.extra_name ? product.extra_name : "");
	listItem.append("<label><strong>" + bm_normal_object.name + ":</strong></label> ");
	listItem.append(nameInput);
	listItem.append("<br>");

	// Price
	var priceInput = jQuery("<input type='text' class='regular-text extra_price' readonly>").val(product && product.extra_price ? product.extra_price : "");
	listItem.append("<label><strong>" + bm_normal_object.price + " (" + bm_normal_object.in + " " + bm_normal_object.currency_symbol + "):</strong></label> ");
	listItem.append(priceInput);
	listItem.append("<br>");

	// Quantity
	var quantityInput = jQuery("<input type='number' name='total_extra_slots_booked[]' class='regular-text extra_quantity' min='1' max=" + (product && product.cap_left ? product.cap_left : "") + " id='extra_quantity_" + index + "' onchange='checkExtraQuantityInputMaxValue(this)' disabled>").val("1");
	listItem.append("<label><strong>" + bm_normal_object.quantity + " (" + bm_normal_object.cap_left + ": <span class='max_cap_text'>" + (product && product.cap_left ? product.cap_left : "") + "</span>):</strong></label> ");
	listItem.append(quantityInput);
	listItem.append("<br>");

	// Price calculation
	var calculationElement = jQuery("<div class='extra-price-calculation'></div>");
	listItem.append(calculationElement);

	listItem.append("<hr>");

	jQuery(".extra_content").append(listItem);

	// Calculate the initial price for the product
	calculateExtraPrice(listItem);
}

// Function to calculate the new price based on quantity and price inputs
function checkExtraQuantityInputMaxValue($this) {
	var id = jQuery($this).attr('id');
	var index = Number(id.split('_')[2]);
	var max = parseInt(jQuery($this).attr('max'));
	var min = parseInt(jQuery($this).attr('min'));

	if (parseInt(jQuery($this).val()) > max) {
		jQuery($this).val(min);
		calculateExtraPrice(jQuery($this).closest("li"), index);
	} else if (parseInt(jQuery($this).val()) <= max) {
		calculateExtraPrice(jQuery($this).closest("li"), index);
	}

	setExtraTotalInputValue();
}

// Function to calculate extra service total price based on quantity and price inputs
function calculateExtraPrice(listItem, index = '') {
	var priceInput = listItem.find(".extra_price");
	var quantityInput = listItem.find(".extra_quantity");
	var calculationElement = listItem.find(".extra-price-calculation");

	var price = parseFloat(priceInput.val());
	var quantity = parseInt(quantityInput.val());

	if (!isNaN(price) && !isNaN(quantity)) {
		var totalPrice = price * quantity;
		calculationElement.text(bm_normal_object.total_price + ": " + changePriceFormat(totalPrice.toFixed(2)) + " (" + bm_normal_object.in + " " + bm_normal_object.currency_symbol + ")");
	} else {
		calculationElement.text("");
	}

	if (index !== '') {
		if (jQuery('#extra_svc_booked_' + index).is(':checked')) {
			if (jQuery('#extra_svc_cost_' + index).length == 0) {
				var priceInput = jQuery("<input type='hidden' id='extra_svc_cost_" + index + "'>").val(totalPrice ? changePriceFormat(totalPrice.toFixed(2)) : "");
				jQuery('.extra_service_costs').append(priceInput);
			} else {
				jQuery('#extra_svc_cost_' + index).val('');
				jQuery('#extra_svc_cost_' + index).val(totalPrice.toFixed(2));
			}
		} else {
			if (jQuery('#extra_svc_cost_' + index).length != 0) {
				jQuery('#extra_svc_cost_' + index).remove();
				setExtraTotalInputValue();
			}
		}
	}
}

// Function to calculate extra service total price based on quantity and price inputs
function getExtraServicePrice($this) {
	var id = jQuery($this).attr('id');
	var index = Number(id.split('_')[3]);
	var listItem = jQuery($this).closest("li");
	var quantityInput = listItem.find(".extra_quantity");
	calculateExtraPrice(listItem, index);
	setExtraTotalInputValue();

	if (jQuery($this).is(':checked')) {
		quantityInput.prop('disabled', false);
	} else {
		quantityInput.prop('disabled', true);
	}
}

// Function to set extra service total price input value
function setExtraTotalInputValue() {
	var totalPrice = 0.00;
	jQuery("[id^='extra_svc_cost_']").each(function () {
		totalPrice += parseFloat(jQuery(this).val());
	});

	jQuery('#extra_svc_cost').val(totalPrice.toFixed(2));
}

// Fetch billing details from order page
jQuery(document).ready(function ($) {
	$("#shipping_same_as_billing").on("click", function () {
		var ship = $(this).is(":checked");
		$("[id^='shipping_']").each(function () {
			var tmpID = this.id.split('shipping_')[1];
			$(this).val(ship ? $("#" + 'billing_' + tmpID).val() : "");
		});
	});
});

// Event handler for calculating price of a product
function calculatePrice(price, quantity) {
	var price = parseFloat(price);
	var quantity = parseInt(quantity);
	var totalPrice = 0;

	if (!isNaN(price) && !isNaN(quantity)) {
		var totalPrice = price * quantity;
		var totalPrice = totalPrice.toFixed(2);
	}

	return totalPrice;
}

// Fetch billing details from order page
jQuery(document).ready(function ($) {
	if (getUrlParameter('id')) {
		setIntlInputForBackendOrder();
	}
	setIntlInputForCustomeForm();
});

//International tel input for phone form fields for backend order
function setIntlInputForBackendOrder() {
	jQuery('#order_form :input').map(function () {
		var type = jQuery(this).prop("type");
		var id = jQuery(this).attr("id");

		if ((type == "tel")) {
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

//International tel input for phone form fields for backend order
function setIntlInputForCustomeForm() {
	jQuery('#customer_form :input').map(function () {
		var type = jQuery(this).prop("type");
		var id = jQuery(this).attr("id");

		if (type == "tel") {
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

// Event handler for adding a new product
jQuery(document).on("change", "#booking_date", function () {
	jQuery('#service_category').val('');
	resetOrderPage();
});