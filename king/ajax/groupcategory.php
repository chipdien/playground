<?php 
header('Content-Type: application/json');
// Initial files
require "../core/init.php";

if(
	isset($_POST['category']) && 
	isset($_POST['groups']) &&
	isset($_POST['action']) && $_POST['action'] == "addgroup"
){
	
	$fbaccount = new fbaccount();
	$res = $fbaccount->addGroupToCategory(Input::get('groups'),Input::get('category'));
	
	if($res){
		echo json_encode(array(
			'status' => 'success',
			'message' => lang('Selected groups has been added to the category',true) 
		));
	}else{
		echo json_encode(array(
			'status' => 'error',
			'message' => $fbaccount->error()
		),JSON_PRETTY_PRINT);
	}
}

if(
	isset($_POST['category']) && 
	isset($_POST['groups']) &&
	isset($_POST['action']) && $_POST['action'] == "deletegroup"
){
	
	$fbaccount = new fbaccount();
	$res = $fbaccount->removeGroupFromCategory(Input::get('groups'),Input::get('category'));
	
	if($res){
		echo json_encode(array(
			'status' => 'success',
			'message' => lang('Selected groups has been deleted',true) 
		));
	}else{
		echo json_encode(array(
			'status' => 'error',
			'message' => $fbaccount->error()
		),JSON_PRETTY_PRINT);
	}
}

if(
	isset($_POST['categoryname']) && 
	isset($_POST['action']) && $_POST['action'] == "addcategory"
){
	
	$fbaccount = new fbaccount();
	$res = $fbaccount->addGroupCategory($_POST['categoryname']);
	if($res){
		echo json_encode(array(
			'status' => 'success',
			'message' => lang('CATEGORY_ADDED_SUCCESSFULLY',true) 
		));
	}else{
		echo json_encode(array(
			'status' => 'error',
			'message' => $fbaccount->error()
		),JSON_PRETTY_PRINT);
	}

}


?>