<script src="/assets/dropzone.js"></script>
<link rel="stylesheet" href="/assets/dropzone.css">
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-image"></i>&nbsp;Images for <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
        <?php endif; ?>

        <br>

        <!-- ERROR -->
        <?php if (validation_errors()): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <p><?php echo validation_errors(); ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <p>Success Message!</p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->


        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "product_images"
        ), true);
        ?>
        <!-- END TABS -->

        <form class="form_standard">

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table border="0" width="100%">
                        <tr>
                            <td valign="top" width="50%">
                                <h3>Existing Images</h3>


                                <div class="image_list_holder">


                                    <div style="clear: both"></div>
                                </div>

                                <div class="no_images">
                                    <strong><em>There are no images associated with this part.</em></strong>
                                </div>

                            </td>
                            <td valign="top" width="50%">
                                <h3>Upload Images</h3>

                                <p>Please provide your images in GIF, JPG, or PNG format only.</p>

                                <p><em>Drag-and-drop pictures to the gray box, or click the gray box to upload.</em></p>

                                <div action="/adminproduct/product_image_add/<?php echo $part_id; ?>" id="mydropzone"   style="width: 80%; margin-left: auto; margin-right: auto; text-align: center; min-height: 12em; border: 2px dashed blue; background: gray; ">
                                    </div>

                                </div>


                            </td>
                        </tr>



                    </table>

                </div>
            </div>

        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
<style>
    .image_list_holder .productimage {
        text-align: center;
        margin-left: 1em;
        margin-right: 1em;
        display: inline-block;
        width: 180px;
    }
</style>
<script type="application/javascript">
    $(window).on("load", function() {
        // populate any of our existing images
        var existing_images = <?php echo json_encode($images); ?>;

        var updateDescriptionFn = function(index) {
            var desc = $("#description_" + existing_images[index].partimage_id).val();
            $.ajax({
                "url" : "/adminproduct/product_image_update_description/<?php echo $part_id; ?>/" + existing_images[index].partimage_id,
                "type" : "POST",
                "dataType" : "json",
                "data" : {
                    "description" : desc
                }
            });
        };

        var removeImageFn = function(index) {
            $(".productimage[data-partimage-id=" + existing_images[index].partimage_id + "]").remove();
            $.ajax({
                "url" : "/adminproduct/product_image_remove/<?php echo $part_id; ?>/" + existing_images[index].partimage_id,
                "type" : "POST",
                "dataType" : "json"
            });
            showWarningFn();
        };

        var showWarningFn = function() {
            if ($(".image_list_holder .productimage").length > 0) {
                $(".no_images").hide();
            } else {
                $(".no_images").show();
            }
        };


        var addImageFn = function(index) {
            var clean_source = existing_images[index].path;
            if (/^store/.exec(clean_source)) {
                clean_source = clean_source.replace('store/', 'store/t');
            } else {
                clean_source = "t" + clean_source;
            }
            $(".image_list_holder").append("<div class='productimage' data-partimage-id='" + existing_images[index].partimage_id + "' ><a href='/productimages/" + existing_images[index].path + "' download='" + existing_images[index].original_filename + "'><img src='/productimages/" + clean_source + "'></a><br/><input type=text id='description_" + existing_images[index].partimage_id + "' value='" + (existing_images[index].description ? existing_images[index].description : "") + "' maxlength=255><br/><button class='update_button_" + index + "'>Update Description</button><br/><a href='#' class='remove_button_" + index + "'><i class='fa fa-remove'></i> Remove</a></div>");
            $(".image_list_holder .remove_button_" + index).on("click", function(e) {
                e.preventDefault();
                removeImageFn(index);
            });
            $(".image_list_holder .update_button_" + index).on("click", function(e) {
                e.preventDefault();
                updateDescriptionFn(index);
            });
            showWarningFn();
        };

        var i;
        for (i = 0; i < existing_images.length; i++) {
            addImageFn(i);
        }

        showWarningFn();


        $(".image_list_holder").sortable({
            change : function(event, ui) {
                setTimeout(function() {
                    // print them, in order.
                    var elements = $(".image_list_holder .productimage");
                    var ids_in_order = [];
                    for (var i = 0; i < elements.length; i++) {
                        if (elements[i].dataset.partimageId) {
                            ids_in_order.push(elements[i].dataset.partimageId);
                        }
                    }
                    console.log(["ids_in_order", ids_in_order]);

                    // now, post it.
                    $.ajax({
                        "url" : "/adminproduct/product_image_reorder/<?php echo $part_id; ?>",
                        "type" : "POST",
                        "data" : {
                            "ids_in_order" : ids_in_order
                        },
                        "dataType" : "json"
                    });
                }, 500);
            }
        });

        new Dropzone("#mydropzone");
        Dropzone.options.mydropzone = {
            maxFilesize: 2,
            acceptedFiles: "image/jpg,image/gif,image/png"
        };

        setTimeout(function() {
            var dropzone = Dropzone.forElement("#mydropzone");
            dropzone.on("success", function (file, resulttext) {
                console.log("Call to success");
                console.log(resulttext);
                try {
                    var data = JSON.parse(resulttext);
                    if (data.success) {
                        existing_images.push(data.partimage);
                        addImageFn(existing_images.length - 1);
                    } else {
                        alert(data.error_message);
                    }
                } catch (err) {
                    console.log("Dropzone success error: " + err + " on result " + resulttext);
                }
            });
            dropzone.on("complete", function (file) {
                try {
                    // remove the file.
                    dropzone.removeFile(file);

                } catch(err) {
                    console.log("Error: " + err);
                }
            });


        }, 4000);
    });
</script>