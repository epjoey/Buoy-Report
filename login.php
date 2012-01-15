<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/LoginPage.php';

$user = new User;

if ($user->userIsLoggedIn()) {
	header('Location:'.Paths::toUserHome());
	exit();
}

if (isset($_POST['submit']) && $_POST['submit'] == 'login') {
	$user->handleLoginFormSubmission();
}

$form = new LoginPage;
$form->loadData();
$form->renderPage();

?>