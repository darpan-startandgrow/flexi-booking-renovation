jQuery(document).ready(function ($) {
    const config = window.bmTimeslotCalendarConfig || {};
    let calendar, dateRangePicker;

    // Initialize DateRangePicker
    function initializeTimeslotDateRangePicker() {
        const picker = $('#timeslot_dateRangePicker').daterangepicker({
            opens: "left",
            startDate: moment(config.timeslotInitialStart),
            endDate: moment(config.timeslotInitialEnd),
            minDate: moment().startOf('day'),
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
            moment(config.timeslotInitialStart).format('DD-MMM') + ' - ' +
            moment(config.timeslotInitialEnd).format('DD-MMM')
        );

        return picker.data('daterangepicker');
    }

    // Initialize FullCalendar
    function initializeTimeslotCalendar() {
        const calendarEl = document.getElementById('timeslot_calendar');
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
            initialDate: moment(config.timeslotInitialStart).toDate(),
            events: config.timeslotevents || [],
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

            timeslotHTML += `
                <div class="timeselectbox" id="timeslot_fullcalendar_slot_value" data-service-id=${servicId} data-timeslot-date="${start}">
                    <span class="slot_value_text">${timeDisplay}</span>
                    <span class="slot_count_text" data-capacity="${capacity}" data-mincap="${minCapacity}">${capacity}</span>
                </div>
            `;
        }

        if (totalSlots > showLimit) {
            timeslotHTML += `<div class="show_more ${bookClasses}" id="${id}" data-timeslot-fullcalendar-id="${start}">${bm_normal_object.show_more}</div>`;
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
        const cat_ids = config.cat_ids != '' ? config.cat_ids.split(',').map(id => parseInt(id.trim())) : [];

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
                action: 'bm_filter_timeslot_fullcalendar_events',
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

        dateRangePicker = initializeTimeslotDateRangePicker();
        calendar = initializeTimeslotCalendar();

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

jQuery(document).on('click', '#timeslot_fullcalendar_slot_value', function () {
    var $clickedSlot = jQuery(this);
    var frontend_button_background_colur = bm_normal_object.svc_button_colour;
    var frontend_button_text_colur = bm_normal_object.svc_btn_txt_colour;

    var time_slot_value = $clickedSlot.find('.slot_value_text').text();
    jQuery('#selected_slot').val(time_slot_value);

    const capacity_left = $clickedSlot.find('.slot_count_text').data('capacity');
    const mincap = $clickedSlot.find('.slot_count_text').data('mincap');
    const service_id = $clickedSlot.attr('data-service-id') ? $clickedSlot.attr('data-service-id') : 0;
    const date = $clickedSlot.attr('data-timeslot-date');

    const dialogHTML = `
        <div id="timeslot-capacity-dialog" title="${bm_normal_object.confirm_selection}">
            <div class="loader_modal" style="position:relative !important;">
                <div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>${bm_normal_object.loading}</p></div>
            </div>
            <div class="timeslot-dialog-content"></div>
        </div>
    `;

    if (!jQuery('#timeslot-capacity-dialog').length) {
        jQuery('body').append(dialogHTML);
    }

    jQuery('#timeslot-capacity-dialog .timeslot-dialog-content').empty();
    jQuery('#timeslot-capacity-dialog .loader_modal').show();

    jQuery('#timeslot-capacity-dialog').dialog({
        modal: true,
        width: 350
    });

    const post = { mincap, capacity_left, service_id, date, time_slot_value };

    jQuery.ajax({
        url: bm_ajax_object.ajax_url,
        type: 'POST',
        data: {
            action: 'bm_fetch_timeslot_dialog_content',
            post,
            nonce: bm_ajax_object.nonce
        },
        beforeSend: function () {
            jQuery('#timeslot-capacity-dialog .loader_modal').show();
        },
        success: function (response) {
            jQuery('#timeslot-capacity-dialog .loader_modal').hide();

            if (response.success) {
                const dialogContent = `
                    ${response.data.html}
                `;

                jQuery('#timeslot-capacity-dialog .timeslot-dialog-content').html(dialogContent);

                const $proceedBtn = jQuery('.timeslot-proceed-btn');
                if (response.data.has_error) {
                    $proceedBtn
                        .addClass('readonly_div')
                        .removeClass('bgcolor textwhite')
                        .css('cssText', 'background-color: initial !important; color: initial !important;')
                        .prop('disabled', true);
                } else {
                    $proceedBtn
                        .removeClass('readonly_div')
                        .addClass('bgcolor textwhite')
                        .css('cssText', `background-color: ${frontend_button_background_colur} !important; color: ${frontend_button_text_colur} !important;`)
                        .prop('disabled', false);
                }

                initializeCounterControls(response.data.min, response.data.max, response.data.step);
            } else {
                jQuery('#timeslot-capacity-dialog .timeslot-dialog-content').html(response.data ? response.data : bm_error_object.server_error);
            }
        },
        error: function () {
            jQuery('#timeslot-capacity-dialog .loader_modal').hide();
            jQuery('#timeslot-capacity-dialog .timeslot-dialog-content').html(bm_error_object.server_error);
        },
        complete: () => jQuery('.imeslot-capacity-dialogr .loader_modal').hide()
    });
});

function initializeCounterControls(minValue, maxValue, stepValue) {
    // Counter minus button functionality
    jQuery('.timeslot-counter-btn.minus').off('click').on('click', function () {
        const input = jQuery('#timeslot-counter');
        let value = parseInt(input.val());

        if (value > minValue) {
            value = Math.max(minValue, value - stepValue);
            input.val(value);
        }

        // Validate the new value
        validateCounterValue(input, minValue, maxValue, stepValue);
    });

    // Counter plus button functionality
    jQuery('.timeslot-counter-btn.plus').off('click').on('click', function () {
        const input = jQuery('#timeslot-counter');
        let value = parseInt(input.val());

        if (value < maxValue) {
            value = Math.min(maxValue, value + stepValue);
            input.val(value);
        }

        // Validate the new value
        validateCounterValue(input, minValue, maxValue, stepValue);
    });

    // Input validation on typing
    jQuery('#timeslot-counter').off('input').on('input', function () {
        validateCounterValue(jQuery(this), minValue, maxValue, stepValue);
    });

    // Additional validation when leaving the field
    jQuery('#timeslot-counter').off('blur').on('blur', function () {
        const input = jQuery(this);
        let value = parseFloat(input.val());

        // If empty or NaN, set to min
        if (isNaN(value)) {
            input.val(minValue);
            return;
        }

        // Enforce step validation
        if ((value - minValue) % stepValue !== 0) {
            // Find closest valid value
            const steps = Math.round((value - minValue) / stepValue);
            const validValue = minValue + (steps * stepValue);

            // Clamp between min and max
            const clampedValue = Math.min(Math.max(validValue, minValue), maxValue);
            input.val(clampedValue);
        }
    });

    // Cancel button action
    jQuery('#timeslot-cancel-btn').off('click').on('click', function () {
        jQuery('#timeslot-capacity-dialog').dialog('close');
    });

    // Initial validation
    validateCounterValue(jQuery('#timeslot-counter'), minValue, maxValue, stepValue);
}

function validateCounterValue(input, minValue, maxValue, stepValue) {
    let value = parseFloat(input.val());

    // If not a number, reset to min
    if (isNaN(value)) {
        input.val(minValue);
        return false;
    }

    // If below min, set to min
    if (value < minValue) {
        input.val(minValue);
        return false;
    }

    // If above max, set to max
    if (value > maxValue) {
        input.val(maxValue);
        return false;
    }

    // If not a whole number when step is 1, or doesn't match step increment
    if ((stepValue === 1 && !Number.isInteger(value)) ||
        (stepValue > 1 && (value - minValue) % stepValue !== 0)) {
        // Round to nearest valid step
        const roundedValue = Math.round((value - minValue) / stepValue) * stepValue + minValue;
        input.val(roundedValue);
        return false;
    }

    return true;
}