	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- SIDEBAR -->
		<div class="sidebar">
			<div class="acct_nav_wrap">
			<div class="acct_nav_content">
				<div class="acct_menu-link">
					<a href="<?php echo base_url(); ?>" style="color:#333;">
						<i class="fa fa-navicon"></i> <b>Account Settings</b>
					</a>
				</div>
				<div id="acct_menu" class="acct_menu">
					<ul>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>"><i class="fa fa-user"></i> My Profile</a></li>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account_edit'; ?>"><i class="fa fa-pencil"></i> Edit Profile</a></li>
		      	<li><a href="<?php echo base_url('shopping/cart'); ?>"><i class="fa fa-shopping-cart"></i> Shopping Cart</a></li>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account_address'; ?>"><i class="fa fa-book"></i> Saved Addresses</a></li>
		      	<li><a href="<?php echo base_url('/shopping/wishlist'); ?>"><i class="fa fa-heart"></i> Wishlist</a></li>
		      	<li><a href="<?php echo $s_baseURL.'checkout/account_order'; ?>"><i class="fa fa-inbox"></i> Order History</a></li>
		      	<?php if($_SESSION['userRecord']['admin']): ?>
		      		<li><a href="<?php echo base_url('admin'); ?>"><i class="fa fa-gears"></i> Admin panel</a></li>
				<?php endif; ?>
		      	<li><a href="<?php echo $s_baseURL.'welcome/logout'; ?>"><i class="fa fa-sign-out"></i> Logout</a></li>
					</ul>
				</div>
			</div>
			</div>
			
		</div>
		<!-- END SIDEBAR -->	
		
		
		<!-- MAIN -->
		<div class="main_content">
		
		<?php if(@$_SESSION['newAccount']): ?>
		  <b>Thank you for creating an Account!</b><br />
		  <?php unset($_SESSION['newAccount']); endif;?>
		  
		  <?php if(@$_SESSION['orderNum']): ?>
		  <!-- SUCCESS MESSAGE -->
		<div class="success">
		  <img src="<?php echo $s_assets; ?>/images/success.png" style="float:left;margin-right:10px;">
	    <h1>Success</h1>
	    <p>
	      Your order number <?php echo $_SESSION['orderNum']; ?> has been placed. <br />
	      You should be receiving an email shortly confirming the order and with details attached.<br />
		  Click on the Orders Page to review this and previous orders.
	    </p>
	    <div class="clear"></div>
		</div>
		<!-- END SUCCESS MESSAGE -->
		<?php unset($_SESSION['orderNum']); 
		endif; ?>
			
			<!-- MY PROFILE -->
			<div class="account_section">
				<h1><i class="fa fa-user"></i> My Orders</h1>
		  <?php if(@$orders): foreach($orders as $order): ?>
		  <p class="post-info">
		  Order #<?php echo $order['order_id']; ?> - <?php echo $order['Reveived_date']; ?> | 
		  Shipping: <?php echo $order['shipping']; ?> | Sales Tax: <?php echo $order['tax']; ?> | <b>Total: $<?php echo number_format(($order['sales_price'] + $order['shipping'] + $order['tax']), 2) ; ?></b>
		  </p>
		  <div class="tabular_data">
		    <table cellpadding="3" style="width:100%;">
					<tr class="head_row">
						<td><b>Billing Info</b></td>
						<td><b>Shipping Info</b></td>
					</tr>
          <tr>
            <td>
              <?php echo $order['first_name']; ?> <?php echo $order['last_name']; ?>
              <?php echo $order['street_address']; ?> <?php echo $order['address_2']; ?><br />
              <?php echo $order['city']; ?> <?php echo $order['state']; ?> <?php echo $order['zip']; ?>
            </td>
            <td>
              <?php echo $order['shipping_first_name']; ?> <?php echo $order['shipping_last_name']; ?>
              <?php echo $order['shipping_street_address']; ?> <?php echo $order['shipping_address_2']; ?><br />
              <?php echo $order['shipping_city']; ?> <?php echo $order['shipping_state']; ?> <?php echo $order['shipping_zip']; ?>
            </td>
          </tr>
           <tr class="head_row">
				<td><b>Product</b></td>
				<td><b>Price</b></td>
			</tr>

          <?php if(@$order['products']): foreach(@$order['products'] as $product): ?>
  				<tr>
  					<td><?php if(strpos(@$product['sku'], 'coupon') === 0): 
  											echo 'Coupon'; 
  										else: 
  											echo $product['display_name']; 
  										endif;?></td>
  					<td><?php echo $product['price']; ?></td>
  				</tr>
				<?php endforeach; endif; ?>
		    </table>
		  </div>
		<?php endforeach; endif; ?>

				</div>
			<!-- END MY PROFILE -->
			
		</div>
		<!-- END MAIN -->		
		
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
