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
            error: function() {
                alert("Sorry, there was a connection error. Please try again.");
            }
        })

    } catch(err) {
        console.log("submitEnquiryFormModal error: " + err);
    }
}