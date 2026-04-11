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
            $('#scanner-result').html('<p>QR Code detected: ' + code.data + '</p>');
            verifyQRCode(code.data);
            stopScanner();
        } else {
            requestAnimationFrame(scanQRCode);
        }
    }
    
    function verifyQRCode(qrData) {
        $.post(bm_ajax_object.ajax_url, {
            action: 'verify_qr_code',
            qr_data: qrData,
            nonce: bm_ajax_object.nonce
        }, function(response) {
            if (response.success) {
                $('#scanner-container').hide();
                $('#scanner-result').append(`<p class="success">${bm_success_object.checked_in_successfully}</p>`);
                const url = new URL(document.referrer || window.location.href); 
                url.searchParams.set('qr_scan_done', qrData); 

                window.location.href = url.toString();
            } else {
                $('#scanner-result').append(`<p class="error">${response.data ? response.data : bm_error_object.server_error}</p>`);
                setTimeout(startScanner, 3000);
            }
        });
    }
    
    $(document).on('click', '.view-details', function(e) {
        e.preventDefault();
        const bookingId = $(this).data('id');
        
        $.post(bm_ajax_object.ajax_url, {
            action: 'get_order_details',
            booking_id: bookingId,
            nonce: bm_ajax_object.nonce
        }, function(response) {
            if (response.success) {
                $('#order-details-content').html(response.data);
                $('#order-details-modal').show();
            } else {
				showMessage(response.data ? response.data : bm_error_object.server_error, 'error');
            }
        });
    });
    
    $(document).on('change', '.checkin-status-dropdown', function() {
        const checkinId = $(this).data('checkin-id');
        const booking_id = $(this).data('booking-id');
        const newStatus = $(this).val();
        
        if (newStatus) {
            $.post(bm_ajax_object.ajax_url, {
                action: 'update_checkin_status',
                booking_id: booking_id,
                checkin_id: checkinId,
                new_status: newStatus,
                nonce: bm_ajax_object.nonce
            }, function(response) {
                if (response.success) {
                    showMessage(bm_success_object.status_successfully_changed, 'success');
                    window.location.reload();
                } else {
                    showMessage(response.data ? response.data : bm_error_object.server_error, 'error');
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

        $.post(bm_ajax_object.ajax_url, {
            action: 'manual_checkin_check',
            search_type: searchType,
            search_value: searchValue,
            nonce: bm_ajax_object.nonce
        }, function(response) {
            if (response.success) {
                $('#manual_checkin-result').html(response.data);
                jQuery('.manual_checkin_records_table').DataTable();
                $('.manual-cherckin-buttons').removeClass('hidden');
            } else {
                $('#manual_checkin-error').html(response.data ? response.data : bm_error_object.server_error);
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

    jQuery.post(bm_ajax_object.ajax_url, {
        action: 'manual_checkin_process',
        search_type: searchType,
        search_value: searchValue,
        booking_ids: bookingIds,
        nonce: bm_ajax_object.nonce
    }, function(response) {
        jQuery('#resendProcess').addClass('hidden');
        jQuery('.manual-cherckin-buttons').addClass('hidden');

        if (response.success) {
            jQuery('#manual_checkin-result').html('<p class="success">' + response.data.message + '</p>');
            setTimeout(function() {
                jQuery('#manual_checkin-modal').hide();
                window.location.reload();
            }, 2000);
        } else {
            jQuery('#manual_checkin-error').html(response.data ? response.data : bm_error_object.server_error);
        }
    }).fail(function() {
        jQuery('#resendProcess').addClass('hidden');
        jQuery('.manual-cherckin-buttons').addClass('hidden');
        jQuery('#manual_checkin-error').html(bm_error_object.server_error);
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

    jQuery.post(bm_ajax_object.ajax_url, {
        action: 'manual_checkin_view_details',
        booking_id: bookingId,
        nonce: bm_ajax_object.nonce
    }, function(response) {
        jQuery('#loader_modal').hide();
        if (response.success) {
            jQuery('.checkin-order-details-container').html(response.data);
        } else {
            jQuery('.checkin-order-details-container').html(response.data ? response.data : bm_error_object.server_error);
        }
    }).fail(function() {
        jQuery('#loader_modal').hide();
        jQuery('.checkin-order-details-container').html(bm_error_object.server_error);
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
            console.log("Image loaded into cropper");
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
            $("#scanner-result").html("<p>" + bm_normal_object.qr_code_detected + ": " + bookingRef + "</p>");

            $.post(bm_ajax_object.ajax_url, {
                action: "qr_checkin_process",
                nonce: bm_ajax_object.nonce,
                booking_reference: bookingRef
            }, function(response) {
                if (response.success) {
                    $("#scanner-result").append("<p class='success'>" + response.data.message + "</p>");

                    $("#scanner-result").append("<p class='success'>Redirecting...</p>");
                    window.location.href = qrScannerData.scannerPageUrl + "?qr_scan_done=" + encodeURIComponent(bookingRef);

                    $("#scanner-container").hide();
                } else {
                    $("#scanner-result").append("<p class='error'>" + response.data + "</p>");
                }
            });
        } else {
            $("#scanner-result").html("<p class='error'>" + bm_error_object.no_qr_code_found + "</p>");
        }

        $modal.hide();
        if (cropper) cropper.destroy();
    });
});
