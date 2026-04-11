// Functions By Darpan

jQuery(document).ready(function ($) {
	$.datepicker.setDefaults($.datepicker.regional[bm_normal_object.current_language]);

	// Success and Error Messages Display Limit
	$("#errorMessage").delay(5000).fadeOut(300);
	$("#successMessage").delay(3000).fadeOut(300);

	// Sort Service Listing
	$('.service_records').sortable({
		axis: "y",
		items: ".single_service_record",
		containment: "#service_records_listing",
		revert: true,
		scroll: true,
		cursor: "move",
		update: function () {
			var ids = {};
			var pagenum = sessionStorage.getItem("servicePagno");
			$(".service_records .single_service_record .service_listing_number").each(function (i) {
				ids[i] = $(this).data('id');
			})
			bm_sort_service_listing(ids, pagenum != null ? pagenum : '1');
		}
	}).disableSelection();

	// Sort Category Listing
	$('.category_records').sortable({
		axis: "y",
		items: ".single_category_record",
		containment: "#category_records_listing",
		revert: true,
		scroll: true,
		cursor: "move",
		update: function () {
			var ids = {};
			var pagenum = sessionStorage.getItem("categoryPagno");
			$(".category_records .single_category_record .category_listing_number").each(function (i) {
				ids[i] = $(this).data('id');
			})
			bm_sort_category_listing(ids, pagenum != null ? pagenum : '1');
		}
	}).disableSelection();


	// Display Condition for Tabs in Service Page
	if (getUrlParameter('extra_id') || sessionStorage.getItem("extravalue") != null) {
		if ($("#service_extra").not(':visible')) $('#service_extra').show();
		if (sessionStorage.getItem("extravalue") != null) {
			$('button.tablinks.active').removeClass('active');
			$("#extra_button").addClass("active");
		}
	} else if (sessionStorage.getItem("galleryvalue") != null) {
		if ($("#service_gallery").not(':visible')) $('#service_gallery').show();
		$('button.tablinks.active').removeClass('active');
		$("#gallery_button").addClass("active");
	} else if ((sessionStorage.getItem("variableprice") != null)) {
		if ($("#price_calendar").not(':visible')) $('#price_calendar').show();
		$('button.tablinks.active').removeClass('active');
		$("#price_calendar_button").addClass("active");
	} else if ((sessionStorage.getItem("variablehour") != null)) {
		if ($("#stopsales_calendar").not(':visible')) $('#stopsales_calendar').show();
		$('button.tablinks.active').removeClass('active');
		$("#stopsales_calendar_button").addClass("active");
	} else if ((sessionStorage.getItem("variablesaleswitch") != null)) {
		if ($("#saleswitch_calendar").not(':visible')) $('#saleswitch_calendar').show();
		$('button.tablinks.active').removeClass('active');
		$("#saleswitch_calendar_button").addClass("active");
	} else if (sessionStorage.getItem("variablecapacity") != null) {
		if ($("#capacity_calendar").not(':visible')) $('#capacity_calendar').show();
		$('button.tablinks.active').removeClass('active');
		$("#capacity_calendar_button").addClass("active");
	} else if (sessionStorage.getItem("variabletimeslot") != null) {
		if ($("#time_slots_calendar").not(':visible')) $('#time_slots_calendar').show();
		$('button.tablinks.active').removeClass('active');
		$("#time_slot_button").addClass("active");
	} else if (sessionStorage.getItem("svcsettingstab") != null) {
		if ($("#svc_settings_section").not(':visible')) $('#svc_settings_section').show();
		$('button.tablinks.active').removeClass('active');
		$("#svc_settings_button").addClass("active");
	} else {
		if ($("#service_details").not(':visible')) $('#service_details').show();
	}


	// Display Condition for Tabs in Fields Page
	if ($("#field_listing").not(':visible')) $('#field_listing').addClass('flexdisplay');

	// Service Image Selection
	var custom_uploader;

	$('.svc-image').click(function (e) {
		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		//Extend the wp.media object
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
					$('#svc_image_id').val(attachment.id);
					$('#svc_image_preview').attr('src', attachment.url);
					$('.svc_image_container').show();
				}
			} else {
				alert(bm_error_object.file_type_not_supported);
			}

		});

		//Open the uploader dialog
		custom_uploader.open();
	});



	// Service Extra Add Button
	$("#add_extra").click(function (e) {
		if ($("#svc_extra_fields").not(':visible')) $("#svc_extra_fields").css('display', 'block');
		$("#if_extra_svc").val('1');
		if ($("#extraTitle").is(':visible')) $("#extraTitle").css('display', 'none');
		if ($("#existing_extra_content").is(':visible')) $("#existing_extra_content").hide();
	});



	// Service Extra Cancel Button
	$("#cancel_extra").click(function (e) {
		if ($("#svc_extra_fields").is(':visible')) $("#svc_extra_fields").css('display', 'none');
		$("#if_extra_svc").val('0');
		if ($("#extraTitle").not(':visible')) $("#extraTitle").css('display', 'block');
		if ($("#existing_extra_content").not(':visible')) $("#existing_extra_content").show();
	});
});



// Ajax for sorting service listing on Page Load
function bm_sort_service_listing(ids = [], pagenum = 1) {
    var post = {
        'pagenum': pagenum ? pagenum : jQuery('#service_pagenum').val(),
        'base': jQuery(location).attr("href"),
        'limit': jQuery.trim(jQuery('#limit_count').val()),
        'ids': ids,
    }

    var data = { 'action': 'bm_sort_service_listing', 'post': post, 'nonce': bm_ajax_object.nonce };
    jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
        var jsondata = jQuery.parseJSON(response);
        var status = jsondata.status ? jsondata.status : '';
        if (status == true) {
            jQuery(".service_records").html('');
            jQuery(".service_pagination").html('');
            var services = jsondata.services ? jsondata.services : [];
            var category_name = jsondata.category_name ? jsondata.category_name : '';
            var pagination = jsondata.pagination ? jsondata.pagination : '';
            var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : '';
            var serviceListing = '';

            for (var i = 0; i < services.length; i++) {
                serviceListing += "<tr class='single_service_record ui-sortable-handle'><form role='form' method='post'>" +
                    "<td style='text-align: center;cursor:move;' data-id='" + services[i].id + "' data-order=" + (i + 1) + " data-position='" + services[i].service_position + "' class='service_listing_number'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
                    "<td style='text-align: center;cursor:move;' title=" + services[i].service_name + ">" + services[i].service_name.substring(0, 40) + '...' + " </td>" +
                    "<td style='text-align: center;' title=" + (category_name[i] ? category_name[i] : '') + ">" + (category_name[i] ? category_name[i].substring(0, 40) + '...' : '') + " </td>" +
                    "<td style='text-align: center;' class='bm-checkbox-td'>" +
                    "<input name='bm_show_service_in_front' type='checkbox' id='bm_show_service_in_front_" + services[i].id + "' class='regular-text auto-checkbox bm_toggle' " + (services[i].is_service_front == 1 ? 'checked' : '') + " onchange='bm_change_service_visibility(this)'>" +
                    "<label for='bm_show_service_in_front_" + services[i].id + "'></label>" +
                    "</td>" +
                    "<td style='text-align: center;'>" +
                    "<div class='copyMessagetooltip' style='margin-bottom: 5px;'>" +
                    "<input style='cursor:pointer;border:none;width:200px;padding: 2px 2px 6px 12px;font-family:serif;' class='copytextTooltip' value='[sgbm_single_service id=\"" + services[i].id + "\"]' onclick='bm_copy_text(this)' onmouseout='bm_copy_message(this)' readonly>" +
                    "<span class='tooltiptext'>" + bm_normal_object.copy_to_clipboard + "</span>" +
                    "<button type='button' class='bm-info-button' data-shortcode='sgbm_single_service' title='" + bm_normal_object.shortcode_info + "'>i</button>" +
                    "</div>" +
                    "<div class='copyMessagetooltip'>" +
                    "<input style='cursor:pointer;border:none;width:200px;padding: 2px 2px 6px 12px;font-family:serif;' class='copytextTooltip' value='[sgbm_single_service_calendar id=\"" + services[i].id + "\"]' onclick='bm_copy_text(this)' onmouseout='bm_copy_message(this)' readonly>" +
                    "<span class='tooltiptext'>" + bm_normal_object.copy_to_clipboard + "</span>" +
                    "<button type='button' class='bm-info-button' data-shortcode='sgbm_single_service_calendar' title='" + bm_normal_object.shortcode_info + "'>i</button>" +
                    "</div>" +
                    "</td>" +
                    "<td style='text-align: center;'>" +
                    "<button type='button' name='editsvc' id='editsvc' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + services[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
                    "<button type='button' name='delsvc' id='delsvc' title='" + bm_normal_object.remove + "' value='" + services[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
                    "</td>" +
                    "</form></tr>";
                current_pagenumber++;
            }
            jQuery(".service_records").append(serviceListing);
            jQuery(".service_pagination").append(pagination);

            jQuery('.bm-info-button').off('click').on('click', function() {
                var shortcode = jQuery(this).data('shortcode');
                var info = bm_shortcode_info[shortcode];
                
                if (info) {
                    jQuery('#bm-shortcode-title').text(info.title);
                    jQuery('#bm-shortcode-description').text(info.description);
                    
                    var attributesBody = jQuery('#bm-shortcode-attributes tbody');
                    attributesBody.empty();
                    
                    if (info.attributes.length > 0) {
                        jQuery.each(info.attributes, function(i, attr) {
                            attributesBody.append(
                                '<tr>' +
                                '<td>' + attr.name + '</td>' +
                                '<td>' + attr.description + '</td>' +
                                '<td>' + attr.default + '</td>' +
                                '</tr>'
                            );
                        });
                    } else {
                        attributesBody.append(
                            '<tr><td colspan="3">' + bm_normal_object.no_attributes + '</td></tr>'
                        );
                    }
                    
                    var examplesHtml = info.examples.join('\n');
                    jQuery('#bm-shortcode-examples').text(examplesHtml);
                    
                    jQuery('#bm-shortcode-info-modal').show();
                }
            });
        }
    });
}



// Redirect to edit service page
jQuery(document).on('click', '#editsvc', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_service&id=' + id;
});



// Redirect to edit template page
jQuery(document).on('click', '#edittemplate', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_template&id=' + id;
});



// Redirect to edit process page
jQuery(document).on('click', '#editprocess', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_notification_process&id=' + id;
});



// Redirect to edit order page
jQuery(document).on('click', '#editorder', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_order&id=' + id;
});

// Remove a service
jQuery(document).on('click', '#delsvc', function () {
	if (confirm(bm_normal_object.sure_remove_service)) {
		var post = {
			'pagenum': sessionStorage.getItem("servicePagno") != null ? sessionStorage.getItem("servicePagno") : jQuery('#category_pagenum').val(),
			'base': jQuery(location).attr("href"),
			'limit': jQuery.trim(jQuery('#limit_count').val()),
			'id': jQuery(this).val(),
		}

		var data = { 'action': 'bm_remove_service', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			if (status == true) {
				jQuery(".service_records").html('');
				jQuery(".service_pagination").html('');
				var services = jsondata.services ? jsondata.services : [];
				var category_name = jsondata.category_name ? jsondata.category_name : '';
				var pagination = jsondata.pagination ? jsondata.pagination : '';
				var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : '';
				var serviceListing = '';
				var j = 0;

				if (services.length != 0) {
					for (var i = 0; i < services.length; i++) {
						serviceListing += "<tr class='single_service_record ui-sortable-handle'><form role='form' method='post'>" +
							"<td style='text-align: center;cursor:move;' data-id='" + services[i].id + "' data-order=" + (i + 1) + " data-position='" + services[i].service_position + "' class='service_listing_number'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
							"<td style='text-align: center;cursor:move;' title=" + services[i].service_name + ">" + services[i].service_name.substring(0, 40) + '...' + " </td>" +
							"<td style='text-align: center;' title=" + (category_name[i] ? category_name[i] : '') + ">" + (category_name[i] ? category_name[i].substring(0, 40) + '...' : '') + "</td>" +
							"<td style='text-align: center;' class='bm-checkbox-td'>" +
							"<input name='bm_show_service_in_front' type='checkbox' id='bm_show_service_in_front_" + services[i].id + "' class='regular-text auto-checkbox bm_toggle' " + (services[i].is_service_front == 1 ? 'checked' : '') + " onchange='bm_change_service_visibility(this)'>" +
							"<label for='bm_show_service_in_front_" + services[i].id + "'></label>" +
							"</td>" +
							"<td style='text-align: center;'>" +
							"<div class='copyMessagetooltip'>" +
							'<input style="cursor:pointer;border:none;width:200px;padding: 2px 2px 6px 12px;font-family:serif;" class="copytextTooltip" id="copyInput_' + services[i].id + '" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>' +
							"<span class='tooltiptext' id='copyTooltip_" + services[i].id + "'>" + bm_normal_object.copy_to_clipboard + "</span>" +
							"</div></td>" +
							"<td style='text-align: center;'>" +
							"<button type='submit' name='editsvc' id='editsvc' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + services[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
							"<button type='submit' name='delsvc' id='delsvc' title='" + bm_normal_object.remove + "' value='" + services[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
							"</td>" +
							"</form></tr>";
						current_pagenumber++;
						j++;
					}
				} else {
					// serviceListing += "<td></td><td></td><td style='text-align: center;font-size: 14px'><b>" + bm_normal_object.no_services + "</b></td><td></td>";
					location.reload();
				}

				jQuery(".service_records").append(serviceListing);
				jQuery(".service_pagination").append(pagination);

				if (j > 0) {
					for (var i = 0; i < services.length; i++) {
						var id = services[i].id.toString().trim();
						var shortcode = '[sgbm_single_service id="' + id + '"]';
						jQuery('#copyInput_' + id).val(shortcode);
					}
				}
			}
		});
	}
});

// Change service visiblity
function bm_change_service_visibility($this) {
	var id = jQuery($this).attr('id');

	if (confirm(bm_normal_object.change_svc_visibility)) {
		var service_id = id.split('_')[5];
		var data = { 'action': 'bm_change_service_visibility', 'id': service_id, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				showMessage(bm_success_object.status_successfully_changed, 'success');
			} else {
				showMessage(bm_error_object.server_error, 'error');
			}
		});
	} else {
		if (jQuery($this).is(':checked')) {
			jQuery('#' + id).prop('checked', false);
		} else {
			jQuery('#' + id).prop('checked', true);
		}
	}
}



function bm_change_extra_service_visibility($this) {
	var id = jQuery($this).attr('id');

	if (confirm(bm_normal_object.change_svc_visibility)) {
		var extra_id = id.split('_')[6];
		var data = { 'action': 'bm_change_extra_service_visibility', 'id': extra_id, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				showMessage(bm_success_object.status_successfully_changed, 'success');
			} else {
				showMessage(bm_error_object.server_error, 'error');
			}
		});
	} else {
		if (jQuery($this).is(':checked')) {
			jQuery('#' + id).prop('checked', false);
		} else {
			jQuery('#' + id).prop('checked', true);
		}
	}
}




// Remove a template
jQuery(document).on('click', '#deltemplate', function () {
	if (confirm(bm_normal_object.sure_remove_template)) {
		var post = {
			'pagenum': sessionStorage.getItem("templatePagno") != null ? sessionStorage.getItem("templatePagno") : jQuery('#template_pagenum').val(),
			'base': jQuery(location).attr("href"),
			'limit': jQuery.trim(jQuery('#limit_count').val()),
			'id': jQuery(this).val(),
		}

		var data = { 'action': 'bm_remove_template', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				jQuery(".template_records").html('');
				jQuery(".template_pagination").html('');
				var templates = jsondata.templates ? jsondata.templates : [];
				var type_names = jsondata.type_name ? jsondata.type_name : '';
				var pagination = jsondata.pagination ? jsondata.pagination : '';
				var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : 1;
				var templateListing = '';
				var j = 0;

				if (templates.length != 0) {
					for (var i = 0; i < templates.length; i++) {
						templateListing += "<tr><form role='form' method='post'>" +
							"<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
							"<td style='text-align: center;' title=" + (templates[i].tmpl_name_en ? templates[i].tmpl_name_en : templates[i].tmpl_name_it) + ">" + (templates[i].tmpl_name_en ? templates[i].tmpl_name_en.substring(0, 80) : templates[i].tmpl_name_it.substring(0, 80)) + '...' + " </td>" +
							"<td style='text-align: center;' title=" + (type_names[i] ? type_names[i] : '') + ">" + (type_names[i] ? type_names[i].substring(0, 80) + '...' : '') + "</td>" +
							"<td style='text-align: center;' class='bm-checkbox-td'>" +
							"<input name='bm_template_status' type='checkbox' id='bm_template_status_" + templates[i].id + "' data-type='" + (templates[i].type ? templates[i].type : -1) + "' class='regular-text auto-checkbox bm_toggle' " + (templates[i].status == 1 ? 'checked' : '') + " onchange='bm_change_template_visibility(this)'>" +
							"<label for='bm_template_status_" + templates[i].id + "'></label>" +
							"</td>" +
							"<td style='text-align: center;'>" +
							"<button type='button' name='edittemplate' class='edit-button' id='edittemplate' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + templates[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
							"<button type='button' name='deltemplate' class='delete-button' id='deltemplate' title='" + bm_normal_object.remove + "' value='" + templates[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
							"</td>" +
							"</form></tr>";
						current_pagenumber++;
						j++;
					}
				} else {
					location.reload();
				}

				jQuery(".template_records").append(templateListing);
				jQuery(".template_pagination").append(pagination);
			}
		});
	}
});



// Remove a process
jQuery(document).on('click', '#delprocess', function () {
	if (confirm(bm_normal_object.sure_remove_process)) {
		var post = {
			'pagenum': sessionStorage.getItem("notificationProcessPagno") != null ? sessionStorage.getItem("notificationProcessPagno") : jQuery('#notification_process_pagenum').val(),
			'base': jQuery(location).attr("href"),
			'limit': jQuery.trim(jQuery('#limit_count').val()),
			'id': jQuery(this).val(),
		}

		var data = { 'action': 'bm_remove_process', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				jQuery(".notification_process_records").html('');
				jQuery(".notification_process_pagination").html('');
				var notificationProcesses = jsondata.notification_processes ? jsondata.notification_processes : [];
				var process_types = jsondata.process_type ? jsondata.process_type : [];
				var pagination = jsondata.pagination ? jsondata.pagination : '';
				var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : 1;
				var notificationProcessListing = '';
				var j = 0;

				if (notificationProcesses.length != 0) {
					for (var i = 0; i < notificationProcesses.length; i++) {
						notificationProcessListing += "<tr><form role='form' method='post'>" +
							"<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
							"<td style='text-align: center;' title=" + (notificationProcesses[i].name ? notificationProcesses[i].name : '') + ">" + (notificationProcesses[i].name ? notificationProcesses[i].name.substring(0, 80) : '') + '...' + " </td>" +
							"<td style='text-align: center;' title=" + (process_types[i] ? process_types[i] : '') + ">" + (process_types[i] ? process_types[i].substring(0, 80) : '') + '...' + " </td>" +
							"<td style='text-align: center;' class='bm-checkbox-td'>" +
							"<input name='bm_process_status' type='checkbox' id='bm_process_status_" + notificationProcesses[i].id + "' data-type='" + (notificationProcesses[i].type ? notificationProcesses[i].type : -1) + "' class='regular-text auto-checkbox bm_toggle' " + (notificationProcesses[i].status == 1 ? 'checked' : '') + " onchange='bm_change_process_visibility(this)'>" +
							"<label for='bm_process_status_" + notificationProcesses[i].id + "'></label>" +
							"</td>" +
							"<td style='text-align: center;'>" +
							"<button type='button' name='editprocess' class='edit-button' id='editprocess' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + notificationProcesses[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
							"<button type='button' name='delprocess' class='delete-button' id='delprocess' title='" + bm_normal_object.remove + "' value='" + notificationProcesses[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
							"</td>" +
							"</form></tr>";
						current_pagenumber++;
						j++;
					}
				} else {
					location.reload();
				}

				jQuery(".notification_process_records").append(notificationProcessListing);
				jQuery(".notification_process_pagination").append(pagination);
			}
		});
	}
});



// Ajax for sorting category listing on Page Load
function bm_sort_category_listing(ids = [], pagenum = 1) {
	var post = {
		'pagenum': pagenum ? pagenum : jQuery('#category_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'ids': ids,
	}

	var data = { 'action': 'bm_sort_category_listing', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		if (status == true) {
			jQuery(".category_records").html('');
			jQuery(".category_pagination").html('');
			var categories = jsondata.categories ? jsondata.categories : 0;
			var cat_ids = jsondata.cat_ids ? jsondata.cat_ids : '';
			var pagination = jsondata.pagination ? jsondata.pagination : '';
			var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : '';
			var categoryListing = '';
			jQuery(".overallCategoryShortcode").val('[sgbm_service_by_category ids="' + cat_ids + '"]');

			for (var i = 0; i < categories.length; i++) {
				categoryListing += "<tr class='single_category_record ui-sortable-handle'><form role='form' method='post'>" +
					"<td style='text-align: center;cursor:move;' data-id='" + categories[i].id + "' data-order=" + (i + 1) + " data-position='" + categories[i].cat_position + "' class='category_listing_number'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
					"<td style='text-align: center;cursor:move;' title=" + categories[i].cat_name + ">" + categories[i].cat_name.substring(0, 40) + '...' + " </td>" +
					"<td style='text-align: center;' class='bm-checkbox-td'>" +
					"<input name='bm_show_category_in_front' type='checkbox' id='bm_show_category_in_front_" + categories[i].id + "' class='regular-text auto-checkbox bm_toggle' " + (categories[i].cat_in_front == 1 ? 'checked' : '') + " onchange='bm_change_category_visibility(this)'>" +
					"<label for='bm_show_category_in_front_" + categories[i].id + "'></label>" +
					"</td>" +
					"<td style='text-align: center;'>" +
					"<div class='copyMessagetooltip'>" +
					'<input style="cursor:pointer;border:none;width:240px;padding: 2px 2px 6px 12px;font-family:serif;" class="copytextTooltip" id="copyInput_' + categories[i].id + '" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>' +
					"<span class='tooltiptext' id='copyTooltip_" + categories[i].id + "'>" + bm_normal_object.copy_to_clipboard + "</span>" +
					"</div></td>" +
					"<td style='text-align: center;'>" +
					"<button type='button' name='editcat' id='editcat' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + categories[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
					"<button type='button' name='delcat' id='delcat' title='" + bm_normal_object.remove + "' value='" + categories[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
					"</td>" +
					"</form></tr>";
				current_pagenumber++;
			}
			jQuery(".category_records").append(categoryListing);
			jQuery(".category_pagination").append(pagination);

			if (categoryListing != '') {
				for (var i = 0; i < categories.length; i++) {
					var id = categories[i].id.toString().trim();
					var shortcode = '[sgbm_service_by_category ids="' + id + '"]';
					jQuery('#copyInput_' + id).val(shortcode);
				}
			}
		}
	});
}



// Redirect to edit category page
jQuery(document).on('click', '#editcat', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_category&id=' + id;
});



// Redirect to edit customer page
jQuery(document).on('click', '#editcust', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_customer&id=' + id;
});



// Change category visiblity
function bm_change_category_visibility($this) {
	var id = jQuery($this).attr('id');

	if (confirm(bm_normal_object.change_cat_visibility)) {
		var category_id = id.split('_')[5];
		var data = { 'action': 'bm_change_category_visibility', 'id': category_id, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				showMessage(bm_success_object.status_successfully_changed, 'success');
			} else {
				showMessage(bm_error_object.server_error, 'error')
			}
		});
	} else {
		if (jQuery($this).is(':checked')) {
			jQuery('#' + id).prop('checked', false);
		} else {
			jQuery('#' + id).prop('checked', true);
		}
	}
}



// Change customer visiblity
function bm_change_customer_visibility($this) {
	var id = jQuery($this).attr('id');

	if (confirm(bm_normal_object.change_cust_visibility)) {
		var customer_id = id.split('_')[3];
		var data = { 'action': 'bm_change_customer_visibility', 'id': customer_id, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				showMessage(bm_success_object.status_successfully_changed, 'success');
			} else {
				showMessage(bm_error_object.server_error, 'error')
			}
		});
	} else {
		if (jQuery($this).is(':checked')) {
			jQuery('#' + id).prop('checked', false);
		} else {
			jQuery('#' + id).prop('checked', true);
		}
	}
}



// Remove a category
jQuery(document).on('click', '#delcat', function () {
	if (confirm(bm_normal_object.sure_remove_category)) {
		var post = {
			'pagenum': sessionStorage.getItem("categoryPagno") != null ? sessionStorage.getItem("categoryPagno") : jQuery('#category_pagenum').val(),
			'base': jQuery(location).attr("href"),
			'limit': jQuery.trim(jQuery('#limit_count').val()),
			'id': jQuery(this).val(),
		}
		var data = { 'action': 'bm_remove_category', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			if (status == true) {
				jQuery(".category_records").html('');
				jQuery(".category_pagination").html('');
				var categories = jsondata.categories ? jsondata.categories : [];
				var cat_ids = jsondata.cat_ids ? jsondata.cat_ids : '';
				var pagination = jsondata.pagination ? jsondata.pagination : '';
				var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : '';
				var categoryListing = '';
				var j = 0;
				jQuery(".overallCategoryShortcode").val('[sgbm_service_by_category ids="' + cat_ids + '"]');

				if (categories.length != 0) {
					for (var i = 0; i < categories.length; i++) {
						categoryListing += "<tr class='single_category_record ui-sortable-handle'><form role='form' method='post'>" +
							"<td style='text-align: center;cursor:move;' data-id='" + categories[i].id + "' data-order=" + (i + 1) + " data-position='" + categories[i].cat_position + "' class='category_listing_number'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
							"<td style='text-align: center;cursor:move;' title=" + categories[i].cat_name + ">" + categories[i].cat_name.substring(0, 40) + '...' + " </td>" +
							"<td style='text-align: center;' class='bm-checkbox-td'>" +
							"<input name='bm_show_category_in_front' type='checkbox' id='bm_show_category_in_front_" + categories[i].id + "' class='regular-text auto-checkbox bm_toggle' " + (categories[i].cat_in_front == 1 ? 'checked' : '') + " onchange='bm_change_category_visibility(this)'>" +
							"<label for='bm_show_category_in_front_" + categories[i].id + "'></label>" +
							"</td>" +
							"<td style='text-align: center;'>" +
							"<div class='copyMessagetooltip'>" +
							'<input style="cursor:pointer;border:none;width:240px;padding: 2px 2px 6px 12px;font-family:serif;" class="copytextTooltip" id="copyInput_' + categories[i].id + '" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>' +
							"<span class='tooltiptext' id='copyTooltip_" + categories[i].id + "'>" + bm_normal_object.copy_to_clipboard + "</span>" +
							"</div></td>" +
							"<td style='text-align: center;'>" +
							"<button type='button' name='editcat' id='editcat' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + categories[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
							"<button type='button' name='delcat' id='delcat' title='" + bm_normal_object.remove + "' value='" + categories[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
							"</td>" +
							"</form></tr>";
						current_pagenumber++;
						j++;
					}
				} else {
					// categoryListing += "<td></td><td></td><td style='text-align: center;font-size: 14px'><b>" + bm_normal_object.no_categories + "</b></td><td></td>";
					location.reload();
				}

				jQuery(".category_records").append(categoryListing);
				jQuery(".category_pagination").append(pagination);

				if (j > 0) {
					for (var i = 0; i < categories.length; i++) {
						var id = categories[i].id.toString().trim();
						var shortcode = '[sgbm_service_by_category ids="' + id + '"]';
						jQuery('#copyInput_' + id).val(shortcode);
					}
				}
			}
		});
	}
});



// Redirect to edit price module page
jQuery(document).on('click', '#editmodule', function () {
	var id = jQuery(this).val();
	window.location = 'admin.php?page=bm_add_external_service_price&id=' + id;
});



// Remove a price module
jQuery(document).on('click', '#delmodule', function () {
	if (confirm(bm_normal_object.sure_remove_prce_module)) {
		var post = {
			'pagenum': sessionStorage.getItem("priceModulePagno") != null ? sessionStorage.getItem("priceModulePagno") : jQuery('#price_module_pagenum').val(),
			'base': jQuery(location).attr("href"),
			'limit': jQuery.trim(jQuery('#limit_count').val()),
			'id': jQuery(this).val(),
		}

		var data = { 'action': 'bm_remove_price_module', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			var removeable_status = jsondata.is_removeable ? jsondata.is_removeable : '';

			if (status == true) {
				if (removeable_status == true) {
					jQuery(".price_module_records").html('');
					jQuery(".price_module_pagination").html('');
					var priceModules = jsondata.price_modules ? jsondata.price_modules : [];
					var pagination = jsondata.pagination ? jsondata.pagination : '';
					var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : 1;
					var priceModuleListing = '';
					var j = 0;

					if (priceModules.length != 0) {
						for (var i = 0; i < priceModules.length; i++) {
							priceModuleListing += "<tr class='single_price_module_record'><form role='form' method='post'>" +
								"<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
								"<td style='text-align: center;' title=" + (priceModules[i].module_name ? priceModules[i].module_name : '') + ">" + (priceModules[i].module_name ? priceModules[i].module_name.substring(0, 80) : '') + '...' + " </td>" +
								"<td style='text-align: center;'>" +
								"<button type='button' name='editmodule' class='edit-button' id='editmodule' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + priceModules[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
								"<button type='button' name='delmodule' class='delete-button' id='delmodule' title='" + bm_normal_object.remove + "' value='" + priceModules[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
								"</td>" +
								"</form></tr>";
							current_pagenumber++;
							j++;
						}
					} else {
						location.reload();
					}

					jQuery(".price_module_records").append(priceModuleListing);
					jQuery(".price_module_pagination").append(pagination);
				} else if (removeable_status == false) {
					showMessage(bm_error_object.linked_module, 'error')
				} else {
					showMessage(bm_error_object.server_error, 'error')
				}
			} else {
				showMessage(bm_error_object.server_error, 'error')
			}
		});
	}
});



// Restore an order
jQuery(document).on('click', '#restoreorder', function() {
    if (confirm(bm_normal_object.sure_restore_order)) {
        var archive_id = jQuery(this).val();
        var data = { 
            'action': 'bm_restore_order', 
            'id': archive_id, 
            'nonce': bm_ajax_object.nonce 
        };
        
        jQuery.post(bm_ajax_object.ajax_url, data, function(result) {
			if (result.success === true) {
				alert(bm_normal_object.order_restored);
				location.reload();
			} else {
				alert(result.data || bm_error_object.restore_failed);
			}
		});

    }
});



// Delete an order
jQuery(document).on('click', '#archiveorder', function() {
    if (confirm(bm_normal_object.sure_archive_order)) {
        var id = jQuery(this).val();
        var data = { 
            'action': 'bm_archive_order', 
            'id': id, 
            'nonce': bm_ajax_object.nonce 
        };
		jQuery.post(bm_ajax_object.ajax_url, data, function(result) {
			if (result.success === true) {
				alert(bm_normal_object.order_archived);
				location.reload();
			} else {
				alert(result.data || bm_error_object.server_error);
			}
		});
    }
});



// Delete an order
jQuery(document).on('click', '#delorder', function() {
    if (confirm(bm_normal_object.sure_restore_order)) {
        var archive_id = jQuery(this).val();
        var data = { 
            'action': 'bm_remove_order', 
            'id': archive_id, 
            'nonce': bm_ajax_object.nonce 
        };

		jQuery.post(bm_ajax_object.ajax_url, data, function(result) {
			if (result.success === true) {
				alert(bm_success_object.remove_success);
				location.reload();
			} else {
				alert(result.data || bm_error_object.server_error);
			}
		});
    }
});



// Delete a failed order
jQuery(document).on('click', '#delfailedorder', function() {
    if (confirm(bm_normal_object.are_you_sure)) {
        var id = jQuery(this).val();
        var data = { 
            'action': 'bm_remove_failed_order', 
            'id': id, 
            'nonce': bm_ajax_object.nonce 
        };
        jQuery.post(bm_ajax_object.ajax_url, data, function(result) {
			if (result.success === true) {
				alert(bm_success_object.remove_success);
				location.reload();
			} else {
				alert(result.data || bm_error_object.server_error);
			}
		});
    }
});



// Service Image Remove
function svc_remove_image() {
	jQuery('#svc_image_id').val('');
	jQuery('#svc_image_preview').attr('src', '');
	jQuery('.svc_image_container').hide();
}

// Service Form Tabs
function openSection(evt, sectionName) {

	// Remove Session Value If Exists
	if (sessionStorage.getItem("extravalue") != null) sessionStorage.removeItem("extravalue");
	if (sessionStorage.getItem("galleryvalue") != null) sessionStorage.removeItem("galleryvalue");
	if (sessionStorage.getItem("variableprice") != null) sessionStorage.removeItem("variableprice");
	if (sessionStorage.getItem("variablehour") != null) sessionStorage.removeItem("variablehour");
	if (sessionStorage.getItem("variablesaleswitch") != null) sessionStorage.removeItem("variablesaleswitch");
	if (sessionStorage.getItem("variablecapacity") != null) sessionStorage.removeItem("variablecapacity");
	if (sessionStorage.getItem("variabletimeslot") != null) sessionStorage.removeItem("variabletimeslot");
	if (sessionStorage.getItem("svcsettingstab") != null) sessionStorage.removeItem("svcsettingstab");


	// Remove Success/Error Messgaes If Exists
	jQuery('.calendar_errortext').hide();
	jQuery('.stopsales_errortext').hide();
	jQuery('.saleswitch_errortext').hide();
	jQuery('.capacity_calendar_errortext').hide();
	jQuery('.price_update_successtext').hide();
	jQuery('.stopsales_update_successtext').hide();
	jQuery('.saleswitch_update_successtext').hide();
	jQuery('.capacity_update_successtext').hide();
	jQuery('.calendar_errortext').html(' ');
	jQuery('.stopsales_errortext').html(' ');
	jQuery('.saleswitch_errortext').html(' ');
	jQuery('.capacity_calendar_errortext').html('');
	jQuery('.price_update_successtext').html('');
	jQuery('.stopsales_update_successtext').html('');
	jQuery('.saleswitch_update_successtext').html('');
	jQuery('.capacity_update_successtext').html('');

	// Tab Switch
	var i, tabcontent, tablinks;
	tabcontent = document.getElementsByClassName("tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}
	tablinks = document.getElementsByClassName("tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace("active", "");
	}
	document.getElementById(sectionName).style.display = "block";
	evt.currentTarget.className += " active";
}



// Service Gallery Image Selection
jQuery(document).ready(function ($) {

	var crossSign = "✕";
	var custom_uploader;
	var counter = 0;

	$('.svc-gallery-image').click(function (e) {
		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}

		if (sessionStorage.getItem("galleryvalue") == null) sessionStorage.setItem("galleryvalue", 1);

		//Extend the wp.media object
		custom_uploader = wp.media.frames.file_frame = wp.media({
			title: bm_normal_object.choose_image,
			button: {
				text: bm_normal_object.choose_image
			},
			library: {
				type: 'image'
			},
			multiple: 'add'
		});


		custom_uploader.on('select', function () {
			var image_ids = $('#svc_gallery_image_id').val();
			image_ids = image_ids.length != 0 ? image_ids.split(',') : [];
			attachments = custom_uploader.state().get('selection').toJSON();

			var min_file_size = parseInt(bm_normal_object.image_min_size);
			var max_file_size = parseInt(bm_normal_object.image_max_size);
			var minimum_width = parseInt(bm_normal_object.image_min_width);
			var maximum_width = parseInt(bm_normal_object.image_max_width);
			var minimum_height = parseInt(bm_normal_object.image_min_height);
			var maximum_height = parseInt(bm_normal_object.image_max_height);

			for (var i = 0; i < attachments.length; i++) {
				if ($.inArray(attachments[i].id.toString(), image_ids) == -1) {
					var file_size = parseInt(attachments[i].filesizeInBytes);
					var file_width = parseInt(attachments[i].sizes.full.width);
					var file_height = parseInt(attachments[i].sizes.full.height);

					if (attachments[i]['type'] == 'image') {
						if (min_file_size != 0 && file_size < min_file_size) {
							counter++;
						} else if (max_file_size != 0 && file_size > max_file_size) {
							counter++;
						} else if (minimum_width != 0 && file_width < minimum_width) {
							counter++;
						} else if (maximum_width != 0 && file_width > maximum_width) {
							counter++;
						} else if (minimum_height != 0 && file_height < minimum_height) {
							counter++;
						} else if (maximum_height != 0 && file_height > maximum_height) {
							counter++;
						}
					} else {
						counter++;
					}

					if (counter == 0) {
						image_ids.push(attachments[i].id);
						$('#gallery_images').append("<span class='svc_gallery_image_container' style='position: relative;display: inline-block;' id='svc_gallery_image_container'><image src=" + attachments[i].url + " width='100' height='100' id='svc_gallery_image_preview'>" +
							"<button type='button' class='svc_gallery_image_remove' id=" + attachments[i].id + " title='" + bm_normal_object.remove + "' onclick='svc_gallery_remove(this)'>" + crossSign + "</button></span>");
					}
				}
			}

			if (counter == 0) {
				$('#svc_gallery_image_id').val(image_ids.join(','));
				$('#gallery_images').show();
				$('#is_gallery_image').val('1');
			} else {
				alert(bm_error_object.file_invalid);
			}
		});

		//Open the uploader dialog
		custom_uploader.open();
	});
});



// Service Gallery Image Remove
function svc_gallery_remove($this) {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("galleryvalue") == null) sessionStorage.setItem("galleryvalue", 1);

	// Remove Image
	var id = jQuery($this).attr('id');
	var image_ids = jQuery('#svc_gallery_image_id').val();
	image_ids = image_ids.split(',');
	if (jQuery.inArray(id, image_ids) !== -1) {
		image_ids = jQuery.grep(image_ids, function (value) {
			return value != id;
		});
	}
	jQuery('#svc_gallery_image_id').val(image_ids.join(','));
	jQuery($this).find('image').attr('src', '');
	jQuery($this).parent('span').hide();
}



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



// Add Session Value on Extra Service Edit, Update, Delete
function extraUpdate() {
	if (sessionStorage.getItem("extravalue") == null) sessionStorage.setItem("extravalue", 1);
}



// Form Validation
function add_form_validation(type = '') {

	jQuery('.errortext').html('');
	jQuery('.svc_short_desc_error').html('');
	jQuery('.errortext').hide();
	jQuery('.svc_short_desc_error').hide();

	var divclass = '.bm_required';
	var b = 0;

	if (type == 'extra') {
		divclass = '.bm_ex_required';
		if (sessionStorage.getItem("extravalue") == null) sessionStorage.setItem("extravalue", 1);
	}

	// Form Validation for extras
	if (jQuery("#if_extra_svc").val() == '1') {
		jQuery('.bm_ex_required').each(
			function (index, element) {
				var type = jQuery(this).children().prop('type');
				var value = type == 'select-one' ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());
				if (value == "") {
					jQuery(this).children('.errortext').html(bm_error_object.required_field);
					jQuery(this).children('.errortext').show();
					b++;
				} else if (jQuery(this).children('input').attr('name') == 'svc_extra_price') {
					var regex = /^[1-9]\d*(\.\d+)?$/;
					if (!value.match(regex)) {
						jQuery(this).children('.errortext').html(bm_error_object.numeric_field);
						jQuery(this).children('.errortext').show();
						b++;
					}
				} else if (jQuery(this).children('select').attr('name') == 'svc_extra_duration') {
					if (jQuery("#svc_extra_duration").val() > jQuery("#svc_extra_operation").val()) {
						jQuery(this).children('.errortext').html(bm_error_object.svc_duration_field);
						jQuery(this).children('.errortext').show();
						b++;
					}
				}
			}
		);
	}

	jQuery(divclass).each(
		function (index, element) {
			var type = jQuery(this).children().prop('type');
			var value = type == 'select-one' ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());
			if (value == "") {
				if (jQuery('#time_slots').html() != '') {
					if (jQuery(this).children().attr('id').startsWith('from_') || jQuery(this).children().attr('id').startsWith('to_') || jQuery(this).children().attr('id').startsWith('min_cap_') || jQuery(this).children().attr('id').startsWith('max_cap_')) {
						if (!jQuery(this).children('input').prop('readonly')) {
							jQuery(this).children('.errortext').html(bm_error_object.required);
							b++;
						}
					} else {
						jQuery(this).children('.errortext').html(bm_error_object.required_field);
						b++;
					}
				} else {
					jQuery(this).children('.errortext').html(bm_error_object.required_field);
					b++;
				}
				jQuery(this).children('.errortext').show();
			} else if (jQuery(this).children('input').attr('name') == 'default_price' || jQuery(this).children('input').attr('name') == 'svc_extra_price') {
				var regex = /^[1-9]\d*(\.\d+)?$/;
				if (!value.match(regex)) {
					jQuery(this).children('.errortext').html(bm_error_object.numeric_field);
					jQuery(this).children('.errortext').show();
					b++;
				}
			}

			if (jQuery(this).children('select').attr('name') == 'service_duration') {
				if (jQuery("#service_duration").val() > jQuery("#service_operation").val()) {
					jQuery(this).children('.errortext').html(bm_error_object.svc_duration_field);
					jQuery(this).children('.errortext').show();
					b++;
				}
			}

			if (jQuery(this).children('select').attr('name') == 'svc_extra_duration') {
				if (jQuery("#svc_extra_duration").val() > jQuery("#svc_extra_operation").val()) {
					jQuery(this).children('.errortext').html(bm_error_object.svc_duration_field);
					jQuery(this).children('.errortext').show();
					b++;
				}
			}
		}
	);

	var svc_shrt_desc_chr_limit = bm_normal_object.svc_shrt_dsc_lmt;

	if (typeof tinymce !== 'undefined') {
		var editor = tinymce.get('service_short_desc');
		if (editor) {
			var content = editor.getContent({ format: 'text' })
				.replace(/\s+/g, ' ')
				.replace(/[\u200B-\u200D\uFEFF]/g, '')
				.replace(/\u00A0/g, ' ')
				.replace(/\n/g, '')
				.trim();

			if (svc_shrt_desc_chr_limit > 0 && content.length > svc_shrt_desc_chr_limit) {
				showMessage(bm_error_object.svc_short_desc_limit, 'error');
				b++;
			}
		}
	}

	if (b === 0) {
		return true;
	} else {
		return false;
	}
}



// Field Icon
jQuery(document).ready(function ($) {

	// Field Image Selection
	var custom_uploader;

	$('.field-image').click(function (e) {
		e.preventDefault();
		//If the uploader object has already been created, reopen the dialog
		if (custom_uploader) {
			custom_uploader.open();
			return;
		}
		//Extend the wp.media object
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
					$('#field_icon').val(attachment.id);
					$('#field_image_preview').attr('src', attachment.url);
					if ($('.field_image_container').not(':visible')) $('.field_image_container').show();
				}
			} else {
				alert(bm_error_object.file_type_not_supported);
			}

		});

		//Open the uploader dialog
		custom_uploader.open();
	});
});




// Show/Hide Section
function bm_open_close_tab(a) {
	if (jQuery('#' + a).is(':visible')) {
		jQuery('#' + a).hide();
		if (a == 'wc_products_section') {
			jQuery('td#products_section').removeClass('bminput bm_required')
		} else if (a == 'wc_svcextra_products_section') {
			jQuery('td#svcextra_products_section').removeClass('bminput bm_ex_required')
		}
		if (a == 'calendar_bulk_price_change') {
			if (jQuery('#single_price_change').not(':visible')) jQuery('#single_price_change').show();
		}
		if (a == 'calendar_bulk_hour_change') {
			if (jQuery('#single_hour_change').not(':visible')) jQuery('#single_hour_change').show();
		}
		if (a == 'calendar_bulk_saleswitch_change') {
			if (jQuery('#single_saleswitch_change').not(':visible')) jQuery('#single_saleswitch_change').show();
		}
		if (a == 'max_cap_bulk_change') {
			if (jQuery('#single_max_cap_change').not(':visible')) jQuery('#single_max_cap_change').show();
		}
		if (a == 'variable_external_price_module') {
			jQuery('#has_variable_price_module').val(0);
			jQuery('#up_svc_price_module').hide();
			jQuery('#up_svc_price').show();
			jQuery('#variable_price').show();
		}
		if (a == 'bulk_variable_external_price_module') {
			jQuery('#has_variable_price_module').val(0);
			jQuery('#up_bulk_vc_price_module').hide();
			jQuery('#up_bulk_svc_price').show();
			jQuery('#bulk_variable_price').show();
		}
		if (a == 'svc_price_modal') {
			jQuery('#link_external_price_module').prop('checked', false);
			jQuery('#bulk_link_external_price_module').prop('checked', false);
		}
		if (a == 'stripe_credentials_checkbox') {
			jQuery('#stripe_credentials').hide();
		}
	} else {
		jQuery('#' + a).show();
		if (a == 'wc_products_section') {
			jQuery('td#products_section').addClass('bminput bm_required')
		} else if (a == 'wc_svcextra_products_section') {
			jQuery('td#svcextra_products_section').addClass('bminput bm_ex_required')
		}
		if (a == 'calendar_bulk_price_change') {
			if (jQuery('#single_price_change').is(':visible')) jQuery('#single_price_change').hide();
		}
		if (a == 'calendar_bulk_hour_change') {
			if (jQuery('#single_hour_change').is(':visible')) jQuery('#single_hour_change').hide();
		}
		if (a == 'calendar_bulk_saleswitch_change') {
			if (jQuery('#single_saleswitch_change').is(':visible')) jQuery('#single_saleswitch_change').hide();
		}
		if (a == 'max_cap_bulk_change') {
			if (jQuery('#single_max_cap_change').is(':visible')) jQuery('#single_max_cap_change').hide();
		}
		if (a == 'variable_external_price_module') {
			jQuery('#has_variable_price_module').val(1);
			jQuery('#up_svc_price').hide();
			jQuery('#variable_price').hide();
			jQuery('#up_svc_price_module').show();
		}
		if (a == 'bulk_variable_external_price_module') {
			jQuery('#has_variable_price_module').val(1);
			jQuery('#up_bulk_svc_price').hide();
			jQuery('#bulk_variable_price').hide();
			jQuery('#up_bulk_vc_price_module').show();
		}
		if (a == 'stripe_credentials_checkbox' && jQuery('#bm_show_stripe_credentials').is(':checked')) {
			jQuery('#bm_show_stripe_credentials').prop('checked', false);
			// jQuery('#stripe_credentials').show();
		}
	}

}




// Field Icon Image Remove
function field_remove_image() {
	jQuery('#field_icon').val('');
	jQuery('#field_image_preview').attr('src', '');
	jQuery('.field_image_container').hide();
}




// Change Maximum Capacity on Minimum Capacity Change
function changeMaxCap($this) {
	jQuery('.capacity_message').html('');
	jQuery('.capacity_message').hide();

	var id = jQuery($this).attr('id');
	var index = Number(id.split("_")[2]);

	if (jQuery($this).attr('id') == 'max_cap_' + index + '') {
		var min_element = jQuery('[id^=min_cap_' + index + ']');
		var max_capacity = parseInt(jQuery($this).val());

		if (max_capacity < parseInt(min_element.val())) {
			jQuery($this).val(min_element.val());
			return false;
		}
	} else if (jQuery($this).attr('id') == 'min_cap_' + index + '') {
		var max_element = jQuery('[id^=max_cap_' + index + ']');
		var min_capacity = parseInt(jQuery($this).val());

		if (min_capacity <= parseInt(max_element.val())) {
			max_element.attr('min', min_capacity);
		} else {
			jQuery($this).val(1);
			max_element.attr('min', 1);
			return false;
		}
	}
}




// Change Variable Maximum Capacity on Variable Minimum Capacity Change
function changeVariableMaxCap($this) {
	jQuery('.variable_capacity_message').html('');
	jQuery('.variable_capacity_message').hide();

	var id = jQuery($this).attr('id');
	var index = Number(id.split("_")[3]);

	if (jQuery($this).attr('id') == 'variable_max_cap_' + index + '') {
		var min_element = jQuery('[id^=variable_min_cap_' + index + ']');
		var max_capacity = parseInt(jQuery($this).val());

		if (max_capacity < parseInt(min_element.val())) {
			jQuery($this).val(min_element.val());
			return false;
		}
	} else if (jQuery($this).attr('id') == 'variable_min_cap_' + index + '') {
		var max_element = jQuery('[id^=variable_max_cap_' + index + ']');
		var min_capacity = parseInt(jQuery($this).val());

		if (min_capacity <= parseInt(max_element.val())) {
			max_element.attr('min', min_capacity);
		} else {
			jQuery($this).val(1);
			max_element.attr('min', 1);
			return false;
		}
	}
}




// Dynamic Time Slots
function showSlots($this) {
	if (confirm_slot_change($this)) {
		if (jQuery('#auto_time').val() == '1') jQuery('#auto_time').val('0');
		jQuery('#service_duration').parent().find('.errortext').html(' ');
		jQuery('#service_operation').parent().find('.errortext').html(' ');

		var showDiv = false;
		jQuery("#time_slots").html('');

		var default_max_cap = jQuery('#default_max_cap').val();
		var duration = jQuery('#service_duration').val();
		var operatingTime = jQuery('#service_operation').val();

		if (jQuery($this).attr('name') == 'service_duration') {
			if (duration.length != 0) {
				if (jQuery('#service_operation').is(':disabled')) {
					jQuery('#service_operation').prop('disabled', false);
				}
			} else {
				jQuery('#service_operation').val('');
				jQuery('#service_operation').prop('disabled', true);
				jQuery("#total_time_slots").val(0);
			}
		}

		if (operatingTime.length != 0) {
			if (parseFloat(operatingTime) < parseFloat(duration) || parseFloat(duration) > parseFloat(operatingTime)) {
				jQuery($this).parent().find('.errortext').html(bm_error_object.svc_duration_field);
				jQuery($this).parent().find('.errortext').show();
			} else {
				showDiv = true;
			}
		}

		var total_element = duration.length != 0 && operatingTime.length != 0 ? Math.floor(operatingTime / duration) : 0;
		jQuery("#total_time_slots").val(total_element);

		if (showDiv == true && total_element != 0) {
			var slot_from = '00:00';
			var slot_to = convertNumToTime(duration);
			var str = '';
			for (var i = 1; i <= total_element; i++) {
				str += "<div id='active_slot_" + i + "'><span class='bminput bm_required time_box'>From:&nbsp;<input type='time' name='time_slots[from][" + i + "]' id='from_" + i + "' value='" + slot_from + "' onchange='checkTime(this)'><span class='errortext calculated_time'></span></span>&nbsp;&nbsp;<span class='bminput bm_required time_box'>To:&nbsp;<input type='time' name='time_slots[to][" + i + "]' id='to_" + i + "' value='" + slot_to + "' onchange='checkTime(this)'><span class='errortext calculated_time'></span></span>&nbsp;&nbsp;" +
					"&nbsp;&nbsp;<span>Min Cap:&nbsp;<input name='time_slots[min_cap][" + i + "]' type='number' min='1' id='min_cap_" + i + "' placeholder='" + bm_normal_object.minimum_capacity + "' value='1' style='width: 80px' onchange='changeMaxCap(this)' autocomplete='off'></span>&nbsp;&nbsp;" +
					"&nbsp;&nbsp;<span>Max Cap:&nbsp;<input name='time_slots[max_cap][" + i + "]' type='number' min='1' id='max_cap_" + i + "' placeholder='" + bm_normal_object.maximum_capacity + "' value=" + default_max_cap + " style='width: 80px' onchange='changeMaxCap(this)' autocomplete='off'></span>&nbsp;&nbsp;" +
					"&nbsp;&nbsp;<span><input type='hidden' name='time_slots[hide_to_slot][" + i + "]' id='hide_to_slot_" + i + "' value='0'>" +
					"&nbsp;&nbsp;<span><input type='checkbox' name='time_slots[hide_to_slot][" + i + "]' id='hide_to_slot_" + i + "' value='1'>&nbsp;Hide 'to' slot ?</span>" +
					"&nbsp;&nbsp;<span><input type='hidden' name='time_slots[disable][" + i + "]' id='disable_" + i + "' value='0'>" +
					"&nbsp;&nbsp;<span><input type='checkbox' name='time_slots[disable][" + i + "]' id='disable_" + i + "' value='1' onchange='disableSlot(this)'>&nbsp;" + bm_normal_object.disable + "</span>" +
					"<div id='universal_slot_error_" + i + "' style='display :none;font-family: monospace;color: #fb0000;font-size: 12px;margin-top :8px'></div></div><br>";
				slot_from = slot_to;
				slot_to = addTime(convertNumToTime(duration), slot_from);
			}
			jQuery('#auto_time').val('1');
			jQuery("#time_slots").append("<div id='autoSelectTime' class='bm-checkbox-td'><b>Autoselect Time ?</b>&nbsp;&nbsp;<input type='checkbox' checked name='autoselect_time' id='autoselect_time' class='auto-checkbox bm_toggle' onchange='autoTime()'><label for='autoselect_time'></label></div><br>" + str);
			jQuery('.slot_blocks').show();
			addTimeSlotInfo();
		} else {
			jQuery('.slot_blocks').hide();
		}
	}
}

// Dynamic Time Slots
function showSlotsTiming($this) {
	if (confirm_slot_change($this)) {
		if (jQuery('#auto_time').val() == '1') jQuery('#auto_time').val('0');
		jQuery('#service_duration').parent().find('.errortext').html(' ');
		jQuery('#service_operation').parent().find('.errortext').html(' ');

		var showDiv = false;
		jQuery("#time_slots").html('');

		var default_max_cap = jQuery('#default_max_cap').val();
		var duration = jQuery('#service_duration').val();
		var operatingTime = jQuery('#service_operation').val();

		if (jQuery($this).attr('name') == 'service_duration') {
			if (duration.length != 0) {
				if (jQuery('#service_operation').is(':disabled')) {
					jQuery('#service_operation').prop('disabled', false);
				}
			} else {
				jQuery('#service_operation').val('');
				jQuery('#service_operation').prop('disabled', true);
				jQuery("#total_time_slots").val(0);
			}
		}

		if (operatingTime.length != 0) {
			if (parseFloat(operatingTime) < parseFloat(duration) || parseFloat(duration) > parseFloat(operatingTime)) {
				jQuery($this).parent().find('.errortext').html(bm_error_object.svc_duration_field);
				jQuery($this).parent().find('.errortext').show();
			} else {
				showDiv = true;
			}
		}

		var total_element = duration.length != 0 && operatingTime.length != 0 ? Math.floor(operatingTime / duration) : 0;
		jQuery("#total_time_slots").val(total_element);

		if (showDiv == true && total_element != 0) {
			var slot_from = '00:00';
			var slot_to = convertNumToTime(duration);
			var str = '';
			for (var i = 1; i <= total_element; i++) {
				str += "<div id='active_slot_" + i + "'><span class='bminput bm_required time_box'>From:&nbsp;<input type='time' name='time_slots[from][" + i + "]' id='from_" + i + "' value='" + slot_from + "' onchange='checkTime(this)'><span class='errortext calculated_time'></span></span>&nbsp;&nbsp;<span class='bminput bm_required time_box'>To:&nbsp;<input type='time' name='time_slots[to][" + i + "]' id='to_" + i + "' value='" + slot_to + "' onchange='checkTime(this)'><span class='errortext calculated_time'></span></span>&nbsp;&nbsp;" +
					"&nbsp;&nbsp;<span>Min Cap:&nbsp;<input name='time_slots[min_cap][" + i + "]' type='number' min='1' id='min_cap_" + i + "' placeholder='" + bm_normal_object.minimum_capacity + "' value='1' style='width: 80px' onchange='changeMaxCap(this)' autocomplete='off'></span>&nbsp;&nbsp;" +
					"&nbsp;&nbsp;<span>Max Cap:&nbsp;<input name='time_slots[max_cap][" + i + "]' type='number' min='1' id='max_cap_" + i + "' placeholder='" + bm_normal_object.maximum_capacity + "' value=" + default_max_cap + " style='width: 80px' onchange='changeMaxCap(this)' autocomplete='off'></span>&nbsp;&nbsp;" +
					"&nbsp;&nbsp;<span><input type='hidden' name='time_slots[hide_to_slot][" + i + "]' id='hide_to_slot_" + i + "' value='0'>" +
					"&nbsp;&nbsp;<span><input type='checkbox' name='time_slots[hide_to_slot][" + i + "]' id='hide_to_slot_" + i + "' value='1'>&nbsp;Hide 'to' slot ?</span>" +
					"&nbsp;&nbsp;<span><input type='hidden' name='time_slots[disable][" + i + "]' id='disable_" + i + "' value='0'>" +
					"&nbsp;&nbsp;<span><input type='checkbox' name='time_slots[disable][" + i + "]' id='disable_" + i + "' value='1' onchange='disableSlot(this)'>&nbsp;" + bm_normal_object.disable + "</span>" +
					"<div id='universal_slot_error_" + i + "' style='display :none;font-family: monospace;color: #fb0000;font-size: 12px;margin-top :8px'></div></div><br>";
				slot_from = slot_to;
				slot_to = addTime(convertNumToTime(duration), slot_from);
			}
			jQuery('#auto_time').val('1');
			jQuery("#time_slots").append("<div id='autoSelectTime' class='bm-checkbox-td'><b>Autoselect Time ?</b>&nbsp;&nbsp;<input type='checkbox' checked name='autoselect_time' id='autoselect_time' class='auto-checkbox bm_toggle' onchange='autoTime()'><label for='autoselect_time'></label></div><br>" + str);
			jQuery('.slot_blocks').show();
			addTimeSlotInfo();
		} else {
			jQuery('.slot_blocks').hide();
		}
	}
}




// Convert Number to Time
function convertNumToTime(number) {
	// Check sign of given number
	var sign = (number >= 0) ? 1 : -1;

	// Set positive value of number of sign negative
	number = number * sign;

	// Separate the int from the decimal part
	var hour = Math.floor(number);
	var decpart = number - hour;

	var min = 1 / 60;
	// Round to nearest minute
	decpart = min * Math.round(decpart / min);

	var minute = Math.floor(decpart * 60) + '';

	// Add padding if need
	if (hour.toString().length < 2) {
		hour = '0' + hour;
	}

	// Add padding if need
	if (minute.length < 2) {
		minute = '0' + minute;
	}

	// Add Sign in final result
	sign = sign == 1 ? '' : '-';

	// Concate hours and minutes
	time = sign + hour + ':' + minute;

	return time;
}




// Add/Subtract functions for Single or Multiple Times
function addTime() {
	if (arguments.length < 2) {
		if (arguments.length == 1 && isFormattedDate(arguments[0])) return arguments[0];
		else return false;
	}

	var time1Split, time2Split, totalHours, totalMinutes;
	if (isFormattedDate(arguments[0])) var totalTime = arguments[0];
	else return false;

	for (var i = 1; i < arguments.length; i++) {
		// Add them up
		time1Split = totalTime.split(':');
		time2Split = arguments[i].split(':');

		totalHours = parseInt(time1Split[0]) + parseInt(time2Split[0]);
		totalMinutes = parseInt(time1Split[1]) + parseInt(time2Split[1]);

		// If total minutes is more than 59, then convert to hours and minutes
		if (totalMinutes > 59) {
			totalHours += Math.floor(totalMinutes / 60);
			totalMinutes = totalMinutes % 60;
		}

		totalTime = padWithZeros(totalHours) + ':' + padWithZeros(totalMinutes);
	}

	return totalTime;
}

function isFormattedDate(date) {
	var splitDate = date.split(':');
	if (splitDate.length == 2 && (parseInt(splitDate[0]) + '').length <= 2 && (parseInt(splitDate[1]) + '').length <= 2) return true;
	else return false;
}

function padWithZeros(number) {
	var lengthOfNumber = (parseInt(number) + '').length;
	if (lengthOfNumber == 2) return number;
	else if (lengthOfNumber == 1) return '0' + number;
	else if (lengthOfNumber == 0) return '00';
	else return false;
}

function strToMins(t) {
	var s = t.split(":");
	return Number(s[0]) * 60 + Number(s[1]);
}

function minsToStr(t) {
	return padWithZeros(Math.trunc(t / 60)) + ':' + padWithZeros(('00' + t % 60)).slice(-2);
}

function timeStringToFloat(time) {
	var hoursMinutes = time.split(/[.:]/);
	var hours = parseInt(hoursMinutes[0], 10);
	var minutes = hoursMinutes[1] ? parseInt(hoursMinutes[1], 10) : 0;
	return hours + minutes / 60;
}




// Change Time as per Selection
function checkTime($this) {
	var duration = jQuery('#service_duration').val();
	var total_element = jQuery('#total_time_slots').val();
	if ($this.length == 0) $this = document.getElementById('from_1');
	var currentindex = Number($this.id.split("_")[1]);
	var slot_value = $this.value;
	if (slot_value == '') slot_value = '00:00';

	jQuery('.calculated_time').html('');
	jQuery('.calculated_time').hide();
	jQuery('.capacity_message').html('');
	jQuery('.capacity_message').hide();


	jQuery('[id^=universal_slot_error_]').each(function (id, item) {
		jQuery(item).text(' ');
		jQuery(item).hide();
	});

	if (jQuery('#auto_time').val() == '1') {
		if (currentindex == 1 && $this.id == 'from_' + currentindex + '') {
			var slot_from = slot_value;
			var slot_to = addTime(convertNumToTime(duration), slot_from);
		} else if (currentindex == 1 && $this.id == 'to_' + currentindex + '') {
			var slot_to = slot_value;
			var slot_from = minsToStr(strToMins(slot_to) - strToMins(convertNumToTime(duration)));
		} else if (currentindex != 1 && $this.id == 'from_' + currentindex + '') {
			var slot_from = minsToStr(strToMins(slot_value) - strToMins(convertNumToTime(duration * (currentindex - 1))));
			var slot_to = addTime(convertNumToTime(duration), slot_from);
		} else if (currentindex != 1 && $this.id == 'to_' + currentindex + '') {
			var slot_to = minsToStr(strToMins(slot_value) - strToMins(convertNumToTime(duration * (currentindex - 1))));
			var slot_from = minsToStr(strToMins(slot_to) - strToMins(convertNumToTime(duration)));
		}

		for (var i = 1; i <= total_element; i++) {
			jQuery('#from_' + i + '').val(slot_from);
			jQuery('#to_' + i + '').val(slot_to);
			slot_from = slot_to;
			slot_to = addTime(convertNumToTime(duration), slot_from);
		}
	} else {
		if (currentindex == 1) {
			var slot_to = addTime(convertNumToTime(duration), jQuery('#from_1').val());
			if ($this.id == 'from_1') {
				if (slot_to < '24:00') {
					jQuery('#to_1').val('');
					jQuery('#to_1').next().html("should be " + slot_to + "");
					jQuery('#to_1').next().show();
				} else {
					jQuery('#to_1').val('');
					jQuery('#to_1').next().html(bm_error_object.max_time);
					jQuery('#to_1').next().show();
				}
			} else if ($this.id == 'to_1') {
				if (slot_value == slot_to) {
					jQuery('#from_2').val('');
					jQuery('#universal_slot_error_2').html("should be " + jQuery('#to_1').val() + " or greater than " + jQuery('#to_1').val() + "");
					jQuery('#universal_slot_error_2').show();
				} else {
					jQuery('#to_1').val('');
					jQuery('#to_1').next().html("should be " + slot_to + "");
					jQuery('#to_1').next().show();
					return false;
				}
			}
		} else {
			var previous_slot_to = jQuery('#to_' + (currentindex - 1) + '').val();
			var next_slot_to = addTime(convertNumToTime(duration), jQuery('#from_' + currentindex + '').val());

			if ($this.id == 'from_' + currentindex + '') {
				if (slot_value >= previous_slot_to) {
					if (next_slot_to < '24:00') {
						jQuery('#to_' + currentindex + '').val('');
						jQuery($this).parent().parent().find('input#to_' + currentindex + '').next().html("should be " + next_slot_to + "");
						jQuery($this).parent().parent().find('input#to_' + currentindex + '').next().show();
						return false;
					} else {
						jQuery('#to_' + currentindex + '').val('');
						jQuery($this).parent().parent().find('input#to_' + currentindex + '').next().html(bm_error_object.max_time);
						jQuery($this).parent().parent().find('input#to_' + currentindex + '').next().show();
						return false;
					}
				} else {
					jQuery($this).val('');
					jQuery('#universal_slot_error_' + currentindex + '').html("should be " + previous_slot_to + " or greater than " + previous_slot_to + "");
					jQuery('#universal_slot_error_' + currentindex + '').show();
					return false;
				}
			} else if ($this.id == 'to_' + currentindex + '') {
				if (slot_value == next_slot_to) {
					if (jQuery('#from_' + (currentindex + 1) + '').length != 0) {
						jQuery('#from_' + (currentindex + 1) + '').val('');
						jQuery('#universal_slot_error_' + (currentindex + 1) + '').html("should be " + jQuery('#to_' + currentindex + '').val() + " or greater than " + jQuery('#to_' + currentindex + '').val() + "");
						jQuery('#universal_slot_error_' + (currentindex + 1) + '').show();
						return false;
					}
				} else {
					jQuery($this).val('');
					jQuery($this).next().html("should be " + next_slot_to + "");
					jQuery($this).next().show();
					return false;
				}
			}
		}
	}
}




// Change Time as per Selection
function checkVariableTime($this, slot_data_id = '') {
	var slot_id = $this.length == 0 ? slot_data_id : Number(jQuery($this).data('slot'));
	var duration = jQuery('#service_duration').val();
	var total_element = jQuery('#total_time_slots').val();
	if ($this.length == 0) $this = document.getElementById('variable_from_1');
	var currentindex = Number($this.id.split("_")[2]);
	var slot_value = $this.value;
	if (slot_value == '') slot_value = '00:00';

	jQuery('.variable_calculated_time').html('');
	jQuery('.variable_calculated_time').hide();
	jQuery('.variable_capacity_message').html('');
	jQuery('.variable_capacity_message').hide();


	jQuery('[id^=variable_universal_slot_error_]').each(function (id, item) {
		jQuery(item).text(' ');
		jQuery(item).hide();
	});

	if (jQuery('#variable_auto_time_' + slot_id + '').val() == '1') {
		if (currentindex == 1 && $this.id == 'variable_from_' + currentindex + '') {
			var slot_from = slot_value;
			var slot_to = addTime(convertNumToTime(duration), slot_from);
		} else if (currentindex == 1 && $this.id == 'variable_to_' + currentindex + '') {
			var slot_to = slot_value;
			var slot_from = minsToStr(strToMins(slot_to) - strToMins(convertNumToTime(duration)));
		} else if (currentindex != 1 && $this.id == 'variable_from_' + currentindex + '') {
			var slot_from = minsToStr(strToMins(slot_value) - strToMins(convertNumToTime(duration * (currentindex - 1))));
			var slot_to = addTime(convertNumToTime(duration), slot_from);
		} else if (currentindex != 1 && $this.id == 'variable_to_' + currentindex + '') {
			var slot_to = minsToStr(strToMins(slot_value) - strToMins(convertNumToTime(duration * (currentindex - 1))));
			var slot_from = minsToStr(strToMins(slot_to) - strToMins(convertNumToTime(duration)));
		}

		for (var i = 1; i <= total_element; i++) {
			jQuery('#variable_from_' + i + '').attr('value', slot_from);
			jQuery('#variable_from_' + i + '').val(slot_from);
			jQuery('#variable_to_' + i + '').attr('value', slot_to);
			jQuery('#variable_to_' + i + '').val(slot_to);
			slot_from = slot_to;
			slot_to = addTime(convertNumToTime(duration), slot_from);
		}
	} else {
		if (currentindex == 1) {
			var slot_to = addTime(convertNumToTime(duration), jQuery('#variable_from_1').val());
			if ($this.id == 'variable_from_1') {
				if (slot_to < '24:00') {
					jQuery('#variable_to_1').val('');
					jQuery('#variable_to_1').next().html("should be " + slot_to + "");
					jQuery('#variable_to_1').next().show();
				} else {
					jQuery('#variable_to_1').val('');
					jQuery('#variable_to_1').next().html(bm_error_object.max_time);
					jQuery('#variable_to_1').next().show();
				}
			} else if ($this.id == 'variable_to_1') {
				if (slot_value == slot_to) {
					jQuery('#variable_from_2').val('');
					jQuery('#variable_universal_slot_error_2').html("should be " + jQuery('#variable_to_1').val() + " or greater than " + jQuery('#variable_to_1').val() + "");
					jQuery('#variable_universal_slot_error_2').show();
				} else {
					jQuery('#variable_to_1').val('');
					jQuery('#variable_to_1').next().html("should be " + slot_to + "");
					jQuery('#variable_to_1').next().show();
					return false;
				}
			}
		} else {
			var previous_slot_to = jQuery('#variable_to_' + (currentindex - 1) + '').val();
			var next_slot_to = addTime(convertNumToTime(duration), jQuery('#variable_from_' + currentindex + '').val());

			if ($this.id == 'variable_from_' + currentindex + '') {
				if (slot_value >= previous_slot_to) {
					if (next_slot_to < '24:00') {
						jQuery('#variable_to_' + currentindex + '').val('');
						jQuery($this).parent().parent().find('input#variable_to_' + currentindex + '').next().html("should be " + next_slot_to + "");
						jQuery($this).parent().parent().find('input#variable_to_' + currentindex + '').next().show();
						return false;
					} else {
						jQuery('#variable_to_' + currentindex + '').val('');
						jQuery($this).parent().parent().find('input#variable_to_' + currentindex + '').next().html(bm_error_object.max_time);
						jQuery($this).parent().parent().find('input#variable_to_' + currentindex + '').next().show();
						return false;
					}
				} else {
					jQuery($this).val('');
					jQuery('#variable_universal_slot_error_' + currentindex + '').html("should be " + previous_slot_to + " or greater than " + previous_slot_to + "");
					jQuery('#variable_universal_slot_error_' + currentindex + '').show();
					return false;
				}
			} else if ($this.id == 'variable_to_' + currentindex + '') {
				if (slot_value == next_slot_to) {
					if (jQuery('#variable_from_' + (currentindex + 1) + '').length != 0) {
						jQuery('#variable_from_' + (currentindex + 1) + '').val('');
						jQuery('#variable_universal_slot_error_' + (currentindex + 1) + '').html("should be " + jQuery('#variable_to_' + currentindex + '').val() + " or greater than " + jQuery('#variable_to_' + currentindex + '').val() + "");
						jQuery('#variable_universal_slot_error_' + (currentindex + 1) + '').show();
						return false;
					}
				} else {
					jQuery($this).val('');
					jQuery($this).next().html("should be " + next_slot_to + "");
					jQuery($this).next().show();
					return false;
				}
			}
		}
	}
}




// Disable Time slot as per Selection
function disableSlot($this) {

	var currentindex = Number($this.id.split("_")[1]);
	var slot_from = jQuery('#from_' + currentindex + '');
	var slot_to = jQuery('#to_' + currentindex + '');
	var hide_to_slot = jQuery('input:checkbox[id="hide_to_slot_' + currentindex + '"]');
	var min_capacity = jQuery('#min_cap_' + currentindex + '');
	var max_capacity = jQuery('#max_cap_' + currentindex + '');

	if (slot_from.prop('readonly')) {
		slot_from.prop('readonly', false);
		if (!slot_from.parent().hasClass('bminput bm_required')) slot_from.parent().addClass('bminput bm_required');
	} else {
		slot_from.prop('readonly', true);
		if (slot_from.parent().hasClass('bminput bm_required')) slot_from.parent().removeClass('bminput bm_required');
	}

	if (slot_to.prop('readonly')) {
		slot_to.prop('readonly', false);
		if (!slot_to.parent().hasClass('bminput bm_required')) slot_to.parent().addClass('bminput bm_required');
	} else {
		slot_to.prop('readonly', true);
		if (slot_to.parent().hasClass('bminput bm_required')) slot_to.parent().removeClass('bminput bm_required');
	}

	if (min_capacity.prop('readonly')) {
		min_capacity.prop('readonly', false);
	} else {
		min_capacity.prop('readonly', true);
	}

	if (max_capacity.prop('readonly')) {
		max_capacity.prop('readonly', false);
	} else {
		max_capacity.prop('readonly', true);
	}

	if (hide_to_slot.hasClass('readonly_checkbox')) {
		hide_to_slot.removeClass('readonly_checkbox');
	} else {
		hide_to_slot.addClass('readonly_checkbox');
	}
}




// Disable external price module age group
function disableAgeGroup($this) {

	var index = $this.id.split("_")[3];
	var slot_from = jQuery('#age_from_' + index);
	var slot_to = jQuery('#age_to_' + index);

	if (slot_from.prop('readonly')) {
		slot_from.prop('readonly', false);
	} else {
		slot_from.prop('readonly', true);
	}

	if (slot_to.prop('readonly')) {
		slot_to.prop('readonly', false);
	} else {
		slot_to.prop('readonly', true);
	}
}




// Disable Variable Time slot as per Selection
function disableVariableSlot($this) {

	var slot_id = Number(jQuery($this).data('slot'));

	if (jQuery($this).prop('checked') == true) {
		jQuery($this).attr('value', 1);
		jQuery($this).attr('checked', 'checked');
	} else {
		jQuery($this).attr('value', 0);
		jQuery($this).removeAttr('checked');
	}

	var currentindex = Number($this.id.split("_")[2]);
	var slot_from = jQuery('input[name="variable_time_slots[' + slot_id + '][from][' + currentindex + ']"]');
	var slot_to = jQuery('input[name="variable_time_slots[' + slot_id + '][to][' + currentindex + ']"]');
	var hide_to_slot = jQuery('input:checkbox[name="variable_time_slots[' + slot_id + '][hide_to_slot][' + currentindex + ']"]');
	var min_capacity = jQuery('input[name="variable_time_slots[' + slot_id + '][min_cap][' + currentindex + ']"]');
	var max_capacity = jQuery('input[name="variable_time_slots[' + slot_id + '][max_cap][' + currentindex + ']"]');

	if (slot_from.prop('readonly')) {
		slot_from.prop('readonly', false);
		if (!slot_from.parent().hasClass('bminput bm_required_variable')) slot_from.parent().addClass('bminput bm_required_variable');
	} else {
		slot_from.prop('readonly', true);
		if (slot_from.parent().hasClass('bminput bm_required_variable')) slot_from.parent().removeClass('bminput bm_required_variable');
	}

	if (slot_to.prop('readonly')) {
		slot_to.prop('readonly', false);
		if (!slot_to.parent().hasClass('bminput bm_required_variable')) slot_to.parent().addClass('bminput bm_required_variable');
	} else {
		slot_to.prop('readonly', true);
		if (slot_to.parent().hasClass('bminput bm_required_variable')) slot_to.parent().removeClass('bminput bm_required_variable');
	}

	if (min_capacity.prop('readonly')) {
		min_capacity.prop('readonly', false);
	} else {
		min_capacity.prop('readonly', true);
	}

	if (max_capacity.prop('readonly')) {
		max_capacity.prop('readonly', false);
	} else {
		max_capacity.prop('readonly', true);
	}

	if (hide_to_slot.hasClass('readonly_checkbox')) {
		hide_to_slot.removeClass('readonly_checkbox');
	} else {
		hide_to_slot.addClass('readonly_checkbox');
	}
}




// Check/Uncheck Hide To Time Slots
function hideToSlot($this) {
	if (jQuery($this).prop('checked') == true) {
		jQuery($this).attr('value', 1);
		jQuery($this).attr('checked', 'checked')
	} else {
		jQuery($this).attr('value', 0);
		jQuery($this).removeAttr('checked');
	}
}




// On/Off AutoTime Selection For Service Time Slots
function autoTime() {
	if (jQuery('#auto_time').val() == '0') {
		jQuery('#auto_time').val('1');
		checkTime('');
	} else {
		jQuery('#auto_time').val('0');
	}
}




// On/Off AutoTime Selection For Variable Service Time Slots
function variableAutoTime($this) {
	var slot_data_id = Number(jQuery($this).attr('id').split('_')[3]);

	if (jQuery(document).find('#variable_auto_time_' + slot_data_id + '').val() == '0') {
		jQuery(document).find('#variable_auto_time_' + slot_data_id + '').val('1');
		jQuery($this).attr('value', 1);
		jQuery($this).attr('checked', 'checked');
		checkVariableTime('', slot_data_id);
	} else {
		jQuery(document).find('#variable_auto_time_' + slot_data_id + '').val('0');
		jQuery($this).attr('value', 0);
		jQuery($this).removeAttr('checked');
	}
}




// Load Price Calendar
jQuery(document).ready(function ($) {
	if (getUrlParameter('id') != '') $('#old_default_price').val($('#default_price').val());

	$("#price_datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		//		numberOfMonths: 3,
		minDate: 0,
		beforeShow: getUrlParameter('id') != '' ? bm_get_service_price() : addPriceInfo(),
		//---^----------- if closed by default (when you're using <input>)
		beforeShowDay: function (date) {
			var returnday = "weekday";
			return [true, returnday];
		},
		onChangeMonthYear: function () { getUrlParameter('id') != '' ? bm_get_service_price() : addPriceInfo('drawYear') },
		onSelect: function (date, inst) {
			if (jQuery('#default_price').val() != '') {
				jQuery('#variable_date').val(date);

				var currency_symbol = bm_normal_object.currency_symbol;

				var text = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
					return jQuery(this).find('a').text() == inst.selectedDay;
				}).find("a").attr('data-custom');

				if (text.indexOf("#module") > -1) {
					jQuery('select[name^="variable_external_price_module"] option:selected').attr("selected", null);
					if (text.split('_')[1] != '' && text.split('_')[1] != 'undefined') {
						jQuery('select[name^="variable_external_price_module"] option[value="' + text.split('_')[1] + '"]').attr("selected", "selected");
					}
					jQuery('#variable_price').hide();
					jQuery('#variable_external_price_module').show();
					jQuery('#link_external_price_module').prop('checked', true);
					jQuery('#up_svc_price').hide();
					jQuery('#up_svc_price_module').show();
				} else {
					if (text.split(currency_symbol)[1] != '' && text.split(currency_symbol)[1] != 'undefined') {
						jQuery('#variable_price').val(text.split(currency_symbol)[1]);
					}
					jQuery('#variable_price').show();
					jQuery('#variable_external_price_module').hide();
					jQuery('#link_external_price_module').prop('checked', false);
					jQuery('#up_svc_price').show();
					jQuery('#up_svc_price_module').hide();
				}

				jQuery('.calendar_errortext').html('');
				jQuery('.price_update_successtext').html('');
				jQuery('.price_update_errortext').html('');
				jQuery('.variable_errortext').html('');
				jQuery('.bulk_errortext').html('');
				jQuery('#from_bulk_price_change').val('');
				jQuery('#to_bulk_price_change').val('');
				jQuery('#bulk_variable_price').val('');

				jQuery('#to_bulk_price_change').prop('readonly', true);
				jQuery('#bulk_price_change').prop('checked', false);

				jQuery('#cancel_svc_price').show();
				jQuery('#svc_price_modal').show();
				jQuery('#single_price_change').show();

				jQuery('.calendar_errortext').hide();
				jQuery('.price_update_successtext').hide();
				jQuery('.price_update_errortext').hide();
				jQuery('.variable_errortext').hide();
				jQuery('.bulk_errortext').hide();
				jQuery('#calendar_bulk_price_change').hide();

				//Prevent the redraw.
				inst.inline = false;
			} else {
				jQuery('.calendar_errortext').html(bm_error_object.set_price);
				jQuery('.calendar_errortext').show();

				//Prevent the redraw.
				inst.inline = false;
			}
		},
	});
	// getUrlParameter('id') != '' ? bm_get_service_price() : addPriceInfo(); // if open by default (when you're using <div>)
});





// Load Stopsales Calendar
jQuery(document).ready(function ($) {
	if (getUrlParameter('id') != '') $('#old_default_stopsales').val($('#default_stopsales').val());

	$("#stopsales_datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		// numberOfMonths: 3,
		minDate: 0,
		beforeShow: getUrlParameter('id') != '' ? bm_get_service_stopsales() : addStopsalesInfo(),
		//---^----------- if closed by default (when you're using <input>)
		beforeShowDay: function (date) {
			var returnday = "weekday";
			return [true, returnday];
		},
		onChangeMonthYear: function () { getUrlParameter('id') != '' ? bm_get_service_stopsales() : addStopsalesInfo('drawYear') },
		onSelect: function (date, inst) {
			jQuery('#variable_stopsales_date').val(date);

			var stopsales = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
				return jQuery(this).find('a').text() == inst.selectedDay;
			}).find("a").attr('data-custom');
			jQuery('select[name^="variable_hour"] option:selected').attr("selected", null);
			if (stopsales.split('h')[0] != '' || stopsales.split('h')[0] != 0) jQuery('select[name^="variable_hour"] option[value="' + stopsales.split('h')[0] + '"]').attr("selected", "selected");

			jQuery('.stopsales_errortext').html(' ');
			jQuery('.stopsales_update_successtext').html(' ');
			jQuery('.stopsales_update_errortext').html(' ');
			jQuery('.variable_hour_errortext').html('');
			jQuery('.bulk_stopsales_errortext').html('');
			jQuery('#from_bulk_stopsales_change').val('');
			jQuery('#to_bulk_stopsales_change').val('');
			jQuery('#bulk_variable_hour').val('');

			jQuery('#to_bulk_stopsales_change').prop('readonly', true);
			jQuery('#bulk_hour_change').prop('checked', false);

			if (jQuery('#up_svc_hour').not(':visible')) jQuery('#up_svc_hour').show();
			if (jQuery('#cancel_svc_stopsales').not(':visible')) jQuery('#cancel_svc_stopsales').show();
			if (jQuery('#stopsales_modal').not(':visible')) jQuery('#stopsales_modal').show();
			jQuery('#single_hour_change').show();

			if (jQuery('.stopsales_errortext').is(':visible')) jQuery('.stopsales_errortext').hide();
			if (jQuery('.stopsales_update_successtext').is(':visible')) jQuery('.stopsales_update_successtext').hide();
			if (jQuery('.stopsales_update_errortext').is(':visible')) jQuery('.stopsales_update_errortext').hide();
			if (jQuery('.variable_hour_errortext').is(':visible')) jQuery('.variable_hour_errortext').hide();
			if (jQuery('.bulk_stopsales_errortext').is(':visible')) jQuery('.bulk_stopsales_errortext').hide();
			jQuery('#calendar_bulk_hour_change').hide();

			//Prevent the redraw.
			inst.inline = false;
		},
	});
	// getUrlParameter('id') != '' ? bm_get_service_stopsales() : addStopsalesInfo(); // if open by default (when you're using <div>)
});




// Load Saleswitch Calendar
jQuery(document).ready(function ($) {
	if (getUrlParameter('id') != '') $('#old_default_saleswitch').val($('#default_saleswitch').val());

	$("#saleswitch_datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		// numberOfMonths: 3,
		minDate: 0,
		beforeShow: getUrlParameter('id') != '' ? bm_get_service_saleswitch() : addSaleswitchInfo(),
		//---^----------- if closed by default (when you're using <input>)
		beforeShowDay: function (date) {
			var returnday = "weekday";
			return [true, returnday];
		},
		onChangeMonthYear: function () { getUrlParameter('id') != '' ? bm_get_service_saleswitch() : addSaleswitchInfo('drawYear') },
		onSelect: function (date, inst) {
			jQuery('#variable_saleswitch_date').val(date);

			var saleswitch = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
				return jQuery(this).find('a').text() == inst.selectedDay;
			}).find("a").attr('data-custom');
			jQuery('select[name^="variable_saleswitch_hour"] option:selected').attr("selected", null);
			if (saleswitch.split('h')[0] != '' || saleswitch.split('h')[0] != 0) jQuery('select[name^="variable_saleswitch_hour"] option[value="' + saleswitch.split('h')[0] + '"]').attr("selected", "selected");

			jQuery('.saleswitch_errortext').html(' ');
			jQuery('.saleswitch_update_successtext').html(' ');
			jQuery('.saleswitch_update_errortext').html(' ');
			jQuery('.variable_saleswitch_errortext').html('');
			jQuery('.bulk_saleswitch_errortext').html('');
			jQuery('#from_bulk_saleswitch_change').val('');
			jQuery('#to_bulk_saleswitch_change').val('');
			jQuery('#bulk_saleswitch_hour').val('');

			jQuery('#to_bulk_saleswitch_change').prop('readonly', true);
			jQuery('#bulk_saleswitch_change').prop('checked', false);

			if (jQuery('#up_svc_saleswitch').not(':visible')) jQuery('#up_svc_saleswitch').show();
			if (jQuery('#cancel_svc_saleswitch').not(':visible')) jQuery('#cancel_svc_saleswitch').show();
			if (jQuery('#saleswitch_modal').not(':visible')) jQuery('#saleswitch_modal').show();
			jQuery('#single_saleswitch_change').show();

			if (jQuery('.saleswitch_errortext').is(':visible')) jQuery('.saleswitch_errortext').hide();
			if (jQuery('.saleswitch_update_successtext').is(':visible')) jQuery('.saleswitch_update_successtext').hide();
			if (jQuery('.saleswitch_update_errortext').is(':visible')) jQuery('.saleswitch_update_errortext').hide();
			if (jQuery('.variable_saleswitch_errortext').is(':visible')) jQuery('.variable_saleswitch_errortext').hide();
			if (jQuery('.bulk_saleswitch_errortext').is(':visible')) jQuery('.bulk_saleswitch_errortext').hide();
			jQuery('#calendar_bulk_saleswitch_change').hide();

			//Prevent the redraw.
			inst.inline = false;
		},
	});
	// getUrlParameter('id') != '' ? bm_get_service_saleswitch() : addSaleswitchInfo(); // if open by default (when you're using <div>)
});




// Load Capacity Calendar
jQuery(document).ready(function ($) {
	if (getUrlParameter('id') != '') $('#old_default_max_cap').val($('#default_max_cap').val());

	$("#cap_datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		// numberOfMonths: 3,
		minDate: 0,
		beforeShow: getUrlParameter('id') != '' ? bm_get_service_max_cap() : addCapacityInfo(),
		//---^----------- if closed by default (when you're using <input>)
		beforeShowDay: function (date) {
			var returnday = "weekday";
			return [true, returnday];
		},
		onChangeMonthYear: function () { getUrlParameter('id') != '' ? bm_get_service_max_cap() : addCapacityInfo('drawYear') },
		onSelect: function (date, inst) {
			if (jQuery('#default_max_cap').val() != '') {
				jQuery('#max_cap_date').val(date);

				var capacity = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
					return jQuery(this).find('a').text() == inst.selectedDay;
				}).find("a").attr('data-custom');
				jQuery('#max_cap_value').val(capacity);


				jQuery('.capacity_calendar_errortext').html(' ');
				jQuery('.capacity_update_successtext').html(' ');
				jQuery('.capacity_update_errortext').html(' ');
				jQuery('.max_cap_errortext').html('');
				jQuery('.bulk_cap_errortext').html('');
				jQuery('#from_bulk_cap_change').val('');
				jQuery('#to_bulk_cap_change').val('');
				jQuery('#bulk_max_cap').val('');

				jQuery('#to_bulk_cap_change').prop('readonly', true);
				jQuery('#bulk_max_cap_change').prop('checked', false);

				jQuery('#up_max_cap').show();
				jQuery('#cancel_max_cap').show();
				jQuery('#max_cap_modal').show();
				jQuery('#single_max_cap_change').show();

				jQuery('.capacity_calendar_errortext').hide();
				jQuery('.capacity_update_successtext').hide();
				jQuery('.capacity_update_errortext').hide();
				jQuery('.max_cap_errortext').hide();
				jQuery('.bulk_cap_errortext').hide();
				jQuery('#max_cap_bulk_change').hide();

				//Prevent the redraw.
				inst.inline = false;
			} else {
				jQuery('.capacity_calendar_errortext').html(bm_error_object.set_max_cap);
				if (jQuery('.capacity_calendar_errortext').not(':visible')) jQuery('.capacity_calendar_errortext').show();

				//Prevent the redraw.
				inst.inline = false;
			}
		},
	});
	// getUrlParameter('id') != '' ? bm_get_service_max_cap() : addCapacityInfo(); // if open by default (when you're using <div>)
});




// Load Time Slots Calendar
jQuery(document).ready(function ($) {
	if (getUrlParameter('id') != '') $('#old_total_time_slots').val($('#total_time_slots').val());

	$("#time_slots_datepicker").datepicker({
		dateFormat: 'yy-mm-dd',
		showButtonPanel: true,
		changeMonth: true,
		changeYear: true,
		// numberOfMonths: 3,
		minDate: 0,
		beforeShow: getUrlParameter('id') != '' ? bm_get_service_time_slots() : addTimeSlotInfo(),
		//---^----------- if closed by default (when you're using <input>)
		beforeShowDay: function (date) {
			var returnday = "weekday";
			return [true, returnday];
		},
		onChangeMonthYear: function () { getUrlParameter('id') != '' ? bm_get_service_time_slots() : addTimeSlotInfo('drawYear') },
		onSelect: function (date, inst) {
			if (jQuery('#time_slots').html() != '') {
				jQuery('#time_slot_date').val(date);
				jQuery("#time_slot_value").html('');
				jQuery('#remove_slot_button').html('');

				var slot_value = jQuery(inst.dpDiv).find('[data-year="' + inst.selectedYear + '"][data-month="' + inst.selectedMonth + '"]').filter(function () {
					return jQuery(this).find('a').text() == inst.selectedDay;
				}).find("a").attr('data-custom');

				var old_total_element = jQuery('#old_total_time_slots').val();
				var total_element = jQuery('#total_time_slots').val();
				var old_default_max_cap = jQuery('#old_default_max_cap').val();
				var default_max_cap = jQuery('#default_max_cap').val();
				var duration = jQuery('#service_duration').val();
				var time_slot_inputs = jQuery('.slot_data_element').length;

				if (slot_value == 'Default' || slot_value == 'N/A') {
					if (time_slot_inputs != 0) {
						var time_slot_lastid = jQuery('.slot_data_element:last').attr("id");
						var time_slot_nextindex = Number(time_slot_lastid.split("_")[4]) + 1;
					} else {
						if (getUrlParameter('id') != '') {
							if (old_total_element != total_element) {
								var time_slot_nextindex = 1;
							} else {
								var time_slot_nextindex = Number(jQuery('#total_variable_slots').val()) + 1;
							}
						} else {
							var time_slot_nextindex = 1;
						}
					}

					if (total_element != 0) {
						var slot_from = '00:00';
						var slot_to = convertNumToTime(duration);
						var time_slot_html = '';
						for (var i = 1; i <= total_element; i++) {
							time_slot_html += "<div id='variable_active_slot_" + i + "'><span class='bminput bm_required_variable time_box'>From:&nbsp;<input type='time' name='variable_time_slots[" + time_slot_nextindex + "][from][" + i + "]' id='variable_from_" + i + "' value='" + slot_from + "' data-slot='" + time_slot_nextindex + "' onchange='checkVariableTime(this)'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;<span class='bminput bm_required_variable time_box'>To:&nbsp;<input type='time' name='variable_time_slots[" + time_slot_nextindex + "][to][" + i + "]' id='variable_to_" + i + "' value='" + slot_to + "' data-slot='" + time_slot_nextindex + "' onchange='checkVariableTime(this)'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
								"&nbsp;&nbsp;<span class='bminput bm_required_variable'>Min Cap:&nbsp;<input name='variable_time_slots[" + time_slot_nextindex + "][min_cap][" + i + "]' type='number' min='1' id='variable_min_cap_" + i + "' placeholder='" + bm_normal_object.minimum_capacity + "' value='1' style='width: 80px' onchange='changeVariableMaxCap(this)' autocomplete='off'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
								"&nbsp;&nbsp;<span class='bminput bm_required_variable'>Max Cap:&nbsp;<input name='variable_time_slots[" + time_slot_nextindex + "][max_cap][" + i + "]' type='number' min='1' id='variable_max_cap_" + i + "' placeholder='" + bm_normal_object.maximum_capacity + "' value=" + default_max_cap + " style='width: 80px' onchange='changeVariableMaxCap(this)' autocomplete='off'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
								"&nbsp;&nbsp;<span><input type='checkbox' name='variable_time_slots[" + time_slot_nextindex + "][hide_to_slot][" + i + "]' id='variable_hide_to_slot_" + i + "' value='0' onchange='hideToSlot(this)'>&nbsp;Hide 'to' slot ?</span>" +
								"&nbsp;&nbsp;<span><input type='checkbox' name='variable_time_slots[" + time_slot_nextindex + "][disable][" + i + "]' id='variable_disable_" + i + "' value='0' data-slot='" + time_slot_nextindex + "' onchange='disableVariableSlot(this)'>&nbsp;" + bm_normal_object.disable + "</span>" +
								"<div id='variable_universal_slot_error_" + i + "' style='display :none;font-family: monospace;color: #fb0000;font-size: 12px;margin-top :8px'></div></div><br>";
							slot_from = slot_to;
							slot_to = addTime(convertNumToTime(duration), slot_from);
						}
						jQuery("#time_slot_value").append("<input type='hidden' name='variable_time_slots[" + time_slot_nextindex + "][total_slots]' value='" + total_element + "'>");
						jQuery("#time_slot_value").append("<input type='hidden' name='variable_time_slots[" + time_slot_nextindex + "][slot_id]' id='variable_slot_id_" + time_slot_nextindex + "' value='" + time_slot_nextindex + "'>");
						jQuery("#time_slot_value").append("<input type='hidden' name='variable_time_slots[" + time_slot_nextindex + "][auto_time]' id='variable_auto_time_" + time_slot_nextindex + "' value='1'>");
						jQuery("#time_slot_value").append("<div id='autoSelectTime' class='bm-checkbox-td'><b>Autoselect Time ?</b>&nbsp;&nbsp;<input type='checkbox' checked name='variable_autoselect_time_" + time_slot_nextindex + "' id='variable_autoselect_time_" + time_slot_nextindex + "' class='auto-checkbox bm_toggle' value='1' onchange='variableAutoTime(this)'><label for='variable_autoselect_time_" + time_slot_nextindex + "'></label></div><br>" + time_slot_html);
						jQuery("#variable_active_slot_" + total_element + "").after("<div class='variable_slot_all_error_text' style='display :none;'></div>");
					}
				} else {
					jQuery('#loader_div').show();
					if (getUrlParameter('id') != '') {
						var id = getUrlParameter('id');
						var data = { 'action': 'bm_get_specific_time_slot', 'id': id, 'date': date, 'nonce': bm_ajax_object.nonce };
						jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
							if (jQuery.parseJSON(response).status == true) {

								var slot_data = jQuery.parseJSON(response).slot_data;

								if (slot_data.total_slots != 0) {
									var time_slot_html = '';

									if (old_default_max_cap != default_max_cap) {
										for (var i = 1; i <= total_element; i++) {
											time_slot_html += "<div id='variable_active_slot_" + i + "'><span class='bminput bm_required_variable time_box'>From:&nbsp;<input type='time' name='variable_time_slots[" + slot_data.slot_id + "][from][" + i + "]' id='variable_from_" + i + "' value='" + slot_data.from[i] + "' data-slot='" + slot_data.slot_id + "' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='checkVariableTime(this)'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;<span class='bminput bm_required_variable time_box'>To:&nbsp;<input type='time' name='variable_time_slots[" + slot_data.slot_id + "][to][" + i + "]' id='variable_to_" + i + "' value='" + slot_data.to[i] + "' data-slot='" + slot_data.slot_id + "' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='checkVariableTime(this)'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
												"&nbsp;&nbsp;<span class='bminput bm_required_variable'>Min Cap:&nbsp;<input name='variable_time_slots[" + slot_data.slot_id + "][min_cap][" + i + "]' type='number' min='1' id='variable_min_cap_" + i + "' placeholder='" + bm_normal_object.minimum_capacity + "' value='" + (slot_data.min_cap[i] !== null && slot_data.min_cap[i] <= default_max_cap ? slot_data.min_cap[i] : '1') + "' style='width: 80px' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='changeVariableMaxCap(this)' autocomplete='off'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
												"&nbsp;&nbsp;<span class='bminput bm_required_variable'>Max Cap:&nbsp;<input name='variable_time_slots[" + slot_data.slot_id + "][max_cap][" + i + "]' type='number' min='1' id='variable_max_cap_" + i + "' placeholder='" + bm_normal_object.maximum_capacity + "' value='" + default_max_cap + "' style='width: 80px' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='changeVariableMaxCap(this)' autocomplete='off'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
												"&nbsp;&nbsp;<span><input type='checkbox' name='variable_time_slots[" + slot_data.slot_id + "][hide_to_slot][" + i + "]' id='variable_hide_to_slot_" + i + "' value='" + (typeof (slot_data.hide_to_slot) != "undefined" && slot_data.hide_to_slot[i] !== null && slot_data.hide_to_slot[i] !== "undefined" ? slot_data.hide_to_slot[i] : '0') + "' " + (typeof (slot_data.hide_to_slot) != "undefined" && slot_data.hide_to_slot[i] !== null && slot_data.hide_to_slot[i] == 1 ? 'checked' : '') + " " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'class="readonly_checkbox"' : '') + " onchange='hideToSlot(this)'>&nbsp;Hide 'to' slot ?</span>" +
												"&nbsp;&nbsp;<span><input type='checkbox' name='variable_time_slots[" + slot_data.slot_id + "][disable][" + i + "]' id='variable_disable_" + i + "' value='" + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] !== "undefined" ? slot_data.disable[i] : '0') + "' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'checked' : '') + " data-slot='" + slot_data.slot_id + "' onchange='disableVariableSlot(this)'>&nbsp;" + bm_normal_object.disable + "</span>" +
												"<div id='variable_universal_slot_error_" + i + "' style='display :none;font-family: monospace;color: #fb0000;font-size: 12px;margin-top :8px'></div></div><br>";
										}
									} else if (old_default_max_cap == default_max_cap) {
										for (var i = 1; i <= slot_data.total_slots; i++) {
											time_slot_html += "<div id='variable_active_slot_" + i + "'><span class='bminput bm_required_variable time_box'>From:&nbsp;<input type='time' name='variable_time_slots[" + slot_data.slot_id + "][from][" + i + "]' id='variable_from_" + i + "' value='" + slot_data.from[i] + "' data-slot='" + slot_data.slot_id + "' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='checkVariableTime(this)'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;<span class='bminput bm_required_variable time_box'>To:&nbsp;<input type='time' name='variable_time_slots[" + slot_data.slot_id + "][to][" + i + "]' id='variable_to_" + i + "' value='" + slot_data.to[i] + "' data-slot='" + slot_data.slot_id + "' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='checkVariableTime(this)'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
												"&nbsp;&nbsp;<span class='bminput bm_required_variable'>Min Cap:&nbsp;<input name='variable_time_slots[" + slot_data.slot_id + "][min_cap][" + i + "]' type='number' min='1' id='variable_min_cap_" + i + "' placeholder='" + bm_normal_object.minimum_capacity + "' value='" + slot_data.min_cap[i] + "' style='width: 80px' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='changeVariableMaxCap(this)' autocomplete='off'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
												"&nbsp;&nbsp;<span class='bminput bm_required_variable'>Max Cap:&nbsp;<input name='variable_time_slots[" + slot_data.slot_id + "][max_cap][" + i + "]' type='number' min='" + slot_data.min_cap[i] + "' id='variable_max_cap_" + i + "' placeholder='" + bm_normal_object.maximum_capacity + "' value='" + slot_data.max_cap[i] + "' style='width: 80px' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'readonly' : '') + " onchange='changeVariableMaxCap(this)' autocomplete='off'><span class='variable_slots_errortext variable_calculated_time'></span></span>&nbsp;&nbsp;" +
												"&nbsp;&nbsp;<span><input type='checkbox' name='variable_time_slots[" + slot_data.slot_id + "][hide_to_slot][" + i + "]' id='variable_hide_to_slot_" + i + "' value='" + (typeof (slot_data.hide_to_slot) != "undefined" && slot_data.hide_to_slot[i] !== null && slot_data.hide_to_slot[i] !== "undefined" ? slot_data.hide_to_slot[i] : '0') + "' " + (typeof (slot_data.hide_to_slot) != "undefined" && slot_data.hide_to_slot[i] !== null && slot_data.hide_to_slot[i] == 1 ? 'checked' : '') + " " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'class="readonly_checkbox"' : '') + " onchange='hideToSlot(this)'>&nbsp;Hide 'to' slot ?</span>" +
												"&nbsp;&nbsp;<span><input type='checkbox' name='variable_time_slots[" + slot_data.slot_id + "][disable][" + i + "]' id='variable_disable_" + i + "' value='" + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] !== "undefined" ? slot_data.disable[i] : '0') + "' " + (typeof (slot_data.disable) != "undefined" && slot_data.disable[i] !== null && slot_data.disable[i] == 1 ? 'checked' : '') + " data-slot='" + slot_data.slot_id + "' onchange='disableVariableSlot(this)'>&nbsp;" + bm_normal_object.disable + "</span>" +
												"<div id='variable_universal_slot_error_" + i + "' style='display :none;font-family: monospace;color: #fb0000;font-size: 12px;margin-top :8px'></div></div><br>";
										}
									}
									jQuery('.bm-slot-spiner').after("<span id='remove_slot_button'><a href='javascript:void(0)' name='remove_time_slot_data' id='" + date + "' class='button button-primary' onClick='remove_time_slot(this.id)'>Remove Slot</a></span>");
									jQuery("#time_slot_value").append("<input type='hidden' name='variable_time_slots[" + slot_data.slot_id + "][total_slots]' value='" + slot_data.total_slots + "'>");
									jQuery("#time_slot_value").append("<input type='hidden' name='variable_time_slots[" + slot_data.slot_id + "][slot_id]' id='variable_slot_id_" + slot_data.slot_id + "' value='" + slot_data.slot_id + "'>");
									jQuery("#time_slot_value").append("<input type='hidden' name='variable_time_slots[" + slot_data.slot_id + "][auto_time]' id='variable_auto_time_" + slot_data.slot_id + "' value='" + slot_data.auto_time + "'>");
									jQuery("#time_slot_value").append("<div id='autoSelectTime' class='bm-checkbox-td'><b>Autoselect Time ?</b>&nbsp;&nbsp;<input type='checkbox' name='variable_autoselect_time_" + slot_data.slot_id + "' id='variable_autoselect_time_" + slot_data.slot_id + "' class='auto-checkbox bm_toggle' value='" + slot_data.auto_time + "' " + (slot_data.auto_time == 1 ? 'checked' : '') + " onchange='variableAutoTime(this)'><label for='variable_autoselect_time_" + slot_data.slot_id + "'></label></div><br>" + time_slot_html);
									jQuery("#variable_active_slot_" + slot_data.total_slots + "").after("<div class='variable_slot_all_error_text' style='display :none;'></div>");
								}
							}
						});
					} else {
						var id = slot_value.split('_')[1];
						jQuery('.bm-slot-spiner').after("<span id='remove_slot_button'><a href='javascript:void(0)' name='remove_time_slot_data' id='" + today + "' class='button button-primary' onClick='remove_time_slot(this.id)'>Remove Slot</a></span>");
						jQuery("#time_slot_value").append(jQuery("#variable_time_slot_data_" + id + "").html());
					}
					jQuery('#loader_div').hide();
				}

				jQuery('.time_slot_calendar_errortext').html('');
				jQuery('.time_slot_update_errortext').html('');
				jQuery('.time_slot_update_successtext').html('');
				jQuery('#time_slot_modal').show();
				jQuery([document.documentElement, document.body]).animate({
					scrollTop: jQuery("#time_slot_date").offset().top
				}, 2000);

				//Prevent the redraw.
				inst.inline = false;
			} else {
				jQuery('.time_slot_calendar_errortext').html(bm_error_object.set_time_slot);
				jQuery('.time_slot_calendar_errortext').show();

				//Prevent the redraw.
				inst.inline = false;
			}
		},
	});
	// getUrlParameter('id') != '' ? bm_get_service_time_slots() : addTimeSlotInfo(); // if open by default (when you're using <div>)
});




// Set Price Value in Calendar in fresh service
function addPriceInfo(type = '') {
	if ((getUrlParameter('id') != '') && (jQuery('#old_default_price').val() !== jQuery('#default_price').val())) {
		if (!confirm(bm_normal_object.price_change)) {
			jQuery('#default_price').val(jQuery('#old_default_price').val());
			return false;
		}
	}

	jQuery('.calendar_errortext').hide();
	jQuery('.price_update_successtext').hide();
	jQuery('.price_update_errortext').hide();
	jQuery('.variable_errortext').hide();
	jQuery('#svc_price_modal').hide();
	jQuery('.calendar_errortext').html(' ');
	jQuery('.price_update_successtext').html(' ');
	jQuery('.price_update_errortext').html(' ');
	jQuery('.variable_errortext').html('');
	if (type == '') {
		jQuery('#svc_calendar_date_inputs').html('');
		jQuery('#svc_calendar_price_inputs').html('');
		jQuery('#svc_calendar_module_date_inputs').html('');
		jQuery('#svc_calendar_module_inputs').html('');
	}

	if (getUrlParameter('id') != '' && jQuery('#old_default_price').val() != jQuery('#default_price').val()) {
		jQuery("#price_datepicker").datepicker("option", "changeMonth", false);
		jQuery("#price_datepicker").datepicker("option", "changeYear", false);
	}

	var currency_symbol = bm_normal_object.currency_symbol;
	var currency_position = bm_normal_object.currency_position;
	var default_price = jQuery.trim(jQuery('#default_price').val());
	var price_array = [];
	var price_date_array = [];
	var module_array = [];
	var module_date_array = [];

	jQuery('[id^=input_variable_price_]').each(function () {
		price_array.push(this.value);
	});

	jQuery('[id^=input_variable_date_]').each(function () {
		price_date_array.push(this.value);
	});

	jQuery('[id^=input_module_variable_]').each(function () {
		module_array.push(this.value);
	});

	jQuery('[id^=input_variable_module_date_]').each(function () {
		module_date_array.push(this.value);
	});

	setTimeout(function () {
		jQuery("#price_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
			var date = jQuery(this).text();
			return /\d/.test(date);
		}).find('a', 'span').html(function (i, html) {

			var day = jQuery(this).data('date');
			var month = jQuery(this).parent().data('month') + 1;
			var year = jQuery(this).parent().data('year');
			var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

			if (jQuery.inArray(date, price_date_array) !== -1) {
				var price = price_array[jQuery.inArray(date, price_date_array)];
				var text = currency_position == 'before' ? currency_symbol + parseFloat(price).toFixed(2) : parseFloat(price).toFixed(2) + currency_symbol;
				jQuery(this).attr('data-custom', price != '' ? text : 'N/A');

				if (parseFloat(price) > parseFloat(default_price)) {
					jQuery(this).addClass('highValue');
				} else if (parseFloat(price) < parseFloat(default_price)) {
					jQuery(this).addClass('lowValue');
				}
			} else if (jQuery.inArray(date, module_date_array) !== -1) {
				var module_id = module_array[jQuery.inArray(date, module_date_array)];
				var text = '#module_' + module_id;
				jQuery(this).attr('data-custom', module_id != '' ? text : 'N/A');
				jQuery(this).addClass('bluetValue');
			} else {
				var text = currency_position == 'before' ? currency_symbol + parseFloat(default_price).toFixed(2) : parseFloat(default_price).toFixed(2) + currency_symbol;
				jQuery(this).attr('data-custom', default_price != '' ? text : 'N/A');
				jQuery(this).addClass('brightValue');
			}
		});
	});
}




// Set Stopsales Value in Calendar in fresh service
function addStopsalesInfo(type = '') {
	if ((getUrlParameter('id') != '') && (jQuery('#old_default_stopsales').val() !== jQuery('#default_stopsales').val())) {
		if (!confirm(bm_normal_object.stopsales_change)) {
			jQuery('#default_stopsales').val(jQuery('#old_default_stopsales').val());
			return false;
		}
	}

	if (jQuery('.stopsales_errortext').is(':visible')) jQuery('.stopsales_errortext').hide();
	if (jQuery('.stopsales_update_successtext').is(':visible')) jQuery('.stopsales_update_successtext').hide();
	if (jQuery('.stopsales_update_errortext').is(':visible')) jQuery('.stopsales_update_errortext').hide();
	if (jQuery('.variable_hour_errortext').is(':visible')) jQuery('.variable_hour_errortext').hide();
	jQuery('#stopsales_modal').hide();
	jQuery('.stopsales_errortext').html(' ');
	jQuery('.stopsales_update_successtext').html(' ');
	jQuery('.stopsales_update_errortext').html(' ');
	jQuery('.variable_hour_errortext').html('');
	if (type == '') {
		jQuery('#stopsales_calendar_date_inputs').html('');
		jQuery('#stopsales_calendar_hour_inputs').html('');
	}

	var default_stopsales = jQuery.trim(jQuery('#default_stopsales').val());

	if (getUrlParameter('id') != '' && jQuery('#old_default_stopsales').val() != default_stopsales) {
		jQuery("#stopsales_datepicker").datepicker("option", "changeMonth", false);
		jQuery("#stopsales_datepicker").datepicker("option", "changeYear", false);
	}

	setTimeout(function () {
		jQuery("#stopsales_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
			var date = jQuery(this).text();
			return /\d/.test(date);
		}).find('a', 'span').html(function (i, html) {

			var day = jQuery(this).data('date');
			var month = jQuery(this).parent().data('month') + 1;
			var year = jQuery(this).parent().data('year');
			var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

			var stopsales = default_stopsales;
			var stopsales_array = [];
			var date_array = [];

			jQuery('[id^=input_variable_stopsales_hour_]').each(function () {
				stopsales_array.push(this.value);
			});

			jQuery('[id^=input_variable_stopsales_date_]').each(function () {
				date_array.push(this.value);
			});

			if (stopsales_array.length != 0 && date_array.length != 0) {
				if (jQuery.inArray(date, date_array) !== -1) {
					stopsales = stopsales_array[jQuery.inArray(date, date_array)];
					if (parseFloat(stopsales) > default_stopsales) {
						jQuery(this).css('color', '#fc2e05');
					} else if (parseFloat(stopsales) < default_stopsales) {
						jQuery(this).css('color', '#12812a');
					}
				}
			}
			if ((parseFloat(stopsales) == default_stopsales) || default_stopsales == '') jQuery(this).addClass('brightValue');
			jQuery(this).attr('data-custom', stopsales != '' ? parseFloat(stopsales) + 'h' : 'N/A');
		});
	});
}



// Set Saleswitch Value in Calendar in fresh service
function addSaleswitchInfo(type = '') {
	if ((getUrlParameter('id') != '') && (jQuery('#old_default_saleswitch').val() !== jQuery('#default_saleswitch').val())) {
		if (!confirm(bm_normal_object.saleswitch_change)) {
			jQuery('#default_saleswitch').val(jQuery('#old_default_saleswitch').val());
			return false;
		}
	}

	if (jQuery('.saleswitch_errortext').is(':visible')) jQuery('.saleswitch_errortext').hide();
	if (jQuery('.saleswitch_update_successtext').is(':visible')) jQuery('.saleswitch_update_successtext').hide();
	if (jQuery('.saleswitch_update_errortext').is(':visible')) jQuery('.saleswitch_update_errortext').hide();
	if (jQuery('.variable_saleswitch_errortext').is(':visible')) jQuery('.variable_saleswitch_errortext').hide();
	jQuery('#saleswitch_modal').hide();
	jQuery('.saleswitch_errortext').html(' ');
	jQuery('.saleswitch_update_successtext').html(' ');
	jQuery('.saleswitch_update_errortext').html(' ');
	jQuery('.variable_saleswitch_errortext').html('');
	if (type == '') {
		jQuery('#saleswitch_calendar_date_inputs').html('');
		jQuery('#saleswitch_calendar_hour_inputs').html('');
	}

	var default_saleswitch = jQuery.trim(jQuery('#default_saleswitch').val());

	if (getUrlParameter('id') != '' && jQuery('#old_default_saleswitch').val() != default_saleswitch) {
		jQuery("#saleswitch_datepicker").datepicker("option", "changeMonth", false);
		jQuery("#saleswitch_datepicker").datepicker("option", "changeYear", false);
	}

	setTimeout(function () {
		jQuery("#saleswitch_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
			var date = jQuery(this).text();
			return /\d/.test(date);
		}).find('a', 'span').html(function (i, html) {

			var day = jQuery(this).data('date');
			var month = jQuery(this).parent().data('month') + 1;
			var year = jQuery(this).parent().data('year');
			var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

			var saleswitch = default_saleswitch;
			var saleswitch_array = [];
			var date_array = [];

			jQuery('[id^=input_variable_saleswitch_hour_]').each(function () {
				saleswitch_array.push(this.value);
			});

			jQuery('[id^=input_variable_saleswitch_date_]').each(function () {
				date_array.push(this.value);
			});

			if (saleswitch_array.length != 0 && date_array.length != 0) {
				if (jQuery.inArray(date, date_array) !== -1) {
					saleswitch = saleswitch_array[jQuery.inArray(date, date_array)];
					if (parseFloat(saleswitch) > default_saleswitch) {
						jQuery(this).css('color', '#fc2e05');
					} else if (parseFloat(saleswitch) < default_saleswitch) {
						jQuery(this).css('color', '#12812a');
					}
				}
			}
			if ((parseFloat(saleswitch) == default_saleswitch) || default_saleswitch == '') jQuery(this).addClass('brightValue');
			jQuery(this).attr('data-custom', saleswitch != '' ? parseFloat(saleswitch) + 'h' : 'N/A');
		});
	});
}




// Set Capacity Value in Calendar in fresh service
function addCapacityInfo(type = '') {
	if ((getUrlParameter('id') != '') && (jQuery('#old_default_max_cap').val() !== jQuery('#default_max_cap').val())) {
		if (!confirm(bm_normal_object.max_cap_change)) {
			jQuery('#default_max_cap').val(jQuery('#old_default_max_cap').val());
			return false;
		}
	}

	jQuery('.capacity_calendar_errortext').hide();
	jQuery('.capacity_update_successtext').hide();
	jQuery('.capacity_update_errortext').hide();
	jQuery('.max_cap_errortext').hide();
	jQuery('.bulk_cap_errortext').hide();

	jQuery('.capacity_calendar_errortext').html(' ');
	jQuery('.capacity_update_successtext').html(' ');
	jQuery('.capacity_update_errortext').html(' ');
	jQuery('.max_cap_errortext').html('');
	jQuery('.bulk_cap_errortext').html('');

	var default_max_cap = jQuery('#default_max_cap').val();

	if (jQuery('#default_max_cap').length != 0 && default_max_cap.length != 0) {
		if (jQuery('#service_duration').is(':disabled')) {
			jQuery('#service_duration').prop('disabled', false);
		}
	} else {
		jQuery('#service_duration').val('');
		jQuery('#service_operation').val('');
		jQuery('#service_duration').prop('disabled', true);
		jQuery('#service_operation').prop('disabled', true);
		jQuery("#total_time_slots").val(0);
		jQuery('#time_slots').html('');
		jQuery('#time_slot_value').html('');
		jQuery('#time_slot_calendar_date_inputs').html('');
		jQuery('#time_slot_calendar_slot_data').html('');
		jQuery('.slot_blocks').hide();
		addTimeSlotInfo();
	}

	if (jQuery('#time_slots').html() != '') {
		jQuery('[id^=max_cap_]').each(function (id, item) {
			jQuery(item).val(default_max_cap);
		});

		jQuery('[id^=min_cap_]').each(function (id, item) {
			if (parseInt(jQuery(item).val()) > parseInt(default_max_cap)) jQuery(item).val(1);
		});
	}

	if (jQuery('#time_slot_value').html() != '') {
		jQuery('[id^=variable_max_cap_]').each(function (id, item) {
			jQuery(item).attr('value', default_max_cap);
			jQuery(item).val(default_max_cap);
		});

		jQuery('[id^=variable_min_cap_]').each(function (id, item) {
			if (parseInt(jQuery(item).val()) > parseInt(default_max_cap)) {
				jQuery(item).attr('value', 1);
				jQuery(item).val(1);
			}
		});
	}

	if (jQuery('#time_slot_calendar_slot_data').html() != '') {
		jQuery('[id^=variable_time_slot_data_]').each(function (id, item) {
			if (jQuery(item).attr('id').startsWith('variable_max_cap_')) {
				jQuery(item).attr('value', default_max_cap);
				jQuery(item).val(default_max_cap);
			}

			if (jQuery(item).attr('id').startsWith('variable_min_cap_')) {
				if (parseInt(jQuery(item).val()) > parseInt(default_max_cap)) {
					jQuery(item).attr('value', 1);
					jQuery(item).val(1);
				}
			}
		});
	}

	if (type == '') {
		jQuery('#capacity_calendar_date_inputs').html('');
		jQuery('#capacity_calendar_cap_inputs').html('');
	}

	if (getUrlParameter('id') != '' && jQuery('#old_default_max_cap').val() != default_max_cap) {
		jQuery("#cap_datepicker").datepicker("option", "changeMonth", false);
		jQuery("#cap_datepicker").datepicker("option", "changeYear", false);
	}

	setTimeout(function () {
		jQuery("#cap_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
			var date = jQuery(this).text();
			return /\d/.test(date);
		}).find('a', 'span').html(function (i, html) {

			var day = jQuery(this).data('date');
			var month = jQuery(this).parent().data('month') + 1;
			var year = jQuery(this).parent().data('year');
			var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

			var capacity = default_max_cap;
			var capacity_array = [];
			var date_array = [];

			jQuery('[id^=input_max_cap_]').each(function () {
				capacity_array.push(this.value);
			});

			jQuery('[id^=input_date_max_cap_]').each(function () {
				date_array.push(this.value);
			});

			if (capacity_array.length != 0 && date_array.length != 0) {
				if (jQuery.inArray(date, date_array) !== -1) {
					capacity = capacity_array[jQuery.inArray(date, date_array)];
					if (parseInt(capacity) > parseInt(default_max_cap)) {
						jQuery(this).css('color', '#fc2e05');
					} else if (parseInt(capacity) < parseInt(default_max_cap)) {
						jQuery(this).css('color', '#12812a');
					}
				}
			}
			if (parseInt(capacity) == parseInt(default_max_cap)) {
				jQuery(this).addClass('brightValue');
			}
			jQuery(this).attr('data-custom', capacity != '' ? capacity : 'N/A');
		});
	});
}




// Confirm Time Slot change in Calendar
function confirm_slot_change($this) {
	if ((getUrlParameter('id') != '')) {
		if (!confirm(bm_normal_object.timeslot_change)) {
			jQuery('#total_time_slots').val(jQuery('#old_total_time_slots').val());
			jQuery($this).val(jQuery($this).data('old'));
			return false;
		} else {
			return true;
		}
	} else {
		return true;
	}
}




// Set Time Slot in Calendar in fresh service
function addTimeSlotInfo(type = '') {
	jQuery('.time_slot_calendar_errortext').hide();
	jQuery('.time_slot_update_errortext').hide();
	jQuery('.time_slot_update_successtext').hide();
	jQuery('#time_slot_modal').hide();

	var total_time_slots = jQuery('#total_time_slots').val();

	if (type == '') {
		jQuery('#time_slot_calendar_date_inputs').html('');
		jQuery('#time_slot_calendar_slot_data').html('');
	}

	if (getUrlParameter('id') != '' && jQuery('#old_total_time_slots').val() != total_time_slots) {
		jQuery("#time_slots_datepicker").datepicker("option", "changeMonth", false);
		jQuery("#time_slots_datepicker").datepicker("option", "changeYear", false);
	}

	setTimeout(function () {
		jQuery("#time_slots_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
			var date = jQuery(this).text();
			return /\d/.test(date);
		}).find('a', 'span').html(function (i, html) {
			var day = jQuery(this).data('date');
			var month = jQuery(this).parent().data('month') + 1;
			var year = jQuery(this).parent().data('year');
			var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

			var date_array = [];
			var id_array = [];

			jQuery('[id^=input_variable_time_slot_date_]').each(function () {
				date_array.push(this.value);
			});

			jQuery('.slot_data_element').each(function () {
				id_array.push(this.id.split('_')[4]);
			});

			if (date_array.length != 0) {
				if (jQuery.inArray(date, date_array) !== -1) {
					jQuery(this).css('color', '#0752FF');
					jQuery(this).attr('data-custom', 'id_' + id_array[jQuery.inArray(date, date_array)]);
				} else {
					jQuery(this).addClass('brightValue');
					jQuery(this).attr('data-custom', 'Default');
				}
			} else {
				var time_slot_html = jQuery('#time_slots').html();
				jQuery(this).attr('data-custom', time_slot_html != '' ? 'Default' : 'N/A');
			}
		});
	});
}




// Variable Service Price Field Validation and Submission
function variable_price_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variableprice") == null) sessionStorage.setItem("variableprice", 1);

	if (jQuery('.calendar_errortext').is(':visible')) jQuery('.calendar_errortext').hide();
	if (jQuery('.price_update_successtext').is(':visible')) jQuery('.price_update_successtext').hide();
	if (jQuery('.price_update_errortext').is(':visible')) jQuery('.price_update_errortext').hide();
	if (jQuery('.variable_errortext').is(':visible')) jQuery('.variable_errortext').hide();
	jQuery('.calendar_errortext').html(' ');
	jQuery('.price_update_successtext').html(' ');
	jQuery('.price_update_errortext').html(' ');
	jQuery('.variable_errortext').html('');

	var id = getUrlParameter('id');
	var price = jQuery.trim(jQuery('#variable_price').val());
	var date = jQuery.trim(jQuery('#variable_date').val());
	var default_price = jQuery.trim(jQuery('#default_price').val());
	var old_default_price = jQuery.trim(jQuery('#old_default_price').val());

	var regex = /^[1-9]\d*(\.\d+)?$/;
	if (price == "" || date == "") {
		if (price == "") {
			jQuery('.variable_errortext').html(bm_error_object.price_required);
			jQuery('.variable_errortext').show();
		} else if (date == "") {
			jQuery('.variable_errortext').html(bm_error_object.date_required);
			jQuery('.variable_errortext').show();
		}
		return false;
	} else if (!price.match(regex)) {
		jQuery('.variable_errortext').html(bm_error_object.price_numeric);
		jQuery('.variable_errortext').show();
		return false;
	}

	if (id != '') {
		var data = {
			'id': id,
			'default_price': default_price,
			'old_default_price': old_default_price,
			'price': price,
			'date': date,
		}
		edit_calendar_service_price(data, 'single');
	} else {
		var done = false;
		var date_parts = date.split("-");
		var day = date_parts[2].replace(/^0+/, '');
		var month = date_parts[1].replace(/^0+/, '') - 1;
		var year = date_parts[0];

		done = save_calendar_service_price(year, month, day, date, price, default_price, done);

		if (done == true) {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_successtext').html(bm_success_object.price_set);
			if (jQuery('.price_update_successtext').not(':visible')) jQuery('.price_update_successtext').show();
		} else {
			jQuery('.variable_errortext').html(bm_error_object.server_error);
			if (jQuery('.variable_errortext').not(':visible')) jQuery('.variable_errortext').show();
		}
	}
}



// Variable Service Price Module Validation and Submission
function variable_price_module_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variableprice") == null) sessionStorage.setItem("variableprice", 1);

	jQuery('.calendar_errortext').hide();
	jQuery('.price_update_successtext').hide();
	jQuery('.price_update_errortext').hide();
	jQuery('.variable_errortext').hide();
	jQuery('.calendar_errortext').html(' ');
	jQuery('.price_update_successtext').html(' ');
	jQuery('.price_update_errortext').html(' ');
	jQuery('.variable_errortext').html('');

	var id = getUrlParameter('id');
	var module = jQuery.trim(jQuery('#variable_external_price_module').val());
	var date = jQuery.trim(jQuery('#variable_date').val());
	var default_price = jQuery.trim(jQuery('#default_price').val());
	var old_default_price = jQuery.trim(jQuery('#old_default_price').val());

	if (module == "" || date == "") {
		if (module == "") {
			jQuery('.variable_errortext').html(bm_error_object.price_module_required);
			jQuery('.variable_errortext').show();
		} else if (date == "") {
			jQuery('.variable_errortext').html(bm_error_object.date_required);
			jQuery('.variable_errortext').show();
		}
		return false;
	}

	if (id != '') {
		var data = {
			'id': id,
			'module': module,
			'date': date,
			'default_price': default_price,
			'old_default_price': old_default_price,
		}
		edit_calendar_service_price_module(data, 'single');
	} else {
		var done = 0;
		var date_parts = date.split("-");
		var day = date_parts[2].replace(/^0+/, '');
		var month = date_parts[1].replace(/^0+/, '') - 1;
		var year = date_parts[0];

		done = save_calendar_service_price_module(year, month, day, date, module, done);

		if (done > 0) {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_successtext').html(bm_success_object.price_set);
			jQuery('.price_update_successtext').show();
		} else {
			jQuery('.variable_errortext').html(bm_error_object.server_error);
			jQuery('.variable_errortext').show();
		}
	}
}




// Variable Service Price Field Validation and Submission
function variable_stopsales_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variablehour") == null) sessionStorage.setItem("variablehour", 1);

	if (jQuery('.stopsales_errortext').is(':visible')) jQuery('.stopsales_errortext').hide();
	if (jQuery('.stopsales_update_successtext').is(':visible')) jQuery('.stopsales_update_successtext').hide();
	if (jQuery('.stopsales_update_errortext').is(':visible')) jQuery('.stopsales_update_errortext').hide();
	if (jQuery('.variable_hour_errortext').is(':visible')) jQuery('.variable_hour_errortext').hide();
	jQuery('.stopsales_errortext').html(' ');
	jQuery('.stopsales_update_successtext').html(' ');
	jQuery('.stopsales_update_errortext').html(' ');
	jQuery('.variable_hour_errortext').html('');

	var id = getUrlParameter('id');
	var stopsales = jQuery.trim(jQuery('#variable_hour').val());
	var date = jQuery.trim(jQuery('#variable_stopsales_date').val());
	var default_stopsales = jQuery.trim(jQuery('#default_stopsales').val());
	var old_default_stopsales = jQuery.trim(jQuery('#old_default_stopsales').val());

	if (date == "") {
		jQuery('.variable_hour_errortext').html(bm_error_object.date_required);
		jQuery('.variable_hour_errortext').show();
		return false;
	}

	if (id != '') {
		var data = {
			'id': id,
			'default_stopsales': default_stopsales,
			'old_default_stopsales': old_default_stopsales,
			'stopsales': stopsales,
			'date': date,
		}
		edit_calendar_service_stopsales(data, 'single');
	} else {
		var done = false;
		var date_parts = date.split("-");
		var day = date_parts[2].replace(/^0+/, '');
		var month = date_parts[1].replace(/^0+/, '') - 1;
		var year = date_parts[0];

		done = save_calendar_service_stopsales(year, month, day, date, stopsales, default_stopsales, done);

		if (done == true) {
			jQuery('#stopsales_modal').hide();
			jQuery('.stopsales_update_successtext').html(bm_success_object.stopsales_set);
			if (jQuery('.stopsales_update_successtext').not(':visible')) jQuery('.stopsales_update_successtext').show();
		} else {
			jQuery('.variable_hour_errortext').html(bm_error_object.server_error);
			if (jQuery('.variable_hour_errortext').not(':visible')) jQuery('.variable_hour_errortext').show();
		}
	}
}



// Variable Service saleswitch Field Validation and Submission
function variable_saleswitch_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variablesaleswitch") == null) sessionStorage.setItem("variablesaleswitch", 1);

	if (jQuery('.saleswitch_errortext').is(':visible')) jQuery('.saleswitch_errortext').hide();
	if (jQuery('.saleswitch_update_successtext').is(':visible')) jQuery('.saleswitch_update_successtext').hide();
	if (jQuery('.saleswitch_update_errortext').is(':visible')) jQuery('.saleswitch_update_errortext').hide();
	if (jQuery('.variable_saleswitch_errortext').is(':visible')) jQuery('.variable_saleswitch_errortext').hide();
	jQuery('.saleswitch_errortext').html(' ');
	jQuery('.saleswitch_update_successtext').html(' ');
	jQuery('.saleswitch_update_errortext').html(' ');
	jQuery('.variable_saleswitch_errortext').html('');

	var id = getUrlParameter('id');
	var saleswitch = jQuery.trim(jQuery('#variable_saleswitch_hour').val());
	var date = jQuery.trim(jQuery('#variable_saleswitch_date').val());
	var default_saleswitch = jQuery.trim(jQuery('#default_saleswitch').val());
	var old_default_saleswitch = jQuery.trim(jQuery('#old_default_saleswitch').val());

	if (date == "") {
		jQuery('.variable_saleswitch_errortext').html(bm_error_object.date_required);
		jQuery('.variable_saleswitch_errortext').show();
		return false;
	}

	if (id != '') {
		var data = {
			'id': id,
			'default_saleswitch': default_saleswitch,
			'old_default_saleswitch': old_default_saleswitch,
			'saleswitch': saleswitch,
			'date': date,
		}
		edit_calendar_service_saleswitch(data, 'single');
	} else {
		var done = false;
		var date_parts = date.split("-");
		var day = date_parts[2].replace(/^0+/, '');
		var month = date_parts[1].replace(/^0+/, '') - 1;
		var year = date_parts[0];

		done = save_calendar_service_saleswitch(year, month, day, date, saleswitch, default_saleswitch, done);

		if (done == true) {
			jQuery('#saleswitch_modal').hide();
			jQuery('.saleswitch_update_successtext').html(bm_success_object.saleswitch_set);
			if (jQuery('.saleswitch_update_successtext').not(':visible')) jQuery('.saleswitch_update_successtext').show();
		} else {
			jQuery('.variable_saleswitch_errortext').html(bm_error_object.server_error);
			if (jQuery('.variable_saleswitch_errortext').not(':visible')) jQuery('.variable_saleswitch_errortext').show();
		}
	}
}




// Service Maximum Capacity Field Validation and Submission
function validate_capacity_and_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variablecapacity") == null) sessionStorage.setItem("variablecapacity", 1);

	jQuery('.capacity_calendar_errortext').hide();
	jQuery('.capacity_update_successtext').hide();
	jQuery('.capacity_update_errortext').hide();
	jQuery('.max_cap_errortext').hide();
	jQuery('.bulk_cap_errortext').hide();

	jQuery('.capacity_calendar_errortext').html(' ');
	jQuery('.capacity_update_successtext').html(' ');
	jQuery('.capacity_update_errortext').html(' ');
	jQuery('.max_cap_errortext').html('');
	jQuery('.bulk_cap_errortext').html('');

	var id = getUrlParameter('id');
	var capacity = jQuery.trim(jQuery('#max_cap_value').val());
	var date = jQuery.trim(jQuery('#max_cap_date').val());
	var default_max_cap = jQuery.trim(jQuery('#default_max_cap').val());
	var old_default_max_cap = jQuery.trim(jQuery('#old_default_max_cap').val());

	if (capacity == "" || date == "") {
		if (capacity == "") {
			jQuery('.max_cap_errortext').html(bm_error_object.capacity_required);
			jQuery('.max_cap_errortext').show();
		} else if (date == "") {
			jQuery('.max_cap_errortext').html(bm_error_object.date_required);
			jQuery('.max_cap_errortext').show();
		}
		return false;
	}

	if (id != '') {
		var data = {
			'id': id,
			'default_max_cap': default_max_cap,
			'old_default_max_cap': old_default_max_cap,
			'capacity': capacity,
			'date': date,
		}
		edit_calendar_service_max_cap(data, 'single');
	} else {
		var done = false;
		var date_parts = date.split("-");
		var day = date_parts[2].replace(/^0+/, '');
		var month = date_parts[1].replace(/^0+/, '') - 1;
		var year = date_parts[0];

		done = save_calendar_service_max_cap(year, month, day, date, capacity, default_max_cap, done);

		if (done == true) {
			jQuery('#max_cap_modal').hide();
			jQuery('.capacity_update_successtext').html(bm_success_object.capacity_set);
			jQuery('.capacity_update_successtext').show();
		} else {
			jQuery('.max_cap_errortext').html(bm_error_object.server_error);
			jQuery('.max_cap_errortext').show();
		}
	}
}



// Service Time Slots Field Validation and Submission
function validate_slots_and_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variabletimeslot") == null) sessionStorage.setItem("variabletimeslot", 1);

	jQuery('.bm-slot-spiner').show();

	jQuery('.time_slot_calendar_errortext').hide();
	jQuery('.time_slot_update_successtext').hide();
	jQuery('.time_slot_update_errortext').hide();

	jQuery('.time_slot_calendar_errortext').html(' ');
	jQuery('.time_slot_update_successtext').html(' ');
	jQuery('.time_slot_update_errortext').html(' ');

	if (validateVariableSlots()) {
		var id = getUrlParameter('id');
		var default_time_slots = {};
		var time_slots_data = {};

		var date = jQuery.trim(jQuery('#time_slot_date').val());
		var service_duration = jQuery.trim(jQuery('#service_duration').val());
		var service_operation = jQuery.trim(jQuery('#service_operation').val());
		var old_default_max_cap = jQuery.trim(jQuery('#old_default_max_cap').val());
		var default_max_cap = jQuery.trim(jQuery('#default_max_cap').val());
		var total_time_slots = jQuery.trim(jQuery('#total_time_slots').val());
		var old_total_time_slots = jQuery.trim(jQuery('#old_total_time_slots').val());

		jQuery('#time_slot_value :input').map(function () {
			var type = jQuery(this).prop("type");
			var name = jQuery(this).attr("name");

			if (!jQuery(this).attr("name").startsWith('variable_autoselect_time_')) {
				if ((type == "checkbox")) {
					if (this.checked) {
						time_slots_data[name.substring(name.indexOf('[') + 1)] = 1;
					} else {
						time_slots_data[name.substring(name.indexOf('[') + 1)] = 0;
					}
				} else {
					time_slots_data[name.substring(name.indexOf('[') + 1)] = jQuery(this).val();
				}
			}
		});

		if (old_total_time_slots != total_time_slots) {
			jQuery('#time_slots :input').map(function () {
				var type = jQuery(this).prop("type");
				var name = jQuery(this).attr("name");

				if (!jQuery(this).attr("name").startsWith('variable_autoselect_time_')) {
					if ((type == "checkbox")) {
						if (this.checked) {
							default_time_slots[name.substring(name.indexOf('[') + 1)] = 1;
						} else {
							default_time_slots[name.substring(name.indexOf('[') + 1)] = 0;
						}
					} else {
						default_time_slots[name.substring(name.indexOf('[') + 1)] = jQuery(this).val();
					}
				}
			});
		}

		if (id != '') {
			var data = {
				'id': id,
				'service_duration': service_duration,
				'service_operation': service_operation,
				'old_default_max_cap': old_default_max_cap,
				'default_max_cap': default_max_cap,
				'total_time_slots': total_time_slots,
				'old_total_time_slots': old_total_time_slots,
				'default_time_slots': default_time_slots,
				'time_slots_data': time_slots_data,
				'date': date,
			}
			edit_calendar_service_variable_time_slots(data);
		} else {
			var done = false;
			var date_parts = date.split("-");
			var day = date_parts[2].replace(/^0+/, '');
			var month = date_parts[1].replace(/^0+/, '') - 1;
			var year = date_parts[0];

			if (jQuery('.slot_data_element').length != 0) {
				var lastid = jQuery('.slot_data_element:last').attr("id");
				var slot_nextindex = Number(lastid.split("_")[4]) + 1;
			} else {
				var slot_nextindex = 1;
			}

			done = save_calendar_variable_time_slots(year, month, day, date, time_slots_data, slot_nextindex, done);

			if (done == true) {
				jQuery('#time_slot_modal').hide();
				jQuery('.time_slot_update_successtext').html(bm_success_object.time_slot_set);
				jQuery('.time_slot_update_successtext').show();
			} else {
				jQuery('.time_slot_errortext').html(bm_error_object.server_error);
				jQuery('.time_slot_errortext').show();
			}
		}
		jQuery('.bm-slot-spiner').hide();
	}
}




// Validation for variable time slots
function validateVariableSlots() {
	jQuery('.variable_slots_errortext').html('');
	jQuery('.variable_slots_errortext').hide();
	jQuery('.variable_slot_all_error_text').html('');

	jQuery('.bm_required_variable').each(
		function (index, element) {
			var value = jQuery.trim(jQuery(this).children('input').val());
			if (value == "") {
				if (!jQuery(this).children('input').prop('readonly')) {
					jQuery(this).children('.variable_slots_errortext').html(bm_error_object.required);
					jQuery(this).children('.variable_slots_errortext').show();
				}
			}
		}
	);

	var b = '';
	b = jQuery('.variable_slots_errortext').each(
		function () {
			var a = jQuery(this).html();
			b = a + b;
			jQuery('.variable_slot_all_error_text').html(b);
		}
	);

	var error = jQuery('.variable_slot_all_error_text').html();
	if (error == '') {
		return true;
	} else {
		return false;
	}
}




// Bulk Service Price Validation and Submission
function bulk_price_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variableprice") == null) sessionStorage.setItem("variableprice", 1);

	var b = 0;
	jQuery('.calendar_errortext').hide();
	jQuery('.price_update_successtext').hide();
	jQuery('.price_update_errortext').hide();
	jQuery('.bulk_errortext').hide();
	jQuery('.calendar_errortext').html(' ');
	jQuery('.price_update_successtext').html(' ');
	jQuery('.price_update_errortext').html(' ');
	jQuery('.bulk_errortext').html(' ');

	var id = getUrlParameter('id');
	var from_date = jQuery.trim(jQuery('#from_bulk_price_change').val());
	var to_date = jQuery.trim(jQuery('#to_bulk_price_change').val());
	var default_price = jQuery.trim(jQuery('#default_price').val());
	var old_default_price = jQuery.trim(jQuery('#old_default_price').val());
	var price = jQuery.trim(jQuery('#bulk_variable_price').val());

	if (from_date == '') {
		jQuery('#from_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.from_date_required);
		jQuery('#from_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').show();
		b++;
	}

	if (to_date == '') {
		jQuery('#to_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.to_date_required);
		jQuery('#to_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').show();
		b++;
	}

	if (price == '') {
		jQuery('#bulk_variable_price').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.price_required);
		jQuery('#bulk_variable_price').parent('.bulk_bm_required').find('.bulk_errortext').show();
		b++;
	} else {
		var regex = /^[1-9]\d*(\.\d+)?$/;
		if (!price.match(regex)) {
			jQuery('#bulk_variable_price').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.price_numeric);
			jQuery('#bulk_variable_price').parent('.bulk_bm_required').find('.bulk_errortext').show();
			b++;
		}
	}

	if (b > 0) {
		return false;
	}

	if (getUrlParameter('id') != '') {
		var data = {
			'id': id,
			'from_date': from_date,
			'to_date': to_date,
			'default_price': default_price,
			'old_default_price': old_default_price,
			'price': price,
		}
		edit_calendar_service_price(data, 'multiple');
	} else {
		var done = false;
		var daysArray = getDaysArray(from_date, to_date);

		for (var i = 0; i < daysArray.length; i++) {
			var date = new Date(daysArray[i]);
			year = date.getFullYear();
			month = date.getMonth();
			day = date.getDate();
			date = year + '-' + padWithZeros(month + 1) + '-' + padWithZeros(day);

			done = save_calendar_service_price(year, month, day, date, price, default_price, done);
		}

		if (done == true) {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_successtext').html(bm_success_object.price_set);
			jQuery('.price_update_successtext').show();
		} else {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_errortext').html(bm_error_object.server_error);
			jQuery('.price_update_errortext').show();
		}
	}
}



// Bulk Service Price Module Validation and Submission
function bulk_variable_price_module_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variableprice") == null) sessionStorage.setItem("variableprice", 1);

	var b = 0;
	jQuery('.calendar_errortext').hide();
	jQuery('.price_update_successtext').hide();
	jQuery('.price_update_errortext').hide();
	jQuery('.bulk_errortext').hide();
	jQuery('.calendar_errortext').html(' ');
	jQuery('.price_update_successtext').html(' ');
	jQuery('.price_update_errortext').html(' ');
	jQuery('.bulk_errortext').html(' ');

	var id = getUrlParameter('id');
	var from_date = jQuery.trim(jQuery('#from_bulk_price_change').val());
	var to_date = jQuery.trim(jQuery('#to_bulk_price_change').val());
	var module = jQuery.trim(jQuery('#bulk_variable_external_price_module').val());
	var default_price = jQuery.trim(jQuery('#default_price').val());
	var old_default_price = jQuery.trim(jQuery('#old_default_price').val());

	if (from_date == '') {
		jQuery('#from_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.from_date_required);
		jQuery('#from_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').show();
		b++;
	}

	if (to_date == '') {
		jQuery('#to_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.to_date_required);
		jQuery('#to_bulk_price_change').parent('.bulk_bm_required').find('.bulk_errortext').show();
		b++;
	}

	if (module == '') {
		jQuery('#bulk_variable_external_price_module').parent('.bulk_bm_required').find('.bulk_errortext').html(bm_error_object.price_module_required);
		jQuery('#bulk_variable_external_price_module').parent('.bulk_bm_required').find('.bulk_errortext').show();
		b++;
	}

	if (b > 0) {
		return false;
	}

	if (getUrlParameter('id') != '') {
		var data = {
			'id': id,
			'from_date': from_date,
			'to_date': to_date,
			'module': module,
			'default_price': default_price,
			'old_default_price': old_default_price,
		}
		edit_calendar_service_price_module(data, 'multiple');
	} else {
		var done = 0;
		var daysArray = getDaysArray(from_date, to_date);

		for (var i = 0; i < daysArray.length; i++) {
			var date = new Date(daysArray[i]);
			year = date.getFullYear();
			month = date.getMonth();
			day = date.getDate();
			date = year + '-' + padWithZeros(month + 1) + '-' + padWithZeros(day);

			done = save_calendar_service_price_module(year, month, day, date, module, done);
		}

		if (done > 0) {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_successtext').html(bm_success_object.price_set);
			jQuery('.price_update_successtext').show();
		} else {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_errortext').html(bm_error_object.server_error);
			jQuery('.price_update_errortext').show();
		}
	}
}



// Bulk Service Stopsales Validation and Submission
function bulk_stopsales_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variablehour") == null) sessionStorage.setItem("variablehour", 1);

	var error = false;
	if (jQuery('.stopsales_errortext').is(':visible')) jQuery('.stopsales_errortext').hide();
	if (jQuery('.stopsales_update_successtext').is(':visible')) jQuery('.stopsales_update_successtext').hide();
	if (jQuery('.stopsales_update_errortext').is(':visible')) jQuery('.stopsales_update_errortext').hide();
	if (jQuery('.variable_hour_errortext').is(':visible')) jQuery('.variable_hour_errortext').hide();
	jQuery('.stopsales_errortext').html(' ');
	jQuery('.stopsales_update_successtext').html(' ');
	jQuery('.stopsales_update_errortext').html(' ');
	jQuery('.variable_hour_errortext').html('');

	jQuery('.bulk_stopsales_bm_required').each(function () {
		var value = jQuery(this).children('input').val();
		if (value == "") {
			if (jQuery(this).children('input').attr('name') == 'from_bulk_stopsales_change') {
				jQuery(this).children('.bulk_stopsales_errortext').html(bm_error_object.from_date_required);
				jQuery(this).children('.bulk_stopsales_errortext').show();
			}
			if (jQuery(this).children('input').attr('name') == 'to_bulk_stopsales_change') {
				jQuery(this).children('.bulk_stopsales_errortext').html(bm_error_object.to_date_required);
				jQuery(this).children('.bulk_stopsales_errortext').show();
			}
			if (error == false) error = true;
		}
	});

	if (error == true) {
		return false;
	}

	var id = getUrlParameter('id');
	var from_date = jQuery.trim(jQuery('#from_bulk_stopsales_change').val());
	var to_date = jQuery.trim(jQuery('#to_bulk_stopsales_change').val());
	var default_stopsales = jQuery.trim(jQuery('#default_stopsales').val());
	var old_default_stopsales = jQuery.trim(jQuery('#old_default_stopsales').val());
	var stopsales = jQuery.trim(jQuery('#bulk_variable_hour').val());

	if (getUrlParameter('id') != '') {
		var data = {
			'id': id,
			'from_date': from_date,
			'to_date': to_date,
			'default_stopsales': default_stopsales,
			'old_default_stopsales': old_default_stopsales,
			'stopsales': stopsales,
		}
		edit_calendar_service_stopsales(data, 'multiple');
	} else {
		var done = false;
		var daysArray = getDaysArray(from_date, to_date);

		for (var i = 0; i < daysArray.length; i++) {
			var date = new Date(daysArray[i]);
			year = date.getFullYear();
			month = date.getMonth();
			day = date.getDate();
			date = year + '-' + padWithZeros(month + 1) + '-' + padWithZeros(day);

			done = save_calendar_service_stopsales(year, month, day, date, stopsales, default_stopsales, done);
		}

		if (done == true) {
			jQuery('#stopsales_modal').hide();
			jQuery('.stopsales_update_successtext').html(bm_success_object.stopsales_set);
			if (jQuery('.stopsales_update_successtext').not(':visible')) jQuery('.stopsales_update_successtext').show();
		} else {
			jQuery('#stopsales_modal').hide();
			jQuery('.stopsales_update_errortext').html(bm_error_object.server_error);
			if (jQuery('.stopsales_update_errortext').not(':visible')) jQuery('.stopsales_update_errortext').show();
		}
	}
}



// Bulk Service Saleswitch Validation and Submission
function bulk_saleswitch_validation_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variablesaleswitch") == null) sessionStorage.setItem("variablesaleswitch", 1);

	var error = false;
	if (jQuery('.saleswitch_errortext').is(':visible')) jQuery('.saleswitch_errortext').hide();
	if (jQuery('.saleswitch_update_successtext').is(':visible')) jQuery('.saleswitch_update_successtext').hide();
	if (jQuery('.saleswitch_update_errortext').is(':visible')) jQuery('.saleswitch_update_errortext').hide();
	if (jQuery('.variable_saleswitch_errortext').is(':visible')) jQuery('.variable_saleswitch_errortext').hide();
	jQuery('.saleswitch_errortext').html(' ');
	jQuery('.saleswitch_update_successtext').html(' ');
	jQuery('.saleswitch_update_errortext').html(' ');
	jQuery('.variable_saleswitch_errortext').html('');

	jQuery('.bulk_saleswitch_bm_required').each(function () {
		var value = jQuery(this).children('input').val();
		if (value == "") {
			if (jQuery(this).children('input').attr('name') == 'from_bulk_saleswitch_change') {
				jQuery(this).children('.bulk_saleswitch_errortext').html(bm_error_object.from_date_required);
				jQuery(this).children('.bulk_saleswitch_errortext').show();
			}
			if (jQuery(this).children('input').attr('name') == 'to_bulk_saleswitch_change') {
				jQuery(this).children('.bulk_saleswitch_errortext').html(bm_error_object.to_date_required);
				jQuery(this).children('.bulk_saleswitch_errortext').show();
			}
			if (error == false) error = true;
		}
	});

	if (error == true) {
		return false;
	}

	var id = getUrlParameter('id');
	var from_date = jQuery.trim(jQuery('#from_bulk_saleswitch_change').val());
	var to_date = jQuery.trim(jQuery('#to_bulk_saleswitch_change').val());
	var default_saleswitch = jQuery.trim(jQuery('#default_saleswitch').val());
	var old_default_saleswitch = jQuery.trim(jQuery('#old_default_saleswitch').val());
	var saleswitch = jQuery.trim(jQuery('#bulk_saleswitch_hour').val());

	if (getUrlParameter('id') != '') {
		var data = {
			'id': id,
			'from_date': from_date,
			'to_date': to_date,
			'default_saleswitch': default_saleswitch,
			'old_default_saleswitch': old_default_saleswitch,
			'saleswitch': saleswitch,
		}
		edit_calendar_service_saleswitch(data, 'multiple');
	} else {
		var done = false;
		var daysArray = getDaysArray(from_date, to_date);

		for (var i = 0; i < daysArray.length; i++) {
			var date = new Date(daysArray[i]);
			year = date.getFullYear();
			month = date.getMonth();
			day = date.getDate();
			date = year + '-' + padWithZeros(month + 1) + '-' + padWithZeros(day);

			done = save_calendar_service_saleswitch(year, month, day, date, saleswitch, default_saleswitch, done);
		}

		if (done == true) {
			jQuery('#saleswitch_modal').hide();
			jQuery('.saleswitch_update_successtext').html(bm_success_object.saleswitch_set);
			if (jQuery('.saleswitch_update_successtext').not(':visible')) jQuery('.saleswitch_update_successtext').show();
		} else {
			jQuery('#saleswitch_modal').hide();
			jQuery('.saleswitch_update_errortext').html(bm_error_object.server_error);
			if (jQuery('.saleswitch_update_errortext').not(':visible')) jQuery('.saleswitch_update_errortext').show();
		}
	}
}



// Bulk Service Maximum Capacity Validation and Submission
function validate_bulk_capacity_and_submit() {

	// Set Session Value If Doesn't Exist
	if (sessionStorage.getItem("variablecapacity") == null) sessionStorage.setItem("variablecapacity", 1);

	var error = false;
	jQuery('.capacity_calendar_errortext').hide();
	jQuery('.capacity_update_successtext').hide();
	jQuery('.capacity_update_errortext').hide();
	jQuery('.max_cap_errortext').hide();
	jQuery('.bulk_cap_errortext').hide();

	jQuery('.capacity_calendar_errortext').html(' ');
	jQuery('.capacity_update_successtext').html(' ');
	jQuery('.capacity_update_errortext').html(' ');
	jQuery('.max_cap_errortext').html('');
	jQuery('.bulk_cap_errortext').html('');

	jQuery('.bulk_cap_bm_required').each(function () {
		var value = jQuery.trim(jQuery(this).children('input').val());
		if (value == "") {
			if (jQuery(this).children('input').attr('name') == 'from_bulk_cap_change') {
				jQuery(this).children('.bulk_cap_errortext').html(bm_error_object.from_date_required);
				jQuery(this).children('.bulk_cap_errortext').show();
			}
			if (jQuery(this).children('input').attr('name') == 'to_bulk_cap_change') {
				jQuery(this).children('.bulk_cap_errortext').html(bm_error_object.to_date_required);
				jQuery(this).children('.bulk_cap_errortext').show();
			}
			if (jQuery(this).children('input').attr('name') == 'bulk_max_cap') {
				jQuery(this).children('.bulk_cap_errortext').html(bm_error_object.capacity_required);
				jQuery(this).children('.bulk_cap_errortext').show();
			}
			if (error == false) error = true;
		}
	});

	if (error == true) {
		return false;
	}

	var id = getUrlParameter('id');
	var from_date = jQuery.trim(jQuery('#from_bulk_cap_change').val());
	var to_date = jQuery.trim(jQuery('#to_bulk_cap_change').val());
	var default_max_cap = jQuery.trim(jQuery('#default_max_cap').val());
	var old_default_max_cap = jQuery.trim(jQuery('#old_default_max_cap').val());
	var capacity = jQuery.trim(jQuery('#bulk_max_cap').val());

	if (id != '') {
		var data = {
			'id': id,
			'from_date': from_date,
			'to_date': to_date,
			'default_max_cap': default_max_cap,
			'old_default_max_cap': old_default_max_cap,
			'capacity': capacity,
		}
		edit_calendar_service_max_cap(data, 'multiple');
	} else {
		var done = false;
		var daysArray = getDaysArray(from_date, to_date);

		for (var i = 0; i < daysArray.length; i++) {
			var date = new Date(daysArray[i]);
			year = date.getFullYear();
			month = date.getMonth();
			day = date.getDate();
			date = year + '-' + padWithZeros(month + 1) + '-' + padWithZeros(day);

			done = save_calendar_service_max_cap(year, month, day, date, capacity, default_max_cap, done);
		}

		if (done == true) {
			jQuery('#max_cap_modal').hide();
			jQuery('.capacity_update_successtext').html(bm_success_object.capacity_set);
			if (jQuery('.capacity_update_successtext').not(':visible')) jQuery('.capacity_update_successtext').show();
		} else {
			jQuery('#max_cap_modal').hide();
			jQuery('.capacity_update_errortext').html(bm_error_object.server_error);
			if (jQuery('.capacity_update_errortext').not(':visible')) jQuery('.capacity_update_errortext').show();
		}
	}
}




// Save Service Calendar Price
function save_calendar_service_price(year = '', month = '', day = '', date = '', price = '', default_price = '', done = false) {
	var element = jQuery("#price_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	}).find("a");

	var price_text = bm_normal_object.currency_position == 'before' ? bm_normal_object.currency_symbol + parseFloat(price).toFixed(2) : parseFloat(price).toFixed(2) + bm_normal_object.currency_symbol;

	element.attr('data-custom', price != '' ? price_text : 'N/A');
	if (parseFloat(price) > default_price) {
		element.css('color', '#fc2e05');
	} else if (parseFloat(price) < default_price) {
		element.css('color', '#12812a');
	} else if (parseFloat(price) == default_price) {
		element.css('color', '#000000');
	}

	var date_duplicate = jQuery('[id^=input_variable_date_]').filter(function () {
		return this.value == date;
	});

	if (date_duplicate.length != 0) {
		var id = date_duplicate.attr('id');
		var index = Number(id.split("_")[3]);
		date_duplicate.remove();
		jQuery('#input_variable_price_' + index).remove();

		jQuery('[id^=input_variable_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_date_' + counter);
			jQuery(item).attr('name', 'variable_svc_prices[date][' + counter + ']');
		});

		jQuery('[id^=input_variable_price_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_price_' + counter);
			jQuery(item).attr('name', 'variable_svc_prices[price][' + counter + ']');
		});

		done = true;
	}

	if (parseFloat(price) != parseFloat(default_price)) {
		var date_element = jQuery('[id^=input_variable_date_]').length;
		var price_element = jQuery('[id^=input_variable_price_]').length;
		if (date_element != 0 && price_element != 0 && date_element == price_element) {
			var date_lastid = jQuery('#svc_calendar_date_inputs input:last').attr("id");
			var price_lastid = jQuery('#svc_calendar_price_inputs input:last').attr("id");
			var date_nextindex = Number(date_lastid.split("_")[3]) + 1;
			var price_nextindex = Number(price_lastid.split("_")[3]) + 1;
		} else {
			var date_nextindex = price_nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_date_' + date_nextindex + '', name: 'variable_svc_prices[date][' + date_nextindex + ']', class: 'price-date-inputs', value: date }).appendTo('#svc_calendar_date_inputs');
		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_price_' + price_nextindex + '', name: 'variable_svc_prices[price][' + price_nextindex + ']', class: 'price-price-inputs', value: price }).appendTo('#svc_calendar_price_inputs');

		done = true;
	}

	var module_date_duplicate = jQuery('[id^=input_variable_module_date_]').filter(function () {
		return this.value == date;
	});

	if (module_date_duplicate.length != 0) {
		var id = module_date_duplicate.attr('id');
		var index = Number(id.split("_")[4]);
		module_date_duplicate.remove();
		jQuery('#input_module_variable_' + index).remove();

		jQuery('[id^=input_variable_module_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_module_date_' + counter);
			jQuery(item).attr('name', 'variable_svc_price_modules[date][' + counter + ']');
		});

		jQuery('[id^=input_module_variable_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_module_variable_' + counter);
			jQuery(item).attr('name', 'variable_svc_price_modules[module][' + counter + ']');
		});

		done = true;
	}

	if (jQuery('[id^=input_variable_date_]').length == 0 && parseFloat(price) == parseFloat(default_price)) {
		done = true;
	}

	return done;
}




// Save Service Calendar Price
function save_calendar_service_price_module(year = '', month = '', day = '', date = '', module = '', done = 0) {
	var element = jQuery("#price_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	}).find("a");

	element.attr('data-custom', module != '' ? 'module_' + module : 'error');
	element.css('color', '##0995fc');

	var module_date_duplicate = jQuery('[id^=input_variable_module_date_]').filter(function () {
		return this.value == date;
	});

	if (module_date_duplicate.length != 0) {
		var id = module_date_duplicate.attr('id');
		var index = Number(id.split("_")[4]);
		module_date_duplicate.remove();
		jQuery('#input_module_variable_' + index).remove();

		jQuery('[id^=input_variable_module_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_module_date_' + counter);
			jQuery(item).attr('name', 'variable_svc_price_modules[date][' + counter + ']');
		});

		jQuery('[id^=input_module_variable_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_module_variable_' + counter);
			jQuery(item).attr('name', 'variable_svc_price_modules[module][' + counter + ']');
		});

		done++;
	}

	if (module != '' && date != '') {
		var date_element = jQuery('[id^=input_variable_module_date_]').length;
		var module_element = jQuery('[id^=input_module_variable_]').length;
		if (date_element != 0 && module_element != 0 && date_element == module_element) {
			var date_lastid = jQuery('#svc_calendar_module_date_inputs input:last').attr("id");
			var module_lastid = jQuery('#svc_calendar_module_inputs input:last').attr("id");
			var date_nextindex = Number(date_lastid.split("_")[4]) + 1;
			var module_nextindex = Number(module_lastid.split("_")[3]) + 1;
		} else {
			var date_nextindex = module_nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_module_date_' + date_nextindex + '', name: 'variable_svc_price_modules[date][' + date_nextindex + ']', class: 'module-date-inputs', value: date }).appendTo('#svc_calendar_module_date_inputs');
		jQuery('<input>').attr({ type: 'hidden', id: 'input_module_variable_' + module_nextindex + '', name: 'variable_svc_price_modules[module][' + module_nextindex + ']', class: 'module-module-inputs', value: module }).appendTo('#svc_calendar_module_inputs');

		done++;
	}

	var price_date_duplicate = jQuery('[id^=input_variable_date_]').filter(function () {
		return this.value == date;
	});

	if (price_date_duplicate.length != 0) {
		var id = price_date_duplicate.attr('id');
		var index = Number(id.split("_")[3]);
		price_date_duplicate.remove();
		jQuery('#input_variable_price_' + index).remove();

		jQuery('[id^=input_variable_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_date_' + counter);
			jQuery(item).attr('name', 'variable_svc_prices[date][' + counter + ']');
		});

		jQuery('[id^=input_variable_price_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_price_' + counter);
			jQuery(item).attr('name', 'variable_svc_prices[price][' + counter + ']');
		});

		done++;
	}

	return done;
}




// Save Service Calendar Stopsales
function save_calendar_service_stopsales(year = '', month = '', day = '', date = '', stopsales = '', default_stopsales = '', done = false) {
	var element = jQuery("#stopsales_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	}).find("a");
	if (stopsales == '') element.addClass('blankStopsales');
	if (element.hasClass('blankStopsales')) element.css('color', '#000000');
	element.attr('data-custom', stopsales != '' ? parseFloat(stopsales) + 'h' : 'N/A');
	if (parseFloat(stopsales) > default_stopsales && default_stopsales != '') {
		element.css('color', '#fc2e05');
	} else if (parseFloat(stopsales) < default_stopsales && default_stopsales != '') {
		element.css('color', '#12812a');
	} else if (parseFloat(stopsales) == default_stopsales && default_stopsales != '') {
		element.css('color', '#000000');
	} else if ((default_stopsales == '' && !element.hasClass('blankStopsales')) || (default_stopsales != '' && element.hasClass('blankStopsales'))) {
		element.css('color', '#0995FC');
	}

	var date_duplicate = jQuery('[id^=input_variable_stopsales_date_]').filter(function () {
		return this.value == date;
	});

	var date_exclude_duplicate = jQuery('[id^=input_variable_stopsales_exclude_date_]').filter(function () {
		return this.value == date;
	});

	if (date_duplicate.length != 0) {
		var id = date_duplicate.attr('id');
		var index = Number(id.split("_")[4]);
		date_duplicate.remove();
		jQuery('#input_variable_stopsales_hour_' + index).remove();

		jQuery('[id^=input_variable_stopsales_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_stopsales_date_' + counter);
			jQuery(item).attr('name', 'variable_stopsales[date][' + counter + ']');
		});

		jQuery('[id^=input_variable_stopsales_hour_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_stopsales_hour_' + counter);
			jQuery(item).attr('name', 'variable_stopsales[stopsales][' + counter + ']');
		});

		if (done == false) done = true;
	}

	if (date_exclude_duplicate.length != 0) {
		date_exclude_duplicate.remove();

		jQuery('[id^=input_variable_stopsales_exclude_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_stopsales_exclude_date_' + counter);
			jQuery(item).attr('name', 'variable_stopsales[exclude_dates][' + counter + ']');
		});

		if (done == false) done = true;
	}

	if (parseFloat(stopsales) != default_stopsales && stopsales != '') {
		var date_element = jQuery('[id^=input_variable_stopsales_date_]').length;
		var hour_element = jQuery('[id^=input_variable_stopsales_hour_]').length;
		if (date_element != 0 && hour_element != 0 && date_element == hour_element) {
			var date_lastid = jQuery('#stopsales_calendar_date_inputs input:last').attr("id");
			var hour_lastid = jQuery('#stopsales_calendar_hour_inputs input:last').attr("id");
			var date_nextindex = Number(date_lastid.split("_")[4]) + 1;
			var hour_nextindex = Number(hour_lastid.split("_")[4]) + 1;
		} else {
			var date_nextindex = hour_nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_stopsales_date_' + date_nextindex + '', name: 'variable_stopsales[date][' + date_nextindex + ']', class: 'stopsales-date-inputs', value: date }).appendTo('#stopsales_calendar_date_inputs');
		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_stopsales_hour_' + hour_nextindex + '', name: 'variable_stopsales[stopsales][' + hour_nextindex + ']', class: 'stopsales-hour-inputs', value: stopsales }).appendTo('#stopsales_calendar_hour_inputs');

		if (done == false) done = true;
	} else if (default_stopsales != '' && stopsales == '') {
		var total_element = jQuery('[id^=input_variable_stopsales_exclude_date_]').length;
		if (total_element != 0) {
			var lastid = jQuery('#stopsales_calendar_exclude_date_inputs input:last').attr("id");
			var nextindex = Number(lastid.split("_")[5]) + 1;
		} else {
			var nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_stopsales_exclude_date_' + nextindex + '', name: 'variable_stopsales[exclude_dates][' + nextindex + ']', class: 'stopsales-exclude-date-inputs', value: date }).appendTo('#stopsales_calendar_exclude_date_inputs');
		if (done == false) done = true;
	}

	if (jQuery('[id^=input_variable_stopsales_exclude_date_]').length == 0 && parseFloat(stopsales) == default_stopsales) {
		if (done == false) done = true;
	} else if (default_stopsales == '' && stopsales == '') {
		if (done == false) done = true;
	}

	return done;
}




// Save Service Calendar Saleswitch
function save_calendar_service_saleswitch(year = '', month = '', day = '', date = '', saleswitch = '', default_saleswitch = '', done = false) {
	var element = jQuery("#saleswitch_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	}).find("a");
	if (saleswitch == '') element.addClass('blankStopsales');
	if (element.hasClass('blankStopsales')) element.css('color', '#000000');
	element.attr('data-custom', saleswitch != '' ? parseFloat(saleswitch) + 'h' : 'N/A');
	if (parseFloat(saleswitch) > default_saleswitch && default_saleswitch != '') {
		element.css('color', '#fc2e05');
	} else if (parseFloat(saleswitch) < default_saleswitch && default_saleswitch != '') {
		element.css('color', '#12812a');
	} else if (parseFloat(saleswitch) == default_saleswitch && default_saleswitch != '') {
		element.css('color', '#000000');
	} else if ((default_saleswitch == '' && !element.hasClass('blankStopsales')) || (default_saleswitch != '' && element.hasClass('blankStopsales'))) {
		element.css('color', '#0995FC');
	}

	var date_duplicate = jQuery('[id^=input_variable_saleswitch_date_]').filter(function () {
		return this.value == date;
	});

	var date_exclude_duplicate = jQuery('[id^=input_variable_saleswitch_exclude_date_]').filter(function () {
		return this.value == date;
	});

	if (date_duplicate.length != 0) {
		var id = date_duplicate.attr('id');
		var index = Number(id.split("_")[4]);
		date_duplicate.remove();
		jQuery('#input_variable_saleswitch_hour_' + index).remove();

		jQuery('[id^=input_variable_saleswitch_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_saleswitch_date_' + counter);
			jQuery(item).attr('name', 'variable_saleswitch[date][' + counter + ']');
		});

		jQuery('[id^=input_variable_saleswitch_hour_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_saleswitch_hour_' + counter);
			jQuery(item).attr('name', 'variable_saleswitch[saleswitch][' + counter + ']');
		});

		if (done == false) done = true;
	}

	if (date_exclude_duplicate.length != 0) {
		date_exclude_duplicate.remove();

		jQuery('[id^=input_variable_saleswitch_exclude_date_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_variable_saleswitch_exclude_date_' + counter);
			jQuery(item).attr('name', 'variable_saleswitch[exclude_dates][' + counter + ']');
		});

		if (done == false) done = true;
	}

	if (parseFloat(saleswitch) != default_saleswitch && saleswitch != '') {
		var date_element = jQuery('[id^=input_variable_saleswitch_date_]').length;
		var hour_element = jQuery('[id^=input_variable_saleswitch_hour_]').length;
		if (date_element != 0 && hour_element != 0 && date_element == hour_element) {
			var date_lastid = jQuery('#saleswitch_calendar_date_inputs input:last').attr("id");
			var hour_lastid = jQuery('#saleswitch_calendar_hour_inputs input:last').attr("id");
			var date_nextindex = Number(date_lastid.split("_")[4]) + 1;
			var hour_nextindex = Number(hour_lastid.split("_")[4]) + 1;
		} else {
			var date_nextindex = hour_nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_saleswitch_date_' + date_nextindex + '', name: 'variable_saleswitch[date][' + date_nextindex + ']', class: 'saleswitch-date-inputs', value: date }).appendTo('#saleswitch_calendar_date_inputs');
		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_saleswitch_hour_' + hour_nextindex + '', name: 'variable_saleswitch[saleswitch][' + hour_nextindex + ']', class: 'saleswitch-hour-inputs', value: saleswitch }).appendTo('#saleswitch_calendar_hour_inputs');

		if (done == false) done = true;
	} else if (default_saleswitch != '' && saleswitch == '') {
		var total_element = jQuery('[id^=input_variable_saleswitch_exclude_date_]').length;
		if (total_element != 0) {
			var lastid = jQuery('#saleswitch_calendar_exclude_date_inputs input:last').attr("id");
			var nextindex = Number(lastid.split("_")[5]) + 1;
		} else {
			var nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_saleswitch_exclude_date_' + nextindex + '', name: 'variable_saleswitch[exclude_dates][' + nextindex + ']', class: 'saleswitch-exclude-date-inputs', value: date }).appendTo('#saleswitch_calendar_exclude_date_inputs');
		if (done == false) done = true;
	}

	if (jQuery('[id^=input_variable_saleswitch_exclude_date_]').length == 0 && parseFloat(saleswitch) == default_saleswitch) {
		if (done == false) done = true;
	} else if (default_saleswitch == '' && saleswitch == '') {
		if (done == false) done = true;
	}

	return done;
}




// Save Service Calendar Maximum Capacity
function save_calendar_service_max_cap(year = '', month = '', day = '', date = '', capacity = '', default_max_cap = '', done = false) {
	var element = jQuery("#cap_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	}).find("a");
	element.attr('data-custom', capacity != '' ? capacity : 'N/A');
	if (capacity > default_max_cap) {
		element.css('color', '#fc2e05');
	} else if (capacity < default_max_cap) {
		element.css('color', '#12812a');
	} else if (capacity == default_max_cap) {
		element.css('color', '#000000');
	}

	var date_duplicate = jQuery('[id^=input_date_max_cap_]').filter(function () {
		return this.value == date;
	});

	if (date_duplicate.length != 0) {
		var id = date_duplicate.attr('id');
		var index = Number(id.split("_")[3]);
		date_duplicate.remove();
		jQuery('#input_max_cap_' + index).remove();

		jQuery('[id^=input_date_max_cap_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_date_max_cap_' + counter);
			jQuery(item).attr('name', 'variable_max_cap[date][' + counter + ']');
		});

		jQuery('[id^=input_max_cap_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'input_max_cap_' + counter);
			jQuery(item).attr('name', 'variable_max_cap[capacity][' + counter + ']');
		});

		if (done == false) done = true;
	}

	if (capacity != default_max_cap && capacity != '') {
		var date_element = jQuery('[id^=input_date_max_cap_]').length;
		var capacity_element = jQuery('[id^=input_max_cap_]').length;
		if (date_element != 0 && capacity_element != 0 && date_element == capacity_element) {
			var date_lastid = jQuery('#capacity_calendar_date_inputs input:last').attr("id");
			var capacity_lastid = jQuery('#capacity_calendar_cap_inputs input:last').attr("id");
			var date_nextindex = Number(date_lastid.split("_")[4]) + 1;
			var capacity_nextindex = Number(capacity_lastid.split("_")[3]) + 1;
		} else {
			var date_nextindex = capacity_nextindex = 1;
		}

		jQuery('<input>').attr({ type: 'hidden', id: 'input_date_max_cap_' + date_nextindex + '', name: 'variable_max_cap[date][' + date_nextindex + ']', class: 'capacity-date-inputs', value: date }).appendTo('#capacity_calendar_date_inputs');
		jQuery('<input>').attr({ type: 'hidden', id: 'input_max_cap_' + capacity_nextindex + '', name: 'variable_max_cap[capacity][' + capacity_nextindex + ']', class: 'capacity-capacity-inputs', value: capacity }).appendTo('#capacity_calendar_cap_inputs');

		if (done == false) done = true;
	}

	if (jQuery('[id^=input_date_max_cap_]').length == 0 && capacity == default_max_cap) {
		if (done == false) done = true;
	}

	return done;
}




// Save Service Calendar Time Slot
function save_calendar_variable_time_slots(year = '', month = '', day = '', date = '', time_slots_data = {}, slot_nextindex = '', done = false) {
	var element = jQuery("#time_slots_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	}).find("a");

	if (element.attr('data-custom') == 'N/A' || element.attr('data-custom') == 'Default') {
		element.attr('data-custom', time_slots_data != '' ? 'id_' + slot_nextindex : 'N/A');
		if (time_slots_data != '') {
			var date_element = jQuery('[id^=input_variable_time_slot_date_]').length;
			var time_slot_element = jQuery('.slot_data_element').length;
			if (date_element != 0 && time_slot_element != 0 && date_element == time_slot_element) {
				var date_lastid = jQuery('#time_slot_calendar_date_inputs input:last').attr("id");
				var date_nextindex = Number(date_lastid.split("_")[5]) + 1;
				var time_slot_nextindex = slot_nextindex;
			} else {
				var date_nextindex = time_slot_nextindex = 1;
			}

			jQuery('<input>').attr({ type: 'hidden', id: 'input_variable_time_slot_date_' + date_nextindex + '', name: 'variable_time_slots[' + date_nextindex + '][date]', class: 'time-slot-date-inputs', value: date }).appendTo('#time_slot_calendar_date_inputs');
			jQuery('#time_slot_calendar_slot_data').append('<div class="slot_data_element" id="variable_time_slot_data_' + time_slot_nextindex + '" style="display :none;"></div>');
			jQuery('#variable_time_slot_data_' + time_slot_nextindex + '').append(jQuery('#time_slot_value').html());

			if (done == false) done = true;
		}
	} else {
		var time_slot_id = Number(element.attr('data-custom').split('_')[1]);
		jQuery('#variable_time_slot_data_' + time_slot_id + '').html('');
		jQuery('#variable_time_slot_data_' + time_slot_id + '').append(jQuery('#time_slot_value').html());

		if (done == false) done = true;
	}

	element.css('color', '#0752FF');

	if (jQuery('[id^=input_variable_time_slot_date_]').length == 0 && time_slots_data != '') {
		if (done == false) done = true;
	}

	return done;
}




// Remove Service Calendar Time Slot
function remove_time_slot(date) {
	var date_parts = date.split("-");
	var day = date_parts[2].replace(/^0+/, '');
	var month = date_parts[1].replace(/^0+/, '') - 1;
	var year = date_parts[0];

	if (getUrlParameter('id') != '') {
		if (confirm(bm_normal_object.sure_remove_timeslot)) {
			var data = { 'action': 'bm_remove_variable_time_slot', 'id': getUrlParameter('id'), 'date': date, 'nonce': bm_ajax_object.nonce };
			jQuery.post(bm_ajax_object.ajax_url, data, function (response) {

				var jsondata = jQuery.parseJSON(response);
				var dates = jsondata.dates;
				var slot_id = jsondata.slot_ids;
				var date_array = [];
				var slot_ids = [];

				jQuery.map(dates, function (index) { date_array.push(index); });
				jQuery.map(slot_id, function (index) { slot_ids.push(index); });

				jQuery('#total_variable_slots').val(date_array.length);

				if (jsondata.status == true) {
					setTimeout(function () {
						jQuery("#time_slots_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
							var date = jQuery(this).text();
							return /\d/.test(date);
						}).find('a', 'span').html(function (i, html) {
							var day = jQuery(this).data('date');
							var month = jQuery(this).parent().data('month') + 1;
							var year = jQuery(this).parent().data('year');
							var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

							if (date_array.length != 0 && slot_ids.length != 0 && jQuery.inArray(date, date_array) !== -1) {
								jQuery(this).attr('data-custom', 'slot_' + slot_ids[jQuery.inArray(date, date_array)]);
								jQuery(this).css('color', '#0752FF');
							} else {
								jQuery(this).attr('data-custom', 'N/A');
								jQuery(this).css('color', '#000000');
							}
						});
					});
					jQuery('#time_slot_modal').hide();
					jQuery('.time_slot_update_successtext').html(bm_success_object.slot_remove_success);
					jQuery('.time_slot_update_successtext').show();
				} else {
					jQuery('#time_slot_modal').hide();
					jQuery('.time_slot_errortext').html(bm_error_object.server_error);
					jQuery('.time_slot_errortext').show();
				}
			});
		}
	} else {
		if (confirm(bm_normal_object.sure_remove_timeslot)) {
			var date_element = jQuery('[id^=input_variable_time_slot_date_]').filter(function () {
				return this.value == date;
			});

			if (date_element.length != 0) {
				var id = date_element.attr('id');
				var removed_index = Number(id.split("_")[5]);
				date_element.remove();
				jQuery('#variable_time_slot_data_' + removed_index + '').remove();

				jQuery('[id^=input_variable_time_slot_date_]').each(function (id, item) {
					var counter = id + 1;
					jQuery(item).attr('id', 'input_variable_time_slot_date_' + counter);
					jQuery(item).attr('name', 'variable_time_slots[' + counter + '][date]');
				});

				jQuery('#time_slot_calendar_slot_data').find('.slot_data_element').each(function (id, item) {
					var counter = id + 1;
					jQuery(item).attr('id', 'variable_time_slot_data_' + counter);
					jQuery(item).find('input').each(function (id, input) {
						var name = jQuery(input).attr('name');
						var input_id = jQuery(input).attr('id');
						var type = jQuery(input).attr('type');

						if (!name.startsWith('variable_autoselect_time_')) {
							var name_part_2 = name.substring(name.indexOf(']') + 1);
							var id_part_2 = name_part_2.match(/\[(.*?)\]/);

							if (typeof (input_id) != 'undefined') {
								if (input_id.startsWith('variable_from_') || input_id.startsWith('variable_to_') || input_id.startsWith('variable_disable_')) {
									jQuery(input).attr('data-slot', counter);
								}
							}

							jQuery(input).attr('name', 'variable_time_slots[' + counter + ']' + name_part_2);

							if (type != 'hidden') {
								var parent_id = jQuery(input).parent().parent().attr('id').split('_')[3];
								jQuery(input).attr('id', 'variable_' + id_part_2[1] + '_' + parent_id);
							} else if (type == 'hidden' && typeof (input_id) != 'undefined' && input_id.startsWith('variable_auto_time_')) {
								jQuery(input).attr('id', 'variable_auto_time_' + counter);
							} else if (type == 'hidden' && typeof (input_id) != 'undefined' && input_id.startsWith('variable_slot_id_')) {
								jQuery(input).attr('value', counter);
								jQuery(input).attr('id', 'variable_slot_id_' + counter);
							}
						} else {
							jQuery(input).attr('name', 'variable_autoselect_time_' + counter);
							jQuery(input).attr('id', 'variable_autoselect_time_' + counter);
						}
					});
				});

				var element = jQuery("#time_slots_datepicker").find('[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
					return jQuery(this).find('a').text() == day;
				}).find("a");

				element.attr('data-custom', 'Default');
				element.css('color', '#000000');

				jQuery("#time_slots_datepicker").datepicker().find(".ui-datepicker-calendar td").find('a').filter(function () {
					return jQuery(this).attr('data-custom') != 'Default';
				}).each(function (id, item) {
					var counter = id + 1;
					jQuery(item).attr('data-custom', 'id_' + counter);
				});
			}
		}

		jQuery('#time_slot_modal').hide();
		jQuery('.time_slot_update_successtext').html(bm_success_object.slot_remove_success);
		jQuery('.time_slot_update_successtext').show();
	}
}




// Edit Service Calendar Price
function edit_calendar_service_price(values = [], type = '') {
	jQuery('.bm-set_price-spiner').show();
	var price = values['price'] ? values['price'] : '';
	var default_price = values['default_price'] ? values['default_price'] : '';
	var date = '';
	var daysArray = [];

	if (type == 'single') {
		date = values['date'] ? values['date'] : '';
	} else if (type == 'multiple') {
		var from_date = values['from_date'] ? values['from_date'] : '';
		var to_date = values['to_date'] ? values['to_date'] : '';

		if (from_date != '' && to_date != '') {
			daysArray = getDaysArray(from_date, to_date);
		}
	}

	var data = { 'action': type == 'single' ? 'bm_set_serice_price' : 'bm_set_bulk_serice_price', 'data': values, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.bm-set_price-spiner').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;

		if (status == true) {
			if (price != '' && default_price != '') {
				jQuery('#old_default_price').attr('value', default_price);
				jQuery('#default_price').attr('value', default_price);

				if (type == 'single') {
					if (date != '') {
						var day = date.split('-')[2];
						var month = Number(date.split('-')[1]) - 1;
						var year = date.split('-')[0];
						set_value_in_price_calendar(day, month, year, price, default_price, 'price');
					}
				} else if (type == 'multiple') {
					if (daysArray.length != 0) {
						for (var i = 0; i < daysArray.length; i++) {
							date = new Date(daysArray[i]);
							year = date.getFullYear();
							month = date.getMonth();
							day = date.getDate();
							set_value_in_price_calendar(day, month, year, price, default_price, 'price');
						}
					}
				}

				var changeMonth = jQuery("#price_datepicker").datepicker("option", "changeMonth");
				var changeYear = jQuery("#price_datepicker").datepicker("option", "changeYear");

				if (changeMonth == false || changeYear == false) {
					if (changeMonth == false) jQuery("#price_datepicker").datepicker("option", "changeMonth", true);
					if (changeYear == false) jQuery("#price_datepicker").datepicker("option", "changeYear", true);
					bm_get_service_price();
				}

				jQuery('#svc_price_modal').hide();
				jQuery('.price_update_successtext').html(bm_success_object.price_set);
				jQuery('.price_update_successtext').show();
			} else {
				jQuery('#svc_price_modal').hide();
				jQuery('.price_update_errortext').html(bm_error_object.server_error);
				jQuery('.price_update_errortext').show();
			}
		} else {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_errortext').html(bm_error_object.server_error);
			jQuery('.price_update_errortext').show();
		}
	});
}




// Edit Service Calendar Price
function edit_calendar_service_price_module(values = [], type = '') {
	jQuery('.bm-set_price-spiner').show();
	var module = values['module'] ? values['module'] : '';
	var default_price = values['default_price'] ? values['default_price'] : '';
	var date = '';
	var daysArray = [];

	if (type == 'single') {
		date = values['date'] ? values['date'] : '';
	} else if (type == 'multiple') {
		var from_date = values['from_date'] ? values['from_date'] : '';
		var to_date = values['to_date'] ? values['to_date'] : '';

		if (from_date != '' && to_date != '') {
			daysArray = getDaysArray(from_date, to_date);
		}
	}

	var data = { 'action': type == 'single' ? 'bm_set_serice_price_module' : 'bm_set_bulk_serice_price_module', 'data': values, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.bm-set_price-spiner').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;

		if (status == true) {
			if (module != '' && default_price != '') {
				jQuery('#old_default_price').attr('value', default_price);
				jQuery('#default_price').attr('value', default_price);

				if (type == 'single') {
					if (date != '') {
						var day = date.split('-')[2];
						var month = Number(date.split('-')[1]) - 1;
						var year = date.split('-')[0];
						set_value_in_price_calendar(day, month, year, module, '', 'module');
					}
				} else if (type == 'multiple') {
					if (daysArray.length != 0) {
						for (var i = 0; i < daysArray.length; i++) {
							date = new Date(daysArray[i]);
							year = date.getFullYear();
							month = date.getMonth();
							day = date.getDate();
							set_value_in_price_calendar(day, month, year, module, '', 'module');
						}
					}
				}

				var changeMonth = jQuery("#price_datepicker").datepicker("option", "changeMonth");
				var changeYear = jQuery("#price_datepicker").datepicker("option", "changeYear");

				if (changeMonth == false || changeYear == false) {
					if (changeMonth == false) jQuery("#price_datepicker").datepicker("option", "changeMonth", true);
					if (changeYear == false) jQuery("#price_datepicker").datepicker("option", "changeYear", true);
					bm_get_service_price();
				}

				jQuery('#svc_price_modal').hide();
				jQuery('.price_update_successtext').html(bm_success_object.module_set);
				jQuery('.price_update_successtext').show();
			}
		} else {
			jQuery('#svc_price_modal').hide();
			jQuery('.price_update_errortext').html(bm_error_object.server_error);
			jQuery('.price_update_errortext').show();
		}
	});
}




// Set module values in service price calendar
function set_value_in_price_calendar(day, month, year, value, default_value, type) {
	var text = '';
	var element = jQuery("#price_datepicker").datepicker().find('td[data-year="' + year + '"][data-month="' + month + '"]').filter(function () {
		return jQuery(this).find('a').text() == day;
	});

	if (type == 'module') {
		text = value == '' ? 'error' : '#module_' + value;
	} else if (type == 'price') {
		var currency_symbol = bm_normal_object.currency_symbol;
		var currency_position = bm_normal_object.currency_position;
		var price_text = currency_position == 'before' ? currency_symbol + parseFloat(value).toFixed(2) : parseFloat(value).toFixed(2) + currency_symbol;
		text = value == '' ? 'error' : price_text;
	}

	element.find("a").attr('data-custom', text);

	if (type == 'module') {
		element.find('a', 'span').html(function (i, html) {
			jQuery(this).css('color', '#0995fc')
		});
	} else if (type == 'price') {
		if (parseFloat(value) > parseFloat(default_value)) {
			element.find('a', 'span').html(function (i, html) {
				jQuery(this).css('color', '#fc2e05')
			});
		} else if (parseFloat(value) < parseFloat(default_value)) {
			element.find('a', 'span').html(function (i, html) {
				jQuery(this).css('color', '#12812a')
			});
		} else {
			element.find('a', 'span').html(function (i, html) {
				jQuery(this).css('color', '#12812a')
			});
		}
	}
}




// Edit Service Calendar Stopsales
function edit_calendar_service_stopsales(values = [], type = '') {
	jQuery('.bm-set_stopsales-spiner').show();
	var data = { 'action': type == 'single' ? 'bm_set_serice_stopsales' : 'bm_set_bulk_serice_stopsales', 'data': values, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.bm-set_stopsales-spiner').hide();
		if (jQuery.parseJSON(response).status == true) {
			if (jQuery.parseJSON(response).default_stopsales != 0) jQuery('#old_default_stopsales').attr('value', jQuery.parseJSON(response).default_stopsales);
			jQuery('select[name^="default_stopsales"] option:selected').attr("selected", null);
			if (jQuery.parseJSON(response).default_stopsales != 0) jQuery('select[name^="default_stopsales"] option[value="' + jQuery.parseJSON(response).default_stopsales + '"]').attr("selected", "selected");

			setTimeout(function () {
				jQuery("#stopsales_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
					var date = jQuery(this).text();
					return /\d/.test(date);
				}).find('a', 'span').html(function (i, html) {
					var day = jQuery(this).data('date');
					var month = jQuery(this).parent().data('month') + 1;
					var year = jQuery(this).parent().data('year');
					var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

					var jsondata = jQuery.parseJSON(response);
					var stopsales = jsondata.default_stopsales;

					if (jsondata.variable_stopsales != null && jsondata.variable_stopsales != '') {
						if (jsondata.variable_stopsales.stopsales != null && jsondata.variable_stopsales.date != '') {
							var variable_stopsales_obj = jsondata.variable_stopsales.stopsales;
							var variable_date_obj = jsondata.variable_stopsales.date;
							var stopsales_array = Object.keys(variable_stopsales_obj).map(function (key) { return variable_stopsales_obj[key]; });
							var date_array = Object.keys(variable_date_obj).map(function (key) { return variable_date_obj[key]; });

							if (jQuery.inArray(date, date_array) !== -1) {
								stopsales = stopsales_array[jQuery.inArray(date, date_array)];
								if (parseFloat(stopsales) > jsondata.default_stopsales && jsondata.default_stopsales != 0) {
									jQuery(this).css('color', '#fc2e05');
								} else if (parseFloat(stopsales) < jsondata.default_stopsales && jsondata.default_stopsales != 0) {
									jQuery(this).css('color', '#12812a');
								} else if (jsondata.default_stopsales == 0) {
									jQuery(this).css('color', '#0995FC');
								}
							}
						}

						if (jsondata.variable_stopsales.exclude_dates != null) {
							var variable_excluded_date_obj = jsondata.variable_stopsales.exclude_dates;
							var excluded_date_array = Object.keys(variable_excluded_date_obj).map(function (key) { return variable_excluded_date_obj[key]; });
							if (jQuery.inArray(date, excluded_date_array) !== -1) stopsales = 'N/A';
							if (jsondata.default_stopsales != 0 && stopsales == 'N/A') jQuery(this).css('color', '#0995FC');
						}
					}
					if (parseFloat(stopsales) == jsondata.default_stopsales) jQuery(this).attr('style', 'color :#000000');
					jQuery(this).attr('data-custom', stopsales == ' ' || stopsales == 0 || stopsales == 'N/A' ? 'N/A' : parseFloat(stopsales) + 'h');
				});

				var changeMonth = jQuery("#stopsales_datepicker").datepicker("option", "changeMonth");
				var changeYear = jQuery("#stopsales_datepicker").datepicker("option", "changeYear");

				if (changeMonth == false || changeYear == false) {
					if (changeMonth == false) jQuery("#stopsales_datepicker").datepicker("option", "changeMonth", true);
					if (changeYear == false) jQuery("#stopsales_datepicker").datepicker("option", "changeYear", true);
					bm_get_service_stopsales();
				}

				jQuery('#stopsales_modal').hide();
				jQuery('.stopsales_update_successtext').html(bm_success_object.stopsales_set);
				if (jQuery('.stopsales_update_successtext').not(':visible')) jQuery('.stopsales_update_successtext').show();
			});
		} else {
			jQuery('#stopsales_modal').hide();
			jQuery('.stopsales_update_errortext').html(bm_error_object.server_error);
			if (jQuery('.stopsales_update_errortext').not(':visible')) jQuery('.stopsales_update_errortext').show();
		}
	});
}




// Edit Service Calendar Saleswitch
function edit_calendar_service_saleswitch(values = [], type = '') {
	jQuery('.bm-set_saleswitch-spiner').show();
	var data = { 'action': type == 'single' ? 'bm_set_service_saleswitch' : 'bm_set_bulk_service_saleswitch', 'data': values, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.bm-set_saleswitch-spiner').hide();
		if (jQuery.parseJSON(response).status == true) {
			if (jQuery.parseJSON(response).default_saleswitch != 0) jQuery('#old_default_saleswitch').attr('value', jQuery.parseJSON(response).default_saleswitch);
			jQuery('select[name^="default_saleswitch"] option:selected').attr("selected", null);
			if (jQuery.parseJSON(response).default_saleswitch != 0) jQuery('select[name^="default_saleswitch"] option[value="' + jQuery.parseJSON(response).default_saleswitch + '"]').attr("selected", "selected");

			setTimeout(function () {
				jQuery("#saleswitch_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
					var date = jQuery(this).text();
					return /\d/.test(date);
				}).find('a', 'span').html(function (i, html) {
					var day = jQuery(this).data('date');
					var month = jQuery(this).parent().data('month') + 1;
					var year = jQuery(this).parent().data('year');
					var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

					var jsondata = jQuery.parseJSON(response);
					var saleswitch = jsondata.default_saleswitch;

					if (jsondata.variable_saleswitch != null && jsondata.variable_saleswitch != '') {
						if (jsondata.variable_saleswitch.saleswitch != null && jsondata.variable_saleswitch.date != '') {
							var variable_saleswitch_obj = jsondata.variable_saleswitch.saleswitch;
							var variable_date_obj = jsondata.variable_saleswitch.date;
							var saleswitch_array = Object.keys(variable_saleswitch_obj).map(function (key) { return variable_saleswitch_obj[key]; });
							var date_array = Object.keys(variable_date_obj).map(function (key) { return variable_date_obj[key]; });

							if (jQuery.inArray(date, date_array) !== -1) {
								saleswitch = saleswitch_array[jQuery.inArray(date, date_array)];
								if (parseFloat(saleswitch) > jsondata.default_saleswitch && jsondata.default_saleswitch != 0) {
									jQuery(this).css('color', '#fc2e05');
								} else if (parseFloat(saleswitch) < jsondata.default_saleswitch && jsondata.default_saleswitch != 0) {
									jQuery(this).css('color', '#12812a');
								} else if (jsondata.default_saleswitch == 0) {
									jQuery(this).css('color', '#0995FC');
								}
							}
						}

						if (jsondata.variable_saleswitch.exclude_dates != null) {
							var variable_excluded_date_obj = jsondata.variable_saleswitch.exclude_dates;
							var excluded_date_array = Object.keys(variable_excluded_date_obj).map(function (key) { return variable_excluded_date_obj[key]; });
							if (jQuery.inArray(date, excluded_date_array) !== -1) saleswitch = 'N/A';
							if (jsondata.default_saleswitch != 0 && saleswitch == 'N/A') jQuery(this).css('color', '#0995FC');
						}
					}
					if (parseFloat(saleswitch) == jsondata.default_saleswitch) jQuery(this).attr('style', 'color :#000000');
					jQuery(this).attr('data-custom', saleswitch == ' ' || saleswitch == 0 || saleswitch == 'N/A' ? 'N/A' : parseFloat(saleswitch) + 'h');
				});

				var changeMonth = jQuery("#saleswitch_datepicker").datepicker("option", "changeMonth");
				var changeYear = jQuery("#saleswitch_datepicker").datepicker("option", "changeYear");

				if (changeMonth == false || changeYear == false) {
					if (changeMonth == false) jQuery("#saleswitch_datepicker").datepicker("option", "changeMonth", true);
					if (changeYear == false) jQuery("#saleswitch_datepicker").datepicker("option", "changeYear", true);
					bm_get_service_saleswitch();
				}

				jQuery('#saleswitch_modal').hide();
				jQuery('.saleswitch_update_successtext').html(bm_success_object.saleswitch_set);
				if (jQuery('.saleswitch_update_successtext').not(':visible')) jQuery('.saleswitch_update_successtext').show();
			});
		} else {
			jQuery('#saleswitch_modal').hide();
			jQuery('.saleswitch_update_errortext').html(bm_error_object.server_error);
			if (jQuery('.saleswitch_update_errortext').not(':visible')) jQuery('.saleswitch_update_errortext').show();
		}
	});
}




// Edit Service Calendar Maximum Capacity
function edit_calendar_service_max_cap(values = [], type = '') {
	jQuery('.bm-capacity-spiner').show();
	var data = { 'action': type == 'single' ? 'bm_set_serice_max_cap' : 'bm_set_bulk_serice_max_cap', 'data': values, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.bm-capacity-spiner').hide();
		if (jQuery.parseJSON(response).status == true) {
			jQuery('#old_default_max_cap').attr('value', jQuery.parseJSON(response).default_max_cap);
			jQuery('#default_max_cap').attr('value', jQuery.parseJSON(response).default_max_cap);

			setTimeout(function () {
				jQuery("#cap_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
					var date = jQuery(this).text();
					return /\d/.test(date);
				}).find('a', 'span').html(function (i, html) {
					var day = jQuery(this).data('date');
					var month = jQuery(this).parent().data('month') + 1;
					var year = jQuery(this).parent().data('year');
					var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

					var jsondata = jQuery.parseJSON(response);
					var capacity = jsondata.default_max_cap;

					if (jsondata.variable_max_cap != null && jsondata.variable_max_cap != '') {
						var variable_max_cap_obj = jsondata.variable_max_cap.capacity;
						var variable_max_cap_date_obj = jsondata.variable_max_cap.date;
						var capacity_array = Object.keys(variable_max_cap_obj).map(function (key) { return variable_max_cap_obj[key]; });
						var date_array = Object.keys(variable_max_cap_date_obj).map(function (key) { return variable_max_cap_date_obj[key]; });
						if (jQuery.inArray(date, date_array) !== -1) {
							capacity = capacity_array[jQuery.inArray(date, date_array)];
							if (capacity > jsondata.default_max_cap) {
								jQuery(this).css('color', '#fc2e05');
							} else if (capacity < jsondata.default_max_cap) {
								jQuery(this).css('color', '#12812a');
							}
						}
					}
					if (capacity == jsondata.default_max_cap) jQuery(this).attr('style', 'color :#000000');
					jQuery(this).attr('data-custom', capacity == '' ? 'N/A' : capacity);
				});

				var changeMonth = jQuery("#cap_datepicker").datepicker("option", "changeMonth");
				var changeYear = jQuery("#cap_datepicker").datepicker("option", "changeYear");

				if (changeMonth == false || changeYear == false) {
					if (changeMonth == false) jQuery("#cap_datepicker").datepicker("option", "changeMonth", true);
					if (changeYear == false) jQuery("#cap_datepicker").datepicker("option", "changeYear", true);
					bm_get_service_max_cap();
				}

				jQuery('#max_cap_modal').hide();
				jQuery('.capacity_update_successtext').html(bm_success_object.capacity_set);
				if (jQuery('.capacity_update_successtext').not(':visible')) jQuery('.capacity_update_successtext').show();
			});
		} else {
			jQuery('#max_cap_modal').hide();
			jQuery('.capacity_update_errortext').html(bm_error_object.server_error);
			if (jQuery('.capacity_update_errortext').not(':visible')) jQuery('.capacity_update_errortext').show();
		}
	});
}




// Edit Service Calendar Time Slot
function edit_calendar_service_variable_time_slots(values = []) {
	var data = { 'action': 'bm_set_variable_time_slot', 'data': values, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {

		var jsondata = jQuery.parseJSON(response);
		var slot_data = jsondata.variable_slot_data;
		var date_array = [];
		var slot_ids = [];

		jQuery.map(slot_data, function (index) {
			date_array.push(index['date']);
			slot_ids.push(index['slot_id']);
		});

		jQuery('#total_variable_slots').val(date_array.length);
		jQuery('#default_max_cap').attr('value', slot_data.default_max_cap);
		jQuery('#total_time_slots').attr('value', slot_data.total_time_slots);
		jQuery('#old_total_time_slots').attr('value', slot_data.total_time_slots);

		if (jsondata.status == true) {
			setTimeout(function () {
				jQuery("#time_slots_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
					var date = jQuery(this).text();
					return /\d/.test(date);
				}).find('a', 'span').html(function (i, html) {
					var day = jQuery(this).data('date');
					var month = jQuery(this).parent().data('month') + 1;
					var year = jQuery(this).parent().data('year');
					var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

					if (date_array.length != 0 && slot_ids.length != 0 && jQuery.inArray(date, date_array) !== -1) {
						jQuery(this).attr('data-custom', 'slot_' + slot_ids[jQuery.inArray(date, date_array)]);
						jQuery(this).css('color', '#0752FF');
					} else {
						jQuery(this).attr('data-custom', 'N/A');
						jQuery(this).css('color', '#000000');
					}
				});

				var changeMonth = jQuery("#time_slots_datepicker").datepicker("option", "changeMonth");
				var changeYear = jQuery("#time_slots_datepicker").datepicker("option", "changeYear");

				if (changeMonth == false || changeYear == false) {
					if (changeMonth == false) jQuery("#time_slots_datepicker").datepicker("option", "changeMonth", true);
					if (changeYear == false) jQuery("#time_slots_datepicker").datepicker("option", "changeYear", true);
					bm_get_service_time_slots();
				}

				jQuery('#time_slot_modal').hide();
				jQuery('.time_slot_update_successtext').html(bm_success_object.time_slot_set);
				jQuery('.time_slot_update_successtext').show();
			});
		} else {
			jQuery('#time_slot_modal').hide();
			jQuery('.time_slot_errortext').html(bm_error_object.server_error);
			jQuery('.time_slot_errortext').show();
		}
	});
}




// Variable Service Price Ajax on Page Load
function bm_get_service_price() {
	jQuery('.calendar_errortext, .price_update_successtext, .price_update_errortext, .variable_errortext, .bulk_errortext')
		.hide()
		.html(' ');

	var currency_symbol = bm_normal_object.currency_symbol;
	var currency_position = bm_normal_object.currency_position;

	if (getUrlParameter('id') != '') {
		var data = {
			'action': 'bm_get_service_prices',
			'id': getUrlParameter('id'),
			'nonce': bm_ajax_object.nonce
		};

		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status;
			var default_price = jsondata.default_price;
			var variable_price_obj = jsondata.variable_price.price || '';
			var variable_price_date_obj = jsondata.variable_price.date || '';
			var variable_module_obj = jsondata.variable_module.module || '';
			var variable_module_date_obj = jsondata.variable_module.date || '';
			var unavailability = jsondata.unavailability || '';
			var gbl_unavailability = jsondata.gbl_unavlabilty || '';

			var price_array = [];
			var module_array = [];
			var price_date_array = [];
			var module_date_array = [];
			var unavailable_days_array = [];
			var weekdays_array = [];

			if (variable_price_obj && variable_price_date_obj) {
				price_array = Object.values(variable_price_obj);
				price_date_array = Object.values(variable_price_date_obj);
			}

			if (variable_module_obj && variable_module_date_obj) {
				module_array = Object.values(variable_module_obj);
				module_date_array = Object.values(variable_module_date_obj);
			}

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

			if (status === true) {
				setTimeout(function () {
					jQuery("#price_datepicker")
						.datepicker()
						.find(".ui-datepicker-calendar td")
						.filter(function () {
							var date = jQuery(this).text();
							return /\d/.test(date);
						})
						.find('a, span')
						.html(function (i, html) {
							var $this = jQuery(this);
							var day = $this.data('date');
							var month = $this.parent().data('month') + 1;
							var year = $this.parent().data('year');
							var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);
							var week_date = new Date(date);

							if (jQuery.inArray(date, price_date_array) !== -1) {
								var price = price_array[jQuery.inArray(date, price_date_array)];
								if (parseFloat(price) > parseFloat(default_price)) {
									$this.addClass('highValue');
								} else if (parseFloat(price) < parseFloat(default_price)) {
									$this.addClass('lowValue');
								}
								var price_text = currency_position == 'before'
									? currency_symbol + parseFloat(price).toFixed(2)
									: parseFloat(price).toFixed(2) + currency_symbol;
								$this.attr('data-custom', price === '' ? 'N/A' : price_text);
							} else if (jQuery.inArray(date, module_date_array) !== -1) {
								var module = module_array[jQuery.inArray(date, module_date_array)];
								var module_text = '#module_' + module;
								$this.attr('data-custom', module_text);
								$this.addClass('bluetValue');
							} else {
								$this.addClass('brightValue');
								var price_text = currency_position == 'before'
									? currency_symbol + parseFloat(default_price).toFixed(2)
									: parseFloat(default_price).toFixed(2) + currency_symbol;
								$this.attr('data-custom', default_price === '' ? 'N/A' : price_text);
							}

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
								$this.addClass('not_available_for_booking');
							} else {
								$this.addClass('available_for_booking');
							}
						});
				});
			} else {
				jQuery('.calendar_errortext').html(bm_error_object.server_error).show();
			}
		});
	} else {
		jQuery('.calendar_errortext').html(bm_error_object.server_error).show();
	}
}




// Variable Service Stopsales Ajax on Page Load
function bm_get_service_stopsales() {
	jQuery('.stopsales_errortext').hide();
	jQuery('.stopsales_update_successtext').hide();
	jQuery('.stopsales_update_errortext').hide();
	jQuery('.variable_hour_errortext').hide();
	jQuery('.stopsales_errortext').html(' ');
	jQuery('.stopsales_update_successtext').html(' ');
	jQuery('.stopsales_update_errortext').html(' ');
	jQuery('.variable_hour_errortext').html('');

	if (getUrlParameter('id') != '') {
		var data = { 'action': 'bm_get_serice_stopsales', 'id': getUrlParameter('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			if (jQuery.parseJSON(response).status == true) {
				setTimeout(function () {
					jQuery("#stopsales_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
						var date = jQuery(this).text();
						return /\d/.test(date);
					}).find('a', 'span').html(function (i, html) {
						var day = jQuery(this).data('date');
						var month = jQuery(this).parent().data('month') + 1;
						var year = jQuery(this).parent().data('year');
						var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

						var jsondata = jQuery.parseJSON(response);
						var stopsales = jsondata.default_stopsales;

						if (jsondata.variable_stopsales != null && jsondata.variable_stopsales != '') {

							if (jsondata.variable_stopsales.stopsales != null && jsondata.variable_stopsales.date != '') {
								var variable_stopsales_obj = jsondata.variable_stopsales.stopsales;
								var variable_date_obj = jsondata.variable_stopsales.date;
								var stopsales_array = Object.keys(variable_stopsales_obj).map(function (key) { return variable_stopsales_obj[key]; });
								var date_array = Object.keys(variable_date_obj).map(function (key) { return variable_date_obj[key]; });

								if (jQuery.inArray(date, date_array) !== -1) {
									stopsales = stopsales_array[jQuery.inArray(date, date_array)];
									if (parseFloat(stopsales) > parseFloat(jsondata.default_stopsales) && jsondata.default_stopsales != 0) {
										jQuery(this).addClass('highValue');
									} else if (parseFloat(stopsales) < parseFloat(jsondata.default_stopsales) && jsondata.default_stopsales != 0) {
										jQuery(this).addClass('lowValue');
									} else if (jsondata.default_stopsales == 0) {
										jQuery(this).addClass('basevalue');
									} else {
										jQuery(this).addClass('brightValue');
									}
								}
							}

							if (jsondata.variable_stopsales.exclude_dates != null) {
								var variable_excluded_date_obj = jsondata.variable_stopsales.exclude_dates;
								var excluded_date_array = Object.keys(variable_excluded_date_obj).map(function (key) { return variable_excluded_date_obj[key]; });
								if (jQuery.inArray(date, excluded_date_array) !== -1) stopsales = 'N/A';
								if (jsondata.default_stopsales != 0 && stopsales == 'N/A') jQuery(this).addClass('basevalue');
							}
						} else {
							jQuery(this).addClass('brightValue');
						}

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
							jQuery(this).addClass('not_available_for_booking');
						} else {
							jQuery(this).addClass('available_for_booking');
						}

						if (!jQuery(this).hasClass('not_available_for_booking')) {
							jQuery(this).addClass('available_for_booking');
						}
						jQuery(this).attr('data-custom', stopsales == ' ' || stopsales == 0 || stopsales == 'N/A' ? 'N/A' : parseFloat(stopsales) + 'h');
					});
				});
			} else {
				jQuery('.stopsales_errortext').html(bm_error_object.server_error);
				jQuery('.stopsales_errortext').show();
			}
		});
	} else {
		jQuery('.stopsales_errortext').html(bm_error_object.server_error);
		if (jQuery('.stopsales_errortext').not(':visible')) jQuery('.stopsales_errortext').show();
	}
}




// Variable Service Saleswitch Ajax on Page Load
function bm_get_service_saleswitch() {
	if (jQuery('.saleswitch_errortext').is(':visible')) jQuery('.saleswitch_errortext').hide();
	if (jQuery('.saleswitch_update_successtext').is(':visible')) jQuery('.saleswitch_update_successtext').hide();
	if (jQuery('.saleswitch_update_errortext').is(':visible')) jQuery('.saleswitch_update_errortext').hide();
	if (jQuery('.variable_hour_errortext').is(':visible')) jQuery('.variable_hour_errortext').hide();
	jQuery('.saleswitch_errortext').html(' ');
	jQuery('.saleswitch_update_successtext').html(' ');
	jQuery('.saleswitch_update_errortext').html(' ');
	jQuery('.variable_saleswitch_errortext').html('');

	if (getUrlParameter('id') != '') {
		var data = { 'action': 'bm_get_service_saleswitch', 'id': getUrlParameter('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			if (jQuery.parseJSON(response).status == true) {
				setTimeout(function () {
					jQuery("#saleswitch_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
						var date = jQuery(this).text();
						return /\d/.test(date);
					}).find('a', 'span').html(function (i, html) {
						var day = jQuery(this).data('date');
						var month = jQuery(this).parent().data('month') + 1;
						var year = jQuery(this).parent().data('year');
						var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

						var jsondata = jQuery.parseJSON(response);
						var saleswitch = jsondata.default_saleswitch;

						if (jsondata.variable_saleswitch != null && jsondata.variable_saleswitch != '') {

							if (jsondata.variable_saleswitch.saleswitch != null && jsondata.variable_saleswitch.date != '') {
								var variable_saleswitch_obj = jsondata.variable_saleswitch.saleswitch;
								var variable_date_obj = jsondata.variable_saleswitch.date;
								var saleswitch_array = Object.keys(variable_saleswitch_obj).map(function (key) { return variable_saleswitch_obj[key]; });
								var date_array = Object.keys(variable_date_obj).map(function (key) { return variable_date_obj[key]; });

								if (jQuery.inArray(date, date_array) !== -1) {
									saleswitch = saleswitch_array[jQuery.inArray(date, date_array)];
									if (parseFloat(saleswitch) > parseFloat(jsondata.default_saleswitch) && jsondata.default_saleswitch != 0) {
										jQuery(this).addClass('highValue');
									} else if (parseFloat(saleswitch) < parseFloat(jsondata.default_saleswitch) && jsondata.default_saleswitch != 0) {
										jQuery(this).addClass('lowValue');
									} else if (jsondata.default_saleswitch == 0) {
										jQuery(this).addClass('basevalue');
									} else {
										jQuery(this).addClass('brightValue');
									}
								}
							}

							if (jsondata.variable_saleswitch.exclude_dates != null) {
								var variable_excluded_date_obj = jsondata.variable_saleswitch.exclude_dates;
								var excluded_date_array = Object.keys(variable_excluded_date_obj).map(function (key) { return variable_excluded_date_obj[key]; });
								if (jQuery.inArray(date, excluded_date_array) !== -1) saleswitch = 'N/A';
								if (jsondata.default_saleswitch != 0 && saleswitch == 'N/A') jQuery(this).addClass('basevalue');
							}
						} else {
							jQuery(this).addClass('brightValue');
						}

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
							jQuery(this).addClass('not_available_for_booking');
						} else {
							jQuery(this).addClass('available_for_booking');
						}

						if (!jQuery(this).hasClass('not_available_for_booking')) {
							jQuery(this).addClass('available_for_booking');
						}
						jQuery(this).attr('data-custom', saleswitch == ' ' || saleswitch == 0 || saleswitch == 'N/A' ? 'N/A' : parseFloat(saleswitch) + 'h');
					});
				});
			} else {
				jQuery('.saleswitch_errortext').html(bm_error_object.server_error);
				if (jQuery('.saleswitch_errortext').not(':visible')) jQuery('.saleswitch_errortext').show();
			}
		});
	} else {
		jQuery('.saleswitch_errortext').html(bm_error_object.server_error);
		if (jQuery('.saleswitch_errortext').not(':visible')) jQuery('.saleswitch_errortext').show();
	}
}




// Maximum Service Capacity Ajax on Page Load
function bm_get_service_max_cap() {
	jQuery('.capacity_calendar_errortext').hide();
	jQuery('.capacity_update_successtext').hide();
	jQuery('.capacity_update_errortext').hide();
	jQuery('.max_cap_errortext').hide();
	jQuery('.bulk_cap_errortext').hide();

	jQuery('.capacity_calendar_errortext').html(' ');
	jQuery('.capacity_update_successtext').html(' ');
	jQuery('.capacity_update_errortext').html(' ');
	jQuery('.max_cap_errortext').html('');
	jQuery('.bulk_cap_errortext').html('');

	if (getUrlParameter('id') != '') {
		var data = { 'action': 'bm_get_service_max_cap', 'id': getUrlParameter('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			if (jQuery.parseJSON(response).status == true) {
				setTimeout(function () {
					jQuery("#cap_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
						var date = jQuery(this).text();
						return /\d/.test(date);
					}).find('a', 'span').html(function (i, html) {
						var day = jQuery(this).data('date');
						var month = jQuery(this).parent().data('month') + 1;
						var year = jQuery(this).parent().data('year');
						var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

						var jsondata = jQuery.parseJSON(response);
						var capacity = jsondata.default_max_cap;

						if (jsondata.variable_max_cap != null && jsondata.variable_max_cap != '') {
							var variable_max_cap_obj = jsondata.variable_max_cap.capacity;
							var variable_max_cap_date_obj = jsondata.variable_max_cap.date;
							var capacity_array = Object.keys(variable_max_cap_obj).map(function (key) { return variable_max_cap_obj[key]; });
							var date_array = Object.keys(variable_max_cap_date_obj).map(function (key) { return variable_max_cap_date_obj[key]; });
							if (jQuery.inArray(date, date_array) !== -1) {
								capacity = capacity_array[jQuery.inArray(date, date_array)];
								if (parseInt(capacity) > parseInt(jsondata.default_max_cap)) {
									jQuery(this).addClass('highValue');
								} else if (parseInt(capacity) < parseInt(jsondata.default_max_cap)) {
									jQuery(this).addClass('lowValue');
								} else {
									jQuery(this).addClass('brightValue');
								}
							}
						} else {
							jQuery(this).addClass('brightValue');
						}

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
							jQuery(this).addClass('not_available_for_booking');
						} else {
							jQuery(this).addClass('available_for_booking');
						}

						if (!jQuery(this).hasClass('not_available_for_booking')) jQuery(this).addClass('available_for_booking');
						jQuery(this).attr('data-custom', capacity == '' ? 'N/A' : capacity);
					});
				});
			} else {
				jQuery('.capacity_calendar_errortext').html(bm_error_object.server_error);
				if (jQuery('.capacity_calendar_errortext').not(':visible')) jQuery('.capacity_calendar_errortext').show();
			}
		});
	} else {
		jQuery('.capacity_calendar_errortext').html(bm_error_object.server_error);
		if (jQuery('.capacity_calendar_errortext').not(':visible')) jQuery('.capacity_calendar_errortext').show();
	}
}




// Variable Service Price Ajax on Page Load
function bm_get_service_time_slots() {
	jQuery('.time_slot_calendar_errortext').hide();
	jQuery('.time_slot_update_successtext').hide();
	jQuery('.time_slot_update_errortext').hide();

	jQuery('.time_slot_calendar_errortext').html(' ');
	jQuery('.time_slot_update_successtext').html(' ');
	jQuery('.time_slot_update_errortext').html(' ');

	if (getUrlParameter('id') != '') {
		var data = { 'action': 'bm_get_service_time_slots', 'id': getUrlParameter('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var dates = jsondata.dates;
			var slot_id = jsondata.slot_ids;
			var date_array = [];
			var slot_ids = [];

			jQuery.map(dates, function (index) { date_array.push(index); });
			jQuery.map(slot_id, function (index) { slot_ids.push(index); });

			jQuery('#total_variable_slots').val(date_array.length);

			if (jsondata.status == true) {
				setTimeout(function () {
					jQuery("#time_slots_datepicker").datepicker().find(".ui-datepicker-calendar td").filter(function () {
						var date = jQuery(this).text();
						return /\d/.test(date);
					}).find('a', 'span').html(function (i, html) {
						var day = jQuery(this).data('date');
						var month = jQuery(this).parent().data('month') + 1;
						var year = jQuery(this).parent().data('year');
						var date = year + "-" + padWithZeros(month) + "-" + padWithZeros(day);

						if (date_array.length != 0 && slot_ids.length != 0 && jQuery.inArray(date, date_array) !== -1) {
							jQuery(this).attr('data-custom', 'slot_' + slot_ids[jQuery.inArray(date, date_array)]);
							jQuery(this).css('color', '#0752FF');
						} else {
							jQuery(this).attr('data-custom', 'N/A');
							jQuery(this).addClass('brightValue');
						}

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
							jQuery(this).addClass('not_available_for_booking');
						} else {
							jQuery(this).addClass('available_for_booking');
						}

						if (!jQuery(this).hasClass('not_available_for_booking')) {
							jQuery(this).addClass('available_for_booking');
						}
					});
				});
			} else {
				jQuery('.time_slot_calendar_errortext').html(bm_error_object.server_error);
				jQuery('.time_slot_calendar_errortext').show();
			}
		});
	} else {
		jQuery('.time_slot_calendar_errortext').html(bm_error_object.server_error);
		jQuery('.time_slot_calendar_errortext').show();
	}
}




// Show To Service Date In Bulk Price/Stopsales Change
function showToDate(type = '') {
	if (type == 'price') {
		var date_from = jQuery('#from_bulk_price_change');
		var date_to = jQuery('#to_bulk_price_change');
	} else if (type == 'stopsales') {
		var date_from = jQuery('#from_bulk_stopsales_change');
		var date_to = jQuery('#to_bulk_stopsales_change');
	} else if (type == 'saleswitch') {
		var date_from = jQuery('#from_bulk_saleswitch_change');
		var date_to = jQuery('#to_bulk_saleswitch_change');
	} else if (type == 'capacity') {
		var date_from = jQuery('#from_bulk_cap_change');
		var date_to = jQuery('#to_bulk_cap_change');
	}
	date_to.val('');
	date_to.attr('min', date_from.val());

	if (date_from.val() != '') {
		if (date_to.prop('readonly')) {
			date_to.prop('readonly', false);
		}
	} else {
		date_to.prop('readonly', true);
		date_to.val('');
	}
}




// Get All Dates in Range
function getDaysArray(start, end) {
	for (var arr = [], dt = new Date(start); dt <= new Date(end); dt.setDate(dt.getDate() + 1)) {
		arr.push(new Date(dt));
	}
	return arr;
};



// Field Tabs
function fieldTabs(evt, fieldSection) {
	jQuery(".field_successtext").html('');
	jQuery(".field_errortext").html('');
	jQuery(".field_successtext").hide();
	jQuery(".field_errortext").hide();
	jQuery('#field_settings').html('');
	jQuery('#field_settings').html('<p style="text-align: center;">' + bm_normal_object.choose_field + '</p>');
	if (jQuery('.field_content').length != 0) {
		jQuery('.field_content').each(function () {
			jQuery(this).remove();
		});
	}

	var i, tabcontent, tablinks;

	// Tab Switch
	tabcontent = document.getElementsByClassName("field_tabcontent");
	for (i = 0; i < tabcontent.length; i++) {
		tabcontent[i].style.display = "none";
	}

	tablinks = document.getElementsByClassName("field_tablinks");
	for (i = 0; i < tablinks.length; i++) {
		tablinks[i].className = tablinks[i].className.replace(" active", "");
	}

	document.getElementById(fieldSection).style.display = "flow-root";
	evt.currentTarget.className += " active";
}




// get fieldkey and order
function get_fieldkey_and_order(type) {
	jQuery(".field_errortext").html('');
	jQuery(".field_errortext").hide();

	var data = { 'action': 'bm_get_fieldkey_and_order', 'type': type, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var data = jQuery.parseJSON(response);
		if (data.length != 0) {
			var type = data.type;
			var ordering = data.ordering;
			var field_key = data.field_key;
			var primary_mail_key = data.primary_mail_key;
			add_bm_field(type, ordering, field_key, primary_mail_key);
		} else {
			jQuery('.field_errortext').html(bm_error_object.server_error);
			jQuery('.field_errortext').show();
		}
	});
}




// Show/Hide Field Content
function add_bm_field(type, ordering, field_key, primary_mail_key) {
	jQuery(".field_successtext").html('');
	jQuery(".field_errortext").html('');
	jQuery(".field_successtext").hide();
	jQuery(".field_errortext").hide();

	var total_element = jQuery(".existing_field_content").length;
	if (total_element != 0) {
		var lastid = jQuery(".existing_field_content:last").attr("id");
		var position = jQuery(".existing_field_content:last").data("position") + 1;
		var nextindex = Number(lastid.split("_")[1]) + 1;
	} else {
		var nextindex = 1;
		var position = 1;
	}

	var crossSign = "✕";
	var fieldBox = "<div class='field_content' data-type=" + type + " id='div_" + nextindex + "' data-order=" + ordering + " data-position=" + position + ">" +
		"<p class='field_text style='float: left;'>" + type.charAt(0).toUpperCase() + type.slice(1).toLowerCase() + " Field</p>" +
		"<div class='field_content_buttons'>" +
		"<button type='button' id='' class='remove_field' title='" + bm_normal_object.remove + "'>" + crossSign + "</button>" +
		"</div>" +
		"</div>";

	jQuery(".content_body").append(fieldBox);
	showFieldSettings(type, field_key, ordering, position, primary_mail_key);
}




// Change Maximum Capacity on Minimum Capacity Change
function changeFieldMaxCap($this) {

	var type = jQuery($this).attr('type') == 'datetime-local' ? 'datetime' : jQuery($this).attr('type');

	if (jQuery($this).attr('name') == 'field_options[max_length]') {
		var min_element = jQuery('input[name="field_options[min_length]"]');
		var max_length = jQuery($this).val();

		if (min_element.val() != '' && max_length < min_element.val()) {
			jQuery($this).val(getMinLength(type));
			return false;
		}
	} else if (jQuery($this).attr('name') == 'field_options[min_length]') {
		var max_element = jQuery('input[name="field_options[max_length]"]');
		var min_length = jQuery($this).val();

		if (max_element.val() != '' && min_length <= max_element.val()) {
			max_element.attr('min', min_length);
		} else {
			jQuery($this).val(getMinLength(type));
			max_element.attr('min', getMinLength(type));
			return false;
		}
	}
}




//get Minimum Length for types of fields
function getMinLength(type, settings = null) {

	var minlength;

	switch (type) {

		case 'date':
			var date = new Date(jQuery.now());
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : date.getFullYear() + "-" + padWithZeros((date.getMonth() + 1)) + "-" + padWithZeros(date.getDate());
			break;

		case 'time':
			var date = new Date(jQuery.now());
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : padWithZeros(date.getHours()) + ":" + padWithZeros(date.getMinutes());
			break;

		case 'datetime':
			var date = new Date(jQuery.now());
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : date.getFullYear() + "-" + padWithZeros((date.getMonth() + 1)) + "-" + padWithZeros(date.getDate()) + " " + padWithZeros(date.getHours()) + ":" + padWithZeros(date.getMinutes());
			break;

		case 'month':
			var date = new Date(jQuery.now());
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : date.getFullYear() + "-" + padWithZeros(date.getMonth());
			break;

		case 'week':
			var date = new Date(jQuery.now());
			var weekNumber = padWithZeros((new Date()).getWeek());
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : date.getFullYear() + "-W" + weekNumber;
			break;

		case 'number':
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : 1;
			break;

		case 'range':
			minlength = settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : 1;
			break;

		default:
			minlength = 1;
			break;
	}

	return minlength;
}




//Get week
Date.prototype.getWeek = function () {
	var onejan = new Date(this.getFullYear(), 0, 1);
	return Math.ceil((((this - onejan) / 86400000) + onejan.getDay() + 1) / 7);
}




//HTML templates for rendering field settings
function showFieldSettings(type, field_key, ordering, field_position, primary_mail_key, settings = null) {

	var hasRequired, hasChoices, hasMultiple, hasMaxlength, hasMinlength, hasVisibility, hasDefaultvalue, hasAutocomplete, hasRows, hasColumns, hasIntlCode;

	switch (type) {
		case 'text':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'email':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = true;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'url':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'password':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'select':
			hasRequired = true;
			hasChoices = true;
			hasMultiple = true;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = false;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'radio':
			hasRequired = true;
			hasChoices = true;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = false;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'textarea':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = true;
			hasColumns = true;
			hasIntlCode = false;
			break;

		case 'checkbox':
			hasRequired = true;
			hasChoices = true;
			hasMultiple = true;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = false;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'date':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'time':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'datetime':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'month':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'week':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'number':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'file':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = true;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'button':
			hasRequired = false;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = false;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'submit':
			hasRequired = false;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = false;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'tel':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = true;
			break;

		case 'hidden':
			hasRequired = false;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = false;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'color':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'range':
			hasRequired = true;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = true;
			hasMinlength = true;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'reset':
			hasRequired = false;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		case 'search':
			hasRequired = false;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = true;
			hasDefaultvalue = true;
			hasAutocomplete = true;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;

		default:
			hasRequired = false;
			hasChoices = false;
			hasMultiple = false;
			hasMaxlength = false;
			hasMinlength = false;
			hasVisibility = false;
			hasDefaultvalue = false;
			hasAutocomplete = false;
			hasRows = false;
			hasColumns = false;
			hasIntlCode = false;
			break;
	}

	var isAutocomplete = "<div><span scope='row'><label class='setting_label'>" + bm_normal_object.automcomplete + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='field_options[autocomplete]' type='checkbox' id='autocomplete' " + (settings != null && settings.field_options.autocomplete == 1 ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' autocomplete='off'>" +
		"<label for='autocomplete'></label></span></div>";

	var fieldLabel = "<div scope='row'><label class='setting_label'>" + bm_normal_object.field_label + "<strong class='required_asterisk'> *</strong></label></div>" +
		"<div class='setting_input bm_field_required'>" +
		"<input name='field_label' type='text' id='field_label' value='" + (settings != null && settings.common.field_label != '' ? settings.common.field_label : '') + "' class='regular-text' autocomplete='off'>" +
		"<div class='field_validate_errortext'></div></div>";

	var fieldName = "<div scope='row'><label class='setting_label'>" + bm_normal_object.field_name_attribute + "<strong class='required_asterisk'> *</strong></label></div>" +
		"<div class='setting_input bm_field_required'>" +
		"<input name='field_name' type='text' id='field_name' value='" + (settings != null && settings.common.field_name != '' ? settings.common.field_name : '') + "' " + (settings != null && settings.field_options.is_default == 1 ? 'readonly' : '') + " class='regular-text' autocomplete='off'>" +
		"<div class='field_validate_errortext'></div></div>";

	var fieldDesc = "<div scope='row'><label class='setting_label'>" + bm_normal_object.field_description + "</label></div>" +
		"<div class='setting_input'>" +
		"<textarea name='field_desc' id='field_desc' cols='5' rows='5' class='regular-text' tabindex='4'>" + (settings != null && settings.common.field_desc != '' ? settings.common.field_desc : '') + "</textarea>" +
		"</div>";

	var placeholder = "<div scope='row'><label class='setting_label'>" + bm_normal_object.placeholder + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_options[placeholder]' type='text' id='placeholder' value='" + (settings != null && settings.field_options.placeholder != '' ? settings.field_options.placeholder : '') + "' class='regular-text' autocomplete='off'>" +
		"</div>";

	var customClass = "<div scope='row'><label class='setting_label'>" + bm_normal_object.custom_class + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_options[custom_class]' type='text' id='custom_class' value='" + (settings != null && settings.field_options.custom_class != '' ? settings.field_options.custom_class : '') + "' class='regular-text' autocomplete='off'>" +
		"</div>";

	var fieldWidth = "<div scope='row'><label class='setting_label'>" + bm_normal_object.field_width + "</label></div>" +
		"<div class='setting_input'>" +
		"<select name='field_options[field_width]'id='field_width' class='regular-text' autocomplete='off'>" +
		"<option value='half' " + (settings != null && settings.field_options.field_width == 'half' ? 'selected' : '') + ">Half</option>" +
		"<option value='full' " + (settings != null && settings.field_options.field_width == 'full' ? 'selected' : '') + ">Full</option>" +
		"</select>" +
		"</div>";

	var isRequired = "<div><span scope='row'><label class='setting_label'>" + bm_error_object.required + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='is_required' type='checkbox' id='is_required' " + (settings != null && settings.common.is_required == 1 ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' autocomplete='off'>" +
		"<label for='is_required'></label></span></div>";

	var isMainEmail = "<div class='email_div'><span scope='row'><label class='setting_label'>" + bm_normal_object.set_as_primary_email + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='field_options[is_main_email]' type='checkbox' id='is_main_email' " + (settings != null && settings.field_options.is_main_email == 1 ? 'checked' : '') + " " + (settings == null && primary_mail_key == '' ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' data-key=" + field_key + " onchange='check_if_any_primary_email(this)' autocomplete='off'>" +
		"<label for='is_main_email'></label></span><div class='field_validate_errortext'></div><div class='field_validate_successtext'></div>";

	var isMultiple = "<div><span scope='row'><label class='setting_label'>" + bm_normal_object.multiple + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='field_options[is_multiple]' type='checkbox' id='is_multiple' " + (settings != null && settings.field_options.is_multiple == 1 ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' autocomplete='off'>" +
		"<label for='is_multiple'></label></span></div>";

	var isEditable = "<div><span scope='row'><label class='setting_label'>" + bm_normal_object.editable + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='is_editable' type='checkbox' id='is_editable' " + ((settings != null && settings.common.is_editable == 1) || settings == null ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' autocomplete='off'>" +
		"<label for='is_editable'></label></span></div>";

	var isVisible = "<div><span scope='row'><label class='setting_label'>" + bm_normal_object.visible + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='field_options[is_visible]' type='checkbox' id='is_visible' " + ((settings != null && settings.field_options.is_visible == 1) || settings == null ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' autocomplete='off'>" +
		"<label for='is_visible'></label></span></div>";

	var fieldOptions = "<div class='options_box'><div scope='row'><label class='setting_label'>" + bm_normal_object.default_options + "</label>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<span class='button' data-type='" + type + "' onclick='addMoreOption(this)'>" + bm_normal_object.add_option + "</span></div></div><br>";

	if (settings != null && "options" in settings.field_options) {
		if (type == 'radio' || type == 'checkbox') {
			var crossSign = "✕";
			if (settings?.field_options?.options?.values && Object.keys(settings.field_options.options.values).length != 0) {
				for (var i = 0; i < Object.keys(settings.field_options.options.values).length; i++) {
					fieldOptions += "<div class='option_element setting_input bm_field_required' id='default_option_" + i + "'>" +
						"<input name='field_options[options][values][" + i + "]' type='text' id='option_" + i + "' style='width: 200px;' value='" + (settings != null && settings.field_options.options.values[i] != '' ? settings.field_options.options.values[i] : '') + "' autocomplete='off'>&nbsp;Selected ?&nbsp;&nbsp;<span>" +
						"<input name='field_options[options][selected][" + i + "]' type='hidden' value='0' id='selected_" + i + "'>" +
						"<input name='field_options[options][selected][" + i + "]' type='checkbox' value='1' " + (settings.field_options.options.selected[i] == 1 ? 'checked' : '') + " id='selected_" + i + "' class='regular-text' autocomplete='off'></span>" +
						"&nbsp;<span id='removeoption_" + i + "' data-type='" + type + "' style='color:red;cursor:pointer;' class='remove_option' title='" + bm_normal_object.remove + "' autocomplete='off'>" + crossSign + "</span>" +
						"<div class='field_validate_errortext'></div></div><br>";
				}
			}
		} else if (type == 'select') {
			var crossSign = "✕";
			if (settings?.field_options?.options?.values && Object.keys(settings.field_options.options.values).length != 0) {
				for (var i = 0; i < Object.keys(settings.field_options.options.values).length; i++) {
					fieldOptions += "<div class='option_element setting_input bm_field_required' id='default_option_" + i + "'>" +
						"<input name='field_options[options][values][" + i + "]' type='text' id='option_" + i + "' style='width: 130px;' value='" + (settings != null && settings.field_options.options.values[i] != '' ? settings.field_options.options.values[i] : '') + "' autocomplete='off'>&nbsp;" +
						"<input name='field_options[options][keys][" + i + "]' type='text' id='key_" + i + "' style='width: 130px;' value='" + (settings != null && settings.field_options.options.keys[i] != '' ? settings.field_options.options.keys[i] : '') + "' autocomplete='off'>&nbsp;" +
						bm_normal_object.selected + " ?&nbsp;&nbsp;<span>" +
						"<input name='field_options[options][selected][" + i + "]' type='hidden' value='0' id='selected_" + i + "'>" +
						"<input name='field_options[options][selected][" + i + "]' type='checkbox' value='1' " + (settings.field_options.options.selected[i] == 1 ? 'checked' : '') + " id='selected_" + i + "' class='regular-text' autocomplete='off'></span>" +
						"&nbsp;<span id='removeoption_" + i + "' data-type='" + type + "' style='color:red;cursor:pointer;' class='remove_option' title='" + bm_normal_object.remove + "' autocomplete='off'>" + crossSign + "</span>" +
						"<div class='field_validate_errortext'></div></div><br>";
				}
			}
		}
	}

	var minLength = "<div scope='row'><label class='setting_label'>" + bm_normal_object.minimum_length + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_options[min_length]' type='" + (type == 'datetime' ? 'datetime-local' : type) + "' id='min_length' min='" + (settings != null && settings.field_options.min_length != '' ? getMinLength(type, settings) : getMinLength(type)) + "' value='" + (settings != null && settings.field_options.min_length != '' ? settings.field_options.min_length : getMinLength(type)) + "' onchange='changeFieldMaxCap(this)' class='regular-text' autocomplete='off'>" +
		"</div>";

	var maxLength = "<div scope='row'><label class='setting_label'>" + bm_normal_object.maximum_length + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_options[max_length]' type='" + (type == 'datetime' ? 'datetime-local' : type) + "' id='max_length' min='" + (settings != null && settings.field_options.min_length != '' ? getMinLength(type, settings) : getMinLength(type)) + "' value='" + (settings != null && settings.field_options.max_length != '' ? settings.field_options.max_length : getMinLength(type)) + "' onchange='changeFieldMaxCap(this)' class='regular-text' autocomplete='off'>" +
		"</div>";

	var isRows = "<div scope='row'><label class='setting_label'>" + bm_normal_object.rows + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_options[rows]' type='number' id='rows' min='1' value='" + (settings != null && settings.field_options.rows != '' ? settings.field_options.rows : '1') + "' class='regular-text' autocomplete='off'>" +
		"</div>";

	var isColumns = "<div scope='row'><label class='setting_label'>" + bm_normal_object.columns + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_options[columns]' type='number' id='columns' min='1' value='" + (settings != null && settings.field_options.columns != '' ? settings.field_options.columns : '1') + "' class='regular-text' autocomplete='off'>" +
		"</div>";

	var fieldKey = "<div scope='row'><label class='setting_label'>" + bm_normal_object.field_key + "</label></div>" +
		"<div class='setting_input'>" +
		"<input name='field_key' type='text' id='field_key' data-id='" + (settings != null && typeof (settings.common.id) != "undefined" ? settings.common.id : '') + "' value='" + field_key + "' oninput='checkFieldKey(this)' class='regular-text' autocomplete='off'>" +
		"<div class='field_validate_errortext'></div></div>";

	var isIntlCode = "<div><span scope='row'><label class='setting_label'>" + bm_normal_object.show_intl_codes + " ?</label></span>" +
		"<span class='setting_input bm-checkbox-td'>" +
		"&nbsp;&nbsp;&nbsp;&nbsp;<input name='field_options[show_intl_code]' type='checkbox' id='show_intl_code' " + ((settings != null && settings.field_options.show_intl_code == 1) || settings == null ? 'checked' : '') + " class='regular-text auto-checkbox bm_toggle' autocomplete='off'>" +
		"<label for='show_intl_code'></label></span></div>";

	var linkWooCommerce = "<div scope='row'><label class='setting_label'>" + bm_normal_object.link_woo_field + "<strong class='required_asterisk'> *</strong></label></div>" +
		"<div class='setting_input bm_field_required'>" +
		"<select name='woocommerce_field' id='woocommerce_field' class='regular-text' autocomplete='off'>" +
		"<option value=''>Select woocommerce field</option>" +
		"<option value='non_woocomerce' " + (settings != null && settings.common.woocommerce_field == 'non_woocomerce' ? 'selected' : '') + ">" + bm_normal_object.non_woocomerce + "</option>" +
		"<option value='billing_first_name' " + (settings != null && settings.common.woocommerce_field == 'billing_first_name' ? 'selected' : '') + ">" + bm_normal_object.billing_first_name + "</option>" +
		"<option value='billing_last_name' " + (settings != null && settings.common.woocommerce_field == 'billing_last_name' ? 'selected' : '') + ">" + bm_normal_object.billing_last_name + "</option>" +
		"<option value='billing_company' " + (settings != null && settings.common.woocommerce_field == 'billing_company' ? 'selected' : '') + ">" + bm_normal_object.billing_company + "</option>" +
		"<option value='billing_country' " + (settings != null && settings.common.woocommerce_field == 'billing_country' ? 'selected' : '') + ">" + bm_normal_object.billing_country + "</option>" +
		"<option value='billing_address_1' " + (settings != null && settings.common.woocommerce_field == 'billing_address_1' ? 'selected' : '') + ">" + bm_normal_object.billing_address_1 + "</option>" +
		"<option value='billing_address_2' " + (settings != null && settings.common.woocommerce_field == 'billing_address_2' ? 'selected' : '') + ">" + bm_normal_object.billing_address_2 + "</option>" +
		"<option value='billing_city' " + (settings != null && settings.common.woocommerce_field == 'billing_city' ? 'selected' : '') + ">" + bm_normal_object.billing_city + "</option>" +
		"<option value='billing_state' " + (settings != null && settings.common.woocommerce_field == 'billing_state' ? 'selected' : '') + ">" + bm_normal_object.billing_state + "</option>" +
		"<option value='billing_postcode' " + (settings != null && settings.common.woocommerce_field == 'billing_postcode' ? 'selected' : '') + ">" + bm_normal_object.billing_postcode + "</option>" +
		"<option value='billing_phone' " + (settings != null && settings.common.woocommerce_field == 'billing_phone' ? 'selected' : '') + ">" + bm_normal_object.billing_phone + "</option>" +
		"<option value='billing_email' " + (settings != null && settings.common.woocommerce_field == 'billing_email' ? 'selected' : '') + ">" + bm_normal_object.billing_email + "</option>" +
		"<option value='shipping_first_name' " + (settings != null && settings.common.woocommerce_field == 'shipping_first_name' ? 'selected' : '') + ">" + bm_normal_object.shipping_first_name + "</option>" +
		"<option value='shipping_last_name' " + (settings != null && settings.common.woocommerce_field == 'shipping_last_name' ? 'selected' : '') + ">" + bm_normal_object.shipping_last_name + "</option>" +
		"<option value='shipping_company' " + (settings != null && settings.common.woocommerce_field == 'shipping_company' ? 'selected' : '') + ">" + bm_normal_object.shipping_company + "</option>" +
		"<option value='shipping_address_1' " + (settings != null && settings.common.woocommerce_field == 'shipping_address_1' ? 'selected' : '') + ">" + bm_normal_object.shipping_address_1 + "</option>" +
		"<option value='shipping_address_2' " + (settings != null && settings.common.woocommerce_field == 'shipping_address_2' ? 'selected' : '') + ">" + bm_normal_object.shipping_address_2 + "</option>" +
		"<option value='shipping_city' " + (settings != null && settings.common.woocommerce_field == 'shipping_city' ? 'selected' : '') + ">" + bm_normal_object.shipping_city + "</option>" +
		"<option value='shipping_state' " + (settings != null && settings.common.woocommerce_field == 'shipping_state' ? 'selected' : '') + ">" + bm_normal_object.shipping_state + "</option>" +
		"<option value='shipping_postcode' " + (settings != null && settings.common.woocommerce_field == 'shipping_postcode' ? 'selected' : '') + ">" + bm_normal_object.shipping_postcode + "</option>" +
		"<option value='order_comments' " + (settings != null && settings.common.woocommerce_field == 'order_comments' ? 'selected' : '') + ">" + bm_normal_object.order_comments + "</option>" +
		"</select>" +
		"<div class='field_validate_errortext'></div></div>";

	if (type != 'hidden' && type != 'button' && type != 'submit' && type != 'reset' && type != 'datetime') {
		var defaultValue = "<div scope='row'><label class='setting_label'>" + bm_normal_object.default_value + "</label></div>" +
			"<div class='setting_input'>" +
			"<input name='field_options[default_value]' type='" + (type == 'textarea' ? 'text' : type) + "' id='default_value' value='" + (settings != null && settings.field_options.default_value != '' ? settings.field_options.default_value : '') + "' class='regular-text' autocomplete='off'>" +
			"</div>";
	} else {
		var defaultValue = "<div scope='row'><label class='setting_label'>" + bm_normal_object.default_value + "</label></div>" +
			"<div class='setting_input'>" +
			"<input name='field_options[default_value]' type='" + (type == 'datetime' ? 'datetime-local' : 'text') + "' id='default_value' value='" + (settings != null && settings.field_options.default_value != '' ? settings.field_options.default_value : '') + "' class='regular-text' autocomplete='off'>" +
			"</div>";
	}

	var finalHtml = "<br>" + fieldLabel + "<br>" + fieldName + "<br>" + placeholder + "<br>" + isEditable + "<br>" + fieldDesc + "<br>" + customClass + "<br>" + fieldWidth + "<br>" + linkWooCommerce + "<br>" + fieldKey;

	if (hasAutocomplete) finalHtml += '<br>' + isAutocomplete;
	if (hasIntlCode) finalHtml += '<br>' + isIntlCode;
	if (hasMultiple) finalHtml += '<br>' + isMultiple;
	if (hasRows) finalHtml += '<br>' + isRows;
	if (hasColumns) finalHtml += '<br>' + isColumns;
	if (hasDefaultvalue) finalHtml += '<br>' + defaultValue;
	if (hasChoices) finalHtml += '<br>' + fieldOptions;
	if (hasMinlength) finalHtml += '<br>' + minLength;
	if (hasMaxlength) finalHtml += '<br>' + maxLength;
	if (hasRequired) finalHtml += '<br>' + isRequired;
	if (hasVisibility) finalHtml += '<br>' + isVisible;
	if (type == 'email') finalHtml += '<br>' + isMainEmail;

	finalHtml += "<input type='hidden' name='ordering' id='ordering' value='" + ordering + "'>";
	finalHtml += "<input type='hidden' name='field_position' id='field_position' value='" + field_position + "'>";
	finalHtml += "<input type='hidden' name='field_options[is_default]' id='is_default' value='" + (settings != null && typeof (settings.field_options.is_default) != "undefined" ? settings.field_options.is_default : 0) + "'>";
	finalHtml += "<div class='all_field_error_text' style='display:none;'></div>";
	finalHtml += "<br><div class='field_settings_from_button' style='text-align :center;'><button type='button' class='button button-primary' id='" + (settings != null ? settings.common.id : 0) + "' onclick='saveField(this.id)'>" + bm_normal_object.save + "</button><button type='button' class='button' style='margin-left: 10px;' onclick='hideSettings()'>" + bm_normal_object.cancel + "</button></div>";

	jQuery('#field_settings').html('');
	jQuery('#field_settings').append("<form id='settings_form' method='post'><h3 class='title' style='text-align :center;'>" +
		(settings != null && settings.common.field_label != '' ? settings.common.field_label.charAt(0).toUpperCase() + settings.common.field_label.slice(1).toLowerCase() : type.charAt(0).toUpperCase() + type.slice(1).toLowerCase() + " " + bm_normal_object.field) + bm_normal_object.settings + "</h3>" +
		"<input type='hidden' name='field_type' value='" + type + "'>" + finalHtml + "</form>");
	jQuery('button.field_tablinks.active').removeClass('active');
	jQuery("#settings_button").addClass("active");
	jQuery('#field_listing').hide();
	jQuery('#field_settings').css({ 'display': 'flex', 'justify-content': 'center' });

}



//Check if existing field key
function checkFieldKey($this) {
	jQuery($this).parent('.setting_input').find('.field_validate_errortext').html('');
	jQuery($this).parent('.setting_input').find('.field_validate_errortext').hide();
	var field_key = jQuery($this).val();
	var field_id = jQuery($this).data('id');

	var post = {
		'field_key': field_key,
		'field_id': field_id,
	}

	var data = { 'action': 'bm_check_if_existing_field_key', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata != '' && jsondata != null) {
			var status = jsondata.status;
			var is_existing = jsondata.is_existing;
			var original_key = jsondata.original_key;

			if (status == true) {
				if (is_existing == 1) {
					jQuery($this).val(original_key);
					jQuery($this).attr('value', original_key);
					jQuery($this).parent('.setting_input').find('.field_validate_errortext').html(bm_error_object.existing_field_key);
					jQuery($this).parent('.setting_input').find('.field_validate_errortext').show();
				}
			} else {
				jQuery($this).val(original_key);
				jQuery($this).attr('value', original_key);
				jQuery($this).parent('.setting_input').find('.field_validate_errortext').html(bm_error_object.server_error);
				jQuery($this).parent('.setting_input').find('.field_validate_errortext').show();
			}
		}
	});
}




//Check for primary email in active fields
function check_if_any_primary_email($this) {
	jQuery($this).parents('div.email_div').find('div.field_validate_errortext').html('');
	jQuery($this).parents('div.email_div').find('div.field_validate_errortext').hide();
	jQuery($this).parents('div.email_div').find('div.field_validate_successtext').html('');
	jQuery($this).parents('div.email_div').find('div.field_validate_successtext').hide();

	var field_key = jQuery($this).data('key');

	var data = { 'action': 'bm_get_primary_email_field_key', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		jQuery('#active_emails_details').html('');

		if (jsondata != '' && jsondata != null) {
			var primary_email_key = jsondata.primary_email_key;
			var total_email_fields = jsondata.total_email_fields;
			var checkbox_html = jsondata.checkbox_html;

			if (field_key == primary_email_key && total_email_fields == 1) {
				jQuery($this).prop('checked', true);
				jQuery($this).parents('div.email_div').find('div.field_validate_errortext').html(bm_error_object.only_primary_email_field);
				jQuery($this).parents('div.email_div').find('div.field_validate_errortext').show();
			} else if (field_key == primary_email_key && total_email_fields > 1) {
				jQuery('#active_emails_details').html(checkbox_html);
				jQuery('#primary_email_modal').addClass('active-modal');
			} else if (field_key != primary_email_key && total_email_fields > 1) {
				if (confirm(bm_normal_object.are_you_sure)) {
					var data = { 'action': 'bm_save_non_primary_email_as_primary', 'field_key': field_key, 'nonce': bm_ajax_object.nonce };
					jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
						var jsondata = jQuery.parseJSON(response);
						if (jsondata != '' && jsondata != null) {
							var status = jsondata.status;
							if (status == true) {
								jQuery(document).find('div.email_div').children('div.field_validate_successtext').html(bm_success_object.save_success);
								jQuery(document).find('div.email_div').children('div.field_validate_successtext').show();
							} else {
								jQuery(document).find('div.email_div').children('div.field_validate_errortext').html(bm_error_object.server_error);
								jQuery(document).find('div.email_div').children('div.field_validate_errortext').show();
							}
						} else {
							jQuery(document).find('div.email_div').children('div.field_validate_errortext').html(bm_error_object.server_error);
							jQuery(document).find('div.email_div').children('div.field_validate_errortext').show();
						}
					});
				}
			} else if ((primary_email_key == '' && total_email_fields == 0) || (primary_email_key != '' && total_email_fields == 0)) {
				jQuery($this).prop('checked', true);
				jQuery($this).parents('div.email_div').find('div.field_validate_errortext').html(bm_error_object.only_primary_email_field);
				jQuery($this).parents('div.email_div').find('div.field_validate_errortext').show();
			}
		} else {
			jQuery($this).prop('checked', true);
			jQuery($this).parents('div.email_div').find('div.field_validate_errortext').html(bm_error_object.server_error);
			jQuery($this).parents('div.email_div').find('div.field_validate_errortext').show();
		}
	});
}




// Close primary email Modal
jQuery(document).on('click', '#primary_email_modal .close', function () {
	jQuery(document).find('#primary_email_modal').removeClass('active-modal');
	jQuery(document).find('#is_main_email').prop('checked', true);
});



// Save primary email
jQuery(document).on('click', '.save_primary_email', function () {
	jQuery(this).parents('.formbottombuttonbar').find('.primary_email_errortext').html('');
	jQuery(this).parents('.formbottombuttonbar').find('.primary_email_errortext').hide();
	jQuery(document).find('div.email_div').children('div.field_validate_errortext').html('');
	jQuery(document).find('div.email_div').children('div.field_validate_errortext').hide();
	jQuery(document).find("div.field_successtext").html('');
	jQuery(document).find("div.field_successtext").hide();
	jQuery(document).find('#primary_email_modal').removeClass('active-modal');

	var $this = jQuery(this);
	var id = 0;
	jQuery(".email_fields_results input").each(function () {
		if (jQuery(this).is(':checked')) {
			id = jQuery(this).attr('id');
		}
	});

	if (id == 0) {
		jQuery(this).parents('.formbottombuttonbar').find('.primary_email_errortext').html(bm_normal_object.choose_one);
		jQuery(this).parents('.formbottombuttonbar').find('.primary_email_errortext').show();
		return false;
	} else {
		var post = {
			'id': id,
		}

		var data = { 'action': 'bm_save_primary_email_field_key', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata != '' && jsondata != null) {
				var status = jsondata.status;
				if (status == true) {
					hideSettings();
					jQuery(document).find("div.field_successtext").html(bm_success_object.save_success);
					jQuery(document).find("div.field_successtext").show();
				} else {
					jQuery(document).find('div.email_div').children('div.field_validate_errortext').html(bm_error_object.server_error);
					jQuery(document).find('div.email_div').children('div.field_validate_errortext').show();
				}
			} else {
				jQuery(document).find('div.email_div').children('div.field_validate_errortext').html(bm_error_object.server_error);
				jQuery(document).find('div.email_div').children('div.field_validate_errortext').show();
			}
		});
	}
});




//Add more options for select/checkbox/radio fields
function addMoreOption($this) {
	var crossSign = "✕";
	var type = jQuery($this).attr("data-type");
	var total_element = jQuery(".option_element").length;
	var max = 10;

	if (total_element != 0) {
		var lastid = jQuery(".option_element:last").attr("id");
		var split_id = lastid.split("_");
		var nextindex = Number(split_id[2]) + 1;

		// if (total_element <= max) {
		jQuery(".option_element:last").after("<div class='option_element setting_input bm_field_required' id='default_option_" + nextindex + "'><br></div>");
		jQuery("#default_option_" + nextindex).append("<input name='field_options[options][values][" + nextindex + "]' type='text' id='option_" + nextindex + "' style='width: 150px;' placeholder='" + bm_normal_object.insert_value + "' autocomplete='off'>&nbsp;" +
			"<input name='field_options[options][keys][" + nextindex + "]' type='text' id='key_" + nextindex + "' style='width: 150px;' placeholder='" + bm_normal_object.insert_key + "' autocomplete='off'>&nbsp;" +
			bm_normal_object.selected + " ?&nbsp;&nbsp;<span>" +
			"<input name='field_options[options][selected][" + nextindex + "]' type='hidden' id='selected_" + nextindex + "' value='0'>" +
			"<input name='field_options[options][selected][" + nextindex + "]' type='checkbox' id='selected_" + nextindex + "' value='1' class='regular-text' autocomplete='off'></span>" +
			"&nbsp;<span id='removeoption_" + nextindex + "' data-type='" + type + "' style='color:red;cursor:pointer;' class='remove_option' title='" + bm_normal_object.remove + "' autocomplete='off'>" + crossSign + "</span><div class='field_validate_errortext'></div>");
		// }
	} else {
		var nextindex = 1;
		jQuery(".options_box").append("<div class='option_element setting_input bm_field_required' id='default_option_" + nextindex + "'><br></div>");
		jQuery("#default_option_" + nextindex).append("<input name='field_options[options][values][" + nextindex + "]' type='text' id='option_" + nextindex + "' style='width: 150px;' placeholder='" + bm_normal_object.insert_value + "' autocomplete='off'>&nbsp;" +
			"<input name='field_options[options][keys][" + nextindex + "]' type='text' id='key_" + nextindex + "' style='width: 150px;' placeholder='" + bm_normal_object.insert_key + "' autocomplete='off'>&nbsp;" +
			bm_normal_object.selected + " ?&nbsp;&nbsp;<span>" +
			"<input name='field_options[options][selected][" + nextindex + "]' type='hidden' id='selected_" + nextindex + "' value='0'>" +
			"<input name='field_options[options][selected][" + nextindex + "]' type='checkbox' id='selected_" + nextindex + "' value='1' class='regular-text' autocomplete='off'></span>" +
			"&nbsp;<span id='removeoption_" + nextindex + "' data-type='" + type + "' style='color:red;cursor:pointer;' class='remove_option' title='" + bm_normal_object.remove + "' autocomplete='off'>" + crossSign + "</span><div class='field_validate_errortext'></div>");
	}
}




//Field section functions
jQuery(document).ready(function ($) {
	jQuery('.global_timezone_errortext').hide();
	jQuery('.fetch_services_by_category_order_field_errortext').hide();


	// Get loader on ajax load
	$body = $("body");

	$(document).on({
		ajaxStart: function () { $body.addClass("loading"); },
		ajaxStop: function () { $body.removeClass("loading"); }
	});

	$(document).on('click', '.email_fields_results input[type="checkbox"]', function () {
		$('input[type="checkbox"]').not(this).prop('checked', false);
	});

	$('.content_body').sortable({
		axis: "y",
		items: ".existing_field_content",
		containment: "#content_section",
		revert: true,
		scroll: true,
		cursor: "move",
		update: function () {
			var ordering = {};
			$(".content_body .existing_field_content").each(function (i) {
				ordering[i] = $(this).data('order');
			})
			bm_get_all_field_labels(ordering);
		}
	}).disableSelection();

	$('.field_validate_errortext').hide();
	$('.field_validate_errortext').html('');
	$('.field_validate_successtext').hide();
	$('.field_validate_successtext').html('');
	$('.all_field_error_text').html('');

	bm_get_all_field_labels();

	//Remove fields with animation
	$(".content_body").on("click", ".remove_field", function () {
		var $this = $(this);
		var id = $this.attr('id') != '' ? $this.attr('id') : 0;

		if (confirm(bm_normal_object.sure_remove_field)) {

			var parent_id = $($this).parent().parent().attr('id');
			var deleteindex = parent_id.split("_")[1];

			$this.parent().parent().slideUp("slow", function () {

				if (id != 0) {
					var data = { 'action': 'bm_remove_field', 'id': id, 'nonce': bm_ajax_object.nonce };
					jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
						if (jQuery.parseJSON(response).status == 'deleted') {
							showMessage(bm_success_object.field_remove_success, 'success');
							// jQuery(".field_successtext").html(bm_success_object.field_remove_success);
							// jQuery(".field_successtext").show();
						} else {
							showMessage(bm_error_object.server_error, 'error');
							// jQuery('.field_errortext').html(bm_error_object.server_error);
							// jQuery('.field_errortext').show();
						}
					});
				}

				$("#div_" + deleteindex).remove();

				if ($('.existing_field_content').length != 0) {
					$(".existing_field_content").each(function (id, item) {
						var counter = id + 1;
						$(item).attr("id", "div_" + counter);
					});
				}

				hideSettings();
			});
		} else {
			if (id == 0) {
				return false;
			} else {
				hideSettings();
			}
		}
	});

	// Remove option
	$(document).on("click", ".remove_option", function () {
		if (confirm(bm_normal_object.sure_remove_option)) {
			var id = this.id;
			var type = $(this).attr('data-type');
			var split_id = id.split("_");
			var deleteindex = split_id[1];

			$("#default_option_" + deleteindex).remove();

			$(".option_element").each(function (id, item) {

				$(this).attr("id", "default_option_" + id);

				$(this).find("input[type='text'][name*='[values]']")
					.attr("name", "field_options[options][values][" + id + "]")
					.attr("id", "option_" + id);

				$(this).find("input[type='text'][name*='[keys]']")
					.attr("name", "field_options[options][keys][" + id + "]")
					.attr("id", "key_" + id);

				$(this).find("input[type='checkbox'][name*='[selected]']")
					.attr("name", "field_options[options][selected][" + id + "]")
					.attr("id", "selected_" + id);

				$(this).find(".remove_option")
					.attr("id", "removeoption_" + id);


				$(this).find(".remove_option").attr('data-type', type);
			});
		}
	});

});




// Save Field Data
function saveField(id) {

	if (validateFields()) {
		var post = {
			'formdata': getFormData('settings_form'),
			'id': id,
		}

		var data = { 'action': 'bm_save_field_and_setting', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			if (response != '' && response != null) {

				var crossSign = "✕";
				var jsonData = jQuery.parseJSON(response);
				var field = jsonData.data;
				var status = jsonData.status;
				var is_default = jsonData.is_default;

				if (status == 'saved') {
					var total_element = jQuery(".existing_field_content").length;
					if (total_element != 0) {
						var lastid = jQuery(".existing_field_content:last").attr("id");
						var nextindex = Number(lastid.split("_")[1]) + 1;
					} else {
						var nextindex = 1;
					}

					var fieldBox = "<div class='existing_field_content' data-type='" + field.field_type + "' id='div_" + nextindex + "' data-order=" + field.ordering + " data-position='" + field.field_position + "'>" +
						"<p title='" + bm_normal_object.type + field.field_type + "' class='field_text style='float: left;'>" + field.field_label + " (" + field.field_type.charAt(0).toUpperCase() + field.field_type.slice(1).toLowerCase() + ")" + " </p>" +
						"<div class='field_content_buttons'>";

					if (field.field_desc !== '') {
						fieldBox += "<button type='button' class='info_field' title='" + field.field_desc + "'><i class='fa fa-info info_icon' aria-hidden='true'></i></button>";
					}

					fieldBox += "<button type='button' class='edit_field' title='" + bm_normal_object.edit + "' id='" + field.id + "' onClick='get_field_Settings(this.id)'><i class='fa fa-pencil edit_icon' aria-hidden='true'></i></button>";

					if (is_default == 0) {
						fieldBox += "<button type='button' id=" + field.id + " class='remove_field' title='" + bm_normal_object.remove + "'>" + crossSign + "</button>";
					}

					fieldBox += "</div></div>";

					total_element != 0 ? jQuery(".existing_field_content:last").after(fieldBox) : jQuery(".content_body").append(fieldBox);
					jQuery('.field_content').remove();
				} else if (status == 'updated') {
					bm_get_all_field_labels();
				}

				hideSettings();
				showMessage(bm_success_object.save_success, 'success');
				// jQuery(".field_successtext").html(bm_success_object.save_success);
				// jQuery(".field_successtext").show();

			}
		});
	}
}




// Hide settings section Field Data
function hideSettings() {
	if (jQuery('.field_content').length != 0) {
		jQuery('.field_content').each(function () {
			jQuery(this).remove();
		});
	}

	jQuery('button.field_tablinks.active').removeClass('active');
	jQuery("#listing_button").addClass("active");
	jQuery('#field_settings').hide();
	jQuery('#field_settings').html('');
	jQuery('#field_settings').html('<p style="text-align: center;">' + bm_normal_object.choose_field + '</p>');
	jQuery('#field_listing').show();
}




// Validate Field Data
function validateFields() {
	jQuery('.field_validate_errortext').hide();
	jQuery('.field_validate_errortext').html('');
	jQuery('.all_field_error_text').html('');


	jQuery('.bm_field_required').each(
		function (index, element) {
			if (jQuery(this).children('input').attr('type') != 'hidden') {
				var value = jQuery(this).children('select').length != 0 ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());

				if (value == "") {
					jQuery(this).find('.field_validate_errortext').html(bm_error_object.required_field);
					jQuery(this).find('.field_validate_errortext').show();
				} else if (jQuery(this).children('input').attr('id') == 'field_label') {
					var regex = /^[a-zA-Z]+(([',. -][a-zA-Z ])?[a-zA-Z]*)*$/;
					if (!value.match(regex)) {
						jQuery(this).find('.field_validate_errortext').html(bm_error_object.field_label_validation);
						jQuery(this).find('.field_validate_errortext').show();
					}
				}
			}
		}
	);
	var b = '';
	b = jQuery('.field_validate_errortext').each(
		function () {
			var a = jQuery(this).html();
			b = a + b;
			jQuery('.all_field_error_text').html(b);
		}
	);
	var error = jQuery('.all_field_error_text').html();
	if (error == '') {
		return true;
	} else {
		return false;
	}
}




// Get form data
function getFormData(formId) {

	var formData = {};
	var common_data = {};
	var conditional = {};

	var inputs = jQuery('#' + formId).serializeArray();

	jQuery.each(inputs, function (i, input) {
		if (input.name.startsWith("field_options")) {
			var str = input.name.split("field_options[").pop();
			conditional[str] = input.value;
		} else {
			common_data[input.name] = input.value;
		}
	});

	formData['common_data'] = common_data;
	formData['conditional'] = { 'field_options': conditional };

	return formData;
}




// Ajax for getting field labels on Page Load
function bm_get_all_field_labels(ordering = []) {
	jQuery('.field_successtext').hide();
	jQuery('.field_errortext').hide();
	jQuery('.field_successtext').html(' ');
	jQuery('.field_errortext').html(' ');

	var data = { 'action': 'bm_get_all_field_labels', 'ordering': ordering, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		if (jQuery.parseJSON(response) != null) {
			jQuery(".content_body").html('');
			var fields = jQuery.parseJSON(response);
			var crossSign = "✕";
			var fieldBox = '';

			var total_element = jQuery(".existing_field_content").length;
			if (total_element != 0) {
				var lastid = jQuery(".existing_field_content:last").attr("id");
				var nextindex = Number(lastid.split("_")[1]) + 1;
			} else {
				var nextindex = 1;
			}

			if (fields.length != 0) {
				for (var i = 0; i < fields.length; i++) {
					fieldBox += "<div class='existing_field_content' data-type='" + fields[i].field_type + "' id='div_" + nextindex + "' data-order=" + fields[i].ordering + " data-position='" + fields[i].field_position + "'>" +
						"<p title='" + bm_normal_object.type + fields[i].field_type + "' class='field_text style='float: left;'>" + fields[i].field_label + " (" + fields[i].field_type.charAt(0).toUpperCase() + fields[i].field_type.slice(1).toLowerCase() + ")" + " </p>" +
						"<div class='field_content_buttons'>";
					if (fields[i].field_desc !== '') {
						fieldBox += "<button type='button' class='info_field' title='" + fields[i].field_desc + "'><i class='fa fa-info info_icon' aria-hidden='true'></i></button>";
					}
					fieldBox += "<button type='button' class='edit_field' title='" + bm_normal_object.edit + "' id='" + fields[i].id + "' onClick='get_field_Settings(this.id)'><i class='fa fa-pencil edit_icon' aria-hidden='true'></i></button>";
					if (fields[i].is_default == 0) {
						fieldBox += "<button type='button' id=" + fields[i].id + " class='remove_field' title='" + bm_normal_object.remove + "'>" + crossSign + "</button>";
					}
					fieldBox += "</div></div>";

					nextindex++;
				}
			} else {
				fieldBox += "<p class='text-align:center'>" + bm_normal_object.no_records + "</p>";
			}
			jQuery(".content_body").append(fieldBox);
		}
	});
}



// Ajax for getting field data on Page Load
function get_field_Settings(id) {
	jQuery('.field_successtext').hide();
	jQuery('.field_errortext').hide();
	jQuery('.field_successtext').html(' ');
	jQuery('.field_errortext').html(' ');

	if (jQuery('.field_content').length != 0) {
		jQuery('.field_content').each(function () {
			jQuery(this).remove();
		});
	}

	var data = { 'action': 'bm_get_field_settings', 'id': id, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		if (jQuery.parseJSON(response) != null) {
			var settings = jQuery.parseJSON(response);
			if (settings.length != 0) {
				showFieldSettings(settings.common.field_type, settings.common.field_key, settings.common.ordering, settings.common.field_position, settings.common.primary_mail_key, settings);
			} else {
				jQuery('.field_errortext').html(bm_error_object.server_error);
				jQuery('.field_errortext').show();
			}
		} else {
			jQuery('.field_errortext').html(bm_error_object.server_error);
			jQuery('.field_errortext').show();
		}
	});
}



// Fetch field preview form
jQuery(document).on('click', '.preview_button', function (e) {
	e.preventDefault();

	var data = { 'action': 'bm_fetch_preview_form', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, async function (response) {
		jQuery('#preview_form').html('');
		if (response != null && response != '') {
			jQuery('#preview_form').html(response);

			jQuery('#preview_form').on('change', 'select[name="billing_country"]', async function () {
				var country_code = jQuery.trim(jQuery(this).val());
				var stateField = jQuery('#preview_form select[name="billing_state"]');

				if (country_code) {
					await bm_get_state_of_country(country_code, stateField);
				}
			});

			var country = jQuery.trim(jQuery('#preview_form select[name="billing_country"]').val());
			if (country) {
				await bm_get_state_of_country(country, jQuery('#preview_form select[name="billing_state"]'));
			}

			jQuery('#preview_form_modal').addClass('active-modal');
			setIntlInput();
		} else {
			jQuery('#preview_form').html(bm_error_object.server_error);
			jQuery('#preview_form_modal').addClass('active-modal');
		}
	});
});



// Close Modal
function closeModal(id) {
	// jQuery('#' + id).removeClass('active-modal');

	var modal = jQuery('#' + id);

	modal.animate({ top: "-=100px" }, 300, function () {
		modal.css({ top: "" });
		modal.removeClass('active-modal');
	});

	if (id == 'resend_email_modal') {
		remove_unsent_temporary_email_attachment();
	}
}



// Template validation
function add_template_validation() {
	jQuery('.tmpl_errortext').html('');
	jQuery('.tmpl_errortext').hide();
	jQuery('.all_tmpl_error_text').html('');
	var b = 0;
	var template_body = tinyMCE.activeEditor.getContent();

	jQuery('.bm_tmpl_required').each(
		function (index, element) {
			var type = jQuery(this).children().prop('type');
			var value = (type == 'select-one' || type == 'select-multiple') ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());
			if (value == "") {
				jQuery(this).children('.tmpl_errortext').html(bm_error_object.required_field);
				jQuery(this).children('.tmpl_errortext').show();
				b++;
			}
		}
	);

	if (template_body == '') {
		jQuery('#email_body_td').children().find('.tmpl_errortext').html(bm_error_object.required_field);
		jQuery('#email_body_td').children().find('.tmpl_errortext').show();
		b++;
	}

	if (b == 0) {
		return true;
	} else {
		return false;
	}
}



// Add admin emails in global mail settings
function bm_add_admin_email_option() {
	var email_option_element = jQuery('td.bm_email_option_field').length;
	var option_box = '<td class="bm_email_option_field"><div class="bm_email_option"><input type="text" class="regular-text" name="bm_shop_admin_email[]" value=""> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="bm_remove_shop_admin_email_field no_left_space" onClick="bm_remove_shop_admin_email(this)">' + bm_normal_object.delete + '</span>&nbsp;</div></td>';
	if (email_option_element !== 0) {
		jQuery('#enable_admin_notification_html td.bm_email_option_field:last').after(option_box);
	} else {
		jQuery('#enable_admin_notification_html td.add_admin_email_option_class').before(option_box);
	}
	jQuery('#enable_admin_notification_html td.bm_email_option_field:last input').focus();

}



// Remove admin email in global mail settings
function bm_remove_shop_admin_email(a) {
	jQuery(a).parent('div.bm_email_option').remove();
}



// Toggle show/hide on conditions
function bm_toggle_tab(instance, id) {
	var $this = jQuery(instance);
	if ($this.attr('type') == 'checkbox') {
		if ($this.is(':checked')) {
			jQuery('#' + id).show();
			if (id == "smtp_settings_html") {
				jQuery('#no_smtp_html').hide();
			}
		} else {
			jQuery('#' + id).hide();
			if (id == "smtp_settings_html") {
				jQuery('#no_smtp_html').show();
			}
		}
	}
}



// Test smtp connection
function bm_test_smtp_connection() {
	jQuery('#smtptestconn a').hide();
	jQuery('#smtptestconn img').show();
	jQuery('span#bm_smtp_result').html('');
	bm_smtp_test_email_address = jQuery("#bm_smtp_test_email_address").val();
	bm_smtp_host = jQuery("#bm_smtp_host").val();
	bm_smtp_encription = jQuery("#bm_smtp_encription").val();
	bm_smtp_port = jQuery("#bm_smtp_port").val();
	bm_smtp_authentication = jQuery("#bm_smtp_authentication").val();
	bm_smtp_username = jQuery("#bm_smtp_username").val();
	bm_smtp_from_email_name = jQuery("#bm_smtp_from_email_name").val();
	bm_smtp_from_email_address = jQuery("#bm_smtp_from_email_address").val();
	bm_smtp_password = jQuery("#bm_smtp_password").val();

	var data = {
		'action': 'bm_test_smtp',
		'nonce': bm_ajax_object.nonce,
		'bm_smtp_test_email_address': bm_smtp_test_email_address,
		'bm_smtp_host': bm_smtp_host,
		'bm_smtp_encription': bm_smtp_encription,
		'bm_smtp_port': bm_smtp_port,
		'bm_smtp_authentication': bm_smtp_authentication,
		'bm_smtp_username': bm_smtp_username,
		'bm_smtp_from_email_name': bm_smtp_from_email_name,
		'bm_smtp_from_email_address': bm_smtp_from_email_address,
		'bm_smtp_password': bm_smtp_password
	};

	jQuery.post(
		bm_ajax_object.ajax_url, data, function (response) {
			jQuery('#smtptestconn a').show()
			jQuery('#smtptestconn img').hide();
			if (jQuery.trim(response) == "1") {
				jQuery('span#bm_smtp_result').html('<span class="bm_smtp_success">' + bm_normal_object.success + '</span>');
			} else {
				jQuery('span#bm_smtp_result').html('<span class="bm_smtp_failed">' + bm_normal_object.failure + '</span>');
			}
		}
	);
}



// Insert email field in wp editor
function bm_insert_field_in_email(a) {
	tinyMCE.activeEditor.execCommand('mceInsertContent', false, a);
}


// Clear content of wp editor
function bm_clear_content_in_wp_editor_body(a) {
	tinymce.activeEditor.setContent(a);
}



//Tooltip
jQuery(document).ready(function ($) {
	$(document).tooltip({
		position: {
			my: "center bottom-20",
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



// Global General Settings validation
function global_general_settings_validation() {
	jQuery('.global_general_errortext').html('');
	jQuery('.global_general_errortext').hide();
	jQuery('.all_global_general_error_text').html('');
	var error = 0;

	jQuery('.global_general_required').each(
		function (index, element) {
			if (jQuery('input[name="bm_frontend_view_type"]:checked').length == 0) {
				jQuery(this).children('.global_general_errortext').html(bm_error_object.required_field);
				jQuery(this).children('.global_general_errortext').show();
				error++;
			}
		}
	);

	if (error == 0) {
		return true;
	} else {
		return false;
	}
}



// Fetch timezone
function bm_fetch_timezone() {
	jQuery('.global_timezone_errortext').html('');
	jQuery('.global_timezone_errortext').hide();

	var country_code = jQuery('#bm_booking_country').val();
	var data = { 'action': 'bm_fetch_timezone', 'country_code': country_code, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		jQuery('#bm_booking_time_zone').html('');

		if (jsondata.status == true) {
			var timezones = jsondata.timezones;
			for (var i = 0; i < timezones.length; i++) {
				jQuery('#bm_booking_time_zone').append(jQuery('<option></option>').val(timezones[i]).text(timezones[i]));
			};
		} else {
			jQuery('.global_timezone_errortext').html(bm_error_object.timezone_error);
			jQuery('.global_timezone_errortext').show();
		}
	});
}




//International tel input for phone form fields
function setIntlInput() {
	jQuery('#preview_form :input').map(function () {
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




// Add unavailable dates in service page
// function bm_add_unavailable_date() {
// 	var total_elements = jQuery('td.date_option_field span.date_input_span').length;
// 	var date = new Date(jQuery.now());
// 	var crossSign = "✕";
// 	date = date.getFullYear() + "-" + padWithZeros((date.getMonth() + 1)) + "-" + padWithZeros(date.getDate());

// 	if (total_elements !== 0) {
// 		var id = jQuery('td.date_option_field span.date_input_span:last input').attr('id');
// 		var index = Number(id.split("_")[2]) + 1;
// 		var option_box = '<span class="date_input_span"><input type="date" id="unavailable_date_' + index + '" name="service_unavailability[dates][' + index + ']">';
// 		option_box += '<button type="button" id="svc_date_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_svc_unavailable_date(this)">' + crossSign + '</button></span>'
// 		jQuery('td.date_option_field span.date_input_span:last').after(option_box);
// 	} else {
// 		var option_box = '<span class="date_input_span"><input type="date" id="unavailable_date_1" name="service_unavailability[dates][1]">';
// 		option_box += '<button type="button" id="svc_date_remove" title="' + bm_normal_object.remove + '" onclick="bm_remove_svc_unavailable_date(this)">' + crossSign + '</button></span>'
// 		jQuery('td.date_option_field span.add_dates_button').before(option_box);
// 	}
// }




// Remove unavailable dates in service page
// function bm_remove_svc_unavailable_date($this) {
// 	if (confirm(bm_normal_object.remove_svc_unavl_date)) {
// 		jQuery($this).parent('span').remove();
// 		jQuery('[id^=unavailable_date_]').each(function (id, item) {
// 			var counter = id + 1;

// 			jQuery(item).attr('id', 'unavailable_date_' + counter);
// 			jQuery(item).attr('name', 'service_unavailability[dates][' + counter + ']');
// 		});
// 	}
// }

// Fetch booked product form
jQuery(document).on('click', '#show-product-dialog', function (e) {
	e.preventDefault();

	var data = { 'action': 'bm_fetch_ordered_product_details', 'order_id': getUrlParameter('id'), 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#booked_service_details').html('');

		if (response != null && response != '') {
			var jsondata = jQuery.parseJSON(response);

			if (jsondata.status == true) {
				var products = jsondata.products;
				var service = products.service;
				var services = products.services;
				var categories = products.categories;
				var extras = products.extras;

				if (service.length !== 0) {
					addProductToList(service, 'service', categories, services);
				}

				if (extras.length !== 0) {
					extras.forEach(function (extra) {
						addProductToList(extra, 'extra');
					});
				} else {
					jQuery("#booked_service_details").append(bm_error_object.products_error);
				}
				jQuery('#booked_service_modal').addClass('active-modal');
			} else {
				jQuery('#booked_service_details').html(bm_error_object.server_error);
				jQuery('#booked_service_modal').addClass('active-modal');
			}
		} else {
			jQuery('#booked_service_details').html(bm_error_object.server_error);
			jQuery('#booked_service_modal').addClass('active-modal');
		}
	});
});




// Event handler for adding a new product
jQuery(document).on("click", "#add-product", function () {
	addProductToList();
});




// Event handler for saving product data
jQuery(document).on("click", "#save-product", function () {
	saveProductChanges();
});



// Remove extra product
jQuery(document).on("click", "#remove-extra-product", function () {
	var confirmation = confirm(bm_normal_object.remove_extra_product);

	if (confirmation === true) {
		var productItem = jQuery(this).closest("li");
		productItem.remove();
	}
});




// Function to save changes made in the dialog
function saveProductChanges() {
	var updatedProducts = [];

	var confirmation = confirm(bm_normal_object.are_you_sure);

	if (confirmation === true) {
		jQuery("#booked_service_details li").each(function () {
			var listItem = jQuery(this);
			var name = listItem.find(".product-name").val();
			var price = listItem.find(".product-price").val();
			var quantity = listItem.find(".product-quantity").val();

			var updatedProduct = {
				service_name: name,
				base_price: price,
				quantity: quantity
			};

			updatedProducts.push(updatedProduct);
		});

		var data = { 'action': 'bm_save_product_order', 'post': updatedProducts, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			if (response != null && response != '') {
				var jsondata = jQuery.parseJSON(response);
				var products = jsondata.products;
				if (products.length !== 0) {
					products.forEach(function (product) {
						addProductToList(product);
					});
				} else {
					jQuery("#booked_service_details").append(bm_error_object.products_error);
				}
				jQuery('#booked_service_modal').addClass('active-modal');
			} else {
				jQuery('#booked_service_details').html(bm_error_object.server_error);
				jQuery('#booked_service_modal').addClass('active-modal');
			}
		});
	}
}




//Dialog for customer details
jQuery(document).ready(function ($) {
	$("#customer-dialog, #order-attachments-dialog, #voucher-info-dialog, #checkin-attachments-dialog").dialog({
		autoOpen: false,
		modal: true,
		width: 400,
		buttons: {
			Close: function () {
				$(this).dialog("close");
			}
		},
		show: {
			effect: "bounce",
			duration: 1000
		},
		hide: {
			effect: "slide",
			direction: 'up',
			duration: 1000
		},
		open: function (event, ui) {
			$(this).fadeIn(500);
		}
	});

	// Customer information dialogue
	$(document).on("click", ".show-customer-dialog", function () {
		var data = { 'action': 'bm_fetch_customer_data_for_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			$("#customer-list").empty();

			if (jsondata.status == true) {
				var customer_info = jsondata.customer_info;
				if (customer_info.length !== 0) {
					var first_name = typeof (customer_info.billing_first_name) != "undefined" && customer_info.billing_first_name != null ? customer_info.billing_first_name : 'N/A';
					var last_name = typeof (customer_info.billing_last_name) != "undefined" && customer_info.billing_last_name != null ? customer_info.billing_last_name : 'N/A';
					var email = typeof (customer_info.billing_email) != "undefined" && customer_info.billing_email != null ? customer_info.billing_email : 'N/A';
					var mobile = typeof (customer_info.billing_contact) != "undefined" && customer_info.billing_contact != null ? customer_info.billing_contact : 'N/A';
					var city = typeof (customer_info.billing_city) != "undefined" && customer_info.billing_city != null ? customer_info.billing_city : 'N/A';
					var state = typeof (customer_info.billing_state) != "undefined" && customer_info.billing_state != null ? customer_info.billing_state : 'N/A';

					var listItem = $("<li></li>");
					listItem.append("<div><strong>" + bm_normal_object.first_name + "</strong> : " + first_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.last_name + "</strong> : " + last_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.email + "</strong> : " + email + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.phone + "</strong> : " + mobile + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.city + "</strong> : " + city + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.state + "</strong> : " + state + "</div>");
					$("#customer-list").append(listItem);
				} else {
					$("#customer-list").append("<div class='error_msg'>" + bm_error_object.customer_error + "</div>");
				}
			} else {
				$("#customer-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#customer-dialog").dialog("open");
		});
	});

	// Customer information dialogue
	$(document).on("click", ".show-failed-order-customer-dialog", function () {
		var data = { 'action': 'bm_fetch_customer_data_for_failed_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			$("#customer-list").empty();

			if (jsondata.status == true) {
				var customer_info = jsondata.customer_info;
				if (customer_info.length !== 0) {
					var first_name = typeof (customer_info.billing_details.billing_first_name) != "undefined" && customer_info.billing_details.billing_first_name != null ? customer_info.billing_details.billing_first_name : 'N/A';
					var last_name = typeof (customer_info.billing_details.billing_last_name) != "undefined" && customer_info.billing_details.billing_last_name != null ? customer_info.billing_details.billing_last_name : 'N/A';
					var email = typeof (customer_info.billing_details.billing_email) != "undefined" && customer_info.billing_details.billing_email != null ? customer_info.billing_details.billing_email : 'N/A';
					var mobile = typeof (customer_info.billing_details.billing_contact) != "undefined" && customer_info.billing_details.billing_contact != null ? customer_info.billing_details.billing_contact : 'N/A';
					var city = typeof (customer_info.billing_details.billing_city) != "undefined" && customer_info.billing_details.billing_city != null ? customer_info.billing_details.billing_city : 'N/A';
					var state = typeof (customer_info.billing_details.billing_state) != "undefined" && customer_info.billing_details.billing_state != null ? customer_info.billing_details.billing_state : 'N/A';

					var listItem = $("<li></li>");
					listItem.append("<div><strong>" + bm_normal_object.first_name + "</strong> : " + first_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.last_name + "</strong> : " + last_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.email + "</strong> : " + email + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.phone + "</strong> : " + mobile + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.city + "</strong> : " + city + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.state + "</strong> : " + state + "</div>");
					$("#customer-list").append(listItem);
				} else {
					$("#customer-list").append("<div class='error_msg'>" + bm_error_object.customer_error + "</div>");
				}
			} else {
				$("#customer-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#customer-dialog").dialog("open");
		});
	});

	// Customer information dialogue
	$(document).on("click", ".show-archived-order-customer-dialog", function () {
		var data = { 'action': 'bm_fetch_customer_data_for_archived_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			$("#customer-list").empty();

			if (jsondata.status == true) {
				var customer_info = jsondata.customer_info;
				if (customer_info) {
					var first_name = typeof (customer_info.billing_first_name) != "undefined" && customer_info.billing_first_name != null ? customer_info.billing_first_name : 'N/A';
					var last_name = typeof (customer_info.billing_last_name) != "undefined" && customer_info.billing_last_name != null ? customer_info.billing_last_name : 'N/A';
					var email = typeof (customer_info.billing_email) != "undefined" && customer_info.billing_email != null ? customer_info.billing_email : 'N/A';
					var mobile = typeof (customer_info.billing_contact) != "undefined" && customer_info.billing_contact != null ? customer_info.billing_contact : 'N/A';
					var city = typeof (customer_info.billing_city) != "undefined" && customer_info.billing_city != null ? customer_info.billing_city : 'N/A';
					var state = typeof (customer_info.billing_state) != "undefined" && customer_info.billing_state != null ? customer_info.billing_state : 'N/A';

					var listItem = $("<li></li>");
					listItem.append("<div><strong>" + bm_normal_object.first_name + "</strong> : " + first_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.last_name + "</strong> : " + last_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.email + "</strong> : " + email + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.phone + "</strong> : " + mobile + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.city + "</strong> : " + city + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.state + "</strong> : " + state + "</div>");
					$("#customer-list").append(listItem);
				} else {
					$("#customer-list").append("<div class='error_msg'>" + bm_error_object.customer_error + "</div>");
				}
			} else {
				$("#customer-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#customer-dialog").dialog("open");
		});
	});

	// Order attachments dialogue
	$(document).on("click", ".show-order-attachments", function () {
		var data = { 'action': 'bm_fetch_attachments_for_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			$("#attachments-list").empty();

			if (status == true) {
				var attachments = jsondata.attachments;
				var order_details = attachments.order_details ? attachments.order_details : '';
				var customer_details = attachments.customer_details ? attachments.customer_details : '';

				if (attachments.length !== 0) {
					var listItem = $("<li></li>");

					if (order_details) {
						listItem.append("<div><strong>" + bm_normal_object.order_details + "</strong>: <a target='_blank' href='" + order_details + "'>" + bm_normal_object.order_details_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div><br>");
					}

					if (customer_details) {
						listItem.append("<div><strong>" + bm_normal_object.customer_details + "</strong>: <a target='_blank' href='" + customer_details + "'>" + bm_normal_object.customer_details_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div>");
					}

					$("#attachments-list").append(listItem);
				} else {
					$("#attachments-list").append("<div class='no_attachments'>" + bm_normal_object.no_attachments + "</div>");
				}
			} else {
				$("#attachments-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#order-attachments-dialog").dialog("open");
		});
	});

	// Order attachments dialogue
	$(document).on("click", ".show-archived-order-attachments", function () {
		var data = { 'action': 'bm_fetch_attachments_for_archived_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			$("#attachments-list").empty();

			if (status == true) {
				var attachments = jsondata.attachments;
				var order_details = attachments.order_details ? attachments.order_details : '';
				var customer_details = attachments.customer_details ? attachments.customer_details : '';

				if (attachments.length !== 0) {
					var listItem = $("<li></li>");

					if (order_details) {
						listItem.append("<div><strong>" + bm_normal_object.order_details + "</strong>: <a target='_blank' href='" + order_details + "'>" + bm_normal_object.order_details_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div><br>");
					}

					if (customer_details) {
						listItem.append("<div><strong>" + bm_normal_object.customer_details + "</strong>: <a target='_blank' href='" + customer_details + "'>" + bm_normal_object.customer_details_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div>");
					}

					$("#attachments-list").append(listItem);
				} else {
					$("#attachments-list").append("<div class='no_attachments'>" + bm_normal_object.no_attachments + "</div>");
				}
			} else {
				$("#attachments-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#order-attachments-dialog").dialog("open");
		});
	});

	// Order attachments dialogue
	$(document).on("click", ".show-failed-order-attachments", function () {
		var data = { 'action': 'bm_fetch_attachments_for_failed_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			$("#attachments-list").empty();

			if (status == true) {
				var attachments = jsondata.attachments;
				var order_details = attachments.order_details ? attachments.order_details : '';
				var customer_details = attachments.customer_details ? attachments.customer_details : '';

				if (attachments.length !== 0) {
					var listItem = $("<li></li>");

					if (order_details) {
						listItem.append("<div><strong>" + bm_normal_object.order_details + "</strong>: <a target='_blank' href='" + order_details + "'>" + bm_normal_object.order_details_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div><br>");
					}

					if (customer_details) {
						listItem.append("<div><strong>" + bm_normal_object.customer_details + "</strong>: <a target='_blank' href='" + customer_details + "'>" + bm_normal_object.customer_details_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div>");
					}

					$("#attachments-list").append(listItem);
				} else {
					$("#attachments-list").append("<div class='no_attachments'>" + bm_normal_object.no_attachments + "</div>");
				}
			} else {
				$("#attachments-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#order-attachments-dialog").dialog("open");
		});
	});

	// Order attachments dialogue
	$(document).on("click", ".show-order-ticket", function () {
		var data = { 'action': 'bm_fetch_attachments_for_order', 'order_id': $(this).attr('id'), 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			$("#ticket-list").empty();

			if (status == true) {
				var attachments = jsondata.attachments;
				var order_details = attachments.order_details ? attachments.order_details : '';

				if (attachments.length !== 0) {
					var listItem = $("<li></li>");

					if (order_details) {
						listItem.append("<div><strong>" + bm_normal_object.order_details + "</strong>: <a target='_blank' href='" + order_details + "'>" + bm_normal_object.order_ticket_pdf + "&nbsp;<i class='fa fa-file-pdf-o' style='font-size:16px;color:red'></i></a></div><br>");
					}

					$("#ticket-list").append(listItem);
				} else {
					$("#ticket-list").append("<div class='no_attachments'>" + bm_normal_object.no_attachments + "</div>");
				}
			} else {
				$("#ticket-list").append("<div class='error_msg'>" + bm_error_object.server_error + "</div>");
			}
			$("#checkin-attachments-dialog").dialog("open");
		});
	});
});


// Event handler for adding a new product
jQuery(document).on("change", "#booking_date", function () {
	jQuery('#service_category').val('');
	resetOrderPage();
});


// Fetch bookable services
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
				if (extras.length != 0) {
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




// Reset order page
function resetOrderPage() {
	jQuery('#service_id').prop('disabled', true);
	jQuery('#service_id').html('');
	resetNoOfServiceSelection();
	resetTimeSlots();
	resetOrderPageServicePrice();
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




// Reset order page service content
function resetTimeSlots() {
	jQuery('#booking_slots').prop('disabled', true);
	jQuery('#booking_slots').html('');
}




// Reset order page customer details content
function resetCustomerDetails() {
	jQuery('.billing_details').hide();
	jQuery('.shipping_details').hide();
}




// Validate Order Page form
function order_form_validation() {
	jQuery('.order_field_errortext').html('');
	jQuery('.order_field_errortext').hide();
	jQuery('.all_order_error_text').html('');

	var tel_pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;

	jQuery('.bm_order_field_required').each(
		function (index, element) {
			var value = jQuery(this).children('select').length != 0 ? jQuery.trim(jQuery(this).children('select').val()) : jQuery.trim(jQuery(this).children('input').val());

			if (jQuery(this).closest('table').attr('id') == 'billing_details' || jQuery(this).closest('table').attr('id') == 'shipping_details') {
				if (jQuery(this).closest('table').is(':visible')) {
					var type = jQuery(this).children('input').attr('type');

					if (type == 'email') {
						var pattern = /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i;

						if (value == "") {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
							jQuery(this).children('.order_field_errortext').show();
						} else if (!pattern.test(value)) {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.invalid_email);
							jQuery(this).children('.order_field_errortext').show();
						}

					} else if (type == 'tel') {

						if (value == "") {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
							jQuery(this).children('.order_field_errortext').show();
						} else if (!tel_pattern.test(value)) {
							jQuery(this).children('.order_field_errortext').html(bm_error_object.invalid_contact);
							jQuery(this).children('.order_field_errortext').show();
						}

					} else if (value == "") {
						jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
						jQuery(this).children('.order_field_errortext').show();
					}
				}
			} else {
				if (value == "") {
					jQuery(this).children('.order_field_errortext').html(bm_error_object.required_field);
					jQuery(this).children('.order_field_errortext').show();
				}
			}
		}
	);

	if (jQuery(document).find('#billing_contact').val() == '') {
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').html(bm_error_object.required_field);
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').show();
	} else if (!tel_pattern.test(jQuery(document).find('#billing_contact').val())) {
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').html(bm_error_object.invalid_contact);
		jQuery('td.order_billing_tel_input').find('.order_field_errortext').show();
	}

	if (jQuery(document).find('#shipping_contact').val() == '') {
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').html(bm_error_object.required_field);
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').show();
	} else if (!tel_pattern.test(jQuery(document).find('#billing_contact').val())) {
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').html(bm_error_object.invalid_contact);
		jQuery('td.order_shipping_tel_input').find('.order_field_errortext').show();
	}

	var b = '';
	b = jQuery('.order_field_errortext').each(
		function () {
			var a = jQuery(this).html();
			b = a + b;
			jQuery('.all_order_error_text').html(b);
		}
	);

	var error = jQuery('.all_order_error_text').html();

	if (error == '') {
		return true;
	} else {
		return false;
	}

}




// Event handler for checking additional number of persons for a service order
function check_for_more_persons($this) {
	if (jQuery($this).is(':checked')) {
		jQuery('.add_more_person_section').show();
		jQuery('#add_more_persons').prop('disabled', false);
	} else {
		jQuery('.add_more_person_section').hide();
		jQuery('#add_more_persons').prop('disabled', true);
	}
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




// Change backend order status
function bm_change_order_status_to_complete_or_cancelled($this) {

	if (jQuery($this).val() == 'completed' || jQuery($this).val() == 'cancelled') {
		if (confirm(bm_normal_object.sure_complete_order)) {

			var post = {
				'status': jQuery($this).val(),
				'id': jQuery($this).attr('id'),
			}

			var data = { 'action': 'bm_change_order_status_to_complete_or_cancelled', 'post': post, 'nonce': bm_ajax_object.nonce };

			jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
				var jsondata = jQuery.parseJSON(response);
				if (jsondata.status == true) {
					location.reload();
				} else {
					alert(bm_error_object.service_error);
				}
			});
		} else {
			jQuery($this).val('on_hold');
		}
	}
}




// Change frontend order status
function bm_change_order_status($this) {
	if (confirm(bm_normal_object.sure_change_status)) {

		var post = {
			'status': jQuery($this).val(),
			'id': jQuery($this).attr('id'),
		}

		var data = { 'action': 'bm_change_order_status', 'post': post, 'nonce': bm_ajax_object.nonce };

		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				location.reload();
			} else {
				alert(bm_error_object.service_error);
			}
		});
	}
}




// Fetch order columns
jQuery(document).on('click', '.edit_order_columns', function (e) {
	e.preventDefault();

	var data = { 'action': 'bm_fetch_columns_screen_options', 'type': 'orders', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#order_columns').html('');
		if (response != null && response != '') {
			jQuery('#order_columns').html(response);
			jQuery('#order_columns_modal').addClass('active-modal');
			sortable_columns('available_columns');
		} else {
			jQuery('#order_columns').html(bm_error_object.server_error);
			jQuery('#order_columns_modal').addClass('active-modal');
		}
	});
});



// Fetch checkin columns
jQuery(document).on('click', '.edit_checkin_columns', function (e) {
	e.preventDefault();

	var data = { 'action': 'bm_fetch_columns_screen_options', 'type': 'checkin', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#checkin_columns').html('');
		if (response != null && response != '') {
			jQuery('#checkin_columns').html(response);
			jQuery('#checkin_columns_modal').addClass('active-modal');
			sortable_columns('available_columns');
		} else {
			jQuery('#checkin_columns').html(bm_error_object.server_error);
			jQuery('#checkin_columns_modal').addClass('active-modal');
		}
	});
});




// Sort columns
function sortable_columns(id) {
	jQuery(document).find("#" + id).sortable({
		axis: "y",
		items: '> :not(.disabled)',
		cursor: "move",
		revert: true,
		update: function (event, ui) {
			var defaultArr = {};
			var orderArr = [];
			var nameArr = [];
			var textArr = [];
			jQuery(this).find("div").each(function () {
				var main_order = jQuery(this).find("input").attr("order");
				var name = jQuery(this).find("input").attr("name");
				var text = jQuery(this).find("label").text();

				orderArr.push(main_order);
				nameArr.push(name);
				textArr.push(text);

				if (jQuery(this).find("input").is(':checked')) {
					defaultArr[name] = text;
				}
			});

			var is_admin = jQuery("#is_admin").val();
			var view_type = jQuery("#view_type").val();

			var post = {
				"default": defaultArr,
				"orders": orderArr,
				"names": nameArr,
				"texts": textArr,
				"is_admin": is_admin,
				"view_type": view_type
			};

			var data = { 'action': 'bm_save_columns_screen_options', 'post': post, 'nonce': bm_ajax_object.nonce };
			jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
				var jsondata = jQuery.parseJSON(response);
				if (jsondata.status == true) {
					// location.reload();
				} else {
					// alert(bm_error_object.server_error);
				}
			});
		}
	});
}




// Save selected order listing columns
jQuery(document).on('click', '.submit_columns', function (e) {
	e.preventDefault();
	jQuery(document).find('.column_errortext').html('');
	var defaultArr = {};
	var orderArr = [];
	var nameArr = [];
	var textArr = [];

	jQuery('#available_columns').find("div").each(function () {
		var main_order = jQuery(this).find("input").attr("order");
		var name = jQuery(this).find("input").attr("name");
		var text = jQuery(this).find("label").text();

		orderArr.push(main_order);
		nameArr.push(name);
		textArr.push(text);

		if (jQuery(this).find("input").is(':checked')) {
			defaultArr[name] = text;
		}
	});

	var is_admin = jQuery("#is_admin").val();
	var view_type = jQuery("#view_type").val();

	var post = {
		"default": defaultArr,
		"orders": orderArr,
		"names": nameArr,
		"texts": textArr,
		"is_admin": is_admin,
		"view_type": view_type
	};

	if (jQuery('#available_columns input:checkbox:checked').length > 0) {
		var data = { 'action': 'bm_save_columns_screen_options', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			if (jsondata.status == true) {
				jQuery('#order_columns_modal').removeClass('active-modal');
				jQuery('#order_columns').html('');
				jQuery('#checkin_columns_modal').removeClass('active-modal');
				jQuery('#checkin_columns').html('');
				location.reload();
			} else {
				jQuery(document).find('.column_errortext').html(bm_error_object.server_error);
				jQuery(document).find('.column_errortext').show();
			}
		});
	} else {
		jQuery(document).find('.column_errortext').html(bm_normal_object.atleast_one_checked);
		jQuery(document).find('.column_errortext').show();
	}
});




jQuery(document).ready(function ($) {
	var current_screen = bm_normal_object.current_screen;

	if (current_screen == 'flexibooking_page_bm_all_orders') {
		var data = { 'action': 'bm_fetch_saved_order_search', 'module': 'orders', 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var saved_search = jQuery.parseJSON(response);
			if (saved_search != null && saved_search != "") {
				if (typeof (saved_search.global_search) != "undefined") {
					$('#global_search').val(saved_search.global_search ? saved_search.global_search : '');
				}
				if (typeof (saved_search.service_from) != "undefined") {
					$('#service_from').val(saved_search.service_from ? saved_search.service_from : '');
				}
				if (typeof (saved_search.service_to) != "undefined") {
					$('#service_to').val(saved_search.service_to ? saved_search.service_to : '');
				}
				if (typeof (saved_search.order_from) != "undefined") {
					$('#order_from').val(saved_search.order_from ? saved_search.order_from : '');
				}
				if (typeof (saved_search.order_to) != "undefined") {
					$('#order_to').val(saved_search.order_to ? saved_search.order_to : '');
				}
				if ((typeof (saved_search.service_from) != "undefined" && saved_search.service_from != '') || (typeof (saved_search.service_to) != "undefined" && saved_search.service_to != '') || (typeof (saved_search.order_from) != "undefined" && saved_search.order_from != '') || (typeof (saved_search.order_to) != "undefined" && saved_search.order_to != '') || (typeof (saved_search.order_status) != "undefined" && saved_search.order_status != '') || (typeof (saved_search.payment_status) != "undefined" && saved_search.payment_status != '')) {
					$("#order_advanced_search_box").slideDown("slow");
				}
				
				if (typeof(saved_search.order_source) != "undefined" && saved_search.order_source != '') {
					$('#order_source_filter').val(saved_search.order_source).trigger('change');
				}
				
				if (typeof(saved_search.order_status) != "undefined" && saved_search.order_status != '') {
					var orderStatusArray = saved_search.order_status.split(',');
					$('#order_status_filter').val(orderStatusArray);
					$('#order_status_filter').multiselect('reload');
				}

				if (typeof(saved_search.payment_status) != "undefined" && saved_search.payment_status != '') {
					var paymentStatusArray = saved_search.payment_status.split(',');
					$('#payment_status_filter').val(paymentStatusArray);
					$('#payment_status_filter').multiselect('reload');
				}
				
				if (typeof(saved_search.services) != "undefined" && saved_search.services != '') {
						var servicesArray = saved_search.services.split(',');
						jQuery('#service_filter').val(servicesArray);
						jQuery('#service_filter').multiselect('reload');
				}
				
				if (typeof(saved_search.categories) != "undefined" && saved_search.categories != '') {
					var categoriesArray = saved_search.categories.split(',');
					jQuery('#category_filter').val(categoriesArray);
					jQuery('#category_filter').multiselect('reload');
				}
			}

			if ($('#order_type').val() == 'failed') {
				bm_fetch_failed_order_as_per_search('save_search');
			} else if ($('#order_type').val() == 'archived') {
				bm_fetch_archived_order_as_per_search('save_search');
			} else {
				bm_search_order_data('save_search');
			}
		});
	}

	if (current_screen == 'flexibooking_page_bm_check_ins') {
		var data = { 'action': 'bm_fetch_saved_checkin_search', 'module': 'checkin', 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var saved_search = jQuery.parseJSON(response);
			if (saved_search != null && saved_search != "") {
				if (typeof (saved_search.global_search) != "undefined") {
					$('#checkin_global_search').val(saved_search.global_search ? saved_search.global_search : '');
				}
				if (typeof (saved_search.service_from) != "undefined") {
					$('#checkin_service_from').val(saved_search.service_from ? saved_search.service_from : '');
				}
				if (typeof (saved_search.service_to) != "undefined") {
					$('#checkin_service_to').val(saved_search.service_to ? saved_search.service_to : '');
				}
				if (typeof (saved_search.checkin_from) != "undefined") {
					$('#checkin_from').val(saved_search.checkin_from ? saved_search.checkin_from : '');
				}
				if (typeof (saved_search.checkin_to) != "undefined") {
					$('#checkin_to').val(saved_search.checkin_to ? saved_search.checkin_to : '');
				}
				if ((typeof (saved_search.service_from) != "undefined" && saved_search.service_from != '') || (typeof (saved_search.service_to) != "undefined" && saved_search.service_to != '') || (typeof (saved_search.checkin_from) != "undefined" && saved_search.checkin_from != '') || (typeof (saved_search.checkin_to) != "undefined" && saved_search.checkin_to != '')) {
					$("#checkin_advanced_search_box").slideDown("slow");
				}
			}
				bm_search_checkin_data('save_search');
		});
	}

	if (current_screen == 'toplevel_page_bm_home') {
		var data = { 'action': 'bm_fetch_saved_order_search', 'module': 'dashboard_upcoming_orders', 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var saved_search = jQuery.parseJSON(response);
			if (saved_search != null && saved_search != "") {
				if (typeof (saved_search.service_from) != "undefined") {
					$('#dashboard_upcoming_orders_service_from').val(saved_search.service_from ? saved_search.service_from : '');
				}
				if (typeof (saved_search.service_to) != "undefined") {
					$('#dashboard_upcoming_orders_service_to').val(saved_search.service_to ? saved_search.service_to : '');
				}
				if (typeof (saved_search.order_from) != "undefined") {
					$('#dashboard_upcoming_orders_order_from').val(saved_search.order_from ? saved_search.order_from : '');
				}
				if (typeof (saved_search.order_to) != "undefined") {
					$('#dashboard_upcoming_orders_order_to').val(saved_search.order_to ? saved_search.order_to : '');
				}
			}
			bm_fetch_upcoming_orders('1', 'search');
		});

		bm_fetch_booking_counts();
		bm_fetch_customer_and_total_booked_slot_counts('total');
		bm_fetch_booking_status_counts();
		bm_fetch_booking_overview();
	}


	// Fetch results per page
	$(document).on('click', 'div.dashboard_table a.page-numbers', function (e) {
		e.preventDefault();
		var id = $(this).parents('div').attr('id');
		var divClass = id.split('_pagination')[0];
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];

		if (divClass == 'dashboard_all_orders') {
			$('#all_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_dashboard_order_data_global_search(pagenum ? pagenum : '1');
		}

		if (divClass == 'dashboard_upcoming_orders') {
			$('#upcoming_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_upcoming_orders(pagenum ? pagenum : '1');
		}

		if (divClass == 'dashoboard_weekly_orders') {
			$('#weekly_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_dashboard_weekly_orders(pagenum ? pagenum : '1');
		}

		if (divClass == 'dashoboard_cat_wise_orders') {
			$('#cat_wise_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_cat_wise_orders(pagenum ? pagenum : '1');
		}

		if (divClass == 'dashboard_revenue_orders') {
			$('#revenue_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_service_wise_revenue(pagenum ? pagenum : '1');
		}

		if (divClass == 'dashboard_datewise_revenue_orders') {
			$('#datewise_revenue_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_datewise_revenue_orders(pagenum ? pagenum : '1');
		}

		if (divClass == 'dashboard_customer_wise_revenue_orders') {
			$('#customer_wise_revenue_orders_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_customer_wise_revenue_orders(pagenum ? pagenum : '1');
		}
	});

	intitializeMultiselect('search_order_by_category_id');
	intitializeMultiselect('search_order_by_service_id');
	intitializeMultiselect('checkin_service_advanced_filter');
	intitializeMultiselect('order_status_filter');
    intitializeMultiselect('payment_status_filter');
	intitializeMultiselect('service_filter');
    intitializeMultiselect('category_filter');
	intitializeMultiselect('manual_checkin_service');

	service_from = $("#service_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
		.on("change", function () {
			service_to.datepicker("option", "minDate", this.value);
			service_to.datepicker("setDate", this.value);
		}),
		service_to = $("#service_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
			.on("change", function () {
				service_from.datepicker("option", "maxDate", this.value);
			});

	order_from = $("#order_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
		.on("change", function () {
			order_to.datepicker("option", "minDate", this.value);
			order_to.datepicker("setDate", this.value);
		}),
		order_to = $("#order_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
			.on("change", function () {
				order_from.datepicker("option", "maxDate", this.value);
			});

	checkin_service_from = $("#checkin_service_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
		.on("change", function () {
			checkin_service_to.datepicker("option", "minDate", this.value);
			checkin_service_to.datepicker("setDate", this.value);
		}),
		checkin_service_to = $("#checkin_service_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
			.on("change", function () {
				checkin_service_from.datepicker("option", "maxDate", this.value);
			});

	checkin_from = $("#checkin_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
		.on("change", function () {
			checkin_to.datepicker("option", "minDate", this.value);
			checkin_to.datepicker("setDate", this.value);
		}),
		checkin_to = $("#checkin_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 3
		})
			.on("change", function () {
				checkin_from.datepicker("option", "maxDate", this.value);
			});

	dashboard_weekly_service_from = $("#weekly_service_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			changeMonth: true,
		})
		.on("change", function () {
			var date = dashboard_weekly_service_from.datepicker('getDate', '+7d');
			date.setDate(date.getDate() + 7);
			dashboard_weekly_service_to.datepicker('setDate', date);
		}),
		dashboard_weekly_service_to = $("#weekly_service_to").datepicker({
			dateFormat: "dd/mm/y",
			changeMonth: true,
		})
			.on("change", function () {
				var date = dashboard_weekly_service_to.datepicker('getDate', '-7d');
				date.setDate(date.getDate() - 7);
				dashboard_weekly_service_from.datepicker('setDate', date);
			});

	dashboard_datewise_revenue_order_from = $("#datewise_revenue_order_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			changeMonth: true,
		})
		.on("change", function () {
			var date = dashboard_datewise_revenue_order_from.datepicker('getDate', '+1d');
			date.setDate(date.getDate() + 1);
			dashboard_datewise_revenue_order_to.datepicker("option", "minDate", this.value);
			dashboard_datewise_revenue_order_to.datepicker('setDate', date);
		}),
		dashboard_datewise_revenue_order_to = $("#datewise_revenue_order_to").datepicker({
			dateFormat: "dd/mm/y",
			changeMonth: true,
		})
			.on("change", function () {
				dashboard_datewise_revenue_order_from.datepicker("option", "maxDate", this.value);
			});

	dashboard_customer_wise_revenue_order_from = $("#customer_wise_revenue_order_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			changeMonth: true,
		})
		.on("change", function () {
			var date = dashboard_datewise_revenue_order_from.datepicker('getDate', '+1d');
			date.setDate(date.getDate() + 1);
			dashboard_datewise_revenue_order_to.datepicker("option", "minDate", this.value);
			dashboard_datewise_revenue_order_to.datepicker('setDate', date);
		}),
		dashboard_customer_wise_revenue_order_to = $("#customer_wise_revenue_order_to").datepicker({
			dateFormat: "dd/mm/y",
			changeMonth: true,
		})
			.on("change", function () {
				dashboard_datewise_revenue_order_from.datepicker("option", "maxDate", this.value);
			});


	dashboard_all_orders_service_from = $("#dashboard_all_orders_service_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
		.on("change", function () {
			dashboard_all_orders_service_to.datepicker("option", "minDate", this.value);
			dashboard_all_orders_service_to.datepicker("setDate", this.value);
		}),
		dashboard_all_orders_service_to = $("#dashboard_all_orders_service_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
			.on("change", function () {
				dashboard_all_orders_service_from.datepicker("option", "maxDate", this.value);
			});

	dashboard_all_orders_order_from = $("#dashboard_all_orders_order_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
		.on("change", function () {
			dashboard_all_orders_order_to.datepicker("option", "minDate", this.value);
			dashboard_all_orders_order_to.datepicker("setDate", this.value);
		}),
		dashboard_all_orders_order_to = $("#dashboard_all_orders_order_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
			.on("change", function () {
				dashboard_all_orders_order_from.datepicker("option", "maxDate", this.value);
			});


	dashboard_upcoming_orders_service_from = $("#dashboard_upcoming_orders_service_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
		.on("change", function () {
			dashboard_upcoming_orders_service_to.datepicker("option", "minDate", this.value);
			dashboard_upcoming_orders_service_to.datepicker("setDate", this.value);
		}),
		dashboard_upcoming_orders_service_to = $("#dashboard_upcoming_orders_service_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
			.on("change", function () {
				dashboard_upcoming_orders_service_from.datepicker("option", "maxDate", this.value);
			});

	dashboard_upcoming_orders_order_from = $("#dashboard_upcoming_orders_order_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
		.on("change", function () {
			dashboard_upcoming_orders_order_to.datepicker("option", "minDate", this.value);
			dashboard_upcoming_orders_order_to.datepicker("setDate", this.value);
		}),
		dashboard_upcoming_orders_order_to = $("#dashboard_upcoming_orders_order_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
			.on("change", function () {
				dashboard_upcoming_orders_order_from.datepicker("option", "maxDate", this.value);
			});

	dashboard_booking_status_from = $("#status_from")
		.datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
		.on("change", function () {
			var date = dashboard_datewise_revenue_order_from.datepicker('getDate', '+1d');
			date.setDate(date.getDate() + 1);
			dashboard_booking_status_to.datepicker("option", "minDate", this.value);
			if ($("#status_to").val(0 == '')) {
				dashboard_booking_status_to.datepicker('setDate', date);
			}
		}),
		dashboard_booking_status_to = $("#status_to").datepicker({
			dateFormat: "dd/mm/y",
			defaultDate: "+1w",
			changeMonth: true,
			numberOfMonths: 1
		})
			.on("change", function () {
				dashboard_booking_status_from.datepicker("option", "maxDate", this.value);
			});

});



// Fetch customer and slot booking counts
function bm_fetch_customer_and_total_booked_slot_counts(type = '') {
	var post = {
		'type': type,
	}

	var data = { 'action': 'bm_fetch_customer_and_total_booked_slot_counts', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery('.slots_booked_count').text(jsondata.slots_booked_count ? jsondata.slots_booked_count : '0');
			jQuery('.total_customers_count').text(jsondata.total_customers_count ? jsondata.total_customers_count : '0');
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch booking status counts
function bm_fetch_booking_status_counts() {
	var post = {
		'type': jQuery('#booking_status_yearly_or_monthly').val(),
		'status': jQuery('#booking_status_value').val(),
		'from': jQuery('#status_from').val(),
		'to': jQuery('#status_to').val(),
	}

	var data = { 'action': 'bm_fetch_booking_status_counts', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true && typeof (jsondata.labels) != "undefined" && typeof (jsondata.data) != "undefined") {
			destroyChart();
			renderStatusChart(jsondata.labels, jsondata.data);
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Destroy a chart
function destroyChart() {
	if (window.dashboardChart != null) {
		window.dashboardChart.destroy();
	}
}



// Fetch booking overview data for dashboard
function bm_fetch_booking_overview() {
	var data = { 'action': 'bm_fetch_booking_overview', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (typeof (jsondata.comparison) != "undefined" && typeof (jsondata.data) != "undefined") {
			jQuery(document).find('.booking_increase_percent').html(jsondata.comparison);
			jQuery(document).find('.booking_overview_data').html(jsondata.data);
		} else {
			alert(bm_error_object.server_error);
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



// Clicking on booking overview tag on dashboard
jQuery(document).on('click', '.booking_overview_tag', function (e) {
	e.preventDefault();
});



// Fetch order search data by date
jQuery(document).on('click', '#date_search_button', function (e) {
	e.preventDefault();
	if (jQuery('#order_type').val() == 'failed') {
		bm_fetch_failed_order_as_per_search('save_search');
	} else if (jQuery('#order_type').val() == 'archived') {
		bm_fetch_archived_order_as_per_search('save_search');
	} else {
		bm_search_order_data('save_search');
	}
});



// Fetch checkin search data by date
jQuery(document).on('click', '#checkin_date_search_button', function (e) {
	e.preventDefault();
	bm_search_checkin_data('save_search');
});



// Fetch order search data by date
jQuery(document).on('click', '#dashboard_all_orders_date_search_button', function (e) {
	e.preventDefault();
	bm_dashboard_order_data_global_search('1', 'save_search');
});



// Fetch order search data by date
jQuery(document).on('click', '#dashboard_upcoming_orders_date_search_button', function (e) {
	e.preventDefault();
	bm_fetch_upcoming_orders('1', 'save_search');
});



// Fetch dashoboard searched order data
function bm_dashboard_order_data_global_search(pagenum = '', type = '') {
	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#all_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'service_from': jQuery('#dashboard_all_orders_service_from').val(),
		'service_to': jQuery('#dashboard_all_orders_service_to').val(),
		'order_from': jQuery('#dashboard_all_orders_order_from').val(),
		'order_to': jQuery('#dashboard_all_orders_order_to').val(),
		'search_string': jQuery('#dashboard_global_search').val(),
		'type': type,
	}

	var data = { 'action': 'bm_fetch_dashoboard_order_global_search', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashboard_all_orders").html('');
			jQuery("#dashboard_all_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined" && typeof (jsondata.saved_search) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;
				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});

				var saved_search = jsondata.saved_search;

				if (saved_search != '' && saved_search != null) {
					jQuery('#dashboard_global_search').val(typeof (saved_search.global_search) != "undefined" ? saved_search.global_search : '');
					jQuery('#dashboard_all_orders_service_from').val(typeof (saved_search.service_from) != "undefined" ? saved_search.service_from : '');
					jQuery('#dashboard_all_orders_service_to').val(typeof (saved_search.service_to) != "undefined" ? saved_search.service_to : '');
					jQuery('#dashboard_all_orders_order_from').val(typeof (saved_search.order_from) != "undefined" ? saved_search.order_from : '');
					jQuery('#dashboard_all_orders_order_to').val(typeof (saved_search.order_to) != "undefined" ? saved_search.order_to : '');

					// if (saved_search.service_from != '' || saved_search.service_to != '' || saved_search.order_from != '' || saved_search.order_to != '') {
					// 	jQuery("#dashboard_all_orders_advanced_search_box").slideDown("slow");
					// }
				}

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align: center;' title='" + bookings[i].service_name + "'>" + bookings[i].service_name.substring(0, 20) + '...' + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "<td style='text-align: center;'><div class='show-customer-dialog linkText' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-file' aria-hidden='true'></i></div></td>";
						orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
						orderListing += "<td style='text-align: center;'>";
						orderListing += status_values[jQuery.inArray(bookings[i].order_status, status_keys)];
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashboard_all_orders").append(orderListing);
					jQuery("#dashboard_all_orders_pagination").append(pagination);
				} else {
					jQuery(".dashboard_all_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch dashoboard upcoming order data
function bm_fetch_upcoming_orders(pagenum = '', type = '') {
	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#upcoming_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'service_from': jQuery('#dashboard_upcoming_orders_service_from').val(),
		'service_to': jQuery('#dashboard_upcoming_orders_service_to').val(),
		'order_from': jQuery('#dashboard_upcoming_orders_order_from').val(),
		'order_to': jQuery('#dashboard_upcoming_orders_order_to').val(),
		'type': type,
	}

	var data = { 'action': 'bm_fetch_upcoming_orders', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashboard_upcoming_orders").html('');
			jQuery("#dashboard_upcoming_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined" && typeof (jsondata.saved_search) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;
				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});

				var saved_search = jsondata.saved_search;

				if (saved_search != '' && saved_search != null) {
					jQuery('#dashboard_upcoming_orders_service_from').val(typeof (saved_search.service_from) != "undefined" ? saved_search.service_from : '');
					jQuery('#dashboard_upcoming_orders_service_to').val(typeof (saved_search.service_to) != "undefined" ? saved_search.service_to : '');
					jQuery('#dashboard_upcoming_orders_order_from').val(typeof (saved_search.order_from) != "undefined" ? saved_search.order_from : '');
					jQuery('#dashboard_upcoming_orders_order_to').val(typeof (saved_search.order_to) != "undefined" ? saved_search.order_to : '');
				}

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align:center;' title='" + bookings[i].service_name + "'>" + bookings[i].service_name.substring(0, 20) + '...' + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
						orderListing += "<td style='text-align: center;'><div class='show-customer-dialog linkText' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-file' aria-hidden='true'></i></div></td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
						orderListing += "<td style='text-align: center;'>";
						orderListing += status_values[jQuery.inArray(bookings[i].order_status, status_keys)];
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashboard_upcoming_orders").append(orderListing);
					jQuery("#dashboard_upcoming_orders_pagination").append(pagination);
				} else {
					jQuery(".dashboard_upcoming_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch dashoboard weekly searched order data
function bm_fetch_dashboard_weekly_orders(pagenum = '', type = '') {
	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#weekly_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'service_from': jQuery('#weekly_service_from').val(),
		'service_to': jQuery('#weekly_service_to').val(),
		'type': type,
	}

	var data = { 'action': 'bm_fetch_dashboard_weekly_orders', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashoboard_weekly_orders").html('');
			jQuery("#dashoboard_weekly_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;
				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align: center;' title='" + bookings[i].service_name + "'>" + bookings[i].service_name.substring(0, 20) + '...' + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "<td style='text-align: center;'><div class='show-customer-dialog linkText' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-file' aria-hidden='true'></i></div></td>";
						orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
						orderListing += "<td style='text-align: center;'>";
						orderListing += status_values[jQuery.inArray(bookings[i].order_status, status_keys)];
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashoboard_weekly_orders").append(orderListing);
					jQuery("#dashoboard_weekly_orders_pagination").append(pagination);
				} else {
					jQuery(".dashoboard_weekly_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch dashoboard category wise searched order data
function bm_fetch_cat_wise_orders(pagenum = '', type = '') {
	var categories = [];
	jQuery("#search_order_by_category_id option:selected").each(function () {
		var id = jQuery(this).val();
		categories.push(id);
	});

	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#cat_wise_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'categories': categories,
		'type': type,
	}

	var data = { 'action': 'bm_fetch_cat_wise_orders', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashoboard_cat_wise_orders").html('');
			jQuery("#dashoboard_cat_wise_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;

				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].category + " </td>";
						orderListing += "<td style='text-align: center;' title='" + bookings[i].service_name + "'>" + bookings[i].service_name.substring(0, 20) + '...' + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
						orderListing += "<td style='text-align: center;'>";
						orderListing += status_values[jQuery.inArray(bookings[i].order_status, status_keys)];
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashoboard_cat_wise_orders").append(orderListing);
					jQuery("#dashoboard_cat_wise_orders_pagination").append(pagination);
				} else {
					jQuery(".dashoboard_cat_wise_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch dashoboard revenue wise searched order data
function bm_fetch_service_wise_revenue(pagenum = '', type = '') {
	var services = [];
	jQuery("#search_order_by_service_id option:selected").each(function () {
		var id = jQuery(this).val();
		services.push(id);
	});

	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#revenue_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'services': services,
		'type': type,
	}

	var data = { 'action': 'bm_fetch_service_wise_revenue', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashboard_revenue_orders").html('');
			jQuery("#dashboard_revenue_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align: center;' title='" + bookings[i].ordered_service + "'>" + bookings[i].ordered_service.substring(0, 20) + '...' + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_orders + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_slots_booked + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_extra_slots_booked + " </td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashboard_revenue_orders").append(orderListing);
					jQuery("#dashboard_revenue_orders_pagination").append(pagination);
				} else {
					jQuery(".dashboard_revenue_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch dashoboard datewise revenue searched order data
function bm_fetch_datewise_revenue_orders(pagenum = '', type = '') {

	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#datewise_revenue_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'order_from': jQuery.trim(jQuery('#datewise_revenue_order_from').val()),
		'order_to': jQuery.trim(jQuery('#datewise_revenue_order_to').val()),
		'type': type,
	}

	var data = { 'action': 'bm_fetch_datewise_revenue_orders', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashboard_datewise_revenue_orders").html('');
			jQuery("#dashboard_datewise_revenue_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].order_date_time + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_orders + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_slots_booked + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_extra_slots_booked + " </td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashboard_datewise_revenue_orders").append(orderListing);
					jQuery("#dashboard_datewise_revenue_orders_pagination").append(pagination);
				} else {
					jQuery(".dashboard_datewise_revenue_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch customer wise searched order data
function bm_fetch_customer_wise_revenue_orders(pagenum = '', type = '') {

	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#customer_wise_revenue_orders_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'search_string': jQuery('#custom_wise_global_search').val(),
		'type': type,
	}

	var data = { 'action': 'bm_fetch_customer_wise_revenue_orders', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".dashboard_customer_wise_revenue_orders").html('');
			jQuery("#dashboard_customer_wise_revenue_orders_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined") {
				var bookings = jsondata.bookings;
				var pagination = jsondata.pagination;
				var current_pagenumber = jsondata.current_pagenumber;

				var orderListing = '';

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].customer_name + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].customer_email + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_orders + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_slots_booked + " </td>";
						orderListing += "<td style='text-align: center;'>" + bookings[i].total_extra_slots_booked + " </td>";
						orderListing += "<td style='text-align: center;'>";
						if (currency_position == 'before') {
							orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
						} else {
							orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
						}
						orderListing += "</td>";
						orderListing += "</form></tr>";
						current_pagenumber++;
					}

					jQuery(".dashboard_customer_wise_revenue_orders").append(orderListing);
					jQuery("#dashboard_customer_wise_revenue_orders_pagination").append(pagination);
				} else {
					jQuery(".dashboard_customer_wise_revenue_orders").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			} else {
				alert(bm_error_object.server_error);
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Reset order search
// Reset order search
jQuery(document).on('click', '#reset_date_search', function (e) {
    e.preventDefault();

    jQuery('#service_from').val('');
    jQuery('#service_to').val('');
    jQuery('#order_from').val('');
    jQuery('#order_to').val('');
    jQuery('#global_search').val('');
    
    jQuery('#checkin_service_from').val('');
    jQuery('#checkin_service_to').val('');
    jQuery('#checkin_from').val('');
    jQuery('#checkin_to').val('');
    jQuery('#checkin_global_search').val('');

	jQuery('#checkin_service_advanced_filter').val([]).multiselect('reload');
    jQuery('#order_source_filter').val('');
    
	jQuery('#order_status_filter').val([]).multiselect('reload');
    jQuery('#payment_status_filter').val([]).multiselect('reload');
	jQuery('#service_filter').val([]).multiselect('reload');
    jQuery('#category_filter').val([]).multiselect('reload');

    jQuery('#service_from').datepicker("option", "maxDate", '');
    jQuery('#service_from').datepicker("option", "minDate", '');
    jQuery('#service_to').datepicker("option", "maxDate", '');
    jQuery('#service_to').datepicker("option", "minDate", '');

    jQuery('#checkin_service_from').datepicker("option", "maxDate", '');
    jQuery('#checkin_service_from').datepicker("option", "minDate", '');
    jQuery('#checkin_service_to').datepicker("option", "maxDate", '');
    jQuery('#checkin_service_to').datepicker("option", "minDate", '');

    jQuery('#order_from').datepicker("option", "maxDate", '');
    jQuery('#order_from').datepicker("option", "minDate", '');
    jQuery('#order_to').datepicker("option", "maxDate", '');
    jQuery('#order_to').datepicker("option", "minDate", '');

    jQuery('#checkin_from').datepicker("option", "maxDate", '');
    jQuery('#checkin_from').datepicker("option", "minDate", '');
    jQuery('#checkin_to').datepicker("option", "maxDate", '');
    jQuery('#checkin_to').datepicker("option", "minDate", '');

    if (jQuery('#order_type').val() == 'failed') {
		bm_fetch_failed_order_as_per_search('save_search');
	} else if (jQuery('#order_type').val() == 'archived') {
		bm_fetch_archived_order_as_per_search('save_search');
	} else {
		bm_search_order_data('save_search');
	}

    bm_search_checkin_data('save_search');
});



// Reset order search
jQuery(document).on('click', '#dashboard_all_orders_reset_date_search', function (e) {
	e.preventDefault();

	jQuery('#dashboard_all_orders_service_from').val('');
	jQuery('#dashboard_all_orders_service_to').val('');
	jQuery('#dashboard_all_orders_order_from').val('');
	jQuery('#dashboard_all_orders_order_to').val('');
	jQuery('#dashboard_global_search').val('');

	jQuery('#dashboard_all_orders_service_from').datepicker("option", "maxDate", '');
	jQuery('#dashboard_all_orders_service_from').datepicker("option", "minDate", '');
	jQuery('#dashboard_all_orders_service_to').datepicker("option", "maxDate", '');
	jQuery('#dashboard_all_orders_service_to').datepicker("option", "minDate", '');

	jQuery('#dashboard_all_orders_order_from').datepicker("option", "maxDate", '');
	jQuery('#dashboard_all_orders_order_from').datepicker("option", "minDate", '');
	jQuery('#dashboard_all_orders_order_to').datepicker("option", "maxDate", '');
	jQuery('#dashboard_all_orders_order_to').datepicker("option", "minDate", '');

	bm_dashboard_order_data_global_search('1', 'save_search');
});



// Reset order search
jQuery(document).on('click', '#dashboard_upcoming_orders_reset_date_search', function (e) {
	e.preventDefault();

	jQuery('#dashboard_upcoming_orders_service_from').val('');
	jQuery('#dashboard_upcoming_orders_service_to').val('');
	jQuery('#dashboard_upcoming_orders_order_from').val('');
	jQuery('#dashboard_upcoming_orders_order_to').val('');

	jQuery('#dashboard_upcoming_orders_service_from').datepicker("option", "maxDate", '');
	jQuery('#dashboard_upcoming_orders_service_from').datepicker("option", "minDate", '');
	jQuery('#dashboard_upcoming_orders_service_to').datepicker("option", "maxDate", '');
	jQuery('#dashboard_upcoming_orders_service_to').datepicker("option", "minDate", '');

	jQuery('#dashboard_upcoming_orders_order_from').datepicker("option", "maxDate", '');
	jQuery('#dashboard_upcoming_orders_order_from').datepicker("option", "minDate", '');
	jQuery('#dashboard_upcoming_orders_order_to').datepicker("option", "maxDate", '');
	jQuery('#dashboard_upcoming_orders_order_to').datepicker("option", "minDate", '');

	bm_fetch_upcoming_orders('1', 'save_search');
});


// Trigger order search
jQuery(document).ready(function ($) {
	// Order lsiting page search
	$('#order_listing_search_icon').on('click', function () {
		if (jQuery('#order_type').val() == 'failed') {
			bm_fetch_failed_order_as_per_search('save_search');
		} else if (jQuery('#order_type').val() == 'archived') {
			bm_fetch_archived_order_as_per_search('save_search');
		} else {
			bm_search_order_data('save_search');
		}
	});

	// Trigger the icon highlight on input
	$('#global_search').on('input', function () {
		if ($(this).val().trim() !== "") {
			$('#order_listing_search_icon').css('color', '#195587');
		} else {
			if (jQuery('#order_type').val() == 'failed') {
				bm_fetch_failed_order_as_per_search('save_search');
			} else if (jQuery('#order_type').val() == 'archived') {
				bm_fetch_archived_order_as_per_search('save_search');
			} else {
				bm_search_order_data('save_search');
			}
			$('#order_listing_search_icon').css('color', '#ccc');
		}
	});

	// checkin lsiting page search
	$('#checkin_listing_search_icon').on('click', function () {
		bm_search_checkin_data('save_search');
	});

	// Trigger the icon highlight on input
	$('#checkin_global_search').on('input', function () {
		if ($(this).val().trim() !== "") {
			$('#checkin_listing_search_icon').css('color', '#195587');
		} else {
			bm_search_checkin_data('save_search');
			$('#checkin_listing_search_icon').css('color', '#ccc');
		}
	});

	// Dashboard page search
	$('#dashboard_all_bookings_search_icon').on('click', function () {
		bm_dashboard_order_data_global_search('1', 'save_search');
	});

	// Trigger the icon highlight on input
	$('#dashboard_global_search').on('input', function () {
		if ($(this).val().trim() !== "") {
			$('#dashboard_all_bookings_search_icon').css('color', '#195587');
		} else {
			bm_dashboard_order_data_global_search('1', 'save_search');
			$('#dashboard_all_bookings_search_icon').css('color', '#ccc');
		}
	});
});


// Search order data
function bm_search_order_data(type = '') {
	var urlParams = new URLSearchParams(window.location.search);
    var orderby = urlParams.get('orderby') || 'id';
    var order = urlParams.get('order') || 'desc';

	var post = {
        'pagenum': jQuery.trim(jQuery('#pagenum').val()),
        'base': jQuery(location).attr("href"),
        'limit': jQuery.trim(jQuery('#limit_count').val()),
        'service_from': jQuery('#service_from').val(),
        'service_to': jQuery('#service_to').val(),
        'order_from': jQuery('#order_from').val(),
        'order_to': jQuery('#order_to').val(),
        'search_string': jQuery.trim(jQuery('#global_search').val()),
        'order_source': jQuery('#order_source_filter').val(),
        'order_status': jQuery('#order_status_filter').val() || [],
    	'payment_status': jQuery('#payment_status_filter').val() || [],
		'services': jQuery('#service_filter').val() || [],
    	'categories': jQuery('#category_filter').val() || [],
        'type': type,
        'orderby': orderby,
        'order': order,
    }

	var data = { 'action': 'bm_fetch_order_as_per_search', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".order_records").html('');
			jQuery("#order_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;
			var num_of_pages = jsondata.num_of_pages ? jsondata.num_of_pages : 0;
			jQuery(document).find("#total_pages").val(num_of_pages);

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.active_columns) != "undefined" && typeof (jsondata.column_values) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined" && typeof (jsondata.saved_search) != "undefined") {
				var bookings = jsondata.bookings;
				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});
				var active_columns = jQuery.map(jsondata.active_columns, function (value, key) {
					return value;
				});
				var column_value_keys = jQuery.map(jsondata.column_values, function (value, key) {
					return key;
				});
				var column_values = jQuery.map(jsondata.column_values, function (value, key) {
					return value;
				});
				var pagination = jsondata.pagination;
				var saved_search = jsondata.saved_search;

				if (saved_search != '' && saved_search != null) {
					jQuery('#global_search').val(typeof(saved_search.global_search) != "undefined" ? saved_search.global_search : '');
					jQuery('#service_from').val(typeof(saved_search.service_from) != "undefined" ? saved_search.service_from : '');
					jQuery('#service_to').val(typeof(saved_search.service_to) != "undefined" ? saved_search.service_to : '');
					jQuery('#order_from').val(typeof(saved_search.order_from) != "undefined" ? saved_search.order_from : '');
					jQuery('#order_to').val(typeof(saved_search.order_to) != "undefined" ? saved_search.order_to : '');
					if (typeof(saved_search.order_source) != "undefined" && saved_search.order_source != '') {
						jQuery('#order_source_filter').val(saved_search.order_source).trigger('change');
					}
					if (typeof(saved_search.order_status) != "undefined" && saved_search.order_status != '') {
						var orderStatusArray = saved_search.order_status.split(',');
						jQuery('#order_status_filter').val(orderStatusArray);
						jQuery('#order_status_filter').multiselect('reload');
					}
					if (typeof(saved_search.payment_status) != "undefined" && saved_search.payment_status != '') {
						var paymentStatusArray = saved_search.payment_status.split(',');
						jQuery('#payment_status_filter').val(paymentStatusArray);
						jQuery('#payment_status_filter').multiselect('reload');
					}
					if (typeof(saved_search.services) != "undefined" && saved_search.services != '') {
						var servicesArray = saved_search.services.split(',');
						jQuery('#service_filter').val(servicesArray);
						jQuery('#service_filter').multiselect('reload');
					}
					if (typeof(saved_search.categories) != "undefined" && saved_search.categories != '') {
						var categoriesArray = saved_search.categories.split(',');
						jQuery('#category_filter').val(categoriesArray);
						jQuery('#category_filter').multiselect('reload');
					}

					if (saved_search.service_from != '' || saved_search.service_to != '' || saved_search.order_from != '' || saved_search.order_to != '' || 
						saved_search.order_source != '' || saved_search.order_status != '' || saved_search.payment_status != '' || saved_search.services != '' || saved_search.categories != '') {
						jQuery("#order_advanced_search_box").slideDown("slow");
					}
				}

				var orderListing = '';
				var current_pagenumber = jsondata.current_pagenumber;

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						for (var j = 0; j < column_values.length; j++) {
							if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
								continue;
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'serial_no') {
								orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
							}
							if (
								typeof column_values[j].column !== "undefined" &&
								column_values[j].column === 'service_name'
							) {
								orderListing +=
									"<td style='text-align:center;width:140px;' title='" + bookings[i].service_name + "'>" +
										"<a href='" + bm_normal_object.admin_side_link +
										"page=bm_single_order&booking_id=" + bookings[i].id + "'>" +
											bookings[i].service_name +
										"</a>" +
									"</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_created_at') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_date') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'first_name') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].first_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'last_name') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].last_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'contact_no') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].contact_no + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'email_address') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].email_address + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_participants') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].service_participants + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'extra_service_participants') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].extra_service_participants + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].service_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].service_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'extra_service_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].extra_service_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].extra_service_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'discount') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].discount);
								} else {
									orderListing += changePriceFormat(bookings[i].discount) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'total_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
								}
								var payment_info = bookings[i].stripe_status + '' + ( bookings[i].updated_paid_at != '' ? convertDateFormat(bookings[i].updated_paid_at, 'atTimeOnDate') : convertDateFormat(bookings[i].paid_at, 'atTimeOnDate') );
								orderListing += "&nbsp;&nbsp;<i class='fa fa-info-circle' aria-hidden='true' title='" + payment_info + "' style='cursor:pointer;'></i>";
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'customer_data') {
								orderListing += "<td style='text-align: center;'><div class='show-customer-dialog linkText' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-file' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'ordered_from') {
								orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'order_status') {
								orderListing += "<td style='text-align: center;'>";
								orderListing += status_values[jQuery.inArray(bookings[i].order_status, status_keys)];
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'payment_status') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].stripe_status + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'order_attachments') {
								orderListing += "<td style='text-align: center;'><div class='show-order-attachments' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-paperclip' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'actions') {
								orderListing += "<td style='text-align: center;width:84px;'>";
								if (bookings[i].booking_type == 'on_request' && bookings[i].is_active == 1 && bookings[i].transaction_status == 'requires_capture') {
									orderListing += "<button type='button' name='approveorder' id='approveorder' title=" + bm_normal_object.approve + " value=" + bookings[i].id + " onclick='bm_approve_bor_order(this)'><i class='fa fa-check' aria-hidden='true' style='color:green;cursor:pointer;'></i></button>";
									orderListing += "<button type='button' name='cancelorder' id='cancelorder' title=" + bm_normal_object.cancel + " value=" + bookings[i].id + " onclick='bm_cancel_bor_order(this)'><i class='fa fa-times' aria-hidden='true' style='cursor:pointer;'></i></button>";
								}
								orderListing += "<button type='button' name='edittransaction' id='" + (bookings[i].is_frontend_booking == 0 ? 'edittransaction' : '') + "' title=" + (bookings[i].is_frontend_booking == 1 ? bm_error_object.transaction_not_editable : bm_normal_object.edit_transaction) + " value=" + bookings[i].id + " onclick='bm_update_transaction(this)' " + (bookings[i].is_frontend_booking == 1 ? 'disabled' : '') + "><i class='fa fa-exchange' aria-hidden='true' style='cursor:pointer;'></i></button>&nbsp;&nbsp;";
								// orderListing += "<button type='button' name='editorder' id='editorder' title="+bm_normal_object.edit+" value="+bookings[i].id+"><i class='fa fa-edit' aria-hidden='true' style='cursor:pointer;'></i></button>";
								orderListing += "<button type='button' name='archiveorder' id='archiveorder' title="+bm_normal_object.archive+" value="+bookings[i].id+"><i class='fa fa-archive' aria-hidden='true' style='color:red;cursor:pointer;'></i></button>";
								orderListing += "</td>";
							}
						}
						orderListing += "</form></tr>";
						current_pagenumber++;
					}
					jQuery(".order_records").append(orderListing);
					jQuery("#order_pagination").append(pagination);

					let prefix = bm_normal_object.total + " = ";

					var totalsRow = "<tr class='totals-row' style='font-weight:bold; background:#f9f9f9;'>";
					for (var j = 0; j < column_values.length; j++) {
						if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
							continue;
						}

						if (typeof(column_values[j].column) != "undefined") {
							let col = column_values[j].column;
							if (col == 'service_participants') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.svc_prtcpants + "</td>";
							} else if (col == 'extra_service_participants') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.ex_svc_prtcpants + "</td>";
							} else if (col == 'service_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.svc_cost_sum + "</td>";
							} else if (col == 'extra_service_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.ex_svc_cost_sum + "</td>";
							} else if (col == 'discount') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.discount_sum + "</td>";
							} else if (col == 'total_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.total_cost_sum + "</td>";
							} else {
								totalsRow += "<td></td>";
							}
						}
					}
					totalsRow += "</tr>";
					jQuery(".order_records").append(totalsRow);
				} else {
					jQuery(".order_records").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}


function bm_fetch_archived_order_as_per_search(type = '') {
	var urlParams = new URLSearchParams(window.location.search);
    var orderby = urlParams.get('orderby') || 'id';
    var order = urlParams.get('order') || 'desc';

	var post = {
        'pagenum': jQuery.trim(jQuery('#pagenum').val()),
        'base': jQuery(location).attr("href"),
        'limit': jQuery.trim(jQuery('#limit_count').val()),
        'service_from': jQuery('#service_from').val(),
        'service_to': jQuery('#service_to').val(),
        'order_from': jQuery('#order_from').val(),
        'order_to': jQuery('#order_to').val(),
        'search_string': jQuery.trim(jQuery('#global_search').val()),
        'order_source': jQuery('#order_source_filter').val(),
        'order_status': jQuery('#order_status_filter').val() || [],
    	'payment_status': jQuery('#payment_status_filter').val() || [],
		'services': jQuery('#service_filter').val() || [],
    	'categories': jQuery('#category_filter').val() || [],
        'type': type,
        'orderby': orderby,
        'order': order,
    }

	var data = { 'action': 'bm_fetch_archived_order_as_per_search', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".order_records").html('');
			jQuery("#order_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;
			var num_of_pages = jsondata.num_of_pages ? jsondata.num_of_pages : 0;
			jQuery(document).find("#total_pages").val(num_of_pages);

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.active_columns) != "undefined" && typeof (jsondata.column_values) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined" && typeof (jsondata.saved_search) != "undefined") {
				var bookings = jsondata.bookings;
				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});
				var active_columns = jQuery.map(jsondata.active_columns, function (value, key) {
					return value;
				});
				var column_value_keys = jQuery.map(jsondata.column_values, function (value, key) {
					return key;
				});
				var column_values = jQuery.map(jsondata.column_values, function (value, key) {
					return value;
				});
				var pagination = jsondata.pagination;
				var saved_search = jsondata.saved_search;

				if (saved_search != '' && saved_search != null) {
					jQuery('#global_search').val(typeof(saved_search.global_search) != "undefined" ? saved_search.global_search : '');
					jQuery('#service_from').val(typeof(saved_search.service_from) != "undefined" ? saved_search.service_from : '');
					jQuery('#service_to').val(typeof(saved_search.service_to) != "undefined" ? saved_search.service_to : '');
					jQuery('#order_from').val(typeof(saved_search.order_from) != "undefined" ? saved_search.order_from : '');
					jQuery('#order_to').val(typeof(saved_search.order_to) != "undefined" ? saved_search.order_to : '');
					if (typeof(saved_search.order_source) != "undefined" && saved_search.order_source != '') {
						jQuery('#order_source_filter').val(saved_search.order_source).trigger('change');
					}
					if (typeof(saved_search.order_status) != "undefined" && saved_search.order_status != '') {
						var orderStatusArray = saved_search.order_status.split(',');
						jQuery('#order_status_filter').val(orderStatusArray);
						jQuery('#order_status_filter').multiselect('reload');
					}
					if (typeof(saved_search.payment_status) != "undefined" && saved_search.payment_status != '') {
						var paymentStatusArray = saved_search.payment_status.split(',');
						jQuery('#payment_status_filter').val(paymentStatusArray);
						jQuery('#payment_status_filter').multiselect('reload');
					}
					if (typeof(saved_search.services) != "undefined" && saved_search.services != '') {
						var servicesArray = saved_search.services.split(',');
						jQuery('#service_filter').val(servicesArray);
						jQuery('#service_filter').multiselect('reload');
					}
					if (typeof(saved_search.categories) != "undefined" && saved_search.categories != '') {
						var categoriesArray = saved_search.categories.split(',');
						jQuery('#category_filter').val(categoriesArray);
						jQuery('#category_filter').multiselect('reload');
					}

					if (saved_search.service_from != '' || saved_search.service_to != '' || saved_search.order_from != '' || saved_search.order_to != '' || 
						saved_search.order_source != '' || saved_search.order_status != '' || saved_search.payment_status != '') {
						jQuery("#order_advanced_search_box").slideDown("slow");
					}
				}

				var orderListing = '';
				var current_pagenumber = jsondata.current_pagenumber;

				if (bookings != null && bookings.length != 0) {
					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						for (var j = 0; j < column_values.length; j++) {
							if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
								continue;
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'serial_no') {
								orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_name') {
								orderListing += "<td style='text-align: center;width:140px;' title='" + bookings[i].service_name + "'>" + bookings[i].service_name.substring(0, 20) + '...' + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_created_at') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_date') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'first_name') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].first_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'last_name') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].last_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'contact_no') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].contact_no + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'email_address') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].email_address + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_participants') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].service_participants + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'extra_service_participants') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].extra_service_participants + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].service_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].service_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'extra_service_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].extra_service_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].extra_service_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'discount') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].discount);
								} else {
									orderListing += changePriceFormat(bookings[i].discount) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'total_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
								}
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'customer_data') {
								orderListing += "<td style='text-align: center;'><div class='show-archived-order-customer-dialog linkText' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-file' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'ordered_from') {
								orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'order_status') {
								orderListing += "<td style='text-align: center;'>";
								orderListing += status_values[jQuery.inArray(bookings[i].original_order_status, status_keys)];
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'payment_status') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].stripe_status + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'order_attachments') {
								orderListing += "<td style='text-align: center;'><div class='show-archived-order-attachments' style='cursor:pointer;font-size:16px;' id=" + bookings[i].original_id + "><i class='fa fa-paperclip' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'actions') {
								orderListing += "<td style='text-align: center;width:84px;'>";
								orderListing += "<button type='button' name='restoreorder' id='restoreorder' title="+bm_normal_object.restore+" value="+bookings[i].id+"><i class='fa fa-refresh' aria-hidden='true' style='color:red;cursor:pointer;'></i></button>&nbsp;&nbsp;";
								orderListing += "<button type='button' name='delorder' id='delorder' title="+bm_normal_object.remove+" value="+bookings[i].id+"><i class='fa fa-trash' aria-hidden='true' style='color:red;cursor:pointer;'></i></button>";
								orderListing += "</td>";
							}
						}
						orderListing += "</form></tr>";
						current_pagenumber++;
					}
					jQuery(".order_records").append(orderListing);
					jQuery("#order_pagination").append(pagination);

					let prefix = bm_normal_object.total + " = ";

					var totalsRow = "<tr class='totals-row' style='font-weight:bold; background:#f9f9f9;'>";
					for (var j = 0; j < column_values.length; j++) {
						if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
							continue;
						}

						if (typeof(column_values[j].column) != "undefined") {
							let col = column_values[j].column;
							if (col == 'service_participants') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.svc_prtcpants + "</td>";
							} else if (col == 'extra_service_participants') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.ex_svc_prtcpants + "</td>";
							} else if (col == 'service_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.svc_cost_sum + "</td>";
							} else if (col == 'extra_service_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.ex_svc_cost_sum + "</td>";
							} else if (col == 'discount') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.discount_sum + "</td>";
							} else if (col == 'total_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.total_cost_sum + "</td>";
							} else {
								totalsRow += "<td></td>";
							}
						}
					}
					totalsRow += "</tr>";
					jQuery(".order_records").append(totalsRow);
				} else {
					jQuery(".order_records").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}


// Sort results
function bm_sort_orders(column, direction) {
    var url = new URL(window.location.href);
    url.searchParams.set('orderby', column);
    url.searchParams.set('order', direction);
    url.searchParams.set('pagenum', 1);
    window.location.href = url.toString();
	bm_search_order_data('save_search');
}


// Search checkin data
function bm_search_checkin_data(type = '') {
	var post = {
		'pagenum': jQuery.trim(jQuery('#pagenum').val()),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
		'service_from': jQuery('#checkin_service_from').val(),
		'service_to': jQuery('#checkin_service_to').val(),
		'checkin_from': jQuery('#checkin_from').val(),
		'checkin_to': jQuery('#checkin_to').val(),
		'search_string': jQuery.trim(jQuery('#checkin_global_search').val()),
		'type': type,
		'service_ids': jQuery('#checkin_service_advanced_filter').val(),
	}

	var data = { 'action': 'bm_fetch_checkin_as_per_search', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".checkin_records").html('');
			jQuery("#checkin_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;
			var num_of_pages = jsondata.num_of_pages ? jsondata.num_of_pages : 0;
			jQuery(document).find("#total_pages").val(num_of_pages);

			if (typeof (jsondata.checkins) != "undefined" && typeof (jsondata.active_columns) != "undefined" && typeof (jsondata.column_values) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined" && typeof (jsondata.saved_search) != "undefined") {
				var checkins = jsondata.checkins ?? [];
				var active_columns = jQuery.map(jsondata.active_columns, function (value, key) {
					return value;
				});
				var column_value_keys = jQuery.map(jsondata.column_values, function (value, key) {
					return key;
				});
				var column_values = jQuery.map(jsondata.column_values, function (value, key) {
					return value;
				});
				var pagination = jsondata.pagination;
				var saved_search = jsondata.saved_search;

				if (saved_search != '' && saved_search != null) {
					jQuery('#checkin_global_search').val(typeof (saved_search.global_search) != "undefined" ? saved_search.global_search : '');
					jQuery('#checkin_service_from').val(typeof (saved_search.service_from) != "undefined" ? saved_search.service_from : '');
					jQuery('#checkin_service_to').val(typeof (saved_search.service_to) != "undefined" ? saved_search.service_to : '');
					jQuery('#checkin_from').val(typeof (saved_search.checkin_from) != "undefined" ? saved_search.checkin_from : '');
					jQuery('#checkin_to').val(typeof (saved_search.checkin_to) != "undefined" ? saved_search.checkin_to : '');
					if (Array.isArray(saved_search.service_ids) && saved_search.service_ids.length > 0) {
						var serviceIdsArray = saved_search.service_ids.join(',');
						jQuery('#checkin_service_advanced_filter').val(serviceIdsArray);
						jQuery('#checkin_service_advanced_filter').multiselect('reload');
					}

					if (
						saved_search.service_from ||
						saved_search.service_to ||
						saved_search.checkin_from ||
						saved_search.checkin_to ||
						(Array.isArray(saved_search.service_ids) && saved_search.service_ids.length > 0)
					) {
						jQuery("#checkin_advanced_search_box").slideDown("slow");
					}
				}

				var checkinListing = '';
				var current_pagenumber = jsondata.current_pagenumber;

				if (checkins != null && checkins.length > 0) {
					for (var i = 0; i < checkins.length; i++) {
						checkinListing += "<tr><form role='form' method='post'>";
						for (var j = 0; j < column_values.length; j++) {
							if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
								continue;
							}

							checkinListing += `<input type="hidden" id="email_id_${i}" value="${checkins[i].email_id ?? 0}">`;
							checkinListing += `<input type="hidden" id="module_id_${i}" value="${checkins[i].module_id ?? 0}">`;
							checkinListing += `<input type="hidden" id="module_type_${i}" value="${checkins[i].module_type ?? ''}">`;
							checkinListing += `<input type="hidden" id="mail_type_${i}" value="${checkins[i].mail_type ?? ''}">`;
							checkinListing += `<input type="hidden" id="template_id_${i}" value="${checkins[i].template_id ?? 0}">`;
							checkinListing += `<input type="hidden" id="process_id_${i}" value="${checkins[i].process_id ?? 0}">`;

							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'serial_no') {
								checkinListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_name') {
								checkinListing += "<td style='text-align: center;width:140px;' title='" + checkins[i].service_name + "'>" + checkins[i].service_name.substring(0, 20) + '...' + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_date') {
								checkinListing += "<td style='text-align: center;'>" + checkins[i].booking_date + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'first_name') {
								checkinListing += "<td style='text-align: center;'>" + checkins[i].first_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'last_name') {
								checkinListing += "<td style='text-align: center;'>" + checkins[i].last_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'contact_no') {
								checkinListing += "<td style='text-align: center;'>" + checkins[i].contact_no + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'email_address') {
								checkinListing += "<td style='text-align: center;'>" + checkins[i].email_address + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'total_cost') {
								checkinListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									checkinListing += currency_symbol + changePriceFormat(checkins[i].total_cost);
								} else {
									checkinListing += changePriceFormat(checkins[i].total_cost) + currency_symbol;
								}
								checkinListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'checkin_time') {
								checkinListing += "<td style='text-align: center;'>" + (checkins[i].checkin_time) + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'checkin_status') {
								const status = checkins[i].checkin_status;
								
								checkinListing += `<td style='text-align: center;'>`;
								checkinListing += `<select class="checkin-status-dropdown" 
												data-checkin-id="${checkins[i].checkin_id}"
												data-booking-id="${checkins[i].booking_id}">
									<option value="pending" ${status == 'pending' ? 'selected' : ''}>Pending</option>
									<option value="checked_in" ${status == 'checked_in' ? 'selected' : ''}>Checked In</option>
									<option value="expired" ${status == 'expired' ? 'selected' : ''}>Expired</option>
								</select>`;
								checkinListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'ticket_pdf') {
								checkinListing += "<td style='text-align: center;'><div class='show-order-ticket' style='cursor:pointer;font-size:16px;' id=" + checkins[i].booking_id + "><i class='fa fa-paperclip' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'actions') {
								checkinListing += `<td style='text-align: center;'>
									<div class="action-buttons">
										<a href="#" class="view-details" data-id="${checkins[i].booking_id}" 
										title="View Details">
											<span class="dashicons dashicons-visibility"></span>
										</a>
										<span class="resend-email" title="${bm_normal_object.resend_ticket_mail}" id="${checkins[i].email_id ?? 0}" data-id="${i}" onclick="bm_open_email_body(this, 'checkin')">
											<span class="dashicons dashicons-email-alt"></span>
										</span>
									</div>
								</td>`;
							}
						}
						checkinListing += "</form></tr>";
						current_pagenumber++;
					}
					jQuery(".checkin_records").append(checkinListing);
					jQuery("#checkin_pagination").append(pagination);
				} else {
					jQuery(".checkin_records").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}


function bm_show_hide_respective_orders($this) {
	if ($this.value == 'failed') {
		jQuery('.status_search_span').hide();
		jQuery('.payment_status_search_span').hide();
		jQuery('.service_search_span').hide();
		jQuery('.category_search_span').hide();
		bm_fetch_failed_order_as_per_search('save_search');
	} else if ($this.value == 'all-non-failed') {
		jQuery('.status_search_span').show();
		jQuery('.payment_status_search_span').show();
		jQuery('.service_search_span').show();
		jQuery('.category_search_span').show();
		bm_search_order_data('save_search');
	} else if ($this.value == 'archived') {
		jQuery('.status_search_span').show();
		jQuery('.payment_status_search_span').show();
		jQuery('.service_search_span').show();
		jQuery('.category_search_span').show();
		bm_fetch_archived_order_as_per_search('save_search');
	}
}


function bm_fetch_failed_order_as_per_search(type = '') {
	var urlParams = new URLSearchParams(window.location.search);
    var orderby = urlParams.get('orderby') || 'id';
    var order = urlParams.get('order') || 'desc';

	var post = {
        'pagenum': jQuery.trim(jQuery('#pagenum').val()),
        'base': jQuery(location).attr("href"),
        'limit': jQuery.trim(jQuery('#limit_count').val()),
        'service_from': jQuery('#service_from').val(),
        'service_to': jQuery('#service_to').val(),
        'order_from': jQuery('#order_from').val(),
        'order_to': jQuery('#order_to').val(),
        'search_string': jQuery.trim(jQuery('#global_search').val()),
        'order_source': jQuery('#order_source_filter').val(),
        'type': type,
        'orderby': orderby,
        'order': order
    };

	var data = { 'action': 'bm_fetch_failed_order_as_per_search', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".order_records").html('');
			jQuery("#order_pagination").html('');
			var currency_symbol = bm_normal_object.currency_symbol;
			var currency_position = bm_normal_object.currency_position;
			var num_of_pages = jsondata.num_of_pages ? jsondata.num_of_pages : 0;
			jQuery(document).find("#total_pages").val(num_of_pages);

			if (typeof (jsondata.bookings) != "undefined" && typeof (jsondata.active_columns) != "undefined" && typeof (jsondata.column_values) != "undefined" && typeof (jsondata.order_statuses) != "undefined" && typeof (jsondata.current_pagenumber) != "undefined" && typeof (jsondata.pagination) != "undefined" && typeof (jsondata.saved_search) != "undefined") {
				var bookings = jsondata.bookings;
				var status_keys = jQuery.map(jsondata.order_statuses, function (value, key) {
					return key;
				});
				var status_values = jQuery.map(jsondata.order_statuses, function (value, key) {
					return value;
				});
				var active_columns = jQuery.map(jsondata.active_columns, function (value, key) {
					return value;
				});
				var column_value_keys = jQuery.map(jsondata.column_values, function (value, key) {
					return key;
				});
				var column_values = jQuery.map(jsondata.column_values, function (value, key) {
					return value;
				});
				var pagination = jsondata.pagination;
				var saved_search = jsondata.saved_search;

				if (saved_search != '' && saved_search != null) {
					jQuery('#global_search').val(typeof (saved_search.global_search) != "undefined" ? saved_search.global_search : '');
					jQuery('#service_from').val(typeof (saved_search.service_from) != "undefined" ? saved_search.service_from : '');
					jQuery('#service_to').val(typeof (saved_search.service_to) != "undefined" ? saved_search.service_to : '');
					jQuery('#order_from').val(typeof (saved_search.order_from) != "undefined" ? saved_search.order_from : '');
					jQuery('#order_to').val(typeof (saved_search.order_to) != "undefined" ? saved_search.order_to : '');

					if (saved_search.service_from != '' || saved_search.service_to != '' || saved_search.order_from != '' || saved_search.order_to != '') {
						jQuery("#order_advanced_search_box").slideDown("slow");
					}
				}

				var orderListing = '';
				var current_pagenumber = jsondata.current_pagenumber;

				if (bookings != null && bookings.length != 0) {

					for (var i = 0; i < bookings.length; i++) {
						orderListing += "<tr><form role='form' method='post'>";
						for (var j = 0; j < column_values.length; j++) {
							if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
								continue;
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'serial_no') {
								orderListing += "<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : i + 1) + "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_name') {
								orderListing += "<td style='text-align: center;width:140px;' title='" + bookings[i].service_name + "'>" + bookings[i].service_name.substring(0, 20) + '...' + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_created_at') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].booking_created_at + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'booking_date') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].booking_date + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'first_name') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].first_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'last_name') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].last_name + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'contact_no') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].contact_no + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'email_address') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].email_address + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_participants') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].service_participants + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'extra_service_participants') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].extra_service_participants + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'service_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].service_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].service_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'extra_service_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].extra_service_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].extra_service_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'discount') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].discount);
								} else {
									orderListing += changePriceFormat(bookings[i].discount) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'total_cost') {
								orderListing += "<td style='text-align: center;'>";
								if (currency_position == 'before') {
									orderListing += currency_symbol + changePriceFormat(bookings[i].total_cost);
								} else {
									orderListing += changePriceFormat(bookings[i].total_cost) + currency_symbol;
								}
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'customer_data') {
								orderListing += "<td style='text-align: center;'><div class='show-failed-order-customer-dialog' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-file' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'ordered_from') {
								orderListing += "<td style='text-align: center;'>" + (bookings[i].is_frontend_booking == 0 ? bm_normal_object.backend : bm_normal_object.frontend) + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'order_status') {
								orderListing += "<td style='text-align: center;'>";
								orderListing += status_values[jQuery.inArray(bookings[i].order_status, status_keys)];
								orderListing += "</td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'payment_status') {
								orderListing += "<td style='text-align: center;'>" + bookings[i].stripe_status + " </td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'order_attachments') {
								orderListing += "<td style='text-align: center;'><div class='show-failed-order-attachments' style='cursor:pointer;font-size:16px;' id=" + bookings[i].id + "><i class='fa fa-paperclip' aria-hidden='true'></i></div></td>";
							}
							if (typeof (column_values[j].column) != "undefined" && column_values[j].column == 'actions') {
								orderListing += "<td style='text-align: center;width:84px;'>";
								orderListing += "<button type='button' id='failedtransaction' title='" + bm_error_object.transaction_not_editable + "' value='0' disabled><i class='fa fa-exchange' aria-hidden='true' style='cursor:pointer;'></i></button>&nbsp;&nbsp;";
								orderListing += "<button type='button' name='delfailedorder' id='delfailedorder' title="+bm_normal_object.remove+" value="+bookings[i].id+"><i class='fa fa-trash' aria-hidden='true' style='color:red;cursor:pointer;'></i></button>";
								orderListing += "</td>";
							}
						}
						orderListing += "</form></tr>";
						current_pagenumber++;
					}
					jQuery(".order_records").append(orderListing);
					jQuery("#order_pagination").append(pagination);

					let prefix = bm_normal_object.total + " = ";

					var totalsRow = "<tr class='totals-row' style='font-weight:bold; background:#f9f9f9;'>";
					for (var j = 0; j < column_values.length; j++) {
						if (active_columns != null && jQuery.inArray(column_value_keys[j], active_columns) == -1) {
							continue;
						}

						if (typeof(column_values[j].column) != "undefined") {
							let col = column_values[j].column;
							if (col == 'service_participants') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.svc_prtcpants + "</td>";
							} else if (col == 'extra_service_participants') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.ex_svc_prtcpants + "</td>";
							} else if (col == 'service_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.svc_cost_sum + "</td>";
							} else if (col == 'extra_service_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.ex_svc_cost_sum + "</td>";
							} else if (col == 'discount') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.discount_sum + "</td>";
							} else if (col == 'total_cost') {
								totalsRow += "<td style='text-align: center;'>" + prefix + jsondata.total_cost_sum + "</td>";
							} else {
								totalsRow += "<td></td>";
							}
						}
					}
					totalsRow += "</tr>";
					jQuery(".order_records").append(totalsRow);
				} else {
					jQuery(".order_records").append('<div class="no_records_class">' + bm_normal_object.no_records + '</div>');
				}
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Export table content
function bm_export_to_csv_old(tableId, filename, excludedColumns = [], includeColumnNames = true) {
	var table = document.getElementById(tableId);
	var rows = Array.from(table.querySelectorAll('tr'));

	var columnNames = Array.from(table.querySelectorAll('th')).map(th => th.innerText);
	var excludedIndices = excludedColumns.map(col => columnNames.indexOf(col));
	columnNames = columnNames.filter(column => !excludedColumns.includes(column))

	var data = rows.map(row => {
		var cells = Array.from(row.querySelectorAll('td'));
		return cells.map((cell, index) => {
			var select = cell.querySelector('select');
			var cellValue = select ? select.options[select.selectedIndex].text : cell.innerText;
			return cellValue.trim();
		}).filter((_, index) => !excludedIndices.includes(index));
	});

	if (includeColumnNames) {
		data.unshift(columnNames);
	}

	var filteredData = data.filter(row => row.some(cell => cell.trim() !== ''));

	var csvContent = '\uFEFF';
	csvContent += filteredData.map(row => row.map(encodeValue).join(',')).join('\n');

	var csvData = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

	var link = document.createElement('a');
	link.setAttribute('href', URL.createObjectURL(csvData));
	link.setAttribute('download', filename);

	link.click();
}



// Fetch order export options html
jQuery(document).on('click', '.export_order_records', function (e) {
	e.preventDefault();
	jQuery('#exportButton').show();
	jQuery('#resendProcess').hide();
	jQuery('#exportButton').prop('disabled', false);

	var data = { 'action': 'bm_fetch_export_order_modal_html', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#export_orders').html('');
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		var html = jsondata.html ? jsondata.html : '';

		if (status == false) {
			jQuery('#exportButton').prop('disabled', true);
		}

		jQuery('#export_orders').html(html);
		jQuery('#order_export_modal').addClass('active-modal');
	});
});



// Show/hide page range
jQuery(document).on('change', '#exportOption', function () {
	if (this.value == '3') {
		jQuery('#rangeOptions').show();
	} else {
		jQuery('#rangeOptions').hide();
	}
});



// Fetch export data on click
jQuery(document).on('click', '#exportButton', function () {
	var exportOption = jQuery('#exportOption').val();
	var totalPages = parseInt(jQuery('#total_pages').val());

	if (exportOption == '1') {
		fetchAndExportData('orders', 'all');
	} else if (exportOption == '2') {
		fetchAndExportData('orders', 'current');
	} else if (exportOption == '3') {
		var startPage = parseInt(jQuery('#startPage').val());
		var endPage = parseInt(jQuery('#endPage').val());

		if (isNaN(startPage) || isNaN(endPage) || startPage <= 0 || endPage <= 0 || startPage % 1 !== 0 || endPage % 1 !== 0 || startPage > endPage || endPage > totalPages) {
			alert(bm_error_object.invalid_page_numbers);
			return;
		}

		fetchAndExportData('orders', 'range', startPage, endPage);
	}
});



// Fetch Export Data
function fetchAndExportData(moduleType, type, startPage = 0, endPage = 0) {
    const urlParams = new URLSearchParams(window.location.search);
    const orderby = urlParams.get('orderby') || 'id';
    const order = urlParams.get('order') || 'desc';

    const post = {
        pagenum: jQuery.trim(jQuery('#pagenum').val()),
        limit: jQuery.trim(jQuery('#limit_count').val()),
        total_pages: jQuery.trim(jQuery('#total_pages').val()),
        service_from: moduleType === 'orders' ? jQuery('#service_from').val() : jQuery('#checkin_service_from').val(),
        service_to: moduleType === 'orders' ? jQuery('#service_to').val() : jQuery('#checkin_service_to').val(),
        order_from: moduleType === 'orders' ? jQuery('#order_from').val() : jQuery('#checkin_from').val(),
        order_to: moduleType === 'orders' ? jQuery('#order_to').val() : jQuery('#checkin_to').val(),
        search_string: moduleType === 'orders' ? jQuery.trim(jQuery('#global_search').val()) : jQuery.trim(jQuery('#checkin_global_search').val()),
        order_source: moduleType === 'orders' ? jQuery('#order_source_filter').val() : null,
        order_status: moduleType === 'orders' ? jQuery('#order_status_filter').val() : null,
        payment_status: moduleType === 'orders' ? jQuery('#payment_status_filter').val() : null,
		services_filter: moduleType === 'orders' ? jQuery('#service_filter').val() : null,
        categories_filter: moduleType === 'orders' ? jQuery('#category_filter').val() : null,
		services: moduleType === 'orders' ? null : jQuery('#checkin_service_advanced_filter').val(),
        type: type,
        start_page: startPage,
        end_page: endPage,
        order_column: orderby,
        order_dir: order
    };

    const ajaxAction = moduleType === 'orders' ? 'bm_fetch_export_order_records' : 'bm_fetch_export_checkin_records';
    const filename = moduleType === 'orders' ? 'orders.csv' : 'checkins.csv';

    const data = {
        action: ajaxAction,
        post: post,
        nonce: bm_ajax_object.nonce
    };

    jQuery.post(bm_ajax_object.ajax_url, data, function(response) {
        jQuery('#order_export_modal, #checkin_export_modal').removeClass('active-modal');
		var response = jQuery.parseJSON(response);

        const status = response.status || false;
        const orders = response.orders || [];
        const headers = response.headers || [];
        const keys = response.keys || [];

        if (status && orders.length > 0 && headers.length > 0 && keys.length > 0 && headers.length === keys.length) {
            exportToCSV(orders, headers, keys, filename);
        } else {
            showMessage(bm_error_object.server_error || bm_error_object.failed_export, 'error');
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        showMessage(bm_error_object.server_error);
    });
}




// Checkin export
jQuery(document).on('click', '.export_checkin_records', function (e) {
	e.preventDefault();
	jQuery('#checkinexportButton').show();
	jQuery('#checkinresendProcess').hide();
	jQuery('#checkinexportButton').prop('disabled', false);

	var data = { 'action': 'bm_export_checkin_options_html', 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#export_checkin').html('');
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		var html = jsondata.html ? jsondata.html : '';

		if (status == false) {
			jQuery('#checkinexportButton').prop('disabled', true);
		}

		jQuery('#export_checkin').html(html);
		jQuery('#checkin_export_modal').addClass('active-modal');
	});
});




// Fetch export data on click
jQuery(document).on('click', '#checkinexportButton', function () {
	var exportOption = jQuery('#exportOption').val();
	var totalPages = parseInt(jQuery('#total_pages').val());

	if (exportOption == '1') {
		fetchAndExportData('checkin', 'all');
	} else if (exportOption == '2') {
		fetchAndExportData('checkin', 'current');
	} else if (exportOption == '3') {
		var startPage = parseInt(jQuery('#startPage').val());
		var endPage = parseInt(jQuery('#endPage').val());

		if (isNaN(startPage) || isNaN(endPage) || startPage <= 0 || endPage <= 0 || startPage % 1 !== 0 || endPage % 1 !== 0 || startPage > endPage || endPage > totalPages) {
			alert(bm_error_object.invalid_page_numbers);
			return;
		}

		fetchAndExportData('checkin', 'range', startPage, endPage);
	}
});




// Export to csv
function exportToCSV(data, headers, headerToKey, filename) {
	var csvContent = '\uFEFF';

	// Add column headers to CSV content
	csvContent += headers.map(encodeValue1).join(',') + '\n';

	// Add data rows to CSV content
	data.forEach(row => {
		let rowArray = headers.map((header, index) => {
			let key = headerToKey[index];
			let value = key && row[key] !== undefined ? row[key] : '';
			return encodeValue1(value);
		});
		csvContent += rowArray.join(',') + '\n';
	});

	var csvData = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });

	var link = document.createElement('a');
	link.setAttribute('href', URL.createObjectURL(csvData));
	link.setAttribute('download', filename);

	link.click();
}



// Handle special characters in export
function encodeValue1(value) {
	value = String(value);
	if (value.includes(',')) {
		return `"${value.replace(/"/g, '""')}"`;
	}
	return value;
}



// Handle special characters in export
function encodeValue(value) {
	return `"${value.replace(/"/g, '""')}"`;
}



// Show/hide search box
function bm_show_search_box(id) {
	if (jQuery("#" + id).is(':visible')) {
		jQuery("#" + id).slideUp("slow");
	} else {
		jQuery("#" + id).slideDown("slow");
	}
}


// Add/remove search box
function bm_remove_hidden_class(id) {
	if (jQuery("#" + id).hasClass('hidden')) {
		jQuery("#" + id).removeClass("hidden");
	} else {
		jQuery("#" + id).addClass("hidden");
	}
}



// Convert one date format to another format
function convertDateFormat_old(date, toFormat) {
	var convertedDate = new Date(date);

	if (!isNaN(convertedDate.getTime())) {
		var formattedDate = '';

		switch (toFormat) {
			case 'YYYY-MM-DD':
				formattedDate = convertedDate.toISOString().split('T')[0];
				break;
			case 'MM/DD/YYYY':
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				formattedDate = month + '/' + day + '/' + convertedDate.getFullYear();
				break;
			case 'DD/MM/YYYY':
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				formattedDate = day + '/' + month + '/' + convertedDate.getFullYear();
				break;
			case 'DD-MM-YYYY':
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				formattedDate = day + '-' + month + '-' + convertedDate.getFullYear();
				break;
			case 'YYYY/MM/DD':
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				formattedDate = convertedDate.getFullYear() + '/' + month + '/' + day;
				break;
			case 'DD/MM/YY':
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				formattedDate = day + '/' + month + '/' + convertedDate.getFullYear().toString().substr(-2);
				break;
			case 'MMMM DD, YYYY':
				formattedDate = convertedDate.toLocaleDateString(undefined, { month: 'long', day: 'numeric', year: 'numeric' });
				break;
			case 'MMM DD, YYYY':
				formattedDate = convertedDate.toLocaleDateString(undefined, { month: 'short', day: 'numeric', year: 'numeric' });
				break;
			case 'YYYY MMMM DD':
				formattedDate = convertedDate.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
				break;
			case 'YYYY MMM DD':
				formattedDate = convertedDate.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' });
				break;
			default:
				formattedDate = convertedDate.toLocaleDateString();
				break;
		}

		return formattedDate;
	} else {
		return false;
	}
}



// Convert one date format to another format
function convertDateFormat(date, toFormat) {
	var convertedDate = new Date(date);

	if (!isNaN(convertedDate.getTime())) {
		var formattedDate = '';

		switch (toFormat) {
			case 'YYYY-MM-DD':
				formattedDate = convertedDate.toISOString().split('T')[0];
				break;

			case 'MM/DD/YYYY':
			case 'DD/MM/YYYY':
			case 'DD-MM-YYYY':
			case 'YYYY/MM/DD':
			case 'DD/MM/YY':
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				var year = convertedDate.getFullYear();
				switch (toFormat) {
					case 'MM/DD/YYYY':
						formattedDate = month + '/' + day + '/' + year;
						break;
					case 'DD/MM/YYYY':
						formattedDate = day + '/' + month + '/' + year;
						break;
					case 'DD-MM-YYYY':
						formattedDate = day + '-' + month + '-' + year;
						break;
					case 'YYYY/MM/DD':
						formattedDate = year + '/' + month + '/' + day;
						break;
					case 'DD/MM/YY':
						formattedDate = day + '/' + month + '/' + year.toString().substr(-2);
						break;
				}
				break;

			case 'MMMM DD, YYYY':
				formattedDate = convertedDate.toLocaleDateString(undefined, {
					month: 'long',
					day: 'numeric',
					year: 'numeric'
				});
				break;

			case 'MMM DD, YYYY':
				formattedDate = convertedDate.toLocaleDateString(undefined, {
					month: 'short',
					day: 'numeric',
					year: 'numeric'
				});
				break;

			case 'YYYY MMMM DD':
				formattedDate = convertedDate.toLocaleDateString(undefined, {
					year: 'numeric',
					month: 'long',
					day: 'numeric'
				});
				break;

			case 'YYYY MMM DD':
				formattedDate = convertedDate.toLocaleDateString(undefined, {
					year: 'numeric',
					month: 'short',
					day: 'numeric'
				});
				break;

			case 'YYYY-MM-DD HH:mm:ss':
				var year = convertedDate.getFullYear();
				var month = ("0" + (convertedDate.getMonth() + 1)).slice(-2);
				var day = ("0" + convertedDate.getDate()).slice(-2);
				var hours = ("0" + convertedDate.getHours()).slice(-2);
				var minutes = ("0" + convertedDate.getMinutes()).slice(-2);
				var seconds = ("0" + convertedDate.getSeconds()).slice(-2);
				formattedDate = `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
				break;

			case 'atTimeOnDate':
				var hours = convertedDate.getHours();
				var minutes = ("0" + convertedDate.getMinutes()).slice(-2);
				var period = hours >= 12 ? 'PM' : 'AM';
				var hour12 = hours % 12 || 12;

				var dayNum = convertedDate.getDate();
				var daySuffix;
				if (dayNum >= 11 && dayNum <= 13) {
					daySuffix = 'th';
				} else {
					switch (dayNum % 10) {
						case 1: daySuffix = 'st'; break;
						case 2: daySuffix = 'nd'; break;
						case 3: daySuffix = 'rd'; break;
						default: daySuffix = 'th';
					}
				}

				var monthName = convertedDate.toLocaleString(undefined, { month: 'long' });
				var year = convertedDate.getFullYear();

				formattedDate = ` at ${hour12}:${minutes} ${period} on ${dayNum}${daySuffix} ${monthName} ${year}`;
				break;

			default:
				formattedDate = convertedDate.toLocaleDateString();
				break;
		}

		return formattedDate;
	} else {
		return false;
	}
}



// Change price format
function changePriceFormat(price, customLocale = '') {
	price = !isNaN(parseFloat(price)) ? parseFloat(price) : 0.00;
	var formatLocale = bm_normal_object.price_format ? bm_normal_object.price_format : 'it-IT';
	formatLocale = formatLocale.replace('_', '-');
	var currency = bm_normal_object.currency_type ? bm_normal_object.currency_type : 'EUR';

	const formattedPrice = new Intl.NumberFormat((customLocale != '' ? customLocale : formatLocale), {
		// style: 'currency',
		// currency: currency,
		minimumFractionDigits: 2,
		maximumFractionDigits: 2,
	}).format(price);

	return formattedPrice;
}



// Global search order listing
function bm_global_search_order_data(value) {
	var value = jQuery.trim(value.toLowerCase());
	var currentPage = jQuery.trim(jQuery('#pagenum').val());
	var baseURL = jQuery(location).attr("href");
	var rowsPerPage = jQuery.trim(jQuery('#limit_count').val());
	var startRow = (currentPage - 1) * rowsPerPage;
	var endRow = startRow + rowsPerPage - 1;
	var total = 0;

	jQuery("#dashboard_all_orders tbody tr").each(function (index) {
		var isVisible = index >= startRow && index <= endRow;
		jQuery(this).toggle(isVisible && jQuery(this).text().toLowerCase().indexOf(value) > -1);
		if (isVisible && jQuery(this).text().toLowerCase().indexOf(value) > -1) total++;
	});

	var totalPages = Math.ceil(total / rowsPerPage);

	var pagination = generatePagination(currentPage, baseURL, totalPages);
	jQuery("#dashboard_all_orders_pagination").html('');
	jQuery("#dashboard_all_orders_pagination").html(pagination);
}



// Pagination
function generatePagination(pageNumber, baseUrl, totalPages) {
	var pagination = jQuery("<ul class='page-numbers'></ul>");

	// "Previous" link
	var previousPage = pageNumber - 1;
	if (previousPage > 0) {
		var previousLink = jQuery("<li><a href='" + baseUrl + previousPage + "'>" + bm_normal_object.previous + "</a></li>");
		pagination.append(previousLink);
	}

	// numeric page links
	for (var i = 1; i <= totalPages; i++) {
		var pageLink = jQuery("<li><a href='" + baseUrl + i + "'>" + i + "</a></li>");
		if (i === pageNumber) {
			pageLink.addClass("active");
		}
		pagination.append(pageLink);
	}

	// "Next" link
	var nextPage = pageNumber + 1;
	if (nextPage <= totalPages) {
		var nextLink = jQuery("<li><a href='" + baseUrl + nextPage + "'>" + bm_normal_object.next + "</a></li>");
		pagination.append(nextLink);
	}

	return pagination;
}



// Show module pop up message
jQuery(document).ready(function ($) {
	$("#close-popup-message").click(function () {
		hideMessage();
	});
});



// Show module pop up message
function showMessage(message, type) {
	jQuery("#popup-message").text(message ? message : bm_error_object.server_error);
	if (type === "success") {
		jQuery("#popup-message-container").css("background-color", "#4CAF50");
	} else if (type === "error") {
		jQuery("#popup-message-container").css("background-color", "#2271b1");
	} else {
		jQuery("#popup-message-container").css("background-color", "#2271b1");
	}

	jQuery("#popup-message-overlay, #popup-message-container").fadeIn();
}



// Hide module pop up message
function hideMessage() {
	jQuery("#popup-message-overlay, #popup-message-container").fadeOut();
}



// Check validity of age values entered in service
function checkServiceAgeValue($this) {
	var index = Number($this.id.split("_")[2]);
	var fieldId = $this.id;
	var val = parseInt($this.value);

	if (fieldId.startsWith('age_from_')) {
		var toFieldId = jQuery('#age_to_' + index);
		var toFieldValue = parseInt(jQuery(toFieldId).val());
		var preToField = jQuery('#age_to_' + Number(index - 1));
		var preToFieldValue = parseInt(jQuery(preToField).val());
		var nextfromField = jQuery('#age_from_' + Number(index + 1));
		var nextfromFieldValue = parseInt(jQuery(nextfromField).val());

		if (jQuery(preToField).length > 0) {
			if (!isNaN(preToFieldValue) && (val <= preToFieldValue)) {
				jQuery($this).val('');
				showMessage(bm_error_object.must_be_greater_than + preToFieldValue, 'error');
				return false;
			}
		}

		if (jQuery(nextfromField).length > 0) {
			if (!isNaN(nextfromFieldValue) && (val >= nextfromFieldValue)) {
				jQuery($this).val('');
				showMessage(bm_error_object.must_be_less_than_field, 'error');
				return false;
			}
		}

		if (!isNaN(toFieldValue) && (val >= toFieldValue)) {
			jQuery($this).val('');
			showMessage(bm_error_object.must_be_less_than + toFieldValue, 'error');
			return false;
		}

		if (isNaN(toFieldValue)) {
			jQuery(toFieldId).val((val + 1));
			jQuery(toFieldId).attr('value', (val + 1));
			jQuery(toFieldId).attr('min', (val + 1));
		}
	} else if (fieldId.startsWith('age_to_')) {
		var fromField = jQuery('#age_from_' + index);
		var fromFieldValue = parseInt(jQuery(fromField).val());
		var nextfromField = jQuery('#age_from_' + Number(index + 1));
		var nextfromFieldValue = parseInt(jQuery(nextfromField).val());
		var preToField = jQuery('#age_to_' + Number(index - 1));
		var preToFieldValue = parseInt(jQuery(preToField).val());

		if (!isNaN(fromFieldValue) && (val <= fromFieldValue)) {
			jQuery($this).val('');
			showMessage(bm_error_object.must_be_greater_than + fromFieldValue, 'error');
			return false;
		}

		if (jQuery(preToField).length > 0) {
			if (!isNaN(preToFieldValue) && (val <= preToFieldValue)) {
				jQuery($this).val('');
				showMessage(bm_error_object.must_be_greater_than_field, 'error');
				return false;
			}
		}

		if (jQuery(nextfromField).length > 0) {
			if (!isNaN(nextfromFieldValue) && (val >= nextfromFieldValue)) {
				jQuery($this).val('');
				showMessage(bm_error_object.must_be_less_than + nextfromFieldValue, 'error');
				return false;
			}
		}

		if (isNaN(fromFieldValue)) {
			jQuery(fromField).val((val - 1));
			jQuery(fromField).attr('value', (val - 1));
			jQuery(fromField).attr('max', (val - 1));
		}
	}
}


jQuery(document).ready(function ($) {
	var current_screen = bm_normal_object.current_screen;
	var screenMap = {
		'flexibooking_page_bm_booking_analytics': 2,
		'admin_page_bm_add_order': 3,
		'admin_page_bm_single_order':3,
		'flexibooking_page_bm_service_booking_planner': 4,
		'flexibooking_page_bm_single_service_booking_planner': 5,
		'admin_page_bm_add_customer': 6,
		'admin_page_bm_customer_profile': 6,
		'admin_page_bm_add_service': 7,
		'admin_page_bm_add_category': 8,
		'admin_page_bm_add_template': 9,
		'admin_page_bm_add_external_service_price': 11,
		'admin_page_bm_add_notification_process': 12,
		'flexibooking_page_bm_email_records': 13,
		'flexibooking_page_bm_voucher_records': 14,
		'flexibooking_page_bm_check_ins':15,
		'flexibooking_page_bm_pdf_customization':16,
		'flexibooking_page_bm_email_logs': 17,
		'flexibooking_page_bm_payment_logs': 18,
		'admin_page_bm_add_coupon': 19,
		'admin_page_bm_global_general_settings': 20,
		'admin_page_bm_global_email_settings': 20,
		'admin_page_bm_global_payment_settings': 20,
		'admin_page_bm_svc_booking_settings': 20,
		'admin_page_bm_global_css_settings': 20,
		'admin_page_bm_global_timezone_country_settings': 20,
		'admin_page_bm_pagination_settings': 20,
		'admin_page_bm_upload_settings': 20,
		'admin_page_bm_global_language_settings': 20,
		'admin_page_bm_global_format_settings': 20,
		'admin_page_bm_global_integration_settings': 20,
		'admin_page_bm_global_coupon_settings': 20
	};

	if (screenMap.hasOwnProperty(current_screen)) {
		var index = screenMap[current_screen];
		var $menu = $('#toplevel_page_bm_home');

		$menu.addClass('current wp-menu-open wp-has-current-submenu')
			.removeClass('wp-not-current-submenu');

		$menu.find('a').addClass('current wp-menu-open');

		$menu.find(`ul.wp-submenu li:eq(${index})`).addClass('current')
			.find('a').attr('aria-current', 'page');
	}

	console.log(current_screen);
});



// Add event condition box in event notification page
function bm_add_condition_box() {
	var last = jQuery('#conditional_content td.condition_field:last').attr("id");
	var next = Number(last.split("_")[2]) + 1;

	var option_box = "<td id='condition_field_" + next + "' class='condition_field'>" +
		"<div id='trigger_condition_div' class='bminput bm_required'>" +
		"<button type='button' class='bm_remove_event_condition' onclick='bm_remove_condition_box(this)'><i class='fa fa-remove'></i></button>" +
		"<select name='trigger_conditions[type][" + next + "]' id='condition_type_" + next + "' onchange='bm_fetch_event_condition_value(this)' class='regular-text emailselect' style='width:20%;max-width:100% !important'>" +
		"<option value='0'>" + bm_normal_object.service + "</option>" +
		"<option value='1'>" + bm_normal_object.category + "</option>" +
		"<option value='2'>" + bm_normal_object.order_status + "</option>" +
		"<option value='3'>" + bm_normal_object.payment_status + "</option>" +
		"<select>&nbsp;&nbsp;" +
		"<select name='trigger_conditions[operator][" + next + "]' id='condition_operator_" + next + "' class='regular-text emailselect' style='width:20%;'>" +
		"<option value='1'>" + bm_normal_object.equal_to + "</option>" +
		"<option value='0'>" + bm_normal_object.not_equal_to + "</option>" +
		"</select>&nbsp;&nbsp;" +
		"<select name='trigger_conditions[values][" + next + "][]' id='condition_values_" + next + "' class='notification-multiselect' style='width:300px;' multiple='multiple'></select>" +
		"<div class='errortext'></div></div></td>";

	bm_return_value_for_event_condition_type(0, next);
	jQuery('#conditional_content td.condition_field:last').after(option_box);
	jQuery('#conditional_content td.condition_field:last select').focus();
}




// Remove condition box in event notification page
function bm_remove_condition_box(a) {
	var total = jQuery('#conditional_content td.condition_field').length;

	if (total == 1) {
		alert(bm_normal_object.at_least_one_condition);
	} else if (confirm(bm_normal_object.sure_remove_condition)) {
		jQuery(a).parents('td.condition_field').remove();
	}
}



jQuery(document).ready(function ($) {
	var current_screen = bm_normal_object.current_screen;
	if (current_screen == 'admin_page_bm_add_notification_process') {
		if (getUrlParameter('id') == '') {
			bm_return_value_for_event_condition_type(0, 0);
		} else if (getUrlParameter('id') != '' && !$('#is_condition').is(':checked')) {
			bm_return_value_for_event_condition_type(0, 0);
		} else if (getUrlParameter('id') != '' && $('#is_condition').is(':checked')) {
			var total = $('.condition_field').length;
			for (var i = 0; i < total; i++) {
				intitializeMultiselect('condition_values_' + i);
			}
		}
	}
});



// Remove condition box in event notification page
function bm_fetch_event_condition_value(a) {
	var id = jQuery(a).attr('id');
	var x = jQuery('#' + id).val();
	var y = Number(id.split("_")[2]);
	bm_return_value_for_event_condition_type(x, y);
}



function bm_return_value_for_event_condition_type(a, b) {
	var post = {
		'type': a,
	}

	var data = { 'action': 'bm_fetch_event_condition_value', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		var value = jsondata.value ? jsondata.value : '';

		if (status == true) {
			jQuery('#condition_values_' + b).html(value);
			intitializeMultiselect('condition_values_' + b);
		} else {
			alert(bm_error_object.event_type_value_error);
		}
	});
}



// Multiselect
function intitializeMultiselect(a) {
	var placeholder = bm_normal_object.choose_option;

	if(a == 'order_status_filter' || a == 'filter_order_status') {
		placeholder = bm_normal_object.choose_order_status;
	} else if(a == 'payment_status_filter' || a == 'filter_payment_status') {
		placeholder = bm_normal_object.choose_payment_status;
	} else if(a == 'search_by_service' || a == 'search_fullcalendar_by_service' || a == 'filter_services' || a == 'search_timeslot_fullcalendar_by_service' || a == 'checkin_service_advanced_filter' || a == 'manual_checkin_service' || a == 'service_filter' ) {
		placeholder = bm_normal_object.filter_service;
	} else if(a == 'search_by_category' || a == 'search_fullcalendar_by_category' || a == 'search_timeslot_fullcalendar_by_category' || a == 'category_filter') {
		placeholder = bm_normal_object.filter_category;
	} else if(a == 'filter_customers') {
		placeholder = bm_normal_object.filter_customer;
	} else if(a == 'filter_emails') {
		placeholder = bm_normal_object.filter_email;
	}

	jQuery('#' + a).multiselect('reload');
	jQuery('#' + a).multiselect({
		columns: 1,
		texts: {
			placeholder: placeholder,
			search: bm_normal_object.search_here,
			selectAll: bm_normal_object.select_all
		},
		search: true,
		selectAll: true,
		onOptionClick: function (element, option) {
			var maxSelect = 100;

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



// Change process visiblity
function bm_change_process_visibility($this) {
	var process_id = $this.id.split('_')[3];
	var inputStatus = jQuery($this).is(':checked') ? 1 : 0;
	var type = jQuery($this).data('type');

	var post = {
		'id': process_id,
		'status': inputStatus,
		'type': type,
	}

	if (confirm(bm_normal_object.change_pro_visibility)) {
		var data = { 'action': 'bm_change_process_visibility', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			if (status == 'error') {
				inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
				showMessage(bm_error_object.active_process_type, 'error');
			} else if (status == false) {
				inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
				showMessage(bm_error_object.server_error, 'error');
			}
		});
	} else {
		inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
	}
}



// Update order transaction data
function bm_update_transaction($this) {
	jQuery(document).find('.edit_transactions_errortext').html('');
	jQuery(document).find('.edit_transactions_errortext').hide();
	jQuery(document).find('#save_trans_button').prop('disabled', false);
	jQuery('#save_trans_button').show();
	jQuery('#resendProcess').hide();
	var id = jQuery($this).val();

	var post = {
		'id': id,
	}

	var data = { 'action': 'bm_update_transaction', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#edit_transaction').html('');
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		var is_active = jsondata.is_active ? jsondata.is_active : 0;
		var html = jsondata.html ? jsondata.html : '';

		if (is_active == 0 || is_active == 2) {
			jQuery(document).find('#save_trans_button').prop('disabled', true);
			jQuery(document).find('.edit_transactions_errortext').html(bm_error_object.transaction_not_editable);
			jQuery(document).find('.edit_transactions_errortext').show();
		}

		if (status == true) {
			jQuery('#edit_transaction').html(html);
			jQuery('#edit_transactions_modal').addClass('active-modal');
		} else if (status == false) {
			jQuery('#edit_transaction').html(bm_error_object.server_error);
			jQuery('#edit_transactions_modal').addClass('active-modal');
		}
	});
}



// Update order transaction
function bm_save_order_transaction() {
	jQuery(document).find('.edit_transactions_errortext').html('');
	jQuery(document).find('.edit_transactions_errortext').hide();
	jQuery(document).find('#refund_id').attr('placeholder', '');
	jQuery(document).find('#refund_id').removeClass('red');
	var is_active = jQuery('#is_active').val();

	var post = {
		'id': jQuery('#booking_id').val(),
		// 'paid_amount': jQuery('#paid_amount').val(),
		// 'paid_amount_currency': jQuery('#paid_amount_currency').val(),
		'transaction_id': jQuery('#transaction_id').length > 0 ? jQuery('#transaction_id').val() : '',
		// 'payment_method': jQuery('#payment_method').val(),
		'payment_status': jQuery('#payment_status').val(),
		'refund_id': jQuery('#refund_id').val(),
		'is_active': is_active,
	}

	if (is_active == 0) {
		jQuery(document).find('.edit_transactions_errortext').html(bm_error_object.transaction_not_editable);
		jQuery(document).find('.edit_transactions_errortext').show();
	} else if (jQuery('#refund_id_input').is(':visible') && jQuery(document).find('#refund_id').val() == '') {
		jQuery(document).find('#refund_id').attr('placeholder', bm_error_object.required_field);
		jQuery(document).find('#refund_id').addClass('red');
	} else if (confirm(bm_normal_object.sure_save_transaction)) {
		jQuery('#save_trans_button').hide();
		jQuery('#resendProcess').show();
		var data = { 'action': 'bm_save_order_transaction', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (status) {
			jQuery('#edit_transactions_modal').removeClass('active-modal');
			if (status == 1) {
				showMessage(bm_success_object.transaction_updated, 'success');
				location.reload();
			} else if (status == 2) {
				showMessage(bm_error_object.wrong_transaction_id, 'error');
			} else if (status == 3) {
				showMessage(bm_error_object.transaction_id_not_required, 'error');
			} else if (status == 4) {
				showMessage(bm_error_object.wrong_refund_id, 'error');
			} else if (status == 5) {
				showMessage(bm_error_object.transaction_changes_revert, 'error');
			} else if (status == 6) {
				showMessage(bm_error_object.transaction_id_exists, 'error');
			} else if (status == 0) {
				showMessage(bm_error_object.server_error, 'error');
			} else {
				showMessage(bm_error_object.server_error, 'error');
			}
		});
	}
}


// Approve book on request order
function bm_approve_bor_order($this) {
	var id = jQuery($this).val();

	var post = {
		'id': id,
	}

	if (confirm(bm_normal_object.approve_bor_order)) {
		var data = { 'action': 'bm_approve_bor_order', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			if (status == true) {
				showMessage(bm_success_object.order_approve_success, 'success');
				location.reload();
			} else if (status == false) {
				showMessage(bm_error_object.server_error, 'error');
			}
		});
	}
}



// Cancel book on request order
function bm_cancel_bor_order($this) {
	var id = jQuery($this).val();

	var post = {
		'id': id,
	}

	if (confirm(bm_normal_object.cancel_bor_order)) {
		var data = { 'action': 'bm_cancel_bor_order', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			if (status == true) {
				showMessage(bm_success_object.order_cancel_success, 'success');
				location.reload();
			} else if (status == false) {
				showMessage(bm_error_object.server_error, 'error');
			}
		});
	}
}



// Change template visiblity
function bm_change_template_visibility($this) {
	var template_id = $this.id.split('_')[3];
	var inputStatus = jQuery($this).is(':checked') ? 1 : 0;
	var type = jQuery($this).data('type');

	var post = {
		'id': template_id,
		'status': inputStatus,
		'type': type,
	}

	if (confirm(bm_normal_object.change_tmpl_visibility)) {
		var data = { 'action': 'bm_change_template_visibility', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			if (status == 'error') {
				inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
				showMessage(bm_error_object.active_template_type, 'error');
			} else if (status == false) {
				inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
				showMessage(bm_error_object.server_error, 'error');
			} else {
				showMessage(bm_success_object.status_successfully_changed, 'success');
			}
		});
	} else {
		inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
	}
}



// Process form Validation
function add_process_form_validation() {
	jQuery('.errortext').html('');
	jQuery('.errortext').hide();
	var b = 0;
	var types = [];
	var operators = [];
	var values = [];
	var serviceZeroOperator = [];
	var serviceOneOperator = [];
	var categoryZeroOperator = [];
	var categoryOneOperator = [];
	var orderStatusZeroOperator = [];
	var orderStatusOneOperator = [];
	var paymentStatusZeroOperator = [];
	var paymentStatusOneOperator = [];
	var insertValue = [];

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

		if (jQuery('#is_condition').is(':checked')) {
			jQuery('[id^=condition_values_]').each(
				function (index, element) {
					var value = jQuery.trim(jQuery(this).val());
					if (value == "") {
						jQuery(this).parents('#trigger_condition_div').children('.errortext').html(bm_error_object.required_field);
						jQuery(this).parents('#trigger_condition_div').children('.errortext').show();
						b++;
					} else {
						types[index] = jQuery('#condition_type_' + index).val();
						operators[index] = jQuery('#condition_operator_' + index).val();
						values[index] = value;
					}
				}
			);
		}

		if (types.length > 0) {
			for (var i = 0; i < types.length; i++) {
				values[i] = values[i] ? values[i].split(',') : [];
				switch (types[i]) {
					case '0':
						if (operators[i] == 0) {
							insertValue = jQuery.merge(values[i], serviceZeroOperator);
							serviceZeroOperator = insertValue;
						} else if (operators[i] == 1) {
							insertValue = jQuery.merge(values[i], serviceOneOperator);
							serviceOneOperator = insertValue;
						}
						break;

					case '1':
						if (operators[i] == 0) {
							insertValue = jQuery.merge(values[i], categoryZeroOperator);
							categoryZeroOperator = insertValue;
						} else if (operators[i] == 1) {
							insertValue = jQuery.merge(values[i], categoryOneOperator);
							categoryOneOperator = insertValue;
						}
						break;

					case '2':
						if (operators[i] == 0) {
							insertValue = jQuery.merge(values[i], orderStatusZeroOperator);
							orderStatusZeroOperator = insertValue;
						} else if (operators[i] == 1) {
							insertValue = jQuery.merge(values[i], orderStatusOneOperator);
							orderStatusOneOperator = insertValue;
						}
						break;

					case '3':
						if (operators[i] == 0) {
							insertValue = jQuery.merge(values[i], paymentStatusZeroOperator);
							paymentStatusZeroOperator = insertValue;
						} else if (operators[i] == 1) {
							insertValue = jQuery.merge(values[i], paymentStatusOneOperator);
							paymentStatusOneOperator = insertValue;
						}
						break;

					default:
						break;
				}
			}
		}

		if (serviceZeroOperator.length > 0 && serviceOneOperator.length > 0 && hasCommonElement(serviceZeroOperator, serviceOneOperator)) {
			showMessage(bm_error_object.invalid_conditions, 'error');
			b++;
		} else if (categoryZeroOperator.length > 0 && categoryOneOperator.length > 0 && hasCommonElement(categoryZeroOperator, categoryOneOperator)) {
			showMessage(bm_error_object.invalid_conditions, 'error');
			b++;
		} else if (orderStatusZeroOperator.length > 0 && orderStatusOneOperator.length > 0 && hasCommonElement(orderStatusZeroOperator, orderStatusOneOperator)) {
			showMessage(bm_error_object.invalid_conditions, 'error');
			b++;
		} else if (paymentStatusZeroOperator.length > 0 && paymentStatusOneOperator.length > 0 && hasCommonElement(paymentStatusZeroOperator, paymentStatusOneOperator)) {
			showMessage(bm_error_object.invalid_conditions, 'error');
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


// Check if two arrays has a common element
function hasCommonElement(arr1, arr2) {
	var hasCommon = false;
	if (arr1.length > 0 && arr2.length > 0) {
		jQuery.each(arr2, function (index, value) {
			if (jQuery.inArray(value, arr1) != -1) {
				hasCommon = true;
			}
			if (hasCommon) {
				return false;
			}
		});
	}

	return hasCommon;
}


// Show mail details
function bm_show_mail_details($this) {
	var id = jQuery($this).attr('id');

	var post = {
		'id': id,
	}

	var data = { 'action': 'bm_show_mail_details', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#email_details').html('');
		jQuery('#email_details').html(response);
		jQuery('#email_details_modal').addClass('active-modal');
	});
}


// Show mail body
function bm_show_email_body($this) {
	var id = jQuery($this).attr('id');

	var post = {
		'id': id,
	}

	var data = { 'action': 'bm_show_email_body', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#email_body').html('');
		jQuery('#email_body').html(response);
		jQuery('#email_body_modal').addClass('active-modal');
	});
}


// Open mail body
function bm_open_email_body($this, $module_type = '') {
	var key = jQuery($this).data('id');
	sessionStorage.setItem("current_resend_mail_key", key);
	sessionStorage.setItem("current_resend_mail_id", jQuery(document).find('#email_id_' + key).val());
	jQuery('#fileList').empty();
	jQuery('#fileList').hide();
	jQuery('#mail_cc').val('');
	jQuery('#cc-input').addClass('hidden');
	jQuery('#bcc-input').addClass('hidden');
	jQuery('#mail_bcc').val('');
	jQuery("#resend-button").show();
	jQuery("#resendProcess").hide();
	var id = jQuery($this).attr('id');

	if ($module_type === 'checkin') {
		jQuery('#email_id_' + key).remove();

		jQuery('<input>', {
			type: 'hidden',
			id: 'email_id_' + key,
			value: id
		}).appendTo('.checkin_records');
	}

	var post = {
		'id': id,
		'module_type': $module_type,
	}

	var data = { 'action': 'bm_open_email_body', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		var to = jsondata.to ? jsondata.to : '';
		var cc = jsondata.cc ? jsondata.cc : '';
		var bcc = jsondata.bcc ? jsondata.bcc : '';
		var attachments = jsondata.attachments ? jsondata.attachments : [];
		var filepaths = typeof (attachments.filepath) != 'undefined' && attachments.filepath.length > 0 ? attachments.filepath : [];
		var fileBaseNames = typeof (attachments.basename) != 'undefined' && attachments.basename.length > 0 ? attachments.basename : [];
		var guids = typeof (attachments.guid) != 'undefined' && attachments.guid.length > 0 ? attachments.guid : [];
		var subject = jsondata.subject ? jsondata.subject : '';
		var body = jsondata.body ? jsondata.body : '';
		var string = '';
		var fileNames = [];
		bm_clear_content_in_wp_editor_body('');
		jQuery('#resend_mail_to').val(to);

		if (cc != '') {
			jQuery('#mail_cc').val(cc);
			jQuery('#cc-input').removeClass('hidden');
		}

		if (bcc != '') {
			jQuery('#mail_bcc').val(bcc);
			jQuery('#bcc-input').removeClass('hidden');
		}

		if (filepaths.length > 0) {
			var crossSign = "✕";
			var ajaxUrl = bm_ajax_object.ajax_url;
			var siteUrl = ajaxUrl.split('/wp-admin/')[0];

			for (var i = 0; i < filepaths.length; i++) {
				var basename = fileBaseNames[i] ? fileBaseNames[i] : '';
				var fileName = filepaths[i].split('/').pop();
				var fileUrl = filepaths[i];
				var guid = guids[i];
				var fileId = 'file_' + i;

				if (/^[A-Za-z]:/.test(fileUrl) || fileUrl.startsWith('file:')) {
					var wpContentIndex = fileUrl.indexOf('wp-content/');
					if (wpContentIndex !== -1) {
						var relativePath = fileUrl.substring(wpContentIndex);
						fileUrl = siteUrl.replace(/\/$/, '') + '/' + relativePath;
					} else {
						fileUrl = fileUrl.replace(/^file:\/+/, siteUrl + '/').replace(/\\/g, '/');
					}
				}

				if (!fileUrl.startsWith('http')) {
					console.warn('Skipping invalid attachment:', fileUrl);
					continue;
				}

				var fileLink = '<a href="' + fileUrl + '" target="_blank" class="file-link" title="Download ' + (basename || fileName) + '">' + (basename || fileName) + '</a>';

				string += '<div class="attachmentimage flex items-center gap-2">' +
							'<img src="' + bm_normal_object.attachment_image + '" alt="Attachment" class="w-5 h-5">' +
							'<span id="' + fileId + '" class="file-item">' +
								fileLink +
								'&nbsp;<button data-id="' + i + '" data-name="' + (basename || fileName) + '" data-guid="' + guid + '" class="remove_email_attachment text-red-500" title="' + bm_normal_object.remove + '" onclick="remove_email_attachmment(this)">' + crossSign + '</button>' +
							'</span>' +
						'</div>';

				fileNames.push(basename || fileName);
			}

			jQuery('#resend_email_attachment').val(fileNames.join(','));
			jQuery('#final_files').val(guids.join(','));
			jQuery('#fileList').append(string);
			jQuery('#fileList').show();
		}

		jQuery('#resend_mail_subject').val(subject);
		bm_insert_field_in_email(body);
		jQuery('#resend_email_modal').addClass('active-modal');
	});
}


// Resend mail
function bm_resend_email(type='') {
	jQuery("#resend-button").hide();
	jQuery("#resendProcess").show();
	var key = sessionStorage.getItem("current_resend_mail_key");
	var to = jQuery('#resend_mail_to').val();
	var cc = jQuery('#mail_cc').val();
	var bcc = jQuery('#mail_bcc').val();
	var subject = jQuery('#resend_mail_subject').val();
	var email_id = jQuery('#email_id_' + key).val();
	var module_id = jQuery('#module_id_' + key).val();
	var module_type = jQuery('#module_type_' + key).val();
	var mail_type = jQuery('#mail_type_' + key).val();
	var template_id = jQuery('#template_id_' + key).val();
	var process_id = jQuery('#process_id_' + key).val();
	var body = tinyMCE.activeEditor.getContent();
	var guids = jQuery('#final_files').val();
	guids = guids.split(',').filter(id => id && id.trim() !== '');
	// var rawtext = tinyMCE.activeEditor.getBody().textContent;

	var customFileUrls = [];

	if (type == 'checkin') {
		jQuery('#fileList .file-link').each(function () {
			var fileUrl = jQuery(this).attr('href');
			if (fileUrl && !fileUrl.includes('undefined')) {
				customFileUrls.push(fileUrl);
			}
		});
	}

	var post = {
		'to': to,
		'cc': cc,
		'bcc': bcc,
		'subject': subject,
		'body': body,
		'email_id': email_id,
		'module_id': module_id,
		'module_type': module_type,
		'mail_type': mail_type,
		'template_id': template_id,
		'process_id': process_id,
		'guids': guids,
		'custom_files': customFileUrls,
		'type': type,
	}

	var data = { 'action': 'bm_resend_email', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('#resend_email_modal').removeClass('active-modal');
		sessionStorage.removeItem("current_resend_mail_id");
		jQuery('#resend_email_attachment').val('');
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status ? jsondata.status : '';
		if (status == true) {
			showMessage(bm_success_object.mail_send_success, 'success');
			location.reload();
		} else {
			showMessage(bm_error_object.server_error, 'error');
		}
	});
}


// Check payment status
function check_payment_status($this) {
	var payment_status = jQuery($this).val();

	jQuery('#is_active').addClass('readonly_checkbox');
	jQuery('#is_active').parent().addClass('readonly_cursor');

	if (payment_status == 'refunded') {
		jQuery(document).find('#refund_id_input').removeClass('hidden');
	} else {
		jQuery(document).find('#refund_id_input').addClass('hidden');
	}

	if (payment_status == 'pending' || payment_status == 'succeeded' || payment_status == 'free') {
		jQuery('#is_active').removeClass('readonly_checkbox');
		jQuery('#is_active').parent().removeClass('readonly_cursor');
	}
}


jQuery(document).ready(function () {
	jQuery(document).on('change', '#email_attachment', add_email_attachment);
});

// Add file attachment
function add_email_attachment() {
	// jQuery('#fileList').hide();
	var allowedExtensions = ['.jpg', '.jpeg', '.png', '.pdf', '.svg', '.zip', '.docx', '.doc', '.xlsx', '.gif', '.ppt', '.csv'];
	var fileInput = document.getElementById('email_attachment');
	var fileList = fileInput.files;
	var totalFiles = fileList.length;
	var string = '';
	var error = 0;
	var duplicate = 0;
	var nextindex = 0;
	var formData = new FormData();
	var attachments = jQuery('#resend_email_attachment').val();
	attachments = attachments.split(',');
	var existing_guids = jQuery('#final_files').val();
	existing_guids = existing_guids.split(',');

	var crossSign = "✕";
	var total_element = jQuery(".file-item").length;
	var max = 6;

	if (total_element > 0) {
		var lastid = jQuery(".file-item:last").attr("id");
		var split_id = lastid.split("_");
		nextindex = Number(split_id[1]) + 1;
	}

	var limit = Number(total_element + totalFiles);

	if (limit <= max) {
		for (var i = 0; i < totalFiles; i++) {
			var fileExtension = fileList[i].name.substring(fileList[i].name.lastIndexOf('.')).toLowerCase();

			if (allowedExtensions.includes(fileExtension)) {
				var fileName = fileList[i].name;

				if (jQuery.inArray(fileName, attachments) == -1) {
					var fileId = 'file_' + nextindex;
					string += '<div class="attachmentimage"><img src=" ' + bm_normal_object.attachment_image + ' " ><span id="' + fileId + '" class="file-item">' + fileName + '&nbsp;<button data-id="' + nextindex + '" data-name="' + fileName + '" data-guid="" class="remove_email_attachment" title="' + bm_normal_object.remove + '" onclick="remove_email_attachmment(this)">' + crossSign + '</button></span></div>';
					attachments[nextindex] = fileName;
					formData.append(i, fileList[i]);
					nextindex++;
				} else {
					duplicate++;
				}
			} else {
				error++;
			}
		}
	} else {
		alert(bm_error_object.max_files_to_be_attached + max);
		jQuery('#fileList').show();
		return false;
	}

	if (error > 0) {
		alert(bm_error_object.choose_correct_file_type);
		return false;
	} else if (duplicate > 0) {
		alert(bm_error_object.duplicate_attachment);
		return false;
	} else {
		var key = sessionStorage.getItem("current_resend_mail_key");
		var email_id = jQuery(document).find('#email_id_' + key).val();

		formData.append('email_id', email_id);
		formData.append('existing_guids', existing_guids);
		formData.append('action', 'bm_add_email_attachment');
		formData.append('nonce', bm_ajax_object.nonce);

		jQuery.ajax({
			url: bm_ajax_object.ajax_url,
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function (response) {
				var jsondata = jQuery.parseJSON(response);
				var status = jsondata.status ? jsondata.status : '';
				var guids = jsondata.guids ? jsondata.guids : [];

				if (status == 1) {
					jQuery('.progress').show();
					var progress = 0;
					var interval = setInterval(function () {
						if (limit > 3) {
							progress += 10;
						} else if (limit > 1 && limit <= 3) {
							progress += 20;
						} else if (limit == 1) {
							progress += 50;
						}

						jQuery('.progress-bar').css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
						if (progress >= 100) {
							clearInterval(interval);
							jQuery('.progress').hide();
							jQuery('#resend_email_attachment').val(attachments.join(','));
							jQuery('#final_files').val(guids.join(','));
							jQuery('#fileList').append(string);
							jQuery('#fileList').show();

							jQuery('[id^=file_]').each(function (id, item) {
								var guid = guids[id];
								jQuery(item).find('button.remove_email_attachment').attr('data-guid', guid);
							});
						}
					}, 300);
				} else if (status == 2) {
					alert(bm_error_object.file_already_exists);
				} else if (status == 0) {
					alert(bm_error_object.file_upload_failed);
				} else {
					alert(bm_error_object.server_error);
				}
			},
			error: function (xhr, status, error) {
				alert(bm_error_object.server_error);
			}
		});
	}
}


// Remove file attachment
function remove_email_attachmment($this) {
	var key = sessionStorage.getItem("current_resend_mail_key");
	var email_id = jQuery(document).find('#email_id_' + key).val();
	var fileName = jQuery($this).data('name');
	var guid = jQuery($this).data('guid');
	var attachments = jQuery('#resend_email_attachment').val();
	attachments = attachments.split(',');
	var guids = jQuery('#final_files').val();
	guids = guids.split(',');

	var post = {
		'id': guid,
		'email_id': email_id,
		'guids': guids,
	}

	if (confirm(bm_normal_object.sure_remove_attchmnt)) {
		var data = { 'action': 'bm_remove_email_attachment', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';
			var guids = jsondata.guids ? jsondata.guids : [];

			if (status == true) {
				attachments = jQuery.grep(attachments, function (value) {
					return value != fileName;
				});

				jQuery('#resend_email_attachment').val(attachments.join(','));
				jQuery('#final_files').val(guids.join(','));
				jQuery($this).parents('.attachmentimage').remove();

				jQuery('[id^=file_]').each(function (id, item) {
					var guid = guids[id];
					jQuery(item).attr('id', 'file_' + id);
					jQuery(item).find('button.remove_email_attachment').attr('data-id', id);
					jQuery(item).find('button.remove_email_attachment').attr('data-guid', guid);
				});
			} else {
				alert(bm_error_object.server_error);
			}
		});
	}
}



// Clear unuploaded temporary file attachment
// function remove_unsent_temporary_email_attachment() {
// 	var email_id = sessionStorage.getItem("current_resend_mail_id");

// 	var post = {
// 		'email_id': email_id,
// 	}

// 	var data = { 'action': 'bm_remove_temporary_email_attachment', 'post': post, 'nonce': bm_ajax_object.nonce };
// 	jQuery.post(bm_ajax_object.ajax_url, data, function (status) {
// 		if (status == 1) {
// 			sessionStorage.removeItem("current_resend_mail_id");
// 			jQuery('#resend_email_attachment').val('');
// 			alert(bm_success_object.attachments_clearing_success);
// 		} else if (status == 0) {
// 			alert(bm_error_object.attachments_clearing_failed);
// 		} else if (status == 2) {
// 		} else {
// 			alert(bm_error_object.server_error);
// 		}
// 	});
// }


// Clear unuploaded temporary file attachment
function remove_unsent_temporary_email_attachment() {
	jQuery('#resend_email_attachment').val('');
	jQuery('#final_files').val('');
	jQuery('#fileList').empty();
	jQuery('#fileList').hide();
}



// Check unuploaded file attachment
jQuery(document).ready(function ($) {
	var current_screen = bm_normal_object.current_screen;

	if (current_screen == 'flexibooking_page_bm_email_records') {
		// if (sessionStorage.getItem("current_resend_mail_id") != null) {
		remove_unsent_temporary_email_attachment();
		// }
	}
});



// Copy text to clipboard
function bm_copy_text(element) {
    element.select();
    element.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(element.value);
    
    // Update tooltip
    var tooltip = element.nextElementSibling;
    if (tooltip) {
        tooltip.innerHTML = bm_normal_object.copied_to_clipboard;
    }
}



// Copy text to clipboard message
function bm_copy_message(element) {
    var tooltip = element.nextElementSibling;
    if (tooltip) {
        tooltip.innerHTML = bm_normal_object.copy_to_clipboard;
    }
}



// Global payment settings validation
function bm_payment_settings_validation() {
	jQuery('.errortext').html('');
	jQuery('.errortext').hide();
	var error = 0;

	jQuery('.bm_required').each(
		function (index, element) {
			if (jQuery('#bm_enable_stripe').is(':checked')) {
				var value = jQuery.trim(jQuery(this).children('input').val());
				if (value == '') {
					jQuery(this).children('.errortext').html(bm_error_object.required_field);
					jQuery(this).children('.errortext').show();
					error++;
				}
			}
		});

	if (error == 0) {
		return true;
	} else {
		return false;
	}
}



// Show admin credentials prompt for stripe credentials
function show_stripe_credentials($this) {
	if (jQuery($this).is(':checked')) {
		promptForAdminPassword();
	} else {
		jQuery('#stripe_credentials').hide();
	}
}



// Dialog to prompt for the admin password
function promptForAdminPassword() {
	jQuery('#stripe_credentials').hide();
	jQuery('.errortext').html('');
	jQuery('.errortext').hide();
	var error = 0;
	var html = '';
	// Create a jQuery dialog box
	var dialog = jQuery('<div>').dialog({
		title: bm_normal_object.enter_admin_credentials,
		modal: true,
		width: "450px",
		show: {
			effect: "slide",
			direction: 'down',
			duration: 1000
		},
		hide: {
			effect: "slide",
			direction: 'up',
			duration: 1000
		},
		close: function () {
			// Close the dialog box
			jQuery('#bm_show_stripe_credentials').prop('checked', false);
			dialog.dialog('destroy');
		},
		buttons: {
			'OK': function () {
				error = 0;
				jQuery('#stripe_credentials').hide();
				jQuery('.errortext').html('');
				jQuery('.errortext').hide();

				// Get the password from the input field
				var username = jQuery('#admin-username').val();
				var password = jQuery('#admin-password').val();

				jQuery('.bm_admin_required').each(function (index, element) {
					var value = jQuery.trim(jQuery(this).children('input').val());
					if (value == '') {
						jQuery(this).children('.errortext').html(bm_error_object.required_field);
						jQuery(this).children('.errortext').show();
						error++;
					}
				});

				if (error > 0) {
					return false;
				} else {
					var post = {
						'username': username,
						'password': password,
					}

					var data = { 'action': 'bm_check_admin_password', 'post': post, 'nonce': bm_ajax_object.nonce };
					jQuery.post(bm_ajax_object.ajax_url, data, function (status) {
						if (status == true) {
							jQuery('#bm_show_stripe_credentials').prop('checked', true);
							jQuery('#stripe_credentials').show();
							dialog.dialog('destroy');
						} else {
							// Display an error message
							jQuery('#bm_show_stripe_credentials').prop('checked', false);
							jQuery('#admin-password-parent').children('.errortext').html(bm_error_object.verification_failed);
							jQuery('#admin-password-parent').children('.errortext').show();
							return false;
						}
					});
				}
			},
			'Cancel': function () {
				jQuery('#stripe_credentials').hide();
				jQuery('.errortext').html('');
				jQuery('.errortext').hide();

				// Close the dialog box
				jQuery('#bm_show_stripe_credentials').prop('checked', false);
				dialog.dialog('destroy');
			}
		}
	});

	// Add inputs to dialogue
	html += '<table role="presentation">';
	html += '<tr>';
	html += '<th scope="row"><label for="admin-username">' + bm_normal_object.username + '<strong class="required_asterisk"> *</strong></label></th>';
	html += '<td class="bminput bm_admin_required">';
	html += '<input type="text" id="admin-username" placeholder="' + bm_normal_object.admin_username + '" value="" class="regular-text" style="width:316px;" autocomplete="off">';
	html += '<div class="errortext"></div>';
	html += '</td>';
	html += '</tr>';
	html += '<tr>';
	html += '<th scope="row"><label for="admin-password">' + bm_normal_object.password + '<strong class="required_asterisk"> *</strong></label></th>';
	html += '<td class="bminput bm_admin_required" id="admin-password-parent">';
	html += '<input type="password" id="admin-password" placeholder="' + bm_normal_object.admin_password + '" value="" class="regular-text" style="width:316px;" autocomplete="new-password">';
	html += '<div class="errortext"></div>';
	html += '</td>';
	html += '</tr>';
	html += '</table>';

	dialog.append(html);
}



// Pagination settings for category and service pages
jQuery(document).ready(function ($) {
	$(document).on('click', 'div#category_records_listing a.page-numbers', function (e) {
		e.preventDefault();
		var id = $(this).parents('div.listing_table').attr('id');
		var divClass = id.split('_pagination')[0];
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];

		sessionStorage.setItem("categoryPagno", pagenum);

		if (divClass == 'category_records_listing') {
			$('#category_pagenum').val(pagenum ? pagenum : '1');
			bm_sort_category_listing([], pagenum ? pagenum : '1');
		}
	});

	$(document).on('click', 'div#service_records_listing a.page-numbers', function (e) {
		e.preventDefault();
		var id = $(this).parents('div.listing_table').attr('id');
		var divClass = id.split('_pagination')[0];
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];

		sessionStorage.setItem("servicePagno", pagenum);

		if (divClass == 'service_records_listing') {
			$('#service_pagenum').val(pagenum ? pagenum : '1');
			bm_sort_service_listing([], pagenum ? pagenum : '1');
		}
	});

	$(document).on('click', 'div#templates_records_listing a.page-numbers', function (e) {
		e.preventDefault();
		var id = $(this).parents('div.listing_table').attr('id');
		var divClass = id.split('_pagination')[0];
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];

		sessionStorage.setItem("templatePagno", pagenum);

		if (divClass == 'templates_records_listing') {
			$('#template_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_template_listing(pagenum ? pagenum : '1');
		}
	});

	$(document).on('click', 'div#price_module_records_listing a.page-numbers', function (e) {
		e.preventDefault();
		var id = $(this).parents('div.listing_table').attr('id');
		var divClass = id.split('_pagination')[0];
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];

		sessionStorage.setItem("priceModulePagno", pagenum);

		if (divClass == 'price_module_records_listing') {
			$('#price_module_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_price_module_listing(pagenum ? pagenum : '1');
		}
	});

	$(document).on('click', 'div#notification_process_records_listing a.page-numbers', function (e) {
		e.preventDefault();
		var id = $(this).parents('div.listing_table').attr('id');
		var divClass = id.split('_pagination')[0];
		var hrefString = $(this).attr('href');
		var pagenum = getUrlVars(hrefString)["pagenum"];

		sessionStorage.setItem("notificationProcessPagno", pagenum);

		if (divClass == 'notification_process_records_listing') {
			$('#notification_process_pagenum').val(pagenum ? pagenum : '1');
			bm_fetch_notification_processes_listing(pagenum ? pagenum : '1');
		}
	});
});



// Fetch templates
function bm_fetch_template_listing(pagenum = '') {
	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#template_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
	}

	var data = { 'action': 'bm_fetch_template_listing', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".template_records").html('');
			jQuery(".template_pagination").html('');
			var templates = jsondata.templates ? jsondata.templates : [];
			var type_names = jsondata.type_name ? jsondata.type_name : '';
			var pagination = jsondata.pagination ? jsondata.pagination : '';
			var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : 1;
			var templateListing = '';
			var j = 0;

			if (templates.length != 0) {
				for (var i = 0; i < templates.length; i++) {
					templateListing += "<tr><form role='form' method='post'>" +
						"<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
						"<td style='text-align: center;' title=" + (templates[i].tmpl_name_en ? templates[i].tmpl_name_en : templates[i].tmpl_name_it) + ">" + (templates[i].tmpl_name_en ? templates[i].tmpl_name_en.substring(0, 80) : templates[i].tmpl_name_it.substring(0, 80)) + '...' + " </td>" +
						"<td style='text-align: center;' title=" + (type_names[i] ? type_names[i] : '') + ">" + (type_names[i] ? type_names[i].substring(0, 80) + '...' : '') + "</td>" +
						"<td style='text-align: center;' class='bm-checkbox-td'>" +
						"<input name='bm_template_status' type='checkbox' id='bm_template_status_" + templates[i].id + "' data-type='" + (templates[i].type ? templates[i].type : -1) + "' class='regular-text auto-checkbox bm_toggle' " + (templates[i].status == 1 ? 'checked' : '') + " onchange='bm_change_template_visibility(this)'>" +
						"<label for='bm_template_status_" + templates[i].id + "'></label>" +
						"</td>" +
						"<td style='text-align: center;'>" +
						"<button type='button' name='edittemplate' class='edit-button' id='edittemplate' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + templates[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
						"<button type='button' name='deltemplate' class='delete-button' id='deltemplate' title='" + bm_normal_object.remove + "' value='" + templates[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
						"</td>" +
						"</form></tr>";
					current_pagenumber++;
					j++;
				}
			} else {
				location.reload();
			}

			jQuery(".template_records").append(templateListing);
			jQuery(".template_pagination").append(pagination);
		}
	});
}



// Fetch price modules
function bm_fetch_price_module_listing(pagenum = '') {
	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#price_module_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
	}

	var data = { 'action': 'bm_fetch_price_module_listing', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".price_module_records").html('');
			jQuery(".price_module_pagination").html('');
			var priceModules = jsondata.price_modules ? jsondata.price_modules : [];
			var pagination = jsondata.pagination ? jsondata.pagination : '';
			var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : 1;
			var priceModuleListing = '';
			var j = 0;

			if (priceModules.length != 0) {
				for (var i = 0; i < priceModules.length; i++) {
					priceModuleListing += "<tr class='single_price_module_record'><form role='form' method='post'>" +
						"<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
						"<td style='text-align: center;' title=" + (priceModules[i].module_name ? priceModules[i].module_name : '') + ">" + (priceModules[i].module_name ? priceModules[i].module_name.substring(0, 80) : '') + '...' + " </td>" +
						"<td style='text-align: center;'>" +
						"<button type='button' name='editmodule' class='edit-button' id='editmodule' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + priceModules[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
						"<button type='button' name='delmodule' class='delete-button' id='delmodule' title='" + bm_normal_object.remove + "' value='" + priceModules[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
						"</td>" +
						"</form></tr>";
					current_pagenumber++;
					j++;
				}
			} else {
				location.reload();
			}

			jQuery(".price_module_records").append(priceModuleListing);
			jQuery(".price_module_pagination").append(pagination);
		}
	});
}



// Fetch notification processes
function bm_fetch_notification_processes_listing(pagenum = '') {
	var post = {
		'pagenum': pagenum != '' ? pagenum : jQuery('#notification_process_pagenum').val(),
		'base': jQuery(location).attr("href"),
		'limit': jQuery.trim(jQuery('#limit_count').val()),
	}

	var data = { 'action': 'bm_fetch_notification_processes_listing', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			jQuery(".notification_process_records").html('');
			jQuery(".notification_process_pagination").html('');
			var notificationProcesses = jsondata.notification_processes ? jsondata.notification_processes : [];
			var process_types = jsondata.process_type ? jsondata.process_type : [];
			var pagination = jsondata.pagination ? jsondata.pagination : '';
			var current_pagenumber = jsondata.current_pagenumber ? jsondata.current_pagenumber : 1;
			var notificationProcessListing = '';
			var j = 0;

			if (notificationProcesses.length != 0) {
				for (var i = 0; i < notificationProcesses.length; i++) {
					notificationProcessListing += "<tr><form role='form' method='post'>" +
						"<td style='text-align: center;'>" + (current_pagenumber ? current_pagenumber : (i + 1)) + "</td>" +
						"<td style='text-align: center;' title=" + (notificationProcesses[i].name ? notificationProcesses[i].name : '') + ">" + (notificationProcesses[i].name ? notificationProcesses[i].name.substring(0, 80) : '') + '...' + " </td>" +
						"<td style='text-align: center;' title=" + (process_types[i] ? process_types[i] : '') + ">" + (process_types[i] ? process_types[i].substring(0, 80) : '') + '...' + " </td>" +
						"<td style='text-align: center;' class='bm-checkbox-td'>" +
						"<input name='bm_process_status' type='checkbox' id='bm_process_status_" + notificationProcesses[i].id + "' data-type='" + (notificationProcesses[i].type ? notificationProcesses[i].type : -1) + "' class='regular-text auto-checkbox bm_toggle' " + (notificationProcesses[i].status == 1 ? 'checked' : '') + " onchange='bm_change_process_visibility(this)'>" +
						"<label for='bm_process_status_" + notificationProcesses[i].id + "'></label>" +
						"</td>" +
						"<td style='text-align: center;'>" +
						"<button type='button' name='editprocess' class='edit-button' id='editprocess' style='margin-right:3px' title='" + bm_normal_object.edit + "' value='" + notificationProcesses[i].id + "'><i class='fa fa-edit' aria-hidden='true'></i></button>" +
						"<button type='button' name='delprocess' class='delete-button' id='delprocess' title='" + bm_normal_object.remove + "' value='" + notificationProcesses[i].id + "'><i class='fa fa-trash' aria-hidden='true' style='color:red'></i></button>" +
						"</td>" +
						"</form></tr>";
					current_pagenumber++;
					j++;
				}
			} else {
				location.reload();
			}

			jQuery(".notification_process_records").append(notificationProcessListing);
			jQuery(".notification_process_pagination").append(pagination);
		}
	});
}




// Prevent right click on payment settings page
jQuery(document).ready(function ($) {
	var current_screen = bm_normal_object.current_screen;

	// if (current_screen == 'admin_page_bm_global_payment_settings' || current_screen == 'flexibooking_page_bm_all_orders') {
	// 	document.addEventListener("contextmenu", function (e) {
	// 		e.preventDefault();
	// 	}, false);
	// }

	// var pluginElement = document.querySelector('.sg-admin-main-box');
	// console.log(pluginElement);

	// if (pluginElement) {
	// 	// block right clicks
	// 	pluginElement.addEventListener("contextmenu", function (e) {
	// 		e.preventDefault();
	// 	}, false);

	// 	// block left clicks
	// 	pluginElement.addEventListener("click", function (e) {
	// 		e.preventDefault();
	// 	}, false);

	// 	// block middle clicks
	// 	pluginElement.addEventListener("mousedown", function (e) {
	// 		if (e.button === 1) { // Middle click is usually button 1
	// 			e.preventDefault();
	// 		}
	// 	}, false);
	// }
});




// Swicth flexibooking language
function change_flexi_language($this) {
	var lang_code = jQuery($this).val();
	sessionStorage.setItem("flexi_current_lang", lang_code);

	var post = {
		'flexi_lang_code': lang_code,
	}

	var data = { 'action': 'bm_flexi_set_lang', 'post': post, 'nonce': bm_ajax_object.nonce };
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




// Array sum
function array_sum(arr) {
	return arr.reduce((a, b) => a + b, 0);
}




function customer_form_validation() {
	let b = 0;
	jQuery('.errortext').html('').hide();
	jQuery('.billing_field_errortext').html('').hide();

	const tel_pattern = /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/;

	jQuery('.bm_required').each(function () {
		const input = jQuery(this).find('input, select');
		const value = jQuery.trim(input.val());

		if (!value) {
			jQuery(this).find('.errortext').html(bm_error_object.required_field).show();
			b++;
		} else if (input.attr('type') === 'email') {
			const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
			if (!regex.test(value)) {
				jQuery(this).find('.errortext').html(bm_error_object.invalid_email).show();
				b++;
			}
		} else if (input.attr('id') === 'tel') {
			if (!tel_pattern.test(value)) {
				jQuery(this).find('.errortext').html(bm_error_object.invalid_contact).show();
				b++;
			}
		}
	});

	if (jQuery('#billing_contact').val() == '') {
		jQuery('.billing_contact_field').find('.billing_field_errortext').html(bm_error_object.required_field).show();
		b++;
	} else if (!tel_pattern.test(jQuery('#billing_contact').val())) {
		jQuery('.billing_contact_field').find('.billing_field_errortext').html(bm_error_object.invalid_contact).show();
		b++;
	}

	if (jQuery('#shipping_contact').val() == '') {
		jQuery('.shipping_contact_field').find('.billing_field_errortext').html(bm_error_object.required_field).show();
		b++;
	} else if (!tel_pattern.test(jQuery('#shipping_contact').val())) {
		jQuery('.shipping_contact_field').find('.billing_field_errortext').html(bm_error_object.invalid_contact).show();
		b++;
	}


	if (b > 0) {
		return Promise.resolve(false);
	}

	const post = {
		main_email: jQuery('#customer_email').val(),
		billing_email: jQuery('#billing_email').val(),
		shipping_email: jQuery('#shipping_email').val(),
		customer_id: getUrlParameter('id'),
	};

	const data = {
		action: 'bm_check_if_exisiting_customer',
		post: post,
		nonce: bm_ajax_object.nonce,
	};

	return jQuery.post(bm_ajax_object.ajax_url, data)
		.then(response => {
			let c = 0;

			if (response.success) {
				if (response.data) {
					if (response.data.main_email) {
						jQuery('#customer_email').next('.errortext').html(bm_error_object.existing_mail).show();
						c++;
					}
					if (response.data.billing_email) {
						jQuery('#billing_email').next('.errortext').html(bm_error_object.existing_mail).show();
						c++;
					}
					if (response.data.shipping_email) {
						jQuery('#shipping_email').next('.errortext').html(bm_error_object.existing_mail).show();
						c++;
					}
				}
			} else {
				showMessage(bm_error_object.server_error, 'error');
				c++;
			}

			return c === 0;
		})
		.catch(() => {
			showMessage(bm_error_object.server_error, 'error');
			return false;
		});
}




jQuery(document).ready(function () {
	jQuery('#customer_form').on('submit', function (e) {
		e.preventDefault();

		customer_form_validation().then(isValid => {
			if (isValid) {
				if (!jQuery('input[name="savecust"]').length) {
					jQuery(this).append('<input type="hidden" name="savecust" value="1">');
				}
				this.submit();
			}
		});
	});
});




// Fill checkout page states wrt country
jQuery(document).ready(async function ($) {
	const country_code = $.trim($('select[id="billing_country"]').val());

	if (country_code) {
		await bm_get_state_of_country(country_code, $('select[id="billing_state"]'));
	}

	$('select[id="billing_country"], select[id="shipping_country"], select[id="recipient_country"]').on('change', async function () {
		const country = $(this).val();
		let stateField;

		$('.loader_modal').show();

		if ($(this).attr('id') === 'billing_country') {
			stateField = $('select[id="billing_state"]');
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




// Voucher booking information dialogue
function bm_show_vocuher_booking_info($this) {
	const data = {
		action: 'bm_fetch_vocuher_booking_info',
		order_id: jQuery($this).attr('id'),
		nonce: bm_ajax_object.nonce
	};

	jQuery('.loader_modal').show();

	jQuery.post(bm_ajax_object.ajax_url, data)
		.done(function (response) {
			jQuery("#voucher-data").empty();

			if (response.success && response.data) {
				jQuery("#voucher-data").append(response.data);
			} else {
				jQuery("#voucher-data").append("<div class='error_msg'>" + (response.data ? response.data : bm_error_object.server_error) + "</div>");
			}

			jQuery("#voucher-info-dialog").dialog("open");
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			alert(bm_error_object.server_error);
		})
		.always(function () {
			jQuery('.loader_modal').hide();
		});
}




// Voucher gifter information dialogue
function bm_show_vocuher_gifter_info($this) {
	const data = {
		action: 'bm_fetch_vocuher_gifter_info',
		order_id: jQuery($this).attr('id'),
		nonce: bm_ajax_object.nonce
	};

	jQuery('.loader_modal').show();

	jQuery.post(bm_ajax_object.ajax_url, data)
		.done(function (response) {
			jQuery("#voucher-data").empty();

			if (response.success && response.data) {
				const customer_info = response.data;
				if (customer_info.length !== 0) {
					var first_name = typeof (customer_info.billing_first_name) != "undefined" && customer_info.billing_first_name != null ? customer_info.billing_first_name : 'N/A';
					var last_name = typeof (customer_info.billing_last_name) != "undefined" && customer_info.billing_last_name != null ? customer_info.billing_last_name : 'N/A';
					var email = typeof (customer_info.billing_email) != "undefined" && customer_info.billing_email != null ? customer_info.billing_email : 'N/A';
					var mobile = typeof (customer_info.billing_contact) != "undefined" && customer_info.billing_contact != null ? customer_info.billing_contact : 'N/A';
					var city = typeof (customer_info.billing_city) != "undefined" && customer_info.billing_city != null ? customer_info.billing_city : 'N/A';
					var state = typeof (customer_info.billing_state) != "undefined" && customer_info.billing_state != null ? customer_info.billing_state : 'N/A';
					var country = typeof (customer_info.billing_country) != "undefined" && customer_info.billing_country != null ? customer_info.billing_country : 'N/A';

					var listItem = jQuery("<li class='voucher-customer-info'></li>");
					listItem.append("<div><strong>" + bm_normal_object.first_name + "</strong> : " + first_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.last_name + "</strong> : " + last_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.email + "</strong> : " + email + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.phone + "</strong> : " + mobile + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.city + "</strong> : " + city + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.state + "</strong> : " + state + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.country + "</strong> : " + country + "</div>");
					jQuery("#voucher-data").append(listItem);
				} else {
					jQuery("#voucher-data").append("<div class='error_msg'>" + bm_error_object.customer_error + "</div>");
				}
			} else {
				jQuery("#voucher-data").append("<div class='error_msg'>" + (response.data ? response.data : bm_error_object.customer_error) + "</div>");
			}
			jQuery("#voucher-info-dialog").dialog("open");
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			alert(bm_error_object.server_error);
		})
		.always(function () {
			jQuery('.loader_modal').hide();
		});
}




// Voucher recipient information dialogue
function bm_show_vocuher_recipient_info($this) {
	const data = {
		action: 'bm_fetch_vocuher_recipient_info',
		code: jQuery($this).attr('id'),
		nonce: bm_ajax_object.nonce
	};

	jQuery('.loader_modal').show();

	jQuery.post(bm_ajax_object.ajax_url, data)
		.done(function (response) {
			jQuery("#voucher-data").empty();

			if (response.success && response.data) {
				const customer_info = response.data;
				if (customer_info.length !== 0) {
					var first_name = typeof (customer_info.recipient_first_name) != "undefined" && customer_info.recipient_first_name != null ? customer_info.recipient_first_name : 'N/A';
					var last_name = typeof (customer_info.recipient_last_name) != "undefined" && customer_info.recipient_last_name != null ? customer_info.recipient_last_name : 'N/A';
					var email = typeof (customer_info.recipient_email) != "undefined" && customer_info.recipient_email != null ? customer_info.recipient_email : 'N/A';
					var mobile = typeof (customer_info.recipient_contact) != "undefined" && customer_info.recipient_contact != null ? customer_info.recipient_contact : 'N/A';
					var city = typeof (customer_info.recipient_city) != "undefined" && customer_info.recipient_city != null ? customer_info.recipient_city : 'N/A';
					var state = typeof (customer_info.billing_state) != "undefined" && customer_info.billing_state != null ? customer_info.billing_state : 'N/A';
					var country = typeof (customer_info.recipient_country) != "undefined" && customer_info.recipient_country != null ? customer_info.recipient_country : 'N/A';

					var listItem = jQuery("<li class='voucher-customer-info'></li>");
					listItem.append("<div><strong>" + bm_normal_object.first_name + "</strong> : " + first_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.last_name + "</strong> : " + last_name + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.email + "</strong> : " + email + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.phone + "</strong> : " + mobile + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.city + "</strong> : " + city + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.state + "</strong> : " + state + "</div>");
					listItem.append("<div><strong>" + bm_normal_object.country + "</strong> : " + country + "</div>");
					jQuery("#voucher-data").append(listItem);
				} else {
					jQuery("#voucher-data").append("<div class='error_msg'>" + bm_error_object.customer_error + "</div>");
				}
			} else {
				jQuery("#voucher-data").append("<div class='error_msg'>" + (response.data ? response.data : bm_error_object.customer_error) + "</div>");
			}
			jQuery("#voucher-info-dialog").dialog("open");
		})
		.fail(function (jqXHR, textStatus, errorThrown) {
			alert(bm_error_object.server_error);
		})
		.always(function () {
			jQuery('.loader_modal').hide();
		});
}




function bm_change_voucher_status($this) {
	let inputStatus = jQuery($this).is(':checked') ? 1 : 0;

	if (confirm(bm_normal_object.change_voucher_vsiblity)) {
		const post = {
			'code': $this.id.split('_')[3],
			'status': inputStatus,
		}

		const data = {
			action: 'bm_change_voucher_status',
			post,
			nonce: bm_ajax_object.nonce
		};

		jQuery('.loader_modal').show();

		jQuery.post(bm_ajax_object.ajax_url, data)
			.done(function (response) {
				if (response.success) {
					showMessage(bm_success_object.status_successfully_changed, 'success');
					location.reload();
				} else {
					inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
					showMessage(response.data ? response.data : bm_error_object.server_error, 'error');
				}
			})
			.fail(function (jqXHR, textStatus, errorThrown) {
				showMessage(bm_error_object.server_error, 'error');
			})
			.always(function () {
				jQuery('.loader_modal').hide();
			});
	} else {
		inputStatus == 1 ? jQuery('#' + $this.id).prop('checked', false) : jQuery('#' + $this.id).prop('checked', true);
	}
}




// Sticky header
jQuery(document).ready(function ($) {
  const table = $(".booking-table").length > 0 ? document.querySelector(".booking-table") : '';
  const wrapper = $(".table-wrapper").length > 0 ? document.querySelector(".table-wrapper") : '';

  // Clone the header
  if ( table && wrapper ) {
	const clonedHeader = table.querySelector("thead").cloneNode(true);
	const headerTable = document.createElement("table");
	headerTable.className = "booking-table cloned-header";
	headerTable.style.tableLayout = "fixed"; // prevents collapse
	headerTable.appendChild(clonedHeader);

	// Append inside wrapper
	wrapper.appendChild(headerTable);
	headerTable.style.position = "absolute";
	headerTable.style.top = "0";
	headerTable.style.left = "0";
	headerTable.style.display = "none";
	headerTable.style.background = "#f1f1f1";
	headerTable.style.zIndex = "5";

	function syncHeaderSize() {
		const originalTh = table.querySelectorAll("thead th");
		const clonedTh = headerTable.querySelectorAll("th");

		clonedTh.forEach((th, i) => {
		if (originalTh[i]) {
			th.style.width = originalTh[i].offsetWidth + "px";
		}
		});

		headerTable.style.width = table.offsetWidth + "px"; // total width match
	}

	// Sync horizontal scroll
	wrapper.addEventListener("scroll", function () {
		headerTable.scrollLeft = wrapper.scrollLeft;
	});

	// Show/hide on vertical scroll
	window.addEventListener("scroll", function () {
		const wrapperRect = wrapper.getBoundingClientRect();
		if (wrapperRect.top <= 0 && wrapperRect.bottom > 0) {
		headerTable.style.display = "table";
		headerTable.style.top = `${Math.max(0, -wrapperRect.top)}px`;
		syncHeaderSize(); // keep alignment updated
		} else {
		headerTable.style.display = "none";
		}
	});

	window.addEventListener("resize", syncHeaderSize);
	syncHeaderSize();

  }
});




jQuery(document).ready(function($) {
    // Initialize your daterangepicker
    $('#service_date_range_picker').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear',
            format: 'YYYY-MM-DD'
        },
        opens: 'left'
    });

    // When user selects a range
    $('#service_date_range_picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
    });

    $('#service_date_range_picker').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Add selected range to list
    $('#add_date_range').on('click', function() {
        const val = $('#service_date_range_picker').val();
        if (!val) {
            alert('Please select a date range first.');
            return;
        }

        let overlap = false;
        $('#unavailable_date_ranges input').each(function() {
            if ($(this).val() === val) overlap = true;
        });
        if (overlap) {
            alert('This range is already added.');
            return;
        }

        const rangesContainer = $('#unavailable_date_ranges');
        const index = rangesContainer.find('.date_range_span').length + 1;

        const span = `
            <span class="date_range_span">
                <input type="text" readonly id="unavailable_date_range_${index}" 
                    name="service_unavailability[dates][${index}]" 
                    value="${val}" class="date_range_input">
                <button type="button" class="remove_range" onclick="bm_remove_unavailable_range(this)">✕</button>
            </span>
        `;

        rangesContainer.append(span);
        $('#service_date_range_picker').val(''); // clear after adding
    });
});




function bm_remove_unavailable_range(el) {
    if (confirm('Remove this date range?')) {
        jQuery(el).parent('span').remove();

        // Reindex
        jQuery('#unavailable_date_ranges .date_range_span input').each(function(index) {
            const i = index + 1;
            jQuery(this).attr('id', 'unavailable_date_range_' + i);
            jQuery(this).attr('name', 'service_unavailability[dates][' + i + ']');
        });
    }
}




jQuery(document).ready(function($) {
    $('#global_date_range_picker').daterangepicker({
        autoUpdateInput: false,
        locale: { cancelLabel: 'Clear', format: 'YYYY-MM-DD' }
    });

    $('#global_date_range_picker').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('YYYY-MM-DD') + ' to ' + picker.endDate.format('YYYY-MM-DD'));
    }).on('cancel.daterangepicker', function() {
        $(this).val('');
    });

    $('#add_global_date_range').on('click', function() {
        const val = $('#global_date_range_picker').val();
        if (!val) return alert('Please select a date range.');

        let overlap = false;
        $('#global_unavailable_date_ranges input').each(function() {
            if ($(this).val() === val) overlap = true;
        });
        if (overlap) return alert('This range is already added.');

        const index = $('#global_unavailable_date_ranges .date_range_span').length + 1;
        $('#global_unavailable_date_ranges').append(`
            <span class="date_range_span">
                <input type="text" readonly name="bm_global_unavailability[dates][${index}]" value="${val}" class="date_range_input">
                <button type="button" class="remove_range" onclick="bm_remove_unavailable_range(this)">✕</button>
            </span>
        `);
        $('#global_date_range_picker').val('');
    });
});




function bm_remove_unavailable_range(el) {
    jQuery(el).parent('span').remove();
}




function bm_remove_global_unavailable_range(el) {
    if (confirm('Remove this date range?')) {
        jQuery(el).parent('span').remove();

        jQuery('#global_unavailable_date_ranges .date_range_span input').each(function(index) {
            const i = index + 1;
            jQuery(this).attr('id', 'global_unavailable_date_range_' + i);
            jQuery(this).attr('name', 'bm_global_unavailability[dates][' + i + ']');
        });
    }
}




jQuery(document).ready(function($) {
	var custom_uploader;

	$('.upload_pdf_logo').click(function (e) {
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
					$('#pdf_logo_guid').val(attachment.id);
					$('#pdf_logo_preview').attr('src', attachment.url);
					$('.pdf_logo_container').show();
				}
			} else {
				alert(bm_error_object.file_type_not_supported);
			}

		});

		custom_uploader.open();
	});
});




function bm_remove_pdf_logo() {
	jQuery('#pdf_logo_guid').val('');
	jQuery('#pdf_logo_preview').attr('src', '');
	jQuery('.pdf_logo_container').hide();
}




// View pdf sample
jQuery(document).on('click', '.bm-view-pdf-sample', function(e) {
    e.preventDefault();
    
    jQuery('.pdf-sample-container').html('');
    jQuery('#pdf-sample-modal').addClass('active-modal');
    jQuery('.loader_modal').show();

    let type = jQuery(this).data('type') || 'booking';

    jQuery.post(bm_ajax_object.ajax_url, {
        action: 'view_pdf_content',
        type: type,
        nonce: bm_ajax_object.nonce
    }, function(response) {
        jQuery('.loader_modal').hide();
        
        if (response.success) {
            jQuery('.pdf-sample-container').html(response.data);
        } else {
            jQuery('.pdf-sample-container').html(
                response.data ? response.data : 'Error loading preview. Please try again.'
            );
        }
    }).fail(function(xhr, status, error) {
        jQuery('.loader_modal').hide();
        jQuery('.pdf-sample-container').html(
            '<div style="color: red;">Server error: ' + error + '. Please check console.</div>'
        );
    });
});

// Close modal function
function closeModal(modalId) {
    jQuery('#' + modalId).removeClass('active-modal');
    jQuery('.pdf-sample-container').html('');
}

