<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier  = 'GLOBAL';
$plugin_path = plugin_dir_url( __FILE__ );
$dbhandler   = new BM_DBhandler();
$bmrequests  = new BM_Request();
$language    = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

if ( filter_input( INPUT_POST, 'save_email_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {

    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_global_email_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array( '_wpnonce', '_wp_http_referer', 'save_email_global', 'resetdata' );

    if ( filter_input( INPUT_POST, 'save_email_global' ) ) {
        $global_mail_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $global_mail_post != false ) {
            $global_mail_post['bm_shop_admin_notification']                    = isset( $global_mail_post['bm_shop_admin_notification'] ) ? 1 : 0;
            $global_mail_post['bm_enable_smtp']                                = isset( $global_mail_post['bm_enable_smtp'] ) ? 1 : 0;
            $global_mail_post['bm_attach_customer_data_with_admin_email_body'] = isset( $global_mail_post['bm_attach_customer_data_with_admin_email_body'] ) ? 1 : 0;

            if ( !isset( $global_mail_post['bm_shop_admin_email'] ) ) {
                $global_mail_post['bm_shop_admin_email'] = array( '' );
            }

            foreach ( $global_mail_post as $key => $value ) {
                $dbhandler->update_global_option_value( $key, $value );
            }

            echo ( '<div id="successMessage" class="bm-notice">' );
            echo esc_html__( 'Data Saved Sucessfully.', 'service-booking' );
            echo ( '</div>' );
        }
    }

    if ( filter_input( INPUT_POST, 'resetdata' ) ) {
        foreach ( $_POST as $key => $value ) {
            delete_option( $key );
        }
        echo ( '<div id="successMessage" class="bm-notice">' );
        echo esc_html__( 'Data Cleared Sucessfully.', 'service-booking' );
        echo ( '</div>' );
    }
}

$admin_emails = maybe_unserialize( $dbhandler->get_global_option_value( 'bm_shop_admin_email' ) );
if ( !is_array( $admin_emails ) ) {
	$admin_emails = array( '' );
}

$templates = $dbhandler->get_all_result( 'EMAIL_TMPL', '*', 1, 'results' );

?>

<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Mail Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_email_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'Send Notification To Shop Admin', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 40%;">
                        <input name="bm_shop_admin_notification" type="checkbox" id="bm_shop_admin_notification" class="regular-text bm_toggle" onClick="bm_toggle_tab(this,'enable_admin_notification_html')" <?php checked( $dbhandler->get_global_option_value( 'bm_shop_admin_notification' ), '1' ); ?>>
                        <label for="bm_shop_admin_notification"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'If checked, the site administrator will be notified for each new order.', 'service-booking' ); ?> 
                    </td>
                </tr>
                <table class="form-table" role="presentation" id="enable_admin_notification_html" 
                <?php
                if ( $dbhandler->get_global_option_value( 'bm_shop_admin_notification', 0 ) == 1 ) {
					echo "style='display: block;'";}
				?>
                >
                    <tr>
                        <th scope="row" class="first_option">
                            <label for="front_view_type"><?php esc_html_e( 'Extra Recipients (optional)', 'service-booking' ); ?></label>
                           
                        </th>
                        <?php
                        if ( !empty( $admin_emails ) ) {
							foreach ( $admin_emails as $email_option ) {
								?>
                            <td class="bm_email_option_field">
                                <div class="bm_email_option">
                                    <input name="bm_shop_admin_email[]" type="text" class="regular-text" value="<?php echo !empty( $email_option ) ? esc_attr( $email_option ) : ''; ?>">
                                    &nbsp;&nbsp;<span class="bm_remove_shop_admin_email_field" onClick="bm_remove_shop_admin_email(this)"><?php esc_html_e( 'Delete', 'service-booking' ); ?></span>&nbsp;
                                </div>
                            </td>
								<?php
                            }
						}
						?>
                        <td class="add_admin_email_option_class">
                            <a href="javascript:void(0)" onClick="bm_add_admin_email_option()"><?php esc_html_e( 'Click to add option', 'service-booking' ); ?></a>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'If you want to notify other people apart from the admin about the order, enter each one\'s email address individually.', 'service-booking' ); ?>
                        </td>
                        
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_new_order_admin_template"><?php esc_html_e( 'New Order Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_new_order_admin_template" id="bm_new_order_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_new_order_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for new order mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_new_request_admin_template"><?php esc_html_e( 'New Request Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_new_request_admin_template" id="bm_new_request_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_new_request_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for new request mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_refund_order_admin_template"><?php esc_html_e( 'Refund Order Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_refund_order_admin_template" id="bm_refund_order_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_refund_order_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for refund order mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_cancel_order_admin_template"><?php esc_html_e( 'Cancel Order Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_cancel_order_admin_template" id="bm_cancel_order_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_cancel_order_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for cancel order mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_approved_order_admin_template"><?php esc_html_e( 'Approved Order Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_approved_order_admin_template" id="bm_approved_order_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_approved_order_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for order approval mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_failed_order_admin_template"><?php esc_html_e( 'Failed Order Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_failed_order_admin_template" id="bm_failed_order_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_failed_order_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for failed order mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_voucher_redeem_admin_template"><?php esc_html_e( 'Voucher Redeem Template', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_voucher_redeem_admin_template" id="bm_voucher_redeem_admin_template" class="regular-text">
                                <?php
                                if ( !empty( $templates ) ) {
                                    foreach ( $templates as $template ) {
                                        $tmpl_name = "tmpl_name_$language";
                                        ?>
                                        <option value="<?php echo esc_attr( $template->id ); ?>" <?php selected( $dbhandler->get_global_option_value( 'bm_voucher_redeem_admin_template' ), esc_attr( $template->id ) ); ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
                                        <?php
                                    }
                                }
                                ?>
                            </select>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Email template for voucher redeem mail notification to admin.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Include Customer Data', 'service-booking' ); ?>
                           
                        </th>
                        <td class="bm-checkbox-td">
                            <input name="bm_attach_customer_data_with_admin_email_body" type="checkbox" id="bm_attach_customer_data_with_admin_email_body" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_attach_customer_data_with_admin_email_body' ), '1' ); ?>>
                            <label for="bm_attach_customer_data_with_admin_email_body"></label> 
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'If checked, customer details entered in booking form will be sent to the admin as a pdf attachement along with notification mail.', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="no_smtp_html" <?php echo $dbhandler->get_global_option_value( 'bm_enable_smtp', 0 ) == 0 ? "style='display: block;'" : "style='display: none;'"; ?>>
                    <tr>
                        <th scope="row">
                            <label for="bm_from_email_name"><?php esc_html_e( 'From Email Name', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <input name="bm_from_email_name" type="text" id="bm_from_email_name" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_from_email_name' ) ); ?>">
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'The \'Sender\'s Name\' inside the header of email which is sent to the admin and the members.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_from_email_address"><?php esc_html_e( 'From Email Address', 'service-booking' ); ?></label>
                           
                        </th>
                        <td style="vertical-align: top;">
                            <input name="bm_from_email_address" type="text" id="bm_from_email_address" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_from_email_address' ) ); ?>">
                        </td>
                         <td style="vertical-align: top;">
                            <?php esc_html_e( 'The \'Reply-to email\' address inside the email which is sent to the admin and the members. It is a good idea to use an email address different from the admin\'s email address to avoid being trapped by spam filters. Also users, may directly reply to notifications emails - therefore you can either user an actively monitored email or specifically mention in your email templates that any replies to automated emails will be ignored.', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">
                            <?php esc_html_e( 'Enable SMTP', 'service-booking' ); ?>
                        </th>
                        <td class="bm-checkbox-td" style="width: 40%;">
                            <input name="bm_enable_smtp" type="checkbox" id="bm_enable_smtp" class="regular-text bm_toggle" onClick="bm_toggle_tab(this,'smtp_settings_html')" <?php checked( $dbhandler->get_global_option_value( 'bm_enable_smtp' ), '1' ); ?>>
                            <label for="bm_enable_smtp"></label>
                        </td>
                        <td style="vertical-align: top;">
                            <?php esc_html_e( 'Route emails from a dedicated email services instead of using your server\'s mail functionality. Allows a lot more control and better chances to avoid overzealous spam filters.', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
                <table class="form-table" role="presentation" id="smtp_settings_html" 
                <?php
                if ( $dbhandler->get_global_option_value( 'bm_enable_smtp', 0 ) == 1 ) {
					echo "style='display: block;'";}
				?>
                >
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_host"><?php esc_html_e( 'SMTP Host', 'service-booking' ); ?></label>
                        </th>
                        <td><input name="bm_smtp_host" type="text" id="bm_smtp_host" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_host' ) ); ?>"></td>
                        <td>
                            <?php esc_html_e( 'Host Server name. For e.g. smtp.gmail.com if you wish to use Gmail. Consult your SMTP service provider for exact name.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_encription"><?php esc_html_e( 'Type of Encryption', 'service-booking' ); ?></label>
                        </th>
                        <td style="width: 40%;">
                            <select name="bm_smtp_encription" id="bm_smtp_encription" class="regular-text">
                                <option value="false" <?php selected( $dbhandler->get_global_option_value( 'bm_smtp_encription' ), 'false' ); ?>><?php esc_html_e( 'None', 'service-booking' ); ?></option>
                                <option value="tls" <?php selected( $dbhandler->get_global_option_value( 'bm_smtp_encription' ), 'tls' ); ?>><?php esc_html_e( 'TLS', 'service-booking' ); ?></option>
                                <option value="ssl" <?php selected( $dbhandler->get_global_option_value( 'bm_smtp_encription' ), 'ssl' ); ?>><?php esc_html_e( 'SSL', 'service-booking' ); ?></option>
                            </select>
                        </td>
                         <td>
                            <?php esc_html_e( 'Encryption supported by your SMTP provider.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_port"><?php esc_html_e( 'SMTP Port', 'service-booking' ); ?></label>
                           
                        </th>
                        <td><input name="bm_smtp_port" type="text" id="bm_smtp_port" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_port' ) ); ?>"></td>
                        <td>
                            <?php esc_html_e( 'SMTP port. Usually a number. For e.g. 465.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_authentication"><?php esc_html_e( 'SMTP Authentication', 'service-booking' ); ?></label>
                        </th>
                        <td>
                            <select name="bm_smtp_authentication" id="bm_smtp_authentication" class="regular-text">
                                <option value="true" <?php selected( $dbhandler->get_global_option_value( 'bm_smtp_authentication' ), 'true' ); ?>><?php esc_html_e( 'Yes', 'service-booking' ); ?></option>
                                <option value="false" <?php selected( $dbhandler->get_global_option_value( 'bm_smtp_authentication' ), 'false' ); ?>><?php esc_html_e( 'No', 'service-booking' ); ?></option>
                            </select>
                        </td>
                         <td>
                            <?php esc_html_e( 'Authentication supported by your SMTP service provider.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_username"><?php esc_html_e( 'SMTP Username', 'service-booking' ); ?></label>
                        </th>
                        <td><input name="bm_smtp_username" type="text" id="bm_smtp_username" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_username' ) ); ?>"></td>
                        <td>
                            <?php esc_html_e( 'Your SMTP Username.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_password"><?php esc_html_e( 'SMTP Password', 'service-booking' ); ?></label>
                        </th>
                        <td><input name="bm_smtp_password" type="password" id="bm_smtp_password" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_password' ) ); ?>"></td>
                        <td>
                            <?php esc_html_e( 'Your SMTP Password.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_from_email_name"><?php esc_html_e( 'From Email Name.', 'service-booking' ); ?></label>
                        </th>
                        <td><input name="bm_smtp_from_email_name" type="text" id="bm_smtp_from_email_name" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_from_email_name' ) ); ?>"></td>
                        <td>
                            <?php esc_html_e( 'From Email Name.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_from_email_address"><?php esc_html_e( 'From Email Address', 'service-booking' ); ?></label>
                        </th>
                        <td><input name="bm_smtp_from_email_address" type="text" id="bm_smtp_from_email_address" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_from_email_address' ) ); ?>"></td>
                         <td>
                            <?php esc_html_e( 'Your SMTP Email Address.', 'service-booking' ); ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="bm_smtp_test_email_address"><?php esc_html_e( 'Test Outgoing Connection', 'service-booking' ); ?></label>
                           
                        </th>
                        <td id="smtptestconn">
                            <input name="bm_smtp_test_email_address" type="text" id="bm_smtp_test_email_address" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_smtp_test_email_address' ) ); ?>">
                            <a class="cancel_button" onclick="bm_test_smtp_connection()"><?php esc_html_e( 'Test', 'service-booking' ); ?></a>
                            <span id="bm_smtp_result"></span>
                            <img class="smtp_check_loader" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>" style="display:none;">
                        </td>
                        <td>
                            <?php esc_html_e( 'For Testing Purpose Only. Once you have filled in all required SMTP details, you can enter an email address here, click \'TEST\' button and check if the email is sent successfully.', 'service-booking' ); ?>
                        </td>
                    </tr>
                </table>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_global_email_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_email_global" id="save_email_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>

