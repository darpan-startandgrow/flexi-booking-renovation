<?php
$dbhandler          = new BM_DBhandler();
$bmrequests         = new BM_Request();
$woocommerceservice = new WooCommerceService();
$limit              = 10;
$total              = $dbhandler->bm_count( 'BOOKING' );
$today              = gmdate( 'Y-m-d' );
$next_day           = $bmrequests->bm_add_day( $today, '+1 day' );
$week_last_day      = $bmrequests->bm_add_day( $today, '+7 day' );
$categories         = $dbhandler->get_all_result( 'CATEGORY', '*', 1, 'results', 0, false, 'cat_position', false );
$services           = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ), 'results', 0, false, 'service_position', false );
$category_ids       = !empty( $categories ) ? wp_list_pluck( $categories, 'id' ) : array();
$service_ids        = !empty( $services ) ? wp_list_pluck( $services, 'id' ) : array();

/**if ( $woocommerceservice->is_enabled() ) {
    $order_statuses = wc_get_order_statuses();
} else {
    $order_statuses = $bmrequests->bm_fetch_order_status_key_value();
}*/

$order_statuses = $bmrequests->bm_fetch_order_status_key_value();
$timezone       = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
$date           = new DateTime( 'now', new DateTimeZone( $timezone ) );
$current_year   = $date->format( 'Y' );
$months         = array(
    '01' => esc_html__( 'Jan', 'service-booking' ),
    '02' => esc_html__( 'Feb', 'service-booking' ),
    '03' => esc_html__( 'Mar', 'service-booking' ),
    '04' => esc_html__( 'Apr', 'service-booking' ),
    '05' => esc_html__( 'May', 'service-booking' ),
    '06' => esc_html__( 'June', 'service-booking' ),
    '07' => esc_html__( 'Jul', 'service-booking' ),
    '08' => esc_html__( 'Aug', 'service-booking' ),
    '09' => esc_html__( 'Sept', 'service-booking' ),
    '10' => esc_html__( 'Oct', 'service-booking' ),
    '11' => esc_html__( 'Nov', 'service-booking' ),
    '12' => esc_html__( 'Dec', 'service-booking' ),
);

?>

<div class="container">
    <div class="pagewrapper">
        <div class="widgetbar">
            <div class="widgetbox">
                <div class="leftwidget">
                    <h2><?php esc_html_e( 'Total Bookings', 'service-booking' ); ?><br />
                        <span class="total_bookings_count"></span>
                    </h2>
                    <ul class="legend0">
                        <li><span class="legend-dots bluedot get_booking-info" data-status="booked" data-type="total"></span><?php esc_html_e( 'Completed', 'service-booking' ); ?></li>
                        <li><span class="legend-dots greydot get_booking-info" data-status="pending" data-type="total"></span><?php esc_html_e( 'Pending', 'service-booking' ); ?></li>
                    </ul>
                </div>
                <div class="rightwidget">
                    <div class="dashboard_month_year_selection">
                        <select class="widgetselect total_year_analytics" data-type="total" onchange="bm_fetch_booking_counts(this)">
                            <option value=""><?php echo esc_html__( 'year', 'service-booking' ); ?></option>
                            <?php for ( $yr = $current_year - 10; $yr <= $current_year + 10; $yr++ ) { ?>
                                <option value="<?php echo esc_attr( $yr ); ?>"><?php echo esc_attr( $yr ); ?></option>
                            <?php } ?>
                        </select>
                        <select class="widgetselect total_month_analytics" data-type="total" onchange="bm_fetch_booking_counts(this)">
                            <option value=""><?php echo esc_html__( 'month', 'service-booking' ); ?></option>
                            <?php foreach ( $months as $key => $month ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $month ); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="svg-item">
                        <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                            <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                            <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                            <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="80 20" stroke-dashoffset="25"></circle>
                            <g class="donut-text">
                                <!-- <text y="50%" transform="translate(0, 2)">
                                  <tspan x="50%" text-anchor="middle" class="donut-percent">40%</tspan>   
                                </text> -->
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="widgetbox">
                <div class="leftwidget">
                    <h2><?php esc_html_e( 'Upcoming Bookings', 'service-booking' ); ?><br />
                        <span class="upcoming_bookings_count"></span>
                    </h2>
                    <ul class="legend0">
                        <li><span class="legend-dots bluedot get_booking-info" data-status="booked" data-type="upcoming"></span><?php esc_html_e( 'Completed', 'service-booking' ); ?></li>
                        <li><span class="legend-dots greydot get_booking-info" data-status="pending" data-type="upcoming"></span><?php esc_html_e( 'Pending', 'service-booking' ); ?></li>
                    </ul>
                </div>
                <div class="rightwidget">
                    <!-- <div class="dashboard_month_year_selection">
                        <select class="widgetselect upcoming_year_analytics" data-type="upcoming" onchange="bm_fetch_booking_counts(this)">
                            <?php for ( $yr = $current_year - 10; $yr <= $current_year + 10; $yr++ ) { ?>
                                <option value="<?php echo esc_attr( $yr ); ?>" <?php echo $yr == $current_year ? 'selected' : ''; ?>><?php echo esc_attr( $yr ); ?></option>
                            <?php } ?>
                        </select>
                        <select class="widgetselect upcoming_month_analytics" data-type="upcoming" onchange="bm_fetch_booking_counts(this)">
                            <option value=""><?php echo esc_html__( 'month', 'service-booking' ); ?></option>
                            <?php foreach ( $months as $key => $month ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $month ); ?></option>
                            <?php } ?>
                        </select>
                    </div> -->

                    <div class="svg-item">
                        <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                            <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                            <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                            <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="80 20" stroke-dashoffset="25"></circle>
                            <g class="donut-text">
                                <!-- <text y="50%" transform="translate(0, 2)">
                                  <tspan x="50%" text-anchor="middle" class="donut-percent">40%</tspan>   
                                </text> -->
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="widgetbox">
                <div class="leftwidget">
                    <h2><?php esc_html_e( 'Bookings This Week', 'service-booking' ); ?><br />
                        <span class="weekly_bookings_count"></span>
                    </h2>
                    <ul class="legend0">
                        <li><span class="legend-dots bluedot get_booking-info" data-status="booked" data-type="weekly"></span><?php esc_html_e( 'Completed', 'service-booking' ); ?></li>
                        <li><span class="legend-dots greydot get_booking-info" data-status="pending" data-type="weekly"></span><?php esc_html_e( 'Pending', 'service-booking' ); ?></li>
                    </ul>
                </div>
                <div class="rightwidget">
                    <div class="widgetselect">

                    </div>

                    <div class="svg-item">
                        <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                            <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                            <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                            <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="80 20" stroke-dashoffset="25"></circle>
                            <g class="donut-text">
                                <!-- <text y="50%" transform="translate(0, 2)">
                                  <tspan x="50%" text-anchor="middle" class="donut-percent">40%</tspan>   
                                </text> -->
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="widgetbox">
                <div class="leftwidget">
                    <h2><?php esc_html_e( 'Total Revenue', 'service-booking' ); ?><br />
                        <span class="total_bookings_revenue"></span>
                    </h2>
                    <ul class="legend0">
                        <li><span class="legend-dots bluedot get_booking-info" data-status="booked" data-type="revenue"></span><?php esc_html_e( 'Completed', 'service-booking' ); ?></li>
                        <li><span class="legend-dots greydot get_booking-info" data-status="pending" data-type="revenue"></span><?php esc_html_e( 'Pending', 'service-booking' ); ?></li>
                    </ul>
                </div>
                <div class="rightwidget">
                    <div class="dashboard_month_year_selection">
                        <select class="widgetselect revenue_year_analytics" data-type="revenue" onchange="bm_fetch_booking_counts(this)">
                            <option value=""><?php echo esc_html__( 'year', 'service-booking' ); ?></option>
                            <?php for ( $yr = $current_year - 10; $yr <= $current_year + 10; $yr++ ) { ?>
                                <option value="<?php echo esc_attr( $yr ); ?>"><?php echo esc_attr( $yr ); ?></option>
                            <?php } ?>
                        </select>
                        <select class="widgetselect revenue_month_analytics" data-type="revenue" onchange="bm_fetch_booking_counts(this)">
                            <option value=""><?php echo esc_html__( 'month', 'service-booking' ); ?></option>
                            <?php foreach ( $months as $key => $month ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_attr( $month ); ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="svg-item">
                        <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                            <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                            <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                            <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="80 20" stroke-dashoffset="25"></circle>
                            <g class="donut-text">
                                <!-- <text y="50%" transform="translate(0, 2)">
                                  <tspan x="50%" text-anchor="middle" class="donut-percent">40%</tspan>   
                                </text> -->
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="searchpage">
            <div class="mainpage " id="leftbar">

                <div class="graph-left-content">
                    <div class="commonleftbar bgcolor">
                        <div class="chart">
                            <h2><?php esc_html_e( 'Booking Status', 'service-booking' ); ?>
                                <span style="float: right;">
                                    <span class="sg-calender-level-box"><input type="text" placeholder="<?php esc_html_e( 'From', 'service-booking' ); ?>" id="status_from" value="<?php echo !empty( $dbhandler->get_global_option_value( 'bm_dashboard_status_from_field' ) ) ? esc_html( $dbhandler->get_global_option_value( 'bm_dashboard_status_from_field' ) ) : esc_html( gmdate( 'd/m/y', strtotime( $today ) ) ); ?>" onchange="bm_fetch_booking_status_counts()"/>
                                    <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                    </span>
                                    
                                    <span class="sg-calender-level-box">
                                    <input type="text" placeholder="<?php esc_html_e( 'To', 'service-booking' ); ?>" id="status_to" value="<?php echo !empty( $dbhandler->get_global_option_value( 'bm_dashboard_status_to_field' ) ) ? esc_html( $dbhandler->get_global_option_value( 'bm_dashboard_status_to_field' ) ) : esc_html( gmdate( 'd/m/y', strtotime( $next_day ) ) ); ?>" onchange="bm_fetch_booking_status_counts()"/>
                                     <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                    </span>
                                    <select id="booking_status_yearly_or_monthly" onchange="bm_fetch_booking_status_counts()">
                                        <option value="yearly" <?php selected( $dbhandler->get_global_option_value( 'bm_dashboard_status_search_type_field' ), 'yearly' ); ?>><?php esc_html_e( 'Yearly', 'service-booking' ); ?></option>
                                        <option value="monthly" <?php selected( $dbhandler->get_global_option_value( 'bm_dashboard_status_search_type_field' ), 'monthly' ); ?>><?php esc_html_e( 'Monthly', 'service-booking' ); ?></option>
                                    </select>
                                    <select id="booking_status_value" onchange="bm_fetch_booking_status_counts()">
                                        <?php
                                        foreach ( $order_statuses as $key => $order_status ) {
                                            /**$value = $bmrequests->bm_fetch_order_status_string( $key );
                                            $text  = $bmrequests->bm_fetch_order_status_key_value( $value );*/
                                            ?>
                                            <option value="<?php echo esc_html( $key ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_dashboard_status_search_value_field' ), $key ); ?>><?php echo esc_html( $order_status ); ?></option>
                                        <?php } ?>
                                    </select>
                                </span>
                            </h2>
                            <canvas id="status_bar"></canvas>
                        </div>
                    </div>

                    <div class="commonleftbar bgcolor">
                        <div class="tab-wrap">
                            <ul class="tab-head">
                                <li class="tablink tab-active" onclick="bm_open_dashboard_table_tabs(this,'tab1')" data-target="tab1">
                                    <?php esc_html_e( 'Upcoming Bookings', 'service-booking' ); ?>
                                </li>
                                <li class="tablink" onclick="bm_open_dashboard_table_tabs(this,'tab2')" data-target="tab2">
                                    <?php esc_html_e( 'All Bookings', 'service-booking' ); ?>
                                </li>
                                <li class="tablink" onclick="bm_open_dashboard_table_tabs(this,'tab3')" data-target="tab3">
                                    <?php esc_html_e( '7 days Bookings', 'service-booking' ); ?>
                                </li>
                                <li class="tablink" onclick="bm_open_dashboard_table_tabs(this,'tab4')" data-target="tab4">
                                    <?php esc_html_e( 'Category wise Bookings', 'service-booking' ); ?>
                                </li>
                                <li class="tablink" onclick="bm_open_dashboard_table_tabs(this,'tab5')" data-target="tab5">
                                    <?php esc_html_e( 'Service Wise Revenue', 'service-booking' ); ?>
                                </li>
                                <li class="tablink" onclick="bm_open_dashboard_table_tabs(this,'tab6')" data-target="tab6">
                                    <?php esc_html_e( 'Date Wise Revenue', 'service-booking' ); ?>
                                </li>
                                <li class="tablink" onclick="bm_open_dashboard_table_tabs(this,'tab7')" data-target="tab7">
                                    <?php esc_html_e( 'Customer wise Bookings', 'service-booking' ); ?>
                                </li>
                            </ul>

                            <div class="tab-main">
                                <div id="tab1" class="tabcontent active">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                             <span class="dash_title"><?php esc_html_e( 'Upcoming Bookings', 'service-booking' ); ?></span>
                                            <div class="upcoming_orders_date_search_span sg-filter-bar">
                                                <div class="service_date_search_span">
                                                    <span class="sg-calender-level-box">
                                                        <input type="text" class="sg-calendar-input-box" id="dashboard_upcoming_orders_service_from" name="dashboard_upcoming_orders_service_from" placeholder="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" title="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" autocomplete="off">
                                                        <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                    </span>
                                                    <span class="sg-calender-level-box">
                                                        <input type="text" class="sg-calendar-input-box" id="dashboard_upcoming_orders_service_to" name="dashboard_upcoming_orders_service_to" placeholder="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" title="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" autocomplete="off">
                                                        <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                    </span>
                                               
                                                    <span class="sg-calender-level-box">
                                                        <input type="text" class="sg-calendar-input-box" id="dashboard_upcoming_orders_order_from" name="dashboard_upcoming_orders_order_from" placeholder="<?php esc_html_e( 'from order date', 'service-booking' ); ?>" title="<?php esc_html_e( 'from order date', 'service-booking' ); ?>" autocomplete="off">
                                                        <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                    </span>
                                                    <span class="sg-calender-level-box">
                                                        <input type="text" class="sg-calendar-input-box" id="dashboard_upcoming_orders_order_to" name="dashboard_upcoming_orders_order_to" placeholder="<?php esc_html_e( 'to order date', 'service-booking' ); ?>" title="<?php esc_html_e( 'to order date', 'service-booking' ); ?>" autocomplete="off">
                                                        <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                    </span>
                                                    <button type="button" class="button button-primary" id="dashboard_upcoming_orders_date_search_button" title="<?php esc_html_e( 'Search', 'service-booking' ); ?>"><?php esc_html_e( 'Search', 'service-booking' ); ?></button>
                                                    <button type="button" class="button button-secondary" id="dashboard_upcoming_orders_reset_date_search" title="<?php esc_html_e( 'Reset', 'service-booking' ); ?>"><?php esc_html_e( 'Reset', 'service-booking' ); ?></button>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table" id="dashboard_upcoming_orders">
                                            <thead>
                                                <tr>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Customer Data', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Cost', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered From', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order Status', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashboard_upcoming_orders"></tbody>
                                        </table>
                                        <div id="dashboard_upcoming_orders_pagination"></div>
                                    </div>
                                </div>
                                <div id="tab2" class="tabcontent">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                            <span class="dash_title"><?php esc_html_e( 'All Bookings', 'service-booking' ); ?></span>
                                            <?php if ( isset( $total ) && !empty( $total > 0 ) ) { ?>
                                               <div style="width:20%;float: right;margin-top: -15px;">
<!--                                                <span style="margin-left: 5px;">
                                                    <a href="javascript:void(0);" class="button button-primary" title="<?php esc_html_e( 'Advanced search', 'service-booking' ); ?>" onclick="bm_show_search_box('dashboard_all_orders_advanced_search_box')"><?php esc_html_e( 'Advanced search', 'service-booking' ); ?>&nbsp;<i class="fa fa-search" aria-hidden="true"></i></a>
                                                </span>-->
                                                
                                                <span class="tab-box sg-search-box">
                                                    <span class="inputgroup">
                                                        <input type="text" id="dashboard_global_search" class="textbox" value="<?php echo esc_html( $dbhandler->get_global_option_value( 'bm_backend_dashboard_global_search_field' ) ); ?>" placeholder="<?php esc_html_e( 'Search', 'service-booking' ); ?>" autocomplete="off" />
                                                        <i class="fa fa-search dashboard-fa dashboard_all_bookings_search_icon" id="dashboard_all_bookings_search_icon" data-title="<?php esc_html_e( 'Search', 'service-booking' ); ?>" title="<?php esc_html_e( 'Click to search', 'service-booking' ); ?>"></i>
                                                    </span>
                                                </span>
                                            </div>

                                                <div class="dashboard_all_orders_advanced_search_box" id="dashboard_all_orders_advanced_search_box" style="width:100%; float: left; margin-top:0px;">
                                                    <span class="service_date_search_span">
                                                        <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="dashboard_all_orders_service_from" name="dashboard_all_orders_service_from" placeholder="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" title="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                        <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="dashboard_all_orders_service_to" name="dashboard_all_orders_service_to" placeholder="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" title="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                    
                                                        <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="dashboard_all_orders_order_from" name="dashboard_all_orders_order_from" placeholder="<?php esc_html_e( 'from order date', 'service-booking' ); ?>" title="<?php esc_html_e( 'from order date', 'service-booking' ); ?>" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                       <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="dashboard_all_orders_order_to" name="dashboard_all_orders_order_to" placeholder="<?php esc_html_e( 'to order date', 'service-booking' ); ?>" title="<?php esc_html_e( 'to order date', 'service-booking' ); ?>" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                        <button type="button" class="button button-primary" id="dashboard_all_orders_date_search_button" title="<?php esc_html_e( 'Search', 'service-booking' ); ?>"><?php esc_html_e( 'Search', 'service-booking' ); ?></button>
                                                        <button type="button" class="button button-secondary" id="dashboard_all_orders_reset_date_search" title="<?php esc_html_e( 'Reset', 'service-booking' ); ?>"><?php esc_html_e( 'Reset', 'service-booking' ); ?></button>
                                                    </span>
                                                </div>

												<?php
                                            }//end if
                                            ?>
                                        </div>
                                        <table class="table" id="dashboard_all_orders">
                                            <thead>
                                                <tr>
                                                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Cost', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Customer Data', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered From', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order Status', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashboard_all_orders"></tbody>
                                        </table>
                                        <div id="dashboard_all_orders_pagination"></div>
                                    </div>
                                </div>
                                
                                <div id="tab3" class="tabcontent">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                             <span class="dash_title"><?php esc_html_e( 'Bookings at an interval of 7 days', 'service-booking' ); ?></span>
                                           <div style="float: right; width: 100%;">
                                                <div class="service_date_search_span">
                                                    <span class="inputgroup" style="margin-right:10px;">
                                                        <span class="weekly_label"><?php esc_html_e( 'Service From: ', 'service-booking' ); ?></span>
                                                        <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="weekly_service_from" name="weekly_service_from" placeholder="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" title="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" value="<?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_service_from_field' ) ) ? esc_html( $dbhandler->get_global_option_value( 'bm_backend_dashboard_service_from_field' ) ) : esc_html( gmdate( 'd/m/y', strtotime( $today ) ) ); ?>" onchange="bm_fetch_dashboard_weekly_orders('1', 'search')" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                    </span>
                                                    <span class="inputgroup">
                                                        <span class="weekly_label"><?php esc_html_e( 'Service To: ', 'service-booking' ); ?></span>
                                                        <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="weekly_service_to" name="weekly_service_to" placeholder="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" title="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" value="<?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_service_to_field' ) ) ? esc_html( $dbhandler->get_global_option_value( 'bm_backend_dashboard_service_to_field' ) ) : esc_html( gmdate( 'd/m/y', strtotime( $week_last_day ) ) ); ?>" onchange="bm_fetch_dashboard_weekly_orders('1', 'search')" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <table class="table" id="dashoboard_weekly_orders">
                                            <thead>
                                                <tr>
                                                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Cost', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Customer Data', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered From', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order Status', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashoboard_weekly_orders"></tbody>
                                        </table>
                                        <div id="dashoboard_weekly_orders_pagination"></div>
                                    </div>
                                </div>
                                <div id="tab4" class="tabcontent">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                            <span class="dash_title"><?php esc_html_e( 'Category wise Bookings', 'service-booking' ); ?></span>
                                            <span>
                                                <span class="dashboard_select_box">
                                                    <select class="dashboard-multiselect" name="search_order_by_category_id[]" id="search_order_by_category_id" onchange="bm_fetch_cat_wise_orders('1', 'search')" multiple="multiple">
                                                        <option value="<?php echo esc_attr( 0 ); ?>" <?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' ) ) && in_array( esc_attr( 0 ), $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' ) ) ? 'selected' : 'selected'; ?> <?php
                                                        if ( empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' ) ) ) {
                                                            echo 'selected';
														}
														?>
                                                                       ><?php esc_html_e( 'Uncategorized', 'service-booking' ); ?></option>
                                                        <?php
                                                        if ( !empty( $categories ) ) {
                                                            foreach ( $categories as $category ) {
																?>
                                                                <option value="<?php echo esc_attr( $category->id ); ?>" <?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' ) ) && in_array( esc_attr( $category->id ), $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' ) ) ? 'selected' : ''; ?> <?php
                                                                if ( empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_cat_wise_order_cat_ids' ) ) && !empty( $category_ids ) && in_array( esc_attr( $category->id ), $category_ids ) ) {
                                                                    echo 'selected';
																}
																?>
                                                                               ><?php echo isset( $category->cat_name ) ? esc_html( $category->cat_name ) : ''; ?></option>
																<?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                            </span>
                                        </div>
                                        <table class="table" id="dashoboard_cat_wise_orders">
                                            <thead>
                                                <tr>
                                                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Category', 'service-booking' ); ?></th>
                                                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Cost', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered From', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order Status', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashoboard_cat_wise_orders"></tbody>
                                        </table>
                                        <div id="dashoboard_cat_wise_orders_pagination"></div>
                                    </div>
                                </div>
                                <div id="tab5" class="tabcontent">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                            <span class="dash_title"><?php esc_html_e( 'Booking Revenue as per Service', 'service-booking' ); ?></span>
                                            <span>
                                                <span class="dashboard_select_box">
                                                    <select class="dashboard-multiselect" name="search_order_by_service_id[]" id="search_order_by_service_id" onchange="bm_fetch_service_wise_revenue('1')" multiple="multiple">
                                                        <?php
                                                        if ( !empty( $services ) ) {
                                                            foreach ( $services as $service ) {
																?>
                                                                <option value="<?php echo esc_attr( $service->id ); ?>" <?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_revenue_wise_order_svc_search_ids' ) ) && in_array( esc_attr( $service->id ), $dbhandler->get_global_option_value( 'bm_backend_dashboard_revenue_wise_order_svc_search_ids' ) ) ? 'selected' : ''; ?> <?php
                                                                if ( empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_revenue_wise_order_svc_search_ids' ) ) && !empty( $service_ids ) && in_array( esc_attr( $service->id ), $service_ids ) ) {
                                                                    echo 'selected';
																}
																?>
                                                                               ><?php echo isset( $service->service_name ) ? esc_html( $service->service_name ) : ''; ?></option>
																<?php
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </span>
                                            </span>
                                        </div>
                                        <table class="table" id="dashboard_revenue_orders">
                                            <thead>
                                                <tr>
                                                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Orders', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Slots Booked', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Extra Service Slots Booked', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Revenue', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashboard_revenue_orders"></tbody>
                                        </table>
                                        <div id="dashboard_revenue_orders_pagination"></div>
                                    </div>
                                </div>
                                
                    
                                
                                
                                <div id="tab6" class="tabcontent">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                            <span class="dash_title"><?php esc_html_e( 'Date Wise Booking Revenue', 'service-booking' ); ?></span>
                                            <div style="float: right; width: 100%;">
                                                <span class="service_date_search_span">
                                                    <span class="inputgroup">
                                                        <span class="weekly_label"><?php esc_html_e( 'Ordered Date From: ', 'service-booking' ); ?></span>
                                                         <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="datewise_revenue_order_from" name="datewise_revenue_order_from" placeholder="<?php esc_html_e( 'ordered date from', 'service-booking' ); ?>" value="<?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_datewise_revenue_order_from_field' ) ) ? esc_html( $dbhandler->get_global_option_value( 'bm_backend_dashboard_datewise_revenue_order_from_field' ) ) : esc_html( gmdate( 'd/m/y', strtotime( $today ) ) ); ?>" onchange="bm_fetch_datewise_revenue_orders('1', 'search')" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                         </span>
                                                    </span>
                                                    <span class="inputgroup">
                                                        <span class="weekly_label"><?php esc_html_e( 'Ordered Date To: ', 'service-booking' ); ?></span>
                                                        <span class="sg-calender-level-box">
                                                            <input type="text" class="sg-calendar-input-box" id="datewise_revenue_order_to" name="datewise_revenue_order_to" placeholder="<?php esc_html_e( 'ordered date to', 'service-booking' ); ?>" value="<?php echo !empty( $dbhandler->get_global_option_value( 'bm_backend_dashboard_datewise_revenue_order_to_field' ) ) ? esc_html( $dbhandler->get_global_option_value( 'bm_backend_dashboard_datewise_revenue_order_to_field' ) ) : esc_html( gmdate( 'd/m/y', strtotime( $next_day ) ) ); ?>" onchange="bm_fetch_datewise_revenue_orders('1', 'search')" autocomplete="off">
                                                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                                                        </span>
                                                    </span>
                                                </span>
                                            </div>
                                        </div>
                                        
                                        <table class="table" id="dashboard_datewise_revenue_orders">
                                            <thead>
                                                <tr>
                                                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Orders', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Slots Booked', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Extra Service Slots Booked', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Revenue', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashboard_datewise_revenue_orders"></tbody>
                                        </table>
                                        <div id="dashboard_datewise_revenue_orders_pagination"></div>
                                    </div>
                                </div>
                                <div id="tab7" class="tabcontent">
                                    <div class="listing_table dashboard_table">
                                        <div class="order_listing_top">
                                            <span class="dash_title"><?php esc_html_e( 'Customer wise Bookings', 'service-booking' ); ?></span>
                                            <span style="float: right;position: relative;">
                                                <span class="tab-box sg-search-box">
                                                    <span class="inputgroup">
                                                        <input type="text" id="custom_wise_global_search" class="textbox" value="<?php echo esc_html( $dbhandler->get_global_option_value( 'bm_backend_dashboard_customer_wise_global_search_field' ) ); ?>" onkeyup="bm_fetch_customer_wise_revenue_orders('1', 'search')" placeholder="<?php esc_html_e( 'Search', 'service-booking' ); ?>" autocomplete="off" />
                                                        <i class="fa fa-search dashboard-fa"></i>
                                                    </span>
                                                </span>
                                            </span>
                                        </div>
                                        <table class="table" id="dashboard_customer_wise_revenue_orders">
                                            <thead>
                                                <tr>
                                                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                                                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Customer Name', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Customer Email', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Orders', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Slots Booked', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Extra Service Slots Booked', 'service-booking' ); ?></th>
                                                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Revenue', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody class="dashboard_customer_wise_revenue_orders"></tbody>
                                        </table>
                                        <div id="dashboard_customer_wise_revenue_orders_pagination"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="graph-right-content">
                    <div class="commonrightbar">
                        <div class="widgetbox">
                             <div style="width:100%; float:left;">
                            <h2><?php esc_html_e( 'Total Confirmed Booked slots and Customers', 'service-booking' ); ?></h2>
                             <select class="widgetselect" onchange="bm_fetch_customer_and_total_booked_slot_counts(this.value)">
                                    <option value="total"><?php esc_html_e( 'Total', 'service-booking' ); ?></option>
                                    <option value="this_week"><?php esc_html_e( 'This Week', 'service-booking' ); ?></option>
                                    <option value="last_week"><?php esc_html_e( 'Last Week', 'service-booking' ); ?></option>
                                </select>
                             </div>
                            <div class="leftwidget" style="width:100%; float:left;">
                                <div class="svg-item" style="width:45%; float:left;">
                                    <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                                        <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                                        <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                                        <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="80 20" stroke-dashoffset="25"></circle>
                                        <g class="donut-text">
                                            <!-- <text y="50%" transform="translate(0, 2)">
                                            <tspan x="50%" text-anchor="middle" class="donut-percent">40%</tspan>   
                                            </text> -->
                                        </g>
                                    </svg>
                                </div>
                                <div class="svg-item" style="width:45%; float:left;">
                                    <svg width="100%" height="100%" viewBox="0 0 40 40" class="donut">
                                        <circle class="donut-hole" cx="20" cy="20" r="15.91549430918954" fill="#fff"></circle>
                                        <circle class="donut-ring" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5"></circle>
                                        <circle class="donut-segment" cx="20" cy="20" r="15.91549430918954" fill="transparent" stroke-width="3.5" stroke-dasharray="80 20" stroke-dashoffset="25"></circle>
                                        <g class="donut-text">
                                            <!-- <text y="50%" transform="translate(0, 2)">
                                            <tspan x="50%" text-anchor="middle" class="donut-percent">40%</tspan>   
                                            </text> -->
                                        </g>
                                    </svg>
                                </div>
                            </div>
                            <div class="rightwidget" style="width:100%; float:left;">
                                <ul class="legend0 custom-dots">
                                    <li class="custom-li"><span class="legend-dots bluedot"></span><?php esc_html_e( 'Slots Booked', 'service-booking' ); ?></li>
                                    <li class="custom-li"><span class="legend-dots bluedot"></span><?php esc_html_e( 'Total Customers', 'service-booking' ); ?></li>
                                </ul>
                            </div>
                        </div>
                        <div class="datawidgetbox">
                            <div class="datawidget">
                                <span class="slots_booked_count count_span"></span><br />
                                <span><?php esc_html_e( 'Total Slots Booked', 'service-booking' ); ?></span>
                            </div>
                            <div class="datawidget">
                            <span class="total_customers_count count_span"></span><br />
                                <span><?php esc_html_e( 'Total Customers', 'service-booking' ); ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="commonrightbar">
                        <div class="timelinebox">
                            <div class="timelineheading">
                                <?php esc_html_e( 'Confirmed Booking Overview', 'service-booking' ); ?><br />
                                <span class="booking_increase_percent"></span>
                            </div>
                            <div class="bs-vertical-wizard booking_overview_data"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>


<input type="hidden" id="all_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" id="upcoming_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" id="weekly_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" id="cat_wise_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" id="revenue_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" id="datewise_revenue_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" id="customer_wise_revenue_orders_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />
<input type="hidden" id="total_search_status" value="booked" />
<!-- <input type="hidden" id="upcoming_search_status" value="booked" /> -->
<input type="hidden" id="revenue_search_status" value="booked" />

<div id="customer-dialog" title="<?php esc_html_e( 'Customer Details', 'service-booking' ); ?>" style="display: none;">
    <ul id="customer-list"></ul>
</div>

<div class="loader_modal"></div>
