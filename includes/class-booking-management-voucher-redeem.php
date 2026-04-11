<?php

/**
 * Concrete class for Voucher Redeem.
 *
 * @author Darpan
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes/interfaces
*/

require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/interfaces/class-booking-management-voucher-interface.php';

class FlexiVoucherRedeem extends FlexiVoucher implements FlexiVoucherRedeemInterface {
    public function __construct( $voucherCode ) {
        parent::__construct( $voucherCode );
    }

    /**
     * Validate voucher
     *
     * @author Darpan
    */
    public function validateVoucher(): array {
        $voucher = $this->getVoucher();

        if ( isset( $voucher['error'] ) ) {
            return $voucher;
        }

        $voucher = $voucher[0];

        if ( $voucher['is_redeemed'] == 1 ) {
            return array( 'error' => __( 'Voucher is already redeemed.', 'service-booking' ) );
        }

        if ( $voucher['is_expired'] == 1 ) {
            return array( 'error' => __( 'Voucher is expired.', 'service-booking' ) );
        }

        if ( $voucher['status'] != 1 ) {
            return array( 'error' => __( 'Voucher is inactive.', 'service-booking' ) );
        }

        $booking = $this->getBookingDetails();

        if ( isset( $booking['error'] ) ) {
            return $booking;
        }

        return array( 'success' => __( 'Voucher is valid', 'service-booking' ) );
    }//end validateVoucher()


    /**
     * Validate voucher
     *
     * @author Darpan
    */
    public function validateVoucherForStatusChange(): array {
        $voucher = $this->getVoucher();

        if ( isset( $voucher['error'] ) ) {
            return $voucher;
        }

        $voucher = $voucher[0];

        if ( $voucher['is_redeemed'] == 1 ) {
            return array( 'error' => __( 'Voucher is already redeemed.', 'service-booking' ) );
        }

        if ( $voucher['is_expired'] == 1 ) {
            return array( 'error' => __( 'Voucher is expired.', 'service-booking' ) );
        }

        return array( 'success' => __( 'Voucher is valid', 'service-booking' ) );
    }//end validateVoucher()


    /**
     * Validate if voucher is redeemed
     *
     * @author Darpan
    */
    public function validateIfRedeemed() {
        $voucher = $this->getVoucher();

        if ( isset( $voucher['error'] ) ) {
            return $voucher;
        }

        $voucher = $voucher[0];

        if ( $voucher['is_redeemed'] == 0 || $voucher['is_expired'] == 0 || $voucher['status'] != 0 ) {
            return false;
        }

        return true;
    }//end validateIfRedeemed()


    /**
     * Get booking details
     *
     * @author Darpan
    */
    public function getBookingInfo(): array {
        return $this->getBookingDetails();
    }//end getBookingDetails()


    /**
     * Get voucher details
     *
     * @author Darpan
    */
    public function getVoucherInfo(): array {
        return $this->getVoucher();
    }//end getVoucherInfo()


    /**
     * Update voucher details
     *
     * @author Darpan
    */
    public function updateVoucherInfo( array $data ): bool {
        return $this->updateVoucher( $data );
    }//end updateVoucher()


    /**
     * Reset voucher
     *
     * @author Darpan
    */
    public function resetVoucher(): bool {
        return $this->updateVoucher(
            array(
				'status'      => 1,
				'is_expired'  => 0,
                'is_redeemed' => 0,
				'updated_at'  => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
            )
        );
    }//end updateVoucher()


    /**
     *  voucher
     *
     * @author Darpan
    */
    public function markVoucherExpired(): bool {
        return $this->updateVoucher(
            array(
				'status'      => 0,
                'is_expired'  => 1,
                'is_redeemed' => 0,
				'updated_at'  => ( new BM_Request() )->bm_fetch_current_wordpress_datetime_stamp(),
            )
        );
    }//end updateVoucher()


    /**
     * Get voucher expiry date
     *
     * @author Darpan
    */
    public function getVoucherExpiry(): array {
        $voucher = $this->getVoucher();

        if ( isset( $voucher['error'] ) ) {
            return $voucher;
        }

        $voucher  = $voucher[0];
        $settings = maybe_unserialize( $voucher['settings'] );

        if ( !isset( $settings['expiry'] ) ) {
            return array( 'error' => __( 'Expiry could not be captured.', 'service-booking' ) );
        }

        return array( 'expiry' => $settings['expiry'] );

    }//end getVoucherExpiry()


    /**
     * Get available slots
     *
     * @author Darpan
    */
    public function fetchAvailableSlots( string $date ): array {
        $slots = $this->getBookableTimeSlots( $date );

        if ( isset( $slots['error'] ) ) {
            return array( 'error' => $slots['error'] );
        }

        return array( 'slots' => $slots );
    }//end fetchAvailableSlots()


    /**
     * Confirm voucher redemption
     *
     * @author Darpan
    */
    public function confirmRedemption( string $date, string $slot ) {
        $validation = $this->validateVoucher();

        if ( isset( $validation['error'] ) ) {
            return $validation;
        }

        $serviceId = $this->getBookingDetails()[0]['service_id'] ?? 0;
        $bookingId = $this->getBookingDetails()[0]['id'] ?? 0;

        if ( !$serviceId ) {
            return array( 'error' => __( 'Service Info not found.', 'service-booking' ) );
        }

        if ( !$bookingId ) {
            return array( 'error' => __( 'Booking Info not found.', 'service-booking' ) );
        }

        $bmrequests      = new BM_Request();
        list($from, $to) = $this->parseSlot( $slot, $serviceId, $date, $bmrequests );

        $voucherData = array(
            'status'      => 0,
            'is_expired'  => 1,
            'is_redeemed' => 1,
            'updated_at'  => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
        );

        if ( $this->updateVoucher( $voucherData ) ) {
            $bookingData = array(
                'booking_date'       => $date,
                'booking_slots'      => maybe_serialize(
                    array(
                        'from' => $from,
                        'to'   => $to,
                    )
                ),
                'is_active'          => 1,
                'booking_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
            );

            if ( $this->updateOrder( $bookingData ) ) {
                $slotCountData = array(
                    'booking_date'    => $date,
                    'is_active'       => 1,
                    'slot_updated_at' => $bmrequests->bm_fetch_current_wordpress_datetime_stamp(),
                );

                $dbhandler = new BM_DBhandler();
                $dbhandler->update_row( 'SLOTCOUNT', 'booking_id', $bookingId, $slotCountData, '', '%d' );
                $dbhandler->update_row( 'EXTRASLOTCOUNT', 'booking_id', $bookingId, $slotCountData, '', '%d' );

                return array( 'success' => __( 'Voucher Redeemed successfully', 'service-booking' ) );
            }
        }

        $this->resetVoucher();
        return array( 'error' => __( 'Failed to redeem the voucher.', 'service-booking' ) );
    }//end confirmRedemption()


    /**
     * Parse slots
     *
     * @author Darpan
    */
    private function parseSlot( string $slot, int $serviceId, string $date, BM_Request $bmrequests ): array {
        if ( strpos( $slot, ' - ' ) !== false ) {
            list($from, $to) = array_map( array( $bmrequests, 'bm_twenty_fourhrs_format' ), explode( ' - ', $slot ) );
        } else {
            $from = $bmrequests->bm_twenty_fourhrs_format( $slot );
            $to   = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date( $serviceId, $date )
                ? $bmrequests->bm_fetch_variable_to_time_slot_by_service_id( $serviceId, $from, $date )
                : $bmrequests->bm_fetch_non_variable_to_time_slot_by_service_id( $serviceId, $from );
        }

        return array( $from, $to );
    }//end parseSlot()
}
