<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 6/30/17
 * Time: 11:26 AM
 */

// What about the website thing?
$CI =& get_instance();
if (isset($store_name)) {
    $bing_site_verification = array_key_exists("bing_site_verification", $store_name) ? $store_name["bing_site_verification"] : "";
    $google_site_verification = array_key_exists("google_site_verification", $store_name) ? $store_name["google_site_verification"] : "";
    $additional_tracking_code = array_key_exists("additional_tracking_code", $store_name) ? $store_name["additional_tracking_code"] : "";
}

?>
<?php if (SEARCH_NOINDEX): ?>
    <meta name="robots" content="noindex" />
<?php endif; ?>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<link rel="icon" href="/favicon.ico?v=2" />
<meta name="description" content="<?php print htmlentities($meta_description, ENT_QUOTES | ENT_COMPAT); ?>">
<meta name="keywords" content="<?php echo htmlentities($meta_keywords, ENT_QUOTES | ENT_COMPAT); ?>">


<script src="https://code.jquery.com/jquery-2.1.1.js"></script>
<script src="https://js.braintreegateway.com/v2/braintree.js"></script>
<script src="https://js.braintreegateway.com/js/braintree-2.30.0.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.6.3/js/client.min.js"></script>
<script src="https://js.braintreegateway.com/web/3.6.3/js/paypal.min.js"></script>
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lucida+Sans:400,500,600,700,900,800,300" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Helvetica:400,500,600,700,900,800,300" />
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,500,600,700,900,800,300" />


<?php if (isset($bing_site_verification) && $bing_site_verification != ""): ?>
<meta name="msvalidate.01" content="<?php echo $bing_site_verification; ?>" />
<?php endif; ?>


<?php if (isset($google_site_verification) && $google_site_verification != ""): ?>
<meta name="google-site-verification" content="<?php echo $google_site_verification; ?>" />
<?php endif; ?>


<?php if (isset($additional_tracking_code) && $additional_tracking_code != ""): ?>
    <?php echo $additional_tracking_code; ?>
<?php endif; ?>

<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="<?php echo base_url("/assets/scriptlibrary.js"); ?>?v=3"></script>

