<?php  
include('core/init.php');

$posts = new Posts();
$template = new Template();
					
if(Input::get("action","GET") == "delete" && Input::Get("id","GET")){
	try{
		$posts->delete(Input::Get("id","GET"));
		Session::Flash("posts","success",lang('POST_DELETED_SUCCESS'),true);
	}catch(Exception $ex){
		Session::Flash("posts","danger",$ex->GetMessage(),true);
	}
	
	Redirect::To("posts.php");
}

$template->header("Posts");
$data = array();

if(Session::exists('posts')){
	foreach(Session::Flash('posts') as $error){
		$data['flash'][] = flash_bag($error['message'],$error['type'],true,false);
	}
}

$posts = new Posts();

$data['posts'] = $posts->get();

$template->render("posts",$data);
$template->footer();

?>
