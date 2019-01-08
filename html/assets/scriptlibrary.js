// https://stackoverflow.com/questions/14936494/get-all-form-elements-values-using-jquery#16362627
// Serialize form fields as URI argument/HTTP POST data
function serializeForm(form) {
    var kvpairs = [];
    for ( var i = 0; i < form.elements.length; i++ ) {
        var e = form.elements[i];
        if(!e.name || !e.value) continue; // Shortcut, may not be suitable for values = 0 (considered as false)
        switch (e.type) {
            case 'email':
            case 'text':
            case 'textarea':
            case 'password':
            case 'hidden':
                kvpairs.push(encodeURIComponent(e.name) + "=" + encodeURIComponent(e.value));
                break;
            case 'radio':
            case 'checkbox':
                if (e.checked) kvpairs.push(encodeURIComponent(e.name) + "=" + encodeURIComponent(e.value));
                break;
            default:
                if (e.name) kvpairs.push(encodeURIComponent(e.name) + "=" + encodeURIComponent(e.value));
                break;
                /*  To be implemented if needed:
                case 'select-one':
                    ... document.forms[x].elements[y].options[0].selected ...
                    break;
                case 'select-multiple':
                    for (z = 0; z < document.forms[x].elements[y].options.length; z++) {
                        ... document.forms[x].elements[y].options[z].selected ...
                    } */
                break;
        }
    }
    return kvpairs.join("&");
}

/*
    The purpose of this is to submit the enquiry form modal; if there is an error, we alert the error message and stay put.
    Otherwise, we close the modal.
 */
function submitEnquiryFormModal(modal_id, url, success_callback) {
    if (!url) {
        url = "/welcome/productEnquiry";
    }

    try {
        var $the_form = $("#" + modal_id + " form");
        var formEl = $the_form[0];
        var formData = serializeForm(formEl);

        // OK, we need to attempt it, and do the right thing...
        $.ajax({
            url: url,
            data: serializeForm(formEl),
            method: "POST",
            dataType: "json",
            success: function(response) {
                if (response.success) {

                    if (success_callback) {
                        success_callback();
                    } else {
                        // shut it down!
                        $("#" + modal_id).modal("hide");
                    }
                } else {
                    // I would ideally like a nice spot to slip this into the form instead of a JavaScript alert.
                    alert(response.error_message);
                    grecaptcha.reset();
                }
            },
            error: function(response) {
                alert("Sorry, there was a connection error. Please try again.");
            }
        })

    } catch(err) {
        console.log("submitEnquiryFormModal error: " + err);
    }
}

if (!window.calculatePricesForPaymentCalculator) {
    window.calculatePricesForPaymentCalculator = function(motorcycleId) {
        var motorcycle = window.motorcycles[motorcycleId];
        var retailPrice = parseFloat(motorcycle.retail_price.replace(/,/g, ''));
        var salePrice = parseFloat(motorcycle.sale_price.replace(/,/g, ''));
        var downPayment = parseFloat(motorcycle.down_payment.replace(/,/g, ''));
        var term = parseFloat(motorcycle.term);
        var interest = parseFloat(motorcycle.interest_rate);
        if ($('.' + motorcycleId + '_warranty_option:checked').length > 0) {
            $('.' + motorcycleId + '_warranty_option:checked').each(function(i, e) {
                var warrantyIndex = $(e).attr('data-value');
                var selectedWarranty = window.motorcycles[motorcycleId]["warranty_options"][warrantyIndex];
                var warrantyPrice = parseFloat(selectedWarranty.price.replace(/,/g, ''));
                retailPrice = retailPrice + warrantyPrice;
                salePrice = salePrice + warrantyPrice;
            });
        }

        if ($('.' + motorcycleId + '_accessory_option:checked').length > 0) {
            $('.' + motorcycleId + '_accessory_option:checked').each(function(i, e) {
                var accessoryIndex = $(e).attr('data-value');
                var selectedAccessory = window.motorcycles[motorcycleId]["accessory_options"][accessoryIndex];
                var accessoryPrice = parseFloat(selectedAccessory.price.replace(/,/g, ''));
                retailPrice = retailPrice + accessoryPrice;
                salePrice = salePrice + accessoryPrice;
            });
        }

        if (!isNaN(parseInt($('#major-unit-payment-calculator-modal_' + motorcycleId + '_down-payment').val()))) {
            downPayment = parseFloat($('#major-unit-payment-calculator-modal_' + motorcycleId + '_down-payment').val().replace(/,/g, ''));
        }

        if (!isNaN(parseInt($('#major-unit-payment-calculator-modal_' + motorcycleId + '_term').val()))) {
            termIdx = parseFloat($('#major-unit-payment-calculator-modal_' + motorcycleId + '_term').val());
            var termObj = window.motorcycles[motorcycleId]["terms"][termIdx];
            interest = parseFloat(termObj.interest_rate.replace(/,/g, ''));
            term = parseFloat(termObj.term.replace(/,/g, ''));
        }

        var savingPrice = retailPrice - salePrice;
        var price = isNaN(salePrice) ? retailPrice : salePrice;
        var principal = price - downPayment;
        var monthlyInterest = (interest / (12 * 100));
        var monthlyPayment = principal * (monthlyInterest / (1 - Math.pow((1 + monthlyInterest), -term) ));

        monthlyPayment = Math.round(monthlyPayment * 100) / 100;

        retailPrice = Math.round(retailPrice * 100) / 100;
        salePrice = Math.round(salePrice * 100) / 100;
        savingPrice = Math.round(savingPrice * 100) / 100;
        $("#major-unit-payment-calculator-modal_retail-price-"+motorcycleId).html('$' + parseFloat(retailPrice).toLocaleString('en'));
        $("#major-unit-payment-calculator-modal_sale-price-"+motorcycleId).html('$' + parseFloat(salePrice).toLocaleString('en'));
        if (savingPrice > 0)
            $("#major-unit-payment-calculator-modal_saving-price-"+motorcycleId).html('Saving Price: $' + parseFloat(savingPrice).toLocaleString('en'));
        $('#major-unit-payment-calculator-modal_' + motorcycleId + '-monthly-payment-value').html(parseFloat(monthlyPayment).toLocaleString('en'));
        $('#major-unit-payment-calculator-modal_' + motorcycleId + '-monthly-payment').val(parseFloat(monthlyPayment));
    };
}

if (!window.submitPaymentCalculatorForm) {
    window.submitPaymentCalculatorForm = function(motorcycleId, actionUrl, redirectUrl) {
        if ($('.' + motorcycleId + '_warranty_option:checked').length > 0) {
            var warrantyOptions = new Array();
            $('.' + motorcycleId + '_warranty_option:checked').each(function(i, e) {
                var warrantyIndex = $(e).attr('data-value');
                var selectedWarranty = window.motorcycles[motorcycleId]["warranty_options"][warrantyIndex];
                warrantyOptions.push(selectedWarranty);
            });
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-warranty-options').val(btoa(JSON.stringify(warrantyOptions)));
        } else {
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-warranty-options').val('');
        }

        if ($('.' + motorcycleId + '_accessory_option:checked').length > 0) {
            var accessoryOptions = new Array();
            $('.' + motorcycleId + '_accessory_option:checked').each(function(i, e) {
                var warrantyIndex = $(e).attr('data-value');
                var selectedAccessory = window.motorcycles[motorcycleId]["accessory_options"][warrantyIndex];
                accessoryOptions.push(selectedAccessory);
            });
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-accessory-options').val(btoa(JSON.stringify(accessoryOptions)));
        } else {
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-accessory-options').val('');
        }

        if (!isNaN(parseInt($('#major-unit-payment-calculator-modal_' + motorcycleId + '_term').val()))) {
            termIdx = parseFloat($('#major-unit-payment-calculator-modal_' + motorcycleId + '_term').val());
            var termObj = window.motorcycles[motorcycleId]["terms"][termIdx];
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-interest-rate').val(termObj.interest_rate);
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-term-term').val(termObj.term);
        } else {
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-interest-rate').val('');
            $('#major-unit-payment-calculator-modal_' + motorcycleId + '-term-term').val('');
        }

        var formSelector = "#major-unit-payment-calculator-modal_" + motorcycleId + " form";
        if ($(formSelector)[0].checkValidity && !$(formSelector)[0].checkValidity()) {
            alert('Please fill all the required fields');
            return;
        }

        submitEnquiryFormModal("major-unit-payment-calculator-modal_" + motorcycleId, actionUrl, function() {
            if (redirectUrl) {
                window.location.href = redirectUrl;
            } else {
                $("#major-unit-payment-calculator-modal_" + motorcycleId).modal("hide");
            }
        });
    }
}