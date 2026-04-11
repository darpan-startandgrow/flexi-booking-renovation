<?php

$pdf_processor = new BM_PDF_Processor();
$html          = $pdf_processor->bm_get_template_pdf_content( 'voucher', $booking_id );

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
            .container {
                width: 100%;
            }
            .header p {
                display: block !important;
                font-size: 24px;
                font-weight: bold;
                text-align: center !important;
                color: #4A90E2;
                padding: 10px 0;
                width: 100% !important;
            }
            .order-details {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
            }
            .order-details tr:nth-child(even) {
                background-color: #f9f9f9;
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
                font-size: 14px;
                color: #555;
            }
            .td-right-align {
                text-align: right;
            }
            .subtotal, .totalbar {
                font-weight: bold;
                background-color: #f1f1f1;
            }
            .billing-shipping-notification {
                width: 100%;
                margin-top: 20px;
                background-color: #fff5e5;
                border-left: 5px solid #ffa500;
                padding: 15px;
                color: #333;
            }
            .addresstext {
                font-size: 14px;
                color: #555;
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
                font-size: 16px;
                color: #4A90E2;
                padding: 10px;
                background-color: #f9f9f9;
                word-wrap: break-word;
            }
            .order-details .subheading strong {
                font-weight: bold;
                color: #333;
            }
            .order-details .subheading span {
                font-size: 14px;
                color: #555;
            }
            .td-center-align {
                text-align: center;
                width: 30%;
            }
            .noborder th, .noborder td {
                border: none !important;
            }
            th, td {
                border: 1px solid #ddd;
                font-size: 14px;
            }
        </style>
    </head>
    <body>' . $html . '</body>
    </html>';
}

echo $html;

