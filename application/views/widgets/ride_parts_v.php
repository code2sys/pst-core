	<div class="side_header tp-cat-head">
		<div class="grg">Refine Search by Category</div>
	</div>
	<div class="tlg">
	 <?php if(@$categories): foreach($categories as $id => $cat):   if(is_array($cat)):  ?>
	<!-- EXPANDER 1 -->
<h4 class="expand_heading"><a href="javascript:void(0);"><?php echo $cat['label']; ?></a></h4>
	<div class="toggle_container">
		<div class="expand_nav">
			<ul>
				<?php foreach($cat['subcats'] as $key => $subcat): ?>
				<li><a href="<?php echo base_url('shopping/productlist'.$subcat['link']); ?>" onclick="setMainSearch( event, 'category', '<?php echo $key; ?>');" id="<?php echo $key; ?>"> &nbsp; <?php echo $subcat['name']; ?> &raquo; </a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</div>
<?php else:  ?>

 	<h4 class="expand_heading"><a href=""><?php echo $cat; ?></a></h4>

<?php  endif; endforeach; endif;?>
</div>
