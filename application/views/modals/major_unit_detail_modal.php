<div class="modal fade pop" id="myModal<?php echo $motorcycle['id']; ?>">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body modal-body-dark">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>

				<p class="modal-body-title">Get Out-The-Door Price</p>

				<div class="modal-unit-details row">
					<div class="col-sm-5">
						<img class="img-responsive center-block" src="<?php echo $motorcycle_image; ?>"/>
					</div>

					<div class="col-sm-7">
						<ul>
							<li><strong><?php echo $motorcycle['title']; ?></strong></li>
							<li>Color: <?php echo $motorcycle['color']; ?></li>
							<li>Stock #: <?php echo $motorcycle['sku']; ?></li>
							<li>Color: <?php echo $motorcycle['color']; ?></li>
						</ul>
					</div>
				</div>

				<div class="row">
					<div class="hidden-xs col-sm-4">
						<ol class="modal-form-instructions">
							<li>Complete the form</li>
							<li>Tell us which model you're interested in</li>
							<li>Recieve your out-the-door price</li>
						</ol>
					</div>

					<div class="col-xs-12 col-sm-8">
						<div class="modal-form-container">
							<p>Fill out the form below to get your free out-the-door price!</p>

							<?php echo form_open('welcome/productEnquiry', array('class' => 'form_standard')); ?>
								<div class="form-group">
									<label for="firstName">First&nbsp;Name:</label>
									<input id="firstName" class="form-control" type="text" name="firstName" required="">
									<div class="formRequired">*</div>
								</div>

								<div class="form-group">
									<label for="lastName">Last&nbsp;Name:</label>
									<input id="lastName" class="form-control" type="text" name="lastName" required="">
									<div class="formRequired">*</div>
								</div>

								<div class="form-group">
									<label for="phone">Phone:</label>
									<input id="phone" class="form-control" type="text" name="phone">
								</div>

								<div class="form-group">
									<label for="email">Email:</label>
									<input id="email" class="form-control" type="email" name="email" required="">
									<div class="formRequired">*</div>
								</div>

								<div class="form-group">
									<label for="questions">Comments:</label>
									<textarea id="questions" class="form-control" type="text" name="questions"></textarea>
								</div>

								<input type="hidden" name="motorcycle" value="<?php echo $motorcycle['title']; ?>">
								<input type="hidden" name="product_id" value="<?php echo $motorcycle['id']; ?>">

								<div class="text-center">
									<input type="submit" class="btn">
								</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>

				<p class="modal-body-footer">We respect your privacy and won't share your information with any other company. Terms and conditions apply.</p>
			</div>
		</div>
	</div>
</div>