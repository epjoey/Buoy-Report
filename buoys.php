<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new BuoyPage();

$page->pageTitle = 'Buoys';
$page->user = new User();
$page->buoys = BuoyService::getAllBuoys();

$page->renderPage();
?>