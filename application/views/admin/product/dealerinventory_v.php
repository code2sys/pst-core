<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <h1><i class="fa fa-clipboard"></i>&nbsp;Dealer Inventory for <?php echo $product['name']; ?></h1>

        <?php if ($product["mx"] == 0): ?>
            <h2>Dealer-Inventory Product</h2>

            <p><em>Fitments can be edited under the &quot;SKUs, Quantities, &amp; Personalizations&quot; tab.</em></p>
        <?php endif; ?>

        <br>

        <!-- TABS -->
        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/product/edit_tab_subnav", array(
            "part_id" => $part_id,
            "tag" => "fitments"
        ), true);
        ?>
        <!-- END TABS -->

        <?php echo form_open('adminproduct/part_update/' . $part_id, array('class' => 'form_standard')); ?>
        <!-- TAB CONTENT -->
        <div class="tab_content">

            <div class="tabular_data">

                <table width="100%" cellpadding="10" id="adminproduct_fitments_v">
                    <tr class="head_row">
                        <thead>
                        <th><b>PST Part #</b></th>
                        <th><b>Distributor</b></th>
                        <th><b>Distributor Part #</b></th>
                        <th><b>Manufactuer Part #</b></th>
                        <th><b>Universal Fit?</b></th>
                        <th><b>Machine Type</b></th>
                        <th><b>Make</b></th>
                        <th><b>Model</b></th>
                        <th><b>Year</b></th>
                        <th><b>Optional Question</b></th>
                        <th><b>Answer</b></th>
                        </thead>
                    </tr>

                </table>

            </div>

        </div>
        <!-- END TAB CONTENT -->
        <br>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
<script type="application/javascript">
    $(window).load(function() {
        var fitments = <?php echo json_encode($fitments); ?>;

        for (var i= 0; i < fitments.length; i++) {
            fitments[i]["universalfit"] = fitments[i]["universalfit"] > 0 ? "Yes" : "No";
        }

        $("#adminproduct_fitments_v").dataTable({
            "processing" : true,
            "data" : fitments,
            "paging" : true,
            "info" : true,
            "stateSave" : true,
            "columns" : [
                {data: "partnumber"},
                {data: "name"},
                {data: "part_number"},
                {data: "manufacturer_part_number"},
                {data: "universalfit"},
                {data: "machinetype"},
                {data: "make"},
                {data: "model"},
                {data: "year"},
                {data: "question"},
                {data: "answer"}
            ]
        });

    });
</script>
