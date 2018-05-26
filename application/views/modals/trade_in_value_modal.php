<div class="modal fade pop" id="trade-in-value-modal_<?php echo $motorcycle['id']; ?>">
	<div class="modal-dialog area" style="width:380px !important;">
		<div class="modal-content">
			<div class="modal-header">
				<div class="clo" data-dismiss="modal">get a quote</div>
			</div>

			<div class="modal-body" id="scol">
				<?php echo form_open('welcome/productEnquiry', array('class' => 'form_standard')); ?>
					<div class="form-group">
						<input type="text" class="form-control" placeholder="first name" name="firstName" required="">
						<div class="formRequired">*</div>
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="last name" name="lastName" required="">
						<div class="formRequired">*</div>
					</div>

					<div class="form-group">
						<input type="email" class="form-control" placeholder="email" name="email" required="">
						<div class="formRequired">*</div>
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="phone" name="phone">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="address" name="address">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="city" name="city">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="state" name="state">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="zip code" name="zipcode">
					</div>

					<?php if (!defined('DISABLE_TEST_DRIVE') || !DISABLE_TEST_DRIVE): ?>
						<h3 class="txt-title"><?php if (defined('WORDING_WANT_TO_SCHEDULE_A_TEST_DRIVE')) { echo WORDING_WANT_TO_SCHEDULE_A_TEST_DRIVE; } else { ?>Want to Schedule a Test Drive?<?php } ?></h3>

						<div class="form-group">
							<input type="text" class="form-control" placeholder="<?php if (defined('WORDING_PLACEHOLDER_DATE_OF_RIDE')) { echo WORDING_PLACEHOLDER_DATE_OF_RIDE; } else { ?>date of ride<?php } ?>" name="date_of_ride">
						</div>

						<hr class="brdr">
					<?php endif; ?>

					<h3 class="txt-title">Trade in?</h3>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="make" name="make">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="model" name="model">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="year" name="year">
					</div>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="miles" name="miles">
					</div>

					<div class="form-group">
						<textarea type="text" class="form-control" placeholder="added accessories" name="accessories"></textarea>
					</div>

					<div class="form-group">
						<textarea type="text" class="form-control" placeholder="comments questions" name="questions"></textarea>
					</div>

					<h3 class="txt-title">I am Interested in this Vehicle</h3>

					<div class="form-group">
						<input type="text" class="form-control" placeholder="Unit Name" value="<?php echo $motorcycle['title']; ?>" readonly name="motorcycle">
					</div>

					<input type="hidden" name="product_id" value="<?php echo $motorcycle['id']; ?>">

					<div class="col-md-12 text-center" style="float:none;">
						<input type="submit" class="btn bttn">
					</div>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>
