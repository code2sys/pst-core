	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-dashboard"></i>&nbsp;Page Settings</h1>
			<h3>Create your space!</h3>
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
			<?php if(@$success): ?>
			<div class="success" id="login_validation_success">
			  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
		    <h1>Success</h1>
		    <div class="clear"></div>
		    <p><?php echo $success; ?></p>
			</div>
			<?php endif; ?>
			<!-- END SUCCESS MESSAGE -->
			
			<a href="<?php echo base_url('pages/edit'); ?>" class="button">Create New</a>
			<div class="clear"></div>
			<div class="divider"></div>

            <!-- We have to make the checkboxes for filtering -->
            <?php if (isset($pages) && is_array($pages) && count($pages) > 0): ?>

            <div style="margin-bottom: 1em;">
                <strong>Filter by Type:</strong>
            <?php
            $seen_types = array();
            foreach ($pages as $page) {
                if (!in_array($page["page_class"], $seen_types)) {
                    $seen_types[] = $page["page_class"];
                    ?>
                        <label><input type="checkbox" name="pageclass" value="<?php echo $page["page_class"]; ?>" checked="true" class="filter_checkbox"><?php echo $page["page_class"]; ?></label>
                <?php
                }
            } ?>
            </div>


            <div class="tabular_data">
                <table width="100%" cellpadding="10" id="page_index_list">
                    <thead>
                    <tr>
                        <th><b>Active</b></th>
                        <th><b>Name</b></th>
                        <th><b>Type</b></th>
                        <th><b>Format</b></th>
                        <th><b>Actions</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($pages as $page): ?>
                    <tr>
                        <td><?php if (!$page['delete'] || $page['active']): ?>Yes<?php else: ?>No<?php endif; ?></td>
                        <td><?php echo htmlentities($page["label"], ENT_COMPAT | ENT_IGNORE); ?></td>
                        <td><?php echo $page["page_class"]; ?></td>
                        <td><?php echo $page["type"]; ?></td>
                        <td>
                            <a href="<?php echo base_url('pages/edit/'.$page['id']); ?>"><i class="fa fa-edit"></i>&nbsp;Edit</a>
                            <?php if ($page['delete']): ?>
                                <?php if ($page['active']): ?>
                                    | <a href="<?php echo base_url('pages/make_inactive/'.$page['id']); ?>" ><i class="fa fa-pause"></i>&nbsp;Make Inactive</a>
                                <?php else: ?>
                                    | <a href="<?php echo base_url('pages/make_active/'.$page['id']); ?>" "><i class="fa fa-play"></i>&nbsp;Make Active</a>
                                <?php endif; ?>
                                | <a href="<?php echo base_url('pages/delete/'.$page['id']); ?>" onClick="return confirm('Are you sure? This cannot be undone.'); "><i class="fa fa-times"></i>&nbsp;Delete</a>
                            <?php endif; ?>

                        </td>

                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php endif; ?>

            <script type="application/javascript">
                $(window).load(function() {
                    $.fn.dataTableExt.afnFiltering.push(function(oSettings, aData, iDataIndex) {
                        var val = aData[2];
                        return $("input[name='pageclass'][value='" + val + "']:checked").length > 0;
                    });

                    var oTable = $("#page_index_list").dataTable({
                        "processing" : true,
                        "paging" : true,
                        "info" : true,
                        "stateSave" : true,
                        "columns" : [
                            null,
                            null,
                            null,
                            null,
                            null
                        ]
                    });

                    $('input.filter_checkbox').on("click", function(e) {
                        oTable.fnDraw();
                        // you need to save the settings...
                    });

                    // we have to add a listener on those checkboxes..

                    // we have to load any existing settings for the filter..

                });
            </script>
			
		</div>
	</div>