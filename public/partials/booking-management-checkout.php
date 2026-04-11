<?php
$bmrequests                = new BM_Request();
$dbhandler                 = new BM_DBhandler();
$bmi18n                    = new Booking_Management_i18n();
$field_data_html           = $bmrequests->bm_fetch_fields();
$countries                 = $bmrequests->bm_get_countries();
$ordered_products          = array();
$plugin_path               = plugin_dir_url( __FILE__ );
$payment_message           = '';
$flexi_booking             = filter_input( INPUT_GET, 'flexi_booking' );
$flexi_payment             = filter_input( INPUT_GET, 'flexi_payment' );
$pid                       = filter_input( INPUT_GET, 'pid' );
$session_status            = filter_input( INPUT_GET, 'status' );
$payment_txn_id            = 0;
$payment_ref_id            = 0;
$existsting_booking_id     = 0;
$customer                  = '';
$session_message           = $dbhandler->bm_fetch_data_from_transient( 'bm_latest_payment_status_message' . $flexi_booking );
$statusMsg                 = __( 'Transaction has failed!', 'service-booking' );
$payment_status            = 'error';
$negative_discount         = 0;
$productDetails            = array();
$total_discounted_infants  = 0;
$total_discounted_children = 0;
$total_discounted_adults   = 0;
$total_discounted_seniors  = 0;
$infants_age_from          = 0;
$children_age_from         = 0;
$adults_age_from           = 0;
$seniors_age_from          = 0;
$infants_age_to            = 0;
$children_age_to           = 0;
$adults_age_to             = 0;
$seniors_age_to            = 0;
$infants_total_discount    = 0;
$children_total_discount   = 0;
$adults_total_discount     = 0;
$seniors_total_discount    = 0;
$infants_total             = 0;
$children_total            = 0;
$adults_total              = 0;
$seniors_total             = 0;
$group_discount            = 0;
$coupon_discount           = 0;
$infants_discount_type     = 'positive';
$children_discount_type    = 'positive';
$adults_discount_type      = 'positive';
$seniors_discount_type     = 'positive';
$discount_type             = 'positive';

if ( $flexi_booking == false || $flexi_booking == null ) {
	$flexi_booking = 0;
}

if ( $flexi_payment == false || $flexi_payment == null ) {
	$flexi_payment = 0;
}

if ( $pid == false || $pid == null ) {
	$pid = 0;
}

if ( $session_status == false || $session_status == null ) {
	$session_status = 0;
}

// trp language
$trp_lang     = get_option( 'trp_lang_' . $flexi_booking, false );
$current_lang = $bmi18n->bm_search_language();
if ( $current_lang && in_array( $current_lang, array( 'en', 'it' ) ) && $flexi_booking != 0 && ! $trp_lang ) {
		update_option( 'trp_lang_' . $flexi_booking, $current_lang );
}

if ( ! empty( $flexi_booking ) ) {
	$ordered_products   = $bmrequests->bm_fetch_booked_service_info_for_checkout( $flexi_booking );
	$payment_message    = $bmrequests->bm_fetch_payment_message_for_checkout_page( $flexi_booking );
	$negative_discount  = $dbhandler->get_global_option_value( 'negative_discount_' . $flexi_booking, 0 );
	$is_allowed_as_gift = $bmrequests->bm_check_if_booked_service_is_allowed_as_gift( $flexi_booking );

	$existsting_booking_id = $dbhandler->get_value( 'BOOKING', 'id', $flexi_booking, 'booking_key' );
}

if ( ! empty( $pid ) ) {
	$payment_txn_id = $pid;
	$booking_id     = $dbhandler->get_value( 'BOOKING', 'id', $pid, 'booking_key' );
	$transaction    = $dbhandler->get_row( 'TRANSACTIONS', $booking_id, 'booking_id' );

	if ( ! empty( $transaction ) ) {
		$payment_ref_id               = isset( $transaction->id ) ? $transaction->id : 0;
		$booking_type                 = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );
		$serviceDate                  = $dbhandler->get_value( 'BOOKING', 'booking_date', $booking_id, 'id' );
		$coupons                      = $dbhandler->get_value( 'BOOKING', 'coupons', $booking_id, 'id' );
		$productDetails               = $bmrequests->bm_fetch_product_info_order_details_page( $booking_id );
		$customer_id                  = isset( $transaction->customer_id ) ? $transaction->customer_id : 0;
		$customer                     = $dbhandler->get_row( 'CUSTOMERS', $customer_id );
		$customer_billing             = ! empty( $customer ) && isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : '';
		$customer_shipping            = ! empty( $customer ) && isset( $customer->shipping_details ) ? maybe_unserialize( $customer->shipping_details ) : '';
		$price_module_data            = $dbhandler->get_value( 'BOOKING', 'price_module_data', $booking_id, 'id' );
		$price_module_data            = ! empty( $price_module_data ) ? maybe_unserialize( $price_module_data ) : array();
		$order_confirmation_header    = esc_html__( 'Thanks for Your Order.', 'service-booking' );
		$order_confirmation_subheader = esc_html__( 'Your order is Confirmed. You will receive a confirmation mail in your billing email.', 'service-booking' );

		if ( ! empty( $price_module_data ) && is_array( $price_module_data ) ) {
			$total_discounted_infants  = isset( $price_module_data['infant']['total'] ) ? intval( $price_module_data['infant']['total'] ) : 0;
			$total_discounted_children = isset( $price_module_data['children']['total'] ) ? intval( $price_module_data['children']['total'] ) : 0;
			$total_discounted_adults   = isset( $price_module_data['adult']['total'] ) ? intval( $price_module_data['adult']['total'] ) : 0;
			$total_discounted_seniors  = isset( $price_module_data['senior']['total'] ) ? intval( $price_module_data['senior']['total'] ) : 0;

			$infants_age_from  = isset( $price_module_data['infant']['age']['from'] ) ? intval( $price_module_data['infant']['age']['from'] ) : 0;
			$children_age_from = isset( $price_module_data['children']['age']['from'] ) ? intval( $price_module_data['children']['age']['from'] ) : 0;
			$adults_age_from   = isset( $price_module_data['adult']['age']['from'] ) ? intval( $price_module_data['adult']['age']['from'] ) : 0;
			$seniors_age_from  = isset( $price_module_data['senior']['age']['from'] ) ? intval( $price_module_data['senior']['age']['from'] ) : 0;

			$infants_age_to  = isset( $price_module_data['infant']['age']['to'] ) ? intval( $price_module_data['infant']['age']['to'] ) : 0;
			$children_age_to = isset( $price_module_data['children']['age']['to'] ) ? intval( $price_module_data['children']['age']['to'] ) : 0;
			$adults_age_to   = isset( $price_module_data['adult']['age']['to'] ) ? intval( $price_module_data['adult']['age']['to'] ) : 0;
			$seniors_age_to  = isset( $price_module_data['senior']['age']['to'] ) ? intval( $price_module_data['senior']['age']['to'] ) : 0;

			$infants_total_discount  = isset( $price_module_data['infant']['total_discount'] ) ? floatval( $price_module_data['infant']['total_discount'] ) : 0;
			$children_total_discount = isset( $price_module_data['children']['total_discount'] ) ? floatval( $price_module_data['children']['total_discount'] ) : 0;
			$adults_total_discount   = isset( $price_module_data['adult']['total_discount'] ) ? floatval( $price_module_data['adult']['total_discount'] ) : 0;
			$seniors_total_discount  = isset( $price_module_data['senior']['total_discount'] ) ? floatval( $price_module_data['senior']['total_discount'] ) : 0;

			$infants_total  = isset( $price_module_data['infant']['total_cost'] ) ? floatval( $price_module_data['infant']['total_cost'] ) : 0;
			$children_total = isset( $price_module_data['children']['total_cost'] ) ? floatval( $price_module_data['children']['total_cost'] ) : 0;
			$adults_total   = isset( $price_module_data['adult']['total_cost'] ) ? floatval( $price_module_data['adult']['total_cost'] ) : 0;
			$seniors_total  = isset( $price_module_data['senior']['total_cost'] ) ? floatval( $price_module_data['senior']['total_cost'] ) : 0;

			$infants_discount_type  = isset( $price_module_data['infant']['discount_type'] ) ? $price_module_data['infant']['discount_type'] : 'positive';
			$children_discount_type = isset( $price_module_data['children']['discount_type'] ) ? $price_module_data['children']['discount_type'] : 'positive';
			$adults_discount_type   = isset( $price_module_data['adult']['discount_type'] ) ? $price_module_data['adult']['discount_type'] : 'positive';
			$seniors_discount_type  = isset( $price_module_data['senior']['discount_type'] ) ? $price_module_data['senior']['discount_type'] : 'positive';

			$group_discount          = isset( $price_module_data['group_discount'] ) ? floatval( $price_module_data['group_discount'] ) : 0;
			$discount_type           = isset( $price_module_data['discount_type'] ) ? $price_module_data['discount_type'] : 'positive';
			$negative_group_discount = $dbhandler->get_global_option_value( 'negative_group_discount_' . $pid, 0 );
		}

		if ( $booking_type == 'on_request' ) {
			$order_confirmation_header    = esc_html__( 'Thanks for Your Request.', 'service-booking' );
			$order_confirmation_subheader = esc_html__( 'Your booking request is received. You will receive a mail in your billing email when your order is confirmed or cancelled.', 'service-booking' );
		}

		if ( ! empty( $coupons ) ) {
			if ( $group_discount > 0 ) {
				$coupon_discount = isset( $productDetails['discount'] ) ? ( $productDetails['discount'] - ( $infants_total_discount + $children_total_discount + $group_discount ) ) : 0;
			} else {
				$coupon_discount = isset( $productDetails['discount'] ) ? ( $productDetails['discount'] - ( $infants_total_discount + $children_total_discount + $adults_total_discount + $seniors_total_discount ) ) : 0;
			}
		}
	}

	if ( ! empty( $payment_txn_id ) && ! empty( $transaction ) && ! empty( $customer ) ) {
		$payment_status = 'success';
		$statusMsg      = __( 'Your Payment has been Successful!', 'service-booking' );
	}
}

if ( empty( $payment_message ) ) {
	$payment_message = __( 'You are making a direct booking. Please accept terms and conditions before proceeding', 'service-booking' );
}

if ( empty( $session_message ) ) {
	$session_message = __( 'Please retry', 'service-booking' );
}

$primary_color      = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
$contrast           = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );

if ( $existsting_booking_id > 0 ) {
	?>
	<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
		<div class="payment_info_div">
			<p class="error" style="text-align:center;"><?php esc_html_e( 'Booking session expired !!', 'service-booking' ); ?></p>
		</div>
	</div>
	<?php
	return false; }

if ( $dbhandler->get_global_option_value( 'bm_enable_stripe', 0 ) == 1 ) {
	if ( ! empty( $flexi_booking ) && empty( $flexi_payment ) && empty( $pid ) ) {
		if ( empty( $session_status ) ) {
			if ( ! empty( $ordered_products ) ) {
				?>

	<div class="container checkout_page" style="width:100%; max-width: inherit;margin: auto;">
		<div class="pagewrapper">
			<div class="topbar">
				<div class="progress-container">
					<div class="progress" id="progress"></div>
					<div class="circle textblue" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>"><i style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" class="fa fa-check"></i></div>
					<div class="circle bgcolor textwhite" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>"><span style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>"><?php echo esc_attr( '2' ); ?></span></div>
					<div class="circle "><?php echo esc_attr( '3' ); ?></div>
				</div>
			</div>

            <div style="width:100%;float: left; <?php echo !empty( $field_data_html ) ? 'border: 1px solid #E1E8EE; margin-bottom:20px;' : ''; ?>">
                <?php if ( !empty( $field_data_html ) ) { ?>
                <div class="fullbox" id="checkout_form">
                    <div class="part" id="checkout_form_fields">
                    <?php
                    if ( $is_allowed_as_gift ) {
                        ?>
                            <div class="inputcheckgroup checkbox_and_radio_div">
                                <input type="checkbox" name="is_gift" class="checkbox-size" id="is_gift" style="cursor: pointer;" onclick="bm_slide_up_down('gift_fields')"/>
                                <label for="is_gift" style="cursor:pointer;"><?php esc_html_e( 'Is it a gift ?', 'service-booking' ); ?></label>
                            </div>
                            <div class="formtable hidden" id="gift_fields" style="margin-bottom:10px;">
                            <h3 class="sub-heading"><?php esc_html_e( 'Gift Recipient details', 'service-booking' ); ?></h3>
                            <div class="formbox">
                                <label for="recipient_first_name"><?php esc_html_e( 'First name', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="text" name="gift_details['first_name']" id="recipient_first_name" placeholder="<?php echo esc_html__( 'enter first name' ); ?>" required>
                            </div>
                            <div class="formbox">
                                <label for="recipient_last_name"><?php esc_html_e( 'Last name', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="text" name="gift_details['last_name']" id="recipient_last_name" placeholder="<?php echo esc_html__( 'enter last name' ); ?>" required>
                            </div>
                            <div class="formbox">
                                <label for="recipient_email"><?php esc_html_e( 'Email', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="email" name="gift_details['email']" id="recipient_email" placeholder="<?php echo esc_html__( 'enter email' ); ?>" required>
                            </div>
                            <div class="formbox">
                                <label for="recipient_contact"><?php esc_html_e( 'Mobile No', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="tel" name="gift_details['contact']" class="intl_phone_field_input" id="recipient_contact" placeholder="<?php echo esc_html__( 'enter contact number' ); ?>" required>
                            </div>
                            <div class="formbox textarea-formbox">
                                <label for="recipient_address"><?php esc_html_e( 'Address', 'service-booking' ); ?></label>
                                <textarea name="gift_details['address']" rows="5" columns="5" id="recipient_address" placeholder="<?php echo esc_html__( 'enter address' ); ?>"></textarea>
                            </div>
                            <div class="formbox checkbox_and_radio_div">
                                <label for="recipient_country"><?php esc_html_e( 'Country', 'service-booking' ); ?></label>
                                <select name="gift_details['country']" id="recipient_country">
                                <?php
                                if ( !empty( $countries ) ) {
                                    foreach ( $countries as $key => $country ) {
                                        ?>
                                    <option value="<?php echo esc_html( $key ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_country' ), esc_html( $key ) ); ?>><?php echo esc_html( $country ); ?></option>
                                            <?php
                                    }
                                }
                                ?>
                                </select>
                            </div>
                            <div class="formbox">
                                <label for="recipient_state"><?php esc_html_e( 'State', 'service-booking' ); ?></label>
                                <select name="gift_details['state']" id="recipient_state"></select>
                            </div>
                            <div class="formbox">
                                <label for="recipient_city"><?php esc_html_e( 'City', 'service-booking' ); ?></label>
                                <input type="text" name="gift_details['city']" id="recipient_city" placeholder="<?php echo esc_html__( 'enter city' ); ?>">
                            </div>
                            <div class="formbox">
                                <label for="recipient_postcode"><?php esc_html_e( 'Postcode', 'service-booking' ); ?></label>
                                <input type="number" name="gift_details['postcode']" id="recipient_postcode" placeholder="<?php echo esc_html__( 'enter zip code' ); ?>">
                            </div>
                        </div>
                    <?php } ?>
                        <h3 class="sub-heading sub-heading-billing-details"><?php esc_html_e( 'Billing details', 'service-booking' ); ?></h3>
                        <div class="inputcheckgroup checkbox_and_radio_div hidden">
                            <input type="checkbox" name="shipping_same_as_billing" class="checkbox-size" id="shipping_same_as_billing" style="cursor: pointer;" onclick="bm_open_close_tab('shipping_fields')" checked/>
                            <label for="shipping_same_as_billing" style="cursor:pointer;"><?php esc_html_e( 'Shipping same as billing', 'service-booking' ); ?></label>
                        </div>
                        <?php echo wp_kses( $field_data_html, $bmrequests->bm_fetch_expanded_allowed_tags() ); ?>
                        <div class="formtable hidden" id="shipping_fields">
                            <h3 class="sub-heading sub-heading-billing-details"><?php esc_html_e( 'Shipping details', 'service-booking' ); ?></h3>
                            <div class="formbox">
                                <label for="shipping_first_name"><?php esc_html_e( 'First name', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="text" name="shipping_details['first_name']" id="shipping_first_name" placeholder="enter first name" required>
                            </div>
                            <div class="formbox">
                                <label for="shipping_last_name"><?php esc_html_e( 'Last name', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="text" name="shipping_details['last_name']" id="shipping_last_name" placeholder="enter last name" required>
                            </div>
                            <div class="formbox">
                                <label for="shipping_email"><?php esc_html_e( 'Email', 'service-booking' ); ?> <strong class="required_asterisk"> *</strong></label>
                                <input type="email" name="shipping_details['email']" id="shipping_email" placeholder="enter email" required>
                            </div>
                            <div class="formbox">
                                <label for="shipping_contact"><?php esc_html_e( 'Mobile No', 'service-booking' ); ?></label>
                                <input type="tel" name="shipping_details['contact']" class="intl_phone_field_input" id="shipping_contact" placeholder="enter contact number">
                            </div>
                            <div class="formbox textarea-formbox">
                                <label for="shipping_address"><?php esc_html_e( 'Address', 'service-booking' ); ?></label>
                                <textarea name="shipping_details['address']" rows="5" columns="5" id="shipping_address" placeholder="enter address"></textarea>
                            </div>
                            <div class="formbox checkbox_and_radio_div">
                                <label for="shipping_country"><?php esc_html_e( 'Country', 'service-booking' ); ?></label>
                                <select name="shipping_details['country']" id="shipping_country">
                                    <?php
                                    if ( !empty( $countries ) ) {
                                        foreach ( $countries as $key => $country ) {
                                            ?>
                                        <option value="<?php echo esc_html( $key ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_booking_country' ), esc_html( $key ) ); ?>><?php echo esc_html( $country ); ?></option>
                                                <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="formbox">
                                <label for="shipping_state"><?php esc_html_e( 'State', 'service-booking' ); ?></label>
                                <select name="shipping_details['state']" id="shipping_state"></select>
                            </div>
                            <div class="formbox">
                                <label for="shipping_city"><?php esc_html_e( 'City', 'service-booking' ); ?></label>
                                <input type="text" name="shipping_details['city']" id="shipping_city" placeholder="enter city">
                            </div>
                            <div class="formbox">
                                <label for="shipping_postcode"><?php esc_html_e( 'Postcode', 'service-booking' ); ?></label>
                                <input type="number" name="shipping_details['postcode']" id="shipping_postcode" placeholder="enter zip code">
                            </div>
                        </div>
                    </div>

					<div class="part" id="checkout_form_order_details">
						<?php
						if ( isset( $ordered_products['age_html'] ) && ! empty( $ordered_products['age_html'] ) ) {
							if ( isset( $ordered_products['hidden_qty'] ) && $ordered_products['hidden_qty'] == false ) {
								?>
							<h3 class="sub-heading"><?php esc_html_e( 'Service discount', 'service-booking' ); ?>
							<i class="fa fa-chevron-down" style="cursor: pointer;" onclick="bm_slide_up_down('agedicountsection')" style="cursor: pointer;"></i>
						</h3>
							<div class="agedicountsection" id="agedicountsection">
							<span class="age_errortext"></span>
								
								<?php
								echo wp_kses( $ordered_products['age_html'], $bmrequests->bm_fetch_expanded_allowed_tags() );
								?>
								<div class="checkout_discount_buttons">
									<span class="primarybutton bgcolor" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>">
										<a href="#" id="check_checkout_discount" class="check_checkout_discount" style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important;'; ?>" title="<?php esc_html_e( 'Calculate', 'service-booking' ); ?>">
											<?php esc_html_e( 'Calculate', 'service-booking' ); ?>
											<!-- <i class="fa fa-calculator"></i> -->
										</a>
									</span>
									<span class="secondarybutton" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>">
										<a href="#" id="reset_checkout_discount" class="reset_checkout_discount" style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" title="<?php esc_html_e( 'Reset', 'service-booking' ); ?>">
											<?php esc_html_e( 'Reset', 'service-booking' ); ?>
											<!-- <i class="fa fa-refresh"></i> -->
										</a>
									</span>
								</div>
							</div>
								<?php
							}
						}
						?>

						<h3 class="sub-heading order_price_heading"><?php esc_html_e( 'Your order details', 'service-booking' ); ?> <i class="fa fa-chevron-down" style="cursor: pointer;" onclick="bm_slide_up_down('expandorderbox')" style="cursor: pointer;"></i></h3>
						<div class="expandorderbox" id="expandorderbox">
						<?php
						echo do_shortcode( '[sgbm_flexibooking_coupon_form]' );
						?>
						<ul class="ordertextbox">
							<li><?php esc_html_e( 'Service Date', 'service-booking' ); ?><span><?php echo isset( $ordered_products['service_datetime'] ) ? esc_html( $ordered_products['service_datetime'] ) : ''; ?></span><br /></li>
                                                        <?php do_action('bm_checkout_after_service_date',$flexi_booking,$ordered_products);?>
						
					<?php
					if ( ! empty( $ordered_products ) && is_array( $ordered_products ) && isset( $ordered_products['product'] ) && ! empty( $ordered_products['product'] ) ) {
						foreach ( $ordered_products['product'] as $key => $product ) {
							?>
							<li title="<?php echo isset( $product['name'] ) ? esc_html( $product['name'] ) : 'NA'; ?>">
									<?php
									echo isset( $product['name'] ) ? esc_html( mb_strimwidth( $product['name'], 0, 40, '...' ) ) : 'NA';
									if ( $key > 0 ) {
										?>
										<?php echo esc_html( 'x' ); ?> <?php echo isset( $product['quantity'] ) ? esc_attr( $product['quantity'] ) : 0; ?>
										<?php
									} elseif ( isset( $ordered_products['hidden_qty'] ) && $ordered_products['hidden_qty'] == false ) {
										?>
											<?php echo esc_html( 'x' ); ?> <?php echo isset( $product['quantity'] ) ? esc_attr( $product['quantity'] ) : 0; ?>
												<?php
									}
									?>
								<span><?php echo isset( $product['amount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['amount'], true ) ) : 0; ?>
							</span></li>
										<?php
						}
					}
					?>
                                    <li><?php esc_html_e( 'Subtotal', 'service-booking' ); ?><span id="checkout_subtotal"><?php echo isset( $ordered_products['subtotal'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['subtotal'], true ) ) : esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['total'], true ) ); ?></span><br /></li>
                                    <li class="<?php echo isset( $ordered_products['discount'] ) && $ordered_products['discount'] <= 0 ? esc_html( 'discount_li hidden' ) : 'discount_li'; ?>"><?php esc_html_e( 'Discount', 'service-booking' ); ?> <span id="checkout_discount" class="<?php echo $negative_discount == 1 ? 'negative_discount' : 'positive_discount'; ?>"><?php echo isset( $ordered_products['discount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['discount'], true ) ) : esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( 0 ) ); ?></span></li>
                            </ul>
                                
                            <p class="total"><?php esc_html_e( 'Total Cost ', 'service-booking' ); ?> <span id="checkout_total"><?php echo isset( $ordered_products['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['total'], true ) ) : 0; ?></span></p>
                            </div>

                            <div class="formbottominnerbox">
                                <div class="inputcheckgroup formbottomcheckgroup">
                                    <input type="checkbox" name="terms_conditions1" id="terms_conditions1" class="checkbox-size" style="cursor: pointer;" required/>
                                    <label for="terms_conditions1" style="cursor:pointer;">
						<?php
						echo esc_html__( 'You must accept terms and conditions beofre proceeding to the payment page', 'service-booking' );
						?>
                                    </label>
                                </div>
                            </div>

							<div class="formbottominnerbox">
								<div class="inputcheckgroup formbottomcheckgroup">
									<input type="checkbox" name="terms_conditions" id="terms_conditions" class="checkbox-size" style="cursor: pointer;" required/>
									<label for="terms_conditions" style="cursor:pointer;">
						<?php
						echo wp_kses_post( $payment_message );
						?>
									</label>
								</div>
							</div>
						</div>
					</div>

					<div class="formbottombox">
						<div id="checkout_response" class="hidden"></div>
						<span id="checkout_loader" class="hidden"><img style="display: inline-flex;left: 4%;position:relative;" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">&nbsp;&nbsp;<span class="checkout_processing"><?php esc_html_e( 'Processing...', 'service-booking' ); ?></span></span>
						<div class="formbottombuttonbar">
							<div class="formbuttoninnerbox checkoutinnerbox">
								<div class="cancelbtn" id="booking_home"><?php esc_html_e( 'Cancel', 'service-booking' ); ?></div>
								<div class="payement_button_parent">
									<div class="bookbtn bgcolor textwhite" style="cursor:pointer;background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" id= "<?php echo empty( $ordered_products['total'] ) ? 'free_booking_no_payment' : 'go_to_payment_page'; ?>">
						<span><?php empty( $ordered_products['total'] ) ? esc_html_e( 'Free Booking', 'service-booking' ) : esc_html_e( 'Pay', 'service-booking' ); ?><span>                                    &nbsp;
						<?php
						if ( isset( $ordered_products['total'] ) && ! empty( $ordered_products['total'] ) ) {
							echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['total'], true ) );
						}
						?>
									</div>
								</div>
							</div>
						</div>
					</div>
						<?php
				} else {
					?>
							<h4 style="width:100%;text-align:center;padding:10px 10px;font-weight:600;"><?php esc_html_e( 'No billing fields found!!, add fields first', 'service-booking' ); ?></h4>
					<?php
				}
				?>
				</div>
			</div>
		</div>
	</div>
					<?php
			} else {
				?>
		<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
			<div class="payment_info_div">
				<h3 style="text-align:center;"><?php esc_html_e( 'No Items on cart', 'service-booking' ); ?></h3>
				<p class="error" style="text-align:center;"><?php esc_html_e( 'please place a new order !!', 'service-booking' ); ?></p>
			</div>
		</div>
				<?php
			}
		} else {
			?>
		<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
			<div class="payment_info_div">
				<h3 style="text-align:center;"><?php esc_html_e( 'Booking could not be completed', 'service-booking' ); ?></h3>
				<p class="error" style="text-align:center;"><?php echo wp_kses_post( $session_message ); ?></p>
			</div>
			<div class="formbuttoninnerbox" style="display: block;">
				<div class="bookbtn bgcolor textwhite" style="cursor:pointer;background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>" id="booking_home"><span style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>"><?php esc_html_e( 'Home', 'service-booking' ); ?></span></div>
			</div>
		</div>
			<?php
		}
	} elseif ( ! empty( $flexi_booking ) && ! empty( $flexi_payment ) && empty( $pid ) ) {
		if ( ! empty( $ordered_products ) ) {
			if ( $bmrequests->bm_is_session_expired( "flexi_current_payment_session_$flexi_booking" ) ) {
				?>
<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
	<div class="payment_info_div">
		<p class="error" style="text-align:center;"><?php esc_html_e( 'Your payment session has expired !!', 'service-booking' ); ?></p>
		<br />
		<button class="payment-form-btn reinit-payment-btn" onClick="window.location.href=window.location.href.split('&')[0]"><i class="rload"></i><?php esc_html_e( 'Re-initiate Payment', 'service-booking' ); ?></button>
	</div>
</div>
			<?php } else { ?>
<div class="container" id="payment_page" style="width:100%;max-width: inherit; margin:auto;">
<div class="pagewrapper">
	<div class="topbar">
		<div class="progress-container">
			<div class="progress" id="progress"></div>
			<div class="circle textblue" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>"><i style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" class="fa fa-check"></i></div>
			<div class="circle textblue" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>"><i style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" class="fa fa-check"></i></div>
			<div class="circle bgcolor textwhite" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>"><span style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>"><?php echo esc_attr( '3' ); ?></span></div>
		</div>
	</div>

	<div class="timer-alert bgcolor bordercolor" class="hidden" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
		<span class="timer-closebtn" style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" onclick="this.parentElement.style.display='none';">&times;</span>
		<div id="countdown"></div>
	</div>

	<div style="width:100%;float: left; border: 1px solid #E1E8EE; margin-bottom:20px;">
		<h3 class="sub-heading"><?php esc_html_e( 'Complete Payment', 'service-booking' ); ?><br/></h3>
		<div id="paymentResponse" class="hidden"></div>
		<div class="fullbox" id="main_payment_section">
			<div class="part" id="payment_main_section_1">
				<div class="formtable custom-formtable">
					<form id="stripes_paymentFrm" class="hidden">
						<div id="stripes_paymentElement"></div>
						<div id="paymentButtonDiv">
							<button id="paymentSubmitBtn" class="payment-form-btn payment-btn bgcolor" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
								<div class="spinner hidden" id="spinner"></div>
								<span id="buttonText">
								<?php
									empty( $ordered_products['total'] ) ? esc_html_e( 'Free Booking', 'service-booking' ) : esc_html_e( 'Pay', 'service-booking' );
								?>
									&nbsp;
									<?php
									if ( isset( $ordered_products['total'] ) && ! empty( $ordered_products['total'] ) ) {
										echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['total'], true ) );
									}
									?>
								</span>
							</button>
						</div>
					</form>
				</div>
			</div>
			<div class="part" id="payment_main_section_2">
				<div class="expandorderbox" id="expandorderbox">
					<ul class="ordertextbox">
						<li><?php esc_html_e( 'Service Date', 'service-booking' ); ?><span><?php echo isset( $ordered_products['service_datetime'] ) ? esc_html( $ordered_products['service_datetime'] ) : ''; ?></span><br /></li>
                                                <?php do_action('bm_checkout_after_service_date',$flexi_booking,$ordered_products);?>
						
						<?php
						if ( ! empty( $ordered_products ) && is_array( $ordered_products ) && isset( $ordered_products['product'] ) ) {
							foreach ( $ordered_products['product'] as $key => $product ) {
								?>
								<li title="<?php echo isset( $product['name'] ) ? esc_html( $product['name'] ) : 'NA'; ?>">
									<?php
									echo isset( $product['name'] ) ? esc_html( mb_strimwidth( $product['name'], 0, 40, '...' ) ) : 'NA';
									if ( $key > 0 ) {
										?>
										<?php echo esc_html( 'x' ); ?> <?php echo isset( $product['quantity'] ) ? esc_attr( $product['quantity'] ) : 0; ?>
										<?php
									} elseif ( isset( $ordered_products['hidden_qty'] ) && $ordered_products['hidden_qty'] == false ) {
										?>
											<?php echo esc_html( 'x' ); ?> <?php echo isset( $product['quantity'] ) ? esc_attr( $product['quantity'] ) : 0; ?>
											<?php
									}
									?>
								<span><?php echo isset( $product['amount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['amount'], true ) ) : 0; ?></span></li>
									<?php
							}
						}
						?>
						<li><?php esc_html_e( 'Subtotal', 'service-booking' ); ?><span><?php echo isset( $ordered_products['subtotal'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['subtotal'], true ) ) : esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['total'], true ) ); ?></span><br /></li>
						<li class="<?php echo isset( $ordered_products['discount'] ) && $ordered_products['discount'] <= 0 ? esc_html( 'discount_li hidden' ) : 'discount_li'; ?>"><?php esc_html_e( 'Discount', 'service-booking' ); ?> <span class="<?php echo $negative_discount == 1 ? 'negative_discount' : 'positive_discount'; ?>"><?php echo isset( $ordered_products['discount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['discount'], true ) ) : esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( 0 ) ); ?></span></li>
					</ul>
						
					<p class="total"><?php esc_html_e( 'Total Cost ', 'service-booking' ); ?> <span><?php echo isset( $ordered_products['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $ordered_products['total'], true ) ) : 0; ?></span></p>
				</div>
			</div>
			<!-- Display processing notification -->
			<div id="frmProcess" class="hidden">
				<img id="payment_loader" class="hidden" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">&nbsp;&nbsp;<span class="payment_processing"><?php esc_html_e( 'Processing...', 'service-booking' ); ?></span>
			</div>

			<!-- Display re-initiate button -->
			<div id="payReinit" class="hidden">
				<button class="payment-form-btn reinit-payment-btn" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" onClick="window.location.href=window.location.href.split('&')[0]"><i class="rload"></i><?php esc_html_e( 'Re-initiate Payment', 'service-booking' ); ?></button>
			</div>
		</div>
	</div>
</div>
</div>
					<?php
			}
		} else {
			?>
<div class="payment_info">
	<div class="payment_info_div" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
		<h3 style="text-align:center;"><?php esc_html_e( 'No Items on cart', 'service-booking' ); ?></h3>
		<p class="error" style="text-align:center;"><?php esc_html_e( 'please place a new order !!', 'service-booking' ); ?></p>
	</div>
</div>
			<?php
		}
	} elseif ( empty( $flexi_booking ) && ! empty( $pid ) && ! empty( $payment_ref_id ) && isset( $productDetails['products'] ) && ! empty( $productDetails['products'] ) ) {
		$ga_purchase_data = apply_filters( 'flexibooking_google_analytics_data', $pid );

		if ( $ga_purchase_data ) {
			if ( is_string( $ga_purchase_data ) ) {
				$ga_purchase_data = array();
			}
		}

        $items = ! empty( $ga_purchase_data['items'] ) && is_array( $ga_purchase_data['items'] )
		? $ga_purchase_data['items']
		: array();

		$ga4_purchase_data = array(
			'event'        => 'purchase',
			'ecommerce'    => array(
				'transaction_id' => $ga_purchase_data['transactionId'],
				'value'          => floatval( $ga_purchase_data['transactionTotal'] ),
				'currency'       => $ga_purchase_data['currency'],
				'tax'            => floatval( $ga_purchase_data['tax'] ),
				'shipping'       => floatval( $ga_purchase_data['shipping'] ),
				'items'          => array_map(
					function ( $item ) {
						return array(
							'item_id'   => $item['itemId'] ?? '',
							'item_name' => $item['itemName'] ?? '',
							'price'     => floatval( $item['price'] ?? 0 ),
							'quantity'  => intval( $item['quantity'] ?? 0 ),
						);
					},
					$items
				),
			),
			'customerData' => $ga_purchase_data['customerData'],
			'orderDate'    => $ga_purchase_data['orderDate'],
		);
		?>

<script>
window.dataLayer = window.dataLayer || [];

window.dataLayer = window.dataLayer.filter(function(e) {
	if (e.event === "purchase" && e.transactionId && e.transactionId !== "<?php echo esc_js( $ga_purchase_data['transactionId'] ); ?>") {
		return false;
	}
	if (e.event === "purchase") {
		return false;
	}
	return true;
});

dataLayer.push(<?php echo wp_json_encode( $ga4_purchase_data ); ?>);
</script>
		<div class="booking_details">
<table class="booking-container">
		<tr>
			<td>
				<table class="header">
					<tr>
						<td>
							<h1><?php esc_html_e( 'Hey ', 'service-booking' ); ?> <?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?>!</h1>
							<p><?php echo esc_html( $order_confirmation_header ); ?></p>
						</td>
					</tr>
				</table>
				
				<table class="order-details">
					<tr>
						<td colspan="5">
							<p><?php echo esc_html( $order_confirmation_subheader ); ?></p>
						</td>
					</tr>
					<tr>
						<td class="subheading"><strong><?php esc_html_e( 'Service Date: ', 'service-booking' ); ?></strong><br/>
							<span><?php echo isset( $serviceDate ) ? esc_html( $bmrequests->bm_month_year_date_format( $serviceDate ) ) : ''; ?></span>
						</td>
                                                <td colspan="1" class="subheading td-center-align"><strong><?php esc_html_e( 'Order Ref: ', 'service-booking' ); ?></strong><br/>
							<span><?php echo esc_html( $payment_txn_id ); ?></span></td>
						<td colspan="3" class="subheading td-right-align"><strong><?php esc_html_e( 'Payment via ', 'service-booking' ); ?></strong><br/>
							<span><?php esc_html_e( 'Card ', 'service-booking' ); ?></span></td>
					</tr>
		<?php
		$count = 1;
		foreach ( $productDetails['products'] as $product ) {
			?>
					
					<tr>
						<td colspan="2">
			<?php if ( $count == 1 ) { ?>
								<span class="theading"><?php esc_html_e( 'Main Product ', 'service-booking' ); ?></span><br>
			<?php } elseif ( $count == 2 ) { ?>
								<span class="theading"><?php esc_html_e( 'Extra Products ', 'service-booking' ); ?></span><br>
			<?php } ?>
								<span class="subtext"><?php echo isset( $product['name'] ) ? esc_html( $product['name'] ) : 'N/A'; ?></span>
						</td>
						<td><span class="theading"><?php esc_html_e( 'Price ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['base_price'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['base_price'], true ) ) : 'N/A'; ?></span></td>
						<td><span class="theading"><?php esc_html_e( 'Qty ', 'service-booking' ); ?></span><br/><span class="subtext"> <?php echo isset( $product['quantity'] ) ? esc_html( $product['quantity'] ) : 'N/A'; ?></span></td>
						<td class="td-right-align"><span class="theading td-right-align"><?php esc_html_e( 'Total ', 'total' ); ?></span><br/><span class="subtext"><?php echo isset( $product['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['total'], true ) ) : 'N/A'; ?></span></td>
					</tr>
			<?php
			++$count;
		}
		?>
					<tr >
						<td class="subtotal " colspan="4"><?php esc_html_e( 'Subtotal ', 'service-booking' ); ?></td>
						<td class="subtotal td-right-align"><?php echo isset( $productDetails['subtotal'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['subtotal'], true ) ) : 'N/A'; ?></td>
					</tr>
					<tr class="noborder">
						<td colspan="4"><?php esc_html_e( 'Discount ', 'service-booking' ); ?></td>
						<td class="discountvalue td-right-align">-<?php echo isset( $productDetails['discount'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['discount'], true ) ) : 'N/A'; ?></td>
					</tr>
					<tr class="totalbar">
						<td colspan="4"><?php esc_html_e( 'Total ', 'service-booking' ); ?></td>
						<td class="td-right-align"><?php echo isset( $productDetails['total'] ) ? esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['total'], true ) ) : 'N/A'; ?></td>
					</tr>
				</table>
				<table class="billing-shipping noborder">
					<tr>
						<th><?php esc_html_e( 'Billing Address', 'service-booking' ); ?></th>
						<th class="td-right-align"><?php esc_html_e( 'Shipping Address', 'service-booking' ); ?></th>
					</tr>
					<tr>
						<td class="addresstext"><?php echo isset( $customer_billing['billing_first_name'] ) ? esc_html( $customer_billing['billing_first_name'] ) . ' ' . esc_html( $customer_billing['billing_last_name'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_address'] ) ? esc_html( $customer_billing['billing_address'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_state'] ) ? esc_html( $customer_billing['billing_state'] ) : ''; ?><br><?php echo isset( $customer_billing['billing_email'] ) ? esc_html( $customer_billing['billing_email'] ) : ''; ?></td>
						<td class="addresstext td-right-align" style="padding-right:0px;"><?php echo isset( $customer_shipping['shipping_first_name'] ) ? esc_html( $customer_shipping['shipping_first_name'] ) . ' ' . esc_html( $customer_shipping['shipping_last_name'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_address'] ) ? esc_html( $customer_shipping['shipping_address'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_state'] ) ? esc_html( $customer_shipping['shipping_state'] ) : ''; ?><br><?php echo isset( $customer_shipping['shipping_email'] ) ? esc_html( $customer_shipping['shipping_email'] ) : ''; ?></td>
					</tr>
				</table>
		<?php
		if ( ! empty( $price_module_data ) || ! empty( $coupons ) ) {
			?>
				<table class="billing-shipping-notification noborder">
					<tr>
						<th>
						<i class="fa fa-exclamation-triangle" aria-hidden="true"></i>    
						<?php esc_html_e( 'This order has', 'service-booking' ); ?></th>
					</tr>
			<?php
			if ( ! empty( $price_module_data ) && is_array( $price_module_data ) ) {
				if ( ! empty( $total_discounted_infants ) ) {
					$infants_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $infants_total_discount, true );
					$class                  = $infants_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
					?>
						<tr>
							<td class="addresstext addresstext-notic">
							<i class="fa fa-hand-o-right" aria-hidden="true"></i>
					<?php echo esc_html( $total_discounted_infants ) . ' <strong>' . esc_html__( 'infant/s of the age group from ', 'service-booking' ) . esc_html( $infants_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $infants_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $infants_total_discount ) . '</span>'; ?>
							</td>
						</tr>
				<?php } ?>

				<?php
				if ( ! empty( $total_discounted_children ) ) {
					$children_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $children_total_discount, true );
					$class                   = $children_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
					?>
						<tr>
							<td class="addresstext addresstext-notic">
							<i class="fa fa-hand-o-right" aria-hidden="true"></i>
					<?php
					echo esc_html( $total_discounted_children ) . ' <strong>' . esc_html__( 'child/children of the age group from ', 'service-booking' ) . esc_html( $children_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $children_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $children_total_discount ) . '</span>';
					?>
							</td>
						</tr>
				<?php } ?>

				<?php
				if ( empty( $group_discount ) ) {
					if ( ! empty( $total_discounted_adults ) ) {
						$adults_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $adults_total_discount, true );
						$class                 = $adults_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
						?>
						<tr>
							<td class="addresstext addresstext-notic">
							<i class="fa fa-hand-o-right" aria-hidden="true"></i>
						<?php echo esc_html( $total_discounted_adults ) . ' <strong>' . esc_html__( 'adult/s of the age group from ', 'service-booking' ) . esc_html( $adults_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $adults_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $adults_total_discount ) . '</span>'; ?>
							</td>
						</tr>
					<?php } ?>

					<?php
					if ( ! empty( $total_discounted_seniors ) ) {
						$seniors_total_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $seniors_total_discount, true );
						$class                  = $seniors_discount_type == 'negative' ? 'negative_discount' : 'postive_price_module_discount';
						?>
						<tr>
							<td class="addresstext addresstext-notic">
							<i class="fa fa-hand-o-right" aria-hidden="true"></i>
						<?php echo esc_html( $total_discounted_seniors ) . ' <strong>' . esc_html__( 'senior/s of the age group from ', 'service-booking' ) . esc_html( $seniors_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $seniors_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $seniors_total_discount ) . '</span>'; ?>
							</td>
						</tr>
						<?php
					}
				} else {
								$class = $negative_group_discount == 1 ? 'negative_discount' : 'postive_price_module_discount';
					?>
								<tr>
							<td class="addresstext addresstext-notic">
							<i class="fa fa-hand-o-right" aria-hidden="true"></i>
					<?php echo esc_html( $total_discounted_adults + $total_discounted_seniors ) . ' <strong>' . esc_html__( 'adult/s and senior/s of the age group from ', 'service-booking' ) . esc_html( $adults_age_from ) . ' ' . esc_html__( 'to ', 'service-booking' ) . esc_html( $seniors_age_to ) . '</strong> ' . esc_html__( 'with total discount of ' ) . '<span class=' . esc_html( $class ) . '>' . esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $group_discount, true ) ) . '</span>'; ?>
							</td>
						</tr>
					<?php
				}
				?>
				<?php
			}
			if ( ! empty( $coupons ) && $coupon_discount > 0 ) {
				$coupon_discount = $bmrequests->bm_fetch_price_in_global_settings_format( $coupon_discount, true );
				?>
				<tr>
					<td class="addresstext addresstext-notic">
					<i class="fa fa-hand-o-right" aria-hidden="true"></i>
				<?php echo esc_html__( 'coupon/s ', 'service-booking' ) . '<strong>' . esc_html( $coupons ) . '</strong>' . ' ' . esc_html__( 'with total discount of ', 'service-booking' ) . '<span class="postive_price_module_discount">' . esc_html( $coupon_discount ) . '</span>'; ?>
					</td>
				</tr>
				<?php
			}
			?>
		</table>
		<?php } ?>
				<table class="discountbox">
					<tr>
						<td>
							<div class="discount">
								<div class="shopnowbtn" id="booking_home"> <?php esc_html_e( 'Home', 'service-booking' ); ?></div>
							</div>
						</td>
					</tr>
				</table>
				<table class="footer">
					<tr>
						<td colspan="2" class="copyright"><img src="<?php echo esc_url( $plugin_path . 'images/logo.png' ); ?>" style="width:200px;"/><br/><?php esc_html_e( 'Copyrights Reserved ', 'service-booking' ); ?> &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</div>
		<?php } elseif ( empty( $flexi_booking ) && ! empty( $pid ) && empty( $transaction ) ) { ?>
<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
	<div class="payment_info_div">
		<h3 class="error" style="text-align:center;"><?php esc_html_e( 'No Such Payment Details Found!', 'service-booking' ); ?></h3>
		<p style="text-align:center;"><?php esc_html_e( 'No transaction record', 'service-booking' ); ?></p>
	</div>
</div>
	<?php } elseif ( empty( $flexi_booking ) && ! empty( $transaction ) && empty( $payment_ref_id ) ) { ?>
<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
	<div class="payment_info_div">
		<h1 class="error" style="text-align:center;"><?php esc_html_e( 'Payment data could not be saved!', 'service-booking' ); ?></h1>
		<p style="text-align:center;"><?php echo esc_html( $statusMsg ); ?></p>
	</div>
	<div class="formbuttoninnerbox" style="display: block;">
		<div class="bookbtn bgcolor textwhite" style="cursor:pointer;background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>" id="booking_home"><?php esc_html_e( 'Home', 'service-booking' ); ?></div>
	</div>
</div>
			<?php
	} else {
		?>
		<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
			<div class="payment_info_div">
				<h3 style="text-align:center;"><?php esc_html_e( 'No order received', 'service-booking' ); ?></h3>
				<p class="error" style="text-align:center;"><?php esc_html_e( 'Please place an order first', 'service-booking' ); ?></p>
			</div>
		</div>
		<?php
	}
} else {
	?>
	<div class="payment_info" style="background:<?php echo esc_html( $svc_button_colour ) . '!important;'; ?>color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>">
		<div class="payment_info_div" style="cursor:pointer;">
			<p class="error" style="text-align:center;"><?php esc_html_e( 'Payment gateway is not enabled !!', 'service-booking' ); ?></p>
		</div>
	</div>
	<?php } ?>

<div class="loader_modal">
	<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
</div>
