<?php

$booking_details = isset( $booking_details['booking_details'] ) ? $booking_details['booking_details'] : array();

?>

<div class="order-details">
    <h1><?php esc_html_e( 'Order Details', 'service-booking' ); ?></h1>
    <div class="order-info">
        <h2><?php esc_html_e( 'Order Number:', 'service-booking' ); ?><span id="order-number"><?php echo !empty( $booking_details ) && isset( $booking_details['order_number'] ) ? esc_html( $booking_details['order_number'] ) : ''; ?></span></h2>
        <p><?php esc_html_e( 'Date:', 'service-booking' ); ?><span id="order-date"><?php echo !empty( $booking_details ) && isset( $booking_details['order_date'] ) ? esc_html( $booking_details['order_date'] ) : ''; ?></span></p>
        <p><?php esc_html_e( 'Email:', 'service-booking' ); ?><span id="email"><?php echo !empty( $booking_details ) && isset( $booking_details['email'] ) ? esc_html( $booking_details['email'] ) : ''; ?></span></p>
        <p><?php esc_html_e( 'Total:', 'service-booking' ); ?><span id="total"><?php echo !empty( $booking_details ) && isset( $booking_details['total'] ) ? esc_html( $booking_details['total'] ) : ''; ?></span></p>
        <p><?php esc_html_e( 'Payment Method:', 'service-booking' ); ?><span id="payment-method"><?php echo !empty( $booking_details ) && isset( $booking_details['payment_method'] ) ? esc_html( $booking_details['payment_method'] ) : ''; ?></span></p>
    </div>
    <h2><?php esc_html_e( 'Product Details', 'service-booking' ); ?></h2>
    <table>
        <thead>
            <tr>
                <th><?php esc_html_e( 'Product', 'service-booking' ); ?></th>
                <th><?php esc_html_e( 'Price', 'service-booking' ); ?></th>
                <th><?php esc_html_e( 'Quantity', 'service-booking' ); ?></th>
                <th><?php esc_html_e( 'Total', 'service-booking' ); ?></th>
            </tr>
        </thead>
        <tbody id="product-details">
            <?php echo !empty( $booking_details ) && isset( $booking_details['product_details'] ) ? wp_kses_post( $booking_details['product_details'] ) : ''; ?>
        </tbody>
    </table>
    <h2><?php esc_html_e( 'Billing Address', 'service-booking' ); ?></h2>
    <div id="billing-address">
    <?php echo !empty( $booking_details ) && isset( $booking_details['billing_address'] ) ? wp_kses_post( $booking_details['billing_address'] ) : ''; ?>
    </div>
</div>
