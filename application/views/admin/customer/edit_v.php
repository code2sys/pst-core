<style>
.lft{min-width:190px; font-weight:bold;}
.flds{width:1000px; height:26px; padding:3px; border:1px solid #D8D9DA; color:#A7A7A7;box-shadow: inset 0 1px 1px rgba(0,0,0,0.075); font-size:15px;}
#bnz table tr td{border:1px solid white;}
label{cursor:pointer;}
.twnty{width:25%; float:left;}
</style>

<div class="content_wrap">
	<div class="content">
		<h1><i class="fa fa-users"></i>&nbsp;Customer Edit</h1>
		<div id="listTable">
			<div class="tabular_data" id="bnz">
				<form action="<?php echo base_url('admin/update_customer/'.$customer['id']); ?>" method="post" class="form_standard">
					<table width="100%" cellpadding="10">
						<tr>
							<td class="lft">First Name</td>
							<td><input type='text' value="<?php echo $customer['first_name']; ?>" name="first_name" required class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Last Name</td>
							<td><input type="text" name="last_name" value="<?php echo $customer['last_name']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Street Address</td>
							<td><input type="text" name="street_address" required value="<?php echo $customer['street_address']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Address 2</td>
							<td><input type="text" name="address_2" value="<?php echo $customer['address_2']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">City</td>
							<td><input type="text" name="city" required value="<?php echo $customer['city']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">State</td>
							<td><input type="text" name="state" required value="<?php echo $customer['state']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Zip</td>
							<td><input type="text" name="zip" required value="<?php echo $customer['zip']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Email</td>
							<td><input type="text" name="email" value="<?php echo $customer['email']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Password</td>
							<td><input type="password" name="password" value="" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Confirm Password</td>
							<td><input type="password" name="cpassword" value="" class="flds"></td>
						</tr>
						<tr>
							<td class="lft">Phone</td>
							<td><input type="text" name="phone" required value="<?php echo $customer['phone']; ?>" class="flds"></td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="Submit" value="Save" style="padding:8px 5px; width:100%; cursor:pointer;">
							</td>
						</tr>
					</table>
				</form>
			</div>
		</div>
	</div>
</div>