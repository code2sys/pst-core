<!-- PAGINATION -->
<?php if(@$pages > 1):?>
<div class="paginate">
  <p>
 <?php $bottom = (($currentPage - $display_pages) < 1) ? 1 : ($currentPage - $display_pages); ?>
 <?php if($bottom > 1): ?> <a href="javascript:void(0);" onclick="paginateProduct(<?php echo $currentPage; ?>, 'down');">&lt; Prev </a> | <?php endif; ?>
<?php $top = (($currentPage + $display_pages) > $pages) ? $pages : ($currentPage + $display_pages); ?>

<?php for($i = $bottom; $i <= $top; $i++): ?>
  <a href="javascript:void(0);" onclick="pageSwitchProduct(<?php echo $i; ?>)">
	  <?php if($i == $currentPage): ?><span style="color:red"><?php echo $i; ?></span>
	    <?php else: echo $i; endif; ?>
  </a> | 
<?php endfor; ?>

<?php if($top < $pages): ?> <a href="javascript:void(0);" onclick="paginateProduct(<?php echo $currentPage; ?>, 'up');"> Next &gt;</a> <?php endif; ?>
	</p>
</div> 
<?php endif; ?>
<!-- END PAGINATION -->
