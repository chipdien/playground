<?php  if (!defined('ABSPATH')) exit('No direct script access allowed');
class Options
{
	public static function Get($key){
		$query = DB::getInstance()->queryGet("SELECT * FROM options");
		$options = array();
		foreach($query->results() as $opt){
			$options[$opt->option] = $opt->value;
		}

		if($key == 'siteurl' && isset($options[$key])){
			$siteurl = defined('BASE_URL') ? BASE_URL : $options[$key];
			if(self::isHTTPS()){
				return "https".substr($siteurl, strrpos($siteurl, '://'), strlen($siteurl)); 
			}
			return $siteurl;
		}

		return isset($options[$key]) ? $options[$key] : false;
	}

	public static function Update($params){
		foreach($params as $key => $value){
			if(Options::Get($key) === false){
				DB::getInstance()->INSERT("options",array('option'=>$key,'value'=>$value));
			}else{
				DB::getInstance()->UPDATE("options","option",$key,array('option'=>$key,'value'=>$value));
			}
		}
	}

	

	private static function GetDomain($url)
	{
	  $pieces = parse_url($url);
	  $domain = isset($pieces['host']) ? $pieces['host'] : '';
	  if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
	    return $regs['domain'];
	  }
	  return false;
	}

	public static function CheckSiteUrl(){
		if(isset($_SERVER['SERVER_PROTOCOL'])){
			if(Options::GetDomain(Options::get('siteurl')) != Options::GetDomain(CurrentPath())){
				die(base64_decode("SXQgc2VlbXMgdGhhdCB5b3UgYXJlIHVzaW5nIGEgZGlmZmVyZW50IGRvbWFpbiwgWW91IG5lZWQgdG8gcmUtaW5zdGFsbCB0aGUgYXBwbGljYXRpb24uPGJyLz5JZiB5b3UgdGhpbmsgeW91J3JlIHNlZWluZyB0aGlzIGJ5IG1pc3Rha2UsIHBsZWFzZSBsZXQgdXMga25vdy4gc3VwcG9ydEBraW5ncG9zdGVyLm5ldA=="));
			}
		}
	}

	public static function CheckForUpdate(){
		$user = new user();
		if($user->HasPermission('admin')){
			// check if already checked for update
			$app_version = null;
			$update_message = "";

			if(Cookie::exists("kp_app_version")){
				$app_version = Cookie::get("kp_app_version");
			}

			// check if already checked for update
			if(Cookie::exists("kp_update_msg")){
				$update_message = Cookie::get("kp_update_msg");
			}

			if(!Cookie::exists("kp_app_version")){
				$curl = new Curl();
				$r = DB::getInstance()->queryGet("SELECT code FROM product_activation")->first();
				$pa = isset($r->code) ? $r->code : "";
				$curl->Get("http://example.com/host/kingposter/update/?source=".Options::get('siteurl')."&pv=".$pa);
				if(isset($curl->response->app_version)){
					$app_version = $curl->response->app_version;
					Cookie::put("kp_app_version", $curl->response->app_version,60*60*24*15);
				}
				if(isset($curl->response->update_message)){
					$update_message = $curl->response->update_message;
					Cookie::put("kp_update_msg", $curl->response->update_message,60*60*24*15);
				}
			}
			
			if($app_version){
				if(DEV_VERSION < $app_version){
					if(!defined("update")){ define('UPDATE',true); }
					if(trim($update_message) != ""){
						Session::Flash("home_update","warning",$update_message,true);
					}
				}
			}
		}
	}

	private static function isHTTPS() {
	  return
	    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
	    || $_SERVER['SERVER_PORT'] == 443;
	}

}
?>