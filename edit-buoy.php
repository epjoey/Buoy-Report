<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$buoyId = $_REQUEST['id'];

if (!$buoyId) {
	header('Location:'.Path::to404());
	exit();
}

$page = new EditBuoyPage;
$page->loadData($buoyId);
$page->renderPage();


?>