<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/AddLocationPage.php';


$user = new User;
if (!$user->isLoggedIn) {
	header('Location:'.Paths::toLogin(null, Paths::toSubmitLocation()));
	exit();
}

$addloc = new AddLocationPage;
$addloc->loadData();

if (isset($_POST['submit-location'])) {	
	$addloc->afterSubmit(); 
}

$addloc->submitError = NULL;
$addloc->renderPage();


?>