<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/SimpleImage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';

/* GLOBALS */
$local_dev = FALSE;
if ($_SERVER["REMOTE_ADDR"] == '::1' || 
	$_SERVER["REMOTE_ADDR"] == "127.0.0.1" ) 
{
	$local_dev = TRUE;
}
$timer = array();

function timer($where){
	global $timer;
	$time = microtime();
	$timer[$where] = $time;
}

function dropCookie($name, $value='', $expire = 0, $path = '', $domain='', $secure=false, $httponly=false) {	
	$_COOKIE[$name] = $value; 	
	$domain = Paths::toCookieDomain();
	setcookie($name, $value, $expire, $path, $domain, $secure, $httponly); 
}

function eatCookie($name) { 
    unset($_COOKIE[$name]); 
    $domain = Paths::toCookieDomain();
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

	$image->load(Paths::toImageFile($imagePath, $imageIsRemote));
	if (isset($image->image)) {
		$image->fitDimensions($width,$height);
		$width = $image->getWidth();
		$height = $image->getHeight();
		$src = PATHS::toImageSrc($imagePath, $imageIsRemote);
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

function getLocalTimeFromGMT($gmtTime, $timezone) {
	
	$localTimezone = new DateTimeZone($timezone);
	$offset = $localTimezone->getOffset(new DateTime('now', new DateTimeZone('GMT')));
	if ( date("m/d/y") == gmstrftime("%m/%d/%y", $gmtTime+$offset)) {
		$localTime = gmstrftime("%l:%M %p", $gmtTime+$offset);
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
	$text = preg_replace('/\[URL]([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)\[\/URL]/i', '<a href="$1">$1</a>', $text);

	//[URL=url]link[/URL]
	$text = preg_replace('/\[URL=([-a-z0-9._~:\/?#@!$&\'()*+,;=%]+)](.+?)\[\/URL]/i', '<a href="$1">$2</a>', $text);
	
	return $text;
}

function bbcodeout($text) {
	echo bbcode2html($text);
}


?>