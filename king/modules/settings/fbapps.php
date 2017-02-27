<h4 class="tab-title"><i class="fa fa-plug"></i> <?php echo lang('FB_APPS'); ?></h4>
<div class="manageAppErrors"></div>
		<?php 
			if($user->hasPermission("admin")){ 
		?>
		<script>
			$( document ).ready(function() {
				$("#addFbApp").click(function(){
					// app_id and app_serect validation
					if($("#fbapp_id").val() == ""){
						$(".manageAppErrors").html("<img src='theme/default/images/loading.gif' alt='loading'/>");
						alertBox("<?php echo lang('APP_ID_CAN_NOT_EMPTY'); ?>",'danger',".manageAppErrors");
					} else {
						$(".manageAppErrors").html("<img src='theme/default/images/loading.gif' alt='loading'/>");;
						$.post( "ajax/fbapp.php", {app_id:$("#fbapp_id").val(),app_secret:$("#fbapp_secret").val(),fbapp_auth_Link:$("#fbapp_auth_Link").val(),},function( data ) {
							if(data == "true"){
								alertBox("<?php echo lang('APP_ADDED_SUCCESS'); ?>",'success',".manageAppErrors");
							}else{
								alertBox(data,'danger',".manageAppErrors");
							}
						});
					}
				});
		});
		</script>
		<label for="fbapp_id"><?php echo lang('FB_APP_ID'); ?></label>
		<input type="text" name="fbapp_id" class="form-control" id="fbapp_id" placeholder="<?php echo lang('FB_APP_ID'); ?>" value="" />

		<label for="fbapp_secret"><?php echo lang('FB_APP_SECRET'); ?></label>
		<input type="text" name="fbapp_secret" class="form-control" id="fbapp_secret" placeholder="<?php echo lang('FB_APP_SECRET'); ?>" value=""/>

		<label for="fbapp_auth_Link"><?php echo lang('FB_APP_AUTH_LINK'); ?>
			&nbsp;<a href="#"  onclick="return false;" data-toggle="tooltip" data-placement="top" style="float:right" title="<?php echo lang('FB_APP_AUTH_LINK_NOTE'); ?>"> 
			<span class="glyphicon glyphicon-question-sign"></span></a>
		</label>
		<input type="text" name="fbapp_auth_Link" class="form-control" id="fbapp_auth_Link" placeholder="<?php echo lang('FB_APP_AUTH_LINK'); ?>" value=""/>

		<input type="button" name="addFbApp" value="<?php echo lang('ADD'); ?>" id="addFbApp" class="btn btn-primary" />

		<br />
		<br />
	<?php 
	}
	?>

	<?php  if($fbaccount->UserDefaultFbAccount()){

		$currentFbAccount = $fbaccount->get($fbaccount->UserDefaultFbAccount()); 
		
		if($currentFbAccount): ?>

				<div class='alert alert-warning' role='alert'>
				<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>
				<?php echo sprintf(lang('Please make sure to logged in to Facebook as %s before authenticate the app.'), "<strong> ".$currentFbAccount->GetFirstname() . " " . $currentFbAccount->GetLastname()." </strong>"); ?>
				</div>
		
		<?php 
		endif;
	}
	?>

	<table class='table table-bordered table-striped'>
		<thead>
			<tr>
				<td><?php echo lang('APP_NAME'); ?></td>
				<td><?php echo lang('STATUS'); ?></td>
				<td></td>
			</tr>
		</thead>
		<script>
			function FbAuth(app_id){
				var oldApi = "";
				if($('#oldApi').is(":checked")){
					oldApi = "&oldApi=true"
				}
				window.open('FbAuth.php?app_id='+app_id+oldApi,'','height=500,width=600');
			}
		</script>
		<tbody id="fbapps">
			<?php 
				
				foreach($fbapps->getAll() as $fbapp){

					if($fb->GetAccessToken($fbapp->getAppId())){
						$statusIcon = "ok";
						$statusText = lang('AUTHENTICATED');
						$statusBtn = "";
						$oldApi = Input::Get("oldApi") ? "&oldApi=true" : "";
					} else {
						$statusIcon = "remove";
						$statusText = lang('NOT_AUTHENTICATED');

						if($fbapp->appType($fbapp->getAppId()) == 2){
							$statusBtn = "<button onclick=\"window.open('resetaccesstoken.php','','height=570,width=600'); return false;\" class='btn btn-primary'>".lang('AUTHENTICATE')."</button> ";
						}
						
						if($fbapp->appType($fbapp->getAppId()) == 3){
							$statusBtn = "<button onclick=\"window.open('defaultappauth.php?app_id=".$fbapp->getAppId()."','','height=450,width=600'); return false;\" class='btn btn-primary'>".lang('AUTHENTICATE')."</button> ";
						}

						if($fbapp->appType($fbapp->getAppId()) == 1){
							$statusBtn = "<button onclick='FbAuth(".$fbapp->getAppId().");return false;' class='btn btn-primary'>".lang('AUTHENTICATE')."</button>";
							if($user->hasPermission("admin")){
								$statusBtn .= "&nbsp; <input type='checkbox' name='oldApi' id='oldApi'/> <label for='oldApi'>API <=2.3</label>";
							}
						}
					}

					echo "<tr><td>".$fbapp->getAppName()."</td>";

					echo "<td><span class='glyphicon glyphicon-".$statusIcon."'></span> " . $statusText . "</td>";
					echo "<td>";
					if($user->hasPermission("admin")){
						echo "<a href='?action=deletefbapp&id=".$fbapp->getAppId()."' title='".lang('DELETE')."' class='btn btn-danger'><span class='glyphicon glyphicon-trash'></span> ".lang('DELETE')."</a>";
					}
					if($fb->GetAccessToken($fbapp->getAppId())){
						echo "<a href='?action=deauthorize&id=".$fbapp->getAppId()."' title='".lang('DEAUTHENTICATE')."' class='btn btn-danger'><span class='glyphicon glyphicon-remove'></span> ".lang('DEAUTHENTICATE')."</a>";
					}else{
						echo $statusBtn;
					}
					echo "</td></tr>";	
				}
				
			?>
		</tbody>
	</table>