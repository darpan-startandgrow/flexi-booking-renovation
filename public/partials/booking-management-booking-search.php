<?php

$dbhandler      = new BM_DBhandler();
$bmrequests     = new BM_Request();
$cat_identifier = 'CATEGORY';
$svc_identifier = 'SERVICE';
$timezone       = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
$now            = ( new DateTime( 'now', new DateTimeZone( $timezone ) ) );
$today          = $now->format( 'Y-m-d' );
$current_time   = $now->format( 'H:i' );
$default_date   = $today;

$date_label_font       = $dbhandler->get_global_option_value( 'bm_date_field_label_font', '20' ) . 'px';
$cat_search_label_font = $dbhandler->get_global_option_value( 'bm_category_search_label_font', '20' ) . 'px';
$cat_checkbox_txt_font = $dbhandler->get_global_option_value( 'bm_category_checkbox_label_font', '14' ) . 'px';

$services = $dbhandler->get_results_with_join(
	array( $svc_identifier, 's' ),
	's.id, s.service_name, s.service_category',
	array(
		array(
			'table' => $cat_identifier,
			'alias' => 'c',
			'on'    => 's.service_category = c.id',
			'type'  => 'LEFT',
		),
	),
	array( 's.is_service_front' => array( '=' => 1 ) ),
	'results',
	0,
	false,
	's.id',
	'DESC',
	'AND (c.cat_in_front = 1 OR s.service_category = 0)'
);

$categories = ! empty( $services ) ? array_values( array_unique( array_column( $services, 'service_category' ) ) ) : array();

$service_start_time = $dbhandler->get_global_option_value( 'bm_svc_overall_start_time' );

if ( ! empty( $service_start_time ) ) {
	if ( $service_start_time < $current_time ) {
		$now->modify( '+1 day' );
		$default_date = $now->format( 'Y-m-d' );
	}
}

$referrer = wp_get_referer();

if ( $referrer && strpos( $referrer, 'cortespa.it/san-valentino' ) !== false ) {
    $default_date = gmdate( 'Y-m-d', strtotime( '2026-02-14' ) );
}

$primary_color      = $bmrequests->bm_get_theme_color( 'primary' ) ?? '#000000';
$contrast           = $bmrequests->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );
?>

<div class="pagewrapper svc_search_shortcode_content" id="first_step">
	<div class="topbar" 
	<?php
	if ( $dbhandler->get_global_option_value( 'bm_show_frontend_progress_bar', 0 ) == 0 ) {
		echo "style='display: none;'";
	}
	?>
						>
		<div class="progress-container <?php echo empty( $services ) ? 'hidden' : ''; ?>">
			<div class="progress" id="progress"></div>
			<div class="circle bgcolor textwhite" style="background:<?php echo esc_html( $svc_button_colour ) . '!important'; ?>"><span style="color:<?php echo esc_html( $svc_btn_txt_colour ) . '!important'; ?>"><?php echo esc_attr( '1' ); ?></span></div>
			<div class="circle"><?php echo esc_attr( '2' ); ?></div>
			<div class="circle"><?php echo esc_attr( '3' ); ?></div>
		</div>
	</div>

	<div class="searchpage">
		<div class="leftbar" id="leftbar">
			<div class="inputgroup"
			<?php
			if ( ! $visibility['date'] ) {
				echo "style='display: none;'";}
			?>
									>
				<h3 class="category_label <?php echo empty( $services ) ? 'hidden' : ''; ?>" style="font-size:<?php echo esc_attr( $date_label_font ) . '!important'; ?>"><?php esc_html_e( 'Select Date', 'service-booking' ); ?></h3>
				<span class="mobile-close" onclick="mobileFilter()">&times;</span>
				<input type="date" class="textbox <?php echo empty( $services ) ? 'hidden' : ''; ?>" id="booking_date" min="<?php echo esc_html( $today ); ?>" value="<?php echo esc_html( $default_date ); ?>" onchange="bm_fetch_all_services('')" />
			</div>
			<div 
			<?php
			if ( ! $visibility['category_filter'] ) {
				echo "style='display: none;'";}
			?>
				>
				<?php if ( ! empty( $categories ) ) { ?>
					<div>
						<h3 class="category_label <?php echo empty( $categories ) ? 'hidden' : ''; ?>" style="font-size:<?php echo esc_attr( $cat_search_label_font ) . '!important'; ?>"><?php esc_html_e( 'Categories:', 'service-booking' ); ?></h3>
					</div>
				<?php } ?>
				<div class="all_available_categories">
					<?php
					if ( ! empty( $categories ) ) {
						$i = 1;
						foreach ( $categories as $key => $category ) {
							$category_name = ( new BM_Request() )->bm_fetch_category_name_by_category_id( $category );
							?>
							<div class="categories_available">
								<input type="checkbox" name="ct_<?php echo esc_attr( $category ); ?>" id="cat_<?php echo esc_attr( $i ); ?>" onclick="bm_filter_categories(this)">
								<label for="cat_<?php echo esc_attr( $i ); ?>" title="<?php echo esc_html( $category_name ); ?>" style="font-size:<?php echo esc_attr( $cat_checkbox_txt_font ) . '!important'; ?>"><?php echo esc_html( mb_strimwidth( $category_name, 0, 24, '...' ) ); ?></label>
							</div>
							<?php
							++$i;
						}
					}
					?>
				</div>
				<div class="showitemcountbox" 
					<?php
					if ( ! $visibility['service_limit'] ) {
						echo "style='display: none;'";}
					?>
												>
						<select class="textbox bordercolor" id="limit_count" onchange="bm_fetch_all_services('')">
							<option value="3"><?php esc_html_e( 'Show 3 items', 'service-booking' ); ?></option>
							<option value="6"><?php esc_html_e( 'Show 6 items', 'service-booking' ); ?></option>
							<option value="9" selected><?php esc_html_e( 'Show 9 items', 'service-booking' ); ?></option>
							<option value="-1"><?php esc_html_e( 'Show All items', 'service-booking' ); ?></option>
						</select>
				</div>
			</div>
		</div>

		<div class="rightbar">
			<!-- <div class="searchboxinnerbox grid-list-heading">
				<h3><?php esc_html_e( 'Search Results', 'service-booking' ); ?></h3>
			</div> -->
			<div class="searchtopbox">
				<div class="booking-container">
					
					<div class="searchboxinnerbox select_order_box <?php echo empty( $services ) ? 'hidden' : ''; ?>" 
				   
					<?php
					if ( ! $visibility['service_sorting'] ) {
						echo "style='display: none;'";}
					?>
																>
						<div class='select-container'>
						<i class="fa fa-file-text-o"></i> 
						<select class="textbox" id="service_category_result_order" onchange="bm_fetch_all_services('')">
							<option value="position_asc" <?php selected( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ), 'position_asc' ); ?>><?php esc_html_e( 'Service Position Ascending', 'service-booking' ); ?></option>
							<option value="position_desc" <?php selected( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ), 'position_desc' ); ?>><?php esc_html_e( 'Service Position Descending', 'service-booking' ); ?></option>
							<option value="name_asc" <?php selected( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ), 'name_asc' ); ?>><?php esc_html_e( 'Service Name Ascending', 'service-booking' ); ?></option>
							<option value="name_desc" <?php selected( $dbhandler->get_global_option_value( 'bm_svc_search_shortcode_data_sorting' ), 'name_desc' ); ?>><?php esc_html_e( 'Service Name Descending', 'service-booking' ); ?></option>
						</select>
						</div>
						
					</div>

					<div class="searchcategorybox">
					<div class="inputgroup mobdatepicker" 
						<?php
						if ( ! $visibility['date'] ) {
							echo "style='display: none;'";}
						?>
					>
						<input type="date" class="textbox <?php echo empty( $services ) ? 'hidden' : ''; ?>" id="booking_date_mobile" min="<?php echo esc_html( $today ); ?>" value="<?php echo esc_html( $default_date ); ?>" onchange="bm_fetch_all_services('', 'mobile')" />
					</div>

					<!-- <div class="mobfilter card-section-icon" onclick="mobileFilter()">
					<i class="fa fa-calendar"></i>
					</div> -->

					<span class="<?php echo empty( $services ) ? 'hidden' : ''; ?> filter-service-box"
						<?php
						if ( ! $visibility['service_filter'] ) {
							echo "style='display: none;'";}
						?>
							>
						<select class="textbox" name="search_by_service[]" id="search_by_service"  onchange="bm_filter_services_by_id()" multiple="multiple">
							<?php
							if ( ! empty( $services ) ) {
								foreach ( $services as $service ) {
									?>
									<option value="<?php echo esc_attr( $service->id ); ?>"><?php echo isset( $service->service_name ) ? esc_html( $service->service_name ) : ''; ?></option>
									<?php
								}
							}
							?>
						</select>
					</span>
					<span class="<?php echo empty( $category ) ? 'hidden' : ''; ?> filter-service-box mobcategorybox"
						<?php
						if ( ! $visibility['category_filter'] ) {
							echo "style='display: none;'";}
						?>
							>
						<select name="search_by_category[]" id="search_by_category" class="textbox" onchange="bm_filter_service_by_category()" multiple="multiple">
							<?php
							if ( ! empty( $categories ) ) {
								$i = 1;
								foreach ( $categories as $key => $category ) {
									$category_name = ( new BM_Request() )->bm_fetch_category_name_by_category_id( $category );
									?>
									<option value="<?php echo esc_html( $category ); ?>"><?php echo isset( $category_name ) ? esc_html( mb_strimwidth( $category_name, 0, 24, '...' ) ) : ''; ?></option>
									<?php
										++$i;
								}
							}
							?>
						</select>
					</span>
					</div>
					
					<div id="tab_nav">
						<ul class="<?php echo empty( $services ) ? 'hidden' : ''; ?>"
						<?php
						if ( ! $visibility['grid_list_button'] ) {
							echo "style='display: none;'";}
						?>
							>
							<li><a href="#" onclick="showGridOrList('gridview')" class="card-section-icon
								<?php
								if ( $visibility['view_type'] == 'grid' ) {
									echo ' selected current-view-type textblue';
								}
								?>
							"><i class="fa fa-th"></i></a></li>
							<li class="selected"><a href="#" onclick="showGridOrList('listview')" class="card-section-icon  
								<?php
								if ( $visibility['view_type'] == 'list' ) {
									echo ' selected current-view-type textblue';
								}
								?>
							"><i class="fa fa-list"></i></a></li>
						</ul>

						<div class="tabcontent 
						<?php
						if ( $visibility['view_type'] == 'grid' ) {
							echo 'selected';
						}
						?>
												">
							<div class="searchresultbar-tab1 gridview"></div>
							<div class="loader_modal">
								<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
							</div>
						</div>

						<div class="tabcontent 
						<?php
						if ( $visibility['view_type'] == 'list' ) {
							echo 'selected';
						}
						?>
												">
							<div class="searchresultbar listview"></div>
							<div class="loader_modal">
								<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
							</div>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
	<div class="bottombar <?php echo empty( $services ) ? 'hidden' : ''; ?>">
		<div class="pagination" 
			<?php
			if ( $dbhandler->get_global_option_value( 'bm_show_frontend_pagination', 0 ) == 0 ) {
				echo "style='display: none;'";
			}
			?>
		></div>
	</div>
</div>

<input type="hidden" id="svc_search_shortcode_pagenum" value="<?php echo esc_attr( 1 ); ?>"/>
<input type="hidden" id="selected_slot">
<input type="hidden" id="current_service_id">
<input type="hidden" id="selected_extra_service_ids">
<input type="hidden" id="selected_service_id">
<input type="hidden" id="total_service_booking">
<input type="hidden" id="no_of_persons">
<input type="hidden" id="active_services">
<input type="hidden" id="active_categories">
<input type="hidden" id="service_id_for_checkout">
<div id="time_slot_modal" class="modaloverlay">
	<div class="modal animate__animated animate__bounceIn">
		<span class="close" onclick="closeModal('time_slot_modal')">
			&times;
		</span>
		<h4><?php apply_filters( 'global_service_shortcode_slot_modal_heading', esc_html_e( 'Slot details', 'service-booking' ) ); ?></h4>
		<div class="modalcontentbox slot_box_modal modal-body" id="slot_details"></div>
		<div class="loader_modal">
			<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
		</div>
	</div>
</div>

<div id="user_form_modal" class="modaloverlay">
	<div class="modal animate__animated animate__bounceIn">
		<span class="close" onclick="closeModal('user_form_modal')">&times;</span>
		<div class="modalcontentbox2 modal-body" id="user_form"></div>
		<div class="loader_modal">
			<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
		</div>
	</div>
</div>

<div id="booking_detail_modal" class="modaloverlay">
	<div class="modal animate__animated animate__bounceIn">
		<span class="close" onclick="closeModal('booking_detail_modal')">&times;</span>
		<h4><?php esc_html_e( 'Booking Details', 'service-booking' ); ?></h4>
		<div class="modalcontentbox modal-body" id="booking_detail"></div>
		<div class="loader_modal">
			<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
		</div>
	</div>
</div>

<div id="extra_service_modal" class="modaloverlay">
	<div class="modal animate__animated animate__jello">
		<span class="close" onclick="closeModal('extra_service_modal')">
			&times;
		</span>
		<h4><?php esc_html_e( 'Select Extra Sevice', 'service-booking' ); ?></h4>
		<div class="modalcontentbox modal-body" id="extra_service_details"></div>
		<div class="loader_modal">
			<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
		</div>
	</div>
</div>

<div id="checkout_options_modal" class="modaloverlay">
	<div class="modal animate__animated animate__bounceIn">
		<span class="close" onclick="closeModal('checkout_options_modal')">&times;</span>
		<h4><?php esc_html_e( 'Select Checkout Type', 'service-booking' ); ?></h4>
		<div class="modalcontentbox modal-body" id="checkout_options_html"></div>
		<div class="loader_modal">
			<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
		</div>
	</div>
</div>

<div id="service_gallery_modal" class="modaloverlay">
	<div class="modal animate__animated animate__bounceIn">
		<span class="close" onclick="closeModal('service_gallery_modal')">&times;</span>
		<h4><?php esc_html_e( 'Gallery Images', 'service-booking' ); ?></h4>
		<div class="modalcontentbox5 modal-body" id="service_gallery_images_html"></div>
		<div class="loader_modal">
			<div class="checkout-spinner-box"><div class="checkout-spinner"></div><p><?php echo esc_html__( 'Loading...', 'service-booking' ); ?></p></div>
		</div>
	</div>
</div>

<!-- <div id="leftbar-modal" class="modaloverlay mobile-modaloverlay">
	<div class="modal">
		<img class="close" onclick="mobileFilter()" src="<?php echo esc_url( plugins_url( '../img/close.svg', __FILE__ ) ); ?>" alt="Akismet" />
		<h4><?php esc_html_e( 'Select Services', 'service-booking' ); ?></h4>
		<div class="modalcontentbox5 filtermodalbox modal-body">
		
			<div class="inputgroup" 
			<?php
			if ( ! $visibility['date'] ) {
				echo "style='display: none;'";}
			?>
									>
				<label class="<?php echo empty( $services ) ? 'hidden' : ''; ?>"><?php esc_html_e( 'Select Date', 'service-booking' ); ?></label>
				<input type="date" class="textbox <?php echo empty( $services ) ? 'hidden' : ''; ?>" id="booking_date_mobile" min="<?php echo esc_html( $today ); ?>" value="<?php echo esc_html( $default_date ); ?>" onchange="bm_fetch_all_services('', 'mobile')" />
			</div>
			<div class="modal-category-services-box"
			<?php
			if ( ! $visibility['category_filter'] ) {
				echo "style='display: none;'";}
			?>
				>
				<?php if ( ! empty( $categories ) ) { ?>
					
						<label class="category_label <?php echo empty( $categories ) ? 'hidden' : ''; ?>"><?php esc_html_e( 'Categories:', 'service-booking' ); ?></label>
					
				<?php } ?>
				<div class="all_available_categories">
					<?php
					if ( ! empty( $categories ) ) {
						$i = 1;
						foreach ( $categories as $key => $category ) {
							$category_name = ( new BM_Request() )->bm_fetch_category_name_by_category_id( $category );
							?>
							<div class="categories_available">
								<input type="checkbox" name="ct_<?php echo esc_attr( $category ); ?>" id="cat_<?php echo esc_attr( $i ); ?>" onclick="bm_filter_categories(this)">
								<label for="cat_<?php echo esc_attr( $i ); ?>" title="<?php echo esc_html( $category_name ); ?>" style="font-size:<?php echo esc_attr( $cat_checkbox_txt_font ) . '!important'; ?>"><?php echo esc_html( mb_strimwidth( $category_name, 0, 24, '...' ) ); ?></label>
							</div>
							<?php
							++$i;
						}
					}
					?>
				</div>
			</div>
		
		</div>
		
	</div>
</div> -->
