<?php 
// set the headers origin
header('Access-Control-Allow-Origin: *');  
header('Content-Type: application/json');

//error_reporting(0);

$json = array(
	'status' => 'error',
	'message' => 'Empty response'
);

// Initial files
require "../core/init.php";

// Get user information
if(Input::Get("schedule_id") && Input::Get("action") == "post"){
	
	if(!Input::Get("repeat_every")){
		$json['status'] = "error";
		$json['message'] = lang("The repeat interval must be specified");
		echo json_encode($json);
		exit();
	}

	if(!Input::Get("repeat_at")){
		$json['status'] = "error";
		$json['message'] = lang("The repeat date and time must be specified");
		echo json_encode($json);
		exit();
	}

	$user = new User();
	$scheduledposts = new scheduledposts();
	$schedule = $scheduledposts->get((int)Input::Get("schedule_id"));

	if(!$schedule){
		$json['status'] = "error";
		$json['message'] = lang("Schedule not found!");
		echo json_encode($json);
		exit();
	}

	try {
    	$currentDateTime = new DateTime(Input::Get("repeat_at") . " " .Input::Get("start_at"));
	} catch (Exception $e) {
		$json['status'] = "error";
		$json['message'] = "Invalid date or time";
		echo json_encode($json);
		exit();
	}

	

	if($schedule->status = 1){
		$data = array(
			(int)Input::Get("repeat_every"),
			$currentDateTime->format("Y-m-d H:i"),
			$currentDateTime->format("Y-m-d H:i"),
			$user->data()->id,
			(int)Input::Get("schedule_id"),
		);
		$query = "UPDATE scheduledposts set status = 0, next_target = 0, repeat_every = ? , repeated_at = ? , next_post_time = ? WHERE userid = ? AND id = ? ";
	}else{
		$data = array(
			(int)Input::Get("repeat_every"),
			$currentDateTime->format("Y-m-d H:i"),
			$user->data()->id,
			(int)Input::Get("schedule_id")
		);
		$query = "UPDATE scheduledposts set repeat_every = ? , repeated_at = ? WHERE userid = ? AND id = ? ";
	}

	if(DB::GetInstance()->query($query,$data)){
		$json['status'] = "success";
		$json['message'] = lang("Schedule has been updated successfully");
	}
}

echo json_encode($json);

?>