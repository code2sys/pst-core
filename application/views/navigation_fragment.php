<?php
if (!isset($partsfinder_link)) {
    $CI =& get_instance();
    $CI->load->model("admin_m");
    $store_name = $CI->admin_m->getAdminShippingProfile();
    $partsfinder_link = $store_name["partsfinder_link"];
}

$number_across = trim($partsfinder_link) == "" ? "six" : "seven";
?>
<a href="<?php echo base_url('streetbikeparts'); ?>" class="streetBike stre-bk_b navacross<?php echo $number_across;?>">
    <div class="stre-bk_b">
        <img src="/qatesting/benz_assets/images/streetBike.png">
    </div>
    <span id="stp">Shop Street Parts & Accessories</span>
</a>
<a href="<?php echo base_url('vtwin'); ?>" class="vtwin navacross<?php echo $number_across;?>">
    <div class="stre-bk_b">
        <img src="/qatesting/benz_assets/images/vtwin.png">
    </div>
    <span id="svp">Shop VTwin Parts & Accessories</span>
</a>
<a href="<?php echo base_url('dirtbikeparts'); ?>" class="bike navacross<?php echo $number_across;?>">
    <div class="stre-bk_b">
        <img src="/qatesting/benz_assets/images/bike.png">
    </div>
    <span id="sdp">Shop Dirt Parts & Accessories</span>
</a>
<a href="<?php echo base_url('atvparts'); ?>" class="atv navacross<?php echo $number_across;?>">
    <div class="stre-bk_b">
        <img src="/qatesting/benz_assets/images/atv.png">
    </div>
    <span id="sap">Shop ATV Parts & Accessories</span>
</a>
<a href="<?php echo base_url('utvparts'); ?>" class="utv navacross<?php echo $number_across;?>">
    <div class="stre-bk_b">
        <img src="/qatesting/benz_assets/images/utv.png">
    </div>
    <span id="sup">Shop UTV Parts & Accessories</span>
</a>
<a href="<?php echo base_url('Motorcycle_Gear_Brands'); ?>" class="last navacross<?php echo $number_across;?>">
    <div class="stre-bk_b" style="height:45px;">
        <img src="/qatesting/benz_assets/images/brand-tag.png">
    </div>
    <span id="sbb">Shop by Brand</span>
</a>
<?php if ($partsfinder_link != ""): ?>
    <a href="<?php echo $partsfinder_link; ?>" class="oemparts navacross<?php echo $number_across;?>" target="_blank">
        <div class="stre-bk_b" style="height:45px;">
            <img src="/assets/oem_parts.png" alt="OEM Parts" />
        </div>
        <span id="sob">Shop OEM Parts</span>
    </a>
<?php endif; ?>