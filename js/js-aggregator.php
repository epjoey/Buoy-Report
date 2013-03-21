<?php
//TODO: make this file work for JS

/* must hit this script on local before uploading cache.css */
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/JSMin.php';

if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler");
else ob_start();

$files = array(
    array('name'=>'jquery-1.7.1.min.js', 'min'=>true),
    array('name'=>'underscore-1.4.3.min.js', 'min'=>true),
    array('name'=>'backbone-0.9.10.min.js', 'min'=>true),
    array('name'=>'jquery.validate.min.js', 'min'=>true),
    array('name'=>'BR.js', 'min'=>false),
    array('name'=>'reportFeedFunctions.js', 'min'=>false)
);

$time = mktime(0,0,0,21,5,1980);
$cache = 'cache.js';

foreach($files as $file) {
    $fileTime = filemtime($file['name']);

    if($fileTime > $time) {
        $time = $fileTime;
    }
}

if(file_exists($cache)) {
    $cacheTime = filemtime($cache);
    if($cacheTime < $time) {
        $time = $cacheTime;
        $recache = true;
    } else {
        $recache = false;
    }
} else {
    $recache = true;
}

if(!$recache && isset($_SERVER['If-Modified-Since']) && strtotime($_SERVER['If-Modified-Since']) >= $time){
    header("HTTP/1.0 304 Not Modified");
} else {
    header('Content-type: text/javascript');
    header('Last-Modified: ' . gmdate("D, d M Y H:i:s",$time) . " GMT");

    if($recache) {
        $js = '';

        foreach($files as $file){
            $contents = file_get_contents($file['name']);
            if (!$file['min']) {
                //$contents = JSMin::minify($contents);
            }
            $js .= $contents;
        }
        
        file_put_contents($cache, $js);
        echo $js;
    } else {
        readfile($cache);
    }
}
?>
