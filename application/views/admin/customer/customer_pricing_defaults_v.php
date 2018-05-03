
<div class="content_wrap">
	<div class="content">
		<h1><i class="fa fa-dollar"></i>&nbsp;Customer Pricing - Default Rules</h1>


<?php

$CI =& get_instance();
echo $CI->load->view("admin/customer/backbone_customerpricing_widget", array(
    "user_id" => null
), true);

?>

    </div>
</div>