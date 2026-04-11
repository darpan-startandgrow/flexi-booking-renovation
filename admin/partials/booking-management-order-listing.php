<?php
$dbhandler             = new BM_DBhandler();
$bmrequests            = new BM_Request();
$woocommerceservice    = new WooCommerceService();
$pagenum               = filter_input( INPUT_GET, 'pagenum' );
$pagenum               = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit                 = !empty( $dbhandler->get_global_option_value( 'bm_orders_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_orders_per_page' ) : 10;
$offset                = ( ( $pagenum - 1 ) * $limit );
$i                     = ( 1 + $offset );
$total                 = 0;
$user_id               = get_current_user_id();
$failed_order_option   = $dbhandler->get_global_option_value( "show_backend_order_page_failed_orders_$user_id", 0 );
$archived_order_option = $dbhandler->get_global_option_value( "show_backend_order_page_archived_orders_$user_id", 0 );
$booking_data          = $dbhandler->get_all_result( 'BOOKING', '*', 1, 'results' );
$total                 = $dbhandler->bm_count( 'BOOKING' );

if ( $failed_order_option == 1 ) {
    $total = $dbhandler->bm_count( 'FAILED_TRANSACTIONS' );
} elseif ( $archived_order_option == 1 ) {
    $total = $dbhandler->bm_count( 'BOOKING_ARCHIVE' );
}

$num_of_pages   = ceil( $total / $limit );
$pagination     = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
$active_columns = $bmrequests->bm_fetch_active_columns( 'orders' );
$column_values  = $bmrequests->bm_fetch_column_order_and_names( 'orders' );
$plugin_path    = plugin_dir_url( __FILE__ );

/**if ( $woocommerceservice->is_enabled() ) {
    $order_statuses = wc_get_order_statuses();
} else {
    $order_statuses = $bmrequests->bm_fetch_order_status_key_value();
}*/

$order_statuses   = $bmrequests->bm_fetch_order_status_key_value();
$payment_statuses = $bmrequests->bm_fetch_payment_statuses();

$services = $dbhandler->get_results_with_join(
    array( 'SERVICE', 's' ),
    's.id, s.service_name, s.service_category',
    array(
        array(
            'table' => 'CATEGORY',
            'alias' => 'c',
            'on'    => 's.service_category = c.id',
            'type'  => 'LEFT',
        ),
    ),
    array( 's.is_service_front' => array( '=' => 1 ) ),
    'results',
    0,
    false,
    's.id',
    'DESC',
    'AND (c.cat_in_front = 1 OR s.service_category = 0)'
);

$categories = !empty( $services ) ? array_values( array_unique( array_column( $services, 'service_category' ) ) ) : array();

unset( $order_statuses['failed'], $payment_statuses['failed'] );

?>

<!-- Orders -->
<div class="sg-admin-main-box order-listing-admin-main-box">
<div class="wrap listing_table">
    <div class="order_listing_top">
        <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'All Orders', 'service-booking' ); ?></h2>
        <span style="float: left;">
            <a href="admin.php?page=bm_add_order" class="button button-primary" title="<?php esc_html_e( 'Add Order', 'service-booking' ); ?>"><?php esc_html_e( 'Add Order', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
        </span>

        <?php if ( !empty( $booking_data ) && is_array( $booking_data ) ) { ?>
            <span style="margin-left: 5px;">
                <a href="javascript:void(0);" class="button button-primary" title="<?php esc_html_e( 'Advanced search', 'service-booking' ); ?>" onclick="bm_show_search_box('order_advanced_search_box')"><?php esc_html_e( 'Advanced search', 'service-booking' ); ?>&nbsp;<i class="fa fa-search" aria-hidden="true"></i></a>
            </span>
            &nbsp;&nbsp;
            <span class="order-type-filter">
                <select name="order_type" id="order_type" onchange="bm_show_hide_respective_orders(this)">
                    <option value="all-non-failed"><?php esc_html_e( 'All Orders (non-failed)', 'service-booking' ); ?></option>
                    <option value="failed" <?php selected( $failed_order_option, '1' ); ?>><?php esc_html_e( 'Failed Orders', 'service-booking' ); ?></option>
                    <option value="archived" <?php selected( $archived_order_option, '1' ); ?>><?php esc_html_e( 'Archived Orders', 'service-booking' ); ?></option>
                </select>
            </span>
       
            <div class="sg-filter-bar" style="float: right; height: 45px;">
                <span class="tab-box">
                    <span class="inputgroup sg-search-box" style="position: relative;">
                        <input type="text" id="global_search" class="textbox" placeholder="<?php esc_html_e( 'Search', 'service-booking' ); ?>" autocomplete="off" />
                        <i class="fa fa-search order_listing_search_icon" id="order_listing_search_icon" data-title="<?php esc_html_e( 'Click to search', 'service-booking' ); ?>"></i>
                    </span>
                </span>
                <a href="javascript:void(0);" class="button button-primary edit_order_columns" title="<?php esc_html_e( 'Manage Columns', 'service-booking' ); ?>">
                    <span>
                        <?php esc_html_e( 'Manage Columns', 'service-booking' ); ?>
                        <i class="fa fa-plus" aria-hidden="true" style="color:#fff;"></i>
                    </span>
                </a>
                <a href="javascript:void(0);" class="button button-primary export_order_records" title="<?php esc_html_e( 'Csv Export', 'service-booking' ); ?>">
                    <span>
                        <?php esc_html_e( 'Csv Export', 'service-booking' ); ?>
                        <img src="<?php echo esc_url( $plugin_path . 'images/export.png' ); ?>" class="options" alt="options" width="15px" height="15px" style="position:relative;top:3px;">
                    </span>
                </a>
            </div>

            <div class="order_advanced_search_box" id="order_advanced_search_box" style="display: none;">
                <span class="service_date_search_span">
                    <span>
                        <input type="text" id="service_from" name="service_from" placeholder='<?php esc_html_e( 'from service date', 'service-booking' ); ?>' title="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                    <span>
                        <input type="text" id="service_to" name="service_to" placeholder='<?php esc_html_e( 'to service date', 'service-booking' ); ?>' title="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                </span>
                <span class="ordered_date_search_span">
                    <span>
                        <input type="text" id="order_from" name="order_from" placeholder='<?php esc_html_e( 'from order date', 'service-booking' ); ?>' title="<?php esc_html_e( 'from order date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                    <span>
                        <input type="text" id="order_to" name="order_to" placeholder='<?php esc_html_e( 'to order date', 'service-booking' ); ?>' title="<?php esc_html_e( 'to order date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                </span>
                <?php if ( $failed_order_option != 1 ) { ?>
                    <span class="status_search_span">
                        <select id="order_status_filter" name="order_status_filter[]" multiple="multiple">
                            <?php foreach ( $order_statuses as $status_key => $status_name ) : ?>
                                <option value="<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </span>
                    <span class="payment_status_search_span">
                        <select id="payment_status_filter" name="payment_status_filter[]" multiple="multiple">
                            <?php foreach ( $payment_statuses as $status_key => $status_name ) : ?>
                                <option value="<?php echo esc_attr( $status_key ); ?>"><?php echo esc_html( $status_name ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </span>
                    <span class="service_search_span">
                        <select id="service_filter" name="service_filter[]" multiple="multiple">
                            <?php
                            if ( !empty( $services ) ) {
                                foreach ( $services as $service ) {
									?>
                                    <option value="<?php echo esc_attr( $service->id ); ?>"><?php echo isset( $service->service_name ) ? esc_html( $service->service_name ) : ''; ?></option>
									<?php
                                }
                            }
							?>
                        </select>
                    </span>
                    <span class="category_search_span">
                        <select id="category_filter" name="category_filter[]" multiple="multiple">
                            <?php
                            if ( !empty( $categories ) ) {
                                foreach ( $categories as $key => $category ) {
                                    $category_name = $bmrequests->bm_fetch_category_name_by_category_id( $category );
                                    ?>
                            <option value="<?php echo esc_html( $category ); ?>"><?php echo isset( $category_name ) ? esc_html( mb_strimwidth( $category_name, 0, 24, '...' ) ) : ''; ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </span>
					<?php }; ?>
                <span class="order-sourder-filter">
                    <select name="order_source_filter" id="order_source_filter">
                        <option value=""><?php esc_html_e( 'All Orders', 'service-booking' ); ?></option>
                        <option value="frontend"><?php esc_html_e( 'Frontend Orders', 'service-booking' ); ?></option>
                        <option value="backend"><?php esc_html_e( 'Backend Orders', 'service-booking' ); ?></option>
                    </select>
                </span>
                <span class="advncd-search-btns">
                    <button type="button" class="button button-primary" id="date_search_button" title="<?php esc_html_e( 'Search', 'service-booking' ); ?>"><i class="fa fa-search"></i></button>
                    <button type="button" class="button" id="reset_date_search" title="<?php esc_html_e( 'Reset', 'service-booking' ); ?>"><i class="fa fa-refresh"></i></button>
                </span>
            </div>
			<?php
		}//end if
		?>
    </div>

									<?php if ( isset( $booking_data ) && !empty( $booking_data ) ) { ?>
        <div class="order_listing-details table-wrapper">
        <table class="wp-list-table widefat striped booking-table" id="order_listing">
            <thead>
                <tr>
										<?php
										if ( !empty( $column_values ) ) {
											foreach ( $column_values as $key => $column ) {
												if ( isset( $active_columns ) && !in_array( $key, $active_columns ) ) {
													continue;
												}

												$skip_sort  = in_array( $column['column'], array( 'actions', 'order_attachments', 'customer_data' ) );
												$sort_class = '';
												$sort_dir   = 'asc';

												if ( !$skip_sort ) {
													$sort_class = 'sortable';
													if ( isset( $_GET['orderby'] ) && $_GET['orderby'] === $column['column'] ) {
														$sort_class .= ' sorted';
														$sort_dir    = isset( $_GET['order'] ) && $_GET['order'] === 'asc' ? 'desc' : 'asc';
														$sort_class .= ' ' . $_GET['order'];
													}
												}
												?>
                <th class="<?php echo esc_attr( $sort_class ); ?>" 
                    style="text-align: center;font-weight: 600;<?php echo !$skip_sort ? 'cursor: pointer;' : ''; ?>"
												<?php if ( !$skip_sort ) : ?>
                        data-column="<?php echo esc_attr( $column['column'] ); ?>"
                        data-order="<?php echo esc_attr( $sort_dir ); ?>"
                        onclick="bm_sort_orders('<?php echo esc_js( $column['column'] ); ?>', '<?php echo esc_js( $sort_dir ); ?>')"
                    <?php endif; ?>>
												<?php echo esc_html( $key ); ?>
												<?php if ( !$skip_sort && isset( $_GET['orderby'] ) && $_GET['orderby'] === $column['column'] ) : ?>
                        <span class="sorting-indicator"></span>
                    <?php endif; ?>
                </th>
												<?php
											}
										} else {
											?>
                        <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Date', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'First name', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Last name', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Contact number', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Email', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total Cost', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Customer Data', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered From', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order Status', 'service-booking' ); ?></th>
                        <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
											<?php
										}//end if
										?>
                </tr>
             <thead>
            <tbody class="order_records"></tbody>
        </table>
       </div>

        <div id="order_pagination">
										<?php echo !empty( $pagination ) ? wp_kses_post( $pagination ?? '' ) : ''; ?>
        </div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Orders Found', 'service-booking' ); ?></p>
            </div>
        </div>
										<?php
    }//end if
    ?>
</div>

<input type="hidden" name="pagenum" id="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />
<input type="hidden" id="total_pages" value="<?php echo esc_attr( $num_of_pages ); ?>" />
<input type="hidden" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />

<div id="customer-dialog" title="<?php esc_html_e( 'Customer Details', 'service-booking' ); ?>" style="display: none;">
    <ul id="customer-list"></ul>
</div>

<div id="order-attachments-dialog" title="<?php esc_html_e( 'Order Attachments', 'service-booking' ); ?>" style="display: none;">
    <ul id="attachments-list"></ul>
</div>

<div id="order_columns_modal" class="modaloverlay">
    <div class="modal manageorderboxmodal animate__animated animate__flipInX">
        <span class="close" onclick="closeModal('order_columns_modal')">&times;</span>
        <h4 style="background:#5EA8ED ; margin:0px; padding:12px;color:#fff;font-size:16px;"><?php esc_html_e( 'Select Columns', 'service-booking' ); ?></h4>
        <div class="modalcontentbox2 manageorderbox modal-body" id="order_columns"></div>
        <div class="bookbtnbar">
            <div class="bookbtn bgcolor textwhite text-center" id="order_column_button">
                <a href="#" id="column_button_tag" class="submit_columns"><?php esc_html_e( 'Save', 'service-booking' ); ?></a>
            </div>
        </div>
        <div class="column_errortext" style="display :none;"></div>
    </div>
</div>

<div id="order_export_modal" class="modaloverlay2">
    <div class="modal animate__animated animate__flipInX">
        <span class="close" onclick="closeModal('order_export_modal')">&times;</span>
        <h2 style="background:#5EA8ED ; margin:0px; padding:12px;color:#fff;font-size:18px;text-align: center;"><?php esc_html_e( 'Export Orders', 'service-booking' ); ?></h2>
        <div class="modalcontentbox modal-body" id="export_orders"></div>
        <div style="margin-bottom:10px;text-align:center;">
            <button type="button" class="button-primary" id="exportButton">
                <span id="buttonText">
									<?php esc_html_e( 'Export', 'service-booking' ); ?>
                </span>
            </button>
            <div id="resendProcess" class="hidden">
                <img src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">
            </div>
        </div>
    </div>
</div>

<div id="edit_transactions_modal" class="modaloverlay">
    <div class="modal animate__animated animate__flipInX">
        <span class="close" onclick="closeModal('edit_transactions_modal')">&times;</span>
        <h2 style="font-size:18px;text-align: center;"><?php esc_html_e( 'Edit Transaction', 'service-booking' ); ?></h2>
        <div class="modalcontentbox modal-body" id="edit_transaction"></div>
        <span class="edit_transactions_errortext" style="display:none;margin-bottom:5px;text-align:center"></span>
        <div style="margin-bottom:10px;text-align:center;">
            <button type="button" class="button-primary" id="save_trans_button" onclick="bm_save_order_transaction()">
                <span id="buttonText">
									<?php esc_html_e( 'Save', 'service-booking' ); ?>
                </span>
            </button>
            <div id="resendProcess" class="hidden">
                <img id="save_transaction_loader" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">
            </div>
        </div>
    </div>
</div>

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__flipInX" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>






