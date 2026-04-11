<?php
$identifier  = 'CUSTOMERS';
$dbhandler   = new BM_DBhandler();
$bmrequests  = new BM_Request();
$customer_id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$countries   = $bmrequests->bm_get_countries();

$country_globally_set = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );

if ( empty( $customer_id ) || is_null( $customer_id ) ) {
    $customer_id = 0;
} else {
    $current_customer = $dbhandler->get_row( $identifier, $customer_id );
    $billing_details  = isset( $current_customer->billing_details ) ?  maybe_unserialize( $current_customer->billing_details ) : array();
    $shipping_details = isset( $current_customer->shipping_details ) ? maybe_unserialize( $current_customer->shipping_details ) : array();
}

if ( ( filter_input( INPUT_POST, 'savecust' ) ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_customer_nonce' ) ) {
        die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
    }

    $customer_id = filter_input( INPUT_POST, 'customer_id', FILTER_VALIDATE_INT );

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'savecust',
        'customer_id',
    );

    $customer = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

    if ( $customer ) {
        $customer['customer_name']            = isset( $customer['customer_name'] ) ? ucfirst( $customer['customer_name'] ) : '';
        $customer['customer_email']           = isset( $customer['customer_email'] ) ? strtolower( $customer['customer_email'] ) : '';
        $customer['shipping_same_as_billing'] = isset( $customer['shipping_same_as_billing'] ) ? 1 : 0;

        if ( empty( $customer_id ) ) {
            $customer['customer_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $customer_id                     = $dbhandler->insert_row( $identifier, $customer );
        } else {
            $customer['customer_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $updated                         = $dbhandler->update_row( $identifier, 'id', $customer_id, $customer, '', '%d' );
        }

        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_all_customers' ) );
        exit;
    } else {
        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
        echo esc_html__( 'Customer Data could not be Processed !!', 'service-booking' );
        echo ( '</div>' );
    }
}

?>

<div class="sg-admin-main-box">
<div class="wrap">
    <form role="form" method="post" id="customer_form">
        <h1 style="text-decoration :underline;"><?php $customer_id > 0 ? esc_html_e( 'Customer Details', 'service-booking' ) : esc_html_e( 'Add Customer', 'service-booking' ); ?></h1>
        <table class="form-table" role="presentation" id="customer_table">
            <tr>
                <th scope="row"><label for="is_active"><?php esc_html_e( 'Status', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                <td  class="bminput bm_required">
                    <select name="is_active" id="is_active" class="regular-text">
                        <option value="1" <?php isset( $current_customer->is_active ) ? selected( $current_customer->is_active, 1 ) : ''; ?>><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
                        <option value="0" <?php isset( $current_customer->is_active ) ? selected( $current_customer->is_active, 0 ) : ''; ?>><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
                    </select>
                    <div class="errortext"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_name"><?php esc_html_e( 'Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                <td class="bminput bm_required">
                    <input name="customer_name" type="text" id="customer_name" placeholder="<?php esc_html_e( 'enter name', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $current_customer->customer_name ) ? esc_html( $current_customer->customer_name ) : ''; ?>" autocomplete="off">
                    <div class="errortext"></div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="customer_email"><?php esc_html_e( 'Email', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                <td class="bminput bm_required">
                    <input name="customer_email" type="email" id="customer_email" placeholder="<?php esc_html_e( 'enter email', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $current_customer->customer_email ) ? esc_html( $current_customer->customer_email ) : ''; ?>" autocomplete="off">
                    <div class="errortext"></div>
                </td>
            </tr>
        </table>
        
        <div class="billing_details" style="display: flex;margin-bottom:20px;">
            <div class="greybox1 billing_details" id="billing_details">
                <h1 style="text-decoration :underline;"><?php esc_html_e( 'Billing Details', 'service-booking' ); ?></h1>
                <table class="form-table" role="presentation" >
                    <tr>
                        <td class="first_td">
                        
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing First Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="billing_details[billing_first_name]" type="text" id="billing_first_name" placeholder="<?php esc_html_e( 'billing first name', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_first_name'] ) ? esc_html( $billing_details['billing_first_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Last Name', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="billing_details[billing_last_name]" type="text" id="billing_last_name" placeholder="<?php esc_html_e( 'billing last name', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_last_name'] ) ? esc_html( $billing_details['billing_last_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Email', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="billing_details[billing_email]" type="email" id="billing_email" placeholder="<?php esc_html_e( 'billing email', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_email'] ) ? esc_html( $billing_details['billing_email'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Phone', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="billing_contact_field">
                            <input name="billing_details[billing_contact]" type="tel" id="billing_contact" placeholder="<?php esc_html_e( 'billing contact', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_contact'] ) ? esc_html( $billing_details['billing_contact'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="billing_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Company', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="billing_details[billing_company]" type="text" id="billing_company" placeholder="<?php esc_html_e( 'billing company', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_company'] ) ? esc_html( $billing_details['billing_company'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Address', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="billing_details[billing_address]" type="text" id="billing_address" placeholder="<?php esc_html_e( 'billing address', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_address'] ) ? esc_html( $billing_details['billing_address'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Country', 'service-booking' ); ?></label></th>
                        <td class="bminput bm_required">
                            <select id="billing_country" name="billing_details[billing_country]" class="regular-text">
                                <?php
                                if ( !empty( $countries ) ) {
                                    foreach ( $countries as $key => $country ) {
                                        ?>
                                        <option value="<?php echo esc_html( $key ); ?>" <?php isset( $billing_details ) && isset( $billing_details['billing_country'] ) ? selected( esc_html( $billing_details['billing_country'] ), esc_html( $key ) ) : ''; ?> <?php !isset( $billing_details ) && !empty( $country_globally_set )  ? selected( esc_html( $country_globally_set ), esc_html( $key ) ) : ''; ?>><?php echo esc_html( $country ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing State', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                            <select id="billing_state" name="billing_details[billing_state]" class="regular-text">
                            <?php
                            $country = $billing_details['billing_country'] ?? $country_globally_set;
                            $states  = $bmrequests->bm_get_states( $country );
							if ( !empty( $states ) ) {
								foreach ( $states as $key => $state ) {
									?>
                                        <option value="<?php echo esc_html( $state['name'] ); ?>" <?php !empty( $billing_details['billing_state'] ) ? selected( esc_html( $billing_details['billing_state'] ), esc_html( $state['name'] ) ) : ''; ?>><?php echo esc_html( $state['name'] ); ?></option>
                                        <?php
								}
							}
							?>
                            </select>
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing City', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="billing_details[billing_city]" type="text" id="billing_city" placeholder="<?php esc_html_e( 'billing city', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_city'] ) ? esc_html( $billing_details['billing_city'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Billing Postcode', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="billing_details[billing_postcode]" type="text" id="billing_postcode" placeholder="<?php esc_html_e( 'billing postcode', 'service-booking' ); ?>" value="<?php echo isset( $billing_details ) && isset( $billing_details['billing_postcode'] ) ? esc_html( $billing_details['billing_postcode'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="greybox2 shipping_details" id="shipping_details">
                <h1 class="shipping_detail_heading" style="text-decoration :underline;"><?php esc_html_e( 'Shipping Details', 'service-booking' ); ?>
                <span class="shipping_after_heading bm-checkbox-td">
                    <input name="shipping_same_as_billing" type="checkbox" id="shipping_same_as_billing" class="regular-text auto-checkbox bm_toggle" <?php isset( $current_customer->shipping_same_as_billing )  ? checked( $current_customer->shipping_same_as_billing, '1' ) : ''; ?>><label for="shipping_same_as_billing"></label>&nbsp;&nbsp;<h4 class="shipping_detail_subheading"><?php esc_html_e( 'Same as billing ?', 'service-booking' ); ?></h4>
                </span>
                </h1>
                <table class="form-table " role="presentation" style="margin-top:0px !important;">
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping First Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="shipping_details[shipping_first_name]" type="text" id="shipping_first_name" placeholder="<?php esc_html_e( 'shipping first name', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_first_name'] ) ? esc_html( $shipping_details['shipping_first_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Last Name', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="shipping_details[shipping_last_name]" type="text" id="shipping_last_name" placeholder="<?php esc_html_e( 'shipping last name', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_last_name'] ) ? esc_html( $shipping_details['shipping_last_name'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Email', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="shipping_details[shipping_email]" type="email" id="shipping_email" placeholder="<?php esc_html_e( 'shipping email', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_email'] ) ? esc_html( $shipping_details['shipping_email'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Phone', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="shipping_contact_field">
                            <input name="shipping_details[shipping_contact]" type="tel" id="shipping_contact" placeholder="<?php esc_html_e( 'shipping contact', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_contact'] ) ? esc_html( $shipping_details['shipping_contact'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="billing_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Company', 'service-booking' ); ?></label></th>
                        <td>
                            <input name="shipping_details[shipping_company]" type="text" id="shipping_company" placeholder="<?php esc_html_e( 'shipping company', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_company'] ) ? esc_html( $shipping_details['shipping_company'] ) : ''; ?>" class="regular-text" autocomplete="off">
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Address', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="shipping_details[shipping_address]" type="text" id="shipping_address" placeholder="<?php esc_html_e( 'shipping address', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_address'] ) ? esc_html( $shipping_details['shipping_address'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Country', 'service-booking' ); ?></label></th>
                        <td class="bminput bm_required">
                            <select id="shipping_country" name="shipping_details[shipping_country]" class="regular-text">
                                <?php
                                if ( !empty( $countries ) ) {
                                    foreach ( $countries as $key => $country ) {
                                        ?>
                                        <option value="<?php echo esc_html( $key ); ?>" <?php isset( $shipping_details ) && isset( $shipping_details['shipping_country'] ) ? selected( esc_html( $shipping_details['shipping_country'] ), $key ) : ''; ?> <?php !isset( $shipping_details ) && !empty( $country_globally_set )  ? selected( esc_html( $country_globally_set ), esc_html( $key ) ) : ''; ?>><?php echo esc_html( $country ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping State', 'service-booking' ); ?></label></th>
                        <td class="bminput">
                        <select id="shipping_state" name="shipping_details[shipping_state]" class="regular-text">
                            <?php
                            $country = $shipping_details['shipping_country'] ?? $country_globally_set;
                            $states  = $bmrequests->bm_get_states( $country );
							if ( !empty( $states ) ) {
								foreach ( $states as $key => $state ) {
									?>
                                        <option value="<?php echo esc_html( $state['name'] ); ?>" <?php !empty( $shipping_details['shipping_state'] ) ? selected( esc_html( $shipping_details['shipping_state'] ), esc_html( $state['name'] ) ) : ''; ?>><?php echo esc_html( $state['name'] ); ?></option>
                                        <?php
								}
							}
							?>
                            </select>
                            <div class="order_field_errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping City', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="shipping_details[shipping_city]" type="text" id="shipping_city" placeholder="<?php esc_html_e( 'shipping city', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_city'] ) ? esc_html( $shipping_details['shipping_city'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Shipping Postcode', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <input name="shipping_details[shipping_postcode]" type="text" id="shipping_postcode" placeholder="<?php esc_html_e( 'shipping postcode', 'service-booking' ); ?>" value="<?php echo isset( $shipping_details ) && isset( $shipping_details['shipping_postcode'] ) ? esc_html( $shipping_details['shipping_postcode'] ) : ''; ?>" class="regular-text" autocomplete="off">
                            <div class="errortext"></div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
            <table>
                <tr>
                    <td>
                        <div class="row">
                            <?php wp_nonce_field( 'save_customer_nonce' ); ?>
                            <input type="hidden" name="customer_id" value="<?php echo esc_attr( $customer_id ); ?>">
                            <a href="admin.php?page=bm_all_customers" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Cancel', 'service-booking' ); ?></a>
                            <button class="button button-primary"><?php empty( $id ) ? esc_attr_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?></button>
                        </div>
                    </td>
                </tr>
            </table>
        </table>
    </form>
</div>

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__swing" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>
