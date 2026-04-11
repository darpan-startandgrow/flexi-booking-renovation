<?php

/**
 * The voucher base class of the plugin.
 *
 * @author Darpan
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
*/

abstract class FlexiVoucher {
    protected $voucherCode;
    protected $dbHandler;

    protected function __construct( $voucherCode ) {
        $this->voucherCode = $voucherCode;
        $this->dbHandler   = new BM_DBhandler();
    }


    /**
     * Get voucher
     *
     * @author Darpan
    */
    protected function getVoucher(): array {
        $voucher = $this->dbHandler->get_all_result(
            'VOUCHERS',
            '*',
            array( 'code' => $this->voucherCode ),
            'results',
            0,
            false,
            null,
            false,
            '',
            'ARRAY_A'
        );

        return $voucher ?: array( 'error' => __( 'Voucher not found', 'service-booking' ) );
    }//end getVoucher()


    /**
     * Get booked details
     *
     * @author Darpan
    */
    protected function getBookingDetails(): array {
        $booking = $this->dbHandler->get_all_result(
            'BOOKING',
            '*',
            1,
            'results',
            0,
            false,
            null,
            false,
            'vouchers IN("' . $this->voucherCode . '")',
            'ARRAY_A'
        );

        return $booking ?: array( 'error' => __( 'No booking for voucher.', 'service-booking' ) );
    }//end getBookingDetails()


    /**
     * Get bookable time slots
     *
     * @author Darpan
    */
    protected function getBookableTimeSlots( string $date ): ?array {
        $tables = array( 'SERVICE', 's' );
        $joins  = array(
            array(
                'table' => 'BOOKING',
                'alias' => 'b',
                'on'    => 'b.service_id = s.id',
                'type'  => 'INNER',
            ),
            array(
                'table' => 'TIME',
                'alias' => 't',
                'on'    => 's.id = t.service_id',
                'type'  => 'INNER',
            ),
        );

        $where = array(
            'b.is_active'    => array( '=' => 1 ),
            'b.vouchers'     => array( 'IN' => array( $this->voucherCode ) ),
            'b.order_status' => array( 'NOT IN' => array( 'cancelled', 'on_hold', 'pending' ) ),
        );

        $columns = 's.id, s.default_max_cap, s.default_stopsales, s.variable_time_slots, t.time_slots';

        $slots = $this->dbHandler->get_results_with_join( $tables, $columns, $joins, $where, 'results', 0, false, null, false, '', false, 10000, 'ARRAY_A' ) ?? array();

        if ( empty( $slots ) ) {
            return array( 'error' => __( 'No slots available for the selected date.', 'service-booking' ) );
        }

        $service = $slots[0] ?? null;
		if ( !$service || !( new BM_Request() )->bm_service_is_bookable( $service['id'], $date ) ) {
			return array( 'error' => __( 'Service is not bookable on the selected date.', 'service-booking' ) );
		}

        $timezone             = $this->dbHandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
        $slot_format          = $this->dbHandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
        $global_show_to_slots = $this->dbHandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
        $now                  = new DateTime( 'now', new DateTimeZone( $timezone ) );
        $currentDateTime      = $now->format( 'Y-m-d H:i' );

        $bmrequests = new BM_Request();

        $time_slots          = maybe_unserialize( $service['time_slots'] ?? array() );
        $variable_time_slots = maybe_unserialize( $service['variable_time_slots'] ?? array() );
        $stopsales           = $bmrequests->bm_fetch_service_stopsales_by_service_id( $service['id'], $date );

        $endDateTime = '';
        if ( !empty( $stopsales ) ) {
            $stopSalesHours   = floor( $stopsales );
            $stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

            if ( $bmrequests->bm_has_dynamic_stopsales_for_date( $service['id'], $date ) ) {
                $endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
            } else {
                $endDateTime = clone $now;
            }

            $endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
            $endDateTime = $endDateTime->format( 'Y-m-d H:i' );
        }

        $dates              = !empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();
        $use_variable_slots = in_array( $date, $dates, true );

        $selected_slots = $use_variable_slots
            ? $variable_time_slots[ array_search( $date, $dates ) ]
            : $time_slots;

        $valid_slots = array_filter(
            $selected_slots['from'],
            function( $slot_time, $i ) use ( $selected_slots ) {
                return !isset( $selected_slots['disable'][ $i ] ) || $selected_slots['disable'][ $i ] != 1;
            },
            ARRAY_FILTER_USE_BOTH
        );

        $available_slots = array_filter(
            array_map(
                function( $slot_from_time, $i ) use ( $service, $date, $selected_slots, $timezone, $slot_format, $global_show_to_slots, $stopsales, $currentDateTime, $endDateTime, $use_variable_slots, $bmrequests ) {
                    $slot_max_cap  = $selected_slots['max_cap'][ $i ] ?? 0;
                    $capacity_left = $bmrequests->bm_fetch_available_slot_capacity_by_service_and_slot_id(
                        $service['id'],
                        $i,
                        $slot_from_time,
                        $date,
                        $slot_max_cap,
                        $use_variable_slots ? 1 : 0
                    );

                    $startSlot          = new DateTime( "$date $slot_from_time", new DateTimeZone( $timezone ) );
                    $startSlotFormatted = $startSlot->format( 'Y-m-d H:i' );

                    $exclude = ( $stopsales && $endDateTime && strtotime( $endDateTime ) > strtotime( $startSlotFormatted ) ) ||
                                  ( empty( $stopsales ) && strtotime( $currentDateTime ) > strtotime( $startSlotFormatted ) ) ||
                                  $capacity_left <= 0 ? true : false;

                    if ( $exclude ) {
                        return null;
                    }

                    return $global_show_to_slots == 0
                        ? $bmrequests->bm_format_slot( $slot_format, $slot_from_time )
                        : $bmrequests->bm_format_slot( $slot_format, $slot_from_time ) . ' - ' . $bmrequests->bm_format_slot( $slot_format, $selected_slots['to'][ $i ] ?? '' );
                },
                $valid_slots,
                array_keys( $valid_slots )
            )
        );

        return !empty( $available_slots ) ? array_values( $available_slots ) : array( 'error' => __( 'No valid slots found for the selected date.', 'service-booking' ) );
    }//end getBookableTimeSlots()


    /**
     * Update voucher
     *
     * @author Darpan
    */
    protected function updateVoucher( array $data ): bool {
        return (bool) $this->dbHandler->update_row( 'VOUCHERS', 'code', $this->voucherCode, $data, '', '%s' );
    }//end updateVoucher()


    /**
     * Update booking
     *
     * @author Darpan
    */
    protected function updateOrder( array $data ): bool {
        $voucher = $this->getVoucher();
        if ( isset( $voucher['error'] ) ) {
            return false;
        }

        $voucher = $voucher[0];

        return (bool) $this->dbHandler->update_row(
            'BOOKING',
            'id',
            $voucher['booking_id'] ?? 0,
            $data,
            '',
            '%d'
        );
    }//end updateVoucher()
}
