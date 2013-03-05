<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Path.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/user/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/IntroPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/HomePage.php';

$user = new User;

//intro page has no form handling. login form sent to login.php
//if (/*!$user->isLoggedIn*/ isset($_REQUEST['intro']) && $_REQUEST['intro']) {

if (!$user->isLoggedIn) {
	$intro = new IntroPage;
	$intro->loadData();
	$intro->renderPage();
	exit();
}

$home = new HomePage;
$home->loadData();
$home->renderPage();	

//scrape data from NOAA, use time to decipher wind & tide. pivate or public logs (added to the public log).
//TIME, LOCATION, TIDE, WIND, DESCRIPTION

?>