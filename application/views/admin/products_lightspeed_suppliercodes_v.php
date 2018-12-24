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
            <h2>Lightspeed Order Feed</h2>

            <div class="tab_content">
                <p>Lightspeed Order Feed is a service of Lightspeed whereby Lightspeed polls your store periodically to check for orders. Lightspeed will also communicate back to your store with updates as orders are processed through Lightspeed. The API is hosted on your ecommerce store. The following credentials need to be provided to Lightspeed to integrate with this service:</p>

                <div class="hidden_table">
                    <table>
                        <tr>
                            <td><strong>Lightspeed API URL</strong></td>
                            <td><?php echo site_url('lightspeedparts'); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lightspeed Username</strong></td>
                            <td><?php echo htmlentities($lightspeed_feed_username); ?></td>
                        </tr>
                        <tr>
                            <td><strong>Lightspeed Password</strong></td>
                            <td><?php echo htmlentities($lightspeed_feed_password); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <h2>Settings</h2>



            <?php echo form_open('admin/save_products_lightspeed_settings', array('class' => 'form_standard')); ?>
            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">
                        <?php
                        global $PSTAPI;
                        initializePSTAPI();
                        ?>
                        <tr >
                            <td style="width:30%;"><b>New Unit Dealer IDs:</b><br/><em>By default, new units will be accepted from all dealer IDs in your Lightspeed feed. If you wish to restrict new unit inventory to specific dealerships, please enter the IDs here as a comma-separated list.</em></td>
                            <td><?php echo form_input(array('name' => 'lightspeed_new_unit_dealership_list',
                                    'value' => $PSTAPI->config()->getKeyValue('lightspeed_new_unit_dealership_list', ''),
                                    'class' => 'text large')); ?></td>
                        </tr>
                        <tr >
                            <td style="width:30%;"><b>Pre-Owned Unit Dealer IDs:</b><br/><em>By default, pre-owned units will be accepted from all dealer IDs in your Lightspeed feed. If you wish to restrict pre-owned unit inventory to specific dealerships, please enter the IDs here as a comma-separated list.</em></td>
                            <td><?php echo form_input(array('name' => 'lightspeed_used_unit_dealership_list',
                                    'value' => $PSTAPI->config()->getKeyValue('lightspeed_used_unit_dealership_list', ''),
                                    'class' => 'text large')); ?></td>
                        </tr>
                        <tr>
                            <td width="30%"><b>Initial Status for Imported Units:</b></td>
                            <td>
                                <label><input type="radio" name="lightspeed_active_load" value="0" <?php if (!($c = $CI->Lightspeed_m->activeOnAdd())): ?>checked="checked"<?php endif; ?> /> Inactive</label>
                                <label><input type="radio" name="lightspeed_active_load" value="1" <?php if ($c): ?>checked="checked"<?php endif; ?> /> Active</label>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"><b>Lightspeed Unit Destination Fee:</b></td>
                            <!-- <?php echo ($c = intVal($CI->Lightspeed_m->destinationOnAdd())); ?> -->
                            <td>
                                <label><input type="radio" name="lightspeed_default_destination_charge" value="0" <?php if ($c === 0): ?>checked="checked"<?php endif; ?> /> No, Do Not Include Destionation Charge By Default</label>
                                <label><input type="radio" name="lightspeed_default_destination_charge" value="1" <?php if ($c === 1): ?>checked="checked"<?php endif; ?> /> Yes, Include Destination Charge By Default</label>
                                <label><input type="radio" name="lightspeed_default_destination_charge" value="2" <?php if ($c === 2): ?>checked="checked"<?php endif; ?> /> Enable if DSRP > MSRP or if freight cost is specified.</label>
                            </td>
                        </tr>

                        <tr>
                            <td width="30%"><b>Include Units in Cycle Trader Feed by Default:</b></td>
                            <td>
                                <label><input type="radio" name="unitCycleTraderDefault" value="0" <?php if (!($c = $CI->Lightspeed_m->unitCycleTraderDefault())): ?>checked="checked"<?php endif; ?> /> No, imported units must be manually added to Cycle Trader Feed</label>
                                <label><input type="radio" name="unitCycleTraderDefault" value="1" <?php if ($c): ?>checked="checked"<?php endif; ?> /> Yes, include for Cycle Trader Feed by default</label>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"><b>Part Pricing Method:</b></td>
                            <td>
                                <label><input type="radio" name="lightSpeedPartPricingRule" value="0" <?php if (!($c = $CI->Lightspeed_m->lightSpeedPartPricingRule())): ?>checked="checked"<?php endif; ?> /> Use Pricing Algorithm</label>
                                <label><input type="radio" name="lightSpeedPartPricingRule" value="1" <?php if ($c): ?>checked="checked"<?php endif; ?> /> Use LightSpeed Part Price</label>
                            </td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <strong>CDK Lead Integration</strong><br/>
                                This enables forwarding leads from the major unit quote request form to the CDK/Lightspeed CRM.
                            </td>
                        </tr>
                        <tr>
                            <td style="width:30%;"><b>Enable:</b></td>
                            <td><label><input type="radio" name="forward_leads_to_cdk" value="Yes" <?php if ("Yes" == ($flc = $PSTAPI->config()->getKeyValue('forward_leads_to_cdk'))): ?>checked="checked"<?php endif; ?> > Yes, forward leads to CDK/Lightspeed</label><label><input type="radio" name="forward_leads_to_cdk" value="No" <?php if ("Yes" != $flc): ?>checked="checked"<?php endif; ?> > No, not not</label></td>
                        </tr>
                        <tr class="cdk_lead_integration">
                            <td style="width:30%;"><b>Dealership ID:</b></td>
                            <td><?php echo form_input(array('name' => 'vsept_dealership_id',
                                    'value' => $PSTAPI->config()->getKeyValue('vsept_dealership_id'),
                                    'class' => 'text large')); ?></td>
                        </tr>

                    </table>

                    <!-- SUBMIT PRODUCT -->
                    <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Settings</button>
                    <div style="clear: both"></div>

                </div>
            </div>
            </form>

            <script type="application/javascript">
                (function() {
                    var forward_leads_to_cdk_fn = function(e) {
                        if ($("input[name=forward_leads_to_cdk][value='Yes']:checked").length > 0) {
                            $(".cdk_lead_integration").show();
                        } else {
                            $(".cdk_lead_integration").hide();
                        }
                    };
                    $("input[name=forward_leads_to_cdk]").on("click", forward_leads_to_cdk_fn);
                    forward_leads_to_cdk_fn();
                })();
            </script>


            <h2>Supplier Codes</h2>

            <p>The Lightspeed part feed includes a supplier code for each part. The supplier code is usually a two- or three-letter abbreviation. This can be for a brand, e.g., FLY for Fly Racing, or for a distributor, e.g., TR for Tucker Rocky. Please define your supplier codes to improve matching your Lightspeed inventory to store inventory.</p>

            <div class="clear"></div>
            <!-- PRODUCT LIST -->
            <div class="tabular_data">
                <table width="100%" cellpadding="10" id="products_lightspeed_suppliercodes_ajax">
                    <thead>
                    <tr>
                        <th><b>Supplier Code</b></th>
                        <th><b>Assignment</b></th>
                        <th><b>Distributor</b></th>
                        <th><b>Brand</b></th>
                        <th><b>Actions</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>


            <div class="clear"></div>



        <?php else: ?>
        <p>Please configure Lightspeed API access credentials in the <a href="/admin/profile">store profile</a>.</p>

        <?php endif; ?>


    </div>
</div>

<script type="application/javascript">
    $(window).load(function() {
        $("#products_lightspeed_suppliercodes_ajax").dataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "<?php echo base_url("admin/products_lightspeed_suppliercodes_ajax"); ?>",
                "type" : "POST"
            },
            "data" : [],
            "paging" : true,
            "info" : true,
            "stateSave" : true,
            "columns" : [
                null,
                null,
                null,
                null,
                null
            ]
        });

    });
</script>
