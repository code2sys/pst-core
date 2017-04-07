			<!-- LINKS -->
			<div class="side_header tp-cat-head">
				<div class="grg">Company Info</div>
			</div>
			<div class="side_section tlg">
								
				<?php if(@$pages): foreach($pages as $page): ?>
					<div class="side_item">
					<p><a href="<?php echo base_url('pages/index/'.$page['tag']); ?>"><i class="fa <?php echo @$page['icon']; ?>"></i> &nbsp; <b><u><?php echo $page['label']; ?></u></b></a></p>
					<div class="clear"></div>
				</div>
				<?php endforeach; endif; ?>
			</div>
			<!-- END LINKS -->
