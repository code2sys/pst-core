<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-clipboard"></i>&nbsp;Dealer Inventory for <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>
        <?php endif; ?>

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


        <br>

        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "dealerinventory"
        ), true);
        ?>
        <!-- END TABS -->

        <?php echo form_open('adminproduct/part_update/' . $part_id, array('class' => 'form_standard')); ?>
        <!-- TAB CONTENT -->
        <div class="tab_content">
            <form method="post" action="/adminproduct/save_dealerinventory/<?php echo $post_id; ?>">

            <div class="tabular_data">

                <table width="100%" cellpadding="10" id="adminproduct_dealerinventory_v">
                    <thead>
                    <tr class="head_row">
                        <th><b>PST Part #</b></th>
                        <th><b>Distributor</b></th>
                        <th><b>Distributor Part #</b></th>
                        <th><b>Manufacturer Part #</b></th>
                        <th><b>Quantity Available</b></th>
                        <th><b>Product Cost</b></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($dealerinventory as $row): ?>
                    <tr>
                        <td><?php echo $row["partnumber"]; ?></td>
                        <td><?php echo $row["name"]; ?></td>
                        <td><?php echo $row["part_number"]; ?></td>
                        <td><?php echo $row["manufacturer_part_number"]; ?></td>
                        <td><input type="text" size="16" name="quantity_available_<?php echo $row["partvariation_id"]; ?>" value="<?php echo $row["quantity_available"]; ?>" /></td>
                        <td><input type="text" size="16" name="cost_<?php echo $row["partvariation_id"]; ?>" value="<?php echo $row["cost"]; ?>" /></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

                <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Update Dealer Inventory and Costs</button>
            </form>

        </div>
        <!-- END TAB CONTENT -->
        <br>
        <div style="clear: both"></div><br/>


    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>