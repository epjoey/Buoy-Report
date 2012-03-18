<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/EditBuoyPage.php';

/*
$user = new User;
if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toAddBuoy()));
	exit();
}
*/

if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	$buoyId = $_REQUEST['id'];
} else {
	header('Location:'.Path::to404());
	exit();
}



$page = new EditBuoyPage;
$page->loadData($buoyId);

if (isset($_POST['submit'])) {	
	$page->afterSubmit(); 
}

$page->submitError = NULL;
$page->renderPage();


?>