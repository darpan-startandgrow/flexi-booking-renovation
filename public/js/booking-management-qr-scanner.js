/**
 * Frontend QR Scanner — public/js/booking-management-qr-scanner.js
 *
 * Handles the [sgbm_qr_scanner] shortcode page for customer self-service check-in:
 *   - Camera scan (tab 1)
 *   - Upload QR image (tab 2)
 *   - Upload booking PDF + crop QR region (tab 3)
 *
 * Depends on: jquery, public-jsqr (jsQR), pdfjs-main, jquery-cropper
 * Localised objects: bm_qr_scanner_obj  (ajax_url, nonce, scannerPageUrl, workerSrc, strings)
 */
/* global jQuery, jsQR, pdfjsLib, Cropper */
(function ($) {
    'use strict';

    // -----------------------------------------------------------------------
    // Guard: abort if the scanner container is not present on this page.
    // -----------------------------------------------------------------------
    if (!document.getElementById('bm-qrs-container')) {
        return;
    }

    // Aliases to localised data (populated via wp_localize_script).
    var S = window.bm_qr_scanner_obj || {};
    var ajaxUrl    = S.ajax_url    || '';
    var nonce      = S.nonce       || '';
    var scannerUrl = S.scannerPageUrl || window.location.href.split('?')[0];
    var workerSrc  = S.workerSrc   || '';
    var str        = S.strings     || {};

    // -----------------------------------------------------------------------
    // Set PDF.js worker src once so getDocument() works correctly.
    // -----------------------------------------------------------------------
    if (typeof pdfjsLib !== 'undefined' && workerSrc) {
        pdfjsLib.GlobalWorkerOptions.workerSrc = workerSrc;
    }

    // -----------------------------------------------------------------------
    // State
    // -----------------------------------------------------------------------
    var videoStream      = null;
    var scannerActive    = false;
    var rafHandle        = null;
    var cropper          = null;
    var isDragging       = false;
    var isResizing       = false;
    var dragOffX         = 0;
    var dragOffY         = 0;

    // -----------------------------------------------------------------------
    // DOM references
    // -----------------------------------------------------------------------
    var $container     = $('#bm-qrs-container');
    var $tabs          = $container.find('.bm-qrs-tab');
    var $tabPanels     = $container.find('.bm-qrs-panel');
    var $result        = $container.find('#bm-qrs-result');

    // Camera tab
    var $video         = $container.find('#bm-qrs-video')[0];
    var $canvas        = $container.find('#bm-qrs-canvas')[0];
    var $startBtn      = $container.find('#bm-qrs-start');
    var $stopBtn       = $container.find('#bm-qrs-stop');
    var $scanLine      = $container.find('#bm-qrs-scanline');

    // Upload tab
    var $fileInput     = $container.find('#bm-qrs-file');
    var $uploadBtn     = $container.find('#bm-qrs-upload-btn');

    // PDF tab
    var $pdfInput      = $container.find('#bm-qrs-pdf-file');
    var $pdfUploadBtn  = $container.find('#bm-qrs-pdf-upload-btn');

    // Cropper modal
    var $modal         = $container.find('#bm-qrs-crop-modal');
    var $modalBox      = $container.find('#bm-qrs-crop-box');
    var $modalHeader   = $container.find('#bm-qrs-crop-header');
    var $modalClose    = $container.find('.bm-qrs-crop-close');
    var $cropImg       = $container.find('#bm-qrs-crop-img');
    var $cropSpinner   = $container.find('#bm-qrs-crop-spinner');
    var $cropConfirm   = $container.find('#bm-qrs-crop-confirm');
    var $resizer       = $container.find('.bm-qrs-resizer');

    // -----------------------------------------------------------------------
    // Tab switching
    // -----------------------------------------------------------------------
    $tabs.on('click', function () {
        var target = $(this).data('tab');
        $tabs.removeClass('bm-qrs-tab--active');
        $(this).addClass('bm-qrs-tab--active');
        $tabPanels.hide();
        $container.find('#bm-qrs-panel-' + target).show();

        if (target !== 'camera') {
            stopCamera();
        }
        clearResult();
    });

    // -----------------------------------------------------------------------
    // Camera scanning
    // -----------------------------------------------------------------------
    $startBtn.on('click', function () {
        clearResult();
        if (!scannerActive) {
            startCamera();
        }
    });

    $stopBtn.on('click', function () {
        stopCamera();
    });

    function startCamera() {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            showResult(str.camera_not_supported || 'Camera not supported on this browser.', 'error');
            return;
        }

        navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
            .then(function (stream) {
                videoStream   = stream;
                $video.srcObject = stream;
                $video.onloadedmetadata = function () {
                    $video.play();
                    scannerActive = true;
                    $startBtn.hide();
                    $stopBtn.show();
                    $scanLine.show();
                    scanFrame();
                };
            })
            .catch(function (err) {
                handleCameraError(err);
            });
    }

    function stopCamera() {
        scannerActive = false;
        if (rafHandle) {
            cancelAnimationFrame(rafHandle);
            rafHandle = null;
        }
        if (videoStream) {
            videoStream.getTracks().forEach(function (t) { t.stop(); });
            videoStream = null;
        }
        $video.srcObject = null;
        $startBtn.show();
        $stopBtn.hide();
        $scanLine.hide();
    }

    function scanFrame() {
        if (!scannerActive) { return; }

        if (!$video.videoWidth || !$video.videoHeight) {
            rafHandle = requestAnimationFrame(scanFrame);
            return;
        }

        var canvas  = $canvas;
        var ctx     = canvas.getContext('2d');
        canvas.width  = $video.videoWidth;
        canvas.height = $video.videoHeight;
        ctx.drawImage($video, 0, 0, canvas.width, canvas.height);

        var imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        var code    = jsQR(imgData.data, canvas.width, canvas.height);

        if (code && code.data) {
            stopCamera();
            submitCheckin(code.data);
        } else {
            rafHandle = requestAnimationFrame(scanFrame);
        }
    }

    function handleCameraError(err) {
        var msgs = {
            NotAllowedError:       str.NotAllowedError       || 'Camera access denied.',
            NotFoundError:         str.NotFoundError         || 'No camera found.',
            NotReadableError:      str.NotReadableError      || 'Camera is in use by another application.',
            OverconstrainedError:  str.OverconstrainedError  || 'Camera constraints not supported.',
            SecurityError:         str.SecurityError         || 'Camera blocked by browser security settings.'
        };
        showResult((msgs[err.name] || err.message), 'error');
    }

    // -----------------------------------------------------------------------
    // File upload (image only)
    // -----------------------------------------------------------------------
    $uploadBtn.on('click', function () {
        clearResult();
        $fileInput.val('').trigger('click');
    });

    $fileInput.on('change', function (e) {
        var file = e.target.files[0];
        if (!file) { return; }

        var reader = new FileReader();
        reader.onload = function (evt) {
            openCropModal(evt.target.result);
        };
        reader.readAsDataURL(file);
    });

    // -----------------------------------------------------------------------
    // PDF upload → render first page → open crop modal
    // -----------------------------------------------------------------------
    $pdfUploadBtn.on('click', function () {
        clearResult();
        $pdfInput.val('').trigger('click');
    });

    $pdfInput.on('change', function (e) {
        var file = e.target.files[0];
        if (!file) { return; }

        if (typeof pdfjsLib === 'undefined') {
            showResult(str.pdfjs_missing || 'PDF library not loaded. Please refresh the page.', 'error');
            return;
        }

        var reader = new FileReader();
        reader.onload = function () {
            var typed = new Uint8Array(this.result);
            pdfjsLib.getDocument({ data: typed }).promise
                .then(function (pdf) {
                    return pdf.getPage(1);
                })
                .then(function (page) {
                    var vp     = page.getViewport({ scale: 2 });
                    var cv     = document.createElement('canvas');
                    var cx     = cv.getContext('2d');
                    cv.width   = vp.width;
                    cv.height  = vp.height;
                    return page.render({ canvasContext: cx, viewport: vp }).promise
                        .then(function () {
                            openCropModal(cv.toDataURL('image/png'));
                        });
                })
                .catch(function (err) {
                    showResult((str.pdf_render_error || 'Could not render PDF.') + ' ' + err.message, 'error');
                });
        };
        reader.readAsArrayBuffer(file);
    });

    // -----------------------------------------------------------------------
    // Crop modal
    // -----------------------------------------------------------------------
    function openCropModal(src) {
        $modal.show();
        $cropSpinner.show();
        $cropImg.hide();
        $cropConfirm.hide();

        $cropImg.off('load').one('load', function () {
            $cropSpinner.hide();
            $cropImg.show();
            $cropConfirm.show();

            if (cropper) { cropper.destroy(); }
            cropper = new Cropper($cropImg[0], {
                aspectRatio: 1,
                viewMode:    1,
                responsive:  true
            });
        });

        $cropImg.attr('src', src);
    }

    function closeCropModal() {
        $modal.hide();
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }

    $modalClose.on('click', function () { closeCropModal(); });

    // Click outside modal-box closes it.
    $modal.on('click', function (e) {
        if ($(e.target).is($modal)) { closeCropModal(); }
    });

    // Keyboard: Escape closes modal.
    $(document).on('keydown.bmqrs', function (e) {
        if (e.key === 'Escape' && $modal.is(':visible')) { closeCropModal(); }
    });

    // Confirm crop → decode → submit.
    $cropConfirm.on('click', function () {
        if (!cropper) { return; }

        var cv      = cropper.getCroppedCanvas();
        var ctx     = cv.getContext('2d');
        var imgData = ctx.getImageData(0, 0, cv.width, cv.height);
        var code    = jsQR(imgData.data, imgData.width, imgData.height);

        closeCropModal();

        if (code && code.data) {
            submitCheckin(code.data);
        } else {
            showResult(str.no_qr_code_found || 'No QR code found in the selected area.', 'error');
        }
    });

    // -----------------------------------------------------------------------
    // Drag-to-move the modal box
    // -----------------------------------------------------------------------
    $modalHeader.on('mousedown', function (e) {
        isDragging = true;
        dragOffX   = e.clientX - $modalBox.offset().left;
        dragOffY   = e.clientY - $modalBox.offset().top;
    });

    $(document).on('mousemove.bmqrs', function (e) {
        if (isDragging) {
            $modalBox.css({ left: (e.clientX - dragOffX) + 'px', top: (e.clientY - dragOffY) + 'px', transform: 'none' });
        }
        if (isResizing) {
            $modalBox.css({
                width:  Math.max(300, e.clientX - $modalBox.offset().left) + 'px',
                height: Math.max(300, e.clientY - $modalBox.offset().top)  + 'px'
            });
            if (cropper) {
                // Refresh cropper after resize.
                var d = cropper.getData();
                cropper.destroy();
                cropper = new Cropper($cropImg[0], { aspectRatio: 1, viewMode: 1, responsive: true,
                    ready: function () { this.setData(d); }
                });
            }
        }
    }).on('mouseup.bmqrs', function () {
        isDragging = false;
        isResizing = false;
    });

    $resizer.on('mousedown', function (e) {
        isResizing = true;
        e.preventDefault();
    });

    // -----------------------------------------------------------------------
    // AJAX check-in submission
    // -----------------------------------------------------------------------
    function submitCheckin(bookingRef) {
        showResult(str.checking_in || 'Checking in…', 'info');
        $container.find('.bm-qrs-spinner').show();

        $.post(ajaxUrl, {
            action:            'bm_qr_scanner_checkin',
            booking_reference: bookingRef,
            nonce:             nonce
        }, function (response) {
            $container.find('.bm-qrs-spinner').hide();
            if (response.success) {
                var token = response.data && response.data.confirm_token ? response.data.confirm_token : '';
                showCheckinSuccess(response.data.message || str.checked_in_successfully || 'Checked in successfully.');
                if (token) {
                    // Redirect to confirmation page with a one-time token (does NOT expose booking key).
                    window.location.href = scannerUrl + (scannerUrl.indexOf('?') >= 0 ? '&' : '?') + 'bm_ci_token=' + encodeURIComponent(token);
                }
            } else {
                var errMsg = (response.data && typeof response.data === 'string') ? response.data : (str.server_error || 'An error occurred.');
                showResult(errMsg, 'error');
            }
        }).fail(function () {
            $container.find('.bm-qrs-spinner').hide();
            showResult(str.server_error || 'An error occurred. Please try again.', 'error');
        });
    }

    // -----------------------------------------------------------------------
    // Result display helpers
    // -----------------------------------------------------------------------
    function showResult(msg, type) {
        // Encode msg as text node to prevent XSS.
        var $p = $('<p>').addClass(type || '').text(msg);
        $result.empty().append($p);
    }

    function showCheckinSuccess(msg) {
        var $wrap = $('<div>').addClass('bm-qrs-success-wrap');
        $wrap.append($('<span>').addClass('bm-qrs-success-icon').text('✓'));
        $wrap.append($('<p>').text(msg));
        $result.empty().append($wrap);
    }

    function clearResult() {
        $result.empty();
    }

}(jQuery));
