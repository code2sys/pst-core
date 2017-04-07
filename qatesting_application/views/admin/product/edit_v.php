 	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-cube"></i>&nbsp;<?php if(@$new): ?>New<?php else: ?>Edit<?php endif; ?> Product</h1>
			<p><b>Please fill out all fields within required tabs with an *</b></p>
			<br>
			
			<!-- ERROR -->
			<?php if(validation_errors()): ?>
			<div class="error">
				<h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
				<p><?php echo validation_errors(); ?></p>
			</div>
			<?php endif; ?>
			<!-- END ERROR -->
			
			<!-- SUCCESS -->
			<?php if(@$success): ?>
			<div class="success">
				<h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
				<p>Success Message!</p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS -->
			
			
			<!-- TABS -->
			<div class="tab">
				<ul>
					<li><a href="<?php echo base_url('admin/product_edit/'.@$product['partnumber']); ?>" class="active"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
					<li><a href="<?php echo base_url('admin/product_category_brand/'.@$product['partnumber']); ?>"><i class="fa fa-file-text-o"></i>&nbsp;Categories and Brands*</a></li>
					<li><a href="<?php echo base_url('admin/product_description/'.@$product['partnumber']); ?>"><i class="fa fa-file-text-o"></i>&nbsp;Description*</a></li>
					<li><a href="<?php echo base_url('admin/product_meta/'.@$product['partnumber']); ?>"><i class="fa fa-list-alt"></i>&nbsp;Meta Data</a></li>
					<li><a href="<?php echo base_url('admin/product_shipping/'.@$product['partnumber']); ?>"><i class="fa fa-truck"></i>&nbsp;Shipping*</a></li>
					<li><a href="<?php echo base_url('admin/product_images/'.@$product['partnumber']); ?>"><i class="fa fa-image"></i>&nbsp;Images*</a></li>
					<li><a href="<?php echo base_url('admin/product_reviews/'.@$product['partnumber']); ?>"><i class="fa fa-image"></i>&nbsp;Reviews</a></li>
					<div class="clear"></div>
				</ul>
			</div>
			<!-- END TABS -->
			<?php echo form_open('admin/update_part/'.$part_id, array('class' => 'form_standard')); ?>	
			<!-- TAB CONTENT -->
			<div class="tab_content">
				<div class="hidden_table">
					<table width="100%" cellpadding="6">

						<tr>
							<td><b>Product Title:</b></td>
							<td>
								<?php if(@$product['mx']): ?>
									<?php echo $product['name']; ?>
								<?php else: ?>
									<input id="name" name="name" placeholder="Enter Title" class="text medium" />
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<td><b>Markup:</b></td>
							<td><input id="markup" name="markup" placeholder="Enter %" class="text mini" value="<?php echo @number_format($product['partnumbers'][0]['markup'], 0); ?>"/></td>
						</tr>
						<tr>
							<td><b>Feature Product:</b></td>
							<td>
								<?php echo form_checkbox('featured', 1, $product['featured']); ?>
							</td>
						</tr>
						<?php
							$market_places = "default";
							if( !empty($product['partnumbers'][0]['closeout_market_place']) ){
								$market_places = 'closeout_market_place';
							}elseif( !empty($product['partnumbers'][0]['exclude_market_place']) ){
								$market_places = 'exclude_market_place';
							}
						?>
						<tr>
							<td><b>Only display inventory if products are on closeout for Market Places:</b></td>
							<td>
								
								<input type="radio" name="market_places" value="closeout_market_place"<?php if($market_places=='closeout_market_place'){?> checked="checked"<?php }?>>
							</td>
						</tr>
						<tr>
							<td><b>Do not display inventory for Market Places:</b></td>
							<td>
								<input type="radio" name="market_places" value="exclude_market_place"<?php if($market_places=='exclude_market_place'){?> checked="checked"<?php }?>>
							</td>
						</tr>
						<tr>
							<td><b>Default state for Market Places:</b></td>
							<td>
								<input type="radio" name="market_places" value="default"<?php if($market_places=='default'){?> checked="checked"<?php }?>>
							</td>
						</tr>
					</table>
					
				</div>
			</div>
			<!-- END TAB CONTENT -->
			<br>
			
			<!-- SUBMIT PRODUCT -->
			<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Submit Product</button>
			
			<!-- SUBMIT DISABLED 
			<p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>
			
			<a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->
			
			
			</form>
			
			
			
		</div>
	</div>
	<!-- END MAIN CONTENT ==================================================================================-->
	<div class="clearfooter"></div>
	

</div>
<!-- END WRAPPER =========================================================================================-->

    <script type="text/javascript">
	   $("#sortable").sortable({
		    revert: true,
		    stop: function(event, ui) {
		        if(!ui.item.data('tag') && !ui.item.data('handle')) {
		            ui.item.data('tag', true);
		        }
		    },
		    receive: function (event, ui) {   
	           $( "ul#sortable" ).find('.dragRemove').css( "display", "inline" );
	        }
	}).droppable({ });
		$(".draggable").draggable({
		    connectToSortable: '#sortable',
		    helper: 'clone',
		    revert: 'invalid'
		});
	
	$("ul, li").disableSelection();    
	
	function removeCategory()
	{
		$(this).remove();
	}

    </script>

