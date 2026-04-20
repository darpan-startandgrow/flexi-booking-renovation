<?php

/**
 * Stripe Webhook handler for the booking plugin.
 *
 * Extracted from Booking_API to maintain separation of concerns.
 * This class handles incoming Stripe webhook events (payment infrastructure),
 * which are not React V2 frontend endpoints.
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if (! defined('ABSPATH')) exit;

class Booking_Stripe_Webhook
{
    /**
     * Register the Stripe webhook REST route.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    /**
     * Register Stripe webhook REST route.
     *
     * @since 1.0.0
     */
    public function register_routes()
    {
        // Stripe webhook - signature verification serves as authentication.
        register_rest_route('booking/v1', '/stripe-webhook', [
            'methods'             => 'POST',
            'callback'            => [$this, 'stripe_webhook'],
            'permission_callback' => '__return_true',
        ]);
    }

    /**
     * Handle incoming Stripe webhook events.
     *
     * Verifies the webhook signature, enforces idempotency by recording each
     * processed event_id, and dispatches to the appropriate handler.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request Incoming REST request.
     * @return WP_REST_Response
     */
    public function stripe_webhook(WP_REST_Request $request)
    {
        $dbhandler = new BM_DBhandler();

        // Read the raw payload before WordPress can alter it.
        $payload    = file_get_contents('php://input');
        // wp_unslash only - sanitize_text_field may corrupt the HMAC signature.
        $sig_header = isset($_SERVER['HTTP_STRIPE_SIGNATURE']) ? wp_unslash($_SERVER['HTTP_STRIPE_SIGNATURE']) : '';

        if (! defined('STRIPE_SECRET_KEY') || ! defined('STRIPE_WEBHOOK_SECRET')) {
            return rest_ensure_response(['status' => 400, 'message' => 'Webhook not configured.']);
        }

        $stripes = new Booking_Management_Stripes(STRIPE_SECRET_KEY);
        $event   = $stripes->verify_webhook_signature($payload, $sig_header, STRIPE_WEBHOOK_SECRET);

        if (! $event) {
            return rest_ensure_response(['status' => 400, 'message' => 'Invalid webhook signature.']);
        }

        // Stripe SDK returns an Event object; use object property access.
        $event_id   = ! empty( $event->id )   ? sanitize_text_field( $event->id )   : '';
        $event_type = ! empty( $event->type ) ? sanitize_text_field( $event->type ) : '';

        if (empty($event_id)) {
            return rest_ensure_response(['status' => 400, 'message' => 'Missing event ID.']);
        }

        // Idempotency: skip events that have already been processed.
        if ($dbhandler->record_exists('STRIPE_EVENTS', array('event_id' => $event_id))) {
            return rest_ensure_response(['status' => 200, 'message' => 'Event already processed.']);
        }

        // Record the event before processing to prevent duplicate processing even if
        // processing fails partway through (the record acts as a distributed lock).
        $dbhandler->insert_if_not_exists(
            'STRIPE_EVENTS',
            array('event_id' => $event_id),
            array(
                'event_id'     => $event_id,
                'event_type'   => $event_type,
                'processed_at' => current_time('mysql'),
            )
        );

        // Dispatch based on event type.
        switch ($event_type) {
            case 'payment_intent.succeeded':
                $payment_intent = isset( $event->data->object ) ? $event->data->object : null;
                $transaction_id = ! empty( $payment_intent->id ) ? sanitize_text_field( $payment_intent->id ) : '';
                if (! empty($transaction_id)) {
                    $payment_id = $dbhandler->get_value('TRANSACTIONS', 'id', $transaction_id, 'transaction_id');
                    if (! empty($payment_id)) {
                        $dbhandler->update_row('TRANSACTIONS', 'id', $payment_id, array(
                            'payment_status'         => 'succeeded',
                            'transaction_updated_at' => current_time('mysql'),
                        ), '', '%d');
                    } else {
                        error_log( 'Stripe webhook: No transaction found for succeeded intent ' . $transaction_id );
                    }
                }
                break;

            case 'payment_intent.payment_failed':
                $payment_intent = isset( $event->data->object ) ? $event->data->object : null;
                $transaction_id = ! empty( $payment_intent->id ) ? sanitize_text_field( $payment_intent->id ) : '';
                $failure_message = '';
                if ( ! empty( $payment_intent->last_payment_error ) && ! empty( $payment_intent->last_payment_error->message ) ) {
                    $failure_message = sanitize_text_field( $payment_intent->last_payment_error->message );
                }
                if (! empty($transaction_id)) {
                    $payment_id = $dbhandler->get_value('TRANSACTIONS', 'id', $transaction_id, 'transaction_id');
                    if (! empty($payment_id)) {
                        $dbhandler->update_row('TRANSACTIONS', 'id', $payment_id, array(
                            'payment_status'         => 'failed',
                            'transaction_updated_at' => current_time('mysql'),
                        ), '', '%d');
                        if ( ! empty( $failure_message ) ) {
                            error_log( 'Stripe payment_intent.payment_failed for ' . $transaction_id . ': ' . $failure_message );
                        }
                    } else {
                        error_log( 'Stripe webhook: No transaction found for failed intent ' . $transaction_id );
                    }
                }
                break;

            case 'payment_intent.canceled':
                $payment_intent = isset( $event->data->object ) ? $event->data->object : null;
                $transaction_id = ! empty( $payment_intent->id ) ? sanitize_text_field( $payment_intent->id ) : '';
                if (! empty($transaction_id)) {
                    $payment_id = $dbhandler->get_value('TRANSACTIONS', 'id', $transaction_id, 'transaction_id');
                    if (! empty($payment_id)) {
                        $dbhandler->update_row('TRANSACTIONS', 'id', $payment_id, array(
                            'payment_status'         => 'canceled',
                            'transaction_updated_at' => current_time('mysql'),
                        ), '', '%d');
                    } else {
                        error_log( 'Stripe webhook: No transaction found for canceled intent ' . $transaction_id );
                    }
                }
                break;

            case 'charge.refunded':
                $charge         = isset( $event->data->object ) ? $event->data->object : null;
                $payment_intent_id = ! empty( $charge->payment_intent ) ? sanitize_text_field( $charge->payment_intent ) : '';
                if (! empty($payment_intent_id)) {
                    $payment_id = $dbhandler->get_value('TRANSACTIONS', 'id', $payment_intent_id, 'transaction_id');
                    if (! empty($payment_id)) {
                        $dbhandler->update_row('TRANSACTIONS', 'id', $payment_id, array(
                            'payment_status'         => 'refunded',
                            'transaction_updated_at' => current_time('mysql'),
                        ), '', '%d');
                    } else {
                        error_log( 'Stripe webhook: No transaction found for refunded charge on intent ' . $payment_intent_id );
                    }
                }
                break;

            default:
                // Unhandled event type - recorded above, no further action needed.
                break;
        }

        return rest_ensure_response(['status' => 200, 'message' => 'Webhook processed.']);
    }
}
