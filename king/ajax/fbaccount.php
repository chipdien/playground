<?php
require "../core/init.php";

$json = array(
	'status' => 'error',
	'message' => 'Empty response'
);

// Add new facebook account using access token
if(Input::Get("fb_accesstoken")){
	
	$fb = new Facebook();
	$user = new User();
	$fbaccount = new fbaccount();
	$errors = array();

	// Test access token
	if(!$fb->IsATValid(Input::Get("fb_accesstoken"))){
		echo lang('INVALID_ACCESS_TOKEN');
		exit();
	}

	// get facebook User info 
	$userData = $fb->GetUserFromAccessToken(Input::Get("fb_accesstoken"));
	if($userData == null){
		echo lang("UNABLE_TO_GET_FB_ACCOUNT_DETAILS");
		exit();
	}

	// Check if this facebook account is already exists;
	$fbAccountExists = $fbaccount->exists($userData->id);
	if(!$fbAccountExists){
		// Check if the user reached the the max number of fb accounts
		if($user->reachedMaxFbAccounts()){
			echo lang("You reached the max facebook accounts allowed");
			exit();
		}
	}

	// Get user groups
	if(isset($user->options()->load_groups) && $user->options()->load_groups == 1){
		$groupsLimit = isset($user->options()->limitImportGroups) && $user->options()->limitImportGroups != null ? $user->options()->limitImportGroups : 500;
		$fbgroups = $fb->LoadFbGroups(Input::Get("fb_accesstoken"),$groupsLimit);
		
		if(!$fbgroups){
			$errors[] = lang("UNABLE_GET_FB_GROUPS");
		}else{
			if(is_array($fbgroups)){
				$fbaccount->setGroups(json_encode($fbgroups));
			}
		}
	}

	// Get user liked pages
	$loadPages = isset($user->options()->load_pages) && $user->options()->load_pages == 1;
	$loadOwnPages = isset($user->options()->load_own_pages) && $user->options()->load_own_pages == 1;

	if($loadPages || $loadOwnPages){
		$pagesLimit = isset($user->options()->limitImportPages) && $user->options()->limitImportPages != null ? $user->options()->limitImportPages : 500;
		$fbpages = $fb->LoadFbPages(Input::Get("fb_accesstoken"),$pagesLimit,$loadPages,$loadOwnPages);
		if(!$fbpages){
			$errors[] = lang("UNABLE_GET_FB_PAGES");
		}else{
			if(is_array($fbpages)){
				$fbaccount->setPages(json_encode($fbpages));
			}
		}
	}

	// Save access token
	if($fbAppDetails = $fb->AppDetailsFromAt(Input::Get("fb_accesstoken"))){
		if($fb->GetAccessToken($fbAppDetails->id,$userData->id)){
			$fb->UpdateAccessToken($user->data()->id,$fbAppDetails->id,$userData->id,Input::Get("fb_accesstoken"));
		}else{
			$fb->SaveAccessToken($user->data()->id,$fbAppDetails->id,$userData->id,Input::Get("fb_accesstoken"));
		}
	}else{
		$errors[] = lang("UNABLE_TO_GET_FB_APP_DETAILS");
	}

	// Save new facebook account
	$fbaccount->setUserId($user->data()->id);
	$fbaccount->setFbId($userData->id);
	$fbaccount->setLastname($userData->last_name);
	$fbaccount->setFirstname($userData->first_name);

	if(!$fbaccount->UserFbAccountDefaultApp()){
		$fbaccount->setDefaultApp($fbAppDetails->id);
	}

	// Check if this facebook account is already exists;
	if($fbaccount->exists($userData->id)){
		$fbaccount->Update();
	}else{
		$fbaccount->Save();
	}

	// Set the current account as the default fb account if there is no default account
	if(!$fbaccount->UserDefaultFbAccount()){
		$user->UpdateOptions(array('default_Fb_Account' => $userData->id));
	}

	if(empty($errors)){
		echo "true";
	}else{
		echo "<ul>";
		foreach ($errors as $error) {
			echo "<li>".$error."</li>";
		}
		echo "</ul>";
	}
}

?>