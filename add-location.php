<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/AddLocationPage.php';


$user = new User;
if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toSubmitLocation()));
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