<?php $countries = array('USA' => 'USA', 'canada' => 'CANADA'); ?>
		<!-- CONTENT -->
		<div class="content">	
			
			<!-- ADMIN SECTION -->
			<div class="admin_sect" style="width:95.6%;">
				
				
				<h1>Calculate Shipping</h1>

		<!-- VALIDATION ALERT -->
		<div class='validation_error hide' id="val_container">
	    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
	    <h1>Error</h1>
	    <p><div id="val_error_message"></div></p>
	    <div class="clear"></div>
		</div>
		<!-- END VALIDATION ALERT -->
		
		<!-- PHP ALERT -->
      <?php if(validation_errors()): ?>
        <div class="validation_error">
        <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
          <h1>Error</h1>
          <p>Please make the following corrections: </p>
          <?php echo validation_errors(); ?>
        </div>
        <br />
      <?php endif; ?>
      
      <pre><?php //print_r(@$postalOptions['USPS']); ?></pre>
      <?php if(@$weight): ?>
      Package weighing <?php echo $weight; ?> lbs going to <?php echo $zip; ?>:<br />
        <?php if(@$postalOptions['UPS']->RatedShipment->TotalCharges->MonetaryValue): ?>
        UPS GROUND: <?php echo @$postalOptions['UPS']->RatedShipment->TotalCharges->MonetaryValue; ?><br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][0]['RATE']): ?>
          USPS FIRST CLASS: <?PHP echo $postalOptions['USPS'][0]['RATE']; ?><br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][1]['COMMERCIALRATE']): ?>
          USPS PRIORITY COMMERCIAL: <?php echo @$postalOptions['USPS'][1]['COMMERCIALRATE']; ?><br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][1]['RATE']): ?>
          USPS PRIORITY: <?php echo @$postalOptions['USPS'][1]['RATE']; ?> <br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][3]['COMMERCIALRATE']): ?>
        USPS EXPRESS COMMERCIAL: <?php echo @$postalOptions['USPS'][3]['COMMERCIALRATE']; ?> <br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][3]['RATE']): ?>
        USPS EXPRESS: <?php echo @$postalOptions['USPS'][3]['RATE']; ?> <br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][12]): ?>
          USPS INTERNATIONAL GXG Envelopes: <?PHP print_r($postalOptions['USPS'][12]['POSTAGE']); ?><br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][1]['POSTAGE']): ?>
          USPS INTERNATIONAL Priority Mail Express International: <?PHP print_r($postalOptions['USPS'][1]['POSTAGE']); ?><br />
        <?php endif; ?>
        <?php if(@$postalOptions['USPS'][2]['POSTAGE']): ?>
          USPS INTERNATIONAL Priority Mail International: <?PHP print_r($postalOptions['USPS'][2]['POSTAGE']); ?><br />
        <?php endif; ?>
		  <?php endif; ?>
		  <?php $attributes = array('id' => 'shippingForm', 'class' => 'form_standard');
		    echo form_open('admin/shipping', $attributes); ?>
				
					<br>
				
				<!-- SHIPPING -->
				<div class="tabular_data">
				
					<table cellpadding="3" style="width:100%;">
						<tr class="head_row">
							<td><b>Destination Zip/Postal</b></td>
							<td><b>Weight (lbs)</b></td>
							<td><b>Country</b></td>
						</tr>
												
						<tr>
							<td>
							  <?php echo form_input(array('name' => 'zip', 
      				                              'value' => '', 
      				                              'placeholder' => 'Zip/Postal',
      				                              'class' => 'text larger', 
      				                              'style' => 'height:16px;width:92%;')); ?>
						  </td>
						  <td>
							  <?php echo form_input(array('name' => 'weight', 
      				                              'value' => '', 
      				                              'placeholder' => 'Weight',
      				                              'class' => 'text larger', 
      				                              'style' => 'height:16px;width:92%;')); ?>
						  </td>
						  <td>
						    <?php echo form_dropdown('country', $countries, 'USA'); ?>
						  </td>
						</tr>
						
						
					</table>

				</div>
				
				<div class="dynamic_button">
      	<?php echo form_submit('submit', 'Calculate', 'class="input_button" style="margin-top:0px"'); ?>
      </div>
					
					<div class="clear"></div>
				<!-- END SHIPPING -->
				</form>
				<br>
			</div>
			<!-- END ADMIN SECTION -->
			
				
			
		</div>
		<!-- END CONTENT -->
			
	</div>
	<!-- END CONTENT WRAP ===================================================================-->
	
	<div class="clearfooter"></div>













