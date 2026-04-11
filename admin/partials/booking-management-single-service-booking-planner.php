<?php

$dbhandler      = new BM_DBhandler();
$bmrequests     = new BM_Request();
$cat_identifier = 'CATEGORY';
$svc_identifier = 'SERVICE';

$cat_ids       = isset( $cat_ids ) ? $cat_ids : '';
$cat_ids_array = $cat_ids != '' ? array_map( 'intval', explode( ',', $cat_ids ) ) : array();

$date_label_font       = $dbhandler->get_global_option_value( 'bm_date_field_label_font', '20' ) . 'px';
$cat_search_label_font = $dbhandler->get_global_option_value( 'bm_category_search_label_font', '20' ) . 'px';
$cat_checkbox_txt_font = $dbhandler->get_global_option_value( 'bm_category_checkbox_label_font', '14' ) . 'px';

$where_conditions = array(
    's.is_service_front' => array( '=' => 1 ),
    's.service_status'   => array( '=' => 1 ),
);

$additional = '';
if ( !empty( $cat_ids_array ) ) {
    if ( in_array( 0, $cat_ids_array ) ) {
        $where_conditions['s.service_category'] = array(
            'IN' => $cat_ids_array,
            'OR' => array( '=' => 0 ),
        );
        $additional                             = 'OR s.service_category = 0';
    } else {
        $where_conditions['s.service_category'] = array( 'IN' => $cat_ids_array );
        $where_conditions['c.cat_status']       = array( '=' => 1 );
    }
} else {
    $where_conditions['c.cat_status'] = array( '=' => 1 );
    $additional                       = 'OR s.service_category = 0';
}

$services = $dbhandler->get_results_with_join(
    array( $svc_identifier, 's' ),
    's.id, s.service_name, s.service_calendar_title, s.service_category, s.service_duration, s.default_price, s.service_desc, s.service_position',
    array(
        array(
            'table' => $cat_identifier,
            'alias' => 'c',
            'on'    => 's.service_category = c.id',
            'type'  => 'LEFT',
        ),
    ),
    $where_conditions,
    'results',
    0,
    false,
    's.service_position',
    false,
    $additional
);

$categories = !empty( $services ) ? array_values( array_unique( array_column( $services, 'service_category' ) ) ) : array();

$timezone       = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
$now            = new DateTime( 'now', new DateTimeZone( $timezone ) );
$start_date_obj = clone $now;
$end_date_obj   = clone $now;
$today          = $start_date_obj->format( 'Y-m-d' );
$end_date_obj->modify( '+6 days' );
$interval   = new DateInterval( 'P1D' );
$date_range = new DatePeriod( $start_date_obj, $interval, $end_date_obj );

$calendar_events = array();
if ( !empty( $services ) && is_array( $services ) ) {
    foreach ( $services as $service ) {
        foreach ( $date_range as $date ) {
            $current_date = $date->format( 'Y-m-d' );

            $has_slots = !empty(
                $bmrequests->bm_fetch_service_time_slot_array_by_service_id(
                    array(
						'id'   => $service->id,
						'date' => $current_date,
					)
                )
            );

            $is_past_date = $current_date < $today;
            $event_class  = $is_past_date ? 'past-date-event' : '';

            if ( $bmrequests->bm_service_is_bookable( $service->id, $current_date ) && $has_slots ) {
                $category_name = $service->service_category ?
                    $bmrequests->bm_fetch_category_name_by_category_id( $service->service_category ) :
                    __( 'Uncategorized', 'service-booking' );

                $calendar_events[] = array(
                    'id'             => $service->id,
                    'title'          => $service->service_name,
                    'calendar_title' => $service->service_calendar_title,
                    'start'          => $current_date,
                    'allDay'         => true,
                    'className'      => $event_class,
                    'extendedProps'  => array(
                        'duration'         => $bmrequests->bm_fetch_float_to_time_string( $service->service_duration ),
                        'price'            => esc_html( $bmrequests->bm_fetch_service_price_by_service_id_and_date( $service->id, $current_date, 'global_format' ) ),
                        'category'         => $service->service_category,
                        'service_position' => $service->service_position,
                        'categoryName'     => $category_name,
                        'full_desc'        => isset( $service->service_desc ) && !empty( $service->service_desc ) ? wp_kses_post( stripslashes( $service->service_desc ) ) : '',
                        'image'            => esc_url( $bmrequests->bm_fetch_image_url_or_guid( $service->id, 'SERVICE', 'url' ) ),
                        'date'             => $current_date,
                        'serviceId'        => $service->id,
                        'isPastDate'       => $is_past_date,
                    ),
                );
            }
        }
    }
}

$calendar_events_json = json_encode( $calendar_events ?: array() )
?>


<div class="calendar-container calendar-container-main-box">
    <div class="filter-container">
        <!-- Date Range Picker -->
        <div class="date-picker" id="dateRangePicker">
        <i class="fa fa-calendar"></i>
        <span id="dateRangeText"></span>
        <i class="fa fa-chevron-down"></i>
        </div>
        <?php if ( $show_filters ) : ?>
        <div class="selectservicecontainer">
        <!-- Services Dropdown -->
            <?php if ( $show_service_filter ) : ?>
        <div class="filter-dropdown-container">
            <select name="search_fullcalendar_by_service[]" id="search_fullcalendar_by_service" class="filter-dropdown" multiple="multiple">
                <?php
                if ( !empty( $services ) ) {
                    foreach ( $services as $service ) {
                        ?>
                <option value="<?php echo esc_attr( $service->id ); ?>"><?php echo isset( $service->service_name ) ? esc_html( $service->service_name ) : ''; ?></option>
                        <?php
                    }
                }
                ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- Category Dropdown -->
            <?php if ( $show_category_filter ) : ?>
            <div class="filter-dropdown-container">
                <select name="search_fullcalendar_by_category[]" id="search_fullcalendar_by_category" class="filter-dropdown" multiple="multiple">
                <?php
                if ( !empty( $categories ) ) {
                    foreach ( $categories as $key => $category ) {
                        $category_name = $bmrequests->bm_fetch_category_name_by_category_id( $category );
                        ?>
                <option value="<?php echo esc_attr( $category ); ?>"><?php echo esc_html( $category_name ); ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
    <div id="calendar"></div>
  </div>
  <div class="loader_modal"></div>
</div>

<div id="time_slot_modal" class="modaloverlay slot-details-modal">
    <div class="modal animate__animated animate__bounceIn">
        <div class="modal-header">
            <span class="close" onclick="closeModal('time_slot_modal')">&times;</span>
            <h4><?php apply_filters( 'service_planner_modal_heading', esc_html_e( 'Slot and Reservation Details', 'service-booking' ) ); ?></h4>
        </div>
        <div class="slot-details-modal-content">
            <div class="modalcontentbox slot_box_modal modal-body" id="single_planner_slot_details"></div>
            <div class="slottablecontainer"></div>
            <div class="loader_modal"></div>
        </div>
    </div>   
</div>

<div class="loader_modal"></div>

<script>
    window.bmCalendarConfig = {
        singlePlannerinitialStart: '<?php echo $now->format( 'Y-m-d' ); ?>',
        singlePlannerinitialEnd: '<?php echo $end_date_obj->format( 'Y-m-d' ); ?>',
        singlePlanneEvents: <?php echo $calendar_events_json; ?>,
        singlePannerCat_ids: '<?php echo isset( $cat_ids ) ? $cat_ids : ''; ?>'
    };
</script>
