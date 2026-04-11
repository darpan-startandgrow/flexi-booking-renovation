// ============================================
// Service Booking - Analytics Module (FINAL)
// ============================================

// ------------------------------------------------------------
// 1. GLOBAL VARIABLES & STATE
// ------------------------------------------------------------
var analyticsCharts = {
    netSalesChart: null,
    ordersChart: null,
    revenueTrendsChart: null,
    itemsSoldChart: null,
    detailChart: null,
    ordersTrendChart: null
};

var detailState = {
    metric: '',
    filters: {},
    orderColumn: 0,
    orderDir: 'desc'
};
var detailTable = null;

var revenueDataTable = null;
var productsDataTable = null;
var categoriesDataTable = null;
var servicesDataTable = null;
var ordersDataTable = null;

// ============================================
// BEAUTIFUL BUTTON GROUPS CONFIGURATION
// ============================================
var buttonGroups = {
    copyGroup: {
        extend: 'collection',
        text: '<i class="fa-regular fa-copy"></i> Copy',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fa-regular fa-copy"></i> Current Page',
                exportOptions: { modifier: { page: 'current' } }
            },
            {
                extend: 'copy',
                text: '<i class="fa-solid fa-copy"></i> All Records',
                exportOptions: { modifier: { page: 'all' } }
            },
            {
                extend: 'copy',
                text: '<i class="fa-regular fa-square-check"></i> Selected',
                exportOptions: { modifier: { selected: true } }
            }
        ]
    },
    csvGroup: {
        extend: 'collection',
        text: '<i class="fa-solid fa-file-csv"></i> CSV',
        buttons: [
            {
                extend: 'csv',
                text: '<i class="fa-regular fa-file"></i> Current Page',
                exportOptions: { modifier: { page: 'current' } }
            },
            {
                extend: 'csv',
                text: '<i class="fa-solid fa-file"></i> All Records',
                exportOptions: { modifier: { page: 'all' } }
            },
            {
                extend: 'csv',
                text: '<i class="fa-regular fa-square-check"></i> Selected',
                exportOptions: { modifier: { selected: true } }
            }
        ]
    },
    excelGroup: {
        extend: 'collection',
        text: '<i class="fa-solid fa-file-excel"></i> Excel',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa-regular fa-file-excel"></i> Current Page',
                exportOptions: { modifier: { page: 'current' } }
            },
            {
                extend: 'excel',
                text: '<i class="fa-solid fa-file-excel"></i> All Records',
                exportOptions: { modifier: { page: 'all' } }
            },
            {
                extend: 'excel',
                text: '<i class="fa-regular fa-square-check"></i> Selected',
                exportOptions: { modifier: { selected: true } }
            }
        ]
    },
    pdfGroup: {
        extend: 'collection',
        text: '<i class="fa-solid fa-file-pdf"></i> PDF',
        buttons: [
            {
                extend: 'pdf',
                text: '<i class="fa-regular fa-file-pdf"></i> Current Page',
                exportOptions: { modifier: { page: 'current' } }
            },
            {
                extend: 'pdf',
                text: '<i class="fa-solid fa-file-pdf"></i> All Records',
                exportOptions: { modifier: { page: 'all' } }
            },
            {
                extend: 'pdf',
                text: '<i class="fa-regular fa-square-check"></i> Selected',
                exportOptions: { modifier: { selected: true } }
            }
        ]
    },
    printGroup: {
        extend: 'collection',
        text: '<i class="fa-solid fa-print"></i> Print',
        buttons: [
            {
                extend: 'print',
                text: '<i class="fa-regular fa-print"></i> Current Page',
                exportOptions: { modifier: { page: 'current' } }
            },
            {
                extend: 'print',
                text: '<i class="fa-solid fa-print"></i> All Records',
                exportOptions: { modifier: { page: 'all' } }
            },
            {
                extend: 'print',
                text: '<i class="fa-regular fa-square-check"></i> Selected',
                exportOptions: { modifier: { selected: true } }
            }
        ]
    },
    colvisGroup: {
        extend: 'colvis',
        text: '<i class="fa-solid fa-table-columns"></i> Columns'
    }
};

// ------------------------------------------------------------
// 2. CORE FUNCTIONS
// ------------------------------------------------------------
function bm_open_analytics_tabs(evt, tabName) {
    jQuery('.analytics-tablink').removeClass('tab-active');
    jQuery(evt).addClass('tab-active');
    jQuery('.tabcontent').removeClass('active').hide();
    jQuery('#' + tabName).addClass('active').show();
    jQuery('#current_analytics_tab').val(tabName);

    if (tabName !== 'analytics-revenue' && revenueDataTable) {
        revenueDataTable.destroy();
        revenueDataTable = null;
    }
    if (tabName !== 'analytics-products' && productsDataTable) {
        productsDataTable.destroy();
        productsDataTable = null;
    }
    if (tabName !== 'analytics-orders' && ordersDataTable) {
        ordersDataTable.destroy();
        ordersDataTable = null;
    }

    switch (tabName) {
        case 'analytics-overview': bm_load_overview_data(); break;
        case 'analytics-revenue':  bm_load_revenue_data();  break;
        case 'analytics-products': bm_load_products_data(); break;
        case 'analytics-orders':   bm_load_orders_data();   break;
        default: bm_load_overview_data(); break;
    }
}

function bm_safe_destroy_table(selector) {
    if (jQuery.fn.DataTable.isDataTable(selector)) {
        try {
            jQuery(selector).DataTable().destroy();
        } catch (e) { 
            console.warn('Table destroy warning for ' + selector, e); 
        }
    }
}

// ------------------------------------------------------------
// 3. DATE & FILTER LOGIC
// ------------------------------------------------------------
function bm_change_analytics_period(period) {
    var today = new Date();
    var dateFrom, dateTo, compareFrom, compareTo;
    var compareType = jQuery('input[name="compare_type"]:checked').val() || 'period';
    jQuery('#analytics_compare_type').val(compareType);
    var compareEnabled = jQuery('#enable_compare').is(':checked');

    var setDates = function(start, end) {
        dateFrom = formatDate(start);
        dateTo = formatDate(end);
        
        if (compareEnabled) {
            if (compareType === 'year') {
                var compStart = new Date(start); compStart.setFullYear(start.getFullYear() - 1);
                var compEnd = new Date(end); compEnd.setFullYear(end.getFullYear() - 1);
                compareFrom = formatDate(compStart);
                compareTo = formatDate(compEnd);
            } else {
                var duration = end.getTime() - start.getTime();
                var compEnd = new Date(start.getTime() - (24*60*60*1000));
                var compStart = new Date(compEnd.getTime() - duration);
                compareFrom = formatDate(compStart);
                compareTo = formatDate(compEnd);
            }
        } else {
            compareFrom = '';
            compareTo = '';
        }
    };

    switch (period) {
        case 'today': setDates(today, today); break;
        case 'yesterday': var y = new Date(today); y.setDate(today.getDate()-1); setDates(y, y); break;
        case 'this_week': 
            var s = new Date(today); s.setDate(today.getDate()-today.getDay()); 
            var e = new Date(s); e.setDate(s.getDate()+6); 
            setDates(s, e); break;
        case 'last_week': 
            var s = new Date(today); s.setDate(today.getDate()-today.getDay()-7); 
            var e = new Date(s); e.setDate(s.getDate()+6); 
            setDates(s, e); break;
        case 'this_month': 
            setDates(new Date(today.getFullYear(), today.getMonth(), 1), new Date(today.getFullYear(), today.getMonth()+1, 0)); break;
        case 'last_month': 
            setDates(new Date(today.getFullYear(), today.getMonth()-1, 1), new Date(today.getFullYear(), today.getMonth(), 0)); break;
        case 'this_year': 
            setDates(new Date(today.getFullYear(), 0, 1), new Date(today.getFullYear(), 11, 31)); break;
        case 'last_year': 
            setDates(new Date(today.getFullYear()-1, 0, 1), new Date(today.getFullYear()-1, 11, 31)); break;
        case 'custom': 
            bm_load_analytics_data(); return;
        default: return;
    }

    jQuery('#analytics_date_from').val(dateFrom);
    jQuery('#analytics_date_to').val(dateTo);
    jQuery('#analytics_compare_from').val(compareFrom);
    jQuery('#analytics_compare_to').val(compareTo);

    bm_load_analytics_data();
}

function formatDate(date) {
    var d = date.getDate();
    var m = date.getMonth() + 1;
    var y = date.getFullYear();
    return (d<10?'0'+d:d) + '/' + (m<10?'0'+m:m) + '/' + y;
}

function formatDateRange(dateFrom, dateTo) {
    if (!dateFrom || !dateTo) return '';
    var fromParts = dateFrom.split('/');
    var toParts = dateTo.split('/');
    if (fromParts.length !== 3 || toParts.length !== 3) return '';
    var fromDate = new Date(fromParts[2], fromParts[1]-1, fromParts[0]);
    var toDate = new Date(toParts[2], toParts[1]-1, toParts[0]);
    if (isNaN(fromDate) || isNaN(toDate)) return '';
    var fromMonth = fromDate.toLocaleString('default', { month: 'short' });
    var toMonth = toDate.toLocaleString('default', { month: 'short' });
    var fromYear = fromDate.getFullYear();
    var toYear = toDate.getFullYear();
    if (fromMonth === toMonth && fromYear === toYear) {
        return fromMonth + ' ' + fromDate.getDate() + '-' + toDate.getDate() + ', ' + fromYear;
    } else if (fromYear === toYear) {
        return fromMonth + ' ' + fromDate.getDate() + ' - ' + toMonth + ' ' + toDate.getDate() + ', ' + fromYear;
    } else {
        return fromDate.toLocaleDateString() + ' - ' + toDate.toLocaleDateString();
    }
}

function bm_load_analytics_data() {
    var currentTab = jQuery('#current_analytics_tab').val();
    switch (currentTab) {
        case 'analytics-revenue': bm_load_revenue_data(); break;
        case 'analytics-products': bm_load_products_data(); break;
        case 'analytics-orders':   bm_load_orders_data(); break;
        default: bm_load_overview_data(); break;
    }
}

function get_common_post_data(action_type) {
    return {
        'date_from': jQuery('#analytics_date_from').val(),
        'date_to': jQuery('#analytics_date_to').val(),
        'compare_from': jQuery('#analytics_compare_from').val(),
        'compare_to': jQuery('#analytics_compare_to').val(),
        'compare_type': jQuery('#analytics_compare_type').val(),
        'category_id': jQuery('#product_category_filter').val(),
        'service_id': jQuery('#product_service_filter').val(),
        'action_type': action_type
    };
}

// ------------------------------------------------------------
// 4. DATA LOADING (AJAX)
// ------------------------------------------------------------
function bm_load_overview_data() {
    jQuery('.loader_modal').show();
    jQuery.post(bm_ajax_object.ajax_url, { 
        'action': 'bm_fetch_analytics_data', 
        'post': get_common_post_data('overview'), 
        'nonce': bm_ajax_object.nonce 
    }, function(res) {
        jQuery('.loader_modal').hide();
        var data = (typeof res === 'string') ? JSON.parse(res) : res;
        if (data.status) {
            updateOverviewMetrics(data);
            updateOverviewCharts(data);
            updateLeaderboards(data);
        }
    });
}

function bm_load_revenue_data() {
    jQuery('.loader_modal').show();
    jQuery.post(bm_ajax_object.ajax_url, { 
        'action': 'bm_fetch_analytics_data', 
        'post': get_common_post_data('revenue'), 
        'nonce': bm_ajax_object.nonce 
    }, function(res) {
        jQuery('.loader_modal').hide();
        var data = (typeof res === 'string') ? JSON.parse(res) : res;
        if (data.status) {
            updateRevenueMetrics(data);
            updateRevenueChart(data);
            updateRevenueTable(data);
        }
    });
}

function bm_load_products_data() {
    jQuery('.loader_modal').show();
    jQuery.post(bm_ajax_object.ajax_url, { 
        'action': 'bm_fetch_analytics_data', 
        'post': get_common_post_data('products'), 
        'nonce': bm_ajax_object.nonce 
    }, function(res) {
        jQuery('.loader_modal').hide();
        var data = (typeof res === 'string') ? JSON.parse(res) : res;
        if (data.status) {
            updateProductsMetrics(data);
            updateProductsChart(data);
            updateProductsTable(data);
        }
    });
}

// ------------------------------------------------------------
// 5. UPDATE UI (Metrics, Charts, Tables)
// ------------------------------------------------------------
function formatCurrency(val, sym, pos) {
    var num = parseFloat(val) || 0;
    var str = num.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
    return pos === 'before' ? sym + str : str + sym;
}

function updateMetric(elementId, value, change, sym = '', pos = 'before', compareType = 'period', showComparison = true) {
    var formattedVal = sym ? (pos === 'before' ? sym + changePriceFormat(value) : changePriceFormat(value) + sym) : value;
    jQuery('#' + elementId).text(formattedVal);
    
    var changeEl = jQuery('#' + elementId + '_change .change-percent');
    var labelEl = jQuery('#' + elementId + '_change .change-label');
    
    if (showComparison) {
        changeEl.text((change >= 0 ? '+' : '') + change + '%')
                .removeClass('positive negative')
                .addClass(change >= 0 ? 'positive' : 'negative');
        labelEl.text(compareType === 'year' ? 'vs previous year' : 'vs previous period');
    } else {
        changeEl.text('');
        labelEl.text('');
    }
}

function changePriceFormat(price) {
    return parseFloat(price || 0).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// --- OVERVIEW ---
function updateOverviewMetrics(data) {
    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();
    var compareType = jQuery('#analytics_compare_type').val();
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    updateMetric('total_sales', data.total_sales, data.total_sales_change, sym, pos, compareType, compareEnabled);
    updateMetric('net_sales', data.net_sales, data.net_sales_change, sym, pos, compareType, compareEnabled);
    updateMetric('total_orders', data.total_orders, data.total_orders_change, '', '', compareType, compareEnabled);
    updateMetric('services_sold', data.services_sold, data.services_sold_change, '', '', compareType, compareEnabled);
    updateMetric('extra_services_sold', data.extra_services_sold, data.extra_services_sold_change, '', '', compareType, compareEnabled);
}

function updateOverviewCharts(data) {
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    var currentRange = formatDateRange(jQuery('#analytics_date_from').val(), jQuery('#analytics_date_to').val());
    var compareRange = formatDateRange(jQuery('#analytics_compare_from').val(), jQuery('#analytics_compare_to').val());

    if (analyticsCharts.netSalesChart) analyticsCharts.netSalesChart.destroy();
    if (document.getElementById('net_sales_chart')) {
        var datasets = [{
            label: currentRange,
            data: data.current_net_sales_data,
            borderColor: '#2271b1',
            backgroundColor: 'rgba(34,113,177,0.1)',
            fill: true,
            tension: 0.4
        }];
        if (compareEnabled) {
            datasets.push({
                label: compareRange,
                data: data.previous_net_sales_data,
                borderColor: '#8c8f94',
                backgroundColor: 'rgba(140,143,148,0.1)',
                borderDash: [5,5],
                fill: true,
                tension: 0.4
            });
        }
        analyticsCharts.netSalesChart = new Chart(document.getElementById('net_sales_chart'), {
            type: 'line',
            data: { labels: data.chart_labels, datasets: datasets },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } } }
        });
    }
    
    if (analyticsCharts.ordersChart) analyticsCharts.ordersChart.destroy();
    if (document.getElementById('orders_chart')) {
        var datasetsOrders = [{
            label: currentRange,
            data: data.current_orders_data,
            borderColor: '#00a32a',
            backgroundColor: 'rgba(0,163,42,0.1)',
            fill: true,
            tension: 0.4
        }];
        if (compareEnabled) {
            datasetsOrders.push({
                label: compareRange,
                data: data.previous_orders_data,
                borderColor: '#8c8f94',
                backgroundColor: 'rgba(140,143,148,0.1)',
                borderDash: [5,5],
                fill: true,
                tension: 0.4
            });
        }
        analyticsCharts.ordersChart = new Chart(document.getElementById('orders_chart'), {
            type: 'line',
            data: { labels: data.chart_labels, datasets: datasetsOrders },
            options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } } }
        });
    }
}

function updateLeaderboards(data) {
    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();

    bm_safe_destroy_table('#top_categories_table');
    var html = '';
    if(data.top_categories && data.top_categories.length > 0) {
        data.top_categories.forEach(function(r, i){ html += `<tr><td>${i+1}</td><td>${r.name}</td><td>${r.services_sold}</td><td>${formatCurrency(r.net_sales, sym, pos)}</td></tr>`; });
    } else { html = '<tr><td colspan="4" class="text-center">No data available</td></tr>'; }
    jQuery('#top_categories_body').html(html);

    bm_safe_destroy_table('#top_services_table');
    var htmlS = '';
    if(data.top_services && data.top_services.length > 0) {
        data.top_services.forEach(function(r, i){ htmlS += `<tr><td>${i+1}</td><td>${r.name}</td><td>${r.services_sold}</td><td>${formatCurrency(r.net_sales, sym, pos)}</td></tr>`; });
    } else { htmlS = '<tr><td colspan="4" class="text-center">No data available</td></tr>'; }
    jQuery('#top_services_body').html(htmlS);
}

// --- REVENUE ---
function updateRevenueMetrics(data) {
    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();
    var compareType = jQuery('#analytics_compare_type').val();
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    updateMetric('gross_sales', data.gross_sales, data.gross_sales_change, sym, pos, compareType, compareEnabled);
    updateMetric('returns', data.returns, data.returns_change, sym, pos, compareType, compareEnabled);
    updateMetric('coupons', data.coupons, data.coupons_change, sym, pos, compareType, compareEnabled);
    updateMetric('revenue_net_sales', data.net_sales, data.net_sales_change, sym, pos, compareType, compareEnabled);
}

function updateRevenueChart(data) {
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    var currentRange = formatDateRange(jQuery('#analytics_date_from').val(), jQuery('#analytics_date_to').val());
    var compareRange = formatDateRange(jQuery('#analytics_compare_from').val(), jQuery('#analytics_compare_to').val());

    if (analyticsCharts.revenueTrendsChart) analyticsCharts.revenueTrendsChart.destroy();
    if (!document.getElementById('revenue_trends_chart')) return;
    var ctx = document.getElementById('revenue_trends_chart').getContext('2d');
    
    var datasets = [
        { label: 'Gross Sales (' + currentRange + ')', data: data.gross_sales_data, borderColor: '#00a32a', fill: false },
        { label: 'Net Sales (' + currentRange + ')', data: data.net_sales_data, borderColor: '#2271b1', fill: false }
    ];
    // If you have previous period data for revenue, you can add them conditionally here.
    // Currently the PHP returns only current period for revenue chart.
    
    analyticsCharts.revenueTrendsChart = new Chart(ctx, {
        type: 'line',
        data: { labels: data.chart_labels || [], datasets: datasets },
        options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } } }
    });
}

function updateRevenueTable(data) {
    bm_safe_destroy_table('#revenue_table');
    jQuery('#revenue_table_body').empty();

    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();

    if (Array.isArray(data.daily_revenue) && data.daily_revenue.length > 0) {
        var html = '';
        data.daily_revenue.forEach(function(day, i) {
            html += `<tr>
                <td>${i+1}</td>
                <td>${day.date}</td>
                <td>${day.orders}</td>
                <td>${formatCurrency(day.gross_sales, sym, pos)}</td>
                <td>${formatCurrency(day.returns, sym, pos)}</td>
                <td>${formatCurrency(day.coupons, sym, pos)}</td>
                <td>${formatCurrency(day.net_sales, sym, pos)}</td>
                <td>${formatCurrency(day.taxes, sym, pos)}</td>
                <td>${formatCurrency(day.shipping, sym, pos)}</td>
                <td>${formatCurrency(day.total_sales, sym, pos)}</td>
            </tr>`;
        });
        jQuery('#revenue_table_body').html(html);
    }

    revenueDataTable = jQuery('#revenue_table').DataTable({
        destroy: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[1, 'desc']],
        select: true,
        dom: '<"datatable-header"Bf>rt<"datatable-footer"ip>',
        buttons: [
            buttonGroups.copyGroup,
            buttonGroups.csvGroup,
            buttonGroups.excelGroup,
            buttonGroups.pdfGroup,
            buttonGroups.printGroup,
            buttonGroups.colvisGroup
        ],
        columnDefs: [
            {
                targets: 0,
                title: '#',
                orderable: false,
                searchable: false,
                width: '50px',
                className: 'dt-center',
                render: function(data, type, row, meta) {
                    return meta.row + 1 + (meta.settings._iDisplayStart || 0);
                }
            }
        ],
        language: {
            emptyTable: 'No data available',
            buttons: { copy: 'Copy', csv: 'CSV', excel: 'Excel', pdf: 'PDF', print: 'Print', colvis: 'Columns' }
        },
        drawCallback: function(settings) {
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });
}

// --- PRODUCTS ---
function updateProductsMetrics(data) {
    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();
    var compareType = jQuery('#analytics_compare_type').val();
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    updateMetric('items_sold', data.items_sold, data.items_sold_change, '', '', compareType, compareEnabled);
    updateMetric('products_net_sales', data.net_sales, data.net_sales_change, sym, pos, compareType, compareEnabled);
    updateMetric('products_orders', data.total_orders, data.total_orders_change, '', '', compareType, compareEnabled);
}

function updateProductsChart(data) {
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    var currentRange = formatDateRange(jQuery('#analytics_date_from').val(), jQuery('#analytics_date_to').val());
    var compareRange = formatDateRange(jQuery('#analytics_compare_from').val(), jQuery('#analytics_compare_to').val());

    if (analyticsCharts.itemsSoldChart) analyticsCharts.itemsSoldChart.destroy();
    if (!document.getElementById('items_sold_chart')) return;
    var ctx = document.getElementById('items_sold_chart').getContext('2d');
    
    var datasets = [{
        label: currentRange,
        data: data.current_items_sold_data,
        backgroundColor: '#2271b1'
    }];
    if (compareEnabled) {
        datasets.push({
            label: compareRange,
            data: data.previous_items_sold_data,
            backgroundColor: '#8c8f94'
        });
    }
    analyticsCharts.itemsSoldChart = new Chart(ctx, {
        type: 'bar',
        data: { labels: data.chart_labels || [], datasets: datasets },
        options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } } }
    });
}

function updateProductsTable(data) {
    bm_safe_destroy_table('#products_table');
    jQuery('#products_table_body').empty();

    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();

    if (Array.isArray(data.products) && data.products.length > 0) {
        var html = '';
        data.products.forEach(function(p, i) {
            var avg = p.orders > 0 ? p.net_sales / p.orders : 0;
            var conv = p.visits > 0 ? (p.orders / p.visits) * 100 : 0;
            html += `<tr>
                <td>${i+1}</td>
                <td>${p.name || 'Unnamed'}</td>
                <td>${p.category || 'Uncategorized'}</td>
                <td>${p.items_sold}</td>
                <td>${formatCurrency(p.net_sales, sym, pos)}</td>
                <td>${p.orders}</td>
                <td>${formatCurrency(avg, sym, pos)}</td>
                <td>${conv.toFixed(2)}%</td>
            </tr>`;
        });
        jQuery('#products_table_body').html(html);
    }

    productsDataTable = jQuery('#products_table').DataTable({
        destroy: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[3, 'desc']],
        select: true,
        dom: '<"datatable-header"Bf>rt<"datatable-footer"ip>',
        buttons: [
            buttonGroups.copyGroup,
            buttonGroups.csvGroup,
            buttonGroups.excelGroup,
            buttonGroups.pdfGroup,
            buttonGroups.printGroup,
            buttonGroups.colvisGroup
        ],
        columnDefs: [
            {
                targets: 0,
                title: '#',
                orderable: false,
                searchable: false,
                width: '50px',
                className: 'dt-center',
                render: function(data, type, row, meta) {
                    return meta.row + 1 + (meta.settings._iDisplayStart || 0);
                }
            }
        ],
        language: {
            emptyTable: 'No data available',
            buttons: { copy: 'Copy', csv: 'CSV', excel: 'Excel', pdf: 'PDF', print: 'Print', colvis: 'Columns' }
        },
        drawCallback: function(settings) {
            var api = this.api();
            var start = api.page.info().start;
            api.column(0, {page: 'current'}).nodes().each(function(cell, i) {
                cell.innerHTML = start + i + 1;
            });
        }
    });
}

// ------------------------------------------------------------
// 6. ORDERS TAB FUNCTIONS
// ------------------------------------------------------------
function bm_load_orders_data() {
    var post = get_common_post_data('orders');
    post.filters = detailState.filters || {};
    jQuery('.loader_modal').show();
    jQuery.post(bm_ajax_object.ajax_url, { 
        'action': 'bm_fetch_analytics_data', 
        'post': post, 
        'nonce': bm_ajax_object.nonce 
    }, function(res) {
        jQuery('.loader_modal').hide();
        var data = (typeof res === 'string') ? JSON.parse(res) : res;
        if (data.status) {
            updateOrdersMetrics(data);
            updateOrdersChart(data);
            updateOrdersTable(data);
            populateOrdersFilters(data.filters);
        }
    });
}

function updateOrdersMetrics(data) {
    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();
    var compareType = jQuery('#analytics_compare_type').val();
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    updateMetric('orders_total', data.total_orders, data.total_orders_change, '', '', compareType, compareEnabled);
    updateMetric('orders_avg_value', data.avg_order_value, data.avg_order_value_change, sym, pos, compareType, compareEnabled);
    updateMetric('orders_revenue', data.total_revenue, data.total_revenue_change, sym, pos, compareType, compareEnabled);
}

function updateOrdersChart(data) {
    var compareEnabled = jQuery('#enable_compare').is(':checked');
    var currentRange = formatDateRange(jQuery('#analytics_date_from').val(), jQuery('#analytics_date_to').val());
    var compareRange = formatDateRange(jQuery('#analytics_compare_from').val(), jQuery('#analytics_compare_to').val());

    if (analyticsCharts.ordersTrendChart) analyticsCharts.ordersTrendChart.destroy();
    if (!document.getElementById('orders_trend_chart')) return;
    var ctx = document.getElementById('orders_trend_chart').getContext('2d');
    
    var datasets = [{
        label: currentRange,
        data: data.current_orders_data,
        borderColor: '#2271b1',
        backgroundColor: 'rgba(34,113,177,0.1)',
        fill: true,
        tension: 0.4
    }];
    if (compareEnabled) {
        datasets.push({
            label: compareRange,
            data: data.previous_orders_data,
            borderColor: '#8c8f94',
            backgroundColor: 'rgba(140,143,148,0.1)',
            borderDash: [5,5],
            fill: true,
            tension: 0.4
        });
    }
    analyticsCharts.ordersTrendChart = new Chart(ctx, {
        type: 'line',
        data: { labels: data.chart_labels || [], datasets: datasets },
        options: { responsive: true, maintainAspectRatio: false, scales: { x: { display: false } } }
    });
}

function updateOrdersTable(data) {
    bm_safe_destroy_table('#orders_table');
    jQuery('#orders_table_body').empty();

    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();

    function formatDateString(dateStr) {
        if (!dateStr) return '';
        var date = new Date(dateStr);
        if (isNaN(date.getTime())) return dateStr;
        
        var day = date.getDate().toString().padStart(2, '0');
        var month = (date.getMonth() + 1).toString().padStart(2, '0');
        var year = date.getFullYear().toString().slice(-2);
        
        return `${day}/${month}/${year}`;
    }

    if (Array.isArray(data.orders) && data.orders.length > 0) {
        var html = '';
        data.orders.forEach(function(order, i) {
            html += `<tr>
                <td>${order.orderId || 0}</td>
                <td>${order.service_name || ''}</td>
                <td>${formatDateString(order.booking_created_at)}</td>
                <td>${formatDateString(order.booking_date)}</td>
                <td>${order.first_name || ''}</td>
                <td>${order.last_name || ''}</td>
                <td>${order.contact_no || ''}</td>
                <td>${order.email_address || ''}</td>
                <td>${order.total_svc_slots || 0}</td>
                <td>${order.total_ext_svc_slots || 0}</td>
                <td>${formatCurrency(order.service_cost, sym, pos)}</td>
                <td>${formatCurrency(order.extra_svc_cost, sym, pos)}</td>
                <td>${formatCurrency(order.disount_amount, sym, pos)}</td>
                <td>${formatCurrency(order.total_cost, sym, pos)}</td>
                <td>${order.order_status || ''}</td>
                <td>${order.payment_status || ''}</td>
            </tr>`;
        });
        jQuery('#orders_table_body').html(html);
    }

    ordersDataTable = jQuery('#orders_table').DataTable({
        destroy: true,
        paging: true,
        searching: true,
        ordering: true,
        info: true,
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'desc']],
        select: true,
        dom: '<"datatable-header"Bf>rt<"datatable-footer"ip>',
        buttons: [
            buttonGroups.copyGroup,
            buttonGroups.csvGroup,
            buttonGroups.excelGroup,
            buttonGroups.pdfGroup,
            buttonGroups.printGroup,
            buttonGroups.colvisGroup
        ],
        columnDefs: [
            {
                targets: 0,
                title: 'Order ID',
                orderable: true,
                searchable: true,
                width: '50px',
                className: 'dt-center',
            }
        ],
        language: {
            emptyTable: 'No data available',
            buttons: { copy: 'Copy', csv: 'CSV', excel: 'Excel', pdf: 'PDF', print: 'Print', colvis: 'Columns' }
        }
    });

    // --- Persistence ---
    var storageKey = 'analytics_orders_columns';
    loadColumnVisibility(ordersDataTable, storageKey);

    ordersDataTable.on('column-visibility.dt', function(e, settings, column, state) {
        saveColumnVisibility(ordersDataTable, storageKey);
    });
}

/**
 * Populate the multi‑select filters with options from the server,
 * restore previously selected values, then initialize them.
 */
function populateOrdersFilters(filters) {
    function initFilter(selectId, options) {
        var $select = jQuery('#' + selectId);
        $select.empty();

        if (options && options.length) {
            options.forEach(function(opt) {
                $select.append(jQuery('<option>', {
                    value: opt.value,
                    text: opt.label
                }));
            });
        }

        var filterKey = selectId.replace('filter_', '');
        var selectedValues = detailState.filters[filterKey];
        if (selectedValues && selectedValues.length) {
            $select.val(selectedValues);
        }

        if (typeof intitializeMultiselect === 'function') {
            intitializeMultiselect(selectId);
        }
    }

    if (filters.customers) initFilter('filter_customers', filters.customers);
    if (filters.services) initFilter('filter_services', filters.services);
    if (filters.order_statuses) initFilter('filter_order_status', filters.order_statuses);
    if (filters.payment_statuses) initFilter('filter_payment_status', filters.payment_statuses);
    if (filters.emails) initFilter('filter_emails', filters.emails);
}

function bm_apply_orders_filters() {
    var filters = {};
    jQuery('#filter_customers, #filter_services, #filter_order_status, #filter_payment_status, #filter_emails').each(function() {
        var val = jQuery(this).val();
        if (val && val.length) {
            var key = this.id.replace('filter_', '');
            filters[key] = val;
        }
    });
    detailState.filters = filters;
    bm_load_orders_data();
}

function bm_reset_orders_filters() {
    jQuery('#filter_customers, #filter_services, #filter_order_status, #filter_payment_status, #filter_emails').each(function() {
        jQuery(this).find('option').prop('selected', false);
        jQuery(this).val([]);
    });

    ['filter_customers', 'filter_services', 'filter_order_status', 'filter_payment_status', 'filter_emails'].forEach(function(id) {
        if (typeof intitializeMultiselect === 'function') {
            intitializeMultiselect(id);
        }
    });

    detailState.filters = {};
    bm_load_orders_data();
}

function bm_toggle_compare() {
    var enabled = jQuery('#enable_compare').is(':checked');
    if (enabled) {
        jQuery('#compare-type-radio').show();
        bm_update_compare_dates();
    } else {
        jQuery('#compare-type-radio').hide();
        jQuery('#analytics_compare_from').val('');
        jQuery('#analytics_compare_to').val('');
        bm_load_analytics_data();
    }
}

function bm_update_compare_dates() {
    if (!jQuery('#enable_compare').is(':checked')) return;
    if (!bm_validate_date_range('#analytics_date_from', '#analytics_date_to')) return;
    var dateFrom = jQuery('#analytics_date_from').val();
    var dateTo = jQuery('#analytics_date_to').val();
    var compareType = jQuery('input[name="compare_type"]:checked').val() || 'period';
    var compare = bm_calculate_compare_dates(dateFrom, dateTo, compareType);
    jQuery('#analytics_compare_from').val(compare.compareFrom);
    jQuery('#analytics_compare_to').val(compare.compareTo);
    bm_load_analytics_data();
}

function bm_validate_date_range(startId, endId, callback) {
    var startVal = jQuery(startId).val();
    var endVal = jQuery(endId).val();
    
    if (!startVal || !endVal) return true;
    
    var startParts = startVal.split('/');
    var endParts = endVal.split('/');
    
    var startDate = new Date(startParts[2], startParts[1] - 1, startParts[0]);
    var endDate = new Date(endParts[2], endParts[1] - 1, endParts[0]);
    
    if (startDate.getDate() != startParts[0] || 
        startDate.getMonth() != startParts[1] - 1 || 
        startDate.getFullYear() != startParts[2]) {
        alert('Invalid start date format or date does not exist.');
        revertToPrevious(startId, endId);
        return false;
    }
    
    if (endDate.getDate() != endParts[0] || 
        endDate.getMonth() != endParts[1] - 1 || 
        endDate.getFullYear() != endParts[2]) {
        alert('Invalid end date format or date does not exist.');
        revertToPrevious(startId, endId);
        return false;
    }
    
    if (endDate < startDate) {
        alert('End date must be greater than or equal to start date.');
        revertToPrevious(startId, endId);
        return false;
    }
    
    jQuery(startId).data('previous', startVal);
    jQuery(endId).data('previous', endVal);
    
    if (callback) callback();
    return true;
}

function revertToPrevious(startId, endId) {
    var $start = jQuery(startId);
    var $end = jQuery(endId);
    var prevStart = $start.data('previous') || $start.val();
    var prevEnd = $end.data('previous') || $end.val();
    $start.val(prevStart);
    $end.val(prevEnd);
}

function bm_calculate_compare_dates(dateFrom, dateTo, compareType) {
    var fromParts = dateFrom.split('/');
    var toParts = dateTo.split('/');
    var fromDate = new Date(fromParts[2], fromParts[1] - 1, fromParts[0]);
    var toDate = new Date(toParts[2], toParts[1] - 1, toParts[0]);

    if (compareType === 'year') {
        var compareFrom = new Date(fromDate);
        compareFrom.setFullYear(fromDate.getFullYear() - 1);
        var compareTo = new Date(toDate);
        compareTo.setFullYear(toDate.getFullYear() - 1);
        return {
            compareFrom: formatDate(compareFrom),
            compareTo: formatDate(compareTo)
        };
    } else {
        var durationMs = toDate - fromDate;
        var durationDays = Math.round(durationMs / (1000 * 60 * 60 * 24));
        var compareTo = new Date(fromDate);
        compareTo.setDate(compareTo.getDate() - 1);
        var compareFrom = new Date(compareTo);
        compareFrom.setDate(compareFrom.getDate() - durationDays);
        return {
            compareFrom: formatDate(compareFrom),
            compareTo: formatDate(compareTo)
        };
    }
}

// ------------------------------------------------------------
// 7. DETAIL VIEW & DOM REBUILD
// ------------------------------------------------------------
function bm_open_metric_detail(metric) {
    jQuery('#current_detail_metric').val(metric);
    detailState.metric = metric;

    if (analyticsCharts.detailChart) { 
        analyticsCharts.detailChart.destroy(); 
        analyticsCharts.detailChart = null;
    }

    jQuery('.analytics-tabs').hide();
    jQuery('#analytics-detail-view').show();
    jQuery('#detail-title').text(metric.replace(/_/g, ' ').toUpperCase() + ' DETAILS');

    bm_load_metric_chart_data(metric);
    bm_init_detail_table(metric);
}

function bm_close_detail_view() {
    jQuery('#analytics-detail-view').hide();
    jQuery('.analytics-tabs').show();
    bm_safe_destroy_table('#detail-table');
}

function bm_load_metric_chart_data(metric) {
    var postData = get_common_post_data('metric_chart');
    postData.metric = metric;
    jQuery.post(bm_ajax_object.ajax_url, { 
        'action': 'bm_fetch_analytics_data', 
        'post': postData, 
        'nonce': bm_ajax_object.nonce 
    }, function(res) {
        var data = (typeof res === 'string') ? JSON.parse(res) : res;
        if (data.status) {
            var ctx = document.getElementById('detail-metric-chart').getContext('2d');
            if (analyticsCharts.detailChart) analyticsCharts.detailChart.destroy();

            var compareEnabled = jQuery('#enable_compare').is(':checked');
            var currentRange = formatDateRange(jQuery('#analytics_date_from').val(), jQuery('#analytics_date_to').val());
            var compareRange = formatDateRange(jQuery('#analytics_compare_from').val(), jQuery('#analytics_compare_to').val());

            var datasets = [{
                label: currentRange,
                data: data.current_data,
                borderColor: '#2271b1',
                backgroundColor: 'rgba(34,113,177,0.1)',
                fill: true,
                tension: 0.4
            }];
            if (compareEnabled) {
                datasets.push({
                    label: compareRange,
                    data: data.previous_data,
                    borderColor: '#8c8f94',
                    backgroundColor: 'rgba(140,143,148,0.1)',
                    borderDash: [5,5],
                    fill: true,
                    tension: 0.4
                });
            }

            analyticsCharts.detailChart = new Chart(ctx, {
                type: 'line',
                data: { labels: data.chart_labels, datasets: datasets },
                options: { maintainAspectRatio: false, scales: { x: { display: true } } }
            });
        }
    });
}

function bm_rebuild_table_dom(tableId) {
    var $wrapper = jQuery(tableId).parent();
    if ($wrapper.length === 0 || !$wrapper.hasClass('table-responsive')) {
        $wrapper = jQuery('.table-responsive'); 
    }
    bm_safe_destroy_table(tableId);
    jQuery(tableId).remove();
    $wrapper.append('<table class="table display" id="detail-table" style="width:100%"><thead></thead><tbody></tbody></table>');
}

function bm_init_detail_table(metric) {
    var sym = jQuery('#analytics_currency_symbol').val();
    var pos = jQuery('#analytics_currency_position').val();
    var renderCurr = function(data) { return formatCurrency(data, sym, pos); };

    var columns = [];
    if (['total_orders', 'orders'].includes(metric)) {
        columns = [
            { title: "Date", data: "booking_date" },
            { title: "Order ID", data: "order_id" },
            { title: "Customer", data: "customer_name" },
            { title: "Service", data: "service_name" },
            { title: "Total", data: "total_cost", render: renderCurr },
            { title: "Status", data: "order_status" }
        ];
    } else if (['services_sold', 'items_sold'].includes(metric)) {
        columns = [
            { title: "Service", data: "service_name" },
            { title: "Category", data: "category" },
            { title: "Items Sold", data: "items_sold" },
            { title: "Net Sales", data: "net_sales", render: renderCurr },
            { title: "Orders", data: "orders" }
        ];
    } else if (['extra_services_sold'].includes(metric)) {
        columns = [
            { title: "Extra", data: "extra_name" },
            { title: "Service", data: "service_name" },
            { title: "Slots", data: "slots_booked" },
            { title: "Revenue", data: "total_revenue", render: renderCurr }
        ];
    } else {
        columns = [
            { title: "Date", data: "date" },
            { title: "Trans ID", data: "transaction_id" },
            { title: "Customer", data: "customer_name" },
            { title: "Service", data: "service_name" },
            { title: "Amount", data: "paid_amount", render: renderCurr },
            { title: "Method", data: "payment_method" },
            { title: "Status", data: "payment_status" }
        ];
    }

    bm_rebuild_table_dom('#detail-table');

    try {
        detailTable = jQuery('#detail-table').DataTable({
            processing: true,
            serverSide: true,
            columns: columns,
            ajax: {
                url: bm_ajax_object.ajax_url,
                type: 'POST',
                data: function(d) {
                    return {
                        action: 'bm_fetch_analytics_detail',
                        nonce: bm_ajax_object.nonce,
                        post: {
                            metric: metric,
                            date_from: jQuery('#analytics_date_from').val(),
                            date_to: jQuery('#analytics_date_to').val(),
                            filters: detailState.filters,
                            draw: d.draw,
                            start: d.start,
                            length: d.length,
                            search: d.search.value,
                            order_col: d.order[0] ? d.columns[d.order[0].column].data : '',
                            order_dir: d.order[0] ? d.order[0].dir : 'asc'
                        }
                    };
                },
                error: function(xhr, error, thrown) {
                    console.error("DataTable Error:", xhr.responseText);
                }
            },
            dom: '<"dt-buttons-wrapper"B><"dt-filters"f>rt<"dt-bottom"ip>',
            buttons: [
                buttonGroups.copyGroup,
                buttonGroups.csvGroup,
                buttonGroups.excelGroup,
                buttonGroups.pdfGroup,
                buttonGroups.printGroup,
                buttonGroups.colvisGroup
            ],
            language: { emptyTable: "No data available" }
        });
    } catch (e) {
        console.error("Init Error", e);
    }
}

function bm_toggle_column_selector() {
    jQuery('#column-selector-dropdown').toggle();
}

/**
 * Save current column visibility state of a DataTable to localStorage.
 * @param {object} table - DataTable instance.
 * @param {string} storageKey - Key under which to save (e.g., 'analytics_orders_columns').
 */
function saveColumnVisibility(table, storageKey) {
    var state = [];
    table.columns().every(function(index) {
        state.push(this.visible());
    });
    localStorage.setItem(storageKey, JSON.stringify(state));
}

/**
 * Load and apply column visibility state from localStorage.
 * @param {object} table - DataTable instance.
 * @param {string} storageKey - Key to load.
 */
function loadColumnVisibility(table, storageKey) {
    var saved = localStorage.getItem(storageKey);
    if (saved) {
        try {
            var state = JSON.parse(saved);
            table.columns().every(function(index) {
                if (state[index] !== undefined) {
                    this.visible(state[index]);
                }
            });
        } catch(e) {
            console.warn('Failed to load column visibility', e);
        }
    }
}

// ------------------------------------------------------------
// 8. INITIALIZATION (with Compare Toggle)
// ------------------------------------------------------------
jQuery(document).ready(function($) {
    $('#analytics_date_from, #analytics_date_to').datepicker({
        dateFormat: 'dd/mm/yy',
        onSelect: function() {
            $('#analytics_period').val('custom');
            bm_validate_date_range('#analytics_date_from', '#analytics_date_to', function() {
                if ($('#enable_compare').is(':checked')) {
                    bm_update_compare_dates();
                } else {
                    bm_load_analytics_data();
                }
            });
        }
    });

    $('#analytics_date_from, #analytics_date_to').on('change', function() {
        bm_validate_date_range('#analytics_date_from', '#analytics_date_to', function() {
            $('#analytics_period').val('custom');
            if ($('#enable_compare').is(':checked')) {
                bm_update_compare_dates();
            } else {
                bm_load_analytics_data();
            }
        });
    });

    $('#enable_compare').on('change', function() {
        var enabled = $(this).is(':checked');
        if (enabled) {
            $('#compare-type-radio').show();
            bm_change_analytics_period($('#analytics_period').val());
        } else {
            $('#compare-type-radio').hide();
            $('#analytics_compare_from').val('');
            $('#analytics_compare_to').val('');
            bm_load_analytics_data();
        }
    });

    $('input[name="compare_type"]').on('change', function() {
        if ($('#enable_compare').is(':checked')) {
            $('#analytics_compare_type').val($(this).val());
            if ($('#analytics_period').val() === 'custom') {
                bm_update_compare_dates();
            } else {
                bm_change_analytics_period($('#analytics_period').val());
            }
        }
    });

    // Initial Load (start with comparison off)
    $('#enable_compare').prop('checked', false);
    $('#compare-type-radio').hide();
    bm_change_analytics_period('this_month');
});