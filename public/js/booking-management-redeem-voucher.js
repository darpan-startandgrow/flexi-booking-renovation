jQuery(document).ready(function ($) {

    const showError = (selector, message) => {
        const errorElement = $(selector);
        errorElement.html(message).removeClass('hidden');
    };


    const hideError = (selector) => {
        const errorElement = $(selector);
        errorElement.html('').addClass('hidden');
    };


    const setLoading = (isLoading) => {
        const buttonDiv = $(".spinner-parent-div");
        const button = $(".spinner-button");
        const spinner = $(".spinner");
        const buttonText = $("#buttonText");

        buttonDiv.css("cursor", isLoading ? "not-allowed" : "pointer");
        button.prop("disabled", isLoading);
        spinner.toggleClass("hidden", !isLoading);
        buttonText.toggleClass("hidden", isLoading);
    };


    const performAjax = (action, postData, successCallback, errorCallback) => {
        const data = {
            action,
            post: postData,
            nonce: bm_ajax_object.nonce
        };

        $.post(bm_ajax_object.ajax_url, data)
            .done((response) => {
                if (response.success) {
                    successCallback(response);
                } else {
                    errorCallback(response);
                }
            })
            .fail(() => {
                errorCallback({ data: bm_normal_object.server_error });
            })
            .always(() => setLoading(false));
    };


    $('.breadcrumbs-item-link').on('click', (e) => e.preventDefault());
    $('.goBackButton').on('click', (e) => e.preventDefault());

    $('.vocuher-box-page').on('click', () => {
        const baseUrl = window.location.href.split('?')[0];
        window.location.href = baseUrl;
    });

    $('.product-details-page').on('click', () => {
        const baseUrl = window.location.href.split('?')[0];
        const newUrl = `${baseUrl}?voucher=${$.trim(getUrlParameter('voucher'))}`;
        window.location.href = newUrl;
    });


    $('.redeem-page-voucher #get_voucher').on('click', function () {
        hideError('.voucher_error');

        const voucher = $.trim($('#voucher_code').val());

        if (!voucher) {
            showError('.voucher_error', bm_normal_object.enter_voucher_code);
            return false;
        }

        setLoading(true);

        performAjax(
            'check_voucher_validity',
            { voucher },
            () => {
                const currentUrl = window.location.href;
                const newUrl = currentUrl.includes('?')
                    ? `${currentUrl}&voucher=${voucher}`
                    : `${currentUrl}?voucher=${voucher}`;
                window.location.href = newUrl;
            },
            (response) => showError('.voucher_error', response.data)
        );

        return true;
    });


    $('#BookDate').on('change', function () {
        hideError('#common_errorMessage');

        const date = $.trim($(this).val());
        const voucher = $.trim(getUrlParameter('voucher'));
        const slotSelect = $('#voucherSlot');

        if (!voucher) {
            showError('#common_errorMessage', bm_normal_object.enter_voucher_code);
            return false;
        }

        if (!date) {
            showError('#common_errorMessage', bm_normal_object.date_required);
            return false;
        }

        setLoading(true);

        performAjax(
            'fetch_available_timeslots',
            { voucher, date },
            (response) => {
                const slots = response.data.slots;
                slotSelect.empty();
                if (slots.length > 0) {
                    slots.forEach(slot => {
                        slotSelect.append($('<option>').val(slot).text(slot));
                    });
                } else {
                    showError('#common_errorMessage', bm_normal_object.slot_required);
                }
            },
            (response) => {
                slotSelect.empty();
                showError('#common_errorMessage', response.data);
            }
        );
    });


    $('.book-slot-section #gotoConfirm').on('click', function (e) {
        e.preventDefault();
        hideError('#common_errorMessage');

        const voucher = $.trim(getUrlParameter('voucher'));
        const date = $.trim($('#BookDate').val());
        const slot = $.trim($('#voucherSlot').val());

        if (!voucher) {
            showError('#common_errorMessage', bm_normal_object.enter_voucher_code);
            return false;
        }

        if (!date) {
            showError('#common_errorMessage', bm_normal_object.date_required);
            return false;
        }

        if (!slot) {
            showError('#common_errorMessage', bm_normal_object.slot_required);
            return false;
        }

        sessionStorage.setItem(`${voucher}-slot`, slot);

        const baseUrl = window.location.href.split('?')[0];
        const newUrl = `${baseUrl}?voucher=${voucher}&BookDate=${date}`;
        window.location.href = newUrl;
    });


    setIntlInput('recipient_details');


    $('#redeemConfirm').on('click', function (e) {
        e.preventDefault();
        hideError('#common_errorMessage');

        const voucher = $.trim(getUrlParameter('voucher'));
        const date = $.trim(getUrlParameter('BookDate'));
        const slot = $.trim(sessionStorage.getItem(`${voucher}-slot`));

        if (!voucher || !date || !slot) {
            if (!voucher) showError('#common_errorMessage', bm_normal_object.enter_voucher_code);
            if (!date) showError('#common_errorMessage', bm_normal_object.date_required);
            if (!slot) showError('#common_errorMessage', bm_normal_object.slot_required);
            return false;
        }

        const recipient = {};

        let hasError = false;

        const patterns = {
            email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
            tel: /([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})/,
        };

        const validateField = ($field) => {
            const type = $field.prop('type');
            const required = $field.prop('required');
            const visible = $field.is(':visible');
            const value = $field.val()?.trim() || '';

            const addError = (message) => {
                $field.siblings('.recipient_errortext').remove();
                $field.after(`<div class="recipient_errortext">${message}</div>`);
            };

            const removeError = () => {
                $field.siblings('.recipient_errortext').remove();
            };

            if (!required || !visible) {
                removeError();
                return value;
            }

            removeError();

            if (!value) {
                addError(bm_error_object.required);
                hasError = true;
            } else if (type === 'email' && !patterns.email.test(value)) {
                addError(bm_error_object.invalid_email);
                hasError = true;
            } else if (type === 'tel' && !patterns.tel.test(value)) {
                addError(bm_error_object.invalid_contact);
                hasError = true;
            }

            return value;
        };

        $('#recipient_details :input').each(function () {
            const $field = $(this);
            const fieldId = $field.attr('id');

            if ($field.hasClass('intl_phone_field_input')) {
                const countryText = $(document).find("div.iti__selected-flag div:first-child").attr('class');
                const countryCode = countryText.split('_').pop();
                const intlCode = $field.parent().find("div.iti__selected-dial-code").text();
                recipient['country_code'] = countryCode;
                recipient[fieldId] = intlCode + validateField($field);
            } else {
                recipient[fieldId] = validateField($field);
            }
        });

        if (hasError) {
            showError('#common_errorMessage', bm_normal_object.recipient_data_required);
            return false;
        }

        setLoading(true);

        performAjax(
            'confirm_voucher_redemption',
            { voucher, date, slot, recipient },
            () => {
                sessionStorage.removeItem(`${voucher}-slot`);
                const baseUrl = window.location.href.split('?')[0];
                window.location.href = `${baseUrl}?thankyou&redeemed=${voucher}`;
            },
            (response) => showError('#common_errorMessage', response.data)
        );
    });

    $('.redeem_home').on('click', () => {
        const baseUrl = window.location.href.split('?')[0];
        window.location.href = baseUrl;
    });

});