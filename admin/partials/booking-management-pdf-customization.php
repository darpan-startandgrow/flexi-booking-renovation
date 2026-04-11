<?php
$identifier        = 'PDF_CUSTOMIZATION';
$dbhandler         = new BM_DBhandler();
$bmrequests        = new BM_Request();
$language          = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
$back_lang         = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
$language          = ! empty( $back_lang ) ? $back_lang : $language;
$booking_pdf       = "booking_pdf_$language";
$voucher_pdf       = "voucher_pdf_$language";
$customer_info_pdf = "customer_info_pdf_$language";
$pdf_record        = $dbhandler->get_row( $identifier, 1, 'id' );
$pdf_logo_image    = $bmrequests->bm_fetch_image_url_or_guid( 1, $identifier, 'url' );
$pdf_logo          = isset( $pdf_record->pdf_logo_guid ) ? $pdf_record->pdf_logo_guid : '';

$editor_settings = array(
    'wpautop'           => false,
    'media_buttons'     => true,
    'textarea_rows'     => 20,
    'tabindex'          => 4,
    'editor_height'     => 200,
    'tabfocus_elements' => ':prev,:next',
    'editor_css'        => '',
    'editor_class'      => '',
    'teeny'             => false,
    'dfw'               => false,
    'tinymce'           => true,
    'quicktags'         => true,
);

$booking_pdf_content       = array_merge( $editor_settings, array( 'textarea_name' => $booking_pdf ) );
$voucher_pdf_content       = array_merge( $editor_settings, array( 'textarea_name' => $voucher_pdf ) );
$customer_info_pdf_content = array_merge( $editor_settings, array( 'textarea_name' => $customer_info_pdf ) );

add_action( 'media_buttons', array( $this, 'bm_fields_list_for_email' ) );

if ( ( filter_input( INPUT_POST, 'savepdfcontent' ) ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_pdf content' ) ) {
        die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
    }

    $exclude = array( '_wpnonce', '_wp_http_referer', 'savepdfcontent', 'bm_field_list' );

    if ( filter_input( INPUT_POST, 'savepdfcontent' ) ) {
        $pdf_data['updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
        $pdf_data               = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $pdf_data ) {
            $pdf_data['pdf_logo_guid'] = isset( $pdf_data['pdf_logo_guid'] ) ? $pdf_data['pdf_logo_guid'] : 0;
            $dbhandler->update_row( $identifier, 'id', 1, $pdf_data, '', '%d' );
            wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_pdf_customization' ) );
            exit;
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'PDF Data could not be Processed !!', 'service-booking' ) . '</div>' );
        }
    }
}
?>

<div class="sg-admin-main-box" id="email-template-main-box">
    <div class="wrap listing_table">
        <div class="row">
            <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'PDF Customization', 'service-booking' ); ?></h2>
        </div>
        
        <form role="form" method="post">
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="pdf_logo_guid"><?php esc_html_e( 'PDF logo', 'service-booking' ); ?></label></th>
                    <td>
                        <input type="hidden" name="pdf_logo_guid" id="pdf_logo_guid" value="<?php echo esc_attr( $pdf_logo ); ?>">
                        <span class="pdf_logo_container" id="pdf_logo_container" style="<?php echo isset( $pdf_logo_image ) && $pdf_logo_image > 0 ? 'display: inline-block' : 'display: none'; ?>">
                            <img src="<?php echo isset( $pdf_logo_image ) ? esc_url( $pdf_logo_image ) : ''; ?>" width="100" height="100" id="pdf_logo_preview">
                            <button type="button" name="remove_pdf_logo" id="remove_pdf_logo" title="<?php esc_attr_e( 'Remove', 'service-booking' ); ?>" onclick="bm_remove_pdf_logo()"><?php esc_attr_e( '✕', 'service-booking' ); ?></button>
                        </span>
                        <div>
                            <a href="javascript:void(0)" class="button upload_pdf_logo"><?php esc_html_e( 'Upload logo', 'service-booking' ); ?>&nbsp;<i class="fa fa-arrow-up" aria-hidden="true"></i></a>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo esc_html( $booking_pdf ); ?>"><?php esc_html_e( 'Booking Ticket PDF', 'service-booking' ); ?></label></th>
                    <td class="bminput">
                        <div style="width: 54%;" class="sg-rg-buttom">
                            <?php wp_editor( isset( $pdf_record->$booking_pdf ) ? $pdf_record->$booking_pdf : '', $booking_pdf, $booking_pdf_content ); ?>
                            <div class="pdf_errortext"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo esc_html( $voucher_pdf ); ?>"><?php esc_html_e( 'Voucher PDF', 'service-booking' ); ?></label></th>
                    <td class="bminput">
                        <div style="width: 54%;" class="sg-rg-buttom">
                            <?php wp_editor( isset( $pdf_record->$voucher_pdf ) ? $pdf_record->$voucher_pdf : '', $voucher_pdf, $voucher_pdf_content ); ?>
                            <div class="pdf_errortext"></div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="<?php echo esc_html( $customer_info_pdf ); ?>"><?php esc_html_e( 'Customer Information PDF', 'service-booking' ); ?></label></th>
                    <td class="bminput">
                        <div style="width: 54%;" class="sg-rg-buttom">
                            <?php wp_editor( isset( $pdf_record->$customer_info_pdf ) ? $pdf_record->$customer_info_pdf : '', $customer_info_pdf, $customer_info_pdf_content ); ?>
                            <div class="pdf_errortext"></div>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_pdf content' ); ?>
                    <input type="submit" name="savepdfcontent" id="savepdfcontent" class="button button-primary" value="<?php esc_attr_e( 'Save Content', 'service-booking' ); ?>">
                <div class="all_pdf_error_text" style="display:none;"></div>
                </p>
            </div>
        </form>
    </div>

    <div id="pdf-sample-modal" class="modaloverlay">
        <div class="modal animate__animated animate__flipInX">
            <span class="close" onclick="closeModal('pdf-sample-modal')">&times;</span>
            <h2>&nbsp;&nbsp;<?php esc_html_e( 'PDF Live Preview', 'service-booking' ); ?></h2>
            <div class="modalcontentbox modal-body pdf-sample-container" style="max-height: 500px; overflow-y: auto; padding: 10px; border: 1px solid #eee; background: #fafafa;"></div>
            <div class="loader_modal"></div>
        </div>
    </div>

    <div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border: 1px solid #ddd;">
        <h3><?php esc_html_e( 'Preview & Download Templates', 'service-booking' ); ?></h3>
        <p><?php esc_html_e( 'Test your PDF templates using dummy customer data to see exactly how they will look.', 'service-booking' ); ?></p>
        
        <div class="pdf-test-buttons" style="display: flex; gap: 20px; margin-top: 20px;">
            <div style="flex: 1; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 4px;">
                <h4 style="margin-top: 0; color: #4A90E2;">
                    <span class="dashicons dashicons-tickets-alt"></span>
                    <?php esc_html_e( 'Booking PDF', 'service-booking' ); ?>
                </h4>
                <div style="display: flex; gap: 8px;">
                    <button class="bm-view-pdf-sample button" data-type="booking" style="width: 156px;">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Live Preview', 'service-booking' ); ?>
                    </button>
                    <!-- <a class="button" target="_blank" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bm_pdf_customization&test_pdf_action=download&type=booking&booking_id=dummy' ), 'test_pdf_action_booking_dummy' ) ); ?>">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e( 'Download Sample', 'service-booking' ); ?>
                    </a> -->
                </div>
            </div>
            
            <div style="flex: 1; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 4px;">
                <h4 style="margin-top: 0; color: #4A90E2;">
                    <span class="dashicons dashicons-awards"></span>
                    <?php esc_html_e( 'Voucher PDF', 'service-booking' ); ?>
                </h4>
                <div style="display: flex; gap: 8px;">
                    <button class="bm-view-pdf-sample button" data-type="voucher" style="width: 156px;">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Live Preview', 'service-booking' ); ?>
                    </button>
                    <!-- <a class="button" target="_blank" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bm_pdf_customization&test_pdf_action=download&type=voucher&booking_id=dummy' ), 'test_pdf_action_voucher_dummy' ) ); ?>">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e( 'Download Sample', 'service-booking' ); ?>
                    </a> -->
                </div>
            </div>
            
            <div style="flex: 1; padding: 15px; background: white; border: 1px solid #ddd; border-radius: 4px;">
                <h4 style="margin-top: 0; color: #4A90E2;">
                    <span class="dashicons dashicons-id-alt"></span>
                    <?php esc_html_e( 'Customer Info PDF', 'service-booking' ); ?>
                </h4>
                <div style="display: flex; gap: 8px;">
                    <button class="bm-view-pdf-sample button" data-type="customer_info" style="width: 156px;">
                        <span class="dashicons dashicons-visibility"></span>
                        <?php esc_html_e( 'Live Preview', 'service-booking' ); ?>
                    </button>
                    <!-- <a class="button" target="_blank" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=bm_pdf_customization&test_pdf_action=download&type=customer_info&booking_id=dummy' ), 'test_pdf_action_customer_info_dummy' ) ); ?>">
                        <span class="dashicons dashicons-media-document"></span>
                        <?php esc_html_e( 'Download Sample', 'service-booking' ); ?>
                    </a> -->
                </div>
            </div>
        </div>
    </div>

    <div style="margin: 20px 0; padding: 15px; background: #f5f5f5; border: 1px solid #ddd;">
        <h3><?php esc_html_e( 'Available PDF Placeholders', 'service-booking' ); ?></h3>
        
        <h4><?php esc_html_e( 'Customer Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{billing_first_name}}</code> - <?php esc_html_e( 'Customer first name', 'service-booking' ); ?></li>
            <li><code>{{billing_last_name}}</code> - <?php esc_html_e( 'Customer last name', 'service-booking' ); ?></li>
            <li><code>{{billing_email}}</code> - <?php esc_html_e( 'Customer email', 'service-booking' ); ?></li>
            <li><code>{{billing_phone}}</code> - <?php esc_html_e( 'Customer phone', 'service-booking' ); ?></li>
            <li><code>{{billing_address}}</code> - <?php esc_html_e( 'Customer full address', 'service-booking' ); ?></li>
        </ul>
        
        <h4><?php esc_html_e( 'Booking Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{booking_key}}</code> - <?php esc_html_e( 'Booking reference number', 'service-booking' ); ?></li>
            <li><code>{{booking_date}}</code> - <?php esc_html_e( 'Service date', 'service-booking' ); ?></li>
            <li><code>{{booking_slots}}</code> - <?php esc_html_e( 'Service time slots', 'service-booking' ); ?></li>
            <li><code>{{service_duration}}</code> - <?php esc_html_e( 'Service duration', 'service-booking' ); ?></li>
            <li><code>{{payment_method}}</code> - <?php esc_html_e( 'Payment method', 'service-booking' ); ?></li>
            <li><code>{{subtotal}}</code> - <?php esc_html_e( 'Order subtotal', 'service-booking' ); ?></li>
            <li><code>{{disount_amount}}</code> - <?php esc_html_e( 'Total discount', 'service-booking' ); ?></li>
            <li><code>{{total_cost}}</code> - <?php esc_html_e( 'Order total', 'service-booking' ); ?></li>
            <li><code>{{coupons}}</code> - <?php esc_html_e( 'Coupon codes (if any)', 'service-booking' ); ?></li>
            <li><code>{{extra_services}}</code> - <?php esc_html_e( 'Extra services list', 'service-booking' ); ?></li>
        </ul>
        
        <h4><?php esc_html_e( 'Service Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{service_name}}</code> - <?php esc_html_e( 'Main service name', 'service-booking' ); ?></li>
            <li><code>{{service_price}}</code> - <?php esc_html_e( 'Service price', 'service-booking' ); ?></li>
            <li><code>{{service_qty}}</code> - <?php esc_html_e( 'Service quantity', 'service-booking' ); ?></li>
            <li><code>{{service_total}}</code> - <?php esc_html_e( 'Service total', 'service-booking' ); ?></li>
        </ul>
        
        <h4><?php esc_html_e( 'Age Group Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{infant_count}}</code> - <?php esc_html_e( 'Number of infants', 'service-booking' ); ?></li>
            <li><code>{{infant_discount}}</code> - <?php esc_html_e( 'Infant discount amount', 'service-booking' ); ?></li>
            <li><code>{{child_count}}</code> - <?php esc_html_e( 'Number of children', 'service-booking' ); ?></li>
            <li><code>{{child_discount}}</code> - <?php esc_html_e( 'Child discount amount', 'service-booking' ); ?></li>
            <li><code>{{adult_count}}</code> - <?php esc_html_e( 'Number of adults', 'service-booking' ); ?></li>
            <li><code>{{adult_discount}}</code> - <?php esc_html_e( 'Adult discount amount', 'service-booking' ); ?></li>
            <li><code>{{senior_count}}</code> - <?php esc_html_e( 'Number of seniors', 'service-booking' ); ?></li>
            <li><code>{{senior_discount}}</code> - <?php esc_html_e( 'Senior discount amount', 'service-booking' ); ?></li>
        </ul>
        
        <h4><?php esc_html_e( 'Voucher Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{voucher_code}}</code> - <?php esc_html_e( 'Voucher code', 'service-booking' ); ?></li>
            <li><code>{{redeemed_date}}</code> - <?php esc_html_e( 'Voucher redemption date', 'service-booking' ); ?></li>
        </ul>
        
        <h4><?php esc_html_e( 'PDF Specific Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{logo}}</code> - <?php esc_html_e( 'Uploaded logo image', 'service-booking' ); ?></li>
            <li><code>{{qr_code}}</code> - <?php esc_html_e( 'QR code for booking verification', 'service-booking' ); ?></li>
            <li><code>{{current_date}}</code> - <?php esc_html_e( 'Current date', 'service-booking' ); ?></li>
            <li><code>{{current_time}}</code> - <?php esc_html_e( 'Current time', 'service-booking' ); ?></li>
            <li><code>{{current_year}}</code> - <?php esc_html_e( 'Current year', 'service-booking' ); ?></li>
        </ul>
        
        <h4><?php esc_html_e( 'System Information', 'service-booking' ); ?></h4>
        <ul>
            <li><code>{{admin_name}}</code> - <?php esc_html_e( 'Admin name', 'service-booking' ); ?></li>
            <li><code>{{admin_email}}</code> - <?php esc_html_e( 'Admin email', 'service-booking' ); ?></li>
            <li><code>{{admin_phone}}</code> - <?php esc_html_e( 'Admin phone', 'service-booking' ); ?></li>
            <li><code>{{from_name}}</code> - <?php esc_html_e( 'Sender name', 'service-booking' ); ?></li>
            <li><code>{{from_mail}}</code> - <?php esc_html_e( 'Sender email', 'service-booking' ); ?></li>
        </ul>
        
        <p><strong><?php esc_html_e( 'Tip:', 'service-booking' ); ?></strong> 
        <?php esc_html_e( 'If a placeholder has no value (e.g., no coupon), it will be replaced with an empty string.', 'service-booking' ); ?></p>
    </div>
</div>
