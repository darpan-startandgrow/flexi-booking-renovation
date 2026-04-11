<?php

/**
 * Interface for Voucher Redeem process.
 *
 * @author Darpan
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes/interfaces
*/

interface FlexiVoucherRedeemInterface {
    public function validateVoucher(): array;
    public function validateIfRedeemed();
    public function getBookingInfo(): array;
    public function getVoucherInfo(): array;
    public function updateVoucherInfo( array $data ): bool;
    public function fetchAvailableSlots( string $date ): array;
    public function confirmRedemption( string $date, string $slot);
}
