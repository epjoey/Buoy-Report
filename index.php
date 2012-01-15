<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/utility/helpers.php';
timer('after_include_help&paths');
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
timer('after_include_user');
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/IntroPage.php';
timer('after_include_intro');
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/HomePage.php';
timer('after_include_home');

$user = new User;
timer('after_instantiate_user');
//intro page has no form handling. reg & login forms sent to register.php,login.php
if (!$user->userIsLoggedIn()) {
	timer('after_user_loggedin_call');

	$intro = new IntroPage;
	timer('after_instance_intro');
	$intro->loadData();
	timer('after_loaddata_intro');
	$intro->renderPage();
	timer('after_render_intro');
	if (isset($timer)) vardump($timer);
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