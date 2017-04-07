<a href="javascript:void(0);" onclick="$('#writeReview').show();" class="button">Write a Review</a>
<div class="clear"></div>
<?php if(@$reviews): ?>
<br />
<table class="hidden_table" width="100%">
	<tr>
		<td>Date:</td><td>Rating</td><td>Name:</td><td>Review:</td>
	</tr>
	<?php foreach($reviews as $review): ?>
	<tr>
		<td><?php echo date('m/d/Y', $review['date']); ?></td>
		<td><?php for($i=0; $i < $review['rating']; $i++): ?> <i class="fa fa-star" style="color:#FFD700"></i><?php endfor; ?></td>
		<td><?php echo @$review['first_name'] ? $review['first_name'] : 'Anonymous'; ?></td>
		<td><?php echo $review['review']; ?></td>
		
	</tr>
	<?php endforeach; ?>
</table>
<?php else: ?>
<h2>Be the first to Review!</h2>
<?php endif; ?>

<div id="reviewComplete" class="hide">
	<p>Your review has been submitted.  Thank you for your feedback.</p>
</div>

<div id="writeReview" class="hide">
<table class="hidden_table">
	<tr><td>
			<input type="hidden" id="rating" name="rating" value="0"><input type="hidden" id="part_id" name="part_id" value="<?php echo $part_id; ?>"></td></tr>
	<tr>
		<td><div style="float:left">Review:</div><div id="rate" class="rating" style="float:right">&nbsp;</div> </td>
	</tr>
	<tr>
		<td><?php echo form_textarea(array('name' => 'review', 
																			                               'value' => '', 
																			                               'id' => 'reviewBox',
																			                               'rows' => '6', 
																			                               'cols' => '50', 
																			                               'placeholder' => 'Tell us what you think of this product.',
																			                               'style' => 'width:96%')); ?></td>
	</tr>
	<tr>
		<td>
			
		</td>
	</tr>
</table>
<a href="javascript:void(0);" onclick="submitReview();" class="button">Submit Your Review</a>
<div class="clear">
</div>



<script>
	function submitReview()
	{	
		 $.post(base_url + 'ajax/createReview/',
			{ 
			 'review' : $('#reviewBox').val(),
			 'rating' : $('#rating').val(),
			 'part_id' : $('#part_id').val(),
			 'ajax' : true
			},
			function()
			{
				$('#writeReview').hide();
				$('#reviewComplete').show();
			});

	}
	
	$(document).ready(function() {
		$('#rate').rating('www.url.php', {maxvalue:5});
	});

</script>