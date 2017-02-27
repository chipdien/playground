<?php
/*
|--------------------------------------------------------------------------
| App version 
|--------------------------------------------------------------------------
|
*/
if(!defined("VERSION")) define("VERSION","1.7.9");
if(!defined("DEV_VERSION")) define("DEV_VERSION","179");
/*
|--------------------------------------------------------------------------
| Constant path for the core folder
|--------------------------------------------------------------------------
|
*/
if ( ! defined('COREPATH') ) define('COREPATH', dirname(__FILE__) . '/');
/*
|--------------------------------------------------------------------------
| Load the config file 
|--------------------------------------------------------------------------
|
*/
require_once COREPATH."../config.php";
require_once DIR_CORE."/general.php";
/*
|------------------------------------
| The uploads folder name
|------------------------------------
|
*/
if(!defined("UPLOADS_FOLDER")) define("UPLOADS_FOLDER","files");
/*
|--------------------------------------------------------------------------
| Enable/Disable error reporting and display errors
|--------------------------------------------------------------------------
|
*/
if(!DEBUG_MODE){
	error_reporting(E_ALL);
	ini_set( 'display_errors', '0' );
}
/*
|--------------------------------------------------------------------------
| Autoload classes
|--------------------------------------------------------------------------
| @param anonymous function
|
*/
function autoload($class){
	// classes dir 
	$dir = ABSPATH.'/core/classes/';
	// note : All classes file have a lowercase name. the class name must be lowercase
	$classFile = $dir.strtolower($class).'.php';
	
	// Check file existence before including the if 
	if (file_exists($classFile)) {
		require_once $classFile;
	}
}
spl_autoload_register('autoload');
/*
|--------------------------------------------------------------------------
| Load facebook SDK required files
|--------------------------------------------------------------------------
|
*/
require_once DIR_CORE.'../facebook/autoload.php';

require_once ABSPATH . 'vendor/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

?>