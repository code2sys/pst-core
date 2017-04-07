			<!-- LINKS -->
			<div class="side_header">
				<h1>Company Info</h1>
			</div>
			<div class="side_section">
								
				<?php if(@$pages): foreach($pages as $page): ?>
					<div class="side_item">
					<p><a href="<?php echo base_url('pages/index/'.$page['tag']); ?>"><i class="fa <?php echo @$page['icon']; ?>"></i> &nbsp; <b><u><?php echo $page['label']; ?></u></b></a></p>
					<div class="clear"></div>
				</div>
				<?php endforeach; endif; ?>
			</div>
			<!-- END LINKS -->
