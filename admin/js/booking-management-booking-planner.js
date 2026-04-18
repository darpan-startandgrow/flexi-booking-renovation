/**
 * Booking Planner – Modern SPA-like Engine
 *
 * Architecture:
 *   State ─► render() ─► DOM
 *   Events ─► setState() ─► batched render via requestAnimationFrame
 *   API calls use optimistic updates with automatic rollback on failure
 *
 * @since 1.0.0
 */
(function ($) {
    'use strict';

    /* ------------------------------------------------------------------ */
    /*  BOOTSTRAP                                                          */
    /* ------------------------------------------------------------------ */

    var $root = $('#bm-planner-root');
    if (!$root.length) { return; }

    var NONCE    = $root.data('nonce') || '';
    var REST_URL = ($root.data('rest-url') || '').replace(/\/+$/, '');

    /** Pixels per hour in service planner. */
    var HOUR_PX = 120;

    /* ------------------------------------------------------------------ */
    /*  HELPERS                                                            */
    /* ------------------------------------------------------------------ */

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function formatDateISO(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function formatDate(d) {
        var days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return days[d.getDay()] + ', ' + months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function addDays(d, n) {
        var r = new Date(d);
        r.setDate(r.getDate() + n);
        return r;
    }

    function timeToMinutes(t) {
        if (!t) { return 0; }
        var p = t.split(':');
        return parseInt(p[0], 10) * 60 + parseInt(p[1] || '0', 10);
    }

    function minutesToTime(m) {
        return pad(Math.floor(m / 60)) + ':' + pad(m % 60);
    }

    function sanitizeHtml(str) {
        if (str == null) { return ''; }
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    /* ------------------------------------------------------------------ */
    /*  STATE                                                              */
    /* ------------------------------------------------------------------ */

    var State = {
        currentDate: new Date(),
        activeView:  'service',
        services:    [],
        categories:  [],
        bookings:    [],
        filters:     { services: [], categories: [] },
        ui:          { loading: false, modalOpen: false },
        timeRange:   { start: 8, end: 22 }
    };

    var _renderScheduled = false;

    function setState(partial) {
        for (var key in partial) {
            if (!partial.hasOwnProperty(key)) { continue; }
            if (key === 'filters' || key === 'ui') {
                State[key] = $.extend({}, State[key], partial[key]);
            } else {
                State[key] = partial[key];
            }
        }
        if (!_renderScheduled) {
            _renderScheduled = true;
            requestAnimationFrame(function () {
                _renderScheduled = false;
                render();
            });
        }
    }

    /* ------------------------------------------------------------------ */
    /*  API LAYER                                                          */
    /* ------------------------------------------------------------------ */

    var API = {
        _ajax: function (method, path, data) {
            var opts = {
                url:    REST_URL + path,
                method: method,
                beforeSend: function (xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', NONCE);
                },
                dataType: 'json'
            };
            if (data && (method === 'POST' || method === 'PUT')) {
                opts.contentType = 'application/json';
                opts.data = JSON.stringify(data);
            } else if (data) {
                opts.data = data;
            }
            return $.ajax(opts);
        },

        fetchServices: function () {
            return API._ajax('GET', '/services');
        },
        fetchCategories: function () {
            return API._ajax('GET', '/categories');
        },
        fetchBookings: function (startDate, endDate) {
            return API._ajax('GET', '/bookings', {
                start_date: formatDateISO(startDate),
                end_date:   formatDateISO(endDate)
            });
        },
        fetchTimeslots: function (serviceId, date) {
            return API._ajax('GET', '/timeslots', {
                service_id: serviceId,
                date:       formatDateISO(date)
            });
        },
        fetchReservations: function (serviceId, date, timeSlot) {
            return API._ajax('GET', '/reservations', {
                service_id: serviceId,
                date:       formatDateISO(date),
                time_slot:  timeSlot
            });
        },
        createBooking: function (data) {
            return API._ajax('POST', '/bookings', data);
        },
        updateBooking: function (id, data) {
            return API._ajax('PUT', '/bookings/' + id, data);
        },
        deleteBooking: function (id) {
            return API._ajax('DELETE', '/bookings/' + id);
        },
        checkAvailability: function (serviceId, date) {
            return API._ajax('GET', '/availability', {
                service_id: serviceId,
                date:       formatDateISO(date)
            });
        }
    };

    /* ------------------------------------------------------------------ */
    /*  TOAST NOTIFICATIONS                                                */
    /* ------------------------------------------------------------------ */

    var Toast = {
        _container: null,

        _ensureContainer: function () {
            if (!this._container || !document.body.contains(this._container[0])) {
                this._container = $('<div class="bmp-toast-container"></div>');
                $('body').append(this._container);
            }
            return this._container;
        },

        show: function (message, type) {
            type = type || 'info';
            var $c = this._ensureContainer();
            var $t = $('<div class="bmp-toast ' + sanitizeHtml(type) + '">' + sanitizeHtml(message) + '</div>');
            $t.css({ opacity: 0, transform: 'translateX(100%)' });
            $c.append($t);

            requestAnimationFrame(function () {
                $t.css({ opacity: 1, transform: 'translateX(0)', transition: 'all .3s ease' });
            });

            var dismiss = function () {
                $t.css({ opacity: 0, transform: 'translateX(100%)' });
                setTimeout(function () { $t.remove(); }, 350);
            };
            $t.on('click', dismiss);
            setTimeout(dismiss, 4000);
        }
    };

    /* ------------------------------------------------------------------ */
    /*  MODAL SYSTEM                                                       */
    /* ------------------------------------------------------------------ */

    var $currentModal = null;

    function openModal(contentHtml, title) {
        closeModal();
        var $overlay = $('<div class="bmp-modal-overlay"></div>');
        var $modal = $(
            '<div class="bmp-modal">' +
                '<div class="bmp-modal-header">' +
                    '<h2 class="bmp-modal-title">' + sanitizeHtml(title || '') + '</h2>' +
                    '<button class="bmp-modal-close" type="button" aria-label="Close">&times;</button>' +
                '</div>' +
                '<div class="bmp-modal-body">' + contentHtml + '</div>' +
            '</div>'
        );
        $overlay.append($modal);
        $root.append($overlay);
        $currentModal = $overlay;

        requestAnimationFrame(function () {
            $overlay.addClass('visible');
        });

        $overlay.on('click', function (e) {
            if ($(e.target).is($overlay)) { closeModal(); }
        });
    }

    function closeModal() {
        if (!$currentModal || !$currentModal.length) { return; }
        $currentModal.removeClass('visible');
        var $m = $currentModal;
        $currentModal = null;
        setTimeout(function () { $m.remove(); }, 280);
    }

    /* ------------------------------------------------------------------ */
    /*  SERVICE COLOUR MAP                                                 */
    /* ------------------------------------------------------------------ */

    var _svcColorMap = {};
    var _colorIdx    = 0;
    var NUM_COLORS   = 8;

    function getServiceColor(serviceId) {
        var id = parseInt(serviceId, 10);
        if (_svcColorMap[id] === undefined) {
            _svcColorMap[id] = _colorIdx % NUM_COLORS;
            _colorIdx++;
        }
        return 'svc-color-' + _svcColorMap[id];
    }

    /* ------------------------------------------------------------------ */
    /*  BOOKING HELPERS                                                    */
    /* ------------------------------------------------------------------ */

    function findBookingById(id) {
        var nid = parseInt(id, 10);
        for (var i = 0; i < State.bookings.length; i++) {
            if (parseInt(State.bookings[i].id, 10) === nid) { return State.bookings[i]; }
        }
        return null;
    }

    function getBookingsForService(serviceId) {
        var sid = parseInt(serviceId, 10);
        var iso = formatDateISO(State.currentDate);
        return State.bookings.filter(function (b) {
            return parseInt(b.service_id, 10) === sid && b.booking_date === iso;
        });
    }

    function getBookingsForHour(hour) {
        var iso = formatDateISO(State.currentDate);
        return State.bookings.filter(function (b) {
            if (b.booking_date !== iso) { return false; }
            var slots = b.booking_slots || {};
            var fromMin = timeToMinutes(slots.from || '');
            return Math.floor(fromMin / 60) === hour;
        });
    }

    /* ------------------------------------------------------------------ */
    /*  BOOKING BLOCK RENDERER (Service Planner)                          */
    /* ------------------------------------------------------------------ */

    function renderBookingBlock(booking, startH, totalHours, gridWidth) {
        var slots   = booking.booking_slots || {};
        var fromMin = timeToMinutes(slots.from || '');
        var toMin   = timeToMinutes(slots.to   || '');
        if (fromMin >= toMin) { toMin = fromMin + 60; }

        var startMin  = startH * 60;
        var totalMin  = totalHours * 60;
        var left      = ((fromMin - startMin) / totalMin) * 100;
        var width     = ((toMin - fromMin) / totalMin) * 100;

        // Clamp to grid boundaries
        if (left < 0)            { width += left; left = 0; }
        if (left + width > 100)  { width = 100 - left; }
        if (width <= 0)          { return ''; }

        var colorClass    = getServiceColor(booking.service_id);
        var statusAttr    = booking.order_status ? ' data-status="' + sanitizeHtml(booking.order_status) + '"' : '';
        var customerName  = booking.customer_name || '';
        var displayName   = booking.service_display_name || booking.service_name || '';
        var timeStr       = (slots.from || '') + ' – ' + (slots.to || '');

        return '<div class="bmp-booking ' + colorClass + '"' + statusAttr +
            ' data-booking-id="' + parseInt(booking.id, 10) + '"' +
            ' style="left:' + left.toFixed(3) + '%;width:' + width.toFixed(3) + '%;position:absolute;top:4px;bottom:4px;"' +
            ' title="' + sanitizeHtml(displayName) + ' | ' + sanitizeHtml(customerName) + ' | ' + sanitizeHtml(timeStr) + '">' +
            '<div class="bmp-booking-title">' + sanitizeHtml(displayName) + '</div>' +
            '<div class="bmp-booking-time">'  + sanitizeHtml(timeStr) + '</div>' +
            (customerName ? '<div class="bmp-booking-customer">' + sanitizeHtml(customerName) + '</div>' : '') +
            '<div class="bmp-resize-handle ui-resizable-e"></div>' +
            '</div>';
    }

    /* ------------------------------------------------------------------ */
    /*  TIME BOOKING CARD RENDERER (Time Planner)                         */
    /* ------------------------------------------------------------------ */

    function renderTimeBookingCard(booking) {
        var slots  = booking.booking_slots || {};
        var status = booking.order_status || 'pending';
        var colorClass = getServiceColor(booking.service_id);

        return '<div class="bmp-time-booking-card ' + colorClass + '"' +
            ' data-booking-id="' + parseInt(booking.id, 10) + '"' +
            ' data-status="' + sanitizeHtml(status) + '">' +
            '<div class="bmp-time-card-service">' + sanitizeHtml(booking.service_display_name || booking.service_name || '') + '</div>' +
            '<div class="bmp-time-card-customer">' + sanitizeHtml(booking.customer_name || '') + '</div>' +
            '<div class="bmp-time-card-meta">' +
                '<span>' + sanitizeHtml(slots.from || '') + '–' + sanitizeHtml(slots.to || '') + '</span>' +
                '<span class="bmp-time-card-status status-' + sanitizeHtml(status) + '">' + sanitizeHtml(status) + '</span>' +
            '</div>' +
            '</div>';
    }

    /* ------------------------------------------------------------------ */
    /*  CURRENT TIME LINE                                                  */
    /* ------------------------------------------------------------------ */

    function renderCurrentTimeLine(startH, totalHours, gridWidth) {
        var now = new Date();
        var iso = formatDateISO(State.currentDate);
        if (formatDateISO(now) !== iso) { return ''; }

        var currentMin = now.getHours() * 60 + now.getMinutes();
        var startMin   = startH * 60;
        var totalMin   = totalHours * 60;
        if (currentMin < startMin || currentMin > startMin + totalMin) { return ''; }

        var left = ((currentMin - startMin) / totalMin) * 100;
        return '<div class="bmp-current-time-line" style="left:' + left.toFixed(2) + '%;position:absolute;top:0;bottom:0;"></div>';
    }

    /* ------------------------------------------------------------------ */
    /*  RENDER: TOOLBAR                                                    */
    /* ------------------------------------------------------------------ */

    function renderToolbar() {
        var svcActive  = State.activeView === 'service' ? ' active' : '';
        var timeActive = State.activeView === 'time'    ? ' active' : '';

        return '<div class="bmp-toolbar">' +
            '<div class="bmp-toolbar-left">' +
                '<div class="bmp-view-switcher">' +
                    '<button class="bmp-view-btn' + svcActive + '" data-view="service">Service Planner</button>' +
                    '<button class="bmp-view-btn' + timeActive + '" data-view="time">Time Planner</button>' +
                '</div>' +
            '</div>' +
            '<div class="bmp-toolbar-center">' +
                '<div class="bmp-date-nav">' +
                    '<button class="bmp-date-nav-btn" data-dir="-1" title="Previous day">&#9664;</button>' +
                    '<button class="bmp-date-display" id="bmp-date-display-btn">' + sanitizeHtml(formatDate(State.currentDate)) + '</button>' +
                    '<button class="bmp-date-nav-btn" data-dir="1" title="Next day">&#9654;</button>' +
                    '<button class="bmp-today-btn">Today</button>' +
                '</div>' +
            '</div>' +
            '<div class="bmp-toolbar-right">' +
                buildFilterDropdown('service') +
                buildFilterDropdown('category') +
            '</div>' +
        '</div>';
    }

    function buildFilterDropdown(type) {
        var items, selected, key, label;
        if (type === 'service') {
            items = State.services; selected = State.filters.services; key = 'services'; label = 'Services';
        } else {
            items = State.categories; selected = State.filters.categories; key = 'categories'; label = 'Categories';
        }

        var panelHtml = '';
        if (!items.length) {
            panelHtml = '<p style="padding:8px 14px;color:#8c8f94;font-size:12px;">No items</p>';
        } else {
            var allChecked = selected.length === items.length ? ' checked' : '';
            panelHtml += '<label class="bmp-filter-select-all">' +
                '<input type="checkbox" data-filter-all="' + key + '"' + allChecked + '> Select All</label>';
            for (var i = 0; i < items.length; i++) {
                var item = items[i];
                var id   = parseInt(item.id, 10);
                var name = type === 'service' ? item.service_name : item.cat_name;
                var chk  = selected.indexOf(id) !== -1 ? ' checked' : '';
                panelHtml += '<label><input type="checkbox" data-filter-key="' + key +
                    '" value="' + id + '"' + chk + '> ' + sanitizeHtml(name) + '</label>';
            }
        }

        return '<div class="bmp-filter-dropdown" data-filter-type="' + type + '">' +
            '<button class="bmp-filter-btn">' + label + ' &#9662;</button>' +
            '<div class="bmp-filter-panel">' + panelHtml + '</div>' +
            '</div>';
    }

    /* ------------------------------------------------------------------ */
    /*  RENDER: SERVICE PLANNER                                            */
    /* ------------------------------------------------------------------ */

    function getFilteredServices() {
        var svcs = State.services;
        var sf   = State.filters.services;
        var cf   = State.filters.categories;
        return svcs.filter(function (s) {
            return sf.indexOf(parseInt(s.id, 10)) !== -1 &&
                   cf.indexOf(parseInt(s.service_category, 10)) !== -1;
        });
    }

    function renderServicePlanner() {
        var startH    = State.timeRange.start;
        var endH      = State.timeRange.end;
        var totalHours = endH - startH;
        var gridWidth = totalHours * HOUR_PX;
        var filtered  = getFilteredServices();

        /* -- Time labels -- */
        var timeLabels = '';
        for (var h = startH; h < endH; h++) {
            var now = new Date();
            var isCurrent = h === now.getHours() && formatDateISO(State.currentDate) === formatDateISO(now);
            timeLabels += '<div class="bmp-time-label' + (isCurrent ? ' current-hour' : '') +
                '" style="min-width:' + HOUR_PX + 'px;max-width:' + HOUR_PX + 'px;">' + pad(h) + ':00</div>';
        }

        /* -- Rows -- */
        var rows = '';
        for (var si = 0; si < filtered.length; si++) {
            var svc        = filtered[si];
            var svcBkgs    = getBookingsForService(svc.id);
            var bkgHtml    = '';
            for (var bi = 0; bi < svcBkgs.length; bi++) {
                bkgHtml += renderBookingBlock(svcBkgs[bi], startH, totalHours, gridWidth);
            }

            /* -- Hour cells -- */
            var cells = '';
            for (var ch = startH; ch < endH; ch++) {
                cells += '<div class="bmp-cell" data-service-id="' + parseInt(svc.id, 10) +
                    '" data-hour="' + ch + '" style="min-width:' + HOUR_PX + 'px;max-width:' + HOUR_PX + 'px;height:100%;"></div>';
            }

            rows += '<div class="bmp-resource-row" data-service-id="' + parseInt(svc.id, 10) + '">' +
                '<div class="bmp-resource-label">' +
                    '<span class="bmp-resource-name">' + sanitizeHtml(svc.service_name) + '</span>' +
                    '<span class="bmp-resource-meta">' + sanitizeHtml(svc.service_duration || '') + ' min</span>' +
                '</div>' +
                '<div class="bmp-timeline" style="width:' + gridWidth + 'px;min-width:' + gridWidth + 'px;position:relative;display:flex;">' +
                    cells +
                    bkgHtml +
                    renderCurrentTimeLine(startH, totalHours, gridWidth) +
                '</div>' +
            '</div>';
        }

        if (!filtered.length) {
            rows = '<div style="padding:40px;text-align:center;color:var(--bmp-text-muted);">No services match the current filters.</div>';
        }

        return '<div class="bmp-grid-container">' +
            '<div class="bmp-grid-header">' +
                '<div class="bmp-resource-header">Resource</div>' +
                '<div class="bmp-time-labels" style="width:' + gridWidth + 'px;min-width:' + gridWidth + 'px;">' + timeLabels + '</div>' +
            '</div>' +
            '<div class="bmp-grid-body">' + rows + '</div>' +
        '</div>' +
        renderSummaryBar();
    }

    /* ------------------------------------------------------------------ */
    /*  RENDER: TIME PLANNER                                               */
    /* ------------------------------------------------------------------ */

    function renderTimePlanner() {
        var startH    = State.timeRange.start;
        var endH      = State.timeRange.end;
        var iso       = formatDateISO(State.currentDate);

        var rows = '';
        for (var h = startH; h < endH; h++) {
            var halfBookings = getBookingsForHourOrHalf(h, false);
            var halfPlusBookings = getBookingsForHourOrHalf(h, true);

            var cardsHtml = '';
            for (var i = 0; i < halfBookings.length; i++) { cardsHtml += renderTimeBookingCard(halfBookings[i]); }
            var cardsHalfHtml = '';
            for (var j = 0; j < halfPlusBookings.length; j++) { cardsHalfHtml += renderTimeBookingCard(halfPlusBookings[j]); }

            rows += '<div class="bmp-time-row">' +
                '<div class="bmp-time-label-cell">' + pad(h) + ':00</div>' +
                '<div class="bmp-time-slot-row">' + cardsHtml + '</div>' +
            '</div>';

            if (halfPlusBookings.length) {
                rows += '<div class="bmp-time-row" style="border-top:1px dashed var(--bmp-border-light);">' +
                    '<div class="bmp-time-label-cell" style="color:var(--bmp-text-muted);font-weight:400;">' + pad(h) + ':30</div>' +
                    '<div class="bmp-time-slot-row">' + cardsHalfHtml + '</div>' +
                '</div>';
            }
        }

        if (!rows) {
            rows = '<div style="padding:40px;text-align:center;color:var(--bmp-text-muted);">No bookings for this date.</div>';
        }

        return '<div class="bmp-grid-container">' +
            '<div class="bmp-grid-body bmp-time-planner">' + rows + '</div>' +
        '</div>' +
        renderSummaryBar();
    }

    function getBookingsForHourOrHalf(hour, halfHour) {
        var iso = formatDateISO(State.currentDate);
        return State.bookings.filter(function (b) {
            if (b.booking_date !== iso) { return false; }
            var slots  = b.booking_slots || {};
            var fromMin = timeToMinutes(slots.from || '');
            var h      = Math.floor(fromMin / 60);
            var m      = fromMin % 60;
            if (h !== hour) { return false; }
            return halfHour ? (m >= 30) : (m < 30);
        });
    }

    /* ------------------------------------------------------------------ */
    /*  RENDER: SUMMARY BAR                                                */
    /* ------------------------------------------------------------------ */

    function renderSummaryBar() {
        var iso   = formatDateISO(State.currentDate);
        var daily = State.bookings.filter(function (b) { return b.booking_date === iso; });
        var completed = daily.filter(function (b) { return b.order_status === 'completed'; }).length;
        var pending   = daily.filter(function (b) { return b.order_status === 'pending' || b.order_status === 'processing'; }).length;

        return '<div class="bmp-summary-bar">' +
            '<span class="bmp-summary-item">Total: <strong class="bmp-summary-count">' + daily.length + '</strong></span>' +
            '<span class="bmp-summary-item">Completed: <strong class="bmp-summary-count">' + completed + '</strong></span>' +
            '<span class="bmp-summary-item">Pending: <strong class="bmp-summary-count">' + pending + '</strong></span>' +
        '</div>';
    }

    /* ------------------------------------------------------------------ */
    /*  MAIN RENDER                                                        */
    /* ------------------------------------------------------------------ */

    function render() {
        var html = renderToolbar();

        if (State.ui.loading) {
            html += '<div class="bm-planner-loading"><div class="bm-planner-spinner"></div><span>Loading&hellip;</span></div>';
        } else if (State.activeView === 'service') {
            html += renderServicePlanner();
        } else {
            html += renderTimePlanner();
        }

        /* Preserve scroll position across re-renders */
        var $body = $root.find('.bmp-grid-body');
        var scrollLeft = $body.scrollLeft();
        var scrollTop  = $body.scrollTop();

        $root.html(html);

        /* Restore scroll */
        var $newBody = $root.find('.bmp-grid-body');
        if (scrollLeft || scrollTop) {
            $newBody.scrollLeft(scrollLeft).scrollTop(scrollTop);
        }

        /* Post-render hooks */
        initDragDrop();
        initResize();
        initDroppableCells();
    }

    /* ------------------------------------------------------------------ */
    /*  DRAG AND DROP (jQuery UI draggable)                               */
    /* ------------------------------------------------------------------ */

    function initDragDrop() {
        $root.find('.bmp-booking').each(function () {
            var $block = $(this);
            if ($block.data('ui-draggable')) { return; }

            $block.draggable({
                axis:        'x',
                containment: $block.closest('.bmp-timeline')[0],
                scroll:      false,
                helper:      'original',
                zIndex:      1000,
                snap:        '.bmp-cell',
                snapMode:    'inner',
                snapTolerance: 10,
                start: function () {
                    $block.addClass('dragging');
                },
                stop: function (event, ui) {
                    $block.removeClass('dragging');
                    handleDragEnd($block, ui);
                }
            });
        });
    }

    function handleDragEnd($block, ui) {
        var bookingId = parseInt($block.data('booking-id'), 10);
        var booking   = findBookingById(bookingId);
        if (!booking) { return; }

        var $timeline  = $block.closest('.bmp-timeline');
        var startH     = State.timeRange.start;
        var endH       = State.timeRange.end;
        var totalHours = endH - startH;
        var gridW      = $timeline.width();
        if (!gridW) { return; }

        var leftPct = parseFloat($block[0].style.left) / 100;
        var startMin = startH * 60;
        var totalMin = totalHours * 60;

        var newFromMin = Math.round((leftPct * totalMin + startMin) / 15) * 15;
        newFromMin = Math.max(startH * 60, Math.min(endH * 60 - 15, newFromMin));

        var oldSlots = $.extend({}, booking.booking_slots || {});
        var durMin   = timeToMinutes((oldSlots.to || '')) - timeToMinutes((oldSlots.from || ''));
        if (durMin <= 0) { durMin = 60; }
        var newToMin = newFromMin + durMin;
        if (newToMin > endH * 60) {
            newToMin   = endH * 60;
            newFromMin = newToMin - durMin;
        }

        var newFrom = minutesToTime(newFromMin);
        var newTo   = minutesToTime(newToMin);

        /* Optimistic update */
        booking.booking_slots = { from: newFrom, to: newTo };
        render();

        API.updateBooking(bookingId, {
            booking_date:   booking.booking_date,
            time_slot_from: newFrom,
            time_slot_to:   newTo,
            service_id:     booking.service_id
        }).fail(function () {
            booking.booking_slots = oldSlots;
            render();
            Toast.show('Failed to move booking. Change reverted.', 'error');
        });
    }

    /* ------------------------------------------------------------------ */
    /*  RESIZE (jQuery UI resizable)                                       */
    /* ------------------------------------------------------------------ */

    function initResize() {
        $root.find('.bmp-booking').each(function () {
            var $block = $(this);
            if ($block.data('ui-resizable')) { return; }

            $block.resizable({
                handles:  'e',
                minWidth: 20,
                containment: $block.closest('.bmp-timeline')[0],
                stop: function (event, ui) {
                    handleResizeEnd($block, ui);
                }
            });
        });
    }

    function handleResizeEnd($block, ui) {
        var bookingId = parseInt($block.data('booking-id'), 10);
        var booking   = findBookingById(bookingId);
        if (!booking) { return; }

        var $timeline  = $block.closest('.bmp-timeline');
        var startH     = State.timeRange.start;
        var endH       = State.timeRange.end;
        var totalHours = endH - startH;
        var gridW      = $timeline.width();
        if (!gridW) { return; }

        var widthPct = ui.size.width / gridW;
        var totalMin = totalHours * 60;

        var slots      = booking.booking_slots || {};
        var fromMin    = timeToMinutes(slots.from || '');
        var durMin     = Math.round((widthPct * totalMin) / 15) * 15;
        if (durMin < 15) { durMin = 15; }
        var newToMin   = fromMin + durMin;
        if (newToMin > endH * 60) { newToMin = endH * 60; }

        var oldTo  = slots.to;
        var newTo  = minutesToTime(newToMin);

        /* Optimistic update */
        booking.booking_slots = { from: slots.from, to: newTo };
        render();

        API.updateBooking(bookingId, {
            booking_date:   booking.booking_date,
            time_slot_from: slots.from,
            time_slot_to:   newTo,
            service_id:     booking.service_id
        }).fail(function () {
            booking.booking_slots = { from: slots.from, to: oldTo };
            render();
            Toast.show('Failed to resize booking. Change reverted.', 'error');
        });
    }

    /* ------------------------------------------------------------------ */
    /*  DROPPABLE CELLS (cross-row drag)                                  */
    /* ------------------------------------------------------------------ */

    function initDroppableCells() {
        $root.find('.bmp-cell').each(function () {
            var $cell = $(this);
            if ($cell.data('ui-droppable')) { return; }
            $cell.droppable({
                accept:    '.bmp-booking',
                tolerance: 'pointer',
                over: function () { $cell.addClass('droppable-hover'); },
                out:  function () { $cell.removeClass('droppable-hover'); },
                drop: function (event, ui) {
                    $cell.removeClass('droppable-hover');
                    var newServiceId = parseInt($cell.data('service-id'), 10);
                    var $block       = ui.draggable;
                    var bookingId    = parseInt($block.data('booking-id'), 10);
                    var booking      = findBookingById(bookingId);
                    if (!booking) { return; }
                    var oldServiceId = parseInt(booking.service_id, 10);
                    if (oldServiceId === newServiceId) { return; }

                    /* Optimistic cross-row move */
                    var oldSvc = findServiceById(oldServiceId);
                    var newSvc = findServiceById(newServiceId);
                    booking.service_id = newServiceId;
                    if (newSvc) {
                        booking.service_name = newSvc.service_name;
                        booking.service_display_name = newSvc.service_name;
                    }
                    render();

                    API.updateBooking(bookingId, {
                        booking_date: booking.booking_date,
                        service_id:   newServiceId
                    }).fail(function () {
                        booking.service_id   = oldServiceId;
                        booking.service_name = oldSvc ? oldSvc.service_name : booking.service_name;
                        booking.service_display_name = booking.service_name;
                        render();
                        Toast.show('Failed to reassign booking. Change reverted.', 'error');
                    });
                }
            });
        });
    }

    function findServiceById(id) {
        var nid = parseInt(id, 10);
        for (var i = 0; i < State.services.length; i++) {
            if (parseInt(State.services[i].id, 10) === nid) { return State.services[i]; }
        }
        return null;
    }

    /* ------------------------------------------------------------------ */
    /*  BOOKING DETAIL MODAL                                               */
    /* ------------------------------------------------------------------ */

    function openBookingDetail(bookingId) {
        var booking = findBookingById(bookingId);
        if (!booking) { return; }

        var slots  = booking.booking_slots || {};
        var status = booking.order_status || '';

        var html = '<dl style="display:grid;grid-template-columns:1fr 1fr;gap:10px 16px;margin:0;">' +
            field('Service',   booking.service_display_name || booking.service_name || '') +
            field('Date',      booking.booking_date || '') +
            field('From',      slots.from || '–') +
            field('To',        slots.to   || '–') +
            field('Customer',  booking.customer_name  || '–') +
            field('Email',     booking.customer_email || '–') +
            field('Status',    '<span class="bmp-status-badge bmp-status-' + sanitizeHtml(status) + '">' + sanitizeHtml(status) + '</span>') +
            field('Payment',   booking.payment_status || '–') +
            field('Total',     booking.total_cost ? '€' + booking.total_cost : '–') +
            field('Booking #', booking.id) +
        '</dl>' +
        '<div class="bmp-modal-footer" style="margin-top:16px;padding-top:12px;border-top:1px solid var(--bmp-border);display:flex;gap:8px;justify-content:flex-end;">' +
            '<button class="bmp-btn bmp-btn-secondary bmp-action-view-reservations" data-booking-id="' + parseInt(booking.id, 10) + '">View Reservations</button>' +
            '<button class="bmp-btn bmp-btn-secondary bmp-action-edit-booking" data-booking-id="' + parseInt(booking.id, 10) + '">Edit</button>' +
            '<button class="bmp-btn bmp-btn-danger bmp-action-delete-booking" data-booking-id="' + parseInt(booking.id, 10) + '">Cancel Booking</button>' +
        '</div>';

        openModal(html, 'Booking #' + booking.id);
    }

    function field(label, value) {
        return '<div>' +
            '<dt style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.3px;color:var(--bmp-text-muted);margin-bottom:2px;">' + sanitizeHtml(label) + '</dt>' +
            '<dd style="margin:0;font-size:13px;color:var(--bmp-text);">' + (typeof value === 'number' ? value : value) + '</dd>' +
        '</div>';
    }

    /* ------------------------------------------------------------------ */
    /*  STATUS CHANGE                                                      */
    /* ------------------------------------------------------------------ */

    function changeBookingStatus(bookingId, newStatus) {
        var booking   = findBookingById(bookingId);
        if (!booking) { return; }
        var oldStatus = booking.order_status;

        /* Optimistic */
        booking.order_status = newStatus;
        render();
        closeModal();

        var slots = booking.booking_slots || {};
        API.updateBooking(bookingId, {
            booking_date:   booking.booking_date,
            time_slot_from: slots.from,
            time_slot_to:   slots.to,
            service_id:     booking.service_id,
            order_status:   newStatus
        }).fail(function () {
            booking.order_status = oldStatus;
            render();
            Toast.show('Status update failed. Reverted.', 'error');
        }).done(function () {
            Toast.show('Booking status updated.', 'success');
        });
    }

    /* ------------------------------------------------------------------ */
    /*  DELETE BOOKING                                                     */
    /* ------------------------------------------------------------------ */

    function deleteBooking(bookingId) {
        var booking = findBookingById(bookingId);
        if (!booking) { return; }
        if (!confirm('Are you sure you want to cancel booking #' + bookingId + '?')) { return; }

        /* Optimistic removal */
        var idx = State.bookings.indexOf(booking);
        State.bookings.splice(idx, 1);
        render();
        closeModal();

        API.deleteBooking(bookingId)
            .done(function () {
                Toast.show('Booking #' + bookingId + ' cancelled.', 'success');
            })
            .fail(function () {
                State.bookings.splice(idx, 0, booking);
                render();
                Toast.show('Failed to cancel booking. Reverted.', 'error');
            });
    }

    /* ------------------------------------------------------------------ */
    /*  EDIT BOOKING MODAL                                                 */
    /* ------------------------------------------------------------------ */

    function openEditBookingModal(bookingId) {
        var booking = findBookingById(bookingId);
        if (!booking) { return; }

        var slots = booking.booking_slots || {};

        var svcOptions = '';
        for (var i = 0; i < State.services.length; i++) {
            var s   = State.services[i];
            var sel = parseInt(s.id, 10) === parseInt(booking.service_id, 10) ? ' selected' : '';
            svcOptions += '<option value="' + parseInt(s.id, 10) + '"' + sel + '>' + sanitizeHtml(s.service_name) + '</option>';
        }

        var html = '<form id="bmp-edit-form" data-booking-id="' + parseInt(booking.id, 10) + '">' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Service</label>' +
                '<select class="bmp-form-select" name="service_id">' + svcOptions + '</select>' +
            '</div>' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Date</label>' +
                '<input type="date" class="bmp-form-input" name="booking_date" value="' + sanitizeHtml(booking.booking_date || '') + '">' +
            '</div>' +
            '<div class="bmp-form-row">' +
                '<div class="bmp-form-group">' +
                    '<label class="bmp-form-label">From</label>' +
                    '<input type="time" class="bmp-form-input" name="time_slot_from" value="' + sanitizeHtml(slots.from || '') + '">' +
                '</div>' +
                '<div class="bmp-form-group">' +
                    '<label class="bmp-form-label">To</label>' +
                    '<input type="time" class="bmp-form-input" name="time_slot_to" value="' + sanitizeHtml(slots.to || '') + '">' +
                '</div>' +
            '</div>' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Customer Name</label>' +
                '<input type="text" class="bmp-form-input" name="customer_name" value="' + sanitizeHtml(booking.customer_name || '') + '">' +
            '</div>' +
            '<div class="bmp-modal-footer">' +
                '<button type="button" class="bmp-btn bmp-btn-secondary bmp-modal-close-btn">Cancel</button>' +
                '<button type="submit" class="bmp-btn bmp-btn-primary">Save Changes</button>' +
            '</div>' +
        '</form>';

        openModal(html, 'Edit Booking #' + booking.id);
    }

    function handleEditFormSubmit($form) {
        var bookingId = parseInt($form.data('booking-id'), 10);
        var booking   = findBookingById(bookingId);
        if (!booking) { return; }

        var data = {
            service_id:     parseInt($form.find('[name="service_id"]').val(), 10),
            booking_date:   $form.find('[name="booking_date"]').val(),
            time_slot_from: $form.find('[name="time_slot_from"]').val(),
            time_slot_to:   $form.find('[name="time_slot_to"]').val(),
            customer_name:  $form.find('[name="customer_name"]').val()
        };

        /* Optimistic */
        var oldData = {
            service_id:   booking.service_id,
            booking_date: booking.booking_date,
            booking_slots: $.extend({}, booking.booking_slots),
            customer_name: booking.customer_name
        };
        var svc = findServiceById(data.service_id);
        booking.service_id    = data.service_id;
        booking.booking_date  = data.booking_date;
        booking.booking_slots = { from: data.time_slot_from, to: data.time_slot_to };
        booking.customer_name = data.customer_name;
        if (svc) { booking.service_display_name = svc.service_name; }
        closeModal();
        render();

        API.updateBooking(bookingId, data)
            .done(function () {
                Toast.show('Booking updated.', 'success');
                loadBookings();
            })
            .fail(function () {
                booking.service_id    = oldData.service_id;
                booking.booking_date  = oldData.booking_date;
                booking.booking_slots = oldData.booking_slots;
                booking.customer_name = oldData.customer_name;
                var oldSvc = findServiceById(oldData.service_id);
                if (oldSvc) { booking.service_display_name = oldSvc.service_name; }
                render();
                Toast.show('Save failed. Changes reverted.', 'error');
            });
    }

    /* ------------------------------------------------------------------ */
    /*  CREATE BOOKING MODAL                                               */
    /* ------------------------------------------------------------------ */

    function openCreateBookingModal(serviceId, hour) {
        var svc = findServiceById(serviceId);

        var fromDefault = pad(hour) + ':00';
        var toDefault   = pad(hour + 1) + ':00';
        var durMin      = svc && svc.service_duration ? parseInt(svc.service_duration, 10) : 60;
        if (durMin > 0) { toDefault = minutesToTime(hour * 60 + durMin); }

        var svcOptions = '';
        for (var i = 0; i < State.services.length; i++) {
            var s   = State.services[i];
            var sel = parseInt(s.id, 10) === parseInt(serviceId, 10) ? ' selected' : '';
            svcOptions += '<option value="' + parseInt(s.id, 10) + '"' + sel + '>' + sanitizeHtml(s.service_name) + '</option>';
        }

        var html = '<form id="bmp-create-form">' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Service</label>' +
                '<select class="bmp-form-select" name="service_id">' + svcOptions + '</select>' +
            '</div>' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Date</label>' +
                '<input type="date" class="bmp-form-input" name="booking_date" value="' + sanitizeHtml(formatDateISO(State.currentDate)) + '">' +
            '</div>' +
            '<div class="bmp-form-row">' +
                '<div class="bmp-form-group">' +
                    '<label class="bmp-form-label">From</label>' +
                    '<input type="time" class="bmp-form-input" name="time_slot_from" value="' + sanitizeHtml(fromDefault) + '">' +
                '</div>' +
                '<div class="bmp-form-group">' +
                    '<label class="bmp-form-label">To</label>' +
                    '<input type="time" class="bmp-form-input" name="time_slot_to" value="' + sanitizeHtml(toDefault) + '">' +
                '</div>' +
            '</div>' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Customer Name</label>' +
                '<input type="text" class="bmp-form-input" name="customer_name" placeholder="Enter name">' +
            '</div>' +
            '<div class="bmp-form-group">' +
                '<label class="bmp-form-label">Customer Email</label>' +
                '<input type="email" class="bmp-form-input" name="customer_email" placeholder="Enter email">' +
            '</div>' +
            '<div class="bmp-modal-footer">' +
                '<button type="button" class="bmp-btn bmp-btn-secondary bmp-modal-close-btn">Cancel</button>' +
                '<button type="submit" class="bmp-btn bmp-btn-primary">Create Booking</button>' +
            '</div>' +
        '</form>';

        openModal(html, 'New Booking');
    }

    function handleCreateFormSubmit($form) {
        var $submit = $form.find('[type="submit"]').prop('disabled', true).text('Creating…');

        var serviceId = parseInt($form.find('[name="service_id"]').val(), 10);
        var svc = findServiceById(serviceId);
        var durMin = svc && svc.service_duration ? parseInt(svc.service_duration, 10) : 60;
        var fromMin = timeToMinutes($form.find('[name="time_slot_from"]').val());
        var toMin   = timeToMinutes($form.find('[name="time_slot_to"]').val());
        if (toMin <= fromMin) { toMin = fromMin + durMin; }
        var totalSlots = Math.max(1, Math.round((toMin - fromMin) / (durMin || 60)));

        var data = {
            service_id:       serviceId,
            booking_date:     $form.find('[name="booking_date"]').val(),
            time_slot_from:   $form.find('[name="time_slot_from"]').val(),
            time_slot_to:     minutesToTime(toMin),
            total_svc_slots:  totalSlots,
            customer_name:    $form.find('[name="customer_name"]').val(),
            customer_email:   $form.find('[name="customer_email"]').val()
        };

        API.createBooking(data)
            .done(function (resp) {
                closeModal();
                Toast.show('Booking created successfully.', 'success');
                loadBookings();
            })
            .fail(function (xhr) {
                $submit.prop('disabled', false).text('Create Booking');
                var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Failed to create booking.';
                Toast.show(msg, 'error');
            });
    }

    /* ------------------------------------------------------------------ */
    /*  VIEW RESERVATIONS                                                  */
    /* ------------------------------------------------------------------ */

    function openReservationsModal(bookingId) {
        var booking = findBookingById(bookingId);
        if (!booking) { return; }
        var slots = booking.booking_slots || {};

        openModal('<div class="bm-planner-loading"><div class="bm-planner-spinner"></div></div>', 'Reservations');

        API.fetchReservations(booking.service_id, State.currentDate, slots.from || '')
            .done(function (resp) {
                var reservations = resp.reservations || [];
                var tbody = '';
                if (reservations.length) {
                    for (var i = 0; i < reservations.length; i++) {
                        var r = reservations[i];
                        tbody += '<tr>' +
                            '<td>' + sanitizeHtml(r.customer_name || '–') + '</td>' +
                            '<td>' + sanitizeHtml(r.time_slot || '–') + '</td>' +
                            '<td><span class="bmp-status-badge bmp-status-' + sanitizeHtml(r.order_status || '') + '">' + sanitizeHtml(r.order_status || '–') + '</span></td>' +
                        '</tr>';
                    }
                } else {
                    tbody = '<tr><td colspan="3" style="text-align:center;color:var(--bmp-text-muted);padding:20px;">No reservations found.</td></tr>';
                }

                var html = '<table class="bmp-res-table">' +
                    '<thead><tr><th>Customer</th><th>Time Slot</th><th>Status</th></tr></thead>' +
                    '<tbody>' + tbody + '</tbody>' +
                '</table>';

                if ($currentModal) {
                    $currentModal.find('.bmp-modal-body').html(html);
                }
            })
            .fail(function () {
                if ($currentModal) {
                    $currentModal.find('.bmp-modal-body').html('<p style="color:var(--bmp-danger);padding:16px;">Failed to load reservations.</p>');
                }
            });
    }

    /* ------------------------------------------------------------------ */
    /*  DATA LOADING                                                       */
    /* ------------------------------------------------------------------ */

    function loadBookings() {
        var d = State.currentDate;
        setState({ ui: { loading: true } });
        API.fetchBookings(d, d)
            .done(function (resp) {
                setState({
                    bookings: (resp && resp.bookings) || [],
                    ui: { loading: false }
                });
            })
            .fail(function () {
                setState({ bookings: [], ui: { loading: false } });
                Toast.show('Failed to load bookings.', 'error');
            });
    }

    function changeDate(newDate) {
        setState({ currentDate: newDate });
        loadBookings();
    }

    /* ------------------------------------------------------------------ */
    /*  SCROLL TO CURRENT HOUR                                             */
    /* ------------------------------------------------------------------ */

    function scrollToCurrentTime() {
        var now  = new Date();
        var startH = State.timeRange.start;
        var endH   = State.timeRange.end;
        var h    = now.getHours();
        if (h < startH) { h = startH; }
        if (h > endH)   { h = endH; }
        var leftPx = (h - startH) * HOUR_PX - 60;
        $root.find('.bmp-grid-body').scrollLeft(Math.max(0, leftPx));
    }

    /* ------------------------------------------------------------------ */
    /*  EVENT BINDING (delegated – survives re-renders)                   */
    /* ------------------------------------------------------------------ */

    $root.on('click', '.bmp-view-btn', function () {
        var view = $(this).data('view');
        if (view && view !== State.activeView) {
            setState({ activeView: view });
        }
    });

    $root.on('click', '.bmp-date-nav-btn', function () {
        var dir = parseInt($(this).data('dir'), 10);
        changeDate(addDays(State.currentDate, dir));
    });

    $root.on('click', '.bmp-today-btn', function () {
        changeDate(new Date());
    });

    /* Native date picker via hidden input */
    $root.on('click', '#bmp-date-display-btn', function () {
        var $btn = $(this);
        var $dp  = $btn.next('.bmp-hidden-dp');
        if (!$dp.length) {
            $dp = $('<input type="date" class="bmp-hidden-dp" style="position:absolute;opacity:0;width:1px;height:1px;pointer-events:none;">')
                .val(formatDateISO(State.currentDate));
            $btn.after($dp);
            $dp.on('change', function () {
                var parts = $(this).val().split('-');
                if (parts.length === 3) {
                    changeDate(new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10)));
                }
            });
        }
        $dp[0].showPicker ? $dp[0].showPicker() : $dp.trigger('click');
    });

    /* Filter dropdowns */
    $root.on('click', '.bmp-filter-btn', function (e) {
        e.stopPropagation();
        var $panel = $(this).siblings('.bmp-filter-panel');
        var isOpen = $panel.hasClass('open');
        $root.find('.bmp-filter-panel.open').removeClass('open');
        if (!isOpen) { $panel.addClass('open'); }
    });

    $(document).on('click', function () {
        $root.find('.bmp-filter-panel.open').removeClass('open');
    });

    $root.on('change', '[data-filter-all]', function () {
        var key      = $(this).data('filter-all');
        var checked  = $(this).prop('checked');
        var items    = key === 'services' ? State.services : State.categories;
        var ids      = items.map(function (item) { return parseInt(item.id, 10); });
        var upd      = {};
        upd[key]     = checked ? ids : [];
        setState({ filters: upd });
    });

    $root.on('change', '[data-filter-key]', function () {
        var key = $(this).data('filter-key');
        var val = parseInt($(this).val(), 10);
        var cur = (State.filters[key] || []).slice();
        if ($(this).prop('checked')) {
            if (cur.indexOf(val) === -1) { cur.push(val); }
        } else {
            cur = cur.filter(function (v) { return v !== val; });
        }
        var upd = {};
        upd[key] = cur;
        setState({ filters: upd });
    });

    /* Booking click (detail modal) */
    $root.on('click', '.bmp-booking, .bmp-time-booking-card', function (e) {
        if ($(e.target).hasClass('bmp-resize-handle') || $(e.target).hasClass('ui-resizable-e')) { return; }
        if ($(this).hasClass('ui-draggable-dragging')) { return; }
        e.stopPropagation();
        var bookingId = parseInt($(this).data('booking-id'), 10);
        openBookingDetail(bookingId);
    });

    /* Empty cell click → create booking */
    $root.on('click', '.bmp-cell', function (e) {
        if ($(e.target).is('.bmp-booking') || $(e.target).closest('.bmp-booking').length) { return; }
        var serviceId = parseInt($(this).data('service-id'), 10);
        var hour      = parseInt($(this).data('hour'), 10);
        openCreateBookingModal(serviceId, hour);
    });

    /* Modal actions */
    $root.on('click', '.bmp-modal-close, .bmp-modal-close-btn', function () { closeModal(); });

    $root.on('click', '.bmp-action-delete-booking', function () {
        var bookingId = parseInt($(this).data('booking-id'), 10);
        deleteBooking(bookingId);
    });

    $root.on('click', '.bmp-action-edit-booking', function () {
        var bookingId = parseInt($(this).data('booking-id'), 10);
        closeModal();
        openEditBookingModal(bookingId);
    });

    $root.on('click', '.bmp-action-view-reservations', function () {
        var bookingId = parseInt($(this).data('booking-id'), 10);
        openReservationsModal(bookingId);
    });

    /* Edit form submit */
    $root.on('submit', '#bmp-edit-form', function (e) {
        e.preventDefault();
        handleEditFormSubmit($(this));
    });

    /* Create form submit */
    $root.on('submit', '#bmp-create-form', function (e) {
        e.preventDefault();
        handleCreateFormSubmit($(this));
    });

    /* Keyboard shortcuts */
    $(document).on('keydown.bmp', function (e) {
        var tag = (e.target.tagName || '').toLowerCase();
        if (tag === 'input' || tag === 'textarea' || tag === 'select') { return; }

        if (e.key === 'Escape') { closeModal(); return; }

        if (e.key === 'ArrowLeft') {
            e.preventDefault();
            changeDate(addDays(State.currentDate, -1));
        } else if (e.key === 'ArrowRight') {
            e.preventDefault();
            changeDate(addDays(State.currentDate, 1));
        } else if (e.key === 't' || e.key === 'T') {
            changeDate(new Date());
        }
    });

    /* ------------------------------------------------------------------ */
    /*  INITIALIZATION                                                     */
    /* ------------------------------------------------------------------ */

    function init() {
        setState({ ui: { loading: true } });

        $.when(API.fetchServices(), API.fetchCategories())
            .done(function (svcResp, catResp) {
                /* $.when passes each result as [data, textStatus, jqXHR] */
                var svcData = Array.isArray(svcResp) ? svcResp[0] : svcResp;
                var catData = Array.isArray(catResp) ? catResp[0] : catResp;
                var services   = (svcData && svcData.services)   || [];
                var categories = (catData && catData.categories) || [];

                /* Assign colour indices deterministically */
                services.forEach(function (s) { getServiceColor(s.id); });

                var allSvcIds = services.map(function (s) { return parseInt(s.id, 10); });
                var allCatIds = categories.map(function (c) { return parseInt(c.id, 10); });

                State.services             = services;
                State.categories           = categories;
                State.filters.services     = allSvcIds;
                State.filters.categories   = allCatIds;
                State.ui.loading           = false;

                render();
                loadBookings();

                /* Scroll to current hour after first data render */
                setTimeout(scrollToCurrentTime, 200);
            })
            .fail(function () {
                setState({ ui: { loading: false } });
                Toast.show('Failed to load planner data.', 'error');
            });
    }

    init();

}(jQuery));
