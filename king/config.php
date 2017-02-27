<?php
/*
|--------------------------------------------------------------------------
| Database name
|--------------------------------------------------------------------------
|
*/
define('ABSPATH', 		dirname( __FILE__ ) . '/');
define('DIR_DB',   		dirname( __FILE__ ) . '/database/');
define('DIR_CORE',    	dirname( __FILE__ ) . '/core/');
define('DIR_INSTALL',	dirname( __FILE__ ) . '/install/');
define('DIR_LANG',   	dirname( __FILE__ ) . '/core/language/');
/*
|--------------------------------------------------------------------------
| Turning ON/OFF debug mode (for maintenance mode)
|--------------------------------------------------------------------------
|
*/
define("DEBUG_MODE",FALSE);
/*
|------------------------------------
| Default language
|------------------------------------
|
*/
define("DEFAULT_LANG","english");
/*
|------------------------------------
| Default language
|------------------------------------
|
*/
define("DEFAULT_TIMEZONE","UTC");
/*
|------------------------------------
| The uploads folder name
|------------------------------------
|
*/
define("UPLOADS_FOLDER","files");
/*
|--------------------------------------------------------------------------
| Database name (It's highly recommended to change the database name)
|--------------------------------------------------------------------------
|
*/
$dbname = "kingposter"; 
/*
|--------------------------------------------------------------------------
| System settings
|--------------------------------------------------------------------------
|
*/
$GLOBALS['config'] = array(
	'db' => array(
		'dbname' => $dbname,
		'driver' => 'sqlite', 	// sqlite or mysql
		'host' => '', 	// MySQL host name
		'username' => '', 	// MySQL database username
		'password' => '',		// MySQL database password
	),
	'remember' => array(
		'cookie_name' => 'kp_token',
		'cookie_expiry' => 604800
	),
	'session' => array(
		'session_name' => 'kp_user_179',
		'token_name' => 'kp_token_179'
	)
);

//define("BASE_URL","");

?>