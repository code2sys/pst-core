<?php if(is_array(@$breadcrumbs['category'])): end($breadcrumbs['category']); $category_id = key($breadcrumbs['category']);  $count =  count($breadcrumbs['category']); $array = array_keys($breadcrumbs['category']); endif;?>
<?php  if(@$category): ?>


	<div class="side_header">
		<div class="grg">Refine Search by Category</div>
	</div>	
			
	<div class="sidebar_frame">
		<div class="sidebar_inner"	>
	 <?php foreach($category as $id => $ref):  ?>
	<!-- EXPANDER 1 -->
	<?php if(is_array($ref)): ?>
	
		<p class="expand_headingend">
			<?php if($id == @$category_id): ?><?php if(@$count > 1): ?><div style="float:right; margin-top:-10px; font-size:10px;">
				<a href="javascript:void(0);" onclick="setMainSearch(event, 'category', '<?php echo $array[$count-2]; ?>');" id="<?php echo $array[$count-2]; ?>" style="color:#F00"> <i class="fa fa-times"></i> &nbsp; </a><script>createURL('category', '<?php echo $array[$count-2]; ?>');</script>
				
			</div><?php endif; endif; ?>
			<?php if(isset($ref['subcats'])): ?>
				<a href="javascript:void(0);" ><?php echo $ref['label']; ?><?php if(@$ref['count']): ?> (<?php echo $ref['count']; ?>) <?php endif; ?></a>
			<?php elseif($ref['link']): ?>
				<a href="<?php echo base_url('shopping/productlist'.@$ref['link']); ?>" onclick="setMainSearch(event, 'category', '<?php echo $id; ?>');"><?php echo $brand.' '.$ref['label']; ?><?php if(@$ref['count']): ?> (<?php echo $ref['count']; ?>) <?php endif; ?></a>
			<?php endif; ?>
		</p>
		<?php if(isset($ref['subcats'])): ?>
		<div class="toggle_container" id="category" >
			<div class="expand_nav">
				<ul>
					<?php foreach($ref['subcats'] as $key => $subcat): ?>
					<li>
						<?php if($key == @$category_id): ?>
							<div style="float:right; margin:-15px -5px;">
								<a href="javascript:void(0);" onclick="setMainSearch(event, 'category', '<?php echo $array[$count-2]; ?>');" style="color:#F00;"> <i class="fa fa-times"></i> &nbsp; </a><script>createURL('category', '<?php echo $array[$count-2]; ?>');</script>
							</div>
						 <?php // Make Link Purple if selected ?>
							<a href="<?php if(isset($subcat['link'])): echo base_url('shopping/productlist'.@$subcat['link']); else: ?>javascript:void(0); <?php endif; ?>" 
								onclick="setMainSearch(event, 'category', '<?php echo $key; ?>');" 
								style="color:#636;">
									&nbsp; <?php if(is_array($subcat)): echo $subcat['label']; ?> (<?php echo $subcat['count']; ?>) <?php else: echo $subcat; endif; ?> &raquo;  
							</a>
							
						<?php else: // Regular color link with no x ?>
							<a href="<?php if(@$subcat['link']): echo base_url('shopping/productlist'.@$subcat['link']); else: ?>javascript:void(0); <?php endif; ?>" 
								onclick="setMainSearch(event, 'category', '<?php echo $key; ?>');" 
								id="<?php echo $key; ?>"> 
									&nbsp; <?php if(is_array($subcat)): echo $subcat['label']; ?> (<?php echo $subcat['count']; ?>) <?php else: echo $subcat; endif; ?> &raquo;  
							</a>
						<?php endif; ?>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</div>
	<?php endif; ?>
	
	
	<?php else: ?>
 	<h4 class="expand_heading"><a href="javascript:void(0);" onclick="setMainSearch(event, 'category', '<?php echo $id; ?>');" id="<?php echo $id; ?>"><?php echo $ref; ?></a><script>createURL('category', '<?php echo $id; ?>');</script></h4>
 	<?php endif; ?>

<?php  endforeach; endif;?>
</div>
</div>