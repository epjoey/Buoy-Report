<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$about = new AboutPage();
$about->renderPage(array(
	'user' => UserService::getUser(),
	'pageTitle' => 'About Buoy Report',
));
?>