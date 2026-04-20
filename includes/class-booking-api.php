<?php

use MailPoetVendor\Doctrine\ORM\Query\Expr\Func;
use WPForms\Vendor\Core\Logger\ConsoleLogger;

if (! defined('ABSPATH')) exit;

class Booking_API
{
    /**
     * The ID of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since  1.0.0
     * @access private
     * @var    string    $version    The current version of this plugin.
     */
    private $version;

    protected static $counter;
    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of the plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version         = $version;

        add_action('rest_api_init', [$this, 'register_routes']);
    } //end __construct()

    public function register_routes()
    {
        function sanitize_array($value)
        {
            if (! is_array($value)) {
                return [];
            }

            $clean = [];

            foreach ($value as $key => $val) {
                $key = sanitize_key($key);

                if (is_array($val)) {
                    // recursive sanitize but SAFE (does not trigger WP dynamic loading)
                    $clean[$key] = sanitize_array($val);
                } else {
                    // sanitize text OR number
                    if (is_numeric($val)) {
                        $clean[$key] = $val + 0; // cast numeric
                    } else {
                        $clean[$key] = sanitize_text_field($val);
                    }
                }
            }
            return $clean;
        }

        function sanitize_int($value)
        {
            return intval($value);
        }

        register_rest_route('booking/v1', '/settings', [
            'methods'  => 'GET',
            'callback' => [$this, 'settings'],
            'permission_callback' => '__return_true',
            'args' => []
        ]);

        register_rest_route('booking/v1', '/availability', [
            'methods'  => 'GET',
            'callback' => [$this, 'check_availability'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);

        register_rest_route('booking/v1', '/service-availability-calendar', [
            'methods'  => 'GET',
            'callback' => [$this, 'service_availability'],
            'permission_callback' => '__return_true',
            'args' => [
                'month' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_month'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);

        register_rest_route('booking/v1', '/categories', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_categories'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('booking/v1', '/services', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_services'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => false,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'category_id' => [
                    'required' => false,
                    'sanitize_callback' => 'absint',
                ],
                'gift' => [
                    'required' => false,
                    'sanitize_callback' => 'absint',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/service-details', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_service_details'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'service_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'coupon_code' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/slot-availability-calendar', [
            'methods'  => 'GET',
            'callback' => [$this, 'slot_availability'],
            'permission_callback' => '__return_true',
            'args' => [
                'month' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_month'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'service_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/has-extra', [
            'methods'  => 'GET',
            'callback' => [$this, 'has_extra'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'service_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'all' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/extras', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_extras'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'service_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'all' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/extra-details', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_extra_details'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'extra_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/addtocart', [
            'methods'  => 'POST',
            'callback' => [$this, 'addtocart'],
            'permission_callback' => '__return_true',
            'args' => [
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'service_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'total_service_booking' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'time_slot' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'extra_svc_ids' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'no_of_persons' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);

        register_rest_route('booking/v1', '/checkout', [
            'methods'  => 'POST',
            'callback' => [$this, 'checkout'],
            'permission_callback' => '__return_true',
            'args' => [
                'billing_details' => [
                    'required' => true,
                    // 'sanitize_callback' => 'sanitize_array',
                ],
                'shipping_details' => [
                    'required' => true,
                    // 'sanitize_callback' => 'sanitize_array',
                ],
                'gift_details' => [
                    'required' => false,
                    // 'sanitize_callback' => 'sanitize_array',
                ],
                'is_gift' => [
                    'required' => false,
                    // 'sanitize_callback' => 'sanitize_int',
                ],
                'booking_data' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'other_data' => [
                    'required' => false,
                    // 'sanitize_callback' => 'sanitize_array',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/check-session', [
            'methods'  => 'POST',
            'callback' => [$this, 'check_session'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/payment-process', [
            'methods'  => 'POST',
            'callback' => [$this, 'payment_process'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'checkout' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'paymentMethod' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/payment-save', [
            'methods'  => 'POST',
            'callback' => [$this, 'payment_save'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'checkout' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/countries', [
            'methods'  => 'GET',
            'callback' => [$this, 'countries'],
            'permission_callback' => '__return_true'
        ]);

        register_rest_route('booking/v1', '/states', [
            'methods'  => 'GET',
            'callback' => [$this, 'states'],
            'permission_callback' => '__return_true',
            'args' => [
                'country_code' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/coupon-list', [
            'methods'  => 'GET',
            'callback' => [$this, 'couponlist'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/auto-apply-coupon', [
            'methods'  => 'GET',
            'callback' => [$this, 'auto_apply_coupon'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'email' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_email',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/apply-coupon', [
            'methods'  => 'POST',
            'callback' => [$this, 'apply_coupon'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'email' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_email',
                ],
                'coupon_code' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/coupon-removal', [
            'methods'  => 'POST',
            'callback' => [$this, 'coupon_removal'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'coupon_code' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/check-voucher-validity', [
            'methods'  => 'POST',
            'callback' => [$this, 'check_voucher_validity'],
            'permission_callback' => '__return_true',
            'args' => [
                'voucher' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/voucher-detail', [
            'methods'  => 'POST',
            'callback' => [$this, 'get_voucher_detail'],
            'permission_callback' => '__return_true',
            'args' => [
                'voucher' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'date' => [
                    'required' => false,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/voucher-redeem', [
            'methods'  => 'POST',
            'callback' => [$this, 'redeem_voucher'],
            'permission_callback' => '__return_true',
            'args' => [
                'voucher' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'date' => [
                    'required' => true,
                    'validate_callback' => ['Booking_Validation', 'validate_date'],
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'slot' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'recipient' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_array',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/price-format', [
            'methods'  => 'GET',
            'callback' => [$this, 'priceformat'],
            'permission_callback' => '__return_true',
            'args' => [
                'service_id' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'capacity' => [
                    'required' => true,
                    'sanitize_callback' => 'absint',
                ],
                'extra_id' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'extra_capacity' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);

        register_rest_route('booking/v1', '/create-payment-intent', [
            'methods'  => 'POST',
            'callback' => [$this, 'payment_process'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'checkout' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'paymentMethod' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/check-for-refund-for-failed-payment', [
            'methods'  => 'POST',
            'callback' => [$this, 'check_for_refund_for_failed_payment'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
                'checkout_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/thankyou', [
            'methods'  => 'POST',
            'callback' => [$this, 'thankyou'],
            'permission_callback' => '__return_true',
            'args' => [
                'booking_key' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/redeem-thankyou', [
            'methods'  => 'POST',
            'callback' => [$this, 'redeem_thankyou'],
            'permission_callback' => '__return_true',
            'args' => [
                'redeem_code' => [
                    'required' => true,
                    'sanitize_callback' => 'sanitize_text_field',
                ]
            ]
        ]);

        register_rest_route('booking/v1', '/send-email', [
            'methods'  => 'POST',
            'callback' => [$this, 'sendemail'],
            'permission_callback' => '__return_true',
            'args' => [
                'email' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_email',
                ],
                'booking_key' => [
                    'required' => false,
                    'sanitize_callback' => 'sanitize_text_field',
                ],
            ]
        ]);

        register_rest_route('booking/v1', '/get-fields', [
            'methods'  => 'GET',
            'callback' => [$this, 'get_fields'],
            'permission_callback' => '__return_true'
        ]);
    }

    public function get_fields(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();
        $fields = $dbhandler->get_all_result('FIELDS', '*', 1, 'results', 0, false, 'field_position', false);

        if (!empty($fields)) {
            foreach ($fields as $key => $field) {
                $fields[$key]->field_options = isset($field->field_options) ? maybe_unserialize($field->field_options) : array();
            }
        }
        return rest_ensure_response([
            'status' => 200,
            'data'   => $fields
        ]);
    }

    public function settings(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();
        $bmrequests = new BM_Request();
        $primary_color = $dbhandler->get_global_option_value('bm_frontend_book_button_color', '#000000');
        $public_key  = $bmrequests->decrypt_key($dbhandler->get_global_option_value('bm_flexi_stripe_public_code'), 'flexibooking_public_stripe_code');

        return rest_ensure_response([
            'status' => 200,
            'data'   => [
                'primary_color' => $primary_color,
                'stripe_public_key' => $public_key,
            ]
        ]);
    }

    public function check_availability(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $params = $request->get_query_params();
        $date   = $params['date'];

        $response = $bmrequests->bm_date_is_bookable($date);
        $data       = array();
        $data['date'] = $date;
        $data['is_bookable'] = $response;



        return rest_ensure_response([
            'status' => 200,
            'data'   => $data
        ]);
    }

    public function service_availability(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $params = $request->get_query_params();
        $month   = $params['month'];

        $days_in_month = (int)date('t', mktime(0, 0, 0, (int)substr($month, 5, 2), 1, (int)substr($month, 0, 4)));
        $availability = [];

        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%s-%02d', $month, $day);
            // Check if the date is past
            if (strtotime($date) < strtotime(date('Y-m-d'))) {
                $availability[] = [
                    'date' => $date,
                    'is_bookable' => false
                ];
                continue;
            }
            $availability_response = $bmrequests->bm_get_no_of_services_bookable_on_date($date);
            $availability[] = $availability_response;
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $availability
        ]);
    }

    public function get_categories(WP_REST_Request $request)
    {
        $dbhandler      = new BM_DBhandler();
        $bmrequests     = new BM_Request();

        $params = $request->get_query_params();
        $categories   = $dbhandler->get_all_result('CATEGORY', array('id', 'cat_name', 'cat_in_front'), array('cat_in_front' => 1), 'results');
        if (!empty($categories)) {
            foreach ($categories as $category) {
                $availability_response = $bmrequests->bm_get_no_of_services_price_by_category($category->id);
                $category->service_count = $availability_response['total_service'];
                $category->price = $availability_response['price'];
            }
        }
        $allCategory = !empty($categories) ? $categories : [];

        return rest_ensure_response([
            'status' => 200,
            'data'   => $allCategory
        ]);
    }

    public function get_services(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();

        $params = $request->get_query_params();
        $date   = $params['date'];
        $category_id = $params['category'] ?? null;
        $all = isset($params['all']) && $params['all'] == 'true' ? true : false;

        $tables = 'SERVICE';
        $where = array(
            'is_service_front' => 1,
        );
        if ($category_id) {
            $where['service_category'] = $category_id;
        }

        $total_services = $dbhandler->get_all_result($tables, 'COUNT(*) as total', $where, 'var');
        $services = $dbhandler->get_all_result($tables, '*', $where, 'results');
        $response = array();
        if (!empty($services)) {
            foreach ($services as $key => $service) {
                $modified_service = $this->service_details($service, $date, $all);
                if (!empty($modified_service)) {
                    $response[] = $modified_service;
                }
            }
        }

        return rest_ensure_response([
            'status' => 200,
            'total_services' => (!empty($response)) ? $total_services : 0,
            'data'   => $response
        ]);
    }

    public function get_service_details(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();

        $params = $request->get_query_params();
        $date   = $params['date'];
        $service_id = $params['service_id'] ?? null;
        $coupon_code = $params['coupon_code'] ?? null;
        $all = isset($params['all']) && $params['all'] == 'true' ? true : false;

        $service_detail = $this->service_details($dbhandler->get_row('SERVICE', $service_id, 'id'), $date, $all);
        $service_detail = is_array($service_detail) ? $service_detail : [];

        if (!empty($service_detail)) {
            $date_slots = [];
            if ($service_id > 0) {
                for ($i = 0; $i < 4; $i++) {
                    $date = date('Y-m-d', strtotime($params['date'] . " +{$i} day"));
                    $slots = $this->service_time_slots($service_id, $date);

                    $date_slots[] = [
                        'date'  => $date,
                        'slots' => $slots,
                    ];
                }
            }

            $service_detail['date_slots'] = $date_slots;
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $service_detail,
            'message' => !empty($service_detail) ? '' : 'No service found for the given date.'
        ]);
    }

    public function slot_availability(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $params = $request->get_query_params();
        $month   = $params['month'];
        $service_id = $params['service_id'];

        $days_in_month = (int)date('t', mktime(0, 0, 0, (int)substr($month, 5, 2), 1, (int)substr($month, 0, 4)));
        $availability = [];
        for ($day = 1; $day <= $days_in_month; $day++) {
            $date = sprintf('%s-%02d', $month, $day);
            // Check if the date is past
            if (strtotime($date) < strtotime(date('Y-m-d'))) {
                $availability[] = [
                    'date' => $date,
                    'is_bookable' => false
                ];
                continue;
            }
            $availability_response = $this->service_time_slots($service_id, $date);
            $availability_response['date'] = $date;
            $availability_response['is_bookable'] = $bmrequests->bm_service_is_bookable($service_id, $date);
            $availability_response['price'] = $svc_price  = $bmrequests->bm_fetch_service_price_by_service_id_and_date($service_id, $date, 'global_format');
            $availability[] = $availability_response;
        }
        return rest_ensure_response([
            'status' => 200,
            'data'   => $availability
        ]);
    }

    public function has_extra(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();
        $bmrequests = new BM_Request();

        $params = $request->get_query_params();
        $date   = $params['date'];
        $service_id = $params['service_id'];
        $all = isset($params['all']) && $params['all'] == 'true' ? true : false;

        $global_extras = $dbhandler->get_all_result('EXTRA', '*', array('is_global' => 1), 'results');
        $extra_rows = $dbhandler->get_all_result('EXTRA', '*', array('service_id' => $service_id), 'results');

        $all_extras = array_merge(
            !empty($global_extras) ? $global_extras : [],
            !empty($extra_rows) ? $extra_rows : []
        );

        $has_extra = false;
        foreach ($all_extras as $extra_service) {
            $cap_left = $bmrequests->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date($extra_service->id, $extra_service->extra_max_cap, 0, $date);
            if ($cap_left > 0 && !$all) {
                $has_extra = true;
                break;
            } elseif ($all) {
                $has_extra = true;
                break;
            }
        }

        return rest_ensure_response([
            'status' => 200,
            'has_extra' => $has_extra
        ]);
    }

    public function get_extras(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();
        $bmrequests = new BM_Request();

        $params = $request->get_query_params();
        $date   = $params['date'];
        $service_id = $params['service_id'];
        $all = isset($params['all']) && $params['all'] == 'true' ? true : false;
        $global_extras = $dbhandler->get_all_result('EXTRA', '*', array('is_global' => 1), 'results');
        $extra_rows = $dbhandler->get_all_result('EXTRA', '*', array('service_id' => $service_id), 'results');

        if (!empty($extra_rows) && !empty($global_extras)) {
            $total_extra_rows = array_merge($global_extras, $extra_rows);
        } elseif (empty($extra_rows) && !empty($global_extras)) {
            $total_extra_rows = $global_extras;
        } elseif (!empty($extra_rows) && empty($global_extras)) {
            $total_extra_rows = $extra_rows;
        } //end if

        if (isset($total_extra_rows) && !empty($total_extra_rows)) {
            foreach ($total_extra_rows as $key => $extra_service) {
                $cap_left = $bmrequests->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date($extra_service->id, $extra_service->extra_max_cap, 0, $date);
                $total_extra_rows[$key]->cap_left = $cap_left;
                $total_extra_rows[$key]->price = $bmrequests->bm_add_price_character(intval($extra_service->extra_price));
            }
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => isset($total_extra_rows) ? $total_extra_rows : []
        ]);
    }

    public function get_extra_details(WP_REST_Request $request)
    {
        $dbhandler  = new BM_DBhandler();
        $bmrequests = new BM_Request();

        $params = $request->get_query_params();
        $date   = $params['date'];
        $extra_id = $params['extra_id'];

        $extra_service = $dbhandler->get_row('EXTRA', $extra_id, 'id');
        if (!empty($extra_service)) {
            $cap_left = $bmrequests->bm_fetch_extra_service_cap_left_by_extra_service_id_and_date($extra_service->id, $extra_service->extra_max_cap, 0, $date);
            $extra_service->cap_left = $cap_left;
            $extra_service->price = $bmrequests->bm_fetch_price_in_global_settings_format($extra_service->extra_price, true);
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => !empty($extra_service) ? $extra_service : null,
            'message' => !empty($extra_service) ? '' : 'No extra service found for the given date.'
        ]);
    }

    public function addtocart(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $params = $request->get_json_params();

        $service_id = $params['service_id'];
        $date = $params['date'];
        $gift = $params['gift'] ?? 0;
        $data = array();

        if (!empty($service_id) && !empty($date)) {
            if (isset($service_id) && !empty($service_id)) {
                if ($bmrequests->bm_service_is_bookable($service_id, $date)) {
                    $dbhandler = new BM_DBhandler();
                    $total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id($service_id);
                    $stopsales        = $bmrequests->bm_fetch_service_stopsales_by_service_id($service_id, $date);
                    $timezone         = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
                    $now              = new DateTime('now', new DateTimeZone($timezone));

                    if (!empty($stopsales)) {
                        $stopSalesHours   = floor($stopsales);
                        $stopSalesMinutes = ($stopsales - $stopSalesHours) * 60;

                        if ($bmrequests->bm_has_dynamic_stopsales_for_date($service_id, $date)) {
                            $endDateTime = new DateTime($date . ' ' . $now->format('H:i'), new DateTimeZone($timezone));
                        } else {
                            $endDateTime = clone $now;
                        }

                        $endDateTime->add(new DateInterval("PT{$stopSalesHours}H{$stopSalesMinutes}M"));
                        $endDateTime = $endDateTime->format('Y-m-d H:i');
                    }

                    if ($total_time_slots == 1) {
                        $time_slot = $bmrequests->bm_fetch_single_time_slot_by_service_id($service_id, $date);

                        if ($time_slot !== '-1' && $time_slot !== '0') {
                            $params['time_slot'] = $time_slot;
                        }
                    }

                    $is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date($service_id, $date);
                    $params['id'] = $service_id;
                    $booking_fields   = ($gift) ? $this->gift_fetch_order_info($params) : $bmrequests->bm_fetch_order_info($params);
                    $booked_slots     = $bmrequests->bm_fetch_booked_slot_info_from_booking_data($booking_fields);
                    $from_slot        = !empty($booked_slots) && isset($booked_slots['from']) ? $booked_slots['from'] : '';
                    $startSlot        = new DateTime($date . ' ' . $from_slot, new DateTimeZone($timezone));
                    $startSlot        = $startSlot->format('Y-m-d H:i');

                    if ($gift || (!empty($booking_fields) && !empty($from_slot))) {
                        $booking_string = $bmrequests->bm_generate_unique_code('', 'FLEXIB', 15);
                        $dbhandler->bm_save_data_to_transient($booking_string, $booking_fields, 72);
                        $total_service_booked = isset($booking_fields['total_service_booking']) ? $booking_fields['total_service_booking'] : 0;
                        $bookable_extra       = $bmrequests->bm_is_selected_extra_service_bookable($booking_string);

                        $slot_info = $bmrequests->bm_fetch_slot_details($service_id, $from_slot, $date, $total_time_slots, $total_service_booked, $is_variable_slot, array('slot_min_cap', 'slot_capacity_left_after_booking'));

                        if ($gift) {
                            $message = 'Service added to cart successfully !!';
                            $data['booking_string'] = $booking_string;
                        } else {
                            if ((!empty($stopsales) && (strtotime($endDateTime) > strtotime($startSlot)))) {
                                $message = 'Can not book on the selected slot';
                            } elseif (isset($slot_info['slot_capacity_left_after_booking']) && ($slot_info['slot_capacity_left_after_booking'] < 0)) {
                                $message = 'Not enough capacity left, try booking another slot or service !!';
                            } elseif (($bookable_extra == false)) {
                                $message = 'One or more extra services does not have enough capacity, choose another !!';
                            } elseif (isset($slot_info['slot_capacity_left_after_booking']) && isset($slot_info['slot_min_cap']) && ($slot_info['slot_capacity_left_after_booking'] >= 0) && ($total_service_booked % $slot_info['slot_min_cap'] == 0)) {
                                $message = 'Service added to cart successfully !!';
                                $data['booking_string'] = $booking_string;
                            } else {
                                $message = 'Service is not bookable !!';
                            }
                        }
                    } else {
                        $message = 'Error fetching booking info !!';
                    }
                } else {
                    $message = 'Service is not bookable !!';
                }
            } //end if
        } //end if

        return rest_ensure_response([
            'status' => 200,
            'data'   => $data,
            'message' => isset($message) ? $message : ''
        ]);
    }

    public function  checkout(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler =  new BM_DBhandler();
        $params = $request->get_json_params();
        if (empty($params['billing_details'])) {
            $params = json_decode(file_get_contents('php://input'), true);
        }
        $data = array();
        $gift = $params['is_gift'];

        $booking_fields = $dbhandler->bm_fetch_data_from_transient($params['booking_data']);
        if (!empty($booking_fields)) {
            $id   = isset($booking_fields['service_id']) ? $booking_fields['service_id'] : 0;
            $date = isset($booking_fields['booking_date']) ? $booking_fields['booking_date'] : '';
            if ($gift || (!empty($id) && !empty($date))) {
                if ($gift || $bmrequests->bm_service_is_bookable($id, $date)) {
                    if (isset($params['other_data']['terms_conditions'])) {
                        unset($params['other_data']['terms_conditions']);
                    }

                    if (isset($params['billing_details'])) {
                        $checkout_string = $bmrequests->bm_generate_unique_code('', 'FLEXIC', 15);
                        $transient_data['billing'] = $params['billing_details'];

                        if (is_array($params['billing_details'])) {
                            foreach ($params['billing_details'] as $key => $value) {
                                $field_name = $dbhandler->get_value('FIELDS', 'field_name', $key, 'field_key');

                                if (!empty($field_name)) {
                                    $billing_details[$field_name] = $value;
                                }
                            }
                            if (!empty($billing_details)) {
                                $params['billing_details'] = $billing_details;
                            }
                        }

                        $checkout_data = $params;
                        unset($checkout_data['booking_data']);
                        $transient_data['checkout'] = $checkout_data;
                        $dbhandler->bm_save_data_to_transient($checkout_string, $transient_data, 72);
                    }

                    $gift_data = isset($params['gift_details']) ? $params['gift_details'] : array();

                    if (isset($params['other_data']['is_gift'])) {
                        $gift_data['is_gift'] = $params['other_data']['is_gift'];
                        unset($params['other_data']['is_gift']);
                    }

                    $gift_key = base64_encode($params['booking_data']);
                    $dbhandler->bm_save_data_to_transient($gift_key, $gift_data, 72);
                    if (defined('STRIPE_SECRET_KEY')) {
                        $stripe_payment_processor = new Booking_Management_Process_Payment(STRIPE_SECRET_KEY);

                        if ($stripe_payment_processor->isConnected()) {
                            if (isset($gift_data['is_gift']) && $gift_data['is_gift'] == 1) {
                                $data = $this->gift_check_payment_type_and_return_data($params['booking_data'], $checkout_string);
                            } else {
                                $data = $bmrequests->bm_check_payment_type_and_return_data($params['booking_data'], $checkout_string);
                            }

                            if (empty($data)) {
                                $resp = 'Error Fetching Payment Info !!';

                                $data['status'] = 'error';
                                $data['data']   = $resp;
                            }
                        } else {
                            $resp = 'Payment Gateway Server Error !!';

                            $data['status'] = 'error';
                            $data['data']   = $resp;
                        }
                    } else {
                        $resp = 'Payment Gateway Not Enabled !!';

                        $data['status'] = 'error';
                        $data['data']   = $resp;
                    }
                } else {
                    $resp = 'Service is Not Bookable !!';

                    $data['status'] = 'error';
                    $data['data']   = $resp;
                }
            } else {
                $resp = 'Error Fetching Booking Info !!';

                $data['status'] = 'error';
                $data['data']   = $resp;
            }
        } else {
            $resp = 'Error Fetching Booking Information !!';

            $data['status'] = 'error';
            $data['data']   = $resp;
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $data,
            'message' => ($data['status'] == 'error') ? $data['data'] : ''
        ]);
    }

    public function check_session(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $params = $request->get_json_params();
        $booking_key = $params['booking_key'];
        $status     = $bmrequests->bm_is_session_expired("flexi_current_payment_session_$booking_key") ? 1 : 0;

        return rest_ensure_response([
            'status' => 200,
            'data'   => ['is_expired' => $status]
        ]);
    }

    public function payment_process(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler  = new BM_DBhandler();
        $params = $request->get_json_params();

        $booking_key  = isset($params['booking']) ? $params['booking'] : '';
        $checkout_key = isset($params['checkout']) ? $params['checkout'] : '';
        $method_id    = isset($params['paymentMethod']) ? $params['paymentMethod'] : '';
        $gift = $params['gift'] ?? false;

        if (!empty($booking_key) && !empty($checkout_key) && !empty($method_id)) {
            if (defined('STRIPE_SECRET_KEY')) {
                $stripe_payment_processor = new Booking_Management_Process_Payment(STRIPE_SECRET_KEY);

                if ($stripe_payment_processor->isConnected()) {
                    $data['status'] = $bmrequests->bm_process_payment_data($booking_key, $checkout_key, $method_id, $gift);

                    if (in_array($data['status'], array('success', 'requires_capture', 'succeeded'), true)) {
                        $data['data'] = base64_decode($dbhandler->get_global_option_value('bm_client_secret' . $booking_key));
                    }
                } else {
                    $resp = 'Payment Gateway Server Error !!';

                    $data['data'] = $resp;
                }
            } else {
                $resp = 'Payment Gateway Not Enabled !!';

                $data['data'] = $resp;
            }
        } else {
            $resp = 'Could Not Capture Booking Info !!';

            $data['data'] = $resp;
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $data,
            'message' => ($data['status'] == 'error') ? 'Error Saving Payment Info !!' : ''
        ]);
    }

    public function payment_save(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler  = new BM_DBhandler();
        $params = $request->get_json_params();

        $booking_key  = isset($params['booking']) ? $params['booking'] : '';
        $checkout_key = isset($params['checkout']) ? $params['checkout'] : '';
        $gift = $params['gift'] ?? false;

        if (!empty($booking_key) && !empty($checkout_key)) {
            if (defined('STRIPE_SECRET_KEY')) {
                $payment_processor = new Booking_Management_Process_Payment(STRIPE_SECRET_KEY);

                if ($payment_processor->isConnected()) {
                    $process_status = $bmrequests->bm_save_payment_data($booking_key, $checkout_key, $gift);
                }
            }
        }
        $data['status'] = isset($process_status) ? $process_status : 'error';
        return rest_ensure_response([
            'status' => 200,
            'data'   => $data,
            'message' => ($data['status'] == 'error') ? 'Error Saving Payment Info !!' : ''
        ]);
    }

    public function countries(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $json_data = $bmrequests->bm_get_countries_and_states();
        $countries = $json_data['country'] ?? array();

        return rest_ensure_response([
            'status' => 200,
            'data'   => $countries
        ]);
    }

    public function states(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $params = $request->get_query_params();
        $country_code = $params['country_code'];
        $json_data = $bmrequests->bm_get_countries_and_states();
        $states    = $json_data['states'] ?? array();

        if (!empty($country_code) && !empty($states)) {
            $states = $states[$country_code] ?? array();
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $states
        ]);
    }

    public function couponlist(WP_REST_Request $request)
    {
        $params = $request->get_query_params();
        $booking_key = $params['booking_key'];

        $dbhandler        = new BM_DBhandler();
        $bmrequests       = new BM_Request();
        $timezone         = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
        $now              = new DateTime('now', new DateTimeZone($timezone));
        $current_date     = $now->format('Y-m-d');
        $data             = array('status' => false);
        $array_event_code = array();
        $event_array      = array();
        $global_inactive  = $dbhandler->get_global_option_value('bm_inactive_coupons', '0');
        if (isset($booking_key) && !empty($booking_key) && $global_inactive == 0) {

            $removed_cpn   = $dbhandler->bm_fetch_data_from_transient('coupon_removed_' . $booking_key);
            $coupons       = $dbhandler->get_all_result('COUPON', '*', 1, 'results');
            $discount_used = $dbhandler->bm_fetch_data_from_transient('coupon_applied_' . $booking_key);
            $discount_used = wp_list_pluck($discount_used, 'code');
            if (isset($coupons) && is_array($coupons) && count($coupons) > 0) {
                foreach ($coupons as $key => $coupon_data) {

                    if (isset($coupon_data->is_event_coupon) && $coupon_data->is_event_coupon == 1 && isset($coupon_data->start_date_val)) {
                        $event_dates       = isset($coupon_data->start_date_val) ? maybe_unserialize($coupon_data->start_date_val) : array();
                        $event_description = !empty($coupon_data->coupon_description) ? $coupon_data->coupon_description : esc_html__('Description not available', 'service-booking');
                        if (is_array($event_dates) && count($event_dates) > 0) {
                            foreach ($event_dates as $key => $event_data) {
                                if ($current_date == $event_data['date'] && !in_array($coupon_data->coupon_code, $discount_used)) {
                                    $event_description  =  !empty($event_data['desc']) ? $event_data['desc'] : $event_description;
                                    $date_display       = '∞';
                                    $timezone           = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
                                    $date_display       = new DateTime($event_data['date'], new DateTimeZone($timezone));
                                    $date_display       = $date_display->format('d F Y');
                                    $image_display      = $bmrequests->bm_fetch_cpn_image_url_or_guid($coupon_data->id, 'COUPON', 'url') ? $bmrequests->bm_fetch_cpn_image_url_or_guid($coupon_data->id, 'COUPON', 'url')  : '';
                                    $array_event_code[] = array(
                                        'code'        => $coupon_data->coupon_code,
                                        'description' => $event_description,
                                        'date'        => $date_display,
                                        'image'       => $image_display,
                                        'type'        => $coupon_data->discount_type,
                                        'amount'      => $coupon_data->discount_amount,
                                    );
                                    $event_array[]      = $coupon_data->coupon_code;
                                }
                            }
                        }
                    }
                }
                if (is_array($removed_cpn) && count($removed_cpn) > 0) {
                    foreach ($removed_cpn as $key => $cpn) {
                        if (!in_array($cpn, $event_array) &&  !in_array($cpn, $discount_used)) {
                            $coupon_data = $dbhandler->get_row('COUPON', $cpn, 'coupon_code');

                            if (isset($coupon_data->coupon_code) && $coupon_data->coupon_code != null) {
                                $date_display      = '∞';
                                $event_description = !empty($coupon_data->coupon_description) ? $coupon_data->coupon_description : 'Description not available';
                                if (isset($coupon_data->expiry_date) &&  $coupon_data->expiry_date != null) {
                                    $timezone     = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
                                    $date_display = new DateTime($coupon_data->expiry_date, new DateTimeZone($timezone));
                                    $date_display = $date_display->format('d F Y');
                                }
                                if (isset($coupon_data->is_event_coupon) && $coupon_data->is_event_coupon == 1 && isset($coupon_data->start_date_val)) {
                                    $event_dates = isset($coupon_data->start_date_val) ? maybe_unserialize($coupon_data->start_date_val) : array();
                                    if (is_array($event_dates) && count($event_dates) > 0) {
                                        foreach ($event_dates as $key => $event_data) {
                                            if ($current_date == $event_data['date'] && !in_array($coupon_data->coupon_code, $discount_used)) {
                                                $event_description =  !empty($event_data['desc']) ? $event_data['desc'] : $event_description;
                                                $timezone          = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
                                                $date_display      = new DateTime($event_data['date'], new DateTimeZone($timezone));
                                                $date_display      = $date_display->format('d F Y');
                                            }
                                        }
                                    }
                                }
                                $image_display      = $bmrequests->bm_fetch_cpn_image_url_or_guid($coupon_data->id, 'COUPON', 'url') ? $bmrequests->bm_fetch_cpn_image_url_or_guid($coupon_data->id, 'COUPON', 'url')  : '';
                                $array_event_code[] = array(
                                    'code'        => $coupon_data->coupon_code,
                                    'description' => $event_description,
                                    'date'        => $date_display,
                                    'image'       => $image_display,
                                    'type'        => $coupon_data->discount_type,
                                    'amount'      => $coupon_data->discount_amount,
                                );
                            }
                        }
                    }
                }
            }
            if (!empty($array_event_code)) {
                $data['status'] = true;
                $data['code']   = $array_event_code;
            }
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $data
        ]);
    }

    public function auto_apply_coupon(WP_REST_Request $request)
    {
        $dbhandler       = new BM_DBhandler();
        $bmrequests      = new BM_Request();
        $timezone        = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
        $now             = new DateTime('now', new DateTimeZone($timezone));
        $data            = array('status' => false);
        $params = $request->get_query_params();
        $booking_key     = $params['booking_key'];
        $email           = $params['email'] ?? '';
        $auto_apply_list = array();

        if (isset($booking_key) && !empty($booking_key) && $booking_key != false) {

            $pre_applied_list = $dbhandler->bm_fetch_data_from_transient('coupon_applied_' . $booking_key);
            $coupon_used      = $dbhandler->bm_fetch_data_from_transient('coupon_used_' . $booking_key);

            if ((!empty($pre_applied_list) && is_array($pre_applied_list) && count($pre_applied_list) > 0) || $coupon_used == 1) {

                $data['status']    = true;
                if ($pre_applied_list) {
                    foreach ($pre_applied_list as $key => $value) {
                        if ($value['coupon_discount']) {
                            $pre_applied_list[$key]['coupon_discount'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($pre_applied_list[$key]['coupon_discount'], true));
                        }
                    }
                }
                $data['code_data'] = $pre_applied_list;
                if ($dbhandler->get_global_option_value('discount_' . $booking_key) == 1) {
                    $order_data    = $dbhandler->bm_fetch_data_from_transient('discounted_' . $booking_key);
                    $data['order'] = $order_data;
                } else {
                    $data['order'] = 'else';
                    $order_data    = $dbhandler->bm_fetch_data_from_transient($booking_key);
                }
                $data['discount'] = isset($order_data['discount']) ? round($order_data['discount'], 2) : '';
                $data['amount']   = isset($order_data['total_cost']) ? round($order_data['total_cost'], 2)  : '';
                $data['subtotal'] = $order_data['subtotal'];
            } else {
                $coupons    = $dbhandler->get_all_result('COUPON', 'coupon_code', 1, 'results');
                $auto_limit = $dbhandler->get_global_option_value('bm_auto_apply_limit');
                $auto_limit = $auto_limit > 0 ? floor($auto_limit) : 2;
                $count      = 0;
                $obj = new Booking_Management_Public($this->plugin_name, $this->version);
                if (isset($coupons) && is_array($coupons) && count($coupons) > 0) {
                    foreach ($coupons as $key => $coupon_code) {
                        $cpn         = $coupon_code->coupon_code;
                        $auto_enable = $obj->bm_check_auto_apply_enable($cpn);
                        if ($auto_enable == 1) {
                            $response = $obj->bm_check_coupon_validity($booking_key, $cpn, $email);

                            if ($response['status'] == true && $auto_limit > $count) {
                                if ($response['coupon_discount']) {
                                    $response['coupon_discount'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($response['coupon_discount'], true));
                                }
                                $auto_apply_list[$count] = $response;
                                ++$count;
                            }
                        }
                        if ($count == $auto_limit) {
                            break;
                        }
                    }
                    if (!empty($auto_apply_list)) {
                        $data['status']    = true;
                        $data['code_data'] = $auto_apply_list;
                        if ($dbhandler->get_global_option_value('discount_' . $booking_key) == 1) {
                            $order_data = $dbhandler->bm_fetch_data_from_transient('discounted_' . $booking_key);
                        } else {
                            $order_data = $dbhandler->bm_fetch_data_from_transient($booking_key);
                        }
                        $data['discount'] = round($order_data['discount'], 2);
                        $data['amount']   = round($order_data['total_cost'], 2);
                        $data['subtotal'] = $auto_apply_list[0]['original_data']['subtotal'];
                    }
                }
            }

            $data['subtotal'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($data['subtotal'], true));
            $data['discount'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($data['discount'], true));
            $data['total']   = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($data['amount'], true));
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $data
        ]);
    }

    public function apply_coupon(WP_REST_Request $request)
    {
        $bmrequests = new Booking_Management_Public($this->plugin_name, $this->version);
        $bmrequest = new BM_Request();
        $params = $request->get_json_params();
        $booking_key  = $params['booking_key'] ?? '';
        $coupon_code  = $params['coupon_code'];
        $email        = $params['email'] ?? '';
        $response     = $bmrequests->bm_check_coupon_validity($booking_key, $coupon_code, $email);
        if ($response['status'] == true && $response['original_data']) {
            $response['original_data']['subtotal'] = esc_html($bmrequest->bm_fetch_price_in_global_settings_format($response['original_data']['subtotal'], true));
            $response['coupon_discount'] = esc_html($bmrequest->bm_fetch_price_in_global_settings_format($response['checkout_discount'], true));
            $response['total'] = esc_html($bmrequest->bm_fetch_price_in_global_settings_format($response['amount'], true));
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $response
        ]);
    }

    public function coupon_removal(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $params = $request->get_json_params();
        $booking_key  = $params['booking_key'];
        $coupon_code        = $params['coupon_code'];
        $response['status'] = false;
        $array_event_code   = array();
        $render_suggestion  = '';
        $removed_cpn        = array();
        $timezone           = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');

        if (isset($booking_key) && !empty($booking_key)) {
            if ($dbhandler->get_global_option_value('discount_' . $booking_key) == 1) {
                $order_data = $dbhandler->bm_fetch_data_from_transient('discounted_' . $booking_key);
            }
            $removed_cpn = $dbhandler->bm_fetch_data_from_transient('coupon_removed_' . $booking_key);
            if (!is_array($removed_cpn)) {
                $removed_cpn = array();
            }

            if (isset($order_data) && !empty($order_data)) {
                $applied_coupons      = $dbhandler->bm_fetch_data_from_transient('coupon_applied_' . $booking_key);
                $applied_coupons      = !empty($applied_coupons) ? $applied_coupons : array();
                $response['amount']   = $order_data['total_cost'];
                $response['discount'] = $order_data['discount'];
                if (is_array($applied_coupons) && count($applied_coupons) > 0) {
                    foreach ($applied_coupons as $key => $value) {
                        if (isset($value['code']) && $value['code'] && $value['code'] == $coupon_code) {
                            $render_suggestion = $value['code'];
                            $removed_cpn[]     = $value['code'];
                            $removed_cpn       = array_unique($removed_cpn);
                            $dbhandler->bm_save_data_to_transient('coupon_removed_' . $booking_key, $removed_cpn, 72);

                            $response['amount'] += $value['coupon_discount'];
                            if (is_array($response) && isset($response['discount'])) {
                                $response['discount'] -= $value['coupon_discount'];
                                $response['discount']  = round($response['discount'], 2);
                            }
                            if ($response['discount'] < 0) {
                                $response['discount'] = 0;
                            }
                            if ($response['amount'] > $order_data['subtotal']) {
                                $response['amount'] = round($order_data['subtotal'], 2);
                            }
                            unset($applied_coupons[$key]);
                        }
                    }
                    if (isset($render_suggestion) && !empty($render_suggestion) && $render_suggestion != '') {

                        $coupon_data       = $dbhandler->get_row('COUPON', $render_suggestion, 'coupon_code');
                        $event_description = !empty($coupon_data->coupon_description) ? $coupon_data->coupon_description : esc_html__('Description not available', 'service-booking');
                        $date_display      = '∞';
                        if (isset($coupon_data->expiry_date) &&  $coupon_data->expiry_date != null) {
                            $date_display = new DateTime($coupon_data->expiry_date, new DateTimeZone($timezone));
                            $date_display = $date_display->format('d F Y');
                        }
                        if (isset($coupon_data->is_event_coupon) && $coupon_data->is_event_coupon == 1 && isset($coupon_data->start_date_val)) {
                            $event_dates  = isset($coupon_data->start_date_val) ? maybe_unserialize($coupon_data->start_date_val) : array();
                            $now          = new DateTime('now', new DateTimeZone($timezone));
                            $current_date = $now->format('Y-m-d');

                            if (is_array($event_dates) && count($event_dates) > 0) {
                                foreach ($event_dates as $key => $event_data) {
                                    if ($current_date == $event_data['date']) {
                                        $event_description =  !empty($event_data['desc']) ? $event_data['desc'] : $event_description;
                                        $date_display      = new DateTime($event_data['date'], new DateTimeZone($timezone));
                                        $date_display      = $date_display->format('d F Y');
                                    }
                                }
                            }
                        }
                        $image_display    = $bmrequests->bm_fetch_cpn_image_url_or_guid($coupon_data->id, 'COUPON', 'url') ? $bmrequests->bm_fetch_cpn_image_url_or_guid($coupon_data->id, 'COUPON', 'url')  : '';
                        $array_event_code = array(
                            'code'        => $coupon_data->coupon_code,
                            'description' => $event_description,
                            'date'        => $date_display,
                            'image'       => $image_display,
                            'type'        => $coupon_data->discount_type,
                            'amount'      => $coupon_data->discount_amount,
                        );

                        $response['code'] = $array_event_code;
                    }

                    $applied_coupons = array_values($applied_coupons);

                    $dbhandler->bm_save_data_to_transient('coupon_applied_' . $booking_key, $applied_coupons, 72);
                    $dbhandler->bm_save_data_to_transient('coupon_final_amount_' . $booking_key, $response['amount'], 72);

                    $response['status']       = true;
                    $response['value']        = $applied_coupons;
                    $order_data['total_cost'] = round($response['amount'], 2);
                    if ($response['discount'] < 0) {
                        $response['discount'] = 0;
                    }
                    $order_data['discount'] = round($response['discount'], 2);
                    if (isset($booking_key) && !empty($booking_key)) {
                        if ($dbhandler->get_global_option_value('discount_' . $booking_key) == 1) {
                            $dbhandler->bm_save_data_to_transient('discounted_' . $booking_key, $order_data, 72);
                        }
                    }

                    $response['coupon_discount'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($response['discount'], true));
                    $response['total'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($response['amount'], true));
                }
            }
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $response
        ]);
    }

    public function check_voucher_validity(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        $voucher = $params['voucher'];

        $redeemVoucher = new FlexiVoucherRedeem($voucher);
        try {
            $validate = $redeemVoucher->validateVoucher();
        } catch (Exception $e) {
            $message = 'Something went wrong.';
            $status  = false;
        }
        if (isset($validate['error'])) {
            $message = $validate['error'];
            $status  = false;
        }

        if (isset($validate['success'])) {
            $validate['status'] = true;
            $message = $validate['success'];
            $status  = true;
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $validate ?? ['status' => $status ?? false],
            'message' => $message ?? ''
        ]);
    }

    public function priceformat(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $params = $request->get_query_params($request);
        $service_id = $params['service_id'] ?? '';
        $capacity = $params['capacity'] ?? 0;
        $date = $params['date'] ?? '';
        $extra_id = $params['extra_id'] ?? '';
        $extra_capacity = $params['extra_capacity'] ?? 0;
        $service_price = ($service_id) ? $bmrequests->bm_fetch_service_price_by_service_id_and_date($service_id, $date) : 0;

        $total = $service_price * $capacity;
        $service_total = $total;

        $extra_service_ids  = isset($extra_id) ? explode(',', $extra_id) : array();
        $extra_slots_booked = isset($extra_capacity) ? explode(',', $extra_capacity) : array();
        if ($service_id && $extra_id) {
            foreach (explode(',', $extra_id) as $key => $id) {
                $extra_service = $dbhandler->get_row('EXTRA', $id);
                if (!empty($extra_service)) {
                    if (isset($extra_service->is_global) && $extra_service->is_global == 1) {
                        // This is a global extra
                    } else {
                        // Check if extra is associated with the service
                        $extra_for_service = $dbhandler->get_all_result('EXTRA', '*', array('id' => $id, 'service_id' => $service_id), 'var');
                        if (empty($extra_for_service)) {
                            // Extra does not exist for this service, unset it
                            unset($extra_service_ids[$key]);
                        }
                    }
                }
            }
        }

        $extra_total = [];
        if (!empty($extra_service_ids) && !empty($extra_slots_booked)) {
            foreach ($extra_service_ids as $key => $extra_id) {
                $extra_service = $dbhandler->get_row('EXTRA', $extra_id);
                if (!empty($extra_service)) {
                    $extra_price = $bmrequests->bm_fetch_total_price($extra_service->extra_price, $extra_slots_booked[$key]);
                    $extra_total[$key] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($extra_price, true));
                    $total += $extra_price;
                }
            }
        }
        $price = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($total, true));


        return rest_ensure_response([
            'status' => 200,
            'data'   => ['service_id' => $service_id, 'service_capacity' => $capacity, 'extra_id' => implode(',', $extra_service_ids), 'extra_capacity' => (count($extra_service_ids) ? implode(',', $extra_slots_booked) : 0), 'total' => $total, 'total_formated' => $price, 'service_total' => esc_html($bmrequests->bm_fetch_price_in_global_settings_format($service_total, true)), 'extra_total' => $extra_total],
            'message' => "Formated Price"
        ]);
    }

    public function get_voucher_detail(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        $code = $params['voucher'];
        $date = $params['date'] ?? date('Y-m-d');

        $message = '';
        $data = array('status' => false);
        if (!$code) {
            $message = 'Voucher is invalid.';
        }

        if (!$date) {
            $message = 'Date is invalid.';
        }

        $redeemVoucher = new FlexiVoucherRedeem($code);

        try {
            $validate = $redeemVoucher->validateVoucher();
        } catch (Exception $e) {
            $message = 'Something went wrong.';
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }

        if (isset($validate['error'])) {
            $message = $validate['error'];
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }

        try {
            $bookingInfo = $redeemVoucher->getBookingInfo();
            $bookingInfo   = $bookingInfo[0];
            $bmrequests    = new BM_Request();
            $dbhandler     = new BM_DBhandler();
            $service_image = $bmrequests->bm_fetch_image_url_or_guid($bookingInfo['service_id'], 'SERVICE', 'url');
            $products      = $bmrequests->bm_fetch_product_info_order_details_page($bookingInfo['id']);
            $service_details = $dbhandler->get_row('SERVICE', $bookingInfo['service_id']);

            $slots = $redeemVoucher->fetchAvailableSlots($date);

            $expiry = $redeemVoucher->getVoucherExpiry()['expiry'];
            $data['expiry_date'] = $expiry;
            $data['expiried_on'] = esc_html($bmrequests->bm_fetch_datetime_difference($expiry)) . esc_html__('s to expire', 'service-booking');
            $data['service_image'] = $service_image;
            $data['service_description'] = !empty($service_details->service_desc) ? wp_kses_post($service_details->service_desc) : '';
            foreach ($products['products'] as $key => $product) {
                $products['products'][$key]['base_price'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($product['base_price'], true));
                $products['products'][$key]['total'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($product['total'], true));
            }
            $data['products'] = $products['products'];
            $data['subtotal'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($products['subtotal'], true));
            $data['discount'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($products['discount'], true));
            $data['total'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($products['total'], true));
            $data['date'] = $date;
            $data['slots'] = $slots['slots'] ?? array();
            $data['recepient_data'] = maybe_unserialize($redeemVoucher->getVoucherInfo()[0]['recipient_data']);
            $data['status'] = true;
        } catch (Exception $e) {
            $message = 'Something went wrong.';
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }

        if (isset($slots['error'])) {
            $message = $slots['error'];
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => $data,
            'message' => $message ?? ''
        ]);
    }

    public function redeem_voucher(WP_REST_Request $request)
    {
        $params = $request->get_json_params();
        $params      = apply_filters('bm_flexibooking_set_voucher_timeslot_data', $params);
        $code      = trim(stripslashes(sanitize_text_field($params['voucher'])));
        $date      = trim(sanitize_text_field($params['date']));
        $slot      = trim(sanitize_text_field($params['slot']));
        $recipient = $params['recipient'];
        $message = '';
        $data = array('status' => false);

        $redeemVoucher = new FlexiVoucherRedeem($code);
        try {
            $validate = $redeemVoucher->validateVoucher();
        } catch (Exception $e) {
            $message = 'Something went wrong.';
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }

        if (isset($validate['error'])) {
            $message = $validate['error'];
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }

        try {
            $confirmRedemption = $redeemVoucher->confirmRedemption($date, $slot);
        } catch (Exception $e) {
            $message = 'Something went wrong.';
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }
        if (isset($confirmRedemption['error'])) {
            $message = $confirmRedemption['error'];
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }

        $redeemVoucher->updateVoucherInfo(
            array(
                'recipient_data' => maybe_serialize($recipient),
                'updated_at'     => (new BM_Request())->bm_fetch_current_wordpress_datetime_stamp(),
            )
        );

        do_action('flexibooking_set_process_voucher_redeem', $redeemVoucher->getBookingInfo()[0]['id']);
        $message = $confirmRedemption['success'] ?? '';
        $confirmRedemption['status'] = 'success';
        return rest_ensure_response([
            'status' => 200,
            'data'   => $confirmRedemption,
            'message' => $message
        ]);
    }

    public function check_for_refund_for_failed_payment(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $params = $request->get_json_params();
        $booking_key = $params['booking_key'];
        $checkout_key = $params['checkout_key'];
        $status = 'error';
        $resp       = '';

        $is_cancelled = $bmrequests->bm_cancel_payment_intent_for_failed_payment($booking_key, $checkout_key);

        if ($is_cancelled) {
            $status = 'cancelled';
        }

        if ($status == 'cancelled') {
            $resp = __('Could not save transaction data, any amount charged is initiated for refund and may take 5–10 business days for funds to settle !!', 'service-booking');
        } else {
            $resp = __('Transaction failed !!', 'service-booking');
        }

        $dbhandler->bm_save_data_to_transient('bm_latest_payment_status_message' . $booking_key, $resp, 0.5);
        return rest_ensure_response([
            'status' => 200,
            'data'   => ['status' => $status],
            'message' => $resp
        ]);
    }

    public function thankyou(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $params = $request->get_json_params();
        $booking_key = $params['booking_key'];
        $booking_id     = $dbhandler->get_value('BOOKING', 'id', $booking_key, 'booking_key');
        $transaction    = $dbhandler->get_row('TRANSACTIONS', $booking_id, 'booking_id');
        $products = [];

        if (!empty($transaction)) {
            $payment_ref_id               = isset($transaction->id) ? $transaction->id : 0;
            $booking_type                 = $dbhandler->get_value('BOOKING', 'booking_type', $booking_id, 'id');
            $serviceDate                  = $dbhandler->get_value('BOOKING', 'booking_date', $booking_id, 'id');
            $coupons                      = $dbhandler->get_value('BOOKING', 'coupons', $booking_id, 'id');
            $productDetails               = $bmrequests->bm_fetch_product_info_order_details_page($booking_id);
            $customer_id                  = isset($transaction->customer_id) ? $transaction->customer_id : 0;
            $customer                     = $dbhandler->get_row('CUSTOMERS', $customer_id);
            $customer_billing             = !empty($customer) && isset($customer->billing_details) ? maybe_unserialize($customer->billing_details) : '';
            $customer_shipping            = !empty($customer) && isset($customer->shipping_details) ? maybe_unserialize($customer->shipping_details) : '';
            $price_module_data            = $dbhandler->get_value('BOOKING', 'price_module_data', $booking_id, 'id');
            $price_module_data            = !empty($price_module_data) ? maybe_unserialize($price_module_data) : array();
            $productDetails['subtotal'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($productDetails['subtotal'], true));
            $productDetails['total'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($productDetails['total'], true));

            foreach ($productDetails['products'] as $key => $product) {
                $products[] = [
                    'product_heading' => ($key == 0) ? 'Main Product' : 'Extra Product',
                    'name' => $product['name'],
                    'price' => isset($product['base_price']) ? esc_html($bmrequests->bm_fetch_price_in_global_settings_format($product['base_price'], true)) : 'N/A',
                    'quantity' => isset($product['quantity']) ? esc_html($product['quantity']) : 'N/A',
                    'total' => isset($product['total']) ? esc_html($bmrequests->bm_fetch_price_in_global_settings_format($product['total'], true)) : 'N/A'
                ];
            }


            if (!empty($price_module_data) && is_array($price_module_data)) {
                $total_discounted_infants  = isset($price_module_data['infant']['total']) ? intval($price_module_data['infant']['total']) : 0;
                $total_discounted_children = isset($price_module_data['children']['total']) ? intval($price_module_data['children']['total']) : 0;
                $total_discounted_adults   = isset($price_module_data['adult']['total']) ? intval($price_module_data['adult']['total']) : 0;
                $total_discounted_seniors  = isset($price_module_data['senior']['total']) ? intval($price_module_data['senior']['total']) : 0;

                $infants_age_from  = isset($price_module_data['infant']['age']['from']) ? intval($price_module_data['infant']['age']['from']) : 0;
                $children_age_from = isset($price_module_data['children']['age']['from']) ? intval($price_module_data['children']['age']['from']) : 0;
                $adults_age_from   = isset($price_module_data['adult']['age']['from']) ? intval($price_module_data['adult']['age']['from']) : 0;
                $seniors_age_from  = isset($price_module_data['senior']['age']['from']) ? intval($price_module_data['senior']['age']['from']) : 0;

                $infants_age_to  = isset($price_module_data['infant']['age']['to']) ? intval($price_module_data['infant']['age']['to']) : 0;
                $children_age_to = isset($price_module_data['children']['age']['to']) ? intval($price_module_data['children']['age']['to']) : 0;
                $adults_age_to   = isset($price_module_data['adult']['age']['to']) ? intval($price_module_data['adult']['age']['to']) : 0;
                $seniors_age_to  = isset($price_module_data['senior']['age']['to']) ? intval($price_module_data['senior']['age']['to']) : 0;

                $infants_total_discount  = isset($price_module_data['infant']['total_discount']) ? floatval($price_module_data['infant']['total_discount']) : 0;
                $children_total_discount = isset($price_module_data['children']['total_discount']) ? floatval($price_module_data['children']['total_discount']) : 0;
                $adults_total_discount   = isset($price_module_data['adult']['total_discount']) ? floatval($price_module_data['adult']['total_discount']) : 0;
                $seniors_total_discount  = isset($price_module_data['senior']['total_discount']) ? floatval($price_module_data['senior']['total_discount']) : 0;

                $infants_total  = isset($price_module_data['infant']['total_cost']) ? floatval($price_module_data['infant']['total_cost']) : 0;
                $children_total = isset($price_module_data['children']['total_cost']) ? floatval($price_module_data['children']['total_cost']) : 0;
                $adults_total   = isset($price_module_data['adult']['total_cost']) ? floatval($price_module_data['adult']['total_cost']) : 0;
                $seniors_total  = isset($price_module_data['senior']['total_cost']) ? floatval($price_module_data['senior']['total_cost']) : 0;

                $infants_discount_type  = isset($price_module_data['infant']['discount_type']) ? $price_module_data['infant']['discount_type'] : 'positive';
                $children_discount_type = isset($price_module_data['children']['discount_type']) ? $price_module_data['children']['discount_type'] : 'positive';
                $adults_discount_type   = isset($price_module_data['adult']['discount_type']) ? $price_module_data['adult']['discount_type'] : 'positive';
                $seniors_discount_type  = isset($price_module_data['senior']['discount_type']) ? $price_module_data['senior']['discount_type'] : 'positive';

                $group_discount          = isset($price_module_data['group_discount']) ? floatval($price_module_data['group_discount']) : 0;
                $discount_type           = isset($price_module_data['discount_type']) ? $price_module_data['discount_type'] : 'positive';
                $negative_group_discount = $dbhandler->get_global_option_value('negative_group_discount_' . $booking_key, 0);
            }


            if (!empty($coupons)) {
                if ($group_discount > 0) {
                    $coupon_discount = isset($productDetails['discount']) ? ($productDetails['discount'] - ($infants_total_discount + $children_total_discount + $group_discount)) : 0;
                } else {
                    $coupon_discount = isset($productDetails['discount']) ? ($productDetails['discount'] - ($infants_total_discount + $children_total_discount + $adults_total_discount + $seniors_total_discount)) : 0;
                }
            }
        }

        $payment_txn_id = $booking_key;
        $payment_status = 'failed';
        $statusMsg = 'Your Payment has been Failed!';

        $data['customer_billing'] = $customer_billing;
        $data['customer_shipping'] = $customer_shipping;

        if (!empty($payment_txn_id) && !empty($transaction) && !empty($customer)) {
            $payment_status = 'success';
            $statusMsg      = 'Your Payment has been Successful!';
        }

        $data['customer_billing'] = $customer_billing;
        $data['customer_shipping'] = $customer_shipping;
        $data['service_date'] = $serviceDate ?? '';
        $data['payment_status'] = $payment_status;
        $data['status_message'] = $statusMsg;
        $data['payment_ref_id'] = $payment_txn_id ?? '';
        $data['booking_type'] = $booking_type ?? '';
        $data['coupon_discount'] = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($coupon_discount ?? 0, true));
        $data['product_details'] = $productDetails ?? [];
        $data['products'] = $products;
        $data['coupon'] = $coupons ?? 'N/A';


        return rest_ensure_response([
            'status' => 200,
            'data'   => $data
        ]);
    }

    public function redeem_thankyou(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $params = $request->get_json_params();
        $redeemed = $params['redeem_code'];
        $redeemVoucher = new FlexiVoucherRedeem($redeemed);
        $validateVoucher = $redeemVoucher->validateIfRedeemed();

        $data = array('status' => false);
        $message = '';

        if (isset($validateVoucher['error']) || !$validateVoucher) {
            $message = esc_html__(sprintf('%s', $validateVoucher['error'] ? $validateVoucher['error'] : 'Voucher is not yet redeemed'), 'service-booking');
            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        } else {
            $voucherInfo = $redeemVoucher->getVoucherInfo();

            if (isset($voucherInfo['error'])) {
                $message = esc_html($voucherInfo['error']);
                return rest_ensure_response([
                    'status' => 200,
                    'data'   => $data,
                    'message' => $message
                ]);
            }

            $bookingInfo = $redeemVoucher->getBookingInfo();

            if (isset($bookingInfo['error'])) {
                $message = esc_html($bookingInfo['error']);
                return rest_ensure_response([
                    'status' => 200,
                    'data'   => $data,
                    'message' => $message
                ]);
            }

            $bookingInfo      = $bookingInfo[0];
            $bookedSlots      = maybe_unserialize($bookingInfo['booking_slots']);
            $bmrequests       = new BM_Request();
            $booking_id       = $bookingInfo['id'] ?? 0;
            $productDetails         = $bmrequests->bm_fetch_product_info_order_details_page($booking_id);
            $recipient        = maybe_unserialize($voucherInfo[0]['recipient_data']);
            $customer_billing = $bmrequests->get_customer_info_for_order($booking_id);

            $products = [];
            foreach ($productDetails['products'] as $key => $product) {
                $products[] = [
                    'product_heading' => ($key == 0) ? 'Main Product' : 'Extra Product',
                    'name' => $product['name'],
                    'price' => isset($product['base_price']) ? esc_html($bmrequests->bm_fetch_price_in_global_settings_format($product['base_price'], true)) : 'N/A',
                    'quantity' => isset($product['quantity']) ? esc_html($product['quantity']) : 'N/A',
                    'total' => isset($product['total']) ? esc_html($bmrequests->bm_fetch_price_in_global_settings_format($product['total'], true)) : 'N/A'
                ];
            }

            $data['status']           = true;
            $data['customer_billing'] = $customer_billing;
            $data['products']         = $products;
            $data['subtotal']         = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($productDetails['subtotal'], true));
            $data['discount']         = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($productDetails['discount'], true));
            $data['total']            = esc_html($bmrequests->bm_fetch_price_in_global_settings_format($productDetails['total'], true));
            $data['booking_slots']    = $bookedSlots;
            $data['recipient']        = $recipient;
            $data['service_date']      = $bookingInfo['booking_date'] ?? '';

            return rest_ensure_response([
                'status' => 200,
                'data'   => $data,
                'message' => $message
            ]);
        }
    }

    public function sendemail(WP_REST_Request $request)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $bm_mail          = new BM_Email();
        $params = $request->get_json_params();
        $booking_key = $params['booking_key'];
        $email = $params['email'];
        $status = false;

        $order_id     = $dbhandler->get_value('BOOKING', 'id', $booking_key, 'booking_key');
        $order = $dbhandler->get_row('BOOKING', $order_id, 'id');
        $mail_type        = 'new_order';
        $language         = $dbhandler->get_global_option_value('bm_flexi_current_language', 'en');

        if (!empty($order)) {
            $source = isset($order->is_frontend_booking) ? $order->is_frontend_booking : -1;

            if ($source == 1) {
                $template_id = $dbhandler->get_all_result(
                    'EMAIL_TMPL',
                    'id',
                    array(
                        'status' => 1,
                        'type'   => 0,
                    ),
                    'var'
                );
            } elseif ($source == 0) {
                $template_id = $dbhandler->get_all_result(
                    'EMAIL_TMPL',
                    'id',
                    array(
                        'status' => 1,
                        'type'   => 1,
                    ),
                    'var'
                );
            }

            $subject               = esc_html('New order');
            $message               = '<p>' . esc_html('New order received') . '</p>';

            if ($language == 'it') {
                $subject = esc_html('Nuovo ordine');
                $message = '<p>' . esc_html('Nuovo ordine ricevuto') . '</p>';
            }

            if (!empty($template_id)) {
                $template_subject = $bm_mail->bm_get_template_email_subject($template_id);
                $template_body    = $bm_mail->bm_get_template_email_content($template_id, (int) $order_id);
            } else {
                $template_subject = $subject;
                $template_body    = $message;
            }

            $mail_data = array(
                'module_type' => 'BOOKING',
                'module_id'   => $order_id,
                'mail_type'   => $mail_type,
                'template_id' => $template_id,
                'process_id'  => 0,
                'mail_to'     => $email,
                'mail_sub'    => wp_kses_post($template_subject),
                'mail_body'   => wp_kses_post(stripslashes($template_body)),
                'mail_lang'   => $language,
                'status'      => 1,
            );

            ob_start();
            include plugin_dir_path(dirname(__FILE__)) . 'admin/partials/booking-management-customer-email-layout.php';
            $template_body = ob_get_contents();
            ob_end_clean();

            $mail_to_customer = $bm_mail->bm_send_invoice_to_email($template_subject, $template_body, (int) $order_id, $email);
            if ($mail_to_customer) {
                $mail_data['created_at'] = $bmrequests->bm_fetch_current_wordpress_datetime_stamp();
                $dbhandler->insert_row('EMAILS', $mail_data);
                $status = true;
            }
        }

        return rest_ensure_response([
            'status' => 200,
            'data'   => ['status' => $status]
        ]);
    }


    protected function service_details($service, $date = '', $all = false)
    {
        $dbhandler  = new BM_DBhandler();
        $bmrequests = new BM_Request();

        $service_id = !empty($service) && isset($service->id) ? esc_attr($service->id) : 0;
        $modified_data = [];
        if (!$all && !$bmrequests->bm_service_is_bookable($service->id, $date)) {
            return $modified_data;
        }
        if (!$all && !empty($date) && empty($bmrequests->bm_fetch_service_time_slot_array_by_service_id(
            array(
                'id'   => $service_id,
                'date' => $date,
            )
        ))) {
            return $modified_data;
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

        $svc_total_cap_left = NULL;
        if (!$all && !empty($date)) {
            $svc_total_cap_left = $dbhandler->get_all_result(
                'SLOTCOUNT',
                'svc_total_cap_left',
                array(
                    'service_id'   => esc_attr($service_id),
                    'booking_date' => esc_html($date),
                    'is_active'    => 1,
                ),
                'var',
                0,
                1,
                'id',
                'DESC'
            );
        }

        $svc_img            = esc_url($bmrequests->bm_fetch_image_url_or_guid($service_id, 'SERVICE', 'url'));
        $svc_name           = !empty($service) && isset($service->service_name) && !empty($service->service_name) ? mb_strimwidth(esc_html($service->service_name), 0, 20, '...') : 'N/A';
        $category_name      = $bmrequests->bm_fetch_category_name_by_service_id($service_id);
        $svc_desc           = !empty($service) && isset($service->service_desc) && !empty($service->service_desc) ? wp_strip_all_tags((wp_kses_post(stripslashes(($service->service_desc))))) : 'N/A';
        $svc_long_desc      = !empty($service) && isset($service->service_desc) && !empty($service->service_desc) ? do_shortcode(wp_kses_post(stripslashes(($service->service_desc)))) : 'N/A';
        $svc_short_desc     = !empty($service) && isset($service->service_short_desc) && !empty($service->service_short_desc) ? wp_strip_all_tags((wp_kses_post(stripslashes(($service->service_short_desc))))) : 'N/A';
        $svc_duration       = !empty($service) && isset($service->service_duration) && !empty($service->service_duration) ? esc_html($bmrequests->bm_fetch_float_to_time_string($service->service_duration)) : 'N/A';
        $svc_default_price  = !empty($service) && isset($service->default_price) && !empty($service->default_price) ? esc_attr($service->default_price) : '';
        $svc_default_price  = $bmrequests->bm_fetch_price_in_global_settings_format($svc_default_price, true);
        $svc_price          = (!empty($date)) ? $bmrequests->bm_fetch_service_price_by_service_id_and_date($service_id, $date, 'global_format') : $svc_default_price;
        $stopsales          = (!empty($date)) ? $bmrequests->bm_fetch_service_stopsales_by_service_id($service_id, $date) : 0;
        $show_svc_img       = $dbhandler->get_global_option_value('bm_show_frontend_service_image', 0) == 0 ? true : false;
        $show_read_more     = $dbhandler->get_global_option_value('bm_show_frontend_service_desc_read_more_button', 0) == 0 ? true : false;
        $show_svc_price     = $dbhandler->get_global_option_value('bm_show_frontend_service_price', 0) == 0 ? true : false;
        $show_duration      = $dbhandler->get_global_option_value('bm_show_frontend_service_duration', 0) == 0 ? true : false;
        $show_svc_desc      = $dbhandler->get_global_option_value('bm_show_frontend_service_description', 0) == 0 ? true : false;
        $svc_name_colour    = $dbhandler->get_global_option_value('bm_frontend_service_title_color', '#000000');
        $svc_button_colour  = $dbhandler->get_global_option_value('bm_frontend_book_button_color', '#000000');
        $price_text_colour  = $dbhandler->get_global_option_value('bm_frontend_service_price_text_color', '#000000');
        $svc_title_font     = $dbhandler->get_global_option_value('bm_service_title_font', '20') . 'px';
        $svc_shrt_desc_font = $dbhandler->get_global_option_value('bm_service_shrt_desc_font', '14') . 'px';
        $svc_price_txt_font = $dbhandler->get_global_option_value('bm_service_price_txt_font', '16') . 'px';
        $svc_btn_txt_colour = $dbhandler->get_global_option_value('bm_frontend_book_button_txt_color', '#ffffff');
        $inactive_show_more = $svc_desc == 'N/A' ? true : false;
        $show_more_title    = $svc_desc == 'N/A' ? '' : __('Show full description', 'service-booking');
        $gallery_title      = __('Show Gallery Images', 'service-booking');
        $category_title     = __(' category: ', 'service-booking');
        $per_person_text    = __('/person', 'service-booking');
        $stopsales_text     = __('Stopsales', 'service-booking');
        $stopsales_title    = sprintf(esc_html__('Products are not bookable until %s after current time', 'service-booking'), $bmrequests->bm_fetch_float_to_time_string($stopsales));
        $duration_title     = sprintf(esc_html__('Service duration is %s', 'service-booking'), $svc_duration);
        $no_description     = __('No short description available', 'service-booking');
        $service_title      = esc_html($service->service_name);

        $service_settings = isset($service->service_settings) && !empty($service->service_settings) ? maybe_unserialize($service->service_settings) : array();

        if (empty($show_duration)) {
            $show_svc_duration = isset($service_settings['show_service_duration']) ? $service_settings['show_service_duration'] : 0;
            $show_duration     = $show_svc_duration == 0 ? true : false;
        }

        // Prepare service array
        $modified_data = [
            'id' => $service_id,
            'service_title' => $service_title,
            'svc_name_colour' => $svc_name_colour,
            'svc_title_font' => $svc_title_font,
            'service_name' => $svc_name,
            'svc_img' => $svc_img,
            'show_svc_img' => $show_svc_img,
            'category_title' => $category_title . $category_name,
            'category_name' => $category_name,
            'show_duration' => $show_duration,
            'duration_title' => $duration_title,
            'duration' => $svc_duration,
            'gallery_images' => $gallery_images,
            'gallery_title' => $gallery_title,
            'svc_long_desc' => $svc_long_desc,
            'show_read_more' => $show_read_more,
            'inactive_show_more' => $inactive_show_more,
            'show_more_title' => $show_more_title,
            'svc_short_desc' => $svc_short_desc != 'N/A' ? $svc_short_desc : $no_description,
            'svc_shrt_desc_font' => $svc_shrt_desc_font,
            'show_svc_desc' => $show_svc_desc,
            'svc_default_price' => $svc_default_price,
            'svc_price' => $svc_price,
            'show_svc_price' => $show_svc_price,
            'svc_price_txt_font' => $svc_price_txt_font,
            'price_text_colour' => $price_text_colour,
            'per_person_text' => $per_person_text,
            'stopsales' => $stopsales,
            'stopsales_text' => $stopsales_text,
            'stopsales_title' => $stopsales_title,
            'show_stopsales_data' => ($service->show_stopsales_data == 1) ? true : false,
            'svc_total_cap_left' => $svc_total_cap_left,
            'book_button_colour' => $svc_button_colour,
            'svc_btn_txt_colour' => $svc_btn_txt_colour,
        ];


        return $modified_data;
    }

    protected function service_time_slots($service_id, $date)
    {
        $bmrequests = new BM_Request();
        $dbhandler = new BM_DBhandler();
        $slot_min_cap       = 0;
        $slot_max_cap       = 0;
        $cap_left          = 0;
        $data               = array();
        $message            = '';

        if ($service_id > 0 && !empty($date)) {
            $service_settings = $dbhandler->get_value('SERVICE', 'service_settings', $service_id, 'id');
            $service_settings = !empty($service_settings) ? maybe_unserialize($service_settings) : array();

            $total_time_slots = $bmrequests->bm_fetch_total_time_slots_by_service_id($service_id);
            $is_bookable      = $bmrequests->bm_service_is_bookable($service_id, $date);
            $is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date($service_id, $date);

            if ($is_variable_slot == 1) {
                $first_from_slot = $bmrequests->bm_fetch_variable_first_from_slot($service_id, $date);
                $slot_min_cap    = $bmrequests->bm_fetch_variable_slot_min_cap_by_service_id_and_slot_id($service_id, 1, $first_from_slot, $date);
            } elseif ($is_variable_slot == 0) {
                $slot_min_cap = $bmrequests->bm_fetch_non_variable_slot_min_cap_by_service_id_and_slot_id($service_id, 1);
            }

            $single_time_slot = array();
            if ($total_time_slots == 1) {
                $time_slot = $bmrequests->bm_fetch_single_time_slot_by_service_id($service_id, $date);

                if ($time_slot !== '-1' && $time_slot !== '0' && $is_bookable) {
                    $match     = preg_match_all('/\<span class\="single_slot_timings"\>(.*?)\<\/span\>/', $time_slot, $slot_details);
                    $time_slot = $match > 0 ? $slot_details[1][0] : $time_slot;

                    if (strpos($time_slot, ' - ') !== false) {
                        $booking_slots = explode(' - ', $time_slot);
                        $from          = $bmrequests->bm_twenty_fourhrs_format($booking_slots[0]);
                    } else {
                        $from = $bmrequests->bm_twenty_fourhrs_format($time_slot);
                    }

                    if (!empty($from)) {
                        $is_variable_slot = $bmrequests->bm_check_if_variable_slot_by_service_id_and_date($service_id, $date);
                        $slot_info        = $bmrequests->bm_fetch_slot_details($service_id, $from, $date, $total_time_slots, 0, $is_variable_slot);
                        $slot_max_cap    = isset($slot_info['slot_max_cap']) ? $slot_info['slot_max_cap'] : 0;
                        $cap_left       = isset($slot_info['capacity_left']) ? $slot_info['capacity_left'] : 0;
                        if (!empty($slot_info) && isset($slot_info['capacity_left']) && isset($slot_info['slot_min_cap'])) {
                            if ($slot_info['capacity_left'] > 0 && $slot_info['slot_min_cap'] > 0 && ($slot_info['capacity_left'] < $slot_info['slot_min_cap'])) {
                            }
                        }
                    } //end if
                } else {
                    if ($is_bookable == false) {
                        $message = 'Service Unavailable on selected Date.';
                    } else {
                        if ($time_slot == '-1') {
                            $message = 'No slots available.';
                        } elseif ($time_slot == '0') {
                            $message = 'All slots booked';
                        }
                    }
                } //end if
                $single_time_slot['slot_type'] = $is_bookable && $time_slot != -1 && $time_slot != 0 ? 'active' : 'readonly';
                $single_time_slot['time_slot'] = isset($time_slot) && $is_bookable ? $time_slot : '';
                $single_time_slot['min_capacity'] = $slot_min_cap;
                $single_time_slot['max_capacity'] = $slot_max_cap;
                $single_time_slot['capacity_left'] = $cap_left;
                $single_time_slot['capacity_left_percent'] = !empty($slot_max_cap) && $slot_max_cap > 0 ? round(($cap_left / $slot_max_cap) * 100) : 0;
            } elseif ($total_time_slots > 1) {
                $slots = $bmrequests->bm_fetch_service_time_slot_detail_by_service_id(array('id' => $service_id, 'date' => $date));
                if (!empty($slots) && $is_bookable) {
                    $time_slots = $slots;
                } else {
                    if ($is_bookable == false) {
                        $message = 'Service Unavailable on selected Date.';
                    } else {
                        $message = 'No slots available.';
                    }
                } //end if
            } //end if

            $time_slots  = isset($time_slots) ? $time_slots : array();
            $totalCap = $capLeft = 0;
            if ($single_time_slot) {
                $totalCap += $single_time_slot['max_capacity'];
                $capLeft += $single_time_slot['capacity_left'];
            }
            if (count($time_slots) > 0) {
                for ($k = 0; $k <= count($time_slots); $k++) {
                    if ($time_slots[$k]) {
                        $totalCap += $time_slots[$k]['max_capacity'];
                        $capLeft += $time_slots[$k]['capacity_left'];
                        $timeslot = explode("-", $time_slots[$k]['time_slot']);
                        $from_time = trim($timeslot[0]);
                        $to_time = trim($timeslot[1]);
                        $from_time = date('H:i', strtotime($from_time));
                        $to_time = date('H:i', strtotime($to_time));
                        // ensure grouping arrays exist
                        if (!isset($data['morning'])) $data['morning'] = [];
                        if (!isset($data['afternoon'])) $data['afternoon'] = [];
                        if (!isset($data['evening'])) $data['evening'] = [];

                        $ts_from = strtotime($from_time);
                        $ts_to = strtotime($to_time);

                        if ($ts_from !== false && $ts_to !== false) {
                            $hour_from = (int) date('G', $ts_from); // 0-23 hour

                            if ($hour_from < 12) {
                                // before 12pm -> morning
                                $data['morning'][] = $time_slots[$k];
                            } elseif ($hour_from >= 12 && $hour_from < 18) {
                                // 12pm to 6pm -> afternoon
                                $data['afternoon'][] = $time_slots[$k];
                            } else {
                                // 6pm+ -> evening (fallback)
                                $data['evening'][] = $time_slots[$k];
                            }
                        } else {
                            // if parsing fails, put into afternoon as sensible default
                            $data['afternoon'][] = $time_slots[$k];
                        }
                    }
                }
            }

            $data['single_time_slot'] = $single_time_slot;
            $data['time_slots'] = $time_slots;
            $data['capacity_left_percentage'] = !empty($totalCap) && $totalCap > 0 ? round(($capLeft / $totalCap) * 100) : 0;
            $data['total_capacity_left'] = $capLeft;
            $data['message'] = $message;
        } //end if

        return $data;
    }

    /**
     * Fetch latest order info for gift
     *
     * @author Jyoti
     */
    public function gift_fetch_order_info($data = array())
    {
        $dbhandler     = new BM_DBhandler();
        $bm = new BM_Request();
        $booking_fields = array();

        if (!empty($data)) {
            $id                       = isset($data['id']) ? $data['id'] : '';
            $time                     = isset($data['time_slot']) ? $data['time_slot'] : '';
            $total_service_booking    = isset($data['total_service_booking']) ? $data['total_service_booking'] : 0;
            $date                     = isset($data['date']) ? $data['date'] : '';
            $extra_svc_ids            = isset($data['extra_svc_ids']) ? $data['extra_svc_ids'] : '';
            $total_extra_slots_booked = isset($data['no_of_persons']) ? $data['no_of_persons'] : '';
            $match                    = preg_match_all('/\<span class\="single_slot_timings"\>(.*?)\<\/span\>/', $time, $slot_details);
            $booking_currency         = $bm->bm_get_currency_symbol($dbhandler->get_global_option_value('bm_booking_currency', 'EUR'));
            $total_extra_price        = 0;

            if (!empty($id) && !empty($date)) {
                $service        = $dbhandler->get_row('SERVICE', $id);
                $svc_img        = esc_url($bm->bm_fetch_image_url_or_guid($id, 'SERVICE', 'url'));
                $svc_name       = !empty($service) && isset($service->service_name) && !empty($service->service_name) ? esc_html($service->service_name) : 'N/A';
                $short_svc_name = !empty($svc_name) ? mb_strimwidth($svc_name, 0, 20, '...') : 'N/A';
                $base_svc_price = $bm->bm_fetch_service_price_by_service_id_and_date($id, $date);
                $booking_price  = $bm->bm_fetch_total_price(str_replace($booking_currency, '', $base_svc_price), $total_service_booking);
                $total_cost     = $booking_price;

                $svc_price_module_id = $bm->bm_fetch_external_service_price_module_by_service_id_and_date($id, $date);

                $booking_fields['service_id']               = $id;
                $booking_fields['booking_slots']            = $match > 0 ? $slot_details[1][0] : $time;
                $booking_fields['booking_date']             = "";
                $booking_fields['service_name']             = $svc_name;
                $booking_fields['total_service_booking']    = $total_service_booking;
                $booking_fields['extra_svc_booked']         = $extra_svc_ids;
                $booking_fields['total_extra_slots_booked'] = $total_extra_slots_booked;
                $booking_fields['base_svc_price']           = $base_svc_price;
                $booking_fields['service_cost']             = $booking_price;
                $booking_fields['svc_price_module_id']      = $svc_price_module_id;

                // Calculate extra service price and total cost
                if (isset($extra_svc_ids) && !empty($extra_svc_ids) && isset($total_extra_slots_booked) && !empty($total_extra_slots_booked)) {
                    $total_slots_booked = explode(',', $total_extra_slots_booked);
                    $extra_total        = array();
                    $additional         = "id in($extra_svc_ids)";

                    $extra_price = $dbhandler->get_all_result('EXTRA', 'extra_price', 1, 'results', 0, false, null, false, $additional);
                    $extra_price = array_column($extra_price, 'extra_price');
                    $i           = 1;

                    if (!empty($extra_price) && !empty($total_slots_booked)) {
                        foreach ($extra_price as $key => $price) {
                            if (!empty($price)) {
                                $extra_total[$key] = $bm->bm_fetch_total_price($price, $total_slots_booked[$key]);
                            }
                            $i++;
                        }
                    } //end if

                    if (!empty($extra_total)) {
                        $total_extra_price = array_sum($extra_total);
                        $total_cost        = ($total_cost + $total_extra_price);
                    } //end if
                } //end if

                $booking_fields['extra_svc_cost'] = $total_extra_price;
                $booking_fields['total_cost']     = $total_cost;
                $booking_fields['subtotal']       = $total_cost;
            } //end if
        } //end if

        return $booking_fields;
    } //end gift_fetch_order_info()

    /**
     * Check payment type
     *
     * @author Jyoti
     */
    public function gift_check_payment_type_and_return_data($booking_key, $checkout_key, $checkout_type = 'flexi_checkout')
    {
        $dbhandler = new BM_DBhandler();
        $bmrequest = new BM_Request();

        if ($dbhandler->get_global_option_value('discount_' . $booking_key) == 1) {
            $booking_details = $dbhandler->bm_fetch_data_from_transient('discounted_' . $booking_key);
        } else {
            $booking_details = $dbhandler->bm_fetch_data_from_transient($booking_key);
        }

        /**$booking_details  = $dbhandler->bm_fetch_data_from_transient( $booking_key );*/
        $checkout_details = $dbhandler->bm_fetch_data_from_transient($checkout_key);
        $data             = array();
        $resp             = '';

        if (!empty($booking_details) && !empty($checkout_details)) {
            $service_id   = isset($booking_details['service_id']) ? $booking_details['service_id'] : 0;

            $bookable_extra       = $bmrequest->bm_is_selected_extra_service_bookable($booking_key);

            if (!empty($service_id)) {

                $payment_session_timer   = $dbhandler->get_global_option_value('bm_payment_session_time', '2');
                $payment_session_timer   = ($payment_session_timer * 60) + 2;
                $is_book_on_request_only = $bmrequest->bm_check_if_book_on_request_only($service_id);

                $timezone    = $dbhandler->get_global_option_value('bm_booking_time_zone', 'Asia/Kolkata');
                $now         = new DateTime('now', new DateTimeZone($timezone));


                if (($bookable_extra == false)) {
                    $resp = __('One or more extra services does not have enough capacity, choose another !!', 'service-booking');

                    $data['status'] = 'error';
                    $data['data']   = wp_kses_post($resp);
                } else {
                    $booked_product = $bmrequest->bm_fetch_booked_service_info_for_stripe_payment_intent($booking_key);

                    $amount      = !empty($booked_product) && isset($booked_product['amount']) ? floatval($booked_product['amount']) * 100 : 0;
                    $currency    = !empty($booked_product) && isset($booked_product['currency']) ? $booked_product['currency'] : '';
                    $description = !empty($booked_product) && isset($booked_product['description']) ? $booked_product['description'] : '';

                    if (($amount > 0) && !empty($currency) && !empty($description)) {

                        if (($is_book_on_request_only == 1)) {
                            $checkout_details['request_type'] = 'on_request';
                        } else {
                            $checkout_details['request_type'] = 'direct';
                        }

                        if ($checkout_type == 'woocommerce_checkout') {
                            $checkout_details['request_type'] = 'direct';
                        }

                        if (isset($checkout_details['billing']) && isset($checkout_details['checkout']) && isset($checkout_details['request_type'])) {
                            $dbhandler->bm_save_data_to_transient($checkout_key, $checkout_details, 24);

                            if ($checkout_type == 'woocommerce_checkout') {
                                $data['status'] = 'success';
                            } else {
                                $string = $bmrequest->bm_create_random_string(20);
                                $bmrequest->bm_start_session_with_expiry("flexi_current_payment_session_$booking_key", $payment_session_timer);

                                $data['status']   = 'success';
                                $data['data']     = wp_kses_post($string);
                                $data['checkout'] = wp_kses_post($checkout_key);
                            }
                        } else {
                            $resp = __('Error Initiating Payment Data !!', 'service-booking');

                            $data['status'] = 'error';
                            $data['data']   = wp_kses_post($resp);
                        }
                    } else {
                        $resp = __('Error Fetching Booking Data !!', 'service-booking');

                        $data['status'] = 'error';
                        $data['data']   = wp_kses_post($resp);
                    }
                }
            } else {
                $resp = __('Invalid Service or Booking Date, Try Again !!', 'service-booking');

                $data['status'] = 'error';
                $data['data']   = wp_kses_post($resp);
            }
        } else {
            $resp = __('Something Went Wrong, Try Again !!', 'service-booking');

            $data['status'] = 'error';
            $data['data']   = wp_kses_post($resp);
        }

        return $data;
    } //end gift_check_payment_type_and_return_data()
}
