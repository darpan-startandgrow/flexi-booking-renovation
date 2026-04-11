<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();

if ( filter_input( INPUT_POST, 'save_general_global' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_global_general_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_general_global',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_general_global' ) ) {
        $general_settings_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $general_settings_post != false ) {
            $general_settings_post['bm_show_frontend_progress_bar']                  = isset( $general_settings_post['bm_show_frontend_progress_bar'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_grid_list_button']              = isset( $general_settings_post['bm_show_frontend_grid_list_button'] ) ? 1 : 0;
            $general_settings_post['bm_frontend_view_type']                          = isset( $general_settings_post['bm_frontend_view_type'] ) ? $general_settings_post['bm_frontend_view_type'] : 'grid';
            $general_settings_post['bm_show_frontend_service_booking_date_field']    = isset( $general_settings_post['bm_show_frontend_service_booking_date_field'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_search']                = isset( $general_settings_post['bm_show_frontend_service_search'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_category_search']               = isset( $general_settings_post['bm_show_frontend_category_search'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_sorting']               = isset( $general_settings_post['bm_show_frontend_service_sorting'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_image']                 = isset( $general_settings_post['bm_show_frontend_service_image'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_desc_read_more_button'] = isset( $general_settings_post['bm_show_frontend_service_desc_read_more_button'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_price']                 = isset( $general_settings_post['bm_show_frontend_service_price'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_duration']              = isset( $general_settings_post['bm_show_frontend_service_duration'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_service_description']           = isset( $general_settings_post['bm_show_frontend_service_description'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_edit_button_in_booking_form']   = isset( $general_settings_post['bm_show_frontend_edit_button_in_booking_form'] ) ? 1 : 0;
            $general_settings_post['bm_show_frontend_pagination']                    = isset( $general_settings_post['bm_show_frontend_pagination'] ) ? 1 : 0;
            $general_settings_post['bm_show_service_to_time_slot']                   = isset( $general_settings_post['bm_show_service_to_time_slot'] ) ? 1 : 0;
            $general_settings_post['bm_show_service_limit_box']                      = isset( $general_settings_post['bm_show_service_limit_box'] ) ? 1 : 0;

            foreach ( $general_settings_post as $key => $value ) {
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
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a> <?php esc_html_e( 'Service Shortcode Content Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_global_general_settings">
        <tbody>
            <br>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row" style="width: 22%;"><label for="bm_show_frontend_progress_bar"><?php esc_html_e( 'Show Progress Bar', 'service-booking' ); ?></label></th>
                    <td class="bm-checkbox-td" style="width: 22%;">
                        <input name="bm_show_frontend_progress_bar" type="checkbox" id="bm_show_frontend_progress_bar" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_progress_bar' ), '1' ); ?>>
                        <label for="bm_show_frontend_progress_bar"></label>
                    </td>
                    <td><?php esc_html_e( 'If checked, the step Bar is shown in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Grid/List View Button', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_grid_list_button" type="checkbox" id="bm_show_frontend_grid_list_button" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_grid_list_button' ), '1' ); ?>>
                        <label for="bm_show_frontend_grid_list_button"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the Grid/List view button in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_frontend_view_type"><?php esc_html_e( 'Service Default View Type', 'service-booking' ); ?></label></th>
                    <td class="bminput global_general_required">
                        <input name="bm_frontend_view_type" type="radio" id="bm_frontend_view_type" value="grid" <?php checked( $dbhandler->get_global_option_value( 'bm_frontend_view_type' ), 'grid' ); ?>> <?php esc_html_e( 'Grid', 'service-booking' ); ?> &nbsp;
                        <input name="bm_frontend_view_type" type="radio" id="bm_frontend_view_type" value="list" <?php checked( $dbhandler->get_global_option_value( 'bm_frontend_view_type' ), 'list' ); ?>> <?php esc_html_e( 'List', 'service-booking' ); ?>
                        <div class="global_general_errortext"></div>
                    </td>
                    <td><?php esc_html_e( 'Switch between Grid and List view in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Booking Date Field', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_booking_date_field" type="checkbox" id="bm_show_frontend_service_booking_date_field" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_booking_date_field' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_booking_date_field"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the Date field in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Service Search', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_search" type="checkbox" id="bm_show_frontend_service_search" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_search' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_search"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the avaialble services list below the date field in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Category Search', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_category_search" type="checkbox" id="bm_show_frontend_category_search" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_category_search' ), '1' ); ?>>
                        <label for="bm_show_frontend_category_search"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the filter by category select box in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Service Sorting Field', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_sorting" type="checkbox" id="bm_show_frontend_service_sorting" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_sorting' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_sorting"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the sort options in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Service Image', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_image" type="checkbox" id="bm_show_frontend_service_image" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_image' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_image"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the service images in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Read More', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_desc_read_more_button" type="checkbox" id="bm_show_frontend_service_desc_read_more_button" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_desc_read_more_button' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_desc_read_more_button"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the show full description icon in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Service Price', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_price" type="checkbox" id="bm_show_frontend_service_price" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_price' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_price"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the service price in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Service Duration', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_duration" type="checkbox" id="bm_show_frontend_service_duration" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_duration' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_duration"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the service duration in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Service Short Description', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_service_description" type="checkbox" id="bm_show_frontend_service_description" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_service_description' ), '1' ); ?>>
                        <label for="bm_show_frontend_service_description"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the service short description in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Edit Button in booking modal', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_edit_button_in_booking_form" type="checkbox" id="bm_show_frontend_edit_button_in_booking_form" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_edit_button_in_booking_form' ), '1' ); ?>>
                        <label for="bm_show_frontend_edit_button_in_booking_form"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the edit button in booking modal.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show Pagination', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_frontend_pagination" type="checkbox" id="bm_show_frontend_pagination" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_frontend_pagination' ), '1' ); ?>>
                        <label for="bm_show_frontend_pagination"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide pagination in service shortcode.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show service \'to\' slots', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_service_to_time_slot" type="checkbox" id="bm_show_service_to_time_slot" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot' ), '1' ); ?>>
                        <label for="bm_show_service_to_time_slot"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide \'to\' service slots in time slot modal.', 'service-booking' ); ?></td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show service limit box', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="bm_show_service_limit_box" type="checkbox" id="bm_show_service_limit_box" class="regular-text bm_toggle" <?php checked( $dbhandler->get_global_option_value( 'bm_show_service_limit_box' ), '1' ); ?>>
                        <label for="bm_show_service_limit_box"></label>
                    </td>
                    <td><?php esc_html_e( 'Show/Hide the select box to choose beween number of service views in service shortcode.', 'service-booking' ); ?></td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_global_general_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_general_global" id="save_general_global" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onClick="return global_general_settings_validation()">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                <div class="all_tmpl_error_text" style="display:none;"></div>
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>





