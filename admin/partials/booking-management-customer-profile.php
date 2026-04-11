<?php
$customer_id = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );

if ( empty( $customer_id ) || is_null( $customer_id ) ) {
    return;
}

echo do_shortcode( "[sgbm_customer_profile id='$customer_id']" );


