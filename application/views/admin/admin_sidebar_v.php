<?php if(@$categories): ?>
<!-- SIDE NAV -->
<div class="side_nav" style="width:15%">
	<h3 style="font-size:22px;">Categories</h3>
	
	<!-- CATEGORIES -->
	<ul id="side_nav" style="list-style:none;margin-left:-40px">
	
	<?php foreach($categories as $category): 
        if(!@$category['parent_code']): ?>
        <li ><a id="parent_<?php echo $category['code']; ?>" href="/adminproduct/product/<?php echo $category['code']; ?>" onclick="liClick('<?php echo $category['code']; ?>');"><?php echo $category['name']; ?></a></li>
        <?php else: ?>
        <ul class="hide <?php echo $category['parent_code']; ?>">
          <li class="subcat"><a href="/adminproduct/product/<?php echo $category['code']; ?>"><?php echo $category['name']; ?></a></li>
        </ul>
        <?php if($cat == $category['code']): $openCat = $category['parent_code']; endif;?>
       
        
      <?php endif; endforeach; ?>	
  </ul>
</div>

<?php endif; ?>


<?php if($openCat): ?>
<script>
  $('.<?php echo $openCat; ?>').show();

</script>
<?php endif; ?>
<script>
  function liClick(catCode)
  {
    <?php $_SESSION['search'] = ''; ?>
    $('.hide:not(.' + catCode + ')').hide(); 
    $('.' + catCode).toggle();
    if($('.' + catCode)[0])
    {
      if($('.' + catCode).is(":visible") == false)
        $('#parent_' + catCode).attr('href', 'javascript:void(0);');
      else
        $('#parent_' + catCode).attr('href', '/adminproduct/product/' + catCode);
    }
  }
</script>

<!-- END SIDE NAV -->