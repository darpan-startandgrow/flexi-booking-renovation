<?php

/**
 * The stripe payment process class of the plugin.
 *
 * @author Darpan
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin
*/

class Booking_Management_Process_Payment extends Booking_Management_Stripes {

    public function isConnected() {
        return $this->is_connected();
    }

    protected function chargeCustomer( $amount, $currency, $token, $description, $customer_id ) {
        return $this->charge_customer( $amount, $currency, $token, $description, $customer_id );
    }

    protected function createPaymentIntent( $amount, $currency, $description, $customerID, $paymentMethodId ) {
        return $this->create_payment_intent( $amount, $currency, $description, $customerID, $paymentMethodId );
    }

    protected function setUpPaymentIntent( $intentId, $paymentMethod ) {
        return $this->setup_payment_intent( $intentId, $paymentMethod );
    }

    public function saveCustomer( $name, $email, $description, $address, $phone, $shipping, $booking_key, $paymentMethodId ) {
        return $this->save_customer( $name, $email, $description, $address, $phone, $shipping, $booking_key, $paymentMethodId );
    }

    public function updateCustomer( $customerID, $name, $description, $address, $phone, $shipping, $booking_key ) {
        return $this->update_customer( $customerID, $name, $description, $address, $phone, $shipping, $booking_key );
    }

    protected function createOneTimeToken( $token ) {
        return $this->create_one_time_token( $token );
    }

    public function createOneTimePaymentIntent( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key ) {
        return $this->create_one_time_payment_intent( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key );
    }

    protected function createRefund( $chargeId, $amount ) {
        return $this->create_refund( $chargeId, $amount );
    }

    public function capturePayment( $paymentIntentId, $amount ) {
        return $this->capture_payment( $paymentIntentId, $amount );
    }

    public function cancelPaymentIntent( $paymentIntentId, $booking_key ) {
        return $this->cancel_payment_intent( $paymentIntentId, $booking_key );
    }

    public function refundPaymentIntent( $paymentIntentId, $amount, $booking_key ) {
        return $this->refund_payment_intent( $paymentIntentId, $amount, $booking_key );
    }

    protected function refundCharge( $chargeId ) {
        return $this->refund_charge( $chargeId );
    }

    protected function isRefundPossible( $chargeId ) {
        return $this->is_refund_possible( $chargeId );
    }

    public function getRefund( $refundId ) {
        return $this->get_refund( $refundId );
    }

    public function preAuthorizeAmount( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key ) {
        return $this->pre_authorize_amount( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key );
    }

    protected function createPaymentSession( $amount, $currency, $description ) {
        return $this->create_payment_session( $amount, $currency, $description );
    }

    protected function createPaymentLink( $amount, $currency, $description ) {
        return $this->create_payment_link( $amount, $currency, $description );
    }

    public function getCustomer( $customerId ) {
        return $this->get_customer( $customerId );
    }

    public function removeCustomer( $customerId ) {
        return $this->remove_customer( $customerId );
    }

    public function getPaymentIntent( $paymentIntentId ) {
        return $this->get_payment_intent( $paymentIntentId );
    }

    public function updatePaymentIntent( $paymentIntentId, $params ) {
        return $this->update_payment_intent( $paymentIntentId, $params );
    }

    protected function updateCharge( $chargeId, $params ) {
        return $this->update_charge( $chargeId, $params );
    }

    protected function createPaymentMethod( $type, $cardNumber, $expMonth, $expYear, $cvc ) {
        return $this->create_payment_method( $type, $cardNumber, $expMonth, $expYear, $cvc );
    }

    protected function createProduct( $name, $description, $price, $currency ) {
        return $this->create_product( $name, $description, $price, $currency );
    }

    protected function createCoupon( $name, $percentOff, $duration, $durationInMonths ) {
        return $this->create_coupon( $name, $percentOff, $duration, $durationInMonths );
    }

    protected function createDiscount( $customerId, $couponId ) {
        return $this->create_discount( $customerId, $couponId );
    }

    protected function createTaxRate( $displayName, $percentage, $description, $jurisdiction ) {
        return $this->create_tax_rate( $displayName, $percentage, $description, $jurisdiction );
    }

    protected function createShippingRate( $name, $amount, $currency ) {
        return $this->create_shipping_rate( $name, $amount, $currency );
    }

    protected function createInvoice( $customerId, $items ) {
        return $this->create_invoice( $customerId, $items );
    }

    protected function getCustomerAccountBalance( $customerId ) {
        return $this->get_customer_account_balance( $customerId );
    }

    protected function getInvoice( $invoiceId ) {
        return $this->get_invoice( $invoiceId );
    }

    protected function createPrice( $currency, $unitAmount, $product ) {
        return $this->create_price( $currency, $unitAmount, $product );
    }

    protected function getPrice( $priceId ) {
        return $this->get_price( $priceId );
    }

    protected function getToken( $token ) {
        return $this->get_token( $token );
    }

    public function confirmPaymentIntent( $paymentIntentId, $paymentMethodId ) {
        return $this->confirm_payment_intent( $paymentIntentId, $paymentMethodId );
    }

    public function attachPaymentMethodToCustomer( $paymentMethodId, $customerID, $booking_key ) {
        return $this->attach_payment_method_to_customer( $paymentMethodId, $customerID, $booking_key );
    }
}
