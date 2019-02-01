<style>
.tabular_data td{border:1px solid white;}
.pointer {cursor:pointer;color:#06C;}
.activities table {border:unset;}
.activities table thead th {background: #EEE;border:unset;}
.activities td {border-bottom: 1px solid #AAA;}
.activities .dataTables_length {
	margin-bottom:4px;
}
.bnz-frm input{width:270px; height:20px; padding:4px; background:#F6F6F6; border:1px solid #AAA; border-radius:3px;}
.dsply-dtl{width:45%; float:left; height:30px;}
.notes-section{width:100%; float:left; margin:15px 0 15px 0;}
.notes-section label{width:100%; display:block; font-weight:bold;}
.notes-section textarea{width:99%; height:80px;}
.notes-section input[type=submit]{float:right; margin:7px 0 0 0; padding:5px 12px;}
.notes-section-footer .show-less { display:none;}
.notes-section-footer .show-more { display:block;}
.notes-section-footer.expanded .show-less { display:block;}
.notes-section-footer.expanded .show-more { display:none;}

.notes-section .text-wrapper {display:flex;flex-direction:column;align-items:flex-start;}
.notes-view {padding: 8px 0px;}
.notes-view .note-view {display:flex; align-items:flex-start;justify-content:start;padding:4px 0px;border-bottom:1px solid #AAA;}
.notes-view .note-view .portrait-wrapper {display:flex;width:32px;height:32px;justify-content:center;}
.notes-view .note-view .portrait-wrapper img{max-width:32px;max-height:32px;object-fit: contain;}
.notes-view .note-view .note-detail {display:flex; flex-direction:column;flex:1;}
.notes-view .note-view .note-info {padding-top:4px;}
.notes-view:not(.expanded) .notes-holder .note-view:nth-child(n+3) {display:none;}
.notes-view.has-few .notes-section-footer{display:none;}
.notes-view .notes-section-footer {background: #EEE;padding:8px;display: flex;justify-content: space-between;}
.notes-view .notes-section-footer .show-less { display:none;color:#06C;cursor:pointer;}
.notes-view .notes-section-footer .show-more { display:block;color:#06C;cursor:pointer;}
.notes-view.expanded .notes-section-footer .show-less { display:block;}
.notes-view.expanded .notes-section-footer .show-more { display:none;}
</style>
<script type="application/javascript" src="/assets/underscore/underscore-min.js" ></script>
<script type="application/javascript" src="/assets/backbone/backbone-min.js" ></script>
<script type="application/javascript"  src="/assets/js_front/moment.js"></script>
<div class="content_wrap" style="background:white; float:left;">
	<div class="content">
		<div class="tabular_data">
			<table width="100%" style="float:left; margin:0 3% 0 0;">
				<tr class="billing_display">
					<td style="width:50%">
						<div style="display:flex;align-items:center;justify-content:center;">
							<h1 style="color:black;font-size:24px;">Sales Lead Owner</h1>
							
							<select id="sales_owner" name="created_by" style="margin:8px;max-width:200px">
							<option value="">None</option>
							<?php
							foreach($sales_persons as $sales_person): ?>
								<option value="<?php echo $sales_person['id']?>" <?php echo $customer['created_by'] == $sales_person['id'] ? 'selected': '';?>>
								<?php echo $sales_person['first_name'].' '.$sales_person['last_name'].'('.$sales_person['email'].')';?>
								</option>
							<?php endforeach;?>
							</select>
						</div>
					</td>
					<td style="width:50%">
					</td>
				</tr>
			</table>
		</div>
		
		<h1 style="padding:5px; letter-spacing:0px; font-size:26px;"><i class="fa fa-users"></i>&nbsp;Customer Details</h1>
		<div id="listTable">
			<div class="tabular_data">
			<form method="post" action="">
				<table width="45%" style="float:left; margin:0 3% 0 0;">
					<?php if(@$customer): ?>
						<tr class="billing_display">
							<td><?php echo $customer['first_name'].' '.$customer['last_name']; ?></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['street_address']; ?></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['address_2']; ?></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['city'].' '.$customer['state'].' '.$customer['zip']; ?></td>
						</tr>
						<tr class="billing_display">
							<td>&nbsp;</td>
						</tr>
						<tr class="billing_display">
							<td><a href="mailto:<?php echo $customer['email']; ?>"><?php echo $customer['email']; ?></a></td>
						</tr>
						<tr class="billing_display">
							<td><?php echo $customer['phone']; ?></td>
						</tr>
						<tr class="billing_display">
							<td>&nbsp;</td>
						</tr>
						<tr>
							<td><button type="button" onclick="$('.billing_display').hide(); $('.billing_edit').show();">Edit Customer</button></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
						</tr>
						<!--<tr>
							<td><b>Customer Since:</b> <?php echo $customer['phone']; ?></td>
						</tr>-->
						
						<tr>
							<td>
								<div class="hidden_table billing_edit hide bnz-frm" style="border:1px solid silver;">
									<table width="100%" cellpadding="8">
										<tr>
											<td><b>Company Name:</b></td>
											<td><?php
												echo form_input(array('name' => 'company[]',
													'value' => @$customer['company'],
													'placeholder' => 'Enter Company Name',
													'id' => 'billing_company',
													'class' => 'text large'));
												?>
											</td>
										</tr>
										<tr>
											<td><b>First Name:*</b></td>
											<td><?php
												echo form_input(array('name' => 'first_name[]',
													'value' => @$customer['first_name'],
													'id' => 'billing_first_name',
													'placeholder' => 'Enter First Name',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td><b>Last Name:*</b></td>
											<td><?php
												echo form_input(array('name' => 'last_name[]',
													'value' => @$customer['last_name'],
													'id' => 'billing_last_name',
													'class' => 'text large',
													'placeholder' => 'Enter Last Name'));
												?></td>
										</tr>
										<tr>
											<td><b>Email Address:*</b></td>
											<td><?php
												echo form_input(array('name' => 'email[]',
													'value' => @$customer['email'],
													'id' => 'billing_email',
													'placeholder' => 'Enter Email Address',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td><b>Phone:</b></td>
											<td><?php
												echo form_input(array('name' => 'phone[]',
													'value' => @$customer['phone'],
													'id' => 'billing_phone',
													'placeholder' => 'Enter Phone Number',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td id="billing_street_address_label"><b>Address Line 1:*</b></td>
											<td><?php
												echo form_input(array('name' => 'street_address[]',
													'value' => @$customer['street_address'],
													'id' => 'billing_street_address',
													'class' => 'text large',
													'placeholder' => 'Enter Address'));
												?></td>
										</tr>
										<tr>
											<td id="billing_address_2_label"><b>Address Line 2:</b></td>
											<td><?php
												echo form_input(array('name' => 'address_2[]',
													'value' => @$customer['address_2'],
													'id' => 'billing_address_2',
													'class' => 'text large',
													'placeholder' => 'Apt. Bld. Etc'));
												?></td>
										</tr>
										<tr>
											<td id="billing_city_label"><b>City:*</b></td>
											<td><?php
												echo form_input(array('name' => 'city[]',
													'value' => @$customer['city'],
													'id' => 'billing_city',
													'placeholder' => 'Enter City',
													'class' => 'text large'));
												?></td>
										</tr>
										<tr>
											<td id="billing_state_label"><b>State:*</b></td>
											<td><?php echo form_dropdown('state[]', $states, @$customer['state'], 'id="billing_state"'); ?></td>
										</tr>
										<tr>
											<td id="billing_zip_label"><b>Zip:*</b></td>
											<td><?php
												echo form_input(array('name' => 'zip[]',
													'value' => @$customer['zip'],
													'id' => 'billing_zip',
													'class' => 'text large',
													'placeholder' => 'Zipcode'));
												?></td>
										</tr>
										<tr>
											<td><b>Country:*</b></td>
											<td>
												<?php
												echo form_dropdown('country[]', $countries, @$customer['country'], 'id="billing_country" onChange="newChangeCountry(\'billing\');"');
												?>
											</td>
										</tr>
									</table>
								</div>
							</td>
						</tr>
					<?php endif; ?>
					<tr>
						<td>
							<div class="notes-section">
								<label>Notes</label>
								<div class="text-wrapper">
									<textarea id="notes"><?php echo $customer['notes'];?></textarea>
									<a class="button save">Save</a>
								</div>
								<div id="notes-wrapper" class="notes-wrapper">
								</div>
							</div>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
					</tr>
					<tr>
						<td class="clndr">
							<?php echo $calendar;?>
						</td>
					</tr>
					<tr>
						<td class="">
							<div class="open_activities activities">
								<div style="margin-top:8px">
									<h3 style="float:left">Open Activities</h3>
									<a class="new_task pointer" style="float:right;">+ New Task</a>
								</div>
								<table width="100%" cellpadding="10" id="open_activities_table_v">
									<thead>
										<tr>
											<th>Subject</th>
											<th>From</th>
											<th>To</th>
											<th>Activity Owner</th>
											<th>Modified Time</th>
										</tr>
									</thead>
									<tbody>
									<tbody>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
					<tr>
						<td class="">
							<div class="closed_activities activities">
								<h3 style="margin-top:8px">Closed Activities</h3>
								<table width="100%" cellpadding="10" id="closed_activities_table_v">
									<thead>
										<tr>
											<th>Subject</th>
											<th>From</th>
											<th>To</th>
											<th>Closed By</th>
											<th>Closed Date</th>
										</tr>
									</thead>
									<tbody>
									<tbody>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</table>
				</form>

				<?php if (ENABLE_CUSTOMER_PRICING) {
					$CI =& get_instance();
					?>
					<div style="width: 45%; float: left;margin-bottom:20px;">
					<?php echo $CI->load->view("admin/customer/backbone_customerpricing_widget", array("user_id" => $user_id), true); ?>
					</div>
					<?php
				} ?>
				<div style="width:10px;"></div>
				<div class="dsply-dtl">
					<p>Displaying <strong> <?php echo count($customer['orders']);?> </strong> - <strong> <?php echo count($customer['orders']);?> </strong> of <strong> <?php count($customer['orders']);?> </strong> Order(s)</p>
				</div>
				<table width="45%" cellpadding="10" style="float:left; border:1px solid silver; border-collapse:separate;">
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
<div class="cstm-pup"></div>
<script type="text/template" id="NotesView">
<div class="notes-holder"></div>

<div class="notes-section-footer">
	<a class="show-less">Show Less</a>
	<a class="show-more">View Previous Notes</a>
	<span class="total_count"></span>
</div>
</script>
<script type="text/template" id="NoteView">
	<div class="portrait-wrapper">
		<img src="<?php echo $assets; ?>/images/icon_user.png">
	</div>
	<div class="note-detail">
		<span class=""><%= obj.note %></span>
		<div class="note-info">
			<span>&#8226;&nbsp;Add Note&nbsp;&#8226;</span>
			<i class='fa fa-clock-o'></i>
			<span class="date"><%= obj.created_at_ago %></span>
			<span class="author">by <%= obj.author_first_name %> <%= obj.author_last_name %></span>
		</div>
	</div>
</script>
<script>

window.NoteModel = Backbone.Model.extend({
	defaults : {
		"id" : 0,
		"user_id": 0,
		"note" : "",
		"created_at" : "",
		"created_at_timestamp": "",
		"created_by" : "",
		"author_first_name":"",
		"author_last_name":"",
		"created_at_ago" : "",
	},
	initialize: function() {
		this.set({"created_at_ago": moment.unix(this.get("created_at_timestamp")).fromNow()});
	}
});

window.NoteCollection = Backbone.Collection.extend({
	model: NoteModel,
	comparator: function(x) {
		return -parseInt(x.get("id"), 10);
	}
});

window.NoteView = Backbone.View.extend({
	className: "note-view",
	template: _.template($("#NoteView").html()),
	events: {

	},
	initialize: function(options) {
		this.options = options || {};
		_.bindAll(this, "render", "subrender");
	},
	"subrender" : function() {
	},
	"render" : function() {
		$(this.el).html(this.template(this.model.toJSON()));
		this.subrender();
		return this;
	}
});

window.NotesView = Backbone.View.extend({
	className: "notes-view",
	template: _.template($("#NotesView").html()),
	events: {
		"click .show-less": "showLess",
		"click .show-more": "showMore",
	},
	initialize: function(options) {
		this.options = options || {};
		_.bindAll(this, "render", "reload","showLess","showMore","addNote");
	}, 
	showLess: function() {
		$(this.el).removeClass('expanded');
		this.expanded = false;
		this.refreshCount();
	},
	showMore: function() {
		$(this.el).removeClass('expanded').addClass('expanded');
		this.expanded = true;
		this.refreshCount();
	},
	addNote: function(noteObj) {
		var that = this;
		that.notes.unshift(noteObj);
		var noteModel = new NoteModel(noteObj);
		that.$(".notes-holder").prepend(new NoteView({
			model: noteModel
		}).render().el);
		that.refreshCount();
	},
	refreshCount: function() {
		var that = this;
		if (that.expanded) {
			that.$(".total_count").html(that.notes.length + '/' + that.notes.length);
		} else {
			that.$(".total_count").html(Math.min(2, that.notes.length) + '/' + that.notes.length);
		}
		if (that.notes.length < 3) {
			$(that.el).removeClass('has-few').addClass('has-few');
		} else {
			$(that.el).removeClass('has-few');
		}
	},
	reload: function() {
		var that = this;
		$.post( this.options.ajax_url, {}, function( result ){
			try {
				res = JSON.parse(result);
				if (res.notes) {
					that.$(".notes-holder").html("");
					that.notes = res.notes;
					var notesCollection = new NoteCollection(res.notes);
					for (var i = 0; i < notesCollection.length; i++) {
						var m = notesCollection.at(i);
						that.$(".notes-holder").append(new NoteView({
							model: m
						}).render().el);
					}
					that.refreshCount();
				}
			} catch(e) {
				console.log(e);
			}
			
			
		});
		// for (var i = 0; i < mySpecGroupCollection.length; i++) {
		// 	this.$(".holder").append(new NoteView({
		// 		model: mySpecGroupCollection.at(i)
		// 	}).render().el);
		// }
	},
	"render" : function() {
		$(this.el).html(this.template({}));
		this.reload();
		this.expanded = false;
		return this;
	}
});

function FormatNumberLength(num, length) {
    var r = "" + num;
    while (r.length < length) {
        r = "0" + r;
    }
    return r;
}

$(document).on('click', '.prev, .next', function() {
	$('#loading-background').show();
	var mnth = $(this).data('month');
	var year = $(this).data('year');
	if( $(this).hasClass('prev') ) {
		mnth = parseInt(mnth)-1;
		if( mnth == 0 ) {
			mnth = 12;
			year = parseInt(year)-1;
		}
	} else if( $(this).hasClass('next') ) {
		mnth = parseInt(mnth)+1;
		if( mnth == 13 ) {
			mnth = 1;
			year = parseInt(year)+1;
		}
	}
	var m = FormatNumberLength(mnth, 2);
	var ajax_url = "<?php echo site_url('admin/getCalendarCustomer/');?>/"+m+'/'+year+"/<?php echo $user_id;?>"+'/true';
	$.post( ajax_url, {}, function( result ){
		$('.clndr').html(result);
		$('#loading-background').hide();
		//alert(result);
	});
	//alert("Pradeep Clicked");
});

$(document).on('click', '.calendar-day', function() {
	if( !$(this).hasClass('childOpened') ) {
		$('#loading-background').show();
		var dt = $(this).data('dt');
		var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>";
		$.post( ajax_url, {'dt':dt, 'user_id': "<?php echo $user_id;?>"}, function( result ){
			$('.cstm-pup').html(result);
			$('#loading-background').hide();
		});
	}
});
$(document).on('click', 'a.new_task', function() {
	if( !$(this).hasClass('childOpened') ) {
		$('#loading-background').show();
		var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>";
		$.post( ajax_url, {'user_id': "<?php echo $user_id;?>"}, function( result ){
			$('.cstm-pup').html(result);
			$('#loading-background').hide();
		});
	}
});
$(document).on('click', 'a.activity', function() {
	if( !$(this).hasClass('childOpened') ) {
		$('#loading-background').show();
		var dt = $(this).attr('data-date');
		var id = $(this).attr('data-id');
		var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>/"+id;
		$.post( ajax_url, {'dt':dt, 'user_id': "<?php echo $user_id;?>"}, function( result ){
			$('.cstm-pup').html(result);
			$('#loading-background').hide();
		});
	}
});
$(document).on('click', '.day-rem', function() {
	var _this = $(this);
	$(this).parent().addClass('childOpened');
	$('#loading-background').show();
	var dt = $(this).data('dt');
	var id = $(this).data('id');
	var ajax_url = "<?php echo site_url('admin/getReminderPopUpCustomer/');?>/"+id;
	$.post( ajax_url, {'dt':dt, 'user_id': "<?php echo $user_id;?>"}, function( result ){
		_this.parent().removeClass('childOpened');
		$('.cstm-pup').html(result);
		$('#loading-background').hide();
	});
});
$(document).on('click', '.clspopup', function() {
	$('.cstm-pup').html('');
});
$(document).on('click', '.svcls', function() {
	$('#reminder_form').submit();
});
$(document).on('click', '.dlt', function() {
	if( confirm("Are you sure you want to delete this event")) {
		var id = $(this).data('id');
		var user = $(this).data('user');
		window.location.href = "<?php echo site_url('admin/deleteReminderPopUpCustomer/');?>/"+id+'/'+user;
	}
});
$(document).on('change', '#sales_owner', function() {
	var ajax_url = "<?php echo site_url('admin/ajax_assign_employee_to_customer/');?>";
	$.post( ajax_url, {'customer':"<?php echo $user_id;?>", 'employee': $(this).val()}, function(){
	});
});

$(document).on('click', '.notes-section a.save', function() {
	var ajax_url = "<?php echo site_url('admin/ajax_save_notes/');?>/"+"/<?php echo $user_id;?>";
	var note = $('#notes').val();
	if (note.trim().length <= 0) {
		return;
	}
	$.post( ajax_url, {'note':note.trim()}, function( result ){
		try {
			var res = JSON.parse(result);
			if (res.result && res.note) {
				if (window.notesView) {
					window.notesView.addNote(res.note);
				}
			}
		} catch(e) {

		}
	});
});

$(window).load(function() {
	$(".open_activities table").dataTable({
		"processing" : true,
		"serverSide" : true,
		"ordering" : false,
		"searching" : false,
		"ajax" : {
			"url" : "<?php echo base_url('admin/ajax_get_open_activities/'.$customer['id']); ?>",
			"type" : "POST",
			"cache" : false
		},
		"data" : [],
		"paging" : true,
		"info" : true,
		"stateSave" : true,
		"fnDrawCallback": function() {
		},
		"columns" : [
			null,
			null,
			null,
			null,
			null,
		],
	});
	$(".closed_activities table").dataTable({
		"processing" : true,
		"serverSide" : true,
		"ordering" : false,
		"searching" : false,
		"ajax" : {
			"url" : "<?php echo base_url('admin/ajax_get_closed_activities/'.$customer['id']); ?>",
			"type" : "POST",
			"cache" : false
		},
		"data" : [],
		"paging" : true,
		"info" : true,
		"stateSave" : true,
		"fnDrawCallback": function() {
		},
		"columns" : [
			null,
			null,
			null,
			null,
			null,
		]
	});


	var notesView = new NotesView({
		ajax_url : "<?php echo site_url('admin/ajax_get_customer_notes/');?>"+"/<?php echo $user_id;?>"
	});
	window.notesView = notesView;
	$("#notes-wrapper").html(notesView.render().el);
});

</script>