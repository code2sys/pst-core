<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 7/11/17
 * Time: 4:20 PM
 */

$CI =& get_instance();
if (isset($store_name)) {
    $additional_footer_code = array_key_exists("additional_footer_code", $store_name) ? $store_name["additional_footer_code"] : "";
}


if (isset($additional_footer_code) && $additional_footer_code != ""): ?>
    <?php echo $additional_footer_code; ?>
<?php endif;
