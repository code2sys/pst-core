<?php
if (!defined('SEARCH_PLACEHOLDER_WORDING')) {
    define('SEARCH_PLACEHOLDER_WORDING', 'Search Parts and Apparel');
}
?>
<div class="searchHolder search-one">
    <form action="<?php echo base_url(); ?>shopping/productlist" method="post" id="moto_search" class="form_standard">
        <input id="search" name="search" placeholder="<?php echo SEARCH_PLACEHOLDER_WORDING; ?>" class="search-bx" style="float:left;" />
        <a href="javascript:void(0);" class="goBtn_b" onClick="setSearch($('#search').val());">Go!</a>
    </form>
    <div class="clear"></div>
</div>