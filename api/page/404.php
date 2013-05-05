<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new ErrorPage();
$page->renderPage(array(
	'user' => UserService::getUser(),
	'pageTitle' => '404 Not Found',
));
?>