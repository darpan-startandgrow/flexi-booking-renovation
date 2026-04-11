<?php

$pdf_processor = new BM_PDF_Processor();
$html          = $pdf_processor->bm_get_template_pdf_content( 'booking', $booking_id );

if ( strpos( $html, '<!DOCTYPE' ) === false && strpos( $html, '<html' ) === false ) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { 
                font-family: Arial, sans-serif; 
                color: #333;
                line-height: 1.6;
            }
            .wrapper {
                width: 100%;
            }
            .container {
                width: 90%;
                margin: auto;
                border-radius: 4px;
                border: 1px solid #E6E6E6;
            }
            .header {
                width: 100%;
            }
            .header p {
                font-size: 24px;
                font-weight: bold;
                text-align: left !important;
                color: #393434;
                padding: 10px 0;
                margin-bottom: 0px;
                margin-left: 5%;
            }
            .qr-container {
                width: 100px;
                height: 100px;
                float: right;
            }
            .order-details {
                width: 100%;
                padding: 10px 16px 10px 16px;
                background: #FFF;
            }
            .order-details td {
                padding-top: 5px;
                padding-bottom: 5px;
            }
            .order-details th {
                background-color: #4A90E2;
                color: #fff;
                font-size: 16px;
                text-align: center;
                padding: 12px 15px;
                border: 1px solid #ddd !important;
            }
            .theading {
                font-weight: bold;
                font-size: 16px;
                color: #4A90E2;
            }
            .subtext {
                font-weight: 400;
                color: #6B6B6B;
            }
            .td-right-align {
                text-align: right;
            }
            .subtotal, .totalbar {
                font-size: 16px;
                font-weight: bold;
            }
            .value {
                font-size: 16px !important;
                font-weight: 400 !important;
            }
            .billing-shipping-notification {
                width: 96%;
                margin-left: 2%;
                margin-top: 20px;
                background-color: #FFF5E5;
                border-left: 5px solid #ffa500;
                padding: 15px;
                padding-left: 32px;
                color: #393434;
                font-size: 16px;
            }
            .addresstext {
                font-size: 12px;
                color: #3B3B3B;
                line-height: 1.5;
            }
            .negative_discount {
                color: #FF5733;
                font-weight: bold;
            }
            .positive_discount, .postive_price_module_discount {
                color: #27AE60;
                font-weight: bold;
            }
            .order-details .subheading {
                font-size: 14px;
                font-weight: 500;
                color: #393434;
                word-wrap: break-word;
            }
            .order-details .subheading span {
                font-weight: 400;
                color: #6B6B6B;
            }
            hr {
                margin: 0px 10px; 
                border: none; 
                border-top: 1px dashed #B9B9B9;
            }
            .td-center-align {
                text-align: center;
            }
            .noborder th, .noborder td {
                border: none !important;
            }
            .discountheading {
                font-size: 20px;
            }
            th, td {
                font-size: 14px;
            }
        </style>
    </head>
    <body>' . $html . '</body>
    </html>';
}

echo $html;

