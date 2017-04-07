<?php if(@$categories): ?>

       


			
			
			
			<form action="" method="post" id="form_example" class="form_standard" onsubmit="searchProducts();">
			<div class="filter_sect">
				<select id="dropdown1" name="dropdown1" onchange="window.location = '/shopping/index/' + $('#dropdown1').val();">
          <option selected="selected" value="">All Products</option>
					<?php foreach($categories as $category): ?>
			        <?php if(!@$category['parent_code']): ?>
			        	<option id="parent_<?php echo $category['code']; ?>" <?php if ($openCat == $category['code']): echo 'selected'; endif; ?> value="<?php echo $category['code']; ?>"><?php echo $category['name']; ?></option>
		          <?php endif; ?>
		      <?php endforeach; ?>	
        </select>
			</div>
			<div class="filter_sect">
				<select id="dropdown2" name="dropdown2" onchange="window.location = '/shopping/index/' + $('#dropdown1').val() + '/' + $('#dropdown2').val();">
          <option selected="selected" value="">Products Types</option>
					<?php foreach($subCategories as $category): ?>
		        	<option id="parent_<?php echo $category['code']; ?>" <?php if ($subCategory == $category['code']): echo 'selected'; endif; ?> value="<?php echo $category['code']; ?>"><?php echo $category['name']; ?></option>
		      <?php endforeach; ?>	
        </select>
			</div>
			<div class="filter_sect" style="margin-right:0px;">
				<div class="dynamic_button" id="searchButton">
				<input id="search" name="search" placeholder="Enter Product Name" class="text medium" style="float:left;margin-right:10px;"/>
					<a href="javascript:void(0);" onclick="searchProducts();">Search</a>
				</div>
			</div>
			<div class="divider"></div>
			</form>









<?php endif; ?>


<?php if($openCat): ?>
<script>
  $('.<?php echo $openCat; ?>').show();
  
  $(document).ready(function(){
	  $('#search').keyup(function(e){
	  	if (e.keyCode == 13) {
		  	$('#searchButton a').click();
		  	return false;
	  	}
	  })
  });
  
  

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
        $('#parent_' + catCode).attr('href', '/shopping/index/' + catCode);
    }
  }
</script>

<!-- END SIDE NAV -->