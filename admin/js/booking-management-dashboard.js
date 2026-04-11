function renderStatusChart(labels, data) {
	var ctx = document.getElementById("status_bar").getContext('2d');

	window.dashboardChart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: labels,
			datasets: [{
				label: bm_normal_object.bookings,
				data: data,
				fill: true,
				borderRadius: 6,
				borderColor: '#5eb8ff',
				backgroundColor: '#5eb8ff',
				barPercentage: 0.4,
				datalabels: {
					display: false
				},
			}],
		},
		options: {
			scales: {
				x: {
					grid: {
						display: false
					},
					ticks: { color: '#000' }
				},
				y: {
					suggestedMin: 0,
					ticks: {
						precision: 0
					}
				}
			},
			responsive: true,
			maintainAspectRatio: false,
			plugins: {
				legend: {
					display: true,
					labels: {
						color: '#000'
					}
				},
				tooltip: {
					enabled: true,
					callbacks: {
						label: function (tooltipItem) {
							return bm_normal_object.bookings + ': ' + tooltipItem.raw;
						}
					}
				}
			}
		},
		plugins: [{
			id: 'noDataMessage',
			afterDraw: function (chart) {
				var chartData = chart.data.datasets[0].data;

				if (!chartData.length || chartData.every(value => value === 0)) {
					var ctx = chart.ctx;
					var width = chart.width;
					var height = chart.height;

					ctx.save();
					ctx.font = "bold 24px 'Helvetica Neue', Arial, sans-serif";
					ctx.fillStyle = "#999";
					ctx.textAlign = "center";
					ctx.textBaseline = "middle";

					ctx.fillText(bm_normal_object.no_data_to_show, width / 2, height / 2);
					ctx.restore();
				}
			}
		}]
	});
}


function bm_open_dashboard_table_tabs(evt, tabName) {
	var i, x, tablinks;
	x = document.getElementsByClassName("tabcontent");
	tablinks = document.getElementsByClassName("tablink");
	for (i = 0; i < x.length; i++) {
		tablinks[i].classList.remove("tab-active");
		var target = tablinks[i].dataset.target;
		document.getElementById(target).classList.remove('active');
	}
	evt.classList.add('tab-active');
	var target = evt.dataset.target;
	document.getElementById(target).classList.add('active');

	switch (tabName) {
		case 'tab2':
			var data = { 'action': 'bm_fetch_saved_order_search', 'module': 'dashboard_all_orders', 'nonce': bm_ajax_object.nonce };
			jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
				var saved_search = jQuery.parseJSON(response);
				if (saved_search != null && saved_search != "") {
					if (typeof (saved_search.global_search) != "undefined") {
						jQuery('#dashboard_global_search').val(saved_search.global_search ? saved_search.global_search : '');
					}
					if (typeof (saved_search.service_from) != "undefined") {
						jQuery('#dashboard_all_orders_service_from').val(saved_search.service_from ? saved_search.service_from : '');
					}
					if (typeof (saved_search.service_to) != "undefined") {
						jQuery('#dashboard_all_orders_service_to').val(saved_search.service_to ? saved_search.service_to : '');
					}
					if (typeof (saved_search.order_from) != "undefined") {
						jQuery('#dashboard_all_orders_order_from').val(saved_search.order_from ? saved_search.order_from : '');
					}
					if (typeof (saved_search.order_to) != "undefined") {
						jQuery('#dashboard_all_orders_order_to').val(saved_search.order_to ? saved_search.order_to : '');
					}
				}
				bm_dashboard_order_data_global_search('1', 'search');
			});
			break;
		case 'tab3':
			bm_fetch_dashboard_weekly_orders('1', 'search');
			break;
		case 'tab4':
			bm_fetch_cat_wise_orders('1', 'search');
			break;
		case 'tab5':
			bm_fetch_service_wise_revenue('1', 'search');
			break;
		case 'tab6':
			bm_fetch_datewise_revenue_orders('1', 'search');
			break;
		case 'tab7':
			bm_fetch_customer_wise_revenue_orders('1', 'search');
			break;
		default:
			break;
	}
}



// Fetch booking counts
function bm_fetch_booking_counts($this = null) {
	var currency_symbol = bm_normal_object.currency_symbol;
	var currency_position = bm_normal_object.currency_position;
	var year_value = null;
	var month_value = null;
	var type = '';
	var status = 'booked';

	if ($this != null) {
		type = jQuery($this).data('type');
		year_value = jQuery('.' + type + '_year_analytics').length ? jQuery('.' + type + '_year_analytics').val() : year_value;
		month_value = jQuery('.' + type + '_month_analytics').length ? jQuery('.' + type + '_month_analytics').val() : month_value;
		status = jQuery('#' + type + '_search_status').length ? jQuery('#' + type + '_search_status').val() : jQuery($this).data('status');
	}

	var post = {
		'year': year_value,
		'month': month_value,
		'type': type,
		'status': status,
	}

	var data = { 'action': 'bm_fetch_booking_counts', 'post': post, 'nonce': bm_ajax_object.nonce };
	jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
		var jsondata = jQuery.parseJSON(response);
		if (jsondata.status == true) {
			if (typeof (jsondata.booking_type) != "undefined") {
				if (jsondata.booking_type == '') {
					jQuery('.total_bookings_count').text(jsondata.total_bookings_count ? jsondata.total_bookings_count : '0');
					jQuery('.upcoming_bookings_count').text(jsondata.upcoming_bookings_count ? jsondata.upcoming_bookings_count : '0');
					jQuery('.weekly_bookings_count').text(jsondata.weekly_bookings_count ? jsondata.weekly_bookings_count : '0');
					if (currency_position == 'before') {
						jQuery('.total_bookings_revenue').text(currency_symbol + (jsondata.total_bookings_revenue ? changePriceFormat(jsondata.total_bookings_revenue) : '0'));
					} else {
						jQuery('.total_bookings_revenue').text((jsondata.total_bookings_revenue ? changePriceFormat(jsondata.total_bookings_revenue) : '0') + currency_symbol);
					}
				} else if (jsondata.booking_type == 'total') {
					jQuery('.total_bookings_count').text(jsondata.total_bookings_count ? jsondata.total_bookings_count : '0');
				} else if (jsondata.booking_type == 'upcoming') {
					jQuery('.upcoming_bookings_count').text(jsondata.upcoming_bookings_count ? jsondata.upcoming_bookings_count : '0');
				} else if (jsondata.booking_type == 'revenue') {
					if (currency_position == 'before') {
						jQuery('.total_bookings_revenue').text(currency_symbol + (jsondata.total_bookings_revenue ? changePriceFormat(jsondata.total_bookings_revenue) : '0'));
					} else {
						jQuery('.total_bookings_revenue').text((jsondata.total_bookings_revenue ? changePriceFormat(jsondata.total_bookings_revenue) : '0') + currency_symbol);
					}
				} else if (jsondata.booking_type == 'weekly') {
					jQuery('.weekly_bookings_count').text(jsondata.weekly_bookings_count ? jsondata.weekly_bookings_count : '0');
				}
			}
		} else {
			alert(bm_error_object.server_error);
		}
	});
}



// Fetch booking counts
jQuery(document).on('click', 'span.get_booking-info', function (e) {
	e.preventDefault();
	var type = jQuery(this).data('type');
	var status = jQuery(this).data('status');
	jQuery('#' + type + '_search_status').val(status);
	jQuery(this).parents('ul.legend0').find('li span.get_booking-info').removeClass('bluedot').addClass('greydot');
	jQuery(this).removeClass('greydot').addClass('bluedot');
	bm_fetch_booking_counts(this);
});