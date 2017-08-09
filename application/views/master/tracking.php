<!-- Start Tracking -->
<!-- BEGIN: Google Trusted Stores -->
<?php 
$google_trust = (array) json_decode($store_name['google_trust']);
?>

<?php if ("" !=  $google_trust['id']): ?>
<script type="text/javascript">
  var gts = gts || [];

  gts.push(["id", "<?php echo $google_trust['id']?>"]);
  gts.push(["badge_position", "<?php echo $google_trust['badge_position']?>"]);
  gts.push(["locale", "<?php echo $google_trust['locale']?>"]);
  <?php if(@$product['partnumber']): ?>
  gts.push(["google_base_offer_id", "<?php echo $product['partnumber']; ?>"]);
  <?php endif; ?>
  gts.push(["google_base_subaccount_id", "<?php echo $google_trust['google_base_subaccount_id']?>"]);
  gts.push(["google_base_country", "<?php echo $google_trust['google_base_country']?>"]);
  gts.push(["google_base_language", "<?php echo $google_trust['locale']?>"]);

  (function() {
    var gts = document.createElement("script");
    gts.type = "text/javascript";
    gts.async = true;
    gts.src = "https://www.googlecommerce.com/trustedstores/api/js";
    var s = document.getElementsByTagName("script")[0];
    s.parentNode.insertBefore(gts, s);
  })();
</script>
<!-- END: Google Trusted Stores -->
<?php endif; ?>

<?php if (false && ($store_name['google_conversion_id'] != '')): ?>
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ad$
--------------------------------------------------->
<script type="text/javascript">
/* <![CDATA[ */
var google_conversion_id = <?php echo $store_name['google_conversion_id']?>;
var google_custom_params = window.google_tag_params;
var google_remarketing_only = true;
/* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
  <?php if ($show_ga_conversion): ?>
<div style="display:inline;">
  <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/<?php echo $store_name['google_conversion_id']?>/?value=0&amp;guid=ON&amp;script=0"/>
  </div>
    <?php endif; ?>
<?php endif; ?>

<?php if (false && $store_name['fb_remarketing_pixel'] != ''): ?>
        <script>(function() {
          var _fbq = window._fbq || (window._fbq = []);
          if (!_fbq.loaded) {
            var fbds = document.createElement('script');
            fbds.async = true;
            fbds.src = '//connect.facebook.net/en_US/fbds.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(fbds, s);
            _fbq.loaded = true;
          }
          _fbq.push(['addPixelId', '<?php echo $store_name['fb_remarketing_pixel']; ?>']);
        })();
	window._fbq = window._fbq || [];
        window._fbq.push(['track', 'PixelInitialized', {}]);
        </script>
        <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=<?php echo $store_name['fb_remarketing_pixel']; ?>&amp;ev=PixelInitialized" /></noscript>
<?php endif; ?>


<!-- Google Analytics -->
<?php if ("" != $store_name['analytics_id']): ?>
<script type="text/javascript">
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

ga('create', '<?php echo $store_name['analytics_id']; ?>', 'auto');
ga('send', 'pageview');
<?php if ($ga_ecommerce): ?>
ga('require', 'ecommerce');
  <?php endif; ?>
</script>
<?php endif; ?>

<!-- End Google Analytics -->
<!-- End Tracking -->
