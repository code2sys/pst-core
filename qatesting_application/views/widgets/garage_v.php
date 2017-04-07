<!-- GARAGE -->
<div class="side_header">
	<h1><?php echo @$_SESSION['userRecord']['first_name']; if(@$_SESSION['userRecord']['first_name']):  ?>'s <?php endif; ?> Garage</h1>
</div>
<div class="side_section" >
	<?php if(@$_SESSION['garage'] ): foreach($_SESSION['garage'] as $label => $rideRecs): 
		 switch(@$rideRecs['make']['machinetype_id']):
			case '13':
				$img = 'icon_dirtbike.png';
				break;
			default:
				$img = 'icon_dirtbike.png';
				break;
		endswitch;
		
	?>
	<div class="side_item">
		<img src="<?php echo $assets; ?>/images/<?php echo $img; ?>"/>
		<p>
			<b><a href="javascript:void(0);" onclick="changeActive('<?php echo $label; ?>')"><?php echo $label; ?></a></b> | 
			<?php if($rideRecs['active']): $_SESSION['activeMachine'] = $rideRecs; ?><div class="garage_active"><p style="color:#606;font-size:18px; padding-left:3px;"><i class="fa fa-check"></i></p></div><?php endif; ?>
			<a href="javascript:void(0);" onclick="deleteFromGarage('<?php echo $label; ?>');" style="color:#F00;">
				<div class="garage_delete"><p style="font-size:18px;padding-left:3px;"><i class="fa fa-times"></i></p></div></a>
			
		<div class="clear"></div>
	</div>
	
	<?php endforeach; else: ?>
		<div class="side_item">
			<p><b>Use "Select Machine" above to add a ride to your garage.<br /><br />Parts for the active ride in your garage will be marked for easy reference throughout the site.</b>
			<div class="clear"></div>
		</div>
	<?php endif; ?>
	</div>
<!-- END GARAGE -->

<script>
	
	function deleteFromGarage(ride)
	{
		$.post(base_url + 'ajax/delete_from_garage/', {'garageLabel': ride},
			function(encodeResponse)
			{
				location.reload();
			});
	}
	
	function changeActive(ride)
	{
		$.post(base_url + 'ajax/change_active_garage/', {'garageLabel': ride},
			function(encodeResponse)
			{
				location.reload();
			});
	}

</script>
