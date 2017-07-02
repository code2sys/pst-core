	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">		
			<div style="width:45.6%; float:left;">
		<h1><i class="fa fa-tags"></i>&nbsp;Categories</h1>
			<p><b>View, edit and add a new categories.</b></p>
			<br>


			<!-- ADD NEW / PAGINATION -->
			<?php if (@$pagination): ?>
			<h3 style="float:right;margin-top:20px;">
				<a href=""><i class="fa fa-chevron-circle-left"></i></a>
				<a href="">1</i></a>
				<a href="">2</i></a>
				<a href="">3</i></a>
				<a href="">4</i></a>
				<a href="">5</i></a>
				<a href="">6</i></a>
				<a href=""><i class="fa fa-chevron-circle-right"></i></a>
			</h3>
			
			<?php endif; ?>
			<div class="clear"></div>
			<!-- END ADD NEW / PAGINATION -->
<!-- PRODUCT LIST -->
			<div class="tabular_data">
				<table width="100%" cellpadding="10">
					<tr class="head_row">
						<td></td>
						<td><b>Name</b></td>
						<td><b>Actions</b></td>
					</tr>

<?php

function printCategoryRow($category_id, $categories, $depth = "") {
	if (array_key_exists($category_id, $categories)) {
		$null_cat = is_null($category_id);

		foreach ($categories[$category_id] as $category) {
			?>
			<tr <?php if (!$null_cat): ?>class="hide <?php echo $category_id; ?>"<?php endif; ?>>
				<td>
					<?php if (array_key_exists($category['category_id'], $categories)): ?>
						<a href="javascript:void(0);" onclick="$('.<?php echo $category['category_id']; ?>').toggle();"><i
								class="fa fa-plus-square-o <?php echo $category['category_id']; ?>"></i></a><a
							href="javascript:void(0);"
							onclick="$('.<?php echo $category['category_id']; ?>').toggle(); $('.bottom').hide(); $('.plus').show(); $('.minus').hide();"
							class="hide <?php echo $category['category_id']; ?>"><i
								class="fa fa-minus-square-o"></i></a>
					<?php endif; ?>
				</td>
				<td><?php echo $depth; ?><?php echo $category['name']; ?></td>
				<td>
					<a href="javascript:void(0);" onclick="populateEdit('<?php echo $category['category_id']; ?>');"><i
							class="fa fa-edit"></i>&nbsp;<b>Edit</b></a></a>
					<?php if (!@$category['mx']): ?>
						| <a href="<?php echo base_url('admin/category_delete/' . $category['category_id']); ?>"><i
								class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
					<?php endif; ?>
				</td>
			</tr>
			<?php

			printCategoryRow($category["category_id"], $categories, $depth . "&nbsp;&nbsp;&nbsp;&nbsp;");
		}
	}
}

printCategoryRow(NULL, $categories);

// We could have run these together, but we don't.

?>

					<!--
					<?php print_r($parent_categories); ?>
					-->

				</table>
			</div>
			<!-- END PRODUCT LIST -->

			<!-- ADD NEW / PAGINATION -->
			
			<?php if (@$pagination): ?>
			<h3 style="float:right;margin-top:5px;">
				<a href=""><i class="fa fa-chevron-circle-left"></i></a>
				<a href="">1</i></a>
				<a href="">2</i></a>
				<a href="">3</i></a>
				<a href="">4</i></a>
				<a href="">5</i></a>
				<a href="">6</i></a>
				<a href=""><i class="fa fa-chevron-circle-right"></i></a>
			</h3>
			
			<?php endif; ?>
			<div class="clear"></div>
		</div>	

		<div style="width:45.6%; float:left; margin-left:10px;">
			<br />
			<br />
			<br />
			<br />
			<br />
			<br />
			<!-- PHP ALERT -->
     
		   <!-- ERROR -->
		   <?php if(validation_errors()): ?>
			<div class="error">
				<h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
				<p><?php echo validation_errors(); ?> </p>
			</div>
			<?php endif; ?>
			<!-- END ERROR -->
			
			<!-- SUCCESS -->
			<?php if(@$success): ?>
			<div class="success">
				<h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
				<p><?php echo $success; ?></p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS -->	
			
			<!-- TABS -->
			<div class="tab">
				<ul>
					<li><a href="<?php echo base_url('admin/category'); ?>" class="active"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
					
					<li><a href="<?php echo base_url('admin/category_image'); ?>" class="image_link"><i class="fa fa-image"></i>&nbsp;Images*</a></li>
					<!--<li><a href="#"><i class="fa fa-info-circle"></i>&nbsp;Vendor Info</a></li>-->

					<div class="clear"></div>
				</ul>
			</div>
			<!-- END TABS -->

			<?php $attributes = array('id' => 'categoryEditForm', 'class' => 'form_standard');
		    echo form_open_multipart('admin/category', $attributes); ?>
			
			<!-- TAB CONTENT -->
			<div class="tab_content">
				<div class="hidden_table">
					<table width="100%" cellpadding="6">
						<tr>
							<td style="width:130px;"><b>Parent Category:</b></td>
							<td>
								<?php echo form_dropdown('parent_category_id', $parent_categories, '', 'id="parent_brand" '); ?>
								<input type="hidden" name="category_id" id="category_id"></td>
						</tr>
						<tr>
							<td><b>Active:</b></td>
							<td><?php echo form_checkbox('active', 1, '', 'id="active"'); ?></td>
						</tr>
						<tr>
							<td><b>Featured:</b></td>
							<td>
								<?php echo form_checkbox('featured', 1, '', 'id="featured"'); ?>
							</td>
						</tr>
						<tr>
							<td><b>Name:</b></td>
							<td>
								<input id="name" name="name" value="" class="text large" placeholder="Enter Name" class="text medium" />
							</td>
						</tr>
						<tr>
							<td><b>Title:</b></td>
							<td>
								<input id="title" name="title" value="" class="text large" placeholder="Enter Title" class="text medium" />
							</td>
						</tr>
						<tr>
							<td><b>Meta Description:</b></td>
							<td>
								<input id="meta_tag" name="meta_tag" value="" class="text medium" placeholder="Enter Meta Tag" />
							</td>
						</tr>
						<tr>
							<td><b>Meta Keywords:</b></td>
							<td>
								<input id="keywords" name="keywords" value="" class="text medium" placeholder="Enter Keywords" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Mark-up Percentage:</b>
								<p style="font-size:10px; line-height:8px">Enter 0 to return the items to suggested retail price.</p>
							</td>
							<td>
								<input id="mark-up" name="mark-up" value="" class="text medium" placeholder="Enter Mark-up Percentage" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Google Category Number:</b>
							</td>
							<td>
								<input id="google_category_num" name="google_category_num" value="" class="text medium" placeholder="Enter Google Category Number" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Ebay Category Number:</b>
							</td>
							<td>
								<input id="ebay_category_num" name="ebay_category_num" value="" class="text medium" placeholder="Enter Ebay Category Number" />
							</td>
						</tr>
						<tr>
							<td>
								<b>Notice:</b>
								<p style="font-size:10px; line-height:8px">This will appear at the bottom of the category page.</p>
							</td>
							<td>
								<?php echo form_textarea(array('name' => 'notice', 
	                      			                                                      'value' => set_value('notice'),
	                      			                                                      'id' => 'notice',
	                      			                                                      'placeholder' => 'Category Notice',
	                      			                                                      'style' => 'height:100px; width:80%;')); ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			</form>	
			<!-- END TAB CONTENT -->
			<br>
			<!-- SUBMIT PRODUCT -->			
			<a href="javascript:void(0);" onclick="submitForm()" id="button" class="new"><i class="fa fa-plus"></i>&nbsp;Add a New Category</a>
			<a href="javascript:void(0);" onclick="submitForm()" id="button" class="hide edit"><i class="fa fa-plus"></i>&nbsp;Edit Category</a>
			<!-- SUBMIT DISABLED 
			<p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p> -->
			
			<!-- CANCEL BUTTON 
			<a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a> -->
			
					
		
		</div>
	</div>
</div>
	<!-- END MAIN CONTENT ==================================================================================-->



<script>

	function submitForm()
	{
		$("#categoryEditForm").submit();
	}
	
	function populateEdit(id)
	{
		
		$.post(base_url + 'admin/load_category_rec/' + id,
			{},
			function(encodeResponse)
			{
				 responseData = JSON.parse(encodeResponse);
				  
				  if(responseData['error'] == true)
				  {
			        $('#val_error_message').html(responseData['error_message']);
			        $('#val_container').fadeIn();
			        $('#simplemodal-container').height('auto'); 
			        setTimeout( function(){
			          $('#val_container').fadeOut(1000, function (){});
			         }, 2000); 
				  }
				  else
				  {
			  		  console.log(responseData);
					  $(".image_link").each(function () {
                            //alert($(this).html());
                            var href = $($(this)).attr("href");
                            $($(this)).attr("href", href + '/' + responseData['category_id']);
                            //alert($(this).text());
                        });
					  
		       		  $('.edit').show();
		       		  $('.new').hide();
		       		  $('#category_id').val(responseData['category_id']);
		       		  $('#parent_brand').val(responseData['parent_category_id']);
		       		  if(responseData['active'] == 1)
		       		  	$('#active').prop('checked', true);
		       		  if(responseData['active'] == 0)
		       		  	$('#active').prop('checked', false);
		       		  if(responseData['featured'] == 1)
		       		  	$('#featured').prop('checked', true);
		       		  if(responseData['featured'] == 0)
		       		  	$('#featured').prop('checked', false);
		       		  
		       		  $('#name').val(responseData['name']);	
		       		  $('#title').val(responseData['title']);
		       		  $('#meta_tag').val(responseData['meta_tag']);
		       		  $('#keywords').val(responseData['keywords']);
		       		  $('#mark-up').val(responseData['mark_up']);
		       		  $('#google_category_num').val(responseData['google_category_num']);
		       		  $('#notice').val(responseData['notice']);
		       		  $('#ebay_category_num').val(responseData['ebay_category_num']);
				  }
			});
	
	}
	
</script>