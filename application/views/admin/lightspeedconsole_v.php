<div class="content_wrap">
    <div class="content">
        <h1>Lightspeed Console</h1>

        <p>The idea is that this gives you the functionality that you expect from the remote interface. This should allow you to test things and compare to the rest of the admin interface.</p>


        <form>
            <p><label>Username: </label> <input type="text" name="username" size="20" value="<?php echo htmlentities($lightspeed_feed_username); ?>"/></p>
            <p><label>Password: </label> <input type="text" name="password" size="20" value="<?php echo htmlentities($lightspeed_feed_password); ?>" /></p>
            <p>URL: <?php echo site_url('lightspeedparts'); ?></p>
            <p>Mode: <label><input type="radio" name="content_type" value="text/xml">XML</label> <label><input type="radio" name="content_type" value="application/json">JSON</label></p>
        </form>

        <button id="version_discovery_service">Version Discovery Service</button>



        <p>Raw Output</p>

        <div id="rawOutput" style="overflow-y: auto; height: 400px; padding: 6px; color: #eeeeee; border: 2px dashed black"></div>

    </div>
</div>
<script type="application/javascript">
    (function() {

        $("#version_discovery_service").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Make an AJAX call to the base URL and then put the result into Raw Output

        })

    })();
</script>
