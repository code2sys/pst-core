<?php require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;


 ?>
<!-- GARAGE -->
<div class="<?php if ($detect->isMobile() && !$detect->isTablet() ) { echo "none-dlck"; }else { echo "none-disp"; } ?>">
	<div class="side_header tablinks <?php if(@$_SESSION['garage'] ) { echo 'active'; }?>" onclick="openCity(event, 'Garage')">
		<div class="grg " ><?php echo @$_SESSION['userRecord']['first_name']; if(@$_SESSION['userRecord']['first_name']):  ?>'s <?php endif; ?> Garage</div>
	</div>
	<div class="side_header tablinks <?php if(!$_SESSION['garage'] ) { echo 'active'; }?>" onclick="openCity(event, 'shop')">
		<div class="grg">Shop Machine</div>
	</div>
	<div id="Garage" class="side_section tabcontent first" style="display:<?php if(@$_SESSION['garage'] ) { if ( $detect->isMobile() && !$detect->isTablet() ) { echo 'none'; }else { echo 'block'; }  } else { echo 'none'; }?>">
		<!--tlg-->
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
				<?php if($rideRecs['active']): $_SESSION['activeMachine'] = $rideRecs; ?><div class="garage_active"><p style="color:rgb(52,120,206);font-size:18px; padding-left:3px;"><i class="fa fa-check"></i></p></div><?php endif; ?>
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
	<div id="shop" class="side_section tabcontent send-none" style="display:<?php if(!$_SESSION['garage'] ) { if ( $detect->isMobile() && !$detect->isTablet() ) { echo 'none'; }else { echo 'block'; } } else { echo 'none'; }?>">		
			<form action="<?php echo base_url('ajax/update_garage'); ?>" method="post" id="update_garage_form" class="form_standard">
				<div id="toggle">
						<ul>
						 <li><div class="heading one">SHOP BY MACHINE</div></li>
							<div class="tlg">
								<select class="selectField" name="machine" id="machine" tabindex="1">
									<option value="">-- Select Machine --</option>
									<?php if(@$machines): foreach($machines as $id => $label): ?>
										<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
									<?php endforeach; endif; ?>-->
								<!-- <optgroup label="Motor Cycles"> -->
								<!----></select>
								<select name="make" id="make" tabindex="2" class="selectField">
									<option>-Make-</option>
								</select>
								<select name="model" id="model" tabindex="3" class="selectField">
									<option>-Model-</option>
								</select>
								<select name="year" id="year" tabindex="4" class="selectField">
									<option>-Year-</option>
								</select>
							<div class="btn-ful-wdt"><a href="javascript:void(0);" onClick="updateGarage();" id="add" class="addToCat button_no" style="padding:6px 13px; text-decoration:none; margin:0px;text-shadow:none; font:inherit; font-size:14px; float: left;border-radius: 0px;">Add To Garage</a></div>
							</div>
						</ul>
				</div>
			</form>
			<div class="clear"></div>		
	</div>
</div>
	
<?php /* ?><div class="none-dlck">
	<div class="side_header tablinks <?php if(@$_SESSION['garage'] ) { echo 'active'; }?>" onclick="openCity(event, 'Grae')">
		<div class="grg" ><?php echo @$_SESSION['userRecord']['first_name']; if(@$_SESSION['userRecord']['first_name']):  ?>'s <?php endif; ?> Garage</div>
	</div>
	<div class="side_header tablinks <?php if(!$_SESSION['garage'] ) { echo 'active'; }?>" onclick="openCity(event, 'shoping')">
		<div class="grg">Shop Machine</div>
	</div>
	<div id="Grae" class="side_section tabcontent first" style="display:<?php if(@$_SESSION['garage'] ) { echo 'none'; } else { echo 'none'; }?>">
		<!--tlg-->
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
				<?php if($rideRecs['active']): $_SESSION['activeMachine'] = $rideRecs; ?><div class="garage_active"><p style="color:rgb(52,120,206);font-size:18px; padding-left:3px;"><i class="fa fa-check"></i></p></div><?php endif; ?>
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
	<div id="shoping" class="side_section tabcontent send-none" style="display:<?php if(!$_SESSION['garage'] ) { echo 'none'; } else { echo 'none'; }?>">		
			<form action="<?php echo base_url('ajax/update_garage'); ?>" method="post" id="update_garage_form" class="form_standard">
				<div id="toggle">
						<ul>
						 <li><div class="heading one">SHOP BY MACHINE</div></li>
							<div class="tlg">
								<select class="selectField" name="machine" id="machine" tabindex="1">
									<option value="">-- Select Machine --</option>
									<?php if(@$machines): foreach($machines as $id => $label): ?>
										<option value="<?php echo $id; ?>"><?php echo $label; ?></option>
									<?php endforeach; endif; ?>-->
								<!-- <optgroup label="Motor Cycles"> -->
								<!----></select>
								<select name="make" id="make" tabindex="2" class="selectField">
									<option>-Make-</option>
								</select>
								<select name="model" id="model" tabindex="3" class="selectField">
									<option>-Model-</option>
								</select>
								<select name="year" id="year" tabindex="4" class="selectField">
									<option>-Year-</option>
								</select>
								<a href="javascript:void(0);" onClick="updateGarage();" id="add" class="addToCat button_no" style="padding:6px 13px; text-decoration:none; margin:0px;text-shadow:none; font:inherit; font-size:14px; float: left;border-radius: 0px;">Add To Garage</a>
							</div>
						</ul>
				</div>
			</form>
			<div class="clear"></div>		
	</div>
</div>	<?php */ ?>
	
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


<script>
	function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    evt.currentTarget.className += " active";
	if($('#'+cityName).hasClass('actv')) {
		$('#'+cityName).removeClass('actv');
		$('#'+cityName).hide();
	} else {
		$('#'+cityName).addClass('actv');
		$('#'+cityName).show();
	}
	//$("#"+cityName).slideToggle();
}
</script>