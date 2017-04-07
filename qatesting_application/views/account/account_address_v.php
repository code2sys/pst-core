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
		
		<?php if(@validation_errors()): ?>		
			<!-- VALIDATION ALERT -->
			<div class="validation_error">
			<img src="<?php echo $s_assets; ?>/images/error.png" style="float:left;margin-right:10px;">
		    <h1>Error</h1>
		    <p><?php echo validation_errors(); ?></p>
		    <div class="clear"></div>
			</div>
			<!-- END VALIDATION ALERT -->
		<?php endif; ?>
		<?php echo form_open($s_baseURL.'checkout/account_edit', array('class' => 'form_standard')); ?>		
			<!-- EDIT PROFILE -->
			<div class="account_section">
			<div style="float:right;"><a href="<?php echo $s_baseURL.'checkout/account_address_edit/new'; ?>" class="button">Create New Address</a></div>
				<h1><i class="fa fa-book"></i> Address Book</h1>
				
				<div class="clear"></div>
				<div class="hidden_table">
					<form class="form_standard">
					<table width="100%" cellpadding="6">
						<tr>
							<?php if(@$addresses): $i = 0; foreach($addresses as $add): if($i > 2): $i = 0; echo "</tr><tr>"; endif; $i++; ?>
							<td>
								<?php if($add['id'] == $_SESSION['userRecord']['billing_id']): ?><h3 style="margin:0;">Billing Address</h3>
								<?php elseif($add['id'] == $_SESSION['userRecord']['shipping_id']): ?><h3 style="margin:0;">Shipping Address</h3>
								<?php else: ?><h3 style="margin:0;">Address</h3><?php endif; ?>
								<?php if($add['company']): echo $add['company'] ; ?><br /><?php endif; ?>
								<?php echo $add['first_name'] . ' ' . $add['last_name']; ?><br />
								<?php echo $add['email']; ?><br />
								<?php echo $add['phone']; ?><br />
								<?php echo $add['street_address']; ?><br />
								<?php if($add['address_2']): echo $add['address_2'] ; ?><br /><?php endif; ?>
								<?php echo $add['city'] . ', ' . $add['state'] . '  ' . $add['zip']; ?><br />
								<?php echo $add['country']; ?><br />
								<a href="<?php echo base_url('checkout/account_address_edit/'.$add['id']); ?>">Edit</a> &nbsp;
								<?php if(($add['id'] != $_SESSION['userRecord']['billing_id']) && ($add['id'] != $_SESSION['userRecord']['shipping_id'])): ?><a href="<?php echo base_url('checkout/make_billing/'.$add['id']); ?>">Make Billing</a> &nbsp; <a href="<?php echo base_url('checkout/make_shipping/'.$add['id']); ?>">Make Shipping</a> <?php endif; ?>
							</td>
							<?php endforeach; endif; ?>
						</tr>
</table>
					
					</form>
				</div>
			</div>
			<!-- END EDIT PROFILE -->
			
		</div>
		<!-- END MAIN -->		
		
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->