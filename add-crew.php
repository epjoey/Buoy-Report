<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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