jQuery(document).ready(function ($) {
    const config = window.bmServiceBookingPlannerConfig || {};
    let calendar, dateRangePicker;

    // Initialize DateRangePicker
    function initializePlannerDateRangePicker() {
        const picker = $('#timeslot_dateRangePicker').daterangepicker({
            opens: "left",
            startDate: moment(config.plannerTimeslotInitialStart),
            endDate: moment(config.plannerTimeslotInitialEnd),
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
            $('#timeslot_dateRangeText').text(start.format('DD-MMM') + ' - ' + end.format('DD-MMM'));
        });

        $('#timeslot_dateRangeText').text(
            moment(config.plannerTimeslotInitialStart).format('DD-MMM') + ' - ' +
            moment(config.plannerTimeslotInitialEnd).format('DD-MMM')
        );

        return picker.data('daterangepicker');
    }

    // Initialize FullCalendar
    function initializePlannerCalendar() {
        const calendarEl = document.getElementById('service_planner_calendar');
        if (!calendarEl) return false;

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
            initialDate: moment(config.plannerTimeslotInitialStart).toDate(),
            events: config.plannerevents || [],
            eventOrder: 'service_position',
            eventContent: renderTimeslotEventContent,
            datesSet: handleTimeslotDatesSet,
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
    function renderTimeslotEventContent(arg) {
        const timeslots = arg.event.extendedProps.timeslots;
        const isPastDate = arg.event.extendedProps.isPastDate;
        const eventClasses = isPastDate ? 'fc-event-past' : '';
        const bookClasses = isPastDate ? 'fc-event-past' : 'get_slot_details fc-flexi-event';
        const id = isPastDate ? 'past-event' : arg.event.id;
        const start = isPastDate ? 'past-event' : moment(arg.event.start).format('YYYY-MM-DD');
        const servicId = isPastDate ? 0 : arg.event.id;

        if (!timeslots || !timeslots.slots || Object.keys(timeslots.slots).length === 0) {
            return {
                html: `<div class="cl-slot-box noevents ${eventClasses}"></div>`
            };
        }

        let timeslotHTML = '';
        const slotKeys = Object.keys(timeslots.slots);
        const totalSlots = slotKeys.length;
        const showLimit = 3;
        let slotsToShow = totalSlots;

        if (totalSlots > showLimit) {
            slotsToShow = showLimit;
        }

        for (let i = 0; i < slotsToShow; i++) {
            const key = slotKeys[i];
            const timeDisplay = timeslots.slots[key];
            const capacity = timeslots.available_capacity[key] || '0';
            const minCapacity = timeslots.min_capacity[key] || '1';
            const maxCapacity = timeslots.max_capacity[key] || '1';

            timeslotHTML += `
                <div class="timeselectbox" id="service_planner_reservation_list" data-service-id=${servicId} data-timeslot-date="${start}">
                    <span class="slot_value_text">${timeDisplay}</span>
                    <span class="slot_count_text" data-capacity="${capacity}" data-mincap="${minCapacity}">${capacity}/${maxCapacity}</span>
                </div>
            `;
        }

        if (totalSlots > showLimit) {
            timeslotHTML += `<div class="show_more ${bookClasses}" id="${id}" data-service-planner-date="${start}">${bm_normal_object.show_more}</div>`;
        }

        return {
            html: `<div class="cl-slot-box ${eventClasses}">${timeslotHTML}</div>`
        };
    }

    // Date range synchronization
    function handleTimeslotDatesSet(info) {
        const start = moment(info.start);
        const end = moment(info.start).add(6, 'days');

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

        $('#timeslot_dateRangeText').text(start.format('DD-MMM') + ' - ' + end.format('DD-MMM'));
        updateTimeslotCalendarEvents(start, end);
    }

    // Event filtering
    function updateTimeslotCalendarEvents(start, end) {
        const services = $('#search_timeslot_fullcalendar_by_service').val() || [];
        const categories = $('#search_timeslot_fullcalendar_by_category').val() || [];
        const cat_ids = config.planner_cat_ids != '' ? config.planner_cat_ids.split(',').map(id => parseInt(id.trim())) : [];

        const formattedStart = moment(start).format('YYYY-MM-DD');
        const formattedEnd = moment(end).format('YYYY-MM-DD');

        const post = {
            start: formattedStart,
            end: formattedEnd,
            services,
            categories,
            cat_ids
        };

        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_filter_service_planner_events',
                post,
                nonce: bm_ajax_object.nonce,
            },
            beforeSend: () => $('.timeslot-calendar-container').find('.loader_modal').show(),
            success: (response) => {
                if (response.success) {
                    calendar.removeAllEvents();
                    calendar.addEventSource(response.data.events);
                    updateSidebarServices(response.data.services || []);
                } else {
                    alert(bm_error_object.server_error);
                }
            },
            error: (xhr, status, error) => {
                alert(bm_error_object.server_error);
            },
            complete: () => $('.timeslot-calendar-container').find('.loader_modal').hide()
        });
    }

    // Initialization sequence
    function init() {
        if ($('#search_timeslot_fullcalendar_by_service').length) {
            intitializeMultiselect('search_timeslot_fullcalendar_by_service');
        }
        if ($('#search_timeslot_fullcalendar_by_category').length) {
            intitializeMultiselect('search_timeslot_fullcalendar_by_category');
        }

        dateRangePicker = initializePlannerDateRangePicker();
        calendar = initializePlannerCalendar();

        $('#timeslot_dateRangePicker').on('apply.daterangepicker', (ev, picker) => {
            calendar.gotoDate(picker.startDate.toDate());
            updateTimeslotCalendarEvents(picker.startDate, picker.endDate);
        });

        $('#search_timeslot_fullcalendar_by_service, #search_timeslot_fullcalendar_by_category').on('change', () => {
            const view = calendar.view;
            updateTimeslotCalendarEvents(moment(view.activeStart), moment(view.activeEnd));
        });
    }

    // Update service sidebar
    function updateSidebarServices(services) {
        const $sidebar = $('.timeslot-calendar-container .calendarinnercontent .sidebar');
        $sidebar.html('');
        $sidebar.prepend('<h3>' + bm_normal_object.services_text + '</h3>');

        if (services.length > 0) {
            services.forEach(service => {
                let service_title = service.calendar_title || service.title;

                let durationHtml = '';
                if (service.extendedProps.show_duration > 0) {
                    durationHtml = `<div class="service-item"><i class="fa fa-clock-o"></i>${service.extendedProps.duration}</div>`;
                }

                const serviceHtml = `
                    <div class="servicebox">
                        <div class="service-item">
                            <h4>
                                <span class="service-title" title="${service_title}">${service_title}</span>
                            </h4>
                        </div>
                        ${durationHtml}
                        <div class="service-item"><i class="fa fa-list-alt"></i>${service.extendedProps.categoryName || ''}</div>
                        <div class="service-item"><i class="fa fa-eur"></i>${service.extendedProps.price || '0.00'}</div>
                    </div>
                `;
                $sidebar.append(serviceHtml);
            });
        } else {
            $sidebar.append('<p class="no_records_class">' + bm_error_object.no_services_text + '</p>');
        }
    }

    init();

    $('#arrowLeft').on('click', function () {
        $('.sidebar').addClass('collapsed');
        $('#arrowLeft').hide();
        $('#arrowRight').show();
    });

    $('#arrowRight').on('click', function () {
        $('.sidebar').removeClass('collapsed');
        $('#arrowRight').hide();
        $('#arrowLeft').show();
    });
});


jQuery(document).on('click', '#service_planner_reservation_list', function(e) {
    e.preventDefault();
	jQuery('#planner_reservation_details').html('');
	jQuery('#service_planner_modal').addClass('active-slot');
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
                jQuery('#planner_reservation_details').html(response.data.html);
            } else {
                jQuery('#planner_reservation_details').html(bm_error_object.server_error);
            }
        },
        error: function(xhr, status, error) {
            jQuery('.loader_modal').hide();
            jQuery('#planner_reservation_details').html(bm_error_object.server_error);
        }
    });
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
	jQuery('#planner_slot_details').html('');
    jQuery('.slottablecontainer').html('');
	jQuery('#time_slot_modal').addClass('active-slot');
    jQuery('#time_slot_modal').show();
	jQuery('.loader_modal').show();

	var post = {
		'date': jQuery(this).attr('data-service-planner-date'),
		'id': jQuery(this).attr('id'),
	}

	var data = { 'action': 'bm_fetch_service_planner_time_slots', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		jQuery('.loader_modal').hide();
		var jsondata = jQuery.parseJSON(response);
		var status = jsondata.status;
		var data = jsondata.data;

		if (status == false) {
			jQuery('#planner_slot_details').html(bm_error_object.server_error);
		} else if (data != null && data != '' && status == true) {
			jQuery('#planner_slot_details').html(data);
		} else {
			jQuery('#planner_slot_details').html(bm_error_object.server_error);
		}
	});
});

// Encode a string
function strict_encode(string = '') {
    return btoa(encodeURIComponent(string));
}

// Decode a string
function strict_decode(string = '') {
    return decodeURIComponent(atob(string));
}

jQuery(document).on('click', '#service_planner_modal .close', function () {
	jQuery(document).find('#service_planner_modal').removeClass('active-slot');
});

jQuery(document).on('click', '#time_slot_modal .close', function () {
	jQuery(document).find('#time_slot_modal').removeClass('active-slot');
});