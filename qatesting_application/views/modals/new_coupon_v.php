<!-- ADD PRODUCT MODAL-->
<div class="modal_wrap hide" id="new_coupon_box">
	
	<!-- MODAL -->
	<div class="modal_box">
		
		<h1>New Coupon</h1>
		
		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="new_coupon_validation_error">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><div id="new_coupon_error_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- SUCCESS MESSAGE -->
		<div class="success hide" id="new_coupon_validation_success">
		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
	    <h1>Success</h1>
	    <p><div id="new_coupon_success_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END SUCCESS MESSAGE -->
		
    <?php echo form_open('/admin/process_new_coupon', array('class' => 'form_standard', 'id' => 'new_coupon_form')); ?>
      <b>Code:</b> <input type="text" placeholder="Coupon Code" name="couponCode" value="<?php echo @$coupon['couponCode']; ?>" class="text medium" />
      <b>Start: </b> <input type="text" placeholder="YYYY-MM-DD" name="startDate" value="<?php echo @$coupon['startDate']; ?>" class="text mini" />
      <b>End:</b> <input type="text" placeholder="YYYY-MM-DD" name="endDate" value="<?php echo @$coupon['endDate']; ?>" class="text mini" />
      <b>Uses:</b> <input type="text" placeholder="Total Uses" name="totalUses" value="<?php echo @$coupon['totalUses']; ?>" class="text medium" />
      <b>Percentage:</b> <?php echo form_radio('type', 'percentage', '', 'class="checkbox"'); ?> | 
      <b>Set Value:</b> <?php echo form_radio('type', 'value', '', 'class="checkbox"'); ?><br />
      <b>Amount:</b> <input type="text" placeholder="Amount" name="amount" value="<?php echo @$coupon['amount']; ?>" class="text medium" />
      <b>Associated Product SKU:</b> <input type="text" placeholder="associatedProductSKU" name="associatedProductSKU" value="<?php echo @$coupon['associatedProductSKU']; ?>" class="text medium" /><br />
      <?php if(@$specialConstraints): foreach($specialConstraints as $opt):?>
      <b><?php echo $opt['displayName']; ?></b> <?php echo form_checkbox($opt['ruleName'], $opt['couponSpecialConstraintsId'], '', 'class="checkbox"'); ?> |
      <?php endforeach; endif; ?>
      </br></br>
			<div class="dynamic_button">
        <a href="javascript:void(0);" onclick="submitNewCoupon();" style="float:left;">Create Coupon</a>
      </div>
			<div class="dynamic_button">
				<a href="javascript:void(0);" onclick="$.modal.close();">Cancel</a>
			</div>
			<div class="clear"></div>
		</form>


	</div>
</div>