// 3. JavaScript for Dashboard Functionality (qr-dashboard.js)
jQuery(document).ready(function($) {
    // Scanner modal
    // Desktop scanner functionality
    $('#ticket-scanner-btn').click(function() {
        if (isMobile()) {
            $('#scanner-modal').show();
            if (!scannerActive) {
                startScanner();
            }
        } else {
            const redirectUrl = window.location.href;
            window.open(
                qrScannerData.scannerPageUrl + '?redirect=' + encodeURIComponent(redirectUrl),
                '_blank'
            );
        }
    });

    function isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }
    
    // Close modals
    $('.close, .manual-cancel-button').click(function() {
        $(this).closest('.checkin-default-modal').hide();
    });
    
    // Scanner functionality
    let scannerActive = false;
    let videoStream = null;
    let scanningInterval = null;
    
    $('#start-scan').click(function() {
        $('.qr_scan_details').hide();
        $('#scanner-container').show();

        const url = new URL(window.location.href);
        url.searchParams.delete('qr_scan_done');
        window.history.replaceState({}, document.title, url.toString());

        if (!scannerActive) {
            startScanner();
        }
    });
    
    $('#stop-scan').click(function() {
        stopScanner();
    });
    
    function startScanner() {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(function(stream) {
            videoStream = stream;
            const video = document.getElementById('scanner-video');
            video.srcObject = stream;
            
            video.onloadedmetadata = function() {
                video.play();
                scannerActive = true;
                scanQRCode();
            };
        })
        .catch(handleCameraError);
    }

    function handleCameraError(error) {
        let errorMessage = 'Error accessing camera: ';
        
        switch(error.name) {
            case 'NotAllowedError':
                errorMessage += bm_normal_object.NotAllowedError;
                break;
            case 'NotFoundError':
                errorMessage += bm_normal_object.NotFoundError;
                break;
            case 'NotReadableError':
                errorMessage += bm_normal_object.NotReadableError;
                break;
            case 'OverconstrainedError':
                errorMessage += bm_normal_object.OverconstrainedError;
                break;
            case 'SecurityError':
                errorMessage += bm_normal_object.SecurityError;
                break;
            default:
                errorMessage += error.message;
        }
        
        $('#scanner-result').html('<p class="error">' + errorMessage + '</p>');
    }

    function stopScanner() {
        if (scanningInterval) {
            clearInterval(scanningInterval);
            scanningInterval = null;
        }
        
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        
        scannerActive = false;
        $('#scanner-result').html('');
    }

    function scanQRCode() {
        if (!scannerActive) return;
        
        const video = document.getElementById('scanner-video');
        
        if (video.videoWidth === 0 || video.videoHeight === 0) {
            setTimeout(scanQRCode, 100);
            return;
        }
        
        const canvas = document.getElementById('scanner-canvas');
        const context = canvas.getContext('2d');
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, canvas.width, canvas.height);
        
        if (code) {
            // Escape all dynamic content before inserting into DOM to prevent XSS.
            const safeData = $('<span>').text(code.data).html();
            const safeLabel = $('<span>').text(bm_normal_object.qr_code_detected).html();
            $('#scanner-result').html('<p>' + safeLabel + ': ' + safeData + '</p>');
            verifyQRCode(code.data);
            stopScanner();
        } else {
            requestAnimationFrame(scanQRCode);
        }
    }
    
    function verifyQRCode(qrData) {
        $.ajax({
            url: checkinRest.url + 'checkins/scan',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ booking_key: qrData }),
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
            success: function() {
                $('#scanner-container').hide();
                $('#scanner-result').append('<p class="success">' + $('<span>').text(bm_success_object.checked_in_successfully).html() + '</p>');
                var base = qrScannerData.scannerPageUrl || window.location.href.split('?')[0];
                var sep  = base.indexOf('?') >= 0 ? '&' : '?';
                window.location.href = base + sep + 'qr_scan_done=' + encodeURIComponent(qrData);
            },
            error: function(jqXHR) {
                var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
                $('#scanner-result').append('<p class="error">' + $('<span>').text(msg).html() + '</p>');
                setTimeout(startScanner, 3000);
            }
        });
    }
    
    $(document).on('click', '.view-details', function(e) {
        e.preventDefault();
        const bookingId = $(this).data('id');
        
        $.ajax({
            url: checkinRest.url + 'checkins/' + bookingId + '/details',
            method: 'GET',
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
            success: function(data) {
                $('#order-details-content').html(bmCheckinBuildDetailsCard(data));
                $('#order-details-modal').show();
            },
            error: function(jqXHR) {
                var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
                showMessage(msg, 'error');
            }
        });
    });
    
    $(document).on('change', '.checkin-status-dropdown', function() {
        const $select    = $(this);
        const checkinId  = $select.data('checkin-id');
        const newStatus  = $select.val();
        const statusColorClasses = [
            'bm-ci-badge--pending', 'bm-ci-badge--checked_in', 'bm-ci-badge--expired',
            'bm-ci-badge--no_show', 'bm-ci-badge--late', 'bm-ci-badge--early', 'bm-ci-badge--checked_out'
        ];

        if (newStatus) {
            // Update color class immediately for visual feedback.
            $select.removeClass( statusColorClasses.join(' ') );
            if (newStatus) {
                $select.addClass( 'bm-ci-badge--' + newStatus );
            }
            $.ajax({
                url: checkinRest.url + 'checkins/' + checkinId + '/status',
                method: 'PATCH',
                contentType: 'application/json',
                data: JSON.stringify({ status: newStatus }),
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
                success: function() {
                    showMessage(bm_success_object.status_successfully_changed, 'success');
                    window.location.reload();
                },
                error: function(jqXHR) {
                    var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
                    showMessage(msg, 'error');
                    window.location.reload();
                }
            });
        }
    });

    // Show/hide fields based on selection
    $('#manual_checkin_type').change(function() {
        $('#manual_checkin-error').html('');
        $('#manual_checkin-result').html('');
        $('.manual-cherckin-buttons').addClass('hidden');
        $('.checkin-input').addClass('hidden');
        $('.select-checkin-input').addClass('hidden');
        if ($(this).val() === 'last_name') {
            $('#manual_checkin_lastname').removeClass('hidden');
        } else if ($(this).val() === 'email') {
            $('#manual_checkin_email').removeClass('hidden');
        } else if ($(this).val() === 'service') {
            $('#manual_checkin_service').val([]).multiselect('reload');
            $('#manual_checkin_service_span').removeClass('hidden');
        } else {
            $('#manual_checkin_reference').removeClass('hidden');
        }
    });

    // Open modal
    $('#manual-checkin-btn').click(function() {
        $('#manual_checkin-result').html('');
        $('#manual_checkin-error').html('');
        $('.checkin-input').val('');
        $('#manual_checkin_service').val([]).multiselect('reload');
        $('.manual-cherckin-buttons').addClass('hidden');
        $('#manual_checkin_type').val('last_name').trigger('change');
        $('#manual_checkin-modal').show();
    });

    // Handle search
    $('#manual-checkin-search').click(function(e) {
        e.preventDefault();
        $('#manual_checkin-result').html('');
        $('#manual_checkin-error').html('');
        $('.manual-cherckin-buttons').addClass('hidden');

        let searchType = $('#manual_checkin_type').val();
        let searchValue = '';

        if (searchType === 'last_name') {
            searchValue = $('#manual_checkin_lastname').val().trim();
            if (!searchValue) {
                $('#manual_checkin-error').html(bm_normal_object.enter_last_name);
                return false;
            }
        } else if (searchType === 'email') {
            searchValue = $('#manual_checkin_email').val().trim();
            if (!searchValue) {
                $('#manual_checkin-error').html(bm_normal_object.enter_email);
                return false;
            }
            let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,6}$/i;
            if (!emailPattern.test(searchValue)) {
                $('#manual_checkin-error').html(bm_error_object.invalid_email);
                return false;
            }
        } else if (searchType === 'service') {
            searchValue = $('#manual_checkin_service').val();
            if (!searchValue) {
                $('#manual_checkin-error').html(bm_normal_object.select_a_service);
                return false;
            }
        } else {
            searchValue = $('#manual_checkin_reference').val().trim();
            if (!searchValue) {
                $('#manual_checkin-error').html(bm_normal_object.enter_reference_no);
                return false;
            }
        }

        $.ajax({
            url: checkinRest.url + 'checkins/search',
            method: 'GET',
            data: { search_type: searchType, search_value: searchType === 'service' ? (Array.isArray(searchValue) ? searchValue.join(',') : searchValue) : searchValue },
            beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
            success: function(data) {
                if (data.results && data.results.length > 0) {
                    $('#manual_checkin-result').html(bmCheckinBuildSearchTable(data.results, searchType));
                    jQuery('.manual_checkin_records_table').DataTable();
                    $('.manual-cherckin-buttons').removeClass('hidden');
                } else {
                    $('#manual_checkin-error').html(bm_normal_object.no_records || 'No bookings found');
                    $('.manual-cherckin-buttons').addClass('hidden');
                }
            },
            error: function(jqXHR) {
                var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
                $('#manual_checkin-error').html(msg);
                $('.manual-cherckin-buttons').addClass('hidden');
            }
        });
    });
});


function bm_checkin_manually() {
    let searchType = jQuery('#manual_checkin_type').val();
    let searchValue = '';
    let bookingIds = [];

    if (searchType === 'last_name') {
        searchValue = jQuery('#manual_checkin_lastname').val().trim();
        if (!searchValue) {
            jQuery('#manual_checkin-error').html(bm_normal_object.enter_last_name);
            return false;
        }
        jQuery('.bm-booking-select:checked').each(function () {
            bookingIds.push(jQuery(this).val());
        });

    } else if (searchType === 'email') {
        searchValue = jQuery('#manual_checkin_email').val().trim();
        if (!searchValue) {
            jQuery('#manual_checkin-error').html(bm_normal_object.enter_email);
            return false;
        }
        let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,6}$/i;
        if (!emailPattern.test(searchValue)) {
            jQuery('#manual_checkin-error').html(bm_normal_object.invalid_email);
            return false;
        }
        jQuery('.bm-booking-select:checked').each(function () {
            bookingIds.push(jQuery(this).val());
        });
    } else if (searchType === 'service') {
        searchValue = jQuery('#manual_checkin_service').val();
        if (!searchValue) {
            jQuery('#manual_checkin-error').html(bm_normal_object.select_a_service);
            return false;
        }
        jQuery('.bm-booking-select:checked').each(function () {
            bookingIds.push(jQuery(this).val());
        });
    } else {
        searchValue = jQuery('#manual_checkin_reference').val().trim();
        if (!searchValue) {
            jQuery('#manual_checkin-error').html(bm_normal_object.enter_reference_no);
            return false;
        }
    }

    if ((searchType === 'email' || searchType === 'last_name' || searchType === 'service') && bookingIds.length === 0) {
        jQuery('#manual_checkin-error').html(bm_normal_object.no_selection || 'Please select at least one booking.');
        return false;
    }

    jQuery('#resendProcess').removeClass('hidden');
    jQuery('#manual-checkin-button').prop('disabled', true);

    jQuery.ajax({
        url: checkinRest.url + 'checkins/bulk',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ booking_ids: bookingIds.map(function(id) { return parseInt(id, 10) || 0; }).filter(function(id) { return id > 0; }) }),
        beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
        success: function(response) {
            jQuery('#resendProcess').addClass('hidden');
            jQuery('.manual-cherckin-buttons').addClass('hidden');
            jQuery('#manual_checkin-result').html('<p class="success">' + jQuery('<span>').text(response.message).html() + '</p>');
            setTimeout(function() {
                jQuery('#manual_checkin-modal').hide();
                window.location.reload();
            }, 2000);
        },
        error: function(jqXHR) {
            jQuery('#resendProcess').addClass('hidden');
            jQuery('.manual-cherckin-buttons').addClass('hidden');
            var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
            jQuery('#manual_checkin-error').html(msg);
        }
    });
}


// Handle "Check All" toggle
jQuery(document).on('change', '#bm-checkall', function() {
    let checked = jQuery(this).is(':checked');
    jQuery('.bm-booking-select').prop('checked', checked);
});


// View details per booking (eye icon)
jQuery(document).on('click', '.bm-view-details', function(e) {
    e.preventDefault();
    jQuery('.checkin-order-details-container').html('');
    jQuery('#checkin-order-details-modal').addClass('active-modal');
    jQuery('#loader_modal').show();

    let bookingId = jQuery(this).data('id');
    if (!bookingId) return;

    jQuery.ajax({
        url: checkinRest.url + 'checkins/' + bookingId + '/details',
        method: 'GET',
        beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
        success: function(data) {
            jQuery('#loader_modal').hide();
            jQuery('.checkin-order-details-container').html(bmCheckinBuildDetailsCard(data));
        },
        error: function(jqXHR) {
            jQuery('#loader_modal').hide();
            var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
            jQuery('.checkin-order-details-container').html(jQuery('<span>').text(msg).html());
        }
    });
});

jQuery(document).ready(function($) {
    let cropper;
    const $modal = $("#qr-cropper-modal");
    const $modalBox = $("#qr-modal-box");
    const $modalHeader = $("#qr-modal-header");
    const $resizer = $(".qr-resizer");
    const $closeBtn = $(".qr-modal-close");
    const $spinner = $("#qr-loading-spinner");
    const $cropperImg = $("#cropper-image");
    const $confirmBtn = $("#crop-confirm");

    // Configure pdf.js worker source so PDF rendering works on the admin page.
    if (typeof pdfjsLib !== 'undefined' && typeof bm_pdf_settings !== 'undefined' && bm_pdf_settings.workerSrc) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = bm_pdf_settings.workerSrc;
    }

    let isDragging = false, dragOffsetX = 0, dragOffsetY = 0;

    $modalHeader.on("mousedown", function(e) {
        isDragging = true;
        dragOffsetX = e.clientX - $modalBox.offset().left;
        dragOffsetY = e.clientY - $modalBox.offset().top;
    });

    $(document).on("mousemove", function(e) {
        if (isDragging) {
            $modalBox.css({
                left: (e.clientX - dragOffsetX) + "px",
                top: (e.clientY - dragOffsetY) + "px",
                transform: "none"
            });
        }
    }).on("mouseup", function() {
        isDragging = false;
    });

    let isResizing = false;

    $resizer.on("mousedown", function(e) {
        isResizing = true;
        e.preventDefault();
    });

    $(document).on("mousemove", function(e) {
        if (isResizing) {
            $modalBox.css({
                width: (e.clientX - $modalBox.offset().left) + "px",
                height: (e.clientY - $modalBox.offset().top) + "px"
            });

            if (cropper) {
                const cropData = cropper.getData();
                const cropBoxData = cropper.getCropBoxData();

                cropper.destroy();
                cropper = new Cropper($cropperImg[0], {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    responsive: true,
                    ready() {
                        this.setData(cropData);
                        this.setCropBoxData(cropBoxData);
                    }
                });
            }
        }
    }).on("mouseup", function() {
        isResizing = false;
    });

    function openCropperModal(imageSrc) {
        $modal.show();
        $spinner.show();
        $cropperImg.hide();
        $confirmBtn.hide();
        
        $cropperImg.off("load").one("load", function() {
            $spinner.hide();
            $cropperImg.show();
            $confirmBtn.show();

            if (cropper) cropper.destroy();
            cropper = new Cropper($cropperImg[0], {
                aspectRatio: 1,
                viewMode: 1
            });
        });

        $cropperImg.attr("src", imageSrc);
    }

    $closeBtn.on("click", function() {
        $modal.hide();
        if (cropper) cropper.destroy();
    });

    $(window).on("click", function(event) {
        if ($(event.target).attr("id") === "qr-cropper-modal") {
            $modal.hide();
            if (cropper) cropper.destroy();
        }
    });

    $("#upload-qr").on("click", function() {
        $(".qr_scan_details").hide();
        $("#scanner-container").show();
        $("#qr-file-input").click();
    });

    $("#qr-file-input").on("change", function(event) {
        let file = event.target.files[0];
        if (!file) return;

        if (file.type === "application/pdf") {
            if (typeof pdfjsLib === 'undefined') {
                $("#scanner-result").html("<p class='error'>" + $('<span>').text(
                    (typeof bm_pdf_settings !== 'undefined' && bm_pdf_settings.pdfjs_missing)
                        ? bm_pdf_settings.pdfjs_missing
                        : 'PDF library not loaded. Please refresh the page.'
                ).html() + "</p>");
                return;
            }
            let fileReader = new FileReader();
            fileReader.onload = function() {
                let typedarray = new Uint8Array(this.result);
                pdfjsLib.getDocument(typedarray).promise.then(function(pdf) {
                    pdf.getPage(1).then(function(page) {
                        let viewport = page.getViewport({ scale: 2 });
                        let canvas = document.createElement("canvas");
                        let ctx = canvas.getContext("2d");
                        canvas.height = viewport.height;
                        canvas.width = viewport.width;

                        page.render({ canvasContext: ctx, viewport: viewport }).promise.then(function() {
                            openCropperModal(canvas.toDataURL("image/png"));
                        });
                    });
                }).catch(function(err) {
                    $("#scanner-result").html("<p class='error'>" + $('<span>').text('Failed to read PDF: ' + (err.message || '')).html() + "</p>");
                });
            };
            fileReader.readAsArrayBuffer(file);
            return;
        }

        let reader = new FileReader();
        reader.onload = function(e) {
            openCropperModal(e.target.result);
        };
        reader.readAsDataURL(file);
    });

    $confirmBtn.on("click", function() {
        let canvas = cropper.getCroppedCanvas();
        let ctx = canvas.getContext("2d");
        let imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);

        let code = jsQR(imageData.data, imageData.width, imageData.height);
        if (code) {
            let bookingRef = code.data;
            // Escape bookingRef as text to prevent XSS from malicious QR codes.
            let safeRef = $('<span>').text(bookingRef).html();
            let safeLabel = $('<span>').text(bm_normal_object.qr_code_detected).html();
            $("#scanner-result").html("<p>" + safeLabel + ": " + safeRef + "</p>");

            $.ajax({
                url: checkinRest.url + 'checkins/scan',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ booking_key: bookingRef }),
                beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
                success: function(response) {
                    $("#scanner-result").append("<p class='success'>" + $('<span>').text(response.message).html() + "</p>");
                    var base = qrScannerData.scannerPageUrl || window.location.href.split('?')[0];
                    var sep  = base.indexOf('?') >= 0 ? '&' : '?';
                    window.location.href = base + sep + 'qr_scan_done=' + encodeURIComponent(bookingRef);
                    $("#scanner-container").hide();
                },
                error: function(jqXHR) {
                    var msg = (jqXHR.responseJSON || {}).message || '';
                    $("#scanner-result").append("<p class='error'>" + $('<span>').text(msg).html() + "</p>");
                }
            });
        } else {
            $("#scanner-result").html("<p class='error'>" + $('<span>').text(bm_error_object.no_qr_code_found).html() + "</p>");
        }

        $modal.hide();
        if (cropper) cropper.destroy();
    });
});

// -----------------------------------------------------------------------
// Undo check-in handler (bm_checkin_undo)
// -----------------------------------------------------------------------
jQuery(document).on('click', '.bm-undo-checkin', function (e) {
    e.preventDefault();
    var bookingId = jQuery(this).data('booking-id');
    if (!bookingId) return;

    jQuery.ajax({
        url: checkinRest.url + 'checkins/' + bookingId + '/undo',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({}),
        beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
        success: function(response) {
            showMessage(response.message, 'success');
            setTimeout(function () { window.location.reload(); }, 1500);
        },
        error: function(jqXHR) {
            var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
            showMessage(msg, 'error');
        }
    });
});

// -----------------------------------------------------------------------
// No-show handler (bm_checkin_no_show)
// -----------------------------------------------------------------------
jQuery(document).on('click', '.bm-mark-no-show', function (e) {
    e.preventDefault();
    var bookingId = jQuery(this).data('booking-id');
    if (!bookingId) return;

    jQuery.ajax({
        url: checkinRest.url + 'checkins/' + bookingId + '/no-show',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({}),
        beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
        success: function(response) {
            showMessage(response.message, 'success');
            setTimeout(function () { window.location.reload(); }, 1500);
        },
        error: function(jqXHR) {
            var msg = (jqXHR.responseJSON || {}).message || bm_error_object.server_error;
            showMessage(msg, 'error');
        }
    });
});

// -----------------------------------------------------------------------
// Status counter refresh
// -----------------------------------------------------------------------
function bm_refresh_checkin_counter() {
    jQuery.ajax({
        url: checkinRest.url + 'checkins/stats',
        method: 'GET',
        beforeSend: function(xhr) { xhr.setRequestHeader('X-WP-Nonce', checkinRest.nonce); },
        success: function(counts) {
            jQuery('#bm-ci-count-total').text(counts.total || 0);
            jQuery('#bm-ci-count-checked_in').text(counts.checked_in || 0);
            jQuery('#bm-ci-count-pending').text(counts.pending || 0);
            jQuery('#bm-ci-count-expired').text(counts.expired || 0);
            jQuery('#bm-ci-count-no_show').text(counts.no_show || 0);
            jQuery('#bm-ci-count-late').text(counts.late || 0);
            jQuery('#bm-ci-count-early').text(counts.early || 0);
            jQuery('#bm-ci-count-checked_out').text(counts.checked_out || 0);
        }
    });
}

// -----------------------------------------------------------------------
// REST helper: build a booking details card from JSON
// Used by .view-details and .bm-view-details to replace the AJAX HTML response.
// -----------------------------------------------------------------------
function bmCheckinBuildDetailsCard(data) {
    var esc = function(v) { return jQuery('<span>').text(v || '').html(); };
    var n   = bm_normal_object || {};
    return '<table class="widefat striped bm-checkin-details-card">' +
        '<tr><th>' + esc(n.booking || 'Booking Key') + '</th><td>' + esc(data.booking_key) + '</td></tr>' +
        '<tr><th>' + esc(n.first_name || 'First Name') + '</th><td>' + esc(data.first_name) + '</td></tr>' +
        '<tr><th>' + esc(n.last_name  || 'Last Name')  + '</th><td>' + esc(data.last_name)  + '</td></tr>' +
        '<tr><th>' + esc(n.email      || 'Email')      + '</th><td>' + esc(data.email)       + '</td></tr>' +
        '<tr><th>' + esc(n.phone      || 'Phone')      + '</th><td>' + esc(data.contact_no)  + '</td></tr>' +
        '<tr><th>' + esc(n.service    || 'Service')    + '</th><td>' + esc(data.service_name) + '</td></tr>' +
        '<tr><th>' + esc(n.booking_date || 'Date')     + '</th><td>' + esc(data.booking_date) + '</td></tr>' +
        '<tr><th>' + esc(n.order_status || 'Order Status') + '</th><td>' + esc(data.order_status) + '</td></tr>' +
        '<tr><th>' + esc(n.checkin_status || 'Check-in Status') + '</th><td>' + esc(data.checkin_status) + '</td></tr>' +
        '<tr><th>' + esc(n.checkin_time   || 'Check-in Time')   + '</th><td>' + esc(data.checkin_time)   + '</td></tr>' +
    '</table>';
}

// -----------------------------------------------------------------------
// REST helper: build search results table from JSON
// Used by #manual-checkin-search to replace the AJAX HTML response.
// -----------------------------------------------------------------------
function bmCheckinBuildSearchTable(results, searchType) {
    var esc = function(v) { return jQuery('<span>').text(v || '').html(); };
    var n   = bm_normal_object || {};

    var thead = '<thead><tr>' +
        '<th><input type="checkbox" id="bm-checkall"></th>' +
        '<th>' + esc(n.booking   || 'Booking Key')  + '</th>' +
        '<th>' + esc(n.service   || 'Service Name') + '</th>';
    if (searchType === 'email') {
        thead += '<th>' + esc(n.email || 'Email') + '</th>';
    } else {
        thead += '<th>' + esc(n.first_name || 'First Name') + '</th>' +
                 '<th>' + esc(n.last_name  || 'Last Name')  + '</th>';
    }
    thead += '<th>' + esc(n.svc_participants  || 'Service Participants')       + '</th>' +
             '<th>' + esc(n.ex_participants   || 'Extra Service Participants') + '</th>' +
             '<th>' + esc(n.checkin_status    || 'Check-in Status')            + '</th>' +
             '<th>' + esc(n.checkin_time      || 'Check-in Date')              + '</th>' +
             '<th>' + esc(n.edit             || 'Actions')                     + '</th>' +
             '</tr></thead>';

    var tbody = '<tbody>';
    jQuery.each(results, function(i, row) {
        var statusLabel = row.checkin_label || (row.checkin_status ? row.checkin_status : 'Pending');
        var checkinDate = row.checkin_time || '-';
        tbody += '<tr>' +
            '<td><input type="checkbox" class="bm-booking-select" value="' + esc(row.id) + '"></td>' +
            '<td>' + esc(row.booking_key)  + '</td>' +
            '<td>' + esc(row.service_name) + '</td>';
        if (searchType === 'email') {
            tbody += '<td>' + esc(row.email_address) + '</td>';
        } else {
            tbody += '<td>' + esc(row.first_name) + '</td><td>' + esc(row.last_name) + '</td>';
        }
        tbody += '<td>' + esc(row.svc_participants) + '</td>' +
            '<td>' + esc(row.ex_participants)  + '</td>' +
            '<td>' + esc(statusLabel)          + '</td>' +
            '<td>' + esc(checkinDate)          + '</td>' +
            '<td><div class="bm-view-details" data-id="' + esc(row.id) + '" style="cursor:pointer;">' +
                '<i class="fa fa-eye"></i> ' + esc(n.edit || 'View') +
            '</div></td>' +
            '</tr>';
    });
    tbody += '</tbody>';

    return '<div class="bm-bookings-list">' +
        '<table class="manual_checkin_records_table widefat striped">' +
        thead + tbody +
        '</table></div>';
}
