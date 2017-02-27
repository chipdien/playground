<?php  if (!defined('ABSPATH')) exit('No direct script access allowed');

class scheduledposts {
	private $db = null;

	public function __construct(){
		$this->db = DB::getInstance();
	}
	
	// Get scheduled posts
	public function get($id = null){
		if($id){
			return $this->db->QueryGet("SELECT * FROM scheduledposts WHERE id = ? ",array($id))->first();
		}else{
			return $this->db->QueryGet("SELECT * FROM scheduledposts ORDER BY id DESC")->results();
		}
	}

	// Get user scheduled posts
	public function userPosts($id = null){
		$user = new User();
		if($id){
			return $this->db->QueryGet("SELECT * FROM scheduledposts WHERE id = ? AND userid = ? ",array($id,$user->data()->id))->first();
		}else{
			return $this->db->QueryGet("SELECT * FROM scheduledposts WHERE userid = ? ORDER BY id DESC",array($user->data()->id))->results();
		}
	}
	
	// Save scheduled posts
	public static function save($params){
		return DB::Getinstance()->Insert("scheduledposts",$params);
	}
	
	// Get posts that have status 0 (not completed) and pause = 0 and next post date <= current date
	public function post(){
		return $this->db->QueryGet("SELECT * FROM scheduledposts WHERE status = 0 AND pause = 0 ")->results();
	}

	// Auto repeat
	public function autoRepeat(){
		return $this->db->QueryGet("SELECT * FROM scheduledposts WHERE status = 1 AND pause = 0 AND repeat_every <> 0")->results();
	}

	// Delete scheduled posts
	public function delete($id){
		$user = new User();
		$this->db->Query("DELETE FROM scheduledposts WHERE id = ? AND  userid = ? ",array($id,$user->data()->id));
		$this->db->Query("DELETE FROM logs WHERE scheduledposts = ? AND  user_id = ? ",array($id,$user->data()->id));
	}

	public function autoPause($schedule){
		$ap = json_decode($schedule->auto_pause);
		if(isset($ap->pause) && $ap->pause != null && $ap->pause != 0){
			if($ap->pause_after == 0){
				$ap->pause_after = $ap->pause-1;
				$currentDateTime = new DateTime();
				$currentDateTime->modify("+".$ap->resume*60+rand(0,10)." minutes");
				DB::GetInstance()->update("scheduledposts","id",$schedule->id,array("auto_pause" => json_encode($ap)));
				return $currentDateTime->format('Y-m-d H:i');
			}else{
				$ap->pause_after = $ap->pause_after-1;
				DB::GetInstance()->update("scheduledposts","id",$schedule->id,array("auto_pause" => json_encode($ap)));
			}
		}

		return false;
	}
}
?>