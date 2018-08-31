<?php
if ((isset($validRide) && $validRide && (isset($garageNeeded) && $garageNeeded)) || $product["universal_fitment"] > 0) {
    require(__DIR__ . "/../fitment_common.php");
} else {
    $has_fitment = false;
}

require_once 'Mobile_Detect.php';
$detect = new Mobile_Detect;
$CI =& get_instance();

$online_in_stock_string = '<span class="online_only hide" style="display: inline-block">Online Only</span><span class="instock hide"  style="display: inline-block">Available For Store Pickup</span>';
$qty_input = form_input(array('name' => 'qty',
    'value' => 1,
    'maxlength' => 250,
    'class' => 'text mini qtyInput',
    'placeholder' => '0',
    'id' => 'qty'));

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

                print "<a href='/shopping/productlist" . $CI->parts_m->categoryReturnURL($breadCrumbCategories[$i]["category_id"]) . "' onClick='setMainSearchCategory(event, \"category\", \"" . $breadCrumbCategories[$i]["category_id"] . "\");'>" .  htmlentities($breadCrumbCategories[$i]["name"]) . "</a>";
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
                <img src="<?php echo site_url() . 'media/' .  $brandMain['image']; ?>"><br/>
                <h1 class='mn' id="mn-md"><?php echo $product['name']; ?></h1>

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
            <?php if ($has_fitment) {
                $image_to_use = $height = $j_width = 0;
                if ($product["universal_fitment"] > 0) {
                    $image_to_use = $universal_image;
                    $height = $universal_height;
                    $j_width = $universal_width;
                } else {
                    $image_to_use = $fitment_image;
                    $height = $fitment_height;
                    $j_width = $fitment_width;
                }
                $height = floor($height / 2.0);
                $j_width = floor($j_width / 2.0);
                ?>


            <?php } ?>
            <?php if (@$product['images']): ?>
                <img itemprop="image" src="<?php echo jsite_url("/productimages/"); ?><?php echo $product['images'][0]['path']; ?>" id="base_image" style="  margin: 0 auto; display: table;  max-width: 318px!important;  max-height: 335px!important;">
                <?php /* ?>  DISPLAYING THE CHECK MARK, IF PRODUCT PART MATCHES WITH GARAGE <?php */ ?>

            <?php else: ?>
                <img src="<?php echo $assets; ?>/images/test_image.jpg" id="base_image" style="  margin: 0 auto; display: table;  max-width: 318px!important;  max-height: 335px!important;">

                <?php /* ?>  DISPLAYING THE CHECK MARK, IF PRODUCT PART MATCHES WITH GARAGE <?php */ ?>
            <?php endif; ?>
            <?php if ($has_fitment): ?><img src="<?php echo $image_to_use; ?>" height="<?php echo $height; ?>" width="<?php echo $j_width; ?>" style=" position: relative; margin-top: -<?php echo 10 + $height; ?>px; float: right; margin-right: 12px; width: <?php echo $j_width; ?>px !important; height: <?php echo $height; ?>px !important; " ><?php endif; ?>


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
                            $original_price = $product['price']['sale_min'];

                            if (array_key_exists('sale_max', $product['price']) && $product['price']['sale_max'] != '' &&  $product['price']['sale_max'] != $original_price) {
                                $original_price .= ' - $' . $product['price']['sale_max'];
                            }
                            echo $original_price;
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
                    <div class="questions_and_quantities_block">
                    <?php
                    $is_qty_displayed = 0;

                    // JLB 08-31-18
                    // This was a shitpile before I got here. It's still weird, but it's not as awful as it was!
                    if (isset($questions) && is_array($questions) && count($questions) > 0) {
                        // Reassemble this into a structure that maps partquestion_id => an array of the question and answers, then display those answers
                        $requestioned = array();
                        foreach ($questions as $question) {
                            $partquestion_id = $question["partquestion_id"];
                            if (!array_key_exists($partquestion_id, $requestioned)) {
                                $requestioned[$partquestion_id] = array(
                                    "question" => $question["question"],
                                    "partquestion_id" => $partquestion_id,
                                    "answers" => array(
                                            '0' => 'Select an option'
                                    )
                                );
                            }
                            $requestioned[$partquestion_id]["answers"][ $question['partnumber'] ] = $question['answer'];
                        }

                        print "<!-- Requestioned: ";
                        print_r($requestioned);
                        print "-->";

                        $currentQuestion = "";

                        foreach ($requestioned as $key => $quest) {
                            $currentQuestion = $quest['partquestion_id'];
 ?>
                            <div class="questionSelector">
                                <div class="question">
                                    <?php echo $quest['question']; ?>:
                                </div>
                                <div class="answer">
                                    <?php
                                    echo form_dropdown('question[]', $quest['answers'], @$_SESSION['cart'][$product['part_id']][$quest['partquestion_id']], 'style="", class="question questionSelector ' . $currentQuestion . '", onchange="updatePrice(' . $currentQuestion . ');"'); ?>
                                </div>
                                <div style="clear: both"></div>
                            </div>
                            <?php
                        }

                    }

                    ?>

                    <div class="quantity_block">
                        <div class="quantity_selector">
                                QTY:
                                <?php echo $qty_input; ?>
                        </div>

                        <div class="quantity_description">
                            <div class="stock hide" id="out_of_stock">
                                <span class="outOfStockStatus">OUT OF STOCK - PLEASE CALL TO ORDER</span>
                            </div>
                            <div class="stock hide" id="in_stock">
                                <span class="stockStatus">In Stock</span>
                                <?php echo $online_in_stock_string; ?>
                                <div class="clear"></div>
                                <div class="hide" id="low_stock" style="display:inline;">
                                    ONLY
                                    <div id="stock_qty" style="display:inline;">1</div>
                                    REMAINING
                                </div>
                                <div class="clear"></div>
                            </div>
                        </div>
                    </div>

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
                    <div class="rightCol" id="shoppingBuyButtonContainer">
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
            <?php if ($has_fitment || !@$this->_mainData['garageNeeded']): ?>
            <a style="padding:10px 10px 8px 10px;" href="javascript:void(0);" onclick="changeTabs('partnumbers')" id="partnumbers">Part Numbers</a>
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
        if ($('#partnumbers').length)
        {
            $('#partnumbers').removeClass('active');
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

    function figureStockStatus(partObj) {
        var $in_stock = $('#in_stock');
        var $out_stock = $('#out_of_stock');
        var $low_stock = $('#low_stock');
        $in_stock.hide();
        $out_stock.hide();
        $("#submit_button").attr("onclick", "submitCart()");
        console.log(partObj.quantity_available);
        if (partObj.quantity_available > 0)
        {
            $in_stock.show();
            $low_stock.hide();
            if (partObj.quantity_available < 6)
            {
                $low_stock.show();
                $('#stock_qty').html(partObj.quantity_available);
            }

            if (partObj.dealer_quantity_available && parseInt(partObj.dealer_quantity_available, 10) > 0) {
                console.log("B Yes ");
                $("#in_stock .instock").show();
                $("#in_stock .online_only").hide();

            } else {
                console.log("B No  ");
                $("#in_stock .online_only").show();
                $("#in_stock .instock").hide();
            }
        } else
        {
            $out_stock.show();
            $("#submit_button").attr("onclick", "outOfStockWarning()");
        }
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
                    figureStockStatus(partObj);
                });
    }

    function outOfStockWarning()
    {
        alert('OUT OF STOCK - PLEASE CALL TO ORDER');
    }

    function updatePrice(questionId)
    {
        $('#price').html("<?php echo $original_price; ?>");

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

                            figureStockStatus(partObj);
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

    <?php if ($garageNeeded && !$validRide) {
        ?>
    $(document).on("ready", function() {
        if ($("#price").text() == "$0") {
            $('.error').show();
            $('#error_message').text('Your machine does not match this item.  Please change your active machine above to add this item to cart.');
            $("span#price").hide();
        } else {
            $("span#price").show();
        }
    });
<?php
    } ?>

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
</script>
<?php
$CI =& get_instance();
echo $CI->load->view("showvideo_function", array(), false);
?>


