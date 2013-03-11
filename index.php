<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/IntroPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/HomePage.php';

$user = new User;

if (!$user->isLoggedIn) {
	$intro = new IntroPage;
	$intro->loadData();
	$intro->renderPage();
	exit();
}

$home = new HomePage;
$home->loadData();
$home->renderPage();
?>