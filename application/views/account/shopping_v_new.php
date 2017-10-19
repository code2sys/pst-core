<?php
if ((@$validRide) && (@$garageNeeded) && (!empty($_SESSION['activeMachine']))):
    $imgg = 'test_image.jpg';
    switch (@$_SESSION['activeMachine']['make']['machinetype_id']):
        case '13':
            $imgg = 'icon_dirtbike_check.png';
            break;
        default:
            $imgg = 'icon_dirtbike_check.png';
            break;
    endswitch;
endif;
 require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
?>
<div class="container dtlpg" style="margin-top:30px;" id="mdcntnr">
    <div class="breadCrumb">
        <?php
        /*
         * JLB 10-19-17
         * OK, this is the new breadcrumbs.
         * We're just intending to show a category.
         */
        if (count($breadCrumbCategories) > 0) {
            ?>| <?php

            for ($i = 0; $i < count($breadCrumbCategories); $i++) {
                if ($i > 0) {
                    print " &gt; ";
                }

                print "<a href='#'>" .  htmlentities($breadCrumbCategories[$i]["name"]) . "</a>";
            }

        }

        ?>
        <?php
        if (isset($brandMain) && array_key_exists("brand_id", $brandMain) && $brandMain["brand_id"] > 0) {
            echo "|&nbsp;";
            ?>
            <a href="<?php echo base_url() . $brandMain['slug']; ?>" onclick="setNamedSearchBrandt(event, 'brand', '<?php echo $brandMain['brand_id'];?>', '<?php echo $brandMain['name'];?>');"><?php echo $brandMain['name']; ?></a> 
        <?php } ?>
    </div>
    <div class="clear"></div>
    <div class="container">
        <?php if (@$brandMain['image']) { ?>
            <div class="brndimg mrgn-tp-mns">
                <p><img src="<?php echo site_url() . 'media/' .  $brandMain['image']; ?>">
                <h1 class='mn' id="mn-md"><?php echo $product['name']; ?></h1>
                </p>
            </div>
        <?php } else { ?>
            <h1 class="mn"><?php echo $product['name']; ?></h1>
        <?php } ?>
    </div>

    <div class="contentSec">
        <script src="https://apis.google.com/js/platform.js"></script>
        <!-- CONTENT -->
        <div class="clear"></div>
        <div class="prodSec">
            <?php if (@$product['images']): ?>
                <img itemprop="image" src="<?php echo jsite_url("/productimages/"); ?><?php echo $product['images'][0]['path']; ?>" id="base_image" style="  margin: 0 auto; display: table;  max-width: 318px!important;  max-height: 335px!important;">
                <?php /* ?>  DISPLAYING THE CHECK MARK, IF PRODUCT PART MATCHES WITH GARAGE <?php */ ?>
                <?php if (@$imgg): ?><img src="<?php echo $assets; ?>/images/<?php echo $imgg; ?>" height="28" width="30" style=" position: relative; margin-top: -42px; float: right; margin-right: 28px;" ><?php endif; ?>
            <?php else: ?>
                <img src="<?php echo $assets; ?>/images/test_image.jpg" id="base_image" style="  margin: 0 auto; display: table;  max-width: 318px!important;  max-height: 335px!important;">

                <?php /* ?>  DISPLAYING THE CHECK MARK, IF PRODUCT PART MATCHES WITH GARAGE <?php */ ?>
                <?php if (@$imgg): ?><img src="<?php echo $assets; ?>/images/<?php echo $imgg; ?>" height="42" width="42" style=" position: relative; margin-top: -42px; float: right; margin-right: 28px;" ><?php endif; ?>	
            <?php endif; ?>
            <?php if( @$product['stock_code'] && $product['stock_code'] == 'Closeout' ) { ?>
                <img class="clsout" src="/qatesting/newassets/images/clst.png" alt="Closeout" />
            <?php } ?>

            <?php if (@$product['images']): ?>
                <div class="prodGallery">
                    <div id="image_name" style="text-align:center;"><?php echo $product['images'][0]['description']; ?></div>
                    <div class="productListView gallery_inner">
                        <?php if (@$product['images']): foreach ($product['images'] as $key => $image): ?>
                                <div class="hide" id="image_name_<?php echo $key; ?>"><?php echo $product['images'][$key]['description']; ?></div>
                                <?php
                            endforeach;
                        endif;
                        ?>
                        <?php if (@$product['images']): foreach ($product['images'] as $key => $image): ?>
                                <a href="javascript:void(0);" onclick="changeImage('<?php echo $key; ?>');" ><img src="<?php echo jsite_url("/productimages/"); ?><?php echo $image['path']; ?>" id="small_image_<?php echo $key; ?>" style="max-height: 40px; max-width: 40px;"></a>
                                <?php
                            endforeach;
                        endif;
                        ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <div class="prodDetailSec">
            <?php if (validation_errors()): ?>
                <div class="error">
                    <h4><span style="color:#C90;"><i class="fa fa-warning"></i></span><!--&nbsp;Error--></h4>
                    <p><?php echo validation_errors(); ?> </p>
                </div>
            <?php endif; ?>			
            <!-- END ERROR -->

            <!-- ERROR -->
            <div class="error hide">
                <h4><span style="color:#C90;"><i class="fa fa-warning"></i></span><!--&nbsp;Error--></h4>
                <p id="error_message"></p>
            </div>
            <!-- END ERROR -->

            <!-- SUCCESS -->
            <?php if (@$success): ?>
                <div class="success">
                    <h4><span style="color:#090;"><i class="fa fa-check"></i></span>&nbsp;Success</h4>
                    <p><?php echo $success; ?></p>
                </div>
            <?php endif; ?>
            <!-- END SUCCESS -->

            <div class="priceDetaiSect">
                <div id="formCont">
                    <?php echo form_hidden('part_id', $product['part_id']); ?>
                    <?php echo form_hidden('display_name', $product['name']); ?>
                    <?php echo form_hidden('images', $product['images'][0]); ?>

                    <?php if (array_key_exists("call_for_price", $product) && $product["call_for_price"] > 0): ?>
                    <?php
                    if (!isset($store_name)) {
                        $CI =& get_instance();
                        $CI->load->model("admin_m");
                        $store_name = $CI->admin_m->getAdminShippingProfile();
                    }

                    ?>
                    <div class="leftCol" style="width:auto">
                        <span class="prodPrice" id="price" style="<?php if (@$product['price']['sale_max']) { ?> font-size:24px;<?php } ?>">CALL FOR PRICE<br/>
                            <?php echo $store_name['phone'];?></span>
                    </div>
                    <div class="rightCol mrgnbtm45">
                    </div>

                    <div class="clear"></div>

                    <div class="rightCol">

                    </div>
                    <div class="clear"></div>

                    <div class="clear"></div>
                </div>

                <div class="prodPurchaseCont">
                    <div class="leftCol">
                        <div class="socialIconCont">
                            <a class="facebookIcon" href="http://www.facebook.com/share.php?u=<?php echo base_url('shopping/item/' . $product['part_id']); ?>" target="_blank"></a>
                            <a href="https://twitter.com/share" data-lang="en" target="_blank" class="twitterIcon"></a>
                            <a href="mailto:?subject=Check out this Part&amp;body=Check out this site <?php echo base_url('shopping/item/' . $product['part_id']); ?>." title="Share by Email" class="mailIcon"></a>
                        </div>
                    </div>
                </div>


                    <?php else: ?>

                    <div class="leftCol">

                        <span class="prodPrice" id="price" style="<?php if (@$product['price']['sale_max']) { ?> font-size:24px;<?php } ?>">$<?php
                            echo $product['price']['sale_min'];
                            if (@$product['price']['sale_max']): echo ' - $' . $product['price']['sale_max'];
                            endif;
                            ?></span>
                        <?php if (@$product['reviews']): ?>
                            <div class="ratingStars">
                                <?php
                                $remainder = floor(5 - $product['reviews']['average']);
                                for ($i = 0; $i < $product['reviews']['average']; $i++):
                                    ?>
                                    <a href="javascript:;" class="filledStar"></a>
                                    <?php
                                endfor;
                                if ($remainder > 0) {
                                    for ($i = 0; $i < $remainder; $i++):
                                        ?> <a href="javascript:;" class="emptyStar"></a>
                                        <?php
                                    endfor;
                                }
                                ?>
                                <span>(<?php echo $product['reviews']['qty']; ?>)</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="rightCol mrgnbtm45">
                        <?php if ($product['price']['sale_min'] < $product['price']['retail_min']): ?>
                            <div class="oldPrice" style="<?php if (@$product['price']['sale_max']) { ?>font-size:18px;<?php } ?>">$<?php
                                echo $product['price']['retail_min'];
                                if (@$product['price']['sale_max']): echo ' - $' . $product['price']['retail_max'];
                                endif;
                                ?></div>
                        <?php endif; ?>

                        <?php if (@$product['price']['percentage']): ?>
                            <div class="savePrice" style="<?php if (@$product['price']['sale_max']) { ?>font-size:11px;<?php } ?>">You <strong>save</strong>
                                $<?php
                                echo ($product['price']['retail_min'] - $product['price']['sale_min']);
                                if (@$product['price']['sale_max']): echo ' - $' . ($product['price']['retail_max'] - $product['price']['sale_max']);
                                endif;
                                ?> (<?php echo number_format($product['price']['percentage'], 0); ?>%) 
                                <!--$31.99 (10%)--> </div>
                        <?php endif; ?>
                <!--<span class="stockStatus">In Stock</span>-->
                    </div>

                    <div class="clear"></div>

                    <?php
                    $is_qty_displayed = 0;
                    if (@$questions): $currentQuestion = '';
                        foreach ($questions as $key => $quest):   // Building Question Options
                            if ($quest['partquestion_id'] == $currentQuestion): $answers[$quest['partnumber']] = $quest['answer'];
                                ?>
                                <?php
                            // First Time Through
                            elseif ($currentQuestion == ''):
                                ?> 
                                <div class="leftCol">
                                    <div class="colSize"><?php echo $quest['question']; ?> :</div>
                                </div>

                                <?php $answers = array('0' => 'Select an option', $quest['partnumber'] => $quest['answer']); ?>
                                <?php
                            // End old question and create New Question.  New Question will never be the first question.
                            else:
                                ?>

                                <div class="rightCol">
                                    <div class="stock hide" id="out_of_stock_<?php echo $currentQuestion; ?>">
                                        <span class="outOfStockStatus">OUT OF STOCK - PLEASE CALL TO ORDER</span>
                                    </div>
                                    <div class="stock hide" id="in_stock_<?php echo $currentQuestion; ?>">
                                        <span class="stockStatus">In Stock</span>
                                    </div>			
                                    <?php echo form_dropdown('question[]', $answers, @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="", class="slctClr mb10 question ' . $currentQuestion . '", onchange="updatePrice(' . $currentQuestion . ');"'); ?>
                                </div>
                                <div class="clear"></div>
                                <div class="leftCol">
                                    <div class="colSize"><?php echo $quest['question']; ?> :</div>
                                </div>
                                <?php $answers = array('0' => 'Select an option', $quest['partnumber'] => $quest['answer']); ?>
                            <?php
                            endif;
                            $currentQuestion = $quest['partquestion_id'];
                        endforeach;
                        ?>

                        <div class="rightCol">
                            <div class="stock hide" id="out_of_stock_<?php echo $currentQuestion; ?>">
                                <span class="outOfStockStatus">OUT OF STOCK - PLEASE CALL TO ORDER</span>
                            </div>			
                            <div class="stock hide"  id="in_stock_<?php echo $currentQuestion; ?>">
                                <span class="stockStatus">In Stock</span>
                                <div class="clear"></div>
                                <div class="hide fltL mb10" id="low_stock_<?php echo $currentQuestion; ?>" style="display:inline;">
                                    - ONLY
                                    <div id="stock_qty_<?php echo $currentQuestion; ?>" style="display:inline;">1</div>
                                    REMANING
                                </div>
                                <div class="clear"></div>
                            </div>

                            <?php
                            // Last time Through
                            echo form_dropdown('question[]', $answers, @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="", class="slctClr mb10 question ' . $currentQuestion . '", onchange="updatePrice(' . $currentQuestion . ');" ');
                            ?>
                        </div>
                        <div class="clear"></div>

                        <?php
                    else:
                        $is_qty_displayed = 1;
                        ?>
                        <div class="algnmd"> 
                            <div class="leftCol mt10 Qnt">
                                <div class="colSize">QTY :</div>
                            </div>
                            <div class="stock hide stckmd" id="out_of_stock_<?php echo $product['part_id']; ?>">
                                <span class="outOfStockStatus">OUT OF STOCK - PLEASE CALL TO ORDER</span>
                            </div>
                            <div class="stock hide stckstts"  id="in_stock_<?php echo $product['part_id']; ?>">
                                <span class="stockStatus">In Stock</span>
                                <div class="stock hide" id="low_stock_<?php echo $product['part_id']; ?>" style="display:inline;"> - ONLY <div id="stock_qty_<?php echo $product['part_id']; ?>" style="display:inline;">1</div> REMANING</div>
								<input type="hidden" name="partnumber" value="<?php echo $partnumbercustom;?>">
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_qty_displayed == 0) { ?>
                        <div class="leftCol mt10">
                            <div class="colSize">QTY :</div>
                        </div>
                    <?php } ?>
                    <div class="rightCol mt10" style="margin:-19px 59% 0px 0px; float:right; width:26% !important;">
                        <?php
                        echo form_input(array('name' => 'qty',
                            'value' => 1,
                            'maxlength' => 250,
                            'class' => 'text mini qtyInput',
                            'placeholder' => '0',
                            'id' => 'qty'));
                        ?>
                    </div>
                    <div class="clear"></div>
                </div>

                <div class="prodPurchaseCont">
                    <div class="leftCol">
                        <div class="socialIconCont">
                            <a class="facebookIcon" href="http://www.facebook.com/share.php?u=<?php echo base_url('shopping/item/' . $product['part_id']); ?>" target="_blank"></a>
                            <a href="https://twitter.com/share" data-lang="en" target="_blank" class="twitterIcon"></a>
                            <a href="mailto:?subject=Check out this Part&amp;body=Check out this site <?php echo base_url('shopping/item/' . $product['part_id']); ?>." title="Share by Email" class="mailIcon"></a>
                        </div>
                    </div>
                    <div class="rightCol" id="dfrnstng">
                        <a href="javascript:void(0);" onclick="submitCart();" class="button prodBuyBtn prdsnglbtn" id="submit_button" style="text-decoration:none;">BUY</a>
                        <div class="clear" style="margin-top: 20px;"></div>
                        <a href="javascript:void(0);" onclick="submitWishlist();" style="text-decoration:none; color:#78909c;font-weight: bold;">Add to Wishlist</a>
                        <div class="clear"></div>
                    </div>
                </div>

                <?php endif; ?>

            </div>
        </div>
        <div class="clear"></div>

        <div class="descriptionArea">
            <?php if (@$product['description']): ?>
                <a href="javascript:void(0);" onclick="changeTabs('description')" id="description" class="desBtn active mr5">Description</a>
            <?php endif; ?>

            <a href="javascript:void(0);" onclick="changeTabs('reviews')" id="reviews" class="revBtn">Review</a>

            <?php if (@$this->_mainData['garageNeeded']): ?>
                <a href="javascript:void(0);" onclick="changeTabs('fitment')" id="fitment"><i class="fa fa-gears"></i>&nbsp;Fitment</a>
            <?php endif; ?>	
            <?php if (@$sizeChart  || $part_sizechart): ?>
                <a style="padding:10px 10px 8px 10px;" href="javascript:void(0);" onclick="changeTabs('sizechart')" id="sizechart"><img style="vertical-align:middle; margin:0 6px 0 0;" src="<?php echo base_url('assets/images/measuring-tape.png'); ?>">&nbsp;Size Chart</a>
            <?php endif; ?>
        </div>
        <div class="desDetailTxt" id="tab_stuff">
            <?php if ($mainVideo != '') { ?>
                <?php
                $CI =& get_instance();
                echo $CI->load->view("master/embedded_videos", array(
                    "class_name" => "prodct_vdo",
                    "mainVideo" => $mainVideo,
                    "mainTitle" => $mainTitle,
                    "video" => $video,
                    "autoplay" => false
                ), true);
                ?>
            <?php } ?>
            <?php echo @$product['description']; ?>
        </div>
        <style>
            .product_box_text h3 a{
                text-decoration:none;
                color: #1e56a9;
            }
        </style>
        <?php echo str_replace("qatesting/index.php?/", "", str_replace("float:right;", "float:right;color: #393;", @$recentlyViewedBand)); ?>	

    </div>


    <div class="leftBar tpgl">
        <div class="box">

            <div class="<?php if ($detect->isMobile() && !$detect->isTablet()) { echo "none-dlck"; }else { echo "none-disp"; } ?>">
                <div class="side_header tablinks <?php if(@$_SESSION['garage'] ) { echo 'active'; }?>" onclick="openCity(event, 'Garage')">
                    <div class="grg "><?php echo @$_SESSION['userRecord']['first_name']; if(@$_SESSION['userRecord']['first_name']):  ?>'s <?php endif; ?> Garage</div>
                </div>
                <div class="side_header tablinks <?php if(!$_SESSION['garage'] ) { echo 'active'; }?>" onclick="openCity(event, 'shop')">
                    <div class="grg">Shop Machine</div>
                </div>
                <div id="Garage" class="side_section tabcontent first" style="display:<?php if(@$_SESSION['garage'] ) { if ( $detect->isMobile() && !$detect->isTablet() ) { echo 'none'; }else { echo 'block'; } } else { echo 'none'; }?>">
                    <!--tlg-->
                    <?php if(@$_SESSION['garage'] ): foreach($_SESSION['garage'] as $label => $rideRecs):
                    switch(@$rideRecs['make']['machinetype_id']):
                        case '13':
                            $img = 'icon_dirtbike.png';
                            break;
                        default:
                            $img = 'icon_dirtbike.png';
                            break;
                    endswitch;

                    ?>
                    <div class="side_item">

                        <img src="<?php echo $assets; ?>/images/<?php echo $img; ?>" style="float: left;width: 30px;"/>
                        <p>
                            <b><a href="javascript:void(0);" onclick="changeActive('<?php echo $label; ?>')"><?php echo $label; ?></a></b> |
                            <?php if($rideRecs['active']): $_SESSION['activeMachine'] = $rideRecs; ?><div class="garage_active"><p style="color:rgb(52,120,206);font-size:18px; padding-left:3px;"><i class="fa fa-check"></i></p></div><?php endif; ?>
                    <a href="javascript:void(0);" onclick="deleteFromGarage('<?php echo $label; ?>');" style="color:#F00;">
                        <div class="garage_delete"><p style="font-size:18px;padding-left:3px;"><i class="fa fa-times"></i></p></div></a>

                    <div class="clear"></div>
                </div>

                <?php endforeach; else: ?>
                    <div class="side_item">
                        <p><b>Use "Select Machine" above to add a ride to your garage.<br /><br />Parts for the active ride in your garage will be marked for easy reference throughout the site.</b>
                        <div class="clear"></div>
                    </div>
                <?php endif; ?>
            </div>

            <div id="shop" class="side_section tlg tabcontent" style="display:<?php if(!$_SESSION['garage'] ) { if ( $detect->isMobile() && !$detect->isTablet() ) { echo 'none'; }else { echo 'block'; } } else { echo 'none'; }?>">
                <form action="<?php echo base_url('ajax/update_garage'); ?>" method="post" id="update_garage_form" class="form_standard">
                    <div id="toggle">
                        <ul>
                            <li><div class="heading one">SHOP BY MACHINE</div></li>
                            <div class="tlg">
                                <select class="selectField" name="machine" id="machine" tabindex="1">
                                    <option value="">-- Select Machine --</option>
                                    <?php if(@$machines): foreach($machines as $id => $label): ?>
                                        <option value="<?php echo $id; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; endif; ?>-->
                                    <!-- <optgroup label="Motor Cycles"> -->
                                    <!----></select>
                                <select name="make" id="make" tabindex="2" class="selectField">
                                    <option>-Make-</option>
                                </select>
                                <select name="year" id="year" tabindex="3" class="selectField">
                                    <option>-Year-</option>
                                </select>
                                <select name="model" id="model" tabindex="4" class="selectField">
                                    <option>-Model-</option>
                                </select>
                                <div class="btn-ful-wdt"><a href="javascript:void(0);" onClick="updateGarage();" id="add" class="addToCat button_no" style="padding:6px 13px; text-decoration:none; margin:0px;text-shadow:none; font:inherit; font-size:14px; float: left;border-radius: 0px;">Add To Garage</a></div>
                            </div>
                        </ul>
                    </div>
                </form>
                <div class="clear"></div>
            </div>
        </div>
    </div>
</div>
</div>


<script>
	function openCity(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    evt.currentTarget.className += " active";
	if($('#'+cityName).hasClass('actv')) {
		$('#'+cityName).removeClass('actv');
		$('#'+cityName).hide();
	} else {
		$('#'+cityName).addClass('actv');
		$('#'+cityName).show();
	}
	//$("#"+cityName).slideToggle();
}
</script>

<script>

    $(document).ready(function () {

        setTimeout(function () {

            $("#formCont").replaceWith('<form id="productDetailForm" accept-charset="utf-8" method="post" action="<?php echo base_url(); ?>shopping/item/<?php echo $product['part_id']; ?>">' + $("#formCont").html() + "</form>");

        }, 200);

        //$("#loading-background").show();

<?php if ($stock): ?>
            getStock('<?php echo $product['part_id']; ?>');
<?php endif; ?>

        $('.gallery_inner').width('<?php echo @$width; ?>px');


    });

    /*$(window).bind("load", function() {
     $("#loading-background").hide();
     });*/

<?php if (!@$product['description']): ?>
        changeTabs('reviews');
<?php endif; ?>

    function changeImage(key)
    {
        small = $('#small_image_' + key).attr("src");
        $('#base_image').attr("src", small);
        $('#image_name').html($('#image_name_' + key).html());
    }

    function changeTabs(tab)
    {
        if ($('#description').length)
        {
            $('#description').removeClass('active');
        }
        if ($('#fitment').length)
        {
            $('#fitment').removeClass('active');
        }
        if ($('#sizechart').length)
        {
            $('#sizechart').removeClass('active');
        }
        $('#reviews').removeClass('active');
        $('#' + tab).addClass('active');

        $.post(base_url + 'ajax/getActiveSection/',
                {
                    'activeSection': tab,
                    'part_id': '<?php echo $product['part_id']; ?>',
                    'ajax': true
                },
                function (displayblock)
                {
                    $('#tab_stuff').html(displayblock);
                });
    }

    function getStock(partId)
    {
		//alert(partId);
        $.post(base_url + 'ajax/getStockByPartId/',
                {
                    'partId': partId,
                    'ajax': true
                },
                function (partRec)
                {
                    var partObj = jQuery.parseJSON(partRec);
                    //$('#productDetailForm').append('<input type="hidden" name="partnumber" value="'+partObj.partnumber+'" />');
					//alert(partObj.partnumber);
                    $('#in_stock_' + partId).hide();
                    $('#out_of_stock_' + partId).hide();
                    $("#submit_button").attr("onclick", "submitCart()");
                    console.log(partObj.quantity_available);
                    if (partObj.quantity_available > 0)
                    {
                        $('#in_stock_' + partId).show();
                        $('#low_stock_' + partId).hide();
                        if (partObj.quantity_available < 6)
                        {
                            $('#low_stock_' + partId).show();
                            $('#in_stock_' + partId).show();
                            $('#stock_qty_' + partId).html(partObj.quantity_available);
                        }
                    } else
                    {
                        $('#out_of_stock_' + partId).show();
                        $("#submit_button").attr("onclick", "outOfStockWarning()");
                    }
                });
    }

    function outOfStockWarning()
    {
        alert('OUT OF STOCK - PLEASE CALL TO ORDER');
    }

    function updatePrice(questionId)
    {
        $('#price').html(0.00);

        $(".question").each(function ()
        {
            if ($(this).val() != 0)
            {
                $.post(base_url + 'ajax/getPriceByPartNumber/',
                        {
                            'partnumber': $(this).val(),
                            'ajax': true
                        },
                        function (partRec)
                        {
                            var partObj = jQuery.parseJSON(partRec);
                            var currentPrice = $('#price').html();
                            currentPrice = currentPrice.replace("$", "");
                            totalprice = parseFloat(currentPrice) + parseFloat(partObj.sale);
                            $('#price').html('$' + parseFloat(totalprice).toFixed(2));
                            $('#in_stock_' + questionId).hide();
                            $('#out_of_stock_' + questionId).hide();
                            $("#submit_button").attr("onclick", "submitCart()");
                            if (partObj.quantity_available > 0)
                            {

                                $('#in_stock_' + questionId).show();
                                $('#low_stock_' + questionId).hide();
                                if (partObj.quantity_available < 6)
                                {
                                    $('#low_stock_' + questionId).show();
                                    $('#in_stock_' + questionId).show();
                                    $('#stock_qty_' + questionId).html(partObj.quantity_available);
                                }
                            } else
                            {
                                $('#out_of_stock_' + questionId).show();
                                $("#submit_button").attr("onclick", "outOfStockWarning()");
                            }
                        });
            }
        });
    }

    function submitCart()
    {
        var proceed;
        if ($(".question")[0])
        {
            $(".question").each(function ()
            {
                // For Combos, check to see if any of them are out of stock before processing
                questClassList = $(this).attr('class').split(/\s+/);
                console.log(questClassList, questClassList[2]);

                if (!$('#out_of_stock_' + questClassList[3]).is(":hidden"))
                {
                    proceed = 'error';
                    $('.error').show();
                    $('#error_message').text('One of the items you selected is OUT OF STOCK - PLEASE CALL TO ORDER.');
                    return false;
                }
                // Make sure all necessary questions are answered before processing

                if ($(this).val() == 0)
                {
                    proceed = 'error';
                    $('.error').show();
                    $('#error_message').text('Please select a dropdown option for this part.');
                    return false;
                }
            });
        }

        if (proceed != 'error')
        {
<?php
if ($garageNeeded):
    if ($validRide):
        ?>
                    if ($.isNumeric($('#qty').val()) && $('#qty').val() > 0)
                    {
        <?php if (@$_SESSION['userRecord']['admin'] && @$_SESSION['OrderProductSearch']): ?>
                //alert("<?php echo $validMachines[0]['partnumber']; ?>");
                if (typeof $('.question').val() == 'undefined') {
                     window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/<?php echo $validMachines[0]['partnumber']; ?>');
                } else {
                     window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/'+$('.question').val());
                }
                            //window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/<?php echo $validMachines[0]['partnumber']; ?>');
                            //window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/'+$('.question').val());
                            //$('.question').val()
        <?php else: ?>
                            $('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo $validMachines[0]['partnumber']; ?>" />');
                            $('#productDetailForm').append('<input type="hidden" name="price" value="' + $('#price').html() + '" />');
                            $('#productDetailForm').append('<input type="hidden" name="type" value="cart" />');
                            $("#productDetailForm").submit();
        <?php endif; ?>
                    } else
                    {
                        $('.error').show();
                        $('#error_message').text('Please enter a valid quantity.');
                    }

    <?php elseif (@$_SESSION['garage']): // No Active Ride     ?>
                    $('.error').show();
                    $('#error_message').text('<?php $CI =& get_instance(); echo $CI->config->item("wording_error_machine_does_not_match"); ?>');
    <?php else: // No Valid Ride     ?>
                    $('.error').show();
                    $('#error_message').text('<?php $CI =& get_instance(); echo $CI->config->item("wording_error_no_machine_selected"); ?>');

    <?php endif; ?>
<?php else: // No Ride Needed     ?>
                if ($.isNumeric($('#qty').val()) && $('#qty').val() > 0)
                {
    <?php if (@$_SESSION['userRecord']['admin'] && @$_SESSION['OrderProductSearch']): ?>
            <?php if(@$partnumbercustom && $stock): ?>
                    window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/<?php echo @$partnumbercustom; ?>');
                    //window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/'+$('.question').val()+'/'+$('#qty').val());
            <?php else: ?>
                var data = [];
                jQuery('.question').each(function() {
                    data.push(jQuery("option:selected", this).val());
                });
                $.post(base_url + 'ajax/addProductToCartFromFE/', {'data': data, 'orderId': "<?php echo $_SESSION['OrderProductSearch']; ?>", 'qty' : $('#qty').val()},
                    function (response)
                    {
                        //alert(response);
                        //location.reload();
                        window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/');
                    });
                //alert("<?php echo @$product['partnumber']; ?>");
                    //window.location.replace(base_url + 'admin/order_edit/<?php echo $_SESSION['OrderProductSearch']; ?>/'+$('.question').val()+'/'+$('#qty').val());
            <?php endif; ?>
    <?php else: ?>
                        //$('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo @$product['partnumber']; ?>" />');
                        $('#productDetailForm').append('<input type="hidden" name="price" value="' + $('#price').html() + '" />');
                        $('#productDetailForm').append('<input type="hidden" name="type" value="cart" />');
                        $("#productDetailForm").submit();
    <?php endif; ?>
                } else
                {
                    $('.error').show();
                    $('#error_message').text('Please enter a valid quantity.');
                }

<?php endif; ?>
        }

        return false;
    }

    function submitWishlist()
    {
        var proceed;
        if ($(".question")[0])
        {
            $(".question").each(function ()
            {
                if ($(this).val() == 0)
                {
                    proceed = 'error';
                    $('.error').show();
                    $('#error_message').text('Please select a dropdown option for this part.');
                    return false;
                }
            });
        }
        if (proceed != 'error')
        {
<?php
if ($garageNeeded):
    if ($validRide):
        ?>
                    if ($.isNumeric($('#qty').val()) && $('#qty').val() > 0)
                    {

                        $('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo $validMachines[0]['partnumber']; ?>" />');
                        $('#productDetailForm').append('<input type="hidden" name="price" value="' + $('#price').html() + '" />');
                        $('#productDetailForm').append('<input type="hidden" name="type" value="wishlist" />');
                        $("#productDetailForm").submit();
                    } else
                    {
                        $('.error').show();
                        $('#error_message').text('Please enter a valid quantity.');
                    }

    <?php elseif (@$_SESSION['garage']): // No Active Ride     ?>
                    $('.error').show();
                    $('#error_message').text('<?php $CI =& get_instance(); echo $CI->config->item("wording_error_machine_does_not_match"); ?>');
    <?php else: // No Valid Ride     ?>
                    $('.error').show();
                    $('#error_message').text('<?php $CI =& get_instance(); echo $CI->config->item("wording_error_no_machine_selected"); ?>');

    <?php endif; ?>
<?php else: // No Ride Needed     ?>
                if ($.isNumeric($('#qty').val()) && $('#qty').val() > 0)
                {
                    $('#productDetailForm').append('<input type="hidden" name="partnumber" value="<?php echo @$product['partnumber']; ?>" />');
                    $('#productDetailForm').append('<input type="hidden" name="price" value="' + $('#price').html() + '" />');
                    $('#productDetailForm').append('<input type="hidden" name="type" value="wishlist" />');
                    $("#productDetailForm").submit();
                } else
                {
                    $('.error').show();
                    $('#error_message').text('Please enter a valid quantity.');
                }

<?php endif; ?>
        }

        return false;
    }

</script>
<script>

    function deleteFromGarage(ride)
    {
        $.post(base_url + 'ajax/delete_from_garage/', {'garageLabel': ride},
                function (encodeResponse)
                {
                    location.reload();
                });
    }

    function changeActive(ride)
    {
        $.post(base_url + 'ajax/change_active_garage/', {'garageLabel': ride},
                function (encodeResponse)
                {
                    location.reload();
                });
    }
    function showVideo(vidId, vidTit) {
        var mainVideo = $('#mainVideo').data('id');
        //var mainTitle = $('.vdottl').html();
        $('.vdottl').html(vidTit);
        $("#mainVideo")[0].src = "https://www.youtube.com/embed/" + vidId + "?rel=0&autoplay=1";
        $('#mainVideo').data('id', vidId);
        //$('.shwVidHalf').show();
        $('#' + vidId).hide();
        $('#' + mainVideo).show();
        //$("#mainVideo")[0].src = "https://www.youtube.com/embed/"+vidId+"?rel=0&autoplay=1";
    }
</script>

