<?php
$identifier            = 'EVENTNOTIFICATION';
$dbhandler             = new BM_DBhandler();
$bmrequests            = new BM_Request();
$notification_event_id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );
$language              = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );

if ( $notification_event_id == false || $notification_event_id == null ) {
    $notification_event_id = 0;
}

if ( filter_input( INPUT_POST, 'save_event' ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_event_data' ) ) {
        die( esc_html__( 'Failed security check', 'service-booking' ) );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'save_event',
    );

    if ( filter_input( INPUT_POST, 'save_event' ) ) {
        $is_condition = isset( $_POST['is_condition'] ) ? true : false;
        $is_offset    = isset( $_POST['is_timeoffset'] ) ? true : false;

        if ( !$is_condition ) {
            $_POST['trigger_conditions'] = null;
        } else {
            $event_trigger_conditions = filter_input( INPUT_POST, 'trigger_conditions', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

            $_POST['trigger_conditions']['type']     = array_values( $event_trigger_conditions['type'] );
            $_POST['trigger_conditions']['operator'] = array_values( $event_trigger_conditions['operator'] );
            $_POST['trigger_conditions']['values']   = array_values( $event_trigger_conditions['values'] );
        }

        if ( !$is_offset ) {
            $_POST['time_offset'] = null;
        }

        unset( $_POST['is_condition'] );
        unset( $_POST['is_timeoffset'] );

        $event_data = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

        if ( $event_data != false ) {
            $current_type   = isset( $event_data['type'] ) ? $event_data['type'] : -1;
            $current_status = isset( $event_data['status'] ) ? $event_data['status'] : -1;
            $active_type    = $bmrequests->bm_check_active_process_of_a_specific_type( $current_type );

            if ( empty( $notification_event_id ) ) {
                if ( $current_status == 1 && $active_type ) {
                    echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                    echo esc_html__( 'There is already an active process for this type, please deactivate the existing process.', 'service-booking' );
                    echo ( '</div>' );
                } else {
                    $event_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
                    $notification_event_id    = $dbhandler->insert_row( $identifier, $event_data );

                    if ( !empty( $notification_event_id ) ) {
                        wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_all_notification_processes' ) );
                        exit;
                    } else {
                        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                        echo esc_html__( 'Process could not be saved !!', 'service-booking' );
                        echo ( '</div>' );
                    }
                }
            } elseif ( $notification_event_id > 0 ) {
                $active_process_id = $bmrequests->bm_fetch_active_process_id_of_a_specific_type( $current_type );

                if ( ( $current_status == 1 ) && $active_type && ( $active_process_id > 0 ) && ( $active_process_id != $notification_event_id ) ) {
                    echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                    echo esc_html__( 'There is already an active process for this type, please deactivate the existing process.', 'service-booking' );
                    echo ( '</div>' );
                } else {
                    $event_data['updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
                    $dbhandler->update_row( $identifier, 'id', $notification_event_id, $event_data, '', '%d' );
                    wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_notification_process&id=' . esc_attr( $notification_event_id ) ) );
                    exit;
                }
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Something is wrong, try again !!', 'service-booking' );
                echo ( '</div>' );
            }
        } else {
            echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
            echo esc_html__( 'Process could not be saved !!', 'service-booking' );
            echo ( '</div>' );
        }
    }
} //end if


if ( !empty( $notification_event_id ) ) {
    $event       = $dbhandler->get_row( $identifier, $notification_event_id );
    $conditions  = isset( $event->trigger_conditions ) ? maybe_unserialize( $event->trigger_conditions ) : array();
    $time_offset = isset( $event->time_offset ) ? maybe_unserialize( $event->time_offset ) : array();
}

$templates = $dbhandler->get_all_result( 'EMAIL_TMPL', '*', 1, 'results' );

?>

<div class="sg-admin-main-box">
    <div class="wrap">
        <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'Email Notification Process Settings', 'service-booking' ); ?></h2>
        <form role="form" method="post" id="notification_process_form">
            <tbody>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row"><label for="status"><?php esc_html_e( 'Status', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <select name="status" id="status" class="regular-text">
                                <option value="1" <?php isset( $event ) && isset( $event->status ) ? selected( $event->status, '1' ) : ''; ?>><?php esc_html_e( 'Active', 'service-booking' ); ?></option>
                                <option value="0" <?php isset( $event ) && isset( $event->status ) ? selected( $event->status, '0' ) : ''; ?>><?php esc_html_e( 'Inactive', 'service-booking' ); ?></option>
                            </select>
                            <span><?php esc_html_e( 'Status of this event', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row"><label for="name"><?php esc_html_e( 'Name', 'service-booking' ); ?><strong class="required_asterisk"> *</strong></label></th>
                        <td class="bminput bm_required">
                            <input name="name" type="text" id="name" placeholder="name" class="regular-text" value="<?php echo isset( $event ) && isset( $event->name ) ? esc_html( $event->name ) : ''; ?>" autocomplete="off">
                            <span><?php esc_html_e( 'Name of this event', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row"><label for="type"><?php esc_html_e( 'Event type', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                        <td class="bminput bm_required">
                            <select name="type" id="type" class="regular-text">
                                <option value="0" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '0' ) : ''; ?>><?php esc_html_e( 'New order (frontend)', 'service-booking' ); ?></option>
                                <option value="1" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '1' ) : ''; ?>><?php esc_html_e( 'New order (backend)', 'service-booking' ); ?></option>
                                <option value="7" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '7' ) : ''; ?>><?php esc_html_e( 'New request (frontend)', 'service-booking' ); ?></option>
                                <option value="8" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '8' ) : ''; ?>><?php esc_html_e( 'New request (backend)', 'service-booking' ); ?></option>
                                <option value="2" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '2' ) : ''; ?>><?php esc_html_e( 'Order refund', 'service-booking' ); ?></option>
                                <option value="3" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '3' ) : ''; ?>><?php esc_html_e( 'Order cancel', 'service-booking' ); ?></option>
                                <option value="4" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '4' ) : ''; ?>><?php esc_html_e( 'Order approval', 'service-booking' ); ?></option>
                                <option value="5" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '5' ) : ''; ?>><?php esc_html_e( 'Failed order', 'service-booking' ); ?></option>
                                <option value="6" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '6' ) : ''; ?>><?php esc_html_e( 'Gift voucher', 'service-booking' ); ?></option>
                                <option value="9" <?php isset( $event ) && isset( $event->type ) ? selected( $event->type, '9' ) : ''; ?>><?php esc_html_e( 'Redeem voucher', 'service-booking' ); ?></option>
                            </select>
                            <span> <?php esc_html_e( 'Type of this event', 'service-booking' ); ?></span>
                            <div class="errortext"></div>
                        </td>

                    </tr>
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Conditions', 'service-booking' ); ?></th>
                        <td class="bm-checkbox-td">
                            <input name="is_condition" type="checkbox" id="is_condition" class="regular-text bm_toggle" <?php echo isset( $conditions ) && !empty( $conditions ) && array_filter( $conditions ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'conditional_content' )">
                            <label for="is_condition"></label>
                        </td>
                    </tr>
                    <table class="form-table" role="presentation" id="conditional_content"
                        <?php
                        if ( isset( $conditions ) && !empty( $conditions ) && array_filter( $conditions ) ) {
                            echo "style='display:inline-table;'";
                        }
                        ?>
                        >

                        <tr>
                            <th scope="row">
                                <label><?php esc_html_e( 'Trigger if', 'service-booking' ); ?></label>
                            </th>
                            <?php
                            if ( isset( $conditions ) && !empty( $conditions ) && array_filter( $conditions ) && is_array( $conditions ) ) {
                                $total = isset( $conditions['type'] ) && is_array( $conditions['type'] ) ? count( $conditions['type'] ) : 0;
                                for ( $i = 0; $i < $total; $i++ ) {
                                    $condition_type     = isset( $conditions['type'][ $i ] ) ? $conditions['type'][ $i ] : '';
                                    $condition_operator = isset( $conditions['operator'][ $i ] ) ? $conditions['operator'][ $i ] : '';
                                    $condition_values   = $bmrequests->bm_fetch_event_type_value_html( $condition_type, $notification_event_id, $i );
									?>
                                    <td id="condition_field_<?php echo esc_attr( $i ); ?>" class="condition_field">
                                        <div id="trigger_condition_div" class="bminput bm_required">
                                            <div style="width:2%; float:left;">
                                                <button type="button" class="bm_remove_event_condition" id="remove_condition_<?php echo esc_attr( $i ); ?>" onclick="bm_remove_condition_box(this)"><i class="fa fa-remove"></i></button>
                                            </div>
                                            <div class="multi-select-input-box">
                                                <?php echo ( $i == 0 ) ? '<label>' . esc_html_e( 'Type', 'service-booking' ) . '</label><br/>' : ''; ?>
                                                <select name="trigger_conditions[type][<?php echo esc_attr( $i ); ?>]" id="condition_type_<?php echo esc_attr( $i ); ?>" onchange="bm_fetch_event_condition_value(this)" class="regular-text emailselect">
                                                    <option value="0" <?php isset( $condition_type ) ? selected( $condition_type, '0' ) : ''; ?>><?php esc_html_e( 'Service', 'service-booking' ); ?></option>
                                                    <option value="1" <?php isset( $condition_type ) ? selected( $condition_type, '1' ) : ''; ?>><?php esc_html_e( 'Category', 'service-booking' ); ?></option>
                                                    <option value="2" <?php isset( $condition_type ) ? selected( $condition_type, '2' ) : ''; ?>><?php esc_html_e( 'Order status', 'service-booking' ); ?></option>
                                                    <option value="3" <?php isset( $condition_type ) ? selected( $condition_type, '3' ) : ''; ?>><?php esc_html_e( 'Payment status', 'service-booking' ); ?></option>
                                                </select>
                                            </div>
                                            <div class="multi-select-input-box">
                                                <?php echo ( $i == 0 ) ? '<label>' . esc_html_e( 'Operator', 'service-booking' ) . '</label><br/>' : ''; ?>
                                                <select name="trigger_conditions[operator][<?php echo esc_attr( $i ); ?>]" id="condition_operator_<?php echo esc_attr( $i ); ?>" class="regular-text emailselect">
                                                    <option value="1" <?php isset( $condition_operator ) ? selected( $condition_operator, '1' ) : ''; ?>><?php esc_html_e( 'Equal to', 'service-booking' ); ?></option>
                                                    <option value="0" <?php isset( $condition_operator ) ? selected( $condition_operator, '0' ) : ''; ?>><?php esc_html_e( 'Not equal to', 'service-booking' ); ?></option>
                                                </select>
                                            </div>
                                            <div class="multi-select-input-box">
                                                <?php echo ( $i == 0 ) ? '<label>' . esc_html_e( 'Values', 'service-booking' ) . '</label><br/>' : ''; ?>
                                                <select name="trigger_conditions[values][<?php echo esc_attr( $i ); ?>][]" id="condition_values_<?php echo esc_attr( $i ); ?>" class="notification-multiselect" style="width:300px;" multiple="multiple">
                                                    <?php echo wp_kses( $condition_values, $bmrequests->bm_fetch_expanded_allowed_tags() ); ?>
                                                </select>
                                            </div>
                                            <div class="errortext"></div>
                                        </div>
                                    </td>
									<?php
                                }
                            } else {
                                ?>
                                <td id="condition_field_0" class="condition_field">
                                    <div id="trigger_condition_div" class="bminput bm_required">
                                        <div style="width:2%; float:left;">
                                            <button type="button" class="bm_remove_event_condition" id="remove_condition_0" onclick="bm_remove_condition_box(this)"><i class="fa fa-remove"></i></button>
                                        </div>
                                        <div class="multi-select-input-box">
                                            <label><?php esc_html_e( 'Type', 'service-booking' ); ?></label><br />
                                            <select name="trigger_conditions[type][0]" id="condition_type_0" class="regular-text emailselect" onchange="bm_fetch_event_condition_value(this)">
                                                <option value="0"><?php esc_html_e( 'Service', 'service-booking' ); ?></option>
                                                <option value="1"><?php esc_html_e( 'Category', 'service-booking' ); ?></option>
                                                <option value="2"><?php esc_html_e( 'Order status', 'service-booking' ); ?></option>
                                                <option value="3"><?php esc_html_e( 'Payment status', 'service-booking' ); ?></option>
                                            </select>
                                        </div>
                                        <div class="multi-select-input-box">
                                            <label><?php esc_html_e( 'Operator', 'service-booking' ); ?></label><br />
                                            <select name="trigger_conditions[operator][0]" id="condition_operator_0" class="regular-text emailselect">
                                                <option value="1"><?php esc_html_e( 'Equal to', 'service-booking' ); ?></option>
                                                <option value="0"><?php esc_html_e( 'Not equal to', 'service-booking' ); ?></option>
                                            </select>
                                        </div>
                                        <div class="multi-select-input-box">
                                            <label><?php esc_html_e( 'Values', 'service-booking' ); ?></label><br />
                                            <select name="trigger_conditions[values][0][]" id="condition_values_0" class="notification-multiselect" style="width:300px;" multiple="multiple"></select>
                                        </div>
                                        <div class="errortext"></div>
                                    </div>
                                </td>
                            <?php } ?>
                            <td class="add_trigger_conditions_class">
                                <button type="button" class="add_condition_box_btn" onclick="bm_add_condition_box()">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                    <span><?php esc_html_e( 'AND', 'service-booking' ); ?></span>
                                </button>
                            </td>
                            <td style="position:relative;right:135px; width:200px;top:12px;vertical-align: top;">
                                <?php esc_html_e( 'Set conditions for this event', 'service-booking' ); ?>
                            </td>
                        </tr>
                    </table>


                    <table class="form-table" role="presentation">

                        <tr>
                            <th scope="row"><?php esc_html_e( 'Time offset', 'service-booking' ); ?></th>
                            <td class="bm-checkbox-td">
                                <input name="is_timeoffset" type="checkbox" id="is_timeoffset" class="regular-text bm_toggle" <?php echo isset( $time_offset ) && !empty( $time_offset ) && array_filter( $time_offset ) ? 'checked' : ''; ?> onclick="bm_open_close_tab( 'offset_content' )">
                                <label for="is_timeoffset"></label>
                            </td>
                        </tr>
                    </table>
                    <table class="form-table" role="presentation" id="offset_content"
                        <?php
                        if ( isset( $time_offset ) && !empty( $time_offset ) && array_filter( $time_offset ) ) {
                            echo "style='display:block;'";
                        }
                        ?>
                        >

                        <tr style="position:relative;">
                            <th scope="row">
                                <label><?php esc_html_e( 'Time offset settings', 'service-booking' ); ?></label>
                            </th>
                            <td id="offset_field" class="offset_field" style="width:79%">
                                <?php $class1 = empty( $notification_event_id ) ? 'add_offset' : 'edit_offset'; ?>
                                <div class="<?php echo esc_html( $class1 ); ?>">
                                    <label><?php esc_html_e( 'Time', 'service-booking' ); ?></label><br />
                                    <input name="time_offset[value]" type="number" min="1" id="time_offset_value" placeholder="value" style="width:100%;" value="<?php echo isset( $time_offset['value'] ) ? esc_attr( $time_offset['value'] ) : 1; ?>" class="regular-text" autocomplete="off">
                                </div>
                                <?php $class2 = empty( $notification_event_id ) ? 'add_offset_interval' : 'edit_offset_interval'; ?>
                                <div class="<?php echo esc_html( $class2 ); ?>">
                                    <label><?php esc_html_e( 'Interval', 'service-booking' ); ?></label><br />
                                    <select name="time_offset[unit]" id="time_offset_unit" class="regular-text" style="vertical-align:baseline; width:100%; min-width:inherit !important; max-width:inherit !important;">
                                        <option value="0" <?php isset( $time_offset['unit'] ) ? selected( $time_offset['unit'], '0' ) : ''; ?>><?php esc_html_e( 'minutes', 'service-booking' ); ?></option>
                                        <option value="1" <?php isset( $time_offset['unit'] ) ? selected( $time_offset['unit'], '1' ) : ''; ?>><?php esc_html_e( 'hours', 'service-booking' ); ?></option>
                                        <option value="2" <?php isset( $time_offset['unit'] ) ? selected( $time_offset['unit'], '2' ) : ''; ?>><?php esc_html_e( 'days', 'service-booking' ); ?></option>
                                    </select>
                                </div>
                                <?php $class3 = empty( $notification_event_id ) ? 'add_offset_type' : 'edit_offset_type'; ?>
                                <div class="<?php echo esc_html( $class3 ); ?>">
                                    <label><?php esc_html_e( 'Event Type', 'service-booking' ); ?></label><br />
                                    <select name="time_offset[position]" id="time_offset_position" class="regular-text" style="vertical-align:baseline; width:100%; min-width:inherit !important; max-width:inherit !important;">
                                        <option value="1" <?php isset( $time_offset['position'] ) ? selected( $time_offset['position'], '1' ) : ''; ?>><?php esc_html_e( 'after the event', 'service-booking' ); ?></option>
                                        <!-- <option value="0" <?php isset( $time_offset['position'] ) ? selected( $time_offset['position'], '0' ) : ''; ?>><?php esc_html_e( 'before the event', 'service-booking' ); ?></option> -->
                                    </select>
                                </div>
                            </td>
                            <td style="position:absolute;right:170px; top:25px;">
                                <?php esc_html_e( 'Set time offsets for this event', 'service-booking' ); ?>
                            </td>
                        </tr>
                    </table>

                <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="template_id"><?php esc_html_e( 'Template', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                    <td class="bminput bm_required">
                        <select name="template_id" id="template_id" class="regular-text">
                            <?php
                            if ( !empty( $templates ) ) {
                                foreach ( $templates as $template ) {
                                    $tmpl_name = "tmpl_name_$language";
									?>
                                    <option value="<?php echo esc_attr( $template->id ); ?>" <?php isset( $event->template_id ) ? selected( $event->template_id, esc_attr( $template->id ) ) : ''; ?>><?php echo esc_html( $template->$tmpl_name ); ?></option>
									<?php
                                }
                            }
                            ?>
                        </select>
                        <span> <?php esc_html_e( 'Email template for this event.', 'service-booking' ); ?></span>
                        <div class="errortext"></div>
                    </td>
                   
                </tr>
               </table>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_event_data' ); ?>
                    <a href="admin.php?page=bm_all_notification_processes" class="button button-secondary">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="save_event" id="save_event" class="button button-primary" value="<?php empty( $notification_event_id ) ? esc_attr_e( 'Save', 'service-booking' ) : esc_attr_e( 'Update', 'service-booking' ); ?>" onclick="return add_process_form_validation()">
                </p>
            </div>
        </tbody>
    </form>
</div>

    <div class="popup-message-overlay" id="popup-message-overlay"></div>
    <div class="popup-message-container" id="popup-message-container">
        <span id="popup-message"></span>
        <button class="close-popup-message" id="close-popup-message" title="<?php esc_html_e( 'Close', 'service-booking' ); ?>"><?php echo esc_html( '✕' ); ?></button>
    </div>

    <div class="loader_modal"></div>
</div>
