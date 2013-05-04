<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new BuoyPage();

$page->pageTitle = 'Buoys';
$page->user = UserService::getUser();
$page->buoys = BuoyService::getAllBuoys();

$page->renderPage();
?>