<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 1/29/18
 * Time: 4:30 PM
 */

$CI =& get_instance();
$CI->load->Model("Lightspeed_m");
$credentials = $CI->Lightspeed_m->getCredentials();
$lightspeed_configured = $credentials["user"] != "" && $credentials["pass"] != "";
$success = $CI->session->flashdata("success");

?>
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div class="clear"></div>


        <div class="admin_search_left">
            <div class="clear"></div>
            <h1><i class="fa fa-cubes"></i>&nbsp;Lightspeed Controls</h1>
        </div>

        <div class="clear"></div>

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


        <div class="clear"></div>

        <?php if ($lightspeed_configured): ?>
            <h2>Settings</h2>



            <?php echo form_open('admin/save_products_lightspeed_settings', array('class' => 'form_standard')); ?>
            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">

                        <tr>
                            <td width="30%"><b>Initial Status for Imported Units:</b></td>
                            <td>
                                <label><input type="radio" name="lightspeed_active_load" value="0" <?php if (!($c = $CI->Lightspeed_m->activeOnAdd())): ?>checked="checked"<?php endif; ?> /> Inactive</label>
                                <label><input type="radio" name="lightspeed_active_load" value="1" <?php if ($c): ?>checked="checked"<?php endif; ?> /> Active</label>
                            </td>
                        </tr>

                    </table>

                    <!-- SUBMIT PRODUCT -->
                    <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Settings</button>
                    <div style="clear: both"></div>

                </div>
            </div>
            </form>




            <h2>Supplier Codes</h2>

            <p>The Lightspeed part feed includes a supplier code for each part. The supplier code is usually a two- or three-letter abbreviation. This can be for a brand, e.g., FLY for Fly Racing, or for a distributor, e.g., TR for Tucker Rocky. Please define your supplier codes to improve matching your Lightspeed inventory to store inventory.</p>


            <?php echo form_open('admin/save_products_lightspeed_suppliercodes', array('class' => 'form_standard')); ?>
            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6" id="supplier_codes_table">
                        <thead>
                            <th width="30%"><strong>Supplier Code</strong></th>
                            <th width="70%"><strong>Assignment</strong></th>
                        </thead>
                        <tbody>

                        </tbody>

                    </table>

                    <!-- SUBMIT PRODUCT -->
                    <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Supplier Codes</button>
                    <div style="clear: both"></div>

                </div>
            </div>
            </form>




        <?php else: ?>
        <p>Please configure Lightspeed API access credentials in the <a href="/admin/profile">store profile</a>.</p>

        <?php endif; ?>


    </div>
</div>
<script type="application/javascript">

    var supplierCodeList = <?php echo json_encode($supplier_code_list); ?>;
    var brands = <?php echo json_encode($brands); ?>;
    var distributors = <?php echo json_encode($distributors); ?>;

    $(document).on("ready", function() {
        var brand_option_string = "<option value='0'>-- Select Brand --</option>";
        for (var i = 0; i < brands.length; i++) {
            brand_option_string+= "<option value='" + brands[i].brand_id + "'>" + brands[i].name + "</option>";
        }

        var distributor_option_string = "<option value='0'>-- Select Distributor --</option>";
        for (var i = 0; i < distributors.length; i++) {
            distributor_option_string += "<option value='" + distributors[i].distributor_id + "'>" + distributors[i].name + "</option>";
        }

        // We have to assemble that form..
        var $t = $("#supplier_codes_table tbody");
        for (var i = 0; i < supplierCodeList.length; i++) {
            $t.append("<tr data-lightspeedsuppliercodeid='" + supplierCodeList[i].lightspeed_suppliercode_id + "'><td  style='border: 1px solid #ccc'  valign='top'>" + supplierCodeList[i].supplier_code + "</td><td style='border: 1px solid #ccc' valign='top'><select class='type_selector' name='type_" + supplierCodeList[i].lightspeed_suppliercode_id + "' data-lightspeedsuppliercodeid='" + supplierCodeList[i].lightspeed_suppliercode_id + "'><option value='Unmatched'>Unmatched</option><option value='Distributor'>Distributor</option><option value='Brand'>Brand</option></select><div class='brand_selector for"+ supplierCodeList[i].lightspeed_suppliercode_id +"'>Brand: <select name='brand_id_" + supplierCodeList[i].lightspeed_suppliercode_id + "'>" + brand_option_string + "</select></div><div class='distributor_selector for"+ supplierCodeList[i].lightspeed_suppliercode_id +"'>Distributor: <select name='distributor_id_" + supplierCodeList[i].lightspeed_suppliercode_id + "'>" + distributor_option_string + "</select></div></td></tr>");
            if (supplierCodeList[i].type == "Distributor") {
                $(".brand_selector.for" + supplierCodeList[i].lightspeed_suppliercode_id).hide();
                $("select[name='type_" + supplierCodeList[i].lightspeed_suppliercode_id + "'] option[value='Distributor").prop("selected", true);
                $("select[name='distributor_id_" + supplierCodeList[i].lightspeed_suppliercode_id + "'] option[value='" + supplierCodeList[i].distributor_id + "']").prop("selected", true);
            } else if (supplierCodeList[i].type == "Brand") {
                $(".distributor_selector.for" + supplierCodeList[i].lightspeed_suppliercode_id).hide();
                $("select[name='type_" + supplierCodeList[i].lightspeed_suppliercode_id + "'] option[value='Brand']").prop("selected", true);
                $("select[name='brand_id_" + supplierCodeList[i].lightspeed_suppliercode_id + "'] option[value='" + supplierCodeList[i].brand_id + "']").prop("selected", true);
            } else {
                $(".distributor_selector.for" + supplierCodeList[i].lightspeed_suppliercode_id).hide();
                $(".brand_selector.for" + supplierCodeList[i].lightspeed_suppliercode_id).hide();
            }
        }

        $(document).on("change", "select.type_selector", function(e) {
            var lightspeed_suppliercode_id = e.target.dataset.lightspeedsuppliercodeid;
            var value = $(e.target).val();

            if (value == "Distributor") {
                $(".distributor_selector.for" + lightspeed_suppliercode_id).show();
                $(".brand_selector.for" + lightspeed_suppliercode_id).hide();
            } else if (value == "Brand") {
                $(".distributor_selector.for" + lightspeed_suppliercode_id).hide();
                $(".brand_selector.for" + lightspeed_suppliercode_id).show();
            } else {
                $(".distributor_selector.for" + lightspeed_suppliercode_id).hide();
                $(".brand_selector.for" + lightspeed_suppliercode_id).hide();
            }

        });

        // Bind something for the events...

    });

</script>

