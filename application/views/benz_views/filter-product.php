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
                    <!-- <div class="mid-r-img-veh">
                        <img src="<?php echo $motorcycle_image; ?>" width="px;"/>
                    </div> -->
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
                        <p>stock :<span><?php echo clean_complex_sku($motorcycle); ?></span></p>
                    <?php } ?>
                    <?php if ($motorcycle['mileage'] > 0) { ?>
                        <p>mileage :<span><?php echo $motorcycle['mileage']; ?></span></p>
                    <?php } ?>
                    <?php if ($motorcycle['engine_type'] != '') { ?>
                        <p>Engine Type :<span><?php echo $motorcycle['engine_type']; ?></span></p>
        <?php } ?>
                    <?php if (($motorcycle['stock_status'] == 'In Stock' && $stock_status_mode >= 2 ) || ($motorcycle['stock_status'] != 'In Stock' && ($stock_status_mode == 1  || $stock_status_mode == 3))): ?>
                        <p>availability : <span style="font-weight: bold; color: <?php echo $motorcycle['stock_status'] == 'In Stock' ? 'green' : 'red'; ?>" ><?php echo $motorcycle['stock_status'];?></span></p>
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
        ?>

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