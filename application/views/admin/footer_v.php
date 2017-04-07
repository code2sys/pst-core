<!-- FOOTER ==============================================================================================-->
<div class="footer_wrap">
	<div class="footer_content">
		
	</div>
</div>
<!-- END FOOTER ==========================================================================================-->
<script>
$(document).on('click', '.cmpltd', function() {
	var id = $(this).data('id');
	if( confirm("Are you sure this event is completed!")) {
		var ajax_url = "<?php echo site_url('admin/completeEvent/');?>/"+id;
		$.post( ajax_url, {}, function( result ){
			location.reload();
		});
	}
});
//rmv-cmpltd
$(document).on('click', '.rmv-cmpltd', function() {
	var id = $(this).data('id');
	var rmvd = $(this).data('rmvd');
	if( confirm("Are you sure this event is completed!")) {
		var ajax_url = "<?php echo site_url('admin/completeRecurEvent/');?>/"+id+'/'+rmvd;
		$.post( ajax_url, {}, function( result ){
			location.reload();
		});
	}
});
$(document).on('change', 'input[name=recur]', function() {
	var tVal = $(this).val();
	if( tVal == 'daily' ) {
		$('#monday').attr('disabled', false);
		$('#tuesday').attr('disabled', false);
		$('#wednesday').attr('disabled', false);
		$('#thursday').attr('disabled', false);
		$('#friday').attr('disabled', false);
		$('#saturday').attr('disabled', false);
		$('#sunday').attr('disabled', false);
		$('input[name=rcr_evry]').val('');
		$('input[name=rcr_evry]').attr('disabled', true);
	} else if( tVal == 'monthly' ) {
		$('#monday').attr('disabled', true);
		$('#tuesday').attr('disabled', true);
		$('#wednesday').attr('disabled', true);
		$('#thursday').attr('disabled', true);
		$('#friday').attr('disabled', true);
		$('#saturday').attr('disabled', true);
		$('#sunday').attr('disabled', true);
		$('input[name=rcr_evry]').val('');
		$('input[name=rcr_evry]').attr('disabled', true);
	} else if( tVal == 'yearly' ) {
		$('#monday').attr('disabled', true);
		$('#tuesday').attr('disabled', true);
		$('#wednesday').attr('disabled', true);
		$('#thursday').attr('disabled', true);
		$('#friday').attr('disabled', true);
		$('#saturday').attr('disabled', true);
		$('#sunday').attr('disabled', true);
		$('input[name=rcr_evry]').val('');
		$('input[name=rcr_evry]').attr('disabled', true);
	} else {
		$('#monday').attr('disabled', false);
		$('#tuesday').attr('disabled', false);
		$('#wednesday').attr('disabled', false);
		$('#thursday').attr('disabled', false);
		$('#friday').attr('disabled', false);
		$('#saturday').attr('disabled', false);
		$('#sunday').attr('disabled', false);
		$('input[name=rcr_evry]').val('');
		$('input[name=rcr_evry]').attr('disabled', false);
	}
});
</script>