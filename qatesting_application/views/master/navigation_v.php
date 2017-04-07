	<?php
		for($i = 0; $i < 9; $i++)
			$arr[$i] = '';
		if (!is_null($pageIndex) && $pageIndex < 9)
			$arr[$pageIndex] = 'class="active"';
	?>	
			
			<!-- NAVAGATION & USER INFO -->
			<div class="header_right">
				<div class="nav_wrap">
					<div class="nav_content">
					<div class="menu-link">
							<a href="<?php echo $s_base_url; ?>" style="color:#FFF;">
								<i class="fa fa-navicon"></i> <b>Menu</b>
							</a>
						</div>
						<div id="menu" class="menu">
							<ul>
				      	<li><a href="<?php echo base_url(); ?>"><i class="fa fa-home"></i> Home </a></li>
				      	<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>"><i class="fa fa-user"></i> Account</a></li>
				      	<li><a href="<?php echo base_url('/shopping/wishlist'); ?>"><i class="fa fa-magic"></i> Wish List</a></li>
				      	<li><a href="<?php echo base_url('shopping/cart'); ?>"><i class="fa fa-shopping-cart"></i> Shopping Cart (<span id="shopping_count"><?php echo @$_SESSION['cart']['qty'] ? $_SESSION['cart']['qty'] : 0 ; ?></span>)</a></li>
							</ul>
						</div>
					</div>
				</div>
				
				<!-- USER -->
				<div class="user">
					<p>
						<b><em id="top_notice">Major Credit Cards Accepted!
						<?php if(@$accountAddress['phone']): ?>
							<span style="color:#939;"> Order By Phone! <?php echo $accountAddress['phone']; ?></span>
						<?php endif; ?>
						Mon - Fri 8 - 5 EST<br></em></b>
						<?php if(@$_SESSION['userRecord']): ?>
						<b>Welcome: <?php echo @$_SESSION['userRecord']['first_name']; ?></b> | <b><a href="<?php echo $s_baseURL.'welcome/logout'; ?>"><u>Logout</u></a></b>
													<?php if($_SESSION['userRecord']['admin']): ?> |
							<a href="<?php echo base_url('admin'); ?>"><b><u>Admin Panel</u></b></a>
							<?php endif; ?>
						<?php else: ?>
							<a href="javascript:void(0);" onclick="openLogin();"><b><u>Login</u></b></a> |
							<a href="javascript:void(0);" onclick="openCreateAccount();"><b><u>Create Account</u></b></a>
						<?php endif; ?>
					</p>
				</div>
				<!-- END USER --> 
				
				<div class="clear"></div>
			</div>
			<!-- END NAVAGATION & USER INFO -->
<script>		

function openLogin()
{
	window.location.replace('<?php echo $s_baseURL.'checkout/account'; ?>');
	/*
$.post(s_base_url + 'welcome/load_login/', {}, function(returnData)
	{
		$.modal(returnData);
		$('#simplemodal-container').height('auto').width('auto');
		$(window).resize();
	});
*/
}
	
function openCreateAccount()
{
	window.location.replace('<?php echo $s_baseURL.'checkout/account'; ?>');
	/*
$.post(s_base_url + 'welcome/load_new_user/', {}, function(returnData)
	{
		$.modal(returnData);
		$('#simplemodal-container').height('auto').width('auto');
	  	$('#create_new').show();
	  	$('#login').hide();
	  	$(window).resize();
	});
*/
}
</script>