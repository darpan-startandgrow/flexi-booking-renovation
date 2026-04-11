<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://startandgrow.in
 * @since      1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define coupon validation
 *
 * @since      1.0.0
 * @package    Booking_Management
 * @subpackage Booking_Management/includes
 * @author     Start and Grow <laravel6@startandgrow.in>
 */
class BM_Coupon_validation {

    // added by vaishali

    /**
     * Fetch coupon event type value response
     *
     * @author Darpan
     */
    public function bm_fetch_coupon_value_html( $type = '', $coupon_id = 0, $array_key = -1 ) {
        $additional_tag = '';
        if( $type == 3 ){
            $type = 0;
            $additional_tag = 'included_services';
        }
         $bmrequests = new BM_Request();
        if ( $type <= 1 ) {
            $module = $bmrequests->bm_fetch_event_module_name_from_type( $type );
        } else {
            $module = 'EMAIL';
        }
        $resp = $this->bm_fetch_trigger_condition_option_values_for_coupon( $module, $coupon_id, $array_key, $additional_tag );

        return $resp;
    } //end bm_fetch_event_type_value_html()


    /**
     * Get response for coupon condition excluded type
     *
     * @author Darpan
     */
    //added by Vaishali
    public function bm_fetch_trigger_condition_option_values_for_coupon( $module = '', $coupon_id = 0, $array_key = -1, $additional_tag = '' ) {
         $dbhandler = new BM_DBhandler();
        $resp       = '';
        $values     = array();

        switch ( $module ) {
            case ( $module == 'SERVICE' ):
                $tag      = 'excluded_services';
                $services = $dbhandler->get_all_result( $module, '*', 1, 'results' );

                if ( !empty( $services ) && is_array( $services ) ) {
                    foreach ( $services as $service ) {
                        $id   = isset( $service->id ) ? $service->id : '';
                        $name = isset( $service->service_name ) ? $service->service_name : '';

                        $values[ $id ] = mb_strimwidth( $name, 0, 30, '...' );
                    }
                }
                break;
            case ( $module == 'CATEGORY' ):
                $tag        = 'excluded_category';
                $categories = $dbhandler->get_all_result( $module, '*', 1, 'results' );

                if ( !empty( $categories ) && is_array( $categories ) ) {
                    foreach ( $categories as $category ) {
                        $id   = isset( $category->id ) ? $category->id : '';
                        $name = isset( $category->cat_name ) ? $category->cat_name : '';

                        $values[ $id ] = mb_strimwidth( $name, 0, 30, '...' );
                    }
                }
                break;
            case ( $module == 'EMAIL' ):
                $tag       = 'excluded_emails';
                $user_data = get_users();
                if ( !empty( $user_data ) && is_array( $user_data ) ) {
                    foreach ( $user_data as $user ) {
                        $id   = isset( $user->user_email ) ? $user->user_email : '';
                        $name = isset( $user->user_email ) ? $user->user_email : '';

                        $values[ $id ] = mb_strimwidth( $name, 0, 30, '...' );
                    }
                }
                break;
            default:
                break;
        } //end switch

        if ( !empty( $values ) && is_array( $values ) && array_filter( $values ) ) {
            if ( empty( $coupon_id ) ) {
                foreach ( $values as $key => $value ) {
                    $resp .= '<option value="' . $key . '">' . $value . '</option>';
                }
            } 
            elseif ( $coupon_id > 0 && $array_key >= 0 && $additional_tag == 'included_services' ) {
                $coupon    = $dbhandler->get_row( 'COUPON', $coupon_id );
                $con_values = isset( $coupon->included_services ) ? maybe_unserialize( $coupon->included_services ) : array();

                foreach ( $values as $key => $value ) {
                    $resp .= '<option value="' . $key . '" ' . ( in_array( $key, $con_values ) ? 'selected' : '' ) . '>' . $value . '</option>';
                }
            }
            elseif( $coupon_id > 0 && $array_key >= 0 && $additional_tag == '' ){
                $coupon    = $dbhandler->get_row( 'COUPON', $coupon_id );
                $conditions = isset( $coupon->excluded_conditions ) ? maybe_unserialize( $coupon->excluded_conditions ) : array();
                $con_values = isset( $conditions[ $tag ] ) ? $conditions[ $tag ] : array();

                foreach ( $values as $key => $value ) {
                    $resp .= '<option value="' . $key . '" ' . ( in_array( $key, $con_values ) ? 'selected' : '' ) . '>' . $value . '</option>';
                }
            }
        }

        return $resp;
    } //end bm_fetch_trigger_condition_option_values_for_coupon()

}
