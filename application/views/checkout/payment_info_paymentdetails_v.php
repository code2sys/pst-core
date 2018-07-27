<div class="hidden_table">
    <div class="pay">
        <p>Field marked with a * are required</p>

        <div class="fld1" style="margin-top:50px;">
            <label for="card-number" style="width:30%;float:left;">Card Number *</label>
            <div id="card-number" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
        </div>

        <div class="fld1" style="margin-top:100px;">
            <label for="cvv" style="width:30%;float:left;">CVV *</label>
            <div id="cvv" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
        </div>

        <div class="fld1" style="margin-top:150px;">
            <label for="expiration-date" style="width:30%;float:left;">Expiration Date *</label>
            <div id="expiration-date" class="fld" style="width:60%;float:left;height:20px;background:white;border:1px solid;border-radius:2px;"></div>
        </div>

        <?php if(@validation_errors() || @$processingError): if(@$_SESSION['failed_validation']): $_SESSION['failed_validation']++; else: $_SESSION['failed_validation'] = 1; endif; ?>
            <div class="g-recaptcha" data-sitekey="<?php echo RECAPTCHA_KEY; ?>"></div>
        <?php endif; ?>

        <input type="submit" class="input_button_purple" style="float:right;margin-top:50px;" value="Process Your Order">
        <!--<button type="submit" class="input_button_purple" style="float:right;">Process Your Order</button>-->
        <div class="clear"></div>
    </div>
</div>
