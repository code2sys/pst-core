		<?php
		for($i = 0; $i < 9; $i++)
			$arr[$i] = '';
		if (!is_null($pageIndex) && $pageIndex < 9)
			$arr[$pageIndex] = 'class="active"';
	?>	


	<!-- NAVAGATION ========================================================================================-->
	<div class="main_nav_wrap">
		<div class="main_nav">
			<ul id="nav">
				<li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[0]; ?>><i class="fa fa-dashboard"></i>&nbsp;Dashboard</a></li>
				<li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[1]; ?>><i class="fa fa-pencil-square-o"></i>&nbsp;Content</a>
					<ul>
<!-- 						<li><a href="<?php echo base_url('/admin_content/images'); ?>" ><i class="fa fa-image"></i>&nbsp;Images</a></li> -->
						<li><a href="<?php echo base_url('/admin_content/social_media'); ?>"><i class="fa fa-rss"></i>&nbsp;Social Media</a></li>
						<li><a href="<?php echo base_url('/admin_content/reviews'); ?>"><i class="fa fa-comments"></i>&nbsp;Reviews</a></li>
						<li><a href="<?php echo base_url('/admin_content/pages'); ?>" ><i class="fa fa-files-o"></i>&nbsp;Pages</a></li>
						<li><a href="<?php echo base_url('/admin_content/email'); ?>"><i class="fa fa-envelope-o"></i>&nbsp;Email</a></li>
					</ul>
				</li>
				<li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[2]; ?>><i class="fa fa-shopping-cart"></i>&nbsp;Store</a>
					<ul>
						<li><a href="<?php echo base_url('/admin/category'); ?>" ><i class="fa fa-th"></i>&nbsp;Categories</a></li>
						<li><a href="<?php echo base_url('/admin/brand'); ?>"><i class="fa fa-tag"></i>&nbsp;Brands</a></li>
						<li><a href="<?php echo base_url('/admin/product'); ?>"><i class="fa fa-cube"></i>&nbsp;Product</a></li>
						<li><a href="<?php echo base_url('/admin/orders'); ?>" ><i class="fa fa-file-o"></i>&nbsp;Orders</a></li>
<!-- 						<li><a href="<?php echo base_url('/admin/wishlists'); ?>" ><i class="fa fa-magic"></i>&nbsp;Wishlist</a></li> -->
						<li><a href="<?php echo base_url('/admin/taxes'); ?>"><i class="fa fa-dollar"></i>&nbsp;Taxes</a></li>
						<li><a href="<?php echo base_url('/admin/shipping_rules'); ?>" ><i class="fa fa-truck"></i>&nbsp;Shipping</a></li>
						<li><a href="<?php echo base_url('/admin/coupon'); ?>" ><i class="fa fa-barcode"></i>&nbsp;Coupons</a></li>
					</ul>
				</li>
				<li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[3]; ?>><i class="fa fa-users"></i>&nbsp;Users</a>
					<ul>
						<li><a href="<?php echo base_url('/admin'); ?>" ><i class="fa fa-list"></i>&nbsp;List</a></li>
						<li><a href="<?php echo base_url('/admin/profile'); ?>" ><i class="fa fa-user"></i>&nbsp;Profile</a></li>
						<li><a href="<?php echo base_url('/admin/distributors'); ?>" ><i class="fa fa-cubes"></i>&nbsp;Distributors</a></li>
						<li><a href="<?php echo base_url('/admin'); ?>" ><i class="fa fa-sign-out"></i>&nbsp;Logout</a></li>
					</ul>
				</li>
				<li><a href="<?php echo base_url(''); ?>">&nbsp;Main Site</a>
				</li>
			</ul>
			<div class="clear"></div>
		</div>
	</div>
	<!-- END NAVAGATION ====================================================================================-->



