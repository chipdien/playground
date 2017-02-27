<?php
class posts {
	private $db = null;

	public function __construct(){
		$this->db = DB::getInstance();
	}
	
	public function get($id = null){
		$user = new user();
		if($id){
			return $this->db->QueryGet("SELECT * from posts where id = ? and userid = ? ",array($id,$user->data()->id))->first();
		}else{
			return $this->db->QueryGet("SELECT * from posts where userid = ? ORDER BY id DESC",array($user->data()->id))->results();
		}
	}
	
	public function getPost($id = null){
		if($id){
			return $this->db->QueryGet("SELECT * from posts where id = ? ",array($id))->first();
		}else{
			return $this->db->QueryGet("SELECT * from posts")->results();
		}
	}
	
	public static function savePost($params,$type,$title){
		$user = new user();
		return DB::Getinstance()->Query("INSERT INTO posts (`userid`,`content`,`post_title`,`type`,`date_created`) VALUES( ? , ? , ? , ? , '".date('Y-m-d H:i')."')",array($user->data()->id,json_encode($params),$title,$type))->lastInsertedId();
	}
	
	public static function updatePost($params,$type,$post){
		$user = new user();
		
		return DB::Getinstance()->Query("UPDATE posts set `content` = ?, type = ? WHERE id = ? AND userid = ? ",array(
			json_encode($params),
			$type,
			$post,
			$user->data()->id
		));
	}

	public static function PostType($content){
		try{
			$content = json_decode($content);
			if(isset($content->link)){
				return "link";
			}else if(isset($content->image)){
				return "image";
			}else if(isset($content->video)){
				return "video";
			}else{
				return "message";
			}
		}catch(Exception $e){
			echo "Error : Could not get the post type!";
		}
	}
	
		// Delete post
	public function delete($id){
		$user = new User();
		echo $this->db->Query("DELETE FROM posts WHERE id = ? AND  userid = ?",array($id,$user->data()->id))->count();
	}
	
	
}
?>