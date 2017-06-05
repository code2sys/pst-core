	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-dashboard"></i>&nbsp;<?php if(@$pageRec): ?>Edit Page<?php else: ?>Create a New Page<?php endif; ?></h1>
			<a href="<?php echo base_url('admin_content/pages'); ?>" class="button" style="float:right; margin:-50px 0 0 100px;">Back to List</a>
			<br>
			
			<!-- VALIDATION ALERT -->
			<?php if(validation_errors() || @$errors): ?>
			<div class="validation_error" id="login_validation_error">
			  <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <div class="clear"></div>
		    <p><?php echo validation_errors(); if(@$errors): foreach($errors as $error): echo $error; endforeach; endif; ?></p>
		    
			</div>
			<?php endif; ?>
			<!-- END VALIDATION ALERT -->
			
			<!-- SUCCESS MESSAGE -->
			<div class="success hide" id="login_validation_success">
			  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
		    <h1>Success</h1>
		    <div class="clear"></div>
		    <p><div id="login_success_message"></div></p>
			</div>
			<!-- END SUCCESS MESSAGE -->
						
			<form action="<?php echo base_url('pages/edit/'.@$pageRec['id']); ?>" method="post" id="form_example" class="form_standard">
			<?php echo form_hidden('id', @$pageRec['id']); ?>
			
				<div class="hidden_table">	
					<table width="100%" cellpadding="6">
						<?php if (@$pageRec['tag']): ?>
						<tr>
							<td>Page URL</td><td><?php echo base_url("pages/index/" . $pageRec['tag']); ?></td>
						</tr>
						<?php endif; ?>
						<tr>
							<td>Page Name</td><td><input id="label" name="label" value="<?php echo @$pageRec['label']; ?>" class="text large" /></td>
						</tr>
						<tr>
							<td>Meta Title</td><td><input id="title" name="title" value="<?php echo @$pageRec['title']; ?>" class="text large" /></td>
						</tr>
						
						<tr>
							<td>Meta KeyWords</td><td><input id="keywords" name="keywords" value="<?php echo @$pageRec['keywords']; ?>" class="text large" /></td>
						</tr>
						<tr>
							<td>Meta Description</td><td><input id="metatags" name="metatags" value="<?php echo @$pageRec['metatags']; ?>" class="text large" /></td>
						</tr>
						<?php if(@$pageRec['id'] != 12): ?>
						<tr>
							<td>CSS</td><td><input id="css" name="css" value="<?php echo @$pageRec['css']; ?>" class="text large" /></td>
						</tr>
						<tr>
							<td>*Javascript</td><td><span style="font-size:10px;">*Open and close your own script tags.</span><br />
																		 <input id="javascript" name="javascript" value="<?php echo @$pageRec['javascript']; ?>" class="text large" />
							</td>
						</tr>
						<?php if(@$pageRec['delete']): ?>
						<tr>
							<td>Icon</td><td><span style="font-size:10px;">*Font-Awesome icons must be used.</span>&nbsp;&nbsp; <br />
															  <span style="font-size:10px;">Current Icon: </span><i class="fa <?php echo @$pageRec['icon']; ?>"></i><br />
															  <input id="icon" name="icon" value="<?php echo @$pageRec['icon']; ?>" class="text large" /></td>
						</tr>
						<tr>
							<td>Location</td><td><span style="font-size:10px;">*Limit <?php echo FOOTER_PAGE_LIMIT; ?> pages for Footer.</span><br />
																	<?php if(@$location): foreach($location as $key => $loc): ?>
																		<?php echo form_checkbox('location[]', $key, is_numeric(array_search($key, $pageRec['location'])) );  ?> <?php echo $loc; ?><br />
																	<?php endforeach; endif; ?>
															  </td>
						</tr>
						<?php endif; ?>
						<tr>
							<td>TextBox Widget</td><td></td>
						</tr>
						<tr>
							<td colspan="2">
							<p> Make changes to the number and order of the widgets below and then submit to edit the content in the sections below.
								<div class="dragcontainer">
									<?php if(@$widgets): ?>
									<ul id="draggable_list">
										<?php foreach($widgets as $wid): ?>
									   		<li class="draggable ui-state-highlight"><?php echo form_hidden('widgets[]', @$wid['id']); ?><?php echo $wid['label'] ; ?><a href="javascript:void(0);" onclick="removeWidget(this);" class="dragRemove">x</a></li><p><?php echo  $wid['text']; ?></p>
										<?php endforeach; ?>
									</ul>
									<?php endif; ?>
									<ul id="sortable">
										<?php if(@$pageRec['widgets']): ?>
												<?php foreach($pageRec['widgets'] as $wid): ?>
													<li class="draggable ui-state-highlight ui-draggable ui-draggable-handle" style="display: list-item;">
														<input type="hidden" value="<?php echo $wid; ?>" name="widgets[]">
														<?php echo $widgets[$wid]['label']; ?><a class="dragRemove" onclick="removeWidget(this);" href="javascript:void(0);" style="display: inline;">x</a>
													</li>
											<?php endforeach; endif; ?>
									</ul>
								
								</div></td>
						</tr>
						<?php endif; ?>
						<?php if(@$pageRec['id'] == 12): ?>
							<?php if(@$pageRec['widgets']): ?>
								<?php foreach($pageRec['widgets'] as $wid): ?>
										<input type="hidden" value="<?php echo $wid; ?>" name="widgets[]">
							<?php endforeach; endif; ?>
						<?php endif; ?>
					</table>
				</div>
				<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Submit</button>
			</form>
			<div class="clear"></div>
			<br /><br />
			
			<?php if(@$pageRec['widgets']): $slider = 0; $textedit = 0; ?>
				<?php foreach($pageRec['widgets'] as $wid): ?>
				<?php switch($wid):
								case '1' :
									++$slider;
			 ?>
			 	<div class="divider"></div>
			 		<h2>Slider <?php echo $slider; ?></h2>
					<?php echo form_open_multipart('pages/addImages/', array('class' => 'form_standard', 'id' => 'admin_banner_form')); ?>  
						<?php echo form_hidden('page', $pageRec['id']); ?>
						<?php echo form_hidden('order', $slider); ?>
								<div class="tab_content">
									<div class="hidden_table">
										<table width="auto" cellpadding="12">
											<tr>
												<td colspan="3">Images must be 1024px wide by 400px high.<br /><br />
													<?php echo form_upload(array('name' => 'image', 'value' => set_value('main'), 'maxlength' => 50, 'class' => '')); ?><br />
													<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Upload New Banner for Slider <?php echo $slider; ?></button>
												</td>
											</tr>
											<?php if(@$bannerImages): foreach($bannerImages as $img): if ($img['order'] == $slider): ?>
											<tr>
												<td valign="top" style="width:130px;"><b>Image Set <?php echo $img['order']; ?>:</b></td>
												<td><img src="<?php echo base_url($media); ?>/<?php echo $img['image']; ?>" width="200px"></td>
												<td valign="top">
													<b><a href="<?php echo base_url('pages/remove_image/'.$img['id'].'/'.$pageRec['id']); ?>">Remove Image</a></b>
												</td>
											</tr>
											<?php endif; endforeach; endif; ?>
										</table>
									</div>
								</div>
							</form>
					<?php break; 
						
							case '2':
								++$textedit;
					?>
						<div class="divider"></div>
						<h2>TextBox <?php echo $textedit; ?></h2>
						<p>
							You can use this like a word processor.  When you click submit, the data will be saved and rendered onto your webpage.
						</p>
						<br>
						<form action="<?php echo base_url('pages/addTextBox'); ?>" method="post" id="form_example" class="form_standard">
						<?php echo form_hidden('pageId', $pageRec['id']); ?>
						<?php echo form_hidden('order', $textedit); ?>
						
						<?php if(@$textboxes): foreach($textboxes as $textbox): if($textbox['order'] == $textedit): 
							$text = $textbox['text'];
							echo form_hidden('id', $textbox['id']); 
						else:
							$text = '';
						endif; endforeach; endif; 
						
						echo form_textarea(array('name' => 'text', 'value' => set_value('text', @$text), 'id' => 'editor'.$textedit)); 
						?>
						<script type="text/javascript">
			
							// LOAD THE CUSTOM CONFIGURATION FOR THIS INSTANCE
							CKEDITOR.replace( 'editor<?php echo $textedit; ?>', { customConfig : '<?php echo $edit_config; ?>' } );
			
						</script>
			
						<input type="submit" value="Save & Publish TextBox" class="button">
						</form>
					<?php break; ?>
			<?php endswitch; endforeach; endif; ?>
			
		</div>
	</div>
	


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
	
	function removeWidget(item)
	{
		$(item).parent().remove();
	}

    </script>
