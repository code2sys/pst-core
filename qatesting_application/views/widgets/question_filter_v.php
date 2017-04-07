<?php 


 if(@$questions): $key = 0;

		$sortURL = 'shopping/productlist';
		
		if(@$breadcrumbs['brand'])
		{
			$brandlist = $breadcrumbs;
			$brand_id = $brandlist['brand'];
			unset($brandlist['brand']);
			$key = array_search($brand_id, $brandlist);
			unset($breadcrumbs[$key]);
			$breadcrumbs['brand'] = $brand_id;
		}
		foreach($breadcrumbs as $name => $value ):  
			if(($name == 'category') && (is_array($value))):
				end($value);
				$id = key($value);
				$sortURL .= '/category/'.$id;	
		
		  else: 
		    foreach($breadcrumbs as $filter => $sortValue): 
					if(($filter != $name) && ($filter != 'category') && (strpos($filter, 'question') !== 0)): // Do not put the ugliness of the long name in the URL
						if($filter == 'search'): 
							$sortURL .= 1; 
						endif; // Do not put search results in URL.
					else:
						if(($name == 'brand') && (strpos($sortURL, 'brand') == 0)):
							$sortURL .= '/brand/'.$value;	
						endif;
					endif; 
				endforeach; 
			endif;
		endforeach;
						
				foreach($questions as $ref):  
					if($ref['question'] != @$question):
						++$key;
						
						if(@$question): // End of section previous and Begin a new one
						
							
?>
			</ul>
		</div>
	</div>
	
	<?php endif; ?>
	<div class="side_header" style="height:75px;">
		<h1>Refine Search by: <br /> <?php echo $ref['question']; ?></h1>
	</div>		
	
	<div class="base_container">
		<div class="base_nav">
			<ul>
				<li>
	<?php if(@$breadcrumbs['question'][$ref['question']] == $ref['answer']):  ?>
	
						<div style="float:right; margin:-15px -5px;"><a href="javascript:void(0);" onclick="removeMainSearch('question', '<?php echo $ref['question']; ?>')" style="color:#F00"> <i class="fa fa-times"></i> &nbsp; </a></div>
					<?php endif; ?>
					<a href="<?php if(isset($ref['link'])): echo base_url('shopping/productlist'.$ref['link']); else: ?>javascript:void(0);<?php endif; ?>" onclick="setNamedSearch(event, 'question', '<?php echo $ref['question']; ?>', '<?php echo $ref['answer']; ?>')" id="<?php echo $ref['answer']; ?>"><?php echo $ref['answer']; ?> (<?php echo $ref['qty']; ?>)</a>					
					
				</li>

	<?php else:   ?>
				<li>
					<?php if(@$breadcrumbs['question'][$ref['question']] == $ref['answer']):  ?>
						<div style="float:right; margin:-15px -5px;"><a href="javascript:void(0);" onclick="removeMainSearch('question', '<?php echo $ref['question']; ?>')" style="color:#F00"> <i class="fa fa-times"></i> &nbsp; </a></div>
					<?php endif; ?>
						<a href="<?php if(isset($ref['link'])): echo base_url('shopping/productlist'.$ref['link']); else: ?>javascript:void(0);<?php endif; ?>" 
						onclick="setNamedSearch(event, 'question', '<?php echo $ref['question']; ?>', '<?php echo $ref['answer']; ?>')" id="<?php echo $ref['answer']; ?>"><?php echo $ref['answer']; ?> (<?php echo $ref['qty']; ?>)</a>
					

				</li>
	
	<?php endif; ?>




<?php $question = $ref['question'];
endforeach; endif; ?>
			</ul>
		</div>
	</div>