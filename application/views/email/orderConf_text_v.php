Thank you for your purchase.

Attached please find a PDF version of your Order Confirmation with all your order details.

<?php if(!empty($ship_tracking_code)): ?>
	<?php if(!empty($carrier)): ?>
		Your order is being shipped by <?php echo $carrier; ?>.
	<?php endif; ?>
	Your tracking number is : <?php echo $ship_tracking_code; ?>.
	<?php if(!empty($trackingURL)): ?>
	Go to this URL to check your status: <?php echo $trackingURL; ?>
	<?php endif; ?>
<?php endif; ?>
									
Sincerely,
The <?php echo WEBSITE_NAME; ?> Team
