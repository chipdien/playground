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

$data = array();

if(Session::exists('signin')){
	foreach(Session::Flash('signin') as $error){
		$data['flash'][] = flash_bag($error['message'],$error['type'],true,false);
	}
}

if(Input::Get("signin")){
	$validate = new Validate();
	$validation = $validate->check($_POST, array(
	  'username' => array(
	    'disp_text' => lang('USERNAME'),
	    'required' => true
	    ),
	  'password' => array(
	    'disp_text' => lang('PASSWORD'),
	    'required' => true
	    )
	  ));

	if($validation->passed()){

	  $user = new User();

	  $remember = Input::get('remember') == "on" ? true : false;

	  try{
	    $login = $user->login(Input::get('username'), Input::get('password'),$remember);
	    Redirect::To("index.php");
	  }catch(Exception $ex){
	    $data['flash'][] = flash_bag($ex->GetMessage(),"danger",true,false);
	  }

	}else{
	  $data['flash'][] = flash_bag(lang("ENTER_USERNAME_PASSWORD"),"danger",true,false);
	}
}

$template->render("signin",$data);

?>