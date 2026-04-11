jQuery(document).ready(function ($) {
	if (getUrlParameter('id') == '') {
		bm_add_per_age_group_html();
	}
});

const ageGroups = bm_normal_object.age_groups;

function bm_add_per_age_group_html() {
	jQuery('#per_age_group_fields').html('');
	var option_box = '<tr>';
	var index = 0;
	ageGroups.forEach(function (group) {
		if (group.type == "infant") {
			option_box += '<th scope="row"><label>' + bm_normal_object.age_price_settings + '</label></th>';
		}
		option_box += '<td class="bm_per_age_group_values" id="age_group_' + index + '">';
		option_box += '<span class="bminput bm_required"><input name="module_values[age_group_name][' + index + ']" type="text" id="age_group_name_' + index + '" placeholder="' + bm_normal_object.name + '" class="per_age_group_input no_border" value="' + group.name + '" onfocus="blur();" autocomplete="off" readonly>&nbsp;&nbsp;<span class="errortext"></span></span>&nbsp;&nbsp;&nbsp;&nbsp;';
		option_box += '<span class="bminput bm_required"><input name="module_values[age_group_from][' + index + ']" type="number" id="age_group_from_' + index + '" placeholder="' + bm_normal_object.from + '" class="per_age_group_input no_border" min="0" max="" value="' + group.from + '" onfocus="blur();" autocomplete="off" readonly>&nbsp;&nbsp;<span class="errortext"></span></span>';
		option_box += '&nbsp;<span>-</span>&nbsp;';
		option_box += '<span class="bminput bm_required"><input name="module_values[age_group_to][' + index + ']" type="number" id="age_group_to_' + index + '" placeholder="' + bm_normal_object.to + '" class="per_age_group_input" min="' + group.from + '" max="" value="' + group.to + '" onchange="checkAgeGroupFromValue(this)" autocomplete="off">&nbsp;&nbsp;<span class="errortext"></span></span>&nbsp;&nbsp;&nbsp;&nbsp;';
		option_box += '<span class="bminput bm_required"><input name="module_values[age_group_price][' + index + ']" type="text" id="age_group_price_' + index + '" placeholder="' + bm_normal_object.price + '" class="per_age_group_input per_age_group_price_input" value="" autocomplete="off">&nbsp;&nbsp;<span class="errortext"></span></span>&nbsp;';
		option_box += '<span><input type="hidden" name="module_values[age_group_disable][' + index + ']" value="0"><input name="module_values[age_group_disable][' + index + ']" type="checkbox" id="age_group_disable_' + index + '" value="1" onchange="disablePerAgeGroup(this)"></span>&nbsp;' + bm_normal_object.disable + '&nbsp;&nbsp;&nbsp;&nbsp;';
		if (index == 0) {
			option_box += '<span class="price_module_info_text">' + bm_normal_object.module_per_age_info + '</span>';
		}
		option_box += '</td>';
		index++;
	});
	option_box += '</tr>';
	jQuery('#per_age_group_fields').append(option_box);
}

function external_price_module_validation() {
	jQuery('.errortext').html('');
	jQuery('.errortext').hide();
	var b = 0;

	jQuery('.bm_required').each(
		function (index, element) {
			if (jQuery(this).parents('.form-table').is(':visible')) {
				var value = jQuery.trim(jQuery(this).children('input').val());
				if (value == "") {
					jQuery(this).children('.errortext').html(bm_error_object.required);
					jQuery(this).children('.errortext').show();
					b++;
				} else if (jQuery(this).children('input').attr('id').startsWith('age_group_price_') || jQuery(this).children('input').attr('id').startsWith('group_price_')) {
					var regex = /^\d+(?:\.\d{1,2})?$/;
					if (!value.match(regex)) {
						jQuery(this).children('.errortext').html(bm_error_object.price_field);
						jQuery(this).children('.errortext').show();
						b++;
					}
				}
			}
		}
	);

	if (b == 0) {
		return true;
	} else {
		return false;
	}
}

function bm_add_price_group() {
	var id = jQuery('#per_group_fields td.bm_per_group_values:last').attr('id');
	var currentIndex = Number(id.split("_")[1]);
	var nextindex = Number(currentIndex) + 1;
	var inputValue = Number(jQuery('#group_to_' + currentIndex).val()) + 1;

	var option_box = '<td class="bm_per_group_values" id="option_' + nextindex + '">';
	option_box += '<span class="bminput bm_required"><input name="module_values[group_from][' + nextindex + ']" type="number" id="group_from_' + nextindex + '" placeholder="' + bm_normal_object.from + '" class="per_group_input no_border" min="' + inputValue + '" max="" value="' + inputValue + '" onfocus="blur();" autocomplete="off" readonly>&nbsp;&nbsp;<span class="errortext"></span></span>';
	option_box += '&nbsp;&nbsp;<span>-</span>&nbsp;';
	option_box += '<span class="bminput bm_required"><input name="module_values[group_to][' + nextindex + ']" type="number" id="group_to_' + nextindex + '" placeholder="' + bm_normal_object.to + '" class="per_group_input" min="' + inputValue + '" max="" value="' + inputValue + '" onchange="checkGroupFromValue(this)" autocomplete="off">&nbsp;&nbsp;<span class="errortext"></span></span>&nbsp;';
	option_box += '<span class="bminput bm_required"><input name="module_values[group_price][' + nextindex + ']" type="text" id="group_price_' + nextindex + '" placeholder="' + bm_normal_object.price + '" class="per_group_input per_group_price_input" value="" autocomplete="off">&nbsp;&nbsp;<span class="errortext"></span></span>&nbsp;';
	option_box += '<span><input type="hidden" name="module_values[group_disable][' + nextindex + ']" value="0"><input name="module_values[group_disable][' + nextindex + ']" type="checkbox" id="group_disable_' + nextindex + '" value="1" onchange="disablePerGroup(this)"></span>&nbsp;' + bm_normal_object.disable + '&nbsp;';
	option_box += '&nbsp;<span class="bm_remove_shop_admin_email_field no_left_space" id="group_remove_' + nextindex + '" title="' + bm_normal_object.remove + '" onClick="bm_remove_per_group_option(this)">' + bm_normal_object.cross_sign + '</span>&nbsp;&nbsp';
	option_box += '</td>';

	jQuery('#per_group_fields td.bm_per_group_values:last').after(option_box);
	jQuery('#per_group_fields td.bm_per_group_values:last input:first').focus();
}

function bm_remove_per_group_option(a) {
	if (confirm(bm_normal_object.are_you_sure)) {
		var index = 0;
		var id = jQuery(a).attr('id');
		var currentIndex = Number(id.split("_")[2]);
		var nextIndex = Number(currentIndex) + 1;
		var desiredValue = jQuery('#group_from_' + currentIndex).val();
		jQuery('#group_from_' + nextIndex).val(desiredValue);
		jQuery('#group_from_' + nextIndex).attr('value', desiredValue);
		jQuery('#group_from_' + nextIndex).attr('min', desiredValue);

		jQuery(a).parent('td.bm_per_group_values').remove();

		jQuery('.bm_per_group_values').each(function (id, item) {
			jQuery(item).attr('id', 'option_' + id);
		});

		jQuery('[id^=group_from]').each(function (id, item) {
			jQuery(item).attr('id', 'group_from_' + id);
			jQuery(item).attr('name', 'module_values[group_from][' + id + ']');
		});

		jQuery('[id^=group_to_]').each(function (id, item) {
			jQuery(item).attr('id', 'group_to_' + id);
			jQuery(item).attr('name', 'module_values[group_to][' + id + ']');
		});

		jQuery('[id^=group_remove_]').each(function (id, item) {
			var counter = id + 1;
			jQuery(item).attr('id', 'group_remove_' + counter);
		});

		jQuery('[id^=group_price_]').each(function (id, item) {
			jQuery(item).attr('id', 'group_price_' + id);
			jQuery(item).attr('name', 'module_values[group_price][' + id + ']');
		});

		jQuery('input[name^="module_values[group_disable]"]').each(function (id, item) {
			var input_id = jQuery(item).attr('id') ? jQuery(item).attr('id') : '';
			var type = jQuery(item).attr('type');

			if ((type == 'hidden') && (input_id == '')) {
				jQuery(item).attr('name', 'module_values[group_disable][' + index + ']');
				jQuery(item).next("input[type=checkbox]").attr('name', 'module_values[group_disable][' + index + ']');
				jQuery(item).next("input[type=checkbox]").attr('id', 'group_disable_' + index);
				index++;
			}
		});
	}
}

function checkGroupFromValue(a) {
	var totalElemets = jQuery('#per_group_fields td.bm_per_group_values').length;
	var id = jQuery(a).attr('id');
	var currentIndex = Number(id.split("_")[2]);
	var nextindex = Number(currentIndex) + 1;
	var desiredValue = Number(jQuery('#group_to_' + currentIndex).val()) + 1;
	var nextValue = Number(jQuery('#group_to_' + nextindex).val());

	if (jQuery('#group_from_' + nextindex).length !== 0) {
		jQuery('#group_from_' + nextindex).val(desiredValue);
		jQuery('#group_from_' + nextindex).attr('min', desiredValue);
		jQuery('#group_to_' + nextindex).attr('min', desiredValue);

		if (desiredValue > nextValue) {
			var newNextValue = Number(desiredValue) + 1;
			jQuery('#group_to_' + nextindex).val(desiredValue);

			for (var i = nextindex + 1; i < totalElemets; i++) {
				jQuery('#group_from_' + i).val(newNextValue);
				jQuery('#group_to_' + i).val(newNextValue);
				jQuery('#group_from_' + i).attr('min', newNextValue);
				jQuery('#group_to_' + i).attr('min', newNextValue);
				newNextValue++;
			}
		}
	}
}

function checkAgeGroupFromValue(a) {
	var totalElemets = jQuery('#per_age_group_fields td.bm_per_age_group_values').length;
	var id = jQuery(a).attr('id');
	var currentIndex = Number(id.split("_")[3]);
	var nextindex = Number(currentIndex) + 1;
	var desiredValue = Number(jQuery('#age_group_to_' + currentIndex).val()) + 1;
	var nextValue = Number(jQuery('#age_group_to_' + nextindex).val());

	if (jQuery('#age_group_from_' + nextindex).length !== 0) {
		jQuery('#age_group_from_' + nextindex).val(desiredValue);
		jQuery('#age_group_from_' + nextindex).attr('min', desiredValue);
		jQuery('#age_group_to_' + nextindex).attr('min', desiredValue);

		if (desiredValue > nextValue) {
			var newNextValue = Number(desiredValue) + 1;
			jQuery('#age_group_to_' + nextindex).val(desiredValue);

			for (var i = nextindex + 1; i < totalElemets; i++) {
				jQuery('#age_group_from_' + i).val(newNextValue);
				jQuery('#age_group_to_' + i).val(newNextValue);
				jQuery('#age_group_from_' + i).attr('min', newNextValue);
				jQuery('#age_group_to_' + i).attr('min', newNextValue);
				newNextValue++;
			}
		}
	}
}

function disablePerGroup($this) {
	var index = Number($this.id.split("_")[2]);
	var slot_from = jQuery('#group_from_' + index + '');
	var slot_to = jQuery('#group_to_' + index + '');
	var slot_price = jQuery('#group_price_' + index + '');

	if (slot_from.prop('readonly')) {
		slot_from.prop('readonly', false);
		slot_from.parent().addClass('bminput bm_required');
	} else {
		slot_from.prop('readonly', true);
		slot_from.parent().removeClass('bminput bm_required');
		slot_from.parent().find('.errortext').html('');
		slot_from.parent().find('.errortext').hide();
	}

	if (slot_to.prop('readonly')) {
		slot_to.prop('readonly', false);
		slot_to.parent().addClass('bminput bm_required');
	} else {
		slot_to.prop('readonly', true);
		slot_to.parent().removeClass('bminput bm_required');
		slot_to.parent().children('span.errortext').html('');
		slot_to.parent().children('span.errortext').hide();
	}

	if (slot_price.prop('readonly')) {
		slot_price.prop('readonly', false);
		slot_price.parent().addClass('bminput bm_required');
	} else {
		slot_price.prop('readonly', true);
		slot_price.parent().removeClass('bminput bm_required');
		slot_price.parent().find('.errortext').html('');
		slot_price.parent().find('.errortext').hide();
	}
}

function disablePerAgeGroup($this) {
	var index = Number($this.id.split("_")[3]);
	var slot_from = jQuery('#age_group_from_' + index + '');
	var slot_to = jQuery('#age_group_to_' + index + '');
	var slot_price = jQuery('#age_group_price_' + index + '');

	if (slot_from.prop('readonly')) {
		slot_from.prop('readonly', false);
		slot_from.parent().addClass('bminput bm_required');
	} else {
		slot_from.prop('readonly', true);
		slot_from.parent().removeClass('bminput bm_required');
		slot_from.parent().find('.errortext').html('');
		slot_from.parent().find('.errortext').hide();
	}

	if (slot_to.prop('readonly')) {
		slot_to.prop('readonly', false);
		slot_to.parent().addClass('bminput bm_required');
	} else {
		slot_to.prop('readonly', true);
		slot_to.parent().removeClass('bminput bm_required');
		slot_to.parent().find('.errortext').html('');
		slot_to.parent().find('.errortext').hide();
	}

	if (slot_price.prop('readonly')) {
		slot_price.prop('readonly', false);
		slot_price.parent().addClass('bminput bm_required');
	} else {
		slot_price.prop('readonly', true);
		slot_price.parent().removeClass('bminput bm_required');
		slot_price.parent().find('.errortext').html('');
		slot_price.parent().find('.errortext').hide();
	}
}