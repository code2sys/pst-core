<div class="content_wrap">
	<div class="content">
		<div id="listTable">
			<div class="tabular_data">
				<table width="45%" style="float:left;">
					<?php if(@$customer): ?>
						<tr>
							<td><?php echo $customer['first_name'].' '.$customer['last_name']; ?></td>
						</tr>
						<tr>
							<td><?php echo $customer['street_address']; ?></td>
						</tr>
						<tr>
							<td><?php echo $customer['address_2']; ?></td>
						</tr>
						<tr>
							<td><?php echo $customer['city'].' '.$customer['state'].' '.$customer['zip']; ?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><?php echo $customer['email']; ?></td>
						</tr>
						<tr>
							<td><?php echo $customer['phone']; ?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><button>Edit Customer</button></td>
						</tr>
						<tr>
							<td><b>Customer Since:</b> <?php echo $customer['phone']; ?></td>
						</tr>
					<?php endif; ?>
				</table>
				<div style="width:10px;"></div>
				<table width="45%" cellpadding="10" style="float:left;">
					<tr class="head_row">
						<td><b>Order #</b></td>
						<td><b>Order Date</b></td>
						<td><b>Total</b></td>
						<td><b>Status</b></td>
					</tr>
					<?php if(@$customer['orders']):
						foreach($customer['orders'] as $order): ?>
							<tr>
								<td><a href="<?php echo base_url('admin/order_edit/'.$order['order_id']); ?>"><?php echo $order['order_id']; ?></a></td>
								<td><?php echo ($order['order_date']) ?  date('m/d/Y', $order['order_date']) : $order['processed_date']; ?></td>
								<td>$<?php echo $order['sales_price'] + $order['shipping'] + $order['tax']; ?></td>
								<td><?php echo $order['status'] ? $order['status'] : 'Pending'; ?></td>
							</tr>
					<?php endforeach;
					endif; ?>
				</table>
			</div>
		</div>
	</div>
</div>