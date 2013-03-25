<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$user = new User;

if ($user->isLoggedIn) {
	header('Location:'.Path::toUserHome());
	exit();
}

$form = new LoginPage;
$form->loadData();
$form->renderPage();

?>