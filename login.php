<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/LoginPage.php';

$user = new User;

if ($user->isLoggedIn) {
	header('Location:'.Path::toUserHome());
	exit();
}

$form = new LoginPage;
$form->loadData();
$form->renderPage();

?>