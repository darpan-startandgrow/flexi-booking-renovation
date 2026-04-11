<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$plugin_path = plugin_dir_url( __FILE__ );
?>

<div class="sg-admin-main-box">
    <div class="bm-setting-wrapper" style="float: none; margin: 0px; padding: 0px;">
    <div class="content bm_settings_option listing_table">
        <div>
            <h2 class="title" style="font-weight: bold;"> <?php esc_html_e( 'Global Settings', 'service-booking' ); ?></h2>
        </div>

        <div class="bm-setting-wrap">
            <div class="settings_row">
                <a href="admin.php?page=bm_global_general_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Service Shortcode Content Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Grid/List view, Service contents in the frontend etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_global_email_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/mail.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Email/Notifications', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Admin mail/SMTP settings etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>
            
            <div class="settings_row">
                <a href="admin.php?page=bm_global_payment_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/payment.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Payment Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Currency, Symbol Position etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_svc_booking_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/stopsales.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Service/booking Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Stopsales, Saleswicth, book on request expiry time etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_global_css_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Service Shortcode CSS Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Font and colour settings etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_global_timezone_country_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Time Zone and Country Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'time zone and country settings etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_pagination_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/pagination.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Pagination Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Order, Service, Category pagination etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_upload_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/upload.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Image Upload Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'image width, height, size, quality etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_global_language_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Language Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'language preference, switcher visibility etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_global_format_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Format Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'time, price format etc.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>

            <div class="settings_row">
                <a href="admin.php?page=bm_global_integration_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Integration Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( '3rd party and Service level integrations.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>
            <div class="settings_row">
                <a href="admin.php?page=bm_global_coupon_settings">
                    <div class="bm_setting_image">
                        <img src="<?php echo esc_url( $plugin_path . 'images/general.png' ); ?>" class="options" alt="options">
                    </div>
                    <div class="bm-setting-heading">
                        <span class="bm-setting-icon-title"><?php esc_html_e( 'Coupon Settings', 'service-booking' ); ?></span>
                        <span class="bm-setting-description"><?php esc_html_e( 'Coupon related global settings.', 'service-booking' ); ?></span>
                    </div>
                </a>
            </div>
            <?php do_action('flexi_booking_global_setting_list');?>
        </div>
    </div>
</div>

<div class="loader_modal"></div>
</div>

