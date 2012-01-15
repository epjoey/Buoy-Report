<?php

print 'lalala';

/*
require_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/HomePage.php';
*/
include_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/pages/IntroPage.php';
/*
$user = new User;
if ($user->userIsLoggedIn()) {
	header('Location:'.Paths::toUserHome());
	exit();
}
*/
$intro = new IntroPage($user->loginError, $user->registerError);
$intro->loadData();
$intro->renderPage();


//scrape data from NOAA, use time to decipher wind & tide. pivate or public logs (added to the public log).
//TIME, LOCATION, TIDE, WIND, DESCRIPTION
*/

?>