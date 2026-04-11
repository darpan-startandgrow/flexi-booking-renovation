<?php
$customer = $customer && isset( $customer[0] ) ? $customer[0] : null;

if ( empty( $customer ) || is_null( $customer ) ) {
    return;
}

$billing_data = $customer->billing_details ? maybe_unserialize( $customer->billing_details ) : array();

if ( empty( $billing_data ) ) {
    return;
}

$orders = !empty( $customer->order_data ) ? array_filter(
    array_map(
        function ( $data ) {
            $order_parts = explode( '|', $data );
            return count( $order_parts ) === 10
                ? array_combine( array( 'id', 'service', 'created', 'date', 'slot', 'cost', 'status', 'total_revenue', 'total_slots_booked', 'total_extra_slots_booked' ), $order_parts )
                : array();
        },
        explode( ',', $customer->order_data )
    )
) : array();

$total_revenue            = !empty( $orders ) ? array_sum( array_column( $orders, 'total_revenue' ) ) : 0;
$total_slots_booked       = !empty( $orders ) ? array_sum( array_column( $orders, 'total_slots_booked' ) ) : 0;
$total_extra_slots_booked = !empty( $orders ) ? array_sum( array_column( $orders, 'total_extra_slots_booked' ) ) : 0;

$products = !empty( $customer->service_product_data ) ? array_filter(
    array_map(
        function ( $data ) {
            $parts = explode( '|', $data );
            return count( $parts ) === 4
                ? array_combine( array( 'name', 'quantities', 'revenue', 'unique_product' ), $parts )
                : array();
        },
        explode( ',', $customer->service_product_data )
    )
) : array();

$unique_service = isset( $products[0]['unique_product'] ) ? $products[0]['unique_product'] : 0;

$extra_products = !empty( $customer->extra_product_data ) ? array_filter(
    array_map(
        function ( $data ) {
            $parts = explode( '|', $data );
            return count( $parts ) === 4
                ? array_combine( array( 'name', 'quantities', 'revenue', 'unique_product' ), $parts )
                : array();
        },
        explode( ',', $customer->extra_product_data )
    )
) : array();

$unique_extra_service = isset( $extra_products[0]['unique_product'] ) ? $extra_products[0]['unique_product'] : 0;

if ( !empty( $products ) && !empty( $extra_products ) ) {
    $products = array_merge( $products, $extra_products );
}

$transactions = !empty( $customer->transaction_data ) ? array_filter(
    array_map(
        function ( $data ) {
            $transaction_parts = explode( '|', $data );
            return count( $transaction_parts ) === 4
                ? array_combine( array( 'id', 'amount', 'method', 'status' ), $transaction_parts )
                : array();
        },
        explode( ',', $customer->transaction_data )
    )
) : array();

$emails = !empty( $customer->email_data ) ? array_filter(
    array_map(
        function ( $data ) {
            $email_parts = explode( '|', $data );
            return count( $email_parts ) === 4
                ? array_combine( array( 'id', 'sub', 'lang', 'created_at' ), $email_parts )
                : null;
        },
        explode( ',', $customer->email_data )
    )
) : array();

$failed_transactions = !empty( $customer->failed_transaction_data ) ? array_filter(
    array_map(
        function ( $data ) {
            $failed_parts = explode( '|', $data );
            return count( $failed_parts ) === 4
                ? array_combine( array( 'id', 'amount', 'currency', 'refund_status' ), $failed_parts )
                : array();
        },
        explode( ',', $customer->failed_transaction_data )
    )
) : array();

$bmrequests = new BM_Request();
$dbhandler  = new BM_DBhandler();
$countries  = $bmrequests->bm_get_countries();

?>

<div class="flexibooking-ProfilePage bgcolor">
    <div class="mainpage">
        <div class="Profilebar commonleftbar">
            <div class="profile-section">
                <img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'images/profile.jpg' ); ?>" alt="Profile Picture" class="profile-picture">
                <div class="profile-details">
                    <h2><?php echo esc_html( $customer->customer_name ) ?? ''; ?><span class="badge lead"><?php echo isset( $customer->is_active ) && $customer->is_active == 1 ? esc_html__( 'Active', 'service-booking' ) : esc_html__( 'Inactive', 'service-booking' ); ?></span> </h2>
                    <p class="added-info"><?php echo esc_html( $customer->customer_email ) ?? ''; ?><br/><?php echo isset( $customer->customer_created_at ) ? __( 'Added ', 'service-booking' ) . esc_html( $bmrequests->bm_fetch_created_at_in_string( $customer->customer_created_at ) ) : ''; ?></p>
                    <div class="icons">
                        <span class="icon" title="<?php echo esc_html__( 'Orders', 'service-booking' ); ?>"><i class="fa fa-shopping-cart"></i><?php echo esc_html( $customer->total_orders ) ?? 0; ?></span>
                        <span class="icon" title="<?php echo esc_html__( 'Mails', 'service-booking' ); ?>"><i class="fa fa-envelope-o"></i><?php echo esc_html( $customer->total_emails ) ?? 0; ?></span>
                        <span class="icon" title="<?php echo esc_html__( 'Revenue', 'service-booking' ); ?>"><i class="fa fa-money"></i><?php echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $total_revenue, true, $customer->paid_amount_currency ) ); ?></span>
                        <span class="icon" title="<?php echo esc_html__( 'Products', 'service-booking' ); ?>"><i class="fa fa-product-hunt"></i><?php echo esc_html( $unique_service ); ?></span>
                        <span class="icon" title="<?php echo esc_html__( 'Extra Products', 'service-booking' ); ?>"><i class="fa fa-edge"></i><?php echo esc_html( $unique_extra_service ); ?></span>
                        <span class="icon" title="<?php echo esc_html__( 'Slots', 'service-booking' ); ?>"><i class="fa fa-calendar-check-o"></i><?php echo esc_html( $total_slots_booked ); ?></span>
                        <span class="icon" title="<?php echo esc_html__( 'Extra slots', 'service-booking' ); ?>"><i class="fa fa-calendar"></i><?php echo esc_html( $total_extra_slots_booked ); ?></span>
                    </div>
                </div>
            </div>
            <div class="goBackButton">
                <a href="admin.php?page=bm_all_customers">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
            </div>
        </div>
    </div>
    <div class="mainpage">
        <div class="contactBox commonleftbar bgcolor">
            <div class="contactPage tab-wrap">
                <ul class="tab-head">
                    <li class="tablink tab-active" data-target="cust_personal_data" onclick="openCustomerProfileTab(event, 'cust_personal_data')"><?php echo esc_html__( 'Personal Information', 'service-booking' ); ?></li>
                    <li class="tablink" data-target="cust_orders" onclick="openCustomerProfileTab(event, 'cust_orders')"><?php echo esc_html__( 'Order History', 'service-booking' ); ?></li>
                    <li class="tablink" data-target="cust_transactions" onclick="openCustomerProfileTab(event, 'cust_transactions')"><?php echo esc_html__( 'Transactions', 'service-booking' ); ?></li>
                    <li class="tablink" data-target="cust_mails" onclick="openCustomerProfileTab(event, 'cust_mails')"><?php echo esc_html__( 'Emails', 'service-booking' ); ?></li>
                    <li class="tablink" data-target="cust_products" onclick="openCustomerProfileTab(event, 'cust_products')"><?php echo esc_html__( 'Ordered Products', 'service-booking' ); ?></li>
                    <li class="tablink" data-target="cust_failed_transactions" onclick="openCustomerProfileTab(event, 'cust_failed_transactions')"><?php echo esc_html__( 'Failed Transactions', 'service-booking' ); ?></li>
                </ul>

                <div class="tab-main">
                    <div id="cust_personal_data" class="tabcontent active cust_personal_data_for_all_inputs">
                        <div class="form-container">
                            <div class="form-section">
                                <h3><?php echo esc_html__( 'Basic Information', 'service-booking' ); ?></h3>
                                <div class="form-row-inline">
                                    <div class="form-row">
                                        <label><?php echo esc_html__( 'Prefix', 'service-booking' ); ?></label>
                                        <select name="billing_details[name_prefix]"  class="prefixfield">
                                            <option value="mr"><?php echo esc_html__( 'Mr', 'service-booking' ); ?></option>
                                            <option value="ms"><?php echo esc_html__( 'Ms', 'service-booking' ); ?></option>
                                            <option value="mrs"><?php echo esc_html__( 'Mrs', 'service-booking' ); ?></option>
                                        </select>
                                    </div>
                                    <div class="form-row form-row-section">
                                        <label><?php echo esc_html__( 'First Name', 'service-booking' ); ?></label>
                                        <input type="text" name="billing_details[billing_first_name]" class="billing_first_name-input"  id="billing_first_name" placeholder="<?php echo esc_html__( 'First Name', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_first_name'] ) ?? ''; ?>">
                                    </div>
                                    <div class="form-row form-row-section">
                                        <label><?php echo esc_html__( 'Last Name', 'service-booking' ); ?></label>
                                        <input type="text" name="billing_details[billing_last_name]" class="billing_first_name-input" id="billing_last_name" placeholder="<?php echo esc_html__( 'Last Name', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_last_name'] ) ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <label><?php echo esc_html__( 'Email Address', 'service-booking' ); ?></label>
                                    <input type="email" name="billing_details[billing_email]" id="billing_email" placeholder="<?php echo esc_html__( 'Email Address', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_email'] ) ?? ''; ?>">
                                </div>
                                <div class="form-row">
                                    <label><?php echo esc_html__( 'Mobile', 'service-booking' ); ?></label>
                                    <input type="tel" name="billing_details[billing_contact]" id="billing_contact" placeholder="<?php echo esc_html__( 'Phone Number', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_contact'] ) ?? ''; ?>">
                                </div>
                                <!-- <div class="form-row">
                                    <label><?php echo esc_html__( 'Date Of Birth', 'service-booking' ); ?></label>
                                    <div class="form-row-inline">
                                        <input type="date" name="date_of_birth" id="date_of_birth" value="<?php echo isset( $customer->date_of_birth ) ? esc_html( $customer->date_of_birth ) : ''; ?>">
                                    </div>
                                </div> -->
                            </div>

                            <div class="form-section">
                                <h3><?php echo esc_html__( 'Address Information', 'service-booking' ); ?></h3>
                                <div class="form-row">
                                    <label><?php echo esc_html__( 'Address Line', 'service-booking' ); ?></label>
                                    <input type="text" name="billing_details[billing_address]" id="billing_address" placeholder="<?php echo esc_html__( 'Address Line', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_address'] ) ?? ''; ?>">
                                </div>
                                <div class="form-row-inline">
                                    <div class="form-row form-row-section">
                                        <label><?php echo esc_html__( 'City', 'service-booking' ); ?></label>
                                        <input type="text" name="billing_details[billing_city]" id="billing_city" placeholder="<?php echo esc_html__( 'City', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_city'] ) ?? ''; ?>">
                                    </div>
                                    <div class="form-row form-row-section">
                                        <label><?php echo esc_html__( 'State', 'service-booking' ); ?></label>
                                        <input type="text" name="billing_details[billing_state]" id="billing_state" placeholder="<?php echo esc_html__( 'State', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_state'] ) ?? ''; ?>">
                                    </div>
                                </div>
                                <div class="form-row-inline">
                                    <div class="form-row form-row-section">
                                        <label><?php echo esc_html__( 'ZIP Code', 'service-booking' ); ?></label>
                                        <input type="text" name="billing_details[billing_postcode]" id="billing_postcode" placeholder="<?php echo esc_html__( 'ZIP Code', 'service-booking' ); ?>" value="<?php echo esc_html( $billing_data['billing_postcode'] ) ?? ''; ?>">
                                    </div>
                                    <div class="form-row form-row-section">
                                        <label><?php echo esc_html__( 'Country', 'service-booking' ); ?></label>
                                        <select id="billing_country" name="billing_details[billing_country]" style="width:302px;">
                                            <?php
                                            if ( !empty( $countries ) ) {
                                                foreach ( $countries as $key => $country ) {
                                                    ?>
                                                    <option value="<?php echo esc_html( $key ); ?>" <?php isset( $billing_details['billing_country'] ) ? selected( esc_html( $billing_details['billing_country'] ), esc_html( $key ) ) : ''; ?> <?php !isset( $billing_details ) && !empty( $dbhandler->get_global_option_value( 'bm_booking_country' ) )  ? selected( esc_html( $dbhandler->get_global_option_value( 'bm_booking_country' ) ), esc_html( $key ) ) : ''; ?>><?php echo esc_html( $country ); ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="updatebuttonbar">
                            <div class="update-button">
                            <?php echo esc_html__( 'Update', 'service-booking' ); ?>
                            </div>
                        </div> -->
                        
                    </div>
                    <div id="cust_orders" class="sg-admin-main-box tabcontent <?php echo empty( $orders ) ? 'empty_table' : ''; ?>">
                        <?php
                            // Order History
						if ( !empty( $orders ) ) {
							?>
                            <table class="wp-list-table widefat striped customer_tables" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th><?php echo esc_html__( 'Serial', 'service-booking' ); ?></th>
                                        <th><?php echo esc_html__( 'Service', 'service-booking' ); ?></th>
                                        <th><?php echo esc_html__( 'Booked', 'service-booking' ); ?></th>
                                        <th><?php echo esc_html__( 'Service Date', 'service-booking' ); ?></th>
                                        <th><?php echo esc_html__( 'Time slot', 'service-booking' ); ?></th>
                                        <th><?php echo esc_html__( 'Cost', 'service-booking' ); ?></th>
                                        <th><?php echo esc_html__( 'Order status', 'service-booking' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
								<?php
								foreach ( $orders as $index => $cust_order ) :
									$slot_data = maybe_unserialize( $cust_order['slot'] );
									$slot      = $slot_data['from'] . '-' . $slot_data['to'];
									?>
                                        <tr>
                                            <td><?php echo esc_html( $index + 1 ); ?></td>
                                            <td><?php echo esc_html( $cust_order['service'] ?? '' ); ?></td>
                                            <td><?php echo esc_html( $bmrequests->bm_fetch_created_at_in_string( $cust_order['created'] ) ?? '' ); ?></td>
                                            <td><?php echo esc_html( $cust_order['date'] ?? '' ); ?></td>
                                            <td><?php echo esc_html( $slot ); ?></td>
                                            <td><?php echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $cust_order['cost'], true, $customer->paid_amount_currency ) ?? '' ); ?></td>
                                            <td><?php echo esc_html( $cust_order['status'] ?? '' ); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                        </table>
                        <?php } else { ?>
                                <div class="no_records"><?php echo esc_html__( 'No orders found.', 'service-booking' ); ?></div>
                            <?php } ?>
                    </div>
                    <div id="cust_transactions" class="sg-admin-main-box tabcontent <?php echo empty( $transactions ) ? 'empty_table' : ''; ?>">
                        <?php
                            // Transactions
						if ( !empty( $transactions ) ) {
							?>
                        <table class="wp-list-table widefat striped customer_tables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__( 'Serial', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Amount', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Payment Method', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Payment Status', 'service-booking' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $transactions as $index => $transaction ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $index + 1 ); ?></td>
                                        <td><?php echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $transaction['amount'], true, $customer->paid_amount_currency ) ?? '' ); ?></td>
                                        <td><?php echo isset( $transaction['method'] ) && !empty( $transaction['method'] ) ? esc_html( $transaction['method'] ) : 'card'; ?></td>
                                        <td><?php echo esc_html( $transaction['status'] ?? '' ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                            <div class="no_records"><?php echo esc_html__( 'No transactions found.', 'service-booking' ); ?></div>
                        <?php } ?>
                    </div>
                    <div id="cust_mails" class="sg-admin-main-box tabcontent <?php echo empty( $emails ) ? 'empty_table' : ''; ?>">
                        <?php
                            // Emails
						if ( !empty( $emails ) ) {
							?>
                        <table class="wp-list-table widefat striped customer_tables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__( 'Serial', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Mail Sub', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Mail Body', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Mail Language', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Sent', 'service-booking' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $emails as $index => $email ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $index + 1 ); ?></td>
                                        <td tilte="<?php echo esc_html( $email['sub'] ?? '' ); ?>"><?php echo esc_html( esc_html( mb_strimwidth( $email['sub'] ?? '', 0, 40, '...' ) ) ); ?></td>
                                        <td><i class="fa fa-envelope" aria-hidden="true" style="cursor:pointer;font-size:16px;" id="<?php echo esc_attr( $email['id'] ?? 0 ); ?>" onclick="bm_show_email_body(this)"></i></td>
                                        <td><?php echo $email['lang'] === 'it' ? esc_html__( 'Italian', 'service-booking' ) : esc_html__( 'English', 'service-booking' ); ?></td>
                                        <td><?php echo isset( $email['created_at'] ) ? esc_html( $bmrequests->bm_fetch_created_at_in_string( $email['created_at'] ) ) : ''; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                            <div class="no_records"><?php echo esc_html__( 'No emails found.', 'service-booking' ); ?></div>
                        <?php } ?>
                    </div>
                    <div id="cust_products" class="sg-admin-main-box tabcontent <?php echo empty( $products ) ? 'empty_table' : ''; ?>">
                        <?php
                            // Emails
						if ( !empty( $products ) ) {
							?>
                        <table class="wp-list-table widefat striped customer_tables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__( 'Serial', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Product', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Total Quantity', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Revenue', 'service-booking' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $products as $index => $product ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $index + 1 ); ?></td>
                                        <td><?php echo esc_html( $product['name'] ?? '' ); ?></td>
                                        <td><?php echo esc_html( $product['quantities'] ?? '' ); ?></td>
                                        <td><?php echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $product['revenue'], true, $customer->paid_amount_currency ) ?? '' ); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                            <div class="no_records"><?php echo esc_html__( 'No Booked products found.', 'service-booking' ); ?></div>
                        <?php } ?>
                    </div>
                    <div id="cust_failed_transactions" class="sg-admin-main-box tabcontent <?php echo empty( $failed_transactions ) ? 'empty_table' : ''; ?>">
                        <?php
                            // Failed Transactions
						if ( !empty( $failed_transactions ) ) {
							?>
                        <table class="wp-list-table widefat striped customer_tables" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><?php echo esc_html__( 'Serial', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Amount', 'service-booking' ); ?></th>
                                    <th><?php echo esc_html__( 'Refund Status', 'service-booking' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ( $failed_transactions as $index => $failed_transaction ) : ?>
                                    <tr>
                                        <td><?php echo esc_html( $index + 1 ); ?></td>
                                        <td><?php echo esc_html( $bmrequests->bm_fetch_price_in_global_settings_format( $failed_transaction['amount'], true, $failed_transaction['currency'] ) ?? '' ); ?></td>
                                        <td>
                                        <?php
                                            $refund_status = esc_html__( 'Could Not Fetch', 'service-booking' );
                                        if ( isset( $failed_transaction['refund_status'] ) ) {
                                            if ( $failed_transaction['refund_status'] === 'succeeded' ) {
                                                $refund_status = esc_html__( 'Refunded', 'service-booking' );
                                            } elseif ( $failed_transaction['refund_status'] === 'failed' ) {
                                                $refund_status = esc_html__( 'Failed', 'service-booking' );
                                            } elseif ( $failed_transaction['refund_status'] === 'not_required' ) {
                                                $refund_status = esc_html__( 'Not Required', 'service-booking' );
                                            }
                                        }
                                            echo esc_html( $refund_status );
                                        ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                            <div class="no_records"><?php echo esc_html__( 'No failed transactions found.', 'service-booking' ); ?></div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="email_body_modal" class="modaloverlay">
    <div class="modal animate__animated animate__swing">
        <span class="close" onclick="closeModal('email_body_modal')">&times;</span>
        <h4 style="background:#5EA8ED ; margin:0px; padding:12px;color:#fff;font-size:16px;"><?php esc_html_e( 'Sent mail body', 'service-booking' ); ?></h4>
        <div class="modalcontentbox2 modal-body" id="email_body"></div>
        <div class="loader_modal"></div>
    </div>
</div>

<div class="loader_modal"></div>
