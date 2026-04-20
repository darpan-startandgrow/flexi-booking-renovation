<?php

/**
 * Renovation-added helpers for the React V2 REST API layer.
 *
 * These methods were introduced in the booking-renovation project on top of the
 * legacy Booking_API class. They are kept here to avoid polluting the legacy
 * class-booking-api.php file, which is intentionally kept identical to the
 * upstream flexibooking-legacy reference.
 *
 * - Booking_API_Permission_Helpers — filterable permission callbacks and array
 *   sanitiser, usable in any REST class that needs the same patterns.
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

if (! defined('ABSPATH')) exit;

/**
 * Filterable permission callbacks and sanitization utilities for REST API routes.
 *
 * Extend this class (or use it statically) when you need filterable auth checks
 * instead of the bare __return_true that ships in class-booking-api.php.
 */
class Booking_API_Permission_Helpers
{
    /**
     * Permission callback for public read-only endpoints.
     *
     * Applies a filter so add-ons can restrict access if needed.
     *
     * @since 1.0.0
     * @return bool
     */
    public function public_read_permission_check()
    {
        return apply_filters('bm_api_public_read_permission', true);
    }

    /**
     * Permission callback for public write endpoints (cart, checkout, payment).
     *
     * Applies a filter so add-ons can add authentication requirements.
     *
     * @since 1.0.0
     * @param WP_REST_Request $request The incoming request.
     * @return bool
     */
    public function public_write_permission_check($request)
    {
        return apply_filters('bm_api_public_write_permission', true, $request);
    }

    /**
     * Sanitize an array recursively for REST API input.
     *
     * @since 1.0.0
     * @param mixed $value The value to sanitize.
     * @return array Sanitized array.
     */
    public static function sanitize_array_callback($value)
    {
        if (! is_array($value)) {
            return [];
        }

        $clean = [];
        foreach ($value as $key => $val) {
            $key = sanitize_key($key);
            if (is_array($val)) {
                $clean[$key] = self::sanitize_array_callback($val);
            } elseif (is_numeric($val)) {
                $clean[$key] = $val + 0;
            } else {
                $clean[$key] = sanitize_text_field($val);
            }
        }

        return $clean;
    }
}
