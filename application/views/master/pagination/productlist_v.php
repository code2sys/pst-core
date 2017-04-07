<!-- ADD NEW / PAGINATION -->
<?php if(@$pages > 1):?>
<?php $bottom = (($currentPage - $display_pages) < 1) ? 1 : ($currentPage - $display_pages); ?>

<?php $top = (($currentPage + $display_pages) > $pages) ? $pages : ($currentPage + $display_pages); ?>


<h3 style="float:right;margin-top:5px;">
 <?php if($bottom > 1): ?> <a href="javascript:void(0);" onclick="paginateProduct(<?php echo $currentPage; ?>, 'down');"><i class="fa fa-chevron-circle-left"></i></a><?php endif; ?>
 <?php for($i = $bottom; $i <= $top; $i++): ?>
	  <a href="javascript:void(0);" onclick="pageSwitchProduct(<?php echo $i; ?>)">
		  <span class="pagenum" id="page_<?php echo $i; ?>" <?php if($i == $currentPage): ?> style="color:red"<?php endif; ?>><?php echo $i; ?></span>
	  </a> 
<?php endfor; ?>
<?php if($top < $pages): ?> <a href="javascript:void(0);" onclick="paginateProduct(<?php echo $currentPage; ?>, 'up');"><i class="fa fa-chevron-circle-right"></i></a> <?php endif; ?>
</h3>
<div class="clear"></div>
<?php endif; ?>
<!-- END ADD NEW / PAGINATION -->

<script>
	
	function paginateProduct(currentPage, direction) 
	{
		$.post(base_url + 'shopping/generateProductListPaginate/' + direction, {'ajax':true, 'page': currentPage}, 
		function(returnData)
		{
			$('.pagination').html(returnData);
		});
	}
	
	function pageSwitchProduct(page)
	{
		
		$('.pagenum').each(function( index ) 
		{
			$( this ).css('color', '#393') 
		});
		$('#page_' + page).css('color', 'red');
		$.post(base_url + 'shopping/generateAdPdtListTable/',
			{'ajax': true,
			 'page': page
			},
			function(returnData){
				$('#mainProductBand').html(returnData);
				$('html, body').animate({scrollTop : 0},800);			
				$('#productPage').html(page);
		});
		
	}

</script>