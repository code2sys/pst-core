<script type="text/javascript" src="/assets/ckeditor4/ckeditor.js"></script>

<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/motorcycle/moto_head", array(
            "new" => @$new,
            "product" => @$product,
            "success" => @$success,
            "assets" => $assets,
            "id" => @$id,
            "active" => "hang_tag",
            "descriptor" => "Hang Tag",
            "source" => @$product["source"],
            "stock_status" => @$product["stock_status"]
        ), true);

        ?>


        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table" style="margin-bottom: 40px;">
                <table width="100%" cellpadding="6">
                    <tr>
                        <td><a href="#" onclick="printPage()">Print</a></td>
                    </tr>
                    <tr>
                        <td>
                            <label for="header_background_color">Header Background Color:&nbsp;</label>
                            <input type="color" name="header_background_color" value="<?php echo $header_background_color;?>">
                        </td>
                        <td>
                            <label for="header_text_color">Header Text Color:&nbsp;</label>
                            <input type="color" name="header_text_color" value="<?php echo $header_text_color;?>">
                        </td>
                        <td>
                            <label for="monthly_payment_color">Monthly Background Color:&nbsp;</label>
                            <input type="color" name="monthly_payment_color" value="<?php echo $monthly_payment_color;?>">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <img class="company_logo_preview img-responsive center-block" style="max-width:300px" src="<?php echo $company_logo ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td><label for="company_logo">Company Logo:&nbsp;</label><input type="file" name="company_logo"></td>
                    </tr>
                </table>
            </div>
            <div class="printing">
                <div class="printing-header hidden_table">
                    <table class="printable-area" width="100%" cellpadding="6">
                        <tr>
                            <td>
                                <table class="" width="100%" cellpadding="6">
                                    <tr>
                                        <td>
                                            <img class="company_logo_preview img-responsive center-block" style="max-width:300px" src="<?php echo $company_logo ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="header-text"><?php echo @$address['company'];?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="header-text"><?php echo @$address['phone'];?></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            <td>
                                <table class="" width="100%" cellpadding="6">
                                    <tr>
                                        <td>
                                            <img class="company_logo_preview img-responsive center-block" style="max-width:300px" src="<?php echo $company_logo ?>"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="header-text"><?php echo @$address['company'];?></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="header-text"><?php echo @$address['phone'];?></span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="printable-area" style="display:flex;">
                    <div style="width: 50%;">
                        <div class="hidden_table">
                            <table width="100%" cellpadding="6">
                                <tr>
                                    <td><div class="motor-name"><?php echo $product["title"];?></div></td>
                                </tr>
                                <tr>
                                    <td><span class="sku">SKU: <?php echo $product['sku'];?></span></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="pricing-widget">
                                        <?php if ($pricing_option['call_for_price']): ?>
                                        <?php else: ?>
                                            <div style="max-width: 320px;display: flex;justify-content: space-between;flex-wrap: wrap;font-size:15px;">
                                                <?php if ($pricing_option['show_retail_price']) { ?>
                                                <div><b>Retail Price:</b>&nbsp;<span style="<?php if ($pricing_option['show_sale_price']) {echo 'text-decoration: line-through;';} ?>"><?php echo $pricing_option['retail_price']?></span></div>
                                                <?php } ?>
                                                <?php if ($pricing_option['show_sale_price']) { ?>
                                                <div style="display: flex;flex-direction: column;text-align: right;<?php if ($pricing_option['discounted']) { echo 'color:#f00;';} ?>">
                                                    <div><b>Our Price:</b>&nbsp;<span><?php echo $pricing_option['sale_price']?></span></div>
                                                    <?php if($pricing_option['discounted']) { ?>
                                                    <div style="font-size: 10px;padding: 4px 0px;">Savings: <?php echo $pricing_option['discount'];?></div>
                                                    <?php } ?>
                                                </div>
                                                <?php } ?>
                                            </div>
                                            <?php if ($pricing_option['show_monthly_payment']) {?>
                                            <div style="display:inline-block">
                                                <div class="vehicle-monthly-payment">
                                                    <svg width="493px" height="52px" viewBox="0 0 493 52" preserveAspectRatio="none" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
                                                        <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                                            <polygon id="shape1" fill="#D8D8D8" fill-rule="nonzero" points="28 0 477 0 493 26 477 52 28 52 45 26"></polygon>
                                                            <polygon id="shape2" fill="#D8D8D8" fill-rule="nonzero" points="0 9.23705556e-14 15 0 31 26 15 52 0 52 17 26"></polygon>
                                                        </g>
                                                    </svg>
                                                    <div class="h4"><?php echo $pricing_option['payment_text'].':&nbsp;'.$pricing_option['monthly_payment'];?>/mo<sup>*</sup></div>
                                                </div>
                                                <div class="vehicle-monthly-payment-desc">
                                                    <div><?php echo 'Plus Tax. '.$pricing_option['months'].' Months, '.$pricing_option['interest_rate'].'% APR. $'.$pricing_option['down_payment'].' Down Payment.';?></div>
                                                </div>
                                            </div>
                                            <?php } ?>
                                        <?php endif ?>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                            <div style="margin: 8px 4px;font-weight:bold;"><?php echo $product['type']?> Details</div>
                            <table class="motor-details" width="100%" cellspacing="0">
                                <tr>
                                    <td width="50%">Year:</td>
                                    <td width="50%"><?php echo $product['year'];?></td>
                                </tr>
                                <tr>
                                    <td>Make:</td>
                                    <td><?php echo $product['make'];?></td>
                                </tr>
                                <tr>
                                    <td>Model:</td>
                                    <td><?php echo $product['model'];?></td>
                                </tr>
                                <?php if ($product['vin_number'] != ''): ?>
                                <tr>
                                    <td>Vin:</td>
                                    <td><?php echo $product['vin_number']?></td>
                                </tr>
                                <?php endif ?>
                                <?php if ($product['mileage'] != ''): ?>
                                <tr>
                                    <td>Mileage:</td>
                                    <td><?php echo $product['mileage']?></td>
                                </tr>
                                <?php endif ?>
                                <?php if ($product['color'] != ''): ?>
                                <tr>
                                    <td>Color:</td>
                                    <td><?php echo $product['color']?></td>
                                </tr>
                                <?php endif ?>
                            </table>
                            <div style="margin: 8px 4px;font-weight:bold;"><?php echo $product['type']?> Specifications</div>
                            <table class="motor-details" width="100%" cellspacing="0">
                                <?php foreach($specs as $spec) { ?>
                                <tr>
                                    <td width="50%"><?php echo $spec['attribute_name'];?></td>
                                    <td width="50%"><?php echo $spec['value'];?></td>
                                </tr>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                    <div style="width: 50%;display:flex;text-align:center;">
                        <div style="display:flex; margin:auto;flex-direction: column;">
                            <img class="company_logo_preview img-responsive center-block" style="max-width:300px;margin:auto" src="<?php echo $company_logo ?>"/>
                            <div class="motor-name"><?php echo $product["title"];?></div>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="loading">
                <div class="spinner">
                    <i class="fa fa-spin fa-spinner"></i>
                </div>
            </div>
        </div>
        <!-- END TAB CONTENT -->



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
<script>
    jQuery(document).ready(function($){
        $('a[data-target="#major-unit-payment-calculator-modal_24"]:not(.vehicle-monthly-payment)').remove();
        var html = '';
        var salePrice = $('.pricing-widget p:nth-child(3)');
        var retailPrice = $('.pricing-widget p:nth-child(2)');
        if (retailPrice.length > 0) {
            var html = '<span class="retail-price">' + retailPrice.html() + '</span>';
            if (salePrice.length > 0) {
                html = html + '<span class="sale-price">' + salePrice.html() + '</span>';
                salePrice.remove();
            }
            retailPrice.replaceWith('')
        }

        var fr=new FileReader();
        fr.onload = function(e) {
            $('img.company_logo_preview').attr('src', this.result);
        };
        $('input[type="file"]')[0].addEventListener("change",function(e) {
            
            // fill fr with image data    
            fr.readAsDataURL(e.target.files[0]);
        });
        $('input[name="header_background_color"]').change(function(e) {
            $('.printing-header').css('background', $(this).val());
        })
        $('input[name="header_text_color"]').change(function(e) {
            $('.header-text').css('color', $(this).val());
        })
        $('input[name="monthly_payment_color"]').change(function(e) {
            $('.vehicle-monthly-payment svg polygon').css('fill', $(this).val());
        })


        $('.printing-header').css('background', $('input[name="header_background_color"]').val());
        $('.header-text').css('color', $('input[name="header_text_color"]').val());
        $('.vehicle-monthly-payment svg polygon').css('fill', $('input[name="monthly_payment_color"]').val());
        
    });
    function printPage() {

        var formData = new FormData();
        var fileInput = $('input[type="file"]')[0];
        if (fileInput.files.length > 0) {
            formData.append('logo', fileInput.files[0]);
        }
        formData.append('header_background_color', $('input[name="header_background_color"]').val());
        formData.append('header_text_color', $('input[name="header_text_color"]').val());
        formData.append('monthly_payment_color', $('input[name="monthly_payment_color"]').val());
        $('.loading').show();
        $.ajax({
            url: '/admin/ajax_motorcycle_hang_tag_settings',
            type: 'POST',
            processData: false,
            contentType: false,
            dataType: 'json',
            data: formData,
            success: function(response) {
                $('.loading').hide();
                window.print();
            },
            error: function(response) {
                $('.loading').hide();
                alert('Failed to save the settings');
            }
        });
    }
</script>
<style>
.loading {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    font-size: 24px;
    background: rgba(0,0,0,0.05);
    display:none;
}
.loading .spinner {
    display:flex;
    justify-content:center;
    align-items:center;
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}
.printing {
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
}
.printing .printable-area {
    max-width: 800px;
    width: 100%;
    margin: auto;
}
.printing-header {
    background:#CCC;
    width: 100%;
}
.header-text{
    color:white;
    font-weight: bold;
}
.motor-name {
    font-size:20px;
    font-weight:bold;
}
.sku {
    font-weight: bold;
}
.vehicle-monthly-payment {
    align-items: center;
    display: flex;
    position: relative;
    -webkit-mask-size: cover;
    mask-size: cover;
    background-repeat: no-repeat;
    background-size: 100% 100%;
    justify-content: center;
    max-width: 360px;
}
.vehicle-monthly-payment svg {
    width: 100%;
    height: 100%;
    position:absolute;
}
.vehicle-monthly-payment svg polygon {
    fill: #00f;
}
.vehicle-monthly-payment .h4 {
    padding: 8px 15px 8px 40px;
    font-weight: 700;
    font-style: oblique;
    color: white;
    margin: unset;
    box-shadow: unset;
    line-height: unset;
    font-size:18px;
    z-index: 1;
}
.vehicle-monthly-payment-desc {
    font-style: oblique;
    font-size: 12px;
    color: black;
    margin: 4px 0px 12px 0px;
}
.strikethrough {
    text-decoration: line-through;
}
.sale-price {
    color: red;
}
table.motor-details td {
    padding: 8px;
}
table.motor-details tr:nth-child(even) {background: #FFF}
table.motor-details tr:nth-child(odd) {background: #EEE}

@media print { 
    body {
        -webkit-print-color-adjust:exact;
    }
    /* All your print styles go here */
    .head_wrap {
        display: none;
    }
    .main_nav_wrap {
        display: none;
    }

    .content_wrap .content>:not(.tab_content) {
        display: none;
    }
    .tab_content {
        border:unset;
        padding:unset;
    }
    .tab_content>:not(.printing) {
        display: none;
    }
    .clear {
        display: none;
    }
    .clearfooter {
        display: none;
    }
    .footer_wrap {
        display:none;
    }
}
</style>
