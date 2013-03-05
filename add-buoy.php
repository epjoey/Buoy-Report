<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/AddBuoyPage.php';

/*
$user = new User;
if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toAddBuoy()));
	exit();
}
*/

$page = new AddBuoyPage;
$page->loadData();

if (isset($_POST['enter-buoy'])) {	
	$page->afterSubmit(); 
}

$page->renderPage();


?>