<?php if(@$validMachines): ?>
<table class="hidden_table">
	<tr>
		<?php	$make = ''; $model = '';  
		foreach($validMachines as $mac):
			if($make != @$mac['record']['make']['make_id']): ?>
			<?php if($make != ''): ?></td><?php endif; ?>
		<td valign="top"width="25%"><h3><?php echo $mac['record']['make']['label']; ?></h3><br />
		<?php endif; if($model != @$mac['record']['model']['model_id']): ?>
		<b><br /><?php echo $mac['record']['model']['label']; ?></b><br />
		<?php endif; ?>
		<i class="fa fa-sun-o"></i><a href="javascript:void(0);" onclick="addToGarage('<?php echo @$mac['record']['make']['make_id']; ?>','<?php echo @$mac['record']['model']['model_id']; ?>','<?php echo @$mac['record']['year']; ?>');"><?php echo $mac['record']['name']; ?></a><br />
		<?php $make =  $mac['record']['make']['make_id']; $model = $mac['record']['model']['model_id'];
		endforeach; ?></div><div class="clear"></div>
<?php else: ?>
	No Valid Machines for this Part.
<?php endif; ?>

<script>
	function addToGarage(makeId, modelId, year)
	{
		var pathname = window.location.pathname;
		$.post(base_url + 'ajax/update_garage/',
					{ 
					 'make' : makeId,
					 'model' : modelId,
					 'year' : year,
					 'url' : pathname,
					 'ajax' : true
					},
					function(returnValue)
					{
						location.reload();
						
					});
	}
</script>