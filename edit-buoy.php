<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditBuoyPage.php';


$buoyId = $_REQUEST['id'];

if (!$buoyId) {
	header('Location:'.Path::to404());
	exit();
}

$page = new EditBuoyPage;
$page->loadData($buoyId);
$page->renderPage();


?>