<?php

$plugin_path      = plugin_dir_url( __FILE__ );
$message          = isset( $template_body ) ? wp_kses_post( stripslashes( $template_body ) ) : '';
$source           = isset( $source ) ? $source : -1;
$mail_type        = isset( $mail_type ) ? $mail_type : '';
$order_id         = isset( $order_id ) ? $order_id : 0;
$mesage_header    = esc_html__( 'Thanks for Your Order', 'service-booking' );
$mesage_subheader = esc_html__( 'We have received your order successfully.', 'service-booking' );
$header_class     = 'normal-header';

if ( $mail_type == 'new_request' ) {
	$mesage_header    = esc_html__( 'Thanks for Your Request', 'service-booking' );
	$mesage_subheader = esc_html__( 'We have received your request successfully.', 'service-booking' );
} elseif ( $mail_type == 'cancel_order' ) {
	$header_class     = 'red-header';
	$mesage_header    = esc_html__( 'Your Order is Cancelled', 'service-booking' );
	$mesage_subheader = esc_html__( 'Your order has been cancelled. We will refund your order within 2-3 working days if any.', 'service-booking' );
} elseif ( $mail_type == 'refund_order' ) {
	$header_class     = 'yellow-header';
	$mesage_header    = esc_html__( 'Your Order is Refunded', 'service-booking' );
	$mesage_subheader = esc_html__( 'Your order has been refunded successfully.', 'service-booking' );
} elseif ( $mail_type == 'failed_order' ) {
	$header_class     = 'red-header';
	$mesage_header    = esc_html__( 'Your Order Failed', 'service-booking' );
	$mesage_subheader = esc_html__( 'Your order has failed. We will refund your order within 2-3 working days if any.', 'service-booking' );
} elseif ( $mail_type == 'approved_order' ) {
	$header_class     = 'green-header';
	$mesage_header    = esc_html__( 'Your Order is Confirmed', 'service-booking' );
	$mesage_subheader = esc_html__( 'Your order has been confirmed.', 'service-booking' );
} elseif ( $mail_type == 'gift_voucher' ) {
	$header_class     = 'pink-header';
	$mesage_header    = esc_html__( 'Your have received a gift voucher', 'service-booking' );
	$mesage_subheader = esc_html__( 'You can use this voucher to redeem the gift.', 'service-booking' );
} elseif ( $mail_type == 'voucher_redeem' ) {
	$header_class     = 'pink-header';
	$mesage_header    = esc_html__( 'Your have redeemed your gift voucher successfully', 'service-booking' );
	$mesage_subheader = esc_html__( 'Now you can avail your service.', 'service-booking' );
}

?>

<!DOCTYPE html>
<html>
<style>
   body {
    font-family: Arial, sans-serif;
    background: #f1f1f1;
   }

   .container {
      width: 100%;
      max-width: 600px;
      margin: 0 auto;
      background: #fff;
      border-collapse: collapse;
   }

   .header {
      width: 100%;
      padding: 10px;
      text-align: center;

   }

   .normal-header {
      background-color: #1f86bb;
   }

   .red-header {
      background-color:rgb(209, 32, 8);
   }

   .yellow-header {
      background-color:rgb(227, 213, 11);
   }

   .green-header {
      background-color:rgb(58, 113, 6);
   }

   .pink-header {
      background-color:rgb(228, 41, 175);
   }

   .header h1 {
      margin: 0;
      color: #ffffff;
      font-weight: normal;
   }

   .header p {
      margin: 0px;
      line-height: 1.8;
      font-size: 20px;
      color: #fff;
   }

   .order-details,
   .billing-shipping,
   .footer {
      width: 100%;
      padding: 20px;

   }

   .billing-shipping th {
      color: #555;
   }

   .order-details th,
   .order-details td,
   .billing-shipping td {
      border-top: 1px solid #e0e0e0;
      padding: 8px;
      text-align: left;
   }

   .billing-shipping th {
      text-align: left;
   }

   .order-details tr:first-child td {
      border-top: none;
   }

   .order-details p {
      font-size: 15px;
      line-height: 1.6;
      margin: 0px;
   }

   .subheading {
      font-size: 11px;
      color: #555;
      line-height: 1.5;
      width: 33%;
   }

   .subheading span {
      font-size: 13px;
      color: #444;
   }

   .subtotal {
      font-size: 14px;
   }

   .noborder td {
      border: none !important;
      padding-top: 4px;
      padding-bottom: 4px;
      font-size: 14px;
   }

   .addresstext {
      line-height: 1.5;
      color: #555;
      padding-left: 0px !important;
   }

   .discountbox {
      border-collapse: collapse;
   }

   .discount {
      background-color: #efefef;
      padding: 20px;
      text-align: center;
      margin-top: 20px;
   }

   .discountheading {
      color: #555;
      font-size: 18px;
      margin-bottom: 0px;
      font-weight: bold;
   }

   .discountheading span {
      color: #d32f2f;
   }

   .discount a {
      display: inline-block;
      padding: 15px 20px;
      background-color: #fff;
      color: #d32f2f;
      font-weight: bold;
      text-decoration: none;
      margin-top: 10px;
      border: 1px dashed #555;
      width: 90%;
      border-radius: 10px;
   }

   .discountvalue {
      color: #30a7d2;
   }

   .shopnowbtn {
      display: inline-block;
      padding: 15px 20px;
      background-color: #515050;
      border: 1px solid #515050;
      color: #fff;
      font-weight: bold;
      text-decoration: none;
      margin-top: 10px;
      width: 90%;
      border-radius: 10px;
   }

   .thankstext {
      font-size: 13px;
      line-height: 1.5;
      color: #555;
   }

   .footer {
      font-size: 13px;
      color: #555;
      text-align: center;

   }

   .copyright {
      text-align: center;
      font-weight: bold;
      margin-bottom: 15px;
      font-size: 12px;
   }

   .privacytext {
      font-size: 14px;
      font-weight: bold;
      color: #555;
      text-align: left;
      padding-top: 15px;
   }

   .termstext {
      font-size: 14px;
      font-weight: bold;
      padding-top: 15px;
      text-align: right;
   }

   .footer a {
      color: #555;
      text-decoration: none;
      margin: 0 15px;

   }
</style>

   <body>
      <table class="container">
         <tr>
            <td>
               <table class="header <?php echo esc_html( $header_class ); ?>">
                  <tr>
                     <td>
                        <p><?php echo esc_html( $mesage_header ); ?></p>
                     </td>
                  </tr>
               </table>
               <!-- <table class="order-details">
                  <tr>
                     <td colspan="4">
                        <p><?php echo esc_html( $mesage_subheader ); ?></p>
                     </td>
                  </tr>
               </table> -->
               <table class="billing-shipping noborder">
                  <tr>
                     <td class="addresstext">
                        <?php echo wp_kses_post( $message ); ?>
                     </td>
                  </tr>
               </table>
               <!-- <table class="footer">
                  <tr>
                     <td colspan="2" class="copyright"><img src="<?php echo esc_url( $plugin_path . 'images/logo.png' ); ?>" style="width:200px;"/><br/><?php esc_html_e( 'Copyrights Reserved ', 'service-booking' ); ?> &copy; <?php echo esc_html( gmdate( 'Y' ) ); ?></td>
                  </tr>
               </table> -->
            </td>
         </tr>
      </table>
   </body>
</html>
