<script type="text/javascript" src="/assets/js/ckeditor/ckeditor.js"></script>

<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/motorcycle/moto_head", array(
            "new" => @$new,
            "product" => @$product,
            "success" => @$success,
            "assets" => $assets,
            "id" => @$id,
            "active" => "description",
            "descriptor" => "Description",
            "source" => @$product["source"]
        ), true);

        ?>


        <form class="form_standard" method="post">

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
                        <tr>
                            <td style="width:130px;" valign="top"><b>Description:</b></td>
                            <td>
                                <textarea id="editor1" name="descr" rows="6" placeholder="Enter Description" cols="50" style="width:100%;"><?php echo $product['description']?></textarea>
								<script type="text/javascript">
									// LOAD THE CUSTOM CONFIGURATION FOR THIS INSTANCE
									CKEDITOR.replace( 'editor1', { customConfig : '<?php echo $edit_config; ?>' } );
								</script>
                            </td>
                        </tr>
                    </table>

                </div>
            </div>
            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->
            <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>

            <!-- CANCEL BUTTON -->
            <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>

        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
