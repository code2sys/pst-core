<style>
.lft{min-width:190px; font-weight:bold;}
.flds{width:1000px; height:26px; padding:3px; border:1px solid #D8D9DA; color:#A7A7A7;box-shadow: inset 0 1px 1px rgba(0,0,0,0.075); font-size:15px;}
#bnz table tr td{border:1px solid white;}
label{cursor:pointer;}
.twnty{width:25%; float:left;}
</style>

<div class="content_wrap">
	<div class="content">
		<h1><i class="fa fa-users"></i>&nbsp;Employee Edit</h1>
		<div id="listTable">
			<div class="tabular_data" id="bnz">
				<form action="<?php echo base_url('admin/employee_edit/'.$employee['id']); ?>" method="post" class="form_standard">
					<table width="100%" cellpadding="10">
						<tr>
							<td class="lft">First Name</td>
							<td><input class="flds" type='text' value="<?php echo $employee['first_name']; ?>" name="first_name" required></td>
						</tr>
						<tr>
							<td class="lft">Last Name</td>
							<td><input class="flds" type="text" name="last_name" required value="<?php echo $employee['last_name']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Address</td>
							<td><input class="flds" type="text" name="street_address" required value="<?php echo $employee['street_address']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Address 2</td>
							<td><input class="flds" type="text" name="address_2" value="<?php echo $employee['address_2']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">City</td>
							<td><input class="flds" type="text" name="city" required value="<?php echo $employee['city']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">State</td>
							<td><input class="flds" type="text" name="state" required value="<?php echo $employee['state']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Country</td>
							<td><input class="flds" type="text" name="country" required value="<?php echo $employee['country']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Zip</td>
							<td><input class="flds" type="text" name="zip" required value="<?php echo $employee['zip']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Phone</td>
							<td><input class="flds" type="text" name="phone" required value="<?php echo $employee['phone']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Email</td>
							<td><input class="flds" type="text" name="email" value="<?php echo $employee['email']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Username</td>
							<td><input class="flds" type="text" name="username" value="<?php echo $employee['username']; ?>"></td>
						</tr>
						<tr>
							<td class="lft">Password</td>
							<td><input class="flds" type="password" name="password" value=""></td>
						</tr>
						<tr>
							<td class="lft">Confirm Password</td>
							<td><input class="flds" type="password" name="cpassword" value=""></td>
						</tr>
						<tr>
							<td class="lft">Super User</td>
							<td>
								<input type="radio" name="admin" value="1" <?php echo $employee['admin'] == 1 ? 'checked' : '';?>>Yes
								<input type="radio" name="admin" value="0" <?php echo $employee['admin'] == 0 ? 'checked' : '';?>>No
							</td>
						</tr>
						<tr>
							<td class="lft">Active</td>
							<td>
								<input type="radio" name="status" value="1" <?php echo $employee['status'] == 1 ? 'checked' : '';?>>Yes
								<input type="radio" name="status" value="0" <?php echo $employee['status'] == 0 ? 'checked' : '';?>>No
							</td>
						</tr>
						<tr>
							<td class="lft">
								<h4>Content</h4>
								<label class="checkbox">
									<input type="checkbox" value="social_media" name="permission[social_media]" <?php echo in_array('social_media', $employee['permissions']) ? 'checked' : '';?>/>Social Media
								</label>
								<label class="checkbox">
									<input type="checkbox" value="reviews" name="permission[reviews]" <?php echo in_array('reviews', $employee['permissions']) ? 'checked' : '';?>/>Reviews
								</label>
								<label class="checkbox">
									<input type="checkbox" value="pages" name="permission[pages]" <?php echo in_array('pages', $employee['permissions']) ? 'checked' : '';?>/>Pages
								</label>
								<label class="checkbox">
									<input type="checkbox" value="email" name="permission[email]" <?php echo in_array('email', $employee['permissions']) ? 'checked' : '';?>/>Email
								</label>
								<label class="checkbox">
									<input type="checkbox" value="data_feeds" name="permission[data_feeds]" <?php echo in_array('data_feeds', $employee['permissions']) ? 'checked' : '';?>/>Data Feeds
								</label>
								<!--<label class="checkbox">
									<input type="checkbox" value="company_info" name="permission[company_info]" <?php echo in_array('company_info', $employee['permissions']) ? 'checked' : '';?>/>Company Info
								</label>-->
								<label class="checkbox">
									<input type="checkbox" value="distributors" name="permission[distributors]" <?php echo in_array('distributors', $employee['permissions']) ? 'checked' : '';?>/>Distributors
								</label>
							</td>
							<td>
								<div class="twnty">
									<h4>Store</h4>
									<label class="checkbox">
										<input type="checkbox" value="categories" name="permission[categories]" <?php echo in_array('categories', $employee['permissions']) ? 'checked' : '';?>/>Categories
									</label>
									<label class="checkbox">
										<input type="checkbox" value="brands" name="permission[brands]" <?php echo in_array('brands', $employee['permissions']) ? 'checked' : '';?>/>Brands
									</label>
									<label class="checkbox">
										<input type="checkbox" value="products" name="permission[products]" <?php echo in_array('products', $employee['permissions']) ? 'checked' : '';?>/>Products
									</label>
									<label class="checkbox">
										<input type="checkbox" value="orders" name="permission[orders]" <?php echo in_array('orders', $employee['permissions']) ? 'checked' : '';?>/>Orders
									</label>
									<label class="checkbox">
										<input type="checkbox" value="taxes" name="permission[taxes]" <?php echo in_array('taxes', $employee['permissions']) ? 'checked' : '';?>/>Taxes
									</label>
									<label class="checkbox">
										<input type="checkbox" value="shipping" name="permission[shipping]" <?php echo in_array('shipping', $employee['permissions']) ? 'checked' : '';?>/>Shipping
									</label>
									<label class="checkbox">
										<input type="checkbox" value="coupons" name="permission[coupons]" <?php echo in_array('coupons', $employee['permissions']) ? 'checked' : '';?>/>Coupons
									</label>
									<label class="checkbox">
										<input type="checkbox" value="product_receiving" name="permission[product_receiving]" <?php echo in_array('product_receiving', $employee['permissions']) ? 'checked' : '';?>/>Product Receiving
									</label>
									<label class="checkbox">
										<input type="checkbox" value="mInventory" name="permission[mInventory]" <?php echo in_array('mInventory', $employee['permissions']) ? 'checked' : '';?>/>Motorcycle Receiving
									</label>
									<label class="checkbox">
										<input type="checkbox" value="customers" name="permission[customers]" <?php echo in_array('customers', $employee['permissions']) ? 'checked' : '';?>/>Customers
									</label>
									<label class="checkbox">
										<input type="radio" value="all_customers" name="prmsion" <?php echo in_array('all_customers', $employee['permissions']) ? 'checked' : '';?>/>All Customers
									</label>
									<label class="checkbox">
										<input type="radio" value="web_customers" name="prmsion" <?php echo in_array('web_customers', $employee['permissions']) ? 'checked' : '';?>/>Web Customers
									</label>
									<label class="checkbox">
										<input type="radio" value="user_specific_customers" name="prmsion" <?php echo in_array('user_specific_customers', $employee['permissions']) ? 'checked' : '';?>/>User Specific Customers
									</label>
								</div>
								<div class="twnty">
									<h4>Finance</h4>
									<label class="checkbox">
										<input type="checkbox" value="finance" name="permission[finance]" <?php echo in_array('finance', $employee['permissions']) ? 'checked' : '';?>/>Credit Applications
									</label>
								</div>
								<div class="twnty">
									<h4>Users</h4>
									<!--<label class="checkbox">
										<input type="checkbox" value="list" name="permission[list]" <?php echo in_array('list', $employee['permissions']) ? 'checked' : '';?>/>List
									</label>-->
									<label class="checkbox">
										<input type="checkbox" value="employees" name="permission[employees]" <?php echo in_array('employees', $employee['permissions']) ? 'checked' : '';?>/>Employees
									</label>
									<label class="checkbox">
										<input type="checkbox" value="profile" name="permission[profile]" <?php echo in_array('profile', $employee['permissions']) ? 'checked' : '';?>/>Store Profile
									</label>
								</div>
								<div class="twnty">
									<h4>View Credit Card Info</h4>
									<label class="checkbox">
										<input type="radio" value="1" <?php echo $employee['cc_permission'] == 1 ? 'checked' : '';?> name="cc_permission" />Yes 
									</label>
									<label class="checkbox">
										<input type="radio" value="0" name="cc_permission" <?php echo $employee['cc_permission'] == 0 ? 'checked' : '';?> />No 
									</label>
								</div>
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="Submit" value="Update" style="padding:8px 5px; width:100%; cursor:pointer;">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>