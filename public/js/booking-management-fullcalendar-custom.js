jQuery(document).ready(function ($) {
    const config = window.bmCalendarConfig || {};
    let calendar, dateRangePicker;

    // Initialize DateRangePicker
    function initializeDateRangePicker() {
        const picker = $('#dateRangePicker').daterangepicker({
            opens: "left",
            startDate: moment(config.initialStart),
            endDate: moment(config.initialEnd),
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
            $('#dateRangeText').text(start.format('DD-MMM') + ' - ' + end.format('DD-MMM'));
        });

        $('#timeslot_dateRangeText').text(
            moment(config.initialStart).format('DD-MMM') + ' - ' +
            moment(config.initialEnd).format('DD-MMM')
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
            initialDate: moment(config.initialStart).toDate(),
            events: config.events || [],
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
                        <button class="fc-show-slots-btn ${bookClasses}" id="${id}" data-fullcalendar-id="${start}">
                            ${bm_normal_object.book}
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
        const cat_ids = config.cat_ids != '' ? config.cat_ids.split(',').map(id => parseInt(id.trim())) : [];

        const formattedStart = moment(start).format('YYYY-MM-DD');
        const formattedEnd = moment(end).format('YYYY-MM-DD');

        const post = { start: formattedStart, end: formattedEnd, services, categories, cat_ids };

        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_filter_fullcalendar_events',
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