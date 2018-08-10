<!-- CONTENT WRAP =========================================================================-->
<?php
$title1 = explode('-', $title);
if (count($title1) == 1) {
    $title1 = explode('–', $title1[0]);
}
$disTitle = '';
foreach ($title1 as $k => $v) {
    if (trim(strtolower($v)) != strtolower(WEBSITE_NAME)) {
        $disTitle .= $v . ' ';
    }
}
?>
<div class="content_wrap">
    <div class="cntnr-ttl">
        <div class="brndimg">
            <?php if ($brandImg != '') { ?>
                <img src="<?php echo site_url() . 'media/' . $brandImg; ?>"><br/>
                <?php } ?>
            <h1 class='mn'><?php echo trim($headTitle); ?></h1>
            </p>
        </div>
    </div>
    <!-- MAIN CONTENT -->
    <div class="main_content fl-wdh">
        <div style="float: right">
            <a href="<?php echo base_url("shopping/search_product"); ?>?brand_bypass=1&brand_id=<?php echo $brand_id; ?>" class="button" style="margin-bottom: 6px">View All <?php echo trim($brandName); ?> Products</a>
        </div>
		<?php if( $sizechart_status == 1 ) { ?>
		<p class="rulerimg">
			<a href="<?php echo base_url($sizechart_url);?>">
				<img src="<?php echo base_url('assets/images/horizontal_ruler-icon.png'); ?>" alt="<?php echo WEBSITE_NAME; ?>">
			<?php echo $brand?> Sizing Charts
			</a>
		</p>
		<?php } ?>
        <div class="clear"></div>
        <script>
            var page = "category";
        </script>
        <div id="mainProductBand">
            <?php echo @$mainProductBand; ?>
            <?php echo @$topSellersBand; ?>
        </div>
        <div class="pagination"><?php echo @$pagination; ?></div>
        <div class="clear"></div>
        <?php echo @$recentlyViewedBand; ?>	
        <?php if (@$notice): ?>
            <div class="content_section" style="background:none;border:none;">
                <p class="rplch" style="font-weight:normal;"><?php echo $notice; ?></p>
            </div>
        <?php endif; ?>			
        <div id="productPage" class="hide">1</div>
    </div>

    <!-- END MAIN CONTENT -->

    <?php echo @$sidebar; ?>
    <div class="clear"></div>
</div>
<script>
    $('.grghdng').html($('.cntnr-ttl').html());
    $('.cntnr-ttl').addClass('ttldspl');
</script>