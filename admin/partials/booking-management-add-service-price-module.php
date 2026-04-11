<?php
$identifier = 'EXTERNAL_SERVICE_PRICE_MODULE';
$dbhandler  = new BM_DBhandler();
$bmrequests = new BM_Request();
$module_id  = filter_input( INPUT_GET, 'id', FILTER_VALIDATE_INT );

if ( $module_id == false || $module_id == null ) {
    $module_id = 0;
}

if ( ( filter_input( INPUT_POST, 'submit_button' ) ) ) {
    $retrieved_nonce = filter_input( INPUT_POST, '_wpnonce' );
    if ( !wp_verify_nonce( $retrieved_nonce, 'save_module' ) ) {
        die( '<div id="errorMessage" class="bm-notice bm-error">' . esc_html__( 'Failed security check', 'service-booking' ) . '</div>' );
    }

    $exclude = array(
        '_wpnonce',
        '_wp_http_referer',
        'submit_button',
        'bm_field_list',
    );

    $module_data = $bmrequests->sanitize_request( $_POST, $identifier, $exclude );

    if ( $module_data != false ) {
        if ( empty( $module_id ) ) {
            $module_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $module_id                 = $dbhandler->insert_row( $identifier, $module_data );

            if ( !empty( $module_id ) ) {
                wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_all_external_service_prices' ) );
                exit;
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Price Module Could not be Added !!', 'service-booking' );
                echo ( '</div>' );
            }
        } elseif ( !empty( $module_id ) ) {
            $module_data['updated_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
            $updated                   = $dbhandler->update_row( $identifier, 'id', $module_id, $module_data, '', '%d' );

            if ( $updated ) {
                wp_safe_redirect( esc_url_raw( 'admin.php?page=bm_add_external_service_price&id=' . esc_attr( $module_id ) ) );
                exit;
            } else {
                echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
                echo esc_html__( 'Price Module Could not be Updated !!', 'service-booking' );
                echo ( '</div>' );
            }
        }//end if
    } else {
        echo ( '<div id="errorMessage" class="bm-notice bm-error">' );
        echo esc_html__( 'Price Module Data could not be Processed !!', 'service-booking' );
        echo ( '</div>' );
    }
}//end if

if ( !empty( $module_id ) ) {
    $price_module  = $dbhandler->get_row( $identifier, $module_id );
    $module_values = !empty( $price_module ) && isset( $price_module->module_values ) ? maybe_unserialize( $price_module->module_values ) : array();
}

?>

<div class="sg-admin-main-box">
<div class="wrap listing_table">
     <div class="row">
        <p>
        <h2 class="title" style="font-weight: bold;"><?php esc_html_e( 'External Service Price Module', 'service-booking' ); ?></h2>
        </p>
    </div>
    
    <form role="form" method="post" id="price_module_form">
        <tbody>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="module_name"><?php esc_html_e( 'Module Name', 'service-booking' ); ?></label><strong class="required_asterisk"> *</strong></th>
                    <td class="bminput bm_required">
                        <input name="module_name" type="text" id="module_name" placeholder="<?php esc_html_e( 'Module Name', 'service-booking' ); ?>" style="width:356px" value="<?php echo !empty( $price_module ) && isset( $price_module->module_name ) ? esc_html( $price_module->module_name ) : ''; ?>" autocomplete="off">
                        &nbsp;<div class="errortext"></div>
                    </td>
                </tr>
                <br />
                <table class="form-table" role="presentation" id="per_age_group_fields">
                    <?php if ( !empty( $module_id ) && isset( $module_values ) && !empty( $module_values ) ) { ?>
                        <tr>
                        <th scope="row"><label><?php esc_html_e( 'Age Wise Price Settings', 'service-booking' ); ?></label></th>
                        <?php
                        if ( isset( $module_values['age_group_name'] ) && is_array( $module_values['age_group_name'] ) && !empty( $module_values['age_group_name'] ) ) {
                            $total = count( $module_values['age_group_name'] );
                            for ( $i = 0; $i < $total; $i++ ) {
                                $groupNo      = "age_group_$i";
                                $group_name   = "module_values[age_group_name][$i]";
                                $from_name    = "module_values[age_group_from][$i]";
                                $to_name      = "module_values[age_group_to][$i]";
                                $price_name   = "module_values[age_group_price][$i]";
                                $disable_name = "module_values[age_group_disable][$i]";
                                $group_id     = "age_group_name_$i";
                                $from_id      = "age_group_from_$i";
                                $to_id        = "age_group_to_$i";
                                $price_id     = "age_group_price_$i";
                                $disable_id   = "age_group_disable_$i";
								?>
                        <td class="bm_per_age_group_values" id="<?php echo esc_html( $groupNo ); ?>">
                            <span class="bminput bm_required">
                                <input name="<?php echo esc_html( $group_name ); ?>" type="text" id="<?php echo esc_html( $group_id ); ?>" placeholder="name" class="per_age_group_input no_border wise-price-setting-input" value="<?php echo isset( $module_values['age_group_name'][ $i ] ) ? esc_html( $module_values['age_group_name'][ $i ] ) : ''; ?>" onfocus="blur();" autocomplete="off" readonly>
                                &nbsp;&nbsp;<span class="errortext"></span>
                            </span>
                            <span class="bminput bm_required">
                                <input name="<?php echo esc_html( $from_name ); ?>" type="number" id="<?php echo esc_html( $from_id ); ?>" placeholder="from" min="0" max="" class="per_age_group_input no_border" value="<?php echo isset( $module_values['age_group_from'][ $i ] ) ? esc_html( $module_values['age_group_from'][ $i ] ) : ''; ?>" onfocus="blur();" autocomplete="off" readonly>
                                &nbsp;&nbsp;<span class="errortext"></span>
                            </span>
                            <span><?php esc_html_e( '-', 'service-booking' ); ?></span>
                            <span class="bminput bm_required bm_per_group_option">
                                <input name="<?php echo esc_html( $to_name ); ?>" type="number" id="<?php echo esc_html( $to_id ); ?>" placeholder="to" min="<?php echo isset( $module_values['age_group_from'][ $i ] ) ? esc_html( $module_values['age_group_from'][ $i ] ) : 0; ?>" class="per_age_group_input" value="<?php echo isset( $module_values['age_group_to'][ $i ] ) ? esc_html( $module_values['age_group_to'][ $i ] ) : ''; ?>" onchange="checkAgeGroupFromValue(this)" autocomplete="off" <?php echo isset( $module_values['age_group_disable'][ $i ] ) && $module_values['age_group_disable'][ $i ] == '1' ? 'readonly' : ''; ?>>
                                &nbsp;&nbsp;<span class="errortext"></span>
                            </span>&nbsp;&nbsp;
                            <span class="bminput bm_required bm_per_group_option">
                                <input name="<?php echo esc_html( $price_name ); ?>" type="text" id="<?php echo esc_html( $price_id ); ?>" placeholder="<?php esc_html_e( 'price', 'service-booking' ); ?>" class="per_age_group_input per_age_group_price_input" value="<?php echo isset( $module_values['age_group_price'][ $i ] ) ? esc_html( $module_values['age_group_price'][ $i ] ) : ''; ?>" autocomplete="off" <?php echo isset( $module_values['age_group_disable'][ $i ] ) && $module_values['age_group_disable'][ $i ] == '1' ? 'readonly' : ''; ?>>
                                &nbsp;<span class="errortext"></span>
                            </span>
                            <span>
                                <input type="hidden" name="<?php echo esc_html( $disable_name ); ?>" value="0">
                                <input type="checkbox" name="<?php echo esc_html( $disable_name ); ?>" id="<?php echo esc_html( $disable_id ); ?>" value="1" onchange="disablePerAgeGroup(this)" <?php echo isset( $module_values['age_group_disable'][ $i ] ) ? checked( esc_attr( $module_values['age_group_disable'][ $i ] ), 1 ) : ''; ?>>&nbsp;<?php esc_html_e( 'Disable ?', 'service-booking' ); ?>
                            </span>
								<?php if ( $i == 0 ) { ?>
                            <span class="price_module_info_text">
									<?php esc_html_e( 'Define prices for different age groups. If this module is linked with that service, these prices will be considered for that service on top of its default price/day specific price. you can diable a group if you don\'t want the price for a specific age group to be considered', 'service-booking' ); ?>
                            </span>
                            <?php } ?>
                        </td>
								<?php
							}
                            ?>
                            </tr>
                            <?php
						}
					}
					?>
                </table>
                <br />
                <table class="form-table" role="presentation" id="per_group_fields">
                <?php
                if ( isset( $module_values ) && !empty( $module_values ) ) {
                    if ( isset( $module_values['group_from'] ) && is_array( $module_values['group_from'] ) && !empty( $module_values['group_from'] ) ) {
                        ?>
                        <tr>
                        <th scope="row"><label><?php esc_html_e( 'Group Wise Price Settings', 'service-booking' ); ?></label></th>
                        <?php
                        $total = count( $module_values['group_from'] );
						for ( $i = 0; $i < $total; $i++ ) {
							$groupNo      = "option_$i";
							$from_name    = "module_values[group_from][$i]";
							$to_name      = "module_values[group_to][$i]";
							$price_name   = "module_values[group_price][$i]";
                            $disable_name = "module_values[group_disable][$i]";
							$from_id      = "group_from_$i";
							$to_id        = "group_to_$i";
							$price_id     = "group_price_$i";
                            $remove_id    = "group_remove_$i";
                            $disable_id   = "group_disable_$i";
							?>
                        <td class="bm_per_group_values" id="<?php echo esc_html( $groupNo ); ?>">
                            <span class="bminput bm_required">
                                <input name="<?php echo esc_html( $from_name ); ?>" type="number" id="<?php echo esc_html( $from_id ); ?>" placeholder="<?php esc_html_e( 'from', 'service-booking' ); ?>" min="1" class="per_group_input no_border" value="<?php echo isset( $module_values['group_from'][ $i ] ) ? esc_html( $module_values['group_from'][ $i ] ) : ''; ?>" onfocus="blur();" autocomplete="off" readonly>
                                &nbsp;&nbsp;<span class="errortext"></span>
                            </span>
                            <span><?php esc_html_e( '-', 'service-booking' ); ?></span>
                            <span class="bminput bm_required bm_per_group_option">
                                <input name="<?php echo esc_html( $to_name ); ?>" type="number" id="<?php echo esc_html( $to_id ); ?>" placeholder="<?php esc_html_e( 'to', 'service-booking' ); ?>" min="1" class="per_group_input" value="<?php echo isset( $module_values['group_to'][ $i ] ) ? esc_html( $module_values['group_to'][ $i ] ) : 20; ?>" onchange="checkGroupFromValue(this)" autocomplete="off" <?php echo isset( $module_values['group_disable'][ $i ] ) &&  $module_values['group_disable'][ $i ] == '1' ? 'readonly' : ''; ?>>
                                &nbsp;<span class="errortext"></span>
                            </span>
                            <span class="bminput bm_required bm_per_group_option">
                                <input name="<?php echo esc_html( $price_name ); ?>" type="text" id="<?php echo esc_html( $price_id ); ?>" placeholder="<?php esc_html_e( 'price', 'service-booking' ); ?>" class="per_group_input per_group_price_input" value="<?php echo isset( $module_values['group_price'][ $i ] ) ? esc_html( $module_values['group_price'][ $i ] ) : ''; ?>" autocomplete="off" <?php echo isset( $module_values['group_disable'][ $i ] ) &&  $module_values['group_disable'][ $i ] == '1' ? 'readonly' : ''; ?>>
                                &nbsp;<span class="errortext"></span>
                            </span>
                            <span>
                                <input type="hidden" name="<?php echo esc_html( $disable_name ); ?>" value="0">
                                <input type="checkbox" name="<?php echo esc_html( $disable_name ); ?>" id="<?php echo esc_html( $disable_id ); ?>" value="1" onchange="disablePerGroup(this)" <?php echo isset( $module_values['group_disable'][ $i ] ) ? checked( esc_attr( $module_values['group_disable'][ $i ] ), 1 ) : ''; ?>>&nbsp;<?php esc_html_e( 'Disable ?', 'service-booking' ); ?>
                            </span>
                            <?php if ( !empty( $i ) ) { ?>
                                &nbsp;<span class="bm_remove_shop_admin_email_field no_left_space" id="<?php echo esc_html( $remove_id ); ?>" title="<?php esc_html_e( 'Remove', 'service-booking' ); ?>" onclick="bm_remove_per_group_option(this)">
                                    <?php echo esc_html( '✕' ); ?>
                                </span>&nbsp;&nbsp;
                            <?php } ?>
                            <?php if ( empty( $i ) ) { ?>
                            &nbsp;&nbsp;<span class="price_module_info_text">
								<?php esc_html_e( 'Define prices for different groups. These prices are only for adult and senior age groups and will be considered only if the booked service has persons belonging to these age groups', 'service-booking' ); ?>
                            </span>
                            <?php } ?>
                        </td>
							<?php
						}
						?>
                        <td class="add_per_group_option_class">
                            <a href="javascript:void(0)" onClick="bm_add_price_group()"><?php esc_html_e( 'add additional group', 'service-booking' ); ?></a>
                        </td>
                        <tr>
                        <?php
					}
				} else {
					?>
                    <tr>
                        <th scope="row"><label><?php esc_html_e( 'Group Wise Price Settings', 'service-booking' ); ?></label></th>
                        <td class="bm_per_group_values" id="option_0">
                            <span class="bminput bm_required">
                                <input name="module_values[group_from][0]" type="number" id="group_from_0" placeholder="<?php esc_html_e( 'from', 'service-booking' ); ?>" min="1" class="per_group_input no_border" value="1" onfocus="blur();" autocomplete="off" readonly>
                                &nbsp;&nbsp;<span class="errortext"></span>
                            </span>
                            <span><?php esc_html_e( '-', 'service-booking' ); ?></span>
                            <span class="bminput bm_required bm_per_group_option">
                                <input name="module_values[group_to][0]" type="number" id="group_to_0" placeholder="<?php esc_html_e( 'to', 'service-booking' ); ?>" min="1" class="per_group_input" value="20" onchange="checkGroupFromValue(this)" autocomplete="off">
                                &nbsp;<span class="errortext"></span>
                            </span>
                            <span class="bminput bm_required bm_per_group_option">
                                <input name="module_values[group_price][0]" type="text" id="group_price_0" placeholder="<?php esc_html_e( 'price', 'service-booking' ); ?>" class="per_group_input per_group_price_input" value="" autocomplete="off">
                                &nbsp;<span class="errortext"></span>
                            </span>
                            <span>
                                <input type="hidden" name="module_values[group_disable][0]" value="0">
                                <input type="checkbox" name="module_values[group_disable][0]" id="group_disable_0" value="1" onchange="disablePerGroup(this)">&nbsp;<?php esc_html_e( 'Disable ?', 'service-booking' ); ?>
                            </span>
                            <span class="price_module_info_text">
							    <?php esc_html_e( 'Define prices for different groups. These prices are only for adult and senior age groups and will be considered only if the booked service has persons belonging to these age groups', 'service-booking' ); ?>
                            </span>
                        </td>
                        <td class="add_per_group_option_class">
                            <a href="javascript:void(0)" onClick="bm_add_price_group()"><?php esc_html_e( 'add additional group', 'service-booking' ); ?></a>
                        </td>
                    </tr>
                    <?php
				}
				?>
                </table>
            </table>
            <div class="row">
                <p class="submit">
                    <?php wp_nonce_field( 'save_module' ); ?>
                    <a href="admin.php?page=bm_all_external_service_prices" class="button">&#8592; &nbsp;<?php esc_attr_e( 'Back', 'service-booking' ); ?></a>
                    <input type="submit" name="submit_button" id="submit_button" class="button button-primary" value="<?php esc_attr_e( 'Save', 'service-booking' ); ?>" onclick="return external_price_module_validation()">
                </p>
            </div>
        </tbody>
    </form>
</div>

<div class="loader_modal"></div>
</div>

