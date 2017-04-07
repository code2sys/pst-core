<?php
	$new_assets_url = jsite_url("/qatesting/benz_assets/");
	$media_url = jsite_url("/media/");
	
if(@$motorcycles) {
foreach($motorcycles as $motorcycle) { ?>
	<div class="mid-r">
		<?php $title = str_replace(' ', '_', trim($motorcycle['title']));?>
		<a href="<?php echo base_url(strtolower($motorcycle['type']).'/'.$title.'/'.$motorcycle['sku']);?>">
			<div class="mid-r-img">
				<div class="mid-r-logo">
					<!--<img src="<?php echo $new_assets_url; ?>images/imgpsh_fullsize (6).png" width="152px;"/>-->
				</div>
				<div class="mid-r-img-veh">
					<img src="<?php echo $media_url.$motorcycle['image_name']; ?>" width="px;"/>
				</div>
			</div>
		</a>
		<div class="mid-r-text">
			<div class="mid-text-left">
				<h3><?php echo $motorcycle['title'];?></h3>
				<?php if( $motorcycle['call_on_price'] == '1' ) { ?>
					<p class="cfp">Call For Price</p>
				<?php } else { ?>
					<p>Retail Price: &nbsp; $<?php echo $motorcycle['retail_price'];?></p>
					<p>Sale Price: &nbsp; &nbsp;&nbsp;$<?php echo $motorcycle['sale_price'];?></p>
				<?php } ?>
			</div>
			<div class="mid-text-right">
				<p>condition :<span><?php echo $motorcycle['condition'] == '1' ? 'New' : 'Pre-Owned';?></span></p>
				<p>color :<span><?php echo $motorcycle['color'];?></span></p>
				<?php if( $motorcycle['engine_hours'] > 0 ) { ?>
					<p>engine hours :<span><?php echo $motorcycle['engine_hours'];?></span></p>
				<?php } ?>
				<?php if( $motorcycle['sku'] != '' ) { ?>
					<p>stock :<span><?php echo $motorcycle['sku'];?></span></p>
				<?php } ?>
				<?php if( $motorcycle['mileage'] > 0 ) { ?>
					<p>mileage :<span><?php echo $motorcycle['mileage'];?></span></p>
				<?php } ?>
				<?php if( $motorcycle['engine_type'] != '' ) { ?>
					<p>fuel type :<span><?php echo $motorcycle['engine_type'];?></span></p>
				<?php } ?>
			</div>
		</div>
		<div class="mid-r-but">
			<a href="#"  data-toggle="modal" data-target="#myModal<?php echo $motorcycle['id'];?>"><img src="<?php echo $new_assets_url; ?>images/message.png" width="20px" height="22px;"/><span class="mid-cen">GET a quote</span></a>
			<a href="#" data-toggle="modal" data-target="#myModal<?php echo $motorcycle['id'];?>"><img src="<?php echo $new_assets_url; ?>images/outgoing.png" width="20px" height="24px;"/>value your <span>trade</span></a>
			<a href="#"><img src="<?php echo $new_assets_url; ?>images/doll.png" width="10px" height="20px;"/><span class="mid-cen">GET FINANCING</span></a>
			<a href="<?php echo base_url(strtolower($motorcycle['type']).'/'.$title.'/'.$motorcycle['sku']);?>"><img src="<?php echo $new_assets_url; ?>images/list.png" width="15px" height="20px;"/>VIEW DETAILS</a>
		</div>
	</div>
	
	<div class="modal fade pop" id="myModal<?php echo $motorcycle['id'];?>">
		<div class="modal-dialog area">	  
			<div class="modal-content">
				<div class="modal-header">
					<div class="clo" data-dismiss="modal">get a quote</div>			 
				</div>
				<?php echo form_open('welcome/productEnquiry', array('class' => 'form_standard')); ?>
					<div class="modal-body" id="scol">				
						 <div class="form-group">						
							<input type="text" class="form-control" placeholder="first name" name="firstName" required="">
							<div class="formRequired">*</div>
						</div>
						 <div class="form-group">						
							<input type="text" class="form-control" placeholder="last name" name="lastName" required="">
							<div class="formRequired">*</div>
						</div>
						 <div class="form-group">						
							<input type="email" class="form-control" placeholder="email" name="email" required="">
							<div class="formRequired">*</div>
						</div>
						 <div class="form-group">						
							<input type="text" class="form-control" placeholder="phone" name="phone">
						</div>
						 <div class="form-group">						
							<input type="text" class="form-control" placeholder="address" name="address">
						</div>
						 <div class="form-group">						
							<input type="text" class="form-control" placeholder="city" name="city">
						</div>
						 <div class="form-group">						
							<input type="text" class="form-control" placeholder="state" name="state">
						</div>
						<div class="form-group">						
							<input type="text" class="form-control" placeholder="zip code" name="zipcode">
						</div>				
						<h3 class="txt-title">Want to Schedule a Test Drive?</h3>
						
						<div class="form-group">						
							<input type="text" class="form-control" placeholder="date of ride" name="date_of_ride">
						</div>
						<hr class="brdr">
						<h3 class="txt-title">Trade in?</h3>
						
						<div class="form-group">						
							<input type="text" class="form-control" placeholder="make" name="make">
						</div>
						<div class="form-group">						
							<input type="text" class="form-control" placeholder="model" name="model">
						</div>
						<div class="form-group">						
							<input type="text" class="form-control" placeholder="year" name="year">
						</div>
						<div class="form-group">						
							<input type="text" class="form-control" placeholder="miles" name="miles">
						</div>
						<div class="form-group">						
							<textarea type="text" class="form-control" placeholder="added accessories" name="accessories"></textarea>
						</div>
						<div class="form-group">						
							<textarea type="text" class="form-control" placeholder="comments questions" name="questions"></textarea>
						</div>
						
						<h3 class="txt-title">I am Interested in this Vehicle</h3>
						
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Poloris" value="<?php echo $motorcycle['title'];?>" readonly name="motorcycle">
						</div>
							<input type="hidden" name="product_id" value="<?php echo $motorcycle['id'];?>">
						<div class="col-md-12 text-center" style="float:none;">
							<input type="submit" class="btn bttn">
						</div>
					</div>								
				</form>					
			</div>	  
		</div>
	</div>
<?php }
 } else { ?>
 	<div class="mid-r">
		No Product's Found in this search criteria!
	</div>
<?php }
?>
<div class="mypagination">
	<ul>
		<?php if( $pages > 1 ) { ?>
			<li class="<?php if ($page == 0): ?>active<?php endif; ?> pgn"><a href="javascript:void(0);">1</a></li>
		<?php } ?>
		<?php for($i=2;$i<=$pages;$i++) { ?>
			<li class="<?php if ($page == $i - 1): ?>active<?php endif; ?> pgn"><a href="javascript:void(0);"><?php echo $i;?></a></li>
		<?php } ?>
	</ul>
</div>