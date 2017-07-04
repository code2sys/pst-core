<?php
$mainCats = array("20416", "20419", "20409", "20422", "69597");
?>
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">		
        <div style="width:45.6%; float:left;">
            <h1><i class="fa fa-tags"></i>&nbsp;Category</h1>
                    <!--<p><b>View, edit and create a new Brand.</b></p>-->
            <br>

            <!-- ADD NEW / PAGINATION -->
            <?php if (@$pagination): ?>
                <h3 style="float:right;margin-top:20px;">
                    <a href=""><i class="fa fa-chevron-circle-left"></i></a>
                    <a href="">1</i></a>
                    <a href="">2</i></a>
                    <a href="">3</i></a>
                    <a href="">4</i></a>
                    <a href="">5</i></a>
                    <a href="">6</i></a>
                    <a href=""><i class="fa fa-chevron-circle-right"></i></a>
                </h3>

            <?php endif; ?>
            <div class="clear"></div>
            <!-- END ADD NEW / PAGINATION -->

            <!-- PRODUCT LIST -->
            <div class="tabular_data">
                <table width="100%" cellpadding="10">
                    <tr class="head_row">
                            <!--<td><b>#</b></td>-->
                        <td><b>Name</b></td>
                        <td><b>Actions</b></td>
                    </tr>
                    <?php
                    if (@$cate): $i = 0;
                        foreach ($cate as $key => $cate):
                            ?>
                            <tr>
                                    <!--<td><?php echo $key; ?></td>-->
                                <td><?php echo $cate['name']; ?></td>
                                <!--<td><b><?php echo @$parent_brands[$cate['parent_category_id']]; ?></b></td>-->
                                <td>
                                    <a href="javascript:void(0);" onclick="populateEdit('<?php echo $cate['category_id']; ?>');"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a></a>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                </table>
            </div>
            <!-- END PRODUCT LIST -->

            <!-- ADD NEW / PAGINATION -->

<?php if (@$pagination): ?>
                <h3 style="float:right;margin-top:5px;">
                    <a href=""><i class="fa fa-chevron-circle-left"></i></a>
                    <a href="">1</i></a>
                    <a href="">2</i></a>
                    <a href="">3</i></a>
                    <a href="">4</i></a>
                    <a href="">5</i></a>
                    <a href="">6</i></a>
                    <a href=""><i class="fa fa-chevron-circle-right"></i></a>
                </h3>

<?php endif; ?>
            <div class="clear"></div>
        </div>	


        <div style="width:45.6%; float:left; margin-left:10px;">
            <br />
            <br />
            <br />
            <br />
            <br />
            <br />
            <!-- PHP ALERT -->

            <!-- ERROR -->
<?php if (validation_errors() || @$errors): ?>
                <div class="error">
                    <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                    <p><?php echo validation_errors() . @$errors; ?> </p>
                </div>
<?php endif; ?>
            <!-- END ERROR -->

            <!-- SUCCESS -->
<?php if (@$success): ?>
                <div class="success">
                    <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                    <p><?php echo $success; ?></p>
                </div>
<?php endif; ?>
            <!-- END SUCCESS -->	

            <!-- TABS -->
            <div class="tab">
                <ul>
                    <li><a href="<?php echo base_url('admin/category'); ?>" class="active"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
                    <li><a href="<?php echo base_url('admin/category_image/' . $id); ?>"><i class="fa fa-image"></i>&nbsp;Image*</a></li>
                    <?php if(!in_array($id, $mainCats)) { ?>
                        <li><a href="<?php echo base_url('admin/category_video'); ?>" class="image_link"><i class="fa fa-image"></i>&nbsp;Videos*</a></li>
                    <?php } ?>
                    <div class="clear"></div>
                </ul>
            </div>
            <!-- END TABS -->

            <?php
            $attributes = array('id' => 'categoryEditForm', 'class' => 'form_standard');
            echo form_open_multipart('admin/category', $attributes);
            ?>

            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
                        <tr>
                            <td style="width:130px;"><b>Parent Category:</b></td>
                            <td>
                                <span id="prnt-nm"></span>
<?php echo form_dropdown('parent_category_id', $parent_categories, '', 'id="parent_brand" '); ?>
                                <input type="hidden" name="category_id" id="category_id">
                            </td>
                        </tr>
                        <tr>
                            <td><b>Featured:</b></td>
                            <td>
<?php echo form_checkbox('featured', 1, '', 'id="featured"'); ?>
                            </td>
                        </tr>
                        <tr>
                            <td><b>Name:</b></td>
                            <td>
                                <input id="name" name="name" value="" class="text large" placeholder="Enter Name" class="text medium" />
                            </td>
                        </tr>
                        <tr class="ttl">
                            <td><b>Title:</b></td>
                            <td>
                                <input id="title" name="title" value="" class="text large" placeholder="Enter Title" class="text medium" />
                            </td>
                        </tr>
                        <tr class="description">
                            <td><b>Meta Description:</b></td>
                            <td>
                                <input id="meta_tag" name="meta_tag" value="" class="text medium" placeholder="Enter Meta Tag" />
                            </td>
                        </tr>
                        <tr class="kywrds">
                            <td><b>Meta Keywords:</b></td>
                            <td>
                                <input id="keywords" name="keywords" value="" class="text medium" placeholder="Enter Keywords" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Mark-up Percentage:</b>
                                <p style="font-size:10px; line-height:8px">Enter 0 to return the items to suggested retail price.</p>
                            </td>
                            <td>
                                <input id="mark-up" name="mark-up" value="" class="text medium" placeholder="Enter Mark-up Percentage" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Google Category Number:</b>
                            </td>
                            <td>
                                <input id="google_category_num" name="google_category_num" value="" class="text medium" placeholder="Enter Google Category Number" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Ebay Category Number:</b>
                            </td>
                            <td>
                                <input id="ebay_category_num" name="ebay_category_num" value="" class="text medium" placeholder="Enter Ebay Category Number" />
                            </td>
                        </tr>
                        <tr class="notice">
                            <td>
                                <b>Notice:</b>
                                <p style="font-size:10px; line-height:8px">This will appear at the bottom of the category page.</p>
                            </td>
                            <td>
                                <?php
                                echo form_textarea(array('name' => 'notice',
                                    'value' => set_value('notice'),
                                    'id' => 'notice',
                                    'placeholder' => 'Category Notice',
                                    'style' => 'height:100px; width:80%;'));
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            </form>	

            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->			
            <a href="javascript:void(0);" onclick="submitForm()" id="button" class="hide edit"><i class="fa fa-plus"></i>&nbsp;Edit Category</a>


        </div>
    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->


<script>

    function submitForm()
    {
        $("#categoryEditForm").submit();
    }

    populateEdit("<?php echo $id; ?>");

    function populateEdit(id)
    {
        var categories = ["20416", "20419", "20409", "20422", "69597"];
        $.post(base_url + 'admin/load_category_rec/' + id,
                {},
                function (encodeResponse)
                {
                    responseData = JSON.parse(encodeResponse);

                    if (responseData['error'] == true)
                    {
                        $('#val_error_message').html(responseData['error_message']);
                        $('#val_container').fadeIn();
                        $('#simplemodal-container').height('auto');
                        setTimeout(function () {
                            $('#val_container').fadeOut(1000, function () {});
                        }, 2000);
                    } else
                    {
                        console.log(responseData);
                        $(".image_link").each(function () {
                            //alert($(this).html());
                            var href = $($(this)).attr("href");
                            $($(this)).attr("href", href + '/' + responseData['category_id']);
                            //alert($(this).text());
                        });

                        if (jQuery.inArray(id, categories) >= 0) {
                            $('.cat-video').hide();
                            $('.ttl').hide();
                            $('.description').hide();
                            $('.kywrds').hide();
                            $('.notice').hide();
                        } else {
                            $('.cat-video').show();
                            $('.ttl').show();
                            $('.description').show();
                            $('.kywrds').show();
                            $('.notice').show();
                        }

                        $('.edit').show();
                        $('.new').hide();
                        $('#category_id').val(responseData['category_id']);
                        $('#parent_brand').val(responseData['parent_category_id']);
                        $('#prnt-nm').html($('option:selected', $('#parent_brand')).html());
                        if (responseData['featured'] == 1)
                            $('#featured').prop('checked', true);
                        if (responseData['featured'] == 0)
                            $('#featured').prop('checked', false);

                        $('#name').val(responseData['name']);
                        $('#title').val(responseData['title']);
                        $('#meta_tag').val(responseData['meta_tag']);
                        $('#keywords').val(responseData['keywords']);
                        $('#mark-up').val(responseData['mark_up']);
                        $('#google_category_num').val(responseData['google_category_num']);
                        $('#notice').val(responseData['notice']);
                        $('#ebay_category_num').val(responseData['ebay_category_num']);
                    }
                });

    }

</script>
<style>
    #parent_brand {
        display:none;
    }
</style>