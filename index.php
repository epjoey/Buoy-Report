<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/helpers.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/IntroPage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/HomePage.php';

$user = new User;
//intro page has no form handling. reg & login forms sent to register.php,login.php
if (!$user->userIsLoggedIn()) {

	$intro = new IntroPage;
	$intro->loadData();
	$intro->renderPage();
	exit();
}

$home = new HomePage;
$home->loadData();
if (isset($_POST['submit'])) {
	$home->afterSubmit();
}
$home->renderPage();	

//scrape data from NOAA, use time to decipher wind & tide. pivate or public logs (added to the public log).
//TIME, LOCATION, TIDE, WIND, DESCRIPTION

?>