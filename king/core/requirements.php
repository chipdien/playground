<?php
/*
|--------------------------------------------------------------------------
| Check php version
|--------------------------------------------------------------------------
|
*/
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
	checkMessage("The King poster script requires <u>PHP version 5.4 or higher.</u><br/>Your server is running php version : " . PHP_VERSION);
}
/*
|--------------------------------------------------------------------------
| Check cURL library
|--------------------------------------------------------------------------
|
*/
if (!extension_loaded('curl')) {
	checkMessage("cURL library is not loaded");
}
/*
|--------------------------------------------------------------------------
| Check curl_init
|--------------------------------------------------------------------------
|
*/
if(!function_exists('curl_init')){
	checkMessage("cURL is not working, curl_init is not available");
}
/*
|--------------------------------------------------------------------------
| Check curl_exec
|--------------------------------------------------------------------------
|
*/
if(!function_exists('curl_exec')){
	checkMessage("cURL is not working, curl_exec is not available");
}

$curl = new Curl();
$curl->get("https://graph.facebook.com");

if($curl->curlErrorMessage) {
	echo "<h5 style='padding:10px;background:#d65656;color:white;line-height: 10px;text-align:center;margin-top:10px'>".$curl->curlErrorMessage."</h5>";
}

function checkMessage($message){
	 die("<h3 style='line-height: 50px;text-align:center;margin-top:50px'>".$message."</h3>");
}
?>