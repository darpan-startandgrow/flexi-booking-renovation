<?php
$identifier    = 'EMAIL_TMPL';
$dbhandler     = new BM_DBhandler();
$bmrequests    = new BM_Request();
$id            = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$language      = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
$back_lang     = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
$language      = ! empty( $back_lang ) ? $back_lang : $language;
$tmpl_name     = "tmpl_name_$language";
$email_subject = "email_subject_$language";
$email_body    = "email_body_$language";

if ( $id == false || $id == null ) {
	$id = 0;
}

if ( ! empty( $id ) ) {
	$template = $dbhandler->get_row( $identifier, $id );
}

$email_content = array(
	'wpautop'           => false,
	'media_buttons'     => true,
	'textarea_name'     => $email_body,
	'textarea_rows'     => 20,
	'tabindex'          => 4,
	'editor_height'     => 200,
	'tabfocus_elements' => ':prev,:next',
	'editor_css'        => '',
	'editor_class'      => '',
	'teeny'             => false,
	'dfw'               => false,
	'tinymce'           => true,
	'quicktags'         => true,
);

add_action( 'media_buttons', array( $this, 'bm_fields_list_for_email' ) );

if ( ( filter_input( INPUT_POST, 'savetemplate' ) ) ) {
	$retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
	if ( ! wp_verify_nonce( $retrieved_nonce, 'save_template_section' ) ) {
		die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
	}

	$exclude = array(
		'_wpnonce',
		'_wp_http_referer',
		'savetemplate',
		'bm_field_list',
	);

	if ( ( filter_input( INPUT_POST, 'savetemplate' ) ) ) {
		$tmpl_data = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

		if ( $tmpl_data != false ) {
			$current_type   = isset( $tmpl_data['type'] ) ? $tmpl_data['type'] : -1;
			$current_status = isset( $tmpl_data['status'] ) ? $tmpl_data['status'] : -1;
			$active_type    = $bmrequests->bm_check_active_email_template_of_a_specific_type( $current_type );

			if ( ! empty( $id ) ) {
				$active_template_id = $bmrequests->bm_fetch_active_email_template_id_of_a_specific_type( $current_type );

				if ( ( $current_status == 1 ) && $active_type && ( $active_template_id > 0 ) && ( $active_template_id != $id ) ) {
					echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
					echo esc_html__( 'There is already an active template for this type, please deactivate the existing template.', 'service-booking' );
					echo ( '</div>' );
				} else {
					$tmpl_data['template_updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

					$updated = $dbhandler->update_row( $identifier, 'id', $id, $tmpl_data, '', '%d' );

					if ( $updated ) {
						wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_template&id=' . esc_attr( $id ) ) );
						exit;
					} else {
						echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
						echo esc_html__( 'Template Could not be Updated !!', 'service-booking' );
						echo ( '</div>' );
					}
				}
			} elseif ( $current_status == 1 && $active_type ) {
					echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
					echo esc_html__( 'There is already an active template for this type, please deactivate the existing template.', 'service-booking' );
					echo ( '</div>' );
			} else {
				$tmpl_data['template_created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();

				$id = $dbhandler->insert_row( $identifier, $tmpl_data );

				if ( ! empty( $id ) ) {
					if ( $dbhandler->get_global_option_value( 'bm_email_templates_created', '0' ) == '0' ) {
						$dbhandler->update_global_option_value( 'bm_email_templates_created', '1' );
					}
					wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_email_templates' ) );
					exit;
				} else {
					echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
					echo esc_html__( 'Template Could not be Added !!', 'service-booking' );
					echo ( '</div>' );
				}
			}
		} else {
			echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
			echo esc_html__( 'Template Data could not be Processed !!', 'service-booking' );
			echo ( '</div>' );
		}
	}
}//end if

?>

<div class="sg-admin-main-box" id="email-template-main-box">
<div class="wrap listing_table">
	<div class="row">
		<p>
		<h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Add Template', 'service-booking' ); ?></h2>
		</p>
	</div>
	
	<form role="form" method="post">
		<tbody>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="status"><?php esc_html_e( 'Status', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
					<td  class="bminput bm_required">
						<select name="status" id="status" class="regular-text">
							<option value="1" <?php isset( $template ) && isset( $template->status ) ? selected( $template->status, 1 ) : ''; ?>><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
							<option value="0" <?php isset( $template ) && isset( $template->status ) ? selected( $template->status, 0 ) : ''; ?>><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
						</select>
						<span> <?php esc_html_e( 'Status of this template', 'service-booking' ); ?></span>
						<div class="errortext"></div>
					</td>
					
				</tr>
				<tr>
					<th scope="row"><label for="<?php echo esc_html( $tmpl_name ); ?>"><?php esc_html_e( 'Template Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
					<td class="bminput bm_tmpl_required">
						<input name="<?php echo esc_html( $tmpl_name ); ?>" type="text" id="<?php echo esc_html( $tmpl_name ); ?>" placeholder="<?php esc_html_e( 'template name', 'service-booking' ); ?>" class="regular-text" value="<?php echo isset( $template ) && ! empty( $template->$tmpl_name ) ? esc_html( $template->$tmpl_name ) : ''; ?>" autocomplete="off">
						<div class="tmpl_errortext"></div>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="type"><?php esc_html_e( 'Template Type', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
					<td class="bminput bm_tmpl_required">
						<select name="type" id="type" class="regular-text">
							<option value="0" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 0 ) : ''; ?>><?php esc_html_e( 'New order (frontend)', 'service-booking' ); ?></option>
							<option value="1" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 1 ) : ''; ?>><?php esc_html_e( 'New order (backend)', 'service-booking' ); ?></option>
							<option value="12" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 12 ) : ''; ?>><?php esc_html_e( 'New request (frontend)', 'service-booking' ); ?></option>
							<option value="13" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 13 ) : ''; ?>><?php esc_html_e( 'New request (backend)', 'service-booking' ); ?></option>
							<option value="2" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 2 ) : ''; ?>><?php esc_html_e( 'Order refund', 'service-booking' ); ?></option>
							<option value="3" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 3 ) : ''; ?>><?php esc_html_e( 'Order cancel', 'service-booking' ); ?></option>
							<option value="4" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 4 ) : ''; ?>><?php esc_html_e( 'Order approval', 'service-booking' ); ?></option>
							<option value="5" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 5 ) : ''; ?>><?php esc_html_e( 'Admin new order notification', 'service-booking' ); ?></option>
							<option value="14" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 14 ) : ''; ?>><?php esc_html_e( 'Admin new request notification', 'service-booking' ); ?></option>
							<option value="6" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 6 ) : ''; ?>><?php esc_html_e( 'Admin order cancel notification', 'service-booking' ); ?></option>
							<option value="7" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 7 ) : ''; ?>><?php esc_html_e( 'Admin order refund notification', 'service-booking' ); ?></option>
							<option value="8" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 8 ) : ''; ?>><?php esc_html_e( 'Admin order approval notification', 'service-booking' ); ?></option>
							<option value="9" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 9 ) : ''; ?>><?php esc_html_e( 'Failed Order', 'service-booking' ); ?></option>
							<option value="10" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 10 ) : ''; ?>><?php esc_html_e( 'Failed order admin notification', 'service-booking' ); ?></option>
							<option value="11" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 11 ) : ''; ?>><?php esc_html_e( 'Gift voucher notification', 'service-booking' ); ?></option>
							<option value="15" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 15 ) : ''; ?>><?php esc_html_e( 'Redeem voucher admin notification', 'service-booking' ); ?></option>
							<option value="16" <?php isset( $template ) && isset( $template->type ) ? selected( $template->type, 16 ) : ''; ?>><?php esc_html_e( 'Redeem voucher notification', 'service-booking' ); ?></option>
						</select>
						<div class="tmpl_errortext"></div>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="<?php echo esc_html( $email_subject ); ?>"><?php esc_html_e( 'Email Subject', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
					<td class="bminput bm_tmpl_required">
						<input name="<?php echo esc_html( $email_subject ); ?>" type="text" placeholder="<?php esc_html_e( 'email subject', 'service-booking' ); ?>" id="<?php echo esc_html( $email_subject ); ?>" class="regular-text" value="<?php echo isset( $template ) && isset( $template->$email_subject ) ? esc_html( $template->$email_subject ) : ''; ?>" autocomplete="off">
						<div class="tmpl_errortext"></div>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="<?php echo esc_html( $email_body ); ?>"><?php esc_html_e( 'Email Body', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
					<td id="email_body_td" class="bminput">
						<div style="width: 54%;" class="sg-rg-buttom">
							<?php isset( $template ) && isset( $template->$email_body ) ? wp_editor( $template->$email_body, $email_body, $email_content ) : wp_editor( '', $email_body, $email_content ); ?>
							<div class="tmpl_errortext"></div>
						</div>
					</td>
				</tr>
			</table>
			<div class="row">
				<p class="submit">
					<?php wp_nonce_field( 'save_template_section' ); ?>
					<a href="admin.php?page=bm_email_templates" class="button">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
					<input type="submit" name="savetemplate" id="savetemplate" class="button button-primary" value="<?php empty( $id ) ? esc_attr_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="return add_template_validation()">
					<!-- <?php if ( empty( $id ) ) { ?>
						<button type="reset" name="resetfrm" id="resetfrm" class="button" style="background-color: #5F5B50;color: white;"><?php esc_attr_e( 'Reset', 'service-booking' ); ?></button>
					<?php } ?> -->
				<div class="all_global_general_error_text" style="display:none;"></div>
				</p>
			</div>
		</tbody>
	</form>
</div>

<div class="loader_modal"></div>
</div>

