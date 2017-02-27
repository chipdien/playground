<?php
require "core/init.php";
$template = new template();
$fb = new Facebook();
$user = new User();
$fbaccount = new fbaccount();

if(!$fbaccount->UserDefaultFbAccount()){
	Session::Flash("home","warning",lang('NO_FB_ACCOUNT_AVAILABLE'),true);
}

$fbaccountDetails = $fbaccount->get($fbaccount->UserDefaultFbAccount());

if($_SERVER['REQUEST_METHOD'] == 'POST'){

	if(Input::get('groupscategory')){
		Session::put("groupscategory",(int)Input::get('groupscategory'));
	}

	$user->UpdateOptions(array(
		'show_groups' => Input::Get("showGroups") == "on" ? 1 : 0,
		'show_pages' => Input::Get("showPages") == "on" ? 1 : 0,
	));

	if(Input::get('removeGroup')){
		try{
			$fbaccount->removeGroupFromCategory(Input::get('removeGroup'));
			Session::Flash("home","success",lang('GROUP_RMOVED_SUCCESS'),true);
		}catch(Exeption $ex){
			Session::Flash("home","danger",$ex->getMessage(),true);
		}
		Redirect::to('index.php');
	}

	if(Input::get('deleteCategory')){
		try{
			$fbaccount->deleteCategory(Input::get('deleteCategory'));
			Session::Flash("home","success",lang('CATEGORY_DELETED_SUCCESS'),true);
		}catch(Exeption $ex){
			Session::Flash("home","danger",$ex->getMessage(),true);
		}
		Redirect::to('index.php');
	}
}

// Get default app
if(!$fbaccount->UserFbAccountDefaultApp()){
	Session::Flash("home","warning",lang('NO_APP_SELECTED'),true);
}

$userFbNodes = $fbaccount->getUserNodes();

// Get list of categories
$groupsCategories = $fbaccount->GetGroupCategories($fbaccount->UserDefaultFbAccount());

$template->header("Home");

if(Session::exists('home')){
	foreach(Session::Flash('home') as $error){
		echo "<div class='alert alert-".$error['type']."' role='alert'>";
		echo "<a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>";
		echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
		echo "&nbsp;".$error['message'];
		echo "</div>";
	}
}

if(Session::exists('home_update')){
	foreach(Session::Flash('home_update') as $error){
		echo "<div class='alert alert-".$error['type']." update_msg' role='alert'>";
		echo "<a href='#' class='close' data-dismiss='alert' onclick='updatemsgdismiss();' aria-label='close'>&times;</a>";
		echo "<span class='glyphicon glyphicon-exclamation-sign' aria-hidden='true'></span>";
		echo "&nbsp;".$error['message'];
		echo "</div>";
	}
}

?>
<div class="homeMessageBox"></div>
<form method='POST' action id="postForm" name="postForm">
<div class="row">
    <div class="col-sm-6">
    <?php
    	include "modules/home/blockpostform/form.php"; 
    ?>
    </div>
	<div class="col-sm-6">
    <?php
    	include "modules/home/blockpostpreview/preview.php"; 
    ?>
    </div>
</div>
<?php
	include "modules/home/blockpostingdetails/details.php";
	include "modules/home/blockgroups/groupstable.php";
?>
</form>
<?php
$template->footer();
?>	
