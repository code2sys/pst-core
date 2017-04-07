<div class="clear"></div>
<div class="wrap">
	<div class="content_wrap">
		<div class="main_content" style="width:100%;">
			<div class="brnds">
				<div class="bnr">
					<h1 class="hdng"><?php echo $title1; ?></h1>
					<p class="pr">
						<?php echo $textboxes[0]['text'];?>
					</p>
				</div>
				<div class="ftrd-brnds">
					<p>Featured <span>Brands</span></p>
					<?php foreach( $featured as $key => $val ) { ?>
						<a href="<?php echo base_url($val['slug']);?>" onclick="setNamedSearchBrandt(event, 'brand', '<?php echo $key; ?>', '<?php echo addslashes($val['name']); ?>');">
						  <div class="bx">
							<img src="/media/<?php echo $val['image'];?>">
							<b><?php echo $val['name'];?></b>
						  </div>
						</a>
					<?php } ?>
				</div>
				<div class="brnd-list">
					<div class="lft">
						<ul>
						  <?php
						  $cnt=1;
						  foreach( $brands as $key => $val ) {
							  if(count($val) > 0) { ?>
								  <li class="<?php echo $cnt==1?'active':'';?> actv" data-key="<?php echo $key;?>">
									<a href="#<?php echo $key;?>"> <?php echo $key;?> </a>
								  </li>
							  <?php
							  $cnt++;
							  } ?>
						  <?php } ?>
						</ul>
					</div>
					<div class="ryt">
						<?php foreach( $brands as $k => $v ) { ?>
							<ul class="act" id="<?php echo $k;?>" tabindex='1'>
								<p>Brands by <?php echo $k;?></p>
								<?php foreach( $v as $key => $val ) { ?>
									<li>
										<a href="<?php echo base_url($val['slug']);?>" class="<?php echo $val['featured']==1?'ftrd-brnd':'';?>" onclick="setNamedSearchBrandt(event, 'brand', '<?php echo $key; ?>', '<?php echo addslashes($val['name']); ?>');"><?php echo $val['name'];?> </a>
									</li>
								<?php } ?>
							</ul>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>
<script>
//$('.actv').click(function() {
//	var id = $(this).data('key');
//	$(window).scrollTop($('#'+id).offset().top);
//	//$('#'+id).focus();
//});
</script>