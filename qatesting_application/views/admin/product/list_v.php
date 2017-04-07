	<!-- MAIN CONTENT =======================================================================================-->
	<div class="content_wrap">
		<div class="content">
			
			<h1><i class="fa fa-cubes"></i>&nbsp;Products</h1>
			<p><b>To add a new product click the button below.</b></p>
			<br>
			<a href="" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>
			
			<div class="pagination"><?php echo @$pagination; ?></div>
			<div class="clear"></div>
			<!-- PRODUCT LIST -->
			<div class="tabular_data">
				<?php echo $productListTable; ?>
			</div>
			<!-- END PRODUCT LIST -->
			<a href="" id="button"><i class="fa fa-plus"></i>&nbsp;Add a new Product</a>
			
			<div class="pagination"><?php echo @$pagination; ?></div>
			<div class="clear"></div>
						
		</div>
	</div>
	<!-- END MAIN CONTENT ==================================================================================-->
<div id="productPage" class="hide">1</div>