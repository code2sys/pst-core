<script src="https://checkout.stripe.com/checkout.js"></script>

<input type="submit" id="stripeCheckoutButton" class="input_button_purple" style="float:right;margin-top:50px;" value="Pay and Checkout">

<script>
    var handler = StripeCheckout.configure({
        key: '<?php echo $stripe_api_key; ?>',
        image: '<?php echo jsite_url('logo.png', true); ?>',
        locale: 'auto',
        token: function(token) {
            // You can access the token ID with `token.id`.
            // Get the token ID to your server-side code for use.
            var addNonce = "<input type='hidden' id='device_data' name='device_data' value='"+ token.id +"'>";
            $("#form_example").append(addNonce);
            $("#form_example").submit();
        }
    });

    document.getElementById('stripeCheckoutButton').addEventListener('click', function(e) {
        // Open Checkout with further options:
        handler.open({
            name: '<?php echo htmlspecialchars($company_name); ?>',
            description: 'Order #<?php echo $order_number; ?>',
            amount: Math.round(caltotal * 100),
            email: '<?php echo htmlspecialchars($email); ?>',
            zipCode: true
        });
        e.preventDefault();
    });

    // Close Checkout on page navigation:
    window.addEventListener('popstate', function() {
        handler.close();
    });
</script>