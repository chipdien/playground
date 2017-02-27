<?php 
class Facebook{
	
	private $_db = null;
	private $_groups = null;
	private $_app_id = null;
	private $_app_secret = null;
	private $error;
	
	/*
	|--------------------------------------------------------------------------
	| Set the accessToken
	| get user info 
	| get list of groups
	|--------------------------------------------------------------------------
	|
	*/
	public function __construct(){
		$this->_db = DB::GetInstance();
	}

	public static function GetUserFromAccessToken($accessToken){
		$curl = new Curl();
		$curl->Get("https://graph.facebook.com/me?&access_token=".$accessToken);

		if (!$curl->error) {
		    return $curl->response;
		}

		return null;
	}
	/*
	|--------------------------------------------------------------------------
	| Set the accessToken Check if the current acces token is valid
	|--------------------------------------------------------------------------
	|
	*/
	public function IsATValid($accessToken){
		if(empty($accessToken)) return false;
		$curl = new Curl();
		$curl->Get("https://graph.facebook.com/oauth/access_token_info?access_token=".$accessToken);
		if ($json = $curl->response) {
			if (isset($json->access_token)){
				return $json->access_token == "" ? false : true;
			}
		} 
		return false;
	}
	
	public function FbUserIdFromAt($accessToken){
		$curl = new Curl();
		$curl->get("https://graph.facebook.com/me?access_token=".$accessToken);
		if (isset($curl->response->id)){
			return $curl->response->id;
		}
		return false;
	}
	
	private function FbAppUserHasRole($FbUserId,$app_id,$app_secret){
		$curl = new Curl();
		$curl->get("https://graph.facebook.com/".$app_id."/roles?fields=user,role&access_token=".$app_id."|".$app_secret."&method=get");
		foreach($curl->response->data as $user){
			if($user->user == $FbUserId){
				return $user->role;
			}
		}
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Get the list of groups of the current user
	|--------------------------------------------------------------------------
	|
	*/ 
	public function LoadFbGroups($accessToken, $limit = 1000){
		$curl = new Curl();
		$curl->Get("https://graph.facebook.com/me/groups?fields=id,name,privacy&limit=".$limit."&access_token=".$accessToken);
		if(isset($curl->response->data)){
			if(empty($curl->response->data)){
				return true;
			}
			return $curl->response->data;
		}
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Get the list of pages of the current user
	|--------------------------------------------------------------------------
	|
	*/ 
	public function LoadFbPages($accessToken, $limit = 500,$loadPages,$loadOwnPages){
		
		$p = $limit > 99 ? $limit / 100 : 1;
		
		$limit = $limit > 100 ? 100 : $limit;

		$pages = array();
		$cursor = "";

		if($loadPages){
			for ($i=0; $i<$p ; $i++) {
				$curl = new Curl();
				$curl->Get("https://graph.facebook.com/me/likes?fields=id,name,likes,access_token&limit=".$limit.$cursor."&access_token=".$accessToken);
				if(isset($curl->response->data)){
					if(!empty($curl->response->data)){
						$pages = array_merge($pages,$curl->response->data);
						if(isset($curl->response->paging->cursors->after)){
							$cursor = "&after=".$curl->response->paging->cursors->after."&";
							continue;
						}
					}
				}

				break;
			}
		}


		if($loadOwnPages){
			for ($i=0; $i<$p ; $i++) {
				$curl = new Curl();
				$curl->Get("https://graph.facebook.com/me/accounts?fields=name,id,likes,access_token&limit=".$limit.$cursor."&access_token=".$accessToken);
				if(isset($curl->response->data)){
					if(!empty($curl->response->data)){
						$pages = array_merge($pages,$curl->response->data);
						if(isset($curl->response->paging->cursors->after)){
							$cursor = "&after=".$curl->response->paging->cursors->after."&";
							continue;
						}
					}
				}

				break;
			}
		}

		return $pages;
	}


	/*
	|--------------------------------------------------------------------------
	| Post to facebook group and return result
	| @return type array
	|--------------------------------------------------------------------------
	|
	*/ 
	public function Post($target,$params,$postType,$accessToken = null,$user = null,$fba = null){
		
		$graph = "https://graph.facebook.com/";
		$fbaccount = new fbaccount();

		$fbNodes = $fbaccount->GetPages(null,$user,$fba);

		$nodeHasAccessToken = false;

		// Check if the Node is a page and has access token
		for($i = 0; $i<count($fbNodes); $i++) {
			if($fbNodes[$i]['id'] == $target && isset($fbNodes[$i]['access_token'])){
				$params[] = "access_token=".$fbNodes[$i]['access_token'];
				$nodeHasAccessToken = true;
				break;
			}
		}
		if(!$nodeHasAccessToken){
			if($accessToken == null){
				$params[] = "access_token=".$this->GetAccessToken();
			}else{
				$params[] = "access_token=".$accessToken;
			}
		}

		$params[] = "method=post";
		switch ($postType) {
			case 'image':
				$edge = $target."/photos/";
				break;
			case 'video':
				$edge = $target."/videos/";
				$graph = "https://graph-video.facebook.com/";
				break;
			default:
				$edge = $target."/feed/";
				break;
		}
		// Generate the post link
		$postLink = $graph.$edge."?".implode("&",$params);

		$curl = new Curl();
		$curl->get($postLink);

		if ($curl->error) {
			$this->error = isset($curl->jsonResponse()->error->message) ? lang($curl->jsonResponse()->error->message) : lang($curl->curlErrorMessage);
			return false;
		}

		if ($json = $curl->response) {
			if (isset($json->error)){
				$this->error = lang($json->error->message);
				return false;
			} elseif (isset($json->id)){ 
				// get post id
				if($postType == 'image'){
					return substr(strrchr($json->post_id, '_'), 1);
				}else if($postType == 'video'){
					return $json->id;
				}else{
					return substr(strrchr($json->id, '_'), 1);
				}
			} else {
				// Mostly this could be a connection error if the script is on localserver
				$this->error = lang("An unexpected error occurred : Check your internet connection");
				return false;
			}
		} else {
			// Mostly this could be a connection error if the script is on localserver
			$this->error = lang("An unexpected error occurred : Check your internet connection");
			return false;
		}
	}
	
	public function AppDetails($app_id){
		$curl = new Curl();
		$curl->get("https://graph.facebook.com/".$app_id);
		
		if ($curl->error) 
			return false;

		return $curl->response;
	}
	
	public function AppDetailsFromAt($accessToken){
		$curl = new Curl();
		$curl->get("https://graph.facebook.com/app/?access_token=".$accessToken);
		if ($curl->error) {
			return false;
		}

		if(!isset($curl->response->error)){
			return $curl->response;
		}

		return false;
	}
	
	public static function App($app_id){
		$app = DB::GetInstance()->QueryGet("SELECT * FROM fbapps WHERE appid = ? ",array($app_id));
		if($app->count()){
			return $app->first();
		}
		return false;
	}
	
	private function FbAppAuth($app_id,$app_secret,$redirect,$oldApi){
		$fb = new Facebook\Facebook([
					'app_id' => $app_id,
					'app_secret' => $app_secret,
					'default_graph_version' => 'v2.4',
				]);

			$helper = $fb->getRedirectLoginHelper();
			
			try {
				$accessToken = $helper->getAccessToken();
			} catch(Facebook\Exceptions\FacebookResponseException $e) {
				// When Graph returns an error
				throw new Exception($e->getMessage());
				return false;
			} catch(Facebook\Exceptions\FacebookSDKException $e) {
				// When validation fails or other local issues
				throw new Exception($e->getMessage());
				return false;
			}

			if(Input::Get('state','GET') && Input::Get('code','GET')){
				return $accessToken;
			}else if(Input::Get('error_message')){
				throw new Exception(Input::Get('error_message','GET'));
			}{
				
				$perms = array();
				$perms[] = "publish_actions";
				$perms[] = "public_profile";
				if($oldApi == "true") $perms[] = "user_groups";
				Redirect::To($helper->getLoginUrl($redirect,$perms));
			}
	}
	
	public function FbAuth($app_id = null,$app_secret = null,$redirect = null,$oldApi = null){
	
		if($app_id == null || $app_secret == null || $redirect == null){
			throw new Exception(lang("Required parameters not supplied!"));
		}else{
			$user = new user();
			
			// Get admin access token
			$adminAccessToken = $this->_db->QueryGet("SELECT admin_access_token FROM fbapps  WHERE appid = ? ",array($app_id))->first()->admin_access_token;
			
			// Get app access token
			$accessToken = $this->FbAppAuth($app_id,$app_secret,$redirect,$oldApi);

			// Check if the access token is valid
			if($this->IsATValid($adminAccessToken)){

				$fb_account = new FbAccount();
				if($fb_account->UserDefaultFbAccount()){

					// Store user app info
					if($this->GetAccessToken($app_id)){
						$this->UpdateAccessToken($user->data()->id,$app_id,$fb_account->UserDefaultFbAccount(),$accessToken);
					}else{
						$this->SaveAccessToken($user->data()->id,$app_id,$fb_account->UserDefaultFbAccount(),$accessToken);
					}	
						
				}else{
					throw new Exception(lang('NO_FB_ACCOUNT_SELECTED'));
				}
				
				// Check if the user is an admin of the facebook app otherwise add him ass a tester
				if($this->FbAppUserHasRole($this->FbUserIdFromAt($accessToken),$app_id,$app_secret) != "administrators"){
					if(!$this->Invite($app_id,$this->FbUserIdFromAt($accessToken),$adminAccessToken)){
						throw new Exception(lang("Unable to add your facebook account as a tester."));
					}else{
						echo "<div class='alerts alert alert-info'>
						<p class='alerttext'>".lang('You will recive a developer requests, before you can post you must confirm the request.')."</p>
						<a href='https://developers.facebook.com/requests/' target='_blank'>https://developers.facebook.com/requests/.</a>
						</div>";
					}
				}
				
			}else if($user->HasPermission("admin")){

				// Check if the user is an admin of the facebook app
				if($this->FbAppUserHasRole($this->FbUserIdFromAt($accessToken),$app_id,$app_secret) === "administrators"){
					$fb_account = new FbAccount();
					if($fb_account->UserDefaultFbAccount()){

						// Store user app info
						if($this->GetAccessToken($app_id)){
							$this->UpdateAccessToken($user->data()->id,$app_id,$fb_account->UserDefaultFbAccount(),$accessToken);
						}else{
							$this->SaveAccessToken($user->data()->id,$app_id,$fb_account->UserDefaultFbAccount(),$accessToken);
						}	
							
					}else{
						throw new Exception(lang('NO_FB_ACCOUNT_SELECTED'));
					}

					// Store the app admin access token
					$this->_db->Update("fbapps","appid",$app_id,array("admin_access_token"=>$accessToken));
		
				}else{
					throw new Exception(lang("The admin must authorized this application first!"));
				}
			}else{
				throw new Exception(lang("The admin must authorized this application first!"));
			}
		} // End params check
	}
	
	public function Invite($app_id,$fbUserId,$accessToken){
		// Invite link
		$url = "https://graph.facebook.com/".$app_id."/roles?user=".$fbUserId."&role=testers&access_token=".$accessToken."&method=POST";
		$curl = new Curl();
		$curl->get($url);
		
		if ($curl->error) {
			return false;
		}

		if(isset($curl->response->success) && $curl->response->success  == true){
			return true;
		}

		return false;

	}
	
	public function SaveAccessToken($userId,$app_id,$fb_id,$accessToken){
		$access_token_date = date('Y-m-d H:i:s');
		$this->_db->Insert("user_fbapp",
			array(
				'userid' => $userId,
				'appid' => $app_id,
				'fb_id' => $fb_id,
				'access_token' => $accessToken,
				'access_token_date' => $access_token_date
			));
	}
	
	public function UpdateAccessToken($userId,$app_id,$fb_id,$accessToken){
		$access_token_date = date('Y-m-d H:i:s');
		$this->_db->Query("UPDATE user_fbapp set access_token = ? , access_token_date = '$access_token_date' WHERE userid  = ? AND appid = ? AND fb_id = ? ",array($accessToken,$userId,$app_id,$fb_id));
	}
	
	public function AppsList(){
		$fbapps = $this->_db->QueryGet("SELECT appid,app_name FROM fbapps");
		if($fbapps->count() != 0){
			return $fbapps->results();
		}
		return false;
	}
	
	public function DeleteApp($app_id){
		$user = new User();
		if($user->hasPermission("admin")){
			try{
				$this->_db->Delete("fbapps",array("appid","=",$app_id));
				$this->_db->Delete("user_fbapp",array("appid","=",$app_id));
			}catch(Exception $ex){
				throw new Exception("Could not delete the app \n Error details : ".$ex->GetMessage());
			}
		}else{
			throw new Exception("You don't have permission to perform this action.");
		}
	}
	
	public function deauthorizeApp($app_id){
		$user = new User();
		try{
			$this->_db->Query("DELETE FROM user_fbapp WHERE appid = ? AND userid = ? ",array($app_id,$user->data()->id));
		}catch(Exception $ex){
			throw new Exception("Could not deauthorize the app \n Error details : ".$ex->GetMessage());
		}
	}

	public function getAccessToken($app_id = null,$fb_id = null,$userId = null){
		$fbaccount = new FbAccount();
		$user = new User();
		
		if($userId == null){
			$userId = $user->data()->id;
		}
		
		if($fb_id == null){
			$fb_id = $fbaccount->UserDefaultFbAccount();
		}

		if($app_id == null){
			$app_id = $fbaccount->UserFbAccountDefaultApp();
		}

		$fbAT = $this->_db->QueryGet("SELECT access_token FROM user_fbapp WHERE userid = ? AND appid = ? AND fb_id = ? ",array($userId,$app_id,$fb_id));
		if($fbAT->count() != 0){
			return $fbAT->first()->access_token;
		}
		return false;
	}

	public function error(){
		return $this->error;
	}
}
?>