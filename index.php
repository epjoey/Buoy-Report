<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

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