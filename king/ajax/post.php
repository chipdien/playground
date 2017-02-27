<?php
header('Content-Type: application/json');
require "../core/init.php";

if(!DEBUG_MODE){
	error_reporting(0);
}

if(isset($_POST["groupID"]) && isset($_POST["postType"])){

	$params = array();
	$user = new User();

	// Check is the user can post
	if($user->reachedMaxPostsPerDay()){
		echo json_encode(array(
			'status' => 'error',
			'message' => lang("You reached the maximum posts per day."),
		),JSON_PRETTY_PRINT);
		exit();
	}
	
	$fbaccount = new fbaccount();
	// Check if the user has a default app
	if(!$defaultApp = $fbaccount->UserFbAccountDefaultApp()){
		echo json_encode(array(
			'status' => 'error',
			'message' => lang("No application has been selected, Please go to the settings page and choose an application."),
		),JSON_PRETTY_PRINT);
		exit();
	}

	$fbapps = new fbapps();
	// Check if the user has a default app
	if(!$fbapps->get($defaultApp)){
		echo json_encode(array(
			'status' => 'error',
			'message' => lang("The default app not found!"),
		),JSON_PRETTY_PRINT);
		exit();
	}

	$fb = new Facebook();
	$spintax = new Spintax();
	if(Input::Get("message")){
		$message = $spintax->get(Input::Get("message"));
		// If is unique post enabled
		if(isset($user->Options()->uniquePost)){
			if($user->Options()->uniquePost == 1){
				$uniqueID = strtoupper(uniqid()); // Generate unique ID
				$message .= "\n\n". $uniqueID;
			}
		}

		$params[] = "message=".urlencode($message);
	}

	if(Input::Get("postType") == "link"){

		$link = $spintax->get(Input::Get("link"));

		// If is unique post link enabled
		if(isset($user->Options()->uniqueLink)){
			if($user->Options()->uniqueLink == 1){
				$uniqueID = strtoupper(uniqid()); // Generate unique ID
				if (strpos($link, '?') !== false) {
					$link = rtrim($link, "/")."&post_".$uniqueID."=".Input::get("groupID");
				}else{
					$link = rtrim($link, "/")."/?post_".$uniqueID."=".Input::get("groupID");
				}
			}
		}

		$params[] = "link=".urlencode($link);

		if(Input::Get("picture")) $params[] = "picture=".urlencode($spintax->get(Input::Get("picture")));
		if(Input::Get("name")) $params[] = "name=".urlencode($spintax->get(Input::Get("name")));
		if(Input::Get("caption")) $params[] = "caption=".urlencode($spintax->get(Input::Get("caption")));
		if(Input::Get("description")) $params[] = "description=".urlencode($spintax->get(Input::Get("description")));

	}else if (Input::Get("postType") == "image") {
		$params[] = "url=".$spintax->get(Input::Get("image"));
	}else if (Input::Get("postType") == "video") {
		$params[] = "file_url=".$spintax->get(Input::Get("file_url"));
		if(Input::Get("message")) $params[] = "title=".urlencode($spintax->get(Input::Get("message")));
		if(Input::Get("descriptionVideo")) $params[] = "description=".urlencode($spintax->get(Input::Get("descriptionVideo")));
	}


	if($result = $fb->Post(Input::get("groupID"),$params,Input::Get("postType"))){
		echo json_encode(array(
			'status' => 'success',
			'id' => $result,
		),JSON_PRETTY_PRINT);
		$user->updateOptions(array('today_num_posts' => $user->options()->today_num_posts+1));
	} else {
		echo json_encode(array(
			'status' => 'error',
			'message' => $fb->error(),
		),JSON_PRETTY_PRINT);
	}

}else{
	echo json_encode(array('error' => lang('EMPTY_REQUEST')),JSON_PRETTY_PRINT);
}
?>