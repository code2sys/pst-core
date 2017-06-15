
<div class="content_wrap">
    <div class="content">
        <h1><i class="fa fa-rss"></i>&nbsp;Data Feeds</h1>
        <h3>View Edit and Manage Data Feeds</h3>
        <br>

        <div class="hidden_table">
            <form action="<?php echo base_url('admin_content/feeds'); ?>" method="post" id="form_example" class="form_standard">
                <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="4"><h2>Google Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><input type="submit" name="getFeed" value="Generate Google Product Feed"></td>
                        <td>Last Run : <?php   echo isset($feed['run_at']) ? date('m/d/y H:i:s', strtotime($feed['run_at'])) : ''; ?></td>
                        <td>Feed URL : <a href="<?php echo site_url() . 'welcome/googleSalesXML'; ?>"><?php echo site_url() . 'welcome/googleSalesXML'; ?></a></td>
                    </tr>
                </table>
            </form>
<!--            <form action="<?php //echo "#"; //echo base_url('admin_content/craglist_feeds');               ?>" method="post" id="form_example" class="form_standard">
                <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="4"><h2>Craigslist Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><input type="submit" name="getcraglist_feeds" value="Generate Craigslist Product Feed"></td>
                        <td>Status : <?php //echo isset($craglist_feeds['status']) && $craglist_feeds['status'] == 1 ? 'Completed' : 'Processing';              ?></td>
                        <td>Last Run : <?php //echo isset($craglist_feeds['run_at']) ? date('m/d/y H:i:s', strtotime($craglist_feeds['run_at'])) : '';              ?></td>
                        <td>Feed URL : <a href="<?php //echo "#"; //echo site_url().'welcome/craglistSalesXML';              ?>"><?php //echo site_url() . 'welcome/craglistSalesXML';              ?></a></td>
                    </tr>
                </table>
            </form>-->
            <form action="<?php echo base_url('admin_content/cycletrader_feeds'); ?>" method="post" id="form_example" class="form_standard">
                <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="4"><h2>Cycletrader Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><input type="submit" name="getcycletrader_feeds" value="Generate Cycletrader Product Feed"></td>
                        <td>Status : <?php echo isset($cycletrader_feeds['status']) && $cycletrader_feeds['status'] == 1 ? 'Completed' : 'Processing'; ?></td>
                        <td>Last Run : <?php echo isset($cycletrader_feeds['run_at']) ? date('m/d/y H:i:s', strtotime($cycletrader_feeds['run_at'])) : ''; ?></td>
                        <td>Feed URL : <a href="<?php echo site_url() . 'welcome/cycletraderSalesFile'; ?>"><?php echo site_url() . 'welcome/cycletraderSalesXML'; ?></a></td>
                    </tr>
                </table>
            </form>
            <form action="<?php echo base_url('admin_content/ebay_feeds'); ?>" method="post" id="form_example" class="form_standard">
<!--              <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="3"><h2>Ebay Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><input type="submit" name="getebay_feeds" value="Generate Ebay Product Feed"></td>
                        <td>Status : <?php echo isset($ebay_feeds['status']) && $ebay_feeds['status'] == 1 ? 'Completed' : 'Processing'; ?></td>
                        <td>Last Run : <?php echo isset($ebay_feeds['run_at']) ? date('m/d/y H:i:s', strtotime($ebay_feeds['run_at'])) : ''; ?></td>
                    </tr>
                    <tr>
                        <td>Feed URL : <a href="<?php echo site_url() . 'welcome/ebaySalesFile'; ?>"><?php echo site_url() . 'welcome/ebaySalesFile'; ?></a></td>
                        <td>Hit Ebay : <a href="<?php echo site_url() . 'welcome/hit_ebay'; ?>"><?php echo site_url() . 'welcome/hit_ebay'; ?></a></td>
                        <td>Check Ebay Feed Status : <a href="<?php echo site_url() . 'welcome/ebay_status'; ?>"><?php echo site_url() . 'welcome/ebay_status'; ?></a></td>
                    </tr>
                    <tr>
                        <td>Generate End Product XML: <a href="<?php echo site_url() . 'welcome/create_ebay_end_xml'; ?>">create_ebay_end_xml</a></td>
                        <td>End all products on ebay: <a href="<?php echo site_url() . 'welcome/hit_ebay_end'; ?>">end_all_products</a></td>
                    </tr>
                      <tr>
                        <td>Generate Update Product XML: <a href="<?php echo site_url() . 'welcome/create_old_product_xml'; ?>">create_old_product_xml</a></td>
                        <td>Update Product on Ebay: <a href="<?php echo site_url() . 'welcome/hit_ebay_update'; ?>">send_update_ebay</a></td>
                    </tr>
                    <tr>
                        <td>Generate New Product XML: <a href="<?php echo site_url() . 'admin_content/create_new_product_xml'; ?>">create_new_product_xml</a></td>
                        <td>Add Product on Ebay: <a href="<?php echo base_url('welcome/hit_ebay/'); ?>">send_new_ebay</a></td>
                    </tr>
                     <tr>
                            <td>Check Previous Running Jobs: <a href="<?php echo site_url() . 'welcome/list_all_feeds'; ?>">list_all_feeds</a></td>
                        <td>Abort Job: <a href="<?php echo base_url('admin_content/send_new_ebay/'); ?>">send_new_ebay</a></td>
                        <td>Check Status: <a href="<?php echo base_url('admin_content/send_new_ebay/'); ?>">check status</a></td>
                    </tr>
                </table>
-->
              <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="3"><h2>Ebay Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><input type="submit" name="getebay_feeds" value="Generate Ebay Product Feed"></td>
<!--                        <td>Status : <?php echo isset($ebay_feeds['status']) && $ebay_feeds['status'] == 1 ? 'Completed' : 'Processing'; ?></td>
                        <td>Last Run : <?php echo isset($ebay_feeds['run_at']) ? date('m/d/y H:i:s', strtotime($ebay_feeds['run_at'])) : ''; ?></td> -->
                        <td>Send to Ebay: <a href="<?php echo base_url('admin_content/send_new_ebay/'); ?>">send_new_ebay</a></td>
                        <td>End all products on ebay: <a href="<?php echo site_url() . 'welcome/hit_ebay_end'; ?>">end_all_products</a></td>
                    </tr>
               </table>
            </form><br>
            <form action="<?php echo base_url('admin_content/paypal_email'); ?>" method="post" id="form_example" class="form_standard">
                <table class="paypal_email" width="100" cellpadding="1">
                    <tr>
                        <td><h3>PayPal Email Address:</h3></td>
                        <td><input  name="paypal_email" type="text" value="<?php echo $paypalemail[0]['value']; ?>"></td>
                        <td><button type="submit" name="paypal">Update</button></td>
                    </tr>
                </table>
            </form><br>
            <form action="<?php echo base_url('admin_content/ebay_quantity'); ?>" method="post" id="form_example" class="form_standard">
                <table class="paypal_email" width="100" cellpadding="1">
                    <tr>
                        <td><h3>Ebay Listing Quantity:</h3></td>
                        <td><input type="number" name="quantity" value="<?php echo $paypalemail[0]['quantity']; ?>"></td>
                        <td><input type="submit" value="add"></td>
                    </tr>
                </table>
            </form><br>
            <form action="<?php echo base_url('admin_content/ebay_settings'); ?>" method="post" id="form_example" class="form_standard">
                <table class="ebay_setting" width="100">


                    <h2 style="color: #444;">ebay shipping rules:</h2>


                    <thead>
                    <th><h3>ProductPrice(Low)</h3></th>
                    <th><h3>ProductPrice(High)</h3></th>
                    <th><h3>Shipping Cost</h3></th>
                    </thead>
                    <tbody>
                        <?php
                        $i = 0;
                        foreach ($ebayshippingsettings as $single_setting) {
                            ?>
                            <tr>
                                <td><input value ="<?php echo $single_setting['min_value']; ?>" name="data[<?php echo $i; ?>][min_value]" type="text"></td>
                                <td><input value ="<?php echo $single_setting['max_value']; ?>" name="data[<?php echo $i; ?>][max_value]" type="text"></td>
                                <td><input value ="<?php echo $single_setting['shipping_cost']; ?>" name="data[<?php echo $i; ?>][shipping_cost]" type="text"></td>
                                <td>
                                    <input value ="<?php echo $single_setting['id']; ?>" name="setting_id" type="hidden">
                                </td>
                                <td><button type="delete" class="delete">Remove</button></td>
                            </tr>
                            <?php $i++; ?>
                        <?php } ?>
                        <tr>
                            <td><input name="data[<?php echo $i; ?>][min_value]" type="text"></td>
                            <td><input name="data[<?php echo $i; ?>][max_value]" type="text"></td>
                            <td><input name="data[<?php echo $i; ?>][shipping_cost]" type="text"></td>

                        </tr>


                    <tbody>

                </table>

                <button class="addmore" name="addmore">Add More Rows</button>
                <button type="submit" name="save_ebay">Save</button>
            </form>

        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        var i = '<?php echo $i; ?>';
        $(".addmore").click(function (e) {
            i++;
            e.preventDefault();
            $('.ebay_setting tr:last').after('<tr> <td> <input name = "data[' + i + '][min_value]" type = "text"> </td><td><input name = "data[' + i + '][max_value]" type = "text"></td><td><input name = "data[' + i + '][shipping_cost]" type = "text"></td>< /tr>');
        });
        $(".delete").click(function (e) {
            e.preventDefault();
            $(this).parent().parent().remove();
        });
    });
//     $(document).on('click','.remove', function(){
//     $(this).parent().parent().remove(); 
//    });
</script>
