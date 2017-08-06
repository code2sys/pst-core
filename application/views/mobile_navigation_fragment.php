<?php

if (!isset($partsfinder_link)) {
    $CI =& get_instance();
    $CI->load->model("admin_m");
    $store_name = $CI->admin_m->getAdminShippingProfile();
    $partsfinder_link = $store_name["partsfinder_link"];
}

?>
<li><a href="<?php echo base_url('streetbikeparts'); ?>">Shop Street</a></li>
<li><a href="<?php echo base_url('vtwin'); ?>">Shop VTwin</a></li>
<li><a href="<?php echo base_url('dirtbikeparts'); ?>">Shop Dirt</a></li>
<li><a href="<?php echo base_url('atvparts'); ?>">Shop ATV</a></li>
<li><a href="<?php echo base_url('utvparts'); ?>">Shop UTV</a></li>
<li><a href=<?php echo base_url('Motorcycle_Gear_Brands'); ?>>Shop by Brand</a></li>
<?php if ($partsfinder_link != ""): ?>
    <li><a href=<?php echo $partsfinder_link; ?> >Shop OEM Parts</a></li
<?php endif; ?>
<li><a href="<?php echo base_url('/shopping/wishlist'); ?>">Wish list</a></li>
<li><a href="<?php echo $s_baseURL.'checkout/account'; ?>">Account</a></li>
<li><a href="javascript:void(0);" onclick="openLogin();">Login/Signup</a></li>