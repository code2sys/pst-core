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
                        <td><b>Parent Brand</b></td>
                        <td><b>Actions</b></td>
                    </tr>
                    <?php
                    if (@$brands): $i = 0;
                        foreach ($brands as $key => $brand):
                            ?>
                            <tr>
                                <td><?php echo $key; ?></td>
                                <td><?php echo $brand['name']; ?></td>
                                <td><b><?php echo @$parent_brands[$brand['parent_brand_id']]; ?></b></td>
                                <td>
                                    <a href="javascript:void(0);" onclick="populateEdit('<?php echo $brand['brand_id']; ?>');"><i class="fa fa-edit"></i>&nbsp;<b>Edit</b></a></a>
                                    <?php if (!@$brand['mx']): ?>
                                        | <a href="<?php echo base_url('admin/brand_delete/' . $brand['brand_id']); ?>"><i class="fa fa-times"></i>&nbsp;<b>Delete</b></a>
                                    <?php endif; ?>
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
                    <li><a href="<?php echo base_url('admin/brand'); ?>"><i class="fa fa-bars"></i>&nbsp;General Options*</a></li>
                    <li><a href="<?php echo base_url('admin/brand_image/' . $id); ?>"><i class="fa fa-image"></i>&nbsp;Image*</a></li>
                    <li><a href="<?php echo base_url('admin/brand_video/' . $id); ?>"><i class="fa fa-image"></i>&nbsp;Videos*</a></li>
                    <li><a href="<?php echo base_url('admin/brand_sizechart/' . $id); ?>" class="active"><i class="fa fa-image"></i>&nbsp;Size Charts*</a></li>
					<li><a href="<?php echo base_url('admin/brand_rule/'.$id); ?>"><i class="fa fa-image"></i>&nbsp;Closeout Schedule*</a></li>
                    <div class="clear"></div>
                </ul>
            </div>
            <!-- END TABS -->

            <!-- TAB CONTENT -->
            <div class="tab_content" style="width:110%;">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
                        <tr>
                            <td colspan="2">
                                <?php echo form_open_multipart('admin/brand_sizechart/' . $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?>
                                <table width="100%" cellpadding="6">
                                    <tr>
                                        <td><b>Activate Sizing Chart Page:</b></td>
                                        <?php $sts = $brands[0]['size_chart_status'] == 1 ? '1' : 0; ?>
                                        <td><?php echo form_checkbox('active', 1, $sts, 'id="active"'); ?></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Sizing Charts Url:</b>
                                            <p style="font-size:10px; line-height:8px">Brand url will not accept Space</p>
                                            <p style="font-size:10px; line-height:8px">and special character. But you can use -,_</p>
                                        </td>
                                        <td>
                                            <?php echo WEBSITE_HOSTNAME;?>/<input id="url" name="size_url" value="<?php echo $brands[0]['sizechart_url']; ?>" class="text large" placeholder="Enter Url" class="text medium" required />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="submit" name="savebrand" value="Save" class="button">
                                        </td>
                                    </tr>
                                </table>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <a href="javascript:void(0);" class="ad-new">Add New</a>
                                <?php echo form_open_multipart('admin/brand_sizechart/' . $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?>
                                <table width="100%" cellpadding="6" id="ad-new" style="border: 1px solid; display: none;">
                                    <tr>
                                        <td><b>Sizing Chart Title:</b></td>
                                        <td><input id="title" name="title" value="" class="text mediun" placeholder="Enter Title" class="text medium" /></td>
                                    </tr>
                                    <tr>
                                        <td><b>Sizing chart thumbnail:</b></td>
                                        <td><input id="image" type="file" name="image" class="text medium" class="text medium" /></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Sizing Chart URL:</b>
                                            <p style="font-size:10px; line-height:8px">Brand url will not accept Space</p>
                                            <p style="font-size:10px; line-height:8px">and special character. But you can use -,_</p>
                                        </td>
                                        <td>
                                            <?php echo WEBSITE_HOSTNAME;?>/<?php echo $brands[0]['sizechart_url']; ?>#<input id="url" name="url" value="" class="text large" placeholder="Enter Url" class="text medium" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <b>This sizing chart will appear for all products under these Brand categories:</b>
                                            <p style="font-size:10px; line-height:8px">*Hold Shift to select more than once category</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <select name="categories[]" multiple="" style="height:100px;width: 200px;">
                                                <?php foreach ($categories as $k => $v) { ?>
                                                    <option value="<?php echo $k; ?>"><?php echo $v['label']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <select name="partquestion_id" multiple="" style="height:100px;width:200px;">
                                                <option value="0">-- Select Age & Gender --</option>
                                                <?php foreach ($age_gender as $k => $v) { ?>
                                                    <option value="<?php echo $v['answer']; ?>"><?php echo $v['answer']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <b>Sizing Chart:</b>
                                            <p style="font-size:10px; line-height:8px">Create table including header <input type="number" name="rows" value="0" style="width:30px;"> Rows tall by <input type="number" name="cols" value="0" style="width:30px;"> Columns wide: <input type="button" name="ctable" class="button" value="Create Table"></p>
                                        </td>
                                    </tr>
                                    <tr class="s-chart" style="display: none;">
                                        <td colspan="2" class="schart"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <b>Text Box:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <textarea name="content" style="width: 100%; height: 100px;"></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <input type="submit" name="save" value="Update" class="button">
                                        </td>
                                    </tr>
                                </table>
                                </form>
                            </td>
                        </tr>
                        <?php foreach ($sizechart as $k => $v) { ?>
                        <?php $content = $v['content'];?>
                            <?php $sizeChart = json_decode($v['size_chart']); ?>
                            <tr class="size-chart" style="background-color: gainsboro;">
                                <td colspan="2"><?php echo $v['title'] ?><span class="expnd" style="width: 30px; float: right">+</span></td>
                            </tr>
                            <tr style="display: none;">
                                <td colspan="2">
                                    <?php echo form_open_multipart('admin/brand_sizechart/' . $id, array('class' => 'form_standard', 'id' => 'admin_brand_form')); ?>
                                    <table width="100%" cellpadding="6" style="border: 1px solid;" class="szrw">
                                        <tr>
                                        <input name="id" value="<?php echo $v['id']; ?>" type="hidden" />
                                        <td><b>Sizing Chart Title:</b></td>
                                        <td>
                                            <input id="title" name="title" value="<?php echo $v['title']; ?>" class="text medium" placeholder="Enter Title" class="text medium" />
                                        </td>
                                        <td style="text-align: center;">
                                            <img src="<?php echo base_url($media); ?>/<?php echo $v['image']; ?>" style="height:60px; width: 50px;"><br>
                                            <a href="<?php echo base_url($brands[0]['sizechart_url'].'#'.$v['url']);?>"><?php echo $v['title'];?></a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><b>Sizing chart thumbnail:</b></td>
                                        <td colspan="2">
                                            <input id="image" type="file" name="image" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <b>Sizing Chart URL:</b>
                                            <p style="font-size:10px; line-height:8px">Brand url will not accept Space</p>
                                            <p style="font-size:10px; line-height:8px">and special character. But you can use -,_</p>
                                        </td>
                                        <td colspan="2">
                                            <?php echo WEBSITE_HOSTNAME;?>/<?php echo $brands[0]['sizechart_url']; ?>#<input id="url" name="url" value="<?php echo $v['url']; ?>" class="text large" placeholder="Enter Url" class="text medium" />
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <b>This sizing chart will appear for all products under these Brand categories:</b>
                                            <p style="font-size:10px; line-height:8px">*Hold Shift to select more than once category</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <?php $category = explode(',', $v['categories']); ?>
                                            <select name="categories[]" multiple="" style="height:100px;width: 200px;">
                                                <?php foreach ($categories as $a => $b) { ?>
                                                    <option value="<?php echo $a; ?>" <?php echo in_array($a, $category) ? 'selected="selected"' : ''; ?>><?php echo $b['label']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <?php $gender = explode(',', $v['partquestion_id']); ?>
                                            <select name="partquestion_id[]" multiple="true" style="height:100px;width:200px;">
                                                <option value="0">-- Select Age & Gender --</option>
                                                <?php foreach ($age_gender as $k => $v) { ?>
                                                    <option value="<?php echo $v['answer']; ?>" <?php echo in_array($v['answer'], $gender) ? 'selected="selected"' : ''; ?>><?php echo $v['answer']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <b>Sizing Chart:</b>
                                            <p style="font-size:10px; line-height:8px">Create table including header <input type="number" name="rows" value="<?php echo count($sizeChart); ?>" style="width:30px;"> Rows tall by <input type="number" name="cols" value="<?php echo count($sizeChart[0]); ?>" style="width:30px;"> Columns wide: <input type="button" class="button" name="ctable" value="Create Table"></p>
                                        </td>
                                    </tr>
                                    <tr class="s-chart">
                                        <td class="schart" colspan="3" style="height:100px;width: 200px;">
                                            <table width='100%'>
                                                <?php foreach ($sizeChart as $a => $b) { ?>
                                                    <tr>
                                                        <?php foreach ($b as $a1 => $b1) { ?>
                                                            <td><input type="text" name="size[<?php echo $a ?>][<?php echo $a1 ?>]" value="<?php echo $b1; ?>"></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <b>Text Box:</b>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <textarea name="content" style="width: 98%; height: 100px;"><?php echo $content; ?></textarea>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="submit" name="update" value="Update" class="button">
                                        </td>
                                        <td>
                                            <input type="submit" name="delete" value="Delete" class="button">
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </td>
                        </tr>
                    <?php } ?>
                    </table>
                </div>
            </div>

            <!-- END TAB CONTENT -->
            <br>
            <!-- SUBMIT PRODUCT -->			
            <a href="javascript:void(0);" onclick="submitForm()" id="button" class="hide edit"><i class="fa fa-plus"></i>&nbsp;Save Image</a>			


        </div>
    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->


<script>

    function submitForm()
    {
        $(".form_standard").submit();
    }

    $(document).on('click', '.add-row', function (e) {
        var str = "<tr><td><input type='text' name='video_url[]' value='' class='text large' placeholder='Enter video URL' style='height:30px;'></td><td><input id='title' name='title[]' value='' class='text large' placeholder='Enter video Title' class='text medium' style='height:30px;'/></td><td><input type='number' value='' name='ordering[]' class='text small' placeholder='Ordering' min='1' style='height:30px;'</td></tr>";
        $('.tbdy').append(str);
    });

    $(document).on('click', '.ad-new', function () {
        $('#ad-new').slideToggle();
    });

    $(document).on('click', '.size-chart', function () {
        $(this).next().slideToggle('slow');
        if ($(this).next().css('visibility') === 'visible') {
            $('.expnd', $(this)).html('+');
        } else {
            $('.expnd', $(this)).html('-');
        }
    });

    $(document).on('click', 'input[name=ctable]', function () {
        var cols = $('input[name=cols]', $(this).parent()).val();
        var rows = $('input[name=rows]', $(this).parent()).val();

        if (cols > 0 && rows > 0) {
            var str = "<table width='100%'>";
            for (i = 0; i < rows; i++) {
                str += "<tr>";
                for (a = 0; a < cols; a++) {
                    str += "<td><input type='text' name='size[" + i + "][" + a + "]' style='width:90%;'></td>";
                }
                str += "</tr>";
            }
            str += "</table>";
            $('.schart', $(this).parent().parent().parent().parent()).html(str);
            $('.s-chart', $(this).parent().parent().parent().parent()).show();
        } else {
            alert('Please enter Cols and Rows');
        }
    });

</script>