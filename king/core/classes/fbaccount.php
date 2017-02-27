<?php 
class fbaccount {
	
	private $userid = null;
	private $fbid = null;
	private $firstname = null;
	private $lastname = null;
	private $defaultApp = null;
	private $groups = null;
	private $pages = null;
	private $totalGroups = 0;
	private $totalPages = 0;
	private $error;
	
	// Userid setter and getter
	public function setUserId($userid){
		$this->userid = $userid;
	}
	
	public function getUserId(){
		return $this->userid;
	}

	// fbid  setter and getter
	public function setFbid($fbid){
		$this->fbid = $fbid;
	}

	public function getFbid(){
		return $this->fbid;
	}

	// Firstname setter and getter
	public function setFirstname($firstname){
		$this->firstname = $firstname;
	}

	public function getFirstname(){
		return $this->firstname;
	}

	// Lastname  setter and getter
	public function setLastname($lastname){
		$this->lastname = $lastname;
	}

	public function getLastname(){
		return $this->lastname;
	}

	// Groups  setter and getter
	public function setDefaultApp($defaultApp){
		$this->defaultApp = $defaultApp;
	}

	public function getDefaultApp(){
		return $this->defaultApp;
	}

	// Groups  setter and getter
	public function setGroups($groups){
		$this->groups = $groups;
	}

	// Pages  setter and getter
	public function setPages($pages){
		$this->pages = $pages;
	}
	
	// Save current instance
	public function save(){
		try{

			if( $this->userid == null || $this->fbid == null ) 
				throw new Exception("User ID and facebook ID can not be empty.");
			

			$fields = array();

			$fields["user_id"] = $this->userid;
			$fields["fb_id"] = $this->fbid;
			$fields["firstname"] = $this->firstname;
			$fields["lastname"] =  $this->lastname;
			$fields["groups"] = $this->groups;
			$fields["pages"] = $this->pages;

			if($this->defaultApp)
				$fields["defaultApp"] = $this->defaultApp;

			DB::getInstance()->Insert("fb_accounts",$fields);

		}catch(Exception $e){
			throw new Exception($e);
		}
	}

	// Update current instance
	public function update(){
		try{

			if( $this->userid == null || $this->fbid == null ) 
				throw new Exception("User ID and facebook ID can not be empty.");

			DB::getInstance()->Query("UPDATE fb_accounts SET 
				firstname = ? ,
				lastname = ?
				WHERE user_id = ? AND fb_id = ?
			",array(
				$this->firstname,
				$this->lastname,
				$this->userid,
				$this->fbid)
			);

			if($this->pages != null){
				DB::getInstance()->Query("UPDATE fb_accounts SET pages = ? WHERE user_id = ? AND fb_id = ?
				",array($this->pages,$this->userid,$this->fbid));
			}

			if($this->groups != null){
				DB::getInstance()->Query("UPDATE fb_accounts SET groups = ? WHERE user_id = ? AND fb_id = ?
				",array($this->groups,$this->userid,$this->fbid));
			}

		}catch(Exception $e){
			throw new Exception($e);
		}
	}


	public function get($fbid,$userid = null){

		if($userid == null){
			$user = new User();
			$userid = $user->data()->id;
		}

		$f = new fbaccount();

		$fbAccount = DB::GetInstance()->QueryGet("SELECT * FROM fb_accounts WHERE user_id = ? AND fb_id = ? ",array($userid,$fbid));

		if($fbAccount->count()){
			
			$f->userid = $fbAccount->first()->user_id;
			$f->fbid = $fbAccount->first() ->fb_id;
			$f->firstname = $fbAccount->first() ->firstname;
			$f->lastname = $fbAccount->first() ->lastname;
			$f->groups = $fbAccount->first() ->groups;
			$f->pages = $fbAccount->first() ->pages;
			$f->defaultApp = $fbAccount->first() ->defaultApp;

		}

		return $f;
	}


	public function  getAll($userid = null){

		if($userid == null){
			$user = new User();
			$userid = $user->data()->id;
		}

		$fba = new ArrayObject();	
		
		$fbAccount = DB::GetInstance()->QueryGet("SELECT user_id,fb_id,firstname,lastname FROM fb_accounts WHERE user_id = ? ",array($userid));

		if($fbAccount->count()){
			foreach($fbAccount->results() as $f){
				$tempfb = new fbaccount();
				$tempfb->userid = $f->user_id;
				$tempfb->fbid = $f->fb_id;
				$tempfb->firstname = $f->firstname;
				$tempfb->lastname = $f->lastname;
				$fba[] = $tempfb;
			}
		}

		return $fba;
	}

	public function delete($id){
		
		$user = new User();
		
		// Delete the facebook account is exists
		if($this->exists($id)){
			
			// Delete the account
			DB::GetInstance()->query("DELETE FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array(
				$id,
				$user->data()->id
			));

			// Delete the account apps
			DB::GetInstance()->query("DELETE FROM user_fbapp WHERE fb_id = ? AND userid = ? ",array(
				$id,
				$user->data()->id
			));

			// Remove the account from user options if it is the default account
			if($this->UserDefaultFbAccount() == $id){
				$user->UpdateOptions(array("default_Fb_Account"=>""));
			}

			return true;
		}
		throw new Exception(lang("FB_ACCOUNT_NOT_EXISTS"));
	}

	public function exists($fbid,$userid = null){

		if($userid == null){
			$user = new User();
			$userid = $user->data()->id;
		}

		$fbAccount = DB::GetInstance()->QueryGet("SELECT * FROM fb_accounts WHERE user_id = ? AND fb_id = ? ",array($userid,$fbid));

		return $fbAccount->count();
	}


	public function UserDefaultFbAccount(){
		$user = new User();

		if(isset($user->Options()->default_Fb_Account)){
			if(trim($user->Options()->default_Fb_Account)){
				return $user->Options()->default_Fb_Account;
			}
		}

		return false;
	}

	public function UserFbAccountDefaultApp(){
		$user = new User();

		if(isset($user->Options()->default_Fb_Account)){
			if(trim($user->Options()->default_Fb_Account)){
				$query = "SELECT defaultApp FROM fb_accounts WHERE user_id = ? AND fb_id = ? ";
				$result = DB::GetInstance()->QueryGet($query,array($user->data()->id,$user->Options()->default_Fb_Account));
				if($result->first()){
					return $result->first()->defaultApp;
				}
			}
		}

		return false;
	}


	// Update current facebook default app
	public function updateDefaultApp($app){

		try{

			if($this->UserDefaultFbAccount()){
				$user = new user();
				$query = "UPDATE fb_accounts SET defaultApp = ? WHERE user_id = ? AND fb_id = ? ";
				DB::getInstance()->Query($query,
					array(
						$app,
						$user->data()->id,
						$this->UserDefaultFbAccount()
					)
				);
			}

		}catch(Exception $e){
			throw new Exception($e);
		}
	}
	/*
	|--------------------------------------------------------------------------
	| get the list of categories
	|--------------------------------------------------------------------------
	|
	|
	*/ 
	public function GetGroupCategories($fbAccount){
		$user = new User();
		return DB::GetInstance()->QueryGet("SELECT id,category_name FROM groups_category WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id))->results();
		
	}
	/*
	|--------------------------------------------------------------------------
	| get the list of groups of the current user
	|--------------------------------------------------------------------------
	|
	|
	*/ 
	public function GetGroups($category = null){
		$user = new User();
		$fbAccount = $this->UserDefaultFbAccount();
		$groups = null;

		if($category){
			$groups = DB::GetInstance()->QueryGet("SELECT groups FROM groups_category WHERE id = ? AND fb_id = ? AND user_id = ? ",array($category,$fbAccount,$user->data()->id));
		}else{
			if(Session::exists("groupscategory")){
				if(Session::get("groupscategory") != -1){
					if( $this->currentFbAccountHasCat(Session::get("groupscategory")) ){
						$groups = DB::GetInstance()->QueryGet("SELECT groups FROM groups_category WHERE id = ? AND fb_id = ? AND user_id = ? ",array(
							Session::get("groupscategory"),
							$fbAccount,
							$user->data()->id
						));
					}else{
						$groups = DB::GetInstance()->QueryGet("SELECT groups FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
					}
					
				}else{
					$groups = DB::GetInstance()->QueryGet("SELECT groups FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
				}

			}else{
				$groups = DB::GetInstance()->QueryGet("SELECT groups FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
			}
		}

		if(isset($groups->first()->groups)){

			$listGroups = json_decode($groups->first()->groups,true);

			// Check show open group only option is on unset non open groups
			if(isset($user->Options()->openGroupOnly)){
				if($user->Options()->openGroupOnly){
					$listGroups = $this->unsetNoneOpenGroups($listGroups);
				}
			}
			
			$this->totalGroups = count($listGroups);
			return $listGroups;

		}
			
		return false;
	}
	/*
	|--------------------------------------------------------------------------
	| get the list of pages of the current user
	|--------------------------------------------------------------------------
	|
	|
	*/ 
	public function GetPages($category = null,$user = null,$fbAccount = null){
		
		if($user == null){
			$user = new User();
		}

		if($fbAccount == null){
			$fbAccount = $this->UserDefaultFbAccount();
		}

		$pages = null;

		if($category){
			$pages = DB::GetInstance()->QueryGet("SELECT pages FROM groups_category WHERE id = ? AND fb_id = ? AND user_id = ? ",array($category,$fbAccount,$user->data()->id));
		}else{
			if(Session::exists("groupscategory")){
				if(Session::get("groupscategory") != -1){
					if( $this->currentFbAccountHasCat(Session::get("groupscategory")) ){
						$pages = DB::GetInstance()->QueryGet("SELECT pages FROM groups_category WHERE id = ? AND fb_id = ? AND user_id = ? ",array(
							Session::get("groupscategory"),
							$fbAccount,
							$user->data()->id
						));
					}else{
						$pages = DB::GetInstance()->QueryGet("SELECT pages FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
					}
					
				}else{
					$pages = DB::GetInstance()->QueryGet("SELECT pages FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
				}

			}else{
				$pages = DB::GetInstance()->QueryGet("SELECT pages FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
			}
		}

		if(isset($pages->first()->pages)){
			$listPages = json_decode($pages->first()->pages,true);
			$this->totalPages = count($listPages);
			return $listPages;
		}
			
		return false;
	}
	/*
	|--------------------------------------------------------------------------
	| get the list of groups of the current user
	|--------------------------------------------------------------------------
	|
	|
	*/ 
	public function GetGroupsAndPages($category = null,$user = null){
		$user = $user == null ? new User() : $user;
		
		$fbAccount = $this->UserDefaultFbAccount();

		if(
			$category && $category != -1 || 
			(
				Session::exists("groupscategory") &&
				Session::get("groupscategory") != -1 &&
				$this->currentFbAccountHasCat(Session::get("groupscategory"))
			)
		){
			$category = $category ? $category : Session::get("groupscategory") ;
			$queryResult = DB::GetInstance()->QueryGet("SELECT groups,pages FROM groups_category WHERE id = ? AND fb_id = ? AND user_id = ? ",array($category,$fbAccount,$user->data()->id));
		}else{
			$queryResult = DB::GetInstance()->QueryGet("SELECT groups,pages FROM fb_accounts WHERE fb_id = ? AND user_id = ? ",array($fbAccount,$user->data()->id));
		}
		
		if($queryResult->first() != null){

			$groups = array();
			$pages = array();

			$queryResult = $queryResult->first();

			if(isset($queryResult->groups) && $queryResult->groups != null){
				$groups = json_decode($queryResult->groups,true);
				if(isset($user->Options()->openGroupOnly)){
					if($user->Options()->openGroupOnly){
						$groups = $this->unsetNoneOpenGroups($groups);
					}
				}
			}

			if(isset($queryResult->pages) && $queryResult->pages != null){
				$pages = json_decode($queryResult->pages,true);
			}

			$this->totalGroups = count($groups);
			$this->totalPages = count($pages);

			$nodes = array_merge($groups,$pages);

			usort($nodes, make_comparer(['name', SORT_ASC]));

			return $nodes;

		}
	
		return false;
	}

	/*
	|--------------------------------------------------------------------------
	| Count number of groups
	|--------------------------------------------------------------------------
	|
	*/
	public function GroupsCount(){
		return $this->totalGroups;
	}
	/*
	|--------------------------------------------------------------------------
	| Count number of pages
	|--------------------------------------------------------------------------
	|
	*/
	public function PagesCount(){
		return $this->totalPages;
	}
	
	private function unsetNoneOpenGroups($listGroups){
		$i = 0;
		foreach ($listGroups as $group) {
			if(isset($group['privacy'])){
				if($group['privacy'] != 'OPEN') {
					unset($listGroups[$i]);
				}
			}
			$i++;
		}

		return $listGroups;
	}
	/*
	|--------------------------------------------------------------------------
	| Add new group category
	|--------------------------------------------------------------------------
	|
	*/
	public function addGroupCategory($name){
		$user = new User();
		$fbAccount = $this->UserDefaultFbAccount();

		if(trim($name) == ""){
			$this->error = lang('CATEGORY NAME CAN NOT BE EMPTY');
			return false;
		}

		if(!preg_match("/^[\p{L}\p{M}\p{Nd} ]{2,}$/u", $name)){
			$this->error = lang("Category name must contain alphanumeric characters underscore and space only");
			return false;
		}

		if(strlen($name) > 20){
			$this->error = lang("Category name is too long");
			return false;
		}

		if($this->isGroupCategoryExistsByName($name)){
			$this->error = lang("A category with same_name is already");
			return false;
		}

		try{
			DB::getInstance()->Insert("groups_category",array(
				'user_id' 	=> $user->data()->id,
				'fb_id' 	=> $fbAccount,
				'category_name'=> $name
			));
			return true;
		}catch(Exception $ex){
			$this->error = $ex->GetMessage();
			return false;
		}
	}

	/*
	|--------------------------------------------------------------------------
	| Remove group from category
	|--------------------------------------------------------------------------
	|
	*/
	public function removeGroupFromCategory($groups,$category){
		
		$user = new User();

		if($category == -1){
			$this->error = lang("Can not remove groups from the main category.");
			return false;
		}

		if(!$this->isGroupCategoryExists($category)){
			$this->error = "Category not Exists.";
			return false;
		}

		if(!is_array($groups)){
			$this->error = "Invalid list of groups, expected array, '". gettype($groups) . "' given Instead.";
			return false;
		}

		if($currentList = $this->GetGroups($category)){
			$currentListTotlaGroups = count($currentList);
			$currentList = array_values($currentList);
			for($i = 0; $i<count($groups); $i++) {
				for($j = 0; $j<$currentListTotlaGroups;$j++) {
					if(in_array($groups[$i], $currentList[$j])){
						unset($currentList[$j]);
						$currentList = array_values($currentList);
						$currentListTotlaGroups--;
						break;
					}
				}
			}

			try{
				DB::getInstance()->query("UPDATE `groups_category` SET `groups` = ? WHERE id = ? AND user_id = ? ",array(
						json_encode($currentList),$category,$user->data()->id,)
					);
				return true;
			}catch(Exception $ex){
				throw new Exception($ex->GetMessage());
				return false;
			}
		}

		return false;
	}
	/*
	|--------------------------------------------------------------------------
	| Add group to category
	|--------------------------------------------------------------------------
	|
	*/
	public function addGroupToCategory($nodes,$category){
		$user = new User();

		if(!$this->isGroupCategoryExists($category)){
			$this->error = "Category not Exists";
			return false;
		}

		if(!is_array($nodes)){
			$this->error = "Invalid list of groups, expected array, '". gettype($nodes) . "' given Instead.";
			return false;
		}

		$newGroups = array();
		$newPages = array();
		$nodeBaseList = array_values($this->GetGroupsAndPages());

		if(is_array($nodes) && count($nodes) != 0){
			if(is_array($nodeBaseList) && count($nodeBaseList) != 0){
				for($i = 0; $i<count($nodes); $i++) {
					for($j = 0; $j<count($nodeBaseList);$j++) {
						if(in_array($nodes[$i], $nodeBaseList[$j])){
							if(isset($nodeBaseList[$j]['privacy'])){
								$newGroups[] = $nodeBaseList[$j];
							}else{
								$newPages[] = $nodeBaseList[$j];
							}
							break;
						}
					}
				}
			}else{
				$this->error = "Could not load the list of groups and pages";
				return false;
			}
		}else{
			$this->error = "Invalid value supplied";
			return false;
		}

		
		$categoryGroups = $newGroups;
		$categoryPages = $newPages;

		// Merge groups 
		if($currentList = $this->GetGroups($category)){
			$categoryGroups = array_merge_recursive($newGroups,$currentList);
		}

		// Merge groups 
		if($currentList = $this->GetPages($category)){
			$categoryPages = array_merge_recursive($newPages,$currentList);
		}

		// Remove duplicated groups
		$categoryGroups = array_map("unserialize", array_unique(array_map("serialize", $categoryGroups)));
		$categoryPages = array_map("unserialize", array_unique(array_map("serialize", $categoryPages)));

		try{
			DB::getInstance()->query("UPDATE `groups_category` SET `groups` = ? WHERE id = ? AND user_id = ? ",array(
					json_encode($categoryGroups),$category,$user->data()->id,)
				);
			DB::getInstance()->query("UPDATE `groups_category` SET `pages` = ? WHERE id = ? AND user_id = ? ",array(
					json_encode($categoryPages),$category,$user->data()->id,)
				);
		}catch(Exception $ex){
			$this->error = $ex->GetMessage();
			return fase;
		}

		return true;
	}
	/*
	|--------------------------------------------------------------------------
	| Get group category
	|--------------------------------------------------------------------------
	|
	*/
	public function isGroupCategoryExists($category){
		$user = new User();
		return DB::GetInstance()->QueryGet("SELECT id FROM groups_category WHERE id = ? AND user_id = ? ",array($category,$user->data()->id))->count() == 0 ? false : true;
		
	}
	/*
	|--------------------------------------------------------------------------
	| Get group category
	|--------------------------------------------------------------------------
	|
	*/
	public function isGroupCategoryExistsByName($category){
		$user = new User();
		$fbAccount = $this->UserDefaultFbAccount();
		return DB::GetInstance()->QueryGet("SELECT category_name FROM groups_category WHERE category_name = ? AND user_id = ? AND fb_id = ? ",array($category,$user->data()->id,$fbAccount))->count() == 0 ? false : true;
		
	}
	/*
	|--------------------------------------------------------------------------
	| Current fb account has category
	|--------------------------------------------------------------------------
	|
	*/
	public function currentFbAccountHasCat($category){
		$user = new User();
		$fbAccount = $this->UserDefaultFbAccount();
		return DB::GetInstance()->QueryGet("SELECT id FROM groups_category WHERE id = ? AND user_id = ? AND fb_id = ? ",array(
			$category,
			$user->data()->id,
			$fbAccount)
		)->count() == 0 ? false : true;
	}

	/*
	|--------------------------------------------------------------------------
	| Delete category
	|--------------------------------------------------------------------------
	|
	*/
	public function deleteCategory($category){
		$user = new User();
		return DB::GetInstance()->Query("DELETE FROM groups_category WHERE id = ? AND user_id = ? ",array($category,$user->data()->id));
		
	}

	/*
	|--------------------------------------------------------------------------
	| Send error
	|--------------------------------------------------------------------------
	|
	*/
	public function error(){
		return $this->error;
	}

	public function countFbAccount($userId = null){
		if($userId){
			return DB::GetInstance()->QueryGet("SELECT COUNT(*) as 'count' FROM fb_accounts WHERE user_id = ? ",array($user_id))->first()->count;
		}else{
			$user = new User();
			return DB::GetInstance()->QueryGet("SELECT COUNT(*) as 'count' FROM fb_accounts WHERE user_id = ? ",array($user->data()->id))->first()->count;
		}
	}

	public function getUserNodes(){
		// Get list of user Fb Nodes
		$user = new User();

		if(!isset($user->Options()->show_groups) && !isset($user->Options()->show_pages)){
			$user->UpdateOptions(array('show_groups' => 1));
			return $this->GetGroups();
		}

		if($user->Options()->show_groups == 1 && $user->Options()->show_pages == 1){
			return $this->GetGroupsAndPages();
		}


		if($user->Options()->show_pages == 1){
			return $this->GetPages();
		}

		$user->UpdateOptions(array('show_groups' => 1));
		return $this->GetGroups();
	}

	public function saveAT($app_id,$accessToken){
		$fb = new Facebook();
		$user = new User();
		if($fb->GetAccessToken($app_id)){
			$fb->UpdateAccessToken($user->data()->id,$app_id,$this->UserDefaultFbAccount(),$accessToken);
		}else{
			$fb->SaveAccessToken($user->data()->id,$app_id,$this->UserDefaultFbAccount(),$accessToken);
		}
	}
}
?>