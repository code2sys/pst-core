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
            <h1><i class="fa fa-cubes"></i>&nbsp;Dealer Track Controls</h1>
        </div>

        <div class="clear"></div>

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

        <?php echo form_open('admin/save_dealer_track_controls', array('class' => 'form_standard')); ?>
            <!-- TAB CONTENT -->
            <div class="tab_content">
                <div class="hidden_table">
                    <table width="100%" cellpadding="6">

                        <tr>
                            <td width="30%"><b>Initial Status for Imported Units:</b></td>
                            <td>
                                <label><input type="radio" name="dealer_track_active_immediately" value="0" <?php if ($dealer_track_active_immediately == 0): ?>checked="checked"<?php endif; ?> /> Inactive</label>
                                <label><input type="radio" name="dealer_track_active_immediately" value="1" <?php if ($dealer_track_active_immediately > 0): ?>checked="checked"<?php endif; ?> /> Active</label>
                            </td>
                        </tr>

                        <tr>
                            <td width="30%"><b>Delete If Not In Feed File:</b><br/>If a file is received, should it be treated as a complete export of Dealer Track major unit inventory, or as an incremental update?</td>
                            <td>
                                <label><input type="radio" name="dealer_track_delete_if_not_in_upload" value="0" <?php if ($dealer_track_delete_if_not_in_upload == 0): ?>checked="checked"<?php endif; ?> /> No, treat the feed file as an incremental update; admin will manually delete major units.</label>
                                <label><input type="radio" name="dealer_track_delete_if_not_in_upload" value="1" <?php if ($dealer_track_delete_if_not_in_upload > 0): ?>checked="checked"<?php endif; ?> /> Yes, delete major units from Dealer Track that are no longer included in the feed file.</label>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"><b>Default Vehicle Type:</b><br/>Dealer Track does not include a &quot;type&quot; field; please specify a default.</td>
                            <td>
                                <select name="dealer_track_default_vehicle_type">
                                    <?php
                                    // sort them...
                                    usort($motorcycle_types, function($a, $b) {
                                        return strnatcasecmp($a["name"], $b["name"]);
                                    });

                                    foreach ($motorcycle_types as $m_rec):
                                    ?>
                                    <option value="<?php echo $m_rec["id"]; ?>" <?php if ($dealer_track_default_vehicle_type == $m_rec["id"]): ?>selected="selected"<?php endif; ?>><?php echo $m_rec["name"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td width="30%"><b>Default Vehicle Category:</b>Dealer Track does not include a &quot;category&quot; field; please specify a default.</td>
                            <td>
                                <select name="dealer_track_default_category">
                                    <?php
                                    // sort them...
                                    usort($motorcycle_categories, function($a, $b) {
                                        return strnatcasecmp($a["name"], $b["name"]);
                                    });

                                    foreach ($motorcycle_categories as $m_rec):
                                        ?>
                                        <option value="<?php echo $m_rec["id"]; ?>" <?php if ($dealer_track_default_vehicle_type == $m_rec["id"]): ?>selected="selected"<?php endif; ?>><?php echo $m_rec["name"]; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                        </tr>
                    </table>

                    <!-- SUBMIT PRODUCT -->
                    <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Settings</button>
                    <div style="clear: both"></div>

                </div>
            </div>
            </form>


    </div>
</div>
