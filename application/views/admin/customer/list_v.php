<style>
.main_nav ul li ul li a{width:157px !important;}
.main_nav ul li a{width:130px !important; height:43px !important;}
.main_nav ul li a.active{width:130px !important; height:43px !important;}
.main_nav ul li a:hover{width:130px !important; height:43px !important;}
.main_nav li ul a:hover, .main_nav li ul li ul li ul li:hover a{height:35px !important; width:157px !important;}
.tabular_data td{padding:8px 3px !important; font-family:'Open Sans',sans-serif;}
.tabular_data table, th, td{border-top:1px solid #BBB !important; border:none;}
table.dataTable thead th, table.dataTable thead td{border-bottom:0px solid #111 !important;}
</style>

<!-- BOOTSTRAP CSS AND JS CODE STARTS HERE -->
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/css/bootstrap.css');?>" />
<link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/css/glyphicons.css');?>" />
<script src="<?php echo base_url('assets/css/bootstrap/js/bootstrap.js');?>"></script>
<!-- BOOTSTRAP CSS AND JS CODE ENDS HERE -->

<div class="content_wrap">
	<div class="content">
		<h1><i class="fa fa-users"></i>&nbsp;Customers</h1>
		<div class="" style="float:left; padding:2% 3% 3% 0%;">
			<a href="<?php echo base_url('admin/customer_edit'); ?>"><button style="color:black; padding:10px 15px;">Add New Customer</button></a>
			<a href="<?php echo base_url('admin/export_customer'); ?>"><button style="color:black; padding:10px 15px;">Export Customers</button></a>
			<?php if (ENABLE_CUSTOMER_PRICING): ?>
			<a href="<?php echo base_url('admin/customer_pricing_defaults'); ?>"><button style="color:black; padding:10px 15px;">Set Default Pricing Rules</button></a>
			<?php endif; ?>
		</div>
		<div class="admin_search_full" style="height:auto;">
			<form action="<?php echo base_url('admin/customers'); ?>/" method="get" id="moto_search" class="form_standard">
				<div class="hidden_table">
					<b>Lookup Customer </b>
					<input name="search" placeholder="Search <?php echo WEBSITE_NAME; ?>" class="text large" style="height:36px;" value="<?php echo $_GET['search'];?>"/>
					<input type="submit" value="Go!" class="button" style="margin-top:6px;">
				</div>
			</form>
		</div>
		
		<div id="listTable">
			<div class="tabular_data">
				<table width="100%" cellpadding="10" class="tblsrtr">
					<thead>
						<tr class="head_row">
							<td><b>Customer</b></td>
							<td><b>Phone</b></td>
							<td><b>Email</b></td>
							<td><b># Orders</b></td>
							<td><b>Employee</b></td>
							<td><b>Reminders</b></td>
							<td><b>Actions</b></td>
						</tr>
					</thead>
				</table>
			</div>
		</div>
	</div>
</div>
<div class="cstm-pup"></div>
<script>
$(document).ready(function() {
        var dataTable = $('.tblsrtr').DataTable( {
            "processing": true,
            "serverSide": true,
		    "searching": false,
			"order": [[ 5, "asc" ]],
			"columnDefs": [ {
						targets: [ 0 ],
						orderable: true
					}, {
						targets: [ 1 ],
						orderable: false
					}, {
						targets: [ 2 ],
						orderable: false
					}, {
						targets: [ 3 ],
						orderable: true
					}, {
						targets: [ 4 ],
						orderable: false
					}, {
						targets: [ 5 ],
						orderable: true
					}, {
						targets: [ 6 ],
						orderable: false
					} ],
		"ajax":{
                url :"<?php echo base_url('admin/load_customer_rec/?srch='.$_GET['search']); ?>", // json datasource
                type: "post",  // method  , by default get
                error: function(){  // error handling
                    $(".employee-grid-error").html("");
                    $(".tblsrtr").append('<tbody class="employee-grid-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $(".employee-grid_processing").css("display","none");
 
                }
            }
        } );
    });
</script>
<script>
// $(document).ready(function(){
	// $(".tblsrtr").DataTable({
		// "processing": true,
		// "serverSide": true,
		// "ajax": "<?php echo base_url('admin/load_customer_rec'); ?>",
		// "searching": false
	// });
// });
$(document).on('click', '.day-rem-evnt', function() {
	$('#loading-background').show();
	var dt = $(this).data('dt');
	var id = $(this).data('id');
	var user_id = $(this).data('user');
	var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>/"+id;
	$.post( ajax_url, {'dt':dt, 'user_id': user_id}, function( result ){
		$('.cstm-pup').html(result);
		$('#loading-background').hide();
	});
});
$(document).on('click', '.clspopup', function() {
	$('.cstm-pup').html('');
});
$(document).on('click', '.svcls', function() {
	$('#reminder_form').submit();
});
</script>