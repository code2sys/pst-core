<select name="<?php echo $name; ?>" class="chzn-select" <?php echo @$additional; ?>>
	<option value=""></option> 
<?php if(is_array(@$dropdowns)):
	foreach($dropdowns as $key => $value): ?>
	<option value="<?php echo $key;?>" <?php if($selected == $key): ?> selected="selected" <?php endif; ?>><?php echo $value; ?></option>
	<?php endforeach; endif; ?>
</select>