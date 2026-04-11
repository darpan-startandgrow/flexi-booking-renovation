<?php
$dbhandler     = new BM_DBhandler();
$bmrequests    = new BM_Request();
$pagenum       = filter_input( INPUT_GET, 'pagenum' );
$pagenum       = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit         = !empty( $dbhandler->get_global_option_value( 'bm_email_records_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_email_records_per_page' ) : 10;
$offset        = ( ( $pagenum - 1 ) * $limit );
$i             = ( 1 + $offset );
$total_records = $dbhandler->get_all_result( 'EMAILS', '*', 1, 'results' );
$total_records = !empty( $total_records ) && is_array( $total_records ) ? $dbhandler->bm_group_data_by_column( $total_records, array( 'module_type', 'module_id', 'mail_type' ) ) : array();
$email_records = !empty( $total_records ) && is_array( $total_records ) ? $dbhandler->bm_apply_offset_limit_and_sort_existing_data( $total_records, $offset, $limit, true, 'id', 'DESC' ) : array();
$total         = !empty( $total_records ) && is_array( $total_records ) ? count( $total_records ) : 0;
$num_of_pages  = ceil( $total / $limit );
$pagination    = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
$plugin_path   = plugin_dir_url( __FILE__ );

$resend_email_content = array(
    'wpautop'           => false,
    'media_buttons'     => true,
    'textarea_name'     => 'resend_email_body',
    'textarea_rows'     => 20,
    'tabindex'          => 4,
    'editor_height'     => 150,
    'tabfocus_elements' => ':prev,:next',
    'editor_css'        => '',
    'editor_class'      => '',
    'teeny'             => false,
    'dfw'               => false,
    'tinymce'           => true,
    'quicktags'         => true,
);

add_action( 'media_buttons', array( $this, 'bm_fields_list_for_email' ) );
/**add_action( 'media_buttons', array( $this, 'bm_add_mail_attachment' ) );*/
?>

<div class="sg-admin-main-box" id="email-records-main-box">
<!-- Email Records -->
<div class="wrap listing_table">
    <div class="row">
        <div>
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Email Records', 'service-booking' ); ?></h2>
        </div>
    </div>
    <?php if ( isset( $email_records ) && !empty( $email_records ) ) { ?>
        <input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'type', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order details', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Mail body', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Mail sent status', 'service-booking' ); ?></th>
                    <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Total mails sent', 'service-booking' ); ?></th>
                    <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ( $email_records as $key => $email ) {
                    ?>
                    <tr>
                        <form role="form" method="post">
                            <input type="hidden" id="email_id_<?php echo esc_attr( $key ); ?>" value="<?php echo isset( $email->id ) ? esc_attr( $email->id ) : 0; ?>" />
                            <input type="hidden" id="module_id_<?php echo esc_attr( $key ); ?>" value="<?php echo isset( $email->module_id ) ? esc_attr( $email->module_id ) : 0; ?>" />
                            <input type="hidden" id="module_type_<?php echo esc_attr( $key ); ?>" value="<?php echo isset( $email->module_type ) ? esc_html( $email->module_type ) : ''; ?>" />
                            <input type="hidden" id="mail_type_<?php echo esc_attr( $key ); ?>" value="<?php echo isset( $email->mail_type ) ? esc_html( $email->mail_type ) : 0; ?>" />
                            <input type="hidden" id="template_id_<?php echo esc_attr( $key ); ?>" value="<?php echo isset( $email->template_id ) ? esc_attr( $email->template_id ) : 0; ?>" />
                            <input type="hidden" id="process_id_<?php echo esc_attr( $key ); ?>" value="<?php echo isset( $email->process_id ) ? esc_attr( $email->process_id ) : 0; ?>" />
                            <td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
                            <td style="text-align: center;width:15%;" title="<?php echo isset( $email->mail_type ) ? esc_html( $bmrequests->bm_fetch_email_type( $email->mail_type ) ) : ''; ?>"><?php echo isset( $email->mail_type ) ? esc_html( mb_strimwidth( $bmrequests->bm_fetch_email_type( $email->mail_type ), 0, 40, '...' ) ) : ''; ?></td>
                            <td style="text-align: center;">
                                <div class="linkText" id="<?php echo isset( $email->id ) ? esc_attr( $email->id ) : 0; ?>" onclick="bm_show_mail_details(this)"><i class="fa fa-eye" aria-hidden="true" style="cursor:pointer;font-size:16px;"></i></div>
                            </td>
                            <td style="text-align: center;">
                                <div class="linkText" id="<?php echo isset( $email->id ) ? esc_attr( $email->id ) : 0; ?>" onclick="bm_show_email_body(this)"><i class="fa fa-envelope" aria-hidden="true" style="cursor:pointer;font-size:16px;"></i></div>
                            </td>
                            <td style="text-align: center;width:25%;">
                                <?php
                                $module_type = isset( $email->module_type ) ? esc_html( $email->module_type ) : '';
                                $mail_status = $dbhandler->get_value( $module_type, 'mail_sent', ( isset( $email->module_id ) ? esc_attr( $email->module_id ) : 0 ), 'id' );
                                $bmrequests->bm_fetch_email_status( $mail_status );
								?>
                            </td>
                            <td style="text-align: center;">
                                <?php
                                $module_type  = isset( $email->module_type ) ? esc_html( $email->module_type ) : '';
                                $module_id    = isset( $email->module_id ) ? esc_html( $email->module_id ) : 0;
                                $mail_type    = isset( $email->mail_type ) ? esc_html( $email->mail_type ) : '';
                                $total_emails = $dbhandler->get_all_result(
                                    'EMAILS',
                                    'id',
                                    array(
										'module_type' => $module_type,
										'module_id'   => $module_id,
										'mail_type'   => $mail_type,
                                    ),
                                    'results'
                                );

                                echo esc_attr( !empty( $total_emails ) && is_array( $total_emails ) ? count( $total_emails ) : 0 );
								?>
                            </td>
                            <td style="text-align: center;">
                                <div title="<?php esc_html_e( 'resend', 'service-booking' ); ?>" id="<?php echo isset( $email->id ) ? esc_attr( $email->id ) : 0; ?>" data-id="<?php echo esc_attr( $key ); ?>" onclick="bm_open_email_body(this)"><i class="fa fa-refresh" aria-hidden="true" style="cursor:pointer;font-size:16px;"></i></div>
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
                <p class="message"><?php esc_html_e( 'No Emails Found', 'service-booking' ); ?></p>
            </div>
        </div>
    <?php } ?>
</div>



<div id="email_details_modal" class="modaloverlay ">
    <div class="modal animate__animated animate__swing">
        <span class="close" onclick="closeModal('email_details_modal')">&times;</span>
        <h4  style="background:#5EA8ED ; margin:0px; padding:12px 24px;color:#fff;font-size:16px;"><?php esc_html_e( 'Details', 'service-booking' ); ?></h4>
        <div class="modalcontentbox2 modal-body" id="email_details"></div>
        <div class="loader_modal"></div>
    </div>
</div>

<div id="email_body_modal" class="modaloverlay">
    <div class="modal animate__animated animate__swing">
        <span class="close" onclick="closeModal('email_body_modal')">&times;</span>
        <h4 style="background:#5EA8ED ; margin:0px; padding:12px;color:#fff;font-size:16px;"><?php esc_html_e( 'Sent mail body', 'service-booking' ); ?></h4>
        <div class="modalcontentbox2 modal-body" id="email_body"></div>
        <div class="loader_modal"></div>
    </div>
</div>

<div id="resend_email_modal" class="modaloverlay">
    <div class="modal animate__animated animate__swing">
        <span style="color:#000;" class="close" onclick="closeModal('resend_email_modal')">&times;</span>
        <div class="modalcontentbox3 modal-body" id="resend_email">
            <div class="email-container">
                <div class="header">
                    <h2><?php esc_html_e( 'Resend mail', 'service-booking' ); ?></h2>
                </div>
                <div class="email-content-box">
                    <div class="input-group">
                        <label for="resend_mail_to"><?php esc_html_e( 'To:', 'service-booking' ); ?></label>
                        <input type="text" id="resend_mail_to" val="" title="<?php esc_html_e( 'add single or comma separated emails', 'service-booking' ); ?>">
                    </div>
                    <div class="input-group">
                        <label for="resend_mail_subject"><?php esc_html_e( 'Subject:', 'service-booking' ); ?></label>
                        <input type="text"  id="resend_mail_subject" val="">
                        <div class="cc-bcc-buttons">
                            <button type="button" onclick="bm_remove_hidden_class('cc-input')"><?php esc_html_e( 'CC', 'service-booking' ); ?></button>
                            <button type="button" onclick="bm_remove_hidden_class('bcc-input')"><?php esc_html_e( 'BCC', 'service-booking' ); ?></button>
                        </div>
                    </div>
                    <div class="input-group hidden" id="cc-input">
                        <label for="mail_cc"><?php esc_html_e( 'Cc:', 'service-booking' ); ?></label>
                        <input type="text" id="mail_cc" val="" title="<?php esc_html_e( 'add single or comma separated emails', 'service-booking' ); ?>">
                    </div>
                    <div class="input-group hidden" id="bcc-input">
                        <label for="mail_bcc"><?php esc_html_e( 'Bcc:', 'service-booking' ); ?></label>
                        <input type="text" id="mail_bcc" val="" title="<?php esc_html_e( 'add single or comma separated emails', 'service-booking' ); ?>">
                    </div>
                    <div class="input-group">
                        <?php wp_editor( '', 'resend_email_body', $resend_email_content ); ?>
                    </div>
                    <div class="progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"><?php echo esc_html( '0%' ); ?></div>
                    </div>
                    <div id="fileList" style="display: none;"></div>
                    <input type="hidden" id="resend_email_attachment" value="">
                    <input type="hidden" id="final_files" value="">
                </div>
                <div class="attachment-send-group">
                    <div class="attachments">
                        <div class="attachment">
                            <label for="email_attachment">
                                <img src="<?php echo esc_url( $plugin_path . 'images/attach.png' ); ?>" alt="<?php esc_html_e( 'attachment', 'service-booking' ); ?>" title="<?php esc_html_e( 'Add attachment', 'service-booking' ); ?>">
                            </label>
                            <input id="email_attachment" name="email_attachment[]" type="file" multiple class="hidden" onclick="this.value = null"/>
                        </div>
                        <!-- <div class="attachment">
                            <label for="image-upload">
                                <img src="<?php echo esc_url( $plugin_path . 'images/image-upload.png' ); ?>" alt="attachment">
                            </label>
                            <input type="file" id="image-upload" name="image-upload" accept="image/*" class="imageupload hidden">
                        </div> -->
                    </div>
                    <button type="button" class="send-button resend-button" id="resend-button" onclick="bm_resend_email()"><?php esc_html_e( 'Send', 'service-booking' ); ?></button>
                    <div id="resendProcess" class="hidden">
                        <img id="resend_loader" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__bounce" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>


