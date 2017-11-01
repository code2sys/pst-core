
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-upload"></i>&nbsp;File Upload - <?php echo $productupload["original_filename"]; ?> - Confirm</h1>
        <p><b>Please confirm to finish processing your upload.</b></p>
        <ul>
            <li><strong>New Parts: </strong> <?php echo number_format($new, 0); ?> <?php if ($new > 0): ?>(Download)<?php endif; ?></li>
            <li><strong>Updated Parts: </strong> <?php echo number_format($update, 0); ?> <?php if ($update > 0): ?>(Download)<?php endif; ?></li>
            <li><strong>Rejected Rows: </strong> <?php echo number_format($reject, 0); ?> <?php if ($reject > 0): ?>(Download)<?php endif; ?></li>
        </ul>
        <br>

        <form method="post" action="<?php echo base_url('adminproductuploader/save_confirm/' . $productupload_id); ?>" >
            <button id="button" type="submit">Confirm and Process</button>
        </form>

        <p><a href="<?php echo base_url('adminproductuploader/index'); ?>">Click here to upload a new file.</a></p>
    </div>
</div>