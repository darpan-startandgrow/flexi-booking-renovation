<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly

class React_Shortcodes_Plugin
{

    private $plugin_path;
    private $plugin_url;

    /**
     * Normalize a shortcode attribute value to a "true"/"false" string.
     */
    private function parse_bool($value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        $lower = strtolower(trim((string) $value));
        return in_array($lower, ['true', '1', 'yes', 'on'], true) ? 'true' : 'false';
    }

    /**
     * Build a div element with data-* attributes from an associative array.
     * Array values are JSON-encoded; scalar values are escaped directly.
     *
     * @param string $id      The element ID.
     * @param array  $data    Map of attribute name => value.
     * @return string         HTML div element.
     */
    private function build_div(string $id, array $data): string
    {
        $attrs = 'id="' . esc_attr($id) . '"';
        foreach ($data as $key => $value) {
            $attr_name = 'data-' . esc_attr($key);
            if (is_array($value)) {
                $attrs .= ' ' . $attr_name . "='" . esc_attr(wp_json_encode($value)) . "'";
            } else {
                $attrs .= ' ' . $attr_name . '="' . esc_attr((string) $value) . '"';
            }
        }
        return '<div ' . $attrs . '></div>';
    }

    /**
     * Parse an attribute that may be a JSON string or already an array.
     */
    private function parse_array_attr($value, array $default): array
    {
        if (is_array($value)) {
            return $value; // Already an array (default was used, no shortcode attr passed)
        }

        if (is_string($value) && !empty($value)) {
            $decoded = json_decode(stripslashes($value), true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded;
            }
        }

        return $default; // Fallback to default if decode fails
    }

    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(__FILE__);
        $this->plugin_url  = plugin_dir_url(__FILE__);

        add_action('wp_enqueue_scripts', [$this, 'enqueue_react_assets']);
        add_shortcode('react_sgbm_starting_date', [$this, 'react_sgbm_starting_date']);
        add_shortcode('react_sgbm_starting_gift', [$this, 'react_sgbm_starting_gift']);
        add_shortcode('react_sgbm_starting_category', [$this, 'react_sgbm_starting_category']);
        add_shortcode('react_sgbm_starting_service', [$this, 'react_sgbm_starting_service']);
        add_shortcode('react_sgbm_redeem_gift', [$this, 'react_sgbm_redeem_gift']);
    }

    /**
     * Enqueue JS & CSS from Vite build
     */
    public function enqueue_react_assets()
    {
        $react_dir = plugin_dir_path(__FILE__) . '../react-frontend-v2/';
        $react_url = plugin_dir_url(__FILE__) . '../react-frontend-v2/';

        $manifest_path = $react_dir . '.vite/manifest.json';

        // Default fallback paths
        $js_file  = $react_url . 'assets/index.js';
        $css_file = $react_url . 'assets/index.css';

        if (file_exists($manifest_path)) {
            $manifest = json_decode(file_get_contents($manifest_path), true);
            $entry = $manifest['index.html'] ?? null;

            if ($entry && isset($entry['file'])) {
                $js_file = $react_url .  $entry['file'];
            }
            if (!empty($entry['css'][0])) {
                $css_file = $react_url .  $entry['css'][0];
            }
        }

        wp_enqueue_script('vite-react-js', $js_file, [], null, true);
        wp_enqueue_style('vite-react-css', $css_file, [], null);

        add_filter('script_loader_tag', function ($tag, $handle) {
            if ($handle === 'vite-react-js') {
                return str_replace('<script ', '<script type="module" ', $tag);
            }
            return $tag;
        }, 10, 2);
    }

    /**
     * Shortcode 1
     */
    public function react_sgbm_starting_date($atts)
    {
        $default_steps_visibility = [
            'step_1_title_visible' => 'true',
            'step_2_title_visible' => 'true',
            'step_3_title_visible' => 'true',
            'step_4_title_visible' => 'false',
            'step_5_title_visible' => 'true',
        ];

        $default_step_titles = [
            'step_1_title' => 'Book your Services',
            'step_2_title' => 'What experience are you looking for?',
            'step_3_title' => 'What experience are you looking for?',
            'step_4_title' => '',
            'step_5_title' => 'Checkout',
        ];

        $atts = shortcode_atts([
            'steps_visibility'          => '',  // ← Empty string, NOT array
            'step_titles'               => '',  // ← Empty string, NOT array
            'topbar'                    => 'true',
            'rightbar'                  => 'false',
            'bottombar'                 => 'false',
            'mobile-heading'            => 'false',
            'show_book_now_button'      => 'false',
            'calendar_info_visibility'  => 'false',
            'calendar_info'             => 'bar',
            'category_label_visibility' => 'false',
            'terms_and_condition_link'  => '#/',
            'secondary_color'           => '#26484d',
        ], $atts, 'react_sgbm_starting_date');

        // Parse arrays safely — handles both string (from shortcode) and array (default fallback)
        $steps_visibility = $this->parse_array_attr($atts['steps_visibility'], $default_steps_visibility);
        $step_titles      = $this->parse_array_attr($atts['step_titles'], $default_step_titles);

        return $this->build_div('react_sgbm_starting_date', [
            'topbar'                    => $this->parse_bool($atts['topbar']),
            'rightbar'                  => $this->parse_bool($atts['rightbar']),
            'bottombar'                 => $this->parse_bool($atts['bottombar']),
            'mobile-heading'            => $this->parse_bool($atts['mobile-heading']),
            'show-book-now-button'      => $this->parse_bool($atts['show_book_now_button']),
            'calendar-info-visibility'  => $this->parse_bool($atts['calendar_info_visibility']),
            'calendar-info'             => esc_attr($atts['calendar_info']),
            'category-label-visibility' => $this->parse_bool($atts['category_label_visibility']),
            'terms-and-condition-link'  => esc_url($atts['terms_and_condition_link']),
            'secondary-color'           => esc_attr($atts['secondary_color']),
            'step-titles'               => array_map('sanitize_text_field', $step_titles),
            'steps-visibility'          => array_map([$this, 'parse_bool'], $steps_visibility),
        ]);
    }

    /**
     * Shortcode 2
     */
    public function react_sgbm_starting_gift($atts)
    {
        $atts = shortcode_atts([
            'steps_visibility'          => [
                'step_1_title_visible' => 'true',
                'step_2_title_visible' => 'true',
                'step_3_title_visible' => 'true',
                'step_4_title_visible' => 'false',
                'step_5_title_visible' => 'true',
            ],
            'step_titles'               => [
                'step_1_title' => 'Book your Services',
                'step_2_title' => 'What experience are you looking for?',
                'step_3_title' => 'What experience are you looking for?',
                'step_4_title' => '',
                'step_5_title' => 'Checkout',
            ],
            'topbar'         => 'true',
            'rightbar'       => 'false',
            'bottombar'      => 'false',
            'mobile-heading' => 'false',
            'show_book_now_button' => 'false',
            'calendar_info_visibility' => 'false',
            'calendar_info' => 'bar', // 'bar' or 'price'
            'category_label_visibility' => 'false',
            'terms_and_condition_link' => '#/',
            'secondary_color' => '#26484d',

        ], $atts, 'react_sgbm_starting_gift');

        return $this->build_div('react_sgbm_starting_gift', [
            'topbar'                    => $this->parse_bool($atts['topbar']),
            'rightbar'                  => $this->parse_bool($atts['rightbar']),
            'bottombar'                 => $this->parse_bool($atts['bottombar']),
            'mobile-heading'            => $this->parse_bool($atts['mobile-heading']),
            'show-book-now-button'      => $this->parse_bool($atts['show_book_now_button']),
            'calendar-info-visibility'  => $this->parse_bool($atts['calendar_info_visibility']),
            'calendar-info'             => esc_attr($atts['calendar_info']),
            'category-label-visibility' => $this->parse_bool($atts['category_label_visibility']),
            'terms-and-condition-link'  => esc_url($atts['terms_and_condition_link']),
            'secondary-color'           => esc_attr($atts['secondary_color']),
            'step-titles'               => array_map('sanitize_text_field', (array) $atts['step_titles']),
            'steps-visibility'          => array_map([$this, 'parse_bool'], (array) $atts['steps_visibility']),
        ]);
    }
    /**
     * Shortcode 3
     */
    public function react_sgbm_starting_category($atts)
    {
        $atts = shortcode_atts([
            'steps_visibility'          => [
                'step_1_title_visible' => 'true',
                'step_2_title_visible' => 'true',
                'step_3_title_visible' => 'true',
                'step_4_title_visible' => 'false',
                'step_5_title_visible' => 'true',
            ],
            'step_titles'               => [
                'step_1_title' => 'What experience are you looking for?',
                'step_2_title' => 'What experience are you looking for?',
                'step_3_title' => 'What experience are you looking for?',
                'step_4_title' => '',
                'step_5_title' => 'Checkout',
            ],
            'topbar'         => 'true',
            'rightbar'       => 'false',
            'bottombar'      => 'false',
            'mobile-heading' => 'false',
            'show_book_now_button' => 'false',
            'calendar_info_visibility' => 'false',
            'calendar_info' => 'bar', // 'bar' or 'price'
            'category_label_visibility' => 'false',
            'terms_and_condition_link' => '#/',
            'secondary_color' => '#26484d',
        ], $atts, 'react_sgbm_starting_category');

        return $this->build_div('react_sgbm_starting_category', [
            'topbar'                    => $this->parse_bool($atts['topbar']),
            'rightbar'                  => $this->parse_bool($atts['rightbar']),
            'bottombar'                 => $this->parse_bool($atts['bottombar']),
            'mobile-heading'            => $this->parse_bool($atts['mobile-heading']),
            'show-book-now-button'      => $this->parse_bool($atts['show_book_now_button']),
            'calendar-info-visibility'  => $this->parse_bool($atts['calendar_info_visibility']),
            'calendar-info'             => esc_attr($atts['calendar_info']),
            'category-label-visibility' => $this->parse_bool($atts['category_label_visibility']),
            'terms-and-condition-link'  => esc_url($atts['terms_and_condition_link']),
            'secondary-color'           => esc_attr($atts['secondary_color']),
            'step-titles'               => array_map('sanitize_text_field', (array) $atts['step_titles']),
            'steps-visibility'          => array_map([$this, 'parse_bool'], (array) $atts['steps_visibility']),
        ]);
    }

    /**
     * Shortcode 4
     */
    public function react_sgbm_starting_service($atts)
    {
        $atts = shortcode_atts([
            'steps_visibility'          => [
                'step_1_title_visible' => 'true',
                'step_2_title_visible' => 'true',
                'step_3_title_visible' => 'false',
                'step_4_title_visible' => 'true',
            ],
            'step_titles'               => [
                'step_1_title' => 'What experience are you looking for?',
                'step_2_title' => 'What experience are you looking for?',
                'step_3_title' => '',
                'step_4_title' => 'Checkout',
            ],
            'topbar'         => 'true',
            'rightbar'       => 'false',
            'bottombar'      => 'false',
            'mobile-heading' => 'false',
            'show_book_now_button' => 'false',
            'calendar_info_visibility' => 'false',
            'calendar_info' => 'bar', // 'bar' or 'price'
            'category_label_visibility' => 'false',
            'terms_and_condition_link' => '#/',
            'secondary_color' => '#26484d',
        ], $atts, 'react_sgbm_starting_service');

        return $this->build_div('react_sgbm_starting_service', [
            'topbar'                    => $this->parse_bool($atts['topbar']),
            'rightbar'                  => $this->parse_bool($atts['rightbar']),
            'bottombar'                 => $this->parse_bool($atts['bottombar']),
            'mobile-heading'            => $this->parse_bool($atts['mobile-heading']),
            'show-book-now-button'      => $this->parse_bool($atts['show_book_now_button']),
            'calendar-info-visibility'  => $this->parse_bool($atts['calendar_info_visibility']),
            'calendar-info'             => esc_attr($atts['calendar_info']),
            'category-label-visibility' => $this->parse_bool($atts['category_label_visibility']),
            'terms-and-condition-link'  => esc_url($atts['terms_and_condition_link']),
            'secondary-color'           => esc_attr($atts['secondary_color']),
            'step-titles'               => array_map('sanitize_text_field', (array) $atts['step_titles']),
            'steps-visibility'          => array_map([$this, 'parse_bool'], (array) $atts['steps_visibility']),
        ]);
    }

    /**
     * Shortcode 5
     */
    public function react_sgbm_redeem_gift($atts)
    {
        $atts = shortcode_atts([
            'steps_visibility'          => [
                'step_1_title_visible' => 'true',
                'step_2_title_visible' => 'true',
                'step_3_title_visible' => 'true',
                'step_4_title_visible' => 'true',
            ],
            'step_titles'               => [
                'step_1_title' => 'Redeem Your gift',
                'step_2_title' => 'Select Date',
                'step_3_title' => 'Select Slot Details',
                'step_4_title' => 'Checkout',
            ],
            'topbar'         => 'true',
            'rightbar'       => 'false',
            'bottombar'      => 'false',
            'terms_and_condition_link' => '#/',
            'secondary_color' => '#26484d',
        ], $atts, 'react_sgbm_redeem_gift');

        return $this->build_div('react_sgbm_redeem_gift', [
            'topbar'         => $this->parse_bool($atts['topbar']),
            'rightbar'       => $this->parse_bool($atts['rightbar']),
            'bottombar'      => $this->parse_bool($atts['bottombar']),
            'terms-and-condition-link' => esc_url($atts['terms_and_condition_link']),
            'secondary-color' => esc_attr($atts['secondary_color']),
            'step-titles'     => array_map('sanitize_text_field', (array) $atts['step_titles']),
            'steps-visibility' => array_map([$this, 'parse_bool'], (array) $atts['steps_visibility']),
        ]);
    }
}
