<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();
$languages  = $dbhandler->get_global_option_value( 'bm_flexibooking_languages', array() );

if ( filter_input( INPUT_POST, 'save_language_settings' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'language_settings_nonce' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_language_settings',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_language_settings' ) ) {
        $language_settings_data = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $language_settings_data != false ) {
            $language_settings_data['bm_show_lng_swtchr_in_admin_bar'] = isset( $language_settings_data['bm_show_lng_swtchr_in_admin_bar'] ) ? 1 : 0;
            $language_settings_data['bm_show_lng_swtchr_in_footer']    = isset( $language_settings_data['bm_show_lng_swtchr_in_footer'] ) ? 1 : 0;

            $current_locale = $language_settings_data['bm_flexi_current_language'] == 'it' ? 'it_IT' : 'en_US';

            foreach ( $language_settings_data as $key => $value ) {
                $dbhandler->update_global_option_value( $key, $value );
            }

            $dbhandler->update_global_option_value( 'bm_flexi_current_locale', $current_locale );
            switch_to_locale( $current_locale );
            update_option( 'WPLANG', $current_locale == 'en_US' ? '' : $current_locale );
        }

        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_global_language_settings' ) );
    }

    if ( filter_input( INPUT_POST, 'resetdata' ) ) {
        foreach ( $_POST as $key => $value ) {
            delete_option( $key );
        }

        switch_to_locale( 'en_US' );
        update_option( 'WPLANG', '' );
        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_global_language_settings' ) );
    }
}

$current_language = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

?>
<div class="sg-admin-main-box" id="language_settings_form">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Language Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_language_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'Show language switcher in admin bar ?', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 31.5%;">
                        <input name="bm_show_lng_swtchr_in_admin_bar" type="checkbox" id="bm_show_lng_swtchr_in_admin_bar" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_lng_swtchr_in_admin_bar' ), '1' ); ?>>
                        <label for="bm_show_lng_swtchr_in_admin_bar"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'Check if you want to show language switcher in admin bar.', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <?php esc_html_e( 'Show language switcher in footer ?', 'service-booking' ); ?>
                    </th>
                    <td class="bm-checkbox-td" style="width: 31.5%;">
                        <input name="bm_show_lng_swtchr_in_footer" type="checkbox" id="bm_show_lng_swtchr_in_footer" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_lng_swtchr_in_footer' ), '1' ); ?>>
                        <label for="bm_show_lng_swtchr_in_footer"></label>
                    </td>
                    <td>
                        <?php esc_html_e( 'Check if you want to show language switcher in footer.', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_flexi_current_language"><?php esc_html_e( 'Choose plugin language', 'service-booking' ); ?></label></th>
                    <td style="width: 31.5%">
                        <select id="bm_flexi_current_language" name="bm_flexi_current_language" class="regular-text">
                            <?php
                            foreach ( $languages as $lang_code => $lang_name ) {
								$selected = ( $current_language === $lang_code ) ? 'selected' : '';
								echo '<option value="' . esc_html( $lang_code ) . '" ' . esc_html( $selected ) . '>' . esc_html( $lang_name ) . '</option>';
							}
							?>
                        </select>
                    </td>
                    <td>
                        <?php esc_html_e( 'Choose a language for the plugin.', 'service-booking' ); ?>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'language_settings_nonce' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_language_settings" id="save_language_settings" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onclick="return bm_language_settings_validation()">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


