<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

//remove
$local_dev = FALSE;
if ($_SERVER["REMOTE_ADDR"] == '::1' || 
	$_SERVER["REMOTE_ADDR"] == "127.0.0.1" ) 
{
	$local_dev = TRUE;
}


function dropCookie($name, $value='', $expire = 0, $path = '/', $domain='', $secure=false, $httponly=false) {	
	$_COOKIE[$name] = $value; 	
	$domain = Path::toCookieDomain();
	setcookie($name, $value, $expire, $path, $domain, $secure, $httponly); 
}

function eatCookie($name) { 
    unset($_COOKIE[$name]); 
    $domain = Path::toCookieDomain();
    setcookie($name, NULL, -1, '', $domain); 
} 

function getImageInfo($imagePath, $width, $height) {
	$imageInfo = array();

	$image = new SimpleImage();

	if (substr($imagePath, 0, 4) == 'http') {
		$imageIsRemote = TRUE;
	} else {
		$imageIsRemote = FALSE;
	}

	$image->load(Path::toImageFile($imagePath, $imageIsRemote));
	if (isset($image->image)) {
		$image->fitDimensions($width,$height);
		$width = $image->getWidth();
		$height = $image->getHeight();
		$src = Path::toImageSrc($imagePath, $imageIsRemote);
		$imageInfo = array('src'=>$src, 'width'=>$width, 'height'=>$height);
	}
	
	return $imageInfo;
}

function html($text) {
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function htmlout($text) {
	echo html($text);
}

function getLocalTimeFromGMT($gmtTime, $timezone = null) {
	// if (!$timezone) {
	// 	$timezone = "GMT";
	// }
	$localTimezone = new DateTimeZone($timezone);
	$offset = $localTimezone->getOffset(new DateTime('now', new DateTimeZone('GMT')));
	if ( gmstrftime("%m/%d/%y", time()+$offset) == gmstrftime("%m/%d/%y", $gmtTime+$offset)) { //if the same day
		$localTime = "today @ ".gmstrftime("%l:%M %p", $gmtTime+$offset);
	} else {
		$localTime = gmstrftime("%m/%d/%y %l:%M %p", $gmtTime+$offset);
	}

	return $localTime;

}

function getTzAbbrev($tz) {
	$dateTime = new DateTime(); 
	$dateTime->setTimeZone(new DateTimeZone($tz)); 
	return $dateTime->format('T');
}

function getOffset($tz) {
	$tz = new DateTimeZone($tz);
	$offset = $tz->getOffset(new DateTime('now', new DateTimeZone('GMT')));
	return $offset;
}

function getDirection($bearing) {
	$cardinalDirections = array(
		'NNW' => array(337.5, 360),
		'NNE' => array(0, 22.5),
		'NE' => array(22.5, 67.5),
		'E' => array(67.5, 112.5),
		'SE' => array(112.5, 157.5),
		'S' => array(157.5, 202.5),
		'SW' => array(202.5, 247.5),
		'W' => array(247.5, 292.5),
		'NW' => array(292.5, 337.5)
	);
	if ($bearing == 360 || $bearing == 0) { 
		$direction = 'N';
	} else {
		foreach ($cardinalDirections as $dir => $angles) {
	 		if ($bearing >= $angles[0] && $bearing < $angles[1]) {
	    		$direction = $dir;
	  			break;
	  		}
	  	}	
	}
  	return $direction;
}	

function printItem($item, $suffix = '', $replacement = 'no data', $html = null) {
	isset($html) && isset($item) ? print '<'.$html.'>' : '';
	isset($item) ? print html($item) . $suffix : print $replacement;
	isset($html) && isset($item) ? print '</'.$html.'>' : '';
}

function vardump ($var) {
	print '<pre>';
	var_dump($var);
	print '</pre>';
}


function bbcode2html($text) {
	$text = html($text);

	//[B]old
	$text = preg_replace('/\[B](.+?)\[\/B]/i', '<strong>$1</strong>', $text);

	//[I]talic
	$text = preg_replace('/\[I](.+?)\[\/I]/i', '<em>$1</em>', $text);

	//convert windows linebreaks (\r\n) to unix (\n)
	//$text = preg_replace('/\r\n/', "\n", $text);
	$text = str_replace("\r\n", "\n", $text);

	//convert mac linebreaks (\r) to unix (\n);
	//$text = preg_replace('/\r/', "\n", $text);
	$text = str_replace("\r", "\n", $text);

	//paragraphs
	//$text = '<p>' . preg_replace('/\n\n/', '</p><p>', $text) . '</p>';
	//$text = '<p>' . str_replace("\n\n", '</p><p>', $text) . '</p>';

	//linebreaks
	//$text = preg_replace('/\n/', '<br>', $text);
	$text = str_replace("\n", '<br>', $text);

	//[URL]link[/URL]
	if(stristr($text, '[url')) {
		$url = stristr($text, '[url');
		if (substr($url, 5, 7) != 'http://' && substr($url, 5, 8) != 'https://') {
			$url = substr_replace($url, "http://", 5, 0);
			$text = substr_replace($text, $url, stripos($text, '[url'));
		}

		$text = preg_replace('/\[url]([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\[\/url]/i', '<a target="_blank" href="$1">$1</a>', $text);

		//[URL=url]link[/URL]
		$text = preg_replace('/\[url=([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)](.+?)\[\/url]/i', '<a target="_blank" href="$1">$2</a>', $text);
	}
	else {
		
		//better link finder
		$reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

		// Check if there is a url in the text
		if(preg_match($reg_exUrl, $text, $url)) {

	       // make the urls hyper links
	       return preg_replace($reg_exUrl, "<a target='blank' href=" . $url[0] . ">link</a> ", $text);
		}			
	}

	
	return $text;
}

function bbcodeout($text) {
	echo bbcode2html($text);
}

function handleFileUpload($upload, $reporterId) {

	$uploadStatus = array();

	if (!is_uploaded_file($upload['tmp_name'])) {
		$uploadStatus['error'] = 'upload-file';
		return $uploadStatus;
	}
	if (preg_match('/^image\/p?jpeg$/i', $upload['type'])) {
		$imageExt = '.jpg';
	} else if (preg_match('/^image\/gif$/i', $upload['type'])) {
		$imageExt = '.gif';
	} else if (preg_match('/^image\/(x-1)?png$/i', $upload['type'])) {
		$imageExt = '.png';
	} else {
		$uploadStatus['error'] = 'file-type'; //unknown file type
		return $uploadStatus;
	}	
	
	//stored in DB. full path prepended
	$uploadStatus['imagepath'] = date('Y') . '/' . date('m') . '/' . $reporterId . '.' . date('d.G.i.s') . $imageExt;

	if (!move_uploaded_file($upload['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/uploads/' . $uploadStatus['imagepath'])) {
		$uploadStatus['error'] = 'file-save';
	} 	
	
	//if we got here, image was copied and array contains image path
	return $uploadStatus; 	
}

?>