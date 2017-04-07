	<!-- CONTENT WRAP =========================================================================-->
	<div class="content_wrap">
		
		<!-- VALIDATION ERROR -->
			<?php if(validation_errors()): ?>
			<div class="validation_error">
				<img src="<?php echo $assets; ?>/images/error.png">
				<h1>Error</h1>
				<div class="clear"></div>
				<p><?php echo validation_errors(); ?></p>
			</div>
			<?php endif; ?>
			<!-- END VALIDATION ERROR -->
		
		<!-- ABOUT -->
		<div class="main_content" style="min-height:700px;">
			<h1>Blog</h1>
			<br />
			
					<!-- PHOTO BLURBS -->	
		<?php if($blogs): $i = 0; foreach($blogs as $blog): $i++;?>	
		  <div class="photo_sect_wrap popup-gallery">
				<div class="photo_sect_photo">
					<a href="<?php echo base_url($media); ?>/<?php echo $blog['value']; ?>" title="Bedside Dresser">
					<img src="<?php echo base_url($media); ?>/<?php echo $blog['value']; ?>">
				</a>
				</div>
				<div class="photo_sect_info">
					<h3><?php echo $blog['title']; ?></h3>
					<p>
						<?php echo $blog['text']; ?>
					</p>
				</div>
				
				<div class="clear"></div>
				<?php if($blog['comments']): foreach($blog['comments'] as $comment): ?>
				<?php echo $comment['value']; ?><br />
				<?php endforeach; endif; ?>
			</div>
							<div style="float:right; margin-top:-20px;"><a href="javascript:void(0);" onclick="$('#<?php echo $blog['id']; ?>').toggle();">Reply</a></div>

			<div class="photo_sect_wrap hide" id="<?php echo $blog['id']; ?>" style="margin-top:-20px;">
			  <h4><?php echo $blog['title']; ?> Comments:</h4>
			  <?php echo form_open('blog/comment/'.$blog['id'], array('class' => 'form_standard', 'id' => 'form_example')); ?>
  			  Name:<input id="name" name="name" placeholder="Your Name" class="text reg" value="<?php echo set_value('name'); ?>" /><br />
  			  Comment:<textarea id="descr" name="message" rows="6" cols="50" style="width:97.8%;"><?php echo set_value('message', ''); ?></textarea><br />
  			  Are you Human?: <?php echo @$captcha['first']; ?> + <?php echo @$captcha['second']; ?> = 
  			  <input type="text" name="user_answer" value="" class="text mini"/><input type="hidden" name="encrypted_answer" value="<?php echo @$captcha['encrypted_answer']; ?>" /><br />
  			  <input type="submit" value="Submit" class="input_button" style="margin-top:-30px; margin-right:15px;">
        </form>
		  </div>
		<?php endforeach; endif; ?>	
		<!-- END PHOTO BLURBS -->

			<br>
			
		</div>
		<!-- END ABOUT -->
		
    <?php echo @$shoppingCart; ?>		
		
		<div class="clear"></div>
	
	</div>
	<!-- END CONTENT WRAP ===================================================================-->



	<!-- CONTENT WRAPPER
		<div class="content_wrapper">
    <div class="main_content">
      <h1>Photo Blog</h1>
      
        		<!-- PHOTO POSTS EXAMPLE
		<?php if(@$posts): foreach($posts as $key => $post):
						if($key !== 'offset'): ?>
		<div class="post_wrap">
			<!-- PHOTO TITLE
			<a href="<?php echo $assets; ?>/photos/<?php echo $post['largeImageLink']; ?>"><h1><?php echo $post['title']; ?></h1></a>
			
			<!-- PHOTO DATE
			<p style="font-family:Helvetica,sans-serif;color:#F33">
				<em>Taken on <?php echo date('F jS, Y' ,strtotime($post['date'])); ?> </em>
			</p>
			<br>
			
			<!-- PHOTO DESCRIPTION
			<div class="post_text">
				<p><?php echo $post['content']; ?></p>
			</div>
		
			
			
			<!-- PHOTO
			<div class="post_photo">
				<div id="gallery">
					<!-- All photo links need to be inside this div!-->
					<a href="<?php echo $assets; ?>/photos/<?php echo $post['largeImageLink']; ?>" title="<?php echo $post['title']; ?>" class="lightbox">
       			<img src="<?php echo $assets; ?>/photos/<?php echo $post['imageLink']; ?>" class="post_photo_img"/>
      		</a>
				</div>
      </div>
			
			
			
			
			<!-- LOCATION & SHARE LINKS
			<div class="post_data">
				<table width="530" cellpadding="2">
					<tr>
						<td width="55"><p><b>Location:</b></p></td>
						<td><p><?php echo $post['location']; ?></p></td>
						<?php if((@$post['twitterEnabled']) || ($post['facebookEnabled'])): ?><td width="30" style="text-align:right"><p><b>Share:</b></p></td>
						<!-- SOCAIL BUTTONS
						<?php if(@$post['twitterEnabled']): ?>
						<td width="20" style="text-align:right">
							<a href="https://twitter.com/share" class="twitter-share-button" data-via="mentalflossDsgn" data-hashtags="creativeworks">Tweet</a>
							<script>
								!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);
								js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
							</script>
						</td>
						<?php endif; ?>
						<?php if($post['facebookEnabled']): ?>
						<td width="20">
							<div class="fb-like" data-href="http://www.mentalflossdesigns.com/general/snaps/<?php echo $post['snapsId']; ?>" data-send="false" data-layout="button_count" data-width="75" data-show-faces="false"></div>
						</td>
						<?php endif; ?>
						<!-- END SOCIAL BUTTONS
						<?php endif; ?>
					</tr>
				</table>
			</div>
			
		</div>
		<?php endif; endforeach; endif; ?>
		<!-- END PHOTO POSTS EXAMPLE 
      
    </div>
	</div>
	
	-->