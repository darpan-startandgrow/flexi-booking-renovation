<?php
if ( ! current_user_can( 'manage_options' ) ) {
    die( esc_html__( 'Forbidden', 'service-booking' ) );
}

$identifier = 'GLOBAL';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();

if ( filter_input( INPUT_POST, 'save_upload' ) || filter_input( INPUT_POST, 'resetdata' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_upload_settings' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_upload',
        'resetdata',
    );

    if ( filter_input( INPUT_POST, 'save_upload' ) ) {
        $global_upload_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $global_upload_post != false ) {
            foreach ( $global_upload_post as $key => $value ) {
                if ( $key == 'bm_image_quality' ) {
                    $value = empty( $value ) ? 90 : $value;
                }
                $dbhandler->update_global_option_value( $key, $value );
            }
        }

        echo ( '<div id="successMessage" class="bm-notice">' );
        echo esc_html__( 'Data Saved Sucessfully.', 'service-booking' );
        echo ( '</div>' );
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
    <h2 class="title" style="font-weight: bold;"><a href="admin.php?page=bm_global"><div class="backbtn">&#8592;</div></a><?php esc_html_e( 'Image Upload Settings', 'service-booking' ); ?></h2>
    <form role="form" method="post" action="admin.php?page=bm_upload_settings">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bm_minimum_image_size"><?php esc_html_e( 'Minimum Image size (in bytes)', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_minimum_image_size" type="number" step="1" min="1" id="bm_minimum_image_size" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_minimum_image_size' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set an lower limit to the size of images to be uploaded. Sizes are in Bytes. For example, for 2MB limit use 2097152', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_maximum_image_size"><?php esc_html_e( 'Maximum Image size (in bytes)', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_maximum_image_size" type="number" step="1" min="1" id="bm_maximum_image_size" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_maximum_image_size' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set an upper limit to the size of images to be uploaded. Sizes are in Bytes. For example, for 2MB limit use 2097152', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_minimum_image_width"><?php esc_html_e( 'Minimum Image Width', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_minimum_image_width" type="number" step="1" min="1" id="bm_minimum_image_width" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_minimum_image_width' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set a minimum width (in pixels) for the images to be uploaded', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_maximum_image_width"><?php esc_html_e( 'Maximum Image Width', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_maximum_image_width" type="number" step="1" min="1" id="bm_maximum_image_width" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_maximum_image_width' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set a maximum width (in pixels) for the images to be uploaded', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_minimum_image_height"><?php esc_html_e( 'Minimum Image Height', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_minimum_image_height" type="number" step="1" min="1" id="bm_minimum_image_height" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_minimum_image_height' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set a minimum height (in pixels) for the images to be uploaded', 'service-booking' ); ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bm_maximum_image_height"><?php esc_html_e( 'Maximum Image Height', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_maximum_image_height" type="number" step="1" min="1" id="bm_maximum_image_height" class="regular-text" value="<?php echo esc_attr( $dbhandler->get_global_option_value( 'bm_maximum_image_height' ) ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set a maximum height (in pixels) for the images to be uploaded', 'service-booking' ); ?>
                    </td>
                </tr>
                <!-- <tr>
                    <th scope="row"><label for="bm_image_quality"><?php esc_html_e( 'Image quality', 'service-booking' ); ?></label></th>
                    <td>
                        <input name="bm_image_quality" type="number" step="1" min="1" max="100" id="bm_image_quality" class="regular-text" value="<?php echo esc_attr( !empty( $dbhandler->get_global_option_value( 'bm_image_quality' ) ) ? $dbhandler->get_global_option_value( 'bm_image_quality' ) : 90 ); ?>">
                    </td>
                    <td>
                        <?php esc_html_e( 'Set quality of images being rendered. A lower quality can improve load times. Values vary between 1 to 100', 'service-booking' ); ?>
                    </td>
                </tr> -->
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_upload_settings' ); ?>
                    <a href="admin.php?page=bm_global" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_upload" id="save_upload" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>">
                    <input type="submit" name="resetdata" id="resetdata" class="button button-secondary" value="<?php esc_attr_e( 'Clear all data', 'service-booking' ); ?>">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>


