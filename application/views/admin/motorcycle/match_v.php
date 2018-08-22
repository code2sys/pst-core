<?php
$CI =& get_instance();
$CI->load->model("CRS_m");

?>
<!-- Gritter -->
<link rel="stylesheet"
      href="/assets/Gritter/css/jquery.gritter.css" />
<!--<link rel="stylesheet" href="/assets/newjs/jquery-ui.structure.min.css" />-->
<link rel="stylesheet" href="/assets/newjs/jquery-ui.min.css" />

<script type="text/javascript"
        src="/assets/Gritter/js/jquery.gritter.min.js"></script>

<script type="application/javascript" src="/assets/underscore/underscore-min.js" ></script>
<script type="application/javascript" src="/assets/backbone/backbone-min.js" ></script>
<script type="application/javascript" src="/assets/dropzone/dropzone.js" ></script>
<script type="application/javascript" src="/assets/newjs/jquery-ui.min.js" ></script>
<script type="text/template" id="ExistingMatchView">

</script>
<script type="text/template" id="AvailableTrimView">

</script>
<div class="content_wrap">
    <div class="content">

<?php
$CI =& get_instance();
echo $CI->load->view("admin/motorcycle/moto_head", array(
    "new" => @$new,
    "product" => @$product,
    "success" => @$success,
    "assets" => $assets,
    "id" => @$id,
    "active" => "match",
    "descriptor" => "Match",
    "source" => @$product["source"],
    "stock_status" => @$product["stock_status"]
), true);

?>
<div class="tab_content">
    <?php if ($product["crs_trim_id"] > 0): ?>
    <div class="existing_trim_holder">
        <?php print_r($CI->CRS_m->getTrim($product["crs_trim_id"])); ?>
    </div>
    <?php endif; ?>


</div>


<script type="application/javascript">
$(document).on("ready", function() {

    <?php if ($product["crs_trim_id"] > 0): ?>

    <?php endif; ?>


});


</script>

    </div>
</div>
