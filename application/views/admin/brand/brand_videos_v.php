	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">		
			<div style="width:45.6%; float:left;">
		<h1><i class="fa fa-tags"></i>&nbsp;Brands</h1>
			<p><b>View, edit and create a new Brand.</b></p>
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
						<td><b>#</b></td>
						<td><b>Name</b></td>
						<td><b>Parent Brand</b></td>
						<td><b>Actions</b></td>
					</tr>
					<?php if(@$brands): $i=0; foreach($brands as $key => $brand): ?>
					<tr>
						<td><?php echo $key; ?></td>
						<td><?php echo $brand['name']; ?></td>
						<td><b><?php echo @$parent_brands[$brand['parent_brand_id']]; ?></b></td>
						<td>
							<a href="javascript:void(0);" onclick="populateEdit('<?php echo $brand['brand_id']; ?>');"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a></a>
							<?php if(!@$brand['mx']): ?>
									| <a href="<?php echo base_url('admin/brand_delete/'.$brand['brand_id']); ?>"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
								<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; endif; ?>
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
		   <?php if(validation_errors() || @$errors): ?>
			<div class="error">
				<h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
				<p><?php echo validation_errors() . @$errors; ?> </p>
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
					<li><a href="<?php echo base_url('admin/brand'); ?>"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
					<li><a href="<?php echo base_url('admin/brand_image/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Image*</a></li>
					<li><a href="<?php echo base_url('admin/brand_video/'.$id); ?>" class="active"><i class="fa fa-image"></i>&nbsp;Videos*</a></li>
					<li><a href="<?php echo base_url('admin/brand_sizechart/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Size Charts*</a></li>
					<li><a href="<?php echo base_url('admin/brand_rule/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Closeout Schedule*</a></li>
					<div class="clear"></div>
				</ul>
			</div>
			<!-- END TABS -->
					
			<!-- TAB CONTENT -->
			<div class="tab_content" style="width:110%;">
				<div class="hidden_table">
				<?php echo form_open_multipart('admin/brand_video/'. $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?> 
					<table width="100%" cellpadding="6">
						<tr>
							<td colspan="2" class="add-row">Add New</td>
						</tr>
						<tr>
							<th>URL</th>
							<th>Title</th>
							<th>Ordering</th>
						</tr>
						<tbody class="tbdy">
					<?php foreach( $brand_video as $key => $val ) { ?>
						<tr>
							<td>
								<input id="video_url" name="video_url[<?php echo $key;?>]" value="<?php echo 'https://www.youtube.com/watch?v='.$val['video_url'];?>" class="text small" placeholder="Enter video URL" class="text small" style='height:30px;'/>
							</td>
							<td>
								<input id="title" name="title[<?php echo $key;?>]" value="<?php echo $val['title'];?>" class="text small" placeholder="Enter video Title" class="text small" style='height:30px;'/>
							</td>
							<td>
								<input id="ordering" name="ordering[<?php echo $key;?>]" value="<?php echo $val['ordering'];?>" class="text small" placeholder="Ordering" class="text small" type='number' min='1' style='height:30px;'/>
							</td>
						</tr>
					<?php } ?>
						<tr>
							<td>
								<?php echo form_hidden('brand_id', $id); ?>
								<input id="video_url" name="video_url[]" value="" class="text small" placeholder="Enter video URL" style='height:30px;'/>
							</td>
							<td>
								<input id="title" name="title[]" value="" class="text small" placeholder="Enter video Title" style='height:30px;'/>
							</td>
							<td>
								<input id="ordering" name="ordering[]" value="" class="text small" placeholder="Ordering" type='number' min='1' style='height:30px;'/>
							</td>
						</tr>
						</tbody>
						<tr>
							<td colspan="2">
								<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save Brand Video</button>
							</td>
						</tr>
					</table>
				</form>
				</div>
			</div>
				
			<!-- END TAB CONTENT -->
			<br>
			<!-- SUBMIT PRODUCT -->			
			<a href="javascript:void(0);" onclick="submitForm()" id="button" class="hide edit"><i class="fa fa-plus"></i>&nbsp;Save Image</a>			
					
		
		</div>
	</div>
</div>
	<!-- END MAIN CONTENT ==================================================================================-->


<script>

	function submitForm()
	{
		$(".form_standard").submit();
	}
	
	$(document).on('click','.add-row',function(e){
		var str = "<tr><td><input type='text' name='video_url[]' value='' class='text large' placeholder='Enter video URL' style='height:30px;'></td><td><input id='title' name='title[]' value='' class='text large' placeholder='Enter video Title' class='text medium' style='height:30px;'/></td><td><input type='number' value='' name='ordering[]' class='text small' placeholder='Ordering' min='1' style='height:30px;'</td></tr>";
		$('.tbdy').append( str );
	});
	
</script>







