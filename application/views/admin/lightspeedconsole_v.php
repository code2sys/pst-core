<div class="content_wrap">
    <div class="content">
        <h1>Lightspeed Console</h1>

        <p>The idea is that this gives you the functionality that you expect from the remote interface. This should allow you to test things and compare to the rest of the admin interface.</p>


        <form>
            <p><label>Username: </label> <input type="text" name="username" size="20" value="<?php echo htmlentities($lightspeed_feed_username); ?>"/></p>
            <p><label>Password: </label> <input type="text" name="password" size="20" value="<?php echo htmlentities($lightspeed_feed_password); ?>" /></p>
            <p>URL: <?php echo site_url('lightspeedparts'); ?></p>
            <p>Mode: <label><input type="radio" name="content_type" value="text/xml">XML</label> <label><input type="radio" checked="checked" name="content_type" value="application/json">JSON</label></p>
        </form>

        <button id="version_discovery_service">Version Discovery Service</button>



        <p>Raw Output</p>

        <div id="rawOutput" style="overflow-y: auto; height: 400px; padding: 12px; background-color: #eeeeee; border: 2px dashed black"><pre></pre></div>

    </div>
</div>
<script type="application/javascript">
    function xmlToString(xmlData) {

        var xmlString;
        //IE
        if (window.ActiveXObject){
            xmlString = xmlData.xml;
        }
        // code for Mozilla, Firefox, Opera, etc.
        else{
            xmlString = (new XMLSerializer()).serializeToString(xmlData);
        }
        return xmlString;
    }

    (function() {



        $("#version_discovery_service").on("click", function(e) {
            e.preventDefault();
            e.stopPropagation();

            // Make an AJAX call to the base URL and then put the result into Raw Output
            $.ajax({
                url: "<?php echo site_url('lightspeedparts'); ?>",
                headers: {
                    "Authorization" : "Basic " + btoa($("input[name='username']").val() + ":" + $("input[name='password']").val()),
                    "Content-Type" : $("input[name='content_type']:checked").val()
                },
                method: 'POST',
                data: {},
                success: function(data) {
                    console.log(['Success', data]);
                    if ($("input[name='content_type']:checked").val() == "text/xml") {
                        $("#rawOutput pre").text(xmlToString(data));
                        console.log("xml");
                        console.log(xmlToString(data));
                    } else {
                        $("#rawOutput pre").text(JSON.stringify(data));
                    }
                },
                error: function(data) {
                    console.log(['Error', data]);
                    $("#rawOutput pre").text("ERROR: " + data.responseText);
                }
            })
        })

    })();
</script>
