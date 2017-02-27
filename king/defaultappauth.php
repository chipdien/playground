<?php 
require "core/init.php";
$template = new template();
?>
<html>
<head>
	<meta charset="UTF-8" />
	<title><?php echo lang('AUTHENTICATE');?></title>
	<meta name="description" content="">
	<meta name="author" content="Abdellah Gounane - Icodix.com">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSS Files -->
	<link href="theme/default/css/custom.css" rel="stylesheet" />	
	<link href="theme/default/css/jquery.datetimepicker.css" rel="stylesheet">
	<link href="theme/default/css/datatables.bootstrap.min.css" rel="stylesheet">
	<link href="theme/default/bootstrap/css/bootstrap.min.css" rel="stylesheet">

	<!-- JS Files -->
	<script src="theme/default/js/jquery.js"></script>
	<script src="core/js/lang.js"></script>
	<script src="core/js/javascript.js"></script>
	<script src="theme/default/js/jsui.js"></script>
	<script src="theme/default/js/postpreview.js"></script>
	<script src="theme/default/bootstrap/js/bootstrap.min.js"></script>
	<script src="theme/default/js/jquery.datetimepicker.min.js"></script>
	<script src="theme/default/js/jquery.dataTables.min.js"></script>
	<script src="theme/default/js/dataTables.bootstrap.min.js"></script>
</head>
<body>
	<noscript>
		<div class="alerts alert alert-danger">
			<p class='alerttext'>JavaScript MUST be enabled in order for you to use kingposter. However, it seems JavaScript is either disabled or not supported by your browser. If your browser supports JavaScript, Please enable JavaScript by changing your browser options, then try again.</p></div>
		</noscript><div class="container">
		<div class="row">
			<form method='POST'>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">&nbsp;<?php echo lang('GET_ACCESS_TOKEN_URL'); ?> </h3>
					</div>
					<div class="panel-body">
						<div class='messageBox'></div>
						<?php

						$app_id = trim(Input::Get("app_id","GET"));

						$fromFBTool = array("41158896424","174829003346");
						$appshaveRealfbid = array('41158896424','10754253724','179375112119470','24553799497','200758583311692','201123586595554');

						if(!$app_id){
							echo "<script> alertBox('".lang('APP_ID_NOT_SPECIFIED')."','danger','.messageBox',true);</script>";
						}else{

							$fbapp = new FbApps();
							$fbapp = $fbapp->get($app_id);

							if($fbapp){
								if(isset($_POST['submit'])){
									$accessToken = trim($_POST['accessToken']);
									if(empty($accessToken)){
										echo "<script> alertBox('".lang('ENTER_ACCESS_TOKEN')."','danger','.messageBox',true);</script>";
									}else{

										$user = new User();
										$fb = new Facebook();
										$fb_account = new FbAccount();

										try{

											if(!$fb_account->UserDefaultFbAccount()){
												throw new Exception(lang('NO_FB_ACCOUNT_SELECTED'));
											}else{
												// Save access token
												if(	$userData = $fb->GetUserFromAccessToken($accessToken)){

													$currentFbAccount = $fb_account->get($fb_account->UserDefaultFbAccount());

													if(	in_array($app_id,$appshaveRealfbid) && 
														$userData->id != $fb_account->UserDefaultFbAccount() && 
														trim($currentFbAccount->GetFirstname()) != trim($userData->first_name) && 
														trim($currentFbAccount->GetLastname()) != trim($userData->last_name)
														){
														echo "<div class='well'>".sprintf(lang("The current facebook account is %s and you are logged in on facebook as %s "),"<strong>".$currentFbAccount->GetFirstname() . " " . $currentFbAccount->GetLastname()."</strong>","<strong>".$userData->first_name." ".$userData->last_name." </strong>")."</div>";
													throw new Exception(lang("To avoid inserting the wrong access token make sure to logged into the same facebook account on facebook."));	
												} else {
													$fb_account->saveAT($app_id,$accessToken);
													echo "<script> alertBox('".lang('ACCESS_TOKEN_SAVED_SUCCESS')." <a href=\'#\' onclick=\'window.opener.location.href = window.opener.location.href;window.close();\'>".lang('Close this window')."</a>.','success','.messageBox',true);</script>";

													if(trim($currentFbAccount->GetFirstname()) != trim($userData->first_name) && trim($currentFbAccount->GetLastname()) != trim($userData->last_name)){	
														echo "<div class='well'>".sprintf(lang("The current facebook account is %s and you are logged in on facebook as %s "),"<strong>".$currentFbAccount->GetFirstname() . " " . $currentFbAccount->GetLastname()."</strong>","<strong>".$userData->first_name." ".$userData->last_name." </strong>")."</div>";
														echo "<div class='alert alert-danger'>".lang('To avoid inserting the wrong access token make sure to logged into the same facebook account on facebook.')."</div>";
													}
												}
											}else{
												throw new Exception(lang("Unable to get facebook account details, make sure that the access token is valid"));
											}
										}

									}catch(Exception $e){
										echo "<script> alertBox('".$e->GetMessage()."','danger','.messageBox',true);</script>";
									}
								}
							}
							?>
							<ol>
								<li>
									<button onclick="window.open('<?php echo $fbapp->getAppAuthLink(); ?>','','height=500,width=600'); return false;" class="btn btn-primary"><?php echo lang('AUTH_APP'); ?></button> <?php echo lang('SET_VISIBILITY_PUBLIC'); ?></li>
									<li>
										<?php if(in_array($app_id,$fromFBTool)): ?>
										<button onclick="window.open('https://developers.facebook.com/tools/debug/accesstoken/?app_id=<?php echo $app_id; ?>','','height=300,width=600'); return false;" class="btn btn-primary"><?php echo lang('GET_APP_AUTH_LINK'); ?></button>
										<?php echo lang('COPY_POPUP_LINK_IN_TEXT_EREA'); ?>
									<?php else: ?>
									<button onclick="window.open('data:text/html,<html><meta http-equiv=\'refresh\' content=\'0; url=view-source:<?php echo $fbapp->getAppAuthLink(); ?>\'></html>','','height=1,width=600'); return false;" class="btn btn-primary"><?php echo lang('GET_APP_AUTH_LINK'); ?></button>
									<input type="text" class="form-control" value="view-source:<?php echo trim($fbapp->getAppAuthLink()); ?>">
									<?php echo lang('COPY_POPUP_LINK_IN_TEXT_EREA'); ?>
								<?php endif; ?>
							</li>
						</ol>

						<?php

					}


				}

				if(!in_array($app_id,$fromFBTool)): 
					?>

				<textarea name='accessTokenURL' id='accessTokenURL' rows='3' cols='100' class="form-control" placeholder='<?php echo lang('PASTE_APP_AUTH_LINK'); ?>'></textarea>

			<?php endif; ?>
			<textarea name='accessToken' rows='3' cols='100' id="accessToken" class="form-control" placeholder='<?php echo lang('ENTER_ACCESS_TOKEN_HERE'); ?>'></textarea>
			<p>
				<input type='submit' class='btn btn-primary' name='submit' value='<?php echo lang('SET_ACCESS_TOKEN'); ?>'>
				<input type='button' class='btn btn-primary testAccessToken' value='<?php echo lang('TEST_ACCESS_TOKEN'); ?>'>
			</p>
			<script>
				$( document ).ready(function() {
					$('#accessTokenURL').bind('input propertychange', function() {
						var at = $(this).val().match(/access_token=(.*)(?=&expires_in)/);
						if(at){$("#accessToken").val(at[1]);}
					});

					$(".testAccessToken").click(function(){
						$.post( "ajax/accesstoken.php", {isAccessTokenValid:'true',accessToken:$("#accessToken").val()},function( data ) {
							if(data != "true"){
								alertBox('<?php echo lang('INVALID_ACCESS_TOKEN'); ?>','danger','.messageBox',true);
							}else{
								alertBox('<?php echo lang('ACCESS_TOKEN_IS_VALID'); ?>','success','.messageBox',true);
							}
						});
					});
				});
			</script>
		</div>
	</div>
</form>
</div>
</div>
<?php $template->footer(); ?>