<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new AddBuoyPage();
$page->renderPage(array(
	'user' => new User(),
	'pageTitle' => 'Submit Buoy',
));
?>