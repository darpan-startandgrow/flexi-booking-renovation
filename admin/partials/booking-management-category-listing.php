<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit        = !empty( $dbhandler->get_global_option_value( 'bm_categories_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_categories_per_page' ) : 10;
$offset       = ( ( $pagenum - 1 ) * $limit );
$i            = ( 1 + $offset );
$total        = $dbhandler->bm_count( 'CATEGORY' );
$categories   = $dbhandler->get_all_result( 'CATEGORY', '*', 1, 'results', $offset, $limit, 'cat_position', false );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
$cat_ids      = wp_list_pluck( $categories, 'id', 0 );
$cat_ids      = !empty( $cat_ids ) && is_array( $cat_ids ) ? implode( ',', ( array_merge( array( 0 ), $cat_ids ) ) ) : '';

?>


<div class="sg-admin-main-box">
<!-- Categories -->
<div class="wrap listing_table" id="category_records_listing">
    <div class="row">
        <span style="display: inline-block;width:50%;">
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'All Categories', 'service-booking' ); ?></h2>
            <a href="admin.php?page=bm_add_category" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Category', 'service-booking' ); ?>"><?php esc_html_e( 'Add Category', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
        </span>
        <?php if ( isset( $categories ) && !empty( $categories ) ) { ?>
            <span class="copyMessagetooltip allShortcode categoryShortcode" style="float:right;">
                <h2 class="title" style="font-weight: bold;margin-left:8px;"><?php esc_html_e( 'Shortcode with multiple category ids ', 'service-booking' ); ?></h2>
                <input class="copytextTooltip overallCategoryShortcode" value="<?php echo esc_html( '[sgbm_service_by_category ids="' . $cat_ids . '"]' ); ?>" id="copyInput_0" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" style="width:100%;" readonly>
                <span class="tooltiptext" id="copyTooltip_0"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
            </span>
        <?php } else { ?>
            <span class="copyMessagetooltip allShortcode categoryShortcode" style="float:right;">
                <h2 class="title" style="font-weight: bold;margin-left:8px;"><?php esc_html_e( 'Shortcode with multiple category ids ', 'service-booking' ); ?></h2>
                <input class="copytextTooltip overallCategoryShortcode" value="<?php echo esc_html( '[sgbm_service_by_category ids="' . esc_attr( 0 ) . '"]' ); ?>" id="copyInput_0" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" style="width:100%;" readonly>
                <span class="tooltiptext" id="copyTooltip_0"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
            </span>
        <?php } ?>
    </div>
    <?php if ( isset( $categories ) && !empty( $categories ) ) { ?>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Show in Frontend', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Single category shortcode', 'service-booking' ); ?></th>
                    <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody class="category_records">
                <?php
                foreach ( $categories as $category ) {
                    ?>
                    <tr class="single_category_record">
                        <form role="form" method="post">
                            <td style="text-align: center;cursor:move;" data-id="<?php echo esc_attr( $category->id ); ?>" data-order="<?php echo esc_attr( $i ); ?>" class="category_listing_number"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;cursor:move;" title="<?php echo isset( $category->cat_name ) ? esc_html( $category->cat_name ) : ''; ?> "><?php echo isset( $category->cat_name ) ? esc_html( mb_strimwidth( $category->cat_name, 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;" class="bm-checkbox-td">
                                <input name="bm_show_category_in_front" type="checkbox" id="bm_show_category_in_front_<?php echo esc_attr( $category->id ); ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $category->cat_in_front ), '1' ); ?> onchange="bm_change_category_visibility(this)">
                                <label for="bm_show_category_in_front_<?php echo esc_attr( $category->id ); ?>"></label>
                            </td>
                            <td style="text-align: center;">
                                <div class="copyMessagetooltip">
                                    <input class="copytextTooltip" value="<?php echo esc_html( '[sgbm_service_by_category ids="' . esc_attr( $category->id ) . '"]' ); ?>" id="copyInput_<?php echo esc_attr( $category->id ); ?>" onclick="bm_copy_text(this)" onmouseout="bm_copy_message(this)" readonly>
                                    <span class="tooltiptext" id="copyTooltip_<?php echo esc_attr( $category->id ); ?>"><?php esc_html_e( 'Copy to clipboard', 'service-booking' ); ?></span>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" name="editcat" class="edit-button" id="editcat" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $category->id ) ? esc_attr( $category->id ) : ''; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" name="delcat" class="delete-button" id="delcat" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $category->id ) ? esc_attr( $category->id ) : ''; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                            </td>
                        </form>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="category_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Categories Found', 'service-booking' ); ?></p>
            </div>
        </div>
		<?php
    }// end if
    ?>
</div>

<input type="hidden" id="category_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__swing" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>

