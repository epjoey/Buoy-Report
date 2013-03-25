<?
include_once $_SERVER['DOCUMENT_ROOT'] . '/utility/Classloader.php';

$page = new LocationPage();
$page->loadData();
$page->renderPage();
?>