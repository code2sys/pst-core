<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-cubes"></i>&nbsp;Lightspeed Controls: Edit Code: <?php echo $row["supplier_code"]; ?></h1>


        <br>

        <!-- ERROR -->
        <?php if (@$error): ?>
            <div class="error">
                <h1><span style="color:#C90;"><i class="fa fa-warning"></i></span>&nbsp;Error</h1>
                <div style="clear: both"></div>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <!-- SUCCESS -->
        <?php if (@$success): ?>
            <div class="success">
                <h1><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h1>
                <div style="clear: both"></div>
                <p><?php echo $success; ?></p>
            </div>
        <?php endif; ?>
        <!-- END SUCCESS -->



        <?php echo form_open('admin/products_lightspeed_suppliercodes_save/' . $row["lightspeed_suppliercode_id"], array('class' => 'form_standard')); ?>
        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table">
                <table width="100%" cellpadding="6">

                    <tr>
                        <td><b>Assignment Type:</b></td>
                        <td>
                            <select name="type">
                                <option value="Unmatched" <?php if ($row["type"] == "Unmatched"): ?>selected="selected"<?php endif; ?> >Unmatched</option>
                                <option value="Brand" <?php if ($row["type"] == "Brand"): ?>selected="selected"<?php endif; ?> >Brand</option>
                                <option value="Distributor" <?php if ($row["type"] == "Distributor"): ?>selected="selected"<?php endif; ?> >Distributor</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="distributors">
                        <td><b>Distributor</b></td>
                        <td>
                            <select name="distributor_id">
                                <option value="0">-- Select Distributor --</option>
                                <?php foreach ($distributors as $d): ?>
                                <option value="<?php echo $d["distributor_id"]; ?>" <?php if ($d["distributor_id"] == $row["distributor_id"]): ?>selected="selected"<?php endif; ?> ><?php echo $d["name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>                    
                    <tr class="brands">
                        <td><b>Brand</b></td>
                        <td>
                            <select name="brand_id">
                                <option value="0">-- Select Brand --</option>
                                <?php foreach ($brands as $d): ?>
                                <option value="<?php echo $d["brand_id"]; ?>" <?php if ($d["brand_id"] == $row["brand_id"]): ?>selected="selected"<?php endif; ?> ><?php echo $d["name"]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <!-- SUBMIT PRODUCT -->
                <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Supplier Code</button>
                <div style="clear: both"></div>

            </div>
        </div>
        <!-- END TAB CONTENT -->
        <br>


        <!-- SUBMIT DISABLED
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>

        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->

<script type="text/javascript">
    (function() {

        var selectionChangeFn = function() {
            var val = $("select[name='type']").val();
            if (val == "Brand") {
                $("tr.distributors").hide();
                $("tr.brands").show();

            } else if (val == "Distributor") {
                $("tr.distributors").show();
                $("tr.brands").hide();

            } else {
                $("tr.distributors").hide();
                $("tr.brands").hide();
            }
        };

        $("select[name='type']").on("change", selectionChangeFn);
        selectionChangeFn();

    })();

</script>

