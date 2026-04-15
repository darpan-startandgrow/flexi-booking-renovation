<?php
$dbhandler       = new BM_DBhandler();
$bmrequests      = new BM_Request();
$pagenum         = filter_input( INPUT_GET, 'pagenum' );
$pagenum         = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit_param     = filter_input( INPUT_GET, 'limit', FILTER_VALIDATE_INT );
$limit           = $limit_param ? $limit_param : ( ! empty( $dbhandler->get_global_option_value( 'bm_templates_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_templates_per_page' ) : 10 );
$offset          = ( ( $pagenum - 1 ) * $limit );
$i               = ( 1 + $offset );
$language        = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
$back_lang       = $dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
$language        = ! empty( $back_lang ) ? $back_lang : $language;
$total           = $dbhandler->bm_count( 'EMAIL_TMPL' );
$email_templates = $dbhandler->get_all_result( 'EMAIL_TMPL', '*', 1, 'results', $offset, $limit );
$num_of_pages    = ceil( $total / $limit );
$pagination      = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );

?>


<div class="sg-admin-main-box">
<!-- Templates -->
<div class="wrap listing_table" id="templates_records_listing">
	<div class="row">
		<div style="float:left;">
			<h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Email Templates', 'service-booking' ); ?></h2>
			<a href="admin.php?page=bm_add_template" class="button button-primary" style="margin-bottom:10px;" title="<?php esc_html_e( 'Add Template', 'service-booking' ); ?>"><?php esc_html_e( 'Add Template', 'service-booking' ); ?>&nbsp;<i class="fa fa-plus" aria-hidden="true"></i></a>
		</div>
	</div>
	<?php if ( isset( $email_templates ) ) { ?>
		<!-- Bulk Actions Bar -->
		<div class="bm-bulk-bar" data-table="email_template" style="margin-bottom:10px;padding:8px 12px;background:#f0f0f1;border:1px solid #c3c4c7;border-radius:4px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
			<select class="bm-bulk-action-select" data-table="email_template" style="min-width:180px;">
				<option value=""><?php esc_html_e( '— Bulk Actions —', 'service-booking' ); ?></option>
				<option value="bulk_delete"><?php esc_html_e( 'Delete Selected', 'service-booking' ); ?></option>
				<option value="bulk_toggle_status"><?php esc_html_e( 'Toggle Status', 'service-booking' ); ?></option>
			</select>
			<span class="bm-bulk-status-wrap" style="display:none;">
				<select class="bm-bulk-status-val">
					<option value="1"><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
					<option value="0"><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
				</select>
			</span>
			<button type="button" class="button button-primary bm-bulk-apply" data-table="email_template" disabled><?php esc_html_e( 'Apply', 'service-booking' ); ?></button>
			<span class="bm-bulk-count" style="color:#666;font-size:12px;margin-left:8px;"></span>
			
			<!-- Dynamic Pagination -->
			<div class="bm-dynamic-pagination" style="margin-left:auto;display:flex;align-items:center;gap:6px;">
				<label for="email_template_items_per_page" style="font-size:13px;color:#3c434a;"><?php esc_html_e( 'Items per page:', 'service-booking' ); ?></label>
				<select id="email_template_items_per_page" name="email_template_items_per_page" style="min-width:80px;">
					<option value="10">10</option>
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="100">100</option>
				</select>
			</div>
		</div>
		<input type="hidden" name="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
		<table class="wp-list-table widefat striped">
			<thead>
				<tr>
					<th style="text-align:center;width:30px;"><input type="checkbox" class="bm-bulk-check-all" data-table="email_template" title="<?php esc_attr_e( 'Select All', 'service-booking' ); ?>"></th>
					<th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
					<th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Name', 'service-booking' ); ?></th>
					<th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Type', 'service-booking' ); ?></th>
					<th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Status', 'service-booking' ); ?></th>
					<th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
				</tr>
			</thead>
			<tbody class="template_records">
				<?php
				foreach ( $email_templates as $template ) {
					$tmpl_name = "tmpl_name_$language";
					?>
					<tr>
						<form role="form" method="post">
							<td style="text-align:center;"><input type="checkbox" class="bm-bulk-row-check email_template-row-check" data-table="email_template" value="<?php echo esc_attr( $template->id ); ?>"></td>
							<td style="text-align: center;"><?php echo esc_attr( $i ); ?></td>
							<td style="text-align: center;" title="<?php echo isset( $template->$tmpl_name ) ? esc_html( $template->$tmpl_name ) : ''; ?>"><?php echo isset( $template->$tmpl_name ) ? esc_html( mb_strimwidth( $template->$tmpl_name, 0, 40, '...' ) ) : ''; ?></td>
							<td style="text-align: center;" title="<?php echo isset( $template->type ) ? esc_html( $bmrequests->bm_fetch_template_type_name_by_type_id( $template->type ) ) : ''; ?>"><?php echo isset( $template->type ) ? esc_html( mb_strimwidth( $bmrequests->bm_fetch_template_type_name_by_type_id( $template->type ), 0, 40, '...' ) ) : ''; ?></td>
							<td style="text-align: center;" class="bm-checkbox-td">
								<input name="bm_template_status" type="checkbox" id="bm_template_status_<?php echo esc_attr( $template->id ); ?>" data-type="<?php echo isset( $template->type ) ? esc_attr( $template->type ) : -1; ?>" class="regular-text auto-checkbox bm_toggle" <?php checked( esc_attr( $template->status ), 1 ); ?> onchange="bm_change_template_visibility(this)">
								<label for="bm_template_status_<?php echo esc_attr( $template->id ); ?>"></label>
							</td>
							<td style="text-align: center;">
								<button type="button" name="edittemplate" class="edit-button" id="edittemplate" title="<?php esc_html_e( 'Edit', 'service-booking' ); ?>" value="<?php echo isset( $template->id ) ? esc_attr( $template->id ) : 0; ?>"><i class="fa fa-edit" aria-hidden="true"></i></button>
								<button type="button" name="deltemplate" class="delete-button" id="deltemplate" title="<?php esc_html_e( 'Delete', 'service-booking' ); ?>" value="<?php echo isset( $template->id ) ? esc_attr( $template->id ) : 0; ?>"><i class="fa fa-trash" aria-hidden="true" style="color:red"></i></button>
							</td>
						</form>
					</tr>
					<?php
					++$i;
				}
				?>
			</tbody>
		</table>
		<div class="template_pagination"><?php echo wp_kses_post( $pagination ?? '' ); ?></div>
	<?php } else { ?>
		<div class="bm_no_records_message" style="display:flow-root;">
			<div class="Pointer">
				<p class="message"><?php esc_html_e( 'No Templates Found', 'service-booking' ); ?></p>
			</div>
		</div>
	<?php } ?>
</div>

<input type="hidden" id="template_pagenum" value="<?php echo esc_attr( 1 ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__jackInTheBox" id="popup-message-container">
	<span id="popup-message"></span>
	<button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>


