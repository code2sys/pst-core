<script type="text/javascript" src="/assets/ckeditor4/ckeditor.js"></script>

<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <?php
        if (isset($id) && isset($product)) {
            $CI =& get_instance();
            echo $CI->load->view("admin/motorcycle/moto_head", array(
                "new" => @$new,
                "product" => @$product,
                "success" => @$success,
                "assets" => $assets,
                "id" => @$id,
                "active" => "payment_option",
                "descriptor" => "Description",
                "source" => @$product["source"],
                "stock_status" => @$product["stock_status"]
            ), true);
        }
        ?>


        <form id="payment_option_form" class="form_standard" method="post">

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <div class="flex layout-wrap">
                        <div class="field flex layout-row middle">
                            <label>Activate Payment Calculator:</label>
                            <input type="radio" name="active" value="1" <?php echo $payment_option['active'] == 1 ? 'checked': ''?>>Yes</input>
                            <input type="radio" name="active" value="0" <?php echo $payment_option['active'] == 0 ? 'checked': ''?>>No</input>
                        </div>
                        <?php if (isset($product)): ?>
                        <div class="field flex layout-row middle">
                            <label>Use Unit Specific Payment Options:</label>
                            <input type="radio" name="custom" value="1" <?php echo $payment_option['custom'] == 1 ? 'checked': ''?>>Yes
                            <input type="radio" name="custom" value="0" <?php echo $payment_option['custom'] == 0 ? 'checked': ''?>>No
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php if (!isset($product)): ?>
                    <div class="flex layout-wrap layout-wrap">
                        <div class="field flex layout-row middle">
                            <label>Apply to new and used units:</label>
                            <input type="radio" name="condition" value="0" <?php echo $payment_option['condition'] == 0 ? 'checked': ''?>>
                        </div>
                        <div class="field flex layout-row middle">
                            <label>Apply to new units Only:</label>
                            <input type="radio" name="condition" value="1" <?php echo $payment_option['condition'] == 1 ? 'checked': ''?>>
                        </div>
                        <div class="field flex layout-row middle">
                            <label>Apply to used units Only:</label>
                            <input type="radio" name="condition" value="2" <?php echo $payment_option['condition'] == 2 ? 'checked': ''?>>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="flex layout-wrap">
                        <div class="field flex layout-row middle">
                            <label>Display Base Payment on Major Unit list and Detail Page:</label>
                            <input type="radio" name="display_base_payment" value="1" <?php echo $payment_option['display_base_payment'] == 1 ? 'checked': ''?>/>Yes
                            <input type="radio" name="display_base_payment" value="0" <?php echo $payment_option['display_base_payment'] == 0 ? 'checked': ''?>/>No
                        </div>
                        <div class="field flex layout-row middle">
                            <label>Down Payment Used to Calculate Base Payment $:</label>
                            <input type="number" name="base_down_payment" class="text" value="<?php echo $payment_option['base_down_payment']?>">
                        </div>
                        <div class="field flex layout-row middle">
                            <label>Payment Text:</label>
                            <input type="text" name="base_payment_text" class="text" value="<?php echo $payment_option['base_payment_text']?>">
                            <span>&nbsp;*Limit to 20 Characters</span>
                        </div>
                    </div>
                    <div class="flex field">
                        <div class="flex layout-row middle">
                            <label>Down Payment Options $:</label>
                        </div>
                        <div class="flex layout-wrap down-payment-options-wrapper">
                        <?php foreach($payment_option['data']['down_payment_options'] as $down_payment_option) { ?>
                            <div class="flex layout-row middle down-payment-option">
                                <input type="number" name="data[down_payment_options][]" class="text" value="<?php echo $down_payment_option ?>">
                                <a onclick="deleteElement(this.parentElement)">Delete</a>
                            </div>
                        <?php } ?>
                        </div>
                        <a onclick="addDownPaymentOption()" class="flex layout-row middle"><span class="fa fa-plus"></span></a>
                        <div class="flex layout-row middle">
                            <div>*Up to 5 defined down payment options</div>
                        </div>
                    </div>
                    <div class="flex">
                        <div class="field flex layout-col">
                            <label  class="middle">Default Term and Rate / Term and Rate Options:</label>
                            <div class="payment-terms-wrapper">
                                <?php foreach($payment_option['data']['terms'] as $index => $term) { ?>
                                <div class="flex layout-row middle payment-term" style="padding:8px 0px 0px 20px;">
                                    <input type="radio" name="data[selected_term]" class="" value="<?php echo $index ?>" <?php echo $index == $payment_option['data']['selected_term'] ? 'checked' : ''?> required/>
                                    <div class="flex layout-row middle layout-wrap">
                                        <div class="flex layout-row middle" style="padding:0px 8px;">
                                            <label>Interest Rate:</label>
                                            <input type="number" class="text interest-rate" step="0.01" name="data[terms][<?php echo $index?>][interest_rate]" value="<?php echo $term['interest_rate']?>">&nbsp;%
                                        </div>
                                        <div class="flex layout-row middle" style="padding:0px 8px;">
                                            <label>Term:</label>
                                            <input type="number" class="text term"  name="data[terms][<?php echo $index?>][term]" value="<?php echo $term['term']?>">
                                        </div>
                                    </div>
                                    <a onclick="deleteTerm(this.parentElement)">Delete</a>
                                </div>
                                <?php } ?>
                            </div>
                            
                            <div class="flex" style="padding:8px 0px 0px 20px;justify-content: flex-end;">
                                <a onclick="addTerm()" class="flex layout-row middle"><span class="fa fa-plus"></span></a>
                                <div class="flex layout-row middle">
                                    <div>*Limit to 5 Rates/Terms</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex field layout-row top" >
                        <label >Fine Print:</label>
                        <div class="editor-wrapper">
                            <textarea class="editor" name="data[fine_print]" rows="6" placeholder="Enter Description" style="width:100%;"><?php echo $payment_option['data']['fine_print']?></textarea>
                        </div>
                    </div>
                    <div class="field">
                        <div class="">
                            <label>Warranty Options:</label>
                        </div>
                        <div class="warranty-options-wrapper" style="padding: 8px 12px;">
                        <?php foreach($payment_option['data']['warranty_options'] as $index => $warranty_option) { ?>
                            <div class="flex layout-row layout-wrap top warranty-option">
                                <div class="field flex layout-row top">
                                    <label>Option Title:</label>
                                    <input type="text" class="text title" name="data[warranty_options][<?php echo $index ?>][title]" value="<?php echo $warranty_option['title']?>">
                                </div>
                                <div class="field flex layout-row top">
                                    <label>Option Description:</label>
                                    <div class="editor-wrapper">
                                        <textarea class="editor description" name="data[warranty_options][<?php echo $index ?>][description]" rows="6" placeholder="Enter Description" style="width:100%;"><?php echo $warranty_option['description']?></textarea>
                                    </div>
                                </div>
                                <div class="field flex layout-row top">
                                    <label>Option Cost $:</label>
                                    <input type="number" class="text price" name="data[warranty_options][<?php echo $index ?>][price]" value="<?php echo $warranty_option['price']?>">
                                </div>
                                <div class="field flex layout-row middle">
                                    <a onclick="deleteWarrantyOption(this.parentElement.parentElement)">Delete Option</a>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <div class="flex layout-row layout-wrap">
                            <a onclick="addWarrantyOption()">Add New Option</a>
                            <label>* Limit to 5 Warranty Options</label>
                        </div>
                    </div>
                    <div class="field">
                        <div class="">
                            <label>Accessory Options:</label>
                        </div>
                        <div class="accessory-options-wrapper" style="padding: 8px 12px;">
                        <?php foreach($payment_option['data']['accessory_options'] as $index => $accessory_option) { ?>
                            <div class="accessory-option">
                                <div class="field flex layout-row layout-wrap ">
                                    <label>Add Product URL</label>
                                    <input type="text" class="text accessory-product-url" name="data[accessory_options][<?php echo $index?>][product]" data-id="<?php echo $index?>" flex style="flex: 1 1 50%; max-width:50%" value="<?php echo $accessory_option["product"]?>">
                                    <div class="flex layout-row middle">
                                        <a onclick="importAccessoryProduct(this)" class="accessory-product-import" data-id="<?php echo $index?>">Save</a>
                                        <div>Or complete the fields below</div>
                                    </div>
                                </div>
                                <div class="flex layout-row layout-wrap top">
                                    <div class="flex layout-col">
                                        <div class="field flex layout-row">
                                            <label>Option Title:</label>
                                            <input type="text" class="text title" name="data[accessory_options][<?php echo $index?>][title]" value="<?php echo $accessory_option["title"] ?>">
                                        </div>
                                        <div class="layout-col">
                                            <img class="accessory-thumbnail img-responsive center-block" style="max-width:200px" data-id="<?php echo $index?>" src="<?php echo $accessory_option["image"] ?>"/>
                                            <input type="file" class="accessory-thumbnail-file" data-id="<?php echo $index?>"/>
                                            <input type="hidden" class="accessory-thumbnail-url" name="data[accessory_options][<?php echo $index?>][image]" data-id="<?php echo $index?>" value="<?php echo $accessory_option["image"] ?>"/>
                                        </div>
                                    </div>
                                    <div class="field flex layout-row top">
                                        <label>Option Description:</label>
                                        <div class="editor-wrapper">
                                            <textarea id="accessory-description-<?php echo $index?>" class="editor description" name="data[accessory_options][<?php echo $index?>][description]" rows="6" placeholder="Enter Description" style="width:100%;"><?php echo $accessory_option["description"] ?></textarea>
                                        </div>
                                    </div>
                                    <div class="field flex layout-row">
                                        <label>Option Cost $:</label>
                                        <input type="number" class="text price" name="data[accessory_options][<?php echo $index?>][price]" value="<?php echo $accessory_option["price"] ?>">
                                    </div>
                                    <div class="field flex layout-row middle">
                                        <a onclick="deleteAccessoryOption(this.parentElement.parentElement.parentElement)">Delete Option</a>
                                    </div>
                                </div>
                            </div>
                            
                        <?php } ?>
                        </div>
                        <div class="flex layout-row layout-wrap">
                            <a onclick="addAccessoryOption()">Add New Option</a>
                            <label>* Limit to 5 Accessory Options</label>
                        </div>
                    </div>

                </div>
            </div>
            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->
            <a class="submit-button" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>

            <!-- CANCEL BUTTON -->
            <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>

        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>
</div>
<!-- END WRAPPER =========================================================================================-->

<style>

.tab_content label {
    font-size: 14px;
    font-weight: 700;
    color: #444;
    padding-right: 8px;
    min-height: 38px;
    display: flex;
    align-items: center;
}
.tab_content label.middle {
}
.tab_content .field {
    padding: 8px 12px;
}
.tab_content .flex {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
}
.tab_content .layout-col {
    display: -webkit-box;
    display: -webkit-flex;
    display: flex;
    flex-direction: column;
}
.tab_content .layout-row.top {
    align-items:flex-start;
    align-content: start;
}
.tab_content .layout-row.middle {
    -webkit-box-align: center;
    -webkit-align-items: center;
    align-items: center;
    -webkit-align-content: center;
    align-content: center;
}
.tab_content .layout-wrap {
    -webkit-flex-wrap: wrap;
    flex-wrap: wrap;
}
.tab_content a {
    padding: 8px;
    color: #06C;
}
.tab_content .editor-wrapper {
    max-width: 600px;
}
</style>
<script>

CKEDITOR.replaceAll('editor');
$('.accessory-options-wrapper .accessory-option input.accessory-thumbnail-file').each(function(index, element) {
    listenAccessoryFileInput(element);
});

function addDownPaymentOption() {
    if ($('.down-payment-option').length >= 5) {
        return;
    }
    var html = `<div class="flex layout-row middle down-payment-option">
        <input type="number" name="data[down_payment_options][]" class="text" value="500.00">
        <a onclick="deleteElement(this.parentElement)">Delete</a></div>`;
    $('.down-payment-options-wrapper').append(html);
}

function deleteElement(element) {
   $(element).remove();
}
function addTerm() {
    if ($('.payment-term').length >= 5) {
        return;
    }
    var index = $('.payment-term').length;
    var html = `<div class="flex layout-row middle payment-term" style="padding:8px 0px 0px 20px;">
            <input type="radio" name="data[selected_term]" class="selected_term" value="` + index + `" required/>
            <div class="flex layout-row middle layout-wrap">
                <div class="flex layout-row middle" style="padding:0px 8px;">
                    <label>Interest Rate:</label>
                    <input type="number" step="0.01" class="text interest-rate" name="data[terms][` + index + `][interest_rate]">&nbsp;%
                </div>
                <div class="flex layout-row middle" style="padding:0px 8px;">
                    <label>Term:</label>
                    <input type="number" class="text term"  name="data[terms][` + index + `][term]" >&nbsp;%
                </div>
            </div>
            <a onclick="deleteTerm(this.parentElement)">Delete</a>
        </div>`;
    $('.payment-terms-wrapper').append(html);
}


function deleteTerm(element) {
    console.log('here');
    if ($('.payment-term').length <= 1) {
        alert('At least one term / interest rate is required.');
        return;
    }

   var value = $(element).find('input.selected_term:checked').val() * 1;
   value = Math.min(value, $('.payment-term').length - 2);
   $(element).remove();
   // fix index
   var index = 0;
   $('.payment-term').each(function(i, e) {
       $(e).find('input.selected_term').val(index);
       $(e).find('input.interest-rate').attr('name', "data[terms][" + index + "][interest_rate]");
       $(e).find('input.term').attr('name', "data[terms][" + index + "][term]");
       if (i == value) {
            $(e).find('input.selected_term').attr('checked', 'checked');
       }
       index ++;
   });
}

function addWarrantyOption() {
    if ($('.warranty-option').length >= 5) {
        return;
    }
    var index = $('.warranty-option').length;
    var html = `<div class="flex layout-row layout-wrap top warranty-option">
                <div class="field flex layout-row top">
                    <label>Option Title:</label>
                    <input type="text" class="text title" name="data[warranty_options][` + index + `][title]">
                </div>
                <div class="field flex layout-row top">
                    <label>Option Description:</label>
                    <div class="editor-wrapper">
                        <textarea class="editor description" name="data[warranty_options][` + index + `][description]" rows="6" placeholder="Enter Description" style="width:100%;"></textarea>
                    </div>
                </div>
                <div class="field flex layout-row top">
                    <label>Option Cost $:</label>
                    <input type="number" class="text price" name="data[warranty_options][` + index + `][price]">
                </div>
                <div class="field flex layout-row middle">
                    <a onclick="deleteWarrantyOption(this.parentElement.parentElement)">Delete Option</a>
                </div>
            </div>`;
    $('.warranty-options-wrapper').append(html);
    $('.warranty-options-wrapper .warranty-option:last-child .editor').each(function(index, element) {
        CKEDITOR.replace(element);
    });
}

function deleteWarrantyOption(element) {
   $(element).remove();
   // fix index
   var index = 0;
   $('.warranty-option').each(function(i, e) {
       $(e).find('input.title').attr('name', "data[warranty_options][" + index + "][title]");
       $(e).find('textarea.description').attr('name', "data[warranty_options][" + index + "][description]");
       $(e).find('input.price').attr('name', "data[warranty_options][" + index + "][price]");
       index ++;
   });
}

function importAccessoryProduct(e) {
    var idx = $(e).attr('data-id');
    var url = $('input.accessory-product-url[data-id="' + idx + '"]').val();
    var parent = $(e).closest('.accessory-option');
    if (url) {
        var regex = /(.*)\/shopping\/item\/(\d+)\/?.*/g;
        var match = regex.exec(url);
        if (match) {
            console.log(match[1]);
            $.ajax({
                url: '/adminproduct/product_json/' + match[2],
                method: "GET",
                dataType: "json",
                success: function(response) {
                    if (response.success && response.product) {
                        if (response.product.name) {
                            $(parent).find('input.title').val(response.product.name);
                            var textId = $(parent).find('textarea.description').attr('id');
                            CKEDITOR.instances[textId].setData(response.product.description);
                            $(parent).find('input.price').val(response.product.price.sale_min ? response.product.price.sale_min : response.product.price.retail_min);
                            if (response.product.images && response.product.images.length > 0 && response.product.images[0].path) {
                                var image = match[1] + '/productimages/' + response.product.images[0].path;
                                $(parent).find('img.accessory-thumbnail')[0].src = image;
                                $(parent).find('input.accessory-thumbnail-url').val(image);
                            }
                        }
                    }
                },
                error: function(response) {
                    console.log(response);
                }
            })
        }
    }
}

function addAccessoryOption() {
    if ($('.accessory-option').length >= 5) {
        return;
    }
    var index = $('.accessory-option').length;
    var html = `
        <div class="accessory-option">
            <div class="field flex layout-row layout-wrap ">
                <label>Add Product URL</label>
                <input type="text" class="text accessory-product-url" name="data[accessory_options][` + index + `][product]" data-id="` + index + `" flex style="flex: 1 1 50%; max-width:50%">
                <div class="flex layout-row middle">
                    <a onclick="importAccessoryProduct(this)" class="accessory-product-import" data-id="` + index + `">Save</a>
                    <div>Or complete the fields below</div>
                </div>
            </div>
            <div class="flex layout-row layout-wrap top">
                <div class="flex layout-col">
                    <div class="field flex layout-row">
                        <label>Option Title:</label>
                        <input type="text" class="text title" name="data[accessory_options][` + index + `][title]">
                    </div>
                    <div class="layout-col">
                        <img class="accessory-thumbnail img-responsive center-block" style="max-width:200px" data-id="` + index + `"/>
                        <input type="file" class="accessory-thumbnail-file" data-id="` + index + `"/>
                        <input type="hidden" class="accessory-thumbnail-url" name="data[accessory_options][` + index + `][image]" data-id="` + index + `"/>
                    </div>
                </div>
                <div class="field flex layout-row top">
                    <label>Option Description:</label>
                    <div class="editor-wrapper">
                        <textarea class="editor description" name="data[accessory_options][` + index + `][description]" rows="6" placeholder="Enter Description" style="width:100%;"></textarea>
                    </div>
                </div>
                <div class="field flex layout-row">
                    <label>Option Cost $:</label>
                    <input type="number" class="text price" name="data[accessory_options][` + index + `][price]">
                </div>
                <div class="field flex layout-row middle">
                    <a onclick="deleteAccessoryOption(this.parentElement.parentElement.parentElement)">Delete Option</a>
                </div>
            </div>
        </div>`;
    $('.accessory-options-wrapper').append(html);
    $('.accessory-options-wrapper .accessory-option:last-child input.accessory-thumbnail-file').each(function(index, element) {
        listenAccessoryFileInput(element);
    });
    $('.accessory-options-wrapper .accessory-option:last-child .editor').each(function(index, element) {
        var uuid = Math.random().toString(36).substring(2, 15) +
        Math.random().toString(36).substring(2, 15);
        $(element).attr('id', 'accessory-description-'+uuid);
        CKEDITOR.replace(element);
    });
}

function deleteAccessoryOption(element) {
   $(element).remove();
   // fix index
   var index = 0;
   $('.accessory-option').each(function(i, e) {
       $(e).find('input.accessory-product-url').attr('data-id', index);
       $(e).find('input.accessory-product-url').attr('name', "data[accessory_options][" + index + "][product]");
       $(e).find('a.accessory-product-import').attr('data-id', index);
       $(e).find('img.accessory-thumbnail').attr('data-id', index);
       $(e).find('input.accessory-thumbnail-file').attr('data-id', index);
       $(e).find('input.accessory-thumbnail-url').attr('data-id', index);
       $(e).find('input.accessory-thumbnail-url').attr('name', "data[accessory_options][" + index + "][image]");
       $(e).find('input.title').attr('name', "data[accessory_options][" + index + "][title]");
       $(e).find('textarea.description').attr('name', "data[accessory_options][" + index + "][description]");
       $(e).find('input.price').attr('name', "data[accessory_options][" + index + "][price]");
       index ++;
   });

   
}

function listenAccessoryFileInput(element) {
    var fr=new FileReader();
    fr.onload = function(e) {
        var selector = 'img.accessory-thumbnail[data-id="' + $(element).attr('data-id') + '"]';
        $(selector)[0].src = this.result;
        selector = 'input.accessory-thumbnail-url[data-id="' + $(element).attr('data-id') + '"]';
        $(selector).val('');
    };
    element.addEventListener("change",function(e) {
        
        // fill fr with image data    
        fr.readAsDataURL(e.target.files[0]);
    });
}

function uploadAccessoryImages(indexes, callback) {
    var formData = new FormData();
    indexes.forEach(function(index) {
        var fileId = 'input.accessory-thumbnail-file[data-id="' + index + '"]';
        formData.append('files[]', $(fileId)[0].files[0]);
    });
    $.ajax({
        url: '/admin/motorcyle_payment_images_upload',
        type: 'POST',
        processData: false,
        contentType: false,
        dataType: 'json',
        data: formData,
        success: function(response) {
            if (response.success && response.images) {
                for (var i = 0; i < indexes.length; i ++) {
                    var selector = 'input.accessory-thumbnail-url[data-id="' + indexes[i] + '"]';
                    $(selector).val(response.images[i]);
                }
                callback();
            } else {
                alert('Failed to upload the images');
            }
        },
        error: function(response) {
            alert('Failed to upload the images');
        }
    });
}

$('#payment_option_form a.submit-button').click(function() {
    var pendingFiles = new Array();
    $('input.accessory-thumbnail-url').each(function(i, element) {
        if (!$(element).val()) {
            var fileId = 'input.accessory-thumbnail-file[data-id="' + $(element).attr('data-id') + '"]';
            if ($(fileId)[0].files && $(fileId)[0].files.length > 0) {
                pendingFiles.push($(element).attr('data-id'));
            }
        }
    });
    if (pendingFiles.length > 0) {
        uploadAccessoryImages(pendingFiles, function() {
            $('#payment_option_form').submit();
        });
    } else {
        $('#payment_option_form').submit();
    }
});
</script>