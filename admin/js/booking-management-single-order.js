jQuery(document).ready(function($) {
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('booking_id');
    
    $('.tab').on('click', function() {
        const tab = $(this);
        const tabType = tab.data('tab');
        
        if (tab.hasClass('active')) return;
        
        const productsTable = $('.products-table-container');
        productsTable.html('<div class="tab-loading"><div class="spinner"></div><p>' + bm_normal_object.loading + '...</p></div>');

        $('.tab').removeClass('active');
        tab.addClass('active');

        switch(tabType) {
            case 'product-info':
                loadProductsTable(bookingId, productsTable);
                break;
            case 'payment-details':
                loadPaymentDetails(bookingId, productsTable);
                break;
            case 'email':
                loadEmailInfo(bookingId, productsTable);
                break;
            default:
                loadProductsTable(bookingId, productsTable);
        }
    });
    
    // Load personal information tab content
    function loadPersonalInfo(bookingId, container) {
        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_get_order_personal_info',
                booking_id: bookingId,
                nonce: bm_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    container.html(`
                        <div class="personal-info-content">
                            <div class="info-section">
                                <h4>${bm_normal_object.loading}</h4>
                                <div class="info-grid">
                                    <div class="info-row">
                                        <span class="info-label">Name:</span>
                                        <span class="info-value">${response.data.billing_name}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Address:</span>
                                        <span class="info-value">${response.data.billing_address}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">City:</span>
                                        <span class="info-value">${response.data.billing_city}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Country:</span>
                                        <span class="info-value">${response.data.billing_country}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Phone:</span>
                                        <span class="info-value">${response.data.billing_phone}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="info-section">
                                <h4>Additional Information</h4>
                                <div class="info-grid">
                                    <div class="info-row">
                                        <span class="info-label">Customer Since:</span>
                                        <span class="info-value">${response.data.customer_since}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Total Orders:</span>
                                        <span class="info-value">${response.data.total_orders}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Notes:</span>
                                        <span class="info-value">${response.data.notes || 'No notes available'}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    container.html('<div class="error-message">' + (response.data || 'Failed to load personal information') + '</div>');
                }
            },
            error: function() {
                container.html('<div class="error-message">Error loading personal information</div>');
            }
        });
    }
    
    // Load payment details tab content
    function loadPaymentDetails(bookingId, container) {
        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_get_order_payment_details',
                booking_id: bookingId,
                nonce: bm_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    container.html(`
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Transaction ID</th>
                                    <th>Amount</th>
                                    <th>Payment Status</th>
                                    <th>Payment Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>${response.data.payment_method}</td>
                                    <td>${response.data.transaction_id}</td>
                                    <td>${response.data.amount}</td>
                                    <td>${response.data.status}</td>
                                    <td>${response.data.date}</td>
                                </tr>
                            </tbody>
                        </table>
                    `);
                } else {
                    container.html('<div class="error-message">' + (response.data || 'Failed to load payment details') + '</div>');
                }
            },
            error: function() {
                container.html('<div class="error-message">Error loading payment details</div>');
            }
        });
    }
    
    // Load email information tab content
    function loadEmailInfo(bookingId, container) {
        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_get_order_email_info',
                booking_id: bookingId,
                nonce: bm_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    if (response.data.emails.length > 0) {
                        // Build email table
                        let html = `
                            <table class="products-table">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Date Sent</th>
                                        <th>Recipient</th>
                                        <th>View Mail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${response.data.emails.map(email => `
                                        <tr>
                                            <td>${email.subject}</td>
                                            <td>${email.date}</td>
                                            <td>${email.recipient}</td>
                                            <td>
                                                <div class="view-email-btn" onclick="bm_show_email_body(this)" id="${email.id}" title="View Email">
                                                    <i class="fa fa-eye"></i>
                                                </div>
                                            </td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        `;
                        container.html(html);
                    } else {
                        const btnHtml = `
                            <p>${bm_normal_object.no_mails_sent}</p>
                            <div class="resend-email-section">
                                <button type="button" class="button button-primary" id="resend-email-btn" data-booking-id="${bookingId}">
                                    ${bm_normal_object.resend_regenerate_mail}
                                </button>
                                <span class="resend-message"></span>
                            </div>
                        `;
                        container.html(btnHtml);
                        $('#resend-email-btn').on('click', function() {
                            resendOrderEmail(bookingId);
                        });
                    }
                } else {
                    container.html('<div class="error-message">' + (response.data || bm_normal_object.failed_mail_load) + '</div>');
                }
            },
            error: function() {
                container.html('<div class="error-message">' + bm_normal_object.error_mail_load + '</div>');
            }
        });
    }

    function resendOrderEmail(bookingId) {
        const $btn = $('#resend-email-btn');
        const $msg = $('.resend-message');

        $btn.prop('disabled', true).text('Sending...');
        $msg.text('').removeClass('success error');

        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_resend_order_email',
                booking_id: bookingId,
                nonce: bm_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    $msg.text(bm_normal_object.email_sent_success).addClass('success');
                    setTimeout(function() {
                        loadEmailInfo(bookingId, $('.products-table-container'));
                    }, 1500);
                } else {
                    $msg.text(response.data || 'Failed to send email.').addClass('error');
                    $btn.prop('disabled', false).text(bm_normal_object.resend_regenerate_mail);
                }
            },
            error: function() {
                $msg.text('Error sending email. Please try again.').addClass('error');
                $btn.prop('disabled', false).text(bm_normal_object.resend_regenerate_mail);
            }
        });
    }
    
    // Load default products table
    function loadProductsTable(bookingId, container) {
        $.ajax({
            url: bm_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'bm_get_order_products',
                booking_id: bookingId,
                nonce: bm_ajax_object.nonce
            },
            success: function(response) {
                if (response.success) {
                    container.html(`
                        <table class="products-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Total Quantity</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${response.data.products.map(product => `
                                    <tr>
                                        <td>${product.product}</td>
                                        <td>${product.total_quantity}</td>
                                        <td>${product.revenue}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `);
                } else {
                    container.html('<div class="error-message">' + (response.data || 'Failed to load products') + '</div>');
                }
            },
            error: function() {
                container.html('<div class="error-message">Error loading products</div>');
            }
        });
    }
    
    $('.tab.active').trigger('click');
});