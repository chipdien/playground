<?php 
header('Content-Type: application/json');
// Initial files
require "../core/init.php";

if(
	isset($_POST['roleid']) && 
	isset($_POST['action']) && $_POST['action'] == "update"
){
	
	$roles = new Roles();
	
	$checkRole = $roles->getRolebyId((int)Input::get("roleid"));

	if(!$checkRole){
		echo json_encode(array(
			'status' => 'error',
			'message' => lang('Role not found',true)
		));
		exit();
	}

	if($checkRole->getName() == "admin"){
		echo json_encode(array(
			'status' => 'error',
			'message' => lang('Can not update the admin role',true)
		));
		exit();
	}

	$roles->setId((int)Input::get("roleid"));

	if(
		Input::get("maxPostsPerDay",'POST') && 
		is_int((int)Input::get("maxPostsPerDay")) && 
		(int)Input::get("maxPostsPerDay") >= 0 &&
		(int)Input::get("maxPostsPerDay") <= 5000
		){

		$roles->setMaxPostsPerDay((int)Input::get("maxPostsPerDay"));
	}

	if(
		Input::get("maxFbAccounts",'POST') && 
		is_int((int)Input::get("maxFbAccounts")) && 
		(int)Input::get("maxFbAccounts") >= 0 &&
		(int)Input::get("maxFbAccounts") <= 50
		){

		$roles->setMaxFbAccounts((int)Input::get("maxFbAccounts"));
	}

	$result = $roles->update();

	if($result){
		echo json_encode(array(
			'status' => 'success',
			'message' => lang('Role has been updated',true)
		));
	}else{
		echo json_encode(array(
			'status' => 'error',
			'message' => $roles->getError()
		),JSON_PRETTY_PRINT);
	}

}


?>