<?php
$dbhandler    = new BM_DBhandler();
$bmrequests   = new BM_Request();
$pagenum      = filter_input( INPUT_GET, 'pagenum' );
$pagenum      = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit        = !empty( $dbhandler->get_global_option_value( 'bm_notification_processes_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_notification_processes_per_page' ) : 10;
$offset       = ( ( $pagenum - 1 ) * $limit );
$i            = ( 1 + $offset );
$total        = $dbhandler->bm_count( 'EVENTNOTIFICATION' );
$processes    = $dbhandler->get_all_result( 'EVENTNOTIFICATION', '*', 1, 'results', $offset, $limit );
$num_of_pages = ceil( $total / $limit );
$pagination   = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );

?>


<div class="sg-admin-main-box">
<!-- Processes -->
<div class="wrap listing_table" id="notification_process_records_listing">
    <div class="row">
        <div>
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Notification Processes', 'service-booking' ); ?></h2>
            <a href="admin.php?page=bm_add_notification_process" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Process', 'service-booking' ); ?>"><?php esc_html_e( 'Add Process', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
        </div>
    </div>
    <?php if ( isset( $processes ) ) { ?>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Type', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
                    <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody class="notification_process_records">
                <?php
                foreach ( $processes as $process ) {
                    ?>
                    <tr>
                        <form role="form" method="post">
                            <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;" title="<?php echo isset( $process->name ) ? esc_html( $process->name ) : ''; ?>"><?php echo isset( $process->name ) ? esc_html( mb_strimwidth( $process->name, 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;" title="<?php echo isset( $process->type ) ? esc_html( $bmrequests->bm_fetch_process_type_name_by_type_id( $process->type ) ) : ''; ?>"><?php echo isset( $process->type ) ? esc_html( mb_strimwidth( $bmrequests->bm_fetch_process_type_name_by_type_id( $process->type ), 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;" class="bm-checkbox-td">
                                <input name="bm_process_status" type="checkbox" id="bm_process_status_<?php echo esc_attr( $process->id ); ?>" data-type="<?php echo isset( $process->type ) ? esc_attr( $process->type ) : -1; ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $process->status ), '1' ); ?> onchange="bm_change_process_visibility(this)">
                                <label for="bm_process_status_<?php echo esc_attr( $process->id ); ?>"></label>
                            </td>
                            <td style="text-align: center;">
                                <button type="button" name="editprocess" class="edit-button" id="editprocess" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $process->id ) ? esc_attr( $process->id ) : 0; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
                                <button type="button" name="delprocess" class="delete-button" id="delprocess" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $process->id ) ? esc_attr( $process->id ) : 0; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
                            </td>
                        </form>
                    </tr>
                    <?php
                    $i++;
                }
                ?>
            </tbody>
        </table>
        <div class="notification_process_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Processes Found', 'service-booking' ); ?></p>
            </div>
        </div>
    <?php } ?>
</div>

<input type="hidden" id="notification_process_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>

