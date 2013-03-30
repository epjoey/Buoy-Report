<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$buoyId = $_REQUEST['id'];

if (!$buoyId) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();
}
$buoy = BuoyService::getBuoy($buoyId);
if (!$buoy) {
	header("HTTP/1.0 404 Not Found");
	include_once $_SERVER['DOCUMENT_ROOT'] . Path::to404();
	exit();			
}

$page = new EditBuoyPage;
$page->renderPage(array(
	'pageTitle' => 'Edit Buoy ' . $buoyId,
	'buoy' => $buoy
));


?>