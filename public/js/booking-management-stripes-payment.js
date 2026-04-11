const FLEXI_PUBLIC_KEY = bm_normal_object.flexi_public_key;
const stripe = Stripe(FLEXI_PUBLIC_KEY);

const flexiIBooking = new URLSearchParams(window.location.search).get(
	"flexi_booking"
);

const flexiPayment = new URLSearchParams(window.location.search).get(
	"flexi_payment"
);

const flexiStatus = new URLSearchParams(window.location.search).get(
	"status"
);

const flexiCheckout = new URLSearchParams(window.location.search).get(
	"checkout"
);

var timer;
let retryCount = 0;

if (!flexiStatus && flexiIBooking && flexiPayment && flexiCheckout) {
	var countDownTime = window.sessionStorage.getItem(flexiPayment) || bm_normal_object.session_timer;
	start_session_timer(countDownTime, function () {
		document.getElementById("countdown").innerHTML = bm_normal_object.session_expired;
	});
	const card = bm_add_payment_element();
	const paymentFrm = document.querySelector("#stripes_paymentFrm");
	const paymentMainSection1 = document.querySelector("#payment_main_section_1");
	const paymentMainSection2 = document.querySelector("#payment_main_section_2");
	let payment_status = false;

	setPaymentProcessing(false);

	if (paymentFrm) {
		paymentFrm.addEventListener("submit", handleSubmit);
	}

	async function handleSubmit(e) {
		e.preventDefault();
		setLoading(true);
		var data = { 'action': 'bm_check_session', 'booking_key': flexiIBooking, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (status) {
			if (status == 1) {
				setLoading(false);
				showPaymentMessage(bm_normal_object.session_expired);
				setReinit();
			} else if (status == 0) {
				initiate_pay(stripe, card);
			} else {
				setLoading(false);
				showPaymentMessage(bm_error_object.server_error);
			}
		});
	}

	var initiate_pay = function (stripe, card) {
		stripe.createPaymentMethod("card", card).then(function (result) {
			if (result.error) {
				if (result.error.type === "card_error" || result.error.type === "validation_error") {
					showPaymentMessage(result.error.message);
				} else {
					showPaymentMessage(bm_error_object.server_error);
				}
				setLoading(false);
			} else if (result.paymentMethod.id) {
				setLoading(true);
				jQuery.post(bm_ajax_object.ajax_url, {
					action: 'bm_process_payment',
					post: {
						booking: flexiIBooking,
						checkout: flexiCheckout,
						paymentMethod: result.paymentMethod.id
					},
					nonce: bm_ajax_object.nonce
				}).done(function (response) {
					var jsondata = jQuery.parseJSON(response);
					var cs = jsondata.data ? jsondata.data : '';
					var status = jsondata.status ? jsondata.status : '';

					if (status) {
						if (cs != '') {
							if (status === "success") {
								handlePaymentConfirmation(stripe, card, cs);
							} else if (status === "requires_capture" || status === "succeeded") {
								payment_status = true;
								setPaymentProcessing(true);
								checkPaymentStatus(cs);
							} else {
								checkForRefund();
							}
						} else {
							checkForRefund();
						}
					} else {
						checkForRefund();
					}
				}).fail(checkForRefund);
			} else {
				checkForRefund();
			}
		});
	};

	function handlePaymentConfirmation(stripe, card, clientSecret) {
		stripe.confirmCardPayment(clientSecret, {
			payment_method: { card: card }
		}).then(function (result) {
			if (result.error) {
				if (result.error.code === 'payment_intent_authentication_failure') {
					handle3DSAuthentication(result.paymentIntent);
				} else if (result.error.code === 'payment_intent_requires_payment_method') {
					handlePaymentMethodRefresh();
				} else {
					checkForRefund();
				}
			} else {
				payment_status = true;
				setPaymentProcessing(true);
				checkPaymentStatus(clientSecret);
			}
		});
	}

	var handlePaymentError = function () {
		setLoading(false);
		showPaymentMessage(bm_error_object.server_error);
	}

	var checkPaymentStatus = function (clientSecret) {

		if (!clientSecret) {
			checkForRefund();
			return;
		}

		stripe
			.retrievePaymentIntent(clientSecret)
			.then(function (result) {
				if (result.paymentIntent) {
					switch (result.paymentIntent.status) {
						case "succeeded":
						case "processing":
						case "requires_capture":
							var post = {
								'booking': flexiIBooking,
								'checkout': flexiCheckout,
							}

							var data = { 'action': 'bm_save_payment', 'post': post, 'nonce': bm_ajax_object.nonce };
							jQuery.post(bm_ajax_object.ajax_url, data, function (status) {
								if (status == 'success' && flexiIBooking !== '') {
									window.location.href = window.location.href.split('?')[0] + '?pid=' + flexiIBooking;
								} else {
									checkForRefund();
								}
							}).fail(function (jqXHR, textStatus, errorThrown) {
								checkForRefund();
							});
							break;
						case "requires_action":
							handle3DSAuthentication(result.paymentIntent);
							break;
						case "requires_payment_method":
							handlePaymentMethodRefresh();
							break;
						default:
							checkForRefund();
							break;
					}
				} else {
					checkForRefund();
				}
			}).catch(function (error) {
				checkForRefund();
			});
	}

	function handle3DSAuthentication(paymentIntent) {
		if (paymentIntent.status === 'requires_action') {
			stripe.handleCardAction(paymentIntent.client_secret).then(function (result) {
				if (result.error) {
					checkForRefund();
				} else {
					payment_status = true;
					setPaymentProcessing(true);
					checkPaymentStatus(paymentIntent.client_secret);
				}
			});
		}
	}

	function handlePaymentMethodRefresh() {
		retryCount++;
		if (retryCount >= 3) {
			showPaymentMessage(bm_normal_object.payment_failed);
			checkForRefund();
			return;
		}

		showPaymentMessage(bm_normal_object.payment_method_refresh);

		const cardElement = document.getElementById('stripes_paymentElement');
		if (cardElement) {
			cardElement.innerHTML = '';
			bm_add_payment_element();
		}

		setLoading(false);
		document.querySelector("#paymentSubmitBtn").disabled = false;
	}


	var checkForRefund = function () {
		var post = {
			'booking': flexiIBooking,
			'checkout': flexiCheckout,
		}

		var data = { 'action': 'bm_check_for_refund', 'post': post, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			window.location.href = window.location.href.split('&')[0] + '&status=payment-failed';
		});
	}

	function showPaymentMessage(messageText) {
		const messageContainer = document.querySelector("#paymentResponse");

		messageContainer.classList.remove("hidden");
		messageContainer.textContent = messageText;

		setTimeout(function () {
			messageContainer.classList.add("hidden");
			messageText.textContent = "";
		}, 5000);
	}

	function setLoading(isLoading) {
		if (isLoading) {
			// Disable the button and show a spinner
			jQuery(document).find("#paymentButtonDiv").css("cursor", "not-allowed");
			jQuery(document).find("#paymentSubmitBtn").prop("disabled", true);
			jQuery(document).find("#spinner").removeClass("hidden");
			jQuery(document).find("#buttonText").addClass("hidden");
		} else {
			// Enable the button and hide spinner
			jQuery(document).find("#paymentButtonDiv").css("cursor", "pointer");
			jQuery(document).find("#paymentSubmitBtn").prop("disabled", false);
			jQuery(document).find("#spinner").addClass("hidden");
			jQuery(document).find("#buttonText").removeClass("hidden");
		}
	}

	function setPaymentProcessing(isProcessing) {
		if (isProcessing) {
			if (payment_status) {
				jQuery(paymentMainSection1).addClass("hidden");
				jQuery(paymentMainSection2).addClass("hidden");
			} else {
				jQuery(paymentFrm).addClass("hidden");
			}

			jQuery(document).find(".timer-alert").addClass("hidden");
			jQuery(document).find("#payment_loader").removeClass("hidden");
			jQuery(document).find("#frmProcess").removeClass("hidden");
		} else {
			jQuery(document).find(".timer-alert").removeClass("hidden");
			jQuery(paymentFrm).removeClass("hidden");
			jQuery(document).find("#frmProcess").addClass("hidden");
			jQuery(document).find("#payment_loader").addClass("hidden");
		}
	}

	function setReinit() {
		jQuery(paymentMainSection1).addClass("hidden");
		jQuery(paymentMainSection2).addClass("hidden");
		jQuery(document).find("#frmProcess").addClass("hidden");
		jQuery(document).find("#payment_loader").addClass("hidden");
		jQuery(document).find("#payReinit").removeClass("hidden");
	}

	// Add payment element
	function bm_add_payment_element() {
		const options = jQuery.parseJSON(bm_normal_object.flexi_card_options);
		const elements = stripe.elements();
		const card = elements.create("card", options);
		if (jQuery(document).find("#stripes_paymentElement").length != 0) {
			card.mount("#stripes_paymentElement");
		}
		return card;
	}

	// Start session timer

	function start_session_timer(i, callback) {
		timer = setInterval(function () {
			minutes = parseInt(i / 60, 10);
			seconds = parseInt(i % 60, 10);

			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;

			document.getElementById("countdown").innerHTML = bm_normal_object.session_ends_in + minutes + ':' + seconds;

			if ((i--) > 0) {
				window.sessionStorage.setItem(flexiPayment, i);
			} else {
				window.sessionStorage.removeItem(flexiPayment);
				clearInterval(timer);
				callback();
			}
		}, 1000);
	}
}


jQuery(document).on('click', '#go_to_payment_page', function (e) {
	e.preventDefault();
	jQuery(document).find('#checkout_response').addClass('hidden');
	jQuery(document).find('#checkout_response').html('');
	jQuery('#checkout_loader').removeClass('hidden');
	var $this = jQuery(this);
	jQuery($this).parents('.formbuttoninnerbox').hide();

	var formData = {};
	var checkoutData = {};
	var billingData = {};
	var shippingData = {};
	var giftData = {};
	var otherData = {};

	jQuery('#checkout_form :input').map(function () {
		if (!jQuery(this).hasClass('booking_form_fields')) {
			validateFields(jQuery(this));

			var type = jQuery(this).prop("type");
			if (typeof (jQuery(this).attr("id")) != "undefined") {
				if (jQuery(this).attr("id") == 'shipping_same_as_billing' || jQuery(this).attr("id") == 'is_gift') {
					var contentFolder = otherData;
				} else if (jQuery(this).attr("id").startsWith('sgbm_field_')) {
					var contentFolder = billingData;
				} else if (jQuery(this).attr("id").startsWith('shipping_')) {
					var contentFolder = shippingData;
				} else if (jQuery(this).attr("id").startsWith('recipient_')) {
					var contentFolder = giftData;
				} else {
					var contentFolder = otherData;
				}

				if ((type == "checkbox")) {
					if (this.checked) {
						contentFolder[jQuery(this).attr('id')] = 1;
					} else {
						contentFolder[jQuery(this).attr('id')] = 0;
					}
				} else if ((type == "radio")) {
					if (this.checked) contentFolder[jQuery(this).attr('id')] = jQuery(this).val();
				} else if ((type == "tel" && jQuery(this).hasClass('intl_phone_field_input') && jQuery(this).val() != '')) {
					var country_text = jQuery(document).find("div.iti__selected-flag div:first-child").attr('class');

					if (country_text && country_text.includes('_')) {
						var country_code = country_text.split('_').pop();
						contentFolder['country_code'] = country_code;
					} else {
						contentFolder['country_code'] = '';
					}

					var intl_code = jQuery(this).parent().find("div.iti__selected-dial-code").text();
					contentFolder[jQuery(this).attr('id')] = intl_code + jQuery(this).val();
				} else {
					contentFolder[jQuery(this).attr('id')] = jQuery(this).val();
				}
			}
		}
	});

	if (jQuery('#checkout_form .required_errortext').length > 0 || jQuery('#checkout_form .terms_required_errortext').length > 0 || jQuery('#checkout_form .checkbox_required_errortext').length > 0) {
		jQuery('#checkout_loader').addClass('hidden');
		jQuery($this).parents('.formbuttoninnerbox').show();
		jQuery(document).find('.iti__selected-flag').css('height', '50% !important');
		return false;
	} else {
		formData['billing_details'] = billingData;
		formData['shipping_details'] = shippingData;
		formData['gift_details'] = giftData;
		formData['other_data'] = otherData;
		checkoutData['booking_data'] = getUrlParameter('flexi_booking');
		checkoutData['checkout_data'] = formData;

		var data = { 'action': 'bm_fetch_checkout_data', 'post': checkoutData, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status;

			if (status == 'error') {
				jQuery('#checkout_loader').addClass('hidden');
				jQuery($this).parents('.formbuttoninnerbox').show();
				jQuery(document).find('#checkout_response').removeClass('hidden');
				jQuery(document).find('#checkout_response').html(jsondata.data ? jsondata.data : bm_error_object.server_error);
				setTimeout(function () {
					jQuery(document).find('#checkout_response').addClass("hidden");
					jQuery(document).find('#checkout_response').html('');
				}, 5000);
			} else if (status == 'success') {
				const p = typeof (jsondata.data) != 'undefined' ? jsondata.data : '';
				const c = typeof (jsondata.checkout) != 'undefined' ? jsondata.checkout : '';

				if (p && c) {
					window.location.href = window.location.href + '&flexi_payment=' + p + '&checkout=' + c;
				} else {
					jQuery('#checkout_loader').addClass('hidden');
					jQuery($this).parents('.formbuttoninnerbox').show();
					jQuery(document).find('#checkout_response').removeClass('hidden');
					jQuery(document).find('#checkout_response').html(bm_error_object.server_error);
					setTimeout(function () {
						jQuery(document).find('#checkout_response').addClass("hidden");
						jQuery(document).find('#checkout_response').html('');
					}, 5000);
				}
			} else {
				jQuery('#checkout_loader').addClass('hidden');
				jQuery($this).parents('.formbuttoninnerbox').show();
				jQuery(document).find('#checkout_response').removeClass('hidden');
				jQuery(document).find('#checkout_response').html(bm_error_object.server_error);
				setTimeout(function () {
					jQuery(document).find('#checkout_response').addClass("hidden");
					jQuery(document).find('#checkout_response').html('');
				}, 5000);
			}
		});
	}
});


jQuery(document).on('click', '#free_booking_no_payment', function (e) {
	e.preventDefault();
	jQuery(document).find('#checkout_response').addClass('hidden');
	jQuery(document).find('#checkout_response').html('');
	jQuery('#checkout_loader').removeClass('hidden');
	var $this = jQuery(this);
	jQuery($this).parents('.formbuttoninnerbox').hide();

	var formData = {};
	var checkoutData = {};
	var billingData = {};
	var shippingData = {};
	var giftData = {};
	var otherData = {};

	jQuery('#checkout_form :input').map(function () {
		if (!jQuery(this).hasClass('booking_form_fields')) {
			validateFields(jQuery(this));

			var type = jQuery(this).prop("type");
			if (typeof (jQuery(this).attr("id")) != "undefined") {
				if (jQuery(this).attr("id") == 'shipping_same_as_billing' || jQuery(this).attr("id") == 'is_gift') {
					var contentFolder = otherData;
				} else if (jQuery(this).attr("id").startsWith('sgbm_field_')) {
					var contentFolder = billingData;
				} else if (jQuery(this).attr("id").startsWith('shipping_')) {
					var contentFolder = shippingData;
				} else if (jQuery(this).attr("id").startsWith('recipient_')) {
					var contentFolder = giftData;
				} else {
					var contentFolder = otherData;
				}

				if ((type == "checkbox")) {
					if (this.checked) {
						contentFolder[jQuery(this).attr('id')] = 1;
					} else {
						contentFolder[jQuery(this).attr('id')] = 0;
					}
				} else if ((type == "radio")) {
					if (this.checked) contentFolder[jQuery(this).attr('id')] = jQuery(this).val();
				} else if ((type == "tel" && jQuery(this).hasClass('intl_phone_field_input') && jQuery(this).val() != '')) {
					var country_text = jQuery(document).find("div.iti__selected-flag div:first-child").attr('class');

					if (country_text && country_text.includes('_')) {
						var country_code = country_text.split('_').pop();
						contentFolder['country_code'] = country_code;
					} else {
						contentFolder['country_code'] = '';
					}

					var intl_code = jQuery(this).parent().find("div.iti__selected-dial-code").text();
					contentFolder[jQuery(this).attr('id')] = intl_code + jQuery(this).val();
				} else {
					contentFolder[jQuery(this).attr('id')] = jQuery(this).val();
				}
			}
		}
	});

	if (jQuery('#checkout_form .required_errortext').length > 0 || jQuery('#checkout_form .terms_required_errortext').length > 0 || jQuery('#checkout_form .checkbox_required_errortext').length > 0) {
		jQuery('#checkout_loader').addClass('hidden');
		jQuery($this).parents('.formbuttoninnerbox').show();
		jQuery(document).find('.iti__selected-flag').css('height', '50% !important');
		return false;
	} else {
		formData['billing_details'] = billingData;
		formData['shipping_details'] = shippingData;
		formData['gift_details'] = giftData;
		formData['other_data'] = otherData;
		checkoutData['booking_data'] = getUrlParameter('flexi_booking');
		checkoutData['checkout_data'] = formData;

		var data = { 'action': 'bm_free_checkout', 'post': checkoutData, 'nonce': bm_ajax_object.nonce };
		jQuery.post(bm_ajax_object.ajax_url, data, function (response) {
			var jsondata = jQuery.parseJSON(response);
			var status = jsondata.status ? jsondata.status : '';

			if (status == 'error') {
				jQuery('#checkout_loader').addClass('hidden');
				jQuery($this).parents('.formbuttoninnerbox').show();
				jQuery(document).find('#checkout_response').removeClass('hidden');
				jQuery(document).find('#checkout_response').html(jsondata.data ? jsondata.data : bm_error_object.server_error);
				setTimeout(function () {
					jQuery(document).find('#checkout_response').addClass("hidden");
					jQuery(document).find('#checkout_response').html('');
				}, 5000);
			} else if (status == 'success') {
				if (flexiIBooking !== '') {
					window.location.href = window.location.href.split('?')[0] + '?pid=' + flexiIBooking;
				} else {
					jQuery('#checkout_loader').addClass('hidden');
					jQuery($this).parents('.formbuttoninnerbox').show();
					jQuery(document).find('#checkout_response').removeClass('hidden');
					jQuery(document).find('#checkout_response').html(bm_error_object.server_error);
					setTimeout(function () {
						jQuery(document).find('#checkout_response').addClass("hidden");
						jQuery(document).find('#checkout_response').html('');
					}, 5000);
				}
			} else {
				jQuery('#checkout_loader').addClass('hidden');
				jQuery($this).parents('.formbuttoninnerbox').show();
				jQuery(document).find('#checkout_response').removeClass('hidden');
				jQuery(document).find('#checkout_response').html(bm_error_object.server_error);
				setTimeout(function () {
					jQuery(document).find('#checkout_response').addClass("hidden");
					jQuery(document).find('#checkout_response').html('');
				}, 5000);
			}
		});
	}
});