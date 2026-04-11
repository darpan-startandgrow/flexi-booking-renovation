<?php

$voucher_code = trim( stripslashes( sanitize_text_field( filter_input( INPUT_GET, 'voucher' ) ) ) );
$BookDate     = trim( sanitize_text_field( filter_input( INPUT_GET, 'BookDate' ) ) );
$redeemed     = trim( sanitize_text_field( filter_input( INPUT_GET, 'redeemed' ) ) );
$timezone     = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
$today        = ( new DateTime( 'now', new DateTimeZone( $timezone ) ) )->format( 'Y-m-d' );

if ( $BookDate && $BookDate < $today ) {
    echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Invalid date selected', 'service-booking' ) . '</div>';
    return;
}

if ( !$redeemed ) {
    $steps = array(
        array(
            'condition'  => !$voucher_code,
            'active'     => 'active-line',
            'notAllowed' => '',
            'text'       => __( 'Insert Code', 'monticello' ),
        ),
        array(
            'condition'  => $voucher_code && !$BookDate,
            'active'     => 'active-line',
            'notAllowed' => '',
            'text'       => __( 'Book Slot', 'monticello' ),
        ),
        array(
            'condition'  => $voucher_code && $BookDate,
            'active'     => 'active-line',
            'notAllowed' => '',
            'text'       => __( 'Confirm Booking', 'monticello' ),
        ),
    );

	?>

<div class="voucher-redeem-main-box">
<div class="bred">
    <?php
    foreach ( $steps as $index => $step ) {
        $activeClass     = $step['condition'] ? $step['active'] : '';
        $notAllowedClass = $step['condition'] ? $step['notAllowed'] : 'not-allowed';
        ?>
        <div class="breadcrumbs-item <?php echo esc_html( $activeClass ); ?> <?php echo esc_html( $notAllowedClass ); ?>">
            <a href="#" class="breadcrumbs-item-link" rel="tag" title="<?php echo esc_html( $step['text'] ); ?>">
                <?php echo esc_html( $step['text'] ); ?>
            </a>
        </div>
        <?php if ( $index < count( $steps ) - 1 ) { ?>
            <i aria-hidden="true" class="fa fa-circle"></i>
        <?php } ?>
    <?php } ?>
</div>
<?php } ?>

<div id="common_errorMessage" class="bm-notice bm-error hidden"></div>

<?php

if ( !$voucher_code && !$redeemed ) {
    ?>
    <form role="form" method="get">
        <div class="voucherbox redeem-page-voucher">
            <div class="input-group mb-3">
                <input type="text" name="voucher_code" id="voucher_code" placeholder="<?php echo esc_html__( 'insert voucher code', 'service-booking' ); ?>" autocomplete="off" required>
                <span class="tickicon hidden">
                    <i class="fa fa-check hidden"></i>
                    <i class="fa fa-check hidden"></i>
                </span>
                <div class="required_errortext voucher_error hidden"></div>
                <div class="input-group-prepend spinner-parent-div">
                    <span class="button-class">
                        <button type="submit" class="input-group-text bgcolor bordercolor textwhite spinner-button" name="get_voucher" id="get_voucher">
                            <div class="spinner hidden" id="spinner"></div>
                            <span id="buttonText"><?php esc_html_e( 'Redeem', 'service-booking' ); ?></span>
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </form>

</div>
    <?php
    return;
}

$redeemVoucher = new FlexiVoucherRedeem( $voucher_code );

if ( $voucher_code && !$BookDate ) {
    $validateVoucher = $redeemVoucher->validateVoucher();

    if ( isset( $validateVoucher['error'] ) ) {
        echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html( $validateVoucher['error'] ) . '</div>';
        return;
    }

    $bookingInfo = $redeemVoucher->getBookingInfo();

    if ( isset( $bookingInfo['error'] ) ) {
        echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html( $bookingInfo['error'] ) . '</div>';
        return;
    }

    $bookingInfo   = $bookingInfo[0];
    $bmrequests    = new BM_Request();
    $service_image = $bmrequests->bm_fetch_image_url_or_guid( $bookingInfo['service_id'], 'SERVICE', 'url' );
    $products      = $bmrequests->bm_fetch_product_info_order_details_page( $bookingInfo['id'] );

    if ( empty( $products ) || !isset( $products['products'] ) || !is_array( $products ) ) {
        echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'No redeemable products found', 'service-booking' ) . '</div>';
        return;
    }

    $slots = $redeemVoucher->fetchAvailableSlots( $today );

    // if ( isset( $slots['error'] ) ) {
    //     echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( $slots['error'], 'service-booking' ) . '</div>';
    //     return;
    // }

    ?>

        <div class="goBackButton vocuher-box-page">
            <a href="#">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
        </div>

        <div class="voucher-box">
            <div class="voucher-details">
                <h4><?php echo esc_html__( 'Redeemed Voucher', 'service-booking' ); ?></h4>
                <p><?php echo esc_html( $voucher_code ); ?></p>
            </div>
            <div class="voucher-time">
                <span><?php echo esc_html( $bmrequests->bm_fetch_datetime_difference( $redeemVoucher->getVoucherExpiry()['expiry'] ) ) . esc_html__( 's to expire', 'service-booking' ); ?></span>
            </div>
        </div>

        <div class="book-slot-section">
            <span class="date-field">
                <label for="BookDate"><?php echo esc_html__( 'Select Date:', 'service-booking' ); ?><strong class="required_asterisk"> *</strong></label>
                <input type="date" name="BookDate" id="BookDate" min="<?php echo esc_html( $today ); ?>" value="<?php echo esc_html( $today ); ?>" required>
            </span>

            <span class="slot-field">
                <label for="voucherSlot"><?php echo esc_html__( 'Select Slot:', 'service-booking' ); ?><strong class="required_asterisk"> *</strong></label>
                <select name="SelectedSlot" id="voucherSlot" required>
                    <?php
                    if ( isset( $slots['slots'] ) ) {
                        foreach ( $slots['slots'] as $slot ) {
                            echo '<option value="' . esc_attr( $slot ) . '">' . esc_html( $slot ) . '</option>';
                        }
                    }
                    ?>
                </select>
            </span>

            <div class="spinner-parent-div">
                <span class="button-class">
                    <button type="button" name="gotoConfirm" id="gotoConfirm" class="bgcolor bordercolor textwhite spinner-button">
                        <div class="spinner hidden" id="spinner"></div>
                        <span id="buttonText"><?php echo esc_html__( 'Next', 'service-booking' ); ?></span>
                    </button>
                </span>
            </div>
        </div>

        <div class="product-container">
            <div class="product-header">
                <h1><?php echo esc_html__( 'Product List', 'service-booking' ); ?></h1>
            </div>

            <div class="products">
                <?php foreach ( $products['products'] as $product ) { ?>
                    <div class="product-card">
                        <img src="<?php echo esc_html( $product['image'] ); ?>" alt="<?php echo esc_html( $product['name'] ); ?>">
                        <div class="product-details">
                            <h3><?php echo esc_html( $product['name'] ); ?></h3>
                            <p><?php echo esc_html__( 'Quantity: ', 'service-booking' ) . esc_html( $product['quantity'] ); ?></p>
                            <p class="price"><?php echo esc_html__( 'Base Price: ', 'service-booking' ) . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['base_price'], true ) ); ?></p>
                            <p><?php echo esc_html__( 'Total: ', 'service-booking' ) . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['total'], true ) ); ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="product-summary">
                <p><?php echo esc_html__( 'Subtotal: ', 'service-booking' ) . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $products['subtotal'], true ) ); ?></p>
                <p><?php echo esc_html__( 'Discount: ', 'service-booking' ) . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $products['discount'], true ) ); ?></p>
                <p class="total"><?php echo esc_html__( 'Total: ', 'service-booking' ) . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $products['total'], true ) ); ?></p>
            </div>
        </div>
    </div>

	<?php
	return; }


if ( $voucher_code && $BookDate ) {
    $countries = ( new BM_Request() )->bm_get_countries();
    $recipient = maybe_unserialize( $redeemVoucher->getVoucherInfo()[0]['recipient_data'] );
	?>
    <div class="goBackButton product-details-page">
        <a href="#">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
    </div>

    <h3 class="sub-heading"><?php esc_html_e( 'Confirm Recipient details', 'service-booking' ); ?></h3>
    <br>
    <div id="recipient_details">
        <div class="formbox">
            <label for="recipient_first_name"><?php esc_html_e( 'First name', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="text" name="gift_details['first_name']" id="recipient_first_name" placeholder="<?php echo esc_html__( 'enter first name' ); ?>" value="<?php echo esc_html( $recipient['recipient_first_name'] ?? '' ); ?>" required>
        </div>
        <div class="formbox">
            <label for="recipient_last_name"><?php esc_html_e( 'Last name', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="text" name="gift_details['last_name']" id="recipient_last_name" placeholder="<?php echo esc_html__( 'enter last name' ); ?>" value="<?php echo esc_html( $recipient['recipient_last_name'] ?? '' ); ?>" required>
        </div>
        <div class="formbox">
            <label for="recipient_email"><?php esc_html_e( 'Email', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="email" name="gift_details['email']" id="recipient_email" placeholder="<?php echo esc_html__( 'enter email' ); ?>" value="<?php echo esc_html( $recipient['recipient_email'] ?? '' ); ?>" required>
        </div>
        <div class="formbox">
            <label for="recipient_contact"><?php esc_html_e( 'Mobile No', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="tel" name="gift_details['contact']" class="intl_phone_field_input" id="recipient_contact" placeholder="<?php echo esc_html__( 'enter contact number' ); ?>" value="<?php echo esc_html( $recipient['recipient_contact'] ?? '' ); ?>" required>
        </div>
        <div class="formbox textarea-formbox">
            <label for="recipient_address"><?php esc_html_e( 'Address', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <textarea name="gift_details['address']" rows="5" columns="5" id="recipient_address" placeholder="<?php echo esc_html__( 'enter address' ); ?>" required><?php echo esc_html( $recipient['recipient_address'] ?? '' ); ?></textarea>
        </div>
        <div class="formbox">
            <label for="recipient_city"><?php esc_html_e( 'City', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="text" name="gift_details['city']" id="recipient_city" placeholder="<?php echo esc_html__( 'enter city' ); ?>" value="<?php echo esc_html( $recipient['recipient_city'] ?? '' ); ?>" required>
        </div>
        <div class="formbox">
            <label for="recipient_state"><?php esc_html_e( 'State', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="text" name="gift_details['state']" id="recipient_state" placeholder="<?php echo esc_html__( 'enter state' ); ?>" value="<?php echo esc_html( $recipient['recipient_state'] ?? '' ); ?>" required>
        </div>
        <div class="formbox checkbox_and_radio_div">
            <label for="recipient_country"><?php esc_html_e( 'Country', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <select name="gift_details['country']" id="recipient_country" required>
            <?php
            if ( !empty( $countries ) ) {
                foreach ( $countries as $key => $country ) {
                    ?>
                <option value="<?php echo esc_html( $key ); ?>" <?php selected( esc_html( $recipient['recipient_country'] ?? '' ), esc_html( $key ) ); ?>><?php echo esc_html( $country ); ?></option>
                        <?php
                }
            }
            ?>
            </select>
        </div>
        <div class="formbox">
            <label for="recipient_postcode"><?php esc_html_e( 'Postcode', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
            <input type="number" name="gift_details['postcode']" id="recipient_postcode" placeholder="<?php echo esc_html__( 'enter zip code' ); ?>" value="<?php echo esc_html( $recipient['recipient_postcode'] ?? '' ); ?>" required>
        </div>
        <div class="spinner-parent-div relative-positioned">
            <span class="button-class">
                <button type="button" name="redeemConfirm" id="redeemConfirm" class="input-group-text bgcolor bordercolor textwhite spinner-button">
                    <div class="spinner hidden" id="spinner"></div>
                    <span id="buttonText"><?php echo esc_html__( 'Confirm', 'service-booking' ); ?></span>
                </button>
            </span>
        </div>
    </div>
	<?php
	return; }


if ( $redeemed ) {
    $redeemVoucher   = new FlexiVoucherRedeem( $redeemed );
    $validateVoucher = $redeemVoucher->validateIfRedeemed();

    if ( isset( $validateVoucher['error'] ) || !$validateVoucher ) {
        echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( sprintf( '%s', $validateVoucher['error'] ? $validateVoucher['error'] : 'Voucher is not yet redeemed' ), 'service-booking' ) . '</div>';
        return;
    }

    $voucherInfo = $redeemVoucher->getVoucherInfo();

    if ( isset( $voucherInfo['error'] ) ) {
        echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html( $voucherInfo['error'] ) . '</div>';
        return;
    }

	$bookingInfo = $redeemVoucher->getBookingInfo();

	if ( isset( $bookingInfo['error'] ) ) {
		echo '<div id="errorMessage" class="bm-notice bm-error">' . esc_html( $bookingInfo['error'] ) . '</div>';
		return;
	}

    $bookingInfo      = $bookingInfo[0];
    $bookedSlots      = maybe_unserialize( $bookingInfo['booking_slots'] );
    $bmrequests       = new BM_Request();
    $booking_id       = $bookingInfo['id'] ?? 0;
    $products         = $bmrequests->bm_fetch_product_info_order_details_page( $booking_id );
    $recipient        = maybe_unserialize( $voucherInfo[0]['recipient_data'] );
    $customer_billing = $bmrequests->get_customer_info_for_order( $booking_id );
    $plugin_path      = plugin_dir_url( __FILE__ );

	?>
<div class="booking_details">
    <table class="booking-container">
        <tr>
            <td>
                <table class="header">
                    <tr>
                        <td>
                            <h1><?php esc_html_e( 'Hey ', 'service-booking' ); ?> <?php echo isset( $recipient['recipient_first_name'] ) ? esc_html( $recipient['recipient_first_name'] ) . ' ' . esc_html( $recipient['recipient_last_name'] ) : ''; ?>!</h1>
                            <p><?php echo esc_html__( 'Thank you for redeeeming voucher- ' . esc_html( $redeemed ), 'service-booking' ); ?></p>
                        </td>
                    </tr>
                </table>
                
                <table class="order-details">
                    <tr>
                        <td colspan="5">
                            <p><?php echo esc_html__( 'You will receive a redeem confirmation mail in your given email.', 'service-booking' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td class="subheading"><strong><?php esc_html_e( 'Service Date: ', 'service-booking' ); ?></strong><br/>
                            <span><?php echo isset( $bookingInfo['booking_date'] ) ? esc_html( $bmrequests->bm_month_year_date_format( $bookingInfo['booking_date'] ) ) : ''; ?></span>
                        </td>
                        <td colspan="1" class="subheading td-center-align"><br/>
                            <span></span></td>
                        <td colspan="3" class="subheading td-right-align"><strong><?php esc_html_e( 'Slots: ', 'service-booking' ); ?></strong><br/>
                            <span><?php echo esc_html( $bookedSlots['from'] . ' - ' . $bookedSlots['to'] ?? '' ); ?></span></td>
                    </tr>
            <?php
            $count = 1;
            foreach ( $products['products'] as $product ) {
                ?>
                        
                        <tr>
                            <td colspan="2">
                <?php if ( $count == 1 ) { ?>
                                    <span class="theading"><?php esc_html_e( 'Main Product ', 'service-booking' ); ?></span><br>
                <?php } elseif ( $count == 2 ) { ?>
                                    <span class="theading"><?php esc_html_e( 'Extra Products ', 'service-booking' ); ?></span><br>
                <?php } ?>
                        <span class="subtext"><image src="<?php echo esc_url( $product['image'] ); ?>" style="width:60px;height:50px;"></span>
                        <span class="subtext positioned"><?php echo isset( $product['name'] ) ? esc_html( $product['name'] ) : 'N/A'; ?></span>
                    </td>
                    <td><span class="theading"><?php esc_html_e( 'Price ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['base_price'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['base_price'], true ) ) : 'N/A'; ?></span></td>
                    <td><span class="theading"><?php esc_html_e( 'Qty ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['quantity'] ) ? esc_html( $product['quantity'] ) : 'N/A'; ?></span></td>
                    <td class="td-right-align"><span class="theading td-right-align"><?php esc_html_e( 'Total ', 'total' ); ?></span><br/><span class="subtext"><?php echo isset( $product['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['total'], true ) ) : 'N/A'; ?></span></td>
                    </tr>
                <?php
                $count++;
            }
            ?>
            <tr>
                <td class="subtotal " colspan="4"><?php esc_html_e( 'Subtotal ', 'service-booking' ); ?></td>
                <td class="subtotal td-right-align"><?php echo isset( $products['subtotal'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $products['subtotal'], true ) ) : 'N/A'; ?></td>
            </tr>
            <tr class="noborder">
                <td colspan="4"><?php esc_html_e( 'Discount ', 'service-booking' ); ?></td>
                <td class="discountvalue td-right-align">-<?php echo isset( $products['discount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $products['discount'], true ) ) : 'N/A'; ?></td>
            </tr>
            <tr class="totalbar">
                <td colspan="4"><?php esc_html_e( 'Total ', 'service-booking' ); ?></td>
                <td class="td-right-align"><?php echo isset( $products['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $products['total'], true ) ) : 'N/A'; ?></td>
            </tr>
        </table>
        <table class="billing-shipping noborder">
            <tr>
                <th><?php esc_html_e( 'Recipient', 'service-booking' ); ?></th>
                <th class="th-positioned"><?php esc_html_e( 'Gifter', 'service-booking' ); ?></th>
            </tr>
            <tr>
                <td class="addresstext"><?php echo isset( $recipient['recipient_first_name'] ) ? esc_html( $recipient['recipient_first_name'] ) . ' ' . esc_html( $recipient['recipient_last_name'] ) : ''; ?><br><?php echo isset( $recipient['recipient_address'] ) ? esc_html( $recipient['recipient_address'] ) : ''; ?><br><?php echo isset( $recipient['recipient_state'] ) ? esc_html( $recipient['recipient_state'] ) : ''; ?><br><?php echo isset( $recipient['recipient_email'] ) ? esc_html( $recipient['recipient_email'] ) : ''; ?></td>
                <td class="addresstext td-right-align" style="padding-right:0px;"><?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_address'] ) ? esc_html( $customer_billing['billing_address'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_state'] ) ? esc_html( $customer_billing['billing_state'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_email'] ) ? esc_html( $customer_billing['billing_email'] ) : ''; ?></td>
            </tr>
        </table>
        <br>
            <div>
                <div class="input-group-text bgcolor bordercolor textwhite spinner-button relative-positioned redeem_home"> <?php esc_html_e( 'Home', 'service-booking' ); ?></div>
            </div>
            <table class="footer">
                <tr>
                    <td colspan="2" class="copyright"><img src="<?php echo esc_url( $plugin_path . 'images/logo.png' ); ?>" style="width:200px;"/><br/><?php esc_html_e( 'Copyrights Reserved ', 'service-booking' ); ?> &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></td>
                </tr>
            </table>
        </table>
    </div>
	<?php
	return; }
