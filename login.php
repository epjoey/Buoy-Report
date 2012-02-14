<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LoginPage.php';

$user = new User;

if ($user->isLoggedIn) {
	header('Location:'.Paths::toUserHome());
	exit();
}

$form = new LoginPage;
$form->loadData();
$form->renderPage();

?>