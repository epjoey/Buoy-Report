<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/AddCrewPage.php';


$user = new User;
if (!$user->isLoggedIn) {
	header('Location:'.Path::toLogin(null, Path::toSubmitCrew()));
	exit;
}

$page = new AddCrewPage;
$page->loadData();

if (isset($_POST['submit-crew'])) {	
	$page->afterSubmit(); 
}

$page->renderPage();


?>