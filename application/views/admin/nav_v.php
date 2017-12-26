<?php
for ($i = 0; $i < 9; $i++)
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
					<!--<li><a href="<?php echo base_url('/admin_content/images'); ?>" ><i class="fa fa-image"></i>&nbsp;Images</a></li> -->
					<?php if(in_array('social_media', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin_content/social_media'); ?>"><i class="fa fa-rss"></i>&nbsp;Social Media</a></li>
					<?php } ?>
					<?php if(in_array('reviews', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin_content/reviews'); ?>"><i class="fa fa-comments"></i>&nbsp;Reviews</a></li>
					<?php } ?>
					<?php if(in_array('pages', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin_content/pages'); ?>" ><i class="fa fa-files-o"></i>&nbsp;Pages</a></li>
					<?php } ?>
					<?php if(in_array('email', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin_content/email'); ?>"><i class="fa fa-envelope-o"></i>&nbsp;Email</a></li>
					<?php } ?>
					<?php if(in_array('data_feeds', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin_content/feeds'); ?>"><i class="fa fa-rss"></i>&nbsp;Data Feeds</a></li>
					<?php } ?>
					<?php if(in_array('distributors', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admindistributors/index'); ?>" ><i class="fa fa-cubes"></i>&nbsp;Distributors</a></li>
					<?php } ?>
                </ul>
            </li>
            <li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[2]; ?>><i class="fa fa-shopping-cart"></i>&nbsp;Store</a>
                <ul>
					<?php if(in_array('categories', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/category'); ?>" ><i class="fa fa-th"></i>&nbsp;Categories</a></li>
					<?php } ?>
<!--					--><?php //if(in_array('navigation', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
<!--						<li><a href="--><?php //echo base_url('/adminnavigation/index'); ?><!--" ><i class="fa fa-navicon"></i>&nbsp;Navigation</a></li>-->
<!--					--><?php //} ?>
					<?php if(in_array('brands', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/brand'); ?>"><i class="fa fa-tag"></i>&nbsp;Brands</a></li>
					<?php } ?>
					<?php if(in_array('products', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/adminproduct/product'); ?>"><i class="fa fa-cube"></i>&nbsp;Product</a></li>
					<?php } ?>
					<?php if(in_array('orders', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/orders'); ?>" ><i class="fa fa-file-o"></i>&nbsp;Orders</a></li>
					<?php } ?>
					<?php if(in_array('taxes', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/taxes'); ?>"><i class="fa fa-dollar"></i>&nbsp;Taxes</a></li>
					<?php } ?>
					<?php if(in_array('shipping', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/shipping_rules'); ?>" ><i class="fa fa-truck"></i>&nbsp;Shipping</a></li>
					<?php } ?>
					<?php if(in_array('coupons', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/coupon'); ?>" ><i class="fa fa-barcode"></i>&nbsp;Coupons</a></li>
					<?php } ?>
					<?php if(in_array('product_receiving', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/product_receiving'); ?>" ><i class="fa fa-cubes"></i>&nbsp;Product Receiving</a></li>
					<?php } ?>
					<?php if(in_array('customers', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/customers'); ?>" ><i class="fa fa-users"></i>&nbsp;Customer</a></li>
					<?php } ?>
                    <?php if (!defined("MOTORCYCLE_SHOP_DISABLE") || !MOTORCYCLE_SHOP_DISABLE): ?>
					<?php if(in_array('mInventory', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/mInventory'); ?>" ><i class="fa fa-motorcycle"></i>&nbsp;Unit Inventory</a></li>
					<?php } ?>
                    <?php endif; ?>
                    <?php
                    if (defined('ENABLE_VAULT') && ENABLE_VAULT):
                    ?>
                    <?php if(in_array('vault', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
                        <li><a href="<?php echo base_url('/adminvault/vault_images'); ?>" ><i class="fa fa-motorcycle"></i>&nbsp;Vault Gallery</a></li>
                    <?php } ?>
                    <?php endif; ?>

                    <!--<li><a href="<?php echo base_url('/admin/wishlists'); ?>" ><i class="fa fa-magic"></i>&nbsp;Wishlist</a></li> -->
                    <!--<li><a href="<?php echo base_url('/admin/closeout_rules'); ?>" ><i class="fa fa-cubes"></i>&nbsp;Closeout Schedule</a></li>-->
                </ul>
            </li>
            <li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[5]; ?>><i class="fa fa-credit-card"></i>&nbsp;Finance</a>
                <ul>
					<?php if(in_array('finance', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/credit_applications'); ?>" ><i class="fa fa-user"></i>&nbsp;Credit Applications</a></li>
					<?php } ?>
					<?php if(in_array('mInventory', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/motorcycle_quotes'); ?>" ><i class="fa fa-motorcycle"></i>&nbsp;Quote Requests</a></li>
					<?php } ?>
                </ul>
            </li>
            <li><a href="<?php echo base_url('/admin'); ?>" <?php echo $arr[3]; ?>><i class="fa fa-users"></i>&nbsp;Users</a>
                <ul>
					<!--<?php if(in_array('list', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin'); ?>" ><i class="fa fa-list"></i>&nbsp;List</a></li>
					<?php } ?>-->
					<?php if(in_array('profile', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/profile'); ?>" ><i class="fa fa-user"></i>&nbsp;Store Profile</a></li>
					<?php } ?>
					<?php if(in_array('employees', $_SESSION['userRecord']['permissions']) || @$_SESSION['userRecord']['admin']) { ?>
						<li><a href="<?php echo base_url('/admin/employees'); ?>" ><i class="fa fa-users"></i>&nbsp;Employees</a></li>
					<?php } ?>
						<li><a href="<?php echo base_url('/welcome/logout'); ?>" ><i class="fa fa-sign-out"></i>&nbsp;Logout</a></li>
                </ul>
            </li>
            <li><a href="<?php echo base_url(''); ?>">&nbsp;Main Site</a>
            </li>
        </ul>
        <div class="clear"></div>
    </div>
</div>
<!-- END NAVAGATION ====================================================================================-->



