<?php

/**
 * The stripe class of the plugin.
 *
 * @author Darpan
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin
*/

if ( !class_exists( '\Stripe\Stripe' ) ) {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'src/stripe/init.php';
}

class Booking_Management_Stripes extends Booking_Management_Payment_Gateway {
    private $stripe_secret_key;

    public function __construct( $secret_key ) {
        $this->stripe_secret_key = $secret_key;
        \Stripe\Stripe::setApiKey( $this->stripe_secret_key );
    }


    /**
     * Check if stripe is connected
     *
     * @author Darpan
     */
    protected function is_connected() {
		try {
			\Stripe\Account::retrieve();
			return true;
		} catch ( Exception $e ) {
			return false;
		}
    }// end is_connected()


    /**
     * Charge a customer
     *
     * @author Darpan
     */
    protected function charge_customer( $amount, $currency, $token, $description, $customer_id ) {
        try {
            $charge = \Stripe\Charge::create(
                array(
					'amount'      => $amount,    // Amount in cents
					'currency'    => $currency,
					'source'      => $token,
                    'description' => $description, // Payment for Order #123,
                    'customer'    => $customer_id, // Associate the card with a customer
                )
            );

            return $charge;
        } catch ( \Stripe\Exception\CardException $e ) {
            // Handle card errors
            return false;
        } catch ( Exception $e ) {
            // Handle other errors
            return false;
        }
    }// end charge_customer()


    /**
     * Create a Payment Intent
     *
     * @author Darpan
     */
    protected function create_payment_intent( $amount, $currency, $description, $customerID, $paymentMethodId ) {
        try {
            $intent = \Stripe\PaymentIntent::create(
                array(
					'amount'               => $amount,    // Amount in cents
					'currency'             => $currency,
                    'description'          => $description,
                    'payment_method'       => $paymentMethodId,
                    'customer'             => $customerID,
					'payment_method_types' => array(
						'card',
					),
                    'setup_future_usage'   => 'off_session', // Allow saving the card for future payments
                    'use_stripe_sdk'       => true, // Enable automatic 3D Secure redirection
                    'confirm'              => true,
                )
            );

            return $intent;
        } catch ( Exception $e ) {
            return false;
        }
    }// end create_payment_intent()


    /**
     * Set up a Payment Intent
     *
     * @author Darpan
     */
    protected function setup_payment_intent( $intentId, $paymentMethod ) {
        try {
            $intent = \Stripe\PaymentIntent::update(
                $intentId,
                array( 'payment_method' => $paymentMethod )
            );

            return $intent;
        } catch ( Exception $e ) {
            return false;
        }
    }// end setup_payment_intent()


    /**
     * Save customer information
     *
     * @author Darpan
     */
    protected function save_customer( $name, $email, $description, $address, $phone, $shipping, $booking_key, $paymentMethodId = null ) {
        try {
            $customer_details = array(
                'name'        => $name,
                'email'       => $email,
                'description' => $description,
                'address'     => $address,
                'phone'       => $phone,
                'shipping'    => $shipping,
            );

            if ( $paymentMethodId !== null ) {
                $customer_details['payment_method'] = $paymentMethodId;
            }

            $customer = \Stripe\Customer::create( $customer_details );

            return $customer;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            error_log( 'Save customer failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage()
            );
            return false;
        }
    }// end save_customer()


    /**
     * Update customer information
     *
     * @author Darpan
     */
    protected function update_customer( $customerID, $name, $description, $address, $phone, $shipping, $booking_key ) {
        try {
            $stripe   = new \Stripe\StripeClient( $this->stripe_secret_key );
            $customer = $stripe->customers->update(
                $customerID,
                array(
                    'name'        => $name,
                    'description' => $description,
                    'address'     => $address,
                    'phone'       => $phone,
                    'shipping'    => $shipping,
                )
            );

            return $customer;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            error_log( 'Update customer failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage()
            );
            return false;
        }
    }// end update_customer()


    /**
     * Create a one-time use token for a payment
     *
     * @author Darpan
     */
    protected function create_one_time_token( $token ) {
        try {
            $token = \Stripe\Token::create(
                array(
					'card' => $token,
                )
            );

            return $token;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end create_one_time_token()


    /**
     * Create a Payment Intent with one-time session
     *
     * @author Darpan
     */
    protected function create_one_time_payment_intent( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key ) {
        try {
            $intent = \Stripe\PaymentIntent::create(
                array(
					'amount'               => $amount,    // Amount in cents
					'currency'             => $currency,
					'description'          => $description,
                    'payment_method'       => $paymentMethodId,
                    'customer'             => $customerID,
					'payment_method_types' => array(
						'card',
					),
                    'use_stripe_sdk'       => true, // Enable automatic 3D Secure redirection
                    'confirm'              => true,
                    'confirmation_method'  => 'automatic',
                    'metadata'             => array(
						'booking_key'  => $booking_key,
						'checkout_key' => $checkout_key,
					),
                )
            );

            return $intent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            error_log( 'PaymentIntent creation failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage(),
                array(
					'amount'   => $amount,
					'currency' => $currency,
                )
            );
            return false;
        }
    }// end create_one_time_payment_intent()



    /**
     * Capture a Payment Intent
     *
     * @author Darpan
     */
    protected function capture_payment( $paymentIntentId, $amount ) {
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve( $paymentIntentId );
            $paymentIntent->capture( array( 'amount_to_capture' => $amount ) );
            return $paymentIntent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end capture_payment()


    /**
     * Cancel a Payment Intent
     *
     * @author Darpan
     */
    protected function cancel_payment_intent( $paymentIntentId, $booking_key ) {
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve( $paymentIntentId );
            $paymentIntent->cancel();
            return $paymentIntent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            error_log( 'Cancel payment failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage()
            );
            return false;
        }
    }// end cancel_payment_intent()


     /**
     * Refund a Charge
     *
     * @author Darpan
     */
    protected function refund_charge( $chargeId ) {
        try {
            $charge = \Stripe\Charge::retrieve( $chargeId );
            $refund = $charge->refund();
            return $refund;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end refund_charge()


    /**
     * create a refund for a charge
     *
     * @author Darpan
     */
    protected function create_refund( $chargeId, $amount ) {
        try {
            $refund = \Stripe\Refund::create(
                array(
					'charge' => $chargeId,
                    'amount' => $amount,
                )
            );

            return $refund;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle invalid refund request
            return false;
        }
    }// end create_refund()


    /**
     * create a refund for a payment intent
     *
     * @author Darpan
     */
    protected function refund_payment_intent( $paymentIntentId, $amount, $booking_key ) {
        try {
            $refund = \Stripe\Refund::create(
                array(
					'amount'         => $amount,
                    'payment_intent' => $paymentIntentId,
                )
            );

            return $refund;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle invalid refund request
            error_log( 'Refund payment failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage()
            );
            return false;
        }
    }// end refund_payment_intent()


    /**
     * Check whether a refund is possible for a charge
     *
     * @author Darpan
     */
    protected function is_refund_possible( $chargeId ) {
        try {
            $charge = \Stripe\Charge::retrieve( $chargeId );

            return $charge->refunded === false;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end is_refund_possible()


    /**
     * Check whether a refund is possible for a charge
     *
     * @author Darpan
     */
    protected function get_refund( $refundId ) {
        try {
            $stripe = new \Stripe\StripeClient( $this->stripe_secret_key );
            $refund = $stripe->refunds->retrieve( $refundId, array() );
            return $refund;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end is_refund_possible()


    /**
     * Pre-authorize an amount without capturing it immediately
     *
     * @author Darpan
     */
    protected function pre_authorize_amount( $amount, $currency, $description, $customerID, $paymentMethodId, $booking_key, $checkout_key ) {
        try {
            $stripe = new \Stripe\StripeClient( $this->stripe_secret_key );

            $intent = $stripe->paymentIntents->create(
                array(
					'amount'               => $amount,    // Amount in cents
					'currency'             => $currency,
                    'description'          => $description,
                    'payment_method'       => $paymentMethodId,
                    'customer'             => $customerID,
                    'payment_method_types' => array(
						'card',
					),
                    'capture_method'       => 'manual',
                    'use_stripe_sdk'       => true, // Enable automatic 3D Secure redirection
                    'confirm'              => true,
                    'confirmation_method'  => 'automatic',
                    'metadata'             => array(
						'booking_key'  => $booking_key,
						'checkout_key' => $checkout_key,
					),
                )
            );

            return $intent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            error_log( 'Pre authorization payment failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage(),
                array(
					'amount'   => $amount,
					'currency' => $currency,
                )
            );
            return false;
        }
    }// end pre_authorize_amount()


    /**
     * Create a payment session
     *
     * @author Darpan
     */
    protected function create_payment_session( $amount, $currency, $description ) {
        try {
            $checkout_session = \Stripe\Checkout\Session::create(
                array(
					'payment_method_types' => array( 'card' ),
					'line_items'           => array(
						array(
							'price_data' => array(
								'currency'     => $currency,
								'product_data' => array(
									'name' => $description,
								),
								'unit_amount'  => $amount, // Amount in cents
							),
							'quantity'   => 1,
						),
					),
					'mode'                 => 'payment',
					'success_url'          => 'https://example.com/success', // Replace with your success URL
					'cancel_url'           => 'https://example.com/cancel',   // Replace with your cancel URL
                )
            );

            return $checkout_session->url;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end create_payment_session()


     /**
     * Retrieve customer information by customer ID
     *
     * @author Darpan
     */
    protected function get_customer( $customerId ) {
        try {
            $customer = \Stripe\Customer::retrieve( $customerId );
            return $customer;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end get_customer()


     /**
     * Attach a payment method to a customer
     *
     * @author Darpan
     */
    protected function attach_payment_method_to_customer( $paymentMethodId, $customerID, $booking_key ) {
        try {
            $stripe = new \Stripe\StripeClient( $this->stripe_secret_key );
            $method = $stripe->paymentMethods->attach(
                $paymentMethodId,
                array( 'customer' => $customerID )
            );
            return $method;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            error_log( 'Payment method attachment failed: ' . $e->getMessage() );
            ( new BM_Request() )->save_payment_error(
                $booking_key,
                $e->getMessage()
            );
            return false;
        }
    }// end attach_payment_method_to_customer()


    /**
     * Remove a customer by customer ID
     *
     * @author Darpan
     */
    protected function remove_customer( $customerId ) {
        try {
            $customer = \Stripe\Customer::delete( $customerId );
            return true;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end remove_customer()


    /**
     * Retrieve payment intent information by payment intent ID
     *
     * @author Darpan
     */
    protected function get_payment_intent( $paymentIntentId ) {
        try {
            $paymentIntent = \Stripe\PaymentIntent::retrieve( $paymentIntentId );
            return $paymentIntent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            return false;
        }
    }// end get_payment_intent()


    /**
     * Update a Payment Intent
     *
     * @author Darpan
     */
    protected function update_payment_intent( $paymentIntentId, $params ) {
        try {
            $paymentIntent = \Stripe\PaymentIntent::update( $paymentIntentId, $params );
            return $paymentIntent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end update_payment_intent()


    /**
     * Confirm a Payment Intent
     *
     * @author Darpan
     */
    protected function confirm_payment_intent( $paymentIntentId, $paymentMethodId ) {
        try {
            $stripe = new \Stripe\StripeClient( $this->stripe_secret_key );
            $intent = $stripe->paymentIntents->confirm(
                $paymentIntentId,
                array( 'payment_method' => $paymentMethodId )
            );
            return $intent;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end confirm_payment_intent()


    /**
     * Update a Charge
     *
     * @author Darpan
     */
    protected function update_charge( $chargeId, $params ) {
        try {
            $charge = \Stripe\Charge::update( $chargeId, $params );
            return $charge;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end update_charge()


    /**
     * Create a Payment Method
     *
     * @author Darpan
     */
    protected function create_payment_method( $type, $cardNumber, $expMonth, $expYear, $cvc ) {
        try {
            $paymentMethod = \Stripe\PaymentMethod::create(
                array(
					'type' => $type,
					'card' => array(
						'number'    => $cardNumber,
						'exp_month' => $expMonth,
						'exp_year'  => $expYear,
						'cvc'       => $cvc,
					),
                )
            );

            return $paymentMethod;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_payment_method()


    /**
     * Create a Product
     *
     * @author Darpan
     */
    protected function create_product( $name, $description, $price, $currency ) {
        try {
            $product = \Stripe\Product::create(
                array(
					'name'        => $name,
					'description' => $description,
					'type'        => 'flexibooking',
					'metadata'    => array(
						'price'    => $price,
						'currency' => $currency,
					),
                )
            );

            return $product;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_product()


     /**
     * Create a Coupon
     *
     * @author Darpan
     */
    protected function create_coupon( $name, $percentOff, $duration, $durationInMonths ) {
        try {
            $coupon = \Stripe\Coupon::create(
                array(
					'name'               => $name,
					'percent_off'        => $percentOff,
					'duration'           => $duration,
					'duration_in_months' => $durationInMonths,
                )
            );

            return $coupon;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_coupon()



    /**
     * Create a Discount
     *
     * @author Darpan
     */
    protected function create_discount( $customerId, $couponId ) {
        try {
            $customer = \Stripe\Customer::retrieve( $customerId );

            $customer->invoice_settings->discounts[] = array( 'coupon' => $couponId );
            $customer->save();
            return $customer;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_discount()


    /**
     * Create a Tax Rate
     *
     * @author Darpan
     */
    protected function create_tax_rate( $displayName, $percentage, $description, $jurisdiction ) {
        try {
            $taxRate = \Stripe\TaxRate::create(
                array(
					'display_name' => $displayName,
                    'description'  => $description,
					'percentage'   => $percentage,
					'inclusive'    => false,
					'jurisdiction' => $jurisdiction, // Change as needed
                )
            );

            return $taxRate;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_tax_rate()


    /**
     * Create a Shipping Rate
     *
     * @author Darpan
     */
    protected function create_shipping_rate( $name, $amount, $currency ) {
        try {
            $shippingRate = \Stripe\ShippingRate::create(
                array(
					'amount'      => $amount,
					'currency'    => $currency,
					'description' => $name,
					'up_to'       => null,
                )
            );

            return $shippingRate;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_shipping_rate()


    /**
     * Create an Invoice for a Customer
     *
     * @author Darpan
     */
    protected function create_invoice( $customerId, $items ) {
        try {
            $invoice = \Stripe\Invoice::create(
                array(
					'customer' => $customerId,
					'items'    => $items,
                )
            );

            /**$items = array(
                array(
                    'price'    => 'price_id_1', // Replace with the actual price ID
                    'quantity' => 2,         // Quantity of this item on the invoice
                ),
                array(
                    'price'    => 'price_id_2', // Replace with the actual price ID
                    'quantity' => 1,         // Quantity of this item on the invoice
                ),
                // Add more items as needed
            );*/

            return $invoice;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_invoice()


    /**
     * Get Customer's Account Balance
     *
     * @author Darpan
     */
    protected function get_customer_account_balance( $customerId ) {
        try {
            $customer = \Stripe\Customer::retrieve( $customerId );
            $balance  = $customer->balance;
            return $balance;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end get_customer_account_balance()


    /**
     * Retrieve an Invoice by Invoice ID
     *
     * @author Darpan
     */
    protected function get_invoice( $invoiceId ) {
        try {
            $invoice = \Stripe\Invoice::retrieve( $invoiceId );
            return $invoice;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end get_invoice()


    /**
     * Create a Price
     *
     * @author Darpan
     */
    protected function create_price( $currency, $unitAmount, $product ) {
        try {
            $price = \Stripe\Price::create(
                array(
					'currency'    => $currency,
					'unit_amount' => $unitAmount, // Amount in cents or smallest currency unit
					'product'     => $product, // Product ID or Product object
                )
            );

            return $price;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end create_price()


    /**
     * Retrieve a Price by Price ID
     *
     * @author Darpan
     */
    protected function get_price( $priceId ) {
        try {
            $price = \Stripe\Price::retrieve( $priceId );
            return $price;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end get_price()


    /**
     * Retrieve a token
     *
     * @author Darpan
     */
    protected function get_token( $token ) {
        try {
            $token = \Stripe\Token::retrieve( $token );
            return $token;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle API errors
            return false;
        }
    }// end get_token()


    /**
     * Create a payment link
     *
     * @author Darpan
    */
    protected function create_payment_link( $amount, $currency, $description ) {
        try {
            $paymentLink = \Stripe\PaymentLink::create(
                array(
					'amount'      => $amount, // Amount in cents
					'currency'    => $currency,
					'description' => $description, // Include customer details in the description
                )
            );

            return $paymentLink->url;
        } catch ( \Stripe\Exception\ApiErrorException $e ) {
            // Handle Stripe API errors
            return false;
        }
    }// end create_payment_link()
}
