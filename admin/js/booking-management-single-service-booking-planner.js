jQuery(document).ready(function ($) {
    const config = window.bmCalendarConfig || {};
    let calendar, dateRangePicker;

    // Initialize DateRangePicker
    function initializeDateRangePicker() {
        const picker = $('#dateRangePicker').daterangepicker({
            opens: "left",
            startDate: moment(config.singlePlannerinitialStart),
            endDate: moment(config.singlePlannerinitialEnd),
            maxSpan: { days: 6 },
            minSpan: { days: 6 },
            locale: { 
            format: 'DD-MMM',
                monthNames: [
                    'January', 'February', 'March', 'April', 'May', 'June',
                    'July', 'August', 'September', 'October', 'November', 'December'
                ]
            },
            alwaysShowCalendars: true,
            showCustomRangeLabel: false
        }, function (start, end) {
            $('#dateRangeText').text(start.format('DD-MMM') + ' - ' + end.format('DD-MMM'));
        });

        $('#timeslot_dateRangeText').text(
            moment(config.singlePlannerinitialStart).format('DD-MMM') + ' - ' +
            moment(config.singlePlannerinitialEnd).format('DD-MMM')
        );

        return picker.data('daterangepicker');
    }

    // Initialize FullCalendar
    function initializeCalendar() {
        const calendarEl = document.getElementById('calendar');

        if (!calendarEl) {
            return false;
        }

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'customWeek',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,customWeek,dayGridDay'
            },
            views: {
                customWeek: {
                    type: 'dayGridWeek',
                    duration: { days: 7 },
                    buttonText: '7 Days'
                }
            },
            dayHeaderContent: function(arg) {
                const date = arg.date;
                const weekday = date.toLocaleDateString('en-US', { weekday: 'short' });
                const day = String(date.getDate()).padStart(2, '0');
                return `${weekday} ${day}`;
            },
            initialDate: moment(config.singlePlannerinitialStart).toDate(),
            events: config.singlePlanneEvents || [],
            eventOrder: 'service_position',
            eventContent: renderEventContent,
            datesSet: handleDatesSet,
            eventDidMount: function (arg) {
                if (arg.event.extendedProps.isPastDate) {
                    arg.el.classList.add('fc-event-past');
                    arg.el.style.pointerEvents = 'none';
                    arg.el.style.opacity = '0.6';
                }
            }
        });

        calendar.render();
        return calendar;
    }

    // Event rendering
    function renderEventContent(arg) {
        const title = arg.event.title;
        const calendar_title = arg.event.extendedProps.calendar_title;
        const price = arg.event.extendedProps.price;
        const fullDesc = arg.event.extendedProps.full_desc || '';
        const isPastDate = arg.event.extendedProps.isPastDate;
        const eventClasses = isPastDate ? 'fc-event-past' : '';
        const bookClasses = isPastDate ? 'fc-event-past' : 'get_slot_details fc-flexi-event';
        const id = isPastDate ? 'past-event' : arg.event.id;
        const start = isPastDate ? 'past-event' : moment(arg.event.start).format('YYYY-MM-DD');

        const infoIconHtml = fullDesc.trim()
            ? `<image src="${bm_normal_object.svc_info_svg_icon}" class="fa fa-info-circle service-desc-fa" title="${bm_normal_object.more_info}"/>`
            : '';

        let service_title = calendar_title || title;

        return {
            html: `
                <div class="fc-event-content ${eventClasses}">
                    <div class="fc-title-wrapper main-parent">
                        <div class="fc-title" title="${calendar_title || title}">${service_title}</div>
                        <div class="service_full_description">${fullDesc}</div>
                        ${infoIconHtml}
                    </div>
                    <div class="slot-price-box">
                        <div class="fc-details">
                            <span>${price}</span>
                        </div>
                        <button class="fc-show-slots-btn ${bookClasses}" id="${id}" single-planner-date="${start}">
                            ${bm_normal_object.booking}
                        </button>
                    </div>
                </div>
            `
        };
    }

    // Date range synchronization
    function handleDatesSet(info) {
        const start = moment(info.start);
        const end = moment(info.end).subtract(1, 'day');

        if (end.diff(start, 'days') !== 6) {
            end = start.clone().add(6, 'days');
            calendar.changeView('customWeek', {
                start: start.toDate(),
                end: end.toDate()
            });
            return;
        }

        if (dateRangePicker) {
            dateRangePicker.setStartDate(start);
            dateRangePicker.setEndDate(end);
        }

        $('#dateRangeText').text(start.format('DD-MMM') + ' - ' + end.format('DD-MMM'));
        updateCalendarEvents(start, end);
    }

    // Event filtering
    function updateCalendarEvents(start, end) {
        const services = $('#search_fullcalendar_by_service').val() || [];
        const categories = $('#search_fullcalendar_by_category').val() || [];
        const cat_ids = config.singlePannerCat_ids != '' ? config.singlePannerCat_ids.split(',').map(id => parseInt(id.trim())) : [];

        const formattedStart = moment(start).format('YYYY-MM-DD');
        const formattedEnd = moment(end).format('YYYY-MM-DD');

        const post = { start: formattedStart, end: formattedEnd, services, categories, cat_ids };

        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_single_service_planner_events',
                post,
                nonce: bm_ajax_object.nonce,
            },
            beforeSend: () => $('.calendar-container').find('.loader_modal').show(),
            success: (response) => {
                if (response.success) {
                    calendar.removeAllEvents();
                    calendar.addEventSource(response.data.events);
                } else {
                    alert(bm_error_object.server_error);
                }
            },
            error: (xhr, status, error) => {
                alert(bm_error_object.server_error);
            },
            complete: () => $('.calendar-container').find('.loader_modal').hide()
        });
    }

    // Initialization sequence
    function init() {
        if ($('#search_fullcalendar_by_service').length) {
            intitializeMultiselect('search_fullcalendar_by_service');
        }
        if ($('#search_fullcalendar_by_category').length) {
            intitializeMultiselect('search_fullcalendar_by_category');
        }

        dateRangePicker = initializeDateRangePicker();
        calendar = initializeCalendar();

        $('#dateRangePicker').on('apply.daterangepicker', (ev, picker) => {
            calendar.gotoDate(picker.startDate.toDate());
            updateCalendarEvents(picker.startDate, picker.endDate);
        });

        $('#search_fullcalendar_by_service, #search_fullcalendar_by_category').on('change', () => {
            const view = calendar.view;
            updateCalendarEvents(moment(view.activeStart), moment(view.activeEnd));
        });
    }
    init();
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

jQuery(document).on('click', '#planner_slot_value', function(e) {
    e.preventDefault();

    var frontend_button_background_colur = bm_normal_object.svc_button_colour;
    var frontend_button_text_colur = bm_normal_object.svc_btn_txt_colour;

    jQuery(this).parent().children()
		.removeClass('bgcolor bordercolor textwhite')
		.css('cssText', 'background-color: initial !important; color: initial !important;');
	jQuery(this)
		.addClass('bgcolor bordercolor textwhite')
		.css('cssText', `background-color: ${frontend_button_background_colur} !important; color: ${frontend_button_text_colur} !important;`);

	jQuery('.slottablecontainer').html('');
	jQuery('.loader_modal').show();

    const $clickedSlot = jQuery(this);
    const time_slot_value = $clickedSlot.find('.slot_value_text').text().trim();
    const service_id = parseInt($clickedSlot.attr('data-service-id')) || 0;
    const date = $clickedSlot.attr('data-timeslot-date') || '';

    const post = {
        service_id: service_id,
        date: date,
        time_slot_value: time_slot_value,
    };

    jQuery.ajax({
        url: bm_ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'bm_fetch_service_planner_dialog_content',
            post,
            nonce: bm_ajax_object.nonce
        },
        success: function(response) {
            jQuery('.loader_modal').hide();
            if (response.success && response.data.html) {
                jQuery('.slottablecontainer').html(response.data.html);
            } else {
                jQuery('.slottablecontainer').html(bm_error_object.server_error);
            }
        },
        error: function(xhr, status, error) {
            jQuery('.loader_modal').hide();
            jQuery('.slottablecontainer').html(bm_error_object.server_error);
        }
    });
});


jQuery(document).on('click', '.get_slot_details', function (e) {
	e.preventDefault();
	jQuery('#single_planner_slot_details').html('');
    jQuery('.slottablecontainer').html('');
	jQuery('#time_slot_modal').addClass('active-slot');
    jQuery('#time_slot_modal').show();
	jQuery('.loader_modal').show();

	var post = {
		'date': jQuery(this).attr('single-planner-date'),
		'id': jQuery(this).attr('id'),
	}

	var data = { 'action': 'bm_fetch_service_planner_time_slots', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;
		var data = jsondata.data;

		if (status == false) {
			jQuery('#single_planner_slot_details').html(bm_error_object.server_error);
		} else if (data != null && data != '' && status == true) {
			jQuery('#single_planner_slot_details').html(data);
		} else {
			jQuery('#single_planner_slot_details').html(bm_error_object.server_error);
		}
	});
});