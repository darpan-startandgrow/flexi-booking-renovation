<?php

$pdf_processor = new BM_PDF_Processor();
$html          = $pdf_processor->bm_get_template_pdf_content( 'customer_info', $booking_id );

if ( strpos( $html, '<!DOCTYPE' ) === false && strpos( $html, '<html' ) === false ) {
    $html = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
    @page {
        margin: 0;
        padding: 0;
    }

    body {
        font-family: Sarabun;
        color: #333;
        margin: 0;
        padding: 0;
    }

    .pdf-container {
        width:640px;
        background:#fff;
        margin:auto;
    }

    .header {
        text-align: left;
    }

    .header h1 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    .logo {
        /* opacity: 0.05; */
        width: 100%;
        height: auto;
        margin-bottom: 5px;
    }
    .customerorderwrapper{
        width:600px;
        height:680px;
        padding:20px;
        float:left;
        border:1px solid #DEDEDE;
        border-radius:4px;
    }
    .billingbox{
        width:300px;
        float:left;
        height: 680px;
        text-align:left;
        border-right:1px solid #dedede; 
    }
    /* .divider{
        width:1px;
        height:800px;
        float:left;
        
    } */
    .shippingbox{
        width:300px;
        float:right;
        height:680px;
        text-align:right;
    }
    h3 {
        font-size: 22px;
        color: #212121;
        margin-bottom: 10px;
    }

    h4 {
        font-size: 16px;
        color: #444;
        margin-bottom: 10px;
        margin-top:0px;
    }

    table.styled-table {
        width: 100%;
        border:none;
        margin-bottom: 20px;
    }

    table.styled-table td {
    font-size:16px;
    font-weight:600;
    color:#424242;
    height:49px;
    min-height:49px;
    border:none;
    line-height: 16px;
    padding-bottom:12px;
    }
    table.styled-table td span{
        font-weight:200px;
        color:#949494;
    }
    .shippingbox table.styled-table td{
        text-align:right;
    }
    .billingbox table.styled-table td{
        text-align:left;
    }

    .footer {
        text-align: center;
        font-size: 12px;
        color: #888;
        margin-top: 30px;
    }
</style>
    </head>
    <body>' . $html . '</body>
    </html>';
}

echo $html;
