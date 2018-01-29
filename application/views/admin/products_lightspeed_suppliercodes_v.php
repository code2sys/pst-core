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


        <div class="clear"></div>

        <?php if ($lightspeed_configured): ?>
            <h2>Settings</h2>



            <?php echo form_open('admin/save_products_lightspeed_settings', array('class' => 'form_standard')); ?>
            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">

                        <tr>
                            <td><b>Initial Status for Imported Unites:</b></td>
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
            <!-- END TAB CONTENT -->
            <br>


            <!-- SUBMIT DISABLED
            <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>

            <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


            </form>




            <h2>Supplier Codes</h2>

            <p>The Lightspeed part feed includes a supplier code for each part. The supplier code is usually a two- or three-letter abbreviation. This can be for a brand, e.g., FLY for Fly Racing, or for a distributor, e.g., TR for Tucker Rocky. Please define your supplier codes to improve matching your Lightspeed inventory to store inventory.</p>




        <?php else: ?>
        <p>Please configure Lightspeed API access credentials in the <a href="/admin/profile">store profile</a>.</p>

        <?php endif; ?>


    </div>
</div>

