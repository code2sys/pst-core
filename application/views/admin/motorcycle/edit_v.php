<?php
$cstdata = (array) json_decode($product['data']);
?>
<!-- MAIN CONTENT =======================================================================================-->
<div class="content_wrap">
    <div class="content">

        <?php
        $CI =& get_instance();
        echo $CI->load->view("admin/motorcycle/moto_head", array(
            "new" => @$new,
            "product" => @$product,
            "success" => @$success,
            "assets" => $assets,
            "id" => @$id,
            "active" => "edit",
            "descriptor" => "General Options",
            "source" => @$product["source"],
            "stock_status" => @$product["stock_status"]
        ), true);

        //$suppress = $id > 0 && $product["crs_trim_id"] > 0;
        $suppress = FALSE;

        ?>

        <!-- END TABS -->
        <?php echo form_open('admin/update_motorcycle/' . $id, array('class' => 'form_standard')); ?>
        <?php
        if ($id == 0): ?>
            <input type="hidden" name="crs_trim_id" value="<?php if (array_key_exists("crs_trim_id", $_REQUEST)) { echo htmlentities($_REQUEST["crs_trim_id"]); } ?>" />
        <?php endif; ?>
        <input type="hidden" name="is_match_color" value="<?php echo $is_match_color?>">
        <!-- TAB CONTENT -->
        <div class="tab_content">
            <div class="hidden_table">
                <table width="100%" cellpadding="6">
                    <?php if ($id > 0): ?>
                    <tr>
                        <td style="width:50px;"><b>Vin Number:</b></td>
                        <td>
                            <input type="text" name="vin_number" value="<?php echo $product['vin_number']==''?$_POST['vin_number']:$product['vin_number']; ?>" class="text small">
                        </td>
                    </tr>
                    <?php else: ?>
                        <tr>
                            <td style="width:50px;"><b>Vin Number:</b></td>
                            <td>
                                <input type="text" name="vin_number" value="<?php echo $product['vin_number']==''?$_POST['vin_number']:$product['vin_number']; ?>" class="text small" id="vin_number_input"> <button type="button" id="query_vin">Search VIN</button> <span id="query_vin_failed" style="display: none; background: #fee; font-style: italic; font-weight: bold;">Sorry, no match by VIN.</span>
                            </td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="width:50px;"><b>Vehicle:*</b></td>
                        <td>
                            <?php if ($suppress): ?>
                                <?php foreach( $vehicles as $v ) { ?>
                                    <?php if($product['vehicle_type'] == $v['id']) { echo htmlentities($v["name"]); }; ?></option>
                                <?php } ?>
                            <?php else: ?>
                            <select name="vehicle_type" class="small-hndr" style="border-radius:0;">
                                <option value="">Select Vehicle</option>
                                <?php foreach( $vehicles as $v ) { ?>
                                    <option value="<?php echo $v['id'];?>" <?php if($product['vehicle_type'] == $v['id']) { echo "selected"; }else if($_POST['vehicle_type']==$v['id']){echo "selected";} ?>><?php echo $v['name'];?></option>
                                <?php } ?>
                            </select>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td><b>Year:*</b></td>
                        <td>
                            <?php if ($suppress): ?>
                                <?php echo htmlentities($product['year']); ?>
                            <?php else: ?>
                            <input type="number" min="1900" name="year" value="<?php echo $product['year']==''?$_POST['year']:$product['year']; ?>" class="text "> <span style="color: red; font-style: italic; display: none" id="year-error">Please use a four-digit year.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td ><b>Make:*</b></td>
                        <td>
                            <?php if ($suppress): ?>
                                <?php echo htmlentities($product['make']); ?>
                            <?php else: ?>
                            <input type="text" name="make" value="<?php echo $product['make']==''?$_POST['make']:$product['make']; ?>" class="text " style="width: 300px"> <span class="make_suggestion" style="display:none; font-style: italic">Please begin typing a make to see auto-complete suggestions.</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td ><b>Model:*</b></td>
                        <td >
                            <?php if ($suppress): ?>
                                <?php echo htmlentities($product['model']); ?>
                            <?php else: ?>
                            <input type="text" name="model" value="<?php echo $product['model']==''?$_POST['model']:$product['model']; ?>" class="text " style="width: 300px"> <span class="model_suggestion" style="display:none; font-style: italic">Please begin typing a model/trim to see auto-complete suggestions.</span>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php if ($id > 0): ?>
                    <tr>
                        <td style="width:50px;"><b>Title:</b></td>
                        <td>
                            <input id="name" name="title" placeholder="Enter Title" class="text large ttl" value="<?php echo $product['title']==''?$_POST['title']:$product['title']; ?>"  />
                        </td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="width:50px;"><b>Active:</b></td>
                        <td>
                            <?php echo form_checkbox('status', 1, $product['status']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Stock Status:</b></td>
                        <td>
                            <?php echo form_dropdown('stock_status', array("In Stock" => "In Stock", "Out Of Stock" => "Out Of Stock"), array($product["stock_status"])); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Location:</b></td>
                        <td>
                            <input type="text" name="location_description" value="<?php echo $product['location_description']==''?$_POST['location_description']:$product['location_description']; ?>" class="text small"><br/>
                            <em>If no location description is provided, the city and state of the store profile address will be used.</em>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Feature:</b></td>
                        <td>
                            <?php echo form_checkbox('featured', 1, $product['featured']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Cycle trader:</b></td>
                        <td>
                            <?php echo form_checkbox('cycletrader_feed_status', 1, $product['cycletrader_feed_status']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Category:*</b></td>
                        <td>
							<input type="text" name="category" value="<?php echo $product['name']==''?$_POST['category']:$product['name']; ?>" class="text small"> <?php if ($id == 0): ?><span class="categorymessage">Please begin typing a category to see auto-complete suggestions.</span><?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td style="width:50px;"><b>Condition:</b></td>
                        <td>
							<select name="condition" class="small-hndr" style="border-radius:0;">
								<option value="1" <?php if($product['condition'] == '1') { echo "selected"; } ?>>New</option>
								<option value="2" <?php if($product['condition'] == '2') { echo "selected"; } ?>>Pre-Owned</option>
							</select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>SKU:*</b></td>
                        <td>
                            <input type="text" name="sku" value="<?php echo $product['sku']==''?$_POST['sku']:$product['sku']; ?>" class="text small small-hndr">
                        </td>
                    </tr>

                    <tr>
						<td colspan="2">
							<table width="100%" class="inr">
								<tr>
									<td class="min-wdh"><b>Mileage:</b></td>
									<td class="inr-td scnd wdt">
										<input type="number" name="mileage" value="<?php echo $product['mileage']==''?$_POST['mileage']:$product['mileage']; ?>" class="text small small-hndr frst mlg">
									</td>
									<td style="width:90px;" class="min-wdh"><b>Engine Hours:</b></td>
									<td class="inr-td scnd">
										<input type="number" name="engine_hours" value="<?php echo $product['engine_hours']==''?$_POST['engine_hours']:$product['engine_hours']; ?>" class="text small small-hndr eh">
									</td>
								</tr>
							</table>
						</td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table width="100%" class="inr">
								<tr>
                                <?php if ($is_match_color == false): ?>
                                    <td class="min-wdh"><b>Color:</b></td>
									<td class="inr-td scnd wdt">
                                        <input type="text" id="unit_color" name="color_code" value="<?php echo empty($product['color_code']) ? $_POST['color_code']:$product['color_code']; ?>" class="text small small-hndr">
                                        <span class="color_suggestion" style="display:none; font-style: italic">Please begin typing a color to see auto-complete suggestions.</span>
                                    </td>
                                <?php else: ?>
                                    <td class="min-wdh"><b>Color:</b></td>
									<td class="inr-td scnd wdt">
                                        <input type="text" autocomplete="off" name="color" value="<?php echo empty($product['color']) ? $_POST['color']:$product['color']; ?>" class="text small small-hndr">
                                    </td>
                                    <td class="min-wdh"><b>Match Color:</b></td>
                                    <td class="inr-td scnd">
                                        <select id="color_code" name="color_code" class="small-hndr" style="border-radius:0;">
                                            <option value="">Please Select Option</option>
                                            <?php foreach( $colors as $color ) { ?>
                                                <option value="<?php echo $color['code'];?>" <?php if($product['color_code'] == $color['code']) { echo "selected"; }else if($_POST['color_code']==$color['code']){echo "selected";} ?>><?php echo $color['code'];?>(<?php echo $color['label'];?>)</option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                <?php endif ?>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Engine Type:</b></td>
                        <td>
                            <input type="text" name="engine_type" value="<?php echo $product['engine_type']==''?$_POST['engine_type']:$product['engine_type']; ?>" class="text small">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Transmission:</b></td>
                        <td>
                            <input type="text" name="transmission" value="<?php echo $product['transmission']==''?$_POST['transmission']:$product['transmission']; ?>" class="text small">
                        </td>
                    </tr>
                    <tr>
						<td colspan="2">
							<table width="100%" class="inr">
								<tr>
									<td class="min-wdh"><b>Retail Price:</b></td>
									<td class="inr-td scnd" style="width:120px;">
										<input type="number" step=".01" name="retail_price" value="<?php echo $product['retail_price']==''?$_POST['retail_price']:$product['retail_price']; ?>" class="text small small-hndr frst rt-prc">
									</td>
									<td style="width:75px;" class="min-wdh"><b>Sale Price:</b></td>
									<td class="inr-td scnd">
										<input type="number" step=".01" name="sale_price" value="<?php echo $product['sale_price']=='' || $product['sale_price']=="0.00"?$_POST['sale_price']:$product['sale_price']; ?>" class="text small small-hndr sl-prc">
									</td>
                                    <td class="inr-td scnd">
                                        <input type="checkbox" name="call_on_price" value="1" class="text small small-hndr sl-prc" id="call_on_price" <?php echo $product['call_on_price'] == '1' ? 'checked' : '';?>>
                                        <label for="call_on_price">Call On Price</label>
                                    </td>
                                    <td class="inr-td scnd">
                                        <input type="checkbox" name="destination_charge" value="1" class="text small small-hndr sl-prc" id="destination_charge" <?php echo $product['destination_charge'] == '1' ? 'checked' : '';?>>
                                        <label for="destination_charge">Destination Charge</label>
                                    </td>
								</tr>
							</table>
						</td>
                    </tr>
                    <tr>
						<td colspan="2">
							<table width="100%" class="inr">
								<tr>
									<td class="min-wdh"><b>Total Cost:</b></td>
									<td class="inr-td scnd" style="width:120px;">
										<input type="number" min="0" name="total_cost" value="<?php echo $cstdata['total_cost']==''?$_POST['total_cost']:$cstdata['total_cost']; ?>" class="text small small-hndr frst bg sm-ttl ttl-cst" readonly>
									</td>
									<td style="width:75px;" class="min-wdh"><b>Unit Cost:</b></td>
									<td class="inr-td scnd auto">
										<input type="number" step=".01" min="0" name="unit_cost" value="<?php echo $cstdata['unit_cost']==''?$_POST['unit_cost']:$cstdata['unit_cost']; ?>" class="text small small-hndr sm">
									</td>
									<td style="width:50px;"><b>Parts:</b></td>
									<td class="inr-td scnd auto">
										<input type="number" min="0" name="parts" value="<?php echo $cstdata['parts']==''?$_POST['parts']:$cstdata['parts']; ?>" class="text small small-hndr sm">
									</td>
									<td style="width:50px;"><b>Service:</b></td>
									<td class="inr-td scnd auto">
										<input type="number" min="0" name="service" value="<?php echo $cstdata['service']==''?$_POST['service']:$cstdata['service']; ?>" class="text small small-hndr sm">
									</td>
									<td style="width:90px;" class="min-wdh"><b>Auction Fee:</b></td>
									<td class="inr-td scnd auto">
										<input type="number" min="0" name="auction_fee" value="<?php echo $cstdata['auction_fee']==''?$_POST['auction_fee']:$cstdata['auction_fee']; ?>" class="text small small-hndr sm">
									</td>
									<td style="width:40px;"><b>Misc:</b></td>
									<td class="inr-td scnd auto">
										<input type="number" min="0" name="misc" value="<?php echo $cstdata['misc']==''?$_POST['misc']:$cstdata['misc']; ?>" class="text small small-hndr sm">
									</td>
								</tr>
							</table>
						</td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Margin:</b></td>
                        <td>
                            <input type="number" min="0" name="margin" value="<?php echo $product['margin']==''?$_POST['margin']:$product['margin']; ?>" class="text small small-hndr bg mrgn" readonly>%
                        </td>
                    </tr>
                    <tr>
                        <td style="width:50px;"><b>Profit:</b></td>
                        <td>
                            <input type="number" min="0" name="profit" value="<?php echo $product['profit']==''?$_POST['profit']:$product['profit']; ?>" class="text small small-hndr bg prft" readonly>
                        </td>
                    </tr>
                </table>

            </div>
        </div>
        <!-- END TAB CONTENT -->
        <br>

        <!-- SUBMIT PRODUCT -->
        <button type="submit" id="button"><i class="fa fa-upload"></i>&nbsp;Save</button>

        <!-- SUBMIT DISABLED 
        <p id="button_no"><i class="fa fa-upload"></i>&nbsp;Submit Product</p>
        
        <a href="" id="button"><i class="fa fa-times"></i>&nbsp;Cancel</a>-->


        </form>



    </div>
</div>
<!-- END MAIN CONTENT ==================================================================================-->
<div class="clearfooter"></div>


</div>
<!-- END WRAPPER =========================================================================================-->
<style>
.small-hndr {width:100px !important;}
.frst {margin-left: 55px !important;}
.inr-td {width:200px;}
</style>
<script type="text/javascript">
    var vehicleTypes = <?php echo json_encode($vehicles); ?>;
    var autoVehicleType = 0;
    var autoMake = "";
    var autoModel = "";
    var autoYear = "";
    <?php if ($id == 0): ?>
    var suppress_vin_decoder = false;

    var queryVinFunction = function(e) {
        if (e) {
            e.stopPropagation();
            e.preventDefault();
        }
        var vin = $("input[name=vin_number]").val().trim();

        // hack
        suppress_vin_decoder = false;

        if (!suppress_vin_decoder && (vin !== "")) {
            // OK, get 'em
            $.ajax({
                "type" : "POST",
                "dataType" : "json",
                "url" : "<?php echo site_url("admin/ajax_motorcycle_vin_decoder"); ?>",
                "data" : {
                    vin: vin
                },
                "success" : function(data) {
                    var err = false;
                    if (data.success) {
                        var returnedTrims = data.data;

                        // we should set all of these...
                        if (returnedTrims.trim_id) {
                            trimData[returnedTrims.display_name] = returnedTrims;

                            for (var i = 0; i < vehicleTypes.length; i++) {
                                if (vehicleTypes[i].crs_type == returnedTrims.machine_type) {
                                    $("select[name='vehicle_type']").val(vehicleTypes[i].id);
                                    autoVehicleType = vehicleTypes[i].id;
                                }
                            }

                            autoYear = returnedTrims.year;
                            $("input[name='year']").val(returnedTrims.year);
                            autoMake = returnedTrims.make;
                            $("input[name='make']").val(returnedTrims.make);
                            autoModel = returnedTrims.display_name;
                            $("input[name='model']").val(returnedTrims.display_name).change();
                        } else {
                            err = true;
                        }
                    } else {
                        err = true;
                    }

                    if (err) {
                        $("#query_vin_failed").show();
                    } else {
                        $("#query_vin_failed").hide();
                    }
                }
            })

        }
    };

    // Also catch this...
    // https://stackoverflow.com/questions/7060750/detect-the-enter-key-in-a-text-input-field
    $("#vin_number_input").on('keyup', function (e) {
        if (e.keyCode == 13) {
            queryVinFunction(e);
        }
    });

    // If you change the VIN, you should ripple down all the effects...
    $("#query_vin").on("click", queryVinFunction);


    <?php else: ?>
    var suppress_vin_decoder = true;
    <?php endif; ?>


    $("select[name='vehicle_type']").on("change", function(e) {
        if ($("select[name='vehicle_type']").val() != autoVehicleType) {
            suppress_vin_decoder = true;
        }
    });

    function getQueryBasis() {
        var year, vehicle_type, make;

        try {
            year = $("input[name=year]").val().trim();
        } catch(err) {
            year = "";
        }

        try {
            vehicle_type = $("select[name=vehicle_type]").val().trim();
        } catch(err) {
            vehicle_type = "";
        }

        try {
            make = $("input[name=make]").val().trim();
        } catch(err) {
            make = "";
        }


        // look, if they got nothing, they got nothing...
        if (vehicle_type === "") {
            return false;
        }

        // does this one have a thing for CRS?
        // TODO: Special case for off road....
        var crs_vehicle_type = "";
        var offroad = null;
        for (var i = 0; i < vehicleTypes.length; i++) {
            if (parseInt(vehicleTypes[i].id, 10) === parseInt(vehicle_type, 10)) {
                crs_vehicle_type = vehicleTypes[i].crs_type;
                if (crs_vehicle_type == "MOT") {
                    offroad = vehicleTypes[i].offroad;
                }
            }
        }

        if (crs_vehicle_type !== "") {
            // OK, start to make your data.
            var data = {
                machine_type: crs_vehicle_type,
                offroad: offroad
            };
            if (year !== "") {
                data.year = year;
            }
            if (make !== "") {
                data.make = make;
            }
            return data;
        } else {
            return false;
        }
    }

    function filterArrayByTerm(source_array, term) {
        var result_array = [];

        var regex = new RegExp(term, "i");
        for (var i = 0; i < source_array.length; i++) {
            if (regex.exec(source_array[i])) {
                result_array.push(source_array[i]);
            }
        }

        return result_array;
    }

    $("input[name=year]").on("change", function(e) {
        var year = $("input[name=year]").val();
        if (year !== autoYear && year !== "") {
            suppress_vin_decoder = true;
        }
        var error = false;
        if (year && year !== "") {
            year = parseInt(year, 10);
            if (year < 1900) {
                error = true;
            }
        }

        if (error) {
            $("#year-error").show();
        } else {
            $("#year-error").hide();
        }

        // If the other one is blank...we should tell them to do something about it..
        if ("" == $("input[name=make]").val()) {
            $(".make_suggestion").show();
        } else {
            $(".make_suggestion").hide();
        }
    });


    var categories = <?php echo json_encode(array_map(function($x) {
        return $x["name"];
    }, $category)); ?>;
    categories.sort();

    $("input[name='category']").autocomplete({
        minLength: 0,
        source: categories
    });

    $("input[name='category']").on("focus", function(e) {
        $("input[name='category']").autocomplete("search", $("input[name='category']").val());
    });

     $("#unit_color").on("focus", function(e) {
        $("#unit_color").autocomplete("search", $("#unit_color").val());
    });

    // unit color autocomplete
    $("#unit_color").autocomplete({
        minLength: 0,
        source: function(request, response) {
            var data;
            var unit_id = <?php if ($id) { echo $id; } else { echo "0"; } ?>;

            data = {
                id: unit_id,
                color : $("#unit_color").val().trim(),
                trim_id: $("input[name='crs_trim_id']").val()
            };

            if (data === false) {
                response([]); // just bail out...
            } else {

                var suggestion_array = [];
                $.ajax({
                    "type" : "POST",
                    "dataType" : "json",
                    "url" : "<?php echo site_url("admin/get_color_autocomplete"); ?>",
                    "data" : data,
                    "success" : function(data) {
                        if (data.success) {
                            var returned_data = data.data;

                            $.each(returned_data, function( index, value ) {
                                suggestion_array.push({
                                    label: value.code+"("+value.label+")",
                                    value: value.code
                                });
                            });

                            suggestion_array.sort();
                        }
                    },
                    "complete" : function() {
                        $(".color_suggestion").hide();
                        response(suggestion_array);
                    }
                })
            }

        }
    });


    $("input[name='make']").autocomplete({
        minLength: 0,
        source: function(request, response) {
            var data = getQueryBasis();

            console.log(["Found data in make query", data]);

            if (data === false) {
                response([]); // just bail out...
            } else {
                if (data.make) {
                    delete(data.make);
                }

                var suggestion_array = [];
                $.ajax({
                    "type" : "POST",
                    "dataType" : "json",
                    "url" : "<?php echo site_url("admin/motorcycle_ajax_ac_make"); ?>",
                    "data" : data,
                    "success" : function(data) {
                        if (data.success) {
                            var returned_data = data.data;
                            console.log(returned_data);
                            for (var i = 0; i < returned_data.length; i++) {
                                suggestion_array.push(returned_data[i].make);
                            }
                            suggestion_array = filterArrayByTerm(suggestion_array, request.term);
                            suggestion_array.sort();
                        }
                    },
                    "complete" : function() {
                        $(".make_suggestion").hide();
                        response(suggestion_array);
                    }
                })
            }

        }
    });

    $("input[name='make']").on("change", function(e) {
        if ($("input[name='make']").val() != autoMake) {
            suppress_vin_decoder = true;
        }
        if ("" == $("input[name=model]").val()) {
            $(".model_suggestion").show();
        } else {
            $(".model_suggestion").hide();
        }
    });

    $("input[name='make']").on("focus", function(e) {
        $("input[name='make']").autocomplete("search", $("input[name='make']").val());
    });

    var trimData = {};

    $("input[name='model']").autocomplete({
        minLength: 0,
        source: function(request, response) {
            var data = getQueryBasis();

            console.log(["Found data in model query", data]);

            if (data === false) {
                response([]); // just bail out...
            } else {
                var suggestion_array = [];
                $.ajax({
                    "type" : "POST",
                    "dataType" : "json",
                    "url" : "<?php echo site_url("admin/motorcycle_ajax_ac_model"); ?>",
                    "data" : data,
                    "success" : function(data) {
                        if (data.success) {
                            var returned_data = data.data;
                            console.log(returned_data);
                            trimData = {};
                            for (var i = 0; i < returned_data.length; i++) {
                                suggestion_array.push(returned_data[i].display_name);
                                trimData[returned_data[i].display_name] = returned_data[i];
                            }
                            suggestion_array = filterArrayByTerm(suggestion_array, request.term);
                            suggestion_array.sort();
                        }
                    },
                    "complete" : function() {
                        $(".model_suggestion").hide();
                        response(suggestion_array);
                    }
                })
            }
        }
    });


    $("input[name='model']").on("focus", function(e) {
        $("input[name='model']").autocomplete("search", $("input[name='model']").val());
    });


    $("input[name='model']").on("change", function(e) {
        if ($("input[name='model']").val() != autoModel) {
            suppress_vin_decoder = true;
        }
        // if it changes, and if it's in our look-up table, we have to auto-populate a few fields...
        var model = $("input[name='model']").val();
        if (trimData[model]) {
            var m = trimData[model];
            $("input[name='engine_type']").val(m.engine_type);
            $("input[name='transmission']").val(m.transmission);
            $("input[name='retail_price']").val(m.msrp);
            $("input[name='destination_charge']").attr("checked", true);
            $("input[name='status']").attr("checked", true);
            $("input[name='crs_trim_id']").val(m.trim_id);
            if (m.default_category) {
                $("input[name='category']").val(m.default_category);
            }
        }
    });

    $("input[name='model']").on( "autocompleteclose", function( event, ui ) {
        $("input[name='model']").change();
    } );


    $("form").on("submit", function(e) {
       var error = false;

       // do they have a sku?
        var required_fields = ["vehicle_type", "make", "model", "year", "sku"];

        // If they are all blank, just refuse to do anything...
        var error_count = 0;
        for (var i = 0; i < required_fields.length; i++) {
            var $m = $("[name='" + required_fields[i]+ "']");
            if ($m.length > 0) {
                if ($m.val() == "") {
                    error = true;
                    error_count++;
                }
            }
        }


       if (error) {
            if (error_count < required_fields.length) {
                // well, they filled in SOMETHING
                alert("Please fill in all required fields.");
            }

           e.preventDefault();
           e.stopPropagation();
       }
       return !error;
    });


    // This is probably all junk for assembling the title - we should just assemble it server-side and permit them to edit it.

	$(document).on('keyup','.sm', function() {
		var ttl = 0;
		$('.sm').each(function() {
			if($(this).val()) {
				ttl = parseInt($(this).val())+ttl;
			}
			//alert();
		});
		$('.sm-ttl').val(ttl);
		
		var cst = parseInt($('.ttl-cst').val());
		var sale = parseInt($('.sl-prc').val());
		var mrgn = parseFloat(((sale - cst)*100)/sale).toFixed(2);
		$('.mrgn').val(mrgn);
		if( cst > 0 && sale > 0 ) {
			$('.prft').val(sale-cst);
		}
	});
	
	$(document).on('keyup','.mlg', function() {
		var vl = $(this).val();
		if( vl != '' ) {
			$('.eh').val('');
			$('.eh').attr('readonly', true);
		} else {
			$('.eh').attr('readonly', false);
		}
	});
	
	$(document).on('keyup','.eh', function() {
		var vl = $(this).val();
		if( vl != '' ) {
			$('.mlg').attr('readonly', true);
			$('.mlg').val('');
		} else {
			$('.mlg').attr('readonly', false);
		}
	});
	
	//ttl-1
	$(document).on('keyup','.ttl-1', function() {
		var ttl = "";
		$('.ttl-1').each(function() {
			if($(this).val()) {
				ttl = ttl + ' ' + $(this).val();
			}
			//alert();
		});
		$('.ttl').val(ttl);
	});
	
	$(document).on('keyup', '.ttl-cst', function() {
		var cst = parseInt($(this).val());
		var sale = parseInt($('.sl-prc').val());
		if( cst > 0 && sale > 0 ) {
			$('.prft').val(sale-cst);
		}
	});
	$(document).on('keyup', '.sl-prc', function() {
		var cst = parseInt($('.ttl-cst').val());
		var sale = parseInt($(this).val());
		var mrgn = parseFloat((cst*100)/sale).toFixed(2);
		$('.mrgn').val(mrgn);
		if( cst > 0 && sale > 0 ) {
			$('.prft').val(sale-cst);
		}
	});

	
    $("#sortable").sortable({
        revert: true,
        stop: function (event, ui) {
            if (!ui.item.data('tag') && !ui.item.data('handle')) {
                ui.item.data('tag', true);
            }
        },
        receive: function (event, ui) {
            $("ul#sortable").find('.dragRemove').css("display", "inline");
        }
    }).droppable({});
    $(".draggable").draggable({
        connectToSortable: '#sortable',
        helper: 'clone',
        revert: 'invalid'
    });

    $("ul, li").disableSelection();

    function removeCategory()
    {
        $(this).remove();
    }

</script>

