document.addEventListener('DOMContentLoaded', function () {
    // alert("HEllo");
    return;
    const applyCouponButton = document.createElement('button');
    applyCouponButton.innerText = 'Apply Coupon';
    applyCouponButton.onclick = function () {
        const couponCode = document.getElementById('custom-coupon-input').value;
        if (!couponCode) {
            alert('Please enter a coupon code');
            return;
        }

        // Send AJAX request to apply coupon
        fetch(wcSettings.siteUrl + '/wp-json/custom/v1/apply-coupon', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': wcSettings.nonce
            },
            body: JSON.stringify({ coupon_code: couponCode })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Coupon applied successfully!');
                console.log('Applied Coupons:', data.appliedCoupons);
            } else {
                alert('Failed to apply coupon: ' + data.message);
            }
        })
        .catch(error => console.error('Error:', error));
    };

    const couponInput = document.createElement('input');
    couponInput.type = 'text';
    couponInput.id = 'custom-coupon-input';
    couponInput.placeholder = 'Enter Coupon Code';

    const checkoutForm = document.querySelector('.wc-block-components-checkout-form');
    if (checkoutForm) {
        checkoutForm.prepend(couponInput);
        checkoutForm.prepend(applyCouponButton);
    }
});
