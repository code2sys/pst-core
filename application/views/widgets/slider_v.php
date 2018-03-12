
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

            <script>
                $(document).ready(function(){
                    $('.bxslider').bxSlider({
                        auto: <?php if (count($sliderImages) > 0): ?>true<?php else: ?>false<?php endif; ?>,
                        pause: 5000,
                        randomStart: <?php if (count($sliderImages) > 0): ?>true<?php else: ?>false<?php endif; ?>
                    });
                });
            </script>
