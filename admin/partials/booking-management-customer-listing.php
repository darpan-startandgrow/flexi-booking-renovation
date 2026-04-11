<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit        = !empty( $dbhandler->get_global_option_value( 'bm_customers_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_customers_per_page' ) : 10;
$offset       = ( ( $pagenum - 1 ) * $limit );
$i            = ( 1 + $offset );
$total        = $dbhandler->bm_count( 'CUSTOMERS' );
$customers    = $dbhandler->get_all_result( 'CUSTOMERS', '*', 1, 'results', $offset, $limit, 'id', 'Desc' );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );

?>


<div class="sg-admin-main-box">
<!-- Customers -->
<div class="wrap listing_table" id="customer_records_listing">
    <div class="row">
        <span style="display: inline-block;width:50%;">
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'All Customers', 'service-booking' ); ?></h2>
            <a href="admin.php?page=bm_add_customer" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Customer', 'service-booking' ); ?>"><?php esc_html_e( 'Add Customer', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
        </span>
    </div>
    <?php if ( isset( $customers ) && !empty( $customers ) ) { ?>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Email', 'service-booking' ); ?></th>
                    <th width="20%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Profile', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
                    <!-- <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Profile shortcode', 'service-booking' ); ?></th> -->
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody class="customer_records">
                <?php
                foreach ( $customers as $customer ) {
                    ?>
                    <tr class="single_customer_record">
                        <form role="form" method="post">
                            <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;" title="<?php echo isset( $customer->customer_name ) ? esc_html( $customer->customer_name ) : ''; ?> "><?php echo isset( $customer->customer_name ) ? esc_html( mb_strimwidth( $customer->customer_name, 0, 60, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;" title="<?php echo isset( $customer->customer_email ) ? esc_html( $customer->customer_email ) : ''; ?> "><?php echo isset( $customer->customer_email ) ? esc_html( mb_strimwidth( $customer->customer_email, 0, 60, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;cursor:pointer;" title="<?php echo esc_html__( 'Check profile', 'service-booking' ); ?>"><a href="admin.php?page=bm_customer_profile&id=<?php echo esc_attr( $customer->id ); ?>"><i class="fa fa-user-circle-o" style="font-size:18px;vertical-align: middle;"></i></a></td>
                            <td style="text-align: center;" class="bm-checkbox-td">
                                <input name="customer_is_active" type="checkbox" id="customer_is_active_<?php echo esc_attr( $customer->id ); ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $customer->is_active ), '1' ); ?> onchange="bm_change_customer_visibility(this)">
                                <label for="customer_is_active_<?php echo esc_attr( $customer->id ); ?>"></label>
                            </td>
                            <!-- <td style="text-align: center;">
                                <div class="copyMessagetooltip">
                                    <input class="copytextTooltip" value="<?php echo esc_html( '[sgbm_customer_profile id="' . esc_attr( $customer->id ) . '"]' ); ?>" id="copyInput_<?php echo esc_attr( $customer->id ); ?>" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                                    <span class="tooltiptext" id="copyTooltip_<?php echo esc_attr( $customer->id ); ?>"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                                </div>
                            </td> -->
                            <td style="text-align: center;">
                                <button type="button" name="editcust" class="edit-button" id="editcust" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $customer->id ) ? esc_attr( $customer->id ) : ''; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <!-- <button type="button" name="delcust" class="delete-button" id="delcust" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $customer->id ) ? esc_attr( $customer->id ) : ''; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button> -->
                            </td>
                        </form>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="customer_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Customers Found', 'service-booking' ); ?></p>
            </div>
        </div>
		<?php
    }// end if
    ?>
</div>

<input type="hidden" id="customer_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__swing" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>

