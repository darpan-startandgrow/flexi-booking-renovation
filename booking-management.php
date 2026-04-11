<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link    https://startandgrow.in
 * @since   1.0.0
 * @package Booking_Management
 *
 * @wordpress-plugin
 * Plugin Name:       BookingManagement
 * Plugin URI:        https://startandgrow.in
 * Description:       A Plugin to book and manage services
 * Version:           1.0.0
 * Author:            Start and Grow
 * Author URI:        https://startandgrow.in
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       service-booking
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

/*
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BOOKING_MANAGEMENT_VERSION', '1.0.0' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-booking-management-activator.php
 */
function activate_booking_management() {
     include_once plugin_dir_path( __FILE__ ) . 'includes/class-booking-management-activator.php';
    $activator = new Booking_Management_Activator();
    $activator->activate();

}//end activate_booking_management()


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-booking-management-deactivator.php
 */
function deactivate_booking_management() {
     include_once plugin_dir_path( __FILE__ ) . 'includes/class-booking-management-deactivator.php';
    $deactivator = new Booking_Management_Deactivator();
    $deactivator->deactivate();

}//end deactivate_booking_management()


register_activation_hook( __FILE__, 'activate_booking_management' );
register_deactivation_hook( __FILE__, 'deactivate_booking_management' );

/*
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-booking-management.php';


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since 1.0.0
 */
function run_booking_management() {
    $plugin = new Booking_Management();
    $plugin->run();

}//end run_booking_management()


run_booking_management();
