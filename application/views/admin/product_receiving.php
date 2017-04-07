<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">
        <div class="clear"></div>

        <!-- VALIDATION ALERT -->
        <?php if (validation_errors() || @$errors): ?>
            <div class="validation_error" id="login_validation_error">
                <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
                <h1>Error</h1>
                <div class="clear"></div>
                <p>
                    <?php
                    echo validation_errors();
                    if (@$errors): foreach ($errors as $error): echo $error;
                        endforeach;
                    endif;
                    ?>
                </p>
            </div>
        <?php endif; ?>
        <!-- END VALIDATION ALERT -->

        <!-- VALIDATION ALERT -->
        <?php if (@$found): ?>
            <div class="validation_error" id="login_validation_error">
                <img src="<?php echo $assets; ?>/images/success.png" style="float:left;margin-right:10px;">
                <h1>Success</h1>
                <div class="clear"></div>
                <p>
				These 
                    <?php
                    foreach ($found as $msg):
						echo $msg['partnumber'].', ';
                    endforeach;
                    ?>
				Part numbers are updated successfully!
                </p>
            </div>
        <?php endif; ?>
        <!-- END VALIDATION ALERT -->
		
        <div class="clear"></div>
        <form method="post" action="" onsubmit="return checkProductReceiving();">
        <table width="60%" cellpadding="6">
            <tr>
                <td colspan="2">Please Select Distributor : 
                    <select name="distributor_id" required>
                        <option value=""> Select Distributor</option>
                        <?php foreach ($distributors as $k => $v) { ?>
                            <option value="<?php echo $v['distributor_id']; ?>"><?php echo $v['name']; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table width="100%" class="add-mr" cellpadding="6">
                        <thead>
                            <tr>
                                <th>Part Number</th>
                                <th>Quantity</th>
                                <th>Product Cost</th>
                            </tr>
                        </thead>
                        <tbody class="rws">
							<?php foreach( $notfound as $key => $val ) { ?>
								<tr>
									<td><input type="text" name="partnumber[<?php echo $key; ?>]" value="<?php echo $val['partnumber'];?>" class="text large pnmbr" data-id="<?php echo $key;?>"></td>
									<td><input type="number" name="quantity[<?php echo $key; ?>]" value="<?php echo $val['quantity'];?>" class="text large pqty" id="<?php echo $key;?>"></td>
									<td><input type="text" name="cost[<?php echo $key; ?>]" value="<?php echo $val['cost'];?>" class="text large"></td>
								</tr>
							<?php } ?>
                            <?php for ($i = 0; $i < 4; $i++) { ?>
                                <tr>
                                    <td><input type="text" name="partnumber[<?php echo $i; ?>]" value="" class="text large pnmbr" data-id="<?php echo $i;?>"></td>
                                    <td><input type="number" name="quantity[<?php echo $i; ?>]" value="" class="text large pqty" id="<?php echo $i;?>"></td>
                                    <td><input type="text" name="cost[<?php echo $i; ?>]" value="" class="text large"></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td class="add-rw">Add More Rows</td>
                                <td><input type="submit" name="save" value="Submit"></td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>
        </table>
        </form>
        <div class="clear"></div>

    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<script>
    $(document).on('click', '.add-rw', function () {
        var str = "";
        var lng = $('.rws tr').length;
        for (var i = lng;i<lng+4;i++) {
            str += "<tr>";
            str += "<td><input type='text' name='partnumber["+i+"]' value='' class='text large pnmbr' data-id='"+i+"'></td>";
            str += "<td><input type='number' name='quantity["+i+"]' value='' class='text large pqty' id='"+i+"'></td>";
            str += "<td><input type='text' name='cost["+i+"]' value='' class='text large'></td>";
            str += "</tr>";
        }
        $('.rws').append(str);
    });
	
	function checkProductReceiving() {
		var err = false;
		var id = null;
		$('.pnmbr').each(function() {
			id = $(this).data('id');
			if( (parseInt($('#'+id, $(this).parent().parent()).val()) == 0 || isNaN(parseInt($('#'+id, $(this).parent().parent()).val())) ) && $(this).val() != "" ) {
				$('#'+id, $(this).parent().parent()).css('border-color', "red");
				err = true;
			} else {
				$('#'+id, $(this).parent().parent()).css('border-color', "#AAA");
			}
		});
		if( err ) {
			return false;
		} else {
			return true;
		}
	}
</script>
<style>
    td { border: none; }
</style>