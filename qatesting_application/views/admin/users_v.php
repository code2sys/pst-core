		<!-- CONTENT -->
		<div class="content">	
		
			<!-- ADMIN SECTION -->
	<div class="admin_sect" style="width:95.6%;">
				
				<h1>Manage Users</h1>
				<p>
					Adjust status and permissions for users.
				</p>
        <input id="search" name="name" placeholder='Search Products' class="search_input"/> 
        <div class="dynamic_button" id="searchButton">
          <a href="javascript:void(0);" onclick="searchUsers();">Search</a>
        </div>
        <!-- JAVASCRIPT ALERT -->
        	<div class='validation_error hide' id="val_container">
        	  <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
            <h1>Error</h1>
            <p id="val_error_message"></p>
            <div class="clear"></div>
          </div>
        <!-- END JAVASCRIPT ALERT -->
        <!-- PHP ALERT -->
          <?php if(validation_errors() || @$errors) { ?>
            <div class="validation_error">
            <img src="<?php echo $assets; ?>/images/error.png" style="float:left;margin-right:10px;">
              <h1>Error</h1>
              <p>Please make the following corrections: </p>
              <?php echo validation_errors(); echo @$errors; ?>
            </div>
            <br />
          <?php } ?>
          <!-- END PHP ALERT -->
          
          <?php $attributes = array('id' => 'editUserForm', 'class' => 'form_standard');
  		  echo form_open('admin/process_edit_users', $attributes);?>	
  		  
  		  <div class="dynamic_button" style="margin-top:0px">
					<a href="javascript:void(0);" onclick="editUsers();">Process Changes</a>
				</div>
				
				<!-- PAGINATION -->
            <div id="pagination">
            	<?php echo @$pagination; ?>
            </div>
        <!-- END PAGINATION -->					
					<div class="clear"></div>
				
					<br>
				
				<!-- PRODUCTS -->
				
				<div id="userTable"><?php echo @$userTable; ?></div>
				
				<!-- END PRODUCTS -->
				</form>
				<br>
			</div>
			<!-- END ADMIN SECTION -->
			
				
			
		</div>
		<!-- END CONTENT -->
			
	</div>
	<!-- END CONTENT WRAP ===================================================================-->
<div class="hide" id="userPage">1</div>	
	<div class="clearfooter"></div>


  <!-- PAGINATION STATE HOLDERS -->
  <div class="hide" id="userFilter">NULL</div>
  <div class="hide" id="userPage">1</div>
  <!-- END PAGINATION STATE HOLDERS -->