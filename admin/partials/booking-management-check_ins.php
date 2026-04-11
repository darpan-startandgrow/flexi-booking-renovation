<?php
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();
$pagenum    = filter_input( INPUT_GET, 'pagenum' );
$pagenum    = isset( $pagenum ) ? absint( $pagenum ) : 1;
$limit      = !empty( $dbhandler->get_global_option_value( 'bm_checkins_per_page' ) ) ? $dbhandler->get_global_option_value( 'bm_checkins_per_page' ) : 10;
$offset     = ( ( $pagenum - 1 ) * $limit );
$i          = ( 1 + $offset );
$checkins   = $bmrequests->bm_fetch_all_order_checkins();
$total      = !empty( $checkins ) && is_array( $checkins ) ? count( $checkins ) : 0;
$user_id    = get_current_user_id();

$num_of_pages      = ceil( $total / $limit );
$pagination        = $dbhandler->bm_get_pagination( $num_of_pages, $pagenum, $bmrequests->bm_get_page_url(), 'list' );
$active_columns    = $bmrequests->bm_fetch_active_columns( 'checkin' );
$column_values     = $bmrequests->bm_fetch_column_order_and_names( 'checkin' );
$plugin_path       = plugin_dir_url( __FILE__ );
$unique_services   = array();
$added_service_ids = array();

$resend_email_content = array(
    'wpautop'           => false,
    'media_buttons'     => true,
    'textarea_name'     => 'resend_email_body',
    'textarea_rows'     => 20,
    'tabindex'          => 4,
    'editor_height'     => 150,
    'tabfocus_elements' => ':prev,:next',
    'editor_css'        => '',
    'editor_class'      => '',
    'teeny'             => false,
    'dfw'               => false,
    'tinymce'           => true,
    'quicktags'         => true,
);

add_action( 'media_buttons', array( $this, 'bm_fields_list_for_email' ) );

?>

<!-- Check ins -->
<div class="sg-admin-main-box checkin-listing-admin-main-box">
<div class="wrap listing_table">
    <div class="checkin_listing_top">
        <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Check Ins', 'service-booking' ); ?></h2>
        <?php
        if ( isset( $checkins ) && !empty( $checkins ) ) {
            foreach ( $checkins as $checkin ) {
				if ( !in_array( $checkin['service_id'], $added_service_ids, true ) ) {
					$unique_services[]   = array(
						'service_id'   => $checkin['service_id'],
						'service_name' => $checkin['service_name'],
					);
					$added_service_ids[] = $checkin['service_id'];
				}
			}
			?>
            <button id="manual-checkin-btn" class="button button-primary">
                <span class="dashicons dashicons-scanner"></span><?php esc_html_e( 'Manual Checkin', 'service-booking' ); ?>
            </button>
            <button id="ticket-scanner-btn" class="button button-primary">
                <span class="dashicons dashicons-scanner"></span><?php esc_html_e( 'Ticket Scanner', 'service-booking' ); ?>
            </button>

            <span style="margin-left: 5px;">
                <a href="javascript:void(0);" class="button button-primary" title="<?php esc_html_e( 'Advanced search', 'service-booking' ); ?>" onclick="bm_show_search_box('checkin_advanced_search_box')"><?php esc_html_e( 'Advanced search', 'service-booking' ); ?>&nbsp;<i class="fa fa-search" aria-hidden="true"></i></a>
            </span>
       
            <div class="sg-filter-bar" style="float: right; height: 45px;">
                <span class="tab-box">
                    <span class="inputgroup sg-search-box" style="position: relative;">
                        <input type="text" id="checkin_global_search" class="textbox" placeholder="<?php esc_html_e( 'Search', 'service-booking' ); ?>" autocomplete="off" />
                        <i class="fa fa-search checkin_listing_search_icon" id="checkin_listing_search_icon" data-title="<?php esc_html_e( 'Click to search', 'service-booking' ); ?>"></i>
                    </span>
                </span>
                <a href="javascript:void(0);" class="button button-primary edit_checkin_columns" title="<?php esc_html_e( 'Manage Columns', 'service-booking' ); ?>">
                    <span>
                        <?php esc_html_e( 'Manage Columns', 'service-booking' ); ?>
                        <i class="fa fa-plus" aria-hidden="true" style="color:#fff;"></i>
                    </span>
                </a>
                <a href="javascript:void(0);" class="button button-primary export_checkin_records" title="<?php esc_html_e( 'Csv Export', 'service-booking' ); ?>">
                    <span>
                        <?php esc_html_e( 'Csv Export', 'service-booking' ); ?>
                        <img src="<?php echo esc_url( $plugin_path . 'images/export.png' ); ?>" class="options" alt="options" width="15px" height="15px" style="position:relative;top:3px;">
                    </span>
                </a>
            </div> 

            <div class="checkin_advanced_search_box" id="checkin_advanced_search_box" style="display: none;">
                <span class="service_date_search_span">
                    <span>
                        <input type="text" id="checkin_service_from" name="checkin_service_from" placeholder='<?php esc_html_e( 'from service date', 'service-booking' ); ?>' title="<?php esc_html_e( 'from service date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                    <span>
                        <input type="text" id="checkin_service_to" name="checkin_service_to" placeholder='<?php esc_html_e( 'to service date', 'service-booking' ); ?>' title="<?php esc_html_e( 'to service date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                </span>
                <span class="checkin_date_search_span">
                    <span>
                        <input type="text" id="checkin_from" name="checkin_from" placeholder='<?php esc_html_e( 'from checkin date', 'service-booking' ); ?>' title="<?php esc_html_e( 'from checkin date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                    <span>
                        <input type="text" id="checkin_to" name="checkin_to" placeholder='<?php esc_html_e( 'to checkin date', 'service-booking' ); ?>' title="<?php esc_html_e( 'to checkin date', 'service-booking' ); ?>" autocomplete="off">
                        <i class="fa fa-calendar calendar-fa"></i>
                    </span>
                    <span class="service_filter_span">
                        <select id="checkin_service_advanced_filter" multiple="multiple">
                            <?php
                            foreach ( $unique_services as $unique_service ) {
                                echo '<option value="' . esc_attr( $unique_service['service_id'] ) . '">' . esc_html( $unique_service['service_name'] ) . '</option>';
                            }
                            ?>
                        </select>
                    </span>
                    <button type="button" class="button button-primary" id="checkin_date_search_button" title="<?php esc_html_e( 'Search', 'service-booking' ); ?>">
                        <i class="fa fa-search"></i>
                    </button>
                    <button type="button" class="button" id="reset_date_search" title="<?php esc_html_e( 'Reset', 'service-booking' ); ?>">
                        <i class="fa fa-refresh"></i>
                    </button>
                </span>
            </div>
			<?php
        }//end if
        ?>
    </div>
    <?php
    if ( isset( $checkins ) && !empty( $checkins ) ) {
		;
		?>
        <table class="wp-list-table widefat striped" id="checkin_listing">
            <thead>
                <tr>
                    <?php
                    if ( !empty( $column_values ) ) {
                        foreach ( $column_values as $key => $column ) {
                            if ( isset( $active_columns ) && !in_array( $key, $active_columns ) ) {
                                continue;
                            }
                            ?>
                            <th style="text-align: center;font-weight: 600;"><?php echo esc_html( $key ); ?></th>
							<?php
                        }
                    } else {
						?>
                        <th width="10%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Serial No', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ordered Service', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Service Date', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Attendee First name', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Attendee Last name', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Attendee Contact', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Attendee Email', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Checkin Time', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Checkin Status', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Order Cost', 'service-booking' ); ?></th>
                        <th style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Ticket PDF', 'service-booking' ); ?></th>
                        <th width="25%" style="text-align: center;font-weight: 600;"><?php esc_html_e( 'Actions', 'service-booking' ); ?></th>
                        <?php
                    }//end if
                    ?>
                </tr>
            </thead>
            <tbody class="checkin_records"></tbody>
        </table>
        <div id="checkin_pagination">
						<?php echo wp_kses_post( $pagination ?? '' ); ?>
        </div>
    <?php } else { ?>
        <div class="bm_no_records_message">
            <div class="Pointer">
                <p class="message"><?php esc_html_e( 'No Checkins Found', 'service-booking' ); ?></p>
            </div>
        </div>
			<?php
    }//end if
    ?>
</div>

<input type="hidden" name="pagenum" id="pagenum" value="<?php echo esc_attr( $pagenum ); ?>" />
<input type="hidden" name="limit_count" id="limit_count" value="<?php echo esc_attr( $limit ); ?>" />
<input type="hidden" id="total_pages" value="<?php echo esc_attr( $num_of_pages ); ?>" />
<input type="hidden" id="user_id" value="<?php echo esc_attr( $user_id ); ?>" />

<div id="checkin-attachments-dialog" title="<?php esc_html_e( 'Order Ticket', 'service-booking' ); ?>" style="display: none;">
    <ul id="ticket-list"></ul>
</div>

<div id="checkin_columns_modal" class="modaloverlay">
    <div class="modal managecheckinboxmodal animate__animated animate__flipInX">
        <span class="close" onclick="closeModal('checkin_columns_modal')">&times;</span>
        <h4 style="background:#5EA8ED ; margin:0px; padding:12px;color:#fff;font-size:16px;"><?php esc_html_e( 'Select Columns', 'service-booking' ); ?></h4>
        <div class="modalcontentbox2 managecheckinbox modal-body" id="checkin_columns"></div>
        <div class="bookbtnbar">
            <div class="bookbtn bgcolor textwhite text-center" id="checkin_column_button">
                <a href="#" id="column_button_tag" class="submit_columns"><?php esc_html_e( 'Save', 'service-booking' ); ?></a>
            </div>
        </div>
        <div class="column_errortext" style="display :none;"></div>
    </div>
</div>

<div id="checkin_export_modal" class="modaloverlay2">
    <div class="modal animate__animated animate__flipInX">
        <span class="close" onclick="closeModal('checkin_export_modal')">&times;</span>
        <h2 style="background:#5EA8ED ; margin:0px; padding:12px;color:#fff;font-size:18px;text-align: center;"><?php esc_html_e( 'Export Checkins', 'service-booking' ); ?></h2>
        <div class="modalcontentbox modal-body" id="export_checkin"></div>
        <div style="margin-bottom:10px;text-align:center;">
            <button type="button" class="button-primary" id="checkinexportButton">
                <span id="buttonText">
                    <?php esc_html_e( 'Export', 'service-booking' ); ?>
                </span>
            </button>
            <div id="checkinresendProcess" class="hidden">
                <img src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">
            </div>
        </div>
    </div>
</div>


<div id="manual_checkin-modal" class="checkin-default-modal" style="display:none;">
    <div class="modal-content animate__animated animate__flipInX">
        <div class="fx-modal-header">
            <span class="close">&times;</span>
            <h2 class="fx-manual_checkin-heading"><?php esc_html_e( 'Manual Checkin', 'service-booking' ); ?></h2>
        </div>
        <div class="manual_checkin-container">
            <select id="manual_checkin_type">
                <option value="last_name" selected><?php esc_html_e( 'Search by Last Name', 'service-booking' ); ?></option>
                <option value="email"><?php esc_html_e( 'Search by Email', 'service-booking' ); ?></option>
                <option value="reference"><?php esc_html_e( 'Search by Reference Number', 'service-booking' ); ?></option>
                <option value="service"><?php esc_html_e( 'Search by Service', 'service-booking' ); ?></option>
            </select>

            <input type="text" id="manual_checkin_lastname" class="checkin-input" placeholder="<?php esc_html_e( 'Enter Last Name', 'service-booking' ); ?>" autocomplete="off" />
            <input type="email" id="manual_checkin_email" class="checkin-input hidden" placeholder="<?php esc_html_e( 'Enter Email', 'service-booking' ); ?>" autocomplete="off" />
            <input type="text" id="manual_checkin_reference" class="checkin-input hidden" placeholder="<?php esc_html_e( 'Enter Booking Reference Number', 'service-booking' ); ?>" autocomplete="off" />
            <span id="manual_checkin_service_span" class="select-checkin-input hidden">
                <select id="manual_checkin_service" multiple="multiple">
                    <?php
                    foreach ( $unique_services as $unique_service ) {
                        echo '<option value="' . esc_attr( $unique_service['service_id'] ) . '">' . esc_html( $unique_service['service_name'] ) . '</option>';
                    }
                    ?>
                </select>
            </span>

            <button id="manual-checkin-search" class="button-primary"><?php esc_html_e( 'Search', 'service-booking' ); ?></button>
            <div id="manual_checkin-error"></div>
            <div id="manual_checkin-result"></div>
        </div>
        <div class="manual-cherckin-buttons hidden">
            <button type="button" class="button-primary manual-checkin-button" id="manual-checkin-button" onclick="bm_checkin_manually()"><?php esc_html_e( 'Checkin', 'service-booking' ); ?></button>
            <div id="resendProcess" class="hidden">
                <img id="resend_loader" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">
            </div>
            <button type="button" class="button manual-cancel-button" id="manual-cancel-button"><?php esc_html_e( 'Cancel', 'service-booking' ); ?></button>
        </div>
    </div>
</div>

<div id="scanner-modal" class="checkin-default-modal" style="display:none;">
    <div class="modal-content animate__animated animate__flipInX">
        <span class="close">&times;</span>
        <h2><?php esc_html_e( 'Ticket Scanner', 'service-booking' ); ?></h2>
        <div class="scanner-container">
            <video id="scanner-video" width="100%" playsinline></video>
            <canvas id="scanner-canvas" style="display:none;"></canvas>
            <div id="scanner-result"></div>
        </div>
        <div class="scanner-controls">
            <button id="start-scan" class="button button-primary"><?php esc_html_e( 'Start Scan', 'service-booking' ); ?></button>
            <button id="stop-scan" class="button"><?php esc_html_e( 'Stop Scan', 'service-booking' ); ?></button>
        </div>
    </div>
</div>

<div id="order-details-modal" class="checkin-default-modal" style="display:none;">
    <div class="modal-content" style="max-width:450px;">
        <span class="close">&times;</span>
        <div id="order-details-content"></div>
    </div>
</div>

<div id="checkin-order-details-modal" class="modaloverlay">
    <div class="modal animate__animated animate__flipInX">
        <span class="close" onclick="closeModal('checkin-order-details-modal')">&times;</span>
        <h2>&nbsp;&nbsp;<?php esc_html_e( 'Checkin Details', 'service-booking' ); ?></h2>
        <div class="modalcontentbox modal-body checkin-order-details-container"></div>
        <div class="loader_modal"></div>
    </div>
</div>

<div id="resend_email_modal" class="modaloverlay">
    <div class="modal animate__animated animate__swing">
        <span style="color:#000;" class="close" onclick="closeModal('resend_email_modal')">&times;</span>
        <div class="modalcontentbox3 modal-body" id="resend_email">
            <div class="email-container">
                <div class="header">
                    <h2><?php esc_html_e( 'Resend mail', 'service-booking' ); ?></h2>
                </div>
                <div class="email-content-box">
                    <div class="input-group">
                        <label for="resend_mail_to"><?php esc_html_e( 'To:', 'service-booking' ); ?></label>
                        <input type="text" id="resend_mail_to" val="" title="<?php esc_html_e( 'add single or comma separated emails', 'service-booking' ); ?>">
                    </div>
                    <div class="input-group">
                        <label for="resend_mail_subject"><?php esc_html_e( 'Subject:', 'service-booking' ); ?></label>
                        <input type="text"  id="resend_mail_subject" val="">
                        <div class="cc-bcc-buttons">
                            <button type="button" onclick="bm_remove_hidden_class('cc-input')"><?php esc_html_e( 'CC', 'service-booking' ); ?></button>
                            <button type="button" onclick="bm_remove_hidden_class('bcc-input')"><?php esc_html_e( 'BCC', 'service-booking' ); ?></button>
                        </div>
                    </div>
                    <div class="input-group hidden" id="cc-input">
                        <label for="mail_cc"><?php esc_html_e( 'Cc:', 'service-booking' ); ?></label>
                        <input type="text" id="mail_cc" val="" title="<?php esc_html_e( 'add single or comma separated emails', 'service-booking' ); ?>">
                    </div>
                    <div class="input-group hidden" id="bcc-input">
                        <label for="mail_bcc"><?php esc_html_e( 'Bcc:', 'service-booking' ); ?></label>
                        <input type="text" id="mail_bcc" val="" title="<?php esc_html_e( 'add single or comma separated emails', 'service-booking' ); ?>">
                    </div>
                    <div class="input-group">
                        <?php wp_editor( '', 'resend_email_body', $resend_email_content ); ?>
                    </div>
                    <div class="progress" style="display: none;">
                        <div class="progress-bar" role="progressbar" style="width:0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"><?php echo esc_html( '0%' ); ?></div>
                    </div>
                    <div id="fileList" style="display: none;"></div>
                    <input type="hidden" id="resend_email_attachment" value="">
                    <input type="hidden" id="final_files" value="">
                </div>
                <div class="attachment-send-group">
                    <div class="attachments">
                        <div class="attachment">
                            <label for="email_attachment">
                                <img src="<?php echo esc_url( $plugin_path . 'images/attach.png' ); ?>" alt="<?php esc_html_e( 'attachment', 'service-booking' ); ?>" title="<?php esc_html_e( 'Add attachment', 'service-booking' ); ?>">
                            </label>
                            <input id="email_attachment" name="email_attachment[]" type="file" multiple class="hidden" onclick="this.value = null"/>
                        </div>
                        <!-- <div class="attachment">
                            <label for="image-upload">
                                <img src="<?php echo esc_url( $plugin_path . 'images/image-upload.png' ); ?>" alt="attachment">
                            </label>
                            <input type="file" id="image-upload" name="image-upload" accept="image/*" class="imageupload hidden">
                        </div> -->
                    </div>
                    <button type="button" class="send-button resend-button" id="resend-button" onclick="bm_resend_email('checkin')"><?php esc_html_e( 'Send', 'service-booking' ); ?></button>
                    <div id="resendProcess" class="hidden">
                        <img id="resend_loader" src="<?php echo esc_url( $plugin_path . 'images/ajax-loader.gif' ); ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="popup-message-overlay" id="popup-message-overlay"></div>
<div class="popup-message-container animate__animated animate__flipInX" id="popup-message-container">
    <span id="popup-message"></span>
    <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
</div>

<div class="loader_modal"></div>
</div>

