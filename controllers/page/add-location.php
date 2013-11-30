<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = UserService::getUser();
if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toSubmitLocation()));
	exit();
}


$error = null;
if (isset($_GET['error']) && $_GET['error']) {
	switch($_GET['error']) {
		case 1: $error = 'Please enter a location';; break;
		case 2: $error = "Location name specified already exists"; break;
	}
}	



$page = new AddLocationPage();
$page->submitError = NULL;
$page->renderPage(array(
	'user' => $user,
	'pageTitle' => 'Submit Location',
	'addLocationError' => $error
));
?>