<?php

/**
 * Class WooCommerceService
 */
class WooCommerceService {



    /**
     * Get WooCommerce Cart
     */
    public function bm_get_woo_commerce_cart() {
         return wc()->cart;

    }//end bm_get_woo_commerce_cart()


    /**
     * Is WooCommerce enabled
     *
     * @return boolean
     */
    public function is_enabled() {
         return class_exists( 'WooCommerce' );

    }//end is_enabled()


    /**
     * Get WooCommerce Cart URL
     */
    public function get_woo_commerce_cart_url() {
         return wc_get_cart_url();

    }//end get_woo_commerce_cart_url()


    /**
     * Get WooCommerce Checkout URL
     */
    public function get_woo_commerce_checkout_url() {
         return wc_get_checkout_url();

    }//end get_woo_commerce_checkout_url()


    /**
     * Add service booking to woocommerce cart
     *
     * @param array    $order_data
     *
     * @return boolean
     */
    public function add_to_cart( $data = array(), $flexi_order_key = '' ) {
        $wooCommerceCart = $this->bm_get_woo_commerce_cart();

        if ( !$wooCommerceCart ) {
            return false;
        }

        try {
            foreach ( $wooCommerceCart->get_cart() as $wc_key => $wc_item ) {
                $wooCommerceCart->remove_cart_item( $wc_key );
            }

            if ( !empty( $data ) && !empty( $flexi_order_key ) ) {
                $dbhandler  = new BM_DBhandler();
                $service_id = $data['service_id'] ?? 0;
                $date       = $data['booking_date'] ?? '';
                $product_id = $dbhandler->get_value( 'SERVICE', 'wc_product', $service_id, 'id' );
                $svc_price  = $data['base_svc_price'] ?? -1;

                if ( $service_id > 0 && !empty( $date ) && $product_id > 0 ) {
                    $total_service_booked = $data['total_service_booking'] ?? 0;
                    $extra_service_ids    = $data['extra_svc_booked'] ?? '';
                    $extra_slots_booked   = $data['total_extra_slots_booked'] ?? 0;

                    /** $service = $dbhandler->get_row('SERVICE', $service_id); */
                    /**if ($product_id == 0 || !in_array($service->service_name, array_column(self::get_all_products([]), 'name'))) {
                    $product_id = self::create_woo_product($service_id, $date);
                    } else if ($product_id == 0 && in_array($service->service_name, array_column(self::get_all_products([]), 'name'))) {
                    $product_id = self::get_wc_product_id_by_title($service->service_name);
                    $dbhandler->update_row('SERVICE', 'id', $service_id, ['is_linked_wc_product' => 1, 'wc_product' => $product_id], '', '%d');
                    }*/

                    $cart_item_data = array(
                        'added_by_flexibooking' => true,
                        'flexi_booking_key'     => $flexi_order_key,
                        'flexi_checkout_key'    => ( new BM_Request() )->bm_generate_unique_code( '', 'FLEXIC', 15 ),
                    );

                    if ( $svc_price != -1 ) {
                        $cart_item_data['flexi_svc_price'] = $svc_price;
                    }

                    $wooCommerceCart->add_to_cart( $product_id, $total_service_booked, 0, array(), $cart_item_data );

                    if ( !empty( $extra_service_ids ) && !empty( $extra_slots_booked ) ) {
                        $extra_slots_booked = explode( ',', $extra_slots_booked );
                        $additional         = "id in($extra_service_ids)";
                        $extras             = $dbhandler->get_all_result( 'EXTRA', '*', 1, 'results', 0, false, null, false, $additional );

                        if ( !empty( $extras ) ) {
                            foreach ( $extras as $key => $extra ) {
                                $product_id      = $extra->svcextra_wc_product ?? 0;
                                $extra_svc_price = $extra->extra_price ?? 0;

                                $cart_item_data['flexi_extra_svc_price'][ $key ] = $extra_svc_price;

                                /**if ($product_id == 0 || !in_array($extra->extra_name, array_column(self::get_all_products([]), 'name'))) {
                                $product_id = self::create_woo_product($extra->id, $date, 'extra');
                                } else if ($product_id == 0 && in_array($extra->extra_name, array_column(self::get_all_products([]), 'name'))) {
                                $product_id = self::get_wc_product_id_by_title($extra->extra_name);
                                $dbhandler->update_row('EXTRA', 'id', $extra->id, ['is_linked_wc_extrasvc' => 1, 'svcextra_wc_product' => $product_id], '', '%d');
                                }*/

                                if ( $product_id > 0 ) {
                                    $wooCommerceCart->add_to_cart( $product_id, $extra_slots_booked[ $key ], 0, array(), $cart_item_data );
                                }
                            }
                        }
                    }//end if
                    return true;
                }
            }//end if
        } catch ( Exception $e ) {
            error_log( print_r( $e->getmessage(), true ) );
            return false;
        }

        return false;

    }//end add_to_cart()


    /**
     * Create new woocommerce product if does not exist.
     *
     * @param int|null $id
     * @param string   $date
     * @param string   $type
     *
     * @return int
     */
    private static function create_woo_product( $id = 0, $date = '', $type = 'service' ) {
        if ( !empty( $id ) && !empty( $date ) ) {
            $dbhandler  = new BM_DBhandler();
            $bmrequests = new BM_Request();
            $data       = array();

            switch ( $type ) {
				case 'service':
					$service        = $dbhandler->get_row( 'SERVICE', $id );
					$category_id    = isset( $service->service_category ) && !empty( $service->service_category ) ? esc_attr( $service->service_category ) : 0;
					$category_title = $bmrequests->bm_fetch_category_name_by_service_id( $id );

					if ( !empty( $service ) ) {
						$data['image']         = isset( $service->service_image_guid ) && $service->service_image_guid !== 0 ? esc_attr( $service->service_image_guid ) : 0;
						$data['name']          = isset( $service->service_name ) && !empty( $service->service_name ) ? esc_html( $service->service_name ) : '';
						$data['slug']          = isset( $service->service_name ) && !empty( $service->service_name ) ? $bmrequests->bm_create_slug( $service->service_name ) : '';
						$data['long_desc']     = isset( $service->service_desc ) && !empty( $service->service_desc ) ? wp_kses_post( stripslashes( $service->service_desc ) ) : '';
						$data['price']         = str_replace( '&euro;', '', $bmrequests->bm_fetch_service_price_by_service_id_and_date( $service->id, $date, 'global_format' ) );
						$data['category']      = !empty( $category_title ) ? self::get_wc_product_category_id_by_title( $category_title ) : -1;
						$data['default_price'] = isset( $service->default_price ) && !empty( $service->default_price ) ? esc_attr( $service->default_price ) : 0;

						if ( $data['category'] == 0 ) {
							$data['category'] = self::create_woo_product_category( $category_id );
						}
					}
                    break;

				case 'extra':
					$extra = $dbhandler->get_row( 'EXTRA', $id );

					if ( !empty( $extra ) ) {
						$data['image']     = 0;
						$data['name']      = isset( $extra->extra_name ) && !empty( $extra->extra_name ) ? esc_html( $extra->extra_name ) : '';
						$data['slug']      = isset( $extra->extra_name ) && !empty( $extra->extra_name ) ? $bmrequests->bm_create_slug( $extra->extra_name ) : '';
						$data['long_desc'] = isset( $extra->extra_desc ) && !empty( $extra->extra_desc ) ? wp_kses_post( stripslashes( $extra->extra_desc ) ) : '';
						$data['price']     = isset( $extra->extra_price ) && !empty( $extra->extra_price ) ? esc_attr( $extra->extra_price ) : '';
						$data['category']  = 0;
					}
                    break;

				default:
                    break;
            }//end switch

            if ( !empty( $data ) ) {
                $product = new WC_Product_Simple();
                $product->set_name( $data['name'] );
                $product->set_slug( $data['slug'] );

                if ( isset( $data['default_price'] ) ) {
                    if ( $data['price'] < $data['default_price'] ) {
                        $product->set_regular_price( $data['default_price'] );
                        $product->set_sale_price( $data['price'] );
                    } else {
                        $product->set_regular_price( $data['price'] );
                    }
                } else {
                    $product->set_regular_price( $data['price'] );
                }

                $product->set_description( $data['long_desc'] );
                $product->set_image_id( $data['image'] );
                $product->set_category_ids( array( $data['category'] ) );
                $product->set_stock_status( 'instock' );
                $product->save();

                if ( !empty( $product ) ) {
                    if ( $type == 'service' ) {
                        $dbhandler->update_row(
                            'SERVICE',
                            'id',
                            $id,
                            array(
								'is_linked_wc_product' => 1,
								'wc_product'           => $product->get_id(),
                            ),
                            '',
                            '%d'
                        );
                    } elseif ( $type == 'extra' ) {
                        $dbhandler->update_row(
                            'EXTRA',
                            'id',
                            $id,
                            array(
								'is_linked_wc_extrasvc' => 1,
								'svcextra_wc_product'   => $product->get_id(),
                            ),
                            '',
                            '%d'
                        );
                    }
                }
            }//end if
        }//end if

        return isset( $product ) && !empty( $product ) ? $product->get_id() : 0;

    }//end create_woo_product()


    /**
     * Get all woocommerce products
     *
     * @param  array $params
     * @return array
     */
    private static function get_all_products( $params ) {
        $params = array_merge(
            array(
				'post_type'      => 'product',
				'posts_per_page' => -1,
            ),
            $params
        );

        $products = array();

        foreach ( get_posts( $params ) as $product ) {
            $products[] = array(
                'id'   => $product->ID,
                'name' => $product->post_title,
            );
        }

        return $products;

    }//end get_all_products()


    /**
     * Get all woocommerce product categories
     *
     * @param  array $params
     * @return array
     */
    private static function get_all_product_categories( $params ) {
        $params = array_merge( array( 'taxonomy' => 'product_cat' ), $params );

        $categories = array();

        foreach ( get_categories( $params ) as $category ) {
            $categories[] = array(
                'id'   => $category->term_id,
                'name' => $category->name,
            );
        }

        return $categories;

    }//end get_all_product_categories()


    /**
     * Create new woocommerce product category if does not exist.
     *
     * @param  int $category_id
     * @return int
     */
    private static function create_woo_product_category( $category_id = 0 ) {
        if ( !empty( $category_id ) ) {
            $dbhandler  = new BM_DBhandler();
            $bmrequests = new BM_Request();
            $category   = $dbhandler->get_row( 'CATEGORY', $category_id );

            if ( !empty( $category ) ) {
                $name = isset( $category->cat_name ) && !empty( $category->cat_name ) ? esc_html( $category->cat_name ) : '';
                $slug = !empty( $name ) ? $bmrequests->bm_create_slug( $name ) : '';

                $category = wp_insert_term(
                    $name,
                    'product_cat',
                    array(
                        'description' => '',
                        'slug'        => $slug,
                    )
                );
            }

            return isset( $category ) && !empty( $category ) ? $category->term_id : 0;
        }//end if

        return 0;

    }//end create_woo_product_category()


    /**
     * Get woocommerce product id by post title
     *
     * @param  string $title
     * @return int
     */
    private static function get_wc_product_id_by_title( $title = '' ) {
        if ( !empty( $title ) ) {
            global $wpdb;
            $post_title = strval( $title );
            $post_table = $wpdb->prefix . 'posts';

            $result = $wpdb->get_col(
                $wpdb->prepare(
                    'SELECT ID FROM %s WHERE post_title LIKE %s AND post_type LIKE %s',
                    $post_table,
                    $post_title,
                    'product'
                )
            );

            if ( empty( $result[0] ) ) {
                return 0;
            } else {
                $product = wc_get_product( intval( $result[0] ) );
                return !empty( $product ) ? $product->get_id() : 0;
            }
        }//end if

        return 0;

    }//end get_wc_product_id_by_title()


    /**
     * Get woocommerce product category id by category title
     *
     * @param  string $title
     * @return int
     */
    private static function get_wc_product_category_id_by_title( $title = '' ) {
        if ( !empty( $title ) ) {
            $result = get_term_by( 'name', $title, 'product_cat' );
            return !empty( $result ) ? $result->term_id : 0;
        }

        return 0;

    }//end get_wc_product_category_id_by_title()


    /**
     * Set woocommerce product price
     *
     * @param  int $product_id
     * @param  float $price
     */
    public function set_wc_product_regular_price( $product_id, $price ) {
        $product = wc_get_product( $product_id );

        if ( $product ) {
            $product->set_regular_price( $price );
            $product->set_price( $price );
            $product->save();
        }

    }//end set_wc_product_regular_price()


    /**
     * Set woocommerce product sale price
     *
     * @param  int $product_id
     * @param  float $price
     */
    public function set_wc_product_sale_price( $product_id, $price ) {
        $product = wc_get_product( $product_id );

        if ( $product ) {
            $product->set_sale_price( $price );
            $product->set_price( $price );
            $product->save();
        }
    }//end set_wc_product_sale_price()


}//end class
