<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of class-booking-management-request
 *
 * @author Start and Grow <laravel6@startandgrow.in>
 */
if ( ! class_exists( 'Dompdf\Dompdf' ) ) {
	require_once plugin_dir_path( __DIR__ ) . 'src/dompdf/vendor/autoload.php';
}

use Dompdf\Dompdf;

class BM_Request {

	public function sanitize_request( $post, $identifier, $exclude = array() ) {
		$bmsanitizer = new BM_Sanitizer();

		$post = $bmsanitizer->remove_magic_quotes( $post );

		foreach ( $post as $key => $value ) {
			if ( ! in_array( $key, $exclude ) ) {
				if ( ! is_array( $value ) ) {
					$data[ $key ] = $bmsanitizer->get_sanitized_fields( $identifier, $key, $value );
				} else {
					$data[ $key ] = maybe_serialize( $this->sanitize_request_array( $value, $identifier ) );
				}
			}
		}

		if ( isset( $data ) ) {
			return $data;
		} else {
			return null;
		}
	}//end sanitize_request()


	public function sanitize_request_array( $post, $identifier ) {
		$bmsanitizer = new BM_Sanitizer();

		foreach ( $post as $key => $value ) {
			if ( is_array( $value ) ) {
				$data[ $key ] = $this->sanitize_request_array( $value, $identifier );
			} else {
				$data[ $key ] = $bmsanitizer->get_sanitized_fields( $identifier, $key, $value );
			}
		}

		if ( isset( $data ) ) {
			return $data;
		} else {
			return null;
		}
	}//end sanitize_request_array()


	/**
	 * Convert duration of services to string
	 *
	 * @author Darpan
	 */
	public function bm_convert_float_to_time_string( $float ) {
		$hours   = floor( $float );
		$minutes = ( $float - $hours ) * 60;
		$string  = '';

		if ( ! empty( $hours ) ) {
			$string .= $hours . 'h ';
		}

		if ( ! empty( $minutes ) ) {
			$string .= $minutes . 'min';
		}

		return $string;
	}//end bm_convert_float_to_time_string()


	/**
	 * Fetch duration of services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_float_to_time_string( $float ) {
		$hours   = floor( (float) $float );
		$minutes = ( (float) $float - $hours ) * 60;
		$text    = '';

		if ( $hours > 0 ) {
			$text .= $hours . 'h';
		}

		if ( $minutes > 0 ) {
			$text .= ' ' . $minutes . 'm';
		}

		return $text;
	}


	/**
	 * Fetch last day of a month
	 *
	 * @author Darpan
	 */
	public function bm_fetch_last_day_of_month( $month, $year ) {
		$days_in_month = array(
			'01' => '31',
			'02' => $this->bm_check_if_leap_year( $year ) == 1 ? '29' : '28',
			'03' => '31',
			'04' => '30',
			'05' => '31',
			'06' => '30',
			'07' => '31',
			'08' => '31',
			'09' => '30',
			'10' => '31',
			'11' => '30',
			'12' => '31',
		);

		return $days_in_month[ $month ] ?? '';
	}


	/**
	 * Check field key availability
	 *
	 * @author Darpan
	 */
	public function bm_fetch_default_key_type( $type ) {
		switch ( $type ) {
			case 'text':
			case 'email':
			case 'url':
			case 'password':
			case 'select':
			case 'radio':
			case 'textarea':
			case 'checkbox':
			case 'date':
			case 'time':
			case 'datetime':
			case 'month':
			case 'week':
			case 'number':
			case 'file':
			case 'button':
			case 'submit':
			case 'tel':
			case 'hidden':
			case 'color':
			case 'range':
			case 'reset':
			case 'search':
				$value = true;
				break;
			default:
				$value = false;
		} //end switch

		return $value;
	}//end bm_fetch_default_key_type()


	/**
	 * Get currency symbol
	 *
	 * @author Darpan
	 */
	public function bm_get_currency_symbol() {
		$dbhandler = new BM_DBhandler();
		$currency  = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );

		switch ( $currency ) {
			case 'USD':
				$sign = '&#36;';
				break;
			case 'EUR':
				$sign = '&#0128;';
				break;
			case 'GBP':
				$sign = '&#163;';
				break;
			case 'AUD':
				$sign = '&#36;';
				break;
			case 'BRL':
				$sign = 'R&#36;';
				break;
			case 'CAD':
				$sign = '&#36;';
				break;
			case 'HKD':
				$sign = '&#36;';
				break;
			case 'ILS':
				$sign = '&#8362;';
				break;
			case 'JPY':
				$sign = '&#165;';
				break;
			case 'MXN':
				$sign = '&#36;';
				break;
			case 'NZD':
				$sign = '&#36;';
				break;
			case 'SGD':
				$sign = '&#36;';
				break;
			case 'THB':
				$sign = '&#3647;';
				break;
			case 'INR':
				$sign = '&#8377;';
				break;
			case 'TRY':
				$sign = '&#8378;';
				break;
			default:
				$sign = $currency;
		} //end switch

		return $sign;
	}//end bm_get_currency_symbol()


	/**
	 * Get currency character
	 *
	 * @author Darpan
	 */
	public function bm_get_currency_char() {
		$dbhandler = new BM_DBhandler();
		$currency  = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );

		switch ( $currency ) {
			case 'USD':
				$sign = '$';
				break;
			case 'EUR':
				$sign = '€';
				break;
			case 'GBP':
				$sign = '£';
				break;
			case 'AUD':
				$sign = '$';
				break;
			case 'BRL':
				$sign = 'R$';
				break;
			case 'CAD':
				$sign = '$';
				break;
			case 'HKD':
				$sign = '$';
				break;
			case 'ILS':
				$sign = '₪';
				break;
			case 'JPY':
				$sign = '¥';
				break;
			case 'MXN':
				$sign = '$';
				break;
			case 'NZD':
				$sign = '$';
				break;
			case 'SGD':
				$sign = '$';
				break;
			case 'THB':
				$sign = '฿';
				break;
			case 'INR':
				$sign = '₹';
				break;
			case 'TRY':
				$sign = '₺';
				break;
			default:
				$sign = $currency;
		} //end switch

		return $sign;
	}//end bm_get_currency_char()


	/**
	 * Get country code and name
	 *
	 * @author Darpan
	 */
	public function bm_fetch_country( $country_code = '' ) {
		$countryList = array(
			'AF' => esc_html__( 'Afghanistan', 'service-booking' ),
			'AX' => esc_html__( 'Aland Islands', 'service-booking' ),
			'AL' => esc_html__( 'Albania', 'service-booking' ),
			'DZ' => esc_html__( 'Algeria', 'service-booking' ),
			'AS' => esc_html__( 'American Samoa', 'service-booking' ),
			'AD' => esc_html__( 'Andorra', 'service-booking' ),
			'AO' => esc_html__( 'Angola', 'service-booking' ),
			'AI' => esc_html__( 'Anguilla', 'service-booking' ),
			'AQ' => esc_html__( 'Antarctica', 'service-booking' ),
			'AG' => esc_html__( 'Antigua and Barbuda', 'service-booking' ),
			'AR' => esc_html__( 'Argentina', 'service-booking' ),
			'AM' => esc_html__( 'Armenia', 'service-booking' ),
			'AW' => esc_html__( 'Aruba', 'service-booking' ),
			'AU' => esc_html__( 'Australia', 'service-booking' ),
			'AT' => esc_html__( 'Austria', 'service-booking' ),
			'AZ' => esc_html__( 'Azerbaijan', 'service-booking' ),
			'BS' => esc_html__( 'Bahamas the', 'service-booking' ),
			'BH' => esc_html__( 'Bahrain', 'service-booking' ),
			'BD' => esc_html__( 'Bangladesh', 'service-booking' ),
			'BB' => esc_html__( 'Barbados', 'service-booking' ),
			'BY' => esc_html__( 'Belarus', 'service-booking' ),
			'BE' => esc_html__( 'Belgium', 'service-booking' ),
			'BZ' => esc_html__( 'Belize', 'service-booking' ),
			'BJ' => esc_html__( 'Benin', 'service-booking' ),
			'BM' => esc_html__( 'Bermuda', 'service-booking' ),
			'BT' => esc_html__( 'Bhutan', 'service-booking' ),
			'BO' => esc_html__( 'Bolivia', 'service-booking' ),
			'BA' => esc_html__( 'Bosnia and Herzegovina', 'service-booking' ),
			'BW' => esc_html__( 'Botswana', 'service-booking' ),
			'BV' => esc_html__( 'Bouvet Island (Bouvetoya)', 'service-booking' ),
			'BR' => esc_html__( 'Brazil', 'service-booking' ),
			'IO' => esc_html__( 'British Indian Ocean Territory (Chagos Archipelago)', 'service-booking' ),
			'VG' => esc_html__( 'British Virgin Islands', 'service-booking' ),
			'BN' => esc_html__( 'Brunei Darussalam', 'service-booking' ),
			'BG' => esc_html__( 'Bulgaria', 'service-booking' ),
			'BF' => esc_html__( 'Burkina Faso', 'service-booking' ),
			'BI' => esc_html__( 'Burundi', 'service-booking' ),
			'KH' => esc_html__( 'Cambodia', 'service-booking' ),
			'CM' => esc_html__( 'Cameroon', 'service-booking' ),
			'CA' => esc_html__( 'Canada', 'service-booking' ),
			'CV' => esc_html__( 'Cape Verde', 'service-booking' ),
			'KY' => esc_html__( 'Cayman Islands', 'service-booking' ),
			'CF' => esc_html__( 'Central African Republic', 'service-booking' ),
			'TD' => esc_html__( 'Chad', 'service-booking' ),
			'CL' => esc_html__( 'Chile', 'service-booking' ),
			'CN' => esc_html__( 'China', 'service-booking' ),
			'CX' => esc_html__( 'Christmas Island', 'service-booking' ),
			'CC' => esc_html__( 'Cocos (Keeling) Islands', 'service-booking' ),
			'CO' => esc_html__( 'Colombia', 'service-booking' ),
			'KM' => esc_html__( 'Comoros the', 'service-booking' ),
			'CD' => esc_html__( 'Congo', 'service-booking' ),
			'CG' => esc_html__( 'Congo the', 'service-booking' ),
			'CK' => esc_html__( 'Cook Islands', 'service-booking' ),
			'CR' => esc_html__( 'Costa Rica', 'service-booking' ),
			'CI' => esc_html__( 'Cote d\'Ivoire', 'service-booking' ),
			'HR' => esc_html__( 'Croatia', 'service-booking' ),
			'CU' => esc_html__( 'Cuba', 'service-booking' ),
			'CY' => esc_html__( 'Cyprus', 'service-booking' ),
			'CZ' => esc_html__( 'Czech Republic', 'service-booking' ),
			'DK' => esc_html__( 'Denmark', 'service-booking' ),
			'DJ' => esc_html__( 'Djibouti', 'service-booking' ),
			'DM' => esc_html__( 'Dominica', 'service-booking' ),
			'DO' => esc_html__( 'Dominican Republic', 'service-booking' ),
			'EC' => esc_html__( 'Ecuador', 'service-booking' ),
			'EG' => esc_html__( 'Egypt', 'service-booking' ),
			'SV' => esc_html__( 'El Salvador', 'service-booking' ),
			'GQ' => esc_html__( 'Equatorial Guinea', 'service-booking' ),
			'ER' => esc_html__( 'Eritrea', 'service-booking' ),
			'EE' => esc_html__( 'Estonia', 'service-booking' ),
			'ET' => esc_html__( 'Ethiopia', 'service-booking' ),
			'FO' => esc_html__( 'Faroe Islands', 'service-booking' ),
			'FK' => esc_html__( 'Falkland Islands (Malvinas)', 'service-booking' ),
			'FJ' => esc_html__( 'Fiji the Fiji Islands', 'service-booking' ),
			'FI' => esc_html__( 'Finland', 'service-booking' ),
			'FR' => esc_html__( 'France, French Republic', 'service-booking' ),
			'GF' => esc_html__( 'French Guiana', 'service-booking' ),
			'PF' => esc_html__( 'French Polynesia', 'service-booking' ),
			'TF' => esc_html__( 'French Southern Territories', 'service-booking' ),
			'GA' => esc_html__( 'Gabon', 'service-booking' ),
			'GM' => esc_html__( 'Gambia the', 'service-booking' ),
			'GE' => esc_html__( 'Georgia', 'service-booking' ),
			'DE' => esc_html__( 'Germany', 'service-booking' ),
			'GH' => esc_html__( 'Ghana', 'service-booking' ),
			'GI' => esc_html__( 'Gibraltar', 'service-booking' ),
			'GR' => esc_html__( 'Greece', 'service-booking' ),
			'GL' => esc_html__( 'Greenland', 'service-booking' ),
			'GD' => esc_html__( 'Grenada', 'service-booking' ),
			'GP' => esc_html__( 'Guadeloupe', 'service-booking' ),
			'GU' => esc_html__( 'Guam', 'service-booking' ),
			'GT' => esc_html__( 'Guatemala', 'service-booking' ),
			'GG' => esc_html__( 'Guernsey', 'service-booking' ),
			'GN' => esc_html__( 'Guinea', 'service-booking' ),
			'GW' => esc_html__( 'Guinea-Bissau', 'service-booking' ),
			'GY' => esc_html__( 'Guyana', 'service-booking' ),
			'HT' => esc_html__( 'Haiti', 'service-booking' ),
			'HM' => esc_html__( 'Heard Island and McDonald Islands', 'service-booking' ),
			'VA' => esc_html__( 'Holy See (Vatican City State)', 'service-booking' ),
			'HN' => esc_html__( 'Honduras', 'service-booking' ),
			'HK' => esc_html__( 'Hong Kong', 'service-booking' ),
			'HU' => esc_html__( 'Hungary', 'service-booking' ),
			'IS' => esc_html__( 'Iceland', 'service-booking' ),
			'IN' => esc_html__( 'India', 'service-booking' ),
			'ID' => esc_html__( 'Indonesia', 'service-booking' ),
			'IR' => esc_html__( 'Iran', 'service-booking' ),
			'IQ' => esc_html__( 'Iraq', 'service-booking' ),
			'IE' => esc_html__( 'Ireland', 'service-booking' ),
			'IM' => esc_html__( 'Isle of Man', 'service-booking' ),
			'IL' => esc_html__( 'Israel', 'service-booking' ),
			'IT' => esc_html__( 'Italy', 'service-booking' ),
			'JM' => esc_html__( 'Jamaica', 'service-booking' ),
			'JP' => esc_html__( 'Japan', 'service-booking' ),
			'JE' => esc_html__( 'Jersey', 'service-booking' ),
			'JO' => esc_html__( 'Jordan', 'service-booking' ),
			'KZ' => esc_html__( 'Kazakhstan', 'service-booking' ),
			'KE' => esc_html__( 'Kenya', 'service-booking' ),
			'KI' => esc_html__( 'Kiribati', 'service-booking' ),
			'KP' => esc_html__( 'Korea', 'service-booking' ),
			'KR' => esc_html__( 'Korea', 'service-booking' ),
			'KW' => esc_html__( 'Kuwait', 'service-booking' ),
			'KG' => esc_html__( 'Kyrgyz Republic', 'service-booking' ),
			'LA' => esc_html__( 'Lao', 'service-booking' ),
			'LV' => esc_html__( 'Latvia', 'service-booking' ),
			'LB' => esc_html__( 'Lebanon', 'service-booking' ),
			'LS' => esc_html__( 'Lesotho', 'service-booking' ),
			'LR' => esc_html__( 'Liberia', 'service-booking' ),
			'LY' => esc_html__( 'Libyan Arab Jamahiriya', 'service-booking' ),
			'LI' => esc_html__( 'Liechtenstein', 'service-booking' ),
			'LT' => esc_html__( 'Lithuania', 'service-booking' ),
			'LU' => esc_html__( 'Luxembourg', 'service-booking' ),
			'MO' => esc_html__( 'Macao', 'service-booking' ),
			'MK' => esc_html__( 'Macedonia', 'service-booking' ),
			'MG' => esc_html__( 'Madagascar', 'service-booking' ),
			'MW' => esc_html__( 'Malawi', 'service-booking' ),
			'MY' => esc_html__( 'Malaysia', 'service-booking' ),
			'MV' => esc_html__( 'Maldives', 'service-booking' ),
			'ML' => esc_html__( 'Mali', 'service-booking' ),
			'MT' => esc_html__( 'Malta', 'service-booking' ),
			'MH' => esc_html__( 'Marshall Islands', 'service-booking' ),
			'MQ' => esc_html__( 'Martinique', 'service-booking' ),
			'MR' => esc_html__( 'Mauritania', 'service-booking' ),
			'MU' => esc_html__( 'Mauritius', 'service-booking' ),
			'YT' => esc_html__( 'Mayotte', 'service-booking' ),
			'MX' => esc_html__( 'Mexico', 'service-booking' ),
			'FM' => esc_html__( 'Micronesia', 'service-booking' ),
			'MD' => esc_html__( 'Moldova', 'service-booking' ),
			'MC' => esc_html__( 'Monaco', 'service-booking' ),
			'MN' => esc_html__( 'Mongolia', 'service-booking' ),
			'ME' => esc_html__( 'Montenegro', 'service-booking' ),
			'MS' => esc_html__( 'Montserrat', 'service-booking' ),
			'MA' => esc_html__( 'Morocco', 'service-booking' ),
			'MZ' => esc_html__( 'Mozambique', 'service-booking' ),
			'MM' => esc_html__( 'Myanmar', 'service-booking' ),
			'NA' => esc_html__( 'Namibia', 'service-booking' ),
			'NR' => esc_html__( 'Nauru', 'service-booking' ),
			'NP' => esc_html__( 'Nepal', 'service-booking' ),
			'AN' => esc_html__( 'Netherlands Antilles', 'service-booking' ),
			'NL' => esc_html__( 'Netherlands the', 'service-booking' ),
			'NC' => esc_html__( 'New Caledonia', 'service-booking' ),
			'NZ' => esc_html__( 'New Zealand', 'service-booking' ),
			'NI' => esc_html__( 'Nicaragua', 'service-booking' ),
			'NE' => esc_html__( 'Niger', 'service-booking' ),
			'NG' => esc_html__( 'Nigeria', 'service-booking' ),
			'NU' => esc_html__( 'Niue', 'service-booking' ),
			'NF' => esc_html__( 'Norfolk Island', 'service-booking' ),
			'MP' => esc_html__( 'Northern Mariana Islands', 'service-booking' ),
			'NO' => esc_html__( 'Norway', 'service-booking' ),
			'OM' => esc_html__( 'Oman', 'service-booking' ),
			'PK' => esc_html__( 'Pakistan', 'service-booking' ),
			'PW' => esc_html__( 'Palau', 'service-booking' ),
			'PS' => esc_html__( 'Palestinian Territory', 'service-booking' ),
			'PA' => esc_html__( 'Panama', 'service-booking' ),
			'PG' => esc_html__( 'Papua New Guinea', 'service-booking' ),
			'PY' => esc_html__( 'Paraguay', 'service-booking' ),
			'PE' => esc_html__( 'Peru', 'service-booking' ),
			'PH' => esc_html__( 'Philippines', 'service-booking' ),
			'PN' => esc_html__( 'Pitcairn Islands', 'service-booking' ),
			'PL' => esc_html__( 'Poland', 'service-booking' ),
			'PT' => esc_html__( 'Portugal, Portuguese Republic', 'service-booking' ),
			'PR' => esc_html__( 'Puerto Rico', 'service-booking' ),
			'QA' => esc_html__( 'Qatar', 'service-booking' ),
			'RE' => esc_html__( 'Reunion', 'service-booking' ),
			'RO' => esc_html__( 'Romania', 'service-booking' ),
			'RU' => esc_html__( 'Russian Federation', 'service-booking' ),
			'RW' => esc_html__( 'Rwanda', 'service-booking' ),
			'BL' => esc_html__( 'Saint Barthelemy', 'service-booking' ),
			'SH' => esc_html__( 'Saint Helena', 'service-booking' ),
			'KN' => esc_html__( 'Saint Kitts and Nevis', 'service-booking' ),
			'LC' => esc_html__( 'Saint Lucia', 'service-booking' ),
			'MF' => esc_html__( 'Saint Martin', 'service-booking' ),
			'PM' => esc_html__( 'Saint Pierre and Miquelon', 'service-booking' ),
			'VC' => esc_html__( 'Saint Vincent and the Grenadines', 'service-booking' ),
			'WS' => esc_html__( 'Samoa', 'service-booking' ),
			'SM' => esc_html__( 'San Marino', 'service-booking' ),
			'ST' => esc_html__( 'Sao Tome and Principe', 'service-booking' ),
			'SA' => esc_html__( 'Saudi Arabia', 'service-booking' ),
			'SN' => esc_html__( 'Senegal', 'service-booking' ),
			'RS' => esc_html__( 'Serbia', 'service-booking' ),
			'SC' => esc_html__( 'Seychelles', 'service-booking' ),
			'SL' => esc_html__( 'Sierra Leone', 'service-booking' ),
			'SG' => esc_html__( 'Singapore', 'service-booking' ),
			'SK' => esc_html__( 'Slovakia (Slovak Republic)', 'service-booking' ),
			'SI' => esc_html__( 'Slovenia', 'service-booking' ),
			'SB' => esc_html__( 'Solomon Islands', 'service-booking' ),
			'SO' => esc_html__( 'Somalia, Somali Republic', 'service-booking' ),
			'ZA' => esc_html__( 'South Africa', 'service-booking' ),
			'GS' => esc_html__( 'South Georgia and the South Sandwich Islands', 'service-booking' ),
			'ES' => esc_html__( 'Spain', 'service-booking' ),
			'LK' => esc_html__( 'Sri Lanka', 'service-booking' ),
			'SD' => esc_html__( 'Sudan', 'service-booking' ),
			'SR' => esc_html__( 'Suriname', 'service-booking' ),
			'SJ' => esc_html__( 'Svalbard & Jan Mayen Islands', 'service-booking' ),
			'SZ' => esc_html__( 'Swaziland', 'service-booking' ),
			'SE' => esc_html__( 'Sweden', 'service-booking' ),
			'CH' => esc_html__( 'Switzerland, Swiss Confederation', 'service-booking' ),
			'SY' => esc_html__( 'Syrian Arab Republic', 'service-booking' ),
			'TW' => esc_html__( 'Taiwan', 'service-booking' ),
			'TJ' => esc_html__( 'Tajikistan', 'service-booking' ),
			'TZ' => esc_html__( 'Tanzania', 'service-booking' ),
			'TH' => esc_html__( 'Thailand', 'service-booking' ),
			'TL' => esc_html__( 'Timor-Leste', 'service-booking' ),
			'TG' => esc_html__( 'Togo', 'service-booking' ),
			'TK' => esc_html__( 'Tokelau', 'service-booking' ),
			'TO' => esc_html__( 'Tonga', 'service-booking' ),
			'TT' => esc_html__( 'Trinidad and Tobago', 'service-booking' ),
			'TN' => esc_html__( 'Tunisia', 'service-booking' ),
			'TR' => esc_html__( 'Turkey', 'service-booking' ),
			'TM' => esc_html__( 'Turkmenistan', 'service-booking' ),
			'TC' => esc_html__( 'Turks and Caicos Islands', 'service-booking' ),
			'TV' => esc_html__( 'Tuvalu', 'service-booking' ),
			'UG' => esc_html__( 'Uganda', 'service-booking' ),
			'UA' => esc_html__( 'Ukraine', 'service-booking' ),
			'AE' => esc_html__( 'United Arab Emirates', 'service-booking' ),
			'GB' => esc_html__( 'United Kingdom', 'service-booking' ),
			'US' => esc_html__( 'United States of America', 'service-booking' ),
			'UM' => esc_html__( 'United States Minor Outlying Islands', 'service-booking' ),
			'VI' => esc_html__( 'United States Virgin Islands', 'service-booking' ),
			'UY' => esc_html__( 'Uruguay', 'service-booking' ),
			'UZ' => esc_html__( 'Uzbekistan', 'service-booking' ),
			'VU' => esc_html__( 'Vanuatu', 'service-booking' ),
			'VE' => esc_html__( 'Venezuela', 'service-booking' ),
			'VN' => esc_html__( 'Vietnam', 'service-booking' ),
			'WF' => esc_html__( 'Wallis and Futuna', 'service-booking' ),
			'EH' => esc_html__( 'Western Sahara', 'service-booking' ),
			'YE' => esc_html__( 'Yemen', 'service-booking' ),
			'ZM' => esc_html__( 'Zambia', 'service-booking' ),
			'ZW' => esc_html__( 'Zimbabwe', 'service-booking' ),
		);

		if ( ! empty( $country_code ) ) {
			$country_code = strtoupper( $country_code );
			return isset( $countryList[ $country_code ] ) ? $countryList[ $country_code ] : '';
		} else {
			return $countryList;
		}
	}//end bm_fetch_country()


	/**
	 * Get all countries and states
	 *
	 * @author Darpan
	 */
	public function bm_get_countries_and_states() {
		$file_path = plugin_dir_path( __DIR__ ) . 'admin/js/country-states.js';

		if ( ! file_exists( $file_path ) ) {
			return array();
		}

		$data      = file_get_contents( $file_path );
		$data      = str_replace( 'const country_and_states = ', '', $data );
		$data      = rtrim( $data, ';' );
		$json_data = json_decode( $data, true );

		return $json_data;
	}//end bm_get_countries_and_states()


	/**
	 * Get country code and name
	 *
	 * @author Darpan
	 */
	public function bm_get_countries( $country_code = '' ) {
		$json_data = $this->bm_get_countries_and_states();
		$countries = $json_data['country'] ?? array();

		if ( ! empty( $country_code ) && ! empty( $countries ) ) {
			$countries = $countries[ $country_code ] ?? array();
		}

		return $countries;
	}//end bm_get_countries()


	/**
	 * Get state code and name
	 *
	 * @author Darpan
	 */
	public function bm_get_states( $country_code = '' ) {
		$json_data = $this->bm_get_countries_and_states();
		$states    = $json_data['states'] ?? array();

		if ( ! empty( $country_code ) && ! empty( $states ) ) {
			$states = $states[ $country_code ] ?? array();
		}

		return $states;
	}//end bm_get_states()



	/**
	 * Fetch button with text
	 *
	 * @author Darpan
	 */
	public function bm_fetch_button_with_text( $module_id = 0, $inactive = 0, $text = 'Book', $link_class = 'get_extra_service', $button_id = 'select_slot_button', $resp = '' ) {
		$button_text = sprintf( esc_html__( '%s', 'service-booking' ), $text );

		if ( $inactive == 1 ) {
			$button_div_class = 'readonly_div';
			$inactive_class   = 'inactiveLink';
		} elseif ( $inactive == 0 ) {
			$button_div_class = 'bgcolor textwhite text-center';
			$inactive_class   = '';
		}

		$resp .= '<div class="bookbtnbar">';
		$resp .= '<div class="bookbtn ' . $button_div_class . '" id="' . $button_id . '">';
		$resp .= '<a href="#" id="' . $module_id . '" class="' . $link_class . ' ' . $inactive_class . '">';
		$resp .= $button_text . '</a>';
		$resp .= '</div></div>';

		return wp_kses_post( $resp );
	}//end bm_fetch_button_with_text()


	/**
	 * Fetch paragraph with text
	 *
	 * @author Darpan
	 */
	public function bm_fetch_paragraph_with_text( $text = 'No Results Found', $class = '', $resp = '' ) {
		$paragraph_text = sprintf( esc_html__( '%s', 'service-booking' ), $text );
		$class          = ! empty( $class ) ? 'class="' . $class . '"' : '';

		$resp .= '<p style="text-align: center;" ' . $class . '>';
		$resp .= $paragraph_text;
		$resp .= '</p>';

		return wp_kses_post( $resp );
	}//end bm_fetch_paragraph_with_text()


	/**
	 * Calculate memory usage of a function
	 *
	 * @author Darpan
	 */
	public function bm_get_memory_usgae( $function_to_call, ...$args ) {
		$start_memory = memory_get_usage();
		$result       = $function_to_call( ...$args );
		$end_memory   = memory_get_usage();
		$memory_used  = ( $end_memory - $start_memory );

		echo esc_html__( 'Memory used by the function: ', 'service-booking' ) . esc_attr( $memory_used ) . esc_html__( ' bytes', 'service-booking' );

		return $result;
	}//end bm_get_memory_usgae()


	/**
	 * Red captcha verification
	 *
	 * @author Darpan
	 */
	public function bm_redcaptcha_verification( $response, $remote_ip ) {
		$dbhandler     = new BM_DBhandler();
		$secret_key    = $dbhandler->get_global_option_value( 'bm_recaptcha_secret_key' );
		$request       = wp_remote_get(
			'https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $response . '&remoteip=' . $remote_ip
		);
		$response_body = wp_remote_retrieve_body( $request );
		$result        = json_decode( $response_body, true );
		return $result['success'];
	}//end bm_redcaptcha_verification()


	/**
	 * Show red captcha
	 *
	 * @author Darpan
	 */
	public function bm_show_redcaptcha( $option ) {
		$dbhandler             = new BM_DBhandler();
		$enable_recaptcha      = $dbhandler->get_global_option_value( 'bm_enable_recaptcha', 0 );
		$enable_recaptcha_form = $dbhandler->get_global_option_value( $option );

		if ( $enable_recaptcha == 1 && $enable_recaptcha_form == 1 ) {
			return true;
		} else {
			return false;
		}
	}//end bm_show_redcaptcha()


	/**
	 * Generate password
	 *
	 * @author Darpan
	 */
	public function bm_generate_password( $length = 12, $include_standard_special_chars = false ) {
		$password = wp_generate_password( $length, $include_standard_special_chars );
		return $password;
	}//end bm_generate_password()


	/**
	 * Fetch timezone
	 *
	 * @author Darpan
	 */
	public function bm_fetch_timezones( $country_code = '' ) {
		$timezones = array();

		if ( empty( $country_code ) ) {
			$timezones = DateTimeZone::listIdentifiers( DateTimeZone::ALL );
		} else {
			$timezones = DateTimeZone::listIdentifiers( DateTimeZone::PER_COUNTRY, $country_code );
		}

		return $timezones;
	}//end bm_fetch_timezones()


	/**
	 * Fetch country code by timezone
	 *
	 * @author Darpan
	 */
	public function bm_fetch_country_code_by_timezone( $timezone ) {
		$country_codes = array();
		$all_countries = array_keys( $this->bm_get_countries() );

		foreach ( $all_countries as $country_code ) {
			$timezones = DateTimeZone::listIdentifiers( DateTimeZone::PER_COUNTRY, $country_code );

			if ( ! empty( $timezones ) ) {
				foreach ( $timezones as $tz ) {
					$country_codes[ $tz ] = $country_code;
				}
			}
		}

		return isset( $country_codes[ $timezone ] ) ? $country_codes[ $timezone ] : null;
	}//end bm_fetch_country_code_by_timezone()


	/**
	 * Fetch the current WordPress datetime
	 *
	 * @author Darpan
	 */
	public function bm_fetch_current_wordpress_datetime_stamp() {
		return current_time( 'mysql' );
	}//end bm_fetch_current_wordpress_datetime_stamp()


	/**
	 * Change datetime format to date
	 *
	 * @author Darpan
	 */
	public function bm_datetime_to_date_format( $datetime ) {
		return gmdate( 'Y-m-d', strtotime( $datetime ) );
	}//end bm_datetime_to_date_format()


	/**
	 * Change time string format to 12hrs or AM/PM
	 *
	 * @author Darpan
	 */
	public function bm_am_pm_format( $timeslot ) {
		return gmdate( 'g:i A', strtotime( $timeslot ) );
	}//end bm_am_pm_format()


	/**
	 * Change time string format to 24hrs
	 *
	 * @author Darpan
	 */
	public function bm_twenty_fourhrs_format( $timeslot ) {
		return gmdate( 'H:i', strtotime( $timeslot ) );
	}//end bm_twenty_fourhrs_format()


	/**
	 * Change float number to time string
	 *
	 * @author Darpan
	 */
	public function bm_float_to_time( $float ) {
		return sprintf( '%02d:%02d', (int) $float, ( fmod( $float, 1 ) * 60 ) );
	}//end bm_float_to_time()


	/**
	 * Change date format like April 4th, 2023
	 *
	 * @author Darpan
	 */
	public function bm_month_year_date_format( $date ) {
		return gmdate( 'F jS , Y', strtotime( $date ) );
	}//end bm_month_year_date_format()


	/**
	 * Change date format like Thursday, June 15th , 2023
	 *
	 * @author Darpan
	 */
	public function bm_day_date_month_year_format( $date ) {
		return gmdate( 'l, F jS , Y', strtotime( $date ) );
	}//end bm_day_date_month_year_format()


	/**
	 * Check if leap year
	 *
	 * @author Darpan
	 */
	public function bm_check_if_leap_year( $year ) {
		$leap = gmdate( 'L', mktime( 0, 0, 0, 1, 1, $year ) );
		return $leap;
	}//end bm_check_if_leap_year()


	/**
	 * Fetch created at in string format
	 *
	 * @author Darpan
	 */
	public function bm_fetch_created_at_in_string( $createdAt ) {
		$string = $this->bm_fetch_datetime_difference( $createdAt );

		if ( empty( $string ) ) {
			return __( 'Added just now', 'service-booking' );
		}

		return $string . __( ' ago', 'service-booking' );
	}//end bm_fetch_created_at_in_string()


	/**
	 * Fetch created at in string format
	 *
	 * @author Darpan
	 */
	public function bm_fetch_datetime_difference( $dateTime ) {
		$timezone    = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$dateTime    = new DateTime( $dateTime, new DateTimeZone( $timezone ) );
		$currentDate = new DateTime( 'now', new DateTimeZone( $timezone ) );

		$interval = $dateTime->diff( $currentDate );

		$string = '';

		if ( $interval->y > 0 ) {
			$string = $interval->y . __( ' year', 'service-booking' ) . ( $interval->m > 1 ? 's' : '' );
		} elseif ( $interval->m > 0 ) {
			$string = $interval->m . __( ' month', 'service-booking' ) . ( $interval->m > 1 ? 's' : '' );
		} elseif ( $interval->d > 0 ) {
			$string = $interval->d . __( ' day', 'service-booking' ) . ( $interval->m > 1 ? 's' : '' );
		} elseif ( $interval->h > 0 ) {
			$string = $interval->h . __( ' hour', 'service-booking' ) . ( $interval->m > 1 ? 's' : '' );
		} elseif ( $interval->i > 0 ) {
			$string = $interval->i . __( ' minute', 'service-booking' ) . ( $interval->m > 1 ? 's' : '' );
		}

		return $string;
	}//end bm_fetch_datetime_difference()


	/**
	 * Convert date to other formats
	 *
	 * @author Darpan
	 */
	public function bm_convert_date_format( $date, $from_format, $to_format ) {
		$converted_date = DateTime::createFromFormat( $from_format, $date );
		if ( $converted_date ) {
			return $converted_date->format( $to_format );
		} else {
			return false;
		}
	}//end bm_convert_date_format()


	/**
	 * Format price as per country
	 *
	 * @author Darpan
	 */
	public function bm_format_price( $price, $remove_cyrrency_symbol = false ) {
		$dbhandler = new BM_DBhandler();

		if ( $remove_cyrrency_symbol ) {
			$booking_currency = $this->bm_get_currency_char( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
			$price            = $this->bm_strip_character( $price, $booking_currency );
		}

		$currency_locale = $dbhandler->get_global_option_value( 'bm_flexi_service_price_format', 'de_DE' );
		$formatter       = new NumberFormatter( $currency_locale, NumberFormatter::DECIMAL );
		$formatter->setAttribute( NumberFormatter::FRACTION_DIGITS, 2 );
		return $formatter->format( (float) $price );
	}//end bm_format_price()


	/**
	 * Change price format
	 *
	 * @author Darpan
	 */
	public function bm_convert_to_european_price_format( $price, $strip = false, $attach = false ) {
		$dbhandler = new BM_DBhandler();

		if ( $strip ) {
			$booking_currency = $this->bm_get_currency_char( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
			$price            = $this->bm_strip_character( $price, $booking_currency );
		}

		$price = number_format( floatval( $price ), 2, ',', '.' );

		if ( $attach ) {
			$price = $this->bm_add_price_character( $price );
		}

		return $price;
	}//end bm_convert_to_european_price_format()


	/**
	 * Format slot as per global settings
	 *
	 * @author Darpan
	 */
	public function bm_format_slot( $slot_format, $slot_time ) {
		if ( $slot_format == '12' ) {
			return $this->bm_am_pm_format( $slot_time );
		} else {
			return $this->bm_twenty_fourhrs_format( $slot_time );
		}
	}


	/**
	 * Strip character from a string
	 *
	 * @author Darpan
	 */
	public function bm_strip_character( $string, $character ) {
		if ( strpos( $string, $character ) !== false ) {
			$string = str_replace( $character, '', $string );
		}

		return $string;
	}//end bm_strip_character()


	/**
	 * Strip add price character
	 *
	 * @author Darpan
	 */
	public function bm_add_price_character( $price, $currency_symbol = '' ) {
		$dbhandler = new BM_DBhandler();

		if ( ! empty( $currency_symbol ) ) {
			$currency = $this->bm_get_currency_symbol( $currency_symbol );
		} else {
			$currency = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
		}

		$price = $dbhandler->get_global_option_value( 'bm_currency_position', 'before' ) == 'before' ? $currency . $price : $price . $currency;
		return $price;
	}//end bm_add_price_character()


	/**
	 * Fetch price in format set in global settings
	 *
	 * @author Darpan
	 */
	public function bm_fetch_price_in_global_settings_format( $price = 0, $has_conversion = false, $currency_symbol = '' ) {
		$price_format = '';

		if ( $has_conversion ) {
			/**$price = $this->bm_convert_to_european_price_format( $price, true );*/
			$price = $this->bm_format_price( $price, true );
		}

		$price_format = $this->bm_add_price_character( $price, $currency_symbol );
		return $price_format;
	}//end bm_fetch_price_in_global_settings_format()


	/**
	 * Fetch N dates in array
	 *
	 * @author Darpan
	 */
	public function bm_fetch_last_n_days( $days, $format = 'Y-m-d' ) {
		$m         = gmdate( 'm' );
		$de        = gmdate( 'd' );
		$y         = gmdate( 'Y' );
		$dateArray = array();

		for ( $i = 0; $i <= ( $days - 1 ); $i++ ) {
			$dateArray[] = '' . gmdate( $format, mktime( 0, 0, 0, $m, ( $de - $i ), $y ) ) . '';
		}

		return $dateArray;
	}//end bm_fetch_last_n_days()


	/**
	 * Add two time slots
	 *
	 * @author Darpan
	 */
	public function bm_add_time( $time1, $time2, $omit = '' ) {
		$times   = array(
			$time1,
			$time2,
		);
		$seconds = 0;
		foreach ( $times as $time ) {
			list($hour, $minute, $second) = explode( ':', $time );
			$seconds                     += ( $hour * 3600 );
			$seconds                     += ( $minute * 60 );
			$seconds                     += $second;
		}

		$hours    = floor( $seconds / 3600 );
		$seconds -= ( $hours * 3600 );
		$minutes  = floor( $seconds / 60 );
		$seconds -= ( $minutes * 60 );
		if ( $seconds < 9 ) {
			$seconds = '0' . $seconds;
		}

		if ( $minutes < 9 ) {
			$minutes = '0' . $minutes;
		}

		if ( $hours < 9 ) {
			$hours = '0' . $hours;
		}

		return $omit !== '' && $omit == 'seconds' ? "{$hours}:{$minutes}" : "{$hours}:{$minutes}:{$seconds}";
	}//end bm_add_time()


	/**
	 * Fetch date as per added days
	 *
	 * @author Darpan
	 */
	public function bm_add_day( $date, $add_days ) {
		$date = strtotime( $date );
		$date = strtotime( $add_days, $date );
		return gmdate( 'Y-m-d', $date );
	}//end bm_add_day()


	/**
	 * Fetch date query
	 *
	 * @author Darpan
	 */
	public function bm_fetch_date_query( $type, $start_date = '', $end_date = '' ) {
		$date_query = array();

		switch ( $type ) {
			case 'today':
				$today        = getdate();
				$date_query[] = array(
					'year'  => $today['year'],
					'month' => $today['mon'],
					'day'   => $today['mday'],
				);
				break;
			case 'yesterday':
				$yesterday    = getdate( strtotime( '-1 day' ) );
				$date_query[] = array(
					'year'  => $yesterday['year'],
					'month' => $yesterday['mon'],
					'day'   => $yesterday['mday'],
				);
				break;
			case 'this_week':
				$start_date    = gmdate( 'Y-m-d', strtotime( 'previous monday' ) );
				$end_date      = gmdate( 'Y-m-d', strtotime( 'next sunday' ) );
				$today         = getdate();
				$newstart_date = strtotime( $start_date );
				$newend_date   = strtotime( $end_date );
				$date_query[]  = array(
					'after'  => array(
						'year'  => gmdate( 'Y', $newstart_date ),
						'month' => gmdate( 'm', $newstart_date ),
						'day'   => gmdate( 'd', $newstart_date ),
					),
					'before' => array(
						'year'  => $today['wday'] == 7 ? $today['year'] : gmdate( 'Y', $newend_date ),
						'month' => $today['wday'] == 7 ? $today['mon'] : gmdate( 'm', $newend_date ),
						'day'   => $today['wday'] == 7 ? $today['mday'] : gmdate( 'd', $newend_date ),
					),
				);
				break;
			case 'last_week':
				$end_date     = gmdate( 'Y-m-d', strtotime( 'previous sunday' ) );
				$now          = strtotime( $end_date );
				$WeekMon      = mktime( 0, 0, 0, gmdate( 'm', $now ), ( gmdate( 'd', $now ) - 6 ), gmdate( 'Y', $now ) );
				$start_date   = getdate( $WeekMon );
				$newend_date  = strtotime( $end_date );
				$date_query[] = array(
					'after'  => array(
						'year'  => $start_date['year'],
						'month' => $start_date['mon'],
						'day'   => $start_date['mday'],
					),

					'before' => array(
						'year'  => gmdate( 'Y', $newend_date ),
						'month' => gmdate( 'm', $newend_date ),
						'day'   => gmdate( 'd', $newend_date ),
					),
				);
				break;
			case 'this_month':
				$date_query[] = array(
					'year'  => gmdate( 'Y' ),
					'month' => gmdate( 'm' ),
				);
				break;
			case 'this_year':
				$date_query[] = array(
					'year' => gmdate( 'Y' ),
				);
				break;
			case 'this_year_in_short':
				$date_query[] = array(
					'year' => gmdate( 'y' ),
				);
				break;
			case 'specific':
				if ( ! empty( $start_date ) && ! empty( $end_date ) ) {
					$start_date   = strtotime( $start_date );
					$end_date     = strtotime( $end_date );
					$date_query[] = array(
						'after'  => array(
							'year'  => gmdate( 'Y', $start_date ),
							'month' => gmdate( 'm', $start_date ),
							'day'   => gmdate( 'd', $start_date ),
						),
						'before' => array(
							'year'  => gmdate( 'Y', $end_date ),
							'month' => gmdate( 'm', $end_date ),
							'day'   => gmdate( 'd', $end_date ),
						),
					);
				}
				break;
		} //end switch

		return $date_query;
	}//end bm_fetch_date_query()


	/**
	 * Pad time slots with no leading zeroes
	 *
	 * @author Darpan
	 */
	public function bm_pad_with_zeros( $number ) {
		$lengthOfNumber = strlen( $number . '' );
		if ( $lengthOfNumber == 2 ) {
			return $number;
		} elseif ( $lengthOfNumber == 1 ) {
			return '0' . $number;
		} elseif ( $lengthOfNumber == 0 ) {
			return '00';
		} else {
			return false;
		}
	} //end padWithZeros()


	/**
	 * Find number of occurence of a shortcode by page id
	 *
	 * @author Darpan
	 */
	public function bm_find_shortcode_occurences_per_page( $shortcode, $post_id, $post_type = 'page' ) {
		$result       = array();
		$args         = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$query_result = new WP_Query( $args );
		foreach ( $query_result->posts as $post ) {
			if ( strpos( $post->post_content, $shortcode ) !== false && $post_id == $post->ID ) {
				preg_match_all( '/' . $shortcode . '/', $post->post_content, $matches, PREG_SET_ORDER );

				if ( ! empty( $matches ) ) {
					$result['post_id']    = $post->ID;
					$result['total_used'] = count( $matches );
				}
			}
		}

		return $result;
	}//end bm_find_shortcode_occurences_per_page()


	/**
	 * Find number of occurence of a shortcode
	 *
	 * @author Darpan
	 */
	public function bm_find_total_shortcode_occurences( $shortcode, $post_type = 'page' ) {
		$counter      = 0;
		$args         = array(
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
		);
		$query_result = new WP_Query( $args );
		foreach ( $query_result->posts as $post ) {
			if ( strpos( $post->post_content, $shortcode ) !== false ) {
				preg_match_all( '/' . $shortcode . '/', $post->post_content, $matches, PREG_SET_ORDER );

				if ( ! empty( $matches ) ) {
					$counter += count( $matches );
				}
			}
		}

		return $counter;
	}//end bm_find_total_shortcode_occurences()


	/**
	 * Find shortcode page id
	 *
	 * @author Darpan
	 */
	public function bm_get_shortcode_page_id( $shortcode ) {
		$string   = '[' . $shortcode;
		$page_id  = 0;
		$my_query = new WP_Query(
			array(
				'post_type' => 'any',
				's'         => $string,
				'fields'    => 'ids',
			)
		);
		if ( ! empty( $my_query->posts ) ) {
			$page_id = $my_query->posts[0];
		}

		return $page_id;
	}//end bm_get_shortcode_page_id()


	/**
	 * Fetch allowed tags
	 *
	 * @author Darpan
	 */
	public function bm_fetch_expanded_allowed_tags() {
		$my_allowed = wp_kses_allowed_html( 'post' );
		// iframe
		$my_allowed['iframe'] = array(
			'src'             => array(),
			'height'          => array(),
			'width'           => array(),
			'frameborder'     => array(),
			'allowfullscreen' => array(),
		);
		// form fields - input
		$my_allowed['input'] = array(
			'class'        => array(),
			'id'           => array(),
			'name'         => array(),
			'value'        => array(),
			'order'        => array(),
			'type'         => array(),
			'required'     => array(),
			'readonly'     => array(),
			'disabled'     => array(),
			'placeholder'  => array(),
			'autocomplete' => array(),
			'checked'      => array(),
			'min'          => array(),
			'max'          => array(),
			'step'         => array(),
			'style'        => array( $this->bm_fetch_allowed_style_attributes() ),
		);
		// form fields - textarea
		$my_allowed['textarea'] = array(
			'class'        => array(),
			'id'           => array(),
			'rows'         => array(),
			'columns'      => array(),
			'required'     => array(),
			'readonly'     => array(),
			'disabled'     => array(),
			'placeholder'  => array(),
			'autocomplete' => array(),
			'style'        => array( $this->bm_fetch_allowed_style_attributes() ),
		);
		// select
		$my_allowed['select'] = array(
			'class'    => array(),
			'id'       => array(),
			'name'     => array(),
			'value'    => array(),
			'type'     => array(),
			'required' => array(),
			'onchange' => array(),
			'onclick'  => array(),
			'readonly' => array(),
			'disabled' => array(),
			'style'    => array( $this->bm_fetch_allowed_style_attributes() ),
		);

		$my_allowed['img'] = array(
			'class'    => array(),
			'id'       => array(),
			'src'      => array(),
			'height'   => array(),
			'width'    => array(),
			'onclick'  => array(),
			'onchange' => array(),
			'alt'      => array(),
			'title'    => array(),
			'name'     => array(),
			'style'    => array( $this->bm_fetch_allowed_style_attributes() ),
		);

		// select options
		$my_allowed['option'] = array(
			'selected' => array(),
			'value'    => array(),
		);

		// form
		$my_allowed['form'] = array(
			'method'   => array(),
			'action'   => array(),
			'class'    => array(),
			'id'       => array(),
			'height'   => array(),
			'width'    => array(),
			'onclick'  => array(),
			'onchange' => array(),
			'name'     => array(),
			'style'    => array( $this->bm_fetch_allowed_style_attributes() ),
		);
		// style
		$my_allowed['style'] = array(
			'types' => array(),
		);

		return $my_allowed;
	}//end bm_fetch_expanded_allowed_tags()


	/**
	 * Fetch allowed style attributes
	 *
	 * @author Darpan
	 */
	public function bm_fetch_allowed_style_attributes() {
		add_filter(
			'safe_style_css',
			function ( $styles ) {
				$styles[] = 'display';
				$styles[] = 'pointer-events';
				return $styles;
			}
		);
	}//end bm_fetch_allowed_style_attributes()


	/**
	 * Create templates on plugin activation
	 *
	 * @author Darpan
	 */
	public function bm_create_default_email_templates() {
		$dbhandler  = new BM_DBhandler();
		$first_name = $dbhandler->get_value( 'FIELDS', 'field_name', 'billing_first_name', 'woocommerce_field' );
		$last_name  = $dbhandler->get_value( 'FIELDS', 'field_name', 'billing_last_name', 'woocommerce_field' );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Your order has been received !!.<br /><br /> Order Reference: {{booking_key}}.<br /> Service Name: {{service_name}}.<br />Service Date: {{booking_date}}.<br />Total cost: {{total_cost}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Il tuo ordine è stato ricevuto !!.<br /><br /> Riferimento allordine: {{booking_key}}.<br /> Nome del servizio: {{service_name}}.<br />Data del servizio: {{booking_date}}.<br />Costo totale: {{total_cost}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Frontend New Order',
			'tmpl_name_it'     => 'Nuovo ordine frontend',
			'type'             => 0,
			'email_subject_en' => 'New order received',
			'email_subject_it' => 'Nuovo ordine ricevuto',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Your order has been received and is on hold.<br /><br />Please pay at our front desk to complete the order.<br /><br /> Order Reference: {{booking_key}}.<br /> Service Name: {{service_name}}.<br />Service Date: {{booking_date}}.<br />Total cost: {{total_cost}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Il tuo ordine è stato ricevuto ed è in attesa.<br /><br />Si prega di pagare presso la nostra reception per completare lordine.<br /><br /> Riferimento allordine: {{booking_key}}.<br /> Nome del servizio: {{service_name}}.<br />Data del servizio: {{booking_date}}.<br />Costo totale: {{total_cost}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Backend New Order',
			'tmpl_name_it'     => 'Nuovo ordine nel backend',
			'type'             => 1,
			'email_subject_en' => 'New order received',
			'email_subject_it' => 'Nuovo ordine ricevuto',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />The amount {{total_cost}} for your order with reference {{booking_key}} has been refunded.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Limporto {{total_cost}} per il tuo ordine con riferimento {{booking_key}} è stato rimborsato.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order Refund',
			'tmpl_name_it'     => "Rimborso dell'ordine",
			'type'             => 2,
			'email_subject_en' => 'Order Amount Refunded',
			'email_subject_it' => "Importo dell'ordine rimborsato",
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />order for {{service_name}} with reference {{booking_key}} has been cancelled.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />ordinare per {{service_name}} con riferimento {{booking_key}} è stato cancellato.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order Cancel',
			'tmpl_name_it'     => 'Annulla ordine',
			'type'             => 3,
			'email_subject_en' => 'Order Cancelled',
			'email_subject_it' => 'Ordine annullato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />order for {{service_name}} with reference {{booking_key}} has been approved.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />ordinare per {{service_name}} con riferimento {{booking_key}} è stato approvato.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order Approval',
			'tmpl_name_it'     => "Approvazione dell'ordine",
			'type'             => 4,
			'email_subject_en' => 'Order Approved',
			'email_subject_it' => 'Ordine approvato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />New order received for {{service_name}}.<br /><br /> Total cost is {{total_cost}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Nuovo ordine ricevuto per {{service_name}}.<br /><br /> Il costo totale è {{total_cost}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'New Order Notification to Admin',
			'tmpl_name_it'     => "Notifica del nuovo ordine all'amministratore",
			'type'             => 5,
			'email_subject_en' => 'New order received',
			'email_subject_it' => 'Nuovo ordine ricevuto',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />Order for {{service_name}} has been cancelled.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Ordina per {{service_name}} è stato cancellato.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order Cancellation Notification to Admin',
			'tmpl_name_it'     => "Notifica di annullamento dell'ordine all'amministratore",
			'type'             => 6,
			'email_subject_en' => 'Order Cancelled',
			'email_subject_it' => 'Ordine annullato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />The amount {{total_cost}} for order with reference {{booking_key}} has been refunded.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Limporto {{total_cost}} per ordine con riferimento {{booking_key}} è stato rimborsato.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order Refund Notification to Admin',
			'tmpl_name_it'     => "Notifica di rimborso dell'ordine all'amministratore",
			'type'             => 7,
			'email_subject_en' => 'Order Refunded',
			'email_subject_it' => 'Ordine rimborsato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />Order for {{service_name}} has been approved.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Ordina per {{service_name}} è stato approvato.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order approval Notification to Admin',
			'tmpl_name_it'     => "Notifica di approvazione dell'ordine all'amministratore",
			'type'             => 8,
			'email_subject_en' => 'Order Approved',
			'email_subject_it' => 'Ordine approvato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />order for {{service_name}} with reference {{booking_key}} has failed.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />ordinare per {{service_name}} con riferimento {{booking_key}} ha fallito.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Order Failed',
			'tmpl_name_it'     => 'Ordine non riuscito',
			'type'             => 9,
			'email_subject_en' => 'Order Failed',
			'email_subject_it' => 'Ordine non riuscito',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />Order for {{service_name}} has failed.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Ordina per {{service_name}} ha fallito.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'failed Order Notification to Admin',
			'tmpl_name_it'     => "Notifica dell'ordine non riuscita all'amministratore",
			'type'             => 10,
			'email_subject_en' => 'Order Failed',
			'email_subject_it' => 'Ordine non riuscito',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{recipient_first_name}} {{recipient_last_name}},<br /><br />you have received a gift voucher from {{' . $first_name . '}} {{' . $last_name . '}}. Voucher code is {{voucher_code}}. Please use this voucher before {{voucher_expiry_date}} on {{voucher_redeem_page_url}}<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{recipient_first_name}} {{recipient_last_name}},<br /><br />hai ricevuto un buono regalo da from {{' . $first_name . '}} {{' . $last_name . '}}. Il codice del buono è {{voucher_code}}. Si prega di utilizzare questo buono prima {{voucher_expiry_date}} SU {{voucher_redeem_page_url}}<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Voucher mail',
			'tmpl_name_it'     => 'Posta voucher',
			'type'             => 11,
			'email_subject_en' => 'Gift voucher',
			'email_subject_it' => 'Buono regalo',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Your request has been received !!.<br /><br /> You will get a further notification regarding acceptance/cancellation of you request.<br /><br /> Request Reference: {{booking_key}}.<br /> Service Name: {{service_name}}.<br />Service Date: {{booking_date}}.<br />Total cost: {{total_cost}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />La tua richiesta è stata ricevuta !!.<br /><br /> Riceverai unulteriore notifica relativa allaccettazione/annullamento della tua richiesta.<br /><br /> Richiedi riferimento: {{booking_key}}.<br /> Nome del servizio: {{service_name}}.<br />Data del servizio: {{booking_date}}.<br />Costo totale: {{total_cost}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Frontend New Request',
			'tmpl_name_it'     => 'Nuova richiesta frontend',
			'type'             => 12,
			'email_subject_en' => 'New Request',
			'email_subject_it' => 'Buono regalo',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />Your request has been received !!.<br /><br /> You will get a further notification regarding acceptance/cancellation of you request.<br /><br /> Request Reference: {{booking_key}}.<br /> Service Name: {{service_name}}.<br />Service Date: {{booking_date}}.<br />Total cost: {{total_cost}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{' . $first_name . '}} {{' . $last_name . '}},<br /><br />La tua richiesta è stata ricevuta !!.<br /><br /> Riceverai unulteriore notifica relativa allaccettazione/annullamento della tua richiesta.<br /><br /> Richiedi riferimento: {{booking_key}}.<br /> Nome del servizio: {{service_name}}.<br />Data del servizio: {{booking_date}}.<br />Costo totale: {{total_cost}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Backend New Request',
			'tmpl_name_it'     => 'Nuova richiesta back-end',
			'type'             => 13,
			'email_subject_en' => 'New Request',
			'email_subject_it' => 'Buono regalo',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />New request received for {{service_name}}. Kindly approve it within {{booking_request_expiry}} to avoid auto cancellation.<br /><br /> Total cost is {{total_cost}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Nuova richiesta ricevuta per {{service_name}}. Si prega di approvarlo entro {{booking_request_expiry}} per evitare la cancellazione automatica.<br /><br /> Il costo totale è {{total_cost}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'New Request Notification to Admin',
			'tmpl_name_it'     => "Nuova richiesta di notifica all'amministratore",
			'type'             => 14,
			'email_subject_en' => 'New request received',
			'email_subject_it' => 'Nuova richiesta ricevuta',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{admin_name}},<br /><br />A gift voucher {{voucher_code}} is redeemed successfully by {{recipient_first_name}} {{recipient_last_name}} for {{service_name}}.<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{admin_name}},<br /><br />Un buono regalo {{voucher_code}} è stato riscattato con successo da {{recipient_first_name}} {{recipient_last_name}} per {{service_name}}.<br /><br />Cordiali saluti.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Voucher Redeem Notification to Admin',
			'tmpl_name_it'     => "Notifica di riscatto del voucher all'amministratore",
			'type'             => 15,
			'email_subject_en' => 'Gift Voucher Redeemed',
			'email_subject_it' => 'Buono regalo riscattato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$body_en   = 'Dear {{recipient_first_name}} {{recipient_last_name}},<br /><br />You have successfully redeemed gift voucher {{voucher_code}}. Please avail the service on {{booking_date}}<br /><br />Kind Regards.<br />{{from_name}}';
		$body_it   = 'Caro {{recipient_first_name}} {{recipient_last_name}},<br /><br />Hai riscattato con successo il buono regalo {{voucher_code}}. Si prega di usufruire del servizio il giorno {{booking_date}}.<br />{{from_name}}';
		$tmpl_data = array(
			'tmpl_name_en'     => 'Voucher Redeem',
			'tmpl_name_it'     => 'Riscatta il buono',
			'type'             => 16,
			'email_subject_en' => 'Voucher Redeemed',
			'email_subject_it' => 'Buono riscattato',
			'email_body_en'    => $body_en,
			'email_body_it'    => $body_it,
		);
		$tmpl_arg  = array(
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
		);
		$dbhandler->insert_row( 'EMAIL_TMPL', $tmpl_data, $tmpl_arg );

		$dbhandler->update_global_option_value( 'bm_email_templates_created', '1' );
	}//end bm_create_default_email_templates()


	/**
	 * Create booking form fields on plugin activation
	 *
	 * @author Darpan
	 */
	public function bm_create_default_booking_form_fields() {
		$dbhandler = new BM_DBhandler();

		$field_options = array(
			'placeholder'   => esc_html__( 'enter your first name', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'half',
			'default_value' => 'John',
			'is_visible'    => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'text',
			'field_label'       => esc_html__( 'First Name', 'service-booking' ),
			'field_name'        => 'billing_first_name',
			'field_desc'        => esc_html__( 'First name field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 1,
			'woocommerce_field' => 'billing_first_name',
			'field_key'         => 'sgbm_field_1',
			'field_position'    => 1,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'   => esc_html__( 'enter your last name', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'half',
			'default_value' => 'Doe',
			'is_visible'    => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'text',
			'field_label'       => esc_html__( 'Last Name', 'service-booking' ),
			'field_name'        => 'billing_last_name',
			'field_desc'        => esc_html__( 'Last name field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 0,
			'is_editable'       => 1,
			'ordering'          => 2,
			'woocommerce_field' => 'billing_last_name',
			'field_key'         => 'sgbm_field_2',
			'field_position'    => 2,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'   => esc_html__( 'enter your email', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'half',
			'default_value' => 'johndoe@testmail.com',
			'is_visible'    => 1,
			'is_main_email' => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'email',
			'field_label'       => esc_html__( 'Email', 'service-booking' ),
			'field_name'        => 'billing_email',
			'field_desc'        => esc_html__( 'Email field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 3,
			'woocommerce_field' => 'billing_email',
			'field_key'         => 'sgbm_field_3',
			'field_position'    => 3,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'    => esc_html__( 'enter your mobile number', 'service-booking' ),
			'custom_class'   => '',
			'field_width'    => 'half',
			'default_value'  => '1234567890',
			'is_visible'     => 1,
			'autocomplete'   => 0,
			'is_default'     => 1,
			'show_intl_code' => 1,
		);

		$field_data = array(
			'field_type'        => 'tel',
			'field_label'       => esc_html__( 'Mobile No', 'service-booking' ),
			'field_name'        => 'billing_contact',
			'field_desc'        => esc_html__( 'Mobile number field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 4,
			'woocommerce_field' => 'billing_phone',
			'field_key'         => 'sgbm_field_4',
			'field_position'    => 4,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'   => esc_html__( 'address...', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'full',
			'rows'          => 5,
			'columns'       => 50,
			'default_value' => '',
			'is_visible'    => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'textarea',
			'field_label'       => esc_html__( 'Address', 'service-booking' ),
			'field_name'        => 'billing_address',
			'field_desc'        => esc_html__( 'Address field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 5,
			'woocommerce_field' => 'billing_address_1',
			'field_key'         => 'sgbm_field_5',
			'field_position'    => 5,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$countries       = $this->bm_get_countries();
		$default_country = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );

		$field_options = array(
			'placeholder'  => esc_html__( 'enter country', 'service-booking' ),
			'custom_class' => '',
			'field_width'  => 'half',
			'options'      => array(
				'values'   => array_values( $countries ),
				'keys'     => array_keys( $countries ),
				'selected' => array_map(
					function ( $key ) use ( $default_country ) {
						return $key === $default_country ? 1 : 0;
					},
					array_keys( $countries )
				),
			),
			'is_visible'   => 1,
			'is_multiple'  => 0,
			'autocomplete' => 0,
			'is_default'   => 1,
		);

		$field_data = array(
			'field_type'        => 'select',
			'field_label'       => esc_html__( 'Country', 'service-booking' ),
			'field_name'        => 'billing_country',
			'field_desc'        => esc_html__( 'country field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 6,
			'woocommerce_field' => 'billing_country',
			'field_key'         => 'sgbm_field_8',
			'field_position'    => 6,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'  => esc_html__( 'enter state', 'service-booking' ),
			'custom_class' => '',
			'field_width'  => 'half',
			'options'      => '',
			'is_visible'   => 1,
			'is_multiple'  => 0,
			'autocomplete' => 0,
			'is_default'   => 1,
		);

		$field_data = array(
			'field_type'        => 'select',
			'field_label'       => esc_html__( 'State', 'service-booking' ),
			'field_name'        => 'billing_state',
			'field_desc'        => esc_html__( 'state field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 0,
			'is_editable'       => 1,
			'ordering'          => 7,
			'woocommerce_field' => 'billing_state',
			'field_key'         => 'sgbm_field_7',
			'field_position'    => 7,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'   => esc_html__( 'enter city', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'half',
			'default_value' => 'abc',
			'is_visible'    => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'text',
			'field_label'       => esc_html__( 'City', 'service-booking' ),
			'field_name'        => 'billing_city',
			'field_desc'        => esc_html__( 'city field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 8,
			'woocommerce_field' => 'billing_city',
			'field_key'         => 'sgbm_field_6',
			'field_position'    => 8,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'   => esc_html__( 'enter your zip code', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'half',
			'default_value' => '123456',
			'is_visible'    => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'number',
			'field_label'       => esc_html__( 'Zip', 'service-booking' ),
			'field_name'        => 'billing_postcode',
			'field_desc'        => esc_html__( 'zip code field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 1,
			'is_editable'       => 1,
			'ordering'          => 9,
			'woocommerce_field' => 'billing_postcode',
			'field_key'         => 'sgbm_field_9',
			'field_position'    => 9,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$field_options = array(
			'placeholder'   => esc_html__( 'order note...', 'service-booking' ),
			'custom_class'  => '',
			'field_width'   => 'full',
			'rows'          => 5,
			'columns'       => 50,
			'default_value' => '',
			'is_visible'    => 1,
			'autocomplete'  => 0,
			'is_default'    => 1,
		);

		$field_data = array(
			'field_type'        => 'textarea',
			'field_label'       => esc_html__( 'Order Notes', 'service-booking' ),
			'field_name'        => 'customer_order_note',
			'field_desc'        => esc_html__( 'Order notes field in the checkout form', 'service-booking' ),
			'field_options'     => maybe_serialize( $field_options ),
			'is_required'       => 0,
			'is_editable'       => 1,
			'ordering'          => 10,
			'woocommerce_field' => 'order_comments',
			'field_key'         => 'sgbm_field_10',
			'field_position'    => 10,
		);
		$field_arg  = array(
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%d',
			'%d',
			'%s',
			'%s',
			'%d',
		);
		$dbhandler->insert_row( 'FIELDS', $field_data, $field_arg );

		$dbhandler->update_global_option_value( 'bm_booking_form_fields_created', '1' );
	}//end bm_create_default_booking_form_fields()


	/**
	 * Fetch images of services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_image_field_value( $id, $identifier, $type, $module_type ) {
		$dbhandler = new BM_DBhandler();
		$field     = strtolower( trim( $identifier ) );

        switch ( $field ) {
            case 'service':
                $row   = $dbhandler->get_row( $identifier, $id );
                $value = isset( $row->service_image_guid ) ? esc_attr( $row->service_image_guid ) : 0;
                break;
            case 'fields':
                $row   = $dbhandler->get_row( $identifier, $id );
                $value = isset( $row->field_icon ) ? esc_attr( $row->field_icon ) : 0;
                break;
            case 'pdf_customization':
                $row   = $dbhandler->get_row( $identifier, $id );
                $value = isset( $row->pdf_logo_guid ) ? esc_attr( $row->pdf_logo_guid ) : 0;
                break;
            case 'gallery':
                $row        = $dbhandler->get_all_result(
                    $identifier,
                    '*',
                    array(
                        'module_type' => $module_type,
                        'module_id'   => $id,
                    ),
                    'results'
                )[0];
                $image_guid = isset( $row->image_guid ) ? $row->image_guid : 0;
                $value      = $type == 'guid' ? $image_guid : explode( ',', $image_guid );
                break;
            case 'coupon':
                $row   = $dbhandler->get_row( $identifier, $id );
                $value = isset( $row->coupon_image_guid ) ? esc_attr( $row->coupon_image_guid ) : 0;
                break;
            default:
                $row   = $dbhandler->get_row( $identifier, $id );
                $value = isset( $row->$field ) ? esc_attr( $row->$field . '_image_guid' ) : '';
                break;
        }

		return isset( $value ) ? $value : '';
	}//end bm_fetch_image_field_value()


	/**
	 * Fetch url/guid of images of services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_image_url_or_guid( $id, $identifier, $type = 'url', $module_type = '' ) {
		$imageid = $this->bm_fetch_image_field_value( $id, $identifier, $type, $module_type );
		$default = plugins_url( '../public/partials/image/Image-not-found.png', __FILE__ );
		if ( $identifier == 'COUPON' ) {
			$default = plugins_url( '../public/partials/image/coupon-image.png', __FILE__ );
		}
		if ( $type == 'guid' ) {
			return $imageid;
		}

		if ( isset( $imageid ) && ! empty( $imageid ) ) {
			if ( is_array( $imageid ) ) {
				$image = array();
				foreach ( $imageid as $guid ) {
					$image[] = wp_get_attachment_url( $guid );
				}
			} elseif ( is_string( $imageid ) ) {
				$image = wp_get_attachment_url( $imageid );
			}
		}

		return isset( $image ) ? $image : $default;
	}//end bm_fetch_image_url_or_guid()



	/**
	 * Fetch url/guid of images of coupons
	 *
	 * @author Darpan
	 */
	public function bm_fetch_cpn_image_url_or_guid( $id, $identifier, $type = 'url', $module_type = '' ) {
		$imageid = $this->bm_fetch_image_field_value( $id, $identifier, $type, $module_type );
		$default = plugins_url( '../public/partials/image/coupon-image.png', __FILE__ );
		if ( $type == 'guid' ) {
			return $imageid;
		}

		if ( isset( $imageid ) && ! empty( $imageid ) ) {
			if ( is_array( $imageid ) ) {
				$image = array();
				foreach ( $imageid as $guid ) {
					$image[] = wp_get_attachment_url( $guid );
				}
			} elseif ( is_string( $imageid ) ) {
				$image = wp_get_attachment_url( $imageid );
			}
		}

		return isset( $image ) ? $image : $default;
	}//end bm_fetch_cpn_image_url_or_guid()

	/**
	 * Create slug
	 *
	 * @author Darpan
	 */
	public function bm_create_slug( $str, $delimiter = '-' ) {
		$extension_pos = strrpos( $str, '.' );

		if ( $extension_pos ) {
			$str = substr( $str, 0, $extension_pos );
		}

		$slug = strtolower( trim( preg_replace( '/[\s-]+/', $delimiter, preg_replace( '/[^A-Za-z0-9-]+/', $delimiter, preg_replace( '/[&]/', 'and', preg_replace( '/[\']/', '', iconv( 'UTF-8', 'ASCII//TRANSLIT', $str ) ) ) ) ), $delimiter ) );
		return $slug;
	}//end bm_create_slug()


	/**
	 * Fetch field key of field module
	 *
	 * @author Darpan
	 */
	public function bm_fetch_field_key( $id ) {
		$key = 'sgbm_field_' . $id;
		return sanitize_key( $key );
	}//end bm_fetch_field_key()


	/**
	 * Fetch category name by category id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_category_name_by_category_id( $category_id = 0 ) {
		if ( $category_id == 0 ) {
			return 'Uncategorized';
		}

		$category = ( new BM_DBhandler() )->get_row( 'CATEGORY', $category_id );
		return isset( $category->cat_name ) ? $category->cat_name : '';
	}//end bm_fetch_category_name_by_category_id()


	/**
	 * Fetch category name by service id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_category_name_by_service_id( $service_id = 0 ) {
		$dbhandler     = new BM_DBhandler();
		$service       = $dbhandler->get_row( 'SERVICE', $service_id );
		$category_name = '';

		if ( isset( $service ) && ! empty( $service ) ) {
			$category_id   = isset( $service->service_category ) ? esc_attr( $service->service_category ) : '';
			$category_name = $this->bm_fetch_category_name_by_category_id( $category_id );
		}

		return $category_name;
	}//end bm_fetch_category_name_by_service_id()


	/**
	 * Fetch category details for multiple services
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_categories_by_service_array( $services = array() ) {
		$data = array();

		if ( ! empty( $services ) ) {
			$category_ids   = array();
			$category_names = array();

			foreach ( $services as $key => $service ) {
				$category_id            = isset( $service->service_category ) ? esc_attr( $service->service_category ) : '';
				$category_ids[ $key ]   = $category_id;
				$category_names[ $key ] = $this->bm_fetch_category_name_by_category_id( $category_id );
			}

			$data['id']    = ! empty( $category_ids ) ? array_values( array_unique( $category_ids ) ) : array();
			$data['name']  = ! empty( $category_names ) ? array_values( array_unique( $category_names ) ) : array();
			$data['total'] = ! empty( $data['name'] ) ? count( $data['name'] ) : array();
		}

		return $data;
	}//end bm_fetch_service_categories_by_service_array()


	/**
	 * Fetch category id by service id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_category_id_by_service_id( $service_id ) {
		$category_id = 0;

		if ( ! empty( $service_id ) && $service_id !== 0 ) {
			$dbhandler   = new BM_DBhandler();
			$category_id = $dbhandler->get_all_result( 'SERVICE', 'service_category', array( 'id' => $service_id ), 'var' );
		}

		return $category_id;
	}//end bm_fetch_category_id_by_service_id()


	/**
	 * Fetch services by category id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_services_by_category_id( $category_id ) {
		$services = array();

		if ( ! empty( $category_id ) && $category_id !== 0 ) {
			$dbhandler = new BM_DBhandler();
			$services  = $dbhandler->get_all_result(
				'SERVICE',
				'*',
				array(
					'is_service_front' => 1,
					'service_category' => $category_id,
				),
				'results',
				0,
				false,
				'service_position',
				'DESC'
			);
		}

		return $services;
	}//end bm_fetch_services_by_category_id()


	/**
	 * Fetch all service ids
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_service_ids( $type = 'array' ) {
		$dbhandler   = new BM_DBhandler();
		$service_ids = array();
		$services    = $dbhandler->get_all_result( 'SERVICE', '*', 1, 'results', 0, false, 'service_position', false );

		if ( isset( $services ) && ! empty( $services ) ) {
			$service_ids = wp_list_pluck( $services, 'id' );
		}

		if ( ! empty( $service_ids ) && is_array( $service_ids ) && $type == 'string' ) {
			$service_ids = implode( ',', $service_ids );
		}

		return $service_ids;
	}//end bm_fetch_all_service_ids()


	/**
	 * Fetch default columns
	 *
	 * @author Darpan
	 */
	public function bm_fetch_active_columns( $view_type ) {
		$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;
		$user_id        = get_current_user_id();
		$dbhandler      = new BM_DBhandler();
		$language       = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$active_columns = $dbhandler->get_all_result(
			'MANAGECOLUMNS',
			'default_columns',
			array(
				'language'  => $language,
				'user_id'   => $user_id,
				'view_type' => $view_type,
				'is_admin'  => $is_admin,
			),
			'var',
			0,
			1,
			'id',
			'DESC'
		);

		if ( ! empty( $active_columns ) ) {
			$active_columns = maybe_unserialize( $active_columns );
		} else {
			switch ( $view_type ) {
				case 'orders':
					$active_columns = array(
						'serial_no'                  => esc_html__( 'Serial No', 'service-booking' ),
						'service_name'               => esc_html__( 'Ordered Service', 'service-booking' ),
						'booking_created_at'         => esc_html__( 'Ordered Date', 'service-booking' ),
						'booking_date'               => esc_html__( 'Service Date', 'service-booking' ),
						'first_name'                 => esc_html__( 'First Name', 'service-booking' ),
						'last_name'                  => esc_html__( 'Last Name', 'service-booking' ),
						'contact_no'                 => esc_html__( 'Contact Number', 'service-booking' ),
						'email_address'              => esc_html__( 'Email', 'service-booking' ),
						'service_participants'       => esc_html__( 'Service Participants', 'service-booking' ),
						'extra_service_participants' => esc_html__( 'Extra Service Participants', 'service-booking' ),
						'service_cost'               => esc_html__( 'Service Cost', 'service-booking' ),
						'extra_service_cost'         => esc_html__( 'Extra Service Cost', 'service-booking' ),
						'discount'                   => esc_html__( 'Discount', 'service-booking' ),
						'total_cost'                 => esc_html__( 'Total Cost', 'service-booking' ),
						'customer_data'              => esc_html__( 'Customer Data', 'service-booking' ),
						'ordered_from'               => esc_html__( 'Ordered From', 'service-booking' ),
						'order_status'               => esc_html__( 'Order Status', 'service-booking' ),
						'order_attachments'          => esc_html__( 'Order Attachments', 'service-booking' ),
						'payment_status'             => esc_html__( 'Payment Status', 'service-booking' ),
						'actions'                    => esc_html__( 'Actions', 'service-booking' ),
					);
					break;
				case 'checkin':
					$active_columns = array(
						'serial_no'      => esc_html__( 'Serial No', 'service-booking' ),
						'service_name'   => esc_html__( 'Ordered Service', 'service-booking' ),
						'booking_date'   => esc_html__( 'Service Date', 'service-booking' ),
						'first_name'     => esc_html__( 'Attendee First Name', 'service-booking' ),
						'last_name'      => esc_html__( 'Attendee Last Name', 'service-booking' ),
						'contact_no'     => esc_html__( 'Attendee Contact', 'service-booking' ),
						'email_address'  => esc_html__( 'Attendee Email', 'service-booking' ),
						'total_cost'     => esc_html__( 'Order Cost', 'service-booking' ),
						'checkin_time'   => esc_html__( 'Checkin Time', 'service-booking' ),
						'checkin_status' => esc_html__( 'Checkin Status', 'service-booking' ),
						'ticket_pdf'     => esc_html__( 'Ticket PDF', 'service-booking' ),
						'actions'        => esc_html__( 'Actions', 'service-booking' ),
					);
					break;
				default:
					$active_columns = array();
			}
		} //end if

		return $active_columns;
	}//end bm_fetch_active_columns()


    /**
     * Create pdf content on plugin activation
     *
     * @author Darpan
     */
    public function bm_create_default_pdf_contents() {
        $dbhandler = new BM_DBhandler();

        /* -------------------------
        * BOOKING PDF – ENGLISH
        * ------------------------- */
        $booking_pdf_en = '
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        {{logo}}
        
        <h1 style="color: #333; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
            Order Confirmation
        </h1>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Booking Reference:</strong> {{booking_key}}<br>
            <strong>Date:</strong> {{date}} at {{time}}
        </div>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Customer Information</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_first_name}} {{billing_last_name}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_email}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Phone:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_contact}}</td>
            </tr>
            <tr>
                <td style="padding: 8px;"><strong>Address:</strong></td>
                <td style="padding: 8px;">{{billing_address}}</td>
            </tr>
        </table>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Service Details</h2>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <strong>Service:</strong> {{service_name}}<br>
            <strong>Date:</strong> {{booking_date}}<br>
            <strong>Time:</strong> {{booking_slots}}<br>
            <strong>Duration:</strong> {{service_duration}}<br>
            <strong>Payment Method:</strong> {{payment_method}}
        </div>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Order Summary</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <tr style="background: #4A90E2; color: white;">
                <th style="padding: 12px; text-align: left;">Description</th>
                <th style="padding: 12px; text-align: center;">Qty</th>
                <th style="padding: 12px; text-align: right;">Price</th>
                <th style="padding: 12px; text-align: right;">Total</th>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #ddd;">{{service_name}}</td>
                <td style="padding: 12px; border-bottom: 1px solid #ddd; text-align: center;">{{service_qty}}</td>
                <td style="padding: 12px; border-bottom: 1px solid #ddd; text-align: right;">{{service_price}}</td>
                <td style="padding: 12px; border-bottom: 1px solid #ddd; text-align: right;">{{service_total}}</td>
            </tr>
            
            {{extra_services}}
            
            <tr>
                <td colspan="3" style="padding: 12px; text-align: right;"><strong>Subtotal:</strong></td>
                <td style="padding: 12px; text-align: right;">{{subtotal}}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 12px; text-align: right;"><strong>Discount:</strong></td>
                <td style="padding: 12px; text-align: right; color: #27AE60;">-{{disount_amount}}</td>
            </tr>
            <tr style="background: #f5f5f5;">
                <td colspan="3" style="padding: 12px; text-align: right;"><strong>Total Amount:</strong></td>
                <td style="padding: 12px; text-align: right; font-weight: bold;">{{total_cost}}</td>
            </tr>
        </table>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Infant Discount:</strong><br>
            {{infant_count}} infant(s) - Discount: {{infant_discount}}
        </div>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Child Discount:</strong><br>
            {{child_count}} child(ren) - Discount: {{child_discount}}
        </div>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Adult Discount:</strong><br>
            {{adult_count}} adult(s) - Discount: {{adult_discount}}
        </div>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Senior Discount:</strong><br>
            {{senior_count}} senior(s) - Discount: {{senior_discount}}
        </div>
        
        <div style="background: #E8F5E8; border-left: 4px solid #27AE60; padding: 15px; margin: 20px 0;">
            <strong>Coupon Applied:</strong><br>
            {{coupons}}
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <h3>Booking Verification QR Code</h3>
            <p>Present this QR code at the time of service</p>
            {{qr_code}}
        </div>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>Thank you for your booking!</p>
            <p>For any questions, please contact our customer service at {{admin_email}}.</p>
            <p>© {{current_year}} Your Company Name. All rights reserved.</p>
        </div>
    </div>';

        /* -------------------------
        * BOOKING PDF – ITALIAN
        * ------------------------- */
        $booking_pdf_it = '
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        {{logo}}
        
        <h1 style="color: #333; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
            Conferma Ordine
        </h1>
        
        <div style="background: #f5f5f5; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <strong>Riferimento Prenotazione:</strong> {{booking_key}}<br>
            <strong>Data:</strong> {{current_date}} alle {{current_time}}
        </div>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Informazioni Cliente</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Nome:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_first_name}} {{billing_last_name}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_email}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Telefono:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_contact}}</td>
            </tr>
            <tr>
                <td style="padding: 8px;"><strong>Indirizzo:</strong></td>
                <td style="padding: 8px;">{{billing_address}}</td>
            </tr>
        </table>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Dettagli Servizio</h2>
        <div style="background: #f9f9f9; padding: 15px; border-radius: 5px; margin: 15px 0;">
            <strong>Servizio:</strong> {{service_name}}<br>
            <strong>Data:</strong> {{booking_date}}<br>
            <strong>Ora:</strong> {{booking_slots}}<br>
            <strong>Durata:</strong> {{service_duration}}<br>
            <strong>Metodo di Pagamento:</strong> {{payment_method}}
        </div>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Riepilogo Ordine</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <tr style="background: #4A90E2; color: white;">
                <th style="padding: 12px; text-align: left;">Descrizione</th>
                <th style="padding: 12px; text-align: center;">Qtà</th>
                <th style="padding: 12px; text-align: right;">Prezzo</th>
                <th style="padding: 12px; text-align: right;">Totale</th>
            </tr>
            <tr>
                <td style="padding: 12px; border-bottom: 1px solid #ddd;">{{service_name}}</td>
                <td style="padding: 12px; border-bottom: 1px solid #ddd; text-align: center;">{{service_qty}}</td>
                <td style="padding: 12px; border-bottom: 1px solid #ddd; text-align: right;">{{service_price}}</td>
                <td style="padding: 12px; border-bottom: 1px solid #ddd; text-align: right;">{{service_total}}</td>
            </tr>
            
            {{extra_services}}
            
            <tr>
                <td colspan="3" style="padding: 12px; text-align: right;"><strong>Subtotale:</strong></td>
                <td style="padding: 12px; text-align: right;">{{subtotal}}</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 12px; text-align: right;"><strong>Sconto:</strong></td>
                <td style="padding: 12px; text-align: right; color: #27AE60;">-{{disount_amount}}</td>
            </tr>
            <tr style="background: #f5f5f5;">
                <td colspan="3" style="padding: 12px; text-align: right;"><strong>Totale:</strong></td>
                <td style="padding: 12px; text-align: right; font-weight: bold;">{{total_cost}}</td>
            </tr>
        </table>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Sconto Neonati:</strong><br>
            {{infant_count}} neonato/i - Sconto: {{infant_discount}}
        </div>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Sconto Bambini:</strong><br>
            {{child_count}} bambino/i - Sconto: {{child_discount}}
        </div>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Sconto Adulti:</strong><br>
            {{adult_count}} adulto/i - Sconto: {{adult_discount}}
        </div>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 20px 0;">
            <strong>Sconto Anziani:</strong><br>
            {{senior_count}} anziano/i - Sconto: {{senior_discount}}
        </div>
        
        <div style="background: #E8F5E8; border-left: 4px solid #27AE60; padding: 15px; margin: 20px 0;">
            <strong>Coupon Applicato:</strong><br>
            {{coupons}}
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <h3>Codice QR di Verifica Prenotazione</h3>
            <p>Presentare questo codice QR al momento del servizio</p>
            {{qr_code}}
        </div>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>Grazie per la tua prenotazione!</p>
            <p>Per qualsiasi domanda, contatta il nostro servizio clienti all\'indirizzo {{admin_email}}.</p>
            <p>© {{current_year}} Nome Azienda. Tutti i diritti riservati.</p>
        </div>
    </div>';

        /* -------------------------
        * VOUCHER PDF – ENGLISH
        * ------------------------- */
        $voucher_pdf_en = '
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px dashed #4A90E2;">
        {{logo}}
        
        <h1 style="color: #4A90E2; text-align: center; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
            SERVICE VOUCHER
        </h1>
        
        <div style="text-align: center; margin: 30px 0; font-size: 18px;">
            <strong>VOUCHER CODE:</strong><br>
            <span style="font-size: 24px; font-weight: bold; color: #27AE60;">{{voucher_code}}</span>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Customer:</strong></td>
                <td style="padding: 12px;">{{billing_first_name}} {{billing_last_name}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Service:</strong></td>
                <td style="padding: 12px;">{{service_name}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Service Date:</strong></td>
                <td style="padding: 12px;">{{booking_date}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Service Time:</strong></td>
                <td style="padding: 12px;">{{booking_slots}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Voucher Value:</strong></td>
                <td style="padding: 12px; font-weight: bold;">{{total_cost}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Redeemed On:</strong></td>
                <td style="padding: 12px;">{{redeemed_date}}</td>
            </tr>
        </table>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 30px 0;">
            <h3 style="margin-top: 0;">Important Instructions:</h3>
            <ul>
                <li>Present this voucher at the time of service</li>
                <li>Voucher must be redeemed on the scheduled service date</li>
                <li>Valid for one-time use only</li>
                <li>Non-transferable and non-refundable</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>For assistance, contact: {{admin_email}} | {{admin_phone}}</p>
            <p>Voucher generated on: {{current_date}}</p>
        </div>
    </div>';

        /* -------------------------
        * VOUCHER PDF – ITALIAN
        * ------------------------- */
        $voucher_pdf_it = '
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; border: 2px dashed #4A90E2;">
        {{logo}}
        
        <h1 style="color: #4A90E2; text-align: center; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
            VOUCHER SERVIZIO
        </h1>
        
        <div style="text-align: center; margin: 30px 0; font-size: 18px;">
            <strong>CODICE VOUCHER:</strong><br>
            <span style="font-size: 24px; font-weight: bold; color: #27AE60;">{{voucher_code}}</span>
        </div>
        
        <table style="width: 100%; border-collapse: collapse; margin: 20px 0;">
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Cliente:</strong></td>
                <td style="padding: 12px;">{{billing_first_name}} {{billing_last_name}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Servizio:</strong></td>
                <td style="padding: 12px;">{{service_name}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Data Servizio:</strong></td>
                <td style="padding: 12px;">{{booking_date}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Ora Servizio:</strong></td>
                <td style="padding: 12px;">{{booking_slots}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Valore Voucher:</strong></td>
                <td style="padding: 12px; font-weight: bold;">{{total_cost}}</td>
            </tr>
            <tr>
                <td style="padding: 12px; background: #f5f5f5;"><strong>Riscattato il:</strong></td>
                <td style="padding: 12px;">{{redeemed_date}}</td>
            </tr>
        </table>
        
        <div style="background: #FFF5E5; border-left: 4px solid #ffa500; padding: 15px; margin: 30px 0;">
            <h3 style="margin-top: 0;">Istruzioni Importanti:</h3>
            <ul>
                <li>Presentare questo voucher al momento del servizio</li>
                <li>Il voucher deve essere riscattato nella data programmata del servizio</li>
                <li>Valido per un solo utilizzo</li>
                <li>Non trasferibile e non rimborsabile</li>
            </ul>
        </div>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>Per assistenza, contattare: {{admin_email}} | {{admin_phone}}</p>
            <p>Voucher generato il: {{current_date}}</p>
        </div>
    </div>';

        /* -------------------------
        * INSERT DEFAULT PDF CONTENT
        * ------------------------- */
        $tmpl_data = array(
            'id'             => 1,
            'booking_pdf_en' => $booking_pdf_en,
            'booking_pdf_it' => $booking_pdf_it,
            'voucher_pdf_en' => $voucher_pdf_en,
            'voucher_pdf_it' => $voucher_pdf_it,
            'pdf_logo_guid'  => 0,
            'created_at'     => current_time( 'mysql' ),
            'updated_at'     => current_time( 'mysql' ),
        );

        $tmpl_arg = array(
            '%d',
            '%s',
            '%s',
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
        );

        /* -------------------------
		* CUSTOMER INFO PDF – ENGLISH
		* ------------------------- */
		$customer_info_pdf_en = '
    <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
        {{logo}}
        
        <h1 style="color: #333; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
            Customer Information
        </h1>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Personal Details</h2>
        <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>First Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_first_name}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Last Name:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_last_name}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_email}}</td>
            </tr>
            <tr>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Phone:</strong></td>
                <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_contact}}</td>
            </tr>
            <tr>
                <td style="padding: 8px;"><strong>Address:</strong></td>
                <td style="padding: 8px;">{{billing_address}}</td>
            </tr>
        </table>
        
        <h2 style="color: #4A90E2; margin-top: 30px;">Account Information</h2>
        <p><strong>Customer since:</strong> {{customer_since}}</p>
        <p><strong>Total bookings:</strong> {{total_bookings}}</p>
        
        <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
            <p>© {{current_year}} Your Company Name. All rights reserved.</p>
        </div>
    </div>';

        /* -------------------------
        * CUSTOMER INFO PDF – ITALIAN
        * ------------------------- */
        $customer_info_pdf_it = '
        <div style="font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px;">
            {{logo}}
            
            <h1 style="color: #333; border-bottom: 2px solid #4A90E2; padding-bottom: 10px;">
                Informazioni Cliente
            </h1>
            
            <h2 style="color: #4A90E2; margin-top: 30px;">Dettagli Personali</h2>
            <table style="width: 100%; border-collapse: collapse; margin: 15px 0;">
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Nome:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_first_name}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Cognome:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_last_name}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Email:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_email}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;"><strong>Telefono:</strong></td>
                    <td style="padding: 8px; border-bottom: 1px solid #ddd;">{{billing_contact}}</td>
                </tr>
                <tr>
                    <td style="padding: 8px;"><strong>Indirizzo:</strong></td>
                    <td style="padding: 8px;">{{billing_address}}</td>
                </tr>
            </table>
            
            <h2 style="color: #4A90E2; margin-top: 30px;">Informazioni Account</h2>
            <p><strong>Cliente dal:</strong> {{customer_since}}</p>
            <p><strong>Prenotazioni totali:</strong> {{total_bookings}}</p>
            
            <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 1px solid #ddd; color: #666; font-size: 12px;">
                <p>© {{current_year}} Nome Azienda. Tutti i diritti riservati.</p>
            </div>
        </div>';

        $tmpl_data = array(
            'id'                   => 1,
            'booking_pdf_en'       => $booking_pdf_en,
            'booking_pdf_it'       => $booking_pdf_it,
            'voucher_pdf_en'       => $voucher_pdf_en,
            'voucher_pdf_it'       => $voucher_pdf_it,
            'customer_info_pdf_en' => $customer_info_pdf_en,
            'customer_info_pdf_it' => $customer_info_pdf_it,
            'pdf_logo_guid'        => 0,
            'created_at'           => current_time( 'mysql' ),
            'updated_at'           => current_time( 'mysql' ),
        );

        $tmpl_arg = array(
            '%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
			'%s',
        );

        $dbhandler->insert_row( 'PDF_CUSTOMIZATION', $tmpl_data, $tmpl_arg );
        $dbhandler->update_global_option_value( 'bm_pdf_contents_created', '1' );
    } //end bm_create_default_pdf_contents()


	/**
	 * Fetch column order and names
	 *
	 * @author Darpan
	 */
	public function bm_fetch_column_order_and_names( $view_type ) {
		$is_admin             = current_user_can( 'manage_options' ) ? 1 : 0;
		$user_id              = get_current_user_id();
		$dbhandler            = new BM_DBhandler();
		$language             = $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' );
		$screen_options       = $dbhandler->get_all_result(
			'MANAGECOLUMNS',
			'screen_options',
			array(
				'language'  => $language,
				'user_id'   => $user_id,
				'view_type' => $view_type,
				'is_admin'  => $is_admin,
			),
			'var',
			0,
			1,
			'id',
			'DESC'
		);
		$active_column_values = array();

		if ( ! empty( $screen_options ) ) {
			$screen_options = maybe_unserialize( $screen_options );

			if ( ! empty( $screen_options ) ) {
				foreach ( $screen_options as $key => $screen_option ) {
					$active_column_values[ $key ] = $screen_option;
				}
			}
		} else {
			switch ( $view_type ) {
				case 'orders':
					$active_column_values = array(
						esc_html__( 'Serial No', 'service-booking' )       => array(
							'order'  => '1',
							'column' => 'serial_no',
						),
						esc_html__( 'Ordered Service', 'service-booking' ) => array(
							'order'  => '2',
							'column' => 'service_name',
						),
						esc_html__( 'Ordered Date', 'service-booking' )    => array(
							'order'  => '3',
							'column' => 'booking_created_at',
						),
						esc_html__( 'Service Date', 'service-booking' )    => array(
							'order'  => '4',
							'column' => 'booking_date',
						),
						esc_html__( 'First Name', 'service-booking' )      => array(
							'order'  => '5',
							'column' => 'first_name',
						),
						esc_html__( 'Last Name', 'service-booking' )       => array(
							'order'  => '6',
							'column' => 'last_name',
						),
						esc_html__( 'Contact Number', 'service-booking' )  => array(
							'order'  => '7',
							'column' => 'contact_no',
						),
						esc_html__( 'Email', 'service-booking' )           => array(
							'order'  => '8',
							'column' => 'email_address',
						),
						esc_html__( 'Service Participants', 'service-booking' )    => array(
							'order'  => '9',
							'column' => 'service_participants',
						),
						esc_html__( 'Extra Service Participants', 'service-booking' )    => array(
							'order'  => '10',
							'column' => 'extra_service_participants',
						),
						esc_html__( 'Service Cost', 'service-booking' )      => array(
							'order'  => '11',
							'column' => 'service_cost',
						),
						esc_html__( 'Extra Service Cost', 'service-booking' )      => array(
							'order'  => '12',
							'column' => 'extra_service_cost',
						),
						esc_html__( 'Discount', 'service-booking' )    => array(
							'order'  => '13',
							'column' => 'discount',
						),
						esc_html__( 'Total Cost', 'service-booking' )      => array(
							'order'  => '14',
							'column' => 'total_cost',
						),
						esc_html__( 'Customer Data', 'service-booking' )   => array(
							'order'  => '15',
							'column' => 'customer_data',
						),
						esc_html__( 'Ordered From', 'service-booking' )    => array(
							'order'  => '16',
							'column' => 'ordered_from',
						),
						esc_html__( 'Order Status', 'service-booking' )    => array(
							'order'  => '17',
							'column' => 'order_status',
						),
						esc_html__( 'Order Attachments', 'service-booking' )    => array(
							'order'  => '18',
							'column' => 'order_attachments',
						),
						esc_html__( 'Payment Status', 'service-booking' )    => array(
							'order'  => '19',
							'column' => 'payment_status',
						),
						esc_html__( 'Actions', 'service-booking' )         => array(
							'order'  => '20',
							'column' => 'actions',
						),
					);
					break;
				case 'checkin':
					$active_column_values = array(
						esc_html__( 'Serial No', 'service-booking' )       => array(
							'order'  => '1',
							'column' => 'serial_no',
						),
						esc_html__( 'Ordered Service', 'service-booking' ) => array(
							'order'  => '2',
							'column' => 'service_name',
						),
						esc_html__( 'Service Date', 'service-booking' )    => array(
							'order'  => '3',
							'column' => 'booking_date',
						),
						esc_html__( 'Attendee First Name', 'service-booking' )      => array(
							'order'  => '4',
							'column' => 'first_name',
						),
						esc_html__( 'Attendee Last Name', 'service-booking' )       => array(
							'order'  => '5',
							'column' => 'last_name',
						),
						esc_html__( 'Attendee Contact', 'service-booking' )  => array(
							'order'  => '6',
							'column' => 'contact_no',
						),
						esc_html__( 'Attendee Email', 'service-booking' )           => array(
							'order'  => '7',
							'column' => 'email_address',
						),
						esc_html__( 'Order Cost', 'service-booking' )      => array(
							'order'  => '8',
							'column' => 'total_cost',
						),
						esc_html__( 'Checkin Time', 'service-booking' )    => array(
							'order'  => '9',
							'column' => 'checkin_time',
						),
						esc_html__( 'Checkin Status', 'service-booking' )    => array(
							'order'  => '10',
							'column' => 'checkin_status',
						),
						esc_html__( 'Ticket PDF', 'service-booking' )    => array(
							'order'  => '11',
							'column' => 'ticket_pdf',
						),
						esc_html__( 'Actions', 'service-booking' )         => array(
							'order'  => '12',
							'column' => 'actions',
						),
					);
					break;
				default:
					$active_column_values = array();
			} //end switch
		} //end if

		return $active_column_values;
	}//end bm_fetch_column_order_and_names()


	/**
	 * Fetch columns for dynamic selection
	 *
	 * @author Darpan
	 */
	public function bm_fetch_columns_screen_options( $view_type, $resp = '' ) {
		if ( ! empty( $view_type ) ) {
			$is_admin       = current_user_can( 'manage_options' ) ? 1 : 0;
			$columns        = $this->bm_fetch_column_order_and_names( $view_type );
			$active_columns = array_keys( $this->bm_fetch_active_columns( $view_type ) );

			if ( ! empty( $columns ) ) {
				$resp .= '<div id="available_columns" class="ui-sortable">';
				$resp .= '<input type="hidden" name="is_admin" id="is_admin" value="' . $is_admin . '">';
				$resp .= '<input type="hidden" name="view_type" id="view_type" value="' . $view_type . '">';
				$i     = 1;

				foreach ( $columns as $key => $column ) {
					$resp .= '<div class="checkbox_list ui-sortable-handle">';
					$resp .= '<input type="checkbox" id="box_' . $i . '" order="' . $column['order'] . '" name="' . $column['column'] . '"  ' . ( ! empty( $active_columns ) && in_array( $column['column'], $active_columns ) ? 'checked' : '' ) . '>';
					$resp .= '<label for="box_' . $i . '">' . $key . '</label>';
					$resp .= '</div>';
					++$i;
				}

				$resp .= '</div>';
				/**$resp .= $this->bm_fetch_button_with_text( 'column_button_tag', 0, 'Save', 'submit_columns', 'order_column_button' );
				$resp .= '<div class="column_errortext" style="display :none;"></div>';*/
			}
		} //end if

		return $resp;
	}//end bm_fetch_columns_screen_options()


	/**
	 * Filter results as per date
	 *
	 * @author Darpan
	 */
	public function bm_filter_results_by_date( $results, $from_date, $to_date, $column_name ) {
		$filtered_results = array();

		if ( ! empty( $results ) && is_array( $results ) ) {
			foreach ( $results as $result ) {
				$columnSelection = is_object( $result ) ? $result->$column_name : $result[ $column_name ];
				$is_match        = true;

				if ( ! empty( $from_date ) && isset( $columnSelection ) && strtotime( $columnSelection ) < strtotime( $from_date ) ) {
					$is_match = false;
				}

				if ( ! empty( $to_date ) && isset( $columnSelection ) && strtotime( $columnSelection ) > strtotime( $to_date ) ) {
					$is_match = false;
				}

				if ( $is_match ) {
					$filtered_results[] = $result;
				}
			}
		}

		return $filtered_results;
	}//end bm_filter_results_by_date()


	/**
	 * Fetch searched data form a existing data
	 *
	 * @author Darpan
	 */
	public function bm_search_string_from_existing_dataset( $data, $search_string ) {
		$results = array();

		if ( is_array( $data ) && ! empty( $search_string ) ) {
			foreach ( $data as $result ) {
				if ( is_array( $result ) ) {
					$searchResult = array_filter(
						$result,
						function ( $value ) use ( $search_string ) {
							if ( is_string( $value ) || is_numeric( $value ) ) {
								$is_string = stripos( $value, $search_string ) !== false;
								if ( $is_string ) {
									return $is_string;
								}
							} elseif ( is_array( $value ) ) {
								foreach ( $value as $data ) {
									$is_string = stripos( $data, $search_string ) !== false;
									if ( $is_string ) {
										return $is_string;
									}
								}
							}
						}
					);

					if ( ! empty( $searchResult ) ) {
						$results[] = $result;
					}
				} elseif ( is_string( $result ) || is_numeric( $result ) ) {
					if ( stripos( $result, $search_string ) !== false ) {
						$results[] = $result;
					}
				} //end if
			} //end foreach
		} //end if

		return $results;
	}//end bm_search_string_from_existing_dataset()


	/**
	 * Fetch searched data form a serialized column
	 *
	 * @author Darpan
	 */
	public function bm_search_data_from_serialized_column( $data, $columname, $search_string ) {
		$results = array();

		if ( is_array( $data ) && ! empty( $columname ) && ! empty( $search_string ) ) {
			foreach ( $data as $result ) {
				$unserialized_data = maybe_unserialize( $result->$columname );

				if ( is_array( $unserialized_data ) ) {
					$searchResult = array_filter(
						$unserialized_data,
						function ( $value ) use ( $search_string ) {
							if ( is_string( $value ) ) {
								$is_string = stripos( $value, $search_string ) !== false;
								if ( $is_string ) {
									return $is_string;
								}
							} elseif ( is_array( $value ) ) {
								foreach ( $value as $data ) {
									$is_string = stripos( $data, $search_string ) !== false;
									if ( $is_string ) {
										return $is_string;
									}
								}
							}
						}
					);

					if ( ! empty( $searchResult ) ) {
						$results[] = $result;
					}
				} elseif ( is_string( $unserialized_data ) && stripos( $unserialized_data, $search_string ) !== false ) {
						$results[] = $result;
				} //end if
			} //end foreach
		} //end if

		return $results;
	}//end bm_search_data_from_serialized_column()


	/**
	 * Fetch service response for service search shortcode
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_response( $service, $date, $type = '', $resp = '' ) {
		if ( ! empty( $service ) && isset( $service->id ) && $this->bm_service_is_bookable( $service->id, $date ) ) {
			$service_id = ! empty( $service ) && isset( $service->id ) ? esc_attr( $service->id ) : 0;

			if ( empty(
				$this->bm_fetch_service_time_slot_array_by_service_id(
					array(
						'id'   => $service_id,
						'date' => $date,
					)
				)
			) ) {
				return $resp;
			}

			$dbhandler          = new BM_DBhandler();
			$svc_total_cap_left = $dbhandler->get_all_result(
				'SLOTCOUNT',
				'svc_total_cap_left',
				array(
					'service_id'   => esc_attr( $service_id ),
					'booking_date' => esc_html( $date ),
					'is_active'    => 1,
				),
				'var',
				0,
				1,
				'id',
				'DESC'
			);

			$gallery_images = $dbhandler->get_all_result(
				'GALLERY',
				'id',
				array(
					'module_type' => 'SERVICE',
					'module_id'   => $service_id,
				),
				'var'
			);

			$booking_currency   = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
			$svc_img            = esc_url( $this->bm_fetch_image_url_or_guid( $service_id, 'SERVICE', 'url' ) );
			$svc_name           = ! empty( $service ) && isset( $service->service_name ) && ! empty( $service->service_name ) ? mb_strimwidth( esc_html( $service->service_name ), 0, 20, '...' ) : 'N/A';
			$category_name      = $this->bm_fetch_category_name_by_service_id( $service_id );
			$svc_desc           = ! empty( $service ) && isset( $service->service_desc ) && ! empty( $service->service_desc ) ? wp_strip_all_tags( ( wp_kses_post( stripslashes( ( $service->service_desc ) ) ) ) ) : 'N/A';
			$svc_long_desc      = ! empty( $service ) && isset( $service->service_desc ) && ! empty( $service->service_desc ) ? do_shortcode( wp_kses_post( stripslashes( ( $service->service_desc ) ) ) ) : 'N/A';
			$svc_short_desc     = ! empty( $service ) && isset( $service->service_short_desc ) && ! empty( $service->service_short_desc ) ? wp_strip_all_tags( ( wp_kses_post( stripslashes( ( $service->service_short_desc ) ) ) ) ) : 'N/A';
			$svc_duration       = ! empty( $service ) && isset( $service->service_duration ) && ! empty( $service->service_duration ) ? esc_html( $this->bm_fetch_float_to_time_string( $service->service_duration ) ) : 'N/A';
			$wc_product_id      = ! empty( $service ) && isset( $service->wc_product ) ? esc_attr( $service->wc_product ) : 0;
			$svc_default_price  = ! empty( $service ) && isset( $service->default_price ) && ! empty( $service->default_price ) ? esc_attr( $service->default_price ) : '';
			$svc_default_price1 = $this->bm_fetch_price_in_global_settings_format( $svc_default_price, true );
			$svc_price          = $this->bm_fetch_service_price_by_service_id_and_date( $service_id, $date );
			$svc_price1         = $this->bm_fetch_service_price_by_service_id_and_date( $service_id, $date, 'global_format' );
			$stopsales          = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );
			$show_svc_img       = $dbhandler->get_global_option_value( 'bm_show_frontend_service_image', 0 ) == 0 ? 'hide_div' : '';
			$show_read_more     = $dbhandler->get_global_option_value( 'bm_show_frontend_service_desc_read_more_button', 0 ) == 0 ? 'hide_div' : '';
			$show_svc_price     = $dbhandler->get_global_option_value( 'bm_show_frontend_service_price', 0 ) == 0 ? 'hide_div' : '';
			$show_duration      = $dbhandler->get_global_option_value( 'bm_show_frontend_service_duration', 0 ) == 0 ? 'hide_div' : '';
			$show_svc_desc      = $dbhandler->get_global_option_value( 'bm_show_frontend_service_description', 0 ) == 0 ? 'hide_div' : '';
			$svc_name_colour    = $dbhandler->get_global_option_value( 'bm_frontend_service_title_color', '#000000' );
			$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', '#000000' );
			$price_text_colour  = $dbhandler->get_global_option_value( 'bm_frontend_service_price_text_color', '#000000' );
			$svc_title_font     = $dbhandler->get_global_option_value( 'bm_service_title_font', '20' ) . 'px';
			$svc_shrt_desc_font = $dbhandler->get_global_option_value( 'bm_service_shrt_desc_font', '14' ) . 'px';
			$svc_price_txt_font = $dbhandler->get_global_option_value( 'bm_service_price_txt_font', '16' ) . 'px';
			$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', '#ffffff' );
			$inactive_show_more = $svc_desc == 'N/A' ? 'hide_div' : '';
			$show_more_title    = $svc_desc == 'N/A' ? '' : __( 'Show full description', 'service-booking' );
			$gallery_title      = __( 'Show Gallery Images', 'service-booking' );
			$category_title     = __( ' category: ', 'service-booking' );
			$per_person_text    = __( '/person', 'service-booking' );
			$stopsales_text     = __( 'Stopsales', 'service-booking' );
			$stopsales_title    = sprintf( esc_html__( 'Products are not bookable until %s after current time', 'service-booking' ), $this->bm_fetch_float_to_time_string( $stopsales ) );
			$duration_title     = sprintf( esc_html__( 'Service duration is %s', 'service-booking' ), $svc_duration );
			$book_text          = __( 'Book Now', 'service-booking' );
			$cap_exhausted_text = __( 'Slots Full', 'service-booking' );
			$no_description     = __( 'No short description available', 'service-booking' );
			$wc_not_activated   = __( 'Not Linked With WooCoomerce', 'service-booking' );
			$service_title      = esc_html( $service->service_name );
			$plugin_path        = plugin_dir_url( __DIR__ );

			$service_settings = isset( $service->service_settings ) && ! empty( $service->service_settings ) ? maybe_unserialize( $service->service_settings ) : array();

			if ( empty( $show_duration ) ) {
				$show_svc_duration = isset( $service_settings['show_service_duration'] ) ? $service_settings['show_service_duration'] : 0;
				$show_duration     = $show_svc_duration == 0 ? 'hide_div' : '';
			}

			$resp .= '<div class="searchproductbox main-parent">';
			$resp .= '<div class="productimg ' . $show_svc_img . '">';
			$resp .= '<img src="' . $svc_img . '" /> ';

			$resp .= '<div class="timebtnbox time-duration' . $show_duration . '" title="' . $duration_title . '">';
			$resp .= '<i class="fa fa-clock-o"></i> ' . $svc_duration;
			$resp .= '</div>';

			if ( ! empty( $gallery_images ) && isset( $gallery_images[0] ) ) {
				$resp .= '<span id="' . $service_id . '" class="timebtn gallery-btn" title="' . $gallery_title . '">';
				$resp .= '<i class="fa fa-picture-o"></i>';
				$resp .= '</span>';
			}

			$resp .= '<span class="category-tag" title="' . $category_title . $category_name . '">';
			$resp .= $category_name;
			$resp .= '</span>';

			$resp .= '</div>';
			$resp .= '<div class="productdescbar">';
			$resp .= '<h4 title="' . $service_title . '" style="color:' . $svc_name_colour . '!important;font-size:' . $svc_title_font . '!important;">' . $svc_name;
			$resp .= '<div class="service_full_description" data-title="' . $service_title . '">' . $svc_long_desc . '</div>';
			$resp .= '<img src="' . esc_url( $plugin_path . 'public/img/si_info-line.svg' ) . '" class="service-desc-fa ' . $show_read_more . ' ' . $inactive_show_more . '" title="' . $show_more_title . '"/>';
			$resp .= '</h4>';

			if ( $svc_short_desc != 'N/A' ) {
				$resp .= '<p class="paratext ' . $show_svc_desc . '"><span id="svc_desc_text" class="svc_desc_text" style="font-size:' . $svc_shrt_desc_font . '!important;">' . $svc_short_desc . '</span></p>';
			} else {
				$resp .= '<p class="paratext"><span id="svc_desc_text" class="svc_desc_text" style="font-size:' . $svc_shrt_desc_font . '!important;">' . $no_description . '</span></p>';
			}

			$resp .= '<div class="info_btn">';
			$resp .= '</div>';
			$resp .= '<div class="timebtnbar">';

			if ( ! empty( $stopsales ) && ( $service->show_stopsales_data == 1 ) ) {
				$resp .= '<span class="timebtnbox timebtn-Stopsales" title="' . $stopsales_title . '">';
				$resp .= '<i class="fa fa-clock-o"></i> ' . $this->bm_fetch_float_to_time_string( $stopsales );
				$resp .= '</span>';
			}

			$resp .= '</div>';
			$resp .= '<div class="pricetext textblue ' . $show_svc_price . '" style="font-size:' . $svc_price_txt_font . '!important;color:' . $price_text_colour . '!important;">';

			if ( $svc_default_price > $svc_price ) {
				$resp .= '<span class="variable_price_class" style="font-size:' . $svc_price_txt_font . '!important;">' . $svc_default_price1 . '</span>';
			}

			$resp .= $svc_price1 . '<span class="price_per_text">';

			if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
				$resp .= $per_person_text;
			}

			$resp .= '</span></div>';
			$resp .= '</div>';
			$resp .= '<div class="productbottombar">';

			$resp .= '<div class="booknowbtn' . ( $svc_total_cap_left == '0' ? ' readonly_div' : ' textblue bordercolor' ) . '"  style="background:' . $svc_button_colour . '!important">';
			$resp .= '<a href="#" id="' . ( $svc_total_cap_left == '0' ? '0' : $service_id ) . '" class="' . ( $svc_total_cap_left == '0' ? 'inactiveLink' : 'get_slot_details' ) . '" style="color:' . $svc_btn_txt_colour . '!important" ' . ( $type == 'mobile' || $this->isMobile() ? "data-mobile-date=$date" : '' ) . '>';
			if ( $svc_total_cap_left == '0' ) {
				$resp .= '<span class="slots_full_text">' . $cap_exhausted_text . '</span>';
			} else {
				$resp .= $book_text;
			}

			$resp .= '</a></div>';
			$resp .= '</div></div>';
		} //end if

		return wp_kses_post( $resp );
	}//end bm_fetch_service_response()


	/**
	 * Detect if mobile
	 *
	 * @author Darpan
	 */
	public function isMobile() {
		$userAgent = strtolower( $_SERVER['HTTP_USER_AGENT'] );
		return preg_match( '/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo|mobile/', $userAgent );
	}//end isMobile()


	/**
	 * Fetch service response service by category shortcode
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_by_category_response( $service, $resp = '', $shortcode_type = '' ) {
		$dbhandler          = new BM_DBhandler();
		$service_id         = ! empty( $service ) && isset( $service->id ) ? esc_attr( $service->id ) : 0;
		$booking_currency   = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
		$svc_name_colour    = $dbhandler->get_global_option_value( 'bm_frontend_service_title_color', '#000000' );
		$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', '#000000' );
		$price_text_colour  = $dbhandler->get_global_option_value( 'bm_frontend_service_price_text_color', '#000000' );
		$svc_title_font     = $dbhandler->get_global_option_value( 'bm_service_title_font', '20' ) . 'px';
		$svc_shrt_desc_font = $dbhandler->get_global_option_value( 'bm_service_shrt_desc_font', '14' ) . 'px';
		$svc_price_txt_font = $dbhandler->get_global_option_value( 'bm_service_price_txt_font', '16' ) . 'px';
		$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', '#ffffff' );

		$svc_img = esc_url( $this->bm_fetch_image_url_or_guid( $service_id, 'SERVICE', 'url' ) );

		if ( ! empty( $service ) && isset( $service->service_name ) && ! empty( $service->service_name ) ) {
			if ( $shortcode_type == 'service_by_id' ) {
				$svc_name = mb_strimwidth( esc_html( $service->service_name ), 0, 50, '...' );
			} else {
				$svc_name = mb_strimwidth( esc_html( $service->service_name ), 0, 16, '...' );
			}
		} else {
			$svc_name = 'N/A';
		}

		$gallery_images = $dbhandler->get_all_result(
			'GALLERY',
			'id',
			array(
				'module_type' => 'SERVICE',
				'module_id'   => $service_id,
			),
			'var'
		);

		$category_name      = $this->bm_fetch_category_name_by_service_id( $service_id );
		$svc_desc           = ! empty( $service ) && isset( $service->service_desc ) && ! empty( $service->service_desc ) ? wp_strip_all_tags( ( wp_kses_post( stripslashes( ( $service->service_desc ) ) ) ) ) : 'N/A';
		$svc_long_desc      = ! empty( $service ) && isset( $service->service_desc ) && ! empty( $service->service_desc ) ? do_shortcode( wp_kses_post( stripslashes( ( $service->service_desc ) ) ) ) : 'N/A';
		$svc_short_desc     = ! empty( $service ) && isset( $service->service_short_desc ) && ! empty( $service->service_short_desc ) ? wp_strip_all_tags( ( wp_kses_post( stripslashes( ( $service->service_short_desc ) ) ) ) ) : 'N/A';
		$svc_shrt_dsc_class = "Style='font-size:$svc_shrt_desc_font!important;'";
		$shrt_dsc_fa_class  = '';
		$book_button_class  = '';
		$service_card_class = '';
		$price_text_class   = "Style='font-size:$svc_price_txt_font!important;color:$price_text_colour!important;'";
		$service_title      = esc_html( $service->service_name );

		if ( $svc_desc != 'N/A' || strlen( $svc_desc ) > 18 ) {
			if ( $shortcode_type == 'service_by_id' ) {
				if ( strlen( $svc_desc ) > 250 ) {
					$svc_shrt_dsc_class = "Style='height:auto;width:97%;text-align:justify;font-size:$svc_shrt_desc_font!important;'";
					/**$shrt_dsc_fa_class  = "Style='position:absolute;bottom:38%;right:5%;clear:both;'";*/
					$book_button_class = "Style='background:$svc_button_colour'";
				} else {
					$book_button_class  = "Style='background:$svc_button_colour'";
					$service_card_class = "Style='margin-top: 2%;'";
				}
				if ( strlen( $svc_desc ) >= 56 && strlen( $svc_desc ) <= 250 ) {
					$book_button_class = "Style='background:$svc_button_colour'";
					$price_text_class  = "Style='margin-top:55px;font-size:$svc_price_txt_font!important;color:$price_text_colour!important;";
				}
			}
		}

		$svc_duration       = ! empty( $service ) && isset( $service->service_duration ) && ! empty( $service->service_duration ) ? esc_html( $this->bm_fetch_float_to_time_string( $service->service_duration ) ) : 'N/A';
		$svc_default_price  = ! empty( $service ) && isset( $service->default_price ) && ! empty( $service->default_price ) ? esc_attr( $service->default_price ) : '';
		$wc_product_id      = ! empty( $service ) && isset( $service->wc_product ) ? esc_attr( $service->wc_product ) : 0;
		$svc_default_price  = $this->bm_fetch_price_in_global_settings_format( $svc_default_price, true );
		$default_stopsales  = ! empty( $service ) && isset( $service->default_stopsales ) && ! empty( $service->default_stopsales ) ? esc_attr( $service->default_stopsales ) : '';
		$show_svc_img       = $dbhandler->get_global_option_value( 'bm_show_frontend_service_image', 0 ) == 0 ? 'hide_div' : '';
		$show_read_more     = $dbhandler->get_global_option_value( 'bm_show_frontend_service_desc_read_more_button', 0 ) == 0 ? 'hide_div' : '';
		$show_svc_price     = $dbhandler->get_global_option_value( 'bm_show_frontend_service_price', 0 ) == 0 ? 'hide_div' : '';
		$show_duration      = $dbhandler->get_global_option_value( 'bm_show_frontend_service_duration', 0 ) == 0 ? 'hide_div' : '';
		$show_svc_desc      = $dbhandler->get_global_option_value( 'bm_show_frontend_service_description', 0 ) == 0 ? 'hide_div' : '';
		$inactive_show_more = $svc_desc == 'N/A' ? 'hide_div' : '';
		$show_more_title    = $svc_desc == 'N/A' ? '' : __( 'Show full description', 'service-booking' );

		/**if ( $shortcode_type == 'service_by_id' ) {
			$inactive_show_more = strlen( $svc_desc ) < 375 ? 'hide_div' : '';
			$show_more_title    = strlen( $svc_desc ) < 375 ? '' : __( 'Show full description', 'service-booking' );
		}*/

		$service_settings = isset( $service->service_settings ) && ! empty( $service->service_settings ) ? maybe_unserialize( $service->service_settings ) : array();

		if ( empty( $show_duration ) ) {
			$show_svc_duration = isset( $service_settings['show_service_duration'] ) ? $service_settings['show_service_duration'] : 0;
			$show_duration     = $show_svc_duration == 0 ? 'hide_div' : '';
		}

		$gallery_title    = __( 'Show Gallery Images', 'service-booking' );
		$category_title   = __( 'category: ', 'service-booking' );
		$per_person_text  = __( '/person', 'service-booking' );
		$stopsales_title  = sprintf( esc_html__( 'Products are not bookable until %s after current time', 'service-booking' ), $this->bm_fetch_float_to_time_string( $default_stopsales ) );
		$duration_title   = sprintf( esc_html__( 'Service duration is %s', 'service-booking' ), $svc_duration );
		$book_text        = __( 'Book Now', 'service-booking' );
		$wc_not_activated = __( 'Not Linked With WooCoomerce', 'service-booking' );
		$no_description   = __( 'No short description available', 'service-booking' );
		$plugin_path      = plugin_dir_url( __DIR__ );

		$resp .= '<div class="service-card main-parent">';
		$resp .= '<div class="productimg ' . $show_svc_img . '">';
		$resp .= '<img src="' . $svc_img . '" />';

		$resp .= '<div class="timebtnbox time-duration' . $show_duration . '" title="' . $duration_title . '">';
		$resp .= '<i class="fa fa-clock-o"></i> ' . $svc_duration;
		$resp .= '</div>';

		if ( ! empty( $gallery_images ) && isset( $gallery_images[0] ) ) {
			$resp .= '<span id="' . $service_id . '" class="timebtn gallery-btn" title="' . $gallery_title . '">';
			$resp .= '<i class="fa fa-picture-o"></i> ';
			$resp .= '</span>';
		}

		$resp .= '<span class="category-tag" title="' . $category_title . $category_name . '">';
		$resp .= $category_name;
		$resp .= '</span>';
		$resp .= '</div>';
		$resp .= '<div class="sg-boobking-service-card-details" >';
		$resp .= '<div class="service-card-details">';
		$resp .= '<h2 title="' . esc_html( $service->service_name ) . '"  style="color:' . $svc_name_colour . '!important;font-size:' . $svc_title_font . '!important;">' . $svc_name;
		$resp .= '<div class="service_full_description" data-title="' . $service_title . '">' . $svc_long_desc . '</div>';
		$resp .= '<img src="' . esc_url( $plugin_path . 'public/img/si_info-line.svg' ) . '" class="service-desc-fa ' . $show_read_more . ' ' . $inactive_show_more . '" ' . $shrt_dsc_fa_class . ' title="' . $show_more_title . '"/>';
		$resp .= '</h2>';

		if ( $svc_short_desc != 'N/A' ) {
			$resp .= '<p class="paratext ' . $show_svc_desc . '"><span id="svc_desc_text" class="svc_desc_text" ' . $svc_shrt_dsc_class . '>' . $svc_short_desc . '</span></p>';
		} else {
			$resp .= '<p class="paratext"><span id="svc_desc_text" class="svc_desc_text" style="font-size:' . $svc_shrt_desc_font . '!important;">' . $no_description . '</span></p>';
		}

		$resp .= '<div class="timebtnbar">';

		if ( ! empty( $default_stopsales ) && $service->show_stopsales_data == 1 ) {
			$resp .= '<span class="timebtnbox timebtn-Stopsales" title="' . $stopsales_title . '">';
			$resp .= '<i class="fa fa-clock-o"></i> ' . $this->bm_fetch_float_to_time_string( $default_stopsales );
			$resp .= '</span>';
		}
		$resp .= '</div>';
		$resp .= '<div class="price-btn">';
		$resp .= '<div class="pricetext textblue ' . $show_svc_price . '" ' . $price_text_class . '>';

		$resp .= $svc_default_price . '<span class="price_per_text">';

		if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
			$resp .= $per_person_text;
		}

		$resp .= '</span></div>';
		$resp .= '</div>';
		$resp .= '</div>';

		$resp .= '<div class="productbottombar">';
		$resp .= '<div class="booknowbtn textblue bordercolor" ' . $book_button_class . '   style="background:' . $svc_button_colour . '!important">';
		$resp .= '<a href="#" id="' . $service_id . '" class="get_calendar_and_slot_details" style="color:' . $svc_btn_txt_colour . '!important">';
		$resp .= $book_text;
		$resp .= '</a></div>';
		$resp .= '</div>';

		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		return wp_kses_post( $resp );
	}//end bm_fetch_service_by_category_response()


	/**
	 * Fetch service by calendar shortcode
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_by_calendar_response( $service ) {
		$dbhandler  = new BM_DBhandler();
		$service_id = ! empty( $service ) && isset( $service->id ) ? esc_attr( $service->id ) : 0;
		$unbookable = false;

		if ( empty( $service ) || ! isset( $service->id ) ) {
			return false;
		}

		$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$today    = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$date     = $today->format( 'Y-m-d' );

		if ( ( $service_id <= 0 ) || ! $this->bm_service_is_bookable( $service->id, $date ) ) {
			$unbookable = true;
		}

		$svc_total_cap_left = $dbhandler->get_all_result(
			'SLOTCOUNT',
			'svc_total_cap_left',
			array(
				'service_id'   => esc_attr( $service_id ),
				'booking_date' => esc_html( $date ),
				'is_active'    => 1,
			),
			'var',
			0,
			1,
			'id',
			'DESC'
		);

		$svc_name_colour    = $dbhandler->get_global_option_value( 'bm_frontend_service_title_color', '#000000' );
		$svc_button_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', '#000000' );
		$svc_title_font     = $dbhandler->get_global_option_value( 'bm_service_title_font', '20' ) . 'px';
		$svc_shrt_desc_font = $dbhandler->get_global_option_value( 'bm_service_shrt_desc_font', '14' ) . 'px';
		$svc_price_txt_font = $dbhandler->get_global_option_value( 'bm_service_price_txt_font', '16' ) . 'px';
		$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', '#ffffff' );

		$svc_img = esc_url( $this->bm_fetch_image_url_or_guid( $service_id, 'SERVICE', 'url' ) );

		if ( ! empty( $service ) && isset( $service->service_name ) && ! empty( $service->service_name ) ) {
			$svc_name = mb_strimwidth( esc_html( $service->service_name ), 0, 16, '...' );
		} else {
			$svc_name = 'N/A';
		}

		$gallery_images = $dbhandler->get_all_result(
			'GALLERY',
			'id',
			array(
				'module_type' => 'SERVICE',
				'module_id'   => $service_id,
			),
			'var'
		);

		$category_name      = $this->bm_fetch_category_name_by_service_id( $service_id );
		$svc_desc           = ! empty( $service ) && isset( $service->service_desc ) && ! empty( $service->service_desc ) ? wp_strip_all_tags( ( wp_kses_post( stripslashes( ( $service->service_desc ) ) ) ) ) : 'N/A';
		$svc_long_desc      = ! empty( $service ) && isset( $service->service_desc ) && ! empty( $service->service_desc ) ? do_shortcode( wp_kses_post( stripslashes( ( $service->service_desc ) ) ) ) : 'N/A';
		$svc_short_desc     = ! empty( $service ) && isset( $service->service_short_desc ) && ! empty( $service->service_short_desc ) ? wp_strip_all_tags( ( wp_kses_post( stripslashes( ( $service->service_short_desc ) ) ) ) ) : 'N/A';
		$svc_shrt_dsc_class = "Style='font-size:$svc_shrt_desc_font!important;'";
		$shrt_dsc_fa_class  = '';
		$book_button_class  = "Style='background:$svc_button_colour'";
		$price_text_class   = "Style='font-size:$svc_price_txt_font!important;'";

		$svc_duration       = ! empty( $service ) && isset( $service->service_duration ) && ! empty( $service->service_duration ) ? esc_html( $this->bm_fetch_float_to_time_string( $service->service_duration ) ) : 'N/A';
		$svc_default_price  = ! empty( $service ) && isset( $service->default_price ) && ! empty( $service->default_price ) ? esc_attr( $service->default_price ) : '';
		$wc_product_id      = ! empty( $service ) && isset( $service->wc_product ) ? esc_attr( $service->wc_product ) : 0;
		$svc_default_price  = $this->bm_fetch_price_in_global_settings_format( $svc_default_price, true );
		$show_svc_img       = $dbhandler->get_global_option_value( 'bm_show_frontend_service_image', 0 ) == 0 ? 'hide_div' : '';
		$show_read_more     = $dbhandler->get_global_option_value( 'bm_show_frontend_service_desc_read_more_button', 0 ) == 0 ? 'hide_div' : '';
		$show_svc_price     = $dbhandler->get_global_option_value( 'bm_show_frontend_service_price', 0 ) == 0 ? 'hide_div' : '';
		$show_duration      = $dbhandler->get_global_option_value( 'bm_show_frontend_service_duration', 0 ) == 0 ? 'hide_div' : '';
		$show_svc_desc      = $dbhandler->get_global_option_value( 'bm_show_frontend_service_description', 0 ) == 0 ? 'hide_div' : '';
		$inactive_show_more = $svc_desc == 'N/A' ? 'hide_div' : '';
		$show_more_title    = $svc_desc == 'N/A' ? '' : __( 'Show full description', 'service-booking' );
		$service_title      = esc_html( $service->service_name );

		$gallery_title    = __( 'Show Gallery Images', 'service-booking' );
		$category_title   = __( 'category: ', 'service-booking' );
		$per_person_text  = __( '/person', 'service-booking' );
		$duration_title   = sprintf( esc_html__( 'Service duration is %s', 'service-booking' ), $svc_duration );
		$book_text        = __( 'Book Now', 'service-booking' );
		$wc_not_activated = __( 'Not Linked With WooCoomerce', 'service-booking' );
		$no_description   = __( 'No short description available', 'service-booking' );
		$plugin_path      = plugin_dir_url( __DIR__ );

		$resp = '<div class="main-parent cardcalendarbox">';
		/**$resp .= '<div class="productimg ' . $show_svc_img . '">';
		$resp .= '<img src="' . $svc_img . '" />';

		$resp .= '<div class="timebtnbox time-duration' . $show_duration . '" title="' . $duration_title . '">';
		$resp .= '<i class="fa fa-clock-o"></i> ' . $svc_duration;
		$resp .= '</div>';

		if ( !empty( $gallery_images ) && isset( $gallery_images[0] ) ) {
			$resp .= '<span id="' . $service_id . '" class="gallery gallery-btn" title="' . $gallery_title . '">';
			$resp .= '<i class="fa fa-picture-o"></i> ';
			$resp .= '</span>';
		}

		$resp .= '<span class="category-tag" title="' . $category_title . $category_name . '">';
		$resp .= $category_name;
		$resp .= '</span>';
		$resp .= '</div>';*/
		$resp .= '<div class="productdescbar" >';
		$resp .= '<h4 title="' . esc_html( $service->service_name ) . '"  style="color:' . $svc_name_colour . '!important;font-size:' . $svc_title_font . '!important;">' . $svc_name;
		$resp .= '<div class="service_full_description" data-title="' . $service_title . '">' . $svc_long_desc . '</div>';
		$resp .= '<span style="vertical-align:text-bottom;"><img src="' . esc_url( $plugin_path . 'public/img/si_info-line.svg' ) . '" class="service-desc-fa ' . $show_read_more . ' ' . $inactive_show_more . '" ' . $shrt_dsc_fa_class . ' title="' . $show_more_title . '" height="20"/></span>';
		$resp .= '<span class="price ' . $show_svc_price . ' ' . $price_text_class . '">' . $svc_default_price . '<span class="price_per_text">';

		if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
			$resp .= $per_person_text;
		}

		$resp .= '</span></span>';
		$resp .= '</h4>';

		if ( $svc_short_desc != 'N/A' ) {
			$resp .= '<p class="paratext ' . $show_svc_desc . '"><span class="svc_desc_text" ' . $svc_shrt_dsc_class . '>' . $svc_short_desc . '</span></p>';
		} else {
			$resp .= '<p class="paratext"><span id="svc_desc_text" class="svc_desc_text" style="font-size:' . $svc_shrt_desc_font . '!important;">' . $no_description . '</span></p>';
		}

		$resp .= '</div>';

		$resp .= '<div class="calender-modal service_calendar_details" data-service-id="' . esc_attr( $service_id ) . '">';
		$resp .= '<div class="booking-status">';
		$resp .= '<div class="booking-statusinnerbox">';
		$resp .= '<div class="status-box">';
		$resp .= '<div class="available_for_booking"></div>';
		$resp .= '<span>' . esc_html__( 'Available', 'service-booking' ) . '</span>';
		$resp .= '</div>';
		$resp .= '<div class="status-box">';
		$resp .= '<div class="not_available_for_booking"></div>';
		$resp .= '<span>' . esc_html__( 'Unavailable', 'service-booking' ) . '</span>';
		$resp .= '</div></div>';
		$resp .= '</div>';

		$resp .= '<div class="calendar-box">';
		$resp .= '<div class="service-by-id-calendar"></div>';
		$resp .= '</div>';

		$resp .= '<div class="productbottombar">';
		$resp .= '<div class="calendar_shortcode_error_message"></div>';
		$resp .= '<div class="booknowbtn' . ( $svc_total_cap_left == '0' || $unbookable == true ? ' readonly_div' : ' textblue bordercolor' ) . '"  style="background:' . $svc_button_colour . '!important">';
		$resp .= '<a href="#" id="' . ( $svc_total_cap_left == '0' ? '0' : $service_id ) . '" class="' . ( $svc_total_cap_left == '0' || $unbookable == true ? 'inactiveLink' : 'get_slot_details' ) . '" data-service-date="' . esc_attr( $date ) . '" style="color:' . $svc_btn_txt_colour . '!important">';

		if ( $svc_total_cap_left == '0' ) {
			$resp .= '<span class="slots_full_text">' . __( 'Slots Full', 'service-booking' ) . '</span>';
		} else {
			$resp .= esc_html__( 'Proceed', 'service-booking' );
		}

		$resp .= '</a></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		return wp_kses( $resp, $this->bm_fetch_expanded_allowed_tags() );
	}//end bm_fetch_service_by_calendar_response()


	/**
	 * Fetch service gallery images response
	 *
	 * @author Darpan
	 */
	public function bm_fetch_gallery_images_response( $image_object, $resp = '' ) {
		if ( ! empty( $image_object ) ) {
			$image_id = isset( $image_object->id ) ? esc_attr( $image_object->id ) : 0;
			$guids    = isset( $image_object->image_guid ) ? esc_html( $image_object->image_guid ) : '';

			if ( ! empty( $guids ) ) {
				$guids = explode( ',', $guids );

				if ( ! empty( $guids ) && is_array( $guids ) ) {
					$i     = 1;
					$j     = 1;
					$total = count( $guids );
					$resp .= '<div class="all_gallery_images">';
					foreach ( $guids as $guid ) {
						$svc_img = wp_get_attachment_url( $guid );
						$resp   .= '<div class="gallery_slides">';
						$resp   .= '<div class="gallerynumbertext">' . $i . '/' . $total . '</div>';
						$resp   .= '<img src="' . $svc_img . '" style="width:100%">';
						$resp   .= '</div>';
						++$i;
					} //end foreach

					$resp .= '<a class="gallery_prev">&#10094;</a>';
					$resp .= '<a class="gallery_next">&#10095;</a>';
					$resp .= '<div class="thumblin">';

					foreach ( $guids as $guid ) {
						$svc_img = wp_get_attachment_url( $guid );
						$resp   .= '<div class="gallery_column">';
						$resp   .= '<img class="gallery_single_image gallery_cursor" src="' . $svc_img . '" style="width:100%" onclick="galleryCurrentSlide(' . $j . ')" alt="Image_' . $j . '">';
						$resp   .= '</div>';
						++$j;
					} //end foreach

					$resp .= '</div>';
					$resp .= '</div>';
				} //end if
			} //end if
		} //end if

		return $resp;
	}//end bm_fetch_gallery_images_response()


	/**
	 * Fetch extra service response
	 *
	 * @author Darpan
	 */
	public function bm_fetch_extra_service_response( $data = array(), $resp = '' ) {
		$dbhandler            = new BM_DBhandler();
		$extra_label          = __( 'Would you like to add extra services ?', 'service-booking' );
		$no_of_persons_label  = __( 'No. of persons', 'service-booking' );
		$skip_text            = __( 'Skip', 'service-booking' );
		$next_text            = __( 'Next', 'service-booking' );
		$max_cap_text         = __( 'Cap Left - ', 'service-booking' );
		$cap_exhausted_text   = __( 'Capacity exhausted', 'service-booking' );
		$wc_text              = __( 'Not Linked With WooCommerce', 'service-booking' );
		$per_person_text      = __( '/person', 'service-booking' );
		$price_text           = __( 'Cost - ', 'service-booking' );
		$global_extras        = $dbhandler->get_all_result(
			'EXTRA',
			'*',
			array(
				'is_global'              => 1,
				'is_extra_service_front' => 1,
			),
			'results'
		);
		$show_edit_button     = $dbhandler->get_global_option_value( 'bm_show_frontend_edit_button_in_booking_form', 0 ) == 0 ? "class='hide_div'" : '';
		$wcmmrce_integration  = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );
		$only_wcmmrce         = $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout', 0 );
		$primary_color        = $this->bm_get_theme_color( 'primary' ) ?? '#000000';
		$contrast             = $this->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
		$svc_button_colour    = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', $primary_color );
		$svc_btn_txt_colour   = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );
		$form_class           = '';
		$hidden_no_persons    = false;
		$hidden_cap_left_text = false;

		if ( ! empty( $data ) ) {
			$service_id = isset( $data['id'] ) ? $data['id'] : '';
			$date       = isset( $data['date'] ) ? $data['date'] : '';
			$type       = isset( $data['type'] ) ? $data['type'] : '';

			if ( ! empty( $service_id ) && ! empty( $date ) ) {
				if ( isset( $service_id ) && ! empty( $service_id ) ) {
					$extra_rows = $dbhandler->get_all_result(
						'EXTRA',
						'*',
						array(
							'service_id'             => $service_id,
							'is_global'              => 0,
							'is_extra_service_front' => 1,
						),
						'results'
					);

					$settings = $dbhandler->get_value( 'SERVICE', 'service_settings', $service_id, 'id' );
					$settings = ! empty( $settings ) ? maybe_unserialize( $settings ) : array();

					if ( isset( $settings['show_cap_left_text'] ) && $settings['show_cap_left_text'] == 0 ) {
						$hidden_cap_left_text = true;
					}

					if ( ! empty( $extra_rows ) && ! empty( $global_extras ) ) {
						$total_extra_rows = array_merge( $global_extras, $extra_rows );
					} elseif ( empty( $extra_rows ) && ! empty( $global_extras ) ) {
						$total_extra_rows = $global_extras;
					} elseif ( ! empty( $extra_rows ) && empty( $global_extras ) ) {
						$total_extra_rows = $extra_rows;
					} //end if

					$resp .= '<div class="extra_service_results">';
					$resp .= '<h4 class="heading_choose_extra">' . $extra_label . '</h4>';
					if ( isset( $total_extra_rows ) && ! empty( $total_extra_rows ) ) {
						foreach ( $total_extra_rows as $key => $extra_service ) {
							$wc_product_id = ! empty( $extra_service ) && isset( $extra_service->svcextra_wc_product ) ? esc_attr( $extra_service->svcextra_wc_product ) : 0;
							$resp         .= '<div class="extra_service_content" id="content_' . $key . '">';

							$cap_left             = $this->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date( $extra_service->id, $extra_service->extra_max_cap, 0, $date );
							$cap_div_style        = $cap_left == 0 ? 'style="cursor:not-allowed;"' : '';
							$cap_zero_input_class = $cap_left == 0 ? 'readonly_checkbox' : '';
							$cap_zero_label_class = $cap_left == 0 ? 'readonly_label' : '';
							$cap_zero_style       = $cap_left == 0 ? 'style="background-color:#f0f0f1;pointer-events:none;"' : '';

							$resp .= '<div class="extra_services_available" ' . $cap_div_style . '>';
							$resp .= '<input type="checkbox" class="listed_extra_service ' . $cap_zero_input_class . '" name="listed_extra_service" id="' . $extra_service->id . '">';
							$resp .= '<label for="' . $extra_service->id . '" class="' . $cap_zero_label_class . '">' . $extra_service->extra_name . '<span class="extra_max_cap_text">';

							if ( $cap_left == 0 ) {
								$resp .= ! $hidden_cap_left_text ? $cap_exhausted_text : $cap_left;
							} else {
								$resp .= ! $hidden_cap_left_text ? $max_cap_text . $cap_left : $cap_left;
							}

							$resp .= '</span></label>';
							$resp .= '</div>';

							$resp .= '<div class="extra_service_booking_no readonly_cursor" ' . $cap_div_style . '>';

							if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
								$resp .= '<p class="extra_persons_label">' . $no_of_persons_label . '</p>';
							}

							$resp .= '<select class="extra_service_total_booking readonly_checkbox" id="extra_service_total_booking_' . $extra_service->id . '" ' . $cap_zero_style . '>';

							for ( $i = 1; $i <= $cap_left; $i++ ) {
								$resp .= '<option value="' . $i . '">' . $i . '</option>';
							}

							$resp .= '</select>';
							$resp .= '</div>';

							$resp .= '<div class="extra_services_price">';
							$resp .= '<span class="listed_extra_service_price">' . $this->bm_fetch_price_in_global_settings_format( $extra_service->extra_price, true );

							if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
								$resp .= $per_person_text;
							}

							$resp .= '</span>';
							$resp .= '</div></div>';
						} //end foreach
					} else {
						$resp .= '<div class="textcenter">' . esc_html__( 'No extra services found', 'service-booking' ) . '</div>';
					} //end if

					if ( $wcmmrce_integration == 1 && ( new WooCommerceService() )->is_enabled() ) {
						if ( $only_wcmmrce == 1 ) {
							if ( $type == 'service_by_category' ) {
								$form_class = 'get_svc_by_cat_checkout_form';
							} else {
								$form_class = 'get_checkout_form';
							}
						} else {
							if ( $type == 'service_by_category' ) {
								$form_class = 'get_svc_by_cat_checkout_options';
							} else {
								$form_class = 'get_checkout_options';
							}//end if
						}
					} else {
						if ( $type == 'service_by_category' ) {
							$form_class = 'get_svc_by_cat_checkout_form';
						} else {
							$form_class = 'get_checkout_form';
						}//end if
					}

					// Edit button class
					if ( $type == 'service_by_category' ) {
						$edit_button_class = 'edit_svc_by_cat_slot_selection';
					} elseif ( $type == 'home_page' ) {
						$edit_button_class = 'edit_slot_selection';
					} //end if

					$resp .= '<div class="formbottombuttonbar">';
					$resp .= '<div class="formbuttoninnerbox">';
					$resp .= '<div ' . $show_edit_button . '>';
					$resp .= '<div class="editbtn bgcolor textwhite ' . $edit_button_class . '" style="background:' . $svc_button_colour . '!important"><span style="color:' . $svc_btn_txt_colour . '!important">' . __( 'Edit', 'service-booking' ) . '</span></div>';
					$resp .= '</div>';
					$resp .= '<div class="rightbuttonbar">';
					$resp .= '<div class="cancelbtn bgcolor textwhite ' . $form_class . '" id="' . $service_id . '" style="background:' . $svc_button_colour . '!important"><span style="color:' . $svc_btn_txt_colour . '!important">' . $skip_text . '</span></div>';
					$resp .= '<div class="bookbtn readonly_div">';
					$resp .= '<a href="#" id="' . $service_id . '" class="' . $form_class . ' inactiveLink" style="color:' . $svc_btn_txt_colour . '!important">';
					$resp .= $next_text . '</a>';
					$resp .= '</div></div></div></div></div>';
				}//end if
			}//end if
		}//end if

		return $resp;
	}//end bm_fetch_extra_service_response()


	/**
	 * Fetch checkout options response
	 *
	 * @author Darpan
	 */
	public function bm_fetch_checkout_options_response( $data = array() ) {
		$dbhandler           = new BM_DBhandler();
		$options_label       = __( 'Select Checkout Type', 'service-booking' );
		$next_text           = __( 'Next', 'service-booking' );
		$wc_text             = __( 'Not Linked With WooCommerce', 'service-booking' );
		$show_edit_button    = $dbhandler->get_global_option_value( 'bm_show_frontend_edit_button_in_booking_form', 0 ) == 0 ? "class='hide_div'" : '';
		$wcmmrce_integration = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );
		$svc_button_colour   = $dbhandler->get_global_option_value( 'bm_frontend_book_button_color', '#000000' );
		$svc_btn_txt_colour  = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', '#ffffff' );
		$form_class          = '';
		$edit_button_class   = '';
		$resp                = '';

		if ( ! empty( $data ) ) {
			$type = isset( $data['type'] ) ? $data['type'] : '';

			if ( ! empty( $type ) && $wcmmrce_integration == 1 && ( new WooCommerceService() )->is_enabled() ) {
				if ( $type == 'service_by_category' ) {
					$form_class        = 'get_svc_by_cat_checkout_form';
					$edit_button_class = 'edit_svc_by_cat_slot_selection';
				} else {
					$form_class        = 'get_checkout_form';
					$edit_button_class = 'edit_slot_selection';
				}//end if
				$resp .= '<div class="extra_service_results">';
				$resp .= '<div class="checkout_options_results">';
				$resp .= '<h4 class="heading_choose_checkout_option">' . $options_label . '</h4>';
				$resp .= '<select id="flexi_checkout_options">';
				$resp .= '<option value="flexi_checkout">' . __( 'Flexi Checkout' ) . '</option>';
				$resp .= '<option value="woocommerce_checkout">' . __( 'WooCommerce Checkout' ) . '</option>';
				$resp .= '</select>';
				$resp .= '</div>';

				$resp .= '<div class="formbottombuttonbar">';
				$resp .= '<div class="formbuttoninnerbox">';
				$resp .= '<div ' . $show_edit_button . '>';
				$resp .= '<div class="editbtn bgcolor textwhite ' . $edit_button_class . '" style="background:' . $svc_button_colour . '!important"><span style="color:' . $svc_btn_txt_colour . '!important">' . __( 'Edit', 'service-booking' ) . '</span></div>';
				$resp .= '</div>';
				$resp .= '<div class="rightbuttonbar">';
				$resp .= '<div class="bookbtn bgcolor textwhite" style="background:' . $svc_button_colour . '!important">';
				$resp .= '<a href="#" class="' . $form_class . '" style="color:' . $svc_btn_txt_colour . '!important">';
				$resp .= $next_text . '</a>';
				$resp .= '</div></div></div></div></div>';
			}//end if
		}//end if

		return $resp;
	}//end bm_fetch_checkout_options_response()


	/**
	 * Fetch all fields
	 *
	 * @author Darpan
	 */
	public function bm_fetch_fields() {
		$resp                = '';
		$dbhandler           = new BM_DBhandler();
		$fields              = $dbhandler->get_all_result( 'FIELDS', '*', 1, 'results', 0, false, 'field_position', false );
		$primary_email_label = __( 'Primary email', 'service-booking' );
		$primary_email_title = __( 'Order email will be sent to this email', 'service-booking' );

		if ( ! empty( $fields ) ) {
			$resp .= '<div class="fields_modal_box">';
			foreach ( $fields as $field ) {
				$field_type    = isset( $field->field_type ) ? $field->field_type : '';
				$field_label   = isset( $field->field_label ) ? $field->field_label : '';
				$field_name    = isset( $field->field_name ) ? $field->field_name : '';
				$field_options = isset( $field->field_options ) ? maybe_unserialize( $field->field_options ) : array();

				switch ( $field_type ) {
					case 'text':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'email':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = true;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'url':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'password':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'select':
						$hasRequired     = true;
						$hasChoices      = true;
						$hasMultiple     = true;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = false;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'radio':
						$hasRequired     = true;
						$hasChoices      = true;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = false;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'textarea':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = true;
						$hasColumns      = true;
						$hasIntlCode     = false;
						break;

					case 'checkbox':
						$hasRequired     = true;
						$hasChoices      = true;
						$hasMultiple     = true;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = false;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'date':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'time':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'datetime':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'month':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'week':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'number':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'file':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = true;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'button':
						$hasRequired     = false;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = false;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'submit':
						$hasRequired     = false;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = false;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'tel':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = true;
						break;

					case 'hidden':
						$hasRequired     = false;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = false;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'color':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'range':
						$hasRequired     = true;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = true;
						$hasMinlength    = true;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'reset':
						$hasRequired     = false;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					case 'search':
						$hasRequired     = false;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = true;
						$hasDefaultvalue = true;
						$hasAutocomplete = true;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
						break;

					default:
						$hasRequired     = false;
						$hasChoices      = false;
						$hasMultiple     = false;
						$hasMaxlength    = false;
						$hasMinlength    = false;
						$hasVisibility   = false;
						$hasDefaultvalue = false;
						$hasAutocomplete = false;
						$hasRows         = false;
						$hasColumns      = false;
						$hasIntlCode     = false;
				} //end switch

				$is_editable = isset( $field->is_editable ) && $field->is_editable == 0 ? 'readonly' : '';
				$placeholder = isset( $field_options['placeholder'] ) && ! empty( $field_options['placeholder'] ) ? 'placeholder="' . $field_options['placeholder'] . '"' : '';

				if ( $field_type == 'date' || $field_type == 'datetime' ) {
					if ( ! empty( $field_options['custom_class'] ) ) {
						$custom_class = 'class="date_field ' . $field_options['custom_class'] . '"';
					} else {
						$custom_class = 'class="date_field"';
					}
				} elseif ( $field_type == 'radio' || $field_type == 'checkbox' ) {
					if ( ! empty( $field_options['custom_class'] ) ) {
						$custom_class = 'class="radio_and_checkbox_field ' . $field_options['custom_class'] . '"';
					} else {
						$custom_class = 'class="radio_and_checkbox_field"';
					}
				} elseif ( $hasIntlCode == true ) {
					if ( isset( $field_options['show_intl_code'] ) && $field_options['show_intl_code'] == '1' ) {
						if ( ! empty( $field_options['custom_class'] ) ) {
							$custom_class = 'class="intl_phone_field_input ' . $field_options['custom_class'] . '"';
						} else {
							$custom_class = 'class="intl_phone_field_input"';
						}
					}
				} elseif ( ! empty( $field_options['custom_class'] ) ) {
						$custom_class = 'class="' . $field_options['custom_class'] . '"';
				} else {
					$custom_class = '';
				} //end if

				$field_key = isset( $field->field_key ) ? $field->field_key : '';

				if ( isset( $field_options['field_width'] ) && ! empty( $field_options['field_width'] ) ) {
					if ( $field_options['field_width'] == 'full' ) {
						if ( $field_type == 'radio' || $field_type == 'checkbox' ) {
							$field_width = 'style=width:100%;padding-left:10px';
						} else {
							$field_width = 'style=width:100%;';
						}
					} elseif ( $field_options['field_width'] == 'half' ) {
						if ( $field_type == 'radio' || $field_type == 'checkbox' ) {
							$field_width = 'style=width:50%;padding-left:10px';
						} else {
							$field_width = 'style=width:50%;';
						}
					}
				}

				$is_required   = $hasRequired == true && isset( $field->is_required ) && $field->is_required == 1 ? 'required' : '';
				$asterisk      = $hasRequired == true && isset( $field->is_required ) && $field->is_required == 1 ? '<strong class="required_asterisk"> *</strong>' : '';
				$minLength     = $hasMinlength == true && isset( $field_options['min_length'] ) && ! empty( $field_options['min_length'] ) ? 'min="' . $field_options['min_length'] . '"' : '';
				$maxLength     = $hasMaxlength == true && isset( $field_options['max_length'] ) && ! empty( $field_options['max_length'] ) ? 'max="' . $field_options['max_length'] . '"' : '';
				$rows          = $hasRows == true && isset( $field_options['rows'] ) && ! empty( $field_options['rows'] ) ? 'rows="' . $field_options['rows'] . '"' : '';
				$columns       = $hasColumns == true && isset( $field_options['columns'] ) && ! empty( $field_options['columns'] ) ? 'columns="' . $field_options['columns'] . '"' : '';
				$autocomplete  = $hasAutocomplete == true && isset( $field_options['autocomplete'] ) && $field_options['autocomplete'] == 0 ? 'autocomplete=off' : '';
				$is_visible    = $hasVisibility == true && isset( $field_options['is_visible'] ) && $field_options['is_visible'] == 0 ? 'style=display:none;' : '';
				$is_multiple   = $hasMultiple == true && isset( $field_options['is_multiple'] ) && $field_options['is_multiple'] == 1 ? 'multiple' : '';
				$default_value = $hasDefaultvalue == true && isset( $field_options['default_value'] ) && ! empty( $field_options['default_value'] ) ? '' . $field_options['default_value'] . '' : '';

				if ( $hasChoices == false ) {
					$resp .= '<div class="formtable" ' . $is_visible . ' ' . $field_width . '>';
					$resp .= '<div class="formbox">';
					$resp .= '<label for="' . $field_key . '">' . $field_label . $asterisk . '</label>';
					if ( $field_type == 'textarea' ) {
						$resp .= '<textarea name="' . $field_name . '" id="' . $field_key . '" ' . $custom_class . ' ' . $rows . ' ' . $columns . ' ' . $placeholder . ' ' . $minLength . '  ' . $maxLength . ' ' . $is_required . ' ' . $is_editable . ' ' . $autocomplete . ' style="width :98%;">' . $default_value . '</textarea>';
					} else {
						$resp .= '<input type="' . $field_type . '" name="' . $field_name . '" id="' . $field_key . '" ' . $placeholder . ' ' . $custom_class . ' ' . $minLength . '  ' . $maxLength . ' ' . $is_required . ' ' . $is_editable . ' ' . $autocomplete . ' value="' . $default_value . '">';
					}

					if ( $field_type == 'email' && isset( $field_options['is_main_email'] ) && $field_options['is_main_email'] == 1 ) {
						$resp .= '<div class="primary_email_info"><span class="primary_email_span">' . $primary_email_label . '</span>(' . $primary_email_title . ')</span></div>';
					}
				} else {
					$radio_class = $field_type == 'radio' || $field_type == 'checkbox' ? 'class="radio_and_checkbox_label"' : 'class="select_box_label"';

					$resp .= '<div class="formtable checkbox_and_radio_div" ' . $is_visible . ' ' . $field_width . '>';
					$resp .= '<div class="formbox">';
					$resp .= '<label ' . $radio_class . ' for="' . $field_key . '">' . $field_label . $asterisk . '</label>';

					if ( isset( $field_options['options'] ) ) {
						if ( $field_type == 'radio' || $field_type == 'checkbox' ) {
							if ( isset( $field_options['options']['values'] ) && is_array( $field_options['options']['values'] ) ) {
								$field_count = count( $field_options['options']['values'] );
								for ( $i = 0; $i < $field_count; $i++ ) {
									$resp .= '<div class="inputcheckgroup">';
									$resp .= '<input type="' . $field_type . '" id="' . $field_key . '_' . $i . '" name="' . $field_name . '" value="' . $field_options['options']['values'][ $i ] . '" ' . ( isset( $field_options['options']['selected'][ $i ] ) && $field_options['options']['selected'][ $i ] == 1 ? 'checked' : '' ) . ' ' . $is_required . ' ' . $custom_class . '/>';
									$resp .= '<label for="' . $field_key . '_' . $i . '">' . $field_options['options']['values'][ $i ] . '</label>';
									$resp .= '</div>';
								}
							}
						} elseif ( $field_type == 'select' ) {
							$resp .= '<select name="' . $field_name . '" id="' . $field_key . '" ' . $is_required . ' ' . $is_multiple . ' ' . $custom_class . '>';

							if ( isset( $field_options['options']['values'] ) && is_array( $field_options['options']['values'] ) ) {
								$field_count = count( $field_options['options']['values'] );
								for ( $i = 0; $i < $field_count; $i++ ) {
									$resp .= '<option value="' . $field_options['options']['keys'][ $i ] . '" ' . ( isset( $field_options['options']['selected'][ $i ] ) && $field_options['options']['selected'][ $i ] == 1 ? 'selected' : '' ) . '>' . $field_options['options']['values'][ $i ] . '</option>';
								}
							}

							$resp .= '</select>';
						}
					}
				} //end if

				$resp .= '</div></div>';
			} //end foreach

			$resp .= '</div>';
		} //end if

		return $resp;
	}//end bm_fetch_fields()


	/**
	 * Fetch user form for booking process
	 *
	 * @author Darpan
	 */
	public function bm_fetch_user_form( $data = array(), $resp = '' ) {
		$dbhandler = new BM_DBhandler();

		if ( ! empty( $data ) ) {
			$id                       = isset( $data['id'] ) ? $data['id'] : '';
			$time                     = isset( $data['time_slot'] ) ? $data['time_slot'] : '';
			$total_service_booking    = isset( $data['total_service_booking'] ) ? $data['total_service_booking'] : '';
			$date                     = isset( $data['date'] ) ? $data['date'] : '';
			$type                     = isset( $data['type'] ) ? $data['type'] : '';
			$extra_svc_ids            = isset( $data['extra_svc_ids'] ) ? $data['extra_svc_ids'] : '';
			$total_extra_slots_booked = isset( $data['no_of_persons'] ) ? $data['no_of_persons'] : '';
			$match                    = preg_match_all( '/\<span class\="single_slot_timings"\>(.*?)\<\/span\>/', $time, $slot_details );
			$show_edit_button         = $dbhandler->get_global_option_value( 'bm_show_frontend_edit_button_in_booking_form', 0 ) == 0 ? 'hide_div' : '';
			$booking_currency         = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
			$edit_button_class        = '';
			$per_person_text          = __( '/person', 'service-booking' );

			if ( ! empty( $id ) && ! empty( $time ) && ! empty( $date ) ) {
				$service        = $dbhandler->get_row( 'SERVICE', $id );
				$svc_img        = esc_url( $this->bm_fetch_image_url_or_guid( $id, 'SERVICE', 'url' ) );
				$svc_name       = ! empty( $service ) && isset( $service->service_name ) && ! empty( $service->service_name ) ? esc_html( $service->service_name ) : 'N/A';
				$short_svc_name = ! empty( $svc_name ) ? esc_html( mb_strimwidth( $svc_name, 0, 20, '...' ) ) : 'N/A';
				$base_svc_price = $this->bm_fetch_service_price_by_service_id_and_date( $id, $date, 'normal_format' );
				$fields         = $this->bm_fetch_fields();

				$booking_price = $this->bm_fetch_total_price( str_replace( $booking_currency, '', $base_svc_price ), $total_service_booking );
				$total_cost    = $booking_price;

				$resp .= '<div class="pagewrapper2">';

				// Progress bar
				$resp .= '<div class="topbar">';
				$resp .= '<div class="progress-container">';
				$resp .= '<div class="progress" id="progress"></div>';
				$resp .= '<div class="circle bordercolor textblue"><i class="fa fa-check"></i></div>';
				$resp .= '<div class="circle bgcolor bordercolor textwhite">' . esc_attr( '2' ) . '</div>';
				$resp .= '<div class="circle">' . esc_attr( '3' ) . '</div>';
				$resp .= '</div></div>';

				// Service image and name
				$resp .= '<div class="serviceviewbox">';
				$resp .= '<div class="servicenamebox">';
				$resp .= '<span class="title">' . __( 'Services:', 'service-booking' ) . '</span><br />';
				$resp .= '<div class="serviceimgbox"><img src="' . $svc_img . '" /></div>';
				$resp .= $short_svc_name;
				$resp .= '</div>';

				// Appointment date
				$resp .= '<div class="servicedescriptionbox edit-top-des">';
				$resp .= '<div class="serviceappointmentdate details">';
				$resp .= '<span class="title">' . __( 'Appointment Date:', 'service-booking' ) . '</span><br />';
				$resp .= $this->bm_month_year_date_format( $date );
				$resp .= '</div>';

				// Booking time
				$resp .= '<div class="servicerange details">';
				$resp .= '<span class="title">' . __( 'Time Range:', 'service-booking' ) . '</span><br />';
				$resp .= $time;
				$resp .= '</div>';

				// Booking price
				$resp .= '<div class="servicerange details">';
				$resp .= '<span class="title">' . __( 'Base Price:', 'service-booking' ) . '</span><br />';
				$resp .= $base_svc_price . '<span class="price_per_text">' . $per_person_text . '</span>';
				$resp .= '</div>';

				// Edit button
				if ( $type == 'service_by_category' ) {
					$edit_button_class = 'edit_svc_by_cat_slot_selection';
				} elseif ( $type == 'home_page' ) {
					$edit_button_class = 'edit_slot_selection';
				}

				$resp .= '<div class="servicedescinnerbox details ' . $show_edit_button . '">';
				$resp .= '<div class="editbtn bgcolor textwhite ' . $edit_button_class . '">' . __( 'Edit', 'service-booking' ) . '</div>';
				$resp .= '</div></div></div>';

				// Fields
				$resp .= '<div id="booking_form">';
				$resp .= $fields;

				// Hidden Fields
				$resp .= '<input type="hidden" id="service_id" name="service_id" value="' . $id . '">';
				$resp .= '<input type="hidden" id="booking_slots" name="booking_slots" value="' . ( $match > 0 ? $slot_details[1][0] : $time ) . '">';
				$resp .= '<input type="hidden" id="booking_date" name="booking_date" value="' . $date . '">';
				$resp .= '<input type="hidden" id="service_name" name="service_name" value="' . $svc_name . '">';
				$resp .= '<input type="hidden" id="total_service_booking" name="total_service_booking" value="' . $total_service_booking . '">';
				$resp .= '<input type="hidden" id="extra_svc_booked" name="extra_svc_booked" value="' . $extra_svc_ids . '">';
				$resp .= '<input type="hidden" id="total_extra_slots_booked" name="total_extra_slots_booked" value="' . $total_extra_slots_booked . '">';

				// Calculate extra service price and total cost
				if ( isset( $extra_svc_ids ) && ! empty( $extra_svc_ids ) && isset( $total_extra_slots_booked ) && ! empty( $total_extra_slots_booked ) ) {
					$total_extra_price = array();
					$extra_price_text  = '';

					$total_slots_booked = explode( ',', $total_extra_slots_booked );

					$additional  = "id in($extra_svc_ids)";
					$extra_price = $dbhandler->get_all_result( 'EXTRA', 'extra_price', 1, 'results', 0, false, null, false, $additional );
					$extra_name  = $dbhandler->get_all_result( 'EXTRA', 'extra_name', 1, 'results', 0, false, null, false, $additional );
					$extra_price = array_column( $extra_price, 'extra_price' );
					$extra_name  = array_column( $extra_name, 'extra_name' );
					$i           = 1;

					if ( ! empty( $extra_price ) && ! empty( $total_slots_booked ) ) {
						foreach ( $extra_price as $key => $price ) {
							if ( ! empty( $price ) ) {
								$total_extra_price[ $key ] = $this->bm_fetch_total_price( $price, $total_slots_booked[ $key ] );
								$extra_price_text         .= '<div class="booked_extra_number">';
								$extra_price_text         .= ( $this->bm_fetch_price_in_global_settings_format( $price, true ) ) . ' X ' . $total_slots_booked[ $key ];
								$extra_price_text         .= '<span class="booked_extra_name">(' . $extra_name[ $key ] . ')</span></div>';
								if ( $i !== count( $total_slots_booked ) ) {
									$extra_price_text .= '<span class="plus_sign"> + </span>';
								}
							}

							++$i;
						}

						$extra_price_text .= ' <span class="bn-extra-equal">=</span> ';
					}

					if ( isset( $total_extra_price ) && ! empty( $total_extra_price ) ) {
						$total_extra_price           = array_sum( $total_extra_price );
						$total_cost                  = ( $total_cost + $total_extra_price );
						$total_extra_price_in_format = $this->bm_fetch_price_in_global_settings_format( $total_extra_price, true );
					} else {
						$total_extra_price_in_format = $this->bm_fetch_price_in_global_settings_format( 0 );
					}
				} else {
					$total_extra_price_in_format = $this->bm_fetch_price_in_global_settings_format( 0 );
				} //end if

				// Service total
				$resp .= '<div style="background:#FAFBFC; display: inline-block; padding:20px; width: 100%;">';
				$resp .= '<div class="formtable">';
				$resp .= '<div class="formbox-left">';
				$resp .= '<span class="label_after_booking_form">' . __( 'Service Total:', 'service-booking' ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="formbox-right">';
				$resp .= '<span class="svc_booking_total_text">' . $base_svc_price . 'X' . $total_service_booking . '<span class="bn-extra-equal"> =</span> </span>';
				$resp .= '<span class="balancetext label_after_booking_form">' . $this->bm_fetch_price_in_global_settings_format( $booking_price, true ) . '</span>';
				$resp .= '</div></div>';

				// Extra Service price
				$resp .= '<div class="formtable">';
				$resp .= '<div class="formbox-left">';
				$resp .= '<span class="label_after_booking_form">' . __( 'Extra Service Cost:', 'service-booking' ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="formbox-right">';

				if ( isset( $extra_price_text ) && ! empty( $extra_price_text ) ) {
					$resp .= '<span class="svc_booking_total_text">' . $extra_price_text . '</span>';
				}

				$resp .= '<span class="balancetext label_after_booking_form">' . $total_extra_price_in_format . '</span>';
				$resp .= '</div></div>';

				// Discount
				$resp .= '<div class="formtable">';
				$resp .= '<div class="formbox">';
				$resp .= '<span class="label_after_booking_form>' . __( 'Discount', 'service-booking' ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="formbox-right">';
				$resp .= '<span class="balancetext label_after_booking_form">';
				$resp .= $this->bm_fetch_price_in_global_settings_format( 0 );
				$resp .= '</span>';
				$resp .= '</div></div>';

				// Total price
				$resp .= '<div class="formtable" style="margin-bottom: 0px;">';
				$resp .= '<div class="formbox">';
				$resp .= '<span class="boldfont label_after_booking_form">' . __( 'Total Cost:', 'service-booking' ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="formbox-right">';
				$resp .= '<span class="boldfont balancetext label_after_booking_form">' . $this->bm_fetch_price_in_global_settings_format( $total_cost, true ) . '</span>';
				$resp .= '</div></div></div>';

				// Price and coupon hidden fields
				$resp .= '<input type="hidden" id="coupons" name="coupons" value="">';
				$resp .= '<input type="hidden" id="wc_coupons" name="wc_coupons" value="">';
				$resp .= '<input type="hidden" id="base_svc_price" name="base_svc_price" value="' . $base_svc_price . '">';
				$resp .= '<input type="hidden" id="service_cost" name="service_cost" value="' . $booking_price . '">';
				$resp .= '<input type="hidden" id="disount_amount" name="disount_amount" value="0">';
				$resp .= '<input type="hidden" id="extra_svc_cost" name="extra_svc_cost" value="' . $total_extra_price . '">';
				$resp .= '<input type="hidden" id="total_cost" name="total_cost" value="' . $total_cost . '">';

				// Newsletter and terms and conditions
				$resp .= '<div class="formbottombox">';
				$resp .= '<div class="formbottominnerbox">';
				$resp .= '<div class="inputcheckgroup formbottomcheckgroup">';
				$resp .= '<input type="checkbox" id="newsletter" name="newsletter"/>';
				$resp .= '<label for="newsletter">' . __( 'Newsletter registration', 'service-booking' ) . '</label>';
				$resp .= '</div>';
				$resp .= '<div class="inputcheckgroup formbottomcheckgroup">';
				$resp .= '<input type="checkbox" id="terms_conditions" name="terms_conditions" required/>';
				$resp .= '<label for="terms_conditions">' . __( 'Condition and terms approval', 'service-booking' ) . '</label>';
				$resp .= '</div></div>';

				// Back and book button
				$resp .= '<div class="formbottombuttonbar">';
				$resp .= '<div class="formbuttoninnerbox">';
				$resp .= '<div class="cancelbtn" id="cancel_booking">' . __( 'Cancel', 'service-booking' ) . '</div>';
				$resp .= '<div class="bookbtn bgcolor bordercolor textwhite" id="confirm_booking">' . __( 'Proceed', 'service-booking' ) . '</div>';
				$resp .= '</div></div></div></div></div>';
			} //end if
		} //end if

		return $resp;
	}//end bm_fetch_user_form()


	/**
	 * Fetch latest order info
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_info( $data = array() ) {
		$dbhandler      = new BM_DBhandler();
		$booking_fields = array();

		if ( ! empty( $data ) ) {
			$id                       = isset( $data['id'] ) ? $data['id'] : '';
			$time                     = isset( $data['time_slot'] ) ? $data['time_slot'] : '';
			$total_service_booking    = isset( $data['total_service_booking'] ) ? $data['total_service_booking'] : 0;
			$date                     = isset( $data['date'] ) ? $data['date'] : '';
			$extra_svc_ids            = isset( $data['extra_svc_ids'] ) ? $data['extra_svc_ids'] : '';
			$total_extra_slots_booked = isset( $data['no_of_persons'] ) ? $data['no_of_persons'] : '';
			$match                    = preg_match_all( '/\<span class\="single_slot_timings"\>(.*?)\<\/span\>/', $time, $slot_details );
			$booking_currency         = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
			$total_extra_price        = 0;

			if ( ! empty( $id ) && ! empty( $time ) && ! empty( $date ) ) {
				$service        = $dbhandler->get_row( 'SERVICE', $id );
				$svc_img        = esc_url( $this->bm_fetch_image_url_or_guid( $id, 'SERVICE', 'url' ) );
				$svc_name       = ! empty( $service ) && isset( $service->service_name ) && ! empty( $service->service_name ) ? esc_html( $service->service_name ) : 'N/A';
				$short_svc_name = ! empty( $svc_name ) ? mb_strimwidth( $svc_name, 0, 20, '...' ) : 'N/A';
				$base_svc_price = $this->bm_fetch_service_price_by_service_id_and_date( $id, $date );
				$booking_price  = $this->bm_fetch_total_price( str_replace( $booking_currency, '', $base_svc_price ), $total_service_booking );
				$total_cost     = $booking_price;

				$svc_price_module_id = $this->bm_fetch_external_service_price_module_by_service_id_and_date( $id, $date );

				$booking_fields['service_id']               = $id;
				$booking_fields['booking_slots']            = $match > 0 ? $slot_details[1][0] : $time;
				$booking_fields['booking_date']             = $date;
				$booking_fields['service_name']             = $svc_name;
				$booking_fields['total_service_booking']    = $total_service_booking;
				$booking_fields['extra_svc_booked']         = $extra_svc_ids;
				$booking_fields['total_extra_slots_booked'] = $total_extra_slots_booked;
				$booking_fields['base_svc_price']           = $base_svc_price;
				$booking_fields['service_cost']             = $booking_price;
				$booking_fields['svc_price_module_id']      = $svc_price_module_id;

				// Calculate extra service price and total cost
				if ( isset( $extra_svc_ids ) && ! empty( $extra_svc_ids ) && isset( $total_extra_slots_booked ) && ! empty( $total_extra_slots_booked ) ) {
					$total_slots_booked = explode( ',', $total_extra_slots_booked );
					$extra_total        = array();
					$additional         = "id in($extra_svc_ids)";

					$extra_price = $dbhandler->get_all_result( 'EXTRA', 'extra_price', 1, 'results', 0, false, null, false, $additional );
					$extra_price = array_column( $extra_price, 'extra_price' );
					$i           = 1;

					if ( ! empty( $extra_price ) && ! empty( $total_slots_booked ) ) {
						foreach ( $extra_price as $key => $price ) {
							if ( ! empty( $price ) ) {
								$extra_total[ $key ] = $this->bm_fetch_total_price( $price, $total_slots_booked[ $key ] );
							}
							++$i;
						}
					} //end if

					if ( ! empty( $extra_total ) ) {
						$total_extra_price = array_sum( $extra_total );
						$total_cost        = ( $total_cost + $total_extra_price );
					} //end if
				} //end if

				$booking_fields['extra_svc_cost'] = $total_extra_price;
				$booking_fields['total_cost']     = $total_cost;
				$booking_fields['subtotal']       = $total_cost;
			} //end if
		} //end if

		return apply_filters( 'bm_flexibooking_frontend_pre_order_booking_fields', $booking_fields );
	}//end bm_fetch_order_info()


	/**
	 * Fetch latest order info for backend order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_backend_order_info( $data = array() ) {
		$dbhandler      = new BM_DBhandler();
		$booking_fields = array();

		if ( ! empty( $data ) ) {
			$id                       = isset( $data['service_id'] ) ? $data['service_id'] : '';
			$time                     = isset( $data['booking_slots'] ) ? $data['booking_slots'] : '';
			$total_service_booking    = isset( $data['total_service_booking'] ) ? $data['total_service_booking'] : 0;
			$date                     = isset( $data['booking_date'] ) ? $data['booking_date'] : '';
			$extra_svc_ids            = isset( $data['extra_svc_booked'] ) ? maybe_unserialize( $data['extra_svc_booked'] ) : '';
			$total_extra_slots_booked = isset( $data['total_extra_slots_booked'] ) ? maybe_unserialize( $data['total_extra_slots_booked'] ) : '';
			$booking_currency         = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
			$total_extra_price        = 0;

			if ( ! empty( $id ) && ! empty( $time ) && ! empty( $date ) ) {
				$service        = $dbhandler->get_row( 'SERVICE', $id );
				$svc_img        = esc_url( $this->bm_fetch_image_url_or_guid( $id, 'SERVICE', 'url' ) );
				$svc_name       = ! empty( $service ) && isset( $service->service_name ) && ! empty( $service->service_name ) ? esc_html( $service->service_name ) : 'N/A';
				$short_svc_name = ! empty( $svc_name ) ? mb_strimwidth( $svc_name, 0, 20, '...' ) : 'N/A';
				$base_svc_price = $this->bm_fetch_service_price_by_service_id_and_date( $id, $date );
				$booking_price  = $this->bm_fetch_total_price( str_replace( $booking_currency, '', $base_svc_price ), $total_service_booking );
				$total_cost     = $booking_price;

				$svc_price_module_id = $this->bm_fetch_external_service_price_module_by_service_id_and_date( $id, $date );

				$booking_fields['service_id']               = $id;
				$booking_fields['booking_slots']            = $time;
				$booking_fields['booking_date']             = $date;
				$booking_fields['service_name']             = $svc_name;
				$booking_fields['total_service_booking']    = $total_service_booking;
				$booking_fields['extra_svc_booked']         = ! empty( $extra_svc_ids ) && is_array( $extra_svc_ids ) ? implode( ',', $extra_svc_ids ) : $extra_svc_ids;
				$booking_fields['total_extra_slots_booked'] = ! empty( $total_extra_slots_booked ) && is_array( $total_extra_slots_booked ) ? implode( ',', $total_extra_slots_booked ) : $total_extra_slots_booked;
				$booking_fields['base_svc_price']           = $base_svc_price;
				$booking_fields['service_cost']             = $booking_price;
				$booking_fields['svc_price_module_id']      = $svc_price_module_id;

				// Calculate extra service price and total cost
				if ( isset( $extra_svc_ids ) && ! empty( $extra_svc_ids ) && isset( $total_extra_slots_booked ) && ! empty( $total_extra_slots_booked ) ) {
					$extra_svc_ids      = is_array( $extra_svc_ids ) ? implode( ',', $extra_svc_ids ) : $extra_svc_ids;
					$total_slots_booked = $total_extra_slots_booked;
					$extra_total        = array();
					$additional         = "id in($extra_svc_ids)";

					$extra_price = $dbhandler->get_all_result( 'EXTRA', 'extra_price', 1, 'results', 0, false, null, false, $additional );
					$extra_price = array_column( $extra_price, 'extra_price' );
					$i           = 1;

					if ( ! empty( $extra_price ) && ! empty( $total_slots_booked ) ) {
						foreach ( $extra_price as $key => $price ) {
							if ( ! empty( $price ) ) {
								$extra_total[ $key ] = $this->bm_fetch_total_price( $price, $total_slots_booked[ $key ] );
							}
							++$i;
						}
					} //end if

					if ( ! empty( $extra_total ) ) {
						$total_extra_price = array_sum( $extra_total );
						$total_cost        = ( $total_cost + $total_extra_price );
					} //end if
				} //end if

				$booking_fields['extra_svc_cost'] = $total_extra_price;
				$booking_fields['total_cost']     = $total_cost;
				$booking_fields['subtotal']       = $total_cost;
			} //end if
		} //end if

		return apply_filters( 'bm_flexibooking_backend_pre_order_booking_fields', $booking_fields );
	}//end bm_fetch_backend_order_info()


	/**
	 * Fetch Dynamic service price by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_price_by_service_id_and_date( $service_id = 0, $date = '', $type = '' ) {
		$dbhandler = new BM_DBhandler();
		$price     = 'N/A';

		if ( ! empty( $date ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$price               = isset( $service->default_price ) ? $service->default_price : 0;
				$variable_svc_prices = isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();
				if ( ! empty( $variable_svc_prices ) && isset( $variable_svc_prices['date'] ) && isset( $variable_svc_prices['price'] ) ) {
					$date_count = count( $variable_svc_prices['date'] );
					if ( $date_count == count( $variable_svc_prices['price'] ) ) {
						for ( $i = 1; $i <= $date_count; $i++ ) {
							if ( in_array( $date, $variable_svc_prices['date'], true ) ) {
								$index = (int) array_search( $date, $variable_svc_prices['date'] );
								$price = isset( $variable_svc_prices['price'][ $index ] ) ? $variable_svc_prices['price'][ $index ] : 0;
							}
						}
					}
				}
			}
		}
                $price = apply_filters( 'bm_service_price_by_services_id_and_date', $price, $service, $date );

		if ( $type == 'global_format' ) {
			if ( ! empty( $price ) && $price !== 'N/A' ) {
				$price = $this->bm_fetch_price_in_global_settings_format( $price, true );
			} elseif ( empty( $price ) ) {
				$price = $this->bm_fetch_price_in_global_settings_format( $price );
			}
		} elseif ( $type == 'normal_format' ) {
			$price = $this->bm_fetch_price_in_global_settings_format( $price );
		}

		return $price;
	}//end bm_fetch_service_price_by_service_id_and_date()


	/**
	 * Fetch Dynamic external service module by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_external_service_price_module_by_service_id_and_date( $service_id = 0, $date = '' ) {
		$dbhandler             = new BM_DBhandler();
		$external_price_module = 0;

		if ( ! empty( $date ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$external_price_module = ! empty( $service ) && isset( $service->external_price_module ) ? $service->external_price_module : 0;
				$variable_module       = isset( $service->variable_svc_price_modules ) ? maybe_unserialize( $service->variable_svc_price_modules ) : array();
				if ( ! empty( $variable_module ) && isset( $variable_module['date'] ) && isset( $variable_module['module'] ) && is_array( $variable_module['date'] ) && is_array( $variable_module['module'] ) ) {
					$date_count   = count( $variable_module['date'] );
					$module_count = count( $variable_module['module'] );
					if ( $date_count == $module_count ) {
						for ( $i = 1; $i <= $date_count; $i++ ) {
							if ( in_array( $date, $variable_module['date'], true ) ) {
								$index                 = (int) array_search( $date, $variable_module['date'], true );
								$external_price_module = isset( $variable_module['module'][ $index ] ) ? $variable_module['module'][ $index ] : 0;
							}
						}
					}
				}
			}
		}

		return $external_price_module;
	}//end bm_fetch_external_service_price_module_by_service_id_and_date()


	/**
	 * Fetch service specific html with age groups and ranges
	 *
	 * @author Darpan
	 */
	public function bm_fetch_age_ranges_defined_in_service( $service_id, $booking_key = '' ) {
		$dbhandler = new BM_DBhandler();
		$data      = array();

		if ( ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$service_options = isset( $service->service_options ) ? maybe_unserialize( $service->service_options ) : array();
				if ( ! empty( $service_options ) && array_filter( $service_options ) ) {
					$svc_infant_age_from = isset( $service_options['svc_infant_age_from'] ) ? $service_options['svc_infant_age_from'] : '';
					$svc_infant_age_to   = isset( $service_options['svc_infant_age_to'] ) ? $service_options['svc_infant_age_to'] : '';
					$svc_infant_disable  = isset( $service_options['svc_infant_disable'] ) ? $service_options['svc_infant_disable'] : 0;

					$svc_children_age_from = isset( $service_options['svc_children_age_from'] ) ? $service_options['svc_children_age_from'] : '';
					$svc_children_age_to   = isset( $service_options['svc_children_age_to'] ) ? $service_options['svc_children_age_to'] : '';
					$svc_children_disable  = isset( $service_options['svc_children_disable'] ) ? $service_options['svc_children_disable'] : 0;

					$svc_adult_age_from = isset( $service_options['svc_adult_age_from'] ) ? $service_options['svc_adult_age_from'] : '';
					$svc_adult_age_to   = isset( $service_options['svc_adult_age_to'] ) ? $service_options['svc_adult_age_to'] : '';
					$svc_adult_disable  = isset( $service_options['svc_adult_disable'] ) ? $service_options['svc_adult_disable'] : 0;

					$svc_senior_age_from = isset( $service_options['svc_senior_age_from'] ) ? $service_options['svc_senior_age_from'] : '';
					$svc_senior_age_to   = isset( $service_options['svc_senior_age_to'] ) ? $service_options['svc_senior_age_to'] : '';
					$svc_senior_disable  = isset( $service_options['svc_senior_disable'] ) ? $service_options['svc_senior_disable'] : 0;

					if ( ( $svc_infant_age_from !== '' ) && ( $svc_infant_age_to !== '' ) && ( $svc_infant_disable == 0 ) ) {
						$infant = array(
							'from' => $svc_infant_age_from,
							'to'   => $svc_infant_age_to,
						);

						$data['infant'] = $infant;
					}

					if ( ( $svc_children_age_from !== '' ) && ( $svc_children_age_to !== '' ) && ( $svc_children_disable == 0 ) ) {
						$children = array(
							'from' => $svc_children_age_from,
							'to'   => $svc_children_age_to,
						);

						$data['children'] = $children;
					}

					if ( ( $svc_adult_age_from !== '' ) && ( $svc_adult_age_to !== '' ) && ( $svc_adult_disable == 0 ) ) {
						$adult = array(
							'from' => $svc_adult_age_from,
							'to'   => $svc_adult_age_to,
						);

						$data['adult'] = $adult;
					}

					if ( ( $svc_senior_age_from !== '' ) && ( $svc_senior_age_to !== '' ) && ( $svc_senior_disable == 0 ) ) {
						$senior = array(
							'from' => $svc_senior_age_from,
							'to'   => $svc_senior_age_to,
						);

						$data['senior'] = $senior;
					}
				}
			}
		}

		return $data;
	}//end bm_fetch_age_ranges_defined_in_service()


	/**
	 * Fetch service specific disabled age groups and ranges
	 *
	 * @author Darpan
	 */
	public function bm_fetch_disabled_age_ranges_in_service( $service_id ) {
		$dbhandler = new BM_DBhandler();
		$data      = array(
			'disable' => array(
				'0' => 0,
				'1' => 0,
				'2' => 0,
				'3' => 0,
			),
		);

		if ( ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$service_options = isset( $service->service_options ) ? maybe_unserialize( $service->service_options ) : array();
				if ( ! empty( $service_options ) ) {
					$svc_infant_disable   = isset( $service_options['svc_infant_disable'] ) ? $service_options['svc_infant_disable'] : 0;
					$svc_children_disable = isset( $service_options['svc_children_disable'] ) ? $service_options['svc_children_disable'] : 0;
					$svc_adult_disable    = isset( $service_options['svc_adult_disable'] ) ? $service_options['svc_adult_disable'] : 0;
					$svc_senior_disable   = isset( $service_options['svc_senior_disable'] ) ? $service_options['svc_senior_disable'] : 0;

					if ( $svc_infant_disable == 1 ) {
						$data['disable'][0] = 1;
					}

					if ( $svc_children_disable == 1 ) {
						$data['disable'][1] = 1;
					}

					if ( $svc_adult_disable == 1 ) {
						$data['disable'][2] = 1;
					}

					if ( $svc_senior_disable == 1 ) {
						$data['disable'][3] = 1;
					}
				}
			}
		}

		return $data;
	}//end bm_fetch_disabled_age_ranges_in_service()


	/**
	 * Fetch external service html with age groups and ranges
	 *
	 * @author Darpan
	 */
	public function bm_fetch_external_service_price_module_age_ranges( $module_id, $service_id ) {
		$dbhandler = new BM_DBhandler();
		$data      = array();

		if ( ! empty( $module_id ) && ! empty( $service_id ) ) {
			$disabled_svc_age_groups = $this->bm_fetch_disabled_age_ranges_in_service( $service_id );

			$module = $dbhandler->get_row( 'EXTERNAL_SERVICE_PRICE_MODULE', $module_id );

			if ( ! empty( $module ) && ! empty( $disabled_svc_age_groups ) ) {
				$module_values = isset( $module->module_values ) ? maybe_unserialize( $module->module_values ) : array();
				if ( ! empty( $module_values ) ) {
					$age_group_name    = isset( $module_values['age_group_name'] ) ? $module_values['age_group_name'] : array();
					$age_group_from    = isset( $module_values['age_group_from'] ) ? $module_values['age_group_from'] : array();
					$age_group_to      = isset( $module_values['age_group_to'] ) ? $module_values['age_group_to'] : array();
					$age_group_disable = isset( $module_values['age_group_disable'] ) ? $module_values['age_group_disable'] : array();

					if ( ! empty( $age_group_disable ) && is_array( $age_group_disable ) ) {
						$disable_count = count( $age_group_disable );
						for ( $i = 0; $i < $disable_count; $i++ ) {
							if ( isset( $disabled_svc_age_groups['disable'][ $i ] ) && ( $disabled_svc_age_groups['disable'][ $i ] == 0 ) ) {
								if ( isset( $age_group_disable[ $i ] ) && ( $age_group_disable[ $i ] == 0 ) ) {
									$name = isset( $age_group_name[ $i ] ) ? strtolower( $age_group_name[ $i ] ) : '';
									$from = isset( $age_group_from[ $i ] ) ? $age_group_from[ $i ] : '';
									$to   = isset( $age_group_to[ $i ] ) ? $age_group_to[ $i ] : '';

									if ( ( $from !== '' ) && ( $to !== '' ) && ( $name !== '' ) ) {
										$data[ $name ]['from'] = $from;
										$data[ $name ]['to']   = $to;
									}
								}
							}
						}
					}
				}
			}
		}

		return $data;
	}//end bm_fetch_external_service_price_module_age_ranges()


	/**
	 * Fetch external service html with age groups and ranges
	 *
	 * @author Darpan
	 */
	public function bm_fetch_module_age_range_html( $data, $booking_key = '' ) {
		$dbhandler = new BM_DBhandler();
		$html      = '';
		$button    = __( 'Check Discount', 'service-booking' );

		$age_label      = __( 'Age', 'service-booking' );
		$age_from_label = __( 'Age From', 'service-booking' );
		$age_to_label   = __( 'Age To', 'service-booking' );
		$total_label    = __( 'Total Persons', 'service-booking' );
		$total_persons  = $dbhandler->bm_fetch_data_from_transient( 'flexi_total_person_discounted_' . $booking_key );

		if ( ! empty( $data ) && is_array( $data ) ) {
			$infant_age_from = isset( $data['infant']['from'] ) ? $data['infant']['from'] : '';

			if ( $infant_age_from == '' ) {
				$infant_age_from = isset( $data['infante']['from'] ) ? $data['infante']['from'] : '';
			}

			$infant_age_to = isset( $data['infant']['to'] ) ? $data['infant']['to'] : '';

			if ( $infant_age_to == '' ) {
				$infant_age_to = isset( $data['infante']['to'] ) ? $data['infante']['to'] : '';
			}

			$children_age_from = isset( $data['children']['from'] ) ? $data['children']['from'] : '';

			if ( $children_age_from == '' ) {
				$children_age_from = isset( $data['bambini']['from'] ) ? $data['bambini']['from'] : '';
			}

			$children_age_to = isset( $data['children']['to'] ) ? $data['children']['to'] : '';

			if ( $children_age_to == '' ) {
				$children_age_to = isset( $data['bambini']['to'] ) ? $data['bambini']['to'] : '';
			}

			$adult_age_from = isset( $data['adult']['from'] ) ? $data['adult']['from'] : '';

			if ( $adult_age_from == '' ) {
				$adult_age_from = isset( $data['adulto']['from'] ) ? $data['adulto']['from'] : '';
			}

			$adult_age_to = isset( $data['adult']['to'] ) ? $data['adult']['to'] : '';

			if ( $adult_age_to == '' ) {
				$adult_age_to = isset( $data['adulto']['to'] ) ? $data['adulto']['to'] : '';
			}

			$senior_age_from = isset( $data['senior']['from'] ) ? $data['senior']['from'] : '';

			if ( $senior_age_from == '' ) {
				$senior_age_from = isset( $data['anziano']['from'] ) ? $data['anziano']['from'] : '';
			}

			$senior_age_to = isset( $data['senior']['to'] ) ? $data['senior']['to'] : '';

			if ( $senior_age_to == '' ) {
				$senior_age_to = isset( $data['anziano']['to'] ) ? $data['anziano']['to'] : '';
			}

			if ( array_filter( $data ) ) {
				$html .= '<div class="checkout_age_range_fields">';

				if ( ( $infant_age_from !== '' ) && ( $infant_age_to !== '' ) ) {
					$title = $age_label . ': ' . $infant_age_from . '-' . $infant_age_to;
					$html .= '<div id="age_type_0" class="hidden">';
					$html .= '<span>';
					$html .= '<input name="age_group_from_0" type="hidden" class="age_range_fields" id="age_group_from_0" value="' . $infant_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input name="age_group_to_0" type="hidden" class="age_range_fields" id="age_group_to_0" value="' . $infant_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Infants', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input name="age_group_total_0" type="number" class="age_range_fields" id="age_group_total_0" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[0] ) ? esc_attr( $total_persons[0] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				if ( ( $children_age_from !== '' ) && ( $children_age_to !== '' ) ) {
					$title = $age_label . ': ' . $children_age_from . '-' . $children_age_to;
					$html .= '<div id="age_type_1" class="hidden">';
					$html .= '<span>';
					$html .= '<input name="age_group_from_1" type="hidden" class="age_range_fields" id="age_group_from_1" value="' . $children_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input name="age_group_to_1" type="hidden" class="age_range_fields" id="age_group_to_1" value="' . $children_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Children', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input name="age_group_total_1" type="number" class="age_range_fields" id="age_group_total_1" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[1] ) ? esc_attr( $total_persons[1] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				if ( ( $adult_age_from !== '' ) && ( $adult_age_to !== '' ) ) {
					$title = $age_label . ': ' . $adult_age_from . '-' . $adult_age_to;
					$html .= '<div id="age_type_2" class="hidden">';
					$html .= '<span>';
					$html .= '<input name="age_group_from_2" type="hidden" class="age_range_fields" id="age_group_from_2" value="' . $adult_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input name="age_group_to_2" type="hidden" class="age_range_fields" id="age_group_to_2" value="' . $adult_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Adults', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input name="age_group_total_2" type="number" class="age_range_fields" id="age_group_total_2" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[2] ) ? esc_attr( $total_persons[2] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				if ( ( $senior_age_from !== '' ) && ( $senior_age_to !== '' ) ) {
					$title = $age_label . ': ' . $senior_age_from . '-' . $senior_age_to;
					$html .= '<div id="age_type_3" class="hidden">';
					$html .= '<span>';
					$html .= '<input name="age_group_from_3" type="hidden" class="age_range_fields" id="age_group_from_3" value="' . $senior_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input name="age_group_to_3" type="hidden" class="age_range_fields" id="age_group_to_3" value="' . $senior_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Seniors', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input name="age_group_total_3" type="number" class="age_range_fields" id="age_group_total_3" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[3] ) ? esc_attr( $total_persons[3] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				$html .= '</div>';
			}
		}

		return wp_kses( $html, $this->bm_fetch_expanded_allowed_tags() );
	}//end bm_fetch_module_age_range_html()


	/**
	 * Fetch external service html with age groups and ranges for backend order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_backend_module_age_range_html( $data, $booking_key = '' ) {
		$dbhandler     = new BM_DBhandler();
		$total_persons = $dbhandler->bm_fetch_data_from_transient( 'flexi_total_person_discounted_' . $booking_key );
		$button        = __( 'Check Discount', 'service-booking' );
		$age_label     = __( 'Age', 'service-booking' );
		$html          = '';

		if ( ! empty( $data ) && is_array( $data ) ) {
			$infant_age_from = isset( $data['infant']['from'] ) ? $data['infant']['from'] : '';

			if ( $infant_age_from == '' ) {
				$infant_age_from = isset( $data['infante']['from'] ) ? $data['infante']['from'] : '';
			}

			$infant_age_to = isset( $data['infant']['to'] ) ? $data['infant']['to'] : '';

			if ( $infant_age_to == '' ) {
				$infant_age_to = isset( $data['infante']['to'] ) ? $data['infante']['to'] : '';
			}

			$children_age_from = isset( $data['children']['from'] ) ? $data['children']['from'] : '';

			if ( $children_age_from == '' ) {
				$children_age_from = isset( $data['bambini']['from'] ) ? $data['bambini']['from'] : '';
			}

			$children_age_to = isset( $data['children']['to'] ) ? $data['children']['to'] : '';

			if ( $children_age_to == '' ) {
				$children_age_to = isset( $data['bambini']['to'] ) ? $data['bambini']['to'] : '';
			}

			$adult_age_from = isset( $data['adult']['from'] ) ? $data['adult']['from'] : '';

			if ( $adult_age_from == '' ) {
				$adult_age_from = isset( $data['adulto']['from'] ) ? $data['adulto']['from'] : '';
			}

			$adult_age_to = isset( $data['adult']['to'] ) ? $data['adult']['to'] : '';

			if ( $adult_age_to == '' ) {
				$adult_age_to = isset( $data['adulto']['to'] ) ? $data['adulto']['to'] : '';
			}

			$senior_age_from = isset( $data['senior']['from'] ) ? $data['senior']['from'] : '';

			if ( $senior_age_from == '' ) {
				$senior_age_from = isset( $data['anziano']['from'] ) ? $data['anziano']['from'] : '';
			}

			$senior_age_to = isset( $data['senior']['to'] ) ? $data['senior']['to'] : '';

			if ( $senior_age_to == '' ) {
				$senior_age_to = isset( $data['anziano']['to'] ) ? $data['anziano']['to'] : '';
			}

			if ( array_filter( $data ) ) {
				$html .= '<div class="checkout_age_range_fields">';

				if ( ( $infant_age_from !== '' ) && ( $infant_age_to !== '' ) ) {
					$title = $age_label . ': ' . $infant_age_from . '-' . $infant_age_to;
					$html .= '<div id="age_type_0" class="hidden">';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_from_0" value="' . $infant_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_to_0" value="' . $infant_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Infants', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input type="number" class="age_range_fields" id="age_group_total_0" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[0] ) ? esc_attr( $total_persons[0] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				if ( ( $children_age_from !== '' ) && ( $children_age_to !== '' ) ) {
					$title = $age_label . ': ' . $children_age_from . '-' . $children_age_to;
					$html .= '<div id="age_type_1" class="hidden">';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_from_1" value="' . $children_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_to_1" value="' . $children_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Children', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input type="number" class="age_range_fields" id="age_group_total_1" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[1] ) ? esc_attr( $total_persons[1] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				if ( ( $adult_age_from !== '' ) && ( $adult_age_to !== '' ) ) {
					$title = $age_label . ': ' . $adult_age_from . '-' . $adult_age_to;
					$html .= '<div id="age_type_2" class="hidden">';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_from_2" value="' . $adult_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_to_2" value="' . $adult_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Adults', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input type="number" class="age_range_fields" id="age_group_total_2" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[2] ) ? esc_attr( $total_persons[2] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				if ( ( $senior_age_from !== '' ) && ( $senior_age_to !== '' ) ) {
					$title = $age_label . ': ' . $senior_age_from . '-' . $senior_age_to;
					$html .= '<div id="age_type_3" class="hidden">';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_from_3" value="' . $senior_age_from . '">';
					$html .= '</span>';
					$html .= '<span>';
					$html .= '<input type="hidden" class="age_range_fields" id="age_group_to_3" value="' . $senior_age_to . '">';
					$html .= '</span>';
					$html .= '</div>';

					$html .= '<div class="age_label_total_parent_div">';
					$html .= '<div class="age_label_input_div">';
					$html .= '<label class="checkout_age_from_label">' . __( 'Seniors', 'service-booking' ) . '</label>';
					$html .= '<i class="fa fa-info-circle" aria-hidden="true" title="' . $title . '"></i>';
					$html .= '</div>';
					$html .= '<div class="age_total_input_div">';
					$html .= '<input type="number" class="age_range_fields" id="age_group_total_3" style="margin-left:3px;" step="1" min="0" placeholder="total" value="' . ( ! empty( $total_persons ) && isset( $total_persons[3] ) ? esc_attr( $total_persons[3] ) : 0 ) . '" autocomplete="off">';
					$html .= '</div>';
					$html .= '</div>';
				}

				$html .= '</div>';
			}
		}

		return wp_kses( $html, $this->bm_fetch_expanded_allowed_tags() );
	}//end bm_fetch_backend_module_age_range_html()


	/**
	 * Fetch discounted price for age groups
	 *
	 * @author Darpan
	 */
	public function bm_fetch_age_type_booking_discounted_price( $data ) {
		$dbhandler              = new BM_DBhandler();
		$booking_key            = isset( $data['booking_key'] ) ? $data['booking_key'] : '';
		$from_data              = isset( $data['from_data'] ) ? $data['from_data'] : array();
		$to_data                = isset( $data['to_data'] ) ? $data['to_data'] : array();
		$total_data             = isset( $data['total_data'] ) ? $data['total_data'] : array();
		$new_price              = array();
		$new_discount_price     = array();
		$age_group_discount     = array();
		$new_age_group_discount = array();
		$discounted_price       = 0;
		$group_total_price      = 0;
		$remaining_people       = 0;
		$coupon_discount        = 0;
		$status                 = 'error';

		$total_eligible_persons_for_group_discount = 0;

		if ( ! empty( $booking_key ) && ! empty( $from_data ) && ! empty( $to_data ) && ! empty( $total_data ) ) {
			$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );

			if ( ! empty( $order_data ) ) {
				$service_id           = isset( $order_data['service_id'] ) ? esc_attr( $order_data['service_id'] ) : 0;
				$module_id            = isset( $order_data['svc_price_module_id'] ) ? esc_attr( $order_data['svc_price_module_id'] ) : 0;
				$base_price           = isset( $order_data['base_svc_price'] ) ? esc_attr( $order_data['base_svc_price'] ) : 0;
				$total_booking        = isset( $order_data['total_service_booking'] ) ? esc_attr( $order_data['total_service_booking'] ) : 0;
				$total_cost           = isset( $order_data['total_cost'] ) ? esc_attr( $order_data['total_cost'] ) : 0;
				$total_extra_svc_cost = isset( $order_data['extra_svc_cost'] ) ? $order_data['extra_svc_cost'] : 0;
				$total_ext_svc_slots  = isset( $order_data['total_extra_slots_booked'] ) && ! empty( $order_data['total_extra_slots_booked'] ) ? array_sum( explode( ',', $order_data['total_extra_slots_booked'] ) ) : array();
				$total_actual_booking = ! empty( $total_ext_svc_slots ) ? ( intval( $total_booking ) + intval( $total_ext_svc_slots ) ) : $total_booking;
				$total_discountable   = array_sum( $total_data );

				$coupon_applied  = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
				$coupon_discount = ! empty( $coupon_applied ) ? floatval( array_sum( array_column( $coupon_applied, 'coupon_discount' ) ) ) : 0;

				if ( $total_discountable <= $total_booking ) {
					$undiscounted_svcs = intval( $total_booking ) - intval( $total_discountable );
					$additional_price  = $this->bm_fetch_total_price( $base_price, $undiscounted_svcs );
					$additional_price  = ! empty( $total_extra_svc_cost ) ? ( $additional_price + $total_extra_svc_cost ) : $additional_price;

					if ( ! empty( $service_id ) && ! empty( $module_id ) && ! empty( $total_booking ) ) {
						$service         = $dbhandler->get_row( 'SERVICE', $service_id );
						$service_options = ! empty( $service ) && isset( $service->service_options ) ? maybe_unserialize( $service->service_options ) : array();
						$module          = $dbhandler->get_row( 'EXTERNAL_SERVICE_PRICE_MODULE', $module_id );
						$module_values   = isset( $module->module_values ) ? maybe_unserialize( $module->module_values ) : array();

						if ( ! empty( $module_values ) ) {
							if ( ! empty( $service_options ) && array_filter( $service_options ) ) {
								$age_group_from = array(
									'0' => isset( $service_options['svc_infant_age_from'] ) ? $service_options['svc_infant_age_from'] : '',
									'1' => isset( $service_options['svc_children_age_from'] ) ? $service_options['svc_children_age_from'] : '',
									'2' => isset( $service_options['svc_adult_age_from'] ) ? $service_options['svc_adult_age_from'] : '',
									'3' => isset( $service_options['svc_senior_age_from'] ) ? $service_options['svc_senior_age_from'] : '',
								);

								$age_group_to = array(
									'0' => isset( $service_options['svc_infant_age_to'] ) ? $service_options['svc_infant_age_to'] : '',
									'1' => isset( $service_options['svc_children_age_to'] ) ? $service_options['svc_children_age_to'] : '',
									'2' => isset( $service_options['svc_adult_age_to'] ) ? $service_options['svc_adult_age_to'] : '',
									'3' => isset( $service_options['svc_senior_age_to'] ) ? $service_options['svc_senior_age_to'] : '',
								);

								$age_group_disable = array(
									'0' => isset( $service_options['svc_infant_disable'] ) ? $service_options['svc_infant_disable'] : 0,
									'1' => isset( $service_options['svc_children_disable'] ) ? $service_options['svc_children_disable'] : 0,
									'2' => isset( $service_options['svc_adult_disable'] ) ? $service_options['svc_adult_disable'] : 0,
									'3' => isset( $service_options['svc_senior_disable'] ) ? $service_options['svc_senior_disable'] : 0,
								);
							} else {
								$age_group_from    = isset( $module_values['age_group_from'] ) ? $module_values['age_group_from'] : array();
								$age_group_to      = isset( $module_values['age_group_to'] ) ? $module_values['age_group_to'] : array();
								$age_group_disable = isset( $module_values['age_group_disable'] ) ? $module_values['age_group_disable'] : array();
							}

							$age_group_price = isset( $module_values['age_group_price'] ) ? $module_values['age_group_price'] : array();

							$group_from    = isset( $module_values['group_from'] ) ? $module_values['group_from'] : array();
							$group_to      = isset( $module_values['group_to'] ) ? $module_values['group_to'] : array();
							$group_price   = isset( $module_values['group_price'] ) ? $module_values['group_price'] : array();
							$group_disable = isset( $module_values['group_disable'] ) ? $module_values['group_disable'] : array();

							if ( is_array( $from_data ) && is_array( $total_data ) && is_array( $to_data ) ) {
								foreach ( $from_data as $key => $from ) {
									$age_wise_index = (int) array_search( $from, $age_group_from, true );
									if ( $age_wise_index !== false && isset( $age_group_disable[ $age_wise_index ] ) && ( $age_group_disable[ $age_wise_index ] == 0 ) ) {
										$age_group_total_data = isset( $total_data[ $key ] ) ? $total_data[ $key ] : 0;
										$age_wise_price       = isset( $age_group_price[ $age_wise_index ] ) ? $age_group_price[ $age_wise_index ] : 0;
										$age_wise_discount    = ( $base_price > $age_wise_price ) ? floatval( $base_price ) - floatval( $age_wise_price ) : floatval( $age_wise_price ) - floatval( $base_price );

										$new_price[ $age_wise_index ]          = $this->bm_fetch_total_price( $age_wise_price, $age_group_total_data );
										$age_group_discount[ $age_wise_index ] = $age_wise_discount;

										if ( $base_price >= $age_wise_price ) {
											$dbhandler->update_global_option_value( 'negative_discount_age_group_' . $age_wise_index . '_' . $booking_key, 0 );
										} else {
											$dbhandler->update_global_option_value( 'negative_discount_age_group_' . $age_wise_index . '_' . $booking_key, 1 );
											return 'negative';
										}
									}
								}
							}

							if ( ! empty( $new_price ) ) {
								if ( ! empty( $group_to ) && is_array( $group_to ) ) {
									$total_adult_persons  = isset( $total_data['2'] ) ? $total_data['2'] : 0;
									$total_senior_persons = isset( $total_data['3'] ) ? $total_data['3'] : 0;

									$total_eligible_persons_for_group_discount = intval( $total_adult_persons ) + intval( $total_senior_persons );

									if ( $total_eligible_persons_for_group_discount > 0 ) {
										$remaining_people = $total_eligible_persons_for_group_discount;

										foreach ( $group_to as $key => $to ) {
											if ( $remaining_people <= 0 ) {
												break;
											}

											if ( isset( $group_disable[ $key ] ) && $group_disable[ $key ] == 0 ) {
												if ( $total_eligible_persons_for_group_discount >= $group_from[ $key ] ) {
													$people_in_group   = min( $remaining_people, $to - $group_from[ $key ] + 1 );
													$group_price_value = isset( $group_price[ $key ] ) ? $group_price[ $key ] : 0;

													if ( $people_in_group > 0 ) {
														$group_total_price += $group_price_value;
													}

													$group_wise_discount      = ( $base_price > $group_price_value ) ? floatval( $base_price ) - floatval( $group_price_value ) : floatval( $group_price_value ) - floatval( $base_price );
													$new_discount_price[]     = $group_price_value;
													$new_age_group_discount[] = $group_wise_discount;
													$remaining_people        -= $people_in_group;
												}
											}
										}
									}
								}

								if ( ! empty( $new_discount_price ) ) {
									if ( isset( $new_price['2'] ) ) {
										unset( $new_price['2'] );
									}

									if ( isset( $new_price['3'] ) ) {
										unset( $new_price['3'] );
									}

									$new_price['2']     = $group_total_price;
									$total_actual_price = $this->bm_fetch_total_price( $base_price, $total_eligible_persons_for_group_discount );

									$age_group_discount['group_discount'] = ( $total_actual_price > $group_total_price ) ? floatval( $total_actual_price ) - floatval( $group_total_price ) : floatval( $group_total_price ) - floatval( $total_actual_price );

									if ( $total_actual_price >= $group_total_price ) {
										$dbhandler->update_global_option_value( 'negative_group_discount_' . $booking_key, 0 );
									} else {
										$dbhandler->update_global_option_value( 'negative_group_discount_' . $booking_key, 1 );
										return 'negative';
									}
								}

								$discounted_price = array_sum( $new_price ) + $additional_price;

								if ( $discounted_price < 0 ) {
									return 'negative';
								}

								$final_discount = floatval( $total_cost ) - floatval( $discounted_price );

								if ( $final_discount < 0 ) {
									return 'negative';
								}

								$dbhandler->update_global_option_value( 'price_module_discount_' . $booking_key, $final_discount );

								$final_discount   = $final_discount + $coupon_discount;
								$discounted_price = $discounted_price - $coupon_discount;

								if ( $discounted_price < 0 || $total_cost < $discounted_price ) {
									return 'negative';
								}

								if ( $total_cost >= $discounted_price ) {
									$dbhandler->update_global_option_value( 'negative_discount_' . $booking_key, 0 );
								} else {
									$dbhandler->update_global_option_value( 'negative_discount_' . $booking_key, 1 );
									return 'negative';
								}

								$order_data['subtotal']   = $total_cost;
								$order_data['total_cost'] = $discounted_price;
								$order_data['discount']   = $final_discount;
								$status                   = 'success';
								$dbhandler->bm_save_data_to_transient( 'flexi_age_wise_discount_' . $booking_key, $age_group_discount, 72 );
								$dbhandler->bm_save_data_to_transient( 'flexi_age_wise_total_price_' . $booking_key, $new_price, 72 );
								$dbhandler->bm_save_data_to_transient( 'flexi_total_person_discounted_' . $booking_key, $total_data, 72 );
								$dbhandler->bm_save_data_to_transient( 'flexi_base_price_' . $booking_key, $base_price, 72 );
								$dbhandler->bm_save_data_to_transient( 'discounted_' . $booking_key, $order_data, 72 );
								$dbhandler->update_global_option_value( 'discount_' . $booking_key, 1 );
							}
						}
					}
				} else {
					$status = 'excess';
				}
			}
		}

		return $status;
	}//end bm_fetch_age_type_booking_discounted_price()


	/**
	 * Fetch discounted price for age groups
	 *
	 * @author Darpan
	 */
	public function bm_fetch_backend_age_type_booking_discounted_price( $order_data ) {
		$dbhandler              = new BM_DBhandler();
		$booking_key            = isset( $order_data['booking_key'] ) ? $order_data['booking_key'] : '';
		$from_data              = isset( $order_data['from_data'] ) ? $order_data['from_data'] : array();
		$to_data                = isset( $order_data['to_data'] ) ? $order_data['to_data'] : array();
		$total_data             = isset( $order_data['total_data'] ) ? $order_data['total_data'] : array();
		$service_id             = isset( $order_data['service_id'] ) ? $order_data['service_id'] : 0;
		$date                   = isset( $order_data['booking_date'] ) ? $order_data['booking_date'] : 0;
		$svc_price_module_id    = $this->bm_fetch_external_service_price_module_by_service_id_and_date( $service_id, $date );
		$module_id              = $svc_price_module_id;
		$base_price             = isset( $order_data['base_svc_price'] ) ? $order_data['base_svc_price'] : 0;
		$total_booking          = isset( $order_data['total_service_booking'] ) ? $order_data['total_service_booking'] : 0;
		$total_cost             = isset( $order_data['total_cost'] ) ? $order_data['total_cost'] : 0;
		$total_extra_svc_cost   = isset( $order_data['extra_svc_cost'] ) ? $order_data['extra_svc_cost'] : 0;
		$total_ext_svc_slots    = isset( $order_data['total_extra_slots_booked'] ) ? $order_data['total_extra_slots_booked'] : array();
		$extra_svc_ids          = isset( $order_data['extra_svc_booked'] ) ? $order_data['extra_svc_booked'] : array();
		$new_price              = array();
		$new_discount_price     = array();
		$age_group_discount     = array();
		$new_age_group_discount = array();
		$discounted_price       = 0;
		$group_total_price      = 0;
		$remaining_people       = 0;
		$status                 = 'error';

		$total_eligible_persons_for_group_discount = 0;
		$order_data['extra_svc_booked']            = ! empty( $extra_svc_ids ) && is_array( $extra_svc_ids ) ? implode( ',', $extra_svc_ids ) : '';
		$order_data['svc_price_module_id']         = $svc_price_module_id;
		$order_data['total_extra_slots_booked']    = ! empty( $total_ext_svc_slots ) && is_array( $total_ext_svc_slots ) ? implode( ',', $total_ext_svc_slots ) : '';

		if ( isset( $order_data['booking_key'] ) ) {
			unset( $order_data['booking_key'] );
		}

		if ( isset( $order_data['from_data'] ) ) {
			unset( $order_data['from_data'] );
		}

		if ( isset( $order_data['to_data'] ) ) {
			unset( $order_data['to_data'] );
		}

		if ( isset( $order_data['total_data'] ) ) {
			unset( $order_data['total_data'] );
		}

		$dbhandler->bm_save_data_to_transient( $booking_key, $order_data, 72 );
		$dbhandler->update_global_option_value( 'backend_order_data_' . $booking_key, 1 );

		if ( ! empty( $booking_key ) && ! empty( $from_data ) && ! empty( $to_data ) && ! empty( $total_data ) ) {
			if ( ! empty( $total_ext_svc_slots ) && is_array( $total_ext_svc_slots ) ) {
				$total_ext_svc_slots = array_sum( $total_ext_svc_slots );
			}

			$total_actual_booking = ! empty( $total_ext_svc_slots ) ? ( intval( $total_booking ) + intval( $total_ext_svc_slots ) ) : $total_booking;
			$total_discountable   = array_sum( $total_data );

			if ( $total_discountable <= $total_booking ) {
				$undiscounted_svcs = intval( $total_booking ) - intval( $total_discountable );
				$additional_price  = $this->bm_fetch_total_price( $base_price, $undiscounted_svcs );
				$additional_price  = ! empty( $total_extra_svc_cost ) ? ( $additional_price + $total_extra_svc_cost ) : $additional_price;

				if ( ! empty( $service_id ) && ! empty( $module_id ) && ! empty( $total_booking ) ) {
					$service         = $dbhandler->get_row( 'SERVICE', $service_id );
					$service_options = ! empty( $service ) && isset( $service->service_options ) ? maybe_unserialize( $service->service_options ) : array();
					$module          = $dbhandler->get_row( 'EXTERNAL_SERVICE_PRICE_MODULE', $module_id );
					$module_values   = isset( $module->module_values ) ? maybe_unserialize( $module->module_values ) : array();

					if ( ! empty( $module_values ) ) {
						if ( ! empty( $service_options ) && array_filter( $service_options ) ) {
							$age_group_from = array(
								'0' => isset( $service_options['svc_infant_age_from'] ) ? $service_options['svc_infant_age_from'] : '',
								'1' => isset( $service_options['svc_children_age_from'] ) ? $service_options['svc_children_age_from'] : '',
								'2' => isset( $service_options['svc_adult_age_from'] ) ? $service_options['svc_adult_age_from'] : '',
								'3' => isset( $service_options['svc_senior_age_from'] ) ? $service_options['svc_senior_age_from'] : '',
							);

							$age_group_to = array(
								'0' => isset( $service_options['svc_infant_age_to'] ) ? $service_options['svc_infant_age_to'] : '',
								'1' => isset( $service_options['svc_children_age_to'] ) ? $service_options['svc_children_age_to'] : '',
								'2' => isset( $service_options['svc_adult_age_to'] ) ? $service_options['svc_adult_age_to'] : '',
								'3' => isset( $service_options['svc_senior_age_to'] ) ? $service_options['svc_senior_age_to'] : '',
							);

							$age_group_disable = array(
								'0' => isset( $service_options['svc_infant_disable'] ) ? $service_options['svc_infant_disable'] : 0,
								'1' => isset( $service_options['svc_children_disable'] ) ? $service_options['svc_children_disable'] : 0,
								'2' => isset( $service_options['svc_adult_disable'] ) ? $service_options['svc_adult_disable'] : 0,
								'3' => isset( $service_options['svc_senior_disable'] ) ? $service_options['svc_senior_disable'] : 0,
							);
						} else {
							$age_group_from    = isset( $module_values['age_group_from'] ) ? $module_values['age_group_from'] : array();
							$age_group_to      = isset( $module_values['age_group_to'] ) ? $module_values['age_group_to'] : array();
							$age_group_disable = isset( $module_values['age_group_disable'] ) ? $module_values['age_group_disable'] : array();
						}

						$age_group_price = isset( $module_values['age_group_price'] ) ? $module_values['age_group_price'] : array();

						$group_from    = isset( $module_values['group_from'] ) ? $module_values['group_from'] : array();
						$group_to      = isset( $module_values['group_to'] ) ? $module_values['group_to'] : array();
						$group_price   = isset( $module_values['group_price'] ) ? $module_values['group_price'] : array();
						$group_disable = isset( $module_values['group_disable'] ) ? $module_values['group_disable'] : array();

						if ( is_array( $from_data ) && is_array( $total_data ) && is_array( $to_data ) ) {
							foreach ( $from_data as $key => $from ) {
								$age_wise_index = (int) array_search( $from, $age_group_from, true );
								if ( $age_wise_index !== false && isset( $age_group_disable[ $age_wise_index ] ) && ( $age_group_disable[ $age_wise_index ] == 0 ) ) {
									$age_group_total_data = isset( $total_data[ $key ] ) ? $total_data[ $key ] : 0;
									$age_wise_price       = isset( $age_group_price[ $age_wise_index ] ) ? $age_group_price[ $age_wise_index ] : 0;
									$age_wise_discount    = ( $base_price > $age_wise_price ) ? floatval( $base_price ) - floatval( $age_wise_price ) : floatval( $age_wise_price ) - floatval( $base_price );

									$new_price[ $age_wise_index ]          = $this->bm_fetch_total_price( $age_wise_price, $age_group_total_data );
									$age_group_discount[ $age_wise_index ] = $age_wise_discount;

									if ( $base_price > $age_wise_price || $base_price == $age_wise_price ) {
										$dbhandler->update_global_option_value( 'negative_discount_age_group_' . $age_wise_index . '_' . $booking_key, 0 );
									} else {
										$dbhandler->update_global_option_value( 'negative_discount_age_group_' . $age_wise_index . '_' . $booking_key, 1 );
									}
								}
							}
						}

						if ( ! empty( $new_price ) ) {
							if ( ! empty( $group_to ) && is_array( $group_to ) ) {
								$total_adult_persons  = isset( $total_data['2'] ) ? $total_data['2'] : 0;
								$total_senior_persons = isset( $total_data['3'] ) ? $total_data['3'] : 0;

								$total_eligible_persons_for_group_discount = intval( $total_adult_persons ) + intval( $total_senior_persons );

								if ( $total_eligible_persons_for_group_discount > 0 ) {
									$remaining_people = $total_eligible_persons_for_group_discount;

									foreach ( $group_to as $key => $to ) {
										if ( $remaining_people <= 0 ) {
											break;
										}

										if ( isset( $group_disable[ $key ] ) && $group_disable[ $key ] == 0 ) {
											if ( $total_eligible_persons_for_group_discount >= $group_from[ $key ] ) {
												$people_in_group   = min( $remaining_people, $to - $group_from[ $key ] + 1 );
												$group_price_value = isset( $group_price[ $key ] ) ? $group_price[ $key ] : 0;

												if ( $people_in_group > 0 ) {
													$group_total_price += $group_price_value;
												}

												$group_wise_discount      = ( $base_price > $group_price_value ) ? floatval( $base_price ) - floatval( $group_price_value ) : floatval( $group_price_value ) - floatval( $base_price );
												$new_discount_price[]     = $group_price_value;
												$new_age_group_discount[] = $group_wise_discount;
												$remaining_people        -= $people_in_group;
											}
										}
									}
								}
							}

							if ( ! empty( $new_discount_price ) ) {
								if ( isset( $new_price['2'] ) ) {
									unset( $new_price['2'] );
								}

								if ( isset( $new_price['3'] ) ) {
									unset( $new_price['3'] );
								}

								$new_price['2']     = $group_total_price;
								$total_actual_price = $this->bm_fetch_total_price( $base_price, $total_eligible_persons_for_group_discount );

								$age_group_discount['group_discount'] = ( $total_actual_price > $group_total_price ) ? floatval( $total_actual_price ) - floatval( $group_total_price ) : floatval( $group_total_price ) - floatval( $total_actual_price );

								if ( $total_actual_price > $group_total_price || $total_actual_price == $group_total_price ) {
									$dbhandler->update_global_option_value( 'negative_group_discount_' . $booking_key, 0 );
								} else {
									$dbhandler->update_global_option_value( 'negative_group_discount_' . $booking_key, 1 );
								}
							}

							$discounted_price = array_sum( $new_price ) + $additional_price;
							$final_discount   = ( $total_cost > $discounted_price ) ? floatval( $total_cost ) - floatval( $discounted_price ) : floatval( $discounted_price ) - floatval( $total_cost );

							if ( $total_cost > $discounted_price || $total_cost == $discounted_price ) {
								$dbhandler->update_global_option_value( 'negative_discount_' . $booking_key, 0 );
							} else {
								$dbhandler->update_global_option_value( 'negative_discount_' . $booking_key, 1 );
							}

							$order_data['subtotal']   = $total_cost;
							$order_data['total_cost'] = $discounted_price;
							$order_data['discount']   = $final_discount;
							$status                   = 'success';
							$dbhandler->bm_save_data_to_transient( 'flexi_age_wise_discount_' . $booking_key, $age_group_discount, 72 );
							$dbhandler->bm_save_data_to_transient( 'flexi_age_wise_total_price_' . $booking_key, $new_price, 72 );
							$dbhandler->bm_save_data_to_transient( 'flexi_total_person_discounted_' . $booking_key, $total_data, 72 );
							$dbhandler->bm_save_data_to_transient( 'flexi_base_price_' . $booking_key, $base_price, 72 );
							$dbhandler->update_global_option_value( 'discount_' . $booking_key, 1 );
							$dbhandler->bm_save_data_to_transient( 'discounted_' . $booking_key, $order_data, 72 );
						}
					}
				}
			} else {
				$status = 'excess';
			}
		}

		return $status;
	}//end bm_fetch_backend_age_type_booking_discounted_price()


	/**
	 * Fetch Dynamic service stopsales by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_stopsales_by_service_id( $service_id = 0, $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$stopsales = 0;

		if ( ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) && ! empty( $date ) ) {
				$stopsales              = isset( $service->default_stopsales ) ? $service->default_stopsales : 0;
				$variable_svc_stopsales = isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();

				if ( ! empty( $variable_svc_stopsales ) ) {
					$dates          = isset( $variable_svc_stopsales['date'] ) ? $variable_svc_stopsales['date'] : array();
					$excluded_dates = isset( $variable_svc_stopsales['exclude_dates'] ) ? $variable_svc_stopsales['exclude_dates'] : array();
					$is_excluded    = ! empty( $excluded_dates ) && in_array( $date, $excluded_dates, true ) ? 1 : 0;

					if ( ! empty( $dates ) && in_array( $date, $dates, true ) && ( $is_excluded == 0 ) ) {
						$index     = (int) array_search( $date, $dates, true );
						$stopsales = isset( $variable_svc_stopsales['stopsales'][ $index ] ) ? $variable_svc_stopsales['stopsales'][ $index ] : 0;
					}

					if ( ( $is_excluded == 1 ) ) {
						$stopsales = 0;
					}
				}
			}

			if ( ! empty( $stopsales ) ) {
				if ( is_int( $stopsales ) ) {
					$stopsales = sprintf( '%.2f', $stopsales );
				} else {
					$stopsales = number_format( $stopsales, 2 );
				}
			}
		}

		return $stopsales;
	}//end bm_fetch_service_stopsales_by_service_id()


	/**
	 * Fetch Dynamic service saleswitch by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_saleswitch_by_service_id( $service_id = 0, $date = '' ) {
		$dbhandler  = new BM_DBhandler();
		$saleswitch = 0;

		if ( ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) && ! empty( $date ) ) {
				$saleswitch              = isset( $service->default_saleswitch ) ? $service->default_saleswitch : 0;
				$variable_svc_saleswitch = isset( $service->variable_saleswitch ) ? maybe_unserialize( $service->variable_saleswitch ) : array();

				if ( ! empty( $variable_svc_saleswitch ) ) {
					$dates          = isset( $variable_svc_saleswitch['date'] ) ? $variable_svc_saleswitch['date'] : array();
					$excluded_dates = isset( $variable_svc_saleswitch['exclude_dates'] ) ? $variable_svc_saleswitch['exclude_dates'] : array();
					$is_excluded    = ! empty( $excluded_dates ) && in_array( $date, $excluded_dates, true ) ? 1 : 0;

					if ( ! empty( $dates ) && in_array( $date, $dates, true ) && ( $is_excluded == 0 ) ) {
						$index      = (int) array_search( $date, $dates, true );
						$saleswitch = isset( $variable_svc_saleswitch['saleswitch'][ $index ] ) ? $variable_svc_saleswitch['saleswitch'][ $index ] : 0;
					}

					if ( ( $is_excluded == 1 ) ) {
						$saleswitch = 0;
					}
				}
			}

			if ( ! empty( $saleswitch ) ) {
				if ( is_int( $saleswitch ) ) {
					$saleswitch = sprintf( '%.2f', $saleswitch );
				} else {
					$saleswitch = number_format( $saleswitch, 2 );
				}
			}
		}

		return $saleswitch;
	}//end bm_fetch_service_saleswitch_by_service_id()


	/**
	 * Fetch Dynamic service time slot details by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_time_slot_by_service_id( $data = array(), $resp = '', $type = '' ) {
		$dbhandler   = new BM_DBhandler();
		$timezone    = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$slot_format = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$now         = new DateTime( 'now', new DateTimeZone( $timezone ) );

		$contrast           = $this->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
		$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );

		if ( ! empty( $data ) ) {
			$service_id             = isset( $data['id'] ) ? $data['id'] : 0;
			$date                   = isset( $data['date'] ) ? $data['date'] : '';
			$current_date           = $now->format( 'Y-m-d' );
			$current_time           = $now->format( 'H:i' );
			$slots_left_text        = __( 'slots left', 'service-booking' );
			$slots_full_text        = __( 'All slots booked', 'service-booking' );
			$no_slot_available_text = __( 'no slots available', 'service-booking' );
			$button_text            = __( 'Book', 'service-booking' );
			$link_class             = '';
			$stopsales              = 0;
			$currentDateTime        = $current_date . ' ' . $current_time;
			$hidden_cap_left_text   = false;

			if ( isset( $service_id ) && ! empty( $service_id ) && isset( $date ) && ! empty( $date ) ) {
				$service              = $dbhandler->get_row( 'SERVICE', $service_id );
				$time_row             = $dbhandler->get_row( 'TIME', $service_id );
				$stopsales            = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );
				$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
				$service_settings     = isset( $service->service_settings ) && ! empty( $service->service_settings ) ? maybe_unserialize( $service->service_settings ) : array();

				if ( isset( $service_settings['show_cap_left_text'] ) && $service_settings['show_cap_left_text'] == 0 ) {
					$hidden_cap_left_text = true;
				}

				if ( ! empty( $stopsales ) ) {
					$stopSalesHours   = floor( $stopsales );
					$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

					if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $date ) ) {
						$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
					} else {
						$endDateTime = clone $now;
					}

					$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
					$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
				}
			}

			if ( isset( $time_row ) && ! empty( $time_row ) && isset( $service ) && ! empty( $service ) ) {
				$total_slots         = isset( $time_row->total_slots ) ? $time_row->total_slots : 0;
				$time_slots          = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				$dates               = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();

				if ( ! empty( $variable_time_slots ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
					$index     = (int) array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];
					for ( $i = 1; $i <= $slot_data['total_slots']; $i++ ) {
						$is_slot_disabled = isset( $slot_data['disable'][ $i ] ) ? $slot_data['disable'][ $i ] : 0;

						if ( $is_slot_disabled != 1 ) {
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date, $slot_data['max_cap'][ $i ], 1 );

							$startSlot = new DateTime( $date . ' ' . $slot_data['from'][ $i ], new DateTimeZone( $timezone ) );
							$startSlot = $startSlot->format( 'Y-m-d H:i' );

							if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
								$resp .= '<div class="timeselectbox readonly_div">';
							} elseif ( ( empty( $stopsales ) && ( strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ) ) {
								$resp .= '<div class="timeselectbox readonly_div">';
							} elseif ( $capacity_left <= 0 ) {
								$resp .= '<div class="timeselectbox readonly_div">';
							} else {
								$resp .= '<div class="timeselectbox" id="slot_value">';
							}

							$resp .= '<span class="slot_value_text">';

							if ( $global_show_to_slots == 0 ) {
								if ( $slot_format == '12' ) {
									$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
								} else {
									$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
								}
							} else {
								$is_slot_hidden = isset( $slot_data['hide_to_slot'][ $i ] ) ? $slot_data['hide_to_slot'][ $i ] : 0;

								if ( $is_slot_hidden != 1 ) {
									if ( $slot_format == '12' ) {
										$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
										$resp .= ' - ';
										$resp .= isset( $slot_data['to'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['to'][ $i ] ) : '';
									} else {
										$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
										$resp .= ' - ';
										$resp .= isset( $slot_data['to'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['to'][ $i ] ) : '';
									}
								} elseif ( $is_slot_hidden == 1 ) {
									if ( $slot_format == '12' ) {
										$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
									} else {
										$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
									}
								}
							}

							$resp .= '</span>';

							if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) ) {
								if ( $capacity_left > 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $slot_data['min_cap'][ $i ] ) ? $slot_data['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $capacity_left . ' ' . $slots_left_text : $capacity_left ) . '</span>';
								} elseif ( $capacity_left <= 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $slot_data['min_cap'][ $i ] ) ? $slot_data['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $slots_full_text : 0 ) . '</span>';
								}
							} elseif ( empty( $stopsales ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
								if ( $capacity_left > 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $slot_data['min_cap'][ $i ] ) ? $slot_data['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $capacity_left . ' ' . $slots_left_text : $capacity_left ) . '</span>';
								} elseif ( $capacity_left <= 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $slot_data['min_cap'][ $i ] ) ? $slot_data['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $slots_full_text : 0 ) . '</span>';
								}
							}

							$resp .= '</div>';
						} //end if
					} //end for
				} else {
					for ( $i = 1; $i <= $total_slots; $i++ ) {
						$is_slot_disabled = isset( $time_slots['disable'][ $i ] ) ? $time_slots['disable'][ $i ] : 0;

						if ( $is_slot_disabled != 1 ) {
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $time_slots['from'][ $i ], $date, $time_slots['max_cap'][ $i ], 0 );

							$startSlot = new DateTime( $date . ' ' . $time_slots['from'][ $i ], new DateTimeZone( $timezone ) );
							$startSlot = $startSlot->format( 'Y-m-d H:i' );

							if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
								$resp .= '<div class="timeselectbox readonly_div">';
							} elseif ( ( empty( $stopsales ) && ( strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ) ) {
								$resp .= '<div class="timeselectbox readonly_div">';
							} elseif ( $capacity_left <= 0 ) {
								$resp .= '<div class="timeselectbox readonly_div">';
							} else {
								$resp .= '<div class="timeselectbox" id="slot_value">';
							}

							$resp .= '<span class="slot_value_text">';

							if ( $global_show_to_slots == 0 ) {
								if ( $slot_format == '12' ) {
									$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
								} else {
									$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
								}
							} else {
								$is_slot_hidden = isset( $time_slots['hide_to_slot'][ $i ] ) ? $time_slots['hide_to_slot'][ $i ] : 0;

								if ( $is_slot_hidden != 1 ) {
									if ( $slot_format == '12' ) {
										$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
										$resp .= ' - ';
										$resp .= isset( $time_slots['to'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['to'][ $i ] ) : '';
									} else {
										$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
										$resp .= ' - ';
										$resp .= isset( $time_slots['to'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['to'][ $i ] ) : '';
									}
								} elseif ( $is_slot_hidden == 1 ) {
									if ( $slot_format == '12' ) {
										$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
									} else {
										$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
									}
								}
							}

							$resp .= '</span>';

							if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) ) {
								if ( $capacity_left > 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $time_slots['min_cap'][ $i ] ) ? $time_slots['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $capacity_left . ' ' . $slots_left_text : $capacity_left ) . '</span>';
								} elseif ( $capacity_left <= 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $time_slots['min_cap'][ $i ] ) ? $time_slots['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $slots_full_text : 0 ) . '</span>';
								}
							} elseif ( empty( $stopsales ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
								if ( $capacity_left > 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $time_slots['min_cap'][ $i ] ) ? $time_slots['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $capacity_left . ' ' . $slots_left_text : $capacity_left ) . '</span>';
								} elseif ( $capacity_left <= 0 ) {
									$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $time_slots['min_cap'][ $i ] ) ? $time_slots['min_cap'][ $i ] : 0 ) . '">' . ( ! $hidden_cap_left_text ? $slots_full_text : 0 ) . '</span>';
								}
							}

							$resp .= '</div>';
						} //end if
					} //end for
				} //end if

				$total_extra_rows = array();
				$global_extras    = $dbhandler->get_all_result(
					'EXTRA',
					'*',
					array(
						'is_global'              => 1,
						'is_extra_service_front' => 1,
					),
					'results'
				);
				$extra_rows       = $dbhandler->get_all_result(
					'EXTRA',
					'*',
					array(
						'is_global'              => 0,
						'service_id'             => $service_id,
						'is_extra_service_front' => 1,
					),
					'results'
				);

				if ( ! empty( $extra_rows ) && ! empty( $global_extras ) ) {
					$total_extra_rows = array_merge( $global_extras, $extra_rows );
				} elseif ( empty( $extra_rows ) && ! empty( $global_extras ) ) {
					$total_extra_rows = $global_extras;
				} elseif ( ! empty( $extra_rows ) && empty( $global_extras ) ) {
					$total_extra_rows = $extra_rows;
				}

				if ( ! empty( $total_extra_rows ) ) {
					if ( $type == 'service_by_category' || $type == 'service_by_category2' ) {
						$link_class = 'get_svc_by_cat_extra_service';
					} elseif ( $type == 'home_page' ) {
						$link_class = 'get_extra_service';
					}
				} else {
					$wcmmrce_integration = $dbhandler->get_global_option_value( 'bm_enable_woocommerce_checkout', 0 );
					$only_wcmmrce        = $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout', 0 );

					if ( $wcmmrce_integration == 1 && ( new WooCommerceService() )->is_enabled() ) {
						if ( $only_wcmmrce == 1 ) {
							if ( $type == 'service_by_category' || $type == 'service_by_category2' ) {
								$link_class = 'get_svc_by_cat_checkout_form';
							} elseif ( $type == 'home_page' ) {
								$link_class = 'get_checkout_form';
							}
						} elseif ( $type == 'service_by_category' || $type == 'service_by_category2' ) {
								$link_class = 'get_svc_by_cat_checkout_options';
						} elseif ( $type == 'home_page' ) {
							$link_class = 'get_checkout_options';
						}
					} elseif ( $type == 'service_by_category' || $type == 'service_by_category2' ) {
							$link_class = 'get_svc_by_cat_checkout_form';
					} elseif ( $type == 'home_page' ) {
						$link_class = 'get_checkout_form';
					}
				}

				if ( $resp == '' ) {
					$resp .= '<p style="text-align :center;">' . $no_slot_available_text . '</p>';
				} else {
					$resp .= '<div class="service_selection_div" style="display :none;"></div>';

					$resp .= '<div class="bookbtnbar">';
					$resp .= '<div class="bookbtn readonly_div" id="select_slot_button">';
					$resp .= '<a href="#" id="' . $service_id . '" class="inactiveLink ' . $link_class . '" style="color:' . $svc_btn_txt_colour . '!important">';
					$resp .= $button_text . '</a>';
					$resp .= '</div></div>';
				}

				if ( $type == 'service_by_category' ) {
					$resp .= '</div></div>';
				}
			} //end if
		} //end if

		return $resp;
	}//end bm_fetch_service_time_slot_by_service_id()


	public function bm_fetch_service_time_slot_by_service_id_for_service_planner( $data = array() ) {
		$dbhandler   = new BM_DBhandler();
		$timezone    = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$slot_format = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$now         = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$resp        = '';

		$contrast           = $this->bm_get_theme_color( 'contrast' ) ?? '#ffffff';
		$svc_btn_txt_colour = $dbhandler->get_global_option_value( 'bm_frontend_book_button_txt_color', $contrast );

		if ( ! empty( $data ) ) {
			$service_id      = isset( $data['id'] ) ? $data['id'] : 0;
			$date            = isset( $data['date'] ) ? $data['date'] : '';
			$current_date    = $now->format( 'Y-m-d' );
			$current_time    = $now->format( 'H:i' );
			$currentDateTime = $current_date . ' ' . $current_time;

			if ( isset( $service_id ) && ! empty( $service_id ) && isset( $date ) && ! empty( $date ) ) {
				$service              = $dbhandler->get_row( 'SERVICE', $service_id );
				$time_row             = $dbhandler->get_row( 'TIME', $service_id );
				$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );

				if ( ! empty( $time_row ) && ! empty( $service ) ) {
					$total_slots         = isset( $time_row->total_slots ) ? $time_row->total_slots : 0;
					$time_slots          = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
					$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
					$dates               = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();

					if ( ! empty( $variable_time_slots ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
						$index     = (int) array_search( $date, $dates );
						$slot_data = $variable_time_slots[ $index ];
						for ( $i = 1; $i <= $slot_data['total_slots']; $i++ ) {
							$is_slot_disabled = isset( $slot_data['disable'][ $i ] ) ? $slot_data['disable'][ $i ] : 0;

							if ( $is_slot_disabled != 1 ) {
								$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date, $slot_data['max_cap'][ $i ], 1 );
								$slot_max_cap  = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date );

								$resp .= '<div class="timeselectbox" id="planner_slot_value" data-service-id="' . ( $service_id ) . '" data-timeslot-date="' . ( $date ) . '">';
								$resp .= '<span class="slot_value_text">';

								if ( $global_show_to_slots == 0 ) {
									if ( $slot_format == '12' ) {
										$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
									} else {
										$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
									}
								} else {
									$is_slot_hidden = isset( $slot_data['hide_to_slot'][ $i ] ) ? $slot_data['hide_to_slot'][ $i ] : 0;

									if ( $is_slot_hidden != 1 ) {
										if ( $slot_format == '12' ) {
											$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
											$resp .= ' - ';
											$resp .= isset( $slot_data['to'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['to'][ $i ] ) : '';
										} else {
											$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
											$resp .= ' - ';
											$resp .= isset( $slot_data['to'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['to'][ $i ] ) : '';
										}
									} elseif ( $is_slot_hidden == 1 ) {
										if ( $slot_format == '12' ) {
											$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
										} else {
											$resp .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
										}
									}
								}

								$resp .= '</span>';
								$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $slot_data['min_cap'][ $i ] ) ? $slot_data['min_cap'][ $i ] : 0 ) . '" data-maxcap="' . ( $slot_max_cap ) . '">' . ( $capacity_left . '/' . $slot_max_cap ) . '</span>';
								$resp .= '</div>';
							} //end if
						} //end for
					} else {
						for ( $i = 1; $i <= $total_slots; $i++ ) {
							$is_slot_disabled = isset( $time_slots['disable'][ $i ] ) ? $time_slots['disable'][ $i ] : 0;

							if ( $is_slot_disabled != 1 ) {
								$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $time_slots['from'][ $i ], $date, $time_slots['max_cap'][ $i ], 0 );
								$slot_max_cap  = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $i );

								$resp .= '<div class="timeselectbox" id="planner_slot_value" data-service-id="' . ( $service_id ) . '" data-timeslot-date="' . ( $date ) . '">';
								$resp .= '<span class="slot_value_text">';

								if ( $global_show_to_slots == 0 ) {
									if ( $slot_format == '12' ) {
										$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
									} else {
										$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
									}
								} else {
									$is_slot_hidden = isset( $time_slots['hide_to_slot'][ $i ] ) ? $time_slots['hide_to_slot'][ $i ] : 0;

									if ( $is_slot_hidden != 1 ) {
										if ( $slot_format == '12' ) {
											$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
											$resp .= ' - ';
											$resp .= isset( $time_slots['to'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['to'][ $i ] ) : '';
										} else {
											$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
											$resp .= ' - ';
											$resp .= isset( $time_slots['to'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['to'][ $i ] ) : '';
										}
									} elseif ( $is_slot_hidden == 1 ) {
										if ( $slot_format == '12' ) {
											$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
										} else {
											$resp .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
										}
									}
								}

								$resp .= '</span>';
								$resp .= '<span class="slot_count_text" data-capacity="' . $capacity_left . '" data-mincap="' . ( isset( $time_slots['min_cap'][ $i ] ) ? $time_slots['min_cap'][ $i ] : 0 ) . '" data-maxcap="' . ( $slot_max_cap ) . '">' . ( $capacity_left . '/' . $slot_max_cap ) . '</span>';
								$resp .= '</div>';
							} //end if
						} //end for
					} //end if
				} //end if
			}
		} //end if

		return $resp;
	}//end bm_fetch_service_time_slot_by_service_id_for_service_planner()


	/**
	 * Fetch Dynamic service time slot details by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_time_slot_array_by_service_id( $data = array() ) {
		$dbhandler = new BM_DBhandler();
		$timezone  = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now       = new DateTime( 'now', new DateTimeZone( $timezone ) );

		$available_slots = array();

		if ( ! empty( $data ) ) {
			$service_id      = $data['id'] ?? 0;
			$date            = $data['date'] ?? '';
			$currentDateTime = $now->format( 'Y-m-d H:i' );

			if ( $service_id && $date ) {
				$tables  = array( 'SERVICE', 's' );
				$joins   = array(
					array(
						'type'  => 'INNER',
						'table' => 'TIME',
						'alias' => 't',
						'on'    => 's.id = t.service_id',
					),
				);
				$where   = array(
					's.id'             => array( '=' => $service_id ),
					's.service_status' => 1,
				);
				$columns = 's.service_name, s.default_max_cap, s.variable_time_slots, t.total_slots, t.time_slots';

				$slot_data = $dbhandler->get_results_with_join(
					$tables,
					$columns,
					$joins,
					$where,
					'results',
					0,
					false,
					null,
					false
				);

				if ( ! empty( $slot_data ) ) {
					$service             = $slot_data[0];
					$time_slots          = isset( $service->time_slots ) ? maybe_unserialize( $service->time_slots ) : array();
					$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();

					$dates              = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();
					$use_variable_slots = false;

					if ( in_array( $date, $dates, true ) ) {
						$index              = array_search( $date, $dates );
						$slot_data          = $variable_time_slots[ $index ];
						$use_variable_slots = true;
					}

					$selected_slots = $use_variable_slots ? $slot_data : $time_slots;

					$valid_slots = array_filter(
						$selected_slots['from'],
						function ( $slot_time, $i ) use ( $selected_slots ) {
							return ! isset( $selected_slots['disable'][ $i ] ) || $selected_slots['disable'][ $i ] != 1;
						},
						ARRAY_FILTER_USE_BOTH
					);

					$available_slots = array_map(
						function ( $slot_from_time, $i ) use ( $service_id, $date, $selected_slots, $timezone, $currentDateTime ) {
							$slot_max_cap  = $selected_slots['max_cap'][ $i ] ?? 0;
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $slot_from_time, $date, $slot_max_cap, 0 );

							$startSlot          = new DateTime( "$date $slot_from_time", new DateTimeZone( $timezone ) );
							$startSlotFormatted = $startSlot->format( 'Y-m-d H:i' );

							if ( strtotime( $currentDateTime ) > strtotime( $startSlotFormatted ) || $capacity_left <= 0 ) {
								return null;
							}

							return $slot_from_time;
						},
						$valid_slots,
						array_keys( $valid_slots )
					);

					$available_slots = array_filter( $available_slots );
				}
			}
		}

		return $available_slots;
	}//end bm_fetch_service_time_slot_array_by_service_id()


	/**
	 * Fetch Dynamic service time slot details by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_planner_time_slot_array_by_service_id( $data = array() ) {
		$dbhandler = new BM_DBhandler();
		$timezone  = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now       = new DateTime( 'now', new DateTimeZone( $timezone ) );

		$available_slots = array();

		if ( ! empty( $data ) ) {
			$service_id      = $data['id'] ?? 0;
			$date            = $data['date'] ?? '';
			$currentDateTime = $now->format( 'Y-m-d H:i' );

			if ( $service_id && $date ) {
				$tables  = array( 'SERVICE', 's' );
				$joins   = array(
					array(
						'type'  => 'INNER',
						'table' => 'TIME',
						'alias' => 't',
						'on'    => 's.id = t.service_id',
					),
				);
				$where   = array(
					's.id'             => array( '=' => $service_id ),
					's.service_status' => 1,
				);
				$columns = 's.service_name, s.default_max_cap, s.variable_time_slots, t.total_slots, t.time_slots';

				$slot_data = $dbhandler->get_results_with_join(
					$tables,
					$columns,
					$joins,
					$where,
					'results',
					0,
					false,
					null,
					false
				);

				if ( ! empty( $slot_data ) ) {
					$service             = $slot_data[0];
					$time_slots          = isset( $service->time_slots ) ? maybe_unserialize( $service->time_slots ) : array();
					$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();

					$dates              = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();
					$use_variable_slots = false;

					if ( in_array( $date, $dates, true ) ) {
						$index              = array_search( $date, $dates );
						$slot_data          = $variable_time_slots[ $index ];
						$use_variable_slots = true;
					}

					$selected_slots = $use_variable_slots ? $slot_data : $time_slots;

					$valid_slots = array_filter(
						$selected_slots['from'],
						function ( $slot_time, $i ) use ( $selected_slots ) {
							return ! isset( $selected_slots['disable'][ $i ] ) || $selected_slots['disable'][ $i ] != 1;
						},
						ARRAY_FILTER_USE_BOTH
					);

					$available_slots = array_map(
						function ( $slot_from_time, $i ) use ( $service_id, $date, $selected_slots ) {
							$slot_max_cap  = $selected_slots['max_cap'][ $i ] ?? 0;
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $slot_from_time, $date, $slot_max_cap, 0 );

							return $slot_from_time;
						},
						$valid_slots,
						array_keys( $valid_slots )
					);

					$available_slots = array_filter( $available_slots );
				}
			}
		}

		return $available_slots;
	}//end bm_fetch_service_planner_time_slot_array_by_service_id()


	/**
	 * Fetch Dynamic service time slot, cap left and min cap details by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date( $service_id = 0, $date = '' ) {
		$dbhandler            = new BM_DBhandler();
		$timezone             = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now                  = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$slot_format          = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );

		$result = array(
			'slots'              => array(),
			'available_capacity' => array(),
			'min_capacity'       => array(),
		);

		if ( $service_id && $date ) {
			$service         = $dbhandler->get_row( 'SERVICE', $service_id );
			$time_row        = $dbhandler->get_row( 'TIME', $service_id );
			$stopsales       = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );
			$current_date    = $now->format( 'Y-m-d' );
			$current_time    = $now->format( 'H:i' );
			$currentDateTime = $current_date . ' ' . $current_time;

			if ( ! empty( $stopsales ) ) {
				$stopSalesHours   = floor( $stopsales );
				$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

				if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $date ) ) {
					$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
				} else {
					$endDateTime = clone $now;
				}

				$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
				$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
			}

			if ( $time_row && $service ) {
				$total_slots         = $time_row->total_slots ?? 0;
				$time_slots          = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				$dates               = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();

				if ( ! empty( $variable_time_slots ) && in_array( $date, $dates, true ) ) {
					$index     = array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];

					for ( $i = 1; $i <= $slot_data['total_slots']; $i++ ) {
						if ( ! isset( $slot_data['disable'][ $i ] ) || $slot_data['disable'][ $i ] != 1 ) {
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id(
								$service_id,
								$i,
								$slot_data['from'][ $i ],
								$date,
								$slot_data['max_cap'][ $i ],
								1
							);

							$slot_max_cap = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date );

							$min_cap   = $slot_data['min_cap'][ $i ] ?? 0;
							$startSlot = new DateTime( $date . ' ' . $slot_data['from'][ $i ], new DateTimeZone( $timezone ) );
							$startSlot = $startSlot->format( 'Y-m-d H:i' );

							if ( ( ! empty( $stopsales ) && strtotime( $endDateTime ) > strtotime( $startSlot ) ) ||
								( empty( $stopsales ) && strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ||
								$capacity_left <= 0 ) {
								continue;
							}

							$is_slot_hidden = $slot_data['hide_to_slot'][ $i ] ?? 0;

							if ( $global_show_to_slots == 0 ) {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $slot_data['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] );
							} elseif ( $is_slot_hidden != 1 ) {
								if ( $slot_format == '12' ) {
									$time_display = $this->bm_am_pm_format( $slot_data['from'][ $i ] ) . ' - ' . $this->bm_am_pm_format( $slot_data['to'][ $i ] );
								} else {
									$time_display = $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) . ' - ' . $this->bm_twenty_fourhrs_format( $slot_data['to'][ $i ] );
								}
							} else {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $slot_data['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] );
							}

							$result['slots'][ $i ]              = $time_display;
							$result['available_capacity'][ $i ] = $capacity_left;
							$result['min_capacity'][ $i ]       = $min_cap;
							$result['max_capacity'][ $i ]       = $slot_max_cap;
						}
					}
				} else {
					for ( $i = 1; $i <= $total_slots; $i++ ) {
						if ( ! isset( $time_slots['disable'][ $i ] ) || $time_slots['disable'][ $i ] != 1 ) {
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id(
								$service_id,
								$i,
								$time_slots['from'][ $i ],
								$date,
								$time_slots['max_cap'][ $i ],
								0
							);

							$min_cap   = $time_slots['min_cap'][ $i ] ?? 0;
							$startSlot = new DateTime( $date . ' ' . $time_slots['from'][ $i ], new DateTimeZone( $timezone ) );
							$startSlot = $startSlot->format( 'Y-m-d H:i' );

							$slot_max_cap = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $i );

							if ( ( ! empty( $stopsales ) && strtotime( $endDateTime ) > strtotime( $startSlot ) ) ||
								( empty( $stopsales ) && strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ||
								$capacity_left <= 0 ) {
								continue;
							}

							$is_slot_hidden = $time_slots['hide_to_slot'][ $i ] ?? 0;

							if ( $global_show_to_slots == 0 ) {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $time_slots['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] );
							} elseif ( $is_slot_hidden != 1 ) {
								if ( $slot_format == '12' ) {
									$time_display = $this->bm_am_pm_format( $time_slots['from'][ $i ] ) . ' - ' . $this->bm_am_pm_format( $time_slots['to'][ $i ] );
								} else {
									$time_display = $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) . ' - ' . $this->bm_twenty_fourhrs_format( $time_slots['to'][ $i ] );
								}
							} else {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $time_slots['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] );
							}

							$result['slots'][ $i ]              = $time_display;
							$result['available_capacity'][ $i ] = $capacity_left;
							$result['min_capacity'][ $i ]       = $min_cap;
							$result['max_capacity'][ $i ]       = $slot_max_cap;
						}
					}
				}
			}
		}

		return $result;
	}//end bm_fetch_service_time_slot_cap_left_min_cap_array_by_service_id_date()


	/**
	 * Fetch Dynamic service time slot, cap left and min cap details by service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_planner_time_slot_cap_left_min_cap_array_by_service_id_date( $service_id = 0, $date = '' ) {
		$dbhandler            = new BM_DBhandler();
		$timezone             = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now                  = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$slot_format          = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );

		$result = array(
			'slots'              => array(),
			'available_capacity' => array(),
			'min_capacity'       => array(),
		);

		if ( $service_id && $date ) {
			$service         = $dbhandler->get_row( 'SERVICE', $service_id );
			$time_row        = $dbhandler->get_row( 'TIME', $service_id );
			$stopsales       = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );
			$current_date    = $now->format( 'Y-m-d' );
			$current_time    = $now->format( 'H:i' );
			$currentDateTime = $current_date . ' ' . $current_time;

			if ( $time_row && $service ) {
				$total_slots         = $time_row->total_slots ?? 0;
				$time_slots          = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				$dates               = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();

				if ( ! empty( $variable_time_slots ) && in_array( $date, $dates, true ) ) {
					$index     = array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];

					for ( $i = 1; $i <= $slot_data['total_slots']; $i++ ) {
						if ( ! isset( $slot_data['disable'][ $i ] ) || $slot_data['disable'][ $i ] != 1 ) {
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id(
								$service_id,
								$i,
								$slot_data['from'][ $i ],
								$date,
								$slot_data['max_cap'][ $i ],
								1
							);

							$slot_max_cap   = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date );
							$min_cap        = $slot_data['min_cap'][ $i ] ?? 0;
							$is_slot_hidden = $slot_data['hide_to_slot'][ $i ] ?? 0;

							if ( $global_show_to_slots == 0 ) {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $slot_data['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] );
							} elseif ( $is_slot_hidden != 1 ) {
								if ( $slot_format == '12' ) {
									$time_display = $this->bm_am_pm_format( $slot_data['from'][ $i ] ) . ' - ' . $this->bm_am_pm_format( $slot_data['to'][ $i ] );
								} else {
									$time_display = $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) . ' - ' . $this->bm_twenty_fourhrs_format( $slot_data['to'][ $i ] );
								}
							} else {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $slot_data['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] );
							}

							$result['slots'][ $i ]              = $time_display;
							$result['available_capacity'][ $i ] = $capacity_left;
							$result['min_capacity'][ $i ]       = $min_cap;
							$result['max_capacity'][ $i ]       = $slot_max_cap;
						}
					}
				} else {
					for ( $i = 1; $i <= $total_slots; $i++ ) {
						if ( ! isset( $time_slots['disable'][ $i ] ) || $time_slots['disable'][ $i ] != 1 ) {
							$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id(
								$service_id,
								$i,
								$time_slots['from'][ $i ],
								$date,
								$time_slots['max_cap'][ $i ],
								0
							);

							$min_cap        = $time_slots['min_cap'][ $i ] ?? 0;
							$slot_max_cap   = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $i );
							$is_slot_hidden = $time_slots['hide_to_slot'][ $i ] ?? 0;

							if ( $global_show_to_slots == 0 ) {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $time_slots['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] );
							} elseif ( $is_slot_hidden != 1 ) {
								if ( $slot_format == '12' ) {
									$time_display = $this->bm_am_pm_format( $time_slots['from'][ $i ] ) . ' - ' . $this->bm_am_pm_format( $time_slots['to'][ $i ] );
								} else {
									$time_display = $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) . ' - ' . $this->bm_twenty_fourhrs_format( $time_slots['to'][ $i ] );
								}
							} else {
								$time_display = ( $slot_format == '12' ) ?
									$this->bm_am_pm_format( $time_slots['from'][ $i ] ) :
									$this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] );
							}

							$result['slots'][ $i ]              = $time_display;
							$result['available_capacity'][ $i ] = $capacity_left;
							$result['min_capacity'][ $i ]       = $min_cap;
							$result['max_capacity'][ $i ]       = $slot_max_cap;
						}
					}
				}
			}
		}

		return $result;
	}//end bm_fetch_service_planner_time_slot_cap_left_min_cap_array_by_service_id_date()


	/**
	 * Fetch Service calendar html for service by category shortcode
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_calendar_html( $date, $resp = '' ) {
		if ( ! empty( $date ) ) {
			$available_text     = __( 'Available', 'service-booking' );
			$unavailable_text   = __( 'Unavailable', 'service-booking' );
			$selected_date_text = __( 'Selected: ', 'service-booking' );
			$resp              .= '<div class="calender-modal">';
			$resp              .= '<div class="booking-status">';
			$resp              .= '<div class="booking-statusinnerbox">';
			$resp              .= '<div class="status-box" style="margin-right:10px;">';
			$resp              .= '<div class="available_for_booking"></div>';
			$resp              .= '<span>' . $available_text . '</span>';
			$resp              .= '</div>';
			$resp              .= '<div class="status-box">';
			$resp              .= '<div class="not_available_for_booking"></div>';
			$resp              .= '<span>' . $unavailable_text . '</span>';
			$resp              .= '</div>';
			$resp              .= '</div>';
			$resp              .= '<div class="selected-date-box">';
			$resp              .= '<span>' . $selected_date_text;
			$resp              .= '<span class="selected_date_div">' . $this->bm_day_date_month_year_format( $date ) . '</span></span>';
			$resp              .= '</div>';
			$resp              .= '</div>';
			$resp              .= '<div class="modal-calender-box">';
			$resp              .= '<div class="calender-box">';
			$resp              .= '<div class="service_by_category_calendar"></div>';
			$resp              .= '</div>';
			$resp              .= '<div class="modalcontentbox modal-body">';
		} //end if

		return $resp;
	}//end bm_fetch_service_calendar_html()


	/**
	 * Fetch time slot by service id for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_to_time_slot_by_service_id( $service_id = '', $from = '' ) {
		$dbhandler = new BM_DBhandler();
		$to        = '00:00';

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );
		}

		if ( ! empty( $from ) && isset( $time_row ) && ! empty( $time_row ) ) {
			$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

			if ( ! empty( $time_slots ) && isset( $time_slots['from'] ) && in_array( $from, $time_slots['from'], true ) ) {
				$index = (int) array_search( $from, $time_slots['from'] );
				$to    = isset( $time_slots['to'] ) ? $time_slots['to'][ $index ] : '';
			}
		}

		return $to;
	}//end bm_fetch_non_variable_to_time_slot_by_service_id()


	/**
	 * Fetch time slot by service id for variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_variable_to_time_slot_by_service_id( $service_id = '', $from = '', $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$to        = '00:00';

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );
		}

		if ( ! empty( $date ) && ! empty( $from ) && isset( $service ) && ! empty( $service ) ) {
			$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			if ( isset( $variable_time_slots ) && ! empty( $variable_time_slots ) ) {
				$dates = wp_list_pluck( $variable_time_slots, 'date' );
			}

			if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
				$index      = (int) array_search( $date, $dates );
				$slot_data  = $variable_time_slots[ $index ];
				$from_slots = isset( $slot_data['from'] ) ? $slot_data['from'] : array();

				if ( ! empty( $slot_data ) && ! empty( $from_slots ) && in_array( $from, $from_slots, true ) ) {
					$index = (int) array_search( $from, $from_slots );
					$to    = $slot_data['to'][ $index ];
				}
			}
		}

		return $to;
	}//end bm_fetch_variable_to_time_slot_by_service_id()


	/**
	 * Fetch bokking details response after successful booking
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_details_by_booking_id( $booking_id = '', $resp = '' ) {
		$dbhandler        = new BM_DBhandler();
		$booking_currency = $this->bm_get_currency_symbol( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );
		$slot_format      = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		if ( isset( $booking_id ) && ! empty( $booking_id ) ) {
			$booking = $dbhandler->get_row( 'BOOKING', $booking_id, 'id' );
		}

		$booking_successfull = __( 'Booking Successfull !!', 'service-booking' );

		if ( isset( $booking ) && ! empty( $booking ) ) {
			$total_cost    = isset( $booking->total_cost ) ? $booking->total_cost : '';
			$booking_slots = isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();

			if ( ! empty( $booking_slots ) ) {
				$resp .= '<h4 style="text-align:center;">' . $booking_successfull . '</h4>';
				$resp .= '<div class="detailsbutton">';
				$resp .= '<label for="fname" class="boldfont">' . __( 'Bookind ID:', 'service-booking' ) . '</label>';
				$resp .= '<span class="bookingtext">' . $booking_id . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="detailsbutton">';
				$resp .= '<label for="fname" class="boldfont">' . __( 'Service Name:', 'service-booking' ) . '</label>';
				$resp .= '<span class="bookingtext">' . ( isset( $booking->service_name ) ? $booking->service_name : '' ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="detailsbutton">';
				$resp .= '<label for="fname" class="boldfont">' . __( 'Date:', 'service-booking' ) . '</label>';
				$resp .= '<span class="bookingtext">' . ( isset( $booking->booking_date ) ? $this->bm_month_year_date_format( $booking->booking_date ) : '' ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="detailsbutton">';
				$resp .= '<label for="fname" class="boldfont">' . __( 'From:', 'service-booking' ) . '</label>';

				if ( $slot_format == '12' ) {
					$resp .= '<span class="bookingtext">' . ( isset( $booking_slots['from'] ) ? $this->bm_am_pm_format( $booking_slots['from'] ) : '' ) . '</span>';
				} else {
					$resp .= '<span class="bookingtext">' . ( isset( $booking_slots['from'] ) ? $this->bm_twenty_fourhrs_format( $booking_slots['from'] ) : '' ) . '</span>';
				}

				$resp .= '</div>';
				$resp .= '<div class="detailsbutton">';
				$resp .= '<label for="fname" class="boldfont">' . __( 'To:', 'service-booking' ) . '</label>';

				if ( $slot_format == '12' ) {
					$resp .= '<span class="bookingtext">' . ( isset( $booking_slots['to'] ) ? $this->bm_am_pm_format( $booking_slots['to'] ) : '' ) . '</span>';
				} else {
					$resp .= '<span class="bookingtext">' . ( isset( $booking_slots['to'] ) ? $this->bm_twenty_fourhrs_format( $booking_slots['to'] ) : '' ) . '</span>';
				}

				$resp .= '</div>';
				$resp .= '<div class="detailsbutton">';
				$resp .= '<label for="fname" class="boldfont">' . __( 'Total Cost:', 'service-booking' ) . '</label>';
				$resp .= '<span class="bookingtext">' . ( $this->bm_add_price_character( $total_cost ) ) . '</span>';
				$resp .= '</div>';
				$resp .= '<div class="formbottombuttonbar">';
				$resp .= '<div class="formbuttoninnerbox">';
				$resp .= '<div class="closebtn" id="close_booking_details">' . __( 'Close', 'service-booking' ) . '</div>';
				$resp .= '</div></div>';
			} //end if
		} //end if

		return $resp;
	}//end bm_fetch_booking_details_by_booking_id()


	/**
	 * Check if a slot is variable slot
	 *
	 * @author Darpan
	 */
	public function bm_check_if_variable_slot_by_service_id_and_date( $service_id = '', $date = '' ) {
		$dbhandler        = new BM_DBhandler();
		$is_variable_slot = 0;

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );
		}

		if ( ! empty( $date ) && isset( $service ) && ! empty( $service ) ) {
			$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			if ( isset( $variable_time_slots ) && ! empty( $variable_time_slots ) ) {
				$dates = wp_list_pluck( $variable_time_slots, 'date' );
			}

			if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
				$is_variable_slot = 1;
			}
		}

		return $is_variable_slot;
	}//end bm_check_if_variable_slot_by_service_id_and_date()


	/**
	 * Check if a service is book on request only
	 *
	 * @author Darpan
	 */
	public function bm_check_if_book_on_request_only( $service_id = 0 ) {
		$dbhandler       = new BM_DBhandler();
		$book_on_request = 0;

		if ( ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$book_on_request = isset( $service->is_only_book_on_request ) ? $service->is_only_book_on_request : 0;
			}
		}

		return $book_on_request;
	}//end bm_check_if_book_on_request_only()


	/**
	 * Fetch slot id by Service id for variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_variable_slot_id_by_service_id_and_date( $service_id = '', $from = '', $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$slot_id   = 0;

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );
		}

		if ( ! empty( $from ) && ! empty( $date ) && isset( $service ) && ! empty( $service ) ) {
			$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			if ( isset( $variable_time_slots ) && ! empty( $variable_time_slots ) ) {
				$dates = wp_list_pluck( $variable_time_slots, 'date' );
			}

			if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
				$index     = (int) array_search( $date, $dates );
				$slot_data = $variable_time_slots[ $index ];

				if ( ! empty( $slot_data ) && isset( $slot_data['from'] ) && in_array( $from, $slot_data['from'], true ) ) {
					$slot_id = (int) array_search( $from, $slot_data['from'] );
				}
			}
		}

		return $slot_id;
	}//end bm_fetch_variable_slot_id_by_service_id_and_date()


	/**
	 * Fetch slot id by Service id for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_slot_id_by_service_id( $service_id = 0, $from = '' ) {
		$dbhandler = new BM_DBhandler();
		$slot_id   = 0;

		if ( $service_id > 0 && ! empty( $from ) ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );

			if ( ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) && isset( $time_slots['from'] ) && in_array( $from, $time_slots['from'], true ) ) {
					$slot_id = (int) array_search( $from, $time_slots['from'] );
				}
			}
		}

		return $slot_id;
	}//end bm_fetch_non_variable_slot_id_by_service_id()


	/**
	 * Fetch slot capacity by Service and slot id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id = 0, $slot_id = '', $from = '', $date = '', $slot_max_cap = 0, $is_variable_slot = 0 ) {
		$dbhandler     = new BM_DBhandler();
		$capacity_left = 0;

		if ( $service_id > 0 && ! empty( $slot_id ) && $slot_max_cap > 0 && ! empty( $date ) ) {
			$max_capacity = $dbhandler->get_all_result(
				'SLOTCOUNT',
				'slot_max_cap',
				array(
					'service_id'   => $service_id,
					'booking_date' => $date,
					'slot_id'      => $slot_id,
					'is_variable'  => $is_variable_slot,
					'is_active'    => 1,
				),
				'var',
				0,
				1,
				'id',
				'DESC'
			);

			if ( ! empty( $max_capacity ) && $slot_max_cap == $max_capacity ) {
				$capacity_left = $dbhandler->get_all_result(
					'SLOTCOUNT',
					'slot_cap_left',
					array(
						'service_id'   => $service_id,
						'booking_date' => $date,
						'slot_id'      => $slot_id,
						'is_variable'  => $is_variable_slot,
						'is_active'    => 1,
					),
					'var',
					0,
					1,
					'id',
					'DESC'
				);
			} elseif ( $is_variable_slot == 1 ) {
				if ( isset( $from ) && ! empty( $from ) ) {
					$capacity_left = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id, $from, $date );
				}
			} else {
				$capacity_left = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id );
			}
		}

		return $capacity_left;
	}//end bm_fetch_available_slot_capacity_by_service_and_slot_id()


	/**
	 * Fetch slot id by Service id for variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id = 0, $slot_id = 0, $from = '', $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$max_cap   = 0;

		if ( $service_id > 0 && ! empty( $slot_id ) && ! empty( $date ) && ! empty( $from ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();

				if ( ! empty( $variable_time_slots ) ) {
					$dates = wp_list_pluck( $variable_time_slots, 'date' );

					if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
						$index     = (int) array_search( $date, $dates );
						$slot_data = $variable_time_slots[ $index ];

						if ( ! empty( $slot_data ) && isset( $slot_data['from'] ) && in_array( $from, $slot_data['from'], true ) ) {
							$index   = (int) array_search( $from, $slot_data['from'] );
							$max_cap = isset( $slot_data['max_cap'] ) ? $slot_data['max_cap'][ $index ] : 0;
						}
					}
				}
			}
		}

		return $max_cap;
	}//end bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id()


	/**
	 * Fetch slot id by Service id for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id = 0, $slot_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$max_cap   = 0;

		if ( $service_id > 0 && ! empty( $slot_id ) ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );

			if ( ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) && isset( $time_slots['max_cap'] ) ) {
					$max_cap = $time_slots['max_cap'][ $slot_id ];
				}
			}
		}

		return $max_cap;
	}//end bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id()


	/**
	 * Fetch slot id by Service id for variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_variable_slot_min_cap_by_service_id_and_slot_id( $service_id = '', $slot_id = '', $from = '', $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$min_cap   = 0;

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );
		}

		if ( ! empty( $slot_id ) && ! empty( $date ) && ! empty( $from ) && isset( $service ) && ! empty( $service ) ) {
			$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			if ( isset( $variable_time_slots ) && ! empty( $variable_time_slots ) ) {
				$dates = wp_list_pluck( $variable_time_slots, 'date' );
			}

			if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
				$index     = (int) array_search( $date, $dates );
				$slot_data = $variable_time_slots[ $index ];

				if ( ! empty( $slot_data ) && isset( $slot_data['from'] ) && in_array( $from, $slot_data['from'], true ) ) {
					$index   = (int) array_search( $from, $slot_data['from'] );
					$min_cap = isset( $slot_data['min_cap'] ) ? $slot_data['min_cap'][ $index ] : 0;
				}
			}
		}

		return $min_cap;
	}//end bm_fetch_variable_slot_min_cap_by_service_id_and_slot_id()


	/**
	 * Fetch from slot by Service id and slot id for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_from_slot_by_service_id_and_slot_id( $service_id = '', $slot_id = '' ) {
		$dbhandler = new BM_DBhandler();
		$from      = '00:00';

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );
		}

		if ( ! empty( $slot_id ) && isset( $time_row ) && ! empty( $time_row ) ) {
			$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

			if ( ! empty( $time_slots ) && isset( $time_slots['from'] ) ) {
				$from = $time_slots['from'][ $slot_id ];
			}
		}

		return $from;
	}//end bm_fetch_non_variable_from_slot_by_service_id_and_slot_id()


	/**
	 * Fetch first from slot for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_first_from_slot( $service_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$from      = '00:00';

		if ( ! empty( $service_id ) ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );

			if ( ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) && isset( $time_slots['from'] ) ) {
					$from = isset( $time_slots['from'][1] ) ? $time_slots['from'][1] : '00:00';
				}
			}
		}

		return $from;
	}//end bm_fetch_non_variable_first_from_slot()


	/**
	 * Fetch first from slot for variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_variable_first_from_slot( $service_id = 0, $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$from      = '00:00';

		if ( ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) && ! empty( $date ) ) {
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				if ( ! empty( $variable_time_slots ) ) {
					$dates = wp_list_pluck( $variable_time_slots, 'date' );

					if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
						$index     = (int) array_search( $date, $dates );
						$slot_data = $variable_time_slots[ $index ];

						if ( ! empty( $slot_data ) && isset( $slot_data['from'] ) ) {
							$from = isset( $slot_data['from'][1] ) ? $slot_data['from'][1] : '00:00';
						}
					}
				}
			}
		}

		return $from;
	}//end bm_fetch_variable_first_from_slot()


	/**
	 * Fetch slot id by Service id for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_slot_min_cap_by_service_id_and_slot_id( $service_id = 0, $slot_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$min_cap   = 0;

		if ( $service_id > 0 && ! empty( $slot_id ) ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );

			if ( ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) && isset( $time_slots['min_cap'] ) ) {
					$min_cap = $time_slots['min_cap'][ $slot_id ];
				}
			}
		}

		return $min_cap;
	}//end bm_fetch_non_variable_slot_min_cap_by_service_id_and_slot_id()


	/**
	 * Fetch slot capacity by Service and slot id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_variable_total_cap_of_service_by_service_id( $service_id = 0 ) {
		$dbhandler      = new BM_DBhandler();
		$total_capacity = 0;

		if ( $service_id > 0 ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );

			if ( ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) ) {
					$max_cap = isset( $time_slots['max_cap'] ) ? $time_slots['max_cap'] : 0;

					if ( ! empty( $max_cap ) ) {
						$total_capacity = array_sum( $max_cap );
					}
				}
			}
		}

		return $total_capacity;
	}//end bm_fetch_non_variable_total_cap_of_service_by_service_id()


	/**
	 * Fetch slot capacity by Service and slot id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_variable_total_cap_of_service_by_service_id_and_date( $service_id = '', $date = '' ) {
		$dbhandler      = new BM_DBhandler();
		$total_capacity = 0;

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );
		}

		if ( ! empty( $date ) && isset( $service ) && ! empty( $service ) ) {
			$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
			if ( isset( $variable_time_slots ) && ! empty( $variable_time_slots ) ) {
				$dates = wp_list_pluck( $variable_time_slots, 'date' );
			}

			if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
				$index     = (int) array_search( $date, $dates );
				$slot_data = $variable_time_slots[ $index ];
				$max_cap   = isset( $slot_data['max_cap'] ) ? $slot_data['max_cap'] : 0;

				if ( ! empty( $max_cap ) ) {
					$total_capacity = array_sum( $max_cap );
				}
			}
		}

		return $total_capacity;
	}//end bm_fetch_variable_total_cap_of_service_by_service_id_and_date()


	/**
	 * Fetch slot id by Service id for non variable slots
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_time_slots_by_service_id( $service_id = 0 ) {
		$dbhandler   = new BM_DBhandler();
		$total_slots = 0;

		if ( $service_id > 0 ) {
			$time_row = $dbhandler->get_row( 'TIME', $service_id );

			if ( ! empty( $time_row ) && isset( $time_row->total_slots ) ) {
				$total_slots = $time_row->total_slots;
			}
		}

		return $total_slots;
	}//end bm_fetch_total_time_slots_by_service_id()


	/**
	 * Fetch total booked slots by service id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_booked_slots_by_service_id( $service_id = 0, $date = '', $slot_id = 0, $is_variable_slot = 0 ) {
		$dbhandler          = new BM_DBhandler();
		$total_booked_slots = 0;

		if ( $service_id > 0 && ! empty( $date ) && ! empty( $slot_id ) ) {
			$slot_ids = $dbhandler->get_all_result(
				'SLOTCOUNT',
				'slot_id',
				array(
					'service_id'   => $service_id,
					'booking_date' => $date,
					'is_variable'  => $is_variable_slot,
					'is_active'    => 1,
				),
				'results'
			);

			if ( ! empty( $slot_ids ) ) {
				$total_booked = array();
				$slot_ids     = array_column( $slot_ids, 'slot_id' );
				$slot_ids     = array_merge( $slot_ids + array( $slot_id ) );
				$slot_ids     = array_values( array_unique( $slot_ids ) );

				if ( ! empty( $slot_ids ) && is_array( $slot_ids ) ) {
					foreach ( $slot_ids as $id ) {
						$total_booked[] = $dbhandler->get_all_result(
							'SLOTCOUNT',
							'slot_total_booked',
							array(
								'service_id'   => $service_id,
								'booking_date' => $date,
								'slot_id'      => $id,
								'is_variable'  => $is_variable_slot,
								'is_active'    => 1,
							),
							'var',
							0,
							1,
							'id',
							'DESC',
						);
					}
				}

				if ( ! empty( $total_booked ) ) {
					$total_booked_slots = array_sum( $total_booked );
				}
			}
		}

		return $total_booked_slots;
	}//end bm_fetch_total_booked_slots_by_service_id()


	/**
	 * Fetch capacity of service with single time slot by Service and slot id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id( $service_id = '', $slot_id = '', $from = '', $date = '', $slot_max_cap = '', $is_variable_slot = 0 ) {
		$dbhandler     = new BM_DBhandler();
		$capacity_left = 0;

		if ( isset( $service_id ) && ! empty( $service_id ) && isset( $slot_id ) && ! empty( $slot_id ) && isset( $slot_max_cap ) && ! empty( $slot_max_cap ) && isset( $date ) && ! empty( $date ) ) {
			$max_capacity = $dbhandler->get_all_result(
				'SLOTCOUNT',
				'slot_max_cap',
				array(
					'service_id'   => $service_id,
					'booking_date' => $date,
					'slot_id'      => $slot_id,
					'is_variable'  => $is_variable_slot,
					'is_active'    => 1,
				),
				'var',
				0,
				1,
				'id',
				'DESC'
			);

			if ( ! empty( $max_capacity ) && $slot_max_cap == $max_capacity ) {
				$capacity_left = $dbhandler->get_all_result(
					'SLOTCOUNT',
					'slot_cap_left',
					array(
						'service_id'   => $service_id,
						'booking_date' => $date,
						'slot_id'      => $slot_id,
						'is_variable'  => $is_variable_slot,
						'is_active'    => 1,
					),
					'var',
					0,
					1,
					'id',
					'DESC'
				);
			} else {
				$capacity_left = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );

				if ( $capacity_left == 0 ) {
					if ( $is_variable_slot == 1 ) {
						if ( isset( $from ) && ! empty( $from ) ) {
							$capacity_left = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id, $from, $date );
						}
					} else {
						$capacity_left = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id );
					}
				}
			}
		}

		return $capacity_left;
	}//end bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id()


	/**
	 * Fetch date specific max cap for variable max cap calendar
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id = 0, $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$max_cap   = 0;

		if ( $service_id > 0 && ! empty( $date ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );

			if ( ! empty( $service ) ) {
				$variable_max_cap = isset( $service->variable_max_cap ) ? maybe_unserialize( $service->variable_max_cap ) : array();

				if ( ! empty( $variable_max_cap ) ) {
					$dates = isset( $variable_max_cap['date'] ) ? $variable_max_cap['date'] : array();

					if ( ! empty( $dates ) && in_array( $date, $dates, true ) ) {
						$index   = (int) array_search( $date, $dates );
						$max_cap = isset( $variable_max_cap['capacity'] ) ? $variable_max_cap['capacity'][ $index ] : 0;
					}
				}
			}
		}

		return $max_cap;
	}//end bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date()


	/**
	 * Fetch slots of service with single time slot by Service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_single_time_slot_by_service_id( $service_id = 0, $date = '' ) {
		$dbhandler        = new BM_DBhandler();
		$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$slot_format      = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$time_slot        = '';
		$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date     = $now->format( 'Y-m-d' );
		$current_time     = $now->format( 'H:i' );
		$currentDateTime  = $now->format( 'Y-m-d H:i' );
		$single_slot_text = __( 'This is a single slot service', 'service-booking' );
		$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );

		if ( $service_id > 0 ) {
			$service   = $dbhandler->get_row( 'SERVICE', $service_id );
			$time_row  = $dbhandler->get_row( 'TIME', $service_id );
			$stopsales = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );

			if ( ! empty( $stopsales ) ) {
				$stopSalesHours   = floor( $stopsales );
				$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

				if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $date ) ) {
					$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
				} else {
					$endDateTime = clone $now;
				}

				$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
				$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
			}

			$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
		}

		if ( $is_variable_slot == 1 ) {
			if ( ! empty( $date ) && ! empty( $service ) ) {
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				$dates               = wp_list_pluck( $variable_time_slots, 'date' );

				if ( isset( $variable_time_slots ) && ! empty( $variable_time_slots ) && in_array( $date, $dates, true ) ) {
					$index     = (int) array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];
					if ( isset( $slot_data['disable'][1] ) && $slot_data['disable'][1] != 1 ) {
						$current_slot_max_cap = isset( $slot_data['max_cap'][1] ) ? $slot_data['max_cap'][1] : 0;
						$slot_max_cap         = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );
						$slot_max_cap         = $slot_max_cap == 0 ? $current_slot_max_cap : $slot_max_cap;

						$capacity_left = $this->bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id( $service_id, 1, $slot_data['from'][1], $date, $slot_max_cap, 1 );

						$startSlot = new DateTime( $date . ' ' . $slot_data['from'][1], new DateTimeZone( $timezone ) );
						$startSlot = $startSlot->format( 'Y-m-d H:i' );

						if ( empty( $stopsales ) && isset( $slot_data['from'][1] ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
							if ( $capacity_left > 0 ) {
								if ( $global_show_to_slots == 0 ) {
									if ( $slot_format == '12' ) {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= $this->bm_am_pm_format( $slot_data['from'][1] );
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									} else {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= $this->bm_twenty_fourhrs_format( $slot_data['from'][1] );
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] != 1 ) {
										$time_slot .= '<span class="single_slot_timings">';

									if ( $slot_format == '12' ) {
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_am_pm_format( $slot_data['from'][1] ) : '';
										$time_slot .= ' - ';
										$time_slot .= isset( $slot_data['to'][1] ) ? $this->bm_am_pm_format( $slot_data['to'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									} else {
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) : '';
										$time_slot .= ' - ';
										$time_slot .= isset( $slot_data['to'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['to'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] == 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_am_pm_format( $slot_data['from'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									} else {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									}
								} //end if
							} elseif ( $capacity_left <= 0 ) {
								$time_slot .= '0';
							} //end if
						} elseif ( empty( $stopsales ) && isset( $slot_data['from'][1] ) && strtotime( $currentDateTime ) > strtotime( $startSlot ) && ( $capacity_left > 0 ) ) {
							$time_slot .= '-1';
						} elseif ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) {
							if ( $capacity_left > 0 ) {
								if ( $global_show_to_slots == 0 ) {
									if ( $slot_format == '12' ) {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_am_pm_format( $slot_data['from'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									} else {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] != 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_am_pm_format( $slot_data['from'][1] ) : '';
										$time_slot .= ' - ';
										$time_slot .= isset( $slot_data['to'][1] ) ? $this->bm_am_pm_format( $slot_data['to'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									} else {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) : '';
										$time_slot .= ' - ';
										$time_slot .= isset( $slot_data['to'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['to'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] == 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_am_pm_format( $slot_data['from'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									} else {
										$time_slot .= '<span class="single_slot_timings">';
										$time_slot .= isset( $slot_data['from'][1] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) : '';
										$time_slot .= '</span>';
										$time_slot .= '<span class="single_slot_info">';
										$time_slot .= '(' . $single_slot_text . ')';
										$time_slot .= '</span>';
									}
								} //end if
							} elseif ( $capacity_left <= 0 ) {
								$time_slot .= '0';
							} //end if
						} elseif ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
							$time_slot .= '-1';
						} //end if
					} //end if
				} //end if
			} //end if
		} elseif ( $is_variable_slot == 0 ) {
			if ( ! empty( $date ) && ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) && isset( $time_slots['disable'][1] ) && $time_slots['disable'][1] != 1 ) {
					$current_slot_max_cap = isset( $time_slots['max_cap'][1] ) ? $time_slots['max_cap'][1] : 0;
					$slot_max_cap         = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );
					$slot_max_cap         = $slot_max_cap == 0 ? $current_slot_max_cap : $slot_max_cap;

					$capacity_left = $this->bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id( $service_id, 1, $time_slots['from'][1], $date, $slot_max_cap, 0 );

					$startSlot = new DateTime( $date . ' ' . $time_slots['from'][1], new DateTimeZone( $timezone ) );
					$startSlot = $startSlot->format( 'Y-m-d H:i' );

					if ( empty( $stopsales ) && isset( $time_slots['from'][1] ) && strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) {
						if ( $capacity_left > 0 ) {
							if ( $global_show_to_slots == 0 ) {
								if ( $slot_format == '12' ) {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_am_pm_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								} else {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] != 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_am_pm_format( $time_slots['from'][1] ) : '';
									$time_slot .= ' - ';
									$time_slot .= isset( $time_slots['to'][1] ) ? $this->bm_am_pm_format( $time_slots['to'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								} else {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) : '';
									$time_slot .= ' - ';
									$time_slot .= isset( $time_slots['to'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['to'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] == 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_am_pm_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								} else {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								}
							} //end if
						} elseif ( $capacity_left <= 0 ) {
							$time_slot .= '0';
						} //end if
					} elseif ( empty( $stopsales ) && isset( $time_slots['from'][1] ) && strtotime( $currentDateTime ) > strtotime( $startSlot ) && ( $capacity_left > 0 ) ) {
						$time_slot .= '-1';
					} elseif ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) {
						if ( $capacity_left > 0 ) {
							if ( $global_show_to_slots == 0 ) {
								if ( $slot_format == '12' ) {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_am_pm_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								} else {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] != 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_am_pm_format( $time_slots['from'][1] ) : '';
									$time_slot .= ' - ';
									$time_slot .= isset( $time_slots['to'][1] ) ? $this->bm_am_pm_format( $time_slots['to'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								} else {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) : '';
									$time_slot .= ' - ';
									$time_slot .= isset( $time_slots['to'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['to'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] == 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_am_pm_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								} else {
									$time_slot .= '<span class="single_slot_timings">';
									$time_slot .= isset( $time_slots['from'][1] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) : '';
									$time_slot .= '</span>';
									$time_slot .= '<span class="single_slot_info">';
									$time_slot .= '(' . $single_slot_text . ')';
									$time_slot .= '</span>';
								}
							} //end if
						} elseif ( $capacity_left <= 0 ) {
							$time_slot .= '0';
						} //end if
					} elseif ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
						$time_slot .= '-1';
					} //end if
				} //end if
			} //end if
		} //end if

		return $time_slot;
	}//end bm_fetch_single_time_slot_by_service_id()


	/**
	 * Fetch date specific max cap for variable max cap calendar
	 *
	 * @author Darpan
	 */
	public function bm_fetch_extra_service_max_cap_by_extra_service_id( $extra_service_id = '' ) {
		$dbhandler = new BM_DBhandler();
		$max_cap   = 0;

		if ( isset( $extra_service_id ) && ! empty( $extra_service_id ) ) {
			$extra_service = $dbhandler->get_row( 'EXTRA', $extra_service_id, 'id' );
		}

		if ( isset( $extra_service ) && ! empty( $extra_service ) ) {
			$max_cap = isset( $extra_service->extra_max_cap ) ? $extra_service->extra_max_cap : 0;
		}

		return $max_cap;
	}//end bm_fetch_extra_service_max_cap_by_extra_service_id()


	/**
	 * Fetch cap left for variable max cap calendar
	 *
	 * @author Darpan
	 */
	public function bm_fetch_extra_service_cap_left_by_extra_service_id_and_date( $extra_service_id = 0, $extra_max_cap = 0, $current_slots_booked = 0, $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$cap_left  = 0;

		if ( ! empty( $extra_service_id ) && ! empty( $extra_max_cap ) && ! empty( $date ) ) {
			$max_capacity = $dbhandler->get_all_result(
				'EXTRASLOTCOUNT',
				'max_cap',
				array(
					'extra_svc_id' => $extra_service_id,
					'booking_date' => $date,
					'is_active'    => 1,
				),
				'var',
				0,
				1,
				'id',
				'DESC'
			);
			if ( ! empty( $max_capacity ) && $extra_max_cap == $max_capacity ) {
				$last_cap_left      = $dbhandler->get_all_result(
					'EXTRASLOTCOUNT',
					'cap_left',
					array(
						'extra_svc_id' => $extra_service_id,
						'booking_date' => $date,
						'is_active'    => 1,
					),
					'var',
					0,
					1,
					'id',
					'DESC'
				);
				$slots_booked       = $dbhandler->get_all_result(
					'EXTRASLOTCOUNT',
					'slots_booked',
					array(
						'extra_svc_id' => $extra_service_id,
						'booking_date' => $date,
						'is_active'    => 1,
					),
					'results'
				);
				$total_slots_booked = array_sum( array_column( $slots_booked, 'slots_booked' ) );
				if ( isset( $last_cap_left ) && ! empty( $last_cap_left ) ) {
					$cap_left = ( $extra_max_cap - ( $total_slots_booked + $current_slots_booked ) );
				}
			} else {
				$cap_left = ( intval( $extra_max_cap ) - intval( $current_slots_booked ) );
			}
		}

		return $cap_left;
	}//end bm_fetch_extra_service_cap_left_by_extra_service_id_and_date()


	/**
	 * Fetch service time slot details
	 *
	 * @author Darpan
	 *
	 * @param int    $service_id
	 * @param string $from
	 * @param string $date
	 * @param int    $svc_total_time_slots
	 * @param int    $current_service_booked
	 * @param int    $is_variable_slot
	 * @param array  $return                 optional
	 *
	 * @return array of $slot_id, $slot_max_cap, $slot_min_cap, $total_capacity, $total_booked_before_current_booking, $total_booked_after_current_booking, $capacity_left, $slot_capacity_left_after_booking, $slot_total_booked, $svc_total_cap_left_after_booking
	 */
	public function bm_fetch_slot_details( $service_id = 0, $from = '00:00', $date = null, $svc_total_time_slots = 0, $current_service_booked = 0, $is_variable_slot = 0, $return = array() ) {
		$data = array();

		if ( ! empty( $date ) ) {
			if ( $is_variable_slot == 1 ) {
				$slot_id = $this->bm_fetch_variable_slot_id_by_service_id_and_date( $service_id, $from, $date );

				if ( $svc_total_time_slots == 1 ) {
					$slot_max_cap = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );
					if ( $slot_max_cap == 0 ) {
						$slot_max_cap = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id, $from, $date );
					}
				} else {
					$slot_max_cap = $this->bm_fetch_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id, $from, $date );
				}

				$slot_min_cap = $this->bm_fetch_variable_slot_min_cap_by_service_id_and_slot_id( $service_id, $slot_id, $from, $date );

				if ( $svc_total_time_slots == 1 ) {
					$total_capacity = $slot_max_cap;
				} else {
					$total_capacity = $this->bm_fetch_variable_total_cap_of_service_by_service_id_and_date( $service_id, $date );
				}
			} elseif ( $is_variable_slot == 0 ) {
				$slot_id = $this->bm_fetch_non_variable_slot_id_by_service_id( $service_id, $from );

				if ( $svc_total_time_slots == 1 ) {
					$slot_max_cap = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );
					if ( $slot_max_cap == 0 ) {
						$slot_max_cap = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id );
					}
				} else {
					$slot_max_cap = $this->bm_fetch_non_variable_slot_max_cap_by_service_id_and_slot_id( $service_id, $slot_id );
				}

				$slot_min_cap = $this->bm_fetch_non_variable_slot_min_cap_by_service_id_and_slot_id( $service_id, $slot_id );

				if ( $svc_total_time_slots == 1 ) {
					$total_capacity = $slot_max_cap;
				} else {
					$total_capacity = $this->bm_fetch_non_variable_total_cap_of_service_by_service_id( $service_id );
				}
			} //end if

			$svc_total_booked_slots_before_booking = $this->bm_fetch_total_booked_slots_by_service_id( $service_id, $date, $slot_id, $is_variable_slot );
			$svc_total_booked_slots_after_booking  = ( $svc_total_booked_slots_before_booking + $current_service_booked );

			if ( $svc_total_time_slots == 1 ) {
				$capacity_left = $this->bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id( $service_id, $slot_id, $from, $date, $slot_max_cap, $is_variable_slot );
			} else {
				$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $slot_id, $from, $date, $slot_max_cap, $is_variable_slot );
			}

			$slot_capacity_left_after_booking = ( intval( $capacity_left ) - intval( $current_service_booked ) );
			$slot_total_booked                = ( intval( $slot_max_cap ) - intval( $slot_capacity_left_after_booking ) );
			$svc_total_cap_left_after_booking = ( intval( $total_capacity ) - intval( $svc_total_booked_slots_after_booking ) );

			$data['slot_id']                             = $slot_id;
			$data['slot_max_cap']                        = $slot_max_cap;
			$data['slot_min_cap']                        = $slot_min_cap;
			$data['total_capacity']                      = $total_capacity;
			$data['total_booked_before_current_booking'] = $svc_total_booked_slots_before_booking;
			$data['total_booked_after_current_booking']  = $svc_total_booked_slots_after_booking;
			$data['capacity_left']                       = $capacity_left;
			$data['slot_capacity_left_after_booking']    = $slot_capacity_left_after_booking;
			$data['slot_total_booked']                   = $slot_total_booked;
			$data['svc_total_cap_left_after_booking']    = $svc_total_cap_left_after_booking;
		} //end if

		if ( is_array( $return ) && ! empty( $return ) ) {
			$filtered = array();
			foreach ( $return as $key ) {
				if ( array_key_exists( $key, $data ) ) {
					$filtered[ $key ] = $data[ $key ];
				}
			}

			return $filtered;
		}

		return $data;
	}//end bm_fetch_slot_details()


	/**
	 * Get total price
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_price( $basic_service_price, $service_min_cap ) {
		$total_price = 0;

		if ( ! empty( $basic_service_price ) && ! empty( $service_min_cap ) ) {
			$total_price = ( floatval( $basic_service_price ) * intval( $service_min_cap ) );
		}

		return $total_price;
	}//end bm_fetch_total_price()


	/**
	 * Fetch service selection response
	 *
	 * @author Darpan
	 */
	/**public function bm_fetch_service_selection_response( $service_id, $capacity_left = '', $min_cap = '', $resp = '' ) {
		$cap_div_style          = $capacity_left <= 0 ? 'style="cursor: not-allowed;"' : '';
		$cap_zero_style          = $capacity_left <= 0 ? 'style="background-color: #f0f0f1;pointer-events: none;"' : '';
		$service_selection_label = __( 'Select number of persons', 'service-booking' );
		$min_cap_text            = __( 'minimum: ', 'service-booking' );

		if ( !empty( $capacity_left ) && !empty( $min_cap ) && ( $capacity_left >= $min_cap ) ) {
			$resp .= '<div class="service_selection">';

			if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
				$resp .= '<span class="label_span"><span class="listed_service">' . $service_selection_label . '</span>';
				$resp .= '<span class="svc_min_cap_text">(' . $min_cap_text . '<span class="red_text">' . $min_cap . '</span>)</span></span>';
			}

			$resp .= '<div class="service_booking_no" ' . $cap_div_style . '>';
			$resp .= '<select class="service_total_booking" ' . $cap_zero_style . '>';

			for ( $i = $min_cap; $i <= $capacity_left; $i += $min_cap ) {
				$resp .= '<option value="' . $i . '">' . $i . '</option>';
			}

			$resp .= '</select>';
			$resp .= '</div></div>';
		}

		if ( $capacity_left < $min_cap ) {
			$resp .= '<div class="textcenter terms_required_errortext">' . esc_html__( 'Slots available are less than min capacity', 'service-booking' ) . '</div>';
		}

		return $resp;
	}*/ // end bm_fetch_service_selection_response()
	public function bm_fetch_service_selection_response( $service_id, $capacity_left = 0, $min_cap = 0, $resp = '' ) {
		$cap_div_style           = $capacity_left <= 0 ? 'style="cursor: not-allowed;"' : '';
		$cap_zero_style          = $capacity_left <= 0 ? 'style="background-color: #f0f0f1;pointer-events: none;"' : '';
		$service_selection_label = __( 'Select number of persons', 'service-booking' );
		$min_cap_text            = __( 'minimum: ', 'service-booking' );
		$hidden_no_persons       = false;

		if ( $capacity_left > 0 && $min_cap > 0 && ( $capacity_left >= $min_cap ) ) {
			$resp .= '<div class="service_selection">';

			if ( $this->bm_is_service_per_person_text_shown( $service_id ) ) {
				$resp .= '<span class="label_span"><span class="listed_service">' . $service_selection_label . '</span>';
				$resp .= '<span class="svc_min_cap_text">(' . $min_cap_text . '<span class="red_text">' . $min_cap . '</span>)</span></span>';
			}

			$resp .= '<div class="service_booking_no" ' . $cap_div_style . '>';

			$number_of_options    = absint( $capacity_left / $min_cap );
			$max_dropdown_options = 100;

			$settings = ( new BM_DBhandler() )->get_value( 'SERVICE', 'service_settings', $service_id, 'id' );
			$settings = ! empty( $settings ) ? maybe_unserialize( $settings ) : array();

			if ( isset( $settings['show_no_of_persons_box'] ) && $settings['show_no_of_persons_box'] == 0 ) {
				$hidden_no_persons = true;
			}

			if ( $number_of_options > $max_dropdown_options ) {
				$resp .= '<input type=' . ( $hidden_no_persons ? 'hidden' : 'number' ) . ' class="service_total_booking" min="' . esc_attr( $min_cap ) . '" step="' . esc_attr( $min_cap ) . '" max="' . esc_attr( $capacity_left ) . '" value="' . esc_attr( $min_cap ) . '" ' . $cap_zero_style . '>';
			} else {
				$resp .= '<select class="service_total_booking ' . ( $hidden_no_persons ? 'hidden' : '' ) . '" ' . $cap_zero_style . '>';
				for ( $i = $min_cap; $i <= $capacity_left; $i += $min_cap ) {
					$resp .= '<option value="' . esc_attr( $i ) . '">' . esc_html( $i ) . '</option>';
				}
				$resp .= '</select>';
			}

			$resp .= '</div></div>';
		}

		if ( $capacity_left < $min_cap ) {
			$resp .= '<div class="textcenter terms_required_errortext">' . esc_html__( 'Slots available are less than min capacity', 'service-booking' ) . '</div>';
		}

		return $resp;
	}


	/**
	 * Fetch field values with keys and fill in woocommerce checkout form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_field_with_field_key( $fields = array() ) {
		$data = array();
		$i    = 1;

		if ( ! empty( $fields ) ) {
			$dbhandler = new BM_DBhandler();

			foreach ( $fields as $key => $value ) {
				if ( ! empty( $key ) ) {
					$field = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_key' => $key ), 'row' );

					if ( ! empty( $field ) ) {
						$data[ $i ]['field_key'] = $key;
						$data[ $i ]['value']     = $value;
						$data[ $i ]['key']       = isset( $field->woocommerce_field ) ? $field->woocommerce_field : '';
						$data[ $i ]['label']     = isset( $field->field_label ) ? $field->field_label : '';
						$data[ $i ]['type']      = isset( $field->field_type ) ? $field->field_type : '';
					}
				}

				++$i;
			}
		}

		return $data;
	}//end bm_fetch_field_with_field_key()


	/**
	 * Check if primary email exists in field data
	 *
	 * @author Darpan
	 */
	public function bm_check_and_return_field_key_of_primary_email_in_field_data() {
		$dbhandler    = new BM_DBhandler();
		$email_fields = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_type' => 'email' ), 'results' );

		if ( empty( $email_fields ) || ! is_array( $email_fields ) ) {
			return false;
		}

		foreach ( $email_fields as $email ) {
			$email_options = isset( $email->field_options ) ? maybe_unserialize( $email->field_options ) : array();

			if ( isset( $email_options['is_main_email'] ) && $email_options['is_main_email'] == 1 ) {
				return $email->field_key ?? false;
			}
		}

		return false;
	}//end bm_check_and_return_field_key_of_primary_email_in_field_data()


	/**
	 * Check if default field
	 *
	 * @author Darpan
	 */
	public function bm_check_is_default_field( $field_id ) {
		$dbhandler  = new BM_DBhandler();
		$is_default = 0;
		$field      = $dbhandler->get_row( 'FIELDS', $field_id );

		if ( ! empty( $field ) ) {
			$field_options = isset( $field->field_options ) ? maybe_unserialize( $field->field_options ) : array();

			if ( ! empty( $field_options ) ) {
				$is_default = isset( $field_options['is_default'] ) ? $field_options['is_default'] : 0;
			}
		}

		return $is_default;
	}//end bm_check_is_default_field()


	/**
	 * Check if a field key exists
	 *
	 * @author Darpan
	 */
	public function bm_check_if_field_key_exists( $field_id ) {
		$dbhandler   = new BM_DBhandler();
		$is_existing = 1;
		$field_key   = $dbhandler->get_value( 'FIELDS', 'field_key', $field_id, 'id' );
		$field_keys  = array_column( ( $dbhandler->get_all_result( 'FIELDS', 'field_key', 1, 'results' ) ), 'field_key' );

		if ( ! empty( $field_key ) && ! in_array( $field_key, $field_keys, true ) ) {
			$is_existing = 0;
		}

		return $is_existing;
	}//end bm_check_if_field_key_exists()


	/**
	 * Check customer order counts
	 *
	 * @author Darpan
	 */
	public function bm_customer_order_count( $booking_id ) {
		$dbhandler   = new BM_DBhandler();
		$is_existing = 0;
		$customer_id = $dbhandler->get_value( 'BOOKING', 'customer_id', $booking_id, 'id' );
		$customer    = $dbhandler->get_row( 'CUSTOMERS', $customer_id, 'id' );

		if ( ! empty( $customer ) ) {
			$is_existing = 1;
		}

		return $is_existing;
	}//end bm_customer_order_count()


	/**
	 * Fetch total number of email fields in active fields
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_number_of_email_fields_in_active_filelds() {
		$dbhandler          = new BM_DBhandler();
		$total_email_fields = 0;
		$email_fields       = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_type' => 'email' ), 'results' );

		if ( ! empty( $email_fields ) && is_array( $email_fields ) ) {
			$total_email_fields = count( $email_fields );
		}

		return $total_email_fields;
	}//end bm_fetch_total_number_of_email_fields_in_active_filelds()


	/**
	 * Fetch select box html of email fields in active fields to set as primary email
	 *
	 * @author Darpan
	 */
	public function bm_fetch_available_email_fields_in_active_filelds_checkbox_html( $primary_email_key = '', $resp = '' ) {
		$dbhandler    = new BM_DBhandler();
		$main_label   = __( 'Choose any primary email field', 'service-booking' );
		$save         = __( 'Save', 'service-booking' );
		$email_fields = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_type' => 'email' ), 'results' );

		if ( ! empty( $email_fields ) && is_array( $email_fields ) ) {
			$resp .= '<div class="email_fields_results">';
			$resp .= '<h4 class="heading_choose_primary_email">' . $main_label . '</h4>';

			foreach ( $email_fields as $key => $email ) {
				if ( ! empty( $email ) ) {
					if ( ! empty( $primary_email_key ) && isset( $email->field_key ) && $email->field_key == $primary_email_key ) {
						continue;
					}

					$email_id    = isset( $email->id ) ? $email->id : 0;
					$email_label = isset( $email->field_label ) ? $email->field_label : '';

					$resp .= '<div class="active_emails_content" id="content_' . $key . '">';
					$resp .= '<div class="active_emails_available_' . $email_id . '">';
					$resp .= '<input type="checkbox" class="active_email_field" name="active_email_field" id="' . $email_id . '">';
					$resp .= '<label class="checkbox_label" for="' . $email_id . '">' . $email_label . '</label>';
					$resp .= '</div></div>';
				}
			}

			$resp .= '<div class="formbottombuttonbar">';
			$resp .= '<div class="formbuttoninnerbox">';
			$resp .= '<div class="bookbtn bgcolor bordercolor textwhite">';
			$resp .= '<a href="#" class="save_primary_email">';
			$resp .= $save . '</a></div></div>';
			$resp .= '<div class="primary_email_errortext" style="display :none;"></div>';
			$resp .= '</div></div>';
		} else {
			$resp .= '<div class="textcenter">' . esc_html__( 'No Emails Found', 'service-booking' ) . '</div>';
		} //end if

		return $resp;
	}//end bm_fetch_available_email_fields_in_active_filelds_checkbox_html()


	/**
	 * Fetch tel type field keys in booking form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_tel_type_field_keys_with_active_intl_code() {
		$dbhandler  = new BM_DBhandler();
		$tel_fields = $dbhandler->get_all_result( 'FIELDS', '*', array( 'field_type' => 'tel' ), 'results', 0, false, 'field_position', false );
		$field_key  = array();

		if ( ! empty( $tel_fields ) ) {
			foreach ( $tel_fields as $key => $tel ) {
				if ( ! empty( $tel ) ) {
					$tel_options = isset( $tel->field_options ) ? maybe_unserialize( $tel->field_options ) : array();

					if ( ! empty( $tel_options ) ) {
						$show_code = isset( $tel_options['show_intl_code'] ) ? $tel_options['show_intl_code'] : 0;
					}

					if ( isset( $show_code ) && $show_code == 1 ) {
						$field_key[ $key ] = isset( $tel->field_key ) ? $tel->field_key : '';
					}
				}
			}
		}

		return ! empty( $field_key ) ? array_values( $field_key ) : $field_key;
	}//end bm_fetch_all_tel_type_field_keys_with_active_intl_code()


	/**
	 * Replace field value in mail content
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_email_from_booking_form_data( $unique_key ) {
		$dbhandler     = new BM_DBhandler();
		$customer_data = array();
		$email         = '';

		if ( ! empty( $unique_key ) ) {
			if ( is_int( $unique_key ) ) {
				$customer_data = $this->get_customer_info_for_order( $unique_key );
			} elseif ( is_string( $unique_key ) ) {
				$customer_data = $dbhandler->get_value( 'FAILED_TRANSACTIONS', 'customer_data', $unique_key, 'booking_key' );
				$customer_data = maybe_unserialize( $customer_data );
				$customer_data = isset( $customer_data['billing_details'] ) ? $customer_data['billing_details'] : array();
			}

			if ( ! empty( $customer_data ) && isset( $customer_data['billing_email'] ) ) {
				$email = $customer_data['billing_email'];
			}
		}

		return $email;
	}//end bm_fetch_customer_email_from_booking_form_data()


	/**
	 * Fetch gift voucher recipient's mail id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_gift_recipient_email_from_booking_form_data( $unique_key ) {
		$email = '';

		if ( ! empty( $unique_key ) ) {
			$dbhandler = new BM_DBhandler();
			$order_id  = is_int( $unique_key ) ? $unique_key : $dbhandler->get_value( 'BOOKING', 'id', $unique_key, 'booking_key' );

			if ( is_int( $order_id ) && $order_id > 0 ) {
				$recipient_data = $dbhandler->get_value( 'VOUCHERS', 'recipient_data', $order_id, 'booking_id' );
				$recipient_data = ! empty( $recipient_data ) ? maybe_unserialize( $recipient_data ) : array();

				if ( isset( $recipient_data['recipient_email'] ) ) {
					$email = $recipient_data['recipient_email'];
				}
			}
		}

		return $email;
	}//end bm_fetch_gift_recipient_email_from_booking_form_data()


	/**
	 * Replace field value in mail content
	 *
	 * @author Darpan
	 */
	public function bm_replace_field_values_in_email_body( $field_name = '', $unique_key = 0, $customer = false ) {
		$dbhandler = new BM_DBhandler();

		$value       = '';
		$booking_key = $dbhandler->get_value( 'BOOKING', 'booking_key', $unique_key, 'id' );

		if ( $customer && ! empty( $booking_key ) ) {
			$old_locale = $this->bm_switch_locale_by_booking_reference( $booking_key );
		}
		if ( is_int( $unique_key ) ) {
			$value = $this->bm_replace_field_values_in_email_body_for_orders_with_order_id( $field_name, $unique_key );
		} elseif ( is_string( $unique_key ) ) {
			$value = $this->bm_replace_field_values_in_email_body_for_orders_with_no_order_id( $field_name, $unique_key );
		}
		if ( isset( $old_locale ) ) {
			$this->bm_restore_locale( $old_locale );
		}

		return $value;
	}//end bm_replace_field_values_in_email_body()


	/**
	 * Replace field value in mail content for orders with order id
	 *
	 * @author Darpan
	 */
	public function bm_replace_field_values_in_email_body_for_orders_with_order_id( $field_name = '', $order_id = 0 ) {
		$dbhandler      = new BM_DBhandler();
		$bm_mail        = new BM_Email();
		$value          = '';
		$order          = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
		$customer_id    = isset( $order->customer_id ) ? $order->customer_id : 0;
		$customer       = $dbhandler->get_row( 'CUSTOMERS', $customer_id );
		$billing_data   = isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : array();
		$recipient_data = $dbhandler->get_value( 'VOUCHERS', 'recipient_data', $order_id, 'booking_id' );
		$recipient_data = ! empty( $recipient_data ) ? maybe_unserialize( $recipient_data ) : array();
		$slot_format    = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );

		if ( $field_name == 'from_name' ) {
			return $bm_mail->bm_get_from_name();
		}

		if ( $field_name == 'from_mail' ) {
			return $bm_mail->bm_get_from_email();
		}

		if ( $field_name == 'admin_name' ) {
			return $bm_mail->bm_get_admin_username();
		}

		if ( $field_name == 'admin_email' ) {
			return get_option( 'admin_email' );
		}

		if ( $field_name == 'recipient_first_name' && isset( $recipient_data['recipient_first_name'] ) ) {
			return $recipient_data['recipient_first_name'];
		}

		if ( $field_name == 'recipient_last_name' && isset( $recipient_data['recipient_last_name'] ) ) {
			return $recipient_data['recipient_last_name'];
		}

		if ( $field_name == 'voucher_code' ) {
			return $dbhandler->get_value( 'VOUCHERS', 'code', $order_id, 'booking_id' );
		}

		if ( $field_name == 'booking_request_expiry' ) {
			$booking_expiry = $dbhandler->get_global_option_value( 'bm_book_on_request_expiry' );
			$booking_expiry = $booking_expiry > 0 ? floor( $booking_expiry ) : 7;
			return $booking_expiry . esc_html__( ' hour/s', 'service-booking' );
		}

		if ( $field_name == 'voucher_expiry_date' ) {
			$settings    = $dbhandler->get_value( 'VOUCHERS', 'settings', $order_id, 'booking_id' );
			$settings    = ! empty( $settings ) ? maybe_unserialize( $settings ) : array();
			$expiry_date = isset( $settings['expiry'] ) ? $settings['expiry'] : '';
			$expiry_date = ! empty( $expiry_date ) ? $this->bm_day_date_month_year_format( $this->bm_datetime_to_date_format( $expiry_date ) ) : '';
			return $expiry_date;
		}

		if ( $field_name == 'voucher_redeem_page_url' ) {
			$redeem_url = esc_url_raw( $dbhandler->bm_fetch_page_by_title( 'Flexibooking Voucher Redeem', OBJECT, 'page', 'url' ) );
			return "<a href='$redeem_url'>" . esc_html__( 'Redeem Here', 'service-booking' ) . '</a>';
		}

		if ( $field_name == 'service_duration' ) {
			$duration = $this->bm_fetch_booked_service_duration( (int) $order_id );
			return $this->bm_fetch_float_to_time_string( $duration );
		}

        // Suggested fix for your real extra services generator:
        if ( $field_name == 'extra_services' ) {
            $html           = '';
            $extra_products = $this->get_booked_extra_products_info( (int) $order_id );

            if ( is_array( $extra_products ) && !empty( $extra_products ) ) {
                $html .= '<tr><td colspan="4" style="padding:10px; border:1px solid #ddd;">';
                $html .= '<strong>Extra Services:</strong><ul style="margin: 5px 0 0 15px; padding: 0;">';

                foreach ( $extra_products as $extra_product ) {
                    $name  = isset( $extra_product['name'] ) ? esc_html( mb_strimwidth( $extra_product['name'], 0, 30, '...' ) ) : 'NA';
                    $qty   = isset( $extra_product['quantity'] ) ? esc_attr( $extra_product['quantity'] ) : 0;
                    $total = isset( $extra_product['total'] ) ? esc_html( $this->bm_fetch_price_in_global_settings_format( $extra_product['total'], true ) ) : 0;

                    $html .= '<li>' . $name . ' x ' . $qty . ' = <span>' . $total . '</span></li>';
                }

                $html .= '</ul></td></tr>';
            }
            return $html;
        }

		if ( empty( $value ) && ! empty( $order ) ) {
			$value = isset( $order->$field_name ) ? $order->$field_name : '';

			if ( $field_name == 'booking_key' ) {
				/**$value = $this->encrypt_key( $value, 'booking_reference_key' );*/
				return base64_encode( $value );
			}

			if ( $field_name == 'booking_created_at' ) {
				$value = $this->bm_datetime_to_date_format( $value );
				return $this->bm_day_date_month_year_format( $value );
			}

			if ( $field_name == 'booking_slots' ) {
				if ( $slot_format == '12' ) {
					$value = maybe_unserialize( $value );
					$from  = isset( $value['from'] ) ? $this->bm_am_pm_format( $value['from'] ) : '';
					$to    = isset( $value['to'] ) ? $this->bm_am_pm_format( $value['to'] ) : '';
					return $from . ' - ' . $to;
				} else {
					$value = maybe_unserialize( $value );
					$from  = isset( $value['from'] ) ? $this->bm_twenty_fourhrs_format( $value['from'] ) : '';
					$to    = isset( $value['to'] ) ? $this->bm_twenty_fourhrs_format( $value['to'] ) : '';
					return $from . ' - ' . $to;
				}
			}

			if ( ( $field_name == 'total_cost' ) || ( $field_name == 'base_svc_price' ) || ( $field_name == 'service_cost' ) || ( $field_name == 'disount_amount' ) || ( $field_name == 'subtotal' ) ) {
				return $this->bm_fetch_price_in_global_settings_format( $value, true );
			}
		}

		if ( empty( $value ) && ! empty( $billing_data ) ) {
			$value = isset( $billing_data[ $field_name ] ) ? $billing_data[ $field_name ] : '';

			if ( $field_name == 'billing_country' ) {
				if ( ! empty( $this->bm_get_countries( $value ) ) ) {
					return $this->bm_get_countries( $value );
				}
			}
		}

		return $value;
	}//end bm_replace_field_values_in_email_body_for_orders_with_order_id()


	/**
	 * Replace field value in mail content for orders with no order id
	 *
	 * @author Darpan
	 */
	public function bm_replace_field_values_in_email_body_for_orders_with_no_order_id( $field_name = '', $order_key = '' ) {
		$dbhandler      = new BM_DBhandler();
		$bm_mail        = new BM_Email();
		$value          = '';
		$order          = $dbhandler->get_row( 'FAILED_TRANSACTIONS', $order_key, 'booking_key' );
		$order_data     = isset( $order->booking_data ) ? maybe_unserialize( $order->booking_data ) : array();
		$customer_data  = isset( $order->customer_data ) ? maybe_unserialize( $order->customer_data ) : array();
		$billing_data   = isset( $customer_data['billing_details'] ) ? $customer_data['billing_details'] : array();
		$recipient_data = isset( $order->gift_data ) ? maybe_unserialize( $order->gift_data ) : array();

		if ( $field_name == 'from_name' ) {
			return $bm_mail->bm_get_from_name();
		}

		if ( $field_name == 'from_mail' ) {
			return $bm_mail->bm_get_from_email();
		}

		if ( $field_name == 'admin_name' ) {
			return $bm_mail->bm_get_admin_username();
		}

		if ( $field_name == 'admin_email' ) {
			return get_option( 'admin_email' );
		}

		if ( $field_name == 'booking_key' ) {
			/**$value = $this->encrypt_key( $order_key, 'booking_reference_key' );*/
			return base64_encode( $value );
		}

		if ( $field_name == 'recipient_first_name' && isset( $recipient_data['recipient_first_name'] ) ) {
			return $recipient_data['recipient_first_name'];
		}

		if ( $field_name == 'recipient_last_name' && isset( $recipient_data['recipient_last_name'] ) ) {
			return $recipient_data['recipient_last_name'];
		}

		if ( $field_name == 'voucher_code' ) {
			return $dbhandler->get_value( 'VOUCHERS', 'code', $order_key, 'booking_key' );
		}

		if ( $field_name == 'booking_request_expiry' ) {
			$booking_expiry = $dbhandler->get_global_option_value( 'bm_book_on_request_expiry' );
			$booking_expiry = $booking_expiry > 0 ? floor( $booking_expiry ) : 7;
			return $booking_expiry . esc_html__( ' hour/s' );
		}

		if ( $field_name == 'voucher_expiry_date' ) {
			$order_created_at    = isset( $order->created_at ) ? $order->created_at : '';
			$voucher_expiry_date = $this->bm_get_voucher_expiry_date( $order_created_at );
			return $this->bm_day_date_month_year_format( $this->bm_datetime_to_date_format( $voucher_expiry_date ) );
		}

		if ( $field_name == 'voucher_redeem_page_url' ) {
			$redeem_url = esc_url_raw( $dbhandler->bm_fetch_page_by_title( 'Flexibooking Voucher Redeem', OBJECT, 'page', 'url' ) );
			return "<a href='$redeem_url'>" . esc_html__( 'Redeem Here', 'service-booking' ) . '</a>';
		}

		if ( $field_name == 'booking_created_at' ) {
			$value = isset( $order->created_at ) ? $order->created_at : '';

			if ( ! empty( $value ) ) {
				$value = $this->bm_datetime_to_date_format( $value );
				return $this->bm_day_date_month_year_format( $value );
			}
		}

		if ( empty( $value ) && ! empty( $billing_data ) ) {
			$value = isset( $billing_data[ $field_name ] ) ? $billing_data[ $field_name ] : '';

			if ( $field_name == 'billing_country' ) {
				if ( ! empty( $this->bm_get_countries( $value ) ) ) {
					return $this->bm_get_countries( $value );
				}
			}
		}

		if ( $field_name == 'service_duration' ) {
			$duration = $this->bm_fetch_booked_service_duration( (string) $order_key );
			return $this->bm_fetch_float_to_time_string( $duration );
		}

		if ( $field_name == 'extra_services' ) {
			$html           = '';
			$extra_products = $this->get_booked_extra_products_info( (string) $order_key );

            if ( is_array( $extra_products ) && !empty( $extra_products ) ) {
                $html .= '<tr><td colspan="4" style="padding:10px; border:1px solid #ddd;">';
                $html .= '<strong>Extra Services:</strong><ul style="margin: 5px 0 0 15px; padding: 0;">';

                foreach ( $extra_products as $extra_product ) {
                    $name  = isset( $extra_product['name'] ) ? esc_html( mb_strimwidth( $extra_product['name'], 0, 30, '...' ) ) : 'NA';
                    $qty   = isset( $extra_product['quantity'] ) ? esc_attr( $extra_product['quantity'] ) : 0;
                    $total = isset( $extra_product['total'] ) ? esc_html( $this->bm_fetch_price_in_global_settings_format( $extra_product['total'], true ) ) : 0;

                    $html .= '<li>' . $name . ' x ' . $qty . ' = <span>' . $total . '</span></li>';
                }

                $html .= '</ul></td></tr>';
            }
            return $html;
        }

		if ( empty( $value ) && ! empty( $order_data ) ) {
			$value = isset( $order_data[ $field_name ] ) ? $order_data[ $field_name ] : '';

			if ( $field_name == 'booking_date' ) {
				return $this->bm_day_date_month_year_format( $value );
			}

			if ( ( $field_name == 'total_cost' ) || ( $field_name == 'base_svc_price' ) || ( $field_name == 'service_cost' ) || ( $field_name == 'disount_amount' ) || ( $field_name == 'subtotal' ) ) {
				return $this->bm_fetch_price_in_global_settings_format( $value, true );
			}
		}

		return $value;
	}//end bm_replace_field_values_in_email_body_for_orders_with_no_order_id()


	/**
	 * Send cutomer data atcchment along with admin mail
	 *
	 * @author Darpan
	 */
	public function bm_get_customer_details_attachment( $unique_key = 0 ) {
		$pdfroot = '';

		if ( is_int( $unique_key ) ) {
			$pdfroot = $this->bm_send_cutomer_data_to_admin_for_orders_with_order_id( $unique_key );
		} elseif ( is_string( $unique_key ) ) {
			$pdfroot = $this->bm_send_cutomer_data_to_admin_for_orders_with_no_order_id( $unique_key );
		}

		return $pdfroot;
	}//end bm_get_customer_details_attachment()


    /**
     * Send order details attachment to admin
     *
     * @author Darpan
     */
    public function bm_get_order_details_attachment( $booking_id, $is_customer = false, $pdfformat = true ) {
        $pdf_processor = new BM_PDF_Processor();
        $is_failed     = is_string( $booking_id );

        if ( $pdfformat ) {
            // Automatically returns the created file path
            return $pdf_processor->generate_booking_pdf( $booking_id, $is_failed, $is_customer );
        }

        // Return pure HTML if PDF format isn't requested
        return $pdf_processor->bm_get_template_pdf_content( 'booking', $booking_id, $is_customer );
    } //end bm_get_order_details_attachment()


    /**
     * Send voucher pdf to recipient
     *
     * @author Darpan
     */
    public function bm_add_voucher_pdf_to_recipient( $booking_id ) {
        if ( $booking_id <= 0 ) {
			return '';
        }

        $pdf_processor = new BM_PDF_Processor();
        return $pdf_processor->generate_voucher_pdf( $booking_id );
    } //end bm_add_voucher_pdf_to_recipient()


    /**
     * Send cutomer information attachment to admin for orders with order id
     *
     * @author Darpan
     */
    public function bm_send_cutomer_data_to_admin_for_orders_with_order_id( $booking_id = 0 ) {
        if ( $booking_id <= 0 ) {
			return '';
        }

        $pdf_processor = new BM_PDF_Processor();
        return $pdf_processor->generate_customer_info_pdf( $booking_id );
    } //end bm_send_cutomer_data_to_admin_for_orders_with_order_id()


    /**
     * Send cutomer information attachment to admin for orders with no order id
     *
     * @author Darpan
     */
    public function bm_send_cutomer_data_to_admin_for_orders_with_no_order_id( $booking_key = '' ) {
        if ( empty( $booking_key ) ) {
            return '';
        }

        $pdf_processor = new BM_PDF_Processor();
        return $pdf_processor->generate_failed_customer_info_pdf( $booking_key );
    } //end bm_send_cutomer_data_to_admin_for_orders_with_no_order_id()


	/**
	 * Check if a field is visible
	 *
	 * @author Darpan
	 */
	public function bm_check_if_field_is_visible( $field_id ) {
		$is_visible = 0;

		if ( ! empty( $field_id ) ) {
			$dbhandler = new BM_DBhandler();
			$field_row = $dbhandler->get_row( 'FIELDS', $field_id );

			if ( ! empty( $field_row ) && ! empty( $field_row->field_options ) ) {
				$field_options = maybe_unserialize( $field_row->field_options );
				$is_visible    = isset( $field_options['is_visible'] ) ? $field_options['is_visible'] : 0;
			}
		}

		return $is_visible;
	}//end bm_check_if_field_is_visible()


	/**
	 * Get page url
	 *
	 * @author Darpan
	 */
	public function bm_get_page_url() {
		return isset( $_SERVER['HTTP_HOST'] ) && isset( $_SERVER['REQUEST_URI'] ) ? filter_input( INPUT_SERVER, 'HTTP_HOST' ) . filter_input( INPUT_SERVER, 'REQUEST_URI' ) : '';
	}//end bm_get_page_url()


	/**
	 * Check service availability
	 *
	 * @author Darpan
	 */
	public function bm_service_is_bookable_old( $service_id = 0, $date = '' ) {
		$dbhandler   = new BM_DBhandler();
		$is_bookable = true;

		if ( ! empty( $service_id ) && ! empty( $date ) ) {
			$service     = $dbhandler->get_row( 'SERVICE', $service_id );
			$day_of_week = gmdate( 'w', strtotime( $date ) );

			if ( ! empty( $service ) ) {
				$service_unavailability = isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();

				if ( ! empty( $service_unavailability ) ) {
					$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
					$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

					if ( ! empty( $unavailable_dates ) && in_array( $date, $unavailable_dates ) ) {
						$is_bookable = false;
					}

					if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
						$is_bookable = false;
					}
				}
			} else {
				$is_bookable = false;
			}
		} else {
			$is_bookable = false;
		}

		return $is_bookable;
	} //end bm_service_is_bookable()


	public function bm_service_is_bookable( $service_id = 0, $date = '' ) {
		$dbhandler   = new BM_DBhandler();
		$is_bookable = true;

		if ( empty( $service_id ) || empty( $date ) ) {
			return false;
		}

		$global_unavailability = maybe_unserialize( $dbhandler->get_global_option_value( 'bm_global_unavailability' ) );
		if ( ! empty( $global_unavailability['dates'] ) ) {
			foreach ( $global_unavailability['dates'] as $range ) {
				list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
				if ( strtotime( $date ) >= strtotime( $start ) && strtotime( $date ) <= strtotime( $end ) ) {
					return false;
				}
			}
		}

		$service     = $dbhandler->get_row( 'SERVICE', $service_id );
		$day_of_week = gmdate( 'w', strtotime( $date ) );

		if ( empty( $service ) ) {
			return false;
		}

		$service_unavailability = isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();

		if ( ! empty( $service_unavailability ) ) {
			$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
			$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

			if ( ! empty( $unavailable_dates ) ) {
				foreach ( $unavailable_dates as $range ) {
					list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
					if ( strtotime( $date ) >= strtotime( $start ) && strtotime( $date ) <= strtotime( $end ) ) {
						return false;
					}
				}
			}

			if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Check bookable service ids
	 *
	 * @author Darpan
	 */
	public function bm_fetch_bookable_service_ids_old( $services = array(), $date = '' ) {
		$service_ids = array();

		if ( ! empty( $services ) && is_array( $services ) && ! empty( $date ) ) {
			$service_ids = wp_list_pluck( $services, 'id' );
			$day_of_week = gmdate( 'w', strtotime( $date ) );

			foreach ( $services as $service ) {
				$service_unavailability = isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();

				if ( ! empty( $service_unavailability ) ) {
					$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
					$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

					if ( ! empty( $unavailable_dates ) && in_array( $date, $unavailable_dates ) ) {
						$key = array_search( $service->id, $service_ids );
						if ( $key !== false ) {
							unset( $service_ids[ $key ] );
						}
					}

					if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
						$key = array_search( $service->id, $service_ids );
						if ( $key !== false ) {
							unset( $service_ids[ $key ] );
						}
					}
				}
			}
		} //end if

		return $service_ids;
	} //end bm_fetch_bookable_service_ids()


	public function bm_fetch_bookable_service_ids( $services = array(), $date = '' ) {
		$service_ids = array();
		$dbhandler   = new BM_DBhandler();

		if ( empty( $services ) || ! is_array( $services ) || empty( $date ) ) {
			return $service_ids;
		}

		$service_ids = wp_list_pluck( $services, 'id' );
		$day_of_week = gmdate( 'w', strtotime( $date ) );

		$global_unavailability = maybe_unserialize( $dbhandler->get_global_option_value( 'bm_global_unavailability' ) );

		if ( ! empty( $global_unavailability['dates'] ) ) {
			foreach ( $global_unavailability['dates'] as $range ) {
				if ( strpos( $range, 'to' ) !== false ) {
					list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
					if ( strtotime( $date ) >= strtotime( $start ) && strtotime( $date ) <= strtotime( $end ) ) {
						return array();
					}
				} elseif ( trim( $range ) === $date ) {
					return array();
				}
			}
		}

		foreach ( $services as $service ) {
			$service_unavailability = isset( $service->service_unavailability )
				? maybe_unserialize( $service->service_unavailability )
				: array();

			$unavailable = false;

			if ( ! empty( $service_unavailability ) ) {
				$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
				$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

				if ( ! empty( $unavailable_dates ) ) {
					foreach ( $unavailable_dates as $range ) {
						if ( strpos( $range, 'to' ) !== false ) {
							list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
							if ( strtotime( $date ) >= strtotime( $start ) && strtotime( $date ) <= strtotime( $end ) ) {
								$unavailable = true;
								break;
							}
						} elseif ( trim( $range ) === $date ) {
							$unavailable = true;
							break;
						}
					}
				}

				if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
					$unavailable = true;
				}

				if ( $unavailable ) {
					$key = array_search( $service->id, $service_ids );
					if ( $key !== false ) {
						unset( $service_ids[ $key ] );
					}
				}
			}
		}

		return $service_ids;
	}


	public function bm_fetch_bookable_category_ids( $services = array(), $date = '' ) {
		$category_ids = array();
		$dbhandler    = new BM_DBhandler();

		if ( empty( $services ) || ! is_array( $services ) || empty( $date ) ) {
			return $category_ids;
		}

		$category_ids = wp_list_pluck( $services, 'service_category' );
		$day_of_week  = gmdate( 'w', strtotime( $date ) );

		$global_unavailability = maybe_unserialize( $dbhandler->get_global_option_value( 'bm_global_unavailability' ) );

		if ( ! empty( $global_unavailability['dates'] ) ) {
			foreach ( $global_unavailability['dates'] as $range ) {
				if ( strpos( $range, 'to' ) !== false ) {
					list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
					if ( strtotime( $date ) >= strtotime( $start ) && strtotime( $date ) <= strtotime( $end ) ) {
						return array();
					}
				} elseif ( trim( $range ) === $date ) {
					return array();
				}
			}
		}

		foreach ( $services as $service ) {
			$service_unavailability = isset( $service->service_unavailability )
				? maybe_unserialize( $service->service_unavailability )
				: array();

			$unavailable = false;

			if ( ! empty( $service_unavailability ) ) {
				$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
				$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

				if ( ! empty( $unavailable_dates ) ) {
					foreach ( $unavailable_dates as $range ) {
						if ( strpos( $range, 'to' ) !== false ) {
							list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
							if ( strtotime( $date ) >= strtotime( $start ) && strtotime( $date ) <= strtotime( $end ) ) {
								$unavailable = true;
								break;
							}
						} elseif ( trim( $range ) === $date ) {
							$unavailable = true;
							break;
						}
					}
				}

				if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
					$unavailable = true;
				}

				if ( $unavailable ) {
					$key = array_search( $service->service_category, $category_ids );
					if ( $key !== false ) {
						unset( $category_ids[ $key ] );
					}
				}
			}
		}

		return $category_ids;
	}


	/**
	 * Filter bookable service ids
	 *
	 * @author Darpan
	 */
	public function bm_filter_bookable_service_ids( $bookable_ids = array() ) {
		$dbhandler = new BM_DBhandler();

		if ( ! empty( $bookable_ids ) && is_array( $bookable_ids ) ) {
			foreach ( $bookable_ids as $key => $service_id ) {
				$category_id = $this->bm_fetch_category_id_by_service_id( $service_id );
				$category    = $dbhandler->get_row( 'CATEGORY', $category_id );

				if ( ! empty( $category ) ) {
					$is_visible = isset( $category->cat_in_front ) ? $category->cat_in_front : 0;

					if ( $is_visible == 0 ) {
						unset( $bookable_ids[ $key ] );
					}
				}
			}

			if ( ! empty( $bookable_ids ) ) {
				$bookable_ids = array_values( array_unique( $bookable_ids ) );
			}
		} //end if

		return $bookable_ids;
	}//end bm_filter_bookable_service_ids()


	/**
	 * Check if category is visible
	 *
	 * @author Darpan
	 */
	public function bm_check_if_category_is_visible( $category_id ) {
		$dbhandler  = new BM_DBhandler();
		$is_visible = 1;

		if ( isset( $category_id ) ) {
			$category = $dbhandler->get_row( 'CATEGORY', $category_id );

			if ( ! empty( $category ) ) {
				$is_visible = isset( $category->cat_in_front ) ? $category->cat_in_front : 0;
			}
		} //end if

		return $is_visible;
	}//end bm_check_if_category_is_visible()


	/**
	 * Fetch visible category ids
	 *
	 * @author Darpan
	 */
	public function bm_fetch_visible_category_ids() {
		$dbhandler    = new BM_DBhandler();
		$category_ids = array( 0 );
		$categories   = $dbhandler->get_all_result( 'CATEGORY', '*', array( 'cat_in_front' => 1 ), 'results' );

		if ( ! empty( $categories ) ) {
			$ids = wp_list_pluck( $categories, 'id' );

			if ( ! empty( $ids ) && is_array( $ids ) ) {
				$category_ids = array_merge( $category_ids, $ids );
			}
		}

		return $category_ids;
	}//end bm_fetch_visible_category_ids()


	/**
	 * Check bookable category ids
	 *
	 * @author Darpan
	 */
	public function bm_fetch_bookable_category_ids_old( $services = array(), $date = '' ) {
		$category_ids = array();

		if ( ! empty( $services ) && is_array( $services ) && ! empty( $date ) ) {
			$category_ids = wp_list_pluck( $services, 'service_category' );
			$day_of_week  = gmdate( 'w', strtotime( $date ) );

			foreach ( $services as $service ) {
				$service_unavailability = isset( $service->service_unavailability ) ? maybe_unserialize( $service->service_unavailability ) : array();

				if ( ! empty( $service_unavailability ) ) {
					$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
					$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

					if ( ! empty( $unavailable_dates ) && in_array( $date, $unavailable_dates ) ) {
						$key = array_search( $service->service_category, $category_ids );
						if ( $key !== false ) {
							unset( $category_ids[ $key ] );
						}
					}

					if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
						$key = array_search( $service->service_category, $category_ids );
						if ( $key !== false ) {
							unset( $category_ids[ $key ] );
						}
					}
				}
			}
		} //end if

		return $category_ids;
	} //end bm_fetch_bookable_category_ids()


	public function bm_fetch_bookable_category_ids_old1( $services = array(), $date = '' ) {
		$category_ids = array();

		if ( ! empty( $services ) && is_array( $services ) && ! empty( $date ) ) {
			$category_ids = wp_list_pluck( $services, 'service_category' );
			$day_of_week  = gmdate( 'w', strtotime( $date ) );

			foreach ( $services as $service ) {
				$service_unavailability = isset( $service->service_unavailability )
					? maybe_unserialize( $service->service_unavailability )
					: array();

				if ( ! empty( $service_unavailability ) ) {
					$unavailable_days  = isset( $service_unavailability['weekdays'] ) ? $service_unavailability['weekdays'] : array();
					$unavailable_dates = isset( $service_unavailability['dates'] ) ? $service_unavailability['dates'] : array();

					$unavailable = false;

					// ✅ Handle ranges
					if ( ! empty( $unavailable_dates ) ) {
						foreach ( $unavailable_dates as $range ) {
							if ( strpos( $range, 'to' ) !== false ) {
								list( $start, $end ) = array_map( 'trim', explode( 'to', $range ) );
								if ( $date >= $start && $date <= $end ) {
									$unavailable = true;
									break;
								}
							} elseif ( $range === $date ) {
								$unavailable = true;
								break;
							}
						}
					}

					// ✅ Handle weekdays
					if ( ! empty( $unavailable_days ) && in_array( $day_of_week, $unavailable_days ) ) {
						$unavailable = true;
					}

					if ( $unavailable ) {
						$key = array_search( $service->service_category, $category_ids );
						if ( $key !== false ) {
							unset( $category_ids[ $key ] );
						}
					}
				}
			}
		}

		return $category_ids;
	}


	/**
	 * Get customer data for a WooCommerce order
	 *
	 * @author Darpan
	 */
	public function get_woocommerce_order_customer_data( $order_id ) {
		$customer_data   = array();
		$default_country = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_country', 'IT' );

		if ( ( new WooCommerceService() )->is_enabled() ) {
			$order = wc_get_order( $order_id );

			$customer_data['billing'] = array(
				'billing_first_name' => $order->get_billing_first_name(),
				'billing_last_name'  => $order->get_billing_last_name(),
				'billing_company'    => $order->get_billing_company(),
				'billing_address_1'  => $order->get_billing_address_1(),
				'billing_address_2'  => $order->get_billing_address_2(),
				'billing_city'       => $order->get_billing_city(),
				'billing_state'      => ! empty( $order->get_billing_state() ) ? $this->bm_get_countries( $order->get_billing_state() ) : $this->bm_get_countries( $default_country ),
				'billing_postcode'   => $order->get_billing_postcode(),
				'billing_country'    => $order->get_billing_country(),
				'billing_email'      => $order->get_billing_email(),
				'billing_phone'      => $order->get_billing_phone(),
			);

			$customer_data['shipping'] = array(
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_company'    => $order->get_shipping_company(),
				'shipping_address_1'  => $order->get_shipping_address_1(),
				'shipping_address_2'  => $order->get_shipping_address_2(),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => ! empty( $order->get_shipping_state() ) ? $this->bm_get_countries( $order->get_shipping_state() ) : $this->bm_get_countries( $default_country ),
				'shipping_postcode'   => $order->get_shipping_postcode(),
				'shipping_country'    => $order->get_shipping_country(),
			);
		}//end if

		return $customer_data;
	}//end get_woocommerce_order_customer_data()


	/**
	 * Get customer data for a WooCommerce order
	 *
	 * @author Darpan
	 */
	public function get_woocommerce_order_checkout_field_data( $wc_order_id ) {
		$customer_data   = array();
		$default_country = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_country', 'IT' );

		if ( ( new WooCommerceService() )->is_enabled() ) {
			$order = wc_get_order( $wc_order_id );

			$billing_address_2  = $order->get_billing_address_2();
			$shipping_address_2 = $order->get_shipping_address_2();

			$customer_data['billing'] = array(
				'billing_first_name'  => $order->get_billing_first_name(),
				'billing_last_name'   => $order->get_billing_last_name(),
				'billing_email'       => $order->get_billing_email(),
				'billing_contact'     => $order->get_billing_phone(),
				'billing_address'     => $order->get_billing_address_1() . ( ! empty( $billing_address_2 ) ? ', ' . $billing_address_2 : '' ),
				'billing_city'        => $order->get_billing_city(),
				'billing_state'       => $order->get_billing_state(),
				'billing_country'     => ! empty( $order->get_billing_country() ) ? $this->bm_get_countries( $order->get_billing_country() ) : $this->bm_get_countries( $default_country ),
				'billing_postcode'    => $order->get_billing_postcode(),
				'customer_order_note' => $order->get_customer_note(),
			);

			$customer_data['shipping'] = array(
				'shipping_first_name' => $order->get_shipping_first_name(),
				'shipping_last_name'  => $order->get_shipping_last_name(),
				'shipping_email'      => $order->get_billing_email(),
				'shipping_contact'    => $order->get_billing_phone(),
				'shipping_address'    => $order->get_shipping_address_1() . ( ! empty( $shipping_address_2 ) ? ', ' . $shipping_address_2 : '' ),
				'shipping_city'       => $order->get_shipping_city(),
				'shipping_state'      => $order->get_shipping_state(),
				'shipping_country'    => ! empty( $order->get_shipping_country() ) ? $this->bm_get_countries( $order->get_shipping_country() ) : $this->bm_get_countries( $default_country ),
				'shipping_zip'        => $order->get_shipping_postcode(),
				'customer_order_note' => $order->get_customer_note(),
			);
		}//end if

		return $customer_data;
	}//end get_woocommerce_order_checkout_field_data()


	/**
	 * Get customer data from an order
	 *
	 * @author Darpan
	 */
	public function get_customer_info_for_order( $booking_id ) {
		$dbhandler     = new BM_DBhandler();
		$customer_data = array();

		if ( $booking_id ) {
			$customer_id = $dbhandler->get_value( 'BOOKING', 'customer_id', $booking_id, 'id' );

			if ( ! empty( $customer_id ) ) {
				$customer = $dbhandler->get_row( 'CUSTOMERS', $customer_id );

				if ( ! empty( $customer ) ) {
					if ( isset( $customer->billing_details ) ) {
						$billing_data = maybe_unserialize( $customer->billing_details );

						if ( ! empty( $billing_data ) ) {
							$customer_data = $billing_data;
						}
					}

					if ( isset( $customer->shipping_details ) ) {
						$shipping_data = maybe_unserialize( $customer->shipping_details );

						if ( ! empty( $shipping_data ) ) {
							$customer_data = array_merge( $billing_data, $shipping_data );
						}
					}
				}
			}
		}

		return $customer_data;
	}//end get_customer_info_for_order()


	/**
	 * Get customer data from an order
	 *
	 * @author Darpan
	 */
	public function get_customer_info_for_failed_order( $failed_booking_id = 0 ) {
		$dbhandler     = new BM_DBhandler();
		$customer_data = array();

		if ( $failed_booking_id > 0 ) {
			$customer_data = $dbhandler->get_value( 'FAILED_TRANSACTIONS', 'customer_data', $failed_booking_id, 'id' );
			$customer_data = ! empty( $customer_data ) ? maybe_unserialize( $customer_data ) : array();
		}

		return $customer_data;
	}//end get_customer_info_for_failed_order()


	/**
	 * Get customer data from an order
	 *
	 * @author Darpan
	 */
	public function get_customer_info_for_archived_order( $archived_booking_id = 0 ) {
		$dbhandler     = new BM_DBhandler();
		$customer_data = array();

		if ( $archived_booking_id > 0 ) {
			$booking_data = $dbhandler->get_value( 'BOOKING_ARCHIVE', 'booking_data', $archived_booking_id, 'id' );
			$booking_data = ! empty( $booking_data ) ? maybe_unserialize( $booking_data ) : array();

			if ( isset( $booking_data->customer_id ) ) {
				$customer_data = $dbhandler->get_value( 'CUSTOMERS', 'billing_details', $booking_data->customer_id, 'id' );
				$customer_data = ! empty( $customer_data ) ? maybe_unserialize( $customer_data ) : array();
			}
		}

		return $customer_data;
	}//end get_customer_info_for_archived_order()


	/**
	 * Get customer data as mentioned in booking form
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_customer_data( $order_id ) {
		$customer_data = array();

		if ( ! empty( $order_id ) && $order_id !== 0 ) {
			$dbhandler = new BM_DBhandler();
			$order     = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );

			if ( ! empty( $order ) ) {
				$fields = isset( $order->field_values ) ? maybe_unserialize( $order->field_values ) : array();
				$data   = ! empty( $fields ) ? $this->bm_fetch_field_with_field_key( $fields ) : array();

				if ( ! empty( $data ) ) {
					foreach ( $data as $field_data ) {
						$field_key   = isset( $field_data['key'] ) ? $field_data['key'] : 0;
						$field_value = isset( $field_data['value'] ) ? $field_data['value'] : '';

						$customer_data[ $field_key ] = $field_value;
					}

					if ( ! array_key_exists( 'billing_state', $customer_data ) ) {
						$customer_data['billing_state'] = isset( $order->booking_country ) ? $order->booking_country : '';
					}
				}
			}
		}

		return $customer_data;
	}//end bm_fetch_order_customer_data()


	/**
	 * Attach customer info to order details
	 *
	 * @author Darpan
	 */
	public function bm_attach_customer_data_to_order_data( $orders ) {
		if ( is_array( $orders ) && ! empty( $orders ) ) {
			foreach ( $orders as $order ) {
				if ( ! empty( $order ) ) {
					$customer_data        = $this->get_customer_info_for_order( $order->id );
					$order->first_name    = isset( $customer_data['billing_first_name'] ) ? $customer_data['billing_first_name'] : '';
					$order->last_name     = isset( $customer_data['billing_last_name'] ) ? $customer_data['billing_last_name'] : '';
					$order->contact_no    = isset( $customer_data['billing_contact'] ) ? $customer_data['billing_contact'] : '';
					$order->email_address = isset( $customer_data['billing_email'] ) ? $customer_data['billing_email'] : '';
				}
			}
		} elseif ( is_object( $orders ) && ! empty( $orders ) ) {
			$customer_data        = $this->get_customer_info_for_order( $orders->id );
			$order->first_name    = isset( $customer_data['billing_first_name'] ) ? $customer_data['billing_first_name'] : '';
			$order->last_name     = isset( $customer_data['billing_last_name'] ) ? $customer_data['billing_last_name'] : '';
			$order->contact_no    = isset( $customer_data['billing_contact'] ) ? $customer_data['billing_contact'] : '';
			$order->email_address = isset( $customer_data['billing_email'] ) ? $customer_data['billing_email'] : '';
		}

		return $orders;
	}//end bm_attach_customer_data_to_order_data()


	/**
	 * Fetch bookable services by date and category id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_bookable_services_by_date_and_category_id( $data = array() ) {
		$dbhandler = new BM_DBhandler();
		$bookable  = array();

		if ( ! empty( $data ) ) {
			$category_id = isset( $data['id'] ) ? $data['id'] : null;
			$date        = isset( $data['date'] ) ? $data['date'] : '';

			if ( isset( $category_id ) && ! empty( $date ) ) {
				$services = $dbhandler->get_all_result(
					'SERVICE',
					'*',
					array(
						'is_service_front' => 1,
						'service_category' => $category_id,
					),
					'results',
					0,
					false,
					'service_position',
					'DESC'
				);

				if ( ! empty( $services ) ) {
					foreach ( $services as $service ) {
						if ( $this->bm_service_is_bookable( $service->id, $date ) ) {
							$bookable[] = $service;
						}
					}
				}
			}
		}

		return $bookable;
	}//end bm_fetch_bookable_services_by_date_and_category_id()


	/**
	 * Get products ordered for a specific order
	 *
	 * @author Darpan
	 */
	public function get_woocommerce_booked_products( $order_id, $booking_id = 0 ) {
		$dbhandler          = new BM_DBhandler();
		$woocommerceservice = new WooCommerceService();
		$products           = array();

		if ( $woocommerceservice->is_enabled() ) {
			$order         = wc_get_order( $order_id );
			$booking_data  = $dbhandler->get_row( 'BOOKING', $booking_id, 'id' );
			$category_id   = $this->bm_fetch_category_id_by_service_id( $booking_data->service_id );
			$category_name = $this->bm_fetch_category_name_by_service_id( $booking_data->service_id );

			foreach ( $order->get_items() as $item ) {
				$product_id       = isset( $item['product_id'] ) ? $item['product_id'] : 0;
				$product_name     = isset( $item['name'] ) ? $item['name'] : '';
				$product_quantity = isset( $item['quantity'] ) ? $item['quantity'] : 0;
				$product_total    = isset( $item['total'] ) ? $item['total'] : 0;

				$products[] = array(
					'id'            => ! empty( $product_id ) ? $product_id : 0,
					'name'          => ! empty( $product_name ) ? $product_name : '',
					'service_id'    => isset( $booking_data->service_id ) ? $booking_data->service_id : 0,
					'category_id'   => ! empty( $category_id ) ? $category_id : 0,
					'category_name' => ! empty( $category_name ) ? $category_name : '',
					'quantity'      => ! empty( $product_quantity ) ? $product_quantity : 0,
					'base_price'    => get_post_meta( $product_id, '_price', true ),
					'total'         => ! empty( $product_total ) ? $product_total : 0,
				);
			}
		} //end if

		return $products;
	}//end get_woocommerce_booked_products()


	/**
	 * Fetch ordered non-woocommerce service info for a specific order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_non_woocmmerce_booked_service_info( $order_id ) {
		$product = array();

		if ( $order_id > 0 ) {
			$dbhandler = new BM_DBhandler();
			$order     = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );

			if ( ! empty( $order ) ) {
				$service_id   = isset( $order->service_id ) ? $order->service_id : 0;
				$booking_date = isset( $order->booking_date ) ? $order->booking_date : '';

                if ( $service_id > 0 && !empty( $booking_date ) ) {
                    $category_id   = $this->bm_fetch_category_id_by_service_id( $service_id );
                    $category_name = $this->bm_fetch_category_name_by_service_id( $service_id );

                    $product[] = array(
                        'name'          => isset( $order->service_name ) ? $order->service_name : '',
                        'image'         => esc_url( $this->bm_fetch_image_url_or_guid( $service_id, 'SERVICE', 'url' ) ),
                        'service_id'    => $service_id,
                        'category_id'   => !empty( $category_id ) ? $category_id : 0,
                        'category_name' => !empty( $category_name ) ? $category_name : '',
                        'booking_slots' => isset( $order->booking_slots ) ? maybe_serialize( $order->booking_slots ) : array(),
                        'quantity'      => !empty( $order->total_svc_slots ) ? $order->total_svc_slots : 0,
                        'base_price'    => isset( $order->base_svc_price ) ? $order->base_svc_price : 0,
                        'total'         => isset( $order->service_cost ) ? $order->service_cost : 0,
                    );
                }
            }
        } //end if

		return $product;
	}//end bm_fetch_non_woocmmerce_booked_service_info()


	/**
	 * Get product info for checkout page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booked_service_info_for_checkout( $booking_key ) {
		$dbhandler = new BM_DBhandler();

		$discounted_key = $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ? 'discounted_' : '';
		$order_data     = $dbhandler->bm_fetch_data_from_transient( $discounted_key . $booking_key );

		$booking_currency  = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
		$currency_char     = $this->bm_get_currency_char( $booking_currency );
		$products          = array();
		$module_age_ranges = array();
		$age_input_html    = '';

		if ( ! empty( $order_data ) ) {
			$main_product = $this->bm_prepare_service_data( $booking_key, $booking_currency );

			if ( ! empty( $main_product ) ) {
				$products['product'][0] = $main_product;
				$products['product']    = ! empty( $products['product'][0] ) ? array_merge( $products['product'], $this->bm_prepare_extra_services_data( $booking_key, $booking_currency ) ) : array();
				$svc_price_module_id    = isset( $order_data['svc_price_module_id'] ) ? esc_attr( $order_data['svc_price_module_id'] ) : 0;

				if ( ! empty( $products ) ) {
					if ( ! empty( $svc_price_module_id ) ) {
						$dbhandler->bm_save_data_to_transient( 'flexi_svc_price_module_id_' . $booking_key, $svc_price_module_id, 72 );
						$module_age_ranges = $this->bm_fetch_age_ranges_defined_in_service( $order_data['service_id'] );
						if ( empty( $module_age_ranges ) ) {
							$module_age_ranges = $this->bm_fetch_external_service_price_module_age_ranges( $svc_price_module_id, $order_data['service_id'] );
						}
					}

					if ( ! empty( $module_age_ranges ) && is_array( $module_age_ranges ) ) {
						$age_input_html = $this->bm_fetch_module_age_range_html( $module_age_ranges, $booking_key );
					}

					$show_no_of_persons_box = false;
					$service_settings       = $dbhandler->get_value( 'SERVICE', 'service_settings', $order_data['service_id'], 'id' );
					$service_settings       = ! empty( $service_settings ) ? maybe_unserialize( $service_settings ) : array();
					if ( isset( $service_settings['show_no_of_persons_box'] ) && $service_settings['show_no_of_persons_box'] == 0 ) {
						$show_no_of_persons_box = true;
					}

					$from = '00:00';
					if ( isset( $order_data['booking_slots'] ) && strpos( $order_data['booking_slots'], ' - ' ) !== false ) {
						$booking_slots = explode( ' - ', $order_data['booking_slots'] );
						$from          = isset( $booking_slots[0] ) ? $booking_slots[0] : '00:00';
					} else {
						$from = isset( $order_data['booking_slots'] ) ? $order_data['booking_slots'] : '00:00';
					}

					$service_date     = isset( $order_data['booking_date'] ) ? esc_attr( $order_data['booking_date'] ) : '';
					$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
					$service_dateTime = new DateTime( $service_date . ' ' . $from, new DateTimeZone( $timezone ) );

					$products['age_html']         = $age_input_html;
					$products['discount']         = isset( $order_data['discount'] ) ? esc_attr( $order_data['discount'] ) : 0;
					$products['subtotal']         = isset( $order_data['subtotal'] ) ? esc_attr( $order_data['subtotal'] ) : esc_attr( $order_data['total_cost'] );
					$products['total']            = isset( $order_data['total_cost'] ) ? esc_attr( $order_data['total_cost'] ) : 0;
					$products['service_date']     = $service_date;
					$products['service_datetime'] = $service_dateTime->format( 'd/m/y H:i' );
					$products['hidden_qty']       = $show_no_of_persons_box;
				}
			}
		}

		$dbhandler->bm_save_data_to_transient( 'flexi_svc_price_module_age_ranges_' . $booking_key, $module_age_ranges, 72 );

		return apply_filters( 'bm_checkout_page_products_data', $products, $booking_key );
	}//end bm_fetch_booked_service_info_for_checkout()



	/**
	 * Get product info for backend order page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_price_info_after_discount( $booking_key ) {
		$dbhandler         = new BM_DBhandler();
		$discounted_key    = $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ? 'discounted_' : '';
		$order_data        = $dbhandler->bm_fetch_data_from_transient( $discounted_key . $booking_key );
		$price_info        = array();
		$module_age_ranges = array();

		if ( ! empty( $order_data ) ) {
			$svc_price_module_id = isset( $order_data['svc_price_module_id'] ) ? esc_attr( $order_data['svc_price_module_id'] ) : 0;
			$service_id          = isset( $order_data['service_id'] ) ? $order_data['service_id'] : 0;

			if ( ! empty( $svc_price_module_id ) ) {
				$dbhandler->bm_save_data_to_transient( 'flexi_svc_price_module_id_' . $booking_key, $svc_price_module_id, 72 );
				$module_age_ranges = $this->bm_fetch_age_ranges_defined_in_service( $service_id );

				if ( empty( $module_age_ranges ) ) {
					$module_age_ranges = $this->bm_fetch_external_service_price_module_age_ranges( $svc_price_module_id, $order_data['service_id'] );
				}
			}

			$price_info['discount'] = isset( $order_data['discount'] ) ? $order_data['discount'] : 0;
			$price_info['total']    = isset( $order_data['total_cost'] ) ? $order_data['total_cost'] : 0;
			$price_info['subtotal'] = isset( $order_data['subtotal'] ) ? $order_data['subtotal'] : $price_info['total'];
		}

		$dbhandler->bm_save_data_to_transient( 'flexi_svc_price_module_age_ranges_' . $booking_key, $module_age_ranges, 72 );

		return apply_filters( 'bm_checkout_page_products_data', $price_info, $booking_key );
	}//end bm_fetch_order_price_info_after_discount()


	/**
	 * Prepare services data for checkout page
	 *
	 * @author Darpan
	 */
	public function bm_prepare_service_data( $booking_key, $currency ) {
		$dbhandler  = new BM_DBhandler();
		$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );

		$order_data = apply_filters( 'bm_prepare_service_checkout_order_data', $order_data, $booking_key );

		$service_id = isset( $order_data['service_id'] ) ? $order_data['service_id'] : 0;
		$service    = $dbhandler->get_row( 'SERVICE', $service_id );
		$product    = array();

        if ( !empty( $service ) ) {
            $product = array(
                'id'          => isset( $service->id ) ? esc_html( $service->id ) : 0,
                'name'        => isset( $service->service_name ) ? esc_html( $service->service_name ) : 'N/A',
                'image'       => esc_url( $this->bm_fetch_image_url_or_guid( $service_id, 'SERVICE', 'url' ) ),
                'description' => isset( $service->service_desc ) ? wp_kses_post( stripslashes( $service->service_desc ) ) : 'N/A',
                'amount'      => isset( $order_data['service_cost'] ) ? esc_attr( $order_data['service_cost'] ) : 0,
                'base_price'  => isset( $order_data['base_svc_price'] ) ? esc_attr( $order_data['base_svc_price'] ) : 0,
                'currency'    => $currency,
                'quantity'    => isset( $order_data['total_service_booking'] ) ? esc_attr( $order_data['total_service_booking'] ) : 0,
            );
        }

		return apply_filters( 'bm_prepare_service_checkout_product_data', $product, $booking_key, $currency );
	}


	/**
	 * Prepare extra services data for checkout page
	 *
	 * @author Darpan
	 */
	public function bm_prepare_extra_services_data( $booking_key, $currency ) {
		$dbhandler  = new BM_DBhandler();
		$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );

		$order_data = apply_filters( 'bm_prepare_extra_services_checkout_order_data', $order_data, $booking_key );

		$extra_service_ids  = isset( $order_data['extra_svc_booked'] ) ? explode( ',', $order_data['extra_svc_booked'] ) : array();
		$extra_slots_booked = isset( $order_data['total_extra_slots_booked'] ) ? explode( ',', $order_data['total_extra_slots_booked'] ) : array();
		$extra_services     = array();

        if ( !empty( $extra_service_ids ) && !empty( $extra_slots_booked ) ) {
            foreach ( $extra_service_ids as $key => $extra_id ) {
                $extra_service = $dbhandler->get_row( 'EXTRA', $extra_id );
                if ( !empty( $extra_service ) ) {
                    $extra_services[] = array(
                        'id'          => isset( $extra_service->id ) ? esc_html( $extra_service->id ) : 0,
                        'name'        => isset( $extra_service->extra_name ) ? esc_html( $extra_service->extra_name ) : 'N/A',
                        'image'       => plugins_url( '../public/partials/image/Image-not-found.png', __FILE__ ),
                        'description' => isset( $extra_service->extra_desc ) ? wp_kses_post( stripslashes( $extra_service->extra_desc ) ) : 'N/A',
                        'quantity'    => $extra_slots_booked[ $key ],
                        'currency'    => $currency,
                        'base_price'  => isset( $extra_service->extra_price ) ? esc_attr( $extra_service->extra_price ) : 0,
                        'amount'      => $this->bm_fetch_total_price( $extra_service->extra_price, $extra_slots_booked[ $key ] ),
                    );
                }
            }
        }

		return apply_filters( 'bm_prepare_extra_services_checkout_product_data', $extra_services, $booking_key, $currency );
	}


	/**
	 * Check if booked product is allowed as a gift
	 *
	 * @author Darpan
	 */
	public function bm_check_if_booked_service_is_allowed_as_gift( $booking_key ) {
		$is_allowed_as_a_gift = false;

		$dbhandler = new BM_DBhandler();

		$discounted_key = $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ? 'discounted_' : '';
		$order_data     = $dbhandler->bm_fetch_data_from_transient( $discounted_key . $booking_key );

		if ( ! empty( $order_data ) ) {
			$service_id           = isset( $order_data['service_id'] ) ? $order_data['service_id'] : 0;
			$is_allowed_as_a_gift = $this->bm_is_service_allowed_as_gift( $service_id );
		}

		return apply_filters( 'bm_if_service_is_allowed_as_gift', $is_allowed_as_a_gift, $booking_key );
	}//end bm_check_if_booked_service_is_allowed_as_gift()


	/**
	 * Get product info for stripe payment intent
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booked_service_info_for_stripe_payment_intent( $booking_key ) {
		$dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
		$booking_currency = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
		$product          = array();

		if ( ! empty( $order_data ) ) {
			$product['description'] = isset( $order_data['service_name'] ) ? esc_html( $order_data['service_name'] ) : 'N/A';
			$product['amount']      = isset( $order_data['total_cost'] ) ? esc_attr( $order_data['total_cost'] ) : 0;
			$product['currency']    = $booking_currency;
			$product['quantity']    = isset( $order_data['total_service_booking'] ) ? esc_attr( $order_data['total_service_booking'] ) : 0;
		} //end if

		return $product;
	}//end bm_fetch_booked_service_info_for_stripe_payment_intent()


	/**
	 * Get product info for stripe payment intent
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booked_service_info_by_payment_intent( $booking_key ) {
		$dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
		$booking_currency = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
		$product          = array();

		if ( ! empty( $order_data ) ) {
			$product['description'] = isset( $order_data['service_name'] ) ? esc_html( $order_data['service_name'] ) : 'N/A';
			$product['amount']      = isset( $order_data['total_cost'] ) ? esc_attr( $order_data['total_cost'] ) : 0;
			$product['currency']    = $booking_currency;
			$product['quantity']    = isset( $order_data['total_service_booking'] ) ? esc_attr( $order_data['total_service_booking'] ) : 0;
		} //end if

		return $product;
	}//end bm_fetch_booked_service_info_by_payment_intent()


	/**
	 * Get ordered extra products info for a specific order
	 *
	 * @author Darpan
	 */
	public function get_booked_extra_products_info( $order_id ) {
		$products = array();

		if ( ! empty( $order_id ) ) {
			$dbhandler = new BM_DBhandler();

			if ( is_string( $order_id ) ) {
				$order_data         = $dbhandler->get_row( 'FAILED_TRANSACTIONS', $order_id, 'booking_key' );
				$order              = isset( $order_data->booking_data ) ? maybe_unserialize( $order_data->booking_data ) : array();
				$extra_ids          = isset( $order['extra_svc_booked'] ) ? $order['extra_svc_booked'] : '';
				$extra_slots_booked = isset( $order['total_extra_slots_booked'] ) ? $order['total_extra_slots_booked'] : '';
				$extra_slots_booked = ! empty( $extra_slots_booked ) ? explode( ',', $extra_slots_booked ) : array();
			} else {
				$order     = $dbhandler->get_row( 'BOOKING', $order_id, 'id' );
				$extra_ids = isset( $order->extra_svc_booked ) ? $order->extra_svc_booked : '';
			}

			if ( ! empty( $extra_ids ) ) {
				$additional = "id in($extra_ids)";
				$items      = $dbhandler->get_all_result( 'EXTRA', '*', 1, 'results', 0, false, 'id', 'DESC', $additional );

				if ( ! empty( $items ) ) {
					foreach ( $items as $key => $item ) {
						$item_price = isset( $item->extra_price ) ? $item->extra_price : 0;

						if ( is_string( $order_id ) ) {
							$quantity = isset( $extra_slots_booked[ $key ] ) ? $extra_slots_booked[ $key ] : 0;
						} else {
							$quantity = $dbhandler->get_all_result(
								'EXTRASLOTCOUNT',
								'slots_booked',
								array(
									'extra_svc_id' => isset( $item->id ) ? $item->id : 0,
									'booking_id'   => $order_id,
								),
								'var'
							);
						}

						$base_price = isset( $item_price ) ? $item_price : 0;
						$total      = isset( $item_price ) ? $this->bm_fetch_total_price( $item_price, $quantity ) : 0;

						$products[] = array(
							'id'         => isset( $item->id ) ? $item->id : 0,
							'name'       => isset( $item->extra_name ) ? $item->extra_name : '',
							'image'      => plugins_url( '../public/partials/image/Image-not-found.png', __FILE__ ),
							'quantity'   => ! empty( $quantity ) ? $quantity : 0,
							'base_price' => $base_price,
							'total'      => $total,
						);
					}
				}
			}
		} //end if

		return $products;
	}//end get_booked_extra_products_info()


	/**
	 * Get ordered products info for a final order details page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_product_info_order_details_page( $order_id = 0, $return_html = false, $checkout = 'flexi_checkout' ) {
		$products = array();
		$results  = array();
		$resp     = '';

		if ( $order_id > 0 ) {
			$dbhandler   = new BM_DBhandler();
			$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $order_id, 'id' );
			$discount    = $dbhandler->get_value( 'BOOKING', 'disount_amount', $order_id, 'id' );
			$total       = $dbhandler->get_value( 'BOOKING', 'total_cost', $order_id, 'id' );
			$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $order_id, 'id' );

			if ( $wc_order_id > 0 && $checkout == 'woocommerce_checkout' ) {
				$products = $this->get_woocommerce_booked_products( $wc_order_id, $order_id );
			} else {
				$products      = $this->bm_fetch_non_woocmmerce_booked_service_info( $order_id );
				$extra_product = $this->get_booked_extra_products_info( (int) $order_id );

				if ( ! empty( $extra_product ) && is_array( $extra_product ) ) {
					$products = array( ...$products, ...$extra_product );
				}
			}

			if ( ! empty( $products ) && is_array( $products ) ) {
				$subtotal = array_sum( array_column( $products, 'total' ) );

				$results['products'] = $products;
				$results['subtotal'] = $subtotal;
				$results['discount'] = $discount;
				$results['total']    = $total;

				$resp .= '<div class="expandorderbox" id="expandorderbox">';
				$resp .= '<ul class="ordertextbox">';

				foreach ( $products as $product ) {
					$resp .= '<li><image src="' . ( isset( $product['image'] ) ? esc_url( $product['image'] ) : '' ) . '" style="width:140px;height:100px;"></li>';
					$resp .= '<li title="' . ( isset( $product['name'] ) ? esc_html( $product['name'] ) : 'NA' ) . '"><strong>' . ( isset( $product['name'] ) ? esc_html( mb_strimwidth( $product['name'], 0, 30, '...' ) ) : 'NA' ) . '</strong> ' . esc_html( 'x' ) . ' ' . ( isset( $product['quantity'] ) ? esc_attr( $product['quantity'] ) : 0 ) . ' ' . ( $product['quantity'] > 1 ? esc_html__( 'quantities - ', 'service-booking' ) : esc_html__( 'quantity - ', 'service-booking' ) ) . '<span>' . ( isset( $product['total'] ) ? esc_html( $this->bm_fetch_price_in_global_settings_format( $product['total'], true ) ) : 0 ) . '</span></li>';
				} //end foreach

				$resp .= '<li><strong>' . esc_html__( 'Discount - ', 'service-booking' ) . '</strong><span id="checkout_discount">' . ( ! empty( $discount ) ? esc_html( $this->bm_fetch_price_in_global_settings_format( $discount ) ) : esc_html( $this->bm_fetch_price_in_global_settings_format( 0 ) ) ) . '</span></li>';
				$resp .= '</ul>';
				$resp .= '<p class="total"><strong>' . esc_html__( 'Total Cost - ', 'service-booking' ) . '</strong><span id="checkout_total">' . ( ! empty( $total ) ? esc_html( $this->bm_fetch_price_in_global_settings_format( $total ) ) : esc_html( $this->bm_fetch_price_in_global_settings_format( 0 ) ) ) . '</span></p>';
				$resp .= '</div>';
			} //end if
		} //end if

		return $return_html ? $resp : $results;
	}//end bm_fetch_product_info_order_details_page()


	/**
	 * Fetch ordered services details
	 *
	 * @author Darpan
	 */
	public function bm_fetch_ordered_service_details( $order_id = 0, $checkout = 'flexi_checkout' ) {
		$product_data = array();
		$resp         = '';

		if ( $order_id > 0 ) {
			$dbhandler   = new BM_DBhandler();
			$wc_order_id = $dbhandler->get_value( 'BOOKING', 'wc_order_id', $order_id, 'id' );

			if ( $wc_order_id > 0 && $checkout == 'woocommerce_checkout' ) {
				$product_data = $this->get_woocommerce_booked_products( $wc_order_id, $order_id );
			} else {
				$product_data  = $this->bm_fetch_non_woocmmerce_booked_service_info( $order_id );
				$extra_product = $this->get_booked_extra_products_info( (int) $order_id );
				if ( ! empty( $extra_product ) && is_array( $extra_product ) ) {
					$product_data = array( $product_data, ...$extra_product );
				}
			}

			if ( ! empty( $product_data ) && is_array( $product_data ) ) {
				$i = 1;
				foreach ( $product_data as $product ) {
					$resp .= $i == 1 ? '<h2>' . __( 'Service', 'service-booking' ) . '</h2>' : '<h2>' . __( 'Extra Service', 'service-booking' ) . '</h2>';
					$resp .= '<div class="product-dialog">';
					$resp .= '<ul id="product-list">';
					$resp .= '<li>';

					$resp .= '<div>';
					$resp .= '<label for="product' . $i . '-name">' . __( 'Name', 'service-booking' ) . ':</label>';
					$resp .= '<input type="text" id="product' . $i . '-name" class="product-name" value="' . $product['name'] . '">';
					$resp .= '</div>';

					$resp .= '<div>';
					$resp .= '<label for="product' . $i . '-price">' . __( 'Price', 'service-booking' ) . ':</label>';
					$resp .= '<input type="number" id="product.' . $i . '-price" class="product-price" value="' . $product['base_price'] . '">';
					$resp .= '</div>';

					$resp .= '<div>';
					$resp .= '<label for="product' . $i . '-quantity">' . __( 'Quantity', 'service-booking' ) . ':</label>';
					$resp .= '<div class="quantity-input">';
					$resp .= '<button class="decrement-btn">-</button>';
					$resp .= '<input type="number" id="product' . $i . '-quantity" class="product-quantity" value="' . $product['quantity'] . '" readonly>';
					$resp .= '<button class="increment-btn">+</button>';
					$resp .= '</div>';
					$resp .= '</div>';

					$resp .= '<span>';
					$resp .= '<label>' . __( 'Total Price', 'service-booking' ) . ':</label>';
					$resp .= '<div class="product-total-price">' . $product['total'] . '</div>';
					$resp .= '</span>';
					$resp .= '<button class="delete-btn">Delete</button>';

					$resp .= '</li>';
					$resp .= '</ul>';
					$resp .= '</div>';

					++$i;
				} //end foreach
			} //end if
		} //end if

		return $resp;
	}//end bm_fetch_ordered_service_details()


	/**
	 * Fetch Service time slot details for backend
	 *
	 * @author Darpan
	 */
	public function bm_fetch_backend_new_order_time_slot_by_service_id( $data = array() ) {
		$dbhandler            = new BM_DBhandler();
		$timezone             = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$slot_format          = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
		$now                  = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$slot_value           = array();

		if ( ! empty( $data ) ) {
			$service_id      = isset( $data['id'] ) ? $data['id'] : 0;
			$date            = isset( $data['date'] ) ? $data['date'] : '';
			$current_date    = $now->format( 'Y-m-d' );
			$current_time    = $now->format( 'H:i' );
			$currentDateTime = $current_date . ' ' . $current_time;

			if ( $service_id > 0 && ! empty( $date ) ) {
				$service   = $dbhandler->get_row( 'SERVICE', $service_id );
				$time_row  = $dbhandler->get_row( 'TIME', $service_id );
				$stopsales = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );

				if ( ! empty( $stopsales ) ) {
					$stopSalesHours   = floor( $stopsales );
					$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

					if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $date ) ) {
						$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
					} else {
						$endDateTime = clone $now;
					}

					$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
					$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
				}

				if ( ! empty( $time_row ) ) {
					$total_slots         = isset( $time_row->total_slots ) ? $time_row->total_slots : 0;
					$time_slots          = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
					$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
					$dates               = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();

					if ( ! empty( $variable_time_slots ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
						$index     = (int) array_search( $date, $dates );
						$slot_data = isset( $variable_time_slots[ $index ] ) ? $variable_time_slots[ $index ] : array();

						if ( ! empty( $slot_data ) ) {
							if ( isset( $slot_data['total_slots'] ) && isset( $slot_data['max_cap'] ) && isset( $slot_data['from'] ) ) {
								for ( $i = 1; $i <= $slot_data['total_slots']; $i++ ) {
									$is_slot_disabled = isset( $slot_data['disable'][ $i ] ) ? $slot_data['disable'][ $i ] : 0;

									if ( $is_slot_disabled != 1 ) {
										$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date, $slot_data['max_cap'][ $i ], 1 );

										$startSlot = new DateTime( $date . ' ' . $slot_data['from'][ $i ], new DateTimeZone( $timezone ) );
										$startSlot = $startSlot->format( 'Y-m-d H:i' );

										if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
											continue;
										} elseif ( $capacity_left <= 0 ) {
											continue;
										} elseif ( ( empty( $stopsales ) && ( strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ) ) {
											continue;
										}

										if ( $global_show_to_slots == 0 ) {
											if ( $slot_format == '12' ) {
												$slot_value[ $i ] = $this->bm_am_pm_format( $slot_data['from'][ $i ] );
											} else {
												$slot_value[ $i ] = $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] );
											}
										} else {
											$is_slot_hidden = isset( $slot_data['hide_to_slot'][ $i ] ) ? $slot_data['hide_to_slot'][ $i ] : 0;

											if ( $is_slot_hidden != 1 ) {
												if ( $slot_format == '12' ) {
													$slot_value[ $i ] = $this->bm_am_pm_format( $slot_data['from'][ $i ] ) . ' - ' . $this->bm_am_pm_format( $slot_data['to'][ $i ] );
												} else {
													$slot_value[ $i ] = $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) . ' - ' . $this->bm_twenty_fourhrs_format( $slot_data['to'][ $i ] );
												}
											} elseif ( $is_slot_hidden == 1 ) {
												if ( $slot_format == '12' ) {
													$slot_value[ $i ] = $this->bm_am_pm_format( $slot_data['from'][ $i ] );
												} else {
													$slot_value[ $i ] = $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] );
												}
											}
										}
									}
								}
							}
						}
					} elseif ( ! empty( $time_slots ) ) {
						if ( isset( $time_slots['max_cap'] ) && isset( $time_slots['from'] ) ) {
							for ( $i = 1; $i <= $total_slots; $i++ ) {
								$is_slot_disabled = isset( $time_slots['disable'][ $i ] ) ? $time_slots['disable'][ $i ] : 0;

								if ( $is_slot_disabled != 1 ) {
									$capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $time_slots['from'][ $i ], $date, $time_slots['max_cap'][ $i ], 0 );

									$startSlot = new DateTime( $date . ' ' . $time_slots['from'][ $i ], new DateTimeZone( $timezone ) );
									$startSlot = $startSlot->format( 'Y-m-d H:i' );

									if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
										continue;
									} elseif ( $capacity_left <= 0 ) {
										continue;
									} elseif ( ( empty( $stopsales ) && ( strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ) ) {
										continue;
									}

									if ( $global_show_to_slots == 0 ) {
										if ( $slot_format == '12' ) {
											$slot_value[ $i ] = $this->bm_am_pm_format( $time_slots['from'][ $i ] );
										} else {
											$slot_value[ $i ] = $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] );
										}
									} else {
										$is_slot_hidden = isset( $time_slots['hide_to_slot'][ $i ] ) ? $time_slots['hide_to_slot'][ $i ] : 0;

										if ( $is_slot_hidden != 1 ) {
											if ( $slot_format == '12' ) {
												$slot_value[ $i ] = $this->bm_am_pm_format( $time_slots['from'][ $i ] ) . ' - ' . $this->bm_am_pm_format( $time_slots['to'][ $i ] );
											} else {
												$slot_value[ $i ] = $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) . ' - ' . $this->bm_twenty_fourhrs_format( $time_slots['to'][ $i ] );
											}
										} elseif ( $is_slot_hidden == 1 ) {
											if ( $slot_format == '12' ) {
												$slot_value[ $i ] = $this->bm_am_pm_format( $time_slots['from'][ $i ] );
											} else {
												$slot_value[ $i ] = $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] );
											}
										}
									}
								}
							}
						}
					} //end if
				} //end if
			}
		} //end if

		return $slot_value;
	}//end bm_fetch_backend_new_order_time_slot_by_service_id()


	/**
	 * Fetch slots of service with single time slot by Service id and date
	 *
	 * @author Darpan
	 */
	public function bm_fetch_backend_new_order_single_time_slot_by_service_id( $service_id = '', $date = '' ) {
		$dbhandler            = new BM_DBhandler();
		$timezone             = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$slot_format          = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
		$time_slot            = array();
		$now                  = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_date         = $now->format( 'Y-m-d' );
		$current_time         = $now->format( 'H:i' );
		$is_variable_slot     = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );
		$currentDateTime      = $current_date . ' ' . $current_time;

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service   = $dbhandler->get_row( 'SERVICE', $service_id );
			$time_row  = $dbhandler->get_row( 'TIME', $service_id );
			$stopsales = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );

			if ( ! empty( $stopsales ) ) {
				$stopSalesHours   = floor( $stopsales );
				$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

				if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $date ) ) {
					$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
				} else {
					$endDateTime = clone $now;
				}

				$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
				$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
			}
		}

		if ( $is_variable_slot == 1 ) {
			if ( ! empty( $date ) && ! empty( $service ) ) {
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				$dates               = ! empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();

				if ( ! empty( $variable_time_slots ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
					$index     = (int) array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];

					if ( isset( $slot_data['disable'][1] ) && $slot_data['disable'][1] != 1 ) {
						$slot_max_cap = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );
						$slot_max_cap = $slot_max_cap == 0 ? $slot_data['max_cap'][1] : $slot_max_cap;

						$capacity_left = $this->bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id( $service_id, 1, $slot_data['from'][1], $date, $slot_max_cap, 1 );

						$startSlot = new DateTime( $date . ' ' . $slot_data['from'][1], new DateTimeZone( $timezone ) );
						$startSlot = $startSlot->format( 'Y-m-d H:i' );

						if ( empty( $stopsales ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
							if ( $capacity_left > 0 ) {
								if ( $global_show_to_slots == 0 ) {
									if ( $slot_format == '12' ) {
										$time_slot[] = $this->bm_am_pm_format( $slot_data['from'][1] );
									} else {
										$time_slot[] = $this->bm_twenty_fourhrs_format( $slot_data['from'][1] );
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] != 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot[] = $this->bm_am_pm_format( $slot_data['from'][1] ) . ' - ' . $this->bm_am_pm_format( $slot_data['to'][1] );
									} else {
										$time_slot[] = $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) . ' - ' . $this->bm_twenty_fourhrs_format( $slot_data['to'][1] );
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] == 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot[] = $this->bm_am_pm_format( $slot_data['from'][1] );
									} else {
										$time_slot[] = $this->bm_twenty_fourhrs_format( $slot_data['from'][1] );
									}
								}
							} elseif ( $capacity_left <= 0 ) {
								return;
							}
						} elseif ( empty( $stopsales ) && strtotime( $currentDateTime ) > strtotime( $startSlot ) && ( $capacity_left > 0 ) ) {
							return;
						} elseif ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) ) {
							if ( $capacity_left > 0 ) {
								if ( $global_show_to_slots == 0 ) {
									if ( $slot_format == '12' ) {
										$time_slot[] = $this->bm_am_pm_format( $slot_data['from'][1] );
									} else {
										$time_slot[] = $this->bm_twenty_fourhrs_format( $slot_data['from'][1] );
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] != 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot[] = $this->bm_am_pm_format( $slot_data['from'][1] ) . ' - ' . $this->bm_am_pm_format( $slot_data['to'][1] );
									} else {
										$time_slot[] = $this->bm_twenty_fourhrs_format( $slot_data['from'][1] ) . ' - ' . $this->bm_twenty_fourhrs_format( $slot_data['to'][1] );
									}
								} elseif ( isset( $slot_data['hide_to_slot'][1] ) && $slot_data['hide_to_slot'][1] == 1 ) {
									if ( $slot_format == '12' ) {
										$time_slot[] = $this->bm_am_pm_format( $slot_data['from'][1] );
									} else {
										$time_slot[] = $this->bm_twenty_fourhrs_format( $slot_data['from'][1] );
									}
								}
							} elseif ( $capacity_left <= 0 ) {
								return;
							}
						} elseif ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
							return;
						} //end if
					} //end if
				} //end if
			} //end if
		} elseif ( $is_variable_slot == 0 ) {
			if ( ! empty( $date ) && ! empty( $time_row ) ) {
				$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

				if ( ! empty( $time_slots ) && isset( $time_slots['disable'][1] ) && $time_slots['disable'][1] != 1 ) {
					$slot_max_cap = $this->bm_fetch_service_with_single_slot_variable_max_cap_by_service_id_and_date( $service_id, $date );
					$slot_max_cap = $slot_max_cap == 0 ? $time_slots['max_cap'][1] : $slot_max_cap;

					$capacity_left = $this->bm_fetch_service_with_single_slot_capacity_by_service_and_slot_id( $service_id, 1, $time_slots['from'][1], $date, $slot_max_cap, 0 );

					$startSlot = new DateTime( $date . ' ' . $time_slots['from'][1], new DateTimeZone( $timezone ) );
					$startSlot = $startSlot->format( 'Y-m-d H:i' );

					if ( empty( $stopsales ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
						if ( $capacity_left > 0 ) {
							if ( $global_show_to_slots == 0 ) {
								if ( $slot_format == '12' ) {
									$time_slot[] = $this->bm_am_pm_format( $time_slots['from'][1] );
								} else {
									$time_slot[] = $this->bm_twenty_fourhrs_format( $time_slots['from'][1] );
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] != 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot[] = $this->bm_am_pm_format( $time_slots['from'][1] ) . ' - ' . $this->bm_am_pm_format( $time_slots['to'][1] );
								} else {
									$time_slot[] = $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) . ' - ' . $this->bm_twenty_fourhrs_format( $time_slots['to'][1] );
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] == 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot[] = $this->bm_am_pm_format( $time_slots['from'][1] );
								} else {
									$time_slot[] = $this->bm_twenty_fourhrs_format( $time_slots['from'][1] );
								}
							}
						} elseif ( $capacity_left <= 0 ) {
							return;
						}
					} elseif ( empty( $stopsales ) && strtotime( $currentDateTime ) > strtotime( $startSlot ) && ( $capacity_left > 0 ) ) {
						return;
					} elseif ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) ) {
						if ( $capacity_left > 0 ) {
							if ( $global_show_to_slots == 0 ) {
								if ( $slot_format == '12' ) {
									$time_slot[] = $this->bm_am_pm_format( $time_slots['from'][1] );
								} else {
									$time_slot[] = $this->bm_twenty_fourhrs_format( $time_slots['from'][1] );
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] != 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot[] = $this->bm_am_pm_format( $time_slots['from'][1] ) . ' - ' . $this->bm_am_pm_format( $time_slots['to'][1] );
								} else {
									$time_slot[] = $this->bm_twenty_fourhrs_format( $time_slots['from'][1] ) . ' - ' . $this->bm_twenty_fourhrs_format( $time_slots['to'][1] );
								}
							} elseif ( isset( $time_slots['hide_to_slot'][1] ) && $time_slots['hide_to_slot'][1] == 1 ) {
								if ( $slot_format == '12' ) {
									$time_slot[] = $this->bm_am_pm_format( $time_slots['from'][1] );
								} else {
									$time_slot[] = $this->bm_twenty_fourhrs_format( $time_slots['from'][1] );
								}
							}
						} elseif ( $capacity_left <= 0 ) {
							return;
						}
					} elseif ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
						return;
					} //end if
				} //end if
			} //end if
		} //end if

		return $time_slot;
	}//end bm_fetch_backend_new_order_single_time_slot_by_service_id()


	/**
	 * Fetch Service price for backend
	 *
	 * @author Darpan
	 */
	public function bm_fetch_new_order_service_price_by_service_id_and_date( $service_id = '', $date = '' ) {
		$dbhandler = new BM_DBhandler();
		$price     = 0;

		if ( isset( $service_id ) && ! empty( $service_id ) ) {
			$service = $dbhandler->get_row( 'SERVICE', $service_id );
		}

		if ( ! empty( $date ) && isset( $service ) && ! empty( $service ) ) {
			$price               = isset( $service->default_price ) ? $service->default_price : 0;
			$variable_svc_prices = isset( $service->variable_svc_prices ) ? maybe_unserialize( $service->variable_svc_prices ) : array();

			if ( ! empty( $variable_svc_prices ) && isset( $variable_svc_prices['date'] ) && isset( $variable_svc_prices['price'] ) ) {
				$date_count = count( $variable_svc_prices['date'] );
				if ( $date_count == count( $variable_svc_prices['price'] ) ) {
					for ( $i = 1; $i <= $date_count; $i++ ) {
						if ( in_array( $date, $variable_svc_prices['date'], true ) ) {
							$index = (int) array_search( $date, $variable_svc_prices['date'] );
							$price = $variable_svc_prices['price'][ $index ];
						}
					}
				}
			}
		}

		return $price;
	}//end bm_fetch_new_order_service_price_by_service_id_and_date()


	/**
	 * Fetch extra service response
	 *
	 * @author Darpan
	 */
	public function bm_fetch_backend_new_order_extra_services( $data = array() ) {
		$dbhandler     = new BM_DBhandler();
		$global_extras = $dbhandler->get_all_result( 'EXTRA', '*', array( 'is_global' => 1 ), 'results' );
		$extras        = array();

		if ( ! empty( $data ) ) {
			$service_id = isset( $data['id'] ) ? $data['id'] : 0;
			$date       = isset( $data['date'] ) ? $data['date'] : '';

			if ( ! empty( $service_id ) && ! empty( $date ) ) {
				if ( isset( $service_id ) && ! empty( $service_id ) ) {
					$extra_rows = $dbhandler->get_all_result( 'EXTRA', '*', array( 'service_id' => $service_id ), 'results' );

					if ( ! empty( $extra_rows ) && ! empty( $global_extras ) ) {
						$total_extra_rows = array_merge( $global_extras, $extra_rows );
					} elseif ( empty( $extra_rows ) && ! empty( $global_extras ) ) {
						$total_extra_rows = $global_extras;
					} elseif ( ! empty( $extra_rows ) && empty( $global_extras ) ) {
						$total_extra_rows = $extra_rows;
					}

					if ( isset( $total_extra_rows ) && ! empty( $total_extra_rows ) ) {
						foreach ( $total_extra_rows as $key => $extra_service ) {
							$cap_left = $this->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date( $extra_service->id, $extra_service->extra_max_cap, 0, $date );

							if ( $cap_left > 0 ) {
								$extras[ $key ]           = $extra_service;
								$extras[ $key ]->cap_left = $cap_left;
							}
						}

						$extras = array_values( $extras );
					}
				} //end if
			} //end if
		} //end if

		return $extras;
	}//end bm_fetch_backend_new_order_extra_services()


	/**
	 * Check if service has hidden to slot
	 *
	 * @author Darpan
	 */
	public function bm_check_if_hidden_to_slot( $service_id = '', $date = '', $from = '00:00' ) {
		$dbhandler            = new BM_DBhandler();
		$is_hidden            = 0;
		$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );

		if ( $global_show_to_slots == 0 ) {
			return $global_show_to_slots;
		}

		if ( ! empty( $service_id ) && ! empty( $date ) && ! empty( $from ) ) {
			$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );

			if ( $is_variable_slot == 1 ) {
				$service             = $dbhandler->get_row( 'SERVICE', $service_id );
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				if ( ! empty( $variable_time_slots ) ) {
					$dates = wp_list_pluck( $variable_time_slots, 'date' );
				}

				if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
					$index     = (int) array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];

					if ( ! empty( $slot_data ) && ! empty( $slot_data['from'] ) && in_array( $from, $slot_data['from'], true ) ) {
						$slot_id   = (int) array_search( $from, $slot_data['from'] );
						$is_hidden = $slot_data['hide_to_slot'][ $slot_id ];
					}
				}
			} elseif ( $is_variable_slot == 0 ) {
				$time_row = $dbhandler->get_row( 'TIME', $service_id );

				if ( ! empty( $time_row ) ) {
					$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

					if ( ! empty( $time_slots ) && isset( $time_slots['from'] ) && in_array( $from, $time_slots['from'], true ) ) {
						$slot_id   = (int) array_search( $from, $time_slots['from'] );
						$is_hidden = $time_slots['hide_to_slot'][ $slot_id ];
					}
				}
			} //end if
		} //end if

		return $is_hidden;
	}//end bm_check_if_hidden_to_slot()


	/**
	 * Check if service has disabled slot
	 *
	 * @author Darpan
	 */
	public function bm_check_if_disabled_slot( $service_id = '', $date = '', $from = '00:00' ) {
		$dbhandler            = new BM_DBhandler();
		$is_disabled          = 0;
		$global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );

		if ( $global_show_to_slots == 0 ) {
			return $global_show_to_slots;
		}

		if ( ! empty( $service_id ) && ! empty( $date ) && ! empty( $from ) ) {
			$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );

			if ( $is_variable_slot == 1 ) {
				$service             = $dbhandler->get_row( 'SERVICE', $service_id );
				$variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
				if ( ! empty( $variable_time_slots ) ) {
					$dates = wp_list_pluck( $variable_time_slots, 'date' );
				}

				if ( isset( $dates ) && ! empty( $dates ) && in_array( $date, $dates, true ) ) {
					$index     = (int) array_search( $date, $dates );
					$slot_data = $variable_time_slots[ $index ];

					if ( ! empty( $slot_data ) && ! empty( $slot_data['from'] ) && in_array( $from, $slot_data['from'], true ) ) {
						$slot_id     = (int) array_search( $from, $slot_data['from'] );
						$is_disabled = $slot_data['disable'][ $slot_id ];
					}
				}
			} elseif ( $is_variable_slot == 0 ) {
				$time_row = $dbhandler->get_row( 'TIME', $service_id );

				if ( ! empty( $time_row ) ) {
					$time_slots = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();

					if ( ! empty( $time_slots ) && isset( $time_slots['from'] ) && in_array( $from, $time_slots['from'], true ) ) {
						$slot_id     = (int) array_search( $from, $time_slots['from'] );
						$is_disabled = $time_slots['disable'][ $slot_id ];
					}
				}
			} //end if
		} //end if

		return $is_disabled;
	}//end bm_check_if_disabled_slot()


	/**
	 * Fetch service by category shortcode html content
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_by_category_shortcode_html_content( $service_content, $category_ids, $counter, $resp = '' ) {
		$plugin_path = plugin_dir_url( __DIR__ );

		$resp .= '<div class="pagewrapper service-by-catrgory" id="' . $counter . '">';
		$resp .= '<div class="searchpage">';
		$resp .= '<div class="rightbar fullbar">';
		$resp .= '<div class="tabberbox">';
		$resp .= '<input type="hidden" id="current_service_id">';
		$resp .= '<input type="hidden" id="booking_date2">';
		$resp .= '<input type="hidden" id="selected_slot">';
		$resp .= '<input type="hidden" id="selected_extra_service_ids">';
		$resp .= '<input type="hidden" id="total_service_booking">';
		$resp .= '<input type="hidden" id="no_of_persons">';
		$resp .= '<input type="hidden" id="service_id_for_checkout">';
		$resp .= '<div class="top-heading-bar">';
		$resp .= '<div class="search-box">';
		$resp .= '<h2 class="sliderheading" style="margin-top: -10px; font-size: 42px;">' . __( 'Search Results', 'service-booking' ) . '</h2>';
		$resp .= '</div>';
		$resp .= '<div class="tab-box">';
		$resp .= '<div class="inputgroup">';
		$resp .= '<input type="text" class="textbox searchtextbox" id="search_service_by_name" placeholder="' . __( 'Search service by name...', 'service-booking' ) . '" />';
		$resp .= '<input type="hidden" id="service_categories" value="' . $category_ids . '"/>';
		$resp .= '<input type="hidden" id="counter" value="' . $counter . '"/>';
		$resp .= '<i class="fa fa-search"></i>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '<div id="category_search_tab_nav_' . $counter . '" class="svc_by_cat_class">';
		$resp .= '<ul>';
		$resp .= '<li><a href="#" class="card-section-icon"><img src="' . esc_url( $plugin_path . 'public/img/et_grid.svg' ) . '"/></a></li>';
		$resp .= '<li class="selected">';
		$resp .= '<a href="#" class="card-section-icon">';
		$resp .= '<img src="' . esc_url( $plugin_path . 'public/img/slider-icon.svg' ) . '"/>';
		$resp .= '</a>';
		$resp .= '</li>';
		$resp .= '</ul>';
		$resp .= '<div class="tabcontent" style="display:block;">';
		$resp .= '<div class="services-searchresult-box service_by_category_gridview">';
		$resp .= wp_kses_post( $service_content );
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '<div class="tabcontent">';
		$resp .= '<div class="searchresultbar service_by_category_gridview slider1">';
		$resp .= wp_kses_post( $service_content );
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';

		$resp .= '<div id="slot_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . apply_filters( 'global_service_shortcode_slot_modal_heading', __( 'Date and Slot Details', 'service-booking' ) ) . '</h4>';
		$resp .= '<div class="modalcontentbox3 slot_box_modal modal-body" id="calendar_and_slot_details"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="user_form_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<div class="modalcontentbox2 modal-body" id="user_form"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="booking_detail_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Booking Details', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="booking_detail"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="extra_service_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__fadeIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Select Extra Sevice', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="extra_service_details"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="checkout_options_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Select Checkout Type', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="checkout_options_html"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="service_gallery_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Gallery Images', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox5 modal-body" id="service_gallery_images_html"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		return wp_kses( $resp, $this->bm_fetch_expanded_allowed_tags() );
	}//end bm_fetch_service_by_category_shortcode_html_content()


	/**
	 * Fetch single service shortcode html content
	 *
	 * @author Darpan
	 */
	public function bm_fetch_single_service_shortcode_html_content( $service_content, $resp = '' ) {
		$resp .= '<div class="pagewrapper service-by-id">';
		$resp .= '<div class="svc_by_id_searchpage">';
		$resp .= '<div class="rightbar fullbar">';
		$resp .= '<div class="tabberbox">';
		$resp .= '<input type="hidden" id="current_service_id">';
		$resp .= '<input type="hidden" id="booking_date2">';
		$resp .= '<input type="hidden" id="selected_slot">';
		$resp .= '<input type="hidden" id="selected_extra_service_ids">';
		$resp .= '<input type="hidden" id="total_service_booking">';
		$resp .= '<input type="hidden" id="no_of_persons">';
		$resp .= '<input type="hidden" id="service_id_for_checkout">';
		$resp .= '<div id="service_by_id">';
		$resp .= wp_kses_post( $service_content );
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';

		$resp .= '<div id="slot_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . apply_filters( 'global_service_shortcode_slot_modal_heading', __( 'Date and Slot Details', 'service-booking' ) ) . '</h4>';
		$resp .= '<div class="modalcontentbox3 slot_box_modal modal-body" id="calendar_and_slot_details"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="user_form_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<div class="modalcontentbox2 modal-body" id="user_form"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="booking_detail_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Booking Details', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="booking_detail"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="extra_service_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__fadeIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Select Extra Sevice', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="extra_service_details"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="checkout_options_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Select Checkout Type', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="checkout_options_html"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="service_gallery_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Gallery Images', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox5 modal-body" id="service_gallery_images_html"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		return wp_kses( $resp, $this->bm_fetch_expanded_allowed_tags() );
	}//end bm_fetch_single_service_shortcode_html_content()


	/**
	 * Fetch single service calendar shortcode html content
	 *
	 * @author Darpan
	 */
	public function bm_fetch_single_service_calendar_shortcode_html_content( $service_content ) {
		$resp  = '<div class="pagewrapper">';
		$resp .= '<div class="svc_by_id_searchpage">';
		$resp .= '<div class="rightbar fullbar">';
		$resp .= '<div class="tabberbox">';
		$resp .= '<input type="hidden" id="current_service_id">';
		$resp .= '<input type="hidden" id="selected_slot">';
		$resp .= '<input type="hidden" id="selected_extra_service_ids">';
		$resp .= '<input type="hidden" id="total_service_booking">';
		$resp .= '<input type="hidden" id="no_of_persons">';
		$resp .= '<input type="hidden" id="service_id_for_checkout">';
		$resp .= '<div>';
		$resp .= wp_kses_post( $service_content );
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';
		$resp .= '</div>';

		$resp .= '<div id="time_slot_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . apply_filters( 'global_service_shortcode_slot_modal_heading', __( 'Slot Details', 'service-booking' ) ) . '</h4>';
		$resp .= '<div class="modalcontentbox3 slot_box_modal modal-body" id="slot_details"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="booking_detail_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Booking Details', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="booking_detail"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="extra_service_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__fadeIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Select Extra Sevice', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="extra_service_details"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="checkout_options_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Select Checkout Type', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox modal-body" id="checkout_options_html"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		$resp .= '<div id="service_gallery_modal" class="modaloverlay">';
		$resp .= '<div class="modal animate__animated animate__bounceIn">';
		$resp .= '<span class="close" id="close_modal">&times;</span>';
		$resp .= '<h4>' . __( 'Gallery Images', 'service-booking' ) . '</h4>';
		$resp .= '<div class="modalcontentbox5 modal-body" id="service_gallery_images_html"></div>';
		$resp .= '<div class="loader_modal"><div class="checkout-spinner-box"><div class="checkout-spinner"></div><p>' . __( 'Loading...', 'service-booking' ) . '</p></div></div>';
		$resp .= '</div></div>';

		return wp_kses( $resp, $this->bm_fetch_expanded_allowed_tags() );
	}//end bm_fetch_single_service_calendar_shortcode_html_content()


	/**
	 * Fetch saved serach data for different modules
	 *
	 * @author Darpan
	 */
	public function bm_fetch_last_saved_search_data( $module, $is_admin ) {
		$dbhandler = new BM_DBhandler();
		$user_id   = get_current_user_id();
		$result    = $dbhandler->get_all_result(
			'SAVESEARCH',
			'search_data',
			array(
				'user_id'  => $user_id,
				'module'   => $module,
				'is_admin' => $is_admin,
			),
			'var',
			0,
			1,
			'id',
			'DESC'
		);
		$result    = maybe_unserialize( $result );
		return $result;
	}//end bm_fetch_last_saved_search_data()


	/**
	 * Fetch service name by service id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booked_service_duration( $order_id ) {
		$dbhandler        = new BM_DBhandler();
		$service_duration = '0.0';
		$service_id       = 0;

		if ( ! empty( $order_id ) ) {
			if ( is_string( $order_id ) ) {
				$booking_data = $dbhandler->get_value( 'FAILED_TRANSACTIONS', 'booking_data', $order_id, 'booking_key' );
				$booking_data = ! empty( $booking_data ) ? maybe_unserialize( $booking_data ) : array();
				$service_id   = isset( $booking_data['service_id'] ) ? $booking_data['service_id'] : 0;
			} else {
				$service_id = $dbhandler->get_value( 'BOOKING', 'service_id', $order_id, 'id' );
			}

			if ( ! empty( $service_id ) ) {
				$service_duration = $dbhandler->get_value( 'SERVICE', 'service_duration', $service_id, 'id' );
			}
		} //end if

		return $service_duration;
	}//end bm_fetch_booked_service_duration()


	/**
	 * Fetch service name by service id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_name_by_service_id( $service_id, $type = 'array' ) {
		$dbhandler    = new BM_DBhandler();
		$service_name = is_array( $service_id ) ? array() : '';

		if ( ! empty( $service_id ) ) {
			if ( is_array( $service_id ) ) {
				$service_ids = implode( ',', $service_id );
				$additional  = "id in ($service_ids)";
				$service     = $dbhandler->get_all_result( 'SERVICE', '*', 1, 'results', 0, false, 'service_position', 'DESC', $additional );
			} else {
				$service = $dbhandler->get_row( 'SERVICE', $service_id );
			}

			if ( isset( $service ) && ! empty( $service ) ) {
				if ( is_array( $service ) ) {
					foreach ( $service as $result ) {
						$service_name[] = $result->service_name;
					}
				} else {
					$service_name = $service->service_name;
				}
			}

			if ( ! empty( $service_name ) && is_array( $service_name ) && $type == 'string' ) {
				$service_name = implode( ',', $service_name );
			}
		} //end if

		return $service_name;
	}//end bm_fetch_service_name_by_service_id()


	/**
	 * Fetch service id by category id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_service_id_by_category_id( $category_id, $type = 'array' ) {
		$dbhandler   = new BM_DBhandler();
		$service_ids = array();

		if ( isset( $category_id ) ) {
			if ( is_array( $category_id ) ) {
				$category_ids = implode( ',', $category_id );
				$additional   = "service_category in ($category_ids)";
				$services     = $dbhandler->get_all_result( 'SERVICE', '*', 1, 'results', 0, false, 'service_position', 'DESC', $additional );
			} else {
				$services = $dbhandler->get_all_result( 'SERVICE', '*', array( 'service_category' => $category_id ), 'results', 0, false, 'service_position', 'DESC' );
			}

			if ( isset( $services ) && ! empty( $services ) ) {
				if ( is_array( $services ) ) {
					foreach ( $services as $service ) {
						$service_ids[] = $service->id;
					}
				}
			}

			if ( ! empty( $service_ids ) && is_array( $service_ids ) && $type == 'string' ) {
				$service_ids = implode( ',', $service_ids );
			}
		} //end if

		return $service_ids;
	}//end bm_fetch_service_id_by_category_id()


	/**
	 * Get payment statuses as per status type
	 *
	 * @author Darpan
	 */
	public function bm_get_payment_status_condition( $status = '' ) {
		if ( $status === 'pending' ) {
			return array( 't.payment_status' => array( 'IN' => array( 'requires_capture', 'pending', 'on_hold' ) ) );
		}
		return array( 't.payment_status' => array( 'NOT IN' => array( 'requires_capture', 'pending', 'on_hold' ) ) );
	}//end bm_get_payment_status_condition()


	/**
	 * Get order statuses as per status type
	 *
	 * @author Darpan
	 */
	public function bm_get_order_status_condition( $status = '' ) {
		if ( $status === 'pending' ) {
			return array( 'b.order_status' => array( 'IN' => array( 'processing', 'pending', 'on_hold' ) ) );
		}
		return array( 'b.order_status' => array( 'NOT IN' => array( 'processing', 'pending', 'on_hold' ) ) );
	}//end bm_get_order_status_condition()


	/**
	 * Get current year dates
	 *
	 * @author Darpan
	 */
	public function bm_get_year_date_range( $year = null ) {
		$timezone     = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$date         = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_year = $date->format( 'Y' );

		if ( $year != null ) {
			$current_year = $year;
		}

		return array(
			'start_date' => "{$current_year}-01-01",
			'end_date'   => "{$current_year}-12-31",
		);
	}//end bm_get_year_date_range()


	/**
	 * Get current month dates
	 *
	 * @author Darpan
	 */
	public function bm_get_month_date_range( $year = null, $month = null ) {
		$timezone      = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$date          = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$current_year  = $date->format( 'Y' );
		$current_month = $date->format( 'm' );

		if ( $year != null && $month != null ) {
			$date          = new DateTime( "$year-$month-01", new DateTimeZone( $timezone ) );
			$current_year  = $year;
			$current_month = $month;
			$date->modify( 'last day of this month' );
		}

		return array(
			'start_date' => "{$current_year}-{$current_month}-01",
			'end_date'   => $year != null && $month != null ? $date->format( 'Y-m-d' ) : $date->format( 'Y-m-t' ),
		);
	}//end bm_get_month_date_range()


	/**
	 * Get current week dates
	 *
	 * @author Darpan
	 */
	public function bm_get_current_week_dates() {
		$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$date     = new DateTime( 'now', new DateTimeZone( $timezone ) );

		$week_start_date = clone $date;
		$week_start_date->modify( 'this week' )->setTime( 0, 0, 0 );

		$week_end_date = clone $week_start_date;
		$week_end_date->modify( '+6 days' )->setTime( 23, 59, 59 );

		$start_date = $week_start_date->format( 'Y-m-d' );
		$end_date   = $week_end_date->format( 'Y-m-d' );

		return array(
			'start_date' => $start_date,
			'end_date'   => $end_date,
		);
	}//end bm_get_current_week_dates()


	/**
	 * Get last week dates
	 *
	 * @author Darpan
	 */
	public function bm_get_last_week_dates() {
		$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$date     = new DateTime( 'now', new DateTimeZone( $timezone ) );

		$week_start_date = clone $date;
		$week_start_date->modify( 'last week' )->setTime( 0, 0, 0 );

		$week_end_date = clone $week_start_date;
		$week_end_date->modify( '+6 days' )->setTime( 23, 59, 59 );

		$start_date = $week_start_date->format( 'Y-m-d' );
		$end_date   = $week_end_date->format( 'Y-m-d' );

		return array(
			'start_date' => $start_date,
			'end_date'   => $end_date,
		);
	}//end bm_get_last_week_dates()


	/**
	 * Fetch total customers count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_customers_count( $type = '' ) {
		$where = $this->bm_get_payment_status_condition();
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		if ( $type === 'this_week' ) {
			$current_week_dates     = $this->bm_get_current_week_dates();
			$current_week_condition = array(
				'b.booking_date' => array(
					'>=' => $current_week_dates['start_date'] ?? '',
					'<=' => $current_week_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_week_condition );
		} elseif ( $type === 'last_week' ) {
			$last_week_dates     = $this->bm_get_last_week_dates();
			$last_week_condition = array(
				'b.booking_date' => array(
					'>=' => $last_week_dates['start_date'] ?? '',
					'<=' => $last_week_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $last_week_condition );
		}

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 't.booking_id = b.id',
				'type'  => 'INNER',
			),
		);

		$columns = 'DISTINCT(b.customer_id)';

		$additional = 'GROUP BY b.customer_id';

		$result = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results', 0, false, null, false, $additional );

		return ! empty( $result ) && is_array( $result ) ? count( $result ) : 0;
	}//end bm_fetch_total_customers_count()


	/**
	 * Fetch total bookings count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_bookings_count( $year = null, $month = null, $status = '' ) {
		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);

		$columns = 't.payment_status, b.order_status';
		$where   = $this->bm_get_order_status_condition( $status );

		if ( ! empty( $year ) && ! is_null( $year ) ) {
			$current_year_dates     = $this->bm_get_year_date_range( $year );
			$current_year_condition = array(
				'b.booking_date' => array(
					'>=' => $current_year_dates['start_date'] ?? '',
					'<=' => $current_year_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_year_condition );
		}

		if ( ! empty( $month ) && ! is_null( $month ) ) {
			$current_month_dates     = $this->bm_get_month_date_range( $year, $month );
			$current_month_condition = array(
				'b.booking_date' => array(
					'>=' => $current_month_dates['start_date'] ?? '',
					'<=' => $current_month_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_month_condition );
		}

		$result = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results' );
		return ! empty( $result ) && is_array( $result ) ? count( $result ) : 0;
	}//end bm_fetch_total_bookings_count()


	/**
	 * Fetch upcoming bookings count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_upcoming_bookings_count( $year = null, $month = null, $status = '' ) {
		$timezone = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$date     = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$today    = $date->format( 'Y-m-d' );

		$where = $this->bm_get_order_status_condition( $status );
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		if ( ! empty( $year ) && ! is_null( $year ) ) {
			$current_year_dates     = $this->bm_get_year_date_range( $year );
			$current_year_condition = array(
				'b.booking_date' => array(
					'>=' => $current_year_dates['start_date'] ?? '',
					'<=' => $current_year_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_year_condition );
		}

		if ( ! empty( $month ) && ! is_null( $month ) ) {
			$current_month_dates     = $this->bm_get_month_date_range( $year, $month );
			$current_month_condition = array(
				'b.booking_date' => array(
					'>=' => $current_month_dates['start_date'] ?? '',
					'<=' => $current_month_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_month_condition );
		}

		$where['b.booking_date']['>='] = "{$today}";

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);

		$columns = 't.payment_status, b.order_status, b.booking_date, b.booking_slots';

		$result = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results' );

		if ( ! empty( $result ) ) {
			$result = array_filter(
				$result,
				function ( $booking ) use ( $date ) {
					$service_date  = $booking->booking_date ?? '';
					$booking_slots = ! empty( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();
					$to_slot       = $booking_slots['to'] ?? '23:59';

					$service_datetime_str = "$service_date $to_slot:00";
					$service_datetime     = DateTime::createFromFormat( 'Y-m-d H:i:s', $service_datetime_str, $date->getTimezone() );

					return $service_datetime && $service_datetime > $date;
				}
			);
		}

		return ! empty( $result ) && is_array( $result ) ? count( $result ) : 0;
	}//end bm_fetch_upcoming_bookings_count()


	/**
	 * Fetch weekly bookings count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_weekly_bookings_count( $status = '' ) {
		$where = $this->bm_get_order_status_condition( $status );
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		$current_week_dates     = $this->bm_get_current_week_dates();
		$current_week_condition = array(
			'b.booking_date' => array(
				'>=' => $current_week_dates['start_date'] ?? '',
				'<=' => $current_week_dates['end_date'] ?? '',
			),
		);

		$where = array_merge( $where, $current_week_condition );

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);

		$columns = 't.payment_status, b.order_status';

		$result = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results' );
		return ! empty( $result ) && is_array( $result ) ? count( $result ) : 0;
	}//end bm_fetch_weekly_bookings_count()


	/**
	 * Fetch total bookings revenue
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_bookings_revenue( $year = null, $month = null, $status = '' ) {
		$where = $this->bm_get_order_status_condition( $status );
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		if ( ! empty( $year ) && ! is_null( $year ) ) {
			$current_year_dates     = $this->bm_get_year_date_range( $year );
			$current_year_condition = array(
				'b.booking_date' => array(
					'>=' => $current_year_dates['start_date'] ?? '',
					'<=' => $current_year_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_year_condition );
		}

		if ( ! empty( $month ) && ! is_null( $month ) ) {
			$current_month_dates     = $this->bm_get_month_date_range( $year, $month );
			$current_month_condition = array(
				'b.booking_date' => array(
					'>=' => $current_month_dates['start_date'] ?? '',
					'<=' => $current_month_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_month_condition );
		}

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);

		$columns = 't.payment_status, b.order_status, b.total_cost';

		$result = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results' );
		return ! empty( $result ) && is_array( $result ) ? array_sum( array_column( $result, 'total_cost' ) ) : 0;
	}//end bm_fetch_total_bookings_revenue()


	/**
	 * Fetch total slot bookings count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_total_slot_bookings_count( $type = '' ) {
		$where = $this->bm_get_order_status_condition();
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		if ( $type === 'this_week' ) {
			$current_week_dates     = $this->bm_get_current_week_dates();
			$current_week_condition = array(
				'b.booking_date' => array(
					'>=' => $current_week_dates['start_date'] ?? '',
					'<=' => $current_week_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $current_week_condition );
		} elseif ( $type === 'last_week' ) {
			$last_week_dates     = $this->bm_get_last_week_dates();
			$last_week_condition = array(
				'b.booking_date' => array(
					'>=' => $last_week_dates['start_date'] ?? '',
					'<=' => $last_week_dates['end_date'] ?? '',
				),
			);

			$where = array_merge( $where, $last_week_condition );
		}

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 't.booking_id = b.id',
				'type'  => 'INNER',
			),
		);

		$columns = 'b.id AS record_id, SUM(b.total_svc_slots) AS total_slots, SUM(b.total_ext_svc_slots) AS total_extra_slots';

		$additional = 'GROUP BY b.id, b.total_svc_slots';

		$results = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results', 0, false, null, false, $additional );

		$service_slots_count       = ! empty( $results ) ? array_sum( array_map( fn( $result ) => intval( $result->total_slots ?? 0 ), $results ) ) : 0;
		$extra_service_slots_count = ! empty( $results ) ? array_sum( array_map( fn( $result ) => intval( $result->total_extra_slots ?? 0 ), $results ) ) : 0;

		return $service_slots_count + $extra_service_slots_count;
	}//end bm_fetch_total_slot_bookings_count()


	/**
	 * Fetch booking status count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_status_count( $query_data = array() ) {
		$dbhandler = new BM_DBhandler();
		$type      = isset( $query_data['type'] ) ? $query_data['type'] : '';
		$status    = isset( $query_data['status'] ) ? $query_data['status'] : '';
		$from      = isset( $query_data['from'] ) ? $query_data['from'] : '';
		$to        = isset( $query_data['to'] ) ? $query_data['to'] : '';
		$data      = array();
		$dbhandler->update_global_option_value( 'bm_dashboard_status_search_type_field', $type );

		if ( ! empty( $status ) && ! empty( $from ) && ! empty( $to ) ) {
			$dbhandler->update_global_option_value( 'bm_dashboard_status_search_value_field', $status );
			$dbhandler->update_global_option_value( 'bm_dashboard_status_from_field', $from );
			$dbhandler->update_global_option_value( 'bm_dashboard_status_to_field', $to );

			$from = $this->bm_convert_date_format( $from, 'd/m/y', 'Y-m-d' );
			$to   = $this->bm_convert_date_format( $to, 'd/m/y', 'Y-m-d' );

			if ( $type == 'yearly' ) {
				$data = $this->bm_fetch_booking_status_count_yearly( $status, $from, $to );
			} elseif ( $type == 'monthly' ) {
				$data = $this->bm_fetch_booking_status_count_monthly( $status, $from, $to );
			}
		}

		return $data;
	}//end bm_fetch_booking_status_count()


	/**
	 * Fetch monthly booking status count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_status_count_monthly( $status, $from, $to ) {
		$dbhandler      = new BM_DBhandler();
		$current_year   = $this->bm_fetch_date_query( 'this_year' )[0]['year'];
		$short_year     = $this->bm_fetch_date_query( 'this_year_in_short' )[0]['year'];
		$data['labels'] = array(
			esc_html__( 'Jan', 'service-booking' ) . "'$short_year",
			esc_html__( 'Feb', 'service-booking' ) . "'$short_year",
			esc_html__( 'Mar', 'service-booking' ) . "'$short_year",
			esc_html__( 'Apr', 'service-booking' ) . "'$short_year",
			esc_html__( 'May', 'service-booking' ) . "'$short_year",
			esc_html__( 'June', 'service-booking' ) . "'$short_year",
			esc_html__( 'Jul', 'service-booking' ) . "'$short_year",
			esc_html__( 'Aug', 'service-booking' ) . "'$short_year",
			esc_html__( 'Sept', 'service-booking' ) . "'$short_year",
			esc_html__( 'Oct', 'service-booking' ) . "'$short_year",
			esc_html__( 'Nov', 'service-booking' ) . "'$short_year",
			esc_html__( 'Dec', 'service-booking' ) . "'$short_year",
		);
		$Jan            = 0;
		$Feb            = 0;
		$Mar            = 0;
		$Apr            = 0;
		$May            = 0;
		$June           = 0;
		$Jul            = 0;
		$Aug            = 0;
		$Sept           = 0;
		$Oct            = 0;
		$Nov            = 0;
		$Dec            = 0;
		$additional     = "order_status = '$status' AND booking_date BETWEEN '$from' AND '$to'";
		$total_bookings = $dbhandler->get_all_result( 'BOOKING', '*', 1, 'results', 0, false, 'booking_date', 'DESC', $additional );

		if ( ! empty( $total_bookings ) && is_array( $total_bookings ) ) {
			foreach ( $total_bookings as $booking ) {
				$service_date = isset( $booking->booking_date ) ? $booking->booking_date : '';
				if ( ! empty( $service_date ) ) {
					$service_date  = strtotime( $service_date );
					$service_month = gmdate( 'm', $service_date );
					$service_year  = gmdate( 'Y', $service_date );

					if ( $service_year == $current_year ) {
						switch ( $service_month ) {
							case '01':
								++$Jan;
								break;
							case '02':
								++$Feb;
								break;
							case '03':
								++$Mar;
								break;
							case '04':
								++$Apr;
								break;
							case '05':
								++$May;
								break;
							case '06':
								++$June;
								break;
							case '07':
								++$Jul;
								break;
							case '08':
								++$Aug;
								break;
							case '09':
								++$Sept;
								break;
							case '10':
								++$Oct;
								break;
							case '11':
								++$Nov;
								break;
							case '12':
								++$Dec;
								break;
							default:
								break;
						} //end switch
					} //end if
				} //end if
			} //end foreach
		} //end if

		$data['data'] = array(
			$Jan,
			$Feb,
			$Mar,
			$Apr,
			$May,
			$June,
			$Jul,
			$Aug,
			$Sept,
			$Oct,
			$Nov,
			$Dec,
		);

		return $data;
	}//end bm_fetch_booking_status_count_monthly()


	/**
	 * Fetch yearly booking status count
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_status_count_yearly( $status, $from, $to ) {
		$dbhandler      = new BM_DBhandler();
		$current_year   = $this->bm_fetch_date_query( 'this_year' )[0]['year'];
		$data['labels'] = array(
			( $current_year - 5 ),
			( $current_year - 4 ),
			( $current_year - 3 ),
			( $current_year - 2 ),
			( $current_year - 1 ),
			$current_year,
			( $current_year + 1 ),
			( $current_year + 2 ),
			( $current_year + 3 ),
			( $current_year + 4 ),
			( $current_year + 5 ),
			( $current_year + 6 ),
		);
		$year1          = 0;
		$year2          = 0;
		$year3          = 0;
		$year4          = 0;
		$year5          = 0;
		$year6          = 0;
		$year7          = 0;
		$year8          = 0;
		$year9          = 0;
		$year10         = 0;
		$year11         = 0;
		$year12         = 0;
		$additional     = "order_status = '$status' AND booking_date BETWEEN '$from' AND '$to'";
		$total_bookings = $dbhandler->get_all_result( 'BOOKING', '*', 1, 'results', 0, false, 'booking_date', 'DESC', $additional );

		if ( ! empty( $total_bookings ) && is_array( $total_bookings ) ) {
			foreach ( $total_bookings as $booking ) {
				$service_date = isset( $booking->booking_date ) ? $booking->booking_date : '';

				if ( ! empty( $service_date ) ) {
					$service_date = strtotime( $service_date );
					$service_year = gmdate( 'Y', $service_date );

					switch ( $service_year ) {
						case ( ( $current_year - 5 ) ):
							++$year1;
							break;
						case ( ( $current_year - 4 ) ):
							++$year2;
							break;
						case ( ( $current_year - 3 ) ):
							++$year3;
							break;
						case ( ( $current_year - 2 ) ):
							++$year4;
							break;
						case ( ( $current_year - 1 ) ):
							++$year5;
							break;
						case $current_year:
							++$year6;
							break;
						case ( ( $current_year + 1 ) ):
							++$year7;
							break;
						case ( ( $current_year + 2 ) ):
							++$year8;
							break;
						case ( ( $current_year + 3 ) ):
							++$year9;
							break;
						case ( ( $current_year + 4 ) ):
							++$year10;
							break;
						case ( ( $current_year + 5 ) ):
							++$year11;
							break;
						case ( ( $current_year + 6 ) ):
							++$year12;
							break;
						default:
							break;
					} //end switch
				} //end if
			} //end foreach
		} //end if

		$data['data'] = array(
			$year1,
			$year2,
			$year3,
			$year4,
			$year5,
			$year6,
			$year7,
			$year8,
			$year9,
			$year10,
			$year11,
			$year12,
		);

		return $data;
	}//end bm_fetch_booking_status_count_yearly()


	/**
	 * Fetch bookings overview data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_overview_data() {
		$dbhandler     = new BM_DBhandler();
		$timezone      = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now           = new DateTime( 'now', new DateTimeZone( $timezone ) );
		$today         = $now->format( 'Y-m-d' );
		$bookings      = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results' );
		$resp          = '';
		$ordered_dates = $this->bm_fetch_last_n_days( 7 );

		if ( ! empty( $bookings ) && is_array( $bookings ) ) {
			foreach ( $bookings as $key => $booking ) {
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking->id, 'booking_id' );

				if ( $payment_status == 'requires_capture' || $payment_status == 'pending' ) {
					if ( isset( $bookings[ $key ] ) ) {
						unset( $bookings[ $key ] );
					}
				}
			}

			if ( ! empty( $bookings ) ) {
				$bookings = array_values( $bookings );
			}
		}

		if ( is_array( $ordered_dates ) && ! empty( $ordered_dates ) ) {
			$resp .= '<ul>';

			foreach ( $ordered_dates as $ordered_date ) {
				$order_from    = $ordered_date . ' 00:00:00';
				$order_to      = $ordered_date . ' 23:59:59';
				$orders        = $this->bm_filter_results_by_date( $bookings, $order_from, $order_to, 'booking_created_at' );
				$total_orders  = ! empty( $orders ) ? count( $orders ) : 0;
				$total_revenue = ! empty( $orders ) ? array_sum( array_column( $orders, 'total_cost' ) ) : 0;

				$resp .= '<li>';
				$resp .= '<a href="#" class="booking_overview_tag">';
				$resp .= __( 'Bookings', 'service-booking' );
				$resp .= ': ';
				$resp .= $total_orders . ', ';
				$resp .= __( 'Revenue', 'service-booking' );
				$resp .= ': ';
				$resp .= $total_revenue !== 0 ? $this->bm_fetch_price_in_global_settings_format( $total_revenue, true ) : $this->bm_fetch_price_in_global_settings_format( $total_revenue );
				$resp .= '<span class="desc">' . $this->bm_day_date_month_year_format( $ordered_date ) . '</span>';
				$resp .= '</a></li>';
			}

			$resp .= '</ul>';
		} else {
			$resp .= '<ul>';

			for ( $i = 1; $i <= 7; $i++ ) {
				$resp .= '<li>';
				$resp .= '<a href="#" class="booking_overview_tag">';
				$resp .= __( 'Bookings', 'service-booking' );
				$resp .= ': ';
				$resp .= ( 0 ) . ', ';
				$resp .= __( 'Revenue', 'service-booking' );
				$resp .= ': ';
				$resp .= $this->bm_fetch_price_in_global_settings_format( 0 );
				$resp .= '<span class="desc">' . $this->bm_day_date_month_year_format( $today ) . '</span>';
				$resp .= '</a></li>';
				$today = $this->bm_add_day( $today, '-1 day' );
			}

			$resp .= '</ul>';
		} //end if

		return $resp;
	}//end bm_fetch_booking_overview_data()


	/**
	 * Fetch booking comparison as per previous month
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booking_comparison_as_per_prev_month() {
		$dbhandler                   = new BM_DBhandler();
		$percent_increase_in_booking = 0;
		$resp                        = '';
		$current_year_and_month      = $this->bm_fetch_date_query( 'this_month' )[0];

		$current_year   = $current_year_and_month['year'];
		$current_month  = $current_year_and_month['month'];
		$previous_month = ( $current_month - 1 );

		$last_day_of_current_month  = $this->bm_fetch_last_day_of_month( $current_month, $current_year );
		$last_day_of_previous_month = $this->bm_fetch_last_day_of_month( $previous_month, $current_year );

		$ordered_from        = $current_year . '-' . $current_month . '-01 00:00:00';
		$ordered_to          = $current_year . '-' . $current_month . '-' . $last_day_of_current_month . ' 23:59:59';
		$additional          = "AND booking_created_at BETWEEN '$ordered_from' AND '$ordered_to'";
		$this_month_bookings = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'booking_created_at', 'DESC', $additional );

		if ( ! empty( $this_month_bookings ) && is_array( $this_month_bookings ) ) {
			foreach ( $this_month_bookings as $key => $booking ) {
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking->id, 'booking_id' );

				if ( $payment_status == 'requires_capture' || $payment_status == 'pending' ) {
					if ( isset( $this_month_bookings[ $key ] ) ) {
						unset( $this_month_bookings[ $key ] );
					}
				}
			}

			if ( ! empty( $this_month_bookings ) ) {
				$this_month_bookings = array_values( $this_month_bookings );
			}
		}

		$ordered_from        = $current_year . '-' . $previous_month . '-01 00:00:00';
		$ordered_to          = $current_year . '-' . $previous_month . '-' . $last_day_of_previous_month . ' 23:59:59';
		$additional          = "AND booking_created_at BETWEEN '$ordered_from' AND '$ordered_to'";
		$last_month_bookings = $dbhandler->get_all_result( 'BOOKING', '*', array( 'is_active' => 1 ), 'results', 0, false, 'booking_created_at', 'DESC', $additional );

		if ( ! empty( $last_month_bookings ) && is_array( $last_month_bookings ) ) {
			foreach ( $last_month_bookings as $key => $booking ) {
				$payment_status = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking->id, 'booking_id' );

				if ( $payment_status == 'requires_capture' || $payment_status == 'pending' ) {
					if ( isset( $last_month_bookings[ $key ] ) ) {
						unset( $last_month_bookings[ $key ] );
					}
				}
			}

			if ( ! empty( $last_month_bookings ) ) {
				$last_month_bookings = array_values( $last_month_bookings );
			}
		}

		$this_month_booking_count = is_array( $this_month_bookings ) && ! empty( $this_month_bookings ) ? count( $this_month_bookings ) : 0;
		$last_month_booking_count = is_array( $last_month_bookings ) && ! empty( $last_month_bookings ) ? count( $last_month_bookings ) : 0;

		if ( ! empty( $this_month_booking_count ) && ! empty( $last_month_booking_count ) ) {
			$percent_increase_in_booking = ( ( $this_month_booking_count - $last_month_booking_count ) / $last_month_booking_count * 100 );
		} elseif ( empty( $last_month_booking_count ) ) {
			$percent_increase_in_booking = $this_month_booking_count;
		}

		if ( ! empty( $percent_increase_in_booking ) ) {
			if ( $percent_increase_in_booking > 0 ) {
				$resp .= '<i class="fa fa-arrow-up"></i>' . ceil( $percent_increase_in_booking ) . '%' . __( ' this month', 'service-booking' );
			} elseif ( $percent_increase_in_booking < 0 ) {
				$resp .= '<i class="fa fa-arrow-down"></i>' . ceil( $percent_increase_in_booking ) . '%' . __( ' this month', 'service-booking' );
			}
		}

		if ( empty( $resp ) ) {
			$resp .= '<i class="fa fa-arrow-down"></i>' . ( 0 ) . '%' . __( ' this month', 'service-booking' );
		}

		return $resp;
	}//end bm_fetch_booking_comparison_as_per_prev_month()


	/**
	 * Save booking data After order completetion
	 *
	 * @author Darpan
	 */
	public function bm_save_booking_data( $booking_key, $checkout_key, $wc_order_id = 0 ) {
		$dbhandler    = new BM_DBhandler();
		$order_number = 0;
		$data         = array();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$order_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}
                $order_data = apply_filters( 'bm_order_data_before_save_booking_data', $order_data, $booking_key, $checkout_key );

		/**$order_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
		$checkout_data    = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$booking_type     = isset( $checkout_data['request_type'] ) ? $checkout_data['request_type'] : '';
		$field_data       = isset( $checkout_data['billing'] ) ? $checkout_data['billing'] : array();
		$booking_currency = $this->bm_get_currency_char( $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ) );

		if ( ! empty( $order_data ) && ! empty( $field_data ) ) {
			$service_id           = isset( $order_data['service_id'] ) && ! empty( $order_data['service_id'] ) ? $order_data['service_id'] : 0;
			$extra_service_ids    = isset( $order_data['extra_svc_booked'] ) && ! empty( $order_data['extra_svc_booked'] ) ? $order_data['extra_svc_booked'] : 0;
			$total_service_booked = isset( $order_data['total_service_booking'] ) && ! empty( $order_data['total_service_booking'] ) ? $order_data['total_service_booking'] : 0;
			$extra_slots_booked   = isset( $order_data['total_extra_slots_booked'] ) && ! empty( $order_data['total_extra_slots_booked'] ) ? $order_data['total_extra_slots_booked'] : 0;
			$total_cost           = isset( $order_data['total_cost'] ) ? $order_data['total_cost'] : 0;
			$subtotal             = isset( $order_data['subtotal'] ) ? $order_data['subtotal'] : $total_cost;
			$booking_country      = isset( $order_data['country_code'] ) && ! empty( $order_data['country_code'] ) ? $this->bm_get_countries( $order_data['country_code'] ) : $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
			$has_extra            = $extra_service_ids !== 0 ? 1 : 0;
			$date                 = isset( $order_data['booking_date'] ) ? $order_data['booking_date'] : '';
			$data['field_values'] = $field_data;

			if ( $service_id > 0 && $total_service_booked > 0 ) {
				$svc_total_time_slots = $this->bm_fetch_total_time_slots_by_service_id( $service_id );
				$is_variable_slot     = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );

				if ( isset( $order_data['base_svc_price'] ) && strpos( $order_data['base_svc_price'], $booking_currency ) !== false ) {
					$order_data['base_svc_price'] = str_replace( $booking_currency, '', $order_data['base_svc_price'] );
				}

				if ( isset( $order_data['service_cost'] ) && strpos( $order_data['service_cost'], $booking_currency ) !== false ) {
					$order_data['service_cost'] = str_replace( $booking_currency, '', $order_data['service_cost'] );
				}

				if ( isset( $order_data['extra_svc_cost'] ) && strpos( $order_data['extra_svc_cost'], $booking_currency ) !== false ) {
					$order_data['extra_svc_cost'] = str_replace( $booking_currency, '', $order_data['extra_svc_cost'] );
				}

				if ( isset( $order_data['discount'] ) && strpos( $order_data['discount'], $booking_currency ) !== false ) {
					$order_data['discount'] = str_replace( $booking_currency, '', $order_data['discount'] );
				}

				if ( isset( $order_data['total_cost'] ) && strpos( $order_data['total_cost'], $booking_currency ) !== false ) {
					$order_data['total_cost'] = str_replace( $booking_currency, '', $order_data['total_cost'] );
				}

				if ( isset( $order_data['booking_slots'] ) && strpos( $order_data['booking_slots'], ' - ' ) !== false ) {
					$booking_slots = explode( ' - ', $order_data['booking_slots'] );
					$from          = $this->bm_twenty_fourhrs_format( $booking_slots[0] );
					$to            = $this->bm_twenty_fourhrs_format( $booking_slots[1] );
				} else {
					$from = $this->bm_twenty_fourhrs_format( $order_data['booking_slots'] );
					if ( $is_variable_slot == 1 ) {
						$to = $this->bm_fetch_variable_to_time_slot_by_service_id( $service_id, $from, $date );
					} else {
						$to = $this->bm_fetch_non_variable_to_time_slot_by_service_id( $service_id, $from );
					}
				}

				$discount_amount = isset( $order_data['discount'] ) ? $order_data['discount'] : 0;
				$coupon_applied  = $dbhandler->bm_fetch_data_from_transient( 'coupon_applied_' . $booking_key );
				$coupon_applied  = ! empty( $coupon_applied ) ? implode( ',', array_column( $coupon_applied, 'code' ) ) : null;

				$slot_info = $this->bm_fetch_slot_details( $service_id, $from, $date, $svc_total_time_slots, $total_service_booked, $is_variable_slot );

                if ( !empty( $slot_info ) || ( isset( $checkout_data['checkout']['is_gift'] ) && $checkout_data['checkout']['is_gift'] == 1 ) ) {
                    if ( ( isset( $slot_info['slot_capacity_left_after_booking'] ) && isset( $slot_info['slot_min_cap'] ) && ( $slot_info['slot_capacity_left_after_booking'] >= 0 ) && ( $total_service_booked % $slot_info['slot_min_cap'] == 0 ) ) || ( isset( $checkout_data['checkout']['is_gift'] ) && $checkout_data['checkout']['is_gift'] == 1 ) ) {
                        if ( isset( $order_data['total_service_booking'] ) ) {
                            unset( $order_data['total_service_booking'] );
                        }

						if ( isset( $order_data['total_extra_slots_booked'] ) ) {
							unset( $order_data['total_extra_slots_booked'] );
						}

						if ( isset( $order_data['country_code'] ) ) {
							unset( $order_data['country_code'] );
						}

						if ( isset( $order_data['subtotal'] ) ) {
							unset( $order_data['subtotal'] );
						}

						if ( isset( $order_data['svc_price_module_id'] ) ) {
							unset( $order_data['svc_price_module_id'] );
						}

						if ( isset( $order_data['discount'] ) ) {
							unset( $order_data['discount'] );
						}

						$order_data['booking_slots']       = array(
							'from' => $from,
							'to'   => $to,
						);
						$order_data['is_frontend_booking'] = 1;
						$order_data['is_active']           = 1;
						$order_data['booking_key']         = $booking_key;
						$order_data['wc_order_id']         = $wc_order_id;
						$order_data['checkout_key']        = $checkout_key;
						$order_data['has_extra']           = $has_extra;
						$order_data['total_svc_slots']     = $total_service_booked;
						$order_data['total_ext_svc_slots'] = ! empty( $extra_slots_booked ) ? array_sum( explode( ',', $extra_slots_booked ) ) : 0;
						$order_data['disount_amount']      = $discount_amount;
						$order_data['subtotal']            = $subtotal;
						$order_data['order_status']        = 'processing';
						$order_data['booking_country']     = $booking_country;
						$order_data['booking_type']        = $booking_type;
						$order_data['price_module_data']   = $this->bm_fetch_price_module_data_for_order( $booking_key );
						$order_data['coupons']             = $coupon_applied;
						$order_data['booking_created_at']  = $this->bm_fetch_current_wordpress_datetime_stamp();

						$data      = array_merge( $data, $order_data );
						$finaldata = $this->sanitize_request( $data, 'BOOKING' );

						if ( $finaldata != false && $finaldata != null ) {

							$booking_id = $dbhandler->insert_row( 'BOOKING', $finaldata );

							if ( $booking_id ) {
                                                                do_action( 'bm_order_data_after_save_booking_data', $booking_id, $finaldata, $booking_key, $checkout_key );

								$order_number    = $booking_id;
								$slot_count_data = array(
									'service_id'           => $service_id,
									'booking_id'           => $booking_id,
									'wc_order_id'          => $wc_order_id,
									'booking_date'         => $date,
									'slot_id'              => $slot_info['slot_id'],
									'is_variable'          => $is_variable_slot,
									'slot_min_cap'         => $slot_info['slot_min_cap'],
									'slot_max_cap'         => $slot_info['slot_max_cap'],
									'slot_cap_left'        => $slot_info['slot_capacity_left_after_booking'],
									'current_slots_booked' => $total_service_booked,
									'slot_total_booked'    => $slot_info['slot_total_booked'],
									'svc_total_booked_slots' => $slot_info['total_booked_after_current_booking'],
									'total_time_slots'     => $svc_total_time_slots,
									'svc_total_cap'        => $slot_info['total_capacity'],
									'svc_total_cap_left'   => $slot_info['svc_total_cap_left_after_booking'],
									'slot_booked_at'       => $this->bm_fetch_current_wordpress_datetime_stamp(),
									'is_active'            => 1,
								);

								$slot_count_final_data = $this->sanitize_request( $slot_count_data, 'SLOTCOUNT' );

								if ( $slot_count_final_data != false && $slot_count_final_data != null ) {
									$slot_count_id = $dbhandler->insert_row( 'SLOTCOUNT', $slot_count_final_data );
								}

								if ( $slot_count_id ) {
									if ( $extra_service_ids != 0 && $extra_slots_booked != 0 ) {
										$extra_service_ids  = explode( ',', $extra_service_ids );
										$extra_slots_booked = explode( ',', $extra_slots_booked );

										foreach ( $extra_service_ids as $key => $extra_id ) {
											$slots_booked  = $extra_slots_booked[ $key ];
											$extra_max_cap = $this->bm_fetch_extra_service_max_cap_by_extra_service_id( $extra_id );
											$cap_left      = $this->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date( $extra_id, $extra_max_cap, $slots_booked, $date );

											$extra_svc_count_data = array(
												'extra_svc_id' => $extra_id,
												'service_id' => $service_id,
												'booking_id' => $booking_id,
												'wc_order_id' => $wc_order_id,
												'booking_count_id' => $slot_count_id,
												'booking_date' => $date,
												'max_cap'  => $extra_max_cap,
												'slots_booked' => $slots_booked,
												'cap_left' => $cap_left,
												'slot_booked_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
												'is_active' => 1,
											);

											$extra_svc_count_final_data = $this->sanitize_request( $extra_svc_count_data, 'EXTRASLOTCOUNT' );

											if ( $extra_svc_count_final_data != false && $extra_svc_count_final_data != null ) {
												$extra_slot_count_id = $dbhandler->insert_row( 'EXTRASLOTCOUNT', $extra_svc_count_final_data );
											}
										} //end foreach
									} //end if
								} //end if
								$dbhandler->update_global_option_value( 'bm_flexibooking_booking_id' . $booking_key, $booking_id );
							} //end if
						} //end if
					} //end if
				} //end if
			} //end if
		} //end if

		return $order_number;
	}//end bm_save_booking_data()


	/**
	 * Fetch price module data for an order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_price_module_data_for_order( $booking_key ) {
		$dbhandler         = new BM_DBhandler();
		$price_module_data = array();

		if ( ! empty( $booking_key ) ) {
			$discounted_age_group_persons = $dbhandler->bm_fetch_data_from_transient( 'flexi_total_person_discounted_' . $booking_key );
			$svc_price_module_id          = $dbhandler->bm_fetch_data_from_transient( 'flexi_svc_price_module_id_' . $booking_key );
			$svc_price_module_age_ranges  = $dbhandler->bm_fetch_data_from_transient( 'flexi_svc_price_module_age_ranges_' . $booking_key );
			$age_wise_discount            = $dbhandler->bm_fetch_data_from_transient( 'flexi_age_wise_discount_' . $booking_key );
			$age_wise_total_price         = $dbhandler->bm_fetch_data_from_transient( 'flexi_age_wise_total_price_' . $booking_key );
			$negative_discount            = $dbhandler->get_global_option_value( 'negative_discount_' . $booking_key, 0 );
			$svc_base_price               = $dbhandler->bm_fetch_data_from_transient( 'flexi_base_price_' . $booking_key );

			if ( ! empty( $discounted_age_group_persons ) && is_array( $discounted_age_group_persons ) ) {
				$total_discounted_infants  = isset( $discounted_age_group_persons[0] ) ? intval( $discounted_age_group_persons[0] ) : 0;
				$total_discounted_children = isset( $discounted_age_group_persons[1] ) ? intval( $discounted_age_group_persons[1] ) : 0;
				$total_discounted_adults   = isset( $discounted_age_group_persons[2] ) ? intval( $discounted_age_group_persons[2] ) : 0;
				$total_discounted_seniors  = isset( $discounted_age_group_persons[3] ) ? intval( $discounted_age_group_persons[3] ) : 0;
			}

			if ( ! empty( $svc_price_module_age_ranges ) && is_array( $svc_price_module_age_ranges ) ) {
				$infants_age_group  = isset( $svc_price_module_age_ranges['infant'] ) ? $svc_price_module_age_ranges['infant'] : array();
				$children_age_group = isset( $svc_price_module_age_ranges['children'] ) ? $svc_price_module_age_ranges['children'] : array();
				$adults_age_group   = isset( $svc_price_module_age_ranges['adult'] ) ? $svc_price_module_age_ranges['adult'] : array();
				$seniors_age_group  = isset( $svc_price_module_age_ranges['senior'] ) ? $svc_price_module_age_ranges['senior'] : array();
			}

			if ( ! empty( $total_discounted_infants ) && ! empty( $infants_age_group ) ) {
				$negative_infant_discount                           = $dbhandler->get_global_option_value( 'negative_discount_age_group_0_' . $booking_key, 0 );
				$price_module_data['infant']['total']               = $total_discounted_infants;
				$price_module_data['infant']['age']['from']         = isset( $infants_age_group['from'] ) ? esc_html( $infants_age_group['from'] ) : 0;
				$price_module_data['infant']['age']['to']           = isset( $infants_age_group['to'] ) ? esc_html( $infants_age_group['to'] ) : 0;
				$price_module_data['infant']['discount_per_person'] = isset( $age_wise_discount[0] ) ? floatval( $age_wise_discount[0] ) : 0;
				$price_module_data['infant']['total_cost']          = isset( $age_wise_total_price[0] ) ? floatval( $age_wise_total_price[0] ) : 0;
				$price_module_data['infant']['discount_type']       = $negative_infant_discount == 1 ? 'negative' : 'positive';
				$price_module_data['infant']['total_discount']      = $this->bm_fetch_total_price( $price_module_data['infant']['discount_per_person'], $total_discounted_infants );
			}

			if ( ! empty( $total_discounted_children ) && ! empty( $children_age_group ) ) {
				$negative_children_discount                           = $dbhandler->get_global_option_value( 'negative_discount_age_group_1_' . $booking_key, 0 );
				$price_module_data['children']['total']               = $total_discounted_children;
				$price_module_data['children']['age']['from']         = isset( $children_age_group['from'] ) ? esc_html( $children_age_group['from'] ) : 0;
				$price_module_data['children']['age']['to']           = isset( $children_age_group['to'] ) ? esc_html( $children_age_group['to'] ) : 0;
				$price_module_data['children']['discount_per_person'] = isset( $age_wise_discount[1] ) ? floatval( $age_wise_discount[1] ) : 0;
				$price_module_data['children']['total_cost']          = isset( $age_wise_total_price[1] ) ? floatval( $age_wise_total_price[1] ) : 0;
				$price_module_data['children']['discount_type']       = $negative_children_discount == 1 ? 'negative' : 'positive';
				$price_module_data['children']['total_discount']      = $this->bm_fetch_total_price( $price_module_data['children']['discount_per_person'], $total_discounted_children );
			}

			if ( ! empty( $total_discounted_adults ) && ! empty( $adults_age_group ) ) {
				$negative_adult_discount                           = $dbhandler->get_global_option_value( 'negative_discount_age_group_2_' . $booking_key, 0 );
				$price_module_data['adult']['total']               = $total_discounted_adults;
				$price_module_data['adult']['age']['from']         = isset( $adults_age_group['from'] ) ? esc_html( $adults_age_group['from'] ) : 0;
				$price_module_data['adult']['age']['to']           = isset( $adults_age_group['to'] ) ? esc_html( $adults_age_group['to'] ) : 0;
				$price_module_data['adult']['discount_per_person'] = isset( $age_wise_discount[2] ) ? floatval( $age_wise_discount[2] ) : 0;
				$price_module_data['adult']['total_cost']          = ( $svc_base_price > $price_module_data['adult']['discount_per_person'] ) ? $this->bm_fetch_total_price( ( $svc_base_price - $price_module_data['adult']['discount_per_person'] ), $total_discounted_adults ) : $this->bm_fetch_total_price( ( $price_module_data['adult']['discount_per_person'] - $svc_base_price ), $total_discounted_adults );
				$price_module_data['adult']['discount_type']       = $negative_adult_discount == 1 ? 'negative' : 'positive';
				$price_module_data['adult']['total_discount']      = $this->bm_fetch_total_price( $price_module_data['adult']['discount_per_person'], $total_discounted_adults );
			}

			if ( ! empty( $total_discounted_seniors ) && ! empty( $seniors_age_group ) ) {
				$negative_senior_discount                           = $dbhandler->get_global_option_value( 'negative_discount_age_group_2_' . $booking_key, 0 );
				$price_module_data['senior']['total']               = $total_discounted_seniors;
				$price_module_data['senior']['age']['from']         = isset( $seniors_age_group['from'] ) ? esc_html( $seniors_age_group['from'] ) : 0;
				$price_module_data['senior']['age']['to']           = isset( $seniors_age_group['to'] ) ? esc_html( $seniors_age_group['to'] ) : 0;
				$price_module_data['senior']['discount_per_person'] = isset( $age_wise_discount[3] ) ? floatval( $age_wise_discount[3] ) : 0;
				$price_module_data['senior']['total_cost']          = ( $svc_base_price > $price_module_data['senior']['discount_per_person'] ) ? $this->bm_fetch_total_price( ( $svc_base_price - $price_module_data['senior']['discount_per_person'] ), $total_discounted_seniors ) : $this->bm_fetch_total_price( ( $price_module_data['senior']['discount_per_person'] - $svc_base_price ), $total_discounted_seniors );
				$price_module_data['senior']['discount_type']       = $negative_senior_discount == 1 ? 'negative' : 'positive';
				$price_module_data['senior']['total_discount']      = $this->bm_fetch_total_price( $price_module_data['senior']['discount_per_person'], $total_discounted_seniors );
			}

			if ( isset( $age_wise_discount['group_discount'] ) ) {
				$price_module_data['group_discount'] = floatval( $age_wise_discount['group_discount'] );
			}

			if ( ! empty( $price_module_data ) ) {
				$price_module_data['discount_type'] = $negative_discount == 1 ? 'negative' : 'positive';
				$price_module_data['module_id']     = ! empty( $svc_price_module_id ) ? $svc_price_module_id : 0;
			}
		}

		return $price_module_data;
	}//end bm_fetch_price_module_data_for_order()


	/**
	 * Fetch customer data from latest checkout data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_data_from_latest_checkout( $checkout_key ) {
		$dbhandler     = new BM_DBhandler();
		$checkout_data = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$checkout_data = isset( $checkout_data['checkout'] ) ? $checkout_data['checkout'] : array();
		$customer_data = array();

		if ( ! empty( $checkout_data ) && is_array( $checkout_data ) ) {
			$billing_details = isset( $checkout_data['billing_details'] ) ? $checkout_data['billing_details'] : array();

			if ( ! empty( $billing_details ) && is_array( $billing_details ) ) {
				$customer_first_name      = isset( $billing_details['billing_first_name'] ) ? $billing_details['billing_first_name'] : '';
				$customer_last_name       = isset( $billing_details['billing_last_name'] ) ? $billing_details['billing_last_name'] : '';
				$customer_data['email']   = isset( $billing_details['billing_email'] ) ? $billing_details['billing_email'] : '';
				$customer_data['contact'] = isset( $billing_details['billing_contact'] ) ? $billing_details['billing_contact'] : '';
				$customer_data['city']    = isset( $billing_details['billing_city'] ) ? $billing_details['billing_city'] : '';
				$customer_data['country'] = isset( $billing_details['billing_country'] ) ? $this->bm_get_countries( $billing_details['billing_country'] ) : $this->bm_get_countries( $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' ) );
				$customer_data['zip']     = isset( $billing_details['billing_postcode'] ) ? $billing_details['billing_postcode'] : '';
				$customer_data['name']    = $customer_first_name . ' ' . $customer_last_name;
			}
		}

		return $customer_data;
	}//end bm_fetch_customer_data_from_latest_checkout()


	/**
	 * Fetch booked slots info for latest order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_booked_slot_info_from_booking_data( $booking_data = array() ) {
		$dbhandler    = new BM_DBhandler();
		$booked_slots = array();

		if ( ! empty( $booking_data ) && is_array( $booking_data ) ) {
			$from       = '';
			$to         = '';
			$service_id = ! empty( $booking_data ) && isset( $booking_data['service_id'] ) ? $booking_data['service_id'] : 0;
			$date       = ! empty( $booking_data ) && isset( $booking_data['booking_date'] ) ? $booking_data['booking_date'] : '';

			if ( ! empty( $service_id ) && ! empty( $date ) ) {
				$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $date );

				if ( isset( $booking_data['booking_slots'] ) && strpos( $booking_data['booking_slots'], ' - ' ) !== false ) {
					$booking_slots = explode( ' - ', $booking_data['booking_slots'] );
					$from          = $this->bm_twenty_fourhrs_format( $booking_slots[0] );
					$to            = $this->bm_twenty_fourhrs_format( $booking_slots[1] );
				} else {
					$from = $this->bm_twenty_fourhrs_format( $booking_data['booking_slots'] );
					if ( $is_variable_slot == 1 ) {
						$to = $this->bm_fetch_variable_to_time_slot_by_service_id( $service_id, $from, $date );
					} else {
						$to = $this->bm_fetch_non_variable_to_time_slot_by_service_id( $service_id, $from );
					}
				}

				$booked_slots['from'] = $from;
				$booked_slots['to']   = $to;
			}
		}

		return $booked_slots;
	}//end bm_fetch_booked_slot_info_from_booking_data()


	/**
	 * Fetch payment message for checkout page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_payment_message_for_checkout_page( $booking_key ) {
		$dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_details = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_details = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$booking_details = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
		$message = '';

		if ( ! empty( $booking_details ) ) {
			$service_id   = isset( $booking_details['service_id'] ) ? $booking_details['service_id'] : 0;
			$booking_date = isset( $booking_details['booking_date'] ) ? $booking_details['booking_date'] : '';

			if ( ! empty( $service_id ) && ! empty( $booking_date ) ) {
				$stopsales        = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $booking_date );
				$saleswitch       = $this->bm_fetch_service_saleswitch_by_service_id( $service_id, $booking_date );
				$booked_slots     = $this->bm_fetch_booked_slot_info_from_booking_data( $booking_details );
				$from_slot        = ! empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $booked_slots['from'] : '';
				$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $booking_date );

				$is_book_on_request_only = $this->bm_check_if_book_on_request_only( $service_id );

				if ( ! empty( $from_slot ) ) {
					$timezone    = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
					$now         = new DateTime( 'now', new DateTimeZone( $timezone ) );
					$slotTime    = new DateTime( $booking_date . ' ' . $from_slot, new DateTimeZone( $timezone ) );
					$slotTime    = $slotTime->format( 'Y-m-d H:i' );
					$total_slots = $this->bm_fetch_total_time_slots_by_service_id( $service_id );
					$slot_info   = $this->bm_fetch_slot_details( $service_id, $from_slot, $booking_date, $total_slots, 0, $is_variable_slot, array( 'capacity_left' ) );

					if ( ! empty( $slotTime ) ) {
						if ( ! empty( $stopsales ) ) {
							$stopSalesHours   = floor( $stopsales );
							$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

							if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $booking_date ) ) {
								$stopsalesDateTime = new DateTime( $booking_date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
							} else {
								$stopsalesDateTime = clone $now;
							}

							$stopsalesDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
							$endStopsales = $stopsalesDateTime->format( 'Y-m-d H:i' );
						}

						if ( ( ! empty( $stopsales ) && ( strtotime( $endStopsales ) > strtotime( $slotTime ) ) ) ) {
							$message = '';
						} elseif ( isset( $slot_info['capacity_left'] ) && ( $slot_info['capacity_left'] <= 0 ) ) {
							$message = '';
						} else {
							$booked_product = $this->bm_fetch_booked_service_info_for_stripe_payment_intent( $booking_key );

							$amount      = ! empty( $booked_product ) && isset( $booked_product['amount'] ) ? floatval( $booked_product['amount'] ) * 100 : 0;
							$currency    = ! empty( $booked_product ) && isset( $booked_product['currency'] ) ? $booked_product['currency'] : '';
							$description = ! empty( $booked_product ) && isset( $booked_product['description'] ) ? $booked_product['description'] : '';

							if ( ( $amount > 0 ) && ! empty( $currency ) && ! empty( $description ) ) {
								if ( ( $is_book_on_request_only == 1 ) ) {
									$message = __(
										'You are making a booking request using card, your card is
                                            pre-authorised for the booking amount, this means that the amount is reserved/blocked on
                                            your card, so it can only be charged later when the bookings are definitively
                                            confirmed. Only when the booking is accepted will the money be debited from the
                                            pre-authorisation. If the request is rejected by the provider for any reason, the
                                            pre-authorization will be released in the following 48h-72h and no charge will be
                                            made.',
										'service-booking'
									);
								} elseif ( ! empty( $stopsales ) && ! empty( $saleswitch ) ) {
										$saleswitchHours   = floor( $saleswitch );
										$saleswitchMinutes = ( $saleswitch - $saleswitchHours ) * 60;
										$endSaleswitch     = $stopsalesDateTime->add( new DateInterval( "PT{$saleswitchHours}H{$saleswitchMinutes}M" ) );
										$endSaleswitch     = $endSaleswitch->format( 'Y-m-d H:i' );

									if ( ( strtotime( $slotTime ) > strtotime( $endSaleswitch ) ) ) {
										$message = __(
											'You are making a direct booking. Please accept terms and conditions before proceeding',
											'service-booking'
										);
									} else {
										$message = __(
											'You are making a booking request using card, your card is
                                                    pre-authorised for the booking amount, this means that the amount is reserved/blocked on
                                                    your card, so it can only be charged later when the bookings are definitively
                                                    confirmed. Only when the booking is accepted will the money be debited from the
                                                    pre-authorisation. If the request is rejected by the provider for any reason, the
                                                    pre-authorization will be released in the following 48h-72h and no charge will be
                                                    made.',
											'service-booking'
										);
									}
								} elseif ( empty( $stopsales ) && ! empty( $saleswitch ) ) {
									$saleswitchHours   = floor( $saleswitch );
									$saleswitchMinutes = ( $saleswitch - $saleswitchHours ) * 60;
									$endSaleswitch     = $now->add( new DateInterval( "PT{$saleswitchHours}H{$saleswitchMinutes}M" ) );
									$endSaleswitch     = $endSaleswitch->format( 'Y-m-d H:i' );

									if ( ( strtotime( $slotTime ) > strtotime( $endSaleswitch ) ) ) {
										$message = __(
											'You are making a direct booking. Please accept terms and conditions before proceeding',
											'service-booking'
										);
									} else {
										$message = __(
											'You are making a booking request using card, your card is
                                                    pre-authorised for the booking amount, this means that the amount is reserved/blocked on
                                                    your card, so it can only be charged later when the bookings are definitively
                                                    confirmed. Only when the booking is accepted will the money be debited from the
                                                    pre-authorisation. If the request is rejected by the provider for any reason, the
                                                    pre-authorization will be released in the following 48h-72h and no charge will be
                                                    made.',
											'service-booking'
										);
									}
								} else {
									$message = __(
										'You are making a direct booking. Please accept terms and conditions before proceeding',
										'service-booking'
									);
								}
							}
						}
					}
				}
			}
		}

		return wp_kses_post( $message );
	} //end bm_fetch_booked_slot_info_from_booking_data()


	/**
	 * Check payment type
	 *
	 * @author Darpan
	 */
	public function bm_check_payment_type_and_return_data( $booking_key, $checkout_key, $checkout_type = 'flexi_checkout' ) {
		$dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_details = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_details = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$booking_details  = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
		$checkout_details = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$data             = array();
		$resp             = '';

		if ( ! empty( $booking_details ) && ! empty( $checkout_details ) ) {
			$service_id   = isset( $booking_details['service_id'] ) ? $booking_details['service_id'] : 0;
			$booking_date = isset( $booking_details['booking_date'] ) ? $booking_details['booking_date'] : '';

			$total_service_booked = isset( $booking_details['total_service_booking'] ) ? $booking_details['total_service_booking'] : 0;
			$bookable_extra       = $this->bm_is_selected_extra_service_bookable( $booking_key );

			if ( ! empty( $service_id ) && ! empty( $booking_date ) ) {
				if ( $this->bm_service_is_bookable( $service_id, $booking_date ) ) {
					$stopsales        = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $booking_date );
					$saleswitch       = $this->bm_fetch_service_saleswitch_by_service_id( $service_id, $booking_date );
					$booked_slots     = $this->bm_fetch_booked_slot_info_from_booking_data( $booking_details );
					$from_slot        = ! empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $booked_slots['from'] : '';
					$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $service_id, $booking_date );

					$payment_session_timer   = $dbhandler->get_global_option_value( 'bm_payment_session_time', '2' );
					$payment_session_timer   = ( $payment_session_timer * 60 ) + 2;
					$is_book_on_request_only = $this->bm_check_if_book_on_request_only( $service_id );

					if ( ! empty( $from_slot ) ) {
						$timezone    = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
						$now         = new DateTime( 'now', new DateTimeZone( $timezone ) );
						$slotTime    = new DateTime( $booking_date . ' ' . $from_slot, new DateTimeZone( $timezone ) );
						$slotTime    = $slotTime->format( 'Y-m-d H:i' );
						$total_slots = $this->bm_fetch_total_time_slots_by_service_id( $service_id );
						$slot_info   = $this->bm_fetch_slot_details( $service_id, $from_slot, $booking_date, $total_slots, $total_service_booked, $is_variable_slot, array( 'slot_min_cap', 'slot_capacity_left_after_booking' ) );

						if ( ! empty( $slotTime ) ) {
							if ( ! empty( $stopsales ) ) {
								$stopSalesHours   = floor( $stopsales );
								$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

								if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $booking_date ) ) {
									$stopsalesDateTime = new DateTime( $booking_date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
								} else {
									$stopsalesDateTime = clone $now;
								}

								$stopsalesDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
								$endStopsales = $stopsalesDateTime->format( 'Y-m-d H:i' );
							}

							if ( ( ! empty( $stopsales ) && ( strtotime( $endStopsales ) > strtotime( $slotTime ) ) ) ) {
								$resp = __( 'Can not book the service on the selected slot !!', 'service-booking' );

								$data['status'] = 'error';
								$data['data']   = wp_kses_post( $resp );
							} elseif ( isset( $slot_info['slot_capacity_left_after_booking'] ) && ( $slot_info['slot_capacity_left_after_booking'] < 0 ) ) {
								$resp = __( 'Not enough capacity left, try booking another slot or service !!', 'service-booking' );

								$data['status'] = 'error';
								$data['data']   = wp_kses_post( $resp );
							} elseif ( ( $bookable_extra == false ) ) {
								$resp = __( 'One or more extra services does not have enough capacity, choose another !!', 'service-booking' );

								$data['status'] = 'error';
								$data['data']   = wp_kses_post( $resp );
							} elseif ( isset( $slot_info['slot_capacity_left_after_booking'] ) && isset( $slot_info['slot_min_cap'] ) && ( $slot_info['slot_capacity_left_after_booking'] >= 0 ) && ( $total_service_booked % $slot_info['slot_min_cap'] == 0 ) ) {
								$booked_product = $this->bm_fetch_booked_service_info_for_stripe_payment_intent( $booking_key );

								$amount      = ! empty( $booked_product ) && isset( $booked_product['amount'] ) ? floatval( $booked_product['amount'] ) * 100 : 0;
								$currency    = ! empty( $booked_product ) && isset( $booked_product['currency'] ) ? $booked_product['currency'] : '';
								$description = ! empty( $booked_product ) && isset( $booked_product['description'] ) ? $booked_product['description'] : '';

								if ( ( $amount > 0 ) && ! empty( $currency ) && ! empty( $description ) ) {

									if ( ( $is_book_on_request_only == 1 ) ) {
										$checkout_details['request_type'] = 'on_request';
									} elseif ( ! empty( $stopsales ) && ! empty( $saleswitch ) ) {
											$saleswitchHours   = floor( $saleswitch );
											$saleswitchMinutes = ( $saleswitch - $saleswitchHours ) * 60;
											$endSaleswitch     = $stopsalesDateTime->add( new DateInterval( "PT{$saleswitchHours}H{$saleswitchMinutes}M" ) );
											$endSaleswitch     = $endSaleswitch->format( 'Y-m-d H:i' );

										if ( ( strtotime( $slotTime ) > strtotime( $endSaleswitch ) ) ) {
											$checkout_details['request_type'] = 'direct';
										} else {
											$checkout_details['request_type'] = 'on_request';
										}
									} elseif ( empty( $stopsales ) && ! empty( $saleswitch ) ) {
										$saleswitchHours   = floor( $saleswitch );
										$saleswitchMinutes = ( $saleswitch - $saleswitchHours ) * 60;
										$endSaleswitch     = $now->add( new DateInterval( "PT{$saleswitchHours}H{$saleswitchMinutes}M" ) );
										$endSaleswitch     = $endSaleswitch->format( 'Y-m-d H:i' );

										if ( ( strtotime( $slotTime ) > strtotime( $endSaleswitch ) ) ) {
											$checkout_details['request_type'] = 'direct';
										} else {
											$checkout_details['request_type'] = 'on_request';
										}
									} else {
										$checkout_details['request_type'] = 'direct';
									}

									if ( $checkout_type == 'woocommerce_checkout' ) {
										$checkout_details['request_type'] = 'direct';
									}

									if ( isset( $checkout_details['billing'] ) && isset( $checkout_details['checkout'] ) && isset( $checkout_details['request_type'] ) ) {
										$dbhandler->bm_save_data_to_transient( $checkout_key, $checkout_details, 24 );

										if ( $checkout_type == 'woocommerce_checkout' ) {
											$data['status'] = 'success';
										} else {
											$string = $this->bm_create_random_string( 20 );
											$this->bm_start_session_with_expiry( "flexi_current_payment_session_$booking_key", $payment_session_timer );

											$data['status']   = 'success';
											$data['data']     = wp_kses_post( $string );
											$data['checkout'] = wp_kses_post( $checkout_key );
										}
									} else {
										$resp = __( 'Error Initiating Payment Data !!', 'service-booking' );

										$data['status'] = 'error';
										$data['data']   = wp_kses_post( $resp );
									}
								} else {
									$resp = __( 'Error Fetching Booking Data !!', 'service-booking' );

									$data['status'] = 'error';
									$data['data']   = wp_kses_post( $resp );
								}
							} else {
								$resp = __( 'Service not bookable !!', 'service-booking' );

								$data['status'] = 'error';
								$data['data']   = wp_kses_post( $resp );
							}
						} else {
							$resp = __( 'Something Went Wrong, Try Again !!', 'service-booking' );

							$data['status'] = 'error';
							$data['data']   = wp_kses_post( $resp );
						}
					} else {
						$resp = __( 'Something Went Wrong, Try Again !!', 'service-booking' );

						$data['status'] = 'error';
						$data['data']   = wp_kses_post( $resp );
					}
				} else {
					$resp = __( 'Service is not bookable !!', 'service-booking' );

					$data['status'] = 'error';
					$data['data']   = wp_kses_post( $resp );
				}
			} else {
				$resp = __( 'Something Went Wrong, Try Again !!', 'service-booking' );

				$data['status'] = 'error';
				$data['data']   = wp_kses_post( $resp );
			}
		} else {
			$resp = __( 'Something Went Wrong, Try Again !!', 'service-booking' );

			$data['status'] = 'error';
			$data['data']   = wp_kses_post( $resp );
		}

		return $data;
	}//end bm_check_payment_type_and_return_data()


	/**
	 * Fetch customer info for payment intent
	 *
	 * @author Darpan
	 */
	public function bm_fetch_customer_info_for_payment_intent( $checkout_key ) {
		$dbhandler     = new BM_DBhandler();
		$customer_data = array();
		$checkout_data = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$checkout_data = isset( $checkout_data['checkout'] ) ? $checkout_data['checkout'] : array();

		if ( ! empty( $checkout_data ) ) {
			$billing_details          = isset( $checkout_data['billing_details'] ) ? $checkout_data['billing_details'] : null;
			$shipping_same_as_billing = isset( $checkout_data['other_data']['shipping_same_as_billing'] ) ? $checkout_data['other_data']['shipping_same_as_billing'] : 0;

			$address          = array();
			$shipping_details = array();
			$shipping         = array();

			if ( $shipping_same_as_billing == 1 ) {
				if ( ! empty( $billing_details ) ) {
					foreach ( $billing_details as $key => $value ) {
						$shipping_details[ str_replace( 'billing', 'shipping', $key ) ] = $value;
					}
				}
			} else {
				$shipping_details = isset( $checkout_data['shipping_details'] ) ? $checkout_data['shipping_details'] : null;
			}

			if ( ! empty( $billing_details ) && ! empty( $shipping_details ) ) {
				$billing_first_name = isset( $billing_details['billing_first_name'] ) && ! empty( $billing_details['billing_first_name'] ) ? $billing_details['billing_first_name'] : 'Unknown';
				$billing_last_name  = isset( $billing_details['billing_last_name'] ) && ! empty( $billing_details['billing_last_name'] ) ? $billing_details['billing_last_name'] : 'Unknown';

				$shipping_first_name = isset( $shipping_details['shipping_first_name'] ) && ! empty( $shipping_details['shipping_first_name'] ) ? $shipping_details['shipping_first_name'] : 'Unknown';
				$shipping_last_name  = isset( $shipping_details['shipping_last_name'] ) && ! empty( $shipping_details['shipping_last_name'] ) ? $shipping_details['shipping_last_name'] : 'Unknown';

				$address['line1']       = isset( $billing_details['billing_address'] ) && ! empty( $billing_details['billing_address'] ) ? $billing_details['billing_address'] : 'Unknown';
				$address['city']        = isset( $billing_details['billing_city'] ) && ! empty( $billing_details['billing_city'] ) ? $billing_details['billing_city'] : 'Unknown';
				$address['state']       = isset( $billing_details['billing_state'] ) && ! empty( $billing_details['billing_state'] ) ? $billing_details['billing_state'] : 'Unknown';
				$address['country']     = isset( $billing_details['billing_country'] ) && ! empty( $billing_details['billing_country'] ) ? $billing_details['billing_country'] : $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
				$address['postal_code'] = isset( $billing_details['billing_postcode'] ) && ! empty( $billing_details['billing_postcode'] ) ? $billing_details['billing_postcode'] : '00000';

				$shipping['address']['line1']       = isset( $shipping_details['shipping_address'] ) && ! empty( $shipping_details['shipping_address'] ) ? $shipping_details['shipping_address'] : 'Unknown';
				$shipping['address']['city']        = isset( $shipping_details['shipping_city'] ) && ! empty( $shipping_details['shipping_city'] ) ? $shipping_details['shipping_city'] : 'Unknown';
				$shipping['address']['state']       = isset( $shipping_details['shipping_state'] ) && ! empty( $shipping_details['shipping_state'] ) ? $shipping_details['shipping_state'] : 'Unknown';
				$shipping['address']['country']     = isset( $shipping_details['shipping_country'] ) && ! empty( $shipping_details['shipping_country'] ) ? $shipping_details['shipping_country'] : $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
				$shipping['address']['postal_code'] = isset( $shipping_details['shipping_zip'] ) && ! empty( $shipping_details['shipping_zip'] ) ? $shipping_details['shipping_zip'] : '00000';
				$shipping['name']                   = $shipping_first_name . ' ' . $shipping_last_name;
				$shipping['phone']                  = isset( $shipping_details['shipping_contact'] ) && ! empty( $shipping_details['shipping_contact'] ) ? $shipping_details['shipping_contact'] : '0000000000';

				$customer_data['name']        = $billing_first_name . ' ' . $billing_last_name;
				$customer_data['email']       = isset( $billing_details['billing_email'] ) && ! empty( $billing_details['billing_email'] ) ? $billing_details['billing_email'] : 'no-email@example.com';
				$customer_data['description'] = sprintf( esc_html__( 'order for %s', 'service-booking' ), $customer_data['email'] );
				$customer_data['address']     = $address;
				$customer_data['phone']       = isset( $billing_details['billing_contact'] ) && ! empty( $billing_details['billing_contact'] ) ? $billing_details['billing_contact'] : '0000000000';
				$customer_data['shipping']    = $shipping;
			}
		}

		return $customer_data;
	}//end bm_fetch_customer_info_for_payment_intent()


	/**
	 * Add customer data to payment
	 *
	 * @author Darpan
	 */
	public function bm_add_customer_data_to_payment( $checkout_key, $booking_key, $method_id = null ) {
		$dbhandler        = new BM_DBhandler();
		$customer_details = $this->bm_fetch_customer_info_for_payment_intent( $checkout_key );
		$customerID       = '';
		$default_address  = array();

		if ( ! empty( $customer_details ) ) {
			$payment = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

			$default_address['line1']       = 'Unknown';
			$default_address['city']        = 'Unknown';
			$default_address['state']       = 'Unknown';
			$default_address['country']     = $dbhandler->get_global_option_value( 'bm_booking_country', 'IT' );
			$default_address['postal_code'] = '00000';

			$name     = isset( $customer_details['name'] ) && ! empty( $customer_details['name'] ) ? $customer_details['name'] : 'Unknown';
			$email    = isset( $customer_details['email'] ) && ! empty( $customer_details['email'] ) ? $customer_details['email'] : 'no-email@example.com';
			$address  = isset( $customer_details['address'] ) && ! empty( $customer_details['address'] ) ? $customer_details['address'] : $default_address;
			$phone    = isset( $customer_details['phone'] ) && ! empty( $customer_details['phone'] ) ? $customer_details['phone'] : '0000000000';
			$shipping = isset( $customer_details['shipping'] ) && ! empty( $customer_details['shipping'] ) ? $customer_details['shipping'] : $default_address;

			$customer_description = isset( $customer_details['description'] ) ? $customer_details['description'] : '';

			$customerID = $dbhandler->get_value( 'CUSTOMERS', 'stripe_id', $email, 'customer_email' );

			if ( empty( $customerID ) ) {
				$customer = $payment->saveCustomer( $name, $email, $customer_description, $address, $phone, $shipping, $booking_key, $method_id );

				if ( ! empty( $customer ) ) {
					$customerID = $customer->id;
				}
			} else {
				$stripe_customer = $payment->getCustomer( $customerID );

				if ( ! empty( $stripe_customer ) ) {
					$customer = $payment->updateCustomer( $customerID, $name, $customer_description, $address, $phone, $shipping, $booking_key );
				} else {
					$customer   = $payment->saveCustomer( $name, $email, $customer_description, $address, $phone, $shipping, $booking_key, $method_id );
					$customerID = $customer->id;
				}

				if ( ! empty( $customer ) ) {
					if ( ! empty( $stripe_customer ) ) {
						if ( $method_id !== null ) {
							$method = $payment->attachPaymentMethodToCustomer( $method_id, $customerID, $booking_key );

							if ( empty( $method ) ) {
								$customerID = '';
							}
						}
					}
				} else {
					$customerID = '';
				}
			}
		}

		return $customerID;
	} // end bm_add_customer_data_to_payment()


	/**
	 * Add order data to payment
	 *
	 * @author Darpan
	 */
	public function bm_add_order_data_to_payment( $customerID, $method_id, $booking_key, $checkout_key ) {
		$dbhandler            = new BM_DBhandler();
		$booked_product       = $this->bm_fetch_booked_service_info_for_stripe_payment_intent( $booking_key );
		$current_request_type = $this->bm_current_request_type( $booking_key );
		$checkout_data        = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$request_type         = ! empty( $checkout_data ) && isset( $checkout_data['request_type'] ) ? $checkout_data['request_type'] : '';
		$payment_intent       = '';

        $gift                 = $checkout_data['checkout']['is_gift'] ?? 0;
        $current_request_type = $gift ? 'direct' : $current_request_type;
        if ( ( $current_request_type == $request_type ) && !empty( $method_id ) ) {
            if ( !empty( $booked_product ) ) {
                $amount      = !empty( $booked_product ) && isset( $booked_product['amount'] ) ? floatval( round( $booked_product['amount'], 2 ) ) * 100 : 0;
                $currency    = !empty( $booked_product ) && isset( $booked_product['currency'] ) ? $booked_product['currency'] : '';
                $description = !empty( $booked_product ) && isset( $booked_product['description'] ) ? $booked_product['description'] : '';

				if ( ( $amount > 0 ) && ! empty( $currency ) && ! empty( $description ) ) {
					$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );

					if ( $request_type == 'on_request' ) {
						$payment_intent = $payment_processor->preAuthorizeAmount( $amount, $currency, $description, $customerID, $method_id, $booking_key, $checkout_key );
					} elseif ( $request_type == 'direct' ) {
						$payment_intent = $payment_processor->createOneTimePaymentIntent( $amount, $currency, $description, $customerID, $method_id, $booking_key, $checkout_key );
					}
				}
			}
		}

		return $payment_intent;
	} // end bm_add_order_data_to_payment()


    /**
     * Process payment data
     *
     * @author Darpan
     */
    public function bm_process_payment_data( $booking_key, $checkout_key, $method_id, $gift = false ) {
         $dbhandler     = new BM_DBhandler();
        $bookable_extra = $this->bm_is_selected_extra_service_bookable( $booking_key );
        $intentStatuses = array( 'processing', 'requires_payment_method', 'requires_confirmation', 'requires_action', 'requires_capture', 'succeeded' );
        $process_status = 'error';

        if ( !empty( $booking_key ) && !empty( $checkout_key ) && !empty( $method_id ) ) {
            if ( $this->bm_check_if_cart_order_is_still_bookable( $booking_key, $gift ) ) {
                if ( $bookable_extra ) {
                    $customerID = $this->bm_add_customer_data_to_payment( $checkout_key, $booking_key, $method_id );

					if ( ! empty( $customerID ) ) {
						$payment_intent = $this->bm_add_order_data_to_payment( $customerID, $method_id, $booking_key, $checkout_key );

						if ( ! empty( $payment_intent ) ) {
							$transaction_id = isset( $payment_intent['id'] ) ? $payment_intent['id'] : '';
							$payment_status = isset( $payment_intent['status'] ) ? $payment_intent['status'] : '';
							$dbhandler->update_global_option_value( 'bm_intent_id' . $booking_key, base64_encode( $transaction_id ) );

							if ( ! empty( $transaction_id ) && ! empty( $payment_status ) ) {
								if ( in_array( $payment_status, $intentStatuses, true ) ) {
									$client_secret = isset( $payment_intent['client_secret'] ) ? $payment_intent['client_secret'] : '';

									if ( ! empty( $client_secret ) ) {
										$dbhandler->update_global_option_value( 'bm_client_secret' . $booking_key, base64_encode( $client_secret ) );

										if ( $payment_status == 'requires_capture' || $payment_status == 'succeeded' ) {
											$process_status = $payment_status;
										} else {
											$process_status = 'success';
										}
									}
								}
							}
						}
					}
				}
			}
		}

		return $process_status;
	} // end bm_process_payment_data()


    /**
     * Save payment data
     *
     * @author Darpan
     */
    public function bm_save_payment_data( $booking_key, $checkout_key, $gift = false ) {
         $dbhandler        = new BM_DBhandler();
        $payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
        $bookable_extra    = $this->bm_is_selected_extra_service_bookable( $booking_key );
        $payment_id        = 0;
        $customer_id       = 0;
        $booking_id        = 0;
        $voucher_id        = 0;
        $transaction_id    = '';
        $payment_status    = '';
        $paid_amount       = 0;
        $process_status    = 'error';
        $intentStatuses    = array( 'succeeded', 'requires_capture' );

        if ( $this->bm_check_if_cart_order_is_still_bookable( $booking_key, $gift ) ) {
            if ( $bookable_extra ) {
                $payment_intent = $payment_processor->getPaymentIntent( base64_decode( $dbhandler->get_global_option_value( 'bm_intent_id' . $booking_key ) ) );

				if ( ! empty( $payment_intent ) ) {
					$payment_status = isset( $payment_intent['status'] ) ? $payment_intent['status'] : '';
					$customerID     = isset( $payment_intent['customer'] ) ? $payment_intent['customer'] : '';

					if ( ! empty( $payment_status ) && ! empty( $customerID ) && in_array( $payment_status, $intentStatuses, true ) ) {
						// Transaction details
						$customer       = $payment_processor->getCustomer( $customerID );
						$transaction_id = isset( $payment_intent['id'] ) ? $payment_intent['id'] : '';
						$paid_amount    = isset( $payment_intent['amount'] ) ? $payment_intent['amount'] : 0;
						$paid_amount    = ( $paid_amount / 100 );
						$paid_currency  = isset( $payment_intent['currency'] ) ? strtoupper( $payment_intent['currency'] ) : '';
						$customer_name  = isset( $customer->name ) ? $customer->name : '';
						$customer_email = isset( $customer->email ) ? $customer->email : '';

						$gift_key       = base64_encode( $booking_key );
						$gift_recipient = $dbhandler->bm_fetch_data_from_transient( $gift_key );
						$is_gift        = isset( $gift_recipient['is_gift'] ) ? $gift_recipient['is_gift'] : 0;

						// Check if transaction data exists
						$payment_id = $dbhandler->get_value( 'TRANSACTIONS', 'id', $transaction_id, 'transaction_id' );

						// Transaction data
						$transaction_data = array(
							'paid_amount'          => $paid_amount,
							'paid_amount_currency' => $paid_currency,
							'transaction_id'       => $transaction_id,
							'payment_method'       => 'card',
							'payment_status'       => $payment_status,
							'is_active'            => 1,
						);

						if ( ! empty( $payment_id ) ) {
							$transaction_data['transaction_updated_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
							$dbhandler->update_row( 'TRANSACTIONS', 'id', $payment_id, $transaction_data, '', '%d' );
						} else {
							$booking_id = $this->bm_save_booking_data( $booking_key, $checkout_key );

							if ( $booking_id ) {
								$checkout_data = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
								$checkout_data = isset( $checkout_data['checkout'] ) ? $checkout_data['checkout'] : array();

								if ( ! empty( $checkout_data ) ) {
									$billing_details          = ! empty( $checkout_data ) && isset( $checkout_data['billing_details'] ) ? $checkout_data['billing_details'] : null;
									$shipping_same_as_billing = ! empty( $checkout_data ) && isset( $checkout_data['other_data']['shipping_same_as_billing'] ) ? $checkout_data['other_data']['shipping_same_as_billing'] : 0;
									$shipping_details         = array();

									if ( $shipping_same_as_billing == 1 ) {
										if ( ! empty( $billing_details ) ) {
											foreach ( $billing_details as $key => $value ) {
												$shipping_details[ str_replace( 'billing', 'shipping', $key ) ] = $value;
											}
										}
									} else {
										$shipping_details = ! empty( $checkout_data ) && isset( $checkout_data['shipping_details'] ) ? $checkout_data['shipping_details'] : null;
									}

									$customer_data = array(
										'stripe_id'        => $customerID,
										'customer_name'    => $customer_name,
										'customer_email'   => $customer_email,
										'billing_details'  => $billing_details,
										'shipping_details' => $shipping_details,
										'shipping_same_as_billing' => $shipping_same_as_billing,
										'is_active'        => 1,
									);

									$customer_final = $this->sanitize_request( $customer_data, 'CUSTOMERS' );

									if ( $customer_final != false && $customer_final != null ) {
										$customer_id = $dbhandler->get_value( 'CUSTOMERS', 'id', $customer_email, 'customer_email' );

										if ( ! empty( $customer_id ) ) {
											$customer_final['customer_updated_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
											$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_final, '', '%d' );
										} else {
											$customer_final['customer_created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
											$customer_id                           = $dbhandler->insert_row( 'CUSTOMERS', $customer_final );
										}

										if ( isset( $customer_id ) && ! empty( $customer_id ) ) {
											$booking_update_data = array(
												'customer_id'        => $customer_id,
												'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
											);

											$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_update_data, '', '%d' );

											// Transaction data
											$transaction_data['booking_id']  = $booking_id;
											$transaction_data['wc_order_id'] = 0;
											$transaction_data['customer_id'] = $customer_id;

											$transaction_data['transaction_created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();

											$payment_final = $this->sanitize_request( $transaction_data, 'TRANSACTIONS' );

											if ( $payment_final != false && $payment_final != null ) {
												$payment_id = $dbhandler->insert_row( 'TRANSACTIONS', $payment_final );

												if ( ! empty( $payment_id ) ) {
													if ( $is_gift == 1 ) {
														$voucher_expiry_date = $this->bm_get_voucher_expiry_date();
														$voucher_code        = $this->bm_generate_unique_code( $customer_email );

														if ( $voucher_expiry_date && $voucher_code ) {
															$gift_data = array(
																'code'           => $voucher_code,
																'booking_id'     => $booking_id,
																'customer_id'    => $customer_id,
																'transaction_id' => $payment_id,
																'recipient_data' => ! empty( $gift_recipient ) ? $gift_recipient : null,
																'is_gift'        => $is_gift,
																'settings'       => array( 'expiry' => $voucher_expiry_date ),
																'created_at'     => $this->bm_fetch_current_wordpress_datetime_stamp(),
															);

															$gift_data = $this->sanitize_request( $gift_data, 'VOUCHERS' );

															if ( $gift_data != false && $gift_data != null ) {
																$voucher_id = $dbhandler->insert_row( 'VOUCHERS', $gift_data );

																if ( ! empty( $voucher_id ) ) {
																	$booking_update_data = array(
																		'vouchers' => implode( ',', array( $voucher_code ) ),
																		'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
																	);

																	$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_update_data, '', '%d' );
																}
															}
														}
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}

				if ( ! empty( $payment_id ) ) {
					$checkin_id   = 0;
					$checkin_data = array(
						'booking_id'      => $booking_id,
						'qr_token'        => $booking_key,
						'qr_scanned'      => 0,
						'status'          => 'pending',
						'service_expired' => 0,
						'created_at'      => $this->bm_fetch_current_wordpress_datetime_stamp(),
					);

					$checkin_final_data = $this->sanitize_request( $checkin_data, 'CHECKIN' );

					if ( $checkin_final_data != false && $checkin_final_data != null ) {
						$checkin_id = $dbhandler->insert_row( 'CHECKIN', $checkin_final_data );
					}

					$dbhandler->update_global_option_value( 'bm_booking-checkin-id-' . $booking_key, $checkin_id );

					$booking_type   = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );
					$process_status = 'success';

					if ( $booking_type == 'on_request' ) {
						do_action( 'flexibooking_set_process_new_request', $booking_id );
					} elseif ( $booking_type == 'direct' ) {
						do_action( 'flexibooking_set_process_new_order', $booking_id );
					}

					$this->bm_unset_session( 'flexi_current_payment_session' );

					if ( ! empty( $voucher_id ) && $booking_type == 'direct' ) {
						do_action( 'flexibooking_set_process_new_order_voucher', $booking_id );
					}
				}
			}
		}

		$order_data = $dbhandler->get_all_result(
			'BOOKING',
			'*',
			array( 'id' => $booking_id ),
			'results',
			0,
			false,
			null,
			false,
			'',
			'ARRAY_A'
		);
		do_action( 'bm_after_booking_saved', $booking_id, isset( $order_data[0] ) ? $order_data[0] : array() );

		if ( $process_status !== 'success' ) {
			$this->bm_remove_order_data_after_failed_payment( $customer_id, $booking_id );
			$this->bm_unset_session( 'flexi_current_payment_session' );
		}

		return $process_status;
	} // end bm_save_payment_data()


	/**
	 * Process free payment data
	 *
	 * @author Darpan
	 */
	public function bm_process_free_payment_data( $post ) {
		$dbhandler       = new BM_DBhandler();
		$resp            = '';
		$data            = array();
		$checkout_data   = isset( $post['checkout_data'] ) ? $post['checkout_data'] : array();
		$booking_key     = isset( $post['booking_data'] ) ? $post['booking_data'] : array();
		$billing_details = array();
		$transient_data  = array();
		$time_slot       = '';

		if ( ! empty( $post ) && ! empty( $checkout_data ) && ! empty( $booking_key ) ) {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( $booking_key );

			if ( ! empty( $booking_fields ) ) {
				$id           = isset( $booking_fields['service_id'] ) ? $booking_fields['service_id'] : 0;
				$date         = isset( $booking_fields['booking_date'] ) ? $booking_fields['booking_date'] : '';
				$booked_slots = $this->bm_fetch_booked_slot_info_from_booking_data( $booking_fields );
				$from_slot    = ! empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $booked_slots['from'] : '';
				$total_booked = isset( $booking_fields['total_service_booking'] ) ? $booking_fields['total_service_booking'] : 0;

				if ( ! empty( $id ) && ! empty( $date ) && ! empty( $from_slot ) ) {
					$total_time_slots = $this->bm_fetch_total_time_slots_by_service_id( $id );
					$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $id, $date );
					$booked_slots     = $this->bm_fetch_booked_slot_info_from_booking_data( $booking_fields );
					$bookable_extra   = $this->bm_is_selected_extra_service_bookable( $booking_key );
					$slot_info        = $this->bm_fetch_slot_details( $id, $from_slot, $date, $total_time_slots, $total_booked, $is_variable_slot, array( 'slot_min_cap', 'slot_capacity_left_after_booking' ) );

					if ( $total_time_slots == 1 ) {
						$time_slot = $this->bm_fetch_single_time_slot_by_service_id( $id, $date );
					}

					if ( $this->bm_service_is_bookable( $id, $date ) ) {
						if ( isset( $slot_info['slot_capacity_left_after_booking'] ) && ( $slot_info['slot_capacity_left_after_booking'] >= 0 ) ) {
							if ( isset( $slot_info['slot_capacity_left_after_booking'] ) && isset( $slot_info['slot_min_cap'] ) && ( $total_booked % $slot_info['slot_min_cap'] == 0 ) ) {
								if ( ( $bookable_extra ) ) {
									if ( $time_slot !== '-1' ) {
										if ( $time_slot !== '0' ) {
											if ( isset( $checkout_data['other_data']['terms_conditions'] ) ) {
												unset( $checkout_data['other_data']['terms_conditions'] );
											}

											if ( isset( $checkout_data['billing_details'] ) ) {
												$checkout_key = $this->bm_generate_unique_code( '', 'FLEXIC', 15 );

												$transient_data['billing'] = $checkout_data['billing_details'];

												if ( is_array( $checkout_data['billing_details'] ) ) {
													foreach ( $checkout_data['billing_details'] as $key => $value ) {
														$field_name = $dbhandler->get_value( 'FIELDS', 'field_name', $key, 'field_key' );

														if ( ! empty( $field_name ) ) {
															$billing_details[ $field_name ] = $value;
														}
													}

													if ( ! empty( $billing_details ) ) {
														$checkout_data['billing_details'] = $billing_details;
													}
												}
											}

											$transient_data['checkout'] = $checkout_data;
											$dbhandler->bm_save_data_to_transient( $checkout_key, $transient_data, 24 );

											$gift_data = isset( $checkout_data['gift_details'] ) ? $checkout_data['gift_details'] : array();

											if ( isset( $checkout_data['other_data']['is_gift'] ) ) {
												$gift_data['is_gift'] = $checkout_data['other_data']['is_gift'];
												unset( $checkout_data['other_data']['is_gift'] );
											}

											$gift_key = base64_encode( $booking_key );
											$dbhandler->bm_save_data_to_transient( $gift_key, $gift_data, 72 );

											if ( ! empty( $checkout_key ) ) {
												$data['status'] = $this->bm_save_free_payment_data( $booking_key, $checkout_key );
											} else {
												$resp = '<div class="textcenter">' . esc_html__( 'Error Saving Booking Info !!', 'service-booking' ) . '</div>';

												$data['status'] = 'error';
												$data['data']   = wp_kses_post( $resp );
											}
										} else {
											$resp = '<div class="textcenter">' . esc_html__( 'All slots booked, choose another !!', 'service-booking' ) . '</div>';

											$data['status'] = 'error';
											$data['data']   = wp_kses_post( $resp );
										}
									} else {
										$resp = '<div class="textcenter">' . esc_html__( 'No slots available for the service, choose another !!', 'service-booking' ) . '</div>';

										$data['status'] = 'error';
										$data['data']   = wp_kses_post( $resp );
									}
								} else {
									$resp = '<div class="textcenter">' . esc_html__( 'One or more extra services does not have enough capacity, choose another !!', 'service-booking' ) . '</div>';

									$data['status'] = 'error';
									$data['data']   = wp_kses_post( $resp );
								}
							} else {
								$resp = '<div class="textcenter">' . esc_html__( 'Not enough capacity for the selected slot, choose another !!', 'service-booking' ) . '</div>';

								$data['status'] = 'error';
								$data['data']   = wp_kses_post( $resp );
							}
						} else {
							$resp = '<div class="textcenter">' . esc_html__( 'Service does not have enough capacity, choose another !!', 'service-booking' ) . '</div>';

							$data['status'] = 'error';
							$data['data']   = wp_kses_post( $resp );
						}
					} else {
						$resp = '<div class="textcenter">' . esc_html__( 'Service is Not Bookable !!', 'service-booking' ) . '</div>';

						$data['status'] = 'error';
						$data['data']   = wp_kses_post( $resp );
					}
				} else {
					$resp = '<div class="textcenter">' . esc_html__( 'Error Fetching Booking Info !!', 'service-booking' ) . '</div>';

					$data['status'] = 'error';
					$data['data']   = wp_kses_post( $resp );
				}
			} else {
				$resp = '<div class="textcenter">' . esc_html__( 'Error Fetching Booking Info !!', 'service-booking' ) . '</div>';

				$data['status'] = 'error';
				$data['data']   = wp_kses_post( $resp );
			}
		} //end if

		return $data;
	} // end bm_process_free_payment_data()


	/**
	 * Save payment data
	 *
	 * @author Darpan
	 */
	public function bm_save_free_payment_data( $booking_key, $checkout_key ) {
		$dbhandler        = new BM_DBhandler();
		$checkout_data    = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$checkout_data    = isset( $checkout_data['checkout'] ) ? $checkout_data['checkout'] : array();
		$gift_key         = base64_encode( $booking_key );
		$gift_recipient   = $dbhandler->bm_fetch_data_from_transient( $gift_key );
		$is_gift          = isset( $gift_recipient['is_gift'] ) ? $gift_recipient['is_gift'] : 0;
		$billing_details  = isset( $checkout_data['billing_details'] ) ? $checkout_data['billing_details'] : null;
		$customer_details = $this->bm_fetch_customer_info_for_payment_intent( $checkout_key );
		$customer_id      = 0;
		$booking_id       = 0;
		$payment_id       = 0;
		$process_status   = 'error';

		if ( isset( $gift_recipient['is_gift'] ) ) {
			unset( $gift_recipient['is_gift'] );
		}

		if ( ! empty( $checkout_data ) && ! empty( $billing_details ) && ! empty( $customer_details ) ) {
			// Transaction details
			$transaction_id = '';
			$paid_amount    = 0;
			$payment_status = 'free';
			$paid_currency  = $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' );
			$customerID     = $this->bm_add_customer_data_to_payment( $checkout_key, $booking_key, null );
			$customer_name  = isset( $customer_details['name'] ) ? $customer_details['name'] : '';
			$customer_email = isset( $customer_details['email'] ) ? $customer_details['email'] : '';

			// Transaction data
			$transaction_data = array(
				'paid_amount'          => $paid_amount,
				'paid_amount_currency' => $paid_currency,
				'transaction_id'       => $transaction_id,
				'payment_method'       => '',
				'payment_status'       => $payment_status,
				'is_active'            => 1,
			);

			$booking_id = $this->bm_save_booking_data( $booking_key, $checkout_key );

			if ( $booking_id ) {
				$shipping_same_as_billing = isset( $checkout_data['other_data']['shipping_same_as_billing'] ) ? $checkout_data['other_data']['shipping_same_as_billing'] : 0;
				$shipping_details         = array();

				if ( $shipping_same_as_billing == 1 ) {
					foreach ( $billing_details as $key => $value ) {
						$shipping_details[ str_replace( 'billing', 'shipping', $key ) ] = $value;
					}
				} else {
					$shipping_details = ! empty( $checkout_data ) && isset( $checkout_data['shipping_details'] ) ? $checkout_data['shipping_details'] : null;
				}

				$customer_data = array(
					'customer_name'            => $customer_name,
					'customer_email'           => $customer_email,
					'billing_details'          => $billing_details,
					'shipping_details'         => $shipping_details,
					'shipping_same_as_billing' => $shipping_same_as_billing,
					'is_active'                => 1,
				);

				$customer_final = $this->sanitize_request( $customer_data, 'CUSTOMERS' );

				if ( $customer_final != false && $customer_final != null ) {
					$customer_id = $dbhandler->get_value( 'CUSTOMERS', 'id', $customer_email, 'customer_email' );

					if ( ! empty( $customer_id ) ) {
						$customer_final['customer_updated_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
						$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_final, '', '%d' );
					} else {
						$customer_final['stripe_id']           = $customerID;
						$customer_final['customer_created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
						$customer_id                           = $dbhandler->insert_row( 'CUSTOMERS', $customer_final );
					}

					if ( isset( $customer_id ) && ! empty( $customer_id ) ) {
						$booking_update_data = array(
							'customer_id'        => $customer_id,
							'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_update_data, '', '%d' );

						// Transaction data
						$transaction_data['booking_id']  = $booking_id;
						$transaction_data['wc_order_id'] = 0;
						$transaction_data['customer_id'] = $customer_id;

						$transaction_data['transaction_created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();

						$payment_final = $this->sanitize_request( $transaction_data, 'TRANSACTIONS' );

						if ( $payment_final != false && $payment_final != null ) {
							$payment_id = $dbhandler->insert_row( 'TRANSACTIONS', $payment_final );

							if ( ! empty( $payment_id ) ) {
								if ( $is_gift == 1 ) {
									$voucher_expiry_date = $this->bm_get_voucher_expiry_date();
									$voucher_code        = $this->bm_generate_unique_code( $customer_email );

									if ( $voucher_expiry_date && $voucher_code ) {
										$gift_data = array(
											'code'        => $voucher_code,
											'booking_id'  => $booking_id,
											'customer_id' => $customer_id,
											'transaction_id' => $payment_id,
											'recipient_data' => ! empty( $gift_recipient ) ? $gift_recipient : null,
											'is_gift'     => $is_gift,
											'settings'    => array( 'expiry' => $voucher_expiry_date ),
											'created_at'  => $this->bm_fetch_current_wordpress_datetime_stamp(),
										);

										$gift_data = $this->sanitize_request( $gift_data, 'VOUCHERS' );

										if ( $gift_data != false && $gift_data != null ) {
											$voucher_id = $dbhandler->insert_row( 'VOUCHERS', $gift_data );

											if ( ! empty( $voucher_id ) ) {
												$booking_update_data = array(
													'vouchers' => implode( ',', array( $voucher_code ) ),
													'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
												);

												$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_update_data, '', '%d' );
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}

		if ( ! empty( $payment_id ) ) {
			$checkin_id   = 0;
			$checkin_data = array(
				'booking_id'      => $booking_id,
				'qr_token'        => $booking_key,
				'qr_scanned'      => 0,
				'status'          => 'pending',
				'service_expired' => 0,
				'created_at'      => $this->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$checkin_final_data = $this->sanitize_request( $checkin_data, 'CHECKIN' );

			if ( $checkin_final_data != false && $checkin_final_data != null ) {
				$checkin_id = $dbhandler->insert_row( 'CHECKIN', $checkin_final_data );
			}

			$dbhandler->update_global_option_value( 'bm_booking-checkin-id-' . $booking_key, $checkin_id );

			$booking_type   = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );
			$process_status = 'success';

			if ( $booking_type == 'on_request' ) {
				do_action( 'flexibooking_set_process_new_request', $booking_id );
			} elseif ( $booking_type == 'direct' ) {
				do_action( 'flexibooking_set_process_new_order', $booking_id );
			}

			$this->bm_unset_session( 'flexi_current_payment_session' );

			if ( ! empty( $voucher_id ) && $booking_type == 'direct' ) {
				do_action( 'flexibooking_set_process_new_order_voucher', $booking_id );
			}
		}

		$order_data = $dbhandler->get_all_result(
			'BOOKING',
			'*',
			array( 'id' => $booking_id ),
			'results',
			0,
			false,
			null,
			false,
			'',
			'ARRAY_A'
		);
		do_action( 'bm_after_booking_saved', $booking_id, isset( $order_data[0] ) ? $order_data[0] : array() );

		if ( $process_status !== 'success' ) {
			$this->bm_remove_order_data_after_failed_payment( $customer_id, $booking_id );
			$this->bm_unset_session( 'flexi_current_payment_session' );
		}

		return $process_status;
	} // end bm_save_free_payment_data()


	/**
	 * STRIPE process payment
	 *
	 * @author Darpan
	 */
	public function bm_cancel_payment_intent_for_failed_payment( $booking_key, $checkout_key ) {
		$dbhandler         = new BM_DBhandler();
		$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
		$intentStatuses    = array( 'requires_payment_method', 'requires_confirmation', 'requires_action', 'requires_capture' );
		$is_cancelled      = false;

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_data = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_data = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$booking_data   = $dbhandler->bm_fetch_data_from_transient( $booking_key )*/
		$checkout_data  = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
		$checkout_data  = isset( $checkout_data['checkout'] ) ? $checkout_data['checkout'] : array();
		$gift_key       = base64_encode( $booking_key );
		$gift_data      = $dbhandler->bm_fetch_data_from_transient( $gift_key );
		$payment_intent = $payment_processor->getPaymentIntent( base64_decode( $dbhandler->get_global_option_value( 'bm_intent_id' . $booking_key ) ) );

		$booking_data['total_svc_slots']     = isset( $booking_data['total_service_booking'] ) ? $booking_data['total_service_booking'] : 0;
		$booking_data['total_ext_svc_slots'] = isset( $booking_data['total_extra_slots_booked'] ) ? array_sum( explode( ',', $booking_data['total_extra_slots_booked'] ) ) : 0;
		$total_cost                          = isset( $booking_data['total_cost'] ) ? $booking_data['total_cost'] : 0;
		$booking_data['subtotal']            = isset( $booking_data['subtotal'] ) ? $booking_data['subtotal'] : $total_cost;

		if ( isset( $booking_data['total_service_booking'] ) ) {
			unset( $booking_data['total_service_booking'] );
		}

		$transaction_data = array(
			'booking_data'  => ! empty( $booking_data ) ? maybe_serialize( $booking_data ) : null,
			'customer_data' => ! empty( $checkout_data ) ? maybe_serialize( $checkout_data ) : null,
			'gift_data'     => ! empty( $gift_data ) ? maybe_serialize( $gift_data ) : null,
			'booking_key'   => $booking_key,
			'checkout_key'  => $checkout_key,
			'is_refunded'   => 0,
			'refund_status' => 'not_required',
		);

		if ( ! empty( $payment_intent ) ) {
			$payment_status = isset( $payment_intent['status'] ) ? $payment_intent['status'] : '';
			$transaction_id = isset( $payment_intent['id'] ) ? $payment_intent['id'] : '';
			$paid_amount    = isset( $payment_intent['amount'] ) ? $payment_intent['amount'] : '';
			$customerID     = isset( $payment_intent['customer'] ) ? $payment_intent['customer'] : '';
			$paid_currency  = isset( $payment_intent['currency'] ) ? $payment_intent['currency'] : '';
			$customer       = $payment_processor->getCustomer( $customerID );
			$customer_email = isset( $customer->email ) ? $customer->email : '';
			$customer_id    = $dbhandler->get_value( 'CUSTOMERS', 'id', $customer_email, 'customer_email' );

			$transaction_data['customer_id']        = $customer_id;
			$transaction_data['payment_status']     = $payment_status;
			$transaction_data['transaction_id']     = $transaction_id;
			$transaction_data['stripe_customer_id'] = $customerID;
			$transaction_data['amount_currency']    = $paid_currency;
			$transaction_data['amount']             = $paid_amount;
			$transaction_data['created_at']         = $this->bm_fetch_current_wordpress_datetime_stamp();

			if ( ! empty( $transaction_id ) ) {
				if ( in_array( $payment_status, $intentStatuses, true ) ) {
					$cancelled_intent = $payment_processor->cancelPaymentIntent( $transaction_id, $booking_key );

					if ( ! empty( $cancelled_intent ) && isset( $cancelled_intent['status'] ) && $cancelled_intent['status'] == 'canceled' ) {
						$refund_id   = '';
						$charge_data = isset( $cancelled_intent['charges']['data'][0] ) ? $cancelled_intent['charges']['data'][0] : array();

						if ( ! empty( $charge_data ) ) {
							$refund_data = isset( $charge_data['refunds']['data'][0] ) ? $charge_data['refunds']['data'][0] : array();

							if ( ! empty( $refund_data ) ) {
								$refund_id = isset( $refund_data['id'] ) ? $refund_data['id'] : '';
							}
						}

						$transaction_data['is_refunded']   = 1;
						$transaction_data['refund_id']     = ! empty( $refund_id ) ? $refund_id : '';
						$transaction_data['refund_status'] = 'succeeded';

						do_action( 'flexibooking_set_process_failed_order_refund', $booking_key );
						$is_cancelled = true;
					} else {
						$transaction_data['refund_status'] = 'failed';
					}
				} elseif ( ( $payment_status == 'succeeded' ) && ( $paid_amount !== '' ) ) {
					$refund = $payment_processor->refundPaymentIntent( $transaction_id, $paid_amount, $booking_key );

					if ( ! empty( $refund ) && isset( $refund['status'] ) && $refund['status'] == 'succeeded' ) {
						$transaction_data['is_refunded']   = 1;
						$transaction_data['refund_id']     = ! empty( $refund['id'] ) ? $refund['id'] : '';
						$transaction_data['refund_status'] = 'succeeded';

						do_action( 'flexibooking_set_process_failed_order_refund', $booking_key );
						$is_cancelled = true;
					} else {
						$transaction_data['refund_status'] = 'failed';
					}
				}
			}
		}

		$transaction_data['error_message'] = $this->get_payment_error( $booking_key );
		$dbhandler->insert_row( 'FAILED_TRANSACTIONS', $transaction_data );

		return $is_cancelled;
	} // end bm_cancel_payment_intent_for_failed_payment()


	/**
	 * Save a payment error to a transient.
	 *
	 * @param string $booking_key   The booking key.
	 * @param string $error_message The error message.
	 * @param array  $context       Additional context (amount, currency, etc.).
	 */
	public function save_payment_error( $booking_key, $error_message, $context = array() ) {
		$data = array(
			'error'   => $error_message,
			'context' => $context,
			'time'    => time(),
		);
		set_transient( 'bm_pay_err_' . $booking_key, $data, HOUR_IN_SECONDS );
	}


	/**
	 * Retrieve and delete a stored payment error.
	 *
	 * @param string $booking_key
	 * @return array|false The error data or false if none.
	 */
	public function get_payment_error( $booking_key ) {
		$key  = 'bm_pay_err_' . $booking_key;
		$data = get_transient( $key );
		if ( $data ) {
			delete_transient( $key );
		}
		return $data;
	}


	/**
	 * Remove order data after failed payment
	 *
	 * @author Darpan
	 */
	public function bm_remove_order_data_after_failed_payment( $customer_id = 0, $booking_id = 0 ) {
		$dbhandler = new BM_DBhandler();

		if ( $customer_id > 0 ) {
			$transactions = $dbhandler->get_all_result( 'TRANSACTIONS', '*', array( 'customer_id' => $customer_id ), 'results' );

			if ( empty( $transactions ) ) {
				$dbhandler->remove_row( 'CUSTOMERS', 'id', $customer_id, '%d' );
			}
		}

		if ( $booking_id > 0 ) {
			$dbhandler->remove_row( 'BOOKING', 'id', $booking_id, '%d' );
			$dbhandler->remove_row( 'SLOTCOUNT', 'booking_id', $booking_id, '%d' );
			$dbhandler->remove_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, '%d' );
		}
	}//end bm_remove_order_data_after_failed_payment()


	/**
	 * Remove order data
	 *
	 * @author Darpan
	 */
	public function bm_remove_order_data( $booking_id = 0, $customer_id = 0 ) {
		if ( $booking_id > 0 ) {
			$dbhandler = new BM_DBhandler();

			if ( $customer_id <= 0 ) {
				$customer_id = $dbhandler->get_value( 'BOOKING', 'customer_id', $booking_id, 'id' );
			}

			if ( $customer_id > 0 ) {
				$book_count = $dbhandler->bm_count( 'BOOKING', array( 'customer_id' => $customer_id ) );
				if ( $book_count <= 1 ) {
					$dbhandler->remove_row( 'CUSTOMERS', 'id', $customer_id, '%d' );
				}
			}

			$dbhandler->remove_row( 'BOOKING', 'id', $booking_id, '%d' );
			$dbhandler->remove_row( 'SLOTCOUNT', 'booking_id', $booking_id, '%d' );
			$dbhandler->remove_row( 'EXTRASLOTCOUNT', 'booking_id', $booking_id, '%d' );
			$dbhandler->remove_row( 'TRANSACTIONS', 'booking_id', $booking_id, '%d' );

			$mail_records = $dbhandler->get_all_result(
				'EMAILS',
				'id',
				array(
					'module_type' => 'BOOKING',
					'module_id'   => $booking_id,
				),
				'results'
			);

			if ( ! empty( $mail_records ) ) {
				foreach ( $mail_records as $mail_record ) {
					$dbhandler->remove_row( 'EMAILS', 'id', $mail_record->id, '%d' );
				}
			}
		}
	}//end bm_remove_order_data()


	/**
	 * Encrypts sensitive data using AES decryption.
	 *
	 * @param string $to_be_encoded The string to be encoded.
	 * @param string $key          The secret key for encryption.
	 *
	 * @return string|false The encrypted data or false on failure.
	 * @author Darpan
	 */
	public function encrypt_key( $data, $key ) {
		// Generate a random initialization vector (IV)
		$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );

		// Encrypt the data using AES-256-CBC encryption
		$encrypted_data = openssl_encrypt( $data, 'aes-256-cbc', $key, 0, $iv );

		if ( $encrypted_data === false ) {
			return false; // Encryption failed
		}

		// Encode the IV and encrypted data for storage
		$encoded_data = base64_encode( $iv . $encrypted_data );

		return $encoded_data;
	} // end encrypt_key()


	/**
	 * Decrypts sensitive data using AES decryption.
	 *
	 * @param string $encoded_data The encoded and encrypted data.
	 * @param string $key          The secret key for decryption.
	 *
	 * @return string|false The decrypted data or false on failure.
	 * @author Darpan
	 */
	public function decrypt_key( $encoded_data, $key ) {
		// Decode the IV and encrypted data
		$decoded_data = base64_decode( $encoded_data );

		// Extract the IV
		$iv        = substr( $decoded_data, 0, openssl_cipher_iv_length( 'aes-256-cbc' ) );
		$encrypted = substr( $decoded_data, openssl_cipher_iv_length( 'aes-256-cbc' ) );

		// Decrypt the data using AES-256-CBC decryption
		$decrypted_data = openssl_decrypt( $encrypted, 'aes-256-cbc', $key, 0, $iv );

		if ( $decrypted_data === false ) {
			return false; // Decryption failed
		}

		return $decrypted_data;
	} // end decrypt_key()


	/**
	 * Fetch book on request transactions
	 *
	 * @author Darpan
	 */
	public function bm_fetch_book_on_request_transactions() {
		$dbhandler    = new BM_DBhandler();
		$transactions = $dbhandler->get_all_result(
			'TRANSACTIONS',
			'*',
			array(
				'payment_status' => 'requires_capture',
				'is_active'      => 1,
			),
			'results'
		);

		return $transactions;
	} // end bm_fetch_book_on_request_transactions()


	/**
	 * Fetch paid bookings with processing status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_paid_bookings_with_processing_status() {
		$where = $this->bm_get_payment_status_condition();
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);

		$columns = 'b.id, b.booking_date, b.booking_slots';

		$bookings = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results' );

		return $bookings;
	}//end bm_fetch_paid_bookings_with_processing_status()


	/**
	 * Fetch unpaid bookings with processing status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_unpaid_bookings_with_processing_status() {
		$where = $this->bm_get_payment_status_condition( 'pending' );
		$where = array_merge(
			$where,
			array(
				'b.is_active' => array( '=' => 1 ),
			)
		);

		$tables = array( 'BOOKING', 'b' );
		$joins  = array(
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
				'type'  => 'INNER',
			),
		);

		$columns = 'b.id, b.booking_date, b.booking_slots';

		$bookings = ( new BM_DBhandler() )->get_results_with_join( $tables, $columns, $joins, $where, 'results' );

		return $bookings;
	}//end bm_fetch_unpaid_bookings_with_processing_status()


	/**
	 * Fetch trnsactions with pending payment status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_transactions_with_pending_payment() {
		$transactions = ( new BM_DBhandler() )->get_all_result(
			'TRANSACTIONS',
			'*',
			array(
				'payment_status' => 'pending',
				'is_active'      => 1,
			),
			'results'
		);

		return $transactions;
	} // end bm_fetch_transactions_with_pending_payment()


	/**
	 * Fetch bookings with free payment status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_free_bookings() {
		$dbhandler = new BM_DBhandler();
		$bookings  = $dbhandler->get_all_result(
			'BOOKING',
			'*',
			array(
				'total_cost' => 0,
				'is_active'  => 1,
			),
			'results'
		);

		return $bookings;
	} // end bm_fetch_free_bookings()


	/**
	 * Mark book on request transactions as approved
	 *
	 * @author Darpan
	 */
	public function bm_approve_book_on_request_transaction( $booking_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$approved  = false;

		if ( ! empty( $booking_id ) ) {
			$capture_amount  = $dbhandler->get_value( 'BOOKING', 'total_cost', $booking_id, 'id' );
			$paymentIntentId = $dbhandler->get_value( 'TRANSACTIONS', 'transaction_id', $booking_id, 'booking_id' );
			$paymentStatus   = $dbhandler->get_value( 'TRANSACTIONS', 'payment_status', $booking_id, 'booking_id' );
			$is_active       = $dbhandler->get_value( 'TRANSACTIONS', 'is_active', $booking_id, 'booking_id' );

			if ( defined( 'STRIPE_SECRET_KEY' ) && ! empty( $paymentIntentId ) && ! empty( $capture_amount ) && ( $paymentStatus == 'requires_capture' ) && ( $is_active == 1 ) ) {
				$payment_processor = new Booking_Management_Process_Payment( STRIPE_SECRET_KEY );
				$transaction_id    = $dbhandler->get_value( 'TRANSACTIONS', 'id', $booking_id, 'booking_id' );
				$booking_is_active = $dbhandler->get_value( 'BOOKING', 'is_active', $booking_id, 'id' );
				$booking_type      = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );

				if ( $payment_processor->isConnected() && ( $booking_type == 'on_request' ) && ( $booking_is_active == 1 ) && ! empty( $transaction_id ) ) {
					$capture_amount  = $capture_amount * 100;
					$approve_payment = $payment_processor->capturePayment( $paymentIntentId, $capture_amount );

					if ( $approve_payment ) {
						$approved       = true;
						$payment_status = isset( $approve_payment['status'] ) ? $approve_payment['status'] : '';

						$transaction_data = array(
							'payment_status'         => $payment_status,
							'transaction_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$booking_data = array(
							'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
						);

						$one = $dbhandler->update_row( 'TRANSACTIONS', 'id', $transaction_id, $transaction_data, '', '%d' );
						$two = $dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_data, '', '%d' );
					}
				}
			}
		}

		return $approved;
	} // end bm_approve_book_on_request_transaction()


	/**
	 * Cancel and refund book on request order
	 *
	 * @author Darpan
	 */
	public function bm_cancel_and_refund_order( $booking_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$cancelled = apply_filters( 'flexibooking_cancel_booking', $booking_id );

		if ( $cancelled ) {
			do_action( 'flexibooking_set_process_cancel_order', $booking_id );
			$refund_id = apply_filters( 'flexibooking_refund_cancelled_order', $booking_id );

			if ( ! empty( $refund_id ) ) {
				do_action( 'flexibooking_set_process_order_refund', $booking_id, $refund_id );
			}

			$dbhandler->update_global_option_value( 'bm_is_booking_cancelled-' . $booking_id, 1 );
		}
	}//end bm_cancel_and_refund_order()



	/**
	 * Cancel an order
	 *
	 * @author Darpan
	 */
	public function bm_cancel_flexi_order( $booking_id = 0 ) {
		$cancelled = apply_filters( 'flexibooking_cancel_booking', $booking_id );
		return $cancelled;
	}//end bm_cancel_flexi_order()



	/**
	 * Mark status of an order as refunded
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_order_status_as_refunded( $booking_id = 0, $refund_id = '' ) {
		$updated = apply_filters( 'flexibooking_update_status_as_refunded', $booking_id, $refund_id );
		return $updated;
	}//end bm_update_flexi_order_status_as_refunded()


	/**
	 * Mark status of an order as completed
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_order_status_as_completed( $booking_id = 0 ) {
		$updated = apply_filters( 'flexibooking_update_status_as_completed', $booking_id );
		return $updated;
	}//end bm_update_flexi_order_status_as_completed()


	/**
	 * Mark status of an order as processing
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_order_status_as_processing( $booking_id = 0 ) {
		$updated = apply_filters( 'flexibooking_update_status_as_processing', $booking_id );
		return $updated;
	}//end bm_update_flexi_order_status_as_processing()


	/**
	 * Mark status of an order as on hold
	 *
	 * @author Darpan
	 */
	public function bm_update_flexi_order_status_as_on_hold( $booking_id = 0 ) {
		$updated = apply_filters( 'flexibooking_update_status_as_on_hold', $booking_id );
		return $updated;
	}//end bm_update_flexi_order_status_as_on_hold()


	/**
	 * Mark processing orders as complete
	 *
	 * @author Darpan
	 */
	public function bm_mark_processing_orders_as_complete( $booking_id = 0 ) {
		$status = apply_filters( 'flexibooking_mark_processing_orders_as_complete', $booking_id );
		return $status;
	}//end bm_mark_processing_orders_as_complete()


	/**
	 * Mark free orders as complete
	 *
	 * @author Darpan
	 */
	public function bm_mark_free_orders_as_complete( $booking_id = 0 ) {
		$status = apply_filters( 'bm_mark_free_orders_as_complete', $booking_id );
		return $status;
	}//end bm_mark_free_orders_as_complete()


	/**
	 * Approve book on request order
	 *
	 * @author Darpan
	 */
	public function bm_approve_pending_book_on_request_order( $booking_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$approved  = $this->bm_approve_book_on_request_transaction( $booking_id );

		if ( $approved ) {
			do_action( 'flexibooking_set_process_approved_order', $booking_id );
			$voucher_code = $dbhandler->get_value( 'VOUCHERS', 'code', $booking_id, 'booking_id' );

			if ( ! empty( $voucher_code ) ) {
				do_action( 'flexibooking_set_process_new_order_voucher', $booking_id );
			}

			$dbhandler->update_global_option_value( 'bm_is_book_on_request_approved-' . $booking_id, 1 );
		}
	}//end bm_approve_pending_book_on_request_order()


    /**
     * Check if cart order is still bookable in payment page
     *
     * @author Darpan
     */
    public function bm_check_if_cart_order_is_still_bookable( $booking_key, $gift = false ) {
         $dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

        /**$booking_fields = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
        $bookable = true;
        if ( $gift ) {
            return $bookable;
        }

		if ( ! empty( $booking_fields ) ) {
			$id   = isset( $booking_fields['service_id'] ) ? $booking_fields['service_id'] : 0;
			$date = isset( $booking_fields['booking_date'] ) ? $booking_fields['booking_date'] : '';

			$total_service_booked = isset( $booking_fields['total_service_booking'] ) ? $booking_fields['total_service_booking'] : 0;

			if ( ! empty( $id ) && ! empty( $date ) ) {
				if ( $this->bm_service_is_bookable( $id, $date ) ) {
					$total_time_slots = $this->bm_fetch_total_time_slots_by_service_id( $id );
					$stopsales        = $this->bm_fetch_service_stopsales_by_service_id( $id, $date );
					$timezone         = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
					$now              = new DateTime( 'now', new DateTimeZone( $timezone ) );

					if ( ! empty( $stopsales ) ) {
						$stopSalesHours   = floor( $stopsales );
						$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

						if ( $this->bm_has_dynamic_stopsales_for_date( $id, $date ) ) {
							$endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
						} else {
							$endDateTime = clone $now;
						}

						$endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
						$endDateTime = $endDateTime->format( 'Y-m-d H:i' );
					}

					if ( $total_time_slots == 1 ) {
						$time_slot = $this->bm_fetch_single_time_slot_by_service_id( $id, $date );
					}

					$is_variable_slot = $this->bm_check_if_variable_slot_by_service_id_and_date( $id, $date );
					$booked_slots     = $this->bm_fetch_booked_slot_info_from_booking_data( $booking_fields );
					$from_slot        = ! empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $booked_slots['from'] : '';
					$startSlot        = new DateTime( $date . ' ' . $from_slot, new DateTimeZone( $timezone ) );
					$startSlot        = $startSlot->format( 'Y-m-d H:i' );

					if ( ! empty( $from_slot ) ) {
						$slot_info = $this->bm_fetch_slot_details( $id, $from_slot, $date, $total_time_slots, $total_service_booked, $is_variable_slot, array( 'slot_min_cap', 'slot_capacity_left_after_booking' ) );

						if ( ( ! empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
							$bookable = false;
						} elseif ( isset( $slot_info['slot_capacity_left_after_booking'] ) && ( $slot_info['slot_capacity_left_after_booking'] < 0 ) ) {
							$bookable = false;
						} elseif ( isset( $slot_info['slot_capacity_left_after_booking'] ) && isset( $slot_info['slot_min_cap'] ) && ( $slot_info['slot_capacity_left_after_booking'] >= 0 ) && ( $total_service_booked % $slot_info['slot_min_cap'] != 0 ) ) {
							$bookable = false;
						} elseif ( isset( $time_slot ) && $time_slot == '-1' ) {
								$bookable = false;
						} elseif ( isset( $time_slot ) && $time_slot == '0' ) {
							$bookable = false;
						}
					} else {
						$bookable = false;
					}
				} else {
					$bookable = false;
				}
			} else {
				$bookable = false;
			}
		} else {
			$bookable = false;
		}

		return $bookable;
	}//end bm_check_if_cart_order_is_still_bookable()


	/**
	 * Start a session
	 *
	 * @author Darpan
	 */
	public function bm_start_session_with_expiry_old( $key, $expiry_time ) {
		if ( ! session_id() ) {
			session_start();
		}
		$_SESSION[ $key ] = time() + $expiry_time;
	} //end bm_start_session_with_expiry()


	/**
	 * Start a session
	 *
	 * @author Darpan
	 */
	public function bm_start_session_with_expiry( $key, $expiry_time ) {
		set_transient( $key, true, $expiry_time );
	}//end bm_start_session_with_expiry()


	/**
	 * Check if a session has expired
	 *
	 * @author Darpan
	 */
	public function bm_is_session_expired_old( $key ) {
		if ( ! session_id() ) {
			session_start();
		}

		if ( ! isset( $_SESSION[ $key ] ) ) {
			return true;
		}

		if ( isset( $_SESSION[ $key ] ) && ( $_SESSION[ $key ] < time() ) ) {
			unset( $_SESSION[ $key ] );
			return true;
		}

		return false;
	} //end bm_is_session_expired()


	/**
	 * Check if a session has expired
	 *
	 * @author Darpan
	 */
	public function bm_is_session_expired( $key ) {
		return get_transient( $key ) === false;
	}//end bm_is_session_expired()


	/**
	 * Unset a session
	 *
	 * @author Darpan
	 */
	public function bm_unset_session( $key ) {
		if ( ! session_id() ) {
			session_start();
		}

		if ( isset( $_SESSION[ $key ] ) ) {
			unset( $_SESSION[ $key ] );
		}
	}//end bm_unset_session()


	/**
	 * Generate secret key
	 *
	 * @author Darpan
	 */
	public function bm_generate_secret_key( $len = 64 ) {
		$secret  = '';
		$charset = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_-+=`~,<>.[]: |';

		for ( $x = 1; $x <= random_int( 1, 10 ); $x++ ) {
			$charset = str_shuffle( $charset );
		}

		for ( $s = 1; $s <= $len; $s++ ) {
			$secret .= substr( $charset, random_int( 0, 86 ), 1 );
		}

		return wp_kses_post( $secret );
	}//end bm_generate_secret_key()


	/**
	 * Create a random string
	 *
	 * @author Darpan
	 */
	public function bm_create_random_string( $length ) {
		$str       = 'abcdefghijklmnopqrstuvwzyz';
		$str1      = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$str2      = '0123456789';
		$shuffled  = str_shuffle( $str );
		$shuffled1 = str_shuffle( $str1 );
		$shuffled2 = str_shuffle( $str2 );
		$total     = $shuffled . $shuffled1 . $shuffled2;
		$shuffled3 = str_shuffle( $total );
		$string    = substr( $shuffled3, 0, $length );

		return wp_kses_post( $string );
	}//end bm_create_random_string()



	public function bm_generate_unique_code( $input_string = '', $initials = 'FLEXI', $length = 8 ) {
		if ( empty( $input_string ) ) {
			$input_string = $this->bm_create_random_string( 5 );
		}

		$normalized_string = strtolower( trim( $input_string ) );
		$hash              = md5( $normalized_string . time() . wp_rand() );
		$unique_code       = substr( $hash, 0, $length );
		$final_code        = strtoupper( $initials . $unique_code );

		return $final_code;
	}//end bm_generate_unique_code()



	/**
	 * Check booking request type in payment page
	 *
	 * @author Darpan
	 */
	public function bm_current_request_type( $booking_key ) {
		$dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_details = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_details = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$booking_details = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
		$request_type = '';

		if ( ! empty( $booking_details ) ) {
			$service_id   = isset( $booking_details['service_id'] ) ? $booking_details['service_id'] : 0;
			$booking_date = isset( $booking_details['booking_date'] ) ? $booking_details['booking_date'] : '';

			if ( ! empty( $service_id ) && ! empty( $booking_date ) ) {
				$stopsales    = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $booking_date );
				$saleswitch   = $this->bm_fetch_service_saleswitch_by_service_id( $service_id, $booking_date );
				$booked_slots = $this->bm_fetch_booked_slot_info_from_booking_data( $booking_details );
				$from_slot    = ! empty( $booked_slots ) && isset( $booked_slots['from'] ) ? $booked_slots['from'] : '';

				$is_book_on_request_only = $this->bm_check_if_book_on_request_only( $service_id );

				if ( ! empty( $from_slot ) ) {
					$timezone = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
					$now      = new DateTime( 'now', new DateTimeZone( $timezone ) );
					$slotTime = new DateTime( $booking_date . ' ' . $from_slot, new DateTimeZone( $timezone ) );
					$slotTime = $slotTime->format( 'Y-m-d H:i' );

					if ( ! empty( $slotTime ) ) {
						if ( ! empty( $stopsales ) ) {
							$stopSalesHours   = floor( $stopsales );
							$stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

							if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $booking_date ) ) {
								$stopsalesDateTime = new DateTime( $booking_date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
							} else {
								$stopsalesDateTime = clone $now;
							}

							$stopsalesDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
							$endStopsales = $stopsalesDateTime->format( 'Y-m-d H:i' );
						}

						if ( ( ! empty( $stopsales ) && ( strtotime( $endStopsales ) > strtotime( $slotTime ) ) ) ) {
							$request_type = '';
						} elseif ( isset( $slot_info['capacity_left'] ) && ( $slot_info['capacity_left'] <= 0 ) ) {
							$request_type = '';
						} else {
							$booked_product = $this->bm_fetch_booked_service_info_for_stripe_payment_intent( $booking_key );

							$amount      = ! empty( $booked_product ) && isset( $booked_product['amount'] ) ? floatval( $booked_product['amount'] ) * 100 : 0;
							$currency    = ! empty( $booked_product ) && isset( $booked_product['currency'] ) ? $booked_product['currency'] : '';
							$description = ! empty( $booked_product ) && isset( $booked_product['description'] ) ? $booked_product['description'] : '';

							if ( ( $amount > 0 ) && ! empty( $currency ) && ! empty( $description ) ) {
								if ( ( $is_book_on_request_only == 1 ) ) {
									$request_type = 'on_request';
								} elseif ( ! empty( $stopsales ) && ! empty( $saleswitch ) ) {
										$saleswitchHours   = floor( $saleswitch );
										$saleswitchMinutes = ( $saleswitch - $saleswitchHours ) * 60;
										$endSaleswitch     = $stopsalesDateTime->add( new DateInterval( "PT{$saleswitchHours}H{$saleswitchMinutes}M" ) );
										$endSaleswitch     = $endSaleswitch->format( 'Y-m-d H:i' );

									if ( ( strtotime( $slotTime ) > strtotime( $endSaleswitch ) ) ) {
										$request_type = 'direct';
									} else {
										$request_type = 'on_request';
									}
								} elseif ( empty( $stopsales ) && ! empty( $saleswitch ) ) {
									$saleswitchHours   = floor( $saleswitch );
									$saleswitchMinutes = ( $saleswitch - $saleswitchHours ) * 60;
									$endSaleswitch     = $now->add( new DateInterval( "PT{$saleswitchHours}H{$saleswitchMinutes}M" ) );
									$endSaleswitch     = $endSaleswitch->format( 'Y-m-d H:i' );

									if ( ( strtotime( $slotTime ) > strtotime( $endSaleswitch ) ) ) {
										$request_type = 'direct';
									} else {
										$request_type = 'on_request';
									}
								} else {
									$request_type = 'direct';
								}
							}
						}
					}
				}
			}
		}

		return wp_kses_post( $request_type );
	}//end bm_current_request_type()


	/**
	 * Check if an extra service is bookable
	 *
	 * @author Darpan
	 */
	public function bm_is_selected_extra_service_bookable( $booking_key ) {
		$dbhandler      = new BM_DBhandler();
		$is_bookable    = true;
		$extra_cap_left = array();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		/**$booking_fields = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/

		if ( ! empty( $booking_fields ) ) {
			$booking_date          = isset( $booking_fields['booking_date'] ) ? $booking_fields['booking_date'] : '';
			$extra_services_booked = isset( $booking_fields['extra_svc_booked'] ) ? explode( ',', $booking_fields['extra_svc_booked'] ) : array();
			$extra_slots_booked    = isset( $booking_fields['total_extra_slots_booked'] ) ? explode( ',', $booking_fields['total_extra_slots_booked'] ) : array();

			if ( ! empty( $booking_date ) && ! empty( $extra_services_booked ) && ! empty( $extra_slots_booked ) && ( is_array( $extra_services_booked ) ) && ( is_array( $extra_slots_booked ) ) ) {
				foreach ( $extra_services_booked as $key => $extra_id ) {
					$extra_max_cap          = $dbhandler->get_value( 'EXTRA', 'extra_max_cap', $extra_id, 'id' );
					$extra_cap_left[ $key ] = $this->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date( $extra_id, $extra_max_cap, $extra_slots_booked[ $key ], $booking_date );
				}

				if ( ! empty( $extra_cap_left ) && is_array( $extra_cap_left ) ) {
					foreach ( $extra_cap_left as $cap_left ) {
						if ( ( $cap_left < 0 ) ) {
							$is_bookable = false;
							break;
						}
					}
				}
			}
		}

		return $is_bookable;
	}//end bm_is_selected_extra_service_bookable()


	/**
	 * Check if an extra service is linked with woocommerce
	 *
	 * @author Darpan
	 */
	public function bm_is_selected_extra_service_wc_linked( $booking_key ) {
		$dbhandler = new BM_DBhandler();

		if ( $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ) {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( 'discounted_' . $booking_key );
		} else {
			$booking_fields = $dbhandler->bm_fetch_data_from_transient( $booking_key );
		}

		if ( ! empty( $booking_fields ) ) {
			$extra_services_booked = ! empty( $booking_fields['extra_svc_booked'] ) ? explode( ',', $booking_fields['extra_svc_booked'] ) : array();

			if ( ! empty( $extra_services_booked ) && ( is_array( $extra_services_booked ) ) ) {
				foreach ( $extra_services_booked as $key => $extra_id ) {
					$extra_max_cap = $dbhandler->get_value( 'EXTRA', 'svcextra_wc_product', $extra_id, 'id' );

					if ( $extra_max_cap <= 0 ) {
						return false;
					}
				}
			}
		}

		return true;
	}//end bm_is_selected_extra_service_wc_linked()


	/**
	 * Get key value pairs of payment statuses
	 *
	 * @author Darpan
	 */
	public function bm_fetch_payment_statuses( $key = '', $exclude = array() ) {
		$statusList = array(
			'pending'   => 'Pending',
			'succeeded' => 'Paid',
			'failed'    => 'Failed',
			'free'      => 'Free',
			'cancelled' => 'Cancelled',
			'refunded'  => 'Refunded',
		);

		if ( ! empty( $key ) ) {
			$key = strtolower( $key );
			return isset( $statusList[ $key ] ) ? $statusList[ $key ] : '';
		}

		if ( ! empty( $exclude ) && is_array( $exclude ) ) {
			$statusList = array_diff( $statusList, $exclude );
		}

		return $statusList;
	}//end bm_fetch_payment_statuses()


	/**
	 * Get all WooCommerce order statuses
	 *
	 * @author Darpan
	 */
	public function get_all_woocommerce_order_statuses() {
		$woocommerceservice = new WooCommerceService();
		$order_statuses     = array();

		if ( $woocommerceservice->is_enabled() ) {
			$order_statuses = wc_get_order_statuses();
		}

		return $order_statuses;
	}//end get_all_woocommerce_order_statuses()


	/**
	 * Get modified woocommerce order status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_status_string( $order_status ) {
		switch ( $order_status ) {
			case 'wc-pending':
				$status = 'pending';
				break;
			case 'wc-processing':
				$status = 'processing';
				break;
			case 'wc-on-hold':
				$status = 'on_hold';
				break;
			case 'wc-completed':
				$status = 'succeeded';
				break;
			case 'wc-cancelled':
				$status = 'cancelled';
				break;
			case 'wc-refunded':
				$status = 'refunded';
				break;
			case 'wc-failed':
				$status = 'failed';
				break;
			case 'wc-checkout-draft':
				$status = 'draft';
				break;
			default:
				$status = $order_status;
		} //end switch

		return $status;
	}//end bm_fetch_order_status_string()


	/**
	 * Get key value pairs of order statuses
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_status_key_value( $status = '', $exclude = array() ) {
		$statusList = array(
			'pending'    => esc_html__( 'Pending', 'service-booking' ),
			'processing' => esc_html__( 'Processing', 'service-booking' ),
			'on_hold'    => esc_html__( 'On Hold', 'service-booking' ),
			'succeeded'  => esc_html__( 'Completed', 'service-booking' ),
			'cancelled'  => esc_html__( 'Cancelled', 'service-booking' ),
			'refunded'   => esc_html__( 'Refunded', 'service-booking' ),
			'failed'     => esc_html__( 'Failed', 'service-booking' ),
			'draft'      => esc_html__( 'Draft', 'service-booking' ),
		);

		if ( ! empty( $status ) ) {
			$status = strtolower( $status );
			return isset( $statusList[ $status ] ) ? $statusList[ $status ] : '';
		}

		if ( ! empty( $exclude ) && is_array( $exclude ) ) {
			$statusList = array_diff( $statusList, $exclude );
		}

		return $statusList;
	}//end bm_fetch_order_status_key_value()


	/**
	 * Fetch process type name from type id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_process_type_name_by_type_id( $type_id = -1 ) {
		$type_name = '';

		if ( $type_id == 0 ) {
			$type_name = esc_html__( 'Frontend new order', 'service-booking' );
		} elseif ( $type_id == 1 ) {
			$type_name = esc_html__( 'Backend new order', 'service-booking' );
		} elseif ( $type_id == 2 ) {
			$type_name = esc_html__( 'Order refund', 'service-booking' );
		} elseif ( $type_id == 3 ) {
			$type_name = esc_html__( 'Order cancel', 'service-booking' );
		} elseif ( $type_id == 4 ) {
			$type_name = esc_html__( 'Order approval', 'service-booking' );
		} elseif ( $type_id == 5 ) {
			$type_name = esc_html__( 'Order failure', 'service-booking' );
		} elseif ( $type_id == 6 ) {
			$type_name = esc_html__( 'Gift voucher', 'service-booking' );
		} elseif ( $type_id == 7 ) {
			$type_name = esc_html__( 'Frontend new request', 'service-booking' );
		} elseif ( $type_id == 8 ) {
			$type_name = esc_html__( 'Backend new request', 'service-booking' );
		} elseif ( $type_id == 9 ) {
			$type_name = esc_html__( 'Redeem voucher', 'service-booking' );
		}

		return $type_name;
	}//end bm_fetch_process_type_name_by_type_id()


	/**
	 * Fetch template type name from type id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_template_type_name_by_type_id( $type_id = -1 ) {
		$type_name = '';

		if ( $type_id == 0 ) {
			$type_name = esc_html__( 'New order from frontend (notify customer)', 'service-booking' );
		} elseif ( $type_id == 1 ) {
			$type_name = esc_html__( 'New order from backend (notify customer)', 'service-booking' );
		} elseif ( $type_id == 2 ) {
			$type_name = esc_html__( 'Refund order (notify customer)', 'service-booking' );
		} elseif ( $type_id == 3 ) {
			$type_name = esc_html__( 'Cancel order (notify customer)', 'service-booking' );
		} elseif ( $type_id == 4 ) {
			$type_name = esc_html__( 'Approved order (notify customer)', 'service-booking' );
		} elseif ( $type_id == 5 ) {
			$type_name = esc_html__( 'New order (notify admin)', 'service-booking' );
		} elseif ( $type_id == 6 ) {
			$type_name = esc_html__( 'Cancel order (notify admin)', 'service-booking' );
		} elseif ( $type_id == 7 ) {
			$type_name = esc_html__( 'Refund order (notify admin)', 'service-booking' );
		} elseif ( $type_id == 8 ) {
			$type_name = esc_html__( 'Approved order (notify admin)', 'service-booking' );
		} elseif ( $type_id == 9 ) {
			$type_name = esc_html__( 'Failed order (notify customer)', 'service-booking' );
		} elseif ( $type_id == 10 ) {
			$type_name = esc_html__( 'Failed order (notify admin)', 'service-booking' );
		} elseif ( $type_id == 11 ) {
			$type_name = esc_html__( 'Gift voucher (notify recipient)', 'service-booking' );
		} elseif ( $type_id == 12 ) {
			$type_name = esc_html__( 'New request from frontend (notify customer)', 'service-booking' );
		} elseif ( $type_id == 13 ) {
			$type_name = esc_html__( 'New request from frontend (notify customer)', 'service-booking' );
		} elseif ( $type_id == 14 ) {
			$type_name = esc_html__( 'New request (notify admin)', 'service-booking' );
		} elseif ( $type_id == 15 ) {
			$type_name = esc_html__( 'Redeem voucher (notify admin)', 'service-booking' );
		} elseif ( $type_id == 16 ) {
			$type_name = esc_html__( 'Redeem voucher (notify customer)', 'service-booking' );
		}

		return $type_name;
	}//end bm_fetch_template_type_name_by_type_id()


	/**
	 * Fetch event type value from module name
	 *
	 * @author Darpan
	 */
	public function bm_fetch_event_type_from_module_name( $module = '' ) {
		$type = '';

		if ( $module == 'SERVICE' ) {
			$type = '0';
		} elseif ( $module == 'CATEGORY' ) {
			$type = '1';
		} elseif ( $module == 'ORDER_STATUS' ) {
			$type = '2';
		} elseif ( $module == 'PAYMENT_STATUS' ) {
			$type = '3';
		}

		return $type;
	}//end bm_fetch_event_type_from_module_name()


	/**
	 * Fetch event type value from module name
	 *
	 * @author Darpan
	 */
	public function bm_fetch_event_module_name_from_type( $type = '' ) {
		$module = '';

		if ( $type == '0' ) {
			$module = 'SERVICE';
		} elseif ( $type == '1' ) {
			$module = 'CATEGORY';
		} elseif ( $type == '2' ) {
			$module = 'ORDER_STATUS';
		} elseif ( $type == '3' ) {
			$module = 'PAYMENT_STATUS';
		}

		return $module;
	}//end bm_fetch_event_module_name_from_type()


	/**
	 * Fetch notification event type value response
	 *
	 * @author Darpan
	 */
	public function bm_fetch_event_type_value_html( $type = '', $process_id = 0, $array_key = -1 ) {
		$module = $this->bm_fetch_event_module_name_from_type( $type );
		$resp   = $this->bm_fetch_trigger_condition_option_values_for_event_notification_type( $module, $process_id, $array_key );

		return $resp;
	}//end bm_fetch_event_type_value_html()


	/**
	 * Get response for event notification type
	 *
	 * @author Darpan
	 */
	public function bm_fetch_trigger_condition_option_values_for_event_notification_type( $module = '', $process_id = 0, $array_key = -1 ) {
		$dbhandler = new BM_DBhandler();
		$resp      = '';
		$values    = array();

		switch ( $module ) {
			case ( $module == 'SERVICE' ):
				$services = $dbhandler->get_all_result( $module, '*', 1, 'results' );

				if ( ! empty( $services ) && is_array( $services ) ) {
					foreach ( $services as $service ) {
						$id   = isset( $service->id ) ? $service->id : '';
						$name = isset( $service->service_name ) ? $service->service_name : '';

						$values[ $id ] = mb_strimwidth( $name, 0, 30, '...' );
					}
				}
				break;
			case ( $module == 'CATEGORY' ):
				$categories = $dbhandler->get_all_result( $module, '*', 1, 'results' );

				if ( ! empty( $categories ) && is_array( $categories ) ) {
					foreach ( $categories as $category ) {
						$id   = isset( $category->id ) ? $category->id : '';
						$name = isset( $category->cat_name ) ? $category->cat_name : '';

						$values[ $id ] = mb_strimwidth( $name, 0, 30, '...' );
					}
				}
				break;
			case ( $module == 'ORDER_STATUS' ):
				$values = $this->bm_fetch_order_status_key_value();
				break;
			case ( $module == 'PAYMENT_STATUS' ):
				$values = $this->bm_fetch_payment_statuses();
				break;
			default:
				break;
		} //end switch

		if ( ! empty( $values ) && is_array( $values ) && array_filter( $values ) ) {
			if ( empty( $process_id ) ) {
				foreach ( $values as $key => $value ) {
					$resp .= '<option value="' . $key . '">' . $value . '</option>';
				} //end foreach
			} elseif ( $process_id > 0 && $array_key >= 0 ) {
				$process    = $dbhandler->get_row( 'EVENTNOTIFICATION', $process_id );
				$conditions = isset( $process->trigger_conditions ) ? maybe_unserialize( $process->trigger_conditions ) : array();
				$con_values = isset( $conditions['values'][ $array_key ] ) ? $conditions['values'][ $array_key ] : array();

				foreach ( $values as $key => $value ) {
					$resp .= '<option value="' . $key . '" ' . ( in_array( $key, $con_values ) ? 'selected' : '' ) . '>' . $value . '</option>';
				} //end foreach
			}
		} //end if

		return $resp;
	}//end bm_fetch_trigger_condition_option_values_for_event_notification_type()


	/**
	 * Check if active template exists of a specific type
	 *
	 * @author Darpan
	 */
	public function bm_check_active_email_template_of_a_specific_type( $type = -1 ) {
		$dbhandler           = new BM_DBhandler();
		$has_active_template = true;

		if ( $type != -1 ) {
			$templates = $dbhandler->get_all_result(
				'EMAIL_TMPL',
				'*',
				array(
					'type'   => $type,
					'status' => 1,
				),
				'results'
			);

			if ( empty( $templates ) ) {
				$has_active_template = false;
			}
		}

		return $has_active_template;
	}//end bm_check_active_email_template_of_a_specific_type()


	/**
	 * Fetch id of active email template of a specific type if any
	 *
	 * @author Darpan
	 */
	public function bm_fetch_active_email_template_id_of_a_specific_type( $type = -1 ) {
		$dbhandler   = new BM_DBhandler();
		$template_id = 0;

		if ( $type != -1 ) {
			$template_id = $dbhandler->get_all_result(
				'EMAIL_TMPL',
				'id',
				array(
					'type'   => $type,
					'status' => 1,
				),
				'var'
			);
		}

		return $template_id;
	}//end bm_fetch_active_email_template_id_of_a_specific_type()


	/**
	 * Check if active process exists of a specific type
	 *
	 * @author Darpan
	 */
	public function bm_check_active_process_of_a_specific_type( $type = -1 ) {
		$dbhandler          = new BM_DBhandler();
		$has_active_process = true;

		if ( $type != -1 ) {
			$processes = $dbhandler->get_all_result(
				'EVENTNOTIFICATION',
				'*',
				array(
					'type'   => $type,
					'status' => 1,
				),
				'results'
			);

			if ( empty( $processes ) ) {
				$has_active_process = false;
			}
		}

		return $has_active_process;
	}//end bm_check_active_process_of_a_specific_type()


	/**
	 * Fetch id of active process of a specific type if any
	 *
	 * @author Darpan
	 */
	public function bm_fetch_active_process_id_of_a_specific_type( $type = -1 ) {
		$dbhandler  = new BM_DBhandler();
		$process_id = 0;

		if ( $type != -1 ) {
			$process_id = $dbhandler->get_all_result(
				'EVENTNOTIFICATION',
				'id',
				array(
					'type'   => $type,
					'status' => 1,
				),
				'var'
			);
		}

		return $process_id;
	}//end bm_fetch_active_process_id_of_a_specific_type()


	/**
	 * Fetch transaction status by order id
	 *
	 * @author Darpan
	 */
	public function bm_fetch_transaction_status_by_order_id( $booking_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$status    = '';

		if ( ! empty( $booking_id ) ) {
			$status = $dbhandler->get_all_result( 'TRANSACTIONS', 'payment_status', array( 'booking_id' => $booking_id ), 'var' );
		}

		return $status;
	}//end bm_fetch_transaction_status_by_order_id()


	/**
	 * Fetch email module
	 *
	 * @author Darpan
	 */
	public function bm_fetch_email_module( $module = '' ) {
		$type = '';

		if ( $module == 'BOOKING' ) {
			$type = 'Order Mail';
		}

		return $type;
	}//end bm_fetch_email_module()


	/**
	 * Fetch email type
	 *
	 * @author Darpan
	 */
	public function bm_fetch_email_type( $type = '' ) {
		$email_type = '';

		if ( $type == 'new_order' ) {
			$email_type = esc_html__( 'New order', 'service-booking' );
		} elseif ( $type == 'cancel_order' ) {
			$email_type = esc_html__( 'Cancel order', 'service-booking' );
		} elseif ( $type == 'refund_order' ) {
			$email_type = esc_html__( 'Refund order', 'service-booking' );
		} elseif ( $type == 'approved_order' ) {
			$email_type = esc_html__( 'Approved order', 'service-booking' );
		} elseif ( $type == 'failed_order' ) {
			$email_type = esc_html__( 'Failed order', 'service-booking' );
		} elseif ( $type == 'gift_voucher' ) {
			$email_type = esc_html__( 'Gift voucher', 'service-booking' );
		} elseif ( $type == 'new_request' ) {
			$email_type = esc_html__( 'New request', 'service-booking' );
		} elseif ( $type == 'voucher_redeem' ) {
			$email_type = esc_html__( 'Voucher redeem', 'service-booking' );
		}

		return $email_type;
	}//end bm_fetch_email_type()


	/**
	 * Fetch email status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_email_status( $status_type = -1 ) {
		$email_status = '';

		if ( esc_attr( $status_type ) == 0 ) {
			$email_status = esc_html_e( 'Mail not sent', 'service-booking' );
		} elseif ( esc_attr( $status_type ) == 1 ) {
			$email_status = esc_html_e( 'Mail sent to admin only', 'service-booking' );
		} elseif ( esc_attr( $status_type ) == 2 ) {
			$email_status = esc_html_e( 'Mail sent to customer only', 'service-booking' );
		} elseif ( esc_attr( $status_type ) == 3 ) {
			$email_status = esc_html_e( 'Mail sent to both admin and customer', 'service-booking' );
		} else {
			$email_status = esc_html_e( 'Status could not be fetched', 'service-booking' );
		}

		return $email_status;
	}//end bm_fetch_email_status()


	/**
	 * Fetch email details
	 *
	 * @author Darpan
	 */
	public function bm_fetch_mail_details( $email_id = 0 ) {
		$dbhandler    = new BM_DBhandler();
		$slot_format  = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
		$mail_details = '';

		if ( $email_id > 0 ) {
			$email_record = $dbhandler->get_row( 'EMAILS', $email_id );

			if ( ! empty( $email_record ) ) {
				$module_type = isset( $email_record->module_type ) ? $email_record->module_type : '';
				$module_id   = isset( $email_record->module_id ) ? $email_record->module_id : 0;
				$email_type  = isset( $email_record->mail_type ) ? $email_record->mail_type : '';

				if ( ! empty( $module_type ) && ! empty( $module_id ) ) {
					if ( $module_type == 'BOOKING' ) {
						$booking = $dbhandler->get_row( $module_type, $module_id, 'id' );

						if ( ! empty( $booking ) ) {
							$booking_id    = isset( $booking->id ) ? $booking->id : 0;
							$customer_data = $this->get_customer_info_for_order( $booking_id );
							$booking_slots = isset( $booking->booking_slots ) ? maybe_unserialize( $booking->booking_slots ) : array();
							$first_name    = isset( $customer_data['billing_first_name'] ) ? $customer_data['billing_first_name'] : '';
							$last_name     = isset( $customer_data['billing_last_name'] ) ? $customer_data['billing_last_name'] : '';
							$contact_no    = isset( $customer_data['billing_contact'] ) ? $customer_data['billing_contact'] : '';
							$email_address = isset( $customer_data['billing_email'] ) ? $customer_data['billing_email'] : '';

							$mail_details .= '<div class="mail_details_parent_div">';
							$mail_details .= '<div class="mailcommonbox">';
							$mail_details .= '<h4>' . __( 'Service Details', 'service-booking' ) . '</h4>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Service Ordered:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( isset( $booking->service_name ) ? $booking->service_name : '' ) . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Date:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( isset( $booking->booking_date ) ? $this->bm_month_year_date_format( $booking->booking_date ) : '' ) . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'From:', 'service-booking' ) . '</label>';

							if ( $slot_format == '12' ) {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots['from'] ) ? $this->bm_am_pm_format( $booking_slots['from'] ) : '' ) . '</span>';
							} else {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots['from'] ) ? $this->bm_twenty_fourhrs_format( $booking_slots['from'] ) : '' ) . '</span>';
							}

							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'To:', 'service-booking' ) . '</label>';

							if ( $slot_format == '12' ) {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots['to'] ) ? $this->bm_am_pm_format( $booking_slots['to'] ) : '' ) . '</span>';
							} else {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots['to'] ) ? $this->bm_twenty_fourhrs_format( $booking_slots['to'] ) : '' ) . '</span>';
							}

							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Total Cost:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( $this->bm_fetch_price_in_global_settings_format( ( isset( $booking->total_cost ) ? $booking->total_cost : 0 ), true ) ) . '</span>';
							$mail_details .= '</div><br>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="mailcommonbox">';
							$mail_details .= '<h4>' . __( 'Customer Details', 'service-booking' ) . '</h4>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Name:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( ! empty( $last_name ) ? $first_name . '' . $last_name : $first_name ) . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Email:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . $email_address . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Contact:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . $contact_no . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '</div>';

							if ( $email_type == 'gift_voucher' ) {
								$recipient_data = $dbhandler->get_value( 'VOUCHERS', 'recipient_data', $module_id, 'booking_id' );
								$recipient_data = ! empty( $recipient_data ) ? maybe_unserialize( $recipient_data ) : array();

								if ( ! empty( $recipient_data ) ) {
									$mail_details .= '<div class="mailcommonbox">';
									$mail_details .= '<h4>' . __( 'Recipient Details', 'service-booking' ) . '</h4>';
									$mail_details .= '<div class="detailsbutton">';
									$mail_details .= '<label for="fname" class="boldfont">' . __( 'Name:', 'service-booking' ) . '</label>';
									$mail_details .= '<span class="bookingtext">' . ( isset( $recipient_data['recipient_last_name'] ) ? $recipient_data['recipient_first_name'] . '' . $recipient_data['recipient_last_name'] : $recipient_data['recipient_first_name'] ) . '</span>';
									$mail_details .= '</div>';
									$mail_details .= '<div class="detailsbutton">';
									$mail_details .= '<label for="fname" class="boldfont">' . __( 'Email:', 'service-booking' ) . '</label>';
									$mail_details .= '<span class="bookingtext">' . ( isset( $recipient_data['recipient_email'] ) ? $recipient_data['recipient_email'] : '' ) . '</span>';
									$mail_details .= '</div>';
									$mail_details .= '<div class="detailsbutton">';
									$mail_details .= '<label for="fname" class="boldfont">' . __( 'Contact:', 'service-booking' ) . '</label>';
									$mail_details .= '<span class="bookingtext">' . ( isset( $recipient_data['recipient_contact'] ) ? $recipient_data['recipient_contact'] : '' ) . '</span>';
									$mail_details .= '</div>';
									$mail_details .= '</div>';
									$mail_details .= '</div>';
								}
							}

							$mail_details .= '</div>';
						}
					} elseif ( $module_type == 'FAILED_TRANSACTIONS' ) {
						$booking = $dbhandler->get_row( $module_type, $module_id, 'id' );

						if ( ! empty( $booking ) ) {
							$customer_data = isset( $booking->customer_data ) ? maybe_unserialize( $booking->customer_data ) : array();
							$customer_data = isset( $customer_data['billing_details'] ) ? $customer_data['billing_details'] : array();
							$booking_data  = isset( $booking->booking_data ) ? maybe_unserialize( $booking->booking_data ) : array();
							$booking_slots = isset( $booking_data['booking_slots'] ) ? $booking_data['booking_slots'] : array();
							$service_name  = isset( $booking_data['service_name'] ) ? $booking_data['service_name'] : '';
							$booking_date  = isset( $booking_data['booking_date'] ) ? $this->bm_month_year_date_format( $booking_data['booking_date'] ) : '';
							$total_cost    = isset( $booking_data['total_cost'] ) ? $booking_data['total_cost'] : 0;

							if ( isset( $booking_slots ) && strpos( $booking_slots, ' - ' ) !== false ) {
								$booking_slots = explode( ' - ', $booking_slots );
							}

							$first_name    = isset( $customer_data['billing_first_name'] ) ? $customer_data['billing_first_name'] : '';
							$last_name     = isset( $customer_data['billing_last_name'] ) ? $customer_data['billing_last_name'] : '';
							$contact_no    = isset( $customer_data['billing_contact'] ) ? $customer_data['billing_contact'] : '';
							$email_address = isset( $customer_data['billing_email'] ) ? $customer_data['billing_email'] : '';

							$mail_details .= '<div class="mail_details_parent_div">';
							$mail_details .= '<div class="mailcommonbox">';
							$mail_details .= '<h4>' . __( 'Service Details', 'service-booking' ) . '</h4>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Service Ordered:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( $service_name ) . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Date:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( $booking_date ) . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'From:', 'service-booking' ) . '</label>';

							if ( $slot_format == '12' ) {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots[0] ) ? $this->bm_am_pm_format( $booking_slots[0] ) : '' ) . '</span>';
							} else {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots[0] ) ? $this->bm_twenty_fourhrs_format( $booking_slots[0] ) : '' ) . '</span>';
							}

							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'To:', 'service-booking' ) . '</label>';

							if ( $slot_format == '12' ) {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots[1] ) ? $this->bm_am_pm_format( $booking_slots[1] ) : '' ) . '</span>';
							} else {
								$mail_details .= '<span class="bookingtext">' . ( isset( $booking_slots[1] ) ? $this->bm_twenty_fourhrs_format( $booking_slots[1] ) : '' ) . '</span>';
							}

							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Total Cost:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( $this->bm_fetch_price_in_global_settings_format( $total_cost ) ) . '</span>';
							$mail_details .= '</div><br>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="mailcommonbox">';
							$mail_details .= '<h4>' . __( 'Customer Details', 'service-booking' ) . '</h4>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Name:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . ( ! empty( $last_name ) ? $first_name . '' . $last_name : $first_name ) . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Email:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . $email_address . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '<div class="detailsbutton">';
							$mail_details .= '<label for="fname" class="boldfont">' . __( 'Contact:', 'service-booking' ) . '</label>';
							$mail_details .= '<span class="bookingtext">' . $contact_no . '</span>';
							$mail_details .= '</div>';
							$mail_details .= '</div>';
							$mail_details .= '</div>';
						}
					}
				}
			}
		}

		return $mail_details;
	}//end bm_fetch_mail_details()


	/**
	 * Fetch email body
	 *
	 * @author Darpan
	 */
	public function bm_fetch_mail_body( $email_id = 0 ) {
		$dbhandler = new BM_DBhandler();
		$bm_mail   = new BM_Email();
		$mail_body = '';

		if ( $email_id > 0 ) {
			$email_record = $dbhandler->get_row( 'EMAILS', $email_id );

			if ( ! empty( $email_record ) ) {
				$module_type = isset( $email_record->module_type ) ? $email_record->module_type : '';
				$module_id   = isset( $email_record->module_id ) ? $email_record->module_id : 0;
				$email_type  = isset( $email_record->mail_type ) ? $email_record->mail_type : '';
				$template_id = isset( $email_record->template_id ) ? $email_record->template_id : 0;
				$mail_body   = isset( $email_record->mail_body ) ? $email_record->mail_body : '';
				$is_resent   = isset( $email_record->is_resent ) ? $email_record->is_resent : 0;

				if ( $is_resent != 1 ) {
					if ( ! empty( $module_type ) && ! empty( $module_id ) && ! empty( $template_id ) && ! empty( $email_type ) ) {
						if ( $email_type == 'failed_order' ) {
							$module_key = $dbhandler->get_value( $module_type, 'booking_key', $module_id, 'id' );
							$mail_body  = $bm_mail->bm_get_template_email_content( $template_id, (string) $module_key );
						} else {
							$mail_body = $bm_mail->bm_get_template_email_content( $template_id, (int) $module_id );
						}
					}
				}
			}
		}

		return $mail_body;
	}//end bm_fetch_mail_body()


	/**
	 * Fetch order transaction data with html
	 *
	 * @author Darpan
	 */
	public function bm_fetch_html_with_order_transaction( $transaction_data = array() ) {
		$dbhandler      = new BM_DBhandler();
		$booking_id     = isset( $transaction_data['booking_id'] ) ? $transaction_data['booking_id'] : 0;
		$paid_amount    = isset( $transaction_data['paid_amount'] ) ? $transaction_data['paid_amount'] : 0;
		$paid_currency  = isset( $transaction_data['paid_amount_currency'] ) ? strtoupper( $transaction_data['paid_amount_currency'] ) : '';
		$transaction_id = isset( $transaction_data['transaction_id'] ) ? $transaction_data['transaction_id'] : '';
		$payment_method = isset( $transaction_data['payment_method'] ) ? $transaction_data['payment_method'] : '';
		$payment_status = isset( $transaction_data['payment_status'] ) ? $transaction_data['payment_status'] : '';
		$refund_id      = isset( $transaction_data['refund_id'] ) ? $transaction_data['refund_id'] : '';
		$is_active      = isset( $transaction_data['is_active'] ) ? $transaction_data['is_active'] : 0;

		$is_frontend_booking      = $dbhandler->get_value( 'BOOKING', 'is_frontend_booking', $booking_id, 'id' );
		$default_payment_statuses = $this->bm_fetch_payment_statuses();
		/**$excluded_payment_statuses = array_keys( $this->bm_fetch_payment_statuses( '', array( 'Refunded' ) ) );*/

		$html  = '<div class="transaction_details_parent_div">';
		$html .= '<h4 style="font-size: 16px;text-decoration: underline;">' . __( 'Transaction Details', 'service-booking' ) . '</h4>';
		$html .= '<input type="hidden" name="booking_id" id="booking_id" value=' . esc_attr( $booking_id ) . '>';
		$html .= '<input type="hidden" name="paid_amount" id="paid_amount" value=' . esc_attr( $paid_amount ) . '>';
		$html .= '<input type="hidden" name="paid_amount_currency" id="paid_amount_currency" value=' . esc_html( $paid_currency ) . '>';
		/**$html .= '<div class="single_transaction_details">';
		$html .= '<label for="paid_amount" class="boldfont">' . __( 'Amount paid:', 'service-booking' ) . '</label>';
		$html .= '<input type="number" name="paid_amount" id="paid_amount" value=' . esc_attr( $paid_amount ) . '>';
		$html .= '</div>';
		$html .= '<div class="single_transaction_details">';
		$html .= '<label for="paid_amount_currency" class="boldfont">' . __( 'Currency:', 'service-booking' ) . '</label>';
		$html .= '<select name="paid_amount_currency" id="paid_amount_currency" class="regular-text" style="max-width:300px">';
		$html .= '<option value="USD" ' . ( $paid_currency == 'USD' ? 'selected' : '' ) . '>' . __( 'US Dollars', 'service-booking' ) . '($)</option>';
		$html .= '<option value="EUR" ' . ( $paid_currency == 'EUR' ? 'selected' : '' ) . '>' . __( 'Euros', 'service-booking' ) . '(&euro;)</option>';
		$html .= '<option value="GBP" ' . ( $paid_currency == 'GBP' ? 'selected' : '' ) . '>' . __( 'Pounds Sterling', 'service-booking' ) . '(&pound;)</option>';
		$html .= '<option value="AUD" ' . ( $paid_currency == 'AUD' ? 'selected' : '' ) . '>' . __( 'Australian Dollars', 'service-booking' ) . '($)</option>';
		$html .= '<option value="BRL" ' . ( $paid_currency == 'BRL' ? 'selected' : '' ) . '>' . __( 'Brazilian Real', 'service-booking' ) . '(R$)</option>';
		$html .= '<option value="CAD" ' . ( $paid_currency == 'CAD' ? 'selected' : '' ) . '>' . __( 'Canadian Dollars', 'service-booking' ) . '($)</option>';
		$html .= '<option value="CZK" ' . ( $paid_currency == 'CZK' ? 'selected' : '' ) . '>' . __( 'Czech Koruna', 'service-booking' ) . '</option>';
		$html .= '<option value="DKK" ' . ( $paid_currency == 'DKK' ? 'selected' : '' ) . '>' . __( 'Danish Krone', 'service-booking' ) . '</option>';
		$html .= '<option value="HKD" ' . ( $paid_currency == 'HKD' ? 'selected' : '' ) . '>' . __( 'Hong Kong Dollar', 'service-booking' ) . '($)</option>';
		$html .= '<option value="HUF" ' . ( $paid_currency == 'HUF' ? 'selected' : '' ) . '>' . __( 'Hungarian Forint', 'service-booking' ) . '</option>';
		$html .= '<option value="ILS" ' . ( $paid_currency == 'ILS' ? 'selected' : '' ) . '>' . __( 'Israeli Shekel', 'service-booking' ) . '(&#x20aa;)</option>';
		$html .= '<option value="JPY" ' . ( $paid_currency == 'JPY' ? 'selected' : '' ) . '>' . __( 'Japanese Yen', 'service-booking' ) . '(&yen;)</option>';
		$html .= '<option value="MYR" ' . ( $paid_currency == 'MYR' ? 'selected' : '' ) . '>' . __( 'Malaysian Ringgits', 'service-booking' ) . '</option>';
		$html .= '<option value="MXN" ' . ( $paid_currency == 'MXN' ? 'selected' : '' ) . '>' . __( 'Mexican Peso', 'service-booking' ) . '($)</option>';
		$html .= '<option value="NZD" ' . ( $paid_currency == 'NZD' ? 'selected' : '' ) . '>' . __( 'New Zealand Dollar', 'service-booking' ) . '($)</option>';
		$html .= '<option value="NOK" ' . ( $paid_currency == 'NOK' ? 'selected' : '' ) . '>' . __( 'Norwegian Krone', 'service-booking' ) . '</option>';
		$html .= '<option value="PHP" ' . ( $paid_currency == 'PHP' ? 'selected' : '' ) . '>' . __( 'Philippine Pesos', 'service-booking' ) . '</option>';
		$html .= '<option value="PLN" ' . ( $paid_currency == 'PLN' ? 'selected' : '' ) . '>' . __( 'Polish Zloty', 'service-booking' ) . '</option>';
		$html .= '<option value="SGD" ' . ( $paid_currency == 'SGD' ? 'selected' : '' ) . '>' . __( 'Singapore Dollar', 'service-booking' ) . '($)</option>';
		$html .= '<option value="SEK" ' . ( $paid_currency == 'SEK' ? 'selected' : '' ) . '>' . __( 'Swedish Krona', 'service-booking' ) . '</option>';
		$html .= '<option value="CHF" ' . ( $paid_currency == 'CHF' ? 'selected' : '' ) . '>' . __( 'Swiss Franc', 'service-booking' ) . '</option>';
		$html .= '<option value="TWD" ' . ( $paid_currency == 'TWD' ? 'selected' : '' ) . '>' . __( 'Taiwan New Dollars', 'service-booking' ) . '</option>';
		$html .= '<option value="THB" ' . ( $paid_currency == 'THB' ? 'selected' : '' ) . '>' . __( 'Thai Baht', 'service-booking' ) . '(&#3647;)</option>';
		$html .= '<option value="INR" ' . ( $paid_currency == 'INR' ? 'selected' : '' ) . '>' . __( 'Indian Rupee', 'service-booking' ) . '(&#x20B9;)</option>';
		$html .= '<option value="TRY" ' . ( $paid_currency == 'TRY' ? 'selected' : '' ) . '>' . __( 'Turkish Lira', 'service-booking' ) . '(&#8378;)</option>';
		$html .= '<option value="RIAL" ' . ( $paid_currency == 'RIAL' ? 'selected' : '' ) . '>' . __( 'Iranian Rial', 'service-booking' ) . '</option>';
		$html .= '<option value="RUB" ' . ( $paid_currency == 'RUB' ? 'selected' : '' ) . '>' . __( 'Russian Rubles', 'service-booking' ) . '</option>';
		$html .= '</select>';
		$html .= '</div>';*/
		if ( $is_frontend_booking == 1 ) {
			$html .= '<div class="single_transaction_details ' . ( $is_active == 0 || $is_active == 2 ? 'readonly_cursor' : '' ) . '">';
			$html .= '<label for="transaction_id" class="boldfont">' . __( 'Transaction ID', 'service-booking' ) . '</label>';
			$html .= '<input type="text" name="transaction_id" id="transaction_id" ' . ( $is_active == 0 || $is_active == 2 ? 'readonly' : '' ) . ' value=' . esc_html( $transaction_id ) . '>';
			$html .= '</div>';
		}
		/**$html .= '<div class="single_transaction_details">';
		$html .= '<label for="payment_method" class="boldfont">' . __( 'Payment Method', 'service-booking' ) . '</label>';
		$html .= '<input type="text" name="payment_method" id="payment_method" value=' . esc_html( $payment_method ) . '>';
		$html .= '</div>';*/
		$html .= '<div class="single_transaction_details ' . ( $is_active == 0 || $is_active == 2 ? 'readonly_cursor' : '' ) . '">';
		$html .= '<label for="payment_status" class="boldfont">' . __( 'Payment Status', 'service-booking' ) . '</label>';
		$html .= '<select name="payment_status" id="payment_status" onchange="check_payment_status(this)"  class="' . ( $is_active == 0 || $is_active == 2 ? 'readonly_checkbox' : '' ) . '">';
		foreach ( $default_payment_statuses as $key => $default_status ) {
			$html .= '<option value="' . $key . '" ' . ( $key == $payment_status ? 'selected' : '' ) . '>' . esc_html( $default_status ) . '</option>';
		}
		$html .= '</select>';
		$html .= '</div>';
		$html .= '<div class="single_transaction_details ' . ( $is_active == 0 || $is_active == 2 || $payment_status != 'refunded' ? 'hidden' : '' ) . '" id="refund_id_input">';
		$html .= '<label for="refund_id" class="boldfont">' . __( 'Refund ID', 'service-booking' ) . '</label>';
		$html .= '<input type="text" name="refund_id" id="refund_id" value=' . esc_html( $refund_id ) . '>';
		$html .= '</div>';
		$html .= '<div class="single_transaction_details readonly_cursor">';
		$html .= '<label for="is_active" class="boldfont">' . __( 'Status', 'service-booking' ) . '</label>';
		$html .= '<select name="is_active" id="is_active" class="regular-text readonly_checkbox" style="max-width:300px">';
		$html .= '<option value="0" ' . ( $is_active == 0 || $is_active == 2 ? 'selected' : '' ) . '>' . __( 'Inactive', 'service-booking' ) . '</option>';
		$html .= '<option value="1" ' . ( $is_active == 1 ? 'selected' : '' ) . '>' . __( 'Active', 'service-booking' ) . '</option>';
		$html .= '</select>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}//end bm_fetch_html_with_order_transaction()


	/**
	 * Fetch paid transaction statuses
	 *
	 * @author Darpan
	 */
	public function bm_fetch_paid_transaction_statuses() {
		$statuses = array( 'succeeded', 'requires_capture' );
		return $statuses;
	}


	/**
	 * Fetch pending transaction statuses
	 *
	 * @author Darpan
	 */
	public function bm_fetch_pending_transaction_statuses() {
		$statuses = array( 'processing', 'requires_payment_method', 'requires_confirmation', 'requires_action' );
		return $statuses;
	}


	/**
	 * Upload file/image and return guid
	 *
	 * @author Darpan
	 */
	public function bm_make_upload_and_get_attached_id( $filefield, $allowed_ext, $require_imagesize = array(), $parent_post_id = 0 ) {
		$attach_id = '';

		if ( is_array( $filefield ) && ! empty( $filefield ) ) {
			$file = array(
				'name'     => isset( $filefield['name'] ) ? $filefield['name'] : '',
				'type'     => isset( $filefield['type'] ) ? $filefield['type'] : '',
				'tmp_name' => isset( $filefield['tmp_name'] ) ? $filefield['tmp_name'] : '',
				'error'    => isset( $filefield['error'] ) ? $filefield['error'] : '',
				'size'     => isset( $filefield['size'] ) ? $filefield['size'] : '',
			);

			$too_small = false;

			if ( ! empty( $require_imagesize ) && ! empty( $file['tmp_name'] ) ) {
				$imagesize    = getimagesize( $file['tmp_name'] );
				$image_width  = $imagesize[0];
				$image_height = $imagesize[1];

				if ( isset( $require_imagesize[2] ) && $file['size'] > $require_imagesize[2] ) {
					$too_small = true;
				} elseif ( $image_width < $require_imagesize['0'] || $image_height < $require_imagesize['1'] ) {
					$too_small = true;
				}
			}

			if ( $filefield['error'] === 0 ) {
				if ( ! function_exists( 'wp_handle_upload' ) ) {
					require_once ABSPATH . 'wp-admin/includes/file.php';
					require_once ABSPATH . 'wp-admin/includes/image.php';
				}

				$upload_overrides = array( 'test_form' => false );
				$movefile         = wp_handle_upload( $file, $upload_overrides );

				if ( $movefile && ! isset( $movefile['error'] ) ) {
					$filename = $movefile['file'];

					$filetype          = wp_check_filetype( basename( $filename ), null );
					$current_file_type = strtolower( $filetype['ext'] );

					if ( in_array( $current_file_type, $allowed_ext ) && $too_small == false ) {
						$wp_upload_dir = wp_upload_dir();

						$attachment = array(
							'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ),
							'post_mime_type' => $filetype['type'],
							'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
							'post_content'   => '',
							'post_status'    => 'inherit',
						);

						include_once ABSPATH . 'wp-admin/includes/image.php';
						$attach_id   = wp_insert_attachment( $attachment, $filename, $parent_post_id );
						$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
						wp_update_attachment_metadata( $attach_id, $attach_data );
					}
				}
			}
		}

		return $attach_id;
	}//end bm_make_upload_and_get_attached_id()


	/**
	 * Fetch order export html
	 *
	 * @author Darpan
	 */
	public function bm_fetch_export_html_with_options() {
		$html  = '<div class="export_details_parent_div">';
		$html .= '<div class="single_export_details">';
		$html .= '<label for="exportOption" class="boldfont">' . __( 'Export action:', 'service-booking' ) . '</label>';
		$html .= '<select id="exportOption" class="regular-text" style="max-width:300px">';
		$html .= '<option value="1">' . __( 'Export All', 'service-booking' ) . '</option>';
		$html .= '<option value="2">' . __( 'Export Current Page Records', 'service-booking' ) . '</option>';
		$html .= '<option value="3">' . __( 'Export From a Range of Pages', 'service-booking' ) . '</option>';
		$html .= '</select>';
		$html .= '</div>';
		$html .= '<div class="single_export_details">';
		$html .= '<div id="rangeOptions" style="display: none;">';
		$html .= '<div id="pageFrom">';
		$html .= '<label for="startPage" class="boldfont">' . __( 'Page From:', 'service-booking' ) . '</label>';
		$html .= '<input type="number" min="1" step="1" id="startPage" value="1">';
		$html .= '</div>';
		$html .= '<div id="pageFrom">';
		$html .= '<label for="endPage" class="boldfont">' . __( 'Page To:', 'service-booking' ) . '</label>';
		$html .= '<input type="number" min="1" step="1" id="endPage" value="1">';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}//end bm_fetch_export_html_with_options()


	/**
	 * Get attachments for an order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_order_attachments( $order_id ) {
		$dbhandler   = new BM_DBhandler();
		$folder      = 'new-mail';
		$attachments = array();

		if ( ! empty( $order_id ) ) {
			$order_status = $dbhandler->get_value( 'BOOKING', 'order_status', $order_id, 'id' );

			/**if ( $order_status == 'failed' ) {
				$order_id = $dbhandler->get_value( 'BOOKING', 'booking_key', $order_id, 'id' );
				$folder   = 'failed-order-mail';
			}*/

			$order_details_file = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details/order-details-booking-' . $order_id . '.pdf';
			$order_attachment   = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details/order-details-booking-' . $order_id . '.pdf';

			if ( file_exists( $order_details_file ) ) {
				$attachments['order_details'] = $order_attachment;
			}

			$customer_details_directory = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/customer-details';
			$customer_attachment        = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/' . $folder . '/customer-details/customer-details-booking-' . $order_id . '.pdf';
			$customer_details_file      = $customer_details_directory . '/customer-details-booking-' . $order_id . '.pdf';

			if ( file_exists( $customer_details_file ) ) {
				$attachments['customer_details'] = $customer_attachment;
			}
		}

		return $attachments;
	}//end bm_fetch_order_attachments()


	/**
	 * Get attachments for an order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_archived_order_attachments( $order_id ) {
		$dbhandler   = new BM_DBhandler();
		$folder      = 'archive';
		$attachments = array();

		if ( $order_id > 0 ) {
			$order_status = $dbhandler->get_value( 'BOOKING', 'order_status', $order_id, 'id' );

			$order_details_file = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details-booking-' . $order_id . '.pdf';
			$order_attachment   = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details-booking-' . $order_id . '.pdf';

			if ( file_exists( $order_details_file ) ) {
				$attachments['order_details'] = $order_attachment;
			}

			$folder = 'new-mail';

			$customer_details_directory = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/customer-details';
			$customer_attachment        = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/' . $folder . '/customer-details/customer-details-booking-' . $order_id . '.pdf';
			$customer_details_file      = $customer_details_directory . '/customer-details-booking-' . $order_id . '.pdf';

			if ( file_exists( $customer_details_file ) ) {
				$attachments['customer_details'] = $customer_attachment;
			}
		}

		return $attachments;
	}//end bm_fetch_archived_order_attachments()


	/**
	 * Get attachments for a failed order
	 *
	 * @author Darpan
	 */
	public function bm_fetch_failed_order_attachments( $failed_order_id ) {
		$dbhandler   = new BM_DBhandler();
		$folder      = 'failed-order-mail';
		$attachments = array();

		if ( $failed_order_id > 0 ) {
			$booking_key = $dbhandler->get_value( 'FAILED_TRANSACTIONS', 'booking_key', $failed_order_id, 'id' );

			$order_details_file = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details/order-details-booking-' . $booking_key . '.pdf';
			$order_attachment   = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/' . $folder . '/order-details/order-details-booking-' . $booking_key . '.pdf';

			if ( file_exists( $order_details_file ) ) {
				$attachments['order_details'] = $order_attachment;
			}

			$customer_details_directory = plugin_dir_path( __DIR__ ) . 'src/mail-attachments/' . $folder . '/customer-details';
			$customer_attachment        = plugin_dir_url( __DIR__ ) . 'src/mail-attachments/' . $folder . '/customer-details/customer-details-booking-' . $booking_key . '.pdf';
			$customer_details_file      = $customer_details_directory . '/customer-details-booking-' . $booking_key . '.pdf';

			if ( file_exists( $customer_details_file ) ) {
				$attachments['customer_details'] = $customer_attachment;
			}
		}

		return $attachments;
	}//end bm_fetch_failed_order_attachments()


	/**
	 * Get product info for checkout page
	 *
	 * @author Darpan
	 */
	public function bm_fetch_price_discount_module_box_for_backend_order( $service_id, $date ) {
		$dbhandler           = new BM_DBhandler();
		$module_age_ranges   = array();
		$age_input_html      = '';
		$svc_price_module_id = 0;

		if ( ! empty( $date ) && $service_id > 0 ) {
			$svc_price_module_id = $this->bm_fetch_external_service_price_module_by_service_id_and_date( $service_id, $date );

			if ( $svc_price_module_id > 0 ) {
				$module_age_ranges = $this->bm_fetch_age_ranges_defined_in_service( $service_id );

				if ( empty( $module_age_ranges ) ) {
					$module_age_ranges = $this->bm_fetch_external_service_price_module_age_ranges( $svc_price_module_id, $service_id );
				}
			}

			if ( ! empty( $module_age_ranges ) && is_array( $module_age_ranges ) ) {
				$age_input_html = $this->bm_fetch_backend_module_age_range_html( $module_age_ranges );

				if ( ! empty( $age_input_html ) ) {
					$age_input_html .= '<div class="checkout_discount_buttons">';
					$age_input_html .= '<span class="primarybutton button-primary">';
					$age_input_html .= '<a href="#" id="check_checkout_discount" class="check_checkout_discount" title="' . esc_html__( 'Calculate', 'service-booking' ) . '">';
					/**$age_input_html .= esc_html__( 'Calculate', 'service-booking' );*/
					$age_input_html .= '<i class="fa fa-calculator"></i>';
					$age_input_html .= '</a>';
					$age_input_html .= '</span>';
					$age_input_html .= '<span class="secondarybutton button-secondary">';
					$age_input_html .= '<a href="#" id="reset_checkout_discount" class="reset_checkout_discount" title="' . esc_html__( 'Reset', 'service-booking' ) . '">';
					/**$age_input_html .= esc_html__( 'Reset', 'service-booking' );*/
					$age_input_html .= '<i class="fa fa-refresh"></i>';
					$age_input_html .= '</a>';
					$age_input_html .= '</span>';
					$age_input_html .= '</div>';
				}
			}
		}

		/**$dbhandler->bm_save_data_to_transient( 'flexi_svc_price_module_age_ranges_' . $booking_key, $module_age_ranges, 72 );*/

		return apply_filters( 'bm_backend_order_price_module_html', wp_kses( $age_input_html, $this->bm_fetch_expanded_allowed_tags() ), $svc_price_module_id, $service_id, $date );
	}//end bm_fetch_price_discount_module_box_for_backend_order()


	/**
	 * Save booking data through woocommerce into flexibooking
	 *
	 * @author Darpan
	 */
	public function bm_save_woocommerce_booking_data_in_flexibooking( $wc_order_id ) {
		if ( ! $wc_order_id || $wc_order_id < 1 ) {
			return;
		}

		$post_type = get_post_type( $wc_order_id );
		if ( ! in_array( $post_type, array( 'shop_order', 'shop_order_placehold' ), true ) ) {
			return;
		}

		$wc_order     = wc_get_order( $wc_order_id );
		$booking_key  = get_post_meta( $wc_order_id, '_flexi_booking_key', true );
		$checkout_key = get_post_meta( $wc_order_id, '_flexi_checkout_key', true );

		if ( empty( $wc_order ) || empty( $booking_key ) || empty( $checkout_key ) ) {
			return 0;
		}

		try {
			$customer_id = 0;
			$booking_id  = 0;
			$dbhandler   = new BM_DBhandler();

			$discounted_key = $dbhandler->get_global_option_value( 'discount_' . $booking_key ) == 1 ? 'discounted_' : '';
			$booking_data   = $dbhandler->bm_fetch_data_from_transient( $discounted_key . $booking_key );

			if ( empty( $booking_data ) ) {
				return 0;
			}

			$service_id   = $booking_data['service_id'] ?? 0;
			$booking_date = $booking_data['booking_date'] ?? '';

			if ( $service_id <= 0 || empty( $booking_date ) || ! isset( $booking_data['total_cost'] ) || ! $this->bm_service_is_bookable( $service_id, $booking_date ) ) {
				return 0;
			}

			update_post_meta( $wc_order_id, '_flexi_service_date', $booking_date );
			update_post_meta( $wc_order_id, '_flexi_booked_slots', $booking_data['booking_slots'] ?? array() );

			$checkout_data            = array();
			$billing_shipping_details = $this->get_woocommerce_order_checkout_field_data( $wc_order_id );

			if ( ! isset( $billing_shipping_details['billing'], $billing_shipping_details['shipping'] ) ) {
				return 0;
			}

			$checkout_data['checkout']['billing_details']  = $billing_shipping_details['billing'];
			$checkout_data['checkout']['shipping_details'] = $billing_shipping_details['shipping'];
			$checkout_data['checkout']['other_data']       = null;

			if ( is_array( $checkout_data['checkout']['billing_details'] ) ) {
				$checkout_billing_details = array();

				foreach ( $checkout_data['checkout']['billing_details'] as $key => $value ) {
					$field_key = $dbhandler->get_value( 'FIELDS', 'field_key', $key, 'field_name' );

					if ( ! empty( $field_key ) ) {
						$checkout_billing_details[ $field_key ] = $value;
					}
				}

				if ( ! empty( $checkout_billing_details ) ) {
					$checkout_data['billing'] = $checkout_billing_details;
				}
			}

			$dbhandler->bm_save_data_to_transient( $checkout_key, $checkout_data, 72 );

			$data = $this->bm_check_payment_type_and_return_data( $booking_key, $checkout_key, 'woocommerce_checkout' );

			if ( ! isset( $data['status'] ) || $data['status'] == 'error' ) {
				return 0;
			}

			if ( ! $this->bm_check_if_cart_order_is_still_bookable( $booking_key ) ) {
				return 0;
			}

			$booking_id = $this->bm_save_booking_data( $booking_key, $checkout_key, $wc_order_id );

			if ( $booking_id <= 0 ) {
				$this->bm_remove_order_data( $booking_id, $customer_id );
				return 0;
			}

			update_post_meta( $wc_order_id, '_flexi_booking_id', $booking_id );

			$payment_status = 'pending';
			if ( $wc_order->is_paid() && (float) $wc_order->get_total() === 0.0 ) {
				$payment_status = 'free';
			} elseif ( $wc_order->get_status() == 'completed' && $wc_order->is_paid() ) {
				$payment_status = 'succeeded';
			} elseif ( $wc_order->get_total_refunded() > 0 ) {
				$payment_status = 'refunded';
			} elseif ( $wc_order->get_status() === 'failed' ) {
				$payment_status = 'failed';
			}

			// Transaction data
			$transaction_data = array(
				'paid_amount'          => $booking_data['total_cost'],
				'paid_amount_currency' => $dbhandler->get_global_option_value( 'bm_booking_currency', 'EUR' ),
				'transaction_id'       => $wc_order->get_payment_method() == 'stripe' && $wc_order->is_paid() ? $wc_order->get_transaction_id() : $wc_order->get_order_key(),
				'payment_method'       => $wc_order->get_payment_method_title(),
				'payment_status'       => $payment_status,
				'is_active'            => 1,
			);

			$checkout_data = $dbhandler->bm_fetch_data_from_transient( $checkout_key );
			$checkout_data = $checkout_data['checkout'] ?? array();

			if ( empty( $checkout_data ) || empty( $checkout_data['billing_details'] ) || empty( $checkout_data['shipping_details'] ) ) {
				$this->bm_remove_order_data( $booking_id, $customer_id );
				return 0;
			}

			$customer_data = array(
				'stripe_id'                => $wc_order->get_meta( '_stripe_customer_id' ) ?? null,
				'customer_name'            => $wc_order->get_billing_first_name() . ' ' . $wc_order->get_billing_last_name(),
				'customer_email'           => $wc_order->get_billing_email(),
				'billing_details'          => $checkout_data['billing_details'],
				'shipping_details'         => $checkout_data['shipping_details'],
				'shipping_same_as_billing' => 0,
				'is_active'                => 1,
			);

			$customer_final = $this->sanitize_request( $customer_data, 'CUSTOMERS' );

			if ( empty( $customer_final ) ) {
				$this->bm_remove_order_data( $booking_id, $customer_id );
				return 0;
			}

			$customer_id = $dbhandler->get_value( 'CUSTOMERS', 'id', $customer_data['customer_email'], 'customer_email' );

			if ( $customer_id > 0 ) {
				$customer_final['customer_updated_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
				$dbhandler->update_row( 'CUSTOMERS', 'id', $customer_id, $customer_final, '', '%d' );
			} else {
				$customer_final['customer_created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
				$customer_id                           = $dbhandler->insert_row( 'CUSTOMERS', $customer_final );
			}

			if ( $customer_id <= 0 ) {
				$this->bm_remove_order_data( $booking_id, $customer_id );
				return 0;
			}

			update_post_meta( $wc_order_id, '_flexi_customer_id', $customer_id );

			$booking_update_data = array(
				'customer_id'        => $customer_id,
				'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
			);

			$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_update_data, '', '%d' );

			// Transaction data
			$transaction_data['booking_id']  = $booking_id;
			$transaction_data['wc_order_id'] = $wc_order_id;
			$transaction_data['customer_id'] = $customer_id;

			$transaction_data['transaction_created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();

			$payment_final = $this->sanitize_request( $transaction_data, 'TRANSACTIONS' );

			if ( empty( $payment_final ) ) {
				$this->bm_remove_order_data( $booking_id, $customer_id );
				return 0;
			}

			$payment_id = $dbhandler->insert_row( 'TRANSACTIONS', $payment_final );

			if ( $payment_id <= 0 ) {
				$this->bm_remove_order_data( $booking_id, $customer_id );
				return 0;
			}

			update_post_meta( $wc_order_id, '_flexi_transaction_id', $payment_id );

			$recipient_data = get_post_meta( $wc_order_id, 'gift_data', true );
			$is_gift        = isset( $recipient_data['is_gift'] ) ? $recipient_data['is_gift'] : 0;
			$voucher_id     = 0;

			if ( $is_gift == 1 ) {
				$voucher_expiry_date = $this->bm_get_voucher_expiry_date();
				$voucher_code        = $this->bm_generate_unique_code( $customer_data['customer_email'] );

				if ( $voucher_expiry_date && $voucher_code ) {
					$gift_data = array(
						'code'           => $voucher_code,
						'booking_id'     => $booking_id,
						'customer_id'    => $customer_id,
						'transaction_id' => $payment_id,
						'recipient_data' => ! empty( $recipient_data ) ? $recipient_data : null,
						'is_gift'        => $is_gift,
						'settings'       => array( 'expiry' => $voucher_expiry_date ),
						'created_at'     => $this->bm_fetch_current_wordpress_datetime_stamp(),
					);

					$gift_data = $this->sanitize_request( $gift_data, 'VOUCHERS' );

					if ( $gift_data != false && $gift_data != null ) {
						$voucher_id = $dbhandler->insert_row( 'VOUCHERS', $gift_data );

						if ( ! empty( $voucher_id ) ) {
							update_post_meta( $wc_order_id, '_flexi_voucher_id', $voucher_id );

							$booking_update_data = array(
								'vouchers'           => implode( ',', array( $voucher_code ) ),
								'booking_updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
							);

							$dbhandler->update_row( 'BOOKING', 'id', $booking_id, $booking_update_data, '', '%d' );
						}
					}
				}
			}

			$only_wcmmrce = $dbhandler->get_global_option_value( 'bm_woocommerce_only_checkout', 0 );
			if ( $only_wcmmrce == 1 ) {
				$email = WC()->mailer()->emails['WC_Email_Customer_Completed_Order'];

				$email->object                  = $wc_order;
				$email->find['order-date']      = '{order_date}';
				$email->replace['order-date']   = wc_format_datetime( $wc_order->get_date_created() );
				$email->find['order-number']    = '{order_number}';
				$email->replace['order-number'] = $wc_order->get_order_number();

				$mail_to      = $wc_order->get_billing_email();
				$mail_subject = $email->get_subject();
				$mail_body    = $email->get_content_html();

				$mail_data = array(
					'module_type' => 'BOOKING',
					'module_id'   => $booking_id,
					'mail_type'   => 'new_order',
					'mail_to'     => $mail_to,
					'mail_sub'    => $mail_subject,
					'mail_body'   => $mail_body,
					'mail_lang'   => $dbhandler->get_global_option_value( 'bm_flexi_current_language', 'en' ),
					'status'      => 1,
					'created_at'  => $this->bm_fetch_current_wordpress_datetime_stamp(),
				);

				$mail_data = $this->sanitize_request( $mail_data, 'EMAILS' );

				if ( $mail_data ) {
					$mail_data['created_at'] = $this->bm_fetch_current_wordpress_datetime_stamp();
					$mail_id                 = $dbhandler->insert_row( 'EMAILS', $mail_data );

					if ( $mail_id > 0 ) {
						$dbhandler->update_row( 'BOOKING', 'id', $booking_id, array( 'mail_sent' => 3 ), '', '%d' );
					}
				}
			} else {
				do_action( 'flexibooking_set_process_new_order', $booking_id );
			}

			$booking_type = $dbhandler->get_value( 'BOOKING', 'booking_type', $booking_id, 'id' );

			if ( ! empty( $voucher_id ) && $booking_type == 'direct' ) {
				do_action( 'flexibooking_set_process_new_order_voucher', $booking_id );
			}

			WC()->session->__unset( 'flexi_booking_key' );
			WC()->session->__unset( 'flexi_checkout_key' );

			return $booking_id;
		} catch ( Exception $e ) {
			return 0;
		}
	}


	/**
	 * Check if exisiting customer email
	 *
	 * @author Darpan
	 */
	public function bm_is_exisiting_customer_email( $mail_to_check, $customer_id = 0 ) {
		if ( empty( $mail_to_check ) ) {
			return true;
		}

		$customer_emails = array();

		if ( $customer_id > 0 ) {
			$customer = ( new BM_DBhandler() )->get_row( 'CUSTOMERS', $customer_id );

			if ( ! empty( $customer ) ) {
				$main_email     = $customer->customer_email ?? '';
				$billing_data   = $customer->billing_details ? maybe_unserialize( $customer->billing_details ) : array();
				$shipping_data  = $customer->shipping_details ? maybe_unserialize( $customer->shipping_details ) : array();
				$billing_email  = $billing_data['billing_email'] ?? '';
				$shipping_email = $billing_data['shipping_email'] ?? '';

				if ( ! empty( $main_email ) ) {
					$customer_emails[] = $main_email;
				}

				if ( ! empty( $billing_email ) ) {
					$customer_emails[] = $billing_email;
				}

				if ( ! empty( $shipping_email ) ) {
					$customer_emails[] = $shipping_email;
				}

				if ( ! empty( $customer_emails ) ) {
					$customer_emails = array_values( array_unique( $customer_emails ) );
				}
			}
		}

		if ( is_array( $mail_to_check ) ) {
			$mail_to_check = array_values( array_unique( $mail_to_check ) );

			foreach ( $mail_to_check as $email ) {
				$customer = ( new BM_DBhandler() )->get_row( 'CUSTOMERS', $email, 'LOWER(customer_email)' );

				if ( ! empty( $customer_emails ) && in_array( $email, $customer_emails ) ) {
					continue;
				}

				if ( ! empty( $customer ) ) {
					return true;
				}
			}
		} else {
			$customer = ( new BM_DBhandler() )->get_row( 'CUSTOMERS', $mail_to_check, 'LOWER(customer_email)' );

			if ( ! empty( $customer_emails ) && in_array( $mail_to_check, $customer_emails ) ) {
				return false;
			}

			if ( ! empty( $customer ) ) {
				return true;
			}
		}

		return false;
	}//end bm_is_exisiting_customer_email()


	/**
	 * Fetch all information related to customer like mails, orders etc
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_customer_related_information( $customer_id ) {
		$joins = array(
			array(
				'table' => 'BOOKING',
				'alias' => 'b',
				'on'    => 'b.customer_id = c.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 't.customer_id = c.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'EMAILS',
				'alias' => 'e',
				'on'    => 'e.module_id = b.id AND e.module_type = "BOOKING"',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'FAILED_TRANSACTIONS',
				'alias' => 'f',
				'on'    => 'f.customer_id = c.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'EXTRASLOTCOUNT',
				'alias' => 'exsc',
				'on'    => 'exsc.booking_id = b.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'EXTRA',
				'alias' => 'ex',
				'on'    => 'ex.id = exsc.extra_svc_id',
				'type'  => 'LEFT',
			),
		);

		global $wpdb;

		$columns = '
            c.id as customer_id, c.customer_name, c.customer_email, c.billing_details, c.is_active, c.customer_created_at,
            COUNT(DISTINCT b.id) AS total_orders,
            GROUP_CONCAT(DISTINCT CONCAT(
                b.id, "|", b.service_name, "|", b.booking_created_at, "|", b.booking_date, "|", b.booking_slots, "|", 
                b.total_cost, "|", b.order_status, "|", 
                IF(b.order_status NOT IN ("processing", "cancelled", "on_hold", "pending") AND b.is_active=1, b.total_cost, 0), "|", 
                IF(b.order_status NOT IN ("processing", "cancelled", "on_hold", "pending") AND b.is_active=1, b.total_svc_slots, 0), "|", 
                IF(b.order_status NOT IN ("processing", "cancelled", "on_hold", "pending") AND b.is_active=1, b.total_ext_svc_slots, 0)
            )) AS order_data,
            t.paid_amount_currency AS paid_amount_currency, 
            COUNT(DISTINCT t.id) AS total_transactions,
            GROUP_CONCAT(DISTINCT CONCAT(t.id, "|", t.paid_amount, "|", t.payment_method, "|", t.payment_status)) AS transaction_data,
            COUNT(DISTINCT e.id) AS total_emails,
            GROUP_CONCAT(DISTINCT CONCAT(e.id, "|", e.mail_sub, "|", e.mail_lang, "|", e.created_at)) AS email_data,
            COUNT(DISTINCT f.id) AS total_failed_transactions,
            GROUP_CONCAT(DISTINCT CONCAT(f.id, "|", f.amount, "|", f.amount_currency, "|", f.refund_status)) AS failed_transaction_data,
            (
                SELECT GROUP_CONCAT(
                    CONCAT(
                        service_name, "|", total_ordered, "|", total_revenue, "|", unique_product
                    )
                )
                FROM (
                    SELECT 
                        b2.service_name AS service_name,
                        SUM(b2.total_svc_slots) AS total_ordered,
                        SUM(b2.service_cost) AS total_revenue,
                        COUNT(DISTINCT b2.service_id) AS unique_product
                    FROM ' . $wpdb->prefix . 'sgbm_booking b2
                    WHERE b2.customer_id = customer_id AND b2.order_status NOT IN ("processing", "cancelled", "on_hold", "pending") AND b2.is_active=1
                    GROUP BY b2.service_id
                ) AS service_summary
            ) AS service_product_data,
            (
                SELECT GROUP_CONCAT(
                    CONCAT(
                        extra_name, "|", total_ordered, "|", total_revenue, "|", unique_product
                    )
                )
                FROM (
                    SELECT 
                        ex.extra_name AS extra_name,
                        SUM(exsc.slots_booked) AS total_ordered,
                        SUM(exsc.slots_booked * ex.extra_price) AS total_revenue,
                        COUNT(DISTINCT exsc.extra_svc_id) AS unique_product
                    FROM  ' . $wpdb->prefix . 'sgbm_extra_svc_booking_count exsc
                    INNER JOIN  ' . $wpdb->prefix . 'sgbm_service_extras ex ON ex.id = exsc.extra_svc_id
                    WHERE exsc.booking_id IN (
                        SELECT b.id
                        FROM  ' . $wpdb->prefix . 'sgbm_booking b
                        WHERE b.customer_id = customer_id AND b.order_status NOT IN ("processing", "cancelled", "on_hold", "pending") AND b.is_active=1
                    )
                    GROUP BY exsc.service_id
                ) AS extra_summary
            ) AS extra_product_data';

		$additional = 'GROUP BY c.id';

		$customerData = ( new BM_DBhandler() )->get_results_with_join(
			array( 'CUSTOMERS', 'c' ),
			$columns,
			$joins,
			array( 'c.id' => array( '=' => $customer_id ) ),
			'results',
			0,
			false,
			null,
			false,
			$additional,
			true
		);

		return $customerData;
	}//end bm_fetch_all_customer_related_information()


	/**
	 * Fetch all checkins for orders
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_order_checkins() {
		$tables = array( 'CHECKIN', 'ch' );

		$joins = array(
			array(
				'type'  => 'INNER',
				'table' => 'BOOKING',
				'alias' => 'b',
				'on'    => 'b.id = ch.booking_id',
			),
			array(
				'type'  => 'LEFT',
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'b.customer_id = c.id',
			),
			array(
				'type'  => 'LEFT',
				'table' => 'EMAILS',
				'alias' => 'e',
				'on'    => 'e.module_id = b.id AND e.module_type = "BOOKING"',
			),
		);

		$columns = '
            b.id as booking_id,
            b.service_id,
            b.service_name,
            b.booking_slots,
            b.booking_date,
            b.total_cost,
            c.billing_details,
            ch.id as checkin_id,
            ch.checkin_time,
            ch.qr_scanned,
            ch.status as checkin_status,
            e.id as email_id,
            e.module_id as module_id,
            e.module_type as module_type,
            e.mail_type as mail_type,
            e.template_id as template_id,
            e.process_id as process_id
        ';

		$additional = 'GROUP BY ch.id';

		$dbhandler = new BM_DBhandler();
		$results   = $dbhandler->get_results_with_join( $tables, $columns, $joins, array( 'b.is_active' => 1 ), 'results', 0, false, null, false, $additional, true );
		$timezone  = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

		if ( ! $results ) {
			return array();
		}

		$processed_results = array();
		$serial_no         = 1;

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				if ( empty( $result->booking_id ) || empty( $result->booking_date ) ) {
					continue;
				}

				$billing_details = isset( $result->billing_details ) && ! empty( $result->billing_details ) ? maybe_unserialize( $result->billing_details ) : array();
				$booking_slots   = isset( $result->booking_slots ) ? maybe_unserialize( $result->booking_slots ) : array();
				$service_date    = new DateTime( $result->booking_date . ' ' . $booking_slots['from'], new DateTimeZone( $timezone ) );
				$service_date    = esc_html( $service_date->format( 'd/m/y H:i' ) );
				$checkin_time    = ! empty( $result->checkin_time ) && ! is_null( $result->checkin_time ) ? new DateTime( $result->checkin_time, new DateTimeZone( $timezone ) ) : '-';
				$checkin_time    = $checkin_time !== '-' ? esc_html( $checkin_time->format( 'd/m/y H:i' ) ) : $checkin_time;

				$processed_results[] = array(
					'id'             => $result->id ?? 0,
					'booking_id'     => $result->booking_id,
					'checkin_id'     => $result->checkin_id ?? 0,
					'serial_no'      => $serial_no++,
					'service_id'     => $result->service_id ?? 0,
					'service_name'   => $result->service_name,
					'booking_date'   => $service_date,
					'first_name'     => $billing_details['billing_first_name'] ?? '',
					'last_name'      => $billing_details['billing_last_name'] ?? '',
					'contact_no'     => $billing_details['billing_contact'] ?? '',
					'email_address'  => $billing_details['billing_email'] ?? '',
					'total_cost'     => $result->total_cost ?? 0,
					'checkin_time'   => $checkin_time,
					'checkin_status' => $result->checkin_status ?? 'pending',
					'email_id'       => $result->email_id ?? 0,
					'module_id'      => $result->module_id ?? 0,
					'module_type'    => $result->module_type ?? '',
					'mail_type'      => $result->mail_type ?? '',
					'template_id'    => $result->template_id ?? 0,
					'process_id'     => $result->process_id ?? 0,
				);
			}
		}

		return $processed_results;
	}//end bm_fetch_all_order_checkins()


	/**
	 * Fetch all order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_orders_with_customer_data() {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();

		$tables  = array( 'BOOKING', 'b' );
		$columns = '
            b.id,
            b.wc_order_id,
            b.service_id,
            b.service_name,
            s.service_category,
            b.booking_date,
            b.booking_slots,
            b.is_frontend_booking,
            b.total_cost,
            b.booking_type,
            b.is_active,
            b.order_status,
            b.booking_created_at,
            b.booking_updated_at,
            b.total_svc_slots as service_participants,
            b.total_ext_svc_slots as extra_service_participants,
            b.service_cost,
            b.extra_svc_cost as extra_service_cost,
            b.disount_amount as discount,
            c.billing_details,
            t.payment_status as transaction_status,
            t.transaction_created_at as paid_at,
            t.transaction_created_at as updated_paid_at
        ';

		$joins = array(
			array(
				'type'  => 'LEFT',
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'b.customer_id = c.id',
			),
			array(
				'type'  => 'LEFT',
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 'b.id = t.booking_id',
			),
			array(
				'type'  => 'LEFT',
				'table' => 'SERVICE',
				'alias' => 's',
				'on'    => 'b.service_id = s.id',
			),
		);

		$results  = $dbhandler->get_results_with_join( $tables, $columns, $joins, array(), 'results', 0, false, 'b.id', 'DESC', '', true );
		$timezone = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

		$processed_results = array();
		$serial_no         = 1;

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$billing_details = isset( $result->billing_details ) && ! empty( $result->billing_details ) ? maybe_unserialize( $result->billing_details ) : array();

				$booking_slots      = isset( $result->booking_slots ) ? maybe_unserialize( $result->booking_slots ) : array();
				$booking_created_at = new DateTime( $result->booking_created_at, new DateTimeZone( $timezone ) );
				$service_date       = new DateTime( $result->booking_date . ' ' . $booking_slots['from'], new DateTimeZone( $timezone ) );

				$stripe_status = $this->bm_fetch_stripe_status( $result->transaction_status );

				$processed_results[] = array(
					'id'                         => $result->id,
					'serial_no'                  => $serial_no++,
					'service_id'                 => $result->service_id,
					'service_name'               => $result->service_name,
					'category'                   => $result->service_category,
					'booking_created_at'         => $booking_created_at->format( 'd/m/y H:i' ),
					'booking_date'               => $service_date->format( 'd/m/y H:i' ),
					'first_name'                 => $billing_details['billing_first_name'] ?? '',
					'last_name'                  => $billing_details['billing_last_name'] ?? '',
					'contact_no'                 => $billing_details['billing_contact'] ?? '',
					'email_address'              => $billing_details['billing_email'] ?? '',
					'total_cost'                 => $result->total_cost,
					'is_frontend_booking'        => $result->is_frontend_booking,
					'ordered_from'               => $result->is_frontend_booking ? 'Frontend' : 'Backend',
					'order_status'               => $result->order_status,
					'transaction_status'         => $result->transaction_status ?? '',
					'booking_key'                => $result->booking_key ?? '',
					'checkout_key'               => $result->checkout_key ?? '',
					'booking_type'               => $result->booking_type ?? '',
					'paid_at'                    => $result->paid_at ?? '',
					'updated_paid_at'            => $result->updated_paid_at ?? '',
					'is_active'                  => $result->is_active ?? 0,
					'stripe_status'              => $stripe_status,
					'service_participants'       => $result->service_participants ?? 0,
					'extra_service_participants' => $result->extra_service_participants ?? 0,
					'service_cost'               => $result->service_cost ?? 0,
					'extra_service_cost'         => $result->extra_service_cost ?? 0,
					'discount'                   => $result->discount ?? 0,
				);
			}
		}

		return $processed_results;
	}//end bm_fetch_all_orders_with_customer_data()


	/**
	 * Fetch all failed order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_failed_transactions_with_customer_data() {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$timezone   = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

		$results = $dbhandler->get_all_result(
			'FAILED_TRANSACTIONS',
			'*',
			1,
			'results',
			0,
			false,
			'id',
			'DESC'
		);

		$processed_results = array();
		$serial_no         = 1;

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$booking_data    = isset( $result->booking_data ) ? maybe_unserialize( $result->booking_data ) : array();
				$customer_data   = isset( $result->customer_data ) ? maybe_unserialize( $result->customer_data ) : array();
				$billing_details = $customer_data['billing_details'] ?? array();

				$booking_slots = $booking_data['booking_slots'] ?? '';
				$slot_time     = ! empty( $booking_slots ) ? explode( ' - ', $booking_slots )[0] : '00:00';

				$transaction_created = new DateTime( $result->created_at, new DateTimeZone( $timezone ) );
				$service_date        = new DateTime(
					( $booking_data['booking_date'] ?? '' ) . ' ' . $slot_time,
					new DateTimeZone( $timezone )
				);

				$stripe_status = $this->bm_fetch_stripe_status( $result->payment_status );

				$processed_results[] = array(
					'id'                         => $result->id,
					'serial_no'                  => $serial_no++,
					'service_id'                 => $booking_data['service_id'] ?? 0,
					'service_name'               => $booking_data['service_name'] ?? '',
					'booking_created_at'         => $transaction_created->format( 'd/m/y H:i' ),
					'booking_date'               => $service_date->format( 'd/m/y H:i' ),
					'first_name'                 => $billing_details['billing_first_name'] ?? '',
					'last_name'                  => $billing_details['billing_last_name'] ?? '',
					'contact_no'                 => $billing_details['billing_contact'] ?? '',
					'email_address'              => $billing_details['billing_email'] ?? '',
					'total_cost'                 => $booking_data['total_cost'] ?? 0,
					'is_frontend_booking'        => 1,
					'ordered_from'               => 'Frontend',
					'order_status'               => 'failed',
					'transaction_status'         => $result->payment_status ?? '',
					'stripe_status'              => $stripe_status,
					'booking_key'                => $result->booking_key ?? '',
					'checkout_key'               => $result->checkout_key ?? '',
					'booking_type'               => $booking_data['booking_type'] ?? '',
					'paid_at'                    => '',
					'updated_paid_at'            => '',
					'is_active'                  => 0,
					'original_booking_id'        => $booking_data['id'] ?? 0,
					'is_refunded'                => $result->is_refunded ?? 0,
					'service_participants'       => $booking_data['total_svc_slots'] ?? 0,
					'extra_service_participants' => $booking_data['total_ext_svc_slots'] ?? 0,
					'service_cost'               => $booking_data['service_cost'] ?? 0,
					'extra_service_cost'         => $booking_data['extra_svc_cost'] ?? 0,
					'discount'                   => $booking_data['discount'] ?? 0,
				);
			}
		}

		return $processed_results;
	}//end bm_fetch_all_failed_transactions_with_customer_data()


	/**
	 * Fetch all archived order data
	 *
	 * @author Darpan
	 */
	public function bm_fetch_all_archived_orders_with_customer_data() {
		$dbhandler  = new BM_DBhandler();
		$bmrequests = new BM_Request();
		$timezone   = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );

		$results = $dbhandler->get_all_result(
			'BOOKING_ARCHIVE',
			'*',
			1,
			'results',
			0,
			false,
			'id',
			'DESC'
		);

		$processed_results = array();
		$serial_no         = 1;

		if ( ! empty( $results ) ) {
			foreach ( $results as $result ) {
				$booking_data     = isset( $result->booking_data ) ? maybe_unserialize( $result->booking_data ) : array();
				$transaction_data = isset( $result->transaction_data ) ? maybe_unserialize( $result->transaction_data ) : array();
				$customer_id      = $booking_data->customer_id ?? 0;

				$customer        = $dbhandler->get_row( 'CUSTOMERS', array( 'id' => $customer_id ) );
				$billing_details = isset( $customer->billing_details ) ? maybe_unserialize( $customer->billing_details ) : array();

				$booking_slots = isset( $booking_data->booking_slots ) && ! empty( $booking_data->booking_slots ) ? maybe_unserialize( $booking_data->booking_slots ) : array();
				$slot_time     = isset( $booking_slots['from'] ) ? $booking_slots['from'] : '00:00';

				$archive_date = new DateTime( $result->deleted_at, new DateTimeZone( $timezone ) );
				$service_date = new DateTime(
					( $booking_data->booking_date ?? '' ) . ' ' . $slot_time,
					new DateTimeZone( $timezone )
				);

				$stripe_status = $this->bm_fetch_stripe_status( $transaction_data->payment_status );

				$processed_results[] = array(
					'id'                         => $result->id,
					'serial_no'                  => $serial_no++,
					'service_id'                 => $booking_data->service_id ?? 0,
					'service_name'               => $booking_data->service_name ?? '',
					'booking_created_at'         => $archive_date->format( 'd/m/y H:i' ),
					'booking_date'               => $service_date->format( 'd/m/y H:i' ),
					'first_name'                 => $billing_details['billing_first_name'] ?? '',
					'last_name'                  => $billing_details['billing_last_name'] ?? '',
					'contact_no'                 => $billing_details['billing_contact'] ?? '',
					'email_address'              => $billing_details['billing_email'] ?? '',
					'total_cost'                 => $booking_data->total_cost ?? 0,
					'is_frontend_booking'        => $booking_data->is_frontend_booking ?? 0,
					'ordered_from'               => ( $booking_data->is_frontend_booking ?? 0 ) ? 'Frontend' : 'Backend',
					'order_status'               => 'archived',
					'transaction_status'         => $transaction_data->payment_status ?? '',
					'stripe_status'              => $stripe_status,
					'booking_key'                => $booking_data->booking_key ?? '',
					'checkout_key'               => $booking_data->checkout_key ?? '',
					'booking_type'               => $booking_data->booking_type ?? '',
					'paid_at'                    => '',
					'updated_paid_at'            => '',
					'is_active'                  => 0,
					'original_id'                => $result->original_id ?? 0,
					'original_order_status'      => $booking_data->order_status ?? '',
					'deleted_at'                 => $result->deleted_at ?? '',
					'deleted_by'                 => $result->deleted_by ?? 0,
					'service_participants'       => $booking_data->total_svc_slots ?? 0,
					'extra_service_participants' => $booking_data->total_ext_svc_slots ?? 0,
					'service_cost'               => $booking_data->service_cost ?? 0,
					'extra_service_cost'         => $booking_data->extra_svc_cost ?? 0,
					'discount'                   => $booking_data->disount_amount ?? 0,
				);
			}
		}

		return $processed_results;
	}//end bm_fetch_all_archived_orders_with_customer_data()


	/**
	 * Fetch stripe payment status
	 *
	 * @author Darpan
	 */
	public function bm_fetch_stripe_status( $transaction_status ) {
		if ( empty( $transaction_status ) ) {
			return 'failed';
		}

		$status_map = array(
			'requires_capture'        => 'pending',
			'failed'                  => 'failed',
			'cancelled'               => 'cancelled',
			'refunded'                => 'refunded',
			'free'                    => 'Free order',
			'succeeded'               => 'paid',
			'requires_payment_method' => 'failed',
		);

		return $status_map[ strtolower( $transaction_status ) ] ?? strtolower( $transaction_status );
	}//end bm_fetch_stripe_status()


	/**
	 * Update checkin status as expired
	 *
	 * @author Darpan
	 */
	public function bm_update_checkin_status_as_expired( $booking_id ) {
		( new BM_DBhandler() )->update_row(
			'CHECKIN',
			'booking_id',
			$booking_id,
			array( 'status' => 'expired' ),
		);
	}//end bm_update_checkin_status_as_expired()


	/**
	 * Check if service is allowed to be added as a gift
	 *
	 * @author Darpan
	 */
	public function bm_is_service_allowed_as_gift( $service_id = 0 ) {
		$settings = ( new BM_DBhandler() )->get_value( 'SERVICE', 'service_settings', $service_id, 'id' );
		$settings = ! empty( $settings ) ? maybe_unserialize( $settings ) : array();

		if ( isset( $settings['allow_as_gift'] ) && $settings['allow_as_gift'] == 1 ) {
			return true;
		}

		return false;
	}//end bm_is_service_allowed_as_gift()


	/**
	 * Check if service price perperson text is shown
	 *
	 * @author Darpan
	 */
	public function bm_is_service_per_person_text_shown( $service_id = 0 ) {
		$settings = ( new BM_DBhandler() )->get_value( 'SERVICE', 'service_settings', $service_id, 'id' );
		$settings = ! empty( $settings ) ? maybe_unserialize( $settings ) : array();

		if ( isset( $settings['show_per_person_text'] ) && $settings['show_per_person_text'] == 1 ) {
			return true;
		}

		return false;
	}//end bm_is_service_per_person_text_shown()


	/**
	 * Mark a voucher expired
	 *
	 * @author Darpan
	 */
	public function bm_mark_vouchers_expired( $voucher_id = 0 ) {
		$voucher_update_data = array(
			'is_expired' => 1,
			'status'     => 0,
			'updated_at' => $this->bm_fetch_current_wordpress_datetime_stamp(),
		);

		$update = ( new BM_DBhandler() )->update_row( 'VOUCHERS', 'id', $voucher_id, $voucher_update_data, '', '%d' );

		if ( $update ) {
			return true;
		}

		return false;
	}//end bm_mark_vouchers_expired()


	/**
	 * Check if voucher is expired
	 *
	 * @author Darpan
	 */
	public function bm_is_voucher_expired( $voucher_code = '' ) {
		$is_expired = ( new BM_DBhandler() )->get_value( 'VOUCHERS', 'is_expired', $voucher_code, 'code' );

		if ( $is_expired == 1 ) {
			return true;
		}

		return false;
	}//end bm_is_voucher_expired()


	/**
	 * Get voucher expiry date
	 *
	 * @author Darpan
	 */
	public function bm_get_voucher_expiry_date( $created_date = '' ) {
		$voucher_id     = 0;
		$dbhandler      = new BM_DBhandler();
		$voucher_expiry = $dbhandler->get_global_option_value( 'bm_voucher_expiry' );
		$voucher_expiry = $voucher_expiry > 0 ? floor( $voucher_expiry ) : 30;
		$timezone       = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$now            = new DateTime( 'now', new DateTimeZone( $timezone ) );

		if ( ! empty( $created_date ) ) {
			$now = new DateTime( $created_date, new DateTimeZone( $timezone ) );
		}

		$voucher_expiry_date = $now->add( new DateInterval( "P{$voucher_expiry}D" ) );
		$voucher_expiry_date = $voucher_expiry_date->format( 'Y-m-d H:i' );

		return $voucher_expiry_date;
	}//end bm_get_voucher_expiry_date()


	/**
	 * Generate date range
	 *
	 * @author Darpan
	 */
	public function bm_generate_date_range( $start = '', $durationInDays = 30 ): array {
		$timezone  = ( new BM_DBhandler() )->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
		$startDate = new DateTime( 'now', new DateTimeZone( $timezone ) );

		if ( $start ) {
			$startDate = new DateTime( $start, new DateTimeZone( $timezone ) );
		}

		$endDate   = $startDate->modify( '+' . $durationInDays . 'days' );
		$interval  = new DateInterval( 'P1D' );
		$dateRange = new DatePeriod( $startDate, $interval, $endDate );

		$dates = array();

		foreach ( $dateRange as $date ) {
			$dates[] = $date->format( 'Y-m-d' );
		}

		return $dates;
	}//end bm_generate_date_range()


	/**
	 * Get theme colour from colours array
	 *
	 * @author Darpan
	 */
	public function bm_get_theme_color( $slug ) {
		$theme_settings = array();
		if ( function_exists( 'wp_get_global_settings' ) ) {
			$theme_settings = wp_get_global_settings();
		}

		$theme_colors = isset( $theme_settings['color']['palette']['theme'] ) ? $theme_settings['color']['palette']['theme'] : array();
		if ( ! empty( $theme_colors ) && is_array( $theme_colors ) ) {
			foreach ( $theme_colors as $color ) {
				if ( isset( $color['slug'] ) && $color['slug'] === $slug ) {
					return $color['color'];
				}
			}
		}
		return null;
	}//end bm_get_theme_color()


	/**
	 * Sort array by key
	 *
	 * @author Darpan
	 */
	public function bm_sort_array_by_key( $array, $key, $ascending = true ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return $array;
		}

		usort(
			$array,
			function ( $a, $b ) use ( $key, $ascending ) {
				$a_value = is_object( $a ) ? $a->$key : $a[ $key ];
				$b_value = is_object( $b ) ? $b->$key : $b[ $key ];

				if ( $a_value == $b_value ) {
					return 0;
				}

				$result = ( $a_value < $b_value ) ? -1 : 1;
				return $ascending ? $result : -$result;
			}
		);

		return $array;
	}//end bm_sort_array_by_key()


	public function bm_fetch_service_planner_reservation_list( $service_id, $date, $start_time ) {
		$joins = array(
			array(
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'c.id = b.customer_id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 't.booking_id = b.id',
				'type'  => 'LEFT',
			),
		);

		$columns = '
            b.id as booking_id,
            b.wc_order_id,
            b.service_name,
            b.booking_date,
            b.booking_slots,
            b.total_svc_slots as svc_participants,
            b.total_ext_svc_slots as ex_svc_participants,
            b.total_cost,
            b.order_status,
            b.booking_key,
            b.booking_created_at,
            c.customer_name,
            c.billing_details,
            c.customer_email,
            t.payment_status
        ';

		$where = array(
			'b.service_id'   => array( '=' => $service_id ),
			'b.booking_date' => array( '=' => $date ),
		);

		$additional = 'ORDER BY b.booking_created_at DESC';

		$reservations = ( new BM_DBhandler() )->get_results_with_join(
			array( 'BOOKING', 'b' ),
			$columns,
			$joins,
			$where,
			'results',
			0,
			false,
			null,
			false,
			$additional
		);

		$formatted_data = array();

		if ( ! empty( $reservations ) ) {
			foreach ( $reservations as $reservation ) {
				$booking_slots = maybe_unserialize( $reservation->booking_slots );

				$billing_details = maybe_unserialize( $reservation->billing_details );
				$billing_details = is_array( $billing_details ) ? $billing_details : array();

				if (
					is_array( $booking_slots ) &&
					isset( $booking_slots['from'] ) &&
					$booking_slots['from'] == $start_time
				) {
					$formatted_data[] = array(
						'reference_number'    => $reservation->booking_key ?: $reservation->booking_id,
						'last_name'           => $billing_details['billing_last_name'] ?? '-',
						'svc_participants'    => $reservation->svc_participants,
						'ex_svc_participants' => $reservation->ex_svc_participants,
						'booking_status'      => $this->bm_fetch_order_status_key_value( $reservation->order_status ?? 'pending' ),
						'payment_status'      => $this->bm_fetch_stripe_status( $reservation->payment_status ),
						'total'               => $this->bm_fetch_price_in_global_settings_format( $reservation->total_cost, true ),
						'booking_id'          => $reservation->booking_id,
						'slot_time'           => $booking_slots['from'] . ' - ' . $booking_slots['to'], // Format time range
					);
				}
			}
		}

		return $formatted_data;
	}


	public function bm_fetch_order_details_for_single_page( $booking_id ) {
		$joins = array(
			array(
				'table' => 'CUSTOMERS',
				'alias' => 'c',
				'on'    => 'c.id = b.customer_id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'TRANSACTIONS',
				'alias' => 't',
				'on'    => 't.booking_id = b.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'SLOTCOUNT',
				'alias' => 'sc',
				'on'    => 'sc.booking_id = b.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'EXTRASLOTCOUNT',
				'alias' => 'exsc',
				'on'    => 'exsc.booking_id = b.id',
				'type'  => 'LEFT',
			),
			array(
				'table' => 'EXTRA',
				'alias' => 'ex',
				'on'    => 'ex.id = exsc.extra_svc_id',
				'type'  => 'LEFT',
			),
		);

		$columns = '
            b.*,
            b.disount_amount as booking_discount,
            c.customer_name,
            c.customer_email,
            c.billing_details,
            t.paid_amount,
            t.paid_amount_currency,
            t.payment_method,
            t.payment_status,
            t.transaction_id,
            sc.slot_id,
            sc.current_slots_booked as participants,
            sc.slot_booked_at,
            GROUP_CONCAT(DISTINCT CONCAT(ex.extra_name, "|", exsc.slots_booked, "|", ex.extra_price)) as extra_services
        ';

		$where = array(
			'b.id' => array( '=' => $booking_id ),
		);

		$additional = 'GROUP BY b.id';

		$order_data = ( new BM_DBhandler() )->get_results_with_join(
			array( 'BOOKING', 'b' ),
			$columns,
			$joins,
			$where,
			'row',
			0,
			false,
			null,
			false,
			$additional
		);

		if ( ! $order_data ) {
			return false;
		}

		$billing_details = maybe_unserialize( $order_data->billing_details );
		$billing_details = is_array( $billing_details ) ? $billing_details : array();
		$invoice_details = maybe_unserialize( $order_data->field_values );
		$invoice_details = is_array( $invoice_details ) ? $invoice_details : array();

		$formatted_data = array(
			'customer_info'    => array(
				'id'         => $order_data->customer_id ?? 0,
				'first_name' => $billing_details['billing_first_name'] ?? ( $order_data->customer_name ?? '' ),
				'last_name'  => $billing_details['billing_last_name'] ?? '',
				'phone'      => $billing_details['billing_contact'] ?? '',
				'email'      => $billing_details['billing_email'] ?? $order_data->customer_email,
			),
			'order_details'    => array(
				'email'             => $billing_details['billing_email'] ?? $order_data->customer_email,
				'service_date_time' => $this->format_service_date_time( $order_data ),
				'order_date_time'   => ( new DateTime( $order_data->booking_created_at ) )->format( 'd/m/Y H:i:s' ),
				'quantity'          => $order_data->participants,
				'price'             => $this->bm_fetch_price_in_global_settings_format( $order_data->total_cost, true ),
				'order_status'      => $this->bm_fetch_order_status_key_value( $order_data->order_status ?? 'pending' ),
				'payment_status'    => $this->bm_fetch_stripe_status( $order_data->payment_status ),
				'discount'          => $this->bm_fetch_price_in_global_settings_format( $order_data->booking_discount, true ),
			),
			'ordered_products' => $this->format_ordered_products( $order_data ),
			'subtotal'         => $this->bm_fetch_price_in_global_settings_format( $order_data->subtotal, true ),
			'billing_details'  => array(
				'address'  => $billing_details['billing_address'] ?? '',
				'country'  => $billing_details['billing_country'] ?? '',
				'state'    => $billing_details['billing_state'] ?? '',
				'city'     => $billing_details['billing_city'] ?? '',
				'postcode' => $billing_details['billing_postcode'] ?? '',
				'notes'    => $billing_details['customer_order_note'] ?? '',
			),
			'invoice_details' => [
				'invoice_company_address' => $invoice_details['invoice_company_address'] ?? '',
				'invoice_company_name'    => $invoice_details['invoice_company_name'] ?? '',
				'invoice_company_country'  => $invoice_details['invoice_company_country'] ?? '',
				'invoice_vat_id'  => $invoice_details['invoice_vat_id'] ?? '',
			]
		);

		return $formatted_data;
	}

	private function format_service_date_time( $booking_data ) {
		$date = $booking_data->booking_date ?? '';
		$date = ( new DateTime( $date ) )->format( 'd/m/Y' );
		$time = '';

		if ( ! empty( $booking_data->booking_slots ) ) {
			$slots = maybe_unserialize( $booking_data->booking_slots );
			if ( is_array( $slots ) ) {
				if ( isset( $slots['from'] ) ) {
					$time = $slots['from'] . ' - ' . $slots['to'];
				}
			}
		}

		return $date . ( $time ? ' ' . $time : '' );
	}

	private function format_ordered_products( $booking_data ) {
		$products = array();

		$products[] = array(
			'product'        => $booking_data->service_name,
			'total_quantity' => $booking_data->participants,
			'revenue'        => $this->bm_fetch_price_in_global_settings_format( $booking_data->total_cost, true ),
		);

		if ( ! empty( $booking_data->extra_services ) ) {
			$extra_services = explode( ',', $booking_data->extra_services );
			foreach ( $extra_services as $extra ) {
				$parts = explode( '|', $extra );
				if ( count( $parts ) === 3 ) {
					$revenue    = $parts[1] * $parts[2];
					$products[] = array(
						'product'        => $parts[0],
						'total_quantity' => $parts[1],
						'revenue'        => $this->bm_fetch_price_in_global_settings_format( $revenue, true ),
					);
				}
			}
		}

		return $products;
	}


	public function bm_has_dynamic_stopsales_for_date( $service_id, $date ) {
		$dbhandler = new BM_DBhandler();
		$service   = $dbhandler->get_row( 'SERVICE', $service_id );
		$variable  = isset( $service->variable_stopsales ) ? maybe_unserialize( $service->variable_stopsales ) : array();
		return ( ! empty( $variable['date'] ) && in_array( $date, $variable['date'], true ) );
	}

	public function bm_get_current_trp_language() {
		if ( ! class_exists( 'TRP_Translate_Press' ) ) {
			return false;
		}

		$trp       = TRP_Translate_Press::get_trp_instance();
		$urlc      = $trp->get_component( 'url_converter' );
		$settings  = get_option( 'trp_settings', array() );
		$url_slugs = isset( $settings['url-slugs'] ) && is_array( $settings['url-slugs'] ) ? $settings['url-slugs'] : array();

		$full = $urlc->get_lang_from_url_string();

		if ( empty( $full ) && ! empty( $settings['default-language'] ) ) {
			$full = $settings['default-language'];
		}

		if ( empty( $full ) ) {
			return false;
		}

		if ( isset( $url_slugs[ $full ] ) && $url_slugs[ $full ] !== '' ) {
			return $url_slugs[ $full ];
		}
		return strtolower( substr( $full, 0, 2 ) );
	}


	public function bm_switch_locale_by_booking_reference( $booking_reference = '', $lang = '' ) {
		if ( empty( $lang ) && ! empty( $booking_reference ) ) {
			$lang = get_option( 'trp_lang_' . $booking_reference, false );
		}
		if ( $lang ) {
			$locale_map = array(
				'en' => 'en_US',
				'it' => 'it_IT',
				// add others if needed
			);
			$current_locale = isset( $locale_map[ $lang ] ) ? $locale_map[ $lang ] : 'en_US';
			$old_locale     = determine_locale();
			switch_to_locale( $current_locale );
			return $old_locale;
		}
		return false;
	}

	public function bm_restore_locale( $old_locale ) {
		if ( $old_locale ) {
			switch_to_locale( $old_locale );
		}
	}

	/**
     * Check date availability
     *
     * @author Jyoti
     */
    public function bm_date_is_bookable( $date = '' ) {
        $dbhandler   = new BM_DBhandler();
        $is_bookable = true;
        if ( !empty( $date ) ) {
            $services = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ) );
            if ( !empty( $services ) ) {
                foreach ( $services as $service ) {
                    if ( $this->bm_service_is_bookable( $service->id, $date ) ) {
                        break;
                    }
                }
            } else {
                $is_bookable = false;
            }
        } else {
            $is_bookable = false;
        }

        return $is_bookable;
    } //end bm_date_is_bookable()

    /**
     * Get no of services bookable on a date
     *
     * @author Jyoti
     */

    public function bm_get_no_of_services_bookable_on_date( $date ) {
         $dbhandler              = new BM_DBhandler();
        $no_of_services_bookable = 0;
        $priceArr                = array();
        if ( !empty( $date ) ) {
            $services       = $dbhandler->get_all_result( 'SERVICE', '*', array( 'is_service_front' => 1 ) );
            $total_services = !empty( $services ) ? count( $services ) : 0;
            if ( !empty( $services ) ) {
                foreach ( $services as $service ) {
                    $slotAvailable = empty(
                        $this->bm_fetch_service_time_slot_array_by_service_id(
                            array(
								'id'   => $service->id,
								'date' => $date,
                            )
                        )
                    ) ? false : true;
                    if ( $this->bm_service_is_bookable( $service->id, $date ) && $slotAvailable ) {
                        $no_of_services_bookable++;
                        $svc_price  = $this->bm_fetch_service_price_by_service_id_and_date( $service->id, $date, 'global_format' );
                        $priceArr[] = $svc_price;
                    }
                }
            }
        }

        return array(
			'date'                         => $date,
			'is_bookable'                  => ( $no_of_services_bookable > 0 ) ? true : false,
			'total_service'                => $no_of_services_bookable,
			'price'                        => !empty( $priceArr ) ? min( $priceArr ) : 0,
			'available_service_percentage' => ( $total_services > 0 ) ? round( ( $no_of_services_bookable / $total_services ) * 100 ) : 0,
		);
    } //end bm_get_no_of_services_bookable_on_date()

    /**
     * Get no of services bookable on a date
     *
     * @author Jyoti
     */

    public function bm_get_no_of_services_price_by_category( $category_id ) {
         $dbhandler     = new BM_DBhandler();
        $priceArr       = array();
        $services       = $dbhandler->get_all_result(
            'SERVICE',
            '*',
            array(
				'is_service_front' => 1,
				'service_category' => $category_id,
            )
        );
        $total_services = !empty( $services ) ? count( $services ) : 0;
        if ( !empty( $services ) ) {
            foreach ( $services as $service ) {
                $svc_default_price = !empty( $service ) && isset( $service->default_price ) && !empty( $service->default_price ) ? esc_attr( $service->default_price ) : '';
                $svc_default_price = $this->bm_fetch_price_in_global_settings_format( $svc_default_price, true );
                $priceArr[]        = $svc_default_price;
            }
        }

        return array(
			'total_service' => $total_services,
			'price'         => !empty( $priceArr ) ? min( $priceArr ) : 0,
		);
    } //end bm_get_no_of_services_price_by_category()

    /**
     * Fetch Dynamic service time slot details by service id and date (no design)
     *
     * @author Jyoti
     */
    public function bm_fetch_service_time_slot_detail_by_service_id( $data = array() ) {
         $dbhandler  = new BM_DBhandler();
        $timezone    = $dbhandler->get_global_option_value( 'bm_booking_time_zone', 'Asia/Kolkata' );
        $slot_format = $dbhandler->get_global_option_value( 'bm_flexi_service_time_slot_format', '24' );
        $now         = new DateTime( 'now', new DateTimeZone( $timezone ) );

        $response = array();
        if ( !empty( $data ) ) {
            $service_id      = isset( $data['id'] ) ? $data['id'] : 0;
            $date            = isset( $data['date'] ) ? $data['date'] : '';
            $current_date    = $now->format( 'Y-m-d' );
            $current_time    = $now->format( 'H:i' );
            $stopsales       = 0;
            $currentDateTime = $current_date . ' ' . $current_time;

            if ( isset( $service_id ) && !empty( $service_id ) && isset( $date ) && !empty( $date ) ) {
                $service              = $dbhandler->get_row( 'SERVICE', $service_id );
                $time_row             = $dbhandler->get_row( 'TIME', $service_id );
                $stopsales            = $this->bm_fetch_service_stopsales_by_service_id( $service_id, $date );
                $global_show_to_slots = $dbhandler->get_global_option_value( 'bm_show_service_to_time_slot', 0 );
                if ( !empty( $stopsales ) ) {
                    $stopSalesHours   = floor( $stopsales );
                    $stopSalesMinutes = ( $stopsales - $stopSalesHours ) * 60;

                    if ( $this->bm_has_dynamic_stopsales_for_date( $service_id, $date ) ) {
                        $endDateTime = new DateTime( $date . ' ' . $now->format( 'H:i' ), new DateTimeZone( $timezone ) );
                    } else {
                        $endDateTime = clone $now;
                    }

                    $endDateTime->add( new DateInterval( "PT{$stopSalesHours}H{$stopSalesMinutes}M" ) );
                    $endDateTime = $endDateTime->format( 'Y-m-d H:i' );
                }
            }

            if ( isset( $time_row ) && !empty( $time_row ) && isset( $service ) && !empty( $service ) ) {
                $total_slots         = isset( $time_row->total_slots ) ? $time_row->total_slots : 0;
                $time_slots          = isset( $time_row->time_slots ) ? maybe_unserialize( $time_row->time_slots ) : array();
                $variable_time_slots = isset( $service->variable_time_slots ) ? maybe_unserialize( $service->variable_time_slots ) : array();
                $dates               = !empty( $variable_time_slots ) ? wp_list_pluck( $variable_time_slots, 'date' ) : array();
                $slotType            = '';
                if ( !empty( $variable_time_slots ) && !empty( $dates ) && in_array( $date, $dates, true ) ) {
                    $index     = (int) array_search( $date, $dates );
                    $slot_data = $variable_time_slots[ $index ];

                    for ( $i = 1; $i <= $slot_data['total_slots']; $i++ ) {
                        $is_slot_disabled = isset( $slot_data['disable'][ $i ] ) ? $slot_data['disable'][ $i ] : 0;

                        if ( $is_slot_disabled != 1 ) {
                            $capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $slot_data['from'][ $i ], $date, $slot_data['max_cap'][ $i ], 1 );

                            $startSlot = new DateTime( $date . ' ' . $slot_data['from'][ $i ], new DateTimeZone( $timezone ) );
                            $startSlot = $startSlot->format( 'Y-m-d H:i' );

                            if ( ( !empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
                                $slotType = 'readonly';
                            } elseif ( ( empty( $stopsales ) && ( strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ) ) {
                                $slotType = 'readonly';
                            } elseif ( $capacity_left <= 0 ) {
                                $slotType = 'readonly';
                            } else {
                                $slotType = 'active';
                            }

                            $time_slots_html = '';
                            if ( $global_show_to_slots == 0 ) {
                                if ( $slot_format == '12' ) {
                                    $time_slots_html .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
                                } else {
                                    $time_slots_html .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
                                }
                            } else {
                                $is_slot_hidden = isset( $slot_data['hide_to_slot'][ $i ] ) ? $slot_data['hide_to_slot'][ $i ] : 0;

                                if ( $is_slot_hidden != 1 ) {
                                    if ( $slot_format == '12' ) {
                                        $time_slots_html .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
                                        $time_slots_html .= ' - ';
                                        $time_slots_html .= isset( $slot_data['to'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['to'][ $i ] ) : '';
                                    } else {
                                        $time_slots_html .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
                                        $time_slots_html .= ' - ';
                                        $time_slots_html .= isset( $slot_data['to'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['to'][ $i ] ) : '';
                                    }
                                } elseif ( $is_slot_hidden == 1 ) {
                                    if ( $slot_format == '12' ) {
                                        $time_slots_html .= isset( $slot_data['from'][ $i ] ) ? $this->bm_am_pm_format( $slot_data['from'][ $i ] ) : '';
                                    } else {
                                        $time_slots_html .= isset( $slot_data['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $slot_data['from'][ $i ] ) : '';
                                    }
                                }
                            }

                            if ( ( !empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) ) {
                                $slot_data['capacity_left'][ $i ] =  $capacity_left;
                            } elseif ( empty( $stopsales ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
                                $slot_data['capacity_left'][ $i ] =  $capacity_left;
                            }
                            $response[] = array(
                                'slot_type'             => $slotType,
                                'time_slot'             => $time_slots_html,
                                'capacity_left'         => $capacity_left,
                                'capacity_left_percent' => !empty( $slot_data['max_cap'][ $i ] ) && $slot_data['max_cap'][ $i ] > 0 ? round( ( $capacity_left / $slot_data['max_cap'][ $i ] ) * 100 ) : 0,
                                'max_capacity'          => isset( $slot_data['max_cap'][ $i ] ) ? $slot_data['max_cap'][ $i ] : 0,
                                'min_capacity'          => isset( $slot_data['min_cap'][ $i ] ) ? $slot_data['min_cap'][ $i ] : 0,
                            );
                        } //end if
                    } //end for
                } else {
                    for ( $i = 1; $i <= $total_slots; $i++ ) {
                        $is_slot_disabled = isset( $time_slots['disable'][ $i ] ) ? $time_slots['disable'][ $i ] : 0;

                        if ( $is_slot_disabled != 1 ) {
                            $capacity_left = $this->bm_fetch_available_slot_capacity_by_service_and_slot_id( $service_id, $i, $time_slots['from'][ $i ], $date, $time_slots['max_cap'][ $i ], 0 );

                            $startSlot = new DateTime( $date . ' ' . $time_slots['from'][ $i ], new DateTimeZone( $timezone ) );
                            $startSlot = $startSlot->format( 'Y-m-d H:i' );

                            if ( ( !empty( $stopsales ) && ( strtotime( $endDateTime ) > strtotime( $startSlot ) ) ) ) {
                                $slotType = 'readonly';
                            } elseif ( ( empty( $stopsales ) && ( strtotime( $currentDateTime ) > strtotime( $startSlot ) ) ) ) {
                                $slotType = 'readonly';
                            } elseif ( $capacity_left <= 0 ) {
                                $slotType = 'readonly';
                            } else {
                                $slotType = 'active';
                            }
                            $time_slots_html = '';
                            if ( $global_show_to_slots == 0 ) {
                                if ( $slot_format == '12' ) {
                                    $time_slots_html .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
                                } else {
                                    $time_slots_html .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
                                }
                            } else {
                                $is_slot_hidden = isset( $time_slots['hide_to_slot'][ $i ] ) ? $time_slots['hide_to_slot'][ $i ] : 0;

                                if ( $is_slot_hidden != 1 ) {
                                    if ( $slot_format == '12' ) {
                                        $time_slots_html .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
                                        $time_slots_html .= ' - ';
                                        $time_slots_html .= isset( $time_slots['to'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['to'][ $i ] ) : '';
                                    } else {
                                        $time_slots_html .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
                                        $time_slots_html .= ' - ';
                                        $time_slots_html .= isset( $time_slots['to'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['to'][ $i ] ) : '';
                                    }
                                } elseif ( $is_slot_hidden == 1 ) {
                                    if ( $slot_format == '12' ) {
                                        $time_slots_html .= isset( $time_slots['from'][ $i ] ) ? $this->bm_am_pm_format( $time_slots['from'][ $i ] ) : '';
                                    } else {
                                        $time_slots_html .= isset( $time_slots['from'][ $i ] ) ? $this->bm_twenty_fourhrs_format( $time_slots['from'][ $i ] ) : '';
                                    }
                                }
                            }

                            if ( ( !empty( $stopsales ) && ( strtotime( $endDateTime ) <= strtotime( $startSlot ) ) ) ) {
                                $time_slots['capacity_left'][ $i ] =  $capacity_left;
                            } elseif ( empty( $stopsales ) && ( strtotime( $currentDateTime ) <= strtotime( $startSlot ) ) ) {
                                $time_slots['capacity_left'][ $i ] =  $capacity_left;
                            }

                            $response[] = array(
                                'slot_type'             => $slotType,
                                'time_slot'             => $time_slots_html,
                                'capacity_left'         => $capacity_left,
                                'capacity_left_percent' => !empty( $time_slots['max_cap'][ $i ] ) && $time_slots['max_cap'][ $i ] > 0 ? round( ( $capacity_left / $time_slots['max_cap'][ $i ] ) * 100 ) : 0,
                                'max_capacity'          => isset( $time_slots['max_cap'][ $i ] ) ? $time_slots['max_cap'][ $i ] : 0,
                                'min_capacity'          => isset( $time_slots['min_cap'][ $i ] ) ? $time_slots['min_cap'][ $i ] : 0,
                            );
                        } //end if
                    } //end for
                } //end if
            }
        } //end if
        return $response;
    } //end bm_fetch_service_time_slot_detail_by_service_id()



}//end class
