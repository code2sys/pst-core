
<link rel="stylesheet" href="/assets/jqwidgets/styles/jqx.base.css" type="text/css" />
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcore.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcolorpicker.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxradiobutton.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxdropdownbutton.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxscrollview.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxbuttons.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxdatetimeinput.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxcalendar.js"></script>
<script type="text/javascript" src="/assets/jqwidgets/js/jqxtooltip.js"></script>




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

            <script type="application/javascript">
                $(document).on("ready", function() {
                    var checkTypes = function() {
                        var value = $("input[name='type']:checked").val();

                        $(".typeSpecific").hide();
                        switch (value) {
                            case "Managed Page":
                                $(".typeSpecific.showManagedPage").show();
                                break;

                            case "External Link":
                                $(".typeSpecific.showExternalLink").show();
                                break;

                            case "File Attachment":
                                $(".typeSpecific.showFileAttachment").show();
                                break;
                        }

                    };

                    $("input[name='type']").on("change", checkTypes);
                    checkTypes();
                });
            </script>
						
			<form action="<?php echo base_url('pages/edit/'.@$pageRec['id']); ?>" method="post" id="form_example" class="form_standard" enctype="multipart/form-data">
			<?php echo form_hidden('id', @$pageRec['id']); ?>
			
				<div class="hidden_table">	
					<table width="100%" cellpadding="6">
						<?php if (@$pageRec['tag']): ?>
						<tr <?php if (array_key_exists("admin_pages_tag_error", $_SESSION) && $_SESSION["admin_pages_tag_error"]): ?>class="error"<?php endif; ?>>
							<td>Page URL</td><td><?php echo base_url("pages/index"); ?>/<input type="text" name="tag" value="<?php echo $pageRec['tag']; ?>" /> <a href="<?php echo base_url("pages/index/" . $pageRec['tag']); ?>" target="_blank" style="font-size: 85%">[ View ]</a> <?php if (array_key_exists("admin_pages_tag_error", $_SESSION) && $_SESSION["admin_pages_tag_error"]): ?><em>Sorry, &quot;<?php echo $_SESSION["admin_pages_tag_requested"]; ?>&quot; is already in use.</em><?php endif; ?></td>
						</tr>
                            <?php $_SESSION["admin_pages_tag_error"] = false; ?>
						<?php endif; ?>
						<tr>
							<td>Page Name</td><td><input id="label" name="label" value="<?php echo @$pageRec['label']; ?>" class="text large" /></td>
						</tr>
                        <?php
                        $type_value = array_key_exists("type", $pageRec) ? $pageRec["type"] : "Managed Page";

                        ?>
						<tr>
							<td>Page Type</td><td>
                                <label><input type="radio" name="type" value="Managed Page" <?php if ($type_value == "Managed Page"): ?>checked="checked" <?php endif; ?> /><strong>Managed Page:</strong> Use content-management tools to manage text, video, and slider content.</label><br/>
                                <label><input type="radio" name="type" value="External Link" <?php if ($type_value == "External Link"): ?>checked="checked" <?php endif; ?> /><strong>External Link:</strong> Page links to an external URL that opens in a new window.</label><br/>
                                <label><input type="radio" name="type" value="File Attachment" <?php if ($type_value == "File Attachment"): ?>checked="checked" <?php endif; ?> /><strong>File Attachment:</strong> Upload a file that is downloaded by the website visitor when they click the link.</label><br/>
						</tr>

                        <tr class="typeSpecific showExternalLink">
                            <td>External Link</td><td><input id="external_url" type="text" name="external_url" value="<?php echo @$pageRec['external_url']; ?>" class="text large" /></td>
                        </tr>



                        <?php if (array_key_exists("original_filename", $pageRec) && $pageRec["original_filename"] != ""): ?>
                            <tr class="typeSpecific showFileAttachment">
                                <td>Existing File</td><td><?php echo $pageRec["original_filename"]; ?> <a href="/pages/admindownload/<?php echo $pageRec["id"]; ?>">Download</a></td>
                            </tr>

                        <?php endif; ?>


                        <tr class="typeSpecific showFileAttachment">
                            <td>New File</td><td><input id="upload" name="upload" type="file" class="text large" /></td>
                        </tr>



						<tr class="typeSpecific showManagedPage">
							<td>Meta Title</td><td><input id="title" name="title" value="<?php echo @$pageRec['title']; ?>" class="text large" /></td>
						</tr>
						
						<tr class="typeSpecific showManagedPage">
							<td>Meta KeyWords</td><td><input id="keywords" name="keywords" value="<?php echo @$pageRec['keywords']; ?>" class="text large" /></td>
						</tr>
						<tr class="typeSpecific showManagedPage">
							<td>Meta Description</td><td><input id="metatags" name="metatags" value="<?php echo @$pageRec['metatags']; ?>" class="text large" /></td>
						</tr>
						<?php if(@$pageRec['id'] != 12): ?>
						<?php if(@$pageRec['delete']): ?>
						<tr class="">
							<td>Icon</td><td><span style="font-size:10px;">*Font-Awesome icons must be used.</span>&nbsp;&nbsp; <br />
															  <span style="font-size:10px;">Current Icon: </span><i class="fa <?php echo @$pageRec['icon']; ?>"></i><br />
															  <input id="icon" name="icon" value="<?php echo @$pageRec['icon']; ?>" class="text large" /></td>
						</tr>
						<tr class="">
							<td>Location</td><td><span style="font-size:10px;">*Limit <?php echo FOOTER_PAGE_LIMIT; ?> pages for Footer.</span><br />
																	<?php if(@$location): foreach($location as $key => $loc): ?>
																		<?php echo form_checkbox('location[]', $key, is_numeric(array_search($key, $pageRec['location'])) );  ?> <?php echo $loc; ?><br />
																	<?php endforeach; endif; ?>
															  </td>
						</tr>
						<?php endif; ?>
						<tr class="typeSpecific showManagedPage">
							<td>TextBox Widget</td><td></td>
						</tr>
						<tr class="typeSpecific showManagedPage">
							<td colspan="2">
							<p> Make changes to the number and order of the widgets below and then submit to edit the content in the sections below.</p>
								<div class="dragcontainer">
									<ul id="draggable_list">
                                        <li ><strong>Text Box Widget</strong> <a href="javascript:void(0);" onclick="addWidget('Textbox');" class=""><i class='fa fa-plus'></i>&nbsp;Add</a></li><p>A place to put updates and announcements.</p>

                                        <li ><strong>Video Widget</strong> <a href="javascript:void(0);" onclick="addWidget('Video');" class=""><i class='fa fa-plus'></i>&nbsp;Add</a></li><p>Embed YouTube videos on a page.</p>

                                        <li ><strong>Slider Widget</strong> <a href="javascript:void(0);" onclick="addWidget('Slider');" class=""><i class='fa fa-plus'></i>&nbsp;Add</a></li><p>Large image slider with 1024x400px images.</p>


                                        <li ><strong>Photo Gallery</strong> <a href="javascript:void(0);" onclick="addWidget('Gallery');" class=""><i class='fa fa-plus'></i>&nbsp;Add</a></li><p>Large, interactive photo gallery.</p>


                                        <li ><strong>Calendar of Events</strong> <a href="javascript:void(0);" onclick="addWidget('Events');" class=""><i class='fa fa-plus'></i>&nbsp;Add</a></li><p>Large image slider with 1024x400px images.</p>



									</ul>
									<ul id="sortable">
                                        <?php
                                        if (isset($page_sections) && is_array($page_sections) && count($page_sections) > 0) {
                                            $slider = 0;
                                            $videos = 0;
                                            $textedit = 0;
                                            $gallery = 0;
                                            $events = 0;
                                            foreach ($page_sections as $section) {
                                                switch ($section["type"]) {
                                                    case "Textbox":

                                                        ++$textedit;
                                                        $label = $section["type"] . " " . $textedit;
                                                        break;

                                                    case "Video":
                                                        $videos++;
                                                        $label = $section["type"] . " " . $videos;
                                                        break;

                                                    case "Slider":
                                                        $slider++;
                                                        $label = $section["type"] . " " . $slider;
                                                        break;

                                                    case "Gallery":
                                                        $gallery++;
                                                        $label = $section["type"] . " " . $gallery;
                                                        break;

                                                    case "Events":
                                                        $events++;
                                                        $label = $section["type"] . " " . $events;
                                                        break;
                                                }

                                                ?>
                                                <li class="draggable ui-state-highlight ui-draggable ui-draggable-handle" style="display: list-item;">
                                                    <input type="hidden" value="<?php echo $section["page_section_id"]; ?>" name="page_section_ids[]">
                                                    <?php echo $label; ?><a class="dragRemove" onclick="removeWidget(this);" href="javascript:void(0);" style="display: inline;">x</a>
                                                </li>
                                        <?php
                                            }
                                        }

                                        ?>
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
            <?php
            if (isset($page_sections) && is_array($page_sections) && count($page_sections) > 0) {
                $slider = 0;
                $videos = 0;
                $textedit = 0;
                $gallery = 0;
                $events = 0;
                foreach ($page_sections as $section) {
                    switch($section["type"]) {
                        case "Textbox":

                            ++$textedit;
                            ?>
                            <div class="divider"></div>
                            <div  class="typeSpecific showManagedPage">
                                <h2>TextBox <?php echo $textedit; ?></h2>
                                <p>
                                    You can use this like a word processor.  When you click submit, the data will be saved and rendered onto your webpage.
                                </p>

                                <br>
                                <form action="<?php echo base_url('pages/addTextBox'); ?>" method="post" id="form_example" class="form_standard">
                                    <?php echo form_hidden('pageId', $pageRec['id']); ?>
                                    <?php echo form_hidden('order', $textedit); ?>
                                    <?php echo form_hidden('page_section_id', $section['page_section_id']); $textboxes = $section["textboxes"]; ?>

                                    <?php $text = ""; if(!is_null($textboxes) && is_array($textboxes) && count($textboxes) > 0) {
                                        for ($i = 0; $i < count($textboxes); $i++) {
                                            $textbox = $textboxes[$i];
                                            $text = $textbox['text'];
                                            echo form_hidden('id', $textbox['id']);
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
                            </div>
                            <?php

                            break;


                        case "Video":
                            $videos++;
                            ?>
            <div class="divider"></div>
        <div  class="typeSpecific showManagedPage">
                                                <h2>Videos <?php echo $videos; ?></h2>
                                                    <?php echo form_open_multipart('pages/addTopVideos/', array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?>
            <?php echo form_hidden('page', $pageRec['id']); ?>
            <?php echo form_hidden('pageId', $pageRec['id']); ?>
            <?php echo form_hidden('page_section_id', $section['page_section_id']); ?>
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6" class="video_table_<?php echo $videos; ?>">
                        <tr>
                            <td colspan="2" class="add-row">Add New</td>
                        </tr>
                        <tr>
                            <th>URL</th>
                            <th>Title</th>
                            <th>Ordering</th>
                        </tr>
                        <tbody class="tbdy">
                        <?php foreach( $section["videos"] as $key => $val ) { ?>
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
        </div>
                            <script type="application/javascript">
                                $(document).on('click','.video_table_<?php echo $videos; ?> .add-row',function(e){
                                    var str = "<tr><td><input type='text' name='video_url[]' value='' class='text large' placeholder='Enter video URL' style='height:30px;width:100%;'></td><td><input id='title' name='title[]' value='' class='text large' placeholder='Enter video Title' class='text medium' style='height:30px;width:100%;'/></td><td><input type='number' value='' name='ordering[]' class='text small' placeholder='Ordering' min='1' style='height:30px;width:100%;'</td></tr>";
                                    $('.video_table_<?php echo $videos; ?> .tbdy').append( str );
                                });
                            </script>
        <?php

                            break;

            case "Gallery":
                $gallery++;

                ?>
                <div class="divider typeSpecific showManagedPage"></div>
            <h2>Gallery <?php echo $gallery; ?></h2>

            <script>
                $(function(){
                    $( "#sortableGallery<?php echo $gallery; ?>" ).sortable();
                    $( "#sortableGallery<?php echo $gallery; ?>" ).disableSelection();
                    var data = "";

                    $("#sortableGallery<?php echo $gallery; ?> li").each(function(i, el){
                        //alert(i);
                        //alert($(el).attr('id'));
                        var p = $(el).text().toLowerCase().replace(" ", "_");
                        //alert(p);
                        data += $(el).attr('id')+"="+$(el).index()+",";

                    });

                    var dta = data.slice(0, -1);
                    $("#order<?php echo $section['page_section_id']; ?>").val(dta);
                });
                $(document).ready(function(){
                    $("#sortableGallery<?php echo $gallery; ?>").sortable({
                        stop: function(event, ui) {
                            var data = "";

                            $("#sortableGallery<?php echo $gallery; ?> li").each(function(i, el){
                                //alert(i);
                                //alert($(el).attr('id'));
                                var p = $(el).text().toLowerCase().replace(" ", "_");
                                //alert(p);
                                data += $(el).attr('id')+"="+$(el).index()+",";

                            });

                            var dta = data.slice(0, -1);
                            $("#order<?php echo $section['page_section_id']; ?>").val(dta);
                        }
                    });
                });
            </script>

                    <!-- TAB CONTENT -->
                    <div class="tab_content">
                        <div class="hidden_table box-table-content">
                            <?php if (isset($section["gallery"]) && is_array($section["gallery"]) && count($section["gallery"]) > 0): ?>
                                <ul id="sortableGallery<?php echo $gallery; ?>">
                                    <?php foreach( $section["gallery"] as $k => $v ) { ?>
                                        <li style="padding:20px;" id="pageVaultGallery<?php echo $v['page_vault_image_id'] ?>" class="ui-state-default">
                                            <div class="tabe"><img height="50" width="50"  src="<?php echo base_url($media); ?>/<?php echo $v['image_name']; ?>"></div>
                                            <form class="form_standard" enctype="multipart/form-data" method="post" action="/pages/vault_updateImage/<?php echo $pageRec['id']; ?>/<?php echo $section['page_section_id']; ?>/<?php echo $v['page_vault_image_id']; ?>">
                                                <input id="name<?php echo $v['page_vault_image_id']; ?>" name="description" placeholder="Description" value="<?php echo $v['description'];?>" class="text medium" /><br>
                                                <input type="submit" value="Update Description" name="update">
                                            </form>
                                            <form class="form_standard" enctype="multipart/form-data" method="post" action="/pages/vault_deleteImage/<?php echo $pageRec['id']; ?>/<?php echo $section['page_section_id']; ?>/<?php echo $v['page_vault_image_id']; ?>">
                                                <input type="submit" class="dlt" value="Delete Image">
                                            </form>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <form class="form_standard float-section" method="post" action="/pages/vault_reorderImages/<?php echo $pageRec['id']; ?>/<?php echo $section['page_section_id']; ?>">
                                    <input type="hidden" name="order" id="order<?php echo $section['page_section_id']; ?>"></input>
                                    <input type="submit" name="orderSubmit" value="Update Order"></input>
                                </form>
                            <?php endif; ?>
                            <table width="auto" cellpadding="12">
                                <tr>
                                    <td valign="top"><b>Add Image:</b></td>
                                    <td valign="top">
                                        <form class="form_standard" enctype="multipart/form-data" method="post" action="/pages/vault_addImage/<?php echo $pageRec['id']; ?>/<?php echo $section['page_section_id']; ?>">
                                            <div style="display:grid">
                                                <input type="file" name="file[]" multiple value="" required>
                                                <span style="margin:10px 0px 10px 0px;">Hold control button to select and upload multiple images at once.</span>
                                            </div>
                                            <input id="name" name="description" placeholder="Description" class="text medium" /><br>
                                            <input type="submit" name="submit" value="Add Image"></b>
                                        </form>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>


        <?php
                break;


            case "Events":
                $events++;

                ?>
            <div class="divider  typeSpecific showManagedPage""></div>
        <h2>Events <?php echo $events; ?></h2>

        <div class="tab_content">
            <div class="hidden_table box-table-content">

                <?php if (count($section['events']) > 0): ?>
                <p><strong>Existing Events</strong></p>

                    <table width="100%" cellpadding="10" id="page_calendar_events<?php echo $section['page_section_id']; ?>">
                        <thead>
                        <tr>
                            <th><b>Title</b></th>
                            <th><b>Start</b></th>
                            <th><b>End</b></th>
                            <th><b>Location</b></th>
                            <th><b>Extra Info</b></th>
                            <th><b>Actions</b></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($section['events'] as $e): ?>
                            <tr>
                                <td><?php echo $e["title"]; ?></td>
                                <td><?php echo $e["start"] > "0000-00-00 00:00:00" ? date("m/d/Y g:i a", strtotime($e["start"])) : ""; ?></td>
                                <td><?php echo $e["end"] > "0000-00-00 00:00:00" ? date("m/d/Y g:i a", strtotime($e["end"])) : ""; ?></td>
                                <td><?php
                                    $leading = false;
                                    if ($e["address1"] != "") {
                                        echo $e["address1"];
                                        $leading = true;
                                    }
                                    if ($e["address2"] != "") {
                                        if ($leading) {
                                            echo ", ";
                                        }
                                        $leading = true;
                                        echo $e["address2"];
                                    }
                                    if ($e["city"] != "") {
                                        if ($leading) {
                                            echo ", ";
                                        }
                                        $leading = true;
                                        echo $e["city"];
                                    }
                                    if ($e["state"] != "") {
                                        if ($leading) {
                                            echo ", ";
                                        }
                                        $leading = true;
                                        echo $e["state"];
                                    }
                                    if ($e["zip"] != "") {
                                        echo " ";
                                        echo $e["zip"];
                                    }

                                    ?></td>
                                <td><?php if ($e["url"] != ""): ?>
                                <a href="<?php echo $e["url"]; ?>" target="_blank">Additional Info URL</a>
                                <?php endif; ?></td>
                                <td>Edit | <a href="/pages/calendar_removeEvent/<?php echo $pageRec['id']; ?>/<?php echo $section['page_section_id']; ?>/<?php echo $e["page_calendar_event_id"]; ?>"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a></td>

                            </tr>

                        <?php endforeach; ?>
                        </tbody>
                    </table>


                <script type="application/javascript">
                    $(window).load(function() {
                        $("#page_calendar_events<?php echo $section['page_section_id']; ?>").dataTable({
                            "processing" : true,
                            "paging" : true,
                            "info" : true,
                            "stateSave" : true,
                            "order": [[ 0, "desc" ]],
                            "columns" : [
                                null,
                                { "width" : "15%", "type" : "datetime" },
                                { "width" : "15%", "type" : "datetime" },
                                null,
                                null,
                                null
                            ]
                        });

                    });
                    </script>

                <?php endif; ?>


                <p><strong>Add Event</strong></p>
                <form method="post" action="/pages/calendar_addEvent/<?php echo $pageRec['id']; ?>/<?php echo $section['page_section_id']; ?>" class="form_standard">
                <table width="auto" cellpadding="12">
                    <tr>
                        <td valign="top"><strong>Title:</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="title" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Description:</strong></td>
                        <td valign="top"><textarea name="description" cols="80" rows="15"></textarea></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Start Date/Time:</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="start" class="enhancedDateSelector" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>End Date/Time:</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="end" class="enhancedDateSelector" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Additional Info Link URL:</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="url" /></td>
                    </tr>
                    <tr>
                        <td colspan="2"><em>Location</em></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Address</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="address1" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Address 2</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="address2" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>City</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="city" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>State</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="state" /></td>
                    </tr>
                    <tr>
                        <td valign="top"><strong>Zip</strong></td>
                        <td valign="top"><input type="text" size="40" maxlength="255" name="zip" /></td>
                    </tr>
                </table>
                    <input type="submit" name="submit" value="Add Event">
                </form>
            </div>
        </div>

        <script type="application/javascript">
        $(document).ready(function() {
            $(".enhancedDateSelector").jqxDateTimeInput({
                "showTimeButton" : true,
                width: '350px',
                height: '25px',
                formatString: 'F'
            });
        })
        </script>



            <?php

                break;


                        case "Slider":
                            $slider++;
            ?>
            <div class="divider  typeSpecific showManagedPage""></div>
        <h2>Slider <?php echo $slider; ?></h2>
        <?php echo form_open_multipart('pages/addImages/', array('class' => 'form_standard', 'id' => 'admin_banner_form')); ?>
        <?php echo form_hidden('page', $pageRec['id']); ?>
        <?php echo form_hidden('page_section_id', $section['page_section_id']); ?>
        <?php echo form_hidden('order', $slider); ?>
        <div class="tab_content">
            <div class="hidden_table">
                <table width="auto" cellpadding="12">
                    <tr>
                        <td colspan="3">
                            <strong>Banner Display Time:</strong> <input type="text" name="slider_seconds" value="<?php echo $section['slider_seconds']; ?>" /> seconds
                        </td>
                    </tr>
                    <tr >
                        <td colspan="3">
                            <button type="submit" id="button" name="submit" value="updateSliderTime">Update Display Time</button>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="3">Images must be 1024px wide by 400px high.<br /><br />
                            <?php echo form_upload(array('name' => 'image', 'value' => set_value('main'), 'maxlength' => 50, 'class' => '')); ?><br />
                            <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Upload New Banner</button>
                            <button type="button" id="button" class="banner-library<?php echo $slider; ?>">Banner Library</button>
                        </td>
                    </tr>
                    <tr class="slider-banners<?php echo $slider; ?>" style="display: none;">
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
                                    $file_type = explode('.', $file);
                                    // $file_type = end($file_type);
                                    $file_type = strtolower($file_type[count($file_type) - 1]);

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
                    <tr class="slider-banners<?php echo $slider; ?>" style="display: none;">
                        <td colspan="3">
                            <button type="submit" id="button" name="submit" value="addBanner">Add Banner</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='3'>
                            <table width='100%' id="sortableBanner<?php echo $slider; ?>" class="sortableBannerTable">
                                <?php if ($section["sliders"]): $bannerImages = $section["sliders"]; foreach ($bannerImages as $img): ?>
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
                            <input type="hidden" name="ordering" id="orderSort<?php echo $slider; ?>"/>                                                                                                <button type="submit" id="button" name="submit" value="saveLink">Submit</button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        </form>
        <script type="application/javascript">
            jQuery('.banner-library<?php echo $slider; ?>').click(function () {
                jQuery('.slider-banners<?php echo $slider; ?>').toggle('slow');
            });
        </script>
        <script type="application/javascript">
            $(function () {
                $("#sortableBanner<?php echo $slider; ?> tbody").sortable();
                $("#sortableBanner<?php echo $slider; ?> tbody").disableSelection();
                var data = "";

                $("#sortableBanner<?php echo $slider; ?> tbody tr").each(function(i, el){
                    //alert(i);
                    //alert($(el).attr('id'));
                    var p = $(el).text().toLowerCase().replace(" ", "_");
                    //alert(p);
                    data += $(el).attr('id')+"="+$(el).index()+",";

                });

                var dta = data.slice(0, -1);
                $("#orderSort<?php echo $slider; ?>").val(dta);
            });
            $(document).ready(function(){
                $("#sortableBanner<?php echo $slider; ?> tbody").sortable({
                    stop: function(event, ui) {
                        var data = "";

                        $("#sortableBanner<?php echo $slider; ?> tbody tr").each(function(i, el){
                            //alert(i);
                            //alert($(el).attr('id'));
                            var p = $(el).text().toLowerCase().replace(" ", "_");
                            //alert(p);
                            data += $(el).attr('id')+"="+$(el).index()+",";

                        });

                        var dta = data.slice(0, -1);
                        $("#orderSort<?php echo $slider; ?>").val(dta);

                    }
                });
            });
        </script>
        <?php


        break;

                    }
                }
            }


            ?>

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
//		$(".draggable").draggable({
//		    connectToSortable: '#sortable',
//		    helper: 'clone',
//		    revert: 'invalid'
//		});
	
	$("ul, li").disableSelection();    
	
	function removeWidget(item)
	{
		$(item).parent().remove();
	}

	function addWidget(type) {
	    $("#sortable").append('<li class="draggable ui-state-highlight ui-draggable ui-draggable-handle" style="display: list-item;"><input type="hidden" value="' + type + '" name="page_section_ids[]">New ' + type + '<a class="dragRemove" onclick="removeWidget(this);" href="javascript:void(0);" style="display: inline;">x</a>');
    }



    </script>

<style>
    .ui-sortable-placeholder {
        border: 2px dashed yellow;
        background-color: #ffeeee;
    }

    .sortableBannerTable{
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
