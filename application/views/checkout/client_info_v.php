<?php  
setlocale(LC_MONETARY, 'en_US'); 
?>
	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- CART -->
		<div class="main_content_full">
		
		  <div style="float:left;">
			  <h1>Check Out</h1>
        <h3>Please Fill Out The Forms Below</h3>
        <p>All fields are required.</p>
		  </div>

			<div class="clear"></div>
				<!-- VALIDATION ALERT -->
				<?php if(@$message): ?>
				<div class="validation_error">
			    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
			    <h1>Error</h1>
			    <div class="clear"></div>
			    <p><?php echo @$message; ?></p>
			    
				</div>
				<?php endif; ?>
				<!-- END VALIDATION ALERT -->			
				<!-- VALIDATION ALERT -->
				<?php if(@$this->session->flashdata('validation')): ?>
				<div class="validation_error">
			    <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
			    <h1>Error</h1>
			    <div class="clear"></div>
			    <p><?php echo @$this->session->flashdata('validation') ?></p>
				</div>
				 <?php endif; ?>
				<!-- END VALIDATION ALERT -->	
			
			<br>
			
			
			<?php echo form_open($s_baseURL.'checkout/process_client_info', array('class' => 'form_standard', 'id' => 'client_info')); ?>
			<!-- BILLING DETAILS -->
            <?php
            $CI =& get_instance();
            print $CI->load->view("checkout/billing_details", array(
                "billing_addresses" => $billing_addresses,
                "value" => isset($value) ? $value : array(),
                "billing" => isset($billing) ? $billing : array(),
                "states" => isset($states) ? $states : array()
            ), true);

            ?>
			<!-- END BILLING DETAILS -->
			
			<!-- SHIPPING DETAILS -->
            <?php
            print $CI->load->view("checkout/shipping_details", array(
                "shipping_addresses" => $shipping_addresses,
                "value" => isset($value) ? $value : array(),
                "billing" => isset($billing) ? $billing : array(),
                "states" => isset($states) ? $states : array()
            ), true);

            ?>

			
			<div class="clear"></div>
			<!-- END SHIPPING DETAILS -->
			<div class="cart_wrap">
			<div style="float:left; margin:10px"><b>Special Instructions:</b></div> <?php echo form_textarea(array('name' => 'special_instr', 
                          			                                                      'value' => set_value('special_instr'),
                          			                                                      'style' => 'height:50px; width:80%;')); ?>
							<div class="clear"></div>
			</div>
			
			<input type="submit" value="Next >" class="input_button_purple" style="float:right;">
			
			</form>
			
			<div class="clear"></div>
			</br>
		</div>
		<!-- END CHECK OUT -->
		
	</div>
	
	</div>
	<div class="clearfooter"></div>
	<!-- END CONTENT WRAP ===================================================================-->
	
<?php if(!@$this->session->flashdata('values')): ?>
<script>


  updateContact($('#shipping_id').val());
  
  $('#client_info').submit(function(){
    $('input[type=submit]', this).attr('disabled', 'disabled');
});

function updateContact(id)
{
  
  if($.isNumeric(id))
  {
    
    $.post(s_base_url + 'ajax/get_contact_info/' + id,
		{},
		function(returnData)
		{
      var dataArr = jQuery.parseJSON(returnData);
      if($('#shipping_country').val() != dataArr.country)
      {
        $('#shipping_country').val(dataArr.country);
        newChangeCountry('shipping');
        setTimeout(function()
        {
          $('#shipping_first_name').val(dataArr.first_name);
          $('#shipping_last_name').val(dataArr.last_name);
          $('#shipping_street_address').val(dataArr.street_address);
          $('#shipping_address_2').val(dataArr.address_2);
          $('#shipping_city').val(dataArr.city);
          $('#shipping_state').val(dataArr.state);
          $('#shipping_zip').val(dataArr.zip);
          $('#shipping_email').val(dataArr.email);
          $('#shipping_phone').val(dataArr.phone);
          $('#shipping_fax').val(dataArr.fax);
          $('#shipping_company').val(dataArr.company);
        }, 100);
      }
      else
      {
        $('#shipping_first_name').val(dataArr.first_name);
        $('#shipping_last_name').val(dataArr.last_name);
        $('#shipping_street_address').val(dataArr.street_address);
        $('#shipping_address_2').val(dataArr.address_2);
        $('#shipping_city').val(dataArr.city);
        $('#shipping_state').val(dataArr.state);
        $('#shipping_zip').val(dataArr.zip);
        $('#shipping_email').val(dataArr.email);
        $('#shipping_phone').val(dataArr.phone);
        $('#shipping_fax').val(dataArr.fax);
        $('#shipping_company').val(dataArr.company);
      }
      
		});
  }
}

function populateShipping()
{
    $('#shipping_id').val('default');
    $('#shipping_first_name').val($('#billing_first_name').val());
    $('#shipping_last_name').val($('#billing_last_name').val());
    $('#shipping_street_address').val($('#billing_street_address').val());
    $('#shipping_address_2').val($('#billing_address_2').val());
    $('#shipping_city').val($('#billing_city').val());
    $('#shipping_state').val($('#billing_state').val());
    $('#shipping_zip').val($('#billing_zip').val());
    $('#shipping_email').val($('#billing_email').val());
    $('#shipping_phone').val($('#billing_phone').val());
    $('#shipping_fax').val($('#billing_fax').val());
    $('#shipping_company').val($('#billing_company').val());
}

function newChangeCountry(addressType)
{
  country = $('#'+addressType+'_country').val();
  currentValue = $('#'+addressType+'_state').val();
  $('#'+addressType+'_state').empty();
	if(country == 'USA')
	{
	  addressDD = $.post(s_base_url + 'checkout/load_states/1',
		{},
		function(returnData)
		{
		  var dataArr = jQuery.parseJSON(returnData);
      var html = '';
      $.each(dataArr, function(i, value) 
      {
        if(currentValue == i)
          html += '<option selected="selected" value="' + i + '">' + value + '</option>';
        else
        html += '<option value="' + i + '">' + value + '</option>';
      })
      $('#'+addressType+'_state').append(html);

		});
	}
	
	if(country == 'Canada')
	{
		addressDD = $.post(s_base_url + 'checkout/load_provinces/1',
		{},
		function(returnData)
		{
      var dataArr = jQuery.parseJSON(returnData);
      var html = '';
      $.each(dataArr, function(i, value) 
      {
        html += '<option value="' + i + '">' + value + '</option>';
      })
      $('#'+addressType+'_state').append(html);

		});
	}
  
  $.post(s_base_url + 'checkout/new_change_country',
	{
	  'country' : country
	},
	function(returnData)
	{
    var dataArr = jQuery.parseJSON(returnData);
    $('#'+addressType+'_street_address_label').html(dataArr.street_address);
    $('#'+addressType+'_address_2_label').html(dataArr.address_2);
    $('#'+addressType+'_city_label').html(dataArr.city);
    $('#'+addressType+'_state_label').html(dataArr.state);
    $('#'+addressType+'_zip_label').html(dataArr.zip);

	});

}

function changeBillingAddress()
{
	contactId = $('#billing_address_selector').val();
	 $.post(s_base_url + 'ajax/get_contact_info/' + contactId,
		{},
		function(returnData)
		{
			var dataArr = jQuery.parseJSON(returnData);
		      if($('#billing_country').val() != dataArr.country)
		      {
		        $('#billing_country').val(dataArr.country);
		        newChangeCountry('billing');
		        setTimeout(function()
		        {
		          $('#billing_first_name').val(dataArr.first_name);
		          $('#billing_last_name').val(dataArr.last_name);
		          $('#billing_street_address').val(dataArr.street_address);
		          $('#billing_address_2').val(dataArr.address_2);
		          $('#billing_city').val(dataArr.city);
		          $('#billing_state').val(dataArr.state);
		          $('#billing_zip').val(dataArr.zip);
		          $('#billing_email').val(dataArr.email);
		          $('#billing_phone').val(dataArr.phone);
		          $('#billing_fax').val(dataArr.fax);
		          $('#billing_company').val(dataArr.company);
		        }, 100);
		      }
		      else
		      {
		        $('#billing_first_name').val(dataArr.first_name);
		        $('#billing_last_name').val(dataArr.last_name);
		        $('#billing_street_address').val(dataArr.street_address);
		        $('#billing_address_2').val(dataArr.address_2);
		        $('#billing_city').val(dataArr.city);
		        $('#billing_state').val(dataArr.state);
		        $('#billing_zip').val(dataArr.zip);
		        $('#billing_email').val(dataArr.email);
		        $('#billing_phone').val(dataArr.phone);
		        $('#billing_fax').val(dataArr.fax);
		        $('#billing_company').val(dataArr.company);
		      }
      
		});

	
}

function changeShippingAddress()
{
	contactId = $('#shipping_address_selector').val();
	 $.post(s_base_url + 'ajax/get_contact_info/' + contactId,
		{},
		function(returnData)
		{
			var dataArr = jQuery.parseJSON(returnData);
		      if($('#shipping_country').val() != dataArr.country)
		      {
		        $('#shipping_country').val(dataArr.country);
		        newChangeCountry('shipping');
		        setTimeout(function()
		        {
		          $('#shipping_first_name').val(dataArr.first_name);
		          $('#shipping_last_name').val(dataArr.last_name);
		          $('#shipping_street_address').val(dataArr.street_address);
		          $('#shipping_address_2').val(dataArr.address_2);
		          $('#shipping_city').val(dataArr.city);
		          $('#shipping_state').val(dataArr.state);
		          $('#shipping_zip').val(dataArr.zip);
		          $('#shipping_email').val(dataArr.email);
		          $('#shipping_phone').val(dataArr.phone);
		          $('#shipping_fax').val(dataArr.fax);
		          $('#shipping_company').val(dataArr.company);
		        }, 100);
		      }
		      else
		      {
		        $('#shipping_first_name').val(dataArr.first_name);
		        $('#shipping_last_name').val(dataArr.last_name);
		        $('#shipping_street_address').val(dataArr.street_address);
		        $('#shipping_address_2').val(dataArr.address_2);
		        $('#shipping_city').val(dataArr.city);
		        $('#shipping_state').val(dataArr.state);
		        $('#shipping_zip').val(dataArr.zip);
		        $('#shipping_email').val(dataArr.email);
		        $('#shipping_phone').val(dataArr.phone);
		        $('#shipping_fax').val(dataArr.fax);
		        $('#shipping_company').val(dataArr.company);
		      }
      
		});

	
}


</script>
<?php endif; ?>