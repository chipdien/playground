<?php
session_start();
ob_start();
/*
|--------------------------------------------------------------------------
| Constant path for the core folder
|--------------------------------------------------------------------------
|
*/
define('COREPATH', dirname(__FILE__) . '/');
/*
|--------------------------------------------------------------------------
| Autoload file
|--------------------------------------------------------------------------
|
*/
require_once COREPATH . "/autoload.php";

/*
|--------------------------------------------------------------------------
| Template file
|--------------------------------------------------------------------------
|
*/
require_once COREPATH . "/template.php";
/*
|--------------------------------------------------------------------------
| Set default Exception handler if not in debug mode
|--------------------------------------------------------------------------
|
*/
if(!DEBUG_MODE){
	function exception_handler($exception) {
		echo $exception->getMessage();
	}
	set_exception_handler('exception_handler');
}
/*
|--------------------------------------------------------------------------
| App fisrt run
|--------------------------------------------------------------------------
|
*/
require_once DIR_CORE . "/firstrun.php";
/*
|-------------------------------------------------
| Set Timezone 
|-------------------------------------------------
|
*/
$defaultTimezone = defined("DEFAULT_TIMEZONE") ? DEFAULT_TIMEZONE : "UTC";
$timeZone = Options::Get('timezone') ? Options::Get('timezone') : "UTC";
date_default_timezone_set($timeZone);
?>