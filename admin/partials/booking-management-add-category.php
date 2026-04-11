<?php
$identifier = 'CATEGORY';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();
$id         = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );

if ( $id == false || $id == null ) {
    $id = 0;
}

if ( $id != 0 ) {
    $cat_row = $dbhandler->get_row( $identifier, $id );
}

if ( ( filter_input( INPUT_POST, 'savecat' ) ) || ( filter_input( INPUT_POST, 'upcat' ) ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_cat_section' ) ) {
        die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'savecat',
        'upcat',
        'resetfrm',
    );

    $cat_post = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

    if ( $cat_post != false ) {
        $data = array(
            'cat_name'     => isset( $cat_post['cat_name'] ) ? ucfirst( $cat_post['cat_name'] ) : '',
            'cat_in_front' => isset( $cat_post['cat_in_front'] ) ? 1 : 0,
        );
    }

    if ( ( filter_input( INPUT_POST, 'savecat' ) ) ) {

        if ( isset( $data ) ) {
            $data['cat_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $category_id            = $dbhandler->insert_row( $identifier, $data );
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Category Data could not be Processed !!', 'service-booking' );
            echo ( '</div>' );
        }

        if ( $category_id ) {
            wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_all_categories' ) );
            exit;
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Category Could not be Added !!', 'service-booking' );
            echo ( '</div>' );
        }
    }

    if ( ( filter_input( INPUT_POST, 'upcat' ) ) ) {
        if ( $id != 0 ) {

            if ( isset( $data ) ) {
                $data['cat_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
                $cat_updated            = $dbhandler->update_row( $identifier, 'id', $id, $data, '', '%d' );
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Category Data could not be Processed !!', 'service-booking' );
                echo ( '</div>' );
            }

            if ( $cat_updated ) {
                wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_category&id=' . esc_attr( $id ) ) );
                exit;
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Category Could not be Updated !!', 'service-booking' );
                echo ( '</div>' );
            }
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Category Id could not fetched !!', 'service-booking' );
            echo ( '</div>' );
        }//end if
    }//end if
}//end if

?>

<div class="sg-admin-main-box">
<div class="wrap">
    <form role="form" method="post">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="cat_name"><?php esc_html_e( 'Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                    <td class="bminput bm_required">
                        <input name="cat_name" type="text" id="cat_name" placeholder="<?php esc_html_e( 'name', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $cat_row ) && !empty( $cat_row->cat_name ) ? esc_html( $cat_row->cat_name ) : ''; ?>" autocomplete="off">
                        <div class="errortext"></div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Show in Frontend', 'service-booking' ); ?></th>
                    <td class="bm-checkbox-td">
                        <input name="cat_in_front" type="checkbox" id="cat_in_front" class="regular-text bm_toggle" <?php echo isset( $cat_row ) && isset( $cat_row->cat_in_front ) ? checked( esc_attr( $cat_row->cat_in_front ), 1 ) : 'checked'; ?>>
                        <label for="cat_in_front"></label>
                    </td>
                </tr>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_cat_section' ); ?>
                    <a href="admin.php?page=bm_all_categories" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <?php if ( !isset( $cat_row ) ) { ?>
                        <input type="submit" name="savecat" id="savecat" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onClick="return add_form_validation()">
                    <?php } else { ?>
                        <input type="submit" name="upcat" id="upcat" class="button button-primary" value="<?php esc_attr_e( 'Update', 'service-booking' ); ?>" onClick="return add_form_validation()">
                    <?php } ?>
                    <!-- <?php if ( !isset( $cat_row ) ) { ?>
                        <button type="reset" name="resetfrm" id="resetfrm" class="button" style="background-color: #5F5B50;color: white;"><?php esc_attr_e( 'Reset', 'service-booking' ); ?></button>
                    <?php } ?> -->
                    <div class="all_error_text" style="display:none;"></div>
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>

