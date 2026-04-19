/**
 * Booking Planner – 3-view SPA (Home · Service Planner · Time Planner)
 *
 * Architecture:
 *   State ──► render() ──► DOM (innerHTML string-building)
 *   Events ──► setState() ──► batched render via requestAnimationFrame
 *   API calls use $.when() for parallel requests
 *
 * @since 1.0.0
 */
(function ($) {
    'use strict';

    /* ===================================================================== */
    /*  BOOTSTRAP                                                             */
    /* ===================================================================== */

    var $root = $('#bm-planner-root');
    if (!$root.length) { return; }

    var NONCE    = $root.data('nonce') || '';
    var REST_URL = ($root.data('rest-url') || '').replace(/\/+$/, '');

    /* ===================================================================== */
    /*  CONSTANTS                                                             */
    /* ===================================================================== */

    var VIEWS = { HOME: 'home', SERVICE: 'service-planner', TIME: 'time-planner' };

    var CATEGORY_COLORS = [
        { solid: '#2563EB', light: '#EFF6FF' },
        { solid: '#9333EA', light: '#FAF5FF' },
        { solid: '#16A34A', light: '#F0FDF4' },
        { solid: '#6B7280', light: '#F9FAFB' },
        { solid: '#EA580C', light: '#FFF7ED' },
        { solid: '#DB2777', light: '#FDF2F8' },
        { solid: '#0D9488', light: '#F0FDFA' },
        { solid: '#4F46E5', light: '#EEF2FF' }
    ];

    var AVAIL_HIGH   = { color: '#16A34A', bg: '#F0FDF4', label: 'High availability',   threshold: 0.5 };
    var AVAIL_MEDIUM = { color: '#D97706', bg: '#FFFBEB', label: 'Medium availability',  threshold: 0.2 };
    var AVAIL_LOW    = { color: '#DC2626', bg: '#FEF2F2', label: 'Low availability',     threshold: 0 };

    var HOUR_HEIGHT  = 80;
    var GRID_START_H = 7;
    var GRID_END_H   = 22;

    var DISPLAY_STORAGE_KEY = 'bm_planner_display_settings';

    /* ===================================================================== */
    /*  HELPERS                                                               */
    /* ===================================================================== */

    function pad(n) { return n < 10 ? '0' + n : '' + n; }

    function sanitizeHtml(str) {
        if (str == null) { return ''; }
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function formatISO(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function addDays(d, n) {
        var r = new Date(d); r.setDate(r.getDate() + n); return r;
    }

    function getMondayOfWeek(d) {
        var day  = d.getDay();
        var diff = day === 0 ? -6 : 1 - day;
        return addDays(d, diff);
    }

    function getVisibleDates() {
        var days = State.display.days || 7;
        var dates = [];
        for (var i = 0; i < days; i++) { dates.push(addDays(State.weekStart, i)); }
        return dates;
    }

    function formatWeekRange(dates) {
        var mo = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var s  = dates[0];
        var e  = dates[dates.length - 1];
        if (s.getMonth() === e.getMonth()) {
            return mo[s.getMonth()] + ' ' + s.getDate() + ' \u2013 ' + e.getDate() + ', ' + e.getFullYear();
        }
        return mo[s.getMonth()] + ' ' + s.getDate() + ' \u2013 ' + mo[e.getMonth()] + ' ' + e.getDate() + ', ' + e.getFullYear();
    }

    function formatFullDate(d) {
        var days   = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
        var months = ['January','February','March','April','May','June','July','August','September','October','November','December'];
        return days[d.getDay()] + ', ' + months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
    }

    function timeToMinutes(t) {
        if (!t) { return 0; }
        var p = t.split(':');
        return parseInt(p[0], 10) * 60 + parseInt(p[1] || '0', 10);
    }

    function formatDuration(mins) {
        mins = parseInt(mins, 10) || 0;
        if (mins < 60) { return mins + ' min'; }
        var h = Math.floor(mins / 60);
        var m = mins % 60;
        if (m === 0) { return h + ' h'; }
        return h + 'h ' + m + 'min';
    }

    function formatPrice(p) {
        var n = parseFloat(p) || 0;
        return '\u20ac' + (n % 1 === 0 ? n.toFixed(0) : n.toFixed(2));
    }

    function getAvailInfo(available, max) {
        if (!max) { return AVAIL_HIGH; }
        var ratio = available / max;
        if (ratio > AVAIL_HIGH.threshold)   { return AVAIL_HIGH; }
        if (ratio > AVAIL_MEDIUM.threshold) { return AVAIL_MEDIUM; }
        return AVAIL_LOW;
    }

    var _catColorMap = {};
    function buildCategoryColorMap(cats) {
        _catColorMap = {};
        (cats || []).forEach(function (c, i) {
            _catColorMap[c.id] = CATEGORY_COLORS[i % CATEGORY_COLORS.length];
        });
    }
    function getCatColor(catId) {
        return _catColorMap[catId] || CATEGORY_COLORS[3];
    }

    function loadDisplaySettings() {
        try {
            var raw = localStorage.getItem(DISPLAY_STORAGE_KEY);
            if (raw) {
                var saved = JSON.parse(raw);
                return $.extend({
                    showDuration: true, showCategory: true, showPrice: true,
                    showSlotPrice: false, maxSlots: 5, days: 7
                }, saved);
            }
        } catch (e) { /* ignore */ }
        return { showDuration: true, showCategory: true, showPrice: true, showSlotPrice: false, maxSlots: 5, days: 7 };
    }

    function saveDisplaySettings(display) {
        try { localStorage.setItem(DISPLAY_STORAGE_KEY, JSON.stringify(display)); } catch (e) { /* ignore */ }
    }

    /* ===================================================================== */
    /*  STATE                                                                 */
    /* ===================================================================== */

    var today     = new Date();
    var todayISO  = formatISO(today);

    var State = {
        view:      VIEWS.HOME,
        weekStart: getMondayOfWeek(today),
        data:      { services: [], slots: {}, categories: [], summary: {} },
        filter:    { cat: 0, svc: 0 },
        display:   loadDisplaySettings(),
        loading:   false,
        _autoHide: false,
        _displayOpen: false,
        _dayView:  null,
        _tpDayView: null
    };

    var _renderScheduled = false;
    function setState(partial) {
        for (var k in partial) {
            if (!partial.hasOwnProperty(k)) { continue; }
            State[k] = partial[k];
        }
        if (partial.display) {
            saveDisplaySettings(State.display);
        }
        if (!_renderScheduled) {
            _renderScheduled = true;
            requestAnimationFrame(function () {
                _renderScheduled = false;
                render();
            });
        }
    }

    /* ===================================================================== */
    /*  API                                                                   */
    /* ===================================================================== */

    var API = {
        _get: function (path, params) {
            return $.ajax({
                url:    REST_URL + '/' + path,
                method: 'GET',
                data:   params || {},
                beforeSend: function (xhr) { xhr.setRequestHeader('X-WP-Nonce', NONCE); },
                dataType: 'json'
            });
        },
        fetchCategories:  function ()            { return API._get('categories'); },
        fetchPlannerWeek: function (start, end, opts) {
            var p = $.extend({ start_date: start, end_date: end }, opts || {});
            return API._get('planner-week', p);
        },
        fetchSlotBookings: function (svcId, date, timeSlot) {
            return API._get('slot-bookings', { service_id: svcId, date: date, time_slot: timeSlot });
        }
    };

    /* ===================================================================== */
    /*  DATA LOADING                                                          */
    /* ===================================================================== */

    function loadWeekData() {
        setState({ loading: true });
        var dates = getVisibleDates();
        var start = formatISO(dates[0]);
        var end   = formatISO(dates[dates.length - 1]);
        var opts  = {};
        if (State.filter.cat) { opts.category_id = State.filter.cat; }
        if (State.filter.svc) { opts.service_id  = State.filter.svc; }

        $.when(API.fetchCategories(), API.fetchPlannerWeek(start, end, opts))
            .then(function (catRes, weekRes) {
                var cats  = (catRes[0]  && catRes[0].categories)  || [];
                var svcs  = (weekRes[0] && weekRes[0].services)   || [];
                var slots = (weekRes[0] && weekRes[0].slots)      || {};
                var summ  = (weekRes[0] && weekRes[0].summary)    || {};
                buildCategoryColorMap(cats);
                setState({
                    loading: false,
                    data: { services: svcs, slots: slots, categories: cats, summary: summ }
                });
            })
            .fail(function () {
                setState({ loading: false });
                Toast.show('Failed to load planner data.', 'error');
            });
    }

    /* ===================================================================== */
    /*  TOAST                                                                 */
    /* ===================================================================== */

    var Toast = {
        _c: null,
        _ensure: function () {
            if (!this._c || !document.body.contains(this._c[0])) {
                this._c = $('<div class="bm-planner-toast-wrap"></div>');
                $('body').append(this._c);
            }
            return this._c;
        },
        show: function (msg, type) {
            var $c = this._ensure();
            var $t = $('<div class="bm-planner-toast ' + sanitizeHtml(type || 'info') + '">' + sanitizeHtml(msg) + '</div>');
            $c.append($t);
            setTimeout(function () { $t.addClass('visible'); }, 10);
            var dismiss = function () {
                $t.removeClass('visible');
                setTimeout(function () { $t.remove(); }, 350);
            };
            $t.on('click', dismiss);
            setTimeout(dismiss, 4000);
        }
    };

    /* ===================================================================== */
    /*  TOOLTIP                                                               */
    /* ===================================================================== */

    var Tooltip = {
        _el: null,
        _timer: null,
        show: function (data, x, y) {
            this.hide();
            var avail = getAvailInfo(data.available_capacity, data.max_capacity);
            var cat   = getCatColor(data.service_category);
            var booked = data.max_capacity ? data.max_capacity - data.available_capacity : data.booking_count;

            var html  =
                '<div class="bm-planner-tooltip__inner" style="border-left:4px solid ' + cat.solid + '">' +
                '<div class="bm-planner-tooltip__hdr">' +
                    '<span class="bm-planner-tooltip__cat" style="background:' + cat.light + ';color:' + cat.solid + '">' + sanitizeHtml(data.category_name || '') + '</span>' +
                    '<span class="bm-planner-tooltip__avail-badge" style="background:' + avail.bg + ';color:' + avail.color + '">' + avail.label + '</span>' +
                '</div>' +
                '<div class="bm-planner-tooltip__body">' +
                    '<h4 class="bm-planner-tooltip__name">' + sanitizeHtml(data.service_name) + '</h4>' +
                    '<div class="bm-planner-tooltip__row"><span>\u23f1 Time</span><span>' + sanitizeHtml(data.time_display) + '</span></div>' +
                    '<div class="bm-planner-tooltip__row"><span>\ud83d\udcc5 Duration</span><span>' + sanitizeHtml(formatDuration(data.service_duration)) + '</span></div>' +
                    '<div class="bm-planner-tooltip__row"><span>\ud83d\udc65 Available slots</span><span style="color:' + avail.color + ';font-weight:600">' + data.available_capacity + ' of ' + data.max_capacity + '</span></div>' +
                    '<div class="bm-planner-tooltip__row"><span>\ud83d\udc64 Participants</span><span>' + booked + '</span></div>' +
                    '<div class="bm-planner-tooltip__row"><span>Price</span><span>' + sanitizeHtml(formatPrice(data.service_price)) + '</span></div>' +
                '</div>' +
                '<div class="bm-planner-tooltip__ftr">' +
                    '<span>' + data.booking_count + ' booking' + (data.booking_count !== 1 ? 's' : '') + '</span>' +
                    '<a href="#" class="bm-planner-tooltip__link"' +
                        ' data-action="view-details"' +
                        ' data-svc-id="' + parseInt(data.service_id, 10) + '"' +
                        ' data-date="' + sanitizeHtml(data.date) + '"' +
                        ' data-from="' + sanitizeHtml(data.slot_from) + '"' +
                    '>View details \u2197</a>' +
                '</div>' +
                '</div>';

            this._el = $('<div class="bm-planner-tooltip" style="position:fixed;z-index:9000">' + html + '</div>');
            $('body').append(this._el);
            this.position(x, y);
        },
        position: function (x, y) {
            if (!this._el) { return; }
            var w = this._el.outerWidth() || 300;
            var h = this._el.outerHeight() || 200;
            var vw = $(window).width();
            var vh = $(window).height();
            var left = x + 12;
            var top  = y - h / 2;
            if (left + w > vw - 10) { left = x - w - 12; }
            if (top < 10)           { top  = 10; }
            if (top + h > vh - 10)  { top  = vh - h - 10; }
            this._el.css({ left: left, top: top });
        },
        hide: function () {
            clearTimeout(this._timer);
            if (this._el) { this._el.remove(); this._el = null; }
        },
        scheduleHide: function (delay) {
            var self = this;
            this._timer = setTimeout(function () { self.hide(); }, delay || 200);
        },
        cancelHide: function () {
            clearTimeout(this._timer);
        }
    };

    /* ===================================================================== */
    /*  MODAL                                                                 */
    /* ===================================================================== */

    var $currentModal = null;

    function openModal(innerHtml) {
        closeModal();
        var $ov = $('<div class="bmp-modal-overlay"></div>');
        $ov.html('<div class="bmp-modal">' + innerHtml + '</div>');
        $('body').append($ov);
        $currentModal = $ov;
        setTimeout(function () { $ov.addClass('visible'); }, 10);
        $ov.on('click', function (e) {
            if ($(e.target).is($ov)) { closeModal(); }
        });
    }

    function closeModal() {
        if (!$currentModal) { return; }
        $currentModal.removeClass('visible');
        var $m = $currentModal; $currentModal = null;
        setTimeout(function () { $m.remove(); }, 280);
    }

    /* ===================================================================== */
    /*  RENDER: HOME                                                          */
    /* ===================================================================== */

    function renderHome() {
        return '<div class="bm-planner-home">' +
            '<div class="bm-planner-home__header">' +
                '<div class="bm-planner-home__icon">' +
                    '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>' +
                '</div>' +
                '<h1 class="bm-planner-home__title">Service Booking Planner</h1>' +
                '<p class="bm-planner-home__subtitle">Manage your service bookings with two optimized views</p>' +
            '</div>' +
            '<div class="bm-planner-home__cards">' +
                renderHomeCard('service-planner', 'green',
                    '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>',
                    'Service Planner',
                    'Services as rows, days as columns. Perfect for managing multiple time slots per service.'
                ) +
                renderHomeCard('time-planner', 'blue',
                    '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>',
                    'Time Planner',
                    'Vertical hourly grid with days as columns. Ideal for viewing availability over time.'
                ) +
            '</div>' +
        '</div>';
    }

    function renderHomeCard(view, color, iconSvg, title, desc) {
        return '<div class="bm-planner-home__card" data-view="' + view + '">' +
            '<div class="bm-planner-home__card-icon bm-planner-home__card-icon--' + color + '">' + iconSvg + '</div>' +
            '<h2 class="bm-planner-home__card-title">' + sanitizeHtml(title) + '</h2>' +
            '<p class="bm-planner-home__card-desc">' + sanitizeHtml(desc) + '</p>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: NAV BAR                                                       */
    /* ===================================================================== */

    function renderNav() {
        var spCls = State.view === VIEWS.SERVICE ? ' bm-planner-nav__tab--active' : '';
        var tpCls = State.view === VIEWS.TIME    ? ' bm-planner-nav__tab--active' : '';
        return '<div class="bm-planner-nav">' +
            '<button class="bm-planner-nav__tab' + spCls + '" data-nav="service-planner">' +
                '<span class="bm-planner-nav__tab-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg></span>' +
                ' Service Planner' +
            '</button>' +
            '<button class="bm-planner-nav__tab' + tpCls + '" data-nav="time-planner">' +
                '<span class="bm-planner-nav__tab-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg></span>' +
                ' Time Planner' +
            '</button>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: TOOLBAR                                                       */
    /* ===================================================================== */

    function renderToolbar() {
        var dates    = getVisibleDates();
        var rangeStr = formatWeekRange(dates);
        var isSP     = State.view === VIEWS.SERVICE;
        var isTP     = State.view === VIEWS.TIME;

        var catOpts = '<option value="0">All Categories</option>';
        (State.data.categories || []).forEach(function (c) {
            var sel = State.filter.cat === parseInt(c.id, 10) ? ' selected' : '';
            catOpts += '<option value="' + parseInt(c.id, 10) + '"' + sel + '>' + sanitizeHtml(c.cat_name) + '</option>';
        });

        var svcOpts = '<option value="0">All Services</option>';
        (State.data.services || []).forEach(function (s) {
            var sel = State.filter.svc === parseInt(s.id, 10) ? ' selected' : '';
            svcOpts += '<option value="' + parseInt(s.id, 10) + '"' + sel + '>' + sanitizeHtml(s.service_name) + '</option>';
        });

        var autoCls  = State._autoHide ? ' bm-planner-tp__auto-btn--active' : '';

        return '<div class="bmp-toolbar">' +
            '<div class="bmp-toolbar-left">' +
                '<button class="bmp-nav-btn" data-week="-1" title="Previous week">&#9664;</button>' +
                '<span class="bmp-date-display">' +
                    '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>' +
                    ' ' + sanitizeHtml(rangeStr) +
                '</span>' +
                '<button class="bmp-today-btn" data-action="today">Today</button>' +
                '<button class="bmp-nav-btn" data-week="1" title="Next week">&#9654;</button>' +
                ( isTP ? '<button class="bmp-today-btn" data-action="ora">\u23f1 Now</button>' : '' ) +
                ( isTP ? '<button class="bmp-today-btn' + autoCls + '" data-action="auto">Auto</button>' : '' ) +
            '</div>' +
            '<div class="bmp-toolbar-right">' +
                '<select class="bmp-filter-select" data-filter="svc">' + svcOpts + '</select>' +
                '<select class="bmp-filter-select" data-filter="cat">' + catOpts + '</select>' +
                '<button class="bm-planner-display-btn" data-action="display">\u2699 Display</button>' +
            '</div>' +
        '</div>' +
        renderDisplayPanel();
    }

    function renderDisplayPanel() {
        if (!State._displayOpen) { return ''; }
        return '<div class="bm-planner-display-panel">' +
            '<p class="bm-planner-display-panel__title">Display Settings</p>' +
            '<p class="bm-planner-display-panel__subtitle">Customize what\'s shown in the planner</p>' +
            '<div class="bm-planner-display-panel__section">' +
                '<p class="bm-planner-display-panel__section-title">Information to show</p>' +
                '<label class="bm-planner-display-panel__check"><input type="checkbox" data-disp="showDuration"' + (State.display.showDuration ? ' checked' : '') + '> Duration</label>' +
                '<label class="bm-planner-display-panel__check"><input type="checkbox" data-disp="showCategory"' + (State.display.showCategory ? ' checked' : '') + '> Category</label>' +
                '<label class="bm-planner-display-panel__check"><input type="checkbox" data-disp="showPrice"' + (State.display.showPrice ? ' checked' : '') + '> Price</label>' +
                '<label class="bm-planner-display-panel__check"><input type="checkbox" data-disp="showSlotPrice"' + (State.display.showSlotPrice ? ' checked' : '') + '> Price on slots</label>' +
            '</div>' +
            '<div class="bm-planner-display-panel__section">' +
                '<p class="bm-planner-display-panel__section-title">Max slots per cell: <strong>' + State.display.maxSlots + '</strong></p>' +
                '<input type="range" class="bm-planner-display-panel__range" data-disp-range="maxSlots" min="1" max="20" value="' + State.display.maxSlots + '">' +
            '</div>' +
            '<div class="bm-planner-display-panel__section">' +
                '<p class="bm-planner-display-panel__section-title">Days to show: <strong>' + State.display.days + '</strong></p>' +
                '<input type="range" class="bm-planner-display-panel__range" data-disp-range="days" min="1" max="14" value="' + State.display.days + '">' +
            '</div>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: FOOTER LEGEND                                                 */
    /* ===================================================================== */

    function renderFooter() {
        var catDots = '';
        (State.data.categories || []).forEach(function (c) {
            var col = getCatColor(c.id);
            catDots += '<span class="bm-planner-footer__item"><span class="bm-planner-footer__dot" style="background:' + col.solid + '"></span> ' + sanitizeHtml(c.cat_name) + '</span>';
        });
        return '<div class="bm-planner-footer">' +
            '<div class="bm-planner-footer__left">' + catDots + '</div>' +
            '<div class="bm-planner-footer__right">' +
                '<span class="bm-planner-footer__item"><span class="bm-planner-footer__dot" style="background:#16A34A"></span> High</span>' +
                '<span class="bm-planner-footer__item"><span class="bm-planner-footer__dot" style="background:#D97706"></span> Medium</span>' +
                '<span class="bm-planner-footer__item"><span class="bm-planner-footer__dot" style="background:#DC2626"></span> Low</span>' +
            '</div>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: SERVICE PLANNER                                               */
    /* ===================================================================== */

    function renderServicePlanner() {
        if (State._dayView) { return renderDayView(State._dayView.date); }

        var dates    = getVisibleDates();
        var numDays  = dates.length;
        var services = State.data.services || [];
        var slots    = State.data.slots    || {};
        var maxSlots = State.display.maxSlots;

        var dayAbbr = ['SUN','MON','TUE','WED','THU','FRI','SAT'];

        var headCells = '<div class="bm-planner-sp__corner">Services</div>';
        dates.forEach(function (d) {
            var iso     = formatISO(d);
            var isToday = iso === todayISO;
            var todayCls = isToday ? ' bm-planner-sp__day-header--today' : '';
            var numCls   = isToday ? ' bm-planner-sp__day-num--today'   : '';
            headCells += '<div class="bm-planner-sp__day-header' + todayCls + '">' +
                '<div class="bm-planner-sp__day-abbr">' + dayAbbr[d.getDay()] + '</div>' +
                '<div class="bm-planner-sp__day-num' + numCls + '" data-action="day-click" data-date="' + sanitizeHtml(iso) + '">' + d.getDate() + '</div>' +
            '</div>';
        });

        var gridStyle = 'grid-template-columns: 200px repeat(' + numDays + ', 1fr)';

        var rows = '';
        if (!services.length) {
            rows = '<div class="bm-planner-sp__empty">No services found.</div>';
        } else {
            services.forEach(function (svc) {
                var catCol = getCatColor(svc.service_category);
                var svcSlots = slots[svc.id] || {};
                var catName  = svc.category_name || '';

                var cells = '<div class="bm-planner-sp__svc-cell">' +
                    '<div class="bm-planner-sp__svc-dot" style="background:' + catCol.solid + '"></div>' +
                    '<div class="bm-planner-sp__svc-info">' +
                        '<span class="bm-planner-sp__svc-name" data-action="svc-info" data-svc-id="' + parseInt(svc.id, 10) + '">' + sanitizeHtml(svc.service_name) + '</span>' +
                        (State.display.showCategory && catName ? '<span class="bm-planner-sp__svc-category" style="color:' + catCol.solid + '">' + sanitizeHtml(catName) + '</span>' : '') +
                        '<span class="bm-planner-sp__svc-meta">' +
                            (State.display.showDuration ? '\u23f1 ' + sanitizeHtml(formatDuration(svc.service_duration)) + ' ' : '') +
                            (State.display.showPrice    ? '\u00b7 ' + sanitizeHtml(formatPrice(svc.default_price)) : '') +
                        '</span>' +
                    '</div>' +
                '</div>';

                dates.forEach(function (d) {
                    var iso       = formatISO(d);
                    var isToday   = iso === todayISO;
                    var daySlots  = svcSlots[iso] || [];
                    var todayCls  = isToday ? ' bm-planner-sp__cell--today' : '';
                    var shown     = daySlots.slice(0, maxSlots);
                    var remaining = daySlots.length - shown.length;

                    var innerHtml = '';
                    if (!daySlots.length) {
                        innerHtml = '<span class="bm-planner-sp__no-slots"><em>No slots</em></span>';
                    } else {
                        shown.forEach(function (slot) {
                            var avail = getAvailInfo(slot.available_capacity, slot.max_capacity);
                            var priceTag = State.display.showSlotPrice ? ' <span class="bm-planner-sp__slot-price">' + sanitizeHtml(formatPrice(svc.default_price)) + '</span>' : '';
                            innerHtml += '<div class="bm-planner-sp__slot-entry"' +
                                ' data-action="slot-hover"' +
                                ' data-svc-id="' + parseInt(svc.id, 10) + '"' +
                                ' data-date="' + sanitizeHtml(iso) + '"' +
                                ' data-from="' + sanitizeHtml(slot.from) + '">' +
                                '<span class="bm-planner-sp__slot-time">' + sanitizeHtml(slot.time_display) + '</span>' +
                                '<span class="bm-planner-sp__slot-avail">' +
                                    '<span class="bm-planner-sp__avail-dot" style="color:' + avail.color + '">\u25cf</span>' +
                                    '<span class="bm-planner-sp__avail-count" title="Available">' + slot.available_capacity + '</span>' +
                                    '<span class="bm-planner-sp__booking-count" title="Bookings">\ud83d\udcdd' + slot.booking_count + '</span>' +
                                    priceTag +
                                '</span>' +
                            '</div>';
                        });
                        if (remaining > 0) {
                            innerHtml += '<span class="bm-planner-sp__slot-more">+' + remaining + ' more</span>';
                        }
                    }

                    cells += '<div class="bm-planner-sp__cell' + todayCls + '">' + innerHtml + '</div>';
                });

                rows += '<div class="bm-planner-sp__row" style="' + gridStyle + '">' + cells + '</div>';
            });
        }

        return '<div class="bm-planner-sp__grid">' +
            '<div class="bm-planner-sp__grid-head" style="' + gridStyle + '">' + headCells + '</div>' +
            '<div class="bm-planner-sp__grid-body">' + rows + '</div>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: SERVICE PLANNER DAY VIEW                                      */
    /* ===================================================================== */

    function renderDayView(dateISO) {
        var d      = new Date(dateISO + 'T00:00:00');
        var slots  = State.data.slots  || {};
        var svcs   = State.data.services || [];
        var dayAbbr = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
        var isToday = dateISO === todayISO;

        /* Compute aggregate stats */
        var totalSlots = 0;
        var totalBookings = 0;
        svcs.forEach(function (svc) {
            var daySl = (slots[svc.id] || {})[dateISO] || [];
            totalSlots += daySl.length;
            daySl.forEach(function (slot) {
                totalBookings += slot.booking_count || 0;
            });
        });

        var sections = '';
        svcs.forEach(function (svc) {
            var daySl  = (slots[svc.id] || {})[dateISO] || [];
            if (!daySl.length) { return; }
            var catCol = getCatColor(svc.service_category);

            /* Service-level stats */
            var svcBookings = 0;
            daySl.forEach(function (slot) { svcBookings += slot.booking_count || 0; });

            var cards = daySl.map(function (slot) {
                var avail = getAvailInfo(slot.available_capacity, slot.max_capacity);
                return '<div class="bm-planner-sp__day-card"' +
                    ' style="border-color:' + avail.color + ';background:' + avail.bg + '"' +
                    ' data-action="slot-click"' +
                    ' data-svc-id="' + parseInt(svc.id, 10) + '"' +
                    ' data-date="' + sanitizeHtml(dateISO) + '"' +
                    ' data-from="' + sanitizeHtml(slot.from) + '">' +
                    '<div class="bm-planner-sp__day-card-time" style="color:' + catCol.solid + '">' + sanitizeHtml(slot.time_display) + '</div>' +
                    '<div class="bm-planner-sp__day-card-avail" style="color:' + avail.color + '">\u25cf ' + slot.available_capacity + ' / ' + slot.max_capacity + '</div>' +
                    '<div class="bm-planner-sp__day-card-bookings">' + slot.booking_count + ' booking' + (slot.booking_count !== 1 ? 's' : '') + '</div>' +
                '</div>';
            }).join('');

            sections += '<div class="bm-planner-sp__day-section">' +
                '<div class="bm-planner-sp__day-section-header">' +
                    '<span class="bm-planner-sp__svc-dot" style="background:' + catCol.solid + ';width:10px;height:10px;border-radius:50%;display:inline-block"></span>' +
                    '<span class="bm-planner-sp__day-section-name">' + sanitizeHtml(svc.service_name) + '</span>' +
                    '<span class="bm-planner-sp__day-section-meta">' +
                        '\u23f1 ' + sanitizeHtml(formatDuration(svc.service_duration)) +
                        ' \u00b7 ' + sanitizeHtml(formatPrice(svc.default_price)) +
                        ' \u00b7 ' + daySl.length + ' slot' + (daySl.length !== 1 ? 's' : '') +
                        ' \u00b7 ' + svcBookings + ' booking' + (svcBookings !== 1 ? 's' : '') +
                    '</span>' +
                '</div>' +
                '<div class="bm-planner-sp__day-cards">' + cards + '</div>' +
            '</div>';
        });

        if (!sections) {
            sections = '<p class="bm-planner-sp__day-empty">No slots available on this day.</p>';
        }

        var dayBadgeCls = isToday ? ' bm-planner-sp__day-badge--today' : '';

        return '<div class="bm-planner-sp__day-view">' +
            '<div class="bm-planner-sp__day-view-header">' +
                '<button class="bmp-today-btn" data-action="back-to-week">\u2190 Week View</button>' +
                '<div class="bm-planner-sp__day-view-title">' +
                    '<span class="bm-planner-sp__day-badge' + dayBadgeCls + '">' + dayAbbr[d.getDay()] + '</span>' +
                    '<span class="bm-planner-sp__day-full">' + sanitizeHtml(formatFullDate(d)) + '</span>' +
                '</div>' +
                '<div class="bm-planner-sp__day-view-stats">' +
                    '<span class="bm-planner-sp__day-stat">' + totalSlots + ' slot' + (totalSlots !== 1 ? 's' : '') + '</span>' +
                    '<span class="bm-planner-sp__day-stat">' + totalBookings + ' booking' + (totalBookings !== 1 ? 's' : '') + '</span>' +
                '</div>' +
            '</div>' +
            sections +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: TIME PLANNER                                                  */
    /* ===================================================================== */

    function renderTimePlanner() {
        if (State._tpDayView) { return renderTimePlannerDayView(State._tpDayView.date); }

        var dates    = getVisibleDates();
        var numDays  = dates.length;
        var services = State.data.services || [];
        var slotsMap = State.data.slots    || {};
        var dayAbbr  = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
        var hours    = [];
        for (var h = GRID_START_H; h <= GRID_END_H; h++) { hours.push(h); }
        var totalHours = GRID_END_H - GRID_START_H;

        var headCells = '<div class="bm-planner-tp__time-corner"></div>';
        dates.forEach(function (d) {
            var iso    = formatISO(d);
            var isTod  = iso === todayISO;
            var todCls = isTod ? ' bm-planner-tp__day-header--today' : '';
            var numCls = isTod ? ' bm-planner-tp__day-num--today'    : '';
            headCells += '<div class="bm-planner-tp__day-header' + todCls + '">' +
                '<div class="bm-planner-tp__day-abbr">' + dayAbbr[d.getDay()] + '</div>' +
                '<div class="bm-planner-tp__day-num' + numCls + '" data-action="tp-day-click" data-date="' + sanitizeHtml(iso) + '">' + d.getDate() + '</div>' +
            '</div>';
        });

        var timeColHtml = '';
        hours.forEach(function (h) {
            var topPx = (h - GRID_START_H) * HOUR_HEIGHT;
            timeColHtml += '<div class="bm-planner-tp__time-label" style="top:' + topPx + 'px">' + pad(h) + ':00</div>';
        });

        var gridH = totalHours * HOUR_HEIGHT;

        var dayCols = '';
        dates.forEach(function (d) {
            var iso    = formatISO(d);
            var isTod  = iso === todayISO;
            var todCls = isTod ? ' bm-planner-tp__day-col--today' : '';

            var lines = '';
            hours.forEach(function (h) {
                var topPx = (h - GRID_START_H) * HOUR_HEIGHT;
                lines += '<div class="bm-planner-tp__hour-line" style="top:' + topPx + 'px"></div>';
            });

            var allCards = [];
            services.forEach(function (svc) {
                var daySl = (slotsMap[svc.id] || {})[iso] || [];
                var catCol = getCatColor(svc.service_category);
                daySl.forEach(function (slot) {
                    var fromMin = timeToMinutes(slot.from);
                    var toMin   = timeToMinutes(slot.to || slot.from);
                    if (toMin <= fromMin) { toMin = fromMin + 60; }
                    var startMin = GRID_START_H * 60;
                    if (toMin <= startMin || fromMin >= GRID_END_H * 60) { return; }
                    var top    = ((fromMin - startMin) / 60) * HOUR_HEIGHT;
                    var height = Math.max(24, ((toMin - fromMin) / 60) * HOUR_HEIGHT - 4);
                    var avail  = getAvailInfo(slot.available_capacity, slot.max_capacity);
                    allCards.push({
                        svcId:    svc.id,
                        svcName:  svc.service_name,
                        catName:  svc.category_name || '',
                        catCol:   catCol,
                        avail:    avail,
                        slot:     slot,
                        svc:      svc,
                        fromMin:  fromMin,
                        toMin:    toMin,
                        top:      top,
                        height:   height,
                        date:     iso
                    });
                });
            });

            allCards.sort(function (a, b) { return a.fromMin - b.fromMin; });
            var cols = [];
            allCards.forEach(function (card) {
                var placed = false;
                for (var ci = 0; ci < cols.length; ci++) {
                    if (cols[ci].toMin <= card.fromMin) {
                        card._col = ci;
                        cols[ci].toMin = card.toMin;
                        placed = true;
                        break;
                    }
                }
                if (!placed) {
                    card._col = cols.length;
                    cols.push({ toMin: card.toMin });
                }
            });
            var numCols = cols.length || 1;

            var cardsHtml = allCards.map(function (card) {
                var leftPct  = (card._col / numCols) * 100;
                var widthPct = (1 / numCols) * 100;
                var isCompact = card.height < 55;
                var compactCls = isCompact ? ' bm-planner-tp__card--compact' : '';
                var catBadge = card.catName ? '<span class="bm-planner-tp__card-cat" style="background:' + card.catCol.light + ';color:' + card.catCol.solid + '">' + sanitizeHtml(card.catName) + '</span>' : '';

                return '<div class="bm-planner-tp__card' + compactCls + '"' +
                    ' style="top:' + card.top.toFixed(0) + 'px;height:' + card.height.toFixed(0) + 'px;' +
                    'left:' + leftPct.toFixed(1) + '%;width:' + widthPct.toFixed(1) + '%;' +
                    'background:' + card.catCol.light + ';border-left:3px solid ' + card.catCol.solid + '"' +
                    ' data-action="slot-hover"' +
                    ' data-svc-id="' + parseInt(card.svcId, 10) + '"' +
                    ' data-date="' + sanitizeHtml(card.date) + '"' +
                    ' data-from="' + sanitizeHtml(card.slot.from) + '">' +
                    '<div class="bm-planner-tp__card-top">' +
                        '<div class="bm-planner-tp__card-name" style="color:' + card.catCol.solid + '">' + sanitizeHtml(card.svcName) + '</div>' +
                        catBadge +
                    '</div>' +
                    '<div class="bm-planner-tp__card-time">' + sanitizeHtml(card.slot.time_display) + '</div>' +
                    '<div class="bm-planner-tp__card-meta">' +
                        '<span class="bm-planner-tp__card-avail" style="color:' + card.avail.color + '">\u25cf ' + card.slot.available_capacity + '/' + card.slot.max_capacity + '</span>' +
                        '<span class="bm-planner-tp__card-price">' + sanitizeHtml(formatPrice(card.svc.default_price)) + '</span>' +
                    '</div>' +
                '</div>';
            }).join('');

            var nowLine = '';
            if (isTod) {
                var now     = new Date();
                var nowMin  = now.getHours() * 60 + now.getMinutes();
                var startMn = GRID_START_H * 60;
                if (nowMin >= startMn && nowMin <= GRID_END_H * 60) {
                    var nowTop = ((nowMin - startMn) / 60) * HOUR_HEIGHT;
                    nowLine = '<div class="bm-planner-tp__now-line" id="bm-planner-tp-now" style="top:' + nowTop.toFixed(0) + 'px"></div>';
                }
            }

            dayCols += '<div class="bm-planner-tp__day-col' + todCls + '" style="height:' + gridH + 'px;position:relative">' +
                lines + cardsHtml + nowLine +
            '</div>';
        });

        var gridStyle = 'grid-template-columns: 60px repeat(' + numDays + ', 1fr)';

        return '<div class="bm-planner-tp__grid">' +
            '<div class="bm-planner-tp__grid-head" style="' + gridStyle + '">' + headCells + '</div>' +
            '<div class="bm-planner-tp__grid-body" id="bm-planner-tp-body" style="' + gridStyle + '">' +
                '<div class="bm-planner-tp__time-col" style="height:' + gridH + 'px;position:relative">' + timeColHtml + '</div>' +
                dayCols +
            '</div>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  RENDER: TIME PLANNER DAY VIEW                                         */
    /* ===================================================================== */

    function renderTimePlannerDayView(dateISO) {
        var d        = new Date(dateISO + 'T00:00:00');
        var services = State.data.services || [];
        var slotsMap = State.data.slots    || {};
        var dayAbbr  = ['SUN','MON','TUE','WED','THU','FRI','SAT'];
        var isToday  = dateISO === todayISO;
        var hours    = [];
        for (var h = GRID_START_H; h <= GRID_END_H; h++) { hours.push(h); }
        var totalHours = GRID_END_H - GRID_START_H;
        var gridH = totalHours * HOUR_HEIGHT;

        var timeColHtml = '';
        hours.forEach(function (h) {
            var topPx = (h - GRID_START_H) * HOUR_HEIGHT;
            timeColHtml += '<div class="bm-planner-tp__time-label" style="top:' + topPx + 'px">' + pad(h) + ':00</div>';
        });

        var lines = '';
        hours.forEach(function (h) {
            var topPx = (h - GRID_START_H) * HOUR_HEIGHT;
            lines += '<div class="bm-planner-tp__hour-line" style="top:' + topPx + 'px"></div>';
        });

        var allCards = [];
        services.forEach(function (svc) {
            var daySl = (slotsMap[svc.id] || {})[dateISO] || [];
            var catCol = getCatColor(svc.service_category);
            daySl.forEach(function (slot) {
                var fromMin = timeToMinutes(slot.from);
                var toMin   = timeToMinutes(slot.to || slot.from);
                if (toMin <= fromMin) { toMin = fromMin + 60; }
                var startMin = GRID_START_H * 60;
                if (toMin <= startMin || fromMin >= GRID_END_H * 60) { return; }
                var top    = ((fromMin - startMin) / 60) * HOUR_HEIGHT;
                var height = Math.max(24, ((toMin - fromMin) / 60) * HOUR_HEIGHT - 4);
                var avail  = getAvailInfo(slot.available_capacity, slot.max_capacity);
                allCards.push({
                    svcId:    svc.id,
                    svcName:  svc.service_name,
                    catName:  svc.category_name || '',
                    catCol:   catCol,
                    avail:    avail,
                    slot:     slot,
                    svc:      svc,
                    fromMin:  fromMin,
                    toMin:    toMin,
                    top:      top,
                    height:   height,
                    date:     dateISO
                });
            });
        });

        allCards.sort(function (a, b) { return a.fromMin - b.fromMin; });
        var cols = [];
        allCards.forEach(function (card) {
            var placed = false;
            for (var ci = 0; ci < cols.length; ci++) {
                if (cols[ci].toMin <= card.fromMin) {
                    card._col = ci;
                    cols[ci].toMin = card.toMin;
                    placed = true;
                    break;
                }
            }
            if (!placed) {
                card._col = cols.length;
                cols.push({ toMin: card.toMin });
            }
        });
        var numCols = cols.length || 1;

        var cardsHtml = allCards.map(function (card) {
            var leftPct  = (card._col / numCols) * 100;
            var widthPct = (1 / numCols) * 100;
            var catBadge = card.catName ? '<span class="bm-planner-tp__card-cat" style="background:' + card.catCol.light + ';color:' + card.catCol.solid + '">' + sanitizeHtml(card.catName) + '</span>' : '';

            return '<div class="bm-planner-tp__card"' +
                ' style="top:' + card.top.toFixed(0) + 'px;height:' + card.height.toFixed(0) + 'px;' +
                'left:' + leftPct.toFixed(1) + '%;width:' + widthPct.toFixed(1) + '%;' +
                'background:' + card.catCol.light + ';border-left:3px solid ' + card.catCol.solid + '"' +
                ' data-action="slot-hover"' +
                ' data-svc-id="' + parseInt(card.svcId, 10) + '"' +
                ' data-date="' + sanitizeHtml(card.date) + '"' +
                ' data-from="' + sanitizeHtml(card.slot.from) + '">' +
                '<div class="bm-planner-tp__card-top">' +
                    '<div class="bm-planner-tp__card-name" style="color:' + card.catCol.solid + '">' + sanitizeHtml(card.svcName) + '</div>' +
                    catBadge +
                '</div>' +
                '<div class="bm-planner-tp__card-time">' + sanitizeHtml(card.slot.time_display) + '</div>' +
                '<div class="bm-planner-tp__card-meta">' +
                    '<span class="bm-planner-tp__card-avail" style="color:' + card.avail.color + '">\u25cf ' + card.slot.available_capacity + '/' + card.slot.max_capacity + '</span>' +
                    '<span class="bm-planner-tp__card-price">' + sanitizeHtml(formatPrice(card.svc.default_price)) + '</span>' +
                '</div>' +
            '</div>';
        }).join('');

        var nowLine = '';
        if (isToday) {
            var now     = new Date();
            var nowMin  = now.getHours() * 60 + now.getMinutes();
            var startMn = GRID_START_H * 60;
            if (nowMin >= startMn && nowMin <= GRID_END_H * 60) {
                var nowTop = ((nowMin - startMn) / 60) * HOUR_HEIGHT;
                nowLine = '<div class="bm-planner-tp__now-line" id="bm-planner-tp-now" style="top:' + nowTop.toFixed(0) + 'px"></div>';
            }
        }

        var dayBadgeCls = isToday ? ' bm-planner-sp__day-badge--today' : '';

        return '<div class="bm-planner-tp__day-view-wrap">' +
            '<div class="bm-planner-tp__day-view-header">' +
                '<button class="bmp-today-btn" data-action="tp-back-to-week">\u2190 Week View</button>' +
                '<div class="bm-planner-sp__day-view-title">' +
                    '<span class="bm-planner-sp__day-badge' + dayBadgeCls + '">' + dayAbbr[d.getDay()] + '</span>' +
                    '<span class="bm-planner-sp__day-full">' + sanitizeHtml(formatFullDate(d)) + '</span>' +
                '</div>' +
            '</div>' +
            '<div class="bm-planner-tp__day-view-grid">' +
                '<div class="bm-planner-tp__day-view-body" id="bm-planner-tp-body">' +
                    '<div class="bm-planner-tp__time-col" style="height:' + gridH + 'px;position:relative">' + timeColHtml + '</div>' +
                    '<div class="bm-planner-tp__day-col" style="height:' + gridH + 'px;position:relative;flex:1">' +
                        lines + cardsHtml + nowLine +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  MAIN RENDER                                                           */
    /* ===================================================================== */

    function render() {
        var html = '';

        if (State.loading) {
            var frame = State.view === VIEWS.HOME ? '' : (renderNav() + renderToolbar());
            html = frame + '<div class="bm-planner-loading"><div class="bm-planner-spinner"></div><span>Loading\u2026</span></div>';
        } else if (State.view === VIEWS.HOME) {
            html = renderHome();
        } else if (State.view === VIEWS.SERVICE) {
            html = renderNav() + renderToolbar() + renderServicePlanner() + renderFooter();
        } else if (State.view === VIEWS.TIME) {
            html = renderNav() + renderToolbar() + renderTimePlanner() + renderFooter();
        }

        var $body    = $root.find('.bm-planner-sp__grid-body, #bm-planner-tp-body');
        var scrollTop = $body.length ? $body.scrollTop() : 0;

        $root.html(html);

        if (scrollTop) {
            $root.find('.bm-planner-sp__grid-body, #bm-planner-tp-body').scrollTop(scrollTop);
        }

        if (State.view === VIEWS.TIME && State._autoHide) {
            scrollToNow();
        }

        updateUrlHash();
    }

    function scrollToNow() {
        var $body = $root.find('#bm-planner-tp-body');
        var $line = $root.find('#bm-planner-tp-now');
        if ($body.length && $line.length) {
            var targetTop = $line.position().top + $body.scrollTop() - 200;
            $body.animate({ scrollTop: Math.max(0, targetTop) }, 300);
        }
    }

    /* ===================================================================== */
    /*  URL ROUTING                                                           */
    /* ===================================================================== */

    function updateUrlHash() {
        var hash = '';
        if (State.view === VIEWS.SERVICE) { hash = '#/service-planner'; }
        else if (State.view === VIEWS.TIME) { hash = '#/time-planner'; }
        try {
            if (window.location.hash !== hash) {
                window.history.replaceState(null, '', window.location.pathname + window.location.search + hash);
            }
        } catch (e) { /* ignore */ }
    }

    function readUrlHash() {
        var hash = window.location.hash || '';
        if (hash.indexOf('service-planner') !== -1) { return VIEWS.SERVICE; }
        if (hash.indexOf('time-planner') !== -1)    { return VIEWS.TIME; }
        return null;
    }

    /* ===================================================================== */
    /*  SERVICE INFO MODAL                                                    */
    /* ===================================================================== */

    function openServiceInfoModal(svcId) {
        var svc = null;
        (State.data.services || []).forEach(function (s) {
            if (parseInt(s.id, 10) === parseInt(svcId, 10)) { svc = s; }
        });
        if (!svc) { return; }

        var catCol = getCatColor(svc.service_category);
        var catName = svc.category_name || 'Uncategorized';
        var svcType = svc.service_type || 'entries';

        var bodyHtml =
            '<div class="bmp-modal-header">' +
                '<h3 class="bmp-modal-title">' + sanitizeHtml(svc.service_name) + '</h3>' +
                '<button class="bmp-modal-close" data-action="close-modal">&times;</button>' +
            '</div>' +
            '<div class="bmp-modal-body">' +
                '<div class="bm-planner-svc-info-grid">' +
                    '<div class="bm-planner-svc-info-card">' +
                        '<div class="bm-planner-svc-info-card__icon" style="background:' + catCol.light + ';color:' + catCol.solid + '">' +
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 7h16M4 12h16M4 17h10"/></svg>' +
                        '</div>' +
                        '<div class="bm-planner-svc-info-card__label">Category</div>' +
                        '<div class="bm-planner-svc-info-card__value" style="color:' + catCol.solid + '">' + sanitizeHtml(catName) + '</div>' +
                    '</div>' +
                    '<div class="bm-planner-svc-info-card">' +
                        '<div class="bm-planner-svc-info-card__icon" style="background:#EFF6FF;color:#2563EB">' +
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>' +
                        '</div>' +
                        '<div class="bm-planner-svc-info-card__label">Duration</div>' +
                        '<div class="bm-planner-svc-info-card__value">' + sanitizeHtml(formatDuration(svc.service_duration)) + '</div>' +
                    '</div>' +
                    '<div class="bm-planner-svc-info-card">' +
                        '<div class="bm-planner-svc-info-card__icon" style="background:#F0FDF4;color:#16A34A">' +
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>' +
                        '</div>' +
                        '<div class="bm-planner-svc-info-card__label">Price</div>' +
                        '<div class="bm-planner-svc-info-card__value">' + sanitizeHtml(formatPrice(svc.default_price)) + '</div>' +
                    '</div>' +
                    '<div class="bm-planner-svc-info-card">' +
                        '<div class="bm-planner-svc-info-card__icon" style="background:#FFF7ED;color:#EA580C">' +
                            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>' +
                        '</div>' +
                        '<div class="bm-planner-svc-info-card__label">Type</div>' +
                        '<div class="bm-planner-svc-info-card__value">' + sanitizeHtml(svcType.charAt(0).toUpperCase() + svcType.slice(1)) + '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        openModal(bodyHtml);
    }

    /* ===================================================================== */
    /*  BOOKING DETAIL MODAL                                                  */
    /* ===================================================================== */

    function openSlotDetailModal(svcId, date, timeSlot) {
        var svc = null;
        (State.data.services || []).forEach(function (s) {
            if (parseInt(s.id, 10) === parseInt(svcId, 10)) { svc = s; }
        });

        var loadingHtml =
            '<div class="bmp-modal-header">' +
                '<h3 class="bmp-modal-title">' + sanitizeHtml(svc ? svc.service_name : 'Loading\u2026') + '</h3>' +
                '<button class="bmp-modal-close" data-action="close-modal">&times;</button>' +
            '</div>' +
            '<div class="bmp-modal-body"><div class="bm-planner-loading"><div class="bm-planner-spinner"></div><span>Loading bookings\u2026</span></div></div>';

        openModal(loadingHtml);

        API.fetchSlotBookings(svcId, date, timeSlot).then(function (res) {
            if (!$currentModal) { return; }
            var avail = getAvailInfo(res.available_capacity, res.max_capacity);
            var d     = new Date(date + 'T00:00:00');
            var slotData   = (State.data.slots[svcId] || {})[date] || [];
            var matchSlot  = null;
            slotData.forEach(function (sl) { if (sl.from === timeSlot) { matchSlot = sl; } });

            var infoRow =
                '<div class="bm-planner-detail__info-row">' +
                    detailInfoCol('Date',         formatFullDate(d)) +
                    detailInfoCol('Time',         sanitizeHtml(matchSlot ? matchSlot.time_display : timeSlot)) +
                    detailInfoCol('Price',        sanitizeHtml(formatPrice(res.service_price))) +
                    detailInfoCol('Availability', res.available_capacity + '/' + res.max_capacity + ' spots <span class="bm-planner-detail__info-badge" style="background:' + avail.bg + ';color:' + avail.color + '">' + (avail.label.split(' ')[0]) + '</span>') +
                '</div>';

            var tableHead =
                '<thead><tr>' +
                    '<th>Order Reference</th><th>Last Name</th><th>Participants</th>' +
                    '<th>Extra Participants</th><th>Booking Status</th><th>Payment Status</th><th>Total</th>' +
                '</tr></thead>';

            var tableBody = '<tbody>';
            if (!res.bookings || !res.bookings.length) {
                tableBody += '<tr><td colspan="7" style="text-align:center;color:#6B7280;padding:24px">No bookings for this slot.</td></tr>';
            } else {
                res.bookings.forEach(function (b) {
                    var oSt    = sanitizeHtml(b.order_status   || '');
                    var pSt    = sanitizeHtml(b.payment_status || '');
                    var oStCls = 'bm-planner-booking-status--' + (b.order_status || 'pending').toLowerCase().replace(/\s+/g, '-');
                    var pStCls = 'bm-planner-payment-status--' + (b.payment_status || 'unpaid').toLowerCase().replace(/\s+/g, '-');
                    tableBody += '<tr>' +
                        '<td><a href="#" class="bm-planner-order-ref">' + sanitizeHtml(b.order_ref || '') + ' \u2197</a></td>' +
                        '<td>' + sanitizeHtml(b.customer_last_name || '') + '</td>' +
                        '<td>' + parseInt(b.total_svc_slots, 10) + '</td>' +
                        '<td>' + parseInt(b.extra_participants, 10) + '</td>' +
                        '<td><span class="' + oStCls + '">' + oSt + '</span></td>' +
                        '<td><span class="' + pStCls + '">' + pSt + '</span></td>' +
                        '<td>' + sanitizeHtml(formatPrice(b.total_cost)) + '</td>' +
                    '</tr>';
                });
            }
            tableBody += '</tbody>';

            var bodyHtml =
                '<div class="bmp-modal-header">' +
                    '<h3 class="bmp-modal-title">' + sanitizeHtml(res.service_name || '') + '</h3>' +
                    '<button class="bmp-modal-close" data-action="close-modal">&times;</button>' +
                '</div>' +
                '<div class="bmp-modal-body">' +
                    infoRow +
                    '<table class="bm-planner-bookings-table">' + tableHead + tableBody + '</table>' +
                '</div>';

            $currentModal.find('.bmp-modal').html(bodyHtml);
        }).fail(function () {
            if (!$currentModal) { return; }
            $currentModal.find('.bmp-modal-body').html('<p style="color:#DC2626;padding:16px">Failed to load booking details.</p>');
        });
    }

    function detailInfoCol(label, valueHtml) {
        return '<div class="bm-planner-detail__info-col">' +
            '<span class="bm-planner-detail__info-label">' + sanitizeHtml(label) + '</span>' +
            '<span class="bm-planner-detail__info-value">' + valueHtml + '</span>' +
        '</div>';
    }

    /* ===================================================================== */
    /*  TOOLTIP DATA BUILDER                                                  */
    /* ===================================================================== */

    function buildTooltipData(svcId, date, slotFrom) {
        var svc  = null;
        (State.data.services || []).forEach(function (s) {
            if (parseInt(s.id, 10) === parseInt(svcId, 10)) { svc = s; }
        });
        if (!svc) { return null; }

        var daySl = (State.data.slots[svcId] || {})[date] || [];
        var slot  = null;
        daySl.forEach(function (s) { if (s.from === slotFrom) { slot = s; } });
        if (!slot) { return null; }

        return {
            service_id:          svc.id,
            service_name:        svc.service_name,
            service_duration:    svc.service_duration,
            service_price:       svc.default_price,
            service_category:    svc.service_category,
            category_name:       svc.category_name || '',
            date:                date,
            slot_from:           slotFrom,
            time_display:        slot.time_display,
            max_capacity:        slot.max_capacity,
            available_capacity:  slot.available_capacity,
            booking_count:       slot.booking_count
        };
    }

    /* ===================================================================== */
    /*  NAVIGATION HELPERS                                                    */
    /* ===================================================================== */

    function navigateToView(view) {
        try { localStorage.setItem('bm_planner_last_view', view); } catch (e) {}
        if (view === State.view) { return; }
        setState({ view: view, _dayView: null, _tpDayView: null, _displayOpen: false });
        if (view !== VIEWS.HOME) { loadWeekData(); }
    }

    function navigateWeek(delta) {
        var daysDelta = State.display.days || 7;
        setState({ weekStart: addDays(State.weekStart, delta * daysDelta), _dayView: null, _tpDayView: null });
        loadWeekData();
    }

    /* ===================================================================== */
    /*  EVENT HANDLING                                                        */
    /* ===================================================================== */

    $root.on('click', '[data-view]', function () {
        navigateToView($(this).data('view'));
    });

    $root.on('click', '[data-nav]', function () {
        navigateToView($(this).data('nav'));
    });

    $root.on('click', '[data-week]', function (e) {
        e.stopPropagation();
        navigateWeek(parseInt($(this).data('week'), 10));
    });

    $root.on('click', '[data-action]', function (e) {
        var action = $(this).data('action');

        if (action === 'today') {
            setState({ weekStart: getMondayOfWeek(today), _dayView: null, _tpDayView: null });
            loadWeekData();
        } else if (action === 'ora') {
            scrollToNow();
        } else if (action === 'auto') {
            setState({ _autoHide: !State._autoHide });
            if (State._autoHide) { scrollToNow(); }
        } else if (action === 'display') {
            e.stopPropagation();
            setState({ _displayOpen: !State._displayOpen });
        } else if (action === 'day-click') {
            setState({ _dayView: { date: $(this).data('date') } });
        } else if (action === 'tp-day-click') {
            setState({ _tpDayView: { date: $(this).data('date') } });
        } else if (action === 'back-to-week') {
            setState({ _dayView: null });
        } else if (action === 'tp-back-to-week') {
            setState({ _tpDayView: null });
        } else if (action === 'close-modal') {
            closeModal();
        } else if (action === 'slot-click') {
            var $el  = $(this);
            openSlotDetailModal($el.data('svc-id'), $el.data('date'), $el.data('from'));
        } else if (action === 'view-details') {
            e.preventDefault();
            var $el2 = $(this);
            Tooltip.hide();
            openSlotDetailModal($el2.data('svc-id'), $el2.data('date'), $el2.data('from'));
        } else if (action === 'svc-info') {
            openServiceInfoModal($(this).data('svc-id'));
        }
    });

    $root.on('mouseenter', '[data-action="slot-hover"]', function (e) {
        Tooltip.cancelHide();
        var $el  = $(this);
        var data = buildTooltipData($el.data('svc-id'), $el.data('date'), $el.data('from'));
        if (data) { Tooltip.show(data, e.clientX, e.clientY); }
    });
    $root.on('mousemove', '[data-action="slot-hover"]', function (e) {
        Tooltip.position(e.clientX, e.clientY);
    });
    $root.on('mouseleave', '[data-action="slot-hover"]', function () {
        Tooltip.scheduleHide(300);
    });
    $('body').on('mouseenter', '.bm-planner-tooltip', function () { Tooltip.cancelHide(); });
    $('body').on('mouseleave', '.bm-planner-tooltip', function () { Tooltip.scheduleHide(200); });

    $root.on('change', '[data-filter]', function () {
        var key = $(this).data('filter');
        var val = parseInt($(this).val(), 10) || 0;
        var f   = $.extend({}, State.filter);
        f[key]  = val;
        setState({ filter: f });
        loadWeekData();
    });

    $root.on('change', '[data-disp]', function () {
        var key = $(this).data('disp');
        var val = $(this).is(':checkbox') ? $(this).prop('checked') : $(this).val();
        var d   = $.extend({}, State.display);
        d[key]  = val;
        setState({ display: d });
    });
    $root.on('input', '[data-disp-range]', function () {
        var key = $(this).data('disp-range');
        var d   = $.extend({}, State.display);
        d[key]  = parseInt($(this).val(), 10);
        setState({ display: d });
        if (key === 'days') {
            loadWeekData();
        }
    });

    $(document).on('click.bm-planner-display', function (e) {
        if (State._displayOpen && !$(e.target).closest('.bm-planner-display-panel, [data-action="display"]').length) {
            setState({ _displayOpen: false });
        }
    });

    $(document).on('keydown.bm-planner', function (e) {
        if (e.key === 'Escape') {
            closeModal();
            Tooltip.hide();
            if (State._displayOpen) { setState({ _displayOpen: false }); }
        }
    });

    setInterval(function () {
        if (State.view === VIEWS.TIME) {
            var now    = new Date();
            var nowMin = now.getHours() * 60 + now.getMinutes();
            var startMn = GRID_START_H * 60;
            var $line  = $root.find('#bm-planner-tp-now');
            if ($line.length) {
                var nowTop = ((nowMin - startMn) / 60) * HOUR_HEIGHT;
                $line.css('top', nowTop.toFixed(0) + 'px');
            }
        }
    }, 60000);

    /* ===================================================================== */
    /*  BOOT                                                                  */
    /* ===================================================================== */

    (function boot() {
        /* Priority: data attribute > URL hash > localStorage */
        var dataView = $root.data('initial-view') || '';
        var hashView = readUrlHash();
        var lastView;
        try { lastView = localStorage.getItem('bm_planner_last_view'); } catch (e) {}

        var initialView = dataView || hashView || lastView;
        if (initialView === VIEWS.SERVICE || initialView === VIEWS.TIME) {
            State.view = initialView;
            render();
            loadWeekData();
        } else {
            render();
        }
    })();

})(jQuery);
