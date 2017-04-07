
			<?php if(@$sliderImages): ?>
			<!-- PAGE HEADER/SLIDER -->
			<div class="page_header">
				<ul class="bxslider">
					<?php foreach($sliderImages as $img): ?>
				  <li><img src="<?php echo base_url($media); ?>/<?php echo $img['image']; ?>" /></li>
				  <?php endforeach; ?>
				 </ul>
			</div>
			<!-- END PAGE HEADER/SLIDER -->
			<?php endif; ?>