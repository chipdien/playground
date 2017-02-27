<?php
class Logs {
	
	public static function save($user_id,$scheduledPostsId,$content){
		$date = new DateTime();
		DB::getInstance()->insert("logs",
				array(
				'user_id' => $user_id,
				'scheduledposts' => $scheduledPostsId,
				'content' => $content,
				'date' => $date->format('Y-m-d H:i')
				)
			);
	}
	
	// Get logs
	public function get($id = null){
		$user = new user();
		if($id){
			return DB::getInstance()->QueryGet("SELECT * FROM logs WHERE scheduledposts = ? AND user_id = ? ORDER BY `date` DESC",array($id,$user->data()->id))->results();
		}else{
			return DB::getInstance()->QueryGet("SELECT * FROM logs WHERE user_id = ? ORDER BY `date` DESC",array($user->data()->id))->results();
		}
	}
	
	public static function Clear($scheduleId = null){
		$user = new user();
		if($scheduleId){
			return DB::GetInstance()->Query("DELETE FROM logs WHERE scheduledposts = ? AND user_id = ? ",array($scheduleId,$user->data()->id));
		}

		return DB::GetInstance()->Query("DELETE FROM logs WHERE user_id = ? ",array($user->data()->id));
	}
}
?>