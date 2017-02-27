<?php   if (!defined('ABSPATH')) exit('No direct script access allowed');

$langFile 	= DIR_LANG . "/languages/" . strtolower( DEFAULT_LANG ) . ".php";

if( defined('USER_LANG') ){
	
	$langFile = DIR_LANG . "/languages/" . strtolower( USER_LANG ) . ".php";
	
	if( file_exists( $langFile )){
		require_once( $langFile );
	}else{
		throw new Exception("Language file not found!");
	}
}else{
	if( file_exists( $langFile )){
		require_once( $langFile );
	}else{
		throw new Exception("Language file not found!");
	}
}


GenerateJsLang();
	
function lang($string,$ucfirst = true){
	global $lang,$langFile;
	$index = strtoupper(clean($string));
	/*if(!isset($lang[$index])){
		$fp = fopen($langFile, 'a+');
		flock($fp, LOCK_EX);
		ftruncate($fp, 0);
		fseek($fp, 0);
		$langContent = "<?php \n";
		$lang = (array)$lang;
		foreach($lang as $key => $value){
			$langContent .= "\$lang['".trim(preg_replace('/\s+/', ' ', $key))."'] = \"".trim(preg_replace('/\s+/', ' ', $value))."\";\n";
		}
		$langContent .= "\$lang['".$index."'] = \"\";\n ?>".PHP_EOL;
		fwrite($fp, $langContent);
		flock($fp, LOCK_UN);
		fclose($fp);
		$lang[$index] = "";
	}*/
	if(isset($lang[$index]) && trim($lang[$index]) != "" ){
		return html_entity_decode($lang[$index]);
	} else {
		$text = strtolower(str_replace('_',' ',$string));
		return $ucfirst ? html_entity_decode(ucfirst($text)) : html_entity_decode($text);
	}
	
}

function l($string,$ucfirst = true){
	echo lang($string,$ucfirst = true);
}
function clean($string) {
   $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.

   return preg_replace('/_+/', '_', $string); // Replaces multiple hyphens with single one.
}

// Generate js lang file
function GenerateJsLang(){
	require dirname(__FILE__) . "/jslang.php";
	$content = "var langs = ". json_encode($jsLang) . ";\n";
	$fp = fopen(ABSPATH.'core/js/lang.js', 'w');
	if($fp){
		flock($fp, LOCK_EX);
		ftruncate($fp, 0);
		fseek($fp, 0);
		fwrite($fp, $content);
		flock($fp, LOCK_UN);
		fclose($fp);
	}
}

?>