<?php  
include('core/init.php');

$user = new User();
$fb = new Facebook();
$template = new Template();
$fbaccount = new fbaccount();
$fbapps = new FbApps();

// Switch facebook account request 
if(Input::Get("switchFbAccount")){
	if($fbaccount->exists(Input::Get("switchFbAccount"))){
		$user->UpdateOptions(array('default_Fb_Account' => Input::Get("switchFbAccount")));
		if(!httpReferer()){
			Redirect::To("settings.php");
		}
		Redirect::To(httpReferer());
	}
}

// Delete facebook account					
if(Input::get("action","GET") == "deletefbaccount" && Input::get("id","GET")){
	try{
		$fbaccount->delete(Input::get("id","GET"));
		Session::Flash("settings","success",lang("FB_ACCOUNT_SUCCESS_DELETED"),true);
	}catch(Exception $ex){
		Session::Flash("settings","danger",$ex->GetMessage(),true);
	}
	
	Redirect::To("settings.php#tab-fbAccounts");
}

// Delete facebook app					
if(Input::get("action","GET") == "deletefbapp" && Input::get("id","GET")){
	try{
		$fb->DeleteApp(Input::get("id","GET"));	
	}catch(Exception $ex){
		Session::Flash("settings","danger",$ex->GetMessage(),true);
	}
	
	Redirect::To("settings.php#tab-fbApps");
}

// Deauthorize
if(Input::get("action","GET") == "deauthorize" && Input::get("id","GET")){
	try{
		$fb->DeauthorizeApp(Input::get("id","GET"));
		Session::Flash("settings","success",lang('APP_DEAUTH_SUCCESS'),true);
		
	}catch(Exception $ex){
		echo $ex->GetMessage();
		Session::Flash("settings","danger",$ex->GetMessage(),true);
	}

	Redirect::To("settings.php#tab-fbApps");
}

if(Input::get('save')){
	
	$validate = new Validate();
	
	$validation = $validate->check($_POST, array(
			'postInterval' => array(
				'disp_text' => lang('POST_INTERVAL'),
				'required' => true,
			),
			'language'=> array(
				'disp_text' => lang('LANGUAGE'),
				'required'	=> true,
				'inArray' 	=> Language::GetAvailableLangs()
			)
	));
	
	if(Input::Get("email") != $user->data()->email){
		$validation = $validate->check($_POST, array(
				'email' => array(
					'disp_text' => lang('EMAIL'),
					'required' => true,
					'unique' => 'users',
					'valid_email' => true,
					)
		));
	}
	
	if(Input::Get("password")){
		$validation = $validate->check($_POST, array(
				'password' => array(
					'disp_text' => lang('PASSWORD'),
					'min' => '6',
					'max' => '32',
					),
				'repassword' => array(
					'disp_text' => lang('RE_ENTER_PASSWORD'),
					'required' => true,
					'matches' => 'password',
					)
		));
	}

	if($validation->passed()){
		try{
			$db = DB::GetInstance();
			
			$openGroupOnly = Input::Get("openGroupOnly") == "on" ? 1 : 0;
			$uniquePost = Input::Get("uniquePost") == "on" ? 1 : 0;
			$uniqueLink = Input::Get("uniqueLink") == "on" ? 1 : 0;
			$loadGroups = Input::Get("loadGroups") == "on" ? 1 : 0;
			$loadPages = Input::Get("loadPages") == "on" ? 1 : 0;
			$loadOwnPages = Input::Get("loadOwnPages") == "on" ? 1 : 0;

			$user->UpdateOptions(array(
				'postInterval' 	=> Input::Get("postInterval"),
				'openGroupOnly'	=> $openGroupOnly,
				'lang'			=> Input::Get("language"),
				'uniquePost'	=> $uniquePost,
				'uniqueLink'	=> $uniqueLink,
				'timezone'		=> Input::Get("timezone"),
				'limitImportGroups'	=> Input::Get("limitImportGroups"),
				'limitImportPages'	=> Input::Get("limitImportPages"),
				'load_groups'	=> $loadGroups,
				'load_pages'	=> $loadPages,
				'load_own_pages'	=> $loadOwnPages,
			));

			// Update the default app for the current facebook account
			if($fb->App(Input::Get('postApp'))){
				$fbaccount->updateDefaultApp(Input::Get('postApp'));
			}

			// Update email
			if(Input::Get("email")){
				$user->update(array('email' => Input::get('email')),$user->data()->id);
			} 
			
			// Update facebook user id 
			if(Input::Get("fbuserid")){
				$user->update(array('fbuserid' => Input::get('fbuserid')),$user->data()->id);
			}

			// Update firstname
			if(Input::Get("firstname")){
				$user->update(array('firstname' => Input::get('firstname')),$user->data()->id);
			} 

			// Update lastname
			if(Input::Get("lastname")){
				$user->update(array('lastname' => Input::get('lastname')),$user->data()->id);
			} 
			
			// Update password
			if(Input::Get("password")){
				$salt = Hash::salt(32);
				$user->update(array('password' => Hash::make(Input::get('password'), $salt),"salt" => $salt),$user->data()->id);
			}
			
			Session::Flash("settings","success",lang('SETTINGS_UPDATED_SUCCESS'),true);

		}catch(Exception $e){
			echo $e->getMessage();
		}
	}else{
		Session::Flash("settings","danger","<ul><li>".implode("</li><li>",$validation->errors())."</li></ul>",false);
	}
}

$template->header("Settings");

if(Session::exists('settings')){
		foreach(Session::Flash('settings') as $error){
			echo "<div class='alert alert-".$error['type']."' role='alert'>";
			echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
			echo $error['message'];
			echo "</div>";
		}
}

?>
<div class="messageBox"></div>
<form method='POST' action='' class="settings" novalidate>
	<div class="row">
		<div class="tabbable tabs-left">
			<div class="col-xs-3">
				
					<?php $tabs = array();

					$tabs[] = array(
						'id' => 'userSettings',
						'icon' => 'user',
						'text' => lang('Profile'),
						'class' => 'active',
						'file' => 'user_settings'
					);

					$tabs[] = array(
						'id' => 'postingSettings',
						'icon' => 'clipboard',
						'text' => lang('POSTING_SETTINGS'),
						'class' => '',
						'file' => 'posting_settings'
					);

					$tabs[] = array(
						'id' => 'fbAccounts',
						'icon' => 'facebook',
						'text' => lang('FB_ACCOUNTS'),
						'class' => '',
						'file' => 'blockfbaccount/fbaccounts'
					);

					$tabs[] = array(
						'id' => 'fbApps',
						'icon' => 'plug',
						'text' => lang('FB_APPS'),
						'class' => 'fbapps',
						'file' => 'fbapps'
					);

					if($user->hasPermission("admin")){
						$tabs[] = array(
							'id' => 'generalSettings',
							'icon' => 'tasks',
							'text' => lang('GENERAL_SETTINGS'),
							'class' => '',
							'file'	=> 'general_settings'
						);

						$tabs[] = array(
							'id' => 'roles',
							'icon' => 'users',
							'text' => lang('Roles'),
							'class' => 'roles',
							'file' => 'blockroles/roles'
						);
					}

					echo '<ul class="nav nav-tabs">';
					foreach ($tabs as $tab) {
						echo "<li class='".$tab['class']."'><a href='#tab-".$tab['id']."' data-toggle='tab'><i class='fa fa-fw fa-".$tab['icon']."'></i> ".$tab['text']."</a></li>";
					}
					echo "</ul>";
					?>

					
			</div>
			<div class="col-xs-9">
				<div class="tab-content">
					<?php 
						foreach ($tabs as $tab) {
							echo "<div class='tab-pane ".$tab['class']."' id='tab-".$tab['id']."'>";
							include ABSPATH . "modules/settings/".$tab['file'].".php";
							echo "</div>";
						}
					?>
					<input type="submit" name="save" value="<?php echo lang('SAVE_CHANGES'); ?>" class="btn btn-primary" />
				</div>
			</div>
		</div>
	</div>
</form>
<?php $template->footer(); ?>