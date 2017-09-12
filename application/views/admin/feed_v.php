
<div class="content_wrap">
    <div class="content">
        <h1><i class="fa fa-rss"></i>&nbsp;Data Feeds</h1>
        <h3>View Edit and Manage Data Feeds</h3>
        <br>

        <div class="hidden_table">
                <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="4"><h2>Google Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><button type="button" name="getgoogle_feeds" value="" onClick="window.location.href = '<?php echo base_url('admin_content/google_feeds/'); ?>'; return false;">Generate Google Product Feed</button></td>
                        <td>Status : <?php echo isset($feed['status']) && $feed['status'] == 1 ? 'Completed' : 'Processing'; ?></td>
                        <td>Last Run : <?php   echo isset($feed['run_at']) ? date('m/d/y H:i:s', strtotime($feed['run_at'])) : ''; ?></td>
                        <td>Feed URL : <a href="<?php echo site_url() . 'welcome/googleSalesXML'; ?>"><?php echo site_url() . 'welcome/googleSalesXML'; ?></a></td>
                    </tr>
                </table>

                <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="4"><h2>Cycletrader Product Data Feed:</h2></th>
                    </tr>
                    <tr>
                        <td><button type="button" name="getcycletrader_feeds" value="" onClick="window.location.href = '<?php echo base_url('admin_content/cycletrader_feeds/'); ?>'; return false;">Generate Cycletrader Product Feed</button></td>
                        <td>Status : <?php echo isset($cycletrader_feeds['status']) && $cycletrader_feeds['status'] == 1 ? 'Completed' : 'Processing'; ?></td>
                        <td>Last Run : <?php echo isset($cycletrader_feeds['run_at']) ? date('m/d/y H:i:s', strtotime($cycletrader_feeds['run_at'])) : ''; ?></td>
                        <?php
                        $feed_url = site_url() . 'welcome/cycletraderSalesFile';
                        ?>
                        <td>Feed URL : <a href="<?php echo $feed_url; ?>"><?php echo $feed_url; ?></a></td>
                    </tr>
                </table>

              <table width="100%" cellpadding="6">
                    <tr>
                        <th colspan="5"><h2>Ebay Product Data Feed:</h2></th>
                    </tr>
                  <?php if ($ebay_error): ?>
                        <tr>
                            <td colspan="5">
                                <div style="border: 5px solid red; background: #fee; padding: 1em;">
                                    <div style="text-align: center"><strong>eBay Configuration Error</strong></div>
                                    <p>Your store has the following eBay configuration errors preventing sending parts to eBay:</p>
                                    <p><?php echo $ebay_error_string; ?></p>
                                </div>
                            </td>
                        </tr>
                  <?php else: ?>
                      <?php if ($ebay_warning): ?>
                      <tr>
                          <td colspan="5">
                              <div style="border: 5px solid yellow; background: #ffe; padding: 1em;">
                                  <div style="text-align: center"><strong>eBay Configuration Warning</strong></div>
                                  <p><?php echo $ebay_warning_string; ?></p>
                              </div>
                          </td>
                      </tr>

                      <?php endif; ?>

                      <tr>
                        <td><button type="button" name="getebay_feeds" value="" onClick="window.location.href = '<?php echo base_url('admin_content/send_new_ebay/'); ?>'; return false;">Generate & Send Ebay Product Feed</button></td>
                        <td>Status : <?php echo isset($ebay_feeds['status']) && $ebay_feeds['status'] == 1 ? 'Completed' : 'Processing'; ?></td>
                        <td>Last Run : <?php echo isset($ebay_feeds['run_at']) ? date('m/d/y H:i:s', strtotime($ebay_feeds['run_at'])) : ''; ?></td>
                        <?php $ebay_feed_file = STORE_DIRECTORY . "/ebay_feed.xml"; ?>
                        <?php if (file_exists($ebay_feed_file) && is_file($ebay_feed_file)): ?>
                        <td><a href="<?php echo jsite_url('/admin_content/download_ebay_xml/', true); ?>">Download eBay XML</a></td>
                        <?php endif; ?>
                        <td>End all products on ebay: <a href="<?php echo site_url() . 'welcome/hit_ebay_end'; ?>">end_all_products</a></td>
                    </tr>

                      <?php if ( isset($ebay_feeds['status']) && $ebay_feeds['status'] == 1): ?>
                          <tr>
                              <td colspan=""5">

                              <h3>eBay Last Run Results (<a href="<?php echo jsite_url("/admin_content/download_ebay_feed_csv"); ?>">download</a>)</h3>

                              <table>
                                  <thead>
                                  <tr>
                                      <th><strong>Count</strong></th>
                                      <th><strong>Results</strong></th>
                                  </tr>
                                  </thead>
                                  <tbody>
                                  <?php foreach ($ebay_feed_counts as $row): ?>
                                    <tr>
                                        <td><?php echo number_format($row["the_count"]); ?></td>
                                        <td><?php if ($row["error"] > 0): ?>Error: <?php echo $row["error_string"]; ?><?php else: ?>Successfully Submitted<?php endif; ?></td>
                                    </tr>
                                  <?php endforeach; ?>
                                  </tbody>

                              </table>

                              </td>
                          </tr>

                      <?php endif; ?>

                  <?php endif; ?>
               </table>
            <br>
            <form action="<?php echo base_url('admin_content/ebay_markup'); ?>" method="post" id="form_example" class="form_standard">


                <table class="paypal_email" cellpadding="1">
                    <tr>
                        <td><h3>eBay Markup %:</h3></td>
                        <td><input  name="ebay_markup" type="text" value="<?php echo $ebaymarkup[0]['value']; ?>"></td>
                        <td><button type="submit" >Set</button></td>
                    </tr>
                </table>
            </form><br>
            <form action="<?php echo base_url('admin_content/ebay_quantity'); ?>" method="post" id="form_example" class="form_standard">
                <table class="paypal_email" cellpadding="1">
                    <tr>
                        <td><h3>eBay Listing Quantity:</h3></td>
                        <td><input type="number" name="quantity" value="<?php echo $quantity[0]['value']; ?>"></td>
                        <td><button type="submit" >Set</button></td>
                    </tr>
                </table>
            </form><br>
            <form action="<?php echo base_url('admin_content/ebay_settings'); ?>" method="post" id="form_example" class="form_standard">
                <table class="ebay_setting" >

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
                                <td><input value ="<?php if($single_setting['max_value']!='0.0000') echo $single_setting['max_value']; ?>" name="data[<?php echo $i; ?>][max_value]" type="text"></td>
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
