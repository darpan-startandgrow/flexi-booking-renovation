jQuery(document).on(
    'click', '#apply_flexi_coupon', function (e) {
        e.preventDefault();
        var sucess = bm_success_object.coupon_applied;
        const couponCode = document.getElementById('input_coupon_code').value;
        const flexiBooking = getUrlParameter('flexi_booking')
        const emailField = document.querySelector('input[name="billing_email"]');
        const email = emailField ? emailField.value : '';
        const couponContainer = document.getElementById('discount-coupon-list');
        const button = document.getElementById('apply_flexi_coupon');

        const billingCountrySelect = document.querySelector('[name="billing_country"]');
        const selectedCountry = billingCountrySelect.value;
        const billingStateSelect = document.querySelector('[name="billing_state"]');
        const selectedState = billingStateSelect.value ? billingStateSelect.value : '';
        const country = {'country': selectedCountry, 'state': selectedState};
        button.disabled = true;
        
        var currency_symbol = bm_normal_object.currency_symbol;
        var currency_position = bm_normal_object.currency_position;

        var post = {
            'coupon_code': couponCode,
            'booking_key': flexiBooking,
            'email': email,
            'country': country
        }
        var data = { 'action': 'validate_coupon', 'post': post, 'nonce': bm_ajax_object.nonce };
        jQuery.post(
            bm_ajax_object.ajax_url, data, function (response) {
                var jsondata = jQuery.parseJSON(response);
                var status = jsondata.status ? jsondata.status : '';
                if (status == true) {
                    if (jsondata.checkout_discount <= 0) {
                        jQuery('.discount_li').addClass('hidden')
                    } else {
                        jQuery('.discount_li').removeClass('hidden')
                    }
                    var couponDis = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.coupon_discount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.coupon_discount).toFixed(2)) + currency_symbol;
                    var finalDis = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.checkout_discount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.checkout_discount).toFixed(2)) + currency_symbol;
                    var finalAmt = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) + currency_symbol;
                    const couponRow = document.createElement("li");
                    couponRow.classList.add("cart-row");
                    couponRow.innerHTML = `<span>Coupon: <span class="coupon">${jsondata.code}</span></span>
                    <span>-${couponDis}<span class="remove-coupon" data-code="${jsondata.code}" data-discount="${jsondata.coupon_discount}">[Remove]</span></span>`;
                    if (jsondata.amount == 0) {
                        
                        jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "free_booking_no_payment");
                        jQuery(document).find('div#free_booking_no_payment').html(bm_normal_object.free_book);
                    } else {
                        jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "go_to_payment_page");
                        jQuery(document).find('div#go_to_payment_page').html(bm_normal_object.pay + finalAmt);
                    }
					
					couponRow.querySelector(".remove-coupon").addEventListener("click", function () {
						const couponCode = this.getAttribute("data-code");
						const discount = this.getAttribute("data-discount");
                        const removeButton = this;

                        if (removeButton.disabled) {
                            return;
                        }
                        removeButton.disabled = true;
                        var post = {
                            'coupon_code': couponCode,
                            'booking_key': flexiBooking,
                            'discount': discount,
                        }
                        // Handle logic after removal
                        var data = { 'action': 'coupon_removal', 'post': post, 'nonce': bm_ajax_object.nonce };
                        jQuery.post(
                            bm_ajax_object.ajax_url, data, function (response) {
                                var jsondata = jQuery.parseJSON(response);
                                var status = jsondata.status ? jsondata.status : '';
                                if(status == true ){
                                    if (jsondata.discount <= 0) {
                                        jQuery('.discount_li').addClass('hidden')
                                    } else {
                                        jQuery('.discount_li').removeClass('hidden')
                                    }
                                    var removalfinalDis = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.discount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.discount).toFixed(2)) + currency_symbol;
                                    var removalfinalAmt = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) + currency_symbol;
                                    document.getElementById("coupon-message").innerHTML = "";
                                    jQuery(document).find('span#checkout_total').html(removalfinalAmt);
                                    jQuery(document).find('span#checkout_total').attr('data-discount', jsondata.amount);
                                    jQuery(document).find('span#checkout_discount').html(removalfinalDis);
                                    if (jsondata.amount > 0) {
                                        jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "go_to_payment_page");
                                        jQuery(document).find('div#go_to_payment_page').html(bm_normal_object.pay + removalfinalAmt);
                                    }
                                    if( jsondata.code ){
                                        addCouponRemoved(jsondata.code);
                                    }
                                    couponRow.remove();
                                }
                                removeButton.disabled = false;
                            });
                    });
                    couponContainer.appendChild(couponRow);
                    jQuery(document).find('span#checkout_total').html(finalAmt);
                    jQuery(document).find('span#checkout_total').attr('data-discount', jsondata.amount);
                    jQuery(document).find('span#checkout_discount').html(finalDis);
                    document.getElementById("coupon-message").innerHTML = sucess + couponDis;
                    document.getElementById("coupon-message").style.color = "green";
                  
                } else {
                    if(jsondata.original_dis || jsondata.original_dis == 0) {
                        if (jsondata.original_dis <= 0) {
                            jQuery('.discount_li').addClass('hidden')
                        } else {
                            jQuery('.discount_li').removeClass('hidden')
                        }
                        var originalDis = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.original_dis).toFixed(2)) : changePriceFormat(parseFloat(jsondata.original_dis).toFixed(2)) + currency_symbol;
                        jQuery(document).find('span#checkout_discount').html(originalDis);
                    }else{
                        jQuery(document).find('span#checkout_discount').html('');
                    }
                    var amount = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) + currency_symbol;
                    jQuery(document).find('span#checkout_total').html(amount);
                    document.getElementById("coupon-message").innerHTML = jsondata.error;
                    document.getElementById("coupon-message").style.color = "red";
                }
                button.disabled = false;
            }
        );
    }
);

//for auto apply
jQuery(document).ready(function ($) {
    
        const flexiBooking = getUrlParameter('flexi_booking');
        const emailField = document.querySelector('input[name="billing_email"]');
        const email = emailField ? emailField.value : '';
        const couponContainer = document.getElementById('discount-coupon-list');
        var currency_symbol = bm_normal_object.currency_symbol;
        var currency_position = bm_normal_object.currency_position;
        var post = {
            'booking_id': flexiBooking,
            'email': email
        };
        var data = { 'action': 'bm_fetch_auto_apply_coupon', 'post': post, 'nonce': bm_ajax_object.nonce };
        jQuery.post(
            bm_ajax_object.ajax_url, data, function (response) {
                var jsondata = jQuery.parseJSON(response);
                if(jsondata.status == true ) {
                    jsondata.code_data.forEach(
                        (row) => {
                            var couponDis = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(row.coupon_discount).toFixed(2)) : changePriceFormat(parseFloat(row.coupon_discount).toFixed(2)) + currency_symbol;
                            const couponRow = document.createElement("li");
                            couponRow.classList.add("cart-row");
                            couponRow.innerHTML = `<span>Coupon: <span class="coupon">${row.code}</span></span>
                            <span>-${couponDis}<span class="remove-coupon" data-code="${row.code}">[Remove]</span></span>`;
							couponRow.querySelector(".remove-coupon").addEventListener("click", function () {
							const couponCode = this.getAttribute("data-code");
                            var post = {
                                'coupon_code': couponCode,
                                'booking_key': flexiBooking,
                            }
                            // Handle logic after removal
                            var data = { 'action': 'coupon_removal', 'post': post, 'nonce': bm_ajax_object.nonce };
                            jQuery.post(
                                bm_ajax_object.ajax_url, data, function (response) {
                                    var jsondata = jQuery.parseJSON(response);
                                    var status = jsondata.status ? jsondata.status : '';
                                    if(status == true ){

                                        if (jsondata.discount <= 0) { 
                                            jQuery('.discount_li').addClass('hidden');
                                        } else {
                                            jQuery('.discount_li').removeClass('hidden');
                                        }
                                        var removalfinalDis = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.discount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.discount).toFixed(2)) + currency_symbol;
                                        var removalfinalAmt =  currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) + currency_symbol;
                                        document.getElementById("coupon-message").innerHTML = "";
                                        jQuery(document).find('span#checkout_total').html(removalfinalAmt);
                                        jQuery(document).find('span#checkout_discount').html(removalfinalDis);
                                        if (jsondata.amount > 0) {
                                            jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "go_to_payment_page");
                                            jQuery(document).find('div#go_to_payment_page').html(bm_normal_object.pay + removalfinalAmt);
                                        }
                                        couponRow.remove();
                                        if( jsondata.code ){
                                            addCouponRemoved(jsondata.code);
                                        }
                                    }
                                });
							});
                            if( couponContainer ){
                                couponContainer.appendChild(couponRow);
                            }
                        }
                    );
                    if (jsondata.discount <= 0) { 
                        jQuery('.discount_li').addClass('hidden');
                    } else {
                        jQuery('.discount_li').removeClass('hidden');
                    }
                    var autoDiscount = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.discount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.discount).toFixed(2)) + currency_symbol;
                    var autoAmount = currency_position == 'before' ? currency_symbol + changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) : changePriceFormat(parseFloat(jsondata.amount).toFixed(2)) + currency_symbol;
                    jQuery(document).find('span#checkout_discount').html(autoDiscount);
                    jQuery(document).find('span#checkout_total').html(autoAmount);
                    jQuery(document).find('div.payement_button_parent').children('div.bookbtn').attr("id", "go_to_payment_page");
                    jQuery(document).find('div#go_to_payment_page').html(bm_normal_object.pay + autoAmount);
                }
                var data = { 'action': 'bm_fetch_current_day_coupon_list', 'booking_key': getUrlParameter('flexi_booking'), 'nonce': bm_ajax_object.nonce };
                jQuery.post(
                    bm_ajax_object.ajax_url, data, function (response) {
                        var jsondata = jQuery.parseJSON(response);
                        if( jsondata.code ){
                            couponEventSuggestionBox( jsondata.code );
                        }
                    }
                );

            }
        );  
        
        var modal = document.getElementById("coupon-myModal");
        var btn = document.getElementById("myBtn");
        var span = document.getElementsByClassName("close")[0];
        if( btn ){
            btn.onclick = function() {
                modal.style.display = "block";
            }
        }
        if( span ){
            span.onclick = function() {
                modal.style.display = "none";
            }
        }
        
        window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
        }
    }
);

function resetCpnData()
{
    jQuery('.loader_modal').show();
    const flexiBooking = getUrlParameter('flexi_booking')
    jQuery('#coupon-message').html("");
    jQuery('#input_coupon_code').val("");
}


function getCouponsFromRenderedCards() {
    const couponData = [];
    document.querySelectorAll(".coupon-box").forEach(couponCard => {
        const code        = couponCard.querySelector(".details")?.getAttribute("data-code");
        const description = couponCard.querySelector(".details")?.getAttribute("data-description");
        const date        = couponCard.querySelector(".details")?.getAttribute("data-date");
        const amount      = couponCard.querySelector(".details")?.getAttribute("data-amount");
        const type        = couponCard.querySelector(".details")?.getAttribute("data-type");
        const image       = couponCard.querySelector(".details")?.getAttribute("data-image");
        if (code && description && date && amount && type && image) {
            couponData.push({amount, code, description, date, image, type });
        }
    });
    return couponData;
}
function getCouponsFromList() {
    const usedCouponData = [];
    document.querySelectorAll(".cart-row").forEach(couponCard => {
        const code        = couponCard.querySelector(".remove-coupon")?.getAttribute("data-code");
        if (code) {
            usedCouponData.push( code );
        }
    });
    return usedCouponData;
}

function addCouponRemoved(couponCode){
    const existingCoupons = getCouponsFromRenderedCards();
    
    const couponHandler = couponEventSuggestionBox(existingCoupons);
    if (couponHandler) {
        couponHandler.addCoupon(couponCode);
    }
}

function couponEventSuggestionBox( coupons ) {
    var currency_symbol = bm_normal_object.currency_symbol;
    const couponContainer = document.getElementById("coupon-container");
    const viewMoreBtn = document.getElementById("myBtn");
    const popupContent = document.getElementById("modal-cpn-container");
    const couponCode = document.getElementById('input_coupon_code');
    if (!coupons) return;
    if (!couponContainer || !viewMoreBtn || !popupContent || !couponCode) {
        return null; 
    }
    let lastHiddenCoupon = null; 

    function renderCoupons()
    {
        couponContainer.innerHTML = "";
        popupContent.innerHTML = "";

        const visibleCoupons = coupons.slice(0, 2);
        const hiddenCoupons = coupons.slice(2);

        visibleCoupons.forEach(
            coupon => {
                const couponCard = createCouponCard(coupon, true);
                couponContainer.appendChild(couponCard);
            }
        );
        hiddenCoupons.forEach(
            coupon => {
            const couponCard = createCouponCard(coupon, false);
            popupContent.appendChild(couponCard);
            }
        );
        viewMoreBtn.style.display = hiddenCoupons.length > 0 ? "block" : "none";
        popupContent.style.display = hiddenCoupons.length > 0 ? "block" : "none";
        if (hiddenCoupons.length === 0) {
            closePopup();
        }
    }
    function createCouponCard(coupon, isVisible)
    {
        
        const couponCard = document.createElement("div");
        couponCard.className = "coupon-box";
        if( coupon.type == 'percent' ){
            description = `<span>${coupon.amount}</span>% OFF`;
        }else{
            description = `${currency_symbol} <span>${coupon.amount}</span> COUPON`;
        }
        let fullDescription = coupon.description;
        let truncatedDescription = fullDescription.length > 20 ? fullDescription.substring(0, 20) + "..." : fullDescription;
    
        couponCard.innerHTML = `
        <img src="${coupon.image}" class="logo" alt="bm-image">
        <div class="details" data-description="${coupon.description}" 
        data-code="${coupon.code}" data-image="${coupon.image}" 
        data-type="${coupon.type}" data-amount="${coupon.amount}" 
        data-date="${coupon.date}"><h2>${coupon.code}</h2>
        <p>${description}</p>
        <span>Valid until ${coupon.date}</span><br>
        <p class="text-container">${truncatedDescription}
                ${fullDescription.length > 20 ? `<span class="tooltip">${fullDescription}</span>` : ""}
            </p>
        </div>`;

        couponCard.addEventListener(
            "click", () => {
                couponCode.value = coupon.code;
                const usedCoupon = getCouponsFromList();
                if (lastHiddenCoupon) {
                    if( !usedCoupon.includes( lastHiddenSuggest.code )){
                        coupons.unshift( lastHiddenSuggest);
                    }
                }
                lastHiddenCoupon = couponCard;
                lastHiddenSuggest = coupon;
                coupons = coupons.filter(c => c.code !== coupon.code);
                renderCoupons();
                const hiddenCouponsnum = coupons.slice(2);
                if (hiddenCouponsnum.length === 0) {
                    closePopup();
                }
            }
        );
        return couponCard;
    }
    function addCoupon(newCoupon) {
        coupons.unshift(newCoupon); 
        renderCoupons(); 
    }
    function closePopup() {
        const modal = document.getElementById("coupon-myModal");
        if (modal) {
            modal.style.display = "none";
        }
    }
    renderCoupons();
    return { addCoupon };
}

