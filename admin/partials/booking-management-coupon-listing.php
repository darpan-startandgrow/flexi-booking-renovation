<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit_param  = filter_input( INPUT_GET, 'limit', FILTER_VALIDATE_INT );
$limit        = $limit_param ? $limit_param : ( !empty( $dbhandler->get_global_option_value( 'bm_coupon_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_coupon_per_page' ) : 10 );
$offset       = ( ( $pagenum - 1 ) * $limit );
$i            = ( 1 + $offset );
$total        = $dbhandler->bm_count( 'COUPON' );
$coupons      = $dbhandler->get_all_result( 'COUPON', '*', 1, 'results', $offset, $limit, 'id', false );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
?>

<div class="sg-admin-main-box">
    <div class="wrap listing_table">
        <div class="row">
            <span style="display: inline-block;width:50%;">
                <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'All Coupons', 'service-booking' ); ?></h2>
                <a href="admin.php?page=bm_add_coupon" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Coupon', 'service-booking' ); ?>"><?php esc_html_e( 'Add Coupon', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
            </span>
        </div>
        <?php if ( isset( $coupons ) && !empty( $coupons ) ) { ?>
            <!-- Bulk Actions Bar -->
            <div class="bm-bulk-bar" data-table="coupon" style="margin-bottom:10px;padding:8px 12px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <select class="bm-bulk-action-select" data-table="coupon" style="min-width:180px;">
                    <option value=""><?php esc_html_e( '— Bulk Actions —', 'service-booking' ); ?></option>
                    <option value="bulk_delete"><?php esc_html_e( 'Delete Selected', 'service-booking' ); ?></option>
                    <option value="bulk_toggle_status"><?php esc_html_e( 'Toggle Status', 'service-booking' ); ?></option>
                </select>
                <span class="bm-bulk-status-wrap" style="display:none;">
                    <select class="bm-bulk-status-val">
                        <option value="1"><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
                        <option value="0"><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
                    </select>
                </span>
                <button type="button" class="button button-primary bm-bulk-apply" data-table="coupon" disabled><?php esc_html_e( 'Apply', 'service-booking' ); ?></button>
                <span class="bm-bulk-count" style="color:#666;font-size:12px;margin-left:8px;"></span>
                
                <!-- Dynamic Pagination -->
                <div class="bm-dynamic-pagination" style="margin-left:auto;display:flex;align-items:center;gap:6px;">
                    <label for="coupon_items_per_page" style="font-size:13px;color:#3c434a;"><?php esc_html_e( 'Items per page:', 'service-booking' ); ?></label>
                    <select id="coupon_items_per_page" name="coupon_items_per_page" style="min-width:80px;">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
            </div>
            <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th style="text-align:center;width:30px;"><input type="checkbox" class="bm-bulk-check-all" data-table="coupon" title="<?php esc_attr_e( 'Select All', 'service-booking' ); ?>"></th>
                        <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Coupon Code', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Expiry Date', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Coupon Type', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Discount Type', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Amount', 'service-booking' ); ?></th>
                        <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                    </tr>
                </thead>
                <tbody class="coupon_records">
                    <?php
                    foreach ( $coupons as $coupon ) {
						?>
                        <tr class="single_coupon_record">
                            <form role="form" method="post">
                                <td style="text-align:center;"><input type="checkbox" class="bm-bulk-row-check coupon-row-check" data-table="coupon" value="<?php echo esc_attr( $coupon->id ); ?>"></td>
                                <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                                <td style="text-align: center;"><?php echo esc_attr( $coupon->coupon_code ); ?></td>
                                <td style="text-align: center;"><?php echo !empty( $coupon->expiry_date ) ? esc_attr( $coupon->expiry_date ) : '-'; ?></td>
                                <td style="text-align: center;"><?php echo isset( $coupon ) && !empty( $coupon->is_event_coupon ) && ( $coupon->is_event_coupon == 1 ) ? 'Event' : 'Normal'; ?></td>
                                <td style="text-align: center;"><?php echo isset( $coupon->is_active ) && ( $coupon->is_active == 1 ) ? esc_attr( 'Active' ) : esc_attr( 'Inactive' ); ?></td>
                                <td style="text-align: center;"><?php echo esc_attr( $coupon->discount_type ); ?></td>
                                <td style="text-align: center;"><?php echo esc_attr( $coupon->discount_amount ); ?></td>
                                <td style="text-align: center;">
                                    <button type="button" name="editcoupon" class="edit-button" id="editcoupon" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $coupon->id ) ? esc_attr( $coupon->id ) : ''; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                    <button type="button" name="delcpn" class="delete-button" id="delcpn" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $coupon->id ) ? esc_attr( $coupon->id ) : ''; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                                </td>
                            </form>
                        </tr>
						<?php
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
            <?php echo wp_kses_post( $pagination ?? '' ); ?>
        <?php } else { ?>
            <div class="bm_no_records_message">
                <div class="Pointer">
                    <p class="message"><?php esc_html_e( 'No Coupon Found', 'service-booking' ); ?></p>
                </div>
            </div>
			<?php
        } //end if
        ?>
    </div>

    <input type="hidden" id="coupon_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
    <input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

    <div class="loader_modal"></div>
</div>
