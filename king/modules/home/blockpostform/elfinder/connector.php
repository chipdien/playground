<?php

error_reporting(0); // E_ALL in Dev mode

require realpath('./../../../../core') . '/init.php';

$user = new User();

$uploadsFolder = UPLOADS_FOLDER . "/";
$currentUser = $user->data()->username;

define("VERIFIY_MIME_TYPE", TRUE);

// check user upload folder
if (!file_exists(ABSPATH . $uploadsFolder . $currentUser )) {
    mkdir(ABSPATH . $uploadsFolder . $currentUser, 0777, true);
    chmod(ABSPATH . $uploadsFolder . $currentUser, 0777);
}

if (!file_exists(ABSPATH . $uploadsFolder . $currentUser . '/index.html')) {
	$handle = fopen(ABSPATH . $uploadsFolder . $currentUser . '/index.html','w+'); 
	fwrite($handle,"<h1>404 Not Found</h1>\nThe page that you have requested could not be found."); 
	fclose($handle); 
}

// elFinder autoload
require  ABSPATH . "vendor/elfinder/autoload.php";

// ===============================================

$allowedTypes = array(
	'image/jpeg',
	'image/png',
	'image/gif',
	'video/x-msvideo',
	'video/mp4',
	'video/mpeg',
	'video/3gpp',
	'video/quicktime',
	'video/ogg',
	'video/webm',
	'text/plain'
);

/**
 * Simple function to demonstrate how to control file access using "accessControl" callback.
 * This method will disable accessing files/folders starting from '.' (dot)
 *
 * @param  string  $attr  attribute name (read|write|locked|hidden)
 * @param  string  $path  file path relative to volume root directory started with directory separator
 * @return bool|null
 **/
function access($attr, $path, $data, $volume) {

	if(VERIFIY_MIME_TYPE){
		global $allowedTypes;
		if(in_array(mime_content_type($path),$allowedTypes) )
	        return null;
	}

	return strpos(basename($path), '.') === 0       // if file/folder begins with '.' (dot)
		? !($attr == 'read' || $attr == 'write')    // set read+write to false, other (locked+hidden) set to true
		:  null;                                    // else elFinder decide it itself
}


// Documentation for connector options:
// https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options
$opts = array(
	'debug' => DEBUG_MODE,
	'roots' => array(
		array(
			'driver'        => 'LocalFileSystem',           // driver for accessing file system (REQUIRED)
			'path'          => ABSPATH . $uploadsFolder . $currentUser,                 // path to files (REQUIRED)
			'URL'           => Options::get('siteurl') . $uploadsFolder . $currentUser, // URL to files (REQUIRED)
			'uploadDeny'    => array('all'),                // All Mimetypes not allowed to upload
			'uploadAllow'   => $allowedTypes,// Mimetype `image` and `text/plain` allowed to upload
			'uploadOrder'   => array('deny', 'allow'),      // allowed Mimetype `image` and `text/plain` only
			'accessControl' => 'access',
			'uploadMaxConn' => -1,
			'uploadMaxSize' => 0,
			'attributes' => array(
	            array( // hide readmes
	                'pattern' => '/\.(html|php|py|pl|sh|xml)$/i',
	                'read'   => false,
	                'write'  => false,
	                'locked' => true,
	                'hidden' => true
	            )
	        )
		)
	)
);

// run elFinder
$connector = new elFinderConnector(new elFinder($opts));
$connector->run();