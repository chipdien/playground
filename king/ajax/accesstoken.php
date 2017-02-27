<?php 
require "../core/init.php";
	
if(Input::Get("isAccessTokenValid")){
	$fb = new Facebook();

	$accessToken = isset($_POST["accessToken"]) ? Input::Get("accessToken") : $fb->getAccessToken();

	if($fb->IsATValid($accessToken)){
		echo "true";
	}else{
		echo "true";
	}
}
?>