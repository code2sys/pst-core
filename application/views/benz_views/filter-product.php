<?php
$new_assets_url = jsite_url("/qatesting/benz_assets/");
$media_url = jsite_url("/media/");


$CI =& get_instance();
$stock_status_mode = $CI->_getStockStatusMode();

if (@$motorcycles) {
    foreach ($motorcycles as $motorcycle) {

        // What is the default...
        $motorcycle_image = $motorcycle['image_name'];
        if ($motorcycle['external'] == 0 ) {
            $motorcycle_image = $media_url . $motorcycle_image;
        }
        if ($motorcycle_image == "" || is_null($motorcycle_image) || $motorcycle_image == $media_url) {
            $motorcycle_image = "/assets/image_unavailable.png";
        }

        ?>
        <div class="mid-r">
            <a href="<?php echo base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']); ?>">
                <div class="mid-r-img">
                    <div class="mid-r-logo">
                            <!--<img src="<?php echo $new_assets_url; ?>images/imgpsh_fullsize (6).png" width="152px;"/>-->
                    </div>
                    <div class="mid-r-img-veh">
                        <img src="<?php echo $motorcycle_image; ?>" width="px;"/>
                    </div>
                </div>
            </a>
            <div class="mid-r-text">
                <div class="mid-text-left">
                    <h3><?php echo $motorcycle['title']; ?></h3>
                    <?php if ($motorcycle['call_on_price'] == '1') { ?>
                        <p class="cfp">Call For Price</p>
                    <?php } else {
                        if ($motorcycle['sale_price']>0 && $motorcycle['sale_price']!=="0.00" && $motorcycle["sale_price"] != $motorcycle["retail_price"]) { ?>
                            <?php if ($motorcycle["retail_price"] > 0): ?>
                        <p>Retail Price: &nbsp; <span class="strikethrough">$<?php echo $motorcycle['retail_price']; ?></span><br>
                            <?php endif; ?>
                            Sale Price: &nbsp;&nbsp;&nbsp;&nbsp;<span class="redtext">$<?php echo $motorcycle['sale_price']; ?></span></p>
                        <?php } else { ?>
                            <p>Retail Price: &nbsp; $<?php echo $motorcycle['retail_price']; ?></p><?php
                        }
                        if ($motorcycle["destination_charge"]) {
                            ?><sub>* Plus Applicable destination charge</sub><?php
                        }
                    }
                ?>
                </div>
                <p class="mid-text-right">
                    <p>condition :<span><?php echo $motorcycle['condition'] == '1' ? 'New' : 'Pre-Owned'; ?></span></p>
                    <?php if ($motorcycle["color"] != "N/A"): ?>
                        <p>color :<span><?php echo $motorcycle['color']; ?></span></p>
                    <?php endif; ?>
                    <?php if ($motorcycle['engine_hours'] > 0) { ?>
                        <p>engine hours :<span><?php echo $motorcycle['engine_hours']; ?></span></p>
                    <?php } ?>
                    <?php if ($motorcycle['sku'] != '') { ?>
                        <p>stock :<span><?php echo $motorcycle['sku']; ?></span></p>
                    <?php } ?>
                    <?php if ($motorcycle['mileage'] > 0) { ?>
                        <p>mileage :<span><?php echo $motorcycle['mileage']; ?></span></p>
                    <?php } ?>
                    <?php if (($motorcycle['stock_status'] == 'In Stock' && $stock_status_mode >= 2 ) || ($stock_status_mode == 1)): ?>
                        <p>availability : <span><?php echo $motorcycle['stock_status'];?></span></p>
                    <?php endif; ?>
                    <?php if ($motorcycle['engine_type'] != '') { ?>
                        <p>Engine Type :<span><?php echo $motorcycle['engine_type']; ?></span></p>
        <?php } ?>
                </div>
            </div>
            <div class="mid-r-but">
                <a href="#"  data-toggle="modal" data-target="#myModal<?php echo $motorcycle['id']; ?>"><img src="<?php echo $new_assets_url; ?>images/message.png" width="20px" height="22px;"/><span class="mid-cen">GET a quote</span></a>
                <a href="#" data-toggle="modal" data-target="#myModal<?php echo $motorcycle['id']; ?>"><img src="<?php echo $new_assets_url; ?>images/outgoing.png" width="20px" height="24px;"/>value your <span>trade</span></a>
                <a href="#"><img src="<?php echo $new_assets_url; ?>images/doll.png" width="10px" height="20px;"/><span class="mid-cen"><?php
                        if (!defined('GET_FINANCING_WORDING')) {
                            define('GET_FINANCING_WORDING', 'GET FINANCING');
                        }
                        echo GET_FINANCING_WORDING;
                        ?></span></a>
                <a href="<?php echo base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']); ?>"><img src="<?php echo $new_assets_url; ?>images/list.png" width="15px" height="20px;"/>VIEW DETAILS</a>
            </div>
        </div>

        <div class="modal fade pop" id="myModal<?php echo $motorcycle['id']; ?>">
            <div class="modal-dialog area">	  
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="clo" data-dismiss="modal">get a quote</div>			 
                    </div>
        <?php echo form_open('welcome/productEnquiry', array('class' => 'form_standard')); ?>
                    <div class="modal-body" id="scol">				
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
                    </div>								
                    </form>					
                </div>	  
            </div>
        </div>
    <?php }
} else { ?>
    <div class="mid-r">
        No Product's Found in this search criteria!
    </div>
<?php } ?>
<div class="mypagination">
    <ul>
        <?php if ($pages > 1): ?>
            <?php if ($page > 1): ?>
                <li class=" pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page - 1; ?>">← Previous</a></li>

                <?php if ($page > 2): ?>

                    <?php if ($page > 3): ?>
                        <li class="pager_spacer"><span>&hellip;</span></li>
                    <?php endif; ?>

                    <li class="pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page - 2; ?>"><?php echo $page - 2; ?></a></li>

                <?php endif; ?>

                <li class="pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page - 1; ?>"><?php echo $page - 1; ?></a></li>
            <?php endif; ?>

            <li class="active pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page; ?>"><?php echo $page; ?></a></li>


            <?php if ($page < $pages): ?>
                <li class="pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page + 1; ?>"><?php echo $page + 1; ?></a></li>

                <?php if ($page < $pages - 1): ?>
                    <li class="pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page + 2; ?>"><?php echo $page + 2; ?></a></li>

                    <?php if ($page < $pages - 2): ?>
                        <li class="pager_spacer"><span>&hellip;</span></li>
                    <?php endif; ?>


                <?php endif; ?>

                <li class=" pgn"><a href="javascript:void(0);" data-page-number="<?php echo $page + 1; ?>">Next →</a></li>
            <?php endif; ?>


        <?php endif; ?>
    </ul>
</div>