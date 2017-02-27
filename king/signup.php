<?php
/*
|--------------------------------------------------------------------------
| Common init file
|--------------------------------------------------------------------------
|
*/
require_once "core/commoninit.php";
require_once "core/language/language.php";

$user = new user();
$template = new Template();

if($user->isLoggedIn()){
	Redirect::to(Options::Get("siteurl").'index.php');
	exit();
}

if(Options::Get('users_can_register') == "0"){
	Redirect::to(Options::Get("siteurl").'index.php');
	exit();
}

$data = array();

if(Input::Get("signup")){
  $validate = new Validate();
  $validation = $validate->check($_POST, array(
    'username' => array(
      'disp_text' => lang("USERNAME"),
      'required' => true,
      'min' => 2,
      'max' => 32,
      'unique' => 'users',
      'regex' => '/^[a-z0-9]+$/'
      ),
    'password' => array(
      'disp_text' => lang("PASSWORD"),
      'required' => true,
      'min' => 6,
      'max' => 16
      ),
    'repassword' => array(
      'disp_text' => lang("RE_ENTER_PASSWORD"),
      'required' => true,
      'matches' => 'password'
      ),
    'email' => array(
      'disp_text' => lang("EMAIL"),
      'required' => true,
      'unique' => 'users',
      'valid_email' => true
      ),
    ));

  if($validation->passed()){
    $user = new User();
    $salt = Hash::salt(32);
    try{

  	// Account activation
      if(Options::Get('user_active_by_admin') == '1' || Options::Get('users_must_confirm_email')  == '1'){
        $active = 0;
      }else{
        $active = 1;
      }

      $user->create(array(
        'username' => Input::get('username'),
        'password' => Hash::make(Input::get('password'), $salt),
        'salt' => $salt,
        'email' => Input::get('email'),
        'roles' => '3',
        'active' => $active,
        'signup' => date('Y-m-d H:i:s')
        ));

    // If the user is successfully registered
      if($newUser = $user->find(Input::get('username'))){

        $user->defaultSettings($user->data()->id);

        $registerSuccessMsg = lang('THANK_YOU_REGISTERING');

        if(Options::Get('user_active_by_admin') == "1"){
          $registerSuccessMsg .= "\n".lang('Your account is awaiting activation by the administration.');
        }else if(Options::Get('users_must_confirm_email') == "1"){
        // Generate activation code
          $code = Token::generate();
          $user->update(array('act_code' => $code),$user->data()->id);
          $registerSuccessMsg .= "\n".lang('THANK_YOU_REGISTERING_CONFIRMATION'); 
        // Send confirmation email
          $registerMessage = "Hello ".Input::get('username').",<br /><br />";
          $registerMessage .= "Thank you for registering with ".Options::get("sitename")."! To complete your registration, please click on the link below or paste it into a browser to confirm your e-mail address.<br/>";
          $registerMessage .= "<a href='".Options::Get("siteurl")."/confirmregistration.php?email=".Input::Get("email")."&code=".$code."' >".Options::Get("siteurl")."/confirmregistration.php?email=".Input::Get("email")."&code=".$code."</a>";
          $registerMessage .= "<br/><br/><br/>".lang("Please do not reply to this message.")."<br/>".Options::get("sitename");
          Mail::Send(Input::Get("email"),Options::get("sitename").lang(' Account activation'),$registerMessage);
        }

        // User message after the signing up
        Session::flash("signin","success",$registerSuccessMsg,true);
        Redirect::To("signin.php");

      }

    }catch(Exception $e){
		$data['flash'][] = flash_bag(lang("OPERATION_FAILED_TRY_AGAIN")."\n ".$e->GetMessage(),"danger",true,false);
    }
  }else{
    foreach($validation->errors() as $error){
      $data['flash'][] = flash_bag($error,"danger",true,false);
    }
  }
}

$template->render("signup",$data);
?>