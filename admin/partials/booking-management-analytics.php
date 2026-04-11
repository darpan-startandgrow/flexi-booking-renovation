<?php
$dbhandler          = new BM_DBhandler();
$bmrequests         = new BM_Request();
$woocommerceservice = new WooCommerceService();
$timezone           = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
$date               = new DateTime( 'now', new DateTimeZone( $timezone ) );
$current_year       = $date->format( 'Y' );
$current_month      = $date->format( 'm' );
$months             = array(
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

// Default date ranges
$today                = gmdate( 'Y-m-d' );
$first_day_month      = gmdate( 'Y-m-01' );
$last_day_month       = gmdate( 'Y-m-t' );
$first_day_prev_month = gmdate( 'Y-m-01', strtotime( '-1 month' ) );
$last_day_prev_month  = gmdate( 'Y-m-t', strtotime( '-1 month' ) );

// Get services and categories for filters
$services   = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ), 'results', 0, false, 'service_position', false );
$categories = $dbhandler->get_all_result( 'CATEGORY', '*', 1, 'results', 0, false, 'cat_position', false );

// Get currency settings
$currency_symbol   = $bmrequests->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
$currency_position = $dbhandler->get_global_option_value( 'bm_currency_position', 'before' );
?>

<div class="container">
    <div class="pagewrapper">
        <!-- ========== HEADER + DATE RANGE ========== -->
        <div class="analytics-header">
            <h1><?php esc_html_e( 'Analytics', 'service-booking' ); ?></h1>
            
            <div class="date-range-selector">
                <div class="date-range-filters">
                    <div class="date-range-inputs">
                        <span class="sg-calender-level-box">
                            <input type="text" id="analytics_date_from" class="sg-calendar-input-box" 
                                   value="<?php echo esc_attr( gmdate( 'd/m/Y', strtotime( $first_day_month ) ) ); ?>" 
                                   placeholder="<?php esc_html_e( 'From', 'service-booking' ); ?>">
                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                        </span>
                        <span class="sg-calender-level-box">
                            <input type="text" id="analytics_date_to" class="sg-calendar-input-box" 
                                   value="<?php echo esc_attr( gmdate( 'd/m/Y', strtotime( $last_day_month ) ) ); ?>" 
                                   placeholder="<?php esc_html_e( 'To', 'service-booking' ); ?>">
                            <i class="fa fa-calendar dashboard-calendar-fa"></i>
                        </span>
                        <select id="analytics_period" onchange="bm_change_analytics_period(this.value)">
                            <option value="custom"><?php esc_html_e( 'Custom', 'service-booking' ); ?></option>
                            <option value="today"><?php esc_html_e( 'Today', 'service-booking' ); ?></option>
                            <option value="yesterday"><?php esc_html_e( 'Yesterday', 'service-booking' ); ?></option>
                            <option value="this_week"><?php esc_html_e( 'This Week', 'service-booking' ); ?></option>
                            <option value="last_week"><?php esc_html_e( 'Last Week', 'service-booking' ); ?></option>
                            <option value="this_month" selected><?php esc_html_e( 'This Month', 'service-booking' ); ?></option>
                            <option value="last_month"><?php esc_html_e( 'Last Month', 'service-booking' ); ?></option>
                            <option value="this_quarter"><?php esc_html_e( 'This Quarter', 'service-booking' ); ?></option>
                            <option value="last_quarter"><?php esc_html_e( 'Last Quarter', 'service-booking' ); ?></option>
                            <option value="this_year"><?php esc_html_e( 'This Year', 'service-booking' ); ?></option>
                            <option value="last_year"><?php esc_html_e( 'Last Year', 'service-booking' ); ?></option>
                        </select>
                        
                        <!-- Compare checkbox and comparison type (initially hidden) -->
                        <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                            <label class="compare-checkbox">
                                <input type="checkbox" id="enable_compare" onchange="bm_toggle_compare()">
                                <?php esc_html_e( 'Compare', 'service-booking' ); ?>
                            </label>
                            
                            <div id="compare-type-radio" style="display: none;">
                                <div class="compare-type-radio">
                                    <label style="margin-right: 10px;">
                                        <input type="radio" name="compare_type" id="compare_type_period" value="period" checked>
                                        <?php esc_html_e( 'Previous Period', 'service-booking' ); ?>
                                    </label>
                                    <label>
                                        <input type="radio" name="compare_type" id="compare_type_year" value="year">
                                        <?php esc_html_e( 'Previous Year', 'service-booking' ); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields for comparison dates -->
        <input type="hidden" id="analytics_compare_from" value="">
        <input type="hidden" id="analytics_compare_to" value="">

        <!-- ========== ANALYTICS TABS ========== -->
        <div class="analytics-tabs">
            <ul class="tab-head analytics-tab-head">
                <li class="tablink analytics-tablink tab-active" onclick="bm_open_analytics_tabs(this, 'analytics-overview')" data-target="analytics-overview">
                    <?php esc_html_e( 'Overview', 'service-booking' ); ?>
                </li>
                <li class="tablink analytics-tablink" onclick="bm_open_analytics_tabs(this, 'analytics-revenue')" data-target="analytics-revenue">
                    <?php esc_html_e( 'Revenue', 'service-booking' ); ?>
                </li>
                <li class="tablink analytics-tablink" onclick="bm_open_analytics_tabs(this, 'analytics-products')" data-target="analytics-products">
                    <?php esc_html_e( 'Products', 'service-booking' ); ?>
                </li>
                <li class="tablink analytics-tablink" onclick="bm_open_analytics_tabs(this, 'analytics-orders')" data-target="analytics-orders">
                    <?php esc_html_e( 'Orders', 'service-booking' ); ?>
                </li>
            </ul>

            <div class="tab-main analytics-tab-main">
                <!-- ========== OVERVIEW TAB ========== -->
                <div id="analytics-overview" class="tabcontent active" style="display: block;">
                    <div class="analytics-overview">
                        <!-- Performance Metrics -->
                        <div class="analytics-section">
                            <h2><?php esc_html_e( 'Performance', 'service-booking' ); ?></h2>
                            <div class="performance-metrics">
                                <!-- Total Sales -->
                                <div class="metric-card" onclick="bm_open_metric_detail('total_sales')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Total Sales', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="total_sales">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="total_sales_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Net Sales -->
                                <div class="metric-card" onclick="bm_open_metric_detail('net_sales')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="net_sales">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="net_sales_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Orders -->
                                <div class="metric-card" onclick="bm_open_metric_detail('total_orders')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Orders', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="total_orders">0</div>
                                    <div class="metric-change" id="total_orders_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Services Sold -->
                                <div class="metric-card" onclick="bm_open_metric_detail('services_sold')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Services Sold', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="services_sold">0</div>
                                    <div class="metric-change" id="services_sold_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Extra Services Sold -->
                                <div class="metric-card" onclick="bm_open_metric_detail('extra_services_sold')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Extra Services Sold', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="extra_services_sold">0</div>
                                    <div class="metric-change" id="extra_services_sold_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="analytics-section">
                            <div class="chart-row">
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></h3>
                                        <div class="chart-legend">
                                            <span class="legend-item">
                                                <span class="legend-color" style="background-color: #2271b1;"></span>
                                                <?php esc_html_e( 'Current Period', 'service-booking' ); ?>
                                            </span>
                                            <span class="legend-item">
                                                <span class="legend-color" style="background-color: #8c8f94;"></span>
                                                <?php esc_html_e( 'Previous Period', 'service-booking' ); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="chart-wrapper">
                                        <canvas id="net_sales_chart"></canvas>
                                    </div>
                                </div>
                                
                                <div class="chart-container">
                                    <div class="chart-header">
                                        <h3><?php esc_html_e( 'Orders', 'service-booking' ); ?></h3>
                                        <div class="chart-legend">
                                            <span class="legend-item">
                                                <span class="legend-color" style="background-color: #00a32a;"></span>
                                                <?php esc_html_e( 'Current Period', 'service-booking' ); ?>
                                            </span>
                                            <span class="legend-item">
                                                <span class="legend-color" style="background-color: #8c8f94;"></span>
                                                <?php esc_html_e( 'Previous Period', 'service-booking' ); ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="chart-wrapper">
                                        <canvas id="orders_chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Leaderboards -->
                        <div class="analytics-section">
                            <div class="leaderboard-row">
                                <div class="leaderboard-container">
                                    <div class="leaderboard-header">
                                        <h3><?php esc_html_e( 'Top Categories - Services Sold', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="leaderboard-table">
                                        <table class="table" id="top_categories_table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php esc_html_e( 'Category', 'service-booking' ); ?></th>
                                                    <th><?php esc_html_e( 'Services Sold', 'service-booking' ); ?></th>
                                                    <th><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="top_categories_body"></tbody>
                                        </table>
                                    </div>
                                </div>
                                
                                <div class="leaderboard-container">
                                    <div class="leaderboard-header">
                                        <h3><?php esc_html_e( 'Top Services - Services Sold', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="leaderboard-table">
                                        <table class="table" id="top_services_table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th><?php esc_html_e( 'Service', 'service-booking' ); ?></th>
                                                    <th><?php esc_html_e( 'Services Sold', 'service-booking' ); ?></th>
                                                    <th><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody id="top_services_body"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== REVENUE TAB ========== -->
                <div id="analytics-revenue" class="tabcontent" style="display: none;">
                    <div class="analytics-revenue">
                        <!-- Revenue Metrics -->
                        <div class="analytics-section">
                            <h2><?php esc_html_e( 'Revenue Breakdown', 'service-booking' ); ?></h2>
                            <div class="revenue-metrics">
                                <div class="metric-card" onclick="bm_open_metric_detail('gross_sales')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Gross Sales', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="gross_sales">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="gross_sales_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="metric-card" onclick="bm_open_metric_detail('returns')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Returns', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="returns">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="returns_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="metric-card" onclick="bm_open_metric_detail('coupons')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Coupons', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="coupons">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="coupons_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="metric-card" onclick="bm_open_metric_detail('revenue_net_sales')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="revenue_net_sales">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="revenue_net_sales_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Chart -->
                        <div class="analytics-section">
                            <div class="chart-container full-width">
                                <div class="chart-header">
                                    <h3><?php esc_html_e( 'Revenue Trends', 'service-booking' ); ?></h3>
                                    <div class="chart-legend">
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #00a32a;"></span>
                                            <?php esc_html_e( 'Gross Sales', 'service-booking' ); ?>
                                        </span>
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #d63638;"></span>
                                            <?php esc_html_e( 'Returns', 'service-booking' ); ?>
                                        </span>
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #f0c33c;"></span>
                                            <?php esc_html_e( 'Coupons', 'service-booking' ); ?>
                                        </span>
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #2271b1;"></span>
                                            <?php esc_html_e( 'Net Sales', 'service-booking' ); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="revenue_trends_chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Revenue Table -->
                        <div class="analytics-section">
                            <div class="table-container">
                                <div class="table-header">
                                    <h3><?php esc_html_e( 'Daily Revenue Breakdown', 'service-booking' ); ?></h3>
                                    <div class="table-actions">
                                        <!-- <button type="button" class="button button-secondary" onclick="bm_download_revenue_csv()">
                                            <i class="fa fa-download"></i> <?php esc_html_e( 'Download CSV', 'service-booking' ); ?>
                                        </button> -->
                                    </div>
                                </div>
                                <div class="table-wrapper">
                                    <table class="table" id="revenue_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php esc_html_e( 'Date', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Orders', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Gross Sales', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Returns', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Coupons', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Taxes', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Shipping', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Total Sales', 'service-booking' ); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="revenue_table_body">
                                            <tr><td colspan="10"><?php esc_html_e( 'No data available', 'service-booking' ); ?></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== PRODUCTS TAB ========== -->
                <div id="analytics-products" class="tabcontent" style="display: none;">
                    <div class="analytics-products">
                        <!-- Products Metrics -->
                        <div class="analytics-section">
                            <h2><?php esc_html_e( 'Products Performance', 'service-booking' ); ?></h2>
                            <div class="product-metrics">
                                <div class="metric-card" onclick="bm_open_metric_detail('items_sold')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Items Sold', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="items_sold">0</div>
                                    <div class="metric-change" id="items_sold_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="metric-card" onclick="bm_open_metric_detail('products_net_sales')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="products_net_sales">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="products_net_sales_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <div class="metric-card" onclick="bm_open_metric_detail('products_orders')" style="cursor: pointer;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Orders', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="products_orders">0</div>
                                    <div class="metric-change" id="products_orders_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="product-filters">
                                <select id="product_category_filter" onchange="bm_load_analytics_data()">
                                    <option value=""><?php esc_html_e( 'All Categories', 'service-booking' ); ?></option>
                                    <option value="0"><?php esc_html_e( 'Uncategorized', 'service-booking' ); ?></option>
                                    <?php if ( !empty( $categories ) ) : ?>
                                        <?php foreach ( $categories as $category ) : ?>
                                            <option value="<?php echo esc_attr( $category->id ); ?>">
                                                <?php echo esc_html( $category->cat_name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                
                                <select id="product_service_filter" onchange="bm_load_analytics_data()">
                                    <option value=""><?php esc_html_e( 'All Services', 'service-booking' ); ?></option>
                                    <?php if ( !empty( $services ) ) : ?>
                                        <?php foreach ( $services as $service ) : ?>
                                            <option value="<?php echo esc_attr( $service->id ); ?>">
                                                <?php echo esc_html( $service->service_name ); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Products Chart -->
                        <div class="analytics-section">
                            <div class="chart-container full-width">
                                <div class="chart-header">
                                    <h3><?php esc_html_e( 'Items Sold Trend', 'service-booking' ); ?></h3>
                                    <div class="chart-legend">
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #2271b1;"></span>
                                            <?php esc_html_e( 'Current Period', 'service-booking' ); ?>
                                        </span>
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #8c8f94;"></span>
                                            <?php esc_html_e( 'Previous Period', 'service-booking' ); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="items_sold_chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Products Table -->
                        <div class="analytics-section">
                            <div class="table-container">
                                <div class="table-header">
                                    <h3><?php esc_html_e( 'Services Performance', 'service-booking' ); ?></h3>
                                    <!-- <div class="table-actions">
                                        <button type="button" class="button button-secondary" onclick="bm_download_products_csv()">
                                            <i class="fa fa-download"></i> <?php esc_html_e( 'Download CSV', 'service-booking' ); ?>
                                        </button>
                                    </div> -->
                                </div>
                                <div class="table-wrapper">
                                    <table class="table" id="products_table">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th><?php esc_html_e( 'Service', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Category', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Items Sold', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Net Sales', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Orders', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Average Order Value', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Conversion Rate', 'service-booking' ); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="products_table_body">
                                            <tr><td colspan="8"><?php esc_html_e( 'No data available', 'service-booking' ); ?></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== ORDERS TAB ========== -->
                <div id="analytics-orders" class="tabcontent" style="display: none;">
                    <div class="analytics-orders">
                        <!-- Order Metrics Cards -->
                        <div class="analytics-section">
                            <h2><?php esc_html_e( 'Orders Performance', 'service-booking' ); ?></h2>
                            <div class="orders-metrics" style="display: flex; gap: 20px; flex-wrap: wrap;">
                                <!-- Total Orders -->
                                <div class="metric-card" onclick="bm_open_metric_detail('total_orders')" style="cursor: pointer; flex: 1; min-width: 200px;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Total Orders', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="orders_total">0</div>
                                    <div class="metric-change" id="orders_total_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Average Order Value -->
                                <div class="metric-card" onclick="bm_open_metric_detail('avg_order_value')" style="cursor: pointer; flex: 1; min-width: 200px;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Average Order Value', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="orders_avg_value">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="orders_avg_value_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Total Revenue -->
                                <div class="metric-card" onclick="bm_open_metric_detail('orders_revenue')" style="cursor: pointer; flex: 1; min-width: 200px;">
                                    <div class="metric-header">
                                        <h3><?php esc_html_e( 'Total Revenue', 'service-booking' ); ?></h3>
                                    </div>
                                    <div class="metric-value" id="orders_revenue">
                                        <?php echo $currency_position == 'before' ? $currency_symbol . '0.00' : '0.00' . $currency_symbol; ?>
                                    </div>
                                    <div class="metric-change" id="orders_revenue_change">
                                        <span class="change-percent">0%</span>
                                        <span class="change-label"><?php esc_html_e( 'vs previous period', 'service-booking' ); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Chart -->
                        <div class="analytics-section">
                            <div class="chart-container full-width">
                                <div class="chart-header">
                                    <h3><?php esc_html_e( 'Orders Trend', 'service-booking' ); ?></h3>
                                    <div class="chart-legend">
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #2271b1;"></span>
                                            <?php esc_html_e( 'Current Period', 'service-booking' ); ?>
                                        </span>
                                        <span class="legend-item">
                                            <span class="legend-color" style="background-color: #8c8f94;"></span>
                                            <?php esc_html_e( 'Previous Period', 'service-booking' ); ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="chart-wrapper">
                                    <canvas id="orders_trend_chart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Orders Filters -->
                        <div class="analytics-section">
                            <div class="orders-filters" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; margin-bottom: 20px;">
                                <select id="filter_customers" multiple placeholder="<?php esc_html_e( 'Select Customers', 'service-booking' ); ?>"></select>
                                <select id="filter_services" multiple placeholder="<?php esc_html_e( 'Select Services', 'service-booking' ); ?>"></select>
                                <select id="filter_order_status" multiple placeholder="<?php esc_html_e( 'Order Status', 'service-booking' ); ?>"></select>
                                <select id="filter_payment_status" multiple placeholder="<?php esc_html_e( 'Payment Status', 'service-booking' ); ?>"></select>
                                <select id="filter_emails" multiple placeholder="<?php esc_html_e( 'Select Emails', 'service-booking' ); ?>"></select>
                                <button type="button" class="button" onclick="bm_apply_orders_filters()"><?php esc_html_e( 'Apply Filters', 'service-booking' ); ?></button>
                                <button type="button" class="button" onclick="bm_reset_orders_filters()"><?php esc_html_e( 'Reset', 'service-booking' ); ?></button>
                            </div>
                        </div>

                        <!-- Orders Table -->
                        <div class="analytics-section">
                            <div class="table-container">
                                <div class="table-header">
                                    <h3><?php esc_html_e( 'Orders List', 'service-booking' ); ?></h3>
                                    <!-- <div class="table-actions">
                                        <button type="button" class="button button-secondary" onclick="bm_download_orders_csv()">
                                            <i class="fa fa-download"></i> <?php esc_html_e( 'Download CSV', 'service-booking' ); ?>
                                        </button>
                                    </div> -->
                                </div>
                                <div class="table-wrapper">
                                    <table class="table" id="orders_table">
                                        <thead>
                                            <tr>
                                                <th><?php esc_html_e( 'Order ID', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'First Name', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Last Name', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Contact Number', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Email', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Service Participants', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Extra Service Participants', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Service Cost', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Extra Service Cost', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Discount', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Total Cost', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Order Status', 'service-booking' ); ?></th>
                                                <th><?php esc_html_e( 'Payment Status', 'service-booking' ); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody id="orders_table_body">
                                            <tr><td colspan="16"><?php esc_html_e( 'No data available', 'service-booking' ); ?></td></tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- /.tab-main -->
        </div> <!-- /.analytics-tabs -->

        <!-- ========== DETAIL VIEW CONTAINER ========== -->
        <div id="analytics-detail-view" style="display: none;">
            <div class="detail-header">
                <button type="button" class="button" onclick="bm_close_detail_view()">
                    ← <?php esc_html_e( 'Back to Overview', 'service-booking' ); ?>
                </button>
                <h2 id="detail-title"></h2>
                <div class="detail-actions">
                    <span class="detail-date-range"></span>
                    <!-- <button type="button" class="button button-secondary" onclick="bm_download_detail_csv()">
                        <i class="fa fa-download"></i> <?php esc_html_e( 'Download CSV', 'service-booking' ); ?>
                    </button> -->
                </div>
            </div>

            <div class="detail-chart-container" style="height: 300px; margin-bottom: 20px;">
                <canvas id="detail-metric-chart"></canvas>
            </div>

            <!-- Filters Bar -->
            <!-- <div class="detail-filters">
                <div id="filter-container"></div>
                <button type="button" class="button" onclick="bm_apply_filters()">
                    <?php esc_html_e( 'Filter', 'service-booking' ); ?>
                </button>
                <button type="button" class="button" onclick="bm_reset_filters()">
                    <?php esc_html_e( 'Reset', 'service-booking' ); ?>
                </button>
            </div> -->

            <!-- Column Visibility Toggle -->
            <!-- <div class="column-selector">
                <button type="button" class="button" onclick="bm_toggle_column_selector()">
                    <?php esc_html_e( 'Show/Hide Columns', 'service-booking' ); ?>
                </button>
                <div id="column-selector-dropdown" style="display: none;">
                    <ul id="column-list"></ul>
                </div>
            </div> -->

            <!-- Table Container -->
            <div class="table-responsive">
                <table class="table" id="detail-table">
                    <thead>
                        <tr id="detail-table-header"></tr>
                    </thead>
                    <tbody id="detail-table-body">
                        <tr><td colspan="20"><?php esc_html_e( 'Loading...', 'service-booking' ); ?></td></tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrap" id="detail-pagination"></div>
        </div>

        <!-- ========== HIDDEN FIELDS ========== -->
        <input type="hidden" id="current_analytics_tab" value="analytics-overview">
        <input type="hidden" id="current_detail_metric" value="">
        <input type="hidden" id="analytics_compare_type" value="period">
        <input type="hidden" id="analytics_currency_symbol" value="<?php echo esc_attr( $currency_symbol ); ?>">
        <input type="hidden" id="analytics_currency_position" value="<?php echo esc_attr( $currency_position ); ?>">

        <div class="loader_modal"></div>
    </div> <!-- /.pagewrapper -->
</div> <!-- /.container -->
