	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- SIDEBAR -->
		<?php if(!isset($_GET['u'])){?>
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
		<?php }?>
		<!-- END SIDEBAR -->	
		
		
		<!-- MAIN -->
		<div class="main_content fl-wdh"<?php if(isset($_GET['u'])){?> style="width:100%;"<?php }?>>
		
		<?php if(@$_SESSION['newAccount']): ?>
		  <b>Thank you for creating an Account!</b><br />
		  <?php unset($_SESSION['newAccount']); endif;?>
		  
		  <?php if(@$_SESSION['orderNum']): ?>
		  <!-- SUCCESS MESSAGE -->
		<div class="success">
		  <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
	    <h1>Success</h1>
	    <div class="clear"></div>
	    <?php if(!isset($_GET['u'])){?>
		<p>
	      Your order number <?php echo @$_SESSION['orderNum']; ?> has been placed. <br />
	      You should be receiving an email shortly confirming the order with details attached.<br />
		  Click on Order History to review this and previous orders.
	    </p>
		<?php }else{?>
		<p style="font-size: 17px;">
	      Your order number <?php echo @$_SESSION['orderNum']; ?> has been placed. <br />
	      You should be receiving an email shortly confirming the order with details attached.<br /><br />
		  <strong>Please note down this information, as you won't be able to browse the same page again because you have checked out as a Guest!</strong><br /><br />
		  <a href="<?php echo $s_baseURL.'welcome/new_account'; ?>" style="font-weight: bold;">Create an account</a> to get full access!
	    </p>
		<?php }?>
	    <div class="clear"></div>
		</div>
		<!-- END SUCCESS MESSAGE -->
		<?php unset($_SESSION['orderNum']); 
		endif; ?>
			
			<!-- MY PROFILE -->
			<?php if(!isset($_GET['u'])){?>
			<div class="account_section">
				<h1><i class="fa fa-user"></i> My Profile</h1>
				<div class="tabular_data">
					<table width="100%" cellpadding="8">
						<tr class="row_dark">
							<td style="width:120px; padding:8px;"><b>Email:</b></td>
							<td style="padding:8px;"><?php echo $_SESSION['userRecord']['username']; ?></td>
						</tr>
						<tr>
							<td style="padding:8px;"><b>First Name:</b></td>
							<td style="padding:8px;"><?php echo @$_SESSION['userRecord']['first_name']; ?></td>
						</tr>
						<tr class="row_dark">
							<td style="padding:8px;"><b>Last Name:</b></td>
							<td style="padding:8px;"><?php echo @$_SESSION['userRecord']['last_name']; ?></td>
						</tr>
						<tr>
							<td style="padding:8px;"><b>Group:</b></td>
							<td style="padding:8px;"><?php echo $_SESSION['userRecord']['admin'] ? 'Admin' : 'Valued Customer'; ?></td>
						</tr>
						<tr  class="row_dark">
							<td style="padding:8px;"><b>Last Login:</b></td>
							<td style="padding:8px;"><?php echo @$_SESSION['userRecord']['last_login'] ? date('m/d/Y', $_SESSION['userRecord']['last_login']) : 'N/A'; ?> </td>
						</tr>
						
					</table>
				</div>
			</div>
			<?php }?>
			<!-- END MY PROFILE -->
			
		</div>
		<!-- END MAIN -->		
		
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
