
			<?php if(@$sliderImages): ?>
			<!-- PAGE HEADER/SLIDER -->
			<div class="page_header">
				<ul class="bxslider">
					<?php foreach($sliderImages as $img): ?>
                                    <li><a href="<?php echo $img['banner_link'];?>"><img src="<?php echo base_url($media); ?>/<?php echo $img['image']; ?>" /></a></li>
				  <?php endforeach; ?>
				 </ul>
			</div>
			<!-- END PAGE HEADER/SLIDER -->
			<?php endif; ?>