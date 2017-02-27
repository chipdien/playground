<!-- New facebook account modal -->
<div id="addNewFbAccount" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title"><?php echo lang('ADD_UPDATE_FACEBOOK_ACCOUNT'); ?></h4>
      </div>
      <div class="modal-body">
        
        <div class='addFbAccountalerts'></div>
		<script>
			$(function(){
				$( "#addFbAccountBtn" ).click(function(){
					var reload = false;
					alertBox("<img src='theme/default/images/loading.gif' alt='loading'/>","",".addFbAccountalerts",false,false);
					$.post(
						"ajax/fbaccount.php",
						{
								fb_accesstoken: $("#accessToken").val()
						},
						function(data){
							if(data == "true"){
								alertBox(<?php echo "'".lang("FB_ACCOUNT_SAVED_SUCCESSFULLY")."'"; ?>,"success",".addFbAccountalerts",false);
									reload = true;
									$(document).on('hide.bs.modal','#addNewFbAccount', function () {
										if(reload)
											document.location.href = "settings.php#tab-fbAccounts";
									});
							}else if(data == ""){
								alertBox(<?php echo "'".lang("EMPTY_REQUEST")."'"; ?>,"danger",".addFbAccountalerts",false);
							}else{
								alertBox(data,"danger",".addFbAccountalerts",false);
							}
						}
					);
				});
			});
		</script>

		<ol>
			<li>
				<button onclick="window.open('https://goo.gl/0tBiWu','','height=500,width=600'); return false;" class="btn btn-primary"><?php echo lang('AUTH_APP'); ?></button> <?php echo lang('SET_VISIBILITY_PUBLIC'); ?>
			</li>
			<li>
				<button onclick="window.open('https://developers.facebook.com/tools/debug/accesstoken/?app_id=41158896424','','height=300,width=600'); return false;" class="btn btn-primary"><?php echo lang('Get app access token'); ?></button>
				<?php echo lang('Copy/paste the access token in the text area below'); ?>
			</li>
		</ol>

		<textarea name='accessToken' rows='3' cols='100' id="accessToken" class="form-control" placeholder='<?php echo lang('ENTER_ACCESS_TOKEN_HERE'); ?>'></textarea>
						
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php l("Close"); ?></button>
        <input type='button' class='btn btn-primary' id="addFbAccountBtn" value='<?php l("Add facebook account"); ?>'>
      </div>
    </div>

  </div>
</div>