<?php



$template = mustache_tmpl_open("master/widgets/motorcycles.html");

mustache_tmpl_set($template, "DISABLE_FRONT_MOTORCYCLE_NAV", defined("DISABLE_FRONT_MOTORCYCLE_NAV") && DISABLE_FRONT_MOTORCYCLE_NAV);
mustache_tmpl_set($template, "MOTORCYCLE_SHOP_DISABLE", defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE);

if (defined("MOTORCYCLE_SHOP_NEW") && MOTORCYCLE_SHOP_NEW && (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED)) {
    mustache_tmpl_set($template, "MOTORCYCLE_OPTION_1", true);
    mustache_tmpl_set($template, "moto_width", 3);
} else if (defined("MOTORCYCLE_SHOP_NEW") && MOTORCYCLE_SHOP_NEW) {
    mustache_tmpl_set($template, "MOTORCYCLE_OPTION_2", true);
    mustache_tmpl_set($template, "moto_width", 4);
} elseif (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED) {
    mustache_tmpl_set($template, "MOTORCYCLE_OPTION_3", true);
    mustache_tmpl_set($template, "moto_width", 4);
} else {
    mustache_tmpl_set($template, "moto_width", 6);
}

mustache_tmpl_set($template, "MOTORCYCLE_SHOP_DISABLE", defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE);
mustache_tmpl_set($template, "MOTORCYCLE_SHOP_DISABLE", defined("MOTORCYCLE_SHOP_DISABLE") && MOTORCYCLE_SHOP_DISABLE);

if (count($featured) > 0) {
    
}


echo mustache_tmpl_parse($template);

?>
<?php if (!defined("DISABLE_FRONT_MOTORCYCLE_NAV") || !DISABLE_FRONT_MOTORCYCLE_NAV): ?>
    <?php if (!defined("MOTORCYCLE_SHOP_DISABLE") || !MOTORCYCLE_SHOP_DISABLE): ?>
        <div class="sw bg">
            <div class="modal-lg">
                <?php if (MOTORCYCLE_SHOP_NEW && (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED)) { ?>
                    <div class="col-md-3 wrap-col">
                        <a href="<?php echo site_url("Motorcycle_List?fltr=new") ?>"><h2>shop new models</h2></a>
                    </div>
                    <div class="col-md-3 wrap-col">
                        <a href="<?php echo site_url("Motorcycle_List?fltr=pre-owned") ?>"><h2>shop pre-owned</h2></a>
                    </div>

                    <?php $moto_width = 3; ?>
                <?php } elseif (MOTORCYCLE_SHOP_NEW) { ?>
                    <div class="col-md-4 wrap-col">
                        <a href="<?php echo site_url("Motorcycle_List?fltr=new") ?>"><h2>shop new models</h2></a>
                    </div>
                    <?php $moto_width = 4; ?>
                <?php } elseif (!defined("MOTORCYCLE_SHOP_USED") || MOTORCYCLE_SHOP_USED) { ?>
                    <div class="col-md-4 wrap-col">
                        <a href="<?php echo site_url("Motorcycle_List?fltr=pre-owned") ?>"><h2>shop pre-owned</h2></a>
                    </div>
                    <?php $moto_width = 4; ?>
                <?php } else { ?>
                    <?php $moto_width = 6; ?>
                <?php } ?>
                <div class="col-md-<?php echo $moto_width; ?> wrap-col">
                    <a href="<?php echo site_url("pages/index/financerequest") ?>"><h2>apply for Financing</h2></a>
                </div>
                <div class="col-md-<?php echo $moto_width; ?> wrap-col">
                    <a href="<?php echo site_url("pages/index/servicerequest") ?>"><h2>schedule service</h2></a>
                </div>
            </div>
        </div>

        <div class="sw filterBar">
            <div class="container_b">
                <h1 class="head-txt"> featured models: <span> <a href="<?php echo base_url('Motorcycle_List');?>">shop more</a> </span></h1>
                <div id="hotels-flats" class="owl-carousel">
                    <?php foreach($featured as $feature) { ?>
                        <?php $title = str_replace(' ', '_', trim($feature['title']));?>
                        <a href="<?php echo base_url(strtolower($feature['type']).'/'.$title.'/'.$feature['sku']);?>">
                            <div class="item">
                                <div class="item-box">
                                    <img class="lazyOwl" data-src="<?php echo base_url().'media/'.$feature['image_name']; ?>" alt="Motorcycle Image">
                                    <p> <?php echo $feature['title'];?></p>
                                    <?php if( $feature['call_on_price'] == '1' ) { ?>
                                        <h2 class="cfp">Call For Price</h2>
                                    <?php } else { ?>
                                        <h2>Sale Price: &nbsp;$<?php echo $feature['sale_price'];?></h2>
                                    <?php } ?>
                                    <!--<h2>CALL FOR PRICE</h2>-->
                                </div>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endif; ?>