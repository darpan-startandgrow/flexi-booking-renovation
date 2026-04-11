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