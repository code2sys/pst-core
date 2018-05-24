<?php
$new_assets_url = jsite_url("/qatesting/newassets/");
$media_url = jsite_url("/media/");

// JLB 12-26-17
// We have to retrofit this because the Benz guys never, ever thought to be consistent in naming.
if (MOTORCYCLE_SHOP_NEW) {
    if (!array_key_exists("fltr", $_GET)) {
        if (array_key_exists("condition", $filter) && $filter["condition"] == 1) {
            $_GET["fltr"] = "new";
        } else {
            $_GET["fltr"] = "pre-owned";
        }
    }
} else {
    $_GET["fltr"] = "pre-owned";
}

if (!array_key_exists("brands", $_GET) && array_key_exists("brands", $filter) && is_array($filter["brands"])) {
    $_GET["brands"] = implode("$", $filter["brands"]);
}

if (!array_key_exists("years", $_GET) && array_key_exists("years", $filter) && is_array($filter["years"])) {
    $_GET["years"] = implode("$", $filter["years"]);
}

if (!array_key_exists("categories", $_GET) && array_key_exists("categories", $filter) && is_array($filter["categories"])) {
    $_GET["categories"] = implode("$", $filter["categories"]);
}

if (!array_key_exists("vehicles", $_GET) && array_key_exists("vehicles", $filter) && is_array($filter["vehicles"])) {
    $_GET["vehicles"] = implode("$", $filter["vehicles"]);
}


$CI =& get_instance();
$stock_status_mode = $CI->_getStockStatusMode();
?>

<!--

<?php print_r($filter);

$bikeControlShow = $_SESSION["bikeControlShow"];
$bikeControlSort = $_SESSION["bikeControlSort"];

?>

-->
<script type="application/javascript">
    $(document).on("change", "select[name='bikeControlShow']", function(e) {
        window.location.href = "/Motorcycle_Show/" + $("select[name='bikeControlShow']").val() + <?php echo array_key_exists("fltr", $_REQUEST) && $_REQUEST["fltr"] == "pre-owned" ? "'/1'" : "'/0'"; ?>;
    });

    $(document).on("change", "select[name='bikeControlSort']", function(e) {
        window.location.href = "/Motorcycle_Sort/" + $("select[name='bikeControlSort']").val() + <?php echo array_key_exists("fltr", $_REQUEST) && $_REQUEST["fltr"] == "pre-owned" ? "'/1'" : "'/0'"; ?>;
    });

    $(document).on("change", "input[name='major_units_featured_only']", function() {
        if ($("input[name='major_units_featured_only']:checked").length > 0) {
            <?php if (array_key_exists("fltr", $_REQUEST) && $_REQUEST["fltr"] == "pre-owned"): ?>
            window.location.href = "/Motorcycle_List/featured_preowned";
            <?php else:?>
            window.location.href = "/Motorcycle_List/featured";
            <?php endif; ?>
        } else {
            window.location.href = "/Motorcycle_Featured/0" + <?php echo array_key_exists("fltr", $_REQUEST) && $_REQUEST["fltr"] == "pre-owned" ? "'/1'" : "'/0'"; ?>;
        }

    });

</script>


<div class="sw filbar-bx">
    <div class="container_b">
        <div class="fltrbar">
            <h3 class="head-tx fiter-menu">FILTER BAR
                <span class="glyphicon glyphicon-filter"></span>
            </h3>

            <div class="fltrbx section-fiter flat prdy-dv drop-blok">
                <fieldset style="position:relative">
                    <div class="up-buttons">
                        <?php if (MOTORCYCLE_SHOP_NEW): ?>
                            <a href="<?php if (array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0): ?>/Motorcycle_List/featured<?php else: ?>/Motorcycle_List<?php endif; ?>?fltr=new" class="benz_views-product up-buttons-2 <?php echo $_GET['fltr'] == 'new' ? 'active' : ''; ?>">new</a>
                        <?php endif; ?>
                        <a href="<?php if (array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0): ?>/Motorcycle_List/featured_preowned<?php else: ?>/Motorcycle_List<?php endif; ?>?fltr=pre-owned" class="benz_views-product up-buttons-2 <?php echo $_GET['fltr'] == 'pre-owned' ? 'active' : ''; ?>">Pre-Owned</a>
                    </div>
                    <!--<span class="glyphicon glyphicon-search search-icon"></span>-->
                    <!--<input type="text" class="brandsearch sd-input it-4 js-searchable-box" placeholder="Search by Brand">-->
                </fieldset>
                <div class="filter-inner">
                    <p class="parg-txt">Search</p>
                    <form method="post" class="major_unit_search">
                        <input type="text" size="30" name="search_keywords" value="<?php echo htmlentities(array_key_exists("major_unit_search_keywords", $_SESSION) ? $_SESSION["major_unit_search_keywords"] : ""); ?>" class="search-bx major_unit_searchbox"/><br/>
                        <input type="submit" name="search_action" value="Search" class="bluebutton_b" />
                        <input type="submit" name="search_action" value="Clear" class="bluebutton_b" />
                    </form>
                </div>

                <div class="filter-inner brandsfilter">
                    <p class="parg-txt">Brand</p>
                    <?php
                    $brnds = explode('$', $_GET['brands']);
                    $brnds = array_filter($brnds);
                    ?>
                    <?php foreach ($brands as $k => $brand) { ?>
                        <?php $key = array_search($brand['make'], $brnds); ?>
                        <div class="checkbox checkbox-primary sdCheckbox brandfltr">
                            <input id="brand<?php echo $k; ?>" class="styled" name="brand[]" value="<?php echo $brand['make']; ?>" type="checkbox"
                                   <?php echo $brnds[$key] == $brand['make'] ? 'checked' : ''; ?>>
                            <label for="brand<?php echo $k; ?>"><?php echo $brand['make']; ?></label>
                        </div>
                    <?php } ?>
                </div>
                <div class="filter-inner">
                    <p class="parg-txt">Year</p>
                    <?php
                    $yr = explode('$', $_GET['years']);
                    $yr = array_filter($yr);
                    ?>
                    <?php foreach ($years as $k => $year) { ?>
                        <?php $key = array_search($year['year'], $yr); ?>
                        <div class="checkbox checkbox-primary sdCheckbox">
                            <input id="year<?php echo $k; ?>" class="styled" name="year[]" value="<?php echo $year['year']; ?>" type="checkbox"
                                   <?php echo $yr[$key] == $year['year'] ? 'checked' : ''; ?>>
                            <label for="year<?php echo $k; ?>"><?php echo $year['year']; ?></label>
                        </div>
                    <?php } ?>
                </div>
                <div class="filter-inner">
                    <p class="parg-txt">Vehicle</p>
                    <?php
                    $vhcls = explode('$', $_GET['vehicles']);
                    $vhcls = array_filter($vhcls);
                    ?>
                    <?php foreach ($vehicles as $vehicle) { ?>
                        <?php $key = array_search($vehicle['id'], $vhcls); ?>
                        <div class="checkbox checkbox-primary sdCheckbox">
                            <input id="vehicle<?php echo $vehicle['id']; ?>" class="styled" name="vehicles[]" value="<?php echo $vehicle['id']; ?>" type="checkbox" <?php echo $vhcls[$key] == $vehicle['id'] ? 'checked' : ''; ?>>
                            <label for="vehicle<?php echo $vehicle['id']; ?>"><?php echo $vehicle['name']; ?></label>
                        </div>
                    <?php } ?>
                </div>

                <div class="filter-inner">
                    <p class="parg-txt">Categories</p>
                    <?php
                    $ctgrs = explode('$', $_GET['categories']);
                    $ctgrs = array_filter($ctgrs);
                    ?>
                    <?php foreach ($categories as $category) { ?>
                        <?php $key = array_search($category['id'], $ctgrs); ?>
                        <div class="checkbox checkbox-primary sdCheckbox">
                            <input id="category<?php echo $category['id']; ?>" class="styled" name="category[]" value="<?php echo $category['id']; ?>" type="checkbox" <?php echo $ctgrs[$key] == $category['id'] ? 'checked' : ''; ?>>
                            <label for="category<?php echo $category['id']; ?>"><?php echo $category['name']; ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php
            $CI =& get_instance();
            echo $CI->load->view("benz_views/recently_viewed", array(
                "subclass" => "search-one flat fit-none",
                "innersubclass" => "search-one fit-none",
                "recentlyMotorcycle" => $recentlyMotorcycle
            ), true);
            ?>

        </div>
        <!--here -->
        <div class="next">
            <div class="bikeControlRow row">
                <div class="col-md-4">Show <select name="bikeControlShow"><option <?php if ($bikeControlShow == 5): ?>selected="selected"<?php endif; ?>>5</option><option <?php if ($bikeControlShow == 10): ?>selected="selected"<?php endif; ?>>10</option><option <?php if ($bikeControlShow == 25): ?>selected="selected"<?php endif; ?>>25</option><option <?php if ($bikeControlShow == 50): ?>selected="selected"<?php endif; ?>>50</option></select> Results</div>
                <div class="col-md-4" style="text-align: center">Sort By: <select name="bikeControlSort"><option value="0"  <?php if ($bikeControlSort == 0): ?>selected="selected"<?php endif; ?>>Relevance</option><option value="1"  <?php if ($bikeControlSort == 1): ?>selected="selected"<?php endif; ?>>Price (High to Low)</option><option value="2" <?php if ($bikeControlSort == 2): ?>selected="selected"<?php endif; ?>>Price (Low to High)</option><option value="3" <?php if ($bikeControlSort == 3): ?>selected="selected"<?php endif; ?>>Year (New to Old)</option><option value="4" <?php if ($bikeControlSort == 4): ?>selected="selected"<?php endif; ?>>Year (Old to New)</option></select></div>

                <div class="col-md-4" style="text-align: right">
                    <label><input type="checkbox" name="major_units_featured_only" value="1" <?php if (array_key_exists("major_units_featured_only", $_SESSION) && $_SESSION["major_units_featured_only"] > 0): ?>checked="checked"<?php endif; ?> /> Featured Only</label>
                </div>

            </div>

            <div class="mid prdts">
                <?php foreach ($motorcycles as $motorcycle) {

                    // What is the default...
                    $motorcycle_image = $motorcycle['image_name'];
                    if ($motorcycle['external'] == 0) {
                        $motorcycle_image = $media_url . $motorcycle_image;
                    }

                    if ($motorcycle_image == "" || is_null($motorcycle_image) || $motorcycle_image == $media_url) {
                        $motorcycle_image = "/assets/image_unavailable.png";
                    }
                    ?>
                    <div class="mid-r">
                        <span class="blok"><?php //echo preg_replace('/[^A-Za-z0-9\-]/', '', $title); ?></span>
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
                                <?php
                                $CI =& get_instance();
                                echo $CI->load->view("benz_views/pricing_widget", array(
                                    "motorcycle" => $motorcycle
                                ), true);
                            ?>
                            </div>
                            <div class="mid-text-right">
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
                                <?php if ($motorcycle['mileage'] > '0') { ?>
                                    <p>mileage :<span><?php echo $motorcycle['mileage']; ?></span></p>
                                <?php } ?>
                                <?php if ($motorcycle['engine_type'] != '') { ?>
                                    <p>Engine type :<span><?php echo $motorcycle['engine_type']; ?></span></p>
                                <?php } ?>
                                <?php if (($motorcycle['stock_status'] == 'In Stock' && $stock_status_mode >= 2 ) || ($motorcycle['stock_status'] != 'In Stock' && ($stock_status_mode == 1  || $stock_status_mode == 3))): ?>
                                    <p>availability : <span style="font-weight: bold; color: <?php echo $motorcycle['stock_status'] == 'In Stock' ? 'green' : 'red'; ?>"><?php echo $motorcycle['stock_status'];?></span></p>
                                <?php endif; ?>

                            </div>
                        </div>
                        <?php
                        $CI->load->helper("mustache_helper");
                        $motorcycle_action_buttons = mustache_tmpl_open("motorcycle_action_buttons.html");
                        mustache_tmpl_set($motorcycle_action_buttons, "motorcycle_id", $motorcycle['id']);
                        mustache_tmpl_set($motorcycle_action_buttons, "new_assets_url", $new_assets_url);
                        if (!defined('GET_FINANCING_WORDING')) {
                            define('GET_FINANCING_WORDING', 'GET FINANCING');
                        }
                        mustache_tmpl_set($motorcycle_action_buttons, "get_financing_wording", GET_FINANCING_WORDING);
                        mustache_tmpl_set($motorcycle_action_buttons, "view_url", base_url(strtolower($motorcycle['type']) . '/' . $motorcycle['url_title'] . '/' . $motorcycle['sku']));
                        echo mustache_tmpl_parse($motorcycle_action_buttons);
                        ?>
                    </div>

                    <?php
                    $this->view('modals/major_unit_detail_modal.php', array(
                        'motorcycle'       => $motorcycle,
                        'motorcycle_image' => $motorcycle_image,
                    ));
                }

                $CI =& get_instance();
                echo $CI->load->view("benz_views/recently_viewed", array(
                    "master_class" => "fltrbar search-two my-wdt",
                    "subclass" => "",
                    "innersubclass" => "",
                    "recentlyMotorcycle" => $recentlyMotorcycle
                ), true);
                ?>
                <div class="mypagination">
                    <?php $page = 1; ?>
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
                </div>
            </div>
        </div>
    </div>
</div>