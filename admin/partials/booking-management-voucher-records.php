<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit        = !empty( $dbhandler->get_global_option_value( 'bm_voucher_records_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_voucher_records_per_page' ) : 10;
$offset       = ( ( $pagenum - 1 ) * $limit );
$i            = ( 1 + $offset );
$total        = $dbhandler->bm_count( 'VOUCHERS' );
$vouchers     = $dbhandler->get_all_result( 'VOUCHERS', '*', 1, 'results', $offset, $limit );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );

?>

<div class="sg-admin-main-box">
<!-- Vouchers -->
<div class="wrap listing_table" id="vocuher_records_listing">
    <div class="row">
        <div>
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Vouchers', 'service-booking' ); ?></h2>
            <!-- <a href="admin.php?page=bm_add_vocuher" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Voucher', 'service-booking' ); ?>"><?php esc_html_e( 'Add Voucher', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a> -->
        </div>
    </div>
    <?php if ( isset( $vouchers ) ) { ?>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Code', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Booking Info', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Gifter', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Recipient', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Expiry Date', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
                    <!-- <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th> -->
                </tr>
            </thead>
            <tbody class="vocuher_records">
                <?php
                foreach ( $vouchers as $voucher ) {
                    $settings = isset( $voucher->settings ) && !empty( $voucher->settings ) ? maybe_unserialize( $voucher->settings ) : array();
                    ?>
                    <tr>
                        <form role="form" method="post">
                            <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;" title="<?php echo isset( $voucher->code ) ? esc_html( $voucher->code ) : ''; ?>"><?php echo isset( $voucher->code ) ? esc_html( mb_strimwidth( $voucher->code, 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;">
                                <div class="linkText" id="<?php echo isset( $voucher->booking_id ) ? esc_attr( $voucher->booking_id ) : 0; ?>" onclick="bm_show_vocuher_booking_info(this)"><i class="fa fa-shopping-cart" aria-hidden="true" style="cursor:pointer;font-size:16px;"></i></div>
                            </td>
                            <td style="text-align: center;">
                                <div class="linkText" id="<?php echo isset( $voucher->booking_id ) ? esc_attr( $voucher->booking_id ) : 0; ?>" onclick="bm_show_vocuher_gifter_info(this)"><i class="fa fa-user" aria-hidden="true" style="cursor:pointer;font-size:16px;"></i></div>
                            </td>
                            <td style="text-align: center;">
                                <div class="linkText" id="<?php echo isset( $voucher->code ) ? esc_attr( $voucher->code ) : 0; ?>" onclick="bm_show_vocuher_recipient_info(this)"><i class="fa fa-user" aria-hidden="true" style="cursor:pointer;font-size:16px;"></i></div>
                            </td>
                            <td style="text-align: center;"><?php echo isset( $settings['expiry'] ) ? esc_html( $bmrequests->bm_convert_date_format( $settings['expiry'], 'Y-m-d H:i', 'd/m/y' ) ) : ''; ?></td>
                            <td style="text-align: center;" class="bm-checkbox-td">
                                <input name="bm_voucher_status" type="checkbox" id="bm_voucher_status_<?php echo esc_attr( $voucher->code ); ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $voucher->status ), '1' ); ?> onchange="bm_change_voucher_status(this)">
                                <label for="bm_voucher_status_<?php echo esc_attr( $voucher->code ); ?>"></label>
                            </td>
                            <!-- <td style="text-align: center;">
                                <button type="button" name="editprocess" class="edit-button" id="editprocess" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $process->id ) ? esc_attr( $process->id ) : 0; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" name="delprocess" class="delete-button" id="delprocess" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $process->id ) ? esc_attr( $process->id ) : 0; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                            </td> -->
                        </form>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="vocuher_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Vouchers Found', 'service-booking' ); ?></p>
            </div>
        </div>
    <?php } ?>
</div>

<input type="hidden" id="vocuher_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div id="voucher-info-dialog" title="<?php esc_html_e( 'Booking Details', 'service-booking' ); ?>" style="display: none;">
    <ul id="voucher-data"></ul>
</div>

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>


