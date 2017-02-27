<?php if (!defined('ABSPATH')) exit('No direct script access allowed');

require ABSPATH ."application/helpers/cron_helper.php";

$cronHelper = new cronHelper();

// If the schedule is already in progress exit
if(!$cronHelper->lock()){
	exit();
}

// Get posts that have status 0 (not completed) and pause = 0 and date <= current date
$scheduledposts = new scheduledposts();

$fb = new Facebook();
$posts = new Posts();
$spintax = new Spintax();


foreach($scheduledposts->post() as $scheduled){
	runSchedule($scheduledposts,$scheduled,$fb,$posts,$spintax);
}


// Repeat every
foreach($scheduledposts->autoRepeat() as $scheduled){
	$user = new User($scheduled->userid);

	// Set user timezone if the user defined the timezone
	if(isset($user->Options()->timezone)){
		if($user->Options()->timezone){
			date_default_timezone_set($user->Options()->timezone);
		}
	}

	// Get current time 
	$currentDateTime = new DateTime();

	if($scheduled->repeat_every == -1){
		$data = array(
			$currentDateTime->format("Y-m-d H:i"),
			$currentDateTime->format("Y-m-d H:i"),
			$scheduled->id,
			
		);

		DB::GetInstance()->query("UPDATE scheduledposts set next_target = 0, repeat_every = 0, status = 0 , repeated_at = ?, next_post_time = ? WHERE id = ? ",$data);
		continue;
	}

	$lastRepeated = new DateTime($scheduled->repeated_at);
	$lastRepeated->modify('+'.$scheduled->repeat_every.' day');

	if(strtotime($currentDateTime->format("Y-m-d H:i")) < strtotime($lastRepeated->format("Y-m-d H:i"))){
		continue;
	}

	$newlastRepeated = new DateTime();
	$newlastRepeated->setTime($lastRepeated->format("H"), $lastRepeated->format("i"));

	$data = array(
		$newlastRepeated->format("Y-m-d H:i"),
		$currentDateTime->format("Y-m-d H:i"),
		$scheduled->id,
	);
	DB::GetInstance()->query("UPDATE scheduledposts set next_target = 0, status = 0 , repeated_at = ?, next_post_time = ? WHERE id = ? ",$data);
}

$cronHelper->unlock();

function runSchedule($scheduledposts,$scheduled,$fb,$posts,$spintax){
	$user = new User($scheduled->userid);

	// Set user timezone if the user defined the timezone
	if(isset($user->Options()->timezone)){
		if($user->Options()->timezone){
			date_default_timezone_set($user->Options()->timezone);
		}
	}

	// Check if the post date <= current datetime of the user
	// Get current time 
	$currentDateTime = new DateTime();
	$next_post_time = new DateTime($scheduled->next_post_time);

	if(strtotime($currentDateTime->format("Y-m-d H:i")) < strtotime($next_post_time->format("Y-m-d H:i"))){
		return false;
	}

	$user->resetNumPosts($user);

	// Check is the user can post
	if($user->reachedMaxPostsPerDay()){
		// Set user timezone if the user defined the timezone
		if(isset($user->Options()->timezone)){
			if($user->Options()->timezone){
				date_default_timezone_set($user->Options()->timezone);
			}
		}
		logs::Save($scheduled->userid,$scheduled->id,lang("You reached the maximum posts per day."));
		DB::GetInstance()->update("scheduledposts","id",$scheduled->id,array("pause" => "1"));
		return false;
	}
	
	// Set user timezone if the user defined the timezone
	if(isset($user->Options()->timezone)){
		if($user->Options()->timezone){
			date_default_timezone_set($user->Options()->timezone);
		}
	}

	// get the post 
	$post = $posts->GetPost($scheduled->post_id);

	// Post is ready
	if(count($post) == 0){
		logs::Save($scheduled->userid,$scheduled->id,lang('POST_NOT_FOUND'));
		DB::GetInstance()->update("scheduledposts","id",$scheduled->id,array("pause" => "1"));
		return false;
	}

	// Get app accessToken
	$accessToken = $fb->getAccessToken($scheduled->post_app,$scheduled->fb_account,$scheduled->userid);

	// Test access token
	if(!$fb->IsATValid($accessToken)){
		logs::Save($scheduled->userid,$scheduled->id,lang('INVALID_ACCESS_TOKEN'));
		DB::GetInstance()->update("scheduledposts","id",$scheduled->id,array("pause" => "1"));
		Session::Flash("scheduledPosts","danger",lang("One or more schedules has been paused due to Invalid access token."),true);
		return false;
	}

	// Send the post
	$params = array();	
	
	// Post param
	$postParam = json_decode($post->content);
	$postType = Posts::PostType($post->content);

	// Get list of groups
	$groups = json_decode($scheduled->targets,true);

	$message = $spintax->get($postParam->message);

	// If is unique post enabled
	if(isset($user->Options()->uniquePost)){
		if($user->Options()->uniquePost == 1){
			$uniqueID = strtoupper(uniqid()); // Generate unique ID
			$message .= "\n\n". $uniqueID;
		}
	}

	if($postParam->message != "") 	$params[] = "message=".urlencode($message);
	
	if($postType == "link"){		
		$link = $spintax->get($postParam->link);
		// If is unique post enabled
		if(isset($user->Options()->uniqueLink)){
			if($user->Options()->uniqueLink == 1){
				$uniqueID = strtoupper(uniqid()); // Generate unique ID
				if (strpos($link, '?') !== false) {
					$link = rtrim($link, "/")."&post_".$uniqueID."=true";
				}else{
					$link = rtrim($link, "/")."/?post_".$uniqueID."=true";
				}
			}
		}

		$params[] = "link=".urlencode($link);
		if($postParam->picture != "") 	$params[] = "picture=".urlencode($spintax->get($postParam->picture));
		if($postParam->name != "") 		$params[] = "name=".urlencode($spintax->get($postParam->name));
		if($postParam->caption != "") 	$params[] = "caption=".urlencode($spintax->get($postParam->caption));
		if($postParam->description != "") $params[] = "description=".urlencode($spintax->get($postParam->description));
	}

	if($postType == "image"){
		$params[] = "url=".urlencode($spintax->get($postParam->image));
	}
	
	if($postType == "video"){
		$params[] = "file_url=".urlencode($spintax->get($postParam->video));
		if($postParam->descriptionVideo != "") $params[] = "description=".urlencode($spintax->get($postParam->descriptionVideo));
	}
	
	// Send post and get the result
	$target = isset($groups[$scheduled->next_target]['id']) ? $groups[$scheduled->next_target]['id'] : $groups[$scheduled->next_target];

	$result = $fb->Post($target,$params,$postType,$accessToken,$user,$scheduled->fb_account);
	
	// Save log
	if($result != false){
		logs::Save($scheduled->userid,$scheduled->id,"<a href='https://www.facebook.com/".$result."' target='_blank'><span class='glyphicon glyphicon-ok'></span> ".lang('VIEW_POST')." </a>",$groups[$scheduled->next_target]);
		$user->updateOptions(array('today_num_posts' => $user->options()->today_num_posts+1));
	}else{
		logs::Save($scheduled->userid,$scheduled->id,$fb->error()." <a href='https://www.facebook.com/".$groups[$scheduled->next_target]."' target='_blank'><span class='glyphicon glyphicon-eye-open'></span> ".lang('Visit the node')." </a>",$groups[$scheduled->next_target]);
	}

	// Check if the current target is the last one
	if($scheduled->next_target+1 >= count($groups)){
		// This was the last target
		DB::GetInstance()->update("scheduledposts","id",$scheduled->id,array("status" => "1"));
	}else{
		// Update the scheduled
		$currentDateTime->modify("+".$scheduled->post_interval." minutes");
		$next_post_time = $scheduledposts->autoPause($scheduled);

		if(!$next_post_time){
			$next_post_time = $currentDateTime->format('Y-m-d H:i');
		}

		// Set the next target
		DB::GetInstance()->update("scheduledposts","id",$scheduled->id,array(
			"next_target" => $scheduled->next_target+1,
			"next_post_time" => $next_post_time,
		));

	}
}
?>