<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new ErrorPage();
$page->renderPage(array(
	'user' => new User(),
	'pageTitle' => '404 Not Found',
));
?>