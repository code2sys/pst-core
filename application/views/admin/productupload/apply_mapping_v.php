
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-upload"></i>&nbsp;File Upload - <?php echo $productupload["original_filename"]; ?> - Applying Mapping to Rows</h1>
        <p><b>Please map the columns from your CSV file to the known column list.</b></p>
        <br>

        <!-- VALIDATION ALERT -->
        <?php if (@$errors): ?>
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

        <div style="padding-top: 5em; padding-bottom: 5em; text-align: center">


            <img src="data:image/gif;base64,R0lGODlhNgA3APMAAP///3h4eBwcHA4ODtjY2FRUVNzc3MTExEhISIqKigAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQECgD/ACwAAAAANgA3AAAEzBDISau9OOvNu/9gKI5kaZ7lgQwDcqBnws5DApMHTb93uOosRC8EpA1Bxdnx8wMKl51ckXckFFgFAkamsy0JAppAe1EFqZaAQBAYXXUFU4DWJhZN4ZlAlMSLRW80cSVzM3UgBnksAgYnamwkBm8FjVCWl5iZmpucnZ4cj4eWoRqFLKJHpgOoFIoDe5ausBeyl7UYqqw9uaVrukOkn8LDxMXGx8ibwY6+JLxydCO3JdMg1dJ/Is+E0SPLcs3Jnt/F28XXw+jC5uXh4u89EQAh+QQFCgAKACwLAAUAJgARAAAEZhDISau9OGdSxiiEJmqE4HlCOK5Ud3oFKwPvO7P1ea/uG+8ig+kkMFwOCA/isDO4CkZLIpeYBASCAHByyHmYgZd2m/QiAEPUlub1oF+CddsdPo2B5dwZcM2uAV1eTH9SVIQYSEqDEQAh+QQFCgAKACwaAAYAHAAdAAAEbhDISau9MggRsLfBIA7dZwrjIJhnurJeOJYwpnF1jhGFWBC6CgE1EgCDkl5qUEBKliMnACqSKlNNp4EoEhikAIOy8AWbz+j0+YAQIQ7gBDThPFAH8GCbikDeq0F/A0h7UH1BdlR5QXJLdFJsbnkRACH5BAUKAAoALCcADQAPACMAAARoEMhJ6wxCBEvD+MPGAQI4CCNpoqMHim0Gp3Rt33iuS0TxFYQRoQQSBC0+06DAUYKazgEnaWJaDMSPwDAyJAvcnXicOiA+iMMo4UxYDtGBmnKOIirxDz5fqTvvFHBRcxRsSm4cZmiEFBEAIfkEBQoACgAsGQAaABwAHQAABGwQyEmrvTjXIETQoBQM5PCFmVAOApqurXuN5SlbnHfvfO//wKDQQiiQCoQfQVUSJHvG1aDgk5aq1oEvuqJaDggS4nAxMEkCgyVhTZSjBXXlkB2Qd+Esglcn8fs8eVZ7O3RZdztsUm4+YGKIOxEAIfkEBQoACgAsCgAmACYAEQAABGoQgIPGQEfqzbvfiSUOyWee0jGOGXoGghBI1WohrhmMsz3mH8FIAPCJgB6hiFiz4ZCcnWim8rWgG5hME1qVPISCpUDAUm5XDkFpEZSxLvGqAHcZ66g73iQf0fcfBmwDAgaAJgZyBYaHjRwRACH5BAUKAAoALAUAGQAdAB0AAARsEICDxkBH6s27TFY4JF7JHaKYmWaVWghrvqJc0qHtuW+sn7jVbwNKkYYdCkyIbDqf0Ki0RChYCgQpQSASZKHWVCGKI9OiYdEYauCGBAapIVyIT4cBgSBwD4j4Um4WAlOCA4RSfiGAiXqMdxsRACH5BAUKAAoALAUADAAPACMAAARkEMgJDhoDHUoT/kPCVSC4UVeJIZwKti7GpSpLHfHZqeJYpRqfcEgsGn2EAqZAEBIEIEFzpCwVfDGsy1cFXUcG6EdgEBqqhfJxzW6738WAQBAgBkB1oRgjGO4HfUJ3H3mCc4VsEQA7" alt="Loading"/>
            <p><strong>Applying Mapping...</strong><br/>We are now applying the column selections to each row and verifying whether it is a new, updated, or invalid row.</p>
            <p>Please keep this window open while this upload is processed.</p>
            <p><?php echo number_format($mapped_rows, 0); ?> of <?php echo number_format($total_rows, 0); ?> Rows Mapped</p>
            <p>If this page does not refresh in a few seconds, <a href="<?php echo base_url('adminproductuploader/applyMapping/' . $productupload_id); ?>">click here.</a></p>

        </div>



    </div>
</div>
<script type="application/javascript">
    (function() {
        setTimeout(function() {
            window.location.href = "<?php echo base_url('adminproductuploader/applyMapping/' . $productupload_id); ?>/" + Date.now();
        }, 10000);
    })();
</script>

