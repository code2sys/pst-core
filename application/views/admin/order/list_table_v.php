			<pre>
			<?php //print_r($orders); ?>
			</pre>
			<div class="tabular_data">
				<table width="100%" cellpadding="10">
					<tr class="head_row">
						<td><b>Order #</b></td>
						<td><b>Customer</b></td>
						<td><b>Order Date</b></td>
						<td><b>Order Time</b></td>
						<td><b>Source</b></td>
						<td><b>Total</b></td>
						<td><b>Paid</b></td>
						<td><b>Status</b></td>
						<td><b>Batch Order</b></td>
						<td><b>Distributor</b></td>
						<td><b>Weight</b></td>
						<td><b>Memo</b></td>
					</tr>
					<?php if(@$orders): foreach($orders as $order): ?>
					<tr>
						<td><a href="<?php echo base_url('admin/order_edit/'.$order['order_id']); ?>"><?php echo $order['order_id']; ?></a></td>
						<td><?php echo $order['first_name'] . ' ' . $order['last_name']; ?></td>
						<td><?php echo ($order['order_date']) ?  date('m/d/Y', $order['order_date']) : $order['processed_date']; ?></td>
						<td><?php echo ($order['order_date']) ?  date('H:i:s', $order['order_date']) : ''; ?></td>
						<td><img src="<?php echo $assets; ?>/images/<?php echo ($order['source']=="eBay"?"ebay_logo.png":"admin_logo.png"); ?>" height="30px" border="0"></td>
						<td><?php echo $order['sales_price'] + $order['shipping'] + $order['tax']; ?></td>
						<td><?php echo $order['paid']; ?></td>
						<td>
							<?php echo $order['status'] ? $order['status'] : 'Pending'; ?>
							<?php //if($order['will_call'] > 1): Pickup ?> 
							
						</td>
						<td><?php echo $order['batch_number'] ? 'Yes' : 'No'; ?></td>
						<td>
							<?php $distributor = array();
										 if(is_array($order['products'])):
											foreach($order['products'] as $product):
												if($product['distributor'])
												$distrubutor[$product['distributor']] = $product['distributor'];
											endforeach; endif;
							  ?>
						</td>
						<td><?php echo $order['weight']; ?></td>
						<td><?php echo substr($order['special_instr'], 0, 30); ?><?php if(strlen($order['special_instr']) > 31): ?>...<?php endif; ?></td>
					</tr>
					<?php endforeach; endif; ?>
				</table>
		</div>