<?php
// Convert to html entitis
function escape($string){
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function CurrentPath(){
	$url = substr($_SERVER['PHP_SELF'], 0, strrpos($_SERVER['PHP_SELF'], '/')); 
	$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	return $protocol.$_SERVER['HTTP_HOST'].$url."/"; 
}

function httpReferer(){
	return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false ;
}

function make_comparer() {
    // Normalize criteria up front so that the comparer finds everything tidy
    $criteria = func_get_args();
    foreach ($criteria as $index => $criterion) {
        $criteria[$index] = is_array($criterion)
            ? array_pad($criterion, 3, null)
            : array($criterion, SORT_ASC, null);
    }
 
    return function($first, $second) use ($criteria) {
        foreach ($criteria as $criterion) {
            // How will we compare this round?
            list($column, $sortOrder, $projection) = $criterion;
            $sortOrder = $sortOrder === SORT_DESC ? -1 : 1;
 
            // If a projection was defined project the values now
            if ($projection) {
                $lhs = call_user_func($projection, $first[$column]);
                $rhs = call_user_func($projection, $second[$column]);
            }
            else {
                $lhs = $first[$column];
                $rhs = $second[$column];
            }
 
            // Do the actual comparison; do not return if equal
            if ($lhs < $rhs) {
                return -1 * $sortOrder;
            }
            else if ($lhs > $rhs) {
                return 1 * $sortOrder;
            }
        }
 
        return 0; // tiebreakers exhausted, so $first == $second
    };
}


if(!function_exists('mime_content_type')){
    function mime_content_type($filename) {
        $result = new finfo();
        if (is_resource($result) === true) {
            return $result->file($filename, FILEINFO_MIME_TYPE);
        }
        return false;
    }
}

function base_url($resource){
    echo Options::get("siteurl") . $resource;
}


if ( ! function_exists('flash_bag') )
{
  function flash_bag($message, $type = "info", $icon = false, $close = true)
  {
    if($icon){
        switch($type){
            case "success":
            $icon = "check-circle"; break;
            case "info":
            $icon = "info-circle"; break;
            case "warning":
            $icon = "exclamation-circle"; break;
            case "danger":
            $icon = "exclamation-triangle"; break;
        }
    }

    return array(
        'message' => $message,
        'type' =>  $type,
        'icon' => $icon,
        'close' => $close
     );
  }
}


?>