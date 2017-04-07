<!-- ADD PRODUCT MODAL-->
<div class="modal_wrap hide" id="new_product_box">
	
	<!-- MODAL -->
	<div class="modal_box">
		
		<h1>New Product</h1>
		
		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="new_product_validation_error">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><div id="new_product_error_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- SUCCESS MESSAGE -->
		<div class="success hide" id="new_product_validation_success">
		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
	    <h1>Success</h1>
	    <p><div id="new_product_success_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END SUCCESS MESSAGE -->

		
		
    <?php echo form_open('/admin/process_new_product', array('class' => 'form_standard', 'id' => 'new_product_form')); ?>
      <input type="text" placeholder="SKU" name="sku" value="<?php echo @$product['sku']; ?>" class="text large" />
			<input id="name" placeholder="Product Name" name="display_name" class="text large"/>
			<input type="text" placeholder="Wholesale" name="wholesale" value="<?php echo @$product['display_name']; ?>" class="text mini" />
			<input type="text" placeholder="Retail" name="retail" value="<?php echo @$product['retail']; ?>" class="text mini" />
			<input type="text" placeholder="WS Sale" name="saleWs" value="<?php echo @$product['saleWs']; ?>" class="text mini" />
			<input type="text" placeholder="Sale" name="sale" value="<?php echo @$product['sale']; ?>" class="text mini" />
			<input type="text" placeholder="Weight" name="weight" value="<?php echo @$product['weight']; ?>" class="text mini" />
			
			<?php echo form_dropdown('category', $categories, @$product['code'], 'style="width:92%"'); ?>
			
			<?php echo form_textarea(array('name' => 'description', 
			                               'value' => @$product['description'], 
			                               'rows' => '6', 
			                               'cols' => '50', 
			                               'placeholder' => 'Description',
			                               'style' => 'width:96%')); ?>
			
			<b>Taxable:</b> <?php echo form_checkbox('taxable', 1, @$product['taxable'], 'class="checkbox"'); ?> | 
			<b>On Sale:</b> <?php echo form_checkbox('onSale', 1,  @$product['onSale'], 'class="checkbox"'); ?> |
			<b>Active:</b> <?php echo form_checkbox('active', 1,  @$product['active'], 'class="checkbox"'); ?>
			
			
			</br></br>
			<div class="dynamic_button">
        <a href="javascript:void(0);" onclick="submitNewProduct();" style="float:left;">Create Product</a>
      </div>
			<div class="dynamic_button">
				<a href="javascript:void(0);" onclick="$.modal.close();">Cancel</a>
			</div>
			<div class="clear"></div>
		</form>
		
	</div>
	<!-- MODAL -->
	
</div>
<!-- END ADD PRODUCT -->
