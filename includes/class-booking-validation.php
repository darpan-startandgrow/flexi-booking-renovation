<?php
class Booking_Validation
{

    /**
     * Validate date format and ensure it's not in the past
     */
    public static function validate_date($value, $request, $param)
    {
        //  Check format
        $d = DateTime::createFromFormat('Y-m-d', $value);
        if (! ($d && $d->format('Y-m-d') === $value)) {
            return new WP_Error(
                'invalid_date_format',
                'Invalid date format. Use YYYY-MM-DD.',
                ['status' => 400]
            );
        }

        // Check if date is in the past
        if (strtotime($value) < strtotime(date('Y-m-d'))) {
            return new WP_Error(
                'past_date',
                'The booking date cannot be in the past.',
                ['status' => 400]
            );
        }

        return true; // ✅ Validation passed
    }

    /**
     * Validate month format (YYYY-MM)
     */
    public static function validate_month($value, $request, $param)
    {
        // Check format
        $d = DateTime::createFromFormat('Y-m', $value);
        if (! ($d && $d->format('Y-m') === $value)) {
            return new WP_Error(
                'invalid_month_format',
                'Invalid month format. Use YYYY-MM.',
                ['status' => 400]
            );
        }

        // Check if month is in the past
        $current_month = date('Y-m');
        if ($value < $current_month) {
            return new WP_Error(
                'past_month',
                'The booking month cannot be in the past.',
                ['status' => 400]
            );
        }

        return true; // ✅ Validation passed
    }
}
