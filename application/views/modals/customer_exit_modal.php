<div class="modal fade pop" id="customer-exit-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-body">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>

				<p class="modal-body-title">Pump <span>The</span> Brakes!</p>

				<p class="modal-body-subtitle">
					<span>Don't Leave Yet</span>
				</p>

				<p class="modal-body-message">
					<span>Can't find what you're looking for? Let us know what you are trying to find and we'll help you out!</span>
				</p>

				<div class="modal-form-container">
					<?php echo form_open('welcome/productEnquiry', array('class' => 'form_standard')); ?>
						<div class="modal-form-field-outer-container">
							<div class="modal-form-field-inner-container">
								<div class="form-group">
									<label for="firstName">First&nbsp;Name:</label>
									<input id="firstName" class="form-control" type="text" name="firstName" required>
									<div class="formRequired">*</div>
								</div>

								<div class="form-group">
									<label for="lastName">Last&nbsp;Name:</label>
									<input id="lastName" class="form-control" type="text" name="lastName" required>
									<div class="formRequired">*</div>
								</div>

								<div class="form-group">
									<label for="phone">Phone:</label>
									<input id="phone" class="form-control" type="text" name="phone">
								</div>

								<div class="form-group">
									<label for="email">Email:</label>
									<input id="email" class="form-control" type="email" name="email" required>
									<div class="formRequired">*</div>
								</div>

								<div class="form-group">
									<label for="questions">Trying&nbsp;to&nbsp;Find:</label>
									<textarea id="questions" class="form-control" type="text" name="questions"></textarea>
								</div>
							</div>
						</div>

						<div class="text-center">
							<input class="btn" type="submit" value="Submit">
						</div>
					<?php echo form_close(); ?>
				</div>

				<p class="modal-body-footer">We respect your privacy and won't share your information with any other company. Terms and conditions apply.</p>
			</div>
		</div>
	</div>
</div>

<script type="application/javascript">
$(document).ready(function () {
	var mouseLeaveEventCount = 0;

	// Show Customer Exit modal
	$(document).mouseleave(function () {
		var siteModalsState = JSON.parse(localStorage.getItem('siteModalsState')) || {};

		// Keep track of how many times the cursor leaves the page
		mouseLeaveEventCount += 1;

		// Don't show Customer Exit modal more than once on this page
		if (mouseLeaveEventCount > 1) return;

		// If user has already seen the modal on two pages don't show it again
		if (siteModalsState['customerExitModalViewCount'] >= 2) return;

		// If user has already made a form submission on another modal, don't show this modal
		if (siteModalsState['hasContactedSales']) return;

		// None of the above conditions held; show the modal
		siteModalsState['customerExitModalViewCount'] = (siteModalsState['customerExitModalViewCount'] + 1) || 1;
		localStorage.setItem('siteModalsState', JSON.stringify(siteModalsState));
		$('.modal').modal('hide');

		// Fixes Bootstrap bug
		setTimeout(function () {
			$('#customer-exit-modal').modal('show');
		}, 500);
	});

	// Record the modal form submission so we don't show more sales modals
	$('.modal form input[type=submit]').click(function () {
		var siteModalsState = JSON.parse(localStorage.getItem('siteModalsState')) || {};
		siteModalsState['hasContactedSales'] = true;
		localStorage.setItem('siteModalsState', JSON.stringify(siteModalsState));
	});
});
</script>
