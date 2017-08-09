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

<?php if ($store_name['google_conversion_id'] != ''): ?>
<!-- Google Code for Remarketing Tag -->
<!--------------------------------------------------
Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ad$
--------------------------------------------------->
  <script>
    if(window.location.pathname == "/"){
      var google_tag_params = {
        ecomm_pagetype: 'home',
        ecomm_totalvalue: 0
      };
    }
    else if (window.location.pathname.indexOf('shopping/productlist') >= 0){
      var google_tag_params = {
        ecomm_pagetype: 'category',
      };
    }
    else if (window.location.pathname.indexOf('shopping/item') >= 0){

      var pid = [];
      for(i=1 ;i<jQuery('select[name="question[]"] option').length ;i++){
        pid.push(jQuery('select[name="question[]"] option')[i].value)
      }
      var google_tag_params = {
        ecomm_prodid: pid,
        ecomm_pagetype: 'product',
        ecomm_totalvalue: parseFloat(jQuery('.prodPrice').text().replace('$',''))
      };
    }
    else if (window.location.pathname == "/shopping/cart"){

      var pid = [];
      jQuery('input[placeholder="Add Quanity"]').each(function(){
        pid.push(jQuery(this).attr('id'))
      })

      var google_tag_params = {
        ecomm_prodid:  pid,
        ecomm_pagetype: 'cart',
        ecomm_totalvalue: parseFloat(jQuery('.cart_total h3').text().split('$')[1])
      };
    }
    else if (window.location.href.indexOf('confirm') > 0 ){
      var google_tag_params = {
        ecomm_pagetype: 'purchase'
      };
    }
    else{
      var google_tag_params = {
        ecomm_pagetype: 'other',
        ecomm_totalvalue: 0
      };
    }
  </script>
  <!-- Google Code for Remarketing Tag -->
  <!--------------------------------------------------
  Remarketing tags may not be associated with personally identifiable information or placed on pages related to sensitive categories. See more information and instructions on how to setup the tag on: http://google.com/ads/remarketingsetup
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
  <noscript>
    <div style="display:inline;">
      <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/<?php echo $store_name['google_conversion_id']?>/?guid=ON&amp;script=0"/>
    </div>
  </noscript>
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
