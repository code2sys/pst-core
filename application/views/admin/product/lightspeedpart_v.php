<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div class="clear"></div>


        <div class="admin_search_left">
            <div class="clear"></div>
            <h1><i class="fa fa-cubes"></i>&nbsp;Lightspeed Parts</h1>
            <p><b>The following parts have been pulled from your Lightspeed Parts feed.</b></p>
        </div>


        <div class="clear"></div>
        <!-- PRODUCT LIST -->
        <div class="tabular_data">
            <table width="100%" cellpadding="10" id="admin_lightpseedpart_list_table_v">
                <thead>
                <tr>
                    <th><b>Part Number</b></th>
                    <th><b>Supplier Code</b></th>
                    <th><b>Description</b></th>
                    <th><b># Available</b></th>
                    <th><b>Price</b></th>
                    <th><b>Cost</b></th>
                    <th><b>Last Seen</b></th>
                    <th><b>Store Product Match</b></th>
                    <th><b>Distributor Part Match</b></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>

        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->

<script type="application/javascript">
    $(window).load(function() {
        $(".tabular_data table").dataTable({
            "processing" : true,
            "serverSide" : true,
            "ajax" : {
                "url" : "<?php echo base_url("admin/products_lightspeedpart_ajax"); ?>",
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
                {"type" : "num"},
                {"type" : "num"},
                {"type" : "num"},
                {"type" : "date"},
                null,
                null
            ]
        });
    });
</script>