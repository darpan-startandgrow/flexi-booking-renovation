<?php
$dbhandler     = new BM_DBhandler();
$bmrequests    = new BM_Request();
$pagenum       = filter_input( INPUT_GET, 'pagenum' );
$pagenum       = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit         = !empty( $dbhandler->get_global_option_value( 'bm_price_modules_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_price_modules_per_page' ) : 10;
$offset        = ( ( $pagenum - 1 ) * $limit );
$i             = ( 1 + $offset );
$total         = $dbhandler->bm_count( 'EXTERNAL_SERVICE_PRICE_MODULE' );
$price_modules = $dbhandler->get_all_result( 'EXTERNAL_SERVICE_PRICE_MODULE', '*', 1, 'results', $offset, $limit );
$num_of_pages  = ceil( $total / $limit );
$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );

?>


<div class="sg-admin-main-box">
<!-- Categories -->
<div class="wrap listing_table" id="price_module_records_listing">
    <div class="row">
        <div>
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'All Price Modules', 'service-booking' ); ?></h2>
            <a href="admin.php?page=bm_add_external_service_price" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Price Module', 'service-booking' ); ?>"><?php esc_html_e( 'Add Price Module', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <?php if ( !empty( $price_modules ) ) { ?>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                    <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody class="price_module_records">
                <?php
                foreach ( $price_modules as $price_module ) {
                    ?>
                    <tr class="single_price_module_record">
                        <form role="form" method="post">
                            <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;" title="<?php echo isset( $price_module->module_name ) ? esc_html( $price_module->module_name ) : ''; ?>"><?php echo isset( $price_module->module_name ) ? esc_html( mb_strimwidth( $price_module->module_name, 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;">
                                <button type="button" name="editmodule" class="edit-button" id="editmodule" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $price_module->id ) ? esc_attr( $price_module->id ) : 0; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" name="delmodule" class="delete-button" id="delmodule" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $price_module->id ) ? esc_attr( $price_module->id ) : 0; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                            </td>
                        </form>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="price_module_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Price Modules Found', 'service-booking' ); ?></p>
            </div>
        </div>
		<?php
    }// end if
    ?>
</div>

<input type="hidden" id="price_module_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>

