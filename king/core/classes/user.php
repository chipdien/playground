<?php  if (!defined('ABSPATH')) exit('No direct script access allowed');
class User{
	private $_db,
			$_data,
			$_sessionName,
			$_cookieName,
			$_isLoggedIn;
	
	public function __construct($user = null){
		
		$this->_db = DB::getInstance();
		$this->_sessionName = "kp_".Config::get('session/session_name');
		$this->_cookieName = Config::get('remember/cookie_name');
		
		if(!$user){
			if(Session::exists($this->_sessionName)){
				$user = Session::get($this->_sessionName);

				if($this->find($user)){
					$this->_isLoggedIn = true;
				}
			}
		}else{
			$this->find($user);
		}
	}
	
	public function create($fields = array()){
		if(!DB::getInstance()->insert('users', $fields)){
			throw new Exception ('There was a problem creating an account.');
		}
	}
	
	public function defaultSettings($userId,$settings = array()){
		$settings['postInterval'] 	= 30;
		$settings['openGroupOnly']	= 0;
		$settings['lang']			= DEFAULT_LANG;
		$settings['uniquePost']		= 0;
		$settings['uniqueLink']		= 0;
		$settings['timezone']		= DEFAULT_TIMEZONE;
		$settings['limitImportGroups']	= 500;
		$settings['limitImportPages']	= 500;
		$settings['show_groups']	= 1;
		$settings['show_pages']	= 1;
		$settings['today_num_posts'] = 0;
		$settings['load_groups'] = 1;
		$settings['load_pages'] = 1;
		$settings['load_own_pages'] = 0;
		$this->UpdateOptions($settings,$userId);
	}

	public function update($fields = array(), $id = null){
		if(!$id && $this->isLoggedIn()){
			$id = $this->data()->id;
		}
		if(!$this->_db->update('users', 'id',$id, $fields)){
			throw new Exception ('There was a problem updating your details.');
		}
	}

	public function find($user = null){
		if($user){
			$field = (is_numeric($user)) ? 'id' : 'username';
			$data = $this->_db->get('users', array($field, '=', $user));
			if($data->count()){
				$this->_data = $data->first();
				return true;
			}
		}
		return false;
	}

	public function login($username = null, $password = null, $remember = false){
		if(!$username && !$password && $this->exists()){
			Session::put($this->_sessionName, $this->data()->id);
			return true;
		}else{
			if($username && $password){
				$user = $this->find($username);
				if($user){
					if($this->data()->password === Hash::make($password, $this->data()->salt)){
						// Check if the user account is activated
						if((int)$this->data()->active == 0 && trim($this->data()->act_code) == ""){
							throw new Exception(lang('Your account is not activated'));
							return false;
						}
						// Check if the user account is activated
						if((int)$this->data()->active == 0){
							throw new Exception(lang('ACCOUNT_INACTIVATED'));
							return false;
						}
						Session::put($this->_sessionName, $this->data()->id);
						if($remember){
							$hash = Hash::unique();
							$hashCheck = $this->_db->get('users_session', array('user_id', '=', $this->data()->id));

							if(!$hashCheck->count()){
								$this->_db->insert('users_session', array(
									'user_id' => $this->data()->id,
									'hash' => $hash
									));
							}else{
								$hash = $hashCheck->first()->hash;
							}

							Cookie::put($this->_cookieName, $hash, Config::get('remember/cookie_expiry'));
						}
						
						return true;
					}
				}
			}
		}
		throw new Exception("Incorrect Username/password.");
		return false;
	}

	public function hasPermission($key){
		
		// check if the data object is not empty
		if($this->data()){
			$group = $this->_db->get('roles', array('id', '=', $this->data()->roles));
			if($group->count()){
				$permissions = json_decode($group->first()->permissions, true);
				
				if(isset($permissions[$key])){
					if($permissions[$key]){
						return true;
					}
				}
			}
		}
		
		return false;
	}

	public function exists(){
		return (!empty($this->_data)) ? true : false; 
	}
	
	public function emailExists($email){
		return $this->_db->queryGet("SELECT * FROM users WHERE email=?",array($email))->count() ? true : false;
	}
	
	public function username($username = null){
		if($username){
			return $this->_db->queryGet("SELECT * FROM users WHERE username= ? ",array($username))->count() ? true : false;
		}
		return false;
	}
	
	public function getUserById($id){
		return DB::getInstance()->queryGet("SELECT * FROM users WHERE id= ? ",array($id))->first();
	}
	
	public function logout(){
		$this->_db->query('DELETE FROM users_session WHERE user_id = ? ', array($this->data()->id));
		Session::delete($this->_sessionName);
		Cookie::delete($this->_cookieName);
	}

	public function data(){
		return $this->_data;
	}

	public function options(){
		if($this->exists()){
			$userId = $this->data()->id;
			if ( $result = DB::getInstance()->QueryGet("SELECT * FROM user_options WHERE userid = $userId")){
				return $result->first();
			}
		}else{
			$user = new user();
			if($user->isLoggedIn()){
				$userId = $user->data()->id;
				if ( $result = DB::getInstance()->QueryGet("SELECT * FROM user_options WHERE userid = $userId")){
					return $result->first();
				}
			}
		}
		return null;
	}
	
	public function UpdateOptions($params,$userId = null){
		$userId = $userId != null ? $userId : $this->data()->id;
		$query = $this->_db->queryGet("SELECT id FROM user_options WHERE userid = ? ",array($userId));	
		if($query->count() != 0){
			return $this->_db->Update("user_options","userid",$userId,$params);
		}else{
			$params["userid"] = $userId;
			return $this->_db->Insert("user_options",$params);
		}
	}
	
	public function UserCode($email,$code){
		$query = DB::getInstance()->queryGet("SELECT id,username FROM users WHERE email = ? AND act_code = ? ",array($email,$code));	
		if($query->count() != 0){
			return $query->first();
		}
		return false;
	}
	
	public function UserResetPWCode($email,$code){
		$query = DB::getInstance()->queryGet("SELECT id,username FROM users WHERE email = ? AND reset_pw_code = ? ",array($email,$code));	
		if($query->count() != 0){
			return $query->first();
		}
		return false;
	}

	public function GetUsers($offset = 0,$limit = 50,$active = null){
		if($active){
			$query = DB::getInstance()->queryGet("SELECT * FROM users WHERE active = 1 ORDER by id DESC LIMIT ? , ? ",array($offset,$limit));
		}else{
			$query = DB::getInstance()->queryGet("SELECT * FROM users ORDER by id DESC LIMIT ? , ? ", array($offset,$limit));
		}
		if($query->count() != 0){
			return $query->results();
		}
		return false;
	}

	public function countUsers($active = null){
		if($active){
			$query = DB::getInstance()->queryGet("SELECT * FROM users WHERE active = 1 ");
		}else{
			$query = DB::getInstance()->queryGet("SELECT * FROM users");
		}
		return $query->count();
	}
	
	public function Delete($userId){
		if($this->data()->id != $userId){
			try{
				$this->_db->Delete("users",array("id","=",$userId));
				$this->_db->Delete("user_options",array("userid","=",$userId));
				$this->_db->Delete("user_fbapp",array("userid","=",$userId));
			}catch(Exception $ex){
				throw new Exception("Could not delete the user \n Error details : ".$ex->GetMessage());
			}
		}else{
			throw new Exception("You can not delete the current user.");
		}
	}
	
	public function activate($userId){
		if($this->data()->id != $userId){
			try{
				$this->update(array('active' => "1"),$userId);
			}catch(Exception $ex){
				throw new Exception("Could not activate the user \n Error details : ".$ex->GetMessage());
			}
		}else{
			throw new Exception("You can not change admin status.");
		}
	}
	
	public function deactivate($userId){
		if($this->data()->id != $userId){
			try{
				$this->update(array('active' => "0"),$userId);
				DB::GetInstance()->query("UPDATE scheduledposts set pause = 1 WHERE userid = ? ",array($userId));
			}catch(Exception $ex){
				throw new Exception("Could not deactivate the user \n Error details : ".$ex->GetMessage());
			}
		}else{
			throw new Exception("You can not change admin status.");
		}
	}
	
	public function isLoggedIn(){
		return $this->_isLoggedIn;
	}

	public function reachedMaxFbAccounts(){
		$roles = new Roles();
		$fbaccount = new FbAccount();
		$role = $roles->getRoleById($this->data()->roles);

		if($role->getMaxFbAccounts() == 0 || $fbaccount->countFbAccount() < $role->getMaxFbAccounts()){
			return false;
		}

		return true;
	}

	public function reachedMaxPostsPerDay(){
		$roles = new Roles();
		$role = $roles->getRoleById($this->data()->roles);

		if($role == false || $role == null){
			return true;
		}

		if($role->getMaxPostsPerDay() == 0){
			return false;
		}

		$this->resetNumPosts();

		if($role->getMaxPostsPerDay() > $this->Options()->today_num_posts ){
			return false;
		}

		return true;
	}

	public function resetNumPosts($user = null){
		// Set the timezone
		date_default_timezone_set('UTC');

		// Get user
		if($user == null){
			$user = $this;
		}

		// check the last reset time
		$currentDateTime = new DateTime();
		$lastNumPostsReset = new DateTime($user->options()->last_num_posts_reset);

		if(!isset($user->options()->last_num_posts_reset) || $user->options()->last_num_posts_reset == null){
			$user->UpdateOptions(array(
				'today_num_posts' => 0,
				'last_num_posts_reset' => $currentDateTime->format('Y-m-d H:i') 
			));
			return true;
		}

		if(strtotime($currentDateTime->format("Y-m-d")) > strtotime($lastNumPostsReset->format("Y-m-d"))){
			$user->UpdateOptions(array(
				'today_num_posts' => 0,
				'last_num_posts_reset' => $currentDateTime->format('Y-m-d H:i') 
			));
			return true;
		}

		return false;
	}

}
?>