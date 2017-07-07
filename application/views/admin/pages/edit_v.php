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
							<p> Make changes to the number and order of the widgets below and then submit to edit the content in the sections below.</p>
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
								
								</div>
							</td>
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
													<button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Upload New Banner</button>
													<button type="button" id="button" class="banner-library">Banner Library</button>
												</td>
											</tr>
                                                                                        <tr class="slider-banners" style="display: none;">
                                                                                            <td colspan="3">
                                                                                                <?php
																								// JLB 07-07-17
																								// This is the only place that Pardy used the constant
																								// STORE_BANNER_LIBRARY
																								// It makes no sense to perpetuate it in one way in one spot,
																								// and then to just assume it/s html/bannerlibrary elsewhere.
                                                                                                $dir = STORE_DIRECTORY . '/html/bannerlibrary/';
                                                                                                $file_display = array(
                                                                                                    'jpg',
                                                                                                    'jpeg',
                                                                                                    'png',
                                                                                                    'gif'
                                                                                                );

                                                                                                if (file_exists($dir) == false) {
                                                                                                    echo 'Directory \'', $dir, '\' not found!';
                                                                                                } else {
                                                                                                    $dir_contents = scandir($dir);

                                                                                                    foreach ($dir_contents as $file) {
                                                                                                        $file_type = strtolower(end(explode('.', $file)));

                                                                                                        if ($file !== '.' && $file !== '..' && in_array($file_type, $file_display) == true) { ?>
                                                                                                            <div class="banner-container">
                                                                                                                <img src='<?php echo jsite_url("/bannerlibrary/".$file); ?>' width='200px' height="100px;"/>
                                                                                                                <input type="checkbox" value="<?php echo $file; ?>" class="check-box" name="banner[]" id="check1" />
                                                                                                            </div>
                                                                                                        <?php }
                                                                                                    }
                                                                                                } ?>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr class="slider-banners" style="display: none;">
                                                                                            <td colspan="3">
                                                                                                <button type="submit" id="button" name="submit" value="addBanner">Add Banner</button>
                                                                                            </td>
                                                                                        </tr>
											<tr>
                                                                                            <td colspan='3'>
                                                                                                <table width='100%' id="sortableBanner">
                                                                                                    <?php if (@$bannerImages): foreach ($bannerImages as $img): ?>
                                                                                                                <tr id="<?php echo $img['id'] ?>" class="ui-state-default">
                                                                                                                    <td valign="top" style="width:130px;"><b>Banner <?php echo $img['order']; ?>:</b></td>
                                                                                                                    <td>
                                                                                                                        <img src="<?php echo base_url($media); ?>/<?php echo $img['image']; ?>" width="200px">
                                                                                                                        <input type="text" name="banner_link[<?php echo $img['id'];?>]" value="<?php echo $img['banner_link'];?>" placeholder="Enter URL" class="sortbannerCls text middle">
                                                                                                                    </td>
												<td valign="top">
                                                                                                                        <b><a href="<?php echo base_url('pages/remove_image/' . $img['id'] . '/' . $pageRec['id']); ?>">Remove Image</a></b>
												</td>
											</tr>
                                                                                                                <?php
                                                                                                        endforeach;
                                                                                                    endif;
                                                                                                    ?>
										</table>
                                                                                            </td>
                                                                                        </tr>
                                                                                        <tr>
                                                                                            <td>
                                                                                                <input type="hidden" name="ordering" id="orderSort"/>                                                                                                <button type="submit" id="button" name="submit" value="saveLink">Submit</button>
                                                                                            </td>
                                                                                        </tr>
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
								<!--
								<?php print_r($textboxes); ?>
								-->
						<br>
						<form action="<?php echo base_url('pages/addTextBox'); ?>" method="post" id="form_example" class="form_standard">
						<?php echo form_hidden('pageId', $pageRec['id']); ?>
						<?php echo form_hidden('order', $textedit); ?>
						
						<?php $text = ""; if(!is_null($textboxes) && is_array($textboxes) && count($textboxes) > 0) {
							for ($i = 0; $i < count($textboxes); $i++) {
								$textbox = $textboxes[$i];
								if ($textbox['order'] == $textedit) {
									$text = $textbox['text'];
									echo form_hidden('id', $textbox['id']);
								}
							}
						}
						
						echo form_textarea(array('name' => 'text', 'value' => set_value('text', $text), 'id' => 'editor'.$textedit));
						?>
						<script type="text/javascript">
			
							// LOAD THE CUSTOM CONFIGURATION FOR THIS INSTANCE
							CKEDITOR.replace( 'editor<?php echo $textedit; ?>', { customConfig : '<?php echo $edit_config; ?>' } );
			
						</script>
			
						<input type="submit" value="Save & Publish TextBox" class="button">
						</form>
					<?php break; ?>
                                        <?php case 3: ?>
                                        <div class="divider"></div>
                                                <h2>Videos</h2>
                                                    <?php echo form_open_multipart('pages/addTopVideos/', array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?>
                                                            <?php echo form_hidden('pageId', $pageRec['id']); ?>
                                            <div class="tab_content">
                                                    <div class="hidden_table">
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
                                                            <?php foreach( $topVideo as $key => $val ) { ?>
                                                                    <tr>
                                                                            <td>
                                                                                    <input id="video_url" name="video_url[<?php echo $key;?>]" value="<?php echo 'https://www.youtube.com/watch?v='.$val['video_url'];?>" class="text small" placeholder="Enter video URL" class="text small" style='height:30px;width:100%;'/>
                                                                            </td>
                                                                            <td>
                                                                                    <input id="title" name="title[<?php echo $key;?>]" value="<?php echo $val['title'];?>" class="text small" placeholder="Enter video Title" class="text small" style='height:30px;width:100%;'/>
                                                                            </td>
                                                                            <td>
                                                                                    <input id="ordering" name="ordering[<?php echo $key;?>]" value="<?php echo $val['ordering'];?>" class="text small" placeholder="Ordering" class="text small" type='number' min='1' style='height:30px;width:100%;'/>
                                                                            </td>
                                                                    </tr>
                                                            <?php } ?>
                                                                    <tr>
                                                                            <td>
                                                                                    <?php echo form_hidden('category_id', $id); ?>
                                                                                    <input id="video_url" name="video_url[]" value="" class="text small" placeholder="Enter video URL" style='height:30px;width:100%;'/>
                                                                            </td>
                                                                            <td>
                                                                                    <input id="title" name="title[]" value="" class="text small" placeholder="Enter video Title" style='height:30px;width:100%;'/>
                                                                            </td>
                                                                            <td>
                                                                                    <input id="ordering" name="ordering[]" value="" class="text small" placeholder="Ordering" type='number' min='1' style='height:30px;width:100%;'/>
                                                                            </td>
                                                                    </tr>
                                                                    </tbody>
                                                                    <tr>
                                                                            <td colspan="2">
                                                                                    <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save Category Video</button>
                                                                            </td>
                                                                    </tr>
                                                            </table>
                                                    </div>
                                            </div>
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
	$(document).on('click','.add-row',function(e){
		var str = "<tr><td><input type='text' name='video_url[]' value='' class='text large' placeholder='Enter video URL' style='height:30px;width:100%;'></td><td><input id='title' name='title[]' value='' class='text large' placeholder='Enter video Title' class='text medium' style='height:30px;width:100%;'/></td><td><input type='number' value='' name='ordering[]' class='text small' placeholder='Ordering' min='1' style='height:30px;width:100%;'</td></tr>";
		$('.tbdy').append( str );
	});

        jQuery('.banner-library').click(function () {
            jQuery('.slider-banners').toggle('slow');
        });
    </script>
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    $(function () {
        $("#sortableBanner tbody").sortable();
        $("#sortableBanner tbody").disableSelection();
	var data = "";

	$("#sortableBanner tbody tr").each(function(i, el){
		//alert(i);
		//alert($(el).attr('id'));
		var p = $(el).text().toLowerCase().replace(" ", "_");
		//alert(p);
		data += $(el).attr('id')+"="+$(el).index()+",";
		
	});
	
	var dta = data.slice(0, -1);
	$("#orderSort").val(dta);
    });
$(document).ready(function(){
	$("#sortableBanner tbody").sortable({
		stop: function(event, ui) {
			var data = "";

			$("#sortableBanner tbody tr").each(function(i, el){
				//alert(i);
				//alert($(el).attr('id'));
				var p = $(el).text().toLowerCase().replace(" ", "_");
				//alert(p);
				data += $(el).attr('id')+"="+$(el).index()+",";
				
			});
			
			var dta = data.slice(0, -1);
			$("#orderSort").val(dta);
			// $.ajax(function(){
				// type : "POST",
				// url : "<?php echo base_url('admin/ajaxOrderSet') ?>",
				// data : { action:"",data: dta },
				// success : function(res){
					// alert(res);
				// }
			// });
			//alert(data.slice(0, -1));
		}
	});
});
</script>
<style>
    #sortableBanner{
        width:120%;
        height:auto;
        padding:30px;
    }
    .sortbannerCls {
        vertical-align: top;
    }
    .banner-container { position: relative; width: 200px; height: 100px; float: left; margin-left: 10px;padding-top: 13px; }
    .check-box { position: absolute; bottom: 0px; right: 0px; }
</style>
