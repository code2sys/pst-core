<div class="hide" id="new_category_box">
<!-- NEW PRODUCT MODAL-->
  <div class="modal_box">
		<form action="/admin/new_category" method="post" id="new_category_form" class="form_standard">
		<h2 style="font-family:Georgia, serif;letter-spacing:-1px;margin:15px 0px 5px 5px"><b><em>New Category</em></b></h2>
  <!-- JAVASCRIPT ALERT -->
		<div class='validation_error hide' id="new_category_validation_error">
	    <div id="new_category_error_message" style="margin-left:20px"></div>
	    <div class="clear"></div>
	  </div>
  <!-- END JAVASCRIPT ALERT -->
		<div class="hidden_table">
			<table width="auto" cellpadding="8" cellspacing="0">
				<tr>
					<td style="width:80px"><b>Code:</b></td>
					<td><input type="text" name="code" value="<?php echo @$category['code']; ?>" class="text medium" /></td>
				</tr>
				<tr>
					<td style="width:80px"><b>Category Name:</b></td>
					<td><input type="text" name="name" value="<?php echo @$category['name']; ?>" class="text medium" /></td>
				</tr>
				<tr>
  				<td style="width:80px"><b>Parent Category:</b></td>
				  <td>
				    <?php echo form_dropdown('parent_code', $parent_categories, @$category['parent_code']); ?>
          </td>
				</tr>
				<tr>
					<td>
					  <b>Active:</b></td><td><?php echo form_checkbox('active', 1,  @$product['active']); ?>
					</td>
				</tr>

				<tr>
					<td style="width:80px"></td>
					<td>
  					<div class="dynamic_button">
              <a href="javascript:void(0);" onclick="submitNewCategory();" style="float:right;">Create New</a>
            </div>
					</td>
				</tr>
			</table>
		</div>
		</form>
	
	</div>
</div>