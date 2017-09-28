		<!-- MOTO SELECT -->
		<div class="moto_select_wrap">
			<div class="moto_select">
				
				<div class="shop_by">
					<h4>SHOP BY MACHINE</h4>
				</div>
				
				<form action="<?php echo $s_baseURL.'ajax/update_garage'; ?>" method="post" id="update_garage_form" class="form_standard">
					
					<select name="machine" id="machine" tabindex="1">
						<option value="">-- Select Machine --</option>
						<?php if(@$machines): foreach($machines as $id => $label): ?>
							<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
						<?php endforeach; endif; ?>
<!-- <optgroup label="Motor Cycles"> -->
					</select>
					<select name="make" id="make" tabindex="2">
						<option value="">-- Make --</option>
					</select>

                    <select name="year" id="year" tabindex="3">
                        <option value="">-- Year --</option>
                    </select>

					<select name="model" id="model" tabindex="4">
						<option value="">-- Model --</option>
					</select>
					
				    <a href="javascript:void(0);" onclick="updateGarage();" id="add" class="button_no" style="border-color:#303;margin-top:0px;">Add To Garage</a>
				      <div class="clear"></div>
				</form>
			</div>
		</div>


<?php
$CI =& get_instance();
echo $CI->load->view("widgets/ride_selection_js", array(
    "product" => isset($prodct) ? $product : null,
    "secure" => true
), true);
?>

		<!-- END MOTO SELECT -->
