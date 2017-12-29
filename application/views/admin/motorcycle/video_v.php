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
            "active" => "video",
            "descriptor" => "Videos",
            "source" => @$product["source"]
        ), true);

        ?>

        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table">

                <?php echo form_open_multipart('admin/motorcycle_video/' . $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?> 
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
                        <?php foreach ($product_video as $key => $val) { ?>
                            <tr>
                                <td>
                                    <input id="video_url" name="video_url[<?php echo $key; ?>]" value="<?php echo 'https://www.youtube.com/watch?v=' . $val['video_url']; ?>" placeholder="Enter video URL" class="text large" style='height:30px;'/>
                                </td>
                                <td>
                                    <input id="title" name="title[<?php echo $key; ?>]" value="<?php echo $val['title']; ?>" placeholder="Enter video Title" class="text large" style='height:30px;'/>
                                </td>
                                <td>
                                    <input id="ordering" name="ordering[<?php echo $key; ?>]" value="<?php echo $val['ordering']; ?>" placeholder="Ordering" class="text large" type='number' min='1' style='height:30px;'/>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td>
                                <?php echo form_hidden('part_id', $id); ?>
                                <input id="video_url" name="video_url[]" value="" class="text small" placeholder="Enter video URL" style='height:30px;'/>
                            </td>
                            <td>
                                <input id="title" name="title[]" value="" class="text small" placeholder="Enter video Title" style='height:30px;'/>
                            </td>
                            <td>
                                <input id="ordering" name="ordering[]" value="" class="text small" placeholder="Ordering" type='number' min='1' style='height:30px;'/>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
        <!-- END TAB CONTENT -->
        <br>

        <!-- SUBMIT PRODUCT -->
        <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>

        <!-- SUBMIT DISABLED 
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>
        
        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>
<script>
    $(document).on('click', '.add-row', function (e) {
        var str = "<tr><td><input type='text' name='video_url[]' value='' class='text large' placeholder='Enter video URL' style='height:30px;'></td><td><input id='title' name='title[]' value='' class='text large' placeholder='Enter video Title' class='text medium' style='height:30px;'/></td><td><input type='number' value='' name='ordering[]' class='text small' placeholder='Ordering' min='1' style='height:30px;'</td></tr>";
        $('.tbdy').append(str);
    });
</script>


</div>
<!-- END WRAPPER =========================================================================================-->
