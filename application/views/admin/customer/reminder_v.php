<div class="benzsw">
  <form method="post" action="<?php echo site_url('admin/saveUpdateReminderCustomer/'.$rem['id']);?>" id="reminder_form">
  <?php
	$data = json_decode($rem['data']);
	$dis = false;
	if($data->recur == 'monthly') {
		$dis = true;
	} else if( $data->recur == 'yearly' ) {
		$dis = true;
	}
  ?>
	<div class="benzpopup">
		<div class="pophdr">
			<a class="clspopup" href="javascript:void(0);"><i class="fa fa-times" aria-hidden="true"></i></a>
			<div class="hdroptn">
				<div class="svcls">
					<span class="icn"><i class="fa fa-floppy-o" aria-hidden="true"></i></span>
					<span class="txt">Save & Close</span>
				</div>
				<div class="dlt" data-id="<?php echo $rem['id'];?>" data-user="<?php echo $user_id;?>">
					<span class="icn"><i class="fa fa-times" aria-hidden="true"></i></span>
					<span class="txt">Delete</span>
				</div>
				<div class="rcrnce">
					<span class="icn"><i class="fa fa-refresh" aria-hidden="true"></i></span>
					<span class="txt">Recurrence</span>
				</div>
				<div class="rcrnceptrn">
					<span class="txt">Recurrence Pattern</span>
					<div class="rcrnceoptn">
						<div class="rdo">
							<input type="radio" name="recur" id="dly" value="daily" <?php echo $data->recur == 'daily' ? 'checked' : '';?>>
							<label for="dly">Daily</label>
						</div>
						<div class="rdo">
							<input type="radio" name="recur" id="wkly" value="weekly" <?php echo $data->recur == 'weekly' ? 'checked' : '';?>>
							<label for="wkly">Weekly</label>
						</div>
						<div class="rdo">
							<input type="radio" name="recur" id="mntly" value="monthly" <?php echo $data->recur == 'monthly' ? 'checked' : '';?>>
							<label for="mntly">Montly</label>
						</div>
						<div class="rdo">
							<input type="radio" name="recur" id="yrly" value="yearly" <?php echo $data->recur == 'yearly' ? 'checked' : '';?>>
							<label for="yrly">Yearly</label>
						</div>
					</div>
					<div class="wkoptn">
						<span class="evry">Recur every <input type="number" name="rcr_evry" value="<?php echo $data->recur_evry;?>" <?php echo $dis == true ? 'disabled' : '';?>> week(s) on:</span>
						<div class="wk">
							<span class="wkdy">
								<input type="checkbox" value="monday" name="recur_per[]" id="monday" <?php echo in_array('monday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="monday">Monday</label>
							</span>
							<span class="wkdy">
								<input type="checkbox" value="tuesday" name="recur_per[]" id="tuesday" <?php echo in_array('tuesday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="tuesday">Tuesday</label>
							</span>
							<span class="wkdy">
								<input type="checkbox" value="wednesday" name="recur_per[]" id="wednesday" <?php echo in_array('wednesday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="wednesday">Wednesday</label>
							</span>
							<span class="wkdy">
								<input type="checkbox" value="thursday" name="recur_per[]" id="thursday" <?php echo in_array('thursday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="thursday">Thursday</label>
							</span><br />
							<span class="wkdy">
								<input type="checkbox" value="friday" name="recur_per[]" id="friday" <?php echo in_array('friday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="friday">Friday</label>
							</span>
							<span class="wkdy">
								<input type="checkbox" value="saturday" name="recur_per[]" id="saturday" <?php echo in_array('saturday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="saturday">Saturday</label>
							</span>
							<span class="wkdy">
								<input type="checkbox" value="sunday" name="recur_per[]" id="sunday" <?php echo in_array('sunday', $data->recur_per) ? 'checked' : '';?> <?php echo $dis == true ? 'disabled' : '';?>>
								<label for="sunday">Sunday</label>
							</span>
						</div>
						<div>
						<?php if( $rem['id'] != '' ) { ?>
							<div class="sv" id="button">
							<?php $prnt = $rem['parent'] == '0' ? $rem['id'] : $rem['parent']; ?>
								<span class="btnlk rmv-cmpltd" data-id="<?php echo $prnt;?>" data-rmvd="<?php echo $rem['id'];?>">Remove Recurrence</span>
							</div>
						<?php } ?>
						</div>
					</div>
					<?php if( $rem['id'] != '' ) { ?>
						<div class="sv" id="cmpletd">
							<span class="btnlk cmpltd" data-id="<?php echo $rem['id'];?>"><i class="fa fa-check" aria-hidden="true"></i>Completed</span>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="popcntnt">
			<div class="rw">
				<div class="lft">Subject</div>
				<div class="ryt"><input type="text" name="subject" required class="sbjct" value="<?php echo $rem['subject'];?>"></div>
			</div>
			<div class="rw">
				<div class="lft">Start Date</div>
				<div class="ryt">
					<input type="date" name="start_date" required value="<?php echo $dateReminder;?>" />
					<select name="start_time">
						<option value="">Select Time</option>
						<?php foreach( $tm as $time) { ?>
							<option value="<?php echo $time;?>"><?php echo $time;?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="rw">
				<div class="lft">End Date</div>
				<div class="ryt">
					<input style="float:left !important; margin-top: 6px;margin-right: 5px;" type="date" name="end_date" required value="<?php echo $dateReminder;?>" />
					<select style="float:left !important;" name="end_time">
						<option value="">Select Time</option>
						<?php foreach( $tm as $time) { ?>
							<option value="<?php echo $time;?>"><?php echo $time;?></option>
						<?php } ?>
					</select>
					<div style="margin-top: 11px;">
						<input style="float:left !important;" type="checkbox" value="1" name="recur_end" id="recur_end" checked>
						<label for="recur_end" style="width:30% !important; float:left !important;">No Recurrence End Date</label>
					</div>
				</div>
			</div>
			<div class="rw">
				<textarea name="notes" required><?php echo $rem['notes'];?></textarea>
			</div>
		</div>
		<input type="hidden" name="date" value="<?php echo $dateReminder;?>" required>
		<input type="hidden" name="user_id" value="<?php echo $user_id;?>" required>
	</div>
  </form>
</div>