<?php

/**
 * The stripe payment gateway class of the plugin.
 *
 * @author Darpan
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
*/

abstract class Booking_Management_Payment_Gateway {
    // Abstract functions for stripe payment
	abstract protected function is_connected();
	abstract protected function charge_customer( $amount, $currency, $token, $description, $customer_id );
	abstract protected function create_payment_intent( $amount, $currency, $description, $customerID, $paymentMethodId );
	abstract protected function setup_payment_intent( $intentId, $paymentMethod );
	abstract protected function save_customer( $name, $email, $description, $address, $phone, $shipping, $booking_key, $paymentMethodId );
	abstract protected function create_one_time_token( $token );
	abstract protected function create_one_time_payment_intent( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key );
	abstract protected function create_refund( $chargeId, $amount );
	abstract protected function is_refund_possible( $chargeId );
	abstract protected function pre_authorize_amount( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key );
	abstract protected function create_payment_session( $amount, $currency, $description );
	abstract protected function get_customer( $customerId );
	abstract protected function remove_customer( $customerId );
	abstract protected function get_payment_intent( $paymentIntentId );
	abstract protected function update_payment_intent( $paymentIntentId, $params );
	abstract protected function update_charge( $chargeId, $params );
	abstract protected function create_payment_method( $type, $cardNumber, $expMonth, $expYear, $cvc );
	abstract protected function create_product( $name, $description, $price, $currency );
	abstract protected function create_coupon( $name, $percentOff, $duration, $durationInMonths );
	abstract protected function create_discount( $customerId, $couponId );
	abstract protected function create_tax_rate( $displayName, $percentage, $description, $jurisdiction );
	abstract protected function create_shipping_rate( $name, $amount, $currency );
	abstract protected function create_invoice( $customerId, $items );
	abstract protected function get_customer_account_balance( $customerId );
	abstract protected function get_invoice( $invoiceId );
	abstract protected function create_price( $currency, $unitAmount, $product );
	abstract protected function get_price( $priceId );
	abstract protected function capture_payment( $paymentIntentId, $amount );
	abstract protected function cancel_payment_intent( $paymentIntentId, $booking_key );
	abstract protected function refund_charge( $chargeId );
	abstract protected function get_token( $token );
	abstract protected function create_payment_link( $amount, $currency, $description );
	abstract protected function confirm_payment_intent( $paymentIntentId, $paymentMethodId );
	abstract protected function refund_payment_intent( $paymentIntentId, $amount, $booking_key );
	abstract protected function attach_payment_method_to_customer( $paymentMethodId, $customerID, $booking_key );
	abstract protected function update_customer( $customerID, $name, $description, $address, $phone, $shipping, $booking_key );
}


