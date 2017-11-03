<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-upload"></i>&nbsp;Upload Multiple Products</h1>
        <p><b>You can upload multiple products via CSV file here.</b></p>
        <p>To get started, <a href="/assets/Multiple_Product_Upload_Template.csv" download="Multiple_Product_Upload_Template.csv">download our starting template</a>. Be sure to upload your file in CSV format.</p>
        <br>

        <!-- VALIDATION ALERT -->
        <?php if (validation_errors() || @$errors): ?>
            <div class="validation_error" id="login_validation_error">
                <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                <h1>Error</h1>
                <div class="clear"></div>
                <p><?php echo $errors; ?></p>

            </div>
        <?php endif; ?>
        <!-- END VALIDATION ALERT -->

        <!-- SUCCESS -->
        <?php if(@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <div class="clear"></div>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->

        <div class="nav nav-tabs tab">
            <ul>
                <li class="active"><a  data-toggle="tab" href="#upload" class="active">Upload</a></li>
                <li><a data-toggle="tab" href="#columns">Columns</a></li>
                <li><a data-toggle="tab" href="#history">History</a></li>
            </ul>
        </div>
        <div style="clear: both"></div>
    <div class="tab_content">
        <div class="tab_pane active tab_upload upload" id="upload">

            <form method="post" action="<?php echo base_url('adminproductuploader/upload'); ?>" enctype="multipart/form-data">
                <strong>Select CSV File</strong><br/>
                <p>Select a CSV file with columns as described on the columns tab. Limit: 1 MB</p>
                <input type="file" name="upload"><br/>

                <button id="button" type="submit">Upload File</button>
            </form>
            <p></p>
            <div style="clear: both"></div>
        </div>
        <div class="tab_pane fade tab_columns columns" id="columns" style="display: none">

            <p>Please include the following columns in your CSV file.</p>

            <table>
                <thead>
                    <th>Column Name</th>
                    <th>Required</th>
                    <th>Description</th>
                </thead>
                <tbody>
                    <?php foreach ($columns as $c): ?>
                    <tr>
                        <td valign="top"><strong><?php echo $c["label"]; ?></strong></td>
                        <td valign="top" align="center"><?php echo $c["required"] ? "Yes" : "No"; ?></td>
                        <td valign="top"><?php echo $c["description"]; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>


            </table>

        </div>
        <div class="tab_pane fade tab_history history" id="history" style="display: none">

            <p>
                Uploads have four status codes:
            </p>

            <ul>
                <li>Uploaded - File has been received, but no processing has happened.</li>
                <li>Columns Assigned - You have identified the columns in the upload file.</li>
                <li>Mapped - The uploaded file has been sorted into new, updated, and rejected products.</li>
                <li>Approved - You have confirmed the rows for new, updated, and rejected products.</li>
                <li>Processed - This file has been completely processed.</li>
            </ul>

            <p><strong>If the upload status is not "Processed", please click the "Resume Processing" link to resume processing.</strong></p>

            <table id="history" width="100%">
                <thead>
                <th>Filename</th>
                <th>Uploaded</th>
                <th>Last Updated</th>
                <th>Status</th>
                <th>Row Count</th>
                <th># New</th>
                <th># Updated</th>
                <th># Rejected</th>
                <th>Action</th>
                </thead>
                <tbody>
                <?php
                usort($uploads, function($x, $y) {
                    return $x["last_update"] < $y["last_update"] ? 1 : ($x["last_update"] > $y["last_update"] ? -1 : 0);
                });
                ?>
                <?php foreach ($uploads as $u): ?>
                    <tr>
                        <td valign="top"><?php echo $u['original_filename']; ?></td>
                        <td valign="top" align="center"><?php echo date("m/d/Y g:i a T", strtotime($u['created'])); ?></td>
                        <td valign="top" align="center"><?php echo date("m/d/Y g:i a T", strtotime($u['last_update'])); ?></td>
                        <td valign="top" align="center"><?php echo $u["status"]; ?></td>
                        <td valign="top" align="center"><a href="<?php echo base_url('adminproductuploader/download/' . $u['productupload_id'] . '/all'); ?>" class="fa fa-download"><i></i> <?php echo $u["upload_row_count"]; ?></a></td>
                        <td valign="top" align="center"><?php if ($u['status'] != 'Uploaded'): ?><?php if ($u['new_row_count'] > 0): ?><a href="<?php echo base_url('adminproductuploader/download/' . $u['productupload_id'] . '/new'); ?>" class="fa fa-download"><i></i> <?php echo $u["new_row_count"]; ?></a><?php else: ?>0<?php endif; ?><?php endif; ?></td>
                        <td valign="top" align="center"><?php if ($u['status'] != 'Uploaded'): ?><?php if ($u['update_row_count'] > 0): ?><a href="<?php echo base_url('adminproductuploader/download/' . $u['productupload_id'] . '/update'); ?>" class="fa fa-download"><i></i> <?php echo $u["update_row_count"]; ?></a><?php else: ?>0<?php endif; ?><?php endif; ?></td>
                        <td valign="top" align="center"><?php if ($u['status'] != 'Uploaded'): ?><?php if ($u['reject_row_count'] > 0): ?><a href="<?php echo base_url('adminproductuploader/download/' . $u['productupload_id'] . '/reject'); ?>" class="fa fa-download"><i></i> <?php echo $u["reject_row_count"]; ?></a><?php else: ?>0<?php endif; ?><?php endif; ?></td>
                        <td valign="top" align="center"><?php if ($u["status"] != "Processed"): ?><a href="<?php echo base_url('adminproductuploader/resume/' . $u['productupload_id']); ?>">Resume Processing</a><?php endif; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>


            </table>


        </div>

        </div>




    </div>
</div>

<script type="application/javascript">
    /*
        This admin frame needs to be upgraded. I'm re-inventing the wheel.
     */

    $('[href="#upload"]').on("click", function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // set it active
        $('[href="#upload"]').addClass("active");
        $('[href="#columns"]').removeClass("active");
        $('[href="#history"]').removeClass("active");

        // change what's visible
        $("#upload").show();
        $("#columns").hide();
        $("#history").hide();

    });


    $('[href="#columns"]').on("click", function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // set it active
        $('[href="#upload"]').removeClass("active");
        $('[href="#columns"]').addClass("active");
        $('[href="#history"]').removeClass("active");

        // change what's visible
        $("#upload").hide();
        $("#columns").show();
        $("#history").hide();

    });


    $('[href="#history"]').on("click", function(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // set it active
        $('[href="#upload"]').removeClass("active");
        $('[href="#columns"]').removeClass("active");
        $('[href="#history"]').addClass("active");

        // change what's visible
        $("#upload").hide();
        $("#columns").hide();
        $("#history").show();

    });


</script>
