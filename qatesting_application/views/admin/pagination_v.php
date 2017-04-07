<?php if(@$pages > 1):?>
  <div style="float:right;margin-top:10px;">
  	  <?php $bottom = (($currentPage - $display_pages) < 1) ? 1 : ($currentPage - $display_pages); ?>
  	  <?php if($bottom > 1): ?> <a href="javascript:void(0);" onclick="paginateUsers(<?php echo $currentPage; ?>, 'down');">&laquo;</a> <?php endif; ?>
  	  <?php $top = (($currentPage + $display_pages) > $pages) ? $pages : ($currentPage + $display_pages); ?>
  		<?php for($i = $bottom; $i <= $top; $i++): ?>
  			<a href="javascript:void(0);" onclick="pageSwitchUser(<?php echo $i; ?>)">
  			  <?php if($i == $currentPage): ?><span style="color:red"><?php echo $i; ?></span>
  			    <?php else: echo $i; endif; ?>
  			 </a>
  		<?php endfor; ?>
  	  <?php if($top < $pages): ?> <a href="javascript:void(0);" onclick="paginateUsers(<?php echo $currentPage; ?>, 'up');">&laquo;</a> <?php endif; ?>
  </div>
<?php endif; ?>
