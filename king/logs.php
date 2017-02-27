<?php  
include('core/init.php');

$logs = new Logs();
$user = new User();
$fbaccount = new FbAccount();

if(Input::get("action","GET") == "clear"){
	try{	
		$scheduleId = Input::get("scheduleid","GET") ? Input::get("scheduleid","GET") : null;
		Logs::Clear($scheduleId);
		Session::Flash("logs","success",lang('LOGS_CLEARED'),true);
	}catch(Exception $ex){
		Session::Flash("logs","danger",$ex->GetMessage(),true);
	}
	
	if($scheduleId)
		Redirect::To("logs.php?scheduleid=".$scheduleId);

	Redirect::To("logs.php");
}

$data = array();

if(Session::exists('logs')){
	foreach(Session::Flash('logs') as $error){
		$data['flash'][] = flash_bag($error['message'],$error['type'],true,false);
	}
}

if(Input::get("scheduleid","GET")){
	$data['logs'] = $logs->Get(Input::get("scheduleid","GET"));
	$data['clear_a'] = "logs.php?action=clear&scheduleid=".Input::get("scheduleid","GET");
}else{
	$data['logs'] = $logs->Get();
	$data['clear_a'] = "logs.php?action=clear";
}

$data['fbaccount'] = $fbaccount;
$data['user'] = $user;

$tpl = new Template();
$tpl->render("logs",$data);
