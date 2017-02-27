<?php 
	require "core/init.php";
	$template = new template();

	$data = array();

	if(isset($_POST['submit'])){
		$accessToken = trim($_POST['accessToken']);
		if(empty($accessToken)){
			$data['flash'][]  = flash_bag(lang('ENTER_ACCESS_TOKEN'),'danger');
		}else{

			$user = new User();
			$fb = new Facebook();
			$fb_account = new FbAccount();
			$app_id = "145634995501895";

			try{

				if($fb_account->UserDefaultFbAccount()){
					if($fb->IsATValid($accessToken)){

						if($fb->GetAccessToken($app_id)){
							$fb->UpdateAccessToken($user->data()->id,$app_id,$fb_account->UserDefaultFbAccount(),$accessToken);
						}else{
							$fb->SaveAccessToken($user->data()->id,$app_id,$fb_account->UserDefaultFbAccount(),$accessToken);
						}

						$data['flash'][]  = flash_bag(lang('Access token has been updated succussfully'),'success');

					}else{
						throw new Exception(lang('INVALID_ACCESS_TOKEN'));
					}

				}else{
					throw new Exception(lang('NO_FB_ACCOUNT_SELECTED'));
				}
			}catch(Exception $e){
				$data['flash'][]  = flash_bag($e->getMessage(),'danger');
			}
		}
	}

$template->render("reset_access_token",$data); 
?>