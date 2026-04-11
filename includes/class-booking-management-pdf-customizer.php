<?php

if ( !class_exists( 'Dompdf\Dompdf' ) ) {
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'src/dompdf/vendor/autoload.php';
}

use Dompdf\Dompdf as Dompdf;

class BM_PDF_Processor {

    private $dbhandler;
    private $bmrequests;

    public function __construct() {
        $this->dbhandler  = new BM_DBhandler();
        $this->bmrequests = new BM_Request();
    }

    /**
     * Main function to get PDF content as HTML string
     */
    public function bm_get_template_pdf_content( $type, $booking_id, $customer = false ) {
        $language   = $this->dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
        if( $customer ) {
            $order    = $this->dbhandler->get_row( 'BOOKING', $booking_id, 'id' );
            $trp_lang = get_option( 'trp_lang_' . $order->booking_key, false );
            $language = ! empty( $trp_lang ) ? $trp_lang : $language;
        }else {
            $back_lang = $this->dbhandler->get_global_option_value( 'bm_flexi_current_language_backend', '' );
            $language  = ! empty( $back_lang ) ? $back_lang : $language;
        }
        $pdf_record = $this->dbhandler->get_row( 'PDF_CUSTOMIZATION', 1, 'id' );

        switch ( $type ) {
            case 'voucher':
                $template_key = 'voucher_pdf_' . $language;
                break;
            case 'customer_info':
                $template_key = 'customer_info_pdf_' . $language;
                break;
            case 'booking':
            default:
                $template_key = 'booking_pdf_' . $language;
                break;
        }

        $template_content = isset( $pdf_record->$template_key ) ? $pdf_record->$template_key : '';

        // If template is empty, fetch fallback
        if ( empty( $template_content ) ) {
            $template_content = $this->get_fallback_template( $type );
        }

        // Wrap in full HTML document if missing
        $template_content = $this->wrap_html( $template_content );

        // Replace all placeholders dynamically
        return $this->replace_all_placeholders( $template_content, $booking_id, $type );
    }

    /**
     * Unified method to replace ALL placeholders efficiently
     */
    private function replace_all_placeholders( $content, $booking_id_or_key, $type ) {
        if ( ! preg_match_all( '/\{\{([^}]+)\}\}/', $content, $matches ) ) {
            return $content;
        }

        $placeholders = array_unique( $matches[1] );

        // Check if we are generating a dummy preview
        $is_dummy        = ( $booking_id_or_key === 'dummy' );
        $is_failed_order = !is_numeric( $booking_id_or_key ) && !$is_dummy;

        $billing_data      = array();
        $price_module_data = array();
        $productDetails    = array();
        $booking           = null;
        $date_time         = new DateTime( 'now', new DateTimeZone( wp_timezone_string() ) );

        // 1. Fetch or Mock Data Based on Context
        if ( $is_dummy ) {
            // --- DUMMY DATA FOR PREVIEW ---
            $billing_data      = array(
                'billing_first_name' => 'John',
                'billing_last_name'  => 'Doe',
                'billing_email'      => 'john.doe@example.com',
                'billing_phone'      => '+1 234 567 8900',
                'billing_address_1'  => '123 Preview Street',
                'billing_city'       => 'Sample City',
                'billing_state'      => 'Test State',
                'billing_country'    => 'US',
            );
            $price_module_data = array(
                'infant'   => array(
					'total'          => 1,
					'total_discount' => 10,
				),
                'children' => array(
					'total'          => 2,
					'total_discount' => 20,
				),
                'adult'    => array(
					'total'          => 2,
					'total_discount' => 0,
				),
                'senior'   => array(
					'total'          => 1,
					'total_discount' => 15,
				),
            );
            $productDetails    = array(
                'products' => array(
                    array(
                        'base_price' => 100,
                        'quantity'   => 1,
                        'total'      => 100,
                    ),
                ),
            );
        } elseif ( $is_failed_order ) {
            // --- FAILED ORDER DATA ---
            $failed_order = $this->dbhandler->get_row( 'FAILED_TRANSACTIONS', $booking_id_or_key, 'booking_key' );
            if ( $failed_order ) {
                $customer_data = isset( $failed_order->customer_data ) ? maybe_unserialize( $failed_order->customer_data ) : array();
                $billing_data  = $customer_data['billing_details'] ?? array();
                $shipping_data = $customer_data['shipping_details'] ?? array();

                if ( !empty( $shipping_data ) ) {
                    $billing_data = array_merge( $billing_data, $shipping_data );
                }
            }

            $timezone  = $this->dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
            $date_time = ( $failed_order && isset( $failed_order->created_at ) )
                ? new DateTime( $failed_order->created_at, new DateTimeZone( $timezone ) )
                : new DateTime( 'now', new DateTimeZone( $timezone ) );

        } else {
            // --- SUCCESSFUL ORDER DATA ---
            $booking = $this->dbhandler->get_row( 'BOOKING', $booking_id_or_key, 'id' );
            if ( $booking ) {
                $customer          = $this->dbhandler->get_row( 'CUSTOMERS', $booking->customer_id, 'id' );
                $billing_data      = isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : array();
                $price_module_data = !empty( $booking->price_module_data ) ? maybe_unserialize( $booking->price_module_data ) : array();
            }

            $timezone  = $this->dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
            $date_time = ( $booking && isset( $booking->booking_created_at ) )
                ? new DateTime( $booking->booking_created_at, new DateTimeZone( $timezone ) )
                : new DateTime( 'now', new DateTimeZone( $timezone ) );

            $productDetails = $this->bmrequests->bm_fetch_product_info_order_details_page( $booking_id_or_key );
        }

        // 2. Map Data to Placeholders
        $replacements = array();
        foreach ( $placeholders as $tag ) {
            $value = '';

            // Handle existing email placeholder logic (for real orders)
            if ( !$is_dummy ) {
                if ( $is_failed_order ) {
                    $email_val = $this->bmrequests->bm_replace_field_values_in_email_body_for_orders_with_no_order_id( $tag, $booking_id_or_key );
                } else {
                    $email_val = $this->bmrequests->bm_replace_field_values_in_email_body_for_orders_with_order_id( $tag, (int) $booking_id_or_key );
                }

                if ( !empty( $email_val ) && $email_val !== $tag ) {
                    if ( $tag === 'booking_key' && !$is_failed_order ) {
                        $decoded = base64_decode( $email_val );
                        $value   = $decoded !== false ? $decoded : $email_val;
                    } else {
                        $value = $email_val;
                    }
                }
            } else {
                // Hardcode standard email dummy values for the preview
                switch ( $tag ) {
                    case 'booking_key':
                        $value = 'PREVIEW-123456';
                        break;
                    case 'service_name':
                        $value = 'Sample Premium Service';
                        break;
                    case 'booking_date':
                        $value = $date_time->format( 'd F Y' );
                        break;
                    case 'booking_slots':
                        $value = '10:00 AM - 11:00 AM';
                        break;
                    case 'service_duration':
                        $value = '1 Hour';
                        break;
                    case 'payment_method':
                        $value = 'Credit Card';
                        break;
                    case 'subtotal':
                        $value = $this->bmrequests->bm_fetch_price_in_global_settings_format( 100, true );
                        break;
                    case 'disount_amount':
                        $value = $this->bmrequests->bm_fetch_price_in_global_settings_format( 10, true );
                        break;
                    case 'total_cost':
                        $value = $this->bmrequests->bm_fetch_price_in_global_settings_format( 90, true );
                        break;
                    case 'voucher_code':
                        $value = 'VOUCH-PREV-789';
                        break;
                    case 'admin_email':
                        $value = get_option( 'admin_email' );
                        break;
                    case 'coupons':
                        $value = 'SUMMER10';
                        break;
                    case 'extra_services':
                        $price = $this->bmrequests->bm_fetch_price_in_global_settings_format( 50, true );
                        $value = '<tr>
                                    <td colspan="4" style="padding: 10px; border: 1px solid #ddd; background: #fafafa;">
                                        <strong>Extra Services:</strong>
                                        <ul style="margin: 5px 0 0 15px; padding: 0;">
                                            <li>Photography x 1 = ' . $price . '</li>
                                        </ul>
                                    </td>
                                  </tr>';
                        break;
                }
            }

            // 3. Fallback to PDF-specific mapping (Applies to BOTH real and dummy data)
            if ( empty( $value ) ) {
                switch ( $tag ) {
                    // Customer & Billing info
                    case 'billing_first_name':
                        $value = $billing_data['billing_first_name'] ?? '';
                        break;
                    case 'billing_last_name':
                        $value = $billing_data['billing_last_name'] ?? '';
                        break;
                    case 'billing_email':
                        $value = $billing_data['billing_email'] ?? ( $customer->customer_email ?? '' );
                        break;
                    case 'billing_phone':
                        $value = $billing_data['billing_contact'] ?? '';
                        break;
                    case 'billing_address':
                        $address_parts = array_filter(
                            array(
								$billing_data['billing_address_1'] ?? '',
								$billing_data['billing_city'] ?? '',
								$billing_data['billing_state'] ?? '',
								$billing_data['billing_country'] ?? '',
                            )
                        );
                        $value         = implode( ', ', $address_parts );
                        break;

                    // Account Info
                    case 'customer_since':
                        $value = ( isset( $customer ) && isset( $customer->customer_created_at ) ) ? $this->bmrequests->bm_month_year_date_format( $customer->customer_created_at ) : '';
                        break;
                    case 'total_bookings':
                        $value = ( isset( $customer ) && isset( $customer->id ) ) ? $this->dbhandler->bm_count( 'BOOKING', array( 'customer_id' => $customer->id ) ) : '0';
                        break;

                    // Service Details
                    case 'service_qty':
                        $value = $this->get_service_quantity( $productDetails );
                        break;
                    case 'service_price':
                        $value = $this->get_service_price( $productDetails );
                        break;
                    case 'service_total':
                        $value = $this->get_service_total( $productDetails );
                        break;
                    case 'payment_method':
                        if ( $booking ) {
                            $transaction = $this->dbhandler->get_row( 'TRANSACTIONS', $booking->id, 'booking_id' );
                            $value       = $transaction->payment_method ?? 'N/A';
                        }
                        break;

                    // Discounts & Modules
                    case 'infant_count':
                        $value = $price_module_data['infant']['total'] ?? 0;
                        break;
                    case 'infant_discount':
                        $value = isset( $price_module_data['infant']['total_discount'] ) ? $this->bmrequests->bm_fetch_price_in_global_settings_format( $price_module_data['infant']['total_discount'], true ) : '0';
                        break;
                    case 'child_count':
                        $value = $price_module_data['children']['total'] ?? 0;
                        break;
                    case 'child_discount':
                        $value = isset( $price_module_data['children']['total_discount'] ) ? $this->bmrequests->bm_fetch_price_in_global_settings_format( $price_module_data['children']['total_discount'], true ) : '0';
                        break;
                    case 'adult_count':
                        $value = $price_module_data['adult']['total'] ?? 0;
                        break;
                    case 'adult_discount':
                        $value = isset( $price_module_data['adult']['total_discount'] ) ? $this->bmrequests->bm_fetch_price_in_global_settings_format( $price_module_data['adult']['total_discount'], true ) : '0';
                        break;
                    case 'senior_count':
                        $value = $price_module_data['senior']['total'] ?? 0;
                        break;
                    case 'senior_discount':
                        $value = isset( $price_module_data['senior']['total_discount'] ) ? $this->bmrequests->bm_fetch_price_in_global_settings_format( $price_module_data['senior']['total_discount'], true ) : '0';
                        break;

                    // Date & Time
                    case 'date_time':
                        $value = $date_time->format( 'd/m/Y H:i' );
                        break;
                    case 'date':
                    case 'current_date':
                        $value = $date_time->format( 'd/m/Y' );
                        break;
                    case 'time':
                    case 'current_time':
                        $value = $date_time->format( 'H:i' );
                        break;
                    case 'current_year':
                        $value = $date_time->format( 'Y' );
                        break;
                    case 'redeemed_date':
                        $value = $is_dummy ? $date_time->format( 'd/m/Y' ) : ( ( $booking ) ? $this->bmrequests->bm_month_year_date_format( $booking->booking_date ) : '' );
                        break;

                    // Media & Global
                    case 'logo':
                        $logo_url = $this->bmrequests->bm_fetch_image_url_or_guid( 1, 'PDF_CUSTOMIZATION', 'url' );
                        if ( $logo_url ) {
                            $image_data = @file_get_contents( $logo_url );
                            if ( $image_data ) {
                                $base64 = base64_encode( $image_data );
                                $mime   = wp_check_filetype( $logo_url )['type'] ?: 'image/jpeg';
                                $value  = '<img src="data:' . $mime . ';base64,' . $base64 . '" style="max-width: 200px; max-height: 100px;">';
                            } else {
                                $value = '<img src="' . esc_url( $logo_url ) . '" style="max-width: 200px; max-height: 100px;">';
                            }
                        }
                        break;
                    case 'qr_code':
                        $qr_key = $is_dummy ? 'PREVIEW-123456' : ( $is_failed_order ? $booking_id_or_key : ( $booking->booking_key ?? '' ) );
                        $value  = $qr_key ? '<img src="data:image/png;base64,' . $this->generate_qr_code( $qr_key ) . '" width="100">' : '';
                        break;
                    case 'admin_phone':
                        $value = $this->dbhandler->get_global_option_value( 'bm_admin_phone', '+1 800 555 1234' );
                        break;

                    default:
                        $value = ''; // Clean empty tags
                }
            }
            $replacements[ '{{' . $tag . '}}' ] = $value;
        }

        return str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
    }

    /**
     * Centralized function to generate and save PDF to file using Dompdf
     */
    private function save_pdf_to_file( $html, $directory, $filename ) {
        if ( ! file_exists( $directory ) ) {
            wp_mkdir_p( $directory );
        }

        $filepath = trailingslashit( $directory ) . $filename;

        // TEMPORARY FIX: Suppress PHP 8.1+ Deprecation notices so they don't corrupt the PDF stream
        $original_error_reporting = error_reporting();
        error_reporting( $original_error_reporting & ~E_DEPRECATED & ~E_USER_DEPRECATED );

        try {
            $dompdf = new Dompdf();
            $dompdf->set_option( 'isHtml5ParserEnabled', true );
            $dompdf->set_option( 'isRemoteEnabled', true );

            $dompdf->loadHtml( $html );
            $dompdf->setPaper( 'A4', 'portrait' );
            $dompdf->render();

            file_put_contents( $filepath, $dompdf->output() );
        } finally {
            error_reporting( $original_error_reporting );
        }

        return $filepath;
    }

    /**
     * Wrap HTML in full document structure to ensure CSS compatibility
     */
    private function wrap_html( $html ) {
        if ( strpos( $html, '<!DOCTYPE' ) === false && strpos( $html, '<html' ) === false ) {
            return '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; color: #333; line-height: 1.6; }
                    table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
                    th { background-color: #4A90E2; color: #fff; text-align: center; padding: 12px 15px; }
                </style>
            </head>
            <body>' . $html . '</body>
            </html>';
        }
        return $html;
    }

    // --- PDF Generators for specific contexts ---

    public function generate_booking_pdf( $booking_id, $is_failed = false, $is_customer = false ) {
        $html      = $this->bm_get_template_pdf_content( 'booking', $booking_id, $is_customer );
        $folder    = $is_failed ? 'failed-order-mail' : 'new-mail';
        $directory = plugin_dir_path( dirname( __FILE__ ) ) . 'src/mail-attachments/' . $folder . '/order-details/';
        return $this->save_pdf_to_file( $html, $directory, 'order-details-booking-' . $booking_id . '.pdf' );
    }

    public function generate_voucher_pdf( $booking_id, $is_customer = true ) {
        $html      = $this->bm_get_template_pdf_content( 'voucher', $booking_id, $is_customer );
        $directory = plugin_dir_path( dirname( __FILE__ ) ) . 'src/mail-attachments/voucher-pdf/';
        return $this->save_pdf_to_file( $html, $directory, 'voucher-' . $booking_id . '.pdf' );
    }

    public function generate_customer_info_pdf( $booking_id, $is_customer = false ) {
        $html      = $this->bm_get_template_pdf_content( 'customer_info', $booking_id, $is_customer );
        $directory = plugin_dir_path( dirname( __FILE__ ) ) . 'src/mail-attachments/new-mail/customer-details/';
        return $this->save_pdf_to_file( $html, $directory, 'customer-details-booking-' . $booking_id . '.pdf' );
    }

    public function generate_failed_customer_info_pdf( $booking_key, $is_customer = false ) {
        $html      = $this->bm_get_template_pdf_content( 'customer_info', $booking_key, $is_customer );
        $directory = plugin_dir_path( dirname( __FILE__ ) ) . 'src/mail-attachments/failed-order-mail/customer-details/';
        return $this->save_pdf_to_file( $html, $directory, 'customer-details-booking-' . $booking_key . '.pdf' );
    }

    // --- Helper Methods ---

    /**
     * Provides robust, beautifully styled fallback templates for Dompdf
     * in case the database records are empty or missing.
    */
	private function get_fallback_template( $type ) {
		switch ( $type ) {
			case 'voucher':
				return '
            <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px dashed #4A90E2;">
                <div style="text-align: center; margin-bottom: 20px;">
                    {{logo}}
                </div>
                
                <h1 style="color: #4A90E2; text-align: center; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
                    SERVICE VOUCHER
                </h1>
                
                <div style="text-align: center; margin: 30px 0; font-size: 18px;">
                    <strong>VOUCHER CODE:</strong><br>
                    <span style="font-size: 24px; font-weight: bold; color: #27AE60;">{{voucher_code}}</span>
                </div>
                
                <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; border: 1px solid #ddd; width: 40%;"><strong>Customer:</strong></td>
                        <td style="padding: 12px; border: 1px solid #ddd;">{{billing_first_name}} {{billing_last_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Service:</strong></td>
                        <td style="padding: 12px; border: 1px solid #ddd;">{{service_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Service Date & Time:</strong></td>
                        <td style="padding: 12px; border: 1px solid #ddd;">{{date}} at {{time}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Voucher Value:</strong></td>
                        <td style="padding: 12px; border: 1px solid #ddd; font-weight: bold;">{{total_cost}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 12px; background: #f5f5f5; border: 1px solid #ddd;"><strong>Redeemed On:</strong></td>
                        <td style="padding: 12px; border: 1px solid #ddd;">{{redeemed_date}}</td>
                    </tr>
                </table>
                
                <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 30px 0;">
                    <h3 style="margin-top: 0;">Important Instructions:</h3>
                    <ul style="margin-bottom: 0;">
                        <li>Present this voucher at the time of service.</li>
                        <li>Voucher must be redeemed on the scheduled service date.</li>
                        <li>Valid for one-time use only.</li>
                        <li>Non-transferable and non-refundable.</li>
                    </ul>
                </div>
                
                <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
                    <p>For assistance, contact: {{admin_email}} | {{admin_phone}}</p>
                    <p>Voucher generated on: {{current_date}}</p>
                </div>
            </div>';

			case 'customer_info':
				return '
            <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                <div style="margin-bottom: 20px;">
                    {{logo}}
                </div>
                
                <h1 style="color: #333; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
                    Customer Information
                </h1>
                
                <h2 style="color: #4A90E2; margin-top: 30px;">Personal Details</h2>
                <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd; background: #f9f9f9; width: 30%;"><strong>Full Name:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{billing_first_name}} {{billing_last_name}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd; background: #f9f9f9;"><strong>Email Address:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{billing_email}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd; background: #f9f9f9;"><strong>Phone Number:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{billing_phone}}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd; background: #f9f9f9;"><strong>Billing Address:</strong></td>
                        <td style="padding: 10px; border-bottom: 1px solid #ddd;">{{billing_address}}</td>
                    </tr>
                </table>
                
                <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
                    <p>Generated on: {{current_date}} at {{current_time}}</p>
                    <p>© {{current_year}} All rights reserved.</p>
                </div>
            </div>';

			case 'booking':
			default:
				return '
            <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
                <table style="width: 100%; border: none; margin-bottom: 20px;">
                    <tr>
                        <td style="border: none; width: 50%;">{{logo}}</td>
                        <td style="border: none; width: 50%; text-align: right;">
                            {{qr_code}}
                        </td>
                    </tr>
                </table>
                
                <h1 style="color: #333; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
                    Order Confirmation
                </h1>
                
                <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #4A90E2;">
                    <strong>Booking Reference:</strong> {{booking_key}}<br>
                    <strong>Date & Time of Booking:</strong> {{date_time}}
                </div>
                
                <table style="width: 100%; border: none; margin: 15px 0;">
                    <tr>
                        <td style="vertical-align: top; width: 50%; padding-right: 15px; border: none;">
                            <h2 style="color: #4A90E2; font-size: 18px;">Customer Information</h2>
                            <p style="margin: 5px 0;"><strong>Name:</strong> {{billing_first_name}} {{billing_last_name}}</p>
                            <p style="margin: 5px 0;"><strong>Email:</strong> {{billing_email}}</p>
                            <p style="margin: 5px 0;"><strong>Phone:</strong> {{billing_phone}}</p>
                            <p style="margin: 5px 0;"><strong>Address:</strong> {{billing_address}}</p>
                        </td>
                        <td style="vertical-align: top; width: 50%; padding-left: 15px; border: none;">
                            <h2 style="color: #4A90E2; font-size: 18px;">Service Details</h2>
                            <p style="margin: 5px 0;"><strong>Service:</strong> {{service_name}}</p>
                            <p style="margin: 5px 0;"><strong>Date:</strong> {{booking_date}}</p>
                            <p style="margin: 5px 0;"><strong>Duration:</strong> {{service_duration}}</p>
                        </td>
                    </tr>
                </table>
                
                <h2 style="color: #4A90E2; margin-top: 30px; font-size: 18px;">Order Summary</h2>
                <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                    <tr style="background: #4A90E2; color: white;">
                        <th style="padding: 10px; text-align: left; border: 1px solid #357ABD;">Description</th>
                        <th style="padding: 10px; text-align: center; border: 1px solid #357ABD;">Qty</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #357ABD;">Price</th>
                        <th style="padding: 10px; text-align: right; border: 1px solid #357ABD;">Total</th>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{service_name}}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{service_qty}}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{service_price}}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: right;">{{service_total}}</td>
                    </tr>
                    
                    {{extra_services}}
                    
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>Subtotal:</strong></td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd;">{{subtotal}}</td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>Discount:</strong></td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; color: #27AE60;">-{{disount_amount}}</td>
                    </tr>
                    <tr style="background: #f9f9f9;">
                        <td colspan="3" style="padding: 10px; text-align: right; border: 1px solid #ddd;"><strong>Total Amount:</strong></td>
                        <td style="padding: 10px; text-align: right; border: 1px solid #ddd; font-weight: bold; font-size: 16px;">{{total_cost}}</td>
                    </tr>
                </table>
                
                <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
                    <p>Thank you for your booking!</p>
                    <p>For any questions, please contact our customer service at {{admin_email}}.</p>
                </div>
            </div>';
		}
	}

    /**
     * Generate QR code directly into memory for Dompdf
     */
	public function generate_qr_code( $booking_reference ) {
        if ( empty( $booking_reference ) ) {
			return '';
        }
        if ( ! class_exists( 'QRcode' ) ) {
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'src/phpqrcode/qrlib.php';
        }
        if ( ob_get_length() ) {
			ob_clean();
        }

        ob_start();
        QRcode::png( $booking_reference, null, QR_ECLEVEL_L, 4, 1 );
        $image_string = ob_get_clean();
        return base64_encode( $image_string );
    }

    private function get_service_quantity( $productDetails ) {
        return isset( $productDetails['products'][0]['quantity'] ) ? $productDetails['products'][0]['quantity'] : '1';
    }

    private function get_service_price( $productDetails ) {
        return isset( $productDetails['products'][0]['base_price'] ) ? $this->bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['products'][0]['base_price'], true ) : '';
    }

    private function get_service_total( $productDetails ) {
        return isset( $productDetails['products'][0]['total'] ) ? $this->bmrequests->bm_fetch_price_in_global_settings_format( $productDetails['products'][0]['total'], true ) : '';
    }

    public function get_test_booking_data() {
        return max( 0, $this->dbhandler->get_all_result( 'BOOKING', 'id', 1, 'var', 0, 1, 'id', 'DESC' ) );
    }

    public function get_html_preview( $type = 'booking', $booking_id = 0 ) {
        $html = $this->bm_get_template_pdf_content( $type, $booking_id );
        return '<div style="background:#fff3cd; border:1px solid #ffeaa7; padding:15px; margin:20px 0;"><h4>Preview Mode</h4></div>' . $html;
    }
}
