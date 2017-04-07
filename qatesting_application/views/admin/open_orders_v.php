<!-- CONTENT -->
<div class="content">	
				
	
	<!-- ADMIN SECTION -->
	<div class="admin_sect" style="width:95.6%;">
		
		
		<h1>Open Orders</h1>
		
		<div class="side_item">
		  <div class="dynamic_button" style="float:right;">
				<a href="<?php echo base_url('/admin/orders_pdf'); ?>">Download PDF</a>
			</div>
			<h3>Downloads</h3>
			<p class="post-info" >Download all new orders since previous download</p>
      <?php /* Note of the date/time and user who batched them out
             * First, Last, Email, Phone, Company, BilltoAddress, BilltoCity, BilltoState, BillToCountry, 
             * ShipToAddress, ShipToCity, ShipToState, ShipToCountry 
             */ ?>
		</div>

    <?php if(!@$orders): ?>
    
    		<!-- NO PURCHASES -->
    		<div class="no_purchases">
    			<h3>There are no new orders.</h3>
    			<div class="clear"></div>
    		</div>
    		<!-- END NO PURCHASES -->
    		
    <?php else: ?>	
      <?php foreach($orders as $order): ?>
      		<!-- ORDERS -->
      		<div class="tabular_data">
      			<table width="100%" cellpadding="5">
      				<tr class="head_row">
      					<td colspan="2"><b>Order Number <?php echo $order['order_id']; ?> & Date <?php echo date('m/d/Y', $order['order_date']); ?></b></td>
      				</tr>
      				<?php if(@$order['products']): $i = 0; foreach(@$order['products'] as $product): ?>
      				<tr>
      					<td><?php echo $product['display_name'] ? $product['display_name'] : 'Sales Tax';?></a></td>
      					<td style="width:20%;"><?php echo $product['price']; ?></td>
      				</tr>
      				<?php $i++; endforeach; endif; ?>
      				<tr>
      					<td>Shipping</td>
      					<td style="width:20%;"><?php echo $order['shipping']; ?></td>
      				</tr>
      				<tr class="head_row">
      					<td><b>Total:</b></td>
      					<td><?php echo $order['sales_price'] + $order['shipping']; ?></td>
      				</tr>
      			</table>
      		</div>
      		<!-- END ORDERS -->
      <?php endforeach; ?>
      <?php endif; ?>
      
      <br /><br />
      <h1>Processed Orders </h1>
      <?php if(@$pages > 1): $i = 1;?>
        <div style="float:right;margin-right:10px;">
    		<?php while($i <= $pages): ?>
    			<a href="<?php echo base_url('admin/open_orders/'.$i); ?>" >
    			  <?php if($i == $currentPage): ?><span style="color:red"><?php echo $i; ?></span>
    			    <?php else: echo $i; endif; ?>
    			 </a>
  		<?php $i++; endwhile; ?>
  </div>
<?php endif; ?>
    <div class="side_item">
    	<h3>Downloads</h3>

        <!-- PAGINATION -->
            <div id="pagination">
            	<?php echo @$pagination; ?>
            </div>
        <!-- END PAGINATION -->					
					<div class="clear"></div>
				
					<br>

    <?php if(@$prev_orders): foreach($prev_orders as $date): ?>
    <p class="post-info" >Download pdf for batch created <?php echo date('m/d/Y H:i:s', $date['process_date']); ?> UTC</p>
		  <div class="dynamic_button">
				<a href="<?php echo base_url('/admin/orders_pdf/'.$date['process_date']); ?>"><?php echo date('m/d/Y H:i:s', $date['process_date']); ?></a>
			</div>
			<br /><br />
		<?php endforeach; endif; ?>
				</div>

	</div>
</div>




