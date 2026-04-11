<?php

/**
 * The SMTP class of the plugin.
 *
 * @link  https://startandgrow.in
 * @since 1.0.0
 *
 * @package    Booking_Management
 * @subpackage Booking_Management/admin
 */
class Booking_Management_SMTP {


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


    /**
     * Initialize the class and set its properties.
     *
     * @since 1.0.0
     * @param string $plugin_name The name of this plugin.
     * @param string $version     The version of this plugin.
     */
    public function __construct( $plugin_name, $version ) {

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

    }


    /**
     * Mail function
     *
     * @since 1.0.0
     */
    public function bm_mail_connection( $phpmailer ) {
        $dbhandler = new BM_DBhandler();

        $enable_smtp = $dbhandler->get_global_option_value( 'bm_enable_smtp', 0 );

        if ( $enable_smtp == 1 ) {
            $phpmailer->Mailer     = 'smtp';
            $phpmailer->Host       = $dbhandler->get_global_option_value( 'bm_smtp_host' );
            $phpmailer->Port       = $dbhandler->get_global_option_value( 'bm_smtp_port' );
            $phpmailer->SMTPSecure = $dbhandler->get_global_option_value( 'bm_smtp_encription' );
            $phpmailer->SMTPAuth   = $dbhandler->get_global_option_value( 'bm_smtp_authentication' ) == 'true' ? true : false;

            if ( $phpmailer->SMTPAuth ) {
                $phpmailer->Username = $dbhandler->get_global_option_value( 'bm_smtp_username' );
                $phpmailer->Password = $dbhandler->get_global_option_value( 'bm_smtp_password' );
            }
        } else {
            $phpmailer->isMail();
        }

        // Set "From" details (should always be applied)
        $phpmailer->From     = $dbhandler->get_global_option_value( 'bm_smtp_from_email_address', get_option( 'admin_email' ) );
        $phpmailer->FromName = $dbhandler->get_global_option_value( 'bm_smtp_from_email_name', get_bloginfo( 'name' ) );
        $phpmailer->Sender   = $phpmailer->From;
        $phpmailer->AddReplyTo( $phpmailer->From, $phpmailer->FromName );
    } // end bm_smtp_connection()

}//end class
