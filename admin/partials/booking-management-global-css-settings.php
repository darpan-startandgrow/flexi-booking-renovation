<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();

$primary_color = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
$contrast      = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';

if ( filter_input( INPUT_POST, 'save_css_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_global_css_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_css_global',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_css_global' ) ) {
        $general_css_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $general_css_post != false ) {
            $general_css_post['bm_frontend_service_title_color']   = isset( $general_css_post['bm_frontend_service_title_color'] ) ? $general_css_post['bm_frontend_service_title_color'] : '#000000';
            $general_css_post['bm_frontend_service_title_color']   = isset( $general_css_post['bm_frontend_service_price_text_color'] ) ? $general_css_post['bm_frontend_service_price_text_color'] : '#000000';
            $general_css_post['bm_frontend_book_button_color']     = isset( $general_css_post['bm_frontend_book_button_color'] ) ? $general_css_post['bm_frontend_book_button_color'] : $primary_color;
            $general_css_post['bm_frontend_book_button_txt_color'] = isset( $general_css_post['bm_frontend_book_button_txt_color'] ) ? $general_css_post['bm_frontend_book_button_txt_color'] : $contrast;

            foreach ( $general_css_post as $key => $value ) {
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

?>

<div class="sg-admin-main-box">
<div class="wrap">
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Service Shortcode CSS Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_css_settings">
        <tbody>
            <br>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_date_field_label_font"><?php esc_html_e( 'Date Field Label Font Size (in px)', 'service-booking' ); ?></label></th>
                    <td><input name="bm_date_field_label_font" type="number" step="1" min="1" id="bm_date_field_label_font" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_date_field_label_font', '20' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the date field label font size in service shortcode in px.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_category_search_label_font"><?php esc_html_e( 'Category Search Label Font Size (in px)', 'service-booking' ); ?></label></th>
                    <td><input name="bm_category_search_label_font" type="number" step="1" min="1" id="bm_category_search_label_font" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_category_search_label_font', '20' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the category search label font size in service shortcode in px.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_category_checkbox_label_font"><?php esc_html_e( 'Categories Checkbox Label Font Size (in px)', 'service-booking' ); ?></label></th>
                    <td><input name="bm_category_checkbox_label_font" type="number" step="1" min="1" id="bm_category_checkbox_label_font" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_category_checkbox_label_font', '14' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the categories checkbox label font size in service shortcode in px.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_service_title_font"><?php esc_html_e( 'Service Title Font Size (in px)', 'service-booking' ); ?></label></th>
                    <td><input name="bm_service_title_font" type="number" step="1" min="1" id="bm_service_title_font" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_service_title_font', '20' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service title font size in service shortcode in px.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_service_shrt_desc_font"><?php esc_html_e( 'Service Short Description Font Size (in px)', 'service-booking' ); ?></label></th>
                    <td><input name="bm_service_shrt_desc_font" type="number" step="1" min="1" id="bm_service_shrt_desc_font" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_service_shrt_desc_font', '14' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service short description font size in service shortcode in px.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_service_price_txt_font"><?php esc_html_e( 'Service Price Text Font Size (in px)', 'service-booking' ); ?></label></th>
                    <td><input name="bm_service_price_txt_font" type="number" step="1" min="1" id="bm_service_price_txt_font" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_service_price_txt_font', '16' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service price text font size in service shortcode in px.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_frontend_service_title_color"><?php esc_html_e( 'Service title Color', 'service-booking' ); ?></label></th>
                    <td><input name="bm_frontend_service_title_color" type="color" id="bm_frontend_service_title_color" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_frontend_service_title_color', '#000000' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service name color in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_frontend_service_price_text_color"><?php esc_html_e( 'Price Text Color', 'service-booking' ); ?></label></th>
                    <td><input name="bm_frontend_service_price_text_color" type="color" id="bm_frontend_service_price_text_color" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_frontend_service_price_text_color', '#000000' ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service price text color in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_frontend_book_button_color"><?php esc_html_e( 'Book button Color', 'service-booking' ); ?></label></th>
                    <td><input name="bm_frontend_book_button_color" type="color" id="bm_frontend_book_button_color" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service book button color in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_frontend_book_button_txt_color"><?php esc_html_e( 'Book button Text Color', 'service-booking' ); ?></label></th>
                    <td><input name="bm_frontend_book_button_txt_color" type="color" id="bm_frontend_book_button_txt_color" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast ) ); ?>"></td>
                    <td><?php esc_html_e( 'Set the service book button text color in service shortcode.', 'service-booking' ); ?></td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_global_css_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_css_global" id="save_css_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>
