<div style="margin-top: 5px;"></div>
<a id="writeReviewAnchor" href="javascript:void(0);" onclick="$(this).hide();$('#writeReview').slideDown();$('#reviewBox').val('');" class="button" style="background: #78909C; color: #fff; padding: 5px; margin-left: 3px; text-decoration: none;">Write a Review</a>

<div id="writeReview" class="hide">
<table class="hidden_table" width="100%">
	<tr>
		<td>
			<input type="hidden" id="rating" name="rating" value="0"><input type="hidden" id="part_id" name="part_id" value="<?php echo $part_id; ?>">
		</td>
	</tr>
	<tr>
		<td>
			<div style="float:left;">Rating:&nbsp;</div>
			<div id="rate" class="rating" style="float:left;">&nbsp;</div>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo form_textarea(array('name' => 'review', 
		   'value' => '', 
		   'id' => "reviewBox",
		   'rows' => "6", 
		   'cols' => "50", 
		   'placeholder' => "Tell us what you think of this product.",
		   'style' => "width:100%;resize: none;border-radius: 3px;padding: 5px;",
		   'onchange' => "$('#reviewBox').css('border','1px solid rgb(169, 169, 169)');")); ?>
	   </td>
	</tr>
	<tr>
		<td></td>
	</tr>
</table>
<div style="margin-top: 5px;"></div>
<a href="javascript:void(0);" onclick="submitReview();" class="button" style="background: #78909C; color: #fff; padding: 5px; margin-left: 3px; text-decoration: none;">Submit Your Review</a> <a href="javascript:void(0);" onclick="$('#writeReview').hide();$('#writeReviewAnchor').show();" class="button" style="text-decoration: none;color:#78909C;">Cancel</a>
</div>

<div id="reviewComplete" class="hide">
	<p>Your review has been submitted.  Thank you for your feedback.</p>
</div>


<div class="clear"></div>
<?php if(@$reviews): ?>
<br />
<table class="hidden_table" width="100%">
	<?php foreach($reviews as $review): ?>
	<tr>
		<td style="padding: 15px; border: 1px solid #cecece; border-radius: 5px;">
		
		<?php for($i=0; $i < $review['rating']; $i++): ?> <i class="fa fa-star" style="color:#FFD700"></i><?php endfor; ?>
		<div style="margin-top: 5px;"></div>
		
		<strong><?php echo @$review['first_name'] ? $review['first_name'] : 'Anonymous'; ?></strong>
		<div style="margin-top: 5px;"></div>
		
		<?php echo date('d M Y', $review['date']); ?>
		<div style="margin-top: 5px;"></div>

		<?php echo $review['review']; ?>
		
		</td>
		
	</tr>
	<tr><td></td></tr>
	<?php endforeach; ?>
</table>
<?php else: ?>
<h2>Be the first to Review!</h2>
<?php endif; ?>

<script>
	function submitReview()
	{	
		if( $.trim($('#reviewBox').val()).length == 0){
			$('#reviewBox').css("border","1px solid red");
			return false;
		}else{
			$('#reviewBox').css("border","1px solid rgb(169, 169, 169)");
		}
	
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
				$('#writeReviewAnchor').show();
				setTimeout(function(){
					$('#reviewComplete').slideUp();
				},5000);
			});

	}
	
	$(document).ready(function() {
		$('#rate').rating('www.url.php', {maxvalue:5});
	});

</script>