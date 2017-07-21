<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div style="width:45.6%; float:left;">
            <h1><i class="fa fa-tags"></i>&nbsp;Brands</h1>
            <p><b>View, edit and create a new Brand.</b></p>
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
                        <td><b>#</b></td>
                        <td><b>Name</b></td>
                        <td><b>Actions</b></td>
                    </tr>
                    <?php if (@$brands): $i = 0;
                        foreach ($brands as $key => $brand): ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                <td><?php echo $brand['name']; ?></td>
                                <td>
                                    <a href="javascript:void(0);" onclick="populateEdit('<?php echo $brand['brand_id']; ?>');"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a>
                                    <?php if (!@$brand['mx']): ?>
                                        | <a href="<?php echo base_url('admin/brand_delete/' . $brand['brand_id']); ?>"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
        <?php endif; ?>
                                </td>
                            </tr>
    <?php endforeach;
endif; ?>
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
<?php if (validation_errors()): ?>
                <div class="error">
                    <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                    <p><?php echo validation_errors(); ?> </p>
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
                    <li><a href="<?php echo base_url('admin/brand'); ?>" class="active"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
                    <li><a href="<?php echo base_url('admin/brand_image'); ?>" class="image_link"><i class="fa fa-image"></i>&nbsp;Image*</a></li>
                    <li><a href="<?php echo base_url('admin/brand_video'); ?>" class="image_link"><i class="fa fa-image"></i>&nbsp;Videos*</a></li>
                    <li><a href="<?php echo base_url('admin/brand_sizechart'); ?>" class="image_link"><i class="fa fa-image"></i>&nbsp;Size Charts*</a></li>
					<li><a href="<?php echo base_url('admin/brand_rule/'.$id); ?>" class="image_link"><i class="fa fa-image"></i>&nbsp;Closeout Schedule*</a></li>
                    <div class="clear"></div>
                </ul>
            </div>
            <!-- END TABS -->

<?php $attributes = array('id' => 'brandEditForm', 'class' => 'form_standard');
echo form_open('admin/brand', $attributes);
?>
            <input type="hidden" name="brand_id" id="brand_id"></td>
            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
<!--                        <tr>-->
<!--                            <td><b>Active:</b></td>-->
<!--                            <td>--><?php //echo form_checkbox('active', 1, '', 'id="active"'); ?><!--</td>-->
<!--                        </tr>-->
                        <tr>
                            <td><b>Featured:</b></td>
                            <td>
<?php echo form_checkbox('featured', 1, '', 'id="featured"'); ?>
                            </td>
                        </tr>

                        <tr>
                            <td><b>Only display inventory if products are on closeout for Market Places:</b></td>
                            <td>

                                <input id="closeout_market_place" type="radio" name="market_places" value="closeout_market_place">
                            </td>
                        </tr>
                        <tr>
                            <td><b>Do not display inventory for Market Places:</b></td>
                            <td>
                                <input id="exclude_market_place" type="radio" name="market_places" value="exclude_market_place">
                            </td>
                        </tr>
                        <tr>
                            <td><b>Default state for Market Places:</b></td>
                            <td>
                                <input id="default_market_place" type="radio" name="market_places" value="default">
                            </td>
                        </tr>
                        <tr>
                            <td><b>Name:</b></td>
                            <td>
                                <input id="name" name="name" value="" class="text large" placeholder="Enter Name" class="text medium" />
                            </td>
                        </tr>
                        <tr>
                            <td><b>Title:</b></td>
                            <td>
                                <input id="title" name="title" value="" class="text large" placeholder="Enter Title" class="text medium" />
                            </td>
                        </tr>
                        <tr>
                            <td><b>Meta Description:</b></td>
                            <td>
                                <input id="meta_tag" name="meta_tag" value="" class="text medium" placeholder="Enter Meta Description" />
                            </td>
                        </tr>
                        <tr>
                            <td><b>Keywords:</b></td>
                            <td>
                                <input id="keywords" name="keywords" value="" class="text medium" placeholder="Enter Keywords" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Mark-up Percentage:</b> <br />
                                <p style="font-size:10px; line-height:8px">Enter 0 to return the items to suggested retail price.</p>
                            </td>
                            <td>
                                <input id="mark-up" name="mark-up" value="" class="text medium" placeholder="Enter Mark-up Percentage" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>MAP Pricing:</b> <br />
                                <p style="font-size:10px; line-height:8px">Check box to clear out MAP Pricing.</p>
<?php echo form_checkbox('MAP_NULL', 1, '', 'id="MAP_NULL"'); ?>
                            </td>
                            <td>
                                <input id="map_percent" name="map_percent" value="" class="text medium" placeholder="Enter MAP Pricing" />
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Brand Url:</b> <br />
                                <p style="font-size:10px; line-height:8px">brand url will not accept Space and special character. But you can use -,_</p>
                            </td>
                            <td>
                                <input id="slug" name="slug" value="" class="text medium" placeholder="Enter Brand URL."/>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Notice:</b>
                                <p style="font-size:10px; line-height:8px">This will appear at the bottom of the brand page.</p>
                            </td>
                            <td>
                                <?php
                                echo form_textarea(array('name' => 'notice',
                                    'value' => set_value('notice'),
                                    'id' => 'notice',
                                    'placeholder' => 'Brand Notice',
                                    'style' => 'height:100px; width:80%;'));
                                ?>
                            </td>
                        </tr>
                        <input id="promo-video" name="promo-video" value="" class="text medium" placeholder="Enter Promo Video URL" type='hidden' />
                    </table>
                </div>
            </div>
            </form>	
            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->
            <a href="javascript:void(0);" onclick="submitForm()" id="button" class="new"><i class="fa fa-plus"></i>&nbsp;Add a New Brand</a>
            <a href="javascript:void(0);" onclick="submitForm()" id="button" class="hide edit"><i class="fa fa-plus"></i>&nbsp;Edit Brand</a>
            <!-- SUBMIT DISABLED 
            <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p> -->

            <!-- CANCEL BUTTON 
            <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a> -->



        </div>
    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->


<script>

    function submitForm()
    {
        $("#brandEditForm").submit();
    }

    function populateEdit(id)
    {

        $.post(base_url + 'admin/load_brand_rec/' + id,
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
                            $($(this)).attr("href", href + '/' + responseData['brand_id']);
                            //alert($(this).text());
                        });
                        //var href = $("a.image_link").attr("href");
                        //$("a.image_link").attr("href", href + '/' + responseData['brand_id']);

                        $('.edit').show();
                        $('.new').hide();

                        $('#brand_id').val(responseData['brand_id']);
//                        if (responseData['active'] == 1)
//                            $('#active').prop('checked', true);
//                        if (responseData['active'] == 0)
//                            $('#active').prop('checked', false);
                        if (responseData['featured'] == 1)
                            $('#featured').prop('checked', true);
                        if (responseData['featured'] == 0)
                            $('#featured').prop('checked', false);
                        if (responseData['exclude_market_place'] == 1)
                            $('#exclude_market_place').prop('checked', true);
                        else if (responseData['closeout_market_place'] == 1)
                            $('#closeout_market_place').prop('checked', true);
                        else
                            $('#default_market_place').prop('checked', true);

                        $('#name').val(responseData['name']);
                        $('#name').attr('readonly', 'true');
                        $('#meta_tag').val(responseData['meta_tag']);
                        $('#keywords').val(responseData['keywords']);
                        $('#mark-up').val(responseData['mark_up']);
                        $('#map_percent').val(responseData['map_percent']);
                        $('#promo-video').val(responseData['promo_video']);
                        $('#slug').val(responseData['slug']);
                        $('#title').val(responseData['title']);
                        $('#notice').val(responseData['notice']);
                    }
                });

    }
</script>
