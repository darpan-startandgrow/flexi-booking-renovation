<?php
$booking_id = isset( $_GET['booking_id'] ) ? intval( $_GET['booking_id'] ) : 0;

if ( !$booking_id ) {
    echo '<div class="error-message">No booking ID provided</div>';
    return;
}

$bm_requests   = new BM_Request();
$order_details = $bm_requests->bm_fetch_order_details_for_single_page( $booking_id );

if ( !$order_details ) {
    echo '<div class="error-message">Order not found</div>';
    return;
}
?>

<div class="single-order-container">
    <div class="order-header">
        <a href="admin.php?page=bm_all_orders"><div class="backbtn">&#8592;</div></a><h1><?php echo esc_html__( 'Single Order Details', 'service-booking' ); ?></h1>
        <div class="order-reference">
            <?php echo esc_html__( 'Order', 'service-booking' ); ?> #<?php echo esc_attr( $booking_id ); ?>
        </div>
    </div>

    <div class="order-section">
        <h2><?php echo esc_html__( 'Customer Info', 'service-booking' ); ?></h2>
        <div class="customer-info-grid">
            <div class="customer-field">
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'First Name', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['customer_info']['first_name'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Last name', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['customer_info']['last_name'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Phone', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['customer_info']['phone'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Email', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['customer_info']['email'] ); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="order-section">
        <h2><?php echo esc_html__( 'Order Details', 'service-booking' ); ?></h2>
        <div class="order-details-grid">
            <div class="order-field">
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Email', 'service-booking' ); ?></span> 
                    <span class="field-value"><?php echo esc_html( $order_details['order_details']['email'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Service date', 'service-booking' ); ?> & <?php echo esc_html__( 'time', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['order_details']['service_date_time'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Order date', 'service-booking' ); ?> & <?php echo esc_html__( 'time', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['order_details']['order_date_time'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Quantity', 'service-booking' ); ?></span>
                     <span class="field-value"><?php echo esc_html( $order_details['order_details']['quantity'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html( $order_details['order_details']['price'] ); ?></span>
                    <span class="field-value"><?php echo esc_html__( 'Price', 'service-booking' ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Order Status', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['order_details']['order_status'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Payment Status', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['order_details']['payment_status'] ); ?></span>
                </div>
            </div>

        </div>
    </div>


    <?php if ( ! empty( $order_details['invoice_details']['invoice_company_address'] ) ) : ?>
    <div class="order-section">
        <h2><?php echo esc_html__( 'Invoice Details', 'service-booking' ); ?></h2>
        <div class="customer-info-grid">
            <div class="customer-field">
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Company Address', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['invoice_details']['invoice_company_address'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Company Name', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['invoice_details']['invoice_company_name'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'Country', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['invoice_details']['invoice_company_country'] ); ?></span>
                </div>
                <div class="label-box">
                    <span class="field-label"><?php echo esc_html__( 'VAT ID', 'service-booking' ); ?></span>
                    <span class="field-value"><?php echo esc_html( $order_details['invoice_details']['invoice_vat_id'] ); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php do_action( 'bm_single_order_after_order_details', $booking_id ); ?>
            
    <div class="order-section">
        <h3><?php echo esc_html__( 'Ordered Products', 'service-booking' ); ?></h3>
        <div class="product-tabs">
            <div class="tab active" data-tab="product-info"><?php echo esc_html__( 'Product Information', 'service-booking' ); ?></div>
            <div class="tab" data-tab="payment-details"><?php echo esc_html__( 'Payment Details', 'service-booking' ); ?></div>
            <div class="tab" data-tab="email"><?php echo esc_html__( 'E-Mail', 'service-booking' ); ?></div>
        </div>
        
        <div class="products-table-container">
            <table class="products-table">
                <thead>
                    <tr>
                        <th><?php echo esc_html__( 'Product', 'service-booking' ); ?></th>
                        <th><?php echo esc_html__( 'Total Quantity', 'service-booking' ); ?></th>
                        <th class="revenue-column-width"><?php echo esc_html__( 'Revenue', 'service-booking' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $order_details['ordered_products'] as $product ) : ?>
                    <tr>
                        <td><?php echo esc_html( $product['product'] ); ?></td>
                        <td><?php echo esc_html( $product['total_quantity'] ); ?></td>
                        <td class="revenue-column-width"><?php echo esc_html( $product['revenue'] ); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="order-summary">
        <div class="summary-row">
            <span class="summary-label"><?php echo esc_html__( 'Subtotal', 'service-booking' ); ?></span>
            <span class="summary-value"><?php echo esc_html( $order_details['subtotal'] ); ?></span>
        </div>
        <div class="summary-row discount">
            <span class="summary-label"><?php echo esc_html__( 'Discount', 'service-booking' ); ?></span>
            <span class="summary-value">-<?php echo esc_html( $order_details['order_details']['discount'] ); ?></span>
        </div>
        <div class="summary-row total">
            <span class="summary-label"><?php echo esc_html__( 'Total', 'service-booking' ); ?></span>
            <span class="summary-value"><?php echo esc_html( $order_details['order_details']['price'] ); ?></span>
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
</div>

<div class="loader_modal"></div>
