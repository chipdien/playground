<?php  
include('core/init.php');

$template = new Template();
$template->header("Upgrade"); 

echo "<div class='alert alert-danger'>Note: This upgrade is for version >= 1.5.2 ONLY, if you have old version (<1.5.2) you need to re-install the script.</div>";

$r = DB::getInstance()->queryGet("SELECT code FROM product_activation")->first();
$pa = isset($r->code) ? $r->code : "";

$curl = new Curl();

$curl->Get("http://example.com/host/kingposter/update/?source=".Options::get('siteurl')."&pv=".$pa."&action=update&driver=".Config::get('db/driver'));

if($curl->response){
	$content = "<?php \n".base64_decode($curl->response) . " ?>";
	$file = ABSPATH.'application/cache/'.md5(time()).".php";
	$fp = fopen($file, 'w');
	if($fp){
		flock($fp, LOCK_EX);
		ftruncate($fp, 0);
		fseek($fp, 0);
		fwrite($fp, $content);
		flock($fp, LOCK_UN);
		fclose($fp);
	}
	if(file_exists($file)){
		include $file;
		unlink($file);
		echo "<div class='alert alert-success'>Your script has been updated to the version latest version, Delete the /upgrade.php file</div>"; 
	}else{
		echo "Updating the script failed Please try again";
	}
	
}else{
	echo "Updating the script failed Please try again";
}

$template->footer(); 

?>
