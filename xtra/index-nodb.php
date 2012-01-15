<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/reporter/classes/model/User.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/utility/Paths.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/HomePage.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/classes/pages/IntroPage.php';


$intro = new IntroPage($user->loginError, $user->registerError);
$intro->loadData();
$intro->renderPage();


//scrape data from NOAA, use time to decipher wind & tide. pivate or public logs (added to the public log).
//TIME, LOCATION, TIDE, WIND, DESCRIPTION

?>